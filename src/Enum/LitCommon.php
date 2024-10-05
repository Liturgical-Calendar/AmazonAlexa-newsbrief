<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitLocale;

/**
 * An enumeration of possible values for the "Common" field of a festivity.
 * The $values array of a "Common" field must contain only values from the $values array
 *  in the \LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon class.
 */
class LitCommon
{
    public const PROPRIO                   = "Proper";
    public const DEDICATIONIS_ECCLESIAE    = "Dedication of a Church";
    public const BEATAE_MARIAE_VIRGINIS    = "Blessed Virgin Mary";
    public const MARTYRUM                  = "Martyrs";
    public const PASTORUM                  = "Pastors";
    public const DOCTORUM                  = "Doctors";
    public const VIRGINUM                  = "Virgins";
    public const SANCTORUM_ET_SANCTARUM    = "Holy Men and Women";

    /** MARTYRUM */
    public const PRO_UNO_MARTYRE                       = "For One Martyr";
    public const PRO_PLURIBUS_MARTYRIBUS               = "For Several Martyrs";
    public const PRO_MISSIONARIIS_MARTYRIBUS           = "For Missionary Martyrs";
    public const PRO_UNO_MISSIONARIO_MARTYRE           = "For One Missionary Martyr";
    public const PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS  = "For Several Missionary Martyrs";
    public const PRO_VIRGINE_MARTYRE                   = "For a Virgin Martyr";
    public const PRO_SANCTA_MULIERE_MARTYRE            = "For a Holy Woman Martyr";

    /** PASTORUM */
    public const PRO_PAPA                              = "For a Pope";
    public const PRO_EPISCOPO                          = "For a Bishop";
    public const PRO_UNO_PASTORE                       = "For One Pastor";
    public const PRO_PLURIBUS_PASTORIBUS               = "For Several Pastors";
    public const PRO_FUNDATORIBUS_ECCLESIARUM          = "For Founders of a Church";
    public const PRO_UNO_FUNDATORE                     = "For One Founder";
    public const PRO_PLURIBUS_FUNDATORIBUS             = "For Several Founders";
    public const PRO_MISSIONARIIS                      = "For Missionaries";

    /** VIRGINUM */
    public const PRO_UNA_VIRGINE                       = "For One Virgin";
    public const PRO_PLURIBUS_VIRGINIBUS               = "For Several Virgins";

    /** SANCTORUM_ET_SANCTARUM */
    public const PRO_PLURIBUS_SANCTIS                  = "For Several Saints";
    public const PRO_UNO_SANCTO                        = "For One Saint";
    public const PRO_ABBATE                            = "For an Abbot";
    public const PRO_MONACHO                           = "For a Monk";
    public const PRO_MONIALI                           = "For a Nun";
    public const PRO_RELIGIOSIS                        = "For Religious";
    public const PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT = "For Those Who Practiced Works of Mercy";
    public const PRO_EDUCATORIBUS                      = "For Educators";
    public const PRO_SANCTIS_MULIERIBUS                = "For Holy Women";

    private string $locale;
    private array $GTXT;

