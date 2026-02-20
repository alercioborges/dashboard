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

            $path = explode(getDir(), $request->getUri()->getPath());
            $_SESSION['redirect'] = $path[1];
            
            return redirect('/login');
        }

        return $handler->handle($request);
    }
}
