<?php

namespace App\Services;

use App\Interfaces\ForgotPasswordServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\MailerService;
use Psr\Log\LoggerInterface;


class ForgotPasswordService implements ForgotPasswordServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private MailerService $mailer;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $userRepository, MailerService $mailer, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->logger = $logger;
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
        $this->userRepository->storePasswordReset(
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


        $resetLink = getUrl() . '/reset-password?token=' . $token;

        try {
            $this->mailer->send(
                $email,
                'Redefinição de senha',
                '<h1>Teste de envio</h1>',
                'Teste de envio'
            );

            $this->logger->info('E-mail enviado com sucesso para ' . $email);



        } catch (\Throwable $e) {
            
            $this->logger->error('Error sending email.',
            [
                'exception' => $e->getMessage()
            ]);
        }

        /*
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
