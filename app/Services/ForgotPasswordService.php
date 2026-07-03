<?php

namespace App\Services;

use App\Interfaces\ForgotPasswordServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\MailerService;
use App\Services\TokenService;

use Psr\Log\LoggerInterface;

class ForgotPasswordService implements ForgotPasswordServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private MailerService $mailer;
    private LoggerInterface $logger;
    private TokenService $tokenService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MailerService $mailer,
        LoggerInterface $logger,
        TokenService $tokenService
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->tokenService = $tokenService;
    }


    public function process(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || (int)$user['is_active'] !== 1) {
            return;
        }

        $token = $this->tokenService->generateToken();

        $expiresAt = new \DateTimeImmutable('+1 hour');

        $forgotId = null;

        try {

            $forgotId = $this->userRepository->storePasswordReset(
                (int) $user['id'],
                password_hash($token, PASSWORD_DEFAULT),
                $expiresAt
            );
        } catch (\Exception $e) {

            $this->logger->error('Error while trying to save password reset token: ' . $e->getMessage());
        }

        if ($forgotId === null) {
            return;
        }

        try {

            $resetLink = getUrl() . '/forgot/reset-password?id=' . $forgotId . '&token=' . $token;



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


    public function validateToken(int $forgotId, string $token): ?array
    {
        return $this->userRepository->findValidPasswordReset($forgotId, $token);
    }


    public function resetPassword(int $forgotId, int $userId, string $password): bool
    {
        return $this->userRepository->updatePassword($forgotId, $userId, $password);
    }

    public function deleteToken()
    {
        return $this->userRepository->deleteExpiredToken();
    }
}
