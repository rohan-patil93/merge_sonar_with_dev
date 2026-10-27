<?php
$cat_id =2;
$option_c = $_GET['option'];
$RID_c = $_GET['Rid'];
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$curl = API."?mode=getcandidategenerateemployee&category_id=".$cat_id."&candidate_id=".$candidate_id;
$cdetails = callAPI($curl);
$cresult = json_decode($cdetails,1);
$vdata_details = $cresult['document_lists'];
$esign_details = $cresult['esgin_budget_approvals'];
$candidatesdetails= $cresult['candidates'];
$employees = $cresult['employees'];
$listemp = array();
foreach($employees as $employees3){
    $listemp[$employees3['id']] = $employees3;
}
$generateemp = $listemp[$candidatesdetails['employee_id']];
$pending_task = $cresult['pending_task'];
$taskerror = array();
foreach($pending_task as $ptask){
    if($ptask['yet_to_start_cnt'] !="0"){
        $taskerror['9111'][$ptask['assigned_to']] = $ptask['assigned_to'];
    }
    if($ptask['review_cnt'] !="0"){
        $taskerror['9112'][$ptask['approval_user_id']] = $ptask['approval_user_id'];
    }
}

?>
<script type="text/javascript">
function submitGenerateEmployee(submit_employee_type){
    var validations = ajaxFormSubmitValidation();
    $("#submit_employee").val(submit_employee_type);
    if(validations == 0){
        $("#formsubmit_generate_employee").submit();
    }
}
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="flex-row">
            <?php  include_once 'candidate_side_nav.php'; ?>

            <div class="flex-row-fluid ml-lg-8">
                <div class="row">
                    <div class="col-lg-12">
                        <form class="px-8 py-8" id="formsubmit_generate_employee" method="post" action=""  name="myForm" enctype="multipart/form-data">
                            <div class="card card-custom " id="kt_page_sticky_card">
                                <?php if(count((array)$taskerror) != 0){
                                        foreach($taskerror as $errokey =>$taskerr){
                                        ?>
                                    <div class="alert alert-danger border-danger alert-dismissible">
                                        <strong><?php echo $_SESSION['error_message'][$errokey]; ?> -
                                            <?php $mk =1; foreach($taskerr as $erroruser){ echo " ".$mk.") ".ucwords($_SESSION['users'][$erroruser]); $mk++;
} ?>
                                        </strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php }   $event_disabled = "event_disabled";
} ?>
                                <div class="card-header table-card-header">
                                    <h5>Generate Employee </h5>
                                    <div class="card-header-right table-card-header ">
                                        <?php if($candidatesdetails['employee_id']!=""){?>
                                            <span class="form-label label label-md label-primary">EMP - <?php echo $generateemp['emp_code']." - ".$generateemp['display_name'];?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-body  row <?php echo $event_disabled; ?>">
                                    <div class="form-group">
                                        <?php foreach ($vdata_details as $details) {?>
                                            <?php if(in_array($details['id'], (array)$checkl)){ ?>
                                                <div class="col-12 col-form-label">
                                                    <div class="checkbox-fade fade-in-primary">
                                                        <label class="form-label">
                                                            <input aria-label="Document[]" type="checkbox"   checked  disabled="disabled"name="document[]" value="<?php echo $details['id']; ?>" />
                                                            <span class="cr">
                                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                            </span>
                                                            <span>
                                                                <?php echo $details['document_name']; ?></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <?php if($_SESSION['user_type'] != 4){ ?>
                                                <div class="form-group col-lg-6" >
                                                    <label for="exampleDropdownFormEmail1" class="form-label">Email address</label>
                                                    <span class="d-block error-message text-danger"></span>
                                                    <input type="email" name="email" class="form-control requried_field" id="exampleDropdownFormEmail1" value="<?php echo $candidatesdetails['email_id']; ?>" placeholder="email@example.com"  readonly/>
                                                </div>
                                                <div class="form-group col-lg-6 ">
                                                    <label for="exampleDropdownFormEmail1" class="form-label">Employee Type</label>
                                                    <select aria-label="Etype" name="etype" class="form-control js-example-basic-single">
                                                        <option value="0">Select Any One</option>
                                                        <option value="1">Regular </option>
                                                        <option selected value="2">Contract </option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="exampleDropdownFormEmail1" class="form-label">Work Order</label>
                                                    <select aria-label="Workorder" name="workorder" class="form-control js-example-basic-single">
                                                        <option value="0">Select Any One</option>
                                                        <?php foreach($vendor_details as $vendor){ ?>
                                                            <option value="<?php echo $vendor['id']; ?>"><?php echo $vendor['transaction_number']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="exampleDropdownFormEmail1" class="form-label">ESign </label>
                                                    <select aria-label="Esign" name="esign" class="form-control js-example-basic-single">
                                                        <option value="0">Select Any One</option>
                                                        <?php foreach($esign_details as $esign){ ?>
                                                            <option value="<?php echo $esign['id']; ?>"><?php echo $esign['transaction_number']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 position-relative">
                                                    <div class="mt-2">
                                                        <span class="col-form-label">Admin Buddy </span>
                                                        <select aria-label="Admin Buddy" class="form-select js-example-basic-single " name="admin_buddy">
                                                            <option value="">Select Any One</option>
                                                            <?php foreach($employees as $employees1){?>
                                                                <option value="<?php echo $employees1['id']; ?>" <?php echo $generateemp['admin_buddy'] == $employees1['id']?"selected":""?>><?php echo $employees1['display_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 position-relative">
                                                    <div class="mt-2">
                                                        <span class="col-form-label">HR Buddy <span class="text-danger">*</span></span>
                                                        <span class="d-block error-message text-danger"></span>
                                                        <select aria-label="Hr Buddy" class="form-select js-example-basic-single requried_field" name="hr_buddy " required="">
                                                            <option value="">Select Any One</option>
                                                            <?php foreach($employees as $employees1){?>
                                                                <option value="<?php echo $employees1['id']; ?>" <?php echo $generateemp['hr_buddy'] == $employees1['id']?"selected":""?>><?php echo $employees1['display_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 position-relative">
                                                    <div class="mt-2">
                                                        <span class="col-form-label">Finance Buddy </span>
                                                        <select aria-label="Finance Buddy" class="form-select js-example-basic-single " name="finance_buddy">
                                                            <option value="">Select Any One</option>
                                                            <?php foreach($employees as $employees1){?>
                                                                <option value="<?php echo $employees1['id']; ?>" <?php echo $generateemp['finance_buddy'] == $employees1['id']?"selected":""?>><?php echo $employees1['display_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 position-relative">
                                                    <div class="mt-2">
                                                        <span class="col-form-label">Project Buddy </span>
                                                        <select aria-label="Project Buddy" class="form-select js-example-basic-single " name="project_buddy">
                                                            <option value="">Select Any One</option>
                                                            <?php foreach($employees as $employees1){?>
                                                                <option value="<?php echo $employees1['id']; ?>" <?php echo $generateemp['project_buddy'] == $employees1['id']?"selected":""?>><?php echo $employees1['display_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 position-relative">
                                                    <div class="mt-2">
                                                        <span class="col-form-label">Location Buddy </span>
                                                        <select aria-label="Loc Buddy" class="form-select js-example-basic-single " name="loc_buddy">
                                                            <option value="">Select Any One</option>
                                                            <?php foreach($employees as $employees1){?>
                                                                <option value="<?php echo $employees1['id']; ?>" <?php echo $generateemp['loc_buddy'] == $employees1['id']?"selected":""?>><?php echo $employees1['display_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if($_SESSION['user_type'] != 4){
                                        $disable = ""; if($candidatesdetails['employee_id']!=""){ $disable="disabled";
}
                                        ?>
                                        <div class="card-footer p-0 event_disabled">
                                            <div class="card-body">
                                                <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                                                <input type="hidden" name="user_type" value="1" />
                                                <input type="hidden" name="role_id" value="<?php echo $_SESSION['config']['default_role_id'];  ?>" />
                                                <input type="hidden" name="submit_create_employee" value="submit" />
                                                                                            <input type="hidden" name="submit_employee" id="submit_employee" value="UE" />

                                                <button type="button" class="btn btn-primary"  onclick="submitGenerateEmployee('E')" <?php echo $disable; ?> >Generate Employee </button>
                                                <button type="button" class="btn btn-primary m-l-20" onclick="submitGenerateEmployee('UE')" <?php echo $disable; ?>>Generate User AND Employee </button>

                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
