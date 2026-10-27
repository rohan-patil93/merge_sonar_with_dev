<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$employee_ids = '';

if(isset($_POST['employee_ids']) && is_array($_POST['employee_ids'])){
    $employee_ids = implode(',', $_POST['employee_ids']);
}

$url = API . "?mode=getEmployeeWiseIssuanceDetails&employee_ids=".$employee_ids."&ajax=Y";
$che = callAPI($url);
$data = json_decode($che, 1);

$issuance_res = $data['issuance_res'] ?? array();
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
        <h5>Employee-wise Issuance Details</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryEmployeeWiseIssuance&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=StationeryEmployeeWiseIssuance&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body custom-scroll" style="max-height:550px !important;">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th scope="col">Sl No</th>
                    <th scope="col">Request No</th>
                    <th scope="col">Employee/Other</th>
                    <th scope="col">Item</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Issued Date</th>
                    <th scope="col">Issued By</th>
                </tr>

                <?php
                if(count((array)$issuance_res) > 0){
                    $sl = 1;
                    $cssdata = [];
                    foreach($issuance_res as $index => $row){
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['request_no']); ?></td>
                            <?php if($row['emp_id'] == 999999){ ?>
                            <td><?php echo htmlspecialchars($row['other_name']); ?></td>
                           <?php }else{ ?>
                            <td><?php echo htmlspecialchars($row['emp_code'] . ' - ' . $row['employee_name']); ?></td>
                           <?php } ?>

                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['request_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['issued_code'] . ' - ' . $row['issued_by']); ?></td>
                        </tr>
                        <?php
                        $emp_display = ($row['emp_id'] == 999999) ? $row['other_name'] : ($row['emp_code'] . ' - ' . $row['employee_name']);
                        $cssdata[] = [
                            'Sl No'                => $index + 1,
                            'Request No'           => $row['request_no'],
                            'Employee/Other'       => $emp_display,
                            'Item'                 => $row['item_name'],
                            'Quantity'             => $row['qty'],
                            'Issued Date'          => date('d-m-Y', strtotime($row['request_date'])),
                            'Issued By'            => $row['issued_code'] . ' - ' . $row['issued_by']
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
