<?php

use App\Controllers\ForgotPasswordController;

$app->get('/forgot', [ForgotPasswordController::class, 'index'])->setName('forgot.index');
$app->post('/forgot', [ForgotPasswordController::class, 'login'])->setName('redefine ');