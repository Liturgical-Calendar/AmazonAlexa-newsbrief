# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**AmazonAlexa-newsbrief** is a reusable PHP library that provides Alexa news brief integration for Liturgy of the Day.
Designed as a Composer-installable package to power Alexa news brief skills that deliver daily liturgical information.

**Deployed Variations:**

- USA (4 timezone feeds)
- Italy (national calendar)
- Rome diocese (diocesan calendar)

## Main Technologies

- **Language:** PHP 8.2+
- **Package Manager:** Composer
- **Framework:** PSR-4 autoloaded library
- **Localization:** GNU gettext + custom pgettext
- **Environment:** vlucas/phpdotenv
- **Quality Tools:** PHPStan (level 10), PHP_CodeSniffer (PSR-12), CaptainHook

## PSR Compliance

This library implements the following PSR standards:

| PSR     | Description          | Implementation                              |
|---------|----------------------|---------------------------------------------|
| PSR-4   | Autoloading          | Composer autoloader                         |
| PSR-7   | HTTP Messages        | `nyholm/psr7` for Request/Response objects  |
| PSR-16  | Simple Cache         | `symfony/cache` with filesystem adapter     |
| PSR-17  | HTTP Factories       | `Nyholm\Psr7\Factory\Psr17Factory`          |
| PSR-18  | HTTP Client          | `guzzlehttp/guzzle` for API requests        |

## Caching

API responses are cached using PSR-16 Simple Cache with a filesystem adapter:

| Cache Target     | TTL         | Description                                |
|------------------|-------------|--------------------------------------------|
| Metadata         | 1 week      | Calendar list from `/calendars` endpoint   |
| Calendar data    | 1 day       | Liturgical events from `/calendar` endpoint|

Cache is stored in the `/cache/` directory (gitignored). Cache is optional and can be
disabled by not passing a `CacheInterface` to the constructor.

## Project Structure

```text
AmazonAlexa-newsbrief/
├── src/
│   ├── LiturgyOfTheDay.php    # Main library class
│   ├── LitCalFeedItem.php     # JSON-serializable feed item model
│   ├── LiturgicalEvent.php    # Liturgical event data model
│   ├── Utilities.php          # Helper methods
│   ├── pgettext.php           # Custom translation function
│   └── Enum/                  # Type-safe enums
│       ├── EnumToArrayTrait.php
│       ├── LitColor.php
│       ├── LitCommon.php
│       ├── LitEventType.php
│       ├── LitGrade.php
│       ├── LitLocale.php
│       └── LitMassVariousNeeds.php
├── index.php                   # Entry point
├── i18n/                       # Translation files
│   ├── litcal.pot             # Template
│   └── {locale}/LC_MESSAGES/  # Translated files
├── composer.json               # Package configuration
├── phpcs.xml                   # Code standards config
├── phpstan.neon.dist           # PHPStan configuration (checked in)
└── captainhook.json            # Git hooks configuration
```

## Development Commands

```bash
# Install dependencies
composer install

# Environment configuration
cp .env.example .env.development
# Set API_PROTOCOL, API_HOST, API_PORT
# Production: https://litcal.johnromanodorazio.com

# Start dev server
php -S localhost:3002

# Code quality
composer lint              # Check code style (phpcs)
composer lint:fix          # Auto-fix code style (phpcbf)
composer lint:md           # Lint markdown files
composer lint:md:fix       # Auto-fix markdown issues
composer analyse           # PHPStan static analysis
composer parallel-lint     # PHP syntax checking
```

## Usage as Package

```bash
composer require liturgical-calendar/alexa-newsbrief
```

```php
use LiturgicalCalendar\AlexaNewsBrief\LiturgyOfTheDay;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

// Optional: Configure PSR-16 cache
$filesystemCache = new FilesystemAdapter('litcal', 0, '/path/to/cache');
$cache           = new Psr16Cache($filesystemCache);

// Initialize with API URL, optional HTTP client, optional cache
$liturgy = new LiturgyOfTheDay($apiUrl, null, $cache);
$liturgy->init();  // Fetches data, initializes translations, generates feed items
```

## Code Standards

### PHP

- **Standard:** PSR-12 with 170-character line limit
- **Configuration:** `phpcs.xml`
- **Static Analysis:** PHPStan level 10 (`phpstan.neon.dist`)
- **Git Hooks:** CaptainHook (`captainhook.json`)

```bash
composer lint              # Check code style
composer analyse           # Static analysis
composer parallel-lint     # Syntax checking
```

### Markdown

All markdown files must conform to `.markdownlint.yml`:

- **Line length:** Maximum 180 characters (code blocks and tables excluded)
- **Tables:** Columns must be vertically aligned (MD060)
- **Code blocks:** Use fenced style with language specifiers

**Note:** Node.js and npm are required to run markdown linting scripts (uses `npx markdownlint-cli2`).

```bash
composer lint:md           # Check markdown style
composer lint:md:fix       # Auto-fix markdown issues
```

## Key Files

| File                      | Purpose                              |
|---------------------------|--------------------------------------|
| `src/LiturgyOfTheDay.php` | Main library class                   |
| `src/LitCalFeedItem.php`  | Feed item model for Alexa            |
| `src/LiturgicalEvent.php` | Liturgical event data model          |
| `phpcs.xml`               | PHP CodeSniffer configuration        |
| `phpstan.neon.dist`       | PHPStan static analysis config       |
| `captainhook.json`        | Git hooks configuration              |

## Internationalization

Supports 8 languages:
it, es, fr, de, pt, la, nl, sk

Translation files in `i18n/{locale}/LC_MESSAGES/`

## CI/CD

- GitHub Actions: `.github/workflows/main.yml`
- Automatic POT file generation on push to main
- Uses xgettext to extract strings from source files
- Weblate integration for community translations

## Composer Scripts

| Script             | Description                           |
|--------------------|---------------------------------------|
| `lint`             | Check code style with phpcs           |
| `lint:fix`         | Auto-fix code style with phpcbf       |
| `lint:md`          | Lint markdown files                   |
| `lint:md:fix`      | Auto-fix markdown issues              |
| `analyse`          | Run PHPStan static analysis           |
| `parallel-lint`    | Check PHP syntax                      |
| `post-install-cmd` | Triggers `Utilities::postInstall()`   |
| `post-update-cmd`  | Triggers `Utilities::postInstall()`   |

## Important Notes

- **Packagist package:** `liturgical-calendar/alexa-newsbrief`
- **Three deployed skills** in Amazon Alexa Skill Store
- **SSML support** - Speech Synthesis Markup Language for pronunciation
- **Timezone handling** - Converts for regional feeds
- **Roman numeral conversion** - For ordinal number display
