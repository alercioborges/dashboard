<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection as DBALConn;

use App\Core\Connection;
use App\Services\QueryBuilderService;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

return [

    // Register the appConfig in the container
    'appConfig' => require __DIR__ . '/app.php',

    // Register the dbConfig in the container
    'dbConfig' => require __DIR__ . '/database.php',

    // Twig
    Twig::class => function (ContainerInterface $c): Twig {
        $appConfig = $c->get('appConfig');

        $twig = Twig::create(__DIR__ . "/../../templates/pages/", [
            'cache' => $appConfig['env'] === 'production'
                ? __DIR__ . '/../../storage/localcache'
                : false,
            'debug' => $appConfig['debug'],
            'auto_reload' => $appConfig['debug']
        ]);


        // Variáveis globais
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);
        $twig->getEnvironment()->addGlobal('get', $_GET ?? []);

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
        }

        return $twig;
    },


    // Twig Middleware
    TwigMiddleware::class => function (ContainerInterface $c) {
        return TwigMiddleware::createFromContainer($c->get(App::class), Twig::class);
    },


    // Database Connection
    PDO::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('dbConfig');
        return Connection::getInstance($dbConfig);
    },


    // Setting Doctrine DBAL Connection
    DBALConn::class => function (ContainerInterface $c) {
        $pdo = $c->get(PDO::class);

        // Configuration parameters of Doctrine DBAL
        $connectionParams = [
            'pdo' => $pdo,
            'driver' => 'pdo_mysql', // ou outro driver adequado
        ];

        return DriverManager::getConnection($connectionParams);
    },
    

    // Query Builder
    QueryBuilderService::class => function (ContainerInterface $c): QueryBuilderService {
        return new QueryBuilderService(
            $c->get(DBALConn::class)
        );
    },


    // Repository
    UserRepositoryInterface::class => function (ContainerInterface $c): User {
        return new User($c->get(QueryBuilderService::class));
    },

];
