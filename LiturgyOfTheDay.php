<?php

//ini_set( 'display_errors', 1 );
//ini_set( 'display_startup_errors', 1 );
//error_reporting( E_ALL );
include_once( 'includes/enums/LitCommon.php' );
include_once( 'includes/enums/LitGrade.php' );
include_once( 'includes/Festivity.php' );

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
    private array $LitCal               = [];
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

    private function prepareReq() {
        if( $this->Locale !== null ){
            $this->queryArray["locale"] = $this->Locale;
        }
        if( $this->NationalCalendar !== null && in_array( $this->NationalCalendar, $this->SUPPORTED_NATIONS ) ) {
            $this->queryArray["nationalcalendar"] = $this->NationalCalendar;
            switch( $this->NationalCalendar ) {
                case "ITALY":
                    $this->queryArray["locale"] = "IT";
                break;
                case "USA":
                    $this->queryArray["locale"] = "EN";
                break;
            }
        }
        if( $this->DiocesanCalendar !== null && array_key_exists( $this->DiocesanCalendar, $this->SUPPORTED_DIOCESES ) ) {
            $this->queryArray["diocesancalendar"] = $this->DiocesanCalendar;
            $this->queryArray["nationalcalendar"] = $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"];
            switch( $this->SUPPORTED_DIOCESES[$this->DiocesanCalendar]["nation"] ) {
                case "ITALY":
                    $this->queryArray["locale"] = "IT";
                break;
                case "USA":
                    $this->queryArray["locale"] = "EN";
                break;
            }
        }
        
        //last resort is Latin for the Universal Calendar
        if( !isset( $this->queryArray["locale"] ) ) {
            $this->queryArray["locale"] = "LA";
        }
    }

    private function sendReq() {
        $ch = curl_init();
        // Disable SSL verification
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        // Will return the response, if false it print the response
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        //curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        // Set the url
        curl_setopt( $ch, CURLOPT_URL, $URL );
        // Set request method to POST
        curl_setopt( $ch, CURLOPT_POST, 1 );
        // Define the POST field data
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $this->queryArray ) );
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

    private function filterEventsDateToday() {
        $dateTimeToday = ( new DateTime( 'now' ) )->format( "Y-m-d" ) . " 00:00:00";
        $dateToday = DateTime::createFromFormat( 'Y-m-d H:i:s', $dateTimeToday, new DateTimeZone( 'UTC' ) );
        $dateTodayTimestamp = $dateToday->format( "U" );
        $dateToday->add( new DateInterval( 'PT10M' ) );
        // Gather the json results from the server into $LitCal array similar to the PHP Engine
        $idx = 0;
        if( isset( $this->LitCalData["LitCal"] ) ) {
            $LitCal = $this->LitCalData["LitCal"];
            foreach ( $LitCal as $key => $value ) {
                //fwrite( $logFile, "Processing litcal event $key..." . "\n" );
                if( $LitCal[$key]["date"] === $dateTodayTimestamp ) {
                    //fwrite( $logFile, "Found litcal event $key with timestamp equal to today!" . "\n" );
                    $publishDate = $dateToday->sub( new DateInterval( 'PT1M' ) )->format( "Y-m-d\TH:i:s\Z" );
                    // retransform each entry from an associative array to a Festivity class object
                    $this->LitCal[$key] = new Festivity(
                        $LitCal[$key]["name"],
                        $LitCal[$key]["date"],
                        $LitCal[$key]["color"],
                        $LitCal[$key]["type"],
                        $LitCal[$key]["grade"],
                        $LitCal[$key]["common"],
                        ( isset( $LitCal[$key]["liturgicalYear"] ) ? $LitCal[$key]["liturgicalYear"] : null ),
                        $LitCal[$key]["displayGrade"]
                    );
                    if( $this->LitCal[$key]->grade === LitGrade::WEEKDAY ){
                        //fwrite( $logFile, "we are dealing with a weekday event" . "\n" );
                        $mainText = _( "Today is" ) . " " . $this->LitCal[$key]->name . ".";
                    } else{ 
                        if( strpos( $this->LitCal[$key]->name, "Vigil" ) ){
                            //fwrite( $logFile, "we are dealing with a Vigil Mass" . "\n" );
                            $mainText = sprintf( _( "This evening there will be a Vigil Mass for the %s %s." ), $LitGrade->i18n( $this->LitCal[$key]->grade ), trim( str_replace( _( "Vigil Mass" ), "", $this->LitCal[$key]->name ) ) );
                        } else if( $this->LitCal[$key]->grade < LitGrade::HIGHER_SOLEMNITY ) {
                            //fwrite( $logFile, "we are dealing with something greater than a weekday but less than a higher ranking solemnity" . "\n" );

                            if( $this->LitCal[$key]->displayGrade != "" ){
                                $mainText = sprintf( _( "Today is %s the %s of %s." ), ( $idx > 0 ? _( "also" ) : "" ), $this->LitCal[$key]->displayGrade, $this->LitCal[$key]->name );
                            } else {
                                if( $this->LitCal[$key]->grade === LitGrade::FEAST_LORD ){
                                    $mainText = sprintf( _( "Today is %s the %s, %s." ), ( $idx > 0 ? _( "also" ) : "" ), $LitGrade->i18n( $this->LitCal[$key]->grade ), $this->LitCal[$key]->name );
                                } else {
                                    $mainText = sprintf( _( "Today is %s the %s of %s." ), ( $idx > 0 ? _( "also" ) : "" ), $LitGrade->i18n( $this->LitCal[$key]->grade ), $this->LitCal[$key]->name );
                                }
                            }
                            
                            if( $this->LitCal[$key]->grade < LitGrade::FEAST && $this->LitCal[$key]->common != LitCommon::PROPER ) {
                                //fwrite( $logFile, "we are dealing with something less than a Feast, and which has a common" . "\n" );
                                $mainText = $mainText . " " . $LitCommon->i18n( $this->LitCal[$key]->common );
                            }
                        } else {
                            $mainText = sprintf( _( "Today is %s the %s." ), ( $idx > 0 ? _( "also" ) : "" ), $this->LitCal[$key]->name );
                        }
                    }
                    //fwrite( $logFile, "mainText = $mainText" . "\n" );
                    $this->LitCalFeed[] = new stdClass();
                    $this->LitCalFeed[count( $this->LitCalFeed )-1]->uid = "urn:uuid:" . md5( "LITCAL-" . $key . '-' . $this->LitCal[$key]->date->format( 'Y' ) );
                    $this->LitCalFeed[count( $this->LitCalFeed )-1]->updateDate = $publishDate;
                    $this->LitCalFeed[count( $this->LitCalFeed )-1]->titleText = _( "Liturgy of the Day " ) . $this->LitCal[$key]->date->format( 'F jS' );
                    $this->LitCalFeed[count( $this->LitCalFeed )-1]->mainText = $mainText;
                    $this->LitCalFeed[count( $this->LitCalFeed )-1]->redirectionUrl = "https://litcal.johnromanodorazio.com/";
                    ++$idx;
                }
            }            
        }
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
        $this->prepareReq();
        $this->sendReq();
        $this->filterEventsDateToday();
        $this->sendResponse();
    }

}

$LiturgyOfTheDay = new LiturgyOfTheDay();
$LiturgyOfTheDay->Init();

die();
?>
