<?php

use App\Controllers\AuthController;

// Authentication routes
$app->get('/login', [AuthController::class, 'index'])->setName('login.index');
$app->post('/login', [AuthController::class, 'login'])->setName('login');
$app->post('/logout', [AuthController::class, 'logout'])->setName('logout');