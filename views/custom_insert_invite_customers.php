<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit_invite'])){
 $_POST['multiinvite'] = "Y";
 $_POST['company_id'] = $_SESSION['company_id'];
 $_POST['data_name'] = $_SESSION['DB_NAME'];
 $_POST['status_date'] = nowDate();
 $_POST['user_id'] = $_SESSION['userid'];
$url = API."?mode=insertcustomerinvitation&method=post";
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
  <?php   header("location:index.php?option=COM_LIST_CUSTOMERS_INVITATIONS"); exit;
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
$role_idd =array(SUPERADMIN_ROLE,ADMIN_ROLE,CANDIDATE_ROLE);
foreach($application_roles as $rolesre){
    if(!in_array($rolesre['role_short_code'], $role_idd)){
        $roles[$rolesre['application_module']][] = $rolesre;
    }
}

?>
<script type="text/javascript">
   var maxvendors = 100;
function addMoreInvitation(){
    maxvendors = maxvendors+1;
    var rowid = 'rowdivision_'.$im;
    var errorvendorrowid = 'errorrowvendor_'+maxvendors;
    var vendorrowid = 'rowvendor_'+maxvendors;
    var email_id = "email_id";

    var htmlRows = '<div class="row mt-3" id="'+rowid+'">';

    htmlRows += '<div class="col-lg-2"><input aria-label="First Name[]" type="text" class="form-control" name="first_name[]" required="" ><input type="hidden" name="user_type[]" value="2"></div>';


    htmlRows += ' <div class="col-lg-2"><input aria-label="Last Name[]" type="text" class="form-control" name="last_name[]" required="" ></div>';

     htmlRows += '<div class="col-lg-3"><input aria-label="User Email[]" type="text" class="form-control allemails" name="user_email[]" data-errorcls="'+errorvendorrowid+'" id="'+vendorrowid+'"  onblur="checkEmailidAllReadyAvilableOrNot()" required="" ><span class="text-danger f-12 allemailerrer" id="'+errorvendorrowid+'"  style="display:none">Email Id Already Register</span></div>';


    htmlRows += '<div class="col-lg-2"><input aria-label="User Mobile[]" type="text" class="form-control" name="user_mobile[]" ></div>';

    htmlRows += '<div class="col-lg-2"><select aria-label="Role []"  class ="form-select js-example-basic-single" name="role_id[]" required="" > <?php foreach($module as $key=>$modulename){ ?> <optgroup label="<?php echo $modulename; ?> "><?php foreach($roles[$key] as $rolesr){  ?> <option value="<?php echo $rolesr['role_short_code']; ?>"><?php echo $rolesr['display_name']; ?></option> <?php
} ?> </optgroup> <?php
}  ?></select></div>';

     htmlRows += '<div class="col-lg-1 p-t-10"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup(\''+rowid+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';

    htmlRows += '</tr>';
    $('#append_invitatiouser').append(htmlRows);
     $(".js-example-basic-single").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
  function deleteRows(id){
      $(".delectedrow_"+id).remove();
  }

  function checkMailidAvailableOrNot(email_id,rowid){
    var usedemailids = $("#usedemailids").val();
    var data  = JSON.parse(usedemailids);
    $(".invitemailid").each(function(index) {
        var arrarowid = $(this).attr("data-rowid");
        if(rowid != arrarowid){
            data.push($(this).val());
        }
    });
    var emailid = $.trim(email_id);
    if ($.inArray(emailid, data) != -1)
    {
        $("#iduser_email_"+rowid).show();
        $("#inviteuser_btn").attr("disabled", true);
    }else{
        $("#iduser_email_"+rowid).hide();
        $("#inviteuser_btn").removeAttr("disabled");
    }
  }

  function checkEmailidAllReadyAvilableOrNot(){
    $(".allemailerrer").hide();
    $("#submituserbtn").removeClass("disabled");
    var poststop = 0;
    var posterrerdata = [];
    $(".allemails").each(function(index) {
      if($(this).val() !=""){
        var arrarowid = $(this).attr("data-errorcls");
        var emailval = $(this).val();
        if($.inArray(emailval, posterrerdata)>=0){
          $("#"+arrarowid).show();
          $("#submituserbtn").addClass("disabled");
          poststop = 1;
        }else{
           posterrerdata.push($(this).val());
        }
      }
    });
    if(poststop == '0'){
      $.ajax({
            url: "r_ajax_o.php",
            data:'page=checkemailidallreadyavilableornot&email_ids='+posterrerdata,
            type: "POST",
            success:function(data){
              var res = JSON.parse(data);
              var allreadyavailable = res.application_users;
              console.log(allreadyavailable);
              $(".allemails").each(function(index){
                  if($(this).val() !=""){
                    var arrarowid = $(this).attr("data-errorcls");
                    var emailval = $(this).val();
                    if($.inArray(emailval, allreadyavailable)>=0){
                      $("#"+arrarowid).show();
                      console.log("match - "+emailval);
                      $("#submituserbtn").addClass("disabled");
                    }else{
                      console.log("not match - "+emailval);
                    }
                  }
              });
            },
            error:function (){}
        });
    }
  }

function removeOrganizationSetup(idd){
    $("#"+idd).remove();
  }
</script>
<div class="page-body">

<div class="card">
    <div class="card-header mb-3">
        <h5>Invite Customers</h5>
    </div>
    <form method="post">
        <div class="card-body m-l-50 m-r-50">
          <div class="row">
              <div class="col-lg-2">
                <span class="form-label">First Name</span>
              </div>
              <div class="col-lg-2">
                <span class="form-label">Last Name</span>
              </div>
              <div class="col-lg-3">
                <span class="form-label">Email Id</span>
              </div>
              <div class="col-lg-2">
                <span class="form-label">Phone</span>
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

                <div class="col-lg-2"><input aria-label="First Name[]" type="text" class="form-control" name="first_name[]" required="" ><input type="hidden" name="user_type[]" value="2"></div>

                <div class="col-lg-2"><input aria-label="Last Name[]" type="text" class="form-control" name="last_name[]" required="" ></div>


                 <div class="col-lg-3">
                  <input aria-label="User Email[]" type="text" class="form-control allemails" name="user_email[]" data-errorcls="<?php echo $errorvendorrowid; ?>" id="<?php echo $vendorrowid; ?>" onblur="checkEmailidAllReadyAvilableOrNot()" required="" >
                  <span class="text-danger f-12 allemailerrer" id="<?php echo $errorvendorrowid; ?>"  style="display:none">Email Id Already Register</span>
                </div>

                <div class="col-lg-2">
                  <input aria-label="User Mobile[]" type="text" class="form-control" name="user_mobile[]"  >
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
                <div class="col-lg-1 p-t-10">
                  <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                </div>
              </div>
              <?php $im++;
} ?>
            </div>
            <div class="">
               <button class="btn btn-info btn-round waves-effect waves-light col-lg-3 fw-bold mt-4" id="addstatickRows" type="button" onclick="addMoreInvitation()">+ Add More Invite user</button>
           </div>
            <div class="">
            <button class="btn btn-info btn-round waves-effect waves-light col-lg-2 mt-5 disabled" id="submituserbtn" name="submit_invite" type="submit" >Invite Customer</button>
        </div>
    </div>
</form>
</div>
</div>

