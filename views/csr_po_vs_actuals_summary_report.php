<?php
// include_once('../includes/session.php');
// include_once('../dbconfig.php');
// include_once('../comm.php');

// ✅ API Call
$url = API . "?mode=getCsrPoVsActualsSummary&start_fy=".STARTING_YEAR;
$apires = callAPI($url);
$result = json_decode($apires, true);
// echo '<pre>'; print_r($result['sql']); echo '</pre>'; // For debugging
$list = $result['po_vs_actuals_list'] ?? [];
$fy_list = $result['fy_list'] ?? [];

if (!function_exists('csrPoActualsCsvValue')) {
    function csrPoActualsCsvValue($value) {
        if ($value === null || $value === '') {
            return '-';
        }

        if (!is_string($value)) {
            return $value;
        }

        $value = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
        $value = preg_replace('/[\r\n\t]+/', ' ', $value);
        $value = str_replace([',', '"'], [' ', ''], $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value) !== '' ? trim($value) : '-';
    }
}

$normalizedFyList = [];
foreach ($fy_list as $fyRow) {
    if (is_array($fyRow)) {
        $fyText = $fyRow['genc_detail_code'] ?? $fyRow['financial_year'] ?? '';
    } else {
        $fyText = $fyRow;
    }

    if ($fyText === '') {
        continue;
    }

    $normalizedFyList[] = [
        'text' => (string)$fyText,
        'key' => str_replace('-', '_', (string)$fyText)
    ];
}

$rowspanCounts = [];
foreach ($list as $item) {
    $pid = $item['project_id'];
    if (!isset($rowspanCounts[$pid])) {
        $rowspanCounts[$pid] = 0;
    }
    $rowspanCounts[$pid]++;
}

