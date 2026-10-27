<?php
$_SESSION['apply_js']['multiselest'] = 1;

$fwdpPageOption = 'COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW';

$admin_type = 1;
if ($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE') {
    $admin_type = 3;
}
if ($_SESSION['working_role_short_code'] == 'FIELD_MANAGER') {
    $admin_type = 2;
}

if (isset($_POST['submit'])) {
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        if ($_POST['user_field_work_type'] == '1' || $_POST['user_field_work_type'] == '2') {
            unset($_POST['user_field_work_type']);
            $table_name = 'field_work_daily_plans';
            $data = $_POST;
            $res = insertData($table_name, $data);
            if ($res) {
                header("location:index.php?option=" . $fwdpPageOption . "&user_id=" . $_POST['user_id'] . "&date=" . $_POST['plan_date'] . "&error=9999");
            }
        } else {
            unset($_POST['user_field_work_type'], $_POST['plan_date']);
            $table_name = 'user_field_work_details';
            $data = $_POST;
            $res = insertUserFieldData($table_name, $data);
            if ($res) {
                header("location:index.php?option=" . $fwdpPageOption . "&user_id=" . $_POST['user_id'] . "&date=" . $_POST['work_from_date'] . "&error=9999");
            }
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        if ($_POST['user_field_work_type'] == '1' || $_POST['user_field_work_type'] == '2') {
            unset($_POST['user_field_work_type']);
            $table_name = 'field_work_daily_plans';
            $rid = $_POST['rid'];
            $date = $_POST['date'];
            $url = API . "?mode=updateFieldWorkDailyPlans&method=get&rid=" . $rid . "&date=" . $date . "&table=" . $table_name;
            $result = callAPI($url);
            $result = json_decode($result, 1);

            if ($result['status'] == 'sucess') {
                unset($_POST['rid'], $_POST['date']);
                $data = $_POST;
                $res = insertData($table_name, $data);
                if ($res) {
                    header("location:index.php?option=" . $fwdpPageOption . "&user_id=" . $_POST['user_id'] . "&date=" . $_POST['plan_date'] . "&error=9998");
                }
            }
        } else {
            $table_name = 'user_field_work_details';
            unset($_POST['user_field_work_type'], $_POST['plan_date']);
            $id = $_POST['user_field_work_details_id'];
            $url = API . "?mode=updateFieldWorkDailyPlans&method=get&rid=" . $id . "&table=" . $table_name;
            $result = callAPI($url);
            $result = json_decode($result, 1);
            if ($result['status'] == 'sucess') {
                unset($_POST['rid'], $_POST['date'], $_POST['user_field_work_details_id']);
                $data = $_POST;
                $res = insertUserFieldData($table_name, $data);
                if ($res) {
                    header("location:index.php?option=" . $fwdpPageOption . "&user_id=" . $_POST['user_id'] . "&date=" . $_POST['work_from_date'] . "&error=9998");
                }
            }
        }
    }
}

