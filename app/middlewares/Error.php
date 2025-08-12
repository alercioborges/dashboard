<?php

namespace App\Middlewares;

use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Error
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getError(String $environment, bool $debug)
    {
        // Setting Whoops to debug (only on development mode)
        if ($environment === 'development') {
            $this->app->add(new WhoopsMiddleware([
                'enable' => true,
                'editor' => 'vscode',
                'title'  => 'Aplication error'
            ]));
        } else {

            $errorMiddleware = $this->app->addErrorMiddleware($debug, true, true);

            $app = $this->app;

            $errorMiddleware->setErrorHandler(
                HttpNotFoundException::class,
                function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app): Response {
                    $response = $app->getResponseFactory()->createResponse(404);

                    // Carregar arquivo HTML externo
                    $htmlFile = __DIR__ . '/../../templates/pages/404.html';

                    if (file_exists($htmlFile)) {
                        $html = file_get_contents($htmlFile);
                        $response->getBody()->write($html);
                    } else {
                        $response->getBody()->write('<div style="display: flex; justify-content: center; align-items: center; height: 100vh;"><div><h1>404 Page not found</h1><button onclick="history.back()">Back page</button></div></div></body>');
                    }

                    return $response->withHeader('Content-Type', 'text/html');
                }
            );
        }
    }
}
