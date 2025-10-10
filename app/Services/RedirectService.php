<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface as Response;

class RedirectService
{
    public static function redirect(string $target): \Slim\Psr7\Response
    {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Location', getUrl() . $target)
            ->withStatus(302);
    }

    public static function back()
    {
        $previus = "javascript:history.go(-1)";

        if (isset($_SERVER['HTTP_REFERER'])) {
            $previus = $_SERVER['HTTP_REFERER'];
        }

        return header("Location: {$previus}");
    }
}
