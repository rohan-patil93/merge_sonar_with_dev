<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=getprojecttimesheets&table=".TBL_PROJECTS."&project_id=".$_GET['projectid'];
$proj_data = callAPI($vurl);
$projectdata = json_decode($proj_data,1);
$projectdetails = $projectdata['project_details'];
?>
<script type="text/javascript">
   function addtimesheetentry() {
      var is_billable = 1;
      if(document.getElementById('is_billable').checked==true){
         is_billable = 0;
      }
      var postdata = 'project_id='+document.getElementById('project_id').value+'&task_id='+document.getElementById('task_id').value+'&time_spent='+document.getElementById('time_spent').value+'&is_billable='+is_billable+'&notes='+document.getElementById('notes').value+'&user_id='+document.getElementById('user_id').value;
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
      Add Timesheet Entry
   </h5>
   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <i aria-hidden="true" class="ki ki-close"></i>
   </button>
</div>

                                          <div class="modal-content">

                                             <div class="modal-body">
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label">Date</span>
                                                   <div class="col-12">
                                                      <input aria-label="Select date" type="text" class="form-control" id="kt_datepicker_1" placeholder="Select date" value="<?php echo fromDate($projectdetails['created_date']); ?>">
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label">Project Name</span>
                                                   <div class="col-12">
                                                      <input aria-label="Input field" class="form-control" type="text" value="<?php echo $projectdetails['name'];?>" placeholder="Project Name" id="project_id" >
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label">Task Name</span>
                                                   <div class="col-12">
                                                      <select aria-label="Param" class="form-control" id="task_id" name="param">
                                                         <option value="AK">Customization</option>
                                                         <option value="HI">Customization</option>
                                                         <option value="CA">Customization</option>
                                                         <option value="NV">Customization</option>
                                                      </select>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <span class="col-12 col-form-label">Time Spent</span>
                                                   <div class="col-12">
                                                      <input aria-label="00.00" class="form-control" type="text" value="" placeholder="00.00" id="time_spent">
                                                   </div>

                                                </div>
                                                <div class="form-group row mb-2 mt-5">
                                                   <div class="col-12">
                                                      <div class="checkbox-list">
                                                         <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                                                            <input aria-label="Checkboxes1" type="checkbox" name="Checkboxes1" id="is_billable">
                                                            <span></span>
                                                            Billable
                                                         </label>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label">Notes</span>
                                                   <div class="col-12">
                                                      <textarea aria-label="Add Notes" class="form-control" name="notes" placeholder="Add Notes" id="notes" rows="3"></textarea>
                                                   </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                   <span class="col-12 col-form-label">Users</span>
                                                   <div class="col-12">
                                                      <select aria-label="Param" class="form-control" id="user_id" name="param">
                                                         <option value="<?php echo $_SESSION['userid']; ?>"><?php echo $_SESSION['employee']['display_name']; ?></option>
                                                      </select>
                                                   </div>
                                                </div>
                                             </div>

                                             <div class="modal-footer">
                                                <button type="button" class="btn btn-primary font-weight-bold" onclick="addtimesheetentry();">Save</button>
                                                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>

                                             </div>
                                          </div>
                                       </div>
                                    </div>

         <!--end: Modal Popup -->
</div>
