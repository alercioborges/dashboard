<?php

namespace App\Core;

use ClanCats\Hydrahon\Builder;

abstract class Model
{
    protected Builder $queryBuilder;

    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
