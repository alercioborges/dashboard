<?php

use App\Controllers\AdministrationController;

$app->group('/admin', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [AdministrationController::class, 'index'])->setName('admin.index');

    // Users routs
    require 'users.php';

    // Users routs
    require 'roles-user.php';
});
