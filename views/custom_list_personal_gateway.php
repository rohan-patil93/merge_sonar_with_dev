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
$work_admin_type=1;
if (
    $_SESSION['working_role_short_code'] == 'EMPLOYEE' &&
    $_SESSION['employee']['department_id'] != 4 &&
    $_SESSION['employee']['designation_id'] != 59 &&
    $_SESSION['employee']['designation_id'] != 75
){
    $work_admin_type=2;
}
$url = API . "?mode=getpersonalgatewaylist&method=get&company_id=" . $_SESSION['company_id'] . "&work_admin_type=".$work_admin_type."&employee_id=".$_SESSION['userid'];
$url .= "&from_date=" . urlencode($from_date);
$url .= "&to_date=" . urlencode($to_date);
$response = callAPI($url);
$data = json_decode($response, true) ?: [];
$entries = $data['entries'] ?? [];

function formatPersonStatus($entry) {
    $method_type = $entry['method_type'] ?? 'entry_first';
    $entry_time = $entry['entry_time'] ?? null;
    $exit_time = $entry['exit_time'] ?? null;

    if ($entry_time === '00:00:00') { $entry_time = null;
}
    if ($exit_time === '00:00:00') { $exit_time = null;
}

    if ($method_type == 'exit_first') {
        return (!empty($entry_time) && !empty($exit_time)) ? 'returned' : 'outside';
    }
    return !empty($exit_time) ? 'exited' : 'inside';
}

function formatPersonDuration($entry) {
    $method_type = $entry['method_type'] ?? 'entry_first';

    if ($method_type == 'exit_first') {
        if (!empty($entry['entry_time']) && !empty($entry['exit_time'])) {
            $exitTime = strtotime($entry['entry_date'] . ' ' . $entry['exit_time']);
            $entryTime = strtotime($entry['entry_date'] . ' ' . $entry['entry_time']);
            $duration = round(($entryTime - $exitTime) / 60);
        } elseif (!empty($entry['exit_time'])) {
            $outTime = strtotime($entry['entry_date'] . ' ' . $entry['exit_time']);
            $duration = round((time() - $outTime) / 60);
        } else {
            $duration = 0;
        }
    } else {
        if (!empty($entry['exit_time'])) {
            $inTime = strtotime($entry['entry_date'] . ' ' . $entry['entry_time']);
            $outTime = strtotime($entry['entry_date'] . ' ' . $entry['exit_time']);
            $duration = round(($outTime - $inTime) / 60);
        } else {
            $inTime = strtotime($entry['entry_date'] . ' ' . $entry['entry_time']);
            $duration = round((time() - $inTime) / 60);
        }
    }

    if ($duration < 0) { $duration = abs($duration);
}
    return $duration >= 60 ? floor($duration/60) . 'h ' . ($duration%60) . 'm' : $duration . 'm';
}

$visitPurposes = [
    'official' => 'Official Work',
    'meeting' => 'Meeting',
    'delivery' => 'Delivery',
    'pickup' => 'Pickup',
    'personal' => 'Personal',
    'other' => 'Other'
];
?>

<div class="d-none personal-gateway-list-php-data"
    data-company-name="<?php echo isset($_SESSION['config']['name']) ? htmlspecialchars($_SESSION['config']['name']) : 'Company Name'; ?>"
    data-company-logo="<?php echo isset($_SESSION['config']['display_logo']) && !empty($_SESSION['config']['display_logo']) ? 'uploads/all/' . htmlspecialchars($_SESSION['config']['display_logo']) : ''; ?>">
</div>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><i class="ti-user"></i> Personal Gateway Records</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_INSERT_PERSONAL_GATEWAY" class="btn btn-success mb-2 desktop-view">
                    <i class="icofont icofont-plus icon-white "></i>
                    Add New Entry
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php include_once 'date_filter_common.php'; ?>
            <hr>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-hover table-bordered personal-gateway-table">
                    <thead>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">#</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Email</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Gateway Type</th>
                            <th scope="col">Vehicle</th>
                            <th scope="col">Location</th>
                            <th scope="col">Method</th>
                            <th scope="col">Date</th>
                            <th scope="col">In Time</th>
                            <th scope="col">Exit Time</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        if (!empty($entries)) {
                            foreach ($entries as $entry) {
                                $duration = formatPersonDuration($entry);
                                $method_type = $entry['method_type'] ?? 'entry_first';

                                $in_time = $entry['entry_time'] ?? null;
                                $out_time = $entry['exit_time'] ?? null;
                                $record_date = $entry['entry_date'] ?? null;

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
                                    <td><strong><?= htmlspecialchars($entry['employee_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($entry['employee_contact'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($entry['employee_email'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($visitPurposes[$entry['visit_purpose']] ?? ucfirst($entry['visit_purpose'])); ?></td>
                                    <td> <?php
                                    if($entry['gateway_type'] == 1){
                                        echo "ONE WAY";
                                    }else{
                                        echo "RETRUN";
                                    }
                                    ?> </td>
                                    <td><?= htmlspecialchars($entry['vehicle_number'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($entry['location'] ?? '-'); ?></td>
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
                                        <a href="index.php?option=COM_INSERT_PERSONAL_GATEWAY&Rid=<?= deocdeId('id:' . $entry['id']); ?>"
                                            class="btn btn-sm btn-primary btn-round" title="Edit">
                                            <i class="ti-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info btn-round print-slip-btn"
                                            data-entry-id="<?= $entry['id']; ?>" title="Print Slip">
                                            <i class="ti-printer"></i>
                                        </button>
                                        <?php if (!$is_complete): ?>
                                            <button type="button" class="btn btn-sm btn-<?= $action_btn_class; ?> btn-round record-action-btn"
                                                data-entry-id="<?= $entry['id']; ?>"
                                                data-employee-name="<?= htmlspecialchars($entry['employee_name']); ?>"
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
                                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
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
                    <input type="hidden" name="entry_id" id="exitEntryId">
                    <input type="hidden" name="method_type" id="actionMethodType">
                    <div class="alert alert-info" id="actionAlertInfo">
                        <i class="ti-info-alt"></i>
                        <span id="actionAlertText">Recording for:</span> <strong id="exitEmployeeName"></strong>
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
<script src="assets/js/custom_list_personal_gateway.js"></script>
