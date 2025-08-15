<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;
use App\Middlewares\MiddlewareError;

return [

    // Register the appConfig in the container
    'config' => require __DIR__ . '/app.php',

    Twig::class => function (ContainerInterface $c): Twig {
        $appConfig = $c->get('config');

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
        return TwigMiddleware::createFromContainer(
            $c->get(App::class),
            Twig::class
        );
    },

    // Middleware de erro
    /*
    Error::class => function (ContainerInterface $c) {
        $appConfig = $c->get('config');
        $app = $c->get(App::class);

        $mdwrError = new MiddlewareError($app);
        $mdwrError->getError($appConfig['env'], $appConfig['debug']);

        return $mdwrError;
    },
    */
];