$_SESSION['download_data'] = [];
?>
<link rel="stylesheet" href="assets/css/csr_po_vs_actuals_summary_report.css">
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>CSR PO VS ACTUALS SUMMARY</h5>
            <?php if (count((array)$list) > 0) { ?>
                <div class="card-header-right">
                    <div class="dropdown-primary dropdown open">
                        <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-csr-po-actuals"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                        <div class="dropdown-menu p-0" aria-labelledby="dropdown-csr-po-actuals" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <a class="dropdown-item waves-light waves-effect"
                                href="custom_view_download.php?page=CSRPovsActualsSummary&format=csv" target="_blank">CSV</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="card-body">
            <?php if (count((array)$list) > 0) { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle f-12">
                        <thead>
                            <tr>
                                <th class="csr-sticky-col csr-sticky-project bg-primary"></th>
                                <th class="csr-sticky-col csr-sticky-nature bg-primary"></th>
                                <th class="csr-sticky-col csr-sticky-partner text-center bg-primary">PO Details</th>
                                <?php foreach ($normalizedFyList as $fyRow) { ?>
                                    <th colspan="3" class="bg-primary text-white text-center">
                                        FY <?= $fyRow['text']; ?>
                                    </th>
                                <?php } ?>
                                <th colspan="5" class="bg-primary text-white text-center">Total Spent & Unspent Amt. (incl GST)</th>
                            </tr>
                            <tr>
                                <th rowspan="1" class="csr-sticky-col csr-sticky-project bg-dark text-white text-center">Name of the CSR Project</th>
                                <th rowspan="1" class="csr-sticky-col csr-sticky-nature bg-dark text-white text-center">Nature of Work Done</th>
                                <th rowspan="1" class="csr-sticky-col csr-sticky-partner bg-dark text-white text-center">Business Partner Name</th>

                                <?php foreach ($normalizedFyList as $fy) { ?>
                                    <th class="bg-dark text-white text-center">PO No</th>
                                    <th class="bg-dark text-white text-center">PO Issued<br>(Incl. GST)</th>
                                    <th class="bg-dark text-white text-center">Amt. Spent<br>(Incl. GST)</th>
                                <?php } ?>
                                <th class="bg-dark text-white text-center">Total PO Issued</th>
                                <th class="bg-dark text-white text-center">Total Spent Amt.</th>
                                <th class="bg-dark text-white text-center">Spent %</th>
                                <th class="bg-dark text-white text-center">Total Unspent Amt.</th>
                                <th class="bg-dark text-white text-center">Unspent %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $printedProjects = [];
                            $csvdata = [];
                            $fyTotals = [];
                            foreach ($normalizedFyList as $fy) {
                                $fyTotals[$fy['key']] = [
                                    'po_value' => 0,
                                    'spent' => 0
                                ];
                            }
                            $grandTotalPo = 0;
                            $grandTotalSpent = 0;
                            $grandTotalUnspent = 0;

                            foreach ($list as $row) {
                                $pid = $row['project_id'];
                                $csvRow = [
                                    'Name of the CSR Project' => csrPoActualsCsvValue($row['project_name'] ?? '-'),
                                    'Nature of Work Done' => csrPoActualsCsvValue($row['nature_of_work_done'] ?? '-'),
                                    'Business Partner Name' => csrPoActualsCsvValue($row['business_partner_name'] ?? '-')
                                ];
                            ?>
                                <tr>
                                    <?php if (!isset($printedProjects[$pid])) { ?>
                                        <td rowspan="<?php echo $rowspanCounts[$pid]; ?>" class="csr-sticky-col csr-sticky-project bg-success">
                                            <?php echo $row['project_name'] ?? '-'; ?>
                                        </td>
                                        <td rowspan="<?php echo $rowspanCounts[$pid]; ?>" class="csr-sticky-col csr-sticky-nature ws-normal">
                                            <?php echo $row['nature_of_work_done'] ?? '-'; ?>
                                        </td>
                                        <?php $printedProjects[$pid] = true; ?>
                                    <?php } ?>

                                    <td class="csr-sticky-col csr-sticky-partner"><?php echo $row['business_partner_name'] ?? '-'; ?></td>
                                    <?php foreach ($normalizedFyList as $fy) { 
                                        $fyText = $fy['text'];
                                        $fyKey  = $fy['key'];
                                        $poValue = $row["po_value_$fyKey"] ?? 0;
                                        $spentValue = $row["spent_$fyKey"] ?? 0;
                                        $fyTotals[$fyKey]['po_value'] += (float)$poValue;
                                        $fyTotals[$fyKey]['spent'] += (float)$spentValue;
                                        $csvRow["FY $fyText PO No"] = csrPoActualsCsvValue($row["po_no_$fyKey"] ?? '-');
                                        $csvRow["FY $fyText PO Issued (Incl. GST)"] = csrPoActualsCsvValue($row["po_value_$fyKey"] ?? '-');
                                        $csvRow["FY $fyText Amt. Spent (Incl. GST)"] = csrPoActualsCsvValue($row["spent_$fyKey"] ?? '-');
                                        ?>
                                        <td class="text-end"><?php echo $row["po_no_$fyKey"] ?? '-'; ?></td>
                                        <td class="text-end"><?php echo $row["po_value_$fyKey"] ?? '-'; ?></td>
                                        <td class="text-end"><?php echo $row["spent_$fyKey"] ?? '-'; ?></td>
                                    <?php } ?>
                                    <?php
                                    $grandTotalPo += (float)($row['total_po_amount'] ?? 0);
                                    $grandTotalSpent += (float)($row['total_spent_amount'] ?? 0);
                                    $grandTotalUnspent += (float)($row['unspent_amount'] ?? 0);

                                    $csvRow['Total PO Issued'] = csrPoActualsCsvValue($row['total_po_amount'] ?? '-');
                                    $csvRow['Total Spent Amt.'] = csrPoActualsCsvValue($row['total_spent_amount'] ?? '-');
                                    $csvRow['Spent %'] = csrPoActualsCsvValue(($row['spent_percent'] ?? '-') . '%');
                                    $csvRow['Total Unspent Amt.'] = csrPoActualsCsvValue($row['unspent_amount'] ?? '-');
                                    $csvRow['Unspent %'] = csrPoActualsCsvValue(($row['unspent_percent'] ?? '-') . '%');
                                    $csvdata[] = $csvRow;
                                    ?>
                                    <td class="text-end"><?php echo $row['total_po_amount'] ?? '-'; ?></td>
                                    <td class="text-end"><?php echo $row['total_spent_amount'] ?? '-'; ?></td>
                                    <td class="text-end"><?php echo $row['spent_percent'] ?? '-'; ?>%</td>
                                    <td class="text-end"><?php echo $row['unspent_amount'] ?? '-'; ?></td>
                                    <td class="text-end"><?php echo $row['unspent_percent'] ?? '-'; ?>%</td>
                                </tr>
                            <?php }
                            $grandSpentPercent = $grandTotalPo > 0 ? round(($grandTotalSpent / $grandTotalPo) * 100, 2) : '-';
                            $grandUnspentPercent = $grandTotalPo > 0 ? round(($grandTotalUnspent / $grandTotalPo) * 100, 2) : '-';

                            $grandCsvRow = [
                                'Name of the CSR Project' => 'Grand Total',
                                'Nature of Work Done' => '',
                                'Business Partner Name' => ''
                            ];
                            foreach ($normalizedFyList as $fy) {
                                $fyText = $fy['text'];
                                $fyKey = $fy['key'];
                                $grandCsvRow["FY $fyText PO No"] = '-';
                                $grandCsvRow["FY $fyText PO Issued (Incl. GST)"] = round($fyTotals[$fyKey]['po_value'], 2);
                                $grandCsvRow["FY $fyText Amt. Spent (Incl. GST)"] = round($fyTotals[$fyKey]['spent'], 2);
                            }
                            $grandCsvRow['Total PO Issued'] = round($grandTotalPo, 2);
                            $grandCsvRow['Total Spent Amt.'] = round($grandTotalSpent, 2);
                            $grandCsvRow['Spent %'] = $grandSpentPercent === '-' ? '-' : $grandSpentPercent . '%';
                            $grandCsvRow['Total Unspent Amt.'] = round($grandTotalUnspent, 2);
                            $grandCsvRow['Unspent %'] = $grandUnspentPercent === '-' ? '-' : $grandUnspentPercent . '%';
                            $csvdata[] = $grandCsvRow;

                            $_SESSION['download_data'] = $csvdata;
                            $_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
                            $_SESSION['csv_header']['title'] = 'CSR PO VS ACTUALS SUMMARY';
                            $_SESSION['csv_header']['date_time'] = date('d-m-Y H:i:s');
                            ?>
                            <tr class="fw-bold bg-light">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <?php foreach ($normalizedFyList as $fy) {
                                    $fyKey = $fy['key'];
                                ?>
                                    <td class="text-end">-</td>
                                    <td class="text-end"><?php echo round($fyTotals[$fyKey]['po_value'], 2); ?></td>
                                    <td class="text-end"><?php echo round($fyTotals[$fyKey]['spent'], 2); ?></td>
                                <?php } ?>
                                <td class="text-end"><?php echo round($grandTotalPo, 2); ?></td>
                                <td class="text-end"><?php echo round($grandTotalSpent, 2); ?></td>
                                <td class="text-end"><?php echo $grandSpentPercent === '-' ? '-' : $grandSpentPercent . '%'; ?></td>
                                <td class="text-end"><?php echo round($grandTotalUnspent, 2); ?></td>
                                <td class="text-end"><?php echo $grandUnspentPercent === '-' ? '-' : $grandUnspentPercent . '%'; ?></td>
                            </tr>
                        </tbody>


                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-4">
                    <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Data Available</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px max-h-300" src="assets/images/work.png" >
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
