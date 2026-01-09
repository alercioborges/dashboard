<?php

use App\Middlewares\AuthMiddleware;

use App\Controllers\DashboardController;

$app->get('', [DashboardController::class, 'index'])->setName('dashboard')->add(AuthMiddleware::class);
