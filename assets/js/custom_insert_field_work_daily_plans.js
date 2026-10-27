
$(document).ready(function () {
    // Form submit handler
    document.getElementById('fieldWorkDailyPlansForm').addEventListener('submit', function (e) {
        $(".error").hide();
        const workType = document.getElementById('user_field_work_type').value;

        if (workType === '2') {
            return;
        }

        let isValid = true;

        // Validate fields in workType1
        if (workType === '1') {
            const fieldsToValidate = ['plan_date']; // Add more field IDs if necessary
            fieldsToValidate.forEach(id => {
                const field = document.getElementById(id);
                const errorDiv = document.getElementById(id + '_err');
                if (!field.value.trim()) {
                    errorDiv.classList.remove('d-none');
                    errorDiv.textContent = 'This field is required.';
                    isValid = false;
                } else {
                    errorDiv.classList.add('d-none');
                }
            });
            $("#inputTable tbody tr").each(function (index, row) {
                const latInput = $(row).find("input[name='plan_latitude[]']");
                const lngInput = $(row).find("input[name='plan_longitude[]']");

                // Validate latitude
                if (!latInput.val() || isNaN(latInput.val())) {
                    $("<div class='error text-danger' >Enter valid Latitude</div>").insertAfter(latInput).show();
                    isValid = false;
                }
                // Validate longitude
                if (!lngInput.val() || isNaN(lngInput.val())) {
                    $("<div class='error text-danger'>Enter valid Longitude</div>").insertAfter(lngInput).show();
                    isValid = false;
                }
            });
        }

        // Validate fields in workType2
        if (workType === '3') {
            const fieldsToValidate = [
                'work_latitude',
                'work_longitude',
                'work_time',
                'work_from_date',
                'work_to_date'
            ]; // Add more field IDs if necessary
            fieldsToValidate.forEach(id => {
                const field = document.getElementById(id);
                const errorDiv = document.getElementById(id + '_err');
                if (!field.value.trim()) {
                    errorDiv.classList.remove('d-none');
                    errorDiv.textContent = 'This field is required.';
                    isValid = false;
                } else {
                    errorDiv.classList.add('d-none');
                }
            });
        }

        if (!isValid) {
            e.preventDefault(); // Prevent form submission if validation fails
            return;
        }

    });

    // Attach change listeners using jQuery (required for Select2 compatibility)
    $('#user_id').on('change', function() {
        console.log('User selection changed:', $(this).val());
        handleUserSelection();
    });
    
    // Trigger initial load if user is already selected
    if ($('#user_id').val() && $('#user_id').val() !== '0') {
        console.log('Initial user value detected, triggering change');
        $('#user_id').trigger('change');
    }

    // Attach change listener for plan_date using jQuery
    $('#plan_date').on('change', function() {
        console.log('Plan date changed:', $(this).val());
        checkData();
    });

    // Attach change listener for work_from_date
    const workFromDateEl = document.getElementById('work_from_date');
    if (workFromDateEl) {
        console.log('Attaching change listener to work_from_date');
        workFromDateEl.addEventListener('change', function() {
            console.log('Work from date changed:', this.value);
            handleFromDateChange();
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            removeRow(e.target);
        }
    });

});


function checkData() {
    const userId = $("#user_id").val();
    const planDate = $("#plan_date").val();
    console.log(planDate, userId);

    if (userId && userId !== "0" && planDate) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                table: 'field_work_daily_plans',
                page: 'fetchFieldWorkDailyPlans',
                user_id: userId,
                date: planDate
            },
            success: function (response) {
                const data = JSON.parse(response);
                const qeury_reslut = data['qeury_reslut'];
                if (qeury_reslut) {
                    window.location.href = `index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=${userId}&date=${planDate}`;
                } else {
                    alert("No data found for the selected user and date.");
                }

            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
                alert("An error occurred while processing the request.");
            }
        });
    } else {
        alert("Please select a user and date.");
    }
}

