<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * Enumeration representing the Grade or Rank of a liturgical celebration.
 *
 * Defines the order of precedence of the liturgical days as indicated in the
 * universal norms for the liturgical year and the general roman calendar
 * promulgated by the motu proprio "mysterii paschalis" by pope paul vi on february 14 1969.
 *
 * Constants defined here (with values in order of importance) are:
 * - HIGHER_SOLEMNITY (7): Higher ranking solemnities, that have precedence over all others
 * - SOLEMNITY (6): Solemnities of the Lord, of the Blessed Virgin Mary, and of the saints
 * - FEAST_LORD (5): Feasts of the Lord
 * - FEAST (4): Feasts of the Blessed Virgin Mary and of the saints
 * - MEMORIAL (3): Memorials of the Blessed Virgin Mary and of the saints
 * - MEMORIAL_OPT (2): Optional memorials
 * - COMMEMORATION (1): Commemoration of the Blessed Virgin Mary and of the saints
 * - WEEKDAY (0): Weekday
 */
enum LitGrade: int
{
    use EnumToArrayTrait;

    // I.
    // HIGHER RANKING SOLEMNITIES, THAT HAVE PRECEDENCE OVER ALL OTHERS:
    // 1. EASTER TRIDUUM
    // 2. CHRISTMAS, EPIPHANY, ASCENSION, PENTECOST
    //    SUNDAYS OF ADVENT, LENT AND EASTER
    //    ASH WEDNESDAY
    //    DAYS OF THE HOLY WEEK, FROM MONDAY TO THURSDAY
    //    DAYS OF THE OCTAVE OF EASTER
    case HIGHER_SOLEMNITY = 7;

    // 3. SOLEMNITIES OF THE LORD, OF THE BLESSED VIRGIN MARY, OF THE SAINTS LISTED IN THE GENERAL CALENDAR
    //    COMMEMORATION OF THE FAITHFUL DEPARTED
    // 4. PARTICULAR SOLEMNITIES:
    //      a) PATRON OF THE PLACE, OF THE COUNTRY OR OF THE CITY
    //      b) SOLEMNITY OF THE DEDICATION AND OF THE ANNIVERSARY OF THE DEDICATION OF A CHURCH
    //      c) SOLEMNITY OF THE TITLE OF A CHURCH
    //      d) SOLEMNITY OF THE TITLE OR OF THE FOUNDER OR OF THE MAIN PATRON OF AN ORDER OR CONGREGATION
    case SOLEMNITY = 6;

    // II.
    // 5. FEASTS OF THE LORD LISTED IN THE GENERAL CALENDAR
    // 6. SUNDAYS OF CHRISTMAS AND OF ORDINARY TIME
    case FEAST_LORD = 5;

    // 7. FEASTS OF THE BLESSED VIRGIN MARY AND OF THE SAINTS IN THE GENERAL CALENDAR
    // 8. PARTICULAR FEASTS:
    //      a) MAIN PATRON OF THE DIOCESE
    //      b) FEAST OF THE ANNIVERSARY OF THE DEDICATION OF THE CATHEDRAL
    //      c) FEAST OF THE MAIN PATRON OF THE REGION OR OF THE PROVINCE, OF THE NATION, OF A LARGER TERRITORY
    //      d) FEAST OF THE TITLE, OF THE FOUNDER, OF THE MAIN PATRON OF AN ORDER OR CONGREGATION
    //      e) OTHER PARTICULAR FEASTS OF SOME CHURCH
    //      f) OTHER FEASTS LISTED IN THE CALENDAR OF EACH DIOCESE, ORDER OR CONGREGATION
    // 9. WEEKDAYS OF ADVENT FROM THE 17th TO THE 24th OF DECEMBER
    //    DAYS OF THE OCTAVE OF CHRISTMAS
    //    WEEKDAYS OF LENT
    case FEAST = 4;

