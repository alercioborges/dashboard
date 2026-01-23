<?php

use App\Controllers\ForgotPasswordController;

$app->get('/forgot', [ForgotPasswordController::class, 'index'])->setName('forgot.index');
$app->post('/forgot', [ForgotPasswordController::class, 'redefine'])->setName('forgot.redefine');

$app->get('/forgot/reset-password', [ForgotPasswordController::class, 'reset'])->setName('reset.index');
$app->post('/forgot/reset-password', [ForgotPasswordController::class, 'store'])->setName('reset.store');
