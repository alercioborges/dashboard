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
        $permissionData = $this->queryBuilder->selectWithJoin(
            'tbl_role_permissions',
            [
                'tbl_permissions p' => ['INNER', 'p.id = m.permission_id']
            ],
            ['p.slug'],
            ['m.role_id' => $roleId]
        );
        
        return array_column($permissionData, 'slug');
    }
}
