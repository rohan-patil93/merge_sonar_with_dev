<?php

include('../includes/session.php');

include('../dbconfig.php');

include('../comm.php');



$schedule_id = (int) ($_POST['schedule_id'] ?? 0);

$url = API . "?mode=getcontractamcscheduledetails&method=get&schedule_id=" . $schedule_id;

$res = json_decode(callAPI($url), true);

$schedule = $res['schedule'] ?? array();

$technicians = $res['technicians'] ?? array();

$employees = $res['employees'] ?? array();

$serviceMode = resolveContractAmcServiceMode(array('service_mode' => $res['service_mode'] ?? '', 'party_type' => $schedule['party_type'] ?? ''));

$isInhouse = ($serviceMode == 'inhouse');

$assigneeLabel = $isInhouse ? 'Employee' : 'Technician';

$checklist_items = $res['checklist_items'] ?? array();

$check_list_status = $res['check_list_status'] ?? array();

$canExecute = !empty($res['can_execute']);

$contractIsApproved = !empty($res['contract_is_approved']);

$contractIsCancelled = (!empty($schedule['cancel_status']) && $schedule['cancel_status'] == 'Y')

    || ((int) ($schedule['current_status'] ?? 0) == (int) ST_CANCEL);

$currentStatus = (int) ($schedule['visit_status'] ?? $schedule['current_status'] ?? 0);

$isViewOnly = !$canExecute;

$statusLabel = htmlspecialchars($schedule['status_label'] ?? 'Pending', ENT_QUOTES, 'UTF-8');

$statusClass = htmlspecialchars($schedule['status_class'] ?? 'bg-primary', ENT_QUOTES, 'UTF-8');

$plannedDisplay = htmlspecialchars($schedule['planned_display'] ?? '—', ENT_QUOTES, 'UTF-8');

$technicianName = htmlspecialchars($schedule['technician_name'] ?? '—', ENT_QUOTES, 'UTF-8');

$effectiveTechnicianId = (int) ($schedule['effective_technician_id'] ?? $schedule['technician_id'] ?? 0);

$assetDefaultTechnicianId = (int) ($schedule['asset_default_technician_id'] ?? 0);

$technicianIsOverride = !empty($schedule['technician_is_override']);

$effectiveTechnicianName = htmlspecialchars($schedule['effective_technician_name'] ?? $technicianName, ENT_QUOTES, 'UTF-8');

$effectiveEmployeeId = (int) ($schedule['effective_employee_id'] ?? $schedule['employee_id'] ?? 0);

$assetDefaultEmployeeId = (int) ($schedule['asset_default_employee_id'] ?? 0);

$employeeIsOverride = !empty($schedule['employee_is_override']);

$effectiveEmployeeName = htmlspecialchars($schedule['effective_employee_name'] ?? '', ENT_QUOTES, 'UTF-8');

$contractName = htmlspecialchars($schedule['contract_name'] ?? '', ENT_QUOTES, 'UTF-8');

$actualStartDisplay = !empty($schedule['actual_start_datetime'])

    ? htmlspecialchars(formatAmcScheduleDateTime($schedule['actual_start_datetime']), ENT_QUOTES, 'UTF-8')

    : '—';

$actualEndDisplay = !empty($schedule['actual_end_datetime'])

    ? htmlspecialchars(formatAmcScheduleDateTime($schedule['actual_end_datetime']), ENT_QUOTES, 'UTF-8')

    : '—';

$checklistDoneCount = 0;

$checklistTotal = count((array) $checklist_items);

if ($checklistTotal > 0) {

    foreach ($checklist_items as $checkKey => $checkName) {

        $saved = $check_list_status[$checkKey] ?? array();

        $savedStatus = $saved['check_status'] ?? '0';

        if ($savedStatus == '1' || $savedStatus == 'Y') {

            $checklistDoneCount++;

        }

    }

}

?>

<style>

#kt_modal_contract_amc_schedule .contract-amc-visit-modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: #fff;
    border-bottom: 0;
    padding: 1rem 1.25rem;
    flex-shrink: 0;
}

#kt_modal_contract_amc_schedule .contract-amc-visit-modal-header .modal-title {

    font-weight: 600;

    font-size: 1.05rem;

}

#kt_modal_contract_amc_schedule .contract-amc-visit-modal-header .contract-amc-visit-subtitle {

    color: rgba(255, 255, 255, 0.82);

    font-size: 0.82rem;

}

