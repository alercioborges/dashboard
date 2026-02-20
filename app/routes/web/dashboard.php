<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\MainUserMiddleware;

use App\Controllers\DashboardController;

$app->get('', [DashboardController::class, 'index'])->setName('dashboard')
->add(MainUserMiddleware::class)
->add(AuthMiddleware::class);
