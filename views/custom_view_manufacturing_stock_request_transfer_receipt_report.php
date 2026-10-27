<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingstockrequesttransferreceiptreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_str_num($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfg_str_date($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfg_str_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1400px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0">Stock Request / Transfer / Receipt Report</h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_STOCK_REQUEST_TRANSFER_RECEIPT_REPORT"><div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingStockRequestTransferReceiptReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col">Stock Request</th><th scope="col">Request Date</th><th scope="col">WO No</th><th scope="col">Material</th><th scope="col">Requested Qty</th><th scope="col">Transferred Qty</th><th scope="col">Received Qty</th><th scope="col">Pending Transfer</th><th scope="col">Pending Receipt</th><th scope="col">Transfers</th><th scope="col">Receipts</th><th scope="col">UOM</th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('Stock Request' => mfg_str_csv_safe($row['stock_request_no'] ?? '-'), 'Request Date' => mfg_str_csv_safe(mfg_str_date($row['stock_request_date'] ?? '')), 'WO No' => mfg_str_csv_safe($row['work_order_no'] ?? '-'), 'Material' => mfg_str_csv_safe($row['material_name'] ?? '-'), 'Requested Qty' => mfg_str_csv_safe(mfg_str_num($row['requested_qty'] ?? 0)), 'Transferred Qty' => mfg_str_csv_safe(mfg_str_num($row['transferred_qty'] ?? 0)), 'Received Qty' => mfg_str_csv_safe(mfg_str_num($row['received_qty'] ?? 0)), 'Pending Transfer' => mfg_str_csv_safe(mfg_str_num($row['pending_transfer_qty'] ?? 0)), 'Pending Receipt' => mfg_str_csv_safe(mfg_str_num($row['pending_receipt_qty'] ?? 0)), 'Transfers' => mfg_str_csv_safe($row['transfer_numbers'] ?? '-'), 'Receipts' => mfg_str_csv_safe($row['receipt_numbers'] ?? '-'), 'UOM' => mfg_str_csv_safe($row['uom_name'] ?? '-'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['stock_request_no']); ?></td><td><?php echo mfg_str_date($row['stock_request_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['material_name']); ?></td><td><?php echo mfg_str_num($row['requested_qty']); ?></td><td><?php echo mfg_str_num($row['transferred_qty']); ?></td><td><?php echo mfg_str_num($row['received_qty']); ?></td><td><?php echo mfg_str_num($row['pending_transfer_qty']); ?></td><td><?php echo mfg_str_num($row['pending_receipt_qty']); ?></td><td><?php echo htmlspecialchars($row['transfer_numbers']); ?></td><td><?php echo htmlspecialchars($row['receipt_numbers']); ?></td><td><?php echo htmlspecialchars($row['uom_name']); ?></td></tr><?php
} if(count((array)$rows)==0){ ?><tr><td colspan="12" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
