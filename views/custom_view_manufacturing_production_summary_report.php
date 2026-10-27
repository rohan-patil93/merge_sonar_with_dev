<?php
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';
$url = API . "?mode=getmanufacturingproductionsummaryreport&from_date=" . urlencode($fromDate) . "&to_date=" . urlencode($toDate);
$result = json_decode(callAPI($url), true);
$rows = $result['data'] ?? array();
$summary = $result['summary'] ?? array();
$_SESSION['download_data'] = array();
$csvdata = array();
function mfg_ps_num($value){ return rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
}
function mfg_ps_date($value){ return ($value == '' || $value == '0000-00-00') ? '-' : date('d-m-Y', strtotime($value));
}
function mfg_ps_badge($row){ $bg = $row['status_bg'] ?: '#e5e7eb'; $color = $row['status_text_color'] ?: '#111827'; return '<span class="badge" style="background-color:'.htmlspecialchars($bg).';color:'.htmlspecialchars($color).';font-weight:600;">'.htmlspecialchars($row['status_name'] ?: 'Unknown').'</span>';
}
function mfg_ps_csv_safe($value){ return str_replace(',', ' ', (string)($value ?? '-'));
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
            <h4 class="mb-0">Production Summary Report</h4>
        </div>
        <div class="card-body">
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="option" value="COM_VIEW_MANUFACTURING_PRODUCTION_SUMMARY_REPORT">
                <div class="col-md-3"><input aria-label="From Date" type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" class="form-control"></div>
                <div class="col-md-3"><input aria-label="To Date" type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" class="form-control"></div>
                <div class="col-md-3"><button class="btn btn-primary" type="submit"><i class="ti-filter"></i> Apply</button></div>
                <div class="col-md-3 text-end"><a href="custom_view_download.php?page=ManufacturingProductionSummaryReport&format=csv" class="btn btn-success" target="_blank"><i class="ti-download"></i> Download CSV</a></div>
            </form>
            <div class="row">
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfg_ps_num($summary['total_work_orders'] ?? 0); ?></h4><p class="mb-0">Production Notes</p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfg_ps_num($summary['planned_qty'] ?? 0); ?></h4><p class="mb-0">Planned Qty</p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfg_ps_num($summary['produced_qty'] ?? 0); ?></h4><p class="mb-0">Produced Qty</p></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><h4><?php echo mfg_ps_num($summary['achievement_percent'] ?? 0); ?>%</h4><p class="mb-0">Achievement</p></div></div></div>
            </div>
            <div class="table-responsive manufacturing-report-scroll">
                <table class="table table-bordered table-sm">
                    <tr style="background-color: #eeeeee;"><th scope="col">Production Note</th><th scope="col">Date</th><th scope="col">WO No</th><th scope="col">Product</th><th scope="col">Requested</th><th scope="col">Produced</th><th scope="col">Wastage</th><th scope="col">Pending</th><th scope="col">Achievement</th><th scope="col">Status</th></tr>
                    <?php foreach($rows as $row){
                        $csvdata[] = array(
                            'Production Note' => mfg_ps_csv_safe($row['production_note_no'] ?? '-'),
                            'Date' => mfg_ps_csv_safe(mfg_ps_date($row['transaction_date'] ?? '')),
                            'WO No' => mfg_ps_csv_safe($row['work_order_no'] ?? '-'),
                            'Product' => mfg_ps_csv_safe($row['product_name'] ?? '-'),
                            'Requested' => mfg_ps_csv_safe(mfg_ps_num($row['planned_qty'] ?? 0)),
                            'Produced' => mfg_ps_csv_safe(mfg_ps_num($row['produced_qty'] ?? 0)),
                            'Wastage' => mfg_ps_csv_safe(mfg_ps_num($row['wastage_qty'] ?? 0)),
                            'Pending' => mfg_ps_csv_safe(mfg_ps_num($row['pending_qty'] ?? 0)),
                            'Achievement' => mfg_ps_csv_safe(mfg_ps_num($row['achievement_percent'] ?? 0) . '%'),
                            'Status' => mfg_ps_csv_safe($row['status_name'] ?: 'Unknown'),
                            'data' => array()
                        );
                    ?>
                        <tr><td><?php echo htmlspecialchars($row['production_note_no']); ?></td><td><?php echo mfg_ps_date($row['transaction_date']); ?></td><td><?php echo htmlspecialchars($row['work_order_no']); ?></td><td><?php echo htmlspecialchars($row['product_name']); ?></td><td><?php echo mfg_ps_num($row['planned_qty']); ?></td><td><?php echo mfg_ps_num($row['produced_qty']); ?></td><td><?php echo mfg_ps_num($row['wastage_qty']); ?></td><td><?php echo mfg_ps_num($row['pending_qty']); ?></td><td><?php echo mfg_ps_num($row['achievement_percent']); ?>%</td><td><?php echo mfg_ps_badge($row); ?></td></tr>
                    <?php } if(count((array)$rows)==0){ ?><tr><td colspan="10" class="text-center text-muted">No report data found.</td></tr><?php
} ?>
                    <?php $_SESSION['download_data'] = $csvdata; ?>
                </table>
            </div>
        </div>
    </div>
</div>
