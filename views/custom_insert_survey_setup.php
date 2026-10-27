<?php
$_SESSION['apply_js']['multiselest'] = '1';
$category = array('1' =>'Survey', '2'=> 'Poll');
//if($_GET['CAT']=="" && $_GET['option']="COM_INSERT_SURVEY_SETUP"){
if($_GET['CAT']==""){
  $_GET['CAT'] = 1;
}
$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_SURVEY_SETUP,TBL_SURVEY_SETUP_QUESTIONS,TBL_SURVEY_SETUP_QUESTION_OPTIONS);
if(isset($_GET['Rid']) && $_GET['Rid']!=""){
 $rids =  encodeId($_GET['Rid']);
 $rid = urldecode($rids);
 $cand = explode(':', $rid);
 $row_id = $cand['1'];
}else{
 $_SESSION['questions_list'] = array();
}
$table_name= $table_list[0];
$company_id = $_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];
$vurl = API."?mode=getsurveydetails&company_id=".$company_id."&emp_id=".$emp_id."&is_active=0&survey_id=".$row_id;
$configesponse = callAPI($vurl);
$employeeDetails = json_decode($configesponse,1);
//echo "<pre>";print_r($employeeDetails);echo "<pre>";
$employee_list = $employeeDetails['employeedetails'];
$qustion_types = $employeeDetails['qustion_types'];
$survey_details = $employeeDetails['survey_details'];
$survey_qustion_details = $employeeDetails['survey_qustion_details'];
$survey_option_details = $employeeDetails['survey_option_details'];
$publish_status = $survey_details['publish_status'];
$filtertbl_rowid = $survey_details['survey_assigned_to'];
$allocated_time =  $survey_details['allocated_time'];
$assign_to_prefill = '';
if($filtertbl_rowid != ''){
 $filterurl = API."?mode=getfilterdetails&company_id=".$company_id."&filter_id=".$filtertbl_rowid."&filter_table=employees";
 $filterresponse = callAPI($filterurl);
 $filterdetails = json_decode($filterresponse,1);
 if(isset($filterdetails['filter_data']['content']) && $filterdetails['filter_data']['content'] != ''){
  $filtercontent = trim($filterdetails['filter_data']['content'],'"');
  $prefill_list = json_decode($filtercontent,1);
  if(is_array($prefill_list) && count((array)$prefill_list) > 0){
   $assign_to_prefill = $filtercontent;
  }else{
   $assign_to_prefill = ALLEMPLOYEES;
  }
 }
}

