<?php
defined('KEY_ERROR_NO') || define('KEY_ERROR_NO', KEY_ERROR_NO);
defined('LBL_NO_ACCESS') || define('LBL_NO_ACCESS', LBL_NO_ACCESS);
defined('LBL_VIEW_ONLY') || define('LBL_VIEW_ONLY', LBL_VIEW_ONLY);
defined('KEY_ROLE_SHORT_CODE') || define('KEY_ROLE_SHORT_CODE', KEY_ROLE_SHORT_CODE);
defined('CLS_TEXT_WARNING') || define('CLS_TEXT_WARNING', 'text-warning');
$rid =  encodeId($_GET['Rid']);
$cand = explode(':', $rid);
$Rid = $cand['1'];
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_access_right'])){
  $_POST['company_id']=$_SESSION['company_id'];
  $_POST['user_id']=$_SESSION['userid'];
  $url = API."?mode=insertcandidateaccessright&method=post";
  $response2 = file_post_contents($url, $_POST, 'insert');
  $response1 = json_decode($response2,1);
    $response = $response1['result'];
   if($response['status'] == 'fail'){
      if(isset($_SESSION['error'][$response[KEY_ERROR_NO]])){
        $dis_err = explode(",", $response['other']);
        foreach ($dis_err as $va) {
          $dis_er [] = $dis_labels[$va];
        }
        $error_message = $response[KEY_ERROR_NO] ." : ".$_SESSION['error'][$response[KEY_ERROR_NO]]." : ".implode(", ", (array)$dis_er);
      }else{
        $error_message = $response['message'];
      }
    }else{
?>
<script type="text/javascript">
document.onreadystatechange = function () {
  if (document.readyState == "complete") {
   tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');

  }
}
</script>
  <?php
  header("location:index.php?option=COM_LIST_CANDIDATE_DISPLAY_FIELDS");
    }
}

$accessrightarry = array("contact_details"=>array("access"=>"Y","fields"=>array('first_name'=>array("access"=>"Y","edit"=>"N"),'middle_name'=>array('access'=>"Y","edit"=>"Y"),'last_name'=>array('access'=>"Y","edit"=>"Y"),'display_name'=>array('access'=>"Y","edit"=>"N"),'phone'=>array('access'=>"N","edit"=>"N"),'email_id'=>array('access'=>"Y","edit"=>"Y"),'address'=>array('access'=>"Y","edit"=>"N"),'post_code'=>array('access'=>"Y","edit"=>"Y"),'bio'=>array('access'=>"Y","edit"=>"N"))),"personal_details"=>array("access"=>"Y","fields"=>array('gender'=>array('access'=>"Y","edit"=>"Y"),'date_of_birth'=>array('access'=>"Y","edit"=>"Y"),'marital_status'=>array('access'=>"Y","edit"=>"Y"),'wedding_date'=>array('access'=>"Y","edit"=>"Y"),'blood_group'=>array('access'=>"Y","edit"=>"Y"),'father_name'=>array('access'=>"Y","edit"=>"Y"),'spouse_name'=>array('access'=>"Y","edit"=>"Y"), 'religion'=>array('access'=>"Y","edit"=>"Y"),'caste'=>array('access'=>"Y","edit"=>"Y"), 'present_house'=>array('access'=>"Y","edit"=>"Y"),'identification_mark'=>array('access'=>"Y","edit"=>"Y"),'hobbies'=>array('access'=>"Y","edit"=>"Y"),'present_address'=>array('access'=>"Y","edit"=>"Y"),'height'=>array('access'=>"Y","edit"=>"Y"), 'weight'=>array('access'=>"Y","edit"=>"Y"),'state_id'=>array('access'=>"Y","edit"=>"Y"), 'adhar_no'=>array('access'=>"Y","edit"=>"Y"),'pan_number'=>array('access'=>"Y","edit"=>"Y"),'hiring_source'=>array('access'=>"Y","edit"=>"Y"))),"dependents"=>array("access"=>"Y","fields"=>array('employee_dependents'=>array('access'=>"Y","edit"=>"N"))),"current_employment"=>array("access"=>"Y","fields"=>array('employee_contacts'=>array('access'=>"Y","edit"=>"Y"),'current_location'=>array('access'=>"Y","edit"=>"Y"),'reason_for_change'=>array('access'=>"Y","edit"=>"Y"),'relocate'=>array('access'=>"Y","edit"=>"Y"),'current_ctc'=>array('access'=>"Y","edit"=>"Y"),'expected_ctc'=>array('access'=>"Y","edit"=>"Y"),'no_of_reportees'=>array('access'=>"Y","edit"=>"Y"),'job_grade'=>array('access'=>"Y","edit"=>"Y"),'pay_template'=>array('access'=>"Y","edit"=>"Y"),'job_type'=>array('access'=>"Y","edit"=>"Y"),'location_id'=>array('access'=>"Y","edit"=>"Y"))),"awards_and_accolades"=>array("access"=>"Y","fields"=>array('candidate_awards'=>array('access'=>"Y","edit"=>"Y"))),"hr_details"=>array("access"=>"Y","fields"=>array('first_role'=>array('access'=>"Y","edit"=>"Y"),'candidate_current_role'=>array('access'=>"Y","edit"=>"Y"),'notice_period'=>array('access'=>"Y","edit"=>"Y"),'np_negotiatable'=>array('access'=>"Y","edit"=>"Y"),'first_pay'=>array('access'=>"Y","edit"=>"Y"),'current_pay'=>array('access'=>"Y","edit"=>"Y"),'gaps_in_employment_reason'=>array('access'=>"Y","edit"=>"Y"),'performance_ratings'=>array('access'=>"Y","edit"=>"Y"))),"skills"=>array("access"=>"Y","fields"=>array('skills'=>array('access'=>"Y","edit"=>"Y"))),"work_experiences"=>array("access"=>"Y","fields"=>array('candidate_experiances'=>array('access'=>"Y","edit"=>"Y"))),"education_details"=>array("access"=>"Y","fields"=>array('candidate_qualifications'=>array('access'=>"Y","edit"=>"Y"))));

