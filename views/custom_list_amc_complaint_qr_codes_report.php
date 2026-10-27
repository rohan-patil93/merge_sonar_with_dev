<?php
$_SESSION['apply_js']['multiselest'] = 1;

// Capture filters
$selected_items  = $_GET['item_id']  ?? [];
$selected_assets = $_GET['asset_id'] ?? [];

// Build GET query string
$queryData = http_build_query($_GET);

// API URL
$url = API . "?mode=getcomplaintqrcodesreportdata&method=get&" . $queryData;

// Call API
$result = callAPI($url);
$result = json_decode($result, true);

// Returned data
$items_res  = $result['items_master']  ?? [];
$assets_res = $result['assets_master'] ?? [];
$qr_res = $result['qr_res'] ?? [];
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>AMC Complaint QR Codes Report</h5>
        </div>
        <div class="card-body">
            <form method="get" id="qrReportForm" class="row g-3 mb-3">
                <input type="hidden" name="option" value="COM_LIST_AMC_COMPLAINT_QR_CODES_REPORT">

                <div class="col-lg-4">
                    <span>Items</span>
                    <select aria-label="Item []" class="form-select js-example-basic-multiple" name="item_id[]" multiple>
                        <?php foreach ($items_res as $it): ?>
                            <option value="<?php echo $it['id']; ?>"
                                <?php
                                if (!empty($_GET['item_id']) && in_array($it['id'], $_GET['item_id']))
                                    { echo 'selected';
}
                                ?>>
                                <?php echo $it['item_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-lg-4">
                    <span>Assets</span>
                    <select aria-label="Asset []" class="form-select js-example-basic-multiple" name="asset_id[]" multiple>
                        <?php foreach ($assets_res as $as): ?>
                            <option value="<?php echo $as['id']; ?>"
                                <?php
                                if (!empty($_GET['asset_id']) && in_array($as['id'], $_GET['asset_id']))
                                    { echo 'selected';
}
                                ?>>
                                <?php echo $as['asset_code'] . ' - ' . $as['asset_description']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-lg-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100"><i class="icofont icofont-search"></i>Search</button>
                </div>

            </form>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <thead class="bg-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Code Type</th>
                            <th scope="col">Location</th>
                            <th scope="col">Item / Asset</th>
                            <th scope="col">QR Code</th>
                            <th scope="col">Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($qr_res)): $i = 1; ?>
                            <?php foreach ($qr_res as $row): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>

                                    <!-- CODE TYPE -->
                                    <td><?php echo htmlspecialchars($row['code_type']); ?></td>

                                    <!-- LOCATION NAME -->
                                    <td><?php echo htmlspecialchars($row['location_name']); ?></td>

                                    <!-- ITEM / ASSET (BASED ON code_type) -->
                                    <td>
                                        <?php if ($row['code_type'] == 'Item'): ?>
                                            <span class="text-primary">
                                                <?php echo $row['item_name'] ?: '-'; ?>
                                            </span>

                                        <?php elseif ($row['code_type'] == 'Assets'): ?>
                                            <span class="text-success">
                                                <?php echo $row['asset_code'] . ' - ' . $row['asset_description']; ?>
                                            </span>

                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>


                                    <td>
                                        <?php if (!empty($row['qr_code'])):
                                            $fullPath = "uploads/all/" . $row['qr_code']; ?>

                                            <a href="<?php echo $fullPath; ?>" target="_blank">
                                                <img src="<?php echo $fullPath; ?>"
                                                    alt="QR"
                                                    style="width:60px; height:60px; object-fit:cover; border:1px solid #ccc;">
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <!-- REMARKS -->
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
