<?php

namespace App\Lib;

class Pluralizer
{
    /**
     * @param string $singular
     * @return string
     */
    public static function pluralize(string $singular)
    {
        $last_letter = strtolower($singular[strlen($singular) - 1]);
        switch ($last_letter)
        {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
            case 'x':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }
}