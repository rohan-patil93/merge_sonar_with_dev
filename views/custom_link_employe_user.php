<?php
$empid = $_GET['newempid'];
$url = API."?mode=togetuserrolelist&empid=".$empid;
$cudata = callAPI($url);
$useedetails = json_decode($cudata,1);
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['Switch'] = 1;
$userdata = $empalldata['application_users'];
?>
<script type="text/javascript">
  function submit_role(){
    var role_id = $("#user_application_role").val();
    var newempidcode = $('#newempidcode').val();
    var newempid = $('#newempid').val();
    var approval_manager_id = $('#approval_manager_id').val();
    var reporting_manager_id = $('#reporting_manager_id').val();
    console.log("page=createuser&emp_id="+newempid+"&user_application_role="+role_id+"&approval_manager_id="+approval_manager_id+"&reporting_manager_id="+reporting_manager_id);
    jQuery.ajax({
      url: "ajax.php",
       data:'page=createuser&emp_id='+newempid+"&user_application_role="+role_id+"&approval_manager_id="+approval_manager_id+"&reporting_manager_id="+reporting_manager_id,
       type: "POST",
       success:function(data){
         console.log(data);
         window.location = "index.php?option=COM_LIST_EMPLOYEES";
       }
    });
  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5>Link Application User</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row col-lg-12">
          <div class="col-lg-4 mt-5">
            <span> Create User  : </span>
          </div>
          <div class="col-lg-2 mt-5">
            <span>
              <input aria-label="Upload Type" type="checkbox"  class="js-switch" value="1"  name="upload_type" id="upload_type" />
            </span>
          </div>
          <div class="row mt-3 col-lg-12">

            <div class="form-group col-lg-6">
              <span>Role </span>
              <select aria-label="User Application Role" class="js-example-basic-single" name="user_application_role" id="user_application_role">
                <option value=""> Select Role</option>
                <?php foreach($useedetails['roles'] as $role){ ?>
                  <option value="<?php echo $role['role_short_code'] ?>"><?php echo $role['display_name'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-6">
              <span>Approval Manager </span>
              <select aria-label="Approval Manager" class="js-example-basic-single" name="approval_manager_id" id="approval_manager_id">
                <option value=""> Select User</option>
                <?php foreach($useedetails['users'] as $user){ ?>
                  <option <?php if($userdata['approval_manager'] == $user['id']){ echo "selected";
}  ?> value="<?php echo $user['id'] ?>"><?php echo $user['display_name'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-6">
              <span>Reporting Manager </span>
              <select aria-label="Reporting Manager" class="js-example-basic-single" name="reporting_manager_id" id="reporting_manager_id">
                <option value=""> Select User</option>
                <?php foreach($useedetails['users'] as $user){ ?>
                  <option <?php if($userdata['reports_to'] == $user['id']){ echo "selected";
}  ?> value="<?php echo $user['id'] ?>"><?php echo $user['display_name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
         <div class="text-right p-3">
        <button class="btn btn-primary" onclick="submit_role()" >
          Save
        </button>
      </div>
      </div>
    </div>
  </div>
</div>
