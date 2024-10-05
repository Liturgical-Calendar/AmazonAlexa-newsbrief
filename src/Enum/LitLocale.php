<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

class LitLocale
{
    public const ENGLISH               = "en";
    public const FRENCH                = "fr";
    public const GERMAN                = "de";
    public const ITALIAN               = "it";
    public const LATIN                 = "la";
    public const PORTUGUESE            = "pt";
    public const SPANISH               = "es";
    public static array $values = [ "en", "fr", "de", "it", "la", "pt", "es" ];

    public static function isValid($value)
    {
        return in_array($value, self::$values);
    }
}
