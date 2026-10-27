<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$employee_id = $_POST['emp_id'];
$url = API."?mode=getemployeeonboardingtask&employee_id=".$employee_id."&taskassign=Y";
$api = callAPI($url);
$result = json_decode($api,1);
foreach($result['tasks_categories'] as $tasks_categories){
  $taskscategories[$tasks_categories['id']] = $tasks_categories['category_name'];
}
$transactionactivityschedule = array();
foreach($result['assign_task_list'] as $transaction_activity_schedule){
    $transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    $totaltask[$transaction_activity_schedule['process_id']]++;
}

?>
<script type="text/javascript">
    function AssignTaskSchedule(emp_id){
        $.ajax({
            url: "ajax.php",
            data:'page=assigntaskschedule&emp_id='+emp_id,
            type: "POST",
            success:function(response){
                console.log(response);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"Task Assign successfully ",'');
                $('#kt_modal_KTDatatable_remotem').modal('hide');
                location.reload();
            }
        });
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Task Details</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
    <?php if(count((array)$transactionactivityschedule) != 0){
        foreach($transactionactivityschedule as $key1=>$tascs){ ?>
            <div class="card">
                <div class="card-header  ">
                    <h5><?php echo $taskscategories[$key1];?></h5>
                </div>
                <div class="card-body">
                          <?php foreach($tascs as $tas){ ?>
                        <div class="card">
                            <div class="card-header  ">
                                <h5><?php echo  $tas['task_name'];?></h5>
                            </div>
                             <div class="card-body">
                            <table style="width: 100%;">
                                      <thead>
                                          <tr>
                                              <th scope="col">Task</th>
                                              <th scope="col">Action Task</th>
                                              <th scope="col">Approval</th>
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
                                 <td style="width: 40%;">

                                    <span><i class="ti-check-box"></i> <?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php
} ?></span>

                                </td>
                                <td style="width: 20%;">
                                    <?php echo fromDate($tas['action_date']); ?>
                                </td>
                                <td style="width: 40%;">
                                  <?php echo $_SESSION['users'][$tas['approval_user_id']];

                                  ?>
                                </td>
                               </tr>
                             <?php }  ?>
                           </tbody>
                         </table>
                      </div>   </div>
                          <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="AssignTaskSchedule('<?php echo $employee_id; ?>')"><i class="la la-check"></i>Assign task</button>
</div>
