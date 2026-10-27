<?php
session_start();
$_SESSION['apply_js']['multiselest'] = 1;

if (!function_exists('h')) {
    function h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'returnonly') {
    $url = API . "?mode=saveEmployeeExitOnlyReturn&method=post";
    $resp = file_post_contents($url, $_POST, 'returnonly');
    $postResult = json_decode($resp, true);

    if (!empty($postResult) && ($postResult['status'] ?? '') === 'success') {
        $message = 'Return submitted successfully';
        $messageType = 'success';
    } else {
        $message = $postResult['message'] ?? 'Return submission failed';
        $messageType = 'error';
    }
}

$selectedEmployeeId = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
$selectedOutDate = trim($_GET['out_date'] ?? '');
$viewId = isset($_GET['view_id']) ? (int)$_GET['view_id'] : 0;

$query = [
    'mode' => 'getEmployeeExitOnlyReturnData',
    'method' => 'get',
];
if ($selectedEmployeeId > 0) {
    $query['employee_id'] = $selectedEmployeeId;
}
if ($selectedOutDate !== '') {
    $query['out_date'] = $selectedOutDate;
}

$apiUrl = API . '?' . http_build_query($query);
$result = callAPI($apiUrl);
$response = json_decode($result, true);

$employees = $response['employees'] ?? array();
$list = $response['list'] ?? array();
$luggageMap = $response['luggage_map'] ?? array();
$buildingGates = $response['building_gates'] ?? array();
$activeOutCount = (int)($response['active_out_count'] ?? 0);
$apiStatus = $response['status'] ?? 'error';
$apiMessage = $response['message'] ?? '';

$summaryNote = 'Showing all active taken out records.';
if ($selectedEmployeeId > 0 && $selectedOutDate !== '') {
    $summaryNote = 'Showing active taken out records for selected employee and selected out date.';
} elseif ($selectedEmployeeId > 0) {
    $summaryNote = 'Showing active taken out records for selected employee.';
} elseif ($selectedOutDate !== '') {
    $summaryNote = 'Showing active taken out records for selected out date.';
}

$option = $_GET['option'] ?? '';

function buildSelfUrl($params, $option) {
    $base = ['option' => $option];
    if (!empty($params['employee_id'])) {
        $base['employee_id'] = (int)$params['employee_id'];
    }
    if (!empty($params['out_date'])) {
        $base['out_date'] = $params['out_date'];
    }
    if (!empty($params['view_id'])) {
        $base['view_id'] = (int)$params['view_id'];
    }
    return 'index.php?' . http_build_query($base);
}
?>

<link rel="stylesheet" href="assets/css/custom_visitor_exitonly_employee_return.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha384-PPIZEGYM1v8zp5Py7UjFb79S58UeqCL9pYVnVPURKEqvioPROaVAJKKLzvH2rDnI" crossorigin="anonymous">

