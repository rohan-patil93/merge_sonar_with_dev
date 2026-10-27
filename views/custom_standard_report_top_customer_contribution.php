<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

// 📅 Handle date filters
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
    $from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : '';
    $to_date   = $_POST['to']   ? date('Y-m-d', strtotime($_POST['to']))   : '';
} else {
    $from_date = date('Y-m-01');
    $to_date   = date('Y-m-t');
}

// 🌐 API call
$url = API . "?mode=togettopcustomercontributionreport&from_date=" . $from_date . "&to_date=" . $to_date . "&company_id=" . $_SESSION['company_id'];
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['report_data'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Top Customer Contribution Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=TopCustomerContribution&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=TopCustomerContribution&format=excel" target="_blank">Excel</a>
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
                        <th scope="col">Rank</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col" class="text-right">Total Sales (₹)</th>
                        <th scope="col" class="text-right">Total Tax (₹)</th>
                        <th scope="col" class="text-right">Gross Total (₹)</th>
                        <th scope="col" class="text-right">No. of Invoices</th>
                        <th scope="col" class="text-right">Avg. Invoice (₹)</th>
                        <th scope="col" class="text-right">% Contribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $csv_data = [];
                    foreach ($list as $row) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_sales_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_tax_amount']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['gross_total_amount']); ?></td>
                            <td class="text-right"><?php echo $row['total_invoices']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['avg_invoice_value']); ?></td>
                            <td class="text-right"><?php echo $row['contribution_percentage']; ?>%</td>
                        </tr>
                        <?php
                        $csv_data[] = [
                            'Rank' => $i++,
                            'Customer Name' => $row['customer_name'],
                            'Total Sales (₹)' => $row['total_sales_amount'],
                            'Total Tax (₹)' => $row['total_tax_amount'],
                            'Gross Total (₹)' => $row['gross_total_amount'],
                            'No. of Invoices' => $row['total_invoices'],
                            'Avg. Invoice (₹)' => $row['avg_invoice_value'],
                            '% Contribution' => $row['contribution_percentage']
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
