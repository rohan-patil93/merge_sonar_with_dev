<?php
unset($_SESSION['apply_js']['MANUFACTURING1_DASHBOARD']);

$url = API . "?mode=getmfgadmindashboarddetails";
$apires = callAPI($url);
$result = json_decode($apires, true);

$workOrders = $result['work_orders'] ?? array();
$summary = $result['summary'] ?? array();
$production = $summary['production'] ?? array();
$material = $summary['material'] ?? array();
$resources = $summary['resources'] ?? array();
$qc = $summary['qc'] ?? array();
$adminChartData = $result['chart_data'] ?? array();

$plannedQty = (float)($summary['planned_qty'] ?? 0);
$completedQty = (float)($summary['completed_qty'] ?? 0);
$completionPercent = $plannedQty > 0 ? round(($completedQty / $plannedQty) * 100, 2) : 0;
$operationHours = round(((float)($resources['operation_minutes'] ?? 0)) / 60, 2);
$qcTotal = (int)($qc['pending'] ?? 0) + (int)($qc['passed'] ?? 0) + (int)($qc['failed'] ?? 0);
$qcFailRate = $qcTotal > 0 ? round(((int)($qc['failed'] ?? 0) / $qcTotal) * 100, 2) : 0;

if (!function_exists('mfgAdminDashboardValue')) {
    function mfgAdminDashboardValue($value, $decimals = 0) {
        return number_format((float)$value, $decimals);
    }
}

if (!function_exists('mfgAdminDashboardDate')) {
    function mfgAdminDashboardDate($date) {
        return !empty($date) && $date != '0000-00-00' ? date('d-m-Y', strtotime($date)) : '-';
    }
}

