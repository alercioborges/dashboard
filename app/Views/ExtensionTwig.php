<?php

namespace App\Views;

use Slim\Interfaces\RouteParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Slim\Csrf\Guard;

class ExtensionTwig extends AbstractExtension
{
    private RouteParserInterface $routeParser;
    private Guard $csrf;
    private string $currentRoute;
    private string $baseDir;

    public function __construct(
        RouteParserInterface $routeParser,
        Guard $csrf,
        string $currentRoute = '',
        string $baseDir = ''
    ) {
        $this->routeParser = $routeParser;
        $this->csrf = $csrf;
        $this->currentRoute = $currentRoute;
        $this->baseDir = $baseDir;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_redirect', [$this, 'routeRedirect']),
            new TwigFunction('message', [$this, 'setMessage']),
            new TwigFunction('pagination', [$this, 'pagination'], ['is_safe' => ['html']]),
            new TwigFunction('is_active_route', [$this, 'isActiveRoute']),
            new TwigFunction('has_active_child', [$this, 'hasActiveChild']),
            new TwigFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])

        ];
    }

    public function routeRedirect(string $routeName, array $params = [], array $queryParams = []): string
    {
        return $this->routeParser->urlFor($routeName, $params, $queryParams);
    }

    public function setMessage(string $field)
    {
        return \App\Services\FlashMessageService::get($field);
    }

    public function isActiveRoute(string $routeName): bool
    {
        try {

            $routeUrl = $this->routeParser->urlFor($routeName);
            $routePath = parse_url($routeUrl, PHP_URL_PATH);

            if ($this->baseDir && str_starts_with($routePath, $this->baseDir)) {
                $routePath = substr($routePath, strlen($this->baseDir));
            }

            $routePath = '/' . trim($routePath, '/');
            $currentPath = '/' . trim($this->currentRoute, '/');

            return $currentPath === $routePath;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Verifica recursivamente se algum filho está ativo
     */
    public function hasActiveChild(array $children): bool
    {
        foreach ($children as $child) {
            if (isset($child['route']) && $this->isActiveRoute($child['route'])) {
                return true;
            }

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

    public function csrfInput(): string
    {
        $nameKey  = $this->csrf->getTokenNameKey();
        $valueKey = $this->csrf->getTokenValueKey();

        $name  = $this->csrf->getTokenName();
        $value = $this->csrf->getTokenValue();

        return sprintf(
            '<input type="hidden" name="%s" value="%s">' .
                '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars($nameKey,  ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($name,  ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($valueKey,  ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($value,  ENT_QUOTES, 'UTF-8')
        );
    }
}
