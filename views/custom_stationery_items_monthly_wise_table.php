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

if(isset($_POST['from_date'])){
    $from_date = $_POST['from_date'];
}

if(isset($_POST['to_date'])){
    $to_date = $_POST['to_date'];
}
if(isset($_POST['location_id'])){
    $location_id = $_POST['location_id'];
}

$url = API . "?mode=getMonthlyStationeryReport"
     . "&category_ids=".$category_ids
     . "&location_id=".$location_id
     . "&from_date=".$from_date
     . "&to_date=".$to_date
     . "&ajax=Y";

$che = callAPI($url);
$data = json_decode($che, 1);

$monthly_res = $data['monthly_res'] ?? array();
$_SESSION['download_data'] = [];
?>
<div class="card">
    <div class="card-header">
        <h5>Monthly Stationery Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryMonthlyWiseReport&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryMonthlyWiseReport&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th scope="col">Sl No</th>
                    <th scope="col">Location</th>
                    <th scope="col">Category</th>
                    <th scope="col">Item</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Date of Issue</th>
                    <th scope="col">Issued By</th>
                </tr>

                <?php
                if(count((array)$monthly_res) > 0){
                    $cssdata = [];
                    foreach($monthly_res as $index => $row){
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['request_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['issued_by']); ?></td>
                        </tr>
                        <?php
                        $cssdata[] = [
                            'Sl No'          => $index + 1,
                            'Location'       => $row['location_name'],
                            'Category'       => $row['category_name'],
                            'Item'           => formatReportCsvValue($row['item_name']),
                            'Quantity'       => $row['qty'],
                            'Date of Issue'  => date('d-m-Y', strtotime($row['request_date'])),
                            'Issued By'      => $row['issued_by']
                        ];
                    }
                    $_SESSION['download_data'] = $cssdata;
                } else {
                    ?>
                    <tr>
                        <td colspan="7">
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
