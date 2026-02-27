<?php

use Psr\Container\ContainerInterface;
use Slim\App;

use App\Middlewares\AuthMiddleware;
use App\Middlewares\PermissionMiddleware;
use App\Interfaces\AuthServiceInterface;
use App\Middlewares\SetupMiddleware;
use App\Interfaces\UserRepositoryInterface;

return [

    // -------------------------------------------------------
    // HTTP MIDDLEWARE LAYER
    // -------------------------------------------------------

    /**
     * Auth Middleware
     *
     * Responsible for handling authentication checks
     * before protected routes are executed.
     */
    AuthMiddleware::class => function (ContainerInterface $c): AuthMiddleware {
        return new AuthMiddleware(
            $c->get(AuthServiceInterface::class)
        );
    },

    SetupMiddleware::class => function (ContainerInterface $c): SetupMiddleware {
        return new SetupMiddleware(
            $c->get(UserRepositoryInterface::class)
        );
    },

    /**
     * Permission Middleware Factory
     *
     * This returns a callable factory so we can pass
     * dynamic permissions to the middleware.
     *
     * Usage:
     * ->add($container->get(PermissionMiddleware::class)('users.create'))
     */
    PermissionMiddleware::class => function (ContainerInterface $c): callable {

        return function (string $permission) use ($c): PermissionMiddleware {

            return new PermissionMiddleware(
                $c->get(AuthServiceInterface::class),
                $permission
            );

        };
    },

    /**
     * Register middleware into Slim application (WEB CONTEXT ONLY)
     */
    'http.middlewares' => function (ContainerInterface $c): callable {

        return function (App $app) use ($c) {

            // Body parsing
            $app->addBodyParsingMiddleware();

            // Method override
            $app->add(new Slim\Middleware\MethodOverrideMiddleware());

            // Global Authentication middleware
            $app->add($c->get(AuthMiddleware::class));

            return $app;
        };
    },

];
