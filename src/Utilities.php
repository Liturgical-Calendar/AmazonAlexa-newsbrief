<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

class Utilities
{
    /**
     * A message to print after the package has been installed.
     *
     * Prints a message of thanks to God and a prayer for the Pope.
     */
    public static function postInstall(): void
    {
        printf("\t\033[4m\033[1;44mCatholic Liturgical Calendar Alexa newsbrief\033[0m\n");
        printf("\t\033[0;33mAd Majorem Dei Gloriam\033[0m\n");
        printf("\t\033[0;36mOremus pro Pontifice nostro Francisco Dominus\n\tconservet eum et vivificet eum et beatum faciat eum in terra\n\tet non tradat eum in animam inimicorum ejus\033[0m\n");
    }
}
