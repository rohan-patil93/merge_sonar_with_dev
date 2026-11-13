<?php
unset($_SESSION['apply_js']['MANUFACTURING1_DASHBOARD']);

$url = API . "?mode=getmfgsupervisordashboarddetails";
$apires = callAPI($url);
$result = json_decode($apires, true);

$workOrders = $result['work_orders'] ?? array();
$summary = $result['summary'] ?? array();
$production = $summary['production'] ?? array();
$material = $summary['material'] ?? array();
$resources = $summary['resources'] ?? array();
$qc = $summary['qc'] ?? array();
$completionPercent = (float)($summary['completion_percent'] ?? 0);
$supervisorChartData = $result['chart_data'] ?? array();
?>

<link rel="stylesheet" type="text/css" href="assets/css/custom_view_manufacturing_dashboard.css">

<div class="page-body mfg-dashboard" data-dashboard="supervisor">
  <div class="card">
    <div class="card-header bg-primary">
      <h3 class="mb-0 f-w-600"><?= __('Manufacturing Supervisor Dashboard'); ?></h3>
      <p class="mb-0 mfg-role-note"><?= __('Role: MANUFACTURING_SUPERVISOR | Shop-floor execution, material receipt, production and QC status'); ?></p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-blue"><?php echo (int)($production['active'] ?? 0); ?></h3><p class="m-b-0"><?= __('Active Orders'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-red"><?php echo (int)($production['hold'] ?? 0); ?></h3><p class="m-b-0"><?= __('On Hold'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-green"><?php echo number_format((float)($resources['manpower_required'] ?? 0)); ?></h3><p class="m-b-0"><?= __('Manpower Required'); ?></p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-yellow"><?php echo $completionPercent; ?>%</h3><p class="m-b-0"><?= __('Completion'); ?></p></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5><?= __('Planned vs Completed By Work Order'); ?></h5></div>
        <div class="card-block"><div id="supervisorWoBar" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Planned'); ?>: <?php echo number_format((float)($summary['planned_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Completed'); ?>: <?php echo number_format((float)($summary['completed_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5><?= __('Production Status Mix'); ?></h5></div>
        <div class="card-block"><div id="supervisorStatusPie" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Active'); ?>: <?php echo (int)($production['active'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Hold'); ?>: <?php echo (int)($production['hold'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-3"></i><?= __('Completed'); ?>: <?php echo (int)($production['completed'] ?? 0); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5><?= __('Overall Completion'); ?></h5></div>
        <div class="mfg-progress-wrap">
          <h3 class="f-w-700 text-c-green"><?php echo $completionPercent; ?>%</h3>
          <div class="mfg-progress-track"><div class="mfg-progress-fill" data-progress="<?php echo min($completionPercent, 100); ?>"></div></div>
        </div>
        <div class="card-block"><div id="supervisorCompletionPie" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Completed'); ?>: <?php echo number_format((float)($summary['completed_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Balance'); ?>: <?php echo number_format((float)($summary['balance_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5><?= __('Material Received View'); ?></h5></div>
        <div class="card-block"><div id="supervisorMaterialBar" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Required'); ?>: <?php echo number_format((float)($material['required_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Received'); ?>: <?php echo number_format((float)($material['received_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-3"></i><?= __('Shortage'); ?>: <?php echo number_format((float)($material['shortage_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5><?= __('Production Actions'); ?></h5></div>
        <div class="card-block">
          <div class="mfg-action-grid">
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-play-circle"></i><?= __('Start'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-pause-circle"></i><?= __('Hold / Resume'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-check-circle"></i><?= __('Complete'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_STOCK_TRANSFER_RECEIPT"><i class="fas fa-truck"></i><?= __('Receive Stock'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_PROCESS_QUALITY_CHECK"><i class="fas fa-check-square"></i><?= __('QC Status'); ?></a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5><?= __('Shop Floor Orders'); ?></h5></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr style="background-color: #eeeeee;"><th scope="col"><?= __('Order No'); ?></th><th scope="col"><?= __('Product'); ?></th><th scope="col"><?= __('Planned'); ?></th><th scope="col"><?= __('Completed'); ?></th><th scope="col"><?= __('Progress'); ?></th><th scope="col"><?= __('Production'); ?></th><th scope="col"><?= __('Material Gap'); ?></th><th scope="col"><?= __('Machines'); ?></th></tr>
              <?php foreach ($workOrders as $wo) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($wo['transaction_no'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($wo['product_name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($wo['planned_qty'] ?? '0'); ?></td>
                  <td><?php echo htmlspecialchars($wo['completed_qty'] ?? '0'); ?></td>
                  <td><?php echo htmlspecialchars($wo['progress_percent'] ?? '0'); ?>%</td>
                  <td><?php echo htmlspecialchars($wo['production_status_name'] ?? ''); ?></td>
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

<textarea aria-label="<?= htmlspecialchars(__('Mfg Dashboard Chart Data'), ENT_QUOTES, 'UTF-8'); ?>" id="mfgDashboardChartData" class="d-none"><?php echo htmlspecialchars(json_encode($supervisorChartData), ENT_QUOTES, 'UTF-8'); ?></textarea>
<script src="assets/js/custom_view_manufacturing_role_charts.js"></script>
