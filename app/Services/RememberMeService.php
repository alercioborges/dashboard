<?php

namespace App\Services;

use App\Models\RememberMe;

class RememberMeService
{
    private RememberMe $rememberToken;

    public function __construct(RememberMe $rememberToken)
    {
        $this->rememberToken = $rememberToken;
    }

    public function deleteRememberMe()
    {        
        $this->rememberToken->deleteExpiredToken();
    }
}
