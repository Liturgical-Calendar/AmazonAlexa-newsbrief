<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Enum\LitColor;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitCommon;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitFeastType;
use LiturgicalCalendar\AlexaNewsBrief\Enum\LitGrade;

/**
 *  CLASS FESTIVITY
 *  SIMILAR TO THE CLASS USED IN THE LITCAL PHP ENGINE,
 *  EXCEPT THAT IT CONVERTS PHP TIMESTAMP TO DATETIME OBJECT
 *  AND DOES NOT IMPLEMENT JSONSERIALIZABLE OR COMPARATOR FUNCTION
 **/
class Festivity
{
    public string $tag;
    public string $name;
    public \DateTime $date;
    public array $color;
    public string $type;
    public int $grade;
    public string $displayGrade;
    public array $common;
    public string $liturgicalYear;
    public bool $isVigilMass;

    public function __construct(array $festivity)
    {
        $this->name     = $festivity["name"];
        $this->date     = \DateTime::createFromFormat('U', $festivity["date"], new \DateTimeZone('UTC'));
        if (is_array($festivity["color"])) {
            if (LitColor::areValid($festivity["color"])) {
                $this->color = $festivity["color"];
            }
        } elseif (is_string($festivity["color"])) {
            $_color             = strtolower($festivity["color"]);
            //the color string can contain multiple colors separated by a comma, when there are multiple commons to choose from for that festivity
            $this->color        = strpos($_color, ",") && LitColor::areValid(explode(",", $_color)) ? explode(",", $_color) : ( LitColor::isValid($_color) ? [ $_color ] : [ '???' ] );
        }
        $this->type     = LitFeastType::isValid($festivity["type"]) ? $festivity["type"] : "";
        $this->grade    = LitGrade::isValid($festivity["grade"]) ? $festivity["grade"] : -1;
        $this->displayGrade     = $festivity["display_grade"];
        if (is_string($festivity["common"])) {
            //Festivity::debugWrite( "*** Festivity.php *** common vartype is string, value = $festivity["common"]" );
            $this->common       = LitCommon::areValid(explode(",", $festivity["common"])) ? explode(",", $festivity["common"]) : [];
        } elseif (is_array($festivity["common"])) {
            //Festivity::debugWrite( "*** Festivity.php *** common vartype is array, value = " . implode( ', ', $festivity["common"] ) );
            if (LitCommon::areValid($festivity["common"])) {
                $this->common = $festivity["common"];
            } else {
                //Festivity::debugWrite( "*** Festivity.php *** common values have not passed the validity test!" );
                $this->common = [];
            }
        }
        $this->liturgicalYear   = $festivity["liturgical_year"] ?? '';
        $this->isVigilMass      = $festivity["is_vigil_mass"] ?? false;
    }
}
