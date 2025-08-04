<?php

namespace App\Utils;

class IdExtractor
{
    public static function extractIdFromUrl(string $url): int
    {
        return (int)basename($url);
    }
}
