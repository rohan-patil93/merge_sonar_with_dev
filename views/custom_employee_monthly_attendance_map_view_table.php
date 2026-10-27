<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$employee_id = $_POST['employee_id'] ?? 0;
$month       = $_POST['month'] ?? '';

if (!$employee_id || !$month) {
	echo '<div class="alert alert-danger">Invalid request</div>';
	exit;
}
// Call API
$url = API . "?mode=getEmployeesAttendanceData&method=get"
	. "&employee_id={$employee_id}"
	. "&month={$month}";
//echo $url;
$response = callAPI($url);
$response = json_decode($response, true);
//echo "<pre>"; print_r($response); echo "</pre>";
$attendance = $response['attendance_data'] ?? array();
$holidays   = $response['holidays'] ?? array();
$leaves     = $response['leaves'] ?? array();
$employee_name = $response['employee_name'] ?? array();
$weak_off_arr = $response['weak_off_arr']['working_days'] ?? array();
$weak_off_arr = json_decode($weak_off_arr,1);
$attendance_key_types = $response['attendance_key_types'] ?? array();
$edit_keys = array();
foreach ($attendance_key_types as $row) {
    $edit_keys[] = $row['display_value'];
}
$calculated_hours = $response['calculated_hours'] ?? array();
// Attendance lookup: date => data
$attendance_map = array();
foreach ($attendance as $row) {
    $attendance_map[$row['attendace_date']] = $row;
}
// print_r($attendance_map);
// Holiday lookup: date => name
$holiday_map = array();
foreach ($holidays as $row) {
    $holiday_map[$row['holiday_date']] = $row['holiday_name'];
}

// Leave ranges
$leave_ranges = $leaves;

/*
|--------------------------------------------------------------------------
| Date Loop Setup
|--------------------------------------------------------------------------
*/
$from_date = $month . '-01';
$to_date   = date('Y-m-t', strtotime($from_date));

$start = new DateTime($from_date);
$end   = new DateTime($to_date);
$end->modify('+1 day'); // include last day

list($yearr, $only_month) = explode('-', $month);
$only_month = str_pad($only_month, 2, '0', STR_PAD_LEFT);
?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h5>Employee Attendance Report (<?= date('F Y', strtotime($month . '-01')); ?>)</h5>
		</div>

		<div class="card-body">

			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%">S.No</th>
							<th>Attendance Date</th>
							<th>Clock In</th>
							<th>Clock Out</th>
                            <!-- <th>Over Time</th>
                            <th>Absenteeism</th> -->
							<th>Status</th>
                            <th></th>
						</tr>
					</thead>
					 <tbody>
                        <?php
                        $i = 1;
                        foreach (new DatePeriod($start, new DateInterval('P1D'), $end) as $dateObj):
                            $date = $dateObj->format('Y-m-d');
                            $day  = $dateObj->format('l');

                            $clock_in  = '-';
                            $clock_out = '-';
                            $status    = '';
                            $timesheet_id = '';
                            $workinghrs = '';
                            $save_type = 'insert';

                        ?>

                        <?php
                        // 1️⃣ Attendance
                        if (isset($attendance_map[$date])) {
                            $clock_in  = $attendance_map[$date]['clock_in'] ?? '-';
                            $clock_out = $attendance_map[$date]['clock_out'] ?? '-';
                            if($clock_out=='00:00:00'){ $clock_out = '-'; }
                            $timesheet_id = $attendance_map[$date]['timesheet_id'];
                            $employee_id = $attendance_map[$date]['employee_id'];
                            $save_type = 'update';
                            if(isset($attendance_map[$date]['clock_in'])){
                                $attendance_key = $attendance_map[$date]['attendance_key_short_code'] ?? '';
                                if (!empty($attendance_key) && isset($attendance_key_types[$attendance_key])) {
                                    $key = $attendance_key_types[$attendance_key];
                                    $status  = $key['display_value'];
                                    $show = $key['display_value'];
                                    if($key['display_value'] == 'SL'){
                                       $show = $key['display_value'].' ('.$attendance_map[$date]['old_clock_times'].')';
                                    }
                                } else {
                                    $status = 'P';
                                    $show = 'P';
                                }
                            }elseif(isset($attendance_map[$date]['leave_type_id']) &&  $attendance_map[$date]['leave_type_id'] !=""){
                                $status  = 'L';
                                $show = 'Leave';

                            }else{
                                $status  = 'A';
                                $show = 'A';
                            }
                        }
    

                        // 2️⃣ Weekend
                        elseif (!in_array($day, $weak_off_arr)) {
                            $status = 'WO';
                            $show = 'Week Off';

                        }

                        // 3️⃣ Holiday
                        elseif (isset($holiday_map[$date])) {
                            $status = 'H';
                            $show = 'H ('.$holiday_map[$date].')';

                        }

                        // 4️⃣ Leave
                        else {
                            foreach ($leave_ranges as $leave) {
                                if ($date >= $leave['leave_from'] && $date <= $leave['leave_to']) {
                                    $status = 'L';
                                    $show = 'Leave ('.$leave['leave_type_name'].')';

                                    break;
                                }
                            }

                            // 5️⃣ Absent
                            if ($status == '') {
                                $status = 'A';
                                $show = 'A';
                            }
                        }
                        $is_editable = in_array($status, $edit_keys) || strpos($status, 'Leave') != false;
                        ?>

                        <?php
                        $has_attendance = isset($attendance_map[$date])
                            && (!empty($attendance_map[$date]['clock_in']) || !empty($attendance_map[$date]['clock_out']));
                        ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= date('d-m-Y', strtotime($date)); ?></td>
                            <td><?= $clock_in; ?></td>
                            <td><?= $clock_out; ?></td>
                            <?php /* ?> <td > <?php echo isset($calculated_hours[$date]) ? $calculated_hours[$date]['ot_string'].' Hrs' : '-'; ?> </td>
                            <td> <?php echo isset($calculated_hours[$date]) ? $calculated_hours[$date]['ut_string'].' Hrs' : '-'; ?> </td> <?php */ ?>
                            <td><?= $show; ?>
                            
                            </td>
                            <td>
                                <?php if ($has_attendance): ?>
                                    <a href="javascript:void(0);"
                                       class="view-attendance-location"
                                       data-date="<?= $date ?>"
                                       data-emp-id="<?= $employee_id ?>"
                                       data-emp-name="<?= htmlspecialchars($employee_name['display_name'], ENT_QUOTES) ?>">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>

                        </td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>
					
				</table>
			</div>

		</div>
	</div>
</div>
<div class="modal fade" id="attendanceLocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Location Map</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="attendanceLocationModalBody"></div>
        </div>
    </div>
</div>
