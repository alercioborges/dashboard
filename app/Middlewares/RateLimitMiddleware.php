<?php

namespace App\Middlewares;

use App\Services\RateLimiterService;
use App\Support\ClientIp;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private RateLimiterService $rateLimiter,
        private string $action,
        private int $maxAttempts,
        private int $decayMinutes,
        private array $trustedProxies = []
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = ClientIp::resolve($request, $this->trustedProxies);

        // Chave por IP (contém força bruta distribuída/volumosa de um mesmo ponto)
        $ipKey = "ip:{$ip}";

        // Chave por conta, se o form enviar e-mail (contém credential stuffing
        // mirado numa conta específica vindo de vários IPs)
        $body = (array) $request->getParsedBody();
        $email = isset($body['email']) ? mb_strtolower(trim((string) $body['email'])) : null;
        $emailKey = $email ? "email:{$email}:{$this->action}" : null;

        foreach (array_filter([$ipKey, $emailKey]) as $key) {
            if ($this->rateLimiter->tooManyAttempts($key, $this->action, $this->maxAttempts, $this->decayMinutes)) {

                $seconds = $this->rateLimiter->availableIn($key, $this->action, $this->decayMinutes);
                $minutes = (int) ceil($seconds / 60);

                flash('error', error("Muitas tentativas. Tente novamente em {$minutes} minuto(s)."));

                return redirect($request->getUri()->getPath())->withStatus(429);
            }
        }

        // Registra a tentativa ANTES de processar — mesmo se o handler explodir,
        // a tentativa já conta (evita contornar o limite provocando erro 500 repetido)
        $this->rateLimiter->hit($ipKey, $this->action);
        if ($emailKey) {
            $this->rateLimiter->hit($emailKey, $this->action);
        }

        return $handler->handle($request);
    }
}
