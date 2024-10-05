<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * An enumeration that represents the type of a feast in the Liturgical Calendar.
 *
 * This class defines two constants:
 * - FIXED "fixed": for feasts that occur on the same date every year
 * - MOBILE "mobile": for feasts that occur on a different date each year, such as Easter.
 *
 * Also provides a static method, isValid, which can be used to check if a given string is a valid "Feast Type".
 *
 * @author John R. D'Orazio <priest@johnromanodorazio.com>
 * @package LiturgicalCalendar\AlexaNewsBrief
 */
class LitFeastType
{
    public const FIXED     = "fixed";
    public const MOBILE    = "mobile";
    public static array $values = [ "fixed", "mobile" ];

    /**
     * Checks if the given string is a valid "Feast Type".
     *
     * @param string $value The value to check.
     * @return bool True if the value is valid, otherwise false.
     */
    public static function isValid(string $value)
    {
        return in_array($value, self::$values);
    }
}
