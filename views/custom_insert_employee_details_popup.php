<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table_name = TBL_EMPLOYEES;
$url = API."?mode=getemployeeinfoforupdation&company_id=".$company_id."&employee_id=".$employee_id."&location_id=".$emp_loction_id."&role_id=".$emp_role_id."&is_active=0&application_tablename=".$table_name;
$response = callAPI($url);
$employee_basic_details = json_decode($response,1);
$dept_details = $employee_basic_details['dept_details'];
$designations_details = $employee_basic_details['designations_details'];
$application_users_details = $employee_basic_details['application_users_details'];
$employment_roles_details = $employee_basic_details['employment_roles_details'];
$locations_details = $employee_basic_details['locations_details'];
$emplevels_details = $employee_basic_details['emplevels_details'];
$employment_bands_details = $employee_basic_details['employment_bands_details'];
$verticals_details = $employee_basic_details['verticals_details'];
$blood_groups_details = $employee_basic_details['blood_groups_details'];
$marital_status_details = $employee_basic_details['marital_status_details'];
$gender_details = $employee_basic_details['gender_details'];
$emptypes_details = $employee_basic_details['emptypes_details'];
$edutypedata = $employee_basic_details['edutypedata'];
$relatypedata = $employee_basic_details['relatypedata'];
$banks = $employee_basic_details['banks'];
$relationtype = array();
foreach($relatypedata as $relatypedata){
    $relationtype[$relatypedata['id']]  = $relatypedata['relation_name'];
}

$department_id = filterArrayValue($dept_details,"id",$_POST['department_id']);
$reporting_manager_id = filterArrayValue($application_users_details,"id",$_POST['reporting_manager_id']);
$designation_id = filterArrayValue($designations_details,"id",$_POST['designation_id']);
$vertical_id = filterArrayValue($verticals_details,"id",$_POST['vertical_id']);
$role_id = filterArrayValue($employment_roles_details,"id",$_POST['role_id']);
$band_id = filterArrayValue($employment_bands_details,"id",$_POST['band_id']);
$level_id = filterArrayValue($emplevels_details,"id",$_POST['level_id']);

$location_id = filterArrayValue($locations_details,"id",$_POST['location_id']);
$employee_type = filterArrayValue($emptypes_details,"genc_detail_code",$_POST['employee_type']);
$gender = filterArrayValue($gender_details,"id",$_POST['gender']);
$blood_group = filterArrayValue($blood_groups_details,"id",$_POST['blood_group']);

//relation_name
$employee_details = $_POST;
$basicinfo = array('emp_code','display_name','first_name','middle_name','last_name','email_id','phone','address','post_code',);
$work = array('joining_date','department_id','reporting_manager_id','designation_id','vertical_id','hiring_source','role_id','experience_current_organization','total_experiance','relevant_experience','notice_period','band_id','level_id','location_id','employee_type','billable','office_phone','office_ext','fax');

$personal = array('date_of_birth','age','gender','blood_group','marital_status','wedding_date','soc_facebook','soc_twitter','soc_linkedin');

$bankdetails = array('bank_id','bank_link','bank_account_no');
?>
<script type="text/javascript">
    function submitdetails(){
        var formvalue =  $("#example-advanced-create-employee").serialize();
        var create_user =  '1';
        if($("#create_user").is(':checked')){
            var create_user =  '0';
        }
        var data_name = $('#data_name').val();
        $.ajax({
            url: "ajax.php",
            data: formvalue+"&page=insertemployeedetails&create_user="+create_user+"&data_name="+data_name,
            type: "POST",
            success:function(data){
                console.log(data);
                var returnres = JSON.parse(data);
                var biometric_code = '';
                if(returnres.biometric_code){
                    biometric_code = '\nBiometric Code : ' + returnres.biometric_code;
                }

                $('#kt_modal_dynamic_popupxxl').modal("hide");
                if(returnres.error = "1" && returnres.errer_name == "insert"){

                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: returnres.employee_create['message'],
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                    return false;

                }
                if(returnres.error = "1" && returnres.errer_name == "email"){

                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: 'Email Id Already Used :'+returnres.email_repeated,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                    return false;
                }
                if(returnres.error = "0"){

                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: 'Employee Successfully Inserted '+biometric_code+' - Do You Want Define The Salary',
                        type: "info",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=SAL&EMPID="+returnres.emp_id;
                        } else {
                            window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=SAL&EMPID="+returnres.emp_id;
                        }
                    });
                    setTimeout(function () {
                        window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=SAL&EMPID="+returnres.emp_id;
                    }, 5000);
                }
            }
        });

    }

