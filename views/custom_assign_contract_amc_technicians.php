<?php
$contract_id = (int) ($_GET['contract_id'] ?? 0);
$url = API . "?mode=getcontractamctechnicianassignments&method=get&contract_id=" . $contract_id;
$response = callAPI($url);
$data = json_decode($response, true);
$contract = $data['contract'] ?? array();
$locations = $data['locations'] ?? array();
$technicians = $data['technicians'] ?? array();
$employees = $data['employees'] ?? array();
$serviceMode = resolveContractAmcServiceMode($contract);
$isInhouse = ($serviceMode == 'inhouse');
$assigneeLabel = $isInhouse ? 'Employee' : 'Technician';
$pageTitle = $isInhouse ? 'Default Employees' : 'Default Technicians';
$canExecute = !empty($data['can_execute']);
$contractIsApproved = !empty($data['contract_is_approved']) || (($contract['current_status'] ?? '') == ST_APPROVED);
$contractIsCancelled = (!empty($contract['cancel_status']) && $contract['cancel_status'] == 'Y')
    || ((int) ($contract['current_status'] ?? 0) == (int) ST_CANCEL);
$contractName = htmlspecialchars($contract['contract_name'] ?? 'Contract', ENT_QUOTES, 'UTF-8');
$transactionNo = htmlspecialchars($contract['transaction_no'] ?? '', ENT_QUOTES, 'UTF-8');
$schedulesUrl = 'index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=' . $contract_id;
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0"><?php echo $pageTitle; ?> — <?php echo $contractName; ?></h5>
                <?php if ($transactionNo != '') { ?>
                    <small class="text-muted">Transaction No: <?php echo $transactionNo; ?></small>
                <?php } ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?option=COM_MANAGE_CONTRACT_AMC_CHECKLIST&contract_id=<?php echo $contract_id; ?>" class="btn btn-outline-secondary btn-sm">Manage Checklist</a>
                <a href="<?php echo $schedulesUrl; ?>" class="btn btn-outline-info btn-sm">View Schedules</a>
                <a href="javascript:history.back();" class="btn  btn-sm"><i class="icofont icofont-reply"></i>Back</a>
            </div>
        </div>
        <div class="card-body">
            <?php if ($contractIsCancelled) { ?>
                <div class="alert alert-danger mb-3" role="alert">
                    This contract has been cancelled. Default <?php echo strtolower($assigneeLabel); ?> assignments cannot be changed.
                    <?php if (!empty($contract['cancel_reason'])) { ?>
                        <span class="d-block mt-1"><strong>Reason:</strong> <?php echo htmlspecialchars($contract['cancel_reason'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } ?>
                </div>
            <?php } elseif (!$contractIsApproved) { ?>
                <div class="alert alert-warning mb-3" role="alert">
                    This contract is not approved yet. You can review assets below, but <?php echo strtolower($assigneeLabel); ?> assignments are disabled until approval is complete.
                </div>
            <?php } else { ?>
                <div class="alert alert-info mb-3 f-12" role="alert">
                    Assign a default <?php echo strtolower($assigneeLabel); ?> for each asset. Pending visits will inherit this assignment. Use the schedule page to override for a specific visit when needed.
                </div>
            <?php } ?>

            <?php if (count((array)$locations) == 0) { ?>
                <p class="text-muted mb-0">No AMC locations or assets found for this contract.</p>
            <?php } else { ?>
                <form id="contractAmcTechnicianAssignForm">
                    <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">
                    <div class="accordion" id="amcTechAssignLocationsAccordion">
                    <?php foreach ($locations as $loc) {
                        if (is_object($loc)) {
                            $loc = json_decode(json_encode($loc), true);
                        }
                        $locId = (int) ($loc['id'] ?? 0);
                        $locName = htmlspecialchars($loc['location_name'] ?? ('Location #' . ($loc['location_id'] ?? '')), ENT_QUOTES, 'UTF-8');
                        $assets = $loc['assets'] ?? array();
                        $assetCount = count((array)$assets);
                        $locCollapseId = 'amcTechLocCollapse_' . $locId;
                        $locHeadingId = 'amcTechLocHeading_' . $locId;
                        ?>
                        <div class="accordion-item mb-2 border rounded shadow-sm">
                            <h2 class="accordion-header" id="<?php echo $locHeadingId; ?>">
                                <button class="accordion-button collapsed fw-semibold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $locCollapseId; ?>" aria-expanded="false" aria-controls="<?php echo $locCollapseId; ?>">
                                    <i class="ti-location-pin me-2"></i><?php echo $locName; ?> (<?php echo $assetCount; ?> asset<?php echo $assetCount == 1 ? '' : 's'; ?>)
                                </button>
                            </h2>
                            <div id="<?php echo $locCollapseId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $locHeadingId; ?>">
                                <div class="accordion-body p-2">
                                    <?php if ($assetCount == 0) { ?>
                                        <p class="text-muted f-12 mb-0">No assets at this location.</p>
                                    <?php } else { ?>
                                        <?php if ($canExecute) { ?>
                                        <div class="d-flex flex-wrap align-items-end gap-2 mb-3 p-2 bg-light rounded border">
                                            <div class="flex-grow-1" style="min-width:220px;">
                                                <label class="form-label f-12 mb-1">Apply to all assets at this location</label>
                                                <select class="form-control js-example-basic-single amc-loc-bulk-assignee-select" data-loc-id="<?php echo $locId; ?>">
                                                    <option value="">Select <?php echo strtolower($assigneeLabel); ?></option>
                                                    <?php
                                                    $bulkList = $isInhouse ? $employees : $technicians;
                                                    if (count((array)$bulkList) > 0) {
                                                        foreach ($bulkList as $aid => $aname) { ?>
                                                    <option value="<?php echo (int) $aid; ?>"><?php echo htmlspecialchars($aname, ENT_QUOTES, 'UTF-8'); ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm amc-loc-bulk-apply-btn" data-loc-id="<?php echo $locId; ?>">Apply</button>
                                        </div>
                                        <?php } ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm f-12 mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Asset</th>
                                                        <th>Frequency</th>
                                                        <th>Pending Visits</th>
                                                        <th>Default <?php echo $assigneeLabel; ?></th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($assets as $asset) {
                                                    if (is_object($asset)) {
                                                        $asset = json_decode(json_encode($asset), true);
                                                    }
                                                    $assetRowId = (int) ($asset['id'] ?? 0);
                                                    $assetCode = htmlspecialchars($asset['asset_code'] ?? '—', ENT_QUOTES, 'UTF-8');
                                                    $assetName = htmlspecialchars($asset['asset_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                                                    $categoryName = htmlspecialchars($asset['category_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                                                    $frequency = htmlspecialchars($asset['service_frequency'] ?? '—', ENT_QUOTES, 'UTF-8');
                                                    $defaultAssigneeId = $isInhouse ? (int) ($asset['default_employee_id'] ?? 0) : (int) ($asset['default_technician_id'] ?? 0);
                                                    $defaultAssigneeName = $isInhouse
                                                        ? ($asset['default_employee_name'] ?? '—')
                                                        : ($asset['default_technician_name'] ?? '—');
                                                    $assigneeList = $isInhouse ? $employees : $technicians;
                                                    $assigneeField = $isInhouse ? 'asset_employees' : 'asset_technicians';
                                                    $pendingCount = (int) ($asset['pending_count'] ?? 0);
                                                    $isAssigned = !empty($asset['is_assigned']);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $categoryName; ?></td>
                                                        <td><span class="fw-semibold"><?php echo $assetCode; ?></span><br><small class="text-muted"><?php echo $assetName; ?></small></td>
                                                        <td><?php echo $frequency; ?></td>
                                                        <td><?php echo $pendingCount; ?></td>
                                                        <td>
                                                            <?php if ($canExecute) { ?>
                                                            <select name="<?php echo $assigneeField; ?>[<?php echo $assetRowId; ?>]" class="form-control js-example-basic-single amc-asset-assignee-select" data-loc-id="<?php echo $locId; ?>" data-asset-id="<?php echo $assetRowId; ?>">
                                                                <option value="">Select <?php echo strtolower($assigneeLabel); ?></option>
                                                                <?php if (count((array)$assigneeList) > 0) {
                                                                    foreach ($assigneeList as $aid => $aname) { ?>
                                                                <option value="<?php echo (int) $aid; ?>" <?php echo ($defaultAssigneeId == (int) $aid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($aname, ENT_QUOTES, 'UTF-8'); ?></option>
                                                                <?php }
                                                                } ?>
                                                            </select>
                                                            <?php } else { ?>
                                                            <?php echo htmlspecialchars($defaultAssigneeName, ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($isAssigned) { ?>
                                                                <span class="badge bg-success">Assigned</span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">Unassigned</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>

                    <?php if ($canExecute) { ?>
                    <div class="border-top pt-3 mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="propagate_pending" id="propagatePendingVisits" value="1" checked>
                            <label class="form-check-label f-12" for="propagatePendingVisits">Update pending visits without overrides</label>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveContractAmcTechniciansBtn">
                            <i class="ti-save me-1"></i> Save Assignments
                        </button>
                    </div>
                    <?php } ?>
                </form>
            <?php } ?>
        </div>
    </div>
</div>
<script>
window.addEventListener("load", function() {
    $(".js-example-basic-single").select2();

    $(document).on("click", ".amc-loc-bulk-apply-btn", function() {
        var locId = $(this).data("loc-id");
        var assigneeId = $('.amc-loc-bulk-assignee-select[data-loc-id="' + locId + '"]').val();
        if (!assigneeId) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Select an assignee first', '');
            return;
        }
        $('.amc-asset-assignee-select[data-loc-id="' + locId + '"]').val(assigneeId).trigger('change');
    });

    $("#saveContractAmcTechniciansBtn").on("click", function() {
        var $form = $("#contractAmcTechnicianAssignForm");
        var data = $form.serializeArray();
        data.push({ name: 'page', value: 'savecontractamcassettechnicians' });
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: $.param(data),
            success: function(resp) {
                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (r.status == 'success') {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', r.message || 'Saved', '');
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || 'Save failed', '');
                }
            }
        });
    });
});
</script>
