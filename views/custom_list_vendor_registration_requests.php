<?php
$url = API."?mode=getvendorregistrationrequests&admin_type=".$_SESSION['admin_type'];
$apicall = callAPI($url);
$result = json_decode($apicall,1);
$vendor_registration_requests = $result['vendor_registration_requests'];
$vendor_legal_application_users =  $result['vendor_legal_application_users'];
$vendor_registers = array();
foreach($result['vendor_registers'] as $vrd){
    $vendor_registers[$vrd['vendor_registration_request_id']] = $vrd['id'];
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

<script type="text/javascript">
    function permision(){
        console.log("workl");
    }
</script>

<link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendor_display_fields.css">
<div class="card">
    <div class="card-header ">
          <h5>Vendor Registration Requests</h5>
          <div class="card-header-right table-card-header ">
              <a href="index.php?option=COM_INSERT_VENDOR_REGISTRATION_REQUESTS" class="btn btn-success mb-2 desktop-view  <?php echo getPagePermision('','vendor_registration_requests','INSERT')['class']; ?>" <?php echo getPagePermision('','vendor_registration_requests','INSERT')['tooltip']; ?>>
            <i class="icofont icofont-plus icon-white text-white"></i>
            Vendor Registration Requests
          </a>
          </div>
      </div>
    <div class="card-body">
        <div class="row">
            <?php if(count((array)$vendor_registration_requests) !="0"){

                $permisions = "no-permission";
                if(getPagePermision('','vendor_registration_requests','INSERT')['permission'] == "Y" || getPagePermision('','vendor_registration_requests','VIEW')['permission'] == "Y" || getPagePermision('','vendor_registration_requests','UPDATE')['permission'] == "Y"){
                    $permisions = "";
                }
                foreach($vendor_registration_requests as $vrr){ ?>
                <div class="col-xl-3 col-md-3 custom-ribbon-accordian cursor-pointer  position-relative">
                    <div class="ribbon ribbon-clip ribbon-right right-0">
                        <?php if(isset($vendor_registers[$vrr['id']])){ ?>
                        <div class="ribbon-target bg-success right-3">
                              <span class="ribbon-inner" ></span> Invited
                        </div>
                        <?php }elseif($vrr['share_pdf']== "Y"){ ?>
                        <div class="ribbon-target bg-primary right-3">
                              <span class="ribbon-inner" ></span>  Registered
                        </div>
                        <?php }else{ ?>
                        <div class="ribbon-target bg-warning right-3">
                              <span class="ribbon-inner" ></span>  Active
                        </div>
                        <?php } ?>
                      </div>
                    <div class="card social-res-card">
                        <div class="card-header">
                            <h5><?php echo ucwords($vrr['company_name']); ?></h5>
                        </div>
                        <div class="card-body m-l-30  m-r-30">
                            <p class="m-b-5 f-w-600">Name</p>
                            <div class="m-b-15">
                                <span class=""><?php echo ucwords($vrr['display_name']); ?></span>
                            </div>
                            <p class="m-b-5 f-w-600">Email</p>
                            <div class="m-b-15">
                                <span class=""><?php echo $vrr['user_email']; ?></span>
                            </div>
                            <p class="m-b-5 f-w-600">Requested By</p>
                            <div class="m-b-15">
                                <span class="d-block"><?php echo $_SESSION['users'][$vrr['originator']]; ?></span>
                                <span class="d-block text-c-yellow"><?php echo $vrr['request_datetime']!=""?fromDate($vrr['request_datetime']):" Pending "; ?></span>
                            </div>

                            <div class=" mt-3 text-center">



                              <a href="index?option=COM_INSERT_VENDOR_REGISTRATION_REQUESTS&Rid=<?php echo deocdeId('id:'.$vrr['id']); ?>" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="<?php if($vrr['share_pdf'] == "Y"){ ?>View Registration Details<?php
}else{  ?>Edit Registration Details<?php
} ?>" class="<?php echo $permisions; ?>">
                                  <button class="btn btn-warning waves-effect waves-light btn-mini"> <?php if($vrr['share_pdf'] == "Y"){ ?>View<?php
}else{  ?>Edit<?php
} ?></button>
                              </a>

                            if($vrr['share_pdf'] =="Y" AND $_SESSION['admin_type'] == 1 AND !isset($vendor_registers[$vrr['id']])){ ?>
                            <a href="index?option=COM_INSERT_INVITE_VENDOR&VRRid=<?php echo deocdeId('id:'.$vrr['id']); ?>" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Invite Vendor" class="<?php echo getPagePermision('COM_INSERT_INVITE_VENDOR','','INSERT')['class']; ?>" <?php echo getPagePermision('COM_INSERT_INVITE_VENDOR','','INSERT')['tooltip']; ?>>
                                <button class="btn btn-success waves-effect waves-light btn-mini"><i class="ti-pencil-alt"></i> Invite</button>
                            </a>
                            <?php } ?>
                          </div>
                        </div>
                    </div>
                </div>
              <?php } ?>
              <?php } else { ?>
                <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Vendor Registration Requests  </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
                    </div>
                </div>

              <?php } ?>
        </div>
    </div>
</div>