$dashboardarray = array("job_posting"=>array("access"=>"Y"),"holidays"=>array("access"=>"N"),"job_postings_monthly"=>array("access"=>"Y"),"job_posting_candidates_list"=>array("access"=>"N"),"quarterly_chart"=>array("access"=>"Y"),"job_posting_activity"=>array("access"=>"N"));

$geturl = API."?mode=getcandidateaccessright&Rid=".$Rid."&Role=".$_GET['ROLE'];
$getapi = callAPI($geturl);
$getresult = json_decode($getapi,1);
$application_roles = $getresult['application_roles'];
$candidate_display_fields = $getresult['candidate_display_fields'];

$candidateaccess = json_decode($candidate_display_fields['field_list'],1);
$dashboard_field_list = json_decode($candidate_display_fields['dashboard_field_list'],1);
$Rid = $candidate_display_fields['id'];

?>

<script type="text/javascript">
  function accessDashSetting(fieldgrpname,seq,type){
      if(type == "access"){
          $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-unlock'></i> <?= __('Access') ?>");
          $("#dash-hidden-"+fieldgrpname+"-"+seq).val('Y');

      }else{
          $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> <?= __(LBL_NO_ACCESS) ?>");
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
        $(".form-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> <?= __(LBL_NO_ACCESS) ?>");
        $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> <?= __(LBL_NO_ACCESS) ?>");

        $("#field_noaccess-"+fieldgrpname).addClass("text-warning");

      }else if(type == "E"){
        $(".form-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> <?= __('Edit') ?>");
        $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> <?= __('Edit') ?>");
        $("#field_edit-"+fieldgrpname).addClass("text-warning");
      }else{
        $(".form-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> <?= __(LBL_VIEW_ONLY) ?>");
        $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> <?= __(LBL_VIEW_ONLY) ?>");
        $("#field_viewonly-"+fieldgrpname).addClass("text-warning");
      }
  }
  function accessFormfieldSetting(fieldname,fieldgrpname,seq,type,value){
      $(".emptyfield-"+fieldname+"-"+seq).removeClass("text-warning");
      $(".empty-"+fieldgrpname).removeClass("text-warning");
      $(".form-"+fieldgrpname+"-label").html("<?= __('Access Varies') ?>");
      $("#fieldaccess-hidden-"+fieldname+"-"+seq).val(type);

       $("#fieldgrpaccess-hidden-"+fieldgrpname).val(0);
      if(type == "NA"){
        $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> <?= __(LBL_NO_ACCESS) ?>");
        $("#field_noaccess-"+fieldname+"-"+seq).addClass("text-warning");
      }else if(type == "E"){
        $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-pencil'></i> <?= __('Edit') ?>");
        $("#field_edit-"+fieldname+"-"+seq).addClass("text-warning");
      }else{
        $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-eye'></i> <?= __(LBL_VIEW_ONLY) ?>");
        $("#field_viewonly-"+fieldname+"-"+seq).addClass("text-warning");
      }

  }
  function selecteRole(value){
    window.location.href = "index.php?option=COM_INSERT_CANDIDATE_DISPLAY_FIELDS&ROLE="+value;
  }

