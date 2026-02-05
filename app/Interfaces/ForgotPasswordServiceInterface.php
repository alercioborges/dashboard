<?php

namespace App\Interfaces;

interface ForgotPasswordServiceInterface
{
    public function process(string $email): void;

    public function validateToken(string $token): ?array;

     public function resetPassword(int $forgotId, int $userId, string $password): bool;
}