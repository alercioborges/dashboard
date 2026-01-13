<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Load vendor
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->safeLoad();

$container = require_once __DIR__ . '/app/config/container.php';

// Create aplication
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register the App instance in the container
$container->set(Slim\App::class, $app);

// Add  directory if exist
if (!empty($appConfig['baseDir'])) {
    $app->setBasePath($appConfig['baseDir']);
}

// Body Parsing to ready POST data
$app->addBodyParsingMiddleware();

// Method Override to convertei _METHOD to HTTP method)
$app->add(new Slim\Middleware\MethodOverrideMiddleware());

// Add Middlewares routs
$methodOverrideMiddleware = new Slim\Middleware\MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware);

// Setting middlewares
$middleware = require __DIR__ . '/app/config/middlewares.php';
$middleware($app);
