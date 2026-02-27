<?php

use App\Controllers\SetupController;

$app->get('/setup', [SetupController::class, 'index'])
    ->setName('setup.index');

$app->post('/setup', [SetupController::class, 'store'])
    ->setName('setup.store');