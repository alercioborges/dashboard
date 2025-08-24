<?php

namespace App\Views;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigRedirect extends AbstractExtension
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
        ];
    }

    public function routeRedirect(string $routeName): string
    {
        return $this->routeParser->urlFor($routeName);
    }
}
