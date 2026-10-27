<?php
$_SESSION['apply_js']['multiselect'] = 1;

function vendorAmcScheduleVisitColumnExportLabel($visitCol)
{
    $visitCol = is_array($visitCol) ? $visitCol : array();
    $monthLabel = trim((string) ($visitCol['month_label'] ?? ''));
    $slot = (int) ($visitCol['slot'] ?? 0);
    $dateKey = trim((string) ($visitCol['key'] ?? ''));
    if ($monthLabel == '') {
        return $dateKey;
    }
    if ($slot > 1) {
        return $monthLabel . ' ' . $slot;
    }
    return $monthLabel;
}

function vendorAmcScheduleBuildCsvRows($reportTitle, $visitColumns, $rows, $fiscalStartYear, $categoryName)
{
    $csvRows = array();
    $si = 1;
    if (count((array)$rows) > 0) {
        foreach ($rows as $assetRow) {
            $csvRow = array(
                'Report' => $reportTitle,
                'Category' => $categoryName,
                'Period' => vendorAmcTrackerPeriodLabel($fiscalStartYear),
                'S.No' => $si++,
                'Vendor' => $assetRow['vendor_name'] ?? '',
                'Asset (Location)' => $assetRow['asset_location'] ?? '',
                'Frequency' => $assetRow['frequency_label'] ?? '',
            );
            $visits = $assetRow['visits'] ?? array();
            if (count((array)$visitColumns) > 0) {
                foreach ($visitColumns as $visitCol) {
                    $visitKey = $visitCol['key'] ?? '';
                    $colLabel = vendorAmcScheduleVisitColumnExportLabel($visitCol);
                    $cell = $visits[$visitKey] ?? array('planned' => '', 'actual_start' => '');
                    $visitDisp = vendorAmcTrackerGetVisitCellDisplay($cell['planned'] ?? '', $cell['actual_start'] ?? '');
                    $csvRow[$colLabel . ' Plan'] = $visitDisp['planned_text'];
                    $csvRow[$colLabel . ' Actual'] = $visitDisp['actual_text'];
                }
            }
            $csvRows[] = $csvRow;
        }
    }
    return $csvRows;
}

$selectedYear = trim((string) ($_GET['year'] ?? ''));
$selectedVendorId = trim((string) ($_GET['vendor_id'] ?? ''));
$selectedCategoryId = trim((string) ($_GET['category_id'] ?? ''));

$hasSearched = (
    $selectedVendorId != '' && is_numeric($selectedVendorId) && (int) $selectedVendorId > 0
    && $selectedCategoryId != '' && is_numeric($selectedCategoryId) && (int) $selectedCategoryId > 0
    && $selectedYear != '' && is_numeric($selectedYear)
);

$filters = array('vendors' => array(), 'categories' => array(), 'years' => array());
$reportTitle = '';
$periodLabel = '';
$reportYear = '';
$categoryName = '';
$visitColumns = array();
$monthGroups = array();
$rows = array();

$queryParams = array(
    'year' => $selectedYear,
    'vendor_id' => $selectedVendorId,
    'category_id' => $selectedCategoryId,
);
$filterUrl = API . "?mode=getvendoramcschedulereportdata&method=get&" . http_build_query($queryParams);
$filterRes = json_decode(callAPI($filterUrl), true);
if (is_array($filterRes)) {
    $filters = $filterRes['filters'] ?? $filters;
    if ($hasSearched) {
        $reportTitle = $filterRes['report_title'] ?? '';
        $periodLabel = $filterRes['period_label'] ?? vendorAmcTrackerPeriodLabel((int) $selectedYear);
        $reportYear = $filterRes['year'] ?? $selectedYear;
        $categoryName = $filterRes['category_name'] ?? '';
        $visitColumns = $filterRes['visit_columns'] ?? array();
        $monthGroups = $filterRes['month_groups'] ?? array();
        $rows = $filterRes['rows'] ?? array();
    }
}

$yearOptions = array();
if (count((array)($filters['years'] ?? array())) > 0) {
    $yearOptions = $filters['years'];
} else {
    $curYear = (int) date('Y');
    for ($y = 2019; $y <= ($curYear + 1); $y++) {
        $yearOptions[] = $y;
    }
    rsort($yearOptions);
}

