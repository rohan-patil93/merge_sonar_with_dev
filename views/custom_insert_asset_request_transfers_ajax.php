<?php
if($_POST['filterapply'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url1 = API."?mode=getassettransferrequests&admin_type=".$_SESSION['admin_type']."&rid=".$_POST['rid']."&categroy_list=".$_POST['categroy_list']."&asset_status=".$_POST['asset_status']."&asset_condition=".$_POST['asset_condition'];
    $data_details1 = callAPI($url1);
    $result = json_decode($data_details1,1);
}
$asset_requests = $result['asset_requests'];
$asset_categories = $result['asset_categories'];
$locations = $result['locations'];
$asset_status = $result['asset_status'];
$asset_condition_type = $result['asset_condition_type'];
$assets = $result['assets'];
?>
<?php
if(count((array)$assets) != "0"){
     foreach ($assets as $assdetails){
    $bgcolor = $assdetails['allocation_status']=="Y"?"bg-info":"";
 ?>
    <div class="card">
        <div class="row p-1 <?php echo $bgcolor; ?>">
            <div class="col-lg-3">
                <img  class="img-120"  src="<?php echo "uploads/assets/".$assdetails['asset_img']; ?>" alt="Attachment Preview" />
            </div>
            <div class="col-lg-7">
                <table>
                    <tr>
                        <th scope="col" style="width: 30%;">Asset Name</th><td style="width: 70%;"><?php echo $assdetails['asset_description']; ?></td>
                    </tr>
                    <tr>
                        <th scope="col">Category</th><td><?php echo $asset_categories[$assdetails['category_id']]['category_description']; ?></td>
                    </tr>
                    <tr>
                        <th scope="col">Status</th><td><?php echo $asset_status[$assdetails['asset_status']]['name']; ?></td>
                    </tr>
                    <tr>
                        <th scope="col">Return Date</th><td> - </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-2">
                <?php if($assdetails['allocation_status']=="N"){ ?>
                <div class="border-checkbox-section">
                      <div class="border-checkbox-group border-checkbox-group-primary">
                        <input class="border-checkbox select_asset_id" name="asset_id[]" type="checkbox" id="allocate_asset_id_<?php echo $assdetails['id']; ?>" value="<?php echo $assdetails['id']; ?>" onclick="selctAssetID('<?php echo $assdetails['id']; ?>')" onkeydown="">
                        <label class="form-label border-checkbox-label" for="allocate_asset_id_<?php echo $assdetails['id']; ?>" aria-label="Select Asset"></label>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
<?php } else { ?>
    <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert"></i> No Assets</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php  } ?>
