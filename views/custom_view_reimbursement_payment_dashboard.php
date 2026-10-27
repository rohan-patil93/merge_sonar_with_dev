<?php
$_SESSION['apply_js']['amchart'] = '1';
$_SESSION['apply_js']['float'] = '1';
$_SESSION['apply_css']['widgetcss'] = '1';
$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
$admindashboardurl = API."?mode=getreimbursementpaymentdashboard&emp_id=".$_SESSION['emp_id']."&roleshortcode=".$roleshortcode."&admin_type=".$_SESSION['admin_type'];
$result = callAPI($admindashboardurl);
$result = json_decode($result,1);
$employees_cnt = $result['employees_cnt'];
$emp_expenses_cnt = $result['emp_expenses_cnt'];
$self_emp_expenses = $result['all_emp_expenses'];
$holidaydata = $result['holidays'];
$holidays = nearestDateFirstShow($holidaydata,'holiday_date');
$total_holidays = count((array)$holidaydata);
$expensescnt =$selfpaymentscnt =  0;
foreach($self_emp_expenses as $see){
  $expensescnt++;
  if($see['current_status'] == ST_APPROVED){
      $selfempexpenses[ST_APPROVED][] = $see;
      if($see['pending_amount'] > 0){
        $selfempexpenses['yettosubmit'][] = $see;
      }
  }else{
    $selfempexpenses['pending'][] = $see;
  }
}

$self_payments = $result['self_payments'];

foreach($self_payments as $sp){
  $selfpaymentscnt++;
  if($sp['current_status'] == ST_APPROVED){
      $selfpayments[ST_APPROVED][] = $sp;
  }elseif($sp['current_status'] == ST_DRAFT || $sp['current_status'] == ST_ACTIVE){
     $selfpayments['yettosubmit'][] = $sp;
  }else{
     $selfpayments['pending'][] = $sp;
  }
}

$transaction_approval_logs = $result['transaction_approval_logs'];
foreach($transaction_approval_logs as $tal){
  if($tal['response_type'] == 0){
      $transactionapprovallogs['pending'][] = $tal;
  }else{
      $transactionapprovallogs['approved'][] = $tal;
  }

}
$status_progress = $admindashboardd['status_progress'];
$statusprogress = array();
foreach($status_progress as $sp) {
  $statusprogress[$sp['status']] = $sp['name'];
}