foreach ($survey_option_details as $option_data){
 $answer[$option_data['question_id']] =  json_decode($option_data['survey_question_option'],1);
}
foreach ($survey_qustion_details as $qdata) {
 $starray[] =array("options_display_type"=>$qdata['options_display_type'],"survey_question"=>$qdata['survey_question'],"require_response"=>$qdata['require_response'],"allow_multiple_selections"=>$qdata['allow_multiple_selections'],"survey_question_option"=>$answer[$qdata['id']],'question_id'=>$qdata['id']);
}
$who_can_see_id = json_decode($survey_details['who_can_see_the_results'],1);
//$survey_date = $survey_details['survey_start_date']." ".$survey_details['survey_start_time']." / ".$survey_details['survey_end_date']." ".$survey_details['survey_end_time'];
$survey_types = $employeeDetails['survey_types'][$_GET['CAT']];
foreach ($qustion_types as $key => $value) {
 $question_types[$value['genc_detail_code']]  = $value['genc_detail_code'];
 $question_title[$value['genc_detail_code']]  = $value['genc_detail_name'];
}
if($row_id!='') {
 foreach ($survey_qustion_details as $qkey => $qvalue) {
  $qid = $qvalue['id'];
  $questions_list = array('title'=>$question_title[$qvalue['options_display_type']],'type'=>$qvalue['options_display_type'],'question'=>$qvalue['survey_question'],'require_response'=>$qvalue['require_response'],'allow_multiple_selections'=>$qvalue['allow_multiple_selections'],'option_data'=>$answer[$qid],'rid'=>$row_id,'question_id'=>$qid);
  $questiondetails[$qid] = json_encode($questions_list,1);
}
$_SESSION['questions_list'] = $questiondetails;
}
if(isset($_POST['create'])){
 $survey_start_date = date('Y-m-d',strtotime(str_replace('/','-',$_POST['survey_start_date'])));
 $survey_start_time = "12:00:00";
 $survey_end_date = date('Y-m-d',strtotime(str_replace('/','-',$_POST['survey_end_date'])));
 $survey_end_time = "12:00:00";
 $survy_setup_array = array('survey_name'=>$_POST['survey_name'],'survey_type'=>$_POST['survey_type'],'description'=>$_POST['description'],'survey_start_date'=> $survey_start_date,'survey_start_time'=>$survey_start_time,'survey_end_date'=>$survey_end_date,'survey_end_time'=>$survey_end_time,'survey_initiator'=>$_POST['survey_initiator'],'allocated_time'=>'0','survey_assigned_to'=>$_POST['survey_assigned_to'],'who_can_see_the_results'=>json_encode($_POST['who_can_see_the_results'],1));
 $survy_setup_array['created_date'] = nowDate();
 $survy_setup_array['created_by'] = $_SESSION['userid'];
 $survy_setup_array['is_active'] = "0";
 if($_GET['Rid']==""){
  $survy_setup_array['current_status'] = "0";
  $survy_setup_array['publish_status'] = "1";
 }
 $survy_setup_array['category'] = $_GET['CAT'];
 $survy_setup_array['company_id'] = $_SESSION['company_id'];
 $qustion = array();
 if(count((array)$_POST['value_id']) > 0){
  foreach ($_POST['value_id'] as $vkey => $i) {
   $survey_question='survey_question'.$i;
   $options_display_type='options_display_type'.$i;
   $question_id='question_id'.$i;
   $allow_multiple_selections='allow_multiple_selections'.$i;
   $require_response='require_response'.$i;
   $survey_question_option='survey_question_option'.$i;
   $_POST[$allow_multiple_selections] = $_POST[$allow_multiple_selections]== 'on' ? '0' : '1';
   $_POST[$require_response] = $_POST[$require_response]== 'on' ? '0' : '1';
   $qustion[]=array('rid'=>$_POST['rid'],'question_id'=>$_POST[$question_id],'survey_question'=>$_POST[$survey_question],'options_display_type'=>$_POST[$options_display_type],'require_response'=>$_POST[$require_response],'allow_multiple_selections'=>$_POST[$allow_multiple_selections],'survey_question_option'=>json_encode($_POST[$survey_question_option],1));
  }
 }
 $data['main'] = $survy_setup_array;
 $data['qustion'] = $qustion;
 if($_GET['Rid']==""){
  $url = API."?mode=createsurveys&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post";
 }else{
  $url = API."?mode=createsurveys&table_setup=".$table_list[0]."&table_questions=".$table_list[1]."&table_options=".$table_list[2]."&method=post&rowid=".$row_id;
 }
 $retunn_survey_id = file_post_contents($url,$data,'insert');
 if($_GET['Rid']!=""){
  header("location:index.php?option=".$_GET['option']."&CAT=".$_GET['CAT']."&Rid=".$_GET['Rid']);exit;
 }else{
  header("location:index.php"); exit;
 }
}

$workflowsteps = array();
$currentStatus = 0;
if(isset($row_id) && $row_id != ''){
 $currentStatus = isset($survey_details['current_status']) ? (int)$survey_details['current_status'] : 0;
 $workflowsteps = getWorkflowStatus(TBL_SURVEY_SETUP, $row_id, $currentStatus);
}

