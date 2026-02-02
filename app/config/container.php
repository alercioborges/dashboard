<?php

use DI\ContainerBuilder;

// Load vendor
loader('/vendor/autoload.php');

// Load app config
$appConfig = loader('/app/config/app.php');

// Setting container DI
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(__DIR__ . '/dependencies/app.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/view.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/database.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/repositories.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/services.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/controllers.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/middleware.php');
$containerBuilder->addDefinitions(__DIR__ . '/dependencies/cli.php'); // <--- IMPORTANTE


// Compile container in production
if ($appConfig['env'] === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/../../storage/cache');
}

// Create variable container
$container = $containerBuilder->build();

return $container;