</script>

<style>


</style>
<div class="modal-header">
    <h4 class="modal-title">View Employee details</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body j-pro">
    <div class="row">
        <div class="col-lg-12 col-xl-12 ">
            <div class="card-header  ">
                <h5>Basic Info </h5>
            </div>
            <div class="">
                <input type="hidden" name="data_name" id="data_name" value="<?php echo $_SESSION['DB_NAME']; ?>">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <?php $mk= 0; $rrd="";
                            foreach($employee_details as $key=>$employee_detail){
                                if(in_array($key, (array)$basicinfo)){
                                    if($key == "emp_code" && $_SESSION['emp_code_genrate']==1 ){

                                        $employee_detail = "Auto Generate";
                                    }
                             ?>
                                <th scope="row"><?php echo ucwords(strtolower(str_replace('_', ' ', $key))); ?></th>
                                <td><?php echo $employee_detail !=""?$employee_detail:"-"; ?></td>
                                <?php if($mk==1){ echo "</tr><tr>"; $mk=0;
}else{ $mk++;
}
}
} ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
        <div class="col-lg-12 col-xl-12 mt-5">
            <div class="card-header  ">
                <h5>Working Details </h5>
            </div>
            <table class="table m-0">
                <tbody>
                    <tr>
                        <?php $mk= 0; foreach($employee_details as $key=>$employee_detail){
                            if(in_array($key, (array)$work)){

if($key == "department_id"){ $employee_detail = $department_id[0]['department_name'];
}
if($key == "reporting_manager_id"){ $employee_detail = $reporting_manager_id[0]['display_name'];
}
if($key == "designation_id"){ $employee_detail =$designation_id[0]['designation_name'];
}
if($key == "vertical_id"){ $employee_detail =$vertical_id[0]['vertical_name'];
}
if($key == "role_id"){ $employee_detail =$role_id[0]['role_name'];
}
if($key == "band_id"){ $employee_detail =$band_id[0]['band_name'];
}
if($key == "level_id"){ $employee_detail =$level_id[0]['level_name'];
}
if($key == "location_id"){ $employee_detail =$location_id[0]['location_name'];
}
if($key == "employee_type"){ $employee_detail =$employee_type[0]['genc_detail_name'];
}
if($key == "billable"){ $employee_detail = $_POST['billable'] == '0'?"Billable":"Non-Billable";
}

                         ?>
                        <th scope="row"><?php echo ucwords(strtolower(str_replace('_', ' ', $key))); ?></th>
                        <td><?php echo $employee_detail !=""?$employee_detail:"-";
                         ?></td>
                        <?php if($mk==1){ echo "</tr><tr>"; $mk=0;
}else{ $mk++;
}
}
} ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-12 col-xl-12 mt-5">
            <div class="card-header  ">
                <h5>Personal Details</h5>
            </div>
            <table class="table m-0">
                <tbody>
                    <tr>
                        <?php $mk= 0; foreach($employee_details as $key=>$employee_detail){
                            if(in_array($key, (array)$personal)){

if($key == "blood_group"){ $employee_detail = $blood_group[0]['blood_group'];
}
if($key == "age"){ $employee_detail = dateyearcalucates($employee_details['date_of_birth']);
}
                         ?>
                        <th scope="row"><?php echo ucwords(strtolower(str_replace('_', ' ', $key))); ?></th>
                        <td><?php echo $employee_detail !=""?$employee_detail:"-"; ?></td>
                        <?php if($mk==1){ echo "</tr><tr>"; $mk=0;
}else{ $mk++;
}
}
} ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php if(EMPLOYEE_INSERT_FORM_BANK_DETAIL_INSERT == "Y"){ ?>
        <div class="col-lg-12 col-xl-12 mt-5">
            <div class="card-header  ">
                <h5>Bank Details</h5>
            </div>
            <table class="table m-0">
                <tbody>
                    <tr>
                        <?php $mk= 0; foreach($employee_details as $key=>$employee_detail){
                            if(in_array($key, (array)$bankdetails)){

if($key == "bank_id"){ $employee_detail = $banks[$employee_detail]['bank_name'];
}
                         ?>
                        <th scope="row"><?php echo ucwords(strtolower(str_replace('_', ' ', $key))); ?></th>
                        <td><?php echo $employee_detail !=""?$employee_detail:"-"; ?></td>
                        <?php if($mk==1){ echo "</tr><tr>"; $mk=0;
}else{ $mk++;
}
}
} ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
            <div class="col-lg-6 col-xl-6 mt-4 p-2">
                <div class="card-header  ">
                    <h5>Experience Details</h5>
                </div>
                <div class="">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th scope="col">Company Name</th>
                                <th scope="col">Job Descrption</th>
                                <th scope="col">Work From</th>
                                <th scope="col">Work To</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $exper =0; foreach($employee_details['company_name'] as $ky=>$valueexp)
                                { if($valueexp !=""){  $exper++;?>
                                    <tr>
                                        <td><?php echo $valueexp; ?></td>
                                        <td><?php echo $employee_details['job_descrption'][$ky]; ?></td>
                                        <td><?php echo $employee_details['work_from'][$ky]; ?></td>
                                        <td><?php echo $employee_details['work_to'][$ky]; ?></td>
                                    </tr>
                                <?php }
} if($exper=="0"){
                                        echo "<tr><td colspan='5' class='text-center'><h4><i class='ti-help-alt'></i> No Experience Details</h4></td</tr>";
                                    } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 mt-4 p-2">
                    <div class="card-header  ">
                    <h5>Qualification Details</h5>
                </div>
                <div class="">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th scope="col">Institute Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $qualifi = 0; foreach($employee_details['institute_name'] as $k2y=>$valueinstitute)
                                {  if($valueinstitute !=""){ $qualifi++;
$type = filterArrayValue($edutypedata,"genc_detail_code",$employee_details['education_type'][$k2y]);
                                 ?>
                                    <tr>
                                        <td><?php echo $valueinstitute; ?></td>
                                        <td><?php echo $type[0]['genc_detail_name']; ?></td>
                                        <td><?php echo $employee_details['start_date'][$k2y]; ?></td>
                                        <td><?php echo $employee_details['end_date'][$k2y]; ?></td>
                                        <td><?php echo $employee_details['score'][$k2y]; ?></td>
                                    </tr>
                                <?php }
} if($qualifi=="0"){
                                        echo "<tr><td colspan='5' class='text-center'><h4><i class='ti-help-alt'></i> No Qualification Details</h4></td</tr>";
                                    } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 mt-4 p-2">
                    <div class="card-header  ">
                    <h5>Dependents Details</h5>
                </div>
                <div class="">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Relationship</th>
                                <th scope="col">Date Of Birth</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $depend=0; foreach($employee_details['dependent_name'] as $ky3=>$valuedepende)
                                {  if($valuedepende !=""){  $depend++; ?>
                                    <tr>
                                        <td><?php echo $valuedepende; ?></td>
                                        <td><?php echo $relationtype[$employee_details['dependent_relationship_type'][$ky3]]; ?></td>
                                        <td><?php echo $employee_details['dependent_date_of_birth'][$ky3]; ?></td>
                                    </tr>
                                <?php }
} if($depend=="0"){
                                        echo "<tr><td colspan='3' class='text-center'><h4><i class='ti-help-alt'></i> No Dependents Details</h4></td</tr>";
                                    } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 mt-4 p-2">
                    <div class="card-header  ">
                        <h5>Emergency Contact</h5>
                    </div>
                    <div class="">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Relationship</th>
                                    <th scope="col">Home No</th>
                                    <th scope="col">Mobile No</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $emrg = 0; foreach($employee_details['contact_name'] as $ky4=>$valueemerg)
                                    { if($valueemerg !=""){ $emrg++; ?>
                                        <tr>
                                            <td><?php echo $valueemerg; ?></td>
                                            <td><?php echo $relationtype[$employee_details['contact_relationship'][$ky3]]; ?></td>
                                            <td><?php echo $employee_details['contact_home_no'][$ky3]; ?></td>
                                            <td><?php echo $employee_details['contact_mobile_no'][$ky3]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php }
                                    if($emrg=="0"){
                                        echo "<tr><td colspan='4' class='text-center'><h4><i class='ti-help-alt'></i> No Emergency Contact</h4></td</tr>";
                                    }

                                     ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Submit</button>
        </div>