function handleFromDateChange() {
    var fromDateInput = document.getElementById("work_from_date");
    var toDateInput = document.getElementById("work_to_date");

    // Set the minimum value of the To Date field to the From Date value
    toDateInput.min = fromDateInput.value;
    if (toDateInput.value < fromDateInput.value) {
        toDateInput.value = fromDateInput.value;
    }

    const userId = $("#user_id").val();
    const planDate = $("#work_from_date").val();
    console.log(planDate, userId);

    if (userId && userId !== "0" && planDate) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                table: 'user_field_work_details',
                page: 'fetchFieldWorkDailyPlans',
                user_id: userId,
                date: planDate
            },
            success: function (response) {
                const data = JSON.parse(response);
                const qeury_reslut = data['qeury_reslut'];
                if (qeury_reslut) {
                    window.location.href = `index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=${userId}&date=${planDate}`;
                } else {
                    console.log("No data found for the selected user and date.");
                }

            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
                alert("An error occurred while processing the request.");
            }
        });
    } else {
        alert("Please select a user and date.");
    }

}

function handleUserSelection() {
    console.log('test');
    // Get selected user and work type
    $("#inputTable tbody").empty();
    document.getElementById("work_latitude").value = '';
    document.getElementById("work_longitude").value = '';
    document.getElementById("location_name").value = '';
    document.getElementById("work_from_date").value = '';
    document.getElementById("work_to_date").value = '';
    document.getElementById("work_time").value = '';
    document.getElementById("work_radius").value = '';
    document.getElementById("contact_person").value = '';
    document.getElementById("mobile").value = '';
    document.getElementById("work_address").value = '';
    const userSelect = document.getElementById('user_id');
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    const workType = selectedOption.getAttribute('data-work-type');

    // Set the hidden input value for user_field_work_type
    document.getElementById('user_field_work_type').value = workType;

    document.getElementById('toDateDiv').classList.add('d-none');
    document.querySelectorAll('.work-type').forEach(el => el.classList.add('d-none'));

    // Show the appropriate section based on workType
    if (workType === '1' || workType === '2') {
        document.getElementById('workType1').classList.remove('d-none');
        document.getElementById('toDateDiv').classList.remove('d-none');
    } else if (workType === '3') {
        document.getElementById('workType2').classList.remove('d-none');
    }

    // Show save button only if a valid user is selected
    if (workType === '1' || workType === '2' || workType === '3') {
        document.getElementById('saveButton').classList.remove('d-none');
    } else {
        document.getElementById('saveButton').classList.add('d-none');
    }
    // Call appropriate AJAX function based on workType
    // const userId = <?php echo $rid ?? null; ?>;
    const userId = document.getElementById('user_id').value;
    const date = new URLSearchParams(window.location.search).get('date');

    if (workType === '1' || workType === '2') {
        fetchFieldWorkDailyPlans(userId, date);
    } else if (workType === '3') {
        fetchUserFieldWorkDetails(userId, date);
    }
    // Ensure only visible fields are included in the form submission
    document.querySelectorAll('.work-type').forEach(section => {

        if (section.classList.contains('d-none')) {
            section.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = true;
            });
        } else {
            section.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = false;
            });
        }
    });
}
// AJAX function for fetching data from `field_work_daily_plans`
function fetchFieldWorkDailyPlans(userId, date) {
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            table: 'field_work_daily_plans',
            page: 'fetchFieldWorkDailyPlans',
            user_id: userId,
            date: date
        },
        success: function (response) {
            try {
                const data = JSON.parse(response);
                const qeury_reslut = data['qeury_reslut'];
                if (qeury_reslut) {
                    populateTableRows(qeury_reslut);
                } else {
                    console.log('No data found for the given user and date.');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching Field Work Daily Plans:', error);
        }
    });
}

// AJAX function for fetching data from `user_field_work_details`
function fetchUserFieldWorkDetails(userId, date) {
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            table: 'user_field_work_details',
            page: 'fetchFieldWorkDailyPlans',
            user_id: userId,
            date: date
        },
        success: function (response) {
            try {
                const data = JSON.parse(response);
                const qeury_reslut = data['qeury_reslut'];
                if (qeury_reslut) {
                    populateWorkType2Fields(qeury_reslut);
                } else {
                    console.log('No data found for the given user and date.');
                }

            } catch (e) {
                console.error('Error parsing response:', e);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching User Field Work Details:', error);
        }
    });
}


