<?php
   // if($_GET['Rid']!=""){
   //    $idvalue = explode(':',encodeId($_GET['Rid']));
   //    $employee_id = $idvalue[1];
   // }else{
   //    $employee_id= $_SESSION['emp_id'];
   // }
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
        confirmButtonText: "<i class='la la-hand-o-up'></i>OK",    
        confirmButtonClass: "btn btn-info", 
        showCancelButton: false,     
        cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
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
   //alert(value);
   if(value=='M') {
     document.getElementById('marriagedateshow').style.display ='block';
   }
   if(value=='S') {
     document.getElementById('marriagedateshow').style.display ='none';
   }
 }
 function statuspopup(status,id){
   //alert("data");
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
   //alert("id");
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
   // var count = $(".itemRow").length;
   //  count++;
   jQuery(document).ready(function() {    
     KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>';
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
   
   htmlRows += '<td><input type="text" name="dependent_name[]" placeholder="Enter Depandent Name "  class="form-control " autocomplete="off">  </td>'; 
   htmlRows += '<td><select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php   } ?></select>  </td>'; 
   htmlRows += '<td><input type="text" name="dependent_date_of_birth[]"  class="form-control datepicker"   autocomplete="off" placeholder="Enter Date Of Birth">  </td>';   
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
   // var count = $(".itemRow").length;
   //  count++;
   jQuery(document).ready(function() {    
     KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>'; 
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
   
   
   htmlRows += '<td><input type="text" name="contact_name[]"  class="form-control " placeholder="Enter Contact Name"  autocomplete="off"> </td>'; 
   htmlRows += '<td><select class="form-control form-select js-example-basic-single" name="contact_relationship[]" > <option value="0"> Select Any One</option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo $reladata[relation_name]; ?></option> <?php   } ?></select>    </td>'; 
   htmlRows += '<td><input type="text" name="contact_home_no[]" placeholder="Enter Home Number"  class="form-control "  autocomplete="off">  </td>';   
   htmlRows += '<td><input type="text" name="contact_mobile_no[]"  class="form-control "  placeholder="Enter Mobile Number"  autocomplete="off">  </td>'; 
   
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
   // var count = $(".itemRow").length;
   // count++;
   jQuery(document).ready(function() {    
     KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>'; 
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
   
   htmlRows += '<td><input type="text" name="company_name[]"  placeholder="Enter Company Name" class="form-control " autocomplete="off">  </td>'; 
   htmlRows += '<td><input type="text" name="job_descrption[]"  placeholder="Enter Job Description" class="form-control " autocomplete="off">  </td>'; 
   htmlRows += '<td><input type="text" name="work_from[]"  placeholder="Enter From Date" class="form-control datepicker"   autocomplete="off"> <input type="text" name="work_to[]"  placeholder="Enter To Date" class="form-control datepicker mt-2" autocomplete="off">  </td>'; 
 //  htmlRows += '<td><input type="text" name="work_to[]"  class="form-control datepicker" autocomplete="off">  </td>';   
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
   // var count = $(".itemRow").length;
   //  count++;
   // document.write("<script src='// ../assets/js/pages/myscrpit/date-picker.js'><\/script>");
   //datepicker();
   jQuery(document).ready(function() {    
     KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>'; 
   htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
   
   htmlRows += '<td><input type="text" name="institute_name[]"  placeholder="Enter Instistute name" class="form-control " autocomplete="off">  </td>'; 
   htmlRows += '<td><select class="form-control form-select js-example-basic-single" name="education_type[]" > <option value="0"> Select Any One</option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option> <?php   } ?></select> </td>'; 
   htmlRows += '<td><input type="text" name="start_date[]" placeholder="Enter Start Date"  class="form-control datepicker"  autocomplete="off"> <input type="text" name="end_date[]"  class="form-control datepicker mt-2"   autocomplete="off" placeholder="Enter End Date">  </td>'; 
  // htmlRows += '<td> </td>'; 
   htmlRows += '<td><input type="text" name="score[]"  class="form-control " placeholder ="Enter Score" autocomplete="off">  </td>';   
   htmlRows += '</tr>';
   // alert(htmlRows);
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


function checkageemployee(val){
  
   var today = new Date();
    var birthDate = new Date(val);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
	$("#ageinyear").val(age);
  // alert(age);
   //return $('#age').html(age+' years old');
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
.content.clearfix {
  padding: 0px 150px;
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
 /** .content.clearfix {
      background-color: white;
      overflow-y: scroll;
      height: 650px;
      width: 75% !important; 
    }
    .steps.clearfix {
      width: 20% !important;
    }
    .text-muted{
      display: none;
    }
.wizard > .content > .body label.error {
  margin-left: 0px !important;
  **/

</style>
<div class="d-flex flex-column-fluid ">
 <!--begin::Container-->
 <div class="container-fluid">
  <?php 
  if($hrconnect_id==0 || $hrconnect_id=="") { ?>
  <!--     <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
         <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
         <div class="alert-text"><?php echo "HR connect is not configured.  <strong>Workflow need to be started!</strong>"; ?></div>
         <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
         </div>
       </div> -->

       <div class="alert alert-primary alert-dismissible">
        <strong><?php echo "HR connect is not configured.  <strong>Workflow need to be started!</strong>"; ?></strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>




    <?php } else { ?>
     <!--  <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
         <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
         <div class="alert-text"></div>
         <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
         </div>
       </div> -->

       <div class="alert alert-primary alert-dismissible">
        <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>
    <?php } ?>




    <!-- <form class="form" method="post" action="" enctype="multipart/form-data">  --->






     <div class="card">
      <div class="card-header  ">
       <h5>Employee</h5>
  <!--      <div class="card-header-right table-card-header ">

        <button type="" class="btn btn-primary font-weight-bolder" onclick="submitempyeeform()"> <i class="ti-save text-white"></i> Create Employee</button>

      </div>   -->
	  
                        <div class="checkbox-fade fade-in-primary float-right">
                          <label class="form-label"><input class="form-control" id="create_user" type="checkbox" value="0"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span>Create User  </label>

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
                    <div id="wizardb">
                      <section>

                       <form class="wizard-form" id="example-advanced-create-employee"  enctype="multipart/form-data" method="post">
         <!--   <div class="wizard-form" id="verticle-wizard">
                      <form class="wizard-form" id="verticle-wizard" action="#">
                      -->                                                     
                      <h3> Basic Info <!-- </br><small>Please enter basic information</small> --> </h3>
                      <fieldset>
                       <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                          <!--  <h4 class="mb-5 font-weight-bold text-dark">Basic Information</h4>
                           --> <?php 
                              //  echo $data['emp_code'].$empdata['emp_code'];

                           $text_color_ec= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                           $display_div_ec = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                               // echo $display_div_ec;
                           $text_color_uc= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                           $display_div_uc = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                           ?>
                           <div class="row">
                            <div class="col-xl-6" style="display: <?php echo $display_div_ec; ?>" >
                             <div class="mt-2" style="display: <?php echo $display_div_ec; ?>">
                              <label class="col-form-label">Employee Code </label>
                            <div class="input-group input-icon-right">
                             <input  class="form-control form-control-lg  required   <?php echo $text_color_ec; ?>" type="text" placeholder="Enter Employee Code" name="emp_code" value="<?php echo $data['emp_code']; ?>" />
                           </div>
                         </div>
                         <!--end::Input-->
                       </div>
                       <div class="col-xl-6" style="display: <?php echo $display_div_uc; ?>" >
                         <div class="mt-2" style="display: <?php echo $display_div_uc; ?>" >
                          <label class="col-form-label">User Name</label>
                          <div class="input-group input-icon-right">
                           <input class="form-control form-control-lg  required  <?php echo $text_color_uc; ?>" type="text" name="display_name" placeholder="Enter User Name " value="<?php echo $data['display_name']; ?>" />
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
                        <label class="col-form-label">First Name </label>
                        <div class="input-group input-icon-right">
                         <input class="form-control required  form-control-lg  <?php echo $text_color_fn; ?>" type="text" placeholder="Enter First Name" name="first_name" value=""/>
                       </div>
                     </div>
                   </div>
                   <div class="col-xl-4" style="display: <?php echo $display_div_mn; ?>">
                     <div class="mt-2" style="display: <?php echo $display_div_mn; ?>" >
                      <label class="col-form-label">Middle Name </label>
                      <div class="input-group input-icon-right">
                       <input class="form-control   form-control-lg  <?php echo $text_color_mn; ?>" type="text" placeholder="Enter Middle Name" name="middle_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-4" style="display: <?php echo $display_div_ln; ?>">
                   <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
                    <label class="col-form-label">Last Name</label>
                      <div class="input-group input-icon-right">
                       <input class="form-control form-control-lg required  <?php echo $text_color_ln; ?>" type="text" placeholder="Enter last Name" name="last_name" value=""/>
                     </div>
                   </div>
                 </div>
                 <div class="col-xl-6" style="display: <?php echo $display_div_email; ?>" >
                   <?php 
                   $text_color_email= $data['email_id'] != $empdata['email_id'] ? 'text-danger':"";
                   $display_div_email = $field_set['EMAIL']['display'] != 1 ? 'none' : '';
                   ?>
                   <div class="mt-2" style="display: <?php echo $display_div_email; ?>" >
                    <label class="col-form-label">Email </label>
                    <div class="input-group input-icon-right">
                     <input  class="form-control form-control-lg  required  <?php echo $text_color_email; ?>" type="text" placeholder="Enter Your Email Address" name="email_id" value="" />
                   </div>
                 </div>
               </div>
               <?php 
               $text_color_ph= $data['phone'] != $empdata['phone'] ? 'text-danger':"";
               $display_div_ph = $field_set['CONTACT']['display'] != 1 ? 'none' : '';

               ?>
               <div class="col-xl-6" style="display: <?php echo $display_div_ph; ?>" >
                 <div class="mt-2" style="display: <?php echo $display_div_ph; ?>" >
                  <label class="col-form-label">Contact</label>
                  <div class="input-group input-icon-right">
                   <input class="form-control form-control-lg  <?php echo $text_color_ph; ?>" type="number" placeholder="Enter Contact Number" name="phone" value=""/>
                 </div>
               </div>
             </div>

             <?php 
             $text_color_adr= $data['address'] != $empdata['address'] ? 'text-danger':"";
             $display_div_adr = $field_set['ADDRESS']['display'] != 1 ? 'none' : '';
             ?>
             <div class="col-xl-6" style="display: <?php echo $display_div_adr; ?>">
               <div class="mt-2" style="display: <?php echo $display_div_adr; ?>" >
                <label class="col-form-label">Address</label>
                  <textarea  style="overflow:auto" class=" form-control form-control-lg <?php echo $text_color_adr; ?>" rows="5" cols="100" placeholder="Enter Your Address" name="address"></textarea>
                </div>
                <?php 
                $text_color_pc= $data['post_code'] != $empdata['post_code'] ? 'text-danger':"";
                $display_div_pc = $field_set['POSTCODE']['display'] != 1 ? 'none' : '';
                ?>
              </div>
              <div class="col-xl-6" style="display: <?php echo $display_div_pc; ?>">
               <div class="mt-2" style="display: <?php echo $display_div_pc; ?>" >
                <label class="col-form-label">Post Code </label>
                <div class="input-group">

                 <input type="text" class="form-control form-control-lg  <?php echo $text_color_pc; ?> " placeholder="Enter Your Post-Code" name="post_code" value=""/>
               </div>
             </div>
           </div>
         </div>
         <!--end::Input-->
       </div>
     </fieldset>

     <h3> Work <!-- </br><small>Please enter your work details</small> --> </h3>
     <fieldset>

      <div class="pb-5" data-wizard-type="step-content">

        <div class="row">

          <?php 
          $text_color_dj= fromDate($data['joining_date']) != fromDate($empdata['joining_date']) ? 'text-danger':"";
          $display_div_dj = $field_set['DATE_OF_JOINING']['display'] != 1 ? 'none' : '';
          ?>
          <div class="col-lg-6" style="display: <?php echo $display_div_dj; ?>" >
           <div  class="mt-2" style="display: <?php echo $display_div_dj; ?>" >
            <label class="col-form-label">Date of Joining </label>
            <div class="">
             <?php $date_pic = fromDate($data['joining_date']); ?>
             <input type="text" class="form-control datepicker form-control-lg  <?php echo $text_color_dj; ?>"  value=""  name="joining_date"  />                               
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
        <label class="col-form-label">Department</label>
          <select class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
            <option value="">Select Any One</option>
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
      <label class="col-form-label">Repoting to </label>
      <select  class ="form-control form-control-lg  form-select js-example-basic-single" name="reporting_manager_id">
        <option value="">Select Any One</option>

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
  <label class="col-form-label">
   Designation
 </label>
 <select class ="form-select js-example-basic-single" name="designation_id">
  <option value="">Select Any One</option>
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
  <label class="col-form-label">Vertical </label>
  <select class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
    <option value="">Select Any One</option>
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
  <label class="col-form-label">
    Hiring Source </label>
  <div class="input-group input-icon-right">
   <input  class="form-control form-control-lg  <?php echo $text_color_hs; ?>" type="text" placeholder="Enter Hiring Source" name="hiring_source" value=""/>
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
  <label class="col-form-label">Role</label> </br>
  <select class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
    <option value="">Select Any One</option>
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
  <label class="col-form-label">Experience in Current Organization</label>
   <div class="input-group input-group-button  ">
     <input type="text" class="form-control form-control-lg width1  <?php echo $text_color_eco; ?>"  placeholder="Enter Experience in Current Organization" name="experience_current_organization" value=""/>
     <span class="input-group-text addon-style"><label class="">Years</label></span>
   </div>

 </div>
</div>
<?php
$text_color_te= $data['total_experiance'] != $empdata['total_experiance'] ? 'text-danger':"";
$display_div_te = $field_set['TOTAL_EXPERIENCE']['display'] != 1 ? 'none' : '';
?>
<div class="col-lg-6"  style="display: <?php echo $display_div_te; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_te; ?>" >
  <label class="col-form-label">Total Experience Year  </label>
  <div class="input-group input-group-button ">
   <input type="text" class="form-control  width1 form-control-lg  <?php echo $text_color_te; ?>"  placeholder="Enter Total Experience"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
   <span class="input-group-text addon-style"><label class="">Years</label></span>

 </div>
</div>
</div>
<?php 
$text_color_re= $data['relevant_experience'] != $empdata['relevant_experience'] ? 'text-danger':"";
$display_div_re = $field_set['RELEVANT_EXPERIENCE']['display'] != 1 ? 'none' : '';
?>


<div class="col-lg-6" style="display: <?php echo $display_div_re; ?>" >
 <div  class="mt-2" style="display: <?php echo $display_div_re; ?>" >
  <label class="col-form-label">Relevant Experience Year  </label>
    <div class="input-group input-group-button  ">
     <input type="text" class="form-control form-control-lg width1  <?php echo $text_color_re; ?>"  placeholder="Enter Total Experience"  name="relevant_experience" value=""/>
     <span class="input-group-text addon-style"><label class="">Years</label></span>
   </div>
 </div>
</div>
<?php 

$text_color_np= $data['notice_period'] != $empdata['notice_period'] ? 'text-danger':"";
$display_div_np = $field_set['NOTICE_PERIOD']['display'] != 1 ? 'none' : '';

?>
<div class="col-lg-6" style="display: <?php echo $display_div_np; ?>">
 <div  class="mt-2" style="display: <?php echo $display_div_np; ?>">
  <label class="col-form-label">Notice Period </label>
  <div class="input-group input-group-button ">
   <input type="text" class="form-control form-control-sm width1  <?php echo $text_color_np; ?>"  placeholder="Enter Notice Period" name="notice_period" value="" />
   <span class="input-group-text addon-style"><label class="">Months</label></span>
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
  <label class="col-form-label">Band </label>
  <select class ="form-control form-control-lg form-select js-example-basic-single" name="band_id">
    <option value="">Select Any One</option>
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
  <label class="col-form-label">Level 
    <?php if($text_color_lv!="") { ?> <span data-bs-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['levels_name']==''?NODATA:$empdata['levels_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php } ?></label>
    <select  class ="form-control form-control-lg  form-select js-example-basic-single" name="level_id">
      <option value="">Select Any One</option>
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
  <label class="col-form-label">Work Location</label>
<select  class ="form-control form-control-lg form-select js-example-basic-single"  name="location_id">
  <option value="">Select Any One</option>
  <?php foreach ($locations_details as $locations) { 
    if($empdata['location_id'] == $locations['location_id']) {
     $empdata['location_name'] = $locations['location_name'];
   }

   if($locations['parent_location_id'] =='') {?>
     <option value="<?php echo $locations['id']; ?>" >
      <?php echo $locations['location_name']; ?>
    </option>
  <?php }  } ?>  
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
<div class="col-lg-6 " style="display: <?php echo $display_div_ety; ?>" >
 <div  class="mt-2" >
  <label class="col-form-label">Employement Type </label>
    <div class="form-radio mt-1">
     <?php
     foreach ($emptypes_details as $emptypes) {
      ?>
             <div class="radio radio-inline"> 
               <label class="form-label <?php echo $text_color_ety; ?>">
                 <input  type="radio" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php } ?> value="<?php echo $emptypes['genc_detail_code'];?>" class="d-none" />
                 <i class="helper"></i><?php echo $emptypes['genc_detail_name'];?> </label>
               </div>



             <?php } ?>
           </div>
         </div>
       </div>
       <div class="col-lg-6" style="display: <?php echo $display_div_ebi; ?>">
         <div  class="mt-2" style="display: <?php echo $display_div_ebi; ?>" >
          <label class="col-form-label">Billable </label>
            <div class="form-radio">
              <div class="radio radio-inline"> 
               <label class="form-label <?php echo $text_color_ebi; ?>">
                 <input  type="radio" name="billable" checked="checked"  value="0" class="d-none" />
                 <i class="helper"></i>Yes </label>
               </div>
               <div class="radio radio-inline">
                 <label class="form-label <?php echo $text_color_ebi; ?>">
                   <input  type="radio" name="billable"  value="1" class="d-none" />
                   <i class="helper"></i>No </label>
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
              <label class="col-form-label">
               Office Phone 
               
             </label>
             <div class="input-group input-icon-right">
               <input  type="text" class="form-control form-control-lg  <?php echo $text_color_ofph; ?>" name="office_phone" placeholder="Enter Office Phone" value=""/>

             </div>
           </div>
         </div>
         <div class="col-lg-4" style="display: <?php echo $display_div_ext; ?>">
           <div  class="mt-2" style="display: <?php echo $display_div_ext; ?>" >
            <label class="col-form-label">
             Extension 
           </label>
           <div class="input-group input-icon-right">
             <input  type="text" class="form-control form-control-lg  <?php echo $text_color_ext; ?>" name="office_ext" placeholder="Enter Extension" value=""/>

           </div>
         </div>
       </div>
       <?php 

       $text_color_fx= $data['fax'] != $empdata['fax'] ? 'text-danger':"";
		$display_div_fx = $field_set['FAX']['display'] != 1 ? 'none' : '';
       ?>
       <div class="col-xl-4" style="display: <?php echo $display_div_fx; ?>">
         <div class="mt-2" style="display: <?php echo $display_div_fx; ?>" >
          <label class="col-form-label">Fax </label>
          <div class="input-group input-icon-right">
           <input  class="form-control form-control-lg  <?php echo $text_color_fx; ?>" type="text" placeholder="Enter Fax Number" name="fax" value=""/>
         </div>
       </div>
     </div>
     <!-- end row-->
   </div>
   <!--end::Select-->
 </div>
</fieldset>
<h3> Personal <!--  </br><small>Please enter your personal details</small>  --></h3>
<fieldset>

  <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
   <!--   <h4 class="mb-5 font-weight-bold text-dark">Personal Details</h4> -->
   <?php 
   $text_color_bir= fromDate($data['date_of_birth']) != fromDate($empdata['date_of_birth']) ? 'text-danger':"";

   $display_div_bir = $field_set['DATE_OF_BIRTH']['display'] != 1 ? 'none' : '';

   ?>
   <div class="row">


    <div class="col-xl-6" style="display: <?php echo $display_div_bir; ?>">
     <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
      <label class="col-form-label">Date of Birth </label>
      <div class="">
       <?php $date_pic1 = fromDate($data['date_of_birth']); ?>
       <input  type="text" class="form-control form-control-lg datepicker  <?php echo $text_color_bir; ?>"  value="<?php echo  fromDate(date('Y-m-d')); ?>"  name="date_of_birth"  onchange="checkageemployee(this.value)" />
                                      
                                      </div>
                                    </div>
                                    <!--end::Input-->
                                  </div>
                                  <?php
                                  $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic1) ; 

                                  ?>
                                  <div class="col-xl-6" style="display: <?php echo $display_div_bir; ?>">
                                   <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
                                    <label class="col-form-label">Age In Years</label>
                                    <div class="input-group input-icon-right">
                                     <input readonly type="text" id="ageinyear" class="form-control form-control-lg " name="age" placeholder="Age" value="0"/>
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
                                <label class="col-form-label">Gender </label>
                                <div <?php echo $read_only_gen; ?> class="form-radio  ">
                                 <?php
                                 foreach ($gender_details as $gender_data) {
                                  ?>
                                  <div class="radio radio-inline">
                                   <label class="form-label <?php echo $text_color_gen; ?>">
                                     <input type="radio" name="gender"  value="<?php echo $gender_data['genc_detail_code'];?>" class="d-none"/>
                                     <i class="helper"></i>
                                     <?php echo $gender_data['genc_detail_name']; ?>
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
                            <label class="col-form-label">Blood Group</label></br>
                          <select  class ="form-select js-example-basic-single" name="blood_group">
                            <option value="">Select Any One</option>
                            <?php foreach ($blood_groups_details as $blood_groups) {
                              if($empdata['blood_group'] == $blood_groups['genc_detail_name']) {
                               $empdata['blood_group'] = $blood_groups['genc_detail_name'];
                             }
                             ?>
                             <option value="<?php echo $blood_groups['genc_detail_name']; ?>" >
                              <?php echo $blood_groups['genc_detail_name']; ?>
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


                      <label class="col-form-label">Marital Status </label>
                      <div  class="form-radio">
                       <?php
                       foreach ($marital_status_details as $marital_stat) {
                        ?>
                        <div class="radio radio-inline">
                         <label  class="form-label <?php echo $text_color_ma; ?>">
                           <input onclick="showmarriagedate(this.value);" type="radio" name="marital_status"  value="<?php echo $marital_stat['genc_detail_code']; ?>" class="d-none"/>
                           <i class="helper"></i>
                           <?php echo $marital_stat['genc_detail_name']; ?>
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
                  <label  class="col-form-label">Marrige Date</label>
                   <?php $date_pic1 = fromDate($data['wedding_date']); ?>
                   <input type="text"  class="form-control form-control-lg datepicker <?php echo $text_color_ms; ?>" placeholder="Enter Marrige Date" name="wedding_date" value=""/>
                    
                  </div>
                </div>

                <!--end::Input-->
                <?php $text_color_fb = ($data['soc_facebook'] != $empdata['soc_facebook']) ? 'text-danger':"";
                $display_div_fb = $field_set['FACEBOOK']['display'] != 1 ? 'none' : '';
                ?>
                <div class="col-lg-6" style="display: <?php echo $display_div_fb; ?>">
                 <div class="mt-2" style="display: <?php echo $display_div_fb; ?>" >
                  <label class="col-form-label">Facebook
                     </label>
                   <div class="input-group input-icon-right">
                     <input  type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="Facebook URL" name="soc_facebook" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span>com</span></span></div>
                   </div>
                 </div>
               </div>

               <?php $text_color_twi = ($data['soc_twitter'] != $empdata['soc_twitter']) ? 'text-danger':"";
               $display_div_twi = $field_set['TWITTER']['display'] != 1 ? 'none' : '';
               ?>
               <div class="col-lg-6" style="display: <?php echo $display_div_twi; ?>">
                 <div class="mt-2" style="display: <?php echo $display_div_twi; ?>" >
                  <label class="col-form-label">Facebook
                    </label>
                   <div class="input-group input-icon-right">
                     <input  type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="Twitter URL" name="soc_twitter" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span>com</span></span></div>
                   </div>
                 </div>
               </div>

               <?php $text_color_ln = ($data['soc_linkedin'] != $empdata['soc_linkedin']) ? 'text-danger':"";
               $display_div_ln = $field_set['LINKEDIN']['display'] != 1 ? 'none' : '';
               ?>
               <div class="col-lg-6" style="display: <?php echo $display_div_ln; ?>" >
                 <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
                  <label class="col-form-label">Facebook
                   </label>
                   <div class="input-group input-icon-right">
                     <input <?php echo $read_only_ln; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="LinkedIn URL" name="soc_linkedin" value=""/>
                     <div class="input-group-append"><span class="input-group-text"><span>com</span></span></div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </fieldset>
         <h3> Experience <!-- </br><small>Please enter your experience details</small>  --></h3>
         <fieldset>
          <div class="pb-5" data-wizard-type="step-content">
          <!--   <h4 class="mb-5 font-weight-bold text-dark ">Experience Details</h4>
           -->  <div class="form-group row">
              <div class="form-group col-lg-12">
                <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="extable">
                  <tr>
                    <th  width="2%"></th>
                    <th class="" width="28%">Company Name</th>
                    <th class="thheader" width="28%">Job Description </th>
                    <th class="" width="20%"> From / To</th>
                  <!--   <th class="thheader" width="20%">To </th> -->
                  </tr>
                  <?php if(count((array)$exp_details)=='0'){ $exp_details = array('company_name'=>""); } $k=1; if(count((array)$exp_details)>0){ 
                   foreach ($exp_details as $expe){ 

                    ?>
                    <tr>
                      <td>
                        <div class="checkbox-fade fade-in-primary">
                          <label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span> </label>

                        </div>
                      </td>
                      <td>
                        <input type="hidden" name="expe_rowid[]" class="form-control " autocomplete="off" value="">
                        <input type="text" name="company_name[]" class="form-control " placeholder="Enter Company Name"  autocomplete="off" value="">  
                      </td>

                        <td>
                          <input type="text" name="job_descrption[]" class="form-control" value="" autocomplete="off" placeholder="Enter Job Description"> 
                        </td>

                        <td>
                          <input type="text" name="work_from[]" class="form-control datepicker"  placeholder="Enter From Date" value="" autocomplete="off"> 
                          <input type="text" name="work_to[]" class="form-control datepicker mt-2" placeholder="Enter To Date" value="" autocomplete="off">  
                        </td>

                       <!--  <td>
                          <input type="text" name="work_to[]" class="form-control datepicker" value="<?php echo fromDate($expe['work_to']); ?>"autocomplete="off">  
                        </td> -->
                      </tr>
                    <?php } } ?>
                  </table>
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <button class="btn btn-danger" id="removeRows" onclick="removeexprows()"  type="button">- Delete</button> &nbsp;
                      <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows" type="button">+ Add More</button>
                    </div>
                  </div>
                </div>
              </div>

            </fieldset>
            <h3> Educational <!-- </br><small>Please enter your educational details</small> --> </h3>
            <fieldset>
             <div class="pb-5" data-wizard-type="step-content" >
            <!--   <h4 class="mb-5 font-weight-bold text-dark">Qualification</h4> -->
              <div class="row">
                <div class="form-group col-lg-12">
                  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="edutable">
                    <tr>
                      <th  width="2%"></th>
                      <th class="" width="25%">Institute Name</th>
                      <th class="thheader" width="20%">Type</th>
                      <th class="thheader" width="20%"> Start Date</th>
           <!--            <th class="thheader" width="20%">End Date </th> -->
                      <th class="thheader" width="15%">Score </th>
                    </tr>
                    <?php if(count((array)$edu_details)=='0'){ $edu_details = array('institute_name'=>""); }  if(count((array)$edu_details)>0){ 
                     foreach ($edu_details as $inst) { ?>
                      <tr>
                        <td>

                         <div class="checkbox-fade fade-in-primary">
                          <label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                          </span> </label>

                        </div>

                        <!--      <label class="checkbox checkbox-primary"><input class="form-control itemRow" type="checkbox"> <span></span> </label> -->
                      </td>
                      <td>
                        <input type="hidden" name="institute_rowid[]" class="form-control " autocomplete="off" value="">
                        <input type="text" name="institute_name[]" class="form-control" value="" autocomplete="off" placeholder="Enter Institute Name">  </td>
                        <td>
                          <select class="form-control form-select js-example-basic-single" name="education_type[]" >
                            <option value=""> Select Any One</option>
                            <?php foreach ($edutypedata as  $edutype) {   ?> 
                              <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                            <?php   } ?>
                            <option value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                          </select>
                        </td>
                        <td>
                          <input type="text" name="start_date[]" class="form-control datepicker"  value=""  autocomplete="off" placeholder="Enter Start Date"> 
                          <input type="text" name="end_date[]" class="form-control datepicker mt-2"  placeholder="Enter End Date"  value="" autocomplete="off"> 
                        </td>
                       
                        <td><input type="text" name="score[]" class="form-control" placeholder="Enter  Score"  value=""  autocomplete="off">  </td>
                      </tr>
                    <?php } } ?>
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

          </fieldset>
          <h3> Dependents <!-- </br><small>Please enter your dependent details</small> --> </h3>
          <fieldset>
           <div class="pb-5" data-wizard-type="step-content" >
          <!--   <h4 class="mb-5 font-weight-bold text-dark">Dependents</h4> -->
            <div class="row">
              <div class="form-group col-lg-12">
                <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="deptable">
                  <tr>
                    <th  width="2%"></th>
                    <th class="" width="25%"> Name</th>
                    <th class="thheader" width="20%">Relationship Type</th>
                    <th class="thheader" width="20%">DOB</th>
                  </tr>
                  <?php if(count((array)$dep_details)=='0'){ $dep_details = array('institute_name'=>""); }  if(count((array)$dep_details)>0){ 
                   foreach ($dep_details as $depe) { ?>
                    <tr>
                      <td>
                       <div class="checkbox-fade fade-in-primary">
                        <label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span> </label>

                      </div>
                    </td>
                    <input type="hidden" name="dependent_rowid[]" class="form-control " autocomplete="off" value="">
                    <td><input type="text" name="dependent_name[]" class="form-control " value="" autocomplete="off" placeholder="Enter Name">  </td>
                    <td>
                      <select class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
                        <option value=""> Select Any One</option>
                        <?php foreach ($relatypedata as  $reladata) {   ?> 
                          <option    value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                        <?php   } ?>
                      </select>
                    </td>
                    <td><input type="text" name="dependent_date_of_birth[]" class="form-control datepicker"  value="" placeholder="Date Of Birth" autocomplete="off">  </td>
                  </tr>
                <?php } } ?>
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

      </fieldset>
      <h3> Emergency <!-- </br><small>Please enter your emergency contact details</small>  --></h3>
      <fieldset>
        <div class="pb-5" data-wizard-type="step-content">
         <!--  <h4 class="mb-5 font-weight-bold text-dark">Emergency Contact</h4> -->
          <!--begin::Section-->
          <div class="row">
            <input type="hidden" name="tmp_id" value="<?php echo $temp_id; ?>">
            <div class="form-group col-lg-12">
              <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="emergetable">
                <tr>
                  <th  width="2%"></th>
                  <!-- <th class="" width="5%">Contact Seqno</th> -->
                  <th class="thheader" width="25%">Name</th>
                  <th class="" width="25%">Relationship</th>
                  <th class="thheader" width="25%">Home No</th>
                  <th class="thheader" width="25%">Mobile No</th>
                  <!-- <th class="thheader" width="18%">Contact Office No</th> -->
                </tr>
                <?php  if(count((array)$emerge_details)=='0'){ $emerge_details = array('contact_name'=>""); }  if(count((array)$emerge_details)>0){ 
                 foreach ($emerge_details as $eno) { ?>
                  <tr>
                    <td>
                      <!--       <label class="checkbox checkbox-primary"><input class="form-control itemRow" type="checkbox"> <span></span> </label> -->

                      <div class="checkbox-fade fade-in-primary">
                        <label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span> </label>

                      </div>

                    </td>
                    <input type="hidden" name="contact_rowid[]" class="form-control " autocomplete="off" value="">
                  <!-- <td>
                   <input type="text" name="contact_seqno[]" class="form-control " value="" autocomplete="off">  </td> -->
                   <td>
                    <input type="text" name="contact_name[]" class="form-control " value="" autocomplete="off" placeholder="Enter Contact Name"> 
                  </td>
                  <td>
                    <select class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                      <option value=""> Select Any One</option>
                      <?php foreach ($relatypedata as  $reladata) {   ?> 
                        <option  value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                      <?php   } ?>
                    </select>
                  </td>
                  <td><input type="number" name="contact_home_no[]" class="form-control " value="" placeholder="Enter Home Number" autocomplete="off">  </td>
                  <td><input type="number" name="contact_mobile_no[]" class="form-control " value="" autocomplete="off" placeholder="Enter Mobile Number">  </td>
                  <!-- <td><input type="text" name="contact_office_no[]" class="form-control " value="" autocomplete="off">  </td> -->
                </tr>
              <?php } } ?>
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
