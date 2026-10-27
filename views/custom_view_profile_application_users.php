<?php
$_SESSION['apply_js']['table']= 1;
if($_GET['Rid'] !=""){
    $rrid =  encodeId($_GET['Rid']);
    $rrid = urldecode($rrid);
    $rids = explode(":", $rrid);
    $rid = $rids['1'];
}else{
    $rid = $_SESSION['userid'];
}
$url = API."?mode=getapplicationusers&rid=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$audetail  = $result['application_users'];
$application_user_module_roles = $result['application_user_module_roles'];
$approval_data = $result['inbox_log'];
$auditrails = $result['auditrails'];
$inbox_already_call = 'Y';
$auditrail_already_call = 'Y';
$usermodule = $userrole =$notification_list= array();
foreach($application_user_module_roles as $aumr){
    $usermodule[$aumr['application_module_id']]  = $aumr['ms_display_name'];
    $userrole[$aumr['role_id']]  = $aumr['ar_display_name'];
}
$notifications = $result['notifications'];
foreach($notifications as $notf){
    if($notf['Notification_Read'] == "1"){
        $notification_list['READ'][]  = $notf;
    }elseif($notf['Notification_Type'] == "INFORMATION"){
        $notification_list['INFORMATION'][]  = $notf;
    }elseif($notf['Notification_Type'] == "ACTION"){
        $notification_list['ACTION'][]  = $notf;
    }
}
$profilereadiness = array("Basic Info"=>array('first_name','middle_name','last_name','user_email','user_mobile','cost_center','location_id','default_warehouse_id','country_id','default_state_id','timezone','default_currency_id','user_photo', 'status'),"Default Setting"=>array('shipping_address','shipping_post_code','shipping_country','shipping_state','shipping_city'));

