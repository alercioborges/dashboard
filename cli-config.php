<?php

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\DBAL\DriverManager;

require __DIR__ . '/vendor/autoload.php';

$config     = new PhpFile(__DIR__ . '/app/config/migrations.php');
$dbParams   = require __DIR__ . '/app/config/migrations-db.php';
$connection = DriverManager::getConnection($dbParams);

return DependencyFactory::fromConnection($config, new ExistingConnection($connection));
