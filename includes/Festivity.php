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
    /**
     * @var string
     */
    public string $name;

    /**
     * @var DateTime object
     */
    public DateTime $date;

    /**
     * @var string
     */
    public LitColor $color;

    /**
     * @var string
     */
    public LitFeastType $type;

    /**
     * @var int
     */
    public LitGrade $grade;

    /**
     * @var string
     */
    public string $displayGrade;

    /**
     * @var string
     */
    public LitCommon $common;

    /**
     * @var string
     */
    public string $liturgicalYear;

    function __construct( $name, $date, $color, $type, $grade = LitGrade::WEEKDAY, $common = '', $liturgicalYear = null, $displayGrade = null )
    {
        $this->name = (string) $name;
        $this->date = DateTime::createFromFormat( 'U', $date, new DateTimeZone( 'UTC' ) ); //
        $this->color = (string) $color;
        $this->type = (string) $type;
        $this->grade = (int) $grade;
        $this->common = (string) $common;
        if( $liturgicalYear !== null ){
            $this->liturgicalYear = (string) $liturgicalYear;
        }
        $this->displayGrade = $displayGrade || '';
    }
}
