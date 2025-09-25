<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\RoleRepositoryInterface;

class Role extends Model implements RoleRepositoryInterface
{
    protected string $table = 'tbl_role';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        $userData = $this->queryBuilder->selectWithJoin(
            $this->table,
        );

        return $userData[0];
    }

    /**
     * Find user by nmae
     */
    public function findByName(string $namel): ?array
    {
        return $this->queryBuilder->select(
            $this->table,
        );
    }

    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 10): array
    {
        return $this->queryBuilder->selectWithJoin(
            $this->table,
        );
    }

    /**
     * Create new user
     */
    public function create(array $data): int
    {
        return $this->queryBuilder->insert(
            $this->table,
            [
                'id'          => NULL,
                'name'   => $data['firstname'],
            ]
        );
    }


    /**
     * Update user role
     */
    public function update(int $id, array $data): bool
    {
        return true;
    }


    /**
     * Delete user role
     */
    public function delete(int $id): bool
    {
        return true;
    }
}
