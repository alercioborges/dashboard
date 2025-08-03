<?php

$getBaseUrl = (function (): string {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Diretório base
    $baseDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    
    // Remove "public" do final se existir
    if (substr($baseDir, -6) === 'public') {
        $baseDir = substr($baseDir, 0, -6);
        $baseDir = rtrim($baseDir, '/'); // remove barra extra
    }

    $baseDir = $baseDir ? '/' . $baseDir : '';

    return $protocol . $host . $baseDir;
});

return [
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'env'   => $_ENV['APP_NODE_ENV'] ?? 'production',
    'url'   => $getBaseUrl()
];
