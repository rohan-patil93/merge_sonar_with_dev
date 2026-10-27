<?php
// existing fetch
date_default_timezone_set('Asia/Kolkata');

if ($_GET['key'] == 'AJAX_CALL_MAIN') {
    $location_id = $_GET['location_id'];
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
}

$url  = API . "?mode=getVisitorsDashboardData&location_id=" . $location_id;
// echo $url;
$che  = callAPI($url);
$data = json_decode($che, true);

$total_visitors      = $data['total_visitors'] ?? 0;
$today_visitors      = $data['today_visitors'] ?? 0;
$yesterday_visitors  = $data['yesterday_visitors'] ?? 0;
$last_7days_visitors = $data['last_7days_visitors'] ?? 0;

$recent_in = $data['recent_in'] ?? [];
$recent_out = $data['recent_out'] ?? [];
$emp_arr = $data['emp_arr'] ?? [];

$by = $data['breakdown'] ?? [];
$typesOrder = ['visitor', 'delivery', 'service', 'employee', 'doctor', 'others'];
$typeIcons = [
    'visitor'  => 'fa-user',
    'delivery' => 'fa-truck',
    'service'  => 'fa-wrench',
    'employee' => 'fa-id-badge',
    'doctor' => 'fa-user-doctor',
    'others'   => 'fa-ellipsis-h'
];

function render_type_tile($key, $stats, $icons, $periodKey, $location_id)
{
    $label = ucfirst($key);
    $icon  = $icons[$key] ?? 'fa-circle';
    $total   = (int)($stats['total']   ?? 0);
    $inOnly  = (int)($stats['in_only'] ?? 0);
    $out     = (int)($stats['out']     ?? 0);
    ob_start(); ?>
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="card h-100 border rounded-3 shadow-sm">
            <div class="card-body p-3 hover-zoom">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa <?= $icon; ?> me-2"></i>
                    <div class="fw-semibold"><?= $label; ?></div>
                </div>
                <!-- 3 numbers: TOTAL / IN ONLY / OUT -->
                <div class="row row-cols-3 g-2 text-center">
                    <div class="col">
                        <div class="fw-bold fs-5 text-primary cursor-pointer openbucketmodal" data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>" data-bucket="total" data-location_id="<?php echo $location_id; ?>">
                            <?= $total; ?>
                        </div>
                        <div class="small text-muted">
                            <!-- <i class="fa fa-circle text-primary me-1"></i> TOTAL -->
                        </div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-5 text-danger cursor-pointer openbucketmodal" data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>" data-bucket="in_only" data-location_id="<?php echo $location_id; ?>">
                            <?= $inOnly; ?>
                        </div>
                        <div class="small text-muted">
                            <!-- <i class="fa fa-circle text-danger me-1"></i> IN ONLY -->
                        </div>
                    </div>
                    <div class="col">
                        <div class="fw-bold fs-5 text-success cursor-pointer openbucketmodal" data-period="<?= htmlspecialchars($periodKey) ?>" data-type="<?= htmlspecialchars($key) ?>" data-bucket="out" data-location_id="<?php echo $location_id; ?>">
                            <?= $out; ?>
                        </div>
                        <div class="small text-muted">
                            <!-- <i class="fa fa-circle text-success me-1"></i> OUT -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php return ob_get_clean();
}

