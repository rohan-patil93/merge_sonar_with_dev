<?php
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['multiselest'] = 1;

$company_id = (int) ($_SESSION['company_id'] ?? 0);

$request_data = $_POST;
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    $request_data = $_GET;
}

$filters = array(
    'employee_id' => isset($request_data['employee_id']) ? trim((string) $request_data['employee_id']) : '',
    'department_id' => isset($request_data['department_id']) ? trim((string) $request_data['department_id']) : '',
    'designation_id' => isset($request_data['designation_id']) ? trim((string) $request_data['designation_id']) : '',
    'location_id' => isset($request_data['location_id']) ? trim((string) $request_data['location_id']) : '',
    'employee_type' => isset($request_data['employee_type']) ? trim((string) $request_data['employee_type']) : '',
    'employee_status' => isset($request_data['employee_status']) ? trim((string) $request_data['employee_status']) : '',
    'joining_date_from' => isset($request_data['joining_date_from']) ? trim((string) $request_data['joining_date_from']) : '',
    'joining_date_to' => isset($request_data['joining_date_to']) ? trim((string) $request_data['joining_date_to']) : '',
    'relieving_date_from' => isset($request_data['relieving_date_from']) ? trim((string) $request_data['relieving_date_from']) : '',
    'relieving_date_to' => isset($request_data['relieving_date_to']) ? trim((string) $request_data['relieving_date_to']) : ''
);

$query_params = array_merge(
    array(
        'method' => 'get',
        'mode' => 'getEmployeeReportData',
        'company_id' => $company_id
    ),
    $filters
);

$api_url = API . '?' . http_build_query($query_params);
$api_response = callAPI($api_url);
$api_data = json_decode((string) $api_response, true);

$employees = $api_data['employees'] ?? array();
$departments = $api_data['departments'] ?? array();
$designations = $api_data['designations'] ?? array();
$locations = $api_data['locations'] ?? array();
$employee_type_map = $api_data['employee_type_map'] ?? array(1 => 'Type 1', 2 => 'Type 2');
$report_rows = $api_data['report_rows'] ?? array();

$error_message = '';
if (!is_array($api_data) || (($api_data['status'] ?? '') === 'error')) {
    $error_message = $api_data['message'] ?? 'Unable to fetch employee report data.';
}

function empReportDateDisplay($date_value)
{
    if ($date_value === null) {
        return '-';
    }
    $date_value = trim((string) $date_value);
    if ($date_value === '' || $date_value === '0000-00-00') {
        return '-';
    }

    $time = strtotime($date_value);
    if ($time === false) {
        return $date_value;
    }
    return date('d-m-Y', $time);
}

function empReportCsvSafe($value)
{
    $value = (string) $value;
    $value = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $value);
    $value = preg_replace('/\s{2,}/', ' ', $value);
    return trim((string) $value);
}

$employee_report_css_v = @filemtime(__DIR__ . '/../assets/css/custom_view_employee_report.css');
$employee_report_js_v = @filemtime(__DIR__ . '/../assets/js/custom_view_employee_report.js');
if ($employee_report_css_v === false) {
    $employee_report_css_v = time();
}
if ($employee_report_js_v === false) {
    $employee_report_js_v = time();
}
?>

<link rel="stylesheet" href="assets/css/custom_view_employee_report.css?v=<?php echo (int) $employee_report_css_v; ?>">

