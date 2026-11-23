<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingproductionsummaryreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$result = json_decode(callAPI($url), true);
$rows = $result['data'] ?? array();
$summary = $result['summary'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfgPsNum($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfgPsDate($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfgPsBadge($row){ $bg = $row['status_bg'] ?: '#e5e7eb'; $color = $row['status_text_color'] ?: '#111827'; return '<span class="badge" style="background-color:'.htmlspecialchars($bg).';color:'.htmlspecialchars($color).';font-weight:600;">'.htmlspecialchars($row['status_name'] ?: 'Unknown').'</span>';
}
function mfgPsCsvSafe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
}
?>
<style>
.manufacturing-report-scroll { overflow-x: auto; width: 100%; padding-bottom: 8px; }
.manufacturing-report-scroll table { min-width: 1200px; white-space: nowrap; }
.manufacturing-report-scroll th, .manufacturing-report-scroll td { vertical-align: middle; }
</style>
<div class="page-body">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= __('Production Summary Report'); ?></h4>
        </div>
        <div class="card-body">
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_PRODUCTION_SUMMARY_REPORT">
                <div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div>
                <div class="col-md-3"><input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div>
                <div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> <?= __('Apply'); ?></button></div>
                <div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingProductionSummaryReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> <?= __('Download CSV'); ?></a></div>
            </form>
            <div class="row">
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfgPsNum($summary['total_work_orders'] ?? 0); ?></h4><p class="mb-0"><?= __('Production Notes'); ?></p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfgPsNum($summary['planned_qty'] ?? 0); ?></h4><p class="mb-0"><?= __('Planned Qty'); ?></p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfgPsNum($summary['produced_qty'] ?? 0); ?></h4><p class="mb-0"><?= __('Produced Qty'); ?></p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfgPsNum($summary['achievement_percent'] ?? 0); ?>%</h4><p class="mb-0"><?= __('Achievement'); ?></p></div></div></div>
            </div>
            <div class="table-responsive manufacturing-report-scroll">
                <table class="table table-bordered table-sm">
                    <tr style="background-color: #eeeeee;"><th scope="col"><?= __('Production Note'); ?></th><th scope="col"><?= __('Date'); ?></th><th scope="col"><?= __('WO No'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('Requested'); ?></th><th scope="col"><?= __('Produced'); ?></th><th scope="col"><?= __('Wastage'); ?></th><th scope="col"><?= __('Pending'); ?></th><th scope="col"><?= __('Achievement'); ?></th><th scope="col"><?= __('Status'); ?></th></tr>
                    <?php foreach($rows as $row){
                        $csvdata[] = array(
                            'Production Note' => mfgPsCsvSafe($row['production_note_no'] ?? '-'),
                            'Date' => mfgPsCsvSafe(mfgPsDate($row['transaction_date'] ?? '')),
                            'WO No' => mfgPsCsvSafe($row['work_order_no'] ?? '-'),
                            'Product' => mfgPsCsvSafe($row['product_name'] ?? '-'),
                            'Requested' => mfgPsCsvSafe(mfgPsNum($row['planned_qty'] ?? 0)),
                            'Produced' => mfgPsCsvSafe(mfgPsNum($row['produced_qty'] ?? 0)),
                            'Wastage' => mfgPsCsvSafe(mfgPsNum($row['wastage_qty'] ?? 0)),
                            'Pending' => mfgPsCsvSafe(mfgPsNum($row['pending_qty'] ?? 0)),
                            'Achievement' => mfgPsCsvSafe(mfgPsNum($row['achievement_percent'] ?? 0) . '%'),
                            'Status' => mfgPsCsvSafe($row['status_name'] ?: 'Unknown'),
                            'data' => array()
                        );
                    ?>
                        <tr><td><?php echo htmlspecialchars($row['production_note_no']); ?></td><td><?php echo mfgPsDate($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo mfgPsNum($row['planned_qty']); ?></td><td><?php echo mfgPsNum($row['produced_qty']); ?></td><td><?php echo mfgPsNum($row['wastage_qty']); ?></td><td><?php echo mfgPsNum($row['pending_qty']); ?></td><td><?php echo mfgPsNum($row['achievement_percent']); ?>%</td><td><?php echo mfgPsBadge($row); ?></td></tr>
                    <?php }
                    if(count((array)$rows)==0){ ?><tr><td colspan="10" class="text-center text-muted"><?= __('No report data found.'); ?></td></tr><?php
} ?>
                    <?php $_SESSION['download_data'] = $csvdata; ?>
                </table>
            </div>
        </div>
    </div>
</div>
