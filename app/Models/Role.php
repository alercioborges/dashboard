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
                'name',
                'shortname',
                'description',
                'created_at',
                'updated_at'
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
            [
                'id',
                'name',
                'shortname',
                'description',
                'created_at',
                'updated_at'
            ],
            ['name' => $name]
        );

        return $roleData[0] ?? NULL;
    }

    /**
     * Find user role by shortname
     */
    public function findByShortname(string $shortname): ?array
    {
        $roleData = $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'name',
                'shortname',
                'description',
                'created_at',
                'updated_at'
            ],
            ['shortname' => $shortname]
        );

        return $roleData[0] ?? NULL;
    }


    /**
     * Get all user roles that was created with pagination
     */
    public function getAllCreated(int $limit = 10, int $offset = 0): array
    {
        return $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'name',
                'description',
                'created_at'
            ],
            [
                'shortname NOT IN' => ['readonly', 'admin']
            ],
            [],
            $limit,
            $offset
        );
    }

    /**
     * Get all user roles with pagination
     */
    public function getAll(int $limit = 10, int $offset = 0): array
    {
        return $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'name',
                'description',
                'created_at'
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
                'name'        => $data['name'],
                'shortname'   => $data['shortname'],
                'description' => $data['description'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => NULL
            ]
        );
    }

    /**
     * Update user role
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->queryBuilder->update(
            $this->table,
            [
                'name'        => $data['name'],
                'shortname'   => $data['shortname'],
                'description' => $data['description'],
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'shortname NOT IN' => ['readonly', 'admin'],
                'id' => $id
            ]
        );

        return $result > 0;
    }

    /**
     * Get specific users data
     */
    public function findFieldExists(string $field, string $value, string $key, int $id): ?array
    {
        return $this->fieldExists($field, $value, $key, $id);
    }

    /**
     * Delete user role
     */
    public function delete(int $id): bool
    {
        $result = $this->queryBuilder->delete(
            $this->table,
            [
                'shortname NOT IN' => ['readonly', 'admin'],
                'id' => $id
            ]
        );

        return $result > 0;
    }

    /**
     * Get total number of user roles
     */
    public function countAll(): int
    {
        $result = $this->queryBuilder->select(
            $this->table,
            [
                $this->queryBuilder->raw('COUNT(*) AS total')
            ]
        );

        return (int) $result[0]['total'];
    }

    /**
     * Get total number of user roles thar was created
     */
    public function countAllCreated(): int
    {
        $result = $this->queryBuilder->select(
            $this->table,
            [
                $this->queryBuilder->raw('COUNT(*) AS total')
            ],
            [
                'shortname NOT IN' => ['readonly', 'admin']
            ]
        );

        return (int) $result[0]['total'];
    }
}
