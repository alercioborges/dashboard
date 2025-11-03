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
    public function authenticate(string $email, string $password): array
    {
        $errors = [];

        // Validate input
        if (empty($email)) {
            $errors[] = 'Email is required';
        }

        if (empty($passworrd)) {
            $errors[] = 'Password is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Find user by email
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'errors' => ['Invalid credentials']];
        }

        // Check if user is active
        if (!$user['is_active']) {
            return ['success' => false, 'errors' => ['Account is inactive']];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['Invalid credentials']];
        }

        // Remove sensitive data
        unset($user['password']);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
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
