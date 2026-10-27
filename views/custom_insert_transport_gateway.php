<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';

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

$url = API . "?mode=gettransportgatewaydetails&method=get&company_id=" . $_SESSION['company_id'];
if ($editMode && $editId) {
    $url .= "&rid=" . $editId;
}
$response = callAPI($url);
$datas = json_decode($response, true);
$locations = $datas['result'] ?? array();
$editData = $datas['transport_gateway'] ?? array();

// Get application users list
$applicationUsers = $datas['application_users'] ?? [];
if (!is_array($applicationUsers)) {
    $applicationUsers = [];
}

$employeeNameMap = array();
foreach ($applicationUsers as $user) {
    $userId = (string)($user['id'] ?? '');
    $displayName = trim((string)($user['display_name'] ?? ''));
    $emp_code = trim((string)($user['emp_code'] ?? ''));
    if ($userId != '' && $displayName != '') {
        $employeeNameMap[$userId] = $emp_code.' - '.$displayName;
    }
}

$currentVehicles = [];
$statistics = [
    'current_count' => 0,
    'today_entries' => 0,
    'today_exits' => 0,
    'avg_duration_minutes' => 0
];
$recentActivity = [];

$vehicleTypes = [
    'demo' => 'Demo',
    'car' => 'Car',
    'truck' => 'Truck',
    'van' => 'Van',
    'motorcycle' => 'Motorcycle',
    'bus' => 'Bus',
    'trailer' => 'Trailer',
    'other' => 'Other'
];

$demoVehicles = $datas['transport_vehicle_master'] ?? array();
$motor_cycle_list = $datas['motor_cycle_list'] ?? array();

