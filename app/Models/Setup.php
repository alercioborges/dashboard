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
  }
}
