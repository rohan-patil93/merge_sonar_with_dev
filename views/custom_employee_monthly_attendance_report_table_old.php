<?php
if($_POST['page_ajax']=='Y'){


include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
}
?>
<link rel="stylesheet" href="assets/css/custom_employee_monthly_attendance_report_table.css">
<?php

$employee_id = $_REQUEST['employee_id'] ?? 0;
$month       = $_REQUEST['month'] ?? '';

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
$shift_info = $response['shift_times'] ?? array();
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
			<?php if (!empty($shift_info)): ?>
				<label class="text-muted m-l-5">
                    <i class="fa fa-clock-o"></i> 
                    <?= $shift_info['shift_name'] ?? 'N/A'; ?> 
                    (<?= $shift_info['clock_in'] ?? '00:00:00'; ?> - <?= $shift_info['clock_out'] ?? '00:00:00'; ?>)
                </label>
			<?php endif; ?>
		</div>

		<div class="card-body">
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
<input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['userid']; ?>">
<input type="hidden" name="emp_id" id="emp_id" value="<?php echo $employee_id; ?>">
<input type="hidden" name="clock_in" id="clock_in" value="<?php echo $shift_info['clock_in']; ?>">
<input type="hidden" name="clock_out" id="clock_out" value="<?php echo $shift_info['clock_out']; ?>">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%">S.No</th>
							<th>Attendance Date</th>
							<th>Clock In</th>
							<th>Clock Out</th>
                            <th>Over Time</th>
                            <th>Absenteeism</th>
							<th>Status</th>
                            <th></th>
                             <th></th>
						</tr>
					</thead>
					 <tbody>
                        <?php
                        $i = 1;
                        $over_time = 0;
                        $absent_time = 0;
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
                         $current_key = '';
                        if (!empty($attendance_map[$date]['leave_type_id'])) {
                            // If leave_type_id exists (4, 6, 9), use it
                            $current_key = (int)$attendance_map[$date]['leave_type_id'];
                        } else {
                            // Map attendance_key_short_code to leave_type_id
                            
                            if ($status == 'P') {
                                $current_key = '1';
                            } elseif ($status == 'A') {
                                $current_key = '2';
                            }
                        }

                        if(isset($calculated_hours[$date]) && $show!='HD' ){
                            list($hours, $minutes) = explode(":",  $calculated_hours[$date]['ot_string']);
                            $over_time += ($hours * 60) + $minutes;

                            list($hours, $minutes) = explode(":",  $calculated_hours[$date]['ut_string']);
                            $absent_time += ($hours * 60) + $minutes;
                        }
                        $oncc = ($timesheet_id!="" ? $timesheet_id :0 ).",'".$date."','".$clock_in."','".$clock_out."'";
                        ?>

                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= date('d-m-Y', strtotime($date)); ?></td>
                            <td class="clock-in-cell" data-rid="<?= $timesheet_id ?>" data-emp-id="<?= $employee_id ?>" data-date="<?= $date ?>"><?= $clock_in; ?></td>
                            <td class="clock-out-cell" data-rid="<?= $timesheet_id ?>" data-emp-id="<?= $employee_id ?>" data-date="<?= $date ?>"><?= $clock_out; ?></td>
                            <td > <?php echo isset($calculated_hours[$date]) ? $calculated_hours[$date]['ot_string'].' Hrs' : '-'; ?> </td>
                            <td> <?php echo isset($calculated_hours[$date]) ? $calculated_hours[$date]['ut_string'].' Hrs' : '-'; ?> </td>
                            <td><?= $show; ?>
                            
                            </td>
                            <td style="width:150px">
                                <select style="width:150px" class="form-select"
                                        data-rid="<?= $timesheet_id ?>"
                                        data-emp-id="<?= $employee_id ?>"
                                        data-date="<?= $date ?>"
                                         onchange ="leavemonthapply(<?php echo $oncc;?>,this.value)">
                                    <option value="">Select</option>
                                    <option value="1" <?= ($current_key == '1') ? 'selected' : '' ?>>Present</option>
                                    <option value="2" <?= ($current_key == '2') ? 'selected' : '' ?>>Absent</option>
                                    <option value="4" <?= ($current_key == '4') ? 'selected' : '' ?>>Earned Leave</option>
                                    <option value="6" <?= ($current_key == '6') ? 'selected' : '' ?>>Personal Leave</option>
                                    <option value="9" <?= ($current_key == '9') ? 'selected' : '' ?>>Short Leave</option>
                                </select>
                            </td>
                            <td><?php if ($is_editable){ ?>
                                <a href="javascript:void(0);"
                                   class="edit-timesheet"
                                   data-id="<?= $timesheet_id ?>"
                                   data-date="<?= $date ?>"
                                   data-emp-id="<?= $employee_id ?>"
                                   data-workinghrs="<?= $workinghrs ?>"
                                   data-emp-name="<?= htmlspecialchars($employee_name['display_name'], ENT_QUOTES) ?>"
                                   data-svaetype="<?= $save_type ?>"
                                   data-month="<?= $only_month ?>"
                                   data-status="<?= $status ?>"
                                   data-year="<?= $yearr ?>"
                                   >
                                    <i class="fa fa-edit text-primary"></i>
                                </a>

                        <?php } ?></td>
                        </tr>

                        <?php endforeach; ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> 
                                <?php
                                $finalHours = floor($over_time / 60);
                                $finalMinutes = $over_time % 60;
                                echo sprintf("%02d:%02d", $finalHours, $finalMinutes);
                                ?>
                                
                            </td>
                            <td>
                                 <?php
                                $finalHours = floor($absent_time / 60);
                                $finalMinutes = $absent_time % 60;
                                echo sprintf("%02d:%02d", $finalHours, $finalMinutes);
                                 ?>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
					
				</table>
			</div>

		</div>
	</div>
