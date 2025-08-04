<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;
use Middlewares\TrailingSlash;


if (!session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// Load vendor
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load app config
$appConfig = require __DIR__ . '/app/config/app.php';

// Create container DI
$container = new Container();

// Create aplication
AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware error (Whoops)
if ($appConfig['debug'] === 'true') {
    $app->add(new WhoopsMiddleware());
}

// Middleware of trailing slash
$app->add(new TrailingSlash(false));

// Adicionando diretório do dominio
if (!empty($appConfig['baseDir'])) {
	$app->setBasePath($appConfig['baseDir']);
}

// Add routs middleware
$app->addRoutingMiddleware();

// add error middleware
$app->addErrorMiddleware($appConfig['debug'], true, true);
