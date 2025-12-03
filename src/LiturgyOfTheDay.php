<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use GuzzleHttp\Client;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\SimpleCache\CacheInterface;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitLocale;
use LiturgicalCalendar\AlexaNewsBrief\LitCalFeedItem;
use LiturgicalCalendar\AlexaNewsBrief\LiturgicalEvent;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

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
 * It then filters out only the events for today and converts each of these events into a LiturgicalEvent object,
 * and calls prepareMainText to generate the main text and optional SSML for each event. Finally, it creates a new
 * LitCalFeedItem for each event and adds it to the $this->LitCalFeed array.
 */
class LiturgyOfTheDay
{
    private Psr17Factory $psr17Factory;
    private ClientInterface $httpClient;
    private ?CacheInterface $cache = null;
    private string $MetadataURL;
    private string $CalendarURL;
    private string $Locale            = LitLocale::LATIN;
    private string $baseLocale        = LitLocale::LATIN;
    private string|false $setLocale   = LitLocale::LATIN;
    private ?string $NationalCalendar = null;
    private ?string $DiocesanCalendar = null;

    /** @var array<string, mixed> */
    private array $LitCalMetadata = [];

    /** @var array<int, array<string, mixed>> */
    private array $LitCalData = [];

    /** @var array<LitCalFeedItem> */
    private array $LitCalFeed = [];
    private \IntlDateFormatter $monthDayFmt;
    private const MANUAL_FIXES                  = [
        'it' => ['/SOLENNITÀ di Immacolata Concezione/' => "SOLENNITÀ dell'Immacolata Concezione"],
    ];
    private const PHONETIC_PRONUNCATION_MAPPING = [
        '/Blessed /'  => '<phoneme alphabet="ipa" ph="ˈblɛsɪd">Blessed</phoneme> ',
        '/Antiochia/' => '<phoneme alphabet="ipa" ph="ɑntɪˈokiɑ">Antiochia</phoneme>',
    ];
    private const ROMAN_NUMERAL_PATTERN_1_34    =
        '/^(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII|XIII|XIV|XV|XVI|XVII|XVIII|XIX|' .
        'XX|XXI|XXII|XXIII|XXIV|XXV|XXVI|XXVII|XXVIII|XXIX|XXX|XXXI|XXXII|XXXIII|XXXIV) /';
    private const ROMAN_TO_ARABIC_MAPPING       = [
        'I'      => 1,
        'II'     => 2,
        'III'    => 3,
        'IV'     => 4,
        'V'      => 5,
        'VI'     => 6,
        'VII'    => 7,
        'VIII'   => 8,
        'IX'     => 9,
        'X'      => 10,
        'XI'     => 11,
        'XII'    => 12,
        'XIII'   => 13,
        'XIV'    => 14,
        'XV'     => 15,
        'XVI'    => 16,
        'XVII'   => 17,
        'XVIII'  => 18,
        'XIX'    => 19,
        'XX'     => 20,
        'XXI'    => 21,
        'XXII'   => 22,
        'XXIII'  => 23,
        'XXIV'   => 24,
        'XXV'    => 25,
        'XXVI'   => 26,
        'XXVII'  => 27,
        'XXVIII' => 28,
        'XXIX'   => 29,
        'XXX'    => 30,
        'XXXI'   => 31,
        'XXXII'  => 32,
        'XXXIII' => 33,
        'XXXIV'  => 34
    ];
    private \NumberFormatter $numberFormatter;

