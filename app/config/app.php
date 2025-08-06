<?php

$pathUrl = (function (): array {
    
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $baseDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if (substr($baseDir, -6) === 'public') {
        $baseDir = substr($baseDir, 0, -6);
        $baseDir = rtrim($baseDir, '/');
    }

    // Corrigir linha que não alterava nada:
    $baseDir = $baseDir ? '/' . $baseDir : '';

    return [
        'url' => $protocol . $host . $baseDir,
        'baseDir' => $baseDir
    ];
})();

return [
    'debug'     => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'env'       => $_ENV['APP_MODE_ENV'] ?? 'production',
    'baseDir'   => $pathUrl['baseDir'],
    'url'       => $pathUrl['url']
];
