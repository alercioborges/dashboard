<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;
use App\Views\ExtensionTwig;

return [

    // -------------------------------------------------------
    // VIEW LAYER (TWIG)
    // -------------------------------------------------------

    Twig::class => function (ContainerInterface $c): Twig {
        $appConfig = $c->get('appConfig');

        $twig = Twig::create(__DIR__ . '/../../../templates/', [
            'cache' => $appConfig['env'] === 'production'
                ? loader('/storage/localcache')
                : false,
            'debug' => $appConfig['debug'],
            'auto_reload' => $appConfig['debug']
        ]);

        $menu_items = loader('/templates/layout/components/config/menu-itens.php');

        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);
        $twig->getEnvironment()->addGlobal('get', $_GET ?? []);
        $twig->getEnvironment()->addGlobal('current_route', $appConfig['current_route']);
        $twig->getEnvironment()->addGlobal('menu_items', $menu_items);

        $twig->addExtension($c->get(ExtensionTwig::class));

        if ($appConfig['env'] === 'development') {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            $twig->getEnvironment()->addGlobal('session', $_SESSION ?? []);
            $twig->getEnvironment()->addGlobal('cookies', $_COOKIE ?? []);
        }

        return $twig;
    },

    ExtensionTwig::class => function (ContainerInterface $c): ExtensionTwig {
        $appConfig = $c->get('appConfig');

        /** @var App $app */
        $app = $c->get(App::class);

        $routeParser = $app->getRouteCollector()->getRouteParser();

        return new ExtensionTwig(
            $routeParser,
            $appConfig['current_route'],
            $appConfig['baseDir']
        );
    },

    TwigMiddleware::class => function (ContainerInterface $c) {
        return TwigMiddleware::createFromContainer(
            $c->get(App::class),
            Twig::class
        );
    },

];
