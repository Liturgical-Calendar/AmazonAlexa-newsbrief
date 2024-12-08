<?php
error_reporting(-1);
ini_set('display_errors', '1');

include './vendor/autoload.php';

use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;

$LiturgyOfTheDay = new LiturgyOfTheDay();
$LiturgyOfTheDay->init();

die();
