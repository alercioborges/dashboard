<?php

use Slim\App;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Middlewares\TrailingSlash;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;
use App\Middlewares\SetupMiddleware;
use App\Middlewares\RequestContextMiddleware;
use Doctrine\DBAL\Exception\TableNotFoundException;

return function (App $app) {

    $appConfig = $app->getContainer()->get('appConfig');

    // Remove barra final das URLs
    $app->add(new TrailingSlash(false));

    // CSRF protection
    $app->add(Guard::class);

    // Middleware Twig
    $twig = $app->getContainer()->get(Twig::class);
    $app->add(TwigMiddleware::create($app, $twig));

    $app->add(SetupMiddleware::class);

    $app->add(RequestContextMiddleware::class);

    // Error middleware    
    if ($appConfig['env'] === 'development') {
        $app->add(new WhoopsMiddleware([
            'enable' => true,
            'editor' => 'vscode',
            'title'  => 'Application error'
        ]));
    } else {
        $errorMiddleware = $app->addErrorMiddleware($appConfig['debug'], true, true);

        // Handler específico para 404
        $errorMiddleware->setErrorHandler(
            HttpNotFoundException::class,
            function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app): Response {

                $response = $app->getResponseFactory()->createResponse(404);

                // Load HTML custom 404
                $htmlFile = __DIR__ . '/../../templates/pages/404.html';

                if (file_exists($htmlFile)) {
                    $html = file_get_contents($htmlFile);
                    $response->getBody()->write($html);
                } else {
                    $response->getBody()->write(
                        '<div style="display:flex;justify-content:center;align-items:center;height:100vh;"><div><h1>404 - Página não encontrada</h1><button onclick="history.back()">Voltar</button></div></div>'
                    );
                }

                return $response->withHeader('Content-Type', 'text/html');
            }
        );

        // Specific handler for missing tableHa
        $errorMiddleware->setErrorHandler(
            TableNotFoundException::class,
            function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app): Response {

                $response = $app->getResponseFactory()->createResponse(500);

                $message = 'The database exists, but a table expected by the application was not found. '
                    . 'The migrations were likely not executed in this environment. '
                    . 'Run: vendor/bin/doctrine-migrations migrate --no-interaction';

                $htmlFile = __DIR__ . '/../../templates/pages/error.html';

                if (file_exists($htmlFile)) {
                    $html = str_replace('{{ message }}', htmlspecialchars($message), file_get_contents($htmlFile));
                } else {
                    $html = '<div style="display:flex;justify-content:center;align-items:center;height:100vh;">'
                        . '<div><h1>Database not configured</h1><p>' . htmlspecialchars($message) . '</p></div></div>';
                }

                $response->getBody()->write($html);

                return $response->withHeader('Content-Type', 'text/html');
            }
        );

        // Handler para qualquer outro erro diferente de 404
        $errorMiddleware->setDefaultErrorHandler(
            function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app): Response {

                $response = $app->getResponseFactory()->createResponse(500);

                $message = 'An internal error occurred';

                // Carrega página externa HTML para outros erros
                $htmlFile = __DIR__ . '/../../templates/pages/error.html';

                if (file_exists($htmlFile)) {
                    $html = str_replace('{{ message }}', htmlspecialchars($message), file_get_contents($htmlFile));
                } else {
                    $html = '<div style="display:flex;justify-content:center;align-items:center;height:100vh;";><h1>Ocorreu um erro inesperado</h1></div>';
                }

                $response->getBody()->write($html);

                return $response->withHeader('Content-Type', 'text/html');
            }
        );
    }
};
