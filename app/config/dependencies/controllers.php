<?php

use Psr\Container\ContainerInterface;

use App\Models\Role;

use App\Controllers\{
    UserController,
    RoleController,
    AuthController,
    ForgotPasswordController,
    SetupController
};

use App\Services\{
    UserService,
    RoleService,
    ForgotPasswordService
};

use App\Interfaces\AuthServiceInterface;
use App\Services\Validators\Validator;

use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return [

    // -------------------------------------------------------
    // HTTP CONTROLLERS
    // -------------------------------------------------------

    /**
     * UserController
     *
     * Handles all user-related HTTP actions.
     */
    UserController::class => function (ContainerInterface $c): UserController {
        return new UserController(
            $c->get(Twig::class),
            $c->get(UserService::class),
            $c->get(Validator::class),
            $c->get(Role::class),
            $c->get(LoggerInterface::class)
        );
    },

    /**
     * RoleController
     *
     * Handles all role-related HTTP actions.
     */
    RoleController::class => function (ContainerInterface $c): RoleController {
        return new RoleController(
            $c->get(Twig::class),
            $c->get(RoleService::class),
            $c->get(Validator::class)
        );
    },

    /**
     * AuthController
     *
     * Handles authentication and authorization actions.
     */
    AuthController::class => function (ContainerInterface $c): AuthController {
        return new AuthController(
            $c->get(Twig::class),
            $c->get(AuthServiceInterface::class),
            $c->get(LoggerInterface::class),
            $c->get(Validator::class)
        );
    },

    ForgotPasswordController::class => function (ContainerInterface $c): ForgotPasswordController {
        return new ForgotPasswordController(
            $c->get(Twig::class),
            $c->get(ForgotPasswordService::class),
            $c->get(Validator::class),
            $c->get(LoggerInterface::class)

        );
    },

    SetupController::class => function (ContainerInterface $c): SetupController {
        return new SetupController(
            $c->get(Twig::class),
            $c->get(UserService::class),
            $c->get(Validator::class),
            $c->get(LoggerInterface::class),
            $c->get(AuthServiceInterface::class)
        );
    }

];
