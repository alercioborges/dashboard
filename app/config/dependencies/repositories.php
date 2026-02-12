<?php

use Psr\Container\ContainerInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\RememberMeRepositoryInterface;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RememberMe;
use App\Services\QueryBuilderService;
use App\Services\PasswordService;

return [

    UserRepositoryInterface::class =>
    fn(ContainerInterface $c) => new User(
        $c->get(QueryBuilderService::class),
        $c->get(PasswordService::class)
    ),

    RoleRepositoryInterface::class =>
    fn(ContainerInterface $c) => new Role(
        $c->get(QueryBuilderService::class)
    ),

    RememberMeRepositoryInterface::class =>
    fn(ContainerInterface $c) => new RememberMe(
        $c->get(QueryBuilderService::class)
    ),

    PermissionRepositoryInterface::class =>
    fn(ContainerInterface $c) => new Permission(
        $c->get(QueryBuilderService::class)
    ),

];
