<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');

$type = $_GET['type'] ?? '';
$title = $_GET['title'] ?? 'CSR Projects';
$financial_year = $_GET['fa_year'] ?? '';
$company_id = $_SESSION['company_id'] ?? 0;
$user_id = $_SESSION['userid'] ?? '';
$working_role_short_code = $_SESSION['working_role_short_code'] ?? '';

$url = API . "?mode=getCsrDashboardPopupProjects&method=get"
    . "&company_id=" . urlencode((string)$company_id)
    . "&user_id=" . urlencode((string)$user_id)
    . "&working_role_short_code=" . urlencode((string)$working_role_short_code)
    . "&type=" . urlencode((string)$type)
    . "&financial_year=" . urlencode((string)$financial_year);
$response = callAPI($url);
$response = json_decode($response, true);
$projects = $response['projects'] ?? array();
// print_r($response['query']);
?>

<div class="modal-header">
    <h5 class="modal-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> Projects</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="card table-card mb-0">
        <div class="card-block custom-scroll" style="height: 350px;">
            <div class="table-responsive">
                <table class="table table-hover m-b-0">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Creator</th>
                            <th>Project Name</th>
                            <th class="text-end"> <?php echo $type == 'spent'?'Spent':'Cost' ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$projects) > 0) { ?>
                            <?php $i = 1; ?>
                            <?php foreach ($projects as $project) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <?php if (($_SESSION['user_image'][$project['created_by']] ?? '') != '') { ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$project['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                                        <?php } else { ?>
                                            <span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$project['created_by']] ?? ''); ?></span>
                                        <?php } ?>
                                        <label class="mt-2 ml-2"><?php echo $_SESSION['users'][$project['created_by']] ?? ''; ?></label>
                                    </td>
                                    <td>
                                        <a class="text-primary m-r-5" href="index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $project['id']) ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<?= htmlspecialchars($project['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars(substr(($project['subject'] ?? ''), 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($project['subject'] ?? '') > 40 ? '...' : '') ?>
                                        </a>
                                    </td>
                                    <td class="text-end"><?php
                                        $costAmount = ($type == 'spent')
                                            ? ($project['spent_amount'] ?? 0)
                                            : ($project['total_budget_grand_total'] ?? 0);
                                        echo customeummarmonyformate($costAmount);
                                    ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
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
</div>
