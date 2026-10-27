<?php
$_SESSION['apply_js']['multiselest'] = 1;

$fwdpPageOption = 'COM_VIEW_MAP_POINTS_NEW';

if (isset($_POST['submit'])) {
    if ($_POST['submit'] == 'insert') {
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
        if ($response) {
            header("location:index.php?option=" . $fwdpPageOption . "&error=9999");
            exit;
        }
    }
}

$url = API . "?mode=getUsersMapPointsData&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
$usersLocations = $result;

$error = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>
<link rel="stylesheet" href="assets/css/custom_view_map_points_new.css">

<div class="fwdp-page" id="viewMapPointsPage"
     data-users-locations='<?= json_encode(
         $usersLocations,
         JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
     ) ?>'>
    <?php if ($error) { ?>
        <div class="alert bg-success alert-dismissible fwdp-alert">
            <strong>Insert successful.</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } else { ?>
        <div class="alert alert-info alert-dismissible fwdp-alert">
            <strong>Click a field user marker to plan a visit, or click the map to place a new location.</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="fwdp-header">
        <div>
            <h1 class="fwdp-title">Map View</h1>
            <p class="fwdp-subtitle">Live field user locations for today</p>
        </div>
        <div class="fwdp-header-actions">
            <a href="index.php?option=COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW" class="fwdp-btn fwdp-btn-outline desktop-view">
                User Map Points
            </a>
            <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="fwdp-btn fwdp-btn-outline desktop-view">
                Daily Plans List
            </a>
            <a href="javascript:history.go(-1)" class="fwdp-btn desktop-view">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>

    <div class="fwdp-card fwdp-map-card">
        <div class="fwdp-map-shell">
            <div class="fwdp-map-search-wrap" id="fwdpMapSearchWrap">
                <i class="fas fa-search"></i>
                <input type="text" id="fwdpMapSearch" class="form-control" placeholder="Search for a place..." autocomplete="off">
            </div>
            <div id="fwdpMap" class="fwdp-map-canvas fwdp-map-large"></div>
            <div id="fwdpMapEmpty" class="fwdp-map-empty" style="display:none;">Map key not configured.</div>

            <div id="infoContainer" class="fwdp-side-panel d-none">
                <div class="fwdp-side-panel-head">
                    <h5 class="fwdp-side-panel-title">Location Details</h5>
                    <button type="button" id="closeContainer" class="fwdp-side-close" title="Close">&times;</button>
                </div>

                <form id="fieldWorkDailyPlansForm" method="post">
                    <div class="row g-3">
                        <div class="col-12 form-group">
                            <label class="fwdp-label">User <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="display_name" id="display_name" disabled />
                            <div class="d-none text-danger" id="user_id_err"></div>
                            <input class="form-control" type="hidden" name="user_id" id="user_id" />
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Date <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="plan_date" id="plan_date" />
                            <div class="d-none text-danger" id="plan_date_err"></div>
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Location Name</label>
                            <input class="form-control" type="text" name="plan_location" id="plan_location">
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Contact person</label>
                            <input class="form-control" type="text" name="contact_person" id="contact_person">
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Phone No.</label>
                            <input class="form-control" type="text" name="mobile" id="mobile">
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Latitude <span class="text-danger">*</span></label>
                            <input class="form-control lat" type="text" name="plan_latitude" id="plan_latitude" readonly>
                            <div class="d-none text-danger" id="plan_latitude_err"></div>
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Longitude <span class="text-danger">*</span></label>
                            <input class="form-control lng" type="text" name="plan_longitude" id="plan_longitude" readonly>
                            <div class="d-none text-danger" id="plan_longitude_err"></div>
                        </div>
                        <div class="col-12 form-group">
                            <label class="fwdp-label">Address</label>
                            <textarea class="form-control fwdp-address-field" name="plan_address" id="plan_address" rows="3" readonly></textarea>
                        </div>
                        <div class="col-12">
                            <div class="fwdp-coords-preview">
                                <span class="fwdp-coord-label">Lat / Lng</span>
                                <span class="fwdp-coord-value" id="fwdpCoordPreview">--</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button id="saveContainer" type="submit" value="insert" name="submit" class="fwdp-btn fwdp-btn-primary fwdp-save-full">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.fwdpPageOption = <?php echo json_encode($fwdpPageOption); ?>;
    window.fwdpGoogleMapsKeyConfigured = <?php echo (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') ? 'true' : 'false'; ?>;
</script>
<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') { ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(GOOGLE_MAPS_API_KEY); ?>&libraries=places"></script>
<?php } ?>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_view_map_points_new.js"></script>
