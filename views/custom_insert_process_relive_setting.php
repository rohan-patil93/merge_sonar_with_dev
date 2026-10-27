<?php
if(isset($_GET['Rid']) && $_GET['Rid']!="") {
    $row_id = $_GET['Rid'];
}
$company_id = $_SESSION['company_id'];
$url = API."?mode=gettasksdetails&company_id=".$company_id."&is_active=0";
   $response = callAPI($url);
   $tasks_details = json_decode($response,1);
   $period_list = $tasks_details['period_list'];
   $due_list = $tasks_details['due_list'];
   $buddy_list = $tasks_details['buddy_list'];
   $task_list = $tasks_details['task_list'];
   $emp_list = $tasks_details['emp_list'];
$buddy_keys = array_keys($buddy_list['genc_detail_code']);

$wherecon["where"] = array('is_active'=> '0','company_id'=>$company_id,'id'=>$row_id);
$data_con = urlencode(json_encode($wherecon));
$url = API."?mode=gettabledetailswithconditions&table=process_task_settings&data=".$data_con;
$response = callAPI($url);
$display_data = json_decode($response,1);
$id = $display_data[0]['id'];
$task_name = $display_data[0]['task_name'];
$assigned_to = $display_data[0]['assigned_to'];
if(!in_array($assigned_to,$buddy_keys)) {
    $assigned_to = OPTION_OTHERS;
    $emp_id =$display_data[0]['assigned_to'];

      ?>
  <script type="text/javascript">document.getElementById('emplistshow').style.display = 'block';</script>

  <?php
}
$process_id = $display_data[0]['process_id'];
$no_action = $display_data[0]['no_action'];
$on_the_day = $display_data[0]['on_the_day'];
$action_no = $display_data[0]['action_no'];
$action_frequency = $display_data[0]['action_frequency'];
$before_or_after = $display_data[0]['before_or_after'];
$description = $display_data[0]['description'];
$files = $display_data[0]['files'];
$display_color = $display_data[0]['display_color'];
$display_background_color = $display_data[0]['display_background_color'];
$remarks = $display_data[0]['remarks'];
$task_for = $display_data[0]['task_for'];

/** Get Task details ***/


if (isset($_POST['submit'])){
    if($_POST['due_date']=="no_action") {
        $_POST['no_action'] = '1';
    }elseif($_POST['due_date']=="on_the_day") {
        $_POST['on_the_day'] = '1';
    }
 $task_list = $tasks_details['task_list'][$_POST['process_id']];
  $_POST['display_color'] = $task_list['display_color'];
  $_POST['display_background_color'] = $task_list['display_background_color'];
$_POST['files'] = urldecode($_POST['files'],1);
$_POST['task_for'] = urldecode($_POST['task_for'],1);

if($_POST['assigned_to']==OPTION_OTHERS) {
$_POST['assigned_to'] = $_POST['category'];
}
        $_POST['row_id']=$_GET['Rid'];

        $url = API."?mode=processtasksettings&method=post";
    $response = file_post_contents($url,$_POST,'insert');

    }
 $pageurl = API."?mode=displayattributesview&fid=application_table_name&view=process_task_settings";
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$content = $list['help_text'] != '' ? $list['help_text'] : "Help Text";

  $title = viewTitle($_GET['option']);

?>
<script type="text/javascript">
   function showemplist(value) {
   if(value=='OB') {
   document.getElementById('emplistshow').style.display ='block';
   } else {
   document.getElementById('emplistshow').style.display ='none';

   }
   }

   function showpopup(popup_function_name){
  var filter = document.getElementById('filter').value;
      var sendlist = filter;

  var files = document.getElementById('files').value;
      var sendfile = files;

      console.log(filter);

     if(popup_function_name=="newtask"){
          jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'select='+sendlist,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});
            },
            error:function (){}
        });

     }

     if(popup_function_name=="addfiles"){
          jQuery.ajax({
            url: "views/newtasks_addfiles.php",
            data:'filelist='+sendfile,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});


            },


            error:function (){}
        });

     }
  }

function getfile()
{

    var selectedfileslist = document.getElementById('files').value;
  console.log("************************"+selectedfileslist);

   $.ajax({
     url: "ajax.php",
       data:'page=selectedfiles&files='+selectedfileslist,
       type: "POST",
       success: function(response) {
         console.log("response ="+response);
          var division = document.getElementById('divshowfiles');
           division.innerHTML = response;
      }
    });
}

function getnamelist(){

  var selectednames = document.getElementById('filter').value;

   $.ajax({
     url: "ajax.php",
       data:'page=selectednames&names='+selectednames,
       type: "POST",
       success: function(response) {
          var division = document.getElementById('tsk_fortable');

         console.log("response ="+response);
           division.innerHTML = response;
      }
    });
}
</script>
  <div class="d-flex flex-column-fluid">
  <div class="container-fluid" >
      <?php if($content !='' && $_POST['option']==''){ ?>
    <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text"><?php echo $content; ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
    <?php }
