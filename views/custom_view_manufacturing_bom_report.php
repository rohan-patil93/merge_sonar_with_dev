<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingbomreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfgBomNum($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfgBomDate($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfgBomCsvSafe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1200px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body"><div class="card"><div class="card-header bg-primary text-white"><h4 class="mb-0"><?= __('BOM Report'); ?></h4></div><div class="card-body">
<form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_BOM_REPORT"><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div><div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div><div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingBomReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div></form>
<div class="table-responsive manufacturing-report-scroll"><table class="table table-bordered table-sm"><tr style="background-color: #eeeeee;"><th scope="col"><?= __('BOM No'); ?></th><th scope="col"><?= __('Date'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('BOM Qty'); ?></th><th scope="col"><?= __('Material'); ?></th><th scope="col"><?= __('Material Qty'); ?></th><th scope="col"><?= __('Scrap'); ?></th><th scope="col"><?= __('Rate'); ?></th><th scope="col"><?= __('Amount'); ?></th><th scope="col"><?= __('BOM UOM'); ?></th></tr>
<?php foreach($rows as $row){
    $csvdata[] = array('BOM No' => mfgBomCsvSafe($row['transaction_no'] ?? '-'), 'Date' => mfgBomCsvSafe(mfgBomDate($row['transaction_date'] ?? '')), 'Product' => mfgBomCsvSafe($row['product_name'] ?? '-'), 'BOM Qty' => mfgBomCsvSafe(mfgBomNum($row['bom_quantity'] ?? 0)), 'Material' => mfgBomCsvSafe($row['material_name'] ?? '-'), 'Material Qty' => mfgBomCsvSafe(mfgBomNum($row['material_qty'] ?? 0)), 'Scrap' => mfgBomCsvSafe(mfgBomNum($row['scrap'] ?? 0)), 'Rate' => mfgBomCsvSafe(mfgBomNum($row['rate'] ?? 0)), 'Amount' => mfgBomCsvSafe(mfgBomNum($row['amount'] ?? 0)), 'UOM' => mfgBomCsvSafe($row['uom_name'] ?? '-'), 'data' => array());
?><tr><td><?php echo htmlspecialchars($row['transaction_no']); ?></td><td><?php echo mfgBomDate($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo mfgBomNum($row['bom_quantity']); ?></td><td><?php echo htmlspecialchars($row['material_name']); ?></td><td><?php echo mfgBomNum($row['material_qty']); ?></td><td><?php echo mfgBomNum($row['scrap']); ?></td><td><?php echo mfgBomNum($row['rate']); ?></td><td><?php echo mfgBomNum($row['amount']); ?></td><td><?php echo htmlspecialchars($row['uom_name']); ?></td></tr><?php
}
if(count((array)$rows)==0){ ?><tr><td colspan="10" class="text-center text-muted"><?= __('No report data found.'); ?></td></tr><?php
} ?>
<?php $_SESSION['download_data'] = $csvdata; ?>
</table></div></div></div></div>
