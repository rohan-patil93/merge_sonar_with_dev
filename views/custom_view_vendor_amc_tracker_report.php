<?php
$_SESSION['apply_js']['multiselect'] = 1;

function vendorAmcTrackerFiscalMonthLabels()
{
    return array(
        '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December',
        '01' => 'January', '02' => 'February', '03' => 'March',
    );
}

function vendorAmcTrackerBuildCsvRows($categories, $fiscalStartYear)
{
    $rows = array();
    $monthOrder = vendorAmcTrackerFiscalMonthOrder();
    $monthLabels = vendorAmcTrackerFiscalMonthLabels();
    if (count((array)$categories) > 0) {
        foreach ($categories as $cat) {
            $catName = $cat['category_name'] ?? '';
            if (count((array)($cat['blocks'] ?? array())) > 0) {
                foreach ($cat['blocks'] as $block) {
                    $si = 1;
                    if (count((array)($block['rows'] ?? array())) > 0) {
                        foreach ($block['rows'] as $assetRow) {
                            $csvRow = array(
                                'Category' => $catName,
                                'Vendor' => $block['vendor_name'] ?? '',
                                'Contract' => $block['contract_number'] ?? '',
                                'Period' => $block['period_label'] ?? vendorAmcTrackerPeriodLabel($fiscalStartYear),
                                'S.No' => $si++,
                                'Equipment Name' => $assetRow['equipment_name'] ?? '',
                                'Frequency' => $assetRow['frequency_label'] ?? '',
                            );
                            $months = $assetRow['months'] ?? array();
                            if (count((array)$monthOrder) > 0) {
                                foreach ($monthOrder as $mk) {
                                    $ml = $monthLabels[$mk] ?? $mk;
                                    $cell = $months[$mk] ?? array('planned' => '', 'actual_start' => '');
                                    $visitDisp = vendorAmcTrackerGetVisitCellDisplay($cell['planned'] ?? '', $cell['actual_start'] ?? '');
                                    $csvRow[$ml . ' Plan'] = $visitDisp['planned_text'];
                                    $csvRow[$ml . ' Actual'] = $visitDisp['actual_text'];
                                }
                            }
                            $rows[] = $csvRow;
                        }
                    }
                }
            }
        }
    }
    return $rows;
}

$selectedYear = trim((string) ($_GET['year'] ?? ''));
$selectedVendorId = trim((string) ($_GET['vendor_id'] ?? ''));
$selectedCategoryId = trim((string) ($_GET['category_id'] ?? ''));
$hasSearched = ($selectedVendorId != '' && is_numeric($selectedVendorId) && (int) $selectedVendorId > 0);

$filters = array('vendors' => array(), 'categories' => array(), 'years' => array());
$categories = array();
$reportYear = '';
$periodLabel = '';

