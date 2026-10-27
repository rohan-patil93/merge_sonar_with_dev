<?php
if(in_array(ADMIN_ROLE,(array)$_SESSION['role_ids']) || in_array(VMS_ADMIN_ROLE,(array)$_SESSION['role_ids'])  || in_array(VENDOR_MODULE_ADMIN_ROLE,(array)$_SESSION['role_ids'])){
  $wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','user_type'=>'3');
}else{
  $wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','user_type'=>'3','created_by'=>$_SESSION['userid']);
}
$wher["field"] = "id,first_name,display_name,user_email,user_mobile,created_date,user_id,role_id,company_name";
$encodeurl = urlencode(json_encode($wher));
$urlcat = API."?mode=gettabledetailswithconditions&table=application_user_invitations&data=".$encodeurl;
$invitationres = callAPI($urlcat);
$invitationlis = json_decode($invitationres,1);
if(!in_array(SUPERADMIN_ROLE,(array)$_SESSION['roles'])){
  foreach($invitationlis as $invitationlis){
    if($invitationlis['role_id'] != ADMIN_ROLES){
      $invitationlists[] =  $invitationlis;
    }
  }
}else{
  $invitationlists =  $invitationlis;
}
$wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$wher["field"] = "id,image,display_name,first_name,user_id,company,image";
$userencodeurl = urlencode(json_encode($wher));
$userurlcat = API."?mode=gettabledetailswithconditions&table=vendors&data=".$userencodeurl;
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$applicationusers = array();
foreach($useruresponse as $useruresponse1){
  $applicationusers[$useruresponse1['user_id']] = $useruresponse1;
}

$userurlcat = API."?mode=gettabledetailswithconditions&table=vendor_registers&data=".$userencodeurl;
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$vendorregisters = array();
foreach($useruresponse as $useruresponse2){
  $vendorregisters[$useruresponse2['user_id']] = $useruresponse2;
}

if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <input type="hidden" id="hide_error_message_code" value="<?php echo $msgcode; ?>">
    <script type="text/javascript" src="../assets/js/tostarnotify_message.js"></script>
<?php
}

?>
<script type="text/javascript" src="../assets/js/custom_list_vendor_invitations.js"></script>

<link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendor_display_fields.css">

<div class="page-body">

  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header ">
          <h5>Vendors</h5>
          <div class="card-header-right table-card-header ">
          <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="hideall" checked class="listUserType">
                        All
                      </label>
          </div>
          <div class="radio radio-inline">
            <label class="form-label">
            <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="active_vend" class="listUserType">
            Active
            </label>
          </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="inprogress_vend" class="listUserType">
                        In-Progress
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="invited_vend" class="listUserType">
                        Invited
                      </label>
                    </div>



