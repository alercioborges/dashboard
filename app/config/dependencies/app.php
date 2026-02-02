<?php

use Slim\Psr7\Factory\ResponseFactory;

return [

    // -------------------------------------------------------
    // APPLICATION CORE
    // -------------------------------------------------------

    'appConfig' =>  require __DIR__ . '/../app.php',
    'dbConfig'  => require __DIR__ . '/../database.php',
    'smtpConfig' => require __DIR__ . '/../smtp.php',

    /**
     * PSR-7 Response Factory (REQUIRED FOR CLI)
     * We provide a concrete implementation so the container
     * does not fail when Slim\App is not created (CLI mode).
     */
    Psr\Http\Message\ResponseFactoryInterface::class =>
    function (): ResponseFactory {
        return new ResponseFactory();
    },

];
