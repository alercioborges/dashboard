<?php

namespace App\Core;

use App\Services\QueryBuilderService;

abstract class Model
{
    protected QueryBuilderService $queryBuilder;
    protected string $table;

    public function __construct(QueryBuilderService $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Método genérico para buscar por qualquer campo
     * Pode ser sobrescrito nas classes filhas se necessário
     */
    public function findByField(string $field, $value): ?array
    {
        if (!isset($this->table)) {
            throw new \Exception("Propriedade 'table' não definida no modelo " . get_class($this));
        }

        $result = $this->queryBuilder->select(
            $this->table,
            ['*'],
            [$field => $value]
        );

        return !empty($result) ? $result[0] : null;
    }
}