<a href="index.php?option=COM_INSERT_INVITE_VENDOR" class="btn btn-success mb-2 desktop-view <?php echo getPagePermision('COM_INSERT_INVITE_VENDOR','','INSERT')['class']; ?>"  <?php echo getPagePermision('COM_INSERT_INVITE_VENDOR','','INSERT')['tooltip']; ?>>
            <i class="icofont icofont-plus icon-white text-white"></i>
            Invite Vendor
          </a>
    </div>

            </div>

        <div class="row p-4">

          <?php
           $bg_color_array = array('bg-warning','bg-info','bg-success');
            if(count((array)$invitationlists) !="0"){
            $permisions = "no-permission";
            if(getPagePermision('COM_INSERT_VENDORS','','INSERT')['permission'] == "Y" || getPagePermision('COM_INSERT_VENDORS','','VIEW')['permission'] == "Y" || getPagePermision('COM_INSERT_VENDORS','','UPDATE')['permission'] == "Y"){
                $permisions = "";
            }
            foreach ($invitationlists as $vendorslist){
              if($vendorslist['user_id'] != $_SESSION['userid']){
                $applicationuser = $applicationusers[$vendorslist['user_id']];
                $vendorregisters11 = $vendorregisters[$vendorslist['user_id']];
                $stausresult = $_SESSION['status'][$vendorslist['current_status']];
                        if(count((array)$applicationuser) != 0){
                            $ribbon =  "Active";
                            $classadded = "active_vend";
                  $tven =2;
                  $url = "index?option=COM_INSERT_VENDORS&VTYPE=V&Rid=".deocdeId('id:'.$applicationuser['id']);
                  $displayname =$applicationuser['display_name'];
                  $first_name = $applicationuser['first_name'];
                  $company_name = $applicationuser['company'];
                  $image = $applicationuser['image'];
                        }elseif(count((array)$vendorregisters11) != 0){
                  $ribbon =  "In-Progress";
                  $classadded = "inprogress_vend";
                  $url = "index?option=COM_INSERT_VENDOR_REGISTERS&VTYPE=VR&Rid=".deocdeId('id:'.$vendorregisters11['id']);
                  $tven =1;
                  $displayname =$vendorregisters11['display_name'];
                  $first_name = $vendorregisters11['first_name'];
                  $company_name = $vendorregisters11['company'];
                  $image = $applicationuser['image'];
                }else{
                            $ribbon = "Invited";
                            $classadded = "invited_vend";
                  $tven =0;
                  $displayname =$vendorslist['display_name'];
                  $first_name = $vendorslist['first_name'];
                  $company_name = $vendorregisters11['company_name'];
                        }
            ?>

      <div class="col-lg-3 custom-ribbon-accordian cursor-pointer  position-relative hideall <?php echo $classadded; ?>" >

              <div class="ribbon ribbon-clip ribbon-right right-0">
                <div class="ribbon-target right-3">
                  <span class="ribbon-inner" ></span> <?php echo $ribbon; ?>
                </div>
              </div>
              <?php if($tven == '1' || $tven == '2'){ ?>
       <a  href="<?php echo $url; ?>" class="<?php echo $permisions; ?>">
       <?php }?>
              <div class="card <?php echo $bg_color_array[$tven]; ?>" style=" ">
                <div class="p-3">
                  <div class="row m-b-10 align-items-center ">
                    <div class="col-auto col-lg-10">
                      <div class="align-middle">


                       <?php if($applicationuser['user_photo'] != "" && $applicationuser['user_activated'] == "Y"){ ?>
    <img src="uploads/all/<?php echo $applicationuser['user_photo']; ?>"  class="img-radius img-30 m-r-15" alt="User Profile">
  <?php } else { ?>
   <span class="img-radius img-50 custom_txt_round  p-2 f-14 m-r-15 text-center bg-white text-mute"><?php echo initials($displayname); ?></span>
  <?php } ?>
                      <div class="d-inline-block width73" >
                        <h6 class="mb-1"><?php echo $company_name !=""?$company_name:$displayname; ?></h6>
                        <p class="text-white m-b-0  f-12"></p>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="row">
                <span class="col-lg-3 f-12 mb-1">Name :</span>
                <span class="col-lg-9 f-12"> <?php echo $displayname; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Since  :</span>
                <span class="col-lg-9 f-12"> <?php echo fromDate($vendorslist['created_date']); ?> </span>
                <span class="col-lg-3 f-12 mb-1">Email  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_email']; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Phone  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_mobile']!=""?$vendorslist['user_mobile']:"-"; ?> </span>
              </div>
              <?php if($tven == '0'){ ?>
              <i class="ti-share float-right position-absolute text-white resendrequest right-70 top-10" data-user_id = '<?php echo $vendorslist['user_id']; ?>' data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Re-Invite User"></i>
            <?php } ?>
            </div>
          </div>
           <?php if($tven == '1' || $tven == '2'){?>
</a>
<?php } ?>
        </div>
        <?php $tven++;
        if($tven ==2){ $tven=0;
}
          }
        }
    }else{   ?>

 <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Vendor Invitation Available</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
      </div>
  </div>

            <?php    } ?>

 <div class="card-px text-center py-20 m-t-30 d-none" id="available_record_error" >
        <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Vendor Invitation Available</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
      </div>
  </div>

      </div>
    </div>
  </div>
</div>
</div>
<?php /**/ ?>
