<?php

namespace App\Services;

use App\Interfaces\SetupRepositoryInterface;

class SetupService
{
    private SetupRepositoryInterface $setupRepository;

    public function __construct(SetupRepositoryInterface $setupRepository)
    {
        $this->setupRepository = $setupRepository;
    }

    public function setupInitialDataInsert(): void
    {
        $this->setupRepository->initialDataInsert();
    }
}
