<?php

use App\Controllers\AdministrationController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\PermissionMiddleware;

$app->group('/admin', function (Slim\Routing\RouteCollectorProxy $group) use ($container) {

    $permission = $container->get(PermissionMiddleware::class);

    $group->get('', [AdministrationController::class, 'index'])->setName('admin.index')
    ->add($permission('users.view'));

    // Users routs
    require __DIR__ . '/users.php';

    // Users routs
    require __DIR__ . '/roles-user.php';
    
})
->add(AuthMiddleware::class);
