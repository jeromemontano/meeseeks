<?php

namespace App\Formatter;

class CharacterResponseFormatter {

    public static function format(array $response, string $search = ""): array
    {
        $formattedData = $response;

        if (!empty($search)) {
            $formattedData['search'] = $search;
        }

        return $formattedData;
    }
}
