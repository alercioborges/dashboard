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
     * Método SELECT - Busca registros
     * 
     * @param string $table Nome da tabela
     * @param array $columns Colunas a serem selecionadas (default: ['*'])
     * @param array $conditions Condições WHERE [coluna => valor]
     * @param array $orderBy Ordenação ['coluna' => 'ASC|DESC']
     * @param int|null $limit Limite de registros
     * @param int|null $offset Offset para paginação
     * @return array
     * @throws Exception
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
            
            // SELECT
            $qb->select(...$columns)->from($table);
            
            // WHERE conditions
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    // Para condições IN
                    $qb->andWhere($qb->expr()->in($column, ':' . $column))
                        ->setParameter($column, $value, ArrayParameterType::STRING);
                } else {
                    $qb->andWhere($column . ' = :' . $column)
                        ->setParameter($column, $value);
                }
            }

            // ORDER BY
            foreach ($orderBy as $column => $direction) {
                $qb->addOrderBy($column, $direction);
            }

            // LIMIT
            if ($limit !== null) {
                $qb->setMaxResults($limit);
            }

            // OFFSET
            if ($offset !== null) {
                $qb->setFirstResult($offset);
            }
            
            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro no SELECT: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método SELECT com JOIN
     * 
     * @param string $mainTable Tabela principal
     * @param array $joins Array de joins ['table' => 'condition', ...]
     * @param array $columns Colunas a serem selecionadas
     * @param array $conditions Condições WHERE
     * @return array
     * @throws Exception
     */
    public function selectWithJoin(
        string $mainTable,
        array $joins = [],
        array $columns = ['*'],
        array $conditions = []
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();

            $qb->select(...$columns)->from($mainTable, 'm');

            // Add JOINs
            foreach ($joins as $table => $condition) {
                $alias = substr($table, 0, 1); // Primeira letra como alias
                $qb->leftJoin('m', $table, $alias, $condition);
            }

            // WHERE conditions
            foreach ($conditions as $column => $value) {
                $qb->andWhere($column . ' = :' . str_replace('.', '_', $column))
                    ->setParameter(str_replace('.', '_', $column), $value);
            }

            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro no SELECT com JOIN: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método INSERT - Insere um novo registro
     * 
     * @param string $table Nome da tabela
     * @param array $data Dados a serem inseridos [coluna => valor]
     * @return int ID do registro inserido
     * @throws Exception
     */
    public function insert(string $table, array $data): int
    {
        try {
            $qb = $this->connection->createQueryBuilder();

            $qb->insert($table);

            foreach ($data as $column => $value) {
                $qb->setValue($column, ':' . $column)
                    ->setParameter($column, $value);
            }

            $qb->executeStatement();

            return (int) $this->connection->lastInsertId();
        } catch (DBALException $e) {
            throw new Exception("Erro no INSERT: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método INSERT múltiplo - Insere vários registros de uma vez
     * 
     * @param string $table Nome da tabela
     * @param array $dataArray Array de arrays com os dados
     * @return int Número de registros inseridos
     * @throws Exception
     */
    public function insertMultiple(string $table, array $dataArray): int
    {
        try {
            $this->connection->beginTransaction();

            $insertedCount = 0;

            foreach ($dataArray as $data) {
                $qb = $this->connection->createQueryBuilder();
                $qb->insert($table);

                foreach ($data as $column => $value) {
                    $qb->setValue($column, ':' . $column)
                        ->setParameter($column, $value);
                }

                $qb->executeStatement();
                $insertedCount++;
            }

            $this->connection->commit();

            return $insertedCount;
        } catch (DBALException $e) {
            $this->connection->rollBack();
            throw new Exception("Erro no INSERT múltiplo: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método UPDATE - Atualiza registros existentes
     * 
     * @param string $table Nome da tabela
     * @param array $data Dados a serem atualizados [coluna => valor]
     * @param array $conditions Condições WHERE [coluna => valor]
     * @return int Número de registros afetados
     * @throws Exception
     */
    public function update(string $table, array $data, array $conditions): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("Condições WHERE são obrigatórias para UPDATE");
            }

            $qb = $this->connection->createQueryBuilder();

            $qb->update($table);

            // SET values
            foreach ($data as $column => $value) {
                $qb->set($column, ':set_' . $column)
                    ->setParameter('set_' . $column, $value);
            }

            // WHERE conditions
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $qb->andWhere($qb->expr()->in($column, ':where_' . $column))
                        ->setParameter('where_' . $column, $value, ArrayParameterType::STRING);
                } else {
                    $qb->andWhere($column . ' = :where_' . $column)
                        ->setParameter('where_' . $column, $value);
                }
            }

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("Erro no UPDATE: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método DELETE - Remove registros
     * 
     * @param string $table Nome da tabela
     * @param array $conditions Condições WHERE [coluna => valor]
     * @return int Número de registros deletados
     * @throws Exception
     */
    public function delete(string $table, array $conditions): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("Condições WHERE são obrigatórias para DELETE");
            }

            $qb = $this->connection->createQueryBuilder();

            $qb->delete($table);

            // WHERE conditions
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $qb->andWhere($qb->expr()->in($column, ':' . $column))
                        ->setParameter($column, $value, ArrayParameterType::STRING);
                } else {
                    $qb->andWhere($column . ' = :' . $column)
                        ->setParameter($column, $value);
                }
            }

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("Erro no DELETE: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método para encontrar um único registro
     * 
     * @param string $table Nome da tabela
     * @param array $conditions Condições WHERE [coluna => valor]
     * @param array $columns Colunas a serem selecionadas
     * @return array|null
     * @throws Exception
     */
    public function findOne(string $table, array $conditions, array $columns = ['*']): ?array
    {
        $results = $this->select($table, $columns, $conditions, [], 1);
        return $results[0] ?? null;
    }

    /**
     * Método para contar registros
     * 
     * @param string $table Nome da tabela
     * @param array $conditions Condições WHERE [coluna => valor]
     * @return int
     * @throws Exception
     */
    public function count(string $table, array $conditions = []): int
    {
        try {
            $qb = $this->connection->createQueryBuilder();

            $qb->select('COUNT(*)')->from($table);

            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $qb->andWhere($qb->expr()->in($column, ':' . $column))
                        ->setParameter($column, $value, ArrayParameterType::STRING);
                } else {
                    $qb->andWhere($column . ' = :' . $column)
                        ->setParameter($column, $value);
                }
            }

            return (int) $qb->executeQuery()->fetchOne();
        } catch (DBALException $e) {
            throw new Exception("Erro no COUNT: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Método para verificar se um registro existe
     * 
     * @param string $table Nome da tabela
     * @param array $conditions Condições WHERE [coluna => valor]
     * @return bool
     * @throws Exception
     */
    public function exists(string $table, array $conditions): bool
    {
        return $this->count($table, $conditions) > 0;
    }

    /**
     * Executa uma query SQL personalizada
     * 
     * @param string $sql Query SQL
     * @param array $params Parâmetros da query
     * @return array
     * @throws Exception
     */
    public function executeCustomQuery(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            return $stmt->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("Erro na query personalizada: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Obtém uma nova instância do QueryBuilder do Doctrine
     * 
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Obtém a conexão DBAL
     * 
     * @return DBALConn
     */
    public function getConnection(): DBALConn
    {
        return $this->connection;
    }

    /**
     * Inicia uma transação
     * 
     * @return void
     * @throws Exception
     */
    public function beginTransaction(): void
    {
        try {
            $this->connection->beginTransaction();
        } catch (DBALException $e) {
            throw new Exception("Erro ao iniciar transação: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Confirma uma transação
     * 
     * @return void
     * @throws Exception
     */
    public function commit(): void
    {
        try {
            $this->connection->commit();
        } catch (DBALException $e) {
            throw new Exception("Erro ao confirmar transação: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Desfaz uma transação
     * 
     * @return void
     * @throws Exception
     */
    public function rollback(): void
    {
        try {
            $this->connection->rollBack();
        } catch (DBALException $e) {
            throw new Exception("Erro ao desfazer transação: " . $e->getMessage(), 0, $e);
        }
    }
}
