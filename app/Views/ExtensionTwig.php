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
            return $this->currentRoute === $routeUrl;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verifica recursivamente se algum filho está ativo
     */
    public function hasActiveChild(array $children): bool
    {
        foreach ($children as $child) {
            // Verifica se o item atual tem uma rota e está ativa
            if (isset($child['route']) && $this->isActiveRoute($child['route'])) {
                return true;
            }

            // Verifica recursivamente os filhos
            if (isset($child['children']) && is_array($child['children'])) {
                if ($this->hasActiveChild($child['children'])) {
                    return true;
                }
            }
        }

        return false;
    }



    private function loadTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates/layout/components/');
        $twig = new \Twig\Environment($loader);

        return $twig;
    }

    public function pagination(int $numPages, int $currentPage): string
    {
        return $this->loadTwig()->render('pagination.twig', [
            'numPages'    => $numPages,
            'currentPage' => $currentPage
        ]);
    }
}
