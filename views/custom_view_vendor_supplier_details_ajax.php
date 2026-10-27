<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

chdir(dirname(__DIR__));
include_once 'includes/session.php';
include_once 'dbconfig.php';
include_once 'comm.php';

$vendor_id = isset($_GET['vendor_id']) ? (int)$_GET['vendor_id'] : 0;
if ($vendor_id <= 0) {
    echo '<div class="alert alert-danger font-weight-bold"><i class="fa fa-exclamation-triangle"></i> Invalid or empty Vendor / Supplier ID.</div>';
    exit;
}

$url = API . "?mode=getVisitorProfileDetails&vendor_id=" . $vendor_id . "&company_id=" . $_SESSION['company_id'];
$res_json = callAPI($url);
$data = json_decode($res_json, true);

if (!$data || (isset($data['status']) && $data['status'] === 'error')) {
    echo '<div class="alert alert-warning font-weight-bold"><i class="fa fa-exclamation-triangle"></i> ' . htmlspecialchars($data['message'] ?? 'Visitor profile log record not found.') . '</div>';
    exit;
}

$all_logs = $data['logs'] ?? [];
$logs = array_values(array_filter($all_logs, function($l) {
    return strtolower($l['visit_type'] ?? '') === 'vendor_supplier';
}));

if (empty($logs)) {
    echo '<div class="alert alert-info font-weight-bold"><i class="fa fa-info-circle"></i> No vendor or supplier logs found for this phone number.</div>';
    exit;
}

$uom_map = $data['uom_map'] ?? [];

$overall_materials = [];
foreach ($logs as $log) {
    if (!empty($log['luggages'])) {
        $has_checked_out = !empty($log['out_datetime']) && $log['out_datetime'] !== '0000-00-00 00:00:00';
        foreach ($log['luggages'] as $lug) {
            $itemName = trim($lug['item_name']);
            $brandOrUom = trim($lug['brand_name']);
            $key = strtolower($itemName . '||' . $brandOrUom);

            if (!isset($overall_materials[$key])) {
                $overall_materials[$key] = [
                    'item_name' => $itemName,
                    'brand_name' => $brandOrUom,
                    'qty_in' => 0,
                    'qty_out' => 0
                ];
            }

            $qty = (float)$lug['qty'];
            if (strtoupper($lug['visitor_type']) === 'IN') {
                $overall_materials[$key]['qty_in'] += $qty;

                if ($has_checked_out) {
                    $remarks = $lug['return_remarks'] ?? '';
                    if (preg_match('/Left Qty:\s*([0-9.]+)/i', $remarks, $matches)) {
                        $left_qty = (float)$matches[1];
                        $taken_out = max(0.0, $qty - $left_qty);
                        $overall_materials[$key]['qty_out'] += $taken_out;
                    } else {
                        $overall_materials[$key]['qty_out'] += $qty;
                    }
                }
            } elseif (strtoupper($lug['visitor_type']) === 'OUT') {
                $overall_materials[$key]['qty_out'] += $qty;
            }
        }
    }
}
$overall_materials = array_values($overall_materials);

$latest = $logs[0];
$vendor_display_name = $latest['vendor_name'] ?: ($latest['company_name'] ?: '—');
$display_name = $latest['name'] ?: ($latest['vendor_supplier_driver_name'] ?: '—');
$display_phone = $latest['phone'] ?: ($latest['vendor_supplier_driver_mobile'] ?: '—');
$display_email = $latest['email'] ?: '—';

$overall_in_qty = 0;
$overall_out_qty = 0;
$has_variance = false;

foreach ($overall_materials as $mat) {
    $overall_in_qty += (float)$mat['qty_in'];
    $overall_out_qty += (float)$mat['qty_out'];
    if (abs($mat['qty_in'] - $mat['qty_out']) > 0.001) {
        $has_variance = true;
    }
}
?>

