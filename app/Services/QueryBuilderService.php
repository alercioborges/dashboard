<?php

namespace App\Services;

use Doctrine\DBAL\Connection as DBALConn;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\ArrayParameterType;
use Exception;
use InvalidArgumentException;

class QueryBuilderService
{
    private DBALConn $connection;

    /**
     * Operadores aceitos ao final de uma chave de condição, ex.: "idade >=", "nome LIKE"
     */
    private const ALLOWED_OPERATORS = ['=', '<>', '>', '<', '>=', '<=', 'LIKE', 'IN', 'NOT IN'];

    /**
     * Formato aceito para nome de coluna: letras, números, underscore,
     * opcionalmente qualificado com alias ("m.nome").
     */
    private const IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/';


    /**
     * Expressões de agregação aceitas na lista de colunas do SELECT,
     * ex.: "COUNT(*) AS total", "SUM(valor) AS soma", "MAX(m.criado_em)".
     */
    private const AGGREGATE_PATTERN =
    '/^(COUNT|SUM|AVG|MIN|MAX)\(\s*(\*|[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?)\s*\)(\s+AS\s+[a-zA-Z_][a-zA-Z0-9_]*)?$/i';


    public function __construct(DBALConn $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Basic SELECT
     *
     * @param array $allowedColumns Allowlist opcional. Se informado, qualquer coluna em
     *                              $columns, $conditions ou $orderBy fora dessa lista
     *                              lança exceção. Use sempre que $columns/$conditions/$orderBy
     *                              puderem conter algo vindo de input do usuário
     *                              (ex.: ordenação escolhida em uma querystring).
     */
    public function select(
        string $table,
        array $columns = ['*'],
        array $conditions = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null,
        array $allowedColumns = []
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$this->prepareSelectColumns($columns, $allowedColumns))->from($table);

            $this->applyConditions($qb, $conditions, $allowedColumns);
            $this->applyOrderBy($qb, $orderBy, $allowedColumns);

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
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null,
        array $allowedColumns = []
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$this->prepareSelectColumns($columns, $allowedColumns))->from($mainTable, 'm');

            foreach ($joins as $alias => [$type, $condition]) {
                [$table, $joinAlias] = explode(' ', $alias);
                $type = strtoupper($type);
                $method = $type === 'INNER' ? 'innerJoin' : 'leftJoin';
                $qb->$method('m', $table, $joinAlias, $condition);
            }

            $this->applyConditions($qb, $conditions, $allowedColumns);
            $this->applyOrderBy($qb, $orderBy, $allowedColumns);

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
    public function insert(string $table, array $data, array $allowedColumns = []): int
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($table);

