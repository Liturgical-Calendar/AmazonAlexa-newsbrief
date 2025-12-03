<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitColor;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitEventType;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;

/**
 * Represents a liturgical event/celebration.
 *
 * Similar to the class used in the Liturgical Calendar API,
 * except that it converts RFC 3339 datetime strings to DateTime objects
 * and does not implement JsonSerializable or a comparator function.
 *
 * @author  John R. D'Orazio <priest@johnromanodorazio.com>
 * @package LiturgicalCalendar\AlexaNewsBrief
 */
class LiturgicalEvent
{
    public string $tag;
    public string $name;
    public \DateTime $date;

    /** @var array<string> */
    public array $color;
    public string $type;
    public int $grade;
    public ?string $displayGrade;

    /** @var array<string> */
    public array $common;
    public string $liturgicalYear;
    public bool $isVigilMass;


    /**
     * Constructor for LiturgicalEvent class.
     *
     * @param array<string, mixed> $event an array representing a liturgical event, with the following keys:
     *      - event_key {string}: the unique identifier for the event
     *      - name {string}: the name of the event
     *      - date {string}: an RFC 3339 datetime string representing the date (e.g. "2018-05-21T00:00:00+00:00")
     *      - color {array}: an array of strings representing the liturgical color(s)
     *      - type {string}: whether the event is "mobile" or "fixed"
     *      - grade {int}: the liturgical grade (e.g. 7=HIGHER_SOLEMNITY, 6=SOLEMNITY, 5=FEAST_LORD, etc.)
     *      - grade_display {?string}: the localized version of the grade for display on frontend applications
     *        (e.g. "Feast of the Lord", "Memorial", etc.)
     *      - common {array}: an array of strings representing the common(s)
     *      - liturgical_year {string}: the liturgical year (e.g. "A", "B", etc.)
     *      - is_vigil_mass {bool}: boolean indicating if the event is a vigil Mass
     */
    public function __construct(array $event)
    {
        $eventKey     = is_string($event['event_key']) ? $event['event_key'] : '';
        $name         = is_string($event['name']) ? $event['name'] : '';
        $date         = is_string($event['date']) ? $event['date'] : 'now';
        $color        = is_array($event['color']) ? $event['color'] : [];
        $type         = is_string($event['type']) ? $event['type'] : '';
        $grade        = is_int($event['grade']) ? $event['grade'] : -1;
        $displayGrade = isset($event['grade_display']) && is_string($event['grade_display'])
            ? $event['grade_display']
            : null;
        $common       = is_array($event['common']) ? $event['common'] : [];
        $liturgYear   = isset($event['liturgical_year']) && is_string($event['liturgical_year'])
            ? $event['liturgical_year']
            : '';
        $isVigilMass  = isset($event['is_vigil_mass']) && is_bool($event['is_vigil_mass'])
            ? $event['is_vigil_mass']
            : false;

        /** @var array<string> $colorStrings */
        $colorStrings = array_filter($color, 'is_string');
        /** @var array<string> $commonStrings */
        $commonStrings = array_filter($common, 'is_string');

        $this->tag            = $eventKey;
        $this->name           = $name;
        $this->date           = new \DateTime($date);
        $this->color          = LitColor::areValid($colorStrings) ? $colorStrings : ['???'];
        $this->type           = LitEventType::isValid($type) ? $type : '';
        $this->grade          = LitGrade::isValid($grade) ? $grade : -1;
        $this->displayGrade   = $displayGrade;
        $this->common         = LitCommon::areValidCommons($commonStrings) ? $commonStrings : [];
        $this->liturgicalYear = $liturgYear;
        $this->isVigilMass    = $isVigilMass;
    }
}
