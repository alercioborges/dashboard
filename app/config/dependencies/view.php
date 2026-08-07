<?php

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\App;
use App\Views\ExtensionTwig;
use Slim\Csrf\Guard;
use App\Interfaces\AuthServiceInterface;

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

        $menu_items = loader('/templates/layout/components/config/menu-items.php');

        $authService = $c->get(AuthServiceInterface::class);

        // Filtra itens de menu sem permissão; mantém apenas pais com filhos visíveis
        $filterMenu = function (array $items) use ($authService, &$filterMenu): array {
            $filtered = [];

            foreach ($items as $item) {
                if (isset($item['children'])) {
                    $item['children'] = $filterMenu($item['children']);

                    if (count($item['children']) === 0) {
                        continue;
                    }
                } elseif (isset($item['permission']) && !$authService->hasPermission($item['permission'])) {
                    continue;
                }

                $filtered[] = $item;
            }

            return $filtered;
        };

        $menu_items = $filterMenu($menu_items);

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
            $c->get(Guard::class),
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
