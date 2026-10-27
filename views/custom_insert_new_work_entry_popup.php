<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$url = API
    . "?mode=togetalldetailsforemployeevalattendance"
    . "&method=get"
    . "&id=" . ($_POST['id'] ?? '')
    . "&date=" . ($_POST['timesheet_date'] ?? '')
    . "&emp_id=" . ($_POST['emp_id'] ?? '')
    . "&workinghrs=" . ($_POST['workinghrs'] ?? '')
    . "&emp_name=" . urlencode($_POST['emp_name'] ?? '')
    . "&attendancepop=" . ($_POST['attendancepop'] ?? '')
    . "&year=" . ($_POST['year'] ?? '')
    . "&svaetype=" . ($_POST['svaetype'] ?? '')
    . "&month=" . ($_POST['month'] ?? '')
    . "&status=" . ($_POST['status'] ?? '');
$cudata = callAPI($url);

$alldetails = json_decode($cudata, 1);
$attendance = $alldetails['attendance'];
$shift = $alldetails['shift'];
$status = $_POST['status'] ?? '';
$isPresent = ($status === 'P');
$isAbsent  = ($status === 'A');
$isLeave   = (strpos($status, 'Leave') != false);

$tsDate = $_POST['timesheet_date'] ?? '';
$clockIn = $attendance['clock_in'];
$clockOut = $attendance['clock_out'];

$clockInVal  = $clockIn  ? ($tsDate . ' ' . $clockIn)  : '';
$clockOutVal = $clockOut ? ($tsDate . ' ' . $clockOut) : '';

$shiftName     = $shift['shift_name'] ?? 'General';
$shiftClockIn  = $shift['clock_in'] ?? '--';
$shiftClockOut = $shift['clock_out'] ?? '--';

?>

