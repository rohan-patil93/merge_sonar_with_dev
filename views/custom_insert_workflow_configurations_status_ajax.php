
<?php
if($_POST['ajax'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $geturl = API."?mode=getworkflowconfiguration&ajax=Y&rid=".$_POST['rid']."&process_type=".$_POST['process_type'];
    $getapi = callAPI($geturl);
    $getresult = json_decode($getapi,1);
    $workflowlist = $getresult['workflow_configuration'];
    $statusrow  = $getresult['status_progress'];
    $listfields = json_decode($getresult['display_attributes']['list_attributes'],1);
    $updatefields = json_decode($workflowlist['approve_update_status'],1);

}
if($_POST['approval_type'] =='role_levels' || $_POST['approval_type'] =='user_hierarchical' || $_POST['approval_type'] =='custom_process'){
    $_POST['sub_approve_type'] = $_POST['sub_approve_type']+1;
}else{
    $_POST['sub_approve_type'] = 2;
}
  $mk=0;
    for($i=1;$i <= $_POST['sub_approve_type'];$i++){
        $ulractionstatuslist = urldecode($updatefields[$mk]['action_status']);
        $actionstatuslist = json_decode($ulractionstatuslist,1);
        ?>
        <div class="form-group row">
            <div class="col-lg-1 mt-4">Level <span class="form-label badge bg-info "><?php echo $i; ?></span></div>
            <div class="col-lg-2  w-full">
                <span class="form-label d-block">Status</span>
                <select aria-label="Status[]" class="form-select js-example-basic-single" name="status[]" >
                <option value="">Select Any One</option>
                    <?php foreach($statusrow as $statusrows){ ?>
                        <option value="<?php echo $statusrows['status']; ?>" <?php echo $statusrows['status']==$updatefields[$mk]['status']?"selected":""; ?>><?php echo $statusrows['name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-lg-2  w-full">
                <span class="form-label d-block">Work Flow Field</span>
                <select aria-label="Work Flow Field[]" class="form-select js-example-basic-single" name="work_flow_field[]" >
                <option value="">Select Any One</option>
                    <?php  foreach($listfields as $fieldkey=>$ros){  ?>
                        <option value="<?php echo $fieldkey; ?>" <?php echo $fieldkey==$updatefields[$mk]['work_flow_field']?"selected":""; ?>><?php echo $ros['lable'];?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-3  w-full">
                <span class="form-label d-block">Action Status</span>
                <input type="hidden" name="action_status[]" id="action_status_<?php echo $mk; ?>" value='<?php echo $ulractionstatuslist; ?>'>
                <input type="hidden" name="action_status_old[]" id="action_status_old_<?php echo $mk; ?>" value='<?php echo $ulractionstatuslist; ?>'>
                <select aria-label="Select option" class="form-select js-example-basic-single" id="actionstatus_<?php echo $mk; ?>" onchange="selectActionStatus('<?php echo $mk; ?>')" >
                <option value="">Select Any One</option>
                    <?php foreach($statusrow as $statusrows){ ?>
                        <option value="<?php echo $statusrows['status']; ?>"> <?php echo $statusrows['name'];?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-4" >
                <span class="form-label d-block">Action Seq</span>
                <div id="view_action_status_<?php echo $mk; ?>">
                    <?php $num = 1; foreach($actionstatuslist as $actionstatuslists){
                        echo "<span class='form-label label label-md label-primary m-1'>".$num.":".$actionstatuslists."</span>";
                    $num++;
}?>
                </div>
            </div>
        </div>
    <?php $mk++;
} ?>
