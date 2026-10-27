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

$url = API . "?mode=togetcustomerbalancereport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['customer_balance_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5> Customer Balance Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                    More
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=CustomerBalance&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=CustomerBalance&format=excel" target="_blank">Excel</a>
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
                    <th scope="col" class="text-right">Opening Balance</th>
                    <th scope="col" class="text-right">Invoices</th>
                    <th scope="col" class="text-right">Payments</th>
                    <th scope="col" class="text-right">Credit Notes</th>
                    <th scope="col" class="text-right">Debit Notes</th>
                    <th scope="col" class="text-right">Closing Balance</th>
                    <th scope="col" class="text-right">Credit Limit</th>
                    <th scope="col" class="text-right">Available Credit</th>
                    <th scope="col" class="text-right">Overdue</th>
                    <th scope="col">Last Payment Date</th>
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
                    <td><?php echo $r['customer_name']; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['opening_balance']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_invoices']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_payments']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_credit_notes']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_debit_notes']); ?></td>
                    <td class="text-right fw-bold text-danger"><?php echo customeummarmonyformate($r['closing_balance']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['credit_limit']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['available_credit']); ?></td>
                    <td class="text-right text-danger"><?php echo customeummarmonyformate($r['overdue_amount']); ?></td>
                    <td><?php echo $r['last_payment_date'] ? fromDate($r['last_payment_date']) : '-'; ?></td>
                </tr>
                <?php
                    $csvData[] = [
                        'Sl No' => $sl++,
                        'Customer' => $r['customer_name'],
                        'Opening Balance' => $r['opening_balance'],
                        'Invoices' => $r['total_invoices'],
                        'Payments' => $r['total_payments'],
                        'Credit Notes' => $r['total_credit_notes'],
                        'Debit Notes' => $r['total_debit_notes'],
                        'Closing Balance' => $r['closing_balance'],
                        'Credit Limit' => $r['credit_limit'],
                        'Available Credit' => $r['available_credit'],
                        'Overdue' => $r['overdue_amount'],
                        'Last Payment Date' => $r['last_payment_date'] ? fromDate($r['last_payment_date']) : ''
                    ];
                }
                $_SESSION['download_data'] = $csvData;
                ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="text-center py-4">
                <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Customer Balances Found</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
