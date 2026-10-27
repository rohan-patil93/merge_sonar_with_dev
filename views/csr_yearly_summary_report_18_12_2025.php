<?php
$_SESSION['apply_js']['multiselest'] = 1;

$todayYear = date("Y");
$todayMonth = date("n");

if ($todayMonth >= 4) {
    $currentFY = $todayYear + 1; // FY 2024–25 represented as 2025 (ending year)
} else {
    $currentFY = $todayYear;
}

$selectedYear = $_POST['year'] ?? $currentFY;


$url = API . "?mode=getCsrYearlySummaryReport&company_id=" . $_SESSION['company_id'] . "&year=" . $selectedYear;

$apires = callAPI($url);
$result = json_decode($apires, true);
$summary = $result['summary'] ?? [];

// Convert FY label from numeric (2025) → “2024–25”
function fyLabel($year) {
    return ($year - 1) . "-" . substr($year, -2);
}
?>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>CSR YEARLY SUMMARY REPORT</h5>
    </div>

    <div class="card-body">

      <!-- ============================
           YEAR FILTER FORM
      ============================= -->
      <form method="POST" id="yearForm">
        <div class="row mb-3">
          <div class="col-md-3">
            <label><b>Financial Year</b></label>
            <select name="year" class="form-control js-example-basic-single" onchange="document.getElementById('yearForm').submit();">
              <?php
                for ($y = 2018; $y <= $currentFY; $y++) {
                    $label = fyLabel($y);
                    $sel = ($y == $selectedYear) ? "selected" : "";
                    echo "<option value='$y' $sel>$label</option>";
                }
              ?>
            </select>
          </div>
        </div>
      </form>

      <!-- ============================
           SUMMARY TABLE
      ============================= -->
      <div class="table-responsive">
        <table class="table table-bordered table-sm f-14 align-middle">

          <tr>
            <th>Particulars</th>
            <th class="text-center">Amount</th>
          </tr>

          <?php if (!empty($summary)) {

            // We expect only ONE year's summary
            $fy = $summary;
            $label = fyLabel($fy['year']);
          ?>

          <!-- Block Header -->
          <tr>
            <th colspan="2" class="bg-light text-center">
              CSR Summary for FY <?= htmlspecialchars($label) ?>
            </th>
          </tr>

          <!-- Gross Required -->
          <tr>
            <th>a) Gross amount required to be spent by the Company as per the Companies Act, 2013 during FY <?= $label ?></th>
            <th class="text-end"><?= number_format($fy['gross_required'], 2) ?></th>
          </tr>

          <tr>
            <td>i) Construction/acquisition of any asset</td>
            <td></td>
          </tr>

          <tr>
            <td>ii) On purposes other than (i)</td>
            <td></td>
          </tr>

          <!-- Total Spent -->
          <tr>
            <th class="text-danger">(b) Total Spent</th>
            <th class="text-danger text-end"><?= number_format($fy['total_spent'], 2) ?></th>
          </tr>

          <!-- Unspent Current Year -->
          <tr>
            <th>Balance amount unspent for FY <?= $label ?></th>
            <td class="text-end"><?= number_format($fy['current_unspent'], 2) ?></td>
          </tr>

          <!-- Previous Year CF -->
          <tr>
            <th>C/F Balance amount unspent for FY <?= fyLabel($fy['year'] - 1) ?></th>
            <th class="text-end"><?= number_format($fy['carry_forward_previous'], 2) ?></th>
          </tr>

          <tr>
            <td>Less: Amount spent in FY <?= $label ?></td>
            <th class="text-end"><?= number_format($fy['spent_from_py'], 2) ?></th>
          </tr>

          <tr>
            <th>Balance amount unspent for FY <?= fyLabel($fy['year'] - 1) ?></th>
            <th class="text-end"><?= number_format($fy['cf_balance_py'], 2) ?></th>
          </tr>

          <!-- Net Unspent -->
          <tr>
            <th>Amount remaining unspent during FY <?= $label ?> after adjusting PY</th>
            <th class="text-end"><?= number_format($fy['remaining_unspent'], 2) ?></th>
          </tr>

          <?php } else { ?>
            <tr>
              <td colspan="2" class="text-center text-danger">No data available for selected financial year.</td>
            </tr>
          <?php } ?>

        </table>
      </div>

    </div>
  </div>
</div>
