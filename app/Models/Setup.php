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
    
  }
}
