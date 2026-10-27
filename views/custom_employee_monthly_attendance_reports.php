<?php
$_SESSION['apply_js']['multiselest'] = 1;

$session_employee_key = 'attendance_report_employee_id';
$session_month_key = 'attendance_report_month';

// Fetch employees
$url = API . "?mode=getEmployeesAttendanceData&method=get";
$result = callAPI($url);
$result = json_decode($result, true);
//echo "<pre>"; print_r($result); echo "<pre>";
$employees = $result['table'] ?? array();

$employee_id = $_SESSION[$session_employee_key] ?? '';
$report_month = $_SESSION[$session_month_key] ?? '';

if (isset($_GET['employee_id']) || isset($_GET['month'])) {
    $get_employee_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
    $get_month = isset($_GET['month']) ? trim($_GET['month']) : '';

    if ($get_employee_id > 0 && $get_month !== '') {
        $_SESSION[$session_employee_key] = $get_employee_id;
        $_SESSION[$session_month_key] = $get_month;
        $employee_id = $get_employee_id;
        $report_month = $get_month;
    } else {
        unset($_SESSION[$session_employee_key], $_SESSION[$session_month_key]);
        $employee_id = 0;
        $report_month = '';
    }
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Employee Monthly Report</h5>
        </div>
        <div class="card-body p-4">
            <form method="get" id="attendanceReportForm">
                <input type="hidden" name="option" value="COM_EMPLOYEE_MONTHLY_ATTENDANCE_REPORTS">
                <div class="row">
                    <!-- Employee -->
                    <div class="form-group col-md-4">
                        <label>Employee <span style="color:red;">*</span></label>
                        <select class="form-select js-example-basic-single" id="employee_id" name="employee_id">
                            <option value="0">Select Employee</option>
                            <?php foreach ($employees as $row): ?>
                                <option value="<?= $row['id']; ?>" <?= ((int)$row['id'] === (int)$employee_id) ? 'selected' : ''; ?>>
                                    <?= $row['display_name'].' ('. $row['emp_code'].')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="form-group col-md-3">
                        <label>Month <span style="color:red;">*</span></label>
                        <input type="month" class="form-control" id="report_month" name="month" value="<?= htmlspecialchars($report_month, ENT_QUOTES); ?>">
                    </div>

                    <!-- Search -->
                    <div class="form-group col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control" id="searchBtn">
                            Search
                        </button>
                    </div>

                </div>
            </form>

            <!-- Result -->
            
        </div>
    </div>
    <div class="mt-4" id="TableContent">
        <?php if (!empty($employee_id) && !empty($report_month)): ?>
            <?php include __DIR__ . '/custom_employee_monthly_attendance_report_table.php'; ?>
        <?php endif; ?>
    </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_employee_monthly_attendance_reports.js"></script>
<script src="assets/js/custom_employee_monthly_attendance_report_table.js"></script>