$reportColspan = 4 + (count((array)$visitColumns) * 2);
$hasExportData = ($hasSearched && count((array)$rows) > 0);

$filterSummaryParts = array();
if ($selectedVendorId != '') {
    $vendorName = '';
    if (count((array)($filters['vendors'] ?? array())) > 0) {
        foreach ($filters['vendors'] as $vendor) {
            if ((string) ($vendor['id'] ?? '') == $selectedVendorId) {
                $vendorName = $vendor['name'] ?? $selectedVendorId;
                break;
            }
        }
    }
    if ($vendorName != '') {
        $filterSummaryParts[] = 'Vendor: ' . $vendorName;
    }
}
if ($selectedCategoryId != '') {
    $catName = '';
    if (count((array)($filters['categories'] ?? array())) > 0) {
        foreach ($filters['categories'] as $cat) {
            if ((string) ($cat['id'] ?? '') == $selectedCategoryId) {
                $catName = $cat['name'] ?? $selectedCategoryId;
                break;
            }
        }
    }
    if ($catName != '') {
        $filterSummaryParts[] = 'Category: ' . $catName;
    }
}
if ($periodLabel != '') {
    $filterSummaryParts[] = 'Period: ' . $periodLabel;
} elseif ($selectedYear != '') {
    $filterSummaryParts[] = 'Period: ' . vendorAmcTrackerPeriodLabel((int) $selectedYear);
}

