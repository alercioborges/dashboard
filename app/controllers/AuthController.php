<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Interfaces\AuthServiceInterface;
use App\Services\Validators\Validator;
use App\Services\RateLimiterService;
use Psr\Log\LoggerInterface;

/**
 * Authentication Controller
 * 
 * Handles user authentication (login/logout)
 */
class AuthController extends Controller
{
    private AuthServiceInterface $authService;
    private Validator $validator;
    private RateLimiterService $rateLimiter;
    private LoggerInterface $logger;

    public function __construct(
        Twig $twig,
        AuthServiceInterface $authService,
        LoggerInterface $logger,
        Validator $validator,
        RateLimiterService $rateLimiter
    ) {
        parent::__construct($twig);
        $this->authService = $authService;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Show login form
     */
    public function index(Request $request, Response $response): Response
    {
        try {

            // Redirect if already authenticated
            if ($this->authService->isAuthenticated()) {
                return redirect('/');
            }

            return $this->twig->render(
                $response,
                'pages/auth.twig',
                [
                    'TITLE' => 'Acessar',
                    'OLD_INPUT' => $this->getOldInput()
                ]
            );
        } catch (\Exception $e) {

            $this->logger->error('[AUTH_ERROR]: ' . $e);

            return $this->twig->render(
                $response,
                'pages/error.html'
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

            if ($this->validator->hasErrors()) {
                $this->setOldInput($data);
                back();
            }

            $remember = (bool) ($data['remember'] ?? false);

            $logged = $this->authService->authenticate($data['email'], $data['password'], $remember);

            if ($logged) {
                
                // Clears rate limit for this account
                $ip = \App\Support\ClientIp::resolve($request);
                $this->rateLimiter->clear("email:" . mb_strtolower($data['email']) . ":login", 'login');
                $this->rateLimiter->clear("ip:{$ip}", 'login');

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

            if (isset($_SESSION['user']['id'])) {
                $this->authService->logout($_SESSION['user']['id']);
            }

            return redirect('/login');
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/dashboard.twig',
                [
                    'TITLE' => 'Lista de usuários',
                    'ERROR' => 'Não é possível atualizar o usuários'
                ]
            );
        }
    }
}
