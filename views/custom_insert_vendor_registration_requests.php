<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_invitation'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['data_name'] = $_SESSION['DB_NAME'];
    $url = API."?mode=insertvendorregistrationrequests&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $ress = json_decode($response,1);
    $res = $ress['vendor_registration_requests'];
  if($res['status'] == 'sucess'){
      if($res['LASTID'] == 0){
        $msgcode = 3001; //update
        $_SESSION['message'] = $_SESSION['error_message']['3001'];
        $_SESSION['insert_msg'] = $msgcode;
        ?>
         <input type="hidden" id="hide_error_message_code" value="3000">
            <script type="text/javascript" src="../assets/js/tostarnotify_message.js"></script>
    <?php

    }else{
        $msgcode = 3000; //insert
        $_SESSION['message'] = $_SESSION['error_message']['3000'];
        $_SESSION['insert_msg'] = $msgcode;
       header("location:index.php?option=COM_LIST_VENDOR_REGISTRATION_REQUESTS"); exit;
    }
  }else{
    if(isset($_SESSION['error'][$res['error_no']])){
      $dis_err = explode(",", $res['other']);
      foreach ($dis_err as $va) {
        $dis_er [] = $dis_labels[$va];
      }
      $error_message = $res['error_no'] ." : ".$_SESSION['error'][$res['error_no']]." : ".implode(", ", (array)$dis_er);
    }else{
      $error_message = $res['message'];
    }
  }
}
$getrid =  encodeId($_GET['Rid']);
$getrid = urldecode($getrid);
$vrridary = explode(":", $getrid);
$vrrid = $vrridary['1'];
$url = API."?mode=getvendorregistrationrequests&rid=".$vrrid;
$apicall = callAPI($url);
$result = json_decode($apicall,1);
$vendor_registration_requests =  $result['vendor_registration_requests'];
$vendor_legal_application_users =  $result['vendor_legal_application_users'];
$countries = $result['countries'];
$states = $result['states'];
$registration_questions = json_decode($vendor_registration_requests['registration_questions'],1);
$vendor_types = $result['vendor_types'];
if($vendor_registration_requests['share_pdf'] =="Y"){
  $eventdisbale = "event_disabled";
  $eventdisabletext = "readonly";
}

?>
<script type="text/javascript" src="../assets/js/custom_insert_vendor_registration_requests.js"></script>

