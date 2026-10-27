<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';
$work_admin_type=1;
if (
    $_SESSION['working_role_short_code'] == 'EMPLOYEE' &&
    $_SESSION['employee']['department_id'] != 4 &&
    $_SESSION['employee']['designation_id'] != 59 &&
    $_SESSION['employee']['designation_id'] != 75
){
    $work_admin_type=2;
}

$editMode = false;
$editData = null;
$editId = '';

if (isset($_GET['Rid']) && !empty($_GET['Rid'])) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $id = $rid['1'];

    if ($id && is_numeric($id)) {
        $editId = $id;
        $editMode = true;
    }
}

$url = API . "?mode=getpersonalgatewaydetails" . "&method=get" . "&company_id=" . $_SESSION['company_id'];
if ($editMode && $editId) {
    $url .= "&rid=" . $editId;
}

$response = callAPI($url);
$datas = json_decode($response, true);

if ($editMode && isset($datas['transport_personal_gateway'])) {
    $editData = $datas['transport_personal_gateway'];

    // Decode person_details JSON to get employee info
    if (!empty($editData['person_details'])) {
        $personDetails = json_decode($editData['person_details'], true);
        if ($personDetails) {
            $editData['employee_id'] = $personDetails['employee_id'] ?? '';
            $editData['employee_name'] = $personDetails['employee_name'] ?? '';
            $editData['employee_contact'] = $personDetails['employee_contact'] ?? '';
            $editData['employee_email'] = $personDetails['employee_email'] ?? '';
        }
    }
}

$locations = $datas['result'] ?? $datas['locations'] ?? [];
if (!is_array($locations)) {
    $locations = [];
}

// Get application users list
$applicationUsers = $datas['application_users'] ?? [];
if (!is_array($applicationUsers)) {
    $applicationUsers = [];
}

$vehicles = $datas['transport_vehicle_master'] ?? [];
if (!is_array($vehicles)) {
    $vehicles = [];
}

$currentPersons = [];
$statistics = [
    'current_count' => 0,
    'today_entries' => 0,
    'today_exits' => 0,
    'avg_duration_minutes' => 0
];
$recentActivity = [];

$visitPurposes = [
    'official' => 'Official Work',
    'meeting' => 'Meeting',
    'delivery' => 'Delivery',
    'pickup' => 'Pickup',
    'personal' => 'Personal',
    'other' => 'Other',
    'lunch' => 'Lunch'
];
$gateway_types = $datas['gateway_types'] ?? array();

$employees = $datas['employees'] ?? [];

?>
<script>
 function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
 }

 function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        console.log("dataa aa");
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                        window.location.reload();
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
    });
 }

</script>

<div class="d-none personal-gateway-php-data"
    data-company-id="<?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>"
    data-user-id="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>"
    data-company-name="<?php echo isset($_SESSION['config']['name']) ? htmlspecialchars($_SESSION['config']['name']) : 'Company Name'; ?>"
    data-company-logo="<?php echo isset($_SESSION['config']['display_logo']) && !empty($_SESSION['config']['display_logo']) ? 'uploads/all/' . htmlspecialchars($_SESSION['config']['display_logo']) : ''; ?>"
    data-display-name="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : ''; ?>"
    data-edit-mode="<?php echo $editMode ? '1' : '0'; ?>"
    data-edit-id="<?php echo $editId; ?>"
    data-edit-data="<?php echo $editMode && $editData ? htmlspecialchars(json_encode($editData), ENT_QUOTES, 'UTF-8') : ''; ?>">
</div>
<input type="hidden" name="work_admin_type" id="work_admin_type" value="<?php echo $work_admin_type; ?>">

<!-- Include external CSS -->
<link rel="stylesheet" href="assets/css/custom_insert_transport_gateway.css">

