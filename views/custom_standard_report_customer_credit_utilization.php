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

$url = API . "?mode=togetcustomercreditutilizationreport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['credit_utilization_report'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Customer Credit Limit Utilization Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                    Export
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=CreditUtilization&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=CreditUtilization&format=excel" target="_blank">Excel</a>
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
                    <th scope="col">Customer</th>
                    <th scope="col" class="text-right">Credit Limit</th>
                    <th scope="col" class="text-right">Total Receivables</th>
                    <th scope="col" class="text-right">Outstanding</th>
                    <th scope="col" class="text-right">Utilized</th>
                    <th scope="col" class="text-right">Utilization %</th>
                    <th scope="col" class="text-right">Available</th>
                    <th scope="col">Status</th>
                    <th scope="col">Last Invoice</th>
                    <th scope="col">Last Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sl = 1;
                $csvData = [];
                foreach ($list as $r) {
                    $statusColor = ($r['credit_status'] == 'Safe') ? 'text-success' : (($r['credit_status'] == 'Near Limit') ? 'text-warning' : 'text-danger');
                ?>
                <tr>
                    <td><?php echo $sl; ?></td>
                    <td><?php echo $r['customer_name']; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['credit_limit']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_receivables']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['outstanding_balance']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['credit_utilized']); ?></td>
                    <td class="text-right fw-bold"><?php echo $r['utilization_percent']; ?>%</td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['available_credit']); ?></td>
                    <td class="<?php echo $statusColor; ?> fw-bold"><?php echo $r['credit_status']; ?></td>
                    <td><?php echo $r['last_invoice_date'] ? fromDate($r['last_invoice_date']) : '-'; ?></td>
                    <td><?php echo $r['last_payment_date'] ? fromDate($r['last_payment_date']) : '-'; ?></td>
                </tr>
                <?php
                    $csvData[] = [
                        'Sl No' => $sl++,
                        'Customer' => $r['customer_name'],
                        'Credit Limit' => $r['credit_limit'],
                        'Total Receivables' => $r['total_receivables'],
                        'Outstanding' => $r['outstanding_balance'],
                        'Utilized' => $r['credit_utilized'],
                        'Utilization %' => $r['utilization_percent'].'%',
                        'Available' => $r['available_credit'],
                        'Status' => $r['credit_status'],
                        'Last Invoice' => $r['last_invoice_date'] ? fromDate($r['last_invoice_date']) : '',
                        'Last Payment' => $r['last_payment_date'] ? fromDate($r['last_payment_date']) : ''
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
