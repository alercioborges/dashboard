<?php

use App\Controllers\AdministrationController;
use App\Middlewares\AuthMiddleware;

$app->group('/admin', function (Slim\Routing\RouteCollectorProxy $group) {

    $group->get('', [AdministrationController::class, 'index'])->setName('admin.index');

    // Users routs
    require 'users.php';

    // Users routs
    require 'roles-user.php';
})->add(AuthMiddleware::class);
