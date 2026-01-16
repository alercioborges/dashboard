<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

use App\Core\Controller;
use App\Interfaces\ForgotPasswordServiceInterface;
use App\Services\Validators\Validator;

/**
 * Forgo tPassword Controller
 * 
 * 
 */
class ForgotPasswordController extends Controller
{
    private ForgotPasswordServiceInterface $forgotService;
    private Validator $validator;

    public function __construct(
        Twig $twig,
        ForgotPasswordServiceInterface $forgotService,
        Validator $validator
    ) {
        parent::__construct($twig);
        $this->forgotService = $forgotService;
        $this->validator = $validator;
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

            return $this->twig->render(
                $response,
                'forgot.html',
                [
                    'TITLE' => 'Esqueceu a senha',
                    'OLD_INPUT' => $this->getOldInput(),
                    'SENT' => $sent
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

            return redirect('/forgot');

            return redirect('/forgot');
        } catch (\Exception $e) {

            return redirect('/forgot');
        }
    }

    /**
     * Process logout
     
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
     */
}
