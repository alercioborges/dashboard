<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

use App\Core\Controller;
use App\Services\UserService;
use App\Services\Validators\Validator;

class SetupController extends Controller
{
    private UserService $userService;
    private Validator $validator;
    private LoggerInterface $logger;

    public function __construct(Twig $twig, UserService $userService, Validator $validator, LoggerInterface $logger)
    {
        parent::__construct($twig);
        $this->userService = $userService;
        $this->validator   = $validator;
        $this->logger      = $logger;
    }

    public function index(Request $request, Response $response): Response
    {
        try {

            return $this->twig->render(
                $response,
                'pages/setup-user.twig',
                [
                    'TITLE'     => 'Cadastrar administrador do site',
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );

        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/error.html',
                [
                    'TITLE' => 'Cadastrar administrador do site'
                ]
            );

            $this->logger->error('Error while trying to save new site administrator user om setup: ' . $e->getMessage() ." onfile ". $e->getFile() ." on line ". $e->getLine());
        }
    }


    public function store(Request $request, Response $response): Response {}
}
