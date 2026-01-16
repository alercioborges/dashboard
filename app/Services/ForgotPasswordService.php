<?php

namespace App\Services;

use App\Interfaces\ForgotPasswordServiceInterface;
use App\Interfaces\UserRepositoryInterface;

class ForgotPasswordService implements ForgotPasswordServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function process(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return;
        }

        $token = bin2hex(random_bytes(32)); // 64 chars
        
        $expiresAt = (new \DateTimeImmutable())->modify('+1 hour');

        /**
         * 5️⃣ Persistência do token
         * 
         * Aqui você TEM DUAS OPÇÕES:
         * 
         * ✔ Opção A (recomendada):
         * Criar tabela password_resets
         * 
         * ✔ Opção B:
         * Salvar no próprio usuário
         */

        // EXEMPLO (tabela password_resets):
        /*
        $this->userRepository->storePasswordResetToken(
            $user['id'],
            password_hash($token, PASSWORD_DEFAULT),
            $expiresAt
        );
        */

        /**
         * 6️⃣ Envio de e-mail
         *
         * Exemplo de link:
         * https://seusite.com/reset-password?token=XXXX
         */

        /*
        $resetLink = $_ENV['APP_URL'] . '/reset-password?token=' . $token;

        $this->mailer->send(
            $email,
            'Redefinição de senha',
            'emails/reset-password.twig',
            [
                'user' => $user,
                'link' => $resetLink,
                'expiresAt' => $expiresAt
            ]
        );
        */
    }
}
