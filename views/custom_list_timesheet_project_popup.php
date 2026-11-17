<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=getprojecttimesheets&table=".TBL_PROJECTS."&project_id=".$_GET['projectid'];
$proj_data = callAPI($vurl);
$projectdata = json_decode($proj_data,1);
$projectdetails = $projectdata['project_details'];
$projectusers = $projectdata['project_users'];
$empnames = $projectdata['empnames'];
$project_tasks = $projectdata['project_tasks'];
?>
<script type="text/javascript">
   function addtimesheetentry() {
      var is_billable = 1;
      if(document.getElementById('is_billable').checked==true){
         is_billable = 0;
      }
      var postdata = 'timesheet_date='+document.getElementById('kt_datepicker_1').value+'&project_id='+document.getElementById('project_id').value+'&task_id='+document.getElementById('task_id').value+'&time_spent='+document.getElementById('time_spent').value+'&is_billable='+is_billable+'&notes='+document.getElementById('notes').value+'&emp_id='+document.getElementById('emp_id').value;
       console.log(postdata);
      $.ajax({
      url: "ajax.php",
      data:'page=addtimesheetentries&'+postdata,
      type: "POST",
      success:function(data){
         console.log(data);
      $('#kt_modal_KTDatatable_remote1').modal('hide');

      },
      error:function (){}
   });
   }
</script>
 <!--begin: Modal Popup -->
 <div class="modal-header">
   <h5 class="modal-title text-center">
      <?= __('Add Timesheet Entry'); ?>
   </h5>
   <button type="button" class="close" data-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>">
      <i aria-hidden="true" class="ki ki-close"></i>
   </button>
</div>

                                          <div class="modal-content">

                                             <div class="modal-body">
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label"><?= __('Date'); ?></span>
                                                   <div class="col-12">
                                                      <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="timesheet_date" class="form-control" id="kt_datepicker_1" placeholder="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo fromDate($projectdetails['created_date']); ?>">
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label"><?= __('Project Name'); ?></span>
                                                   <div class="col-12">
                                                      <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="<?php echo $projectdetails['name'];?>" placeholder="<?= htmlspecialchars(__('Project Name'), ENT_QUOTES, 'UTF-8'); ?>" id="project_name" readonly >
                                                      <input  type="hidden" value="<?php echo $projectdetails['id'];?>" id="project_id" >
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label"><?= __('Task Name'); ?></span>
                                                   <div class="col-12">
                                                      <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="task_id" name="param">
                                                          <?php foreach($project_tasks as $tasks=>$taskdata){
                                                            ?>
                                                            <option value="<?php echo $tasks; ?>"><?php echo $taskdata['task_name']; ?></option>
                                                         <?php  }  ?>
                                                      </select>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <span class="col-12 col-form-label"><?= __('Time Spent'); ?></span>
                                                   <div class="col-12">
                                                      <input aria-label="<?= htmlspecialchars(__('TOTAL TIME IN MINUTES'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="" placeholder="<?= htmlspecialchars(__('TOTAL TIME IN MINUTES'), ENT_QUOTES, 'UTF-8'); ?>" id="time_spent">
                                                   </div>

                                                </div>
                                                <div class="form-group row mb-2 mt-5">
                                                   <div class="col-12">
                                                      <div class="checkbox-list">
                                                         <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                                                            <input aria-label="<?= htmlspecialchars(__('Checkboxes1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes1" id="is_billable">
                                                            <span></span>
                                                            <?= __('Billable'); ?>
                                                         </label>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label"><?= __('Notes'); ?></span>
                                                   <div class="col-12">
                                                      <textarea aria-label="<?= htmlspecialchars(__('Add Notes'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="notes" placeholder="<?= htmlspecialchars(__('Add Notes'), ENT_QUOTES, 'UTF-8'); ?>" id="notes" rows="3"></textarea>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label"><?= __('Users'); ?></span>
                                                   <div class="col-12">
                                                      <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="emp_id" name="param">
                                                         <option value="<?php echo $_SESSION['userid']; ?>"><?php echo $_SESSION['employee']['display_name']; ?></option>
                                                         <?php foreach($projectusers as $puserid=>$pusers){
                                                            ?>
                                                            <option value="<?php echo $puserid; ?>"><?php echo $empnames[$puserid]['display_name']; ?></option>
                                                         <?php  }  ?>
                                                      </select>
                                                   </div>
                                                </div>
                                             </div>

                                             <div class="modal-footer">
                                                <button type="button" class="btn btn-primary font-weight-bold" onclick="addtimesheetentry();"><?= __('Save'); ?></button>
                                                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal"><?= __('Close'); ?></button>

                                             </div>
                                          </div>
                                       </div>
                                    </div>

         <!--end: Modal Popup -->
</div>
