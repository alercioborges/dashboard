<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Services\RoleService;

class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(Twig $twig, RoleService $roleService)
    {
        parent::__construct($twig);
        $this->roleService = $roleService;
    }
}