$queryParams = array(
    'year' => $selectedYear,
    'vendor_id' => $selectedVendorId,
    'category_id' => $selectedCategoryId,
);
$filterUrl = API . "?mode=getvendoramctrackerreportdata&method=get&" . http_build_query($queryParams);
$filterRes = json_decode(callAPI($filterUrl), true);
if (is_array($filterRes)) {
    $filters = $filterRes['filters'] ?? $filters;
    if ($hasSearched) {
        $categories = $filterRes['categories'] ?? array();
        $reportYear = $filterRes['year'] ?? $selectedYear;
        $periodLabel = $filterRes['period_label'] ?? vendorAmcTrackerPeriodLabel((int) $selectedYear);
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

$monthOrder = vendorAmcTrackerFiscalMonthOrder();
$monthLabels = vendorAmcTrackerFiscalMonthLabels();
$reportColspan = 3 + (count((array)$monthOrder) * 2);

$hasExportData = ($hasSearched && count((array)$categories) > 0);
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
    $catName = 'All';
    if (count((array)($filters['categories'] ?? array())) > 0) {
        foreach ($filters['categories'] as $cat) {
            if ((string) ($cat['id'] ?? '') == $selectedCategoryId) {
                $catName = $cat['name'] ?? $selectedCategoryId;
                break;
            }
        }
    }
    $filterSummaryParts[] = 'Category: ' . $catName;
}
if ($periodLabel != '') {
    $filterSummaryParts[] = 'Period: ' . $periodLabel;
} elseif ($selectedYear != '') {
    $filterSummaryParts[] = 'Period: ' . vendorAmcTrackerPeriodLabel((int) $selectedYear);
}

if ($hasExportData) {
    $_SESSION['download_data'] = vendorAmcTrackerBuildCsvRows($categories, (int) $reportYear);
    $_SESSION['csv_header'] = array(
        'company_name' => $_SESSION['config']['name'] ?? '',
        'title' => 'Vendor AMC Tracker' . ($periodLabel != '' ? ' ' . $periodLabel : ''),
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
            <h5 class="mb-0">Vendor AMC Tracker</h5>
            <?php if ($hasExportData) { ?>
            <a href="custom_view_download.php?page=VendorAmcTracker&format=csv" target="_blank" class="btn btn-primary waves-effect waves-light">CSV</a>
            <?php } ?>
        </div>
        <div class="card-body">
            <form method="get" id="vendorAmcTrackerForm" class="row g-3 mb-3">
                <input type="hidden" name="option" value="COM_VIEW_VENDOR_AMC_TRACKER_REPORT">
                <div class="col-lg-3">
                    <span>Vendor <span class="text-danger">*</span></span>
                    <select aria-label="Vendor" class="form-select js-example-basic-single select2" style="width:100%" name="vendor_id" id="vat_vendor_id">
                        <option value="">Select Vendor</option>
                        <?php if (count((array)($filters['vendors'] ?? array())) > 0) {
                            foreach ($filters['vendors'] as $vendor) { ?>
                        <option value="<?php echo (int) ($vendor['id'] ?? 0); ?>" <?php echo ($selectedVendorId == (string) ($vendor['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($vendor['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                    <div id="vat_vendor_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-3">
                    <span>Asset Category</span>
                    <select aria-label="Category" class="form-select js-example-basic-single select2" style="width:100%" name="category_id" id="vat_category_id">
                        <option value="">All</option>
                        <?php if (count((array)($filters['categories'] ?? array())) > 0) {
                            foreach ($filters['categories'] as $cat) { ?>
                        <option value="<?php echo (int) ($cat['id'] ?? 0); ?>" <?php echo ($selectedCategoryId == (string) ($cat['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span>Year (Apr–Mar)</span>
                    <select aria-label="Year" class="form-select js-example-basic-single select2" style="width:100%" name="year" id="vat_year">
                        <option value="">All</option>
                        <?php if (count((array)$yearOptions) > 0) {
                            foreach ($yearOptions as $yr) { ?>
                        <option value="<?php echo (int) $yr; ?>" <?php echo ($selectedYear == (string) $yr) ? 'selected' : ''; ?>><?php echo (int) $yr; ?> – <?php echo (int) $yr + 1; ?></option>
                        <?php }
                        } ?>
                    </select>
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
            <p class="f-16 fw-bold m-b-10"><i class="icofont icofont-search"></i> Select vendor and click Search to load the report.</p>
        </div>
    </div>
    <?php } elseif (count((array)$categories) == 0) { ?>
    <div class="card">
        <div class="card-body text-center text-danger">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No records found for the selected filters.</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    </div>
    <?php } else {
        if (count((array)$categories) > 0) {
            foreach ($categories as $catIndex => $cat) {
                $catName = $cat['category_name'] ?? 'Category';
                $catId = 'vatCat' . (int) $catIndex;
    ?>
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0 text-primary"><i class="icofont icofont-chart-histogram"></i> <?php echo htmlspecialchars($catName, ENT_QUOTES, 'UTF-8'); ?></h6>
        </div>
        <div class="card-body">
            <?php if (count((array)($cat['blocks'] ?? array())) > 0) {
                foreach ($cat['blocks'] as $block) {
                    $blockTitle = 'AMC TRACKER : ' . ($block['period_label'] ?? $periodLabel);
                    $vendorName = trim((string) ($block['vendor_name'] ?? ''));
                    $contractNo = trim((string) ($block['contract_number'] ?? ''));
            ?>
            <div class="mb-4">
                <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($blockTitle, ENT_QUOTES, 'UTF-8'); ?></h6>
                <?php if ($vendorName != '' || $contractNo != '') { ?>
                <p class="text-muted small mb-2">
                    <?php if ($vendorName != '') { ?>Vendor: <?php echo htmlspecialchars($vendorName, ENT_QUOTES, 'UTF-8'); ?><?php
} ?>
                    <?php if ($vendorName != '' && $contractNo != '') { ?> | <?php
} ?>
                    <?php if ($contractNo != '') { ?>Contract: <?php echo htmlspecialchars($contractNo, ENT_QUOTES, 'UTF-8'); ?><?php
} ?>
                </p>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th scope="col" rowspan="2" class="align-middle">SI NO</th>
                                <th scope="col" rowspan="2" class="align-middle">Equipment Name</th>
                                <th scope="col" rowspan="2" class="align-middle">Frequency</th>
                                <?php if (count((array)$monthOrder) > 0) {
                                    foreach ($monthOrder as $mk) { ?>
                                <th scope="col" colspan="2" class="text-center"><?php echo htmlspecialchars($monthLabels[$mk] ?? $mk, ENT_QUOTES, 'UTF-8'); ?></th>
                                <?php }
                                } ?>
                            </tr>
                            <tr>
                                <?php if (count((array)$monthOrder) > 0) {
                                    foreach ($monthOrder as $mk) { ?>
                                <th scope="col" class="text-center">Plan</th>
                                <th scope="col" class="text-center">Actual</th>
                                <?php }
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $si = 1;
                            if (count((array)($block['rows'] ?? array())) > 0) {
                                foreach ($block['rows'] as $assetRow) {
                                    $months = $assetRow['months'] ?? array();
                            ?>
                            <tr>
                                <td><?php echo (int) $si++; ?></td>
                                <td><?php echo htmlspecialchars($assetRow['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($assetRow['frequency_label'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if (count((array)$monthOrder) > 0) {
                                    foreach ($monthOrder as $mk) {
                                        $cell = $months[$mk] ?? array('planned' => '', 'actual_start' => '');
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
            <?php }
            } ?>
        </div>
    </div>
    <?php }
        }
    } ?>
</div>

<script>
$(function() {
    if ($.fn.select2) {
        $("#vat_vendor_id").select2({ width: "100%" });
        $("#vat_category_id, #vat_year").select2({ width: "100%", allowClear: true });
    }
});

$("#vendorAmcTrackerForm").submit(function(e) {
    var vendorId = $("#vat_vendor_id").val();
    $("#vat_vendor_err").addClass("d-none");
    if (!vendorId) {
        $("#vat_vendor_err").text("Please select Vendor").removeClass("d-none");
        e.preventDefault();
    }
});
</script>
