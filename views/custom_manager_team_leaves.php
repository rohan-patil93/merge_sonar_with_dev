<?php
$company_id = isset($_SESSION['company_id']) ? (int)$_SESSION['company_id'] : 0;
$user_id    = isset($_SESSION['userid'])     ? (int)$_SESSION['userid']     : 0;

$api_response = array(
    'status'          => 'error',
    'message'         => 'Session company_id or user_id is missing',
    'total'           => 0,
    'employees_count' => 0,
    'data'            => array()
);

if ($company_id > 0 && $user_id > 0) {
    $url          = API . "?method=get&mode=getManagerTeamLeaves&company_id=" . $company_id . "&user_id=" . $user_id;
    $raw_response = callAPI($url);
    $decoded      = json_decode($raw_response, true);
    if (is_array($decoded)) {
        $api_response = $decoded;
    }
}

$rows            = isset($api_response['data']) && is_array($api_response['data']) ? $api_response['data'] : array();
$total           = isset($api_response['total'])           ? (int)$api_response['total']           : count($rows);
$employees_count = isset($api_response['employees_count']) ? (int)$api_response['employees_count'] : 0;
$message         = isset($api_response['message'])         ? (string)$api_response['message']      : '';

function mlFormatDate($value) {
    if (empty($value)) { return '-';
}
    $ts = strtotime(substr($value, 0, 10));
    return $ts ? date('d-m-Y', $ts) : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function mlStatusBadge($status) {
    $badge = '<span class="badge badge-secondary">'.__('Unknown').'</span>';
    $s = (string)$status;
    if ($s === '0') {
        $badge = '<span class="badge badge-warning">'.__('Pending').'</span>';
    } elseif ($s === '199') {
        $badge = '<span class="badge badge-success">'.__('Approved').'</span>';
    } elseif ($s === '30' || $s === '20' || $s === '201') {
        $badge = '<span class="badge badge-danger">'.__('Rejected').'</span>';
    } elseif ($s === '40') {
        $badge = '<span class="badge badge-secondary">'.__('Cancelled').'</span>';
    }
    return $badge;
}
?>

<link rel="stylesheet" href="assets/css/custom_manager_team_leaves.css">

<div class="container-fluid manager-leaves-page py-3">
    <div class="manager-leaves-shell card">
        <div class="card-header manager-leaves-header">
            <div>
                <h4 class="mb-1"><?= __('Manager Team Leaves'); ?></h4>
                <p class="mb-0 text-muted"><?= __('Direct reports by approval manager with active leave requests'); ?></p>
            </div>
            <a href="javascript:history.go(-1)" class="btn btn-outline-secondary btn-sm">
                <i class="icofont icofont-reply"></i> <?= __('Back'); ?>
            </a>
        </div>

        <div class="card-body">
            <div class="manager-leaves-stats">
                <div class="stat-tile">
                    <div class="stat-label"><?= __('Team Employees'); ?></div>
                    <div class="stat-value"><?php echo $employees_count; ?></div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label"><?= __('Leave Rows'); ?></div>
                    <div class="stat-value" id="stat-total"><?php echo $total; ?></div>
                </div>

            </div>

            <div class="manager-leaves-filters">
                <div class="row g-2">
                    <div class="col-lg-4 col-md-6">
                        <input aria-label="<?= htmlspecialchars(__('Search by employee, code, leave Type, Special Request'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="filter-keyword" class="form-control" placeholder="<?= htmlspecialchars(__('Search by employee, code, leave Type, Special Request'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <input aria-label="<?= htmlspecialchars(__('Leave from date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" id="filter-from" class="form-control" title="<?= htmlspecialchars(__('Leave from date'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <input aria-label="<?= htmlspecialchars(__('Leave to date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" id="filter-to" class="form-control" title="<?= htmlspecialchars(__('Leave to date'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-lg-2 col-md-12 d-grid">
                        <button class="btn btn-light border" type="button" data-action="clear-leave-filters"><?= __('Clear Filters'); ?></button>
                    </div>
                </div>
            </div>

            <div class="table-responsive manager-leaves-table-wrap">
                <table class="table table-hover manager-leaves-table" id="manager-team-leaves-table">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Employee'); ?></th>
                            <th scope="col"><?= __('Emp Code'); ?></th>
                            <th scope="col"><?= __('Leave Type'); ?></th>
                            <th scope="col"><?= __('Leave From'); ?></th>
                            <th scope="col"><?= __('Leave To'); ?></th>
                            <th scope="col"><?= __('Days'); ?></th>
                            <th scope="col"><?= __('Applied On'); ?></th>
                            <th scope="col"><?= __('Status'); ?></th>
                            <th scope="col"><?= __('Special Request'); ?></th>
                            <th scope="col"><?= __('Action'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="manager-team-leaves-tbody">
                        <?php if (!empty((array)$rows)) { ?>
                            <?php foreach ((array)$rows as $row) { ?>
                                <?php
                                    $r_name      = isset($row['display_name'])     ? $row['display_name']     : '';
                                    $r_code      = isset($row['emp_code'])         ? $row['emp_code']         : '';
                                    $r_type      = isset($row['leave_type_name'])  ? $row['leave_type_name']  : 'N/A';
                                    $r_color     = isset($row['leave_type_color']) ? $row['leave_type_color'] : '';
                                    $r_from      = isset($row['leave_from'])       ? substr($row['leave_from'], 0, 10)       : '';
                                    $r_to        = isset($row['leave_to'])         ? substr($row['leave_to'], 0, 10)         : '';
                                    $r_days      = isset($row['leave_days'])       ? $row['leave_days']       : '-';
                                    $r_appdate   = isset($row['application_date']) ? $row['application_date'] : '';
                                    $r_status    = isset($row['current_status'])   ? $row['current_status']   : '';
                                    $r_narrative = '-';
                                    if (isset($row['special_request']) && $row['special_request'] !== '') {
                                        $r_narrative = $row['special_request'];
                                    } elseif (isset($row['details'])) {
                                        $r_narrative = $row['details'];
                                    }
                                    $r_search    = strtolower($r_name . ' ' . $r_code . ' ' . $r_type . ' ' . $r_narrative);
                                    $r_chip_style = $r_color ? ' style="background:' . htmlspecialchars($r_color, ENT_QUOTES, 'UTF-8') . ';color:#fff;"' : '';
                                ?>
                                <tr
                                    data-search="<?php echo htmlspecialchars($r_search, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-from="<?php echo htmlspecialchars($r_from, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-to="<?php echo htmlspecialchars($r_to, ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                    <td><?php echo htmlspecialchars($r_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($r_code, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="leave-type-chip"<?php echo $r_chip_style; ?>>
                                            <?php echo htmlspecialchars($r_type, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo mlFormatDate($r_from); ?></td>
                                    <td><?php echo mlFormatDate($r_to); ?></td>
                                    <td><?php echo htmlspecialchars((string)$r_days, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo mlFormatDate($r_appdate); ?></td>
                                    <td><?php echo mlStatusBadge($r_status); ?></td>
                                    <td title="<?php echo htmlspecialchars($r_narrative, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($r_narrative, ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <div class="manager-leaves-actions">
                                            <button
                                                type="button"
                                                class="btn btn-success btn-sm"
                                                data-action="team-leave-status"
                                                data-status="199"
                                                data-empid="<?php echo (int)$row['employee_id']; ?>"
                                                data-leaveid="<?php echo (int)$row['employee_leave_id']; ?>"
                                            ><?= __('Approve'); ?></button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                data-action="team-leave-status"
                                                data-status="30"
                                                data-empid="<?php echo (int)$row['employee_id']; ?>"
                                                data-leaveid="<?php echo (int)$row['employee_leave_id']; ?>"
                                            ><?= __('Reject'); ?></button>
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-action="team-leave-status"
                                                data-status="40"
                                                data-empid="<?php echo (int)$row['employee_id']; ?>"
                                                data-leaveid="<?php echo (int)$row['employee_leave_id']; ?>"
                                            ><?= __('Cancle'); ?></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="manager-team-leaves-empty" class="manager-leaves-empty <?php echo empty((array)$rows) ? '' : 'd-none'; ?>">
                <?= __('No leave records found.'); ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_manager_team_leaves.js"></script>
