<?php
$day_type = 'TODAY';
$location_id = $location_id ?? $_GET['location_id'] ?? $_SESSION['location_id'] ?? 0;
if (isset($_GET['page']) && $_GET['page'] === 'AJAX_CALL' && isset($_GET['day_type'])) {
    $day_type = $_GET['day_type'];
    $location_id = $_GET['location_id'] ?? $location_id;
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$q = trim($_GET['q'] ?? '');
$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
$tab = $_GET['tab'] ?? 'ALL';

$url = API . "?mode=getVisitorsDataBasedOnDate&in_datetime=" . urlencode($day_type) . "&offset=" . urlencode($offset) . "&limit=" . urlencode($limit) . "&q=" . urlencode($q) . "&location_id=" . $location_id;
$che = callAPI($url);
$data = json_decode($che, 1);

$visitor_logs_all = $data['visitor_logs_all'] ?? array();
$visitor_logs_pending = $data['visitor_logs_pending'] ?? array();
$visitor_logs_completed = $data['visitor_logs_completed'] ?? array();

$luggage_map = $data['luggage_map'] ?? array();
$delivery_company_res = $data['delivery_company_res'] ?? array();
$service_company_res = $data['service_company_res'] ?? array();
$id_card_types_res = $data['id_card_types_res'] ?? array();
$emp_arr = $data['emp_arr'] ?? array();
$vendors_res = $data['vendors_res'] ?? array();
$employees_map = $data['employees_map'] ?? array();
$users_map = $data['users_map'] ?? array();

$all_count = 0;
$in_count = 0;
$out_count = 0;

foreach ((array)($data['visitor_logs_res'] ?? []) as $row) {
    $all_count++;
    $c_status = (int)($row['current_Status'] ?? 0);
    if (!empty($row['in_datetime']) && empty($row['out_datetime']) && $c_status !== 10 && $c_status !== 199) {
        $in_count++;
    }
    if (!empty($row['in_datetime']) && (!empty($row['out_datetime']) || $c_status === 199)) {
        $out_count++;
    }
}

// Pagination context
$current_count = count((array)$visitor_logs_all);
$show_from = ($all_count > 0 && $current_count > 0) ? ($offset + 1) : 0;
$show_to = ($current_count > 0) ? min($offset + $current_count, $all_count) : 0;
$prev_offset = max($offset - $limit, 0);
$next_offset = $offset + $limit;
$has_next = ($next_offset < $all_count);

$pending_count_now = count((array)$visitor_logs_pending);
$show_from_p = ($in_count > 0 && $pending_count_now > 0) ? ($offset + 1) : 0;
$show_to_p = ($pending_count_now > 0) ? min($offset + $pending_count_now, $in_count) : 0;
$has_next_p = (($offset + $limit) < $in_count);

$completed_count_now = count((array)$visitor_logs_completed);
$show_from_c = ($out_count > 0 && $completed_count_now > 0) ? ($offset + 1) : 0;
$show_to_c = ($completed_count_now > 0) ? min($offset + $completed_count_now, $out_count) : 0;
$has_next_c = (($offset + $limit) < $out_count);

$title = "";
if ($day_type == 'TODAY') {
    $title = "Today Visitors";
} elseif ($day_type == 'YESTERDAY') {
    $title = "Yesterday Visitors";
} elseif ($day_type == 'LAST7DAYS') {
    $title = "Last 7 Days Visitors";
} elseif ($day_type == 'ALL') {
    $title = "All Visitors";
}

$ITEM_SETS = [
    'laptop'      => 'fa-laptop',
    'mobile'      => 'fa-mobile',
    'tablet'      => 'fa-tablet',
    'camera'      => 'fa-camera',
    'headphones'  => 'fa-headphones',
    'pendrive'    => 'fa-usb',
    'harddisk'    => 'fa-hdd-o',
    'documents'   => 'fa-file-text-o',
    'samplebox'   => 'fa-dropbox',
    'backpack'    => 'fa-briefcase',
    'courier'     => 'fa-box',
    'food'        => 'fa-cutlery',
    'supplies'    => 'fa-pencil',
    'spares'      => 'fa-gears',
    'returnship'  => 'fa-undo',
    'crate'       => 'fa-archive',
    'envelope'    => 'fa-envelope-o',
    'drum'        => 'fa-database',
    'trolley'     => 'fa-shopping-cart',
    'pallet'      => 'fa-tasks',
    'toolkit'     => 'fa-wrench',
    'ladder'      => 'fa-level-up',
    'safety'      => 'fa-shield',
    'cleaning'    => 'fa-trash',
    'ac'          => 'fa-snowflake-o',
    'lights'      => 'fa-lightbulb-o',
    'fan'         => 'fa-refresh',
    'pc'          => 'fa-desktop',
    'printer'     => 'fa-print',
    'router'      => 'fa-wifi',
    'projector'   => 'fa-video-camera',
    'companylaptop' => 'fa-laptop',
    'monitor'       => 'fa-desktop',
    'keyboard'      => 'fa-keyboard-o',
    'mouse'         => 'fa-mouse-pointer',
    'headset'       => 'fa-headphones',
    'idcard'        => 'fa-id-card-o',
    'docs'          => 'fa-file-text-o',
    'demo'          => 'fa-suitcase',
    'samples'       => 'fa-flask',
    'chair'         => 'fa-wheelchair',
    'art'           => 'fa-picture-o',
    'plants'        => 'fa-leaf',
    'electronics'   => 'fa-plug',
    'box'           => 'fa-cube',
    'bag'           => 'fa-shopping-bag',
    'misc'          => 'fa-ellipsis-h',
];
?>
<link rel="stylesheet" href="assets/css/custom_view_visitor_dashboard_table.css">

<div class="tbl-header-row">
    <div class="tbl-title">
        <h5><?php echo $title; ?></h5>
    </div>


    <div>
        <ul class="nav-tabs-custom">
            <li>
                <a class="nav-link <?php echo $tab == 'ALL' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#ALL" role="tab" onclick="changeDashboardTab('ALL')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    All <span class="badge-tab"><?php echo $all_count; ?></span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo $tab == 'PENDING' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#PENDING" role="tab" onclick="changeDashboardTab('PENDING')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    In Premises <span class="badge-tab"><?php echo $in_count; ?></span>
                </a>
            </li>
            <li>
                <a class="nav-link <?php echo $tab == 'COMPLETED' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#COMPLETED" role="tab" onclick="changeDashboardTab('COMPLETED')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    Exited <span class="badge-tab"><?php echo $out_count; ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content">

    <!-- ================= ALL VISITORS TAB ================= -->
    <div class="tab-pane <?php echo $tab == 'ALL' ? 'active' : ''; ?>" id="ALL" role="tabpanel">
        <div class="search-filter-panel">
            <input aria-label="Search name, email, phone, ID card..." type="text" id="visitors_q" placeholder="Search name, email, phone, ID card..." value="<?php echo htmlspecialchars($q); ?>">
            <input type="hidden" id="visitors_limit" value="<?php echo (int)$limit; ?>">
            <button type="button" class="btn btn-sm btn-primary getvisitorsdata"
                    data-period_key="<?= strtoupper($day_type); ?>"
                    data-location_id="<?php echo $location_id; ?>"
                    data-offset="0" data-limit="visitors_limit" data-q="visitors_q" data-tab="ALL"
                    style="padding:6px 12px; border-radius:6px;">
                <i class="fa fa-search"></i>
            </button>
            <?php if ($q !== '') { ?>
                <button type="button" class="btn btn-sm btn-outline-secondary getvisitorsdata"
                        data-period_key="<?= strtoupper($day_type); ?>"
                        data-location_id="<?php echo $location_id; ?>"
                        data-offset="0" data-limit="visitors_limit" data-q="" data-tab="ALL"
                        style="padding:6px 12px; border-radius:6px;">
                    Reset
                </button>
            <?php } ?>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th scope="col" style="width:30%;">Visitor details</th>
                        <th scope="col" style="width:12%;">Visit Type</th>
                        <th scope="col" style="width:15%;">Belongings</th>
                        <th scope="col" style="width:20%;">Whom to Meet</th>
                        <th scope="col" style="width:12%;">In Time</th>
                        <th scope="col" style="width:11%;">Out Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count((array)$visitor_logs_all) > 0) {
                        foreach ($visitor_logs_all as $v) {
                            $current_status = (int)($v['current_Status'] ?? 0);
                            $is_inside = (empty($v['out_datetime']) && $current_status !== 199);
                            if ($current_status === 10) {
                                $status_class = 'badge-warning';
                                $status_lbl = 'Pending Approval';
                            } else {
                                $status_class = $is_inside ? 'badge-in' : 'badge-out';
                                $status_lbl = $is_inside ? 'Inside' : 'Exited';
                            }

                            $vt = strtolower($v['visit_type'] ?? '');
                            $displayName = trim($v['name'] ?? '');
                            $line2 = trim($v['email'] ?? '');
                            $line3 = trim($v['phone'] ?? '');
                            $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';

                            switch ($vt) {
                                case 'delivery':
                                    $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: trim($v['name'] ?? ''));
                                    $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                    $whoCell = $employees_map[$v['deliver_to']] ?? $users_map[$v['deliver_to']] ?? $_SESSION['employees'][$v['deliver_to']] ?? $_SESSION['users'][$v['deliver_to']] ?? '-';
                                    break;
                                case 'service':
                                    $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                    $svcPerson = trim($v['service_person'] ?? '');
                                    $displayName = $svcCompany ?: ($svcPerson ?: trim($v['name'] ?? ''));
                                    $line2 = $svcPerson ? ('Person: ' . $svcPerson) : '';
                                    $whoCell = $employees_map[$v['authorized_employee']] ?? $users_map[$v['authorized_employee']] ?? $_SESSION['employees'][$v['authorized_employee']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                    break;
                                case 'employee':
                                    $emp = $emp_arr[$v['employee_code']] ?? null;
                                    if (is_array($emp)) {
                                        $displayName = $emp['display_name'] ?? $displayName;
                                        $line2 = $emp['email_id'] ?? $line2;
                                    }
                                    if (empty($displayName)) { $displayName = 'Code: ' . ($v['employee_code'] ?? '-');
}
                                    $whoCell = $v['employee_department'] ?? '-';
                                    break;
                                case 'doctor':
                                    $displayName = trim($v['doctor_name'] ?? '');
                                    $line2 = '';
                                    $line3 = trim($v['doctor_phone'] ?? '');
                                    $whoCell = '-';
                                    break;
                                case 'vendor_supplier':
                                    $vendorName = trim($vendors_res[$v['vendor_supplier_vendor_id']] ?? '');
                                    $driverName = trim($v['vendor_supplier_driver_name'] ?? '');
                                    $displayName = $vendorName ?: ($driverName ?: trim($v['name'] ?? ''));
                                    $line2 = $driverName ? ('Driver: ' . $driverName) : '';
                                    if (!empty($v['vendor_supplier_vehicle_number'])) {
                                        $line2 .= ($line2 ? ' · ' : '') . 'Vehicle: ' . $v['vendor_supplier_vehicle_number'];
                                    }
                                    $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';
                                    break;

                                default:
                                break;
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="avatar-wrapper">
                                        <?php if (!empty($v['image_path'])) { ?>
                                            <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="round-avatar-img" alt="User Profile">
                                        <?php } else { ?>
                                            <span class="round-avatar-initials"><?= initials($displayName) ?></span>
                                        <?php } ?>
                                        <div>
                                            <span class="lbl-name"><?= htmlspecialchars($displayName) ?></span>
                                            <span class="lbl-subtext"><?= htmlspecialchars($line2 ?: ($line3 ?: 'No contact details')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-capitalize text-muted" style="font-weight: 500;"><?= $vt ?></span>
                                </td>
                                <td>
                                    <div class="luggage-list">
                                        <?php
                                        $items = $luggage_map[$v['id']] ?? [];
                                        if (empty($items)) {
                                            echo '<span class="text-muted small">-</span>';
                                        } else {
                                            foreach ($items as $it) {
                                                $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis-h';
                                                ?>
                                                <span class="luggage-item-icon" data-bs-toggle="tooltip"
                                                      title="Type: <?= $it['visitor_type'] ?> · Serial: <?= $it['serial_or_remarks'] ?: '-' ?>">
                                                    <i class="fa <?= $icon ?>"></i>
                                                </span>
                                            <?php }
                                        } ?>
                                    </div>
                                </td>
                                <td class="text-dark"><?= htmlspecialchars($whoCell) ?></td>
                                <td class="text-muted"><?= date('d-M-Y h:i A', strtotime($v['in_datetime'])) ?></td>
                                <td>
                                    <?php if (!empty($v['out_datetime']) || $current_status === 199) { ?>
                                        <span class="text-muted"><?= date('d-M-Y h:i A', strtotime($v['out_datetime'])) ?></span>
                                    <?php } elseif ($current_status === 10) { ?>
                                        <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 11px; border-radius: 6px; font-weight: 600;"><i class="fa fa-clock me-1"></i> Awaiting Approval</span>
                                    <?php } else {
                                        $searchTerm = $v['visitor_idcard_no'] ?? ($v['employee_code'] ?? ($v['phone'] ?? $displayName));
                                        ?>
                                        <a href="javascript:void(0);" class="checkout-btn js-checkout"
                                           data-url="index.php?option=custom_insert_visitor_logs&active_tab=checkout&search_term=<?= urlencode($searchTerm) ?>&visitor_log_id=<?= $v['id'] ?>&from_dashboard=1">
                                            <i class="fa fa-sign-out"></i> Out
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa fa-info-circle mb-2" style="font-size:20px;"></i>
                                <p class="m-0">No records found for the selection criteria.</p>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="table-footer-pagination">
            <div class="small text-muted">
                Showing <strong><?php echo $show_from; ?></strong> to <strong><?php echo $show_to; ?></strong> of <strong><?php echo $all_count; ?></strong> entries
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
                <select aria-label="Select option" class="form-select form-select-sm w-auto getvisitorsdata"
                        data-period_key="<?= strtoupper($day_type); ?>"
                        data-location_id="<?php echo $location_id; ?>"
                        data-offset="0" data-q="<?php echo $q; ?>" data-tab="ALL"
                        style="padding: 4px 8px; font-size:12px; border-radius:4px;">
                    <?php foreach ([10, 20, 30, 50] as $opt) { ?>
                        <option value="<?php echo $opt; ?>" <?php echo $limit == $opt ? 'selected' : ''; ?>><?php echo $opt; ?> / page</option>
                    <?php } ?>
                </select>

                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $prev_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="ALL"
                            <?php echo $offset <= 0 ? 'disabled' : ''; ?>>Prev</button>

                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $next_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="ALL"
                            <?php echo !$has_next ? 'disabled' : ''; ?>>Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= PENDING TAB ================= -->
    <div class="tab-pane <?php echo $tab == 'PENDING' ? 'active' : ''; ?>" id="PENDING" role="tabpanel">
        <div class="search-filter-panel">
            <input aria-label="Search in Premises..." type="text" id="visitors_q_pending" placeholder="Search in Premises..." value="<?php echo htmlspecialchars($q); ?>">
            <button type="button" class="btn btn-sm btn-primary getvisitorsdata"
                    data-period_key="<?= strtoupper($day_type); ?>"
                    data-location_id="<?php echo $location_id; ?>"
                    data-offset="0" data-limit="<?php echo $limit; ?>" data-q="visitors_q_pending" data-tab="PENDING"
                    style="padding:6px 12px; border-radius:6px;">
                <i class="fa fa-search"></i>
            </button>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th scope="col" style="width:30%;">Visitor details</th>
                        <th scope="col" style="width:12%;">Visit Type</th>
                        <th scope="col" style="width:15%;">Belongings</th>
                        <th scope="col" style="width:20%;">Whom to Meet</th>
                        <th scope="col" style="width:12%;">In Time</th>
                        <th scope="col" style="width:11%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count((array)$visitor_logs_pending) > 0) {
                        foreach ($visitor_logs_pending as $v) {
                            $vt = strtolower($v['visit_type'] ?? '');
                            $displayName = trim($v['name'] ?? '');
                            $line2 = trim($v['email'] ?? '');
                            $line3 = trim($v['phone'] ?? '');
                            $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';

                            switch ($vt) {
                                case 'delivery':
                                    $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: trim($v['name'] ?? ''));
                                    $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                    $whoCell = $employees_map[$v['deliver_to']] ?? $users_map[$v['deliver_to']] ?? $_SESSION['employees'][$v['deliver_to']] ?? $_SESSION['users'][$v['deliver_to']] ?? '-';
                                    break;
                                case 'service':
                                    $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                    $svcPerson = trim($v['service_person'] ?? '');
                                    $displayName = $svcCompany ?: ($svcPerson ?: trim($v['name'] ?? ''));
                                    $line2 = $svcPerson ? ('Person: ' . $svcPerson) : '';
                                    $whoCell = $employees_map[$v['authorized_employee']] ?? $users_map[$v['authorized_employee']] ?? $_SESSION['employees'][$v['authorized_employee']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                    break;
                                case 'employee':
                                    $emp = $emp_arr[$v['employee_code']] ?? null;
                                    if (is_array($emp)) {
                                        $displayName = $emp['display_name'] ?? $displayName;
                                        $line2 = $emp['email_id'] ?? $line2;
                                    }
                                    if (empty($displayName)) { $displayName = 'Code: ' . ($v['employee_code'] ?? '-');
}
                                    $whoCell = $v['employee_department'] ?? '-';
                                    break;
                                case 'doctor':
                                    $displayName = trim($v['doctor_name'] ?? '');
                                    $line2 = '';
                                    $line3 = trim($v['doctor_phone'] ?? '');
                                    $whoCell = '-';
                                    break;
                                case 'vendor_supplier':
                                    $vendorName = trim($vendors_res[$v['vendor_supplier_vendor_id']] ?? '');
                                    $driverName = trim($v['vendor_supplier_driver_name'] ?? '');
                                    $displayName = $vendorName ?: ($driverName ?: trim($v['name'] ?? ''));
                                    $line2 = $driverName ? ('Driver: ' . $driverName) : '';
                                    if (!empty($v['vendor_supplier_vehicle_number'])) {
                                        $line2 .= ($line2 ? ' · ' : '') . 'Vehicle: ' . $v['vendor_supplier_vehicle_number'];
                                    }
                                    $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';
                                    break;

                                default:
                                break;
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="avatar-wrapper">
                                        <?php if (!empty($v['image_path'])) { ?>
                                            <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="round-avatar-img" alt="User Profile">
                                        <?php } else { ?>
                                            <span class="round-avatar-initials"><?= initials($displayName) ?></span>
                                        <?php } ?>
                                        <div>
                                            <span class="lbl-name"><?= htmlspecialchars($displayName) ?></span>
                                            <span class="lbl-subtext"><?= htmlspecialchars($line2 ?: ($line3 ?: 'No contact details')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-capitalize text-muted" style="font-weight: 500;"><?= $vt ?></span></td>
                                <td>
                                    <div class="luggage-list">
                                        <?php
                                        $items = $luggage_map[$v['id']] ?? [];
                                        if (empty($items)) {
                                            echo '<span class="text-muted small">-</span>';
                                        } else {
                                            foreach ($items as $it) {
                                                $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis-h';
                                                ?>
                                                <span class="luggage-item-icon" data-bs-toggle="tooltip" title="Type: <?= $it['visitor_type'] ?> · Serial: <?= $it['serial_or_remarks'] ?>">
                                                    <i class="fa <?= $icon ?>"></i>
                                                </span>
                                            <?php }
                                        } ?>
                                    </div>
                                </td>
                                <td class="text-dark"><?= htmlspecialchars($whoCell) ?></td>
                                <td class="text-muted"><?= date('d-M-Y h:i A', strtotime($v['in_datetime'])) ?></td>
                                <td>
                                    <?php
                                    $searchTerm = $v['visitor_idcard_no'] ?? ($v['employee_code'] ?? ($v['phone'] ?? $displayName));
                                    ?>
                                    <a href="javascript:void(0);" class="checkout-btn js-checkout"
                                       data-url="index.php?option=custom_insert_visitor_logs&active_tab=checkout&search_term=<?= urlencode($searchTerm) ?>&visitor_log_id=<?= $v['id'] ?>&from_dashboard=1">
                                        <i class="fa fa-sign-out"></i> Out
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No visitors currently inside.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Pending Footer -->
        <div class="table-footer-pagination">
            <div class="small text-muted">
                Showing <strong><?php echo $show_from_p; ?></strong> to <strong><?php echo $show_to_p; ?></strong> of <strong><?php echo $in_count; ?></strong> in premises
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $prev_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="PENDING"
                            <?php echo $offset <= 0 ? 'disabled' : ''; ?>>Prev</button>

                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $next_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="PENDING"
                            <?php echo !$has_next_p ? 'disabled' : ''; ?>>Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= COMPLETED TAB ================= -->
    <div class="tab-pane <?php echo $tab == 'COMPLETED' ? 'active' : ''; ?>" id="COMPLETED" role="tabpanel">
        <div class="search-filter-panel">
            <input aria-label="Search completed logs..." type="text" id="visitors_q_completed" placeholder="Search completed logs..." value="<?php echo htmlspecialchars($q); ?>">
            <button type="button" class="btn btn-sm btn-primary getvisitorsdata"
                    data-period_key="<?= strtoupper($day_type); ?>"
                    data-location_id="<?php echo $location_id; ?>"
                    data-offset="0" data-limit="<?php echo $limit; ?>" data-q="visitors_q_completed" data-tab="COMPLETED"
                    style="padding:6px 12px; border-radius:6px;">
                <i class="fa fa-search"></i>
            </button>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th scope="col" style="width:30%;">Visitor details</th>
                        <th scope="col" style="width:12%;">Visit Type</th>
                        <th scope="col" style="width:15%;">Belongings</th>
                        <th scope="col" style="width:20%;">Whom to Meet</th>
                        <th scope="col" style="width:12%;">In Time</th>
                        <th scope="col" style="width:11%;">Out Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count((array)$visitor_logs_completed) > 0) {
                        foreach ($visitor_logs_completed as $v) {
                            $vt = strtolower($v['visit_type'] ?? '');
                            $displayName = trim($v['name'] ?? '');
                            $line2 = trim($v['email'] ?? '');
                            $line3 = trim($v['phone'] ?? '');
                            $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';

                            switch ($vt) {
                                case 'delivery':
                                    $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '') ?: (trim($v['delivery_company'] ?? '') ?: trim($v['name'] ?? ''));
                                    $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                    $whoCell = $employees_map[$v['deliver_to']] ?? $users_map[$v['deliver_to']] ?? $_SESSION['employees'][$v['deliver_to']] ?? $_SESSION['users'][$v['deliver_to']] ?? '-';
                                    break;
                                case 'service':
                                    $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                    $svcPerson = trim($v['service_person'] ?? '');
                                    $displayName = $svcCompany ?: ($svcPerson ?: trim($v['name'] ?? ''));
                                    $line2 = $svcPerson ? ('Person: ' . $svcPerson) : '';
                                    $whoCell = $employees_map[$v['authorized_employee']] ?? $users_map[$v['authorized_employee']] ?? $_SESSION['employees'][$v['authorized_employee']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                    break;
                                case 'employee':
                                    $emp = $emp_arr[$v['employee_code']] ?? null;
                                    if (is_array($emp)) {
                                        $displayName = $emp['display_name'] ?? $displayName;
                                        $line2 = $emp['email_id'] ?? $line2;
                                    }
                                    if (empty($displayName)) { $displayName = 'Code: ' . ($v['employee_code'] ?? '-');
}
                                    $whoCell = $v['employee_department'] ?? '-';
                                    break;
                                case 'doctor':
                                    $displayName = trim($v['doctor_name'] ?? '');
                                    $line2 = '';
                                    $line3 = trim($v['doctor_phone'] ?? '');
                                    $whoCell = '-';
                                    break;
                                case 'vendor_supplier':
                                    $vendorName = trim($vendors_res[$v['vendor_supplier_vendor_id']] ?? '');
                                    $driverName = trim($v['vendor_supplier_driver_name'] ?? '');
                                    $displayName = $vendorName ?: ($driverName ?: trim($v['name'] ?? ''));
                                    $line2 = $driverName ? ('Driver: ' . $driverName) : '';
                                    if (!empty($v['vendor_supplier_vehicle_number'])) {
                                        $line2 .= ($line2 ? ' · ' : '') . 'Vehicle: ' . $v['vendor_supplier_vehicle_number'];
                                    }
                                    $whoCell = $employees_map[$v['whom_to_meet']] ?? $users_map[$v['whom_to_meet']] ?? $_SESSION['employees'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['whom_to_meet']] ?? '-';
                                    break;

                                default:
                                break;
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="avatar-wrapper">
                                        <?php if (!empty($v['image_path'])) { ?>
                                            <img src="uploads/visitors/<?= htmlspecialchars($v['image_path']) ?>" class="round-avatar-img" alt="User Profile">
                                        <?php } else { ?>
                                            <span class="round-avatar-initials"><?= initials($displayName) ?></span>
                                        <?php } ?>
                                        <div>
                                            <span class="lbl-name"><?= htmlspecialchars($displayName) ?></span>
                                            <span class="lbl-subtext"><?= htmlspecialchars($line2 ?: ($line3 ?: 'No contact details')) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-capitalize text-muted" style="font-weight: 500;"><?= $vt ?></span></td>
                                <td>
                                    <div class="luggage-list">
                                        <?php
                                        $items = $luggage_map[$v['id']] ?? [];
                                        if (empty($items)) {
                                            echo '<span class="text-muted small">-</span>';
                                        } else {
                                            foreach ($items as $it) {
                                                $icon = $ITEM_SETS[$it['item_key']] ?? 'fa-ellipsis-h';
                                                ?>
                                                <span class="luggage-item-icon" data-bs-toggle="tooltip" title="Type: <?= $it['visitor_type'] ?> · Serial: <?= $it['serial_or_remarks'] ?>">
                                                    <i class="fa <?= $icon ?>"></i>
                                                </span>
                                            <?php }
                                        } ?>
                                    </div>
                                </td>
                                <td class="text-dark"><?= htmlspecialchars($whoCell) ?></td>
                                <td class="text-muted"><?= date('d-M-Y h:i A', strtotime($v['in_datetime'])) ?></td>
                                <td class="text-muted"><?= date('d-M-Y h:i A', strtotime($v['out_datetime'])) ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No completed exit records.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Completed Footer -->
        <div class="table-footer-pagination">
            <div class="small text-muted">
                Showing <strong><?php echo $show_from_c; ?></strong> to <strong><?php echo $show_to_c; ?></strong> of <strong><?php echo $out_count; ?></strong> exited logs
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $prev_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="COMPLETED"
                            <?php echo $offset <= 0 ? 'disabled' : ''; ?>>Prev</button>

                    <button type="button" class="btn btn-outline-secondary getvisitorsdata"
                            data-period_key="<?= strtoupper($day_type); ?>"
                            data-location_id="<?php echo $location_id; ?>"
                            data-offset="<?php echo $next_offset; ?>"
                            data-limit="<?php echo $limit; ?>" data-q="<?php echo $q; ?>" data-tab="COMPLETED"
                            <?php echo !$has_next_c ? 'disabled' : ''; ?>>Next</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    // Helper to toggle active tab state locally on clicking tab items
    function changeDashboardTab(tabVal) {
        if (window.jQuery) {
            jQuery('[data-bs-toggle="tab"]').removeClass("active");
            jQuery('.tab-pane').removeClass("active");
            jQuery('#' + tabVal).addClass("active");
        }
    }

    // Auto initialize Bootstrap tooltips inside table
    if (window.jQuery) {
        jQuery(document).ready(function() {
            jQuery('[data-bs-toggle="tooltip"]').tooltip();
        });
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery) {
                jQuery('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
</script>
