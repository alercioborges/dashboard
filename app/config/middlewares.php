<?php

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Middlewares\TrailingSlash;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

use App\Middlewares\SetupMiddleware;

return function (App $app) {

    $app->add(SetupMiddleware::class);

    $appConfig = require __DIR__ . '/app.php';

    // Remove barra final das URLs
    $app->add(new TrailingSlash(false));

    // Middleware do Twig
    $twig = $app->getContainer()->get(Twig::class);
    $app->add(TwigMiddleware::create($app, $twig));

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

        // Handler para qualquer outro erro diferente de 404
        $errorMiddleware->setDefaultErrorHandler(
            function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app): Response {

                $response = $app->getResponseFactory()->createResponse(500);

                // Carrega página externa HTML para outros erros
                $htmlFile = __DIR__ . '/../../templates/pages/error.html';

                if (file_exists($htmlFile)) {
                    $html = file_get_contents($htmlFile);
                    $response->getBody()->write($html);
                } else {
                    $response->getBody()->write(
                        '<div style="display:flex;justify-content:center;align-items:center;height:100vh;";><h1>Ocorreu um erro inesperado</h1></div>'
                    );
                }

                return $response->withHeader('Content-Type', 'text/html');
            }
        );
    }
};
