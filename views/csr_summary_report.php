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
      <h5><?= __('CSR SUMMARY REPORT') ?></h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <table class="table table-bordered table-sm f-14 align-middle">

          <tr>
            <th scope="col"><?= __('Particulars') ?></th>
            <th scope="col" class="text-center"><?= __('Amount') ?></th>
          </tr>

          <?php foreach ($summary as $fy) { ?>
            <!-- Block Header -->
            <tr>
              <th scope="col" colspan="2" class="bg-light text-center">
                <?= __('CSR Summary for FY') ?> <?= htmlspecialchars($fy['label']) ?>
              </th>
            </tr>

            <!-- Gross Required -->
            <tr>
              <th scope="col">a) <?= __('Gross amount required to be spent by the Company as per the Companies Act, 2013 during the year FY') ?> <?= $fy['label'] ?></th>
              <th scope="col" class="text-end"><?= number_format($fy['gross_required'], 2) ?></th>
            </tr>
            <tr>
              <td>i) <?= __('Construction/acquisition of any asset') ?></td>
              <td align="right"></td>
            </tr>
            <tr>
              <td>ii) <?= __('On purposes other than (i) above') ?></td>
              <td align="right"></td>
            </tr>

            <!-- Total Spent -->
            <tr>
              <th scope="col" class="text-danger">(b) <?= __('Total Spent') ?></th>
              <th scope="col" class="text-danger text-end"><?= number_format($fy['total_spent'], 2) ?></th>
            </tr>

            <!-- Unspent Current Year -->
            <tr>
              <th scope="col"><?= __('Balance amount unspent for FY') ?> <?= $fy['label'] ?></th>
              <td align="right"><?= number_format($fy['current_unspent'], 2) ?></td>
            </tr>

            <!-- Previous Year CF -->
            <tr>
              <th scope="col"><?= __('C/F Balance amount unspent for FY') ?> <?= ($fy['year'] - 1) ?>-<?= substr(($fy['year'] - 1), -2) ?></th>
              <th scope="col" class="text-end"><?= number_format($fy['carry_forward_previous'], 2) ?></th>
            </tr>
            <tr>
              <td><?= __('Less :- Amount spent in FY') ?> <?= $fy['label'] ?></td>
              <th scope="col" class="text-end"><?= number_format($fy['spent_from_py'], 2) ?></th>
            </tr>
            <tr>
              <th scope="col">Balance amount unspent for FY <?= ($fy['year'] - 1) ?>-<?= substr(($fy['year'] - 1), -2) ?></th>
              <th scope="col" class="text-end"><?= number_format($fy['cf_balance_py'], 2) ?></th>
            </tr>

            <!-- Net Unspent -->
            <tr>
              <th scope="col"><?= __('Amount remaining unspent during FY') ?> <?= $fy['label'] ?> <?= __('after adjsting PY O/s') ?></th>
              <th scope="col" class="text-end"><?= number_format($fy['remaining_unspent'], 2) ?></th>
            </tr>
          <?php } ?>

        </table>

      </div>
    </div>
  </div>
</div>
