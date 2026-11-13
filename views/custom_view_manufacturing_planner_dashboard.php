<?php
unset($_SESSION['apply_js']['MANUFACTURING1_DASHBOARD']);

$url = API . "?mode=getmfgplannerdashboarddetails";
$apires = callAPI($url);
$result = json_decode($apires, true);

$workOrders = $result['work_orders'] ?? array();
$summary = $result['summary'] ?? array();
$material = $summary['material'] ?? array();
$resources = $summary['resources'] ?? array();
$plannerChartData = $result['chart_data'] ?? array();
?>

<link rel="stylesheet" type="text/css" href="assets/css/custom_view_manufacturing_dashboard.css">

<div class="page-body mfg-dashboard" data-dashboard="planner">
  <div class="card">
    <div class="card-header bg-primary">
      <h3 class="mb-0 f-w-600"><?= __('Manufacturing Planner Dashboard'); ?></h3>
      <p class="mb-0 mfg-role-note"><?= __('Role: MANUFACTURING_PLANNER | Forecast, firm order, BOM, work order and material readiness'); ?></p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-blue"><?php echo (int)($summary['work_orders_planned'] ?? 0); ?></h3><p class="m-b-0"><?= __('Open Plan WOs'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-green"><?php echo number_format((float)($summary['planned_qty'] ?? 0)); ?></h3><p class="m-b-0"><?= __('Planned Qty'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-yellow"><?php echo number_format((float)($resources['manpower_required'] ?? 0)); ?></h3><p class="m-b-0"><?= __('Manpower Demand'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-red"><?php echo number_format((float)($material['shortage_qty'] ?? 0)); ?></h3><p class="m-b-0"><?= __('Material Shortage'); ?></p></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5><?= __('Plan Conversion Funnel'); ?></h5></div>
        <div class="card-block"><div id="plannerFunnelBar" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Forecast'); ?>: <?php echo number_format((float)($plannerChartData['planningSeries'][0] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Firm Order'); ?>: <?php echo number_format((float)($plannerChartData['planningSeries'][1] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-3"></i><?= __('Work Order'); ?>: <?php echo number_format((float)($plannerChartData['planningSeries'][2] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-4"></i><?= __('Produced'); ?>: <?php echo number_format((float)($plannerChartData['planningSeries'][3] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5><?= __('Material Readiness'); ?></h5></div>
        <div class="card-block"><div id="plannerReadinessPie" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Required'); ?>: <?php echo number_format((float)($material['required_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Received'); ?>: <?php echo number_format((float)($material['received_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-3"></i><?= __('Shortage'); ?>: <?php echo number_format((float)($material['shortage_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5><?= __('Demand vs Produced Trend'); ?></h5></div>
        <div class="card-block"><div id="plannerTrendLine" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Demand'); ?>: <?php echo number_format(array_sum($plannerChartData['trendRequest'] ?? array())); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Produced'); ?>: <?php echo number_format(array_sum($plannerChartData['trendProduced'] ?? array())); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5><?= __('Top Planned Products'); ?></h5></div>
        <div class="card-block"><div id="plannerProductBar" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <?php $productColorIndex = 1; foreach (($plannerChartData['productLabels'] ?? array()) as $productIndex => $productName) { $productQty = $plannerChartData['productSeries'][$productIndex] ?? 0; ?>
            <span><i class="mfg-chart-color-<?php echo (($productColorIndex - 1) % 4) + 1; ?>"></i><?php echo htmlspecialchars($productName); ?>: <?php echo number_format($productQty); ?></span>
          <?php $productColorIndex++;
} ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5><?= __('Planning Actions'); ?></h5></div>
        <div class="card-block">
          <div class="mfg-action-grid">
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_FORECAST"><i class="fas fa-chart-line"></i><?= __('Forecast'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_FIRM_ORDERS"><i class="fas fa-calendar-check"></i><?= __('Firm Order'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_BOMS"><i class="fas fa-cubes"></i><?= __('BOM'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_WORK_ORDERS"><i class="fas fa-cogs"></i><?= __('Work Order'); ?></a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5><?= __('Upcoming Work Order Plan'); ?></h5></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr style="background-color: #eeeeee;"><th scope="col"><?= __('Order No'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('Planned Qty'); ?></th><th scope="col"><?= __('Start'); ?></th><th scope="col"><?= __('End'); ?></th><th scope="col"><?= __('Approval'); ?></th><th scope="col"><?= __('Shortage'); ?></th><th scope="col"><?= __('Machines'); ?></th></tr>
              <?php foreach ($workOrders as $wo) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($wo['transaction_no'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($wo['product_name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($wo['planned_qty'] ?? '0'); ?></td>
                  <td><?php echo !empty($wo['planned_start_date']) ? date('d-m-Y', strtotime($wo['planned_start_date'])) : ''; ?></td>
                  <td><?php echo !empty($wo['planned_end_date']) ? date('d-m-Y', strtotime($wo['planned_end_date'])) : ''; ?></td>
                  <td><?php echo htmlspecialchars($wo['approval_status_name'] ?? ''); ?></td>
                  <td><?php echo number_format((float)($wo['material_shortage_qty'] ?? 0)); ?></td>
                  <td><?php echo (int)($wo['machine_count'] ?? 0); ?></td>
                </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<textarea aria-label="<?= htmlspecialchars(__('Mfg Dashboard Chart Data'), ENT_QUOTES, 'UTF-8'); ?>" id="mfgDashboardChartData" class="d-none"><?php echo htmlspecialchars(json_encode($plannerChartData), ENT_QUOTES, 'UTF-8'); ?></textarea>
<script src="assets/js/custom_view_manufacturing_role_charts.js"></script>
