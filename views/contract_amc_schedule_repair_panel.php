<?php

$repairEligibility = $repairEligibility ?? 'hidden';

$repairOrder = is_array($repairOrder ?? null) ? $repairOrder : array();

$repairOrderLines = is_array($repairOrderLines ?? null) ? $repairOrderLines : array();

$repairContractAssets = is_array($repairContractAssets ?? null) ? $repairContractAssets : array();

$repairItems = is_array($repairItems ?? null) ? $repairItems : array();

if (count((array)$repairItems) == 0 && count((array)($repair_items ?? array())) > 0) {
    $repairItems = $repair_items;
}

$repairScheduleId = (int) ($schedule_id ?? 0);

$repairContractId = (int) ($schedule['contract_id'] ?? 0);

$repairLocationId = (int) ($repairOrder['location_id'] ?? 0);

$repairDefaultAssetId = (int) ($repairOrder['default_asset_id'] ?? ($schedule['asset_id'] ?? 0));

$repairOrderId = (int) ($repairOrder['id'] ?? 0);

$repairVendorName = htmlspecialchars($repairOrder['vendor_name'] ?? ($schedule['party_name'] ?? ''), ENT_QUOTES, 'UTF-8');

$repairContractName = htmlspecialchars($repairOrder['contract_name'] ?? ($schedule['contract_name'] ?? ''), ENT_QUOTES, 'UTF-8');

if ($repairEligibility == 'hidden') {

    return;

}

$isRepairEditable = ($repairEligibility == 'editable');

?>

