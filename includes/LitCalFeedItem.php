<?php
include_once( 'includes/Festivity.php' );

class LitCalFeedItem {

    private string $uid;
    private string $titleText;
    private string $mainText;
    private string $redirectionUrl;
    private DateTime $updateDate;

    public function __construct( string $key, Festivity $festivity, DateTime $publishDate, $mainText ) {
        $this->uid = "urn:uuid:" . md5( "LITCAL-" . $key . '-' . $festivity->date->format( 'Y' ) );
        $this->updateDate       = $publishDate;
        $this->titleText        = _( "Liturgy of the Day " ) . $festivity->date->format( 'F jS' );
        $this->mainText         = $mainText;
        $this->redirectionUrl   = "https://litcal.johnromanodorazio.com/";
    }

}