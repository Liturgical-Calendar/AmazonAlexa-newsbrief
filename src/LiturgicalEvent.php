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
        $this->tag      = $event["event_key"];
        $this->name     = $event["name"];
        $this->date     = new \DateTime($event["date"]);
        $this->color    = LitColor::areValid($event["color"]) ? $event["color"] : ['???'];
        $this->type     = LitEventType::isValid($event["type"]) ? $event["type"] : '';
        $this->grade    = LitGrade::isValid($event["grade"]) ? $event["grade"] : -1;
        $this->displayGrade     = $event["grade_display"];
        $this->common   = LitCommon::areValidCommons($event["common"]) ? $event["common"] : [];
        $this->liturgicalYear   = $event["liturgical_year"] ?? '';
        $this->isVigilMass      = $event["is_vigil_mass"] ?? false;
    }
}
