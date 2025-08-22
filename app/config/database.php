<?php

return [
    'driver'    => $_ENV['DB_DRIVER']   ?? 'mysql',
    'host'      => $_ENV['DB_HOST']     ?? 'localhost',
    'port'      => $_ENV['DB_PORT']     ?? '3306',
    'database'  => $_ENV['DB_DATABASE'] ?? 'dashboard',
    'username'  => $_ENV['DB_USER']     ?? 'root',
    'password'  => $_ENV['DB_PASS']     ?? '',
    'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'options'   => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . ($_ENV['DB_CHARSET'] ?? 'utf8mb4')
    ]
];
