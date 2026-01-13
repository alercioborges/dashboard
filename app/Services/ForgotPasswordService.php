<?php

namespace App\Services;

use App\Interfaces\ForgotPasswordServiceInterface;
use App\Interfaces\UserRepositoryInterface;

class ForgotPasswordService implements ForgotPasswordServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
