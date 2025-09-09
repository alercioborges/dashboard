<?php

namespace App\Services;

class FlashMessageService extends Sanitize
{
    public static function add(string $index, string $message)
    {
        return $_SESSION[$index] = $message;
    }

    public static function get(string $field): ?string
    {
        if (isset($_SESSION[$field])) {
            $message = $_SESSION[$field];
            unset($_SESSION[$field]);
            return $message;
        }

        return NULL;
    }

    function old(string $field, $value): ?string
    {
        if (!isset($_SESSION['old'][$field])) {
            $_SESSION['old'][$field] = $value;
            return htmlspecialchars($value, ENT_QUOTES);
        }

        return NULL;
    }
}
