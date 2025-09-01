<?php

namespace App\Interfaces\Validators;

interface UserValidatorInterface
{
    public function required($field);

    public function email($field);

    public function max($field, $max);

    public function min($field, $max);

    public function unique($field, $model);

    public function uppercase($field);
    
}