$disabled = '';
$editableStatuses = array(ST_DRAFT, ST_ACTIVE, ST_SEND_BACK, ST_CALLBACK, "");
if(isset($row_id) && $row_id != '' && !in_array($currentStatus, $editableStatuses)){
 $disabled = 'event_disabled';
}
?>
<!-- <script src="assets/js/pages/myscrpit/bootstrap-daterangepicker.js"></script>  -->
<script type="text/javascript">
 function deletequestion(qid){
  $.ajax({
   url: "ajax.php",
   data:'page=removeqidfromsession&qid='+qid,
   type: "POST",
   success:function(){
    $('#'+qid).remove();
  },
  error:function (){}
});
}
function addnewoptionrows(qid) {
  var op = document.getElementById('option_row').value;
  var op = parseInt(op)+1;
     var optionlistid = "optionlist"+qid+op;
     $("#options"+qid).append('<div id='+"'"+optionlistid+"'"+'> <div class="form-group row" id="'+"'"+op+"'"+'"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"> <input type="text" class="form-control display-inline"  name="survey_question_option'+qid+'[]" placeholder="Option"> <div class="input-group-append"><span class="input-group-text bg-primary"><span><i class="fa fa-trash close text-white" onclick="delnewoptionrows('+"'"+op+"'"+','+"'"+qid+"'"+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Option"></i></span></span></div></div></div></div></div>');
     document.getElementById('option_row').value = op ;
   }


   function delnewoptionrows(rowid,qid) {
   //alert(qid);

   //alert(rowid);
   var optionlistid = "optionlist"+qid+rowid;
   $("#"+optionlistid).remove();
 }
 function dynamiceditpopup(type,title){
   jQuery.ajax({
    url: "views/custom_insert_survey_addquestions.php",
    data:'type='+type+"&question_title="+title,
    type: "GET",
    success:function(data){
     $('#popup2').html(data);
   //$('#kt_modal_KTDatatable_setup').modal({ show: true});
   $("#kt_modal_KTDatatable_setup").modal("show");
 },
 error:function (){}
});
 }
 $(document).on("click", ".submitDynamicWorkflowPopUp", function () {
  var table_name = $(this).data("table_name");
  var transaction_id = $(this).data("transaction_id");
  var current_status = $(this).data("current_status");
  var next_status = $(this).data("next_status");
  submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status);
 });

 $(document).on("click", ".submitDynamicWorkflow", function () {
  var table_name = $(this).data("table_name");
  var transaction_id = $(this).data("transaction_id");
  var current_status = $(this).data("current_status");
  var next_status = $(this).data("next_status");
  var old_status_name = $(this).data("old_status_name");
  var new_status_name = $(this).data("new_status_name") || $(this).attr("new_status_name");
  submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name);
 });

 function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
  jQuery.ajax({
   url: "views/custom_insert_dynamic_status_popup.php",
   data: 'table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
   type: "POST",
   success: function (data) {
    $('#popup1').html(data);
    $('#kt_modal_KTDatatable_remote').modal("show");
   },
   error: function () {}
  });
 }

 function submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name) {
  swal({
   title: "<?php echo ucwords((string)($_SESSION['config']['name'] ?? '')); ?>",
   text: "Please note the status of the survey will change to " + new_status_name,
   type: "info",
   showCancelButton: true,
   confirmButtonClass: "btn-danger",
   confirmButtonText: "Yes",
   cancelButtonText: "No",
   closeOnConfirm: false
  },
  function () {
   jQuery.ajax({
    url: "ajax.php",
    data: 'page=submitstatusdynamicworkflow&table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
    type: "POST",
    success: function (response) {
     var res = JSON.parse(response);
     window.location.href = window.location.href;
     if (res.transaction_update.status == 'sucess') {
      window.location.href = window.location.href;
      swal({
       title: "<?php echo ucwords((string)($_SESSION['config']['name'] ?? '')); ?>",
       text: "<?php echo (string)($_SESSION['error_message']['9059'] ?? ''); ?>",
       type: "success",
       showCancelButton: false,
       closeOnConfirm: false,
       showLoaderOnConfirm: true
      }, function () {
       window.location.href = window.location.href;
      });
     } else {
      swal({
       title: "Oops...",
       text: res.transaction_update.message,
       type: "error",
       showCancelButton: true,
       showConfirmButton: false,
       cancelButtonText: "Ok",
       confirmButtonClass: "btn-danger",
       closeOnCancel: false
      });
     }
    },
    error: function () {}
   });
  });
 }

 function runFilterBulktransferScripts(container){
  jQuery(container).find('script').each(function(){
   if(this.src){
    jQuery.getScript(this.src);
   }else{
    jQuery.globalEval(this.textContent || this.innerText || '');
   }
  });
 }
 function showpopup(){
  var filter_id = document.getElementById('filtertbl_rowid').value;
  jQuery.ajax({
    url: "views/custom_filter_bulktransfer.php",
    data:'filter_id='+filter_id,
    type: "GET",
    success:function(data){
     $('#dynamic_popup1').html(data);
     runFilterBulktransferScripts('#dynamic_popup1');
     $('#kt_modal_dynamic_popup').modal('show');
   },
   error:function (){}
 });
}
 function getnamelist(emplist){
  if(!emplist){
   return;
  }
  document.getElementById('filterby').value = emplist;
  $.ajax({
   url: "ajax.php",
   data:'page=selectednames&names='+encodeURIComponent(emplist),
   type: "POST",
   success: function(response) {
    document.getElementById('tsk_fortable').innerHTML = response;
   }
  });
 }

 function parseSurveyDateDmy(dateStr){
  var parts = dateStr.trim().split('/');
  if(parts.length != 3){
   return null;
  }
  var day = parseInt(parts[0], 10);
  var month = parseInt(parts[1], 10);
  var year = parseInt(parts[2], 10);
  if(isNaN(day) || isNaN(month) || isNaN(year)){
   return null;
  }
  var parsedDate = new Date(year, month - 1, day);
  if(parsedDate.getFullYear() != year || parsedDate.getMonth() != (month - 1) || parsedDate.getDate() != day){
   return null;
  }
  return parsedDate;
 }

 function validateSurveySetupForm(){
  var displayTag = <?php echo json_encode($display_tag); ?>;
  var surveyNameEl = document.querySelector('#survey_setup_form input[name="survey_name"]');
  var descriptionEl = document.querySelector('#survey_setup_form textarea[name="description"]');
  var startDateEl = document.querySelector('#survey_setup_form input[name="survey_start_date"]');
  var endDateEl = document.querySelector('#survey_setup_form input[name="survey_end_date"]');
  var surveyTypeEl = document.querySelector('#survey_setup_form input[name="survey_type"]:checked');
  var assignToEl = document.getElementById('filtertbl_rowid');

  if(!surveyNameEl || surveyNameEl.value.trim() == ''){
   return {valid:false, message:displayTag+' name is required.'};
  }
  if(!surveyTypeEl || surveyTypeEl.value.trim() == ''){
   return {valid:false, message:displayTag+' type is required.'};
  }
  if(!descriptionEl || descriptionEl.value.trim() == ''){
   return {valid:false, message:displayTag+' description is required.'};
  }
  if(!startDateEl || startDateEl.value.trim() == ''){
   return {valid:false, message:'Start date is required.'};
  }
  if(!endDateEl || endDateEl.value.trim() == ''){
   return {valid:false, message:'End date is required.'};
  }
  console.log('startDateEl', startDateEl.value);
  console.log('endDateEl', endDateEl.value);
  // var startDate = parseSurveyDateDmy(startDateEl.value);
  // var endDate = parseSurveyDateDmy(endDateEl.value);
  var startDate = startDateEl.value;
  var endDate = endDateEl.value;
  console.log('startDate', startDate);
  console.log('endDate', endDate);
  if(startDate == null || endDate == null){
   return {valid:false, message:'Please enter valid start and end dates (DD/MM/YYYY).'};
  }
  if(endDate < startDate){
   return {valid:false, message:'End date must be on or after start date.'};
  }
  if(!assignToEl || assignToEl.value.trim() == '' || assignToEl.value.trim() == '0'){
   return {valid:false, message:'Assign to is required. Please use Edit to select employees.'};
  }
  var valueIds = document.querySelectorAll('#survey_setup_form input[name="value_id[]"]');
  if(valueIds.length == 0){
   return {valid:false, message:'At least one question is required.'};
  }
  for(var v = 0; v < valueIds.length; v++){
   var qIndex = valueIds[v].value;
   var questionEl = document.querySelector('#survey_setup_form textarea[name="survey_question'+qIndex+'"]');
   var qtypeEl = document.querySelector('#survey_setup_form input[name="options_display_type'+qIndex+'"]');
   if(!questionEl || questionEl.value.trim() == ''){
    return {valid:false, message:'Question text is required for all questions.'};
   }
   var qtype = qtypeEl ? qtypeEl.value : '';
   if(qtype == '1' || qtype == '3'){
    var optionEls = document.querySelectorAll('#survey_setup_form input[name="survey_question_option'+qIndex+'[]"]');
    var nonemptyCount = 0;
    for(var o = 0; o < optionEls.length; o++){
     if(optionEls[o].value.trim() != ''){
      nonemptyCount++;
     }
    }
    if(nonemptyCount < 2){
     return {valid:false, message:'Each multiple choice/ranking question must have at least 2 options.'};
    }
   }
   if(qtype == '2'){
    var ratingEl = document.querySelector('#survey_setup_form input[name="survey_question_option'+qIndex+'[]"]');
    var scaleVal = ratingEl ? parseInt(ratingEl.value, 10) : 0;
    if(isNaN(scaleVal) || scaleVal <= 0){
     return {valid:false, message:'Rating scale must be greater than 0 for all rating questions.'};
    }
   }
  }
  return {valid:true, message:''};
 }

 jQuery(document).ready(function(){
  var assignPrefill = <?php echo json_encode($assign_to_prefill); ?>;
  if(assignPrefill){
   getnamelist(assignPrefill);
  }
  jQuery('#survey_setup_form').on('submit', function(e){
   var result = validateSurveySetupForm();
   if(!result.valid){
    e.preventDefault();
    swal('Validation Error', result.message, 'error');
   }
  });
 });
 </script>
