<script>
    const AVAILABILITY_UNKNOWN = 0;
    const AVAILABILITY_AVAILABLE = 1;
    const AVAILABILITY_NOT_AVAILABLE = 2;
    const AVAILABILITY_PLANNED = 3;
    const AVAILABILITY_POTENTIALLY_AVAILABLE = 4;
    const AVAILABILITY_LIMITED_CAPACITY = 5;
    const AVAILABILITY_ORDER_PROHIBITED = 6;

    const PROVIDER_BT = 1;
    const PROVIDER_CITYFIBRE = 7;
    const PROVIDER_VODAFONE = 8;

    // Map API provider strings to internal provider constants
    function mapProvider(providerString) {
        switch (providerString) {
            case 'WBC_21CN':
            case 'BT':
                return PROVIDER_BT;
            case 'CITYFIBRE':
                return PROVIDER_CITYFIBRE;
            case 'VODAFONE':
                return PROVIDER_VODAFONE;
            default:
                return null;
        }
    }

    // Map API availability_flag strings to internal availability constants
    function mapAvailability(entry) {
        const flag = entry.availability_flag;

        switch (flag) {
            case 'AVAILABLE':
                return AVAILABILITY_AVAILABLE;
            case 'NOT_AVAILABLE':
                return AVAILABILITY_NOT_AVAILABLE;
            case 'PLANNED':
                return AVAILABILITY_PLANNED;
            case 'POTENTIALLY_AVAILABLE':
                return AVAILABILITY_POTENTIALLY_AVAILABLE;
            case 'LIMITED_CAPACITY':
                return AVAILABILITY_LIMITED_CAPACITY;
            case 'PROHIBITED':
                return AVAILABILITY_ORDER_PROHIBITED;
            default:
                return AVAILABILITY_UNKNOWN;
        }
    }

    // Normalise a raw API product entry into the internal format used by populateTable/formatAvailabilityCell
    function normaliseProduct(entry) {
        return {
            Technology: entry.technology,
            Provider: mapProvider(entry.provider),
            Availability: mapAvailability(entry),
            SpeedRange: entry.speed_range || null,
            SpeedRangeUp: entry.speed_range_up || null,
            AvailabilityDate: entry.availability_date || null,
            Name: entry.name,
            LikelyDownSpeed: entry.likely_down_speed,
            LikelyUpSpeed: entry.likely_up_speed,
            RangeBottom: entry.range_bottom,
            RangeTop: entry.range_top,
            LimitedCapacity: entry.limited_capacity
        };
    }

    function populateTable(broadbandAvailabilityId, jsonData) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${broadbandAvailabilityId}"]`);
        if (!table) {
            console.error('Table with specified broadband availability ID not found');
            return;
        }

        const tableBody = table.querySelector('tbody');

        // Clear the content of the table cells except headers and technology names
        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)');
            cells.forEach(cell => {
                cell.innerHTML = '';
            });
        });

        // Group results by technology and provider
        const technologies = ['SOADSL', 'SoGEA', 'SOGFast', 'FTTP'];
        const providers = {
            [PROVIDER_BT]: 'td:nth-child(2)',
            [PROVIDER_CITYFIBRE]: 'td:nth-child(3)',
            [PROVIDER_VODAFONE]: 'td:nth-child(4)'
        };

        // Normalise raw API entries before processing
        const normalisedData = jsonData.map(normaliseProduct);

        normalisedData.forEach(entry => {
            const tech = entry.Technology;
            const row = tableBody.querySelector(`#${tech}`);
            if (!row) return;

            const providerSelector = providers[entry.Provider];
            if (!providerSelector) return;

            const cell = row.querySelector(providerSelector);
            if (!cell) return;

            cell.innerHTML = formatAvailabilityCell(entry);
        });

        // Fill empty cells
        tableBody.querySelectorAll('tr').forEach(row => {
            const btCell = row.querySelector('td:nth-child(2)');
            const cfCell = row.querySelector('td:nth-child(3)');
            const vfCell = row.querySelector('td:nth-child(4)');

            if (btCell && !btCell.innerHTML) {
                btCell.innerHTML = '<span class="broadband-availability-not-available">Not Available</span>';
            }
            if (cfCell && !cfCell.innerHTML) {
                cfCell.innerHTML = '<span class="broadband-availability-not-available">Not Available</span>';
            }
            if (vfCell && !vfCell.innerHTML) {
                vfCell.innerHTML = '<span class="broadband-availability-not-available">Not Available</span>';
            }
        });

        // CityFibre does not offer SOADSL, SoGEA or SOGFast
        ['SOADSL', 'SoGEA', 'SOGFast'].forEach(tech => {
            const row = tableBody.querySelector(`#${tech}`);
            if (row) {
                const cfCell = row.querySelector('td:nth-child(3)');
                if (cfCell && cfCell.querySelector('.broadband-availability-not-available')) {
                    cfCell.innerHTML = '<span class="broadband-availability-dash">-</span>';
                }
            }
        });

        // Vodafone does not offer SOADSL or SOGFast
        ['SOADSL', 'SOGFast'].forEach(tech => {
            const row = tableBody.querySelector(`#${tech}`);
            if (row) {
                const vfCell = row.querySelector('td:nth-child(4)');
                if (vfCell && vfCell.querySelector('.broadband-availability-not-available')) {
                    vfCell.innerHTML = '<span class="broadband-availability-dash">-</span>';
                }
            }
        });
    }

    function formatAvailabilityCell(entry) {
        const availability = entry.Availability;

        if (availability === AVAILABILITY_AVAILABLE) {
            if (entry.SpeedRange) {
                let html = '<span class="broadband-availability-available">&darr; ' + entry.SpeedRange + '</span>';
                if (entry.SpeedRangeUp) {
                    html += '<br><span class="broadband-availability-speed-up">&uarr; ' + entry.SpeedRangeUp + '</span>';
                }
                return html;
            }
            return '<span class="broadband-availability-available">Available</span>';
        }

        if (availability === AVAILABILITY_POTENTIALLY_AVAILABLE) {
            if (entry.SpeedRange) {
                let html = '<span class="broadband-availability-planned">&darr; ' + entry.SpeedRange + '</span>';
                if (entry.SpeedRangeUp) {
                    html += '<br><span class="broadband-availability-speed-up">&uarr; ' + entry.SpeedRangeUp + '</span>';
                }
                return html;
            }
            return '<span class="broadband-availability-planned">Potentially Available</span>';
        }

        if (availability === AVAILABILITY_PLANNED) {
            let html = '<span class="broadband-availability-planned">Planned</span>';
            if (entry.AvailabilityDate) {
                html += '<br><span class="broadband-availability-date">' + entry.AvailabilityDate + '</span>';
            }
            return html;
        }

        if (availability === AVAILABILITY_LIMITED_CAPACITY) {
            let html = '<span class="broadband-availability-planned">Limited Capacity</span>';
            if (entry.AvailabilityDate) {
                html += '<br><span class="broadband-availability-date">' + entry.AvailabilityDate + '</span>';
            }
            return html;
        }

        if (availability === AVAILABILITY_ORDER_PROHIBITED) {
            return '<span class="broadband-availability-not-available">Order Prohibited</span>';
        }

        return '<span class="broadband-availability-not-available">Not Available</span>';
    }

    function populateAddressList(id, jsonData, path) {
        const addressList = document.querySelector('#broadband-availability-address-list[broadband-availability-id="' + id + '"]');
        const tableBody = addressList.querySelector("tbody");

        tableBody.innerHTML = '';

        jsonData.addresses.forEach((address, index) => {
            const row = document.createElement("tr");

            const radioCell = document.createElement("td");
            const radioInput = document.createElement("input");
            radioInput.type = "radio";
            radioInput.name = "broadband-availability-address-radio-" + id;
            radioInput.onclick = function() {
                sendAddressPos(id, index, path);
            };
            radioCell.appendChild(radioInput);
            row.appendChild(radioCell);

            const addressCell = document.createElement("td");

            if (address.nad_key == null)
                address.nad_key = "";

            addressCell.innerHTML = `
        ${formatAddress(address)}
        <input class="broadband-availability-address-pos" value="${index}" style="display: none;">
        <span class="broadband-availability-address-nad"> ${address.nad_key}</span>
        ${address.uprn ? '<span class="broadband-availability-address-uprn"> ' + address.uprn + '</span>' : ''}
        `;
            row.appendChild(addressCell);

            tableBody.appendChild(row);
        });

        // Add the "None of the above" row
        const noneRow = document.createElement("tr");
        const noneRadioCell = document.createElement("td");
        const noneRadioInput = document.createElement("input");
        noneRadioInput.type = "radio";
        noneRadioInput.name = "broadband-availability-address-radio-" + id;
        noneRadioInput.onclick = function() {
                sendAddressPos(id, -1, path);
        };
        noneRadioCell.appendChild(noneRadioInput);
        noneRow.appendChild(noneRadioCell);

        const noneAddressCell = document.createElement("td");
        noneAddressCell.innerText = "None of the above";
        const noneAddressPosInput = document.createElement("input");
        noneAddressPosInput.className = "broadband-availability-address-pos";
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
        const addressRows = addressList.querySelectorAll("tbody tr");

        addressRows.forEach(row => {
            const addressCell = row.querySelector("td:nth-child(2)");
            if (!addressCell) return;
            const addressText = addressCell.textContent.toLowerCase();
            if (addressText.includes(filterText) || addressText.trim() === "none of the above") {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        });
    }

    function sendAddressPos(id, address_pos, path) {
        hideAddressList(id);
        showResults(id);

        fetch(path, {
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

        if (address.company_name) parts.push(address.company_name);
        if (address.sub_premises) parts.push(address.sub_premises);
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
        var regex = /^[A-Z]{1,2}[0-9]{1,2}[A-Z]? ?[0-9][A-Z]{2}$/i;
        return regex.test(postcode);
    }

    function valid_alid(input) {
        return /^BBEU/i.test(input);
    }

    function showResults(id) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${id}"]`);
        const tableBody = table.querySelector('tbody');

        table.style.display = 'inline-table';

        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)');
            cells.forEach(cell => {
                cell.innerHTML = '<div class="broadband-availability-loader"></div>';
            });
        });
    }

    function hideResults(id) {
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${id}"]`);
        const tableBody = table.querySelector('tbody');

        table.style.display = '';

        tableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td:not(:first-child)');
            cells.forEach(cell => {
                cell.innerHTML = '';
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

        // Strip special characters and Unicode breaking spaces from input
        cli_postcode = cli_postcode.replace(/\+|\(|\)|\-/g, '');
        cli_postcode = cli_postcode.replace(/[\u202C-\u202D]/g, '');
        cli_postcode = cli_postcode.trim();

        const errorMessage = document.querySelector("div[broadband-availability-id='" + id + "'] .broadband-availability-search-error");
        const table = document.querySelector(`#broadband-availability-results[broadband-availability-id="${id}"]`);

        if (errorMessage) errorMessage.style.display = 'none';

        // If not searching for postcode then show results module immediately
        if (!valid_postcode(cli_postcode))
            showResults(id);
        else
            hideResults(id);
        
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
                populateAddressList(id, json, path);
                showAddressList(id);
            } else {
                hideResults(id);
                if (errorMessage) errorMessage.style.display = 'inline';
                console.log('Broadband Availability Checker API: A serverside error occurred, this is most likely due to an invalid phone number or postcode.'); 
            }
        })
        .catch((error) => {
            hideResults(id);
            if (errorMessage) errorMessage.style.display = 'inline';
            console.log('Broadband Availability Checker API: A serverside error occurred, this is likely due to an error thrown from api.interdns.co.uk.', error); 
        });
    }
</script>
