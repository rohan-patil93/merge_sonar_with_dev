<?php


$day_type = 'TODAY';
if (isset($_GET['page']) && $_GET['page'] === 'AJAX_CALL' && isset($_GET['day_type'])) {
    $day_type = $_GET['day_type'];
    $location_id = $_GET['location_id'];
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
}
$q      = trim($_GET['q'] ?? '');
$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = min(50, max(5, (int)($_GET['limit'] ?? 10))); // default 10, max 50
$tab = $_GET['tab'] ?? 'ALL';

$url = API . "?mode=getVisitorsDataBasedOnDate&in_datetime=" . urlencode($day_type) . "&offset=" . urlencode($offset) . "&limit=" . urlencode($limit) . "&q=" . urlencode($q) . "&location_id=" . $location_id;
// echo $url;
$che = callAPI($url);
$data = json_decode($che, 1);
$visitor_logs_all =  $data['visitor_logs_all'] ?? array();
$visitor_logs_pending =  $data['visitor_logs_pending'] ?? array();
$visitor_logs_completed =  $data['visitor_logs_completed'] ?? array();

$luggage_map =  $data['luggage_map'] ?? array();
$delivery_company_res =  $data['delivery_company_res'] ?? array();
$service_company_res =  $data['service_company_res'] ?? array();
$id_card_types_res =  $data['id_card_types_res'] ?? array();
$emp_arr =  $data['emp_arr'] ?? array();
// echo '<pre>'; print_r($emp_arr);  echo '</pre>';
$all_count = 0;
$in_count = 0;
$out_count = 0;
foreach ((array)$data['visitor_logs_res']  as $row) {
    $all_count++;
    if (!empty($row['in_datetime']) && empty($row['out_datetime'])) {
        $in_count++;
    }
    if (!empty($row['in_datetime']) && !empty($row['out_datetime'])) {
        $out_count++;
    }
}

/* page context (kept for rendering footer) */
$current_count = count((array)$visitor_logs_all);
$show_from     = ($all_count > 0 && $current_count > 0) ? ($offset + 1) : 0;
$show_to       = ($current_count > 0) ? min($offset + $current_count, $all_count) : 0;
/* prev/next offsets */
$prev_offset = max($offset - $limit, 0);
$next_offset = $offset + $limit;
/* disable next if we reached the end */
$has_next = ($next_offset < $all_count);

/* Pending page context */
$pending_count_now = count((array)$visitor_logs_pending);
$show_from_p       = ($in_count > 0 && $pending_count_now > 0) ? ($offset + 1) : 0;
$show_to_p         = ($pending_count_now > 0) ? min($offset + $pending_count_now, $in_count) : 0;
$has_next_p        = (($offset + $limit) < $in_count);

/* Completed page context */
$completed_count_now = count((array)$visitor_logs_completed);
$show_from_c         = ($out_count > 0 && $completed_count_now > 0) ? ($offset + 1) : 0;
$show_to_c           = ($completed_count_now > 0) ? min($offset + $completed_count_now, $out_count) : 0;
$has_next_c          = (($offset + $limit) < $out_count);


