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

    protected function getOldInput(): ?array
    {
        if (isset($_SESSION['old_input'])) {
            $oldInput = $_SESSION['old_input'];
            unset($_SESSION['old_input']);
            return $oldInput;
        }

        return NULL;
    }

    protected function setOldInput(array $formData): ?array
    {
        $_SESSION['old_input'] = array_filter($formData);

        if (empty($_SESSION['old_input']) && isset($_SESSION['old_input'])) {
            unset($_SESSION['old_input']);
            return null;
        }

        return $_SESSION['old_input'];
    }
}
