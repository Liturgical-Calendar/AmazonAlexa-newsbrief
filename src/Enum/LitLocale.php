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
    public static array $primaryRegion = [
        "en" => "US",
        "fr" => "FR",
        "de" => "DE",
        "it" => "IT",
        "la" => "VA",
        "pt" => "PT",
        "es" => "ES"
    ];

    public static function isValid($value)
    {
        $baseLocale = \Locale::getPrimaryLanguage($value);
        return in_array($baseLocale, self::$values);
    }
}
