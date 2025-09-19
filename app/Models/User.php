<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\UserRepositoryInterface;

class User extends Model implements UserRepositoryInterface
{

    protected string $table = 'tbl_users';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        return [];
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->queryBuilder->select(
            $this->table,
            ['email'],
            ['email' => $email]
        ) ?? [];
    }


    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 10): array
    {
        return $this->queryBuilder->selectWithJoin(
            $this->table,
            [
                'tbl_roles r' => ['INNER', 'r.id = m.role_id']
            ],
            [
                "m.id",
                "CONCAT(m.firstname, ' ', m.lastname) AS name",
                "m.email",
                "r.name AS role"
            ]
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
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'password'    => $data['password'],
                'role_id'     => $data['role_id'],
                'is_active'   => 1,
                'last_login'  => NULL,
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
        return true;
    }


    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        return true;
    }
}