function populateTableRows(data) {
    const tableBody = $("#inputTable tbody");
    tableBody.empty();
    data.forEach(row => {
        const newRow = `
            <tr>
                <td>
                    <i class="fas fa-trash-alt text-danger cursor-pointer removeRow"></i>
                </td>
                <td><input class="form-control" type="text" name="contact_person[]" value="${row.contact_person || ''}"></td>
                <td><input class="form-control" type="text" name="mobile[]" value="${row.mobile || ''}"></td>
                <td><input class="form-control" type="text" name="plan_location[]" value="${row.plan_location || ''}"></td>
                <td><textarea class="form-control" name="plan_address[]" rows="2" readonly>${row.plan_address || ''}</textarea></td>
                <td><input class="form-control lat" type="text" name="plan_latitude[]" value="${row.plan_latitude || ''}" readonly></td>
                <td><input class="form-control lng" type="text" name="plan_longitude[]" value="${row.plan_longitude || ''}" readonly></td>
                <td>
                    <button type="button" class="btn btn-success addMapBtn">Map</button>
                </td>
                </tr>`;
        tableBody.append(newRow);
    });
}

function populateWorkType2Fields(data) {

    if (data) {
        const result = data;
        if (result.id) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'user_field_work_details_id';
            hiddenInput.value = result.id;
            document.getElementById('workType2').appendChild(hiddenInput);
        }

        // Set values for the fields
        document.getElementById("work_latitude").value = result.work_latitude || '';
        document.getElementById("work_longitude").value = result.work_longitude || '';
        document.getElementById("location_name").value = result.location_name || '';
        document.getElementById("work_from_date").value = result.work_from_date || '';
        document.getElementById("work_to_date").value = result.work_to_date || '';
        document.getElementById("work_time").value = result.work_time || '';
        document.getElementById("work_radius").value = result.work_radius || '';
        document.getElementById("contact_person").value = result.contact_person || '';
        document.getElementById("mobile").value = result.mobile || '';
        document.getElementById("work_address").value = result.work_address || '';
    } else {
        console.error("Invalid data format:", data);
    }
}
let map, marker;
let currentLatInput = null;
let currentLngInput = null;

$(document).ready(function () {
    const userSelect = document.getElementById('user_id');
    if (userSelect) {
        userSelect.dispatchEvent(new Event('change'));
    }
    $("#addMoreBtn").click(function () {
        const newRow = `
            <tr>
                <td>
                    <i class="fas fa-trash-alt text-danger cursor-pointer removeRow" ></i>
                </td>
                <td><input class="form-control " type="text" name="contact_person[]" ></td>
                <td><input class="form-control " type="text" name="mobile[]" ></td>
                <td><input class="form-control" type="text" name="plan_location[]"></td>
                <td><textarea class="form-control" name="plan_address[]" rows="2" readonly></textarea></td>
                <td><input class="form-control lat" type="text" name="plan_latitude[]" readonly></td>
                <td><input class="form-control lng" type="text" name="plan_longitude[]" readonly></td>
                <td>
                    <button type="button" class="btn btn-success addMapBtn">Map</button>
                </td>
                </tr>`;
        $("#inputTable tbody").append(newRow);
    });
    // Open Map Modal for Add Map Point
    $(document).on("click", ".addMapBtn", function () {
        const closestTableRow = $(this).closest("tr");

        if (closestTableRow.length > 0) {
            currentLatInput = closestTableRow.find(".lat").first();
            currentLngInput = closestTableRow.find(".lng").first();
        } else {
            const parentRow = $(this).closest(".row");
            currentLatInput = parentRow.find("input[name='work_latitude']").first();
            currentLngInput = parentRow.find("input[name='work_longitude']").first();
        }

        // Open the map modal
        $("#mapModal").modal("show");

        // Initialize the map
        initializeMap();
    });

    // Handle "View All Map Points" button
    $("#updateMapBtn").click(function () {
        // Open the map modal
        $("#mapModal").modal("show");

        // Initialize the map with all points
        initializeMap(true);
    });

    // Handle map modal resizing when shown
    $("#mapModal").on("shown.bs.modal", function () {
        if (map) {
            map.invalidateSize();
        }
    });

    // Cleanup when map modal is hidden
    $("#mapModal").on("hidden.bs.modal", function () {
        if (marker) {
            marker.remove();
        }
        $(".addMapBtn").focus();
    });
});

