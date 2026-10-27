<?php
date_default_timezone_set('Asia/Kolkata');

$location_id = $_GET['location_id'] ?? $_SESSION['location_id'] ?? 0;
if (($_GET['key'] ?? '') == 'AJAX_CALL_MAIN') {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}

$url = API . "?mode=getVisitorsDashboardData&location_id=" . $location_id;
$che = callAPI($url);
$data = json_decode($che, true);

$total_visitors = $data['total_visitors'] ?? 0;
$today_visitors = $data['today_visitors'] ?? 0;
$yesterday_visitors = $data['yesterday_visitors'] ?? 0;
$last_7days_visitors = $data['last_7days_visitors'] ?? 0;

$recent_in = $data['recent_in'] ?? [];
$recent_out = $data['recent_out'] ?? [];
$emp_arr = $data['emp_arr'] ?? [];

$by = $data['breakdown'] ?? [];
$typesOrder = ['visitor', 'delivery', 'service', 'employee', 'doctor', 'vendor_supplier', 'others'];
$typeIcons = [
    'visitor' => 'fa-user',
    'delivery' => 'fa-truck',
    'service' => 'fa-wrench',
    'employee' => 'fa-id-card-o',
    'doctor' => 'fa-user-md',
    'vendor_supplier' => 'fa-handshake-o',
    'others' => 'fa-ellipsis-h'
];

