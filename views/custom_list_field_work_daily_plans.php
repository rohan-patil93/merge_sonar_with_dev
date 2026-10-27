<?php
$_SESSION['apply_js']['multiselest'] = 1;
$admin_type = 1;
if($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE'){
    $admin_type = 3;
}
if($_SESSION['working_role_short_code'] == 'FIELD_MANAGER'){
    $admin_type = 2;
}

$url = API . "?mode=getApplicationUsersData&method=get&admin_type=".$admin_type;
$result = callAPI($url);
$result = json_decode($result, 1);
$application_users  = array();
$application_users = $result['application_users'];

?>
<link rel="stylesheet" href="assets/css/custom_list_field_work_daily_plans.css">

<div class="page-body" id="fieldWorkDailyPlansPage"
     data-admin-type="<?= $_SESSION['admin_type'] ?>"
     data-user-id="<?= $_SESSION['userid'] ?>"
     data-app-users='<?= json_encode(
         $application_users,
         JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
     ) ?>'>
    <div class="alert alert-light alert-dismissible">
        <strong>This function is used for maintaining <strong>Field Work</strong>. * represents mandatory fields.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>Field Work Daily Plans</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS" class="btn btn-success mb-2 desktop-view">
                  <i class="icofont icofont-plus icon-white "></i>
              User Work Daily Plans            </a>
              <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>

    <div class="mt-2">
        <div class="card-body p-4 ">

            <div class="row">
                <?php if($admin_type != '3'){
                 if($admin_type != '2'){ ?>

                    <div class="form-group col-lg-6">
                        <span>Manager<span class="text-danger">*</span></span>
                        <select aria-label="Manager []" class="form-select js-example-basic-single" id="manager_id" name="manager_id[]" multiple="multiple" >
                            <option value="0"> Select Any one </option>
                            <?php foreach ($application_users as $row): ?>
                                <option value="<?php echo $row['id']; ?>">
                                    <?php echo $row['display_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="d-none text-danger" id="manager_id_err"></div>
                    </div>
                <?php } ?>

                <div class="form-group col-lg-6">
                    <span>User <span class="text-danger">*</span></span>
                    <select aria-label="User []" class="form-select js-example-basic-single" id="user_id" name="user_id[]" multiple="multiple">
                        <option value="0"> Select Any one </option>

                    </select>
                    <div class="d-none text-danger" id="user_id_err"></div>
                </div>
                <?php } else{?>
                    <input type="hidden" name="user_id" id="user_id_2" value="<?php echo $_SESSION['userid'] ?>">
                <?php  } ?>

                <div class="col-lg-4 col-xs-6 form-group">
                    <span>From Date <span class="text-danger">*</span></span>
                    <input aria-label="From Date" class="form-control" type="date" name="from_date" id="from_date" value="" >
                    <div class="d-none text-danger" id="from_date_err"></div>
                </div>
                <div class="col-lg-4 col-xs-4 form-group">
                    <span>To Date <span class="text-danger">*</span></span>
                    <input aria-label="To Date" class="form-control" type="date" name="to_date" id="to_date" value="">
                    <div class="d-none text-danger" id="to_date_err"></div>
                </div>
                <div class="col-lg-4 col-xs-6 form-group ">
                    <span> &nbsp;</span>
                    <button type="button" class="btn btn-primary form-control " id="getDataButton">Search</button>
                </div>
            </div>

            <!-- Daily Plan -->
            <div class="row">
                <div id="workType1" class="work-type d-none">

                    <table class="table table-striped table-bordered nowrap dataTable" id="fiedWorkTable">
                        <thead>
                            <tr>
                                <th scope="col">Sl No.</th>
                                <th scope="col">User</th>
                                <th scope="col">Location Name</th>
                                <th scope="col">Latitude </th>
                                <th scope="col">Longitude </th>
                                <th scope="col">From Date</th>
                                <th scope="col">To Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/custom_list_field_work_daily_plans.js"></script>