#kt_modal_contract_amc_schedule .modal-content {
    max-height: calc(100vh - 2rem);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

#kt_modal_contract_amc_schedule #contractAmcScheduleForm {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
}

#kt_modal_contract_amc_schedule .contract-amc-visit-modal-body {
    padding: 1.25rem;
    background: #f8f9fb;
    flex: 1 1 auto;
    min-height: 0;
    max-height: min(500px, calc(100vh - 220px));
    overflow-y: auto;
}

#kt_modal_contract_amc_schedule .contract-amc-visit-alert {

    display: flex;

    align-items: flex-start;

    gap: 0.65rem;

    border-radius: 10px;

    padding: 0.75rem 0.9rem;

    margin-bottom: 1rem;

    font-size: 0.88rem;

    line-height: 1.45;

}

#kt_modal_contract_amc_schedule .contract-amc-visit-alert i {

    font-size: 1.1rem;

    margin-top: 0.1rem;

}

#kt_modal_contract_amc_schedule .contract-amc-visit-alert-danger {

    background: #fff5f5;

    border: 1px solid #f5c2c7;

    color: #842029;

}

#kt_modal_contract_amc_schedule .contract-amc-visit-alert-warning {

    background: #fff8e6;

    border: 1px solid #ffecb5;

    color: #664d03;

}

#kt_modal_contract_amc_schedule .contract-amc-info-card {

    background: #fff;

    border: 1px solid #e9ecef;

    border-radius: 10px;

    padding: 0.85rem 1rem;

    height: 100%;

    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);

}

#kt_modal_contract_amc_schedule .contract-amc-info-label {

    display: block;

    font-size: 0.72rem;

    font-weight: 600;

    letter-spacing: 0.04em;

    text-transform: uppercase;

    color: #6c757d;

    margin-bottom: 0.35rem;

}

#kt_modal_contract_amc_schedule .contract-amc-info-value {

    display: block;

    font-size: 0.95rem;

    font-weight: 600;

    color: #212529;

    word-break: break-word;

}

#kt_modal_contract_amc_schedule .contract-amc-section-title {

    font-size: 0.92rem;

    font-weight: 600;

    color: #343a40;

    margin-bottom: 0.75rem;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-panel {

    background: #fff;

    border: 1px solid #e9ecef;

    border-radius: 10px;

    padding: 1rem;

    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-table {

    margin-bottom: 0;

    font-size: 0.88rem;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-table thead th {

    background: #f1f3f5;

    border-color: #e9ecef;

    font-size: 0.78rem;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.03em;

    color: #495057;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-table tbody td {

    vertical-align: middle;

    border-color: #eef1f4;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-item-name {

    font-weight: 500;

    color: #212529;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-status {

    display: inline-flex;

    align-items: center;

    gap: 0.35rem;

    font-size: 0.82rem;

    font-weight: 600;

    padding: 0.2rem 0.55rem;

    border-radius: 999px;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-status.done {

    background: #d1e7dd;

    color: #0f5132;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-status.pending {

    background: #e9ecef;

    color: #495057;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-remarks {

    color: #495057;

    font-size: 0.86rem;

}

#kt_modal_contract_amc_schedule .contract-amc-checklist-remarks.empty {

    color: #adb5bd;

    font-style: italic;

}

#kt_modal_contract_amc_schedule .contract-amc-tech-panel {

    background: #fff;

    border: 1px solid #e9ecef;

    border-radius: 10px;

    padding: 1rem;

    margin-top: 1rem;

}

#kt_modal_contract_amc_schedule .contract-amc-visit-modal-footer {
    background: #fff;
    border-top: 1px solid #e9ecef;
    padding: 0.85rem 1.25rem;
    flex-shrink: 0;
}

#kt_modal_contract_amc_schedule .contract-amc-checklist-progress {

    font-size: 0.78rem;

    color: #6c757d;

}

</style>

<div class="modal-header contract-amc-visit-modal-header">

    <div>

        <h5 class="modal-title mb-1">

            <i class="ti-<?php echo $isViewOnly ? 'eye' : 'settings'; ?> me-1"></i>

            <?php echo $isViewOnly ? 'View Visit' : 'Manage Visit'; ?>

        </h5>

        <?php if ($contractName != '') { ?>

            <div class="contract-amc-visit-subtitle"><?php echo $contractName; ?></div>

        <?php } ?>

    </div>

    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

