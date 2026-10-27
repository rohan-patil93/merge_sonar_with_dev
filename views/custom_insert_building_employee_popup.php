<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$emp_id = $_POST['emp_id'];
$url = API."?mode=getequipmentallocations&pagetype=getemployeedetails&emp_id=".$emp_id;
$response = callAPI($url);
$result = json_decode($response,1);
$employees_list = $result['employees_list'];
$departments = $result['departments'];
$locations = $result['locations'];
$employees = $result['employees'];
$location_buddy_employees_list = $result['location_buddy_employees_list'];
$employees_list['finance_buddy'] = $employees_list['finance_buddy']!=""?$employees_list['finance_buddy']:$_SESSION['emp_id'];

?>
<script type="text/javascript">
    function insertEmployeeDetails(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var formData = new FormData();
            formData.append("page", "insertemployeepopup");
            var dataArray = $("#employee_popup_form").serializeArray(),
            dataObj = {};
            $(dataArray).each(function(i, field){
                formData.append(field.name, field.value);
            });
            var user_image = document.getElementById('user_image').files[0];
            formData.append("user_image",user_image);
            jQuery.ajax({
                url: "ajax.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
                success:function(response){
                    console.log(response);
                    var result = JSON.parse(response);
                    if(result['status'] == "fail"){
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", result['message'], "error");
                    }else{
                      if(result['LASTID'] == "0"){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                        }else{
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        }
                        $('#kt_modal_dynamic_popup').modal("hide");
                        closeInsertEmployees();
                    }
                }
            });
        }
    }
    function getEmployeeEmailIdAlreadyAvailableOrNot(email_id,emp_id){
        if(email_id !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=getemployeeemailidalreadyavailableornot&email_id='+email_id+'&emp_id='+emp_id,
                type: "POST",
                async : false,
                success:function(data){
                    var res = JSON.parse(data);
                    if(res.res == 'true'){
                        $("#emailiderror").show();
                        $("#insertemployeedetails_btn").addClass("disabled");
                    }else{
                        $("#emailiderror").hide();
                        $("#insertemployeedetails_btn").removeClass("disabled");
                    }
                }
            });
        }else{
            $("#insertemployeedetails_btn").removeClass("disabled");
        }
    }
    function closeInsertEmployees(){
        var urlParams = new URLSearchParams(window.location.search);
        var param_value = "B";
        if (urlParams.has('TAB1')) {
            param_value = urlParams.get('TAB1');
            param_value = (param_value !=null || param_value !="")?param_value:"B";
        }
        getBuildingDynamicPage(param_value);
    }

    function employeeInactiveupdates(){
        if ($("#employeeInactiveupdate").is(":checked")) {
            $("#employeeInactiveupdate_text").text('');
        }else{
            $("#employeeInactiveupdate_text").text('Are you sure this employee is inactive/disabled? All space allocations will also be removed. Please Update.');
        }
    }

