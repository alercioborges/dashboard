<?php

namespace App\Services;

use PDO;
use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\FetchableInterface;
use ClanCats\Hydrahon\Query\Sql\Insert;

class QueryBuilderService
{
    private PDO $connection;
    private Builder $queryBuilder;

    public function __construct(PDO $connection, Builder $queryBuilder)
    {
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Execute a query and return appropriate result
     */
    public function execute($query, string $queryString, array $queryParameters)
    {
        try {
            $statement = $this->connection->prepare($queryString);
            $statement->execute($queryParameters);

            if ($query instanceof FetchableInterface) {
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($query instanceof Insert) {
                return $this->connection->lastInsertId();
            } else {
                return $statement->rowCount();
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Create a select query
     */
    public function select(mixed $columnsOrTable = ['*'], ?string $table = null): \ClanCats\Hydrahon\Query\Sql\Select
    {
        if (func_num_args() === 1 && is_string($columnsOrTable)) {
            $table = $columnsOrTable;
            $columns = ['*'];
        } elseif (func_num_args() === 2 && is_array($columnsOrTable) && is_string($table)) {
            $columns = $columnsOrTable;
        } elseif (func_num_args() === 0) {
            $columns = ['*'];
            $table = null; // Table must be specified when using this service
        } else {
            throw new \InvalidArgumentException("Argumentos inválidos para o método");
        }

        if (!$table || !is_string($table)) {
            throw new \InvalidArgumentException("The table name must be string type");
        }

        return $this->queryBuilder->table($table)->select((array) $columns);
    }

    /**
     * Get the query builder instance
     */
    public function getBuilder(): Builder
    {
        return $this->queryBuilder;
    }
}