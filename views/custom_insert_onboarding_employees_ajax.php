<?php
if($_POST['ajaxcallemployee'] =="Y"){
    $employee_id = $_POST['employee_id'];
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getemployees&employee_id=".$employee_id."&working_role_short_code=".$_SESSION['working_role_short_code'];
    $response = callAPI($url);
    $result = json_decode($response,1);
    $data = $result['employees'];
}
$employees_display_fields = $result['employees_display_fields'];
$vafield = json_decode($employees_display_fields['field_list'],1);
$departments = $result['departments'];
$designations = $result['designations'];
$divisions = $result['divisions'] ?? array();
$job_titles = $result['job_titles'] ?? array();
$employee_other_access = $result['employee_other_access'] ?? array();
$locations = $result['locations'];
$employment_bands = $result['employment_bands'];
$employment_levels = $result['employment_levels'];
$employment_roles = $result['employment_roles'];
$application_roles = $result['application_roles'];
$marital_statuses = $result['marital_statuses'];
$blood_groups = $result['blood_groups'];
$verticals = $result['verticals'];
$gender_details = $result['gender_details'];
$employeement_types = $result['employeement_types'];
$education_type = $result['education_type'];
$banks = $result['banks'];
$emp_educations = $result['emp_educations'];
$emp_dependents = $result['emp_dependents'];
$emp_contacts = $result['emp_contacts'];
$emp_work_experiences = $result['emp_work_experiences'];
$relations = $result['relations'];
$application_users = $result['application_users'];
$all_employees = $result['all_employees'];
$countries = $result['countries'];
$emp_visa = $result['emp_visa'];
$emp_skills = $result['emp_skills'];
$skills = $result['skills'];
$teams =  $result['teams'];
$emp_banks = $result['emp_banks'];
$emp_bank_status = $result['emp_bank_status'];
$employee_access = $result['employee_access'];
$statuse = ViewStatusData();

$billable = array("0"=>"Yes","1"=>"No"," "=>"Not Defined");
$yesno = array('0'=>'Yes','1'=>'No');

$birthDate = new DateTime($data['date_of_birth']);
$current = new DateTime(date('Y-m-d'));
$interval = $birthDate->diff($current);
$agemonth = ($interval->m < 10 && $interval->m >= 0) ? '0'.$interval->m:$interval->m;
$agecalc = $interval->y.".".$agemonth;
$bank_work_flow = "Y";
?>
<script type="text/javascript">
    function employementviewhistoryppup(emp_id) {
        jQuery.ajax({
            url: "views/custom_view_employment_type_details.php",
            data:'emp_id='+emp_id,
            type: "GET",
            success:function(data){
                console.log("Chelll");
                $('#popupsq').html(data);
                $('#kt_modal_KTDatatable_remotesq').modal("show");
            },
            error:function (){}
        });
    }
    function employeementtypepopup(emp_id, val) {
        console.log('emp_id='+emp_id+'&type='+val);
        jQuery.ajax({
            url: "views/custom_insert_employment_type_details.php",
            data:'emp_id='+emp_id+'&type='+val,
            type: "GET",
            success:function(data){
                $('#popupsq').html(data);
                $('#kt_modal_KTDatatable_remotesq').modal("show");
            },
            error:function (){}
        });
    }