</script>

<div class="card" >
    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
<?php } ?>
        <div class="card-header" id="custom-popup">
        <h5> <?= __('Access Rights') ?> </h5>
    </div>

    <div class="card-body">
    <form method="post">
    <div class="row">
      <div class="form-group col-lg-4">
        <span class="form-label"><?= __('Role') ?></span>
        <select aria-label="<?= htmlspecialchars(__('Role Short Code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single" name="role_short_code" onchange="selecteRole(this.value)" required>
          <option value=""><?= __('Select Any One') ?></option>
          <?php
          $getroleid = $candidate_display_fields[KEY_ROLE_SHORT_CODE] !=""?$candidate_display_fields[KEY_ROLE_SHORT_CODE]:$_GET['ROLE'];
          foreach($application_roles as $applicationroles){?>
            <option value="<?php echo $applicationroles[KEY_ROLE_SHORT_CODE];?>" <?php echo $getroleid == $applicationroles[KEY_ROLE_SHORT_CODE]?"selected":""; ?>><?php echo $applicationroles['name'];?></option>
          <?php } ?>
        </select>
      </div>
    </div>
      <ul class="nav nav-tabs md-tabs infisuite_tab">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#candidate_form" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="icofont icofont-ui-user "></i> <?= __('Candidate Form'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#dashboard" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="icofont icofont-home"></i> <?= __('Dashboard'); ?></a>
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
                  $candfieldgrpaccesslabel ="<i class='ti-lock text-danger'></i> ".__(LBL_NO_ACCESS)."";
              }elseif($candfieldgrpaccess == "E"){
                  $candfieldgrpaccesslabel ="<i class='ti-pencil'></i> ".__('Edit')."";
              }elseif($candfieldgrpaccess == "V"){
                  $candfieldgrpaccesslabel ="<i class='ti-eye'></i> ".__(LBL_VIEW_ONLY)."";
              }
          ?>
                <div class="col-lg-4">
                    <div class="card <?php echo $divcss; ?> form-<?php echo $headingkey."-".$mj."-div"; ?>">
                          <div id="">
                                <div class="card-header" id="custom-popup_2">
                        <div class="row">
                          <div class="col-lg-5">
                                  <h5> <?php echo htmlspecialchars(__(ucwords(str_replace("_"," ",$headingkey)))); ?></h5>
                      </div>
                      <div class="col-lg-4">
                         <span class="form-<?php echo $headingkey."-label"; ?>"><?php echo $candfieldgrpaccesslabel;?></span>
                      </div>
                      <div class="col-lg-3 text-right">
                          <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][access]" id="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                          <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','NA')" aria-label="<?= __(LBL_NO_ACCESS) ?>"><i class="ti-lock empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'NA'?CLS_TEXT_WARNING:"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __(LBL_NO_ACCESS) ?>" id="field_noaccess-<?php echo $headingkey; ?>"></i></button>

                          <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','E')" aria-label="<?= __('Edit') ?>"><i class="ti-pencil ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'E'?CLS_TEXT_WARNING:"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __('Edit') ?>" id="field_edit-<?php echo $headingkey; ?>"></i></button>

                          <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormSetting('<?php echo $headingkey; ?>','<?php echo $mj; ?>','V')" aria-label="<?= __(LBL_VIEW_ONLY) ?>"><i class="ti-eye ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'V'?CLS_TEXT_WARNING:"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __(LBL_VIEW_ONLY) ?>" id="field_viewonly-<?php echo $headingkey; ?>"></i></button>

                                </div>
                    </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover table-md <?php echo $divcss; ?> form-<?php echo $headingkey."-".$mj."-div"; ?>">
                                    <tbody>
                                        <?php $mm=1; foreach($profilereadinesval['fields'] as $profilereadines=>$accesval){
                                $candfieldgrpaccess = $candidateaccess[$headingkey]['field'][$profilereadines] != ""?$candidateaccess[$headingkey]['field'][$profilereadines]:"E";
                                  if($candfieldgrpaccess == "NA"){
                                      $candfieldgrpaccesslabel ="<i class='ti-lock text-danger'></i> ".__(LBL_NO_ACCESS)."";
                                  }elseif($candfieldgrpaccess == "V"){
                                      $candfieldgrpaccesslabel ="<i class='ti-eye'></i> ".__(LBL_VIEW_ONLY)."";
                                  }else{
                                      $candfieldgrpaccesslabel ="<i class='ti-pencil'></i> ".__('Edit')."";
                                  }
                                ?>
                                    <tr class="field-<?php echo $profilereadines."-".$mm."-div"; ?>">
                                          <th scope="col">
                                            <h6 class="mb-0 f-14"><?php echo ucwords(str_replace("_"," ",$profilereadines)); ?></h6>
                                          </th>
                                  <th scope="col" class="allformfield-<?php echo $headingkey."-label"; ?>" id="formfield-<?php echo $profilereadines."-".$mm."-label"; ?>" ><?php echo htmlspecialchars(__($candfieldgrpaccesslabel ?? '')); ?>
                                  </th>
                                          <th scope="col" class="text-right">
                                  <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][field][<?php echo $profilereadines; ?>]" class="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" id="fieldaccess-hidden-<?php echo $profilereadines."-".$mm; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                                  <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','NA')" aria-label="<?= __(LBL_NO_ACCESS) ?>"><i class="ti-lock allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "NA"?CLS_TEXT_WARNING:""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __(LBL_NO_ACCESS) ?>" id="field_noaccess-<?php echo $profilereadines."-".$mm; ?>"></i></button>

                                  <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','E')" aria-label="<?= __('Edit') ?>"><i class="ti-pencil ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "E"?CLS_TEXT_WARNING:""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __('Edit') ?>" id="field_edit-<?php echo $profilereadines."-".$mm; ?>"></i></button>

                                  <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessFormfieldSetting('<?php echo $profilereadines; ?>','<?php echo $headingkey; ?>','<?php echo $mm; ?>','V')" aria-label="<?= __(LBL_VIEW_ONLY) ?>"><i class="ti-eye ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "V"?CLS_TEXT_WARNING:""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __(LBL_VIEW_ONLY) ?>" <?php echo $candfieldgrpaccess == "V"?CLS_TEXT_WARNING:""?> id="field_viewonly-<?php echo $profilereadines."-".$mm; ?>"></i></button>

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
                          <?php echo $dashboardaccess == "Y"?"<i class='ti-unlock'></i> ".__('Access')."":"<i class='ti-lock  text-danger'></i> ".__(LBL_NO_ACCESS).""?>
                      </span>
                    </div>
                       <div class="col-lg-1 text-right">
                      <input type="hidden" name="dashboard_field_list[<?php echo $dashboardcoulnmname;?>][access]" id="dash-hidden-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>"  value="<?php echo $dashboardaccess;?>">

                               <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessDashSetting('<?php echo $dashboardcoulnmname; ?>','<?php echo $mk; ?>','noaccess')" aria-label="<?= __(LBL_NO_ACCESS) ?>"><i class="ti-lock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __(LBL_NO_ACCESS) ?>" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-access" style="<?php echo $dashboardaccess == "N"?"display:none":""?>"></i></button>

                              <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="accessDashSetting('<?php echo $dashboardcoulnmname; ?>','<?php echo $mk; ?>','access')" aria-label="<?= __('Access') ?>"><i class="ti-unlock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= __('Access') ?>" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-noaccess" style="<?php echo $dashboardaccess == "Y"?"display:none":""?>"></i></button>
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
        <button class="input-group-text addon-style cursor-pointer" name="submit_access_right" ><?= __('Submit') ?></button>
    </div>
  </form>
</div>