function render_period_block($title, $bigNumber, $bucket, $typesOrder, $icons, $borderClass, $periodKey, $location_id)
{
    ob_start(); ?>
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body p-3 hover-zoom">
            <div class="row g-3 align-items-stretch">
                <!-- Left summary -->
                <div class="col-lg-3">
                    <div class="h-100 ps-3 border-start border-5 <?= $borderClass; ?> rounded-start">
                        <div class="d-flex align-items-center mt-1 mb-2">
                            <i class="fa fa-users me-2"></i>
                            <div class="fw-bold"><?= htmlspecialchars($title); ?></div>
                        </div>
                        <div class="fw-bold fs-1 lh-1 zoom-number cursor-pointer getvisitorsdata" data-period_key="<?= strtoupper($periodKey); ?>" data-location_id="<?php echo $location_id; ?>"><?= (int)$bigNumber; ?></div>
                        <div class="text-muted small mt-1">Total entries in this period</div>

                        <!-- Legend -->
                        <div class="d-flex gap-3 small mt-2">
                            <span class="text-nowrap"><i class="fa fa-circle text-primary me-1"></i> TOTAL</span>
                            <span class="text-nowrap"><i class="fa fa-circle text-danger me-1"></i> IN ONLY</span>
                            <span class="text-nowrap"><i class="fa fa-circle text-success me-1"></i> OUT</span>
                        </div>
                    </div>
                </div>

                <!-- Right: type tiles -->
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
<?php return ob_get_clean();
}
?>
<link rel="stylesheet" href="assets/css/custom_view_visitor_dashboard_main.css">

