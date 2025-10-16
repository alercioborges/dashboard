<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\RoleRepositoryInterface;

class Role extends Model implements RoleRepositoryInterface
{
    protected string $table = 'tbl_roles';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    /**
     * Find user role by ID
     */
    public function findById(int $id): ?array
    {
        $roleData = $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'name'
            ],
            ['id' => $id]
        );

        return $roleData[0] ?? NULL;
    }

    /**
     * Find user role by name
     */
    public function findByName(string $name): ?array
    {
        $roleData = $this->queryBuilder->select(
            $this->table,
            ['name'],
            ['name LIKE' => '%' . $name . '%']
        );

        return $roleData ?? NULL;
    }


    /**
     * Get all user roles with pagination
     */
    public function getAll(int $limit = 10, int $offset = 1): array
    {
        return $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'name'
            ],
            [],
            [],
            $limit,
            $offset
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
                'name'   => $data['name'],
                'description'    => $data['description'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => NULL
            ]
        );
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->queryBuilder->update(
            $this->table,
            [
                'name'   => $data['firstname'],
                'description'    => $data['lastname'],
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            ['id' => $id]
        );

        return $result > 0;
    }

    /**
     * Get specific users data
     */
    public function findFieldExists($field, $value, $key, $id): ?array
    {
        return $this->fieldExists($field, $value, $key, $id);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        return true;
    }

    /**
     * Get number of active users
     */
    public function countAll(): int
    {
        $result = $this->queryBuilder->select(
            $this->table,
            [
                'COUNT(*) AS total'
            ],
        );

        return (int) $result[0]['total'];
    }
}
