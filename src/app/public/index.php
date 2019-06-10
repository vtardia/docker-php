<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('Hello, World!');
    return $response;
});

$app->get('/mysql', function (Request $request, Response $response, array $args) {
    $dsn = sprintf('mysql:dbname=%s;host=%s', getenv('MYSQL_DATABASE'), getenv('MYSQL_HOST'));
    $user = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    try {
        $dbh = new PDO($dsn, $user, $password);
        $sth = $dbh->prepare('SELECT * FROM `users`;');
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
    $dsn = sprintf(
        'mongodb://%s:%s@%s:%s/admin',
        getenv('MONGODB_USER'),
        getenv('MONGODB_PASSWORD'),
        getenv('MONGODB_HOST'),
        (getenv('MONGODB_PORT') ?? 27017)
    );
    try {
        $client = new MongoDB\Client($dsn);
        $db = $client->selectDatabase(getenv('MONGODB_DATABASE'));
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
