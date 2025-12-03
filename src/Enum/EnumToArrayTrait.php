<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * A trait that provides utility methods for backed enums.
 *
 * Provides methods for retrieving enum names, values, and validating values.
 */
trait EnumToArrayTrait
{
    /**
     * Returns an array of all enum case names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Returns an array of all enum case values.
     *
     * @return array<string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns an associative array of enum names to values.
     *
     * @return array<string, string|int>
     */
    public static function asArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Validates a single value against the enum values.
     *
     * @param string|int $value The value to validate.
     * @return bool True if the value is valid, otherwise false.
     */
    public static function isValid(string|int $value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Validates multiple values against the enum values.
     *
     * @param array<string|int> $values The values to validate.
     * @return bool True if all values are valid, otherwise false.
     */
    public static function areValid(array $values): bool
    {
        return array_reduce($values, function (bool $carry, string|int $value): bool {
            return $carry && self::isValid($value);
        }, true);
    }
}