$title = "";
if ($day_type == 'TODAY') {
    $title = "Today Visitors";
} else if ($day_type == 'YESTERDAY') {
    $title = "Yesterday Visitors";
} else if ($day_type == 'LAST7DAYS') {
    $title = "Last 7 Days Visitors";
} else if ($day_type == 'ALL') {
    $title = "All Visitors";
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/custom_view_visitor_dashboard_table.css">

<div class="card-header">
    <h5> <?php echo $title; ?> </h5>
    <div class="card-header-right d-flex align-items-center gap-3">
        <a href="index.php?option=COM_VIEW_VISITORS_REPORT"
            target="_blank"
            title="Go to Report Page"
            class="ml-15">
            <i class="ti-fullscreen icon-22 cursor-pointer"></i>
        </a>


        <ul class="nav md-tabs infisuite_tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 <?php echo $tab == 'ALL' ? 'active' : ''; ?> " data-bs-toggle="tab" href="#ALL" role="tab" aria-selected="true">All
                    <label class="form-label label label-xs label-success m-l-10"><?php echo $all_count; ?></label>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 <?php echo $tab == 'PENDING' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#PENDING" role="tab" aria-selected="false">Pending to Exit
                    <label class="form-label label label-xs label-success m-l-10"><?php echo $in_count; ?></label>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 <?php echo $tab == 'COMPLETED' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#COMPLETED" role="tab" aria-selected="false">Exit Completed
                    <label class="form-label label label-xs label-success m-l-10"><?php echo $out_count; ?></label>
                </a>
            </li>
        </ul>
    </div>

</div>
<?php

$ITEM_SETS = [

    'laptop'      => 'fa-laptop',
    'mobile'      => 'fa-mobile-screen',
    'tablet'      => 'fa-tablet-screen-button',
    'camera'      => 'fa-camera',
    'headphones'  => 'fa-headphones',
    'pendrive'    => 'fa-usb',
    'harddisk'    => 'fa-hard-drive',
    'documents'   => 'fa-file-lines',
    'samplebox'   => 'fa-box-open',
    'backpack'    => 'fa-bag-shopping',


    'courier'     => 'fa-box',
    'food'        => 'fa-bowl-food',
    'supplies'    => 'fa-pencil',
    'spares'      => 'fa-gears',
    'returnship'  => 'fa-arrow-rotate-left',
    'crate'       => 'fa-box-open',
    'envelope'    => 'fa-envelope',
    'drum'        => 'fa-drum-steelpan',
    'trolley'     => 'fa-cart-flatbed',
    'pallet'      => 'fa-pallet',


    'toolkit'     => 'fa-screwdriver-wrench',
    'laptop'      => 'fa-laptop',
    'ladder'      => 'fa-person-arrow-up-from-line',
    'safety'      => 'fa-helmet-safety',
    'cleaning'    => 'fa-soap',
    'camera'      => 'fa-camera',
    'spares'      => 'fa-gears',
    'ac'          => 'fa-snowflake',
    'lights'      => 'fa-lightbulb',
    'fan'         => 'fa-fan',
    'pc'          => 'fa-desktop',
    'printer'     => 'fa-print',
    'router'      => 'fa-wifi',
    'projector'   => 'fa-video',


    'companylaptop' => 'fa-laptop',
    'monitor'       => 'fa-desktop',
    'keyboard'      => 'fa-keyboard',
    'mouse'         => 'fa-computer-mouse',
    'headset'       => 'fa-headphones',
    'idcard'        => 'fa-id-card',
    'backpack'      => 'fa-bag-shopping',
    'docs'          => 'fa-file-lines',
    'demo'          => 'fa-suitcase-rolling',
    'samples'       => 'fa-flask',


    'chair'       => 'fa-chair',
    'art'         => 'fa-image',
    'music'       => 'fa-guitar',
    'sports'      => 'fa-medal',
    'food'        => 'fa-burger',
    'plants'      => 'fa-seedling',
    'electronics' => 'fa-bolt',
    'box'         => 'fa-box',
    'bag'         => 'fa-briefcase',
    'misc'        => 'fa-ellipsis',

];

?>

<div class="tab-content tabs card-block mt-3">
    <div class="tab-pane <?php echo $tab == 'ALL' ? 'active' : ''; ?>" id="ALL" role="tabpanel">
        <div class="d-flex justify-content-end align-items-center mb-2">
            <div class="d-flex gap-8">
                <input type="text"
                    id="visitors_q"
                    class="form-control form-control-sm min-w-260"
                    placeholder="Search name / email / phone / ID / Emp code"
                    value="<?php echo htmlspecialchars($q); ?>">
                <input type="hidden" id="visitors_limit" value="<?php echo (int)$limit; ?>">

                <button type="button"
                    class="btn btn-sm btn-primary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="visitors_limit" data-q="visitors_q" data-tab="ALL">
                    <i class="fa fa-search me-1"></i>
                </button>

                <?php if ($q !== '') { ?>
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="visitors_limit" data-q="" data-tab="ALL">
                        Reset
                    </button>
                <?php } ?>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th class="col-w-20 text-wrap">Name / Company / Contact</th>
                    <th class="col-w-15">Visit type</th>
                    <th class="col-w-20">Items</th>
                    <th class="col-w-20 text-wrap">Whom to meet / Deliver to / Authorized</th>
                    <th class="col-w-12-5">In Date Time</th>
                    <th class="col-w-12-5">Out Date Time</th>
                </tr>
                <?php if (count((array)$visitor_logs_all) > 0) {
                    foreach ($visitor_logs_all as $index => $v) {
                        $name_label_class = 'bg-c-red';
                        if (!empty($v['in_datetime']) && !empty($v['out_datetime'])) {
                            $name_label_class = 'bg-c-green';
                        }
                        $vt = strtolower($v['visit_type'] ?? '');
                        $displayName = trim($v['name'] ?? '');
                        $line2 = trim($v['email'] ?? '');
                        $line3 = trim($v['phone'] ?? '');
                        $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ??  '-');

                        switch ($vt) {
                            case 'delivery':
                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '');
                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                $line3 = '';
                                $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                break;

                            case 'service':
                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                $svcPerson  = trim($v['service_person'] ?? '');
                                $displayName = $svcCompany ?: ($svcPerson ?? '');
                                $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                $line3 = '';
                                $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                $whoCell = htmlspecialchars($authEmp) ?? '';
                                break;

                            case 'employee':
                                $empEmail = $v['email'] ?? '';
                                $empPhone = $v['emp_temp_phone'] ?? '';
                                $displayName = $v['emp_temp_name'] ?? '';
                                $emp = $emp_arr[$v['employee_code']] ?? null;
                                if (is_array($emp)) {
                                    $empEmail = $emp['email'] ?? $empEmail;
                                    $empPhone = $emp['email_id'] ?? $empPhone;
                                    $displayName = $emp['display_name'] ?? $displayName;
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
                                <?php if (!empty($v['image_path'])) { ?>
                                    <span class="avatar-hover">
                                        <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="rounded-circle <?php echo $roundClass; ?> me-2 avatar-40">
                                        <span class="rounded-circle-pop"><img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" alt="">
                                            <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                        </span>
                                    </span>
                                <?php } else { ?>
                                    <span class="avatar-hover">
                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>  me-2 avatar-initial avatar-initial-40"

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
                                <span class="label f-14 <?php echo $name_label_class; ?>"><?= htmlspecialchars($displayName ?: '-') ?></span><br>
                                <?php if ($line2) { ?><span class=""><?= htmlspecialchars($line2) ?></span><br><?php } ?>
                                <?php if ($line3) { ?><span class=""><?= htmlspecialchars($line3) ?></span><?php } ?>
                            </td>
                            <td class="text-capitalize"><?= htmlspecialchars($vt ?: '-') ?></td>
                            <td>
                                <?php
                                $items = $luggage_map[$v['id']] ?? [];
                                if (empty($items)) {
                                    echo '<span class="text-muted small">-</span>';
                                } else {
                                    foreach ($items as $it) {
                                        $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis';
                                        $serial = trim($it['serial_or_remarks'] ?? '');
                                        $label  = $serial !== '' ? htmlspecialchars($serial) : '-';
                                ?>
                                        <span class="avatar-hover">
                                            <span class="d-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>   avatar-initial avatar-40-pointer">
                                                <i class="fa <?= $icon ?>"></i>
                                            </span>
                                            <span class="items-circle-pop ">
                                                <div class="items-pop">
                                                    <div>Serial No:<?= htmlspecialchars($label ?? '-') ?></div>
                                                    <div>Type: <?= htmlspecialchars($it['visitor_type'] ?? '-') ?></div>
                                                    <div>Return: <?= $it['returnable'] == 1 ? 'YES' . ' (' . $it['return_date'] . ')' : 'NO'  ?></div>
                                                </div>
                                            </span>
                                        </span>
                                <?php
                                    }
                                }
                                ?>
                            </td>
                            <td><?= $whoCell ?: '-' ?></td>
                            <td><?= !empty($v['in_datetime'])  ? date('d-M-Y h:i A', strtotime($v['in_datetime']))  : '' ?></td>
                            <td><?php if (!empty($v['out_datetime'])): ?>
                                    <?= htmlspecialchars(date('d-M-Y h:i A', strtotime($v['out_datetime'])), ENT_QUOTES) ?>
                                <?php else: ?>
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-primary js-checkout"
                                        data-url="index.php?option=VISITOR_OUT_VIEW&type=<?php echo $vt; ?>&gate_id=<?php echo $_SESSION['gate_id'] ?? 1; ?>&Rid=<?= deocdeId('id:' . $v['id']) ?>"
                                        data-target="_blank"
                                        rel="noopener">
                                        Check out
                                    </a>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 max-h-300" alt="" src="assets/images/work.png">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Footer: showing X–Y of total, with Prev / Next -->
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="small text-muted mb-2 mb-sm-0">
                <?php if ($all_count > 0 && $current_count > 0) { ?>
                    Showing <strong><?php echo $show_from; ?></strong> to <strong><?php echo $show_to; ?></strong> of <strong><?php echo (int)$all_count; ?></strong>
                <?php } else { ?>
                    Showing 0 to 0 of <?php echo (int)$all_count; ?>
                <?php } ?>
            </div>

            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <!-- Limit selector -->
                <select class="form-select form-select-sm w-auto getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-q="<?php echo json_encode($q); ?>" data-tab="ALL">
                    <?php foreach ([10, 20, 30, 50] as $opt) { ?>
                        <option value="<?php echo $opt; ?>" <?php echo ($limit == $opt ? 'selected' : ''); ?>>
                            <?php echo $opt; ?>/page
                        </option>
                    <?php } ?>
                </select>

                <!-- Prev / Next as a compact btn-group -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Pager">
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$prev_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="ALL"
                        <?php echo ($offset <= 0 ? 'disabled' : ''); ?>>
                        ‹ Prev
                    </button>

                    <!-- Next -->
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$next_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="ALL"
                        <?php echo (!$has_next ? 'disabled' : ''); ?>>
                        Next ›
                    </button>
                </div>
            </div>
        </div>


    </div>
    <div class="tab-pane <?php echo $tab == 'PENDING' ? 'active' : ''; ?>" id="PENDING" role="tabpanel">
        <div class="d-flex justify-content-end align-items-center mb-2">
            <div class="d-flex gap-8">
                <input type="text"
                    id="visitors_q_pending"
                    class="form-control form-control-sm min-w-260"
                    placeholder="Search in Pending"
                    value="<?php echo htmlspecialchars($q); ?>">
                <button type="button"
                    class="btn btn-sm btn-primary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="<?php echo (int)$limit; ?>" data-q="visitors_q_pending" data-tab="PENDING">
                    <i class="fa fa-search me-1"></i>
                </button>
                <?php if ($q !== '') { ?>
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="<?php echo (int)$limit; ?>" data-q="" data-tab="PENDING">
                        Reset
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th class="col-w-20 text-wrap">Name / Company / Contact</th>
                    <th class="col-w-15">Visit type</th>
                    <th class="col-w-20">Items</th>
                    <th class="col-w-20 text-wrap">Whom to meet / Deliver to / Authorized</th>
                    <th class="col-w-12-5">In Date Time</th>
                    <th class="col-w-12-5">Out Date Time</th>

                </tr>
                <?php if (count((array)$visitor_logs_pending) > 0) {
                    foreach ($visitor_logs_pending as $index => $v) {
                        $name_label_class = 'bg-c-red';

                        $vt = strtolower($v['visit_type'] ?? '');
                        $displayName = trim($v['name'] ?? '');
                        $line2 = trim($v['email'] ?? '');
                        $line3 = trim($v['phone'] ?? '');
                        $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ??  '-');

                        switch ($vt) {
                            case 'delivery':
                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '');
                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                $line3 = '';
                                $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                break;

                            case 'service':
                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                $svcPerson  = trim($v['service_person'] ?? '');
                                $displayName = $svcCompany ?: ($svcPerson  ?? '');
                                $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                $line3 = '';
                                $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                $whoCell = htmlspecialchars($authEmp) ?? '';
                                break;

                            case 'employee':
                                $empEmail = $v['email'] ?? '';
                                $empPhone = $v['emp_temp_phone'] ?? '';
                                $displayName = $v['emp_temp_name'] ?? '';
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
                                <?php if (!empty($v['image_path'])) { ?>
                                    <span class="avatar-hover">
                                        <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="rounded-circle <?php echo $roundClass; ?> me-2 avatar-40">
                                        <span class="rounded-circle-pop"><img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" alt="">
                                            <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                        </span>
                                    </span>
                                <?php } else { ?>
                                    <span class="avatar-hover">
                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>  me-2 avatar-initial avatar-initial-40"
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
                                <span class="label f-14 <?php echo $name_label_class; ?>"><?= htmlspecialchars($displayName ?: '-') ?></span><br>
                                <?php if ($line2) { ?><span class=""><?= htmlspecialchars($line2) ?></span><br><?php } ?>
                                <?php if ($line3) { ?><span class=""><?= htmlspecialchars($line3) ?></span><?php } ?>
                            </td>
                            <td class="text-capitalize"><?= htmlspecialchars($vt ?: '-') ?></td>
                            <td>
                                <?php
                                $items = $luggage_map[$v['id']] ?? [];
                                if (empty($items)) {
                                    echo '<span class="text-muted small">-</span>';
                                } else {
                                    foreach ($items as $it) {
                                        $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis';
                                        $serial = trim($it['serial_or_remarks'] ?? '');
                                        $label  = $serial !== '' ? htmlspecialchars($serial) : '-';
                                ?>
                                        <span class="avatar-hover">
                                            <span class="d-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>   avatar-initial avatar-40-pointer">
                                                <i class="fa <?= $icon ?>"></i>
                                            </span>
                                            <span class="items-circle-pop ">
                                                <div class="items-pop">
                                                    <div>Serial No:<?= htmlspecialchars($label ?? '-') ?></div>
                                                    <div>Type: <?= htmlspecialchars($it['visitor_type'] ?? '-') ?></div>
                                                    <div>Return: <?= $it['returnable'] == 1 ? 'YES' . ' (' . $it['return_date'] . ')' : 'NO'  ?></div>
                                                </div>
                                            </span>
                                        </span>
                                <?php
                                    }
                                }
                                ?>
                            </td>
                            <td><?= $whoCell ?: '-' ?></td>

                            <td><?= !empty($v['in_datetime'])  ? date('d-M-Y h:i A', strtotime($v['in_datetime']))  : '' ?></td>
                            <td>
                                <?php if (!empty($v['out_datetime'])): ?>
                                    <?= htmlspecialchars(date('d-M-Y h:i A', strtotime($v['out_datetime'])), ENT_QUOTES) ?>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-primary" href='index.php?option=VISITOR_OUT_VIEW&type=<?php echo $vt; ?>&gate_id=<?php echo $_SESSION['gate_id'] ?? 1; ?>&Rid=<?= deocdeId('id:' . $v['id']) ?>' target="_blank" rel="noopener">Check out</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px  max-h-300" alt="" src="assets/images/work.png">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        </table>

        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="small text-muted mb-2 mb-sm-0">
                <?php if ($in_count > 0 && $pending_count_now > 0) { ?>
                    Showing <strong><?php echo $show_from_p; ?></strong> to <strong><?php echo $show_to_p; ?></strong> of <strong><?php echo (int)$in_count; ?></strong>
                <?php } else { ?>
                    Showing 0 to 0 of <?php echo (int)$in_count; ?>
                <?php } ?>
            </div>

            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <!-- Limit selector -->
                <select class="form-select form-select-sm w-auto getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-q="<?php echo json_encode($q); ?>" data-tab="PENDING">
                    <?php foreach ([10, 20, 30, 50] as $opt) { ?>
                        <option value="<?php echo $opt; ?>" <?php echo ($limit == $opt ? 'selected' : ''); ?>>
                            <?php echo $opt; ?>/page
                        </option>
                    <?php } ?>
                </select>

                <!-- Prev / Next as a compact btn-group -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Pager">
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$prev_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="PENDING"
                        <?php echo ($offset <= 0 ? 'disabled' : ''); ?>>
                        ‹ Prev
                    </button>

                    <!-- Next -->
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$next_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="PENDING"
                        <?php echo (!$has_next_p ? 'disabled' : ''); ?>>
                        Next ›
                    </button>
                </div>
            </div>
        </div>

    </div>
    <div class="tab-pane <?php echo $tab == 'COMPLETED' ? 'active' : ''; ?>" id="COMPLETED" role="tabpanel">
        <div class="d-flex justify-content-end align-items-center mb-2">
            <div class="d-flex gap-8">
                <input type="text"
                    id="visitors_q_completed"
                    class="form-control form-control-sm min-w-260"
                    placeholder="Search in Completed"
                    value="<?php echo htmlspecialchars($q); ?>">
                <button type="button"
                    class="btn btn-sm btn-primary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="<?php echo (int)$limit; ?>" data-q="visitors_q_completed" data-tab="COMPLETED">
                    <i class="fa fa-search me-1"></i>
                </button>
                <?php if ($q !== '') { ?>
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-limit="<?php echo (int)$limit; ?>" data-q="" data-tab="COMPLETED">
                        Reset
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th class="col-w-20 text-wrap">Name / Company / Contact</th>
                    <th class="col-w-15">Visit type</th>
                    <th class="col-w-20">Items</th>
                    <th class="col-w-20 text-wrap">Whom to meet / Deliver to / Authorized</th>
                    <th class="col-w-12-5">In Date Time</th>
                    <th class="col-w-12-5">Out Date Time</th>

                </tr>
                <?php if (count((array)$visitor_logs_completed) > 0) {
                    foreach ($visitor_logs_completed as $index => $v) {
                        $name_label_class = 'bg-c-green';

                        $vt = strtolower($v['visit_type'] ?? '');
                        $displayName = trim($v['name'] ?? '');
                        $line2 = trim($v['email'] ?? '');
                        $line3 = trim($v['phone'] ?? '');
                        $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ??  '-');

                        switch ($vt) {
                            case 'delivery':
                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '');
                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                $line3 = '';
                                $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                break;

                            case 'service':
                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                $svcPerson  = trim($v['service_person'] ?? '');
                                $displayName = $svcCompany ?: ($svcPerson  ?? '');
                                $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                $line3 = '';
                                $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                $whoCell = htmlspecialchars($authEmp) ?? '';
                                break;

                            case 'employee':
                                $empEmail = $v['email'] ?? '';
                                $empPhone = $v['emp_temp_phone'] ?? '';
                                $displayName = $v['emp_temp_name'] ?? '';
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
                                <?php if (!empty($v['image_path'])) { ?>
                                    <span class="avatar-hover">
                                        <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="rounded-circle <?php echo $roundClass; ?> me-2 avatar-40">
                                        <span class="rounded-circle-pop"><img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" alt="">
                                            <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                        </span>
                                    </span>
                                <?php } else { ?>
                                    <span class="avatar-hover">
                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>  me-2 avatar-initial avatar-initial-40"

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
                                <span class="label f-14 <?php echo $name_label_class; ?>"><?= htmlspecialchars($displayName ?: '-') ?></span><br>
                                <?php if ($line2) { ?><span class=""><?= htmlspecialchars($line2) ?></span><br><?php } ?>
                                <?php if ($line3) { ?><span class=""><?= htmlspecialchars($line3) ?></span><?php } ?>
                            </td>
                            <td class="text-capitalize"><?= htmlspecialchars($vt ?: '-') ?></td>
                            <td>
                                <?php
                                $items = $luggage_map[$v['id']] ?? [];
                                if (empty($items)) {
                                    echo '<span class="text-muted small">-</span>';
                                } else {
                                    foreach ($items as $it) {
                                        $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis';
                                        $serial = trim($it['serial_or_remarks'] ?? '');
                                        $label  = $serial !== '' ? htmlspecialchars($serial) : '-';
                                ?>
                                        <span class="avatar-hover">
                                            <span class="d-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>   avatar-initial avatar-40-pointer">
                                                <i class="fa <?= $icon ?>"></i>
                                            </span>
                                            <span class="items-circle-pop ">
                                                <div class="items-pop">
                                                    <div>Serial No:<?= htmlspecialchars($label ?? '-') ?></div>
                                                    <div>Type: <?= htmlspecialchars($it['visitor_type'] ?? '-') ?></div>
                                                    <div>Return: <?= $it['returnable'] == 1 ? 'YES' . ' (' . $it['return_date'] . ')' : 'NO'  ?></div>
                                                </div>
                                            </span>
                                        </span>
                                <?php
                                    }
                                }
                                ?>
                            </td>

                            <td><?= $whoCell ?: '-' ?></td>

                            <td><?= !empty($v['in_datetime'])  ? date('d-M-Y h:i A', strtotime($v['in_datetime']))  : '' ?></td>
                            <td><?= !empty($v['out_datetime']) ? date('d-M-Y h:i A', strtotime($v['out_datetime'])) : '' ?></td>
                        </tr>
                    <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px max-h-300" alt="" src="assets/images/work.png">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        </table>

        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="small text-muted mb-2 mb-sm-0">
                <?php if ($out_count > 0 && $completed_count_now > 0) { ?>
                    Showing <strong><?php echo $show_from_c; ?></strong> to <strong><?php echo $show_to_c; ?></strong> of <strong><?php echo (int)$out_count; ?></strong>
                <?php } else { ?>
                    Showing 0 to 0 of <?php echo (int)$out_count; ?>
                <?php } ?>
            </div>

            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <!-- Limit selector -->
                <select class="form-select form-select-sm w-auto getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="0" data-q="<?php echo json_encode($q); ?>" data-tab="COMPLETED">
                    <?php foreach ([10, 20, 30, 50] as $opt) { ?>
                        <option value="<?php echo $opt; ?>" <?php echo ($limit == $opt ? 'selected' : ''); ?>>
                            <?php echo $opt; ?>/page
                        </option>
                    <?php } ?>
                </select>

                <!-- Prev / Next as a compact btn-group -->
                <div class="btn-group btn-group-sm " role="group" aria-label="Pager">
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$prev_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="COMPLETED"
                        <?php echo ($offset <= 0 ? 'disabled' : ''); ?>>
                        ‹ Prev
                    </button>

                    <!-- Next -->
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center text-nowrap getvisitorsdata" data-period_key="<?= strtoupper($day_type); ?>" data-location_id="<?php echo $location_id; ?>" data-offset="<?php echo (int)$next_offset; ?>" data-limit="<?php echo (int)$limit; ?>" data-q="<?php echo json_encode($q); ?>" data-tab="COMPLETED"
                        <?php echo (!$has_next_c ? 'disabled' : ''); ?>>
                        Next ›
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>