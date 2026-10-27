<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingqcsummaryrejectionreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_qc_date($value){ return ($value == '' || $value == '0000-00-00 00:00:00') ? '-' : date('d-m-Y H:i', strtotime($value));
}
function mfg_qc_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1600px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0">QC Summary & Rejection Report</h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_QC_SUMMARY_REJECTION_REPORT"><div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingQcSummaryRejectionReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col">QC ID</th><th scope="col">Production Note</th><th scope="col">Production Date</th><th scope="col">WO No</th><th scope="col">Product</th><th scope="col">Produced Qty</th><th scope="col">Serial/Lot</th><th scope="col">Lot Qty</th><th scope="col">QC Status</th><th scope="col">Required</th><th scope="col">Checked</th><th scope="col">Failed</th><th scope="col">Inspected Date</th><th scope="col">Remarks</th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('QC ID' => mfg_qc_csv_safe($row['qc_id'] ?? '-'), 'Production Note' => mfg_qc_csv_safe($row['production_note_no'] ?? '-'), 'Production Date' => mfg_qc_csv_safe(mfg_qc_date($row['production_note_date'] ?? '')), 'WO No' => mfg_qc_csv_safe($row['work_order_no'] ?? '-'), 'Product' => mfg_qc_csv_safe($row['product_name'] ?? '-'), 'Produced Qty' => mfg_qc_csv_safe($row['produced_qty'] ?? 0), 'Serial/Lot' => mfg_qc_csv_safe($row['serial_lot_no'] ?? '-'), 'Lot Qty' => mfg_qc_csv_safe($row['lot_qty'] ?? 0), 'QC Status' => mfg_qc_csv_safe($row['qc_status'] ?? '-'), 'Required' => mfg_qc_csv_safe($row['required_count'] ?? 0), 'Checked' => mfg_qc_csv_safe($row['checked_count'] ?? 0), 'Failed' => mfg_qc_csv_safe($row['failed_count'] ?? 0), 'Inspected Date' => mfg_qc_csv_safe(mfg_qc_date($row['inspected_date'] ?? '')), 'Remarks' => mfg_qc_csv_safe(($row['item_remarks'] ?? '') ?: ($row['qc_remarks'] ?? '-')), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['qc_id']); ?></td><td><?php echo htmlspecialchars($row['production_note_no']); ?></td><td><?php echo mfg_qc_date($row['production_note_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo htmlspecialchars($row['produced_qty']); ?></td><td><?php echo htmlspecialchars($row['serial_lot_no']); ?></td><td><?php echo htmlspecialchars($row['lot_qty']); ?></td><td><?php echo htmlspecialchars($row['qc_status']); ?></td><td><?php echo htmlspecialchars($row['required_count']); ?></td><td><?php echo htmlspecialchars($row['checked_count']); ?></td><td><?php echo htmlspecialchars($row['failed_count']); ?></td><td><?php echo mfg_qc_date($row['inspected_date']); ?></td><td><?php echo htmlspecialchars($row['item_remarks'] ?: $row['qc_remarks']); ?></td></tr><?php
} if(count((array)$rows)==0){ ?><tr><td colspan="14" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
