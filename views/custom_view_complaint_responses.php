<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'complaints';
    $url = API . "?mode=getComplaintsData&method=get&rid=$rid";
    $che = callAPI($url);
    $return = json_decode($che, 1);
    $return = $return['table'];


    $table_name = 'complaint_responses';
    $column = 'complaint_id';
    $url = API . "?mode=getComplaintsResponseData&method=get&table=$table_name&id=$rid&column=$column";
    $result = callAPI($url);
    $result = json_decode($result, 1);
    $result = $result['table'];
    $rcount = count($result);


    $table_name = 'complaints';
    $column = 'complaint_image';
    $url = API."?mode=getImagesData&method=get&table=$table_name&id=$rid&column=$column";
    $img_result = callAPI($url);
    $img_result = json_decode($img_result, 1);
    $img_result = $img_result['table'];

}
?>
<div class="page-body">

    <div class="card proj-progress-card">
        <div class="card-header">
            <h5>Complaint Response View</h5>

            <div class="card-header-right">
                <a href="index.php?option=COM_LIST_COMPLAINTS" class="btn btn-success mb-2 desktop-view">

                Complaints List</a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <div class="row">
              <div class="card-body">
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab0" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Complaint Details</a>
                    </li>
                    <?php for($i=1; $i <= $rcount; $i++) {
                        echo '<li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab' . $i . '" role="tab" onkeypress="if(event.key===\'Enter\'||event.key===\' \'){this.click();}">Response ' . $i . '</a>
                        </li>';
                    } ?>


                </ul>
                <div class="tab-content tabs mt-4">
                    <div class="tab-pane active" id="tab0" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th scope="col">Complaint Name</th>
                                    <td><?php echo $return['complaint_name'] ?></td>
                                </tr>
                                <tr>
                                    <th scope="col">Complaint Type</th>
                                    <td><?php echo $return['complaint_type'] ?></td>
                                </tr>
                                <tr>
                                    <th scope="col">Complaint Priority</th>
                                    <td style="font-weight: 900; font-size: 1rem;<?php
                                    if($return['complaint_priority']=='L'){
                                        echo 'color: green;';
                                    } elseif($return['complaint_priority']=='M'){
                                        echo 'color: yellow; ';
                                    } else {
                                        echo 'color: red;';
                                    }
                                ?>">
                                <?php
                                if($return['complaint_priority']=='L'){
                                    echo 'Low';
                                } elseif($return['complaint_priority']=='M'){
                                    echo 'Medium';
                                } else {
                                    echo "High";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="col">Date</th>
                            <td><?php
                            echo !empty($return['transaction_date'])?fromDate($return['transaction_date']): ''; ?></td>
                        </tr>
                        <?php if(isset($return['item_name']) && $return['item_name'] !='' ): ?>
                            <tr>
                                <th scope="col">Item</th>
                                <td><?php echo $return['item_name'] ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(isset($return['asset_code']) && $return['asset_code'] !='' ): ?>
                            <tr>
                                <th scope="col">Asset</th>
                                <td><?php echo $return['asset_code']."-".$return['asset_description'] ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(isset($return['building_name']) && $return['building_name'] !='' ): ?>
                            <tr>
                                <th scope="col">Building</th>
                                <td><?php echo $return['building_name'] ?></td>
                            </tr>
                            <tr>
                                <th scope="col">Building Floor</th>
                                <td><?php echo $return['floor_name'] ?></td>
                            </tr>
                            <tr>
                                <th scope="col">Building Floor Space</th>
                                <td><?php echo $return['cabin_name'] ?></td>
                            </tr>
                        <?php endif; ?>


                            <tr>
                                <th scope="col">Status</th>
                                <td><span class="form-label label label-lg  label-primary label-inline f-w-900 " style="background-color:<?php echo $return['background_color']; ?> ; color:<?php echo $return['color']; ?> ;"><?php echo $return['name']; ?></span> </td>
                            </tr>
                            <tr>
                                <th scope="col">Complaint User</th>
                                <td><?php echo $return['display_name'] ?? '' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">Created Time</th>
                                <td><?php echo  $return['created_date'] ?? '' ?></td>
                            </tr>
                            <tr>
                                <th scope="col">Images</th>
                                <td>
                                    <?php foreach ($img_result as $row) {
                                        echo '<a href="uploads/all/' . $row['file_name'] . '" target="_blank" class="mx-2">
                                        <img src="uploads/all/' . $row['file_name'] . '" class="img-60 border" alt="">
                                        </a>';
                                    } ?>
                                </td>
                            </tr>


                        </table>
                    </div>
                </div>
                <?php foreach ($result as $key => $row): ?>
                    <div class="tab-pane" id="tab<?php echo $key;?>" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th scope="col">Response User</th>
                                    <td><?php echo $row['display_name']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="col">Response Date</th>
                                    <td><?php echo $row['transaction_date']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="col">Respone Comments</th>
                                    <td><?php echo $row['remarks']; ?></td>
                                </tr>
                                <?php
    $table_name = 'complaint_responses';
    $column = 'complaint_image';
    $url = API."?mode=getImagesData&method=get&table=$table_name&id={$row['id']
}&column=$column";
    $res_img_result = callAPI($url);
    $res_img_result = json_decode($res_img_result, 1);
    $res_img_result = $res_img_result['table'];

                                 ?>

                                <tr>
                                    <th scope="col">Response Images</th>
                                    <td>
                                        <?php foreach ($res_img_result as $row) {
                                    echo '<a href="uploads/all/' . $row['file_name'] . '" target="_blank" class="mx-2">
                                    <img src="uploads/all/' . $row['file_name'] . '" class="img-60 border" alt="">
                                    </a>';
                                } ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>


    </div>
</div>
</div>
</div>
