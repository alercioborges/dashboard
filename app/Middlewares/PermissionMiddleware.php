<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Interfaces\AuthServiceInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    private AuthServiceInterface $authService;
    private string $permission;

    public function __construct(
        AuthServiceInterface $authService,
        string $permission
    ) {
        $this->authService = $authService;
        $this->permission = $permission;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        if (!$this->authService->hasPermission($this->permission)) {
            flash('error', 'Acesso não autorizado');
            return redirect('/');
        }

        return $handler->handle($request);
    }
}
