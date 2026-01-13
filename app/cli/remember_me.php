<?php

use App\Services\RememberMeService;

$container = require __DIR__ . '/../config/container.php';

$rememberMeService = $container->get(RememberMeService::class);

// Executar limpeza
$rememberMeService->deleteRememberMe();

echo "Tokens expirados removidos com sucesso.\n";