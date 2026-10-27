<?php
              $todate = date('Y-m-d', strtotime('+30 days'));
              $fromdate = date('Y-m-d',strtotime('-30 days'));
              $vurl = API."?mode=togetkpitaskhistories&company_id=".$company_id."&is_active=0&user_id=".$_SESSION['userid']."&task_date_from=".$fromdate."&task_date_to=".$todate;
              $configesponse = callAPI($vurl);
              $kpi_data_list = json_decode($configesponse,1);


              $total_tasks = count((array)$kpi_data_list);

?>
<script>
function statuspopup(status,id,table_name,auth,task=""){


     if(auth == 0){

         if(task == "task"){
             if($('#checkstausinput'+id).prop("checked") == true){
                 jQuery.ajax({
                     url: "views/custom_insert_status_popup_task.php",
                     data:'table='+table_name+'&status='+status+'&rid='+id,
                     type: "POST",
                     success:function(data){
                         $('#popup1').html(data);
                         $("#kt_modal_KTDatatable_remote").modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                         $('#kt_modal_KTDatatable_remote').modal("show");
                     },
                     error:function (){}
                 });
             }

         }else{
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
     }else{
         if(task == "task"){
             $('#checkstausinput'+id).prop('checked', false);
             $("#checkstausid"+id).removeClass("done-task");
         }
         Swal.fire({
             icon: 'error',
             title: 'Oops...',
             text: auth
         })
     }
 }
</script>
<div class="page-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header table-card-header">
                    <h5>Pending Tasks</h5>
                    <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                    <div class="card-body">
                        <div class="form-group row ml-20 mr-20 j-wrapper  ">
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
                                      <div class="checkbox-fade fade-in-primary" onclick="statuspopup('<?php echo $task['status_list'][0]['status']; ?>','<?php echo $Notification_Table_Id; ?>','<?php echo $Notification_Table; ?>','<?php echo $task['view_status']=="0"?"0":$_SESSION['error_message'][$task['view_status']]; ?>','task')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                          <label class="form-label check-task " id="checkstausid<?php echo $Notification_Table_Id; ?>">
                                              <input aria-label="Checkbox" type="checkbox"  value="" id="checkstausinput<?php echo $Notification_Table_Id; ?>" >
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
    </div>
</div>

