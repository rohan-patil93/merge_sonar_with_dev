<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

function csrTrackingManageDecodeRid($ridParam)
{
    if ($ridParam == '') {
        return 0;
    }
    $rid = encodeId($ridParam);
    $rid = urldecode($rid);
    $ridParts = explode(':', $rid);
    return (int)($ridParts[1] ?? 0);
}

function csrTrackingManageEncodeRid($proposalId)
{
    $proposalId = (int)$proposalId;
    if ($proposalId <= 0) {
        return '';
    }
    return deocdeId('id:' . $proposalId);
}

function csrTrackingManageText($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$company_id = $_SESSION['company_id'] ?? 0;
$user_id = $_SESSION['userid'] ?? '';
$proposal_id = isset($_POST['proposal_id']) ? (int)$_POST['proposal_id'] : 0;
$encodedRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : '';

if ($proposal_id <= 0 && $encodedRid != '') {
    $proposal_id = csrTrackingManageDecodeRid($encodedRid);
}
if ($encodedRid == '' && $proposal_id > 0) {
    $encodedRid = csrTrackingManageEncodeRid($proposal_id);
}

$approved_status = defined('ST_APPROVED') ? (int)ST_APPROVED : 199;
$proposal = array();
$tracking_rows = array();
$errorMessage = '';

if ($proposal_id > 0) {
    $url = API . "?mode=getCsrProjectTrackingManage&method=get"
        . "&company_id=" . urlencode((string)$company_id)
        . "&proposal_id=" . urlencode((string)$proposal_id);
    $response = json_decode(callAPI($url), true);
    if (($response['status'] ?? '') == 'success') {
        $proposal = $response['proposal'] ?? array();
        $tracking_rows = $response['tracking_rows'] ?? array();
        $approved_status = (int)($response['approved_status'] ?? $approved_status);
    } else {
        $errorMessage = $response['message'] ?? 'Unable to load tracking details';
    }
} else {
    $errorMessage = 'Invalid project details';
}

$projectTitle = is_array($proposal) ? ($proposal['subject'] ?? 'Project Tracking') : ($proposal->subject ?? 'Project Tracking');
$rowCounter = 0;
?>

<div class="modal-header">
    <h5 class="modal-title"><?php echo csrTrackingManageText($projectTitle); ?> — Tracking</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php if ($errorMessage != '') { ?>
        <div class="alert alert-danger mb-0"><?php echo csrTrackingManageText($errorMessage); ?></div>
    <?php } else { ?>
        <form method="post" action="" id="csr_tracking_manage_form">
            <input type="hidden" name="submit" value="save_tracking">
            <input type="hidden" name="proposal_id" value="<?php echo (int)$proposal_id; ?>">
            <input type="hidden" name="Rid" value="<?php echo csrTrackingManageText($encodedRid); ?>">

            <div class="table-responsive">
                <table class="table table-bordered" id="csr_tracking_manage_table">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Tracking Name</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$tracking_rows) > 0) {
                            foreach ($tracking_rows as $tracking) {
                                $rowCounter++;
                                $trackingRow = is_array($tracking) ? $tracking : (array)$tracking;
                                $rowId = (int)($trackingRow['id'] ?? 0);
                                $currentStatus = (int)($trackingRow['current_status'] ?? 0);
                                $isApproved = ($currentStatus == $approved_status);
                                $disabledAttr = $isApproved ? 'disabled' : '';
                                $readonlyClass = $isApproved ? 'bg-light' : '';
                        ?>
                        <tr class="<?php echo $readonlyClass; ?>">
                            <td><?php echo $rowCounter; ?></td>
                            <td>
                                <input type="hidden" name="tracking_id[<?php echo $rowCounter; ?>]" value="<?php echo $rowId; ?>">
                                <input type="text" class="form-control" name="tracking_name[<?php echo $rowCounter; ?>]" value="<?php echo csrTrackingManageText($trackingRow['tracking_name'] ?? ''); ?>" <?php echo $disabledAttr; ?>>
                            </td>
                            <td>
                                <textarea class="form-control" name="description[<?php echo $rowCounter; ?>]" <?php echo $disabledAttr; ?>><?php echo csrTrackingManageText($trackingRow['description'] ?? ''); ?></textarea>
                            </td>
                            <td>
                                <input type="date" class="form-control csr-track-start-date" data-row="<?php echo $rowCounter; ?>" name="start_date[<?php echo $rowCounter; ?>]" value="<?php echo csrTrackingManageText($trackingRow['start_date'] ?? ''); ?>" <?php echo $disabledAttr; ?>>
                            </td>
                            <td>
                                <input type="date" class="form-control" id="csr_track_end_date_<?php echo $rowCounter; ?>" name="end_date[<?php echo $rowCounter; ?>]" value="<?php echo csrTrackingManageText($trackingRow['end_date'] ?? ''); ?>" <?php echo $disabledAttr; ?>>
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control text_align_right" name="tracking_budget[<?php echo $rowCounter; ?>]" value="<?php echo csrTrackingManageText($trackingRow['tracking_budget'] ?? ''); ?>" <?php echo $disabledAttr; ?>>
                            </td>
                        </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-primary mt-2" id="csr_add_tracking_row">Add More</button>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    <?php } ?>
</div>

<script>
(function () {
    var rowCounter = <?php echo (int)$rowCounter; ?>;

    function updateEndDateMin(rowNumber) {
        var startInput = document.querySelector('.csr-track-start-date[data-row="' + rowNumber + '"]');
        var endInput = document.getElementById('csr_track_end_date_' + rowNumber);
        if (startInput && endInput && startInput.value) {
            endInput.min = startInput.value;
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('csr-track-start-date')) {
            updateEndDateMin(e.target.getAttribute('data-row'));
        }
    });

    var addBtn = document.getElementById('csr_add_tracking_row');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            rowCounter++;
            var tableBody = document.querySelector('#csr_tracking_manage_table tbody');
            if (!tableBody) {
                return;
            }
            var newRow = document.createElement('tr');
            newRow.innerHTML =
                '<td>' + rowCounter + '</td>' +
                '<td><input type="hidden" name="tracking_id[' + rowCounter + ']" value="">' +
                '<input type="text" class="form-control" name="tracking_name[' + rowCounter + ']" value=""></td>' +
                '<td><textarea class="form-control" name="description[' + rowCounter + ']"></textarea></td>' +
                '<td><input type="date" class="form-control csr-track-start-date" data-row="' + rowCounter + '" name="start_date[' + rowCounter + ']" value=""></td>' +
                '<td><input type="date" class="form-control" id="csr_track_end_date_' + rowCounter + '" name="end_date[' + rowCounter + ']" value=""></td>' +
                '<td><input type="number" min="0" class="form-control text_align_right" name="tracking_budget[' + rowCounter + ']" value=""></td>';
            tableBody.appendChild(newRow);
        });
    }
})();
</script>
