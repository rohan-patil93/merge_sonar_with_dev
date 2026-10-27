<?php
$_SESSION['apply_js']['multiselest'] = 1;

$fwdpPageOption = 'COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW';

$admin_type = 1;
if ($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE') {
    $admin_type = 3;
}
if ($_SESSION['working_role_short_code'] == 'FIELD_MANAGER') {
    $admin_type = 2;
}

$url = API . "?mode=getApplicationUsersData&method=get&admin_type=" . $admin_type;
$result = callAPI($url);
$result = json_decode($result, 1);
$application_users = array();
$application_users = $result['application_users'];
?>

<link rel="stylesheet" href="assets/css/custom_list_field_work_user_map_points_new.css">

<div class="fwdp-page">
    <div class="alert alert-info alert-dismissible fwdp-alert">
        <strong>This function is used for maintaining <strong>Field Work</strong>. * represents mandatory fields.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="fwdp-header">
        <div>
            <h1 class="fwdp-title">Field Work Daily Plans Map View</h1>
            <p class="fwdp-subtitle">Review visit status and open routes on Google Maps</p>
        </div>
        <div class="fwdp-header-actions">
            <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="fwdp-btn fwdp-btn-outline desktop-view">
                Daily Plans List
            </a>
            <a href="index.php?option=COM_VIEW_MAP_POINTS_NEW" class="fwdp-btn fwdp-btn-outline desktop-view">
                Live Map View
            </a>
            <a href="javascript:history.go(-1)" class="fwdp-btn desktop-view">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>

    <div class="fwdp-card">
        <div class="row g-3">
            <div class="form-group col-lg-5">
                <label class="fwdp-label">User <span class="text-danger">*</span></label>
                <select class="form-select js-example-basic-multiple" id="user_id" name="user_id[]" multiple="multiple">
                    <option value="0">Select Any one</option>
                    <?php if (count((array)$application_users) > 0) {
                        foreach ($application_users as $row) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['display_name']; ?>
                            </option>
                        <?php }
                    } ?>
                </select>
                <div class="d-none text-danger" id="user_id_err"></div>
            </div>

            <div class="col-lg-3 col-xs-4 form-group">
                <label class="fwdp-label">From Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="from_date" id="from_date" value="">
                <div class="d-none text-danger" id="from_date_err"></div>
            </div>
            <div class="col-lg-3 col-xs-4 form-group">
                <label class="fwdp-label">To Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="to_date" id="to_date" value="">
                <div class="d-none text-danger" id="to_date_err"></div>
            </div>

            <div class="col-lg-1 col-xs-4 form-group">
                <label class="fwdp-label">&nbsp;</label>
                <button type="button" class="fwdp-btn fwdp-btn-primary form-control" id="getDataButton">Search</button>
            </div>
        </div>

        <div class="row mt-3">
            <div id="workType1" class="work-type d-none">
                <div class="fwdp-table-wrap">
                    <table class="table fwdp-table" id="fiedWorkTable">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Visited</th>
                                <th>Extra</th>
                                <th>Pending</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content fwdp-map-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Map View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="fwdp-map-legend" id="fwdpMapLegend">
                    <span class="fwdp-legend-item"><span class="fwdp-dot fwdp-dot-visited"></span> Visited</span>
                    <span class="fwdp-legend-item"><span class="fwdp-dot fwdp-dot-extra"></span> Extra</span>
                    <span class="fwdp-legend-item"><span class="fwdp-dot fwdp-dot-pending"></span> Not Visited</span>
                </div>
                <div class="fwdp-map-body">
                    <div id="fwdpMap" class="fwdp-map-canvas"></div>
                    <div id="fwdpMapEmpty" class="fwdp-map-empty" style="display:none;">Map key not configured.</div>
                    <div id="fwdpMapDistance" class="fwdp-map-distance" style="display:none;"></div>
                </div>
                <div class="fwdp-map-selected">
                    <label class="fwdp-label">Route Summary</label>
                    <p id="fwdpSelectedAddress">Select a row and open the map to view visit points.</p>
                    <div id="fwdpRouteSummary" class="fwdp-route-summary" style="display:none;"></div>
                    <div id="fwdpNavActions" class="fwdp-nav-actions" style="display:none;">
                        <a id="fwdpOpenGoogleNav" href="#" target="_blank" rel="noopener noreferrer" class="fwdp-btn fwdp-btn-primary">
                            <i class="fas fa-directions"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="fwdp-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.fwdpPageOption = <?php echo json_encode($fwdpPageOption); ?>;
    window.fwdpGoogleMapsKeyConfigured = <?php echo (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') ? 'true' : 'false'; ?>;
</script>
<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') { ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(GOOGLE_MAPS_API_KEY); ?>&libraries=geometry"></script>
<?php } ?>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_list_field_work_user_map_points_new.js"></script>
