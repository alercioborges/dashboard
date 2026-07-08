<?php

use DI\ContainerBuilder;

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
if ($GLOBALS['app_config']['env'] === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/../../storage/cache');
}

return $containerBuilder->build();
