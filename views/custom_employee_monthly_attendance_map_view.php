<?php
$_SESSION['apply_js']['multiselest'] = 1;

// Fetch employees
$url = API . "?mode=getEmployeesAttendanceData&method=get";
$result = callAPI($url);
$result = json_decode($result, true);
$employees = $result['table'] ?? array();
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Employee Monthly Report</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <!-- Employee -->
                <div class="form-group col-md-4">
                    <label>Employee <span style="color:red;">*</span></label>
                    <select class="form-select js-example-basic-single" id="employee_id">
                        <option value="0">Select Employee</option>
                        <?php foreach ($employees as $row): ?>
                            <option value="<?= $row['id']; ?>">
                                <?= $row['display_name'].' ('. $row['emp_code'].')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Month -->
                <div class="form-group col-md-3">
                    <label>Month <span style="color:red;">*</span></label>
                    <input type="month" class="form-control" id="report_month">
                </div>

                <!-- Search -->
                <div class="form-group col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary form-control" id="searchBtn">
                        Search
                    </button>
                </div>

            </div>

            <!-- Result -->
            
        </div>
    </div>
    <div class="mt-4" id="TableContent"></div>
</div>

<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_employee_monthly_attendance_map_view_table.js"></script>
<script>
$(document).ready(function () {

    $('#searchBtn').click(function () {
        let valid = true;
        const employee_id = $('#employee_id').val();
        const month       = $('#report_month').val();

        // Employee validation
        if (employee_id == 0) {
        	tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger','animated flipInX', 'animated flipOutX', 'Please select employee', '');
            valid = false;
        }

        // Month validation
        if (!month) {
        	tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger','animated flipInX', 'animated flipOutX', 'Please select month', '');
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
            url: "views/custom_employee_monthly_attendance_map_view_table.php",
            type: "POST",
            data: {
                employee_id: employee_id,
                month: month
            },
            success: function (data) {
                $('#TableContent').html(data);
            },
            error: function () {
                $('#TableContent').html('<div class="text-danger text-center">Something went wrong</div>');
            }
        });

    });

});
</script>
