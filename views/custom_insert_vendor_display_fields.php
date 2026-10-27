<?php
$rid =  encodeId($_GET['Rid']);
$cand = explode(':', $rid);
$Rid = $cand['1'];
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_access_right'])){
  $_POST['company_id']=$_SESSION['company_id'];
  $_POST['user_id']=$_SESSION['userid'];
  $url = API."?mode=insertvendordisplayfields&method=post";
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
         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');

       }
     }
   </script>
   <?php
  }
}

$noteditable = array('company','email');
$accessrightarry = array("business_information"=>array("access"=>"Y","fields"=>array('company'=>array("access"=>"Y","edit"=>"N"),'email'=>array("access"=>"Y","edit"=>"N"),'website_url'=>array("access"=>"Y","edit"=>"N"),
  'company_trading_name'=>array('access'=>"Y","edit"=>"Y"),
  'company_subsidiary_division'=>array('access'=>"Y","edit"=>"Y"),
  'first_name'=>array('access'=>"Y","edit"=>"N"),
  'middle_name'=>array('access'=>"N","edit"=>"N"),
  'last_name'=>array('access'=>"Y","edit"=>"Y"),
  'phone'=>array('access'=>"Y","edit"=>"N"),
  'billing_address'=>array('access'=>"Y","edit"=>"N"),
  'billing_country'=>array('access'=>"Y","edit"=>"N"),
  'billing_state'=>array('access'=>"Y","edit"=>"N"),
  'billing_city'=>array('access'=>"Y","edit"=>"N"),
  'billing_post_code'=>array('access'=>"Y","edit"=>"N"),
  'contact_person'=>array('access'=>"Y","edit"=>"N"),
  'mobile'=>array('access'=>"Y","edit"=>"N"),
  'shipping_address'=>array('access'=>"Y","edit"=>"N"),
  'shipping_country'=>array('access'=>"Y","edit"=>"N"),
  'shipping_state'=>array('access'=>"Y","edit"=>"N"),
  'shipping_city'=>array('access'=>"Y","edit"=>"N"),
  'shipping_post_code'=>array('access'=>"Y","edit"=>"N"),
  'vendor_tax_reg_no'=>array('access'=>"Y","edit"=>"N"),
  'business_type'=>array('access'=>"Y","edit"=>"N"),
  'vendor_type_id'=>array('access'=>"Y","edit"=>"N"),
  'service_types'=>array('access'=>"Y","edit"=>"N"))),"company_information"=>array("access"=>"Y","fields"=>array('vendor_tax_reg_no'=>array('access'=>"Y","edit"=>"Y"),
  'business_type'=>array('access'=>"Y","edit"=>"Y"),
  'duns_no'=>array('access'=>"Y","edit"=>"Y"),
  'company_registration_number'=>array('access'=>"Y","edit"=>"Y"),
  'company_registration_country'=>array('access'=>"Y","edit"=>"Y"),
  'payment_terms'=>array('access'=>"Y","edit"=>"Y"),
  'gstin'=>array('access'=>"Y","edit"=>"Y"),
  'pan_no'=>array('access'=>"Y","edit"=>"Y"),
  'tan_number'=>array('access'=>"Y","edit"=>"Y"),
  'msme_number'=>array('access'=>"Y","edit"=>"Y"),
  'incoterm_id'=>array('access'=>"Y","edit"=>"Y"),
  'image'=>array('access'=>"Y","edit"=>"Y"))),"bank_information"=>array("access"=>"Y","fields"=>array('bank_name'=>array('access'=>"Y","edit"=>"Y"),
  'bank_address'=>array('access'=>"Y","edit"=>"Y"),
  'bank_country'=>array('access'=>"Y","edit"=>"Y"),
  'bank_state'=>array('access'=>"Y","edit"=>"Y"),
  'bank_city'=>array('access'=>"Y","edit"=>"Y"),
  'bank_post_code'=>array('access'=>"Y","edit"=>"Y"),
  'bank_beneficiary_name'=>array('access'=>"Y","edit"=>"Y"),
  'bank_account_no'=>array('access'=>"Y","edit"=>"Y"),
  'account_type'=>array('access'=>"Y","edit"=>"Y"),
  'ifsc_code'=>array('access'=>"Y","edit"=>"Y"),
  'routing_aba_dfi_ach'=>array('access'=>"Y","edit"=>"Y"),
  'routing_wire_Code'=>array('access'=>"Y","edit"=>"Y"),
  'swift_bic_code'=>array('access'=>"Y","edit"=>"Y"),
  'iban_code'=>array('access'=>"Y","edit"=>"Y"),
  'bank_query_person_name'=>array('access'=>"Y","edit"=>"Y"),
  'bank_query_contact_no'=>array('access'=>"Y","edit"=>"Y"),
  'bank_query_email_id'=>array('access'=>"Y","edit"=>"Y"))),"other_information"=>array("access"=>"Y","fields"=>array('vendor_type'=>array('access'=>"Y","edit"=>"Y"),
  'title'=>array('access'=>"Y","edit"=>"Y"),
  'suffix'=>array('access'=>"Y","edit"=>"Y"),
  'gsttype'=>array('access'=>"Y","edit"=>"Y"),
  'parent_vendor'=>array('access'=>"Y","edit"=>"Y"),
  'cst_reg_no'=>array('access'=>"Y","edit"=>"Y"),
  'tds_apply'=>array('access'=>"Y","edit"=>"Y"),
  'pref_payment_method'=>array('access'=>"Y","edit"=>"Y"),
  'preferred_deliver_method'=>array('access'=>"Y","edit"=>"Y"),
  'opening_balance'=>array('access'=>"Y","edit"=>"Y"),
  'asof'=>array('access'=>"Y","edit"=>"Y"),
  'pay_with_me'=>array('access'=>"Y","edit"=>"Y"),
  'credit_limit'=>array('access'=>"Y","edit"=>"Y"),
  'default_discount_perc'=>array('access'=>"Y","edit"=>"Y"),
  'payment_discount_perc'=>array('access'=>"Y","edit"=>"Y"),
  'sales_type'=>array('access'=>"Y","edit"=>"Y"))),"other_contact_information"=>array("access"=>"Y","fields"=>array('other_contact_information'=>array('access'=>"Y","edit"=>"Y"))),"note"=>array("access"=>"Y","fields"=>array('vendor_po_notes'=>array('access'=>"Y","edit"=>"Y"),'vendor_other_notes'=>array('access'=>"Y","edit"=>"Y"))),"questions"=>array("access"=>"Y","fields"=>array('questions'=>array('access'=>"Y","edit"=>"Y"))));