            foreach ($data as $col => $val) {
                $col = $this->assertValidColumn($col, $allowedColumns);
                $qb->setValue($this->quote($col), ':' . $this->paramName($col))
                    ->setParameter($this->paramName($col), $val);
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
    public function update(string $table, array $data, array $conditions, array $allowedColumns = []): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("UPDATE sem WHERE não permitido.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->update($table);

            foreach ($data as $col => $val) {
                $col = $this->assertValidColumn($col, $allowedColumns);
                $qb->set($this->quote($col), ':set_' . $this->paramName($col))
                    ->setParameter('set_' . $this->paramName($col), $val);
            }

            $this->applyConditions($qb, $conditions, $allowedColumns, 'where_');

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("Erro no UPDATE: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * DELETE
     */
    public function delete(string $table, array $conditions, array $allowedColumns = []): bool
    {
        try {
            if (empty($conditions)) {
                throw new Exception("DELETE sem WHERE não permitido.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($table);

            $this->applyConditions($qb, $conditions, $allowedColumns);

            return (bool) $qb->executeStatement();
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
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
    public function getConnection(): DBALConn
    {
        return $this->connection;
    }

    // -----------------------------------------------------------------
    // Helpers de segurança (allowlist + parsing seguro de identificadores)
    // -----------------------------------------------------------------

    /**
     * Aplica as condições WHERE de forma segura: separa coluna/operador,
     * valida o nome da coluna contra o allowlist (se informado) e
     * quota o identificador antes de montar o SQL.
     */
    private function applyConditions(QueryBuilder $qb, array $conditions, array $allowedColumns, string $paramPrefix = ''): void
    {
        foreach ($conditions as $rawColumn => $value) {

            if ($value === 'IS NULL') {
                [$column] = $this->parseColumn($rawColumn, $allowedColumns);
                $qb->andWhere($this->quote($column) . ' IS NULL');
                continue;
            }

            if ($value === 'NOT NULL') {
                [$column] = $this->parseColumn($rawColumn, $allowedColumns);
                $qb->andWhere($this->quote($column) . ' IS NOT NULL');
                continue;
            }

            [$column, $operator] = $this->parseColumn($rawColumn, $allowedColumns);
            $param = $paramPrefix . $this->paramName($column) . '_' . substr(md5($rawColumn), 0, 6);

            if (is_array($value) && in_array($operator, ['IN', 'NOT IN'], true)) {
                $qb->andWhere($this->quote($column) . ' ' . $operator . ' (:' . $param . ')')
                    ->setParameter($param, $value, ArrayParameterType::STRING);
                continue;
            }

            $qb->andWhere($this->quote($column) . ' ' . $operator . ' :' . $param)
                ->setParameter($param, $value);
        }
    }

    /**
     * Aplica ORDER BY validando coluna e restringindo direção a ASC/DESC.
     */
    private function applyOrderBy(QueryBuilder $qb, array $orderBy, array $allowedColumns): void
    {
        foreach ($orderBy as $column => $direction) {
            $column = $this->assertValidColumn($column, $allowedColumns);
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $qb->addOrderBy($this->quote($column), $direction);
        }
    }


    /**
     * Valida colunas do SELECT. Aceita:
     *  - '*'                          (todas as colunas)
     *  - identificador simples         (validado e quotado)
     *  - expressão de agregação        (ex.: "COUNT(*) AS total", passada como está)
     */
    private function prepareSelectColumns(array $columns, array $allowedColumns): array
    {
        return array_map(function ($column) use ($allowedColumns) {

            if ($column === '*') {
                return $column;
            }

            if (preg_match(self::AGGREGATE_PATTERN, trim($column), $matches)) {
                // Se um allowlist foi passado, ainda valida a coluna interna (ex.: o "valor" de SUM(valor))
                if (!empty($allowedColumns) && $matches[2] !== '*') {
                    $this->assertValidColumn($matches[2], $allowedColumns);
                }
                return trim($column); // expressão já validada pelo formato, não precisa (e não deve) ser quotada
            }

            return $this->quote($this->assertValidColumn($column, $allowedColumns));
        }, $columns);
    }


    /**
     * Separa "coluna" de "coluna OPERADOR" (ex.: "idade >=" -> ['idade', '>=']).
     * Assume '=' quando nenhum operador é informado.
     */
    private function parseColumn(string $rawColumn, array $allowedColumns): array
    {
        $operatorPattern = implode('|', array_map(fn($op) => preg_quote($op, '/'), self::ALLOWED_OPERATORS));

        if (preg_match('/^(.+?)\s+(' . $operatorPattern . ')$/i', trim($rawColumn), $matches)) {
            $column = $this->assertValidColumn(trim($matches[1]), $allowedColumns);
            $operator = strtoupper($matches[2]);
            return [$column, $operator];
        }

        return [$this->assertValidColumn(trim($rawColumn), $allowedColumns), '='];
    }

    /**
     * Garante que o nome da coluna tem formato de identificador válido e,
     * se um allowlist foi passado, que a coluna está nele.
     */
    private function assertValidColumn(string $column, array $allowedColumns = []): string
    {
        if (!preg_match(self::IDENTIFIER_PATTERN, $column)) {
            throw new InvalidArgumentException("Nome de coluna inválido: '{$column}'");
        }

        if (!empty($allowedColumns) && !in_array($column, $allowedColumns, true)) {
            throw new InvalidArgumentException("Coluna '{$column}' não permitida nesta consulta.");
        }

        return $column;
    }


    /**
     * Quota o identificador (lida com "alias.coluna" automaticamente).
     */
    private function quote(string $column): string
    {
        return $this->connection->quoteIdentifier($column);
    }

    private function paramName(string $column): string
    {
        return preg_replace('/\W/', '_', $column);
    }
}
