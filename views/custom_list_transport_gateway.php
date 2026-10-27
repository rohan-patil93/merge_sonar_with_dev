<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['datepicker'] = 1;

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date = date('Y-m-t');
}

$from_date = date('2025-01-01');
    $to_date = date('2025-12-31');

$url = API . "?mode=getvehiclegatewaylist&method=get&company_id=" . $_SESSION['company_id'];
$url .= "&from_date=" . urlencode($from_date);
$url .= "&to_date=" . urlencode($to_date);

$response = callAPI($url);
$data = json_decode($response, true) ?: [];
$vehicles = $data['vehicles'] ?? [];

function formatVehicleStatus($vehicle) {
    $method_type = $vehicle['method_type'] ?? 'entry_first';
    $entry_time = $vehicle['entry_time'] ?? null;
    $exit_time = $vehicle['exit_time'] ?? null;

    if ($entry_time === '00:00:00') { $entry_time = null;
}
    if ($exit_time === '00:00:00') { $exit_time = null;
}

    if ($method_type == 'exit_first') {
        return (!empty($entry_time) && !empty($exit_time)) ? 'returned' : 'outside';
    }
    return !empty($exit_time) ? 'exited' : 'inside';
}

function formatVehicleDuration($vehicle) {
    $method_type = $vehicle['method_type'] ?? 'entry_first';

    if ($method_type == 'exit_first') {
        if (!empty($vehicle['entry_time']) && !empty($vehicle['exit_time'])) {
            $exitTime = strtotime($vehicle['exit_date'] . ' ' . $vehicle['exit_time']);
            $entryTime = strtotime($vehicle['entry_date'] . ' ' . $vehicle['entry_time']);
            $duration = round(($entryTime - $exitTime) / 60);
        } elseif (!empty($vehicle['exit_timestamp'])) {
            $duration = round((time() - strtotime($vehicle['exit_timestamp'])) / 60);
        } else {
            $duration = 0;
        }
    } else {
        if (!empty($vehicle['exit_timestamp'])) {
            $duration = round((strtotime($vehicle['exit_timestamp']) - strtotime($vehicle['entry_timestamp'])) / 60);
        } else {
            $duration = round((time() - strtotime($vehicle['entry_timestamp'])) / 60);
        }
    }

    if ($duration < 0) { $duration = abs($duration);
}
    return $duration >= 60 ? floor($duration/60) . 'h ' . ($duration%60) . 'm' : $duration . 'm';
}

$vehicleTypes = [
    'car' => 'Car',
    'truck' => 'Truck',
    'van' => 'Van',
    'motorcycle' => 'Motorcycle',
    'bus' => 'Bus',
    'trailer' => 'Trailer',
    'other' => 'Other'
];
?>

<div class="d-none transport-gateway-list-php-data"
    data-company-name="<?php echo isset($_SESSION['config']['name']) ? htmlspecialchars($_SESSION['config']['name']) : 'Company Name'; ?>"
    data-company-logo="<?php echo isset($_SESSION['config']['display_logo']) && !empty($_SESSION['config']['display_logo']) ? 'uploads/all/' . htmlspecialchars($_SESSION['config']['display_logo']) : ''; ?>">
