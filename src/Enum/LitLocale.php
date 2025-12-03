<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * A class representing supported locales for the Liturgical Calendar.
 *
 * This is kept as a class rather than an enum because it has dynamic locale loading
 * and runtime state that doesn't fit the enum pattern.
 */
class LitLocale
{
    public const ENGLISH    = 'en';
    public const FRENCH     = 'fr';
    public const GERMAN     = 'de';
    public const ITALIAN    = 'it';
    public const LATIN      = 'la';
    public const PORTUGUESE = 'pt';
    public const SPANISH    = 'es';

    /** @var array<string> */
    public static array $values = ['en', 'fr', 'de', 'it', 'la', 'pt', 'es'];

    /** @var array<string, string> */
    public static array $primaryRegion = [
        'en' => 'US',
        'fr' => 'FR',
        'de' => 'DE',
        'it' => 'IT',
        'la' => 'VA',
        'pt' => 'PT',
        'es' => 'ES'
    ];

    /**
     * Validates a locale value.
     *
     * @param string $value The locale to validate.
     * @return bool True if the locale is valid.
     */
    public static function isValid(string $value): bool
    {
        $baseLocale = \Locale::getPrimaryLanguage($value);
        return in_array($baseLocale, self::$values, true);
    }
}
