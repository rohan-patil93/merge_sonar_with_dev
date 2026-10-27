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
$list = $result['cancelled_amended_orders_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5> Cancelled / Amended Orders Report</h5>
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
                        <th scope="col">Order No</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Customer</th>
                        <th scope="col" class="text-right">Original Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Amendment No</th>
                        <th scope="col">Amended Date</th>
                        <th scope="col" class="text-right">Revised Amount</th>
                        <th scope="col" class="text-right">% Change</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Amended By</th>
                        <th scope="col">Cancelled Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1;
                    $cssdata = [];
                    foreach ($list as $r) {
                        $order_date = $r['order_date'] ? fromDate($r['order_date']) : '-';
                        $amended_date = $r['amended_date'] ? fromDate($r['amended_date']) : '-';
                        $cancelled_date = $r['cancelled_date'] ? fromDate($r['cancelled_date']) : '-';
                    ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo $r['order_number']; ?></td>
                            <td class="p-1"><?php echo $order_date; ?></td>
                            <td class="p-1"><?php echo $r['party_name']; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['original_amount']); ?></td>
                            <td class="p-1 fw-bold"><?php echo $r['order_status']; ?></td>
                            <td class="p-1"><?php echo $r['amendment_number']; ?></td>
                            <td class="p-1"><?php echo $amended_date; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['revised_amount']); ?></td>
                            <td class="text-right p-1"><?php echo $r['percentage_change']; ?>%</td>
                            <td class="p-1"><?php echo $r['reason_description']; ?></td>
                            <td class="p-1"><?php echo $r['amended_by']; ?></td>
                            <td class="p-1"><?php echo $cancelled_date; ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'Sl No'              => $sl++,
                            'Order No'           => $r['order_number'],
                            'Order Date'         => $order_date,
                            'Customer'           => $r['party_name'],
                            'Original Amount'    => customeummarmonyformate($r['original_amount']),
                            'Status'             => $r['order_status'],
                            'Amendment No'       => $r['amendment_number'],
                            'Amended Date'       => $amended_date,
                            'Revised Amount'     => customeummarmonyformate($r['revised_amount']),
                            '% Change'           => $r['percentage_change'].'%',
                            'Reason'             => $r['reason_description'],
                            'Amended By'         => $r['amended_by'],
                            'Cancelled Date'     => $cancelled_date
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
