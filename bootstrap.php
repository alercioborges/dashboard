<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Middlewares\TrailingSlash;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\middlewares\Error;


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

// Setting container DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/app/config/dependencies.php');
$container = $containerBuilder->build();

// Create aplication
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register the App instance in the container
$container->set(Slim\App::class, $app);

// Middleware of trailing slash
$app->add(new TrailingSlash(false));

// Add  directory if exist
if (!empty($appConfig['baseDir'])) {
    $app->setBasePath($appConfig['baseDir']);
}

// Add routs middleware
$app->addRoutingMiddleware();

// Trailing slash
$app->add(new TrailingSlash(false));

// add error middleware
$container->get(Error::class);

// Twig middleware
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));
