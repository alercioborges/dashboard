<?php

namespace App\Services;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RedirectService
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public static function redirect(string $target): Response
    {
        $response = new Response;
        return $response
            ->withHeader('Location', getUrl() . $target)
            ->withStatus(302);
    }

    public static function back(): void
    {
        $target = self::safeReferer() ?? (getUrl() . '/');

        header("Location: {$target}");
    }

    /**
     * Valida o header Referer contra o host/porta da própria aplicação.
     * Retorna null se o Referer não existir ou não for confiável,
     * sinalizando ao chamador para usar um destino padrão seguro.
     */
    private static function safeReferer(): ?string
    {
        if (empty($_SERVER['HTTP_REFERER'])) {
            return null;
        }

        $referer = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL);
        $refererParts = parse_url($referer);
        $appParts = parse_url(getUrl());

        if (
            $refererParts === false
            || !isset($refererParts['host'])
            || !isset($refererParts['scheme'])
            || !in_array($refererParts['scheme'], ['http', 'https'], true)
        ) {
            return null;
        }

        $sameHost = isset($appParts['host'])
            && strcasecmp($refererParts['host'], $appParts['host']) === 0;

        $samePort = ($refererParts['port'] ?? null) === ($appParts['port'] ?? null);

        if (!$sameHost || !$samePort) {
            return null;
        }

        return $referer;
    }

    public static function getRequestPath(Request $request): string
    {
        $path = $request->getUri()->getPath();
        $dir  = \getDir();
        return ($dir !== '' && str_starts_with($path, $dir))
            ? (substr($path, strlen($dir)) ?: '/')
            : $path;
    }
}
