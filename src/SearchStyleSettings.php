<?php
namespace Icuk\BroadbandAvailabilityPhp;

use Spatie\Color\Factory;
use Spatie\Color\Exceptions\InvalidColorValue;

/**
 * Contains values that can be customised to change the style of the search module of the broadband 
 * availability checker.
 * 
 * @property string $button_gradient_low Colour of the button at the bottom of the gradient
 * @property string $button_gradient_high Colour of the button at the top of the gradient
 * @property string $button_gradient_low_hover Colour of the button while its being hovered over at the bottom of the gradient
 * @property string $button_gradient_high_hover Colour of the button while its being hovered over at the top of the gradient
 * @property string $button_text_colour Colour of the text on the button
 * 
 * @property string $input_bg_colour Colour the the input box
 * @property string $input_text_colour Colour of the text on the input box
 * @property string $input_hover_fade_colour Colour of the fade around the input box while it is selected
 * 
 * @property string $error_message_colour Colour of the error message
 */
class SearchStyleSettings {
    // Button Styles
    public $button_gradient_low;
    public $button_gradient_high;
    public $button_gradient_low_hover;
    public $button_gradient_high_hover;
    public $button_text_colour;

    // Input Styles
    public $input_bg_colour;
    public $input_text_colour;
    public $input_hover_fade_colour;

    // Error Styles
    public $error_message_colour;

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
            error_log("Broadband Availability Checker SearchStyleSettings.php failed to validate CSS colour: " . $colour);
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
        if (self::validate_colour($this->button_gradient_low) &&
            self::validate_colour($this->button_gradient_high) &&
            self::validate_colour($this->button_gradient_low_hover) &&
            self::validate_colour($this->button_gradient_high_hover) &&
            self::validate_colour($this->button_text_colour) &&
            self::validate_colour($this->input_bg_colour) &&
            self::validate_colour($this->input_text_colour) &&
            self::validate_colour($this->input_hover_fade_colour) &&
            self::validate_colour($this->error_message_colour)
        ) {
            return true;
        } 
        return false;
    }

    /**
     * Construct SearchStyleSettings
     * 
     * Initialise all the style properties to their default values
     * 
     * @return void
     */
    public function __construct() {
        $this->button_gradient_low = "#05c";
        $this->button_gradient_high = "#08c";
        $this->button_gradient_low_hover = "#0074cc";
        $this->button_gradient_high_hover = "#0074cc";
        $this->button_text_colour = "#fff";
        $this->input_bg_colour = "#fff";
        $this->input_text_colour = "#111";
        $this->input_hover_fade_colour = "rgba(82, 168, 236, .8)";
        $this->error_message_colour = "#ee1100";
    }
}