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
            new TwigFunction('oldInput', [$this, 'setOldInput'])
        ];
    }

    public function routeRedirect(string $routeName): string
    {
        return $this->routeParser->urlFor($routeName);
    }

    public function setMessage($field)
    {
        return \App\Services\FlashMessageService::get($field);
    }

    public function setOldInput(string $field)
    {
        $formData = \App\Services\FlashMessageService::getOldInput();

        if ($formData === null) {
            return null;
        }

        if ($field !== null && isset($formData[$field])) {
            return $formData[$field];
        }

        return null;
    }
}
