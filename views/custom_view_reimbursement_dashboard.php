<?php
$_SESSION['apply_js']['amchart'] = '1';
$_SESSION['apply_js']['float'] = '1';
$_SESSION['apply_css']['widgetcss'] = '1';
$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
$admindashboardurl = API."?mode=getreimbursementdashboard&emp_id=".$_SESSION['emp_id']."&roleshortcode=".$roleshortcode."&admin_type=".$_SESSION['admin_type'];
$result = callAPI($admindashboardurl);
$result = json_decode($result,1);
$employees_cnt = $result['employees_cnt'];

$emp_expenses_cnt = $result['emp_expenses_cnt'];
$self_emp_expenses = $result['self_emp_expenses'];
$holidaydata = $result['holidays'];
$holidays = nearestDateFirstShow($holidaydata,'holiday_date');
$total_holidays = count((array)$holidaydata);
foreach($self_emp_expenses as $see){
  if($see['current_status'] == ST_APPROVED){
      $selfempexpenses[ST_APPROVED][] = $see;
  }elseif($see['current_status'] == ST_DRAFT || $see['current_status'] == ST_ACTIVE){
     $selfempexpenses['yettosubmit'][] = $see;
  }else{
     $selfempexpenses['pending'][] = $see;
  }
}
$team_emp_expenses = $result['team_emp_expenses'];
$transaction_approval_logs = $result['transaction_approval_logs'];

$self_trips = $result['self_trips'];
foreach($self_trips as $see1){
  if($see1['current_status'] == ST_APPROVED){
      $selftrips[ST_APPROVED][] = $see1;
  }elseif($see1['current_status'] == ST_DRAFT || $see1['current_status'] == ST_ACTIVE){
     $selftrips['yettosubmit'][] = $see1;
  }else{
     $selftrips['pending'][] = $see1;
  }
}

foreach($transaction_approval_logs as $tal){
  if($tal['response_type'] == 0){
      $transactionapprovallogs['pending'][] = $tal;
  }else{
      $transactionapprovallogs['approved'][] = $tal;
  }

}

