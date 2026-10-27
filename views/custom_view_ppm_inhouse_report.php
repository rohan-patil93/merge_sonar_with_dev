<?php
$_SESSION['apply_js']['multiselect'] = 1;

$selectedYear = trim((string) ($_GET['year'] ?? ''));
$selectedCategoryId = trim((string) ($_GET['category_id'] ?? ''));
$selectedLocationId = trim((string) ($_GET['location_id'] ?? ''));
$hasSearched = ($selectedYear != '' && is_numeric($selectedYear));

$filters = array('categories' => array(), 'locations' => array(), 'years' => array());
$rows = array();
$reportYear = '';

$queryParams = array(
    'year' => $selectedYear,
    'category_id' => $selectedCategoryId,
    'location_id' => $selectedLocationId,
);
$filterUrl = API . "?mode=getppminhousereportdata&method=get&" . http_build_query($queryParams);
$filterRes = json_decode(callAPI($filterUrl), true);
if (is_array($filterRes)) {
    $filters = $filterRes['filters'] ?? $filters;
    if ($hasSearched) {
        $rows = $filterRes['rows'] ?? array();
        $reportYear = $filterRes['year'] ?? $selectedYear;
    }
}

$yearOptions = array();
$curYear = (int) date('Y');
for ($y = 2019; $y <= ($curYear + 1); $y++) {
    $yearOptions[] = $y;
}
rsort($yearOptions);

$monthLabels = array(
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec',
);

function ppmInhouseFormatDate($date)
{
    if (empty($date)) {
        return '-';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '-';
    }
    if (date('H:i', $ts) != '00:00') {
        return date('d-m-Y h:i A', $ts);
    }
    return date('d-m-Y', $ts);
}

function ppmInhouseGetVisitCellDisplay($planned, $actualStart)
{
    $result = array(
        'planned_text' => '-',
        'planned_class' => '',
        'actual_text' => '-',
        'actual_class' => '',
    );
    $planned = trim((string) $planned);
    $actualStart = trim((string) $actualStart);
    if ($planned == '') {
        return $result;
    }
    $plannedTs = strtotime($planned);
    if ($plannedTs === false) {
        return $result;
    }

    // Planned column: always show the planned date.
    $result['planned_text'] = ppmInhouseFormatDate($planned);

    $today = date('Y-m-d');
    $plannedDate = date('Y-m-d', $plannedTs);
    $hasActual = ($actualStart != '');
    $actualTs = $hasActual ? strtotime($actualStart) : false;
    $isFuture = ($plannedDate > $today);
    $isOverdue = ($plannedDate < $today) && !$hasActual;

    if ($hasActual && $actualTs !== false) {
        $result['actual_text'] = ppmInhouseFormatDate($actualStart);
        if ($actualTs > $plannedTs) {
            $result['actual_class'] = 'text-danger fw-bold';
        }
    } elseif ($isOverdue) {
        $result['actual_text'] = 'DELAYED';
        $result['actual_class'] = 'text-danger fw-bold';
    } elseif ($isFuture) {
        $result['actual_text'] = '-';
    } else {
        $result['actual_text'] = '-';
    }

    return $result;
}

function ppmInhouseFrequencyLabel($freq)
{
    $freq = strtolower(trim((string) $freq));
    $labels = array(
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'halfyearly' => 'Half Yearly',
        'half_yearly' => 'Half Yearly',
        'yearly' => 'Yearly',
        'custom' => 'Custom',
        'other' => 'Other',
    );
    if (isset($labels[$freq])) {
        return $labels[$freq];
    }
    return $freq != '' ? ucfirst($freq) : '—';
}

function ppmInhouseNormalizeFrequencySlug($freq)
{
    $freq = strtolower(trim((string) $freq));
    if ($freq == 'half_yearly') {
        return 'halfyearly';
    }
    return $freq != '' ? $freq : 'other';
}

