<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$category_ids = '';
$location_id = '';

if(isset($_POST['category_ids']) && is_array($_POST['category_ids'])){
    $category_ids = implode(',', $_POST['category_ids']);
}
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';
$url = API . "?mode=getStationeryAvailableItemsData&category_ids=".$category_ids."&location_id=".$location_id."&ajax=Y";
$che = callAPI($url);
$data = json_decode($che, 1);

$available_items_res = $data['available_items_res'] ?? array();
$_SESSION['download_data'] = [];
?>
<div class="card">
    <div class="card-body">
        <div class="card-title mb-4">
            <div class="row">
                <div class="col-lg-3">
                    <div class="col bg-light-primary">
                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                            <img src="uploads/all/file_1704095913_659270a975d4a.png"
                                 class="img-fluid report-logo st_width_40" alt="Logo">
                        </span>
                        <span class="text-dark-75 font-weight-bolder font-size-h3">
                            <?php echo $_SESSION['config']['name']; ?>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4 class="card-label text-center f-w-700"><br>
                        <small class="f-14">
                            Name :
                            <?php echo $_SESSION['users'][$_SESSION['userid']] ?? $_SESSION['us_name']; ?>
                            &nbsp; Date &amp; Time :
                            <?php echo date('Y-m-d H:i:s'); ?>
                        </small>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5>Available Stationery Items</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryAvailableItems&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryAvailableItems&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body custom-scroll" style="max-height:550px !important;">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th scope="col">Sl No</th>
                    <th scope="col">Location</th>
                    <th scope="col">Category</th>
                    <th scope="col">Item</th>
                    <th scope="col">Received</th>
                    <th scope="col">Current Stock</th>
                    <th scope="col">Consumed</th>
                    <th scope="col">Balance</th>
                </tr>

                <?php
                if(count((array)$available_items_res) > 0){
                    $sl = 1;
                    $cssdata = [];
                    foreach($available_items_res as $index => $row){
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['location_name']); ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo $row['received']; ?></td>
                            <td><?php echo $row['current_stock']; ?></td>
                            <td><?php echo $row['consumed']; ?></td>
                            <td><?php echo $row['balance']; ?></td>
                        </tr>
                        <?php
                        $cssdata[] = [
                            'Sl No'           => $index + 1,
                            'Location'        => $row['location_name'],
                            'Category'        => $row['category_name'],
                            'Item'            => formatReportCsvValue($row['item_name']),
                            'Received'        => $row['received'],
                            'Current Stock'   => $row['current_stock'],
                            'Consumed'        => $row['consumed'],
                            'Balance'         => $row['balance']
                        ];
                    }
                    $_SESSION['download_data'] = $cssdata;
                } else {
                    ?>
                    <tr>
                        <td colspan="8">
                            <div class="text-center">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                    <i class="fa fa-warning"></i> No Records
                                </p>
                                <div class="text-center px-4">
                                    <img class="mw-100 empty-img-300"
                                         src="assets/images/work.png" alt="Preview">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
