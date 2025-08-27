<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\UserRepositoryInterface;

class User extends Model implements UserRepositoryInterface
{

    private $table = 'tbl_users';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        return [];
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return [];
    }


    /**
     * Get all users with pagination
     */
    public function getAll(int $page = 1, int $perPage = 10): array
    {
        $all = $this->queryBuilder->select($this->table);
        return $all;
    }

    /**
     * Create new user
     */
    public function create(array $data): int
    {
        return true;
    }


    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        return true;
    }


    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        return true;
    }

}