?>
<script type="text/javascript">
    function editUserDetails(transactiontype=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#ajaxuserdetail').html(htmloader);
    var rid = '<?php echo $rid; ?>';
    jQuery.ajax({
        url: "views/custom_view_profile_application_user_ajax.php",
        data:'rid='+rid+"&transactiontype="+transactiontype+"&ajaxcall=Y",
        type: "POST",
        success:function(data){
            $("#ajaxuserdetail").html(data);
            $(".js-example-basic-single").select2();
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(transactiontype !=""){
                $("#chnagebtndiv").html('<button class="btn btn-danger" onclick="editUserDetails()"><i class="ti-close text-white"></i> Cancel</button>');
            }else{
                $("#chnagebtndiv").html('');
            }
        },
        error:function (){}
      });

}
function selectCounty(countydetail){
    var countydetail = countydetail.split("||");
    console.log(countydetail);
    var c_id = countydetail[0];
    var timezone = JSON.parse(countydetail[1]);
    $.ajax({
        url: "ajax.php",
        data:'page=getState&c_id='+c_id,
        type: "POST",
        success: function(response) {
            console.log(response);
            $("#getstate").html(response);
        }
    });
    var html ="";
    console.log(timezone);
    $.each(timezone, function( key, valu) {
        console.log(valu);
        html +="<option value='"+valu.zoneName+"'>"+valu.zoneName+"</option>";
    });
    $("#time_zone").html(html);
}
function dynamicSubmitForm(formtype){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
        var rid = '<?php echo $rid; ?>';
        var formData = new FormData();
        formData.append("page", "updateapplicationusers");
        formData.append("rid", rid);
        formData.append("formtype", formtype);
        var dataArray = $("#"+formtype).serializeArray(),
        dataObj = {};
        $(dataArray).each(function(i, field){
          formData.append(field.name, field.value);
        });
        var popattachment = document.getElementById('profile_user_photo').files[0];
        formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                var result = JSON.parse(data);
                console.log(result);
                var respons = result.respons;
                  if(respons.status =="fail"){
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',respons.message,'');
                }else{
                    if(respons.LASTID == '0'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    }
                    editUserDetails();
                }
            },
            error:function (){}
        });
    }
}
function submitRoleModuleForm(){
    var role_short_code = $("#role_short_code").val();
    var rid = '<?php echo $rid; ?>';
    $("#module_role_id_error").hide();
    if(role_short_code !=""){
        $.ajax({
        url: "ajax.php",
        data:'page=updateapplicationusers&role_short_code='+role_short_code+"&formtype=application_user_module_roles&rid="+rid,
        type: "POST",
        success: function(response){
            var result = JSON.parse(response);
            console.log(result);
            var respons = result.respons;
              if(respons.status =="fail"){
                tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',respons.message,'');
            }else{
                if(respons.LASTID == '0'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }
                editUserDetails('application_user_module_roles');
            }
        }
    });
    }else{
        $("#module_role_id_error").show();
    }
}
function removeRoleModule(module_role_id){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: '<?php echo $_SESSION['error_message']['9127']; ?> Module Role?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            var rid = '<?php echo $rid; ?>';
            $.ajax({
                url: "ajax.php",
                data:'page=updateapplicationusers&module_role_id='+module_role_id+"&formtype=remove_application_user_module_roles&rid="+rid,
                type: "POST",
                success: function(response){
                    var result = JSON.parse(response);
                    console.log(result);
                    var respons = result.respons;
                      if(respons.status =="fail"){
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',respons.message,'');
                    }else{
                        if(respons.LASTID == '0'){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                        }else{
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        }
                        editUserDetails('application_user_module_roles');
                    }
                }
            });
        }
    });


}
//             url: "ajax.php",
//             data: 'page=updateapplicationusers&formtype='+formtype+'&rid='+rid+'&'+formdata,
//             type: "POST",
</script>
<div class="card mb-1 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <div class="banner">
            <img src="assets/images/user-profile/bg-img1.jpg" class="w-100 b-radius-20" alt="">
        </div>
    </div>
    <div class="profile-img" style="margin:-40px 0 0 0;">
        <div class="row align-items-bottom">
            <div class="col-lg-1"></div>
            <div class="col-lg-4">
                <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
                    <form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
                        <label class="btn-hover-text-primary position-absolute  text-white" data-action="remove" data-bs-toggle="tooltip" title="Edit Profile Pic" style="left: -3px; top: -2px;">
                            <span><i class="ti-pencil-altj"></i></span>
                            <input aria-label="Profile Pic File" type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg"  onchange="updateProfilePic()" />
                        </label>
                        <?php if($audetail['user_photo']!=""){ ?>
                            <img src="<?php echo "uploads/all/".$audetail['user_photo']; ?>" class="img-radius img-90" alt="">
                        <?php  }else{ ?>
                            <div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
                                <span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40" style=" border-radius: 50%; line-height: 1.1;"><?php echo $audetail['display_name']!=""?initials($audetail['display_name']):"NA"; ?></span>
                            </div>
                        <?php } ?>
                         <span id="verified_btn">
                              <?php if($audetail['user_activated'] == 'Y' && $audetail['is_active'] == '0'){ ?>
                             <button type="button" class="btn waves-effect waves-light btn-success pt-1 pb-1 m-l-20" >        <i class="ti-check m-r-5"></i> Active
                             </button>
                         <?php }else{ ?>
                              <button type="button" class="btn waves-effect waves-light btn-warning pt-1 pb-1 m-l-20" ><i class="ti-alert m-r-5"></i> In-Active
                             </button>
                             <?php  } ?>
                         </span>
                    </form>
                </div>
                <div class="mt-3 d-block mb-3">
                    <div class="d-flex mr-3 mb-2">
                        <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600"><?php echo $audetail['display_name'];?></a>
                    </div>
                    <?php if($audetail['user_email']!=""){ ?>
                    <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                        <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $audetail['user_email'];?></a>
                    <?php }
