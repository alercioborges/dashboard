<?php

namespace App\Services;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RedirectService
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }




    public static function redirect(string $target): Response
    {
        $response = new Response;
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

    public static function getRequestPath(Request $request): string
    {
        $path = explode(
            getDir(),
            $request->getUri()->getPath()
        );

        return $path[1];
    }
}
