<?php
if (!defined('ATTR_DISABLED_TRUE')) {
    define('ATTR_DISABLED_TRUE', ATTR_DISABLED_TRUE);
}

   if($_GET['Rid']!=""){
      $idvalue = explode(':',encodeId($_GET['Rid']));
      $employee_id = $idvalue['1'];
   }else{
      $employee_id= $_SESSION['emp_id'];
   }
   $emp_role_id=$_SESSION['role_id'];
   $table_name = "employees";

   /****************************** Get All details *****************/
   $url = API."?mode=getemployeeinfoforupdation&company_id=".$company_id."&employee_id=".$employee_id."&location_id=".$location_id."&role_id=".$emp_role_id."&is_active=0&application_tablename=".$table_name;
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


                 /************** end ********************/

                 /* Employee Details Start */
                 $url = API."?mode=employeedetails&emp_id=".$employee_id."&company_id=".$company_id;
                 $response = callAPI($url);
                 $maindata = json_decode($response,1);
                 $empdata  = $maindata['emp_data'];

                 $location_id = $empdata['location_id'];
                 /* Employee Details End */

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


               if(isset($_POST) && !empty($_POST)){
                  if($_POST['submit_type']=="hr") {
                     $_POST['emp_id'] = $employee_id;
                     $_POST['is_active'] = 0;

                     $_POST['user_id'] = $_SESSION['userid'];
                     $_POST['updated_date'] = date($_SESSION['date_formate']." H:i:s");
                     $_POST['company_id'] = $company_id;

                     include_once 'submit_hr.php';
                  } else {

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
                        $ex['institute_rowid'] = $_POST['institute_rowid'][$key];
                        $ex['institute_name'] = $institute_name;
                        $ex['education_type'] = $_POST['education_type'][$key];
                        $ex['start_date'] = $_POST['start_date'][$key];
                        $ex['end_date'] = $_POST['end_date'][$key];
                        $ex['score'] = $_POST['score'][$key];
                        $edu[] = $ex;
                     }
                     $_POST['education_details'] = $edu;


                     $ex =array();
                     foreach ($_POST['dependent_name'] as $key=> $dependent_name) {
                        $ex['dependent_rowid'] = $_POST['dependent_rowid'][$key];

                        $ex['dependent_name'] = $dependent_name;
                        $ex['dependent_relationship_type'] = $_POST['dependent_relationship_type'][$key];
                        $ex['dependent_relationship'] = $_POST['dependent_relationship'][$key];
                        $ex['dependent_date_of_birth'] = $_POST['dependent_date_of_birth'][$key];
                        $dep[] = $ex;
                     }

                     $_POST['dependent_details'] = $dep;

                     $ex =array();
                     foreach ($_POST['contact_name'] as $key=> $contact_name) {
                        $ex['contact_rowid'] = $_POST['contact_rowid'][$key];
                        $ex['contact_name'] = $contact_name;
                        $ex['contact_seqno'] = $_POST['contact_seqno'][$key];
                        $ex['contact_relationship'] = $_POST['contact_relationship'][$key];
                        $ex['contact_home_no'] = $_POST['contact_home_no'][$key];
                        $ex['contact_mobile_no'] = $_POST['contact_mobile_no'][$key];
                        $ex['contact_office_no'] = $_POST['contact_office_no'][$key];
                        $eme[] = $ex;
                     }

                     $_POST['emergency_details'] = $eme;

                     $unset =  array('company_name','job_descrption','work_from','work_to','institute_name','institute_name','education_type','start_date','end_date','score','dependent_name','dependent_relationship_type','dependent_relationship','dependent_date_of_birth','contact_name','contact_seqno','contact_relationship','contact_home_no','contact_mobile_no','contact_office_no','contact_rowid','dependent_rowid','institute_rowid','expe_rowid');
                     foreach ($unset as  $set) {
                        unset($_POST[$set]);
                     }
                     $postdata['content'] = json_encode($_POST,1);
                     $postdata['is_active'] = 0;
                     $postdata['application_tablename'] = "employees";
                     $postdata['company_id'] = $company_id;
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
              }

              header("location:index.php?option=".$_GET['option']);

           }


           ?>
<script type="text/javascript">
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
     $("#dropdownlist_"+id).html(data);
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
   htmlRows += '<td><label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Dependent Name[]'); ?>" type="text" name="dependent_name[]"  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control" name="dependent_relationship_type[]" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo __($reladata[relation_name]); ?></option> <?php
} ?></select>  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Dependent Date Of Birth[]'); ?>" type="text" name="dependent_date_of_birth[]"  class="form-control "  id="kt_datepicker_dep_dob" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#deptable').append(htmlRows);
   }


   function addemrgerows(){
   jQuery(document).ready(function() {
   KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>';
   htmlRows += '<td><label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label></td> ';


   htmlRows += '<td><input aria-label="<?php echo __('Contact Name[]'); ?>" type="text" name="contact_name[]"  class="form-control " autocomplete="off"> </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control" name="dependent_relationship_type[]" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($relatypedata as  $reladata) {   ?> <option   value="<?php echo $reladata[id]; ?>"> <?php echo __($reladata[relation_name]); ?></option> <?php
} ?></select>    </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Contact Home No[]'); ?>" type="text" name="contact_home_no[]"  class="form-control "  autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Contact Mobile No[]'); ?>" type="text" name="contact_mobile_no[]"  class="form-control "  autocomplete="off">  </td>';

   htmlRows += '</tr>';
   $('#emergetable').append(htmlRows);
   }

   function addexpreencerows(){
   jQuery(document).ready(function() {
   KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>';
   htmlRows += '<td><label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Company Name[]'); ?>" type="text" name="company_name[]"  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Job Descrption[]'); ?>" type="text" name="job_descrption[]"  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Work From[]'); ?>" type="text" name="work_from[]"  class="form-control "  id="kt_datepicker_35" autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Work To[]'); ?>" type="text" name="work_to[]"  class="form-control "  id="kt_datepicker_35_2" autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#extable').append(htmlRows);
   }

   function addedurows(){
   jQuery(document).ready(function() {
   KTBootstrapDatepicker.init();
   });
   var htmlRows = '';
   htmlRows += '<tr>';
   htmlRows += '<td><label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label></td> ';

   htmlRows += '<td><input aria-label="<?php echo __('Institute Name[]'); ?>" type="text" name="institute_name[]"  class="form-control " autocomplete="off">  </td>';
   htmlRows += '<td><select aria-label="<?php echo __('Education Type'); ?>" class="form-control" name="education_type" > <option value="0"> <?php echo __('Select Any One'); ?></option> <?php foreach ($edutypedata as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option> <?php
} ?></select> </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Start Date[]'); ?>" type="text" name="start_date[]"  class="form-control " id="kt_datepicker_35_3" autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('End Date[]'); ?>" type="text" name="end_date[]"  class="form-control "  id="kt_datepicker_35_4" autocomplete="off">  </td>';
   htmlRows += '<td><input aria-label="<?php echo __('Score[]'); ?>" type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';
   htmlRows += '</tr>';
   $('#edutable').append(htmlRows);
   }


