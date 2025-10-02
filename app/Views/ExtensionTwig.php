<?php

namespace App\Views;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExtensionTwig extends AbstractExtension
{
    private RouteParserInterface $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_redirect', [$this, 'routeRedirect']),
            new TwigFunction('message', [$this, 'setMessage']),
            new TwigFunction('is_active', [$this, 'isActive']),
            new TwigFunction('is_enable', [$this, 'isEnable'])
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

    public function isActive(string $url, string $currentRoute): string
    {
        return ($currentRoute == $url) ? 'active' : '';
    }

    public function isEnable(array $routes, string $currentRoute): string
    {
        return (in_array($currentRoute, $routes)) ? 'menu-open' : '';
    }
}
