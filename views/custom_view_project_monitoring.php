<?php
$_SESSION['apply_js']['multiselest'] = 1;

function csrProjectMonitoringFinancialYearStart($financialYear) {
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
if ($selectedYear == '' || csrProjectMonitoringFinancialYearStart($selectedYear) > $currentFinancialYearStart) {
  $selectedYear = $currentFinancialYear;
}
$url = API . "?mode=getCsrProjectMonitoringDashboardData&company_id=" . $_SESSION['company_id'] . "&financial_year=" . urlencode($selectedYear);
$apires = callAPI($url);
$projectMonitoringData = json_decode($apires, true);

$summary = $projectMonitoringData['summary'] ?? array();
$financialYears = $projectMonitoringData['financial_years'] ?? array();
$out_rows = $projectMonitoringData['out_rows'] ?? array();
$p_spent = 0;
foreach((array)$out_rows as $out_row){ 
  $p_spent = $p_spent+$out_row['total_spent'];
}
$selectedFinancialYear = $projectMonitoringData['selected_financial_year'] ?? '';
if (csrProjectMonitoringFinancialYearStart($selectedFinancialYear) > $currentFinancialYearStart) {
  $selectedFinancialYear = $currentFinancialYear;
}
$previousFinancialYear = $projectMonitoringData['previous_financial_year'] ?? '';
$projects = $projectMonitoringData['projects'] ?? array();
$impact = $projectMonitoringData['impact'] ?? array();
$insights = $projectMonitoringData['insights'] ?? array();

function csrProjectMonitoringText($value) {
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrProjectMonitoringValue($row, $key) {
  return is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
}

function csrProjectMonitoringMoney($amount) {
  $amount = (float)$amount;
  if (function_exists('customeummarmonyformate')) {
    $formatted = customeummarmonyformate($amount);
    return $formatted != '' ? $formatted : '0';
  }
  return 'Rs. ' . number_format($amount, 2);
}

function csrProjectMonitoringDate($date) {
  if ($date == '' || $date == '0000-00-00') {
    return '-';
  }
  if (function_exists('fromDate')) {
    return fromDate($date);
  }
  return date('d-m-Y', strtotime($date));
}

function csrProjectMonitoringFyLabel($financialYear) {
  $financialYear = $financialYear ?? [];
  return $financialYear != '' ? $financialYear : 'Selected FY';
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/custom_view_project_monitoring.css">

<div class="row mt-3">
  <div class="col-lg-7 col-md-12 col-sm-12 col-12">
    <h3 class="f-w-600 mb-0">Project Monitoring</h3>
  </div>
  <div class="col-lg-5 col-md-12 col-sm-12 col-12 text-end">
    <form method="GET" id="csrProjectMonitoringYearForm" class="csr-dashboard-filter d-inline-flex align-items-center justify-content-end w-auto">
      <input type="hidden" name="option" value="<?php echo csrProjectMonitoringText($_GET['option'] ?? 'COM_VIEW_PROJECT_MONITORING'); ?>">
      <label class="form-label f-w-600 mb-0 me-2 text-nowrap" for="csrProjectMonitoringFySelect" style="min-width: 55px;">FA Year</label>
      <select name="fa_year" class="form-control js-example-basic-single csr-dashboard-fy-select" id="csrProjectMonitoringFySelect" style="width: 170px; min-width: 170px;">
        <?php foreach ($financialYears as $fyRow) { ?>
          <?php $fyCode = csrProjectMonitoringValue($fyRow, 'genc_detail_code'); ?>
          <?php if (csrProjectMonitoringFinancialYearStart($fyCode) > $currentFinancialYearStart) { continue;
} ?>
          <option value="<?php echo csrProjectMonitoringText($fyCode); ?>" <?php echo $selectedFinancialYear == $fyCode ? 'selected' : ''; ?>><?php echo csrProjectMonitoringText(csrProjectMonitoringValue($fyRow, 'genc_detail_name')); ?></option>
        <?php } ?>
      </select>
    </form>
  </div>
</div>
<div class="user-details-sec csr-project-monitoring-summary mt-3">
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-user f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Project</span>
            <h4 class="f-30 f-w-600"><?php echo (int)($summary['total_project'] ?? 0); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-success"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Obligation (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</span>
<h4 class="f-24 f-w-600"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($summary['total_obligation']  ?? 0)); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600"> Spent (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</span>
            <h4 class="f-24 f-w-600"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($summary['selected_spent_amount']-$p_spent ?? 0)); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-primary"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Spent ( Previous Year )</span>
            <h4 class="f-24 f-w-600"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($p_spent ?? 0)); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-database f-24 text-purple"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Total Unspent (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</span>
            <h4 class="f-24 f-w-600"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($summary['unallocated_funds'] ?? 0)); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-pie-chart f-24 text-purple"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">Utilization (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</span>
            <h4 class="f-24 f-w-600"><?php echo csrProjectMonitoringText($summary['utilization_percent'] ?? 0); ?>%</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="details-box">
    <div class="card m-h-auto b-radius-10 mb-2">
      <div class="card-body">
        <div class="d-flex">
          <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3">
            <i class="fa fa-exclamation-triangle f-24 text-danger"></i>
          </span>
          <div class="d-inline-block">
            <span class="f-12 f-w-600">At Risk Projects</span>
            <h4 class="f-24 f-w-600"><?php echo (int)($summary['delayed_project'] ?? 0); ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <?php if(!empty($out_rows)){ ?>
  <div class="col-lg-4 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <h4 class="f-w-600 f-20 mb-0 d-flex align-items-center flex-wrap">
            Previous Project Spent 
          </h4>
        </div>
        <div class="table-responsive mt-3 custom-scroll" style="height:450px;">
          <table class="table table-borderless table-sm">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Project Name</th>
              <th scope="col">Spant</th>
            </tr>
            <?php $ik=1; foreach($out_rows as $out_row){ ?> 
              <tr>
                <td><?php echo $ik; ?></td>
                <td><?php echo $out_row['subject']; ?></td>
                <td><?php echo customeummarmonyformate($out_row['total_spent']); ?></td>
              </tr>
            <?php $ik=$ik+1;
              $total_spent = $total_spent+$out_row['total_spent'];
             } ?>
             <tr class="f-w-600">
                <td></td>
                <td><?php echo "Total"; ?></td>
                <td><?php echo customeummarmonyformate($total_spent); ?></td>
              </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
  <?php if (count((array)$projects) > 0) { ?>
    <?php foreach ($projects as $project) {
      $progress = (int)csrProjectMonitoringValue($project, 'progress');
      $progressClass = csrProjectMonitoringValue($project, 'progress_class') ?: 'progress-bar-success';
      $selectedApproved = (float)csrProjectMonitoringValue($project, 'selected_approved_amount');
      $selectedSpent = (float)csrProjectMonitoringValue($project, 'selected_spent_amount');
      $selectedUnspent = max($selectedApproved - $selectedSpent, 0);
      $proposalId = (int)csrProjectMonitoringValue($project, 'id');
      $projectCurrentStatus = (int)csrProjectMonitoringValue($project, 'current_status');
      $hasTracking = (int)csrProjectMonitoringValue($project, 'has_tracking') === 1;

      $trackingTotal = (int)csrProjectMonitoringValue($project, 'total_tracking');
      $trackingYetToStart = (int)csrProjectMonitoringValue($project, 'yet_to_start_tracking');
      $trackingInProgress = (int)csrProjectMonitoringValue($project, 'inprogress_tracking');
      $trackingHold = (int)csrProjectMonitoringValue($project, 'hold_tracking');
      $trackingCompleted = (int)csrProjectMonitoringValue($project, 'completed_tracking');

      $trackingUrl = 'index.php?option=COM_VIEW_CSR_TRACKING&Rid=' . deocdeId('id:' . $proposalId);
      $editUrl = 'index.php?option=COM_INSERT_CSR_PROPOSAL_DETAILS&Rid=' . deocdeId('id:' . $proposalId);
    ?>
      <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h4 class="f-w-600 f-20 mb-0 d-flex align-items-center flex-wrap">
                <a class="csr-project-monitoring-title" href="index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?php echo deocdeId('id:' . $proposalId); ?>">
                  <?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'subject') ?: '-'); ?>
                </a>
                <?php if ($projectCurrentStatus === 0) { ?>
                  <a href="<?php echo csrProjectMonitoringText($editUrl); ?>" class="csr-project-monitoring-edit-link ms-2" title="Edit proposal">
                    <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                  </a>
                <?php } ?>
              </h4>
              <button type="button" class="btn <?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'status_class') ?: 'btn-success'); ?> btn-sm b-radius-30 ms-2"><?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'status_label') ?: '-'); ?></button>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-borderless table-sm">
                <tr>
                  <th scope="col">Type</th>
                  <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'type_of_project') ?: csrProjectMonitoringValue($project, 'type_of_work') ?: '-'); ?></th>
                </tr>
                <tr>
                  <th scope="col">Sector</th>
                  <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'sector') ?: '-'); ?></th>
                </tr>
                <tr>
                  <th scope="col">Location</th>
                  <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringValue($project, 'location_name') ?: '-'); ?></th>
                </tr>
                <tr>
                  <th scope="col">Amount</th>
                  <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney(csrProjectMonitoringValue($project, 'total_budget_grand_total'))); ?></th>
                </tr>
              </table>
            </div>
            <?php if ($hasTracking) { ?>
              <a href="<?php echo csrProjectMonitoringText($trackingUrl); ?>" class="csr-project-monitoring-progress-link d-block text-decoration-none text-body mb-4" title="View project tracking">
                <div class="d-flex justify-content-between">
                  <span class="f-w-600 f-14">Progress</span>
                  <span class="f-w-600 f-14"><?php echo $progress; ?>%</span>
                </div>
                <div class="progress progress-sm mt-1">
                  <div class="progress-bar <?php echo csrProjectMonitoringText($progressClass); ?>" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <?php if ($trackingTotal > 0) { ?>
                <div class="csr-project-monitoring-tracking-stats mt-3">
                  <p class="f-w-600 f-12 mb-2 text-muted">Tracking Statistics</p>
                  <div class="row text-center g-1">
                    <div class="col csr-project-monitoring-tracking-stat">
                      <p class="f-11 f-w-600 mb-0 text-muted">Total</p>
                      <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText($trackingTotal); ?></span>
                    </div>
                    <div class="col csr-project-monitoring-tracking-stat">
                      <p class="f-11 f-w-600 mb-0 text-muted">Yet to Start</p>
                      <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText($trackingYetToStart); ?></span>
                    </div>
                    <div class="col csr-project-monitoring-tracking-stat">
                      <p class="f-11 f-w-600 mb-0 text-muted">In Progress</p>
                      <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText($trackingInProgress); ?></span>
                    </div>
                    <div class="col csr-project-monitoring-tracking-stat">
                      <p class="f-11 f-w-600 mb-0 text-muted">Hold</p>
                      <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText($trackingHold); ?></span>
                    </div>
                    <div class="col csr-project-monitoring-tracking-stat">
                      <p class="f-11 f-w-600 mb-0 text-muted">Completed</p>
                      <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText($trackingCompleted); ?></span>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </a>

            <?php } else { ?>
            <div class="csr-project-monitoring-progress-static mb-4">
              <div class="d-flex justify-content-between">
                <span class="f-w-600 f-14">Progress</span>
                <span class="f-w-600 f-14"><?php echo $projectCurrentStatus === 0 ? 'Draft' : ($projectCurrentStatus > 100 && $projectCurrentStatus < (int)ST_APPROVED ? 'In Progress' : ($projectCurrentStatus === (int)ST_REJECTED ? 'Rejected' : $progress . '%')); ?></span>
              </div>
              <div class="progress progress-sm mt-1">
                <div class="progress-bar <?php echo csrProjectMonitoringText($progressClass); ?>" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <?php } ?>
            <h3 class="f-w-600 f-14 mb-2">Planned Timeline</h3>
            <div class="mt-1">
              <div class="d-flex justify-content-between border-top border-bottom p-2">
                <div>
                  <i class="fa fa-calendar me-2"></i>
                  <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText(csrProjectMonitoringDate(csrProjectMonitoringValue($project, 'project_start_date'))); ?></span>
                </div>
                <span class="f-w-600 f-14">to</span>
                <div>
                  <i class="fa fa-calendar me-2"></i>
                  <span class="f-w-600 f-14"><?php echo csrProjectMonitoringText(csrProjectMonitoringDate(csrProjectMonitoringValue($project, 'project_end_date'))); ?></span>
                </div>
              </div>
            </div>
            <div class="mt-4 mb-3 bg-gray p-3">
              <div class="row">
                <div class="col-md-4 col-sm-4 col-12 text-center mb-2">
                  <p class="f-14 f-w-600 mb-0 text-muted">Approved (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</p>
                  <span class="f-w-600 f-18"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($selectedApproved)); ?></span>
                </div>
                <div class="col-md-4 col-sm-4 col-12 text-center mb-2">
                  <p class="f-14 f-w-600 mb-0 text-muted">Spent (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</p>
                  <span class="f-w-600 f-18"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($selectedSpent)); ?></span>
                </div>
                <div class="col-md-4 col-sm-4 col-12 text-center mb-2">
                  <p class="f-14 f-w-600 mb-0 text-muted">Unspent (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</p>
                  <span class="f-w-600 f-18"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($selectedUnspent)); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php } else { ?>
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body text-center">
          <h4 class="f-w-600 mb-0">No projects found for selected financial year.</h4>
        </div>
      </div>
    </div>
  <?php } ?>
