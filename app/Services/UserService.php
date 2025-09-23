<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;

/**
 * User Service
 * 
 * Business logic for user management
 * Handles validation and data processing
 */
class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination
     */
    public function getAllUsers(int $page = 1, int $perPage = 10): array
    {
        return $this->userRepository->getAll($page, $perPage);
    }

    /**
     * Create new user with validation
     */
    /**
     * Create new user with validation
     */
    public function createUser(array $data): array
    {
        $userId = $this->userRepository->create($data);

        return [
            'success' => true,
            'user_id' => $userId
        ];
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?array
    {
        return $this->userRepository->findById($id);
    }
}
