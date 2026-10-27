
<?php
$_SESSION['apply_js']['multiselest'] = 1;
if($_POST['submit']){
    $table_name = 'locations';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);
        if ($response["status"] == "sucess") {
            header("location:index.php?option=COM_INSERT_LOCATIONS");
        }
    }
}
$url = API . "?mode=getAllLocationsData&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
$locations = $countries = $application_users = array();
$locations = $result['locations'];
$countries = $result['countries'];
$application_users = $result['application_users'];
?>

<div class="page-body">

    <div class="card">
        <div class="card-header">
            <h5>Locations </h5>

            <div class="card-header-right">

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-infisuite mt-2">
            <div class="card-body p-4 ">
                <form id="locationsForm" method="post">

                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span>Short Name <span style="color: red;">*</span></span>
                            <input aria-label="Short Name" class="form-control" id="short_name" name="short_name" />
                            <div style="display: none; color: red;" id="short_name_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Location Name <span style="color: red;">*</span></span>
                            <input aria-label="Location Name" class="form-control" id="location_name" name="location_name" />
                            <div style="display: none; color: red;" id="location_name_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Location Address <span style="color: red;">*</span></span>
                            <textarea aria-label="Location Address" class="form-control" id="location_address" name="location_address" ></textarea>
                            <div style="display: none; color: red;" id="location_address_err"></div>
                        </div>

                        <div class="form-group col-lg-4">
                            <span>Tax Reference</span>
                            <input aria-label="Tax Ref" class="form-control" id="tax_ref" name="tax_ref" />
                            <div style="display: none; color: red;" id="tax_ref_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Parent Location ID</span>
                            <select aria-label="Parent Location" class="form-select js-example-basic-single" id="parent_location_id" name="parent_location_id">
                                <option value="0">Select Any one</option>
                                <?php foreach ($locations as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['location_name']
}</option>";
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="parent_location_id_err"></div>
                        </div>

                        <div class="form-group col-lg-4">
                            <span>Location Country</span>
                            <select aria-label="Location Country" class="form-select js-example-basic-single" id="location_country" name="location_country">
                                <option value="0">Select Any one</option>
                                <?php foreach ($countries as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['country_name']
}-{$row['other_code']
}</option>";
                                } ?>

                            </select>
                            <div style="display: none; color: red;" id="location_country_err"></div>
                        </div>

                        <div class="form-group col-lg-4">
                            <span>Location State</span>
                            <select aria-label="Location State" class="form-select js-example-basic-single" id="location_state" name="location_state">
                                <option value="0">Select Any one</option>
                            </select>
                            <div style="display: none; color: red;" id="location_state_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Location City</span>
                            <select aria-label="Location City" class="form-select js-example-basic-single" id="location_city" name="location_city">
                                <option value="0">Select Any one</option>
                            </select>
                            <div style="display: none; color: red;" id="location_city_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Location Post Code</span>
                            <input aria-label="Location Post Code" class="form-control" id="location_post_code" name="location_post_code" />
                            <div style="display: none; color: red;" id="location_post_code_err"></div>
                        </div>

                        <div class="form-group col-lg-3">
                            <span>Latitude</span>
                            <input aria-label="Loc Latitude" class="form-control" id="loc_latitude" name="loc_latitude" readonly/>
                            <div style="display: none; color: red;" id="loc_latitude_err"></div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span>Longitude</span>
                            <input aria-label="Loc Longitude" class="form-control" id="loc_longitude" name="loc_longitude" readonly/>
                            <div style="display: none; color: red;" id="loc_longitude_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Location Description</span>
                            <textarea aria-label="Location Description" class="form-control" id="location_description" name="location_description" readonly></textarea>
                            <div style="display: none; color: red;" id="location_description_err"></div>
                        </div>
                        <div class="form-group col-lg-2">
                            <span>&nbsp;</span>
                            <button type="button" class="btn btn-success form-control" id="getLocationBtn">Add Map Point</button>
                        </div>

                        <div class="form-group col-lg-3">
                            <span>In Location</span>
                            <select aria-label="In Location" class="form-select js-example-basic-single" id="in_location" name="in_location">
                                <option value="0">Select Any one</option>
                                <?php foreach ($locations as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['location_name']
}</option>";
                                } ?>
                            </select>

                            <div style="display: none; color: red;" id="in_location_err"></div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span>WIP Location</span>
                            <select aria-label="Wip Location" class="form-select js-example-basic-single" id="wip_location" name="wip_location">
                                <option value="0">Select Any one</option>
                                <?php foreach ($locations as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['location_name']
}</option>";
                                } ?>
                            </select>

                            <div style="display: none; color: red;" id="wip_location_err"></div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span>FG Location</span>
                            <select aria-label="Fg Location" class="form-select js-example-basic-single" id="fg_location" name="fg_location">
                                <option value="0">Select Any one</option>
                                <?php foreach ($locations as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['location_name']
}</option>";
                                } ?>
                            </select>

                            <div style="display: none; color: red;" id="fg_location_err"></div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span>Rejection Location</span>
                            <select aria-label="Rej Location" class="form-select js-example-basic-single" id="rej_location" name="rej_location">
                                <option value="0">Select Any one</option>
                                <?php foreach ($locations as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['location_name']
}</option>";
                                } ?>
                            </select>

                            <div style="display: none; color: red;" id="rej_location_err"></div>
                        </div>

                        <div class="form-group col-lg-4">
                            <span>HR Connect</span>
                            <select aria-label="Hr Connect" class="form-select js-example-basic-single" id="hr_connect" name="hr_connect">
                                <option value="0">Select Any one</option>
                                <?php foreach ($application_users as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['display_name']
}</option>";
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="hr_connect_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Admin Connect</span>
                            <select aria-label="Admin Connect" class="form-select js-example-basic-single" id="admin_connect" name="admin_connect">
                                <option value="0">Select Any one</option>
                                <?php foreach ($application_users as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['display_name']
}</option>";
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="admin_connect_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>Finance Connect</span>
                            <select aria-label="Finance Connect" class="form-select js-example-basic-single" id="finance_connect" name="finance_connect">
                                <option value="0">Select Any one</option>
                                <?php foreach ($application_users as $row) {
                                   echo  "<option value='{$row['id']
}'>{$row['display_name']
}</option>";
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="finance_connect_err"></div>
                        </div>
                    </div>

                    <div class="border-top">

                        <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary mt-2">
                            Save
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"  integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-search/dist/leaflet-search.min.css"  integrity="sha384-hmGjQL+woE2q+PPiSWoyxr2OIxUwljVBnQsJUMxM9phtUCIDhJHicsFADtTMMoHj" crossorigin="anonymous"/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet-search/dist/leaflet-search.min.js" integrity="sha384-R0LItKioY9lIP7pUbpLvkw3VYcmDSKW+mR5/8UKh8NiEtz0EUVgoJwzdVZl343Yz" crossorigin="anonymous"></script>
<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Select Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 32rem; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>

