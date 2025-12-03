<?php

include './vendor/autoload.php';

use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.local', '.env.development', '.env.staging', '.env.production'], false);
$dotenv->safeLoad();

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    $protocol = isset($_ENV['API_PROTOCOL']) && is_string($_ENV['API_PROTOCOL']) ? $_ENV['API_PROTOCOL'] : null;
    $host     = isset($_ENV['API_HOST']) && is_string($_ENV['API_HOST']) ? $_ENV['API_HOST'] : null;
    $port     = isset($_ENV['API_PORT']) && is_string($_ENV['API_PORT']) ? $_ENV['API_PORT'] : null;
    if ($protocol === null || $host === null || $port === null) {
        die('API_PROTOCOL, API_HOST and API_PORT must be defined in .env.development or similar dotenv when APP_ENV=development');
    }
    $apiUrl = "{$protocol}://{$host}:{$port}";
} else {
    $apiUrl = 'https://litcal.johnromanodorazio.com/api/dev';
}

// Create PSR-16 filesystem cache
$cacheDir        = __DIR__ . '/cache';
$filesystemCache = new FilesystemAdapter('litcal', 0, $cacheDir);
$cache           = new Psr16Cache($filesystemCache);

$LiturgyOfTheDay = new LiturgyOfTheDay($apiUrl, null, $cache);
$LiturgyOfTheDay->init();

die();
