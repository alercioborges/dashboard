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

function error($message): string
{
    return '<div class="alert alert-danger" role="alert"><strong>' . $message . '</strong></div>';
}

function success($message): string
{
    return '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>' . $message . '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function getUrl(): string
{
    return \App\Services\LoadFileService::file('/app/config/app.php')['url'];
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