function insertData($table_name, $data)
{
    $result = array();
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    $data['status_date'] = nowDate();
    $data['company_id'] = $_SESSION['company_id'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $k = count($data['plan_latitude']);
    $contact_person = $data['contact_person'];
    $mobile = $data['mobile'];
    $plan_latitude = $data['plan_latitude'];
    $plan_longitude = $data['plan_longitude'];
    $plan_location = $data['plan_location'];
    $plan_address = $data['plan_address'];
    $plan_time = isset($data['plan_time']) ? $data['plan_time'] : array();
    unset($data['plan_latitude'], $data['plan_longitude'], $data['plan_location'], $data['plan_address'], $data['plan_time'], $data['contact_person'], $data['mobile']);
    for ($i = 0; $i < $k; $i++) {
        $data['contact_person'] = $contact_person[$i];
        $data['mobile'] = $mobile[$i];
        $data['plan_latitude'] = $plan_latitude[$i];
        $data['plan_longitude'] = $plan_longitude[$i];
        $data['plan_location'] = $plan_location[$i];
        $data['plan_address'] = $plan_address[$i];
        $data['plan_time'] = (isset($plan_time[$i]) && $plan_time[$i] != '') ? $plan_time[$i] : '';
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $result[] = $response;
    }
    return $result;
}

function insertUserFieldData($table_name, $data)
{
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    $data['status_date'] = nowDate();
    $data['company_id'] = $_SESSION['company_id'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
    $response = file_post_contents($url, $data, 'insert');
    return $response;
}

$url = API . "?mode=getApplicationUsersDataforInsert&method=get&admin_type=" . $admin_type;
$result = callAPI($url);
$result = json_decode($result, 1);
$application_users = array();
$application_users = $result['application_users'];

$rid = '';
$date = '';
$view = '';
$copy = '';
$error = '';
if ($_GET) {
    $rid = $_GET['user_id'] ?? '';
    $date = $_GET['date'] ?? '';
    $view = $_GET['view'] ?? '';
    $copy = $_GET['copy'] ?? '';
    $error = $_GET['error'] ?? '';
}

$planDateReadonly = '';
if ($rid != "" && $view == '1') {
    $planDateReadonly = 'readonly';
}
?>

<link rel="stylesheet" href="assets/css/custom_insert_field_work_daily_plans_new.css">

<div class="fwdp-page">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <?php if ($error) { ?>
                        <div class="alert <?php echo ($error == '9999') ? 'bg-success' : 'bg-warning'; ?> alert-dismissible fwdp-alert">
                            <strong>
                                <?php
                                if ($error == '9999') {
                                    echo "Insert successful.";
                                } elseif ($error == '9998') {
                                    echo "Update successful.";
                                }
                                ?>
                            </strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info alert-dismissible fwdp-alert">
                            <strong>This function is used for maintaining <strong>Field Work</strong>. * represents mandatory fields.</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <div class="fwdp-header">
                        <div>
                            <h1 class="fwdp-title">Field Work Daily Plans</h1>
                            <p class="fwdp-subtitle">Plan and manage field visit locations</p>
                        </div>
                        <div class="fwdp-header-actions">
                            <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS_NEW" class="fwdp-btn fwdp-btn-primary desktop-view">User Work Daily Plans List</a>
                            <a href="javascript:history.go(-1)" class="fwdp-btn desktop-view">
                                <i class="icofont icofont-reply"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="fwdp-card">
                        <form id="fieldWorkDailyPlansForm" method="post">
                            <div class="row g-3">
                                <div class="col-md-4 form-group">
                                    <label class="fwdp-label">User <span class="text-danger">*</span></label>
                                    <select class="form-select js-example-basic-single" id="user_id" name="user_id">
                                        <option value="0">Select Any one</option>
                                        <?php if (count((array)$application_users) > 0) {
                                            foreach ($application_users as $row) { ?>
                                                <option value="<?php echo $row['id']; ?>" data-work-type="<?php echo $row['user_field_work_type']; ?>" <?php echo $rid == $row['id'] ? 'selected' : ''; ?>>
                                                    <?php echo $row['display_name']; ?>
                                                </option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <div class="d-none text-danger" id="user_id_err"></div>
                                </div>

                                <div id="toDateDiv" class="col-md-4 form-group d-none">
                                    <label class="fwdp-label">Date <span class="text-danger">*</span></label>
                                    <input class="form-control" type="date" name="plan_date" id="plan_date" value="<?php echo isset($date) && $date != '' ? date('Y-m-d', strtotime($date)) : ''; ?>" <?php echo $planDateReadonly; ?> min="<?php echo date('Y-m-d'); ?>" />
                                    <div class="d-none text-danger" id="plan_date_err"></div>
                                </div>
                            </div>

                            <input type="hidden" id="user_field_work_type" name="user_field_work_type" value="">

                            <div id="workType1" class="fwdp-work-type d-none">
                                <div class="fwdp-section-header">
                                    <h3 class="fwdp-section-title">Visit Locations</h3>
                                    <?php if ($view != '1') { ?>
                                        <button type="button" class="fwdp-btn fwdp-btn-primary" id="addMoreBtn">+ Add Location</button>
                                    <?php } ?>
                                </div>
                                <div id="locationCardsContainer">
                                    <div class="fwdp-location-card">
                                        <div class="fwdp-location-card-head">
                                            <span class="fwdp-location-num">1</span>
                                            <button type="button" class="fwdp-remove-row" title="Remove location"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="fwdp-label">Contact Person</label>
                                                <input class="form-control" type="text" name="contact_person[]">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="fwdp-label">Phone No.</label>
                                                <input class="form-control" type="text" name="mobile[]">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="fwdp-label">Location Name</label>
                                                <input class="form-control" type="text" name="plan_location[]">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="fwdp-label">Planned Time</label>
                                                <input class="form-control" type="time" name="plan_time[]">
                                            </div>
                                            <div class="col-12">
                                                <label class="fwdp-label">Address</label>
                                                <textarea class="form-control fwdp-address-field" name="plan_address[]" rows="2" readonly></textarea>
                                            </div>
                                            <div class="col-md-6 d-none">
                                                <input class="form-control lat" type="text" name="plan_latitude[]" readonly>
                                            </div>
                                            <div class="col-md-6 d-none">
                                                <input class="form-control lng" type="text" name="plan_longitude[]" readonly>
                                            </div>
                                        </div>
                                        <div class="fwdp-location-footer">
                                            <div class="fwdp-coords-preview">
                                                <span class="fwdp-coord-label">Lat / Lng</span>
                                                <span class="fwdp-coord-value">--</span>
                                            </div>
                                            <button type="button" class="fwdp-btn fwdp-btn-map addMapBtn">
                                                <i class="fas fa-map-marker-alt"></i> Pick on Map
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="fwdp-map-actions">
                                    <button type="button" class="fwdp-btn fwdp-btn-outline" id="updateMapBtn">View All on Map</button>
                                </div>
                            </div>

                            <div id="workType2" class="fwdp-work-type d-none">
                                <div class="fwdp-section-header">
                                    <h3 class="fwdp-section-title">Security Work Details</h3>
                                </div>
                                <div class="fwdp-security-card">
                                    <div class="row g-3">
                                        <div class="col-lg-6 form-group">
                                            <label class="fwdp-label">From Date <span class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="work_from_date" id="work_from_date" value="" min="<?php echo date('Y-m-d'); ?>">
                                            <div class="d-none text-danger" id="work_from_date_err"></div>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label class="fwdp-label">To Date <span class="text-danger">*</span></label>
                                            <input class="form-control" type="date" name="work_to_date" id="work_to_date">
                                            <div class="d-none text-danger" id="work_to_date_err"></div>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label class="fwdp-label">Time <span class="text-danger">*</span></label>
                                            <input class="form-control" type="time" name="work_time" id="work_time">
                                            <div class="d-none text-danger" id="work_time_err"></div>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label class="fwdp-label">Work Radius</label>
                                            <input class="form-control" type="number" name="work_radius" id="work_radius">
                                            <div class="d-none text-danger" id="work_radius_err"></div>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Contact Person</label>
                                            <input class="form-control" type="text" name="contact_person" id="contact_person">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Phone No.</label>
                                            <input class="form-control" type="text" name="mobile" id="mobile">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Location Name</label>
                                            <input class="form-control" type="text" name="location_name" id="location_name">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Latitude <span class="text-danger">*</span></label>
                                            <input class="form-control lat" type="text" name="work_latitude" id="work_latitude" readonly>
                                            <div class="d-none text-danger" id="work_latitude_err"></div>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Longitude <span class="text-danger">*</span></label>
                                            <input class="form-control lng" type="text" name="work_longitude" id="work_longitude" readonly>
                                            <div class="d-none text-danger" id="work_longitude_err"></div>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="fwdp-label">Address</label>
                                            <textarea class="form-control fwdp-address-field" name="work_address" id="work_address" rows="2" readonly></textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="fwdp-btn fwdp-btn-map addMapBtn">
                                                <i class="fas fa-map-marker-alt"></i> Pick on Map
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="saveButton" class="fwdp-save-bar d-none">
                                <?php if ($view != '1') {
                                    if ($rid != "" && $copy != '1') { ?>
                                        <button name="submit" value="update" class="fwdp-btn fwdp-btn-primary">Update</button>
                                        <input name="rid" type="hidden" value="<?php echo $rid; ?>">
                                        <input name="date" type="hidden" value="<?php echo $date; ?>">
                                    <?php } else { ?>
                                        <button name="submit" value="insert" class="fwdp-btn fwdp-btn-primary">Save</button>
                                    <?php }
                                } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content fwdp-map-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Select Location on Map</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="fwdp-map-search-wrap" id="fwdpMapSearchWrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="fwdpMapSearch" class="form-control" placeholder="Search for a place..." autocomplete="off">
                </div>
                <div class="fwdp-map-body">
                    <div id="fwdpMap" class="fwdp-map-canvas"></div>
                    <div id="fwdpMapEmpty" class="fwdp-map-empty" style="display:none;">Map key not configured.</div>
                    <div id="fwdpMapDistance" class="fwdp-map-distance" style="display:none;"></div>
                </div>
                <div class="fwdp-map-selected" id="fwdpMapSelectedWrap">
                    <label class="fwdp-label">Route Summary</label>
                    <p id="fwdpSelectedAddress">Click on the map or search to select a location.</p>
                    <div id="fwdpRouteSummary" class="fwdp-route-summary" style="display:none;"></div>
                    <div id="fwdpNavActions" class="fwdp-nav-actions" style="display:none;">
                        <a id="fwdpOpenGoogleNav" href="#" target="_blank" rel="noopener noreferrer" class="fwdp-btn fwdp-btn-primary">
                            <i class="fas fa-directions"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="fwdpMapModalFooter">
                <button type="button" class="fwdp-btn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="fwdp-btn fwdp-btn-confirm" id="fwdpConfirmLocation">Confirm Location</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.fwdpPageOption = <?php echo json_encode($fwdpPageOption); ?>;
    window.fwdpGoogleMapsKeyConfigured = <?php echo (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') ? 'true' : 'false'; ?>;
</script>
<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY != '') { ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(GOOGLE_MAPS_API_KEY); ?>&libraries=places,geometry"></script>
<?php } ?>
<script src="assets/js/custom_insert_field_work_daily_plans_new.js"></script>
