<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$admin_type = 1;
$user_roles = $_SESSION['role_ids'];
$empiid = $_POST['employee_id'];
$document_category_id = 1;
$encodeuser_roles = json_encode((array)$_SESSION['role_ids']);
$employee_id = $empiid !=""?$empiid:$_SESSION['emp_id'];
$url = API."?mode=getemployeeonboardingtask&employee_id=".$employee_id."&admin_type=".$admin_type."&document_category_id=".$document_category_id;
$api = callAPI($url);
$result = json_decode($api,1);
$stausresult = ViewStatusData();
$taskscategories = array();
$completedcnt = 0;
foreach($result['tasks_categories'] as $tasks_categories){
  $taskscategories[$tasks_categories['id']] = $tasks_categories['category_name'];
}
$transactionactivityschedule = array();
$transactionactivityschedulecompletedstatus = array();
$rejetcedcnt = 0;
foreach($result['transaction_activity_schedule'] as $transaction_activity_schedule){
    $transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      $complitedtask[$transaction_activity_schedule['process_id']]++;
    }
    if($transaction_activity_schedule['current_status'] == ST_COMPLETE){
      $transactionactivityschedulecompletedstatus[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
      $completedcnt++;
    }
    if($transaction_activity_schedule['current_status'] == ST_REJECTED){
      $rejetcedcnt++;
    }
    if($transaction_activity_schedule['link_page'] == '0'){
    $linktransactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      $linkcomplitedtask[$transaction_activity_schedule['process_id']]++;
    }
  }

  $totaltask[$transaction_activity_schedule['process_id']]++;
}
?>
<script type="text/javascript">
 function showUpdateChecklist(id){
  $(".custom-popup-open").hide();
  $(".custom-popup-slide-updatechecklist-"+id).show('slow');
}
function closeSmallPopUp(){
 $(".custom-popup-open").hide('slow');
}
function getTaskCheckList(id,aryposition,candidate_id){
 $('.checkcheckboxcopliance_'+id+'_'+aryposition).attr("disabled", true);
 $("#kt_modal_KTDatatable_remote").modal({
  backdrop: 'static',
  keyboard: false
});
 jQuery.ajax({
  url: "views/custom_insert_transaction_activity_schedule_popup.php",
  data:'id='+id+"&aryposition="+aryposition+"&candidate_id="+candidate_id,
  type: "POST",
  success:function(data){
    $('#popup1').html(data);
    $('#kt_modal_KTDatatable_remote').modal("show");
  },
  error:function (){}
});
}

function updateTaskStatus(task_id,status){
  var transaction_action_table = "employees";
  jQuery.ajax({
    url: "views/custom_update_onboardning_task_status_popup.php",
    data:'task_id='+task_id+"&status="+status+'&transaction_action_table='+transaction_action_table,
    type: "POST",
    success:function(data){
      $('#popup1').html(data);
      $('#kt_modal_KTDatatable_remote').modal("show");
    },
    error:function (){}
  });
}

</script>
<div class="row">
  <div class="col-lg-8">