<div class="contract-amc-repair-panel mt-3 border-top pt-3" id="contractAmcRepairPanel"

    data-schedule-id="<?php echo $repairScheduleId; ?>"

    data-repair-eligibility="<?php echo htmlspecialchars($repairEligibility, ENT_QUOTES, 'UTF-8'); ?>">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">

        <h6 class="contract-amc-section-title mb-0"><?= __('Repair Order') ?></h6>

        <?php if (!$isRepairEditable) { ?>

        <span class="badge bg-secondary"><?= __('Locked — job completed') ?></span>

        <?php } ?>

    </div>

    <?php if ($isRepairEditable) { ?>

    <p class="text-muted small mb-2"><?= __('Capture parts and PO numbers while the job is in progress. Save before Finish Job.') ?></p>

    <?php } ?>

    <form id="contractAmcRepairOrderForm">

        <input type="hidden" name="repair_order_id" id="repair_order_id" value="<?php echo $repairOrderId; ?>">

        <input type="hidden" name="schedule_id" value="<?php echo $repairScheduleId; ?>">

        <input type="hidden" name="contract_id" value="<?php echo $repairContractId; ?>">

        <input type="hidden" name="source_type" value="planned_visit">

        <input type="hidden" name="location_id" value="<?php echo $repairLocationId; ?>">

        <input type="hidden" name="default_asset_id" value="<?php echo $repairDefaultAssetId; ?>">

        <input type="hidden" name="contract_amc_location_id" value="<?php echo (int) ($repairOrder['contract_amc_location_id'] ?? 0); ?>">

        <div class="row g-2 mb-2">

            <div class="col-md-3">

                <label for="repair_servicenow_ref" class="form-label small mb-0"><?= __('ServiceNow Ref') ?></label>

                <?php if ($isRepairEditable) { ?>

                <input type="text" class="form-control form-control-sm" name="servicenow_ref" id="repair_servicenow_ref" value="<?php echo htmlspecialchars($repairOrder['servicenow_ref'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= htmlspecialchars(__('Optional'), ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars(__('ServiceNow Ref'), ENT_QUOTES, 'UTF-8') ?>">

                <?php } else { ?>

                <div class="form-control form-control-sm bg-light"><?php echo htmlspecialchars($repairOrder['servicenow_ref'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>

                <?php } ?>

            </div>

            <div class="col-md-3">

                <label for="repair_default_po" class="form-label small mb-0"><?= __('Default PO') ?></label>

                <?php if ($isRepairEditable) { ?>

                <input type="text" class="form-control form-control-sm" name="default_po_number" id="repair_default_po" value="<?php echo htmlspecialchars($repairOrder['default_po_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= htmlspecialchars(__('Optional'), ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars(__('Default PO'), ENT_QUOTES, 'UTF-8') ?>">

                <?php } else { ?>

                <div class="form-control form-control-sm bg-light"><?php echo htmlspecialchars($repairOrder['default_po_number'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></div>

                <?php } ?>

            </div>

            <div class="col-md-3">

                <span class="form-label small mb-0 d-block"><?= __('Contract') ?></span>

                <div class="form-control form-control-sm bg-light"><?php echo $repairContractName != '' ? $repairContractName : '—'; ?></div>

            </div>

            <div class="col-md-3">

                <span class="form-label small mb-0 d-block"><?= __('Vendor') ?></span>

                <div class="form-control form-control-sm bg-light"><?php echo $repairVendorName != '' ? $repairVendorName : '—'; ?></div>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-sm table-bordered mb-2" id="contractAmcRepairLinesTable">

                <thead class="table-light">

                    <tr>

                        <th scope="col"><?= __('Item') ?></th>

                        <th scope="col"><?= __('Part / Description') ?></th>

                        <th scope="col" style="width:70px;"><?= __('Qty') ?></th>

                        <th scope="col"><?= __('PO No.') ?></th>

                        <th scope="col" style="width:90px;"><?= __('Amount') ?></th>

                        <?php if ($isRepairEditable) { ?><th scope="col" style="width:40px;" aria-label="<?= htmlspecialchars(__('Actions'), ENT_QUOTES, 'UTF-8') ?>"><span class="visually-hidden"><?= __('Actions') ?></span></th><?php } ?>

                    </tr>

                </thead>

                <tbody id="contractAmcRepairLinesBody">

                    <?php

                    include_once __DIR__ . '/contract_amc_schedule_repair_line_row.php';

                    $lineIdx = 0;

                    $repairItems = $repairItems ?? array();

                    if (count((array)$repairOrderLines) > 0) {

                        foreach ($repairOrderLines as $line) {

                            if (is_object($line)) {

                                $line = (array) $line;

                            }

                            render_contract_amc_schedule_repair_line_row(array(
                                'line' => $line,
                                'isRepairEditable' => $isRepairEditable ?? false,
                                'repairItems' => $repairItems,
                                'lineIdx' => $lineIdx,
                            ));

                            $lineIdx++;

                        }

                    } elseif ($isRepairEditable) {

                        $line = array();

                        render_contract_amc_schedule_repair_line_row(array(
                            'line' => $line,
                            'isRepairEditable' => $isRepairEditable ?? false,
                            'repairItems' => $repairItems,
                            'lineIdx' => $lineIdx,
                        ));

                        $lineIdx = 1;

                    }

                    ?>

                </tbody>

            </table>

        </div>

        <?php if ($isRepairEditable) { ?>

        <button type="button" class="btn btn-outline-success btn-sm mb-2" id="contractAmcRepairAddLine">

            <i class="ti-plus" aria-hidden="true"></i> <?= __('Add Item') ?>

        </button>

        <button type="button" class="btn btn-primary btn-sm" id="contractAmcRepairSaveBtn">

            <i class="ti-save" aria-hidden="true"></i> <?= __('Save Repair Order') ?>

        </button>

        <?php } elseif (count((array)$repairOrderLines) == 0) { ?>

        <p class="text-muted small mb-0"><?= __('No repair items recorded for this visit.') ?></p>

        <?php } ?>

    </form>

</div>

<script type="application/json" id="contractAmcRepairItemsJson"><?php echo json_encode($repairItems); ?></script>

<script type="application/json" id="contractAmcRepairAssetsJson"><?php echo json_encode($repairContractAssets['assets'] ?? array()); ?></script>

<script type="application/json" id="contractAmcRepairLocationsJson"><?php echo json_encode($repairContractAssets['locations'] ?? array()); ?></script>

