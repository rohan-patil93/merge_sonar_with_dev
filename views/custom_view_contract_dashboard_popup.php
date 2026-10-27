<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$contract_ids = json_decode(urldecode($_POST['contract_ids']),1);
$url = API . "?mode=getdashboardreact&method=get&admin_type=".$_SESSION['admin_type']."&LOCID=".$_POST['LOCID'];
$response = callAPI($url);
$data = json_decode($response, true);
$contracts = $data['allcontracts'] ?? [];
$allstatus = $data['allstatus'] ?? [];
$showcontracts = array();
foreach ($contracts as $contract) {
    if(in_array($contract['id'], $contract_ids)){
        $showcontracts[] = $contract;
    }
}

function isExpired($dateString)
{
    if (!$dateString)
        { return false;
}
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $endDate = new DateTime($dateString);
    return $endDate < $today;
}
?>
<div class="modal-header">
    <h4 class="modal-title">Contracts</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="contractTable">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Contract</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Contract ID</th>
            </tr>

            <?php if (!empty($showcontracts)): ?>
                <?php foreach ($showcontracts as $index => $contract): ?>
                    <?php
                    $expired = isExpired($contract['end_date']);
                    $statusMatch = null;
                    foreach ($allstatus as $status) {
                        if ($status['name'] === $contract['contract_status']) {
                            $statusMatch = $status;
                            break;
                        }
                    }
                    $background = $statusMatch['background_color'] ?? '#28a745';
                    $color = $statusMatch['color'] ?? '#fff';
                    ?>
                    <tr>
                        <td><?= ($currentPage - 1) * $itemsPerPage + $index + 1 ?></td>
                        <td><a class="text-primary" href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo deocdeId("id:".$contract['id']);?>" ><?= htmlspecialchars($contract['contract_name']." (".$contract['contract_type'].")") ?></a>
                        </td>
                        <td><?= fromDate($contract['start_date']) ?></td>
                        <td>
                            <?= fromDate($contract['end_date']) ?>
                            <?php if ($expired): ?>
                                <span class="form-label label label-danger">Expired</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($contract['transaction_no'] ?? 'Not Provided') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No contracts found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
