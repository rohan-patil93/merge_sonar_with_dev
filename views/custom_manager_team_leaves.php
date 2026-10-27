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
    $s = (string)$status;
    if ($s === '0')   { return '<span class="badge badge-warning">Pending</span>';
}
    if ($s === '199') { return '<span class="badge badge-success">Approved</span>';
}
    if ($s === '30' || $s === '20' || $s === '201') { return '<span class="badge badge-danger">Rejected</span>';
}
    if ($s === '40') { return '<span class="badge badge-secondary">Cancelled</span>';
}
    return '<span class="badge badge-secondary">Unknown</span>';
}
?>

<link rel="stylesheet" href="assets/css/custom_manager_team_leaves.css">

<div class="container-fluid manager-leaves-page py-3">
    <div class="manager-leaves-shell card">
        <div class="card-header manager-leaves-header">
            <div>
                <h4 class="mb-1">Manager Team Leaves</h4>
                <p class="mb-0 text-muted">Direct reports by approval manager with active leave requests</p>
            </div>
            <a href="javascript:history.go(-1)" class="btn btn-outline-secondary btn-sm">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>

        <div class="card-body">
            <div class="manager-leaves-stats">
                <div class="stat-tile">
                    <div class="stat-label">Team Employees</div>
                    <div class="stat-value"><?php echo $employees_count; ?></div>
                </div>
                <div class="stat-tile">
                    <div class="stat-label">Leave Rows</div>
                    <div class="stat-value" id="stat-total"><?php echo $total; ?></div>
                </div>

            </div>

            <div class="manager-leaves-filters">
                <div class="row g-2">
                    <div class="col-lg-4 col-md-6">
                        <input aria-label="Search by employee, code, leave Type, Special Request" type="text" id="filter-keyword" class="form-control" placeholder="Search by employee, code, leave Type, Special Request">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <input aria-label="Leave from date" type="date" id="filter-from" class="form-control" title="Leave from date">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <input aria-label="Leave to date" type="date" id="filter-to" class="form-control" title="Leave to date">
                    </div>
                    <div class="col-lg-2 col-md-12 d-grid">
                        <button class="btn btn-light border" type="button" data-action="clear-leave-filters">Clear Filters</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive manager-leaves-table-wrap">
                <table class="table table-hover manager-leaves-table" id="manager-team-leaves-table">
                    <thead>
                        <tr>
                            <th scope="col">Employee</th>
                            <th scope="col">Emp Code</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Leave From</th>
                            <th scope="col">Leave To</th>
                            <th scope="col">Days</th>
                            <th scope="col">Applied On</th>
                            <th scope="col">Status</th>
                            <th scope="col">Special Request</th>
                            <th scope="col">Action</th>
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
                                    $r_narrative = isset($row['special_request']) && $row['special_request'] !== ''
                                                    ? $row['special_request']
                                                    : (isset($row['details']) ? $row['details'] : '-');
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
                                            >Approve</button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                data-action="team-leave-status"
                                                data-status="30"
                                                data-empid="<?php echo (int)$row['employee_id']; ?>"
                                                data-leaveid="<?php echo (int)$row['employee_leave_id']; ?>"
                                            >Reject</button>
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-action="team-leave-status"
                                                data-status="40"
                                                data-empid="<?php echo (int)$row['employee_id']; ?>"
                                                data-leaveid="<?php echo (int)$row['employee_leave_id']; ?>"
                                            >Cancle</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="manager-team-leaves-empty" class="manager-leaves-empty <?php echo empty((array)$rows) ? '' : 'd-none'; ?>">
                No leave records found.
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_manager_team_leaves.js"></script>
