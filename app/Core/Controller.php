<?php

namespace App\Core;

use Slim\Views\Twig;

abstract class Controller
{
    protected Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }
}
