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
            <h5>CSR UNSPENT CALCULATION REPORT</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm f-14 align-middle">

                    <!-- 🟦 Static Headers -->
                    <tr>
                        <th colspan="2">MICROCHIP TECHNOLOGY (INDIA) PRIVATE LIMITED</th>
                        <th colspan="<?= count((array)$report) ?>"></th>
                    </tr>
                    <tr>
                        <th colspan="2">Statutory Audit for the year ended March 31, <?= end($report)['year'] ?></th>
                        <th colspan="<?= count((array)$report) ?>"></th>
                    </tr>
                    <tr>
                        <td colspan="2">Computation of Net Profit for CSR contribution as per Section 198</td>
                        <th colspan="<?= count((array)$report) ?>"></th>
                    </tr>

                    <!-- 📅 Financial Year Row -->
                    <tr>
                        <td></td>
                        <th>Particulars</th>
                        <?php foreach ($report as $r) { ?>
                            <th class="text-center">FY <?= ($r['year_label']) ?></th>
                        <?php } ?>
                    </tr>

                    <!-- 💰 Actual Dynamic Data Rows -->
                    <tr>
                        <td></td>
                        <th>Net profit before tax MTIPL</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end fw-bold"><?= number_format($r['budget']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- Other entities (empty) -->
                    <tr>
                        <td></td>
                        <th>Net profit before tax MSSIPL</th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th>Net profit before tax MIPL</th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th>Net profit before tax MCIPL</th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <tr>
                        <td></td>
                        <th>Net profit before tax Symmetricom</th><?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>
                    <!-- Static Dynamic Row -->
                    <tr>
                        <td></td>
                        <th>Difference</th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th>Add:</th>
                        <th>Allowed Credits</th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td>Profit on sale of immovable property</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right"></td>
                        <td>( Original Cost - WDV )</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td colspan="<?= 2 + count((array)$report) ?>">-</td>
                    </tr>

                    <tr>
                        <th>Less:</th>
                        <th>Credits Disallowed</th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td>Premium on shares or debentures</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td>Profit on sale of forfeited shares</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td>Profit on sale of immovable property</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right"></td>
                        <td>(Sale Value of Immovable Property - Original Cost)</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td>Surplus in P&L on measurement of asset or liability at fair value</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th>Less:</th>
                        <th>Expenses Allowed</th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td>All the usual Working Charges</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td>Director's Remuneration</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td>Bonus or Commission paid to Staff</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td>Tax on excess or abnormal profits</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">5</td>
                        <td>Tax on business profits imposed for special reasons</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">6</td>
                        <td>Interest on Debentures</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">7</td>
                        <td>Interest on Loans</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">8</td>
                        <td>Expenses on repairs ( other than Capital Expenditure )</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">9</td>
                        <td>Contributions made under section 181 ( Bonafide Charitable Trusts )</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">10</td>
                        <td>Depreciation</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">11</td>
                        <td>Prior period items</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">12</td>
                        <td>Legal liability for compensation or damages</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">13</td>
                        <td>Insurance Expenses</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <th>Add:</th>
                        <th>Expenses Disallowed</th>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">1</td>
                        <td>Income Tax</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">2</td>
                        <td>Compensations, damages or payments made voluntarily</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">3</td>
                        <td>Capital Loss on sale of undertaking or part thereof (Not include losses on sale of asset)</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <tr>
                        <td align="right">4</td>
                        <td>Expenditure in P&L on measurement of asset or liability at fair value</td>
                        <?php foreach ($report as $r) { ?><td class="text-end"></td><?php } ?>
                    </tr>

                    <!-- 📊 Net profit as per Section 198 -->
                    <tr>
                        <td align="right"></td>
                        <th>Net profit as per Section 198</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end fw-bold"><?= number_format($r['budget']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🧮 IN MN. INR -->
                    <tr>
                        <td align="right"></td>
                        <td>IN MN. INR</td>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['in_mn'], 2) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 📈 Average Net Profit -->
                    <tr>
                        <td align="right">(!)</td>
                        <th class="text-danger">Average net profit for last three preceding financial years</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= $r['avg_net_profit'] ? number_format($r['avg_net_profit']) : '-' ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🪙 CSR 2% Amount -->
                    <tr>
                        <td align="right">(!!)</td>
                        <th class="text-danger">Amount to be spent on CSR as on March 31 (2% Amt)</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end text-danger"><?= number_format($r['two_percent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 💸 Amount spent -->
                    <tr>
                        <td align="right">(!!!)</td>
                        <th>Amount spent on CSR as on March 31</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end "><?= number_format($r['actual_spent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟥 Unspent / Overspent -->
                    <tr>
                        <td align="right">(!!!!)</td>
                        <th>Amount unspent (+) / Overspent on CSR (-)</th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['unspent']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟨 Carry forward -->
                    <tr>
                        <td align="right"></td>
                        <td>Less : Excess Spent in P.Y. Adjusted in C.Y.</td>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['carry_forward_used']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🧾 Provision -->
                    <tr>
                        <td align="right">(iv)</td>
                        <th>Amt to be Debited to P&L (Provision amt) as of 31-Mar-<?= $report[0]['year'] ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['provision']) ?></td>
                        <?php } ?>
                    </tr>

                    <!-- 🟩 Net Amt -->
                    <tr>
                        <td align="right">(v)</td>
                        <th>Net Amt to be Debited to P&L other than Provision amt as of 31-Mar-<?= $report[0]['year'] ?></th>
                        <?php foreach ($report as $r) { ?>
                            <td class="text-end"><?= number_format($r['net_debit']) ?></td>
                        <?php } ?>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>