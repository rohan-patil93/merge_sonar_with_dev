<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingbomreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_bom_num($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfg_bom_date($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfg_bom_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1200px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0">BOM Report</h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_BOM_REPORT"><div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingBomReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col">BOM No</th><th scope="col">Date</th><th scope="col">Product</th><th scope="col">BOM Qty</th><th scope="col">Material</th><th scope="col">Material Qty</th><th scope="col">Scrap</th><th scope="col">Rate</th><th scope="col">Amount</th><th scope="col">BOM UOM</th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('BOM No' => mfg_bom_csv_safe($row['transaction_no'] ?? '-'), 'Date' => mfg_bom_csv_safe(mfg_bom_date($row['transaction_date'] ?? '')), 'Product' => mfg_bom_csv_safe($row['product_name'] ?? '-'), 'BOM Qty' => mfg_bom_csv_safe(mfg_bom_num($row['bom_quantity'] ?? 0)), 'Material' => mfg_bom_csv_safe($row['material_name'] ?? '-'), 'Material Qty' => mfg_bom_csv_safe(mfg_bom_num($row['material_qty'] ?? 0)), 'Scrap' => mfg_bom_csv_safe(mfg_bom_num($row['scrap'] ?? 0)), 'Rate' => mfg_bom_csv_safe(mfg_bom_num($row['rate'] ?? 0)), 'Amount' => mfg_bom_csv_safe(mfg_bom_num($row['amount'] ?? 0)), 'UOM' => mfg_bom_csv_safe($row['uom_name'] ?? '-'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['transaction_no']); ?></td><td><?php echo mfg_bom_date($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo mfg_bom_num($row['bom_quantity']); ?></td><td><?php echo htmlspecialchars($row['material_name']); ?></td><td><?php echo mfg_bom_num($row['material_qty']); ?></td><td><?php echo mfg_bom_num($row['scrap']); ?></td><td><?php echo mfg_bom_num($row['rate']); ?></td><td><?php echo mfg_bom_num($row['amount']); ?></td><td><?php echo htmlspecialchars($row['uom_name']); ?></td></tr><?php
} if(count((array)$rows)==0){ ?><tr><td colspan="10" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
