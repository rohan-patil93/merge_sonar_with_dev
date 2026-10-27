<?php
$rid =  encodeId($_GET['Rid']);
$cand = explode(':', $rid);
$Rid = $cand['1'];
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_access_right'])){
  $_POST['company_id']=$_SESSION['company_id'];
  $_POST['user_id']=$_SESSION['userid'];
  $url = API."?mode=insertemployeesdisplayfields&method=post";
  $response2 = file_post_contents($url, $_POST, 'insert');
  $response1 = json_decode($response2,1);
  $response = $response1['result'];
  if($response['status'] == 'fail'){
    if(isset($_SESSION['error'][$response['error_no']])){
      $dis_err = explode(",", $response['other']);
      foreach ($dis_err as $va) {
        $dis_er [] = $dis_labels[$va];
      }
      $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
    }else{
      $error_message = $response['message'];
    }
  }else{
    ?>
    <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

       }
     }
   </script>
   <?php
  }
}
$bank_work_flow = "Y";
$noteditable = array('emp_code','email_id','date_of_leaving','age');

$accessrightarry = array("basic_information"=>array("access"=>"Y","fields"=>array('emp_code'=>array("access"=>"Y","edit"=>"N"),'email_id'=>array('access'=>"Y","edit"=>"N"),'first_name'=>array('access'=>"Y","edit"=>"N"),'middle_name'=>array('access'=>"N","edit"=>"N"),'last_name'=>array('access'=>"Y","edit"=>"Y"),'phone'=>array('access'=>"Y","edit"=>"N"),'address'=>array('access'=>"Y","edit"=>"Y"),'contact_address'=>array('access'=>"Y","edit"=>"N"),'post_code'=>array('access'=>"Y","edit"=>"N"),'fax'=>array('access'=>"Y","edit"=>"N"),'image'=>array('access'=>"Y","edit"=>"N"))),
"work"=>array("access"=>"Y","fields"=>array('department_id'=>array('access'=>"Y","edit"=>"Y"),
  'designation_id'=>array('access'=>"Y","edit"=>"Y"),
  'band_id'=>array('access'=>"Y","edit"=>"Y"),
  'level_id'=>array('access'=>"Y","edit"=>"Y"),
  'vertical_id'=>array('access'=>"Y","edit"=>"Y"),
  'hiring_source'=>array('access'=>"Y","edit"=>"Y"),
  'joining_date'=>array('access'=>"Y","edit"=>"Y"),
  'experience_current_organization'=>array('access'=>"Y","edit"=>"Y"),
  'total_experiance'=>array('access'=>"Y","edit"=>"Y"),
  'relevant_experience'=>array('access'=>"Y","edit"=>"Y"),
  'notice_period'=>array('access'=>"Y","edit"=>"Y"),
  'location_id'=>array('access'=>"Y","edit"=>"Y"),
  'employee_type'=>array('access'=>"Y","edit"=>"Y"),
  'billable'=>array('access'=>"Y","edit"=>"Y"),
  'office_phone'=>array('access'=>"Y","edit"=>"Y"),
  'office_ext'=>array('access'=>"Y","edit"=>"Y"))),
"personal_details"=>array("access"=>"Y","fields"=>array('date_of_birth'=>array('access'=>"Y","edit"=>"Y"),'age'=>array('access'=>"Y","edit"=>"Y"),'marital_status'=>array('access'=>"Y","edit"=>"Y"),'wedding_date'=>array('access'=>"Y","edit"=>"Y"),'gender'=>array('access'=>"Y","edit"=>"Y"),'blood_group'=>array('access'=>"Y","edit"=>"Y"),'disability'=>array('access'=>"Y","edit"=>"Y"),'disability_percentage'=>array('access'=>"Y","edit"=>"Y"),'work_textile_industry'=>array('access'=>"Y","edit"=>"Y"),'physically_challenged_children'=>array('access'=>"Y","edit"=>"Y"),'soc_facebook'=>array('access'=>"Y","edit"=>"Y"),'soc_twitter'=>array('access'=>"Y","edit"=>"Y"),'soc_linkedin'=>array('access'=>"Y","edit"=>"Y"))),
"employee_buddies"=>array("access"=>"Y","fields"=>array('role_id'=>array('access'=>"Y","edit"=>"Y"),'reporting_manager_id'=>array('access'=>"Y","edit"=>"Y"),'hr_buddy'=>array('access'=>"Y","edit"=>"Y"),
'finance_buddy'=>array('access'=>"Y","edit"=>"Y"),
'admin_buddy'=>array('access'=>"Y","edit"=>"Y"),
'project_buddy'=>array('access'=>"Y","edit"=>"Y"),
'loc_buddy'=>array('access'=>"Y","edit"=>"Y"))),

"proof_details"=>array("access"=>"Y","fields"=>array('adhar_no'=>array('access'=>"Y","edit"=>"Y"),'passport'=>array('access'=>"Y","edit"=>"Y"),
'driving_licence'=>array('access'=>"Y","edit"=>"Y"),
'pan_number'=>array('access'=>"Y","edit"=>"Y"),
'pf_no'=>array('access'=>"Y","edit"=>"Y"),
'pf_uan'=>array('access'=>"Y","edit"=>"Y"),
'gosi_number'=>array('access'=>"Y","edit"=>"Y"))),

"experience_details"=>array("access"=>"Y","fields"=>array('employee_experiances'=>array('access'=>"Y","edit"=>"Y"))),
"employee_qualifications"=>array("access"=>"Y","fields"=>array('employee_qualifications'=>array('access'=>"Y","edit"=>"Y"))),
"employee_dependents"=>array("access"=>"Y","fields"=>array('employee_dependents'=>array('access'=>"Y","edit"=>"Y"))),
"employee_contacts"=>array("access"=>"Y","fields"=>array('employee_contacts'=>array('access'=>"Y","edit"=>"Y"))),
"emp_visa"=>array("access"=>"Y","fields"=>array('emp_visa'=>array('access'=>"Y","edit"=>"Y"))),
"emp_skills"=>array("access"=>"Y","fields"=>array('emp_skills'=>array('access'=>"Y","edit"=>"Y"))),
"employee_access"=>array(
  "access"=>"Y",
    "fields"=>array(
      'cover_under_pf'=>array("access"=>"Y","edit"=>"N"),
      'cover_under_esi'=>array('access'=>"Y","edit"=>"N"),
      'clock_event_type'=>array('access'=>"Y","edit"=>"N"),
      'b_machine_id'=>array('access'=>"Y","edit"=>"N")
    )
  )

);
if($bank_work_flow == 'Y'){
    $accessrightarry["bank_details"] = array(
        "access" => "Y",
        "fields" => array(
            'bank_details' => array('access' => "Y", 'edit' => "Y")
        )
    );
}else{
    $accessrightarry["bank_details"] = array(
        "access" => "Y",
        "fields" => array(
            'bank_id' => array('access' => "Y", 'edit' => "Y"),
            'bank_link' => array('access' => "Y", 'edit' => "Y"),
            'bank_account_no' => array('access' => "Y", 'edit' => "Y")
        )
    );
}

 //`job_title_id`, `employment_category_id`,  `seat_location`, `father_name`, `spouse_name`, `date_of_leaving`, `rejoin_ref_id`,  `contact_end`, `image`, `user_id`, `vendor_id`, `cost_center_id`, `attendance_type`, `payment_mode`, `manuval_tds`, `cover_under_pf`, `change_pf_date`, `pran_no`, `esi_no`, `cover_under_esi`, `change_esi_date`, `nationality`, , ``, ``, ``, `work_textile_industry`, `religion`, `old_ref_no`, `alternate_number`, `other_mail`, `skill_set`,`about_me`, `expertise`, `first_working_date`, `on_off_site`, `salary_type`, `work_from_home`, `race_id`, `veteran_id`, `career_start_date`, `division_id`, `section_id`, `job_grade_id`

