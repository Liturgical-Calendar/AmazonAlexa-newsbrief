<?php

include_once( 'includes/enums/LitCommon.php' );
include_once( 'includes/enums/LitGrade.php' );
include_once( 'includes/enums/LitLocale.php' );
include_once( 'includes/Festivity.php' );
include_once( 'includes/LitCalFeedItem.php' );

class LiturgyOfTheDay {

    const METADATA_URL  = 'https://litcal.johnromanodorazio.com/api/v3/LitCalMetadata.php';
    const LITCAL_URL    = 'https://litcal.johnromanodorazio.com/api/v3/LitCalEngine.php';
    private LitCommon $LitCommon;
    private LitGrade $LitGrade;
    private string $Locale              = LitLocale::LATIN;
    private ?string $NationalCalendar   = null;
    private ?string $DiocesanCalendar   = null;
    private ?string $Timezone           = null;
    private array $SUPPORTED_DIOCESES   = [];
    private array $SUPPORTED_NATIONS    = [];
    private array $queryArray           = [];
    private array $LitCalData           = [];
    private array $LitCalFeed           = [];
    private IntlDateFormatter $monthDayFmt;

    public function __construct() {
        $this->Locale = isset( $_GET["locale"] ) ? strtoupper( $_GET["locale"] ) : LitLocale::LATIN;
        $this->NationalCalendar = isset( $_GET["nationalcalendar"] ) ? strtoupper( $_GET["nationalcalendar"] ) : null;
        $this->DiocesanCalendar = isset( $_GET["diocesancalendar"] ) ? strtoupper( $_GET["diocesancalendar"] ) : null;
        $this->Timezone = isset( $_GET["timezone"] ) ? $_GET["timezone"] : null;        
        if( $this->Timezone === null ) {
            ini_set( 'date.timezone', 'Europe/Vatican' );
        } else {
            ini_set( 'date.timezone', $this->Timezone );
        }
    }

    private function prepareL10N() : void {
        //die( 'nationalcalendar = ' . $this->NationalCalendar . ', Locale = ' . $this->Locale );
        $localeArray = [
            strtolower( $this->Locale ) . '_' . $this->Locale . '.utf8',
            strtolower( $this->Locale ) . '_' . $this->Locale . '.UTF-8',
            strtolower( $this->Locale ) . '_' . $this->Locale,
            strtolower( $this->Locale )
        ];
        setlocale( LC_ALL, $localeArray );
        bindtextdomain("litcal", "i18n");
        textdomain("litcal");
        $this->LitCommon    = new LitCommon( $this->Locale );
        $this->LitGrade     = new LitGrade( $this->Locale );
        $this->monthDayFmt  = IntlDateFormatter::create($this->Locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'UTC', IntlDateFormatter::GREGORIAN, 'd MMMM' );
    }

    private function sendMetadataReq() : void {
        $ch = curl_init( self::METADATA_URL );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        
        if ( curl_errno( $ch ) ) {
            die( "Could not send request. Curl error: " . curl_error( $ch ) );
        } else {
            $resultStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if ( $resultStatus !== 200 ) {
                if( $resultStatus === 412 ){
                    die( "the index.json file simply doesn't exist yet" );
                } else {
                    die( "Request failed. HTTP status code: " . $resultStatus );
                }
            } else {
                $response = json_decode( $result );
                $this->SUPPORTED_DIOCESES = (array) $response->LitCalMetadata->DiocesanCalendars;
                $this->SUPPORTED_NATIONS =  get_object_vars( $response->LitCalMetadata->NationalCalendars );
            }
        }
        curl_close( $ch );
    }

    private function prepareReq() : array {
        $queryArray = [];
        if( $this->Locale !== null ){
            $queryArray["locale"] = $this->Locale;
        }
        if( $this->NationalCalendar !== null && in_array( $this->NationalCalendar, $this->SUPPORTED_NATIONS ) ) {
            $queryArray["nationalcalendar"] = $this->NationalCalendar;
            switch( $this->NationalCalendar ) {
                case "ITALY":
                    $queryArray["locale"] = LitLocale::ITALIAN;
                    $this->Locale = LitLocale::ITALIAN;
                break;
                case "USA":
                    $queryArray["locale"] = LitLocale::ENGLISH;
                    $this->Locale = LitLocale::ENGLISH;
                break;
            }
        }
        if( $this->DiocesanCalendar !== null && array_key_exists( $this->DiocesanCalendar, $this->SUPPORTED_DIOCESES ) ) {
            $queryArray["diocesancalendar"] = $this->DiocesanCalendar;
            $queryArray["nationalcalendar"] = $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"];
            switch( $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"] ) {
                case "ITALY":
                    $queryArray["locale"] = LitLocale::ITALIAN;
                    $this->Locale = LitLocale::ITALIAN;
                break;
                case "USA":
                    $queryArray["locale"] = LitLocale::ENGLISH;
                    $this->Locale = LitLocale::ENGLISH;
                break;
            }
        }
        
        //last resort is Latin for the Universal Calendar
        if( !isset( $queryArray["locale"] ) ) {
            $queryArray["locale"] = LitLocale::LATIN;
            $this->Locale = LitLocale::LATIN;
        }
        return $queryArray;
    }

