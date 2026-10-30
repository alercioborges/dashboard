<?php

namespace App\Support;

use Psr\Http\Message\ServerRequestInterface;

final class ClientIp
{
    /**
     * IPs/ranges de proxies confiáveis (seu load balancer, nginx reverso, etc.).
     * Configure via .env/appConfig — NUNCA confie em X-Forwarded-For
     * vindo de uma origem desconhecida, ou qualquer cliente pode forjar
     * o cabeçalho pra burlar o rate limit.
     */
    public static function resolve(ServerRequestInterface $request, array $trustedProxies = []): string
    {
        $remoteAddr = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';

        if (in_array($remoteAddr, $trustedProxies, true)) {
            $forwarded = $request->getHeaderLine('X-Forwarded-For');
            if ($forwarded !== '') {
                // primeiro IP da lista é o do cliente original
                return trim(explode(',', $forwarded)[0]);
            }
        }

        return $remoteAddr;
    }
}
