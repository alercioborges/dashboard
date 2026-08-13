<?php

namespace App\Middlewares;

use App\Services\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestContextMiddleware implements MiddlewareInterface
{
    private readonly RequestContext $context;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->context->set($request);

        try {
            return $handler->handle($request);
        } finally {
            $this->context->clear();
        }
    }
}
