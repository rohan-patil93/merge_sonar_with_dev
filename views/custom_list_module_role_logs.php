<?php
$_SESSION['apply_js']['multiselect'] = 1;

$filter_url = API . "?mode=getmodulerolelogs&method=get&filters_only=Y";
$filter_res = json_decode(callAPI($filter_url), true);
$filter_modules = isset($filter_res['filter_modules']) ? $filter_res['filter_modules'] : array();
$filter_roles = isset($filter_res['filter_roles']) ? $filter_res['filter_roles'] : array();
$session_users = isset($_SESSION['users']) ? $_SESSION['users'] : array();
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Module Role Logs</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-3 col-md-6 form-group">
                    <span>From Date <span class="text-danger">*</span></span>
                    <input aria-label="From Date" class="form-control" type="date" name="from_date" id="from_date" value="">
                    <div style="display: none;color: red;" id="from_date_err"></div>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>To Date <span class="text-danger">*</span></span>
                    <input aria-label="To Date" class="form-control" type="date" name="to_date" id="to_date" value="">
                    <div style="display: none;color: red;" id="to_date_err"></div>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>User</span>
                    <select aria-label="Filter User" class="form-control js-example-basic-single" id="filter_user_id" name="filter_user_id" style="width:100%">
                        <option value="">All Users</option>
                        <?php if(count((array)$session_users) > 0){
                            foreach($session_users as $uid => $uname){ ?>
                                <option value="<?php echo $uid; ?>"><?php echo htmlspecialchars($uname); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Module</span>
                    <select aria-label="Filter Module" class="form-control js-example-basic-single" id="filter_module_id" name="filter_module_id" style="width:100%">
                        <option value="">All Modules</option>
                        <?php if(count((array)$filter_modules) > 0){
                            foreach($filter_modules as $module_row){
                                $module_id = is_array($module_row) ? $module_row['module_id'] : $module_row->module_id;
                                $module_name = is_array($module_row) ? $module_row['display_name'] : $module_row->display_name;
                        ?>
                                <option value="<?php echo $module_id; ?>"><?php echo htmlspecialchars($module_name); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Role</span>
                    <select aria-label="Filter Role" class="form-control js-example-basic-single" id="filter_role_id" name="filter_role_id" style="width:100%">
                        <option value="">All Roles</option>
                        <?php if(count((array)$filter_roles) > 0){
                            foreach($filter_roles as $role_row){
                                $role_id = is_array($role_row) ? $role_row['id'] : $role_row->id;
                                $role_name = is_array($role_row) ? $role_row['display_name'] : $role_row->display_name;
                        ?>
                                <option value="<?php echo $role_id; ?>"><?php echo htmlspecialchars($role_name); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Action</span>
                    <select aria-label="Action" class="form-control js-example-basic-single" id="action" name="action" style="width:100%">
                        <option value="">All Actions</option>
                        <option value="Assigned">Assigned</option>
                        <option value="Removed">Removed</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Modified By</span>
                    <select aria-label="Action By" class="form-control js-example-basic-single" id="action_by" name="action_by" style="width:100%">
                        <option value="">All</option>
                        <?php if(count((array)$session_users) > 0){
                            foreach($session_users as $uid => $uname){ ?>
                                <option value="<?php echo $uid; ?>"><?php echo htmlspecialchars($uname); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>&nbsp;</span>
                    <button type="button" class="btn btn-primary form-control" id="getModuleRoleLogsBtn">Search</button>
                </div>
            </div>
            <div class="row mt-3  custom-scroll" id="TableContent" style="height:500px">
                <div class="col-12">
                    <p class="text-muted mb-0">Select a date range and click Search to view module role logs.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".js-example-basic-single").select2();

        $("#from_date").on("change", function () {
            var fromDate = $(this).val();
            $("#to_date").attr("min", fromDate);
            if($("#to_date").val() != "" && $("#to_date").val() < fromDate){
                $("#to_date").val(fromDate);
            }
        });

        $("#getModuleRoleLogsBtn").on("click", function () {
            $("#from_date_err").hide();
            $("#to_date_err").hide();

            var fromDate = $("#from_date").val();
            var toDate = $("#to_date").val();
            var isValid = true;

            if(fromDate == ""){
                $("#from_date_err").text("Please select From Date").show();
                isValid = false;
            }
            if(toDate == ""){
                $("#to_date_err").text("Please select To Date").show();
                isValid = false;
            }
            if(!isValid){
                return;
            }

            var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
            $("#TableContent").html(loader);

            $.ajax({
                url: "views/custom_list_module_role_logs_ajax.php",
                type: "POST",
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    filter_user_id: $("#filter_user_id").val(),
                    filter_module_id: $("#filter_module_id").val(),
                    filter_role_id: $("#filter_role_id").val(),
                    action: $("#action").val(),
                    action_by: $("#action_by").val()
                },
                success: function (response) {
                    $("#TableContent").html(response);
                },
                error: function () {
                    $("#TableContent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load module role logs.</p></div>');
                }
            });
        });
    });
</script>
