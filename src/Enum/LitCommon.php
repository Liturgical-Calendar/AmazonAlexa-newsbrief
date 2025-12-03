<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * Enumeration of possible values for the "Common" field of a festivity.
 *
 * The values represent different commons of saints used in the liturgy,
 * such as "Martyrs", "Pastors", "Virgins", etc.
 */
enum LitCommon: string
{
    use EnumToArrayTrait;

    case PROPRIO                                    = 'Proper';
    case DEDICATIONIS_ECCLESIAE                     = 'Dedication of a Church';
    case BEATAE_MARIAE_VIRGINIS                     = 'Blessed Virgin Mary';
    case MARTYRUM                                   = 'Martyrs';
    case PASTORUM                                   = 'Pastors';
    case DOCTORUM                                   = 'Doctors';
    case VIRGINUM                                   = 'Virgins';
    case SANCTORUM_ET_SANCTARUM                     = 'Holy Men and Women';

    // MARTYRUM
    case PRO_UNO_MARTYRE                            = 'For One Martyr';
    case PRO_PLURIBUS_MARTYRIBUS                    = 'For Several Martyrs';
    case PRO_MISSIONARIIS_MARTYRIBUS                = 'For Missionary Martyrs';
    case PRO_UNO_MISSIONARIO_MARTYRE                = 'For One Missionary Martyr';
    case PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS       = 'For Several Missionary Martyrs';
    case PRO_VIRGINE_MARTYRE                        = 'For a Virgin Martyr';
    case PRO_SANCTA_MULIERE_MARTYRE                 = 'For a Holy Woman Martyr';

    // PASTORUM
    case PRO_PAPA                                   = 'For a Pope';
    case PRO_EPISCOPO                               = 'For a Bishop';
    case PRO_UNO_PASTORE                            = 'For One Pastor';
    case PRO_PLURIBUS_PASTORIBUS                    = 'For Several Pastors';
    case PRO_FUNDATORIBUS_ECCLESIARUM               = 'For Founders of a Church';
    case PRO_UNO_FUNDATORE                          = 'For One Founder';
    case PRO_PLURIBUS_FUNDATORIBUS                  = 'For Several Founders';
    case PRO_MISSIONARIIS                           = 'For Missionaries';

    // VIRGINUM
    case PRO_UNA_VIRGINE                            = 'For One Virgin';
    case PRO_PLURIBUS_VIRGINIBUS                    = 'For Several Virgins';

    // SANCTORUM_ET_SANCTARUM
    case PRO_PLURIBUS_SANCTIS                       = 'For Several Saints';
    case PRO_UNO_SANCTO                             = 'For One Saint';
    case PRO_ABBATE                                 = 'For an Abbot';
    case PRO_MONACHO                                = 'For a Monk';
    case PRO_MONIALI                                = 'For a Nun';
    case PRO_RELIGIOSIS                             = 'For Religious';
    case PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT = 'For Those Who Practiced Works of Mercy';
    case PRO_EDUCATORIBUS                           = 'For Educators';
    case PRO_SANCTIS_MULIERIBUS                     = 'For Holy Women';

    case NONE = '';

    /**
     * Latin names of the Commons of Saints.
     *
     * @var array<string, string>
     */
    public const LATIN = [
        'PROPRIO'                                    => 'Proprio',
        'DEDICATIONIS_ECCLESIAE'                     => 'Dedicationis ecclesiæ',
        'BEATAE_MARIAE_VIRGINIS'                     => 'Beatæ Mariæ Virginis',
        'MARTYRUM'                                   => 'Martyrum',
        'PASTORUM'                                   => 'Pastorum',
        'DOCTORUM'                                   => 'Doctorum',
        'VIRGINUM'                                   => 'Virginum',
        'SANCTORUM_ET_SANCTARUM'                     => 'Sanctorum et Sanctarum',
        'PRO_UNO_MARTYRE'                            => 'Pro uno martyre',
        'PRO_PLURIBUS_MARTYRIBUS'                    => 'Pro pluribus martyribus',
        'PRO_MISSIONARIIS_MARTYRIBUS'                => 'Pro missionariis martyribus',
        'PRO_UNO_MISSIONARIO_MARTYRE'                => 'Pro uno missionario martyre',
        'PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS'       => 'Pro pluribus missionariis martyribus',
        'PRO_VIRGINE_MARTYRE'                        => 'Pro virgine martyre',
        'PRO_SANCTA_MULIERE_MARTYRE'                 => 'Pro sancta muliere martyre',
        'PRO_PAPA'                                   => 'Pro papa',
        'PRO_EPISCOPO'                               => 'Pro episcopo',
        'PRO_UNO_PASTORE'                            => 'Pro uno pastore',
        'PRO_PLURIBUS_PASTORIBUS'                    => 'Pro pluribus pastoribus',
        'PRO_FUNDATORIBUS_ECCLESIARUM'               => 'Pro fundatoribus ecclesiarum',
        'PRO_UNO_FUNDATORE'                          => 'Pro uno fundatore',
        'PRO_PLURIBUS_FUNDATORIBUS'                  => 'Pro pluribus fundatoribus',
        'PRO_MISSIONARIIS'                           => 'Pro missionariis',
        'PRO_UNA_VIRGINE'                            => 'Pro una virgine',
        'PRO_PLURIBUS_VIRGINIBUS'                    => 'Pro pluribus virginibus',
        'PRO_PLURIBUS_SANCTIS'                       => 'Pro pluribus sanctis',
        'PRO_UNO_SANCTO'                             => 'Pro uno sancto',
        'PRO_ABBATE'                                 => 'Pro abbate',
        'PRO_MONACHO'                                => 'Pro monacho',
        'PRO_MONIALI'                                => 'Pro moniali',
        'PRO_RELIGIOSIS'                             => 'Pro religiosis',
        'PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT' => 'Pro iis qui opera misericordiae exercuerunt',
        'PRO_EDUCATORIBUS'                           => 'Pro educatoribus',
        'PRO_SANCTIS_MULIERIBUS'                     => 'Pro sanctis mulieribus',
        'NONE'                                       => ''
    ];

