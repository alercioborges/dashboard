<?php

namespace App\Views;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Slim\Views\Twig;

class ExtensionTwig extends AbstractExtension
{
    private RouteParserInterface $routeParser;
    private string $currentRoute;

    public function __construct(RouteParserInterface $routeParser, string $currentRoute = '')
    {
        $this->routeParser = $routeParser;
        $this->currentRoute = $currentRoute;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_redirect', [$this, 'routeRedirect']),
            new TwigFunction('message', [$this, 'setMessage']),
            new TwigFunction('pagination', [$this, 'pagination'], ['is_safe' => ['html']]),
            new TwigFunction('is_active_route', [$this, 'isActiveRoute']),
            new TwigFunction('has_active_child', [$this, 'hasActiveChild']),
        ];
    }

    public function routeRedirect(string $routeName, array $params = [], array $queryParams = []): string
    {
        return $this->routeParser->urlFor($routeName, $params, $queryParams);
    }

    public function setMessage($field)
    {
        return \App\Services\FlashMessageService::get($field);
    }

    /**
     * Verifica se a rota atual corresponde à rota do menu
     */
    public function isActiveRoute(string $routeName): bool
    {
        try {
            $routeUrl = $this->routeParser->urlFor($routeName);

            // Normaliza as URLs removendo trailing slashes
            $currentPath = rtrim($this->currentRoute, '/');
            $routePath = rtrim(parse_url($routeUrl, PHP_URL_PATH), '/');

            // Compara as rotas
            return $currentPath === $routePath || $currentPath === $routePath . '/';
        } catch (\Exception $e) {
            return false;
        }
    }
}
