<?php

use Psr\Container\ContainerInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection as DBALConn;

return [

    DBALConn::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('dbConfig');

        return DriverManager::getConnection([
            'driver'   => 'pdo_mysql',
            'host'     => $dbConfig['host'],
            'port'     => $dbConfig['port'],
            'dbname'   => $dbConfig['database'],
            'user'     => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'charset'  => $dbConfig['charset'],
        ]);
    },

];
