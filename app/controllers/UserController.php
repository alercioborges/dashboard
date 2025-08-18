<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Core\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(Request $request, Response $response): Response
    {
        $users = $this->userService->getAllUsers();

        return $this->twig->render($response, 'users.html', $users);
    }
}
