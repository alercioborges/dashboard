<?php

use Slim\Psr7\Factory\ResponseFactory;

return [

    // -------------------------------------------------------
    // APPLICATION CORE
    // -------------------------------------------------------

    'appConfig'  => loader('/app/config/app.php'),
    'dbConfig'   => loader('/app/config/database.php'),
    'smtpConfig' => loader('/app/config/smtp.php'),

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