$geturl = API."?mode=getemployeesdisplayfields&Role=".$_GET['ROLE'];
$getapi = callAPI($geturl);
$getresult = json_decode($getapi,1);
$application_roles = $getresult['application_roles'];
$employees_display_fields = $getresult['employees_display_fields'];
$candidateaccess = json_decode($employees_display_fields['field_list'],1);
$Rid = $employees_display_fields['id'];
?>

<script type="text/javascript">
  function accessDashSetting(fieldgrpname,seq,type){
    if(type == "access"){
      $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-unlock'></i> Access");
      $("#dash-hidden-"+fieldgrpname+"-"+seq).val('Y');

       }else{
        $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> No Access");
        $("#dash-hidden-"+fieldgrpname+"-"+seq).val('N');
        }
        $(".dash-"+fieldgrpname+"-"+seq).hide();
        $("#dash-"+fieldgrpname+"-"+seq+"-"+type).show("slow");
      }

  function accessFormSetting(fieldgrpname,seq,type){
    console.log(type);
    $(".empty-"+fieldgrpname).removeClass("text-warning");
    $(".allempty-"+fieldgrpname).removeClass("text-warning");
    $("#fieldgrpaccess-hidden-"+fieldgrpname).val(type);
    $(".fieldgrpaccess-hidden-"+fieldgrpname).val(type);
    if(type == "NA"){
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> No Access");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> No Access");

      $("#field_noaccess-"+fieldgrpname).addClass("text-warning");

    }else if(type == "E"){
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> Edit");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> Edit");
      $("#field_edit-"+fieldgrpname).addClass("text-warning");
    }else{
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> View Only");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> View Only");
      $("#field_viewonly-"+fieldgrpname).addClass("text-warning");
    }
  }
  function accessFormfieldSetting(fieldname,fieldgrpname,seq,type,value){
    $(".emptyfield-"+fieldname+"-"+seq).removeClass("text-warning");
    $(".empty-"+fieldgrpname).removeClass("text-warning");
    $(".form-"+fieldgrpname+"-label").html("Access Varies");
    $("#fieldaccess-hidden-"+fieldname+"-"+seq).val(type);

    $("#fieldgrpaccess-hidden-"+fieldgrpname).val(0);
    if(type == "NA"){
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> No Access");
      $("#field_noaccess-"+fieldname+"-"+seq).addClass("text-warning");
    }else if(type == "E"){
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-pencil'></i> Edit");
      $("#field_edit-"+fieldname+"-"+seq).addClass("text-warning");
    }else{
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-eye'></i> View Only");
      $("#field_viewonly-"+fieldname+"-"+seq).addClass("text-warning");
    }

  }
  function selecteRole(value){
    window.location.href = "index.php?option=COM_INSERT_EMPLOYEES_DISPLAY_FIELDS&ROLE="+value;
  }

