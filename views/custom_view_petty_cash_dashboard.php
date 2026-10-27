<?php
$_SESSION['apply_js']['amchart'] =1;
$_SESSION['apply_js']['multiselest'] = "1";
$startdate = date('Y-m-01');
$enddate = date('Y-m-t');
$url1 = API."?mode=pettycashdashboard&admin_type=".$_SESSION['admin_type']."&startdate=".$startdate."&enddate=".$enddate;
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
$total_cnt = $result['total_cnt'];
$petty_cash_box_owner = $result['petty_cash_box_owner'];
$topup_frequency = $result['topup_frequency'];
$transaction_approval_logs = $admindashboardd['transaction_approval_logs'];
foreach($transaction_approval_logs as $tal){
    if($tal['response_type'] == 0){
        $transactionapprovallogs['pending'][] = $tal;
    }else{
        $transactionapprovallogs['approved'][] = $tal;
    }
}
$dashboardcall = "Y";
?>
<script type="text/javascript">
    function getChartYear(){
        billChart();
        billChart1();
    }


</script>
<div class="banner">
    <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
    <div class="banner-text">
        <h2>Start managing Petty Cash Boxes with ease!</h2>
        <p>Helps you in managing: Petty Cash Boxes Request and Payments. Renatl does this all through your modern Web Browser. Be assured to track your bills and payments from anywhere anytime.</p>
        <a href="index.php?option=COM_INSERT_PETTY_CASH_BOX_TOPUP_REQUESTS" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10"> TopUp Request</a>

        <a href="index.php?option=COM_INSERT_PETTY_CASH" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10"> Create Expenses</a>


    </div>
</div>
<div class="card">
    <div class="card-block">
        <div class="width-77">
            <div class="row align-items-center">
               <div class="d-flex align-items-center col-md" >
                    <div class="rounded-circle bg-primary p-3 m-r-10">
                        <i class="fa fa-user-plus f-20"></i>
                    </div>
                    <div class="flex">
                        <a href="index.php?option=COM_LIST_TENANAT_CUSTOMERS"><div class="card-title f-w-600 f-16"><?php echo $total_cnt['total_petty_cash_box_owner']; ?> Boxes</div>
                            <p class="card-subtitle text-muted mt-1 f-12"> Total Petty Cash Boxes</p>
                        </a>
                    </div>
                </div>
                <div class="d-flex align-items-center col-md">
                    <div class="rounded-circle bg-primary p-3  m-r-10">
                        <i class="fa fa-shopping-basket f-20"></i>
                    </div>
                    <div class="flex">
                       <a href="index.php?option=COM_LIST_RENTAL_INVOICES">
                        <div class="card-title f-w-600 f-16"><?php echo $total_cnt['total_petty_cash_box_topup_requests']; ?> Requests</div>
                        <p class="card-subtitle text-muted mt-1 f-12"> Total Petty Cash Box Top-up Request</p>
                        </a>
                    </div>
                </div>
                <div class="d-flex align-items-center col-md">
                    <div class="rounded-circle bg-primary p-3  m-r-10">
                        <i class="fa fa-shopping-basket f-20"></i>
                    </div>
                    <div class="flex">
                       <a href="index.php?option=COM_LIST_RENTAL_INVOICES">
                        <div class="card-title f-w-600 f-16"><?php echo $total_cnt['total_petty_cash']; ?> Expense</div>
                        <p class="card-subtitle text-muted mt-1 f-12"> Total Expense</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-lg-7"></div>
    <div class="col-lg-3 text-right pt-2">
        <span class="form-label">Topup Request And Expenses Chart Year</span>
    </div>
    <div class="col-lg-2">
       <select aria-label="Chartyear" class ="form-control js-example-basic-single" id="chartyear" onchange="getChartYear()">
          <?php $startyear = date("Y"); for($mk=$startyear-10;$mk <= $startyear; $mk++){
            ?>
            <option  value="<?php echo  $mk; ?>" <?php echo date('Y')==$mk?"selected":"";?>>
              <?php echo $mk; ?>
            </option>
            <?php

         } ?>
       </select>
   </div>
</div>
<div class="row">
    <div class="col-md-12 col-xl-6">
        <div class="card sale-card">
            <div class="card-header">
                <h5>Topup Request</h5>
            </div>
            <div class="card-block">
                <div id="deal-analytic-bills-chart" class="chart-shadow" style="height:300px"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-6">
        <div class="card sale-card">
            <div class="card-header">
                <h5>Expenses</h5>
            </div>
            <div class="card-block">
                <div id="deal-analytic-pettycashexpenses-chart" class="chart-shadow" style="height:300px"></div>
            </div>
        </div>
    </div>
</div>
<div>
     <?php include_once "custom_list_petty_cash_box_topup_requests.php";?>
</div>
<div>
     <?php include_once "custom_list_petty_cash.php";?>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Cash Box Expenses</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <?php if(count((array)$top_rent_res) !=0 ){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                    <thead>
                        <tr>
                            <th scope="col">Tenant Name</th>
                            <th scope="col" class="text-right">Monthly Rent</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($top_rent_res as $rcc){ ?>
                        <tr>
                             <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                            <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rcc['monthly_rent']); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Contract</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row">
        <div class="col-lg-6">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approval Pending</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                     <?php if(count((array)$transactionapprovallogs['pending'])!='0'){  ?>
                  <table   class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Payment Number</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1;
                        foreach($transactionapprovallogs['pending'] as $empe){ $ridrecengtbils = deocdeId("id:".$empe['id']); ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                            <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $ridrecengtbils; ?>"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['request_date']); ?></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo $empe['amount']; ?></td>
                        </tr>
                    <?php $mq++;
}  ?>
                      </tbody>
                    </table>
                     <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approval Pending</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approved</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                    <?php if(count((array)$transactionapprovallogs['approved'])!='0'){  ?>
                  <table class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Payment Number</th>
                        <th scope="col">Response Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1;
                        foreach($transactionapprovallogs['approved'] as $empe){
                            $ridrecengtbils = deocdeId("id:".$empe['id']);
                             ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $ridrecengtbils; ?>"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['response_date']); ?></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo $empe['amount']; ?></td>
                        </tr>
                    <?php $mq++;
}  ?>
                      </tbody>
                    </table>
                    <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approved</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <?php include_once 'views/custom_petty_cash_aging_and_projection_reports.php'; ?>
</div>
