<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitLocale;
use LiturgicalCalendar\AlexaNewsBrief\Festivity;
use LiturgicalCalendar\AlexaNewsBrief\LitCalFeedItem;

class LiturgyOfTheDay
{
    private const METADATA_URL          = 'https://litcal.johnromanodorazio.com/api/dev/calendars';
    private const LITCAL_URL            = 'https://litcal.johnromanodorazio.com/api/dev/calendar';
    private string $CalendarURL         = LiturgyOfTheDay::LITCAL_URL;
    private string $Locale              = LitLocale::LATIN;
    private LitCommon $LitCommon;
    private LitGrade $LitGrade;
    private ?string $NationalCalendar   = null;
    private ?string $DiocesanCalendar   = null;
    private array $LitCalMetadata       = [];
    private array $LitCalData           = [];
    private array $LitCalFeed           = [];
    private \IntlDateFormatter $monthDayFmt;
    private array $queryParams          = [];
    //private string $logFile = 'debug.log';

    public function __construct()
    {
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
            $this->CalendarURL = self::LITCAL_URL . '/nation/' . $this->NationalCalendar;
            $NationalCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['national_calendars'],
                fn($nationalCalendar) => $nationalCalendar['calendar_id'] === $this->NationalCalendar
            ))[0];
            if ($this->NationalCalendar !== 'VA') {
                $this->Locale = $NationalCalendarMetadata["settings"]["locale"];
            }
        }

        if (isset($_GET["diocesancalendar"]) && $_GET["diocesancalendar"] !== "") {
            if (false === in_array($_GET["diocesancalendar"], $this->LitCalMetadata['diocesan_calendars_keys'])) {
                die("Request failed. Requested diocesan calendar '{$_GET["diocesancalendar"]}' is not supported. Supported diocesan calendars are: "
                    . implode(', ', $this->LitCalMetadata['diocesan_calendars_keys']));
            }
            $this->DiocesanCalendar = $_GET["diocesancalendar"];
            $this->CalendarURL = self::LITCAL_URL . '/diocese/' . $this->DiocesanCalendar;
            $DiocesanCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['diocesan_calendars'],
                fn($diocesanCalendar) => $diocesanCalendar['calendar_id'] === $this->DiocesanCalendar
            ))[0];
            $NationalCalendarMetadata = array_values(array_filter(
                $this->LitCalMetadata['national_calendars'],
                fn($nationalCalendar) => $nationalCalendar['calendar_id'] === $DiocesanCalendarMetadata["nation"]
            ))[0];
            $this->Locale = $NationalCalendarMetadata["settings"]["locale"];
        }

        if (isset($_GET["timezone"]) && LiturgyOfTheDay::isValidTimezone($_GET["timezone"])) {
            ini_set('date.timezone', $_GET["timezone"]);
        } else {
            ini_set('date.timezone', 'Europe/Vatican');
        }
    }

    private function prepareL10N(): void
    {
        $baseLocale = \Locale::getPrimaryLanguage($this->Locale);
        $localeArray = [
            $this->Locale . '.utf8',
            $this->Locale . '.UTF-8',
            $baseLocale . '.utf8',
            $baseLocale . '.UTF-8',
            $this->Locale
        ];
        setlocale(LC_ALL, $localeArray);
        bindtextdomain("litcal", "i18n");
        textdomain("litcal");
        $this->LitCommon    = new LitCommon($this->Locale);
        $this->LitGrade     = new LitGrade($this->Locale);
        $this->monthDayFmt  = \IntlDateFormatter::create($this->Locale, \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, 'UTC', \IntlDateFormatter::GREGORIAN, 'd MMMM');
    }

    private function sendMetadataReq(): void
    {
        $ch = curl_init(self::METADATA_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Could not send request. Curl error: " . curl_error($ch));
        }

        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus !== 200) {
            if ($resultStatus === 412) {
                die("the index.json file simply doesn't exist yet");
            } else {
                die("Metadata request failed. HTTP status code: " . $resultStatus);
            }
        }

        $response = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            die("Metadata request failed. Could not decode metadata JSON data. " . json_last_error_msg());
        }

        ["litcal_metadata" => $this->LitCalMetadata] = $response;

        curl_close($ch);
    }


    private function sendReq()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->CalendarURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->queryParams));
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

    private function filterEventsToday()
    {
        $dateTimeToday = ( new \DateTime('now') )->format("Y-m-d") . " 00:00:00";
        $dateToday = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeToday, new \DateTimeZone('UTC'));
        $dateTodayTimestamp = intval($dateToday->format("U"));
        $dateToday->add(new \DateInterval('PT10M'));
        $idx = 0;
        foreach ($this->LitCalData as $key => $value) {
            //file_put_contents( $this->logFile, "Processing litcal event $key..." . "\n", FILE_APPEND );
            if ($value["date"] === $dateTodayTimestamp) {
                //file_put_contents( $this->logFile, "Found litcal event $key with timestamp equal to today!" . "\n", FILE_APPEND );
                $publishDate = $dateToday->sub(new \DateInterval('PT1M'));
                // retransform each entry from an associative array to a Festivity class object
                $festivity = new Festivity($value);
                $festivity->tag = $key;
                $mainText = $this->prepareMainText($festivity, $idx);
                //file_put_contents( $this->logFile, "mainText = $mainText" . "\n", FILE_APPEND );
                $titleText = _("Liturgy of the Day") . " ";
                if ($this->Locale === LitLocale::ENGLISH) {
                    $titleText .= $festivity->date->format('F jS');
                } else {
                    $titleText .= $this->monthDayFmt->format($festivity->date->format('U'));
                }
                $this->LitCalFeed[] = new LitCalFeedItem($key, $festivity, $publishDate, $titleText, $mainText);
                $idx++;
            }
        }
    }

    private function prepareMainText(Festivity $festivity, int $idx): string
    {
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
                if ($festivity->displayGrade != "") {
                    $mainText = sprintf(
                        /**translators: 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                        _('Today is %1$s the %2$s of %3$s.'),
                        ( $idx > 0 ? _("also") : "" ),
                        $festivity->displayGrade,
                        $festivity->name
                    );
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
        }
        return $mainText;
    }

    private function isValidTimezone($timezone)
    {
        if (in_array($timezone, \DateTimeZone::listIdentifiers())) {
            return true;
        }
        return false;
    }

    private function sendResponse()
    {
        header('Content-Type: application/json');
        if (count($this->LitCalFeed) === 1) {
            echo json_encode($this->LitCalFeed[0]);
        } elseif (count($this->LitCalFeed) > 1) {
            echo json_encode($this->LitCalFeed);
        }
    }

    public function init()
    {
        $this->sendReq();
        $this->prepareL10N();
        $this->filterEventsToday();
        $this->sendResponse();
    }
}
