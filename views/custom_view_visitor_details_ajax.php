<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
chdir(dirname(__DIR__));
include_once 'includes/session.php';
include_once 'dbconfig.php';
include_once 'comm.php';

$log_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($log_id <= 0) {
    echo '<div class="alert alert-danger font-weight-bold"><i class="fa fa-exclamation-triangle"></i> Invalid visitor log ID.</div>';
    exit;
}

$url = API . "?mode=getVisitorDetails&id=" . $log_id;
$che = callAPI($url);
$data = json_decode($che, 1);

if (!$data || (isset($data['status']) && $data['status'] === 'error')) {
    echo '<div class="alert alert-warning font-weight-bold"><i class="fa fa-exclamation-triangle"></i> ' . htmlspecialchars($data['message'] ?? 'Visitor log record not found.') . '</div>';
    exit;
}

$log = (object) $data['log'];
$host_name = $data['host_name'];
$dept_name = $data['dept_name'];
$vendor_name = $data['vendor_name'];
$in_gate_name = $data['in_gate_name'];
$out_gate_name = $data['out_gate_name'];
$in_guard_name = $data['in_guard_name'];
$out_guard_name = $data['out_guard_name'];
$luggages = [];
if (!empty($data['luggages'])) {
    foreach ($data['luggages'] as $lug) {
        $luggages[] = (object) $lug;
    }
}
$uom_map = $data['uom_map'] ?? [];

// Formatting date times
$in_time = !empty($log->in_datetime) && $log->in_datetime !== '0000-00-00 00:00:00' ? date('d-M-Y h:i A', strtotime($log->in_datetime)) : '—';
$out_time = !empty($log->out_datetime) && $log->out_datetime !== '0000-00-00 00:00:00' ? date('d-M-Y h:i A', strtotime($log->out_datetime)) : '—';

// Visit Type Badge color
$visit_type = strtolower($log->visit_type ?? 'visitor');
switch ($visit_type) {
    case 'visitor':
        $badge_cls = 'bg-primary';
        $type_label = 'Visitor';
        break;
    case 'delivery':
        $badge_cls = 'bg-warning text-dark';
        $type_label = 'Delivery';
        break;
    case 'service':
        $badge_cls = 'bg-success';
        $type_label = 'Service / Contractor';
        break;
    case 'employee':
        $badge_cls = 'bg-info text-dark';
        $type_label = 'Employee Temp';
        break;
    case 'doctor':
        $badge_cls = 'bg-danger';
        $type_label = 'Doctor';
        break;
    case 'vendor_supplier':
        $badge_cls = 'bg-secondary';
        $type_label = 'Vendor / Supplier';
        break;
    default:
        $badge_cls = 'bg-dark';
        $type_label = ucfirst($visit_type);
        break;
}

