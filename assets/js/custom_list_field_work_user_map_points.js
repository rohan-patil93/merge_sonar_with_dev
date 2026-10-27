
$(document).ready(function () {
        // Attach change listener for from_date using jQuery
    $('#from_date').on('change', function() {
        console.log('From date changed:', $(this).val());
        handleFromDateChange();
    });

    $(document).on('click', '.viewMapBtn', function () {
        const id = $(this).data('id');
        const user_field_work_type = $(this).data('work-type');
        const map_date = $(this).data('date');

        ViewMapPoint(id, user_field_work_type, map_date);
    });
});

function handleFromDateChange() {
    var fromDateInput = document.getElementById("from_date");
    var toDateInput = document.getElementById("to_date");

    // Set the minimum value of the To Date field to the From Date value
    toDateInput.min = fromDateInput.value;
    if (toDateInput.value < fromDateInput.value) {
        toDateInput.value = fromDateInput.value;
    }
}

let leafletMap = null;
// let map, marker;
$(document).ready(function () {
    $('#getDataButton').on('click', function () {
        // Get selected values
        let user_ids = $('#user_id').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        // Validate input
        if (!user_ids || user_ids.length === 0) {
            $('#user_id_err').text('Please select at least one user').show();
            return;
        } else {
            $('#user_id_err').hide();
        }

        if (!from_date) {
            $('#from_date_err').text('Please select a from date').show();
            return;
        } else {
            $('#from_date_err').hide();
        }
        if (!to_date) {
            $('#to_date_err').text('Please select a from date').show();
            return;
        } else {
            $('#to_date_err').hide();
        }

        // Send AJAX request
        $.ajax({
            url: 'ajax.php', // Replace with your backend script
            method: 'POST',
            data: {
                page: 'getFieldWorkUserMapPointsData',
                user_id: user_ids,
                from_date: from_date,
                to_date: to_date,
            },
            success: function (response) {
                console.log(response);
                const data = JSON.parse(response);
                document.getElementById('workType1').classList.remove('d-none');

                const tableBody = document.querySelector('#fiedWorkTable tbody');

                // Clear existing content
                tableBody.innerHTML = '';

                // Check if data is empty
                if (data.length === 0) {
                    // Show the "No Records" message
                    tableBody.innerHTML = `
                        <tr>
                        <td colspan="7">
                            <div class="text-center">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                    <i class="fa fa-warning"></i> No Records
                                </p>   
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px work-img" alt="" src="assets/images/work.png" >
                                </div>   
                            </div>
                            </td>
                            </tr>
                        `;
                } else {
                    let i = 1;
                    // Loop through the data and populate the table
                    data.forEach(item => {
                        const newRow = `
                                <tr>
                                    <td>${i}</td>
                                    <td>${item.display_name || item.id}</td>
                                    <td>${item.user_email || ''}</td>
                                    <td>${item.map_date || ''}</td>
                                    <td>${item.visited || ''}</td>
                                    <td>${item.extra || ''}</td>
                                    <td>${item.pending || ''}</td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-success viewMapBtn"
                                            data-id="${item.id}"
                                            data-work-type="${item.user_field_work_type}"
                                            data-date="${item.map_date}">
                                        View Map Point
                                    </button>

                                    </td>
                                </tr>`;
                        tableBody.innerHTML += newRow;
                        i++;
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#result-container').html('<p>Error fetching data</p>');
            }
        });
    });

    $("#mapModal").on("shown.bs.modal", function () {
        leafletMap.invalidateSize();
    });
    // Update All Points Button
    $("#updateMapBtn").click(function () {

        $("#mapModal").modal("show");
        initializeMap(assignedLocations, visitedLocations);
    });
    // Initialize Map Modal
    $("#mapModal").on("hidden.bs.modal", function () {
        $("#updateMapBtn").focus();
    });
});
function ViewMapPoint(id, user_field_work_type, map_date) {
    if (user_field_work_type == '2' || user_field_work_type == '3') {
        $.ajax({
            url: 'ajax.php', // Replace with your backend script
            method: 'POST',
            data: {
                page: 'getMapPoints',
                user_id: id,
                map_date: map_date,
                user_field_work_type: user_field_work_type
            },
            success: function (response) {
                const data = JSON.parse(response);
                const visitedLocations = data?.visitedLocations;
                $("#mapModal").modal("show");
                initializeVisitedMap(visitedLocations)
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#result-container').html('<p>Error fetching data</p>');
            }
        });

        // initializeMap(assignedLocations);
    } else {

        $.ajax({
            url: 'ajax.php', // Replace with your backend script
            method: 'POST',
            data: {
                page: 'getMapPoints',
                user_id: id,
                map_date: map_date,
                user_field_work_type: user_field_work_type
            },
            success: function (response) {
                const data = JSON.parse(response);
                const visitedLocations = data?.visitedLocations;
                const assignedLocations = data?.assignedLocations;
                $("#mapModal").modal("show");
                initializeMap(assignedLocations, visitedLocations);
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#result-container').html('<p>Error fetching data</p>');
            }
        });

        // initializeMap(assignedLocations, visitedLocations);
    }
    console.log(id, user_field_work_type, map_date);
}

const greenIcon = L.ExtraMarkers.icon({
    icon: 'fa-check', // Font Awesome icon
    markerColor: 'green',
    shape: 'circle',
    prefix: 'fa'
});

const redIcon = L.ExtraMarkers.icon({
    icon: 'fa-times',
    markerColor: 'red',
    shape: 'circle',
    prefix: 'fa'
});

function initializeMap(assignedLocationsObj, visitedLocationsObj) {
    const assignedLocations = Object.values(assignedLocationsObj);
    const visitedLocations = Object.values(visitedLocationsObj);
    // Check if Leaflet map is already initialized
    if (leafletMap) {
        // Clear previous markers and layers, retain tile layer
        leafletMap.eachLayer(function (layer) {
            if (!layer._url) leafletMap.removeLayer(layer);
        });
    } else {
        // Initialize Leaflet map for the first time
        leafletMap = L.map("map").setView([22.836946, 78.354492], 5); // Default center
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(leafletMap);
    }

    const visitedSet = new Set(visitedLocations.map(loc => `${loc.lat},${loc.lng}`));
    const visitedPoints = []; // For polyline
    const allBounds = []; // To store all marker locations for fitting bounds
    const visitedid = [];
    // Add markers for visited locations
    visitedLocations.forEach(location => {
        const lat = location.lat;
        const lng = location.lng;
        const vid = location.field_work_daily_plan_id;
        visitedPoints.push([lat, lng]); // Add to polyline points
        allBounds.push([lat, lng]); // Add to bounds array
        visitedid.push(vid);
        // Add green marker
        L.marker([lat, lng], {
            icon: greenIcon
        })
            .addTo(leafletMap)
            .bindPopup(`<b>Location:</b> ${location.name || "Unassigned"}<br><b>Time:</b> ${location.map_time || "No Time"}<br><b>Status:</b> Visited`);
    });
    // Add markers for assigned locations that are not visited
    assignedLocations.forEach(location => {
        const lat = location.lat;
        const lng = location.lng;
        const asid = location.id;
        if (!visitedid.includes(asid)) {
            allBounds.push([lat, lng]); // Add to bounds array

            // Add red marker
            L.marker([lat, lng], {
                icon: redIcon
            })
                .addTo(leafletMap)
                .bindPopup(`<b>Location:</b> ${location.name}<br><b>Status:</b> Not Visited`);
        }
    });

    // Draw polyline connecting visited locations
    if (visitedPoints.length > 1) {
        L.polyline(visitedPoints, {
            color: "green"
        }).addTo(leafletMap);
    }

    // Automatically adjust the map to fit all markers and lines
    if (allBounds.length > 0) {
        const bounds = L.latLngBounds(allBounds); // Create bounds from all points
        leafletMap.fitBounds(bounds, {
            padding: [5, 20]
        }); // Add padding for better visibility
    }
}
function initializeVisitedMap(visitedLocationsObj) {
    const visitedLocations = Object.values(visitedLocationsObj); // Convert object to array

    // Check if Leaflet map is already initialized
    if (leafletMap) {
        // Clear previous markers and layers, retain tile layer
        leafletMap.eachLayer(function (layer) {
            if (!layer._url) leafletMap.removeLayer(layer);
        });
    } else {
        // Initialize Leaflet map for the first time
        leafletMap = L.map("map").setView([22.836946, 78.354492], 5); // Default center
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(leafletMap);
    }

    const visitedPoints = []; // To store visited marker points for bounds and polyline

    // Add markers for visited locations
    visitedLocations.forEach(location => {
        const lat = location.lat;
        const lng = location.lng;

        visitedPoints.push([lat, lng]); // Add to bounds array

        // Add green marker
        L.marker([lat, lng], { icon: greenIcon })
            .addTo(leafletMap)
            .bindPopup(`<b>Location:</b> ${location.name || "Unassigned"}<br><b>Time:</b> ${location.map_time || "No Time"}<br><b>Status:</b> Visited`);
    });
    if (visitedPoints.length > 1) {
        L.polyline(visitedPoints, {
            color: "green"
        }).addTo(leafletMap);
    }

    // Automatically adjust the map to fit all markers
    if (visitedPoints.length > 0) {
        const bounds = L.latLngBounds(visitedPoints); // Create bounds from all points
        leafletMap.fitBounds(bounds, { padding: [5, 20] }); // Add padding for better visibility
    }
}