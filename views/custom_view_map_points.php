<?php
$_SESSION['apply_js']['multiselest'] = 1;

if (isset($_POST['submit']) && $_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $table_name = 'field_work_daily_plans';
        $data = $_POST;
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date'] = nowDate();
        $data['company_id'] = $_SESSION['company_id'];
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
    }

$url = API . "?mode=getUsersMapPointsData&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
$usersLocations = $result;

?>
<link rel="stylesheet" href="assets/css/custom_view_map_points.css">
<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet.css">
<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet.extra-markers.min.css">

<div class="page-body" id="viewMapPointsPage"
     data-users-locations='<?= json_encode(
         $usersLocations,
         JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
     ) ?>'>
    <div class="card">
        <div class="card-header">
            <h5>Map View</h5>
        </div>
        <div class="card-body p-3 " id="mapModal">
            <div id="map" class="map-large"></div>
        </div>

    </div>
</div>

<div id="infoContainer" class="info-container d-none card-block custom-scroll">

    <div class="d-flex justify-between align-center">
        <h5>Location Details</h5>
        <button id="closeContainer" class="info-close-btn">&times;</button>
    </div>
    <hr>

    <form id="fieldWorkDailyPlansForm" method="post">
        <div class="row">
            <div  class="col-lg-12 col-xs-12 form-group">
                <span>User <span class="text-danger">*</span></span>
                <input aria-label="Display Name" class="form-control" type="text" name="display_name" id="display_name"  disabled />
                <div class="d-none text-danger" id="user_id_err"></div>
                <input class="form-control" type="hidden" name="user_id" id="user_id" />
            </div>
            <div  class="col-lg-12 col-xs-12 form-group">
                <span>Date<span class="text-danger">*</span></span>
                <input aria-label="Plan Date" class="form-control" type="date" name="plan_date" id="plan_date" />
                <div class="d-none text-danger" id="plan_date_err"></div>
            </div>
            <!-- Location -->
            <div class="col-lg-12 col-xs-12 form-group">
                <span>Location Name</span>
                <input aria-label="Plan Location" class="form-control" type="text" name="plan_location" id="plan_location">
            </div>
            <div class="col-lg-12 col-xs-6 form-group">
                <span>Contact person </span>
                <input aria-label="Contact Person" class="form-control lat" type="text" name="contact_person" id="contact_person" >

            </div>
            <div class="col-lg-12 col-xs-6 form-group">
                <span>Phone No.</span>
                <input aria-label="Mobile" class="form-control lat" type="text" name="mobile" id="mobile" >

            </div>

            <!-- Latitude -->
            <div class="col-lg-12 col-xs-6 form-group">
                <span>Latitude <span class="text-danger">*</span> </span>
                <input aria-label="Plan Latitude" class="form-control lat" type="text" name="plan_latitude" id="plan_latitude"  readonly>
                <div class="d-none text-danger" id="plan_latitude_err"></div>
            </div>

            <!-- Longitude -->
            <div class="col-lg-12 col-xs-6 form-group">
                <span>Longitude <span class="text-danger">*</span></span>
                <input aria-label="Plan Longitude" class="form-control lng" type="text" name="plan_longitude" id="plan_longitude"  readonly>
                <div class="d-none text-danger" id="plan_longitude_err"></div>
            </div>
            <div class="col-lg-12 col-xs-12 form-group">
                <span>Address</span>
                <textarea aria-label="Plan Address" class="form-control" type="text" name="plan_address" id="plan_address" readonly></textarea>
            </div>

            <div class="border-top">
                <button id="saveContainer" type="submit" value='insert'  name='submit' class="save-btn" >Save</button>

            </div>
        </div>
    </form>
</div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/bower_components/leaflet/leaflet.js" ></script>
<script src="assets/bower_components/leaflet/leaflet.extra-markers.min.js" ></script>
<script src="assets/js/custom_view_map_points.js"></script>
