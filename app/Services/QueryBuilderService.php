<?php

namespace App\Services;

use Doctrine\DBAL\Connection as DBALConn;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\ArrayParameterType;

use Exception;
use InvalidArgumentException;

use App\Services\RawExpression;

class QueryBuilderService
{
    private DBALConn $connection;

    /**
     * Comparison operators allowed at the end of a condition key.
     * This is NOT a column allowlist: operators are a finite, immutable set,
     * so validating them is safe and requires no maintenance.
     */
    private const ALLOWED_OPERATORS = ['=', '<>', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN'];

    /**
     * Strict identifier format: a simple name, optionally qualified by one alias.
     * Accepts "id", "m.id", "r.name". Rejects spaces, parentheses, quotes,
     * semicolons, comments and anything else that could carry an injection.
     */
    private const IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/';

    public function __construct(DBALConn $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Creates a trusted raw SQL expression (e.g. CONCAT, CASE, COUNT, aliases).
     *
     * Use this for expressions written by you in code. NEVER pass user input here,
     * as RawExpression bypasses all identifier validation and quoting.
     */
    public function raw(string $expression): RawExpression
    {
        return new RawExpression($expression);
    }

    /**
     * Creates a trusted raw condition fragment paired with a bound value.
     *
     */
    public function rawCondition(string $fragment, mixed $value): array
    {
        return [
            '__raw'    => true,
            'fragment' => trim($fragment),
            'value'    => $value,
        ];
    }

    // -----------------------------------------------------------------
    // Security helpers
    // -----------------------------------------------------------------

    /**
     * Validates a pure string identifier against the strict pattern and
     * quotes it with the driver-aware Doctrine quoter. Qualified names
     * ("m.id") are split and each part is quoted individually -> `m`.`id`.
     *
     * RawExpression instances are trusted and returned as-is.
     */
    private function quoteIdentifier(RawExpression|string $identifier): string
    {
        if ($identifier instanceof RawExpression) {
            return (string) $identifier;
        }

        $identifier = trim($identifier);

        if (!preg_match(self::IDENTIFIER_PATTERN, $identifier)) {
            // Walk the stack to find the first frame outside this class (the real caller).
            $caller = $this->findCaller();

            throw new InvalidArgumentException(
                "Invalid SQL identifier: '{$identifier}'. " .
                    "For expressions/functions/aliases use QueryBuilderService::raw(). " .
                    "Called from {$caller['file']}:{$caller['line']}."
            );
        }

        $parts = array_map(
            fn(string $part) => $this->connection->quoteIdentifier($part),
            explode('.', $identifier)
        );

        return implode('.', $parts);
    }

    /**
     * Identifier that optionally has a simple "AS alias".
     * Accepts "m.id", "r.name AS role". The base and the alias are both
     * validated as strict identifiers and quoted independently.
     * Rejects functions, subqueries or anything outside the identifier grammar.
     */
    private function quoteSelectIdentifier(RawExpression|string $column): string
    {
        if ($column instanceof RawExpression) {
            return (string) $column; // trusted
        }

        $column = trim($column);

        if ($column === '*') {
            return '*';
        }

        // Split on a single, case-insensitive " AS " (surrounded by spaces).
        if (preg_match('/^(.+?)\s+AS\s+([a-zA-Z_][a-zA-Z0-9_]*)$/i', $column, $m)) {
            $base  = $this->quoteIdentifier($m[1]); // validates "r.name"
            $alias = $this->connection->quoteIdentifier($m[2]);
            return $base . ' AS ' . $alias;
        }

        // No alias: fall back to the strict identifier rule.
        return $this->quoteIdentifier($column);
    }

    /**
     * Finds the first stack frame outside this file/class,
     * i.e. the code that actually passed the invalid identifier.
     */
    private function findCaller(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $selfFile = __FILE__;

        foreach ($trace as $frame) {
            $file = $frame['file'] ?? null;

            // Skip any frame that lives inside this same file
            // (methods AND closures like the array_map callback).
            if ($file !== null && $file !== $selfFile) {
                return [
                    'file' => $file,
                    'line' => $frame['line'] ?? 0,
                ];
            }
        }

        return ['file' => 'unknown', 'line' => 0];
    }


    /**
     * Validates a table name (single identifier, no alias) and quotes it.
     */
    private function quoteTable(string $table): string
    {
        $table = trim($table);

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new InvalidArgumentException("Invalid table name: '{$table}'");
        }

        return $this->connection->quoteIdentifier($table);
    }

    /**
     * Splits a condition key into [identifier, operator].
     * "expires_at >" -> ["expires_at", ">"]; "email" -> ["email", "="].
     * The identifier is validated/quoted; the operator is checked against
     * the fixed ALLOWED_OPERATORS set.
     */
    private function parseCondition(string $key): array
    {
        $key = trim($key);
        $operatorPattern = implode('|', array_map(
            fn($op) => preg_quote($op, '/'),
            self::ALLOWED_OPERATORS
        ));

        if (preg_match('/^(.+?)\s+(' . $operatorPattern . ')$/i', $key, $m)) {
            return [$this->quoteIdentifier($m[1]), strtoupper($m[2])];
        }

        return [$this->quoteIdentifier($key), '='];
    }

    /**
     * Builds a unique, safe parameter placeholder name from a raw key.
     */
    private function paramName(string $key, string $prefix = ''): string
    {
        return $prefix . preg_replace('/\W/', '_', $key) . '_' . substr(md5($key), 0, 6);
    }

    /**
     * Applies WHERE conditions safely for any QueryBuilder.
     * Handles IS NULL / IS NOT NULL, IN / NOT IN and scalar operators.
     */
    private function applyConditions(QueryBuilder $qb, array $conditions, string $paramPrefix = ''): void
    {
        foreach ($conditions as $rawKey => $value) {

            // 0. rawCondition(): trusted fragment + bound value.
            if (is_array($value) && ($value['__raw'] ?? false) === true) {
                $param = $this->paramName($rawKey, $paramPrefix . 'raw_');
                $qb->andWhere($value['fragment'] . ' :' . $param)
                    ->setParameter($param, $value['value']);
                continue;
            }

            // NULL handling via sentinel string values.
            if (is_string($value) && strtoupper($value) === 'IS NULL') {
                [$column] = $this->parseCondition($rawKey);
                $qb->andWhere($column . ' IS NULL');
                continue;
            }
            if (is_string($value) && strtoupper($value) === 'NOT NULL') {
                [$column] = $this->parseCondition($rawKey);
                $qb->andWhere($column . ' IS NOT NULL');
                continue;
            }

            [$column, $operator] = $this->parseCondition($rawKey);
            $param = $this->paramName($rawKey, $paramPrefix);

            // IN / NOT IN with array value.
            if (is_array($value)) {
                $op = in_array($operator, ['IN', 'NOT IN'], true) ? $operator : 'IN';
                $qb->andWhere($column . ' ' . $op . ' (:' . $param . ')')
                    ->setParameter($param, $value, ArrayParameterType::STRING);
                continue;
            }

            // Scalar comparison (=, <>, >, <, >=, <=, LIKE...).
            $qb->andWhere($column . ' ' . $operator . ' :' . $param)
                ->setParameter($param, $value);
        }
    }

    /**
     * Validates/quotes each SELECT column. RawExpression passes untouched.
     */
    private function prepareColumns(array $columns): array
    {
        return array_map(
            fn($column) => $this->quoteSelectIdentifier($column),
            $columns
        );
    }

    /**
     * Applies ORDER BY, validating the column and restricting direction to ASC/DESC.
     */
    private function applyOrderBy(QueryBuilder $qb, array $orderBy): void
    {
        foreach ($orderBy as $column => $direction) {
            $safeColumn = $this->quoteIdentifier($column);
            $safeDir = strtoupper((string) $direction) === 'DESC' ? 'DESC' : 'ASC';
            $qb->addOrderBy($safeColumn, $safeDir);
        }
    }

    // -----------------------------------------------------------------
    // Public query methods
    // -----------------------------------------------------------------

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
            $qb->select(...$this->prepareColumns($columns))
                ->from($this->quoteTable($table));

            $this->applyConditions($qb, $conditions);
            $this->applyOrderBy($qb, $orderBy);

            if ($limit !== null) {
                $qb->setMaxResults($limit);
            }
            if ($offset !== null) {
                $qb->setFirstResult($offset);
            }

            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("SELECT error: " . $e->getMessage(), 0, $e);
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
        ?int $offset = null
    ): array {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select(...$this->prepareColumns($columns))
                ->from($this->quoteTable($mainTable), 'm');

            // Build the JOINs. The ON condition is trusted (hardcoded by dev),
            // but table/alias are validated.
            foreach ($joins as $alias => [$type, $condition]) {
                [$table, $joinAlias] = explode(' ', trim($alias));
                $this->quoteTable($table);   // validate table name
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $joinAlias)) {
                    throw new InvalidArgumentException("Invalid join alias: '{$joinAlias}'");
                }
                $method = strtoupper($type) === 'INNER' ? 'innerJoin' : 'leftJoin';
                $qb->$method('m', $table, $joinAlias, $condition);
            }

            $this->applyConditions($qb, $conditions);
            $this->applyOrderBy($qb, $orderBy);

            if ($limit !== null) {
                $qb->setMaxResults($limit);
            }
            if ($offset !== null) {
                $qb->setFirstResult($offset);
            }

            return $qb->executeQuery()->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new Exception("SELECT with JOIN error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * INSERT
     */
    public function insert(string $table, array $data): int
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->quoteTable($table));

            foreach ($data as $col => $val) {
                $safeCol = $this->quoteIdentifier($col);
                $param = $this->paramName($col);
                $qb->setValue($safeCol, ':' . $param)->setParameter($param, $val);
            }

            $qb->executeStatement();
            return (int) $this->connection->lastInsertId();
        } catch (DBALException $e) {
            throw new Exception("INSERT error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * UPDATE
     */
    public function update(string $table, array $data, array $conditions): int
    {
        try {
            if (empty($conditions)) {
                throw new Exception("UPDATE without WHERE is not allowed.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->update($this->quoteTable($table));

            foreach ($data as $col => $val) {
                $safeCol = $this->quoteIdentifier($col);
                $param = $this->paramName($col, 'set_');
                $qb->set($safeCol, ':' . $param)->setParameter($param, $val);
            }

            $this->applyConditions($qb, $conditions, 'where_');

            return $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("UPDATE error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * DELETE
     */
    public function delete(string $table, array $conditions): bool
    {
        try {
            if (empty($conditions)) {
                throw new Exception("DELETE without WHERE is not allowed.");
            }

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($this->quoteTable($table));

            $this->applyConditions($qb, $conditions);

            return (bool) $qb->executeStatement();
        } catch (DBALException $e) {
            throw new Exception("DELETE error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Custom query (developer is fully responsible for the SQL string).
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
            throw new Exception("Custom query error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Transactions
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