<div class="card">
  <div class="card-header  ">
    <h5>Tasks  <?php if($rejetcedcnt != 0){ ?>&nbsp;<span class="form-label label label-md label-danger float-right"><?php echo "Reject : ".$rejetcedcnt; ?></span><?php
} ?></h5>
  </div>
  <div class="accordion-desc">
   <div class="accordion-block color-accordion-block">
    <div class="color-accordion" id="color-accordion">
      <?php if(count((array)$transactionactivityschedule) != 0){
        foreach($transactionactivityschedule as $key1=>$transactionactivityschedule){ ?>
          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?>
          <span class="float-end"><?php echo $totaltask[$key1]
          ;?> of <?php echo $complitedtask[$key1] !=""?$complitedtask[$key1]:"0";?> Completed</span>
        </a>
        <div class="accordion-desc">
          <table class="table table-hover">
            <tbody>
              <?php foreach($transactionactivityschedule as $tas){ ?>
                <tr id="custom-popup" >
                  <th scope="col" class="w-b-w-s" onclick="showUpdateChecklist('<?php echo $tas['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                   <h6 class="mb-0 f-14" >
                    <?php if($tas['link_page'] == '1'){?>
                      <i class="ti-check-box"></i>
                    <?php  }else{ ?>
                     <i class="ti-new-window"></i>
                   <?php } ?>
                   <?php echo $tas['task_name']; ?></h6>
                 </th>
                 <th scope="col" style="text-align: center;" >
                   <?php $stausresultv = $stausresult[$tas['current_status']];
                   ?>
                   <span  class="statusid_<?php echo $tas['id']; ?>"><span class="form-label label label-success" style="color: <?php echo "#".$stausresultv['color']; ?>;    background:  <?php echo $stausresultv['background_color']; ?>; border-color: <?php echo $stausresultv['background_color']; ?>;" ><?php echo $stausresultv['name']; ?></span></span>
                   <?php if($tas['check_list'] !=""){ ?>
                    <div class="custom-popup-open custom-popup-slide-updatechecklist-<?php echo $tas['id']; ?> text-start" style="display: none;left: 18px;top: 55px; ">
                      <div class="card">
                        <div class="card-header">
                          <h5>Task</h5>
                          <div class="card-header-right">
                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                          </div>
                        </div>
                        <div class="card-body">
                          <table style="width: 100%;">
                            <thead>
                              <tr class="d-none">
                                <th scope="col">Task Checklist</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $check_list = json_decode($tas['check_list'], 1);
                              foreach($check_list as $keyval=>$list){
                                $checkboxcl=$classcheck="";
                                if($list['checklist_completed']=="Y"){
                                  $taskstatus++;
                                  $classcheck="check-task check-task done-task";
                                  $checkboxcl = "checked disabled";
                                }
                                ?>
                                <tr>
                                 <td style="width: 90%;">
                                  <div class="to-do-list">
                                    <div class="checkbox-fade fade-in-primary" role="group" onchange="getTaskCheckList('<?php echo $tas['id']; ?>','<?php echo $keyval; ?>','<?php echo $employee_id; ?>')">
                                      <label class="form-label check-task <?php echo $classcheck; ?> campliancecros_<?php echo $tas['id']."_".$keyval; ?>">
                                        <input aria-label="Checkbox" type="checkbox" value="" <?php echo $checkboxcl; ?>  class="checkcheckboxcopliance_<?php echo $tas['id']."_".$keyval; ?>" >
                                        <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span><?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php
} ?></span>
                                      </label>
                                    </div>
                                  </div>
                                </th>
                                <th scope="col" style="width: 10%;">
                                  <span class="float_end">
                                    <?php
                                    if(trim($list['downloadfile']) !="" && strlen($list['downloadfile']) !='0'){ ?>
                                      <a href="<?php echo "uploads/all/".$list['downloadfile']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Demo Form Download" download><i class="ti-cloud-down"></i></a>
                                      <?php
                                    }
                                    if(trim($list['attachment_name']) !="" && strlen($list['attachment_name']) !='0'){
                                     $path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$employee_id."/Documents/";
                                     ?>
                                     <a href="<?php echo $path.$list['attachment_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploaded Form Download" download><i class="ti-cloud-down m-l-20"></i></a>
                                   <?php }
if($tas['link_page'] == '0'){ ?>
                                     <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><div class="mb-0 f-14" ><i class="ti-new-window"></i></div></a>
                                     <?php } ?>
                                   </span>
                                 </th>
                               </tr>
                             <?php }  ?>
                           </tbody>
                         </table>
                       </div>
                     </div>
                   </div>
                 <?php } ?>
                </td>

             </tr>
           <?php } ?>
         </tbody>
       </table>
     </div>
   <?php } ?>
   <?php } else { ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
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
            <div class="card-header  ">
                <h5>Review Pending  &nbsp;<span class="form-label label label-md label-warning float-right"><?php echo count((array)$transactionactivityschedulecompletedstatus);?> </span></h5>
                <div class="card-header-right table-card-header ">
                    <div class="input-group input-group-button mb-0">
                        <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                        <span class="input-group-text addon-style">
                            <span class=""><span class="ti-search text-white mt-2"></span></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                      <div class="accordion-desc">
   <div class="accordion-block color-accordion-block">
    <div class="color-accordion" id="color-accordion_2">
      <?php if(count((array)$transactionactivityschedulecompletedstatus) != 0){
        foreach($transactionactivityschedulecompletedstatus as $key1=>$tascs){ ?>
          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?>
        </a>
        <div class="accordion-desc">
            <table class="table table-hover">
            <tbody>
              <?php foreach($tascs as $tas){ ?>
                <tr id="custom-popup_2" >
                  <th scope="col" class="w-b-w-s" onclick="showUpdateChecklist('qq<?php echo $tas['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                   <h6 class="mb-0 f-14" >
                    <?php if($tas['link_page'] == '1'){?>
                      <i class="ti-check-box"></i>
                    <?php  }else{ ?>
                     <i class="ti-new-window"></i>
                   <?php } ?>
                   <?php echo $tas['task_name']; ?></h6>
                 </th>
                 <th scope="col" style="text-align: center;" class="statusid_<?php echo $tas['id']; ?>">
                   <?php
                      $stausresultv = $stausresult[$tas['current_status']];
                   ?>
                   <button class="btn waves-effect waves-light btn-success btn-icon" onclick="updateTaskStatus('<?php echo $tas['id']; ?>','<?php echo ST_APPROVED; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Approve"><i class="ti-thumb-up f-18"></i></button>
                   <button class="btn waves-effect waves-light btn-danger btn-icon"><button type="button" class="btn p-0 border-0 bg-transparent ti-thumb-down f-18" onclick="updateTaskStatus('<?php echo $tas['id']; ?>','<?php echo ST_REJECTED; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Reject" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></button>

                   <?php if($tas['check_list'] !=""){ ?>
                    <div class="custom-popup-open custom-popup-slide-updatechecklist-qq<?php echo $tas['id']; ?> text-start" style="display: none;left: 18px;top: 55px; ">
                      <div class="card">
                        <div class="card-header">
                          <h5>Task</h5>
                          <div class="card-header-right">
                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                          </div>
                        </div>
                        <div class="card-body">
                          <table style="width: 100%;">
                            <thead>
                              <tr class="d-none">
                                <th scope="col">Task Checklist</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $check_list = json_decode($tas['check_list'], 1);
                              foreach($check_list as $keyval=>$list){
                                $checkboxcl=$classcheck="";
                                if($list['checklist_completed']=="Y"){
                                  $taskstatus++;
                                  $classcheck="check-task check-task done-task";
                                  $checkboxcl = "checked disabled";
                                }
                                ?>
                                <tr>
                                 <td style="width: 90%;">
                                  <div class="to-do-list">
                                    <div class="checkbox-fade fade-in-primary" role="group" onchange="getTaskCheckList('<?php echo $tas['id']; ?>','<?php echo $keyval; ?>','<?php echo $employee_id; ?>')">
                                      <label class="form-label check-task <?php echo $classcheck; ?> campliancecros_<?php echo $tas['id']."_".$keyval; ?>">
                                        <input aria-label="Checkbox" type="checkbox" value="" <?php echo $checkboxcl; ?>  class="checkcheckboxcopliance_<?php echo $tas['id']."_".$keyval; ?>" >
                                        <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span><?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php
} ?></span>
                                      </label>
                                    </div>
                                  </div>
                                </th>
                                <th scope="col" style="width: 10%;">
                                  <span class="float_end">
                                    <?php
                                    if(trim($list['downloadfile']) !="" && strlen($list['downloadfile']) !='0'){ ?>
                                      <a href="<?php echo "uploads/all/".$list['downloadfile']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Demo Form Download" download><i class="ti-cloud-down"></i></a>
                                      <?php
                                    }
                                    if(trim($list['attachment_name']) !="" && strlen($list['attachment_name']) !='0'){
                                     $path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$employee_id."/Documents/";
                                     ?>
                                     <a href="<?php echo $path.$list['attachment_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploaded Form Download" download><i class="ti-cloud-down m-l-20"></i></a>
                                   <?php }
if($tas['link_page'] == '0'){ ?>
                                     <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><div class="mb-0 f-14" ><i class="ti-new-window"></i></div></a>
                                     <?php } ?>
                                   </span>
                                 </th>
                               </tr>
                             <?php }  ?>
                           </tbody>
                         </table>
                       </div>
                     </div>
                   </div>
                 <?php } ?>
               </td>
             </tr>
           <?php } ?>
         </tbody>
       </table>
     </div>
      <?php } ?>
      <?php } else { ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
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

</div>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
  <script type="text/javascript" src="assets/pages/todo/todo.js"></script>
