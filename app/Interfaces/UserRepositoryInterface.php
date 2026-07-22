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
     * Get all active users with pagination
     */
    public function getAll(int $limit = 10, int $offset = 0, array $search = []): array;

    /**
     * Create new user
     */
    public function create(array $data): int;

    /**
     * Update user
     */
    public function update(int $id, array $data): bool;

    public function changeRole(int $userId, int $roleId):bool;

    /**
     * find user by field
     */
    public function findFieldExists(string $field, string $value, string $key, int $id): ?array;

    /**
     * Delete user
     */
    public function delete(int $id): bool;

    /**
     * Get number of active users
     */
    public function countAll(): int;

    /**
     * Get number of active users matching search filter
     */
    public function countFiltered(array $search = []): int;

    public function storePasswordReset(
        int $userId,
        string $tokenHash,
        \DateTimeImmutable $expiresAt
    ): ?int;

    public function findValidPasswordReset(int $forgotId, string $token): ?array;

     public function updatePassword(int $forgotId, int $userId, string $password): bool;

    public function deleteExpiredToken(): bool;
}
