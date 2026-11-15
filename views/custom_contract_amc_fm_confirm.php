<?php

include_once '../includes/session.php';

include_once '../dbconfig.php';

include_once '../comm.php';

$schedule_id = (int) ($_POST['schedule_id'] ?? 0);
$url = API . "?mode=getcontractamcfmconfirmdetails&method=get&schedule_id=" . $schedule_id;
$res = json_decode(callAPI($url), true) ?? [];
$schedule = $res['schedule'] ?? array();
$canConfirm = !empty($res['can_confirm']);

$vendorName = htmlspecialchars($schedule['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8');
$assetLabel = htmlspecialchars($schedule['asset_label'] ?? '—', ENT_QUOTES, 'UTF-8');
$plannedDisplay = htmlspecialchars($schedule['planned_display'] ?? '—', ENT_QUOTES, 'UTF-8');
$techCompleted = !empty($schedule['actual_end_datetime'])
    ? htmlspecialchars(formatAmcScheduleDateTime($schedule['actual_end_datetime']), ENT_QUOTES, 'UTF-8')
    : '—';
$contractName = htmlspecialchars($schedule['contract_name'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<div class="modal-header bg-primary text-white">
    <div>
        <h5 class="modal-title mb-1"><?= __('Confirm Visit') ?></h5>
        <?php if ($contractName != '') { ?>
            <small class="text-white-50"><?php echo $contractName; ?></small>
        <?php } ?>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= __('Close') ?>"></button>
</div>
<?php if (!$canConfirm) { ?>
<div class="modal-body">
    <div class="alert alert-warning mb-0"><?= __('This visit cannot be confirmed. It may not be finished by the technician yet, or you are not authorized.') ?></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('Close') ?></button>
</div>
<?php } else { ?>
<form id="contractAmcFmConfirmForm" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="f-12 text-muted mb-1"><?= __('Vendor') ?></div>
                <div class="fw-semibold"><?php echo $vendorName; ?></div>
            </div>
            <div class="col-md-6">
                <div class="f-12 text-muted mb-1"><?= __('Asset') ?></div>
                <div class="fw-semibold"><?php echo $assetLabel; ?></div>
            </div>
            <div class="col-md-6">
                <div class="f-12 text-muted mb-1"><?= __('Planned') ?></div>
                <div><?php echo $plannedDisplay; ?></div>
            </div>
            <div class="col-md-6">
                <div class="f-12 text-muted mb-1"><?= __('Technician Completed') ?></div>
                <div><?php echo $techCompleted; ?></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="fm_confirmation_remarks" class="form-label"><?= __('Remarks') ?> <span class="text-danger">*</span></label>
            <textarea class="form-control" name="fm_confirmation_remarks" id="fm_confirmation_remarks" rows="3" placeholder="<?= __('Enter confirmation remarks') ?>" aria-label="<?= __('Remarks') ?>" required></textarea>
        </div>
        <div class="mb-0">
            <label for="fm_confirmation_file" class="form-label"><?= __('Job Done Document') ?> <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="fm_confirmation_file" id="fm_confirmation_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xls,.xlsx" aria-label="<?= __('Job Done Document') ?>" required>
            <small class="text-muted f-12"><?= __('PDF, Word, Excel, or image files') ?></small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary contract-amc-fm-confirm-close-btn" data-bs-dismiss="modal"><?= __('Cancel') ?></button>
        <button type="button" class="btn btn-primary" id="submitContractAmcFmConfirmBtn">
            <i class="ti-check me-1" aria-hidden="true"></i> <?= __('Submit Confirmation') ?>
        </button>
    </div>
</form>
<script>
(function() {
    $("#submitContractAmcFmConfirmBtn").off("click").on("click", function() {
        var $form = $("#contractAmcFmConfirmForm");
        var remarks = $.trim($form.find('[name="fm_confirmation_remarks"]').val());
        var fileInput = $form.find('[name="fm_confirmation_file"]')[0];
        if (remarks == '') {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', <?= json_encode(__('Remarks are required')) ?>, '');
            return;
        }
        if (!fileInput || !fileInput.files || fileInput.files.length == 0) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', <?= json_encode(__('Job done document is required')) ?>, '');
            return;
        }
        var formData = new FormData($form[0]);
        formData.append('page', 'confirmcontractamcfmvisit');
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (r.status == 'success') {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', r.message || <?= json_encode(__('Confirmed')) ?>, '');
                    $("#kt_modal_contract_amc_fm_confirm").modal('hide');
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || <?= json_encode(__('Confirmation failed')) ?>, '');
                }
            }
        });
    });
    $(".contract-amc-fm-confirm-close-btn").off("click").on("click", function() {
        $("#kt_modal_contract_amc_fm_confirm").modal('hide');
    });
})();
</script>
<?php } ?>
