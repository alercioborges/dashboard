<?php

use App\Controllers\AuthController;

// Authentication routes
$app->get('/login', [AuthController::class, 'index'])->setName('login.index');
$app->post('/login', [AuthController::class, 'login'])->setName('login');
$app->post('/logout/{id:[0-9]+}', [AuthController::class, 'logout'])->setName('logout');