if($audetail['user_mobile']!=""){  ?>
                        <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                            <i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo DynamicPhoneNumberFormt($audetail['user_mobile']);?></a>
                        <?php }  ?>
                    <div class="font-weight-bold text-dark-50 mt-1 f-14">
                        <?php if($audetail['reports_to']!=""){  ?>
                            <b class="f-w-600">Report Manager : </b><?php echo emptytextdashShow($_SESSION['users'][$audetail['reports_to']]); ?>&nbsp;
                        <?php }
if($audetail['company']!=""){  ?>
                            <b class="f-w-600">company : </b><?php echo $audetail['company']; ?>&nbsp;
                        <?php }  ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 p-3">
                <div class="row mt-5">
                    <div class="col-3 text-center">
                        <div class="s-caption  text-primary"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">HR Connect</span>
                            <p class="m-b-0 f-10"><?php echo emptytextdashShow($_SESSION['users'][$audetail['hr_connect']]); ?></p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Finance</span>
                            <p class="m-b-0 f-10"><?php echo emptytextdashShow($_SESSION['users'][$audetail['finance_connect']]); ?></p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Admin</span>
                            <p class="m-b-0 f-10"><?php echo emptytextdashShow($_SESSION['users'][$audetail['admin_connect']]); ?></p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-success"><i class=" ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Project</span>
                            <p class="m-b-0 f-10"> <?php echo emptytextdashShow($_SESSION['users'][$audetail['project_connect']]); ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-3 text-center">
                        <div class="s-caption"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Location</span>
                            <p class="m-b-0 f-10 "><?php echo emptytextdashShow($_SESSION['users'][$audetail['location_connect']]); ?> </p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">ESOP</span>
                            <p class="m-b-0 f-10 "><?php echo emptytextdashShow($_SESSION['users'][$audetail['esop_connect']]); ?> </p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Legal</span>
                            <p class="m-b-0  f-10"><?php echo emptytextdashShow($_SESSION['users'][$audetail['legal_connect']]); ?></p>
                        </div>
                    </div>
                    <div class="col-3 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <span class="f-w-700 m-b-0">Approval Manager</span>
                            <p class="m-b-0  f-10"><?php echo emptytextdashShow($_SESSION['users'][$audetail['approval_manager']]); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#overview_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Overview</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#notification_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Notification</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#approval_transactions" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approval Transactions</a>
                        <div class="slide"></div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#user_activity" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">User Activity</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#reset_password_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Reset Password</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#mobile_otp_tab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Mobile OTP</a>
                        <div class="slide"></div>
                    </li>

                </ul>
            </div>
            <div class="col-lg-4 text-right">
                <?php if($_SESSION['admin_type'] ==1){ ?>
                    <a href="index?option=COM_LIST_APPLICATION_USER_INVITATIONS" class="btn btn-primary waves-effect waves-light <?php echo getPagePermision('COM_LIST_APPLICATION_USER_INVITATIONS','','LIST')['class']; ?>" <?php echo getPagePermision('COM_LIST_APPLICATION_USER_INVITATIONS','','LIST')['tooltip']; ?>><i class="ti ti-user"></i> Invite User</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="tab-content card-block p-0">
    <div class="tab-pane" id="notification_tab" role="tabpanel">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Action Notification</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$notification_list['ACTION']) != 0){ ?>
                        <table id="basic-btn-non-1" class="table table-striped table-bordered nowrap">
                              <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Notification</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <?php $sq=1; foreach($notification_list['ACTION'] as $nlist){
                                      $option_url = $nlist['option'];
                                    if($nlist['option']==""|| $nlist['option']==null ){
                                        $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$nlist['Notification_Table'],1);
                                        $option_url = $tabledata['insert_page_name'];
                                    }
                                      $nrid = "id:".$nlist['option_id'];
                                    $link = "index.php?option=".$option_url."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($nlist['id']);
                                      ?>
                                    <tr>
                                        <td class="f-2" style="width: 10%;"><?php echo $sq; ?></td>
                                        <td class="f-2 w-b-w-s" style="width: 90%;"><a href="<?php echo $link; ?>"><?php echo $nlist['Notification_Message']; ?>
                                        <small class="f-w-200 d-block"><?php echo notificationdatesdiff($nlist['Notification_DateTime']); ?></small></a>
                                    </td>
                                <?php $sq++;
} ?>
                                  </tr>
                              </tbody>
                        </table>
                    <?php }else{  ?>
                        <div class="card-body">
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Action  Notification</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Information Notification</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$notification_list['INFORMATION']) != 0){ ?>
                        <table id="basic-btn-non-2" class="table table-striped table-bordered nowrap">
                              <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Notification</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <?php $sq=1; foreach($notification_list['INFORMATION'] as $nlist){ ?>
                                    <tr>
                                        <td class="f-2" style="width: 10%;"><?php echo $sq; ?></td>
                                        <td class="f-2 w-b-w-s" style="width: 90%;"><?php echo $nlist['Notification_Message']; ?>
                                        <small class="f-w-200 d-block"><?php echo notificationdatesdiff($nlist['Notification_DateTime']); ?></small>
                                    </td>
                                <?php $sq++;
} ?>
                                  </tr>
                              </tbody>
                        </table>
                    <?php }else{  ?>
                        <div class="card-body">
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Notification</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Read Action</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$notification_list['READ']) != 0){ ?>
                        <table id="basic-btn-non-3" class="table table-striped table-bordered nowrap">
                              <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Notification</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <?php $sq=1; foreach($notification_list['READ'] as $nlist){ ?>
                                    <tr>
                                        <td class="f-2" style="width: 10%;"><?php echo $sq; ?></td>
                                        <td class="f-2 w-b-w-s" style="width: 90%;"><?php echo $nlist['Notification_Message']; ?>
                                        <small class="f-w-200 d-block"><?php echo notificationdatesdiff($nlist['Notification_DateTime']); ?></small>
                                    </td>
                                <?php $sq++;
} ?>
                                  </tr>
                              </tbody>
                        </table>
                    <?php }else{  ?>
                        <div class="card-body">
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Read Notification</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="approval_transactions" role="tabpanel">
        <?php include_once 'views/custom_list_inbox.php'; ?>
    </div>

    <div class="tab-pane" id="user_activity" role="tabpanel">
        <?php include_once 'views/custom_view_audittrails.php'; ?>
    </div>
    <div class="tab-pane active" id="overview_tab" role="tabpanel">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Profile completion</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-md">
                            <tbody>
                                <?php foreach($profilereadiness as $keylabel=>$profilereadines){
                                    $complete_field =  $total_field = 0;
                                    $total_field = count((array)$profilereadines);
                                    foreach($profilereadines as $profilereadines){
                                        if($audetail[$profilereadines] != ""){
                                            $complete_field++;
                                        }
                                    }
                                    $percentage = (($complete_field/$total_field)*100);
                                    ?>
                                    <tr>
                                        <th scope="col">
                                            <h6 class="mb-0 f-14"><?php echo $keylabel; ?></h6>
                                        </th>
                                        <th scope="col" style="text-align: right;">
                                            <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
                                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                    <span class="f-12 f-w-600">Profile </span>
                                                    <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5>Overview</h5>
                        <div class="card-header-right" id="chnagebtndiv">
                        </div>
                    </div>
                    <div class="card-body" id="ajaxuserdetail">
                        <?php include_once 'views/custom_view_profile_application_user_ajax.php';?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="reset_password_tab" role="tabpanel">
        <?php include_once 'views/custom_change_password.php';?>
    </div>
    <div class="tab-pane" id="mobile_otp_tab" role="tabpanel">
        <?php include_once 'views/custom_view_mobile_otp.php';?>
    </div>
</div>
