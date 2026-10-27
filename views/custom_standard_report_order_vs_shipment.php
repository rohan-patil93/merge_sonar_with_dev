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

$url = API . "?mode=togetordervsshipmentdata&transaction_table=sales_orders&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$groups = $result['so_vs_do_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Order vs Shipment (Delivery) Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2">
                    <a class="dropdown-item" href="custom_view_download.php?page=OrdervsShipment&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=OrdervsShipment&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <!-- date dropdown UI same as POvsGRN -->
        <?php include_once 'date_filter_common.php'; ?>

        <hr>

        <?php if (count((array)$groups) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mp">
                <thead>
                    <tr>
                        <th scope="col">Order Info</th>
                        <th scope="col">Delivery Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cssdata = array();
                    $sl = 1;
                    foreach ($groups as $order_id => $tableslist) { ?>
                        <tr>
                            <td>
                                <table role="presentation">
                                    <tr>
                                        <td class="p-1">Order No.</td>
                                        <td class="p-1 text-primary cursor-pointer"
                                            onclick="openBucketModal('<?= htmlspecialchars($tableslist[0]['sales_id']) ?>>','SO','<?= $tableslist[0]['so_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <?= $tableslist[0]['so_no']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Date</td>
                                        <td class="p-1"><?= fromDate($tableslist[0]['so_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Customer</td>
                                        <td class="p-1"><?= $tableslist[0]['party_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Order Qty</td>
                                        <td class="p-1" class="text-right"><?= customeummarmonyformate($tableslist[0]['so_qty']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Order Amount</td>
                                        <td class="p-1" class="text-right"><?= customeummarmonyformate($tableslist[0]['so_amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Requested By</td>
                                        <td class="p-1"><?= $_SESSION['users'][$tableslist[0]['so_requested_by']] ?? '-'; ?></td>
                                    </tr>
                                </table>
                            </td>
                            <td class="align-top">
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="p-1">DO No</th>
                                            <th scope="col" class="p-1">Date</th>
                                            <th scope="col" class="p-1">Party</th>
                                            <th scope="col" class="text-right">DO Qty</th>
                                            <th scope="col" class="text-right">DO Amount</th>
                                            <th scope="col" class="p-1">Requested By</th>
                                            <th scope="col" class="text-right">Qty Variance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tableslist as $row) { ?>
                                            <tr>
                                                <td class="text-primary cursor-pointer"
                                                    onclick="openBucketModal('<?= htmlspecialchars($row['delivery_id']) ?>>','DO','<?= $row['do_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <?= $row['do_no'] ?? '-'; ?>
                                                </td>
                                                <td class="p-1"><?= fromDate($row['do_date']) ?? '-'; ?></td>
                                                <td class="p-1"><?= $row['do_party'] ?? '-'; ?></td>
                                                <td class="text-right"><?= customeummarmonyformate($row['do_qty']); ?></td>
                                                <td class="text-right"><?= customeummarmonyformate($row['do_amount']); ?></td>
                                                <td class="p-1"><?= $_SESSION['users'][$row['do_requested_by']] ?? '-'; ?></td>
                                                <td class="text-right <?= ($row['qty_variance'] == 0 ? 'text-success' : 'text-danger'); ?>">
                                                    <?= customeummarmonyformate($row['qty_variance']); ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $cssdata[] = array(
                                                'Sl No' => $sl,
                                                'Order No' => $tableslist[0]['so_no'],
                                                'Order Date' => fromDate($tableslist[0]['so_date']),
                                                'Customer' => $tableslist[0]['party_name'],
                                                'Order Qty' => customeummarmonyformate($tableslist[0]['so_qty']),
                                                'Order Amount' => customeummarmonyformate($tableslist[0]['so_amount']),
                                                'Requested By' => $_SESSION['users'][$tableslist[0]['so_requested_by']] ?? '-',
                                                'DO No' => $row['do_no'] ?? '-',
                                                'DO Date' => fromDate($row['do_date']) ?? '-',
                                                'DO Party' => $row['do_party'] ?? '-',
                                                'DO Qty' => customeummarmonyformate($row['do_qty']),
                                                'DO Amount' => customeummarmonyformate($row['do_amount']),
                                                'DO Requested By' => $_SESSION['users'][$row['do_requested_by']] ?? '-',
                                                'Qty Variance' => customeummarmonyformate($row['qty_variance'])
                                            );
                                            $sl++;
                                        } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php }
                    $_SESSION['download_data'] = $cssdata; ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="bucketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content"><!-- filled by PHP partial via .load() --></div>
    </div>
</div>
<script>
    function openBucketModal(Rid, type, transaction_no) {
        var url = 'views/custom_standard_report_order_vs_shipment_popup.php' +
            '?rid=' + encodeURIComponent(Rid) +
            '&type=' + encodeURIComponent(type) +
            '&transaction_no=' + encodeURIComponent(transaction_no);

        var $content = $('#bucketModal .modal-content');
        $content.html('<div class="modal-header py-2"><h6 class="modal-title">Loading…</h6>' +
            '<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
            '<div class="modal-body py-4 text-center text-muted">Fetching records…</div>');
        $content.load(url, function() {
            var modal = new bootstrap.Modal(document.getElementById('bucketModal'));
            modal.show();
        });
    }
</script>
