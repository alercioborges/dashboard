<?php

namespace App\Models;

use App\Interfaces\SetupRepositoryInterface;
use App\Services\QueryBuilderService;

class Setup implements SetupRepositoryInterface
{
  private QueryBuilderService $queryBuilder;

  public function __construct(QueryBuilderService $queryBuilder)
  {
    $this->queryBuilder = $queryBuilder;
  }

  public function initialDataInsert(): void
  {

    if (!$this->queryBuilder->exists('tbl_roles', ['shortname' => 'readonly'])) {

      $this->queryBuilder->insert(
        'tbl_roles',
        [
          'name'        => 'Somente Leitura',
          'shortname'   => 'readonly',
          'description' => 'Apenas vizualixação das informações.'
        ]
      );
    }

    if (!$this->queryBuilder->exists('tbl_roles', ['shortname' => 'admin'])) {

      $this->queryBuilder->insert(
        'tbl_roles',
        [
          'name'        => 'Administrador',
          'shortname'   => 'admin',
          'description' => 'Todos os acesso.'
        ]
      );
    }

    $permissions = [
      ['slug' => 'users.view',      'description' => 'Visualizar usuários'],
      ['slug' => 'users.create',    'description' => 'Criar usuários'],
      ['slug' => 'users.edit',      'description' => 'Editar usuários'],
      ['slug' => 'users.delete',    'description' => 'Excluir usuários'],
      ['slug' => 'role.view',       'description' => 'Visualizar papéis de usuários'],
      ['slug' => 'role.create',     'description' => 'Criar papéis de usuários'],
      ['slug' => 'role.edit',       'description' => 'Editar papéis de usuários'],
      ['slug' => 'role.delete',     'description' => 'Excluir papéis de usuários'],
      ['slug' => 'role.assignment', 'description' => 'Atribuição de papéis para usuários'],
      ['slug' => 'role.assign',     'description' => 'Atribuir papéi para usuário']
    ];

    foreach ($permissions as $permission) {
      if (!$this->queryBuilder->exists('tbl_permissions', ['slug' => $permission['slug']])) {
        $this->queryBuilder->insert('tbl_permissions', $permission);
      }
    }

    // role <-> permissions (admin get all)
    $role = $this->queryBuilder->select(
      'tbl_roles',
      ['id'],
      ['shortname' => 'admin'],
      [],
      1
    );

    if (!empty($role)) {
      $roleId = (int) $role[0]['id'];

      $rows = $this->queryBuilder->select(
        'tbl_permissions',
        ['id'],
        ['slug' => array_column($permissions, 'slug')] // make IN (...)
      );

      foreach ($rows as $row) {
        $permissionId = (int) $row['id'];

        $link = ['role_id' => $roleId, 'permission_id' => $permissionId];

        if (!$this->queryBuilder->exists('tbl_role_permissions', $link)) {
          $this->queryBuilder->insert('tbl_role_permissions', $link);
        }
      }
    }
  }
}
