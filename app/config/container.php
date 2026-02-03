<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;

// Load vendor
require __DIR__ . '/../../vendor/autoload.php';

// ENSURE .env IS LOADED (WEB + CLI)
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

// Load app config
$appConfig = require __DIR__ . '/../config/app.php';

// Setting container DI
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(__DIR__ . '/dependencies/app.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/view.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/database.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/repositories.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/services.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/controllers.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/middleware.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/cli.php');

// Compile container in production
if ($appConfig['env'] === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/../../storage/cache');
}

return $containerBuilder->build();
