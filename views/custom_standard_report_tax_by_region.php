<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// 📅 Handle date filters (same style as your existing code)
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : '';
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : '';
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

// 🌐 Call API
$url = API . "?mode=togettaxreportbyregion&from_date=" . $from_date . "&to_date=" . $to_date . "&company_id=" . $_SESSION['company_id'];
$apires = callAPI($url);
$result = json_decode($apires, true);

// 📋 Get list
$list = $result['report_data'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Tax Report (GST/VAT/Sales Tax by Region)</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=TaxReportByRegion&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=TaxReportByRegion&format=excel" target="_blank">Excel</a>
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
                        <th scope="col">Region / Location</th>
                        <th scope="col" class="text-right">Total Taxable Amount (₹)</th>
                        <th scope="col" class="text-right">Total Tax Amount (₹)</th>
                        <th scope="col" class="text-right">Average Tax Rate (%)</th>
                        <th scope="col" class="text-right">Total Invoices</th>
                        <th scope="col" class="text-right">Total Items</th>
                        <th scope="col" class="text-right">Total Tax Lines</th>
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
                            <td><?php echo $row['region_name']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_taxable_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_tax_amount']); ?></td>
                            <td class="text-right"><?php echo $row['avg_tax_rate_percent']; ?></td>
                            <td class="text-right"><?php echo $row['total_invoices']; ?></td>
                            <td class="text-right"><?php echo $row['total_items']; ?></td>
                            <td class="text-right"><?php echo $row['total_tax_lines']; ?></td>
                        </tr>
                        <?php
                        $csv_data[] = [
                            'S.No'                    => $i++,
                            'Region / Location'       => $row['region_name'],
                            'Total Taxable Amount (₹)'=> customeummarmonyformate($row['total_taxable_amount']),
                            'Total Tax Amount (₹)'    => customeummarmonyformate($row['total_tax_amount']),
                            'Average Tax Rate (%)'    => $row['avg_tax_rate_percent'],
                            'Total Invoices'          => $row['total_invoices'],
                            'Total Items'             => $row['total_items'],
                            'Total Tax Lines'         => $row['total_tax_lines']
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