$visitPurposes = [
    'delivery' => 'Delivery',
    'pickup' => 'Pickup',
    'meeting' => 'Business Meeting',
    'maintenance' => 'Maintenance',
    'service' => 'Service',
    'visitor' => 'Visitor',
    'employee' => 'Employee Vehicle',
    'interbranch' => 'Interbranch',
    'event' => 'Event',
    'petrolpump' => 'Petrolpump',
    'replacement' => 'Peplacement',
    'testdrive_showroom' => 'Testdrive - Showroom',
    'testdrive_doorstep' => 'Testdrive - Doorstep',
    'customer_visit' => 'Customer - Visit',
    'other' => 'Other'
];
?>
<script>
    function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data: 'table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
            type: "POST",
            success: function(data) {
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error: function() {}
        });
    }

    function submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name) {
        swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Please note the status of the document will change to " + new_status_name,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false
            },
            function() {
                console.log("dataa aa");
                jQuery.ajax({
                    url: "ajax.php",
                    data: 'page=submitstatusdynamicworkflow&table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
                    type: "POST",
                    success: function(response) {
                        console.log(response);
                        var res = JSON.parse(response);
                        window.location.reload();
                        if (res.transaction_update.status == 'sucess') {
                            window.location.reload();
                            swal({
                                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function() {
                                window.location.reload();
                            });
                        } else {
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
                    error: function() {}
                });
            });
    }
</script>

<div class="d-none transport-gateway-php-data"
    data-company-id="<?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>"
    data-user-id="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>"
    data-company-name="<?php echo isset($_SESSION['config']['name']) ? htmlspecialchars($_SESSION['config']['name']) : 'Company Name'; ?>"
    data-company-logo="<?php echo isset($_SESSION['config']['display_logo']) && !empty($_SESSION['config']['display_logo']) ? 'uploads/all/' . htmlspecialchars($_SESSION['config']['display_logo']) : ''; ?>"
    data-employee-map="<?php echo htmlspecialchars(json_encode($employeeNameMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>"
    data-display-name="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : ''; ?>"
    data-edit-mode="<?php echo $editMode ? '1' : '0'; ?>"
    data-edit-id="<?php echo $editId; ?>"
    data-edit-data="<?php echo $editMode && $editData ? htmlspecialchars(json_encode($editData), ENT_QUOTES, 'UTF-8') : ''; ?>">
    >
</div>

<!-- Include external CSS -->
<link rel="stylesheet" href="assets/css/custom_insert_transport_gateway.css">

<div class="page-body">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary order-card">
                <div class="card-body">
                    <h6 class="text-white">Vehicles Inside</h6>
                    <h2 class="text-right text-white"><i
                            class="ti-car f-left"></i> <span id="stat-current-count"> 0</span></h2>
                    <p class="m-b-0 text-white">Currently in premises</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Entries</h6>
                    <h2 class="text-right text-white"><i
                            class="ti-import f-left"></i> <span id="stat-today-entries"> 0</span></h2>
                    <p class="m-b-0 text-white">Vehicles entered today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-danger order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Exits</h6>
                    <h2 class="text-right text-white"><i
                            class="ti-export f-left"></i> <span id="stat-today-exits"> 0</span></h2>
                    <p class="m-b-0 text-white">Vehicles exited today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning order-card">
                <div class="card-body">
                    <h6 class="text-white">Avg. Duration</h6>
                    <h2 class="text-right text-white"><i
                            class="ti-time f-left"></i> <span id="stat-avg-duration"> 0h</span>
                    </h2>
                    <p class="m-b-0 text-white">Average stay time</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-8">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                if ($editData['current_status'] != "") {

                    $workflowsteps = getWorkflowStatus('transport_gateway', $editId, $editData['current_status']);
                    echo $workflowsteps['current_status_ribbon'];
                } ?>

                <div class="card-header table-card-header">
                    <h5><i class="ti-car"></i> Vehicle Gateway Entry</h5>
                    <div class="card-header-right table-card-header">
                        <?php echo $workflowsteps['workflow_btn']; ?>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    if ($editData['current_status'] != "") {
                        echo $workflowsteps['breadcrumb'];
                    } ?>
                    <form id="vehicleEntryForm" <?php if ($editData['current_status'] > 0) {
                                                    echo 'style="pointer-events: none; opacity: 0.7;"';
                                                } ?>>

                        <div class="form-group row">
                            <?php if ($editMode && $editData): ?>
                                <input type="hidden" name="entry_id" value="<?php echo $editData['id']; ?>">
                                <input type="hidden" name="edit_mode" value="1">
                            <?php endif; ?>
                            <div class="form-group col-lg-12">
                                <span class="form-label">Movement Type <span class="text-danger">*</span></span>
                                <div class="d-flex gap-3">
                                    <div class="transport-gateway-radio radio-success mr-4">
                                        <input type="radio" name="method_type" id="method_entry_first" value="entry_first"
                                            <?php echo ($editMode && ($editMode && ($editData['method_type'] ?? '') == 'entry_first')) ? 'checked' : ''; ?>>
                                        <label for="method_entry_first" class="mr-3">
                                            <i class="ti-import text-success"></i> Entry First (Vehicle Coming In)
                                        </label>
                                    </div>
                                    <div class="transport-gateway-radio radio-danger">
                                        <input type="radio" name="method_type" id="method_exit_first" value="exit_first"
                                            <?php echo (!$editMode || ($editData['method_type'] ?? 'exit_first') == 'exit_first') ? 'checked' : ''; ?>>
                                        <label for="method_exit_first">
                                            <i class="ti-export text-danger"></i> Exit First (Vehicle Going Out)
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">Select "Exit First" when staff is taking a company vehicle out</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Vehicle Type <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Vehicle Type" class="form-select js-example-basic-single" name="vehicle_type"
                                        id="vehicleType" required>
                                        <option value="">Select Type</option>
                                        <?php foreach ($vehicleTypes as $key => $type): ?>
                                            <option value="<?php echo $key; ?>"
                                                <?php echo ($editMode && ($editData['vehicle_type'] ?? '') == $key) ? 'selected' : ''; ?>>
                                                <?php echo $type; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6" id="vehicleNumberInputDiv">
                                <span class="form-label">Vehicle Number <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="e.g., ABC-1234" type="text" class="form-control text-uppercase" name="vehicle_number"
                                        id="vehicle_number" placeholder="e.g., ABC-1234" required value="<?php echo $editMode ? htmlspecialchars($editData['vehicle_number'] ?? '') : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group w-full col-lg-6 d-none-initial" id="vehicleNumberSelectDiv">
                                <span class="form-label">Vehicle Number <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Vehicle Number Select" class="form-select js-example-basic-single" name="vehicle_number_select"
                                        id="vehicle_number_select">
                                        <option value="">Select Demo Vehicle</option>
                                        <?php foreach ($demoVehicles as $vehicle): ?>
                                            <option value="<?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>"
                                                <?php echo isset($editData['vehicle_number']) && $editData['vehicle_number']!= '' && $editData['vehicle_number']==$vehicle['vehicle_reg_no'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>
                                                <?php if (!empty($vehicle['model'])): ?>
                                                    - <?php echo htmlspecialchars($vehicle['model']); ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group w-full col-lg-6 d-none-initial" id="vehicleNumberMotorCycleDiv">
                                <span class="form-label">Motor Cycle Vehicle Number <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Motor Cycle Vehicle Number" class="form-select js-example-basic-single" name="motor_cycle_vehicle_number"
                                        id="motor_cycle_vehicle_number">
                                        <option value="">Select Demo Vehicle</option>
                                        <?php foreach ($motor_cycle_list as $vehicle){ ?>
                                            <option value="<?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>"
                                        <?php echo isset($editData['vehicle_number']) && $editData['vehicle_number']!= '' && $editData['vehicle_number']==$vehicle['vehicle_reg_no'] ? 'selected' : ''; ?>
                                                >
                                                <?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>
                                                <?php if (!empty($vehicle['model'])){ ?>
                                                    - <?php echo htmlspecialchars($vehicle['model']); ?>
                                                <?php } ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Ownership Type -->
                        <div class="form-group row d-none-initial" id="vehicleOwnershipRow">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Vehicle Ownership <span class="text-danger">*</span></span>
                                <div>
                                    <div class="transport-gateway-radio radio-primary">
                                        <input type="radio" name="vehicle_ownership" id="company_vehicle" value="company">
                                        <label for="company_vehicle">Company Vehicle</label>
                                    </div>
                                    <div class="transport-gateway-radio radio-primary">
                                        <input type="radio" name="vehicle_ownership" id="private_vehicle" value="private">
                                        <label for="private_vehicle">Private Vehicle</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none-initial" id="otherVehicleTypeRow">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Specify Vehicle Type <span
                                        class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="Please specify vehicle type" type="text" class="form-control" name="other_vehicle_type"
                                        id="otherVehicleType" placeholder="Please specify vehicle type">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Employee <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Driver Name" class="form-select js-example-basic-single" name="driver_name" required>
                                        <option value="">Select Employee</option>
                                        <?php foreach ($applicationUsers as $user){ ?>
                                            <?php
                                            $displayName = $user['display_name'] ?? '';
                                            $empCode = $user['emp_code'] ?? '';
                                            $selectedDriverName = $editMode ? ($editData['driver_name'] ?? '') : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php echo ($selectedDriverName == $user['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars(trim($empCode . ' - ' . $displayName, ' -'), ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Contact</span>
                                <div>
                                    <input aria-label="Phone number" type="tel" class="form-control" name="driver_contact"
                                        placeholder="Phone number"
                                        value="<?php echo $editMode ? htmlspecialchars($editData['driver_contact'] ?? '') : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Persons Section -->
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div id="additionalPersonsContainer">
                                    <!-- Additional persons will be added here -->
                                </div>
                                <div id="newPersonInputRow" class="d-none-initial">
                                    <div class="card border mb-2">
                                        <div class="card-body py-2">
                                            <div class="form-group row mb-0">
                                                <div class="form-group col-lg-5 mb-0">
                                                    <select aria-label="New Person Select" class="form-select js-example-basic-single new-person-select" id="newPersonSelect">
                                                        <option value="">Select Employee</option>
                                                        <?php foreach ($applicationUsers as $user){ ?>
                                                            <option value="<?php echo htmlspecialchars($user['id']); ?>"
                                                                data-mobile="<?php echo htmlspecialchars($user['user_mobile'] ?? ''); ?>"
                                                                data-name="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>">
                                                                <?php echo $user['emp_code'] . " - " . htmlspecialchars($user['display_name'] ?? ''); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-5 mb-0">
                                                    <input aria-label="Contact Number" type="tel" class="form-control form-control-sm" id="newPersonContact"
                                                        placeholder="Contact Number" readonly>
                                                </div>
                                                <div class="form-group col-lg-2 mb-0">
                                                    <button type="button" class="btn btn-sm btn-success w-100 add-person-btn">
                                                        <i class="ti-check"></i> Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="showAddPersonBtn">
                                    <i class="ti-plus"></i> Add More Person
                                </button>
                                <!-- Hidden field to store person details as JSON -->
                                <input type="hidden" name="person_details" id="personDetailsJson" value="[]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Purpose of Visit <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Visit Purpose" class="form-select js-example-basic-single" name="visit_purpose"
                                        id="visitPurpose" required>
                                        <option value="">Select Purpose</option>
                                        <?php foreach ($visitPurposes as $key => $purpose): ?>
                                            <option value="<?php echo $key; ?>"
                                                <?php echo ($editMode && ($editData['visit_purpose'] ?? '') == $key) ? 'selected' : ''; ?>>
                                                <?php echo $purpose; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none-initial" id="otherPurposeRow">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Specify Purpose <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="Please specify purpose of visit" type="text" class="form-control" name="other_visit_purpose" id="otherPurpose"
                                        placeholder="Please specify purpose of visit">
                                </div>
                            </div>
                            <div class="form-group w-full col-lg-6">
                                <span class="form-label">Expected Duration</span>
                                <div>
                                    <select aria-label="Expected Duration" class="form-select js-example-basic-single" name="expected_duration">
                                        <option value="">Select Duration</option>
                                        <option value="30">30 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="120">2 hours</option>
                                        <option value="240">4 hours</option>
                                        <option value="480">8 hours</option>
                                        <option value="full_day">Full Day</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Visiting Person/Department</span>
                                <div>
                                    <input aria-label="Person or department to visit" type="text" class="form-control" name="visiting_person"
                                        placeholder="Person or department to visit" value="<?php echo $editMode ? htmlspecialchars($editData['visiting_person'] ?? '') : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Company/Organization</span>
                                <div>
                                    <input aria-label="Visitor" type="text" class="form-control" name="company_name"
                                        placeholder="Visitor's company name" value="<?php echo $editMode ? htmlspecialchars($editData['company_name'] ?? '') : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label">Location <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Location" class="form-select js-example-basic-single" name="location" id="locationSelect" required>
                                        <option value="">Select Location</option>
                                        <?php

                                        if (count((array)$locations) > 0) { ?>
                                            <?php foreach ($locations as $location) { ?>
                                                <?php if (isset($location['id'])) { ?>
                                                    <option value="<?php echo htmlspecialchars($location['location_name']); ?>"
                                                        <?php echo ($editMode && ($editData['location'] ?? '') == $location['location_name']) ? 'selected' : ''; ?>>
                                                        <?php echo $location['location_name']; ?> </option>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="99999" <?php echo ($editMode && ($editData['location'] ?? '') == '99999') ? 'selected' : ''; ?>>Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 <?php echo ($editMode && ($editData['location'] ?? '') == '99999') ? '' : 'd-none-initial'; ?>" id="otherLocationCol">
                                <span class="form-label">Other Location <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="Enter other location" type="text" class="form-control" name="other_location" id="otherLocation"
                                        placeholder="Enter other location"
                                        value="<?php echo $editMode && $editData ? htmlspecialchars($editData['other_location'] ?? '') : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-4" >
                                <span class="form-label">Vehicle Reading <span class="text-danger">*</span></span>
                                <?php
                                $vehicle_reading=0;
                                if($editMode && ($editData['method_type'] == 'entry_first')){
                                    $vehicle_reading =  $editData['vehicle_entry_reading'];
                                }elseif($editMode && ($editData['method_type'] == 'exit_first')){
                                    $vehicle_reading =  $editData['vehicle_exit_reading'];
                                }

                                ?>
                                <div>
                                    <input aria-label="12345" type="number" class="form-control text-right" name="vehicle_reading"
                                        id="vehicle_reading" step="0.01" min="0" placeholder="12345" required value="<?php echo $vehicle_reading; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="entryDateTimeRow">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Date</span>
                                <div>
                                    <input aria-label="Entry Date" type="date" class="form-control" name="entry_date"
                                        value="<?php echo $editMode && !empty($editData['entry_date']) ? $editData['entry_date'] : date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Time</span>
                                <div>
                                    <input aria-label="Entry Time" type="time" class="form-control" name="entry_time"
                                        value="<?php echo $editMode && !empty($editData['entry_time']) ? substr($editData['entry_time'], 0, 5) : date('H:i'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-12">
                                <span class="form-label">Additional Notes</span>
                                <div>
                                    <textarea aria-label="Any additional information..." class="form-control" name="notes" rows="3"
                                        placeholder="Any additional information..."><?php echo $editMode ? htmlspecialchars($editData['notes'] ?? '') : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input aria-label="Has Goods" type="checkbox" name="has_goods" value="1" id="hasGoods"
                                            <?php echo ($editMode && ($editData['has_goods'] ?? '') == 1) ? 'checked' : ''; ?>>
                                        <span class="cr"><i
                                                class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Vehicle carrying goods/materials</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none-initial" id="goodsDetails">
                            <div class="form-group col-lg-12">
                                <span class="form-label">Goods Description</span>
                                <div>
                                    <textarea aria-label="Describe the goods being carried..." class="form-control" name="goods_description" rows="2"
                                        placeholder="Describe the goods being carried..."><?php echo $editMode ? htmlspecialchars($editData['goods_description'] ?? '') : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <button type="submit"
                                class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" id="submitBtn">
                                <i class="la la-check"></i><span id="submitBtnText"><?php echo $editMode ? 'Update Entry' : 'Record Entry'; ?></span>
                            </button>
                            <button type="reset"
                                class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-refresh"></i>Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="col-lg-4">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-list"></i> Active Vehicles</h5>
                    <div class="card-header-right">
                        <span class="badge badge-primary text-white" id="vehicles-count-badge_2">0 vehicles</span>
                    </div>
                </div>
                <div class="card-body custom-scroll" style="height: 1050px; overflow-y: auto;">
                    <!-- Search Box -->
                    <div class="mb-2">
                        <input aria-label="Search by vehicle number, driver name, or purpose..." type="text" class="form-control form-control-sm" id="vehicleSearchInput_2"
                            placeholder="Search by vehicle number, driver name, or purpose..."
                            style="border-radius: 4px; padding: 6px 10px;">
                    </div>
                    <ul class="nav nav-tabs" id="currentVehiclesTabs_2">
                        <li class="nav-item">
                            <a class="nav-link active" id="pending-vehicles-tab_2" data-tab="pending" data-toggle="tab" href="#pendingVehicles" role="tab" aria-controls="pendingVehicles" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                Pending Entry/Exit <span class="badge badge-warning" id="pending-vehicles-count_2">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="missing-vehicles-tab_2" data-tab="missing" data-toggle="tab" href="#missingVehicles" role="tab" aria-controls="missingVehicles" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                Approved<span class="badge badge-danger" id="missing-vehicles-count_2">0</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="currentVehiclesTabContent_2">
                        <div class="tab-pane fade show active" id="pendingVehicles_2" role="tabpanel" aria-labelledby="pending-vehicles-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="pendingVehiclesTable_2">
                                    <tbody>
                                        <tr>
                                            <th scope="col" colspan="2" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                                <p class="text-muted mt-2">Loading vehicles...</p>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="missingVehicles_2" role="tabpanel" aria-labelledby="missing-vehicles-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="missingVehiclesTable_2">
                                    <tbody>
                                        <tr>
                                            <th scope="col" colspan="2" class="text-center p-4">
                                                <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                                <p class="text-muted mt-2">Loading vehicles...</p>
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
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-time"></i> Recent Activity</h5>
                    <div class="card-header-right">
                        <a href="index.php?option=custom_list_transport_gateway" class="btn btn-sm btn-primary">
                            <i class="ti-eye"></i> View All Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentActivityTable">
                            <thead>
                                <tr>
                                    <th scope="col">Vehicle</th>
                                    <th scope="col">Driver</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Purpose</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">In/Out Time</th>
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
                <h4 class="modal-title"><i class="ti-export"></i> Record Vehicle Exit</h4>
                <button type="button" class="close" onclick="closeExitModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exitForm">
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="exitVehicleId">
                    <input type="hidden" name="method_type" id="actionMethodType" value="entry_first">
                    <div class="alert alert-info">
                        <i class="ti-info-alt"></i>
                        Recording exit for vehicle: <strong id="exitVehicleNumber"></strong>
                    </div>

                    <div class="card border mb-3" id="exitVehicleDetails" style="display: none;">
                        <div class="card-body py-2">
                            <h6 class="card-title mb-2"><i class="ti-car"></i> Vehicle Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Driver:</strong> <span id="exitDriverName">-</span></p>
                                    <p class="mb-1"><strong>Contact:</strong> <span id="exitDriverContact">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Purpose:</strong> <span id="exitVisitPurpose">-</span></p>
                                    <p class="mb-1"><strong>Location:</strong> <span id="exitLocation">-</span></p>
                                </div>
                            </div>
                            <div id="exitAdditionalPersons" style="display: none;">
                                <hr class="my-2">
                                <p class="mb-1"><strong><i class="ti-user"></i> Additional Persons:</strong></p>
                                <div id="exitAdditionalPersonsList" class="text-muted"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <span class="form-label">Date</span>
                            <input aria-label="Exit Date" type="date" class="form-control" name="exit_date"
                                value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Time</span>
                            <input aria-label="Exit Time" type="time" class="form-control" name="exit_time" value="<?php echo date('H:i'); ?>"
                                readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="form-label">Notes (Optional)</span>
                        <textarea aria-label="Any observations or notes..." class="form-control" name="exit_notes" rows="3"
                            placeholder="Any observations or notes..."></textarea>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <div class="checkbox-fade fade-in-primary">
                                <label>
                                    <input aria-label="Goods Checked" type="checkbox" name="goods_checked" value="1" id="goodsChecked">
                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                    <span>Goods/materials verified</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <div class="checkbox-fade fade-in-primary">
                                <label>
                                    <input aria-label="Security Check" type="checkbox" name="security_check" value="1" id="securityCheck">
                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                    <span>Security check completed</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6" >
                                <span class="form-label">Vehicle Reading <span class="text-danger">*</span></span>
                            <div>
                                <input aria-label="12345" type="number" class="form-control text-right" name="meater_reading"
                                    id="meater_reading" step="0.01" min="0" placeholder="12345" required value="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="form-label">Authorized By</span>
                        <input aria-label="Name of person authorizing" type="text" class="form-control" name="authorized_by"
                            placeholder="Name of person authorizing"
                            value="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : ''; ?>">
                    </div>
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
                <h4 class="modal-title"><i class="ti-printer"></i> Visitor Gate Pass</h4>
                <button type="button" class="close close-print-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
<script src="assets/js/custom_insert_transport_gateway.js"></script>
