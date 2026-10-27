<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

function csrProposalDatesDecodeRid($ridParam)
{
    if ($ridParam == '') {
        return 0;
    }
    $rid = encodeId($ridParam);
    $rid = urldecode($rid);
    $ridParts = explode(':', $rid);
    return (int)($ridParts[1] ?? 0);
}

function csrProposalDatesEncodeRid($proposalId)
{
    $proposalId = (int)$proposalId;
    if ($proposalId <= 0) {
        return '';
    }
    return deocdeId('id:' . $proposalId);
}

function csrProposalDatesText($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$company_id = $_SESSION['company_id'] ?? 0;
$user_id = $_SESSION['userid'] ?? '';
$proposal_id = isset($_POST['proposal_id']) ? (int)$_POST['proposal_id'] : 0;
$encodedRid = isset($_POST['Rid']) ? (string)$_POST['Rid'] : '';

if ($proposal_id <= 0 && $encodedRid != '') {
    $proposal_id = csrProposalDatesDecodeRid($encodedRid);
}
if ($encodedRid == '' && $proposal_id > 0) {
    $encodedRid = csrProposalDatesEncodeRid($proposal_id);
}

$proposal = array();
$errorMessage = '';

if ($proposal_id > 0) {
    $url = API . "?mode=getCsrProposalProjectDates&method=get"
        . "&company_id=" . urlencode((string)$company_id)
        . "&proposal_id=" . urlencode((string)$proposal_id);
    $response = json_decode(callAPI($url), true)?? [];;
    if (($response['status'] ?? '') == 'success') {
        $proposal = $response['proposal'] ?? array();
    } else {
        $errorMessage = $response['message'] ?? 'Unable to load project details';
    }
} else {
    $errorMessage = 'Invalid project details';
}

$projectTitle = is_array($proposal) ? ($proposal['subject'] ?? 'Project Details') : ($proposal->subject ?? 'Project Details');
$projectStartDate = is_array($proposal) ? ($proposal['project_start_date'] ?? '') : ($proposal->project_start_date ?? '');
$projectEndDate = is_array($proposal) ? ($proposal['project_end_date'] ?? '') : ($proposal->project_end_date ?? '');
?>

<div class="modal-header">
    <h5 class="modal-title"><?php echo csrProposalDatesText($projectTitle); ?> — Project Dates</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php if ($errorMessage != '') { ?>
        <div class="alert alert-danger mb-0"><?php echo csrProposalDatesText($errorMessage); ?></div>
    <?php } else { ?>
        <form method="post" action="" id="csr_proposal_dates_form">
            <input type="hidden" name="submit" value="save_project_dates">
            <input type="hidden" name="proposal_id" value="<?php echo (int)$proposal_id; ?>">
            <input type="hidden" name="Rid" value="<?php echo csrProposalDatesText($encodedRid); ?>">

            <div class="row mb-3">
                <div class="col-lg-3">
                    <label class="f-w-600" for="project_start_date">Start Date</label>
                </div>
                <div class="col-lg-9">
                    <input type="date" class="form-control" id="project_start_date" name="project_start_date" value="<?php echo csrProposalDatesText($projectStartDate); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-3">
                    <label class="f-w-600" for="project_end_date">End Date</label>
                </div>
                <div class="col-lg-9">
                    <input type="date" class="form-control" id="project_end_date" name="project_end_date" value="<?php echo csrProposalDatesText($projectEndDate); ?>">
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    <?php } ?>
</div>

<script>
(function () {
    var startInput = document.getElementById('project_start_date');
    var endInput = document.getElementById('project_end_date');
    if (startInput && endInput) {
        startInput.addEventListener('change', function () {
            if (startInput.value) {
                endInput.min = startInput.value;
            }
        });
        if (startInput.value) {
            endInput.min = startInput.value;
        }
    }
})();
</script>
