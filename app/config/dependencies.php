<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\App;

Use App\middlewares\Error;

$appConfig = require __DIR__ . '/app.php';

return [

    Twig::class => function (ContainerInterface $c) use ($appConfig):Twig {
        $twig = Twig::create(__DIR__ . "/../../templates/pages/", [
            'cache' => $appConfig['env'] === 'production' 
                ? __DIR__ . '/../../storage/localcache' 
                : false,
            'debug' => $appConfig['debug']
        ]);

        // add globais variables on Twig
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);
        $twig->getEnvironment()->addGlobal('get', $_GET ?? []);

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
        }

        return $twig;
    },

    // Middleware error
    Error::class => function (ContainerInterface $c) use ($appConfig) {
        $app = $c->get(App::class);
        $mdwrError = new Error($app);
        $mdwrError->getError($appConfig['env'], $appConfig['debug']);
        return $mdwrError;
    },

];
