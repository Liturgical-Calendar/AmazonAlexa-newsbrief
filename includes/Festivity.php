<?php
include_once( 'includes/enums/LitColor.php' );
include_once( 'includes/enums/LitCommon.php' );
include_once( 'includes/enums/LitFeastType.php' );
include_once( 'includes/enums/LitGrade.php' );

/** 
 *  CLASS FESTIVITY
 *  SIMILAR TO THE CLASS USED IN THE LITCAL PHP ENGINE, 
 *  EXCEPT THAT IT CONVERTS PHP TIMESTAMP TO DATETIME OBJECT 
 *  AND DOES NOT IMPLEMENT JSONSERIALIZABLE OR COMPARATOR FUNCTION
 **/
class Festivity
{
    public string       $name;
    public DateTime     $date;
    public LitColor     $color;
    public LitFeastType $type;
    public LitGrade     $grade;
    public string       $displayGrade;
    public LitCommon    $common;
    public string       $liturgicalYear;

    function __construct( array $festivity ) {
        $this->name     = $festivity["name"];
        $this->date     = DateTime::createFromFormat( 'U', $festivity["date"], new DateTimeZone( 'UTC' ) );
        $this->color    = $festivity["color"];
        $this->type     = $festivity["type"];
        $this->grade    = $festivity["grade"];
        $this->common   = $festivity["common"];
        $this->liturgicalYear   = $festivity["liturgicalYear"] ?? null;
        $this->displayGrade     = $festivity["displayGrade"];
    }
}
