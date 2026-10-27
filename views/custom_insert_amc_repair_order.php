<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['custom_insert_amc_repair_order'] = 1;

$complaint_id = (int) ($_GET['complaint_id'] ?? 0);
$schedule_id = (int) ($_GET['schedule_id'] ?? 0);
$repair_order_id = 0;
if (!empty($_GET['Rid'])) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $repair_order_id = (int) ($rid[1] ?? 0);
}

$query = http_build_query(array_filter(array(
    'complaint_id' => $complaint_id ?: null,
    'schedule_id' => $schedule_id ?: null,
    'repair_order_id' => $repair_order_id ?: null,
    'id' => $repair_order_id ?: null,
)));
$url = API . "?mode=getamcrepairorderformdata&method=get&admin_type=".$_SESSION['admin_type']."&". $query;
$formData = json_decode(callAPI($url), true)?? [];
$repairOrder = $formData['repair_order'] ?? array();
$lines = $formData['lines'] ?? array();
$contracts = $formData['contracts'] ?? array();
$contractAssets = $formData['contract_assets'] ?? array();
$repairItems = $formData['items'] ?? array();
$scheduleContext = $formData['schedule'] ?? array();
$repairEligibility = $formData['repair_eligibility'] ?? 'editable';
if ($schedule_id <= 0) {
    $schedule_id = (int) ($repairOrder['schedule_id'] ?? 0);
}
$isFromSchedule = ($schedule_id > 0);
$isRepairEditable = ($repairEligibility != 'readonly');
$isEdit = ($repair_order_id > 0 || !empty($repairOrder['id']));
$pageTitle = $isEdit ? 'Edit Repair Order' : 'New Repair Order';
if (!$isRepairEditable) {
    $pageTitle = 'View Repair Order';
}
$scheduleLocationLabel = $scheduleContext['location_label'] ?? '';
$scheduleAssetLabel = $scheduleContext['asset_name'] ?? '';
if ($scheduleLocationLabel == '' && (int) ($repairOrder['location_id'] ?? 0) > 0 && count((array)($contractAssets['locations'] ?? array())) > 0) {
    foreach ($contractAssets['locations'] as $locRow) {
        $locRow = is_array($locRow) ? $locRow : (array) $locRow;
        if ((int) ($locRow['location_id'] ?? 0) == (int) ($repairOrder['location_id'] ?? 0)) {
            $scheduleLocationLabel = $locRow['location_label'] ?? '';
            break;
        }
    }
}
if ($scheduleAssetLabel == '' && (int) ($repairOrder['default_asset_id'] ?? 0) > 0 && count((array)($contractAssets['assets'] ?? array())) > 0) {
    foreach ($contractAssets['assets'] as $assetRow) {
        $assetRow = is_array($assetRow) ? $assetRow : (array) $assetRow;
        if ((int) ($assetRow['asset_id'] ?? 0) == (int) ($repairOrder['default_asset_id'] ?? 0)) {
            $scheduleAssetLabel = trim(($assetRow['asset_code'] ?? '') . ' ' . ($assetRow['asset_description'] ?? $assetRow['asset_name'] ?? ''));
            break;
        }
    }
}
$scheduleVendorLabel = $scheduleContext['vendor_name'] ?? ($repairOrder['vendor_name'] ?? '');
if ($scheduleVendorLabel == '') {
    $scheduleVendorLabel = '—';
}
?>
<script src="assets/js/custom_insert_amc_repair_order.js"></script>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>
                <?php if (!empty($repairOrder['transaction_no'])) { ?>
                <small class="text-muted">(<?php echo htmlspecialchars($repairOrder['transaction_no'], ENT_QUOTES, 'UTF-8'); ?>)</small>
                <?php } ?>
            </h5>
            <a href="javascript:history.go(-1)" class="btn btn-outline-secondary btn-sm"><i class="icofont icofont-reply"></i> Back</a>
        </div>
        <div class="card-body">
            <?php if ($isFromSchedule) { ?>
            <div class="alert alert-light border mb-3">
                <h6 class="mb-2">Visit context</h6>
                <div class="row g-2 small">
                    <div class="col-md-3"><strong>Planned:</strong> <?php echo htmlspecialchars($scheduleContext['planned_display'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Actual start:</strong> <?php echo htmlspecialchars($scheduleContext['actual_start_display'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Actual end:</strong> <?php echo htmlspecialchars($scheduleContext['actual_end_display'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Status:</strong> <?php echo htmlspecialchars($scheduleContext['status_label'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Contract:</strong> <?php echo htmlspecialchars($scheduleContext['contract_name'] ?? ($repairOrder['contract_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Vendor:</strong> <?php echo htmlspecialchars($scheduleVendorLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Location:</strong> <?php echo htmlspecialchars($scheduleLocationLabel != '' ? $scheduleLocationLabel : '—', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="col-md-3"><strong>Asset:</strong> <?php echo htmlspecialchars($scheduleAssetLabel != '' ? $scheduleAssetLabel : '—', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <?php } ?>
            <?php if (!$isRepairEditable) { ?>
            <div class="alert alert-secondary py-2 mb-3">This visit is closed. Repair order is read-only.</div>
            <?php } ?>
            <form id="amcRepairOrderForm" method="post">
                <input type="hidden" name="repair_order_id" value="<?php echo (int) ($repairOrder['id'] ?? 0); ?>">
                <input type="hidden" name="complaint_id" value="<?php echo (int) ($repairOrder['complaint_id'] ?? $complaint_id); ?>">
                <input type="hidden" name="schedule_id" value="<?php echo (int) ($repairOrder['schedule_id'] ?? $schedule_id); ?>">
                <input type="hidden" name="source_type" value="<?php echo htmlspecialchars($repairOrder['source_type'] ?? ($isFromSchedule ? 'planned_visit' : ($complaint_id ? 'adhoc_complaint' : 'manual')), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="contract_amc_location_id" value="<?php echo (int) ($repairOrder['contract_amc_location_id'] ?? 0); ?>">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">ServiceNow Ref</label>
                        <input type="text" class="form-control" name="servicenow_ref" value="<?php echo htmlspecialchars($repairOrder['servicenow_ref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional" <?php echo $isRepairEditable ? '' : 'readonly'; ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Default PO Number</label>
                        <input type="text" class="form-control" id="amc_repair_default_po" name="default_po_number" value="<?php echo htmlspecialchars($repairOrder['default_po_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional" <?php echo $isRepairEditable ? '' : 'readonly'; ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Repair Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="repair_date" required value="<?php echo !empty($repairOrder['repair_date']) ? date('Y-m-d', strtotime($repairOrder['repair_date'])) : date('Y-m-d'); ?>" <?php echo $isRepairEditable ? '' : 'readonly'; ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">AMC Contract <span class="text-danger">*</span></label>
                        <?php if ($isFromSchedule) { ?>
                        <input type="hidden" name="contract_id" value="<?php echo (int) ($repairOrder['contract_id'] ?? 0); ?>">
                        <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($repairOrder['contract_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php } else { ?>
                        <select class="form-select js-example-basic-single" name="contract_id" id="amc_repair_contract_id" required>
                            <option value="">Select contract</option>
                            <?php if (count((array)$contracts) > 0) {
                                foreach ($contracts as $c) {
                                    $c = is_array($c) ? $c : (array) $c;
                                    $sel = ((int) ($repairOrder['contract_id'] ?? 0) == (int) ($c['id'] ?? 0)) ? 'selected' : '';
                                    ?>
                            <option value="<?php echo (int) $c['id']; ?>" data-vendor-id="<?php echo ($c['party_type'] ?? '') == 'vendor' ? (int) ($c['party_id'] ?? 0) : 0; ?>" data-vendor-name="<?php echo htmlspecialchars($c['vendor_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c['contract_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php }
                            } ?>
                        </select>
                        <?php } ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vendor</label>
                        <input type="hidden" name="vendor_id" id="amc_repair_vendor_id" value="<?php echo (int) ($repairOrder['vendor_id'] ?? 0); ?>">
                        <input type="text" class="form-control" id="amc_repair_vendor_name" readonly value="<?php echo htmlspecialchars($repairOrder['vendor_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <?php if ($isFromSchedule) { ?>
                        <input type="hidden" name="location_id" value="<?php echo (int) ($repairOrder['location_id'] ?? 0); ?>">
                        <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($scheduleLocationLabel, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php } else { ?>
                        <select class="form-select js-example-basic-single" name="location_id" id="amc_repair_location_id" <?php echo $isRepairEditable ? '' : 'disabled'; ?>>
                            <option value="">Select</option>
                            <?php if (count((array)($contractAssets['locations'] ?? array())) > 0) {
                                foreach ($contractAssets['locations'] as $loc) {
                                    $loc = is_array($loc) ? $loc : (array) $loc;
                                    $sel = ((int) ($repairOrder['location_id'] ?? 0) == (int) ($loc['location_id'] ?? 0)) ? 'selected' : '';
                                    ?>
                            <option value="<?php echo (int) $loc['location_id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($loc['location_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php }
                            } ?>
                        </select>
                        <?php } ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Default Asset</label>
                        <?php if ($isFromSchedule) { ?>
                        <input type="hidden" name="default_asset_id" value="<?php echo (int) ($repairOrder['default_asset_id'] ?? 0); ?>">
                        <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($scheduleAssetLabel, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php } else { ?>
                        <select class="form-select js-example-basic-single" name="default_asset_id" id="amc_repair_default_asset_id" <?php echo $isRepairEditable ? '' : 'disabled'; ?>>
                            <option value=""><?php echo ((int) ($repairOrder['location_id'] ?? 0) > 0) ? 'Select' : 'Select location first'; ?></option>
                            <?php
                            $headerLocationId = (int) ($repairOrder['location_id'] ?? 0);
                            if (count((array)($contractAssets['assets'] ?? array())) > 0) {
                                foreach ($contractAssets['assets'] as $a) {
                                    $a = is_array($a) ? $a : (array) $a;
                                    if ($headerLocationId > 0 && (int) ($a['location_id'] ?? 0) != $headerLocationId) {
                                        continue;
                                    }
                                    $sel = ((int) ($repairOrder['default_asset_id'] ?? 0) == (int) ($a['asset_id'] ?? 0)) ? 'selected' : '';
                                    $label = trim(($a['asset_code'] ?? '') . ' ' . ($a['asset_description'] ?? $a['asset_name'] ?? ''));
                                    ?>
                            <option value="<?php echo (int) $a['asset_id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php }
                            } ?>
                        </select>
                        <?php } ?>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="2" <?php echo $isRepairEditable ? '' : 'readonly'; ?>><?php echo htmlspecialchars($repairOrder['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Repair Items</h6>
                    <?php if ($isRepairEditable) { ?>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="amcRepairAddLine"><i class="ti-plus"></i> Add Item</button>
                    <?php } ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Part / Description</th>
                                <th>Qty</th>
                                <th>PO No.</th>
                                <th>Amount</th>
                                <?php if ($isRepairEditable) { ?><th></th><?php } ?>
                            </tr>
                        </thead>
                        <tbody id="amcRepairLinesBody">
                            <?php
                            $lineIdx = 0;
                            $repairContractAssets = $contractAssets;
                            $repairItems = $repairItems;
                            $repairLocationId = (int) ($repairOrder['location_id'] ?? 0);
                            if (count((array)$lines) > 0) {
                                foreach ($lines as $line) {
                                    $line = is_array($line) ? $line : (array) $line;
                                    include __DIR__ . '/contract_amc_schedule_repair_line_row.php';
                                    $lineIdx++;
                                }
                            } elseif ($isRepairEditable) {
                                $line = array();
                                include __DIR__ . '/contract_amc_schedule_repair_line_row.php';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small">Leave PO blank for customer-supplied parts. Default PO applies to lines unless overridden.</p>
                <?php if ($isRepairEditable) { ?>
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<script type="application/json" id="amcRepairItemsJson"><?php echo json_encode($repairItems); ?></script>
<script type="application/json" id="amcRepairAssetsJson"><?php echo json_encode($contractAssets['assets'] ?? array()); ?></script>
<script type="application/json" id="amcRepairLocationsJson"><?php echo json_encode($contractAssets['locations'] ?? array()); ?></script>
