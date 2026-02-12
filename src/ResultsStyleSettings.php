<?php
namespace Icuk\BroadbandAvailabilityPhp;

use Spatie\Color\Factory;
use Spatie\Color\Exceptions\InvalidColorValue;

/**
 * Contains values that can be customised to change the style of the results module of the broadband 
 * availability checker.
 * 
 * @property string $background_colour Background colour of the results table
 * @property string $head_background_colour Background colour of the head of the results table
 * @property string $left_background_colour Background colour of the left side of the results table
 * @property string $seperators_colour Colour of the seperators between cells
 * 
 * @property string $text_colour Colour of the text of the results
 * @property string $head_text_colour Colour of the text of the head of the results table
 * @property string $left_text_colour Colour of the text of the left of the results table
 * 
 * @property string $available_label_colour Colour of the available label
 * @property string $not_available_label_colour Colour of the not available label
 * @property string $planned_label_colour Colour of the planned/limited capacity/potentially available label
 * 
 * @property string $available_text_colour Colour of the text on the available label
 * @property string $not_available_text_colour Color of the text on the not available label
 * @property string $planned_text_colour Colour of the text on the planned label
 * 
 * @property string $loading_circle_primary_colour The colour of the spinner on the loading circle
 * @property string $loading_circle_secondary_colour The colour of static background of the loading circle
 * 
 * @property bool $hide_results Hide the results table before search (bool)
 */
class ResultsStyleSettings {
    // Background Colors
    public $background_colour;
    public $head_background_colour;
    public $left_background_colour;
    public $seperators_colour;

    // Text Colors
    public $text_colour;
    public $head_text_colour;
    public $left_text_colour;
    public $available_text_colour;
    public $not_available_text_colour;
    public $planned_text_colour;

    // Label Colors
    public $available_label_colour;
    public $not_available_label_colour;
    public $planned_label_colour;

    // Loading Colors
    public $loading_circle_primary_colour;
    public $loading_circle_secondary_colour;

    // Miscellaneous
    public $hide_results;

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
        if (!is_bool($this->hide_results)) {
            error_log("Broadband Availability Checker ResultsStyleSettings.php : hide_results needs be a boolean");
            return false;
        }

        if (self::validate_colour($this->background_colour) &&
            self::validate_colour($this->head_background_colour) &&
            self::validate_colour($this->left_background_colour) &&
            self::validate_colour($this->seperators_colour) &&
            self::validate_colour($this->text_colour) &&
            self::validate_colour($this->head_text_colour) &&
            self::validate_colour($this->left_text_colour) &&
            self::validate_colour($this->available_label_colour) &&
            self::validate_colour($this->not_available_label_colour) &&
            self::validate_colour($this->planned_label_colour) &&
            self::validate_colour($this->available_text_colour) &&
            self::validate_colour($this->not_available_text_colour) &&
            self::validate_colour($this->planned_text_colour) &&
            self::validate_colour($this->loading_circle_primary_colour) &&
            self::validate_colour($this->loading_circle_secondary_colour)
        ) {
            return true;
        } 
        return false;
    }

    /**
     * Construct ResultsStyleSettings
     * 
     * Initialise all the style properties to their default values
     * 
     * @return void
     */
    public function __construct() {
        $this->background_colour = "#fff";
        $this->head_background_colour = "#255B76";
        $this->left_background_colour = "#626262";
        $this->seperators_colour = "#ccc";
        $this->text_colour = "#000";
        $this->head_text_colour = "#fff";
        $this->left_text_colour = "#fff";
        $this->available_label_colour = "#468847";
        $this->not_available_label_colour = "#c00";
        $this->planned_label_colour = "#f89406";
        $this->available_text_colour = "#fff";
        $this->not_available_text_colour = "#fff";
        $this->planned_text_colour = "#fff";
        $this->loading_circle_primary_colour = "#3498db";
        $this->loading_circle_secondary_colour = "#f3f3f3";
        $this->hide_results = true;
    }
}