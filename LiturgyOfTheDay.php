<?php

//ini_set( 'display_errors', 1 );
//ini_set( 'display_startup_errors', 1 );
//error_reporting( E_ALL );
include_once( 'includes/enums/LitCommon.php' );
include_once( 'includes/enums/LitGrade.php' );
include_once( 'includes/Festivity.php' );
include_once( 'includes/LitCalFeedItem.php' );

class LiturgyOfTheDay {

    const METADATA_URL  = 'https://litcal.johnromanodorazio.com/api/v3/LitCalMetadata.php';
    const LITCAL_URL    = 'https://litcal.johnromanodorazio.com/api/v3/LitCalEngine.php';
    private LitCommon $LitCommon;
    private LitGrade $LitGrade;
    private ?string $Locale             = null;
    private ?string $NationalCalendar   = null;
    private ?string $DiocesanCalendar   = null;
    private ?string $Timezone           = null;
    private array $SUPPORTED_DIOCESES   = [];
    private array $SUPPORTED_NATIONS    = [];
    private array $queryArray           = [];
    private array $LitCalData           = [];
    private array $LitCalFeed           = [];

    public function __construct() {
        $this->Locale = isset( $_GET["locale"] ) ? strtoupper( $_GET["locale"] ) : null;
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
    }

    private function sendMetadataReq() : void {
        $ch = curl_init( self::METADATA_URL );
        // Disable SSL verification
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        // Will return the response, if false it print the response
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        //curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        // Execute
        $result = curl_exec( $ch );
        
        if ( curl_errno( $ch ) ) {
            // this would be your first hint that something went wrong
            die( "Could not send request. Curl error: " . curl_error( $ch ) );
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if ( $resultStatus !== 200 ) {
                // the request did not complete as expected. common errors are 4xx
                // ( not found, bad request, etc. ) and 5xx ( usually concerning
                // errors/exceptions in the remote script execution )
                if( $resultStatus === 412 ){
                    die( "the index.json file simply doesn't exist yet" );
                } else {
                    die( "Request failed. HTTP status code: " . $resultStatus );
                }
            } else {
                //we have results from the metadata endpoint
                $this->SUPPORTED_DIOCESES = json_decode( $result, true );
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
                    $queryArray["locale"] = "IT";
                break;
                case "USA":
                    $queryArray["locale"] = "EN";
                break;
            }
        }
        if( $this->DiocesanCalendar !== null && array_key_exists( $this->DiocesanCalendar, $this->SUPPORTED_DIOCESES ) ) {
            $queryArray["diocesancalendar"] = $this->DiocesanCalendar;
            $queryArray["nationalcalendar"] = $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"];
            switch( $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"] ) {
                case "ITALY":
                    $queryArray["locale"] = "IT";
                break;
                case "USA":
                    $queryArray["locale"] = "EN";
                break;
            }
        }
        
        //last resort is Latin for the Universal Calendar
        if( !isset( $queryArray["locale"] ) ) {
            $queryArray["locale"] = "LA";
        }
        return $queryArray;
    }

