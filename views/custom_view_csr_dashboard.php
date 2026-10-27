<?php
$_SESSION['apply_js']['multiselest'] = 1;

function csrDashboardFinancialYearStart($financialYear) {
    $matches = $matches ?? '';
  if (preg_match('/^([0-9]{4})/', (string)$financialYear, $matches)) {
    return (int)$matches[1];
  }
  return 0;
}

$currentYear = (int)date('Y');
$currentMonth = (int)date('n');
$currentFinancialYearStart = $currentMonth >= 4 ? $currentYear : $currentYear - 1;
$currentFinancialYear = $currentFinancialYearStart . '-' . substr((string)($currentFinancialYearStart + 1), -2);
$selectedYear = $_GET['fa_year'] ?? '';
if ($selectedYear == '' || csrDashboardFinancialYearStart($selectedYear) > $currentFinancialYearStart) {
  $selectedYear = $currentFinancialYear;
}
$url = API . "?mode=getCsrFirstDashboardData&company_id=" . $_SESSION['company_id'] . "&financial_year=" . urlencode($selectedYear);
$apires = callAPI($url);
$csrDashboardData = json_decode($apires, true);
$summary = $csrDashboardData['summary'] ?? array();
$financialYears = $csrDashboardData['financial_years'] ?? array();
$selectedFinancialYear = $csrDashboardData['selected_financial_year'] ?? '';
if (csrDashboardFinancialYearStart($selectedFinancialYear) > $currentFinancialYearStart) {
  $selectedFinancialYear = $currentFinancialYear;
}
$yearlyRows = $csrDashboardData['yearly_rows'] ?? array();
$sectorRows = $csrDashboardData['sector_rows'] ?? array();
$locationRows = $csrDashboardData['location_rows'] ?? array();
$approvedProjects = $csrDashboardData['approved_projects'] ?? array();
$rejectedProjects = $csrDashboardData['rejected_projects'] ?? array();

function csrDashboardMoney($amount) {
  $amount = (float)$amount;
  if (function_exists('customeummarmonyformate')) {
    $formatted = customeummarmonyformate($amount);
    return $formatted != '' ? $formatted : '0';
  }
  return '₹ ' . number_format($amount, 2);
}

