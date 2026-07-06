<?php

function dd($data): void
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}

function path(): string
{
    $vendorDir = dirname(dirname(__FILE__));
    return dirname($vendorDir);
}

function flash(string $index, string $message): string
{
    return \App\Services\FlashMessageService::add($index, $message);
}

function error(string $message): string
{
    return '<div class="alert alert-danger" role="alert"><strong>' . $message . '</strong></div>';
}

function success(string $message): string
{
    return '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>' . $message . '</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

function getUrl(): string
{
    return $GLOBALS['app_config']['url'];
}

function getDir(): string
{
    return $GLOBALS['app_config']['baseDir'];
}

function redirect(string $target): Slim\Psr7\Response
{
    return \App\Services\RedirectService::redirect($target);
}

function back(): void
{
    \App\Services\RedirectService::back();
    exit;
}

function currentRoute()
{
    return $GLOBALS['app_config']['current_route'];
}

function loader(string $file): mixed
{
    return \App\Services\LoadFileService::file($file);
}

function getRequestPath(Psr\Http\Message\ServerRequestInterface $request): string
{
    return \App\Services\RedirectService::getRequestPath($request);
}
