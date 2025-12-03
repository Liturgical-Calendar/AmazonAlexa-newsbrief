<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * Enumeration of Masses and Prayers for Various Needs and Occasions.
 *
 * These are liturgical celebrations that can be used on days when there is no
 * obligatory memorial, feast, or solemnity, and when pastoral need suggests them.
 */
enum LitMassVariousNeeds: string
{
    use EnumToArrayTrait;

    // I. Pro sancta Ecclesia
    case PRO_ECCLESIA                                      = 'For the Church';
    case PRO_PAPA                                          = 'For the Pope';
    case PRO_EPISCOPO                                      = 'For the Bishop';
    case PRO_ELIGENDO_PAPA_VEL_EPISCOPO                    = 'For the Election of a Pope or a Bishop';
    case PRO_CONCILIO_VEL_SYNODO                           = 'For a Council or a Synod';
    case PRO_SACERDOTIBUS                                  = 'For Priests';
    case PRO_SEIPSO_SACERDOTE                              = 'For the Priest Himself';
    case IN_ANNIVERSARIO_PROPRIAE_ORDINATIONIS             = 'On the Anniversary of His Ordination';
    case PRO_MINISTRIS_ECCLESIAE                           = 'For Ministers of the Church';
    case PRO_VOCATIONIBUS_AD_SACROS_ORDINES                = 'For Vocations to Holy Orders';
    case PRO_LAICIS                                        = 'For the Laity';
    case IN_ANNIVERSARIIS_MATRIMONII_IN_ANNIVERSARIO       = 'On the Anniversaries of Marriage: On Any Anniversary';
    case IN_ANNIVERSARIIS_MATRIMONII_IN_XXV_ANNIVERSARIO   = 'On the Anniversaries of Marriage: On the Twenty-Fifth Anniversary';
    case IN_ANNIVERSARIIS_MATRIMONII_IN_L_ANNIVERSARIO     = 'On the Anniversaries of Marriage: On the Fiftieth Anniversary';
    case PRO_FAMILIA                                       = 'For the Family';
    case PRO_RELIGIOSIS                                    = 'For Religious';
    case IN_XXV_VEL_L_ANNIVERSARIO_PROFESSIONIS_RELIGIOSAE = 'On the Twenty-Fifth or Fiftieth Anniversary of Religious Profession';
    case PRO_VOCATIONIBUS_AD_VITAM_RELIGIOSAM              = 'For Vocations to Religious Life';
    case PRO_CONCORDIA_FOVENDA                             = 'For Promoting Harmony';
    case PRO_RECONCILIATIONE                               = 'For Reconciliation';
    case PRO_UNITATE_CHRISTIANORUM                         = 'For the Unity of Christians';
    case PRO_EVANGELIZATIONE_POPULORUM                     = 'For the Evangelization of Peoples';
    case PRO_CHRISTIANIS_PERSECUTIONE_VEXATIS              = 'For Persecuted Christians';
    case IN_CONVENTU_SPIRITUALI_VEL_PASTORALI              = 'For a Spiritual or Pastoral Gathering';

    // II. Pro circumstantiis publicis
    case PRO_PATRIA_VEL_CIVITATE                  = 'For the Nation or State';
    case PRO_REM_PUBLICAM_MODERANTIBUS            = 'For Those in Public Office';
    case PRO_COETU_MODERATORUM_NATIONUM           = 'For a Governing Assembly';
    case PRO_SUPREMO_NATIONIS_MODERATORE_VEL_REGE = 'For the Head of State or Ruler';
    case INITIO_ANNI_CIVILIS                      = 'At the Beginning of the Civil Year';
    case PRO_HUMANO_LABORE_SANCTIFICANDO          = 'For the Sanctification of Human Labor';
    case IN_AGRIS_CONSERENDIS                     = 'At Seedtime';
    case POST_COLLECTOS_FRUCTUS_TERRAE            = 'After the Harvest';
    case PRO_POPULORUM_PROGRESSIONE               = 'For the Progress of Peoples';
    case PRO_PACE_ET_IUSTITIA_SERVANDA            = 'For the Preservation of Peace and Justice';
    case TEMPORE_BELLI_VEL_EVERSIONIS             = 'In Time of War or Civil Disturbance';
    case PRO_PROFUGIS_ET_EXSULIBUS                = 'For Refugees and Exiles';
    case TEMPORE_FAMIS_VEL_PRO_FAME_LABORANTIBUS  = 'In Time of Famine or for Those Suffering Hunger';
    case TEMPORE_TERRAEMOTUS                      = 'In Time of Earthquake';
    case AD_PETENDAM_PLUVIAM                      = 'For Rain';
    case AD_POSTULANDAM_AERIS_SERENITATEM         = 'For Fine Weather';
    case AD_REPELLENDAS_TEMPESTATES               = 'For an End to Storms';

