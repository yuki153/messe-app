<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use app\controllers\MesseController;

require __DIR__ . '/../vendor/autoload.php';
$settings = require __DIR__ . '/../conf/settings.php';

$app = new \Slim\App($settings);

$app->get('/test/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/messe/login', messeController::class . ':login');
$app->get('/messe', messeController::class . ':index');
$app->get('/messe/observe/log', messeController::class . ':observeLog');
$app->post('/messe/ajax/log', messeController::class . ':ajaxLog');
$app->run();