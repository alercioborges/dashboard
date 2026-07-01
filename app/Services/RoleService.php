<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;
use App\Services\PaginationService;

/**
 * User Role Service
 * 
 * Business logic for user role management
 * 
 */
class RoleService
{
    private RoleRepositoryInterface $roleRepository;
    private PaginationService $pagination;

    public function __construct(RoleRepositoryInterface $roleRepository, PaginationService $pagination)
    {
        $this->roleRepository = $roleRepository;
        $this->pagination = $pagination;
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
        return $this->roleRepository->findById($id);
    }

    /**
     * Get role by name
     */
    public function getRoleByName(string $name): ?array
    {
        return $this->roleRepository->findByName($name);
    }

    public function updateUserRole(int $id, array $data): bool
    {
        return $this->roleRepository->update($id, $data);
    }

    public function nameExists(string $name, int $id): ?array
    {
        return $this->roleRepository->findFieldExists('name', $name, 'id', $id);
    }

    public function deleteRole(int $id): bool
    {
        return $this->roleRepository->delete($id);
    }

    public function countUsers(): ?int
    {
        return $this->roleRepository->countAll();
    }

    public function getPaginatedRole(int $page, int $limit): array
    {
        $pagination = $this->pagination->paginate($page, $limit, $this->countUsers());
        $data =  $this->roleRepository->getAll($limit, $pagination['offset']);

        return [
            'data'        => $data,
            'numPages'    => $pagination['numPages'],
            'currentPage' => $page,
            'total'       => $this->countUsers()
        ];
    }
}
