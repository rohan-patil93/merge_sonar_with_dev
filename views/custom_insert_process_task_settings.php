<?php
if(isset($_GET['popup'])){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
}
if(isset($_GET['Rid']) && $_GET['Rid']!="") {
  $rid =  encodeId($_GET['Rid']);
  $rid = urldecode($rid);
  $jrid = explode(":",$rid);
  $row_id = $jrid['1'];
}
$company_id = $_SESSION['company_id'];
$url = API."?mode=gettasksdetails&company_id=".$company_id."&is_active=0&rid=".$row_id;
$response = callAPI($url);
$tasks_details = json_decode($response,1);
$period_list = $tasks_details['period_list'];
$due_list = $tasks_details['due_list'];
$buddy_list = $tasks_details['buddy_list'];
$task_list = $tasks_details['task_list'];
$emp_list = $tasks_details['emp_list'];
$application_pages = $tasks_details['application_pages'];
$display_data = $tasks_details['process_task_settings'];
$processtypedata = $tasks_details['task_process_type'];
$trigger_date_type = $tasks_details['trigger_date_type'];
$emaildata = $tasks_details['email_content'];
$mopdulesetup = $tasks_details['module_setup'];
$title = viewTitle($_GET['option']);
$id = $display_data['id'];

$task_name = $display_data['task_name'];
$assigned_to = $display_data['assigned_to'];
$main_data = $display_data;
if($row_id!=""){
  $where2con["where"] = array('is_active'=> '0','company_id'=>$company_id,'transaction_id'=>$row_id);
  $data2_con = urlencode(json_encode($where2con));
  $url2 = API."?mode=gettabledetailswithconditions&table=filter_tables&data=".$data2_con;
  $response2 = callAPI($url2);
  $filte_data = json_decode($response2,1);
  $filter_id = $filte_data[0]['id'];
}
if(!in_array($assigned_to,(array)$buddy_keys)) {
  $assigned_to = OPTION_OTHERS;
  $emp_id =$display_data['assigned_to'];
}
$process_id = $display_data['process_id'];
$no_action = $display_data['no_action'];
$on_the_day = $display_data['on_the_day'];
$action_no = $display_data['action_no'];
$action_frequency = $display_data['action_frequency'];
$before_or_after = $display_data['before_or_after'];
$description = $display_data['description'];
$files = $display_data['files'];
$display_color = $display_data['display_color'];
$display_background_color = $display_data['display_background_color'];
$remarks = $display_data['remarks'];
$task_for = $display_data['task_for'];
?>
<style>
  .radio-inline .form-label input[type="radio"]{
   display: none;
 }
