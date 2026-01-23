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

        if (!$user) { //|| (int)$user['is_active'] !== 1) {
            return;
        }

        $token = bin2hex(random_bytes(32));

        $expiresAt = new \DateTimeImmutable('+1 hour');

        try {

            $this->userRepository->storePasswordReset(
                (int) $user['id'],
                password_hash($token, PASSWORD_DEFAULT),
                $expiresAt
            );

        } catch (\Exception $e) {

            $this->logger->error('Error while trying to save password reset token: ' . $e->getMessage());
        }

        $resetLink = getUrl() . '/forgot/reset-password?token=' . $token;

        try {

            $this->mailer->send(
                $email,
                'Redefinição de senha',
                'reset-password.twig',
                [
                    'USER'       => $user,
                    'LINK'       => $resetLink,
                    'EXPIRES_AT' => $expiresAt
                ]
            );

            $this->logger->info(
                'Password reset email sent',
                ['email' => $email]
            );
        } catch (\Throwable $e) {

            $this->logger->error(
                'Error sending password reset email',
                [
                    'email'     => $email,
                    'exception' => $e->getMessage()
                ]
            );
        }
    }
}
