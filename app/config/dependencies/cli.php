    <?php

use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;

return [

    /**
     * CLI MODE RESPONSE FACTORY
     * Prevents container failure when Slim\App is not created.
     */
    ResponseFactoryInterface::class =>
        fn() => new ResponseFactory(),

];
