<?php
$contract_id = (int) ($_GET['contract_id'] ?? 0);
$url = API . "?mode=getcontractamcscheduledrilldown&method=get&contract_id=" . $contract_id;
$response = callAPI($url);
$data = json_decode($response, true);
$contract = $data['contract'] ?? array();
$locations = $data['locations'] ?? array();
$serviceMode = resolveContractAmcServiceMode($contract);
$isInhouse = ($serviceMode == 'inhouse');
$assigneeLabel = $isInhouse ? 'Employee' : 'Technician';
$canExecute = !empty($data['can_execute']);
$contractIsApproved = !empty($data['contract_is_approved']) || (($contract['current_status'] ?? '') == ST_APPROVED);
$contractIsCancelled = (!empty($contract['cancel_status']) && $contract['cancel_status'] == 'Y')
    || ((int) ($contract['current_status'] ?? 0) == (int) ST_CANCEL);
$contractName = htmlspecialchars($contract['contract_name'] ?? 'Contract', ENT_QUOTES, 'UTF-8');
$transactionNo = htmlspecialchars($contract['transaction_no'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">AMC Schedules — <?php echo $contractName; ?></h5>
                <?php if ($transactionNo != '') { ?>
                    <small class="text-muted">Transaction No: <?php echo $transactionNo; ?></small>
                <?php } ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($contract_id > 0) { ?>
                <a href="index.php?option=COM_MANAGE_CONTRACT_AMC_CHECKLIST&contract_id=<?php echo $contract_id; ?>" class="btn btn-outline-secondary btn-sm">Manage Checklist</a>
                <a href="index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=<?php echo $contract_id; ?>" class="btn btn-outline-primary btn-sm">Manage Default <?php echo $assigneeLabel; ?>s</a>
                <?php } ?>
                <button type="button" class="btn  btn-sm"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
        <div class="card-body">
            <?php if ($contractIsCancelled) { ?>
                <div class="alert alert-danger mb-3" role="alert">
                    This contract has been cancelled. You can review planned visits below, but start/finish job actions are disabled.
                    <?php if (!empty($contract['cancel_reason'])) { ?>
                        <span class="d-block mt-1"><strong>Reason:</strong> <?php echo htmlspecialchars($contract['cancel_reason'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } ?>
                </div>
            <?php } elseif (!$contractIsApproved) { ?>
                <div class="alert alert-warning mb-3" role="alert">
                    This contract is not approved yet. You can review planned visits below, but start/finish job actions are disabled until approval is complete.
                </div>
            <?php } ?>
            <?php if (count((array)$locations) == 0) { ?>
                <p class="text-muted mb-0">No AMC locations or schedules found for this contract.</p>
            <?php } else { ?>
                <div class="accordion" id="amcScheduleLocationsAccordion">
                <?php foreach ($locations as $loc) {
                    if (is_object($loc)) {
                        $loc = json_decode(json_encode($loc), true);
                    }
                    $locId = (int) ($loc['id'] ?? 0);
                    $locName = htmlspecialchars($loc['location_name'] ?? ('Location #' . ($loc['location_id'] ?? '')), ENT_QUOTES, 'UTF-8');
                    $assets = $loc['assets'] ?? array();
                    $assetCount = count((array)$assets);
                    $locCollapseId = 'amcLocCollapse_' . $locId;
                    $locHeadingId = 'amcLocHeading_' . $locId;
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
                                    <div class="accordion" id="amcScheduleAssetsAccordion_<?php echo $locId; ?>">
                                    <?php foreach ($assets as $asset) {
                                        if (is_object($asset)) {
                                            $asset = json_decode(json_encode($asset), true);
                                        }
                                        $assetId = (int) ($asset['id'] ?? 0);
                                        $assetCode = htmlspecialchars($asset['asset_code'] ?? '—', ENT_QUOTES, 'UTF-8');
                                        $assetName = htmlspecialchars($asset['asset_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                                        $categoryName = htmlspecialchars($asset['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                                        $rollup = $asset['rollup'] ?? array();
                                        $schedules = $asset['schedules'] ?? array();
                                        $assetCollapseId = 'amcAssetCollapse_' . $assetId;
                                        $assetHeadingId = 'amcAssetHeading_' . $assetId;
                                        ?>
                                        <div class="accordion-item border-0 border-start border-3 border-info mb-2">
                                            <h2 class="accordion-header" id="<?php echo $assetHeadingId; ?>">
                                                <button class="accordion-button collapsed py-2 f-12" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $assetCollapseId; ?>" aria-expanded="false" aria-controls="<?php echo $assetCollapseId; ?>">
                                                    <span class="fw-semibold"><?php echo $assetCode; ?> — <?php echo $assetName; ?></span>
                                                    <?php if ($categoryName != '') { ?>
                                                    <small class="ms-2 text-muted">(<?php echo $categoryName; ?>)</small>
                                                    <?php } ?>
                                                    <?php if (count((array)$rollup) > 0) { ?>
                                                    <small class="ms-auto text-muted me-2">
                                                        Pending: <?php echo (int) ($rollup['pending_count'] ?? 0); ?>
                                                        <?php if (!empty($rollup['next_visit'])) { ?> | Next: <?php echo fromDate($rollup['next_visit']); ?><?php
} ?>
                                                    </small>
                                                    <?php } ?>
                                                </button>
                                            </h2>
                                            <div id="<?php echo $assetCollapseId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $assetHeadingId; ?>">
                                                <div class="accordion-body p-0 pt-2">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm f-12 mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th scope="col">Planned</th>
                                                                    <th scope="col">Actual Start</th>
                                                                    <th scope="col">Actual End</th>
                                                                    <th scope="col"><?php echo $assigneeLabel; ?></th>
                                                                    <th scope="col">Repair Order Amount</th>
                                                                    <th scope="col">Status</th>
                                                                    <th scope="col">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (count((array)$schedules) == 0) { ?>
                                                                    <tr><td colspan="7" class="text-center text-muted">No visits</td></tr>
                                                                <?php } else {
                                                                    foreach ($schedules as $sch) {
                                                                        if (is_object($sch)) {
                                                                            $sch = json_decode(json_encode($sch), true);
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($sch['planned_display'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                            <td><?php echo htmlspecialchars($sch['actual_start_display'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                            <td><?php echo htmlspecialchars($sch['actual_end_display'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                            <td>
                                                                                <?php
                                                                                if ($isInhouse) {
                                                                                    $assigneeName = htmlspecialchars($sch['effective_employee_name'] ?? $sch['effective_assignee_name'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                                    $isOverride = !empty($sch['employee_is_override']);
                                                                                } else {
                                                                                    $assigneeName = htmlspecialchars($sch['effective_technician_name'] ?? $sch['effective_assignee_name'] ?? $sch['technician_name'] ?? '', ENT_QUOTES, 'UTF-8');
                                                                                    $isOverride = !empty($sch['technician_is_override']);
                                                                                }
                                                                                if ($assigneeName == '') {
                                                                                    echo '<span class="text-muted">Unassigned</span>';
                                                                                } else {
                                                                                    echo $assigneeName;
                                                                                    if ($isOverride) {
                                                                                        echo ' <span class="badge bg-warning text-dark">override</span>';
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            <td><?php echo customeNumberFormate((float) ($sch['added_amount'] ?? 0)); ?></td>
                                                                            <td><span class="badge <?php echo $sch['status_class'] ?? 'bg-primary'; ?>"><?php echo htmlspecialchars($sch['status_label'] ?? 'Pending', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                                                            <td>
                                                                                <a href="#" class="openContractAmcScheduleModal" data-schedule-id="<?php echo (int) ($sch['id'] ?? 0); ?>" title="<?php echo $canExecute ? 'Manage visit' : 'View visit'; ?>">
                                                                                    <i class="ti-eye text-info f-16"></i>
                                                                                </a>
                                                                                <?php if (($sch['repair_eligibility'] ?? '') == 'editable') {
                                                                                    $repairUrl = 'index.php?option=COM_INSERT_AMC_REPAIR_ORDER&schedule_id=' . (int) ($sch['id'] ?? 0);
                                                                                    if (!empty($sch['repair_order_id'])) {
                                                                                        $repairUrl .= '&Rid=' . deocdeId('id:' . (int) $sch['repair_order_id']);
                                                                                    }
                                                                                    ?>
                                                                                <a href="<?php echo htmlspecialchars($repairUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="ms-1" title="Repair order (job in progress)">
                                                                                    <i class="fa fa-wrench text-warning f-16"></i>
                                                                                </a>
                                                                                <?php } elseif (($sch['repair_eligibility'] ?? '') == 'readonly' || !empty($sch['has_repair_order'])) {
                                                                                    $repairUrl = 'index.php?option=COM_INSERT_AMC_REPAIR_ORDER&schedule_id=' . (int) ($sch['id'] ?? 0);
                                                                                    if (!empty($sch['repair_order_id'])) {
                                                                                        $repairUrl .= '&Rid=' . deocdeId('id:' . (int) $sch['repair_order_id']);
                                                                                    }
                                                                                    ?>
                                                                                <a href="<?php echo htmlspecialchars($repairUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="ms-1" title="View repair order">
                                                                                    <i class="fa fa-wrench text-muted f-16"></i>
                                                                                </a>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php }
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade" id="kt_modal_contract_amc_schedule" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3" id="contract_amc_schedule_modal_body"></div>
    </div>
</div>
<script>
window.addEventListener("load", function() {
    $(document).on("click", ".openContractAmcScheduleModal", function(e) {
        e.preventDefault();
        var scheduleId = $(this).data("schedule-id");
        var loader = '<div class="text-center p-5"><img src="assets/images/spiner/spiner3.gif" style="width: 200px;" alt="Preview"></div>';
        $("#contract_amc_schedule_modal_body").html(loader);
        $("#kt_modal_contract_amc_schedule").modal("show");
        $.ajax({
            url: "views/custom_contract_amc_schedule_edit.php",
            type: "POST",
            data: { schedule_id: scheduleId },
            success: function(html) {
                $("#contract_amc_schedule_modal_body").html(html);
                $(".js-example-basic-single").select2();
                if (typeof window.initContractAmcScheduleModalHandlers === 'function') {
                    window.initContractAmcScheduleModalHandlers();
                }
            }
        });
    });
});
</script>
