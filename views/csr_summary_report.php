<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$url = API . "?mode=getCsrSummaryReport&company_id=".$_SESSION['company_id'];
$apires = callAPI($url);
$result = json_decode($apires, true);
$summary = $result['summary'] ?? [];

// Reverse order to show latest FY first
// $summary = array_reverse($summary);
?>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>CSR SUMMARY REPORT</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <table class="table table-bordered table-sm f-14 align-middle">

          <tr>
            <th>Particulars</th>
            <th class="text-center">Amount</th>
          </tr>

          <?php foreach ($summary as $fy) { ?>
            <!-- Block Header -->
            <tr>
              <th colspan="2" class="bg-light text-center">
                CSR Summary for FY <?= htmlspecialchars($fy['label']) ?>
              </th>
            </tr>

            <!-- Gross Required -->
            <tr>
              <th>a) Gross amount required to be spent by the Company as per the Companies Act, 2013 during the year FY <?= $fy['label'] ?></th>
              <th class="text-end"><?= number_format($fy['gross_required'], 2) ?></th>
            </tr>
            <tr>
              <td>i) Construction/acquisition of any asset</td>
              <td align="right"></td>
            </tr>
            <tr>
              <td>ii) On purposes other than (i) above</td>
              <td align="right"></td>
            </tr>

            <!-- Total Spent -->
            <tr>
              <th class="text-danger">(b) Total Spent</th>
              <th class="text-danger text-end"><?= number_format($fy['total_spent'], 2) ?></th>
            </tr>

            <!-- Unspent Current Year -->
            <tr>
              <th>Balance amount unspent for FY <?= $fy['label'] ?></th>
              <td align="right"><?= number_format($fy['current_unspent'], 2) ?></td>
            </tr>

            <!-- Previous Year CF -->
            <tr>
              <th>C/F Balance amount unspent for FY <?= ($fy['year'] - 1) ?>-<?= substr(($fy['year'] - 1), -2) ?></th>
              <th class="text-end"><?= number_format($fy['carry_forward_previous'], 2) ?></th>
            </tr>
            <tr>
              <td>Less :- Amount spent in FY <?= $fy['label'] ?></td>
              <th class="text-end"><?= number_format($fy['spent_from_py'], 2) ?></th>
            </tr>
            <tr>
              <th>Balance amount unspent for FY <?= ($fy['year'] - 1) ?>-<?= substr(($fy['year'] - 1), -2) ?></th>
              <th class="text-end"><?= number_format($fy['cf_balance_py'], 2) ?></th>
            </tr>

            <!-- Net Unspent -->
            <tr>
              <th>Amount remaining unspent during FY <?= $fy['label'] ?> after adjsting PY O/s</th>
              <th class="text-end"><?= number_format($fy['remaining_unspent'], 2) ?></th>
            </tr>
          <?php } ?>

        </table>

      </div>
    </div>
  </div>
</div>
