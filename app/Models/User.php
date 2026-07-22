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

        return $userData[0] ?? NULL;
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
                'firstname',
                'lastname',
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
     * Build base SELECT query and params for filtered user search.
     */
    private function getUserQuery(array $search): array
    {
        $sql = "SELECT m.id, CONCAT(m.firstname, ' ', m.lastname) AS name, m.email, r.name AS role
                FROM tbl_users m
                INNER JOIN tbl_roles r ON r.id = m.role_id
                WHERE m.is_active = 1";
        $params = [];

        $name = trim($search['name'] ?? '');
        if ($name !== '') {
            $sql .= " AND CONCAT(m.firstname, ' ', m.lastname) LIKE :search_name";
            $params[':search_name'] = '%' . $name . '%';
        }

        $email = trim($search['email'] ?? '');
        if ($email !== '') {
            $sql .= " AND m.email LIKE :search_email";
            $params[':search_email'] = '%' . $email . '%';
        }

        $sql .= " ORDER BY m.id ASC";

        return [$sql, $params];
    }

    /**
     * Get all active users with pagination
     */
    public function getAll(int $limit = 10, int $offset = 0, array $search = []): array
    {
        $name = trim($search['name'] ?? '');
        $email = trim($search['email'] ?? '');

        if ($name === '' && $email === '') {
            return $this->queryBuilder->selectWithJoin(
                $this->table,
                ['tbl_roles r' => ['INNER', 'r.id = m.role_id']],
                [
                    "m.id",
                    $this->queryBuilder->raw("CONCAT(m.firstname, ' ', m.lastname) AS name"),
                    "m.email",
                    "r.name AS role"
                ],
                ['m.is_active' => 1],
                ['m.id' => 'ASC'],
                $limit,
                $offset
            );
        }

        [$sql, $params] = $this->getUserQuery($search);
        $sql .= sprintf(" LIMIT %d OFFSET %d", $limit, $offset);

        return $this->queryBuilder->query($sql, $params);
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
                'email'       => $data['email']
            ],
            ['id' => $id]
        );

        return $result > 0;
    }


    public function changeRole(int $userId, int $roleId):bool
    {
        $result = $this->queryBuilder->update(
            $this->table,
            [
                'role_id' => $roleId
            ],
            ['id' => $userId]
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
                $this->queryBuilder->raw('COUNT(*) AS total')
            ],
            ['is_active' => 1]
        );

        return (int) $result[0]['total'];
    }

    /**
     * Get number of active users matching search filter
     */
    public function countFiltered(array $search = []): int
    {
        $name = trim($search['name'] ?? '');
        $email = trim($search['email'] ?? '');

        if ($name === '' && $email === '') {
            return $this->countAll();
        }

        [$sql, $params] = $this->getUserQuery($search);
        $sql = "SELECT COUNT(*) AS total FROM ($sql) AS filtered";

        $result = $this->queryBuilder->query($sql, $params);
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


    public function findValidPasswordReset(int $forgotId, string $token): ?array
    {
        $resets = $this->queryBuilder->select(
            'tbl_password_resets',
            ['token_hash', 'user_id'],
            [
                'id'           => $forgotId,
                'expires_at >' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        );

        $reset = $resets[0] ?? null;

        if ($reset && $this->passwordService->verify($token, $reset['token_hash'])) {
            return $reset;
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
