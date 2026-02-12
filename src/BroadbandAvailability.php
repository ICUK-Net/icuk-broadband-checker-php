<?php
namespace Icuk\BroadbandAvailabilityPhp;

/**
 *  Provides functions to render the search and results which are submitted to a local api proxy.
 *  It also stores a unique ID that allows separation of multiple broadband availability
 *  checkers on the same webpage.
 * 
 *  @property string $id Unique identifier for search/results modules
 *  @property string $api_proxy_path Path of page that proxys availability requests
 */
class BroadbandAvailability {
    public $id;
    public $api_proxy_path;


    /**
     * Initialises the Broadband Availability checker
     * 
     * Initialises and validates the format of the api.interdns.co.uk API key.
     * As well as generating a unique 6 byte identifier for the search/results modules
     * 
     * @param string $api_proxy_path Path to the API proxy endpoint
     * 
     * @return void
     */
    public function __construct($api_proxy_path) {
        $this->id = $this->random_hex(12);
        $this->api_proxy_path = $api_proxy_path;
    }

    /**
     * Renders search tool
     * 
     * Renders search tool by including a Search template and setting globals for the data that it needs
     * which will then be substituted in by php in the file. The globals are then unset.
     * 
     * @param string $error_message Custom error message to display on failure
     * 
     * @return void
     */
    public function render_search($error_message="Availability check failed") {
        $GLOBALS["BroadbandAvailabilityId"] = urlencode($this->id);
        $GLOBALS["BroadbandAvailabilityPath"] = $this->api_proxy_path;
        $GLOBALS["BroadbandAvailabilityErrorMessage"] = htmlspecialchars($error_message);
        include(__DIR__ . '/modules/Search.php');
        unset($GLOBALS["BroadbandAvailabilityId"]);
        unset($GLOBALS["BroadbandAvailabilityPath"]);
        unset($GLOBALS["BroadbandAvailabilityErrorMessage"]);
    }

    /**
     * Renders search results
     * 
     * Renders search results by including a results template and setting globals for the data that it needs
     * which will then be substituted in by php in the file. The globals are then unset.
     * 
     * @return void
     */
    public function render_results() {
        $GLOBALS["BroadbandAvailabilityId"] = urlencode($this->id);
        include(__DIR__ . '/modules/Results.php');
        unset($GLOBALS["BroadbandAvailabilityId"]);
    }

    /**
     * Renders address list
     * 
     * Renders address list by including a template and setting globals for the data that it needs
     * which will then be substituted in by php in the file. The globals are then unset.
     * 
     * @return void
     */
    public function render_address_list() {
        $GLOBALS["BroadbandAvailabilityId"] = urlencode($this->id);
        include(__DIR__ . '/modules/AddressSelect.php');
        unset($GLOBALS["BroadbandAvailabilityId"]);
    }

    /**
     * Renders scripts
     * 
     * Includes a php file with the javascript needed for the availability checker to work
     * 
     * @return void
     */
    public function render_scripts() {
        include(__DIR__ . '/modules/Scripts.php');
    }

    /**
     * Renders styles
     * 
     * Takes in customisable style classes to configure the style and then uses them on a style
     * template to generate a stylesheet. If no style configurations are given it will generate the
     * default style configs.
     * 
     * @param SearchStyleSettings|null $search_style_settings Search module style configuration
     * @param ResultsStyleSettings|null $results_style_settings Results module style configuration
     * @param AddressSelectStyleSettings|null $address_select_style_settings Address select module style configuration
     * 
     * @return bool Returns true if successfully loaded
     */
    public function render_styles(SearchStyleSettings $search_style_settings = null, ResultsStyleSettings $results_style_settings = null, AddressSelectStyleSettings $address_select_style_settings = null)
    {
        if ($search_style_settings == null)
            $search_style_settings = new SearchStyleSettings();

        if ($results_style_settings == null)
            $results_style_settings = new ResultsStyleSettings();

        if ($address_select_style_settings == null)
            $address_select_style_settings = new AddressSelectStyleSettings();

        if (!$results_style_settings->validate() || 
            !$search_style_settings->validate() ||
            !$address_select_style_settings->validate())
            return false;

        $GLOBALS["BroadbandAvailabilitySearchStyleSettings"] = $search_style_settings;
        $GLOBALS["BroadbandAvailabilityResultsStyleSettings"] = $results_style_settings;
        $GLOBALS["BroadbandAvailabilityAddressSelectStyleSettings"] = $address_select_style_settings;
        include(__DIR__ . '/modules/Styles.php');
        unset($GLOBALS["BroadbandAvailabilitySearchStyleSettings"]);
        unset($GLOBALS["BroadbandAvailabilityResultsStyleSettings"]);
        unset($GLOBALS["BroadbandAvailabilityAddressSelectStyleSettings"]);
        return true;
    }

    /**
     * Generates a random hex string
     * 
     * Takes in a length and outputs a hex string
     * The input length is in terms of hex characters and not bytes
     * 
     * @param int $length Length of hex string in characters
     * 
     * @return string Random hex string
     */
    public static function random_hex($length = 16){
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }
}