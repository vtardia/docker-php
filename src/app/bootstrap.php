<?php

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Slim 4.x does not ship with a container library.
 * It supports all PSR-11 implementations such as PHP-DI
 * To install PHP-DI `composer require php-di/php-di`
 */
$container = new \DI\Container();
AppFactory::setContainer($container);

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

$container = $app->getContainer();

// Logger
$container->set('logger', function (\Psr\Container\ContainerInterface $container) {
    $logger = new Logger('activity');
    $logger->pushHandler(
        new StreamHandler('php://stdout', Logger::INFO)
    );
    return $logger;
});

// MySQL database
$container->set('mysql', function (\Psr\Container\ContainerInterface $container) {
    $logger = $container->get('logger');
    $dsn = sprintf('mysql:dbname=%s;host=%s', getenv('MYSQL_DATABASE'), getenv('MYSQL_HOST'));
    $user = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    for ($i = 1; $i <= 5; $i++) {
        try {
            $dbh = new PDO($dsn, $user, $password);
            $logger->info('Connected to MySQL');
            return $dbh;
        } catch (PDOException $e) {
            $logger->error('Unable to connect to MySQL', ['error' => $e->getMessage()]);
            sleep(5);
        }
    }
    throw new Exception(sprintf('Unable to connect to MySQL after %d attempts', $i));
});

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true, $container->get('logger'));
