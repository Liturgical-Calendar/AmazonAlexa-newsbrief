<?php

class LitLocale
{
    public const ENGLISH               = "EN";
    public const FRENCH                = "FR";
    public const GERMAN                = "DE";
    public const ITALIAN               = "IT";
    public const LATIN                 = "LA";
    public const PORTUGUESE            = "PT";
    public const SPANISH               = "ES";
    public static array $values = [ "EN", "FR", "DE", "IT", "LA", "PT", "ES" ];

    public static function isValid($value)
    {
        return in_array($value, self::$values);
    }
}
