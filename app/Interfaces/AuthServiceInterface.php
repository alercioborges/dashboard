<?php

namespace App\Interfaces;

/**
 * Authentication Service Interface
 * 
 * Defines contract for authentication services
 * Follows Interface Segregation Principle
 */
interface AuthServiceInterface
{
    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password): array;

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool;

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): ?array;

    /**
     * Check if current user has permission
     */
    public function hasPermission(string $permission): bool;
}