<div class="profile-details-view">
    <!-- Header Block -->
    <div class="card p-3 mb-4 border-0" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; border-radius: 12px;">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <div class="detail-avatar">
                <?php if (!empty($latest['image_path'])): ?>
                    <img src="uploads/visitors/<?= htmlspecialchars($latest['image_path']) ?>" alt="Driver" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; border: 3px solid rgba(255,255,255,0.1); color: #38bdf8;">
                        <?= htmlspecialchars(strtoupper(substr($vendor_display_name, 0, 2))) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center text-md-start flex-grow-1">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 flex-wrap">
                    <h4 class="mb-0 font-weight-bold" style="color:#ffffff;"><?= htmlspecialchars($vendor_display_name) ?></h4>
                    <span class="badge bg-secondary px-2.5 py-1 text-xs font-weight-bold" style="border-radius: 50px; background-color: #64748b !important;">Vendor / Supplier</span>
                    <span class="badge bg-info text-dark px-2.5 py-1 text-xs font-weight-bold" style="border-radius: 50px; background-color: #38bdf8 !important;">ID: <?= htmlspecialchars($latest['vendor_supplier_vendor_id'] ?: '—') ?></span>
                </div>
                <div class="mt-2 opacity-75 small">
                    <i class="fa fa-user me-1 text-info"></i> Latest Driver: <strong><?= htmlspecialchars($display_name) ?></strong> &nbsp;|&nbsp;
                    <i class="fa fa-phone me-1 text-info"></i> <?= htmlspecialchars($display_phone) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-box" style="background-color: #f8fafc; border-color: #e2e8f0;">
                <h4 class="text-slate" style="color: #475569;"><?= count($logs) ?></h4>
                <p>Total Entries</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-box" style="background-color: #eff6ff; border-color: #bfdbfe;">
                <h4 class="text-primary" style="color: #2563eb;"><?= $overall_in_qty ?></h4>
                <p>Brought IN (Qty)</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="stat-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                <h4 class="text-success" style="color: #16a34a;"><?= $overall_out_qty ?></h4>
                <p>Taken OUT (Qty)</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <?php if ($has_variance): ?>
                <div class="stat-box" style="background-color: #fff1f2; border-color: #fecdd3;">
                    <h4 class="text-danger" style="color: #e11d48;"><i class="fa fa-exclamation-circle animate-pulse"></i> Pending</h4>
                    <p>Reconciliation Status</p>
                </div>
            <?php else: ?>
                <div class="stat-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <h4 class="text-success" style="color: #16a34a;"><i class="fa fa-check-circle"></i> Reconciled</h4>
                    <p>Reconciliation Status</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <?php if (!empty($overall_materials)): ?>
        <div class="card mb-4 border-light" style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); padding: 18px;">
            <div class="details-grid-title"><i class="fa fa-balance-scale text-warning me-2"></i> Materials Reconciliation Ledger</div>
            <div class="table-responsive">
                <table class="table table-hover table-sm small mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">Item / Material Name</th>
                            <th scope="col">Brand / UOM</th>
                            <th scope="col" class="text-end" style="width: 150px; text-align: right;">Qty Brought IN</th>
                            <th scope="col" class="text-end" style="width: 150px; text-align: right;">Qty Taken OUT</th>
                            <th scope="col" class="text-center" style="width: 150px;">Variance (Balance)</th>
                            <th scope="col" class="text-center" style="width: 150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overall_materials as $mat):
                            $diff = $mat['qty_in'] - $mat['qty_out'];
                            if (abs($diff) < 0.001) {
                                $diff_class = 'qty-diff-ok';
                                $status_badge = '<span class="badge bg-success-light text-success" style="font-weight: 700; border-radius: 5px; padding: 5px 10px;">Fully Reconciled</span>';
                            } elseif ($diff > 0) {
                                $diff_class = 'qty-diff-warning';
                                $status_badge = '<span class="badge bg-warning-light text-warning" style="font-weight: 700; border-radius: 5px; padding: 5px 10px;">Pending Exit</span>';
                            } else {
                                $diff_class = 'qty-diff-warning';
                                $status_badge = '<span class="badge bg-danger-light text-danger" style="font-weight: 700; border-radius: 5px; padding: 5px 10px;">Overdrawn</span>';
                            }

                            // Map UOM name
                            $uomBrand = $mat['brand_name'];
                            if (isset($uom_map[$mat['brand_name']])) {
                                $uomBrand = $uom_map[$mat['brand_name']];
                            }
                        ?>
                            <tr>
                                <td><strong class="text-dark"><?= htmlspecialchars($mat['item_name']) ?></strong></td>
                                <td><?= htmlspecialchars($uomBrand ?: '—') ?></td>
                                <td class="text-end" style="text-align: right;"><span class="qty-badge qty-in-badge"><?= $mat['qty_in'] ?></span></td>
                                <td class="text-end" style="text-align: right;"><span class="qty-badge qty-out-badge"><?= $mat['qty_out'] ?></span></td>
                                <td class="text-center">
                                    <span class="qty-badge <?= $diff_class ?>">
                                        <?= ($diff > 0) ? '+' . $diff : $diff ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $status_badge ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-4 border-light p-4 text-center text-muted" style="border-radius: 10px; background: #ffffff;">
            <i class="fa fa-info-circle fa-2x mb-2 text-secondary"></i>
            <p class="mb-0">No materials brought in or taken out by this driver/supplier.</p>
        </div>
    <?php endif; ?>


    <div class="card border-light" style="border-radius: 10px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); padding: 18px;">
        <div class="details-grid-title"><i class="fa fa-history text-muted me-2"></i> Historical Log of Check-ins &amp; Logistics</div>
        <div class="table-responsive">
            <table class="table table-hover table-sm small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" style="width: 50px;">No</th>
                        <th scope="col">Transaction Info</th>
                        <th scope="col">Vehicle &amp; Loading Times</th>
                        <th scope="col">Host / Department</th>
                        <th scope="col">Entry / Exit Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $index => $log):
                        $in_dt = !empty($log['in_datetime']) && $log['in_datetime'] !== '0000-00-00 00:00:00' ? date('d-M-Y h:i A', strtotime($log['in_datetime'])) : '—';
                        $out_dt = !empty($log['out_datetime']) && $log['out_datetime'] !== '0000-00-00 00:00:00' ? date('d-M-Y h:i A', strtotime($log['out_datetime'])) : '—';
                        $load_start = !empty($log['vendor_supplier_loading_start']) ? date('d-M-Y h:i A', strtotime($log['vendor_supplier_loading_start'])) : '—';
                        $load_end = !empty($log['vendor_supplier_loading_end']) ? date('d-M-Y h:i A', strtotime($log['vendor_supplier_loading_end'])) : '—';
                    ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <div><strong class="text-dark">Transaction No:</strong> <code style="background: #f1f5f9; padding: 2px 4px; border-radius: 4px; color: #0f172a; font-weight: 600;"><?= htmlspecialchars($log['vendor_supplier_transaction_no'] ?: $log['visitor_idcard_no'] ?: '—') ?></code></div>
                                <div class="text-xs text-muted mt-1"><strong>Purpose:</strong> <?= htmlspecialchars($log['vendor_supplier_purpose'] ?: $log['purpose'] ?: '—') ?></div>
                                <div class="text-xs text-muted"><strong>Gate In/Out:</strong> <?= htmlspecialchars($log['in_gate_name'] ?: '—') ?> &rarr; <?= htmlspecialchars($log['out_gate_name'] ?: '—') ?></div>
                            </td>
                            <td>
                                <div><strong class="text-secondary"><i class="fa fa-truck me-1"></i><?= htmlspecialchars($log['vendor_supplier_vehicle_number'] ?: $log['vehicle_number'] ?: '—') ?></strong> (<?= htmlspecialchars($log['vendor_supplier_vehicle_type'] ?: $log['vehicle_type'] ?: '—') ?>)</div>
                                <div class="text-xs text-muted mt-1"><strong>Load Start:</strong> <?= $load_start ?></div>
                                <div class="text-xs text-muted"><strong>Load End:</strong> <?= $load_end ?></div>
                            </td>
                            <td>
                                <div><strong class="text-dark"><?= htmlspecialchars($log['host_name']) ?></strong></div>
                                <div class="text-xs text-muted"><strong>Dept:</strong> <?= htmlspecialchars($log['department_name']) ?></div>
                                <div class="text-xs text-muted"><strong>Vendor:</strong> <?= htmlspecialchars($log['vendor_name']) ?></div>
                            </td>
                            <td>
                                <div class="text-success" style="font-weight: 600;"><i class="fa fa-sign-in-alt me-1"></i> IN: <?= $in_dt ?></div>
                                <div class="text-xs text-muted ms-3">Guard: <?= htmlspecialchars($log['in_guard_name'] ?: '—') ?></div>
                                <?php if ($out_dt !== '—'): ?>
                                    <div class="text-danger mt-1" style="font-weight: 600;"><i class="fa fa-sign-out-alt me-1"></i> OUT: <?= $out_dt ?></div>
                                    <div class="text-xs text-muted ms-3">Guard: <?= htmlspecialchars($log['out_guard_name'] ?: '—') ?></div>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark mt-1 px-2 py-0.5" style="font-size:10px; font-weight: 700;">In Premises</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
