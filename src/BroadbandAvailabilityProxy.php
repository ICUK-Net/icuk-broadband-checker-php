<?php
namespace Icuk\BroadbandAvailabilityPhp;


/**
 * Functions to proxy the broadband availability api
 * 
 * Contains the function handle_api when run will take the post information from the page and
 * proxy it towards api.interdns.co.uk using the specified username and password, also contains functions
 * to handle oauth authentication.
 */
class BroadbandAvailabilityProxy {
    const API_PLATFORM = 'LIVE';
    const API_HOST = 'https://api.interdns.co.uk';

    /**
     * Handles broadband availability API calls
     * 
     * Proxys broadband availability API calls on the page using either the phone number, ALID or address
     * for checking broadband availability or the postcode for listing addresses.
     * 
     * When an address is searched there is a possibility the "None of the above" selection was made
     * on the address list in which case the postcode is used instead.
     * 
     * These api requests are forwarded to api.interdns.co.uk with the provided credentials. The results can be returned on a web page
     * after being json encoded. This function also validates the phone number and postcode to save API calls
     * and prevent potential attacks.
     * 
     * On failure this function will respond with a fatal error in case of a failure code response from api.interdns.co.uk
     * and if validation before the API call fails then it will respond with null
     * 
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     */
    public static function handle_api(string $username, string $password) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["cli_or_postcode"])) {
            $cli_or_postcode = trim($_POST["cli_or_postcode"]);

            // Strip special characters and Unicode breaking spaces
            $cli_or_postcode = preg_replace('/[\+\(\)\-]/', '', $cli_or_postcode);
            $cli_or_postcode = preg_replace('/[\x{202C}-\x{202D}]/u', '', $cli_or_postcode);

            /* If valid postcode then return addresses on road */
            if (self::validate_postcode($cli_or_postcode))
                return self::list_address($cli_or_postcode, $username, $password);

            /* If valid phone number or ALID return availability results */
            if (self::validate_phonenumber($cli_or_postcode) || self::validate_alid($cli_or_postcode))
                return self::search_phonenumber_or_postcode($cli_or_postcode, $username, $password);
        }

        if (isset($_POST["address_pos"])) {
            $address_pos = $_POST["address_pos"];

            // If "None of the above" selected then search postcode otherwise search address
            if ($address_pos == -1) {
                $postcode = $_SESSION["BroadbandAvailability"]["POSTCODE"] ?? null;
                if ($postcode) {
                    return self::search_phonenumber_or_postcode($postcode, $username, $password);
                }
                error_log("Broadband Availability: No postcode found in session for 'None of the above' selection");
                return null;
            }

            if (!isset($_SESSION["BroadbandAvailability"]["ADDRESS_LIST"])) {
                error_log("Broadband Availability: The session address list was not found when doing a broadband availability search");
                return null;
            }

            if (!isset($_SESSION["BroadbandAvailability"]["ADDRESS_LIST"][$address_pos])) {
                error_log("Broadband Availability: Invalid address position " . $address_pos);
                return null;
            }

            $address = $_SESSION["BroadbandAvailability"]["ADDRESS_LIST"][$address_pos];
            return self::search_address($address, $username, $password);
        }
    }

    /**
     * Calls /broadband/availability API endpoint with a phone number, ALID or postcode
     * 
     * Makes a call to the cli_or_postcode api endpoint using the provided credentials.
     * 
     * @param string $cli_or_postcode Either a phone number, ALID or a postcode to lookup
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     * 
     * @return \OpenAPI\Client\Model\BroadbandAvailabilityResults
     */
    public static function search_phonenumber_or_postcode(string $cli_or_postcode, string $username, string $password) {
        $oauth_token = self::get_oauth_token($username, $password);

        $config = \OpenAPI\Client\Configuration::getDefaultConfiguration();
        $config->setAccessToken($oauth_token);
        $config->setHost(self::API_HOST);

        $apiInstance = new \OpenAPI\Client\Api\BroadbandAvailabilityApi(
            new \GuzzleHttp\Client(),
            $config
        );

        return $apiInstance->broadbandAvailabilityCliOrPostcodeGet($cli_or_postcode, self::API_PLATFORM);
    }

    /**
     * Calls /broadband/address_search API endpoint to list addresses in postcode
     * 
     * Makes a call to the address_search api endpoint using the provided credentials.
     * The address list will also be stored in the session as later on the data will
     * need to be accessed for the /broadband/availability endpoint
     * 
     * @param string $postcode Postcode to lookup
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     * 
     * @return \OpenAPI\Client\Model\BroadbandAddressSearchResults
     */
    public static function list_address(string $postcode, string $username, string $password) {
        $oauth_token = self::get_oauth_token($username, $password);

        $config = \OpenAPI\Client\Configuration::getDefaultConfiguration();
        $config->setAccessToken($oauth_token);
        $config->setHost(self::API_HOST);

        $apiInstance = new \OpenAPI\Client\Api\BroadbandAddressApi(
            new \GuzzleHttp\Client(),
            $config
        );

        $result = $apiInstance->broadbandAddressPostcodeGet($postcode, self::API_PLATFORM);
        $_SESSION["BroadbandAvailability"]["POSTCODE"] = $postcode;
        $_SESSION["BroadbandAvailability"]["ADDRESS_LIST"] = $result["addresses"];
        return $result;
    }

    /**
     * Calls /broadband/availability API endpoint with an address
     * 
     * Makes a call to the availability api endpoint using the provided credentials and address.
     * 
     * @param mixed $address Address object from the address list
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     * 
     * @return \OpenAPI\Client\Model\BroadbandAvailabilityResults
     */
    public static function search_address($address, $username,  $password) {
        $oauth_token = self::get_oauth_token($username, $password);

        $config = \OpenAPI\Client\Configuration::getDefaultConfiguration();
        $config->setAccessToken($oauth_token);
        $config->setHost(self::API_HOST);

        $apiInstance = new \OpenAPI\Client\Api\BroadbandAvailabilityApi(
            new \GuzzleHttp\Client(),
            $config
        );

        return $apiInstance->broadbandAvailabilityPost(self::API_PLATFORM, $address);
    }

    /**
     * Get an OAuth token
     * 
     * Returns an OAuth token and stores it across the session.
     * If the token stored in the session but expired then it will automatically
     * renew the OAuth token and then return the value. If no session is found the
     * token is taken directly from the API.
     * 
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     * 
     * @return string OAuth token
     */
    public static function get_oauth_token(string $username, string $password) {
        if (session_status() !== PHP_SESSION_ACTIVE)
            return self::update_oauth_token($username, $password);

        if (!isset($_SESSION["BroadbandAvailability"]["OAUTH_EXPIRY"]))
            return self::update_oauth_token($username, $password);

        if (time() > $_SESSION["BroadbandAvailability"]["OAUTH_EXPIRY"])
            return self::update_oauth_token($username, $password);

        return $_SESSION["BroadbandAvailability"]["OAUTH_TOKEN"];
    }

    /**
     * Update this sessions Oauth token
     * 
     * Accesses the /oauth/token api endpoint to get OAuth tokens using the
     * credentials provided with basic authentication.
     * The OAuth information is then stored in these session variables:
     * $_SESSION["BroadbandAvailability"]["OAUTH_TOKEN"]
     * $_SESSION["BroadbandAvailability"]["OAUTH_EXPIRY"]
     * 
     * @param string $username Username for api.interdns.co.uk
     * @param string $password Password for api.interdns.co.uk
     * 
     * @return string OAuth token
     */
    public static function update_oauth_token(string $username, string $password) {
        $config = \OpenAPI\Client\Configuration::getDefaultConfiguration();
        $config->setHost(self::API_HOST);
    
        $client = new \GuzzleHttp\Client(['auth' => [$username, $password]]);
        $apiInstance = new \OpenAPI\Client\Api\OAuthApi($client, $config);
        $grant_type = 'client_credentials';
    
        $result = $apiInstance->oauthTokenPost(self::API_PLATFORM, $grant_type);

<<<<<<< HEAD
        if (isset($_SESSION)) {
=======
        if (session_status() === PHP_SESSION_ACTIVE) {
        }
    }

    /**
     * Checks if the specified value is a valid postcode
     * 
<<<<<<< HEAD
     * Validates a postcode using regex.
     * 
     * @param string $data A string to validate as a phone number
=======
     * Validates a UK postcode using regex.
     * 
     * @param string $data A string to validate as a postcode
>>>>>>> 474681e (Clean initial commit)
     * 
     * @return bool
     */
    public static function validate_postcode(string $data) {
<<<<<<< HEAD
        $postcode_regex = "/^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$/";
        return preg_match($postcode_regex, strtoupper(str_replace(' ','', $data)));
=======
        $postcode_regex = "/^[A-Z]{1,2}[0-9]{1,2}[A-Z]? ?[0-9][A-Z]{2}$/i";
        return preg_match($postcode_regex, strtoupper(str_replace(' ', '', $data)));
>>>>>>> 474681e (Clean initial commit)
    }

    /**
     * Checks if the specified value is a valid phone number
     * 
     * Validates a phone number by attempting to create a phonenumber object with phonenumberlib
     * and using a catch statement to return false if the library is unable to parse the phone number.
     * 
     * @param string $data A string to validate as a phone number
     * 
     * @return bool
     */
    public static function validate_phonenumber(string $data) {
        $phone_util = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phone_number_proto = $phone_util->parse($data, "GB");
            return $phone_util->isValidNumber($phone_number_proto);
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }
    }
<<<<<<< HEAD
=======

    /**
     * Checks if the specified value is a valid ALID (Alternative Line Identifier)
     * 
     * Validates an ALID by checking if it starts with 'BBEU'.
     * 
     * @param string $data A string to validate as an ALID
     * 
     * @return bool
     */
    public static function validate_alid(string $data) {
        return stripos($data, 'BBEU') === 0;
    }
>>>>>>> 474681e (Clean initial commit)
}