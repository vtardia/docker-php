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
        $sth = $db->prepare('SELECT * FROM `users`;');
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $response->getBody()->write('Connection failed: ' . $e->getMessage());
        return $response;
    }
    $response->getBody()->write('<pre>' . print_r($data, true) . '</pre>');
    return $response;
});

$app->get('/mongodb', function (Request $request, Response $response, array $args) {
    try {
        $db = $this->get('mongodb');
        $collection = $db->selectCollection('dummies');
        $result = $collection->find();
        $data = [];
        foreach ($result as $document) {
            $doc = $document->getArrayCopy();
            $doc['_id'] = (string) $doc['_id'];
            $data[] = $doc;
        }
    } catch (Exception $e) {
        $response->getBody()->write('Error: ' . $e->getMessage());
        return $response;
    }

    $response->getBody()->write('<pre>' . print_r($data, true) . '</pre>');
    return $response;
});

$app->run();