    // III.
    // 10. MEMORIALS OF THE GENERAL CALENDAR
    // 11. PARTICULAR MEMORIALS:
    //      a) MEMORIALS OF THE SECONDARY PATRON OF A PLACE, OF A DIOCESE, OF A REGION OR RELIGIOUS PROVINCE
    //      b) OTHER MEMORIALS LISTED IN THE CALENDAR OF EACH DIOCESE, ORDER OR CONGREGATION
    case MEMORIAL = 3;

    // 12. OPTIONAL MEMORIALS, WHICH CAN HOWEVER BE OBSERVED IN DAYS INDICATED AT N. 9,
    //     ACCORDING TO THE NORMS DESCRIBED IN "PRINCIPLES AND NORMS" FOR THE LITURGY OF THE HOURS AND THE MISSAL
    case MEMORIAL_OPT = 2;

    //     SIMILARLY MEMORIALS CAN BE OBSERVED AS OPTIONAL MEMORIALS THAT SHOULD FALL DURING THE WEEKDAYS OF LENT
    case COMMEMORATION = 1;

    // 13. WEEKDAYS OF ADVENT UNTIL DECEMBER 16th
    //     WEEKDAYS OF CHRISTMAS, FROM JANUARY 2nd UNTIL THE SATURDAY AFTER EPIPHANY
    //     WEEKDAYS OF THE EASTER SEASON, FROM THE MONDAY AFTER THE OCTAVE OF EASTER UNTIL SATURDAY BEFORE PENTECOST
    //     WEEKDAYS OF ORDINARY TIME
    case WEEKDAY = 0;

    /**
     * Get the localized name for this liturgical grade.
     *
     * @param string $locale The locale to translate to.
     * @param bool $html If true, the localized text will be wrapped with HTML tags.
     * @return string The translated grade name.
     */
    public function i18n(string $locale, bool $html = true): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');

        ['grade' => $grade, 'tags' => $tags] = match ($this) {
            /**translators: liturgical rank. Keep lowercase */
            self::WEEKDAY => [
                'grade' => $isLatin ? 'feria' : _('weekday'),
                'tags'  => ['<I>', '</I>']
            ],
            /**translators: liturgical rank. Keep Capitalized */
            self::COMMEMORATION => [
                'grade' => $isLatin ? 'Commemoratio' : _('Commemoration'),
                'tags'  => ['<I>', '</I>']
            ],
            /**translators: liturgical rank. Keep Capitalized */
            self::MEMORIAL_OPT => [
                'grade' => $isLatin ? 'Memoria ad libitum' : _('Optional memorial'),
                'tags'  => ['', '']
            ],
            /**translators: liturgical rank. Keep Capitalized */
            self::MEMORIAL => [
                'grade' => $isLatin ? 'Memoria obligatoria' : _('Memorial'),
                'tags'  => ['', '']
            ],
            /**translators: liturgical rank. Keep UPPERCASE */
            self::FEAST => [
                'grade' => $isLatin ? 'FESTUM' : _('FEAST'),
                'tags'  => ['', '']
            ],
            /**translators: liturgical rank. Keep UPPERCASE */
            self::FEAST_LORD => [
                'grade' => $isLatin ? 'FESTUM DOMINI' : _('FEAST OF THE LORD'),
                'tags'  => ['<B>', '</B>']
            ],
            /**translators: liturgical rank. Keep UPPERCASE */
            self::SOLEMNITY => [
                'grade' => $isLatin ? 'SOLLEMNITAS' : _('SOLEMNITY'),
                'tags'  => ['<B>', '</B>']
            ],
            /**translators: liturgical rank. Keep lowercase */
            self::HIGHER_SOLEMNITY => [
                'grade' => $isLatin ? 'celebratio altioris ordinis quam sollemnitatis' : _('celebration with precedence over solemnities'),
                'tags'  => ['<B><I>', '</I></B>']
            ],
        };

        return $html ? $tags[0] . $grade . $tags[1] : $grade;
    }
}
