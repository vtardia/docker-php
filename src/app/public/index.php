<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../bootstrap.php';

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('Hello, World!');
    return $response;
});

$app->get('/mysql', function (Request $request, Response $response, array $args) {
    try {
        $db = $this->get('mysql');
        $sth = $db->prepare('select * from users;');
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response->getBody()->write('Connection failed: ' . $e->getMessage());
        return $response;
    }
    $response->getBody()->write('<pre>' . print_r($data, true) . '</pre>');
    return $response;
});

$app->get('/postgres', function (Request $request, Response $response, array $args) {
    try {
        $db = $this->get('postgres');
        $sth = $db->prepare('select * from users;');
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response->getBody()->write('Connection failed: ' . $e->getMessage());
        return $response;
    }
    $response->getBody()->write('<pre>' . print_r($data, true) . '</pre>');
    return $response;
});

$app->run();
