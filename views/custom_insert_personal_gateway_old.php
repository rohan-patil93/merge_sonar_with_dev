<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';
$work_admin_type=1;
if($_SESSION['working_role_short_code']=='EMPLOYEE'){
    $work_admin_type=2;
}
$url = API . "?mode=getpersonalgatewaydetails&method=get&company_id=" . $_SESSION['company_id'];
$response = callAPI($url);
$datas = json_decode($response, true);
$locations = $datas['result'] ?? [];
if (!is_array($locations)) {
    $locations = [];
}

// Get application users list
$applicationUsers = $datas['application_users'] ?? [];
if (!is_array($applicationUsers)) {
    $applicationUsers = [];
}

// Get vehicles from transport_vehicle_master
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
    'other' => 'Other'
];
?>

<!-- Hidden div for PHP data to be used by JavaScript -->
<div class="d-none personal-gateway-php-data"
    data-company-id="<?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>"
    data-user-id="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>"
    data-company-name="<?php echo isset($_SESSION['config']['name']) ? htmlspecialchars($_SESSION['config']['name']) : 'Company Name'; ?>"
    data-company-logo="<?php echo isset($_SESSION['config']['display_logo']) && !empty($_SESSION['config']['display_logo']) ? 'uploads/all/' . htmlspecialchars($_SESSION['config']['display_logo']) : ''; ?>"
    data-display-name="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : ''; ?>">
</div>

