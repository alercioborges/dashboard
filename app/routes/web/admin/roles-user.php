<?php

use App\Controllers\RoleController;

$group->group('/roles', function (Slim\Routing\RouteCollectorProxy $group) use ($permission) {

    $group->get('', [RoleController::class, 'show'])
    ->setName('roles-user.show')
    ->add($permission('role.view'));

    $group->get('/create', [RoleController::class, 'create'])
    ->setName('roles-user.create')
    ->add($permission('role.create'));

    $group->post('/create', [RoleController::class, 'store'])
    ->setName('roles-user.store')
    ->add($permission('role.create'));

    $group->get('/{id:[0-9]+}/edit', [RoleController::class, 'edit'])
    ->setName('roles-user.edit')
    ->add($permission('role.edit'));

    $group->put('/{id:[0-9]+}', [RoleController::class, 'update'])
    ->setName('roles-user.update')
    ->add($permission('role.edit'));

    $group->delete('/{id:[0-9]+}', [RoleController::class, 'destroy'])
    ->setName('roles-user.destroy')
    ->add($permission('role.delete'));

    $group->get('/assign', [RoleController::class, 'assignment'])
    ->setName('roles-user.assignment')
    ->add($permission('role.assignment'));

    $group->post('/assign', [RoleController::class, 'assign'])
    ->setName('roles-user.assign')
    ->add($permission('role.assign'));

});