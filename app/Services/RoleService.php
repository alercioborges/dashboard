<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;

/**
 * User Role Service
 * 
 * Business logic for user role management
 * 
 */
class RoleService
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Create new user with validation
     */
    /**
     * Create new user role
     */
    public function createUser(array $data): array
    {
        $roleId = $this->roleRepository->create($data);

        return [
            'success' => true,
            'role_id' => $roleId
        ];
    }

    /**
     * Get user role by ID
     */
    public function getUserById(int $id): ?array
    {
        //return $this->userRepository->findById($id);
        return [];
    }

    /**
     * Get user by name
     */
    public function getUserByName(string $name): ?array
    {
        //return $this->userRepository->findByEmail($email);
        return [];
    }

    public function updateUserRole(int $id, array $data): bool
    {
        //return $this->userRepository->update($id, $data);
        return true;
    }

    public function nameExists(string $name, int $id): ?array
    {
        //return $this->userRepository->findFieldExists('email', $email, 'id', $id);
        return [];
    }

    public function countUsers(): ?int
    {
        return $this->roleRepository->countAll();
    }

    public function getPaginatedRole(int $page, int $limit): array
    {
        $total    = $this->countUsers();
        $numPages = (int) ceil($total / $limit);
        $offset   = ($page - 1) * $limit;

        $data = $this->roleRepository->getAll($limit, $offset);

        return [
            'data' => $data,
            'numPages' => $numPages,
            'currentPage' => $page,
            'total' => $total
        ];
    }
}
