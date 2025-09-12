<?php

namespace App\Services;

class FlashMessageService
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
}
