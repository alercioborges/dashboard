<?php

namespace App\Services;

class LoadFileService
{
    public static function file(string $file): mixed
    {
        $file = path() . $file;

        if (!file_exists($file)) {
            throw new \Exception("This file does not exist or could not be found: ($file)");
        }

        return require $file;
    }
}