<form id="contractAmcScheduleForm">

    <div class="modal-body contract-amc-visit-modal-body custom-scroll">

        <?php if ($contractIsCancelled) { ?>

        <div class="contract-amc-visit-alert contract-amc-visit-alert-danger" role="alert">

            <i class="ti-na"></i>

            <div>This contract has been cancelled. Service visits cannot be started or updated. This visit is view-only.</div>

        </div>

        <?php } elseif (!$contractIsApproved) { ?>

        <div class="contract-amc-visit-alert contract-amc-visit-alert-warning" role="alert">

            <i class="ti-info-alt"></i>

            <div>Contract must be approved before service visits can be started or updated. This visit is view-only.</div>

        </div>

        <?php } ?>



        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">



        <div class="row g-3">

            <div class="col-sm-6 col-lg-3">

                <div class="contract-amc-info-card">

                    <span class="contract-amc-info-label">Planned</span>

                    <span class="contract-amc-info-value"><?php echo $plannedDisplay; ?></span>

                </div>

            </div>

            <div class="col-sm-6 col-lg-3">

                <div class="contract-amc-info-card">

                    <span class="contract-amc-info-label">Status</span>

                    <span class="contract-amc-info-value">

                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>

                    </span>

                </div>

            </div>

            <div class="col-sm-6 col-lg-3">

                <div class="contract-amc-info-card">

                    <span class="contract-amc-info-label">Actual Start</span>

                    <span class="contract-amc-info-value"><?php echo $actualStartDisplay; ?></span>

                </div>

            </div>

            <div class="col-sm-6 col-lg-3">

                <div class="contract-amc-info-card">

                    <span class="contract-amc-info-label">Actual End</span>

                    <span class="contract-amc-info-value"><?php echo $actualEndDisplay; ?></span>

                </div>

            </div>

        </div>



        <div class="contract-amc-tech-panel">

            <label class="contract-amc-info-label mb-2"><?php echo $assigneeLabel; ?></label>

            <?php if ($canExecute) { ?>

            <?php if ($isInhouse) { ?>

            <select name="employee_id" class="form-control js-example-basic-single contract-amc-assignee-select" data-schedule-id="<?php echo $schedule_id; ?>" data-assignee-type="employee">

                <option value="">Select employee</option>

                <?php if (count((array)$employees) > 0) {

                    foreach ($employees as $eid => $ename) { ?>

                <option value="<?php echo (int) $eid; ?>" <?php echo ($effectiveEmployeeId == (int) $eid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($ename, ENT_QUOTES, 'UTF-8'); ?></option>

                <?php }

                } ?>

            </select>

            <?php if (!$employeeIsOverride && $assetDefaultEmployeeId > 0 && $effectiveEmployeeId == $assetDefaultEmployeeId) { ?>

            <small class="text-muted d-block mt-1">Default for this asset</small>

            <?php } ?>

            <?php if ($employeeIsOverride) { ?>

            <div class="mt-2">

                <button type="button" class="btn btn-outline-secondary btn-sm contract-amc-reset-assignee-btn" data-schedule-id="<?php echo $schedule_id; ?>">

                    <i class="ti-reload me-1"></i> Reset to asset default

                </button>

            </div>

            <?php } ?>

            <?php } else { ?>

            <select name="technician_id" class="form-control js-example-basic-single contract-amc-assignee-select" data-schedule-id="<?php echo $schedule_id; ?>" data-assignee-type="technician">

                <option value="">Select technician</option>

                <?php if (count((array)$technicians) > 0) {

                    foreach ($technicians as $tid => $tname) { ?>

                <option value="<?php echo (int) $tid; ?>" <?php echo ($effectiveTechnicianId == (int) $tid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($tname, ENT_QUOTES, 'UTF-8'); ?></option>

                <?php }

                } ?>

            </select>

            <?php if (!$technicianIsOverride && $assetDefaultTechnicianId > 0 && $effectiveTechnicianId == $assetDefaultTechnicianId) { ?>

            <small class="text-muted d-block mt-1">Default for this asset</small>

            <?php } ?>

            <?php if ($technicianIsOverride) { ?>

            <div class="mt-2">

                <button type="button" class="btn btn-outline-secondary btn-sm contract-amc-reset-tech-btn" data-schedule-id="<?php echo $schedule_id; ?>">

                    <i class="ti-reload me-1"></i> Reset to asset default

                </button>

            </div>

            <?php } ?>

            <?php } ?>

            <?php } else { ?>

            <div class="contract-amc-info-value">

                <?php if ($isInhouse) { ?>

                <?php echo ($effectiveEmployeeName != '' && $effectiveEmployeeName != '—') ? $effectiveEmployeeName : 'Unassigned'; ?>

                <?php if ($employeeIsOverride) { ?>

                <span class="badge bg-warning text-dark ms-1">override</span>

                <?php } ?>

                <?php } else { ?>

                <?php echo ($effectiveTechnicianName != '' && $effectiveTechnicianName != '—') ? $effectiveTechnicianName : 'Unassigned'; ?>

                <?php if ($technicianIsOverride) { ?>

                <span class="badge bg-warning text-dark ms-1">override</span>

                <?php } ?>

                <?php } ?>

            </div>

            <?php } ?>

        </div>



        <?php if (count((array)$checklist_items) > 0) { ?>

        <div class="contract-amc-checklist-panel mt-3">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">

                <h6 class="contract-amc-section-title mb-0">Checklist</h6>

                <span class="contract-amc-checklist-progress">

                    <?php echo $checklistDoneCount; ?> / <?php echo $checklistTotal; ?> completed

                    <?php if ($canExecute) { ?><span class="text-danger ms-1">(required before finish)</span><?php } ?>

                </span>

            </div>

            <div class="table-responsive">

                <table class="table table-sm contract-amc-checklist-table">

                    <thead>

                        <tr>

                            <th style="width:45%;">Item</th>

                            <th style="width:18%;">Done</th>

                            <th style="width:37%;">Remarks</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php $ci = 0;

                    foreach ($checklist_items as $checkKey => $checkName) {

                        $saved = $check_list_status[$checkKey] ?? array();

                        $savedStatus = $saved['check_status'] ?? '0';

                        $savedRemarks = $saved['check_remarks'] ?? '';

                        $savedRowId = $saved['id'] ?? '';

                        $isDone = ($savedStatus == '1' || $savedStatus == 'Y');

                        ?>

                        <tr>

                            <td>

                                <span class="contract-amc-checklist-item-name"><?php echo htmlspecialchars($checkName, ENT_QUOTES, 'UTF-8'); ?></span>

                                <input type="hidden" name="checklist_items[<?php echo $ci; ?>]" value="<?php echo htmlspecialchars($checkName, ENT_QUOTES, 'UTF-8'); ?>">

                                <input type="hidden" name="checklist_keys[<?php echo $ci; ?>]" value="<?php echo htmlspecialchars($checkKey, ENT_QUOTES, 'UTF-8'); ?>">

                                <input type="hidden" name="existing_checklist_ids[<?php echo $ci; ?>]" value="<?php echo htmlspecialchars($checkKey, ENT_QUOTES, 'UTF-8'); ?>">

                                <?php if ($savedRowId != '') { ?>

                                <input type="hidden" name="saved_checklist_row_ids[<?php echo htmlspecialchars($checkKey, ENT_QUOTES, 'UTF-8'); ?>]" value="<?php echo (int) $savedRowId; ?>">

                                <?php } ?>

                            </td>

                            <td>

                                <?php if ($canExecute) { ?>

                                <div class="form-check mb-0">

                                    <input type="checkbox" class="form-check-input" name="checklist_status[<?php echo htmlspecialchars($checkKey, ENT_QUOTES, 'UTF-8'); ?>]" value="1" id="contract_amc_chk_<?php echo $ci; ?>" <?php echo $isDone ? 'checked' : ''; ?>>

                                    <label class="form-check-label f-12" for="contract_amc_chk_<?php echo $ci; ?>">Done</label>

                                </div>

                                <?php } else { ?>

                                <span class="contract-amc-checklist-status <?php echo $isDone ? 'done' : 'pending'; ?>">

                                    <i class="ti-<?php echo $isDone ? 'check' : 'minus'; ?>"></i>

                                    <?php echo $isDone ? 'Done' : 'Pending'; ?>

                                </span>

                                <?php } ?>

                            </td>

                            <td>

                                <?php if ($canExecute) { ?>

                                <input type="text" class="form-control form-control-sm" name="checklist_remarks[<?php echo $ci; ?>]" value="<?php echo htmlspecialchars($savedRemarks, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Add remarks">

                                <?php } else { ?>

                                <span class="contract-amc-checklist-remarks<?php echo ($savedRemarks == '') ? ' empty' : ''; ?>">

                                    <?php echo ($savedRemarks != '') ? htmlspecialchars($savedRemarks, ENT_QUOTES, 'UTF-8') : 'No remarks'; ?>

                                </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php $ci++;

                    } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <?php } ?>

    </div>

    <div class="modal-footer contract-amc-visit-modal-footer">

        <button type="button" class="btn btn-light border contract-amc-visit-close-btn" data-bs-dismiss="modal">Close</button>

        <?php if ($canExecute) { ?>

            <?php if ($currentStatus == 0) { ?>

            <button type="button" class="btn btn-warning contract-amc-action-btn" data-action="start">

                <i class="ti-control-play me-1"></i> Start Job

            </button>

            <?php } ?>

            <?php if ($currentStatus > 0 && $currentStatus < 199) { ?>

            <button type="button" class="btn btn-success contract-amc-action-btn" data-action="finish">

                <i class="ti-check me-1"></i> Finish Job

            </button>

            <?php } ?>

            <button type="button" class="btn btn-primary contract-amc-save-checklist-btn">

                <i class="ti-save me-1"></i> Save Checklist

            </button>

        <?php } ?>

    </div>

</form>

<script>

window.initContractAmcScheduleModalHandlers = function() {

    function postContractAmcSchedule(action) {

        var $form = $("#contractAmcScheduleForm");

        var data = $form.serializeArray();

        if (action) {

            data.push({ name: 'action', value: action });

        }

        data.push({ name: 'page', value: 'updatecontractamcscheduledetails' });

        $.ajax({

            url: 'ajax.php',

            type: 'POST',

            data: $.param(data) + '&page=updatecontractamcscheduledetails',

            success: function(resp) {

                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

                if (r.status == 'success') {

                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', r.message || 'Saved', '');

                    $("#kt_modal_contract_amc_schedule").modal('hide');

                    setTimeout(function() { window.location.reload(); }, 800);

                } else {

                    tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || 'Failed', '');

                }

            }

        });

    }

    $("#kt_modal_contract_amc_schedule .contract-amc-action-btn").off('click').on('click', function() {

        postContractAmcSchedule($(this).data('action'));

    });

    $("#kt_modal_contract_amc_schedule .contract-amc-save-checklist-btn").off('click').on('click', function() {

        postContractAmcSchedule('');

    });

    $("#kt_modal_contract_amc_schedule .contract-amc-assignee-select").off('change').on('change', function() {

        var assigneeType = $(this).data('assignee-type') || 'technician';

        var payload = {

            page: 'assigncontractamctechnician',

            schedule_id: $(this).data('schedule-id')

        };

        if (assigneeType == 'employee') {

            payload.employee_id = $(this).val();

        } else {

            payload.technician_id = $(this).val();

        }

        $.ajax({

            url: 'ajax.php',

            type: 'POST',

            data: payload,

            success: function(resp) {

                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

                if (r.status != 'success') {

                    tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || 'Assign failed', '');

                }

            }

        });

    });

    $("#kt_modal_contract_amc_schedule .contract-amc-reset-assignee-btn, #kt_modal_contract_amc_schedule .contract-amc-reset-tech-btn").off('click').on('click', function() {

        var scheduleId = $(this).data('schedule-id');

        $.ajax({

            url: 'ajax.php',

            type: 'POST',

            data: {

                page: 'assigncontractamctechnician',

                schedule_id: scheduleId,

                reset_to_default: 1

            },

            success: function(resp) {

                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

                if (r.status == 'success') {

                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', r.message || 'Reset', '');

                    $("#kt_modal_contract_amc_schedule").modal('hide');

                    setTimeout(function() { window.location.reload(); }, 800);

                } else {

                    tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', r.message || 'Reset failed', '');

                }

            }

        });

    });

    $("#kt_modal_contract_amc_schedule .contract-amc-visit-close-btn, #kt_modal_contract_amc_schedule .btn-close").off('click').on('click', function() {

        $("#kt_modal_contract_amc_schedule").modal('hide');

    });

};

window.initContractAmcScheduleModalHandlers();

</script>


