<?php

namespace App\Interfaces;

/**
 * User Repository Interface
 * 
 * Defines contract for user data access
 * Follows Repository pattern
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(int $id): ?array;

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array;

    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 10): array;

    /**
     * Create new user
     */
    public function create(array $data): int;

    /**
     * Update user
     */
    public function update(int $id, array $data): bool;

    /**
     * find user by field
     */
    public function findFieldExists($field, $value, $key, $id): ?array;

    /**
     * Delete user
     */
    public function delete(int $id): bool;
}
