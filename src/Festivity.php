<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitColor;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitEventType;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;

/**
 * Represents a liturgical celebration.
 *
 * Similar to the class used in the Liturgical Calendar API,
 * except that it converts php timestamps to datetime objects
 * and does not implement jsonserializable or a comparator function.
 * @author  John R. D'Orazio <priest@johnromanodorazio.com>
 * @package LiturgicalCalendar\AlexaNewsBrief
 */
class Festivity
{
    public string $tag;
    public string $name;
    public \DateTime $date;
    public array $color;
    public string $type;
    public int $grade;
    public ?string $displayGrade;
    public array $common;
    public string $liturgicalYear;
    public bool $isVigilMass;


    /**
     * Constructor for Festivity class.
     *
     * @param array $festivity an array representing a liturgical celebration, with the following keys:
     *      - event_key {string}: the unique identifier for the festivity
     *      - name {string}: the name of the festivity
     *      - date {string}: an RFC 3339 datetime string representing the date of the festivity (e.g. "2018-05-21T00:00:00+00:00")
     *      - color {array}: an array of strings or a single string representing the liturgical color(s) for the festivity
     *      - type {string}: whether the festivity if "mobile" or "fixed"
     *      - grade {int}: the liturgical grade of the festivity (e.g. 7=HIGHER_SOLEMNITY, 6=SOLEMNITY, 5=FEAST_LORD, etc.)
     *      - grade_display {?string}: the localized version of the grade of the festivity, for display on frontend applications
     *        (e.g. "Feast of the Lord", "Memorial", etc.)
     *      - common {array}: an array of strings or a single string representing the common(s) for the festivity
     *      - liturgical_year {string}: the liturgical year of the festivity (e.g. "A", "B", etc.)
     *      - is_vigil_mass {bool}: boolean indicating if the festivity is a vigil Mass
     */
    public function __construct(array $festivity)
    {
        $this->tag      = $festivity["event_key"];
        $this->name     = $festivity["name"];
        $this->date     = new \DateTime($festivity["date"]);
        if (is_array($festivity["color"])) {
            if (LitColor::areValid($festivity["color"])) {
                $this->color = $festivity["color"];
            }
        } elseif (is_string($festivity["color"])) {
            $_color             = strtolower($festivity["color"]);
            // in the original implementation, the color string could contain multiple colors separated by a comma,
            // when there are multiple commons to choose from for a liturgical celebration;
            // in the more recent implementation, multiple colors are passed as an array of strings,
            // so this is just for backward compatibility but should probably be phased out
            if (strpos($_color, ",") && LitColor::areValid(explode(",", $_color))) {
                $this->color = explode(",", $_color);
            } elseif (LitColor::isValid($_color)) {
                $this->color = [ $_color ];
            } else {
                $this->color = [ '???' ];
            }
        }
        $this->type     = LitEventType::isValid($festivity["type"]) ? $festivity["type"] : "";
        $this->grade    = LitGrade::isValid($festivity["grade"]) ? $festivity["grade"] : -1;
        $this->displayGrade     = $festivity["grade_display"];
        if (is_string($festivity["common"])) {
            $this->common       = LitCommon::areValid(explode(",", $festivity["common"])) ? explode(",", $festivity["common"]) : [];
        } elseif (is_array($festivity["common"])) {
            if (LitCommon::areValid($festivity["common"])) {
                $this->common = $festivity["common"];
            } else {
                $this->common = [];
            }
        }
        $this->liturgicalYear   = $festivity["liturgical_year"] ?? '';
        $this->isVigilMass      = $festivity["is_vigil_mass"] ?? false;
    }
}
