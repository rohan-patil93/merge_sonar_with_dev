<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_invite'])){
 $_POST['multiinvite'] = "Y";
 $_POST['company_id'] = $_SESSION['company_id'];
 $_POST['data_name'] = $_SESSION['DB_NAME'];
 $_POST['status_date'] = nowDate();
 $_POST['user_id'] = $_SESSION['userid'];
    $_POST['display_name'] = $_POST['user_name'];
    $url = API."?mode=insertuserinvitation&table=".$table_name."&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    $response = $response1['application_users'];
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
    var mesg = "All User Invited Successfully";
    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
  }
}
</script>
  <?php   header("location:index.php?option=COM_LIST_APPLICATION_USER_INVITATIONS"); exit;
    }
}

$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'], 'module_id'=>$_SESSION['work_module_setup_id']);
$wh2["field"] = "id,module_id,module_name";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data2;
$moduleres = callAPI($url2);
$moduleres = json_decode($moduleres,1);
foreach($moduleres as $moduleres){
  $module[$moduleres['module_id']] = $moduleres['module_name'];
}
$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$wh2["field"] = "id,name,application_module,role_short_code,display_name";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=application_roles&data=".$data2;
$rolesres = callAPI($url2);
$rolesres = json_decode($rolesres,1);
$role_idd  =array(SUPERADMIN_ROLE,ADMIN_ROLE);

$application_roles =  $rolesres;
$role_idd =array(SUPERADMIN_ROLE,ADMIN_ROLE,CANDIDATE_ROLE,VENDOR_ROLE);
foreach($application_roles as $rolesre){
    if(!in_array($rolesre['role_short_code'], $role_idd)){
        $roles[$rolesre['application_module']][] = $rolesre;
    }
}

?>
<script type="text/javascript" src="../assets/js/custom_list_multi_application_user_invitations.js"></script>
<div class="page-body">

<div class="d-none" id="application_roles_html_option">
  <?php foreach($module as $key=>$modulename){ ?>
        <optgroup label="<?php echo $modulename; ?> ">
        <?php foreach($roles[$key] as $rolesr){  ?>
            <option value="<?php echo $rolesr['role_short_code']; ?>"><?php echo $rolesr['display_name']; ?></option>
       <?php  } ?>
        </optgroup>
      <?php  }  ?>
</div>
<div class="card">
    <div class="card-header mb-3">
        <h5>Invite Users</h5>
    </div>
    <form method="post">
        <div class="card-body m-l-50 m-r-50">
          <div class="row">
              <div class="col-lg-3">
                <span class="form-label">First Name</span>
              </div>
              <div class="col-lg-3">
                <span class="form-label">Last Name</span>
              </div>
              <div class="col-lg-3">
                <span class="form-label">Email Id</span>
              </div>
              <div class="col-lg-2">
                <span class="form-label">Role</span>
              </div>
            </div>
            <div id="append_invitatiouser">
              <?php $im=1;

            $vendors[] = array();

            foreach($vendors as $vend){
            $rowid = 'rowdivision_'.$im;
            $errorvendorrowid = 'errorrowvendor_'.$im;
            $vendorrowid = 'rowvendor_'.$im;
            ?>
              <div class="row" id="<?php echo $rowid; ?>">

                <div class="col-lg-3"><input aria-label="First Name[]" type="text" class="form-control" name="first_name[]" required="" autocomplete="off" value="<?php echo $_GET['UFNAME']; ?>"><input type="hidden" name="user_type[]" value="1"></div>

                <div class="col-lg-3"><input aria-label="Last Name[]" type="text" class="form-control" name="last_name[]" required="" autocomplete="off"></div>

                 <div class="col-lg-3">
                  <input aria-label="User Email[]" type="text" class="form-control allemails checkEmailidAllReadyAvilableOrNot" name="user_email[]" data-errorcls="<?php echo $errorvendorrowid; ?>" data-errorcls1="validrepeateemailerror_<?php echo $errorvendorrowid; ?>" id="<?php echo $vendorrowid; ?>"  required="" >
                  <span class="text-danger f-12 allemailerrer d-none" id="<?php echo $errorvendorrowid; ?>"  >Email Id Already Register</span>
                   <span class="text-danger f-12 allemailerrer d-none" id="validrepeateemailerror_<?php echo $errorvendorrowid; ?>"  >Please Enter Valid Email Id</span>
                </div>

                <div class="col-lg-2">
                   <select aria-label="Role []"  class ="form-select js-example-basic-single" name="role_id[]" required="" >
                      <?php foreach($module as $key=>$modulename){ ?>
                        <optgroup label="<?php echo $modulename; ?> ">
                        <?php foreach($roles[$key] as $rolesr){  ?>
                            <option value="<?php echo $rolesr['role_short_code']; ?>"><?php echo $rolesr['display_name']; ?></option>
                       <?php  } ?>
                        </optgroup>
                      <?php  }  ?>
                    </select>
                </div>
                <div class="col-lg-1">
                  <i class="ti-trash text-danger removeOrganizationSetup" data-rowid='<?php echo $rowid; ?>' data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" ></i>
                </div>
              </div>
          <?php $im++;
} ?>
            </div>

<div class="">
               <button class="btn btn-info btn-round waves-effect waves-light col-lg-3 fw-bold mt-4 addMoreInvitation" id="addstatickRows" type="button">+ Add More Invite user</button>
           </div>
<div class="">
            <button class="btn btn-info btn-round waves-effect waves-light col-lg-2 mt-5 disabled" id="submituserbtn" name="submit_invite" type="submit" >Invite Users</button>
        </div>
    </div>
</form>
</div>
</div>