    /** @var array<string> */
    private static array $genericSpelloutOrdinal = [
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
     *
     * @var array<string>
     */
    private static array $mascFemSpelloutOrdinal = [
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
     *
     * @var array<string>
     */
    private static array $mascFemNeutSpelloutOrdinal = [
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
     *
     * @var array<string>
     */
    private static array $commonNeutSpelloutOrdinal = ['da'];  //Danish

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
     * @param string $apiURL The base URL of the Liturgical Calendar API.
     * @param ClientInterface|null $httpClient Optional PSR-18 HTTP client. If not provided, a default Guzzle client is used.
     * @param CacheInterface|null $cache Optional PSR-16 cache. If provided, metadata and calendar data will be cached.
     * @throws \Exception
     */
    public function __construct(string $apiURL, ?ClientInterface $httpClient = null, ?CacheInterface $cache = null)
    {
        $this->psr17Factory = new Psr17Factory();
        $this->httpClient   = $httpClient ?? new Client();
        $this->cache        = $cache;
        $this->MetadataURL  = $apiURL . '/calendars';
        $this->CalendarURL  = $apiURL . '/calendar';
        $this->sendMetadataReq();

        $localeParam  = isset($_GET['locale']) && is_string($_GET['locale']) ? $_GET['locale'] : null;
        $this->Locale = $localeParam !== null && LitLocale::isValid($localeParam)
            ? ( \Locale::canonicalize($localeParam) ?? LitLocale::LATIN )
            : LitLocale::LATIN;

        $nationalCalendarKeys = isset($this->LitCalMetadata['national_calendars_keys'])
            && is_array($this->LitCalMetadata['national_calendars_keys'])
            ? $this->LitCalMetadata['national_calendars_keys']
            : [];
        $nationalCalendars    = isset($this->LitCalMetadata['national_calendars'])
            && is_array($this->LitCalMetadata['national_calendars'])
            ? $this->LitCalMetadata['national_calendars']
            : [];
        $diocesanCalendarKeys = isset($this->LitCalMetadata['diocesan_calendars_keys'])
            && is_array($this->LitCalMetadata['diocesan_calendars_keys'])
            ? $this->LitCalMetadata['diocesan_calendars_keys']
            : [];
        $diocesanCalendars    = isset($this->LitCalMetadata['diocesan_calendars'])
            && is_array($this->LitCalMetadata['diocesan_calendars'])
            ? $this->LitCalMetadata['diocesan_calendars']
            : [];

        $nationalCalendarParam = isset($_GET['nationalcalendar']) && is_string($_GET['nationalcalendar'])
            ? $_GET['nationalcalendar']
            : '';
        if ($nationalCalendarParam !== '') {
            if (false === in_array($nationalCalendarParam, $nationalCalendarKeys, true)) {
                die("Request failed. Requested national calendar '{$nationalCalendarParam}' is not supported. Supported national calendars are: "
                    . implode(', ', $nationalCalendarKeys));
            }
            $this->NationalCalendar   = $nationalCalendarParam;
            $this->CalendarURL        = $this->CalendarURL . '/nation/' . $this->NationalCalendar;
            $NationalCalendarMetadata = array_values(array_filter(
                $nationalCalendars,
                fn($nationalCalendar) => is_array($nationalCalendar)
                    && isset($nationalCalendar['calendar_id'])
                    && $nationalCalendar['calendar_id'] === $this->NationalCalendar
            ))[0] ?? [];
            if ($this->NationalCalendar !== 'VA' && is_array($NationalCalendarMetadata)) {
                // TODO: allow to request a different locale among those that are supported by the requested calendar
                $locales      = isset($NationalCalendarMetadata['locales']) && is_array($NationalCalendarMetadata['locales'])
                    ? $NationalCalendarMetadata['locales']
                    : [];
                $this->Locale = isset($locales[0]) && is_string($locales[0]) ? $locales[0] : LitLocale::LATIN;
            }
        }

        $diocesanCalendarParam = isset($_GET['diocesancalendar']) && is_string($_GET['diocesancalendar'])
            ? $_GET['diocesancalendar']
            : '';
        if ($diocesanCalendarParam !== '') {
            if (false === in_array($diocesanCalendarParam, $diocesanCalendarKeys, true)) {
                die("Request failed. Requested diocesan calendar '{$diocesanCalendarParam}' is not supported. Supported diocesan calendars are: "
                    . implode(', ', $diocesanCalendarKeys));
            }
            $this->DiocesanCalendar   = $diocesanCalendarParam;
            $this->CalendarURL        = $this->CalendarURL . '/diocese/' . $this->DiocesanCalendar;
            $DiocesanCalendarMetadata = array_values(array_filter(
                $diocesanCalendars,
                fn($diocesanCalendar) => is_array($diocesanCalendar)
                    && isset($diocesanCalendar['calendar_id'])
                    && $diocesanCalendar['calendar_id'] === $this->DiocesanCalendar
            ))[0] ?? [];
            $diocesanNation           = is_array($DiocesanCalendarMetadata) && isset($DiocesanCalendarMetadata['nation'])
                && is_string($DiocesanCalendarMetadata['nation'])
                ? $DiocesanCalendarMetadata['nation']
                : '';
            $NationalCalendarMetadata = array_values(array_filter(
                $nationalCalendars,
                fn($nationalCalendar) => is_array($nationalCalendar)
                    && isset($nationalCalendar['calendar_id'])
                    && $nationalCalendar['calendar_id'] === $diocesanNation
            ))[0] ?? [];
            // TODO: allow to request a different locale among those that are supported by the requested calendar
            if (is_array($NationalCalendarMetadata)) {
                $locales      = isset($NationalCalendarMetadata['locales']) && is_array($NationalCalendarMetadata['locales'])
                    ? $NationalCalendarMetadata['locales']
                    : [];
                $this->Locale = isset($locales[0]) && is_string($locales[0]) ? $locales[0] : LitLocale::LATIN;
            }
        }

        $timezoneParam = isset($_GET['timezone']) && is_string($_GET['timezone']) ? $_GET['timezone'] : null;
        if ($timezoneParam !== null && self::isValidTimezone($timezoneParam)) {
            ini_set('date.timezone', $timezoneParam);
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
        $this->baseLocale = \Locale::getPrimaryLanguage($this->Locale) ?? LitLocale::LATIN;
        $localeArray      = [
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
        bindtextdomain('litcal', 'i18n');
        textdomain('litcal');
        $monthDayFmt = \IntlDateFormatter::create(
            $this->Locale,
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
            'd MMMM'
        );
        if ($monthDayFmt === null) {
            throw new \RuntimeException('Failed to create IntlDateFormatter');
        }
        $this->monthDayFmt     = $monthDayFmt;
        $this->numberFormatter = new \NumberFormatter($this->baseLocale, \NumberFormatter::SPELLOUT);
        if (in_array($this->baseLocale, self::$genericSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal');
        } elseif (in_array($this->baseLocale, self::$mascFemSpelloutOrdinal) || in_array($this->baseLocale, self::$mascFemNeutSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal-feminine');
        } elseif (in_array($this->baseLocale, self::$commonNeutSpelloutOrdinal)) {
            $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal-common');
        } else {
            $this->numberFormatter = new \NumberFormatter($this->baseLocale, \NumberFormatter::ORDINAL);
        }
    }

    /** Cache TTL for metadata: 1 week in seconds */
    private const METADATA_CACHE_TTL = 604800;

    /** Cache TTL for calendar data: 1 day in seconds */
    private const CALENDAR_CACHE_TTL = 86400;

    /**
     * Sends a request to the Liturgical Calendar API's /calendars path to retrieve
     * metadata about all available liturgical calendars.
     *
     * If caching is enabled, the metadata will be cached for 1 week.
     *
     * If the request fails, it will die with an error message.
     *
     * If the request succeeds, it will decode the JSON response
     * and store the "litcal_metadata" array in $this->LitCalMetadata.
     *
     * @throws \Exception
     */
    private function sendMetadataReq(): void
    {
        $cacheKey = 'litcal_metadata_' . md5($this->MetadataURL);

        // Try to get from cache first
        if ($this->cache !== null) {
            $cachedMetadata = $this->cache->get($cacheKey);
            if (is_array($cachedMetadata)) {
                /** @var array<string, mixed> $typedMetadata */
                $typedMetadata        = $cachedMetadata;
                $this->LitCalMetadata = $typedMetadata;
                return;
            }
        }

        $request = $this->psr17Factory->createRequest('GET', $this->MetadataURL)
            ->withHeader('Accept', 'application/json');

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            die('Could not send request. HTTP client error: ' . $e->getMessage());
        }

        $resultStatus = $response->getStatusCode();
        if ($resultStatus !== 200) {
            die('Metadata request failed. HTTP status code: ' . $resultStatus);
        }

        $result = $response->getBody()->getContents();

        /** @var array<string, mixed>|null $responseData */
        $responseData = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error() || !is_array($responseData)) {
            die('Metadata request failed. Could not decode metadata JSON data. ' . json_last_error_msg());
        }

        $litcalMetadata = $responseData['litcal_metadata'] ?? [];
        if (is_array($litcalMetadata)) {
            /** @var array<string, mixed> $typedMetadata */
            $typedMetadata        = $litcalMetadata;
            $this->LitCalMetadata = $typedMetadata;

            // Store in cache
            if ($this->cache !== null) {
                if (!$this->cache->set($cacheKey, $typedMetadata, self::METADATA_CACHE_TTL)) {
                    error_log('Failed to cache metadata for key: ' . $cacheKey);
                }
            }
        }
    }


    /**
     * Sends a request to the calendar API at $this->CalendarURL
     * with the Accept-Language header set to $this->Locale and the
     * Accept header set to application/json.
     *
     * If caching is enabled, the calendar data will be cached for 1 day.
     *
     * If the request fails, it will die with an error message.
     *
     * If the request succeeds, it will decode the JSON response
     * and store the "litcal" array in $this->LitCalData.
     */
    private function sendReq(): void
    {
        $cacheKey = 'litcal_calendar_' . md5($this->CalendarURL . '_' . $this->Locale);

        // Try to get from cache first
        if ($this->cache !== null) {
            $cachedData = $this->cache->get($cacheKey);
            if (is_array($cachedData)) {
                /** @var array<int, array<string, mixed>> $typedData */
                $typedData        = $cachedData;
                $this->LitCalData = $typedData;
                return;
            }
        }

        $body    = $this->psr17Factory->createStream(http_build_query(['year_type' => 'CIVIL']));
        $request = $this->psr17Factory->createRequest('POST', $this->CalendarURL)
            ->withHeader('Accept-Language', $this->Locale)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            die('Could not send request. HTTP client error: ' . $e->getMessage());
        }

        $resultStatus = $response->getStatusCode();
        if ($resultStatus !== 200) {
            die("Request to API /calendar route failed at URL '$this->CalendarURL'. HTTP status code: " . $resultStatus);
        }

        $result = $response->getBody()->getContents();

        /** @var array<string, mixed>|null $jsonData */
        $jsonData = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error() || !is_array($jsonData)) {
            die('Request failed. Could not decode calendar JSON data. ' . json_last_error_msg());
        }

        if (false === array_key_exists('litcal', $jsonData)) {
            die('Request failed. Cannot elaborate JSON data.');
        }

        $litcal = $jsonData['litcal'];
        /** @var array<int, array<string, mixed>> $litcalData */
        $litcalData       = is_array($litcal) ? $litcal : [];
        $this->LitCalData = $litcalData;

        // Store in cache
        if ($this->cache !== null) {
            if (!$this->cache->set($cacheKey, $litcalData, self::CALENDAR_CACHE_TTL)) {
                error_log('Failed to cache calendar data for key: ' . $cacheKey);
            }
        }
    }

    /**
     * This function takes the LitCal data and filters out only the events for today.
     * It then converts each of these events into a LiturgicalEvent object, and calls prepareMainText
     * to generate the main text and optional SSML for each event. Finally, it creates a new LitCalFeedItem
     * for each event and adds it to the $this->LitCalFeed array.
     */
    private function filterEventsToday(): void
    {
        $dateToday    = new \DateTime('now', new \DateTimeZone('UTC'));
        $dateTodayStr = $dateToday->format('Y-m-d');
        // For the publishDate, add 15 minutes offset
        $publishDate = clone $dateToday;
        $publishDate->add(new \DateInterval('PT15M'));
        $idx = 0;
        foreach ($this->LitCalData as $value) {
            // API returns RFC 3339 datetime strings like "2018-05-21T00:00:00+00:00"
            // Extract date portion (YYYY-MM-DD) directly from string for performance
            $dateValue    = isset($value['date']) && is_string($value['date']) ? $value['date'] : '';
            $eventDateStr = substr($dateValue, 0, 10);
            if ($eventDateStr === $dateTodayStr) {
                // retransform each entry from an associative array to a LiturgicalEvent class object
                $event                                     = new LiturgicalEvent($value);
                ['mainText' => $mainText, 'ssml' => $ssml] = $this->prepareMainText($event, $idx);
                $titleText                                 = _('Liturgy of the Day') . ' ';
                if ($this->baseLocale === LitLocale::ENGLISH) {
                    $titleText .= $event->date->format('F jS');
                } else {
                    $titleText .= $this->monthDayFmt->format($event->date->format('U'));
                }
                $this->LitCalFeed[] = new LitCalFeedItem($event, $publishDate, $titleText, $mainText, $ssml);
                $idx++;
            }
        }
    }

    private static function detectRomanNumeral(string $str): int|false
    {
        if (preg_match(self::ROMAN_NUMERAL_PATTERN_1_34, $str, $matches) === 1) {
            return self::ROMAN_TO_ARABIC_MAPPING[$matches[1]];
        }
        return false;
    }

    /**
     * Prepares the main text for a given LiturgicalEvent, given its index in the LitCalFeed array.
     *
     * This function takes into account the grade of the event and its relative position in the LitCalFeed array
     * and returns a string that can be used as the main text for that event.
     *
     * @param LiturgicalEvent $event The LiturgicalEvent to generate the main text for.
     * @param int $idx The index of the LiturgicalEvent in the LitCalFeed array.
     * @return array{mainText: string, ssml: string|null} A two-element array containing the main text and the SSML string.
     */
    private function prepareMainText(LiturgicalEvent $event, int $idx): array
    {
        $mainText = '';
        $ssml     = null;
        //Situations in which we don't need to actually state "Feast of the Lord":
        $filterTagsDisplayGrade   = [
            '/OrdSunday[0-9]{1,2}(_vigil){0,1}/',
            '/Advent[1-4](_vigil){0,1}/',
            '/Lent[1-5](_vigil){0,1}/',
            '/Easter[1-7](_vigil){0,1}/'
        ];
        $isSundayOrdAdvLentEaster = false;
        foreach ($filterTagsDisplayGrade as $pattern) {
            if (preg_match($pattern, $event->tag) === 1) {
                $isSundayOrdAdvLentEaster = true;
                break;
            }
        }

        if ($isSundayOrdAdvLentEaster) {
            $startsWithRoman = self::detectRomanNumeral($event->name);
            if ($startsWithRoman !== false) {
                $replacement  = $this->numberFormatter->format($startsWithRoman);
                $replacedName = preg_replace(self::ROMAN_NUMERAL_PATTERN_1_34, $replacement . ' ', $event->name);
                $event->name  = $replacedName ?? $event->name;
            }
        }

        if ($event->grade === LitGrade::WEEKDAY->value) {
            $mainText = _('Today is') . ' ' . $event->name . '.';
        } else {
            if ($event->isVigilMass) {
                if ($isSundayOrdAdvLentEaster) {
                    $mainText = sprintf(
                        /**translators: 1. name of the festivity */
                        _('This evening there will be a Vigil Mass for the %1$s.'),
                        trim(str_replace(_('Vigil Mass'), '', $event->name))
                    );
                } else {
                    $gradeEnum = LitGrade::tryFrom($event->grade);
                    $mainText  = sprintf(
                        /**translators: 1. grade of the festivity, 2. name of the festivity */
                        _('This evening there will be a Vigil Mass for the %1$s %2$s.'),
                        $gradeEnum?->i18n($this->Locale, false) ?? '',
                        trim(str_replace(_('Vigil Mass'), '', $event->name))
                    );
                }
            } elseif ($event->grade < LitGrade::HIGHER_SOLEMNITY->value) {
                if ($event->displayGrade !== null) {
                    if ($event->displayGrade === '') {
                        $mainText = sprintf(
                            /**translators: 1. (also|''), 2. name of the festivity */
                            _('Today is %1$s the %2$s.'),
                            ( $idx > 0 ? _('also') : '' ),
                            $event->name
                        );
                    } else {
                        $mainText = sprintf(
                            /**translators: 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _('Today is %1$s the %2$s of %3$s.'),
                            ( $idx > 0 ? _('also') : '' ),
                            $event->displayGrade,
                            $event->name
                        );
                    }
                } else {
                    if ($event->grade === LitGrade::FEAST_LORD->value) {
                        if ($isSundayOrdAdvLentEaster) {
                            $mainText = sprintf(
                                /**translators: CTXT: Sundays. 1. (also|''), 2. name of the festivity */
                                _('Today is %1$s the %2$s.'),
                                ( $idx > 0 ? _('also') : '' ),
                                $event->name
                            );
                        } else {
                            $mainText = sprintf(
                                /**translators: CTXT: Feast of the Lord. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                                _('Today is %1$s the %2$s, %3$s.'),
                                ( $idx > 0 ? _('also') : '' ),
                                LitGrade::FEAST_LORD->i18n($this->Locale, false),
                                $event->name
                            );
                        }
                    } elseif (strpos($event->tag, 'SatMemBVM') !== false) {
                        $mainText = sprintf(
                            /**translators: CTXT: Saturday memorial BVM. 1. (also|''), 2. name of the festivity */
                            _('Today is %1$s the %2$s.'),
                            ( $idx > 0 ? _('also') : '' ),
                            $event->name
                        );
                    } else {
                        $gradeEnum = LitGrade::tryFrom($event->grade);
                        $mainText  = sprintf(
                            /**translators: CTXT: (optional) memorial or feast. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _('Today is %1$s the %2$s of %3$s.'),
                            ( $idx > 0 ? _('also') : '' ),
                            $gradeEnum?->i18n($this->Locale, false) ?? '',
                            $event->name
                        );
                    }
                }

                if ($event->grade < LitGrade::FEAST->value && !in_array(LitCommon::PROPRIO->value, $event->common, true)) {
                    $mainText = $mainText . ' ' . LitCommon::toReadableString($event->common, $this->Locale);
                }
            } else {
                $mainText = sprintf(
                    /**translators: CTXT: higher grade solemnity with precedence over other solemnities. 1. (also|''), 2. name of the festivity  */
                    _('Today is %1$s the day of %2$s.'),
                    ( $idx > 0 ? _('also') : '' ),
                    $event->name
                );
            }

            $mainText = preg_replace('/  +/', ' ', $mainText) ?? $mainText;
            if (array_key_exists($this->baseLocale, LiturgyOfTheDay::MANUAL_FIXES)) {
                foreach (LiturgyOfTheDay::MANUAL_FIXES[$this->baseLocale] as $pattern => $replacement) {
                    $mainText = preg_replace($pattern, $replacement, $mainText) ?? $mainText;
                }
            }

            // Create the <speak> root element
            $speak      = new \SimpleXMLElement('<speak></speak>');
            $voice      = $speak->addChild('voice');
            $lang       = $voice->addChild('lang', $mainText);
            $namespaces = [
                'xml' => 'http://www.w3.org/XML/1998/namespace'
            ];
            // translate PHP Locale identifier to Unicode BCP 47 locale identifiers
            $locale = str_replace('_', '-', $this->Locale);
            // https://developer.amazon.com/it-IT/docs/alexa/custom-skills/speech-synthesis-markup-language-ssml-reference.html#supported-locales-for-the-xmllang-attribute
            switch ($this->baseLocale) {
                case 'en':
                    // Supported voices: Ivy, Joanna, Joey, Justin, Kendra, Kimberly, Matthew, Salli
                    $voice->addAttribute('name', 'Joanna');
                    $lang->addAttribute('xml:lang', 'en-US', $namespaces['xml']);
                    break;
                case 'es':
                    // Supported voices: Conchita, Enrique, Lucia
                    $voice->addAttribute('name', 'Conchita');
                    $lang->addAttribute('xml:lang', 'es-ES', $namespaces['xml']);
                    break;
                case 'fr':
                    // Supported voices: Celine, Lea, Mathieu
                    $voice->addAttribute('name', 'Celine');
                    $lang->addAttribute('xml:lang', 'fr-FR', $namespaces['xml']);
                    break;
                case 'de':
                    // Supported voice: Hans, Marlene, Vicki
                    $voice->addAttribute('name', 'Marlene');
                    $lang->addAttribute('xml:lang', 'de-DE', $namespaces['xml']);
                    break;
                case 'it':
                    // Supported voices: Carla, Giorgio, Bianca
                    $voice->addAttribute('name', 'Carla');
                    $lang->addAttribute('xml:lang', 'it-IT', $namespaces['xml']);
                    break;
                case 'pt':
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
            $dom                     = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput       = false;
            $speakXml                = $speak->asXML();
            if ($speakXml !== false) {
                $dom->loadXML($speakXml);
                $ssml = $dom->saveXML($dom->documentElement);
                if ($ssml !== false) {
                    //Fix some phonetic pronunciations
                    foreach (LiturgyOfTheDay::PHONETIC_PRONUNCATION_MAPPING as $key => $value) {
                        if (preg_match($key, $mainText) === 1) {
                            $ssml = preg_replace($key, $value, $ssml) ?? $ssml;
                        }
                    }
                } else {
                    $ssml = null;
                }
            }
        }
        return ['mainText' => $mainText, 'ssml' => is_string($ssml) ? $ssml : null];
    }

    /**
     * Determines if the given timezone is valid.
     */
    private static function isValidTimezone(string $timezone): bool
    {
        return in_array($timezone, \DateTimeZone::listIdentifiers(), true);
    }

    /**
     * Sends the Alexa Flash Briefing response.
     *
     * This method will send either a single JSON object or an array of JSON objects, depending on the number of
     * LiturgicalEvent objects in the LitCalFeed array. If the LitCalFeed array contains only one event, it will
     * send the JSON representation of the single event. If the LitCalFeed array contains more than one event,
     * it will send the JSON representation of the LitCalFeed array itself.
     *
     * Uses PSR-7 Response and Laminas SapiEmitter for standards-compliant output.
     */
    private function sendResponse(): void
    {
        $emitter = new SapiEmitter();

        if (count($this->LitCalFeed) === 0) {
            $body     = $this->psr17Factory->createStream(
                json_encode(['error' => 'Missing data from response: LitCalFeed is empty']) ?: ''
            );
            $response = $this->psr17Factory->createResponse(500)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($body);
            $emitter->emit($response);
            return;
        }

        $data = count($this->LitCalFeed) === 1
            ? $this->LitCalFeed[0]
            : $this->LitCalFeed;

        $jsonData = json_encode($data);
        if ($jsonData === false) {
            error_log('Failed to encode response data: ' . json_last_error_msg());
            $body     = $this->psr17Factory->createStream(
                json_encode(['error' => 'Failed to encode response data']) ?: ''
            );
            $response = $this->psr17Factory->createResponse(500)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($body);
            $emitter->emit($response);
            return;
        }

        $body     = $this->psr17Factory->createStream($jsonData);
        $response = $this->psr17Factory->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);

        $emitter->emit($response);
    }

    /**
     * Initializes the LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay object.
     *
     * This method will call the other methods in the correct order to generate and send the Alexa Flash Briefing response.
     */
    public function init(): void
    {
        $this->sendReq();
        $this->prepareL10N();
        $this->filterEventsToday();
        $this->sendResponse();
    }
}
