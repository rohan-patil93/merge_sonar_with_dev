<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$category_ids = '';
$location_id = '';
$from_date = '';
$to_date = '';

if(isset($_POST['category_ids']) && is_array($_POST['category_ids'])){
    $category_ids = implode(',', $_POST['category_ids']);
}
if(isset($_POST['location_id'])){
    $location_id = $_POST['location_id'];
}
if(isset($_POST['from_date'])){
    $from_date = $_POST['from_date'];
}
if(isset($_POST['to_date'])){
    $to_date = $_POST['to_date'];
}

$url = API . "?mode=getStationeryStockInOutDatewise"
    . "&category_ids=".$category_ids
    . "&location_id=".$location_id
    . "&from_date=".$from_date
    . "&to_date=".$to_date
    . "&ajax=Y";

$che = callAPI($url);
$data = json_decode($che, 1);
$report_res = $data['report_res'] ?? array();
$_SESSION['download_data'] = [];
?>
<div class="card">
    <div class="card-header">
        <h5>Stationery Stock In/Out Datewise</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryStockInOutDatewise&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryStockInOutDatewise&format=excel" target="_blank">Excel</a>
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

                    <th scope="col">InStock</th>
                    <th scope="col">OutStock</th>
                    <th scope="col">Current Stock</th>

                </tr>

                <?php
                if(count((array)$report_res) > 0){
                    $cssdata = [];
                    foreach($report_res as $index => $row){
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['location_name']); ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td class="text-wrap-cell"><?php echo htmlspecialchars($row['item_name']); ?></td>

                            <td><?php echo $row['instock']; ?></td>
                            <td><?php echo $row['outstock']; ?></td>
                            <td><?php echo $row['available_qty']; ?></td>

                        </tr>
                        <?php
                        $cssdata[] = [
                            'Sl No' => $index + 1,
                            'Location' => $row['location_name'],
                            'Category' => $row['category_name'],
                            'Item' => formatReportCsvValue($row['item_name']),
                            'InStock' => $row['instock'],
                            'OutStock' => $row['outstock'],
                            'Current Stock' => $row['available_qty']

                        ];
                    }
                    $_SESSION['download_data'] = $cssdata;
                }else{
                    ?>
                    <tr>
                        <td colspan="8">
                            <div class="text-center">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                    <i class="fa fa-warning"></i> No Records
                                </p>
                                <div class="text-center px-4">
                                    <img class="mw-100 empty-img-300" src="assets/images/work.png" alt="Preview">
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
