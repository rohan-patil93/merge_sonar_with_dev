<?php
$furl = API . "?mode=togetuserfielddashboard&method=get&moduletype=".$_SESSION['working_role_short_code'];
$field_res_call = callAPI($furl);
$field_res = json_decode($field_res_call, 1);
$field_tabs = $field_res['alldatamonth'];
?>

<script src="assets/js/custom_view_field_employee_dashboard.js"></script>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-blue">
            <div class="card-body">
                <div class="row align-items-center m-b-30">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Planned Visits</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $field_res['user_daily_plan']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database text-c-blue f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-primary m-r-10">+12%</span>From This Month</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-red">
            <div class="card-body">
                <div class="row align-items-center m-b-30">
                    <div class="col">
                        <h6 class="m-b-5 text-white">User Total Visited</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $field_res['all_user_map']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-danger m-r-10">+11%</span>From This Month</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-green">
            <div class="card-body">
                <div class="row align-items-center m-b-30">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Visited</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $field_res['all_user_daily_map']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign text-c-green f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-success m-r-10">+52%</span>From This Month</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card card-yellow">
            <div class="card-body">
                <div class="row align-items-center m-b-30">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Extra Visits</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $field_res['all_user_extra_map']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags text-c-yellow f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-warning m-r-10">+52%</span>From This Month</p>
            </div>
        </div>
    </div>


    <div class="col-xl-6 col-md-6">
        <div class="card new-cust-card">
            <div class="card-header">
                <h5>Today Visited List</h5>

            </div>
            <div class="card-block">
                <iframe title="Field Employee Dashboard" src="https:
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6">
        <div class="card new-cust-card" id="tablist">

            <?php include_once 'custom_filed_employee_tab_ajax.php'; ?>
        </div>
    </div>
</div>
