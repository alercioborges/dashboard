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

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?array
    {
        return $this->userRepository->findByEmail($email);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function updateUser(int $id, array $data): bool
    {
        return $this->userRepository->update($id, $data);
    }

    public function emailExists(string $email, int $id): ?array
    {
        return $this->userRepository->findFieldExists('email', $email, 'id', $id);
    }

    public function countUsers(): ?int
    {
        return $this->userRepository->countAll();
    }

    public function getPaginatedUsers(int $page, int $limit): array
    {
        $total    = $this->countUsers();
        $numPages = (int) ceil($total / $limit);
        $offset   = ($page - 1) * $limit;
        
        $data =  $this->userRepository->getAll($limit, $offset);
        
        return [
            'data'        => $data,
            'numPages'    => $numPages,
            'currentPage' => $page,
            'total'       => $total
        ];
    }
}