</script>
<div class="modal-header  ">
    <h5 class="modal-title"> Employee</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeInsertEmployees()"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <form id="employee_popup_form">
        <input type="hidden" name="emp_id" value='<?php echo $emp_id; ?>'>
        <div class="row">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-xl-4 form-group" >
                        <span class="form-label d-block">Emp Code <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Emp Code" class="form-control requried_field" type="text" name="emp_code" value="<?php echo $employees_list['emp_code']; ?>"/>
                   </div>
                    <div class="col-xl-4 form-group" >
                        <span class="form-label">First Name <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="First Name" class="form-control requried_field" type="text" name="first_name" value="<?php echo $employees_list['first_name']; ?>"/>
                   </div>
                   <div class="col-xl-4 form-group" >
                        <span class="form-label  d-block">Middle Name</span>
                        <input aria-label="Middle Name" class="form-control" type="text" name="middle_name" value="<?php echo $employees_list['middle_name']; ?>"/>
                   </div>
                   <div class="col-xl-4 form-group" >
                        <span class="form-label  d-block">Last Name <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Last Name" class="form-control requried_field" type="text" name="last_name" value="<?php echo $employees_list['last_name']; ?>"/>
                   </div>
                   <div class="col-xl-4 form-group" >
                        <span class="form-label  d-block">Email <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Email" class="form-control requried_field" type="text" name="email_id" value="<?php echo $employees_list['email_id']; ?>" onblur="getEmployeeEmailIdAlreadyAvailableOrNot(this.value,'<?php echo $emp_id; ?>')"/>
                         <span class="text-danger f-12" id="emailiderror"  style="display:none">Email Id Already Register</span>
                   </div>
                   <div class="col-xl-4 form-group" >
                        <span class="form-label  d-block">Contact </span>

                        <input aria-label="Phone" class="form-control" type="text" name="phone" value="<?php echo $employees_list['phone']; ?>"/>
                   </div>
                   <div class="col-xl-4 form-group w-full" >
                        <span class="form-label  d-block">Department </span>
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="Department" class="js-example-basic-single" name="department_id" >
                            <option value="">Select Any One</option>
                            <?php foreach($departments as $deprt){ ?>
                            <option value="<?php echo $deprt['id']; ?>" <?php echo $deprt['id']==$employees_list['department_id']?"selected":""; ?>><?php echo $deprt['department_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">Location <span class="text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <select aria-label="Location" class="js-example-basic-single requried_field" name="location_id" >
                            <option value="">Select Any One</option>
                            <?php foreach($locations as $loc){ ?>
                            <option value="<?php echo $loc['id']; ?>" <?php echo $loc['id']==$employees_list['location_id']?"selected":""; ?>><?php echo $loc['location_name']; ?></option>
                            <?php } ?>
                        </select>
                   </div>
                    <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">Address </span>
                        <textarea aria-label="Address"  class=" form-control form-control-lg" rows="5" cols="100" name="address"><?php echo $employees_list['address']; ?></textarea>
                   </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">Reporting Manager </span>
                        <select aria-label="Reporting Manager" class="js-example-basic-single" name="reporting_manager_id" >
                            <option value="">Select Any One</option>
                            <?php foreach($employees as $empp){?>
                                <option value="<?php echo $empp['id']; ?>" <?php echo $empp['id']==$employees_list['reporting_manager_id']?"selected":""; ?>><?php echo $empp['display_name']; ?></option>
                            <?php } ?>
                        </select>
                   </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label d-block">Location Buddy <?php if($_SESSION['working_role_short_code'] =="34"){ ?><span class="text-danger">*</span> <?php
} ?></span>
                        <?php if($_SESSION['working_role_short_code'] =="34"){ ?><span class="d-block error-message text-danger"></span><?php
} ?>
                        <select aria-label="Select option" class="js-example-basic-single <?php if($_SESSION['working_role_short_code'] =="34"){ ?>requried_field <?php
} ?>" name="loc_buddy" >
                            <option value="">Select Any One</option>
                            <?php foreach($location_buddy_employees_list as $empp){ ?>
                                <option value="<?php echo $empp['id']; ?>" <?php echo $empp['id']==$employees_list['loc_buddy']?"selected":""; ?>><?php echo $empp['display_name']; ?></option>
                            <?php } //} ?>
                        </select>
                   </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">HR Buddy </span>
                        <select aria-label="Hr Buddy" class="js-example-basic-single" name="hr_buddy" >
                            <option value="">Select Any One</option>
                            <?php foreach($employees as $empp){?>
                                <option value="<?php echo $empp['id']; ?>" <?php echo $empp['id']==$employees_list['hr_buddy']?"selected":""; ?>><?php echo $empp['display_name']; ?></option>
                            <?php } ?>
                        </select>
                   </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">Admin Buddy </span>
                        <select aria-label="Admin Buddy" class="js-example-basic-single" name="admin_buddy" >
                            <option value="">Select Any One</option>
                            <?php foreach($employees as $empp){?>
                                <option value="<?php echo $empp['id']; ?>" <?php echo $empp['id']==$employees_list['admin_buddy']?"selected":""; ?>><?php echo $empp['display_name']; ?></option>
                            <?php } ?>
                        </select>
                   </div>
                   <div class="col-xl-4 form-group  w-full" >
                        <span class="form-label  d-block">Finance Buddy </span>
                        <select aria-label="Finance Buddy" class="js-example-basic-single" name="finance_buddy" >
                            <option value="">Select Any One</option>
                            <?php foreach($employees as $empp){?>
                                <option value="<?php echo $empp['id']; ?>" <?php echo $empp['id']==$employees_list['finance_buddy']?"selected":""; ?>><?php echo $empp['display_name']; ?></option>
                            <?php } ?>
                        </select>
                   </div>
                   <div class="col-xl-4 form-group  w-full <?php echo $employees_list['is_active'] == "0"?"":"event_disabled"; ?>" >
                        <span class="form-label  d-block">Employee Status</span>
                        <input aria-label="Is Active" type="checkbox" class="js-single-switch" name="is_active" id="employeeInactiveupdate" value="Y" onchange="employeeInactiveupdates()" checked >
                        <span id="employeeInactiveupdate_text" class="text-danger d-block"></span>
                    </div>
               </div>
            </div>
            <div class="col-lg-2">
                <span class="form-label">Profile Pic</span>
                <div class="col-lg-9 col-xl-6">
                    <div class="profile-pic">
                        <label class="-label" for="user_image">
                            <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                        </label>
                        <input type="file" id="user_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                        <img src="<?php echo "uploads/all/".$employees_list['image'] ; ?>" id="outputuser_image" width="200" alt="User Profile" />
                        <input type="hidden" name="old_image" value="<?php echo $employees_list['image'] ;  ?>">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="closeInsertEmployees()">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="insertemployeedetails_btn" onclick="insertEmployeeDetails()"><?php echo $emp_id !=""?"Update":"Submit"; ?> </button>
</div>
