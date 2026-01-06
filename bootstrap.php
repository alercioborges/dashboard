<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

session_set_cookie_params([
    'lifetime' => 0
    ]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Load vendor
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->safeLoad();

// Load app config
$appConfig = require __DIR__ . '/app/config/app.php';

// Setting container DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/app/config/dependencies.php');

// Compile container in production
if ($appConfig['env'] === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/storage/cache');
}

// Create variable container
$container = $containerBuilder->build();

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
