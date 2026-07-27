<?php

use Doctrine\DBAL\DriverManager;

$dbConfig = require __DIR__ . '/app/config/database.php';

return DriverManager::getConnection([
    'dbname'   => $dbConfig['database'],
    'user'     => $dbConfig['username'],
    'password' => $dbConfig['password'],
    'host'     => $dbConfig['host'],
    'port'     => $dbConfig['port'],
    'driver'   => $dbConfig['driver']
]);
