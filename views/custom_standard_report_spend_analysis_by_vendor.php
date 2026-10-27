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

$url = API . "?mode=togetspendanalysisbyvendor&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['spend_analysis_vendor_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Spend Analysis Report – By Vendor</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SpendAnalysisVendor&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=SpendAnalysisVendor&format=excel" target="_blank">Excel</a>
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
                        <th scope="col">Vendor Name</th>
                        <th scope="col" class="text-right">Total PO Value</th>
                        <th scope="col" class="text-right">Total GRN Value</th>
                        <th scope="col" class="text-right">Total Invoice Value</th>
                        <th scope="col" class="text-right">% Spend</th>
                        <th scope="col" class="text-right"># POs</th>
                        <th scope="col" class="text-right"># GRNs</th>
                        <th scope="col" class="text-right"># Invoices</th>
                        <th scope="col">Last Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1;
                    $cssdata = [];
                    foreach ($list as $r) {
                        $last_purchase_date = $r['last_purchase_date'] ? fromDate($r['last_purchase_date']) : '-';
                    ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo $r['party_name'] ?? $r['display_name']; ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_po_value']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_grn_value']); ?></td>
                            <td class="text-right p-1"><?php echo customeummarmonyformate($r['total_invoice_value']); ?></td>
                            <td class="text-right p-1"><?php echo $r['spend_percent']; ?>%</td>
                            <td class="text-right p-1"><?php echo $r['no_of_pos']; ?></td>
                            <td class="text-right p-1"><?php echo $r['no_of_grns']; ?></td>
                            <td class="text-right p-1"><?php echo $r['no_of_invoices']; ?></td>
                            <td class="p-1"><?php echo $last_purchase_date; ?></td>
                        </tr>
                    <?php
                        $cssdata[] = [
                            'Sl No'               => $sl++,
                            'Vendor Name'         => $r['party_name'] ?? $r['display_name'],
                            'Total PO Value'      => customeummarmonyformate($r['total_po_value']),
                            'Total GRN Value'     => customeummarmonyformate($r['total_grn_value']),
                            'Total Invoice Value' => customeummarmonyformate($r['total_invoice_value']),
                            '% Spend'             => $r['spend_percent'] . '%',
                            '# POs'               => $r['no_of_pos'],
                            '# GRNs'              => $r['no_of_grns'],
                            '# Invoices'          => $r['no_of_invoices'],
                            'Last Purchase Date'  => $last_purchase_date
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
