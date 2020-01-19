<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use app\controllers\MesseController;

require __DIR__ . '/../vendor/autoload.php';
$settings = require __DIR__ . '/../conf/settings.php';

$app = new \Slim\App($settings);

$app->get('/messe/login', MesseController::class . ':login');
$app->get('/messe/logout', MesseController::class . ':logout');
$app->get('/messe/', MesseController::class . ':index');
$app->get('/messe/observe/log', MesseController::class . ':observeLog');
$app->post('/messe/ajax/log', MesseController::class . ':ajaxLog');
$app->run();