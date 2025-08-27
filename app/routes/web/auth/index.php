<?php

use App\Controllers\AuthController;

// Authentication routes
$app->get('/login', [AuthController::class, 'index'])->setName('login');
//$$app->post('/login', [AuthController::class, 'login']);
//$$app->post('/logout', [AuthController::class, 'logout'])->setName('logout');
