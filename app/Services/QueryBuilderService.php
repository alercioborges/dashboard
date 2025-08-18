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

    public function executeQuery($query)
    {
        list($queryString, $queryParameters) = $query->build();

        $statement = $this->connection->prepare($queryString);
        $statement->execute($queryParameters);

        if ($query instanceof FetchableInterface) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($query instanceof Insert) {
            return $this->connection->lastInsertId();
        } else {
            return $statement->rowCount();
        }
    }

    // Métodos de conveniência para diferentes tipos de queries
    public function select(string $table): \ClanCats\Hydrahon\Query\Sql\Select
    {
        return $this->queryBuilder->table($table)->select();
    }

    public function insert(string $table): Insert
    {
        return $this->queryBuilder->table($table)->insert();
    }

    public function update(string $table): \ClanCats\Hydrahon\Query\Sql\Update
    {
        return $this->queryBuilder->table($table)->update();
    }

    public function delete(string $table): \ClanCats\Hydrahon\Query\Sql\Delete
    {
        return $this->queryBuilder->table($table)->delete();
    }

    // Método para executar queries cruas se necessário
    public function raw(string $sql, array $parameters = []): array
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
