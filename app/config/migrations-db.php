<?php
// app/config/migrations-db.php

$dbConfig = require __DIR__ . '/database.php';

return [
    'dbname'         => $dbConfig['database'],
    'user'           => $dbConfig['username'],
    'password'       => $dbConfig['password'],
    'host'           => $dbConfig['host'],
    'port'           => $dbConfig['port'],
    'driver'         => ($dbConfig['driver'] === 'mysql') ? 'pdo_mysql' : $dbConfig['driver'],
    'charset'        => $dbConfig['charset'],
];
    