
<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_invitation'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['data_name'] = $_SESSION['DB_NAME'];
    $url = API."?mode=insertapplicationuserinvitations&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $respons = json_decode($response,1);
    $res = $respons['application_users'];
    if($res['status'] == 'sucess'){
        $_SESSION['message'] = $_SESSION['error_message']['3000'];
        $_SESSION['insert_msg'] = 3000;
        header("location:index.php?option=COM_LIST_VENDOR_INVITATIONS"); exit;
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
$getrid =  encodeId($_GET['VRRid']);
$getrid = urldecode($getrid);
$vrridary = explode(":", $getrid);
$vrrid = $vrridary['1'];
$url = API."?mode=getinvitationvendors&VRRid=".$vrrid;
$apicall = callAPI($url);
$result = json_decode($apicall,1);
$rolesres = $result['application_roles'];
$module =  $result['module_setup'];
$vendor_registration_requests =  $result['vendor_registration_requests'];
$vendor_admin_application_users =  $result['vendor_admin_application_users'];
$vendor_hr_application_users =  $result['vendor_hr_application_users'];
$vendor_finanace_application_users =  $result['vendor_finanace_application_users'];
$vendor_project_application_users =  $result['vendor_project_application_users'];
$vendor_location_application_users =  $result['vendor_location_application_users'];
$vendor_purchase_application_users =  $result['vendor_purchase_application_users'];
$vendor_legal_application_users =  $result['vendor_legal_application_users'];
$vendor_types = $result['vendor_types'];

$role_idd =array(BILL_VENDOR_ROLE,VMS_VENDOR_ROLE,PURCHASE_VENDOR_ROLE);
foreach($rolesres as $rolesre){
    if(in_array($rolesre['role_short_code'], $role_idd)){
        $roles[$rolesre['application_module']][] = $rolesre;
    }
}
if($vendor_registration_requests['id'] !=""){
    $eventdisbale = "event_disabled";
}

?>
<input type="hidden" id="hid_vendor_registration_requests_id" value="<?php echo $vendor_registration_requests['id']; ?>">

<script type="text/javascript" src="../assets/js/custom_insert_invite_vendor.js"></script>

<div class="card">
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>"></button>
        </div>
<?php } ?>
    <div class="card-header">
        <h5><?php echo __('Invite Vendor'); ?></h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light templateBackButton ">
                <i class="icofont icofont-reply"></i>  <?php echo __('Back'); ?>
            </a>
        </div>
    </div>
    <div class="card-body m-r-50 m-l-50">
        <form id="send_invitation_form" method="post" >

            <div class="form-group row ml-20 mr-20">
                <div class="form-group col-lg-4 <?php echo $eventdisbale; ?>" >
                    <span class="form-label d-block"><?php echo __('Company Name&nbsp;'); ?><span class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                    <input aria-label="<?php echo __('Company Name'); ?>" class="form-control requried_field checkCompanyNameAllReadyAvilableOrNot"  type="text"  id="company_name" name="company_name"  value="<?php echo $vendor_registration_requests['company_name']; ?>" autocomplete="off">
                     <span class="text-danger f-12 d-none" id="allreadycompanyname" ><?php echo __('Company Name Already Register'); ?></span>
                </div>
                <div class="form-group col-lg-4 <?php echo $eventdisbale; ?>" >
                     <span class="form-label  d-block"><?php echo __('Contact Person First Name&nbsp;'); ?><span class="requried">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                     <input aria-label="<?php echo __('First Name'); ?>" class="form-control requried_field"  type="text"  id="first_name" name="first_name"  value="<?php echo $vendor_registration_requests['first_name']; ?>" autocomplete="off">
                 </div>
                 <div class="form-group col-lg-4 <?php echo $eventdisbale; ?>" >
                     <span class="form-label  d-block"><?php echo __('Contact Person Last Name &nbsp;'); ?><span class="requried">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                     <input aria-label="<?php echo __('Last Name'); ?>" class="form-control requried_field"  type="text"  id="last_name" name="last_name"  value="<?php echo $vendor_registration_requests['last_name']; ?>" autocomplete="off">
                     <div class="d-none text-danger" id="last_name_err"> <?php echo __('Please enter Last Name'); ?> </div>
                </div>
                <div class="form-group col-lg-4 <?php echo $eventdisbale; ?>" >
                     <span class="form-label d-block"><?php echo __('Contact Person Email Id&nbsp;'); ?><span class="requried">*</span></span>
                         <span class="d-block error-message text-danger"></span>
                        <input aria-label="<?php echo __('User Email'); ?>" class="form-control allemails requried_field checkEmailidAllReadyAvilableOrNot"  type="text"  id="user_email" name="user_email" data-errorcls="repeateemailerror" data-errorcls1="validrepeateemailerror"   value="<?php echo $vendor_registration_requests['user_email']; ?>" autocomplete="off">
                    <span class="text-danger f-12 allemailerrer d-none" id="repeateemailerror" ><?php echo __('Email Id Already Register'); ?></span>
                    <span class="text-danger f-12 allemailerrer  d-none" id="validrepeateemailerror" ><?php echo __('Please Enter Valid Email Id'); ?></span>
                </div>
                <div class="form-group col-lg-4 <?php echo $eventdisbale; ?>" >
                    <span class="form-label  d-block"><?php echo __('Contact Person Mobile No.'); ?></span>
                        <span class="d-block error-message text-danger"></span>
                    <div class="input-group input-icon-right">
                    <input aria-label="<?php echo __('User Mobile'); ?>" class="form-control"  type="number"  id="user_mobile" name="user_mobile"  value="<?php echo $vendor_registration_requests['user_mobile']; ?>" autocomplete="off">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label  d-block"><?php echo __('Vendor Type &nbsp;'); ?><span class="requried">*</span></span>

                    <div class="kt-input-icon">
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="<?php echo __('Vendor Type'); ?>"  name="vendor_type_id"  class="form-select js-example-basic-single selectVendorTypes requried_field">
                            <option value=""><?php echo __('Select Any One'); ?></option>
                            <?php foreach ($vendor_types as $vendortypes) { ?>
                                <option value="<?php echo $vendortypes['id']; ?>" <?php echo ($vendor_registration_requests['vendor_type_id'] == $vendortypes['id'])?"selected":""; ?>>
                                    <?php echo $vendortypes['type_name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>
                    <div class="d-none text-danger" id="phone_err"> <?php echo __('Please Vendor Type'); ?> </div>
                </div>
                <div class="form-group col-lg-4">
                     <span class="form-label  d-block"><?php echo __('Invitation Type &nbsp;'); ?><span class="requried">*</span></span>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                          <label class="form-label">
                            <input aria-label="<?php echo __('Transaction Table'); ?>" type="radio" name="transaction_table" value="vendors" <?php echo $_GET['transaction_table'] == "vendors"?"checked":""; ?>>
                            <i class="helper"></i> <?php echo __('Invite Direct Vendor'); ?>
                          </label>
                        </div>
                        <div class="radio radio-inline">
                          <label class="form-label">
                            <input aria-label="<?php echo __('Transaction Table'); ?>" type="radio" name="transaction_table" value="vendor_registers" <?php echo ($_GET['transaction_table']== "" || $_GET['transaction_table']== "vendor_registers")?"checked":""; ?>>
                            <i class="helper"></i> <?php echo __('Invite Vendor Registration Process'); ?>
                          </label>
                        </div>
                    </div>

                    <input class="form-control"  type="hidden" id="role_id" name="role_id"  value="<?php echo VENDOR_ROLE; ?>">
              </div>

                <div class="col-lg-4 form-group bothhidemsme msme_register_yes_no_switch_class <?php echo ($vendor_registration_requests['vendor_type_id'] == '3')?"":"d-none"; ?>">
                    <span class="form-label d-block"><?php echo __('Udyam Registration/MSME'); ?></span>
                    <input aria-label="<?php echo __('Msme Register Yes No'); ?>" type="checkbox" class="js-switch MSMERegisterYesNo" value="Y" name="msme_register_yes_no"
                        id="msme_register_yes_no" <?php echo $vendordetail['msme_register_yes_no'] == "Y" ? "checked" : ""; ?> >
                </div>

                <div class="col-lg-4 form-group bothhidemsme d-none" id="msme_number_id" >
                    <span class="form-label d-block"><?php echo __('Udyam Registration/MSME Number'); ?></span>
                    <input aria-label="<?php echo __('Input field'); ?>" class="form-control <?php echo $accessreadonly; ?>" type="text" id="msme_number"
                            name="msme_number" value="<?php echo $vendordetail['msme_number']; ?>" autocomplete="off">
                </div>



            </div>
            <div class="form-group row ml-20 mr-20">
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendor_registration_requests['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block"><?php echo __('HR Connect'); ?></span>
                     <span class="d-block error-message text-danger"></span>
                    <select aria-label="<?php echo __('Connect[hr Connect]'); ?>" class="form-control js-example-basic-single 3rdpartyvendortypeselect
                    " name="connect[hr_connect]" >
                        <option value=""><?php echo __('Not Applicable'); ?></option>
                        <?php foreach($vendor_hr_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendor_registration_requests['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block"><?php echo __('Finance Connect'); ?></span>
                    <select aria-label="<?php echo __('Connect[finance Connect]'); ?>" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="connect[finance_connect]" >
                        <option value=""><?php echo __('Not Applicable'); ?></option>
                        <?php foreach($vendor_finanace_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>

                    </select>
                </div>
                <div class="form-group col-lg-3  w-full ">
                    <span class="form-label  d-block"><?php echo __('Admin Connect &nbsp;'); ?><span class="requried">*</span></span>
                    <select aria-label="<?php echo __('Connect[admin Connect]'); ?>" class="form-control js-example-basic-single all_connect_check requried_field" name="connect[admin_connect]" >
                        <?php foreach($vendor_admin_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>" <?php echo $_SESSION['userid'] == $auser['id']?"selected":""?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                            <option value="" ><?php echo __('Not Applicable'); ?></option>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full  notrequird3rdpartyvendortype <?php echo ($vendor_registration_requests['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label  d-block"><?php echo __('Project Connect'); ?></span>
                    <select aria-label="<?php echo __('Connect[project Connect]'); ?>" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="connect[project_connect]" >
                        <option value=""><?php echo __('Not Applicable'); ?></option>
                        <?php foreach($vendor_project_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>"><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full ">
                    <span class="form-label  d-block"><?php echo __('Location Connect'); ?></span>

                    <select aria-label="<?php echo __('Connect[location Connect]'); ?>" class="form-control js-example-basic-single all_connect_check " name="connect[location_connect]" >
                        <option value=""><?php echo __('Not Applicable'); ?></option>
                        <?php foreach($vendor_location_application_users as $auser){  ?>
                          <option value="<?php echo $auser['id']; ?>" <?php echo $_SESSION['userid'] == $auser['id']?"selected":""?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3  w-full notrequird3rdpartyvendortype <?php echo ($vendor_registration_requests['vendor_type_id'] != 1)?"d-none":""; ?>">
                    <span class="form-label"><?php echo __('Purchase Connect'); ?></span>

                    <select aria-label="<?php echo __('Connect[purchase Connect]'); ?>" class="form-control js-example-basic-single all_connect_check 3rdpartyvendortypeselect" name="connect[purchase_connect]" >
                         <option value=""><?php echo __('Not Applicable'); ?></option>
                      <?php foreach($vendor_purchase_application_users as $auser){  ?>
                            <option value="<?php echo $auser['id']; ?>" ><?php echo $auser['display_name']; ?></option>
                            <?php } ?>

                    </select>
                </div>
                 <div class="form-group col-lg-3 w-full <?php echo $eventdisbale; ?>" >
                    <span class="form-label  d-block"><?php echo __('Legal Connect'); ?></span>

                    <select aria-label="<?php echo __('Connect[legal Connect]'); ?>" class="form-control js-example-basic-single all_connect_check " name="connect[legal_connect]" >
                        <option value=""><?php echo __('Not Applicable'); ?></option>
                      <?php foreach($vendor_legal_application_users as $auser){
                        $selected = "";
                        if($vendor_registration_requests['legal_connect'] == $auser['id']){
                           $selected = "selected";
                        }
                       ?>
                          <option value="<?php echo $auser['id']; ?>" <?php echo $selected; ?>><?php echo $auser['display_name']; ?></option>
                            <?php } ?>

                    </select>
                </div>


                <input type="hidden" name="user_type" value="3">
                <input  type="hidden"  id="company_name_validate" value="1">
                <input  type="hidden"  id="user_email_validate" value="1">
                <input  type="hidden"  name="vendor_registration_request_id" value="<?php echo $vrrid; ?>">
            </div>
            <div class="">
                <div class="row">
                  <span class="text-danger f-12 d-none" id="selectanyoneconnecterror" ><?php echo __('Please Select Any One Connect'); ?></span>
                    <div class="col-lg-9">
                        <input type="hidden" name="submit_invitation" value="Add" />
                        <button type="button" class="btn btn-primary sendInvitation mr-2 disabled"   name="submit_value" value="exit" id="invitevendorbtn" ><i class="la la-check"></i><?php echo __('Send Invitation'); ?></button>

                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

