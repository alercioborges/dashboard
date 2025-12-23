<?php

use App\Controllers\RoleController;

$group->group('/roles', function (Slim\Routing\RouteCollectorProxy $group) {

    $group->get('', [RoleController::class, 'show'])->setName('roles-user.show');
    $group->get('/create', [RoleController::class, 'create'])->setName('roles-user.create');
    $group->post('/create', [RoleController::class, 'store'])->setName('roles-user.store');


    //$group->post('/create', [UserController::class, 'store'])->setName('users.store');
    //$group->get('/{id:[0-9]+}/profile', [UserController::class, 'profile'])->setName('users.profile');    
    //$group->get('/{id:[0-9]+}/edit', [UserController::class, 'edit'])->setName('users.edit');
    //$group->post('/{id:[0-9]+}/edit', [UserController::class, 'update'])->setName('users.update');


    //$group->put('/{id}', 'App\Controllers\UserController:update')->setName('users.update');    
    //$group->get('/edit/{id:[0-9]+}', [UserController::class, 'edit']);
    //$group->post('/edit/{id:[0-9]+}', [UserController::class, 'update']);
    //$group->get('/delete/{id:[0-9]+}', [UserController::class, 'delete']);
});
