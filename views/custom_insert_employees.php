<?php
defined('LBL_CHECKBOX') || define('LBL_CHECKBOX', 'Checkbox');
defined('LBL_SELECT_ANY_ONE') || define('LBL_SELECT_ANY_ONE', 'Select Any One');
defined('KEY_RELATION_NAME') || define('KEY_RELATION_NAME', 'relation_name');
defined('LBL_DATE_OF_BIRTH') || define('LBL_DATE_OF_BIRTH', 'Date Of Birth');
defined('LBL_ENTER_CONTACT_NAME') || define('LBL_ENTER_CONTACT_NAME', 'Enter Contact Name');
$_SESSION['apply_js']['wizardjs'] = '1';

$company_id = $_SESSION['company_id'];
$emp_role_id = $_SESSION['role_id'];
$role_ids = json_decode($emp_role_id);
$emp_role_id = implode(',', (array)$role_ids);

$emp_loction_id = $_SESSION['employee']['location_id'];
$table_name = TBL_EMPLOYEES;
$url = API."?mode=getemployeeinfoforupdation&company_id=".$company_id."&employee_id=".$employee_id."&location_id=".$emp_loction_id."&role_id=".$emp_role_id."&is_active=0&application_tablename=".$table_name;
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
$admin_buddy_list = array();
$hr_buddy_list = array();
$finance_buddy_list = array();
$project_buddy_list = array();
$location_buddy_list = array();
foreach($buddy_list as $buddy_res){
    $extra_json = json_decode($buddy_res['role_id'],1);
    if(in_array(ADMIN_BUDDY,(array)$extra_json)){
        $admin_buddy_list[] = $buddy_res;
    }
    if(in_array(HR_BUDDY,(array)$extra_json)){
        $hr_buddy_list[] = $buddy_res;
    }
    if(in_array(FINANACE_BUDDY,(array)$extra_json)){
        $finance_buddy_list[] = $buddy_res;
    }
    if(in_array(PROJECT_BUDDY,(array)$extra_json)){
        $project_buddy_list[] = $buddy_res;
    }
    if(in_array(LOCATION_BUDDY,(array)$extra_json)){
        $location_buddy_list[] = $buddy_res;
    }
}

/* Employee Details Start */

