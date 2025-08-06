<?php

use App\controllers\DashboardController;

$app->get('', [DashboardController::class, 'index']);