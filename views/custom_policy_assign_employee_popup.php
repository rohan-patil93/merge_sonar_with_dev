<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$empdata =array();
$ress =$_POST;
if($_POST['location']!=""){ $vdata1["where"]["location_id"] = $_POST['location'];
}
if($_POST['cost_center_pp']!=""){ $vdata1["where"]["cost_center_id"] = $_POST['cost_center_pp'];
}
if($_POST['emp_name_code']!=""){ $vdata1["where"]["emp_name_code"] = $_POST['emp_name_code'];
}
if($_POST['employee_type']!=""){ $vdata1["where"]["check_list"] = $_POST['employee_type'];
}
$vdata1["where"]["company_id"] = $_SESSION['company_id'];
$vdata1["where"]["is_active"] = 0;
$url = API."?mode=getuserreimbursementpolicies&method=post&policy_id=".$_POST['policy_number'];
$response = file_post_contents($url, $vdata1);
$empdata = json_decode($response,1);
$users_list = $empdata['users_list'];
$emploc = $empdata['locations'];
$empcos = $empdata['cost_centers'];
$user_reimbursement_policies = $empdata['user_reimbursement_policies'];
foreach($user_reimbursement_policies as $empesginda){
    $emp_sign[$empesginda['user_id']] = $empesginda['user_id'];
}

?>
<script type="text/javascript">
    function assignemployeelist(id){
        var table_name = document.getElementById('table_name').value;
        var location = document.getElementById('location').value;
        var cost_center_pp = document.getElementById('cost_center_pp').value;
        var emp_name_code= document.getElementById('emp_name_code').value;
        var policy_number = document.getElementById('policy_number').value;
        jQuery.ajax({
            url: "views/custom_policy_assign_employee_popup.php",
            data:'table='+table_name+'&workorder_id='+id+'&location='+location+'&cost_center_pp='+cost_center_pp+'&emp_name_code='+emp_name_code+'&policy_number='+policy_number,
            //data:'table='+table_name+'&workorder_id='+id+'&department='+department+'&designation='+designation+'&location='+location+'&vendor='+vendor+'&cost_center_pp='+cost_center_pp+'&employee_type='+employee_type+'&emp_name_code='+emp_name_code+'&policy_number='+policy_number,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal({ show: true});
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
            },
            error:function (){}
        });
    }
    function toinsertandpdateesignemployeeonlyone(id,uname,newcreate = ""){
        var error = 0;
        var message = '';
        var policy_number = document.getElementById('policy_number').value;
        $.ajax({
            url: "ajax.php",
            data:'page=assignemployeelistpolicy&policy_number='+policy_number+'&emp_id='+id+'&newcreate='+newcreate,
            type: "POST",
            success:function(data){
                console.log(data);
                var returnres = JSON.parse(data)
                console.log(returnres.other_policy);
                if(returnres.other_policy== 'Y'){
                    Swal.fire({
                        title: '<?php echo $_SESSION['error_message']['1001']; ?>',
                        icon: 'info',
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: 'Yes',
                        denyButtonText: 'No',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            toinsertandpdateesignemployeeonlyone(id,uname,"Y")
                        }else if (result.isDenied) {
                            $("#checkbox_"+id).prop('checked', false);
                        }
                    })
                }else{
                    if($("#checkbox_"+id).prop('checked') == true){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',uname+' Allocated to Policy','');
                    } else {
                        $("#checkbox_"+id).attr('checked', false);
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger','animated flipInX','animated flipOutX',uname+' Removed Form Policy','');
                    }
                }
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Assign Employees Policy</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row kt-margin-b-20 mb-2" >
        <div class="col-lg-3 form-group w-full">
            <span class="form-label d-block"><?php echo "Cost Center"; ?>:</span>
            <select aria-label="Cost Center Pp" class="form-control js-example-basic-single" id="cost_center_pp" name="">
                <option value=""> All  </option>
                <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
            <?php } ?>
        </select>
    </div>
<div class="col-lg-3  form-group w-full">
    <span class="form-label d-block"><?php echo "Location"; ?>:</span>
    <select aria-label="Location" class="form-control js-example-basic-single" id="location" name="">
        <option value=""> All  </option>
        <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
        <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
    <?php } ?>
</select>
</div>

<div class="col-lg-3 form-group">
    <span class="form-label d-block">Enter Usser Name/Email</span>
    <input aria-label="Emp Name Code" type="text" class="form-control" name="emp_name_code"  value="<?php echo $_POST['emp_name_code']; ?>" id="emp_name_code">
</div>
<div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
    <span>&nbsp;</span>
    <input type="hidden" name="submit" value="search" />
    <div>
        <button class="btn btn-primary btn-brand--icon mt-2" type="button" id="kt_search" onclick="assignemployeelist()">
            <span>
                <span>Search</span>
            </span>
        </button>
    </div>
</div>
</div>
<div class="separator separator-solid mb-3"></div>
<div class="dt-responsive table-responsive">
    <table class="table table-hover" id="kt_datatable1">
        <thead>
            <tr>
                <th scope="col">&nbsp;</th>
                <th scope="col">#</th>
                <th scope="col">Name </th>
                <th scope="col">Email Id </th>
                <th scope="col">Cost Center</th>
                <th scope="col">Location</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach ($users_list as $row){ ?>
                <tr>
                    <td class="p-2">
                        <label class="kt-checkbox">
                            <input aria-label="Input field" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['display_name']; ?>')" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>" type="checkbox" value="<?php echo $row['id']; ?>" onkeydown=""> <span> </span> </label>
                        </td>
                        <td class="p-2"><?php echo $i; ?></td>
                        <td class="p-2">
                            <div class="align-middle">
                                <?php if($row['user_photo']!="") {?>
                                    <img src="uploads/all/<?php echo $row['user_photo']; ?>"   class="img-radius img-50 align-top m-r-15" alt="User Profile">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <a href="#!" class="text-danger text-capitalize"><h6> <?php echo $row['display_name']; ?></h6></a>
                                    <p class="text-muted m-b-0"><?php echo $row['user_email']; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-2"><?php echo $row['user_email']; ?></td>

                        <td class="p-2"><?php echo $emp_cos[$row['cost_center_id']]; ?></td>
                        <td class="p-2"><?php echo $emp_loc[$row['location_id']]; ?></td>

                    </tr>
                    <?php $i++;
} ?>

                </tbody>
            </table>
        </div>
        <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
        <input type="hidden" name="table_name" id='table_name' value="<?php echo $_POST['table']; ?>">
        <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
        <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
        <input type="hidden" name="extra" id='extra' value="<?php echo $_POST['extra']; ?>">
        <input type="hidden" name="policy_number" id='policy_number' value="<?php echo $_POST['policy_number']; ?>">
    </div>
</div>

