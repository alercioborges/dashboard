<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\RoleService;
use App\Services\Validators\Validator;
use App\Services\UserService;

class RoleController extends Controller
{
    private RoleService $roleService;
    private Validator $validator;
    private UserService $userService;

    public function __construct(
        Twig $twig,
        RoleService $roleService,
        Validator $validator,
        UserService $userService
    ) {
        parent::__construct($twig);
        $this->roleService = $roleService;
        $this->validator = $validator;
        $this->userService = $userService;
    }


    public function show(Request $request, Response $response): Response
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $perPage = 10;

        try {

            $pagination = $this->roleService->getPaginatedAllCreated($page, $perPage);

            return $this->twig->render(
                $response,
                'pages/roles-user.twig',
                [
                    'TITLE' => 'Lista de perfis de usuários',
                    'ROLES' => $pagination['data'],
                    'NUM_PAGES'    => $pagination['numPages'],
                    'CURRENT_PAGE' => $pagination['currentPage']
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-user.twig',
                [
                    'TITLE' => 'Lista de perfis de usuários',
                    'ERROR' => 'Não foi possível carregar lista usuários'
                ]
            );
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {

            return $this->twig->render(
                $response,
                'pages/roles-create.twig',
                [
                    'TITLE'     => 'Criar novo perfil de usuário',
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-create.twig',
                [
                    'TITLE' => 'Criar novo perfil usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar criar novo perfil de usuário'
                ]
            );
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {

            $data = $this->validator->validate([
                'name'        => 'required:max@30:min@2:onlyLetter:uppercase',
                'shortname'   => 'required:max@30:min@2',
                'description' => 'required:max@255:min@5'
            ]);

            if ($this->roleService->getRoleByName($data['name'])) {
                $this->validator->setError('name', 'Esse nome já existe');
            }

            if ($this->roleService->getRoleByShortname($data['shortname'])) {
                $this->validator->setError('shortname', 'Esse nome breve já existe');
            }

            if ($this->validator->hasErrors()) {
                $this->setOldInput($data);
                back();
            }

            $this->roleService->createRole($data);

            flash('message', success('Perfil criado com sucesso'));

            return redirect('/admin/roles');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-create.twig',
                [
                    'TITLE' => 'Criar novo perfil de usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar criar novo perfil de usuário'
                ]
            );
        }
    }

    public function edit(Request $request, Response $response, array $arg): Response
    {
        try {

            $roleData = $this->roleService->getRoleById((int) $arg['id']);

            return $this->twig->render(
                $response,
                'pages/roles-user-edit.twig',
                [
                    'TITLE'     => 'Editar perfil de usuário',
                    'ROLE_DATA' => $roleData,
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-user.twig',
                [
                    'TITLE' => 'Lista de perfis de usuários',
                    'ERROR' => 'Ocorreu um erro ao tentar carregar perfil de usuário'
                ]
            );
        }
    }

    public function update(Request $request, Response $response, array $arg): Response
    {
        try {

            $data = $this->validator->validate([
                'name'        => 'required:max@30:min@2:onlyLetter:uppercase',
                'shortname'   => 'required:max@30:min@2',
                'description' => 'required:max@255:min@6'
            ]);

            if ($this->roleService->nameExists($data['name'], (int) $arg['id'])) {
                $this->validator->setError('name', 'Esse nome já existe');
            }

            if ($this->roleService->shortnameExists($data['shortname'], (int) $arg['id'])) {
                $this->validator->setError('shortname', 'Esse nome breve já existe');
            }

            if ($this->validator->hasErrors()) {
                $this->setOldInput($data);
                back();
            }

            $this->roleService->updateRole((int) $arg['id'], $data);

            flash('message', success('Perfil atualizado com sucesso'));

            return redirect('/admin/roles');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-create.twig',
                [
                    'TITLE' => 'Editar perfil de usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar atualizar perfil de usuário'
                ]
            );
        }
    }

    public function destroy(Request $request, Response $response, array $arg): Response
    {
        try {
            
            $roleId = (int) $arg['id'];
            $readOlyRolyId = $this->roleService->getRoleByShortname('readonly');

            $users = $this->userService->getUserByRoleId($roleId);            
            
            if (!empty($users)) {
                $this->userService->switchUsersRole(array_column($users, 'id'), $readOlyRolyId['id']);
            }
            
            $this->roleService->deleteRole($roleId);

            flash('message', success('Perfil excluído com sucesso'));

            return redirect('/admin/roles');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-user.twig',
                [
                    'TITLE' => 'Lista de perfis de usuários',
                    'ERROR' => 'Ocorreu um erro ao tentar excluir perfil de usuário'
                ]
            );
        }
    }

    public function assignment(Request $request, Response $response): Response
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $perPage = 10;

        try {

            $pagination = $this->roleService->getPaginatedAll($page, $perPage);

            return $this->twig->render(
                $response,
                'pages/roles-assignment.twig',
                [
                    'TITLE'        => 'Atribuir papéis para usuário',
                    'ROLES'        => $pagination['data'],
                    'NUM_PAGES'    => $pagination['numPages'],
                    'CURRENT_PAGE' => $pagination['currentPage']
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/roles-assignment.twig',
                [
                    'TITLE' => 'Lista de perfis de usuários',
                    'ERROR' => 'Ocorreu um erro ao tentar carregar papéis de usuário'
                ]
            );
        }
    }

    public function assignUser(Request $request, Response $response, array $arg): Response
    {
        $roleId = (int) $arg['id'];
        $assignedUsers   = $this->userService->getAssignedRole($arg['id']);
        $unassignedUsers = $this->userService->getUnassignedRole($arg['id']);
        $roleData = $this->roleService->getRoleById($roleId);

        return $this->twig->render(
            $response,
            'pages/roles-assign-user.twig',
            [
                'TITLE'            => 'Atribuir papéis para usuário',
                'ROLE_ID'          => $roleId,
                'ROLE_SHORTNAME'   => $roleData['shortname'],
                'ASSIGNED_USERS'   => $assignedUsers,
                'UNASSIGNED_USERS' => $unassignedUsers
            ]
        );
    }

    public function assign(Request $request, Response $response): Response
    {
        $data   = (array) $request->getParsedBody();
        $roleId = (int) ($data['role_id'] ?? 0);
        $action = (string) ($data['action'] ?? '');
        $ids    = (array) ($data['user_ids'] ?? []);
        $role   = $this->roleService->getRoleById($roleId) ?? [];
        $target = $role ? '/admin/roles/' . $roleId . '/assignment' : '/admin/roles/assignment';

        try {

            if (!$role || empty($ids)) {
                flash('message', error('Selecione ao menos um usuário para atualizar'));
                return redirect($target);
            }

            $roleIdToApply = $roleId;

            if ($action === 'remove') {
                $defaultRole   = $this->roleService->getRoleByShortname('readonly');
                $roleIdToApply = (int) ($defaultRole['id'] ?? 0);
            }

            if ($action !== 'assign' && $action !== 'remove' || $roleIdToApply < 1) {
                flash('message', error('Não foi possível atualizar os usuários do perfil'));
                return redirect($target);
            }

            foreach ($ids as $userId) {
                $this->userService->assignUserRole($userId, $roleIdToApply);
            }

            flash('message', success('Usuários atualizados com sucesso'));

            return redirect($target);
        } catch (\Exception $e) {

            flash('message', error('Ocorreu um erro ao atualizar os usuários do perfil'));

            return redirect($target);
        }
    }
}
