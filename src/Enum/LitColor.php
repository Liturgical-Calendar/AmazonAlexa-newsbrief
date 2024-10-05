<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitLocale;

/**
 * The LitColor class is an enumeration of valid liturgical colors.
 *
 * Liturgical colors are colors that are used to decorate the church and
 * vestments on specific days and feasts in the liturgical calendar.
 *
 * The values of the enumeration are:
 *  - GREEN: Green is used on most ordinary Sundays.
 *  - PURPLE: Purple is used during the Advent and Lent seasons.
 *  - WHITE: White is used on most holy days and feasts, and during the
 *  Christmas and Easter seasons.
 *  - RED: Red is used on Pentecost and on feasts of the martyrs.
 *  - PINK: Pink is used only on Laetare Sunday (the 4th Sunday of Lent)
 *  and on Gaudete Sunday (the 3rd Sunday of Advent).
 *
 * The class contains methods for checking if a given value or array of
 * values is valid.
 */
class LitColor
{
    public const GREEN     = "green";
    public const PURPLE    = "purple";
    public const WHITE     = "white";
    public const RED       = "red";
    public const PINK      = "pink";
    public static array $values = [ "green", "purple", "white", "red", "pink" ];

    /**
     * Returns true if the given value is a valid liturgical color.
     *
     * If $value is a comma-separated string of values, it will be split into an
     * array and passed to {@see LitColor::areValid()}.
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value)
    {
        if (strpos($value, ',')) {
            return LitColor::areValid(explode(',', $value));
        }
        return in_array($value, self::$values);
    }

    /**
     * Returns true if all of the given values are valid liturgical colors.
     * @param array $values
     * @return bool
     */
    public static function areValid(array $values)
    {
        return empty(array_diff($values, self::$values));
    }

    /**
     * Translate a liturgical color to the given locale.
     *
     * @param string $value
     * @param string $locale
     * @return string
     */
    public static function i18n(string $value, string $locale): string
    {
        switch ($value) {
            case self::GREEN:
                /**translators: context = liturgical color */
                return $locale === LitLocale::LATIN ? 'viridis'     : _("green");
            case self::PURPLE:
                /**translators: context = liturgical color */
                return $locale === LitLocale::LATIN ? 'purpura'     : _("purple");
            case self::WHITE:
                /**translators: context = liturgical color */
                return $locale === LitLocale::LATIN ? 'albus'       : _("white");
            case self::RED:
                /**translators: context = liturgical color */
                return $locale === LitLocale::LATIN ? 'ruber'       : _("red");
            case self::PINK:
                /**translators: context = liturgical color */
                return $locale === LitLocale::LATIN ? 'rosea'       : _("pink");
        }
    }
}
