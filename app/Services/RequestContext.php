<?php

namespace App\Services;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class RequestContext
{
    private ?ServerRequestInterface $request = null;

    public function set(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function request(): ServerRequestInterface
    {
        return $this->request
            ?? throw new \RuntimeException('RequestContext não inicializado.');
    }

    public function routeName(): ?string
    {
        $route = RouteContext::fromRequest($this->request())->getRoute();

        return $route?->getName();
    }

    public function path(): string
    {
        return $this->request()->getUri()->getPath();
    }

    public function isRoute(string ...$names): bool
    {
        return in_array($this->routeName(), $names, true);
    }

    public function clear(): void
    {
        $this->request = null;
    }
}
