<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Interfaces\UserRepositoryInterface;

class SetupMiddleware implements MiddlewareInterface
{
    private UserRepositoryInterface $mainUser;

    public function __construct(UserRepositoryInterface $mainUser)
    {
        $this->mainUser = $mainUser;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();        

        $relativePath = str_starts_with($path, getDir())
            ? substr($path, strlen(getDir()))
            : $path;

        $isSetupRoute = in_array($relativePath, ['/setup', '/setup/']);

        $hasUsers = $this->mainUser->countAll() > 0;

        // Nenhum usuário cadastrado → só permite /setup
        if (!$hasUsers) {
            if (!$isSetupRoute) {
                return redirect('/setup');
            }

            // Deixa passar sem AuthMiddleware
            return $handler->handle($request);
        }

        // Já existe usuário → bloqueia acesso ao /setup e segue fluxo normal
        if ($isSetupRoute) {
            return redirect('/');
        }

        return $handler->handle($request);
    }
}
