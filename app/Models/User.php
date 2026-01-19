<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\UserRepositoryInterface;
use App\Services\PasswordService;

class User extends Model implements UserRepositoryInterface
{
    protected string $table = 'tbl_users';
    private PasswordService $passwordService;

    public function __construct(QueryBuilderService $queryBuilder, PasswordService $passwordService)
    {
        parent::__construct($queryBuilder);
        $this->passwordService = $passwordService;
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        $userData = $this->queryBuilder->selectWithJoin(
            $this->table,
            [
                'tbl_roles r' => ['INNER', 'r.id = m.role_id']
            ],
            [
                "m.id",
                "m.firstname",
                "m.lastname",
                "m.email",
                "r.name AS role",
                "m.created_at"
            ],
            ['m.id' => $id]
        );

        return $userData[0];
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->queryBuilder->select(
            $this->table,
            [
                'id',
                'email',
                'password',
                'is_active',
                'role_id'
            ],
            ['email' => $email]
        );
    }


    /**
     * Get all active users with pagination
     */
    public function getAll(int $limit = 10, int $offset = 0): array
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
            ],
            ['m.is_active' => 1],
            ['m.id' => 'ASC'],
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
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'password'    => $this->passwordService->make($data['password']),
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
        $result = $this->queryBuilder->update(
            $this->table,
            [
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'role_id'     => $data['role_id'],
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
        $email = $this->findById($id)['email'];

        $result = $this->queryBuilder->update(
            $this->table,
            [
                'email'     => $email . '-(del)',
                'is_active' => 0
            ],
            ['id' => $id]
        );

        return $result > 0;
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
            ['is_active' => 1]
        );

        return (int) $result[0]['total'];
    }

    /*
    public function storePasswordReset(inr id, )
    {
        $this->passwordService->make($data['password'])
    }
    */
}
