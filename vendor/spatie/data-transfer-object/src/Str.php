<?php

namespace Spatie\DataTransferObject;

class Str
{
    public static function contains(string $string, $searches): bool
    {
        $searches = (array) $searches;

        foreach ($searches as $search) {
            if (strpos($string, $search) !== false) {
                return true;
            }
        }

        return false;
    }
}
