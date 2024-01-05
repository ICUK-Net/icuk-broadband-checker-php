<script>
    function populateTable(broadbandAvailabilityId, jsonData) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${broadbandAvailabilityId}"]`);
        if (!table) {
            console.error('Table with specified broadband availability ID not found');
            return;
        }

        const tableBody = table.querySelector('tbody');

        // Clear the content of the table cells except headers and technology names
        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)'); // Exclude the first cell (technology name)
            cells.forEach(cell => {
            cell.textContent = ''; // Clear the content of the cell
            });
        });

        jsonData.forEach(entry => {
            if (entry.technology == "LLU") {
                entry.technology = "ADSL2";
            }

            const rowId = entry.technology.replace(/[^\w\s]/gi, ''); // Remove special characters for ID
            const row = tableBody.querySelector(`#${rowId}`); // Use tableBody here
            if (row) {
                const downloadSpeedCell = row.querySelector('td:nth-child(2)');
                const uploadSpeedCell = row.querySelector('td:nth-child(3)');

                let downloadSpeed = entry.speed_range ? entry.speed_range : entry.likely_down_speed;
                let uploadSpeed = entry.speed_range_up ? entry.speed_range_up : entry.likely_up_speed;

                // Mark the download speed if it's higher than the current value
                if (!downloadSpeedCell.textContent || parseFloat(downloadSpeed) > parseFloat(downloadSpeedCell.textContent)) {
                    downloadSpeedCell.textContent = downloadSpeed || '';
                }

                // Mark the upload speed if it's higher than the current value
                if (!uploadSpeedCell.textContent || parseFloat(uploadSpeed) > parseFloat(uploadSpeedCell.textContent)) {
                    uploadSpeedCell.textContent = uploadSpeed || '';
                }

                const btWholesaleCell = row.querySelector('td:nth-child(4)');
                const talkTalkCell = row.querySelector('td:nth-child(5)');

                // Mark availability based on BT Wholesale provider
                if (entry.provider.startsWith('WBC') && entry.availability) {
                    btWholesaleCell.innerHTML = '<span id="broadband-availability-available">Available</span>';
                }

                // Mark availability based on TalkTalk Business provider
                if (entry.provider === 'TTB' && entry.availability) {
                    talkTalkCell.innerHTML = '<span id="broadband-availability-available">Available</span>';
                }
            }
        });

        // Iterate over the table again to fill empty provider cells as "Not Available" and speeds as "-"
        tableBody.querySelectorAll('tr').forEach(row => {
            const downloadSpeedCell = row.querySelector('td:nth-child(2)');
            const uploadSpeedCell = row.querySelector('td:nth-child(3)');
            const btWholesaleCell = row.querySelector('td:nth-child(4)');
            const talkTalkCell = row.querySelector('td:nth-child(5)');

            if (!downloadSpeedCell.textContent) {
                downloadSpeedCell.textContent = '-';
            }
            
            if (!uploadSpeedCell.textContent) {
                uploadSpeedCell.textContent = '-';
            }

            if (!btWholesaleCell.textContent) {
                btWholesaleCell.innerHTML = '<span id="broadband-availability-not-available">Not Available</span>';
            }
            
            if (!talkTalkCell.textContent) {
                talkTalkCell.innerHTML = '<span id="broadband-availability-not-available">Not Available</span>';
            }
        });
    }

    function populateAddressList(id, jsonData) {
        // Get the table body element
        const addressList = document.querySelector('#broadband-availability-address-list[broadband-availability-id="' + id + '"]');
        const tableBody = addressList.querySelector("tbody");

        // Clear the table body first
        tableBody.innerHTML = '';

        // Loop through the addresses and populate the table
        jsonData.addresses.forEach((address, index) => {
            const row = document.createElement("tr");

            const radioCell = document.createElement("td");
            const radioInput = document.createElement("input");
            radioInput.type = "radio";
            radioInput.onclick = function() {
                sendAddressPos(id, index);
            };
            radioCell.appendChild(radioInput);
            row.appendChild(radioCell);

            const addressCell = document.createElement("td");

            // Convert from null to empty string to value from user
            if (address.nad_key == null)
                address.nad_key = "";

            addressCell.innerHTML = `
        ${formatAddress(address)}
        <input id="broadband-availability-address-pos" value="${index}" style="display: none;">
        <span id="broadband-availability-address-nad"> ${address.nad_key}</span>
        `;
            row.appendChild(addressCell);

            tableBody.appendChild(row);
        });

        // Add the "None of the above" row
        const noneRow = document.createElement("tr");
        const noneRadioCell = document.createElement("td");
        const noneRadioInput = document.createElement("input");
        noneRadioInput.type = "radio";
        noneRadioCell.appendChild(noneRadioInput);
        noneRow.appendChild(noneRadioCell);

        const noneAddressCell = document.createElement("td");
        noneAddressCell.innerText = "None of the above";
        const noneAddressPosInput = document.createElement("input");
        noneAddressPosInput.id = "broadband-availability-address-pos";
        noneAddressPosInput.value = "-1";
        noneAddressPosInput.style.display = "none";
        noneAddressCell.appendChild(noneAddressPosInput);
        noneRow.appendChild(noneAddressCell);

        tableBody.appendChild(noneRow);
    }

    function filterAddressList(id) {
        const addressList = document.querySelector('#broadband-availability-address-list[broadband-availability-id="' + id + '"]');
        const filterInput = addressList.querySelector('#broadband-availability-address-filter');
        const filterText = filterInput.value.toLowerCase();
        const addressRows = document.querySelectorAll("#broadband-availability-address-body tr");

        addressRows.forEach(row => {
            const addressText = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            if (addressText.includes(filterText) || addressText == "none of the above") {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        });
    }

    function sendAddressPos(id, address_pos) {
        hideAddressList(id);
        showResults(id);

        fetch("/api.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "address_pos=" + encodeURIComponent(address_pos)
        })
        .then(response => response.json())
        .then(data => {
            populateTable(id, data["products"]);
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }

    function formatAddress(address) {
        let parts = [];

        if (address.premises_name) parts.push(address.premises_name);

        let thoroughfare_list = [];
        if (address.thoroughfare_number) thoroughfare_list.push(address.thoroughfare_number);
        if (address.thoroughfare_name) thoroughfare_list.push(address.thoroughfare_name);
        if (thoroughfare_list.length > 0) parts.push(thoroughfare_list.join(" "));

        if (address.locality) parts.push(address.locality);
        if (address.post_town) parts.push(address.post_town);
        if (address.county) parts.push(address.county);
        if (address.postcode) parts.push(address.postcode);

        return parts.join(', ');
    }

    function valid_postcode(postcode) {
        postcode = postcode.replace(/\s/g, "");
        var regex = /^[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}$/i;
        return regex.test(postcode);
    } 

    function showResults(id) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${id}"]`);
        const tableBody = table.querySelector('tbody');

        table.style.display = 'inline-table';

        // Fill table with loading animations
        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)'); // Exclude the first cell (technology name)
            cells.forEach(cell => {
            cell.innerHTML = '<div class="broadband-availability-loader"></div>'; // Put loading animation into cell
            });
        });
    }

    function hideResults(id) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${id}"]`);
        const tableBody = table.querySelector('tbody');

        table.style.display = '';

        // Remove all loading animations
        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)'); // Exclude the first cell (technology name)
            cells.forEach(cell => {
            cell.innerHTML = ''; // Clear the content of the cell
            });
        });
    }

    function showAddressList(id) {
        const addressList = document.querySelector('#broadband-availability-address-list[broadband-availability-id="' + id + '"]');
        addressList.style.display = 'inline-table';
    }

    function hideAddressList(id) {
        const addressList = document.querySelector('#broadband-availability-address-list[broadband-availability-id="' + id + '"]');
        addressList.style.display = 'none';
    }

    function cli_or_postcode(id, path) {
        let cli_postcode = document.querySelector("div[broadband-availability-id='" + id + "'] input").value;
        const errorMessage = document.querySelector('#broadband-availability-search-error');
        const table = document.querySelector(`table[broadband-availability-id="${id}"]`);
        const tableBody = table.querySelector('tbody');

        errorMessage.style.display = 'none';

        // If not searching for postcode then show results module immediatly
        if (!valid_postcode(cli_postcode))
            showResults(id);
        else
            hideResults(id);
        
        // Make request to api endpoint
        fetch(path, {
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: "cli_or_postcode=" + encodeURIComponent(cli_postcode)
        })
        .then((response) => {
            if (response.ok) { 
                return response.json();
            }
            return Promise.reject(response); 
        })
        .then((json) => {
            if (typeof json["products"] != 'undefined'){
                populateTable(id, json["products"]);
            } else if (typeof json["addresses"] != 'undefined') {
                populateAddressList(id, json);
                showAddressList(id);
            } else {
                hideResults(id);
                errorMessage.style.display = 'inline';
                console.log('Broadband Availability Checker API: A serverside error occured, this is most likely due to an invalid phone number or postcode.', error); 
            }
        })
        .catch((error) => {
            hideResults(id);
            errorMessage.style.display = 'inline';
            console.log('Broadband Availability Checker API: A serverside error occured, this is likely due to an error thrown from api.interdns.co.uk.', error); 
        });
    }
</script>
