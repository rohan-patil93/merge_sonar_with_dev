<?php
$_SESSION['apply_js']['wizardjs'] = '1';
$_SESSION['apply_js']['multiselest'] = "1";
if($_POST['rid']!=""){
  $employee_id=$_POST['rid'];
}elseif($_GET['Rid']!=""){
  $idvalue = explode(':',encodeId($_GET['Rid']));
  $employee_id = $idvalue[1];
}else{
  $employee_id= $_SESSION['emp_id'];
}
if(isset($_POST) && !empty($_POST)){
    $_POST['emp_id'] = $employee_id;
    $_POST['is_active'] = 0;
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['updated_date'] = date("Y-m-d H:i:s");
    $_POST['company_id'] = $_SESSION['company_id'];
    include('submit_hr.php');
/**
  if($_POST['submit_type']=="hr") {
    $_POST['emp_id'] = $employee_id;
    $_POST['is_active'] = 0;
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['updated_date'] = date("Y-m-d H:i:s");
    $_POST['company_id'] = $_SESSION['company_id'];
    include('submit_hr.php');
  }else{
    foreach ($_POST['company_name'] as $key=> $company_name) {
     $ex['expe_rowid'] = $_POST['expe_rowid'][$key];
     $ex['company_name'] = $company_name;
     $ex['job_descrption'] = $_POST['job_descrption'][$key];
     $ex['work_from'] = $_POST['work_from'][$key];
     $ex['work_to'] = $_POST['work_to'][$key];
     $exp[] = $ex;
   }
   $_POST['experience_details'] = $exp;
   $ex =array();
   foreach ($_POST['institute_name'] as $key=> $institute_name) {
    if($institute_name !=""){  
      $ex['institute_rowid'] = $_POST['institute_rowid'][$key];
      $ex['institute_name'] = $institute_name;
      $ex['education_type'] = $_POST['education_type'][$key];
      $ex['start_date'] = $_POST['start_date'][$key];
      $ex['end_date'] = $_POST['end_date'][$key];
      $ex['score'] = $_POST['score'][$key];
      $edu[] = $ex;
    }
  }
  $_POST['education_details'] = $edu;
  $ex =array();
  foreach ($_POST['dependent_name'] as $key=> $dependent_name) {
    if($dependent_name !=""){ 
      $ex['dependent_rowid'] = $_POST['dependent_rowid'][$key];
      $ex['dependent_name'] = $dependent_name;
      $ex['dependent_relationship_type'] = $_POST['dependent_relationship_type'][$key];
      $ex['dependent_relationship'] = $_POST['dependent_relationship'][$key];
      $ex['dependent_date_of_birth'] = $_POST['dependent_date_of_birth'][$key];
      $dep[] = $ex;
    }
  }
  $_POST['dependent_details'] = $dep;
  $ex =array();
  foreach ($_POST['contact_name'] as $key=> $contact_name) {
    if($contact_name !=""){
      $ex['contact_rowid'] = $_POST['contact_rowid'][$key];
      $ex['contact_name'] = $contact_name;
      $ex['contact_seqno'] = $_POST['contact_seqno'][$key];
      $ex['contact_relationship'] = $_POST['contact_relationship'][$key];
      $ex['contact_home_no'] = $_POST['contact_home_no'][$key];
      $ex['contact_mobile_no'] = $_POST['contact_mobile_no'][$key];
      $ex['contact_office_no'] = $_POST['contact_office_no'][$key];
      $eme[] = $ex;
    }
  }
  $_POST['emergency_details'] = $eme;
	  $hr_id = $_POST['hr_id']; // hr notification send
    $unset =  array('hr_id','company_name','job_descrption','work_from','work_to','institute_name','institute_name','education_type','start_date','end_date','score','dependent_name','dependent_relationship_type','dependent_relationship','dependent_date_of_birth','contact_name','contact_seqno','contact_relationship','contact_home_no','contact_mobile_no','contact_office_no','contact_rowid','dependent_rowid','institute_rowid','expe_rowid');
    foreach ($unset as  $set) {
      unset($_POST[$set]);
    }
    $postdata['content'] = json_encode($_POST,1);
    $postdata['is_active'] = 0;
    $postdata['application_tablename'] = "employees";
    $postdata['company_id'] =  $_SESSION['company_id'];
    $postdata['table_id'] = $employee_id;
    $temp_id = $_POST['tmp_id'];
    if($temp_id!=""){
      $postdata['updated_date'] = nowDate();
      $postdata['updated_by'] = $_SESSION['userid'];
      $url = API."?mode=updaterowtabledata&rid=".$temp_id."&table=application_temp&method=post";
      $response = file_post_contents($url, $postdata, 'update');
    }else{ 
      $postdata['created_date'] = nowDate();
      $postdata['created_by'] = $_SESSION['userid'];
      $url = API."?mode=adddata&table=application_temp&method=post";
      $response = file_post_contents($url, $postdata, 'insert');
    }
    
   //  $aupostdata['confirmed_basic_ino'] = '0';
   //  $aupostdata['updated_date'] = nowDate();
   //  $aupostdata['updated_by'] = $_SESSION['userid'];
   //  $url11 = API."?mode=updaterowtabledata&rid=".$_POST['emp_user_id']."&table=application_users&method=post";
   // //print_r($aupostdata);
   //  file_post_contents($url11, $aupostdata, 'update');


      $respons= json_decode($response,1);
      if($respons['status'] == 'sucess'){
       $crfeatebynotdataa = array('company_id'=>$data['company_id'], 'notification_message'=>'Employee Details Submit For '.$_POST["display_name"].'('.$_POST["emp_code"].')', 'id'=>$employee_id, 'application_table_name'=>'employees', 'type'=>'ACTION', 'notification_users'=>array($hr_id),'option'=>'COM_INSERT_ONBOARDING_EMPLOYEES');
       $url = API."?mode=tosendnotifiations&method=post";
  			//$response44 = file_post_contents($url, $crfeatebynotdataa, 'insert');
       $crfeatebynotdataa = array('company_id'=>$data['company_id'], 'notification_message'=>'Employee Details Submit To Approve','id'=>$employee_id, 'application_table_name'=>'employees', 'type'=>'INFORMATION', 'notification_users'=>array($_POST['emp_user_id']),'option'=>'COM_INSERT_ONBOARDING_EMPLOYEES');
       $url = API."?mode=tosendnotifiations&method=post";
     }	 
 } **/
 if($_GET['Rid']!=""){ $crid= "&Rid=".$_GET['Rid']; }
		//header("location:index.php?option=".$_GET['option'].$crid);
}
   //echo $employee_id;
$company_id = $_SESSION['company_id'];
$emp_role_id = $_SESSION['role_id'];
$role_ids = json_decode($emp_role_id);
$emp_role_id = implode(',', (array)$role_ids);
$emp_loction_id = $_SESSION['employee']['location_id'];
$table_name = TBL_EMPLOYEES;
$url = API."?mode=getemployeeinfoforupdation&company_id=".$company_id."&employee_id=".$employee_id."&location_id=".$emp_loction_id."&role_id=".$_SESSION['working_role_short_code']."&is_active=0&application_tablename=".$table_name;
$response = callAPI($url);
$employee_basic_details = json_decode($response,1);
$hrconnect_id  = $employee_basic_details['hr_connect']; 
$fieldset = $employee_basic_details['fieldset'];
$field_set = json_decode($fieldset,1);
$tempdata = $employee_basic_details['tempdata'];
$temp_id = $tempdata['id']; 
$tempdata = json_decode($tempdata['content'],1); 
$dept_details = $employee_basic_details['dept_details'];
$designations_details = $employee_basic_details['designations_details'];
$application_users_details = $employee_basic_details['application_users_details'];
$employment_roles_details = $employee_basic_details['employment_roles_details'];
$locations_details = $employee_basic_details['locations_details'];
$emplevels_details = $employee_basic_details['emplevels_details'];
$employment_bands_details = $employee_basic_details['employment_bands_details'];
$verticals_details = $employee_basic_details['verticals_details'];
$blood_groups_details = $employee_basic_details['blood_groups_details'];
$marital_status_details = $employee_basic_details['marital_status_details'];
$gender_details = $employee_basic_details['gender_details'];
$emptypes_details = $employee_basic_details['emptypes_details'];
$edutypedata = $employee_basic_details['edutypedata'];
$relatypedata = $employee_basic_details['relatypedata'];
$buddy_list = $employee_basic_details['buddy_list'];
$user_role_list = $employee_basic_details['application_roles'];
$module_setup =  $employee_basic_details['module_setup'];
foreach($module_setup as $msetup){
  $module[$msetup['module_id']] = $msetup['display_name'];
}
$application_roles =  $employee_basic_details['application_roles'];
$role_idd =array(SUPERADMIN_ROLE,ADMIN_ROLE);
foreach($application_roles as $rolesre){
  if(!in_array($rolesre['role_short_code'], $role_idd)){
    $roles[$rolesre['application_module']][] = $rolesre;
  }
}