function render_type_tile($key, $stats, $icons, $periodKey, $location_id)
{
    $label = ($key === 'vendor_supplier') ? 'Vendor/Supplier' : ucfirst($key);
    $icon = $icons[$key] ?? 'fa-circle-o';
    $total = (int) ($stats['total'] ?? 0);
    $inOnly = (int) ($stats['in_only'] ?? 0);
    $out = (int) ($stats['out'] ?? 0);
    ob_start();
    ?>
    <div class="col-xl-custom-7 col-md-4 col-6">
        <div class="card h-100 type-stats-card">
            <div class="card-body p-2 text-center">
                <div class="type-icon-row mb-1">
                    <i class="fa <?= $icon; ?>"></i>
                    <span class="type-lbl"><?= $label; ?></span>
                </div>
                <div class="type-counts-grid">
                    <div class="type-count-item cursor-pointer openbucketmodal"
                        data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>"
                        data-bucket="total" data-location_id="<?php echo $location_id; ?>" data-bs-toggle="tooltip"
                        title="Total Entries">
                        <span class="c-val text-primary"><?= $total; ?></span>
                    </div>
                    <div class="type-count-item cursor-pointer openbucketmodal"
                        data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>"
                        data-bucket="in_only" data-location_id="<?php echo $location_id; ?>" data-bs-toggle="tooltip"
                        title="Inside Premises">
                        <span class="c-val text-danger"><?= $inOnly; ?></span>
                    </div>
                    <div class="type-count-item cursor-pointer openbucketmodal"
                        data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>"
                        data-bucket="out" data-location_id="<?php echo $location_id; ?>" data-bs-toggle="tooltip"
                        title="Exited">
                        <span class="c-val text-success"><?= $out; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function render_period_block($title, $bigNumber, $bucket, $typesOrder, $icons, $borderClass, $periodKey, $location_id)
{
    ob_start();
    ?>
    <div class="card mb-3 period-analytics-card">
        <div class="card-body p-3">
            <div class="row align-items-stretch g-3">
                <!-- Summary Column -->
                <div class="col-lg-3 summary-col-border <?= $borderClass; ?>">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fa fa-users text-muted me-2" style="font-size:15px;"></i>
                        <span class="period-title-lbl"><?= htmlspecialchars($title); ?></span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="period-big-num cursor-pointer getvisitorsdata"
                            data-period_key="<?= strtoupper($periodKey); ?>" data-location_id="<?php echo $location_id; ?>">
                            <?= (int) $bigNumber; ?>
                        </div>
                        <span class="text-muted f-11">Total Entries</span>
                    </div>

                    <!-- Small Legend -->
                    <div class="d-flex gap-2 small mt-2 f-10 text-muted">
                        <span><i class="fa fa-circle text-primary"></i> TOT</span>
                        <span><i class="fa fa-circle text-danger"></i> IN</span>
                        <span><i class="fa fa-circle text-success"></i> OUT</span>
                    </div>
                </div>

                <!-- Breakdown Tiles -->
                <div class="col-lg-9">
                    <div class="row g-2">
                        <?php foreach ($typesOrder as $t) {
                            $stats = $bucket[$t] ?? ['total' => 0, 'in_only' => 0, 'out' => 0];
                            echo render_type_tile($t, $stats, $icons, $periodKey, $location_id);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
<link rel="stylesheet" href="assets/css/custom_view_visitor_dashboard_main.css">
<style>
    .period-analytics-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        transition: all 0.2s ease-in-out;
    }

    .period-analytics-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .summary-col-border {
        border-left: 4px solid #cbd5e1;
        padding-left: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .border-danger {
        border-left-color: #ef4444 !important;
    }

    .border-primary {
        border-left-color: #3b82f6 !important;
    }

    .border-success {
        border-left-color: #10b981 !important;
    }

    .border-warning {
        border-left-color: #f59e0b !important;
    }

    .period-title-lbl {
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
    }

    .period-big-num {
        font-size: 32px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
        transition: color 0.15s;
    }

    .period-big-num:hover {
        color: #3b82f6;
    }

    .type-stats-card {
        border: 1px solid #f1f5f9;
        border-radius: 8px;
        background: #f8fafc;
        transition: all 0.15s;
    }

    .type-stats-card:hover {
        background: #fff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .type-icon-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #64748b;
    }

    .type-icon-row i {
        font-size: 13px;
    }

    .type-lbl {
        font-size: 11.5px;
        font-weight: 600;
    }

    .type-counts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        border-top: 1px solid #e2e8f0;
        margin-top: 6px;
        padding-top: 6px;
    }

    .type-count-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .type-count-item:not(:last-child) {
        border-right: 1px solid #f1f5f9;
    }

    .c-val {
        font-size: 13px;
        font-weight: 700;
    }

    /* Tab Styles */
    .dashboard-tabs {
        border-bottom: 2px solid #f1f5f9;
    }

    .dashboard-tabs .nav-link {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        border: none;
        background: none;
        padding: 10px 16px;
        position: relative;
        transition: color 0.15s;
    }

    .dashboard-tabs .nav-link:hover {
        color: #3b82f6;
    }

    .dashboard-tabs .nav-link.active {
        color: #3b82f6;
    }

    .dashboard-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3b82f6;
    }

    .recent-logs-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
    }

    .recent-logs-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .recent-logs-header h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        font-weight: 700;
    }

    .avatar-in {
        background: #fee2e2;
        color: #ef4444;
        border: 1.5px solid #fca5a5;
    }

    .avatar-out {
        background: #dcfce7;
        color: #10b981;
        border: 1.5px solid #86efac;
    }
</style>

<?php
$inside_now = 0;
if (isset($by['ALL'])) {
    foreach ($by['ALL'] as $type => $stats) {
        $inside_now += (int)($stats['in_only'] ?? 0);
    }
}

$exited_today = 0;
if (isset($by['TODAY'])) {
    foreach ($by['TODAY'] as $type => $stats) {
        $exited_today += (int)($stats['out'] ?? 0);
    }
}
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark" style="font-family: 'Outfit', sans-serif;">Visitor Analytics Dashboard</h4>
        <small class="text-muted">Real-time visitor logs and check-in metrics</small>
    </div>
    <a href="index.php?option=custom_insert_visitor_logs" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px; font-weight: 500; font-family: 'Outfit', sans-serif; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
        <i class="fa fa-plus-circle" style="font-size: 16px;"></i> VMS Check-In / Out Console
    </a>
</div>

