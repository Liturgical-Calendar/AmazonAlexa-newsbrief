<?php

include './vendor/autoload.php';

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Emit a JSON error response with the given status code and message.
 */
$emitErrorResponse = static function (int $statusCode, string $message): void {
    $psr17Factory = new Psr17Factory();
    $emitter      = new SapiEmitter();

    $body     = $psr17Factory->createStream(json_encode(['error' => $message]) ?: '');
    $response = $psr17Factory->createResponse($statusCode)
        ->withHeader('Content-Type', 'application/json')
        ->withBody($body);

    $emitter->emit($response);
};

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.local', '.env.development', '.env.staging', '.env.production'], false);
$dotenv->safeLoad();

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    $protocol = isset($_ENV['API_PROTOCOL']) && is_string($_ENV['API_PROTOCOL']) ? $_ENV['API_PROTOCOL'] : null;
    $host     = isset($_ENV['API_HOST']) && is_string($_ENV['API_HOST']) ? $_ENV['API_HOST'] : null;
    $port     = isset($_ENV['API_PORT']) && is_string($_ENV['API_PORT']) ? $_ENV['API_PORT'] : null;
    if ($protocol === null || $host === null || $port === null) {
        $emitErrorResponse(500, 'API_PROTOCOL, API_HOST and API_PORT must be defined in .env.development or similar dotenv when APP_ENV=development');
        exit(1);
    }
    $apiUrl = "{$protocol}://{$host}:{$port}";
} else {
    $apiUrl = 'https://litcal.johnromanodorazio.com/api/dev';
}

// Create PSR-16 filesystem cache with graceful fallback
$cache = null;
try {
    $cacheDir        = __DIR__ . '/cache';
    $filesystemCache = new FilesystemAdapter('litcal', 0, $cacheDir);
    $cache           = new Psr16Cache($filesystemCache);
} catch (\Throwable $e) {
    error_log('Failed to initialize cache: ' . $e->getMessage());
    // Continue without cache - LiturgyOfTheDay handles null cache gracefully
}

try {
    $LiturgyOfTheDay = new LiturgyOfTheDay($apiUrl, null, $cache);
    $LiturgyOfTheDay->init();
} catch (\InvalidArgumentException $e) {
    error_log('Validation error: ' . $e->getMessage());
    $emitErrorResponse(400, $e->getMessage());
    exit(1);
} catch (\RuntimeException $e) {
    error_log('Runtime error: ' . $e->getMessage());
    $emitErrorResponse(502, $e->getMessage());
    exit(1);
} catch (\Throwable $e) {
    error_log('Unexpected error: ' . $e->getMessage());
    $emitErrorResponse(500, 'An unexpected error occurred');
    exit(1);
}
