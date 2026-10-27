<?php

$table_name = 'csr_proposal_data';

$url = API . "?mode=getAdminDashBordData&method=get";
$csr_res_arr = callAPI($url);
$csr_res_arr = json_decode($csr_res_arr, 1);
$csr_proposal_arr = $csr_res_arr['csr_proposal_arr'];
$tracking_percent_arr = $csr_res_arr['tracking_percent_arr'];
$users_arr = $csr_res_arr['users_arr'];
$grouped_array = $csr_res_arr['grouped_array'];
$csr_users_by_role = $csr_res_arr['csr_users_by_role'] ?? array();
$csr_role_tabs = array(
    'CSR_CREATER' => 'Creator',
    'CSR_COMMITTEE_GROUP' => 'Committee Group',
    'CSR_COMMITTEE_MEMBER' => 'Committee Member',
    'CSR_COMMITTEE_HEAD' => 'Committee Head',
);
$completedCount = 0;
$approvedCount = 0;
$progressCount = 0;
$activeCount = 0;
$trackingCount = 0;
$totalCount = count($csr_proposal_arr) ?? 0;

$csr_data = $active_data = $inprogress_data = $approved_data = $tracking_data = $completed_data = array();
$totalValue = $activeValue = $progressValue = $approvedValue = $trackingValue = $completedValue = 0;

$currentYear = date('Y');
$currentYearTotalValue = $currentYearActiveValue = $currentYearProgressValue = $currentYearApprovedValue = $currentYearTrackingValue = $currentYearCompletedValue = 0;
$currentYearcompletedCount = 0;
$currentYearapprovedCount = 0;
$currentYearprogressCount = 0;
$currentYearactiveCount = 0;
$currentYeartrackingCount = 0;
$currentYeartotalCount = 0;

foreach ($csr_proposal_arr as $row) {
    $csr_data[$row['id']] = $row;
    $totalValue += $row['total_budget_grand_total'] ?? 0;
    $year = date('Y', strtotime($row['project_start_date'] ?? ''));

    if (isset($row['current_status']) && $row['current_status'] == 0) {
        $activeCount++;
        $active_data[] = $row['id'];
        $activeValue += $row['total_budget_grand_total'] ?? 0;
    }
    if (isset($row['current_status']) && ($row['current_status'] > 0 && $row['current_status'] < 199)) {
        $progressCount++;
        $inprogress_data[] = $row['id'];
        $progressValue += $row['total_budget_grand_total'] ?? 0;
    }
    if (isset($row['current_status']) && $row['current_status'] == 199) {
        $approvedCount++;
        $approved_data[] = $row['id'];
        $approvedValue += $row['total_budget_grand_total'] ?? 0;
    }
    if (isset($row['current_status']) && $row['current_status'] == 199 && $row['tracking_status'] == 0) {
        $trackingCount++;
        $tracking_data[] = $row['id'];
        $trackingValue += $row['total_budget_grand_total'] ?? 0;
    }
    if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 1)) {
        $completedCount++;
        $completed_data[] = $row['id'];
        $completedValue += $row['total_budget_grand_total'] ?? 0;
    }

    if ($year == $currentYear) {
        $currentYearTotalValue += $row['total_budget_grand_total'] ?? 0;
        $currentYeartotalCount++;

        if (isset($row['current_status']) && $row['current_status'] == 0) {
            $currentYearActiveValue += $row['total_budget_grand_total'] ?? 0;
            $currentYearactiveCount++;

        }
        if (isset($row['current_status']) && ($row['current_status'] > 0 && $row['current_status'] < 199)) {
            $currentYearProgressValue += $row['total_budget_grand_total'] ?? 0;
            $currentYearprogressCount++;

        }
        if (isset($row['current_status']) && $row['current_status'] == 199) {
            $currentYearApprovedValue += $row['total_budget_grand_total'] ?? 0;
            $currentYearapprovedCount++;

        }
        if (isset($row['current_status']) && $row['current_status'] == 199 && $row['tracking_status'] == 0) {
            $currentYearTrackingValue += $row['total_budget_grand_total'] ?? 0;
            $currentYeartrackingCount++;

        }
        if (isset($row['current_status']) && ($row['current_status'] == 199 && $row['tracking_status'] == 1)) {
            $currentYearCompletedValue += $row['total_budget_grand_total'] ?? 0;
            $currentYearcompletedCount++;

        }
    }
}
$progress_var =  gettableStatusArray($table_name);
?>
<link rel="stylesheet" href="assets/css/custom_view_csr_admin_dashboard.css">

