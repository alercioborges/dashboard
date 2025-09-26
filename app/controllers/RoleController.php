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

    public function __construct(Twig $twig, RoleService $roleService, Validator $validator;)
    {
        parent::__construct($twig);
        $this->roleService = $roleService;
        $this->validator = $validator;
    }

    public function show(Request $request, Response $response): Response
    {
        $roles = $this->rolerService->getAllUserRoles();

        return $this->twig->render(
            $response,
            'roles-user.twig',
            [
                'TITLE' => 'Lista de perfis de usuários',
                'ROLES' => $roles
            ]
        );
    }
}
