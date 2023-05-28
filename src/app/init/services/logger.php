<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

$container->set('logger', function (ContainerInterface $container) {
    $logger = new Logger('activity');
    $logger->pushHandler(
        new StreamHandler('php://stdout', Logger::INFO)
    );
    return $logger;
});
