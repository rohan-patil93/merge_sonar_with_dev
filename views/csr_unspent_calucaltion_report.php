<?php


$url = API . "?mode=getCsrUnspentCalculationReport";
$apires = callAPI($url);
$result = json_decode($apires, true);
$report = $result['report'] ?? [];
// echo '<pre>'; print_r($report); echo '</pre>';
// Reverse order: current FY on the left, oldest FY on the right
$report = array_reverse($report);
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5><?= __('CSR UNSPENT CALCULATION REPORT') ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm f-14 align-middle">

                    <!-- 🟦 Static Headers -->
                    <tr>
                        <th scope="col" colspan="2"><?= __('MICROCHIP TECHNOLOGY (INDIA) PRIVATE LIMITED') ?></th>
                        <th scope="col" colspan="<?= count((array)$report) ?>"></th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="2"><?= __('Statutory Audit for the year ended March 31,') ?> <?= end($report)['year'] ?></th>
                        <th scope="col" colspan="<?= count((array)$report) ?>"></th>
                    </tr>
                    <tr>
                        <td colspan="2"><?= __('Computation of Net Profit for CSR contribution as per Section 198') ?></td>
                        <th scope="col" colspan="<?= count((array)$report) ?>"></th>
                    </tr>

                    <!-- 📅 Financial Year Row -->
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Particulars') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <th scope="col" class="text-center"><?= __('FY'); ?> <?= ($r['year_label']) ?></th>
                        <?php } ?>
                    </tr>

                    <!-- 💰 Actual Dynamic Data Rows -->
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Net profit before tax MTIPL') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end fw-bold"><?= number_format($r['budget']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- Other entities (empty) -->
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Net profit before tax MSSIPL') ?></th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Net profit before tax MIPL') ?></th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Net profit before tax MCIPL') ?></th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Net profit before tax Symmetricom') ?></th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <!-- Static Dynamic Row -->
                    <tr>
                        <td></td>
                        <th scope="col"><?= __('Difference') ?></th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th scope="col"><?= __('Add:') ?></th>
                        <th scope="col"><?= __('Allowed Credits') ?></th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td><?= __('Profit on sale of immovable property') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right"></td>
                        <td><?= __('( Original Cost - WDV )') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td colspan="<?= 2 + count((array)$report) ?>">-</td>
                    </tr>

                    <tr>
                        <th scope="col"><?= __('Less:') ?></th>
                        <th scope="col"><?= __('Credits Disallowed') ?></th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td><?= __('Premium on shares or debentures') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td><?= __('Profit on sale of forfeited shares') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td><?= __('Profit on sale of immovable property') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right"></td>
                        <td><?= __('(Sale Value of Immovable Property - Original Cost)') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td><?= __('Surplus in P&L on measurement of asset or liability at fair value') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th scope="col"><?= __('Less:') ?></th>
                        <th scope="col"><?= __('Expenses Allowed') ?></th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td><?= __('All the usual Working Charges') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td><?= __("Director's Remuneration") ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td><?= __('Bonus or Commission paid to Staff') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td><?= __('Tax on excess or abnormal profits') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">5</td>
                        <td><?= __('Tax on business profits imposed for special reasons') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">6</td>
                        <td><?= __('Interest on Debentures') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">7</td>
                        <td><?= __('Interest on Loans') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">8</td>
                        <td><?= __('Expenses on repairs ( other than Capital Expenditure )') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">9</td>
                        <td><?= __('Contributions made under section 181 ( Bonafide Charitable Trusts )') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">10</td>
                        <td><?= __('Depreciation') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">11</td>
                        <td><?= __('Prior period items') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">12</td>
                        <td><?= __('Legal liability for compensation or damages') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">13</td>
                        <td><?= __('Insurance Expenses') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th scope="col"><?= __('Add:') ?></th>
                        <th scope="col"><?= __('Expenses Disallowed') ?></th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td><?= __('Income Tax') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td><?= __('Compensations, damages or payments made voluntarily') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td><?= __('Capital Loss on sale of undertaking or part thereof (Not include losses on sale of asset)') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td><?= __('Expenditure in P&L on measurement of asset or liability at fair value') ?></td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <!-- 📊 Net profit as per Section 198 -->
                    <tr>
                        <td align="right"></td>
                        <th scope="col"><?= __('Net profit as per Section 198') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end fw-bold"><?= number_format($r['budget']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🧮 IN MN. INR -->
                    <tr>
                        <td align="right"></td>
                        <td><?= __('IN MN. INR') ?></td>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['in_mn'], 2) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 📈 Average Net Profit -->
                    <tr>
                        <td align="right">(!)</td>
                        <th scope="col" class="text-danger"><?= __('Average net profit for last three preceding financial years') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= $r['avg_net_profit'] ? number_format($r['avg_net_profit']) : '-' ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🪙 CSR 2% Amount -->
                    <tr>
                        <td align="right">(!!)</td>
                        <th scope="col" class="text-danger"><?= __('Amount to be spent on CSR as on March 31 (2% Amt)') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end text-danger"><?= number_format($r['two_percent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 💸 Amount spent -->
                    <tr>
                        <td align="right">(!!!)</td>
                        <th scope="col"><?= __('Amount spent on CSR as on March 31') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end "><?= number_format($r['actual_spent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟥 Unspent / Overspent -->
                    <tr>
                        <td align="right">(!!!!)</td>
                        <th scope="col"><?= __('Amount unspent (+) / Overspent on CSR (-)') ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['unspent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟨 Carry forward -->
                    <tr>
                        <td align="right"></td>
                        <td><?= __('Less : Excess Spent in P.Y. Adjusted in C.Y.') ?></td>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['carry_forward_used']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🧾 Provision -->
                    <tr>
                        <td align="right"><?= __('(iv)'); ?></td>
                        <th scope="col"><?= __('Amt to be Debited to P&L (Provision amt) as of 31-Mar-') ?><?= $report[0]['year'] ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['provision']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟩 Net Amt -->
                    <tr>
                        <td align="right">(v)</td>
                        <th scope="col"><?= __('Net Amt to be Debited to P&L other than Provision amt as of 31-Mar-') ?><?= $report[0]['year'] ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['net_debit']) ?></td>
                        <?php } ?>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>