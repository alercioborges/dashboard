<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

Use App\middlewares\Error;

$appConfig = require __DIR__ . '/app.php';

return [

    Twig::class => function (ContainerInterface $c) use ($appConfig) {
        $twig = Twig::create(__DIR__ . "/../../templates/pages/", [
            'cache' => $appConfig['env'] === 'production' 
                ? __DIR__ . '/../../storage/localcache' 
                : false,
            'debug' => $appConfig['debug']
        ]);

        // Adiciona variáveis globais no Twig
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
            $twig->getEnvironment()->addGlobal('get', $_GET ?? []);
        }

        return $twig;
    },

    // Middleware error
    Error::class => function (ContainerInterface $c) use ($appConfig) {
        $mdwtError = new Error($app);
        $mdwtError->getError($appConfig['env'], $appConfig['debug']);        
    },

];
