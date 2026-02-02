<?php

use Psr\Container\ContainerInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection as DBALConn;
use App\Core\Connection;

return [

    PDO::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('dbConfig');
        return Connection::getInstance($dbConfig);
    },

    DBALConn::class => function (ContainerInterface $c) {
        $c->get(PDO::class);
        $dbConfig = $c->get('dbConfig');

        $connectionParams = [
            'driver' => 'pdo_mysql',
            'host' => $dbConfig['host'],
            'port' => $dbConfig['port'],
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'charset' => $dbConfig['charset']
        ];

        return DriverManager::getConnection($connectionParams);
    },

];
