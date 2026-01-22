<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\UserService;
use App\Services\Validators\Validator;
use App\Models\Role;

class UserController extends Controller
{
    private UserService $userService;
    private Validator $validator;
    private Role $role;

    public function __construct(Twig $twig, UserService $userService, Validator $validator, Role $role)
    {
        parent::__construct($twig);
        $this->userService = $userService;
        $this->validator   = $validator;
        $this->role        = $role;
    }

    public function show(Request $request, Response $response): Response
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $perPage = 10;

        try {

            $pagination = $this->userService->getPaginatedUsers($page, $perPage);

            return $this->twig->render(
                $response,
                'pages/users.twig',
                [
                    'TITLE'        => 'Lista de usuários',
                    'USERS'        => $pagination['data'],
                    'NUM_PAGES'    => $pagination['numPages'],
                    'CURRENT_PAGE' => $pagination['currentPage']
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/users.twig',
                [
                    'TITLE' => 'Lista de usuários',
                    'ERROR' => 'Não foi possível carregar lista usuários'
                ]
            );
        }
    }


    public function create(Request $request, Response $response): Response
    {
        try {

            $userRoles = $this->role->getAll($this->role->countAll(), 0);

            return $this->twig->render(
                $response,
                'pages/users-create.twig',
                [
                    'TITLE'     => 'Cadastrar novo usuário',
                    'OLD_INPUT' => $this->getOldInput(),
                    'ROLES'     => $userRoles
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/users.twig',
                [
                    'TITLE' => 'Cadastrar novo usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar cadastrar novo usuário'
                ]
            );
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {

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

            flash('message', success('Usuário criado com sucesso'));

            return redirect('/admin/users');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/users-create.twig',
                [
                    'TITLE' => 'Cadastrar novo usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar cadastrar novo usuário'
                ]
            );
        }
    }

    public function profile(Request $request, Response $response, array $arg): Response
    {
        try {

            $userData = $this->userService->getUserById((int) $arg['id']);

            return $this->twig->render(
                $response,
                'pages/user-profile.twig',
                [
                    'TITLE'     => 'Perfil do usuário',
                    'USER_DATA' => $userData
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/user-profile.twig',
                [
                    'TITLE' => 'Cadastrar novo usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar visualizar perfil de usuário'
                ]
            );
        }
    }

    public function edit(Request $request, Response $response, array $arg): Response
    {
        try {

            $userData = $this->userService->getUserById((int) $arg['id']);
            $userRoles = $this->role->getAll($this->role->countAll(), 0);

            return $this->twig->render(
                $response,
                'pages/user-edit.twig',
                [
                    'TITLE' => 'Modificar perfil',
                    'USER_DATA' => $userData,
                    'ROLES' => $userRoles,
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/users.twig',
                [
                    'TITLE' => 'Lista de usuários',
                    'ERROR' => 'Não é possível atualizar o usuários'
                ]
            );
        }
    }

    public function update(Request $request, Response $response, array $arg): Response
    {
        try {

            $data = $this->validator->validate([
                'firstname' => 'required:max@30:min@2:onlyLetter:uppercase',
                'lastname'  => 'required:max@30:min@2:onlyLetter:uppercase',
                'email'     => 'required:email:max@60',
                'role_id'   => 'required'
            ]);

            if ($this->userService->emailExists($data['email'], (int) $arg['id'])) {
                $this->validator->setError('email', 'Esse e-mail já existe');
            }

            if ($this->validator->hasErrors($data)) {
                $this->setOldInput($data);
                back();
            }

            $this->userService->updateUser((int) $arg['id'], $data);

            flash('message', success('Usuário atualizado com sucesso'));

            return redirect('/admin/users');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/user-edit.twig',
                [
                    'TITLE' => 'Cadastrar novo usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar atualizar cadastro de  usuário'
                ]
            );
        }
    }

    public function delete(Request $request, Response $response, array $arg): Response
    {
        try {

            $userData = $this->userService->getUserById((int) $arg['id']);

            return $this->twig->render(
                $response,
                'pages/user-delete.twig',
                [
                    'TITLE'     => 'Excluir cadastro de usuário',
                    'USER_DATA' => $userData
                ]
            );

        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/users-delete.twig',
                [
                    'TITLE' => 'Excluir cadastro de usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar excluir cadastro de usuário'
                ]
            );
        }
    }

    public function destroy(Request $request, Response $response, array $arg): Response
    {
        try {

            $this->userService->deleteUser((int) $arg['id']);

            flash('message', success('Usuário excluído com sucesso'));

            return redirect('/admin/users');

        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/user-delete.twig',
                [
                    'TITLE' => 'Lista de usuários',
                    'ERROR' => 'Ocorreu um erro ao tentar excluir cadastro de  usuário'
                ]
            );
        }
    }
}
