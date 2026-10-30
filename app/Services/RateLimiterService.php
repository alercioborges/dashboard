<?php

namespace App\Services;

use App\Services\QueryBuilderService;

class RateLimiterService
{
    private QueryBuilderService $queryBuilder;

    public function __construct(QueryBuilderService $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    private const TABLE = 'tbl_rate_limit_attempts';

    /**
     * Quantidade de tentativas dentro da janela de tempo.
     */
    public function attempts(string $identifier, string $action, int $decayMinutes): int
    {
        $since = (new \DateTime("-{$decayMinutes} minutes"))->format('Y-m-d H:i:s');

        $rows = $this->queryBuilder->select(
            self::TABLE,
            ['id'],
            [
                'identifier'     => $identifier,
                'action'         => $action,
                'created_at >' => $since
            ]
        );

        return count($rows);
    }

    /**
     * Já estourou o limite de tentativas na janela atual?
     */
    public function tooManyAttempts(string $identifier, string $action, int $maxAttempts, int $decayMinutes): bool
    {
        return $this->attempts($identifier, $action, $decayMinutes) >= $maxAttempts;
    }

    /**
     * Registra uma nova tentativa.
     */
    public function hit(string $identifier, string $action): void
    {
        $this->queryBuilder->insert(self::TABLE, [
            'identifier' => $identifier,
            'action'     => $action,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Segundos restantes até a tentativa mais antiga da janela expirar
     * (ou seja, até o usuário poder tentar de novo).
     */
    public function availableIn(string $identifier, string $action, int $decayMinutes): int
    {
        $since = (new \DateTime("-{$decayMinutes} minutes"))->format('Y-m-d H:i:s');

        $rows = $this->queryBuilder->select(
            self::TABLE,
            ['created_at'],
            [
                'identifier'     => $identifier,
                'action'         => $action,
                'created_at >' => $since
            ],
            ['created_at' => 'ASC'],
            1
        );

        if (empty($rows)) {
            return 0;
        }

        $oldest = new \DateTime($rows[0]['created_at']);
        $resetsAt = (clone $oldest)->modify("+{$decayMinutes} minutes");
        $diff = $resetsAt->getTimestamp() - time();

        return max($diff, 0);
    }

    /**
     * Limpa as tentativas (chamar após login bem-sucedido).
     */
    public function clear(string $identifier, string $action): void
    {
        $this->queryBuilder->delete(self::TABLE, [
            'identifier' => $identifier,
            'action'     => $action
        ]);
    }
}