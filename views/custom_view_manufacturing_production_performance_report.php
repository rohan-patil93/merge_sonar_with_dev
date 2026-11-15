<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingproductionperformancereport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_pp_date($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfg_pp_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1300px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0"><?= __('Production Performance Report'); ?></h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_PRODUCTION_PERFORMANCE_REPORT"><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingProductionPerformanceReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col"><?= __('Production Note'); ?></th><th scope="col"><?= __('Date'); ?></th><th scope="col"><?= __('Month'); ?></th><th scope="col"><?= __('WO No'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('Requested Qty'); ?></th><th scope="col"><?= __('Produced Qty'); ?></th><th scope="col"><?= __('Wastage Qty'); ?></th><th scope="col"><?= __('Performance'); ?></th><th scope="col"><?= __('Wastage %'); ?></th><th scope="col"><?= __('Status'); ?></th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('Production Note' => mfg_pp_csv_safe($row['production_note_no'] ?? '-'), 'Date' => mfg_pp_csv_safe(mfg_pp_date($row['transaction_date'] ?? '')), 'Month' => mfg_pp_csv_safe($row['production_month'] ?? '-'), 'WO No' => mfg_pp_csv_safe($row['work_order_no'] ?? '-'), 'Product' => mfg_pp_csv_safe($row['product_name'] ?? '-'), 'Requested Qty' => mfg_pp_csv_safe($row['planned_qty'] ?? 0), 'Produced Qty' => mfg_pp_csv_safe($row['produced_qty'] ?? 0), 'Wastage Qty' => mfg_pp_csv_safe($row['wastage_qty'] ?? 0), 'Performance' => mfg_pp_csv_safe(($row['performance_percent'] ?? 0) . '%'), 'Wastage %' => mfg_pp_csv_safe(($row['wastage_percent'] ?? 0) . '%'), 'Status' => mfg_pp_csv_safe($row['status_name'] ?? '-'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['production_note_no']); ?></td><td><?php echo mfg_pp_date($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['production_month']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo htmlspecialchars($row['planned_qty']); ?></td><td><?php echo htmlspecialchars($row['produced_qty']); ?></td><td><?php echo htmlspecialchars($row['wastage_qty']); ?></td><td><?php echo htmlspecialchars($row['performance_percent']); ?>%</td><td><?php echo htmlspecialchars($row['wastage_percent']); ?>%</td><td><?php echo htmlspecialchars($row['status_name']); ?></td></tr><?php
}
if(count((array)$rows)==0){ ?><tr><td colspan="11" class="text-center text-muted"><?= __('No report data found.'); ?></td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
