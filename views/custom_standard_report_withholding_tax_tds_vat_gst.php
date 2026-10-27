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

$url = API . "?mode=togetwithholdingtax&from_date=" . $from_date . "&to_date=" . $to_date;

$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['withholding_tax_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Withholding Tax (TDS / VAT / GST) Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=WithholdingTax&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=WithholdingTax&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <?php include_once 'date_filter_common.php'; ?>

        <hr>

        <?php if (count((array)$list) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-right">SL No</th>
                        <th scope="col">PI No.</th>

                        <th scope="col">Supplier</th>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Invoice Date</th>
                        <th scope="col" class="text-right">Taxable Amount</th>
                        <th scope="col" class="text-right">GST/VAT Amount</th>
                        <th scope="col" class="text-right">TDS Withheld</th>
                        <th scope="col" class="text-right">Invoice Total</th>
                        <th scope="col" class="text-right">Net Payable</th>
                        <th scope="col">Tax Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cssdata = [];
                    $sl = 1;
                    foreach ($list as $r) {
                        // Display helpers
                        $gst_type = ($r['tax_state'] === 'S') ? 'Intra (CGST+SGST)' : (($r['tax_state'] === 'I') ? 'Inter (IGST)' : '-');
                    ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo $r['transaction_number']; ?></td>

                            <td class="p-1"><?php echo $r['party_name']; ?></td>
                            <td class="p-1"><?php echo $r['invoice_no']; ?></td>
                            <td class="p-1"><?php echo fromDate($r['invoice_date']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['taxable_amount']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['gst_vat_amount']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['tds_withheld']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['invoice_total']); ?></td>
                            <td class="text-right p-1 fw-bold"><?php echo customeummarmonyformate($r['net_payable']); ?></td>
                            <td class="p-1"><?php echo $gst_type; ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'Sl No'           => $sl++,
                            'PI No.'         => $r['transaction_number'],
                            'Supplier'        => $r['party_name'],
                            'Invoice No'      => $r['invoice_no'],
                            'Invoice Date'    => fromDate($r['invoice_date']),
                            'Taxable Amount'  => customeummarmonyformate($r['taxable_amount']),
                            'GST/VAT Amount'  => customeummarmonyformate($r['gst_vat_amount']),
                            'TDS Withheld'    => customeummarmonyformate($r['tds_withheld']),
                            'Invoice Total'   => customeummarmonyformate($r['invoice_total']),
                            'Net Payable'     => customeummarmonyformate($r['net_payable']),
                            'Tax Type'        => $gst_type
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
