<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : '';
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : '';
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

$url = API . "?mode=togetcustomerpaymenthistoryreport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['payment_history_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Customer Payment History Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=CustomerPaymentHistory&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=CustomerPaymentHistory&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php include_once 'date_filter_common.php'; ?>
        <hr>

        <?php if (count((array)$list) > 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Payment Ref No.</th>
                        <th scope="col">Payment Mode</th>
                        <th scope="col" class="text-right">Payment Amount (₹)</th>
                        <th scope="col" class="text-right">TDS/Other Deductions (₹)</th>
                        <th scope="col" class="text-right">Net Received (₹)</th>
                        <th scope="col" class="text-right">Balance After Payment (₹)</th>
                        <th scope="col">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $csv_data = [];
                    foreach ($list as $row) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo fromDate($row['payment_date']); ?></td>
                            <td><?php echo $row['payment_reference_no']; ?></td>
                            <td><?php echo $row['payment_mode']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['payment_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['tds_deductions']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['net_received']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['balance_after_payment']); ?></td>
                            <td><?php echo $row['remarks']; ?></td>
                        </tr>
                        <?php
                        $csv_data[] = [
                            'S.No' => $i++,
                            'Customer Name' => $row['customer_name'],
                            'Payment Date' => fromDate($row['payment_date']),
                            'Payment Ref No.' => $row['payment_reference_no'],
                            'Payment Mode' => $row['payment_mode'],
                            'Payment Amount (₹)' => $row['payment_amount'],
                            'TDS/Other Deductions (₹)' => $row['tds_deductions'],
                            'Net Received (₹)' => $row['net_received'],
                            'Balance After Payment (₹)' => $row['balance_after_payment'],
                            'Remarks' => $row['remarks']
                        ];
                    }
                    $_SESSION['download_data'] = $csv_data;
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold">
                    <i class="ti-alert text-warning"></i> No Data Available
                </p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