<!-- Include external CSS -->
<link rel="stylesheet" href="assets/css/custom_insert_transport_gateway.css">
<input type="hidden" name="work_admin_type" id="work_admin_type" value="<?php echo $work_admin_type; ?>">
<div class="page-body">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary order-card">
                <div class="card-body">
                    <h6 class="text-white">Persons Inside</h6>
                    <h2 class="text-right text-white"><i class="ti-user f-left"></i> <span id="stat-current-count"> 0</span></h2>
                    <p class="m-b-0 text-white">Currently in premises</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Entries</h6>
                    <h2 class="text-right text-white"><i class="ti-import f-left"></i> <span id="stat-today-entries"> 0</span></h2>
                    <p class="m-b-0 text-white">Persons entered today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-danger order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Exits</h6>
                    <h2 class="text-right text-white"><i class="ti-export f-left"></i> <span id="stat-today-exits"> 0</span></h2>
                    <p class="m-b-0 text-white">Persons exited today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
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
        <!-- Personal Entry Form -->
        <div class="col-lg-8">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-user"></i> Personal Gateway Entry</h5>
                    <div class="card-header-right table-card-header">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="personalEntryForm">
                        <!-- Method Type Selection -->
                        <div class="form-group row">
                            <div class="form-group col-lg-12">
                                <label class="form-label">Movement Type <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="transport-gateway-radio radio-success mr-4">
                                        <input type="radio" name="method_type" id="method_entry_first" value="entry_first" checked>
                                        <label for="method_entry_first" class="mr-3">
                                            <i class="ti-import text-success"></i> Entry First (Person Coming In)
                                        </label>
                                    </div>
                                    <div class="transport-gateway-radio radio-danger">
                                        <input type="radio" name="method_type" id="method_exit_first" value="exit_first">
                                        <label for="method_exit_first">
                                            <i class="ti-export text-danger"></i> Exit First (Person Going Out)
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">Select "Exit First" when staff is going out for official work</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <label class="form-label">Employee Name <span class="text-danger">*</span></label>
                                <div>
                                    <select class="form-select js-example-basic-single" name="employee_id" id="employeeSelect" required>
                                        <option value="">Select Employee</option>
                                        <?php foreach ($applicationUsers as $user): ?>
                                            <option value="<?php echo htmlspecialchars($user['id']); ?>"
                                                data-mobile="<?php echo htmlspecialchars($user['user_mobile'] ?? ''); ?>"
                                                data-email="<?php echo htmlspecialchars($user['user_email'] ?? ''); ?>"
                                                data-name="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>">
                                                <?php echo htmlspecialchars($user['display_name'] ?? ''); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-label">Contact Number</label>
                                <div>
                                    <input type="tel" class="form-control" name="employee_contact" id="employeeContact" placeholder="Contact number" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <label class="form-label">Email</label>
                                <div>
                                    <input type="email" class="form-control" name="employee_email" id="employeeEmail" placeholder="Email address" readonly>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                <div>
                                    <select class="form-select js-example-basic-single" name="visit_purpose" id="visitPurpose" required>
                                        <option value="">Select Purpose</option>
                                        <?php foreach ($visitPurposes as $key => $purpose): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $purpose; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none-initial" id="otherPurposeRow">
                            <div class="form-group col-lg-6">
                                <label class="form-label">Specify Purpose <span class="text-danger">*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="other_visit_purpose" id="otherPurpose" placeholder="Please specify purpose">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <label class="form-label">Vehicle Number</label>
                                <div>
                                    <select class="form-select js-example-basic-single" name="vehicle_number" id="vehicleNumber">
                                        <option value="">Select Vehicle (Optional)</option>
                                        <?php foreach ($vehicles as $vehicle): ?>
                                            <option value="<?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>">
                                                <?php echo htmlspecialchars($vehicle['vehicle_reg_no']); ?>
                                                <?php if (!empty($vehicle['model'])): ?>
                                                    - <?php echo htmlspecialchars($vehicle['model']); ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <div>
                                    <select class="form-select js-example-basic-single" name="location" required>
                                        <option value="">Select Location</option>
                                        <?php if (!empty($locations) && is_array($locations)): ?>
                                            <?php foreach ($locations as $location): ?>
                                                <?php if (isset($location['id']) && isset($location['short_name'])): ?>
                                                    <option value="<?php echo htmlspecialchars($location['short_name']); ?>">
                                                        <?php echo htmlspecialchars($location['short_name']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="dateTimeRow">
                            <div class="form-group col-lg-6">
                                <label class="form-label">Date</label>
                                <div>
                                    <input type="date" class="form-control" name="entry_date" id="entryDate" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-label">Time</label>
                                <div>
                                    <input type="time" class="form-control" name="in_time" id="inTime" value="<?php echo date('H:i'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-12">
                                <label class="form-label">Additional Notes</label>
                                <div>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="Any additional information..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" id="submitBtn">
                                <i class="la la-check"></i> <span id="submitBtnText">Record Entry</span>
                            </button>
                            <button type="reset" class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-refresh"></i> Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Persons List -->
        <div class="col-lg-4">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-list"></i> Active Per Premises</h5>
                    <div class="card-header-right">
                        <span class="badge badge-primary text-white" id="persons-count-badge">0 persons</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless" id="currentPersonsTable">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center p-4">
                                        <i class="fa fa-spinner fa-spin text-muted loading-spinner-icon"></i>
                                        <p class="text-muted mt-2">Loading persons...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                        <a href="index.php?option=custom_list_personal_gateway" class="btn btn-sm btn-primary">
                            <i class="ti-eye"></i> View All Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentActivityTable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Action</th>
                                    <th>Purpose</th>
                                    <th>Vehicle</th>
                                    <th>Date</th>
                                    <th>In/Out Time</th>
                                    <th>Print</th>
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
                    <div class="alert alert-info">
                        <i class="ti-info-alt"></i>
                        Recording exit for: <strong id="exitEmployeeName"></strong>
                    </div>

                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="exit_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="form-label">Time</label>
                            <input type="time" class="form-control" name="exit_time" value="<?php echo date('H:i'); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="exit_notes" rows="3" placeholder="Any observations or notes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Authorized By</label>
                        <input type="text" class="form-control" name="authorized_by" placeholder="Name of person authorizing"
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

<!-- Print Slip Modal -->
<div class="modal fade" id="printSlipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-printer"></i> Personal Gate Pass</h4>
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
<script src="assets/js/custom_insert_personal_gateway.js"></script>
