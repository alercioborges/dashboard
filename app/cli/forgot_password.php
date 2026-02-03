<?php

use App\Services\ForgotPasswordService;

$container = require __DIR__ . '/../config/container.php';

$forgotPasswordService = $container->get(ForgotPasswordService::class);
$forgotPasswordService->deleteToken();

echo "Tokens expirados removidos com sucesso\n";
