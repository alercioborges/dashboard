<?php

use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Contexto CLI não tem $_SERVER['HTTP_HOST']/SCRIPT_NAME/REQUEST_URI
// (usados em app/config/app.php para montar a URL base da aplicação web).
// Preenche com valores neutros para não gerar warnings de índice indefinido.
$_SERVER['HTTP_HOST']    = $_SERVER['HTTP_HOST']    ?? 'cli';
$_SERVER['SCRIPT_NAME']  = $_SERVER['SCRIPT_NAME']  ?? '';
$_SERVER['REQUEST_URI']  = $_SERVER['REQUEST_URI']  ?? '/';

require __DIR__ . '/../config/global-vars.php';

return require __DIR__ . '/../config/container.php';
