<?php

use App\Services\RememberMeService;

$container = require __DIR__ . '/bootstrap.php';

$rememberMeService = $container->get(RememberMeService::class);
$rememberMeService->deleteRememberMe();

echo "Expired tokens successfully removed.\n";