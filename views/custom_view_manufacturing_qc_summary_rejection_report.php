<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingqcsummaryrejectionreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfgQcDate($value){ return ($value == '' || $value == '0000-00-00 00:00:00') ? '-' : date('d-m-Y H:i', strtotime($value));
}
function mfgQcCsvSafe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1600px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0"><?= htmlspecialchars(__('QC Summary & Rejection Report'), ENT_QUOTES, 'UTF-8'); ?></h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_QC_SUMMARY_REJECTION_REPORT"><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingQcSummaryRejectionReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col"><?= __('QC ID'); ?></th><th scope="col"><?= __('Production Note'); ?></th><th scope="col"><?= __('Production Date'); ?></th><th scope="col"><?= __('WO No'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('Produced Qty'); ?></th><th scope="col"><?= __('Serial/Lot'); ?></th><th scope="col"><?= __('Lot Qty'); ?></th><th scope="col"><?= __('QC Status'); ?></th><th scope="col"><?= __('Required'); ?></th><th scope="col"><?= __('Checked'); ?></th><th scope="col"><?= __('Failed'); ?></th><th scope="col"><?= __('Inspected Date'); ?></th><th scope="col"><?= __('Remarks'); ?></th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('QC ID' => mfgQcCsvSafe($row['qc_id'] ?? '-'), 'Production Note' => mfgQcCsvSafe($row['production_note_no'] ?? '-'), 'Production Date' => mfgQcCsvSafe(mfgQcDate($row['production_note_date'] ?? '')), 'WO No' => mfgQcCsvSafe($row['work_order_no'] ?? '-'), 'Product' => mfgQcCsvSafe($row['product_name'] ?? '-'), 'Produced Qty' => mfgQcCsvSafe($row['produced_qty'] ?? 0), 'Serial/Lot' => mfgQcCsvSafe($row['serial_lot_no'] ?? '-'), 'Lot Qty' => mfgQcCsvSafe($row['lot_qty'] ?? 0), 'QC Status' => mfgQcCsvSafe($row['qc_status'] ?? '-'), 'Required' => mfgQcCsvSafe($row['required_count'] ?? 0), 'Checked' => mfgQcCsvSafe($row['checked_count'] ?? 0), 'Failed' => mfgQcCsvSafe($row['failed_count'] ?? 0), 'Inspected Date' => mfgQcCsvSafe(mfgQcDate($row['inspected_date'] ?? '')), 'Remarks' => mfgQcCsvSafe(($row['item_remarks'] ?? '') ?: ($row['qc_remarks'] ?? '-')), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['qc_id']); ?></td><td><?php echo htmlspecialchars($row['production_note_no']); ?></td><td><?php echo mfgQcDate($row['production_note_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo htmlspecialchars($row['produced_qty']); ?></td><td><?php echo htmlspecialchars($row['serial_lot_no']); ?></td><td><?php echo htmlspecialchars($row['lot_qty']); ?></td><td><?php echo htmlspecialchars($row['qc_status']); ?></td><td><?php echo htmlspecialchars($row['required_count']); ?></td><td><?php echo htmlspecialchars($row['checked_count']); ?></td><td><?php echo htmlspecialchars($row['failed_count']); ?></td><td><?php echo mfgQcDate($row['inspected_date']); ?></td><td><?php echo htmlspecialchars($row['item_remarks'] ?: $row['qc_remarks']); ?></td></tr><?php
}
if(count((array)$rows)==0){ ?><tr><td colspan="14" class="text-center text-muted"><?= __('No report data found.'); ?></td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
