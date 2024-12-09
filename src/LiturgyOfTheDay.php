<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitLocale;
use LiturgicalCalendar\AlexaNewsBrief\Festivity;
use LiturgicalCalendar\AlexaNewsBrief\LitCalFeedItem;

/**
 * The LiturgyOfTheDay class is the main class of the Liturgical Calendar Alexa News Brief.
 *
 * It fetches metadata about all available calendars from the Liturgical Calendar API and
 * initialize the LiturgyOfTheDay object with the locale from the
 * GET parameter "locale". If the GET parameter "nationalcalendar" is
 * set, use it to set the national calendar and the locale. If the GET
 * parameter "diocesancalendar" is set, use it to set the diocesan
 * calendar, national calendar, and the locale. If the GET parameter
 * "timezone" is set, use it to set the timezone.
 *
 * It also sets up the locale and gettext translation environment.
 *
 * It fetches the liturgical data for the given locale and calendar from the Liturgical Calendar API
 * and stores the "litcal" array in $this->LitCalData.
 *
 * It then filters out only the events for today and converts each of these events into a Festivity object,
 * and calls prepareMainText to generate the main text and optional SSML for each event. Finally, it creates a new
 * LitCalFeedItem for each event and adds it to the $this->LitCalFeed array.
 */
class LiturgyOfTheDay
{
    private string $MetadataURL;
    private string $CalendarURL;
    private string $Locale              = LitLocale::LATIN;
    private string $baseLocale          = LitLocale::LATIN;
    private string $setLocale           = LitLocale::LATIN;
    private LitCommon $LitCommon;
    private LitGrade $LitGrade;
    private ?string $NationalCalendar   = null;
    private ?string $DiocesanCalendar   = null;
    private array $LitCalMetadata       = [];
    private array $LitCalData           = [];
    private array $LitCalFeed           = [];
    private \IntlDateFormatter $monthDayFmt;
    private const MANUAL_FIXES = [
        'it' => [
            '/SOLENNITÀ di Immacolata Concezione/' => "SOLENNITÀ dell'Immacolata Concezione",
        ],
    ];
    private const PHONETIC_PRONUNCATION_MAPPING = [
        '/Blessed /'   => '<phoneme alphabet="ipa" ph="ˈblɛsɪd">Blessed</phoneme> ',
        '/Antiochia/'  => '<phoneme alphabet="ipa" ph="ɑntɪˈokiɑ">Antiochia</phoneme>',
    ];
    private const ROMAN_NUMERAL_PATTERN_1_34 = '/^(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII|XIII|XIV|XV|XVI|XVII|XVIII|XIX|XX|XXI|XXII|XXIII|XXIV|XXV|XXVI|XXVII|XXVIII|XXIX|XXX|XXXI|XXXII|XXXIII|XXXIV) /';
    private const ROMAN_TO_ARABIC_MAPPING = [
        'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9,
        'X' => 10, 'XI' => 11, 'XII' => 12, 'XIII' => 13, 'XIV' => 14, 'XV' => 15, 'XVI' => 16, 'XVII' => 17, 'XVIII' => 18, 'XIX' => 19,
        'XX' => 20, 'XXI' => 21, 'XXII' => 22, 'XXIII' => 23, 'XXIV' => 24, 'XXV' => 25, 'XXVI' => 26, 'XXVII' => 27, 'XXVIII' => 28, 'XXIX' => 29,
        'XXX' => 30, 'XXXI' => 31, 'XXXII' => 32, 'XXXIII' => 33, 'XXXIV' => 34
    ];
    private \NumberFormatter $numberFormatter;
    private static $genericSpelloutOrdinal          = [
        'af', //Afrikaans
        'am', //Amharic
        'as', //Assamese
        'az', //Azerbaijani
        'bn', //Bengali
        'bo', //Tibetan
        'chr', //Cherokee,
        'de', //German : has also spellout-ordinal-n, spellout-ordinal-r, spellout-ordinal-s
              //        these seem to affect the article "the" preceding the ordinal,
              //        making it masculine, feminine, or neuter (or plural)
              //        but which is which between n-r-s? I believe r = masc, n = plural, s = neut?
              //        perhaps depends also on case: genitive, dative, etc.
        'dsb', //Lower Sorbian
        'dz', //Dzongha
        'en', //English
        'ee', //Ewe
        'es', //Esperanto
        'fi', //Finnish : also supports a myriad of other forms, too complicated to handle!
        'fil', //Filipino
        'gl', //Gallegan
        'gu', //Gujarati
        'ha', //Hausa
        'haw', //Hawaiian
        'hsb', //Upper Sorbian
        'hu', //Hungarian
        'id', //Indonesian
        'ig', //Igbo
        'ja', //Japanese
        'kk', //Kazakh
        'km', //Khmer
        'kn', //Kannada
        'kok', //Konkani
        'jy', //Kirghiz
        'lb', //Luxembourgish
        'lkt', //Lakota
        'ln', //Lingala
        'lo', //Lao
        'ml', //Malayalam
        'mn', //Mongolian
        'mr', //Marathi
        'ms', //Malay
        'my', //Burmese
        'ne', //Nepali
        'nl', //Dutch
        'om', //Oromo
        'or', //Oriva
        'pa', //Panjabi
        'ps', //Pushto
        'si', //Sinhalese
        'smn', //Inari Sami
        'sr', //Serbian
        'sw', //Swahili
        'ta', //Tamil
        'te', //Telugu
        'th', //Thai
        'to', //Tonga
        'tr', //Turkish
        'ug', //Uighur
        'ur', //Urdu
        'uz', //Uzbek
        'vi', //Vietnamese
        'wae', //Walser
        'yi', //Yiddish
        'yo', //Yoruba
        'zh', //Chinese
        'zu'  //Zulu
    ];

