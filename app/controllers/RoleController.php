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
}
