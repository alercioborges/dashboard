<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection as DBALConn;

use App\Views\ExtensionTwig;

use App\Core\Connection;
use App\Services\QueryBuilderService;

use App\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use App\Controllers\UserController;
use App\Core\Controller;
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

        // Global variables
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);
        $twig->getEnvironment()->addGlobal('get', $_GET ?? []);
        $twig->addExtension($c->get(ExtensionTwig::class));

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
        }

        return $twig;
    },

    // Twig function to redirect to route name
    ExtensionTwig::class => function (ContainerInterface $c): ExtensionTwig {
        $routeParser = $c->get(App::class)
            ->getRouteCollector()
            ->getRouteParser();
        return new ExtensionTwig($routeParser);
    },


    // Twig Middleware
    TwigMiddleware::class => function (ContainerInterface $c) {
        return TwigMiddleware::createFromContainer($c->get(App::class), Twig::class);
    },


    // Database Connection PDO
    PDO::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('dbConfig');
        return Connection::getInstance($dbConfig);
    },


    // Setting Doctrine DBAL Connection
    DBALConn::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('dbConfig');
        $c->get(PDO::class);

        // Configuration parameters of Doctrine DBAL
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


    // Query Builder
    QueryBuilderService::class => function (ContainerInterface $c): QueryBuilderService {
        return new QueryBuilderService(
            $c->get(DBALConn::class)
        );
    },


    Controller::class => function (ContainerInterface $c): Controller {
        return new Controller(
            $c->get(Twig::class)
        );
    },


    // Repository User implements UserRepositoryInterface
    UserRepositoryInterface::class => function (ContainerInterface $c): UserRepositoryInterface {
        return new User($c->get(QueryBuilderService::class));
    },


    // UserService
    UserService::class => function (ContainerInterface $c): UserService {
        return new UserService($c->get(UserRepositoryInterface::class));
    },


    // UserController
    UserController::class => function (ContainerInterface $c) {
        return new UserController(
            $c->get(Twig::class),
            $c->get(UserService::class)
        );
    },

];
