<?php

namespace App\Services;

class LoadFileService
{
    private static array $cache = [];

    public static function file(string $file): mixed
    {
        if (isset(self::$cache[$file])) {
            return self::$cache[$file];
        }

        $absolutePath = path() . $file;

        if (!file_exists($absolutePath)) {
            throw new \Exception("This file does not exist or could not be found: ($absolutePath)");
        }

        return self::$cache[$file] = require $absolutePath;
    }
}
