<?php
$_SESSION['apply_js']['amchart'] =1;

if($_SESSION['config']['system_start_date'] !=""){
    $year = date("Y",strtotime($_SESSION['config']['system_start_date']));
    $month = date("m",strtotime($_SESSION['config']['system_start_date']));
}else{
    $year = date("Y");
    $month = date("m");
}
$todaydate =date("Y-m-d");
$admindashboardurl = API."?mode=getrentaladmindashboard&vend_limit=3&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&year=$year&month=$month";
$admindashboardres = callAPI($admindashboardurl);
$admindashboardd = json_decode($admindashboardres,1);
$customers = $admindashboardd['customers'];
$approvedinvoices = $unapprovedinvoices = array();
$rental_invoices_transnumber = array();
foreach($admindashboardd['rental_invoices_list'] as $pending_list){
    if($pending_list['pending_amount'] <='0'){
        $paidinvoicse[] = $pending_list;
    }elseif($pending_list['shared']=='0'){
        $submittedinvoice[] = $pending_list;
    }elseif($pending_list['generated_invoice']=='0'){
        $generatedinvoice[] = $pending_list;
    }
}
foreach($admindashboardd['all_rental_invoices_list'] as $pending_list){
    $rental_invoices_transnumber[$pending_list['id']] = $pending_list['transaction_number'];
}
$transaction_approval_logs = $admindashboardd['transaction_approval_logs'];
foreach($transaction_approval_logs as $tal){
    if($tal['response_type'] == 0){
        $transactionapprovallogs['pending'][] = $tal;
    }else{
        $transactionapprovallogs['approved'][] = $tal;
    }
}
$vendorinvoicecnt = $admindashboardd['vendor_invoice_cnt'];
$tenant_billings  =$admindashboardd['tenant_billings'];

$recent_create_contract =$admindashboardd['recent_create_contract'];

$recent15days = $recent30days =$recent45days = array();
foreach($recent_create_contract as $rcc){
    $todaydate = date('Y-m-d');
    $sdate = new DateTime($rcc['created_date']);
    $edate = new DateTime($todaydate);
    $totald= $sdate->diff($edate);
    $totaldays = $totald->days;
    if($totaldays < 15){
        $recent15days[] = $rcc;
    }elseif($totaldays < 30){
         $recent30days[] = $rcc;
    }elseif($totaldays < 45){
         $recent45days[] = $rcc;
    }
}
$recent_expire_contract =$admindashboardd['recent_expire_contract'];
$expire15days =$expire30days  =$expire45days = array();
foreach($recent_expire_contract as $rcc){
    $todaydate = date('Y-m-d');
    $sdate = new DateTime($rcc['lease_period_end_date']);
    $edate = new DateTime($todaydate);
    $totald= $sdate->diff($edate);
    $totaldays = $totald->days;
    if($totaldays < 15){
        $expire15days[] = $rcc;
    }elseif($totaldays < 30){
         $expire30days[] = $rcc;
    }elseif($totaldays < 45){
         $expire45days[] = $rcc;
    }
}
$recent_renewal_contract =$admindashboardd['recent_renewal_contract'];
$renewal15days =$renewal30days=$renewal45days = array();
foreach($recent_renewal_contract as $rcc){
    $todaydate = date('Y-m-d');
    $sdate = new DateTime($rcc['renewal_date']);
    $edate = new DateTime($todaydate);
    $totald= $sdate->diff($edate);
    $totaldays = $totald->days;
    if($totaldays < 15){
        $renewal15days[] = $rcc;
    }elseif($totaldays < 30){
         $renewal30days[] = $rcc;
    }elseif($totaldays < 45){
         $renewal45days[] = $rcc;
    }
}
$top_rent_res = $admindashboardd['top_rent_res'];
$top_oldest = $admindashboardd['top_oldest'];
?>
<script>

  function billChart(){
    var reimbursementsyear = $("#billchartyear").val();
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getrentaldashboardchart&reimbursementsyear='+reimbursementsyear,
        type: "POST",
        success:function(resp){
               var res =  JSON.parse(resp);
    var chart = AmCharts.makeChart("deal-analytic-bills-chart", {
        "type": "serial",
        "theme": "light",
        "dataDateFormat": "YYYY-MM-DD",
        "precision": 2,
        "valueAxes": [{
            "id": "v1",
            "position": "left",
            "autoGridCount": false,
            "labelFunction": function(value) {
                return "$" + Math.round(value) + "M";
            }
        }, {
            "id": "v2",
            "gridAlpha": 0,
            "autoGridCount": false
        }],
        "graphs": [{
            "id": "g1",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#2ed8b6",
            "title": "Bills",
            "useLineColorForBulletBorder": true,
            "valueField": "market1",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }, {
            "id": "g2",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#e95753",
            "title": "Paid",
            "useLineColorForBulletBorder": true,
            "valueField": "market2",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "legend": {
            "useGraphSettings": true,
            "position": "top"
        },
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "dataProvider": res
    });
        },
        error:function (){}
    });
}
function getRentMonth(monthyear){
    var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#biilingrent').html(htmloader);
    jQuery.ajax({
        url: "views/custom_insert_property_tenant_billings.php",
        data:'monthyear='+monthyear,
        type: "POST",
        success:function(resp){
            $("#biilingrent").html(resp);
        }
    });
}

