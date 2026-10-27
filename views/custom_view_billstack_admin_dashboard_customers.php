<div class="banner">
            <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
            <div class="banner-text">
                <h2>Start managing Sales invoices with ease!</h2>
                <p>Helps you in managing: Sales bills and Receipts. BillsStack does this all through your modern Web Browser. Be assured to track your bills and receipts from anywhere anytime.</p>
                <a href="index.php?option=COM_INSERT_SALES_INVOICE_DYNAMIC" class="btn-white b-radius-5 text-uppercase f-w-600">Upload Invoices</a>
                <a href="index.php?option=COM_INSERT_SALES_INVOICES" class="btn-white b-radius-5 text-uppercase f-w-600">Create Invoice</a>

                 <a href="index.php?option=COM_LIST_RECEIPTS" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10">Receipts</a>
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
                                <a href="index.php?option=COM_LIST_CUSTOMERS"><div class="card-title f-w-600 f-16"><?php echo count((array)$customers)." Customers"?></div>
                                    <p class="card-subtitle text-muted mt-1 f-12"> Customers Who Are Active</p>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center col-md">
                            <div class="rounded-circle bg-primary p-3  m-r-10">
                                <i class="fa fa-shopping-basket f-20"></i>
                            </div>
                            <div class="flex">
                               <a href="index.php?option=COM_LIST_SALES_INVOICES">
                                <div class="card-title f-w-600 f-16"><?php echo $admindashboardd['sales_invoices_cnt']!=""?$admindashboardd['sales_invoices_cnt']:0;?> Invoices</div>
                                <p class="card-subtitle text-muted mt-1 f-12"> Sales Invoices Created In the System</p>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center col-md">
                            <div class="rounded-circle bg-primary p-3 m-r-10">
                                <i class="fa fa-check-circle f-20"></i>
                            </div>
                            <div class="flex">
                               <a href="index.php?option=COM_LIST_SALES_INVOICES_TABLE">
                                    <div class="card-title f-w-600 f-16"><?php echo $admindashboardd['si_cnt']!=""?$admindashboardd['si_cnt']:0; ?> Invoices</div>
                                    <p class="card-subtitle text-muted mt-1 f-12">Sales Invoices Approved In the System</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xl-8">
                <div class="card sale-card">
                    <div class="card-header">
                        <h5>Customers Invoice/Payments</h5>
                        <div class="float-right">
                            <select aria-label="Saleschartyear" class ="form-control js-example-basic-single" id="saleschartyear" onchange="salesChart()">
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
                    <div class="card-block">
                        <div id="deal-analytic-saless-chart" class="chart-shadow" style="height:300px"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-4">
                <div class="card sale-card">
                    <div class="card-header">
                        <h5>Customers Invoice</h5>
                    </div>
                    <div class="card-block text-center">
                         <?php

                            $tamount = $customerinvoicecnt['total_amount'];
                            $totalpaidamount= round($customerinvoicecnt['total_amount'])-round($customerinvoicecnt['total_pending']);
                            $percengraf = 0;
                            if($tamount !="" && $tamount !=0){
                              $percengraf = customNanAndEmptyValidation(round(($totalpaidamount/$tamount)*100),1);
                            }
                          ?>
                        <div class="m-b-20" style="text-align: -webkit-center;">
                              <div class="circle_gradiant mb-2" style="--p1: <?php echo ($percengraf*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
                                <span class="text-dark f-16 f-w-600 d-block"><?php echo $percengraf; ?>% </span>
                            </div>
                        </div>
                        <h6 class="m-t-40">Total Invoices</h6>
                        <h3 class="f-w-700 m-b-40"><?php echo convert_Rupee_Number($tamount); ?> </h3>
                        <div class="row">
                            <div class="col-4">
                                <p class="m-b-5">Paid</p>
                                <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($totalpaidamount); ?></h3>
                            </div>
                            <div class="col-4">
                                <p class="m-b-5">Payment Due</p>
                                <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($customerinvoicecnt['payment_due']); ?></h3>
                            </div>
                            <div class="col-4">
                                <p class="m-b-5">Yet To Due</p>
                                <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($customerinvoicecnt['yet_to_due']); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                  <div class="card table-card">
                    <div class="card-header">
                      <h5>Approval Pending</h5>
                    </div>
                    <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                        <div class="card-block table-responsive" >
                          <table   class="table table-hover m-b-0" style="font-size:13px;">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Number</th>
                                <th scope="col">Request Date</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col" class="text-right">Price</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $mq = 1; if(count((array)$transactionapprovallogscustomer['pending'])!='0'){
                                foreach($transactionapprovallogscustomer['pending'] as $empe){ $ridrecengtbils = deocdeId("id:".$empe['id']); ?>
                                  <tr>
                                  <td class="p-2"><?php echo $mq; ?></td>
                                    <td class="p-2"><a class="text-primary" href="index?option=COM_INSERT_SALES_INVOICES&Rid=<?php echo $ridrecengtbils; ?>"><?php echo $empe['transaction_no']; ?></a></td>
                                  <td class="p-2"><?php echo fromDate($empe['request_date']); ?></td>
                                  <td class="p-2"><?php echo fromDate($empe['payment_date']); ?></td>
                                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($empe['amount']); ?></td>
                                </tr>
                            <?php $mq++;
}
}else{
                                  echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Approval Pending</h4></td></tr>";
                                } ?>
                              </tbody>
                            </table>
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
                          <table class="table table-hover m-b-0" style="font-size:13px;">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Number</th>
                                <th scope="col">Response Date</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col" class="text-right">Price</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $mq = 1; if(count((array)$transactionapprovallogscustomer['approved'])!='0'){
                                foreach($transactionapprovallogscustomer['approved'] as $empe){
                                    $ridrecengtbils = deocdeId("id:".$empe['id']);
                                     ?>
                                  <tr>
                                  <td class="p-2"><?php echo $mq; ?></td>
                                  <td class="p-2"><a class="text-primary"  href="index?option=COM_INSERT_SALES_INVOICES&Rid=<?php echo $ridrecengtbils; ?>"><?php echo $empe['transaction_no']; ?></a></td>
                                  <td class="p-2"><?php echo fromDate($empe['response_date']); ?></td>
                                  <td class="p-2"><?php echo fromDate($empe['payment_date']); ?></td>
                                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($empe['amount']); ?></td>
                                </tr>
                            <?php $mq++;
}
}else{
                                  echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Approved</h4></td></tr>";
                                } ?>
                              </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body border-bottom">
                        <div class="width-77">
                            <div class="row">
                                <div class="card-header">
                                    <h5>TOP 3 Customers</h5>
                                    <div class="card-header-right table-card-header ">
                                        <a href="index.php?option=COM_LIST_CUSTOMERS" class=" b-b-primary text-primary float-right">View all</a>
                                    </div>
                                </div>
                                <?php
                                $bg_color_array = array('bg-primary','bg-success','bg-info','bg-danger');
                                $tven = 0;
                                if(count((array)$admindashboardd['customes_list']) != 0){
                                    foreach($admindashboardd['customes_list'] as $vendorslist){

                                        $tamount = $vendorslist['total_amount'];
                                        $pending_amount= $vendorslist['total_amount']-$vendorslist['pending_amount'];
                                        $percengraf = 0;
                                        if($tamount !="" && $tamount !=0){
                                        $percengraf = customNanAndEmptyValidation(round(($pending_amount/$tamount)*100),1);
}

                                      ?>
                                        <div class="col-xl-4 col-md-12">
                                            <a href="index?option=COM_INSERT_CUSTOMERS&Rid=<?php echo deocdeId("id:".$vendorslist['id']);?>">
                                                <div class="card <?php echo $bg_color_array[$tven]; ?>" style="min-height:100px">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto">
                                                                <?php
                                                                if($vendorslist['image']!=""){ ?>
                                                                   <img src="<?php echo "uploads/all/".$vendorslist['image']; ?>" class="img-radius img-50" alt="">
                                                               <?php }else{ ?>
                                                                <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round p-2 f-1 text-center bg-white text-mute"><?php echo initials($vendorslist['display_name']); ?></span>
                                                            <?php } ?>

                                                        </div>
                                                        <div class="col text-end">
                                                            <i class="fa fa-rupee text-white d-inline"></i>
                                                            <h6 class="m-b-0 text-white d-inline"><?php echo convert_Rupee_Number($vendorslist['total_amount']); ?></h6>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="m-b-0 text-white"> <i class="fa fa-briefcase"></i> <?php echo $vendorslist['company']; ?></p>
                                                        <a href="#" class="text-white m-b-0 f-16"> <i class="ti-user"></i> <?php echo $vendorslist['display_name']; ?></a>
                                                    </div>
                                                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3" style="min-width: 200px;">
                                                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                            <span class="fw-bold fs-6 text-gray-400">Payments</span>
                                                            <span class="fw-bolder fs-6 ml-2"><?php echo $percengraf; ?>%</span>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="progress  progress-xs">
                                                                <div class="progress-bar progress-bar-pink " role="progressbar" style="width: <?php echo $percengraf; ?>%;" aria-valuenow="<?php echo $percengraf; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </a>
                                    </div>
                                    <?php $tven++;
} ?>
                                <?php }else{?>
                                    <div class="card-px text-center py-20 m-t-30">
                                      <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Customers</p>
                                      <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="page-body mt-4 border-bottom">
                    <div class="width-77">
                        <div class="row">
                            <div class="card-header">
                                <h5>Recent Customers</h5>
                                <div class="card-header-right table-card-header ">
                                    <a href="index.php?option=COM_LIST_CUSTOMERS" class=" b-b-primary text-primary float-right">View all</a>
                                </div>
                            </div>
                            <?php if(count((array)$admindashboardd['customers_recent_join']) != 0){ foreach($admindashboardd['customers_recent_join'] as $vendorsrecentjoin){
                                  $tamount = $vendorsrecentjoin['total_amount'];
                                  $pending_amount= $vendorsrecentjoin['total_amount']-$vendorsrecentjoin['pending_amount'];
                                  $percengraf = 0;
                                  if($tamount !="" && $tamount !=0){
                                  $percengraf = customNanAndEmptyValidation(round(($pending_amount/$tamount)*100),1);
}
                              ?>
                            <div class="col-xl-4 col-md-12">
                                <a href="index?option=COM_INSERT_CUSTOMERS&Rid=<?php echo deocdeId("id:".$vendorsrecentjoin['id']);?>">
                                <div class="card proj-t-card" style="min-height: 90px;">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                               <?php
                                               if($vendorsrecentjoin['image']!=""){ ?>
                                                   <img src="<?php echo "uploads/all/".$vendorsrecentjoin['image']; ?>" class="img-radius img-50" alt="">
                                               <?php }else{ ?>
                                                <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round p-2 f-1 text-center bg-white text-mute"><?php echo initials($vendorsrecentjoin['display_name']); ?></span>
                                                <?php } ?></div>
                                                <div class="col p-l-0">
                                                    <h6 class="m-b-5 f-16"><?php echo $vendorsrecentjoin['display_name']; ?></h6>
                                                    <h6 class="m-b-0 text-muted f-12"><?php echo $vendorsrecentjoin['company']; ?></h6>
                                                </div>
                                                <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3" style="min-width: 200px;">
                                                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                    <span class="fw-bold fs-6 text-gray-400">Payments</span>
                                                    <span class="fw-bolder fs-6 ml-2"><?php echo $percengraf; ?>%</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="progress  progress-xs">
                                                        <div class="progress-bar progress-bar-pink " role="progressbar" style="width: <?php echo $percengraf; ?>%;" aria-valuenow="<?php echo $percengraf; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            <?php } ?>
                            <?php }else{?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Customers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                  <div class="page-body mt-4 border-bottom">
                    <div class="width-77">
                        <div class="row">
                            <div class="col-lg-12" >
                                <?php include_once 'views/custom_list_sales_invoices_table.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
