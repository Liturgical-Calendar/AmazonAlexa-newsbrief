<?php

include './vendor/autoload.php';

use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.local', '.env.development', '.env.production'], false);
$dotenv->safeLoad();

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    if (false === isset($_ENV['API_PROTOCOL']) || false === isset($_ENV['API_HOST']) || false === isset($_ENV['API_PORT'])) {
        die("API_PROTOCOL, API_HOST and API_PORT must be defined in .env.development or similar dotenv when APP_ENV=development");
    }
    $apiUrl = "{$_ENV['API_PROTOCOL']}://{$_ENV['API_HOST']}:{$_ENV['API_PORT']}";
} else {
    $apiUrl = "https://litcal.johnromanodorazio.com/api/dev";
}

$LiturgyOfTheDay = new LiturgyOfTheDay($apiUrl);
$LiturgyOfTheDay->init();

die();
