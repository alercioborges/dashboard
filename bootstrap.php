<?php

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

// Load vendor autoloader
require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// -----------------------------------------------------------------
// Session hardening — precisa ser configurado ANTES de session_start()
// -----------------------------------------------------------------
if (session_status() !== PHP_SESSION_ACTIVE) {

    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    // Força secure em produção (defesa em profundidade), senão depende da detecção de HTTPS
    $isProd = ($_ENV['APP_MODE_ENV'] ?? 'production') === 'production';

    // Impede que o PHP aceite um session ID inventado pelo cliente
    ini_set('session.use_strict_mode', '1');

    // Nunca expõe o session ID na URL
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    session_set_cookie_params([
        'lifetime' => 0,          // cookie de sessão (expira ao fechar o navegador)
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isProd || $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

// Load global variables
require __DIR__ . '/app/config/global-vars.php';

// Load container
$container = require __DIR__ . '/app/config/container.php';

// Create application
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register the App instance in the container
$container->set(Slim\App::class, $app);

// Add directory if exist
if (!empty($GLOBALS['app_config']['baseDir'])) {
    $app->setBasePath($GLOBALS['app_config']['baseDir']);
}

// Body Parsing to read POST data
$app->addBodyParsingMiddleware();

// Method Override to convert _METHOD to HTTP method
$app->add(new Slim\Middleware\MethodOverrideMiddleware());

// Setting middlewares
$middleware = require __DIR__ . '/app/config/middlewares.php';
$middleware($app);
