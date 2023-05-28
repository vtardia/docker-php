<?php

use Psr\Container\ContainerInterface;

// Postgres database
$container->set('postgres', function (ContainerInterface $container) {
    $logger = $container->get('logger');
    $dsn = sprintf('pgsql:dbname=%s;host=%s', getenv('POSTGRES_DATABASE'), getenv('POSTGRES_HOST'));
    $user = getenv('POSTGRES_USER');
    $password = getenv('POSTGRES_PASSWORD');
    for ($i = 1; $i <= 5; $i++) {
        try {
            $dbh = new PDO($dsn, $user, $password);
            $logger->info('Connected to Postgres');
            return $dbh;
        } catch (PDOException $e) {
            $logger->error('Unable to connect to Postgres', ['error' => $e->getMessage()]);
            sleep(5);
        }
    }
    throw new Exception(sprintf('Unable to connect to Postgres after %d attempts', $i));
});
