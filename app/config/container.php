<?php

use DI\ContainerBuilder;

// Load vendor
require __DIR__ . '/../../vendor/autoload.php';

// Load app config
$appConfig = require __DIR__ . '/../config/app.php';

// Setting container DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/dependencies.php');

// Compile container in production
if ($appConfig['env'] === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/../../storage/cache');
}

// Create variable container
$container = $containerBuilder->build();

return $container;