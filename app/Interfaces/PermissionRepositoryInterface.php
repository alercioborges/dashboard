<?php

namespace App\Interfaces;

interface PermissionRepositoryInterface
{
    public function getPermissionsByRoleId(int $roleId): array;
}