/* Employee Details Start */
$url = API."?mode=employeedetails&emp_id=".$employee_id."&company_id=".$company_id; 
$response = callAPI($url);
$maindata = json_decode($response,1);
$empdata  = $maindata['emp_data'];
$location_id = $empdata['location_id'];
if(!isset($tempdata['emp_code'])) {
  $data = $maindata['emp_data']; // Employees table
}else{
  $data = $tempdata; //Application_temp table
}
if(!isset($tempdata['emp_code'])){
  $edu_details = $employee_basic_details['edu_details'];
  $dep_details = $employee_basic_details['dep_details'];
  $emerge_details = $employee_basic_details['emerge_details'];
  $exp_details = $employee_basic_details['exp_details'];
}else{
  $edu_details =  $tempdata['education_details'];
  $exp_details =  $tempdata['experience_details'];
  $dep_details =  $tempdata['dependent_details'];
  $emerge_details =  $tempdata['emergency_details'];
  $department_details = $tempdata['department_id'];
}
$admin_buddy_list = array();
$hr_buddy_list = array();
$finance_buddy_list = array();
$project_buddy_list = array();
$location_buddy_list = array();
foreach($buddy_list as $buddy_res){
  $admin_buddy_list[] = $buddy_res;
  $hr_buddy_list[] = $buddy_res;
  $finance_buddy_list[] = $buddy_res;
  $project_buddy_list[] = $buddy_res;
  $location_buddy_list[] = $buddy_res;
}
$vendor_display_fields = $maindata['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'],1);
//echo "<pre>"; print_r($vafield);echo "</pre>";
?>
<script type="text/javascript">
 function submit_role(emp_id){
  $("#popupshowerror").hide();
  var role_id = $("#user_application_role").val();
  if(role_id !=""){
    jQuery.ajax({
     url: "ajax.php",
     data:'page=createuser&empid='+emp_id+"&user_application_role="+role_id,
     type: "POST",
     success:function(data){
      $("#modal_role").modal("hide");
      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9036']; ?>','');
      location.reload();	 
    }
  });
  }else{
    $("#popupshowerror").show();
  }
}
function createuser(emp_id){
  $("#modal_role").modal("show");
}
function submitempyeeform(){
  $("#verticle-wizard").submit();
}
function showmarriagedate(value) {
 if(value=='2') {
   document.getElementById('marriagedateshow').style.display ='block';
   document.getElementById('marriagedateshow1').style.display ='block';
 }else{
   document.getElementById('marriagedateshow').style.display ='none';
   document.getElementById('marriagedateshow1').style.display ='none';
 }
}
function statuspopup(status,id){
 var table_name = document.getElementById('table_name').value;
 jQuery.ajax({
   url: "views/custom_insert_status_popup.php",
   data:'table='+table_name+'&status='+status+'&rid='+id,
   type: "POST",
   success:function(data){
     $('#popup1').html(data);
     $('#kt_modal_KTDatatable_remote').modal({ show: true});
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
     $("#dropdownlist").html(data);
   },
   error:function (){}
 });
}
function removeexprows(){
 $(".itemRow:checked").each(function() {
   console.log(this);
   $(this).closest('tr').remove();
 });
 $('#checkAll').prop('checked', false);
}
function removeedurows(){
 $(".itemRow:checked").each(function() {
   console.log(this);
   $(this).closest('tr').remove();
 });
 $('#checkAll').prop('checked', false);
}
function removedeprows(){
 $(".itemRow:checked").each(function() {
   console.log(this);
   $(this).closest('tr').remove();
 });
 $('#checkAll').prop('checked', false);
}
function removeemerows(){
 $(".itemRow:checked").each(function() {
   console.log(this);
   $(this).closest('tr').remove();
 });
 $('#checkAll').prop('checked', false);
}
function adddeprows(){
 jQuery(document).ready(function() {    
   KTBootstrapDatepicker.init();
 });
 var htmlRows = '';
 htmlRows += '<tr>';
 htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

 htmlRows += '<td><input type="text" name="dependent_name[]"  class="form-control " autocomplete="off">  </td>'; 
 htmlRows += '<td class="w-full"><select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option> <?php   } ?></select>  </td>'; 
 htmlRows += '<td><input type="text" name="dependent_date_of_birth[]"  class="form-control datepicker"   autocomplete="off">  </td>';   
 htmlRows += '</tr>';
 $('#deptable').append(htmlRows);
 $(".js-example-basic-single").select2();
 $(".js-example-disabled-results").select2();
 $( ".datepicker" ).datepicker({
  showOn: "both", 
  buttonText: "<i class='ti-calendar'></i>",
  dateFormat: date_format,
  changeMonth: true,
  changeYear: true
});
}
function addemrgerows(){
 jQuery(document).ready(function() {    
   KTBootstrapDatepicker.init();
 });
 var htmlRows = '';
 htmlRows += '<tr>'; 
 htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
 htmlRows += '<td><input type="text" name="contact_name[]"  class="form-control " autocomplete="off"> </td>'; 
 htmlRows += '<td class="w-full"><select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option> <?php   } ?></select>    </td>'; 
 htmlRows += '<td><input type="text" name="contact_home_no[]"  class="form-control "  autocomplete="off">  </td>';   
 htmlRows += '<td><input type="text" name="contact_mobile_no[]"  class="form-control "  autocomplete="off">  </td>'; 
 htmlRows += '</tr>';
 $('#emergetable').append(htmlRows);
 $(".js-example-basic-single").select2();
 $(".js-example-disabled-results").select2();
 $( ".datepicker" ).datepicker({
  showOn: "both", 
  buttonText: "<i class='ti-calendar'></i>",
  dateFormat: date_format,
  changeMonth: true,
  changeYear: true
});
}
function addexpreencerows(){
 jQuery(document).ready(function() {    
   KTBootstrapDatepicker.init();
 });
 var htmlRows = '';
 htmlRows += '<tr>'; 
 htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none">  <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

 htmlRows += '<td><input type="text" name="company_name[]"  class="form-control " autocomplete="off">  </td>'; 
 htmlRows += '<td><input type="text" name="job_descrption[]"  class="form-control " autocomplete="off">  </td>'; 
 htmlRows += '<td><input type="text" name="work_from[]"  class="form-control datepicker"   autocomplete="off">  </br>'; 
 htmlRows += '<input type="txet" name="work_to[]"  class="form-control datepicker"  autocomplete="off">  </td>';   
 htmlRows += '</tr>';
 $('#extable').append(htmlRows);
 $(".js-example-basic-single").select2();
 $(".js-example-disabled-results").select2();
 $( ".datepicker" ).datepicker({
  showOn: "both", 
  buttonText: "<i class='ti-calendar'></i>",
  dateFormat: date_format,
  changeMonth: true,
  changeYear: true
});
}
function addedurows(){
 jQuery(document).ready(function() {    
   KTBootstrapDatepicker.init();
 });
 var htmlRows = '';
 htmlRows += '<tr>'; 
 htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

 htmlRows += '<td><input type="text" name="institute_name[]"  class="form-control " autocomplete="off">  </td>'; 
 htmlRows += '<td class="w-full"><select class="form-control form-select js-example-basic-single" name="education_type" > <option value="0"> Select Any One</option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option> <?php   } ?></select> </td>'; 
 htmlRows += '<td><input type="text" name="start_date[]"  class="form-control datepicker"  autocomplete="off">  </br>'; 
 htmlRows += '<input type="txet" name="end_date[]"  class="form-control datepicker"   autocomplete="off">  </td>'; 
 htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';  
 htmlRows += '</tr>';
 $('#edutable').append(htmlRows);
 $(".js-example-basic-single").select2();
 $(".js-example-disabled-results").select2();
 $( ".datepicker" ).datepicker({
  showOn: "both", 
  buttonText: "<i class='ti-calendar'></i>",
  dateFormat: date_format,
  changeMonth: true,
  changeYear: true
});
}
function getBandid(id){
  $.ajax({
   url: "ajax.php",
   data:'page=getemployeementlevel&id='+id,
   type: "POST",
   success:function(data){
     console.log(data);
     $("#employeement_level").html(data);
   },
   error:function (){}
 });
}
</script>
<style>
  .content.clearfix {
    background-color: white;
    overflow-y: scroll;
    height: 650px;
  }
  .text-muted{
    display: none;
  }
  fieldsetnone{
    display: none;
  }
  .wizard > .content > .body label.error {
    margin-left: 0px !important;
  }

  </style>
  <div class="d-flex flex-column-fluid ">
   <div class="container-fluid">
    <?php 
    if($hrconnect_id==0 || $hrconnect_id=="") { ?>
     <div class="alert alert-primary alert-dismissible">
      <strong><?php echo "HR connect is not configured.  <strong>Workflow need to be started!</strong>"; ?></strong> 
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
  <?php } else { ?>
   <div class="alert alert-primary alert-dismissible">
    <strong><?php echo "This function is used for maintaining <strong>Employee Details</strong>. * represents mandatory fields."; ?></strong> 
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
<?php } ?>
<div class="card  j-pro">
  <div class="card-header  ">
   <h5>Employee</h5>
   <div class="card-header-right table-card-header ">
    <?php if($maindata['emp_data']['user_id']=="" AND in_array($_SESSION['working_role_short_code'],array(PAYROLL_ADMINISTRATOR_ROLE,HR_ADMIN_ROLE,ADMIN_ROLE))){ ?>
      <button type="" class="btn btn-warning font-weight-bolder text-uppercase" onclick="createuser(<?php echo $maindata['emp_data']['id']; ?>)" >Create User</button>  
    <?php } ?>
    <button type="" class="btn btn-primary" onclick="submitempyeeform()"> Submit</button>
  </div>