    /**
     * General commons (top-level categories).
     *
     * @var array<LitCommon>
     */
    public const COMMUNES_GENERALIS = [
        self::PROPRIO,
        self::DEDICATIONIS_ECCLESIAE,
        self::BEATAE_MARIAE_VIRGINIS,
        self::MARTYRUM,
        self::PASTORUM,
        self::DOCTORUM,
        self::VIRGINUM,
        self::SANCTORUM_ET_SANCTARUM
    ];

    /**
     * Commons of Martyrs.
     *
     * @var array<LitCommon>
     */
    public const COMMUNE_MARTYRUM = [
        self::PRO_UNO_MARTYRE,
        self::PRO_PLURIBUS_MARTYRIBUS,
        self::PRO_MISSIONARIIS_MARTYRIBUS,
        self::PRO_UNO_MISSIONARIO_MARTYRE,
        self::PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS,
        self::PRO_VIRGINE_MARTYRE,
        self::PRO_SANCTA_MULIERE_MARTYRE
    ];

    /**
     * Commons of Pastors.
     *
     * @var array<LitCommon>
     */
    public const COMMUNE_PASTORUM = [
        self::PRO_PAPA,
        self::PRO_EPISCOPO,
        self::PRO_UNO_PASTORE,
        self::PRO_PLURIBUS_PASTORIBUS,
        self::PRO_FUNDATORIBUS_ECCLESIARUM,
        self::PRO_UNO_FUNDATORE,
        self::PRO_PLURIBUS_FUNDATORIBUS,
        self::PRO_MISSIONARIIS
    ];

    /**
     * Commons of Virgins.
     *
     * @var array<LitCommon>
     */
    public const COMMUNE_VIRGINUM = [
        self::PRO_UNA_VIRGINE,
        self::PRO_PLURIBUS_VIRGINIBUS
    ];

    /**
     * Commons of Holy Men and Women.
     *
     * @var array<LitCommon>
     */
    public const COMMUNE_SANCTORUM = [
        self::PRO_PLURIBUS_SANCTIS,
        self::PRO_UNO_SANCTO,
        self::PRO_ABBATE,
        self::PRO_MONACHO,
        self::PRO_MONIALI,
        self::PRO_RELIGIOSIS,
        self::PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT,
        self::PRO_EDUCATORIBUS,
        self::PRO_SANCTIS_MULIERIBUS
    ];

