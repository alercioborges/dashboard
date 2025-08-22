<?php

namespace App\Core;

use App\Services\QueryBuilderService;

abstract class Model
{
    protected QueryBuilderService $queryBuilder;

    public function __construct(QueryBuilderService $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
