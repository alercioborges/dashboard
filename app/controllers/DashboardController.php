<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class DashboardController
{
    private Twig $twig;
    
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }
    
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {        
        return $this->twig->render($response, 'dashboard.html');
    }
}