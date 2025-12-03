<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * Enumeration of valid liturgical colors.
 *
 * Liturgical colors are colors that are used to decorate the church and
 * vestments on specific days and feasts in the liturgical calendar.
 *
 * - GREEN: Green is used on most ordinary Sundays.
 * - PURPLE: Purple is used during the Advent and Lent seasons.
 * - WHITE: White is used on most holy days and feasts, and during the
 *   Christmas and Easter seasons.
 * - RED: Red is used on Pentecost and on feasts of the martyrs.
 * - ROSE: Pink is used only on Laetare Sunday (the 4th Sunday of Lent)
 *   and on Gaudete Sunday (the 3rd Sunday of Advent).
 */
enum LitColor: string
{
    use EnumToArrayTrait;

    case GREEN  = 'green';
    case PURPLE = 'purple';
    case WHITE  = 'white';
    case RED    = 'red';
    case ROSE   = 'rose';

    /**
     * Translate the liturgical color to the given locale.
     *
     * @param string $locale The locale to translate to.
     * @return string The translated color name.
     */
    public function i18n(string $locale): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');
        return match ($this) {
            /**translators: context = liturgical color */
            self::GREEN  => $isLatin ? 'viridis' : _('green'),
            /**translators: context = liturgical color */
            self::PURPLE => $isLatin ? 'purpura' : _('purple'),
            /**translators: context = liturgical color */
            self::WHITE  => $isLatin ? 'albus'   : _('white'),
            /**translators: context = liturgical color */
            self::RED    => $isLatin ? 'ruber'   : _('red'),
            /**translators: context = liturgical color */
            self::ROSE   => $isLatin ? 'rosea'   : _('rose'),
        };
    }
}
