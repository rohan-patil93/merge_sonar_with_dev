<?php
$_SESSION['apply_js']['multiselest'] = 1;

$lookupUrl = API . "?mode=getamccomplaintsdetails&method=get";
$lookup = json_decode(callAPI($lookupUrl), true);
$filterContracts = $lookup['contracts'] ?? array();
$filterVendors = $lookup['filter_vendors'] ?? array();
$filterLocations = $lookup['locations'] ?? array();
$filterAssets = $lookup['filter_assets'] ?? array();

$queryData = http_build_query($_GET);
$url = API . "?mode=getamccomplaintsreportdata&method=get&" . $queryData;
$result = callAPI($url);
$result = json_decode($result, true);
$report = $result['report_res'] ?? array();

if (!empty($_GET['from_date']) && !empty($_GET['to_date']) && count((array)$report) > 0) {
    $csvRows = array();
    foreach ($report as $row) {
        $row = is_array($row) ? $row : (array) $row;
        $assetItem = ($row['complaint_type'] == 'Item') ? ($row['item_name'] ?? '') : trim(($row['asset_code'] ?? '') . ' ' . ($row['asset_description'] ?? ''));
        $st = (int) ($row['current_status'] ?? 0);
        $stLabel = ($st == 0) ? 'Active' : (($st >= 199) ? 'Completed' : 'In Progress');
        $csvRows[] = array(
            'Request No' => $row['transaction_no'] ?? '',
            'Date' => !empty($row['transaction_date']) ? date('d-M-Y', strtotime($row['transaction_date'])) : '',
            'Complaint Name' => $row['complaint_name'] ?? '',
            'Contract' => $row['contract_name'] ?? '',
            'Vendor' => $row['vendor_name'] ?? '',
            'Asset Item' => $assetItem,
            'Location' => $row['location_name'] ?? '',
            'ServiceNow No' => $row['servicenow_ref'] ?? '',
            'Status' => $stLabel,
            'Remarks' => $row['remarks'] ?? '',
        );
    }
    $_SESSION['download_data'] = $csvRows;
    $_SESSION['csv_header'] = array(
        'company_name' => $_SESSION['company_name'] ?? '',
        'title' => 'Adhoc Service Requests Report',
        'name' => 'Filters: ' . http_build_query(array_filter($_GET, function ($k) { return $k != 'option';
}, ARRAY_FILTER_USE_KEY)),
        'date_time' => date('d-M-Y H:i'),
    );
}

