<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\UserService;
use App\Services\Validators\Validator;

class UserController extends Controller
{
    private UserService $userService;
    private Validator $validator;

    public function __construct(Twig $twig, UserService $userService, Validator $validator)
    {
        parent::__construct($twig);
        $this->userService = $userService;
        $this->validator = $validator;
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
                'TITLE' => 'Cadastrar novo usuários',
                'OLD_INPUT' => $this->getOldInput()
            ]
        );
    }

    public function store()
    {
        $data = $this->validator->validate([
            'firstname' => 'required:max@30:min@2:onlyLetter:uppercase',
            'lastname'  => 'required:max@30:min@2:onlyLetter:uppercase',
            'email'     => 'required:email:max@60',
            'role_id'   => 'required',
            'password'  => 'required:max@30:min@6'
        ]);
        
        if ($this->userService->getUserByEmail($data['email'])) {
            $this->validator->setError('email', 'Esse e-mail já existe');            
        }
        
        if ($this->validator->hasErrors($data)) {
            $this->setOldInput($data);
            back();
        }

        $this->userService->createUser($data);

        return redirect('/admin/users/');
    }

    public function profile(Request $request, Response $response, array $arg): Response
    {
        $userData = $this->userService->getUserById($arg['id']);

        return $this->twig->render(
            $response,
            'user-profile.twig',
            [
                'TITLE' => 'Perfil do usuário',
                'USER_DATA' => $userData
            ]
        );
    }

    public function edit(Request $request, Response $response, array $arg): Response
    {
        $userData = $this->userService->getUserById((int) $arg['id']);

        return $this->twig->render(
            $response,
            'user-edit.twig',
            [
                'TITLE' => 'Modificar perfil',
                'USER_DATA' => $userData,
                'OLD_INPUT' => $this->getOldInput()
            ]
        );
    }

    public function update(Request $request, Response $response, array $arg): Response
    {
        $data = $this->validator->validate([
            'firstname' => 'required:max@30:min@2:onlyLetter:uppercase',
            'lastname'  => 'required:max@30:min@2:onlyLetter:uppercase',
            'email'     => 'required:email:max@60',
            'role_id'   => 'required'
        ]);

        if ($this->userService->emailExists($data['email'], $arg['id'])) {
            $this->validator->setError('email', 'Esse e-mail já existe');
        }

        if ($this->validator->hasErrors($data)) {
            $this->setOldInput($data);
            back();
        }

        $userData = $this->userService->updateUser($data, $arg['id']);

        return redirect('/admin/users/');
    }
}
