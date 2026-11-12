<?php

return [
    'driver'   => $_ENV['DB_DRIVER']?? 'mysql',
    'host'     => $_ENV['DB_HOST']     ?? 'localhost',
    'port'     => $_ENV['DB_PORT']     ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? 'dbname',
    'username' => $_ENV['DB_USER']     ?? 'root',
    'password' => $_ENV['DB_PASS']     ?? '',
    'charset'  => $_ENV['DB_CHARSET']  ?? 'utf8mb4',
];