function OpenInsertRentalInvoicePopUp(billtype=""){
    jQuery.ajax({
        url: "views/custom_insert_rental_invoices_popup.php",
        data: "insert=Y&bill_type="+billtype,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        }
    });
}
</script>

<div class="banner">
    <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
    <div class="banner-text">
        <h2>Start managing Rental invoices with ease!</h2>
        <p>Helps you in managing: Rental bills and Payments. Renatl does this all through your modern Web Browser. Be assured to track your bills and payments from anywhere anytime.</p>
        <button type="button" !' class="btn-white b-radius-5 text-uppercase f-w-600" onclick="OpenInsertRentalInvoicePopUp()">Rent Invoice</button>
        <?php if(RENTAL_INVOICE_SPLIT =="Y"){ ?>
            <button type="button" !' class="btn-white b-radius-5 text-uppercase f-w-600" onclick="OpenInsertRentalInvoicePopUp('U')">Utility  Invoice</button>
        <?php } ?>
            <a href="index.php?option=COM_LIST_TENANAT_CUSTOMERS" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10"> Tenants</a>
        <?php $_SESSION['payment_transaction_type'] = 'customers'; ?>
         <a href="index.php?option=COM_LIST_PAYMENTS" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10">Payments</a>
         <?php if(in_array('RENTAL_ADMIN',(array)$_SESSION['role_ids']) || in_array('ADMIN',(array)$_SESSION['role_ids'])){ ?>
        <a href="index.php?option=COM_LIST_APPLICATION_USER_INVITATIONS" class="btn-white b-radius-5 text-uppercase f-w-600 m-r-10">invite User</a>
        <?php } ?>
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
                    <a href="index.php?option=COM_LIST_TENANAT_CUSTOMERS"><div class="card-title f-w-600 f-16"><?php echo count((array)$customers)." Tenants"?></div>
                        <p class="card-subtitle text-muted mt-1 f-12"> Tenants Who Are Active</p>
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-center col-md">
                <div class="rounded-circle bg-primary p-3  m-r-10">
                    <i class="fa fa-shopping-basket f-20"></i>
                </div>
                <div class="flex">
                   <a href="index.php?option=COM_LIST_RENTAL_INVOICES">
                    <div class="card-title f-w-600 f-16"><?php echo $admindashboardd['supplier_bill_cnt']!=""?$admindashboardd['supplier_bill_cnt']:0;?> Invoices</div>
                    <p class="card-subtitle text-muted mt-1 f-12"> Rental Invoices Created In the System</p>
                </a>
            </div>
        </div>
        <div class="d-flex align-items-center col-md">
            <div class="rounded-circle bg-primary p-3 m-r-10">
                <i class="fa fa-check-circle f-20"></i>
            </div>
            <div class="flex">
               <a href="index.php?option=COM_LIST_RENTAL_INVOICES_TABLE&type=P">
                <div class="card-title f-w-600 f-16"><?php echo count((array)$paidinvoicse); ?> Invoices</div>
                <p class="card-subtitle text-muted mt-1 f-12">Rental Invoices Paid In the System</p>
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
                <h5>Tenants Invoice/Payments</h5>
                <div class="float-right">
            <select aria-label="Billchartyear" class ="form-control js-example-basic-single" id="billchartyear" onchange="billChart()">
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
                <div id="deal-analytic-bills-chart" class="chart-shadow" style="height:300px"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <div class="card sale-card">
            <div class="card-header">
                <h5>Tenants Invoice</h5>
            </div>
            <div class="card-block text-center">
                 <?php

                    $tamount = $vendorinvoicecnt['total_amount'];
                    $totalpaidamount= round($vendorinvoicecnt['total_amount'])-round($vendorinvoicecnt['total_pending']);
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
                        <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($vendorinvoicecnt['payment_due']); ?></h3>
                    </div>
                    <div class="col-4">
                        <p class="m-b-5">Yet To Due</p>
                        <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($vendorinvoicecnt['yet_to_due']); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div>

    <div class="row mb-3">
        <div class="col-lg-8"></div>
        <div class="col-lg-2 text-right">
            <span class="form-label"> Rent Month </span>
        </div>
        <div class="col-lg-2">
            <div class=" form-group mb-0">
                <input aria-label="Month" type="month" class="form-control" name="month" id="month" value="<?php echo $year."-".$month; ?>" onchange="getRentMonth(this.value)" min="<?php echo $_SESSION['config']['system_start_date'] !=""?date("Y-m",strtotime($_SESSION['config']['system_start_date'])):date("Y-m"); ?>">
            </div>
        </div>
    </div>
    <div class="" id="biilingrent">
    <?php include_once "custom_insert_property_tenant_billings.php";?>
    </div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Recently Created Contracts</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body" >
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#recent15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent15days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent30days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 45 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent45days);?></span></a>
                    </li>
                </ul>
                <div class="tab-content tabs">
                    <div class="tab-pane card-body active p-0 custom-scroll" id="recent15days" role="tabpanel"  style=" height: 300px;">
                        <?php if(count((array)$recent15days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Contract Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($recent15days as $rcc){?>
                                <tr>
                                    <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['created_date']); ?></td>
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
                    <div class="tab-pane card-body p-0 custom-scroll" id="recent30days" role="tabpanel" style=" height: 300px;">
                        <?php if(count((array)$recent30days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Contract Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($recent30days as $rcc){?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['created_date']); ?></td>
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
                    <div class="tab-pane card-body p-0 custom-scroll" id="recent45days" role="tabpanel" style=" height: 300px;">
                         <?php if(count((array)$recent45days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Contract Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($recent45days as $rcc){?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['created_date']); ?></td>
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
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Expiring Contracts</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body" >
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#expire15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire15days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expire30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire30days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expire45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 45 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire45days);?></span></a>
                    </li>
                </ul>
                <div class="tab-content tabs">
                    <div class="tab-pane card-body active p-0 custom-scroll" id="expire15days" role="tabpanel" style=" height: 300px;">
                        <?php if(count((array)$expire15days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Expire Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($expire15days as $rcc){ ?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['lease_period_end_date']); ?></td>
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
                    <div class="tab-pane card-body p-0 custom-scroll" id="expire30days" role="tabpanel" style=" height: 300px;">
                         <?php if(count((array)$expire30days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Expire Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($expire30days as $rcc){ ?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['lease_period_end_date']); ?></td>
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
                    <div class="tab-pane card-body p-0 custom-scroll" id="expire45days" role="tabpanel" style=" height: 300px;">
                         <?php if(count((array)$expire45days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Expire Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($expire45days as $rcc){ ?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['lease_period_end_date']); ?></td>
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
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Renewed Contracts</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body" >
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#renewal15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$renewal15days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#renewal30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$renewal30days);?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#renewal45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 45 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$renewal45days);?></span></a>
                    </li>
                </ul>
                <div class="tab-content tabs">
                    <div class="tab-pane card-body active p-0 custom-scroll" id="renewal15days" role="tabpanel" style=" height: 300px;">
                        <?php if(count((array)$renewal15days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Renewal Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($renewal15days as $rcc){ ?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['renewal_date']); ?></td>
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
                    <div class="tab-pane card-body  p-0 custom-scroll" id="renewal30days" role="tabpanel" style=" height: 300px;">
                        <?php if(count((array)$renewal30days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Renewal Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($renewal30days as $rcc){ ?>
                                <tr>
                                     <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['renewal_date']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                             <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Contract</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                        </table>
                    </div>
                    <div class="tab-pane card-body p-0 custom-scroll" id="renewal45days" role="tabpanel" style=" height: 300px;">
                        <?php if(count((array)$renewal45days) !=0 ){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" >
                            <thead>
                                <tr>
                                    <th scope="col">Tenant Name</th>
                                    <th scope="col">Renewal Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($renewal45days as $rcc){ ?>
                                <tr>
                                    <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                                    <td class="p-2"><?php echo fromDate($rcc['renewal_date']); ?></td>
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
        </div>
    </div>
     <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Rent</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body custom-scroll" style=" height: 300px;">
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Oldest Tenants</h5>
                <div class="card-header-right table-card-header ">
                    <a href="index.php?option=COM_INSERT_PROPERTY_TENANT_DETAILS" class=" b-b-primary text-primary float-right">View all</a>
                </div>
            </div>
            <div class="card-body custom-scroll" style=" height: 300px;">
                <?php if(count((array)$top_oldest) !=0 ){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                    <thead>
                        <tr>
                            <th scope="col">Tenant Name</th>
                            <th scope="col" class="text-right">Days</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($top_oldest as $rcc){ ?>
                        <tr>
                            <td class="p-2"><a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId("id:".$rcc['id']);?>" target="_blank" class="text-primary"><?php echo $rcc['tenant_name']; ?></a></td>
                            <td class="p-2 text-right"><?php echo $rcc['days']; ?></td>
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 5 Efficient Payment</h5>
            </div>
            <div class="card-body custom-scroll" style=" height: 300px;">
                <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Contract</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
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
                        <th scope="col">Payment Number</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1; if(count((array)$transactionapprovallogs['pending'])!='0'){
                        foreach($transactionapprovallogs['pending'] as $empe){ $ridrecengtbils = deocdeId("id:".$empe['id']); ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                            <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $ridrecengtbils; ?>"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['request_date']); ?></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo $empe['amount']; ?></td>
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
                        <th scope="col">Payment Number</th>
                        <th scope="col">Response Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1; if(count((array)$transactionapprovallogs['approved'])!='0'){
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
            <?php if(count((array)$admindashboardd['vendors_list']) != 0){ ?>
            <div class="page-body border-bottom">
                <div class="width-77">
                    <div class="row">
                        <div class="card-header">
                            <h5>TOP 3 Tenants</h5>
                            <div class="card-header-right table-card-header ">
                                <a href="index.php?option=COM_LIST_TENANAT_CUSTOMERS" class=" b-b-primary text-primary float-right">View all</a>
                            </div>
                        </div>
                        <?php
                        $bg_color_array = array('bg-primary','bg-success','bg-info','bg-danger');
                        $tven = 0;

                            foreach($admindashboardd['vendors_list'] as $vendorslist){

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
                                                           <img src="<?php echo "uploads/customer/".$vendorslist['image']; ?>" class="img-radius img-50" alt="">
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
                                                <span class="text-white m-b-0 f-16"> <i class="ti-user"></i> <?php echo $vendorslist['display_name']; ?></span>
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
                </div>
            </div>
            <?php } ?>
        </div>
        <?php    if(count((array)$admindashboardd['vendors_recent_join']) != 0){ ?>
        <div class="page-body mt-4 border-bottom">
            <div class="width-77">
                <div class="row">
                    <div class="card-header">
                        <h5>Recent Customers</h5>
                        <div class="card-header-right table-card-header ">
                            <a href="index.php?option=COM_LIST_TENANAT_CUSTOMERS" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                    </div>

                    <?php foreach($admindashboardd['vendors_recent_join'] as $vendorsrecentjoin){

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
                                                   <img src="<?php echo "uploads/customer/".$vendorsrecentjoin['image']; ?>" class="img-radius img-50" alt="">
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
            </div>
        </div>
    </div>
<?php } ?>
    <div class="page-body mt-4 border-bottom">
        <div class="width-77">
            <div class="row">
                <div class="col-lg-12" >
                    <?php include_once 'views/custom_rental_aging_and_projection_reports.php'; ?>
                </div>
                <div class="col-lg-12" >
                    <?php include_once 'views/custom_list_rental_invoices_table.php'; ?>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
