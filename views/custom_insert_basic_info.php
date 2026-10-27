<?php
if(isset($_POST['emp_id'])){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
}
include_once "new_from_submit.php";
$_SESSION['apply_js']['table']= 1;
$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
if(isset($_POST['emp_id']) && $_POST['emp_id']!=""){
  $employee_id = $_POST['emp_id'];
}else{
  $employee_id = $_SESSION['emp_id'];
}
$url = API."?mode=employeedetails&emp_id=".$employee_id;
$response = callAPI($url);
$empdata = json_decode($response,1);
$data = $empdata['emp_data'];
$teams =  $empdata['teams'];
$asserts = $empdata['teams'];
$documents =  $empdata['documents'];
foreach($empdata['marital_statuses'] as $maritalstat){
  $marital_statuses[$maritalstat['id']] = $maritalstat['marital_status_name'];
}
 foreach($empdata['blood_groups'] as $bloodgroup){
  $bloodgroups[$bloodgroup['id']] = $bloodgroup['blood_group'];
}
$vendor_display_fields = $empdata['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'],1);
?>
<script type="text/javascript">
   function displayitemdata(option,id){
      console.log("dataaaaa");
      jQuery.ajax({
           url: "views/view_roll_back1.php",
           data:'option='+option+'&feild=emp_id&view_reload=1&value='+$('#employee_id').val(),
           type: "POST",
           success:function(data){
                     console.log("page respons");
                      $("#"+id).html(data);
           },
           error:function (){}
      });
   }
   function statuspopup(status,id){
     var table_name = document.getElementById('table_name').value;
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
   function changebillable(id,name){

      var billable=  $("#billable_"+id).val();
      var status = "";
      var color = "";
      if(billable==0){ billable =1; status = "Non Billable"; color="red";  }else{ billable = 0; status = "Billable";  }
      $(".modallabel").text("Billable OR Non-Billable");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("Billable and Non Billable");
      $("#field_value").val(billable);
      $('#modal_status').modal('show');
      $("#billable_"+id).val(billable);
      $(".billable_lable"+id).text(status);
       $(".billable_lable"+id).css("color",color);

  }

function changeonsite(id,name){
      var onsite=  $("#onsite_"+id).val();
      var status = "";
      var color = "";
      if(onsite==0){ onsite =1;  status = "Off Site"; color="red"; }else{ onsite = 0; status = "On Site";}
      $(".modallabel").text("On Site OR Off Site");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("Offsite and Onsite");
      $("#field_value").val(onsite);
      $('#modal_status').modal('show');
      $("#onsite_"+id).val(onsite);
    $(".onsite_lable"+id).text(status);
      $(".onsite_lable"+id).css("color",color);
   }


function save_statuse_bilable_onsite(){
   $.ajax({
   type: 'post',
   url: 'ajax.php',
   data: $('#insertemphistries').serialize()+"&page=insertemphistries",
   success: function (response) {
      $('#modal_status').modal('hide');

      }
   });
}





</script>


<?php if(isset($_POST['emp_id'])){  ?>

<div class="modal-header">
    <h5 class="modal-title">
        Basic Info
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<?php } ?>


<div class="d-flex flex-column-fluid">
   <div class="container-fluid">

<div class=""> <!-- resolution class remove d-flex flex-row -->
   <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employee_id;  ?>">
   <!--begin::Content-->
   <div class="flex-row-fluid ml-lg-12 col-lg-12 j-content pt-2">
      <!--begin::Row-->
      <div class="row">
         <div class="card-body card-custom">
          <div class="row">
             <div class="col-12 col-lg-2 mt-15">
                  <ul class="nav nav-tabs md-tabs tabs-left b-none custom_basic_info infisuite_tab">
                     <li class="nav-item ">
                        <a class="nav-link active besic-info-txt-start" id="home-tab-5" data-bs-toggle="tab" href="#home-5" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                        <span class="nav-text">Basic Info</span>
                        </a>
                          <div class="slide"></div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link besic-info-txt-start" id="home-tab-6" data-bs-toggle="tab" href="#home-6" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                        <span class="nav-text">My Team</span>
                        </a>
                          <div class="slide"></div>
                     </li>
                     <li class="nav-item">
                      <a class="nav-link besic-info-txt-start" id="home-tab-7" data-bs-toggle="tab" href="#home-7"  role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                        <span class="nav-text">Calendar</span>
                        </a>
                         <div class="slide"></div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link besic-info-txt-start" id="home-tab-9" data-bs-toggle="tab" href="#home-9" onclick="displayitemdata('COM_LIST_EMP_ASSETS', 'display_side_assets')"  role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                        <span class="nav-text">Assets</span>

                        </a>
                        <div class="slide"></div>
                     </li>
                    <?php ?>
                      <?php if(in_array('8',(array)$_SESSION['module_list']) || $data['employee_type'] == '1'){ ?>
                    <li class="nav-item">
                        <a class="nav-link besic-info-txt-start" id="home-tab-70" data-bs-toggle="tab" href="#home-70" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-text">Pay<?php //echo $pass_module['0']['page']; ?> </span>
                        </a>
                          <div class="slide"></div>
                     </li>
                     <?php } ?>




                  </ul>
               </div>
               <div class="col-12 col-lg-10">
                  <div class="tab-content" id="myTabContent5">
                     <div class="tab-pane fade active show" id="home-5" role="tabpanel" aria-labelledby="home-tab-5">
                      <ul class="nav md-tabs infisuite_tab">
                           <?php
                            $fieldaccesright = $vafield['basic_information'];
                            if($fieldaccesright['access'] !="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link active"   data-bs-toggle="tab" href="#kt_tab_pane_1" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                              <span class="nav-text">Basic Info</span>
                              </a>
                           </li>
                         <?php } ?>
                         <?php
                            $fieldaccesright = $vafield['work'];
                            if($fieldaccesright['access'] !="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <span class="nav-text">Work</span>
                              </a>
                           </li>
                          <?php } ?>
                          <?php
                            $fieldaccesright = $vafield['personal_details'];
                            if($fieldaccesright['access'] !="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                              <span class="nav-text">Personal</span>
                              </a>
                           </li>
                           <?php } ?>
                           <?php
                            $fieldaccesright = $vafield['experience_details'];
                            if($fieldaccesright['field']['employee_experiances']!="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_4" onclick="displayitemdata('COM_LIST_EMP_WORK_EXPERIENCES', 'display_wrok')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                              <span class="nav-text">Employment History</span>
                              </a>
                           </li>
                           <?php } ?>
                           <?php
                            $fieldaccesright = $vafield['employee_qualifications'];
                            if($fieldaccesright['field']['employee_qualifications']!="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5"  onclick="displayitemdata('COM_LIST_EMP_EDUCATIONS', 'display_education')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                              <span class="nav-text">Education Details</span>
                              </a>
                           </li>
                           <?php } ?>
                           <?php
                            $fieldaccesright = $vafield['employee_dependents'];
                            if($fieldaccesright['field']['employee_dependents']!="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6" onclick="displayitemdata('COM_LIST_EMP_DEPENDENTS', 'display_dependents')"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                              <span class="nav-text">Dependent</span>
                              </a>
                           </li>
                           <?php } ?>
                            <?php
                            $fieldaccesright = $vafield['employee_contacts'];
                            if($fieldaccesright['field']['employee_contacts']!="NA"){ ?>
                           <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_7" onclick="displayitemdata('COM_LIST_EMP_CONTACTS', 'display_contacts')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                              <span class="nav-text">Emergency Contact</span>
                              </a>
                           </li>
                           <?php } ?>
                            <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                 <span class="nav-text">Personal Details</span>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <?php
                                $fieldaccesright = $vafield['proof_details'];
                                if($fieldaccesright['field']['proof_details']!="NA"){ ?>
                                 <a class="  dropdown-item" href="#kt_tab_pane_9"   data-bs-toggle="tab" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Identity Cards</a>
                               <?php } ?>
                               <?php
                                $fieldaccesright = $vafield['emp_visa'];
                                if($fieldaccesright['field']['emp_visa']!="NA"){ ?>
                                 <button type="button" class="  dropdown-item" kt_tab_pane_10"   data-bs-toggle="tab" aria-haspopup="true" aria-expanded="false" onclick="displayitemdata('COM_LIST_EMP_visa', 'display_visa')">Visa Details</button>
                                 <?php
                                }
                                $fieldaccesright = $vafield['emp_skills'];
                                if($fieldaccesright['field']['emp_skills']!="NA"){ ?>
                                 <button type="button" class="  dropdown-item" kt_tab_pane_11"   data-bs-toggle="tab" aria-haspopup="true" aria-expanded="false" onclick="displayitemdata('COM_LIST_EMP_SKILLS', 'display_skill')">Skill Details</button>
                                <?php } ?>
                              </div>
                           </li>
                           <?php  ?>

                        </ul>
                        <?php if($employee_id == $_SESSION['emp_id']){ ?>
                        <a href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES" class="btn btn-primary float-end" > <i class="ti-pencil-alt"></i> Employee Details </a>
                      <?php } ?>
                        <div class="tab-content mt-5" id="myTabContent">
                           <div class="tab-pane fade  show active" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                              <form class="form">
                                <div class="card-body p-2">


                           <div class="row">
                              <div class="col-lg-12 col-xl-6">
                                 <div class="table-responsive">
                                    <table class="table m-0">
                                       <tbody>
                                          <?php
                                            $fieldaccesright = $vafield['basic_information'];
                                            if($fieldaccesright['field']['emp_code']!="NA"){
                                          ?>
                                          <tr>
                                             <th scope="row">Employee Code</th>
                                             <td><?php echo emptytextdashShow($data['emp_code']); ?></td>
                                          </tr>
                                        <?php  }if($fieldaccesright['field']['middle_name']!="NA"){ ?>
                                          <tr>
                                             <th scope="row">Middle Name</th>
                                             <td><?php echo emptytextdashShow($data['middle_name']); ?></td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['phone']!="NA"){ ?>
                                          <tr>
                                             <th scope="row">Contact</th>
                                             <td><?php echo emptytextdashShow($data['phone']); ?></td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['address']!="NA"){ ?>
                                          <tr>
                                             <th scope="row">Permanent Address</th>
                                             <td class="address-basic" style="line-height:normal;"><?php echo emptytextdashShow($data['address']); ?></td>
                                          </tr>
                                          <?php  }  ?>
                                          <?php if($fieldaccesright['field']['contact_address']!="NA"){ ?>
                                          <tr>
                                             <th scope="row">Current Address</th>
                                             <td class="address-basic" style="line-height:normal;"><?php echo emptytextdashShow($data['c_address']); ?></td>
                                          </tr>
                                          <?php  }  ?>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                              <div class="col-lg-12 col-xl-6">
                                 <div class="table-responsive">
                                    <table class="table m-0">
                                       <tbody>
                                        <?php if($fieldaccesright['field']['first_name']!="NA"){  ?>
                                          <tr>
                                             <th scope="row">First Name</th>
                                             <td><?php echo emptytextdashShow($data['first_name']); ?></td>
                                          </tr>
                                        <?php }if($fieldaccesright['field']['last_name']!="NA"){  ?>
                                          <tr>
                                             <th scope="row">Last Name</th>
                                             <td><?php echo emptytextdashShow($data['last_name']); ?></td>
                                          </tr>
                                           <?php }if($fieldaccesright['field']['email_id']!="NA"){  ?>
                                          <tr>
                                             <th scope="row">Email</th>
                                             <td><?php echo emptytextdashShow($data['email_id']); ?></td>
                                          </tr>
                                           <?php }if($fieldaccesright['field']['post_code']!="NA"){  ?>
                                          <tr>
                                             <th scope="row">Postcode</th>
                                             <td><?php echo emptytextdashShow($data['post_code']); ?></td>
                                          </tr>
                                           <?php }if($fieldaccesright['field']['fax']!="NA"){  ?>
                                          <tr>
                                             <th scope="row">Fax</th>
                                             <td><?php echo emptytextdashShow($data['fax']); ?></td>
                                          </tr>
                                          <?php  } ?>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>



                                </div>
                              </form>
                            </div>


                            <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                              <form class="form" method="post">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col-lg-12 col-xl-6">
                                          <div class="table-responsive">
                                             <table class="table m-0">
                                                <tbody>
                                                  <?php
                                                    $fieldaccesright = $vafield['work'];
                                                    if($fieldaccesright['field']['department_id']!="NA"){
                                                  ?>
                                                   <tr>
                                                      <th scope="row">Department</th>
                                                      <?php foreach ($empdata['departments'] as $departments) {
                                                        $department[$departments['id']] = $departments['department_name'];
                                                        } ?>
                                                      <td><?php echo emptytextdashShow($department[$data['department_id']]); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['designation_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Designation</th>
                                                      <td>
                                                         <?php foreach ($empdata['designations'] as $designations) {
                                                           $designation[$designations['id']] = $designations['designation_name'];
                                                          } ?>

                                                         <?php echo emptytextdashShow($designation[$data['designation_id']]);?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['band_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Band</th>
                                                      <td>
                                                         <?php $bandval =""; foreach ($empdata['employment_bands'] as $employment_bands) { if($data['band_id'] == $employment_bands['id']){ $bandval = $employment_bands['band_name'];
}
} echo emptytextdashShow($bandval); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['location_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Work Location</th>
                                                      <td>
                                                         <?php $worklocaton = "";foreach ($empdata['locations'] as $locations) { if($data['location_id'] == $locations['id']){ $worklocaton = $locations['location_name'];
}
} echo emptytextdashShow($worklocaton); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['office_phone']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Office Phone</th>
                                                      <td>
                                                         <?php echo emptytextdashShow($data['office_phone']); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['hiring_source']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Hiring Source</th>
                                                      <td><?php echo emptytextdashShow($data['hiring_source']); ?></td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['total_experiance']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Total Experience</th>
                                                      <td><?php echo emptytextdashShow($data['total_experiance']); ?> - Year(s)</td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['relevant_experience']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Relevant Experience</th>
                                                      <td><?php echo emptytextdashShow($data['relevant_experience']); ?> - Year(s)</td>
                                                   </tr>

                                                   <?php }if($fieldaccesright['field']['employee_type']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Employment Type</th>
                                                      <td>
                                                         <div class="radio radio-outline mt-1">
                                                            <label class="radio radio-inline">
                                                            <input aria-label="Employee Type" readonly type="radio" name="employee_type" <?php if($data['employee_type'] == '1') {?> checked="checked" <?php
}else{ echo "disabled";
} ?> value="1" />
                                                            <span></span>
                                                            Permanent
                                                            </label>
                                                            <label class="radio radio-inline">
                                                            <input aria-label="Radio option" readonly type="radio"  <?php if($data['employee_type'] == '2') {?> checked="checked" <?php
}else{ echo "disabled";
} ?> name="employee_type" value="2" />
                                                            <span></span>
                                                            Contract
                                                            </label>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['billable']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Billable</th>
                                                      <td>
                                                         <div class="radio radio-outline  ">
                                                            <label class="radio radio-inline">
                                                            <input aria-label="Billable" readonly  name="billable" type="radio" <?php if($data['billable'] == 0) {?> checked="checked" <?php
}else{ echo "disabled";
} ?> value="0" />
                                                            <span></span>
                                                            Yes
                                                            </label>
                                                            <label class="radio radio-inline">
                                                            <input aria-label="Billable" readonly type="radio"  name="billable" <?php if($data['billable'] == 1) {?> checked="checked" <?php
}else{ echo "disabled";
} ?> name="employee_type" value="1" />
                                                            <span></span>
                                                            No
                                                            </label>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                 <?php } ?>
                                                </tbody>
                                             </table>
                                          </div>
                                       </div>
                                       <div class="col-lg-12 col-xl-6">
                                          <div class="table-responsive">
                                             <table class="table m-0">
                                                <tbody>
                                                  <?php if($fieldaccesright['field']['vertical_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Vetical</th>
                                                      <td>
                                                         <?php $vertical=""; foreach ($empdata['verticals'] as $verticals) { if($data['vertical_id'] == $verticals['id']){ $vertical = $verticals['vertical_name'];
}
} echo emptytextdashShow($vertical); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['role_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Role</th>
                                                      <td>
                                                         <?php $employment_role =""; foreach ($empdata['employment_roles'] as $employment_roles) { if($data['role_id'] == $employment_roles['id']){ $employment_role= $employment_roles['role_name'];
}
} echo emptytextdashShow($employment_role); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['level_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Level</th>
                                                      <td><?php $employment_level="";  foreach ($empdata['employment_levels'] as $employment_levels) {   if($data['level_id'] == $employment_levels['id']){  $employment_level = $employment_levels['level_name'];
}
}  echo emptytextdashShow($employment_level); ?></td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['seat_location']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Seat Location</th>
                                                      <td>
                                                         <?php $location = ""; foreach ($empdata['locations'] as $locations) { if($data['seat_location'] == $locations['id']){ $location =  $locations['location_name'];
}
} echo emptytextdashShow($location); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['office_ext']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Extension</th>
                                                      <td>
                                                         <?php echo emptytextdashShow($data['office_ext']); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['joining_date']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Date of Joining</th>
                                                      <td><?php $date_pic = $data['joining_date'] !='0000-00-00'?$data['joining_date']:nowDate(); echo dateFormate($date_pic); ?></td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['experience_current_organization']!="NA"){  ?>
                                                   <tr>
                                                      <th class="" scope="row">Exp in</th>
                                                      <td>
                                                         <?php echo $data['experience_current_organization'] !=""?$data['experience_current_organization']." - Year(s)":" - "; ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['notice_period']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Notice Period</th>
                                                      <td>
                                                         <?php echo $data['notice_period'] !=""?$data['notice_period']."- Day(s)":" - "; ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['reporting_manager_id']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Repoting to</th>
                                                      <td>
                                                         <?php $reportto = ""; foreach ($empdata['application_users'] as $application_users) { if($data['reporting_manager_id'] == $application_users['id']){ $reportto =  $application_users['display_name'];
}
} echo emptytextdashShow($reportto); ?>
                                                      </td>
                                                   </tr>
                                                   <?php } ?>
                                                </tbody>
                                             </table>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </form>
                            </div>
                             <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                              <form class="form">
                                 <div class="card-body">

                                    <div class="row">
                                       <div class="col-lg-12 col-xl-6">
                                          <div class="table-responsive">
                                             <table class="table m-0">
                                                <tbody>
                                                  <?php
                                                    $fieldaccesright = $vafield['personal_details'];
                                                    if($fieldaccesright['field']['date_of_birth']!="NA"){
                                                  ?>
                                                   <tr>
                                                      <th scope="row">Date of Birth</th>
                                                      <td>
                                                         <?php  echo dateFormate($data['date_of_birth']); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['marital_status']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Marital Status</th>
                                                      <td>
                                                       <?php echo emptytextdashShow($marital_statuses[$data['marital_status']]); ?>
                                                      </td>
                                                  </tr>
                                                  <?php }if($fieldaccesright['field']['gender']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Gender</th>
                                                      <td>
                                                        <?php
                                                          if($data['gender'] == "M"){
                                                            echo "Male";
                                                          }elseif($data['gender'] == "F"){
                                                            echo "Female";
                                                          }else{
                                                            echo "-";
                                                          }
                                                        ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['soc_facebook']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Facebook Link</th>
                                                      <td>
                                                         <?php  echo emptytextdashShow($data['soc_facebook']);  ?>
                                                      </td>
                                                   </tr>
                                                 <?php } ?>
                                                </tbody>
                                             </table>
                                          </div>
                                       </div>
                                       <div class="col-lg-12 col-xl-6">
                                          <div class="table-responsive">
                                             <table class="table m-0">
                                                <tbody>
                                                  <?php if($fieldaccesright['field']['date_of_birth']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Age</th>
                                                      <td>
                                                         <?php $diff =  strtotime(date("Y-m-d")) - strtotime($data['date_of_birth']) ;
                                                          ?>
                                                         <?php echo floor($diff / (365*60*60*24)); ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['blood_group']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Blood Group</th>
                                                      <td>
                                                         <?php   ?>

                                                                <?php echo $bloodgroups[$data['blood_group']]!=""?$bloodgroups[$data['blood_group']]:"-";?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['soc_linkedin']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Linkedin Link</th>
                                                      <td>
                                                         <?php  echo emptytextdashShow($data['soc_linkedin']);  ?>
                                                      </td>
                                                   </tr>
                                                   <?php }if($fieldaccesright['field']['soc_twitter']!="NA"){  ?>
                                                   <tr>
                                                      <th scope="row">Twitter Link</th>
                                                      <td>
                                                         <?php  echo emptytextdashShow($data['soc_twitter']);  ?>
                                                      </td>
                                                   </tr>
                                                 <?php } ?>
                                                </tbody>
                                             </table>
                                          </div>
                                       </div>
                                    </div>


                                  </div>
                               </form>
                             </div>
                             <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel" aria-labelledby="kt_tab_pane_4">

                              <div id="display_wrok">

                               <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel" aria-labelledby="kt_tab_pane_5">
                              <div id="display_education">
                                 <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_6" role="tabpanel" aria-labelledby="kt_tab_pane_6">

                              <div id="display_dependents">

                               <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>

                           <div class="tab-pane fade" id="kt_tab_pane_7" role="tabpanel" aria-labelledby="kt_tab_pane_7">
                              <div id="display_contacts">

                               <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_8" role="tabpanel" aria-labelledby="kt_tab_pane_8">
                              Other
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_9" role="tabpanel" aria-labelledby="kt_tab_pane_9">
                              <form class="form j-pro">
                                 <div class="card-body">
                                    <div class="form-group row table-responsive">
                                     <table class="table table-vertical-center">
                                       <thead class="thead-light">
                                          <tr>
                                             <th class="text-center" scope="col">#</th>
                                             <th class="text-center" scope="col">Type</th>
                                              <th class="text-center"  scope="col">Number</th>

                                             <th  class="text-center" scope="col">Expiry Date</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php
                                            $fieldaccesright = $vafield['proof_details'];
                                            if($fieldaccesright['field']['adhar_no']!="NA"){
                                          ?>
                                          <tr>
                                             <td scope="row">1</td>
                                             <td>
                                                 <input aria-label="Input field" type="text" name="" disabled class="form-control" value="Aadhar Card">
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['adhar_no']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text" name="" disabled class="form-control" value="<?php echo ""; ?>" disabled>
                                             </td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['passport']!="NA"){  ?>
                                          <tr>
                                             <td scope="row">2</td>
                                             <td>
                                                   <input aria-label="Input field" type="text" name="" disabled class="form-control" value="Passport" disabled>
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['passport']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['passport_expiry_date']!="0000-00-00"?$data['passport_expiry_date']:""; ?>" disabled>
                                             </td>
                                          </tr>
                                         <?php }if($fieldaccesright['field']['driving_licence']!="NA"){  ?>
                                          <tr>
                                             <td scope="row">3</td>
                                             <td>
                                                   <input aria-label="Input field" type="text" name="" disabled class="form-control" value="Driving">
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['driving_licence']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['driving_licence_expiry_date']!="0000-00-00"?$data['driving_licence_expiry_date']:""; ?>" disabled>
                                             </td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['pan_number']!="NA"){  ?>
                                          <tr>
                                             <td scope="row">4</td>
                                             <td>
                                                   <input aria-label="Input field" type="text" name="" disabled class="form-control" value="Pan Number">
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['pan_number']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text"  disabled name="" class="form-control" disabled>
                                             </td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['pf_no']!="NA"){  ?>
                                          <tr>
                                             <td scope="row">5</td>
                                             <td>
                                                   <input aria-label="Input field" type="text" name="" disabled class="form-control" value="PF Number">
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['pf_no']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text" disabled name="" class="form-control" disabled>
                                             </td>
                                          </tr>
                                          <?php }if($fieldaccesright['field']['pf_uan']!="NA"){  ?>
                                           <tr>
                                             <td scope="row">6</td>
                                             <td>
                                                   <input aria-label="Input field" type="text" name="" disabled class="form-control" value="UAN Number">
                                             </td>
                                             <td>
                                                <input aria-label="Input field" type="text" name="" class="form-control" value="<?php echo $data['pf_uan']; ?>" disabled>
                                             </td>

                                             <td>
                                                <input aria-label="Input field" type="text" disabled name="" class="form-control" disabled>
                                             </td>
                                          </tr>
                                        <?php } ?>
                                       </tbody>
                                    </table>
                                  </div>
                               </form>
                           </div>
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_10" role="tabpanel" aria-labelledby="kt_tab_pane_10">
                              <div id="display_visa">

                               <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="kt_tab_pane_11" role="tabpanel" aria-labelledby="kt_tab_pane_11">
                              <div id="display_skill">

                               <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                                    <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                              </div>
                              </div>
                           </div>
                          </div>
                        </div>
                        <div class="tab-pane fade" id="home-6" role="tabpanel" aria-labelledby="home-tab-6">
                              <?php if(isset($teams['reproter']['id'])){  $report =$teams['reproter'] ; ?>
                        <div class="row">

                           <h4 class="f-w-700 mb-2"> Reporting Manager </h4>
                           <div class="col-xl-3 col-md-6">
                              <div class="card sale-card ">
                                 <div class="card-header">
                                    <h5><a href="#" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0"><?php echo ucwords($report['display_name']);?></a></h5>
                                 </div>
                                 <div class="card-block text-center">

                                    <div data-label="<?php echo initials($report['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
                                    <?php if($report['image']!=""){ ?>
                                      <img src="uploads/all/<?php echo $report['image']; ?>" alt="Attachment Preview">
                                    <?php } ?>

                                        </div>
                                    <div class="row">
                                          <span class="text-muted font-weight-bold f-12"> <?php  echo $emp_department[$report['department_id']]; ?></span>
                                          <?php if($report['email_id'] !=""){?>
                                          <span class="text-muted font-weight-bold f-12"><?php echo '<i class="ti-email"></i> '.$report['email_id'];?></span>
                                        <?php } ?>
                                          <?php if($report['phone'] !=""){?>
                                            <span class="text-muted font-weight-bold f-12"><?php echo '<i class="fa fa-phone"></i> '.$report['phone'];?></span>
                                          <?php } ?>
                                          <span class="text-muted font-weight-bold f-12"><?php  echo $emp_designation[$team['designation_id']]; ?></span>

                                    </div>
                                 </div>
                              </div>
                           </div>





                        </div>
                        <?php }if(count((array)$teams['teams']) !=0){ ?>
                        <div class="mt-10">
                          <h4 class="f-w-700 mb-2">Teams</h4>


                           <div class="row">



<?php foreach ($teams['teams'] as $team) {  ?>

   <div class="col-xl-3 col-md-6">
        <div class="card sale-card ">
            <div class="card-header">
                <h5><?php echo ucwords($team['display_name']);?></h5>
            </div>
            <div class="card-block text-center">
                <div data-label="<?php echo initials($team['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
            <?php if($team['image']!=""){ ?>
              <img src="uploads/all/<?php echo $team['image']; ?>" alt="Attachment Preview">
            <?php } ?>

                </div>
                  <div class="row  mb-3">




       <span class="text-muted font-weight-bold f-12">&nbsp;<?php  echo $emp_designation[$team['designation_id']]; ?></span>

       <span class="text-muted font-weight-bold f-12"><?php echo $team['email_id']!=""?'<i class="ti-email"></i> '.$team['email_id']:"";?></span>

       <span class="text-muted font-weight-bold f-12">&nbsp;<?php echo $team['phone']!=""?'<i class="fa fa-phone"></i> '.$team['phone']:"";?></span>

                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="s-cont d-inline-block">
    <input aria-label="Checkbox" type="checkbox" class="js-success-sm" <?php if($team['on_off_site']==0){ echo "checked";
} ?> onchange="changeonsite(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;<span style="font-size: 12px;
 <?php if($team['on_off_site']==1){ echo "color: red";
} ?>" class="col-form-label onsite_lable<?php echo $team['id']; ?>">
<?php if($team['on_off_site']==1){ echo "Off-Site";
}else{ echo "On-Site";
} ?> </span>
      <input type="hidden" id="onsite_<?php echo $team['id']; ?>" value="<?php echo $team['on_off_site']; ?>">

                        </div>
                    </div>
                    <div class="col-6 b-l-default">
                        <div class="s-cont d-inline-block">

            <input aria-label="Checkbox" type="checkbox" class="js-primary-sm" <?php if($team['billable']=='0'){ echo "checked";
} ?> onchange="changebillable(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;

            <span style="font-size: 12px; <?php if($team['billable']=='1'){ echo "color: red";
} ?>" class="col-form-label billable_lable<?php echo $team['id']; ?>" >
 <?php if($team['billable']=='1'){ echo "Non Billable";
}else{ echo "Billable";
} ?> </span>

          <input type="hidden" id="billable_<?php echo $team['id']; ?>" value="<?php echo $team['billable']; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>




                           </div>
                        </div>
                        <?php }else{  ?>
            <div class="card-px text-center ">
              <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Team</p>
              <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
              </div>
            </div>

                        <?php } ?>
                        </div>

                        <div class="tab-pane fade" id="home-7" role="tabpanel" aria-labelledby="home-tab-7">
                            <?php include_once 'custom_list_calendor.php'; ?>

                        </div>

                        <div class="tab-pane fade" id="home-8" role="tabpanel" aria-labelledby="home-tab-8">
                            Goals
                         </div>
                     <div class="tab-pane fade" id="home-9" role="tabpanel" aria-labelledby="home-tab-9">
                        <div id="display_side_assets">

                           <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                           </div>
                        </div>
                     </div>
                     <div class="tab-pane fade" id="home-19" role="tabpanel" aria-labelledby="home-tab-19">
                        <div id="display_side_work_order">
                           <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                           </div>
                        </div>
                     </div>
                     <div class="tab-pane fade" id="home-22" role="tabpanel" aria-labelledby="home-tab-19">
                        <div id="display_side_esign">
                           <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                           </div>
                        </div>
                     </div>

                     <div class="tab-pane fade" id="home-10" role="tabpanel" aria-labelledby="home-tab-10">
                          <?php  include_once 'custom_list_employee_events.php'; ?>
                      </div>
                      <div class="tab-pane fade" id="home-11" role="tabpanel" aria-labelledby="home-tab-11">
                        Travel
                      </div>
                      <div class="tab-pane fade" id="home-12" role="tabpanel" aria-labelledby="home-tab-12">
                        Clamis
                      </div>

                       <div class="tab-pane fade" id="home-70" role="tabpanel" aria-labelledby="home-tab-70">
                              <?php include_once "custom_basic_info_pay.php"; ?>

                        </div>
                      <div class="tab-pane fade" id="home-13" role="tabpanel" aria-labelledby="home-tab-13">

                            <div class="card card-custom gutter-b card-stretch">
                                 <div class="card-header border-0">
                                    <h3 class="card-title">My Documents</h3>

                                 </div>
                                 <div class="card-body">
                                    <?php foreach ($documents as $document) { ?>
                                    <div class="d-flex flex-column align-items-center col-lg-3 float-left mt-3">
                                       <img alt="" class="max-h-65px" src="assets/media/svg/files/pdf.svg">
                                       <a href="uploads/all/><?php echo $document['filename']; ?>" class="text-dark-75 font-weight-bold mt-5 font-size-lg"><?php echo $document['document_name']; ?></a>
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                      </div>
                      <div class="tab-pane fade" id="home-14" role="tabpanel" aria-labelledby="home-tab-14">
                        Leaves
                      </div>
                      <div class="tab-pane fade" id="home-15" role="tabpanel" aria-labelledby="home-tab-15">
                        <div id="display_side_performance">

                           <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                           </div>
                        </div>
                      </div>
            <div class="tab-pane fade" id="home-22_2" role="tabpanel" aria-labelledby="home-tab-22">
                        <div id="display_side_performance_2">

                           <div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                           </div>
                        </div>
                      </div>

                      </div>
                    </div>
                  </div>
            </div>
      </div>
  </div>
</div></div>
</div>




<!-- Modal -->
<div class="modal fade" id="modal_status" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title modallabel" id="exampleModalLabel">Effort Share Status</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>


<form class="form" id="insertemphistries">
      <input type="hidden" name="emp_id" id="emp_id">
      <input type="hidden" name="field_name" id="field_name">
      <input type="hidden" name="field_value" id="field_value">
      <div class="modal-body">
        <div class="alert alert-custom alert-default" role="alert">
          <div class="row" style="width: 100%;">
             <span class="col-lg-4"><b>Employee name : </b></span>
             <div class="col-lg-8 ">
                <p class="modal_view_employee_name"></p>
             </div>
             <span class="col-lg-4"><b>Status : </b></span>
             <div class="col-lg-8 ">
                <p class="modal_view_status"></p>
             </div>
          </div>

       </div>
      <div class="card-body">
        <div class="form-group row">
           <label for="example-date-input-from" class="col-4 col-form-label">Effective From</label>
           <div class="col-5">
              <input class="form-control" type="date" name="effective_from" value="2011-08-19" id="example-date-input-from">
           </div>
           <label for="example-date-input-to" class="col-4 col-form-label mt-2">Effective To</label>
           <div class="col-5 mt-2">
              <input class="form-control" type="date" name="effective_to" value="2011-08-19" id="example-date-input-to">
           </div>
        </div>
      </div>
   </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary " onclick="save_statuse_bilable_onsite()">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php if(isset($_POST['emp_id'])){ ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
<script type="text/javascript" src="assets/pages/chart/google/js/com_insert_payslip_charts.js"></script>
<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
    <script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
<?php }?>
