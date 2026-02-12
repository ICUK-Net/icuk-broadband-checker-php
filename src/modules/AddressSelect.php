<table id="broadband-availability-address-list" broadband-availability-id=
    <?php
        echo '"' . $GLOBALS["BroadbandAvailabilityId"] . '"'
    ?>>
    <thead id="broadband-availability-address-head">
        <tr>
            <th style="width: 5%;">

            </th>
            <th>
                <input type="text" placeholder="Filter" id="broadband-availability-address-filter" onkeyup=
                <?php
                    echo "'filterAddressList(\"" . $GLOBALS["BroadbandAvailabilityId"] . "\")'"
                ?>>
            </th>
        </tr>
    </thead>
    <tbody id="broadband-availability-address-body">
    </tbody>
</table>