$trips_transaction_approval_logs = $result['trips_transaction_approval_logs'];
foreach($trips_transaction_approval_logs as $tal){
  if($tal['response_type'] == 0){
      $tripstransactionapprovallogs['pending'][] = $tal;
  }else{
      $tripstransactionapprovallogs['approved'][] = $tal;
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
        data:'page=getreimbursementdashboardchart&reimbursementsyear='+reimbursementsyear,
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

      <a href="index?option=COM_INSERT_EMP_EXPENSES" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer"> New Expense</a>
    <a href="index?option=COM_LIST_EMP_EXPENSES" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer"> Browse Expense Report</a>
    <?php $_SESSION['payment_transaction_type'] = 'employees'; ?>
    <?php if(in_array(CLAIM_EXECUTIVE_ROLE,(array)$_SESSION['role_ids']) || $_SESSION['admin_type'] == 1){ ?>
      <a class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer" href="#!" onclick="insertEmployeePopUp()"> Add Employee</a>
     <a href="index.php?option=COM_VIEW_REIMBURSEMENT_PAYMENT_DASHBOARD" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer">Payments</a>
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
            <a href="#"><div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['total_bill_cnt'] !=""?$emp_expenses_cnt['total_bill_cnt']:0;?> BillS </div>
              <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills</p>
            </a>
          </div>
        </div>
        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3 m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
            <a href="#"><div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['yettosubmit_cnt'] !=""?$emp_expenses_cnt['yettosubmit_cnt']:0;?> BillS </div>
              <p class="card-subtitle text-muted mt-1 f-12"> Yet To Submit</p>
            </a>
          </div>
        </div>
        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3 m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
            <a href="#"><div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['inprocess_cnt'] !=""?$emp_expenses_cnt['inprocess_cnt']:0;?> BillS </div>
              <p class="card-subtitle text-muted mt-1 f-12"> In-Process Bills</p>
            </a>
          </div>
        </div>
        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3 m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
           <a href="#">
            <div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['approved_cnt'] !=""?$emp_expenses_cnt['approved_cnt']:0;?> BillS </div>
            <p class="card-subtitle text-muted mt-1 f-12"> Approved Bills</p>
          </a>
        </div>
      </div>
        <div class="d-flex align-items-center col-md" >
          <div class="rounded-circle bg-primary p-3  m-r-10">
            <i class="fa fa-user-plus f-20"></i>
          </div>
          <div class="flex">
             <a href="#">
              <div class="card-title f-w-600 f-16"><?php echo $emp_expenses_cnt['clearbill_cnt'] !=""?$emp_expenses_cnt['clearbill_cnt']:0;?> BillS </div>
              <p class="card-subtitle text-muted mt-1 f-12"> Cleared Bills </p>
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
        if($tamount !="" && $tamount !=0){
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
            <h3 class="f-w-700 text-c-yellow"><?php echo $employees_cnt['emp_cnt']; ?></h3>
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
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#selftrips" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-bus f-20"></i> Trips <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$self_trips); ?></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#approvaltrips" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-bus f-20"></i> Trip Approvals <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$tripstransactionapprovallogs['pending']); ?></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#selfexpenses" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-check-circle f-20"></i> Self Expenses <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$self_emp_expenses); ?></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#teamexpenses" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-shopping-basket f-20"></i> Team Expenses <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$team_emp_expenses); ?></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#approvalexpenses" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-shopping-basket f-20"></i> Expenses Approvals <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$transactionapprovallogs['pending']); ?></span></a>
  </li>

</ul>
<div class="tab-content tabs">

  <div class="tab-pane card-body p-0" id="selftrips" role="tabpanel" >
    <div class="row">
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Yet To Submit</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn" class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Trip Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selftrips['yettosubmit'])!='0'){
                        foreach($selftrips['yettosubmit'] as $empe){

                        $totalamt =  $totalamt+ $empe['budget_amount'];
                        $ridbill = deocdeId("id:".$empe['id']);
                         ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['transaction_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['budget_amount']); ?></td>
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
                  <table id="basic-btn_2"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Trip Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selftrips['pending'])!='0'){
                        foreach($selftrips['pending'] as $empe){
                          $totalamt =  $totalamt+ $empe['budget_amount'];
                          $ridbill = deocdeId("id:".$empe['id']);?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></td>
                          <td><?php echo fromDate($empe['transaction_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['budget_amount']); ?></td>
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
                  <table id="basic-btn_3"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Trip Number</th>
                        <th scope="col" class="">Reimbursement #</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                   <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selftrips[ST_APPROVED])!='0'){
                        foreach($selftrips[ST_APPROVED] as $empe){
                         $totalamt =  $totalamt+ $empe['budget_amount'];
                         $ridbill = deocdeId("id:".$empe['id']);
                         $exp_idid = deocdeId("id:".$empe['exp_id']);

                         ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td> <a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>

                          <td>
                            <?php if($empe['expense_code'] !=""){ ?>
                            <a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $exp_idid; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a>
                            <?php }else{  ?>
                              <a href="index?option=COM_INSERT_EMP_EXPENSES&TRIPID=<?php echo $empe['id']; ?>" class="text-primary"><span class="text-danger">Add</span> New Expense
                            <?php } ?>
                          </td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['budget_amount']); ?></td>

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

  <div class="tab-pane card-body active p-0" id="selfexpenses" role="tabpanel" >
    <div class="row">
        <div class="col-lg-4">
          <div class="card table-card">
            <div class="card-header">
              <h5>Yet To Submit</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_4" class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfempexpenses['yettosubmit'])!='0'){
                        foreach($selfempexpenses['yettosubmit'] as $empe){

                        $totalamt =  $totalamt+ $empe['total_amount'];
                        $ridbill = deocdeId("id:".$empe['id']);
                         ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
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
              <h5>In Process</h5>
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
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfempexpenses['pending'])!='0'){
                        foreach($selfempexpenses['pending'] as $empe){
                          $totalamt =  $totalamt+ $empe['total_amount'];
                          $ridbill = deocdeId("id:".$empe['id']);?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></td>
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
               <div class="card-header-right">
                  <span><i class="ti-check-box text-success"></i>  Paid</span>
                  <span><i class="ti-timer text-danger"></i>  Un-Paid</span>
              </div>

            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_6"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Trip No.</th>
                        <th scope="col" class="text-right">Amount</th>
                        <th scope="col" class="text-right">Paid</th>
                      </tr>
                    </thead>
                   <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$selfempexpenses[ST_APPROVED])!='0'){
                        foreach($selfempexpenses[ST_APPROVED] as $empe){
                         $totalamt =  $totalamt+ $empe['total_amount'];
                         $ridbill = deocdeId("id:".$empe['id']);
                         $tripid_id = deocdeId("id:".$empe['trip_id']);
                         $pendinamt = customeummarmonyformateifine($empe['total_amount']-$empe['pending_amount']);
                         ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><?php
                            if($pendinamt >=0){
                          ?><i class="ti-check-box text-success"></i><?php
}else{ ?>  <i class="ti-timer text-danger"></i><?php
} ?> <a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridbill; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>

                          <td>
                            <?php if($empe['transaction_no'] !=""){ ?>
                            <a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $tripid_id; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a>
                            <?php }else{ echo "-";
} ?>
                          </td>

                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['total_amount']); ?></td>
                          <td class="text-right"><?php echo $pendinamt; ?></td>
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

    <div class="tab-pane card-body p-0" id="teamexpenses" role="tabpanel">
        <div class="row custom-scroll" style="max-height: 400px; min-height: 400px;">
          <?php  if(count((array)$team_emp_expenses) != 0 ){ foreach($team_emp_expenses as $teamexp){

        $tamount = $teamexp['total_expenses'];
        $pending_amount= $teamexp['total_expenses']-$teamexp['pending_amount'];
        $percengraf = 0;
        if($tamount !="" && $tamount !=0){
        $percengraf = customNanAndEmptyValidation(round(($pending_amount/$tamount)*100),1);
}
           ?>
            <div class="col-xl-2">
                <div class="card proj-t-card" >
                    <div class="card-body" >
                        <div class="row align-items-center">
                            <div class="col-auto">
                              <img src="uploads/all/file_1694175852_64fb126c305db.png" class="img-radius img-50" alt="">
                            </div>
                            <div class="col p-l-0" style="min-height: 120px;">
                                <span class="m-b-5 f-14"><?php echo $teamexp['display_name']; ?></span>
                                <span class="d-block">Claim : <?php echo $teamexp['total_expenses']!=""?customeummarmonyformateifine($teamexp['total_expenses']):0; ?></span>
                                <span class="text-danger">Pending : <?php echo customeummarmonyformateifine(($teamexp['total_expenses']-$teamexp['pending_amount'])); ?> </span>
                            </div>
                            <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3" >
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bold fs-6 text-gray-400">Reimbursements</span>
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
            </div>
          <?php } ?>
          <?php } else { ?>
            <div class="card">
              <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i>No Team </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
               </div>
          <?php } ?>
        </div>
    </div>

  <div class="tab-pane card-body p-0" id="approvalexpenses" role="tabpanel">
      <div class="row">
        <div class="col-lg-6">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approval Pending</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_7"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$transactionapprovallogs['pending'])!='0'){
                        foreach($transactionapprovallogs['pending'] as $empe){  $ridrecengtbils = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['total_amount'];
                                                   ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                           <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
                          <td><?php echo fromDate($empe['request_date']); ?></td>

                          <td><?php echo fromDate($empe['expense_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['total_amount']); ?></td>
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
                  <table id="basic-btn_8"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Bill Number</th>
                        <th scope="col">Response Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1; if(count((array)$transactionapprovallogs['approved'])!='0'){
                        foreach($transactionapprovallogs['approved'] as $empe){
                          $ridrecengtbils = deocdeId("id:".$empe['id']);
                           $totalamt =  $totalamt+ $empe['total_amount'];
                           ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['expense_code']; ?></a></td>
                          <td> <?php echo fromDate($empe['response_date']); ?></td>

                          <td><?php echo fromDate($empe['expense_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['total_amount']); ?></td>
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

  <div class="tab-pane card-body p-0" id="approvaltrips" role="tabpanel">
      <div class="row">
        <div class="col-lg-6">
          <div class="card table-card">
            <div class="card-header">
              <h5>Approval Pending</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 400px; min-height: 400px;">
                <div class="card-block table-responsive" >
                  <table id="basic-btn_9"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Trip Number</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalamt = 0; $mq = 1; if(count((array)$tripstransactionapprovallogs['pending'])!='0'){
                        foreach($tripstransactionapprovallogs['pending'] as $empe){  $ridrecengtbils = deocdeId("id:".$empe['id']);
                          $totalamt =  $totalamt+ $empe['trips'];
                                                   ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                           <td><a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td><?php echo fromDate($empe['request_date']); ?></td>

                          <td><?php echo fromDate($empe['transaction_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['trips']); ?></td>
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
                  <table id="basic-btn_10"  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Trip Number</th>
                        <th scope="col">Response Date</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $mq = 1; if(count((array)$tripstransactionapprovallogs['approved'])!='0'){
                        foreach($tripstransactionapprovallogs['approved'] as $empe){
                          $ridrecengtbils = deocdeId("id:".$empe['id']);
                           $totalamt =  $totalamt+ $empe['trips'];
                           ?>
                          <tr>
                          <td><?php echo $mq; ?></td>
                          <td><a href="index?option=COM_INSERT_TRIPS&Rid=<?php echo $ridrecengtbils; ?>" class="text-primary"><?php echo $empe['transaction_no']; ?></a></td>
                          <td> <?php echo fromDate($empe['response_date']); ?></td>

                          <td><?php echo fromDate($empe['transaction_date']); ?></td>
                          <td class="text-right"><?php echo customeummarmonyformateifine($empe['trips']); ?></td>
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
