<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RememberMeRepositoryInterface;

/**
 * Authentication Service
 * 
 * Handles user authentication logic
 * Implements business rules for login/logout
 */
class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private RememberMeRepositoryInterface $rememberMeRepository;

    public function __construct(UserRepositoryInterface $userRepository, RememberMeRepositoryInterface $rememberMeRepository)
    {
        $this->userRepository = $userRepository;
        $this->rememberMeRepository = $rememberMeRepository;
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password, bool $remember = false): bool
    {
        // Find user by email
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $user[0]['is_active'] === 0 || !password_verify($password, $user[0]['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id'      => $user[0]['id'],
            'role_id' => $user[0]['role_id'],
            'logged'  => true
        ];

        if ($remember) {
            $this->createRememberMeToken($user[0]['id']);
        }

        return true;
    }


    private function createRememberMeToken(int $userId)
    {
        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $expiresAt = new \DateTime('7 days');

        $result = $this->rememberMeRepository->store($userId, $hash, $expiresAt);

        setcookie(
            'remember_me',
            $token,
            [
                'expires'  => $expiresAt->getTimestamp(),
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        return $result;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        if (
            !isset($_SESSION['user'])
            || empty($_SESSION['user'])
            || $_SESSION['user']['logged'] !== true
            || !(int)$_SESSION['user']['id'] > 0
        ) {
            return false;
        }

        if (!empty($_COOKIE['remember_me'])) {
            return $this->loginViaRememberMe($_COOKIE['remember_me']);
        }

        return true;
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 60,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }
    }

    /**
     * Check if current user has permission
     */

    /*
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $this->userRepository->hasPermission($user['id'], $permission);
    }
    */
}