<div class="card employee-return-card">
    <div class="card-header">
        <h5>Employee Exit Return Search</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>

    <div class="card-infisuite mt-2">
        <div class="card-body p-4">
            <div class="employee-return-toolbar">
                <form id="employeeExitReturnFilterForm" class="row g-3 align-items-end" method="get" action="index.php">
                    <input type="hidden" name="option" value="<?php echo h($option); ?>">
                    <div class="form-group col-lg-4 col-md-6 col-12">
                        <label for="return_employee_id">Employee</label>
                        <select class="form-select js-example-basic-single" name="employee_id" id="return_employee_id">
                            <option value="">All Employees</option>
                            <?php foreach ($employees as $emp): ?>
                                <?php $empId = (int)($emp['id'] ?? 0); ?>
                                <option value="<?php echo h($empId); ?>" <?php echo ($selectedEmployeeId === $empId) ? 'selected' : ''; ?>>
                                    <?php echo h(($emp['emp_code'] ?? '') . ' - ' . ($emp['display_name'] ?? '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-6 col-12">
                        <label for="return_out_date">Out Date</label>
                        <input type="date" class="form-control" name="out_date" id="return_out_date" value="<?php echo h($selectedOutDate); ?>">
                    </div>

                    <div class="form-group col-lg-5 col-12 employee-return-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                        <a href="<?php echo h(buildSelfUrl([], $option)); ?>" class="btn btn-outline-secondary" id="employeeExitReturnResetBtn">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="employee-return-summary" id="employeeExitReturnSummary">
                <div class="employee-return-summary-card">
                    <span class="summary-label">Active Taken Out Records</span>
                    <strong class="summary-value" id="employeeExitReturnCount"><?php echo h($activeOutCount); ?></strong>
                </div>
                <div class="employee-return-summary-note" id="employeeExitReturnSummaryNote"><?php echo h($summaryNote); ?></div>
            </div>

            <?php if ($apiStatus !== 'success'): ?>
                <div class="employee-return-feedback alert alert-danger" id="employeeExitReturnFeedback"><?php echo h($apiMessage ?: 'Unable to fetch data'); ?></div>
            <?php endif; ?>

            <?php if ($message !== ''): ?>
                <div class="alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo h($message); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive employee-return-table-wrap">
                <table class="table table-bordered table-hover align-middle employee-return-table">
                    <thead>
                        <tr>
                            <th scope="col">Employee Code</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Out Date Time</th>
                            <th scope="col">Out Gate No</th>
                            <th scope="col">Out Gate Security</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeeExitReturnTableBody">
                        <?php if (count((array)$list) == 0){ ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No active taken out records found.</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($list as $row){ ?>
                                <?php
                                    $rowId = (int)($row['id'] ?? 0);
                                    $isOpen = ($viewId === $rowId);
                                    $items = $luggageMap[$rowId] ?? ($luggageMap[(string)$rowId] ?? []);
                                    $toggleParams = [
                                        'employee_id' => $selectedEmployeeId,
                                        'out_date' => $selectedOutDate,
                                    ];
                                    if (!$isOpen) {
                                        $toggleParams['view_id'] = $rowId;
                                    }
                                    $toggleUrl = buildSelfUrl($toggleParams, $option);
                                    $gateDisplay = ($row['out_gate_name'] ?? '') !== '' ? $row['out_gate_name'] : ($row['out_gate_no'] ?? '-');
                                    $securityDisplay = ($row['out_security_name'] ?? '') !== '' ? $row['out_security_name'] : ($row['out_security_id'] ?? '-');
                                ?>
                                <tr class="employee-return-main-row" data-record-id="<?php echo h($rowId); ?>">
                                    <td><?php echo h($row['employee_code'] ?? '-'); ?></td>
                                    <td><?php echo h($row['employee_name'] ?? '-'); ?></td>
                                    <td><?php echo h($row['out_datetime'] ?? '-'); ?></td>
                                    <td><?php echo h($gateDisplay); ?></td>
                                    <td><?php echo h($securityDisplay); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo h($toggleUrl); ?>" class="btn btn-sm btn-outline-primary employee-return-toggle-btn <?php echo $isOpen ? 'is-open' : ''; ?>" aria-expanded="<?php echo $isOpen ? 'true' : 'false'; ?>">
                                            <?php if ($isOpen) { ?>
                                                <i class="fa-solid fa-eye-slash"></i>
                                            <?php } else { ?>
                                                <i class="fa-regular fa-eye"></i>
                                            <?php } ?>
                                        </a>
                                    </td>
                                </tr>

                                <?php if ($isOpen) { ?>
                                    <tr class="employee-return-detail-row" id="employee-return-row-<?php echo h($rowId); ?>">
                                        <td colspan="6">
                                            <form method="post" action="" class="employee-return-form">
                                                <input type="hidden" name="visitor_log_id" value="<?php echo h($rowId); ?>">
                                                <input type="hidden" name="company_id" value="<?php echo h($_SESSION['company_id'] ?? ''); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo h($_SESSION['userid'] ?? ''); ?>">
                                                <div class="employee-return-items-panel">
                                                    <div class="employee-return-items-title">Taken Out Items</div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-md-4 col-12">
                                                            <span class="form-label fw-semibold">Return Gate</span>
                                                            <select aria-label="Return Gate No" class="form-select js-example-basic-single" name="return_gate_no" id="return_gate_no_<?php echo h($rowId); ?>" required>
                                                                <?php foreach ($buildingGates as $gate) { ?>
                                                                    <option value="<?php echo h($gate->id ?? $gate['id'] ?? ''); ?>">
                                                                        <?php echo h($gate->name ?? $gate['name'] ?? ''); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php if (count((array)$items) == 0) { ?>
                                                        <div class="employee-return-empty-items">No taken out items found for this record.</div>
                                                    <?php } else { ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped mb-0 employee-return-items-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">#</th>
                                                                        <th scope="col">Item Name</th>
                                                                        <th scope="col">Brand</th>
                                                                        <th scope="col">Qty</th>
                                                                        <th scope="col">Serial / Remarks</th>
                                                                        <th scope="col">Returnable</th>
                                                                        <th scope="col">Return Date</th>
                                                                        <th scope="col">Return Remarks</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($items as $idx => $item) { ?>
                                                                        <?php $itemId = (int)($item['id'] ?? $item->id ?? 0); ?>
                                                                        <tr>
                                                                            <td><?php echo h($idx + 1); ?></td>
                                                                            <td><?php echo h($item['item_name'] ?? $item->item_name ?? '-'); ?></td>
                                                                            <td><?php echo h($item['brand_name'] ?? $item->brand_name ?? '-'); ?></td>
                                                                            <td><?php echo h($item['qty'] ?? $item->qty ?? '-'); ?></td>
                                                                            <td><?php echo h($item['serial_or_remarks'] ?? $item->serial_or_remarks ?? '-'); ?></td>
                                                                            <td><?php echo ((int)($item['returnable'] ?? $item->returnable ?? 0) === 1) ? 'Yes' : 'No'; ?></td>
                                                                            <td><?php echo h($item['return_date'] ?? $item->return_date ?? '-'); ?></td>
                                                                            <td>
                                                                                <textarea aria-label="Text area" class="form-control form-control-sm" name="item_remarks[<?php echo h($itemId); ?>]" rows="1" placeholder="Return remarks"></textarea>
                                                                                <input type="hidden" name="item_ids[]" value="<?php echo h($itemId); ?>">
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="row g-2 mt-3 mb-3">
                                                        <div class="col-12">
                                                            <span class="form-label fw-semibold">Overall Remarks</span>
                                                            <textarea aria-label="Overall Remarks" class="form-control" name="overall_remarks" id="overall_remarks_<?php echo h($rowId); ?>" rows="3" placeholder="Enter return remarks for this record"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-auto">
                                                            <button type="submit" name="submit" value="returnonly" class="btn btn-success btn-sm" id="return_submit_btn_<?php echo h($rowId); ?>">
                                                                <i class="fa-solid fa-check"></i> Return
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
