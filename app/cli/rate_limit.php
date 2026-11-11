<?php

use App\Services\RateLimiterService;

$container = require __DIR__ . '/bootstrap.php';

$RateLimit = $container->get(RateLimiterService::class);
$RateLimit->deleteExpired();

echo "Expired login attempts removed.\n";