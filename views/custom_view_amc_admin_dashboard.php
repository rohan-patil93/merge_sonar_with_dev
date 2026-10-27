<?php
$_SESSION['apply_js']['amc_dashboard_js']="1";
$url = API."?mode=getamcdashboarddata&admin_type=".$_SESSION['admin_type'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$amcstnreq['0']['staus'] ='Total';
$amcstnreq['1']['staus'] ='Active';
$amcstnreq['2']['staus'] ='Inprogress';
$amcstnreq['3']['staus'] ='Approved';
$amcstnreq['0']['cnt'] = $details['stationery_request_total']!="" ? $details['stationery_request_total']: 0;
$amcstnreq['1']['cnt'] =$details['stationery_request_active']!="" ? $details['stationery_request_active']: 0;
$amcstnreq['2']['cnt'] =$details['stationery_request_process']!="" ? $details['stationery_request_process']: 0;
$amcstnreq['3']['cnt'] =$details['stationery_request_approved']!="" ? $details['stationery_request_approved']: 0;
$amccomreq['0']['staus'] ='Total';
$amccomreq['1']['staus'] ='Active';
$amccomreq['2']['staus'] ='Inprogress';
$amccomreq['3']['staus'] ='Closed';
$amccomreq['0']['cnt'] = $details['complaints_total']!="" ? $details['complaints_total']: 0;
$amccomreq['1']['cnt'] =$details['complaints_active']!="" ? $details['complaints_active']: 0;
$amccomreq['2']['cnt'] =$details['complaints_process']!="" ? $details['complaints_process']: 0;
$amccomreq['3']['cnt'] =$details['complaints_closed']!="" ? $details['complaints_closed']: 0;

?>
<style type="text/css">
    .bgtopbt{
            margin-bottom: 10px !important;
    }
</style>

<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<div class="row">
    <div class="col-lg-2">
        <div class="card ticket-card bg-info bgtopbt" >
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['stationery_items_count'] !=""?$details['stationery_items_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Stationery Total Items</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-danger bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['stationery_low_stock_count'] !=""?$details['stationery_low_stock_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Stationery Under Minmum Qty </p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-warning bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['stationery_grn_count']!=""?$details['stationery_grn_count']:0; ?></h2>
                     <p class="m-b-0 m-t-5">Stationery GRN Qty </p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-primary bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['stationery_request_total']!=""?$details['stationery_request_total']:0; ?></h2>
                     <p class="m-b-0 m-t-5">Stationery Requests </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Stationery Requests</h5>
            </div>
            <div class="card-block">
                <input type="hidden" id="ams_strq_json" value='<?php echo json_encode($amcstnreq); ?>'>
                <div id="asset_strq_chart" class="demo-placeholder" style="height:330px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Complaints </h5>
            </div>
            <div class="card-block">
                <input type="hidden" id="amc_comp_json" value='<?php echo json_encode($amccomreq); ?>'>
                <div id="amc_comp_chart" style="width: 100%; height: 330px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="card ticket-card bg-info bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['products_count'] !=""?$details['products_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total AMC Products</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-success bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['services_count']!=""?$details['services_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total  Services </p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-warning bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['service_companies_count']!=""?$details['service_companies_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total AMC Service Companies</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-primary bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['customers_count']!=""?$details['customers_count']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total AMC  Customers</p>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="row mt-4">
    <div class="col-lg-10 assetstable">
        <?php
            if(file_exists("views/custom_view_amc_detail_dashboard_ajax.php")){
                include_once "views/custom_view_amc_detail_dashboard_ajax.php";
            }else{
                include_once "custom_view_amc_detail_dashboard_ajax.php";
            }
        ?>
    </div>
    <div class="col-lg-2">
        <div class="card ticket-card bg-info bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['amc_registration_total'] !=""?$details['amc_registration_total']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total AMC Registrations</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-success bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['amc_registration_schedule']!=""?$details['amc_registration_schedule']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Total Registration Scheduled </p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-warning bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['amc_registration_pending']!=""?$details['amc_registration_pending']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Pending Registration Schedules</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-danger bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['amc_registration_closed']!=""?$details['amc_registration_closed']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Schedule Registration Closed</p>
                </div>
            </div>
        </div>
        <div class="card ticket-card bg-primary bgtopbt">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block "><?php echo $details['amc_registration_running']!=""?$details['amc_registration_running']:0; ?></h2>
                    <p class="m-b-0 m-t-5">Running AMC Registrations</p>
                </div>
            </div>
        </div>
    </div>
</div>

