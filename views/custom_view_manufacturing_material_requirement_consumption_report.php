<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingmaterialrequirementconsumptionreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$rows = json_decode(callAPI($url), true)['data'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_mrc_num($value)
{
    return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfg_mrc_date($value)
{
    return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function csv_safe($value)
{
    $value = (string)($value ?? '-');

    // replace commas only
    return str_replace(',', ' ', $value);
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1400px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= htmlspecialchars(__('Material Requirement & Consumption Report'), ENT_QUOTES, 'UTF-8'); ?></h4>
        </div>
        <div class="card-body">
            <form method="get" class="row g-2 mb-3"><input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_MATERIAL_REQUIREMENT_CONSUMPTION_REPORT">
                <div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div>
                <div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div>
                <div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div>
                <div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingMaterialRequirementConsumptionReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div>
            </form>
            <div class="table-responsive manufacturing-report-scroll">
                <table class="table table-bordered table-sm">
                    <tr style="background-color: #eeeeee;">
                        <th scope="col"><?= __('Production Notes'); ?></th>
                        <th scope="col"><?= __('Last Production Date'); ?></th>
                        <th scope="col"><?= __('WO No'); ?></th>
                        <th scope="col"><?= __('Product'); ?></th>
                        <th scope="col"><?= __('Material'); ?></th>
                        <th scope="col"><?= __('Required'); ?></th>
                        <th scope="col"><?= __('Stock Requested'); ?></th>
                        <th scope="col"><?= __('Stock Received'); ?></th>
                        <th scope="col"><?= __('Production Received'); ?></th>
                        <th scope="col"><?= __('Consumed'); ?></th>
                        <th scope="col"><?= __('Wastage'); ?></th>
                        <th scope="col"><?= __('Variance'); ?></th>
                        <th scope="col"><?= __('UOM'); ?></th>
                    </tr>
                    <?php foreach ($rows as $row) {
                        $csvdata[] = [
                            'Production Notes' => csv_safe($row['production_note_no'] ?? '-'),
                            'Last Production Date' => csv_safe(mfg_mrc_date($row['transaction_date'] ?? '')),
                            'WO No' => csv_safe($row['work_order_no'] ?? '-'),
                            'Product' => csv_safe($row['product_name'] ?? '-'),
                            'Material' => csv_safe($row['material_name'] ?? '-'),
                            'Required' => csv_safe(mfg_mrc_num($row['required_qty'] ?? 0)),
                            'Stock Requested' => csv_safe(mfg_mrc_num($row['stock_request_qty'] ?? 0)),
                            'Stock Received' => csv_safe(mfg_mrc_num($row['stock_receipts_qty'] ?? 0)),
                            'Production Received' => csv_safe(mfg_mrc_num($row['production_received_qty'] ?? 0)),
                            'Consumed' => csv_safe(mfg_mrc_num($row['consumed_qty'] ?? 0)),
                            'Wastage' => csv_safe(mfg_mrc_num($row['wastage_qty'] ?? 0)),
                            'Variance' => csv_safe(mfg_mrc_num($row['variance_qty'] ?? 0)),
                            'UOM' => csv_safe($row['uom_name'] ?? '-'),
                            'data' => []
                        ];
                    ?><tr>
                            <td><?php echo htmlspecialchars($row['production_note_no']); ?></td>
                            <td><?php echo mfg_mrc_date($row['transaction_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['work_order_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['material_name']); ?></td>
                            <td><?php echo mfg_mrc_num($row['required_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['stock_request_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['stock_receipts_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['production_received_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['consumed_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['wastage_qty']); ?></td>
                            <td><?php echo mfg_mrc_num($row['variance_qty']); ?></td>
                            <td><?php echo htmlspecialchars($row['uom_name']); ?></td>
                        </tr><?php }
                            if (count((array)$rows) == 0) { ?><tr>
                            <td colspan="13" class="text-center text-muted"><?= __('No report data found.'); ?></td>
                        </tr><?php } ?>
                    <?php $_SESSION['download_data'] = $csvdata; ?>
                </table>
            </div>
        </div>
    </div>
</div>
