<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : date('Y-m-t');
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

$url = API . "?mode=togetblockedorderscreditreport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['blocked_orders_report'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Blocked Orders Due to Credit Limit</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                    Export
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=BlockedOrdersCredit&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=BlockedOrdersCredit&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php include_once 'date_filter_common.php'; ?>
        <hr>

        <?php if (!empty($list)) { ?>
        <table class="table table-separate table-head-custom table-checkable data-table">
            <thead>
                <tr>
                    <th scope="col">Sl No</th>
                    <th scope="col">Order No</th>
                    <th scope="col">Order Date</th>
                    <th scope="col">Customer</th>
                    <th scope="col" class="text-right">Credit Limit</th>
                    <th scope="col" class="text-right">Current Utilization</th>
                    <th scope="col" class="text-right">Order Value</th>
                    <th scope="col" class="text-right">New Total</th>
                    <th scope="col" class="text-right">Available Credit</th>
                    <th scope="col" class="text-right">Excess Amount</th>
                    <th scope="col" class="text-right">Utilization % After Order</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sl = 1;
                $csvData = [];
                foreach ($list as $r) {
                ?>
                <tr>
                    <td><?php echo $sl; ?></td>
                    <td><?php echo $r['order_number']; ?></td>
                    <td><?php echo fromDate($r['order_date']); ?></td>
                    <td><?php echo $r['customer_name']; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['credit_limit']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['current_utilization']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['order_value']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['new_total']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['available_credit']); ?></td>
                    <td class="text-right text-danger fw-bold"><?php echo customeummarmonyformate($r['excess_amount']); ?></td>
                    <td class="text-right fw-bold"><?php echo $r['utilization_percent_after_order']; ?>%</td>
                </tr>
                <?php
                    $csvData[] = [
                        'Sl No' => $sl++,
                        'Order No' => $r['order_number'],
                        'Order Date' => fromDate($r['order_date']),
                        'Customer' => $r['customer_name'],
                        'Credit Limit' => $r['credit_limit'],
                        'Current Utilization' => $r['current_utilization'],
                        'Order Value' => $r['order_value'],
                        'New Total' => $r['new_total'],
                        'Available Credit' => $r['available_credit'],
                        'Excess Amount' => $r['excess_amount'],
                        'Utilization % After Order' => $r['utilization_percent_after_order'].'%'
                    ];
                }
                $_SESSION['download_data'] = $csvData;
                ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Records Found</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
