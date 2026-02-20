<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Interfaces\UserRepositoryInterface;

class MainUserMiddleware implements MiddlewareInterface
{
    private UserRepositoryInterface $mainUser;

    public function __construct(UserRepositoryInterface $mainUser)
    {
        $this->mainUser = $mainUser;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->mainUser->countAll() === 0) {        
            return redirect('/main-user');
        }

        return $handler->handle($request);

    }
}
