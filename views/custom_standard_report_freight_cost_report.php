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

$url = API . "?mode=togetfreightcostreport&from_date=".$from_date."&to_date=".$to_date."&company_id=".$_SESSION['company_id'];
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['freight_report'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5><?= __('Freight Cost Report'); ?></h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= __('More'); ?>
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=FreightCostReport&format=csv" target="_blank"><?= __('CSV'); ?></a>
                    <a class="dropdown-item" href="custom_view_download.php?page=FreightCostReport&format=excel" target="_blank"><?= __('Excel'); ?></a>
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
                        <th scope="col"><?= __('S.No'); ?></th>
                        <th scope="col"><?= __('Transaction No'); ?></th>
                        <th scope="col"><?= __('Transaction Date'); ?></th>
                        <th scope="col"><?= __('Customer / Party'); ?></th>
                        <th scope="col" class="text-right"><?= __('Shipping Amount (₹)'); ?></th>
                        <th scope="col" class="text-right"><?= __('Shipping Tax Amount (₹)'); ?></th>
                        <th scope="col" class="text-right"><?= __('Total Freight (₹)'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $csv_data = [];
                    $grand_base = $grand_tax = $grand_total = 0;
                    foreach ($list as $row) {
                        $grand_base += $row['base_amount'];
                        $grand_tax += $row['tax_amount'];
                        $grand_total += $row['total_amount'];
                    ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row['transaction_number']; ?></td>
                            <td><?php echo fromDate($row['transaction_date']); ?></td>
                            <td><?php echo $row['party_name']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['base_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['tax_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_amount']); ?></td>
                        </tr>
                        <?php
                        $csv_data[] = [
                            'S.No' => $i++,
                            'Transaction No' => $row['transaction_number'],
                            'Transaction Date' => fromDate($row['transaction_date']),
                            'Customer / Party' => $row['party_name'],
                            'Shipping Amount (₹)' => customeummarmonyformate($row['base_amount']),
                            'Shipping Tax Amount (₹)' => customeummarmonyformate($row['tax_amount']),
                            'Total Freight (₹)' => customeummarmonyformate($row['total_amount'])
                        ];
                    }
                    // Grand Total row
                    $csv_data[] = [
                        'S.No' => '',
                        'Transaction No' => '',
                        'Transaction Date' => '',
                        'Customer / Party' => 'GRAND TOTAL',
                        'Shipping Amount (₹)' => $grand_base,
                        'Shipping Tax Amount (₹)' => $grand_tax,
                        'Total Freight (₹)' => $grand_total
                    ];
                    $_SESSION['download_data'] = $csv_data;
                    ?>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end"><?= __('Grand Total'); ?></td>
                        <td class="text-right"><?php echo customeummarmonyformate($grand_base); ?></td>
                        <td class="text-right"><?php echo customeummarmonyformate($grand_tax); ?></td>
                        <td class="text-right"><?php echo customeummarmonyformate($grand_total); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold">
                    <i class="ti-alert text-warning"></i> <?= __('No Data Available'); ?>
                </p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
