<?php

namespace App\Interfaces;

interface ForgotPasswordServiceInterface
{
    public function process(string $email): void;

    public function validateToken(int $forgotId, string $token): ?array;

     public function resetPassword(int $forgotId, int $userId, string $password): bool;
}