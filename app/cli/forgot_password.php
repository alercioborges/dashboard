<?php

use App\Services\ForgotPasswordService;

$container = require __DIR__ . '/bootstrap.php';

$forgotPasswordService = $container->get(ForgotPasswordService::class);
$forgotPasswordService->deleteToken();

echo "Expired tokens successfully removed\n";
