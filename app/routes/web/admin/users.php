<?php

use App\Controllers\UserController;

$group->group('/users', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [UserController::class, 'show']);
    //$group->get('/create', [UserController::class, 'create']);
    //$group->post('/create', [UserController::class, 'store']);
    //$group->get('/edit/{id:[0-9]+}', [UserController::class, 'edit']);
    //$group->post('/edit/{id:[0-9]+}', [UserController::class, 'update']);
    //$group->get('/delete/{id:[0-9]+}', [UserController::class, 'delete']);
});
