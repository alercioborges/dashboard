<?php

namespace App\Services;

use Doctrine\DBAL\Connection as DBALConn;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\ArrayParameterType;
use Exception;

class QueryBuilderService
{
    private DBALConn $connection;

    public function __construct(DBALConn $connection)
    {
        $this->connection = $connection;
    }

    /**
     * SELECT básico
     */
    public function select(
        string $table,
        array $columns = ['*'],
        array $conditions = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$columns)->from($table);

            foreach ($conditions as $column => $value) {
                // Verifica se tem operador na chave (ex: "id <>", "email LIKE")
                if (preg_match('/\s(=|<>|>|<|>=|<=|LIKE)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column); // transforma "id <>" em "id__"
                    $qb->andWhere($column . ' :' . $param)
                        ->setParameter($param, $value);
                } elseif (is_array($value)) {
                    $param = preg_replace('/\W/', '_', $column);
                    $qb->andWhere($qb->expr()->in($column, ':' . $param))
                        ->setParameter($param, $value, ArrayParameterType::STRING);
                } else {
                    $param = str_replace('.', '_', $column);
                    $qb->andWhere($column . ' = :' . $param)
                        ->setParameter($param, $value);
                }
            }

            foreach ($orderBy as $column => $direction) {
                $qb->addOrderBy($column, $direction);
            }

            if ($limit !== null) {
                $qb->setMaxResults($limit);
            }
            if ($offset !== null) {
                $qb->setFirstResult($offset);
            }

            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro no SELECT: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * SELECT with JOIN
     */
    public function selectWithJoin(
        string $mainTable,
        array $joins = [],
        array $columns = ['*'],
        array $conditions = [],
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$columns)->from($mainTable, 'm');

            foreach ($joins as $alias => [$type, $condition]) {
                [$table, $joinAlias] = explode(' ', $alias);
                $type = strtoupper($type);
                $method = $type === 'INNER' ? 'innerJoin' : 'leftJoin';
                $qb->$method('m', $table, $joinAlias, $condition);
            }

            foreach ($conditions as $column => $value) {
                $param = str_replace('.', '_', $column);
                $qb->andWhere($column . ' = :' . $param)
                    ->setParameter($param, $value);
            }

            if ($limit !== null) {
                $qb->setMaxResults($limit);
            }
            if ($offset !== null) {
                $qb->setFirstResult($offset);
            }

            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro no SELECT com JOIN: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * INSERT
     */
    public function insert(string $table, array $data): int
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($table);

            foreach ($data as $col => $val) {
                $qb->setValue($col, ':' . $col)->setParameter($col, $val);
            }

            $qb->executeStatement();
            return (int) $this->connection->lastInsertId();
        } catch (DBALException $e) {
            throw new Exception("Error at INSERT: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * UPDATE
     */
    public function update(string $table, array $data, array $conditions): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("UPDATE sem WHERE não permitido.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->update($table);

            foreach ($data as $col => $val) {
                $qb->set($col, ':set_' . $col)
                    ->setParameter('set_' . $col, $val);
            }

            foreach ($conditions as $col => $val) {
                $qb->andWhere($col . ' = :where_' . $col)
                    ->setParameter('where_' . $col, $val);
            }

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("Erro no UPDATE: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * DELETE
     */
    public function delete(string $table, array $conditions): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("DELETE sem WHERE não permitido.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($table);

            foreach ($conditions as $col => $val) {
                $qb->andWhere($col . ' = :' . $col)
                    ->setParameter($col, $val);
            }

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("Erro no DELETE: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Query customizada
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            return $stmt->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro na query customizada: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Transações
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }
    public function commit(): void
    {
        $this->connection->commit();
    }
    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    /**
     * Helpers
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
    public function getConnection(): DBALConn
    {
        return $this->connection;
    }
}
