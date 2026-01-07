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

use App\Services\Validators\Validator;

use App\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use App\Controllers\UserController;
use App\Models\User;

use App\Core\Controller;
use App\Core\Model;

use App\Services\PasswordService;

use App\Interfaces\RoleRepositoryInterface;
use App\Services\RoleService;
use App\Controllers\RoleController;
use App\Models\Role;

use App\Interfaces\RememberMeRepositoryInterface;
use App\Services\RememberMeService;
use App\Models\RememberMe;

use App\Middlewares\AuthMiddleware;

use App\Services\AuthService;
use App\Interfaces\AuthServiceInterface;
use App\Controllers\AuthController;
use Doctrine\DBAL\Query;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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

        // Load menu itens
        $menu_items = require __DIR__ . '/../../templates/layout/components/config/menu-itens.php';

        // Global variables
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);
        $twig->getEnvironment()->addGlobal('get', $_GET ?? []);
        $twig->getEnvironment()->addGlobal('current_route', $appConfig['current_route']);
        $twig->getEnvironment()->addGlobal('menu_items', $menu_items);

        // Load external Twig functions
        $twig->addExtension($c->get(ExtensionTwig::class));

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
        }

        return $twig;
    },

    // Twig function to redirect to route name
    ExtensionTwig::class => function (ContainerInterface $c): ExtensionTwig {
        $appConfig = $c->get('appConfig');
        $routeParser = $c->get(App::class)
            ->getRouteCollector()
            ->getRouteParser();
        return new ExtensionTwig(
            $routeParser,
            $appConfig['current_route'],
            $appConfig['baseDir']
        );
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

    // Core Controller
    Controller::class => function (ContainerInterface $c): Controller {
        return new Controller(
            $c->get(Twig::class)
        );
    },

    // Core Model
    Model::class => function (ContainerInterface $c): Model {
        return new Model(
            $c->get(QueryBuilderService::class)
        );
    },


    // Validator
    Validator::class => function (ContainerInterface $c): Validator {
        return new Validator($c);
    },


    // Repository User implements UserRepositoryInterface
    UserRepositoryInterface::class => function (ContainerInterface $c): UserRepositoryInterface {
        return new User(
            $c->get(QueryBuilderService::class),
            $c->get(PasswordService::class)
        );
    },


    // UserService
    UserService::class => function (ContainerInterface $c): UserService {
        return new UserService($c->get(UserRepositoryInterface::class));
    },


    //User
    User::class => function (ContainerInterface $c): User {
        return new User(
            $c->get(QueryBuilderService::class),
            $c->get(PasswordService::class)
        );
    },


    // UserController
    UserController::class => function (ContainerInterface $c): UserController {
        return new UserController(
            $c->get(Twig::class),
            $c->get(UserService::class),
            $c->get(Validator::class),
            $c->get(Role::class)
        );
    },

    // Pasword
    PasswordService::class => function (ContainerInterface $c): PasswordService {
        return new PasswordService(12);
    },

    // Repository User role implements UserRepositoryInterface
    RoleRepositoryInterface::class => function (ContainerInterface $c): RoleRepositoryInterface {
        return new Role(
            $c->get(QueryBuilderService::class)
        );
    },


    // RoleService
    RoleService::class => function (ContainerInterface $c): RoleService {
        return new RoleService($c->get(RoleRepositoryInterface::class));
    },


    // Role
    Role::class => function (ContainerInterface $c): Role {
        return new Role(
            $c->get(QueryBuilderService::class)
        );
    },


    // RoleController
    RoleController::class => function (ContainerInterface $c): RoleController {
        return new RoleController(
            $c->get(Twig::class),
            $c->get(RoleService::class),
            $c->get(Validator::class)
        );
    },

    // AuthService implements AuthServiceInterface
    AuthServiceInterface::class => function (ContainerInterface $c): AuthServiceInterface {
        return new AuthService(
            $c->get(UserRepositoryInterface::class),
            $c->get(RememberMeRepositoryInterface::class)
        );
    },

    // AuthService
    AuthService::class => function (ContainerInterface $c): AuthService {
        return new AuthService(
            $c->get(UserRepositoryInterface::class),
            $c->get(RememberMeRepositoryInterface::class)
        );
    },

    // Logger (necessário para o AuthController)
    LoggerInterface::class => function (ContainerInterface $c): LoggerInterface {
        $appConfig = $c->get('appConfig');
        $logger = new Logger('app');

        $logPath = __DIR__ . '/../../storage/logs/app.log';
        $handler = new StreamHandler($logPath, Logger::DEBUG);
        $logger->pushHandler($handler);

        return $logger;
    },

    // AuthController
    AuthController::class => function (ContainerInterface $c): AuthController {
        return new AuthController(
            $c->get(Twig::class),
            $c->get(AuthServiceInterface::class),
            $c->get(LoggerInterface::class),
            $c->get(Validator::class)
        );
    },

    // AuthMiddleware
    AuthMiddleware::class => function (ContainerInterface $c): AuthMiddleware {
        return new AuthMiddleware(
            $c->get(AuthServiceInterface::class)
        );
    },

     // RememberMe
    RememberMe::class => function (ContainerInterface $c): RememberMe {
        return new RememberMe(
            $c->get(QueryBuilderService::class),
        );
    },

    // RememberMeRepositoryInterface
    RememberMeRepositoryInterface::class => function (ContainerInterface $c): RememberMe {
        return new RememberMe(
            $c->get(QueryBuilderService::class)
        );
    },

];