// Determine name, phone, email based on type
$display_name = $log->name ?: ($log->emp_temp_name ?: ($log->service_person ?: ($log->vendor_supplier_driver_name ?: '—')));
$display_phone = $log->phone ?: ($log->emp_temp_phone ?: ($log->service_phone ?: ($log->vendor_supplier_driver_mobile ?: '—')));
$display_email = $log->email ?: '—';
?>
<div class="visitor-details-view">
    <!-- Header Summary Card -->
    <div class="card p-3 mb-4 border-0"
        style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff; border-radius: 12px;">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <div class="detail-avatar">
                <?php if (!empty($log->image_path)): ?>
                    <img src="uploads/visitors/<?= htmlspecialchars($log->image_path) ?>" alt="Visitor"
                        style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
                <?php else: ?>
                    <div
                        style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; border: 3px solid rgba(255,255,255,0.1);">
                        <?= htmlspecialchars(strtoupper(substr($display_name, 0, 2))) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center text-md-start flex-grow-1">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 flex-wrap">
                    <h4 class="mb-0 font-weight-bold"><?= htmlspecialchars($display_name) ?></h4>
                    <span class="badge <?= $badge_cls ?> px-2.5 py-1 text-xs font-weight-bold"
                        style="border-radius: 50px;"><?= $type_label ?></span>
                </div>
                <div class="mt-1 opacity-75 small">
                    <i class="fa fa-phone me-1"></i> <?= htmlspecialchars($display_phone) ?> &nbsp;|&nbsp;
                    <i class="fa fa-envelope me-1"></i> <?= htmlspecialchars($display_email) ?>
                </div>
                <div class="mt-1 small">
                    <span class="opacity-75">ID / Badge Code:</span> <strong
                        class="text-warning"><?= htmlspecialchars($log->visitor_idcard_no ?: '—') ?></strong>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <!-- Left Side: Basic Visit Details -->
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-light"
                style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 text-dark font-weight-bold"><i class="fa fa-info-circle text-primary me-2"></i>
                        Visit &amp; Purpose Info</h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-borderless table-sm small mb-0">
                        <tr>
                            <th scope="col" class="text-muted" style="width: 40%;">Host Employee:</th>
                            <th scope="col" class="font-weight-bold text-dark"><?= htmlspecialchars($host_name) ?></th>
                        </tr>
                        <?php if ($visit_type === 'vendor_supplier'): ?>
                            <tr>
                                <td class="text-muted">Vendor Company:</td>
                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($vendor_name) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Department:</td>
                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($dept_name) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Purpose of Visit:</td>
                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($log->purpose ?: '—') ?></td>
                        </tr>
                        <?php if (!empty($log->purpose_other)): ?>
                            <tr>
                                <td class="text-muted">Purpose Details:</td>
                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($log->purpose_other) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Visiting Company:</td>
                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($log->company_name ?: '—') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Address:</td>
                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($log->address ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Gender:</td>
                            <td class="font-weight-bold text-dark"><?= htmlspecialchars(ucfirst($log->gender ?? '—')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Expected Duration:</td>
                            <td class="font-weight-bold text-dark">
                                <?= htmlspecialchars($log->expected_duration ?: '—') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Check-in / Out & Guard Details -->
        <div class="col-md-6 mb-3">
            <div class="card h-100 border-light"
                style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 text-dark font-weight-bold"><i class="fa fa-sign-in-alt text-success me-2"></i> Gate
                        Entry &amp; Exit History</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-3">
                        <!-- Check In Block -->
                        <div class="p-2.5 rounded" style="background-color: #f0fdf4; border-left: 4px solid #10b981;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-xs font-weight-bold text-success text-uppercase">Check-In
                                    Details</span>
                                <i class="fa fa-check-circle text-success"></i>
                            </div>
                            <div class="mt-1 small">
                                <div><strong>Time:</strong> <span class="text-dark"><?= $in_time ?></span></div>
                                <div><strong>Gate:</strong> <span
                                        class="text-dark"><?= htmlspecialchars($in_gate_name) ?></span></div>
                                <div><strong>Guard:</strong> <span
                                        class="text-dark"><?= htmlspecialchars($in_guard_name) ?></span></div>
                            </div>
                        </div>

                        <!-- Check Out Block -->
                        <?php if (!empty($log->out_datetime) && $log->out_datetime !== '0000-00-00 00:00:00'): ?>
                            <div class="p-2.5 rounded" style="background-color: #fef2f2; border-left: 4px solid #ef4444;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-xs font-weight-bold text-danger text-uppercase">Check-Out
                                        Details</span>
                                    <i class="fa fa-sign-out-alt text-danger"></i>
                                </div>
                                <div class="mt-1 small">
                                    <div><strong>Time:</strong> <span class="text-dark"><?= $out_time ?></span></div>
                                    <div><strong>Gate:</strong> <span
                                            class="text-dark"><?= htmlspecialchars($out_gate_name) ?></span></div>
                                    <div><strong>Guard:</strong> <span
                                            class="text-dark"><?= htmlspecialchars($out_guard_name) ?></span></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-2.5 rounded text-center"
                                style="background-color: #fffbeb; border: 1px dashed #f59e0b;">
                                <div class="text-warning mb-1"><i class="fa fa-clock fa-spin"></i></div>
                                <span class="text-xs font-weight-bold text-warning text-uppercase">Still Inside
                                    Premises</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle & Cargo Parameters -->
    <div class="card mb-4 border-light"
        style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 text-dark font-weight-bold"><i class="fa fa-truck text-info me-2"></i> Vehicle &amp;
                Security Compliance</h6>
        </div>
        <div class="card-body pt-0">
            <div class="row text-center small">
                <div class="col-6 col-md-3 mb-2">
                    <div class="p-2 bg-light rounded h-100">
                        <div class="text-muted text-xs">Vehicle Type</div>
                        <strong
                            class="text-dark"><?= htmlspecialchars($log->vehicle_type ?: ($log->vendor_supplier_vehicle_type ?: 'None')) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="p-2 bg-light rounded h-100">
                        <div class="text-muted text-xs">Vehicle Number</div>
                        <strong
                            class="text-dark"><?= htmlspecialchars($log->vehicle_number ?: ($log->vendor_supplier_vehicle_number ?: '—')) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="p-2 bg-light rounded h-100">
                        <div class="text-muted text-xs">Package Count</div>
                        <strong class="text-dark"><?= (int) ($log->package_count ?? 1) ?></strong>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="p-2 bg-light rounded h-100">
                        <div class="text-muted text-xs font-weight-bold">Security Cleared</div>
                        <div>
                            <?php if (($log->security_cleared ?? 0) == 1): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($visit_type === 'service'): ?>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="p-2 bg-light rounded h-100">
                            <div class="text-muted text-xs">Work Permit No</div>
                            <strong class="text-dark"><?= htmlspecialchars($log->work_permit_no ?: '—') ?></strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="p-2 bg-light rounded h-100">
                            <div class="text-muted text-xs font-weight-bold">Safety Briefing</div>
                            <div>
                                <?php if (($log->safety_briefing ?? 0) == 1): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($visit_type === 'vendor_supplier'): ?>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="p-2 bg-light rounded h-100">
                            <div class="text-muted text-xs">Vehicle Loading Window</div>
                            <strong class="text-dark" style="font-size: 11px;">
                                <?= $log->vendor_supplier_loading_start ? date('d/M h:i A', strtotime($log->vendor_supplier_loading_start)) : '—' ?>
                                to
                                <?= $log->vendor_supplier_loading_end ? date('d/M h:i A', strtotime($log->vendor_supplier_loading_end)) : '—' ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="p-2 bg-light rounded h-100">
                            <div class="text-muted text-xs">Vehicle Status</div>
                            <strong
                                class="text-dark"><?= htmlspecialchars(str_replace('_', ' ', $log->vendor_supplier_vehicle_status ?? '—')) ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Luggages & Inward Materials Section -->
    <div class="card border-light"
        style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 text-dark font-weight-bold"><i class="fa fa-boxes text-warning me-2"></i> Accompanying
                Luggages / Material Items</h6>
        </div>
        <div class="card-body pt-0">
            <?php if ($luggages && !empty($luggages)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm small mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">Item / Material Name</th>
                                <th scope="col">Description / Serial</th>
                                <th scope="col" class="text-right">Qty</th>
                                <th scope="col">Brand / UOM</th>
                                <th scope="col" class="text-center">Returnable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($luggages as $lug): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($lug->item_name ?: '—') ?></strong></td>
                                    <td><?= htmlspecialchars($lug->serial_or_remarks ?: '—') ?></td>
                                    <td class="text-right"><strong><?= (float) $lug->qty ?></strong></td>
                                    <td>
                                        <?php
                                        if ($visit_type === 'vendor_supplier' && isset($uom_map[$lug->brand_name])) {
                                            echo htmlspecialchars($uom_map[$lug->brand_name]);
                                        } else {
                                            echo htmlspecialchars($lug->brand_name ?: '—');
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($lug->returnable ?? 0) == 1): ?>
                                            <span class="badge bg-light-warning text-warning">Yes
                                                (<?= !empty($lug->return_date) ? date('d-M-Y', strtotime($lug->return_date)) : '—' ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-light-secondary text-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-3 text-muted small">
                    <i class="fa fa-box-open me-1"></i> No luggage or material items logged for this visit.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
