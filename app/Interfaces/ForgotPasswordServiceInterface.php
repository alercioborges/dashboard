<?php

namespace App\Interfaces;

interface ForgotPasswordServiceInterface
{
    public function process(string $email): void;

    public function validateToken(string $token): ?array;
}