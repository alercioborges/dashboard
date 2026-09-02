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


    private function buildConditions(array $search): array
    {
        $conditions = ['m.is_active' => 1];

        if (!empty($search)) {

            $filterField = [];

            foreach ($search as $key => $value) {
                $filterField[$key] = trim($value ?? '');
            }

            if ($filterField['name'] !== '') {
                $conditions['name_search'] = $this->queryBuilder->rawCondition(
                    "CONCAT(m.firstname, ' ', m.lastname) LIKE",
                    '%' . $filterField['name'] . '%'
                );
            }

            if ($filterField['email'] !== '') {
                $conditions['m.email LIKE'] = '%' . $filterField['email'] . '%';
            }
        }

        return $conditions;
    }


    /**
     * Get all active users with pagination
     */
    public function getAll(int $limit = 10, int $offset = 0, array $search = []): array
    {
        return $this->queryBuilder->selectWithJoin(
            $this->table,
            ['tbl_roles r' => ['INNER', 'r.id = m.role_id']],
            [
                "m.id",
                $this->queryBuilder->raw("CONCAT(m.firstname, ' ', m.lastname) AS name"),
                "m.email",
                "r.name AS role"
            ],
            $this->buildConditions($search),
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
        $roles = $this->queryBuilder->select(
            'tbl_roles',
            ['id'],
            ['shortname' => 'readonly']
        );

        if (empty($roles)) {
            throw new \RuntimeException(
                "Could not create the user: the default 'readonly' role is not registered."
            );
        }

        return $this->queryBuilder->insert(
            $this->table,
            [
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'password'    => $this->passwordService->make($data['password']),
                'role_id'     => $roles[0]['id'],
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


    public function changeRole(int $userId, string $shortname_role): bool
    {
        $roles = $this->queryBuilder->select(
            'tbl_roles',
            ['id'],
            ['shortname' => $shortname_role]
        );

        if (empty($roles)) {
            return false;
        }

        $result = $this->queryBuilder->update(
            $this->table,
            ['role_id' => $roles[0]['id']],
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
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }
        
        $result = $this->queryBuilder->update(
            $this->table,
            [
                'email'     => $user['email'] . '-(del)',
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
        $name  = trim($search['name'] ?? '');
        $email = trim($search['email'] ?? '');

        if ($name === '' && $email === '') {
            return $this->countAll();
        }

        $result = $this->queryBuilder->selectWithJoin(
            $this->table,
            ['tbl_roles r' => ['INNER', 'r.id = m.role_id']],
            [$this->queryBuilder->raw('COUNT(*) AS total')],
            $this->buildConditions($search)
        );

        return (int) ($result[0]['total'] ?? 0);
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
            ['token_hash', 'user_id', 'used_at'],
            [
                'id'           => $forgotId,
                'expires_at >' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        );

        if (
            empty($resets)
            || !$this->passwordService->verify($token, $resets[0]['token_hash'])
            || $resets[0]['used_at'] !== NULL
        ) {
            return NULL;
        }

        return $resets[0];
    }


    public function updatePassword(int $forgotId, int $userId, string $password): bool
    {
        $this->queryBuilder->beginTransaction();

        try {
            $reset = $this->queryBuilder->update(
                'tbl_password_resets',
                ['used_at' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')],
                [
                    'id'      => $forgotId,
                    'user_id' => $userId,
                    'used_at' => 'IS NULL',
                ]
            );

            if ($reset === 0) {
                $this->queryBuilder->rollback();
                return false;
            }

            $this->queryBuilder->update(
                $this->table,
                ['password' => $this->passwordService->make($password)],
                ['id' => $userId]
            );

            $this->queryBuilder->commit();
            return true;
        } catch (\Throwable $e) {

            $this->queryBuilder->rollback();
            throw $e;
        }
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
