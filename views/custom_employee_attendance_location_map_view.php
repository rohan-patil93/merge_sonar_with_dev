<?php
include_once(__DIR__ . '/../includes/session.php');
include_once(__DIR__ . '/../dbconfig.php');
include_once(__DIR__ . '/../comm.php');

$employee_id = $_GET['employee_id'] ?? ($_POST['employee_id'] ?? 0);
$attendance_date = $_GET['attendance_date'] ?? ($_POST['attendance_date'] ?? '');
$employee_name_param = $_GET['emp_name'] ?? '';

if (!$employee_id || !$attendance_date) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit;
}

$url = API . "?mode=getEmployeeAttendanceLocationDetails&method=get"
    . "&employee_id=" . urlencode($employee_id)
    . "&attendance_date=" . urlencode($attendance_date);

$response = callAPI($url);
$response = json_decode($response, true);

$details = $response['details'] ?? array();
$employee = $response['employee'] ?? array();
$attendance_date = $response['attendance_date'] ?? $attendance_date;

$total_checkins = $response['total_checkins'] ?? 0;
$total_checkouts = $response['total_checkouts'] ?? 0;
$total_duration = $response['total_duration'] ?? '00:00:00';

$employee_name = $employee['display_name'] ?? $employee_name_param;
$employee_code = $employee['emp_code'] ?? '';

$page_data = json_encode(
    array(
        'details' => $details,
        'employee_name' => $employee_name,
        'employee_code' => $employee_code,
        'attendance_date' => $attendance_date
    ),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>

<link rel="stylesheet" href="assets/css/custom_employee_attendance_location_map_view.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@9.0.0/ol.css">

<div class="page-body" id="attendanceLocationMapPage" data-attendance='<?= $page_data ?>'>
    <div class="card">
        <div class="card-header">
            <h5>Attendance Location Map</h5>
        </div>
        <div class="card-body">
            <div class="attendance-location-header">
                <div class="attendance-location-employee">
                    <div class="title">Employee</div>
                    <div class="value"><?= htmlspecialchars($employee_name, ENT_QUOTES) ?></div>
                </div>
                <div class="attendance-location-employee">
                    <div class="title">Emp Code</div>
                    <div class="value"><?= htmlspecialchars($employee_code, ENT_QUOTES) ?></div>
                </div>
                <div class="attendance-location-employee">
                    <div class="title">Date</div>
                    <div class="value"><?= htmlspecialchars(date('d-m-Y', strtotime($attendance_date)), ENT_QUOTES) ?></div>
                </div>
            </div>

            <div class="attendance-summary">
                <div class="summary-card">
                    <div class="summary-label">Check-ins</div>
                    <div class="summary-value"><?= $total_checkins ?></div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Check-outs</div>
                    <div class="summary-value"><?= $total_checkouts ?></div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Total Duration</div>
                    <div class="summary-value"><?= htmlspecialchars($total_duration, ENT_QUOTES) ?></div>
                </div>
            </div>

            <div class="attendance-map-container">
                <div id="attendanceLocationMap" class="attendance-location-map"></div>
                <div class="attendance-map-legend">
                    <div class="legend-title">Legend</div>
                    <div class="legend-item"><span class="legend-dot legend-dot-in"></span>Clock In</div>
                    <div class="legend-item"><span class="legend-dot legend-dot-out"></span>Clock Out</div>
                </div>
                <div id="attendanceLocationEmpty" class="attendance-location-empty">
                    No location data available for this date.
                </div>
            </div>

            <div class="attendance-location-table">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Clock In</th>
                                <th>Clock In Location</th>
                                <th>Clock Out</th>
                                <th>Clock Out Location</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($details)) : ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No attendance details found</td>
                            </tr>
                        <?php else : ?>
                            <?php $i = 1; ?>
                            <?php foreach ($details as $row) : ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= !empty($row['clock_in']) ? htmlspecialchars($row['clock_in'], ENT_QUOTES) : '-' ?></td>
                                    <td>
                                        <?= !empty($row['clock_in_lat']) && !empty($row['clock_in_long'])
                                            ? htmlspecialchars($row['clock_in_lat'] . ', ' . $row['clock_in_long'], ENT_QUOTES)
                                            : '-' ?>
                                    </td>
                                    <td><?= !empty($row['clock_out']) ? htmlspecialchars($row['clock_out'], ENT_QUOTES) : '-' ?></td>
                                    <td>
                                        <?= !empty($row['clock_out_lat']) && !empty($row['clock_out_long'])
                                            ? htmlspecialchars($row['clock_out_lat'] . ', ' . $row['clock_out_long'], ENT_QUOTES)
                                            : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@9.0.0/dist/ol.js"></script>
<script src="assets/js/custom_employee_attendance_location_map_view.js"></script>
