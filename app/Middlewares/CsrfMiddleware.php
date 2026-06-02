<?php

namespace App\Middlewares;

use Slim\Views\Twig;
use Slim\Csrf\Guard;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Twig $twig,
        private Guard $guard
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $this->twig->getEnvironment()->addGlobal('csrf', [
            'nameKey'  => $this->guard->getTokenNameKey(),
            'valueKey' => $this->guard->getTokenValueKey(),
            'name'     => $request->getAttribute(
                $this->guard->getTokenNameKey()
            ),
            'value'    => $request->getAttribute(
                $this->guard->getTokenValueKey()
            ),
        ]);

        return $handler->handle($request);
    }
}