    private function sendReq( array $queryArray ) {
        $ch = curl_init();
        // Disable SSL verification
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        // Will return the response, if false it print the response
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        //curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        // Set the url
        curl_setopt( $ch, CURLOPT_URL, self::LITCAL_URL );
        // Set request method to POST
        curl_setopt( $ch, CURLOPT_POST, 1 );
        // Define the POST field data
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $queryArray ) );
        // Execute
        $result = curl_exec( $ch );
        
        if ( curl_errno( $ch ) ) {
            // this would be your first hint that something went wrong
            //fwrite( $logFile, curl_error( $ch ) . "\n" );
            die( "Could not send request. Curl error: " . curl_error( $ch ) );
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if ( $resultStatus != 200 ) {
                // the request did not complete as expected. common errors are 4xx
                // ( not found, bad request, etc. ) and 5xx ( usually concerning
                // errors/exceptions in the remote script execution )
                //fwrite( $logFile, "HTTP STATUS " . $resultStatus . "\n" );
                die( "Request failed. HTTP status code: " . $resultStatus );
            } else {
                //echo $result;
                //fwrite( $logFile, $result . "\n" );
                $this->LitCalData = json_decode( $result, true );
            }
        }
        
        // Closing
        curl_close( $ch );
    }

    private function filterEventsToday() {
        $dateTimeToday = ( new DateTime( 'now' ) )->format( "Y-m-d" ) . " 00:00:00";
        $dateToday = DateTime::createFromFormat( 'Y-m-d H:i:s', $dateTimeToday, new DateTimeZone( 'UTC' ) );
        $dateTodayTimestamp = $dateToday->format( "U" );
        $dateToday->add( new DateInterval( 'PT10M' ) );

        if( isset( $this->LitCalData["LitCal"] ) ) {
            $LitCal = $this->LitCalData["LitCal"];
            foreach ( $LitCal as $key => $value ) {
                //fwrite( $logFile, "Processing litcal event $key..." . "\n" );
                if( $LitCal[$key]["date"] === $dateTodayTimestamp ) {
                    //fwrite( $logFile, "Found litcal event $key with timestamp equal to today!" . "\n" );
                    $publishDate = $dateToday->sub( new DateInterval( 'PT1M' ) )->format( "Y-m-d\TH:i:s\Z" );
                    // retransform each entry from an associative array to a Festivity class object
                    $festivity = new Festivity( $LitCal[$key] );
                    $mainText = $this->prepareMainText( $festivity );
                    //fwrite( $logFile, "mainText = $mainText" . "\n" );
                    $this->LitCalFeed[] = new LitCalFeedItem( $key, $festivity, $publishDate, $mainText );
                }
            }            
        }
    }

    private function prepareMainText( Festivity $festivity ) : string {
        if( $festivity->grade === LitGrade::WEEKDAY ){
            //fwrite( $logFile, "we are dealing with a weekday event" . "\n" );
            $mainText = _( "Today is" ) . " " . $festivity->name . ".";
        } else{ 
            if( strpos( $festivity->name, "Vigil" ) ){
                //fwrite( $logFile, "we are dealing with a Vigil Mass" . "\n" );
                $mainText = sprintf( _( "This evening there will be a Vigil Mass for the %s %s." ), $this->LitGrade->i18n( $festivity->grade ), trim( str_replace( _( "Vigil Mass" ), "", $festivity->name ) ) );
            } else if( $festivity->grade < LitGrade::HIGHER_SOLEMNITY ) {
                //fwrite( $logFile, "we are dealing with something greater than a weekday but less than a higher ranking solemnity" . "\n" );
                if( $festivity->displayGrade != "" ){
                    $mainText = sprintf( _( "Today is %s the %s of %s." ), ( $idx > 0 ? _( "also" ) : "" ), $festivity->displayGrade, $festivity->name );
                } else {
                    if( $festivity->grade === LitGrade::FEAST_LORD ){
                        $mainText = sprintf( _( "Today is %s the %s, %s." ), ( $idx > 0 ? _( "also" ) : "" ), $this->LitGrade->i18n( $festivity->grade ), $festivity->name );
                    } else {
                        $mainText = sprintf( _( "Today is %s the %s of %s." ), ( $idx > 0 ? _( "also" ) : "" ), $this->LitGrade->i18n( $festivity->grade ), $festivity->name );
                    }
                }
                
                if( $festivity->grade < LitGrade::FEAST && $festivity->common != LitCommon::PROPER ) {
                    //fwrite( $logFile, "we are dealing with something less than a Feast, and which has a common" . "\n" );
                    $mainText = $mainText . " " . $this->LitCommon->i18n( $festivity->common );
                }
            } else {
                $mainText = sprintf( _( "Today is %s the %s." ), ( $idx > 0 ? _( "also" ) : "" ), $festivity->name );
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
        $this->prepareL10N();
        $this->sendMetadataReq();
        $queryArray = $this->prepareReq();
        $this->sendReq( $queryArray; );
        $this->filterEventsToday();
        $this->sendResponse();
    }

}

$LiturgyOfTheDay = new LiturgyOfTheDay();
$LiturgyOfTheDay->Init();

die();
?>
