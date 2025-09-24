<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;

/**
 * User Service
 * 
 * Business logic for user roles management
 */
class RoleService
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get all user roles with pagination
     */
    public function getAllUserRoles(int $page = 1, int $perPage = 10): array
    {
        return $this->roleRepository->getAll($page, $perPage);
    }

    /**
     * Create new user role
     */
    public function createUserRole(array $data): array
    {
        $userId = $this->roleRepository->create($data);

        return [
            'success' => true,
            'user_id' => $userId
        ];
    }

    /**
     * Get user role by ID
     */
    public function getUserRoleById(int $id): ?array
    {
        return $this->roleRepository->findById($id);
    }
}
