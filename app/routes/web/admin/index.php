<?php

use App\Controllers\UserController;

$app->group('/admin', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [UserController::class, 'index']);
    
    // Users routs
    require 'users.php';
});
