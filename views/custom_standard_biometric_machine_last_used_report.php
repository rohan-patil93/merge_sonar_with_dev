<?php
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['multiselest'] = 1;

$machineOptions = array();
$latestRowsByMachine = array();
$errorMessage = '';
$successMessage = '';
$machine_number = $_POST['machine_number'] ?? '';

$url = API
    . "?method=get&mode=getBiometricMachineLastUsedReport"
    . "&company_id=" . urlencode($_SESSION['company_id'])
    . "&machine_number=" . urlencode($machine_number);
$apiResponse = callAPI($url);
$result = json_decode($apiResponse, true);
$latestRowsByMachine = array();
if (count((array)$result)>0) {
    $machineOptions = $result['machines'] ?? array();
    if (($result['status'] ?? '') === 'success') {
        $latestRowsByMachine = $result['latest_rows_by_machine'] ?? array();
        if (count((array)$machineOptions) == 0) {
            $successMessage = 'No biometric machine usage data available.';
        }
    } else {
        $errorMessage = $result['message'] ?? 'Unable to fetch biometric machine report data.';
    }
} else {
    $errorMessage = 'Invalid response received from server.';
}
?>

<link rel="stylesheet" href="assets/css/custom_standard_biometric_machine_last_used_report.css">

<div class="page-body biometric-machine-last-used-report">
    <div class="card">
        <div class="card-header">
            <h5>Biometric Machine Last Used Report</h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                        More
                    </button>
                    <div class="dropdown-menu p-0">
                        <a class="dropdown-item" href="custom_view_download.php?page=BiometricMachineLastUsedReport&format=csv" target="_blank">CSV</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <span class="form-label">Machine</span>
                        <select aria-label="Machine Number" class="form-control js-biometric-machine-filter js-example-basic-single" name="machine_number">
                            <option value="">All Machines</option>
                            <?php foreach ($machineOptions as $machineOption) {
                                $optionMachineNumber = (string)($machineOption['MachineNumber'] ?? '');
                                if ($optionMachineNumber == '') {
                                    continue;
                                }
                                $optionMachineName = (string)($machineOption['biomatric_machine_name'] ?? '');
                                $optionLabel = trim($optionMachineName . ' - ' . $optionMachineNumber, ' -');
                                ?>
                                <option value="<?php echo htmlspecialchars($optionMachineNumber, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($machine_number == $optionMachineNumber) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($optionLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-info w-100">Search</button>
                    </div>
                </div>
            </form>

            <?php if ($errorMessage != '') { ?>
                <div class="alert alert-danger mt-3 mb-0"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <?php if ($successMessage != '' && $errorMessage == '') { ?>
                <div class="alert alert-info mt-3 mb-0"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover biometric-machine-last-used-table">
                    <thead>
                        <tr>
                            <th scope="col">Sl No</th>
                            <th scope="col">Machine Name</th>
                            <th scope="col">Machine Number</th>
                            <th scope="col">Location</th>
                            <th scope="col">InOutIndicator</th>
                            <th scope="col">Latest Date Time</th>
                            <th scope="col">Last Employee Code</th>
                            <th scope="col">Last Employee Name</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $csvData = array();
                        $tableData = array();
                        foreach ($machineOptions as $machineOption) {
                            $machineKey = (string)($machineOption['MachineNumber'] ?? '');
                            $latestRow = $latestRowsByMachine[$machineKey] ?? array();

                            $tableData[] = array(
                                'machine_name' => $machineOption['biomatric_machine_name'] ?? '',
                                'machine_number' => $machineOption['MachineNumber'] ?? '',
                                'location' => $machineOption['Location'] ?? '',
                                'InOutIndicator' => $machineOption['InOutIndicator'] == 'O' ? "OUT":"IN",
                                'clock_in_date' => $latestRow['clock_in_date'] ?? ($latestRow['attendace_date'] ?? ''),
                                'clock_in' => $latestRow['clock_in'] ?? '',
                                'clock_out_date' => $latestRow['clock_out_date'] ?? ($latestRow['attendace_date'] ?? ''),
                                'clock_out' => $latestRow['clock_out'] ?? '',
                                'last_employee_code' => $latestRow['last_employee_code'] ?? '',
                                'last_employee_name' => $latestRow['last_employee_name'] ?? ''
                            );
                        }

                        if (!empty($tableData)) {
                            $sl = 1;
                            foreach ($tableData as $reportRow) {
                                $lastUsedDateTime = '-';

                                $clockInDate = trim((string)($reportRow['clock_in_date'] ?? ''));
                                $clockInTime = trim((string)($reportRow['clock_in'] ?? ''));
                                $clockOutDate = trim((string)($reportRow['clock_out_date'] ?? ''));
                                $clockOutTime = trim((string)($reportRow['clock_out'] ?? ''));

                                $clockInTimestamp = '';
                                if ($clockInDate != '' && $clockInTime != '' && $clockInTime != '00:00:00') {
                                    $clockInTimestamp = strtotime($clockInDate . ' ' . $clockInTime);
                                }

                                $clockOutTimestamp = '';
                                if ($clockOutDate != '' && $clockOutTime != '' && $clockOutTime != '00:00:00') {
                                    $clockOutTimestamp = strtotime($clockOutDate . ' ' . $clockOutTime);
                                }

                                if ($clockInTimestamp != '' && $clockOutTimestamp != '') {
                                    $lastUsedDateTime = date('d-m-Y H:i:s', max($clockInTimestamp, $clockOutTimestamp));
                                } elseif ($clockOutTimestamp != '') {
                                    $lastUsedDateTime = date('d-m-Y H:i:s', $clockOutTimestamp);
                                } elseif ($clockInTimestamp != '') {
                                    $lastUsedDateTime = date('d-m-Y H:i:s', $clockInTimestamp);
                                }

                                $csvData[] = array(
                                    'Sl No' => $sl,
                                    'Machine Name' => $reportRow['machine_name'] ?? '',
                                    'Machine Number' => $reportRow['machine_number'] ?? '',
                                    'Location' => $reportRow['location'] ?? '',
                                    'InOutIndicator' => $reportRow['InOutIndicator'] ?? '',
                                    'Latest Date Time' => $lastUsedDateTime,
                                    'Last Employee Code' => $reportRow['last_employee_code'] ?? '',
                                    'Last Employee Name' => $reportRow['last_employee_name'] ?? ''

                                );
                                ?>
                                <tr>
                                    <td><?php echo $sl; ?></td>
                                    <td><?php echo htmlspecialchars($reportRow['machine_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($reportRow['machine_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($reportRow['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string)($reportRow['InOutIndicator'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="latest-used-value"><?php echo htmlspecialchars($lastUsedDateTime, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($reportRow['last_employee_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($reportRow['last_employee_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                    </tr>
                                <?php
                                $sl++;
                            }

                            $_SESSION['download_data'] = $csvData;
                            $_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
                            $_SESSION['csv_header']['title'] = 'Biometric Machine Last Used Report';
                            $_SESSION['csv_header']['date_time'] = date('d-m-Y H:i:s');
                        } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center">No Data Available</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_standard_biometric_machine_last_used_report.js"></script>