function ppmInhouseFrequencyDisplayOrder()
{
    return array('yearly', 'halfyearly', 'quarterly', 'monthly', 'weekly', 'daily', 'custom');
}

function ppmInhouseGroupRowsByFrequency($rows)
{
    $groups = array();
    if (count((array)$rows) > 0) {
        foreach ($rows as $row) {
            if (is_object($row)) {
                $row = json_decode(json_encode($row), true);
            }
            $slug = ppmInhouseNormalizeFrequencySlug($row['frequency'] ?? '');
            if (!isset($groups[$slug])) {
                $groups[$slug] = array();
            }
            $groups[$slug][] = $row;
        }
    }
    $ordered = array();
    if (count((array)ppmInhouseFrequencyDisplayOrder()) > 0) {
        foreach (ppmInhouseFrequencyDisplayOrder() as $slug) {
            if (count((array)($groups[$slug] ?? array())) > 0) {
                $ordered[$slug] = $groups[$slug];
                unset($groups[$slug]);
            }
        }
    }
    if (count((array)$groups) > 0) {
        foreach ($groups as $slug => $items) {
            if (count((array)$items) > 0) {
                $ordered[$slug] = $items;
            }
        }
    }
    return $ordered;
}

$ppmReportColspan = 5 + (count((array)$monthLabels) * 2);
$rowsByFrequency = $hasSearched ? ppmInhouseGroupRowsByFrequency($rows) : array();

