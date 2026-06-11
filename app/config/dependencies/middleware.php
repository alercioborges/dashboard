<?php

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use App\Middlewares\AuthMiddleware;
use App\Middlewares\PermissionMiddleware;
use App\Interfaces\AuthServiceInterface;
use App\Middlewares\SetupMiddleware;
use App\Interfaces\UserRepositoryInterface;
use App\Services\AuthService;

return [

    // -------------------------------------------------------
    // CSRF GUARD
    // -------------------------------------------------------

    Guard::class => function (ContainerInterface $c): Guard {

        $responseFactory = $c->get(App::class)->getResponseFactory();
        $auth = $c->get(AuthService::class);
        $logger = $c->get(LoggerInterface::class);

        $guard = new Guard($responseFactory);
        $guard->setPersistentTokenMode(true);

        $guard->setFailureHandler(
            function (
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ) use ($auth, $responseFactory, $logger) {

                $path = explode(getDir(), $request->getUri()->getPath());

                $logger->warning('[CSRF] Token validation failed.', [
                    'method'      => $request->getMethod(),
                    'uri'         => $path[1],
                    'had_session' => isset($_SESSION['user']),
                ]);

                if (!isset($_SESSION['user'])) {

                    if (isset($_SESSION['csrf'])) {
                        unset($_SESSION['csrf']);
                    }

                    flash('error', error('Formulário expirado. Recarregue a página e tente novamente.'));
                    return redirect($path[1]);
                }

                unset($_SESSION['csrf']);
                unset($_SESSION['user']);

                if (isset($_COOKIE['remember_me'])) {
                    setcookie('remember_me', '', time() - 3600, '/');
                    unset($_COOKIE['remember_me']);
                }

                flash('error', error('Sua sessão expirou. Tente novamente.'));
                return redirect('/login');
            }
        );

        return $guard;
    },


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
