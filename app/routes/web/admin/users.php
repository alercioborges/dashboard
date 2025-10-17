<?php

use App\Controllers\UserController;

$group->group('/users', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [UserController::class, 'show'])->setName('users.show');
    $group->get('/create', [UserController::class, 'create'])->setName('users.create');
    $group->post('/create', [UserController::class, 'store'])->setName('users.store');
    $group->get('/{id:[0-9]+}/profile', [UserController::class, 'profile'])->setName('users.profile');    
    $group->get('/{id:[0-9]+}/edit', [UserController::class, 'edit'])->setName('users.edit');
    $group->post('/{id:[0-9]+}/edit', [UserController::class, 'update'])->setName('users.update');
});
