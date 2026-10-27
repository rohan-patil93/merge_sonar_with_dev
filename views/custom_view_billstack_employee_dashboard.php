 <?php
 $_SESSION['apply_js']['amchart'] = '1';
 $_SESSION['apply_js']['float'] = '1';
 $_SESSION['apply_css']['widgetcss'] = '1';
 $admindashboardurl = API."?mode=getemployeedashboard&emp_id=".$_SESSION['emp_id']."&bill_limit=4";
 $admindashboardres = callAPI($admindashboardurl);
 $admindashboardd = json_decode($admindashboardres,1);
 $status_progress = $admindashboardd['status_progress'];
 $statusprogress = array();
 foreach($status_progress as $sp) {
    $statusprogress[$sp['status']] = $sp['name'];
 }
 $holiday_list = array();
 $current_year = date("Y");
 $holidaywr["where"] = array('company_id'=> $_SESSION['company_id'],'DATE_FORMAT(holiday_date,"%Y")'=>$current_year);
 $holidaycon = urlencode(json_encode($holidaywr));
 $holidayurl = API."?mode=gettabledetailswithconditions&table=holidays&data=".$holidaycon;
 $holidaydata = callAPI($holidayurl);
 $holidaydata = json_decode($holidaydata,1);
 $total_holidays = count((array)$holidaydata);
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
</script>

<div class="banner">
  <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
  <div class="banner-text">
    <h2>Start managing expenses and reimbursements With ease! </h2>
    <p>Helps you in managing: Expenses And Reimbursements. BillStack does this all through your modern Web Browser. Be assured to track your bills and payments from anywhere anytime.</p>
    <button type="button" onclick="expensesDetailsPopUp(<?php echo $_SESSION['emp_id']; ?>)" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer"> New Expense Report</button>
    <a href="index?option=COM_LIST_EMP_EXPENSES" class="btn-white b-radius-5 text-uppercase f-w-600 cursor-pointer"> Browse Expense Report</a>
  </div>
</div>
<div class="card">
  <div class="card-block">
    <div class="width-77">
      <div class="row align-items-center">
       <div class="d-flex align-items-center col-md mb-2" onclick="dashboardpopup('bill_cnt','Total Number Of Bills','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">

        <div class="rounded-circle bg-primary p-3 m-r-10">
          <i class="fa fa-user-plus f-20"></i>
        </div>
        <div class="flex">
          <a href="#"><div class="card-title f-w-600 f-16"><?php echo count((array)$admindashboardd['bill_cnt'])."+ BillS"?></div>
            <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills</p>
          </a>
        </div>

      </div>
      <div class="d-flex align-items-center col-md mb-2" onclick="dashboardpopup('bill_approved','Total Number Of Bills Cleared','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
        <div class="rounded-circle bg-primary p-3  m-r-10">
          <i class="fa fa-user-plus f-20"></i>
        </div>
        <div class="flex">
         <a href="#">
          <div class="card-title f-w-600 f-16"><?php echo count((array)$admindashboardd['bill_approved'])."+ Bills"?></div>
          <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills Cleared</p>
        </a>
      </div>
    </div>
    <div class="d-flex align-items-center col-md mb-2" onclick="dashboardpopup('pending_approved','Total Number Of Bills Pendding','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
      <div class="rounded-circle bg-primary p-3 m-r-10">
        <i class="fa fa-user-plus f-20"></i>
      </div>
      <div class="flex">
       <a href="#">
        <div class="card-title f-w-600 f-16"><?php echo count((array)$admindashboardd['pending_approved'])."+ Bills"?></div>
        <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Bills Pendding</p>
      </a>
    </div>
  </div>
