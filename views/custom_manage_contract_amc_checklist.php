<?php
$contract_id = (int) ($_GET['contract_id'] ?? 0);
$url = API . "?mode=getcontractamcchecklist&method=get&contract_id=" . $contract_id;
$response = callAPI($url);
$data = json_decode($response, true);
$contract = $data['contract'] ?? array();
$checklist_items = $data['checklist_items'] ?? array();
$checklistIsDefault = !empty($data['checklist_is_default']);
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
                <h5 class="mb-0">Manage Checklist — <?php echo $contractName; ?></h5>
                <?php if ($transactionNo != '') { ?>
                    <small class="text-muted">Transaction No: <?php echo $transactionNo; ?></small>
                <?php } ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo $schedulesUrl; ?>" class="btn btn-outline-info btn-sm">View Schedules</a>
                <button type="button" class="btn  btn-sm"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
        <div class="card-body">
            <?php if ($contractIsCancelled) { ?>
                <div class="alert alert-danger mb-3" role="alert">
                    This contract has been cancelled. Checklist items cannot be changed.
                    <?php if (!empty($contract['cancel_reason'])) { ?>
                        <span class="d-block mt-1"><strong>Reason:</strong> <?php echo htmlspecialchars($contract['cancel_reason'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } ?>
                </div>
            <?php } elseif (!$contractIsApproved) { ?>
                <div class="alert alert-warning mb-3" role="alert">
                    This contract is not approved yet. You can review the checklist below, but changes are disabled until approval is complete.
                </div>
            <?php } else { ?>
                <div class="alert alert-info mb-3 f-12" role="alert">
                    Define checklist items for all service visits on this contract. Technicians must complete every item before finishing a visit.
                    <?php if ($checklistIsDefault) { ?>
                        <span class="d-block mt-1">Showing default items — save to customize for this contract.</span>
                    <?php } ?>
                </div>
            <?php } ?>

            <form id="contractAmcChecklistForm">
                <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">
                <div id="contractAmcChecklistContainer" class="mb-3">
                    <?php if (count((array)$checklist_items) > 0) {
                        foreach ($checklist_items as $itemId => $itemName) { ?>
                    <div class="d-flex align-items-start gap-2 mb-2 contract-amc-checklist-row">
                        <div class="flex-grow-1">
                            <input type="hidden" name="existing_checklist_ids[]" value="<?php echo htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8'); ?>">
                            <input aria-label="Checklist Items[]" type="text" name="checklist_items[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $canExecute ? '' : 'readonly'; ?> required>
                        </div>
                        <?php if ($canExecute) { ?>
                        <button type="button" class="btn btn-sm btn-outline-danger contract-amc-checklist-remove-btn" title="Remove item">
                            <i class="ti-trash"></i>
                        </button>
                        <?php } ?>
                    </div>
                    <?php }
                    } else { ?>
                    <p class="text-muted mb-0 f-12">No checklist items.</p>
                    <?php } ?>
                </div>

                <?php if ($canExecute) { ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-top pt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="contractAmcChecklistAddBtn">
                        <i class="ti-plus me-1"></i> Add Item
                    </button>
                    <button type="button" class="btn btn-primary" id="saveContractAmcChecklistBtn">
                        <i class="ti-save me-1"></i> Save Checklist
                    </button>
                </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<script>
window.addEventListener("load", function() {
    function escapeHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function addChecklistRow(value, existingId) {
        var val = escapeHtml(value || '');
        var idVal = escapeHtml(existingId || '');
        var row = '<div class="d-flex align-items-start gap-2 mb-2 contract-amc-checklist-row">' +
            '<div class="flex-grow-1">' +
            '<input type="hidden" name="existing_checklist_ids[]" value="' + idVal + '">' +
            '<input aria-label="Checklist Items[]" type="text" name="checklist_items[]" class="form-control form-control-sm" value="' + val + '" required>' +
            '</div>' +
            '<button type="button" class="btn btn-sm btn-outline-danger contract-amc-checklist-remove-btn" title="Remove item">' +
            '<i class="ti-trash"></i></button></div>';
        $("#contractAmcChecklistContainer").append(row);
    }

    $(document).on("click", ".contract-amc-checklist-remove-btn", function() {
        var $rows = $("#contractAmcChecklistContainer .contract-amc-checklist-row");
        if ($rows.length <= 1) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'At least one checklist item is required', '');
            return;
        }
        $(this).closest('.contract-amc-checklist-row').remove();
    });

    $("#contractAmcChecklistAddBtn").on("click", function() {
        addChecklistRow('', '');
    });

    $("#saveContractAmcChecklistBtn").on("click", function() {
        var $rows = $("#contractAmcChecklistContainer .contract-amc-checklist-row");
        if ($rows.length == 0) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'At least one checklist item is required', '');
            return;
        }
        var hasBlank = false;
        $rows.find('input[name="checklist_items[]"]').each(function() {
            if ($.trim($(this).val()) == '') {
                hasBlank = true;
            }
        });
        if (hasBlank) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Checklist item name cannot be empty', '');
            return;
        }

        var data = $("#contractAmcChecklistForm").serializeArray();
        data.push({ name: 'page', value: 'savecontractamcchecklist' });
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