function statusBadge($s)
{
    if ($s == 0) { return '<span class="badge bg-primary">Active</span>';
}
    if ($s > 0 && $s < 199) { return '<span class="badge bg-warning">In Progress</span>';
}
    if ($s == 199) { return '<span class="badge bg-success">Completed</span>';
}
    return '<span class="badge bg-secondary">Unknown</span>';
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Adhoc Service Requests Report</h5>
            <?php if (!empty($_SESSION['download_data'])) { ?>
            <a href="custom_view_download.php?page=AmcAdhocRequests&format=csv" class="btn btn-outline-primary btn-sm">CSV Export</a>
            <?php } ?>
        </div>
        <div class="card-body">
            <form method="get" id="reportFilterForm" class="row g-3 mb-3">
                <input type="hidden" name="option" value="COM_LIST_AMC_COMPLAINTS_REPORT">
                <div class="col-lg-2">
                    <span>From Date <span class="text-danger">*</span></span>
                    <input aria-label="From Date" type="date" class="form-control" name="from_date" id="from_date" value="<?php echo $_GET['from_date'] ?? ''; ?>">
                    <div id="from_date_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-2">
                    <span>To Date <span class="text-danger">*</span></span>
                    <input aria-label="To Date" type="date" class="form-control" name="to_date" id="to_date" value="<?php echo $_GET['to_date'] ?? ''; ?>">
                    <div id="to_date_err" class="text-danger small d-none"></div>
                </div>
                <div class="col-lg-2 form-group w-full">
                    <span>Contract</span>
                    <select aria-label="Contract" class="form-select js-example-basic-single" id="contract_id" name="contract_id">
                        <option value="">All</option>
                        <?php if (count((array)$filterContracts) > 0) {
                            foreach ($filterContracts as $c) {
                                $c = is_array($c) ? $c : (array) $c;
                                $sel = (isset($_GET['contract_id']) && $_GET['contract_id'] == $c['id']) ? 'selected' : '';
                                ?>
                        <option value="<?php echo (int) $c['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c['contract_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span>Vendor</span>
                    <select aria-label="Vendor" class="form-select js-example-basic-single" name="vendor_id">
                        <option value="">All</option>
                        <?php if (count((array)$filterVendors) > 0) {
                            foreach ($filterVendors as $v) {
                                $v = is_array($v) ? $v : (array) $v;
                                $sel = (isset($_GET['vendor_id']) && $_GET['vendor_id'] == $v['id']) ? 'selected' : '';
                                ?>
                        <option value="<?php echo (int) $v['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($v['display_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span>Location</span>
                    <select aria-label="Location" class="form-select js-example-basic-single" name="location_id">
                        <option value="">All</option>
                        <?php if (count((array)$filterLocations) > 0) {
                            foreach ($filterLocations as $loc) {
                                $loc = is_array($loc) ? $loc : (array) $loc;
                                $sel = (isset($_GET['location_id']) && $_GET['location_id'] == $loc['id']) ? 'selected' : '';
                                ?>
                        <option value="<?php echo (int) $loc['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($loc['short_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span>Asset</span>
                    <select aria-label="Asset" class="form-select js-example-basic-single" name="asset_id">
                        <option value="">All</option>
                        <?php if (count((array)$filterAssets) > 0) {
                            foreach ($filterAssets as $a) {
                                $a = is_array($a) ? $a : (array) $a;
                                $sel = (isset($_GET['asset_id']) && $_GET['asset_id'] == $a['id']) ? 'selected' : '';
                                $label = trim(($a['asset_code'] ?? '') . ' ' . ($a['asset_description'] ?? ''));
                                ?>
                        <option value="<?php echo (int) $a['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span>Status</span>
                    <select aria-label="Status" class="form-select js-example-basic-single" name="status">
                        <option value="">All</option>
                        <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Active</option>
                        <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="199" <?php echo (isset($_GET['status']) && $_GET['status'] == '199') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="icofont icofont-search"></i> Search</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th scope=\"col\">#</th>
                            <th scope=\"col\">Date</th>
                            <th scope=\"col\">Request No.</th>
                            <th scope=\"col\">Complaint Name</th>
                            <th scope=\"col\">Contract</th>
                            <th scope=\"col\">Vendor</th>
                            <th scope=\"col\">Asset / Item</th>
                            <th scope=\"col\">Location</th>
                            <th scope=\"col\">ServiceNow</th>
                            <th scope=\"col\">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$report) > 0) {
                            $i = 1;
                            foreach ($report as $row) {
                                $row = is_array($row) ? $row : (array) $row;
                                ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['transaction_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['complaint_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['contract_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if (($row['complaint_type'] ?? '') == 'Item') {
                                    echo htmlspecialchars($row['item_name'] ?? '-', ENT_QUOTES, 'UTF-8');
                                } else {
                                    echo htmlspecialchars(trim(($row['asset_code'] ?? '') . ' ' . ($row['asset_description'] ?? '')), ENT_QUOTES, 'UTF-8');
                                } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['location_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['servicenow_ref'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo statusBadge($row['current_status'] ?? 0); ?></td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr><td colspan="10" class="text-center text-muted">No records</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$("#reportFilterForm").submit(function(e) {
    var from = $("#from_date").val();
    var to = $("#to_date").val();
    $("#from_date_err, #to_date_err").addClass("d-none");
    if (!from) {
        $("#from_date_err").text("Please select From Date").removeClass("d-none");
        e.preventDefault();
        return;
    }
    if (!to) {
        $("#to_date_err").text("Please select To Date").removeClass("d-none");
        e.preventDefault();
        return;
    }
    if (new Date(to) < new Date(from)) {
        $("#to_date_err").text("To Date must be greater than From Date").removeClass("d-none");
        e.preventDefault();
    }
});
</script>
