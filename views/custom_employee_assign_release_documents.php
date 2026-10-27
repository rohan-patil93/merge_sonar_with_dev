<?php
$_SESSION['apply_js']['multiselest'] = 1;

// Fetch employees
//print_r($_POST);
if((isset($_POST['search'])) ){
    $employee_id = $_REQUEST['employee_id'] ?? 0;
    $assessment_year       = $_REQUEST['assessment_year'] ?? '';
    header("location:index?option=COM_EMPLOYEE_ASSIGN_RELEASE_DOCUMENTS&employee_id=".$employee_id."&assessment_year=".$assessment_year ); exit;
}
if(isset($_GET['employee_id']) && isset($_GET['assessment_year']) ){
    $employee_id = $_REQUEST['employee_id'] ?? 0;
    $assessment_year = $_REQUEST['assessment_year'] ?? '';
}


$url = API . "?mode=togetemployeesassementyear&method=get";
$result = callAPI($url);
$result = json_decode($result, true);
//echo "<pre>"; print_r($result); echo "<pre>";
$employees = $result['emp_data'] ?? array();
$assessment_years = $result['assessment_years'] ?? array();
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Employee Release Documnet Report</h5>
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
                    <label>Assessment Year <span style="color:red;">*</span></label>
                    <select class="form-select js-example-basic-single" name="assessment_year" id="assessment_year">
                        <option value="0">Select Assessment Year</option>
                        <?php foreach ($assessment_years as $assessmentyear): ?>
                            <option <?php if( $assessment_year == $assessmentyear){ echo "selected" ;} ?> value="<?= $assessmentyear; ?>">
                                <?= $assessmentyear; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
    if(isset($_GET['employee_id']) && isset($_GET['assessment_year']) ){
        include('custom_employee_assign_release_documents_table.php'); 
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
    const assessment_year = $('#assessment_year').val();

    // Employee validation
    if (employee_id == 0) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select employee', '');
        valid = false;
    }

    // Month validation
    if (!assessment_year) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select Assessment Year', '');
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
        url: "views/custom_employee_assign_release_documents_table.php",
        type: "POST",
        data: {
            employee_id: employee_id,
            assessment_year : assessment_year,
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