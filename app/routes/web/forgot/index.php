<?php

use App\Controllers\ForgotPasswordController;
use App\Middlewares\RateLimitMiddleware;

$app->get('/forgot', [ForgotPasswordController::class, 'index'])->setName('forgot.index');

$app->post('/forgot', [ForgotPasswordController::class, 'redefine'])
    ->setName('forgot.redefine')
    ->add($container->get(RateLimitMiddleware::class)('forgot_password', 3, 30));

$app->get('/forgot/reset-password', [ForgotPasswordController::class, 'reset'])->setName('reset.index');

$app->post('/forgot/reset-password', [ForgotPasswordController::class, 'store'])
    ->setName('reset.store')
    ->add($container->get(RateLimitMiddleware::class)('reset_password', 5, 15));
