<?php
if(isset($_POST['ownership'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    if(ASSET_USER_ROLE == $_SESSION['working_role_short_code']){// employeeee
        $url = API."?mode=getassetallocation&list=Y&employeetype=Y&ownership=".$_POST['ownership']."&allocation_status=".$_POST['allocation_status']."&parent_asset=".$_POST['parent_asset']."&category=".$_POST['category']."&location=".$_POST['location']."&asset_owner=".$_POST['userid']."&status=".$_POST['status']."&candition=".$_POST['candition']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
    }else{
        $url = API."?mode=getassetallocation&list=Y&ownership=".$_POST['ownership']."&allocation_status=".$_POST['allocation_status']."&parent_asset=".$_POST['parent_asset']."&category=".$_POST['category']."&location=".$_POST['location']."&asset_owner=".$_POST['asset_owner']."&status=".$_POST['status']."&candition=".$_POST['candition']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
    }
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);
    $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
    $employees = $details['employees'];
    $users = $details['users'];
    $list_assets = $details['list_assets'];
    $ass_condition = $details['asset_condition_type'];
    foreach($details['asset_categories'] as $asset_category_list){
        $asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
    }
    foreach($details['locations'] as $locations_list){
        $locations_lists[$locations_list['id']] = $locations_list['location_name'];
    }
    foreach($details['asset_status'] as $asset_status){
        $ass_status[$asset_status['id']] = $asset_status['name'];
    }
    $allocation_type = array('1'=>'Create Assets','2'=>'Update Assets','3'=>'Allocation','4'=>'Asset accept','5'=>'Asset Reject','6'=>'Return Assets','7'=>'Transefer');
}

$asset_allocation_status= array('0'=>'allocate','1'=>'Accepted','2'=>'Rejected','3'=>'Return');
if(count((array)$list_assets) != 0){
if(in_array(ASSET_USER_ROLE,(array)$_SESSION['role_ids'])){ // employee screen
?>
<div class="dt-responsive table-responsive">
    <table id="basic-btn" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col">
                    <div class="checkbox-fade fade-in-primary">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Ectall Checks" type="checkbox" id="selectall_checks" class="itemRow" onclick="selectallcheck()" onkeydown="">
                            <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                        </label> All
                    </div>
                </th>
                <th scope="col">Asset Code</th>
                <th scope="col">Asset Name</th>
                <th scope="col">Categroty</th>
                <th scope="col">Location </th>
                <th scope="col">Asset Satus</th>
                <th scope="col">Condition</th>
                <th scope="col">End Date</th>
                <th scope="col">Remarks</th>
                <th scope="col">Status</th>

                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list_assets as $list_asset){
                ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td class="text-center">
                            <div class="checkbox-fade fade-in-primary" >
                                <label class="checkbox checkbox-success">
                                    <input aria-label="Checkbox" type="checkbox" class="itemRow checkboxassets" name="checkassets[<?php echo $list_asset['id']; ?>]" value="<?php echo $list_asset['id']; ?>" onchange="selectcheck()">
                                    <span class="cr">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td><button type="button" class="text-primary" onclick="AddAssets(<?php echo $list_asset['id']; ?>)"><?php echo $list_asset['asset_code']; ?></button></td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                    <td><?php echo $asset_category_lists[$list_asset['category_id']]; ?></td>
                        <td><?php echo $locations_lists[$list_asset['asset_location']]; ?></td>
                        <td><?php echo $ass_status[$list_asset['asset_status']]; ?></td>
                        <td><?php echo $ass_condition[$list_asset['conditions']]; ?></td>
                        <td><?php echo $list_asset['end_date']; ?></td>
                        <td><?php echo $list_asset['remarks']; ?></td>
                        <td><?php echo $list_asset['allocation_type'] != ""?'<span class="form-label label label-warning">'.$asset_allocation_status[$list_asset['allocation_type']].'</span>':""; ?>
                    </td>
                        <td>
                            <?php if($list_asset['allocation_type']==0){ ?>
                            <button class="btn btn-primary" type="button"  onclick="assetaccept('<?php echo $list_asset['id']; ?>','<?php echo $list_asset['emp_asset_id']; ?>')">View</button>
                        <?php }else{  ?>
                            <button class="btn btn-success" type="button"  onclick="extraassetattributesreqquest('<?php echo $list_asset['id']; ?>','<?php echo $list_asset['emp_asset_id']; ?>','<?php echo $list_asset['category_id']; ?>','<?php echo $list_asset['emp_attribute']; ?>')">Extra</button>
                        <?php } ?>
                        </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

<?php }else{

foreach($details['employees'] as $employee){
        $employe[$employee['id']] = $employee['display_name'];
    }

    ?>
<div class="dt-responsive table-responsive">
    <table id="basic-btn_2" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col">
                    <div class="checkbox-fade fade-in-primary">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Ectall Checks" type="checkbox" id="selectall_checks_2" class="itemRow" onclick="selectallcheck()" onkeydown="">
                            <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                        </label> All
                    </div>
                </th>
                <th scope="col">Asset Code</th>
                <th scope="col">Asset Name</th>
                <th scope="col">Categroty</th>
                <th scope="col">Location </th>
                <th scope="col">Allocated To</th>
                <th scope="col">Asset Satus</th>
                <th scope="col">Brand</th>
                <th scope="col">Model</th>
                <th scope="col">Serial Number</th>
                <th scope="col">Condition</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list_assets as $list_asset){

            $emp_assetdeatil = $emp_asset[$list_asset['id']];
            ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td class="text-center">
                            <div class="checkbox-fade fade-in-primary" >
                                <label class="checkbox checkbox-success">
                                    <input aria-label="Checkbox" type="checkbox" class="itemRow checkboxassets" name="checkassets[<?php echo $list_asset['id']; ?>]" value="<?php echo $list_asset['id']; ?>" onchange="selectcheck()">
                                    <span class="cr">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td><button type="button" class="text-primary" onclick="AddAssets(<?php echo $list_asset['id']; ?>)"><?php echo $list_asset['asset_code']; ?></button></td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                    <td><?php echo $asset_category_lists[$list_asset['category_id']]; ?></td>
                        <td><?php echo $locations_lists[$list_asset['asset_location']]; ?></td>
                        <td><?php echo $list_asset['user_assets_user_id'] != ""?'<span class="form-label label label-info">'.$_SESSION['users'][$list_asset['user_assets_user_id']].'</span>':""; ?> </td>
                        <td><?php echo $ass_status[$list_asset['asset_status']]; ?></td>
                        <td><?php echo $list_asset['brand']; ?></td>
                        <td><?php echo $list_asset['model']; ?></td>
                        <td><?php echo $list_asset['serial_no']; ?></td>
                        <td><?php echo $ass_condition[$list_asset['conditions']]; ?></td>
                        <td><?php echo $list_asset['allocation_type'] != ""?'<span class="form-label label label-warning">'.$asset_allocation_status[$list_asset['allocation_type']].'</span>':""; ?>
                    </td>

                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
<?php } ?>
<?php } else { ?>

<div class="card-px text-center ">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Assets</p>
    <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
</div>

<?php } ?>