    /**
     * Construct a new LiturgicalCalendar\NewsBrief\Enum\LitCommon object
     *
     * @param string $locale The locale to use for the object
     */
    public function __construct(string $locale)
    {
        $this->locale = strtoupper($locale);
        $this->GTXT = [
            self::PROPRIO                           => _("Proper"),
            /**translators: context = from the Common of nn */
            self::DEDICATIONIS_ECCLESIAE            => _("Dedication of a Church"),
            /**translators: context = from the Common of nn */
            self::BEATAE_MARIAE_VIRGINIS            => _("Blessed Virgin Mary"),
            /**translators: context = from the Common of nn */
            self::MARTYRUM                          => _("Martyrs"),
            /**translators: context = from the Common of nn */
            self::PASTORUM                          => _("Pastors"),
            /**translators: context = from the Common of nn */
            self::DOCTORUM                          => _("Doctors"),
            /**translators: context = from the Common of nn */
            self::VIRGINUM                          => _("Virgins"),
            /**translators: context = from the Common of nn */
            self::SANCTORUM_ET_SANCTARUM            => _("Holy Men and Women"),

            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_MARTYRE                       => _("For One Martyr"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_MARTYRIBUS               => _("For Several Martyrs"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MISSIONARIIS_MARTYRIBUS           => _("For Missionary Martyrs"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_MISSIONARIO_MARTYRE           => _("For One Missionary Martyr"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS  => _("For Several Missionary Martyrs"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_VIRGINE_MARTYRE                   => _("For a Virgin Martyr"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_SANCTA_MULIERE_MARTYRE            => _("For a Holy Woman Martyr"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PAPA                              => _("For a Pope"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_EPISCOPO                          => _("For a Bishop"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_PASTORE                       => _("For One Pastor"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_PASTORIBUS               => _("For Several Pastors"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_FUNDATORIBUS_ECCLESIARUM          => _("For Founders of a Church"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_FUNDATORE                     => _("For One Founder"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_FUNDATORIBUS             => _("For Several Founders"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MISSIONARIIS                      => _("For Missionaries"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNA_VIRGINE                       => _("For One Virgin"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_VIRGINIBUS               => _("For Several Virgins"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_SANCTIS                  => _("For Several Saints"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_SANCTO                        => _("For One Saint"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_ABBATE                            => _("For an Abbot"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MONACHO                           => _("For a Monk"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MONIALI                           => _("For a Nun"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_RELIGIOSIS                        => _("For Religious"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT => _("For Those Who Practiced Works of Mercy"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_EDUCATORIBUS                      => _("For Educators"),
            /**translators: context = from the Common of nn: nn */
            self::PRO_SANCTIS_MULIERIBUS                => _("For Holy Women")
        ];
    }

    /**
     * Latin names of the Commons of Saints.
     *
     * Each key is a constant from this class, and the value is the Latin name
     * associated with that constant.
     *
     * @var array<string,string>
     */
    public const LATIN = [
        self::PROPRIO                               => "Proprio",
        self::DEDICATIONIS_ECCLESIAE                => "Dedicationis ecclesiæ",
        self::BEATAE_MARIAE_VIRGINIS                => "Beatæ Mariæ Virginis",
        self::MARTYRUM                              => "Martyrum",
        self::PASTORUM                              => "Pastorum",
        self::DOCTORUM                              => "Doctorum",
        self::VIRGINUM                              => "Virginum",
        self::SANCTORUM_ET_SANCTARUM                => "Sanctorum et Sanctarum",
        self::PRO_UNO_MARTYRE                       => "Pro uno martyre",
        self::PRO_PLURIBUS_MARTYRIBUS               => "Pro pluribus martyribus",
        self::PRO_MISSIONARIIS_MARTYRIBUS           => "Pro missionariis martyribus",
        self::PRO_UNO_MISSIONARIO_MARTYRE           => "Pro uno missionario martyre",
        self::PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS  => "Pro pluribus missionariis martyribus",
        self::PRO_VIRGINE_MARTYRE                   => "Pro virgine martyre",
        self::PRO_SANCTA_MULIERE_MARTYRE            => "Pro sancta muliere martyre",
        self::PRO_PAPA                              => "Pro papa",
        self::PRO_EPISCOPO                          => "Pro episcopo",
        self::PRO_UNO_PASTORE                       => "Pro uno pastore",
        self::PRO_PLURIBUS_PASTORIBUS               => "Pro pluribus pastoribus",
        self::PRO_FUNDATORIBUS_ECCLESIARUM          => "Pro fundatoribus ecclesiarum",
        self::PRO_UNO_FUNDATORE                     => "Pro uno fundatore",
        self::PRO_PLURIBUS_FUNDATORIBUS             => "Pro pluribus fundatoribus",
        self::PRO_MISSIONARIIS                      => "Pro missionariis",
        self::PRO_UNA_VIRGINE                       => "Pro una virgine",
        self::PRO_PLURIBUS_VIRGINIBUS               => "Pro pluribus virginibus",
        self::PRO_PLURIBUS_SANCTIS                  => "Pro pluribus sanctis",
        self::PRO_UNO_SANCTO                        => "Pro uno sancto",
        self::PRO_ABBATE                            => "Pro abbate",
        self::PRO_MONACHO                           => "Pro monacho",
        self::PRO_MONIALI                           => "Pro moniali",
        self::PRO_RELIGIOSIS                        => "Pro religiosis",
        self::PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT => "Pro iis qui opera misericordiae exercuerunt",
        self::PRO_EDUCATORIBUS                      => "Pro educatoribus",
        self::PRO_SANCTIS_MULIERIBUS                => "Pro sanctis mulieribus"
    ];

    /**
     * @param string $value
     * @return string
     *
     * Returns glue string for use between "From the Common" and the actual common.
     * If the value is "Blessed Virgin Mary", returns "of the".
     * If the value is "Virgins", returns "of".
     * If the value is one of "Martyrs", "Pastors", "Doctors", "Holy Men and Women",
     * returns "of".
     * If the value is "Dedication of a Church", returns "of the".
     * Otherwise, returns "of the".
     */

    public static function possessive(string $value): string
    {
        switch ($value) {
            case "Blessed Virgin Mary":
                /**translators: (singular feminine) glue between "From the Common" and the actual common. Latin: leave empty! */
                return pgettext("(SING_FEMM)", "of the");
            case "Virgins":
                /**translators: (plural feminine) glue between "From the Common" and the actual common. Latin: leave empty! */
                return pgettext("(PLUR_FEMM)", "of");
            case "Martyrs":
            case "Pastors":
            case "Doctors":
            case "Holy Men and Women":
                /**translators: (plural masculine) glue between "From the Common" and the actual common. Latin: leave empty! */
                return pgettext("(PLUR_MASC)", "of");
            case "Dedication of a Church":
                /**translators: (singular feminine) glue between "From the Common" and the actual common. Latin: leave empty! */
                return pgettext("(SING_FEMM)", "of the");
            default:
                /**translators: (singular masculine) glue between "From the Common" and the actual common. Latin: leave empty! */
                return pgettext("(SING_MASC)", "of the");
        }
    }

    /**
     * List of possible values for the "Common" field of a festivity.
     * These values are used in the "Common" field of a festivity,
     * and are also used as the key in the associative array returned by the
     * {@see i18n()} and {@see getPossessive()} methods.
     * @var string[]
     */
    public static array $values = [
        "Proper",
        "Dedication of a Church",
        "Blessed Virgin Mary",
        "Martyrs",
        "Pastors",
        "Doctors",
        "Virgins",
        "Holy Men and Women",
        "For One Martyr",
        "For Several Martyrs",
        "For Missionary Martyrs",
        "For One Missionary Martyr",
        "For Several Missionary Martyrs",
        "For a Virgin Martyr",
        "For a Holy Woman Martyr",
        "For a Pope",
        "For a Bishop",
        "For One Pastor",
        "For Several Pastors",
        "For Founders of a Church",
        "For One Founder",
        "For Several Founders",
        "For Missionaries",
        "For One Virgin",
        "For Several Virgins",
        "For Several Saints",
        "For One Saint",
        "For an Abbot",
        "For a Monk",
        "For a Nun",
        "For Religious",
        "For Those Who Practiced Works of Mercy",
        "For Educators",
        "For Holy Women"
    ];

    /**
     * Determines if the given value is a valid "Common" value.
     * This method can handle a single value, or a comma-separated list of values.
     * If the value contains a colon (:), it is split into separate values.
     * @param string $value The value to test.
     * @return bool True if the value is valid, otherwise false.
     */
    public static function isValid(string $value)
    {
        if (strpos($value, ',') || strpos($value, ':')) {
            $values = preg_split('/[,:]/', $value);
            return self::areValid($values);
        }
        return in_array($value, self::$values);
    }

    /**
     * Determines if all of the given values are valid "Common" values.
     * This method can handle an array of values, or a comma-separated list of values.
     * If the value contains a colon (:), it is split into separate values.
     * @param array<string> $values The values to test.
     * @return bool True if all of the values are valid, otherwise false.
     */
    public static function areValid(array $values)
    {
        $values = array_reduce($values, function ($carry, $key) {
            return strpos($key, ':') ? ( $carry + explode(':', $key) ) : ( [ ...$carry, $key ] );
        }, []);
        return empty(array_diff($values, self::$values));
    }

    /**
     * Translate the given value or values into the current locale.
     * If the value is an array, each element of the array is translated.
     * If the value is a string, it is assumed to be a valid "Common" value.
     * If the value is not a valid "Common" value, it is returned unchanged.
     * @param string|array $value The value or values to translate.
     * @return string|array The translated value or values.
     */
    public function i18n(string|array $value): string|array
    {
        if (is_array($value) && self::areValid($value)) {
            return array_map([$this, 'i18n'], $value);
        } elseif (self::isValid($value)) {
            if ($this->locale === LitLocale::LATIN) {
                return self::LATIN[ $value ];
            } else {
                return $this->GTXT[ $value ];
            }
        }
        return $value;
    }

    /**
     * If the locale is Latin, returns an empty string.
     * Otherwise returns the possessive form of the given string,
     * according to the rules defined in the possessive() method.
     * If the given string is an array, applies the same rules to each element of the array.
     * @param string|array $value the string or array of strings to get the possessive of
     * @return string|array the possessive form of the given string, or an array of such strings
     */
    public function getPossessive(string|array $value): string|array
    {
        if (is_array($value)) {
            return array_map([$this, 'getPossessive'], $value);
        }
        return $this->locale === LitLocale::LATIN ? "" : self::possessive($value);
    }

    /**
     * Returns a translated human readable string of the Common or the Proper
     * @param string|array $common the Common or the Proper to return the human readable string for
     * @return string|array the human readable string, or an array of such strings
     */
    public function c(string|array $common = ""): string|array
    {
        if (( is_string($common) && $common !== "" ) || is_array($common)) {
            if ((is_string($common) && $common === LitCommon::PROPRIO) || ( is_array($common) && in_array(LitCommon::PROPRIO, $common) )) {
                $common = $this->locale === LitLocale::LATIN ? "De Proprio" : _("From the Proper of the festivity");
            } else {
                if (is_string($common)) {
                    $commons = explode(",", $common);
                } else {
                    $commons = $common;
                }
                $commons = array_map(function ($txt) {
                    if (strpos($txt, ":") !== false) {
                        [$commonGeneral, $commonSpecific] = explode(":", $txt);
                    } else {
                        $commonGeneral = $txt;
                        $commonSpecific = "";
                    }
                    $fromTheCommon = $this->locale === LitLocale::LATIN ? "De Commune" : _("From the Common");
                    return $fromTheCommon . " " . $this->getPossessive($commonGeneral) . " " . $this->i18n($commonGeneral) . ($commonSpecific != "" ? ": " . $this->i18n($commonSpecific) : "");
                }, $commons);
                /**translators: when there are multiple possible commons, this will be the glue "or from the common of..." */
                $common = implode("; " . _("or") . " ", $commons);
            }
        }
        return $common;
    }
}
