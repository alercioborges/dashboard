<?php

use DI\Container;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
/*
use CrateSpace\Hydrahon\Builder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Config\Database;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\MailService;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
*/

// Twig
$container->set('view', function () {
    $appConfig = require __DIR__ . 'app.php';
    $loader = new FilesystemLoader(__DIR__ . '/../storage/localcache');
    $twig = new Environment($loader, [
        'cache' => __DIR__ . '/../storage/localcache',
        'cache' => (string)$appConfig['debug'],
        'debug' => $appConfig['debug'],
    ]);

    $twig->addExtension(new DebugExtension());
    $twig->addGlobal('session', $_SESSION);
    $twig->addGlobal('get', $_GET);
    $twig->addGlobal('base_path', $appConfig['url']);

    return $twig;
});

/*
// Logger
$container->set('logger', function () {
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
    return $logger;
});


// Database
$container->set('db', function () {
    return Database::getConnection();
});

// Repositories
$container->set(UserRepository::class, function (Container $c) {
    return new UserRepository($c->get('db'));
});

$container->set(RoleRepository::class, function (Container $c) {
    return new RoleRepository($c->get('db'));
});

// Services
$container->set(UserService::class, function (Container $c) {
    return new UserService(
        $c->get(UserRepository::class),
        $c->get('logger')
    );
});

$container->set(AuthService::class, function (Container $c) {
    return new AuthService(
        $c->get(UserRepository::class),
        $c->get('logger')
    );
});

$container->set(MailService::class, function (Container $c) {
    return new MailService($c->get('logger'));
});
*/