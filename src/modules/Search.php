<div class="broadband-availability-search" broadband-availability-id=
    <?php
        echo '"' . $GLOBALS["BroadbandAvailabilityId"] . '"'
    ?>>
    
	<input class="broadband-availability-search-input" placeholder="Phone number or postcode">

    <button class="broadband-availability-search-submit" onclick=
    <?php
        echo "'cli_or_postcode(\"" . $GLOBALS["BroadbandAvailabilityId"] . "\", \"" . $GLOBALS["BroadbandAvailabilityPath"] . "\")'"
     ?>>Check Availability</button>
    <br/>
     <a class="broadband-availability-search-error" style="display: none;"><?php
        echo $GLOBALS["BroadbandAvailabilityErrorMessage"];
     ?></a>
</div>