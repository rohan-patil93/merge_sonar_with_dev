<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$table_name = 'csr_proposal_data';

$url = API . "?mode=getAdminDashBordData&method=get";
$csr_res_arr = callAPI($url);
$csr_res_arr = json_decode($csr_res_arr, 1);
$csr_proposal_arr = $csr_res_arr['csr_proposal_arr'];
$tracking_percent_arr = $csr_res_arr['tracking_percent_arr'];
$users_arr = $csr_res_arr['users_arr'];
$grouped_array = $csr_res_arr['grouped_array'];
// print_r($tracking_percent_arr);

$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;
$activeCount = 0;
$trackingCount = 0;
$totalCount = count($csr_proposal_arr) ?? 0;

$csr_data = $active_data = $inprogress_data = $approved_data = $tracking_data = $completed_data = array();
foreach ($csr_proposal_arr as $row) {
    $csr_data[$row['id']] = $row;

    if (isset($row['current_status']) && $row['current_status'] == 0) {
        $activeCount++;
        $active_data[] = $row['id'];
    }
    if (isset($row['current_status']) && ($row['current_status'] > 0 && $row['current_status'] < 199)) {
        $progressCount++;
        $inprogress_data[] = $row['id'];
    }
    if (isset($row['current_status']) && $row['current_status'] == 199) {
        $approvedCount++;
        $approved_data[] = $row['id'];
    }
    if (isset($row['current_status']) && $row['current_status'] == 199 && $row['tracking_status'] == 0) {
        $trackingCount++;
        $tracking_data[] = $row['id'];
    }
    if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 1)) {
        $completedCount++;
        $completed_data[] = $row['id'];
    }
}

?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<div class="page-body">
    <div class="row">

        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $totalCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $activeCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> In Progress CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-purple"><?php echo $progressCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-lite-green"><?php echo $approvedCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-file-archive"></i> Tracking CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-yellow"><?php echo $trackingCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-file-archive"></i> Completed CSRs</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-green"><?php echo $completedCount; ?></h2>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card new-cust-card">
                <div class="card-header">
                    <h5>Committee Members</h5>
                </div>

                <div class="card-block custom-scroll" style="height: 350px;">
                    <?php
                    if ($users_arr) {
                        foreach ($users_arr as $row) { ?>
                            <div class="align-middle m-b-25">
                                <img src="uploads/all/<?php echo $_SESSION['user_image'][$row['id']]; ?>" alt="user image" class="img-radius img-40 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
                                <div class="d-inline-block">
                                    <a href="#!">
                                        <h6><?php echo $row['display_name'] ?></h6>
                                    </a>
                                    <p class="text-muted m-b-0">
                                    <h6><?php echo $row['user_email'] ?></h6>
                                    </p>
                                </div>
                            </div>
                    <?php }
                    } else {
                        echo '<div class="text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Members</p>   
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>   
                            </div>';
                    } ?>

                </div>
            </div>
        </div>
        <div class="col-xl-8 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Recent CSRs</h5>
                </div>
                <div class="card-block custom-scroll" style="height: 350px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th>Creator</th>
                                    <th>CSRs Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class='text-center'>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($csr_proposal_arr) {
                                    $lastFiveReversed = array_reverse(array_slice($csr_proposal_arr, -5));
                                    $max_count = count($csr_proposal_arr);
                                    if (count($csr_proposal_arr) > 4) {
                                        $max_count = 4;
                                    }
                                    foreach ($lastFiveReversed as $value) { ?>
                                        <tr>
                                            <td>
                                                <?php if ($_SESSION['user_image'][$value['created_by']] != "") { ?>
                                                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$value['created_by']]; ?>" class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                                                <?php } else { ?>
                                                    <span alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$value['created_by']]); ?></span>
                                                <?php } ?>
                                                <label class='mt-2 ml-2'> <?php echo $_SESSION['users'][$value['created_by']]; ?> </label>
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
                                } else {
                                    echo '<div class="text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>   
                                    </div>';
                                } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <?php

        function labelClass($percentage = 0)
        {
            if ($percentage < 50) {
                $labelClass = 'label-danger';
            } elseif ($percentage < 100) {
                $labelClass = 'label-warning';
            } else {
                $labelClass = 'label-success';
            }
            return $labelClass;
        }

        ?>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Active CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($active_data)): ?>
                                    <?php foreach ($active_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>In Progress CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($inprogress_data)): ?>
                                    <?php foreach ($inprogress_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Approved CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($approved_data)): ?>
                                    <?php foreach ($approved_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Tracking CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($tracking_data)): ?>
                                    <?php foreach ($tracking_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Completed CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($completed_data)): ?>
                                    <?php foreach ($completed_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Total CSRs</h5>
                </div>
                <div class="card-block custom-scroll p-b-0" style="height: 285px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (!empty($csr_data)): ?>
                                    <?php foreach ($csr_data as $row): ?>
                                        <tr>
                                            <td>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($row['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </td>
                                            <td><?= customeummarmonyformate($row['total_budget_grand_total']) ?></td>
                                            <?php $percentage = $tracking_percent_arr[$row['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <td class='text-end'>
                                                <label class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</label>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>CSRs Budget</h5>
                </div>
                <div class="card-block custom-scroll" style="height: 400px;">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th>CSRs Name</th>
                                    <th>1-30 Days</th>
                                    <th>31-60 Days</th>
                                    <th>61-90 Days</th>
                                    <th>91-120 Days</th>
                                    <th>121-150 Days</th>
                                    <th>151-180 Days</th>
                                    <th>180+ Days</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tracking_data)): ?>
                                    <?php foreach ($tracking_data as $row):
                                    ?>

                                        <tr>
                                            <td><a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a></td>
                                            <?php
                                            $ranges = [
                                                '0-30 days' => [0, 30],
                                                '31-60 days' => [31, 60],
                                                '61-90 days' => [61, 90],
                                                '91-120 days' => [91, 120],
                                                '121-150 days' => [121, 150],
                                                '151-180 days' => [151, 180],
                                                '180+ days' => [181, PHP_INT_MAX]
                                            ];
                                            $column_totals = array_fill_keys(array_keys($ranges), 0);
                                            $column_totals['Total'] = 0;
                                            $current_date = new DateTime();

                                            $row_totals = array_fill_keys(array_keys($ranges), 0);
                                            $row_totals['Total'] = 0;
                                            // Use the system's current date
                                            $budget_rows = $grouped_array[$row];
                                            foreach ($budget_rows as $row) {
                                                $start_date = new DateTime($row['start_date']);
                                                $days_to_start = $current_date->diff($start_date)->days;
                                                if ($start_date < $current_date) {
                                                    $days_to_start = -$days_to_start; // Handle past dates
                                                }

                                                foreach ($ranges as $range_label => $range) {
                                                    if ($days_to_start >= $range[0] && $days_to_start <= $range[1]) {
                                                        $row_totals[$range_label] += $row['tracking_budget'];
                                                        break;
                                                    }
                                                }
                                            }
                                            $row_totals['Total'] = array_sum($row_totals);
                                            foreach ($row_totals as $key => $value) {
                                                $column_totals[$key] += $value;
                                                echo '<td class="text-end">' . customeummarmonyformate($value) . '</td>';
                                            }
                                            ?>
                                        </tr>


                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfooter>
                                <tr>
                                    <td>Total</td>
                                    <?php
                                    foreach ($column_totals as $value) {
                                        echo "<td class='text-end'>" . customeummarmonyformate($value) . "</td>";
                                    }
                                    ?>
                                </tr>
                            </tfooter>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Pradeep HTML End here -->
</div>