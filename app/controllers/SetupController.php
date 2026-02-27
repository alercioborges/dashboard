<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

use App\Core\Controller;
use App\Services\UserService;
use App\Services\Validators\Validator;
use App\Services\AuthService;
use Dom\Attr;

class SetupController extends Controller
{
    private UserService $userService;
    private Validator $validator;
    private LoggerInterface $logger;
    private AuthService $authService;

    public function __construct(Twig $twig, UserService $userService, Validator $validator, LoggerInterface $logger, AuthService $authService)
    {
        parent::__construct($twig);
        $this->userService = $userService;
        $this->validator   = $validator;
        $this->logger      = $logger;
        $this->authService = $authService;
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

            $this->logger->error('Error while trying to save new site administrator user om setup: ' . $e->getMessage() . " onfile " . $e->getFile() . " on line " . $e->getLine());

            return $this->twig->render(
                $response,
                'pages/error.html',
                [
                    'TITLE' => 'Cadastrar administrador do site'
                ]
            );
        }
    }


    public function store(Request $request, Response $response): Response
    {
        $data = $this->validator->validate([
            'firstname' => 'required:max@30:min@2:onlyLetter:uppercase',
            'lastname'  => 'required:max@30:min@2:onlyLetter:uppercase',
            'email'     => 'required:email:max@60',
            'password'  => 'required:max@30:min@6'
        ]);

        if ($this->validator->hasErrors($data)) {
            $this->setOldInput($data);
            back();
        }

        $user = $this->userService->createUser($data);
        $this->userService->changeUserRole($user['user_id'], 2);

         $logged = $this->authService->authenticate($data['email'], $data['password'], true);

        return redirect('/');
    }
}
