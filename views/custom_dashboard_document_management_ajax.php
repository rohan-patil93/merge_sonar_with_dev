<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$status_filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : '';
$company_id    = isset($_POST['company_id'])    ? (int)$_POST['company_id']    : (int)($_SESSION['company_id'] ?? 0);
$user_id       = isset($_POST['user_id'])       ? $_POST['user_id']            : ($_SESSION['userid'] ?? '');

$url = API . "?mode=getdocumentmanagementdashboard&method=get"
    . "&company_id="    . urlencode($company_id)
    . "&user_id="       . urlencode($user_id)
    . "&status_filter=" . urlencode($status_filter);

$apiRes = callAPI($url);
$result = json_decode($apiRes, true);
$result = is_array($result) ? $result : array();
$rows   = isset($result['rows']) ? $result['rows'] : array();

$statusList = getstatusprogresslist('document_management');
$statusMap  = array();
if (count((array)$statusList) > 0) {
    foreach ($statusList as $s) {
        $s = is_array($s) ? $s : (array)$s;
        $statusMap[(int)$s['status']] = $s;
    }
}

if (count((array)$rows) > 0) {
?>
<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Document Name</th>
                <th>Document Type</th>
                <th>Files</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array)$row;
                $rid         = deocdeId('id:' . (int)($row['id'] ?? 0));
                $docName     = htmlspecialchars($row['document_name'] ?? '-');
                $typeName    = htmlspecialchars($row['type_name']     ?? '-');
                $fileCount   = (int)($row['file_count'] ?? 0);
                $createdDate = !empty($row['created_date']) ? fromDate($row['created_date']) : '-';
                $curStatus   = (int)($row['current_status'] ?? 0);

                $statusName  = '';
                $statusBg    = '#6c757d';
                $statusColor = '#fff';
                if (isset($statusMap[$curStatus])) {
                    $statusName  = $statusMap[$curStatus]['name'] ?? '';
                }
                if ($curStatus == 0) {
                    $statusBg = '#4e73df';
                    $statusColor = '#fff';
                } elseif ($curStatus > 100 && $curStatus < 199) {
                    $statusBg = '#f6a21e';
                    $statusColor = '#fff';
                } elseif ($curStatus == 199) {
                    $statusBg = '#1cc88a';
                    $statusColor = '#fff';
                }
                if ($statusName == '') {
                    $statusName = 'Status ' . $curStatus;
                }
            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $docName; ?></td>
                <td><?php echo $typeName; ?></td>
                <td><?php echo $fileCount; ?></td>
                <td>
                    <label class="form-label label label-md"
                        style="background:<?php echo $statusBg; ?>; color:<?php echo $statusColor; ?>; border-color:<?php echo $statusBg; ?>;">
                        <?php echo htmlspecialchars($statusName); ?>
                    </label>
                </td>
                <td><?php echo htmlspecialchars($createdDate); ?></td>
                <td>
                    <a href="index.php?option=COM_INSERT_DOCUMENT_MANAGEMENT&Rid=<?php echo urlencode($rid); ?>"
                       title="View" class="btn btn-info btn-sm">
                        <i class="icofont icofont-eye-alt"></i>
                    </a>
                </td>
            </tr>
            <?php
                $i++;
            }
            ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
<div class="card-px text-center py-20 mt-2">
    <p class="text-muted f-16 fw-bold"><i class="ti-alert text-warning"></i> No Documents Found</p>
    <div class="text-center px-4">
        <img class="mw-100 " style="max-height: 400px;" alt="" src="assets/images/work.png">
    </div>
</div>
<?php } ?>
