<?php

namespace App\Interfaces;

/**
 * User role Repository Interface
 * 
 * Defines contract for user role data access
 * Follows Repository pattern
 */
interface RoleRepositoryInterface
{
    /**
     * Find role by ID
     */
    public function findById(int $id): ?array;

    /**
     * Find role by email
     */
    public function findByName(string $name): ?array;

    /**
     * Get all role with pagination
     */
    public function getAll(int $page = 1, int $perPage = 10): array;

    /**
     * Create new user role
     */
    public function create(array $data): int;

    /**
     * Update user role
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete user role
     */
    public function delete(int $id): bool;
}
