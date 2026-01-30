<?php

use App\Services\ForgotPasswordService;

$forgotPasswordService = $container->get(ForgotPasswordService::class);

// Executar limpeza
$forgotPasswordService->deleteToken();

echo "Tokens expirados removidos com sucesso.\n";