<?php

namespace App\Services;

class FlashMessageService
{
    public static function add(string $index, string $message)
    {
        return $_SESSION[$index] = $message;
    }

    public static function get(string $type)
    {
        if (isset($_SESSION[$type])) {
            $message = $_SESSION[$type];
            unset($_SESSION[$type]);

            return $message;
        }

        return null;
    }
}
