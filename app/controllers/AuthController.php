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
        // Redirect if already authenticated
        if (isset($_SESSION['user'])) {
            return redirect('/');
        }

        return $this->twig->render(
            $response,
            'auth.html',
            [
                'TITLE' => 'Acessar',
                'OLDPUT_EMAIL' => $_SESSION['email'] ?? NULL
            ]
        );
    }

    /**
     * Process login
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $result = $this->authService->authenticate($email, $password);

        if ($result['success']) {
            $_SESSION['user'] = $result['user'];

            $this->logger->info('User logged in successfully', [
                'user_id' => $result['user']['id'],
                'email' => $result['user']['email']
            ]);

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        $_SESSION['errors'] = $result['errors'];

        $this->logger->warning('Failed login attempt', [
            'email' => $email,
            'ip' => $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    /**
     * Process logout
     */
    public function logout(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user']['id'] ?? null;

        session_destroy();

        if ($userId) {
            $this->logger->info('User logged out', ['user_id' => $userId]);
        }

        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}
