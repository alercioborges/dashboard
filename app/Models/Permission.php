<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;
use App\Interfaces\PermissionRepositoryInterface;

class Permission extends Model implements PermissionRepositoryInterface
{
    protected string $table = 'tbl_permissions';

    public function __construct(QueryBuilderService $queryBuilder)
    {
        parent::__construct($queryBuilder);
    }

    public function getPermissionsByRoleId(int $roleId): array
    {
        return $this->queryBuilder->selectWithJoin(
            'tbl_role_permissions rp',
            [
                'tbl_permissions p' => ['INNER', 'p.id = rp.permission_id']
            ],
            ['p.slug'],
            ['rp.role_id' => $roleId]
        );
    }
}
