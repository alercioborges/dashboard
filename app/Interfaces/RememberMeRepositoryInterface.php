<?php

namespace App\Interfaces;

interface RememberMeRepositoryInterface
{
    public function store(int $userId, string $hash, DateTimeInterface $expiresAt): ?array;

    public function findValidUserByToken(string $hash): ?array;

    public function delete(string $hash): bool;
}