<div class="page-body">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Total CSRs</h5>
                </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card">
                                <div class="card-body">
                                    <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total</p>
                                    <div class="text-center">
                                        <h2 class="m-t-5 m-b-0 d-inline-block text-c-red cursor-pointer csr-click" data-title="Total" data-count="<?= $totalCount ?>"data-type="total" > <?php echo $totalCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($totalValue); ?></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active</p>
                                    <div class="text-center">
                                        <h2 class="m-t-20 m-b-0 d-inline-block text-c-blue cursor-pointer csr-click"data-title="Active"
                                        data-count="<?= $activeCount ?>"
                                        data-type="active">
                                            <?php echo $activeCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($activeValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> Approval In Progress</p>
                                    <div class="text-center">
                                        <h2 class="m-b-0 d-inline-block text-c-purple cursor-pointer csr-click"
                                        data-title="Approval In Progress"
                                        data-count="<?= $progressCount ?>"
                                        data-type="progress">
                                            <?php echo $progressCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($progressValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved</p>
                                    <div class="text-center">
                                        <h2 class="m-t-20 m-b-0 d-inline-block text-c-lite-green cursor-pointer csr-click"
                                        data-title="Approved"
                                        data-count="<?= $approvedCount ?>"
                                        data-type="approved">
                                            <?php echo $approvedCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($approvedValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-md-6">
                    <div class="card prod-p-card card-red">
                        <div class="card-body">
                            <div class="row align-items-center m-b-30">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">Work In Progress CSRs</h6>
                                    <h3 class="m-b-0 f-w-700 text-white d-inline-block text-c-yellow cursor-pointer csr-click"
                                    data-title="Work In Progress"
                                    data-count="<?= $trackingCount ?>"
                                    data-type="tracking">
                                        <?php echo $trackingCount; ?>
                                    </h3>
                                </div>
                            </div>
                            <p class="m-b-0 text-white"><?php echo customeummarmonyformate($trackingValue); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6">
                    <div class="card prod-p-card card-blue">
                        <div class="card-body">
                            <div class="row align-items-center m-b-30">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">Completed CSRs</h6>
                                    <h3 class="m-b-0 f-w-700 text-white d-inline-block text-c-green cursor-pointer csr-click"
                                    data-title="Completed"
                                    data-count="<?= $completedCount ?>"
                                    data-type="completed">
                                    <?php echo $completedCount; ?>
                                    </h3>
                                </div>
                            </div>
                            <p class="m-b-0 text-white"><?php echo customeummarmonyformate($completedValue); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Year CSRs</h5>
                </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-red lbl-card"><i class="fas fa-file-archive"></i> Total</p>
                                    <div class="text-center">
                                        <h2 class="m-t-20 m-b-0 d-inline-block text-c-red cursor-pointer csr-click"
                                            data-title="Total"
                                            data-count="<?= $currentYeartotalCount ?>"
                                            data-type="total"
                                            data-year="<?php echo $currentYear; ?>">
     <?php echo $currentYeartotalCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"> <?php echo customeummarmonyformate($currentYearTotalValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> Active</p>
                                    <div class="text-center">
                                        <h2 class="m-t-20 m-b-0 d-inline-block text-c-blue cursor-pointer csr-click"
                                            data-title="Active"
                                            data-count="<?= $currentYearactiveCount ?>"
                                            data-type="active"
                                            data-year="<?php echo $currentYear; ?>">
                                            <?php echo $currentYearactiveCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($currentYearActiveValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-purple lbl-card"><i class="fas fa-file-archive"></i> Approval In Progress</p>
                                    <div class="text-center">
                                        <h2 class="m-b-0 d-inline-block text-c-purple cursor-pointer csr-click"
                                        data-title="Approval In Progress"
                                        data-count="<?= $currentYearprogressCount ?>"
                                        data-type="progress"
                                        data-year="<?php echo $currentYear; ?>">
                                            <?php echo $currentYearprogressCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"> <?php echo customeummarmonyformate($currentYearProgressValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <div class="card ticket-card" >
                                <div class="card-body">
                                    <p class="m-b-5 bg-c-lite-green lbl-card"><i class="fas fa-file-archive"></i> Approved</p>
                                    <div class="text-center">
                                        <h2 class="m-t-20 m-b-0 d-inline-block text-c-lite-green cursor-pointer csr-click"
                                        data-title="Approved"
                                        data-count="<?= $currentYearapprovedCount ?>"
                                        data-type="approved"
                                        data-year="<?php echo $currentYear; ?>">
                                            <?php echo $currentYearapprovedCount; ?>
                                        </h2>
                                        <p class="m-b-0 f-w-600 m-t-10"><?php echo customeummarmonyformate($currentYearApprovedValue); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-md-6">
                    <div class="card prod-p-card card-yellow">
                        <div class="card-body">
                            <div class="row align-items-center m-b-30">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">Work In Progress CSRs</h6>
                                    <h3 class="m-b-0 f-w-700 text-white d-inline-block text-c-yellow cursor-pointer csr-click"
                                    data-title="Work In Progress"
                                    data-count="<?= $currentYeartrackingCount ?>"
                                    data-type="tracking"
                                    data-year="<?php echo $currentYear; ?>">
                                    <?php echo $currentYeartrackingCount; ?>
                                    </h3>
                                </div>
                            </div>
                            <p class="m-b-0 text-white"><?php echo customeummarmonyformate($currentYearTrackingValue); ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6">
                    <div class="card prod-p-card card-green">
                        <div class="card-body">
                            <div class="row align-items-center m-b-30">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">Completed CSRs</h6>
                                    <h3 class="m-b-0 f-w-700 text-white d-inline-block text-c-green cursor-pointer csr-click"
                                    data-title="Completed"
                                    data-count="<?= $currentYearcompletedCount ?>"
                                    data-type="completed"
                                    data-year="<?php echo $currentYear; ?>">
                                    <?php echo $currentYearcompletedCount; ?>
                                    </h3>
                                </div>
                            </div>
                            <p class="m-b-0 text-white"><?php echo customeummarmonyformate($currentYearCompletedValue); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">

            <div class="card new-cust-card">
                <div class="card-header">
                    <h5>CSR USERS</h5>
                </div>
                <ul class="nav nav-tabs md-tabs csr-users-tabs">
                    <?php
                    $csr_tab_index = 0;
                    foreach ($csr_role_tabs as $role_key => $role_label) {
                        $role_users = $csr_users_by_role[$role_key] ?? array();
                        $tab_active = ($csr_tab_index == 0) ? 'active' : '';
                        $tab_id = 'csr_users_' . strtolower($role_key);
                    ?>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 <?php echo $tab_active; ?>" data-bs-toggle="tab" href="#<?php echo $tab_id; ?>" role="tab" aria-selected="<?php echo ($csr_tab_index == 0) ? 'true' : 'false'; ?>">
                            <?php echo $role_label; ?> <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$role_users); ?></span>
                        </a>
                    </li>
                    <?php
                        $csr_tab_index++;
                    } ?>
                </ul>
                <div class="tab-content">
                    <?php
                    $csr_tab_index = 0;
                    foreach ($csr_role_tabs as $role_key => $role_label) {
                        $role_users = $csr_users_by_role[$role_key] ?? array();
                        $tab_active = ($csr_tab_index == 0) ? 'active' : '';
                        $tab_id = 'csr_users_' . strtolower($role_key);
                    ?>
                    <div class="tab-pane <?php echo $tab_active; ?>" id="<?php echo $tab_id; ?>" role="tabpanel">
                        <div class="card-block custom-scroll">
                            <?php
                            if (count((array)$role_users) > 0) {
                                foreach ($role_users as $row) {
                                    $user_image = $_SESSION['user_image'][$row['id']] ?? '';
                                    if ($user_image == '') {
                                        $user_image = 'no-image.jpg';
                                    }
                            ?>
                            <div class="align-middle m-b-25">
                                <img src="uploads/all/<?php echo $user_image; ?>" alt="User" class="img-radius img-40 align-top m-r-15">
                                <div class="d-inline-block">
                                    <a href="#!">
                                        <h6 class="f-12 f-w-600"><?php echo $row['display_name']; ?></h6>
                                    </a>
                                    <p class="text-muted m-b-0">
                                    <h6 class="f-14"><?php echo $row['user_email']; ?></h6>
                                    </p>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo '<div class="text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Members</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
                                        </div>
                                    </div>';
                            } ?>
                        </div>
                    </div>
                    <?php
                        $csr_tab_index++;
                    } ?>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-12 col-md-12">
            <div class="card table-card pb-5">
                <div class="card-header">
                    <h5>Recent CSRs</h5>
                    <div class="card-header-right">
                        <div class="input-group input-group-button mb-0">
                            <input type="text" name="searcing_recent"  id="searcing_recent" class="form-control form-control-sm width1 p-2 fill" placeholder="Search" data-bs-original-title="Search" aria-label="Search">
                            <span class="input-group-text addon-style m-0">
                                <span class=""><span class="fa fa-search text-white"></span></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-block custom-scroll mt-4 custom-scroll-350"  id ="recent_csr_list">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col">Sr. No</th>
                                    <th scope="col">Creator</th>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col" class='text-center'>Cost</th>
                                    <th scope="col">Status</th>
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
                                    $i = 1;
                                    foreach ($lastFiveReversed as $value) { ?>
                                        <tr>
                                            <td> <?php echo $i++; ?> </td>
                                            <td>
                                                <?php if ($_SESSION['user_image'][$value['created_by']] != "") { ?>
                                                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$value['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                                <?php } else { ?>
                                                    <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$value['created_by']]); ?></span>
                                                <?php } ?>
                                                <span class='mt-2 ml-2'> <?php echo $_SESSION['users'][$value['created_by']]; ?> </span>
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
                                            <td>
                                                <span class="form-label label label-md csr-status-label"
    data-color="<?php echo $_SESSION['status'][$value['current_status']]['color']; ?>"
    data-bg="<?php echo $_SESSION['status'][$value['current_status']]['background_color'] ?? 'blue'; ?>">
                                                    <?php
                                                    if ($value['current_status'] == 0) {
                                                        echo "Active";
                                                    } elseif ($value['current_status'] > 0 && $value['current_status'] < 199) {
                                                        echo "In Progress";
                                                    } elseif ($value['current_status'] == 199 && $value['tracking_status'] == 0) {
                                                        echo "Tracking";
                                                    } elseif ($value['current_status'] == 199 && $value['tracking_status'] == 1) {
                                                        echo "Completed";
                                                    } elseif ($value['current_status'] == 199) {
                                                        echo "Approved";
                                                    } else {
                                                        echo "Unknown";
                                                    }
                                                    ?>
                                                </span>

                                            </td>
                                        </tr>
                                <?php }
                                } else {
                                    echo '<tr> <td colspan="6" class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
                                        </div>
                                     </td> </tr> ';
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
                    <span class="form-label label label-sm label-primary "> <?php echo $activeCount; ?> </span>
                    <div class="card-header-right">
                        <span class="form-label label label-sm label-primary m-l-10">
                            <?php echo customeummarmonyformate($activeValue); ?>
                        </span>
                    </div>
                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285" >
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                                <?php if (count((array)$active_data)>0){
                                    $i = 1;
                                     foreach ($active_data as $row){ ?>
                                        <tr>
                                            <th scope="col"> <?php echo $i++; ?> </th>
                                            <th scope="col">
                                                <?php if ($_SESSION['user_image'][$value['created_by']] != "") { ?>
                                                <div class="align-middle">
                                                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$value['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                                <?php } else { ?>
                                                    <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$value['created_by']]); ?></span>
                                                <?php } ?>
                                                    <div class="d-inline-block m-l-5">
                                                        <span class='d-block mb-0'> <?php echo $_SESSION['users'][$value['created_by']]; ?> </span>
                                                        <a class='text-primary m-r-5'
                                                            href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                            data-bs-toggle='tooltip'
                                                            data-bs-placement='top'
                                                            data-bs-html='true'
                                                            title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                            <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </th>
                                            <th scope="col"><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></th>
                                            <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                            $labelClass = labelClass($percentage) ?>
                                            <th scope="col" class='text-end'>
                                                <span class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</span>
                                            </th>

                                        </tr>

                                    <?php } ?>
                                    <?php }
                                     else{ ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png"
                                                        >
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
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Approval In Progress CSRs</h5>
                    <span class="form-label label label-sm bg-c-purple"> <?php echo $progressCount; ?> </span>
                    <div class="card-header-right">
                        <span class="form-label label label-sm bg-c-purple m-l-10">
                            <?php echo customeummarmonyformate($progressValue); ?>
                        </span>
                    </div>
                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                            <?php if (count((array)$inprogress_data) > 0) {
                            $i = 1;
                            foreach ($inprogress_data as $row) { ?>
                                <tr>
                                    <th scope="col"> <?php echo $i++; ?> </th>
                                    <th scope="col">
                                        <?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                        <div class="align-middle">
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                            <div class="d-inline-block m-l-5">
                                                <span class='d-block mb-0'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                                <a class='text-primary m-r-5'
                                                href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'
                                                data-bs-html='true'
                                                title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col"><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></th>
                                    <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                    $labelClass = labelClass($percentage) ?>
                                    <th scope="col" class='text-end'>
                                        <span class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</span>
                                    </th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt=""
                                                src="assets/images/work.png">
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
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Approved CSRs</h5>
                    <span class="form-label label label-sm label-success "> <?php echo $approvedCount; ?> </span>
                    <div class="card-header-right">
                        <span class="form-label label label-sm label-success m-l-10">
                            <?php echo customeummarmonyformate($approvedValue); ?>
                        </span>
                    </div>
                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                            <?php if (count((array)$approved_data) > 0) {
                            $i = 1;
                            foreach ($approved_data as $row) { ?>
                                <tr>
                                    <th scope="col"> <?php echo $i++; ?> </th>
                                    <th scope="col">
                                        <?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                        <div class="align-middle">
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                            <div class="d-inline-block m-l-5">
                                                <span class='d-block mb-0'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                                <a class='text-primary m-r-5'
                                                href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'
                                                data-bs-html='true'
                                                title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col"><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></th>
                                    <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                    $labelClass = labelClass($percentage) ?>
                                    <th scope="col" class='text-end'>
                                        <span class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</span>
                                    </th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt=""
                                                src="assets/images/work.png">
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
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Work In Progress CSRs</h5>
                    <span class="form-label label label-sm label-danger "> <?php echo $trackingCount; ?> </span>
                    <div class="card-header-right">
                        <span class="form-label label label-sm label-danger m-l-10">
                            <?php echo customeummarmonyformate($trackingValue); ?>
                        </span>
                    </div>
                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                            <?php if (count((array)$tracking_data) > 0) {
                            $i = 1;
                            foreach ($tracking_data as $row) { ?>
                                <tr>
                                    <th scope="col"> <?php echo $i++; ?> </th>
                                    <th scope="col">
                                        <?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                        <div class="align-middle">
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                            <div class="d-inline-block m-l-5">
                                                <span class='d-block mb-0'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                                <a class='text-primary m-r-5'
                                                href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'
                                                data-bs-html='true'
                                                title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col"><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></th>
                                    <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                    $labelClass = labelClass($percentage) ?>
                                    <th scope="col" class='text-end'>
                                            <a class=''
                                                href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'>
                                                 <span class='cursor-pointer form-label label <?= $labelClass ?>'><?= $percentage; ?>% </span>
                                            </a>
                                    </th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt=""
                                                src="assets/images/work.png">
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
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Completed CSRs</h5>
                    <span class="form-label label label-sm label-primary "> <?php echo $completedCount; ?> </span>
                    <div class="card-header-right">
                        <span class="form-label label label-sm label-primary m-l-10">
                            <?php echo customeummarmonyformate($completedValue); ?>
                        </span>
                    </div>
                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                            <?php if (count((array)$completed_data) > 0) {
                            $i = 1;
                            foreach ($completed_data as $row) { ?>
                                <tr>
                                    <th scope="col"> <?php echo $i++; ?> </th>
                                    <th scope="col">
                                        <?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                        <div class="align-middle">
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                            <div class="d-inline-block m-l-5">
                                                <span class='d-block mb-0'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col"><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']) ?></th>
                                    <?php $percentage = $tracking_percent_arr[$csr_data[$row]['id']] ?? 0;
                                    $labelClass = labelClass($percentage) ?>
                                    <th scope="col" class='text-end'>
                                        <a class='text-primary m-r-5'
                                                href='index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'><span class='cursor-pointer form-label label <?= $labelClass ?>'><?= $percentage; ?>% </span>
                                                </a>
                                    </th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt=""
                                                src="assets/images/work.png">
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
        <div class="col-xl-4 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Total CSRs</h5>
                    <span class="form-label label label-sm label-success "> <?php echo $totalCount; ?> </span>
                    <div class="card-header-right"><span class="form-label label label-sm label-success m-l-10"> <?php echo customeummarmonyformate($totalValue); ?> </span></div>

                </div>
                <div class="card-block custom-scroll p-b-0 m-b-30 custom-scroll-285">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0 without-header f-12">
                            <tbody>
                            <?php if (count((array)$csr_data) > 0) {
                            $i = 1;
                            foreach ($csr_data as $row) { ?>
                                <tr>
                                    <th scope="col"> <?php echo $i++; ?> </th>
                                    <th scope="col">
                                        <?php if ($_SESSION['user_image'][$row['created_by']] != "") { ?>
                                        <div class="align-middle">
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
                                        <?php } ?>
                                            <div class="d-inline-block m-l-5">
                                                <span class='d-block mb-0'> <?php echo $_SESSION['users'][$row['created_by']]; ?> </span>
                                                <a class='text-primary m-r-5'
                                                    href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
                                                    data-bs-toggle='tooltip'
                                                    data-bs-placement='top'
                                                    data-bs-html='true'
                                                    title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                    <?= htmlspecialchars(substr($row['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 15 ? '...' : '') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col"><?= customeummarmonyformate($row['total_budget_grand_total']) ?></th>
                                    <?php $percentage = $tracking_percent_arr[$row['id']] ?? 0;
                                    $labelClass = labelClass($percentage) ?>
                                    <th scope="col" class='text-end'>
                                        <span class='form-label label <?= $labelClass ?>'> <?= $percentage; ?>%</span>
                                    </th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt=""
                                                src="assets/images/work.png">
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

        <div class="col-xl-12 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Fund Flow For Work In Progress CSRs</h5>
                </div>
                <div class="card-block custom-scroll custom-scroll-400" >
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col">Sr. No</th>
                                    <th scope="col">Creator</th>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">1-30 Days</th>
                                    <th scope="col">31-60 Days</th>
                                    <th scope="col">61-90 Days</th>
                                    <th scope="col">91-120 Days</th>
                                    <th scope="col">121-150 Days</th>
                                    <th scope="col">151-180 Days</th>
                                    <th scope="col">180+ Days</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$tracking_data)>0){
                                    $i = 1;
                                     foreach ($tracking_data as $row){
                                    ?>

                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                        <span class='mt-2 ml-2'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                        </td>
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
                                            $row_totals['Total'] = array_sum($row_totals);?>
                                            <?php
                                            foreach ($row_totals as $key => $value) {
                                                $column_totals[$key] += $value;
                                                echo '<td class="text-end">' . customeummarmonyformate($value) . '</td>';
                                            }
                                            ?>

                                        </tr>

                                    <?php }
                                }else{ ?>
                                    <tr>
                                        <td colspan="10">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfooter>
                                <tr>
                                    <td colspan='3' class="text-center">Total</td>
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

        <div class="col-xl-6 col-md-6">
            <div class="card table-card">
                <div class="card-header">
                    <h5>Fund Release</h5>
                </div>
                <div class="card-block custom-scroll custom-scroll-400" >
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col">Sr. No</th>
                                    <th scope="col">Creator</th>
                                    <th scope="col">Project Name</th>
                                    <th scope="col" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$completed_data)>0){
                                    $total_budget = 0;
                                    $i = 1;
                                     foreach ($completed_data as $row){
                                    ?>

                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php if ($_SESSION['user_image'][$csr_data[$row]['created_by']] != "") { ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$csr_data[$row]['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php } else { ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$csr_data[$row]['created_by']]); ?></span>
                                        <?php } ?>
                                        <span class='mt-2 ml-2'> <?php echo $_SESSION['users'][$csr_data[$row]['created_by']]; ?> </span>
                                        </td>
                                        <td><a class='text-primary m-r-5'
                                                href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $csr_data[$row]['id']) ?>'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'
                                                data-bs-html='true'
                                                title='<?= htmlspecialchars($csr_data[$row]['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                                <?= htmlspecialchars(substr($csr_data[$row]['subject'], 0, 15), ENT_QUOTES, 'UTF-8') . (strlen($csr_data[$row]['subject']) > 15 ? '...' : '') ?>
                                            </a>
                                        </td>
                                        <td class='text-end'><?= customeummarmonyformate($csr_data[$row]['total_budget_grand_total']); ?></td>
                                        <?php $total_budget += $csr_data[$row]['total_budget_grand_total']; ?>
                                        </tr>

                                    <?php }
                                }else{ ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt=""
                                                        src="assets/images/work.png">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfooter>
                                <tr>
                                    <td colspan='3' class="text-center">Total</td>
                                    <?php

                                        echo "<td class='text-end'>" . customeummarmonyformate($total_budget) . "</td>";

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
<script src="assets/js/custom_view_csr_admin_dashboard.js"></script>
