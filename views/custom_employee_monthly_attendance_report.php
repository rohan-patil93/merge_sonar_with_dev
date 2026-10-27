<?php
$_SESSION['apply_js']['multiselest'] = 1;

// Fetch employees
//print_r($_POST);
if((isset($_POST['search'])) ){
    $employee_id = $_REQUEST['employee_id'] ?? 0;
    $month       = $_REQUEST['month'] ?? '';
    header("location:index?option=COM_EMPLOYEE_MONTHLY_ATTENDANCE_REPORT&employee_id=".$employee_id."&month=".$month ); exit;
}
if(isset($_GET['employee_id']) && isset($_GET['employee_id']) ){
    $employee_id = $_REQUEST['employee_id'] ?? 0;
    $month       = $_REQUEST['month'] ?? '';
}


$url = API . "?mode=getEmployeesAttendanceData&method=get";
$result = callAPI($url);
$result = json_decode($result, true);
//echo "<pre>"; print_r($result); echo "<pre>";
$employees = $result['table'] ?? array();

?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Employee Monthly Report</h5>
        </div>
        <div class="card-body p-4">
            <form method="post" >
            <div class="row">
                <!-- Employee -->
                <div class="form-group col-md-4">
                    <label>Employee <span style="color:red;">*</span></label>
                    <select class="form-select js-example-basic-single" name="employee_id" id="employee_id">
                        <option value="0">Select Employee</option>
                        <?php foreach ($employees as $row): ?>
                            <option <?php if( $row['id'] == $employee_id){ echo "selected" ;} ?> value="<?= $row['id']; ?>">
                                <?= $row['display_name'].' ('. $row['emp_code'].')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Month -->
                <div class="form-group col-md-3">
                    <label>Month <span style="color:red;">*</span></label>
                    <input type="month" name="month" class="form-control" id="report_month" value="<?php echo $month; ?>">
                </div>

                <!-- Search -->
                <div class="form-group col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control" name="search" value="search">
                        Search
                    </button>
                </div>

            </div>
             </form>
            <!-- Result -->
            
        </div>
    </div>
    <div class="mt-4" id="TableContent">
        <?php
    if(isset($_GET['employee_id']) && isset($_GET['month']) ){
        include('custom_employee_monthly_attendance_report_table.php'); 
    }
    ?>
    </div>
</div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script>
/*$(document).ready(function () {

    $('#searchBtn').click(function () {
        reloadAttendanceTable();
    });

});*/
/**
 * Reload the attendance report table
 */
function reloadAttendanceTable() {
    let valid = true;
    const employee_id = $('#employee_id').val();
    const month = $('#report_month').val();

    // Employee validation
    if (employee_id == 0) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select employee', '');
        valid = false;
    }

    // Month validation
    if (!month) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select month', '');
        valid = false;
    }

    if (!valid) return;

    var loader = `
        <div class="text-center">
            <img src="assets/images/spiner/spiner3.gif" style="width:300px;">
        </div>
    `;
    $('#TableContent').html(loader);

    $.ajax({
        url: "views/custom_employee_monthly_attendance_report_table.php",
        type: "POST",
        data: {
            employee_id: employee_id,
            month: month,
            page_ajax:'Y'
        },
        success: function (data) {
            $('#TableContent').html(data);
        },
        error: function () {
            $('#TableContent').html('<div class="text-danger text-center">Something went wrong</div>');
        }
    });
}
</script>
<script src="assets/js/custom_employee_monthly_attendance_report_table.js"></script>