?>
<script>
  function dashboardpopup(option,title){
    jQuery.ajax({
      url: "views/custom_view_dashboard_popup.php",
      data:'option='+option+'&table=emp_expenses&title='+title,
      type: "POST",
      success:function(data){
        console.log(data);
        $('#popupm').html(data);
        $('#kt_modal_KTDatatable_remotem').modal("show");
      },
      error:function (){}
    });
  }

  function slidepopupdetails(id){
    closedPopup();
    $(".custom-popup-slide-"+id).show('slow');
  }
  function closedPopup(){
    $(".custom-popup-open").hide('fast');
  }
  function expensesDetailsPopUp(emp_id,exp_id="",sub_id=""){
    var table_name = 'project_timesheets';
        jQuery.ajax({
         url: "views/custom_insert_emp_expense_details_popup.php",
         data:'table='+table_name+'&exp_id='+exp_id+'&sub_id='+sub_id+'&emp_id='+emp_id,
         type: "POST",
         success:function(data){
          $('#dynamic_popup11').html(data);
          $('#kt_modal_dynamic_popup2').modal("show");
        },
        error:function (){}
      });
      }

      function statuspopup(status,id){
     var table_name = document.getElementById('table_name').value;
     jQuery.ajax({
      url: "views/custom_insert_status_popup.php",
      data:'table='+table_name+'&status='+status+'&rid='+id,
      type: "POST",
      success:function(data){
        $('#popup1').html(data);
        $('#kt_modal_KTDatatable_remote').modal("show");
      },
      error:function (){}
    });
   }

   function displaystausdrop(id,table,status_id){
    $.ajax({
      url: "ajax.php",
      data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
      type: "POST",
      success:function(data){
        console.log(data);
        $("#dropdownlist_"+id).html(data);
      },
      error:function (){}
    });

  }
  function insertEmployeePopUp(emp_id=""){

        $('#kt_modal_dynamic_popup').modal("show");
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#dynamic_popup1').html(htmloader);

        jQuery.ajax({
            url: "views/custom_insert_building_employee_popup.php",
            data:'emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);

                $(".js-example-basic-single").select2();
                $('.tooltip').remove();
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
  function reimbursementsChart(){
    var reimbursementsyear = $("#reimbursementsyear").val();
    console.log(reimbursementsyear);
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getreimbursementdashboardchart&reimbursementsyear='+reimbursementsyear+'&paymentsdashboard=Y',
        type: "POST",
        success:function(resp){
               var res =  JSON.parse(resp);
    var chart = AmCharts.makeChart("deal-analytic-reimbursement-chart", {
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
            "title": "Claims",
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
</script>

<div class="banner">
  <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
  <div class="banner-text">
    <h2>Start managing expenses and reimbursements with ease! </h2>
    <p>Helps you in managing: Expenses And Reimbursements. Billing Module does this all through your modern Web Browser. Be assured to track your bills and payments from anywhere anytime.</p>
    <a href="index" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer"> Claims</a>
    <?php $_SESSION['payment_transaction_type'] = 'employees'; ?>
    <?php if(in_array(CLAIM_EXECUTIVE_ROLE,(array)$_SESSION['role_ids']) || $_SESSION['admin_type'] == 1){ ?>
      <a class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer" href="#!" onclick="insertEmployeePopUp()"> Add Employee</a>

       <a href="index.php?option=COM_LIST_PAYMENTS" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer">Make Payments</a>
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
            <a href="#"><div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['total_cnt'] !=""?$emp_expenses_cnt['total_cnt']:0;?> BillS</div>
              <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills</p>
            </a>
          </div>
        </div>

        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3  m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
             <a href="#">
              <div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['total_approved_cnt'] !=""?$emp_expenses_cnt['total_approved_cnt']:0;?> BillS</div>
              <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills Cleared</p>
            </a>
          </div>
        </div>
        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3 m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
           <a href="#">
            <div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['inprocess_cnt'] !=""?$emp_expenses_cnt['inprocess_cnt']:0;?> BillS </div>
            <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills Pendding</p>
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
        <h5>Employee Bills/Payments</h5>
        <div class="float-right">
            <select aria-label="Reimbursementsyear" class ="form-control js-example-basic-single" id="reimbursementsyear" onchange="reimbursementsChart()">
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
      <div class="card-block" >
        <div id="deal-analytic-reimbursement-chart" class="chart-shadow" style="height:300px"></div>
      </div>
    </div>
  </div>
  <div class="col-md-12 col-xl-4">
    <div class="card sale-card">
      <div class="card-header">
        <h5>Bills Till</h5>
      </div>
      <?php
        $tamount = $emp_expenses_cnt['total_amount'];
        $pending_amount= $emp_expenses_cnt['pending_amount'];
        $paidamount= $emp_expenses_cnt['paid_amount'];
        $percengraf = 0;
        if($paidamount !="" && $paidamount !=0){
          $percengraf = customNanAndEmptyValidation(round(($paidamount/$tamount)*100),1);
        }       ?>
      <div class="card-block text-center">
        <div class="m-b-20" style="text-align: -webkit-center;">
          <div class="circle_gradiant mb-2" style="--p1: <?php echo ($percengraf*3.6).'deg';?>; --p1_color: #dc3545;--p2_color: #2ed8b6;">
            <span class="text-dark f-16 f-w-600 d-block"><?php echo $percengraf; ?>% </span>
          </div>
        </div>
        <h6 class="m-t-40">Total Bills</h6>
        <h3 class="f-w-700 m-b-40"><?php echo convert_Rupee_Number($tamount); ?></h3>
        <div class="row">
          <div class="col-4">
            <p class="m-b-5">Total Employees</p>
            <h3 class="f-w-700 text-c-yellow"><?php echo $employees_cnt; ?></h3>
          </div>
          <div class="col-4">
            <p class="m-b-5">Paid</p>
            <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($paidamount); ?></h3>
          </div>
          <div class="col-4">
            <p class="m-b-5">Un-Paid</p>
            <h3 class="f-w-700 text-c-yellow"><?php echo convert_Rupee_Number($pending_amount); ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
<ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#expenseslist" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-check-circle f-20"></i> Expenses <span class="form-label label label-xs label-success m-l-10"><?php echo $expensescnt; ?></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#paymentslist" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-shopping-basket f-20"></i> Payments <span class="form-label label label-xs label-success m-l-10"><?php echo $selfpaymentscnt; ?></span></a>
  </li>
   <li class="nav-item">
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#approvalpayments" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-shopping-basket f-20"></i> Approvals <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$transactionapprovallogs['pending']); ?></span></a>
  </li>
</ul>
<div class="tab-content tabs">
  <div class="tab-pane card-body active p-0" id="expenseslist" role="tabpanel" >
    <div class="row">
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>In Process</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive">
                  <table id="basic-btn"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0;  $mq = 1; if(count((array)$selfempexpenses['pending'])!='0'){
                        foreach($selfempexpenses['pending'] as $empe){
                          $transrid = deocdeId("id:".$empe['id']);
                           $totalamt =  $totalamt+ $empe['total_amount'];
                          ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
                          <td><?php echo fromDate($empe['expense_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['total_amount']); ?></td>
                        </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approved</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_2"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                   <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfempexpenses[ST_APPROVED])!='0'){
                        foreach($selfempexpenses[ST_APPROVED] as $empe){
                          $transrid = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['total_amount'];
                          ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                           <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
                          <td><?php echo fromDate($empe['expense_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['total_amount']); ?></td>
                          </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Pending Payments</h5>

            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive">
                  <table id="basic-btn_3"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Pending Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfempexpenses['yettosubmit'])!='0'){
                        foreach($selfempexpenses['yettosubmit'] as $empe){
                          $transrid = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['pending_amount'];
                          ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
                          <td><?php echo fromDate($empe['expense_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['pending_amount']); ?></td>
                        </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>

      </div>
  </div>

    <div class="tab-pane card-body p-0" id="paymentslist" role="tabpanel">
        <div class="row">
                    <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Yet To Submit</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfpayments['yettosubmit'])!='0'){
                        foreach($selfpayments['yettosubmit'] as $empe){ $transrid = deocdeId("id:".$empe['id']);
                         $totalamt =  $totalamt+ $empe['amount'];
                         ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['amount']); ?></td>
                        </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>In Process</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_4"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfpayments['pending'])!='0'){
                        foreach($selfpayments['pending'] as $empe){ $transrid = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['amount'];
                           ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['amount']); ?></td>
                        </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approved</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_5"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                   <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfpayments[ST_APPROVED])!='0'){
                        foreach($selfpayments[ST_APPROVED] as $empe){ $transrid = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['amount'];
                           ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $transrid; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['payment_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['amount']); ?></td>
                          </tr>
                    <?php $mq++;
}
}else{
                          echo "<tr><td colspan='4' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills </h4></td></tr>";
                        } ?>
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>
        </div>
    </div>

  <div class="tab-pane card-body p-0" id="approvalpayments" role="tabpanel">
      <div class="row">
        <div class="col-lg-6">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approval Pending</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table  id="basic-btn_6"  class="table table-hover m-b-0" style="font-size:13px;">
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
                      <?php $totalamt = 0; $mq = 1; if(count((array)$transactionapprovallogs['pending'])!='0'){
                        foreach($transactionapprovallogs['pending'] as $empe){ $ridrecengtbils = deocdeId("id:".$empe['id']);
                        $totalamt =  $totalamt+ $empe['amount'];?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                            <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
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
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
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
                  <table  id="basic-btn_7"  class="table table-hover m-b-0" style="font-size:13px;">
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
                      <?php  $totalamt = 0; $mq = 1; if(count((array)$transactionapprovallogs['approved'])!='0'){
                        foreach($transactionapprovallogs['approved'] as $empe){
                            $ridrecengtbils = deocdeId("id:".$empe['id']);
                            $totalamt = $totalamt+ $empe['amount'];
                             ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_PAYMENTS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
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
            <div class="mt-3 p-3">
                  <span class="text-warning float-left">Total</span>
                  <span class="text-warning float-right f-w-700"><?php echo customeummarmonyformateifine($totalamt); ?></span>
            </div>
          </div>
        </div>
  </div>

</div>

  <div class="page-body mt-4 border-bottom">
    <div class="width-77">
      <div class="row">
        <div class="col-lg-6 col-xl-6 col-sm-6 col-12">
         <div class="card social-card custom_hover_card" style="min-height: 365px;">
          <div class="card-body text-center">
           <div class="text-facebook m-b-20"><i class="ti-announcement "></i></div>
           <h3 class="text-facebook f-w-700">Announcements</h3>
           <?php
           $anouncement =  API."?mode=togetlatestannouncement";
           $anouncement_list = callAPI($anouncement);
           $anouncement_list = json_decode($anouncement_list,1);
           ?>
           <p><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
           <p class="m-b-0 m-t-15"><a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light"><?php echo $anouncement_list['button_label']; ?></a></p>
         </div>
       </div>
     </div>
     <div class="col-lg-6 col-xl-6 col-sm-6 col-12">
          <div class="card sale-card mb-2">
          <div class="card-header">
            <h5>Holidays </h5>
            <div class="card-header-right table-card-header ">
              <span class="text-dark f-w-600"><?php echo date('Y');?></span>
            </div>
          </div>
          <div class="card-block custom-scroll" style=" height: 300px;">
            <?php
            if(!empty($holidays)) {
              foreach ($holidays as  $holiday) {

                $Notification_Message = $holiday['holiday_name'];
                $Notification_DateTime = date('d M',strtotime($holiday['holiday_date']));
                ?>
                <div class="align-middle m-b-10" style="border-bottom:1px dashed #cccccc;">

                  <div class="">
                    <p class="text-muted m-b-5 f-12"><?php echo $Notification_Message; ?>  <span class="float-right"><?php echo $Notification_DateTime; ?></span></p>
                  </div>
                </div>
              <?php } ?>
              <?php } else {
                echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
              } ?>
            </div>

          </div>
      </div>
