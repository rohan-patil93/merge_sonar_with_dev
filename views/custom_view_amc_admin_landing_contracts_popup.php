<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$purpose = $_GET['purpose'] ?? 'edit';
$title = $_GET['title'] ?? 'Contracts';
$locId = $_GET['LOCID'] ?? '';
$sort = $_GET['sort'] ?? '';

if ($purpose == 'assign' || $purpose == 'schedules') {
    $type = 'amc';
    if ($purpose == 'assign' && $sort == '') {
        $sort = 'assignment';
    }
} else {
    $type = $_GET['type'] ?? 'total';
}

$url = API . "?mode=getamcadminlandingcontractspopup&method=get"
    . "&admin_type=" . $_SESSION['admin_type']
    . "&LOCID=" . urlencode($locId)
    . "&type=" . urlencode($type);
if ($sort != '') {
    $url .= "&sort=" . urlencode($sort);
}
$response = json_decode(callAPI($url), true);
$contracts = $response['contracts'] ?? array();

$showAssignment = ($purpose == 'assign');
$colspan = $showAssignment ? 9 : 7;
?>

<div class="modal-header">
    <h5 class="modal-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="card table-card mb-0">
        <div class="card-block custom-scroll" style="height: 400px;">
            <div class="table-responsive">
                <table class="table table-bordered table-sm f-12 m-b-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Contract No</th>
                            <th scope="col">Contract Name</th>
                            <th scope="col">Party Type</th>
                            <th scope="col">Status</th>
                            <?php if ($showAssignment) { ?>
                            <th scope="col">Assignment</th>
                            <th scope="col">Assignee</th>
                            <?php } ?>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$contracts) > 0) {
                            $serial = 1;
                            foreach ($contracts as $contract) {
                                $contractId = (int) ($contract['id'] ?? 0);
                                if ($purpose == 'assign') {
                                    $targetUrl = 'index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=' . $contractId;
                                } elseif ($purpose == 'schedules') {
                                    $targetUrl = 'index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=' . $contractId;
                                } else {
                                    $targetUrl = 'index.php?option=COM_INSERT_CONTRACTS&Rid=' . deocdeId('id:' . $contractId);
                                }
                                $transactionNo = htmlspecialchars($contract['transaction_no'] ?? '-', ENT_QUOTES, 'UTF-8');
                                $contractName = htmlspecialchars($contract['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8');
                                $partyType = htmlspecialchars($contract['party_type'] ?? '-', ENT_QUOTES, 'UTF-8');
                                $statusLabel = htmlspecialchars($contract['status_label'] ?? '-', ENT_QUOTES, 'UTF-8');
                                $statusClass = htmlspecialchars($contract['status_class'] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8');
                                $assignmentLabel = htmlspecialchars($contract['assignment_label'] ?? '-', ENT_QUOTES, 'UTF-8');
                                $assignmentClass = htmlspecialchars($contract['assignment_class'] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8');
                                $assigneeLabel = htmlspecialchars($contract['assignee_label'] ?? '—', ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><a class="text-primary" href="<?php echo $targetUrl; ?>"><?php echo $transactionNo != '' ? $transactionNo : '-'; ?></a></td>
                                    <td><?php echo $contractName; ?></td>
                                    <td><?php echo $partyType; ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                                    <?php if ($showAssignment) { ?>
                                    <td><span class="badge <?php echo $assignmentClass; ?>"><?php echo $assignmentLabel; ?></span></td>
                                    <td><?php echo $assigneeLabel; ?></td>
                                    <?php } ?>
                                    <td><?php echo !empty($contract['start_date']) ? fromDate($contract['start_date']) : '-'; ?></td>
                                    <td><?php echo !empty($contract['end_date']) ? fromDate($contract['end_date']) : '-'; ?></td>
                                </tr>
                                <?php
                                $serial++;
                            }
                        } else { ?>
                            <tr>
                                <td colspan="<?php echo (int) $colspan; ?>" class="text-center text-muted">No contracts found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
</div>
