<?php
namespace Icuk\BroadbandAvailabilityPhp;

use Spatie\Color\Factory;
use Spatie\Color\Exceptions\InvalidColorValue;

/**
 * Contains values that can be customised to change the style of the results module of the broadband 
 * availability checker.
 */
 class AddressSelectStyleSettings {
    // Background Colors
    public $background_colour;
    public $head_background_colour;
    public $filter_box_background_colour;

    // Text Colors
    public $address_text_colour;
    public $filter_box_text_colour;
    public $nad_text_colour;

    // Border Colors
    public $border_colour;
    public $head_border_colour;
    public $filter_box_border_colour;

    // Hover/Fade Colors
    public $filter_box_hover_fade_colour;

    /**
     * Validates a CSS colour
     * 
     * Validates a CSS color by attempting to turn it into a class with Spatie\Color and if it throws an
     * exception then it will log an error and return false. If it successfully is turned into a colour
     * then it will return true;
     * 
     * @param string $colour A CSS colour
     * 
     * @return bool Is the CSS colour valid
     */
    public static function validate_colour($colour) {
        try {
            Factory::fromString($colour);
            return true;
        } catch (InvalidColorValue $exception) {
            error_log("Broadband Availability Checker ResultsStyleSettings.php failed to validate CSS colour: " . $colour);
            return false;
        }
    }

    /**
     * Validate this classes style properties
     * 
     * Runs all the style colour properties in the class through validate_colour to make sure
     * that they are valid colours.
     * 
     * @return bool Returns true if the style properties are valid
     */
    public function validate() {
        if (self::validate_colour($this->background_colour) &&
            self::validate_colour($this->head_background_colour) &&
            self::validate_colour($this->filter_box_background_colour) &&
            self::validate_colour($this->address_text_colour) &&
            self::validate_colour($this->filter_box_text_colour) &&
            self::validate_colour($this->nad_text_colour) &&
            self::validate_colour($this->border_colour) &&
            self::validate_colour($this->head_border_colour) &&
            self::validate_colour($this->filter_box_border_colour) &&
            self::validate_colour($this->filter_box_hover_fade_colour)
        ) {
            return true;
        } 
        return false;
    }

    /**
     * Construct AddressSelectStyleSettings
     * 
     * Initialise all the style properties to their default values
     * 
     * @return void
     */
    public function __construct() {
        $this->background_colour = "#fff";
        $this->head_background_colour = "#ddd";
        $this->filter_box_background_colour = "#fff";
        $this->address_text_colour = "#000";
        $this->filter_box_text_colour = "#111";
        $this->nad_text_colour = "#999";
        $this->border_colour = "#ccc";
        $this->head_border_colour = "#fff";
        $this->filter_box_border_colour = "#aaa";
        $this->filter_box_hover_fade_colour = "rgba(82, 168, 236, .8)";
    }
 }