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
     * Basic SELECT
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

            // Montagem de condições (WHERE)
            foreach ($conditions as $column => $value) {

                // IN / NOT IN
                if (is_array($value) && preg_match('/\s(IN|NOT IN)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column);
                    $qb->andWhere($column . ' (:' . $param . ')')
                        ->setParameter($param, $value, ArrayParameterType::INTEGER);
                    continue;
                }

                // Operadores simples
                if (preg_match('/\s(=|<>|>|<|>=|<=|LIKE)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column);
                    $qb->andWhere($column . ' :' . $param)
                        ->setParameter($param, $value);
                    continue;
                }

                // Igualdade padrão
                $param = str_replace('.', '_', $column);
                $qb->andWhere($column . ' = :' . $param)
                    ->setParameter($param, $value);
            }


            // Ordenação (ORDER BY)
            foreach ($orderBy as $column => $direction) {
                $qb->addOrderBy($column, $direction);
            }

            // Paginação (LIMIT / OFFSET)
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
        array $orderBy = [],          // 🔹 Novo parâmetro opcional
        ?int $limit = null,
        ?int $offset = null
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$columns)->from($mainTable, 'm');

            // 🔸 Monta os JOINs
            foreach ($joins as $alias => [$type, $condition]) {
                [$table, $joinAlias] = explode(' ', $alias);
                $type = strtoupper($type);
                $method = $type === 'INNER' ? 'innerJoin' : 'leftJoin';
                $qb->$method('m', $table, $joinAlias, $condition);
            }

            // 🔸 Monta as condições (WHERE)
            foreach ($conditions as $column => $value) {
                // Suporte a operadores (LIKE, >=, <=, etc.)
                if (preg_match('/\s(=|<>|>|<|>=|<=|LIKE)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column);
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

            // 🔹 Adiciona a cláusula ORDER BY (caso exista)
            foreach ($orderBy as $column => $direction) {
                $qb->addOrderBy($column, strtoupper($direction));
            }

            // 🔹 Paginação (LIMIT / OFFSET)
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
    public function delete(string $table, array $conditions): bool
    {
        try {

            if (empty($conditions)) {
                throw new Exception("DELETE sem WHERE não permitido.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($table);

            foreach ($conditions as $column => $value) {

                if ($value === 'IS NULL') {
                    $qb->andWhere($column . ' IS NULL');
                    continue;
                }

                if ($value === 'NOT NULL') {
                    $qb->andWhere($column . ' IS NOT NULL');
                    continue;
                }

                // IN / NOT IN
                if (is_array($value) && preg_match('/\s(IN|NOT IN)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column);
                    $qb->andWhere($column . ' (:' . $param . ')')
                        ->setParameter($param, $value, ArrayParameterType::INTEGER);
                    continue;
                }

                // Operadores (=, <>, >, <, >=, <=, LIKE)
                if (preg_match('/\s(=|<>|>|<|>=|<=|LIKE)$/i', $column)) {
                    $param = preg_replace('/\W/', '_', $column);
                    $qb->andWhere($column . ' :' . $param)
                        ->setParameter($param, $value);
                    continue;
                }

                // Igualdade padrão
                $param = str_replace('.', '_', $column);
                $qb->andWhere($column . ' = :' . $param)
                    ->setParameter($param, $value);
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