<div class="page-body">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card bg-primary order-card">
                <div class="card-body">
                    <h6 class="text-white">Persons Inside</h6>
                    <h2 class="text-right text-white"><i class="ti-user f-left"></i> <span id="stat-current-count"> 0</span></h2>
                    <p class="m-b-0 text-white">Currently in premises</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card bg-success order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Entries</h6>
                    <h2 class="text-right text-white"><i class="ti-import f-left"></i> <span id="stat-today-entries"> 0</span></h2>
                    <p class="m-b-0 text-white">Persons entered today</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card bg-danger order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Exits</h6>
                    <h2 class="text-right text-white"><i class="ti-export f-left"></i> <span id="stat-today-exits"> 0</span></h2>
                    <p class="m-b-0 text-white">Persons exited today</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card bg-warning order-card">
                <div class="card-body">
                    <h6 class="text-white">Avg. Duration</h6>
                    <h2 class="text-right text-white"><i class="ti-time f-left"></i> <span id="stat-avg-duration"> 0h</span></h2>
                    <p class="m-b-0 text-white">Average stay time</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                if($editData['current_status'] !=""){

                $workflowsteps = getWorkflowStatus('transport_personal_gateway', $editId, $editData['current_status']);
                echo $workflowsteps['current_status_ribbon'];
                }?>

                <div class="card-header table-card-header">
                    <h5><i class="ti-user"></i> <?php echo $editMode ? 'Edit Personal Gateway Entry' : 'Personal Gateway Entry'; ?></h5>
                    <div class="card-header-right table-card-header">
                        <?php echo $workflowsteps['workflow_btn'];?>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
            if($editData['current_status'] !=""){
                echo $workflowsteps['breadcrumb'];
            }?>
                    <form id="personalEntryForm" <?php if($editData['current_status']>0){echo 'style="pointer-events: none; opacity: 0.7;"';
} ?>>
                        <?php if ($editMode && $editData): ?>
                            <input type="hidden" name="entry_id" value="<?php echo $editData['id']; ?>">
                            <input type="hidden" name="edit_mode" value="1">
                        <?php endif; ?>


                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <span class="form-label">Movement Type <span class="text-danger">*</span></span>
                                    <div class="d-flex gap-3">
                                        <div class="transport-gateway-radio radio-success mr-4">
                                            <input type="radio" name="method_type" id="method_entry_first" value="entry_first"
                                                <?php echo ($editMode && ($editMode && $editData && ($editData['method_type'] ?? 'entry_first') == 'entry_first')) ? 'checked' : ''; ?>>
                                            <label for="method_entry_first" class="mr-3">
                                                <i class="ti-import text-success"></i> Entry First (Person Coming In)
                                            </label>
                                        </div>
                                        <div class="transport-gateway-radio radio-danger">
                                            <input type="radio" name="method_type" id="method_exit_first" value="exit_first"
                                                <?php echo (!$editMode || $editData && ($editData['method_type'] ?? 'exit_first') == 'exit_first') ? 'checked' : ''; ?>>
                                            <label for="method_exit_first">
                                                <i class="ti-export text-danger"></i> Exit First (Person Going Out)
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Select "Exit First" when staff is going out for official work</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Employee Name  <span class="text-danger">*</span></span>
                                    <div class="transport_select">
                                        <select aria-label="Employee" class="form-select js-example-basic-single" name="employee_id" id="employeeSelect" required>
                                            <option value="">Select Employee</option>
                                            <?php
                                            $sessionEmployeeId = "";
                                            if ($work_admin_type == 2) {
                                                $sessionEmployeeId = isset($_SESSION['emp_id']) ? $_SESSION['emp_id'] : '';
                                            }
                                            $selectedEmployeeId = $editMode && $editData ? ($editData['employee_id'] ?? '') : $sessionEmployeeId;

                                            foreach ($employees as $user):
                                                $isSelected = ($selectedEmployeeId != '' && $user['id'] == $selectedEmployeeId) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo htmlspecialchars($user['id']); ?>"
                                                    data-mobile="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email_id'] ?? ''); ?>"
                                                    data-name="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>"
                                                    <?php echo $isSelected; ?>>
                                                    <?php echo htmlspecialchars($user['display_name'].' ('.$user['emp_code'].')' ?? ''); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Contact Number</span>
                                    <div>
                                        <input aria-label="Contact number" type="tel" class="form-control" name="employee_contact" id="employeeContact"
                                            placeholder="Contact number" readonly
                                            value="<?php echo $editMode && $editData ? htmlspecialchars($editData['employee_contact'] ?? '') : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Email</span>
                                    <div>
                                        <input aria-label="Email address" type="email" class="form-control" name="employee_email" id="employeeEmail"
                                            placeholder="Email address" readonly
                                            value="<?php echo $editMode && $editData ? htmlspecialchars($editData['employee_email'] ?? '') : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Purpose <span class="text-danger">*</span></span>
                                    <div class="transport_select">
                                        <select aria-label="Visit Purpose" class="form-select js-example-basic-single" name="visit_purpose" id="visitPurpose" required>
                                            <option value="">Select Purpose</option>
                                            <?php
                                            $selectedPurpose = $editMode && $editData ? ($editData['visit_purpose'] ?? '') : '';
                                            foreach ($visitPurposes as $key => $purpose): ?>
                                                <option value="<?php echo $key; ?>" <?php echo ($selectedPurpose == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $purpose; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Gateway Type <span class="text-danger">*</span></span>
                                    <div class="transport_select">
                                        <select aria-label="Gateway Type" class="form-select js-example-basic-single" name="gateway_type" id="gateway_type" required>
                                            <option value="">Select Gateway Type</option>
                                            <?php
                                            $gateway_type = $editMode && $editData ? ($editData['gateway_type'] ?? '') : '';
                                            if (count((array)$gateway_types)>0){ ?>
                                                <?php foreach ($gateway_types as $row){ ?>
                                                    <option value="<?php echo htmlspecialchars($row['id']); ?>"
                                                        <?php echo ($gateway_type == $row['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($row['type_name']); ?>
                                                    </option>
                                                <?php } ?>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Requested By</span>
                                    <?php $requested_by = $editMode && $editData ? (int)($editData['requested_by'] ?? 0) : 2; ?>
                                    <div class="d-flex gap-3">
                                        <div class="transport-gateway-radio radio-primary mr-4">
                                            <input type="radio" name="requested_by" id="requested_by_security" value="1"
                                                <?php echo ($requested_by == 1) ? 'checked' : ''; ?>>
                                            <label for="requested_by_security" class="mr-3">Security</label>
                                        </div>
                                        <div class="transport-gateway-radio radio-primary">
                                            <input type="radio" name="requested_by" id="requested_by_showroom_hostess" value="2"
                                                <?php echo ($requested_by != 1) ? 'checked' : ''; ?>>
                                            <label for="requested_by_showroom_hostess">Showroom Hostess</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row <?php echo ($editMode && $editData && ($editData['visit_purpose'] ?? '') == 'other') ? '' : 'd-none-initial'; ?>" id="otherPurposeRow">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Specify Purpose <span class="text-danger">*</span></span>
                                    <div>
                                        <input aria-label="Please specify purpose" type="text" class="form-control" name="other_visit_purpose" id="otherPurpose"
                                            placeholder="Please specify purpose"
                                            value="<?php echo $editMode && $editData ? htmlspecialchars($editData['other_visit_purpose'] ?? '') : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Location <span class="text-danger">*</span></span>
                                    <div class="transport_select">
                                        <select aria-label="Location" class="form-select js-example-basic-single" name="location" id="locationSelect" required>
                                            <option value="">Select Location</option>
                                            <?php
                                            $selectedLocation = $editMode && $editData ? ($editData['location'] ?? '') : '';
                                            if (!empty($locations) && is_array($locations)): ?>
                                                <?php foreach ($locations as $location): ?>
                                                    <?php if (isset($location['id'])): ?>
                                                        <option value="<?php echo htmlspecialchars($location['location_name']); ?>"
                                                            <?php echo ($selectedLocation == $location['location_name']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($location['location_name']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <option value="99999" <?php echo ($selectedLocation === '99999') ? 'selected' : ''; ?>>Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12 <?php echo ($selectedLocation === '99999') ? '' : 'd-none-initial'; ?>" id="otherLocationCol">
                                <div class="form-group">
                                    <span class="form-label">Other Location <span class="text-danger">*</span></span>
                                    <div>
                                        <input aria-label="Enter other location" type="text" class="form-control" name="other_location" id="otherLocation"
                                            placeholder="Enter other location"
                                            value="<?php echo $editMode && $editData ? htmlspecialchars($editData['other_location'] ?? '') : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Authorized By</span>
                                    <?php $selected_authorized_by = $editMode && $editData ? ($editData['authorized_by'] ?? '') : ($_SESSION['userid'] ?? ''); ?>
                                    <div class="transport_select">
                                        <select aria-label="Authorized By" class="form-select js-example-basic-single" name="authorized_by" id="authorized_by" data-selected-value="<?php echo htmlspecialchars((string)$selected_authorized_by, ENT_QUOTES, 'UTF-8'); ?>">
                                            <option value="">No data found</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="dateTimeRow">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Date</span>
                                    <div>
                                        <input aria-label="Entry Date" type="date" class="form-control" name="entry_date" id="entryDate"
                                            value="<?php echo $editMode && $editData && !empty($editData['entry_date']) ? $editData['entry_date'] : date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <span class="form-label">Time</span>
                                    <div>
                                        <input aria-label="In Time" type="time" class="form-control" name="in_time" id="inTime"
                                            value="<?php echo $editMode && $editData && !empty($editData['entry_time']) && $editData['entry_time'] != '00:00:00' ? substr($editData['entry_time'], 0, 5) : date('H:i'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <span class="form-label">Additional Notes</span>
                                    <div>
                                        <textarea aria-label="Any additional information..." class="form-control" name="notes" rows="3" placeholder="Any additional information..."><?php echo $editMode && $editData ? htmlspecialchars($editData['notes'] ?? '') : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                            <button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" id="submitBtn">
                                <i class="la la-check"></i> <span id="submitBtnText"><?php echo $editMode ? 'Update Entry' : 'Record Entry'; ?></span>
                            </button>
                            <button type="reset" class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-refresh"></i> Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-lg-4 col-md-12 col-sm-12 col-12">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-list"></i> Active Per Premises</h5>
                    <div class="card-header-right">
                        <span class="badge badge-primary text-white" id="persons-count-badge">0 persons</span>
                    </div>
                </div>
                <div class="card-body custom-scroll" style="height: 715px; overflow-y: auto;">
                    <!-- Search Box -->
                    <div class="mb-4">
                        <input aria-label="Search by name, vehicle, or purpose..." type="text" class="form-control form-control-sm" id="personSearchInput"
                            placeholder="Search by name, vehicle, or purpose..."
                            style="border-radius: 4px; padding: 6px 10px;">
                    </div>
                    <ul class="nav md-tabs infisuite_tab mb-2" id="currentPersonsTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="inside-persons-tab" data-tab="inside" data-toggle="tab" href="#insidePersons" role="tab" aria-controls="insidePersons" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                Pending <span class="badge badge-warning" id="inside-persons-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="exited-persons-tab" data-tab="exited" data-toggle="tab" href="#exitedPersons" role="tab" aria-controls="exitedPersons" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                Approved <span class="badge badge-danger" id="exited-persons-count">0</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="currentPersonsTabContent">
                        <div class="tab-pane fade show active" id="insidePersons" role="tabpanel" aria-labelledby="inside-persons-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="insidePersonsTable">
                                    <tbody>
                                        <tr>
                                            <th scope="col" colspan="2" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                                <p class="text-muted mt-2">Loading...</p>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="exitedPersons" role="tabpanel" aria-labelledby="exited-persons-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="exitedPersonsTable">
                                    <tbody>
                                        <tr>
                                            <th scope="col" colspan="2" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                                <p class="text-muted mt-2">Loading...</p>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-time"></i> Recent Activity</h5>
                    <div class="card-header-right">
                        <a href="index.php?option=COM_LIST_PERSONAL_GATEWAY" class="btn btn-sm btn-primary">
                            <i class="ti-eye text-white"></i> View All Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentActivityTable">
                            <thead>
                                <tr>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Vehicle</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">In/Exit Time</th>
                                    <th scope="col">Print</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center p-4">
                                        <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                        <p class="text-muted mt-2">Loading recent activity...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exit Modal -->
<div class="modal fade" id="exitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-export"></i> Record Exit</h4>
                <button type="button" class="close" onclick="closeExitModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exitForm">
                <div class="modal-body">
                    <input type="hidden" name="entry_id" id="exitEntryId">
                    <input type="hidden" name="method_type" id="actionMethodType" value="entry_first">
                    <input type="hidden" name="action_status" id="actionStatusInput" value="">
                    <div class="alert alert-info">
                        <i class="ti-info-alt"></i>
                        Recording exit for: <strong id="exitEmployeeName"></strong>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <span class="form-label">Date</span>
                            <input aria-label="Exit Date" type="date" class="form-control" name="exit_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Time</span>
                            <input aria-label="Exit Time" type="time" class="form-control" name="exit_time" value="<?php echo date('H:i'); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="form-label">Notes (Optional)</span>
                        <textarea aria-label="Any observations or notes..." class="form-control" name="exit_notes" rows="3" placeholder="Any observations or notes..."></textarea>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="goodsChecked" name="goods_checked" value="1">
                                <label class="custom-control-label" for="goodsChecked">
                                    Goods Checked
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="securityCheck" name="security_check" value="1">
                                <label class="custom-control-label" for="securityCheck">
                                    Security Check
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="form-label">Authorized By</span>
                        <input aria-label="Name of person authorizing" type="text" class="form-control" name="authorized_by" placeholder="Name of person authorizing"
                            value="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : ''; ?>">
                    </div>
                    <input type="hidden" name="gateway_type" id="gateway_type_id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" onclick="closeExitModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">
                        <i class="ti-check"></i> Record Exit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="printSlipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-printer"></i> Personal Gate Pass</h4>
            </div>
            <div class="modal-body" id="printSlipContent">
                <!-- Print content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-print-modal-btn">Close</button>
                <button type="button" class="btn btn-primary print-slip-btn">
                    <i class="ti-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include external JavaScript -->
<script src="assets/js/custom_insert_personal_gateway.js"></script>
