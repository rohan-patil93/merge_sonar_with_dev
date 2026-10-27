<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// ===============================
// 📅 Date Filters
// ===============================
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

// ===============================
// 🌐 API Call
// ===============================
$url = API . "?mode=togetcancelledoramendedorders&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['amended_orders_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Amended Orders Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=CancelledAmendedOrders&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=CancelledAmendedOrders&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- 📅 Date Filter -->
        <?php include_once 'date_filter_common.php'; ?>
        <hr>

        <?php if (count((array)$list) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-right">SL No</th>
                        <th scope="col">Transaction No</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col">Reference Type</th>
                        <th scope="col">Reference No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1;
                    $cssdata = [];
                    foreach ($list as $r) {
                        $transaction_date = $r['transaction_date'] ? fromDate($r['transaction_date']) : '-';
                    ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo htmlspecialchars($r['transaction_number']); ?></td>
                            <td class="p-1"><?php echo $transaction_date; ?></td>
                            <td class="p-1"><?php echo htmlspecialchars($r['ref_type']); ?></td>
                            <td class="p-1"><?php echo htmlspecialchars($r['ref_no']); ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'SL No'             => $sl++,
                            'Transaction No'    => $r['transaction_number'],
                            'Transaction Date'  => $transaction_date,
                            'Reference Type'    => $r['ref_type'],
                            'Reference No'      => $r['ref_no']
                        ];
                    }
                    $_SESSION['download_data'] = $cssdata;
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Data Available</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
