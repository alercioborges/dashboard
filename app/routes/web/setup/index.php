<?php



$app->get('/setup', [SetupController::class, 'index']);
$app->post('/setup', [SetupController::class, 'store']);