</div>
</div>
</div>
</div>
<div class="pcoded-inner-content">
  <!-- Main-body start -->
  <div class="main-body">
    <div class="page-wrapper">
      <!-- Page-body start -->
      <div class="page-body border-bottom">
        <div class="width-77">
          <div class="row">

            <div class="col-xl-4 col-md-6 cursor-pointer">
              <div class="card prod-p-card card-red" onclick="dashboardpopup('unsubmited','Total Number Of Bills Unsubmite','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5 text-white">Un-Submited</h6>
                      <h3 class="m-b-0 f-w-700 text-white"><?php echo $admindashboardd['unsubmited_amt']!=""?customeummarmonyformate($admindashboardd['unsubmited_amt']):"0.00"; ?></h3>
                    </div>
                    <div class="col-auto">
                      <i class="ti-help text-c-red f-18 p-2"></i>
                    </div>
                  </div>
                  <p class="m-b-0 text-white"><span class="label label-danger m-r-10"><?php echo $admindashboardd['unsubmited_cnt']; ?> </span>Reports</p>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-md-6 cursor-pointer">
              <div class="card prod-p-card card-blue" onclick="dashboardpopup('pending_approved','Total Number Of Bills Pendding','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                <div class="card-body">
                  <div class="row align-items-center m-b-30">
                    <div class="col">
                      <h6 class="m-b-5 text-white">Submited</h6>
                      <h3 class="m-b-0 f-w-700 text-white"><?php echo $admindashboardd['pending_approved_amt']!=""?customeummarmonyformate( $admindashboardd['pending_approved_amt']):"0.00"; ?></h3>
                    </div>
                    <div class="col-auto">
                     <i class="ti-thumb-up text-c-blue f-18 p-2"></i>
                   </div>
                 </div>
                 <p class="m-b-0 text-white"><span class="label label-primary m-r-10"><?php echo $admindashboardd['pending_approved_cnt']; ?></span>Reports</p>
               </div>
             </div>
           </div>
           <div class="col-xl-4 col-md-6 cursor-pointer">
            <div class="card prod-p-card card-green" onclick="dashboardpopup('bill_approved','Total Number Of Bills Cleared','emp_expenses')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
              <div class="card-body">
                <div class="row align-items-center m-b-30">
                  <div class="col">
                    <h6 class="m-b-5 text-white"> Reimbursed</h6>
                    <h3 class="m-b-0 f-w-700 text-white"><?php echo $admindashboardd['bill_approved_amonut']!=""?customeummarmonyformate($admindashboardd['bill_approved_amonut']):"0.00"; ?></h3>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-money-bill-alt text-c-green f-18 p-2"></i>
                  </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-success m-r-10"><?php echo $admindashboardd['bill_approved_cnt']; ?> </span>Reports</p>
              </div>
            </div>
          </div>
          <!-- product profit end -->
        </div>
      </div>
    </div>

      <div class="page-body mt-4 border-bottom">
        <div class="width-77">
          <div class="row">
            <h3 class="f-20 mb-3">Recet Bills</h3>
            <?php if(count((array)$admindashboardd['billsres']) != 0){ foreach($admindashboardd['billsres'] as $billsreslist){
              $stausresult = $_SESSION['status'][$billsreslist['current_status']];
              $ridrecengtbils = deocdeId("id:".$billsreslist['id']);
              ?>
              <div class="col-xl-3 col-md-12" id="custom-popup" >
               <div class="card proj-t-card">
                <div class="card-body">
                  <div class="row align-items-center" onclick="slidepopupdetails('<?php echo $billsreslist['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                    <div class="col-auto">
                      <i class="fa fa-file f-28"></i>
                    </div>
                    <div class="col p-l-0">
                      <h6 class="m-b-5 f-16"><?php echo $billsreslist['expense_code']; ?></h6>
                      <h6 class="m-b-0 text-muted f-12">Rs. <?php echo customeummarmonyformate($billsreslist['amount']); ?></h6>
                    </div>
                  </div>
                  <h6 class="pt-badge f-10" style="background-color: <?php echo $stausresult['background_color'][0] == '#'?$stausresult['background_color']:"#".$stausresult['background_color']; ?>; color:<?php echo $stausresult['color'][0] =='#'?$stausresult['color']:'#'.$stausresult['color']; ?>"><?php echo $statusprogress[$billsreslist['current_status']]?></h6>
                </div>
              </div>
                  <div class="custom-popup-open custom-popup-slide-<?php echo $billsreslist['id']; ?>" style="left: 14px; top: 70px;">
                      <div class="card">
                        <div class="card-header">
                          <h5>Expense</h5>
                          <div class="card-header-right">
                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closedPopup()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-auto">
                               <i class="fa fa-file f-28"></i>
                            </div>
                            <div class="col p-l-0">
                              <h6 class="m-b-5 f-16"><?php echo $billsreslist['expense_code']; ?></h6>
                              <h6 class="m-b-0 text-muted f-12">Rs. <?php echo customeummarmonyformate($billsreslist['amount']); ?></h6>
                            </div>
                          </div>
                          <p class="mt-3 text-muted"><?php echo $billsreslist['description']; ?></p>
                          <div class="row text-center mt-3">
                            <div class="col-auto d-flex align-items-center">
                              <i class="fa fa-subway text-muted m-r-5"></i>
                              <p class="flex text-black-50 lh-1 mb-0">
                                <small><?php echo $billsreslist['expense_date']; ?></small>
                              </p>
                            </div>
                            <div class="col-auto d-flex align-items-center">
                              <i class="fa fa-file text-muted m-r-5"></i>
                              <p class="flex text-black-50 lh-1 mb-0">
                                <small><?php echo $billsreslist['reference_date']; ?></small>
                              </p>
                            </div>
                          </div>
                          <div class="mt-4">
                            <a href="index.php?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridrecengtbils; ?>" class="btn btn-primary">Edit</a>
                         </div>
                      </div>
                    </div>
                  </div>

          </div>
        <?php  } ?>
      <?php }else{?>
        <div class="card-px text-center py-20 m-t-30">
              <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Recet Bills</p>
              <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
      <?php } ?>

      </div>
    </div>
  </div>
  <!-- Page-body start -->

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
         <div class="card new-cust-card custom_hover_card">
          <div class="card-header">
           <h5>Holidays List</h5> <span class="form-label label label-success"><?php echo $total_holidays; ?></span>

         </div>
         <div class="card-block custom-scroll" style=" height: 300px;">

           <?php
          if(!empty($holidaydata)) {
             foreach ($holidaydata as  $holiday) {

              $Notification_Message = $holiday['holiday_name'];
              $Notification_DateTime = $holiday['holiday_date'];
              ?>
              <div class="align-middle m-b-35">
               <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($Notification_Message); ?></span>
               <div class="d-inline-block">
                <a href="#!"><h6><?php echo $Notification_Message; ?></h6></a>
                <p class="text-muted m-b-0"><?php echo $Notification_DateTime; ?></p>
              </div>
            </div>
          <?php } ?>
          <?php } else {

            echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
          } ?>

        </div>
      </div>
    </div>

    <div class="col-xl-6">

     <div class="card">
      <div class="card-header">
       <h5>Tasks due in 7 days
       </h5>
       <span class="form-label label label-danger"><?php echo count((array)$count_task['due']); ?></span>
       <span class="form-label label label-warning"><?php echo count((array)$count_task['now']); ?></span>
       <span class="form-label label label-primary"><?php echo count((array)$count_task['after']); ?></span>
     </div>
     <div class="card-block  custom-scroll" style="height: 300px;">
       <div class="new-task">

        <?php
        if(!empty($kpi_data_list)){
          foreach ($kpi_data_list as  $task) {
           $rtext ='xyz';
           $Notification_Message = $task['task']['id']."-".$task['task']['name'];
           $Notification_Table_Id = $task['task']['id'];
           $Notification_Table = "employee_kpi_task_histories";
           $current_status = $task['task']['current_status'];
           $text_masege="";
           if($task['task']['task_date'] == date('Y-m-d')){
            $class_text = 'text-warning';

          }elseif(strtotime($task['task']['task_date']) < strtotime(date('Y-m-d'))){
            $class_text = 'text-danger';

          }else{
            $class_text = 'text-muted';
            $text_masege= "Due in next";
            $rtext ='ago';
          }
          $Notification_DateTime = notificationdatesdiff($task['task']['task_date']) ." ago";
                                  $created_userid = $task['task']['created_by'];
                                  $icon_initials = initials($_SESSION['users'][$created_userid]);
                                  ?>

                                  <div class="to-do-list">
                                      <div class="checkbox-fade fade-in-primary" >
                                          <label class="form-label check-task " id="checkstausid<?php echo $Notification_Table_Id; ?>">
                                              <input aria-label="Checkbox" type="checkbox"  value="" id="checkstausinput<?php echo $Notification_Table_Id; ?>" onclick="statuspopup('<?php echo $task['status_list'][0]['status']; ?>','<?php echo $Notification_Table_Id; ?>','<?php echo $Notification_Table; ?>','<?php echo $task['view_status']=="0"?"0":$_SESSION['error_message'][$task['view_status']]; ?>','task')" onkeydown="">
                                              <span class="cr ">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                              </span>
                                              <span class="<?php echo $class_text; ?>"><?php echo $Notification_Message; ?></br><small class="m-l-35 <?php echo $class_text; ?>"><?php echo $text_masege." ".str_replace($rtext,"",$Notification_DateTime); ?></small></span>
                                          </label>
                                      </div>
                             </div>
                           <?php } ?>
                           <?php } else {

                            echo "<h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Task Available</h4>";
                         } ?>

                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="col-lg-6 col-xl-6">
                   <div class="col-lg-12">
                    <div class="card table-card">
                     <div class="card-header">
                      <h5>Reviews</h5><span class="form-label badge bg-danger"><?php echo count((array)$reviewdata['total_review_count']['due']); ?></span><span class="form-label badge bg-warning ml-2"><?php echo count((array)$reviewdata['total_review_count']['now']); ?></span>

                    </div>
                    <div class="card-block custom-scroll" style="height: 300px;">
                      <div class="dt-responsive table-responsive reviewtableaax" >

                       <?php include_once "views/custom_dashboard_reviews.php"; ?>

                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>


</div>
</div>
<!-- Main-body start -->
</div>