$geturl = API."?mode=getvendordisplayfields&Role=".$_GET['ROLE'];
$getapi = callAPI($geturl);
$getresult = json_decode($getapi,1);
$application_roles = $getresult['application_roles'];
$application_roles[] = array('name'=>'Supper Admin','role_short_code'=>'ADMIN');
$vendor_display_fields = $getresult['vendor_display_fields'];
$candidateaccess = json_decode($vendor_display_fields['field_list'],1);
$dashboard_field_list = json_decode($vendor_display_fields['dashboard_field_list'],1);
$Rid = $vendor_display_fields['id'];
?>
<script type="text/javascript" src="../assets/js/custom_insert_vendor_display_fields.js"></script>

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
          <select aria-label="Role Short Code" class="form-control js-example-basic-single selecteRole" name="role_short_code"  required>
            <option value="">Select Any One</option>
            <?php
            $getroleid = $vendor_display_fields['role_short_code'] !=""?$vendor_display_fields['role_short_code']:$_GET['ROLE'];
            foreach($application_roles as $applicationroles){?>
              <option value="<?php echo $applicationroles['role_short_code'];?>" <?php echo $getroleid == $applicationroles['role_short_code']?"selected":""; ?>><?php echo $applicationroles['name'];?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    <?php if($_GET['ROLE'] !=""){ ?>
      <ul class="nav nav-tabs md-tabs infisuite_tab">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" href="#candidate_form" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="icofont icofont-ui-user "></i> Vendor Form</a>
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
                   <h5> <?php echo ucwords(str_replace("_"," ",$headingkey)); ?></h5>
                 </div>
                 <div class="col-lg-4">
                   <span class="form-<?php echo $headingkey."-label"; ?>"><?php echo $candfieldgrpaccesslabel;?></span>
                 </div>
                 <div class="col-lg-3 text-right <?php echo $headingkey == 'business_information'?"custom_readonly":""; ?>">
                  <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][access]" id="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                  <i class="accessFormSetting ti-lock empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'NA'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access" data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mj; ?>"  data-type="NA" id="field_noaccess-<?php echo $headingkey; ?>"></i>

                  <i class="accessFormSetting ti-pencil ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'E'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mj; ?>"  data-type="E" id="field_edit-<?php echo $headingkey; ?>"></i>

                  <i class="accessFormSetting ti-eye ml-2 empty-<?php echo $headingkey; ?> <?php echo $candfieldgrpaccess == 'V'?"text-warning":"";?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Only" data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mj; ?>"  data-type="V" id="field_viewonly-<?php echo $headingkey; ?>"></i>

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
                    <h6 class="mb-0 f-14"><?php echo ucwords(str_replace("_"," ",$profilereadines)); ?></h6>
                  </th>
                  <th scope="col" class="allformfield-<?php echo $headingkey."-label"; ?>" id="formfield-<?php echo $profilereadines."-".$mm."-label"; ?>" ><?php echo $candfieldgrpaccesslabel; ?>
                </th>

                <th scope="col" class="text-right">
                  <input type="hidden" name="fieldgrpaccess[<?php echo $headingkey; ?>][field][<?php echo $profilereadines; ?>]" class="fieldgrpaccess-hidden-<?php echo $headingkey; ?>" id="fieldaccess-hidden-<?php echo $profilereadines."-".$mm; ?>" value="<?php echo $candfieldgrpaccess; ?>">

                  <i class="accessFormfieldSetting ti-lock allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "NA"?"text-warning":""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access"
                  data-fieldname="<?php echo $profilereadines; ?>"  data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mm; ?>"  data-type="NA"  id="field_noaccess-<?php echo $profilereadines."-".$mm; ?>"></i>

                  <i class="accessFormfieldSetting ti-pencil ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "E"?"text-warning":""?> <?php echo in_array($profilereadines,$noteditable)?"custom_readonly":""; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" data-fieldname="<?php echo $profilereadines; ?>"  data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mm; ?>"  data-type="E" id="field_edit-<?php echo $profilereadines."-".$mm; ?>"></i>

                  <i class="accessFormfieldSetting ti-eye ml-2 allempty-<?php echo $headingkey; ?> emptyfield-<?php echo $profilereadines."-".$mm; ?> <?php echo $candfieldgrpaccess == "V"?"text-warning":""?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Only" data-fieldname="<?php echo $profilereadines; ?>"  data-fieldgrpname="<?php echo $headingkey; ?>"  data-seq="<?php echo $mm; ?>"  data-type="V" id="field_viewonly-<?php echo $profilereadines."-".$mm; ?>"></i>

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

                <i class=" <?php echo $dashboardaccess == "N"?"d-none":""?> accessDashSetting ti-lock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Access"  data-fieldgrpname="<?php echo $dashboardcoulnmname; ?>"  data-seq="<?php echo $mk; ?>"  data-type="noaccess" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-access"></i>

                <i class="<?php echo $dashboardaccess == "Y"?"d-none":""?> accessDashSetting ti-unlock dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Access"  data-fieldgrpname="<?php echo $dashboardcoulnmname; ?>"  data-seq="<?php echo $mk; ?>"  data-type="access" id="dash-<?php echo $dashboardcoulnmname; ?>-<?php echo $mk; ?>-noaccess" ></i>
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