if ($hasExportData) {
    $_SESSION['download_data'] = vendorAmcScheduleBuildCsvRows(
        $reportTitle,
        $visitColumns,
        $rows,
        (int) $reportYear,
        $categoryName
    );
    $_SESSION['csv_header'] = array(
        'company_name' => $_SESSION['config']['name'] ?? '',
        'title' => $reportTitle != '' ? $reportTitle : ($categoryName . ' AMC Schedule'),
        'name' => count((array)$filterSummaryParts) > 0 ? implode(' | ', $filterSummaryParts) : '',
        'date_time' => date('d-m-Y h:i A'),
    );
} else {
    $_SESSION['download_data'] = array();
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Vendor AMC Schedule</h5>
            <?php if ($hasExportData) { ?>
            <a href="custom_view_download.php?page=VendorAmcSchedule&format=csv" target="_blank" class="btn btn-primary waves-effect waves-light">CSV</a>
            <?php } ?>
        </div>
        <div class="card-body">
            <form method="get" id="vendorAmcScheduleForm" class="row g-3 mb-3">
                <input type="hidden" name="option" value="COM_VIEW_VENDOR_AMC_SCHEDULE_REPORT">
                <div class="col-lg-3">
                    <span>Vendor <span class="text-danger">*</span></span>
                    <select aria-label="Vendor" class="form-select js-example-basic-single select2" style="width:100%" name="vendor_id" id="vas_vendor_id">
                        <option value="">Select Vendor</option>
                        <?php if (count((array)($filters['vendors'] ?? array())) > 0) {
                            foreach ($filters['vendors'] as $vendor) { ?>
                        <option value="<?php echo (int) ($vendor['id'] ?? 0); ?>" <?php echo ($selectedVendorId == (string) ($vendor['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($vendor['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                    <div id="vas_vendor_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-3">
                    <span>Asset Category <span class="text-danger">*</span></span>
                    <select aria-label="Category" class="form-select js-example-basic-single select2" style="width:100%" name="category_id" id="vas_category_id">
                        <option value="">Select Category</option>
                        <?php if (count((array)($filters['categories'] ?? array())) > 0) {
                            foreach ($filters['categories'] as $cat) { ?>
                        <option value="<?php echo (int) ($cat['id'] ?? 0); ?>" <?php echo ($selectedCategoryId == (string) ($cat['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                    <div id="vas_category_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-2">
                    <span>Year (Apr–Mar) <span class="text-danger">*</span></span>
                    <select aria-label="Year" class="form-select js-example-basic-single select2" style="width:100%" name="year" id="vas_year">
                        <option value="">Select Year</option>
                        <?php if (count((array)$yearOptions) > 0) {
                            foreach ($yearOptions as $yr) { ?>
                        <option value="<?php echo (int) $yr; ?>" <?php echo ($selectedYear == (string) $yr) ? 'selected' : ''; ?>><?php echo (int) $yr; ?> – <?php echo (int) $yr + 1; ?></option>
                        <?php }
                        } ?>
                    </select>
                    <div id="vas_year_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icofont icofont-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$hasSearched) { ?>
    <div class="card">
        <div class="card-body text-center text-muted py-4">
            <p class="f-16 fw-bold m-b-10"><i class="icofont icofont-search"></i> Select vendor, asset category, and year, then click Search.</p>
        </div>
    </div>
    <?php } elseif (count((array)$rows) == 0) { ?>
    <div class="card">
        <div class="card-body text-center text-danger">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No records found for the selected filters.</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0 text-primary"><i class="icofont icofont-chart-histogram"></i> <?php echo htmlspecialchars($reportTitle != '' ? $reportTitle : ($categoryName . ' AMC Schedule'), ENT_QUOTES, 'UTF-8'); ?></h6>
        </div>
        <div class="card-body">
            <?php if (count((array)$filterSummaryParts) > 0) { ?>
            <p class="text-muted small mb-3"><?php echo htmlspecialchars(implode(' | ', $filterSummaryParts), ENT_QUOTES, 'UTF-8'); ?></p>
            <?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th scope="col" rowspan="2" class="align-middle">S.No</th>
                            <th scope="col" rowspan="2" class="align-middle">Vendor</th>
                            <th scope="col" rowspan="2" class="align-middle">Asset (Location)</th>
                            <th scope="col" rowspan="2" class="align-middle">Frequency</th>
                            <?php if (count((array)$monthGroups) > 0) {
                                foreach ($monthGroups as $monthGroup) { ?>
                            <th scope="col" colspan="<?php echo (int) ($monthGroup['colspan'] ?? 2); ?>" class="text-center"><?php echo htmlspecialchars($monthGroup['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></th>
                            <?php }
                            } ?>
                        </tr>
                        <tr>
                            <?php if (count((array)$visitColumns) > 0) {
                                foreach ($visitColumns as $visitCol) { ?>
                            <th scope="col" class="text-center">Plan</th>
                            <th scope="col" class="text-center">Actual</th>
                            <?php }
                            } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $si = 1;
                        if (count((array)$rows) > 0) {
                            foreach ($rows as $assetRow) {
                                $visits = $assetRow['visits'] ?? array();
                        ?>
                        <tr>
                            <td><?php echo (int) $si++; ?></td>
                            <td><?php echo htmlspecialchars($assetRow['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($assetRow['asset_location'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($assetRow['frequency_label'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php if (count((array)$visitColumns) > 0) {
                                foreach ($visitColumns as $visitCol) {
                                    $visitKey = $visitCol['key'] ?? '';
                                    $cell = $visits[$visitKey] ?? array('planned' => '', 'actual_start' => '');
                                    $visitDisp = vendorAmcTrackerGetVisitCellDisplay($cell['planned'] ?? '', $cell['actual_start'] ?? '');
                            ?>
                            <td class="text-nowrap <?php echo htmlspecialchars($visitDisp['planned_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($visitDisp['planned_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap <?php echo htmlspecialchars($visitDisp['actual_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($visitDisp['actual_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php }
                            } ?>
                        </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
$(function() {
    if ($.fn.select2) {
        $("#vas_vendor_id, #vas_category_id, #vas_year").select2({ width: "100%" });
    }
});

$("#vendorAmcScheduleForm").submit(function(e) {
    var hasError = false;
    $("#vas_vendor_err, #vas_category_err, #vas_year_err").addClass("d-none");
    if (!$("#vas_vendor_id").val()) {
        $("#vas_vendor_err").text("Please select Vendor").removeClass("d-none");
        hasError = true;
    }
    if (!$("#vas_category_id").val()) {
        $("#vas_category_err").text("Please select Asset Category").removeClass("d-none");
        hasError = true;
    }
    if (!$("#vas_year").val()) {
        $("#vas_year_err").text("Please select Year").removeClass("d-none");
        hasError = true;
    }
    if (hasError) {
        e.preventDefault();
    }
});
</script>
