<?php

namespace LiturgicalCalendar\AlexaNewsBrief\Enum;

/**
 * Enumeration representing the type of a liturgical event.
 *
 * - FIXED: for feasts that occur on the same date every year
 * - MOBILE: for feasts that occur on a different date each year, such as Easter.
 *
 * @author John R. D'Orazio <priest@johnromanodorazio.com>
 * @package LiturgicalCalendar\AlexaNewsBrief
 */
enum LitEventType: string
{
    use EnumToArrayTrait;

    case FIXED  = 'fixed';
    case MOBILE = 'mobile';
}