$_SESSION['download_data'] = array();
$csv_data = array();
$filterSummaryParts = array();
if ($reportYear != '') {
    $filterSummaryParts[] = 'Year: ' . $reportYear;
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
if ($selectedLocationId != '') {
    $locName = 'All';
    if (count((array)($filters['locations'] ?? array())) > 0) {
        foreach ($filters['locations'] as $loc) {
            if ((string) ($loc['id'] ?? '') == $selectedLocationId) {
                $locName = $loc['name'] ?? $selectedLocationId;
                break;
            }
        }
    }
    $filterSummaryParts[] = 'Location: ' . $locName;
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">In-house PPM Report</h5>
            <?php if ($hasSearched && count((array)$rows) > 0) { ?>
            <a href="custom_view_download.php?page=PpmInhouseReport&format=csv" target="_blank" class="btn btn-primary waves-effect waves-light">CSV</a>
            <?php } ?>
        </div>
        <div class="card-body">
            <form method="get" id="ppmInhouseReportForm" class="row g-3 mb-3">
                <input type="hidden" name="option" value="COM_VIEW_PPM_INHOUSE_REPORT">
                <div class="col-lg-2">
                    <span>Year <span class="text-danger">*</span></span>
                    <select aria-label="Year" class="form-select js-example-basic-single select2" style="width:100%" name="year" id="ppm_year">
                        <option value="">Select Year</option>
                        <?php if (count((array)$yearOptions) > 0) {
                            foreach ($yearOptions as $yr) { ?>
                        <option value="<?php echo (int) $yr; ?>" <?php echo ($selectedYear == (string) $yr) ? 'selected' : ''; ?>><?php echo (int) $yr; ?></option>
                        <?php }
                        } ?>
                    </select>
                    <div id="ppm_year_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-3">
                    <span>Asset Category</span>
                    <select aria-label="Category" class="form-select js-example-basic-single select2" style="width:100%" name="category_id" id="ppm_category_id">
                        <option value="">All</option>
                        <?php if (count((array)($filters['categories'] ?? array())) > 0) {
                            foreach ($filters['categories'] as $cat) { ?>
                        <option value="<?php echo (int) ($cat['id'] ?? 0); ?>" <?php echo ($selectedCategoryId == (string) ($cat['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <span>Location</span>
                    <select aria-label="Location" class="form-select js-example-basic-single select2" style="width:100%" name="location_id" id="ppm_location_id">
                        <option value="">All</option>
                        <?php if (count((array)($filters['locations'] ?? array())) > 0) {
                            foreach ($filters['locations'] as $loc) { ?>
                        <option value="<?php echo (int) ($loc['id'] ?? 0); ?>" <?php echo ($selectedLocationId == (string) ($loc['id'] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($loc['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th scope="col" rowspan="2" class="align-middle">S.No</th>

                            <th scope="col" rowspan="2" class="align-middle">Asset Number</th>
                            <th scope="col" rowspan="2" class="align-middle">Equipment Name</th>
                            <th scope="col" rowspan="2" class="align-middle">Category</th>
                            <th scope="col" rowspan="2" class="align-middle">Location</th>
                            <?php if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $ml) { ?>
                            <th scope="col" colspan="2" class="text-center"><?php echo $ml; ?></th>
                            <?php }
                            } ?>
                        </tr>
                        <tr>
                            <?php if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $ml) { ?>
                            <th scope="col" class="text-center"><?php echo $ml; ?> Planned</th>
                            <th scope="col" class="text-center"><?php echo $ml; ?> Actual</th>
                            <?php }
                            } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$hasSearched) { ?>
                        <tr>
                            <td colspan="<?php echo (int) $ppmReportColspan; ?>" class="text-center text-muted py-4">
                                <p class="f-16 fw-bold m-b-10"><i class="icofont icofont-search"></i> Select year and click Search to load the report.</p>
                            </td>
                        </tr>
                        <?php } elseif (count((array)$rowsByFrequency) > 0) {
                            $i = 1;
                            $monthTotals = array();
                            if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $mk => $ml) {
                                    $monthTotals[$mk] = array('planned' => 0, 'actual_done' => 0);
                                }
                            }
                            foreach ($rowsByFrequency as $freqSlug => $freqRows) {
                                $freqLabel = ppmInhouseFrequencyLabel($freqSlug);
                                $freqCount = count((array)$freqRows);
                                $sectionTotals = array();
                                if (count((array)$monthLabels) > 0) {
                                    foreach ($monthLabels as $mk => $ml) {
                                        $sectionTotals[$mk] = array('planned' => 0, 'actual_done' => 0);
                                    }
                                }
                        ?>
                        <tr class="table-secondary">
                            <td colspan="<?php echo (int) $ppmReportColspan; ?>" class="fw-bold text-primary py-2">
                                <i class="icofont icofont-chart-histogram"></i>
                                <?php echo htmlspecialchars($freqLabel, ENT_QUOTES, 'UTF-8'); ?>
                                <span class="text-muted fw-normal">(<?php echo (int) $freqCount; ?> asset<?php echo $freqCount == 1 ? '' : 's'; ?>)</span>
                            </td>
                        </tr>
                        <?php
                                if (count((array)$freqRows) > 0) {
                                    foreach ($freqRows as $row) {
                                        if (is_object($row)) {
                                            $row = json_decode(json_encode($row), true);
                                        }
                                        $months = $row['months'] ?? array();
                                        $csvRow = array(
                                            'S.No' => $i,
                                            'Frequency Group' => $freqLabel,
                                            'Asset Number' => $row['asset_code'] ?? '',
                                            'Equipment Name' => $row['equipment_name'] ?? '',
                                            'Category' => $row['category_name'] ?? '',
                                            'Location' => $row['location_name'] ?? '',
                                            'Frequency' => ppmInhouseFrequencyLabel($row['frequency'] ?? ''),
                                        );
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>

                            <td><?php echo htmlspecialchars($row['asset_code'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['location_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $mk => $ml) {
                                    $cell = $months[$mk] ?? array('planned' => '', 'actual_start' => '');
                                    $plannedRaw = trim((string) ($cell['planned'] ?? ''));
                                    $actualRaw = trim((string) ($cell['actual_start'] ?? ''));
                                    if ($plannedRaw != '') {
                                        $monthTotals[$mk]['planned']++;
                                        $sectionTotals[$mk]['planned']++;
                                    }
                                    if ($actualRaw != '') {
                                        $monthTotals[$mk]['actual_done']++;
                                        $sectionTotals[$mk]['actual_done']++;
                                    }
                                    $visitDisp = ppmInhouseGetVisitCellDisplay($plannedRaw, $actualRaw);
                                    $csvRow[$ml . ' Planned'] = $visitDisp['planned_text'];
                                    $csvRow[$ml . ' Actual'] = $visitDisp['actual_text'];
                            ?>
                            <td class="text-nowrap <?php echo htmlspecialchars($visitDisp['planned_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($visitDisp['planned_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap <?php echo htmlspecialchars($visitDisp['actual_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($visitDisp['actual_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php }
                            } ?>
                        </tr>
                        <?php
                                        $csv_data[] = $csvRow;
                                    }
                                }
                        ?>
                        <tr class="table-light fw-semibold">
                            <td colspan="6" class="text-end"><?php echo htmlspecialchars($freqLabel, ENT_QUOTES, 'UTF-8'); ?> subtotal</td>
                            <?php if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $mk => $ml) { ?>
                            <td class="text-center"><?php echo (int) ($sectionTotals[$mk]['planned'] ?? 0); ?></td>
                            <td class="text-center"><?php echo (int) ($sectionTotals[$mk]['actual_done'] ?? 0); ?></td>
                            <?php }
                            } ?>
                        </tr>
                        <tr class="bg-white"><td colspan="<?php echo (int) $ppmReportColspan; ?>" class="p-1 border-0"></td></tr>
                        <?php
                            }
                            $csvTotalRow = array(
                                'S.No' => '',
                                'Frequency Group' => '',
                                'Asset Number' => '',
                                'Equipment Name' => '',
                                'Category' => '',
                                'Location' => 'Grand Total',
                                'Frequency' => '',
                            );
                            if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $mk => $ml) {
                                    $csvTotalRow[$ml . ' Planned'] = (int) ($monthTotals[$mk]['planned'] ?? 0);
                                    $csvTotalRow[$ml . ' Actual'] = (int) ($monthTotals[$mk]['actual_done'] ?? 0);
                                }
                            }
                            $csv_data[] = $csvTotalRow;
                            $_SESSION['download_data'] = $csv_data;
                            $_SESSION['csv_header'] = array(
                                'company_name' => $_SESSION['config']['name'] ?? '',
                                'title' => 'INHOUSE PPM Report ' . ($reportYear != '' ? $reportYear : ''),
                                'name' => count((array)$filterSummaryParts) > 0 ? implode(' | ', $filterSummaryParts) : '',
                                'date_time' => date('d-m-Y h:i A'),
                            );
                        } else { ?>
                        <tr>
                            <td colspan="<?php echo (int) $ppmReportColspan; ?>" class="text-center text-danger">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No records found for the selected filters.</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <?php if ($hasSearched && count((array)$rowsByFrequency) > 0) { ?>
                    <tfoot>
                        <tr class="fw-bold table-light">
                            <td colspan="5" class="text-end">Grand Total</td>
                            <?php if (count((array)$monthLabels) > 0) {
                                foreach ($monthLabels as $mk => $ml) { ?>
                            <td class="text-center"><?php echo (int) ($monthTotals[$mk]['planned'] ?? 0); ?></td>
                            <td class="text-center"><?php echo (int) ($monthTotals[$mk]['actual_done'] ?? 0); ?></td>
                            <?php }
                            } ?>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    if ($.fn.select2) {
        $("#ppm_year").select2({ width: "100%" });
        $("#ppm_category_id, #ppm_location_id").select2({ width: "100%", allowClear: true });
    }
});

$("#ppmInhouseReportForm").submit(function(e) {
    var year = $("#ppm_year").val();
    $("#ppm_year_err").addClass("d-none");
    if (!year) {
        $("#ppm_year_err").text("Please select Year").removeClass("d-none");
        e.preventDefault();
    }
});
</script>
