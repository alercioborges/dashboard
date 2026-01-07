<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

use App\Core\Controller;
use App\Interfaces\AuthServiceInterface;
use App\Services\Validators\Validator;

/**
 * Authentication Controller
 * 
 * Handles user authentication (login/logout)
 */
class AuthController extends Controller
{
    private AuthServiceInterface $authService;
    private LoggerInterface $logger;
    private Validator $validator;

    public function __construct(
        Twig $twig,
        AuthServiceInterface $authService,
        LoggerInterface $logger,
        Validator $validator
    ) {
        parent::__construct($twig);
        $this->authService = $authService;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * Show login form
     */
    public function index(Request $request, Response $response): Response
    {
        try {

            // Redirect if already authenticated
            if (isset($_SESSION['user'])) {
                return redirect('/');
            }

            return $this->twig->render(
                $response,
                'auth.html',
                [
                    'TITLE' => 'Acessar',
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'error.html'
            );
        }
    }

    /**
     * Process login
     */
    public function login(Request $request, Response $response): Response
    {
        try {

            $data = $this->validator->validate([
                'email'     => 'required:email',
                'password'  => 'required'
            ]);
            
            if ($this->validator->hasErrors($data)) {
                $this->setOldInput($data);
                back();
            }
            
            $logged = $this->authService->authenticate($data['email'], $data['password'], $data['remember']);

            if ($logged) {

                if (isset($_SESSION['redirect'])) {
                    $redirect = $_SESSION['redirect'];
                    unset($_SESSION['redirect']);
                    return redirect($redirect);
                }

                return redirect('/');
            }

            $this->setOldInput($data);

            flash('error', error("Nome de usuário e/ou senha incorreto"));
            return redirect('/login');
            
        } catch (\Exception $e) {

            return redirect('/login');
        }
    }

    /**
     * Process logout
     */
    public function logout(Request $request, Response $response): Response
    {
        try {

            $this->authService->logout();

            return redirect('/login');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'dashboard.html.twig',
                [
                    'TITLE' => 'Lista de usuários',
                    'ERROR' => 'Não é possível atualizar o usuários'
                ]
            );
        }
    }
}