</script>

<div class="card" >
    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
      <strong><?php echo $error_message; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php } ?>
  <div class="card-header" id="custom-popup">
    <h5> Access Rights </h5>
  </div>

  <div class="card-body">
     <form method="post">
      <div class="row">
        <div class="form-group col-lg-4">
          <span class="form-label">Role</span>
          <select aria-label="Role Short Code" class="form-control js-example-basic-single" name="role_short_code" onchange="selecteRole(this.value)" required>
            <option value="">Select Any One</option>
            <?php
            $application_roles[] = array('role_short_code'=>ADMIN_ROLES,'name'=>'Admin');
            $getroleid = $employees_display_fields['role_short_code'] !=""?$employees_display_fields['role_short_code']:$_GET['ROLE'];
            foreach($application_roles as $applicationroles){?>
              <option value="<?php echo $applicationroles['role_short_code'];?>" <?php echo $getroleid == $applicationroles['role_short_code']?"selected":""; ?>><?php echo $applicationroles['name'];?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    <?php if($_GET['ROLE'] !=""){ ?>
      <ul class="nav nav-tabs md-tabs infisuite_tab">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" href="#candidate_form" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="icofont icofont-ui-user "></i> Employee Form</a>
        </li>

      </ul>

      <div class="tab-content card-block">
        <div class="tab-pane active" id="candidate_form" role="tabpanel">

         <div class="row">
          <?php $mj=1; foreach($accessrightarry as $headingkey=>$profilereadinesval){
            $divcss = $profilereadinesval['access'] == "N"?"bg-danger":"";
            $candfieldgrpaccess = $candidateaccess[$headingkey]['access'] != ""?$candidateaccess[$headingkey]['access']:"0";
            $candfieldgrpaccesslabel ="Access Varies";
            if($candfieldgrpaccess == "NA"){
              $candfieldgrpaccesslabel ="<i class='ti-lock text-danger'></i> No Access";
            }elseif($candfieldgrpaccess == "E"){
              $candfieldgrpaccesslabel ="<i class='ti-pencil'></i> Edit";
            }elseif($candfieldgrpaccess == "V"){
              $candfieldgrpaccesslabel ="<i class='ti-eye'></i> View Only";
            }
            ?>
            <div class="col-lg-4">
             <div class="card <?php echo $divcss; ?> form-<?php echo $headingkey."-".$mj."-div"; ?>">
              <div id="">
               <div class="card-header" id="custom-popup_2">
                <div class="row">
                  <div class="col-lg-5">
                   <h5>
                    <?php echo ucwords(str_replace("_"," ",$headingkey)); ?></h5>
                 </div>
                 <div class="col-lg-4">
                   <span class="form-<?php echo $headingkey."-label"; ?>"><?php echo $candfieldgrpaccesslabel;?></span>
                 </div>
                 <div class="col-lg-3 text-right <?php echo $headingkey == 'basic_information'?"custom_readonly":""; ?>">
                  <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][access]" id="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                  <i class="ti-lock empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'NA'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access" onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','NA')"  id="field_noaccess-<?php echo $headingkey; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                  <i class="ti-pencil ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'E'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit"  onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','E')" id="field_edit-<?php echo $headingkey; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                  <i class="ti-eye ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'V'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Only"  onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','V')" id="field_viewonly-<?php echo $headingkey; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                </div>
              </div>
            </div>
            <div class="card-body">
             <table class="table table-hover table-md <?php echo $divcss; ?> form-<?php echo $headingkey."-".$mj."-div"; ?>">
              <tbody>
               <?php $mm=1; foreach($profilereadinesval['fields'] as $profilereadines=>$accesval){
                $candfieldgrpaccess = $candidateaccess[$headingkey]['field'][$profilereadines] != ""?$candidateaccess[$headingkey]['field'][$profilereadines]:"E";
                if($candfieldgrpaccess == "NA"){
                  $candfieldgrpaccesslabel ="<i class='ti-lock text-danger'></i> No Access";
                }elseif($candfieldgrpaccess == "V"){
                  $candfieldgrpaccesslabel ="<i class='ti-eye'></i> View Only";
                }else{
                  $candfieldgrpaccesslabel ="<i class='ti-pencil'></i> Edit";
                }
                ?>
                <tr class="field-<?php echo $profilereadines."-".$mm."-div"; ?>">
                  <th scope="col" class="w-b-w-s">
                    <h6 class="mb-0 f-14">
                      <?php

                      $lable = ucwords(str_replace("_"," ",$profilereadines));
                    if(isset($accesval['name']) && $accesval['name']!=""){
                       $lable = ucwords($accesval['name']);
                    }
                    echo $lable;  ?>

                    </h6>
                  </th>
                  <th scope="col" class="allformfield-<?php echo $headingkey."-label"; ?>" id="formfield-<?php echo $profilereadines."-".$mm."-label"; ?>" ><?php echo $candfieldgrpaccesslabel; ?>
                </th>

                <th scope="col" class="text-right">
                  <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][field][<?php echo $profilereadines; ?>]" class="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" id="fieldaccess-hidden-<?php echo $profilereadines."-".$mm; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                  <i class="ti-lock allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "NA"?"text-warning":""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access" onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','NA')"  id="field_noaccess-<?php echo $profilereadines."-".$mm; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                  <i class="ti-pencil ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "E"?"text-warning":""?> <?php echo in_array($profilereadines,$noteditable)?"custom_readonly":""; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit"  onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','E')"  id="field_edit-<?php echo $profilereadines."-".$mm; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                  <i class="ti-eye ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "V"?"text-warning":""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Only"  onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','V')" <?php echo $candfieldgrpaccess == "V"?"text-warning":""?> id="field_viewonly-<?php echo $profilereadines."-".$mm; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                </th>
              </tr>
              <?php $mm++;
} ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $mj++;
} ?>
</div>
</div>
  <div class="tab-pane" id="dashboard" role="tabpanel">
    <div class="row">
      <?php $mk=1; foreach($dashboardarray as $dashboardcoulnmname=>$fieldval){
        $dashboardaccess = $dashboard_field_list[$dashboardcoulnmname]['access']!= ""?$dashboard_field_list[$dashboardcoulnmname]['access']:"Y";
        ?>
        <div class="col-lg-4">
          <div class="card social-res-card " id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-div">
            <div class="row p-15">
              <div class="col-lg-7">
                <h6><?php echo ucwords(str_replace("_"," ",$dashboardcoulnmname)); ?></h6>
              </div>
              <div class="col-lg-4">
                <span id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-label">
                  <?php echo $dashboardaccess == "Y"?"<i class='ti-unlock'></i> Access":"<i class='ti-lock  text-danger'></i> No Access"?>
                </span>
              </div>
              <div class="col-lg-1 text-right">
                <input type="hidden" name="dashboard_field_list[<?php echo $dashboardcoulnmname;?>][access]" id="dash-hidden-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>"  value="<?php echo $dashboardaccess;?>">

                <i class="ti-lock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access" onclick="accessDashSetting('<?php echo $dashboardcoulnmname; ?>','<?php echo $mk; ?>','noaccess')" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-access" style="<?php echo $dashboardaccess == "N"?"display:none":""?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                <i class="ti-unlock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Access" onclick="accessDashSetting('<?php echo $dashboardcoulnmname; ?>','<?php echo $mk; ?>','access')" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-noaccess" style="<?php echo $dashboardaccess == "Y"?"display:none":""?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
              </div>
            </div>
          </div>
        </div>
      <?php $mk++;
}  ?>
    </div>
  </div>
</div>
<div class="card-footer pt-0">
  <input type="hidden" name="Rid" value="<?php echo $Rid; ?>">
  <button class="input-group-text addon-style cursor-pointer" name="submit_access_right" >Submit</button>
</div>
<?php }else{ ?>
          <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> Please Select Role </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
<?php } ?>
</form>

</div>

