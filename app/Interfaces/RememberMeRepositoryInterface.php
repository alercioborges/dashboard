<?php

namespace App\Interfaces;

interface RememberMeRepositoryInterface
{
    public function create(int $userId): void;

    public function autoLogin(): ?int;

    public function forget(): void;
}
