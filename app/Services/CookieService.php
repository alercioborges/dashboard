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
     * @param string   $name     Nome do cookie.
     * @param string   $value    Valor do cookie.
     * @param int|null $duration Duração em segundos (padrão: 7 dias; 0 = cookie de sessão).
     * @param string   $path     Caminho de validade.
     * @param string   $sameSite 'Lax', 'Strict' ou 'None'.
     *
     * @return bool Resultado do setcookie().
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function setCookie(
        string $name,
        string $value,
        ?int $duration = null,
        string $path = '/',
        string $sameSite = 'Lax'
    ): bool {
        // Validação do nome (token RFC 6265)
        if ($name === '' || !preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name)) {
            throw new InvalidArgumentException("Nome de cookie inválido: '{$name}'.");
        }

        if ($duration !== null && $duration < 0) {
            throw new InvalidArgumentException('A duração não pode ser negativa.');
        }

        $sameSite = ucfirst(strtolower($sameSite));
        if (!in_array($sameSite, ['Lax', 'Strict', 'None'], true)) {
            throw new InvalidArgumentException("SameSite inválido: '{$sameSite}'.");
        }

        $secure = self::isSecureConnection();

        // SameSite=None exige Secure nos navegadores modernos
        if ($sameSite === 'None' && !$secure) {
            throw new RuntimeException('SameSite=None exige conexão HTTPS.');
        }

        if (headers_sent($file, $line)) {
            throw new RuntimeException("Headers já enviados em {$file}:{$line}.");
        }

        $duration ??= 7 * 24 * 60 * 60; // 7 dias
        $expires  = $duration === 0 ? 0 : time() + $duration;

        return setcookie($name, $value, [
            'expires'  => $expires,
            'path'     => $path,
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
    }

    /**
     * Retrieves the value of a cookie.
     */
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Removes a cookie by setting its expiration date in the past.
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