    private function sendReq( array $queryArray ) {
        //die( json_encode( $queryArray ) );
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_URL, self::LITCAL_URL );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $queryArray ) );
        $result = curl_exec( $ch );
        if ( curl_errno( $ch ) ) {
            die( "Could not send request. Curl error: " . curl_error( $ch ) );
        } else {
            $resultStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if ( $resultStatus != 200 ) {
                die( "Request failed. HTTP status code: " . $resultStatus );
            } else {
                $this->LitCalData = json_decode( $result, true );
            }
        }
        curl_close( $ch );
    }

    private function filterEventsToday() {
        $dateTimeToday = ( new DateTime( 'now' ) )->format( "Y-m-d" ) . " 00:00:00";
        $dateToday = DateTime::createFromFormat( 'Y-m-d H:i:s', $dateTimeToday, new DateTimeZone( 'UTC' ) );
        $dateTodayTimestamp = $dateToday->format( "U" );
        $dateToday->add( new DateInterval( 'PT10M' ) );
        if( isset( $this->LitCalData["LitCal"] ) ) {
            $LitCal = $this->LitCalData["LitCal"];
            $idx = 0;
            foreach ( $LitCal as $key => $value ) {
                //fwrite( $logFile, "Processing litcal event $key..." . "\n" );
                if( $LitCal[$key]["date"] === $dateTodayTimestamp ) {
                    //fwrite( $logFile, "Found litcal event $key with timestamp equal to today!" . "\n" );
                    $publishDate = $dateToday->sub( new DateInterval( 'PT1M' ) );
                    // retransform each entry from an associative array to a Festivity class object
                    $festivity = new Festivity( $LitCal[$key] );
                    $mainText = $this->prepareMainText( $festivity, $idx );
                    //fwrite( $logFile, "mainText = $mainText" . "\n" );
                    $titleText = _( "Liturgy of the Day" ) . " ";
                    if( $this->Locale === LitLocale::ENGLISH ) {
                        $titleText .= $festivity->date->format( 'F jS' );
                    } else {
                        $titleText .= $this->monthDayFmt->format( $festivity->date->format( 'U' ) );
                    }
                    $this->LitCalFeed[] = new LitCalFeedItem( $key, $festivity, $publishDate, $titleText, $mainText );
                    $idx++;
                }
            }            
        }
    }

    private function prepareMainText( Festivity $festivity, int $idx ) : string {
        if( $festivity->grade === LitGrade::WEEKDAY ) {
            $mainText = _( "Today is" ) . " " . $festivity->name . ".";
        } else{ 
            if( $festivity->isVigilMass ) {
                /**translators: grade, name */
                $mainText = sprintf( _( "This evening there will be a Vigil Mass for the %s %s." ), $this->LitGrade->i18n( $festivity->grade, false ), trim( str_replace( _( "Vigil Mass" ), "", $festivity->name ) ) );
            } else if( $festivity->grade < LitGrade::HIGHER_SOLEMNITY ) {
                if( $festivity->displayGrade != "" ) {
                    $mainText = sprintf(
                        /**translators: 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                        _( "Today is %$1s the %$2s of %$3s." ),
                        ( $idx > 0 ? _( "also" ) : "" ),
                        $festivity->displayGrade,
                        $festivity->name
                    );
                } else {
                    if( $festivity->grade === LitGrade::FEAST_LORD ) {
                        $mainText = sprintf(
                            /**translators: CTXT: Feast of the Lord. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _( "Today is %$1s the %$2s, %$3s." ),
                            ( $idx > 0 ? _( "also" ) : "" ),
                            $this->LitGrade->i18n( $festivity->grade, false ),
                            $festivity->name
                        );
                    } else {
                        $mainText = sprintf(
                            /**translators: CTXT: (optional) memorial or feast. 1. (also|''), 2. grade of the festivity, 3. name of the festivity */
                            _( "Today is %$1s the %$2s of %$3s." ),
                            ( $idx > 0 ? _( "also" ) : "" ),
                            $this->LitGrade->i18n( $festivity->grade, false ),
                            $festivity->name
                        );
                    }
                }
                
                if( $festivity->grade < LitGrade::FEAST && $festivity->common != LitCommon::PROPRIO ) {
                    $mainText = $mainText . " " . $this->LitCommon->i18n( $festivity->common );
                }
            } else {
                $mainText = sprintf(
                    /**translators: CTXT: higher grade solemnity with precedence over other solemnities. 1. (also|''), 2. name of the festivity  */
                    _( "Today is %$1s the day of %$2s." ),
                    ( $idx > 0 ? _( "also" ) : "" ),
                    $festivity->name
                );
            }
        }
        return $mainText;
    }

    private function sendResponse() {
        header( 'Content-Type: application/json' );
        if( count( $this->LitCalFeed ) === 1 ){
            echo json_encode( $this->LitCalFeed[0] );
        } else if( count( $this->LitCalFeed ) > 1 ){
            echo json_encode( $this->LitCalFeed );
        }
    }

    public function Init() {
        $this->sendMetadataReq();
        $queryArray = $this->prepareReq();
        $this->sendReq( $queryArray );
        $this->prepareL10N();
        $this->filterEventsToday();
        $this->sendResponse();
    }

}
