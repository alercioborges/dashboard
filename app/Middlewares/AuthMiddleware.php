<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Interfaces\AuthServiceInterface;

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
           
            $_SESSION['redirect'] = getRequestPath($request);

            return redirect('/login');
        }

        return $handler->handle($request);
    }
}
