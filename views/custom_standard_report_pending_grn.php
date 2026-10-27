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

$url = API . "?mode=togetpendinggrn&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$pending_grns = $result['pending_grn_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Pending GRNs (Not Yet Invoiced) Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=PendingGRN&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=PendingGRN&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

<?php include_once 'date_filter_common.php'; ?>

        <hr>

        <!-- Report Table -->
        <?php if (count((array)$pending_grns) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col">GRN / PO Info</th>
                        <th scope="col">Items</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cssdata = array();
                    $sl = 1;
                    foreach ($pending_grns as $grn_id => $rows) { ?>
                        <tr>
                            <!-- Left Column: GRN/PO Info -->
                            <td>
                                <table role="presentation">
                                    <tr>
                                        <td class="p-1">GRN No.</td>
                                        <td class="p-1"><?php echo $rows[0]['grn_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">GRN Date</td>
                                        <td class="p-1"><?php echo fromDate($rows[0]['grn_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">Vendor</td>
                                        <td class="p-1"><?php echo $rows[0]['vendor_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">PO No.</td>
                                        <td class="p-1"><?php echo $rows[0]['po_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1">PO Date</td>
                                        <td class="p-1"><?php echo fromDate($rows[0]['po_date']); ?></td>
                                    </tr>
                                </table>
                            </td>

                            <!-- Right Column: Items -->
                            <td class="align-top">
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="p-1">Item</th>
                                            <th scope="col" class="text-right p-1">GRN Qty</th>
                                            <th scope="col" class="text-right p-1">Invoiced Qty</th>
                                            <th scope="col" class="text-right p-1">Pending Qty</th>
                                            <th scope="col" class="text-right p-1">GRN Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row) { ?>
                                            <tr>
                                                <td class="p-1"><?php echo $row['item_name']; ?></td>
                                                <td class="text-right p-1"><?php echo customeummarmonyformate($row['grn_qty']); ?></td>
                                                <td class="text-right p-1"><?php echo customeummarmonyformate($row['invoiced_qty']); ?></td>
                                                <td class="text-right p-1 text-danger"><?php echo customeummarmonyformate($row['pending_qty']); ?></td>
                                                <td class="text-right p-1"><?php echo customeummarmonyformate($row['grn_amount']); ?></td>
                                            </tr>
                                        <?php
                                            $cssdata[] = array(
                                                'Sl No'        => $sl,
                                                'GRN No'       => $rows[0]['grn_no'],
                                                'GRN Date'     => fromDate($rows[0]['grn_date']),
                                                'Vendor'       => $rows[0]['vendor_name'],
                                                'PO No'        => $rows[0]['po_no'],
                                                'PO Date'      => fromDate($rows[0]['po_date']),
                                                'Item'         => $row['item_name'],
                                                'GRN Qty'      => customeummarmonyformate($row['grn_qty']),
                                                'Invoiced Qty' => customeummarmonyformate($row['invoiced_qty']),
                                                'Pending Qty'  => customeummarmonyformate($row['pending_qty']),
                                                'GRN Amount'   => customeummarmonyformate($row['grn_amount'])
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
            <tr>
                <td colspan="15">
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </div>
</div>
