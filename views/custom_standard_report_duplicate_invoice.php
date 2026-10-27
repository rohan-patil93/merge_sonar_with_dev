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

$url = API . "?mode=togetduplicateinvoicedata&from_date=" . $from_date . "&to_date=" . $to_date;
$apires = callAPI($url);
$result = json_decode($apires, true);

$data = $result['material_shortage_list'] ?? [];
$_SESSION['download_data'] = [];
?>

<div class="card">
    <div class="card-header">
        <h5>Duplicate Invoice Check Report</h5>
        <div class="card-header-right">
            <div class="dropdown-primary dropdown open">
                <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=DuplicateInvoiceCheckReport&format=csv" target="_blank">CSV</a>
                    <a class="dropdown-item waves-light waves-effect"
                        href="custom_view_download.php?page=DuplicateInvoiceCheckReport&format=excel" target="_blank">Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">

        <?php include_once 'date_filter_common.php'; ?>

        <hr>

        <!-- Shortage Report Table -->
        <?php if (count((array)$data) != 0) { ?>
            <table class="table table-separate table-head-custom table-checkable data-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-right">SL No</th>
                        <th scope="col">PI No.</th>

                        <th scope="col">Supplier Name</th>
                        <th scope="col">Invoice Date</th>
                        <th scope="col" class="text-right">Total Amount</th>
                        <th scope="col" class="text-center">Duplicate Count</th>
                        <th scope="col">Duplicate Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cssdata = array();
                    $sl = 1;
                    foreach ($data as $row) { ?>
                        <tr>
                            <td class="text-right p-1"><?php echo $sl; ?></td>
                            <td class="p-1"><?php echo $row['transaction_number']; ?></td>

                            <td><?php echo $row['party_name']; ?></td>
                            <td><?php echo $row['invoice_no']; ?></td>
                            <td><?php echo fromDate($row['invoice_date']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($row['total_amount']); ?></td>
                            <td class="text-center"><?php echo $row['duplicate_count']; ?></td>
                            <td>
                                <span class="label label-md <?php echo ($row['duplicate_type'] == 'Duplicate Invoice') ? 'label-danger' : 'label-success'; ?>">
                                    <?php echo $row['duplicate_type']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                        $cssdata[] = array(
                            'Sl No'          => $sl,
                            'PI No.'         => $row['transaction_number'],
                            'Supplier Name' => $row['party_name'],
                            'Invoice No' => $row['invoice_no'],
                            'Invoice Date' => ($row['invoice_date']) ? fromDate($row['invoice_date']) : '',
                            'Total Amount' => $row['total_amount'],
                            'Duplicate Count' => $row['duplicate_count'],
                            'Duplicate Type' => $row['duplicate_type']
                        );
                        $sl++;
                    }
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