?>
<script type="text/javascript">
  function createuser(emp_id){
    jQuery.ajax({
     url: "ajax.php",
     data:'page=createuser&emp_id='+emp_id,
     type: "POST",
     success:function(data){
       console.log(data);
       var msg = <?php echo json_encode(__($_SESSION['error_message']['9026'])); ?>;
       swal.fire({
        title: msg,
        type: "success",
        buttonsStyling: false,
        confirmButtonText: "<i class='la la-hand-o-up'></i><?php echo __('OK'); ?>",
        confirmButtonClass: "btn btn-info",
        showCancelButton: false,
        cancelButtonText: "<i class='la la-hand-o-right'></i> <?php echo __('No, continue'); ?>",
        cancelButtonClass: "btn btn-default"
      });
     },
     error:function (){}
   });
  }

  function submitempyeeform(){
  var formvalue =  $("#example-advanced-create-employee").serialize();
  jQuery.ajax({
   url: "views/custom_insert_employee_popup.php",
   data: formvalue,
   type: "POST",
   success:function(data){
    console.log(data);
    $('#dynamic_popupxxl').html(data);
    $('#kt_modal_dynamic_popupxxl').modal("show");
  }
});

}
function showmarriagedate(value) {
   if(value=='2') {
     document.getElementById('marriagedateshow').style.display ='block';
   }else{
        document.getElementById('marriagedateshow').style.display ='none';
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __(LBL_CHECKBOX); ?>" class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Depandent Name'); ?>" type="text" name="dependent_name[]" placeholder="<?php echo __('Enter Depandent Name'); ?> "  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> <?php echo __(LBL_SELECT_ANY_ONE); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata[KEY_RELATION_NAME]); ?></option> <?php
} ?></select>  </td>';
   htmlRows += '<td><input aria-label="<?php echo __(LBL_DATE_OF_BIRTH); ?>" type="text" name="dependent_date_of_birth[]"  class="form-control datepicker"   autocomplete="off" placeholder="<?php echo __(LBL_DATE_OF_BIRTH); ?>">  </td>';
   htmlRows += '</tr>';
   $('#deptable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __(LBL_CHECKBOX); ?>" class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';


   htmlRows += '<td><input aria-label="<?php echo __(LBL_ENTER_CONTACT_NAME); ?>" type="text" name="contact_name[]"  class="form-control " placeholder="<?php echo __(LBL_ENTER_CONTACT_NAME); ?>"  autocomplete="off"> </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Contact Relationship[]'); ?>" class="form-control form-select js-example-basic-single" name="contact_relationship[]" > <option value="0"> <?php echo __(LBL_SELECT_ANY_ONE); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata[KEY_RELATION_NAME]); ?></option> <?php
} ?></select>    </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Enter Home Number'); ?>" type="text" name="contact_home_no[]" placeholder="<?php echo __('Enter Home Number'); ?>"  class="form-control "  autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Enter Mobile Number'); ?>" type="text" name="contact_mobile_no[]"  class="form-control "  placeholder="<?php echo __('Enter Mobile Number'); ?>"  autocomplete="off">  </td>';

   htmlRows += '</tr>';
   $('#emergetable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __(LBL_CHECKBOX); ?>" class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Company Name'); ?>" type="text" name="company_name[]"  placeholder="<?php echo __('Enter Company Name'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Enter Job Description'); ?>" type="text" name="job_descrption[]"  placeholder="<?php echo __('Enter Job Description'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('From Date'); ?>" type="text" name="work_from[]"  placeholder="<?php echo __('From Date'); ?>" class="form-control datepicker"   autocomplete="off"> <input aria-label="<?php echo __('To Date'); ?>" type="text" name="work_to[]"  placeholder="<?php echo __('To Date'); ?>" class="form-control datepicker mt-2" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#extable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __(LBL_CHECKBOX); ?>" class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Instistute name'); ?>" type="text" name="institute_name[]"  placeholder="<?php echo __('Enter Instistute name'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Education Type[]'); ?>" class="form-control form-select js-example-basic-single" name="education_type[]" > <option value="0"> <?php echo __(LBL_SELECT_ANY_ONE); ?></option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option> <?php
} ?></select> </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Start Date'); ?>" type="text" name="start_date[]" placeholder="<?php echo __('Start Date'); ?>"  class="form-control datepicker"  autocomplete="off"> <input aria-label="<?php echo __('End Date'); ?>" type="text" name="end_date[]"  class="form-control datepicker mt-2"   autocomplete="off" placeholder="<?php echo __('End Date'); ?>">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Enter Score'); ?>" type="text" name="score[]"  class="form-control " placeholder ="<?= htmlspecialchars(__('Enter Score'), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#edutable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar f-20 f-w-800 m-r-10'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
 }

function checkageemployee(val){

   var today = new Date();
    var birthDate = new Date(val);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    if(isNaN(age)){
    age = 0;
    }
    $("#ageinyear").val(age);
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



 .wizard > .steps > ul > li > a {
      font-size: 10px;
 }
 .wizard > .content > .body {
  position: unset !important;
}
.wizard > .content {
  border: none !important;
}
.input-group {
  margin-bottom: 0em;
}
.wizard > .content > .body label {
  margin-bottom: 0em;
}
.select2-container{
   width: 100% !important;
   max-width: 100%;
}
#hr_buddy-error, #admin_buddy-error, #finance_buddy-error, #project_buddy-error, #loc_buddy-error{
position: absolute;
    bottom: -20px;
    margin: 0px;
}
.position-relative{
    position: relative;
}