</div>
<div class="modal fade" id="timesheet_edit_modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content " id="workentrypop">

          </div>
        </div>
      </div>

<script>
    window.addEventListener("load", function() {

    $(document).on('click', '.edit-timesheet', function () {

    var data = {
        id: $(this).data('id'),
        timesheet_date: $(this).data('date'),
        emp_id: $(this).data('emp-id'),
        workinghrs: $(this).data('workinghrs'),
        emp_name: $(this).data('emp-name'),
        attendancepop: 1,
        year: $(this).data('year'),
        svaetype: $(this).data('svaetype'),
        month: $(this).data('month'),
        status: $(this).data('status'),
        page: 'S'

    };

    $.ajax({
        url: "views/custom_insert_new_work_entry_popup.php",
        type: "POST",
        data: data,
        success: function (html) {
            $('#workentrypop').html(html);
            $('#timesheet_edit_modal').modal('show');
        }
    });

    });
});

function leavemonthapply(id, timesheet_date, main_clock_in, main_clock_out, leave_type_id) {
 
    Swal.fire({
        title: 'Are you sure?',
        text: "Are you sure to change this?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Change it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
 
        if (!result.isConfirmed) {
            return; // STOP if cancelled
        }

   
    // Get hidden field values
    var company_id = $('#company_id').val();
    var user_id    = $('#user_id').val();
    var emp_id     = $('#emp_id').val();
    var clock_in   = $('#clock_in').val();
    var clock_out  = $('#clock_out').val();
    // Determine presentcheck
    var presentcheck = '';
    if (leave_type_id == 1) {
        presentcheck = 'present';
    } else if (leave_type_id == 2) {
        presentcheck = 'absent';
    } else {
        presentcheck = 'leave';
    }
    // Create data array
    var formData = {
        page: 'saveEmployeeAttendance',
        id: id,
        timesheet_date: timesheet_date,
        company_id: company_id,
        user_id: user_id,
        emp_id: emp_id, 
        shift_clock_in: clock_in,
        shift_clock_out: clock_out,
        clock_in: clock_in,
        clock_out: clock_out, 
        main_clock_in: main_clock_in,
        main_clock_out: main_clock_out, 
        absent_date: timesheet_date,
        leave_date: timesheet_date,
        leave_type_id: leave_type_id,
        presentcheck: presentcheck
    };
    //console.log(formData);
    // AJAX call
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: formData,
        success: function(data) {
            reloadAttendanceTable();
            /*if (data.status == 'success') {
                // Reload the table after success
                setTimeout(function() {
                    reloadAttendanceTable();
                }, 1500);
            } else {
                alert(data.message || 'Save failed');
            }*/
        },
        error: function() {
            alert('Server error');

        }

    });
     });
}
 
</script>