<div class="page-body">

    <?php
    echo render_period_block('Today Visitors',       $today_visitors,      $by['TODAY']     ?? [], $typesOrder, $typeIcons, 'border-danger',   'TODAY', $location_id);
    echo render_period_block('Yesterday Visitors',   $yesterday_visitors,  $by['YESTERDAY'] ?? [], $typesOrder, $typeIcons, 'border-primary',  'YESTERDAY', $location_id);
    echo render_period_block('Last 7 Days Visitors', $last_7days_visitors, $by['LAST7DAYS'] ?? [], $typesOrder, $typeIcons, 'border-success',  'LAST7DAYS', $location_id);
    echo render_period_block('Total Visitors',       $total_visitors,      $by['ALL']       ?? [], $typesOrder, $typeIcons, 'border-warning',  'ALL', $location_id);

    ?>

    <!-- Existing table -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="DayWiseVisitors">
                        <?php include('custom_view_visitor_dashboard_table.php'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <h5>Recent 10 </h5>
                        <div class="card-header-right d-flex align-items-center gap-3">
                            <ul class="nav md-tabs infisuite_tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#IN" role="tab" aria-selected="true">IN
                                        <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent_in); ?></label>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#OUT" role="tab" aria-selected="false">OUT
                                        <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent_out); ?></label>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>


                    <div class="tab-content tabs card-block mt-3">
                        <div class="tab-pane active" id="IN" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle f-12 table-sm">
                                    <thead>
                                        <tr>
                                            <th class="col-w-45 text-wrap">Name / Company / Contact</th>
                                            <th class="col-w-15">Visit type</th>
                                            <th class="col-w-25 text-wrap">Whom to meet / Deliver to / Authorized</th>
                                            <th class="col-w-15">In Date Time</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_in)) {
                                            foreach ($recent_in as $v) {
                                                $vt = strtolower($v['visit_type'] ?? '');
                                                // Defaults
                                                $displayName = trim($v['name'] ?? '');
                                                $line2 = trim($v['email'] ?? '');
                                                $line3 = trim($v['phone'] ?? '');
                                                $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ?? '-');

                                                switch ($vt) {
                                                    case 'delivery':
                                                        // delivery_company_person (preferred) → delivery_company → name
                                                        $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: (trim($v['name'] ?? '')));
                                                        $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                                        $line3 = ''; // keep two lines for delivery
                                                        $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                                        break;

                                                    case 'service':
                                                        // service_company / service_person
                                                        $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                                        $svcPerson  = trim($v['service_person'] ?? '');
                                                        $displayName = $svcCompany ?: ($svcPerson ?: (trim($v['name'] ?? '')));
                                                        $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                                        $line3 = '';
                                                        $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                                        $whoCell = htmlspecialchars($authEmp)  ??  '';
                                                        break;

                                                    case 'employee':
                                                        // Prefer to fetch from employees table if helper exists
                                                        $empEmail = $v['email'] ?? '';
                                                        $empPhone = $v['phone'] ?? '';
                                                        $emp = $emp_arr[$v['employee_code']] ?? null;
                                                        if (is_array($emp)) {
                                                            $empEmail = $emp['email_id'] ?? $empEmail;
                                                            $empPhone = $emp['phone'] ?? $empPhone;
                                                            if (empty($displayName)) $displayName = $emp['display_name'] ?? $displayName;
                                                        }

                                                        if (empty($displayName)) $displayName = 'Emp Code: ' . htmlspecialchars($v['employee_code'] ?? '-');
                                                        $line2 = htmlspecialchars($empEmail);
                                                        $line3 = htmlspecialchars($empPhone);
                                                        // Employee generally won’t have “whom to meet”; show department if present
                                                        $whoCell = htmlspecialchars($v['employee_department'] ?? '-');
                                                        break;

                                                    // visitor/others → keep defaults (name/email/phone + whom_to_meet)
                                                    default:
                                                        // already set
                                                        break;
                                                }
                                                $roundClass = $v['out_datetime'] ? 'out' : 'in';
                                                $visit_id = $v['visitor_idcard_no'] ?? $v['employee_code']  ?? '';
                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="align-middle">
                                                        <?php if (!empty($v['image_path'])) { ?>
                                                            <span class="avatar-hover">
                                                                <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>"
                                                                    class="rounded-circle <?= $roundClass; ?> me-2 avatar-40">

                                                                <span class="rounded-circle-pop"><img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" alt="">
                                                                    <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                                                </span>
                                                            </span>
                                                        <?php } else { ?>
                                                            <!-- <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>  me-2"
                                                                style="width:40px;height:40px;font-size:15px;">
                                                                <?= htmlspecialchars(initials($displayName)) ?>
                                                            </span> -->
                                                            <span class="avatar-hover">
                                                                <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?= $roundClass; ?> me-2 avatar-initial avatar-initial-40"
                                                                    title="<?= htmlspecialchars($displayName) ?>">
                                                                    <?= htmlspecialchars(initials($displayName)) ?>
                                                                </span>

                                                                <span class="rounded-circle-pop ">
                                                                    <div class="initials-big">
                                                                        <div class=""><?= htmlspecialchars($visit_id ?: '-') ?></div>
                                                                    </div>
                                                                </span>
                                                            </span>
                                                        <?php } ?>
                                                            <div class="d-inline-block align-top">
                                                                <span class="label f-12 bg-c-red"><?= htmlspecialchars($displayName ?: '-') ?></span>
                                                                <?php if ($line2) { ?><span class="d-block mt-2"><?= htmlspecialchars($line2) ?></span><?php } ?>
                                                                <?php if ($line3) { ?><span class="d-block mt-2"><?= htmlspecialchars($line3) ?></span><?php } ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-capitalize"><?= htmlspecialchars($vt ?: '-') ?></td>

                                                    <td><?= $whoCell ?: '-' ?></td>

                                                    <td>
                                                        <?= !empty($v['in_datetime']) ? date('d-M-Y h:i A', strtotime($v['in_datetime'])) : '' ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="4">
                                                    <div class="text-center p-4">
                                                        <p class="text-muted fw-bold mb-3"><i class="fa fa-warning me-1"></i>No Records</p>
                                                        <img class="img-fluid max-h-300" alt="" src="assets/images/work.png">

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane " id="OUT" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle f-12 table-sm">
                                    <thead>
                                        <tr>
                                            <th class="col-w-40 text-wrap">Name / Company / Contact</th>
                                            <th class="col-w-15">Visit type</th>
                                            <th class="col-w-20 text-wrap">Whom to meet / Deliver to / Authorized</th>
                                            <th class="col-w-12-5">In Date Time</th>
                                            <th class="col-w-12-5">Out Date Time</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_out)) {
                                            foreach ($recent_out as $v) {
                                                $vt = strtolower($v['visit_type'] ?? '');
                                                $displayName = trim($v['name'] ?? '');
                                                $line2 = trim($v['email'] ?? '');
                                                $line3 = trim($v['phone'] ?? '');
                                                $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ??  '-');

                                                switch ($vt) {
                                                    case 'delivery':
                                                        $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: (trim($v['name'] ?? '')));
                                                        $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                                        $line3 = '';
                                                        $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                                        break;

                                                    case 'service':
                                                        $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                                        $svcPerson  = trim($v['service_person'] ?? '');
                                                        $displayName = $svcCompany ?: ($svcPerson ?: (trim($v['name'] ?? '')));
                                                        $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                                        $line3 = '';
                                                        $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                                        $whoCell = htmlspecialchars($authEmp) ?? '';
                                                        break;

                                                    case 'employee':
                                                        $empEmail = $v['email'] ?? '';
                                                        $empPhone = $v['phone'] ?? '';
                                                        $emp = $emp_arr[$v['employee_code']] ?? null;
                                                        if (is_array($emp)) {
                                                            $empEmail = $emp['email'] ?? $empEmail;
                                                            $empPhone = $emp['email_id'] ?? $empPhone;
                                                            if (empty($displayName)) $displayName = $emp['display_name'] ?? $displayName;
                                                        }

                                                        if (empty($displayName)) $displayName = 'Emp Code: ' . htmlspecialchars($v['employee_code'] ?? '-');
                                                        $line2 = htmlspecialchars($empEmail);
                                                        $line3 = htmlspecialchars($empPhone);
                                                        $whoCell = htmlspecialchars($v['employee_department'] ?? '-');
                                                        break;

                                                    default:
                                                        // visitor / others keep defaults: name/email/phone, whom_to_meet
                                                        break;
                                                }
                                                $roundClass = $v['out_datetime'] ? 'out' : 'in';
                                                $visit_id = $v['visitor_idcard_no'] ?? $v['employee_code']  ?? '';
                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="align-middle">
                                                        <?php if (!empty($v['image_path'])) { ?>
                                                            <span class="avatar-hover">
                                                                <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>"
                                                                    class="rounded-circle <?= $roundClass; ?> me-2 avatar-40">

                                                                <span class="rounded-circle-pop"><img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" alt="">
                                                                    <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                                                </span>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="avatar-hover">
                                                                <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?= $roundClass; ?> me-2 avatar-initial avatar-initial-40"
                                                                    title="<?= htmlspecialchars($displayName) ?>">
                                                                    <?= htmlspecialchars(initials($displayName)) ?>
                                                                </span>

                                                                <span class="rounded-circle-pop ">
                                                                    <div class="initials-big">
                                                                        <div class=""><?= htmlspecialchars($visit_id ?: '-') ?></div>
                                                                    </div>
                                                                </span>
                                                            </span>
                                                        <?php } ?>
                                                            <div class="d-inline-block align-top">
                                                                <span class="label f-12 bg-c-green"><?= htmlspecialchars($displayName ?: '-') ?></span>
                                                                <?php if ($line2) { ?><span class="d-block mt-2"><?= htmlspecialchars($line2) ?></span><?php } ?>
                                                                <?php if ($line3) { ?><span class="d-block mt-2"><?= htmlspecialchars($line3) ?></span><?php } ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-capitalize"><?= htmlspecialchars($vt ?: '-') ?></td>

                                                    <td><?= $whoCell ?: '-' ?></td>

                                                    <td><?= !empty($v['in_datetime'])  ? date('d-M-Y h:i A', strtotime($v['in_datetime']))  : '' ?></td>
                                                    <td><?= !empty($v['out_datetime']) ? date('d-M-Y h:i A', strtotime($v['out_datetime'])) : '' ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5">
                                                    <div class="text-center p-4">
                                                        <p class="text-muted fw-bold mb-3"><i class="fa fa-warning me-1"></i>No Records</p>
                                                        <img class="img-fluid max-h-300" alt="" src="assets/images/work.png">

                                                    </div>
                                                </td>
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
    </div>

</div>
<script src="assets/js/custom_view_visitor_dashboard_main.js"></script>


<div class="modal fade" id="bucketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content"><!-- filled by PHP partial via .load() --></div>
    </div>
</div>