function initializeMap(showAllPoints = false) {
    if (map) {
        map.eachLayer(function (layer) {
            if (!layer._url) {
                map.removeLayer(layer);
            }
        });
    }

    const searchControl = new L.Control.Search({
        url: "https://nominatim.openstreetmap.org/search?format=json&q={s}",
        jsonpParam: "json_callback",
        propertyName: "display_name",
        propertyLoc: ["lat", "lon"],
        marker: false,
        autoCollapse: true,
        autoType: false,
        minLength: 2,
    });

    if (!map) {
        map = L.map("map").setView([22.836946, 78.354492], 5);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);
        map.addControl(searchControl);
    }


    if (showAllPoints) {
        const points = [];
        $(".lat").each(function () {
            const lat = parseFloat($(this).val());
            const lng = parseFloat($(this).closest("tr").find(".lng").val());
            if (!isNaN(lat) && !isNaN(lng)) {
                points.push([lat, lng]);
            }
        });

        if (points.length > 0) {
            points.forEach((point) => L.marker(point).addTo(map));
            if (points.length > 1) {
                const polyline = L.polyline(points, { color: "blue" }).addTo(map);

                let totalDistance = 0; // Initialize total distance
                for (let i = 0; i < points.length - 1; i++) {
                    const latlng1 = L.latLng(points[i]);
                    const latlng2 = L.latLng(points[i + 1]);
                    const segmentDistance = latlng1.distanceTo(latlng2) / 1000; // Convert to kilometers
                    totalDistance += segmentDistance;

                    // Display distance on each segment
                    const midPoint = [
                        (points[i][0] + points[i + 1][0]) / 2,
                        (points[i][1] + points[i + 1][1]) / 2
                    ];
                    L.tooltip({
                        permanent: true,
                        direction: "center",
                        className: "distance-tooltip"
                    })
                        .setLatLng(midPoint)
                        .setContent(segmentDistance.toFixed(2) + " km")
                        .addTo(map);
                }
                // Remove existing total distance control if it exists
                if (map.totalDistanceControl) {
                    map.removeControl(map.totalDistanceControl);
                }

                // Create and add a new total distance control
                map.totalDistanceControl = L.control({
                    position: 'bottomleft'
                });
                map.totalDistanceControl.onAdd = function () {
                    const div = L.DomUtil.create('div', 'total-distance');
                    div.innerHTML = `Total Distance: ${totalDistance.toFixed(2)} km`;
                    return div;
                };
                map.totalDistanceControl.addTo(map);

            }

            map.fitBounds(points, { padding: [5, 20] });
        }
    } else {
        const lat = currentLatInput.val();
        const lng = currentLngInput.val();
        if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
            marker = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 12);
        }

        searchControl.on("search:locationfound", function (e) {
            const selectedLat = e.latlng.lat.toFixed(6);
            const selectedLng = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.remove();
            }
            marker = L.marker([selectedLat, selectedLng]).addTo(map);
            map.setView([selectedLat, selectedLng], 12);
            currentLatInput.val(selectedLat);
            currentLngInput.val(selectedLng);
            fetchAddressFromCoordinates(selectedLat, selectedLng);
        });

        map.off("click").on("click", function (e) {
            const clickedLat = e.latlng.lat.toFixed(6);
            const clickedLng = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.remove();
            }
            marker = L.marker([clickedLat, clickedLng]).addTo(map);

            currentLatInput.val(clickedLat);
            currentLngInput.val(clickedLng);
            fetchAddressFromCoordinates(clickedLat, clickedLng);
        });
    }
}

function fetchAddressFromCoordinates(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data && data.display_name) {
                const address = data.display_name;
                $('#work_address').val(address);
                currentLatInput.closest("tr").find("textarea[name='plan_address[]']").val(address);
            }
        })
        .catch((error) => {
            console.error("Error fetching address:", error);
        });
}

function removeRow(button) {
    $(button).closest("tr").remove();
}