    // III. Ad diversa
    case PRO_REMISSIONE_PECCATORUM                           = 'For the Forgiveness of Sins';
    case AD_POSTULANDAM_CONTINENTIAM                         = 'For Chastity';
    case AD_POSTULANDAM_CARITATEM                            = 'For Charity';
    case PRO_FAMILIARIBUS_ET_AMICIS                          = 'For Relatives and Friends';
    case PRO_AFFLIGENTIBUS_NOS                               = 'For Our Oppressors';
    case PRO_CAPTIVITATE_DETENTIS                            = 'For Those Held in Captivity';
    case PRO_DETENTIS_IN_CARCERE                             = 'For Those in Prison';
    case PRO_INFIRMIS                                        = 'For the Sick';
    case PRO_MORIENTIBUS                                     = 'For the Dying';
    case AD_POSTULANDAM_GRATIAM_BENE_MORIENDI                = 'For the Grace of a Happy Death';
    case IN_QUACUMQUE_NECESSITATE                            = 'In Any Need';
    case GIVING_THANKS_TO_GOD_FOR_THE_GIFT_OF_HUMAN_LIFE_USA = 'For Giving Thanks to God for the Gift of Human Life [USA]';
    case PRO_GRATIIS_DEO_REDDENDIS                           = 'For Giving Thanks to God';

    /**
     * Masses for the Holy Church (I. Pro sancta Ecclesia).
     *
     * @var array<LitMassVariousNeeds>
     */
    public const PRO_SANCTA_ECCLESIA = [
        self::PRO_ECCLESIA,
        self::PRO_PAPA,
        self::PRO_EPISCOPO,
        self::PRO_ELIGENDO_PAPA_VEL_EPISCOPO,
        self::PRO_CONCILIO_VEL_SYNODO,
        self::PRO_SACERDOTIBUS,
        self::PRO_SEIPSO_SACERDOTE,
        self::IN_ANNIVERSARIO_PROPRIAE_ORDINATIONIS,
        self::PRO_MINISTRIS_ECCLESIAE,
        self::PRO_VOCATIONIBUS_AD_SACROS_ORDINES,
        self::PRO_LAICIS,
        self::IN_ANNIVERSARIIS_MATRIMONII_IN_ANNIVERSARIO,
        self::IN_ANNIVERSARIIS_MATRIMONII_IN_XXV_ANNIVERSARIO,
        self::IN_ANNIVERSARIIS_MATRIMONII_IN_L_ANNIVERSARIO,
        self::PRO_FAMILIA,
        self::PRO_RELIGIOSIS,
        self::IN_XXV_VEL_L_ANNIVERSARIO_PROFESSIONIS_RELIGIOSAE,
        self::PRO_VOCATIONIBUS_AD_VITAM_RELIGIOSAM,
        self::PRO_CONCORDIA_FOVENDA,
        self::PRO_RECONCILIATIONE,
        self::PRO_UNITATE_CHRISTIANORUM,
        self::PRO_EVANGELIZATIONE_POPULORUM,
        self::PRO_CHRISTIANIS_PERSECUTIONE_VEXATIS,
        self::IN_CONVENTU_SPIRITUALI_VEL_PASTORALI
    ];

    /**
     * Masses for Public Circumstances (II. Pro circumstantiis publicis).
     *
     * @var array<LitMassVariousNeeds>
     */
    public const PRO_CIRCUMSTANTIIS_PUBLICIS = [
        self::PRO_PATRIA_VEL_CIVITATE,
        self::PRO_REM_PUBLICAM_MODERANTIBUS,
        self::PRO_COETU_MODERATORUM_NATIONUM,
        self::PRO_SUPREMO_NATIONIS_MODERATORE_VEL_REGE,
        self::INITIO_ANNI_CIVILIS,
        self::PRO_HUMANO_LABORE_SANCTIFICANDO,
        self::IN_AGRIS_CONSERENDIS,
        self::POST_COLLECTOS_FRUCTUS_TERRAE,
        self::PRO_POPULORUM_PROGRESSIONE,
        self::PRO_PACE_ET_IUSTITIA_SERVANDA,
        self::TEMPORE_BELLI_VEL_EVERSIONIS,
        self::PRO_PROFUGIS_ET_EXSULIBUS,
        self::TEMPORE_FAMIS_VEL_PRO_FAME_LABORANTIBUS,
        self::TEMPORE_TERRAEMOTUS,
        self::AD_PETENDAM_PLUVIAM,
        self::AD_POSTULANDAM_AERIS_SERENITATEM,
        self::AD_REPELLENDAS_TEMPESTATES
    ];

