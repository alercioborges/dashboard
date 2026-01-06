<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\RememberMeRepositoryInterface;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;

class RememberToken extends Model implements RememberMeRepositoryInterface
{
    protected string $table = 'tbl_user_remember_tokens';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    public function store(int $userId, string $hash, DateTimeInterface $expiresAt)
    {
        return $this->queryBuilder->insert(
            $this->table,
            [
                'id'         => NULL,
                'user_id'    => $userId,
                'token_hash' => $hash,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );
    }

    public function findValidUserByToken(string $hash): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id
            FROM user_remember_tokens
            WHERE token_hash = :hash
              AND expires_at > NOW()
            LIMIT 1
        ");

        $stmt->execute(['hash' => $hash]);
        $userId = $stmt->fetchColumn();

        return $userId ? (int) $userId : null;
    }

    public function delete(string $hash): 
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM user_remember_tokens WHERE token_hash = :hash
        ");
        $stmt->execute(['hash' => $hash]);
    }
}
