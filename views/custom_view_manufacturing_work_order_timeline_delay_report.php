<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingworkordertimelinedelayreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_tl_date($value){ return ($value == '' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00') ? '-' : date('d-m-Y H:i', strtotime($value));
}
function mfg_tl_badge($row){ $bg = $row['status_bg'] ?: '#e5e7eb'; $color = $row['status_text_color'] ?: '#111827'; return '<span class="badge" style="background-color:'.htmlspecialchars($bg).';color:'.htmlspecialchars($color).';font-weight:600;">'.htmlspecialchars($row['status_name'] ?: 'Unknown').'</span>';
}
function mfg_tl_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1700px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0">Work Order Timeline / Delay Report</h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_WORK_ORDER_TIMELINE_DELAY_REPORT"><div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingWorkOrderTimelineDelayReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col">WO No</th><th scope="col">Product</th><th scope="col">WO Date</th><th scope="col">Stock Request</th><th scope="col">First Transfer</th><th scope="col">First Receipt</th><th scope="col">Production Start</th><th scope="col">Last Hold</th><th scope="col">Production Complete</th><th scope="col">Production Note</th><th scope="col">QC</th><th scope="col">Planned End</th><th scope="col">Actual End</th><th scope="col">Delay Days</th><th scope="col">Status</th></tr>
<?php foreach($rows as $row){
    $transferInfo = ($row['first_transfer_no'] ?: '-') . ' ' . mfg_tl_date($row['first_transfer_date']) . (!empty($row['transfer_count']) ? ' (' . (int)$row['transfer_count'] . ')' : '');
    $receiptInfo = ($row['first_receipt_no'] ?: '-') . ' ' . mfg_tl_date($row['first_receipt_date']) . (!empty($row['receipt_count']) ? ' (' . (int)$row['receipt_count'] . ')' : '');
    $csvdata[] = array('WO No' => mfg_tl_csv_safe($row['transaction_no'] ?? '-'), 'Product' => mfg_tl_csv_safe($row['product_name'] ?? '-'), 'WO Date' => mfg_tl_csv_safe(mfg_tl_date($row['transaction_date'] ?? '')), 'Stock Request' => mfg_tl_csv_safe(($row['stock_request_no'] ?: '-') . ' ' . mfg_tl_date($row['stock_request_date'] ?? '')), 'First Transfer' => mfg_tl_csv_safe($transferInfo), 'First Receipt' => mfg_tl_csv_safe($receiptInfo), 'Production Start' => mfg_tl_csv_safe(mfg_tl_date($row['production_start_time'] ?? '')), 'Last Hold' => mfg_tl_csv_safe(mfg_tl_date($row['last_hold_time'] ?? '')), 'Production Complete' => mfg_tl_csv_safe(mfg_tl_date($row['production_complete_time'] ?? '')), 'Production Note' => mfg_tl_csv_safe(($row['production_note_no'] ?: '-') . ' ' . mfg_tl_date($row['production_note_date'] ?? '')), 'QC' => mfg_tl_csv_safe(($row['qc_status'] ?: '-') . ' ' . mfg_tl_date($row['qc_date'] ?? '')), 'Planned End' => mfg_tl_csv_safe(mfg_tl_date($row['planned_end_date'] ?? '')), 'Actual End' => mfg_tl_csv_safe(mfg_tl_date($row['actual_end_date'] ?? '')), 'Delay Days' => mfg_tl_csv_safe(max((int)$row['delay_days'], 0)), 'Status' => mfg_tl_csv_safe($row['status_name'] ?: 'Unknown'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['transaction_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo mfg_tl_date($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['stock_request_no'] ?: '-'); ?><br><small><?php echo mfg_tl_date($row['stock_request_date']); ?></small></td><td><?php echo htmlspecialchars($row['first_transfer_no'] ?: '-'); ?><br><small><?php echo mfg_tl_date($row['first_transfer_date']); ?><?php echo !empty($row['transfer_count']) ? ' (' . (int)$row['transfer_count'] . ')' : ''; ?></small></td><td><?php echo htmlspecialchars($row['first_receipt_no'] ?: '-'); ?><br><small><?php echo mfg_tl_date($row['first_receipt_date']); ?><?php echo !empty($row['receipt_count']) ? ' (' . (int)$row['receipt_count'] . ')' : ''; ?></small></td><td><?php echo mfg_tl_date($row['production_start_time']); ?></td><td><?php echo mfg_tl_date($row['last_hold_time']); ?></td><td><?php echo mfg_tl_date($row['production_complete_time']); ?></td><td><?php echo htmlspecialchars($row['production_note_no'] ?: '-'); ?><br><small><?php echo mfg_tl_date($row['production_note_date']); ?></small></td><td><?php echo htmlspecialchars($row['qc_status'] ?: '-'); ?><br><small><?php echo mfg_tl_date($row['qc_date']); ?></small></td><td><?php echo mfg_tl_date($row['planned_end_date']); ?></td><td><?php echo mfg_tl_date($row['actual_end_date']); ?></td><td><?php echo max((int)$row['delay_days'], 0); ?></td><td><?php echo mfg_tl_badge($row); ?></td></tr><?php
} if(count((array)$rows)==0){ ?><tr><td colspan="15" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
