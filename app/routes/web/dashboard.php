<?php

use App\Controllers\DashboardController;

$app->get('', [DashboardController::class, 'index'])->setName('dashboard');
