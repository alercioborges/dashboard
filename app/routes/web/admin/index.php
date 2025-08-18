<?php

use app\controllers\UserController;

$app->group('/admin', function (Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', [UserController::class, 'index']);
    
    // Rotas de usuários
    require 'users.php';
});