</style>
<div class="d-flex flex-column-fluid ">
 
            <div class="card">
              <div class="card-block">
                <div class="row">
                  <div class="col-md-12">
                    <div id="wizardb_2">
                      <section>
                       <form class="wizard-form" id="example-advanced-create-employee"  enctype="multipart/form-data" method="post" >
                        <?php if($_GET['RSC'] == EMPLOYEE_ONBOARDING){?>

                          <input type="hidden" name="roll_short_code" value="<?php echo EMPLOYEE_ONBOARDING; ?>">
                        <?php } ?>

                      <h3> <?php echo __('Basic Info'); ?> </h3>
                      <fieldset>
                       <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">

                           <div class="row">
                            <div class="col-xl-6"  >
                             <div class="mt-2" >
                              <span class="col-form-label"><?php echo __('Employee Code'); ?>  <span class="text-danger">*</span></span>
                            <div class=" input-icon-right">
                             <input aria-label="<?php echo __('Enter Employee Code'); ?>"  class="form-control form-control-lg required" type="text" placeholder="<?php echo __('Enter Employee Code'); ?>" name="emp_code" value="<?php echo $data['emp_code']; ?>" />
                            </div>
                         </div>

                       </div>
                       <div class="col-xl-6"  >
                         <div class="mt-2"  >
                          <span class="col-form-label"><?php echo __('User Name'); ?> <span class="text-danger">*</span></span>
                          <div class=" input-icon-right">
                           <input aria-label="<?php echo __('Enter User Name'); ?>" class="form-control form-control-lg  required text-capitalize"   type="text" name="display_name" placeholder="<?php echo __('Enter User Name'); ?> " value="<?php echo $data['display_name']; ?>" />
                         </div>
                       </div>
                     </div>

                     <div class="col-xl-4" >
                       <div class="mt-2" >
                        <span class="col-form-label"><?php echo __('First Name'); ?>  <span class="text-danger">*</span></span>
                        <div class=" input-icon-right">
                         <input aria-label="<?php echo __('Input field'); ?>" class="form-control required  form-control-lg text-capitalize <?php echo $emtyy; ?>" type="text" placeholder="<?php echo __('Enter First Name'); ?>" name="first_name" value=""/>
                       </div>
                     </div>
                   </div>
                   <div class="col-xl-4" >
                     <div class="mt-2"  >
                      <span class="col-form-label"><?php echo __('Middle Name'); ?> </span>
                      <div class="input-group input-icon-right">
                       <input aria-label="<?php echo __('Enter Middle Name'); ?>" class="form-control   form-control-lg  text-capitalize" type="text" placeholder="<?php echo __('Enter Middle Name'); ?>" name="middle_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-4" >
                   <div class="mt-2" >
                    <span class="col-form-label"><?php echo __('Last Name'); ?>  <span class="text-danger">*</span></span>
                      <div class="input-group input-icon-right">
                       <input aria-label="<?php echo __('Enter last Name'); ?>" class="form-control form-control-lg required  text-capitalize" type="text" placeholder="<?php echo __('Enter last Name'); ?>" name="last_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-6"  >

                   <div class="mt-2" >
                    <span class="col-form-label"><?php echo __('Email'); ?>  <span class="text-danger">*</span></span>
                    <div class=" input-icon-right">
                     <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  required  <?php echo $text_color_email; ?>" type="text" placeholder="<?php echo __('Enter Your Email Address'); ?>" name="email_id" value="" />
                   </div>
                 </div>
               </div>

               <div class="col-xl-6"  >
                 <div class="mt-2"  >
                  <span class="col-form-label"><?php echo __('Contact'); ?></span>
                  <div class="input-group input-icon-right">
                   <input aria-label="<?php echo __('Enter Contact Number'); ?>" class="form-control form-control-lg  " type="number" placeholder="<?php echo __('Enter Contact Number'); ?>" name="phone" value=""/>
                 </div>
               </div>
             </div>

             <div class="col-xl-6" >
               <div class="mt-2" >
                <span class="col-form-label"><?php echo __('Address'); ?></span>
                  <textarea aria-label="<?php echo __('Text area'); ?>"  style="overflow:auto" class=" form-control form-control-lg <?php echo $text_color_adr; ?>" rows="5" cols="100" placeholder="<?php echo __('Enter Your Address'); ?>" name="address"></textarea>
                </div>

              </div>
              <div class="col-xl-6" >
               <div class="mt-2"  >
                <span class="col-form-label"><?php echo __('Post Code'); ?> </span>
                <div class="input-group">

                 <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control form-control-lg  <?php echo $text_color_pc; ?> " placeholder="<?php echo __('Enter Your Post-Code'); ?>" name="post_code" value=""/>
               </div>
             </div>
           </div>
         </div>

       </div>
     </fieldset>

     <h3> <?php echo __('Work'); ?> </h3>
     <fieldset>

      <div class="pb-5" data-wizard-type="step-content">

        <div class="row">

          <div class="col-lg-6" >
           <div  class="mt-2"  >
            <span class="col-form-label"><?php echo __('Date of Joining'); ?> </span>
            <div class="">
             <?php $date_pic = fromDate($data['joining_date']); ?>
             <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control datepicker form-control-lg  <?php echo $text_color_dj; ?>"  value=""  name="joining_date"  />
           </div>
         </div>
       </div>


      <div class="col-lg-6" >
       <div  class="mt-2"  >
        <span class="col-form-label"><?php echo __('Department'); ?></span>
          <select aria-label="<?php echo __('Department'); ?>" class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
            <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
            <?php foreach ($dept_details as $departments) {
              ?>
              <option  value="<?php echo $departments['id']; ?>" >
                <?php echo __($departments['department_name']); ?>
              </option>
              <?php

           } ?>
         </select>
       </div>
     </div>

    <div class="col-lg-6"  >
     <div  class="mt-2" >
      <span class="col-form-label"><?php echo __('Repoting to'); ?> </span>
      <select aria-label="<?php echo __('Reporting Manager'); ?>"  class ="form-control form-control-lg  form-select js-example-basic-single" name="reporting_manager_id">
        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>

        <?php foreach ($application_users_details as $application_users) {

         ?>
         <option value="<?php echo $application_users['id']; ?>" >
          <?php echo __($application_users['display_name']); ?>
        </option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="col-lg-6"  >
 <div  class="mt-2"  >
  <span class="col-form-label">
   <?php echo __('Designation'); ?>
 </span>
 <select aria-label="<?php echo __('Designation'); ?>" class ="form-select js-example-basic-single" name="designation_id">
  <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
  <?php foreach ($designations_details as $desigs) {
   ?>
   <option value="<?php echo $desigs['id']; ?>" >
    <?php echo __($desigs['designation_name']); ?>
  </option>
<?php } ?>
</select>
</div>
</div>

<div class="col-lg-6" >
 <div  class="mt-2"  >
  <span class="col-form-label"><?php echo __('Vertical'); ?> </span>
  <select aria-label="<?php echo __('Vertical'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
    <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
    <?php foreach ($verticals_details as $verticals) {
     ?>
     <option value="<?php echo $verticals['id']; ?>" >
      <?php echo __($verticals['vertical_name']); ?>
    </option>
  <?php } ?>
</select>
</div>
</div>

<div class="col-lg-6" >
 <div  class="mt-2" >
  <span class="col-form-label">
    <?php echo __('Hiring Source'); ?> </span>
  <div class="input-group input-icon-right">
   <input aria-label="<?php echo __('Enter Hiring Source'); ?>"  class="form-control form-control-lg text-capitalize " type="text" placeholder="<?php echo __('Enter Hiring Source'); ?>" name="hiring_source" value=""/>
 </div>
</div>
</div>

<div class="col-lg-6" >
 <div  class="mt-2"  >
  <span class="col-form-label"><?php echo __('Role'); ?></span> </br>
  <select aria-label="<?php echo __('Role'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
    <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
    <?php foreach ($employment_roles_details as $employment_roles) {

     ?>
     <option value="<?php echo $employment_roles['id']; ?>" >
      <?php echo __($employment_roles['role_name']); ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<?php
$text_color_eco= $data['experience_current_organization'] != $empdata['experience_current_organization'] ? 'text-danger':"";
$diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ;
$display_div_eco = $field_set['EXPERIENCE_IN_CURRENT_ORGANIZATION']['display'] != 1 ? 'none' : '';

?>
<?php
$diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ;
?>
<div class="col-lg-6" >
 <div class="mt-2" >
  <span class="col-form-label"><?php echo __('Experience in Current Organization'); ?></span>
   <div class="input-group input-group-button  ">
     <input aria-label="<?php echo __('Enter Experience in Current Organization'); ?>" type="text" class="form-control form-control-lg width1 "  placeholder="<?php echo __('Enter Experience in Current Organization'); ?>" name="experience_current_organization" value=""/>
     <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>
   </div>

 </div>
</div>

<div class="col-lg-6"  >
 <div  class="mt-2"  >
  <span class="col-form-label"><?php echo __('Total Experience Year'); ?>  </span>
  <div class="input-group input-group-button ">
   <input aria-label="<?php echo __('Enter Total Experience'); ?>" type="text" class="form-control  width1 form-control-lg "  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
   <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>

 </div>
</div>
</div>

<div class="col-lg-6"  >
 <div  class="mt-2"  >
  <span class="col-form-label"><?php echo __('Relevant Experience Year'); ?>  </span>
    <div class="input-group input-group-button  ">
     <input aria-label="<?php echo __('Enter Total Experience'); ?>" type="text" class="form-control form-control-lg width1  "  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="relevant_experience" value=""/>
     <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>
   </div>
 </div>
</div>

<div class="col-lg-6" >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Notice Period'); ?> </span>
  <div class="input-group input-group-button ">
   <input aria-label="<?php echo __('Enter Notice Period'); ?>" type="text" class="form-control form-control-sm width1  "  placeholder="<?php echo __('Enter Notice Period'); ?>" name="notice_period" value="" />
   <span class="input-group-text addon-style"><span class=""><?php echo __('Months'); ?></span></span>
 </div>
</div>
</div>

<div class="col-lg-6" >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Band'); ?> </span>
  <select aria-label="<?php echo __('Band'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="band_id" onchange="getBandid(this.value)">
    <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
    <?php foreach ($employment_bands_details as $employment_bands) {

     ?>
     <option value="<?php echo $employment_bands['id']; ?>" >
      <?php echo __($employment_bands['band_name']); ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<div class="col-lg-6 w-full" >
 <div  class="mt-2"  >
  <label class="col-form-label"><?php echo __('Level'); ?>
    <select aria-label="<?php echo __('Level'); ?>"  class ="form-control form-control-lg  form-select js-example-basic-single" name="level_id" id="employeement_level">
      <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
  </select>
</div>
</div>
<div class="col-lg-6" >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Work Location'); ?></span>
<select aria-label="<?php echo __('Location'); ?>"  class ="form-control form-control-lg form-select js-example-basic-single"  name="location_id">
  <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
  <?php foreach ($locations_details as $locations) {
   if($locations['parent_location_id'] =='') {?>
     <option value="<?php echo $locations['id']; ?>" >
      <?php echo __($locations['location_name']); ?>
    </option>
  <?php } ?>
  <?php } ?>
</select>
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
<div class="col-lg-6 "  >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Employement Type'); ?> </span>
    <div class="form-radio mt-1">
     <?php
     foreach ($emptypes_details as $emptypes) {
      ?>
             <div class="radio radio-inline">
               <label class="form-label <?php echo $text_color_ety; ?>">
                 <input aria-label="<?php echo __('Employee Type'); ?>"  type="radio" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php
} ?> value="<?php echo $emptypes['genc_detail_code'];?>" style="display:none" />
                 <i class="helper"></i><?php echo __($emptypes['genc_detail_name']);?> </label>
               </div>

             <?php } ?>
           </div>
         </div>
       </div>
       <div class="col-lg-6" >
         <div  class="mt-2"  >
          <span class="col-form-label"><?php echo __('Billable'); ?> </span>
            <div class="form-radio">
              <div class="radio radio-inline">
               <label class="form-label <?php echo $text_color_ebi; ?>">
                 <input aria-label="<?php echo __('Billable'); ?>"  type="radio" name="billable" checked="checked"  value="0" style="display:none" />
                 <i class="helper"></i><?php echo __('Yes'); ?> </label>
               </div>
               <div class="radio radio-inline">
                 <label class="form-label <?php echo $text_color_ebi; ?>">
                   <input aria-label="<?php echo __('Billable'); ?>"  type="radio" name="billable"  value="1"  style="display:none"/>
                   <i class="helper"></i><?php echo __('No'); ?> </label>
                 </div>
               </div>
             </div>
           </div>

           <div class="col-lg-4" >
             <div  class="mt-2"  >
              <span class="col-form-label">
               <?php echo __('Office Phone'); ?>

             </span>
             <div class="input-group input-icon-right">
               <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_ofph; ?>" name="office_phone" placeholder="<?php echo __('Enter Office Phone'); ?>" value=""/>

             </div>
           </div>
         </div>
         <div class="col-lg-4" >
           <div  class="mt-2" >
            <span class="col-form-label">
             <?php echo __('Extension'); ?>
           </span>
           <div class="input-group input-icon-right">
             <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_ext; ?>" name="office_ext" placeholder="<?php echo __('Enter Extension'); ?>" value=""/>

           </div>
         </div>
       </div>

       <div class="col-xl-4" >
         <div class="mt-2" >
          <span class="col-form-label"><?php echo __('Fax'); ?> </span>
          <div class="input-group input-icon-right">
           <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  <?php echo $text_color_fx; ?>" type="text" placeholder="<?php echo __('Enter Fax Number'); ?>" name="fax" value=""/>
         </div>
       </div>
     </div>
     
          <div class="row">
            <input type="hidden" name="tmp_id" value="<?php echo $temp_id; ?>">
            <div class="form-group col-lg-12">
              <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="emergetable">
                <tr>
                  <th scope="col" style="width: 2%;"></th>

                  <th scope="col" class="thheader" style="width: 25%;"><?php echo __('Name'); ?></th>
                  <th scope="col" class="" style="width: 25%;"><?php echo __('Relationship'); ?></th>
                  <th scope="col" class="thheader" style="width: 25%;"><?php echo __('Home No'); ?></th>
                  <th scope="col" class="thheader" style="width: 25%;"><?php echo __('Mobile No'); ?></th>

                </tr>
                <?php  if(count((array)$emerge_details)=='0'){ $emerge_details[] = array('contact_name'=>"");
}
if(count((array)$emerge_details)>0){
                 foreach ($emerge_details as $eno) { ?>
                  <tr>
                    <td>


                      <div class="checkbox-fade fade-in-primary">
                        <label class="form-label"><input aria-label="<?php echo __(LBL_CHECKBOX); ?>" class="form-control itemRow" type="checkbox" style="display:none"> <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span> </label>

                      </div>

                    </td>
                    <input type="hidden" name="contact_rowid[]" class="form-control " value="">

                   <td>
                    <input aria-label="<?php echo __(LBL_ENTER_CONTACT_NAME); ?>" type="text" name="contact_name[]" class="form-control text-capitalize" value="" autocomplete="off" placeholder="<?php echo __(LBL_ENTER_CONTACT_NAME); ?>">
                  </td>
                  <td>
                    <select aria-label="<?php echo __('Contact Relationship[]'); ?>" class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                      <option value=""> <?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                      <?php foreach ($relatypedata as  $reladata) {   ?>
                        <option  value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata[KEY_RELATION_NAME]); ?></option>
                      <?php   } ?>
                    </select>
                  </td>
                  <td><input aria-label="<?php echo __('Enter Home Number'); ?>" type="number" name="contact_home_no[]" class="form-control " value="" placeholder="<?php echo __('Enter Home Number'); ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Enter Mobile Number'); ?>" type="number" name="contact_mobile_no[]" class="form-control " value="" autocomplete="off" placeholder="<?php echo __('Enter Mobile Number'); ?>">  </td>

                </tr>
              <?php } ?>
              <?php } ?>
            </table>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button class="btn btn-danger" id="removeRows_eme" onclick="removeemerows()"  type="button">- <?php echo __('Delete'); ?></button> &nbsp;
                <button class="btn btn-success "  onclick="addemrgerows()" id="addRows_eme" type="button">+ <?php echo __('Add More'); ?></button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </fieldset>
<h3> <?php echo __('Proof Details'); ?> </h3>
        <fieldset>
            <div class="pb-5" data-wizard-type="step-content">
            <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Proof Details'); ?></h4>
                <div class="row">
                    <div >
                     <div class="card-body">
                        <div class="form-group row">
                         <table class="table table-vertical-center">
                           <thead class="thead-light">
                              <tr>
                                 <th class="text-center" scope="col">#</th>
                                 <th class="text-center" scope="col"><?php echo __('Type'); ?></th>
                                  <th class="text-center"  scope="col"><?php echo __('Number'); ?></th>
                                 <th class="text-center" scope="col"><?php echo __('Issue Date'); ?></th>
                                 <th  class="text-center" scope="col"><?php echo __('Expiry Date'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td>1</td>
                                 <td>
                                     <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('Adhar Card'); ?>">
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Adhar No'); ?>" type="text" name="adhar_no" class="form-control" value="<?php echo dynamicDataDecode($data['adhar_no']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Adhar Issue Date'); ?>" type="text" name="adhar_issue_date" class="form-control datepicker" value="<?php echo $data['adhar_issue_date']!="0000-00-00"?$data['adhar_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo ""; ?>" >
                                 </td>
                              </tr>
                              <tr>
                                 <td>2</td>
                                 <td>
                                       <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('Passport'); ?>" disabled>
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Passport'); ?>" type="text" name="passport" class="form-control" value="<?php echo dynamicDataDecode($data['passport']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Passport Issue Date'); ?>" type="text" name="passport_issue_date" class="form-control datepicker" value="<?php echo $data['passport_issue_date']!="0000-00-00"?$data['passport_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Passport Expiry Date'); ?>" type="text" name="passport_expiry_date" class="form-control datepicker" value="<?php echo $data['passport_expiry_date']!="0000-00-00"?$data['passport_expiry_date']:""; ?>" >
                                 </td>
                              </tr>

                              <tr>
                                 <td>3</td>
                                 <td>
                                       <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('Driving'); ?>">
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Driving Licence'); ?>" type="text" name="driving_licence" class="form-control" value="<?php echo dynamicDataDecode($data['driving_licence']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Driving Licence Issue Date'); ?>" type="text" name="driving_licence_issue_date" class="form-control datepicker" value="<?php echo $data['driving_licence_issue_date']!="0000-00-00"?$data['driving_licence_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Driving Licence Expiry Date'); ?>" type="text" name="driving_licence_expiry_date" class="form-control datepicker" value="<?php echo $data['driving_licence_expiry_date']!="0000-00-00"?$data['driving_licence_expiry_date']:""; ?>" >
                                 </td>
                              </tr>
                              <tr>
                                 <td>4</td>
                                 <td>
                                       <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('Pan Number'); ?>">
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pan Number'); ?>" type="text" name="pan_number" class="form-control" value="<?php echo dynamicDataDecode($data['pan_number']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pan Issue Date'); ?>" type="text" name="pan_issue_date" class="form-control datepicker" value="<?php echo $data['pan_issue_date']!="0000-00-00"?$data['pan_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Input field'); ?>" type="text"  disabled name="" class="form-control" >
                                 </td>
                              </tr>
                              <tr>
                                 <td>5</td>
                                 <td>
                                       <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('PF Number'); ?>">
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pf No'); ?>" type="text" name="pf_no" class="form-control" value="<?php echo dynamicDataDecode($data['pf_no']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pf Issue Date'); ?>" type="text" name="pf_issue_date" class="form-control datepicker" value="<?php echo $data['pf_issue_date']!="0000-00-00"?$data['pf_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Input field'); ?>" type="text" disabled name="" class="form-control" disabled>
                                 </td>
                              </tr>
                               <tr>
                                 <td>6</td>
                                 <td>
                                       <input aria-label="<?php echo __('Input field'); ?>" type="text" name="" disabled class="form-control" value="<?php echo __('UAN Number'); ?>">
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pf Uan'); ?>" type="text" name="pf_uan" class="form-control" value="<?php echo dynamicDataDecode($data['pf_uan']); ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Pf Uan Issue Date'); ?>" type="text" name="pf_uan_issue_date" class="form-control datepicker" value="<?php echo $data['pf_uan_issue_date']!="0000-00-00"?$data['  pf_uan_issue_date']:""; ?>" >
                                 </td>
                                 <td>
                                    <input aria-label="<?php echo __('Input field'); ?>" type="text" disabled name="" class="form-control" disabled>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                      </div>
                   </div>
                </div>
            </div>
        </fieldset>

    <h3> <?php echo __('Employee Buddy'); ?> </h3>
        <fieldset>
            <div class="pb-5" data-wizard-type="step-content">
                <div class="row">
                    <div class="col-lg-6 position-relative">
                       <div class="mt-2">
                    <span class="col-form-label"><?php echo __('Admin Buddy'); ?>  <span class="text-danger">*</span></span></br>
                    <select aria-label="<?php echo __('Admin Buddy'); ?>"  class ="form-select js-example-basic-single " name="admin_buddy">
                        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                        <?php foreach ($admin_buddy_list as $admin_buddy_res) { ?>
                         <option value="<?php echo $admin_buddy_res['emp_id']; ?>" >
                          <?php echo __($admin_buddy_res['display_name']); ?>
                        </option>
                      <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                       <div class="mt-2">
                    <span class="col-form-label"><?php echo __('HR Buddy'); ?>  <span class="text-danger">*</span></span></br>
                    <select aria-label="<?php echo __('Hr Buddy'); ?>"  class ="form-select js-example-basic-single " name="hr_buddy">
                        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                        <?php foreach ($hr_buddy_list as $hr_buddy_res) { ?>
                         <option value="<?php echo $hr_buddy_res['emp_id']; ?>" >
                          <?php echo __($hr_buddy_res['display_name']); ?>
                        </option>
                      <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                       <div class="mt-2">
                    <span class="col-form-label"><?php echo __('Finance Buddy'); ?>  <span class="text-danger">*</span></span></br>
                    <select aria-label="<?php echo __('Finance Buddy'); ?>"  class ="form-select js-example-basic-single " name="finance_buddy">
                        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                        <?php foreach ($finance_buddy_list as $finance_buddy_res) { ?>
                         <option value="<?php echo $finance_buddy_res['emp_id']; ?>" >
                          <?php echo __($finance_buddy_res['display_name']); ?>
                        </option>
                      <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                       <div class="mt-2">
                    <span class="col-form-label"><?php echo __('Project Buddy'); ?>  <span class="text-danger">*</span></span></br>
                    <select aria-label="<?php echo __('Project Buddy'); ?>"  class ="form-select js-example-basic-single " name="project_buddy">
                        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                        <?php foreach ($project_buddy_list as $project_buddy_res) { ?>
                         <option value="<?php echo $project_buddy_res['emp_id']; ?>" >
                          <?php echo __($project_buddy_res['display_name']); ?>
                        </option>
                      <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                       <div class="mt-2">
                    <span class="col-form-label"><?php echo __('Location Buddy'); ?>  <span class="text-danger">*</span></span></br>
                    <select aria-label="<?php echo __('Loc Buddy'); ?>"  class ="form-select js-example-basic-single " name="loc_buddy">
                        <option value=""><?php echo __(LBL_SELECT_ANY_ONE); ?></option>
                        <?php foreach ($location_buddy_list as $location_buddy_res) { ?>
                         <option value="<?php echo $location_buddy_res['emp_id']; ?>" >
                          <?php echo __($location_buddy_res['display_name']); ?>
                        </option>
                      <?php } ?>
                        </select>
                      </div>
                    </div>

                </div>
            </div>
        </fieldset>


  </form>   </section>
</div>
</div>
</div>
</div>
</div> </div>
<!-- Verticle Wizard card end -->

</section></div></div></div></div>

</div>
</div>
<!--end::Container-->
