<?php

include './vendor/autoload.php';

use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;

$LiturgyOfTheDay = new LiturgyOfTheDay();
$LiturgyOfTheDay->init();

die();
