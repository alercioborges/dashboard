<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;

class AuthController extends Controller
{

    public function __construct(Twig $twig)
    {
        parent::__construct($twig);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'auth.html',
            [
                'TITLE' => 'Acessar'
            ]
        );
    }
}
