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
     * Find user role by ID
     */
    public function findById(int $id): ?array;

    /**
     * Find user role by name
     */
    public function findByName(string $name): ?array;

    /**
     * Get all user roles with pagination
     */
    public function getAll(int $limit = 10, int $offset = 1): array;

    /**
     * Create new user role
     */
    public function create(array $data): int;

    /**
     * Update user role
     */
    public function update(int $id, array $data): bool;

    /**
     * find user role by field
     */
    public function findFieldExists($field, $value, $key, $id): ?array;

    /**
     * Delete user role
     */
    public function delete(int $id): bool;

    /**
     * Get number of user roles
     */
    public function countAll(): int;
}
