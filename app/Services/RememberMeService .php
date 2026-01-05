<?php

namespace App\Services;

use App\Interfaces\RememberMeServiceInterface;
use App\Repositories\RememberTokenRepository;

class RememberMeService implements RememberMeServiceInterface
{
    private const COOKIE_NAME = 'remember_me';
    private const EXPIRATION_DAYS = 30;

    public function __construct(
        private RememberTokenRepository $repository
    ) {}

    public function create(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $this->repository->store(
            $userId,
            $hash,
            now()->modify('+'.self::EXPIRATION_DAYS.' days')
        );

        $this->setCookie($token);
    }

    public function autoLogin(): ?int
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return null;
        }

        $token = $_COOKIE[self::COOKIE_NAME];
        $hash  = hash('sha256', $token);

        $userId = $this->repository->findValidUserByToken($hash);

        if (!$userId) {
            $this->forget();
            return null;
        }

        // 🔄 Rotação do token
        $this->repository->delete($hash);
        $this->create($userId);

        return $userId;
    }

    public function forget(): void
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return;
        }

        $hash = hash('sha256', $_COOKIE[self::COOKIE_NAME]);
        $this->repository->delete($hash);

        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
    }

    private function setCookie(string $token): void
    {
        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires'  => time() + (86400 * self::EXPIRATION_DAYS),
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );
    }
}