</div>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><i class="ti-car"></i> Vehicle Gateway Records</h5>
            <div class="card-header-right">
                <a href="index.php?option=custom_insert_transport_gateway" class="btn btn-primary waves-effect waves-light">
                    <i class="ti-plus text-white"></i> Add New Entry
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="">
                <div class="row col-lg-12 align-items-end">
                    <?php
                    $shortcode = $_POST['shortcode_date'] ?? "TM";
                    $pick_date = $_POST['pick_date'] ?? "This Month";
                    $from_date_display = $_POST['from'] ?? fromDate(date("Y-m-01"));
                    $to_date_display = $_POST['to'] ?? fromDate(date("Y-m-t"));
                    ?>
                    <div class="col-lg-4">
                        <input type="hidden" name="pick_date" id="pick_date" value="This Month">
                        <input type="hidden" name="shortcode_date" id="shortcode_date" value="TM">
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                                <i class="ti-calendar"></i>
                                <span id="btn_date"><?php echo $pick_date; ?></span>
                            </button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                                <span class="visually-hidden">Toggle primary</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                                    id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                                    id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('sunday this week'))); ?>')">This Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                                    id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                                    id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                                    id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                                    id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous Month</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                                    id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                            </div>
                        </div>
                        <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
                            <div class="col-lg-6">
                                <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date" value="<?php echo $from_date_display; ?>">
                            </div>
                            <div class="col-lg-6">
                                <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date" value="<?php echo $to_date_display; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" name="applyform" value="apply" class="btn btn-info p-2">Search</button>
                    </div>
                </div>
            </form>
            <hr>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-hover table-bordered vehicle-gateway-table">
                    <thead>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">#</th>
                            <th scope="col">Vehicle Number</th>
                            <th scope="col">Vehicle Type</th>
                            <th scope="col">Driver Name</th>
                            <th scope="col">Driver Contact</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Location</th>
                            <th scope="col">Method</th>
                            <th scope="col">Date</th>
                            <th scope="col">In Time</th>
                            <th scope="col">Out Time</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        if (!empty($vehicles)) {
                            foreach ($vehicles as $vehicle) {
                                $duration = formatVehicleDuration($vehicle);
                                $method_type = $vehicle['method_type'] ?? 'entry_first';

                                $in_time = $vehicle['entry_time'] ?? null;
                                $out_time = $vehicle['exit_time'] ?? null;
                                $record_date = $vehicle['entry_date'] ?? $vehicle['exit_date'] ?? null;

                                if ($in_time === '00:00:00') { $in_time = null;
}
                                if ($out_time === '00:00:00') { $out_time = null;
}

                                if ($method_type == 'exit_first') {
                                    $is_complete = !empty($in_time) && !empty($out_time);
                                    $status_bg = $is_complete ? '#6c757d' : '#dc3545';
                                    $status_icon = $is_complete ? 'ti-check' : 'ti-export';
                                    $status_text = $is_complete ? 'Completed' : 'Outside';
                                    $action_text = 'Record In';
                                    $action_icon = 'ti-import';
                                    $action_btn_class = 'success';
                                } else {
                                    $is_complete = !empty($out_time);
                                    $status_bg = $is_complete ? '#6c757d' : '#28a745';
                                    $status_icon = $is_complete ? 'ti-check' : 'ti-import';
                                    $status_text = $is_complete ? 'Completed' : 'Inside';
                                    $action_text = 'Record Out';
                                    $action_icon = 'ti-export';
                                    $action_btn_class = 'danger';
                                }
                                $status_color = '#fff';
                                ?>
                                <tr>
                                    <td><?= $sl++; ?></td>
                                    <td><strong><?= htmlspecialchars($vehicle['vehicle_number']); ?></strong></td>
                                    <td><?= htmlspecialchars($vehicleTypes[$vehicle['vehicle_type']] ?? ucfirst($vehicle['vehicle_type'])); ?></td>
                                    <td><?= htmlspecialchars($vehicle['driver_name']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['driver_contact'] ?? '-'); ?></td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($vehicle['visit_purpose'])); ?>
                                        <?php if (!empty($vehicle['visiting_person'])): ?>
                                            <br><small class="text-muted">To: <?= htmlspecialchars($vehicle['visiting_person']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $location = $vehicle['location'];
                                         if($vehicle['location'] == '99999'){
                                            $location = $vehicle['other_location'];
                                        } ?>
                                        <?= htmlspecialchars($vehicle['location'] ?? '-'); ?></td>
                                    <td>
                                        <?php if ($method_type == 'exit_first'): ?>
                                            <span class="badge badge-danger" style="padding:4px 8px;">
                                                <i class="ti-export"></i> Exit First
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success" style="padding:4px 8px;">
                                                <i class="ti-import"></i> Entry First
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($record_date) ? date('d-M-Y', strtotime($record_date)) : '-'; ?></td>
                                    <td><?= !empty($in_time) ? date('g:i A', strtotime($in_time)) : '<span class="text-muted">Pending</span>'; ?></td>
                                    <td><?= !empty($out_time) ? date('g:i A', strtotime($out_time)) : '<span class="text-muted">Pending</span>'; ?></td>
                                    <td><?= $duration; ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?= $status_bg; ?>; color: <?= $status_color; ?>; padding:6px 10px; border-radius:6px;">
                                            <i class="<?= $status_icon; ?>"></i> <?= $status_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-round print-slip-btn"
                                            data-vehicle-id="<?= $vehicle['id']; ?>" title="Print Slip">
                                            <i class="ti-printer"></i>
                                        </button>
                                        <?php if (!$is_complete): ?>
                                            <button type="button" class="btn btn-sm btn-<?= $action_btn_class; ?> btn-round record-action-btn"
                                                data-vehicle-id="<?= $vehicle['id']; ?>"
                                                data-vehicle-number="<?= htmlspecialchars($vehicle['vehicle_number']); ?>"
                                                data-method-type="<?= $method_type; ?>"
                                                title="<?= $action_text; ?>">
                                                <i class="<?= $action_icon; ?>"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                          ?>
                          <tr>
                            <td colspan="14">
                              <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                  <i class="ti-alert text-warning"></i> No Data Available
                                </p>
                                <div class="text-center px-4">
                                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                    style="max-height: 300px!important;">
                                </div>
                              </div>
                            </td>
                          </tr>
                          <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="printSlipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-printer"></i> Vehicle Gate Pass</h4>
                <button type="button" class="close close-print-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="printSlipContent">
                <!-- Print content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-print-modal-btn">Close</button>
                <button type="button" class="btn btn-primary print-slip-btn-action">
                    <i class="ti-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Exit/Return Modal -->
<div class="modal fade" id="exitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="actionModalTitle"><i class="ti-export"></i> Record Out Time</h4>
                <button type="button" class="close close-exit-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exitForm">
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="exitVehicleId">
                    <input type="hidden" name="method_type" id="actionMethodType">
                    <div class="alert alert-info" id="actionAlertInfo">
                        <i class="ti-info-alt"></i>
                        <span id="actionAlertText">Recording for vehicle:</span> <strong id="exitVehicleNumber"></strong>
                    </div>
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <span class="form-label">Date</span>
                            <input aria-label="Exit Date" type="date" class="form-control" name="exit_date" id="actionDate" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Time</span>
                            <input aria-label="Exit Time" type="time" class="form-control" name="exit_time" id="actionTime" value="<?php echo date('H:i'); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="form-label">Notes (Optional)</span>
                        <textarea aria-label="Any observations or notes..." class="form-control" name="exit_notes" rows="3" placeholder="Any observations or notes..."></textarea>
                    </div>
                    <div class="form-group">
                        <span class="form-label">Authorized By</span>
                        <input aria-label="Name of person authorizing" type="text" class="form-control" name="authorized_by" placeholder="Name of person authorizing" value="<?php echo $_SESSION['display_name']; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect close-exit-modal-btn">Cancel</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light" id="actionSubmitBtn">
                        <i class="ti-check"></i> <span id="actionSubmitText">Record Out</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include external JavaScript -->
<script src="assets/js/custom_list_transport_gateway.js"></script>
