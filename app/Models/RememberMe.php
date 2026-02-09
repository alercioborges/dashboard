<?php

namespace App\Models;

use App\Core\Model;
use App\Interfaces\RememberMeRepositoryInterface;
use App\Services\QueryBuilderService;
use DateTimeInterface;

class RememberMe extends Model implements RememberMeRepositoryInterface
{
    protected string $table = 'tbl_user_remember_tokens';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    public function store(int $userId, string $hash, DateTimeInterface $expiresAt): int
    {
        return $this->queryBuilder->insert(
            $this->table,
            [
                'user_id'    => $userId,
                'token_hash' => $hash,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]
        );
    }

    public function findValidUserByToken(string $hash): ?array
    {
        $result = $this->queryBuilder->selectWithJoin(
            $this->table,
            [
                'tbl_users u' => ['INNER', 'u.id = m.user_id']
            ],
            [
                'u.id',
                'u.role_id'
            ],
            [
                'm.token_hash' => $hash,
                'm.expires_at >' => date('Y-m-d H:i:s')
            ],
            [],
            1
        );

        return $result[0] ?? null;
    }

    public function delete(string $hash): bool
    {
        return $this->queryBuilder->delete($this->table, [
            'token_hash' => $hash
        ]);
    }

    public function deleteExpiredToken(): bool
    {
        return $this->queryBuilder->delete(
            $this->table,
            [
                'expires_at <' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }

    public function deleteOnLogout(int $userId): bool
    {
        return $this->queryBuilder->delete(
            $this->table,
            [
                'user_id' => $userId
            ]
        );
    }
}
