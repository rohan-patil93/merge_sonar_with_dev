<?php
$_SESSION['apply_js']['multiselest'] = 1;

$fwdpPageOption = 'COM_LIST_FIELD_WORK_DAILY_PLANS_NEW';
$fwdpInsertOption = 'COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW';

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
$from_date = date('Y-m-01');
$to_date = date('Y-m-d');
?>
<link rel="stylesheet" href="assets/css/custom_list_field_work_daily_plans_new.css">

<div class="fwdp-page" id="fieldWorkDailyPlansPage"
     data-admin-type="<?= (int)$admin_type ?>"
     data-user-id="<?= $_SESSION['userid'] ?>"
     data-insert-option="<?= htmlspecialchars($fwdpInsertOption, ENT_QUOTES) ?>"
     data-app-users='<?= json_encode(
         $application_users,
         JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
     ) ?>'>
    <div class="alert alert-info alert-dismissible fwdp-alert">
        <strong>This function is used for maintaining <strong>Field Work</strong>. * represents mandatory fields.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="fwdp-header">
        <div>
            <h1 class="fwdp-title">Field Work Daily Plans</h1>
            <p class="fwdp-subtitle">Search and manage planned field visit locations</p>
        </div>
        <div class="fwdp-header-actions">
            <a href="index.php?option=<?= $fwdpInsertOption ?>" class="fwdp-btn fwdp-btn-primary desktop-view">
                <i class="icofont icofont-plus"></i> User Work Daily Plans
            </a>
            <a href="index.php?option=COM_LIST_FIELD_WORK_USER_MAP_POINTS_NEW" class="fwdp-btn fwdp-btn-outline desktop-view">
                <i class="fas fa-map"></i> Map Points
            </a>
            <a href="javascript:history.go(-1)" class="fwdp-btn desktop-view">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>

    <div class="fwdp-card">
        <div class="row g-3">
            <?php if ($admin_type != '3') {
                if ($admin_type != '2') { ?>
                    <div class="form-group col-lg-6">
                        <label class="fwdp-label">Manager</label>
                        <select class="form-select js-example-basic-single" id="manager_id" name="manager_id[]" multiple="multiple">
                            <option value="0">Select Any one</option>
                            <?php if (count((array)$application_users) > 0) {
                                foreach ($application_users as $row) { ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['display_name']; ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                        <div class="d-none text-danger" id="manager_id_err"></div>
                    </div>
                <?php } ?>

                <div class="form-group col-lg-6">
                    <label class="fwdp-label">User</label>
                    <select class="form-select js-example-basic-single" id="user_id" name="user_id[]" multiple="multiple">
                        <option value="0">Select Any one</option>
                    </select>
                    <div class="d-none text-danger" id="user_id_err"></div>
                </div>
            <?php } else { ?>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['userid'] ?>">
            <?php } ?>

            <div class="col-lg-4 col-xs-6 form-group">
                <label class="fwdp-label">From Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
                <div class="d-none text-danger" id="from_date_err"></div>
            </div>
            <div class="col-lg-4 col-xs-4 form-group">
                <label class="fwdp-label">To Date <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
                <div class="d-none text-danger" id="to_date_err"></div>
            </div>
            <div class="col-lg-4 col-xs-6 form-group">
                <label class="fwdp-label">&nbsp;</label>
                <button type="button" class="fwdp-btn fwdp-btn-primary form-control" id="getDataButton">Search</button>
            </div>
        </div>

        <div class="row mt-3">
            <div id="workType1" class="work-type">
                <div class="fwdp-table-wrap">
                    <table class="table fwdp-table" id="fiedWorkTable">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>User</th>
                                <th>Location Name</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>From Date</th>
                                <th>To Date</th>
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

<script>
    window.fwdpPageOption = <?php echo json_encode($fwdpPageOption); ?>;
    window.fwdpInsertOption = <?php echo json_encode($fwdpInsertOption); ?>;
</script>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_list_field_work_daily_plans_new.js"></script>
