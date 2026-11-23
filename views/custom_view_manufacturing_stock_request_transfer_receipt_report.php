<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingstockrequesttransferreceiptreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfgStrNum($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfgStrDate($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfgStrCsvSafe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1400px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0"><?= __('Stock Request / Transfer / Receipt Report'); ?></h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_STOCK_REQUEST_TRANSFER_RECEIPT_REPORT"><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingStockRequestTransferReceiptReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col"><?= __('Stock Request'); ?></th><th scope="col"><?= __('Request Date'); ?></th><th scope="col"><?= __('WO No'); ?></th><th scope="col"><?= __('Material'); ?></th><th scope="col"><?= __('Requested Qty'); ?></th><th scope="col"><?= __('Transferred Qty'); ?></th><th scope="col"><?= __('Received Qty'); ?></th><th scope="col"><?= __('Pending Transfer'); ?></th><th scope="col"><?= __('Pending Receipt'); ?></th><th scope="col"><?= __('Transfers'); ?></th><th scope="col"><?= __('Receipts'); ?></th><th scope="col"><?= __('UOM'); ?></th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('Stock Request' => mfgStrCsvSafe($row['stock_request_no'] ?? '-'), 'Request Date' => mfgStrCsvSafe(mfgStrDate($row['stock_request_date'] ?? '')), 'WO No' => mfgStrCsvSafe($row['work_order_no'] ?? '-'), 'Material' => mfgStrCsvSafe($row['material_name'] ?? '-'), 'Requested Qty' => mfgStrCsvSafe(mfgStrNum($row['requested_qty'] ?? 0)), 'Transferred Qty' => mfgStrCsvSafe(mfgStrNum($row['transferred_qty'] ?? 0)), 'Received Qty' => mfgStrCsvSafe(mfgStrNum($row['received_qty'] ?? 0)), 'Pending Transfer' => mfgStrCsvSafe(mfgStrNum($row['pending_transfer_qty'] ?? 0)), 'Pending Receipt' => mfgStrCsvSafe(mfgStrNum($row['pending_receipt_qty'] ?? 0)), 'Transfers' => mfgStrCsvSafe($row['transfer_numbers'] ?? '-'), 'Receipts' => mfgStrCsvSafe($row['receipt_numbers'] ?? '-'), 'UOM' => mfgStrCsvSafe($row['uom_name'] ?? '-'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['stock_request_no']); ?></td><td><?php echo mfgStrDate($row['stock_request_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['material_name']); ?></td><td><?php echo mfgStrNum($row['requested_qty']); ?></td><td><?php echo mfgStrNum($row['transferred_qty']); ?></td><td><?php echo mfgStrNum($row['received_qty']); ?></td><td><?php echo mfgStrNum($row['pending_transfer_qty']); ?></td><td><?php echo mfgStrNum($row['pending_receipt_qty']); ?></td><td><?php echo htmlspecialchars($row['transfer_numbers']); ?></td><td><?php echo htmlspecialchars($row['receipt_numbers']); ?></td><td><?php echo htmlspecialchars($row['uom_name']); ?></td></tr><?php
}
if(count((array)$rows)==0){ ?><tr><td colspan="12" class="text-center text-muted"><?= __('No report data found.'); ?></td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