<!-- Premium Summary Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Registered -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-summary-card stat-card-blue h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted f-12 d-block mb-1">Total Registered (All-Time)</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_visitors); ?></h3>
                </div>
                <div class="stat-card-icon bg-light-blue">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2: Active Visitors -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-summary-card stat-card-red h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted f-12 d-block mb-1">Active On-Site (Inside)</span>
                    <h3 class="fw-bold mb-0 text-danger"><?= number_format($inside_now); ?></h3>
                </div>
                <div class="stat-card-icon bg-light-red">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 3: Today's Arrivals -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-summary-card stat-card-warning h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted f-12 d-block mb-1">Today's Arrivals</span>
                    <h3 class="fw-bold mb-0 text-warning"><?= number_format($today_visitors); ?></h3>
                </div>
                <div class="stat-card-icon bg-light-warning">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 4: Exited Today -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-summary-card stat-card-green h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted f-12 d-block mb-1">Exited Today</span>
                    <h3 class="fw-bold mb-0 text-success"><?= number_format($exited_today); ?></h3>
                </div>
                <div class="stat-card-icon bg-light-green">
                    <i class="fa fa-sign-out"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Period Summary Rows -->
    <div class="col-12 mb-3">
        <?php
        echo render_period_block('Today Visitors', $today_visitors, $by['TODAY'] ?? [], $typesOrder, $typeIcons, 'border-danger', 'TODAY', $location_id);
        echo render_period_block('Yesterday Visitors', $yesterday_visitors, $by['YESTERDAY'] ?? [], $typesOrder, $typeIcons, 'border-primary', 'YESTERDAY', $location_id);
        echo render_period_block('Last 7 Days Visitors', $last_7days_visitors, $by['LAST7DAYS'] ?? [], $typesOrder, $typeIcons, 'border-success', 'LAST7DAYS', $location_id);
        echo render_period_block('Total Visitors', $total_visitors, $by['ALL'] ?? [], $typesOrder, $typeIcons, 'border-warning', 'ALL', $location_id);
        ?>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column: Daywise Log Table -->
    <div class="col-lg-7">
        <div class="card shadow-sm border" style="border-radius: 12px; overflow:hidden;">
            <div class="DayWiseVisitors">
                <?php include_once 'custom_view_visitor_dashboard_table.php'; ?>
            </div>
        </div>
    </div>


    <div class="col-lg-5">
        <div class="recent-logs-card shadow-sm">
            <div class="recent-logs-header">
                <h5>Recent Logs Stream</h5>

                <ul class="nav dashboard-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#IN" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            IN <span class="badge bg-danger ms-1"><?= count((array) $recent_in); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#OUT" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            OUT <span class="badge bg-success ms-1"><?= count((array) $recent_out); ?></span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content card-body p-3" style="max-height: 480px; overflow-y: auto;">
                <!-- Tab Pane IN -->
                <div class="tab-pane active" id="IN" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size:12.5px;">
                            <thead>
                                <tr class="text-muted">
                                    <th scope="col">Visitor Profile</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Whom to Meet</th>
                                    <th scope="col" style="white-space:nowrap;">In Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_in)) {
                                    foreach ($recent_in as $v) {
                                        $vt = strtolower($v['visit_type'] ?? '');
                                        $displayName = trim($v['name'] ?? '');
                                        $line2 = trim($v['email'] ?? '');
                                        $line3 = trim($v['phone'] ?? '');
                                        $whoCell = $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';

                                        switch ($vt) {
                                            case 'delivery':
                                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: trim($v['name'] ?? ''));
                                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                                $whoCell = $_SESSION['employees'][$v['deliver_to']] ?? $_SESSION['users'][$v['deliver_to']] ?? '-';
                                                break;
                                            case 'service':
                                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                                $svcPerson = trim($v['service_person'] ?? '');
                                                $displayName = $svcCompany ?: ($svcPerson ?: trim($v['name'] ?? ''));
                                                $line2 = $svcPerson ? ('Person: ' . $svcPerson) : '';
                                                $whoCell = $_SESSION['employees'][$v['authorized_employee']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                                break;
                                            case 'employee':
                                                $emp = $emp_arr[$v['employee_code']] ?? null;
                                                if (is_array($emp)) {
                                                    $displayName = $emp['display_name'] ?? $displayName;
                                                    $line2 = $emp['email_id'] ?? $line2;
                                                }
                                                if (empty($displayName))
                                                    { $displayName = 'Code: ' . ($v['employee_code'] ?? '-');
}
                                                $whoCell = $v['employee_department'] ?? '-';
                                                break;

                                            default:
                                            break;
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($v['image_path'])) { ?>
                                                        <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>"
                                                            class="rounded-circle avatar-in"
                                                            style="width:32px; height:32px; object-fit:cover;" alt="User Profile">
                                                    <?php } else { ?>
                                                        <span class="avatar-circle avatar-in"><?= initials($displayName) ?></span>
                                                    <?php } ?>
                                                    <div>
                                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($displayName) ?>
                                                        </div>
                                                        <div class="text-muted f-10">
                                                            <?= htmlspecialchars($line2 ?: ($line3 ?: 'Check-in entry')) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-capitalize text-muted"><?= $vt ?></td>
                                            <td class="text-dark"><?= htmlspecialchars($whoCell) ?></td>
                                            <td style="white-space:nowrap;" class="text-muted">
                                                <?= date('d M, h:i A', strtotime($v['in_datetime'])) ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No entry logs for today.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Pane OUT -->
                <div class="tab-pane" id="OUT" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size:12.5px;">
                            <thead>
                                <tr class="text-muted">
                                    <th scope="col">Visitor Profile</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Whom to Meet</th>
                                    <th scope="col" style="white-space:nowrap;">Out Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_out)) {
                                    foreach ($recent_out as $v) {
                                        $vt = strtolower($v['visit_type'] ?? '');
                                        $displayName = trim($v['name'] ?? '');
                                        $line2 = trim($v['email'] ?? '');
                                        $line3 = trim($v['phone'] ?? '');
                                        $whoCell = $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';

                                        switch ($vt) {
                                            case 'delivery':
                                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: trim($v['name'] ?? ''));
                                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                                $whoCell = $_SESSION['employees'][$v['deliver_to']] ?? $_SESSION['users'][$v['deliver_to']] ?? '-';
                                                break;
                                            case 'service':
                                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                                $svcPerson = trim($v['service_person'] ?? '');
                                                $displayName = $svcCompany ?: ($svcPerson ?: trim($v['name'] ?? ''));
                                                $line2 = $svcPerson ? ('Person: ' . $svcPerson) : '';
                                                $whoCell = $_SESSION['employees'][$v['authorized_employee']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                                break;
                                            case 'employee':
                                                $emp = $emp_arr[$v['employee_code']] ?? null;
                                                if (is_array($emp)) {
                                                    $displayName = $emp['display_name'] ?? $displayName;
                                                    $line2 = $emp['email_id'] ?? $line2;
                                                }
                                                if (empty($displayName))
                                                    { $displayName = 'Code: ' . ($v['employee_code'] ?? '-');
}
                                                $whoCell = $v['employee_department'] ?? '-';
                                                break;

                                            default:
                                            break;
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($v['image_path'])) { ?>
                                                        <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>"
                                                            class="rounded-circle avatar-out"
                                                            style="width:32px; height:32px; object-fit:cover;" alt="User Profile">
                                                    <?php } else { ?>
                                                        <span class="avatar-circle avatar-out"><?= initials($displayName) ?></span>
                                                    <?php } ?>
                                                    <div>
                                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($displayName) ?>
                                                        </div>
                                                        <div class="text-muted f-10">
                                                            <?= htmlspecialchars($line2 ?: ($line3 ?: 'Check-out exit')) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-capitalize text-muted"><?= $vt ?></td>
                                            <td class="text-dark"><?= htmlspecialchars($whoCell) ?></td>
                                            <td style="white-space:nowrap;" class="text-muted">
                                                <?= date('d M, h:i A', strtotime($v['out_datetime'])) ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No exit logs for today.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_view_visitor_dashboard_main.js"></script>

<!-- Global bucket detail modal container -->
<div class="modal fade" id="bucketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