    /**
     * Translate the common to the given locale.
     *
     * @param string $locale The locale to translate to.
     * @return string The translated common name.
     */
    public function i18n(string $locale): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');
        if ($isLatin) {
            return self::LATIN[$this->name];
        }
        return match ($this) {
            self::PROPRIO                               => _('Proper'),
            /**translators: context = from the Common of nn */
            self::DEDICATIONIS_ECCLESIAE                => _('Dedication of a Church'),
            /**translators: context = from the Common of nn */
            self::BEATAE_MARIAE_VIRGINIS                => _('Blessed Virgin Mary'),
            /**translators: context = from the Common of nn */
            self::MARTYRUM                              => _('Martyrs'),
            /**translators: context = from the Common of nn */
            self::PASTORUM                              => _('Pastors'),
            /**translators: context = from the Common of nn */
            self::DOCTORUM                              => _('Doctors'),
            /**translators: context = from the Common of nn */
            self::VIRGINUM                              => _('Virgins'),
            /**translators: context = from the Common of nn */
            self::SANCTORUM_ET_SANCTARUM                => _('Holy Men and Women'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_MARTYRE                       => _('For One Martyr'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_MARTYRIBUS               => _('For Several Martyrs'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MISSIONARIIS_MARTYRIBUS           => _('For Missionary Martyrs'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_MISSIONARIO_MARTYRE           => _('For One Missionary Martyr'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_MISSIONARIIS_MARTYRIBUS  => _('For Several Missionary Martyrs'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_VIRGINE_MARTYRE                   => _('For a Virgin Martyr'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_SANCTA_MULIERE_MARTYRE            => _('For a Holy Woman Martyr'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PAPA                              => _('For a Pope'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_EPISCOPO                          => _('For a Bishop'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_PASTORE                       => _('For One Pastor'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_PASTORIBUS               => _('For Several Pastors'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_FUNDATORIBUS_ECCLESIARUM          => _('For Founders of a Church'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_FUNDATORE                     => _('For One Founder'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_FUNDATORIBUS             => _('For Several Founders'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MISSIONARIIS                      => _('For Missionaries'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNA_VIRGINE                       => _('For One Virgin'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_VIRGINIBUS               => _('For Several Virgins'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_PLURIBUS_SANCTIS                  => _('For Several Saints'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_UNO_SANCTO                        => _('For One Saint'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_ABBATE                            => _('For an Abbot'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MONACHO                           => _('For a Monk'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_MONIALI                           => _('For a Nun'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_RELIGIOSIS                        => _('For Religious'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_IIS_QUI_OPERA_MISERICORDIAE_EXERCUERUNT => _('For Those Who Practiced Works of Mercy'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_EDUCATORIBUS                      => _('For Educators'),
            /**translators: context = from the Common of nn: nn */
            self::PRO_SANCTIS_MULIERIBUS                => _('For Holy Women'),
            self::NONE                                  => '',
        };
    }

    /**
     * Get the possessive form for this common (glue between "From the Common" and the actual common).
     *
     * @param string $locale The locale.
     * @return string The possessive form.
     */
    public function possessive(string $locale): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');
        if ($isLatin) {
            return '';
        }
        return match ($this) {
            /**translators: (singular feminine) glue between "From the Common" and the actual common. Latin: leave empty! */
            self::BEATAE_MARIAE_VIRGINIS, self::DEDICATIONIS_ECCLESIAE => pgettext('(SING_FEMM)', 'of the'),
            /**translators: (plural feminine) glue between "From the Common" and the actual common. Latin: leave empty! */
            self::VIRGINUM => pgettext('(PLUR_FEMM)', 'of'),
            /**translators: (plural masculine) glue between "From the Common" and the actual common. Latin: leave empty! */
            self::MARTYRUM, self::PASTORUM, self::DOCTORUM, self::SANCTORUM_ET_SANCTARUM => pgettext('(PLUR_MASC)', 'of'),
            /**translators: (singular masculine) glue between "From the Common" and the actual common. Latin: leave empty! */
            default => pgettext('(SING_MASC)', 'of the'),
        };
    }

    /**
     * Validates a common string value, which may contain a colon separator.
     *
     * @param string $value The value to validate (e.g., "Martyrs:For One Martyr").
     * @return bool True if valid.
     */
    public static function isValidCommon(string $value): bool
    {
        if (strpos($value, ':') !== false) {
            $parts = explode(':', $value);
            foreach ($parts as $part) {
                if (!self::isValid($part)) {
                    return false;
                }
            }
            return true;
        }
        return self::isValid($value);
    }

    /**
     * Validates an array of common string values.
     *
     * @param array<string> $values The values to validate.
     * @return bool True if all values are valid.
     */
    public static function areValidCommons(array $values): bool
    {
        foreach ($values as $value) {
            if (!self::isValidCommon($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a translated human readable string of the Common or the Proper.
     *
     * @param array<string> $commons The common values from the API.
     * @param string $locale The locale for translation.
     * @return string The human readable string.
     */
    public static function toReadableString(array $commons, string $locale): string
    {
        if (count($commons) === 0) {
            return '';
        }

        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');

        // Check for Proper
        if (in_array(self::PROPRIO->value, $commons, true)) {
            return $isLatin ? 'De Proprio' : _('From the Proper of the festivity');
        }

        $translatedCommons = array_map(function (string $txt) use ($locale, $isLatin): string {
            if (strpos($txt, ':') !== false) {
                [$commonGeneralStr, $commonSpecificStr] = explode(':', $txt);
                $commonGeneral = self::tryFrom($commonGeneralStr);
                $commonSpecific = self::tryFrom($commonSpecificStr);
            } else {
                $commonGeneral = self::tryFrom($txt);
                $commonSpecific = null;
            }

            $fromTheCommon = $isLatin ? 'De Commune' : _('From the Common');

            if ($commonGeneral === null) {
                return $fromTheCommon . ' ' . $txt;
            }

            $result = $fromTheCommon . ' ' . $commonGeneral->possessive($locale) . ' ' . $commonGeneral->i18n($locale);

            if ($commonSpecific !== null) {
                return $result . ': ' . $commonSpecific->i18n($locale);
            }

            return $result;
        }, $commons);

        /**translators: when there are multiple possible commons, this will be the glue "or from the common of..." */
        return implode('; ' . _('or') . ' ', $translatedCommons);
    }
}
