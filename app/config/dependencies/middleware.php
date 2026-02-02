<?php

use Psr\Container\ContainerInterface;
use Slim\App;

use App\Middlewares\AuthMiddleware;
use App\Interfaces\AuthServiceInterface;

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

    /**
     * Register middleware into Slim application (WEB CONTEXT ONLY)
     *
     * This definition is only used when the application is running
     * in HTTP mode (not CLI).
     */
    'http.middlewares' => function (ContainerInterface $c): callable {

        return function (App $app) use ($c) {

            // Body parsing (for POST/PUT/PATCH requests)
            $app->addBodyParsingMiddleware();

            // Method override (allows _METHOD to simulate HTTP verbs)
            $app->add(new Slim\Middleware\MethodOverrideMiddleware());

            // Authentication middleware (custom)
            $app->add($c->get(AuthMiddleware::class));

            return $app;
        };
    },

];
