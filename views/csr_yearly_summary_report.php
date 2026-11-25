<?php
$_SESSION['apply_js']['multiselest'] = 1;

$todayYear  = date("Y");
$todayMonth = date("n");

if ($todayMonth >= 4) {
    // Example: May 2025 → FY 2025-26
    $startYear = $todayYear;
    $endYear   = $todayYear + 1;
} else {
    // Example: Feb 2025 → FY 2024-25
    $startYear = $todayYear - 1;
    $endYear   = $todayYear;
}

$currentFY = $startYear . '-' . substr($endYear, -2);

// If user selects year from dropdown use that, otherwise use current FY
$selectedYear = $_POST['year'] ?? $currentFY;


$url = API . "?mode=getCsrYearlySummaryReport&company_id=" . $_SESSION['company_id'] . "&year=" . $selectedYear;

$apires = callAPI($url);
$result = json_decode($apires, true);
$summary = $result['summary'] ?? [];
$financial_years = $result['financial_years'] ?? [];

// Convert FY label from numeric (2025) → “2024–25”
function fyLabel($year) {
    return ($year - 1) . "-" . substr($year, -2);
}
?>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5><?= __('CSR YEARLY SUMMARY REPORT') ?></h5>
    </div>

    <div class="card-body">

      <!-- ============================
           YEAR FILTER FORM
      ============================= -->
      <form method="POST" id="yearForm">
        <div class="row mb-3">
          <div class="col-md-3">
            <span class="d-inline-block fw-bold" style="margin-bottom:5px"><b><?= __('Financial Year') ?></b></span>
            <select aria-label="<?= htmlspecialchars(__('Year'), ENT_QUOTES, 'UTF-8'); ?>" name="year"
        class="form-control js-example-basic-single"
        id="csrYearSelect">
              <?php foreach($financial_years as $row){
                $selected = (isset($selectedYear) && $selectedYear == $row['genc_detail_code']) ? 'selected' : '';
                ?>
                <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $selected; ?>> <?= htmlspecialchars(__($row['genc_detail_name'] ?? '')) ?></option>

              <?php  } ?>
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
            <th scope="col"><?= __('Particulars') ?></th>
            <th scope="col" class="text-center"><?= __('Amount') ?></th>
          </tr>

          <?php if (!empty($summary)) {

            // We expect only ONE year's summary
            $fy = $summary;
            $label = $fy['year'];
          ?>

          <!-- Block Header -->
          <tr>
            <th scope="col" colspan="2" class="bg-light text-center">
              <?= __('CSR Summary for FY') ?> <?= htmlspecialchars($label) ?>
            </th>
          </tr>

          <!-- Gross Required -->
          <tr>
            <th scope="col">a) <?= __('Gross amount required to be spent by the Company as per the Companies Act, 2013 during FY') ?> <?= $label ?></th>
            <th scope="col" class="text-end"><?= number_format($fy['gross_required'], 2) ?></th>
          </tr>

          <tr>
            <td>i) <?= __('Construction/acquisition of any asset') ?></td>
            <td></td>
          </tr>

          <tr>
            <td><?= __('ii)'); ?> <?= __('On purposes other than (i)') ?></td>
            <td></td>
          </tr>

          <!-- Total Spent -->
          <tr>
            <th scope="col" class="text-danger">(b) <?= __('Total Spent') ?></th>
            <th scope="col" class="text-danger text-end"><?= number_format($fy['total_spent'], 2) ?></th>
          </tr>

          <!-- Unspent Current Year -->
          <tr>
            <th scope="col"><?= __('Balance amount unspent for FY') ?> <?= $label ?></th>
            <td class="text-end"><?= number_format($fy['current_unspent'], 2) ?></td>
          </tr>

         

          <?php } else { ?>
            <tr>
              <td colspan="2" class="text-center text-danger"><?= __('No data available for selected financial year.') ?></td>
            </tr>
          <?php } ?>

        </table>
      </div>

    </div>
  </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/js/csr_yearly_summary_report.js"></script>
