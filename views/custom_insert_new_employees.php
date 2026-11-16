<?php
$company_id = $_SESSION['company_id'];
$emp_role_id = $_SESSION['role_id'];
$role_ids = json_decode($emp_role_id);
$emp_role_id = implode(',', (array)$role_ids);
$emp_loction_id = $_SESSION['employee']['location_id'];
$table_name = TBL_EMPLOYEES;
$url = API."?mode=getemployeeinfoforupdation&company_id=".$company_id."&employee_id=".$employee_id."&location_id=".$emp_loction_id."&role_id=".$emp_role_id."&is_active=0&application_tablename=".$table_name;
$response = callAPI($url);
$employee_basic_details = json_decode($response,1);
$hrconnect_id  = $employee_basic_details['hr_connect']['hr_connect_id'];
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
/* Employee Details Start */
$url = API."?mode=employeedetails&emp_id=".$employee_id."&company_id=".$company_id;
$response = callAPI($url);
$maindata = json_decode($response,1);
$empdata  = $maindata['emp_data'];
$location_id = $empdata['location_id'];
if(empty($tempdata)) {
  $data = $maindata['emp_data']; // Employees table
} else {
  $data = $tempdata; //Application_temp table
}
if(empty($tempdata)){
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

?>
<script type="text/javascript">
  function createuser(emp_id){
    jQuery.ajax({
     url: "ajax.php",
     data:'page=createuser&emp_id='+emp_id,
     type: "POST",
     success:function(data){
       console.log(data);
       var msg = <?php echo "'".$_SESSION['error_message']['9026']."'" ?>;
       swal.fire({
        title: msg,
        type: "success",
        buttonsStyling: false,
        confirmButtonText: "<i class='la la-hand-o-up'></i><?= htmlspecialchars(__('OK'), ENT_QUOTES, 'UTF-8'); ?>",
        confirmButtonClass: "btn btn-info",
        showCancelButton: false,
        cancelButtonText: "<i class='la la-hand-o-right'></i> <?= htmlspecialchars(__('No, continue'), ENT_QUOTES, 'UTF-8'); ?>",
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
   if(value=='M') {
     document.getElementById('marriagedateshow').style.display ='block';
   }
   if(value=='S') {
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Depandent Name'); ?>" type="text" name="dependent_name[]" placeholder="<?php echo __('Enter Depandent Name'); ?> "  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php
} ?></select>  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Date Of Birth'); ?>" type="text" name="dependent_date_of_birth[]"  class="form-control datepicker"   autocomplete="off" placeholder="<?php echo __('Date Of Birth'); ?>">  </td>';
   htmlRows += '</tr>';
   $('#deptable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';


   htmlRows += '<td><input aria-label="<?php echo __('Enter Contact Name'); ?>" type="text" name="contact_name[]"  class="form-control " placeholder="<?php echo __('Enter Contact Name'); ?>"  autocomplete="off"> </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Contact Relationship[]'); ?>" class="form-control form-select js-example-basic-single" name="contact_relationship[]" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php
} ?></select>    </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Home Number'); ?>" type="text" name="contact_home_no[]" placeholder="<?php echo __('Home Number'); ?>"  class="form-control "  autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Mobile Number'); ?>" type="text" name="contact_mobile_no[]"  class="form-control "  placeholder="<?php echo __('Mobile Number'); ?>"  autocomplete="off">  </td>';

   htmlRows += '</tr>';
   $('#emergetable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Company Name'); ?>" type="text" name="company_name[]"  placeholder="<?php echo __('Enter Company Name'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Enter Job Description'); ?>" type="text" name="job_descrption[]"  placeholder="<?php echo __('Enter Job Description'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('From Date'); ?>" type="text" name="work_from[]"  placeholder="<?php echo __('From Date'); ?>" class="form-control datepicker"   autocomplete="off"> <input aria-label="<?php echo __('To Date'); ?>" type="text" name="work_to[]"  placeholder="<?php echo __('To Date'); ?>" class="form-control datepicker mt-2" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#extable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
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
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Enter Instistute name'); ?>" type="text" name="institute_name[]"  placeholder="<?php echo __('Enter Instistute name'); ?>" class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Education Type[]'); ?>" class="form-control form-select js-example-basic-single" name="education_type[]" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option> <?php
} ?></select> </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Start Date'); ?>" type="text" name="start_date[]" placeholder="<?php echo __('Start Date'); ?>"  class="form-control datepicker"  autocomplete="off"> <input aria-label="<?php echo __('End Date'); ?>" type="text" name="end_date[]"  class="form-control datepicker mt-2"   autocomplete="off" placeholder="<?php echo __('End Date'); ?>">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Score'); ?>" type="text" name="score[]"  class="form-control " placeholder ="<?= htmlspecialchars(__('Score'), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#edutable').append(htmlRows);
   $(".js-example-basic-single").select2();
   $(".js-example-disabled-results").select2();
   $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
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
    $("#ageinyear").val(age);
}

</script>
<style>

  .wizard > .steps > ul > li {
   width: 14%;
 }

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


</style>
<div class="d-flex flex-column-fluid ">
 <!--begin::Container-->
 <div class="container-fluid">
  <?php
  if($hrconnect_id==0 || $hrconnect_id=="") { ?>


       <div class="alert alert-primary alert-dismissible">
        <strong><?php echo "".__('HR connect is not configured.')."  <strong>".__('Workflow need to be started!')."</strong>"; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>">
        </button>
      </div>

    <?php } else { ?>


       <div class="alert alert-primary alert-dismissible">
        <strong><?php echo "This function is used for maintaining <strong>".__('Display Attributes')."</strong>".__('. * represents mandatory fields.').""; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>">
        </button>
      </div>
    <?php } ?>

     <div class="card">
      <div class="card-header  ">
       <h5><?php echo __('Employee'); ?></h5>


                        <div class="checkbox-fade fade-in-primary float-right">
                          <label class="form-label"><input aria-label="<?php echo __('Create User'); ?>" class="form-control" id="create_user" type="checkbox" value="0"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span><?php echo __('Create User'); ?>  </label>

                        </div>
    </div>
    <div class="card-block j-pro">
     <div class="row ">
       <div class="col-md-12">
         <div id="wizardb">
           <section>
            <!-- Verticle Wizard card start -->
            <div class="card">
              <div class="card-block">
                <div class="row">
                  <div class="col-md-12">
                    <div id="wizardb_2">
                      <section>

                       <form class="wizard-form" id="example-advanced-create-employee"  enctype="multipart/form-data" method="post">

                      <h3> <?php echo __('Basic Info'); ?>  </h3>
                      <fieldset>
                       <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                           <?php

                           $text_color_ec= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                           $display_div_ec = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                           $text_color_uc= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                           $display_div_uc = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                           ?>
                           <div class="row">
                            <div class="col-xl-6" style="display: <?php echo $display_div_ec; ?>" >
                             <div class="mt-2" style="display: <?php echo $display_div_ec; ?>">
                              <span class="col-form-label"><?php echo __('Employee Code'); ?> </span>
                            <div class="input-group input-icon-right">
                             <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  required   <?php echo $text_color_ec; ?>" type="text" placeholder="<?php echo __('Enter Employee Code'); ?>" name="emp_code" value="<?php echo $data['emp_code']; ?>" />
                           </div>
                         </div>

                       </div>
                       <div class="col-xl-6" style="display: <?php echo $display_div_uc; ?>" >
                         <div class="mt-2" style="display: <?php echo $display_div_uc; ?>" >
                          <span class="col-form-label"><?php echo __('User Name'); ?></span>
                          <div class="input-group input-icon-right">
                           <input aria-label="<?php echo __('Input field'); ?>" class="form-control form-control-lg  required  <?php echo $text_color_uc; ?>" type="text" name="display_name" placeholder="<?php echo __('Enter User Name'); ?> " value="<?php echo $data['display_name']; ?>" />
                         </div>
                       </div>
                     </div>

                     <?php
                     $text_color_fn= $data['first_name'] != $empdata['first_name'] ? 'text-danger':"";
                     $display_div_fn = $field_set['FIRST_NAME']['display'] != 1 ? 'none' : '';
                     $text_color_ln= $data['last_name'] != $empdata['last_name'] ? 'text-danger':"";
                     $display_div_ln = $field_set['LAST_NAME']['display'] != 1 ? 'none' : '';
                     $text_color_mn= $data['middle_name'] != $empdata['middle_name'] ? 'text-danger':"";
                     $display_div_mn = $field_set['MIDDLE_NAME']['display'] != 1 ? 'none' : '';
                     ?>
                     <div class="col-xl-4" style="display: <?php echo $display_div_fn; ?>">
                       <div class="mt-2" style="display: <?php echo $display_div_fn; ?>" >
                        <span class="col-form-label"><?php echo __('First Name'); ?> </span>
                        <div class="input-group input-icon-right">
                         <input aria-label="<?php echo __('Input field'); ?>" class="form-control required  form-control-lg  <?php echo $text_color_fn; ?>" type="text" placeholder="<?php echo __('Enter First Name'); ?>" name="first_name" value=""/>
                       </div>
                     </div>
                   </div>
                   <div class="col-xl-4" style="display: <?php echo $display_div_mn; ?>">
                     <div class="mt-2" style="display: <?php echo $display_div_mn; ?>" >
                      <span class="col-form-label"><?php echo __('Middle Name'); ?> </span>
                      <div class="input-group input-icon-right">
                       <input aria-label="<?php echo __('Input field'); ?>" class="form-control   form-control-lg  <?php echo $text_color_mn; ?>" type="text" placeholder="<?php echo __('Enter Middle Name'); ?>" name="middle_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-4" style="display: <?php echo $display_div_ln; ?>">
                   <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
                    <span class="col-form-label"><?php echo __('Last Name'); ?></span>
                      <div class="input-group input-icon-right">
                       <input aria-label="<?php echo __('Input field'); ?>" class="form-control form-control-lg required  <?php echo $text_color_ln; ?>" type="text" placeholder="<?php echo __('Enter last Name'); ?>" name="last_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-6" style="display: <?php echo $display_div_email; ?>" >
                   <?php
                   $text_color_email= $data['email_id'] != $empdata['email_id'] ? 'text-danger':"";
                   $display_div_email = $field_set['EMAIL']['display'] != 1 ? 'none' : '';
                   ?>
                   <div class="mt-2" style="display: <?php echo $display_div_email; ?>" >
                    <span class="col-form-label"><?php echo __('Email'); ?> </span>
                    <div class="input-group input-icon-right">
                     <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  required  <?php echo $text_color_email; ?>" type="text" placeholder="<?php echo __('Enter Your Email Address'); ?>" name="email_id" value="" />
                   </div>
                 </div>
               </div>
               <?php
               $text_color_ph= $data['phone'] != $empdata['phone'] ? 'text-danger':"";
               $display_div_ph = $field_set['CONTACT']['display'] != 1 ? 'none' : '';

               ?>
               <div class="col-xl-6" style="display: <?php echo $display_div_ph; ?>" >
                 <div class="mt-2" style="display: <?php echo $display_div_ph; ?>" >
                  <span class="col-form-label"><?php echo __('Contact'); ?></span>
                  <div class="input-group input-icon-right">
                   <input aria-label="<?php echo __('Input field'); ?>" class="form-control form-control-lg  <?php echo $text_color_ph; ?>" type="number" placeholder="<?php echo __('Enter Contact Number'); ?>" name="phone" value=""/>
                 </div>
               </div>
             </div>

             <?php
             $text_color_adr= $data['address'] != $empdata['address'] ? 'text-danger':"";
             $display_div_adr = $field_set['ADDRESS']['display'] != 1 ? 'none' : '';
             ?>
             <div class="col-xl-6" style="display: <?php echo $display_div_adr; ?>">
               <div class="mt-2" style="display: <?php echo $display_div_adr; ?>" >
                <span class="col-form-label"><?php echo __('Address'); ?></span>
                  <textarea aria-label="<?php echo __('Text area'); ?>"  style="overflow:auto" class=" form-control form-control-lg <?php echo $text_color_adr; ?>" rows="5" cols="100" placeholder="<?php echo __('Enter Your Address'); ?>" name="address"></textarea>
                </div>
                <?php
                $text_color_pc= $data['post_code'] != $empdata['post_code'] ? 'text-danger':"";
                $display_div_pc = $field_set['POSTCODE']['display'] != 1 ? 'none' : '';
                ?>
              </div>
              <div class="col-xl-6" style="display: <?php echo $display_div_pc; ?>">
               <div class="mt-2" style="display: <?php echo $display_div_pc; ?>" >
                <span class="col-form-label"><?php echo __('Post Code'); ?> </span>
                <div class="input-group">

                 <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control form-control-lg  <?php echo $text_color_pc; ?> " placeholder="<?php echo __('Enter Your Post-Code'); ?>" name="post_code" value=""/>
               </div>
             </div>
           </div>
         </div>

       </div>
     </fieldset>

     <h3> <?php echo __('Work'); ?>  </h3>
     <fieldset>

      <div class="pb-5" data-wizard-type="step-content">

        <div class="row">

          <?php
          $text_color_dj= fromDate($data['joining_date']) != fromDate($empdata['joining_date']) ? 'text-danger':"";
          $display_div_dj = $field_set['DATE_OF_JOINING']['display'] != 1 ? 'none' : '';
          ?>
          <div class="col-lg-6" style="display: <?php echo $display_div_dj; ?>" >
           <div  class="mt-2" style="display: <?php echo $display_div_dj; ?>" >
            <span class="col-form-label"><?php echo __('Date of Joining'); ?> </span>
            <div class="">
             <?php $date_pic = fromDate($data['joining_date']); ?>
             <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control datepicker form-control-lg  <?php echo $text_color_dj; ?>"  value=""  name="joining_date"  />
           </div>
         </div>
       </div>

       <?php
       $text_color_dep= $data['department_id'] != $empdata['department_id'] ? 'text-danger':"";
       if($empdata['department_id']==0){
        $text_color_dep = "";
      }
      $display_div_dep = $field_set['DEPARTMENT']['display'] != 1 ? 'none' : '';
      ?>

      <div class="col-lg-6" style="display: <?php echo $display_div_dep; ?>">
       <div  class="mt-2" style="display: <?php echo $display_div_dep; ?>" >
        <span class="col-form-label"><?php echo __('Department'); ?></span>
          <select aria-label="<?php echo __('Department'); ?>" class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
            <option value=""><?php echo __('Select Any One'); ?></option>
            <?php foreach ($dept_details as $departments) {
              ?>
              <option  value="<?php echo $departments['id']; ?>" >
                <?php echo $departments['department_name'];
                $emp_department[$departments['id']] = $departments['department_name'];
                ?>
              </option>
              <?php
              if($empdata['department_id'] == $departments['id']) {
               $empdata['department_name'] = $departments['department_name'];
             }
           } ?>
         </select>
       </div>
     </div>
     <?php
     $text_color_rto= $data['reporting_manager_id'] != $empdata['reporting_manager_id'] ? 'text-danger':"";
     if($empdata['reporting_manager_id']==0){
      $text_color_rto = "";
    }
    $display_div_rto = $field_set['REPOTING_TO']['display'] != 1 ? 'none' : '';

    ?>
    <div class="col-lg-6" style="display: <?php echo $display_div_rto; ?>" >
     <div  class="mt-2" style="display: <?php echo $display_div_rto; ?>" >
      <span class="col-form-label"><?php echo __('Repoting to'); ?> </span>
      <select aria-label="<?php echo __('Reporting Manager'); ?>"  class ="form-control form-control-lg  form-select js-example-basic-single" name="reporting_manager_id">
        <option value=""><?php echo __('Select Any One'); ?></option>

        <?php foreach ($application_users_details as $application_users) {
          if($empdata['reporting_manager_id'] == $application_users['id']) {
           $empdata['display_name'] = $application_users['display_name'];
         }

         ?>
         <option value="<?php echo $application_users['id']; ?>" >
          <?php echo $application_users['display_name']; ?>
        </option>
      <?php } ?>
    </select>
  </div>
</div>

<?php

$text_color_des= $data['designation_id'] != $empdata['designation_id'] ? 'text-danger':"";
if($empdata['designation_id']==0){
  $text_color_des = "";
}
$display_div_des = $field_set['DESIGNATION']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6"  style="display: <?php echo $display_div_des; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_des; ?>" >
  <span class="col-form-label">
   <?php echo __('Designation'); ?>
 </span>
 <select aria-label="<?php echo __('Designation'); ?>" class ="form-select js-example-basic-single" name="designation_id">
  <option value=""><?php echo __('Select Any One'); ?></option>
  <?php foreach ($designations_details as $desigs) {
    if($empdata['designation_id'] == $desigs['id']) {
     $empdata['designation_name'] = $desigs['designation_name'];
   }

   ?>
   <option value="<?php echo $desigs['id']; ?>" >
    <?php echo $desigs['designation_name']; ?>
  </option>
<?php } ?>
</select>
</div>
</div>

<?php
$text_color_ver= $data['vertical_id'] != $empdata['vertical_id'] ? 'text-danger':"";
if($empdata['vertical_id']==0){
  $text_color_ver = "";
}
$display_div_ver = $field_set['VERTICAL']['display'] != 1 ? 'none' : '';

?>
<div class="col-lg-6" style="display: <?php echo $display_div_ver; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_ver; ?>" >
  <span class="col-form-label"><?php echo __('Vertical'); ?> </span>
  <select aria-label="<?php echo __('Vertical'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
    <option value=""><?php echo __('Select Any One'); ?></option>
    <?php foreach ($verticals_details as $verticals) {
      if($empdata['vertical_id'] == $verticals['id']) {
       $empdata['vertical_name'] = $verticals['vertical_name'];
     }
     ?>
     <option value="<?php echo $verticals['id']; ?>" >
      <?php echo $verticals['vertical_name']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<?php
$text_color_hs= $data['hiring_source'] != $empdata['hiring_source'] ? 'text-danger':"";
$display_div_hs = $field_set['HIRING_SOURCE']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6" style="display: <?php echo $display_div_hs; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_hs; ?>">
  <span class="col-form-label">
    <?php echo __('Hiring Source'); ?> </span>
  <div class="input-group input-icon-right">
   <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  <?php echo $text_color_hs; ?>" type="text" placeholder="<?php echo __('Enter Hiring Source'); ?>" name="hiring_source" value=""/>
 </div>
</div>
</div>
<?php

$text_color_role= $data['role_id'] != $empdata['role_id'] ? 'text-danger':"";
if($empdata['role_id']==0){
  $text_color_role = "";
}
$display_div_role = $field_set['ROLE']['display'] != 1 ? 'none' : '';

?>

<div class="col-lg-6" style="display: <?php echo $display_div_role; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_role; ?>" >
  <span class="col-form-label"><?php echo __('Role'); ?></span> </br>
  <select aria-label="<?php echo __('Role'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
    <option value=""><?php echo __('Select Any One'); ?></option>
    <?php foreach ($employment_roles_details as $employment_roles) {
      if($empdata['role_id'] == $employment_roles['id']) {
       $empdata['role_name'] = $employment_roles['role_name'];
     }
     ?>
     <option value="<?php echo $employment_roles['id']; ?>" >
      <?php echo $employment_roles['role_name']; ?>
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
<div class="col-lg-6" style="display: <?php echo $display_div_eco; ?>">
 <div class="mt-2" style="display: <?php echo $display_div_eco; ?>" >
  <span class="col-form-label"><?php echo __('Experience in Current Organization'); ?></span>
   <div class="input-group input-group-button  ">
     <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control form-control-lg width1  <?php echo $text_color_eco; ?>"  placeholder="<?php echo __('Enter Experience in Current Organization'); ?>" name="experience_current_organization" value=""/>
     <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>
   </div>

 </div>
</div>
<?php
$text_color_te= $data['total_experiance'] != $empdata['total_experiance'] ? 'text-danger':"";
$display_div_te = $field_set['TOTAL_EXPERIENCE']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6"  style="display: <?php echo $display_div_te; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_te; ?>" >
  <span class="col-form-label"><?php echo __('Total Experience Year'); ?>  </span>
  <div class="input-group input-group-button ">
   <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control  width1 form-control-lg  <?php echo $text_color_te; ?>"  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
   <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>

 </div>
</div>
</div>
<?php
$text_color_re= $data['relevant_experience'] != $empdata['relevant_experience'] ? 'text-danger':"";
$display_div_re = $field_set['RELEVANT_EXPERIENCE']['display'] != 1 ? 'none' : '';
?>

<div class="col-lg-6" style="display: <?php echo $display_div_re; ?>" >
 <div  class="mt-2" style="display: <?php echo $display_div_re; ?>" >
  <span class="col-form-label"><?php echo __('Relevant Experience Year'); ?>  </span>
    <div class="input-group input-group-button  ">
     <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control form-control-lg width1  <?php echo $text_color_re; ?>"  placeholder="<?php echo __('Enter Total Experience'); ?>"  name="relevant_experience" value=""/>
     <span class="input-group-text addon-style"><span class=""><?php echo __('Years'); ?></span></span>
   </div>
 </div>
</div>
<?php

$text_color_np= $data['notice_period'] != $empdata['notice_period'] ? 'text-danger':"";
$display_div_np = $field_set['NOTICE_PERIOD']['display'] != 1 ? 'none' : '';

?>
<div class="col-lg-6" style="display: <?php echo $display_div_np; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_np; ?>">
  <span class="col-form-label"><?php echo __('Notice Period'); ?> </span>
  <div class="input-group input-group-button ">
   <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control form-control-sm width1  <?php echo $text_color_np; ?>"  placeholder="<?php echo __('Enter Notice Period'); ?>" name="notice_period" value="" />
   <span class="input-group-text addon-style"><span class=""><?php echo __('Months'); ?></span></span>
 </div>
</div>
</div>
<?php
$text_color_lv= $data['level_id'] != $empdata['level_id'] ? 'text-danger':"";
if($empdata['level_id']==0){
  $text_color_lv = "";
}
$display_div_lv = $field_set['LEVEL']['display'] != 1 ? 'none' : '';

$text_color_bd = $data['band_id'] != $empdata['band_id'] ? 'text-danger':"";
if($empdata['band_id']==0){
  $text_color_bd = "";
}
$display_div_bd = $field_set['BAND']['display'] != 1 ? 'none' : '';

?>
<div class="col-lg-6" style="display: <?php echo $display_div_bd; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_bd; ?>">
  <span class="col-form-label"><?php echo __('Band'); ?> </span>
  <select aria-label="<?php echo __('Band'); ?>" class ="form-control form-control-lg form-select js-example-basic-single" name="band_id">
    <option value=""><?php echo __('Select Any One'); ?></option>
    <?php foreach ($employment_bands_details as $employment_bands) {
      if($empdata['band_id'] == $employment_bands['id']) {
       $empdata['band_name'] = $employment_bands['band_name'];
     }
     ?>
     <option value="<?php echo $employment_bands['id']; ?>" >
      <?php echo $employment_bands['band_name']; ?>
    </option>
  <?php } ?>
</select>
</div>
</div>
<div class="col-lg-6" style="display: <?php echo $display_div_lv; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_lv; ?>" >
  <span class="col-form-label"><?php echo __('Level'); ?>
    <?php if($text_color_lv!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['levels_name']==''?NODATA:$empdata['levels_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
    <select aria-label="<?php echo __('Level'); ?>"  class ="form-control form-control-lg  form-select js-example-basic-single" name="level_id">
      <option value=""><?php echo __('Select Any One'); ?></option>
      <?php foreach ($emplevels_details as $employment_levels) {
        if($empdata['level_id'] == $employment_levels['id']) {
         $empdata['levels_name'] = $employment_levels['levels_name'];
       }
       ?>
       <option value="<?php echo $employment_levels['id']; ?>" >
        <?php echo $employment_levels['level_name']; ?>
      </option>
    <?php } ?>
  </select>
</div>
</div>
<?php
$text_color_loc= $data['location_id'] != $empdata['location_id'] ? 'text-danger':"";
if($empdata['location_id']==0){
  $text_color_loc = "";
}
$display_div_loc = $field_set['WORK_LOCATION']['display'] != 1 ? 'none' : '';

$text_color_ety= $data['employee_type'] != $empdata['employee_type'] ? 'text-danger':"";
$display_div_ety = $field_set['EMPLOYEMENT_TYPE']['display'] != 1 ? 'none' : '';

$text_color_ebi= $data['billable'] != $empdata['billable'] ? 'text-danger':"";
$display_div_ebi   = $field_set['BILLABLE']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6" style="display: <?php echo $display_div_loc; ?>" >
 <div  class="mt-2" style="display: <?php echo $display_div_loc; ?>" >
  <span class="col-form-label"><?php echo __('Work Location'); ?></span>
<select aria-label="<?php echo __('Location'); ?>"  class ="form-control form-control-lg form-select js-example-basic-single"  name="location_id">
  <option value=""><?php echo __('Select Any One'); ?></option>
  <?php foreach ($locations_details as $locations) {
    if($empdata['location_id'] == $locations['location_id']) {
     $empdata['location_name'] = $locations['location_name'];
   }
   if($locations['parent_location_id'] =='') {?>
     <option value="<?php echo $locations['id']; ?>" >
      <?php echo $locations['location_name']; ?>
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
<div class="col-lg-4 " style="display: <?php echo $display_div_ety; ?>" >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Employement Type'); ?> </span>
    <div class="form-radio mt-1">
     <?php
     foreach ($emptypes_details as $emptypes) {
      ?>
             <div class="radio radio-inline">
               <label class="form-label <?php echo $text_color_ety; ?>">
                 <input aria-label="<?php echo __('Employee Type'); ?>"  type="radio" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php
} ?> value="<?php echo $emptypes['genc_detail_code'];?>" class="d-none" />
                 <i class="helper"></i><?php echo __($emptypes['genc_detail_name']);?> </label>
               </div>

             <?php } ?>
           </div>
         </div>
       </div>
       <div class="col-lg-4" style="display: <?php echo $display_div_ebi; ?>">
         <div  class="mt-2" style="display: <?php echo $display_div_ebi; ?>" >
          <span class="col-form-label"><?php echo __('Billable'); ?> </span>
            <div class="form-radio">
              <div class="radio radio-inline">
               <label class="form-label <?php echo $text_color_ebi; ?>">
                 <input aria-label="<?php echo __('Billable'); ?>"  type="radio" name="billable" checked="checked"  value="0" class="d-none" />
                 <i class="helper"></i><?php echo __('Yes'); ?> </label>
               </div>
               <div class="radio radio-inline">
                 <label class="form-label <?php echo $text_color_ebi; ?>">
                   <input aria-label="<?php echo __('Billable'); ?>"  type="radio" name="billable"  value="1" class="d-none" />
                   <i class="helper"></i><?php echo __('No'); ?> </label>
                 </div>

               </div>
             </div>
           </div>
       <div class="col-lg-4 " style="display: <?php echo $display_div_ety; ?>" >
 <div  class="mt-2" >
  <span class="col-form-label"><?php echo __('Cover Under PF'); ?> </span>
    <div class="form-radio mt-1">
                  <div class="radio radio-inline">
               <label class="form-label">
                 <input aria-label="<?php echo __('Cover Under Pf'); ?>"  type="radio" name="cover_under_pf" checked="checked"  value="0" class="d-none" />
                 <i class="helper"></i><?php echo __('Yes'); ?></label>
               </div>
          <div class="radio radio-inline">
               <label class="form-label">
                 <input aria-label="<?php echo __('Cover Under Pf'); ?>"  type="radio" name="cover_under_pf" value="1" class="d-none" />
                 <i class="helper"></i><?php echo __('No'); ?></label>
               </div>

             </div>
         </div>
       </div>

           <?php
           $text_color_ofph= $data['office_phone'] != $empdata['office_phone'] ? 'text-danger':"";

           $display_div_ofph = $field_set['OFFICE_PHONE']['display'] != 1 ? 'none' : '';

           $text_color_ext= $data['office_ext'] != $empdata['office_ext'] ? 'text-danger':"";

           $display_div_ext = $field_set['EXTENSION']['display'] != 1 ? 'none' : '';

           ?>
           <div class="col-lg-4" style="display: <?php echo $display_div_ofph; ?>">
             <div  class="mt-2" style="display: <?php echo $display_div_ofph; ?>" >
              <span class="col-form-label">
               <?php echo __('Office Phone'); ?>

             </span>
             <div class="input-group input-icon-right">
               <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_ofph; ?>" name="office_phone" placeholder="<?php echo __('Enter Office Phone'); ?>" value=""/>

             </div>
           </div>
         </div>
         <div class="col-lg-4" style="display: <?php echo $display_div_ext; ?>">
           <div  class="mt-2" style="display: <?php echo $display_div_ext; ?>" >
            <span class="col-form-label">
             <?php echo __('Extension'); ?>
           </span>
           <div class="input-group input-icon-right">
             <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_ext; ?>" name="office_ext" placeholder="<?php echo __('Enter Extension'); ?>" value=""/>

           </div>
         </div>
       </div>
       <?php

       $text_color_fx= $data['fax'] != $empdata['fax'] ? 'text-danger':"";
        $display_div_fx = $field_set['FAX']['display'] != 1 ? 'none' : '';
       ?>
       <div class="col-xl-4" style="display: <?php echo $display_div_fx; ?>">
         <div class="mt-2" style="display: <?php echo $display_div_fx; ?>" >
          <span class="col-form-label"><?php echo __('Fax'); ?> </span>
          <div class="input-group input-icon-right">
           <input aria-label="<?php echo __('Input field'); ?>"  class="form-control form-control-lg  <?php echo $text_color_fx; ?>" type="text" placeholder="<?php echo __('Enter Fax Number'); ?>" name="fax" value=""/>
         </div>
       </div>
     </div>
     <!-- end row-->
   </div>

 </div>
</fieldset>
<h3> <?php echo __('Personal'); ?> </h3>
<fieldset>

  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">

   <?php
   $text_color_bir= fromDate($data['date_of_birth']) != fromDate($empdata['date_of_birth']) ? 'text-danger':"";

   $display_div_bir = $field_set['DATE_OF_BIRTH']['display'] != 1 ? 'none' : '';

   ?>
   <div class="row">

    <div class="col-xl-6" style="display: <?php echo $display_div_bir; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
      <span class="col-form-label"><?php echo __('Date of Birth'); ?> </span>
      <div class="">
       <?php $date_pic1 = fromDate($data['date_of_birth']); ?>
       <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg datepicker  <?php echo $text_color_bir; ?>"  value="<?php echo  fromDate(date('Y-m-d')); ?>"  name="date_of_birth"  onchange="checkageemployee(this.value)" />

                                      </div>
                                    </div>

                                  </div>
                                  <?php
                                  $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic1) ;

                                  ?>
                                  <div class="col-xl-6" style="display: <?php echo $display_div_bir; ?>">
                                   <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
                                    <span class="col-form-label"><?php echo __('Age In Years'); ?></span>
                                    <div class="input-group input-icon-right">
                                     <input aria-label="<?php echo __('Age'); ?>" readonly type="text" id="ageinyear" class="form-control form-control-lg " name="age" placeholder="<?php echo __('Age'); ?>" value="0"/>
                                   </div>
                                 </div>
                               </div>
                               <?php
                               $text_color_gen= $data['gender'] != $empdata['gender'] ? 'text-danger':"";

                               $display_div_gen = $field_set['GENDER']['display'] != 1 ? 'none' : '';

                               $text_color_ma= $data['marital_status'] != $empdata['marital_status'] ? 'text-danger':"";

                               $display_div_ma = $field_set['MARITAL_STATUS']['display'] != 1 ? 'none' : '';
                               ?>
                               <?php
                               foreach ($gender_details as $gender_data) {
                                if($empdata['gender']==$gender_data['genc_detail_code']) {
                                 $exist_gender = $gender_data['genc_detail_name'];
                                 break;
                               }
                             }
                             ?>
                             <div class="col-lg-6" style="display: <?php echo $display_div_gen; ?>">
                               <div class="mt-2" style="display: <?php echo $display_div_gen; ?>" >
                                <span class="col-form-label"><?php echo __('Gender'); ?> </span>
                                <div <?php echo $read_only_gen; ?> class="form-radio  ">
                                 <?php
                                 foreach ($gender_details as $gender_data) {
                                  ?>
                                  <div class="radio radio-inline">
                                   <label class="form-label <?php echo $text_color_gen; ?>">
                                     <input aria-label="<?php echo __('Gender'); ?>" type="radio" name="gender"  value="<?php echo $gender_data['genc_detail_code'];?>" class="d-none"/>
                                     <i class="helper"></i>
                                     <?php echo __($gender_data['genc_detail_name']); ?>
                                   </label>
                                 </div>
                               <?php } ?>
                             </div>
                           </div>
                         </div>
                         <?php
                         $text_color_bg= $data['blood_group'] != $empdata['blood_group'] ? 'text-danger':"";
                         $display_div_bg = $field_set['BLOOD_GROUP']['display'] != 1 ? 'none' : '';
                         ?>

                         <div class="col-lg-6" style="display: <?php echo $display_div_bg; ?>">
                           <div class="mt-2" style="display: <?php echo $display_div_bg; ?>" >
                            <span class="col-form-label"><?php echo __('Blood Group'); ?></span></br>
                          <select aria-label="<?php echo __('Blood Group'); ?>"  class ="form-select js-example-basic-single" name="blood_group">
                            <option value=""><?php echo __('Select Any One'); ?></option>
                            <?php foreach ($blood_groups_details as $blood_groups) {
                              if($empdata['blood_group'] == $blood_groups['genc_detail_name']) {
                               $empdata['blood_group'] = $blood_groups['genc_detail_name'];
                             }
                             ?>
                             <option value="<?php echo __($blood_groups['genc_detail_name']); ?>" >
                              <?php echo __($blood_groups['genc_detail_name']); ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <?php
                    foreach ($marital_status_details as $marital_data) {
                      if($empdata['marital_status']==$marital_data['genc_detail_code']) {
                       $exist_marital_data = $marital_data['genc_detail_name'];
                       break;
                     }
                   }
                   ?>
                   <div class="col-lg-6" style="display: <?php echo $display_div_ma; ?>">
                     <div class="mt-2" style="display: <?php echo $display_div_ma; ?>" >

                      <span class="col-form-label"><?php echo __('Marital Status'); ?> </span>
                      <div  class="form-radio">
                       <?php
                       foreach ($marital_status_details as $marital_stat) {
                        ?>
                        <div class="radio radio-inline">
                         <label  class="form-label <?php echo $text_color_ma; ?>">
                           <input aria-label="<?php echo __('Marital Status'); ?>" onclick="showmarriagedate(this.value);" type="radio" name="marital_status"  value="<?php echo $marital_stat['genc_detail_code']; ?>" class="d-none" onkeydown="" />
                           <i class="helper"></i>
                           <?php echo __($marital_stat['genc_detail_name']); ?>
                         </label>
                       </div>
                     <?php } ?>
                   </div>
                 </div>
               </div>
               <?php
               $text_color_ms= fromDate($data['wedding_date']) != fromDate($empdata['wedding_date']) ? 'text-danger':"";
               $display_div_ms = $field_set['MARRIGE_DATE']['display'] != 1 ? 'none' : '';

               ?>
               <div class="col-lg-6" style="display: none"  id="marriagedateshow">
                 <div class="mt-2" style="display: <?php echo $display_div_ms; ?>" >
                  <span  class="col-form-label"><?php echo __('Marrige Date'); ?></span>
                   <?php $date_pic1 = fromDate($data['wedding_date']); ?>
                   <input aria-label="<?php echo __('Input field'); ?>" type="text"  class="form-control form-control-lg datepicker <?php echo $text_color_ms; ?>" placeholder="<?php echo __('Marrige Date'); ?>" name="wedding_date" value=""/>

                  </div>
                </div>


                <?php $text_color_fb = ($data['soc_facebook'] != $empdata['soc_facebook']) ? 'text-danger':"";
                $display_div_fb = $field_set['FACEBOOK']['display'] != 1 ? 'none' : '';
                ?>
                <div class="col-lg-6" style="display: <?php echo $display_div_fb; ?>">
                 <div class="mt-2" style="display: <?php echo $display_div_fb; ?>" >
                  <span class="col-form-label"><?php echo __('Facebook'); ?>
                     </span>
                   <div class="input-group input-icon-right">
                     <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="<?php echo __('Facebook URL'); ?>" name="soc_facebook" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span><?php echo __('com'); ?></span></span></div>
                   </div>
                 </div>
               </div>

               <?php $text_color_twi = ($data['soc_twitter'] != $empdata['soc_twitter']) ? 'text-danger':"";
               $display_div_twi = $field_set['TWITTER']['display'] != 1 ? 'none' : '';
               ?>
               <div class="col-lg-6" style="display: <?php echo $display_div_twi; ?>">
                 <div class="mt-2" style="display: <?php echo $display_div_twi; ?>" >
                  <span class="col-form-label"><?php echo __('Facebook'); ?>
                    </span>
                   <div class="input-group input-icon-right">
                     <input aria-label="<?php echo __('Input field'); ?>"  type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="<?php echo __('Twitter URL'); ?>" name="soc_twitter" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span><?php echo __('com'); ?></span></span></div>
                   </div>
                 </div>
               </div>

               <?php $text_color_ln = ($data['soc_linkedin'] != $empdata['soc_linkedin']) ? 'text-danger':"";
               $display_div_ln = $field_set['LINKEDIN']['display'] != 1 ? 'none' : '';
               ?>
               <div class="col-lg-6" style="display: <?php echo $display_div_ln; ?>" >
                 <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
                  <span class="col-form-label"><?php echo __('Facebook'); ?>
                   </span>
                   <div class="input-group input-icon-right">
                     <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ln; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="<?php echo __('LinkedIn URL'); ?>" name="soc_linkedin" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span><?php echo __('com'); ?></span></span></div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </fieldset>
         <h3> <?php echo __('Experience'); ?> </h3>
         <fieldset>
          <div class="pb-5" data-wizard-type="step-content">
            <div class="form-group row">
              <div class="form-group col-lg-12">
                <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="extable">
                  <tr>
                    <th scope="col" style="width: 2%;"></th>
                    <th scope="col" class="" style="width: 28%;"><?php echo __('Company Name'); ?></th>
                    <th scope="col" class="thheader" style="width: 28%;"><?php echo __('Job Description'); ?> </th>
                    <th scope="col" class="" style="width: 20%;"> <?php echo __('From / To'); ?></th>

                  </tr>
                  <?php if(count((array)$exp_details)=='0'){ $exp_details = array('company_name'=>"");
} $k=1; if(count((array)$exp_details)>0){
                   foreach ($exp_details as $expe){

                    ?>
                    <tr>
                      <td>
                        <div class="checkbox-fade fade-in-primary">
                          <label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span> </label>

                        </div>
                      </td>
                      <td>
                        <input type="hidden" name="expe_rowid[]" class="form-control " value="">
                        <input aria-label="<?php echo __('Enter Company Name'); ?>" type="text" name="company_name[]" class="form-control " placeholder="<?php echo __('Enter Company Name'); ?>"  autocomplete="off" value="">
                      </td>

                        <td>
                          <input aria-label="<?php echo __('Enter Job Description'); ?>" type="text" name="job_descrption[]" class="form-control" value="" autocomplete="off" placeholder="<?php echo __('Enter Job Description'); ?>">
                        </td>

                        <td>
                          <input aria-label="<?php echo __('From Date'); ?>" type="text" name="work_from[]" class="form-control datepicker"  placeholder="<?php echo __('From Date'); ?>" value="" autocomplete="off">
                          <input aria-label="<?php echo __('To Date'); ?>" type="text" name="work_to[]" class="form-control datepicker mt-2" placeholder="<?php echo __('To Date'); ?>" value="" autocomplete="off">
                        </td>


                      </tr>
                    <?php } ?>
                    <?php } ?>
                  </table>
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <button class="btn btn-danger" id="removeRows_exp" onclick="removeexprows()"  type="button"><?php echo __('- Delete'); ?></button> &nbsp;
                      <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows_exp" type="button"><?php echo __('+ Add More'); ?></button>
                    </div>
                  </div>
                </div>
              </div>

            </fieldset>
            <h3> <?php echo __('Educational'); ?>  </h3>
            <fieldset>
             <div class="pb-5" data-wizard-type="step-content" >

              <div class="row">
                <div class="form-group col-lg-12">
                  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="edutable">
                    <tr>
                      <th scope="col" style="width: 2%;"></th>
                      <th scope="col" class="" style="width: 25%;"><?php echo __('Institute Name'); ?></th>
                      <th scope="col" class="thheader" style="width: 20%;"><?php echo __('Type'); ?></th>
                      <th scope="col" class="thheader" style="width: 20%;"> <?php echo __('Start Date'); ?></th>

                      <th scope="col" class="thheader" style="width: 15%;"><?php echo __('Score'); ?> </th>
                    </tr>
                    <?php if(count((array)$edu_details)=='0'){ $edu_details = array('institute_name'=>"");
}
if(count((array)$edu_details)>0){
                     foreach ($edu_details as $inst) { ?>
                      <tr>
                        <td>

                         <div class="checkbox-fade fade-in-primary">
                          <label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span> </label>

                        </div>


                      </td>
                      <td>
                        <input type="hidden" name="institute_rowid[]" class="form-control " value="">
                        <input aria-label="<?php echo __('Enter Institute Name'); ?>" type="text" name="institute_name[]" class="form-control" value="" autocomplete="off" placeholder="<?php echo __('Enter Institute Name'); ?>">  </td>
                        <td>
                          <select aria-label="<?php echo __('Education Type[]'); ?>" class="form-control form-select js-example-basic-single" name="education_type[]" >
                            <option value=""> <?php echo __('Select Any One'); ?></option>
                            <?php foreach ($edutypedata as  $edutype) {   ?>
                              <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option>
                            <?php   } ?>
                            <option value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option>
                          </select>
                        </td>
                        <td>
                          <input aria-label="<?php echo __('Start Date'); ?>" type="text" name="start_date[]" class="form-control datepicker"  value=""  autocomplete="off" placeholder="<?php echo __('Start Date'); ?>">
                          <input aria-label="<?php echo __('End Date'); ?>" type="text" name="end_date[]" class="form-control datepicker mt-2"  placeholder="<?php echo __('End Date'); ?>"  value="" autocomplete="off">
                        </td>

                        <td><input aria-label="<?php echo __('Score'); ?>" type="text" name="score[]" class="form-control" placeholder="<?php echo __('Score'); ?>"  value=""  autocomplete="off">  </td>
                      </tr>
                    <?php } ?>
                    <?php } ?>
                  </table>
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <button class="btn btn-danger" id="removeRows_edu" onclick="removeedurows()"  type="button"><?php echo __('- Delete'); ?></button> &nbsp;
                      <button class="btn btn-success" onclick="addedurows()" id="addRows_edu" type="button"><?php echo __('+ Add More'); ?></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </fieldset>
          <h3> <?php echo __('Dependents'); ?>  </h3>
          <fieldset>
           <div class="pb-5" data-wizard-type="step-content" >

            <div class="row">
              <div class="form-group col-lg-12">
                <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="deptable">
                  <tr>
                    <th scope="col" style="width: 2%;"></th>
                    <th scope="col" class="" style="width: 25%;"> <?php echo __('Name'); ?></th>
                    <th scope="col" class="thheader" style="width: 20%;"><?php echo __('Relationship Type'); ?></th>
                    <th scope="col" class="thheader" style="width: 20%;"><?php echo __('DOB'); ?></th>
                  </tr>
                  <?php if(count((array)$dep_details)=='0'){ $dep_details = array('institute_name'=>"");
}
if(count((array)$dep_details)>0){
                   foreach ($dep_details as $depe) { ?>
                    <tr>
                      <td>
                       <div class="checkbox-fade fade-in-primary">
                        <label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span> </label>

                      </div>
                    </td>
                    <input type="hidden" name="dependent_rowid[]" class="form-control " value="">
                    <td><input aria-label="<?php echo __('Enter Name'); ?>" type="text" name="dependent_name[]" class="form-control " value="" autocomplete="off" placeholder="<?php echo __('Enter Name'); ?>">  </td>
                    <td>
                      <select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
                        <option value=""> <?php echo __('Select Any One'); ?></option>
                        <?php foreach ($relatypedata as  $reladata) {   ?>
                          <option    value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                        <?php   } ?>
                      </select>
                    </td>
                    <td><input aria-label="<?php echo __('Date Of Birth'); ?>" type="text" name="dependent_date_of_birth[]" class="form-control datepicker"  value="" placeholder="<?php echo __('Date Of Birth'); ?>" autocomplete="off">  </td>
                  </tr>
                <?php } ?>
                <?php } ?>
              </table>
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <button class="btn btn-danger " id="removeRows_dep" onclick="removedeprows()"  type="button"><?php echo __('- Delete'); ?></button> &nbsp;
                  <button class="btn btn-success"  onclick="adddeprows()" id="addRows_dep" type="button"><?php echo __('+ Add More'); ?></button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </fieldset>
      <h3> <?php echo __('Emergency'); ?> </h3>
      <fieldset>
        <div class="pb-5" data-wizard-type="step-content">

          <!--begin::Section-->
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
                <?php  if(count((array)$emerge_details)=='0'){ $emerge_details = array('contact_name'=>"");
}
if(count((array)$emerge_details)>0){
                 foreach ($emerge_details as $eno) { ?>
                  <tr>
                    <td>


                      <div class="checkbox-fade fade-in-primary">
                        <label class="form-label"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span> </label>

                      </div>

                    </td>
                    <input type="hidden" name="contact_rowid[]" class="form-control " value="">

                   <td>
                    <input aria-label="<?php echo __('Enter Contact Name'); ?>" type="text" name="contact_name[]" class="form-control " value="" autocomplete="off" placeholder="<?php echo __('Enter Contact Name'); ?>">
                  </td>
                  <td>
                    <select aria-label="<?php echo __('Contact Relationship[]'); ?>" class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                      <option value=""> <?php echo __('Select Any One'); ?></option>
                      <?php foreach ($relatypedata as  $reladata) {   ?>
                        <option  value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                      <?php   } ?>
                    </select>
                  </td>
                  <td><input aria-label="<?php echo __('Home Number'); ?>" type="number" name="contact_home_no[]" class="form-control " value="" placeholder="<?php echo __('Home Number'); ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Mobile Number'); ?>" type="number" name="contact_mobile_no[]" class="form-control " value="" autocomplete="off" placeholder="<?php echo __('Mobile Number'); ?>">  </td>

                </tr>
              <?php } ?>
              <?php } ?>
            </table>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button class="btn btn-danger" id="removeRows_eme" onclick="removeemerows()"  type="button"><?php echo __('- Delete'); ?></button> &nbsp;
                <button class="btn btn-success "  onclick="addemrgerows()" id="addRows_eme" type="button"><?php echo __('+ Add More'); ?></button>
              </div>
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
