<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, '/pages/dashboard.html');
    }
}
