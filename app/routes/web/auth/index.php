<?php

use App\Controllers\AuthController;
use App\Middlewares\RateLimitMiddleware;

// Authentication routes
$app->get('/login', [AuthController::class, 'index'])->setName('login.index');

$app->post('/login', [AuthController::class, 'login'])
->setName('login')
->add($container->get(RateLimitMiddleware::class)('login', 5, 15));


$app->post('/logout', [AuthController::class, 'logout'])->setName('logout');
