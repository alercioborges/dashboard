<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

use App\Core\Controller;
use App\Interfaces\ForgotPasswordServiceInterface;
use App\Services\Validators\Validator;

/**
 * Forgot Password Controller
 * 
 * 
 */
class ForgotPasswordController extends Controller
{
    private ForgotPasswordServiceInterface $forgotService;
    private Validator $validator;
    private LoggerInterface $logger;

    public function __construct(
        Twig $twig,
        ForgotPasswordServiceInterface $forgotService,
        Validator $validator,
         LoggerInterface $logger
    ) {
        parent::__construct($twig);
        $this->forgotService = $forgotService;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * Show forgot password form
     */
    public function index(Request $request, Response $response): Response
    {
        try {

            // Redirect if already authenticated
            if (isset($_SESSION['user'])) {
                return redirect('/');
            }

            $sent = $_SESSION['forgot_password_sent'] ?? false;
            unset($_SESSION['forgot_password_sent']);

            $redefined = $_SESSION['redefined_password'] ?? false;
            unset($_SESSION['redefined_password']);

            return $this->twig->render(
                $response,
                'pages/forgot.html',
                [
                    'TITLE'     => 'Esqueceu a senha',
                    'OLD_INPUT' => $this->getOldInput(),
                    'SENT'      => $sent,
                    'REDEFINED' => $redefined
                ]
            );
        } catch (\Exception $e) {

            return $this->twig->render(
                $response,
                'pages/error.html'
            );
        }
    }

    /**
     * Process login
     */
    public function redefine(Request $request, Response $response): Response
    {
        try {

            $data = $this->validator->validate([
                'email' => 'required:email'
            ]);

            if ($this->validator->hasErrors($data)) {
                $this->setOldInput($data);
                back();
            }

            $this->forgotService->process($data['email']);

            $_SESSION['forgot_password_sent'] = true;

            $response = redirect('/forgot');

            ignore_user_abort(true);
            
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }

            return $response;
            
        } catch (\Exception $e) {

            return redirect('/forgot');
        }
    }


    public function reset(Request $request, Response $response): Response
    {
        $token = $request->getQueryParams()['token'] ?? null;

        if (!$token) {
            return redirect('/forgot');
        }

        $reset = $this->forgotService->validateToken($token);

        if (!$reset) {
            flash('message', error('Token inválido ou expirado.'));
            return redirect('/forgot');
        }

        return $this->twig->render(
            $response,
            'pages/reset-password.html',
            [
                'OLD_INPUT' => $this->getOldInput(),
                'TOKEN' => $token
            ]
        );
    }


    public function store(Request $request, Response $response): Response
    {
        $data = $this->validator->validate([
            'token' => 'required',
            'password' => 'required',
            'password-confirm' => 'required'
        ]);

        if ($data['password'] !== $data['password-confirm']) {
            $this->validator->setError('password', 'Estas senhas não são iguais');
            $this->validator->setError('password-confirm', 'Estas senhas não são iguais');
        }

        if ($this->validator->hasErrors($data)) {
            $this->setOldInput($data);
            back();
        }

        $reset = $this->forgotService->validateToken($data['token']);

        if (!$reset) {
            flash('message', error('Token inválido ou expirado.'));
            return redirect('/forgot');
        }

        $this->forgotService->resetPassword(
            (int) $reset['user_id'],
            $data['password']
        );

        $_SESSION['redefined_password'] = true;

        return redirect('/forgot');
    }
}
