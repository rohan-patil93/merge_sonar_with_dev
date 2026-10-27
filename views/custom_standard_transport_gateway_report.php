<?php
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['datepicker'] = '1';
$_SESSION['apply_js']['multiselest'] = 1;

$reportRows = array();
$employeeOptions = array();
$vehicleOptions = array();
$errorMessage = '';
$successMessage = '';

$from_date = $_POST['from_date'] ?? date('Y-m-d');
$from_time = $_POST['from_time'] ?? '00:00';
$to_date = $_POST['to_date'] ?? date('Y-m-d');
$to_time = $_POST['to_time'] ?? date('H:i');
$vehicle_number = $_POST['vehicle_number'] ?? '';
$employee_id = $_POST['employee_id'] ?? '';

$masterUrl = API
    . "?method=get&mode=gettransportgatewayreport"
    . "&from_date=" . urlencode($from_date)
    . "&from_time=" . urlencode($from_time)
    . "&to_date=" . urlencode($to_date)
    . "&to_time=" . urlencode($to_time)
    . "&company_id=" . urlencode($_SESSION['company_id']);
$masterApiResponse = callAPI($masterUrl);
$masterResult = json_decode($masterApiResponse, true);
if (is_array($masterResult)) {
    $employeeOptions = $masterResult['employees'] ?? array();
    $vehicleOptions = $masterResult['vehicles'] ?? array();
}

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    if ($from_date == '' || $from_time == '' || $to_date == '' || $to_time == '') {
        $errorMessage = 'Please fill all date and time fields.';
    } else {
        $fromDateTime = strtotime($from_date . ' ' . $from_time);
        $toDateTime = strtotime($to_date . ' ' . $to_time);

        if ($fromDateTime == false || $toDateTime == false) {
            $errorMessage = 'Invalid date/time format.';
        } elseif ($fromDateTime > $toDateTime) {
            $errorMessage = 'From date time should be less than or equal to To date time.';
        } else {
            $url = API
                . "?method=get&mode=gettransportgatewayreport"
                . "&from_date=" . urlencode($from_date)
                . "&from_time=" . urlencode($from_time)
                . "&to_date=" . urlencode($to_date)
                . "&to_time=" . urlencode($to_time)
                . "&company_id=" . urlencode($_SESSION['company_id'])
                . "&vehicle_number=" . urlencode($vehicle_number)
                . "&employee_id=" . urlencode($employee_id);
            $apiResponse = callAPI($url);
            $result = json_decode($apiResponse, true);

            if (is_array($result)) {
                $employeeOptions = $result['employees'] ?? $employeeOptions;
                $vehicleOptions = $result['vehicles'] ?? $vehicleOptions;
            }

            if (is_array($result) && ($result['status'] ?? '') === 'success') {
                $reportRows = $result['data'] ?? array();
                if (!is_array($reportRows)) {
                    $reportRows = array();
                }
                if (empty($reportRows)) {
                    $successMessage = 'No data found for selected date time range.';
                }
            } else {
                $errorMessage = $result['message'] ?? 'Unable to fetch report data.';
            }
        }
    }
}
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Transport Gateway Report</h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                        More
                    </button>
                    <div class="dropdown-menu p-0">
                        <a class="dropdown-item" href="custom_view_download.php?page=TransportGatewayReport&format=csv" target="_blank">CSV</a>

                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <span class="form-label">From Date</span>
                        <input aria-label="From Date" type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <span class="form-label">From Time</span>
                        <input aria-label="From Time" type="time" name="from_time" class="form-control" value="<?php echo htmlspecialchars($from_time, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <span class="form-label">To Date</span>
                        <input aria-label="To Date" type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <span class="form-label">To Time</span>
                        <input aria-label="To Time" type="time" name="to_time" class="form-control" value="<?php echo htmlspecialchars($to_time, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <span class="form-label">Vehicle</span>
                        <select aria-label="Vehicle Number" class="form-control js-example-basic-single" name="vehicle_number" >
                            <option value="">All Vehicles</option>
                            <?php foreach ($vehicleOptions as $vehicle) {
                                $vehicleNo = (string)($vehicle['vehicle_reg_no'] ?? '');
                                if ($vehicleNo == '') {
                                    continue;
                                }
                                $modelGroup = (string)($vehicle['model_group'] ?? '');
                                $model = (string)($vehicle['model'] ?? '');
                                $vehicleLabel = $vehicleNo;
                                if ($modelGroup != '' || $model != '') {
                                    $vehicleLabel .= ' - ' . trim($modelGroup . ' - ' . $model, ' -');
                                }
                                ?>
                                <option value="<?php echo htmlspecialchars($vehicleNo, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($vehicle_number == $vehicleNo) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($vehicleLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee" class="form-control js-example-basic-single" name="employee_id" >
                            <option value="">All Employees</option>
                            <?php foreach ($employeeOptions as $employee) {
                                $empId = (string)($employee['id'] ?? '');
                                if ($empId == '') {
                                    continue;
                                }
                                $empCode = (string)($employee['emp_code'] ?? '');
                                $empName = (string)($employee['display_name'] ?? '');
                                $employeeLabel = trim($empCode . ' - ' . $empName, ' -');
                                ?>
                                <option value="<?php echo htmlspecialchars($empId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($employee_id == $empId) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($employeeLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" name="applyform" value="apply" class="btn btn-info">Search</button>
                    </div>
                </div>
            </form>

            <?php if ($errorMessage != '') { ?>
                <div class="alert alert-danger mt-3 mb-0"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <?php if ($successMessage != '') { ?>
                <div class="alert alert-info mt-3 mb-0"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>
        </div>
    </div>

    <?php if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply' && $errorMessage == '') { ?>
        <div class="card">
            <div class="card-body custom-scroll" style="max-height: 600px !important;">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Sl No</th>
                                <th scope="col">Vehicle Number</th>
                                <th scope="col">Gate-OutDate-Time</th>
                                <th scope="col">Gate-InDate-Time</th>
                                <th scope="col">Gate-Out Purpose</th>
                                <th scope="col">Driver/Person Name</th>
                                <th scope="col">Other Members(If Any)</th>
                                <th scope="col">Order By</th>
                                <th scope="col">Location</th>
                                <th scope="col">Panel Name</th>
                                <th scope="col">Out-Reading</th>
                                <th scope="col">In-Reading</th>
                                <th scope="col">Drive Duration</th>
                                <th scope="col">Drive Running</th>
                                <th scope="col">Remark</th>
                                <th scope="col">Method Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $csvData = array();
                            if (!empty($reportRows)) {
                                $sl = 1;
                                foreach ($reportRows as $row) {
                                    $hasEntryDateTime = (($row['entry_date'] ?? '') != '' && ($row['entry_date'] ?? '') != '0000-00-00' && ($row['entry_time'] ?? '') != '' && ($row['entry_time'] ?? '') != '00:00:00');
                                    $hasExitDateTime = (($row['exit_date'] ?? '') != '' && ($row['exit_date'] ?? '') != '0000-00-00' && ($row['exit_time'] ?? '') != '' && ($row['exit_time'] ?? '') != '00:00:00');

                                    $entryDateTime = '-';
                                    if ($hasEntryDateTime) {
                                        $entryDateTime = fromDate($row['entry_date']) . ' ' . substr($row['entry_time'], 0, 5);
                                    }

                                    $exitDateTime = '-';
                                    if ($hasExitDateTime) {
                                        $exitDateTime = fromDate($row['exit_date']) . ' ' . substr($row['exit_time'], 0, 5);
                                    }


                                    $purposeDisplay = ucwords(strtolower(str_replace('_', ' ', (string)($row['visit_purpose'] ?? ''))));
                                    $methodTypeDisplay = ucwords(strtolower(str_replace('_', ' ', (string)($row['method_type'] ?? ''))));

                                    // Drive Duration Calculation
                                $driveDuration = '00:00';
                                if ($hasEntryDateTime && $hasExitDateTime) {
                                    $entryTimestamp = strtotime($row['entry_date'] . ' ' . $row['entry_time']);
                                    $exitTimestamp = strtotime($row['exit_date'] . ' ' . $row['exit_time']);

                                    if ($entryTimestamp && $exitTimestamp && $entryTimestamp >= $exitTimestamp) {
                                        $diffSeconds = $entryTimestamp - $exitTimestamp;

                                        $hours = floor($diffSeconds / 3600);
                                        $minutes = floor(($diffSeconds % 3600) / 60);

                                        $driveDuration = sprintf('%02d:%02d', $hours, $minutes);
                                    }
                                }

                                // Drive Running Calculation
                                $driveRunning = 0;
                                $outReading = $row['vehicle_exit_reading'] ?? '';
                                $inReading = $row['vehicle_entry_reading'] ?? '';

                                if ($outReading != '' && $inReading != '' && is_numeric($outReading) && is_numeric($inReading) && $inReading >= $outReading) {
                                        $driveRunning = $inReading - $outReading;
                                    }

                                    $additionalEmployees = $row['additional_employees'] ?? '';
                                    $additionalEmployees = str_replace('"', '""', $additionalEmployees); // escape quotes

                                    $csvData[] = array(
                                        'Sl No' => $sl,
                                        'Vehicle Number' => $row['vehicle_number'] ?? '',
                                        'Gate-OutDate-Time' => $exitDateTime,
                                        'Gate-InDate-Time' => $entryDateTime,
                                        'Gate-Out Purpose' => $purposeDisplay,
                                        'Driver/Person Name' => $row['employee_name'] ?? ($row['driver_name'] ?? ''),
                                        'Other Members(If Any)' => '"' . $additionalEmployees . '"',
                                        'Order By' => $row['created_by_name'],
                                        'Location' => $row['location'] ?? '',
                                        'Panel Name' => '-',
                                        'Out-Reading' => $row['vehicle_exit_reading'] ?? '',
                                        'In-Reading' => $row['vehicle_entry_reading'] ?? '',
                                        'Drive Duration' => $driveDuration,
                                        'Drive Running' => $driveRunning,
                                        'Remark' => $row['notes'] ?? '',
                                        'Method Type' => $methodTypeDisplay
                                    );
                                    ?>
                                    <tr>
                                        <td><?php echo $sl; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($exitDateTime, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($entryDateTime, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($purposeDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['employee_name'] ?? ($row['driver_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['additional_employees'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_by_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo  '-'; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_exit_reading'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_entry_reading'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($driveDuration, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($driveRunning, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($methodTypeDisplay, ENT_QUOTES, 'UTF-8'); ?></td>

                                    </tr>
                                    <?php
                                    $sl++;
                                }
                                $_SESSION['download_data'] = $csvData;
                                $_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
                                $_SESSION['csv_header']['title'] = 'Transport Gateway Report';
                                $_SESSION['csv_header']['date_time'] = date('d-m-Y H:i:s');
                            } else {
                                ?>
                                <tr>
                                    <td colspan="16" class="text-center">No Data Available</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
