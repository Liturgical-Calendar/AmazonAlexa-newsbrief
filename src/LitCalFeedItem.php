<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Festivity;

class LitCalFeedItem implements \JsonSerializable
{
    public string $uid;
    public string $titleText;
    public string $mainText;
    public string $redirectionUrl;
    public string $updateDate;
    public ?string $ssml = null;

    public function __construct(string $key, Festivity $festivity, \DateTime $publishDate, string $titleText, string $mainText, ?string $ssml = null)
    {
        $this->uid = "urn:uuid:" . md5("LITCAL-" . $key . '-' . $festivity->date->format('Y'));
        $this->updateDate       = $publishDate->format("Y-m-d\TH:i:s\Z");
        $this->titleText        = $titleText;
        if (null !== $ssml) {
            $this->ssml = $ssml;
        }
        $this->mainText         = $mainText;
        $this->redirectionUrl   = "https://litcal.johnromanodorazio.com/";
    }

    public function jsonSerialize(): array
    {
        if (null === $this->ssml) {
            unset($this->ssml);
        }
        return get_object_vars($this);
    }
}
