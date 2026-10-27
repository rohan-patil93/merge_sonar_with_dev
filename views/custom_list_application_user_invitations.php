<?php

if(in_array(SUPERADMIN_ROLE,(array)$_SESSION['roles'])){
  $wher["where"] = array('is_active'=>'0','role_id'=>ADMIN_ROLES);
}else{
  $wher["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
}
$wher["field"] = "id,first_name,display_name,user_email,user_mobile,created_date,user_id,role_id";
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
$wher["field"] = "id,user_activated,user_photo,display_name,first_name";
$userencodeurl = urlencode(json_encode($wher));
$userurlcat = API."?mode=gettabledetailswithconditions&table=application_users&data=".$userencodeurl;
$userures = callAPI($userurlcat);
$useruresponse = json_decode($userures,1);
$applicationusers = array();
foreach($useruresponse as $useruresponse){
  $applicationusers[$useruresponse['id']] = $useruresponse;
}

?>

<script type="text/javascript" src="../assets/js/custom_list_application_user_invitations.js"></script>
<div class="page-body">

  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header ">
          <h5>User Invitation List</h5>
          <div class="card-header-right table-card-header ">
          <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="0" checked class="listUserType" >
                        All
                      </label>
          </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="1" class="listUserType">
                        Active
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="Reg Invoit" type="radio" name="reg_invoit" value="2" class="listUserType">
                        Invited
                      </label>
                    </div>


<a href="index.php?option=COM_LIST_MULTI_APPLICATION_USER_INVITATIONS" class="btn btn-success mb-2 desktop-view <?php echo getPagePermision('COM_LIST_MULTI_APPLICATION_USER_INVITATIONS','','INSERT')['class']; ?>" <?php echo getPagePermision('COM_LIST_MULTI_APPLICATION_USER_INVITATIONS','','INSERT')['tooltip']; ?>>
            <i class="icofont icofont-plus icon-white text-white"></i>
            Invite User
          </a>
    </div>

            </div>

        <div class="row p-4">

          <?php
           $bg_color_array = array('bg-warning','bg-info');
            if(count((array)$invitationlists) !="0"){
            foreach ($invitationlists as $vendorslist){
              if($vendorslist['user_id'] != $_SESSION['userid']){
                $applicationuser = $applicationusers[$vendorslist['user_id']];
                $stausresult = $_SESSION['status'][$vendorslist['current_status']];
                        if($applicationuser['user_activated'] == "Y"){
                            $ribbon =  "Active";
                            $classadded = "register";
                  $tven =1;
                  $displayname =$applicationuser['display_name'];
                  $first_name = $applicationuser['first_name'];
                        }else{
                            $ribbon = "Invited";
                            $classadded = "invited";
                  $tven =0;
                  $displayname =$vendorslist['display_name'];
                  $first_name = $vendorslist['first_name'];
                        }
            ?>

      <div class="col-xl-3 col-md-12 custom-ribbon-accordian cursor-pointer  position-relative <?php echo $classadded; ?>" >

              <div class="ribbon ribbon-clip ribbon-right" style= "right: 0;">
                <div class="ribbon-target" style= "right: 3px;">
                  <span class="ribbon-inner" ></span> <?php echo $ribbon; ?>
                </div>
              </div>

              <div class="card analytic-card <?php echo $bg_color_array[$tven]; ?>" style="  rohan min-height:170px">
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
                         <?php if($tven == '1'){ ?>
       <a  href="index.php?option=COM_VIEW_PROFILE_APPLICATION_USERS&Rid=<?php echo deocdeId("id:".$vendorslist['user_id']); ?>" class="text-primary">
       <?php }?>
                        <h6 class="mb-1"><?php echo $displayname; ?></h6>
                         <?php if($tven == '1'){?>
</a>
<?php } ?>
                        <p class="text-white m-b-0  f-12"></p>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="row">
                <span class="col-lg-3 f-12 mb-1">Name :</span>
                <span class="col-lg-9 f-12"> <?php echo $first_name; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Since  :</span>
                <span class="col-lg-9 f-12"> <?php echo fromDate($vendorslist['created_date']); ?> </span>
                <span class="col-lg-3 f-12 mb-1">Email  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_email']; ?> </span>
                <span class="col-lg-3 f-12 mb-1">Phone  :</span>
                <span class="col-lg-9 f-12"> <?php echo $vendorslist['user_mobile']!=""?$vendorslist['user_mobile']:"-"; ?> </span>
              </div>
              <?php if($tven == '0'){ ?>
              <i class="ti-share float-right position-absolute text-white resendrequest" data-userid="<?php echo $vendorslist['user_id']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Re-Invite User" style="right: 100px;top: 10px;"></i>
            <?php } ?>
            <i class="ti-key float-right position-absolute text-danger reSentPassword" data-userid="<?php echo $vendorslist['user_id']; ?>"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Re-Set Password" style="right: 70px;top: 10px;"></i>
            </div>
          </div>

        </div>
        <?php $tven++;
        if($tven ==2){ $tven=0;
}
          }
        }
    }else{   ?>

 <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No User Invitation Available</p>
                                    <div class="text-center px-4">
                                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                  </div>
                              </div>

            <?php    } ?>
      </div>
    </div>
  </div>
</div>
</div>
<?php /**/ ?>