</div>



<div class="row mt-4">
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600">Compliance & Risk Overview</h3>
        <div class="table-responsive mt-3 risk-overview">
          <table class="table table-bordered align-middle">
            <tr>
              <td><span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center"><i class="ti-user f-16 text-success f-w-600"></i></span></td>
              <th scope="col">CSR Obligation Due Date</th>
              <th scope="col"><?php echo csrProjectMonitoringText($selectedFinancialYear != '' ? '31 March ' . ('20' . substr($selectedFinancialYear, -2)) : '-'); ?></th>
              <td><span class="form-label label label-success">On Track</span></td>
            </tr>
            <tr>
              <td><span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center"><i class="ti-user f-16 text-warning f-w-600"></i></span></td>
              <th scope="col">Board Approval Pending</th>
              <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($summary['board_approval_pending'] ?? 0)); ?></th>
              <td><span class="form-label label label-warning">Pending</span></td>
            </tr>
            <tr>
              <td><span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center"><i class="ti-user f-16 text-warning f-w-600"></i></span></td>
              <th scope="col">Project Delayed</th>
              <th scope="col"><?php echo (int)($summary['delayed_project'] ?? 0); ?></th>
              <td><span class="form-label label label-warning">At Risk</span></td>
            </tr>
            <tr>
              <td><span class="user-icon-40 b-radius-15 bg-red-50 f-14 f-w-600 text-center"><i class="ti-user f-16 text-danger f-w-600"></i></span></td>
              <th scope="col">Unallocated CSR Funds</th>
              <th scope="col"><?php echo csrProjectMonitoringText(csrProjectMonitoringMoney($summary['unallocated_funds'] ?? 0)); ?></th>
              <td><span class="form-label label label-danger">High</span></td>
            </tr>
            <tr>
              <td><span class="user-icon-40 b-radius-15 bg-yellow-50 f-14 f-w-600 text-center"><i class="ti-user f-16 text-warning f-w-600"></i></span></td>
              <th scope="col">Utilization Certificate Pending</th>
              <th scope="col"><?php echo (int)($summary['utilization_certificate_pending'] ?? 0); ?></th>
              <td><span class="form-label label label-warning">Pending</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-body">
        <h3 class="f-w-600">AI Insights</h3>
        <div class="table-responsive mt-3 ai-insights">
          <table class="table table-bordered align-middle">
            <?php if (count((array)$insights) > 0) { ?>
              <?php foreach ($insights as $insight) { ?>
                <tr>
                  <td><span class="user-icon-40 b-radius-15 bg-blue-50 f-14 f-w-600 text-center"><i class="fa fa-user f-16 text-primary f-w-600"></i></span></td>
                  <th scope="col"><?php echo csrProjectMonitoringText($insight); ?></th>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2" class="text-center">No insights available</td>
              </tr>
            <?php } ?>
          </table>
        </div>
        <a href="index.php?option=COM_VIEW_CSR_DASHBOARD&fa_year=<?php echo csrProjectMonitoringText(urlencode($selectedFinancialYear)); ?>" class="bg-blue-50 p-2 b-radius-30 float-end f-w-600 text-primary">View Detailed Insights</a>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h3>Impact Snapshot (FY <?php echo csrProjectMonitoringText(csrProjectMonitoringFyLabel($selectedFinancialYear)); ?>)</h3>
    <div class="user-details-sec mt-3">
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-blue-50 p-2 b-radius-15 text-center me-3"><i class="fa fa-graduation-cap f-24 text-primary"></i></span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Student Supported</span>
                <h4 class="f-30 f-w-600"><?php echo (int)($impact['student_supported'] ?? 0); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3"><i class="fa fa-home f-24 text-purple"></i></span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Villages Covered</span>
                <h4 class="f-24 f-w-600"><?php echo (int)($impact['villages_covered'] ?? 0); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-red-50 p-2 b-radius-15 text-center me-3"><i class="far fa-heart f-24 text-danger"></i></span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Health Camps Conducted</span>
                <h4 class="f-24 f-w-600"><?php echo (int)($impact['health_camps_conducted'] ?? 0); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-green-50 p-2 b-radius-15 text-center me-3"><i class="fa fa-tree f-24 text-success"></i></span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Trees Planted</span>
                <h4 class="f-24 f-w-600"><?php echo (int)($impact['trees_planted'] ?? 0); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="details-box">
        <div class="card m-h-auto b-radius-10 mb-2">
          <div class="card-body">
            <div class="d-flex">
              <span class="user-icon bg-purple-50 p-2 b-radius-15 text-center me-3"><i class="fas fa-female f-24 text-purple"></i></span>
              <div class="d-inline-block">
                <span class="f-12 f-w-600">Women Benefited</span>
                <h4 class="f-24 f-w-600"><?php echo (int)($impact['women_benefited'] ?? 0); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/custom_view_project_monitoring.js"></script>