    /**
     * Masses for Various Needs (III. Ad diversa).
     *
     * @var array<LitMassVariousNeeds>
     */
    public const AD_DIVERSA = [
        self::PRO_REMISSIONE_PECCATORUM,
        self::AD_POSTULANDAM_CONTINENTIAM,
        self::AD_POSTULANDAM_CARITATEM,
        self::PRO_FAMILIARIBUS_ET_AMICIS,
        self::PRO_AFFLIGENTIBUS_NOS,
        self::PRO_CAPTIVITATE_DETENTIS,
        self::PRO_DETENTIS_IN_CARCERE,
        self::PRO_INFIRMIS,
        self::PRO_MORIENTIBUS,
        self::AD_POSTULANDAM_GRATIAM_BENE_MORIENDI,
        self::IN_QUACUMQUE_NECESSITATE,
        self::GIVING_THANKS_TO_GOD_FOR_THE_GIFT_OF_HUMAN_LIFE_USA,
        self::PRO_GRATIIS_DEO_REDDENDIS
    ];

    /**
     * Latin values of the Masses and Prayers for Various Needs and Occasions.
     *
     * @var array<string, string>
     */
    public const LATIN = [
        'PRO_ECCLESIA'                                        => 'Pro Ecclesia',
        'PRO_PAPA'                                            => 'Pro Papa',
        'PRO_EPISCOPO'                                        => 'Pro Episcopo',
        'PRO_ELIGENDO_PAPA_VEL_EPISCOPO'                      => 'Pro eligendo Papa vel Episcopo',
        'PRO_CONCILIO_VEL_SYNODO'                             => 'Pro Concilio vel Synodo',
        'PRO_SACERDOTIBUS'                                    => 'Pro sacerdotibus',
        'PRO_SEIPSO_SACERDOTE'                                => 'Pro seipso sacerdote',
        'IN_ANNIVERSARIO_PROPRIAE_ORDINATIONIS'               => 'In anniversario propriae ordinationis',
        'PRO_MINISTRIS_ECCLESIAE'                             => 'Pro ministris Ecclesiae',
        'PRO_VOCATIONIBUS_AD_SACROS_ORDINES'                  => 'Pro vocationibus ad sacros Ordines',
        'PRO_LAICIS'                                          => 'Pro laicis',
        'IN_ANNIVERSARIIS_MATRIMONII_IN_ANNIVERSARIO'         => 'In anniversariis matrimonii: In anniversario',
        'IN_ANNIVERSARIIS_MATRIMONII_IN_XXV_ANNIVERSARIO'     => 'In anniversariis matrimonii: In XXV anniversario',
        'IN_ANNIVERSARIIS_MATRIMONII_IN_L_ANNIVERSARIO'       => 'In anniversariis matrimonii: In L anniversario',
        'PRO_FAMILIA'                                         => 'Pro familia',
        'PRO_RELIGIOSIS'                                      => 'Pro religiosis',
        'IN_XXV_VEL_L_ANNIVERSARIO_PROFESSIONIS_RELIGIOSAE'   => 'In XXV vel L anniversario professionis religiosae',
        'PRO_VOCATIONIBUS_AD_VITAM_RELIGIOSAM'                => 'Pro vocationibus ad vitam religiosam',
        'PRO_CONCORDIA_FOVENDA'                               => 'Pro concordia fovenda',
        'PRO_RECONCILIATIONE'                                 => 'Pro reconciliatione',
        'PRO_UNITATE_CHRISTIANORUM'                           => 'Pro unitate christianorum',
        'PRO_EVANGELIZATIONE_POPULORUM'                       => 'Pro evangelizatione populorum',
        'PRO_CHRISTIANIS_PERSECUTIONE_VEXATIS'                => 'Pro christianis persecutione vexatis',
        'IN_CONVENTU_SPIRITUALI_VEL_PASTORALI'                => 'In conventu spirituali vel pastorali',
        'PRO_PATRIA_VEL_CIVITATE'                             => 'Pro patria vel civitate',
        'PRO_REM_PUBLICAM_MODERANTIBUS'                       => 'Pro rem publicam moderantibus',
        'PRO_COETU_MODERATORUM_NATIONUM'                      => 'Pro coetu moderatorum nationum',
        'PRO_SUPREMO_NATIONIS_MODERATORE_VEL_REGE'            => 'Pro supremo nationis moderatore vel rege',
        'INITIO_ANNI_CIVILIS'                                 => 'Initio anni civilis',
        'PRO_HUMANO_LABORE_SANCTIFICANDO'                     => 'Pro humano labore sanctificando',
        'IN_AGRIS_CONSERENDIS'                                => 'In agris conserendis',
        'POST_COLLECTOS_FRUCTUS_TERRAE'                       => 'Post collectos fructus terrae',
        'PRO_POPULORUM_PROGRESSIONE'                          => 'Pro populorum progressione',
        'PRO_PACE_ET_IUSTITIA_SERVANDA'                       => 'Pro pace et iustitia servanda',
        'TEMPORE_BELLI_VEL_EVERSIONIS'                        => 'Tempore belli vel eversionis',
        'PRO_PROFUGIS_ET_EXSULIBUS'                           => 'Pro profugis et exsulibus',
        'TEMPORE_FAMIS_VEL_PRO_FAME_LABORANTIBUS'             => 'Tempore famis, vel pro fame laborantibus',
        'TEMPORE_TERRAEMOTUS'                                 => 'Tempore terraemotus',
        'AD_PETENDAM_PLUVIAM'                                 => 'Ad petendam pluviam',
        'AD_POSTULANDAM_AERIS_SERENITATEM'                    => 'Ad postulandam aeris serenitatem',
        'AD_REPELLENDAS_TEMPESTATES'                          => 'Ad repellendas tempestates',
        'PRO_REMISSIONE_PECCATORUM'                           => 'Pro remissione peccatorum',
        'AD_POSTULANDAM_CONTINENTIAM'                         => 'Ad postulandam continentiam',
        'AD_POSTULANDAM_CARITATEM'                            => 'Ad postulandam caritatem',
        'PRO_FAMILIARIBUS_ET_AMICIS'                          => 'Pro familiaribus et amicis',
        'PRO_AFFLIGENTIBUS_NOS'                               => 'Pro affligentibus nos',
        'PRO_CAPTIVITATE_DETENTIS'                            => 'Pro captivitate detentis',
        'PRO_DETENTIS_IN_CARCERE'                             => 'Pro detentis in carcere',
        'PRO_INFIRMIS'                                        => 'Pro infirmis',
        'PRO_MORIENTIBUS'                                     => 'Pro morientibus',
        'AD_POSTULANDAM_GRATIAM_BENE_MORIENDI'                => 'Ad postulandam gratiam bene moriendi',
        'IN_QUACUMQUE_NECESSITATE'                            => 'In quacumque necessitate',
        // Intentionally English, as this is a USA-specific category
        'GIVING_THANKS_TO_GOD_FOR_THE_GIFT_OF_HUMAN_LIFE_USA' => 'For Giving Thanks to God for the Gift of Human Life [USA]',
        'PRO_GRATIIS_DEO_REDDENDIS'                           => 'Pro gratiis Deo reddendis'
    ];