<div class="card " >
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
    <div class="card-header">
        <h5>Vendor Registration Request</h5>
        <div class="card-header-right">

            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light templateBackButton">
                <i class="icofont icofont-reply"></i>  Back
            </a>
        </div>
    </div>
    <div class="card-body m-r-50 m-l-50 <?php echo $eventdisbale; ?>" >
        <form id="send_invitation_form" method="post" >

            <div class="form-group row ml-20 mr-20">
                <div class="form-group col-lg-4">
                    <span class="form-label">Company Name&nbsp;<span class="requried text-dange">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                    <input aria-label="Company Name" class="form-control requried_field checkCompanyNameAllReadyAvilableOrNot" type="text"  id="company_name" name="company_name"  value="<?php echo $vendor_registration_requests['company_name']; ?>" >
                     <span class="text-danger f-12 d-none" id="allreadycompanyname" >Company Name Already Register</span>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Company Web Site</span>
                    <input aria-label="Web Site" class="form-control"  type="text"  id="web_site" name="web_site"  value="<?php echo $vendor_registration_requests['web_site']; ?>">
                </div>
                <div class="form-group col-lg-4">
                      <span class="form-label">Vendor Type <span class="requried text-dange">*</span></span>
                      <select aria-label="Vendor Type"  name="vendor_type_id"  class="form-select js-example-basic-single requried_field SelectVendorTypes">
                          <?php foreach ($vendor_types as $vendortypes) { ?>
                              <option value="<?php echo $vendortypes['id']; ?>" <?php echo $vendor_registration_requests['vendor_type_id'] == $vendortypes['id']?"selected":""?>>
                                  <?php echo $vendortypes['type_name']; ?>
                              </option>
                          <?php } ?>
                      </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Organization Country <span class="requried text-dange">*</span></span>
                    <select aria-label="Country" class="form-control js-example-basic-single selectedcategory requried_field " name="country_id" data-element_id="state_id"data-table_name="states" data-field_name="country_id">
                      <?php foreach($countries as $coun){  ?>
                            <option value="<?php echo $coun['id']; ?>" <?php echo (($vendor_registration_requests['country_id'] == $coun['id']) || ($vendor_registration_requests['country_id'] =="" && $coun['id'] == '101')) ?"selected":""?>><?php echo $coun['country_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Organization State <span class="requried text-dange">*</span></span>
                    <select aria-label="State" class="form-control js-example-basic-single requried_field" name="state_id" id="state_id">
                      <option value="">Select Any One</option>
                      <?php foreach($states as $sts){  ?>
                            <option value="<?php echo $sts['id']; ?>" <?php echo $vendor_registration_requests['state_id'] == $sts['id']?"selected":""?>><?php echo $sts['state_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Legal Connect &nbsp;<span class="requried text-danger">*</span></span>
                     <span class="d-block error-message text-danger"></span>
                    <select aria-label="Legal Connect" class="form-control js-example-basic-single  requried_field" name="legal_connect" >
                      <option value="">Select Any One</option>
                      <?php foreach($vendor_legal_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>" <?php echo $vendor_registration_requests['legal_connect'] == $auser['id']?"selected":""?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                     <span class="form-label">Contact Person First Name&nbsp;<span class="requried text-dange">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                     <input aria-label="First Name" class="form-control requried_field"  type="text"  id="first_name" name="first_name"  value="<?php echo $vendor_registration_requests['first_name']; ?>" >
                 </div>
                 <div class="form-group col-lg-4">
                     <span class="form-label">Contact Person Last Name &nbsp;<span class="requried text-danger">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                     <input aria-label="Last Name" class="form-control requried_field"  type="text"  id="last_name" name="last_name"  value="<?php echo $vendor_registration_requests['last_name']; ?>" >
                </div>
                <div class="form-group col-lg-4">
                     <span class="form-label d-block">Contact Person Email Id &nbsp;<span class="requried text-danger">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                        <input aria-label="User Email" class="form-control allemails requried_field checkEmailidAllReadyAvilableOrNot"  type="text"  id="user_email" name="user_email" data-errorcls="repeateemailerror" data-errorcls1="validrepeateemailerror"    value="<?php echo $vendor_registration_requests['user_email']; ?>" >
                     <span class="text-danger f-12 allemailerrer d-none" id="repeateemailerror" >Email Id Already Register</span>
                     <span class="text-danger f-12 allemailerrer d-none" id="validrepeateemailerror" >Please Enter Valid Email Id</span>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Contact Person Mobile No.</span>
                    <input aria-label="User Mobile" class="form-control"  type="number"  id="user_mobile" name="user_mobile"  value="<?php echo $vendor_registration_requests['user_mobile']; ?>">
                </div>
                <div class="form-group col-lg-8">
                    <span class="form-label">Note</span>
                    <textarea aria-label="Note" class="form-control"  type="text"  id="note" name="note" ><?php echo $vendor_registration_requests['note']; ?></textarea>
                </div>
                <input  type="hidden"  id="company_name_validate" value="1">
                <input  type="hidden"  id="user_email_validate" value="1">
            </div>
            <div>
              <div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Vendor Related Questions</div>
              <div class="m-l-20">
                <div class="row mt-2">
                  <div class="border-checkbox-section mt-4">
                    <div class="border-checkbox-group border-checkbox-group-primary buttonEnableOrDisable read_policy_div">
                        <input class="border-checkbox" type="checkbox" id="read_policy" value="Y" name="registration_questions[read_policy]" <?php echo $registration_questions['read_policy'] =="Y"?"checked":""; ?>>
                        <label class="form-label border-checkbox-label f-w-600" for="read_policy">I have read <?php echo ucwords($_SESSION['config']['name']); ?> <a class="text-primary" href="https://microchiplearningcenter.sumtotal.host/rcore/c/pillarRedirect?relyingParty=LM&url=core%2factivitydetails%2fViewActivityDetails%3fActivityId%3d114643%26UserMode%3d0" target="_blank">Global Anti-Bribery Policy</a>
                          </label>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <span class="f-w-600">1) The Consulting Company has been proposed on the basis that it can provide professional services solely for legitimate business purposes.</span>
                </div>
                <div class="row mt-2">
                  <span class="f-w-600">2) To the best of my knowledge, the Consulting Company has a good business and ethical reputation in the country(ies) in which the Consulting Company will provide services to <?php echo ucwords($_SESSION['config']['name']); ?></span>
                </div>
                <div class="row mt-2">
                  <span class="text-danger f-w-600">3) The Consulting Company has not promised, offered, or given to me, or to anyone else that I am aware of at <?php echo ucwords($_SESSION['config']['name']); ?>, any kind of gift, money or anything else of value, in exchange for proposing it as a Consulting Company.</span>
                </div>
                <div class="row mt-2">
                  <span class="f-w-600"> 4) I have asked the proposed company and to the best of my knowledge, the Consulting Company is not currently suspended or debarred by any government in any part of the world from doing business.</span>
                </div>
                <div class="row mt-2">
                  <span class="f-w-600">5) For engagements with a Consulting Company who supports accounts outside the U.S., I confirm that I have taken the <a class="text-primary" href="https://microchiplearningcenter.sumtotal.host/rcore/c/pillarRedirect?isDeepLink=1&relyingParty=LM&url=https%3A%2F%2FMICROCHIPLEARNINGCENTER.sumtotal.host%2Flearning%2Fcore%2Factivitydetails%2FViewActivityDetails%3FUserMode%3D0%26ActivityId%3D105653%26ClassUnderStruct%3DFalse%26CallerUrl%3D%2Flearning%2Flearner%2FHome%2FGoToPortal%3Fkey%3D0%26SearchCallerURL%3Dhttps%253A%252F%252FMICROCHIPLEARNINGCENTER.sumtotal.host%252Fcore%252FsearchRedirect%253FViewType%253DList%2526SearchText%253Dcompliance%25252520with%25252520laws%2526startRow%253D0%26SearchCallerID%3D2" target="_blank">Compliance Training</a> within the last 12 months.</span>
                </div>
                <div class="row mt-2">
                  <span class="f-w-600">6) I agree to promptly notify (1) my local legal representative or (2) contact legal.department@microchip.com of any information that would conflict with any of the points addressed in this certification or if any other concerns are later raised regarding the Consulting Company’s activities or qualifications.</span>
                </div>
              <div class="row mt-2">
                <span class="f-w-600">7) I have reviewed and agree that the existing Preferred Consulting Company able to carry out the proposed consulting services?</span>
                <div class="form-radio">
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Registration Questions[questions][1]" type="radio" value="Y" name="registration_questions[questions][1]" <?php echo $registration_questions['questions']['1'] =="Y"?"checked":""; ?> class="onchangeQuestionAnswer" >
                      <i class="helper"></i>Yes
                    </label>
                  </div>
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Registration Questions[questions][1]" type="radio" value="N" name="registration_questions[questions][1]" <?php echo ($registration_questions['questions']['1']=="N" || $registration_questions['questions']['1']=="")?"checked":""; ?> class="onchangeQuestionAnswer">
                      <i class="helper"></i>No
                    </label>
                  </div>
                </div>
              </div>
              <div class="m-l-20 mt-2" style="">
                <div id="registration_questions_7_yes" class="<?php echo $registration_questions['questions']['1'] =="Y"?"":"d-none"; ?>">
                  <span class="f-w-600">a) If YES what are the specific business justifications for entering into this Consulting Company relationship?</span>
                  <textarea aria-label="Registration Questions[questions][Y]" class="form-control mt-2"  type="text"  id="user_mobile_2" name="registration_questions[questions][Y]" ><?php echo $registration_questions['questions']['Y']; ?></textarea>
                </div>
                <div class=" mt-2 <?php echo ($registration_questions['questions']['1']=="N" || $registration_questions['questions']['1']=="")?"":"d-none"; ?>" id="registration_questions_7_no" >
                  <span class="f-w-600">a) If NO what were the main reasons why, during Consulting Company Selection, you could not find an existing Consulting Company partner that fits your need: For example, geographical, customer requirement, security clearance, technical skills, etc.</span>
                  <textarea aria-label="Registration Questions[questions][N]" class="form-control mt-2"  type="text"  id="user_mobile_3" name="registration_questions[questions][N]"  ><?php echo $registration_questions['questions']['N']; ?></textarea>
                </div>
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">8) Describe the nature of services and deliverables that the proposed Consulting Company will provide. Please include as much detail as possible, including any technology details or skills sets. Also, please go to the Technology List table at the foot of this document and check each and every Technology Area that you recommend this Consulting Company is capable of:</span>
                  <textarea aria-label="Registration Questions[questions][8]" class="form-control mt-2"  type="text"  id="user_mobile_4" name="registration_questions[questions][8]" ><?php echo $registration_questions['questions']['8']; ?></textarea>
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">8) What were the selection criteria that helped you choose this particular Consulting Company?</span>
                  <textarea aria-label="Registration Questions[questions][9]" class="form-control mt-2"  type="text"  id="user_mobile_5" name="registration_questions[questions][9]" ><?php echo $registration_questions['questions']['9']; ?></textarea>
              </div>
              <div class="row mt-2">
                  <div class="border-checkbox-section mt-4">
                    <div class="border-checkbox-group border-checkbox-group-primary buttonEnableOrDisable" >
                        <input class="border-checkbox" type="checkbox" id="termsandcondition_3" value="Y" name="registration_questions[aggree][check]" <?php echo $registration_questions['aggree']['check'] =="Y"?"checked":""; ?>>
                        <label class="form-label border-checkbox-label f-w-600" for="termsandcondition_3">I AGREE</label>
                    </div>
                </div>
              </div>
              <div class=" mt-2">
                <input  type="hidden"  name="registration_questions[aggree][name]" value="<?php echo $registration_questions['aggree']['name'] !=""?$registration_questions['aggree']['name']:$_SESSION['user']['display_name']; ?>">
                  <span class="d-block"><span class="f-w-600">Name :</span> <?php echo $registration_questions['aggree']['name'] !=""?$registration_questions['aggree']['name']:$_SESSION['user']['display_name']; ?></span>
                   <input  type="hidden"  name="registration_questions[aggree][date]" value="<?php echo $registration_questions['aggree']['date'] !=""?$registration_questions['aggree']['date']:fromDate(); ?>">
                  <span class="d-block"><span class="f-w-600">Date :</span> <?php echo $registration_questions['aggree']['date'] !=""?$registration_questions['aggree']['date']:fromDate(); ?></span>
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Business Unit</span>
                  <input aria-label="Registration Questions[aggree][business Unit]" class="form-control mt-2"  type="text" name="registration_questions[aggree][business_unit]"  value="<?php echo $registration_questions['aggree']['business_unit']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Business Unit Vice President</span>
                  <input aria-label="Registration Questions[aggree][business Unit Vice President]" class="form-control mt-2"  type="text" name="registration_questions[aggree][business_unit_vice_president]" value="<?php echo $registration_questions['aggree']['business_unit_vice_president']; ?>">
              </div>
              <div class="f-15 f-w-600 text-info mb-2 mt-4" style="clear: both;">Commercial Terms<span class="f-12 d-block text-mute">What are the Consulting Company’s indicative day rates for services (paste list if applicable)?</span></div>
              <div class=" mt-2">
                  <span class="f-w-600">US$ / INR per day</span>
                  <input aria-label="Registration Questions[incoterms][inr Per Day]" class="form-control mt-2 decimalnumberallowe "  type="text"  name="registration_questions[incoterms][inr_per_day]"  value="<?php echo $registration_questions['incoterms']['inr_per_day']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Total Spend for this project</span>
                  <input aria-label="Registration Questions[incoterms][total Spend Project]" class="form-control mt-2 decimalnumberallowe "  type="text"   name="registration_questions[incoterms][total_spend_project]"  value="<?php echo $registration_questions['incoterms']['total_spend_project']; ?>">
              </div>
              <div class="row mt-2">
                <h6>Will you use this Consulting Company for another project</h6>
                <div class="form-radio">
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Registration Questions[incoterms][another Project]" type="radio" value="Y" name="registration_questions[incoterms][another_project]" <?php echo $registration_questions['incoterms']['another_project'] =="Y"?"checked":""; ?>>
                      <i class="helper"></i>Yes
                    </label>
                  </div>
                  <div class="radio radio-inline">
                    <label class="form-label">
                      <input aria-label="Registration Questions[incoterms][another Project]" type="radio" value="N" name="registration_questions[incoterms][another_project]" <?php echo ($registration_questions['incoterms']['another_project']=="N" || $registration_questions['incoterms']['another_project']=="")?"checked":""; ?>>
                      <i class="helper"></i>No
                    </label>
                  </div>
                </div>
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Total Spend in next 12 months</span>
                  <input aria-label="Registration Questions[incoterms][total Spend 12 Month]" class="form-control mt-2 decimalnumberallowe "  type="text"  name="registration_questions[incoterms][total_spend_12_month]"  value="<?php echo $registration_questions['incoterms']['total_spend_12_month']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Where will the services be performed</span>
                  <textarea aria-label="Registration Questions[incoterms][services Be Performed]" class="form-control mt-2"  type="text" name="registration_questions[incoterms][services_be_performed]" ><?php echo $registration_questions['incoterms']['services_be_performed']; ?></textarea>
              </div>

               <div class="f-15 f-w-600 text-info mb-2 mt-4" style="clear: both;">Consulting Company's Notices Contact (person to receive all official notices under the Consulting Agreement)</div>
              <div class=" mt-2">
                  <span class="f-w-600">Name or Title</span>
                  <input aria-label="Registration Questions[official Notices][name Title]" class="form-control mt-2"  type="text" name="registration_questions[official_notices][name_title]" value="<?php echo $registration_questions['official_notices']['name_title']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Mailing Address</span>
                  <input aria-label="Registration Questions[official Notices][address]" class="form-control mt-2"  type="text" name="registration_questions[official_notices][address]" value="<?php echo $registration_questions['official_notices']['address']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Facsimile Number</span>
                  <input aria-label="Registration Questions[official Notices][facsimile Number]" class="form-control mt-2"  type="text" name="registration_questions[official_notices][facsimile_number]" value="<?php echo $registration_questions['official_notices']['facsimile_number']; ?>">
              </div>

               <div class="f-15 f-w-600 text-info mb-2 mt-4" style="clear: both;">Consulting Company Contact with authority to sign the Consulting Agreement on behalf of the Consulting Company</div>
              <div class=" mt-2">
                  <span class="f-w-600">Name</span>
                  <input aria-label="Registration Questions[authority Sign Behalf][name]" class="form-control mt-2"  type="text"  name="registration_questions[authority_sign_behalf][name]" value="<?php echo $registration_questions['authority_sign_behalf']['name']; ?>">
              </div>
              <div class=" mt-2">
                  <span class="f-w-600">Email Address</span>
                  <input aria-label="Registration Questions[authority Sign Behalf][email]" class="form-control mt-2"  type="text" name="registration_questions[authority_sign_behalf][email]" value="<?php echo $registration_questions['authority_sign_behalf']['email']; ?>">
              </div>
              <span class="f-12 text-danger">IMPORTANT: The person named above must have the authority to legally bind the Consulting Company. Failure to provide the correct person will delay the Consulting Company 's ability to sign the Consulting Agreement.</span>

          </div>
        </div>
        <?php if($vendor_registration_requests['share_pdf'] !="Y"){ ?>
          <?php $permisions = "no-permission";
          if(getPagePermision('COM_INSERT_INVITE_VENDOR','','INSERT')['permission'] == "Y" || getPagePermision('COM_INSERT_INVITE_VENDOR','','VIEW')['permission'] == "Y" || getPagePermision('COM_INSERT_INVITE_VENDOR','','UPDATE')['permission'] == "Y"){
              $permisions = "";
            } ?>
            <div class="mt-3">
                <div class="row">
                    <div class="col-lg-9">
                        <input type="hidden" name="submit_invitation" value="Add" />
                        <input type="hidden" name="submit_type" id="submit_type" value="SE" />
                        <input type="hidden" name="rid" id="rid" value="<?php echo $vendor_registration_requests['id']; ?>" />
                        <button type="button" class="btn btn-primary  mr-2 <?php echo $vendor_registration_requests['id'] ==""?"disabled":""; ?> invitevendorbtn sendInvitation" data-sendinvtype="SE" name="submit_value" value="exit" id="invitevendorbtn"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click to save the data" <?php echo $permisions; ?>><i class="la la-check"></i>Save</button>

                        <button type="button" class="btn btn-danger  mr-2 <?php echo $vendor_registration_requests['id'] ==""?"disabled":""; ?> invitevendorbtn sendInvitation" data-sendinvtype="SNE"  name="submit_value" value="exit" id="invitevendorbtn_2" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click to submit the request to admin"><i class="la la-check"></i>Submit</button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</div>
