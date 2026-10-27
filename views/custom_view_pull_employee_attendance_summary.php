<?php
$employee_id = isset($_REQUEST['employee_id']) ? (int)$_REQUEST['employee_id'] : 0;
$from_date = !empty($_REQUEST['from_date']) ? date('Y-m-d', strtotime($_REQUEST['from_date'])) : date('Y-m-01');
$to_date = !empty($_REQUEST['to_date']) ? date('Y-m-d', strtotime($_REQUEST['to_date'])) : date('Y-m-t');

if ($from_date > $to_date) {
    $tmp = $from_date;
    $from_date = $to_date;
    $to_date = $tmp;
}

$url = API . "?mode=getEmployeeDailyAttendanceSummary&method=get&company_id=" . $_SESSION['company_id'] . "&from_date=" . $from_date . "&to_date=" . $to_date;
if ($employee_id > 0) {
    $url .= "&employee_id=" . $employee_id;
}

$response = callAPI($url);
$data = json_decode($response, true);

$employees = $data['employees'] ?? array();
$legend = $data['legend'] ?? array();
$date_headers = $data['date_headers'] ?? array();
$attendance_data = $data['attendance_data'] ?? array();
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom mb-5">
            <div class="card-header">
                <h5>Pull Employee Attendance Summary</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="" class="row">


                    <div class="form-group col-md-3">
                        <span class="form-label">From Date</span>
                        <input aria-label="From Date" type="date" class="form-control" name="from_date" value="<?php echo $from_date; ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <span class="form-label">To Date</span>
                        <input aria-label="To Date" type="date" class="form-control" name="to_date" value="<?php echo $to_date; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee" class="form-control js-example-basic-single" name="employee_id">
                            <option value="0">All Employees</option>
                            <?php foreach ((array)$employees as $emp) { ?>
                                <option value="<?php echo (int)$emp['id']; ?>" <?php echo ((int)$employee_id === (int)$emp['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(($emp['emp_code'] ?? '-') . ' - ' . ($emp['display_name'] ?? '')); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Apply</button>
                        <a href="index.php?option=COM_VIEW_EMPLOYEE_DAILY_ATTENDANCE_SUMMARY" class="btn btn-light">Reset</a>
                    </div>
                </form>

                <?php if (count((array)$legend) > 0) { ?>
                    <div class="mt-4">
                        <?php foreach ((array)$legend as $item) { ?>
                            <span class="badge text-white mr-2 mb-2" style="background-color: <?php echo htmlspecialchars($item['color_code'] ?? '#6c757d'); ?>;">
                                <?php echo htmlspecialchars(($item['display_value'] ?? '-') . ' - ' . ($item['key_name'] ?? '')); ?>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body">
                <style>
                    .attendance-table th,
                    .attendance-table td {
                        white-space: nowrap;
                        font-size: 12px;
                        padding: 6px;
                        text-align: center;
                    }

                    .attendance-table .sticky-col {
                        position: sticky;
                        left: 0;
                        background: #fff;
                        z-index: 3;
                        min-width: 220px;
                        text-align: left;
                    }

                    .attendance-table th:nth-child(2),
                    .attendance-table td:nth-child(2) {
                        position: sticky;
                        left: 220px;
                        background: #fff;
                        z-index: 2;
                        width: 45px;
                    }

                    .attendance-table th:nth-child(3),
                    .attendance-table td:nth-child(3) {
                        position: sticky;
                        left: 265px;
                        background: #fff;
                        z-index: 2;
                        width: 45px;
                    }

                    .attendance-table thead th {
                        position: sticky;
                        top: 0;
                        background: #f8f9fa;
                        z-index: 4;
                    }
                </style>

                <div class="table-responsive">
                    <table class="table table-bordered attendance-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="sticky-col">Employee</th>
                                <th scope="col">P</th>
                                <th scope="col">A</th>
                                <?php foreach ((array)$date_headers as $header) { ?>
                                    <th scope="col">
                                        <div><?php echo htmlspecialchars($header['day_number']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($header['day_name']); ?></small>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count((array)$attendance_data) > 0) { ?>
                                <?php $index = 1; ?>
                                <?php foreach ((array)$attendance_data as $emp_row) { ?>
                                    <tr>
                                        <td class="sticky-col">
                                            <span class="f-12 mr-2"><?php echo (int)$index; ?></span>
                                            <?php if (!empty($emp_row['employee']['image'])) { ?>
                                                <img src="uploads/all/<?php echo htmlspecialchars($emp_row['employee']['image']); ?>" class="img-radius img-30 align-top m-r-15" alt="User">
                                            <?php } else { ?>
                                                <span class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" title="user image"><?php echo initials($emp_row['employee']['display_name'] ?? ''); ?></span>
                                            <?php } ?>
                                            <span><?php echo htmlspecialchars(($emp_row['employee']['emp_code'] ?? '-') . ' - ' . ($emp_row['employee']['display_name'] ?? '')); ?></span>
                                        </td>
                                        <td><?php echo (int)($emp_row['present_count'] ?? 0); ?></td>
                                        <td><?php echo (int)($emp_row['absent_count'] ?? 0); ?></td>
                                        <?php foreach ((array)$date_headers as $header) {
                                            $date_key = $header['date'];
                                            $day_cell = $emp_row['days'][$date_key] ?? array();
                                            $cell_value = $day_cell['display_value'] ?? '-';
                                            $cell_color = $day_cell['color_code'] ?? '#f8f9fa';
                                            $cell_title = ($day_cell['status_label'] ?? '-')
                                                . ' | In: ' . ($day_cell['clock_in'] ?? '-')
                                                . ' | Out: ' . ($day_cell['clock_out'] ?? '-')
                                                . ' | Hrs: ' . ($day_cell['worked_hours'] ?? '-')
                                                . ' | Shift: ' . ($day_cell['shift_code'] ?? '-')
                                                . ' | Loc: ' . ($day_cell['location_status'] ?? '-');
                                        ?>
                                            <td style="background-color: <?php echo htmlspecialchars($cell_color); ?>;" title="<?php echo htmlspecialchars($cell_title); ?>">
                                                <?php echo htmlspecialchars($cell_value); ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php $index++; ?>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="<?php echo 3 + count((array)$date_headers); ?>" class="text-center">
                                        No attendance data found for selected filters.
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
