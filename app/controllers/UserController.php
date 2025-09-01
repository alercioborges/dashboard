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
    private User $user;

    public function __construct(Twig $twig, UserService $userService)
    {
        parent::__construct($twig);
        $this->userService = $userService;
    }

    public function show(Request $request, Response $response): Response
    {
        $users = $this->userService->getAllUsers();

        return $this->twig->render(
            $response,
            'users.twig',
            [
                'TITLE' => 'Lista de usuários',
                'USERS' => $users
            ]
        );
    }


    public function create(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'users-create.twig',
            [
                'TITLE' => 'Cadastrar novo usuários'
            ]
        );
    }

    public function store()
    {
        dd($_POST);
    }
}
