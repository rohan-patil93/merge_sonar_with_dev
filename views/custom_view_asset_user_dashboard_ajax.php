<?php
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=USERDASH&pagetype=".$_POST['pagetype'];
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);
    $assets = $details['assets'];
    $application_users = $details['application_users'];
    $asset_status= $details['asset_status'];
    $ass_condition = $details['asset_condition_type'];
}
$statuse = ViewStatusData();
?>
<?php if($_POST['pagetype'] == "ASSETS_REQUEST"){
    $asset_requests = $details['asset_requests'];
    $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
?>
    <div class="card">
        <div class="card-header">
            <h5>Asset Requests</h5>
        </div>
        <div class="card-body">
            <div class="custom-scroll" style="height: 500px;">
            <?php
            if(count((array)$asset_requests) != 0){ ?>
                <table  class="table table-hover m-b-0" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Location</th>
                            <th scope="col">Delivery Type</th>
                            <th scope="col">Delivery Address</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Request Date</th>
                            <th scope="col">Approval User</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($asset_requests as $arequest){  ?>
                    <tr>
                        <td class="p-2"><?php echo $assetcategory[$arequest['category_id']]['category_description']; ?></td>
                        <td class="p-2"><?php echo $locations[$arequest['location_id']]['location_name']; ?></td>
                        <td class="p-2"><?php echo $arequest['delivery_type']=="D"?"Delivery":"Self"; ?></td>
                        <td class="p-2 w-b-w-s"><?php echo $arequest['delivery_address'] !=""?$arequest['delivery_address']:"-"; ?></td>
                        <td class="p-2 text-right"><?php echo $arequest['quantity']!=""?$arequest['quantity']:0; ?></td>
                        <td class="p-2"><?php echo $arequest['request_datetime']!=""?fromDate($arequest['request_datetime']):"-"; ?></td>
                        <td class="p-2"><?php echo $arequest['approval_user_id']!=""?$_SESSION['users'][$arequest['approval_user_id']]:"-"; ?></td>
                        <td class="p-2">
                            <span class="form-label label label-md" style="color:<?php echo $statuse[$arequest['current_status']]['color']; ?>; background-color:<?php echo $statuse[$arequest['current_status']]['background_color']; ?>;"><?php echo $statuse[$arequest['current_status']]['name']; ?></span>
                        </td>
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

<?php }elseif($_POST['pagetype'] == "REQUEST_INPROCESS"){
    $asset_request_transfers= $details['asset_request_transfers'];
    $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
 ?>
<div class="card">
    <div class="card-header">
        <h5>Request In-Process</h5>
    </div>
    <div class="card-body">
        <div class="custom-scroll" style="height: 500px;">
        <?php
        if(count((array)$asset_request_transfers) != 0){ ?>
            <table  class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th scope="col">Request Datetime</th>
                        <th scope="col">Code</th>
                        <th scope="col">Asset</th>
                        <th scope="col">Transfer Date</th>

                        <th scope="col">Delivery Type</th>
                        <th scope="col">Accept/Reject Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($asset_request_transfers as $auasset){  ?>
                <tr>
                    <td class="p-2"><?php echo $auasset['request_datetime']!=""?fromDate($auasset['request_datetime']):"-"; ?></td>
                    <td class="p-2"><?php echo $assets[$auasset['asset_id']]['asset_code']; ?></td>
                    <td class="p-2 w-b-w-s"><?php echo $assets[$auasset['asset_id']]['asset_description']; ?></td>

                    <td class="p-2"><?php echo $auasset['asset_transfer_date']!=""?fromDate($auasset['asset_transfer_date']):"-"; ?></td>

                    <td class="p-2"><?php echo $auasset['delivery_type']!="D"?"Delivery Partner":"Self"; ?></td>
                    <td class="p-2"><?php echo $auasset['asset_accept_or_reject_date']!=""?fromDate($auasset['asset_accept_or_reject_date']):"-"; ?></td>
                    <td class="p-2">
                        <span class="form-label label label-md" style="color:<?php echo $statuse[$auasset['current_status']]['color']; ?>; background-color:<?php echo $statuse[$auasset['current_status']]['background_color']; ?>;"><?php echo $statuse[$auasset['current_status']]['name']; ?></span>
                    </td>
                    <td class="p-2">
                        <?php if($auasset['current_status'] == 0){ ?>
                        <i class="ti ti-eye" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Return Request" onclick="assetRequestTransfersAcceptOrReject('<?php echo $auasset['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                    <?php }else{ echo "-";
} ?>
                    </td>
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

<?php }elseif($_POST['pagetype'] == "ASSET_RETURNS"){
    $asset_returns= $details['asset_returns'];
    $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
    ?>

<div class="card">
    <div class="card-header">
        <h5>Asset Return</h5>
    </div>
    <div class="card-body">
        <div class="custom-scroll" style="height: 500px;">
        <?php
        if(count((array)$asset_returns) != 0){ ?>
            <table  class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Asset</th>
                        <th scope="col">Request Datetime</th>
                        <th scope="col">Return Remark</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($asset_returns as $auasset){  ?>
                <tr>
                    <td class="p-2"><?php echo $auasset['asset_code']; ?></td>
                    <td class="p-2 w-b-w-s"><?php echo $auasset['asset_description']; ?></td>
                    <td class="p-2"><?php echo $auasset['return_date']!=""?fromDate($auasset['return_date']):"-"; ?></td>
                    <td class="p-2"><?php echo $auasset['description']!=""?$auasset['description']:"-"; ?></td>
                    <td class="p-2"><?php echo $auasset['approve_datetime']!=""?$auasset['approve_datetime']:"-"; ?></td>
                    <td class="p-2">
                        <span class="form-label label label-md" style="color:<?php echo $statuse[$auasset['current_status']]['color']; ?>; background-color:<?php echo $statuse[$auasset['current_status']]['background_color']; ?>;"><?php echo $statuse[$auasset['current_status']]['name']; ?></span>
                    </td>
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

<?php
}else{
    $user_assets= $details['user_assets'];
        $assetcategory = $details['asset_categories'];
    $locations = $details['locations'];
    $asset_returns  = $details['asset_returns'];
 ?>

<div class="card">
    <div class="card-header">
        <h5>Self Assets</h5>
    </div>
    <div class="card-body">
        <div class="custom-scroll" style="height: 500px;">
        <?php
        if(count((array)$user_assets) != 0){ ?>
            <table  class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Asset</th>
                        <th scope="col">Category</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">Stop Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($user_assets as $auasset){  ?>
                <tr>

                    <td class="p-2"><?php echo $auasset['asset_code']; ?></td>
                    <td class="p-2 w-b-w-s"><?php echo $auasset['asset_description']; ?></td>
                    <td class="p-2"><?php echo $assetcategory[$auasset['category_id']]['category_description']; ?></td>
                    <td class="p-2"><?php echo $auasset['start_date']!=""?fromDate($auasset['start_date']):"-"; ?></td>
                    <td class="p-2"><?php echo $auasset['end_date']!=""?fromDate($auasset['end_date']):"-"; ?></td>
                    <td class="p-2">
                        <span class="form-label label label-md" style="color:<?php echo $statuse[$auasset['current_status']]['color']; ?>; background-color:<?php echo $statuse[$auasset['current_status']]['background_color']; ?>;"><?php echo $statuse[$auasset['current_status']]['name']; ?></span>
                    </td>
                    <td class="p-2">
                        <?php if(isset($asset_returns[$auasset['asset_id']])){ echo "Return Process";
}else{ ?>
                         <i class="ti ti-unlink"  return request  onclick="assetReturnRequest('<?php echo $auasset['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                     <?php  } ?>
                    </td>
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

<?php } ?>
