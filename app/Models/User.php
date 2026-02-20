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
        $user = $this->queryBuilder->select(
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

        return $user[0] ?? NULL;
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
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'password'    => $this->passwordService->make($data['password']),
                'role_id'     => 1,
                'is_active'   => 1
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
                'role_id'     => $data['role_id']
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


    public function storePasswordReset(
        int $userId,
        string $tokenHash,
        \DateTimeImmutable $expiresAt
    ): ?int {

        return $this->queryBuilder->insert(
            'tbl_password_resets',
            [
                'user_id'    => $userId,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s')
            ]
        );
    }


    public function findValidPasswordReset(string $token): ?array
    {
        $resets = $this->queryBuilder->select(
            'tbl_password_resets',
            ['token_hash', 'user_id'],
            [
                'expires_at >' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        );

        foreach ($resets as $reset) {
            if ($this->passwordService->verify($token, $reset['token_hash'])) {
                return $reset;
            }
        }

        return NULL;
    }
    

    public function updatePassword(int $forgotId, int $userId, string $password): bool
    {
        $result = $this->queryBuilder->update(
            $this->table,
            ['password' => $this->passwordService->make($password)],
            ['id' => $userId]
        );

        $this->queryBuilder->update(
            'tbl_password_resets',
            ['used_at' => (new \DateTime())->format('Y-m-d H:i:s')],
            ['id' => $forgotId]
        );

        return $result > 0;
    }
    
    
    public function deleteExpiredToken(): bool
    {
        return $this->queryBuilder->delete(
            'tbl_password_resets',
            [
                'expires_at <' => (new \DateTime())->format('Y-m-d H:i:s'),
                'used_at'      => 'IS NULL'
            ]
        );
    }
}