</style>
<script type="text/javascript" src="../assets/js/custom_insert_process_task_settings.js"></script>
<div class="">
  <input type="hidden" id="getpopup_id" value="<?php echo $_GET['popup']; ?>">
  <div class="card" >
    <?php if($_GET['error'] !=''){ ?>
     <div class="alert alert-danger alert-dismissible">
      <strong> <?php echo $_SESSION['error_message'][$_GET['error']]; ?> </strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php } ?>
  <div class="card card-custom <?php echo $card_sticky; ?> j-pro" id="kt_page_sticky_card ">
    <div class="card-header">
      <h5><?php echo $title;  ?></h5>
      <div class="card-header-right">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
          <i class="icofont icofont-reply"></i>
          Back
        </a>
      </div>
    </div>
    <div class="card-body ">
      <div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="step-first" data-wizard-clickable="true">
        <div class="row ">
          <div class="col-xl-12">
            <form class="form" id="kt_form_submits" method="post" action="" enctype="multipart/form-data">

             <input type="hidden" name="row_id"  value="<?php echo $row_id; ?>">
             <input type="hidden" name="Rid_id"  value="<?php echo $row_id; ?>">
                  <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>">
                  <input type="hidden" name="files" id="files" value="<?php echo urlencode($main_data['files']); ?>">
                  <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">
                  <div class="p-4" data-wizard-type="step-content" data-wizard-state="current">

                    <div class="row">
                      <div class="col-lg-5 mb-1 ">
                        <span class="form-label">Task Name *</span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Task Name" type="text" class="form-control requried_field" name="task_name" value="<?php echo $task_name; ?>" />
                      </div>
                      <?php  if($row_id ==""){ ?>
                        <div class="form-group col-lg-4 w-full">
                          <span class="form-label">Module</span>
                          <span class="d-block error-message text-danger"></span>
                          <select aria-label="Module Setup []" class="form-select js-example-basic-multiple requried_field" id="module_setup_id" name="module_setup_id[]" multiple>
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo ($main_data['module_setup_id'] == $modulesetup['module_id'] || ($_SESSION['work_module_setup_id'] == $modulesetup['module_id'] && $main_data['module_setup_id'] ==""))?"selected":""; ?>><?php echo $modulesetup['display_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      <?php }else{ ?>
                        <div class="form-group col-lg-4 w-full">
                          <span class="form-label">Module</span>
                          <span class="d-block error-message text-danger"></span>
                          <select aria-label="Module Setup" class="form-select js-example-basic-single requried_field" id="module_setup_id_2" name="module_setup_id">
                            <option value="">Select Any One</option>
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $main_data['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['display_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>

                      <?php } ?>



                                            <div class="col-lg-9 mt-2 mb-1">
                        <span class="form-label"> Assign To</span>
                        <div class="form-radio">
                          <?php
                          foreach ((array)$buddy_list as $emptypes) {
                            ?>
                            <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Assign To <?php echo $emptypes['genc_detail_name'] ?? '';?>" type="radio" name="assigned_to" class="d-none showemplist" value="<?php echo $emptypes['genc_detail_code'] ?? '';?>" <?php if(($main_data['assigned_to'] ?? '') == ($emptypes['genc_detail_code'] ?? '')) {?> checked="checked" <?php } ?> />
                                <i class="helper"></i>
                                <?php echo $emptypes['genc_detail_name'] ?? '';?>
                              </label>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                      <?php if(($main_data['assigned_to'] ?? '') !='OB'){ $csd = "d-none"; }else{ $csd = ""; }?>
                      <div id="emplistshow" class="col-xl-4 w-full <?php echo $csd; ?>">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee" name="other_id" class="form-control form-select js-example-basic-single" id="fdrf">
                          <option value="">Select Any One </option>
                          <?php foreach ((array)$emp_list as $emp) { ?>
                            <option value="<?php echo $emp['id'] ?? '';?>" <?php if(($main_data['user_id'] ?? '') == ($emp['id'] ?? '')){ ?>selected <?php } ?> ><?php echo $emp['display_name'] ?? '';?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-lg-9 mt-2 mb-1">
                        <span class="form-label"> Approval Type</span>
                        <div class="form-radio">
                          <?php
                          foreach ((array)$buddy_list as $emptypes) {
                            ?>
                            <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Approval Type <?php echo $emptypes['genc_detail_name'] ?? '';?>" type="radio" name="approval_type" class="d-none showemplist" value="<?php echo $emptypes['genc_detail_code'] ?? '';?>" <?php if(($main_data['approval_type'] ?? '') == ($emptypes['genc_detail_code'] ?? '')) {?> checked="checked" <?php } ?> />
                                <i class="helper"></i>
                                <?php echo $emptypes['genc_detail_name'] ?? '';?>
                              </label>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                      <?php if(($main_data['approval_type'] ?? '') !='OB'){ $csd = "d-none"; }else{ $csd = ""; }?>
                      <div id="emplistshowapprovaltype" class="col-xl-4 w-full mb-2 <?php echo $csd; ?>">
                        <span class="form-label">Employee</span>
                        <select aria-label="Approval Employee" name="approval_other_id" class="form-control form-select js-example-basic-single">
                          <option value="">Select Any One </option>
                          <?php foreach ((array)$emp_list as $emp) { ?>
                            <option value="<?php echo $emp['id'] ?? '';?>" <?php if(($main_data['approval_other_id'] ?? '') == ($emp['id'] ?? '')){ ?>selected <?php } ?> ><?php echo $emp['display_name'] ?? '';?></option>
                          <?php } ?>
                        </select>
                      </div>
<div class="col-lg-9">
                        <div class="row ">
                          <div class="col-lg-6">
                            <span class="form-label">Email Template</span>
                            <select aria-label="Email Content" name="email_content_id" class="form-select js-example-basic-single" id="email_template">
                              <option value="">Select Any One </option>
                              <?php foreach ($emaildata as $email) {  ?>
                                <option value="<?php echo $email['id'];?>" <?php if($main_data['email_content_id'] == $email['id']){ ?>selected <?php
} ?> ><?php echo $email['template_name'];?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <?php if(isset($_GET['process_type'])){?>
                            <input type="hidden" name="process_type" id="process_type" value="<?php echo $_GET['process_type']; ?>">
                          <?php }else{ ?>
                            <div class="col-lg-6">
                              <span class="form-label">Process Type</span>
                              <select aria-label="Process Type" name="process_type" class="form-control form-select js-example-basic-single" id="process_type_2" onchange="processtylelabel(this.value)">
                                <option value="">Select Any One </option>
                                <?php foreach ($processtypedata as $processtype) {  ?>
                                  <option value="<?php echo $processtype['genc_detail_code'];?>" <?php if($main_data['process_type'] == $processtype['genc_detail_code']){ ?>selected <?php
} ?> ><?php echo $processtype['genc_detail_name'];?></option>
                                <?php } ?>
                              </select>
                            </div>
                          <?php } ?>
                        </div>
                      </div>

                      <div class="col-lg-9 mt-3 mt-1">
                        <span>Trigger Date</span></br>
                        <div class="form-radio">
                          <?php $mk =1; foreach($trigger_date_type as $tdt){ ?>
                            <?php
                            $checked = "";
                            if($tdt['genc_detail_code']==$main_data['trigger_date'] || ($main_data['trigger_date'] =="" && $mk == '1')){
                              $checked = "checked";
                            }
                            ?>
                            <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Trigger Date" type="radio" name="trigger_date" value="<?php echo $tdt['genc_detail_code'];?>" <?php echo $checked; ?>/>
                                <i class="helper"></i><?php echo $tdt['genc_detail_name'];?>
                              </label>
                            </div>
                            <?php $mk++;
} ?>

                          </div></br>

                          <div class="mb-3 row">

                            <div class="col-lg-4 col-md-3 col-sm-3 col-12 m-b-10 w-full">
                              <span class="form-label">Start</span>
                              <select aria-label="Before Or After" name="before_or_after" style="width:150px;" class="form-control form-select js-example-basic-single">
                                <option value="">Select</option>
                                <?php foreach ($period_list as $due) {  ?>
                                  <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['before_or_after'] == $due['genc_detail_code']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-lg-1">
                              <span class="form-label">Day</span>
                              <input aria-label="Action No" type="number" class="form-control " name="action_no" value="<?php echo $main_data['action_no'] !=""?$main_data['action_no']:'0';?>" />
                            </div>
                            <div class="col-lg-4 w-full">
                              <span class="form-label">Frequency</span>
                              <select aria-label="Action Frequency" name="action_frequency"  style="width:150px;" class="form-control form-select js-example-basic-single">
                                <?php foreach ($due_list as $due) {    ?>
                                  <option value="<?php echo $due['genc_detail_code'];?>" <?php if($main_data['action_frequency'] == $due['genc_detail_code']){ ?>selected <?php
} ?> ><?php echo $due['genc_detail_name'];?></option>
                                <?php } ?>
                              </select>
                            </div>

                            <div class="col-lg-3">
                              <span class="form-label">Planned Day</span>
                              <input aria-label="Planned Day" type="number" class="form-control col-lg-2" name="planned_day" value="<?php echo $main_data['planned_day'] !=""?$main_data['planned_day']:0;?>">
                            </div>

                          </div>
                        </div>
                      </div>
                      <div class="col-lg-9">
                        <div class="row mt-2 mb-1">
                          <span class="form-label col-lg-3">Category</span>
                          <div class="col-lg-9">
                            <select aria-label="Process" name="process_id" class="form-control form-select js-example-basic-single">
                              <option value="">Select Any One </option>
                              <?php
                              foreach ($task_list as $task) {
                                ?>
                                <option value="<?php echo $task['id'];?>" <?php if($main_data['process_id'] == $task['id']){ ?>selected <?php
} ?> ><?php echo $task['category_name'];?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-9 mt-2 mb-1">
                          <span class="form-label">Description</span>
                          <textarea aria-label="Description" style="overflow:auto" class="form-control form-control-lg" name="description" > <?php echo $main_data['description']; ?> </textarea>
                        </div>
                      </div>

                      <div class="col-lg-9  mt-2 mb-1">
                        <div class="row mb-2">
                          <div class="col-lg-12">
                            <span class="form-label d-block">Link Page (if User Wants To Open Any Custome page Or URL Select This Option)</span>
                            <input aria-label="Link Page" type="checkbox" class="js-switch" value="0" id="link_page" name="link_page" onchange="checkPageRedirect()" <?php echo $main_data['link_page'] =='0'?"checked":""; ?>>
                          </div>
                        </div>

                        <div class="row hideurl <?php echo $main_data['link_page'] =='0'?"":"d-none"; ?>" >
                          <div class="col-lg-4">
                            <span class="form-label d-block">Internal Link</span>
                            <input aria-label="Internal Link" type="checkbox" class="js-switch" value="0" id="internal_link" name="internal_link" onchange="checkPageInternalLink()" <?php echo $main_data['link_page'] =='0'?"checked":""; ?>>
                          </div>

                          <div class="col-lg-4 w-full internallinkclass">
                            <span class="form-label d-block">Internal Page</span>
                            <select aria-label="Select option" class="form-control form-select js-example-basic-single" onchange="pageChange(this.value)">
                              <option value="">Select Any One </option>
                              <?php
                              foreach ($application_pages as $applicationpages) {
                                ?>
                                <option value="<?php echo $applicationpages['insert_page_name'];?>"  ><?php echo $applicationpages['page_title'] !=""?$applicationpages['page_title']:ucwords(str_replace("_"," ",($applicationpages['application_table_name'])));?></option>
                              <?php } ?>

                            </select>
                          </div>

                      <div class="col-lg-12 mt-2 mb-1">
                        <span class="form-label d-block">Page URL</span>
                        <textarea aria-label="Page Url"  class="form-control form-control-lg" id="page_url" name="page_url" > <?php echo $main_data['page_url']; ?> </textarea>
                      </div>
                    </div>
                  </div>

              <div class="row hidenoturl <?php echo $main_data['link_page'] !='0'?"":"d-none"; ?>" >
                <div class="col-lg-6 mt-3">
                 <span class="form-label">This is required for...</span>
               </div>
               <div id="tsk_fortable"></div>
               <div class="col-lg-12">
                <i class="flaticon2-group text-success"></i>
                <a href="#" class="text-primary showpopup">Edit </a>
              </div>
            </div>
            <div class="col-lg-12 mt-3 hidenoturl <?php echo $main_data['link_page'] !='0'?"":"d-none"; ?>" >
              <div id="append_statick" class="">
                <div class="row" style="margin-top: 10px;">
                  <div class="col-lg-4">
                    <span class="form-label">Check List</span>
                  </div>
                  <div class="col-lg-1">
                    <span class="form-label text-center">Mandatory</span>
                  </div>
                  <div class="col-lg-1">
                    <span class="form-label text-center">Attachment</span>
                  </div>
                  <div class="col-lg-1 text-center">
                    <span class="form-label">Form</span>
                  </div>

                        <div class="col-lg-1">
                          <span>Action</span>
                        </div>
                      </div>
                      <?php $checklist_data = json_decode($main_data['check_list'],1);

                      $m=1;
                      if(count((array)$checklist_data) == 0){ $checklist_data[] = array("name"=>"");
}
                      foreach ($checklist_data as $checkvalue) {
                        ?>
                        <div class="row sfitemRow mt-1" id="static_div_<?php echo $m; ?>">
                          <div class="col-lg-4">
                            <input aria-label="Input field" type="text" class="form-control"  id="static_value_<?php echo $m; ?>"  name="checklist[]" value="<?php echo $checkvalue['name']; ?>">
                          </div>

                          <div class="col-lg-1 text-center">
                            <div class="checkbox-fade fade-in-primary">
                             <label class="form-label mt-2">
                              <input aria-label="Checkbox" type="checkbox" value="Y" name="mandatory_checklist[<?php echo $m; ?>]" <?php echo $checkvalue['mandatory_checklist'] == "Y"?"checked":""; ?>>
                              <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                              </span>
                            </label>
                          </div>
                        </div>
                        <div class="col-lg-1 text-center">
                          <input type="hidden" name="checkboxid[]" value="<?php echo $m; ?>">
                          <div class="checkbox-fade fade-in-primary">
                           <label class="form-label mt-2">
                            <input aria-label="Checkbox" type="checkbox" value="Y" name="attachment_requerd[<?php echo $m; ?>]" <?php echo $checkvalue['attachment_requerd'] == "Y"?"checked":""; ?>>
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                          </label>
                        </div>
                      </div>
                      <div class="col-lg-1 text-center">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
                              <label class="btn-hover-text-primary text-dark" data-action="remove" data-bs-toggle="tooltip" title="Upload File" style="left: -9px;top: 0px;">
                                <span> <i class="fa fa-upload"></i></span>
                                <input aria-label="Dyanamic File" type="file" name="dyanamic_file" accept=".pdf" class="updateresume"  />
                                <input type="hidden" name="hidden_file[]" value="<?php echo $checkvalue['downloadfile']; ?>">
                              </label>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <?php if($checkvalue['downloadfile'] !=""){ ?>
                              <a href="uploads/all/<?php echo $checkvalue['downloadfile']; ?>" download data-action="remove" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download"></i></a>
                            <?php } ?>
                          </div>
                        </div>


         </div>
         <div class="col-lg-1 text-center">
           <input type="hidden" value="0" id="static_delete_value_<?php echo $m; ?>" >
           <i class="fa fa-trash text-danger deleteRows" data-id="static_div_<?php echo $m; ?>" ></i>
         </div>
       </div>

       <?php $m++;
} ?>
     </div>
     <div class="" style="margin-top: 10px;">
       <button class="btn btn-success waves-effect waves-light col-lg-3 fw-bold addstatickRows123" id="addstatickRows" type="button" >+ Add Check List</button>                  </div>
     </div>
   </div>

   <div class="p-4">
    <button type="button" name="submit" class="btn btn-primary waves-effect waves-light text-uppercase submittasksettings mt-15 ml-3" id="submittasksettings_btn"/> submit</button>
  </div>
</div>
</form>
</div>
</div>
</form>
</div>
</div>
</div>
</div>

<div id="kt_modal_dynamic_popup" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" >
    <div class="modal-content j-wrapper"  id="dynamic_popup1">

    </div>
  </div>
</div>

<div id="kt_modal_dynamic_popup_task_setting" class="modal fade" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-xl" >
    <div class="modal-content"  id="dynamic_popup_task_settings">

    </div>
  </div>
</div>

<?php if($_GET['popup']==0){ ?>
  <script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
  </script>
  <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
  <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<?php } ?>

