<?php

//ini_set( 'display_errors', 1 );
//ini_set( 'display_startup_errors', 1 );
//error_reporting( E_ALL );

include './vendor/autoload.php';

use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;

$LiturgyOfTheDay = new LiturgyOfTheDay();
$LiturgyOfTheDay->init();

die();
