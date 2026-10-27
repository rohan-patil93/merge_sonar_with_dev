<?php
if($_POST['ajaxpagecall'] == "Y"){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&ownership=".$_POST['ownership']."&resultgettype=".$_POST['resultgettype']."&allocation_status=".$_POST['allocation_status']."&parent_asset=".$_POST['parent_asset']."&category=".$_POST['category']."&location=".$_POST['location']."&asset_owner=".$_POST['asset_owner']."&status=".$_POST['status']."&candition=".$_POST['candition'];
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);

    $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
    $application_users = $details['application_users'];
    $asset_status= $details['asset_status'];
    $ass_condition = $details['asset_condition_type'];
    $assets = $details['assets'];

}
$user_assets = $details['user_assets'];
?>
<div class="card">
    <div class="card-header">
        <h5>Assets</h5>
        <div class="card-header-right">
            <span class="form-label label label-warning">
                <span class="d-inline-block" id="astcnt" >0</span>
                <span class="d-inline-block">- Asset Selected</span>
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="custom-scroll" style="height: 500px;">
        <?php
        if(count((array)$assets) != 0){ ?>
            <table  class="table table-hover tabel-sm m-b-0 align-middle" style="font-size:13px;">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Asset</th>
                        <th scope="col">Category</th>
                        <th scope="col">Location</th>
                        <th scope="col">Asset Status</th>
                        <th scope="col">Condition</th>
                        <th scope="col">Custodian Name</th>
                        <th scope="col">User Name</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($assets as $list_asset){  ?>
                <tr>
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
                    <td class="p-2 w-b-w-s">
                        <button type="button" class="btn btn-link text-primary p-0 border-0 align-baseline" onclick="AddAssets(<?php echo $list_asset['id']; ?>)">
                             <?php echo $list_asset['asset_description']; ?> -
                            <span class="f-12"><?php echo $list_asset['asset_code']; ?></span> <i class="ti-pencil-alt"></i>
                        </button>
                    </td>
                    <td class="p-2 w-b-w-s"><?php echo $assetcategory[$list_asset['category_id']]['category_description']; ?></td>
                    <td class="p-2"><?php echo $locations[$list_asset['asset_location']]['location_name']; ?></td>

                    <td class="p-2"><?php echo $asset_status[$list_asset['asset_status']]['name']; ?></td>
                    <td class="p-2"><?php echo $ass_condition[$list_asset['conditions']]['condition_name']; ?></td>
                    <td class="p-2 w-b-w-s"><?php echo $application_users[$list_asset['asset_custodian']]['display_name']; ?></td>
                    <td class="p-2"> <?php echo $user_assets[$list_asset['id']] !=""?$_SESSION['users'][$user_assets[$list_asset['id']]]:"-"; ?> </td>
                </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Assets</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