<div class="modal-header">
    <h5 class="modal-title">Attendance Info<span class="form-label badge  p-2 m-l-2"><i class="ti-user"></i> (<?php echo $_REQUEST['emp_name']; ?>) </span> </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="row">
                <form class="kt-form kt-form--label-right" action="" id="popupformattendace" method="post">
                    <div class="card latest-update-card">
                        <div class="card-header">
                            <h5>Employee Working Details</h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                            <div class="col-lg-12">
                                <div class="form-radio p-2">
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck" class="attendance-radio"
                                                value="present" <?= $isPresent ? 'checked' : '' ?>>
                                            <i class="helper"></i>Present
                                        </label>
                                    </div>

                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck" class="attendance-radio"
                                                value="absent" <?= $isAbsent ? 'checked' : '' ?>>
                                            <i class="helper"></i>Absent
                                        </label>
                                    </div>

                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                            <input aria-label="Presentcheck" type="radio" name="presentcheck" class="attendance-radio"
                                                value="leave" <?= $isLeave ? 'checked' : '' ?>>
                                            <i class="helper"></i>Leave
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="present-block <?= $isPresent ? '' : 'd-none' ?>">
                                    <div class="row p-2">
                                        <div class="col-lg-6">
                                            <span class="form-label">Clock In</span>
                                            <input aria-label="Clock In" type="datetime-local"
                                                name="clock_in"
                                                class="form-control"
                                                value="<?= $clockInVal ? date('Y-m-d\TH:i', strtotime($clockInVal)) : '' ?>">
                                        </div>

                                        <div class="col-lg-6">
                                            <span class="form-label">Clock Out</span>
                                            <input aria-label="Clock Out" type="datetime-local"
                                                name="clock_out"
                                                class="form-control"
                                                value="<?= $clockOutVal ? date('Y-m-d\TH:i', strtotime($clockOutVal)) : '' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="absent-block <?= $isAbsent ? '' : 'd-none' ?>">
                                    <div class="row p-2">
                                        <div class="col-lg-6">
                                            <span class="form-label">Absent Date</span>
                                            <input aria-label="Absent Date" type="date"
                                                name="absent_date"
                                                class="form-control "
                                                value="<?= $_POST['timesheet_date'] ?? '' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="leave-block <?= $isLeave ? '' : 'd-none' ?>">
                                    <div class="row p-2">
                                        <div class="col-lg-6">
                                            <span class="form-label">Leave Category</span>
                                            <select aria-label="Leave Type" name="leave_type_id"
                                                class="form-control form-select js-example-basic-single">
                                                <option value="">Select Leave Category</option>
                                                <?php if (!empty($alldetails['leave_types'])) {
                                                    foreach ($alldetails['leave_types'] as $lt) { ?>
                                                        <option value="<?= $lt['id'] ?>">
                                                            <?= $lt['leave_type_name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <span class="form-label">Leave Date</span>
                                            <input aria-label="Leave Date" type="date"
                                                name="leave_date"
                                                class="form-control "
                                                value="<?= $_POST['timesheet_date'] ?? '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="col-lg-12">
                                <div class="card p-2">
                                    <div class="row text-center">
                                        <div class="col-lg-4 col-12 d-flex flex-row">
                                            <h6 class="mb-1">Status : <span class="f-w-600"><?= htmlspecialchars($status) ?></span></h6>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card p-2">
                                    <div class="row text-center">

                                        <div class="col-lg-4 col-12">
                                            <h6 class="mb-1">Shift</h6>
                                            <span class="f-w-600"><?= htmlspecialchars($shiftName) ?></span>
                                        </div>

                                        <div class="col-lg-4 col-6">
                                            <h6 class="mb-1">Clock In</h6>
                                            <span class="f-w-600"><?= $shiftClockIn ?></span>
                                        </div>

                                        <div class="col-lg-4 col-6">
                                            <h6 class="mb-1">Clock Out</h6>
                                            <span class="f-w-600"><?= $shiftClockOut ?></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <?php
                                $overtime  = '00:00';
                                $undertime = '00:00';
                                $showOver  = false;
                                $showUnder = false;

                                if ($isPresent && !empty($attendance['clock_in']) && !empty($attendance['clock_out'])) {

                                    $ci = strtotime($_POST['timesheet_date'].' '.$attendance['clock_in']);
                                    $co = strtotime($_POST['timesheet_date'].' '.$attendance['clock_out']);

                                    if ($co > $ci) {
                                        $workedSeconds = $co - $ci;

                                        list($h, $m, $s) = explode(':', $alldetails['attendance_settings']['full_day_hours']);
                                        $fullDaySeconds = ($h * 3600) + ($m * 60) + $s;

                                        if ($workedSeconds > $fullDaySeconds) {
                                            $otSeconds = $workedSeconds - $fullDaySeconds;
                                            $overtime = gmdate('H:i', $otSeconds);
                                            $showOver = true;
                                        } elseif ($workedSeconds < $fullDaySeconds) {
                                            $utSeconds = $fullDaySeconds - $workedSeconds;
                                            $undertime = gmdate('H:i', $utSeconds);
                                            $showUnder = true;
                                        }
                                    }
                                }
                                ?>

                                <?php if ($isPresent) { ?>
                                <div class="card proj-progress-card p-3" id="overtimeBox">
                                    <div class="row text-center">

                                        <div class="col-lg-6 col-6">
                                            <h6>OverTime</h6>
                                            <h5 class="m-b-30 f-w-700 ">
                                                <?= $overtime ?> Hrs
                                            </h5>
                                        </div>

                                        <div class="col-lg-6 col-6">
                                            <h6>Absenteeism</h6>
                                            <h5 class="m-b-30 f-w-700 ">
                                                <?= $undertime ?> Hrs
                                            </h5>
                                        </div>

                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>


                        </div>

                        <div id="attendanceAutoData"
                            data-original-status="<?= htmlspecialchars($status, ENT_QUOTES) ?>"
                            data-id="<?= $_POST['id'] ?? '' ?>"
                            data-emp-id="<?= (int)$_POST['emp_id'] ?? '' ?>"
                            data-timesheet-date="<?= $_POST['timesheet_date'] ?>">
                        </div>

                        <input type="hidden" name="id" value="<?= $_POST['id'] ?? '' ?>">
                        <input type="hidden" name="emp_id" value="<?= $_POST['emp_id'] ?>">
                        <input type="hidden" name="timesheet_date" value="<?= $_POST['timesheet_date'] ?>">
                        <input type="hidden" name="original_status" value="<?= htmlspecialchars($status) ?>">

                </form>
            </div>
        </div>
        <div class="border-top">
            <div class="text-start ">
                <button type="button"
                    class="btn btn-primary"
                    id="saveAttendanceBtn">
                    Save
                </button>
            </div>

        </div>

    </div>
</div>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script>
    $(document).on('change', '.attendance-radio', function() {
        $('.present-block, .absent-block, .leave-block').addClass('d-none');

        if (this.value === 'present') {
            $('.present-block').removeClass('d-none');
        } else if (this.value === 'absent') {
            $('.absent-block').removeClass('d-none');
        } else if (this.value === 'leave') {
            $('.leave-block').removeClass('d-none');
            $('.js-example-basic-single').select2({});
        }

    });


    /* helper */

    $(document).on('change', '.attendance-radio', function() {

        if (this.value !== 'present') return;

        const box = document.getElementById('attendanceAutoData');
        if (!box) return;

        const originalStatus = box.dataset.originalStatus || '';
        if (originalStatus === 'P') return;

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'getAutoAttendanceTime',
                id: box.dataset.id,
                emp_id: box.dataset.empId,
                date: box.dataset.timesheetDate
            },
            success: function(res) {
                try {
                    const data = JSON.parse(res);
                    if (data.status === 'success') {
                        $('input[name="clock_in"]').val(data.clock_in);
                        $('input[name="clock_out"]').val(data.clock_out);
                    }
                } catch (e) {}
            }
        });

    });
</script>
<script>
    $(document).on('click', '#saveAttendanceBtn', function() {

        var $form = $('#popupformattendace');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: $form.serialize() + '&page=saveEmployeeAttendance',
            success: function(res) {
                try {
                    var data = JSON.parse(res);

                    if (data.status === 'success') {
                        $('#timesheet_edit_modal').modal('hide');

                        // reload attendance table
                        // full page reload
                        window.location.reload();

                    } else {
                        alert(data.message || 'Save failed');
                    }
                } catch (e) {
                    alert('Invalid response');
                }
            },
            error: function() {
                alert('Server error');
            }
        });

    });
</script>
