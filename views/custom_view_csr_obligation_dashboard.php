<?php
$_SESSION['apply_js']['multiselest'] = 1;

function csrOblDashText($value) {
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrOblDashMoney($amount) {
  $amount = (float)$amount;
  if (function_exists('customeummarmonyformate')) {
    $formatted = customeummarmonyformate($amount);
    return $formatted != '' ? $formatted : '₹ 0';
  }
  return '₹ ' . number_format($amount, 0);
}

function csrOblDashPct($value) {
  return number_format((float)$value * 100, 2) . '%';
}

function csrOblDashCsvValue($value) {
  if ($value == null || $value == '') {
    return '-';
  }
  if (!is_string($value)) {
    return $value;
  }
  $value = preg_replace('/[\r\n\t]+/', ' ', $value);
  $value = str_replace(',', ' ', $value);
  $value = preg_replace('/\s+/', ' ', $value);
  $value = trim($value);
  return $value != '' ? $value : '-';
}

function csrOblDashSpentPctClass($pct) {
  if ((float)$pct >= 0.95) {
    return 'csr-obl-pct-good';
  }
  return 'csr-obl-pct-warn';
}

function csrOblDashCsvBaseRow($label, $totalAmount, $fyRange, $fyValues, $isPct) {
  $row = array(
    'Project Name' => csrOblDashCsvValue($label),
    'Type' => '',
    'Entity' => '',
    'Location' => '',
    'Status' => '',
    'Total Amount' => $totalAmount
  );
  if (count((array)$fyRange) > 0) {
    foreach ($fyRange as $yIdx => $fyCode) {
      $val = isset($fyValues[$yIdx]) ? $fyValues[$yIdx] : '';
      if ($isPct && is_numeric($val)) {
        $row['FY ' . $fyCode] = number_format((float)$val * 100, 2) . '%';
      } else if (is_numeric($val)) {
        $row['FY ' . $fyCode] = (float)$val;
      } else {
        $row['FY ' . $fyCode] = '-';
      }
    }
  }
  return $row;
}

$yearFrom = isset($_GET['year_from']) ? $_GET['year_from'] : '';
$yearTo = isset($_GET['year_to']) ? $_GET['year_to'] : '';
$locationId = isset($_GET['location_id']) ? $_GET['location_id'] : '';
$entityCode = isset($_GET['entity_code']) ? $_GET['entity_code'] : '';
$pageOption = isset($_GET['option']) ? $_GET['option'] : 'COM_VIEW_CSR_OBLIGATION_DASHBOARD';

$url = API . "?mode=getCsrObligationDashboardData"
  . "&year_from=" . urlencode($yearFrom)
  . "&year_to=" . urlencode($yearTo)
  . "&location_id=" . urlencode($locationId)
  . "&entity_code=" . urlencode($entityCode);
$apires = callAPI($url);
$dashData = json_decode($apires, true);

$financialYears = $dashData['financial_years'] ?? array();
$locations = $dashData['locations'] ?? array();
$entityNames = $dashData['entity_names'] ?? array();
$selected = $dashData['selected'] ?? array();
$fyRange = $dashData['fy_range'] ?? array();
$summary = $dashData['summary'] ?? array();
$yearlyRows = $dashData['yearly_rows'] ?? array();
$projectGroups = $dashData['project_groups'] ?? array();
$locationRows = $dashData['location_rows'] ?? array();

$yearFrom = $selected['year_from'] ?? $yearFrom;
$yearTo = $selected['year_to'] ?? $yearTo;
$locationId = $selected['location_id'] ?? $locationId;
$entityCode = $selected['entity_code'] ?? $entityCode;

$totalObligation = (float)($summary['total_obligation'] ?? 0);
$totalSpent = (float)($summary['total_spent'] ?? 0);
$overallUtilization = (float)($summary['overall_utilization'] ?? 0);
$finalUnspent = (float)($summary['final_unspent'] ?? 0);
$totalUnspentPct = (float)($summary['total_unspent_pct'] ?? 0);

$yoyObligation = array();
$yoySpent = array();
$yoySpentPct = array();
$yoyUnspent = array();
$yoyUnspentPct = array();
if (count((array)$yearlyRows) > 0) {
  foreach ($yearlyRows as $yr) {
    $yoyObligation[] = (float)(is_array($yr) ? ($yr['obligation'] ?? 0) : ($yr->obligation ?? 0));
    $yoySpent[] = (float)(is_array($yr) ? ($yr['spent_amount'] ?? 0) : ($yr->spent_amount ?? 0));
    $yoySpentPct[] = (float)(is_array($yr) ? ($yr['spent_pct'] ?? 0) : ($yr->spent_pct ?? 0));
    $yoyUnspent[] = (float)(is_array($yr) ? ($yr['unspent_amount'] ?? 0) : ($yr->unspent_amount ?? 0));
    $yoyUnspentPct[] = (float)(is_array($yr) ? ($yr['unspent_pct'] ?? 0) : ($yr->unspent_pct ?? 0));
  }
}

$grandTotal = 0;
$grandYears = array();
if (count((array)$fyRange) > 0) {
  foreach ($fyRange as $fyCode) {
    $grandYears[] = 0;
  }
}
if (count((array)$projectGroups) > 0) {
  foreach ($projectGroups as $group) {
    $grandTotal += (float)($group['total'] ?? 0);
    if (count((array)$group['years']) > 0) {
      foreach ($group['years'] as $yIdx => $yearAmt) {
        if (!isset($grandYears[$yIdx])) {
          $grandYears[$yIdx] = 0;
        }
        $grandYears[$yIdx] += (float)$yearAmt;
      }
    }
  }
}

$colCount = 6 + count((array)$fyRange);
$csvdata = array();
$_SESSION['download_data'] = array();
?>

<link rel="stylesheet" href="assets/css/custom_view_csr_obligation_dashboard.css">

<div class="csr-obl-dashboard page-body">
  <div class="row align-items-center csr-obl-header">
    <div class="col-lg-6 col-md-12">
      <h3 class="csr-obl-title mb-1">CSR Obligation Tracking Report</h3>
      <p class="csr-obl-subtitle mb-0">Year-on-Year Spending Against CSR Obligation</p>
    </div>
    <div class="col-lg-6 col-md-12 text-end">
      <a class="btn btn-outline-success btn-sm csr-obl-csv-btn" href="custom_view_download.php?page=CSRObligationDashboard&format=csv" target="_blank">
        <i class="fa fa-download"></i> CSV
      </a>
    </div>
  </div>

  <form method="GET" id="csrOblYearForm" class="csr-obl-filter-form mt-3">
    <input type="hidden" name="option" value="<?php echo csrOblDashText($pageOption); ?>">
    <div class="row">
      <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
        <label class="form-label mb-1 f-w-600" for="csrOblYearFrom">From</label>
        <select name="year_from" id="csrOblYearFrom" class="form-control js-example-basic-single csr-obl-fy-select">
          <?php if (count((array)$financialYears) > 0) { ?>
            <?php foreach ($financialYears as $fyRow) { ?>
              <?php
                $fyCode = is_array($fyRow) ? ($fyRow['genc_detail_code'] ?? '') : ($fyRow->genc_detail_code ?? '');
                $fyName = is_array($fyRow) ? ($fyRow['genc_detail_name'] ?? $fyCode) : ($fyRow->genc_detail_name ?? $fyCode);
              ?>
              <option value="<?php echo csrOblDashText($fyCode); ?>" <?php echo $yearFrom == $fyCode ? 'selected' : ''; ?>>
                <?php echo csrOblDashText($fyName); ?>
              </option>
            <?php } ?>
          <?php } ?>
        </select>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
        <label class="form-label mb-1 f-w-600" for="csrOblYearTo">To</label>
        <select name="year_to" id="csrOblYearTo" class="form-control js-example-basic-single csr-obl-fy-select">
          <?php if (count((array)$financialYears) > 0) { ?>
            <?php foreach ($financialYears as $fyRow) { ?>
              <?php
                $fyCode = is_array($fyRow) ? ($fyRow['genc_detail_code'] ?? '') : ($fyRow->genc_detail_code ?? '');
                $fyName = is_array($fyRow) ? ($fyRow['genc_detail_name'] ?? $fyCode) : ($fyRow->genc_detail_name ?? $fyCode);
              ?>
              <option value="<?php echo csrOblDashText($fyCode); ?>" <?php echo $yearTo == $fyCode ? 'selected' : ''; ?>>
                <?php echo csrOblDashText($fyName); ?>
              </option>
            <?php } ?>
          <?php } ?>
        </select>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
        <label class="form-label mb-1 f-w-600" for="csrOblLocation">Location</label>
        <select name="location_id" id="csrOblLocation" class="form-control js-example-basic-single">
          <option value="">All</option>
          <?php if (count((array)$locations) > 0) { ?>
            <?php foreach ($locations as $loc) { ?>
              <?php
                $locId = is_array($loc) ? ($loc['id'] ?? '') : ($loc->id ?? '');
                $locName = is_array($loc) ? ($loc['location_name'] ?? '') : ($loc->location_name ?? '');
              ?>
              <option value="<?php echo csrOblDashText($locId); ?>" <?php echo $locationId == $locId ? 'selected' : ''; ?>>
                <?php echo csrOblDashText($locName); ?>
              </option>
            <?php } ?>
          <?php } ?>
        </select>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
        <label class="form-label mb-1 f-w-600" for="csrOblEntity">Entity</label>
        <select name="entity_code" id="csrOblEntity" class="form-control js-example-basic-single">
          <option value="">All</option>
          <?php if (count((array)$entityNames) > 0) { ?>
            <?php foreach ($entityNames as $ent) { ?>
              <?php
                $entCode = is_array($ent) ? ($ent['entity_code'] ?? '') : ($ent->entity_code ?? '');
              ?>
              <option value="<?php echo csrOblDashText($entCode); ?>" <?php echo $entityCode == $entCode ? 'selected' : ''; ?>>
                <?php echo csrOblDashText($entCode); ?>
              </option>
            <?php } ?>
          <?php } ?>
        </select>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 mb-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary btn-sm" id="csrOblFilterBtn">
          <i class="fa fa-filter"></i> Filter
        </button>
      </div>
    </div>
  </form>

  <div class="row mt-3">
    <div class="col-xl-3 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label text-c-green">Overall Utilization</span>
          <h4 class="csr-obl-kpi-value text-c-green text-right"><?php echo csrOblDashText(csrOblDashPct($overallUtilization)); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">(A) Total CSR Obligation</span>
          <h4 class="csr-obl-kpi-value  text-right"><?php echo csrOblDashText(csrOblDashMoney($totalObligation)); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">(b) Amount Spent</span>
          <h4 class="csr-obl-kpi-value text-right"><?php echo csrOblDashText(csrOblDashMoney($totalSpent)); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">Total Spent in %</span>
          <h4 class="csr-obl-kpi-value text-c-green text-right"><?php echo csrOblDashText(csrOblDashPct($overallUtilization)); ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-2">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="f-w-600 mb-3">CSR Obligation Tracking</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-sm csr-obl-table mb-0" id="csrOblProjectTable">
              <thead>
                <tr>
                  <th>Project Name</th>
                  <th>Type</th>
                  <th>Entity</th>
                  <th>Location</th>
                  <th>Status</th>
                  <th>Total Amount</th>
                  <?php if (count((array)$fyRange) > 0) { ?>
                    <?php foreach ($fyRange as $fyCode) { ?>
                      <th>FY <?php echo csrOblDashText($fyCode); ?></th>
                    <?php } ?>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <tr class="csr-obl-summary-row">
                  <td colspan="5"><strong>(B) Year on Year CSR Obligation</strong></td>
                  <td><strong><?php echo csrOblDashText(csrOblDashMoney($totalObligation)); ?></strong></td>
                  <?php if (count((array)$yoyObligation) > 0) { ?>
                    <?php foreach ($yoyObligation as $amt) { ?>
                      <td><strong><?php echo csrOblDashText(csrOblDashMoney($amt)); ?></strong></td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php $csvdata[] = csrOblDashCsvBaseRow('(B) Year on Year CSR Obligation', $totalObligation, $fyRange, $yoyObligation, false); ?>

                <tr class="csr-obl-summary-row">
                  <td colspan="5"><strong>(D) Total Spent in INR</strong></td>
                  <td><strong><?php echo csrOblDashText(csrOblDashMoney($totalSpent)); ?></strong></td>
                  <?php if (count((array)$yoySpent) > 0) { ?>
                    <?php foreach ($yoySpent as $amt) { ?>
                      <td><strong><?php echo csrOblDashText(csrOblDashMoney($amt)); ?></strong></td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php $csvdata[] = csrOblDashCsvBaseRow('(D) Total Spent in INR', $totalSpent, $fyRange, $yoySpent, false); ?>

                <tr class="csr-obl-summary-row csr-obl-pct-row">
                  <td colspan="5"><strong>Year on Year Spent in %</strong></td>
                  <td><strong><?php echo csrOblDashText(csrOblDashPct($overallUtilization)); ?></strong></td>
                  <?php if (count((array)$yoySpentPct) > 0) { ?>
                    <?php foreach ($yoySpentPct as $pct) { ?>
                      <td class="<?php echo csrOblDashSpentPctClass($pct); ?>">
                        <strong><?php echo csrOblDashText(csrOblDashPct($pct)); ?></strong>
                      </td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php $csvdata[] = csrOblDashCsvBaseRow('Year on Year Spent in %', csrOblDashPct($overallUtilization), $fyRange, $yoySpentPct, true); ?>

                <?php if (count((array)$projectGroups) > 0) { ?>
                  <?php foreach ($projectGroups as $group) { ?>
                    <tr class="csr-obl-group-header">
                      <td colspan="<?php echo (int)$colCount; ?>">
                        <strong><?php echo csrOblDashText($group['category']); ?></strong>
                      </td>
                    </tr>
                    <?php
                      $csvHeaderRow = array(
                        'Project Name' => csrOblDashCsvValue($group['category']),
                        'Type' => '',
                        'Entity' => '',
                        'Location' => '',
                        'Status' => '',
                        'Total Amount' => ''
                      );
                      if (count((array)$fyRange) > 0) {
                        foreach ($fyRange as $fyCode) {
                          $csvHeaderRow['FY ' . $fyCode] = '';
                        }
                      }
                      $csvdata[] = $csvHeaderRow;
                    ?>
                    <?php if (count((array)$group['projects']) > 0) { ?>
                      <?php foreach ($group['projects'] as $project) { ?>
                        <?php
                          $statusClass = 'csr-obl-status-process';
                          if ($project['status'] == 'Completed') {
                            $statusClass = 'csr-obl-status-completed';
                          } else if ($project['status'] == 'Not Started') {
                            $statusClass = 'csr-obl-status-notstarted';
                          }
                          $csvRow = array(
                            'Project Name' => csrOblDashCsvValue($project['name']),
                            'Type' => csrOblDashCsvValue($project['type']),
                            'Entity' => csrOblDashCsvValue($project['entity']),
                            'Location' => csrOblDashCsvValue($project['location']),
                            'Status' => csrOblDashCsvValue($project['status']),
                            'Total Amount' => (float)$project['total']
                          );
                        ?>
                        <tr>
                          <td><?php echo csrOblDashText($project['name']); ?></td>
                          <td><?php echo csrOblDashText($project['type']); ?></td>
                          <td><?php echo csrOblDashText($project['entity']); ?></td>
                          <td><?php echo csrOblDashText($project['location']); ?></td>
                          <td><span class="csr-obl-status <?php echo csrOblDashText($statusClass); ?>"><?php echo csrOblDashText($project['status']); ?></span></td>
                          <td><?php echo csrOblDashText(csrOblDashMoney($project['total'])); ?></td>
                          <?php if (count((array)$project['years']) > 0) { ?>
                            <?php foreach ($project['years'] as $yIdx => $yearAmt) { ?>
                              <?php
                                $fyCodeCol = isset($fyRange[$yIdx]) ? $fyRange[$yIdx] : '';
                                $csvRow['FY ' . $fyCodeCol] = is_numeric($yearAmt) ? (float)$yearAmt : '-';
                              ?>
                              <td><?php echo is_numeric($yearAmt) ? csrOblDashText(csrOblDashMoney($yearAmt)) : '-'; ?></td>
                            <?php } ?>
                          <?php } ?>
                        </tr>
                        <?php $csvdata[] = $csvRow; ?>
                      <?php } ?>
                    <?php } ?>
                    <tr class="csr-obl-category-row">
                      <td colspan="5"><strong><?php echo csrOblDashText($group['category']); ?> Total</strong></td>
                      <td><strong><?php echo csrOblDashText(csrOblDashMoney($group['total'])); ?></strong></td>
                      <?php
                        $csvTotalRow = array(
                          'Project Name' => csrOblDashCsvValue($group['category'] . ' Total'),
                          'Type' => '',
                          'Entity' => '',
                          'Location' => '',
                          'Status' => '',
                          'Total Amount' => (float)$group['total']
                        );
                      ?>
                      <?php if (count((array)$group['years']) > 0) { ?>
                        <?php foreach ($group['years'] as $yIdx => $yearAmt) { ?>
                          <?php
                            $fyCodeCol = isset($fyRange[$yIdx]) ? $fyRange[$yIdx] : '';
                            $csvTotalRow['FY ' . $fyCodeCol] = (float)$yearAmt;
                          ?>
                          <td><strong><?php echo csrOblDashText(csrOblDashMoney($yearAmt)); ?></strong></td>
                        <?php } ?>
                      <?php } ?>
                    </tr>
                    <?php $csvdata[] = $csvTotalRow; ?>
                  <?php } ?>

                  <tr class="csr-obl-grand-total">
                    <td colspan="5"><strong>Grand Total</strong></td>
                    <td><strong><?php echo csrOblDashText(csrOblDashMoney($grandTotal)); ?></strong></td>
                    <?php if (count((array)$grandYears) > 0) { ?>
                      <?php foreach ($grandYears as $yearAmt) { ?>
                        <td><strong><?php echo csrOblDashText(csrOblDashMoney($yearAmt)); ?></strong></td>
                      <?php } ?>
                    <?php } ?>
                  </tr>
                  <?php $csvdata[] = csrOblDashCsvBaseRow('Grand Total', $grandTotal, $fyRange, $grandYears, false); ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="<?php echo (int)$colCount; ?>" class="text-center">No Records</td>
                  </tr>
                  <?php
                    $emptyCsv = array(
                      'Project Name' => 'No Records',
                      'Type' => '',
                      'Entity' => '',
                      'Location' => '',
                      'Status' => '',
                      'Total Amount' => ''
                    );
                    if (count((array)$fyRange) > 0) {
                      foreach ($fyRange as $fyCode) {
                        $emptyCsv['FY ' . $fyCode] = '';
                      }
                    }
                    $csvdata[] = $emptyCsv;
                  ?>
                <?php } ?>

                <tr class="csr-obl-summary-row">
                  <td colspan="5"><strong>(E) Total Un-Spent in INR</strong></td>
                  <td class="<?php echo $finalUnspent < 0 ? 'text-danger' : ''; ?>">
                    <strong><?php echo csrOblDashText(csrOblDashMoney($finalUnspent)); ?></strong>
                  </td>
                  <?php if (count((array)$yoyUnspent) > 0) { ?>
                    <?php foreach ($yoyUnspent as $amt) { ?>
                      <td class="<?php echo $amt < 0 ? 'text-danger' : ''; ?>">
                        <strong><?php echo csrOblDashText(csrOblDashMoney($amt)); ?></strong>
                      </td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php $csvdata[] = csrOblDashCsvBaseRow('(E) Total Un-Spent in INR', $finalUnspent, $fyRange, $yoyUnspent, false); ?>

                <tr class="csr-obl-summary-row csr-obl-pct-row">
                  <td colspan="5"><strong>Year on Year Un-Spent in %</strong></td>
                  <td><strong><?php echo csrOblDashText(csrOblDashPct($totalUnspentPct)); ?></strong></td>
                  <?php if (count((array)$yoyUnspentPct) > 0) { ?>
                    <?php foreach ($yoyUnspentPct as $pct) { ?>
                      <td class="<?php echo $pct < 0 ? 'text-danger' : ''; ?>">
                        <strong><?php echo csrOblDashText(csrOblDashPct($pct)); ?></strong>
                      </td>
                    <?php } ?>
                  <?php } ?>
                </tr>
                <?php $csvdata[] = csrOblDashCsvBaseRow('Year on Year Un-Spent in %', csrOblDashPct($totalUnspentPct), $fyRange, $yoyUnspentPct, true); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-2">
    <div class="col-xl-4 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">(C) Final Unspent Amount / Final Obligation Balance</span>
          <h4 class="csr-obl-kpi-value text-right <?php echo $finalUnspent < 0 ? 'text-danger' : 'text-c-green'; ?>">
            <?php echo csrOblDashText(csrOblDashMoney($finalUnspent)); ?>
          </h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">Total Unspent in %</span>
          <h4 class="csr-obl-kpi-value  text-right text-c-green"><?php echo csrOblDashText(csrOblDashPct($totalUnspentPct)); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-12">
      <div class="card csr-obl-kpi">
        <div class="card-body">
          <span class="csr-obl-kpi-label">Location-wise Spending</span>
          <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered mb-0 csr-obl-mini-table">
              <thead>
                <tr>
                  <th>Location</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count((array)$locationRows) > 0) { ?>
                  <?php foreach ($locationRows as $loc) { ?>
                    <?php
                      $locStatusClass = 'csr-obl-status-process';
                      if ($loc['status'] == 'Completed') {
                        $locStatusClass = 'csr-obl-status-completed';
                      } else if ($loc['status'] == 'Not Started') {
                        $locStatusClass = 'csr-obl-status-notstarted';
                      }
                    ?>
                    <tr>
                      <td><?php echo csrOblDashText($loc['location']); ?></td>
                      <td><?php echo csrOblDashText(csrOblDashMoney($loc['amount'])); ?></td>
                      <td><span class="csr-obl-status <?php echo csrOblDashText($locStatusClass); ?>"><?php echo csrOblDashText($loc['status']); ?></span></td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="3" class="text-center">No Records</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$_SESSION['download_data'] = $csvdata;
?>

<script type="text/javascript" src="assets/js/custom_view_csr_obligation_dashboard.js"></script>