    /**
     * Languages that use spellout-ordinal-masculine and spellout-ordinal-feminine
     */
    private static $mascFemSpelloutOrdinal          = [
        'ar', //Arabic
        'ca', //Catalan
        'es', //Spanish : also supports plural forms, as well as a masculine adjective form (? spellout-ordinal-masculine-adjective)
        'fr', //French
        'he', //Hebrew
        'hi', //Hindi
        'it', //Italian
        'pt'  //Portuguese
    ];

    /**
     * Languages that use spellout-ordinal-masculine, spellout-ordinal-feminine, and spellout-ordinal-neuter
     */
    private static $mascFemNeutSpelloutOrdinal      = [
        'bg', //Bulgarian
        'be', //Belarusian
        'el', //Greek
        'hr', //Croatian
        'nb', //Norwegian Bokmål
        'ru', //Russian : also supports a myriad of other cases, too complicated to handle for now
        'sv'  //Swedish : also supports spellout-ordinal-reale ?
    ];

    //even though these do not yet support spellout-ordinal, however they do support digits-ordinal
    /*private static $noSpelloutOrdinal               = [
        'bs', //Bosnian
        'cs', //Czech
        'cy', //Welsh
        'et', //Estonian
        'fa', //Persian
        'fo', //Faroese
        'ga', //Irish
        'hy', //Armenian
        'is', //Icelandic
        'ka', //Georgian
        'kl', //Greenlandic aka Kalaallisut
        'ko', //Korean : supports specific forms spellout-ordinal-native etc. but too complicated to handle for now
        'lt', //Lithuanian
        'lv', //Latvian
        'mk', //Macedonian
        'mt', //Maltese
        'nn', //Norwegian Nynorsk
        'pl', //Polish
        'ro', //Romanian
        'se', //Northern Sami
        'sk', //Slovak
        'sl', //Slovenian
        'sq', //Albanian
        'uq'  //Ukrainian
    ];*/

