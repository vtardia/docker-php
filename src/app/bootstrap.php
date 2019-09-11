<?php
require __DIR__ . '/vendor/autoload.php';

use Slim\App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$app = new Slim\App;

$container = $app->getContainer();

// Logger
$container['logger'] = function ($c) {

    $logger = new Logger('activity');
    $logger->pushHandler(
        new StreamHandler('php://stdout', Logger::INFO)
    );
    return $logger;
};

// MySQL database
$container['mysql'] = function ($c) {
    $logger = $c->get('logger');
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
};

// MongoDB database
$container['mongodb'] = function ($c) {
    $logger = $c->get('logger');
    $dsn = sprintf(
        'mongodb://%s:%s@%s:%s/admin',
        getenv('MONGODB_USER'),
        getenv('MONGODB_PASSWORD'),
        getenv('MONGODB_HOST'),
        (getenv('MONGODB_PORT') ?? 27017)
    );
    for ($i = 1; $i <= 5; $i++) {
        try {
            $client = new MongoDB\Client($dsn);
            $db = $client->selectDatabase(getenv('MONGODB_DATABASE'));
            $logger->info('Connected to MongoDB');
            return $db;
        } catch (PDOException $e) {
            $logger->error('Unable to connect to MongoDB', ['error' => $e->getMessage()]);
            sleep(5);
        }
    }
    throw new Exception(sprintf('Unable to connect to MongoDB after %d attempts', $i));
};
