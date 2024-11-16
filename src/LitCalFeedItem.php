<?php

namespace LiturgicalCalendar\AlexaNewsBrief;

use LiturgicalCalendar\AlexaNewsBrief\Festivity;

/**
 * A data structure that represents a single item in the Liturgical Calendar Alexa News Brief.
 *
 * Contains the following properties:
 *
 * - $uid: a unique identifier for the item.
 * - $titleText: the title of the item, which is the name of the festivity.
 * - $mainText: the main text of the item, which is the text that is read to the user.
 * - $redirectionUrl: the URL that the user is redirected to when they click on the item.
 * - $updateDate: the date that the item was last updated.
 * - $ssml: an optional SSML string that can be used to generate a longer audio clip.
 *
 * The LitCalFeedItem class is serializable to JSON, which means that it can be easily converted to a JSON string and sent
 * to the Alexa service.
 */
class LitCalFeedItem implements \JsonSerializable
{
    public string $uid;
    public string $titleText;
    public string $mainText;
    public string $redirectionUrl;
    public string $updateDate;
    public ?string $ssml = null;

    /**
     * Constructs a new LitCalFeedItem from a Festivity object, the key for that festivity,
     * a publish date, a title text, a main text, and an optional SSML string.
     *
     * The publish date is converted to a string in the format "Y-m-d\TH:i:s\Z".
     * If $ssml is null, the ssml property of the object will remain null.
     * Otherwise, the ssml property is set to $ssml.
     * The mainText property is set to $mainText.
     * The redirectionUrl property is set to "https://litcal.johnromanodorazio.com/"
     *
     * @param Festivity $festivity The Festivity object.
     * @param \DateTime $publishDate The publish date.
     * @param string $titleText The title text.
     * @param string $mainText The main text.
     * @param string $ssml The optional SSML string.
     */
    public function __construct(Festivity $festivity, \DateTime $publishDate, string $titleText, string $mainText, ?string $ssml = null)
    {
        $this->uid = "urn:uuid:" . md5("LITCAL-" . $festivity->tag . '-' . $festivity->date->format('Y'));
        $this->updateDate       = $publishDate->format("Y-m-d\TH:i:s\Z");
        $this->titleText        = $titleText;
        if (null !== $ssml) {
            $this->ssml = $ssml;
        }
        $this->mainText         = $mainText;
        $this->redirectionUrl   = "https://litcal.johnromanodorazio.com/";
    }

    /**
     * jsonSerialize is needed because if $this->ssml is null, json_encode will output it as
     * a null value, which is not what we want. Instead, we want it to be excluded from the output
     * so that Alexa doesn't try to interpret it. So we unset it if it's null.
     */
    public function jsonSerialize(): array
    {
        if (null === $this->ssml) {
            unset($this->ssml);
        }
        return get_object_vars($this);
    }
}