</div>
<div class="card-block ">
 <div class="row j-content">
   <div class="col-md-12">
     <div id="wizardb">
       <section>
        <div class="card">
          <div class="card-block">
            <div class="row">
              <div class="col-md-12">
                <div id="wizardb">
                  <section>
                   <form class="wizard-form" id="verticle-wizard"  enctype="multipart/form-data" method="post">
                    <?php if($_SESSION['userid']==$hrconnect_id) { ?>
                      <input type="hidden" name="submit_type" value="hr">
                    <?php } ?>
                    <input type="hidden" name="hr_id" value="<?php echo $hrconnect_id; ?>">
                    <input type="hidden" name="emp_user_id" value="<?php echo $maindata['emp_data']['user_id']; ?>">                                                    
                    <h3> Basic Info </br><small>Please enter basic information</small> </h3>
                    <fieldset>
                     <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                       <h4 class="mb-5 font-weight-bold text-dark">Basic Information</h4>
                       <?php 
                       $fieldaccesright = $vafield['basic_information']; 
                       if($fieldaccesright['field']['website_url']!="NA"){ 
                        $read_only_ec = $fieldaccesright['field']['emp_code'] !="E"?"readonly":"";
                      }else{
                        $display_div_ec = 'none';
                      }
                      $text_color_ec= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                      ?>
                      <div class="row">
                        <div class="col-xl-6" style="display: <?php echo $display_div_ec; ?>" >
                         <div class="mt-2" style="display: <?php echo $display_div_ec; ?>">
                          <label class="col-form-label mb-0">Employee Code <span class="text-danger">*</span>
                            <?php if($text_color_ec!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['emp_code']==''?NODATA:$empdata['emp_code'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
                          </label>
                          <div class="input-group input-icon-right mb-0">
                           <input <?php echo $read_only_ec; ?> class="form-control form-control-lg   <?php echo $text_color_ec; ?>" type="text" placeholder="Enter employee Code" name="emp_code" value="<?php echo $data['emp_code']; ?>" />
                         </div>
                       </div>
                     </div>
                     <?php  
                     if($fieldaccesright['field']['display_name']!="NA"){ 
                      $read_only_uc = $fieldaccesright['field']['display_name'] !="E"?"readonly":"";
                    }else{
                      $display_div_uc = 'none';
                    }
                    $text_color_uc= $data['display_name'] != $empdata['display_name'] ? 'text-danger':"";
                    ?>
                    <div class="col-xl-6" style="display: <?php echo $display_div_uc; ?>" >
                     <div class="mt-2" style="display: <?php echo $display_div_uc; ?>" >
                      <label class="col-form-label mb-0">Username <span class="text-danger">*</span>     <?php if($text_color_uc!="")  { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['display_name']==''?NODATA:$empdata['display_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
                      <div class="input-group input-icon-right mb-0">
                       <input <?php echo $read_only_uc; ?> class="form-control form-control-lg  <?php echo $text_color_uc; ?>" type="text" placeholder="Username:" name="display_name" value="<?php echo $data['display_name']; ?>" />
                     </div>
                   </div>
                 </div>   
                 <?php 
                 if($fieldaccesright['field']['first_name']!="NA"){ 
                  $read_only_fn = $fieldaccesright['field']['first_name'] !="E"?"readonly":"";
                }else{
                  $display_div_fn = 'none';
                }
                if($fieldaccesright['field']['last_name']!="NA"){ 
                  $read_only_ln = $fieldaccesright['field']['last_name'] !="E"?"readonly":"";
                }else{
                  $display_div_ln = 'none';
                }
                if($fieldaccesright['field']['middle_name']!="NA"){ 
                  $read_only_mn = $fieldaccesright['field']['middle_name'] !="E"?"readonly":"";
                }else{
                  $display_div_mn = 'none';
                }
                $text_color_fn= $data['first_name'] != $empdata['first_name'] ? 'text-danger':"";
                $text_color_ln= $data['last_name'] != $empdata['last_name'] ? 'text-danger':"";
                $text_color_mn= $data['middle_name'] != $empdata['middle_name'] ? 'text-danger':"";
                ?>
                <div class="col-xl-4" style="display: <?php echo $display_div_fn; ?>">
                 <div class="mt-2" style="display: <?php echo $display_div_fn; ?>" >
                  <label class="col-form-label mb-0">First Name <span class="text-danger">*</span> <?php if($text_color_fn!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['first_name']==''?NODATA:$empdata['first_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
                  <div class="input-group input-icon-right mb-0">
                   <input <?php echo $read_only_fn; ?> class="form-control form-control-lg  <?php echo $text_color_fn; ?>" type="text" placeholder="Enter first Name" name="first_name" value="<?php echo $data['first_name']; ?>"/>
                 </div>
               </div>
             </div>
             <div class="col-xl-4" style="display: <?php echo $display_div_mn; ?>">
               <div class="mt-2" style="display: <?php echo $display_div_mn; ?>" >
                <label class="col-form-label mb-0">Middle Name <?php if($text_color_mn!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['middle_name']==''?NODATA:$empdata['middle_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
                <div class="input-group input-icon-right mb-0">
                 <input <?php echo $read_only_mn; ?> class="form-control form-control-lg  <?php echo $text_color_mn; ?>" type="text" placeholder="Enter middle Name" name="middle_name" value="<?php echo $data['middle_name']; ?>"/>
               </div>
             </div>
           </div>
           <div class="col-xl-4" style="display: <?php echo $display_div_ln; ?>">
             <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
              <label class="col-form-label mb-0">Last Name <span class="text-danger">*</span>
                <?php if($text_color_ln!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['last_name']==''?NODATA:$empdata['last_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
                <div class="input-group input-icon-right mb-0">
                 <input <?php echo $read_only_ln; ?> class="form-control form-control-lg  <?php echo $text_color_ln; ?>" type="text" placeholder="Enter last Name" name="last_name" value="<?php echo $data['last_name']; ?>"/>
               </div>
             </div>
           </div>
           <div class="col-xl-6" style="display: <?php echo $display_div_email; ?>" >
             <?php 
             if($fieldaccesright['field']['email_id']!="NA"){ 
              $read_only_email = $fieldaccesright['field']['email_id'] !="E"?"readonly":"";
            }else{
              $display_div_email = 'none';
            }
            $text_color_email= $data['email_id'] != $empdata['email_id'] ? 'text-danger':"";
            ?>
            <div class="mt-2" style="display: <?php echo $display_div_email; ?>" >
              <label class="col-form-label mb-0">Email <span class="text-danger">*</span> <?php if($text_color_email!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['email_id']==''?NODATA:$empdata['email_id'] ;;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
              <div class="input-group input-icon-right mb-0">
               <input <?php echo $read_only_email; ?> class="form-control form-control-lg  <?php echo $text_color_email; ?>" type="text" placeholder="Enter email" name="email_id" value="<?php echo $data['email_id']; ?>" />
             </div>
           </div>
         </div>

         <?php 
         if($fieldaccesright['field']['phone']!="NA"){ 
          $read_only_ph = $fieldaccesright['field']['phone'] !="E"?"readonly":"";
        }else{
          $display_div_ph = 'none';
        }
        $text_color_ph= $data['phone'] != $empdata['phone'] ? 'text-danger':"";
        ?>
        <div class="col-xl-6" style="display: <?php echo $display_div_ph; ?>" >
         <div class="mt-2" style="display: <?php echo $display_div_ph; ?>" >
          <label class="col-form-label mb-0">Contact <?php if($text_color_ph!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['phone']==''?NODATA:$empdata['phone'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
          <div class="input-group input-icon-right mb-0">
           <input <?php echo $read_only_ph; ?> class="form-control form-control-lg  <?php echo $text_color_ph; ?>" type="text" placeholder="Enter first Name" name="phone" value="<?php echo $data['phone']; ?>"/>
         </div>
       </div>
     </div>

     <?php 
     if($fieldaccesright['field']['address']!="NA"){ 
      $read_only_adr = $fieldaccesright['field']['address'] !="E"?"readonly":"";
    }else{
      $display_div_adr = 'none';
    }
    $text_color_adr= $data['address'] != $empdata['address'] ? 'text-danger':"";
    ?>
    <div class="col-xl-6" style="display: <?php echo $display_div_adr; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_adr; ?>" >
      <label>Permanent Address<?php if($text_color_adr!="") { ?> 
        <span  data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['address']==''?NODATA:$empdata['address'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span > <?php } ?></label>
        <textarea <?php echo $read_only_adr; ?> style="overflow:auto" class="form-control form-control-lg  <?php echo $text_color_adr; ?>" placeholder="Enter your address" name="address"><?php echo $data['address'];?></textarea>
      </div>
      <?php
      if($fieldaccesright['field']['post_code']!="NA"){ 
        $read_only_pc = $fieldaccesright['field']['post_code'] !="E"?"readonly":"";
      }else{
        $display_div_pc = 'none';
      } 
      $text_color_pc= $data['post_code'] != $empdata['post_code'] ? 'text-danger':"";
      ?>
    </div>
    <div class="col-xl-6" style="display: <?php echo $display_div_pc; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_pc; ?>" >
      <label>Post code 
        <?php if($text_color_pc!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['post_code']==''?NODATA:$empdata['post_code'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
      </label>
      <div class="input-group">

       <input <?php echo $read_only_pc; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_pc; ?> " placeholder="Enter your postcode" name="post_code" value="<?php echo $data['post_code']; ?>"/>
     </div>
   </div>
   </div>
   <?php
   if($fieldaccesright['field']['contact_address']!="NA"){ 
      $read_only_conadr = $fieldaccesright['field']['contact_address'] !="E"?"readonly":"";
    }else{
      $display_div_conadr = 'none';
    }
    $text_color_conadr= $data['contact_address'] != $empdata['contact_address'] ? 'text-danger':"";
    ?>
    <div class="col-xl-6" style="display: <?php echo $display_div_conadr; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_conadr; ?>" >
      <label>Current Address<?php if($text_color_conadr!="") { ?> 
        <span  data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['contact_address']==''?NODATA:$empdata['contact_address'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span > <?php } ?></label>
        <textarea <?php echo $read_only_conadr; ?> style="overflow:auto" class="form-control form-control-lg  <?php echo $text_color_conadr; ?>" placeholder="Enter your address" name="contact_address"><?php echo $data['contact_address'];?></textarea>
      </div>
    </div>
     <?php
     if($fieldaccesright['field']['fax']!="NA"){ 
      $read_only_fx = $fieldaccesright['field']['fax'] !="E"?"readonly":"";
    }else{
      $display_div_fx = 'none';
    } 
    $text_color_fx= $data['fax'] != $empdata['fax'] ? 'text-danger':"";
    ?>                             
      <div class="col-xl-6" style="display: <?php echo $display_div_fx; ?>">
       <div class="mt-2" style="display: <?php echo $display_div_fx; ?>" >
        <label class="col-form-label mb-0">Fax <?php if($text_color_fx!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['fax']==''?NODATA:$empdata['fax'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
        <div class="input-group input-icon-right mb-0">
           <input <?php echo $read_only_fx; ?> class="form-control form-control-lg  <?php echo $text_color_fx; ?>" type="text" placeholder="Enter middle Name" name="fax" value="<?php echo $data['fax']; ?>"/>
          </div>
        </div>
      </div>
</div>



</div>
</fieldset>
<?php $fieldaccesright = $vafield['work'];
$display_work = $divclassname = "";
if($fieldaccesright['access'] =="NA"){ 
   $display_work = "style='display:none'";
   $divclassname="none";
}else{
  ?><h3> Work</br><small>Please enter your work details</small> </h3> <?php
}
?>
<fieldset<?php echo $divclassname; ?> >
<div class="pb-5" data-wizard-type="step-content" >
<h4 class="mb-5 font-weight-bold text-dark" <?php echo $display_work; ?>>Work Details</h4> 
    <?php 
   
   if($fieldaccesright['field']['department_id']!="NA"){ 
    $read_only = $fieldaccesright['field']['department_id'] !="E"?"custom_readonly":"";
  }else{
    $display_div_dep = 'none';
  }
  $text_color_dep= $data['department_id'] != $empdata['department_id'] ? 'text-danger':"";
  if($empdata['department_id']==0){
    $text_color_dep = "";
  }
  if($fieldaccesright['field']['vertical_id']!="NA"){ 
    $read_only_ver = $fieldaccesright['field']['vertical_id'] !="E"?"custom_readonly":"";
  }else{
    $display_div_ver = 'none';
  }
  $text_color_ver= $data['vertical_id'] != $empdata['vertical_id'] ? 'text-danger':"";
  if($empdata['vertical_id']==0){
    $text_color_ver = "";
  }
  ?>
  <div class="row">
    <div class="col-lg-6 " style="display: <?php echo $display_div_dep; ?>">
     <div  class="mt-2 w-full" style="display: <?php echo $display_div_dep; ?>" >
      <label class="col-form-label">Department
        <?php if($text_color_dep!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['department_name']==''?NODATA:$empdata['department_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
        <div class="<?php echo $read_only; ?>">
        <select class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
          <option value="">Select Any One</option>
          <?php foreach ($dept_details as $departments) {
            ?>
            <option <?php if($data['department_id'] == $departments['id']){  ?>selected <?php } ?> value="<?php echo $departments['id']; ?>" >
              <?php echo $departments['department_name']; 
              $emp_department[$departments['id']] = $departments['department_name'];
              ?>
            </option>
          <?php } ?> 
        </select>
      </div>
      </div>
    </div>
    <div class="col-lg-6 " style="display: <?php echo $display_div_ver; ?>">
     <div  class="mt-2 w-full" style="display: <?php echo $display_div_ver; ?>" >
      <label class="col-form-label">Vertical <?php if($text_color_ver!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['vertical_name']==''?NODATA:$empdata['vertical_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
      <div class="<?php echo $read_only_ver; ?>">
      <select class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
        <option value="">Select Any One</option>

        <?php foreach ($verticals_details as $verticals) {
          if($empdata['vertical_id'] == $verticals['id']) {
           $empdata['vertical_name'] = $verticals['vertical_name'];
         } 
         ?>
         <option <?php if($data['vertical_id'] == $verticals['id']){ ?>selected <?php } ?> value="<?php echo $verticals['id']; ?>" >
          <?php echo $verticals['vertical_name']; ?>
        </option>
      <?php } ?>  
    </select>
  </div>
  </div>
</div>
<?php
if($fieldaccesright['field']['reporting_manager_id']!="NA"){ 
  $read_only_rto = $fieldaccesright['field']['reporting_manager_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_rto = 'none';
} 
$text_color_rto= $data['reporting_manager_id'] != $empdata['reporting_manager_id'] ? 'text-danger':"";
if($empdata['reporting_manager_id']==0){
  $text_color_rto = "";
}
if($fieldaccesright['field']['hiring_source']!="NA"){ 
  $read_only_hs = $fieldaccesright['field']['hiring_source'] !="E"?"custom_readonly":"";
}else{
  $display_div_hs = 'none';
} 
$text_color_hs= $data['hiring_source'] != $empdata['hiring_source'] ? 'text-danger':"";
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_rto; ?>" >
 <div  class="mt-2 w-full" style="display: <?php echo $display_div_rto; ?>" >
  <label class="col-form-label">Repoting to <?php if($text_color_rto!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['display_name']==''?NODATA:$empdata['display_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="<?php echo $read_only_rto; ?>">
  <select <?php echo $field_set['REPOTING_TO']['readonly']!=0?'disabled="true"':'';?> class ="form-control form-control-lg  form-select js-example-basic-single" name="reporting_manager_id">
    <option value="">Select Any One</option>

    <?php foreach ($application_users_details as $application_users) {
      if($empdata['reporting_manager_id'] == $application_users['id']) {
       $empdata['display_name'] = $application_users['display_name'];
     } 

     ?>
     <option <?php if($data['reporting_manager_id'] == $application_users['id']){ ?>selected <?php } ?> value="<?php echo $application_users['id']; ?>" >
      <?php echo $application_users['display_name']; ?>
    </option>
  <?php } ?>  
</select>
</div>
</div>
</div>
<div class="col-lg-6 " style="display: <?php echo $display_div_hs; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_hs; ?>">
  <label class="col-form-label">
    Hiring Source 
    <?php if($text_color_hs!="") { ?> 
      <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['hiring_source']==''?NODATA:$empdata['hiring_source'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span>
    <?php } ?>
  </label>
  <div class="input-group input-icon-right mb-0 <?php echo $read_only_hs; ?>">
   <input  class="form-control form-control-lg  <?php echo $text_color_hs; ?>" type="text" placeholder="Hiring Source" name="hiring_source" value="<?php echo $data['hiring_source']; ?>"/>
 </div>
</div>
</div>
<?php 
if($fieldaccesright['field']['designation_id']!="NA"){ 
  $read_only_des = $fieldaccesright['field']['designation_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_des = 'none';
} 

$text_color_des= $data['designation_id'] != $empdata['designation_id'] ? 'text-danger':"";
if($empdata['designation_id']==0){
  $text_color_des = "";
}
if($fieldaccesright['field']['role_id']!="NA"){ 
  $read_only_role = $fieldaccesright['field']['role_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_role = 'none';
} 
$text_color_role= $data['role_id'] != $empdata['role_id'] ? 'text-danger':"";
if($empdata['role_id']==0){
  $text_color_role = "";
}
?>
<div class="col-lg-6 "  style="display: <?php echo $display_div_des; ?>">
 <div  class="mt-2 w-full" style="display: <?php echo $display_div_des; ?>" >
  <label class="col-form-label">
   Designation <?php if($text_color_des!="") { ?> 
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['designation_name']==''?NODATA:$empdata['designation_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span>
   <?php } ?>
 </label>
 <div class="<?php echo $read_only_des; ?>">
 <select class ="form-control form-control-lg form-select js-example-basic-single" name="designation_id">
  <option value="">Select Any One</option>
  <?php foreach ($designations_details as $desigs) {
    ?>
    <option <?php if($data['designation_id'] == $desigs['id']){ ?> selected <?php } ?> value="<?php echo $desigs['id']; ?>" >
      <?php echo $desigs['designation_name']; ?>
    </option>
  <?php } ?>  
</select>
</div>
</div>
</div>
<div class="col-lg-6 " style="display: <?php echo $display_div_role; ?>">
 <div  class="mt-2 w-full" style="display: <?php echo $display_div_role; ?>" >
  <label class="col-form-label">Role <?php if($text_color_role!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['role_name']==''?NODATA:$empdata['role_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="<?php echo $read_only_role; ?>">
  <select class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
    <option value="">Select Any One</option>
    <?php foreach ($employment_roles_details as $employment_roles) {
     ?>
     <option <?php if($data['role_id'] == $employment_roles['id']){ ?>selected  <?php } ?> value="<?php echo $employment_roles['id']; ?>" >
      <?php echo $employment_roles['role_name']; ?>
    </option>
  <?php } ?>  
</select>
</div>
</div>
</div>
<?php 
if($fieldaccesright['field']['joining_date']!="NA"){ 
  $read_only_dj = $fieldaccesright['field']['joining_date'] !="E"?"custom_readonly":"";
}else{
  $display_div_dj = 'none';
} 
$text_color_dj= fromDate($data['joining_date']) != fromDate($empdata['joining_date']) ? 'text-danger':"";
$text_color_eco= $data['experience_current_organization'] != $empdata['experience_current_organization'] ? 'text-danger':"";

$diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ; 
if($fieldaccesright['field']['experience_current_organization']!="NA"){ 
  $read_only_eco = $fieldaccesright['field']['experience_current_organization'] !="E"?"readonly":"";
}else{
  $display_div_eco = 'none';
} 
//$read_only_eco = $field_set['EXPERIENCE_IN_CURRENT_ORGANIZATION']['readonly'] != 0 ? 'readonly' : '';
//$display_div_eco = $field_set['EXPERIENCE_IN_CURRENT_ORGANIZATION']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_dj; ?>" >
 <div  class="mt-2" style="display: <?php echo $display_div_dj; ?>" >
  <label class="col-form-label">Date of Joining <?php if($text_color_dj!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['joining_date']==''?NODATA:$empdata['joining_date'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="<?php echo $read_only_dj; ?>">
   <?php $date_pic = fromDate($data['joining_date']); ?>
   <input type="text" class="form-control datepicker form-control-lg  <?php echo $text_color_dj; ?>"  value="<?php echo $date_pic; ?>"  name="joining_date"  id="kt_datepicker_99" />
 </div>
</div>
</div>
<?php
$diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ; 
?>
<div class="col-lg-6" style="display: <?php echo $display_div_eco; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_eco; ?>" >
  <label class="col-form-label">Experience in Current Organization
   <?php if($data['experience_current_organization']!=$empdata['experience_current_organization']) { ?>
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['experience_current_organization']==''?NODATA:$empdata['experience_current_organization'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0">
     <input <?php echo $read_only_eco; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="Experience in Current Organization" name="experience_current_organization" value="<?php echo floor($diff / (365*60*60*24)); ?>"/>

   </div>
 </div>
</div>
<?php
if($fieldaccesright['field']['total_experiance']!="NA"){ 
  $read_only_te = $fieldaccesright['field']['total_experiance'] !="E"?"custom_readonly":"";
}else{
  $display_div_te = 'none';
} 
$text_color_te= $data['total_experiance'] != $empdata['total_experiance'] ? 'text-danger':"";
if($fieldaccesright['field']['relevant_experience']!="NA"){ 
  $read_only_re = $fieldaccesright['field']['relevant_experience'] !="E"?"custom_readonly":"";
}else{
  $display_div_re = 'none';
}
$text_color_re= $data['relevant_experience'] != $empdata['relevant_experience'] ? 'text-danger':"";

if($fieldaccesright['field']['notice_period']!="NA"){ 
  $read_only_np = $fieldaccesright['field']['notice_period'] !="E"?"custom_readonly":"";
}else{
  $display_div_np = 'none';
}
$text_color_np= $data['notice_period'] != $empdata['notice_period'] ? 'text-danger':"";
?>
<div class="col-lg-6 "  style="display: <?php echo $display_div_te; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_te; ?>" >
  <label class="col-form-label">Total Experience Year <?php if($text_color_te!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['total_experiance']==''?NODATA:$empdata['total_experiance'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?> </label>
  <div class="input-group input-icon-right mb-0 <?php echo $read_only_te; ?>">
   <input   type="text" class="form-control form-control-lg  <?php echo $text_color_te; ?>"  placeholder="Total Experience"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
 </div>
</div>
</div>
<div class="col-lg-6 " style="display: <?php echo $display_div_re; ?>" >
 <div  class="mt-2" style="display: <?php echo $display_div_re; ?>" >
  <label class="col-form-label">Relevant Experience Year <?php if($text_color_re!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['relevant_experience']==''?NODATA:$empdata['relevant_experience'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?> </label>
  <div class="input-group input-icon-right mb-0 <?php echo $read_only_re; ?>">
   <input   type="text" class="form-control form-control-lg  <?php echo $text_color_re; ?>"  placeholder="Total Experience"  name="relevant_experience" value="<?php echo $data['relevant_experience']; ?>"/>
 </div>
</div>
</div>
<div class="col-lg-6 " style="display: <?php echo $display_div_np; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_np; ?>">
  <label class="col-form-label">Notice Period <?php if($text_color_np!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['notice_period']==''?NODATA:$empdata['notice_period'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="input-group input-group-button <?php echo $read_only_np; ?>">
   <input  type="text" class="form-control form-control-sm width1  <?php echo $text_color_np; ?>"  placeholder="Notice Period" name="notice_period" value="<?php echo $data['notice_period']; ?>" />
   <span class="input-group-text addon-style"><label class="">Days</label></span>
 </div>
</div>
</div>
<?php 
if($fieldaccesright['field']['band_id']!="NA"){ 
  $read_only_bd = $fieldaccesright['field']['band_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_bd = 'none';
}
$text_color_bd = $data['band_id'] != $empdata['band_id'] ? 'text-danger':"";
if($empdata['band_id']==0){
  $text_color_bd = "";
}
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_bd; ?>">
 <div  class="mt-2 w-full <?php echo $read_only_lv; ?>" style="display: <?php echo $display_div_bd; ?>">
  <label class="col-form-label">Band <?php if($text_color_bd!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['band_name']==''?NODATA:$empdata['band_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="<?php echo $read_only_bd; ?>">
  <select class ="form-control form-control-lg form-select js-example-basic-single" name="band_id" onchange="getBandid(this.value)">
    <option value="">Select Any One</option>
    <?php foreach ($employment_bands_details as $employment_bands) { 
      ?>
      <option <?php if($data['band_id'] == $employment_bands['id']){ ?>selected  <?php } ?> value="<?php echo $employment_bands['id']; ?>" >
        <?php echo $employment_bands['band_name']; ?>
      </option>
    <?php } ?>  
  </select>
</div>
</div>
</div>
<?php 
if($fieldaccesright['field']['level_id']!="NA"){ 
  $read_only_lv = $fieldaccesright['field']['level_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_lv = 'none';
}
$text_color_lv= $data['level_id'] != $empdata['level_id'] ? 'text-danger':"";
if($empdata['level_id']==0){
  $text_color_lv = "";
}
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_lv; ?>">
 <div  class="mt-2 w-full" style="display: <?php echo $display_div_lv; ?>" >
  <label class="col-form-label">Level 
    <?php if($text_color_lv!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['levels_name']==''?NODATA:$empdata['levels_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
    <div class="<?php echo $read_only_lv; ?>">
    <select class ="form-control form-control-lg  form-select js-example-basic-single" name="level_id" id="employeement_level">
      <option value="">Select Any One</option>
      <?php foreach ($emplevels_details as $employment_levels) { 
       if($empdata['level_id'] == $employment_levels['id']) {
         $empdata['levels_name'] = $employment_levels['level_name'];
       }                                          ?>
       <option <?php if($data['level_id'] == $employment_levels['id']){ ?> selected <?php } ?>  value="<?php echo $employment_levels['id']; ?>" >
        <?php echo $employment_levels['level_name']; ?>
      </option>
    <?php } ?>   
  </select>
</div>
</div>
</div>
<?php 
if($fieldaccesright['field']['location_id']!="NA"){ 
  $read_only_loc = $fieldaccesright['field']['location_id'] !="E"?"custom_readonly":"";
}else{
  $display_div_loc = 'none';
}
$text_color_loc= $data['location_id'] != $empdata['location_id'] ? 'text-danger':"";
if($empdata['location_id']==0){
  $text_color_loc = "";
}
if($fieldaccesright['field']['employee_type']!="NA"){ 
  $read_only_ety = $fieldaccesright['field']['employee_type'] !="E"?"custom_readonly":"";
}else{
  $display_div_ety = 'none';
}
$text_color_ety= $data['employee_type'] != $empdata['employee_type'] ? 'text-danger':"";
if($fieldaccesright['field']['billable']!="NA"){ 
  $read_only_ebi = $fieldaccesright['field']['billable'] !="E"?"custom_readonly":"";
}else{
  $display_div_ebi = 'none';
}
$text_color_ebi= $data['billable'] != $empdata['billable'] ? 'text-danger':"";
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_loc; ?>" >
 <div  class="mt-2 w-full" style="display: <?php echo $display_div_loc; ?>" >
  <label class="col-form-label">Work Location                       <?php if($text_color_loc!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['location_name']==''?NODATA:$empdata['location_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
</label>
<div class="<?php echo $read_only_loc; ?>">
<select <?php echo $field_set['WORK_LOCATION']['readonly']!=0?'disabled="true"':'';?> class ="form-control form-control-lg form-select js-example-basic-single"  name="location_id">
  <option value="">Select Any One</option>
  <?php foreach ($locations_details as $locations) { 
    if($locations['parent_location_id'] =='') {?>
     <option <?php if($data['location_id'] == $locations['id']){ ?>selected <?php } ?> value="<?php echo $locations['id']; ?>" >
      <?php echo $locations['location_name']; ?>
    </option>
  <?php }  } ?>  
</select>
</div>
</div>
</div>
<?php
foreach ($emptypes_details as $emptypes) {
  if($empdata['employee_type']==$emptypes['genc_detail_code']) {
   $exist_emptypes = $emptypes['genc_detail_name'];
   break;
 }
}
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_ety; ?>" >
 <div  class="mt-2" >
  <label class="form-label ">Employment Type
    <?php if($text_color_ety!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['employee_type']==''?NODATA: $exist_emptypes; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
    <div class="form-radio mt-1 <?php echo  $read_only_ety; ?>">
     <?php
     foreach ($emptypes_details as $emptypes) {
      ?>

      <div class="radio radio-inline"> 
       <label class="form-label <?php echo $text_color_ety; ?>">
         <input type="radio" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?> value="<?php echo $emptypes['genc_detail_code'];?>" style="display:none"/>
         <i class="helper"></i><?php echo $emptypes['genc_detail_name'];?> </label>
       </div>
     <?php } ?>
   </div>
 </div>
</div>
<div class="col-lg-6 " style="display: <?php echo $display_div_ebi; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_ebi; ?>" >
  <label class="">Billable 
    <?php if($text_color_ebi!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['billable']==''?NODATA: ($empdata['billable']==1?'Yes':'No'); ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
    <div class="form-radio <?php echo $read_only_ebi; ?>">
      <div class="radio radio-inline"> 
       <label class="form-label <?php echo $text_color_ebi; ?>">
         <input type="radio" name="billable" <?php if($data['billable'] ==0) {?> checked="checked" <?php } ?> value="0" style="display:none" />
         <i class="helper"></i>Yes </label>
       </div>
       <div class="radio radio-inline">
         <label class="form-label <?php echo $text_color_ebi; ?>">
           <input type="radio" name="billable" <?php if($data['billable'] ==1) {?> checked="checked" <?php } ?> value="1" style="display:none" />
           <i class="helper"></i>No </label>
         </div>
       </div>
     </div>
   </div>
   <?php 
   if($fieldaccesright['field']['office_phone']!="NA"){ 
    $read_only_ofph = $fieldaccesright['field']['office_phone'] !="E"?"custom_readonly":"";
  }else{
    $display_div_ofph = 'none';
  }
  $text_color_ofph= $data['office_phone'] != $empdata['office_phone'] ? 'text-danger':"";
  if($fieldaccesright['field']['office_ext']!="NA"){ 
    $read_only_ext = $fieldaccesright['field']['office_ext'] !="E"?"custom_readonly":"";
  }else{
    $display_div_ext = 'none';
  }
  $text_color_ext= $data['office_ext'] != $empdata['office_ext'] ? 'text-danger':"";
  ?>
  <div class="col-lg-6" style="display: <?php echo $display_div_ofph; ?>">
   <div  class="mt-2" style="display: <?php echo $display_div_ofph; ?>" >
    <label class="col-form-label">
     Office Phone 
     <?php if($text_color_ofph!="") { ?> 
       <div data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['office_phone']==''?NODATA:$empdata['office_phone'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </div>
     <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0 <?php echo $read_only_ofph; ?>">
     <input  type="text" class="form-control form-control-lg  <?php echo $text_color_ofph; ?>" name="office_phone" placeholder="Office Phone" value="<?php echo $data['office_phone']; ?>"/>
   </div>
 </div>
</div>
<div class="col-lg-6" style="display: <?php echo $display_div_ext; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_ext; ?>" >
  <label class="col-form-label">
   Extension <?php if($text_color_ext!="") { ?> 
     <div data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['office_ext']==''?NODATA:$empdata['office_ext'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </div>
   <?php } ?>
 </label>
 <div class="input-group input-icon-right mb-0 <?php echo $read_only_ext; ?>">
   <input  type="text" class="form-control form-control-lg  <?php echo $text_color_ext; ?>" name="office_ext" placeholder="Extension" value="<?php echo $data['office_ext']; ?>"/>
 </div>
</div>
</div>
</div>
</div>
</fieldset<?php echo $divclassname; ?>>
<?php $fieldaccesright = $vafield['personal_details'];
$display_work = $divclassname = "";
if($fieldaccesright['access'] =="NA"){ 
   $display_work = "style='display:none'";
   $divclassname="none";
}else{
  ?><h3> Personal Details</br><small>Please enter your personal details</small> </h3> <?php
}
?>
<fieldset<?php echo $divclassname; ?>>
  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
   <h4 class="mb-5 font-weight-bold text-dark">Personal Details</h4>
   <?php 
   if($fieldaccesright['field']['date_of_birth']!="NA"){ 
    $read_only_bir = $fieldaccesright['field']['date_of_birth'] !="E"?"custom_readonly":"";
  }else{
    $display_div_bir = 'none';
  }
  $text_color_bir= fromDate($data['date_of_birth']) != fromDate($empdata['date_of_birth']) ? 'text-danger':"";
  ?>
  <div class="row">
    <div class="col-xl-6 " style="display: <?php echo $display_div_bir; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
      <label class="col-form-label">Date of Birth <?php if($text_color_bir!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['date_of_birth']==''?NODATA:$empdata['date_of_birth'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
      <div class="<?php echo $read_only_bir; ?>">
       <?php $date_pic1 = fromDate($data['date_of_birth']); ?>
       <input type="text" class="form-control form-control-lg datepicker  <?php echo $text_color_bir; ?>"  value="<?php echo  fromDate($date_pic1); ?>"  name="date_of_birth"   />
     </div>
   </div>
 </div>
 <?php
 $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic1) ; 
 ?>
 <div class="col-xl-6" style="display: <?php echo $display_div_bir; ?>">
   <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
    <label class="col-form-label">Age In Years</label>
    <div class="input-group input-icon-right mb-0">
     <input readonly type="text" class="form-control form-control-lg " name="age" placeholder="Age" value="<?php echo floor($diff / (365*60*60*24)); ?>"/> </div>
   </div>
 </div>
 <?php
 if($fieldaccesright['field']['gender']!="NA"){ 
  $read_only_gen = $fieldaccesright['field']['gender'] !="E"?"custom_readonly":"";
}else{
  $display_div_gen = 'none';
} 
$text_color_gen= $data['gender'] != $empdata['gender'] ? 'text-danger':"";
if($fieldaccesright['field']['marital_status']!="NA"){ 
  $read_only_ma = $fieldaccesright['field']['marital_status'] !="E"?"custom_readonly":"";
}else{
  $display_div_ma = 'none';
}
$text_color_ma= $data['marital_status'] != $empdata['marital_status'] ? 'text-danger':"";
?>
<?php
foreach ($gender_details as $gender_data) {
  if($empdata['gender']==$gender_data['genc_detail_code']) {
   $exist_gender = $gender_data['genc_detail_name'];
   break;
 }
}
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_gen; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_gen; ?>" >
  <label class="col-form-label">Gender <?php if($text_color_gen!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['gender']==""?NODATA : $exist_gender; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="form-radio  <?php echo $read_only_gen; ?>">
   <?php
   foreach ($gender_details as $gender_data) {
    ?>
    <div class="radio radio-inline">
     <label class="form-label <?php echo $text_color_gen; ?>">
       <input  type="radio" name="gender" <?php if($data['gender'] == $gender_data['genc_detail_code']) {?>  checked="checked" <?php } ?> value="<?php echo $gender_data['genc_detail_code'];?>" style="display:none"/>
       <i class="helper"></i>
       <?php echo $gender_data['genc_detail_name']; ?>
     </label>
   </div>
 <?php } ?>
</div>
</div>
</div>
<?php
foreach ($marital_status_details as $marital_data) {
  if($empdata['marital_status']==$marital_data['id']) {
   $exist_marital_data = $marital_data['marital_status_name'];
   break;
 }
}
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_ma; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_ma; ?>" >
  <label class="col-form-label">Marital Status <?php if($text_color_ma!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['marital_status'] == '' ? NODATA : $exist_marital_data; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div  class="form-radio <?php echo $read_only_ma; ?>">
   <?php
   foreach ($marital_status_details as $marital_stat) {
    ?>
    <div class="radio radio-inline">
     <label   class="form-label <?php echo $text_color_ma; ?>">
       <input onclick="showmarriagedate(this.value);" type="radio" name="marital_status" <?php if($data['marital_status'] == $marital_stat['id']) {?> checked="checked" <?php } ?> value="<?php echo $marital_stat['id']; ?>" style="display:none"/>
       <i class="helper"></i>
       <?php echo $marital_stat['marital_status_name']; ?>
     </label>
   </div>
 <?php } ?>
</div>
</div>
</div>
<!------------------------- 4 fields --------------------->
<?php 
if($fieldaccesright['field']['disability']!="NA"){ 
  $read_only_disa = $fieldaccesright['field']['disability'] !="E"?"custom_readonly":"";
}else{
  $display_div_disa = 'none';
}
$text_color_disa= $data['disability'] != $empdata['disability'] ? 'text-danger':"";
if($fieldaccesright['field']['disability_percentage']!="NA"){ 
  $read_only_disap = $fieldaccesright['field']['disability_percentage'] !="E"?"readonly":"";
}else{
  $display_div_disap = 'none';
}
$text_color_disap= $data['disability_percentage'] != $empdata['disability_percentage '] ? 'text-danger':"";
?>
<div class="col-lg-6" style="display: <?php echo $display_div_disa; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_disa; ?>" >
  <label class="col-form-label">Disability <?php if($text_color_disa!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" ><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div  class="form-radio  <?php echo $read_only_disa; ?>">
   <?php
   $yesno = array('0'=>'Yes', '1'=>'No');
   foreach ($yesno as $ynkey=>$yn) {
    ?>
    <div class="radio radio-inline">
     <label class="form-label <?php echo $text_color_gen; ?>">
       <input   type="radio" name="disability" <?php if($data['disability'] == $ynkey) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
       <i class="helper"></i>
       <?php echo $yn; ?>
     </label>
   </div>
 <?php } ?>
</div>
</div>
</div>
<div class="col-lg-6" style="display: <?php echo $display_div_disap; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_disap; ?>" >
  <label class="col-form-label"> Disability percentage
   <?php if($data['disability_percentage']!=$empdata['disability_percentage']) { ?>
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['soc_facebook']==''?NODATA:$empdata['soc_facebook'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0">
     <input <?php echo $read_only_disap; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_disap; ?>"  placeholder="" name="disability_percentage" value="<?php echo $data['disability_percentage']; ?>"/>
   </div>
 </div>
</div>
<?php 
if($fieldaccesright['field']['physically_challenged_children']!="NA"){ 
  $read_only_pcc = $fieldaccesright['field']['physically_challenged_children'] !="E"?"custom_readonly":"";
}else{
  $display_div_pcc = 'none';
}
$text_color_pcc= $data['physically_challenged_children'] != $empdata['physically_challenged_children'] ? 'text-danger':"";
//$display_div_wti = 'none';
if($fieldaccesright['field']['work_textile_industry']!="NA"){ 
  $read_only_wti = $fieldaccesright['field']['work_textile_industry'] !="E"?"custom_readonly":"";
}else{
  $display_div_wti = 'none';
}
$text_color_wti= $data['work_textile_industry'] != $empdata['work_textile_industry'] ? 'text-danger':"";
?>
<div class="col-lg-6 " style="display: <?php echo $display_div_wti; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_wti; ?>" >
  <label class="col-form-label">Work Textile Industry <?php if($text_color_wti!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" ><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div  class="form-radio  <?php echo $read_only_wti; ?>">
   <?php
   $yesno = array('0'=>'Yes', '1'=>'No');
   foreach ($yesno as $ynkey=>$yn) {
    ?>
    <div class="radio radio-inline">
     <label class="form-label <?php echo $text_color_gen; ?>">
       <input   type="radio" name="work_textile_industry" <?php if($data['work_textile_industry '] == $ynkey) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
       <i class="helper"></i>
       <?php echo $yn; ?>
     </label>
   </div>
 <?php } ?>
</div>
</div>
</div>
<div class="col-lg-6" style="display: <?php echo $display_div_pcc; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_pcc; ?>" >
  <label class="col-form-label">Physically Challenged Children <?php if($text_color_pcc!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" ><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div  class="form-radio  <?php echo $read_only_pcc; ?>">
   <?php
   $yesno = array('0'=>'Yes', '1'=>'No');
   foreach ($yesno as $ynkey=>$yn) {
    ?>
    <div class="radio radio-inline">
     <label class="form-label <?php echo $text_color_gen; ?>">
       <input   type="radio" name="physically_challenged_children" <?php if($data['physically_challenged_children'] == $ynkey) {?>  checked="checked" <?php } ?> value="<?php echo $ynkey; ?>" style="display:none"/>
       <i class="helper"></i>
       <?php echo $yn; ?>
     </label>
   </div>
 <?php } ?>
</div>
</div>
</div>
<?php
if($fieldaccesright['field']['wedding_date']!="NA"){ 
  $read_only_ms = $fieldaccesright['field']['wedding_date'] !="E"?"custom_readonly":"";
}else{
  $display_div_ms = 'none';
}
$text_color_ms= fromDate($data['wedding_date']) != fromDate($empdata['wedding_date']) ? 'text-danger':"";
if($fieldaccesright['field']['blood_group']!="NA"){ 
  $read_only_bg = $fieldaccesright['field']['blood_group'] !="E"?"custom_readonly":"";
}else{
  $display_div_bg = 'none';
}
$text_color_bg= $data['blood_group'] != $empdata['blood_group'] ? 'text-danger':"";
?>
<div class="col-lg-6 " style=""  id="marriagedateshow">
 <div class="mt-2" style="display: <?php echo $display_div_ms; ?>" id="marriagedateshow1">
  <label  class="col-form-label">Marrige Date<?php if($text_color_ms!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['wedding_date']==''?NODATA:$empdata['wedding_date'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
  <div class="date <?php echo $read_only_ms; ?>">
   <?php $date_pic1 = fromDate($data['wedding_date']); ?>
   <input type="text"  class="form-control form-control-lg datepicker  <?php echo $text_color_ms; ?>" placeholder="Marrige Date" name="wedding_date" value="<?php echo  $date_pic1; ?>"/>

 </div>
</div>
</div>
<div class="col-lg-6 >" style="display: <?php echo $display_div_bg; ?>" >
 <div class="mt-2 w-full" style="display: <?php echo $display_div_bg; ?>" >
  <label class="col-form-label">Blood Group <?php if($text_color_bg!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['blood_group']==''?NODATA:$empdata['blood_group'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
</label>
<div class="<?php echo $read_only_bg; ?>">
<select class ="form-select js-example-basic-single" name="blood_group">
  <option value="">Select Any One</option>
  <?php foreach ($blood_groups_details as $blood_groups) {

    ?>
    <option <?php if($data['blood_group'] == $blood_groups['id']){ ?>selected  <?php } ?> value="<?php echo $blood_groups['id']; ?>" >
      <?php echo $blood_groups['blood_group']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
</div>
<!--end::Input-->
<?php 
if($fieldaccesright['field']['soc_facebook']!="NA"){ 
  $read_only_fb = $fieldaccesright['field']['soc_facebook'] !="E"?"readonly":"";
}else{
  $display_div_fb = 'none';
}
$text_color_fb = ($data['soc_facebook'] != $empdata['soc_facebook']) ? 'text-danger':"";
?>
<div class="col-lg-6" style="display: <?php echo $display_div_fb; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_fb; ?>" >
  <label class="col-form-label">Facebook
   <?php if($data['soc_facebook']!=$empdata['soc_facebook']) { ?>
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['soc_facebook']==''?NODATA:$empdata['soc_facebook'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0">
     <input <?php echo $read_only_fb; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="Facebook URL" name="soc_facebook" value="<?php echo $data['soc_facebook']; ?>"/>
   </div>
 </div>
</div>
<?php 
if($fieldaccesright['field']['soc_twitter']!="NA"){ 
  $read_only_twi = $fieldaccesright['field']['soc_twitter'] !="E"?"readonly":"";
}else{
  $display_div_twi = 'none';
}
$text_color_twi = ($data['soc_twitter'] != $empdata['soc_twitter']) ? 'text-danger':"";
$display_div_twi = $field_set['TWITTER']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6" style="display: <?php echo $display_div_twi; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_twi; ?>" >
  <label class="col-form-label">Twitter
   <?php if($data['soc_twitter']!=$empdata['soc_twitter']) { ?>
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['soc_twitter']==''?NODATA:$empdata['soc_twitter'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0">
     <input <?php echo $read_only_twi; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="Twitter URL" name="soc_twitter" value="<?php echo $data['soc_twitter']; ?>"/>
   </div>
 </div>
</div>
<?php 
if($fieldaccesright['field']['soc_linkedin']!="NA"){ 
  $read_only_ln = $fieldaccesright['field']['soc_linkedin'] !="E"?"readonly":"";
}else{
  $display_div_ln = 'none';
}
$text_color_ln = ($data['soc_linkedin'] != $empdata['soc_linkedin']) ? 'text-danger':"";
?>
<div class="col-lg-6" style="display: <?php echo $display_div_ln; ?>" >
 <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
  <label class="col-form-label">Linkedin
   <?php if($data['soc_linkedin']!=$empdata['soc_linkedin']) { ?>
     <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['soc_linkedin']==''?NODATA:$empdata['soc_linkedin'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?>
   </label>
   <div class="input-group input-icon-right mb-0">
     <input <?php echo $read_only_ln; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="LinkedIn URL" name="soc_linkedin" value="<?php echo $data['soc_linkedin']; ?>"/>
   </div>
 </div>
</div>
</div>
</div>
</fieldset<?php echo $divclassname; ?>>
<?php $fieldaccesright = $vafield['experience_details'];
$display_work = $divclassname = "";
if($fieldaccesright['access'] =="NA"){ 
   $display_work = "style='display:none'";
   $divclassname="none";
}else{
  ?><h3> Employment History </br><small>Please enter your experience details</small> </h3> <?php
}
?>
<fieldset<?php echo $divclassname; ?>>
  <div class="pb-5" data-wizard-type="step-content">
    <h4 class="mb-5 font-weight-bold text-dark ">Employment History</h4>
    <?php 
    $readonlyclass = $displaydivclass = "";
    if($fieldaccesright['field']['employee_experiances']!="NA"){ 
      $readonlyclass = $fieldaccesright['field']['employee_experiances'] !="E"?"custom_readonly":"";
    }else{
      $displaydivclass = 'none';
    }
    ?>
    <div class="form-group row <?php echo $readonlyclass ?>" style="display: <?php echo $displaydivclass; ?>">
      <div class="form-group col-lg-12">
        <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="extable">
          <tr>
            <th  width="2%"></th>
            <th class="" width="28%">Company Name</th>
            <th class="thheader" width="28%">Job Description </th>
            <th class="" width="20%"> From/To</th>
          </tr> 
          <?php $k=1; if(count((array)$exp_details)=='0'){ 
            $exp_details[] = array("id"=>NULL,"expe_rowid"=>NULL,"company_name"=>NULL,"job_descrption"=>NULL,"work_from"=>NULL,"work_to"=>NULL); } 
                  //if(count($exp_details)>0){  
            foreach ($exp_details as $expe) { 
              ?>
              <tr>
                <td>
                  <div class="checkbox-fade fade-in-primary">
                    <label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span> </label>
                  </div>
                </td> 
                <td>
                  <input type="hidden" name="expe_rowid[]" class="form-control" autocomplete="off" value="<?php echo $expe['expe_rowid']=='0'?$expe['id']:$expe['expe_rowid']; ?>">

                  <input type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>">  </td>
                  <td><input type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off">  </td>
                  <td><input type="text d-block" name="work_from[]" class="form-control datepicker" id="kt_datepicker_33<?php echo $k; ?>" value="<?php echo fromDate($expe['work_from']); ?>" autocomplete="off"> 
                    <input type="txet" name="work_to[]" class="form-control datepicker" id="kt_datepicker_11<?php echo $k; ?>" value="<?php echo fromDate($expe['work_to']); ?>"autocomplete="off">  
                  </td> 
                </tr>
              <?php }  ?>
            </table>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button class="btn btn-danger" id="removeRows" onclick="removeexprows()"  type="button">- Delete</button> &nbsp;
                <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows" type="button">+ Add More</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </fieldset<?php echo $divclassname; ?>>
      <?php $fieldaccesright = $vafield['employee_qualifications'];
      $display_work = $divclassname = "";
      if($fieldaccesright['access'] =="NA"){ 
         $display_work = "style='display:none'";
         $divclassname="none";
      }else{
        ?><h3> Educational Details</br><small>Please enter your educational details</small> </h3> <?php
      }
      ?>
      <fieldset<?php echo $divclassname; ?>>
       <div class="pb-5" data-wizard-type="step-content" >
        <h4 class="mb-5 font-weight-bold text-dark">Educational Details</h4>
         <?php 
          $readonlyclass = $displaydivclass = "";
          if($fieldaccesright['field']['employee_qualifications']!="NA"){ 
            $readonlyclass = $fieldaccesright['field']['employee_qualifications'] !="E"?"custom_readonly":"";
          }else{
            $displaydivclass = 'none';
          }
          ?>
        <div class="row <?php echo $readonlyclass ?>" style="display: <?php echo $displaydivclass; ?>">
          <div class="form-group col-lg-12">
            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="edutable">
              <tr>
                <th  width="2%"></th>
                <th class="" width="25%">Institute Name</th>
                <th class="thheader" width="20%">Type</th>
                <th class="thheader" width="20%"> Start/End  Date</th>
                <th class="thheader" width="15%">Score </th>
              </tr>
              <?php if(count((array)$edu_details)=='0'){ $edu_details[] = array('institute_name'=>""); } 
              foreach ($edu_details as $inst) { ?>
                <tr>
                  <td>
                   <div class="checkbox-fade fade-in-primary">
                    <label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span> </label>

                  </div>
                </td>
                <td>
                  <input type="hidden" name="institute_rowid[]" class="form-control " autocomplete="off" value="<?php echo $inst['institute_rowid']==''? $inst['id']:$inst['institute_rowid']; ?>">
                  <input type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off">  </td>
                  <td class="w-full">
                    <select class="form-control form-select js-example-basic-single" name="education_type[]" >
                      <option value="0"> Select Any One</option>
                      <?php foreach ($edutypedata as  $edutype) {   ?> 
                        <option  <?php if($inst['education_type']== $edutype['genc_detail_code']){  ?> selected <?php } ?>   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                      <?php   } ?>
                      <option value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                    </select>
                  </td>
                  <td><input type="text" name="start_date[]" class="form-control datepicker"  id="kt_datepicker_44<?php echo $k; ?>" value="<?php echo fromDate($inst['start_date']); ?>"  autocomplete="off"></br>
                    <input type="text" name="end_date[]" class="form-control datepicker" id="kt_datepicker_22<?php echo $k; ?>"  value="<?php echo fromDate($inst['end_date']); ?>" autocomplete="off">  </td>
                    <td><input type="text" name="score[]" class="form-control "  value="<?php echo $inst['score']; ?>"  autocomplete="off">  </td>
                  </tr>
                <?php }  ?>
              </table>
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <button class="btn btn-danger" id="removeRows" onclick="removeedurows()"  type="button">- Delete</button> &nbsp;
                  <button class="btn btn-success" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </fieldset<?php echo $divclassname; ?>>  
      <?php $fieldaccesright = $vafield['employee_dependents'];
      $display_work = $divclassname = "";
      if($fieldaccesright['access'] =="NA"){ 
         $display_work = "style='display:none'";
         $divclassname="none";
      }else{
        ?> <h3> Dependents Details</br><small>Please enter your dependent details</small> </h3> <?php
      }
      ?>      
     
      <fieldset<?php echo $divclassname; ?>>
       <div class="pb-5" data-wizard-type="step-content" >
        <h4 class="mb-5 font-weight-bold text-dark">Dependents</h4>
        <?php 
          $readonlyclass = $displaydivclass = "";
          if($fieldaccesright['field']['employee_dependents']!="NA"){ 
            $readonlyclass = $fieldaccesright['field']['employee_dependents'] !="E"?"custom_readonly":"";
          }else{
            $displaydivclass = 'none';
          }
          ?>
        <div class="row <?php echo $readonlyclass ?>" style="display: <?php echo $displaydivclass; ?>">
          <div class="form-group col-lg-12">
            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="deptable">
              <tr>
                <th  width="2%"></th>
                <th class="" width="25%"> Name</th>
                <th class="thheader" width="20%">Relationship Type</th>
                <th class="thheader" width="20%">DOB</th>
              </tr>
              <?php  if(count((array)$dep_details)=='0'){ $dep_details[] = array('dependent_name'=>""); } 
              foreach ($dep_details as $depe) { ?>
                <tr>
                  <td>
                   <div class="checkbox-fade fade-in-primary">
                    <label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span> </label>

                  </div>
                </td>
                <input type="hidden" name="dependent_rowid[]" class="form-control " autocomplete="off" value="<?php echo $depe['dependent_rowid']==''? $depe['id']:$depe['institute_rowid']; ?>">
                <td><input type="text" name="dependent_name[]" class="form-control " value="<?php echo $depe['dependent_name']; ?>" autocomplete="off">  </td>
                <td class="w-full">
                  <select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
                    <option value="0"> Select Any One</option>
                    <?php foreach ($relatypedata as  $reladata) {   ?> 
                      <option   <?php if($depe['dependent_relationship_type']== $reladata['id']){  ?> selected <?php } ?> value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                    <?php   } ?>
                    <option value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                  </select>
                </td>
                <td><input type="text" name="dependent_date_of_birth[]" class="form-control datepicker" id="kt_datepicker_55<?php echo $k; ?>" value="<?php echo fromDate($depe['dependent_date_of_birth']); ?>" autocomplete="off">  </td>
              </tr>
            <?php }  ?>
          </table>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <button class="btn btn-danger " id="removeRows" onclick="removedeprows()"  type="button">- Delete</button> &nbsp;
              <button class="btn btn-success"  onclick="adddeprows()" id="addRows" type="button">+ Add More</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </fieldset<?php echo $divclassname; ?>>
  <?php $fieldaccesright = $vafield['employee_contacts'];
  $display_work = $divclassname = "";
      if($fieldaccesright['access'] =="NA"){ 
         $display_work = "style='display:none'";
         $divclassname="none";
      }else{
        ?>   <h3> Emergency Contact</br><small>Please enter your emergency contact details</small> </h3> <?php
      }
      ?>

  <fieldset<?php echo $divclassname; ?>>
    <div class="pb-5" data-wizard-type="step-content">
      <h4 class="mb-5 font-weight-bold text-dark">Emergency Contact</h4>
      <!--begin::Section-->
      <?php 
          $readonlyclass = $displaydivclass = "";
          if($fieldaccesright['field']['employee_contacts']!="NA"){ 
            $readonlyclass = $fieldaccesright['field']['employee_contacts'] !="E"?"custom_readonly":"";
          }else{
            $displaydivclass = 'none';
          }
          ?>
      <div class="row <?php echo $readonlyclass ?>" style="display: <?php echo $displaydivclass; ?>">
        <input type="hidden" name="tmp_id" value="<?php echo $temp_id; ?>">
        <div class="form-group col-lg-12">
          <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="emergetable">
            <tr>
              <th  width="2%"></th>
              <th class="thheader" width="25%">Name</th>
              <th class="" width="25%">Relationship</th>
              <th class="thheader" width="25%">Home No</th>
              <th class="thheader" width="25%">Mobile No</th>
            </tr>
            <?php if(count((array)$emerge_details)=='0'){ $emerge_details[] = array('contact_name'=>""); }  
            foreach ($emerge_details as $eno) { ?>
              <tr>
                <td>
                  <div class="checkbox-fade fade-in-primary">
                    <label class="form-label"><input class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span> </label>
                  </div>
                </td>
                <input type="hidden" name="contact_rowid[]" class="form-control " autocomplete="off" value="<?php echo $eno['contact_rowid']==''? $eno['id']:$eno['institute_rowid']; ?>">
                <td>
                  <input type="text" name="contact_name[]" class="form-control " value="<?php echo $eno['contact_name']; ?>" autocomplete="off"> 
                </td>
                <td class="w-full">
                  <select class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                    <option value="0"> Select Any One</option>
                    <?php foreach ($relatypedata as  $reladata) {   ?> 
                      <option   <?php if($eno['contact_relationship']== $reladata['id']){  ?> selected <?php } ?> value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                    <?php   } ?>
                  </select>
                </td>
                <td><input type="text" name="contact_home_no[]" class="form-control " value="<?php echo $eno['contact_home_no']; ?>" autocomplete="off">  </td>
                <td><input type="text" name="contact_mobile_no[]" class="form-control " value="<?php echo $eno['contact_mobile_no']; ?>" autocomplete="off">  </td>
              </tr>
            <?php }  ?>
          </table>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <button class="btn btn-danger" id="removeRows" onclick="removeemerows()"  type="button">- Delete</button> &nbsp;
              <button class="btn btn-success "  onclick="addemrgerows()" id="addRows" type="button">+ Add More</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </fieldset<?php echo $divclassname; ?>>
   <?php $fieldaccesright = $vafield['proof_details'];
   $display_work = $divclassname = "";
      if($fieldaccesright['access'] =="NA"){ 
         $display_work = "style='display:none'";
         $divclassname="none";
      }else{
        ?> <h3> Proof Details</h3><?php
      }
      ?>

  <fieldset<?php echo $divclassname; ?>>
   <div class="pb-5" data-wizard-type="step-content">
     <h4 class="mb-5 font-weight-bold text-dark">Proof Details</h4>
     <div class="row">
       <div >
        <div class="card-body">
          <div class="form-group row">
           <table class="table table-vertical-center">
             <thead class="thead-light">
               <tr>
                 <th class="text-center" scope="col">#</th>
                 <th class="text-center" scope="col">Type</th>
                 <th class="text-center"  scope="col">Number</th>
                 <!-- <th class="text-center" scope="col">Issue Date</th> -->
                 <th  class="text-center" scope="col">Expiry Date</th>
               </tr>
             </thead>
             <tbody>
              <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['adhar_no']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['adhar_no'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'custom_readonly';
                }
              ?>
               <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
                 <td scope="row">1</td>
                 <td>
                  <input type="text" name="" disabled class="form-control" value="Aadhar Card" >
                </td>
                <td>
                 <input type="text" name="adhar_no" class="form-control" value="<?php echo $data['adhar_no']; ?>" <?php echo $readonlyclass;?>>
               </td>
               <!-- <td>
                 <input type="text" name="adhar_issue_date" class="form-control datepicker" value="<?php echo $data['adhar_issue_date']!="0000-00-00"?$data['adhar_issue_date']:""; ?>" >
               </td> -->
               <td>
                 <input type="text" name="" disabled class="form-control" value="<?php echo ""; ?>" >
               </td>
             </tr>
             <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['passport']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['passport'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'custom_readonly';
                }
              ?>
             <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
               <td scope="row">2</td>
               <td>
                <input type="text" name="" disabled class="form-control" value="Passport" disabled>
              </td>
              <td>
               <input type="text" name="passport" class="form-control" value="<?php echo $data['passport']; ?>" <?php echo $readonlyclass;?>>
             </td>
             <!-- <td>
               <input type="text" name="passport_issue_date" class="form-control datepicker" value="<?php echo $data['passport_issue_date']!="0000-00-00"?$data['passport_issue_date']:""; ?>" >
             </td> -->
             <td>
               <input type="text" name="passport_expiry_date" class="form-control datepicker" value="<?php echo $data['passport_expiry_date']!="0000-00-00"?$data['passport_expiry_date']:""; ?>" >
             </td>
           </tr>
           <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['driving_licence']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['driving_licence'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'd-none';
                }
              ?>
           <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
             <td scope="row">3</td>
             <td>
              <input type="text" name="" disabled class="form-control" value="Driving">
            </td>
            <td>
             <input type="text" name="driving_licence" class="form-control" value="<?php echo $data['driving_licence']; ?>" >
           </td>
          <!--  <td>
             <input type="text" name="driving_licence_issue_date" class="form-control datepicker" value="<?php echo $data['driving_licence_issue_date']!="0000-00-00"?$data['driving_licence_issue_date']:""; ?>" >
           </td> -->
           <td>
             <input type="text" name="driving_licence_expiry_date" class="form-control datepicker" value="<?php echo $data['driving_licence_expiry_date']!="0000-00-00"?$data['driving_licence_expiry_date']:""; ?>" >
           </td>
         </tr>
         <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['pan_number']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['pan_number'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'd-none';
                }
              ?>
         <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
           <td scope="row">4</td>
           <td>
            <input type="text" name="" disabled class="form-control" value="Pan Number">
          </td>
          <td>
           <input type="text" name="pan_number" class="form-control" value="<?php echo $data['pan_number']; ?>" >
         </td>
         <!-- <td>
           <input type="text" name="pan_issue_date" class="form-control datepicker" value="<?php echo $data['pan_issue_date']!="0000-00-00"?$data['pan_issue_date']:""; ?>" >
         </td> -->
         <td>
           <input type="text"  disabled name="" class="form-control" >
         </td>
       </tr>
       <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['pf_no']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['pf_no'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'd-none';
                }
              ?>
       <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
         <td scope="row">5</td>
         <td>
          <input type="text" name="" disabled class="form-control" value="PF Number" >
        </td>
        <td>
         <input type="text" name="pf_no" class="form-control" value="<?php echo $data['pf_no']; ?>" >
       </td>
       <!-- <td>
         <input type="text" name="pf_issue_date" class="form-control datepicker" value="<?php echo $data['pf_issue_date']!="0000-00-00"?$data['pf_issue_date']:""; ?>" >
       </td> -->
       <td>
         <input type="text" disabled name="" class="form-control" disabled>
       </td>
     </tr>
     <?php $readonlyclass = $displaycalss = ""; 
              if($fieldaccesright['field']['pf_uan']!="NA"){ 
                  $readonlyclass = $fieldaccesright['field']['pf_uan'] !="E"?"custom_readonly":"";
                }else{
                  $displaycalss = 'd-none';
                }
              ?>
     <tr class="<?php echo $displaycalss; ?> <?php echo $readonlyclass;?>">
       <td scope="row">6</td>
       <td>
        <input type="text" name="" disabled class="form-control" value="UAN Number" >
      </td>
      <td>
       <input type="text" name="pf_uan" class="form-control" value="<?php echo $data['pf_uan']; ?>" >
     </td>
     <!-- <td>
       <input type="text" name="pf_uan_issue_date" class="form-control datepicker" value="<?php echo $data['pf_uan_issue_date']!="0000-00-00"?$data['  pf_uan_issue_date']:""; ?>" >
     </td> -->
     <td>
       <input type="text" disabled name="" class="form-control" disabled>
     </td>
   </tr>
 </tbody>
</table>
</div>
</div>
</div>
</div>
</fieldset<?php echo $divclassname; ?>>	 
<?php $fieldaccesright = $vafield['employee_buddies'];
      $display_work = $divclassname = "";
      if($fieldaccesright['access'] =="NA"){ 
         $display_work = "style='display:none'";
         $divclassname="none";
      }else{
        ?> <h3> Employee Buddy</h3><?php
      }
      ?>  

<fieldset<?php echo $divclassname; ?>>
 <div class="pb-5" data-wizard-type="step-content">
  <div class="row">
   <?php	$text_color_ln = ($data['admin_buddy'] != $empdata['admin_buddy']) ? 'text-danger':"";
   $read_only_ln = $display_div_ln="";
      if($fieldaccesright['field']['admin_buddy']!="NA"){ 
        $read_only_ln = $fieldaccesright['field']['admin_buddy'] !="E"?"custom_readonly":"";
      }else{
        $display_div_ln = 'none';
      }
       
   //$read_only_ln = $field_set['ADMIN_BUDDY']['readonly'] != 0 ? 'readonly' : '';
  // $display_div_ln = $field_set['ADMIN_BUDDY']['display'] != 1 ? 'none' : '';
      echo "<pre>"; print_r($admin_buddy_res); echo "</pre>";
   ?>
   <div class="col-lg-6 w-full position-relative <?php echo $read_only_ln; ?>" style="display: <?php echo $text_color_ln; ?>">
    <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
     <label class="col-form-label">Admin Buddy <span class="text-danger">*</span></label></br>
     <select  class ="form-select js-example-basic-single required" <?php echo $read_only_ln; ?> name="admin_buddy">
      <option value="">Select Any One</option>
      <?php foreach ($admin_buddy_list as $admin_buddy_res) { ?>
       <option value="<?php echo $admin_buddy_res['emp_id']; ?>"  <?php echo $data['admin_buddy'] == $admin_buddy_res['emp_id']?"selected":""; ?>>
        <?php echo $admin_buddy_res['display_name']; ?>
      </option>
    <?php } ?>
  </select>
</div>
</div>
<?php 	$text_color_ln = ($data['hr_buddy'] != $empdata['hr_buddy']) ? 'text-danger':"";
//$read_only_ln = $field_set['HR_BUDDY']['readonly'] != 0 ? 'readonly' : '';
//$display_div_ln = $field_set['HR_BUDDY']['display'] != 1 ? 'none' : '';
 $read_only_ln = $display_div_ln="";
if($fieldaccesright['field']['hr_buddy']!="NA"){ 
        $read_only_ln = $fieldaccesright['field']['hr_buddy'] !="E"?"custom_readonly":"";
      }else{
        $display_div_ln = 'none';
      }
?>
<div class="col-lg-6 position-relative <?php echo $read_only_ln; ?>" style="display: <?php echo $text_color_ln; ?>"> 
  <div class="mt-2 w-full" style="display: <?php echo $display_div_ln; ?>">
   <label class="col-form-label">HR Buddy <span class="text-danger">*</span></label></br>
   <select  class ="form-select js-example-basic-single required" name="hr_buddy" <?php echo $read_only_ln; ?>>
    <option value="">Select Any One</option>
    <?php foreach ($hr_buddy_list as $hr_buddy_res) { ?>
     <option value="<?php echo $hr_buddy_res['emp_id']; ?>" <?php echo $data['hr_buddy'] == $hr_buddy_res['emp_id']?"selected":""; ?>>
      <?php echo $hr_buddy_res['display_name']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<?php 	
 $read_only_ln = $display_div_ln="";
if($fieldaccesright['field']['finance_buddy']!="NA"){ 
  $read_only_ln = $fieldaccesright['field']['finance_buddy'] !="E"?"custom_readonly":"";
}else{
  $display_div_ln = 'none';
}
$text_color_ln = ($data['finance_buddy'] != $empdata['finance_buddy']) ? 'text-danger':"";
//$read_only_ln = $field_set['FINANACE_BUDDY']['readonly'] != 0 ? 'readonly' : '';
//$display_div_ln = $field_set['FINANACE_BUDDY']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6 position-relative <?php echo $read_only_ln; ?>" style="display: <?php echo $text_color_ln; ?>"> 
  <div class="mt-2 w-full" style="display: <?php echo $display_div_ln; ?>">
   <label class="col-form-label">Finance Buddy <span class="text-danger">*</span></label></br>
   <select  class ="form-select js-example-basic-single required" name="finance_buddy" <?php echo $read_only_ln; ?>>
    <option value="">Select Any One</option>
    <?php foreach ($finance_buddy_list as $finance_buddy_res) { ?>
     <option value="<?php echo $finance_buddy_res['emp_id']; ?>" <?php echo $data['finance_buddy'] == $finance_buddy_res['emp_id']?"selected":""; ?>>
      <?php echo $finance_buddy_res['display_name']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<?php 	
 $read_only_ln = $display_div_ln="";
if($fieldaccesright['field']['project_buddy']!="NA"){ 
  $read_only_ln = $fieldaccesright['field']['project_buddy'] !="E"?"custom_readonly":"";
}else{
  $display_div_ln = 'none';
}
$text_color_ln = ($data['project_buddy'] != $empdata['project_buddy']) ? 'text-danger':"";
//$read_only_ln = $field_set['PROJECT_BUDDY']['readonly'] != 0 ? 'readonly' : '';
//$display_div_ln = $field_set['PROJECT_BUDDY']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6 position-relative <?php echo $read_only_ln; ?>" style="display: <?php echo $text_color_ln; ?>" > 
  <div class="mt-2 w-full" style="display: <?php echo $display_div_ln; ?>">
   <label class="col-form-label">Project Buddy <span class="text-danger">*</span></label></br>
       <select  class ="form-select js-example-basic-single required" name="project_buddy" >
        <option value="">Select Any One</option>
        <?php foreach ($project_buddy_list as $project_buddy_res) { ?>
         <option value="<?php echo $project_buddy_res['emp_id']; ?>" <?php echo $data['project_buddy'] == $project_buddy_res['emp_id']?"selected":""; ?>>
          <?php echo $project_buddy_res['display_name']; ?>
        </option>
      <?php } ?>
    </select> 
  </div>
</div>
<?php 	
$read_only_ln = $display_div_ln="";
if($fieldaccesright['field']['loc_buddy']!="NA"){ 
  $read_only_ln = $fieldaccesright['field']['loc_buddy'] !="E"?"custom_readonly":"";
}else{
  $display_div_ln = 'none';
}
$text_color_ln = ($data['loc_buddy'] != $empdata['loc_buddy']) ? 'text-danger':"";
//$read_only_ln = $field_set['LOC_BUDDY']['readonly'] != 0 ? 'readonly' : '';
//$display_div_ln = $field_set['LOC_BUDDY']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6 position-relative" style="display: <?php echo $text_color_ln; ?>" > 
  <div class="mt-2 w-full <?php echo $read_only_ln; ?>" style="display: <?php echo $display_div_ln; ?>">
   <label class="col-form-label">Location Buddy <span class="text-danger">*</span></label></br>
   <select  class ="form-select js-example-basic-single required" name="loc_buddy" >
    <option value="">Select Any One</option>
    <?php foreach ($location_buddy_list as $location_buddy_res) { ?>
     <option value="<?php echo $location_buddy_res['emp_id']; ?>"  <?php echo $data['loc_buddy'] == $location_buddy_res['emp_id']?"selected":""; ?>>
      <?php echo $location_buddy_res['display_name']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
</div>
</div>
</fieldset<?php echo $divclassname; ?>>	   
</form>
</section>
</div>
</div>
</div>
</div>
</div> </div> 
</section>
</div>
</div>
</div>
</div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="modal_role" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title modallabel" id="exampleModalLabel">User Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>   
      <form class="form" id="user_application_form" enctype="multipart/form-data" method="post">  
        <div class="modal-body j-pro">
          <div class="card-body">
            <div class="form-group">
              <div class="mt-2 w-full" >
                <?php
                foreach($module_setup as $msetup){
                  $module[$msetup['module_id']] = $msetup['display_name'];
                }
                ?>
                <label class="col-form-label">User Role <span class="text-danger">*</span></label></br>
                <?php foreach($roles as $rolesd){   echo $rolesd['display_name']; }?>
                <select  class ="form-select js-example-basic-single" id="user_application_role" name="user_application_role" <?php echo $read_only_ln; ?>>
                  <?php foreach($roles as $key=>$rolesd){ ?>
                    <optgroup label="<?php echo $module[$key]; ?> ">
                      <?php foreach($rolesd as $rolesr){  ?>
                        <option value="<?php echo $rolesr['role_short_code']; ?>"><?php echo $rolesr['display_name']; ?></option>
                      <?php  } ?>
                    </optgroup>
                  <?php  }  ?>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <span class="text-danger f-w-700" id="popupshowerror" style="display: none">Please fill out all required fields</span>
          <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="refrashpage()">Cancel</button>
          <button type="button" class="btn btn-primary " onclick="submit_role(<?php echo $maindata['emp_data']['id']; ?>)" >Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
<script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
<script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
<script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>