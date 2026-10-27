<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingworkorderstatusreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_wos_date($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfg_wos_badge($row){ $bg = $row['status_bg'] ?: '#e5e7eb'; $color = $row['status_text_color'] ?: '#111827'; return '<span class="badge" style="background-color:'.htmlspecialchars($bg).';color:'.htmlspecialchars($color).';font-weight:600;">'.htmlspecialchars($row['status_name'] ?: 'Unknown').'</span>';
}
function mfg_wos_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1100px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0">Work Order Status Report</h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_WORK_ORDER_STATUS_REPORT"><div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingWorkOrderStatusReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col">WO No</th><th scope="col">Date</th><th scope="col">Product</th><th scope="col">Planned Qty</th><th scope="col">Completed Qty</th><th scope="col">Progress</th><th scope="col">Status</th><th scope="col">Actual Start</th><th scope="col">Actual End</th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('WO No' => mfg_wos_csv_safe($row['transaction_no'] ?? '-'), 'Date' => mfg_wos_csv_safe(mfg_wos_date($row['transaction_date'] ?? '')), 'Product' => mfg_wos_csv_safe($row['product_name'] ?? '-'), 'Planned Qty' => mfg_wos_csv_safe($row['planned_qty'] ?? 0), 'Completed Qty' => mfg_wos_csv_safe($row['completed_qty'] ?? 0), 'Progress' => mfg_wos_csv_safe(($row['progress_percent'] ?? 0) . '%'), 'Status' => mfg_wos_csv_safe($row['status_name'] ?: 'Unknown'), 'Actual Start' => mfg_wos_csv_safe(mfg_wos_date($row['actual_start_date'] ?? '')), 'Actual End' => mfg_wos_csv_safe(mfg_wos_date($row['actual_end_date'] ?? '')), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['transaction_no']); ?></td><td><?php echo mfg_wos_date($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo htmlspecialchars($row['planned_qty']); ?></td><td><?php echo htmlspecialchars($row['completed_qty']); ?></td><td><?php echo htmlspecialchars($row['progress_percent']); ?>%</td><td><?php echo mfg_wos_badge($row); ?></td><td><?php echo mfg_wos_date($row['actual_start_date']); ?></td><td><?php echo mfg_wos_date($row['actual_end_date']); ?></td></tr><?php
} if(count((array)$rows)==0){ ?><tr><td colspan="9" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
