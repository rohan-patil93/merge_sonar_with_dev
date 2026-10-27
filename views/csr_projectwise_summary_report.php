<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$_SESSION['apply_js']['multiselest'] = 1;

$todayYear = date("Y");
$todayMonth = date("n");
if ($todayMonth >= 4) {
    $startYear = $todayYear;
    $endYear = $todayYear + 1;
} else {
    $startYear = $todayYear - 1;
    $endYear = $todayYear;
}
$currentFY = $startYear . '-' . substr($endYear, -2);
$selectedYear = $_GET['year'] ?? '';

// ✅ API Call
$url = API . "?mode=getCsrProjectSummaryReport&year=" . urlencode($selectedYear);
$apires = callAPI($url);
$result = json_decode($apires, true);

$list = $result['project_summary_list'] ?? [];
$financial_years = $result['financial_years'] ?? [];
$selectedYear = $result['selected_year'] ?? ($selectedYear != '' ? $selectedYear : $currentFY);
$_SESSION['download_data'] = [];

if (!function_exists('csrProjectSummaryCsvValue')) {
    function csrProjectSummaryCsvValue($value) {
        if ($value === null || $value === '') {
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
}

// ✅ Grand Totals
$grand_total_po       = 0;
$grand_total_spent    = 0;
$grand_total_unspent  = 0;
?>
<link rel="stylesheet" href="assets/css/csr_projectwise_summary_report.css">
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>CSR PROJECTWISE SUMMARY</h5>
            <div class="card-header-right">
                <div class="dropdown-primary dropdown open">
                    <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                    <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=CSRProjectSummary&format=csv" target="_blank">CSV</a>
                        <a class="dropdown-item waves-light waves-effect"
                            href="custom_view_download.php?page=CSRProjectSummary&format=excel" target="_blank">Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            <form method="GET" id="csrProjectSummaryYearForm">
                <input type="hidden" name="option" value="<?php echo htmlspecialchars($_GET['option'] ?? 'CSR_PROJECTWISE_SUMMARY_REPORT', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label><b>Financial Year</b></label>
                        <select name="year" id="csrProjectSummaryYearSelect" class="form-control js-example-basic-single">
                            <?php if (count((array)$financial_years) > 0) {
                                foreach ($financial_years as $fyRow) {
                                    $fyCode = is_array($fyRow) ? ($fyRow['genc_detail_code'] ?? '') : ($fyRow->genc_detail_code ?? '');
                                    $fyName = is_array($fyRow) ? ($fyRow['genc_detail_name'] ?? $fyCode) : ($fyRow->genc_detail_name ?? $fyCode);
                                    $selected = ($selectedYear == $fyCode) ? 'selected' : '';
                            ?>
                                <option value="<?php echo htmlspecialchars($fyCode, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($fyName, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (count((array)$list) != 0) { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle ">
                        <thead>
                            <tr>
                                <th colspan="3" class="bg-primary text-white text-center">CSR Actual Spent Details</th>
                                <th colspan="3">-</th>
                                <th colspan="5" class="bg-primary text-white text-center">Amount Summary</th>
                                <th></th>
                            </tr>
                            <tr class="bg-light">
                                <th class="text-center">Location</th>
                                <th class="text-center">Name of the CSR Project</th>
                                <th class="text-center">Nature of work done</th>
                                <th class="text-center">Type of work</th>
                                <th class="text-center">Type of Project</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">PO Issued<br><span class="f-12">(Incl. GST)</span></th>
                                <th class="text-center">Amount Spent<br><span class="f-12">(Incl. GST)</span></th>
                                <th class="text-center">Unspent Amount</th>
                                <th class="text-center">Spent %</th>
                                <th class="text-center">Unspent %</th>
                                <th class="text-center">Status of Project</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $csvdata = [];
                            foreach ($list as $row) {
                                $grand_total_po      += $row['total_po_excl_gst'];
                                $grand_total_spent   += $row['total_spent_excl_gst'];
                                $grand_total_unspent += $row['unspent_amount'];
                            ?>
                                <tr>
                                    <td class=""><?php echo $row['location'] ?? '-'; ?></td>
                                    <th class=""><?php echo $row['project_name'] ?? '-'; ?></th>
                                    <td class=""><?php echo $row['nature_of_work_done'] ?? '-'; ?></td>
                                    <td class=""><?php echo $row['type_of_work'] ?? '-'; ?></td>
                                    <td class=""><?php echo $row['type_of_project'] ?? '-'; ?></td>
                                    <td class=""><?php echo $row['area'] ?? '-'; ?></td>
                                    <td class="text-end "><?php echo customeummarmonyformate($row['total_po_excl_gst']) ?? '-'; ?></td>
                                    <td class="text-end "><?php echo customeummarmonyformate($row['total_spent_excl_gst']) ?? '-'; ?></td>
                                    <td class="text-end "><?php echo customeummarmonyformate($row['unspent_amount']) ?? '-'; ?></td>
                                    <td class="text-end "><?php echo $row['spent_percent'] ?? '-'; ?>%</td>
                                    <td class="text-end "><?php echo $row['unspent_percent'] ?? '-'; ?>%</td>
                                    <td class="text-center "><?php echo $row['project_status'] ?? '-'; ?></td>
                                </tr>
                            <?php
                                $csvdata[] = [
                                    'Location' => csrProjectSummaryCsvValue($row['location'] ?? '-'),
                                    'Name of the CSR Project' => csrProjectSummaryCsvValue($row['project_name'] ?? '-'),
                                    'Nature of work done' => csrProjectSummaryCsvValue($row['nature_of_work_done'] ?? '-'),
                                    'Type of work' => csrProjectSummaryCsvValue($row['type_of_work'] ?? '-'),
                                    'Type of Project' => csrProjectSummaryCsvValue($row['type_of_project'] ?? '-'),
                                    'Area' => csrProjectSummaryCsvValue($row['area'] ?? '-'),
                                    'PO Issued (Excl. GST)' => (float)($row['total_po_excl_gst'] ?? 0),
                                    'Amount Spent (Excl. GST)' => (float)($row['total_spent_excl_gst'] ?? 0),
                                    'Unspent Amount' => (float)($row['unspent_amount'] ?? 0),
                                    'Spent %' => csrProjectSummaryCsvValue(($row['spent_percent'] ?? 0) . '%'),
                                    'Unspent %' => csrProjectSummaryCsvValue(($row['unspent_percent'] ?? 0) . '%'),
                                    'Status of Project' => csrProjectSummaryCsvValue($row['project_status'] ?? '-')
                                ];
                            }
                            $_SESSION['download_data'] = $csvdata;
                            ?>
                            <!-- Grand Total Row -->
                            <tr class="fw-bold bg-light" >
                                <td colspan="6" class="text-end">Grand Total</td>
                                <td class="text-end"><?php echo customeummarmonyformate($grand_total_po); ?></td>
                                <td class="text-end"><?php echo customeummarmonyformate($grand_total_spent); ?></td>
                                <td class="text-end"><?php echo customeummarmonyformate($grand_total_unspent); ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center py-4">
                    <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Data Available</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px max-h-300" alt="" src="assets/images/work.png" >
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<script src="assets/js/csr_projectwise_summary_report.js"></script>