function csrDashboardText($value) {
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrDashboardValue($row, $key) {
  return is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
}

$chartData = array(
  'obligationSpentUnspent' => array('labels' => array(), 'obligation' => array(), 'spent' => array(), 'unspent' => array()),
  'spentTrend' => array('labels' => array(), 'spent' => array()),
  'budgetFlow' => array('labels' => array(), 'obligation' => array(), 'approved' => array(), 'spent' => array()),
  'sectorAllocation' => array('labels' => array(), 'values' => array()),
  'locationAllocation' => array('labels' => array(), 'values' => array())
);

foreach ($yearlyRows as $row) {
  $obligation = (float)csrDashboardValue($row, 'obligation');
  $approved = (float)csrDashboardValue($row, 'approved_amount');
  $spent = (float)csrDashboardValue($row, 'spent_amount');
  $chartData['obligationSpentUnspent']['labels'][] = csrDashboardValue($row, 'financial_year');
  $chartData['obligationSpentUnspent']['obligation'][] = $obligation;
  $chartData['obligationSpentUnspent']['spent'][] = $spent;
  $chartData['obligationSpentUnspent']['unspent'][] = max($obligation - $spent, 0);
  $chartData['spentTrend']['labels'][] = csrDashboardValue($row, 'financial_year');
  $chartData['spentTrend']['spent'][] = $spent;
  $chartData['budgetFlow']['labels'][] = csrDashboardValue($row, 'financial_year');
  $chartData['budgetFlow']['obligation'][] = $obligation;
  $chartData['budgetFlow']['approved'][] = $approved;
  $chartData['budgetFlow']['spent'][] = $spent;
}

$sectorTotalApproved = 0;
$sectorTotalSpent = 0;
foreach ($sectorRows as $row) {
  $approved = (float)csrDashboardValue($row, 'approved_amount');
  $spent = (float)csrDashboardValue($row, 'spent_amount');
  $sectorTotalApproved += $approved;
  $sectorTotalSpent += $spent;
  $chartData['sectorAllocation']['labels'][] = csrDashboardValue($row, 'label');
  $chartData['sectorAllocation']['values'][] = (float)csrDashboardValue($row, 'allocation_amount');
}

foreach ($locationRows as $row) {
  $allocation = (float)csrDashboardValue($row, 'allocation_amount');
  $chartData['locationAllocation']['labels'][] = csrDashboardValue($row, 'label');
  $chartData['locationAllocation']['values'][] = $allocation;
}

$chartJson = htmlspecialchars(json_encode($chartData), ENT_QUOTES, 'UTF-8');
?>

<link rel="stylesheet" href="assets/css/custom_view_csr_dashboard.css">

<div class="row mt-3">
  <div class="col-lg-7 col-md-12 col-sm-12 col-12">
    <h3 class="f-w-600 mb-0">CSR Dashboard - Executive Summary</h3>
  </div>
  <div class="col-lg-5 col-md-12 col-sm-12 col-12">
    <form method="GET" id="csrDashboardYearForm" class="csr-dashboard-filter">
      <input type="hidden" name="option" value="<?php echo csrDashboardText($_GET['option'] ?? 'COM_VIEW_CSR_DASHBOARD'); ?>">
      <label class="form-label f-w-600 mb-0" for="csrDashboardFySelect">FA Year</label>
      <select name="fa_year" class="form-control js-example-basic-single csr-dashboard-fy-select" id="csrDashboardFySelect">
        <?php foreach ($financialYears as $fyRow) { ?>
          <?php $fyCode = csrDashboardValue($fyRow, 'genc_detail_code'); ?>
          <?php if (csrDashboardFinancialYearStart($fyCode) > $currentFinancialYearStart) { continue;
} ?>
          <option value="<?php echo csrDashboardText($fyCode); ?>" <?php echo $selectedFinancialYear == $fyCode ? 'selected' : ''; ?>><?php echo csrDashboardText(csrDashboardValue($fyRow, 'genc_detail_name')); ?></option>
        <?php } ?>
      </select>
    </form>
  </div>
</div>

<div class="csr-dashboard mt-4">
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body csr-click cursor-pointer" data-title="Total Active Projects" data-type="active_project" data-fa-year="<?php echo csrDashboardText($selectedFinancialYear); ?>">
        <span class="f-12 f-w-600">Total Active Projects</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['active_project_amount'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-blue"><i class="fa fa-briefcase f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body csr-click cursor-pointer" data-title="Project Approved" data-type="project_approved" data-fa-year="<?php echo csrDashboardText($selectedFinancialYear); ?>">
        <span class="f-12 f-w-600">Project Approved</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['project_approved'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-green"><i class="fa fa-check-circle f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body csr-click cursor-pointer" data-title="Approval in Process" data-type="approved_in_progress" data-fa-year="<?php echo csrDashboardText($selectedFinancialYear); ?>">
        <span class="f-12 f-w-600">Approval in Process</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['approved_in_progress'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-green"><i class="fas fa-hourglass-half f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Total Obligation</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['total_obligation'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-blue"><i class="fa fa-calculator f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body csr-click cursor-pointer" data-title="Spent" data-type="spent" data-fa-year="<?php echo csrDashboardText($selectedFinancialYear); ?>">
        <span class="f-12 f-w-600">Spent</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['spent'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-green"><i class="fa fa-database f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Unspent</span>
        <h4 class="f-24 f-w-600 mt-2"><?php echo csrDashboardText(csrDashboardMoney($summary['unspent'] ?? 0)); ?></h4>
        <div class="pt-badge bg-c-red"><i class="fa fa-suitcase f-18"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Obligation vs Spent vs Unspent</h3>
        </div>
        <div class="csr-chart-wrap"><canvas id="csr-obligation-chart"></canvas></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">CSR Spent Trend(Year-Wise)</h3>
        </div>
        <div class="csr-chart-wrap"><canvas id="csr-spent-trend-chart"></canvas></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Budget Flow</h3>
        </div>
        <div class="csr-chart-wrap"><canvas id="csr-budget-flow-chart"></canvas></div>
      </div>
    </div>
  </div>
</div>

<div class="csr-dashboard mt-4">
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Total Projects</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['total_project'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-blue"><i class="fa fa-briefcase f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Active Project</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['active_project'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-green"><i class="fa fa-check-circle f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">On Track Project</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['on_track_project'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-green"><i class="fa fa-check-circle f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">At Risk Project</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['at_risk_project'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-red"><i class="fa fa-exclamation-triangle f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Sectors Covered</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['sectors_covered'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-blue"><i class="fa fa-users f-18"></i></div>
      </div>
    </div>
  </div>
  <div class="csr-dashboard-box">
    <div class="card proj-t-card mb-2">
      <div class="card-body">
        <span class="f-12 f-w-600">Location Covered</span>
        <h4 class="f-30 f-w-600 mt-2"><?php echo (int)($summary['locations_covered'] ?? 0); ?></h4>
        <div class="pt-badge bg-c-blue"><i class="fa fa-map-marker f-18"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-12">
    <h3 class="f-w-600">Allocation Insights</h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Sector-wise Allocation(Approved Amount)</h3>
        </div>
        <div class="csr-chart-wrap"><canvas id="csr-sector-allocation-chart"></canvas></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="f-20 f-w-600">Location-wise Allocation(Approved Amount)</h3>
        </div>
        <div class="csr-chart-wrap"><canvas id="csr-location-allocation-chart"></canvas></div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h3 class="f-w-600 mb-3">Sector-wise Progress</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <tr>
          <th scope="col">Sector</th>
          <th scope="col">Approved Amount</th>
          <th scope="col">Spent</th>
          <th scope="col">Progress</th>
        </tr>
        <?php if (count((array)$sectorRows) > 0) { ?>
          <?php foreach ($sectorRows as $row) {
            $approved = (float)csrDashboardValue($row, 'approved_amount');
            $spent = (float)csrDashboardValue($row, 'spent_amount');
            $progress = $approved > 0 ? min(round(($spent / $approved) * 100), 100) : 0;
          ?>
            <tr>
              <td><?php echo csrDashboardText(csrDashboardValue($row, 'label')); ?></td>
              <td><?php echo csrDashboardText(csrDashboardMoney($approved)); ?></td>
              <td><?php echo csrDashboardText(csrDashboardMoney($spent)); ?></td>
              <td>
                <?php echo $progress; ?>%
                <div class="progress progress-xs">
                  <div class="progress-bar progress-bar-primary csr-progress-bar" role="progressbar" data-progress="<?php echo $progress; ?>" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </td>
            </tr>
          <?php } ?>
          <?php $totalProgress = $sectorTotalApproved > 0 ? min(round(($sectorTotalSpent / $sectorTotalApproved) * 100), 100) : 0; ?>
          <tr>
            <td>Total</td>
            <td><?php echo csrDashboardText(csrDashboardMoney($sectorTotalApproved)); ?></td>
            <td><?php echo csrDashboardText(csrDashboardMoney($sectorTotalSpent)); ?></td>
            <td>
              <?php echo $totalProgress; ?>%
              <div class="progress progress-xs">
                <div class="progress-bar progress-bar-primary csr-progress-bar" role="progressbar" data-progress="<?php echo $totalProgress; ?>" aria-valuenow="<?php echo $totalProgress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </td>
          </tr>
        <?php } else { ?>
          <tr>
            <td colspan="4" class="text-center">No Records</td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600 mb-3">Approved Projects</h3>
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <tr>
              <th scope="col">Project</th>
              <th scope="col">FA Year</th>
              <th scope="col">Approved Amount</th>
            </tr>
            <?php if (count((array)$approvedProjects) > 0) { ?>
              <?php foreach ($approvedProjects as $project) { ?>
                <tr>
                  <td>
                    <a class="text-primary" href="index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . csrDashboardValue($project, 'id')); ?>">
                      <?php echo csrDashboardText(csrDashboardValue($project, 'subject') ?: '-'); ?>
                    </a>
                    <div class="text-muted f-12"><?php echo csrDashboardText(csrDashboardValue($project, 'institue_name') ?: '-'); ?></div>
                  </td>
                  <td><?php echo csrDashboardText(csrDashboardValue($project, 'financial_year') ?: '-'); ?></td>
                  <td><?php echo csrDashboardText(csrDashboardMoney(csrDashboardValue($project, 'total_budget_grand_total'))); ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="3" class="text-center">No Records</td>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600 mb-3">Rejected Projects</h3>
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <tr>
              <th scope="col">Project</th>
              <th scope="col">FA Year</th>
              <th scope="col">Amount</th>
            </tr>
            <?php if (count((array)$rejectedProjects) > 0) { ?>
              <?php foreach ($rejectedProjects as $project) { ?>
                <tr>
                  <td>
                    <a class="text-primary" href="index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . csrDashboardValue($project, 'id')); ?>">
                      <?php echo csrDashboardText(csrDashboardValue($project, 'subject') ?: '-'); ?>
                    </a>
                    <div class="text-muted f-12"><?php echo csrDashboardText(csrDashboardValue($project, 'institue_name') ?: '-'); ?></div>
                  </td>
                  <td><?php echo csrDashboardText(csrDashboardValue($project, 'financial_year') ?: '-'); ?></td>
                  <td><?php echo csrDashboardText(csrDashboardMoney(csrDashboardValue($project, 'total_budget_grand_total'))); ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="3" class="text-center">No Records</td>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="csr-dashboard-chart-data" data-chart="<?php echo $chartJson; ?>"></div>

<script type="text/javascript" src="assets/bower_components/chart.js/js/Chart.js"></script>
<script type="text/javascript" src="assets/js/custom_view_csr_dashboard.js"></script>
