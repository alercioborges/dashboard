<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Services\PaginationService;

/**
 * User Service
 * 
 * Business logic for user management
 * Handles validation and data processing
 */
class UserService
{
    private UserRepositoryInterface $userRepository;
    private PaginationService $pagination;

    public function __construct(UserRepositoryInterface $userRepository, PaginationService $pagination)
    {
        $this->userRepository = $userRepository;
        $this->pagination = $pagination;
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

    public function changeUserRole(int $userId, int $roleId): bool
    {
        return $this->userRepository->changeRole($userId, $roleId);
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
        $total = $this->countUsers();
        
        $pagination = $this->pagination->paginate($page, $limit, $total);
        $data =  $this->userRepository->getAll($limit, $pagination['offset']);

        return [
            'data'        => $data,
            'numPages'    => $pagination['numPages'],
            'currentPage' => $page,
            'total'       => $total
        ];
    }
}
