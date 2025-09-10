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

    public static function addOldInput(array $formData):array
    {
        return $_SESSION['old_input'] = $formData;
    }

    public static function getOldInput(): ?array
    {
        if (isset($_SESSION['old_input'])) {
            $formData = $_SESSION['old_input'];           
            unset($_SESSION['old_input']);
            return $formData;
        }

        return NULL;
    }
}
