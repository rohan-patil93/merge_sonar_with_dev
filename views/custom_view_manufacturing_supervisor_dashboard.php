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
      <h3 class="mb-0 f-w-600">Manufacturing Supervisor Dashboard</h3>
      <p class="mb-0 mfg-role-note">Role: MANUFACTURING_SUPERVISOR | Shop-floor execution, material receipt, production and QC status</p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-blue"><?php echo (int)($production['active'] ?? 0); ?></h3><p class="m-b-0">Active Orders</p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-red"><?php echo (int)($production['hold'] ?? 0); ?></h3><p class="m-b-0">On Hold</p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-green"><?php echo number_format((float)($resources['manpower_required'] ?? 0)); ?></h3><p class="m-b-0">Manpower Required</p></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card comp-card"><div class="card-body"><h3 class="f-w-700 text-c-yellow"><?php echo $completionPercent; ?>%</h3><p class="m-b-0">Completion</p></div></div></div>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5>Planned vs Completed By Work Order</h5></div>
        <div class="card-block"><div id="supervisorWoBar" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i>Planned: <?php echo number_format((float)($summary['planned_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i>Completed: <?php echo number_format((float)($summary['completed_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5>Production Status Mix</h5></div>
        <div class="card-block"><div id="supervisorStatusPie" class="mfg-chart"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i>Active: <?php echo (int)($production['active'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-2"></i>Hold: <?php echo (int)($production['hold'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-3"></i>Completed: <?php echo (int)($production['completed'] ?? 0); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5>Overall Completion</h5></div>
        <div class="mfg-progress-wrap">
          <h3 class="f-w-700 text-c-green"><?php echo $completionPercent; ?>%</h3>
          <div class="mfg-progress-track"><div class="mfg-progress-fill" data-progress="<?php echo min($completionPercent, 100); ?>"></div></div>
        </div>
        <div class="card-block"><div id="supervisorCompletionPie" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i>Completed: <?php echo number_format((float)($summary['completed_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i>Balance: <?php echo number_format((float)($summary['balance_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5>Material Received View</h5></div>
        <div class="card-block"><div id="supervisorMaterialBar" class="mfg-chart-small"></div></div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i>Required: <?php echo number_format((float)($material['required_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-2"></i>Received: <?php echo number_format((float)($material['received_qty'] ?? 0)); ?></span>
          <span><i class="mfg-chart-color-3"></i>Shortage: <?php echo number_format((float)($material['shortage_qty'] ?? 0)); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5>Production Actions</h5></div>
        <div class="card-block">
          <div class="mfg-action-grid">
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-play-circle"></i>Start</a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-pause-circle"></i>Hold / Resume</a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_PRODUCT_NOTE_NEW"><i class="fas fa-check-circle"></i>Complete</a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_STOCK_TRANSFER_RECEIPT"><i class="fas fa-truck"></i>Receive Stock</a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_PROCESS_QUALITY_CHECK"><i class="fas fa-check-square"></i>QC Status</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5>Shop Floor Orders</h5></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr style="background-color: #eeeeee;"><th scope="col">Order No</th><th scope="col">Product</th><th scope="col">Planned</th><th scope="col">Completed</th><th scope="col">Progress</th><th scope="col">Production</th><th scope="col">Material Gap</th><th scope="col">Machines</th></tr>
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

<textarea aria-label="Mfg Dashboard Chart Data" id="mfgDashboardChartData" class="d-none"><?php echo htmlspecialchars(json_encode($supervisorChartData), ENT_QUOTES, 'UTF-8'); ?></textarea>
<script src="assets/js/custom_view_manufacturing_role_charts.js"></script>
