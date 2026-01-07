<?php

namespace App\Interfaces;

use DateTimeInterface;

interface RememberMeRepositoryInterface
{
    public function store(int $userId, string $hash, DateTimeInterface $expiresAt);
    public function findValidUserByToken(string $hash): ?array;
    public function delete(string $hash): bool;
}