</script>
<?php if($_POST['edittype'] =="VIEW"){ ?>
    <?php if($_POST['pagetype'] =="basic_information" || $_POST['showpagetype'] =="basic_information"){ ?>
        <?php
               $fieldaccesright = $vafield['basic_information'];
        ?>
        <div class="row">
            <?php if($fieldaccesright['field']['emp_code']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Employee Code </span>
                  <p> <?php echo emptytextdashShow($data['emp_code']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['email_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Email</span>
                  <p> <?php echo emptytextdashShow($data['email_id']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['first_name']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">First Name</span>
                  <p> <?php echo emptytextdashShow($data['first_name']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['middle_name']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Middle Name</span>
                  <p> <?php echo emptytextdashShow($data['middle_name']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['last_name']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Last Name</span>
                  <p> <?php echo emptytextdashShow($data['last_name']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['phone']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Contact</span>
                  <p> <?php echo emptytextdashShow($data['phone']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['address']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Permanent Address</span>
                  <p> <?php echo emptytextdashShow($data['address']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['contact_address']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Current Address</span>
                  <p> <?php echo emptytextdashShow($data['contact_address']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['post_code']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Postcode</span>
                  <p> <?php echo emptytextdashShow($data['post_code']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['fax']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Fax</span>
                  <p> <?php echo emptytextdashShow($data['fax']); ?></p>
              </div>
          <?php } ?>
        </div>
    <?php }elseif($_POST['pagetype'] =="work_details" || $_POST['showpagetype'] =="work_details"){ ?>
        <?php
               $fieldaccesright = $vafield['work'];
        ?>
        <div class="row">
            <?php if($fieldaccesright['field']['department_id']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Department </span>
                  <p> <?php echo emptytextdashShow($departments[$data['department_id']]['department_name']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['designation_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Designation</span>
                  <p>  <?php echo emptytextdashShow($designations[$data['designation_id']]['designation_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['band_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Band</span>
                  <p> <?php echo emptytextdashShow($employment_bands[$data['band_id']]['band_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['level_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">level</span>
                  <p>  <?php echo emptytextdashShow($employment_levels[$data['level_id']]['level_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['vertical_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Vertical</span>
                  <p>  <?php echo emptytextdashShow($verticals[$data['vertical_id']]['vertical_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['hiring_source']!="NA"){ ?>
              <div class="col-xl-6 form-group" >
                <span class="form-label d-block">Hiring Source  </span>
                <p><?php echo emptytextdashShow($data['hiring_source']); ?></p>
            </div>
              <?php }
if($fieldaccesright['field']['joining_date']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Date of Joining</span>
                  <p>  <?php echo $data['joining_date'] !=""?fromDate($data['joining_date']):"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['experience_current_organization']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Current Organization Experience</span>
                  <p> <?php echo emptytextdashShow($data['experience_current_organization']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['total_experiance']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Total Experience</span>
                  <p> <?php echo $data['total_experiance'] !=""?$data['total_experiance']."- Year(s)":"-"; ?> </p>
              </div>
              <?php }
if($fieldaccesright['field']['relevant_experience']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Relevant Experience</span>
                  <p> <?php echo $data['relevant_experience'] !=""?$data['relevant_experience']."- Year(s)":"-"; ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['notice_period']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Notice Period</span>
                  <p> <?php echo $data['notice_period'] !=""?$data['notice_period']."- Day(s)":" - "; ?> </p>
              </div>
              <?php }
if($fieldaccesright['field']['location_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Work Location</span>
                  <p>  <?php echo emptytextdashShow($locations[$data['location_id']]['location_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['employee_type']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Employment Type</span>
                  <p> <?php echo $data['employee_type']!=""?emptytextdashShow($employeement_types[$data['employee_type']]['genc_detail_name']):"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['billable']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Billable</span>
                  <p>  <?php echo emptytextdashShow($billable[$data['billable']]); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['office_phone']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Office Phone</span>
                  <p> <?php echo emptytextdashShow($data['office_phone']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['office_ext']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Extension</span>
                  <p> <?php echo emptytextdashShow($data['office_ext']); ?></p>
              </div>
              <?php } ?>

        </div>
    <?php }elseif($_POST['pagetype'] =="personal_details" || $_POST['showpagetype'] =="personal_details"){
        $fieldaccesright = $vafield['personal_details'];
    ?>
        <div class="row">
            <?php if($fieldaccesright['field']['date_of_birth']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Date of Birth </span>
                  <p> <?php echo dateFormate($data['date_of_birth']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['age']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Age </span>
                  <p> <?php echo $agecalc; ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['marital_status']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Marital Status </span>
                  <p> <?php echo emptytextdashShow($marital_statuses[$data['marital_status']]['marital_status_name']); ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['wedding_date']!="NA"){  ?>
              <div class="col-xl-6 form-group" style="<?php echo $data['marital_status'] =="2"?"":"display: none"; ?>">
                  <span class="form-label d-block">Marrige Date</span>
                  <p> <?php echo $data['wedding_date'] !=""?fromDate($data['wedding_date']):"-"; ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['gender']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Gender </span>
                  <p> <?php echo emptytextdashShow($gender_details[$data['gender']]['genc_detail_name']);?></p>
              </div>
              <?php }
if($fieldaccesright['field']['blood_group']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Blood Group </span>
                  <p>     <?php echo $blood_groups[$data['blood_group']]['blood_group']!=""?$blood_groups[$data['blood_group']]['blood_group']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['disability']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Disability </span>
                  <p>     <?php echo $data['disability'] !=""?$yesno[$data['disability']]:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['disability_percentage']!="NA"){  ?>
              <div class="col-xl-6 form-group" style="<?php echo $data['disability'] =="0"?"":"display: none"; ?>">
                  <span class="form-label d-block">Disability percentage </span>
                  <p> <?php echo $data['disability_percentage'] !=""?$data['disability_percentage']:"-"; ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['work_textile_industry']!="NA"){  ?>
              <div class="col-xl-6 form-group" >
                  <span class="form-label d-block">Working In Textile Industry</span>
                  <p><?php echo $data['work_textile_industry'] !=""?$yesno[$data['work_textile_industry']]:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['physically_challenged_children']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Physically Challenged Children</span>
                  <p><?php echo $data['physically_challenged_children'] !=""?$yesno[$data['physically_challenged_children']]:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['soc_facebook']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Facebook Link </span>
                  <p> <?php  echo emptytextdashShow($data['soc_facebook']);  ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['soc_twitter']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Twitter Link </span>
                  <p> <?php  echo emptytextdashShow($data['soc_twitter']);  ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['soc_linkedin']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Linkedin Link </span>
                  <p> <?php  echo emptytextdashShow($data['soc_linkedin']);  ?></p>
              </div>
          <?php } ?>
        </div>
    <?php }elseif($_POST['pagetype'] =="bank_details" || $_POST['showpagetype'] =="bank_details"){
        $fieldaccesright = $vafield['bank_details'];
    ?>
        <div class="row">
            <?php
            if($bank_work_flow == 'Y'){
            if(count((array)$emp_banks) !="0"){
                foreach ($emp_banks as $emp_bank) {  ?>
                <div class="col-xl-4 col-md-4" >
                    <div class="card social-res-card">
                        <div class="card-body">
                            <span class="form-label d-block ">Bank
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['bank_details']!="V"){ ?>
                                <?php if($emp_bank['activation_status'] == 'Y'){ ?>
                                    <span class="form-label label label-warning float-end">Active</span>
                                <?php }else{
                                    $visiblebtnstatus =array(ST_ACTIVE,ST_SEND_BACK,ST_CALLBACK);
                                    if(in_array($emp_bank['current_status'], (array)$visiblebtnstatus)){
                                    ?>
                                        <span class="float-end cursor-pointer" onclick="addBankRequest('<?php echo $emp_bank['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-pencil"></i></span>
                                <?php }else{
                                    $stausresult = $statuse[$emp_bank['current_status']];
                                    ?>
                                    <span class="form-label label float-end" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $emp_bank_status[$emp_bank['current_status']]['name']; ?></span>

                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            </label>
                               <p><?php echo $banks[$emp_bank['bank_id']]['bank_name']!=""?$banks[$emp_bank['bank_id']]['bank_name']:"-";?></p>

                               <span class="form-label d-block">IFSC Code</span>
                            <p><?php  echo emptytextdashShow($emp_bank['ifsc_code']);  ?></p>

                            <span class="form-label d-block">Account No</span>
                               <p><?php echo emptytextdashShow($emp_bank['bank_account_no']); ?></p>

                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php }else{ ?>
                <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Employee Banks </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
            <?php } ?>
            <?php }else{
             if($fieldaccesright['field']['bank_id']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Bank </span>
                  <p> <?php echo $banks[$data['bank_id']]['bank_name']!=""?$banks[$data['bank_id']]['bank_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['bank_link']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">IFSC Code </span>
                  <p> <?php  echo emptytextdashShow($data['bank_link']);  ?></p>
              </div>
              <?php }
if($fieldaccesright['field']['bank_account_no']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Account No </span>
                  <p> <?php  echo emptytextdashShow($data['bank_account_no']);  ?></p>
              </div>
          <?php } ?>
          <?php } ?>
        </div>

        <?php }elseif($_POST['pagetype'] =="proof_details" || $_POST['showpagetype'] =="proof_details"){
        $fieldaccesright = $vafield['proof_details'];
        $attached_files = json_decode($data['attached_files'],1);
    ?>

        <table class="table">
               <thead>
                  <tr>
                     <th scope="col">Type</th>
                      <th scope="col" >Number</th>
                     <th scope="col">Expiry Date</th>
                     <th scope="col">File</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                    if($fieldaccesright['field']['adhar_no']!="NA"){
                  ?>
                  <tr>
                     <th scope="col" class="p-2">Aadhar Card</th>
                     <td class="p-2"><?php echo emptytextdashShow($data['adhar_no']); ?></td>
                     <td class="p-2"> - </td>
                     <td class="p-2">
                     <?php if($attached_files['aadhar_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['aadhar_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                    <?php }else{ echo "-";
} ?>
                     </td>
                </tr>
                <?php }
if($fieldaccesright['field']['passport']!="NA"){  ?>
                <tr>
                    <th scope="col" class="p-2">Passport</th>
                    <td class="p-2"><?php echo emptytextdashShow($data['passport']); ?></td>
                    <td class="p-2"><?php echo ($data['passport_expiry_date']!="0000-00-00" && $data['passport_expiry_date']!="")?fromDate($data['passport_expiry_date']):" - "; ?></td>
                    <td class="p-2">
                    <?php if($attached_files['passport_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['passport_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                    <?php }else{ echo "-";
} ?>
                     </td>
                </tr>
                <?php }
if($fieldaccesright['field']['driving_licence']!="NA"){  ?>
                <tr>
                    <th scope="col" class="p-2">Driving Licence</th>
                    <td class="p-2"><?php echo emptytextdashShow($data['driving_licence']); ?></td>
                    <td class="p-2"><?php echo ($data['driving_licence_expiry_date']!="0000-00-00" && $data['driving_licence_expiry_date']!="")?fromDate($data['driving_licence_expiry_date']):" - "; ?></td>
                    <td class="p-2">
                        <?php if($attached_files['driving_licence_attachment'] !=""){ ?>
                        <a href="uploads/all/<?php echo $attached_files['driving_licence_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                        <?php }else{ echo "-";
} ?>
                    </td>
                 </tr>
                 <?php }
if($fieldaccesright['field']['pan_number']!="NA"){  ?>
                 <tr>
                    <th scope="col" class="p-2">PAN Number</th>
                    <td class="p-2"><?php echo emptytextdashShow($data['pan_number']); ?> </td>
                    <td class="p-2"> - </td>
                    <td class="p-2">
                        <?php if($attached_files['pan_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pan_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                    <?php }else{ echo "-";
} ?>
                     </td>
                  </tr>
                  <?php }
if($fieldaccesright['field']['pf_no']!="NA"){  ?>
                  <tr>
                     <th scope="col" class="p-2">PF Number</th>
                     <td class="p-2"><?php echo emptytextdashShow($data['pf_no']); ?> </td>
                     <td class="p-2"> - </td>
                     <td class="p-2">
                     <?php if($attached_files['pf_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pf_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                    <?php }else{ echo "-";
} ?>
                     </td>
                  </tr>
                  <?php }
if($fieldaccesright['field']['pf_uan']!="NA"){  ?>
                   <tr>
                     <th scope="col" class="p-2">UAN Number</th>
                     <td class="p-2"><?php echo emptytextdashShow($data['pf_uan']); ?></td>
                     <td class="p-2"> - </td>
                     <td class="p-2">
                         <?php if($attached_files['pf_uan_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pf_uan_attachment']; ?>" class="text-primary mt-2 f-14" download><i class="ti-cloud-down"></i> </a>
                    <?php }else{ echo "-";
} ?>
                     </td>
                  </tr>
            <?php } ?>
           </tbody>
        </table>

   <?php }elseif($_POST['pagetype'] =="experience_details" || $_POST['showpagetype'] =="experience_details"){
        $fieldaccesright = $vafield['experience_details'];
    if(count((array)$emp_work_experiences) !="0"){
    ?>
    <div class="card-body border-bottom ">
        <div class="card-block">
            <div class="scroll-widget">
                  <div class="latest-update-box">
                  <?php
                  foreach ($emp_work_experiences as $expe) {  ?>
                    <div class="row p-t-20 p-b-30">
                        <div class="col-auto text-end update-meta p-r-0">
                            <i class="b-primary update-icon ring"></i>
                        </div>
                        <div class="col p-l-5">
                            <a href="#!"><h6><?php echo $expe['company_name']; ?></h6></a>
                            <p class="text-muted m-b-0"><?php echo fromDate($expe['work_from']); ?> To <?php echo fromDate($expe['work_to']); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>

                  </div>
            </div>
        </div>
    </div>
    <?php }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Employment History </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>

   <?php }elseif($_POST['pagetype'] =="employee_qualifications" || $_POST['showpagetype'] =="employee_qualifications"){
        $fieldaccesright = $vafield['employee_qualifications'];
          if(count((array)$emp_educations) !="0"){
    ?>

    <div class="card-body border-bottom ">
        <div class="card-block">
            <div class="scroll-widget">
                  <div class="latest-update-box">
                      <?php
                      foreach ($emp_educations as $emp_edu) {  ?>
                    <div class="row p-t-20 p-b-30">
                        <div class="col-auto text-end update-meta p-r-0">
                            <i class="b-primary update-icon ring"></i>
                        </div>
                        <div class="col p-l-5">
                            <h6 class="mb-1"><?php echo emptytextdashShow($emp_edu['institute_name']); ?></h6>
                            <span class="text-muted m-b-0 f-w-600 f-12"><?php echo $education_type[$emp_edu['education_type']]['genc_detail_name']; ?> - (<?php echo $emp_edu['score']; ?>) </span>
                            <p class="text-muted m-b-0"><?php echo fromDate($emp_edu['start_date']); ?> To <?php echo fromDate($emp_edu['end_date']); ?>

                            </p>
                        </div>
                    </div>
                <?php } ?>
                  </div>
            </div>
        </div>
    </div>
    <?php }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Educational Details </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
     <?php }elseif($_POST['pagetype'] =="employee_dependents" || $_POST['showpagetype'] =="employee_dependents"){
        $fieldaccesright = $vafield['employee_dependents'];
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_dependents) !="0"){
              foreach ($emp_dependents as $emp_dep) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block">Name</span>
                    <p><?php echo emptytextdashShow($emp_dep['dependent_name']); ?></p>
                    <span class="form-label d-block">Relationship Type</span>
                       <p><?php echo $relations[$emp_dep['dependent_relationship_type']]['relation_name']; ?></p>
                       <span class="form-label d-block">Date Of Birth</span>
                       <p><?php echo fromDate($emp_dep['dependent_date_of_birth']); ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php }else{ ?>
            <div class="card-px text-center py-20 mt-2">
                <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Dependents </p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
    <?php }elseif($_POST['pagetype'] =="employee_contacts" || $_POST['showpagetype'] =="employee_contacts"){
        $fieldaccesright = $vafield['employee_contacts'];
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_contacts) !="0"){
            foreach ($emp_contacts as $emp_con) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block">Name</span>
                    <p><?php echo emptytextdashShow($emp_con['contact_name']); ?></p>
                    <span class="form-label d-block">Relationship Type</span>
                       <p><?php echo emptytextdashShow($relations[$emp_con['contact_relationship']]['relation_name']); ?></p>
                       <span class="form-label d-block">Home No</span>
                       <p><?php echo emptytextdashShow($emp_con['contact_home_no']); ?></p>
                       <span class="form-label d-block">Mobile No</span>
                       <p><?php echo emptytextdashShow($emp_con['contact_mobile_no']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Emergency Contact </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>

    <?php }elseif($_POST['pagetype'] =="employee_buddies" || $_POST['showpagetype'] =="employee_buddies"){
        $fieldaccesright = $vafield['employee_buddies'];
    ?>
        <div class="row">
            <?php if($fieldaccesright['field']['role_id']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Employment Role </span>
                  <p> <?php echo $employment_roles[$data['role_id']]['role_name']!=""?$employment_roles[$data['role_id']]['role_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['reporting_manager_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Repoting To</span>
                  <p> <?php echo $application_users[$data['reporting_manager_id']]['display_name']!=""?$application_users[$data['reporting_manager_id']]['display_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['admin_buddy']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Admin Buddy </span>
                  <p> <?php echo $all_employees[$data['admin_buddy']]['display_name']!=""?$all_employees[$data['admin_buddy']]['display_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['hr_buddy']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">HR Buddy</span>
                  <p> <?php echo $all_employees[$data['hr_buddy']]['display_name']!=""?$all_employees[$data['hr_buddy']]['display_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['finance_buddy']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Finance Buddy</span>
                  <p> <?php echo $all_employees[$data['finance_buddy']]['display_name']!=""?$all_employees[$data['finance_buddy']]['display_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['project_buddy']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Project Buddy</span>
                  <p> <?php echo $all_employees[$data['project_buddy']]['display_name']!=""?$all_employees[$data['project_buddy']]['display_name']:"-";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['loc_buddy']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Location Buddy</span>
                  <p> <?php echo $all_employees[$data['loc_buddy']]['display_name']!=""?$all_employees[$data['loc_buddy']]['display_name']:"-";?></p>
              </div>
              <?php } ?>
        </div>

<!-- added by koti start -->
<?php }elseif(($_POST['pagetype'] ?? '') =="employee_other_access" || ($_POST['showpagetype'] ?? '') =="employee_other_access"){
		$fieldaccesright = $vafield['employee_other_access'] ?? array(); ?>
		<div class="row">
		<?php if(($fieldaccesright['field']['job_title_id'] ?? '') !="NA"){
		  	$readonlycheck = ($fieldaccesright['field']['job_title_id'] ?? '') !="E"?"custom_readonly":""; ?>
		   	<div class="col-xl-6 mt-2">
		        <span class="form-label">Job Title </span>
		        <div class="<?php echo $readonlycheck; ?>">
			       <select aria-label="Job Title" class ="form-control form-control-lg form-select js-example-basic-single" name="job_title_id">
					  	<option value="">Select Any One</option>
					  	<?php foreach ((array)$job_titles as $job_title) {
					    ?>
					    	<option <?php if(($data['job_title_id'] ?? '') == ($job_title['id'] ?? '')){ ?> selected <?php } ?> value="<?php echo $job_title['id'] ?? ''; ?>" >
					      	<?php echo htmlspecialchars($job_title['job_title'] ?? '', ENT_QUOTES); ?>
					    	</option>
					  <?php } ?>
					</select>
				</div>
		    </div>
		    <?php }?>
		<?php if (($fieldaccesright['field']['is_muslim'] ?? '') != "NA") {
	        $readonlycheck = ($fieldaccesright['field']['is_muslim'] ?? '') != "E" ? "custom_readonly" : ""; ?>
	        <div class="col-xl-6 mt-2">
	            <span class="form-label d-block">Are You Muslim ?</span>
	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input aria-label="Muslim Yes" class="form-check-input" type="radio" name="cover_under_pf" value="0" <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '0') echo 'checked'; ?>>
	                    <label class="form-check-label">YES</label>
	                </div>
	                <div class="form-check form-check-inline">
	                    <input aria-label="Muslim No" class="form-check-input" type="radio" name="cover_under_pf" value="1" <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '1') echo 'checked'; ?>>
	                    <label class="form-check-label">NO</label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    <?php if (($fieldaccesright['field']['is_local'] ?? '') != "NA") {
	        $readonlycheck = ($fieldaccesright['field']['is_local'] ?? '') != "E" ? "custom_readonly" : ""; ?>
	        <div class="col-xl-6 mt-2">
	            <span class="form-label d-block">Are You Local ?</span>
	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input aria-label="Local Yes" class="form-check-input" type="radio" name="cover_under_pf" value="0" <?php if (isset($data['is_local']) && $data['is_local'] === '0') echo 'checked'; ?>>
	                    <label class="form-check-label">YES</label>
	                </div>
	                <div class="form-check form-check-inline">
	                    <input aria-label="Local No" class="form-check-input" type="radio" name="cover_under_pf" value="1" <?php if (isset($data['is_local']) && $data['is_local'] === '1') echo 'checked'; ?>>
	                    <label class="form-check-label">NO</label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    </div>
	}elseif($_POST['pagetype'] =="employee_access" || $_POST['showpagetype'] =="employee_access"){
        $fieldaccesright = $vafield['employee_access'];
    ?>
        <div class="row">
            <?php if($fieldaccesright['field']['cover_under_pf']!="NA"){  ?>
            <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Cover Under PF </span>
                  <p> <?php echo $data['cover_under_pf']!="" && $data['cover_under_pf'] == '0' ? "YES":"NO";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['cover_under_esi']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">Cover Under ESI</span>
                  <p>  <?php echo $data['cover_under_esi']!="" && $data['cover_under_esi'] == '0'?"YES":"NO";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['clock_event_type']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">ClockIn & ClockOut Access </span>
                  <p>  <?php echo $data['clock_event_type']!="" && $data['clock_event_type'] == 'Y'?"YES":"NO";?></p>
              </div>
              <?php }
if($fieldaccesright['field']['b_machine_id']!="NA"){  ?>
              <div class="col-xl-6 form-group">
                  <span class="form-label d-block">B Machine Id </span>
                  <p>  <?php echo $data['b_machine_id']!="" ? $data['b_machine_id']:"-";?></p>
              </div>
              <?php } ?>
        </div>
<!-- added by koti end -->

    <?php }elseif($_POST['pagetype'] =="emp_visa" || $_POST['showpagetype'] =="emp_visa"){
        $fieldaccesright = $vafield['emp_visa'];
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_visa) !="0"){
            foreach ($emp_visa as $emp_v) {  ?>
        <div class="col-xl-6 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block">VISA Number</span>
                    <p><?php echo emptytextdashShow($emp_v['visa_number']); ?></p>
                    <span class="form-label d-block">Country</span>
                       <p><?php echo emptytextdashShow($countries[$emp_v['country_id']]['country_name']); ?></p>
                       <span class="form-label d-block">Issue Date</span>
                       <p><?php echo $emp_v['visa_issue_date'] !=""?fromDate($emp_v['visa_issue_date']):" - "; ?></p>
                       <span class="form-label d-block">Expiry Date</span>
                       <p><?php echo $emp_v['visa_expiry_date'] !=""?fromDate($emp_v['visa_expiry_date']):"-"; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No VISA Details </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>

    <?php }elseif($_POST['pagetype'] =="emp_skills" || $_POST['showpagetype'] =="emp_skills"){
        $fieldaccesright = $vafield['emp_skills'];
    ?>
    <div class="row">
        <?php
        if(count((array)$emp_skills) !="0"){
            foreach ($emp_skills as $emp_skil) {  ?>
        <div class="col-xl-6 col-md-6" >
            <div class="card social-res-card">
                <div class="card-body">
                    <span class="form-label d-block" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">Skill <?php if($fieldaccesright['field']['emp_skills']!="V"){  ?><span class="float-end cursor-pointer" onclick="skillpopupNew('<?php echo $emp_skil['id']; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-pencil"></i></span><?php
} ?></span>
                       <p><?php echo emptytextdashShow($skills[$emp_skil['skill_id']]['skill']); ?></p>
                       <span class="form-label d-block">Years Of Experience</span>
                    <p><?php echo emptytextdashShow($emp_skil['years_of_exp']); ?></p>
                    <span class="form-label d-block">Rating</span>
                       <p><?php echo $skills[$emp_skil['skill_id']]['max_ratting']."/".emptytextdashShow($emp_skil['rating']); ?></p>
                       <span class="form-label d-block">comments</span>
                       <p><?php echo emptytextdashShow($emp_skil['comments']); ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Skills </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>
    <?php } ?>

<?php } //View part End ?>

<?php if($_POST['edittype'] =="INSERT"){ ?>
    <?php
    if($_POST['pagetype'] =="basic_information" || $_POST['showpagetype'] =="basic_information"){
           $fieldaccesright = $vafield['basic_information'];
    ?>
        <div class="row">
        <?php if($fieldaccesright['field']['emp_code']!="NA"){
              $readonlycheck = $fieldaccesright['field']['emp_code'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Employee Code </span>
                <input aria-label="Input field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="emp_code" value="<?php echo $data['emp_code']; ?>" readonly/>
            </div>
        <?php }
if($fieldaccesright['field']['email_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['email_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Email </span>
                <input aria-label="Input field" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="Enter email" name="email_id" value="<?php echo $data['email_id']; ?>" readonly/>
            </div>
        <?php }
if($fieldaccesright['field']['first_name']!="NA"){
              $readonlycheck = $fieldaccesright['field']['first_name'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">First Name  </span>
                <input aria-label="Input field" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" type="text" placeholder="Enter first Name" name="first_name" value="<?php echo $data['first_name']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['middle_name']!="NA"){
              $readonlycheck = $fieldaccesright['field']['middle_name'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Middle Name </span>
                <input aria-label="Input field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="Enter middle Name" name="middle_name" value="<?php echo $data['middle_name']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['last_name']!="NA"){
              $readonlycheck = $fieldaccesright['field']['last_name'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Last Name </span>
                <input aria-label="Input field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="Enter last Name" name="last_name" value="<?php echo $data['last_name']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['phone']!="NA"){
              $readonlycheck = $fieldaccesright['field']['phone'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Contact </span>
                <input aria-label="Input field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" placeholder="Enter Contact" name="phone" value="<?php echo $data['phone']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['address']!="NA"){
              $readonlycheck = $fieldaccesright['field']['address'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Permanent Address </span>
                <textarea aria-label="Text area" style="overflow:auto" class="form-control form-control-lg  <?php echo $readonlycheck; ?>" placeholder="Enter your permanent address" name="address"><?php echo $data['address'];?></textarea>
            </div>
         <?php }
if($fieldaccesright['field']['contact_address']!="NA"){
              $readonlycheck = $fieldaccesright['field']['contact_address'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Current Address </span>
                <textarea aria-label="Text area" style="overflow:auto" class="form-control <?php echo $readonlycheck; ?>" placeholder="Enter your current address" name="contact_address"><?php echo $data['contact_address'];?></textarea>
            </div>
        <?php }
if($fieldaccesright['field']['post_code']!="NA"){
              $readonlycheck = $fieldaccesright['field']['post_code'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Postcode </span>
                <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>" placeholder="Enter your postcode" name="post_code" value="<?php echo $data['post_code']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['fax']!="NA"){
              $readonlycheck = $fieldaccesright['field']['fax'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Fax </span>
                <input aria-label="Input field" class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="Enter fax" name="fax" value="<?php echo $data['fax']; ?>"/>
            </div>
        <?php }
if($fieldaccesright['field']['image']!="NA"){
              $readonlycheck = $fieldaccesright['field']['image'] !="E"?"custom_readonly":""; ?>
            <div class="mt-2 col-lg-2 <?php echo $readonlycheck; ?>">
                <span class="form-label">Profile Image</span>
                <div class="col-lg-9 col-xl-6">
                    <div class="profile-pic">
                        <label class="-label" for="profile_image">
                            <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                        </label>
                        <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                        <img src="<?php echo "uploads/all/".$data['image'] ; ?>" id="outputprofile_image" width="200" alt="User Profile" />
                        <input type="hidden" name="old_profile_image" value="<?php echo $data['image'] ;  ?>">
                    </div>
                </div>
            </div>
        <?php } ?>

        </div>

    <?php }elseif($_POST['pagetype'] =="work_details" || $_POST['showpagetype'] =="work_details"){ ?>
        <?php
               $fieldaccesright = $vafield['work'];
        ?>
        <div class="row">
        <?php if($fieldaccesright['field']['department_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['department_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Department </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Department" class ="form-control form-control-lg form-select js-example-basic-single"   name="department_id">
                          <option value="">Select Any One</option>
                          <?php foreach ($departments as $departments) {
                        ?>
                        <option <?php if($data['department_id'] == $departments['id']){  ?>selected <?php
} ?> value="<?php echo $departments['id']; ?>" ><?php echo $departments['department_name'];?></option>
                          <?php } ?>
                    </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['designation_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['designation_id'] !="E"?"custom_readonly":""; ?>
               <div class="col-xl-6 mt-2" >
                <span class="form-label">Designation </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Designation" class ="form-control form-control-lg form-select js-example-basic-single" name="designation_id">
                          <option value="">Select Any One</option>
                          <?php foreach ($designations as $desigs) {
                        ?>
                            <option <?php if($data['designation_id'] == $desigs['id']){ ?> selected <?php
} ?> value="<?php echo $desigs['id']; ?>" >
                              <?php echo $desigs['designation_name']; ?>
                            </option>
                      <?php } ?>
                    </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['band_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['band_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Band </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Band" class ="form-control form-control-lg form-select js-example-basic-single" name="band_id" onchange="getBandid(this.value)">
                        <option value="">Select Any One</option>
                        <?php foreach ($employment_bands as $employment_ban) {
                          ?>
                          <option <?php if($data['band_id'] == $employment_ban['id']){ ?>selected  <?php
} ?> value="<?php echo $employment_ban['id']; ?>" >
                            <?php echo $employment_ban['band_name']; ?>
                          </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['level_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['level_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Level </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Level" class ="form-control js-example-basic-single" name="level_id" id="employeement_level">
                          <option value="">Select Any One</option>
                          <?php foreach ($employment_levels as $employment_levl) { ?>
                           <option <?php if($data['level_id'] == $employment_levl['id']){ ?> selected <?php
} ?>  value="<?php echo $employment_levl['id']; ?>" >
                        <?php echo $employment_levl['level_name']; ?>
                          </option>
                    <?php } ?>
                  </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['vertical_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['vertical_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Vertical </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Vertical" class ="form-control form-control-lg form-select js-example-basic-single" name="vertical_id">
                        <option value="">Select Any One</option>
                        <?php foreach($verticals as $verticals){ ?>
                         <option <?php if($data['vertical_id'] == $verticals['id']){ ?>selected <?php
} ?> value="<?php echo $verticals['id']; ?>" >
                          <?php echo $verticals['vertical_name']; ?>
                        </option>
                      <?php } ?>
                    </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['hiring_source']!="NA"){
              $readonlycheck = $fieldaccesright['field']['hiring_source'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Hiring Source  </span>
                <input aria-label="Input field"  class="form-control <?php echo $readonlycheck; ?>" type="text" placeholder="Enter Hiring Source" name="hiring_source" value="<?php echo $data['hiring_source']; ?>"/>
            </div>
            <?php }
if($fieldaccesright['field']['joining_date']!="NA"){
              $readonlycheck = $fieldaccesright['field']['joining_date'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Date of Joining </span>
                <div class="<?php echo $readonlycheck; ?>">
                      <input aria-label="Input field" type="text" class="form-control datepicker" value="<?php echo ($data['joining_date']!="0000-00-00" && $data['joining_date']!="")?fromDate($data['joining_date']):""; ?>"  name="joining_date" placeholder="Enter Date of Joining"/>
                  </div>
            </div>
            <?php }
if($fieldaccesright['field']['experience_current_organization']!="NA"){
              $readonlycheck = $fieldaccesright['field']['experience_current_organization'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Experience in Current Organization </span>
                <input aria-label="Enter number" type="number" class="form-control <?php echo $readonlycheck; ?>"  placeholder="Enter Experience in Current Organization" name="experience_current_organization" value="<?php echo $data['experience_current_organization']; ?>"/>
            </div>
            <?php }
if($fieldaccesright['field']['total_experiance']!="NA"){
              $readonlycheck = $fieldaccesright['field']['total_experiance'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Total Experience </span>
                <div class="input-group input-group-button">
                    <input aria-label="Enter number"  type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="Enter Total Experience"  name="total_experiance" value="<?php echo $data['total_experiance']; ?>"/>
                    <span class="input-group-text addon-style"><span class="">Years</span></span>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['relevant_experience']!="NA"){
              $readonlycheck = $fieldaccesright['field']['relevant_experience'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Relevant Experience </span>
                <div class="input-group input-group-button">
                    <input aria-label="Enter number"   type="number" class="form-control form-control-lg width1 <?php echo $readonlycheck; ?>"  placeholder="Enter Relevant Experience"  name="relevant_experience" value="<?php echo $data['relevant_experience']; ?>"/>
                    <span class="input-group-text addon-style"><span class="">Years</span></span>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['notice_period']!="NA"){
              $readonlycheck = $fieldaccesright['field']['notice_period'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Notice Period </span>
                <div class="input-group input-group-button">
                   <input aria-label="Enter number"  type="number" class="form-control width1 <?php echo $readonlycheck; ?>"  placeholder="Enter Notice Period" name="notice_period" value="<?php echo $data['notice_period']; ?>" />
                   <span class="input-group-text addon-style"><span class="">Days</span></span>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['location_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['location_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Work Location </span>
                <div class="<?php echo $readonlycheck; ?>">
                    <select aria-label="Location" class ="form-control js-example-basic-single" name="location_id">
                        <option value="">Select Any One</option>
                          <?php foreach ($locations as $locations) {
                            if($locations['parent_location_id'] =='') { ?>
                            <option <?php if($data['location_id'] == $locations['id']){ ?>selected <?php
} ?> value="<?php echo $locations['id']; ?>" >
                              <?php echo $locations['location_name']; ?>
                            </option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['employee_type']!="NA"){
              $readonlycheck = $fieldaccesright['field']['employee_type'] !="E"?"custom_readonly":""; ?>
           <div class="col-xl-6 mt-2" >
                <span class="form-label">Employment Type</span>
                <span class="float-end btn btn-success" onclick="employementviewhistoryppup(<?php echo $employee_id; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> History </span>
                <div class="form-radio <?php echo $readonlycheck; ?>">
                <?php
                 foreach ($employeement_types as $emptypes) {
                ?>
                  <div class="radio radio-inline">
                   <label class="form-label <?php echo $text_color_ety; ?>">
                     <input aria-label="Radio option" type="radio" onclick="employeementtypepopup(<?php echo $employee_id.",".$emptypes['genc_detail_code'];?>)" name="employee_type" <?php if($data['employee_type'] == $emptypes['genc_detail_code']) {?> checked="checked" <?php
} ?> value="<?php echo $emptypes['genc_detail_code'];?>" style="display:none" onkeydown="" />
                     <i class="helper"></i><?php echo $emptypes['genc_detail_name'];?> </label>
                   </div>
                <?php } ?>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['billable']!="NA"){
              $readonlycheck = $fieldaccesright['field']['billable'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Billable </span>
                <div class="form-radio <?php echo $readonlycheck; ?>">
                   <div class="radio radio-inline">
                           <label class="form-label <?php echo $text_color_ebi; ?>">
                         <input aria-label="Billable" type="radio" name="billable" <?php if($data['billable'] ==0) {?> checked="checked" <?php
} ?> value="0" style="display:none" />
                         <i class="helper"></i>Yes </label>
                    </div>
                    <div class="radio radio-inline">
                        <label class="form-label <?php echo $text_color_ebi; ?>">
                           <input aria-label="Billable" type="radio" name="billable" <?php if($data['billable'] ==1) {?> checked="checked" <?php
} ?> value="1" style="display:none" />
                           <i class="helper"></i>No </label>
                    </div>
                </div>
            </div>
            <?php }
if($fieldaccesright['field']['office_phone']!="NA"){
              $readonlycheck = $fieldaccesright['field']['office_phone'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Office Phone  </span>
                <input aria-label="Enter number"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_phone" placeholder="Enter Office Phone" value="<?php echo $data['office_phone']; ?>"/>
            </div>
            <?php }
if($fieldaccesright['field']['office_ext']!="NA"){
              $readonlycheck = $fieldaccesright['field']['office_ext'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">Extension </span>
                <input aria-label="Enter number"  type="number" class="form-control <?php echo $readonlycheck; ?>" name="office_ext" placeholder="Enter Extension" value="<?php echo $data['office_ext']; ?>"/>
            </div>
            <?php } ?>
        </div>
    <?php }elseif($_POST['pagetype'] =="personal_details" || $_POST['showpagetype'] =="personal_details"){
        $fieldaccesright = $vafield['personal_details'];
    ?>
    <div class="row">
     <?php if($fieldaccesright['field']['date_of_birth']!="NA"){
              $readonlycheck = $fieldaccesright['field']['date_of_birth'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Date of Birth </span>
            <div class="<?php echo $readonlycheck; ?>">
                <input aria-label="Input field" type="text" class="form-control form-control-lg datepicker"  value="<?php echo  ($data['date_of_birth']!="0000-00-00" && $data['date_of_birth']!="")?fromDate($data['date_of_birth']):""; ?>"  name="date_of_birth" placeholder="Enter Date of Birth" onchange="selectdateOfBirth(this.value)" />
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['marital_status']!="NA"){
              $readonlycheck = $fieldaccesright['field']['marital_status'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Marital Status</span>
            <div class="form-radio <?php echo $readonlycheck; ?>">
            <?php
                   foreach ($marital_statuses as $marital_stat) {
             ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                           <input aria-label="Marital Status" onclick="showmarriagedate(this.value);" type="radio" name="marital_status" <?php if($data['marital_status'] == $marital_stat['id']) {?> checked="checked" <?php
} ?> value="<?php echo $marital_stat['id']; ?>" style="display:none" onkeydown="" />
                           <i class="helper"></i>
                           <?php echo $marital_stat['marital_status_name']; ?>
                     </label>
                   </div>
             <?php } ?>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['wedding_date']!="NA"){
              $readonlycheck = $fieldaccesright['field']['wedding_date'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 " style="<?php echo $data['marital_status'] =="2"?"":"display: none"; ?>"  id="marriagedateshow">
            <div class="mt-2" id="marriagedateshow1">
                  <span  class="form-label">Marrige Date</span>
                  <div class="<?php echo $readonlycheck; ?>">
                      <input aria-label="Wedding Date" type="text"  class="form-control datepicker" name="wedding_date" id="wedding_date" value="<?php echo ($data['wedding_date']!="0000-00-00" && $data['wedding_date']!="")?fromDate($data['wedding_date']):""; ?>" placeholder="Enter Marrige Date"/>
                </div>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['gender']!="NA"){
              $readonlycheck = $fieldaccesright['field']['gender'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Gender</span>
            <div class="form-radio <?php echo $readonlycheck; ?>">
                   <?php
                       foreach ($gender_details as $gender_data) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label <?php echo $text_color_gen; ?>">
                       <input aria-label="Gender"  type="radio" name="gender" <?php if($data['gender'] == $gender_data['genc_detail_code']){ ?>  checked="checked" <?php
} ?> value="<?php echo $gender_data['genc_detail_code'];?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $gender_data['genc_detail_name']; ?>
                     </label>
               </div>
             <?php } ?>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['blood_group']!="NA"){
              $readonlycheck = $fieldaccesright['field']['blood_group'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Blood Group</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Blood Group" class ="form-select js-example-basic-single" name="blood_group">
                      <option value="">Select Any One</option>
                      <?php foreach ($blood_groups as $blood_group) { ?>
                    <option <?php if($data['blood_group'] == $blood_group['id']){ ?>selected  <?php
} ?> value="<?php echo $blood_group['id']; ?>" >
                      <?php echo $blood_group['blood_group']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['disability']!="NA"){
              $readonlycheck = $fieldaccesright['field']['disability'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Disability</span>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
               foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <button type="button" class="btn p-0 border-0 bg-transparent form-label" onclick="selectDisability()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                       <input aria-label="Disability" type="radio" name="disability" id="disability" <?php if($data['disability'] == $ynkey || ($data['disability'] =="" && $ynkey=='1')) {?>  checked="checked" <?php
} ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                     </label>
                   </div>
             <?php } ?>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['disability_percentage']!="NA"){
              $readonlycheck = $fieldaccesright['field']['disability_percentage'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" id="disabilitypercentage_id" style="<?php echo $data['disability'] =="0"?"":"display: none"; ?>">
            <span class="form-label">Disability percentage</button>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="" name="disability_percentage" value="<?php echo $data['disability_percentage']; ?>"/>
        </div>
        <?php }
if($fieldaccesright['field']['work_textile_industry']!="NA"){
              $readonlycheck = $fieldaccesright['field']['work_textile_industry'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Working In Textile Industry</span>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
                   foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                       <input aria-label="Work Textile Industry"   type="radio" name="work_textile_industry" <?php if($data['work_textile_industry '] == $ynkey || ($data['work_textile_industry'] =="" && $ynkey=='1')) {?>  checked="checked" <?php
} ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                    </label>
                   </div>
                 <?php } ?>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['physically_challenged_children']!="NA"){
              $readonlycheck = $fieldaccesright['field']['physically_challenged_children'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Physically Challenged Children</span>
            <div  class="form-radio <?php echo $readonlycheck; ?>">
               <?php
               foreach ($yesno as $ynkey=>$yn) {
                ?>
                <div class="radio radio-inline">
                     <label class="form-label">
                       <input aria-label="Physically Challenged Children"   type="radio" name="physically_challenged_children" <?php if($data['physically_challenged_children'] == $ynkey || ($data['work_textile_industry'] =="" && $ynkey=='1')) {?>  checked="checked" <?php
} ?> value="<?php echo $ynkey; ?>" style="display:none"/>
                       <i class="helper"></i>
                       <?php echo $yn; ?>
                     </label>
               </div>
             <?php } ?>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['soc_facebook']!="NA"){
              $readonlycheck = $fieldaccesright['field']['soc_facebook'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 mt-2" >
            <span  class="form-label">Facebook Link</span>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="Enter Facebook URL" name="soc_facebook" value="<?php echo $data['soc_facebook']; ?>"/>
        </div>
        <?php }
if($fieldaccesright['field']['soc_twitter']!="NA"){
              $readonlycheck = $fieldaccesright['field']['soc_twitter'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 mt-2" >
            <span  class="form-label">Twitter Link</span>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="Enter Twitter URL" name="soc_twitter" value="<?php echo $data['soc_twitter']; ?>"/>
        </div>
        <?php }
if($fieldaccesright['field']['soc_linkedin']!="NA"){
              $readonlycheck = $fieldaccesright['field']['soc_linkedin'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 mt-2" >
            <span  class="form-label">Linkedin Link</span>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  placeholder="Enter LinkedIn URL" name="soc_linkedin" value="<?php echo $data['soc_linkedin']; ?>"/>
        </div>
        <?php } ?>
    </div>
    <?php }elseif($_POST['pagetype'] =="bank_details" || $_POST['showpagetype'] =="bank_details"){
        $fieldaccesright = $vafield['bank_details'];
    ?>
    <div class="row">
        <?php if($bank_work_flow == 'N'){
         if($fieldaccesright['field']['bank_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['bank_id'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Bank</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Bank" class ="form-control form-control-lg form-select js-example-basic-single"  name="bank_id">
                    <option value="">Select Any One</option>
                    <?php foreach ($banks as $bnk) {  ?>
                       <option <?php if($data['bank_id'] == $bnk['id']){ ?>selected <?php
} ?> value="<?php echo $bnk['id']; ?>" >
                        <?php echo $bnk['bank_name']; ?>
                      </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['bank_link']!="NA"){
              $readonlycheck = $fieldaccesright['field']['bank_link'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 mt-2" >
            <span  class="form-label">IFSC Code</span>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_link" value="<?php echo $data['bank_link']; ?>" placeholder="Enter IFSC Code"/>
        </div>
        <?php }
if($fieldaccesright['field']['bank_account_no']!="NA"){
              $readonlycheck = $fieldaccesright['field']['bank_account_no'] !="E"?"custom_readonly":""; ?>
        <div class="col-lg-6 mt-2" >
            <span  class="form-label">Account No</span>
            <input aria-label="Input field" type="text" class="form-control <?php echo $readonlycheck; ?>"  name="bank_account_no" value="<?php echo $data['bank_account_no']; ?>" placeholder="Enter Account No"/>
        </div>
        <?php } ?>
    </div>

    <?php }
    }elseif($_POST['pagetype'] =="employee_buddies" || $_POST['showpagetype'] =="employee_buddies"){
        $fieldaccesright = $vafield['employee_buddies'];
    ?>
    <div class="row">
        <?php if($fieldaccesright['field']['role_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['role_id'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Employment Role</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Role" class ="form-control form-control-lg form-select js-example-basic-single" name="role_id">
                    <option value="">Select Any One</option>
                    <?php foreach ($employment_roles as $employment_rol) {
                     ?>
                     <option <?php if($data['role_id'] == $employment_rol['id']){ ?>selected  <?php
} ?> value="<?php echo $employment_rol['id']; ?>" >
                      <?php echo $employment_rol['role_name']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['reporting_manager_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['reporting_manager_id'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Repoting to</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Reporting Manager" class ="form-control  js-example-basic-single" name="reporting_manager_id">
                    <option value="">Select Any One</option>
                    <?php foreach($application_users as $application_users){ ?>
                     <option <?php if($data['reporting_manager_id'] == $application_users['id']){ ?>selected <?php
} ?> value="<?php echo $application_users['id']; ?>" >
                      <?php echo $application_users['display_name']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['admin_buddy']!="NA"){
              $readonlycheck = $fieldaccesright['field']['admin_buddy'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Admin Buddy</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Select option"  class ="form-select js-example-basic-single required" <?php echo $read_only_ln; ?> name="admin_buddy">
                      <option value="">Select Any One</option>
                      <?php foreach ($all_employees as $admin_buddy_res) { ?>
                       <option value="<?php echo $admin_buddy_res['id']; ?>"  <?php echo $data['admin_buddy'] == $admin_buddy_res['id']?"selected":""; ?>>
                        <?php echo $admin_buddy_res['display_name']; ?>
                      </option>
                    <?php } ?>
                 </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['hr_buddy']!="NA"){
              $readonlycheck = $fieldaccesright['field']['hr_buddy'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">HR Buddy</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Hr Buddy"  class ="form-select js-example-basic-single required" name="hr_buddy" <?php echo $read_only_ln; ?>>
                    <option value="">Select Any One</option>
                    <?php foreach ($all_employees as $hr_buddy_res) { ?>
                     <option value="<?php echo $hr_buddy_res['id']; ?>" <?php echo $data['hr_buddy'] == $hr_buddy_res['id']?"selected":""; ?>>
                      <?php echo $hr_buddy_res['display_name']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['finance_buddy']!="NA"){
              $readonlycheck = $fieldaccesright['field']['finance_buddy'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Finance Buddy </span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Finance Buddy"  class ="form-select js-example-basic-single required" name="finance_buddy" <?php echo $read_only_ln; ?>>
                    <option value="">Select Any One</option>
                    <?php foreach ($all_employees as $finance_buddy_res) { ?>
                     <option value="<?php echo $finance_buddy_res['id']; ?>" <?php echo $data['finance_buddy'] == $finance_buddy_res['id']?"selected":""; ?>>
                      <?php echo $finance_buddy_res['display_name']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['project_buddy']!="NA"){
              $readonlycheck = $fieldaccesright['field']['project_buddy'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Project Buddy</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Project Buddy"  class ="form-select js-example-basic-single required" name="project_buddy" >
                    <option value="">Select Any One</option>
                    <?php foreach ($all_employees as $project_buddy_res) { ?>
                     <option value="<?php echo $project_buddy_res['id']; ?>" <?php echo $data['project_buddy'] == $project_buddy_res['id']?"selected":""; ?>>
                      <?php echo $project_buddy_res['display_name']; ?>
                    </option>
                  <?php } ?>
                </select>
            </div>
        </div>
        <?php }
if($fieldaccesright['field']['loc_buddy']!="NA"){
              $readonlycheck = $fieldaccesright['field']['loc_buddy'] !="E"?"custom_readonly":""; ?>
        <div class="col-xl-6 mt-2" >
            <span class="form-label">Location Buddy</span>
            <div class="<?php echo $readonlycheck; ?>">
                <select aria-label="Loc Buddy"  class ="form-select js-example-basic-single required" name="loc_buddy" >
                    <option value="">Select Any One</option>
                    <?php foreach ($all_employees as $location_buddy_res) { ?>
                     <option value="<?php echo $location_buddy_res['id']; ?>"  <?php echo $data['loc_buddy'] == $location_buddy_res['id']?"selected":""; ?>>
                      <?php echo $location_buddy_res['display_name']; ?>
                    </option>
                      <?php } ?>
                </select>
            </div>
        </div>
        <?php } ?>
    </div>

<!-- added by koti start -->
<?php
    }elseif(($_POST['pagetype'] ?? '') =="employee_other_access" || ($_POST['showpagetype'] ?? '') =="employee_other_access"){
		$fieldaccesright = $vafield['employee_other_access'] ?? array(); ?>
		<div class="row">
		<?php if(($fieldaccesright['field']['job_title_id'] ?? '') !="NA"){
		  	$readonlycheck = ($fieldaccesright['field']['job_title_id'] ?? '') !="E"?"custom_readonly":""; ?>
		   	<div class="col-xl-6 mt-2">
		        <span class="form-label">Job Title </span>
		        <div class="<?php echo $readonlycheck; ?>">
			       <select aria-label="Job Title" class ="form-control form-control-lg form-select js-example-basic-single" name="job_title_id">
					  	<option value="">Select Any One</option>
					  	<?php foreach ((array)$job_titles as $job_title) {
					    ?>
					    	<option <?php if(($data['job_title_id'] ?? '') == ($job_title['id'] ?? '')){ ?> selected <?php } ?> value="<?php echo $job_title['id'] ?? ''; ?>" >
					      	<?php echo htmlspecialchars($job_title['job_title'] ?? '', ENT_QUOTES); ?>
					    	</option>
					  <?php } ?>
					</select>
				</div>
		    </div>
		    <?php }?>
		<?php if (($fieldaccesright['field']['is_muslim'] ?? '') != "NA") {
	        $readonlycheck = ($fieldaccesright['field']['is_muslim'] ?? '') != "E" ? "custom_readonly" : ""; ?>
	        <div class="col-xl-6 mt-2">
	            <span class="form-label d-block">Are You Muslim ?</span>
	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input aria-label="Muslim Yes" class="form-check-input" type="radio" name="cover_under_pf" value="0" <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '0') echo 'checked'; ?>>
	                    <label class="form-check-label">YES</label>
	                </div>
	                <div class="form-check form-check-inline">
	                    <input aria-label="Muslim No" class="form-check-input" type="radio" name="cover_under_pf" value="1" <?php if (isset($data['is_muslim']) && $data['is_muslim'] === '1') echo 'checked'; ?>>
	                    <label class="form-check-label">NO</label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    <?php if (($fieldaccesright['field']['is_local'] ?? '') != "NA") {
	        $readonlycheck = ($fieldaccesright['field']['is_local'] ?? '') != "E" ? "custom_readonly" : ""; ?>
	        <div class="col-xl-6 mt-2">
	            <span class="form-label d-block">Are You Local ?</span>
	            <div class="<?php echo $readonlycheck; ?>">
	                <div class="form-check form-check-inline">
	                    <input aria-label="Local Yes" class="form-check-input" type="radio" name="cover_under_pf" value="0" <?php if (isset($data['is_local']) && $data['is_local'] === '0') echo 'checked'; ?>>
	                    <label class="form-check-label">YES</label>
	                </div>
	                <div class="form-check form-check-inline">
	                    <input aria-label="Local No" class="form-check-input" type="radio" name="cover_under_pf" value="1" <?php if (isset($data['is_local']) && $data['is_local'] === '1') echo 'checked'; ?>>
	                    <label class="form-check-label">NO</label>
	                </div>
	            </div>
	        </div>
	    <?php } ?>
	    </div>
	}elseif($_POST['pagetype'] =="employee_access" || $_POST['showpagetype'] =="employee_access"){
        $fieldaccesright = $vafield['employee_access'];

    ?>
    <div class="row">
        <!-- Cover Under PF -->
        <?php if ($fieldaccesright['field']['cover_under_pf'] != "NA") {
            $readonlycheck = $fieldaccesright['field']['cover_under_pf'] != "E" ? "custom_readonly" : ""; ?>

            <div class="col-xl-6 mt-2">
                <span class="form-label d-block">Cover Under PF</span>

                <div class="<?php echo $readonlycheck; ?>">
                    <div class="form-check form-check-inline">
                        <input aria-label="Cover Under Pf" class="form-check-input"
                               type="radio"
                               name="cover_under_pf"
                               value="0"
                               <?php if (isset($data['cover_under_pf']) && $data['cover_under_pf'] === '0') { echo 'checked';
} ?>>
                        <span class="form-check-label">YES</span>
                    </div>

                    <div class="form-check form-check-inline">
                        <input aria-label="Cover Under Pf" class="form-check-input"
                               type="radio"
                               name="cover_under_pf"
                               value="1"
                               <?php if (isset($data['cover_under_pf']) && $data['cover_under_pf'] === '1') { echo 'checked';
} ?>>
                        <span class="form-check-label">NO</span>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Cover Under ESI -->
        <?php if ($fieldaccesright['field']['cover_under_esi'] != "NA") {
            $readonlycheck = $fieldaccesright['field']['cover_under_esi'] != "E" ? "custom_readonly" : ""; ?>

            <div class="col-xl-6 mt-2">
                <span class="form-label d-block">Cover Under ESI</span>

                <div class="<?php echo $readonlycheck; ?>">
                    <div class="form-check form-check-inline">
                        <input aria-label="Cover Under Esi" class="form-check-input"
                               type="radio"
                               name="cover_under_esi"
                               value="0"
                               <?php if (isset($data['cover_under_esi']) && $data['cover_under_esi'] === '0') { echo 'checked';
} ?>>
                        <span class="form-check-label">YES</span>
                    </div>

                    <div class="form-check form-check-inline">
                        <input aria-label="Cover Under Esi" class="form-check-input"
                               type="radio"
                               name="cover_under_esi"
                               value="1"
                               <?php if (isset($data['cover_under_esi']) && $data['cover_under_esi'] === '1') { echo 'checked';
} ?>>
                        <span class="form-check-label">NO</span>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Clock In & Clock Out Access -->
        <?php if ($fieldaccesright['field']['clock_event_type'] != "NA") {
            $readonlycheck = $fieldaccesright['field']['clock_event_type'] != "E" ? "custom_readonly" : ""; ?>

            <div class="col-xl-6 mt-2">
                <span class="form-label d-block">ClockIn & ClockOut Access</span>

                <div class="<?php echo $readonlycheck; ?>">
                    <div class="form-check form-check-inline">
                        <input aria-label="Clock Event Type" class="form-check-input"
                               type="radio"
                               name="clock_event_type"
                               value="Y"
                               <?php if (isset($data['clock_event_type']) && $data['clock_event_type'] === 'Y') { echo 'checked';
} ?>>
                        <span class="form-check-label">YES</span>
                    </div>

                    <div class="form-check form-check-inline">
                        <input aria-label="Clock Event Type" class="form-check-input"
                               type="radio"
                               name="clock_event_type"
                               value="N"
                               <?php if (isset($data['clock_event_type']) && $data['clock_event_type'] === 'N') { echo 'checked';
} ?>>
                        <span class="form-check-label">NO</span>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if($fieldaccesright['field']['b_machine_id']!="NA"){
              $readonlycheck = $fieldaccesright['field']['b_machine_id'] !="E"?"custom_readonly":""; ?>
            <div class="col-xl-6 mt-2" >
                <span class="form-label">B Machine Id</span>
                <input aria-label="Input field" class="form-control form-control-lg <?php echo $readonlycheck; ?>" type="text" name="b_machine_id" value="<?php echo $data['b_machine_id']; ?>" />
            </div>
        <?php } ?>

    </div>

<!-- added by koti end -->

    <?php }elseif($_POST['pagetype'] =="proof_details" || $_POST['showpagetype'] =="proof_details"){
        $fieldaccesright = $vafield['proof_details'];
        $attached_files = json_decode($data['attached_files'],1);
    ?>

    <table class="table table-vertical-center">
        <thead class="thead-light">
            <tr>
                <th class="text-center" scope="col">Type</th>
                <th class="text-center"  scope="col">Number</th>
                <th  class="text-center" scope="col">Expiry Date</th>
                <th  class="text-center" scope="col">File</th>
            </tr>
        </thead>
        <tbody>
            <?php if($fieldaccesright['field']['adhar_no']!="NA"){
                $readonlycheck = $fieldaccesright['field']['adhar_no'] !="E"?"custom_readonly":"";
                 ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">Aadhar Card</th>
                <td class="p-2">
                    <input aria-label="Adhar No" type="text" name="adhar_no" class="form-control" value="<?php echo $data['adhar_no']; ?>" placeholder="Enter Aadhar Card">
                </td>
                <td class="p-2"> - </td>
                <td class="p-2">
                    <input aria-label="Aadhar Attachment" class="form-control" type="file" id="aadhar_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_aadhar_attachment" value="<?php echo $attached_files['aadhar_attachment']; ?>" >
                    <?php if($attached_files['aadhar_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['aadhar_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['aadhar_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php }
if($fieldaccesright['field']['passport']!="NA"){
                $readonlycheck = $fieldaccesright['field']['passport'] !="E"?"custom_readonly":"";
                ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">Passport</th>
                <td class="p-2">
                    <input aria-label="Passport" type="text" name="passport" class="form-control" value="<?php echo $data['passport']; ?>" placeholder="Enter Passport No.">
                </td>
                <td class="p-2">
                    <input aria-label="Passport Expiry Date" type="text" name="passport_expiry_date" class="form-control datepicker" value="<?php echo ($data['passport_expiry_date']!="0000-00-00" && $data['passport_expiry_date']!="")?fromDate($data['passport_expiry_date']):""; ?>" placeholder="Enter Expiry Date">
                </td>
                <td class="p-2">
                    <input aria-label="Passport Attachment" class="form-control" type="file" id="passport_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_passport_attachment" value="<?php echo $attached_files['passport_attachment']; ?>" >
                    <?php if($attached_files['passport_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['passport_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['passport_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php }
if($fieldaccesright['field']['driving_licence']!="NA"){
            $readonlycheck = $fieldaccesright['field']['driving_licence'] !="E"?"custom_readonly":"";
             ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">Driving Licence</th>
                <td class="p-2">
                    <input aria-label="Driving Licence" type="text" name="driving_licence" class="form-control" value="<?php echo $data['driving_licence']; ?>" placeholder="Enter Driving Licence">
                </td>
                <td class="p-2">
                    <input aria-label="Driving Licence Expiry Date" type="text" name="driving_licence_expiry_date" class="form-control datepicker" value="<?php echo ($data['driving_licence_expiry_date']!="0000-00-00" && $data['driving_licence_expiry_date']!="")?fromDate($data['driving_licence_expiry_date']):""; ?>" placeholder="Enter Driving Licence Expiry Date">
                </td>
                <td class="p-2">
                    <input aria-label="Driving Licence Attachment" class="form-control" type="file" id="driving_licence_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_driving_licence_attachment" value="<?php echo $attached_files['driving_licence_attachment']; ?>" >

                    <?php if($attached_files['driving_licence_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['driving_licence_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['driving_licence_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php }
if($fieldaccesright['field']['pan_number']!="NA"){
                $readonlycheck = $fieldaccesright['field']['pan_number'] !="E"?"custom_readonly":"";
                ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">PAN Number</th>
                <td class="p-2">
                    <input aria-label="Pan Number" type="text" name="pan_number" class="form-control" value="<?php echo $data['pan_number']; ?>" placeholder="Enter PAN Number">
                </td>
                <td class="p-2"> - </td>
                <td class="p-2">
                    <input aria-label="Pan Attachment" class="form-control" type="file" id="pan_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_pan_attachment" value="<?php echo $attached_files['pan_attachment']; ?>" >

                    <?php if($attached_files['pan_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pan_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pan_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php }
if($fieldaccesright['field']['pf_no']!="NA"){
                $readonlycheck = $fieldaccesright['field']['pf_no'] !="E"?"custom_readonly":"";
                ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">PF Number</th>
                <td class="p-2">
                    <input aria-label="Pf No" type="text" name="pf_no" class="form-control" value="<?php echo $data['pf_no']; ?>" placeholder="Enter PF Number">
                </td>
                <td class="p-2"> - </td>
                <td class="p-2">
                    <input aria-label="Pf Attachment" class="form-control" type="file" id="pf_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_pf_attachment" value="<?php echo $attached_files['pf_attachment']; ?>" >

                    <?php if($attached_files['pf_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pf_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pf_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php }
if($fieldaccesright['field']['pf_uan']!="NA"){
                $readonlycheck = $fieldaccesright['field']['pf_uan'] !="E"?"custom_readonly":"";
                ?>
            <tr class="<?php echo $readonlycheck; ?>">
                <th scope="col" class="p-2">UAN Number</th>
                <td class="p-2">
                    <input aria-label="Pf Uan" type="text" name="pf_uan" class="form-control" value="<?php echo $data['pf_uan']; ?>" placeholder="Enter UAN Number">
                </td>
                <td class="p-2"> - </td>
                <td class="p-2">
                    <input aria-label="Pf Uan Attachment" class="form-control" type="file" id="pf_uan_attachment" value="" accept=".jpg,.png,.pdf">
                    <input class="form-control " type="hidden" name="old_pf_uan_attachment" value="<?php echo $attached_files['pf_uan_attachment']; ?>" >
                    <?php if($attached_files['pf_uan_attachment'] !=""){ ?>
                    <a href="uploads/all/<?php echo $attached_files['pf_uan_attachment']; ?>" class="text-primary mt-2 f-10" download><i class="ti-cloud-down"></i> <?php echo $attached_files['pf_uan_attachment']; ?></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php }elseif($_POST['pagetype'] =="experience_details" || $_POST['showpagetype'] =="experience_details"){
        $fieldaccesright = $vafield['experience_details'];
        if($fieldaccesright['field']['employee_experiances']!="NA"){
            $readonlycheck = $fieldaccesright['field']['employee_experiances'] !="E"?"custom_readonly":"";
        }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="extable">
        <tr>
            <th scope="col" style="width: 30%;">Company Name <span class="text-danger">*</span></th>
            <th scope="col" style="width: 30%;">Job Description</th>
            <th scope="col" style="width: 16%;"> From Date <span class="text-danger">*</span></th>
            <th scope="col" style="width: 16%;"> To Date</th>
            <th scope="col" style="width: 8%;"> File <span class="f-10">(jpg,pdf)</span></th>
        </tr>
        <?php
        $k=1;
        if(count((array)$emp_work_experiences)=='0'){
            $emp_work_experiences[] = array();
        }
        foreach ($emp_work_experiences as $expe) {
            ?>
            <tr id="exp_details_<?php echo $k; ?>">
                <td class="p-2">
                    <input type="hidden" name="expe_rowid[]" class="form-control" value="<?php echo $expe['id']; ?>">
                    <input aria-label="Company Name[]" type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>" placeholder="Enter Company Name">
                </td>
                <td class="p-2">
                    <input aria-label="Job Descrption[]" type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off" placeholder="Enter Job Description">
                </td>
                <td class="p-2">
                    <input aria-label="Work From[]" type="text d-block" name="work_from[]" class="form-control datepicker"  value="<?php echo ($expe['work_from']!="0000-00-00" && $expe['work_from']!="")?fromDate($expe['work_from']):""; ?>" autocomplete="off" placeholder="Enter From Date">
                </td>
                <td class="p-2">
                    <input aria-label="Work To[]" type="text" name="work_to[]" class="form-control datepicker" value="<?php echo ($expe['work_to']!="0000-00-00" && $expe['work_to']!="")?fromDate($expe['work_to']):""; ?>"autocomplete="off" placeholder="Enter To Date">
                </td>
                <td class="p-2 w-b-w-s">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="image-input image-input-empty image-input-outline" id="kt_image_5">
                                 <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Upload">
                                  <span><i class="fa fa-upload"></i></span>
                                  <input aria-label="Work Experience Attachment" type="file" name="work_experience_attachment" class="form-control" onchange="getAttachedFileName(this,'workexperiencefilename_<?php echo $k; ?>')">
                                  <input type="hidden" name="old_work_experience_attachment[]" value="<?php echo $expe['file']; ?>">
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6 p-0">
                            <?php if($expe['file']!=""){ ?>
                                <i class="ti-eye cursor-pointer" onclick="dynamicFilePriviews('uploads/all/<?php echo $expe['file']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                              <?php } ?>
                         </div>
                     </div>
                      <span id="workexperiencefilename_<?php echo $k; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('exp_details_<?php echo $k; ?>')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $k++;
}  ?>
        </table>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <button class="btn btn-success"  onclick="addexpreencerows()" id="addRows_exp" type="button">+ Add More</button>
            </div>
        </div>

   <?php }elseif($_POST['pagetype'] =="employee_qualifications" || $_POST['showpagetype'] =="employee_qualifications"){
        $fieldaccesright = $vafield['employee_qualifications'];
        if($fieldaccesright['field']['employee_qualifications']!="NA"){
            $readonlycheck = $fieldaccesright['field']['employee_qualifications'] !="E"?"custom_readonly":"";
        }
    ?>

    <table class="table <?php echo $readonlycheck; ?>" id="edutable">
        <tr>
            <th scope="col" class="" style="width: 20%;">Institute Name <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 20%;">Type <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 15%;"> Start Date <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 15%;"> End Date</th>
            <th scope="col" class="thheader" style="width: 10%;">Score</th>
            <th scope="col" style="width: 10%;"> File <span class="f-10">(jpg,pdf)</span></th>
            <th scope="col" style="width: 10%;"> </th>
        </tr>
        <?php
        if(count((array)$emp_educations)=='0'){
            $emp_educations[] = array();
        }
        $mk = 1;
        foreach($emp_educations as $inst) { ?>
            <tr id="emp_educations_<?php echo $mk; ?>">
                <td class="p-2">
                    <input type="hidden" name="institute_rowid[]" class="form-control " value="<?php echo $inst['id']; ?>">
                    <input aria-label="Institute Name[]" type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off" placeholder="Enter Institute Name">
                </td>
                <td class="w-full p-2">
                    <select aria-label="Education Type[]" class="form-control form-select js-example-basic-single" name="education_type[]" >
                        <option value=""> Select Any One</option>
                        <?php foreach ($education_type as  $edutype) {   ?>
                            <option  <?php if($inst['education_type']== $edutype['genc_detail_code']){  ?> selected <?php
} ?>   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                        <?php   } ?>
                        <option value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Start Date[]" type="text" name="start_date[]" class="form-control datepicker"  value="<?php echo ($inst['start_date']!="0000-00-00" && $inst['start_date']!="")?fromDate($inst['start_date']):""; ?>"  autocomplete="off" placeholder="Enter Start Date">
                </td>
                <td class="p-2">
                    <input aria-label="End Date[]" type="text" name="end_date[]" class="form-control datepicker"  value="<?php echo ($inst['end_date']!="0000-00-00" && $inst['end_date']!="")?fromDate($inst['end_date']):""; ?>" autocomplete="off" placeholder="Enter End Date">
                </td>
                <td class="p-2">
                    <input aria-label="Score[]" type="text" name="score[]" class="form-control "  value="<?php echo $inst['score']; ?>"  autocomplete="off" placeholder="Enter Score">
                </td>
                <td class="p-2 w-b-w-s">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="image-input image-input-empty image-input-outline" id="kt_image_5_2">
                                 <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Upload">
                                  <span><i class="fa fa-upload"></i></span>
                                  <input aria-label="Educations Attachment" type="file" name="educations_attachment" class="form-control" onchange="getAttachedFileName(this,'educationsfilename_<?php echo $mk; ?>')">
                                  <input type="hidden" name="old_educations_attachment[]" value="<?php echo $inst['file']; ?>">
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6 p-0">
                            <?php if($inst['file']!=""){ ?>
                                <i class="ti-eye cursor-pointer" onclick="dynamicFilePriviews('uploads/all/<?php echo $inst['file']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                              <?php } ?>
                         </div>
                     </div>
                      <span id="educationsfilename_<?php echo $mk; ?>" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span>
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_educations_<?php echo $mk; ?>')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-trash"></i>
                    </span>
                </td>
                </tr>
            <?php $mk++;
}  ?>
            </table>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <button class="btn btn-success" onclick="addEmpEducationsRows()" id="addRows_edu" type="button">+ Add More</button>
                </div>
            </div>

    <?php }elseif($_POST['pagetype'] =="employee_dependents" || $_POST['showpagetype'] =="employee_dependents"){
        $fieldaccesright = $vafield['employee_dependents'];
        if($fieldaccesright['field']['employee_dependents']!="NA"){
            $readonlycheck = $fieldaccesright['field']['employee_dependents'] !="E"?"custom_readonly":"";
        }
    ?>

    <table class="table <?php echo $readonlycheck; ?>" id="deptable">
        <tr>
            <th scope="col" class="" style="width: 30%;"> Name <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 30%;">Relationship Type <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 30%;">DOB</th>
            <th scope="col" class="thheader" style="width: 10%;"></th>
        </tr>
        <?php $mm = 1;
        if(count((array)$emp_dependents)=='0'){
            $emp_dependents[] = array();
        }
        foreach ($emp_dependents as $depe) { ?>
            <tr id="employee_dependents_<?php echo $mm; ?>">

                <td class="p-2">
                    <input type="hidden" name="dependent_rowid[]" class="form-control " value="<?php echo $depe['id']; ?>">
                    <input aria-label="Dependent Name[]" type="text" name="dependent_name[]" class="form-control " value="<?php echo $depe['dependent_name']; ?>" autocomplete="off" placeholder="Enter Dependent Name">  </td>
                <td class="w-full p-2">
                    <select aria-label="Dependent Relationship Type[]" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" >
                        <option value=""> Select Any One</option>
                        <?php foreach ($relations as  $reladata) {   ?>
                            <option   <?php if($depe['dependent_relationship_type']== $reladata['id']){  ?> selected <?php
} ?> value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                        <?php   } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Dependent Date Of Birth[]" type="text" name="dependent_date_of_birth[]" class="form-control datepicker"  value="<?php echo ($depe['dependent_date_of_birth']!="0000-00-00" && $depe['dependent_date_of_birth']!="")?fromDate($depe['dependent_date_of_birth']):""; ?>" autocomplete="off" placeholder="Enter Date Of Birth">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('employee_dependents_<?php echo $mm; ?>')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $mm++;
}  ?>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success"  onclick="adddeprows()" id="addRows_dep" type="button">+ Add More</button>
        </div>
    </div>

    <?php }elseif($_POST['pagetype'] =="employee_contacts" || $_POST['showpagetype'] =="employee_contacts"){
        $fieldaccesright = $vafield['employee_contacts'];
        if($fieldaccesright['field']['employee_contacts']!="NA"){
            $readonlycheck = $fieldaccesright['field']['employee_contacts'] !="E"?"custom_readonly":"";
        }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="emergetable">
        <tr>
            <th scope="col" class="thheader" style="width: 30%;">Name <span class="text-danger">*</span></th>
            <th scope="col" class="" style="width: 30%;">Relationship <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 20%;">Home No</th>
            <th scope="col" class="thheader" style="width: 20%;">Mobile No</th>
            <th scope="col" class="thheader" style="width: 10%;"></th>
        </tr>
        <?php
        $mc = 1;
        if(count((array)$emp_contacts)=='0'){
            $emp_contacts[] = array();
        }
        foreach ($emp_contacts as $eno) { ?>
            <tr id="emp_contacts_<?php echo $mc; ?>">
                <td class="p-2">
                    <input type="hidden" name="contact_rowid[]" class="form-control " value="<?php echo $eno['id']; ?>">
                    <input aria-label="Contact Name[]" type="text" name="contact_name[]" class="form-control " value="<?php echo $eno['contact_name']; ?>" autocomplete="off" placeholder="Enter Contact Name">
                </td>
                <td class="w-full p-2">
                    <select aria-label="Contact Relationship[]" class="form-control form-select js-example-basic-single" name="contact_relationship[]" >
                        <option value=""> Select Any One</option>
                        <?php foreach ($relations as  $reladata) {   ?>
                            <option   <?php if($eno['contact_relationship']== $reladata['id']){  ?> selected <?php
} ?> value="<?php echo $reladata['id']; ?>"> <?php echo $reladata['relation_name']; ?></option>
                        <?php   } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Contact Home No[]" type="number" name="contact_home_no[]" class="form-control " value="<?php echo $eno['contact_home_no']; ?>" autocomplete="off" placeholder="Enter Contact Home No">
                </td>
                <td class="p-2">
                    <input aria-label="Contact Mobile No[]" type="number" name="contact_mobile_no[]" class="form-control " value="<?php echo $eno['contact_mobile_no']; ?>" autocomplete="off" placeholder="Enter Contact Mobile No">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_contacts_<?php echo $mc; ?>')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $mc++;
}  ?>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success "  onclick="addEmployeeContactsRows()" id="addRows_contact" type="button">+ Add More</button>
        </div>
    </div>

    <?php }elseif($_POST['pagetype'] =="emp_visa" || $_POST['showpagetype'] =="emp_visa"){
        $fieldaccesright = $vafield['emp_visa'];
        if($fieldaccesright['field']['emp_visa']!="NA"){
            $readonlycheck = $fieldaccesright['field']['emp_visa'] !="E"?"custom_readonly":"";
        }
    ?>
    <table class="table <?php echo $readonlycheck; ?>" id="emp_visatable">
        <tr>
            <th scope="col" class="thheader" style="width: 30%;">VISA Number <span class="text-danger">*</span></th>
            <th scope="col" class="" style="width: 30%;">Country <span class="text-danger">*</span></th>
            <th scope="col" class="thheader" style="width: 20%;">Issue Date</th>
            <th scope="col" class="thheader" style="width: 20%;">Expiry Date</th>
            <th scope="col" class="thheader" style="width: 10%;"></th>
        </tr>
        <?php
        $mc = 1;
        if(count((array)$emp_visa)=='0'){
            $emp_visa[] = array();
        }
        foreach ($emp_visa as $empvisa) { ?>
            <tr id="emp_visa_<?php echo $mc; ?>">
                <td class="p-2">
                    <input type="hidden" name="visa_rowid[]" class="form-control " value="<?php echo $empvisa['id']; ?>">
                    <input aria-label="Visa Number[]" type="text" name="visa_number[]" class="form-control " value="<?php echo $empvisa['visa_number']; ?>" autocomplete="off" placeholder="Enter Visa Number">
                </td>
                <td class="w-full p-2">
                    <select aria-label="Country []" class="form-control form-select js-example-basic-single" name="country_id[]" >
                        <option value=""> Select Any One</option>
                        <?php foreach ($countries as  $countrys) {   ?>
                            <option   <?php if($empvisa['country_id']== $countrys['id']){  ?> selected <?php
} ?> value="<?php echo $countrys['id']; ?>"> <?php echo $countrys['country_name']; ?></option>
                        <?php   } ?>
                    </select>
                </td>
                <td class="p-2">
                    <input aria-label="Visa Issue Date[]" type="text" name="visa_issue_date[]" class="form-control datepicker" value="<?php echo ($empvisa['visa_issue_date']!="0000-00-00" && $empvisa['visa_issue_date']!="")?fromDate($empvisa['visa_issue_date']):""; ?>" autocomplete="off" placeholder="Enter Visa Issue Date">
                </td>
                <td class="p-2">
                    <input aria-label="Visa Expiry Date[]" type="text" name="visa_expiry_date[]" class="form-control datepicker" value="<?php echo ($empvisa['visa_expiry_date']!="0000-00-00" && $empvisa['visa_expiry_date']!="")?fromDate($empvisa['visa_expiry_date']):""; ?>" autocomplete="off" placeholder="Enter Visa Expiry Date">
                </td>
                <td class="p-2">
                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow('emp_visa_<?php echo $mc; ?>')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                   <i class="ti-trash"></i>
                    </span>
                </td>
            </tr>
        <?php $mc++;
}  ?>
    </table>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-success "  onclick="addEmpVisatableRows()" id="addRows_visa" type="button">+ Add More</button>
        </div>
    </div>

    <?php } ?>

<?php } ?>
