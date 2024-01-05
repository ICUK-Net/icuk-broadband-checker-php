<div id="broadband-availability-search" broadband-availability-id=
    <?php
        echo '"' . $GLOBALS["BroadbandAvailabilityId"] . '"'
    ?>>
    
	<input id="broadband-availability-search-input">

    <button id="broadband-availability-search-submit" onclick=
    <?php
        echo "'cli_or_postcode(\"" . $GLOBALS["BroadbandAvailabilityId"] . "\", \"" . $GLOBALS["BroadbandAvailabilityPath"] . "\")'"
     ?>>Check Availability</button>
    <br/>
     <a id="broadband-availability-search-error" style="display: none;"><?php
        echo $GLOBALS["BroadbandAvailabilityErrorMessage"];
     ?></a>
</div>