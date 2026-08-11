<?php

namespace App\Middlewares;

use App\Services\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Popula o RequestContext com a ServerRequest do ciclo atual.
 *
 * Deve ser registrado ANTES de addRoutingMiddleware() no arquivo
 * de middlewares — como a pilha do Slim é LIFO, isso garante que
 * o roteamento já tenha resolvido a rota quando este middleware
 * executar, permitindo que RequestContext::routeName() funcione.
 *
 * O contexto é limpo no finally para evitar vazamento de estado
 * entre requests em ambientes de processo persistente
 * (Swoole, RoadRunner, FrankenPHP).
 */
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
