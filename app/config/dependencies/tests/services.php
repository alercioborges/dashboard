<?php

use Psr\Container\ContainerInterface;

use Services\Tests\AuthServiceTeste;

use App\Services\AuthService;

return [

    AuthServiceTeste::class =>
    fn(ContainerInterface $c): AuthServiceTeste => new AuthServiceTeste(
        $c->get(AuthService::class)
    ),

];