$alerts = array();
foreach ((array)$workOrders as $wo) {
    if (!empty($wo['is_delayed'])) {
        $alerts[] = htmlspecialchars(($wo['transaction_no'] ?? 'Work order') . ' delayed, ETA ' . mfgAdminDashboardDate($wo['planned_end_date'] ?? ''));
    } elseif ((float)($wo['material_shortage_qty'] ?? 0) > 0) {
        $alerts[] = htmlspecialchars(($wo['transaction_no'] ?? 'Work order') . ' material shortage: ' . mfgAdminDashboardValue($wo['material_shortage_qty'] ?? 0));
    } elseif (($wo['production_status_name'] ?? '') == 'Hold') {
        $alerts[] = htmlspecialchars(($wo['transaction_no'] ?? 'Work order') . ' production on hold');
    } elseif (($wo['approval_status_value'] ?? '') != ST_APPROVED) {
        $alerts[] = htmlspecialchars(($wo['transaction_no'] ?? 'Work order') . ' approval pending: ' . ($wo['approval_status_name'] ?? 'Not Updated'));
    }
}
if (empty($alerts)) {
    $alerts[] = 'No active manufacturing exceptions';
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/custom_view_manufacturing_dashboard.css">

<div class="page-body mfg-dashboard" data-dashboard="admin">
  <div class="card">
    <div class="card-header bg-primary">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h3 class="mb-0 f-w-600"><?= __('Infisuite Manufacturing Dashboard'); ?></h3>
          <p class="mb-0 mfg-role-note"><?= __('Role: MANFACTRUING_ADMIN | Full control, approvals, stock, production, QC and reports'); ?></p>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
              <option><?= __('All Plants'); ?></option>
            </select>
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <input type="date" class="form-control" aria-label="<?= htmlspecialchars(__('From date'), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group mb-0">
            <input type="date" class="form-control" aria-label="<?= htmlspecialchars(__('To date'), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="row">
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-blue"><?php echo (int)($summary['total_work_orders'] ?? 0); ?></h3><p class="m-b-0"><?= __('Work Orders'); ?></p></div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-green"><?php echo mfgAdminDashboardValue($completedQty); ?></h3><p class="m-b-0"><?= __('Produced Qty'); ?></p></div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-yellow"><?php echo mfgAdminDashboardValue($completionPercent, 2); ?>%</h3><p class="m-b-0"><?= __('Completion'); ?></p></div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-red"><?php echo mfgAdminDashboardValue($material['shortage_qty'] ?? 0); ?></h3><p class="m-b-0"><?= __('Material Shortage'); ?></p></div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-black"><?php echo mfgAdminDashboardValue($qcFailRate, 2); ?>%</h3><p class="m-b-0"><?= __('QC Fail Rate'); ?></p></div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6">
          <div class="card comp-card">
            <div class="card-body"><h3 class="f-w-700 text-c-blue"><?php echo mfgAdminDashboardValue($operationHours, 2); ?>h</h3><p class="m-b-0"><?= __('Operation Time'); ?></p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="text-danger"><i class="fa fa-bell m-r-10"></i><?= __('Alerts'); ?></h5>
        </div>
        <div class="card-block list-tag">
          <ul>
            <?php foreach (array_slice($alerts, 0, 6) as $alert) { ?>
              <li><i class="icofont icofont-double-right text-success m-r-10"></i><?php echo $alert; ?></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5><?= __('Production Trend'); ?></h5>
          <div class="card-header-right ml-5">
            <div class="legend-item"><span class="dot dot-one"></span><span class="f-w-600"><?= __('Request'); ?></span></div>
            <div class="legend-item"><span class="dot dot-two"></span><span class="f-w-600"><?= __('Produced'); ?></span></div>
          </div>
        </div>
        <div class="card-block">
          <div id="adminProductionTrend" class="mfg-chart height-380"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5><?= __('Quality Defects'); ?></h5></div>
        <div class="card-block p-0 mt-3">
          <div id="adminQcPie" class="mfg-chart-small"></div>
        </div>
        <div class="mfg-chart-summary">
          <span><i class="mfg-chart-color-1"></i><?= __('Pending'); ?>: <?php echo (int)($qc['pending'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-2"></i><?= __('Passed'); ?>: <?php echo (int)($qc['passed'] ?? 0); ?></span>
          <span><i class="mfg-chart-color-3"></i><?= __('Failed'); ?>: <?php echo (int)($qc['failed'] ?? 0); ?></span>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="card">
        <div class="card-header"><h5><?= __('Work Orders'); ?></h5></div>
        <div class="card-body">
          <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm" id="workOrdersTable">
              <tr style="background-color: #eeeeee;">
                <th scope="col"><?= __('Order No'); ?></th>
                <th scope="col"><?= __('Product'); ?></th>
                <th scope="col"><?= __('Quantity'); ?></th>
                <th scope="col"><?= __('Produced'); ?></th>
                <th scope="col"><?= __('Production'); ?></th>
                <th scope="col"><?= __('Approval'); ?></th>
                <th scope="col"><?= __('Shortage'); ?></th>
                <th scope="col"><?= __('ETA'); ?></th>
              </tr>
              <?php foreach ((array)$workOrders as $wo) { ?>
                <tr class="work-order-row cursor-pointer" data-bs-toggle="modal" data-bs-target="#order-number" data-workorder="<?php echo htmlspecialchars(json_encode($wo), ENT_QUOTES, 'UTF-8'); ?>">
                  <td class="text-primary"><?php echo htmlspecialchars($wo['transaction_no'] ?? ''); ?></td>
                  <td>
                    <strong><?php echo htmlspecialchars($wo['product_name'] ?? ''); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($wo['product_sku'] ?? ''); ?></small>
                  </td>
                  <td><?php echo mfgAdminDashboardValue($wo['planned_qty'] ?? 0); ?></td>
                  <td><?php echo mfgAdminDashboardValue($wo['completed_qty'] ?? 0); ?></td>
                  <td><?php echo htmlspecialchars($wo['production_status_name'] ?? 'Not Started'); ?></td>
                  <td>
                    <span class="badge" style="background-color: <?php echo htmlspecialchars($wo['status_bg'] ?: '#cccccc'); ?>; color: <?php echo htmlspecialchars($wo['status_text_color'] ?: '#000000'); ?>; font-weight: 600;">
                      <?php echo htmlspecialchars($wo['approval_status_name'] ?? 'Not Updated'); ?>
                    </span>
                  </td>
                  <td><?php echo mfgAdminDashboardValue($wo['material_shortage_qty'] ?? 0); ?></td>
                  <td><?php echo mfgAdminDashboardDate($wo['planned_end_date'] ?? ''); ?></td>
                </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5><?= htmlspecialchars(__('Machine & Line Status'), ENT_QUOTES, 'UTF-8'); ?></h5></div>
        <div class="card-block mt-3">
          <div class="row">
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-green"><div class="card-body"><h6 class="m-b-5"><?= __('Active Orders'); ?></h6><h3 class="m-b-0 f-w-700"><?php echo (int)($production['active'] ?? 0); ?></h3><p class="m-b-0 f-w-600"><?= __('In production'); ?></p></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-yellow"><div class="card-body"><h6 class="m-b-5"><?= __('Hold Orders'); ?></h6><h3 class="m-b-0 f-w-700"><?php echo (int)($production['hold'] ?? 0); ?></h3><p class="m-b-0 f-w-600"><?= __('Needs action'); ?></p></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-green"><div class="card-body"><h6 class="m-b-5"><?= __('Machines Planned'); ?></h6><h3 class="m-b-0 f-w-700"><?php echo (int)($resources['machine_required'] ?? 0); ?></h3><p class="m-b-0 f-w-600"><?= __('From operations'); ?></p></div></div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="card prod-p-card card-red"><div class="card-body"><h6 class="m-b-5"><?= __('Delayed Orders'); ?></h6><h3 class="m-b-0 f-w-700"><?php echo (int)($summary['delayed_orders'] ?? 0); ?></h3><p class="m-b-0 f-w-600"><?= __('Past planned end'); ?></p></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5><?= htmlspecialchars(__('Material & Inventory'), ENT_QUOTES, 'UTF-8'); ?></h5></div>
        <div class="card-block">
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr><td><?= __('Required Material'); ?></td><th scope="col" class="text-end"><?php echo mfgAdminDashboardValue($material['required_qty'] ?? 0); ?> <?= __('Units'); ?></th></tr>
              <tr><td><?= __('Received Material'); ?></td><th scope="col" class="text-end"><?php echo mfgAdminDashboardValue($material['received_qty'] ?? 0); ?> <?= __('Units'); ?></th></tr>
              <tr><td class="text-danger"><?= __('Shortage Qty'); ?></td><th scope="col" class="text-danger text-end"><?php echo mfgAdminDashboardValue($material['shortage_qty'] ?? 0); ?> <?= __('Units'); ?></th></tr>
              <tr><td class="text-danger"><?= __('Shortage Lines'); ?></td><th scope="col" class="text-danger text-end"><?php echo (int)($material['shortage_lines'] ?? 0); ?></th></tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h5><?= __('Downtime Causes'); ?></h5></div>
        <div class="card-block">
          <div id="adminStatusPie" class="mfg-chart-small"></div>
        </div>
        <div class="mfg-chart-summary">
          <?php foreach (($adminChartData['statusLabels'] ?? array()) as $statusIndex => $statusName) { ?>
            <span><i class="mfg-chart-color-<?php echo (($statusIndex) % 4) + 1; ?>"></i><?php echo htmlspecialchars($statusName); ?>: <?php echo (int)($adminChartData['statusSeries'][$statusIndex] ?? 0); ?></span>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><h5><?= __('Workforce Summary'); ?></h5></div>
        <div class="card-block">
          <div class="table-responsive mt-4">
            <table class="table table-bordered">
              <tr><td><?= __('Manpower Required'); ?></td><th scope="col" class="text-end"><?php echo mfgAdminDashboardValue($resources['manpower_required'] ?? 0); ?></th></tr>
              <tr><td><?= __('Machine Required'); ?></td><th scope="col" class="text-end"><?php echo (int)($resources['machine_required'] ?? 0); ?></th></tr>
              <tr><td><?= __('Operation Hours'); ?></td><th scope="col" class="text-end"><?php echo mfgAdminDashboardValue($operationHours, 2); ?></th></tr>
              <tr><td><?= __('Operation Cost'); ?></td><th scope="col" class="text-end"><?php echo mfgAdminDashboardValue($resources['operation_cost'] ?? 0, 2); ?></th></tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h5><?= __('Approval Actions'); ?></h5></div>
        <div class="card-block">
          <div class="mfg-action-grid">
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_FORECAST"><i class="fas fa-chart-line"></i><?= __('Forecast'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_FIRM_ORDERS"><i class="fas fa-calendar-check"></i><?= __('Firm Order'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_MANUFACTURING_WORK_ORDERS"><i class="fas fa-cogs"></i><?= __('Work Order'); ?></a>
            <a class="mfg-action-tile" href="index.php?option=COM_INSERT_PROCESS_QUALITY_CHECK"><i class="fas fa-check-square"></i><?= __('QC Review'); ?></a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><h5><?= __('Stock Position'); ?></h5></div>
        <div class="card-block">
          <div id="adminStockBar" class="mfg-chart-small"></div>
          <div class="mfg-chart-summary">
            <?php foreach (($adminChartData['stockLabels'] ?? array()) as $stockIndex => $stockName) { ?>
              <span><i class="mfg-chart-color-<?php echo (($stockIndex) % 4) + 1; ?>"></i><?php echo htmlspecialchars($stockName); ?>: <?php echo mfgAdminDashboardValue($adminChartData['stockSeries'][$stockIndex] ?? 0); ?></span>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="order-number" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?= __('Work Order Details'); ?></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
      </div>
      <div class="modal-body">
        <h5 class="text-primary"><?= __('Work Order'); ?></h5>
        <div class="table-responsive mt-4">
          <table class="table table-sm mfg-work-order-info"><thead><tr class="visually-hidden"><th scope="col"><?= __('Data'); ?></th></tr></thead></table>
        </div>
        <div class="card-block list-tag">
          <div class="row">
            <div class="col-lg-6">
              <h5 class="f-w-600 mb-3 mt-3"><?= __('Material Consumption'); ?></h5>
              <ul class="mfg-material-list"></ul>
            </div>
            <div class="col-lg-6">
              <h5 class="f-w-600 mb-3 mt-3"><?= __('Operation Summary'); ?></h5>
              <ul class="mfg-operation-list"></ul>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"><?= __('Close'); ?></button>
      </div>
    </div>
  </div>
</div>

<textarea aria-label="<?= htmlspecialchars(__('Mfg Dashboard Chart Data'), ENT_QUOTES, 'UTF-8'); ?>" id="mfgDashboardChartData" class="d-none"><?php echo htmlspecialchars(json_encode($adminChartData), ENT_QUOTES, 'UTF-8'); ?></textarea>
<script src="assets/js/custom_view_manufacturing_role_charts.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    function escapeHtml(value) {
      return String(value || "").replace(/[&<>"']/g, function (char) {
        return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;", "'": "&#039;" })[char];
      });
    }

    function parseJson(value) {
      try {
        return JSON.parse(value || "[]");
      } catch (error) {
        return [];
      }
    }

    document.querySelectorAll(".work-order-row").forEach(function (row) {
      row.addEventListener("click", function () {
        var wo = parseJson(row.getAttribute("data-workorder"));
        document.querySelector("#order-number h5.text-primary").textContent = (wo.transaction_no || "Work Order") + " - " + (wo.product_name || "");
        document.querySelector("#order-number .mfg-work-order-info").innerHTML =
          "<tr><th scope="col"><?= htmlspecialchars(__('Planned Qty'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.planned_qty) + "</td><th scope="col"><?= htmlspecialchars(__('Produced Qty'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.completed_qty) + "</td></tr>" +
          "<tr><th scope="col"><?= htmlspecialchars(__('Production'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.production_status_name) + "</td><th scope="col"><?= htmlspecialchars(__('Approval'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.approval_status_name) + "</td></tr>" +
          "<tr><th scope="col"><?= htmlspecialchars(__('Start Date'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.planned_start_date) + "</td><th scope="col"><?= htmlspecialchars(__('End Date'), ENT_QUOTES, 'UTF-8'); ?>:</th><td>" + escapeHtml(wo.planned_end_date) + "</td></tr>";

        var materials = parseJson(wo.materials);
        document.querySelector("#order-number .mfg-material-list").innerHTML = materials.length
          ? materials.map(function (item) {
              return "<li><i class=\"icofont icofont-double-right text-success\"></i> " +
                escapeHtml(item.item_name || ("Item #" + item.item_id)) +
                " - Required " + escapeHtml(item.request_qty) + " " + escapeHtml(item.uom) +
                ", Received " + escapeHtml(item.stock_receipts_qty) + "</li>";
            }).join("")
          : "<li><?= htmlspecialchars(__('No materials listed'), ENT_QUOTES, 'UTF-8'); ?></li>";

        var operations = parseJson(wo.operations);
        document.querySelector("#order-number .mfg-operation-list").innerHTML = operations.length
          ? operations.map(function (operation) {
              return "<li><i class=\"icofont icofont-tools-alt-2 text-primary\"></i> " +
                escapeHtml(operation.operation_name || "Operation") +
                " - " + escapeHtml(operation.operation_time) + " mins, manpower: " +
                escapeHtml(operation.operation_manpower) + "</li>";
            }).join("")
          : "<li><?= htmlspecialchars(__('No operations recorded'), ENT_QUOTES, 'UTF-8'); ?></li>";
      });
    });
  });
</script>
