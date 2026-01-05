<?php

namespace App\Models;

class RememberToken
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function store(
        int $userId,
        string $hash,
        DateTimeInterface $expiresAt
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_remember_tokens (user_id, token_hash, expires_at)
            VALUES (:user_id, :hash, :expires_at)
        ");

        $stmt->execute([
            'user_id'    => $userId,
            'hash'       => $hash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findValidUserByToken(string $hash): ?int
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

    public function delete(string $hash): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM user_remember_tokens WHERE token_hash = :hash
        ");
        $stmt->execute(['hash' => $hash]);
    }
}
