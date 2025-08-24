<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(Twig $twig, UserService $userService)
    {
        parent::__construct($twig);
        $this->userService = $userService;
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'admin.html',
            [
                'TITLE' => 'Administração do site'
            ]
        );
    }

    public function show(Request $request, Response $response): Response
    {
        $users = $this->userService->getAllUsers();

        return $this->twig->render(
            $response,
            'users.html',
            [
                'TITLE' => 'Lista de usuários',
                'USERS' => $users
            ]
        );
    }
}