    /**
     * Translate the Mass for Various Needs to the given locale.
     *
     * @param string $locale The locale to translate to.
     * @return string The translated name.
     */
    public function i18n(string $locale): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');
        if ($isLatin) {
            return self::LATIN[$this->name];
        }
        return match ($this) {
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_ECCLESIA => _('For the Church'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_PAPA => _('For the Pope'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_EPISCOPO => _('For the Bishop'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_ELIGENDO_PAPA_VEL_EPISCOPO => _('For the Election of a Pope or a Bishop'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_CONCILIO_VEL_SYNODO => _('For a Council or a Synod'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_SACERDOTIBUS => _('For Priests'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_SEIPSO_SACERDOTE => _('For the Priest Himself'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_ANNIVERSARIO_PROPRIAE_ORDINATIONIS => _('On the Anniversary of His Ordination'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_MINISTRIS_ECCLESIAE => _('For Ministers of the Church'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_VOCATIONIBUS_AD_SACROS_ORDINES => _('For Vocations to Holy Orders'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_LAICIS => _('For the Laity'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_ANNIVERSARIIS_MATRIMONII_IN_ANNIVERSARIO => _('On the Anniversaries of Marriage: On Any Anniversary'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_ANNIVERSARIIS_MATRIMONII_IN_XXV_ANNIVERSARIO => _('On the Anniversaries of Marriage: On the Twenty-Fifth Anniversary'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_ANNIVERSARIIS_MATRIMONII_IN_L_ANNIVERSARIO => _('On the Anniversaries of Marriage: On the Fiftieth Anniversary'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_FAMILIA => _('For the Family'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_RELIGIOSIS => _('For Religious'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_XXV_VEL_L_ANNIVERSARIO_PROFESSIONIS_RELIGIOSAE => _('On the Twenty-Fifth or Fiftieth Anniversary of Religious Profession'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_VOCATIONIBUS_AD_VITAM_RELIGIOSAM => _('For Vocations to Religious Life'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_CONCORDIA_FOVENDA => _('For Promoting Harmony'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_RECONCILIATIONE => _('For Reconciliation'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_UNITATE_CHRISTIANORUM => _('For the Unity of Christians'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_EVANGELIZATIONE_POPULORUM => _('For the Evangelization of Peoples'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_CHRISTIANIS_PERSECUTIONE_VEXATIS => _('For Persecuted Christians'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_CONVENTU_SPIRITUALI_VEL_PASTORALI => _('For a Spiritual or Pastoral Gathering'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_PATRIA_VEL_CIVITATE => _('For the Nation or State'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_REM_PUBLICAM_MODERANTIBUS => _('For Those in Public Office'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_COETU_MODERATORUM_NATIONUM => _('For a Governing Assembly'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_SUPREMO_NATIONIS_MODERATORE_VEL_REGE => _('For the Head of State or Ruler'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::INITIO_ANNI_CIVILIS => _('At the Beginning of the Civil Year'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_HUMANO_LABORE_SANCTIFICANDO => _('For the Sanctification of Human Labor'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_AGRIS_CONSERENDIS => _('At Seedtime'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::POST_COLLECTOS_FRUCTUS_TERRAE => _('After the Harvest'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_POPULORUM_PROGRESSIONE => _('For the Progress of Peoples'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_PACE_ET_IUSTITIA_SERVANDA => _('For the Preservation of Peace and Justice'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::TEMPORE_BELLI_VEL_EVERSIONIS => _('In Time of War or Civil Disturbance'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_PROFUGIS_ET_EXSULIBUS => _('For Refugees and Exiles'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::TEMPORE_FAMIS_VEL_PRO_FAME_LABORANTIBUS => _('In Time of Famine or for Those Suffering Hunger'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::TEMPORE_TERRAEMOTUS => _('In Time of Earthquake'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_PETENDAM_PLUVIAM => _('For Rain'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_POSTULANDAM_AERIS_SERENITATEM => _('For Fine Weather'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_REPELLENDAS_TEMPESTATES => _('For an End to Storms'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_REMISSIONE_PECCATORUM => _('For the Forgiveness of Sins'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_POSTULANDAM_CONTINENTIAM => _('For Chastity'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_POSTULANDAM_CARITATEM => _('For Charity'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_FAMILIARIBUS_ET_AMICIS => _('For Relatives and Friends'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_AFFLIGENTIBUS_NOS => _('For Our Oppressors'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_CAPTIVITATE_DETENTIS => _('For Those Held in Captivity'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_DETENTIS_IN_CARCERE => _('For Those in Prison'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_INFIRMIS => _('For the Sick'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_MORIENTIBUS => _('For the Dying'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::AD_POSTULANDAM_GRATIAM_BENE_MORIENDI => _('For the Grace of a Happy Death'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::IN_QUACUMQUE_NECESSITATE => _('In Any Need'),
            /**translators: context = Masses and Prayers for Various Needs and Occasions */
            self::PRO_GRATIIS_DEO_REDDENDIS => _('For Giving Thanks to God'),
            // Intentionally English only, as this is a USA-specific category
            self::GIVING_THANKS_TO_GOD_FOR_THE_GIFT_OF_HUMAN_LIFE_USA => self::GIVING_THANKS_TO_GOD_FOR_THE_GIFT_OF_HUMAN_LIFE_USA->value
        };
    }

    /**
     * Translates the value with the full prefix.
     *
     * @param string $locale The locale to translate to.
     * @return string The translated value with prefix.
     */
    public function fullTranslate(string $locale): string
    {
        $isLatin = strtoupper($locale) === 'LA' || str_starts_with(strtoupper($locale), 'LA_');
        return $isLatin
            ? 'MISSAE ET ORATIONES PRO VARIIS NECESSITATIBUS VEL AD DIVERSA: ' . self::LATIN[$this->name]
            : _('Masses and Prayers for Various Needs and Occasions') . ': ' . $this->i18n($locale);
    }
}