    /**
     * Whatever does spellout-ordinal-common mean?
     * ChatGPT tells us:
     * In Danish, ordinals are formed by adding "-te" (for most ordinals) or "-ende" (for some specific cases).
     * Danish ordinal formation becomes relatively regular after the first few numbers, with "-te" being the primary suffix.
     * In Danish, ordinals may change depending on gender. For example, "2nd -> second": Anden (for common gender) vs. andet (for neuter gender).
     * Examples:
     *  - "anden plads" (second place)
     *  - "andet hus" (second house) for neuter gender
     *
     * So apparently it is very similar to spellout-ordinal with a few cases using neutral gender.
     */
    private static $commonNeutSpelloutOrdinal       = [
        'da'  //Danish
    ];

    /**
     * Construct the Liturgy of the Day object.
     *
     * Fetches metadata about all available calendars from the Liturgical Calendar API and
     * initialize the LiturgyOfTheDay object with the locale from the
     * GET parameter "locale". If the GET parameter "nationalcalendar" is
     * set, use it to set the national calendar and the locale. If the GET
     * parameter "diocesancalendar" is set, use it to set the diocesan
     * calendar, national calendar, and the locale. If the GET parameter
     * "timezone" is set, use it to set the timezone.
     *
     * @throws \Exception
     */
    public function __construct(string $apiURL)
    {
        $this->MetadataURL = $apiURL . '/calendars';
        $this->CalendarURL = $apiURL . '/calendar';
        $this->sendMetadataReq();

        $this->Locale = isset($_GET["locale"]) && LitLocale::isValid($_GET["locale"])
            ? \Locale::canonicalize($_GET["locale"])
            : LitLocale::LATIN;

        if (isset($_GET["nationalcalendar"]) && $_GET["nationalcalendar"] !== "") {
            if (false === in_array($_GET["nationalcalendar"], $this->LitCalMetadata['national_calendars_keys'])) {
                die("Request failed. Requested national calendar '{$_GET["nationalcalendar"]}' is not supported. Supported national calendars are: "
                    . implode(', ', $this->LitCalMetadata['national_calendars_keys']));
            }
            $this->NationalCalendar = $_GET["nationalcalendar"];
            $this->CalendarURL = $this->CalendarURL . '/nation/' . $this->NationalCalendar;
            $NationalCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['national_calendars'],
                fn($nationalCalendar) => $nationalCalendar['calendar_id'] === $this->NationalCalendar
            ))[0];
            if ($this->NationalCalendar !== 'VA') {
                // TODO: allow to request a different locale among those that are supported by the requested calendar
                $this->Locale = $NationalCalendarMetadata["locales"][0];
            }
        }

        if (isset($_GET["diocesancalendar"]) && $_GET["diocesancalendar"] !== "") {
            if (false === in_array($_GET["diocesancalendar"], $this->LitCalMetadata['diocesan_calendars_keys'])) {
                die("Request failed. Requested diocesan calendar '{$_GET["diocesancalendar"]}' is not supported. Supported diocesan calendars are: "
                    . implode(', ', $this->LitCalMetadata['diocesan_calendars_keys']));
            }
            $this->DiocesanCalendar = $_GET["diocesancalendar"];
            $this->CalendarURL = $this->CalendarURL . '/diocese/' . $this->DiocesanCalendar;
            $DiocesanCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['diocesan_calendars'],
                fn($diocesanCalendar) => $diocesanCalendar['calendar_id'] === $this->DiocesanCalendar
            ))[0];
            $NationalCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['national_calendars'],
                fn($nationalCalendar) => $nationalCalendar['calendar_id'] === $DiocesanCalendarMetadata["nation"]
            ))[0];
            // TODO: allow to request a different locale among those that are supported by the requested calendar
            $this->Locale = $NationalCalendarMetadata["locales"][0];
        }

        if (isset($_GET["timezone"]) && LiturgyOfTheDay::isValidTimezone($_GET["timezone"])) {
            ini_set('date.timezone', $_GET["timezone"]);
        } else {
            ini_set('date.timezone', 'Europe/Vatican');
        }
    }

    /**
     * Sets up the locale and gettext translation environment.
     *
     * We set the locale to either $this->Locale or the language part of $this->Locale (which is the locale
     * from the national calendar if applicable, Latin otherwise), since the national calendar locale is the
     * actual locale used for the translations. The country code shouldn't be too
     * relevant for the translations.
     *
     * We also set up the gettext translation environment, which is used to
     * translate the texts for the liturgical day.
     */
    private function prepareL10N(): void
    {
        $this->baseLocale = \Locale::getPrimaryLanguage($this->Locale);
        $localeArray = [
            $this->Locale . '.utf8',
            $this->Locale . '.UTF-8'
        ];
        if ($this->baseLocale !== $this->Locale) {
            $localeArray[] = $this->baseLocale . '.utf8';
            $localeArray[] = $this->baseLocale . '.UTF-8';
            $localeArray[] = $this->baseLocale . '_' . LitLocale::$primaryRegion[$this->baseLocale] . '.utf8';
            $localeArray[] = $this->baseLocale . '_' . LitLocale::$primaryRegion[$this->baseLocale] . '.UTF-8';
            $localeArray[] = $this->baseLocale . '_' . LitLocale::$primaryRegion[$this->baseLocale];
            $localeArray[] = $this->baseLocale;
        }
        if ($this->baseLocale === $this->Locale) {
            $localeArray[] = $this->Locale . '_' . LitLocale::$primaryRegion[$this->Locale] . '.utf8';
            $localeArray[] = $this->Locale . '_' . LitLocale::$primaryRegion[$this->Locale] . '.UTF-8';
            $localeArray[] = $this->Locale . '_' . LitLocale::$primaryRegion[$this->Locale];
            $localeArray[] = $this->Locale;
        }
        $this->setLocale = setlocale(LC_ALL, $localeArray);
        bindtextdomain("litcal", "i18n");
        textdomain("litcal");
        $this->LitCommon    = new LitCommon($this->Locale);
        $this->LitGrade     = new LitGrade($this->Locale);
        $this->monthDayFmt  = \IntlDateFormatter::create(
            $this->Locale,
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
            'd MMMM'
        );
        $this->numberFormatter = new \NumberFormatter($this->baseLocale, \NumberFormatter::SPELLOUT);
        if (in_array($this->baseLocale, self::$genericSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal");
        } elseif (in_array($this->baseLocale, self::$mascFemSpelloutOrdinal) || in_array($this->baseLocale, self::$mascFemNeutSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal-feminine");
        } elseif (in_array($this->baseLocale, self::$commonNeutSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal-common");
        } else {
            $this->numberFormatter = new \NumberFormatter($this->baseLocale, \NumberFormatter::ORDINAL);
        }
    }

    /**
     * Sends a request to the Liturgical Calendar API's /calendars path to retrieve
     * metadata about all available liturgical calendars.
     *
     * If the request fails, it will die with an error message.
     *
     * If the request succeeds, it will decode the JSON response
     * and store the "litcal_metadata" array in $this->LitCalMetadata.
     * @throws \Exception
     */
    private function sendMetadataReq(): void
    {
        $ch = curl_init($this->MetadataURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Could not send request. Curl error: " . curl_error($ch));
        }

        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus !== 200) {
            die("Metadata request failed. HTTP status code: " . $resultStatus);
        }

        $response = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            die("Metadata request failed. Could not decode metadata JSON data. " . json_last_error_msg());
        }

        ["litcal_metadata" => $this->LitCalMetadata] = $response;

        curl_close($ch);
    }


    /**
     * @throws \Exception
     */
    /**
     * Sends a request to the calendar API at $this->CalendarURL
     * with the Accept-Language header set to $this->Locale and the
     * Accept header set to application/json.
     *
     * If the request fails, it will die with an error message.
     *
     * If the request succeeds, it will decode the JSON response
     * and store the "litcal" array in $this->LitCalData.
     */
    private function sendReq()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->CalendarURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["year_type" => "CIVIL"]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept-Language: $this->Locale",
            "Accept: application/json"
        ]);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            die("Could not send request. Curl error: " . curl_error($ch));
        }

        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus != 200) {
            die("Request to API /calendar route failed at URL '$this->CalendarURL'. HTTP status code: " . $resultStatus);
        }

        $jsonData = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            die("Request failed. Could not decode calendar JSON data. " . json_last_error_msg());
        }

        if (false === array_key_exists('litcal', $jsonData)) {
            die("Request failed. Cannot elaborate JSON data.");
        }

        ["litcal" =>  $this->LitCalData] = $jsonData;

        curl_close($ch);
    }

    /**
     * This function takes the LitCal data and filters out only the events for today.
     * It then converts each of these events into a Festivity object, and calls prepareMainText
     * to generate the main text and optional SSML for each event. Finally, it creates a new LitCalFeedItem
     * for each event and adds it to the $this->LitCalFeed array.
     */
    private function filterEventsToday()
    {
        $dateTimeToday = ( new \DateTime('now') )->format("Y-m-d") . " 00:00:00";
        $dateToday = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeToday, new \DateTimeZone('UTC'));
        $dateTodayTimestampStr = $dateToday->format("U");
        $dateTodayTimestamp = intval($dateTodayTimestampStr);
        $dateToday->add(new \DateInterval('PT15M'));
        $idx = 0;
        foreach ($this->LitCalData as $value) {
            //file_put_contents( $this->logFile, "Processing litcal event $value['event_key']..." . "\n", FILE_APPEND );
            if ($value["date"] === $dateTodayTimestamp) {
                //file_put_contents( $this->logFile, "Found litcal event $value['event_key'] with timestamp equal to today!" . "\n", FILE_APPEND );
                $publishDate = $dateToday;
                // retransform each entry from an associative array to a Festivity class object
                $festivity = new Festivity($value);
                ["mainText" => $mainText, "ssml" => $ssml] = $this->prepareMainText($festivity, $idx);
                $titleText = _("Liturgy of the Day") . " ";
                if ($this->baseLocale === LitLocale::ENGLISH) {
                    $titleText .= $festivity->date->format('F jS');
                } else {
                    $titleText .= $this->monthDayFmt->format($festivity->date->format('U'));
                }
                $this->LitCalFeed[] = new LitCalFeedItem($festivity, $publishDate, $titleText, $mainText, $ssml);
                $idx++;
            }
        }
    }

    private static function detectRomanNumeral($str): int|false
    {
        if (preg_match(self::ROMAN_NUMERAL_PATTERN_1_34, $str, $matches) === 1) {
            return self::ROMAN_TO_ARABIC_MAPPING[$matches[1]];
        }
        return false;
    }

    /**
     * Prepares the main text for a given Festivity, given its index in the LitCalFeed array.
     *
     * This function takes into account the grade of the festivity and its relative position in the LitCalFeed array and returns
     * a string that can be used as the main text for that festivity.
     *
     * @param Festivity $festivity The Festivity to generate the main text for.
     * @param int $idx The index of the Festivity in the LitCalFeed array.
     * @return array A two-element array containing the main text and the SSML string, if any.
     */
    private function prepareMainText(Festivity $festivity, int $idx): array
    {
        $mainText = "";
        $ssml = null;
        //Situations in which we don't need to actually state "Feast of the Lord":
        $filterTagsDisplayGrade = [
            "/OrdSunday[0-9]{1,2}(_vigil){0,1}/",
            "/Advent[1-4](_vigil){0,1}/",
            "/Lent[1-5](_vigil){0,1}/",
            "/Easter[1-7](_vigil){0,1}/"
        ];
        $isSundayOrdAdvLentEaster = false;
        foreach ($filterTagsDisplayGrade as $pattern) {
            if (preg_match($pattern, $festivity->tag) === 1) {
                $isSundayOrdAdvLentEaster = true;
                break;
            }
        }

        if ($isSundayOrdAdvLentEaster) {
            $startsWithRoman = self::detectRomanNumeral($festivity->name);
            if ($startsWithRoman !== false) {
                $replacement = $this->numberFormatter->format($startsWithRoman);
                $festivity->name = preg_replace(self::ROMAN_NUMERAL_PATTERN_1_34, $replacement . ' ', $festivity->name);
            }
        }

        if ($festivity->grade === LitGrade::WEEKDAY) {
            $mainText = _("Today is") . " " . $festivity->name . ".";
        } else {
            if ($festivity->isVigilMass) {
                if ($isSundayOrdAdvLentEaster) {
                    $mainText = sprintf(
                        /**translators: 1. name of the festivity */
                        _('This evening there will be a Vigil Mass for the %1$s.'),
                        trim(str_replace(_("Vigil Mass"), "", $festivity->name))
                    );
                } else {
                    $mainText = sprintf(
                        /**translators: 1. grade of the festivity, 2. name of the festivity */
                        _('This evening there will be a Vigil Mass for the %1$s %2$s.'),
                        $this->LitGrade->i18n($festivity->grade, false),
                        trim(str_replace(_("Vigil Mass"), "", $festivity->name))
                    );
                }
            } elseif ($festivity->grade < LitGrade::HIGHER_SOLEMNITY) {
                if ($festivity->displayGrade !== null) {
                    if ($festivity->displayGrade === '') {
                        $mainText = sprintf(
                            /**translators: 1. (also|''), 2. name of the festivity */
                            _('Today is %1$s the %2$s.'),
                            ( $idx > 0 ? _("also") : "" ),
                            $festivity->name
                        );
                    } else {
                        $mainText = sprintf(
                            /**translators: 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _('Today is %1$s the %2$s of %3$s.'),
                            ( $idx > 0 ? _("also") : "" ),
                            $festivity->displayGrade,
                            $festivity->name
                        );
                    }
                } else {
                    if ($festivity->grade === LitGrade::FEAST_LORD) {
                        if ($isSundayOrdAdvLentEaster) {
                            $mainText = sprintf(
                                /**translators: CTXT: Sundays. 1. (also|''), 2. name of the festivity */
                                _('Today is %1$s the %2$s.'),
                                ( $idx > 0 ? _("also") : "" ),
                                $festivity->name
                            );
                        } else {
                            $mainText = sprintf(
                                /**translators: CTXT: Feast of the Lord. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                                _('Today is %1$s the %2$s, %3$s.'),
                                ( $idx > 0 ? _("also") : "" ),
                                $this->LitGrade->i18n($festivity->grade, false),
                                $festivity->name
                            );
                        }
                    } elseif (strpos($festivity->tag, "SatMemBVM") !== false) {
                        $mainText = sprintf(
                            /**translators: CTXT: Saturday memorial BVM. 1. (also|''), 2. name of the festivity */
                            _('Today is %1$s the %2$s.'),
                            ( $idx > 0 ? _("also") : "" ),
                            $festivity->name
                        );
                    } else {
                        $mainText = sprintf(
                            /**translators: CTXT: (optional) memorial or feast. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _('Today is %1$s the %2$s of %3$s.'),
                            ( $idx > 0 ? _("also") : "" ),
                            $this->LitGrade->i18n($festivity->grade, false),
                            $festivity->name
                        );
                    }
                }

                if ($festivity->grade < LitGrade::FEAST && $festivity->common != LitCommon::PROPRIO) {
                    $mainText = $mainText . " " . $this->LitCommon->c($festivity->common);
                }
            } else {
                $mainText = sprintf(
                    /**translators: CTXT: higher grade solemnity with precedence over other solemnities. 1. (also|''), 2. name of the festivity  */
                    _('Today is %1$s the day of %2$s.'),
                    ( $idx > 0 ? _("also") : "" ),
                    $festivity->name
                );
            }

            $mainText = preg_replace('/  +/', ' ', $mainText);
            if (array_key_exists($this->baseLocale, LiturgyOfTheDay::MANUAL_FIXES)) {
                foreach( LiturgyOfTheDay::MANUAL_FIXES[$this->baseLocale] as $pattern => $replacement ) {
                    $mainText = preg_replace($pattern, $replacement, $mainText);
                }
            }

            // Create the <speak> root element
            $speak = new \SimpleXMLElement('<speak></speak>');
            $voice = $speak->addChild('voice');
            $lang = $voice->addChild('lang', $mainText);
            $namespaces = [
                'xml' => 'http://www.w3.org/XML/1998/namespace'
            ];
            // translate PHP Locale identifier to Unicode BCP 47 locale identifiers
            $locale = str_replace('_', '-', $this->Locale);
            // https://developer.amazon.com/it-IT/docs/alexa/custom-skills/speech-synthesis-markup-language-ssml-reference.html#supported-locales-for-the-xmllang-attribute
            switch ($this->baseLocale) {
                case "en":
                    // Supported voices: Ivy, Joanna, Joey, Justin, Kendra, Kimberly, Matthew, Salli
                    $voice->addAttribute('name', 'Joanna');
                    $lang->addAttribute('xml:lang', 'en-US', $namespaces['xml']);
                    break;
                case "es":
                    // Supported voices: Conchita, Enrique, Lucia
                    $voice->addAttribute('name', 'Conchita');
                    $lang->addAttribute('xml:lang', 'es-ES', $namespaces['xml']);
                    break;
                case "fr":
                    // Supported voices: Celine, Lea, Mathieu
                    $voice->addAttribute('name', 'Celine');
                    $lang->addAttribute('xml:lang', 'fr-FR', $namespaces['xml']);
                    break;
                case "de":
                    // Supported voice: Hans, Marlene, Vicki
                    $voice->addAttribute('name', 'Marlene');
                    $lang->addAttribute('xml:lang', 'de-DE', $namespaces['xml']);
                    break;
                case "it":
                    // Supported voices: Carla, Giorgio, Bianca
                    $voice->addAttribute('name', 'Carla');
                    $lang->addAttribute('xml:lang', 'it-IT', $namespaces['xml']);
                    break;
                case "pt":
                    // Supported voices: Vitoria, Camila, Ricardo
                    $voice->addAttribute('name', 'Vitoria');
                    $lang->addAttribute('xml:lang', 'pt-BR', $namespaces['xml']);
                    break;
                default:
                    $voice->addAttribute('name', 'Joanna');
                    $lang->addAttribute('xml:lang', $locale, $namespaces['xml']);
                    break;
            }

            // Convert SimpleXMLElement to DOMDocument
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
            $dom->loadXML($speak->asXML());
            $ssml = $dom->saveXML($dom->documentElement);

            //Fix some phonetic pronunciations
            foreach (LiturgyOfTheDay::PHONETIC_PRONUNCATION_MAPPING as $key => $value) {
                if (preg_match($key, $mainText) === 1) {
                    $ssml = preg_replace($key, $value, $ssml);
                }
            }
        }
        return ["mainText" => $mainText, "ssml" => $ssml];
    }

    /**
     * Determines if the given timezone is valid.
     *
     * @param string $timezone
     * @return boolean
     */
    private function isValidTimezone($timezone)
    {
        if (in_array($timezone, \DateTimeZone::listIdentifiers())) {
            return true;
        }
        return false;
    }

    /**
     * Sends the Alexa Flash Briefing response.
     *
     * This method will send either a single JSON object or an array of JSON objects, depending on the number of Festivity objects
     * in the LitCalFeed array.  If the LitCalFeed array contains only one Festivity, it will send the JSON representation of the
     * single Festivity.  If the LitCalFeed array contains more than one Festivity, it will send the JSON representation of the
     * LitCalFeed array itself.
     */
    private function sendResponse()
    {
        header('Content-Type: application/json');
        if (count($this->LitCalFeed) === 1) {
            echo json_encode($this->LitCalFeed[0]);
        } elseif (count($this->LitCalFeed) > 1) {
            echo json_encode($this->LitCalFeed);
        } else {
            die("Missing data from response: LitCalFeed seems to be empty or null? " . count($this->LitCalFeed));
        }
    }

    /**
     * Initializes the LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay object.
     *
     * This method will call the other methods in the correct order to generate and send the Alexa Flash Briefing response.
     */
    public function init()
    {
        $this->sendReq();
        $this->prepareL10N();
        $this->filterEventsToday();
        $this->sendResponse();
    }
}