</script>
<div class="d-flex flex-column-fluid">
   <!--begin::Container-->
   <div class="container">
      <div class="card card-custom">
         <div class="card-header">
            <div class="card-title">
               <h3 class="card-label">&nbsp;<?php echo __('Employee'); ?> </h3>
            </div>
            <div class="card-toolbar">
               <?php if($employee_id!="") {  $nmm= ViewStatusValue($empdata['current_status'],$table_name);  ?>
               <div class="btn-group">
                  <button onclick="displaystausdrop(<?php echo $employee_id; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $empdata['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo __($nmm['name']); ?> </button>
                  <div class="dropdown-menu" id="dropdownlist">
                  </div>
               </div>
               <?php } ?>
            </div>
         </div>
         <div class="card-body p-0">
            <!--begin: Wizard-->
            <div class="wizard wizard-2" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
               <!--begin: Wizard Nav-->
               <div class="wizard-nav border-right py-8 px-8 py-lg-10 px-lg-10">
                  <!--begin::Wizard Step 1 Nav-->
                  <div class="wizard-steps">
                     <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Basic Info'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter basic information'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 1 Nav-->
                     <!--begin::Wizard Step 2 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Work'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your work details'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 2 Nav-->
                     <!--begin::Wizard Step 3 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Personal Details'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your personal details'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 3 Nav-->
                     <!--begin::Wizard Step 4 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Experience Details'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your experience details'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 4 Nav-->
                     <!--begin::Wizard Step 5 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Educational Details'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your educational details'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 5 Nav-->
                     <!--begin::Wizard Step 6 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Dependents Details'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your dependent details'); ?></div>
                           </div>
                        </div>
                     </div>
                     <!--end::Wizard Step 6 Nav-->
                     <div class="wizard-step" data-wizard-type="step">
                        <div class="wizard-wrapper">
                           <div class="wizard-icon">
                           </div>
                           <div class="wizard-label">
                              <h3 class="wizard-title"><?php echo __('Emergency Contact'); ?></h3>
                              <div class="wizard-desc"><?php echo __('Please enter your emergency contact details'); ?></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!--end: Wizard Nav-->
               <!--begin: Wizard Body-->
               <div class="wizard-body py-5 px-2 py-lg-10 px-lg-3">

                  <div class="offset-xxl-2 col-xxl-8">
                     <form class="form" id="kt_form" method="post" action="" enctype="multipart/form-data">
                        <!--begin: Wizard Step 1-->
                        <!--end: Wizard Step 2-->
                        <!--begin: Wizard Step 3-->
                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                           <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Basic Informtion'); ?></h4>
                           <?php
                              $text_color_ec= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                              $read_only_ec = $field_set['EMPLOYEE_CODE']['readonly'] != 0 ? 'readonly' : '';
                              $display_div_ec = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                              $text_color_uc= $data['emp_code'] != $empdata['emp_code'] ? 'text-danger':"";
                              $read_only_uc = $field_set['EMPLOYEE_CODE']['readonly'] != 0 ? 'readonly' : '';
                              $display_div_uc = $field_set['EMPLOYEE_CODE']['display'] != 1 ? 'none' : '';
                              ?>
                           <div class="row">
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_ec; ?>">
                                    <span><?php echo __('Employee Code'); ?>                                                  <?php if($text_color_ec!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['emp_code']==''?__('No Data'):$empdata['emp_code'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?>
                                    </span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ec; ?> class="form-control form-control-lg   <?php echo $text_color_ec; ?>" type="text" placeholder="<?php echo __('Enter employee Code'); ?>" name="emp_code" value="<?php echo $data['emp_code']; ?>" />
                                    </div>
                                 </div>

                              </div>
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_uc; ?>" >
                                    <span><?php echo __('Username'); ?>       <?php if($text_color_uc!="")  { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['emp_code']==''?__('No Data'):$empdata['emp_code'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_uc; ?> class="form-control form-control-lg  <?php echo $text_color_uc; ?>" type="text" placeholder="<?php echo __('Username:'); ?>" value="<?php echo $data['emp_code']; ?>" />
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xl-12">
                                 <?php
                                    $text_color_email= $data['email_id'] != $empdata['email_id'] ? 'text-danger':"";
                                    $read_only_email = $field_set['EMAIL']['readonly'] != 0 ? 'readonly' : '';
                                    $display_div_email = $field_set['EMAIL']['display'] != 1 ? 'none' : '';
                                    ?>
                                 <div class="mt-2" style="display: <?php echo $display_div_email; ?>" >
                                    <span class="col-form-label"><?php echo __('Email'); ?>  <?php if($text_color_email!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['email_id']==''?__('No Data'):$empdata['email_id'];?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_email; ?> class="form-control form-control-lg  <?php echo $text_color_email; ?>" type="text" placeholder="<?php echo __('Enter email'); ?>" name="email_id" value="<?php echo $data['email_id']; ?>" />
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_fn= $data['first_name'] != $empdata['first_name'] ? 'text-danger':"";
                                 $read_only_fn = $field_set['FIRST_NAME']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_fn = $field_set['FIRST_NAME']['display'] != 1 ? 'none' : '';

                                 $text_color_ln= $data['last_name'] != $empdata['last_name'] ? 'text-danger':"";

                                 $read_only_ln = $field_set['LAST_NAME']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ln = $field_set['LAST_NAME']['display'] != 1 ? 'none' : '';

                                 $text_color_mn= $data['middle_name'] != $empdata['middle_name'] ? 'text-danger':"";
                                 $read_only_mn = $field_set['MIDDLE_NAME']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_mn = $field_set['MIDDLE_NAME']['display'] != 1 ? 'none' : '';
                                 ?>
                              <div class="col-xl-4">
                                 <div class="mt-2" style="display: <?php echo $display_div_fn; ?>" >
                                    <span class="col-form-label"><?php echo __('First Name'); ?>  <?php if($text_color_fn!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['first_name']==''?__('No Data'):$empdata['first_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_fn; ?> class="form-control form-control-lg  <?php echo $text_color_fn; ?>" type="text" placeholder="<?php echo __('Enter first Name'); ?>" name="first_name" value="<?php echo $data['first_name']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xl-4">
                                 <div class="mt-2" style="display: <?php echo $display_div_mn; ?>" >
                                    <span class="col-form-label"><?php echo __('Middle Name'); ?>  <?php if($text_color_mn!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['middle_name']==''?__('No Data'):$empdata['middle_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_mn; ?> class="form-control form-control-lg  <?php echo $text_color_mn; ?>" type="text" placeholder="<?php echo __('Enter middle Name'); ?>" name="middle_name" value="<?php echo $data['middle_name']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xl-4">
                                 <div class="mt-2" style="display: <?php echo $display_div_ln; ?>" >
                                    <span class="col-form-label"><?php echo __('Last Name'); ?>
                                    <?php if($text_color_ln!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['last_name']==''?__('No Data'):$empdata['last_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ln; ?> class="form-control form-control-lg  <?php echo $text_color_ln; ?>" type="text" placeholder="<?php echo __('Enter last Name'); ?>" name="last_name" value="<?php echo $data['last_name']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_ph= $data['phone'] != $empdata['phone'] ? 'text-danger':"";
                                 $read_only_ph = $field_set['CONTACT']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ph = $field_set['CONTACT']['display'] != 1 ? 'none' : '';

                                 $text_color_fx= $data['fax'] != $empdata['fax'] ? 'text-danger':"";

                                 $read_only_fx = $field_set['FAX']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_fx = $field_set['FAX']['display'] != 1 ? 'none' : '';
                                 ?>
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_ph; ?>" >
                                    <span class="col-form-label"><?php echo __('Contact'); ?>  <?php if($text_color_ph!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['phone']==''?__('No Data'):$empdata['phone'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ph; ?> class="form-control form-control-lg  <?php echo $text_color_ph; ?>" type="text" placeholder="<?php echo __('Enter first Name'); ?>" name="phone" value="<?php echo $data['phone']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_fx; ?>" >
                                    <span class="col-form-label"><?php echo __('Fax'); ?>  <?php if($text_color_fx!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['fax']==''?__('No Data'):$empdata['fax'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_fx; ?> class="form-control form-control-lg  <?php echo $text_color_fx; ?>" type="text" placeholder="<?php echo __('Enter middle Name'); ?>" name="fax" value="<?php echo $data['fax']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_adr= $data['address'] != $empdata['address'] ? 'text-danger':"";
                                 $read_only_adr = $field_set['ADDRESS']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_adr = $field_set['ADDRESS']['display'] != 1 ? 'none' : '';
                                 ?>
                              <div class="col-xl-12">
                                 <div class="mt-2" style="display: <?php echo $display_div_adr; ?>" >
                                    <span><?php echo __('Address'); ?> <?php if($text_color_adr!="") { ?>
                                    <span  data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['address']==''?__('No Data'):$empdata['address'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span > <?php
} ?></span>
                                    <textarea aria-label="<?php echo __('Text area'); ?>" <?php echo $read_only_adr; ?> style="overflow:auto" class="form-control form-control-lg  <?php echo $text_color_adr; ?>" placeholder="<?php echo __('Enter your address'); ?>" name="address"><?php echo $data['address'];?></textarea>
                                 </div>
                                 <?php
                                    $text_color_pc= $data['post_code'] != $empdata['post_code'] ? 'text-danger':"";
                                    $read_only_pc = $field_set['POSTCODE']['readonly'] != 0 ? 'readonly' : '';
                                    $display_div_pc = $field_set['POSTCODE']['display'] != 1 ? 'none' : '';
                                    ?>
                              </div>
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_pc; ?>" >
                                    <span><?php echo __('Post code'); ?>
                                    <?php if($text_color_pc!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['post_code']==''?__('No Data'):$empdata['post_code'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?>
                                    </span>
                                    <div class="input-group">
                                       <div class="input-group-append"><span class="input-group-text"><i class="la la-bookmark-o"></i></span></div>
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_pc; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_pc; ?> " placeholder="<?php echo __('Enter your postcode'); ?>" name="post_code" value="<?php echo $data['post_code']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                           </div>

                        </div>
                        <div class="pb-5" data-wizard-type="step-content">
                           <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Work Details'); ?></h4>

                           <?php
                              $text_color_dep= $data['department_id'] != $empdata['department_id'] ? 'text-danger':"";
                              $read_only = $field_set['DEPARTMENT']['readonly'] != 0 ? 'readonly' : '';
                              $display_div_dep = $field_set['DEPARTMENT']['display'] != 1 ? 'none' : '';

                              $text_color_ver= $data['vertical_id'] != $empdata['vertical_id'] ? 'text-danger':"";
                              $read_only_ver = $field_set['VERTICAL']['readonly'] != 0 ? 'readonly' : '';
                              $display_div_ver = $field_set['VERTICAL']['display'] != 1 ? 'none' : '';

                              ?>
                           <div class="row">
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_dep; ?>" >
                                    <span class="col-form-label"><?php echo __('Department'); ?>
                                    <?php if($text_color_dep!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['department_name']==''?__('No Data'):$empdata['department_name'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['DEPARTMENT']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_dep; ?>" name="department_id">
                                       <?php foreach ($dept_details as $departments) {
                                          ?>
                                       <option <?php if($data['department_id'] == $departments['id']){ ?>selected <?php
} ?> value="<?php echo $departments['id']; ?>" >
                                          <?php echo __($departments['department_name']);
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
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_ver; ?>" >
                                    <span class="col-form-label"><?php echo __('Vertical'); ?>  <?php if($text_color_ver!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['vertical_name']==''?__('No Data'):$empdata['vertical_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['VERTICAL']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_ver; ?>" name="vertical_id">
                                       <?php foreach ($verticals_details as $verticals) {
                                          if($empdata['vertical_id'] == $verticals['id']) {
                                             $empdata['vertical_name'] = $verticals['vertical_name'];
                                          }
                                          ?>
                                       <option <?php if($data['vertical_id'] == $verticals['id']){ ?>selected <?php
} ?> value="<?php echo $verticals['id']; ?>" >
                                          <?php echo __($verticals['vertical_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <?php
                                 $text_color_rto= $data['reporting_manager_id'] != $empdata['reporting_manager_id'] ? 'text-danger':"";

                                 $read_only_rto = $field_set['REPOTING_TO']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_rto = $field_set['REPOTING_TO']['display'] != 1 ? 'none' : '';

                                 $text_color_hs= $data['hiring_source'] != $empdata['hiring_source'] ? 'text-danger':"";
                                 $read_only_hs = $field_set['HIRING_SOURCE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_hs = $field_set['HIRING_SOURCE']['display'] != 1 ? 'none' : '';

                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_rto; ?>" >
                                    <span class="col-form-label"><?php echo __('Repoting to'); ?>  <?php if($text_color_rto!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['display_name']==''?__('No Data'):$empdata['display_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['REPOTING_TO']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_rto; ?>" name="reporting_manager_id">
                                       <?php foreach ($application_users_details as $application_users) {
                                          if($empdata['reporting_manager_id'] == $application_users['id']) {
                                             $empdata['display_name'] = $application_users['display_name'];
                                          }

                                          ?>
                                       <option <?php if($data['reporting_manager_id'] == $application_users['id']){ ?>selected <?php
} ?> value="<?php echo $application_users['id']; ?>" >
                                          <?php echo __($application_users['display_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_hs; ?>">
                                    <span class="col-form-label">
                                    <?php echo __('Hiring Source'); ?>
                                    <?php if($text_color_hs!="") { ?>
                                    <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['hiring_source']==''?__('No Data'):$empdata['hiring_source'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span>
                                    <?php } ?>
                                    </span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_hs; ?> class="form-control form-control-lg  <?php echo $text_color_hs; ?>" type="text" placeholder="<?php echo __('Hiring Source'); ?>" name="hiring_source" value="<?php echo $data['hiring_source']; ?>"/>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_des= $data['designation_id'] != $empdata['designation_id'] ? 'text-danger':"";

                                 $read_only_des = $field_set['DESIGNATION']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_des = $field_set['DESIGNATION']['display'] != 1 ? 'none' : '';

                                 $text_color_role= $data['role_id'] != $empdata['role_id'] ? 'text-danger':"";
                                 $read_only_role = $field_set['ROLE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_role = $field_set['ROLE']['display'] != 1 ? 'none' : '';

                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_des; ?>" >
                                    <span class="col-form-label">
                                       <?php echo __('Designation'); ?>  <?php if($text_color_des!="") { ?>
                                       <div data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['designation_name']==''?__('No Data'):$empdata['designation_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </div>
                                       <?php } ?>
                                    </span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['DESIGNATION']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_des; ?>" name="designation_id">
                                       <?php foreach ($designations_details as $desigs) {
                                          if($empdata['designation_id'] == $desigs['id']) {
                                             $empdata['designation_name'] = $desigs['designation_name'];
                                          }

                                          ?>
                                       <option <?php if($data['id'] == $desigs['id']){ ?>selected <?php
} ?> value="<?php echo $desigs['id']; ?>" >
                                          <?php echo __($desigs['designation_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_role; ?>" >
                                    <span class="col-form-label"><?php echo __('Role'); ?>  <?php if($text_color_role!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['role_name']==''?__('No Data'):$empdata['role_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['ROLE']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_role; ?>" name="role_id">
                                       <?php foreach ($employment_roles_details as $employment_roles) {
                                          if($empdata['role_id'] == $employment_roles['id']) {
                                             $empdata['role_name'] = $employment_roles['role_name'];
                                          }
                                          ?>
                                       <option <?php if($data['role_id'] == $employment_roles['id']){ ?>selected <?php
} ?> value="<?php echo $employment_roles['id']; ?>" >
                                          <?php echo __($employment_roles['role_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <?php $text_color_dj= fromDate($data['joining_date']) != fromDate($empdata['joining_date']) ? 'text-danger':"";
                                 $read_only_dj = $field_set['DATE_OF_JOINING']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_dj = $field_set['DATE_OF_JOINING']['display'] != 1 ? 'none' : '';


                                 $text_color_eco= $data['experience_current_organization'] != $empdata['experience_current_organization'] ? 'text-danger':"";

                                 $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ;

                                 $read_only_eco = $field_set['EXPERIENCE_IN_CURRENT_ORGANIZATION']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_eco = $field_set['EXPERIENCE_IN_CURRENT_ORGANIZATION']['display'] != 1 ? 'none' : '';


                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_dj; ?>" >
                                    <span class="col-form-label"><?php echo __('Date of Joining'); ?>  <?php if($text_color_dj!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['joining_date']==''?__('No Data'):$empdata['joining_date'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group date">
                                       <?php $date_pic = fromDate($data['joining_date']); ?>
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $field_set['DATE_OF_JOINING']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> type="text" class="form-control form-control-lg  <?php echo $text_color_dj; ?>"  value="<?php echo $date_pic; ?>"  name="joining_date"  id="kt_datepicker_32" />
                                       <div class="input-group-append">
                                          <span class="input-group-text">
                                          <i class="la la-calendar"></i>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic) ;
                                 ?>
                              <div class="col-lg-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_eco; ?>" >
                                    <span class="col-form-label"><?php echo __('Experience in Current Organization'); ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_eco; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_eco; ?>"  placeholder="<?php echo __('Experience in Current Organization'); ?>" name="experience_current_organization" value="<?php echo floor($diff / (365*60*60*24)); ?>"/>
                                       <div class="input-group-append"><span class="input-group-text"><span><?php echo __('Years'); ?></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_te= $data['total_experiance'] != $empdata['total_experiance'] ? 'text-danger':"";

                                 $read_only_te = $field_set['TOTAL_EXPERIENCE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_te = $field_set['TOTAL_EXPERIENCE']['display'] != 1 ? 'none' : '';


                                 $text_color_np= $data['notice_period'] != $empdata['notice_period'] ? 'text-danger':"";
                                 $read_only_np = $field_set['NOTICE_PERIOD']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_np = $field_set['NOTICE_PERIOD']['display'] != 1 ? 'none' : '';
                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_te; ?>" >
                                    <span class="col-form-label"><?php echo __('Total Experience'); ?>  <?php if($text_color_te!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['total_experiance']==''?__('No Data'):$empdata['total_experiance'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?> </span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_te; ?>  type="text" class="form-control form-control-lg  <?php echo $text_color_te; ?>"  placeholder="<?php echo __('Total Experience'); ?>"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
                                       <div class="input-group-append"><span class="input-group-text"><span><?php echo __('Years'); ?></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_np; ?>">
                                    <span class="col-form-label"><?php echo __('Notice Period'); ?>  <?php if($text_color_np!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['notice_period']==''?__('No Data'):$empdata['notice_period'] ;?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_np; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_np; ?>"  placeholder="<?php echo __('Notice Period'); ?>" name="notice_period" value="<?php echo $data['notice_period']; ?>" />
                                       <div class="input-group-append"><span class="input-group-text"><span><?php echo __('Months'); ?></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_lv= $data['level_id'] != $empdata['level_id'] ? 'text-danger':"";
                                 $read_only_lv = $field_set['LEVEL']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_lv = $field_set['LEVEL']['display'] != 1 ? 'none' : '';

                                 $text_color_bd = $data['band_id'] != $empdata['band_id'] ? 'text-danger':"";

                                 $read_only_bd = $field_set['BAND']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_bd = $field_set['BAND']['display'] != 1 ? 'none' : '';

                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_bd; ?>">
                                    <span class="col-form-label"><?php echo __('Band'); ?>  <?php if($text_color_bd!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['band_name']==''?__('No Data'):$empdata['band_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['BAND']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_bd; ?>" name="band_id">
                                       <?php foreach ($employment_bands_details as $employment_bands) {
                                          if($empdata['band_id'] == $employment_bands['id']) {
                                             $empdata['band_name'] = $employment_bands['band_name'];
                                          }
                                          ?>
                                       <option <?php if($data['band_id'] == $employment_bands['id']){ ?>selected <?php
} ?> value="<?php echo $employment_bands['id']; ?>" >
                                          <?php echo __($employment_bands['band_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_lv; ?>" >
                                    <span class="col-form-label"><?php echo __('Level'); ?>
                                    <?php if($text_color_lv!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['levels_name']==''?__('No Data'):$empdata['levels_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['LEVEL']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_lv; ?>" name="level_id">
                                       <?php foreach ($emplevels_details as $employment_levels) {
                                          if($empdata['level_id'] == $employment_levels['id']) {
                                             $empdata['levels_name'] = $employment_levels['levels_name'];
                                          }
                                          ?>
                                       <option <?php if($data['level_id'] == $employment_levels['id']){ ?>selected <?php
} ?> value="<?php echo $employment_levels['id']; ?>" >
                                          <?php echo __($employment_levels['levels_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>
                              <?php
                                 $text_color_loc= $data['location_id'] != $empdata['location_id'] ? 'text-danger':"";
                                 $read_only_loc = $field_set['WORK_LOCATION']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_loc = $field_set['WORK_LOCATION']['display'] != 1 ? 'none' : '';

                                 $text_color_ety= $data['employee_type'] != $empdata['employee_type'] ? 'text-danger':"";
                                 $read_only_ety = $field_set['EMPLOYEMENT_TYPE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ety = $field_set['EMPLOYEMENT_TYPE']['display'] != 1 ? 'none' : '';

                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_loc; ?>" >
                                    <span class="col-form-label"><?php echo __('Work Location'); ?>                        <?php if($text_color_loc!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['location_name']==''?__('No Data'):$empdata['location_name'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?>
                                    </span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['WORK_LOCATION']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg"  <?php echo $text_color_loc; ?>" name="location_id">
                                       <?php foreach ($locations_details as $locations) {
                                          if($empdata['location_id'] == $locations['location_id']) {
                                             $empdata['location_name'] = $locations['location_name'];
                                          }

                                          if($locations['parent_location_id'] =='') {?>
                                       <option <?php if($data['location_id'] == $locations['id']){ ?>selected <?php
} ?> value="<?php echo $locations['id']; ?>" >
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
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_ety; ?>" >
                                    <span class="col-form-label"><?php echo __('Employement Type'); ?>
                                    <?php if($text_color_ety!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['employee_type']==''?__('No Data'): $exist_emptypes; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="radio-inline mt-5">
                                       <?php
                                          foreach ($emptypes_details as $emptypes) {
                                            ?>
                                       <label class="radio radio-solid <?php echo $text_color_ety; ?>">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $field_set['EMPLOYEMENT_TYPE']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> type="radio" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php
} ?> value="<?php echo __($emptypes['genc_detail_name']);?>"/>
                                       <span></span>
                                       <?php echo __($emptypes['genc_detail_name']);?>
                                       </label>
                                       <?php } ?>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_ofph= $data['office_phone'] != $empdata['office_phone'] ? 'text-danger':"";

                                 $read_only_ofph = $field_set['OFFICE_PHONE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ofph = $field_set['OFFICE_PHONE']['display'] != 1 ? 'none' : '';

                                 $text_color_ext= $data['office_ext'] != $empdata['office_ext'] ? 'text-danger':"";

                                 $read_only_ext = $field_set['EXTENSION']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ext = $field_set['EXTENSION']['display'] != 1 ? 'none' : '';

                                 ?>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_ofph; ?>" >
                                    <span class="col-form-label">
                                       <?php echo __('Office Phone'); ?>
                                       <?php if($text_color_ofph!="") { ?>
                                       <div data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['office_phone']==''?__('No Data'):$empdata['office_phone'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </div>
                                       <?php } ?>
                                    </span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ofph; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_ofph; ?>" name="office_phone" placeholder="<?php echo __('Office Phone'); ?>" value="<?php echo $data['office_phone']; ?>"/>
                                       <div class="input-group-append"><span class="input-group-text"><span><i class="la la-phone"></i></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div  class="mt-2" style="display: <?php echo $display_div_ext; ?>" >
                                    <span class="col-form-label">
                                       <?php echo __('Extension'); ?>  <?php if($text_color_ext!="") { ?>
                                       <div data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['office_ext']==''?__('No Data'):$empdata['office_ext'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </div>
                                       <?php } ?>
                                    </span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $read_only_ext; ?> type="text" class="form-control form-control-lg  <?php echo $text_color_ext; ?>" name="office_ext" placeholder="<?php echo __('Extension'); ?>" value="<?php echo $data['office_ext']; ?>"/>
                                       <div class="input-group-append"><span class="input-group-text"><span><i class="la la-phone"></i></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <!-- end row-->
                           </div>

                        </div>
                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                           <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Personal Details'); ?></h4>
                           <?php
                              $text_color_bir= fromDate($data['date_of_birth']) != fromDate($empdata['date_of_birth']) ? 'text-danger':"";

                              $read_only_bir = $field_set['DATE_OF_BIRTH']['readonly'] != 0 ? 'readonly' : '';
                              $display_div_bir = $field_set['DATE_OF_BIRTH']['display'] != 1 ? 'none' : '';

                              ?>
                           <div class="row">
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
                                    <span class="col-form-label"><?php echo __('Date of Birth'); ?>  <?php if($text_color_bir!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['date_of_birth']==''?__('No Data'):$empdata['date_of_birth'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group date">
                                       <?php $date_pic1 = fromDate($data['date_of_birth']); ?>
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $field_set['DATE_OF_BIRTH']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> type="text" class="form-control form-control-lg  <?php echo $text_color_bir; ?>"  value="<?php echo  fromDate($date_pic1); ?>"  name="date_of_birth"  id="kt_datepicker_dob" />
                                       <div class="input-group-append">
                                          <span class="input-group-text">
                                          <i class="la la-calendar"></i>
                                          </span>
                                       </div>
                                    </div>
                                 </div>

                              </div>
                              <?php
                                 $diff =  strtotime(date("Y-m-d")) - strtotime($date_pic1) ;

                                 ?>
                              <div class="col-xl-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_bir; ?>" >
                                    <span class="col-form-label"><?php echo __('Age'); ?></span>
                                    <div class="input-group input-icon-right">
                                       <input aria-label="<?php echo __('Age'); ?>" readonly type="text" class="form-control form-control-lg " name="age" placeholder="<?php echo __('Age'); ?>" value="<?php echo floor($diff / (365*60*60*24)); ?>"/>
                                       <div class="input-group-append"><span class="input-group-text"><span><?php echo __('Years'); ?></span></span></div>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_gen= $data['gender'] != $empdata['gender'] ? 'text-danger':"";

                                 $read_only_gen = $field_set['GENDER']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_gen = $field_set['GENDER']['display'] != 1 ? 'none' : '';

                                 $text_color_ma= $data['marital_status'] != $empdata['marital_status'] ? 'text-danger':"";

                                 $read_only_ma = $field_set['MARITAL_STATUS']['readonly'] != 0 ? 'readonly' : '';
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
                              <div class="col-lg-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_gen; ?>" >
                                    <span class="col-form-label"><?php echo __('Gender'); ?>  <?php if($text_color_gen!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['gender']==""?__('No Data'): $exist_gender; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div <?php echo $read_only_gen; ?> class="radio-inline  ">
                                       <?php
                                          foreach ($gender_details as $gender_data) {
                                            ?>
                                       <label class="radio radio-solid <?php echo $text_color_gen; ?>">
                                       <input aria-label="<?php echo __('Input field'); ?>"  <?php echo $field_set['GENDER']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> type="radio" name="gender" <?php if($data['gender'] == $gender_data['genc_detail_code']) {?>  checked="checked" <?php
} ?> value="<?php echo $gender_data['genc_detail_code'];?>"/>
                                       <span></span>
                                       <?php echo __($gender_data['genc_detail_name']); ?>
                                       </label>
                                       <?php } ?>
                                    </div>
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
                              <div class="col-lg-6" >
                                 <div class="mt-2" style="display: <?php echo $display_div_ma; ?>" >
                                    <span class="col-form-label"><?php echo __('Marital Status'); ?>  <?php if($text_color_ma!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['marital_status'] == '' ? 'No Data' : $exist_marital_data; ?>"<i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div <?php echo $read_only; ?> class="radio-inline">
                                       <?php
                                          foreach ($marital_status_details as $marital_stat) {
                                            ?>
                                       <label   class="radio radio-solid <?php echo $text_color_ma; ?>">
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $field_set['MARITAL_STATUS']['readonly']!=0?ATTR_DISABLED_TRUE:'';?>  onclick="showmarriagedate(this.value);" type="radio" name="marital_status" <?php if($data['marital_status'] == $marital_stat['genc_detail_name']) {?> checked="checked" <?php
} ?> value="<?php echo $marital_stat['genc_detail_code']; ?>" onkeydown="" />
                                       <span></span>
                                       <?php echo __($marital_stat['genc_detail_name']); ?>
                                       </label>
                                       <?php } ?>
                                    </div>
                                 </div>
                              </div>
                              <?php
                                 $text_color_ms= fromDate($data['wedding_date']) != fromDate($empdata['wedding_date']) ? 'text-danger':"";
                                 $read_only_ms = $field_set['MARRIGE_DATE']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_ms = $field_set['MARRIGE_DATE']['display'] != 1 ? 'none' : '';

                                 $text_color_bg= $data['blood_group'] != $empdata['blood_group'] ? 'text-danger':"";
                                 $read_only_bg = $field_set['BLOOD_GROUP']['readonly'] != 0 ? 'readonly' : '';
                                 $display_div_bg = $field_set['BLOOD_GROUP']['display'] != 1 ? 'none' : '';
                                 ?>
                              <div class="col-lg-6" style="display: none"  id="marriagedateshow">
                                 <div class="mt-2" style="display: <?php echo $display_div_ms; ?>" >
                                    <span  class="col-form-label"><?php echo __('Marrige Date'); ?> <?php if($text_color_ms!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['wedding_date']==''?__('No Data'):$empdata['wedding_date'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?></span>
                                    <div class="input-group date">
                                       <?php $date_pic1 = fromDate($data['wedding_date']); ?>
                                       <input aria-label="<?php echo __('Input field'); ?>" <?php echo $field_set['MARRIGE_DATE']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> type="text" id="kt_datepicker_wedding" class="form-control form-control-lg  <?php echo $text_color_ms; ?>" placeholder="<?php echo __('Marrige Date'); ?>" name="wedding_date" value="<?php echo  $date_pic1; ?>"/>
                                       <div class="input-group-append">
                                          <span class="input-group-text">
                                          <i class="la la-calendar"></i>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div class="mt-2" style="display: <?php echo $display_div_bg; ?>" >
                                    <span class="col-form-label"><?php echo __('Blood Group'); ?>  <?php if($text_color_bg!="") { ?> <span data-toggle="tooltip" data-theme="dark" title="<?php echo $empdata['blood_group']==''?__('No Data'):$empdata['blood_group'] ; ?>"><i class="fa fa-comment-alt text-success mr-2 icon-nm"></i>   </span> <?php
} ?>
                                    </span>
                                    <select aria-label="<?php echo __('Select option'); ?>" <?php echo $field_set['BLOOD_GROUP']['readonly']!=0?ATTR_DISABLED_TRUE:'';?> class ="form-control form-control-lg  <?php echo $text_color_bg; ?>" name="blood_group">
                                       <?php foreach ($blood_groups_details as $blood_groups) {
                                          if($empdata['blood_group'] == $blood_groups['genc_detail_name']) {
                                             $empdata['blood_group'] = $blood_groups['genc_detail_name'];
                                          }
                                          ?>
                                       <option <?php if($data['blood_group'] == $blood_groups['genc_detail_name']){ ?>selected <?php
} ?> value="<?php echo __($blood_groups['genc_detail_name']); ?>" >
                                          <?php echo __($blood_groups['genc_detail_name']); ?>
                                       </option>
                                       <?php } ?>
                                    </select>
                                 </div>
                              </div>

                           </div>
                        </div>
                  </div>
                  <div class="offset-xxl-2 col-xxl-10">
                  <!--end: Wizard Step 5-->
                  <!--begin: Wizard Step 5-->
                  <div class="pb-5" data-wizard-type="step-content">
                  <h4 class="mb-5 font-weight-bold text-dark "><?php echo __('Experience Details'); ?></h4>
                  <div class="form-group row">
                  <div class="form-group col-lg-12">
                  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="extable">
                  <tr>
                  <th scope="col" style="width: 2%;"></th>
                  <th scope="col" class="" style="width: 28%;"><?php echo __('Company Name'); ?></th>
                  <th scope="col" class="thheader" style="width: 28%;"><?php echo __('job Descrption'); ?></th>
                  <th scope="col" class="" style="width: 20%;"><?php echo __('From'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('To'); ?></th>
                  </tr>
                  <?php $k=1; if(count((array)$exp_details)>0){
                     foreach ($exp_details as $expe) {

                        ?>
                  <tr>
                  <td>
                  <label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label>
                  </td>
                  <td>
                  <input type="hidden" name="expe_rowid[]" class="form-control " value="<?php echo $expe['expe_rowid']==''? $expe['id']:$expe['expe_rowid']; ?>">
                  <input aria-label="<?php echo __('Company Name[]'); ?>" type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>">  </td>
                  <td><input aria-label="<?php echo __('Job Descrption[]'); ?>" type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Work From[]'); ?>" type="text" name="work_from[]" class="form-control " id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo fromDate($expe['work_from']); ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Work To[]'); ?>" type="text" name="work_to[]" class="form-control " id="kt_datepicker_3_2<?php echo $k; ?>" value="<?php echo fromDate($expe['work_to']); ?>"autocomplete="off">
                  </td>
                  </tr>
                  <?php $k++; ?>
                  <?php } ?>
                  <?php } ?>
                  </table>
                  <div class="row">
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                  <button class="btn btn-danger" id="removeRows_exp" onclick="removeexprows()"  type="button">- <?php echo __('Delete'); ?></button> &nbsp;
                  <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows_exp" type="button">+ <?php echo __('Add More'); ?></button>
                  </div>
                  </div>
                  </div>
                  </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content" >
                  <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Qualification'); ?></h4>
                  <div class="row">
                  <div class="form-group col-lg-12">
                  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center " id="edutable">
                  <tr>
                  <th scope="col" style="width: 2%;"></th>
                  <th scope="col" class="" style="width: 25%;"><?php echo __('Institute Name'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('Type'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('Start Date'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('End Date'); ?></th>
                  <th scope="col" class="thheader" style="width: 15%;"><?php echo __('Score'); ?></th>
                  </tr>
                  <?php $k=1; if(count((array)$edu_details)>0){
                     foreach ($edu_details as $inst) { ?>
                  <tr>
                  <td>
                  <label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label>
                  </td>
                  <td>
                  <input type="hidden" name="institute_rowid[]" class="form-control " value="<?php echo $inst['institute_rowid']==''? $inst['id']:$inst['institute_rowid']; ?>">
                  <input aria-label="<?php echo __('Institute Name[]'); ?>" type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off">  </td>
                  <td>
                  <select aria-label="<?php echo __('Education Type[]'); ?>" class="form-control" name="education_type[]" >
                  <option value="0"> <?php echo __('Select Any One'); ?></option>
                  <?php foreach ($edutypedata as  $edutype) {   ?>
                  <option  <?php if($inst['education_type']== $edutype['genc_detail_code']){  ?> selected <?php
} ?>   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option>
                  <?php   } ?>
                  <option value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo __($edutype['genc_detail_name']); ?></option>
                  </select>
                  </td>
                  <td><input aria-label="<?php echo __('Start Date[]'); ?>" type="text" name="start_date[]" class="form-control "  id="kt_datepicker_3_3<?php echo $k; ?>" value="<?php echo fromDate($inst['start_date']); ?>"  autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('End Date[]'); ?>" type="text" name="end_date[]" class="form-control " id="kt_datepicker_3_4<?php echo $k; ?>"  value="<?php echo fromDate($inst['end_date']); ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Score[]'); ?>" type="text" name="score[]" class="form-control "  value="<?php echo $inst['score']; ?>"  autocomplete="off">  </td>
                  </tr>
                  <?php $k++; ?>
                  <?php } ?>
                  <?php } ?>
                  </table>
                  <div class="row">
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                  <button class="btn btn-danger" id="removeRows_edu" onclick="removeedurows()"  type="button">- <?php echo __('Delete'); ?></button> &nbsp;
                  <button class="btn btn-success" onclick="addedurows()" id="addRows_edu" type="button">+ <?php echo __('Add More'); ?></button>
                  </div>
                  </div>
                  </div>
                  </div>
                  </div>
                  <div class="pb-5" data-wizard-type="step-content" >
                  <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Dependents'); ?></h4>
                  <div class="row">
                  <div class="form-group col-lg-12">
                  <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer text-center" id="deptable">
                  <tr>
                  <th scope="col" style="width: 2%;"></th>
                  <th scope="col" class="" style="width: 25%;"><?php echo __('Name'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('Relationship Type'); ?></th>
                  <th scope="col" class="thheader" style="width: 20%;"><?php echo __('DOB'); ?></th>
                  </tr>
                  <?php $k=1; if(count((array)$dep_details)>0){
                     foreach ($dep_details as $depe) { ?>
                  <tr>
                  <td>
                  <label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label>
                  </td>
                  <input type="hidden" name="dependent_rowid[]" class="form-control " value="<?php echo $depe['dependent_rowid']==''? $depe['id']:$depe['institute_rowid']; ?>">
                  <td><input aria-label="<?php echo __('Dependent Name[]'); ?>" type="text" name="dependent_name[]" class="form-control " value="<?php echo $depe['dependent_name']; ?>" autocomplete="off">  </td>
                  <td>
                  <select aria-label="<?php echo __('Dependent Relationship Type[]'); ?>" class="form-control" name="dependent_relationship_type[]" >
                  <option value="0"> <?php echo __('Select Any One'); ?></option>
                  <?php foreach ($relatypedata as  $reladata) {   ?>
                  <option   <?php if($depe['dependent_relationship_type']== $reladata['id']){  ?> selected <?php
} ?> value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata['relation_name']); ?></option>
                  <?php   } ?>
                  <option value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata['relation_name']); ?></option>
                  </select>
                  </td>
                  <td><input aria-label="<?php echo __('Dependent Date Of Birth[]'); ?>" type="text" name="dependent_date_of_birth[]" class="form-control " id="kt_datepicker_dep_dob<?php echo $k; ?>" value="<?php echo fromDate($depe['dependent_date_of_birth']); ?>" autocomplete="off">  </td>
                  </tr>
                  <?php $k++; ?>
                  <?php } ?>
                  <?php } ?>
                  </table>
                  <div class="row">
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                  <button class="btn btn-danger " id="removeRows_dep" onclick="removedeprows()"  type="button">- <?php echo __('Delete'); ?></button> &nbsp;
                  <button class="btn btn-success"  onclick="adddeprows()" id="addRows_dep" type="button">+ <?php echo __('Add More'); ?></button>
                  </div>
                  </div>
                  </div>
                  </div>
                  </div>
                  <!--end: Wizard Step 5-->
                  <!--begin: Wizard Step 6-->
                  <div class="pb-5" data-wizard-type="step-content">
                  <h4 class="mb-5 font-weight-bold text-dark"><?php echo __('Emergency Contact'); ?></h4>
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
                  <?php if(count((array)$emerge_details)>0){
                     foreach ($emerge_details as $eno) { ?>
                  <tr>
                  <td>
                  <label class="checkbox checkbox-primary"><input aria-label="<?php echo __('Checkbox'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label>
                  </td>
                  <input type="hidden" name="contact_rowid[]" class="form-control " value="<?php echo $eno['contact_rowid']==''? $eno['id']:$eno['institute_rowid']; ?>">

                  <td>
                  <input aria-label="<?php echo __('Contact Name[]'); ?>" type="text" name="contact_name[]" class="form-control " value="<?php echo $eno['contact_name']; ?>" autocomplete="off">
                  </td>
                  <td>
                  <select aria-label="<?php echo __('Contact Relationship[]'); ?>" class="form-control" name="contact_relationship[]" >
                  <option value="<?php echo $eno['contact_name']; ?>"> <?php echo $eno['contact_name']; ?></option>
                  <option value="0"> <?php echo __('Select Any One'); ?></option>
                  <?php foreach ($relatypedata as  $reladata) {   ?>
                  <option   <?php if($eno['contact_relationship']== $reladata['id']){  ?> selected <?php
} ?> value="<?php echo $reladata['id']; ?>"> <?php echo __($reladata['relation_name']); ?></option>
                  <?php   } ?>
                  </select>
                  </td>
                  <td><input aria-label="<?php echo __('Contact Home No[]'); ?>" type="text" name="contact_home_no[]" class="form-control " value="<?php echo $eno['contact_home_no']; ?>" autocomplete="off">  </td>
                  <td><input aria-label="<?php echo __('Contact Mobile No[]'); ?>" type="text" name="contact_mobile_no[]" class="form-control " value="<?php echo $eno['contact_mobile_no']; ?>" autocomplete="off">  </td>

                  </tr>
                  <?php } ?>
                  <?php } ?>
                  </table>
                  <div class="row">
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
                  <button class="btn btn-danger" id="removeRows_eme" onclick="removeemerows()"  type="button">- <?php echo __('Delete'); ?></button> &nbsp;
                  <button class="btn btn-success "  onclick="addemrgerows()" id="addRows_eme" type="button">+ <?php echo __('Add More'); ?></button>
                  </div>
                  </div>
                  </div>
                  </div>
                  </div>
                  <!--end: Wizard Step 6-->
                  <!--begin: Wizard Actions-->
                  <div class="d-flex justify-content-between border-top mt-5 pt-10">
                  <div class="mr-2">
                  <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev"><?php echo __('Previous'); ?></button>
                  </div>
                  <div>
                  <?php if($_SESSION['userid']==$hrconnect_id) { ?>
                  <input type="hidden" name="submit_type" value="hr">
                  <?php } ?>
                  <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-submit"><?php echo __('Save'); ?></button>
                  <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next"><?php echo __('Next'); ?></button>
                  </div>
                  </div>
                  </div>
                  <!--end: Wizard Actions-->
                  </form>
               </div>
               <!--end: Wizard-->
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

</div>
<!--end::Content-->
<!--begin::Footer-->
