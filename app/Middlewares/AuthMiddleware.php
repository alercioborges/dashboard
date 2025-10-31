<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;
use App\Interfaces\AuthServiceInterface;

/**
 * Authentication Middleware
 * 
 * Ensures user is authenticated before accessing protected routes
 */
class AuthMiddleware implements MiddlewareInterface
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->authService->isAuthenticated()) {
            $response = new Response();
            return $response
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
