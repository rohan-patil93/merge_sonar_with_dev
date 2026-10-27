<?php
if($_POST['ajax'] == 'Y'){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getassetverifications&admin_type=".$_SESSION['admin_type']."&type=".$_POST['type'];
    $data_details = callAPI($url);
    $result = json_decode($data_details,1);
    $asset_verifications = $result['asset_verifications'];
    foreach($result['locations'] as $locations_list){
        $locations_lists[$locations_list['id']] = $locations_list['location_name'];
    }
}
?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <?php if(count((array)$asset_verifications) !="0"){ ?>
            <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
            <thead>
                <tr>
                    <th scope="col">Asset Code</th>
                    <th scope="col">Location</th>
                    <th scope="col">Asset Name</th>
                    <th scope="col">Owner</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($asset_verifications as $av){?>
                    <tr>
                        <td class="p-2"><?php echo $av['asset_code'];?></td>
                        <td class="p-2"><?php echo $locations_lists[$av['asset_location']];?></td>
                        <td class="p-2"><?php echo $av['asset_description'];?></td>
                        <td class="p-2"><?php echo $_SESSION['users'][$av['asset_owner']];?></td>
                        <td class="p-2"><?php echo fromDate($av['start_date_time']); ?></td>
                        <td class="p-2">
                            <?php if($av['current_status'] == ST_APPROVED){ ?>
                                <p class="mb-0">
                                    <span class="d-block">Date : <?php echo $av['verification_date_time'];?></span>
                                    <span>Note : <?php echo $av['user_note'];?></span>
                                </p>
                            <?php }else{ ?>
                              <button class="btn btn-primary cursor-pointer" onclick="assetVerification('<?php echo $av['id']; ?>')">verify</button>
                          <?php } ?>
                      </td>
                  </tr>
              <?php } ?>
          </tbody>
            </table>
        <?php }else{ ?>
        </div>
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ta ta-alert"></i> No Verifications</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
        <?php } ?>
    </div>
</div>
