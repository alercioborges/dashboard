<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\RoleService;
use App\Services\Validators\Validator;

class RoleController extends Controller
{
    private RoleService $roleService;
    private Validator $validator;

    public function __construct(Twig $twig, RoleService $roleService, Validator $validator)
    {
        parent::__construct($twig);
        $this->roleService = $roleService;
        $this->validator = $validator;
    }


    public function show(Request $request, Response $response): Response
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $perPage = 10;

        try {

            $pagination = $this->roleService->getPaginatedRole($page, $perPage);

            return $this->twig->render(
                $response,
                'roles-user.twig',
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
                'roles-user.twig',
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
                'roles-create.twig',
                [
                    'TITLE'     => 'Criar novo perfil de usuário',
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'roles-create.twig',
                [
                    'TITLE' => 'Criar novo perfil usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar criar novo perfil de usuário'
                ]
            );
        }
    }

    public function store(Request $request, Response $response): Response
    {
        /*

        try {

            $data = $this->validator->validate([
                'name'       => 'required:max@30:min@2:onlyLetter:uppercase',
                'description' => 'required:max@30:min@6'
            ]);            

            if ($this->roleService->getRoleByName($data['name'])) {
                $this->validator->setError('name', 'Esse nome já existe');
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
                'users-create.twig',
                [
                    'TITLE' => 'Cadastrar novo usuário',
                    'ERROR' => 'Ocorreu um erro ao tentar cadastrar novo usuário'
                ]
            );
        }
        
        */

        return $response;
    }
}
