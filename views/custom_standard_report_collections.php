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

$url = API . "?mode=togetcollectionsreport&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);
$list = $result['collections_report'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5> Collections Report (Payments vs Outstanding)</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" data-bs-toggle="dropdown">
                    Export
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item" href="custom_view_download.php?page=Collections&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item" href="custom_view_download.php?page=Collections&format=excel" target="_blank">Excel</a>
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
                    <th scope="col" class="text-right">Opening</th>
                    <th scope="col" class="text-right">Invoices</th>
                    <th scope="col" class="text-right">Total Receivable</th>
                    <th scope="col" class="text-right">Payments</th>
                    <th scope="col" class="text-right">Collection %</th>
                    <th scope="col" class="text-right">Outstanding</th>
                    <th scope="col" class="text-right">Overdue</th>
                    <th scope="col">Last Payment</th>
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
                    <td class="text-right"><?php echo customeummarmonyformate($r['total_receivable']); ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($r['payments_received']); ?></td>
                    <td class="text-right fw-bold text-primary"><?php echo $r['collection_percent']; ?>%</td>
                    <td class="text-right fw-bold text-danger"><?php echo customeummarmonyformate($r['outstanding_balance']); ?></td>
                    <td class="text-right text-danger"><?php echo customeummarmonyformate($r['overdue_amount']); ?></td>
                    <td><?php echo $r['last_payment_date'] ? fromDate($r['last_payment_date']) : '-'; ?></td>
                </tr>
                <?php
                    $csvData[] = [
                        'Sl No' => $sl++,
                        'Customer' => $r['customer_name'],
                        'Opening' => $r['opening_balance'],
                        'Invoices' => $r['total_invoices'],
                        'Total Receivable' => $r['total_receivable'],
                        'Payments' => $r['payments_received'],
                        'Collection %' => $r['collection_percent'],
                        'Outstanding' => $r['outstanding_balance'],
                        'Overdue' => $r['overdue_amount'],
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
