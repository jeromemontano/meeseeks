<?php

namespace App\Formatter;

class CharacterResponseFormatter {

    public static function format(array $characters, string $search): array
    {
        return [
            'name' => $search,
            'characters' => $characters,
        ];
    }
}
