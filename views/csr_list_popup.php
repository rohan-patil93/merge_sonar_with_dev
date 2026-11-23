<?php
defined('KEY_CURRENT_STATUS') || define('KEY_CURRENT_STATUS', 'current_status');
defined('STATUS_PROGRESS') || define('STATUS_PROGRESS', 'progress');
defined('STATUS_APPROVED') || define('STATUS_APPROVED', 'approved');
defined('STATUS_TRACKING') || define('STATUS_TRACKING', 'tracking');
defined('STATUS_COMPLETED') || define('STATUS_COMPLETED', 'completed');
$title = $_POST['title'] ?? 'CSR List';
$count = $_POST['count'] ?? 0;
$status = $_POST['status'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$currentYear = $_POST['year'] ?? '';
$current_status = $_POST[KEY_CURRENT_STATUS] ?? '';

include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if($current_status != ''){
    $table_name = 'csr_proposal_data';
    $url = API . "?mode=getcsrdata&method=get&table=".$table_name."&id=".$user_id."&current_status=".$current_status;
    $csr_response = callAPI($url);
    $response = json_decode($csr_response, 1);
    if ($status === 'total') {
        $filtered = $response['total_records'] ?? array();
    } elseif ($status === 'active') {
        $filtered = $response['pending_approvals'] ?? array();
    } elseif ($status === STATUS_PROGRESS) {
        $filtered = $response['approval_inprogress'] ?? array();
    } elseif ($status === STATUS_APPROVED) {
        $filtered = $response[STATUS_APPROVED] ?? array();
    } elseif ($status === STATUS_TRACKING) {
        $filtered = $response['tracking_inprogress'] ?? array();
    } elseif ($status === STATUS_COMPLETED) {
        $filtered = $response['tracking_completed'] ?? array();
    }

}else{

    if (isset($user_id) && $user_id != '') {
        $url = API . "?mode=getcsrProposalData&orgininaor=" . $user_id;
    } else {
        $url = API . "?mode=getAdminDashBordData&method=get";
    }

    $response = callAPI($url);
    $data = json_decode($response, true)['csr_proposal_arr'] ?? [];
    $filtered = array();
    foreach ($data as $row) {
        $year = date('Y', strtotime($row['project_start_date'] ?? ''));
        if ($currentYear == '') {
            if ($status === 'total') {
                $filtered[] = $row;
            } elseif ($status === 'active' && $row[KEY_CURRENT_STATUS] == 0) {
                $filtered[] = $row;
            } elseif ($status === STATUS_PROGRESS && $row[KEY_CURRENT_STATUS] > 0 && $row[KEY_CURRENT_STATUS] < 199) {
                $filtered[] = $row;
            } elseif ($status === STATUS_APPROVED && $row[KEY_CURRENT_STATUS] == 199) {
                $filtered[] = $row;
            } elseif ($status === STATUS_TRACKING && $row[KEY_CURRENT_STATUS] == 199 && $row['tracking_status'] == 0) {
                $filtered[] = $row;
            } elseif ($status === STATUS_COMPLETED && $row[KEY_CURRENT_STATUS] == 199 && $row['tracking_status'] == 1) {
                $filtered[] = $row;
            }
        }
        if (isset($currentYear) && $currentYear != '' && $year == $currentYear) {

            if ($status === 'total') {
                $filtered[] = $row;
            } elseif ($status === 'active' && $row[KEY_CURRENT_STATUS] == 0) {
                $filtered[] = $row;
            } elseif ($status === STATUS_PROGRESS && $row[KEY_CURRENT_STATUS] > 0 && $row[KEY_CURRENT_STATUS] < 199) {
                $filtered[] = $row;
            } elseif ($status === STATUS_APPROVED && $row[KEY_CURRENT_STATUS] == 199) {
                $filtered[] = $row;
            } elseif ($status === STATUS_TRACKING && $row[KEY_CURRENT_STATUS] == 199 && $row['tracking_status'] == 0) {
                $filtered[] = $row;
            } elseif ($status === STATUS_COMPLETED && $row[KEY_CURRENT_STATUS] == 199 && $row['tracking_status'] == 1) {
                $filtered[] = $row;
            }
        }
    }
}


?>
<!-- Display table similar to the recent CSRs table... -->
<div class="modal-header">
    <h5 class="modal-title">
        <?php echo htmlspecialchars(__($title), ENT_QUOTES, 'UTF-8'); ?> <?= __('CSRs') ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8') ?>"></button>
</div>
<div class="modal-body">

    <div class="card table-card">

        <div class="card-block custom-scroll" style="height: 350px;">
            <div class="table-responsive">
                <table class="table table-hover m-b-0">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Sr. No') ?></th>
                            <th scope="col"><?= __('Creator') ?></th>
                            <th scope="col"><?= __('Project Name') ?></th>
                            <th scope="col"><?= __('Start Date') ?></th>
                            <th scope="col"><?= __('End Date') ?></th>
                            <th scope="col" class='text-center'><?= __('Cost') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count((array)$filtered) > 0) {
                            $i = 1;
                            foreach ($filtered as $value) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <?php if ($_SESSION['user_image'][$value['created_by']] != "") { ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$value['created_by']]; ?>" class="img-radius img-40 align-top" data-onerror-src="uploads/all/no-image.jpg" alt="" >
                                        <?php } else { ?>
                                            <span alt="<?= htmlspecialchars(__('user image'), ENT_QUOTES, 'UTF-8') ?>" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$value['created_by']]); ?></span>
                                        <?php } ?>
                                        <span class='mt-2 ml-2 d-inline-block fw-bold' style="margin-bottom:5px"> <?php echo $_SESSION['users'][$value['created_by']]; ?> </span>
                                    </td>
                                    <td>
                                        <a class='text-primary m-r-5'
                                            href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $value['id']) ?>'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'
                                            data-bs-html='true'
                                            title='<?= htmlspecialchars($value['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                            <?= htmlspecialchars(substr($value['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($value['subject']) > 40 ? '...' : '') ?>
                                        </a>
                                    </td>
                                    <td><?php echo  fromDate($value['project_start_date']) ?? ''; ?> </td>
                                    <td><?php echo fromDate($value['project_end_date']) ?? ''; ?> </td>

                                    <td class='text-end'><?php echo customeummarmonyformate($value['total_budget_grand_total']);  ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6">
                                    <div class="text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Records') ?></p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;" >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
