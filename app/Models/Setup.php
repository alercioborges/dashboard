<?php

namespace App\Models;

use App\Core\Model;
use App\Services\QueryBuilderService;

class Setup extends Model
{
  public function __construct(QueryBuilderService $queryBuilder)
  {
    parent::__construct($queryBuilder);
  }

  public function initialDataInsert()
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