if($_GET['error'] !=''){ ?>
      <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">  <?php echo $_SESSION['error_message'][$_GET['error']]; ?> </div>
        <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-close"></i></span>
                </button>
            </div>
      </div>
    <?php } ?>
          <div class="card card-custom <?php echo $card_sticky; ?>" id="kt_page_sticky_card">
        <div class="card-header">
          <div class="card-title">

            <h3 class="card-label">&nbsp;<?php echo $title;  ?> </h3>
          </div>
          <div class="card-toolbar">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
              <i class="ki ki-long-arrow-back icon-xs"></i>
              Back
            </a>
          </div>
        </div>
              <div class="card-body p-0">
                  <!--begin: Wizard-->
                  <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
                      <!--begin: Wizard Nav-->

                      <!--end: Wizard Nav-->
                      <!--begin: Wizard Body-->
                      <div class="row justify-content-center py-10 px-8 py-lg-12 px-lg-10">
                          <div class="col-xl-12 col-xxl-7">

                              <form class="form" id="kt_form" method="post" action="" enctype="multipart/form-data">
                                  <!--begin: Wizard Step 1-->
  <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>">
  <input type="hidden" name="files" id="files" value="<?php echo urlencode($files); ?>">
  <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="">

  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
      <h4 class="mb-10 font-weight-bold text-dark">Tasks</h4>

      <div class="row">
          <div class="col-xl-9">

              <span>Task Name *</span>
              <input aria-label="Task Name" type="text" class="form-control" name="task_name" value="<?php echo $task_name; ?>" />
          </div>
          <div class="col-xl-12 mt-5">
              <span>Assign To</span>
              <div class="radio-inline ">
                  <?php
                  foreach ($buddy_list as $emptypes) {
                      ?>
                      <label class="radio radio-solid">
                          <input aria-label="Assigned To" type="radio" name="assigned_to"  onclick="showemplist(this.value);" value="<?php echo $emptypes['genc_detail_code'];?>" <?php if($assigned_to == $emptypes['genc_detail_code']) {?> checked="checked" <?php
} ?> onkeydown="" />
                          <span></span>
                          <?php echo $emptypes['genc_detail_name'];?>
                      </label>
                  <?php } ?>
              </div>

</div>
          <div style="display: none"  id="emplistshow" class="col-xl-6 mt-5">

              <select aria-label="Category" name="category" class="form-control">
                  <option value="">Select Any One </option>
                  <?php
                  foreach ($emp_list as $emp) {
                      ?>
                      <option value="<?php echo $emp['id'];?>" <?php if($emp_id == $emp['id']){ ?>selected <?php
} ?> ><?php echo $emp['display_name'];?></option>
                  <?php } ?>
              </select>
          </div>

          <div class="col-xl-12 mt-6">


              <span>Due Date</span>
              <div class="radio-inline mt-3">
                  <label class="radio">
                      <input aria-label="Due Date" type="radio" name="due_date" <?php if($no_action =='1') {?> checked="checked" <?php
} ?>  value="no_action"/>
                      <span></span>
                      None
                  </label>
              </div>
                  <div class="radio-inline mt-3">
                  <label class="radio radio-solid">
                      <input aria-label="Due Date" type="radio" name="due_date" <?php if($on_the_day=='1') {?> checked="checked" <?php
} ?> value="on_the_day"/>
                      <span></span>
                      On Hire Date
                  </label>
</div>
<div class="radio-inline mt-3">
                  <label class="radio radio-solid">
                      <input aria-label="Due Date" type="radio" name="due_date" <?php if($action_no !='0' || !empty($action_no)) {?> checked="checked" <?php
} ?> value="frequency"/>
                      <span></span>

                  </label>

                  <input aria-label="Action No" type="text" class="form-control mr-2" style="width:50px;"  name="action_no" value="<?php echo $action_no;?>" />

                  <select aria-label="Action Frequency" name="action_frequency"  style="width:150px;"class="form-control">
                      <option value="">Select</option>
                      <?php
                      foreach ($due_list as $due) {
                          ?>
                          <option value="<?php echo $due['genc_detail_code'];?>" <?php if($action_frequency == $due['id']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                      <?php } ?>
                  </select>
                  &nbsp;
                  <select aria-label="Before Or After" name="before_or_after" style="width:150px;" class="form-control">

                      <option value="">Select</option>
                      <?php

                      foreach ($period_list as $due) {
                          ?>
                          <option value="<?php echo $due['genc_detail_code'];?>" <?php if($before_or_after == $due['id']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                      <?php } ?>
                  </select> &nbsp;
                  <span class="mt-3 ml-2"> Reliving  </span>

</div>
              </div>

          </div>

          <div class="row">
                                        <div class="col-xl-9 mt-5">

              <span>Category</span>
              <select aria-label="Process" name="process_id" class="form-control">
                  <option value="">Select Any One </option>
                  <?php
                  foreach ($task_list as $task) {
                      ?>
                      <option value="<?php echo $task['id'];?>" <?php if($process_id == $task['id']){ ?>selected <?php
} ?> ><?php echo $task['category_name'];?></option>
                  <?php } ?>
              </select>
          </div>
      </div>
                                    <div class="row">
          <div class="col-xl-9 mt-5">
              <span>Description</span>
              <textarea aria-label="Description" style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $description; ?> </textarea>
          </div>
</div>
              <div class="row">
          <div class="col-xl-6 mt-5">
              <span>Attach File(s)</span>
              <button type="button" onclick="showpopup('addfiles');">Add</button>

              <div id="divshowfiles"></div>
</div>

                                        </div>
                                        <div class="row">
                                        <div class="col-xl-6 mt-5">
                                            <span>This is required for...</span>
                                        </div>
                                        <div id="tsk_fortable"></div>

                                        <div class="col-xl-12">
                                            <i class="flaticon2-group text-success"></i>

                                            <button type="button" onclick="showpopup('newtask');">Edit </button>
                                        </div>
                                    </div>

                                    <input type="submit" name="submit" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 mt-15"/>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end: Wizard Step 4-->
                    <!--begin: Wizard Actions-->
                    <!--end: Wizard Actions-->
                </form>

            </div>
        </div>
        <!--end: Wizard Body-->
    </div>
    <!--end: Wizard-->
</div>
</div>
</div>
<!--end::Container-->
</div>
