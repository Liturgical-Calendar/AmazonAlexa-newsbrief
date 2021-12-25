<?php
include_once( 'includes/Festivity.php' );

class LitCalFeedItem {

    public string $uid;
    public string $titleText;
    public string $mainText;
    public string $redirectionUrl;
    public string $updateDate;

    public function __construct( string $key, Festivity $festivity, DateTime $publishDate, string $mainText ) {
        $this->uid = "urn:uuid:" . md5( "LITCAL-" . $key . '-' . $festivity->date->format( 'Y' ) );
        $this->updateDate       = $publishDate->format( "Y-m-d\TH:i:s\Z" );
        $this->titleText        = _( "Liturgy of the Day " ) . $festivity->date->format( 'F jS' );
        $this->mainText         = $mainText;
        $this->redirectionUrl   = "https://litcal.johnromanodorazio.com/";
    }

}