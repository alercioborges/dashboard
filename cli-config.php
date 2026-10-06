<?php

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

// carrega o .env antes de resolver a configuração do banco,
// igual ao bootstrap de public/index.php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config     = new PhpFile(__DIR__ . '/app/config/migrations/migrations.php');
$dbParams   = require __DIR__ . '/app/config/migrations/migrations-db.php';
$connection = DriverManager::getConnection($dbParams);

return DependencyFactory::fromConnection($config, new ExistingConnection($connection));