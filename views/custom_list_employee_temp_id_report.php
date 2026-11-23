<?php
$_SESSION['apply_js']['multiselect'] = 1;

$filter_url = API . "?mode=getemployeetempidreport&method=get&filters_only=Y";
$filter_res = json_decode(callAPI($filter_url), true);
$locations = isset($filter_res['locations']) ? $filter_res['locations'] : array();
$employees = isset($filter_res['employees']) ? $filter_res['employees'] : array();
$today = date('Y-m-d');
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><?= __('Employee Temp ID / Forgot ID Report'); ?></h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="employeeTempIdReportDownload" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= __('More'); ?></button>
                    <div class="dropdown-menu p-0" aria-labelledby="employeeTempIdReportDownload" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employee Temp ID Report" target="_blank"><?= __('CSV'); ?></a>
                    </div>
                </div>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    <?= __('Back'); ?>
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="from_date"><?= __('From Date'); ?> <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="from_date" id="from_date" value="<?php echo $today; ?>" aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="to_date"><?= __('To Date'); ?> <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="to_date" id="to_date" value="<?php echo $today; ?>" aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="location_id"><?= __('Location'); ?></label>
                    <select class="form-control js-example-basic-single" id="location_id" name="location_id" style="width:100%" aria-label="<?= htmlspecialchars(__('Location Id'), ENT_QUOTES, 'UTF-8'); ?>">
                        <option value=""><?= __('All Locations'); ?></option>
                        <?php if(count((array)$locations) > 0){
                            foreach($locations as $loc_row){
                                $lid = is_array($loc_row) ? $loc_row['id'] : $loc_row->id;
                                $lname = is_array($loc_row) ? $loc_row['location_name'] : $loc_row->location_name;
                        ?>
                                <option value="<?php echo $lid; ?>"><?php echo htmlspecialchars($lname); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="employee_id"><?= __('Employee'); ?></label>
                    <select class="form-control js-example-basic-single" id="employee_id" name="employee_id" style="width:100%" aria-label="<?= htmlspecialchars(__('Employee Id'), ENT_QUOTES, 'UTF-8'); ?>">
                        <option value=""><?= __('All Employees'); ?></option>
                        <?php if(count((array)$employees) > 0){
                            foreach($employees as $employee_row){
                                $eid = is_array($employee_row) ? $employee_row['id'] : $employee_row->id;
                                $ename = is_array($employee_row) ? $employee_row['display_name'] : $employee_row->display_name;
                                $ecode = is_array($employee_row) ? (isset($employee_row['emp_code']) ? $employee_row['emp_code'] : '') : (isset($employee_row->emp_code) ? $employee_row->emp_code : '');
                                $label = $ename;
                                if($ecode != ''){
                                    $label .= ' ('.$ecode.')';
                                }
                        ?>
                                <option value="<?php echo $eid; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="temp_reason"><?= __('Temp Reason'); ?></label>
                    <select class="form-control js-example-basic-single" id="temp_reason" name="temp_reason" style="width:100%" aria-label="<?= htmlspecialchars(__('Temp Reason'), ENT_QUOTES, 'UTF-8'); ?>">
                        <option value=""><?= __('All'); ?></option>
                        <option value="forgot"><?= __('Forgot'); ?></option>
                        <option value="new"><?= __('New'); ?></option>
                        <option value="xbranch"><?= __('Cross-branch'); ?></option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="card_return"><?= __('Card Return'); ?></label>
                    <select class="form-control js-example-basic-single" id="card_return" name="card_return" style="width:100%" aria-label="<?= htmlspecialchars(__('Card Return'), ENT_QUOTES, 'UTF-8'); ?>">
                        <option value=""><?= __('All'); ?></option>
                        <option value="not_returned"><?= __('Not Returned'); ?></option>
                        <option value="returned"><?= __('Returned'); ?></option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <label for="getEmployeeTempIdReportBtn" aria-label="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>">&nbsp;</label>
                    <button type="button" class="btn btn-primary form-control" id="getEmployeeTempIdReportBtn"><?= __('Search'); ?></button>
                </div>
            </div>
            <div class="row mt-3 custom-scroll" id="tableContent" style="height:500px">
                <div class="col-12">
                    <p class="text-muted mb-0"><?= __('Select filters and click Search to view employee temp ID records.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function toast(msg) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', msg, '');
    }

    $(document).ready(function () {
        $(".js-example-basic-single").select2();

        $("#from_date").on("change", function () {
            var fromDate = $(this).val();
            $("#to_date").attr("min", fromDate);
            if($("#to_date").val() != "" && $("#to_date").val() < fromDate){
                $("#to_date").val(fromDate);
            }
        });

        $("#getEmployeeTempIdReportBtn").on("click", function () {
            var fromDate = $("#from_date").val();
            var toDate = $("#to_date").val();

            if(fromDate == ""){
                toast("Please select From Date");
                return;
            }
            if(toDate == ""){
                toast("Please select To Date");
                return;
            }
            if(toDate < fromDate){
                toast("To Date should be greater than or equal to From Date");
                return;
            }

            var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
            $("#tableContent").html(loader);

            $.ajax({
                url: "views/custom_list_employee_temp_id_report_ajax.php",
                type: "POST",
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    location_id: $("#location_id").val(),
                    employee_id: $("#employee_id").val(),
                    temp_reason: $("#temp_reason").val(),
                    card_return: $("#card_return").val()
                },
                success: function (response) {
                    $("#tableContent").html(response);
                },
                error: function () {
                    $("#tableContent").html('<div class="col-12"><p class="text-danger mb-0"><?= htmlspecialchars(__('Unable to load employee temp ID report.'), ENT_QUOTES, 'UTF-8'); ?></p></div>');
                }
            });
        });
    });
</script>
