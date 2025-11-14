<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\UserRepositoryInterface;

/**
 * Authentication Service
 * 
 * Handles user authentication logic
 * Implements business rules for login/logout
 */
class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password): bool
    {
        // Find user by email
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $user[0]['is_active'] === 0) {
            return false;
        }

        return password_verify($password, $user[0]['password']) ?
            ($_SESSION['user'] = [
                'id'      => $user[0]['id'],
                'role_id' => $user[0]['role_id'],
                'logged'  => true
            ]) && true
            : false;
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
        session_unset();
        session_destroy();
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