<script>
    let map, marker;
    let mapInitialized = false;
        // Initialize the form with default visibility
    $(document).ready(function() {
        $("#getLocationBtn").click(function() {
            $("#mapModal").modal("show");
            if (!mapInitialized) {
                initializeMap();
                mapInitialized = true;
            }
        });
        $("#mapModal").on("shown.bs.modal", function() {
            map.invalidateSize();
        });
        $("#mapModal").on("hidden.bs.modal", function() {
            // Move focus to an element outside the modal
            $("#getLocationBtn").focus();
        });

        $("#locationsForm").submit(function(e) {
            if (!validatelocationsForm()) {
                e.preventDefault();
            }
        });
        const preselectedcountryId = <?php echo json_encode($return['location_country']); ?>;
        const preselectedstateId = <?php echo json_encode($return['location_state']); ?>;
        const preselectedCityId = <?php echo json_encode($return['location_city']); ?>;

        function loadStates(countryId, selectedstateId) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'gettableData',
                    table: 'states',
                    id: countryId,
                    column: 'country_id',
                },
                success: function (response) {
                    response = JSON.parse(response);
                    const stateSelect = $("#location_state");
                    stateSelect.empty().append('<option value="0"> Select Any one </option>');
                    const tableData = Object.values(response.table);
                    if (tableData && tableData.length > 0) {
                        tableData.forEach(function (row) {
                            stateSelect.append(
                        `<option value="${row.id}" ${selectedstateId == row.id ? 'selected' : ''}>${row.state_name}</option>`
                        );
                        });
                        if (selectedstateId) {
                            loadCites(selectedstateId, preselectedCityId);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching States:", error);
                },
            });
        }

        function loadCites(stateId, selectedCityId) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    page: 'gettableData',
                    table: 'cities',
                    id: stateId,
                    column: 'state_id',
                },
                success: function (response) {
                    response = JSON.parse(response);
                    const citySelect = $("#location_city");
                    citySelect.empty().append('<option value="0"> Select Any one </option>');
                    const tableData = Object.values(response.table);
                    if (tableData && tableData.length > 0) {
                        tableData.forEach(function (row) {
                            citySelect.append(
                        `<option value="${row.id}" ${selectedCityId == row.id ? 'selected' : ''}>${row.city_name}</option>`
                        );
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching cities:", error);
                },
            });
        }

        if (preselectedcountryId && preselectedcountryId !== "0") {
            loadStates(preselectedcountryId, preselectedstateId);
        }

        // Handle building selection
        $("#location_country").change(function () {
            const countryId = $(this).val();
            if (countryId && countryId !== "0") {
                loadStates(countryId);
            } else {
                $("#location_state").empty().append('<option value="0"> Select Any one </option>');
                $("#location_city").empty().append('<option value="0"> Select Any one </option>');
            }
        });

        // Handle floor selection
        $("#location_state").change(function () {
            const stateId = $(this).val();
            if (stateId && stateId !== "0") {
                loadCites(stateId);
            } else {
                $("#location_city").empty().append('<option value="0"> Select Any one </option>');
            }
        });
    });
    function initializeMap() {
        let latitude = $("#loc_latitude").val();
        let longitude = $("#loc_longitude").val();
        if (latitude && longitude) {
            map = L.map('map').setView([latitude, longitude], 15);
            if (marker) {
                marker.remove();
            }
            marker = L.marker([latitude, longitude]).addTo(map);
        } else {
            map = L.map('map').setView([22.836946, 78.354492], 5);
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        const searchControl = new L.Control.Search({
            url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
            jsonpParam: 'json_callback',
            propertyName: 'display_name',
            propertyLoc: ['lat', 'lon'],
            marker: false,
            autoCollapse: true,
            autoType: false,
            minLength: 2,
        });
        map.addControl(searchControl);
        searchControl.on('search:locationfound', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.remove();
            }
            marker = L.marker([lat, lng]).addTo(map);

            $("#loc_latitude").val(lat);
            $("#loc_longitude").val(lng);

            map.setView([lat, lng], 12);
            fetchAddressFromCoordinates(lat, lng);
        });
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            if (marker) {
                marker.remove();
            }
            marker = L.marker([lat, lng]).addTo(map);
            $("#loc_latitude").val(lat);
            $("#loc_longitude").val(lng);
            fetchAddressFromCoordinates(lat, lng);
        });

    }
    function fetchAddressFromCoordinates(lat, lng) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

        fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data && data.display_name) {
                const address = data.display_name;
                $('#location_description').val(address)
            }
        })
        .catch((error) => {
            console.error("Error fetching address:", error);
        });
    }
    function validatelocationsForm() {
        let isValid = true;

        // Clear previous error messages
        $(".form-group div").hide();

        // Validate common mandatory fields
        const shortName = $("#short_name").val();
        const locationName = $("#location_name").val();
        const locationAddress = $("#location_address").val();

        if (!shortName) {
            $("#short_name_err").text("Please enter the Short Name").show();
            isValid = false;
        }
        if (!locationName) {
            $("#location_name_err").text("Please enter the Location Name").show();
            isValid = false;
        }
        if (!locationAddress) {
            $("#location_address_err").text("Please enter the Location Address").show();
            isValid = false;
        }

        return isValid;
    }
</script>

