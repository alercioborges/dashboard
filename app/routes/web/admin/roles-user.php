<?php

use App\Controllers\RoleController;

$group->group('/roles', function (Slim\Routing\RouteCollectorProxy $group) use ($permission) {

    $group->get('', [RoleController::class, 'show'])
    ->setName('roles-user.show')
    ->add($permission('users.view'));

    $group->get('/create', [RoleController::class, 'create'])
    ->setName('roles-user.create')
    ->add($permission('users.view'));

    $group->post('/create', [RoleController::class, 'store'])
    ->setName('roles-user.store')
    ->add($permission('users.view'));

    $group->get('/{id:[0-9]+}/edit', [RoleController::class, 'edit'])
    ->setName('roles-user.edit')
    ->add($permission('users.view'));

    $group->put('/{id:[0-9]+}', [RoleController::class, 'update'])
    ->setName('roles-user.update')
    ->add($permission('users.view'));

    $group->delete('/{id:[0-9]+}', [RoleController::class, 'destroy'])
    ->setName('roles-user.destroy')
    ->add($permission('users.view'));

});
