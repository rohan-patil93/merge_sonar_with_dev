<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$transaction_approval_id = $_POST['transaction_approval_id'] ?? 0;
$doa_id = $_POST['doa_id'] ?? 0;
$status = $_POST['status'] ?? '';
$type = $_POST['type'] ?? '';

$url = API . "?mode=getexitemployeeapprovalpopupdata&transaction_approval_id={$transaction_approval_id
}";
$response = callAPI($url);
$result = json_decode($response, true);

$data = $result['data'] ?? null;
?>

<form method="POST" class="modal-content">
    <input type="hidden" name="page" value="updateApprovalStatus">
    <input type="hidden" name="tranaction_approval_id" value="<?php echo $transaction_approval_id; ?>">
    <input type="hidden" name="doa_id" value="<?php echo $doa_id; ?>">
    <input type="hidden" name="response_type" value="<?php echo $status; ?>">

    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <?php
                if($status == 199){
                     echo 'Approve Request';
                }elseif($status == 201){
                    echo 'Reject Request';
                }else{
                    echo $data['emp_code'] ?? '';
                }
            ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <?php if (!empty($data)) { ?>
            <div class="p-2 mb-3 border rounded bg-light">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Name:</strong> <?php echo htmlspecialchars($data['display_name']); ?><br>
                        <small class="text-muted"><?php echo $data['emp_code'] ?? ''; ?></small>
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> <?php echo htmlspecialchars($data['termination_type'] ?? '-'); ?>
                    </div>

                </div>

                <div class="row mb-2">

                    <div class="col-md-6">
                        <strong>Email:</strong> <?php echo htmlspecialchars($data['email_id']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Resign Date:</strong>
                        <?php echo !empty($data['resignation_date']) ? date('d-m-Y', strtotime($data['resignation_date'])) : '-'; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Last Working:</strong>
                        <?php echo !empty($data['leaving_date']) ? date('d-m-Y', strtotime($data['leaving_date'])) : '-'; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Remarks by Employee:</strong><br>
                        <span class="text-muted">
                            <?php echo !empty($data['remarks_by_employee']) ? nl2br(htmlspecialchars($data['remarks_by_employee'])) : '-'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (!empty($data) && ($status == 199 || $status == 201)) { ?>
            <div class="mb-3">
                <label for="comments" class="form-label fw-bold">Comments</label>
                <textarea name="comments" id="comments" class="form-control" rows="4" placeholder="Enter your comments..." required></textarea>
            </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-warning mb-0">No record found for this approval request.</div>
        <?php } ?>
    </div>

    <?php if (!empty($data) && ($status == 199 || $status == 201)) { ?>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-<?php echo ($status == 199) ? 'success' : 'danger'; ?>">
                <?php echo ($status == 199) ? 'Approve' : 'Reject'; ?>
            </button>
        </div>
    <?php } ?>
</form>
