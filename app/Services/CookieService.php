<?php

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

class CookieService
{
    /**
     * Detecta de forma robusta se a conexão atual usa HTTPS.
     */
    private static function isSecureConnection(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }
        if (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
        ) {
            return true;
        }
        return false;
    }

    /**
     * Cria (define) um cookie de forma segura.
     *
     * @param string      $name     Nome do cookie.
     * @param string      $value    Valor do cookie.
     * @param int         $duration Duração em segundos (0 = cookie de sessão).
     * @param string      $path     Caminho de validade.
     *
     * @return bool  Resultado do setcookie().
     * @throws InvalidArgumentException
     */
    public function setCookie(
        string $name,
        string $value,
        int $duration = 3000,
        string $path = '/'
    ): bool {

        // Validação do nome.
        if ($name === '' || preg_match('/[=,; \t\r\n\013\014]/', $name)) {
            throw new InvalidArgumentException("Nome de cookie inválido: '{$name}'.");
        }

        // Não permite enviar cookie após o output já ter começado.
        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers já enviados em {$file}:{$line}.");
        }

        return setcookie(
            $name,
            $value,
            [
                'expires'  => $duration,
                'path'     => $path,
                'domain'   => '',
                'secure'   => self::isSecureConnection(),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * Recupera o valor de um cookie.
     */
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Remove um cookie definindo expiração no passado.
     */
    public function deleteCookie(string $name, string $path = '/'): bool
    {
        unset($_COOKIE[$name]);

        return setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => $path,
            'domain'   => '',
            'secure'   => self::isSecureConnection(),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}