<div class="card">          
  <div class="card-header table-card-header">
    <h5><?php echo $display_tag; ?> Setup</h5>
    <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light "><i class="icofont icofont-reply"></i>Back</a>
    </div>
  </div>
  <div class="card-custom">
    <div class="card-body">
      <?php if(isset($row_id) && $row_id != '') { ?>
      <?php echo $workflowsteps['current_status_ribbon']; ?>
      <div class="row mb-3">
        <div class="col-lg-6">
          <?php echo $workflowsteps['breadcrumb']; ?>
        </div>
        <div class="col-lg-6 text-right">
          <?php echo $workflowsteps['workflow_btn']; ?>
        </div>
      </div>
      <?php } ?>
      <form method="post" class="<?php echo $disabled; ?>" action="" id="survey_setup_form">
        <div class="row">
          <div class="col-lg-6 px-4">
            <input type="hidden" id="filterby" name="filterby"> 
            <input type="hidden" name="survey_assigned_to" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
            <div class="card card-custom gutter-b">
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label"><?php echo $display_tag; ?> Name <span class="text-danger">*</span></label>
                  <input type="text" name="survey_name" value="<?php echo $survey_details['survey_name'];?>" class="form-control" placeholder="Enter Title" required>
                </div>
                <div class="form-group">
                  <label class="form-label"><?php echo $display_tag; ?> Type <span class="text-danger">*</span></label>
                  <div class="col-9 col-form-label">
                    <div class="radio-inline">
                      <?php foreach ($survey_types as $value) { ?>
                      <label class="radio radio-success">
                        <input type="radio" name="survey_type" value="<?php echo $value['genc_detail_code'];?>" <?php if($survey_details['survey_type'] == $value['genc_detail_code'] || $survey_details['survey_type'] =="") {?> checked="checked" <?php } ?>  />
                        <span></span><?php echo $value['genc_detail_name']; ?>
                      </label>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group mt-3">
                    <label class="form-label"><?php echo $display_tag; ?> Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="description" placeholder="Enter Title" required><?php echo $survey_details['description'];?></textarea>
                  </div>
                  <div class="form-group">
                    <label class="form-label"><?php echo $display_tag; ?> Date Range <span class="text-danger">*</span></label>
                    <div class='row' id='kt_daterangepicker_survey'>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <input type='text' class="form-control datepicker"  name="survey_start_date"  value="<?php echo $survey_details['survey_start_date']; ?>" required />
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <input type='text' class="form-control datepicker"  name="survey_end_date" value="<?php echo $survey_details['survey_end_date']; ?>" required />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="example-date-input" class="form-label"><?php echo $display_tag; ?> Initiator</label>
                    <select class="form-select js-example-basic-single" id="kt_select2_48" name="survey_initiator">
                      <option> Select any one</option>
                        <?php $survey_init =  $survey_details['survey_initiator']!="" ? $survey_details['survey_initiator'] :$_SESSION['emp_id'];
                        foreach ($employee_list as $value) { ?>
                        <option value="<?php echo $value['id'];?>" <?php if($value['id'] == $survey_init){ ?>selected <?php } ?> ><?php echo $value['display_name']; ?>
                      </option>
                        <?php } ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label w-100">Asign to <span class="text-danger">*</span>
                      <a href="#" class="float-end f-w-600" onclick="showpopup();">Edit</a>
                    </label>
                    <div id="tsk_fortable"></div>
                    <div class="col-xl-12">
                      <i class="flaticon2-group text-success"></i>
                      
                    </div>
                  </div> 
                  <div class="form-group">
                    <?php //print_r($who_can_see_id); ?>
                    <label for="example-date-input" class="form-label">Who can see the results</label> 
                    <select class="form-select js-example-basic-multiple" id="kt_select2_43" name="who_can_see_the_results[]" multiple="multiple">
                      <?php foreach ($employee_list as $value) { 
                      $key=$value['id']; ?>
                      <option <?php if(count((array)$who_can_see_id) != 0 && in_array($key,$who_can_see_id)){ ?> selected <?php } ?> value="<?php echo $key;?>"><?php echo $value['display_name']; ?></option>
                      <?php } ?>
                    </select>
                  </div>  
                </div>
                <div class="card-footer d-flex row p-1">
                  <div class="col-lg-6">
                    <button type="submit" name="create" class="btn btn-primary font-weight-bold float-left"><?php echo $_GET['Rid']!='' ? 'Update' : 'Save'; ?></button>                       
                  </div>

                  <div class="col-lg-6 text-right">
                    <!-- Example single danger button-->
                    <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add Questions</button>
                      <div class="dropdown-menu">
                        <?php  foreach ($qustion_types as $key => $value) {
                        //$question_types[$value['genc_detail_code']]  = $value['genc_detail_code']; ?>
                        <a href="#" onclick="dynamiceditpopup('<?php echo $value['genc_detail_code']; ?>','<?php echo $value['genc_detail_name']; ?>')" class="dropdown-item">  <?php echo $value['genc_detail_name']; ?>
                        </a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3"></div>
              <div class="col-lg-3"></div>
            </div>
          </div> 
          <div class="col-lg-6 px-4" id="survey_questions">
            <div class="card card-custom" style="min-height:631px;">
              <?php if($_GET['Rid']!='') { 
                $value_id = 0;
                $totalquestion = count((array)$_SESSION['questions_list']);
                foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
                $value_id ++;
                $value = json_decode($qvalue,1);
                $allow_multiple_selections_status = $value['allow_multiple_selections']=='0'?'checked' :'';
                $require_response_status = $value['require_response']=='0'?'checked' :'';
                $options = $value['option_data'];
                $question_id = $value['question_id'];
                $rid = $value['rid'];
                $totaloption = count((array)$options);
                $title = $value['title'];
                $display_question .= ' <div class="mt-5" id="'.$question_id.'"> <div class="col-lg-12 table-bordered">
                <input type="hidden" name="totalquestion" value="'.$totalquestion.'">
                <input type="hidden" name="question_id'.$qkey.'" value="'.$question_id.'">
                <input type="hidden" name="rid" value="'.$rid.'">
                <input type="hidden" name="value_id[]" value="'.$question_id.'">
                <input type="hidden" name="options_display_type'.$qkey.'" value="'.$value['type'].'">

                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
                <i aria-hidden="true" class="fa fa-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$question_id."'".')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Question"></i>
                </div>
                <div class="card-body mt-3">
                <div class="form-group text-left">
                <label>Question Text: <span class="text-danger"></span></label>
                <textarea id="survey_question'.$qkey.'" name="survey_question'.$qkey.'" class="form-control border" placeholder=""> '.$value['question'].' </textarea>
                </div>
                <div class="form-group text-left">
                <div class="checkbox-inline">';
                if($value['type']==1 || $value['type']==3)   { 
                  $display_question .='
                  <label class="checkbox checkbox-primary">
                    <input class="border" type="checkbox" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections'.$qkey.'" '.$allow_multiple_selections_status.'/>
                    <span></span>
                    Allow multiple selections
                  </label>';
                }
                else{
                 $display_question .='<input type="hidden" name="allow_multiple_selections'.$qkey.'" id="allow_multiple_selections'.$qkey.'" value="1"/>';
                }
                $display_question .='
                <label class="checkbox checkbox-primary">
                  <input class="border" type="checkbox" name="require_response'.$qkey.'" id="require_response'.$qkey.'" '.$require_response_status.'>
                  <span></span>
                  Require a Response
                </label>
                </div>
                </div>' ;
                if($value['type']==1 || $value['type']==3)   { 
                  $display_question .= '<div id="options'.$qkey.'">';
                  foreach ($options as $opkey => $opvalue) { 
                  $display_question .= ' <div id="optionlist'.$qkey.$opkey.'">
                  <div class="form-group row" id="'.$opkey.'">
                  <div class="col-lg-1"></div>
                  <div class="col-lg-8">
                  <div class="input-group input-group-button"> 
                  <input type="text" class="form-control display-inline width1" name="survey_question_option'.$qkey.'[]"  placeholder="Option" value="'.$opvalue.'">
                  <span class="input-group-text addon-style">
                  <label class="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Option"><i class="fa fa-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$qkey."'".')"></i> </label>
                  </span>
                  </div>
                  </div>
                  </div>
                  </div>
                  ';

                }
                $display_question .= '</div>';
                $display_question .= '<div class="form-group">
                <input type="hidden" value="'.$opkey.'" id="option_row"/>
                <button class="btn btn-success" onclick="addnewoptionrows('."'".$qkey."'".')" id="addNewRows" type="button">+ Add More Options</button>
                </div>';
              }  
              if($value['type']==2)   { 
                $display_question .= ' <div class="form-group text-left mt-10">
                <label>Scale from 1 to: <span class="text-danger">*</span></label>
                <input type="number" name="survey_question_option'.$qkey.'[]" class="form-control border" placeholder="'.$options[0].'" value"'.$options[0].'" > 
                </div>';
              }  
              if($value['type']==4)   { 
                $display_question .= ' <input type="hidden" name="survey_question_option'.$qkey.'[]" class="form-control border" value=""> ';
              } 

              $display_question .= '</div> </div>  </div>';

              }
                echo $display_question;
              } ?> 
            </div>
          </div>
        </div>
      </form>
      <?php if(isset($row_id) && $row_id != '') { ?>
      <div class="row mt-4">
        <?php
        echo todisplaythree(array(
        'table' => TBL_SURVEY_SETUP,
        'originator' => $survey_details['created_by'],
        'encode' => 'no',
        'id' => $row_id,
        'dis_query' => 'yes',
        'dis_timeline' => 'yes',
        'dis_comments' => 'no',
        'dis_track' => 'no'
        ));
        ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <div id="kt_modal_KTDatatable_setup" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="popup2">
      </div>
    </div>
  </div>
</div>
