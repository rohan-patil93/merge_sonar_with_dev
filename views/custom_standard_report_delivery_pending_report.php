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

$url = API . "?mode=togetdeliverypendingreport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['delivery_pending_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5> Delivery Pending Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=DeliveryPending&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=DeliveryPending&format=excel" target="_blank">Excel</a>
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
                    <th scope="col">DO No.</th>
                    <th scope="col">DO Date</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Branch</th>
                    <th scope="col" class="text-right">Order Qty</th>
                    <th scope="col" class="text-right">Dispatched Qty</th>
                    <th scope="col" class="text-right">Pending Qty</th>
                    <th scope="col" class="text-right">Order Value</th>
                    <th scope="col" class="text-right">Dispatched Value</th>
                    <th scope="col" class="text-right">Pending Value</th>
                    <th scope="col">Required Delivery Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Vehicle No</th>
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
                    <td><?php echo $r['delivery_order_no']; ?></td>
                    <td><?php echo fromDate($r['delivery_order_date']); ?></td>
                    <td><?php echo $r['party_name']; ?></td>
                    <td><?php echo $r['branch_name']; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_order_qty']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_dispatched_qty']); ?></td>
                    <td class="text-right fw-bold text-danger"><?php echo customeummarmonyformate($r['pending_qty']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['order_value']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['dispatched_value']); ?></td>
                    <td class="text-right fw-bold text-danger"><?php echo customeummarmonyformate($r['pending_value']); ?></td>
                    <td><?php echo fromDate($r['required_delivery_date']); ?></td>
                    <td><?php echo $r['dispatch_status']; ?></td>
                    <td><?php echo $r['vehicle_no']; ?></td>
                </tr>
                <?php
                    $csvData[] = [
                        'Sl No' => $sl++,
                        'DO No.' => $r['delivery_order_no'],
                        'DO Date' => fromDate($r['delivery_order_date']),
                        'Customer' => $r['party_name'],
                        'Branch' => $r['branch_name'],
                        'Order Qty' => $r['total_order_qty'],
                        'Dispatched Qty' => $r['total_dispatched_qty'],
                        'Pending Qty' => $r['pending_qty'],
                        'Order Value' => $r['order_value'],
                        'Dispatched Value' => $r['dispatched_value'],
                        'Pending Value' => $r['pending_value'],
                        'Required Delivery Date' => fromDate($r['required_delivery_date']),
                        'Status' => $r['dispatch_status'],
                        'Vehicle No' => $r['vehicle_no']
                    ];
                }
                $_SESSION['download_data'] = $csvData;
                ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Pending Deliveries</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