<div class="page-body employee-report-page">
    <div class="employee-report-backdrop"></div>

    <div class="card employee-report-card">
        <div class="card-header employee-report-header">
            <div>
                <h5 class="mb-1">Employee Report</h5>
                <p class="mb-0 text-muted">Apply optional filters and export the same results as CSV.</p>
            </div>
            <div class="employee-report-actions">
                <a class="btn btn-outline-primary" href="custom_view_download.php?page=EmployeeReport&format=csv" target="_blank">Download CSV</a>
            </div>
        </div>

        <div class="card-body">
            <form method="post"  class="employee-report-form">

                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee" name="employee_id" class="form-control js-employee-report-select">
                            <option value="">All Employees</option>
                            <?php foreach ((array) $employees as $employee) {
                                $emp_id = (string) ($employee['id'] ?? '');
                                if ($emp_id === '') {
                                    continue;
                                }
                                $emp_name = trim((string) ($employee['display_name'] ?? ''));
                                $emp_code = trim((string) ($employee['emp_code'] ?? ''));
                                $label = trim($emp_name . ' (' . $emp_code . ')', ' ()');
                                ?>
                                <option value="<?php echo htmlspecialchars($emp_id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['employee_id'] === $emp_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Department</span>
                        <select aria-label="Department" name="department_id" class="form-control js-employee-report-select">
                            <option value="">All Departments</option>
                            <?php foreach ((array) $departments as $department) {
                                $department_id = (string) ($department['id'] ?? '');
                                if ($department_id === '') {
                                    continue;
                                }
                                ?>
                                <option value="<?php echo htmlspecialchars($department_id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['department_id'] === $department_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars((string) ($department['department_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Designation</span>
                        <select aria-label="Designation" name="designation_id" class="form-control js-employee-report-select">
                            <option value="">All Designations</option>
                            <?php foreach ((array) $designations as $designation) {
                                $designation_id = (string) ($designation['id'] ?? '');
                                if ($designation_id === '') {
                                    continue;
                                }
                                ?>
                                <option value="<?php echo htmlspecialchars($designation_id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['designation_id'] === $designation_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars((string) ($designation['designation_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Location</span>
                        <select aria-label="Location" name="location_id" class="form-control js-employee-report-select">
                            <option value="">All Locations</option>
                            <?php foreach ((array) $locations as $location) {
                                $location_id = (string) ($location['id'] ?? '');
                                if ($location_id === '') {
                                    continue;
                                }
                                ?>
                                <option value="<?php echo htmlspecialchars($location_id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['location_id'] === $location_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars((string) ($location['location_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Employee Type</span>
                        <select aria-label="Employee Type" name="employee_type" class="form-control js-employee-report-select">
                            <option value="">All Types</option>
                            <?php foreach ((array) $employee_type_map as $type_value => $type_label) {
                                $type_value = (string) $type_value;
                                ?>
                                <option value="<?php echo htmlspecialchars($type_value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['employee_type'] === $type_value) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars((string) $type_label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Active / Resigned</span>
                        <select aria-label="Employee Status" name="employee_status" class="form-control js-employee-report-select">
                            <option value="">All</option>
                            <option value="active" <?php echo ($filters['employee_status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="resigned" <?php echo ($filters['employee_status'] === 'resigned') ? 'selected' : ''; ?>>Resigned</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Join Date From</span>
                        <input aria-label="Joining Date From" type="date" name="joining_date_from" value="<?php echo htmlspecialchars($filters['joining_date_from'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Join Date To</span>
                        <input aria-label="Joining Date To" type="date" name="joining_date_to" value="<?php echo htmlspecialchars($filters['joining_date_to'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Relieving Date From</span>
                        <input aria-label="Relieving Date From" type="date" name="relieving_date_from" value="<?php echo htmlspecialchars($filters['relieving_date_from'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <span class="form-label">Relieving Date To</span>
                        <input aria-label="Relieving Date To" type="date" name="relieving_date_to" value="<?php echo htmlspecialchars($filters['relieving_date_to'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                    </div>
                </div>

                <div id="employee-report-validation" class="alert alert-warning mt-3 mb-0 d-none"></div>

                <div class="employee-report-form-actions mt-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <button type="button" class="btn btn-light" data-action="reset-employee-report">Reset</button>
                </div>
            </form>

            <?php if ($error_message !== '') { ?>
                <div class="alert alert-danger mt-3 mb-0"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="card employee-report-card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover employee-report-table">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Emp Code</th>
                            <th scope="col">Emp Display Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Employee Type</th>
                            <th scope="col">Department</th>
                            <th scope="col">Designation</th>
                            <th scope="col">Location</th>
                            <th scope="col">Active/Resigned</th>
                            <th scope="col">Joining Date</th>
                            <th scope="col">Relieving Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $csv_data = array();
                        if (count((array) $report_rows) > 0) {
                            $serial_no = 1;
                            foreach ((array) $report_rows as $row) {
                                $csv_data[] = array(
                                    'S.No' => $serial_no,
                                    'Emp Code' => empReportCsvSafe($row['emp_code'] ?? ''),
                                    'Emp Display Name' => empReportCsvSafe($row['display_name'] ?? ''),
                                    'Phone' => empReportCsvSafe($row['phone'] ?? ''),
                                    'Employee Type' => empReportCsvSafe($row['employee_type_label'] ?? ''),
                                    'Department' => empReportCsvSafe($row['department'] ?? ''),
                                    'Designation' => empReportCsvSafe($row['designation'] ?? ''),
                                    'Location' => empReportCsvSafe($row['location'] ?? ''),
                                    'Active/Resigned' => empReportCsvSafe($row['employee_status'] ?? ''),
                                    'Joining Date' => empReportCsvSafe(empReportDateDisplay($row['joining_date'] ?? '')),
                                    'Relieving Date' => empReportCsvSafe(empReportDateDisplay($row['date_of_leaving'] ?? ''))
                                );
                                ?>
                                <tr>
                                    <td><?php echo $serial_no; ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['emp_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['display_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['employee_type_label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['department'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['designation'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($row['location'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="badge <?php echo (strtolower((string) ($row['employee_status'] ?? '')) === 'active') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            <?php echo htmlspecialchars((string) ($row['employee_status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(empReportDateDisplay($row['joining_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars(empReportDateDisplay($row['date_of_leaving'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php
                                $serial_no++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="11" class="text-center">No employee records found.</td>
                            </tr>
                            <?php
                        }

                        $_SESSION['download_data'] = $csv_data;
                        $_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
                        $_SESSION['csv_header']['title'] = 'Employee Report';
                        $_SESSION['csv_header']['date_time'] = date('d-m-Y H:i:s');
                        $_SESSION['csv_header']['name'] = $_SESSION['display_name'] ?? '';
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_view_employee_report.js?v=<?php echo (int) $employee_report_js_v; ?>"></script>
