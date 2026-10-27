<?php

$cudata = '';
$url = API . "?mode=getamccomplaintslistdetails&method=get&admin_type=".$_SESSION['admin_type'];
$cudata = callAPI($url);
$cudata = json_decode($cudata, 1);
$tableData = $cudata['table'] ?? array();
$active = $pending = $closed = array();
$active_cnt = $pending_cnt = $closed_cnt = 0;
foreach ($tableData  as $row) {
    if ($row['current_status'] == 0) {
        $active[] = $row;
    }
    if (!empty($row['current_status']) && $row['current_status'] > 0 && $row['current_status'] < 199) {
        $pending[] = $row;
    }
    if (!empty($row['current_status']) && $row['current_status'] == 199) {
        $closed[] = $row;
    }
}
$active_cnt = count((array)$active);
$pending_cnt = count((array)$pending);
$closed_cnt = count((array)$closed);

function amcComplaintListScheduleDisplay($row)
{
    if (empty($row['schedule_id'])) {
        return 'Adhoc';
    }
    $display = !empty($row['planned_service_datetime']) ? fromDate($row['planned_service_datetime']) : '';
    $st = (int) ($row['schedule_status'] ?? 0);
    if ($st > 0 && $st < 199) {
        $statusLabel = 'In Progress';
    } elseif ($st >= 199) {
        $statusLabel = 'Completed';
    } else {
        $statusLabel = 'Pending';
    }
    return trim($display . ($display != '' ? ' ' : '') . '(' . $statusLabel . ')');
}

function amcComplaintListAssetDisplay($row)
{
    $row = $row ?? [];
    if (!empty($row['asset_code']) || !empty($row['asset_description'])) {
        return trim(($row['asset_code'] ?? '') . '-' . ($row['asset_description'] ?? ''));
    }
    if (!empty($row['item_name'])) {
        return $row['item_name'];
    }
    return '—';
}

?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">AMC Complaints List <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$tableData) ?? 0; ?></span></h5>
            <div class="card-header-right d-flex align-items-center gap-3 flex-wrap">
                <a href="index.php?option=COM_INSERT_AMC_COMPLAINTS" class="btn btn-success  desktop-view">
                    <i class="icofont icofont-plus text-white"></i>
                AMC Complaint</a>

                <ul class="nav md-tabs infisuite_tab">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12  ACTIVE active" data-bs-toggle="tab" href="#ACTIVE" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Active
                            <span class="form-label label label-xs label-success m-l-10"><?php echo $active_cnt; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 PENDING" data-bs-toggle="tab" href="#PENDING" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending
                            <span class="form-label label label-xs label-success m-l-10"><?php echo $pending_cnt; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 CLOSED" data-bs-toggle="tab" href="#CLOSED" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Closed
                            <span class="form-label label label-xs label-success m-l-10"><?php echo $closed_cnt; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content tabs card-block mt-3">
            <div class="tab-pane ACTIVE active" id="ACTIVE" role="tabpanel">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap">Sl No.</th>
                                    <th scope="col" style="min-width: 160px;">Complaint Name</th>
                                    <th scope="col" class="text-nowrap">Priority</th>
                                    <th scope="col" class="text-nowrap">Date</th>
                                    <th scope="col" style="min-width: 140px;">Contract</th>
                                    <th scope="col" style="min-width: 130px;">Location</th>
                                    <th scope="col" style="min-width: 130px;">Asset</th>
                                    <th scope="col" style="min-width: 120px;">Schedule</th>
                                    <th scope="col" class="text-nowrap">ServiceNow Ref</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$active) > 0) {
                                    $i = 1;
                                    foreach ($active as $row) { ?>
                                        <tr>
                                            <td class="text-nowrap"><?php echo $i++; ?> </td>
                                            <td><?php echo htmlspecialchars($row['complaint_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap" style="font-weight: 900; font-size: 1rem;">
                                                <?php
                                                if ($row['complaint_priority'] == 'L') {
                                                    echo 'Low';
                                                } elseif ($row['complaint_priority'] == 'M') {
                                                    echo 'Medium';
                                                } else {
                                                    echo "High";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-nowrap"><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo htmlspecialchars($row['contract_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars($row['location_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListAssetDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListScheduleDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap"><?php echo !empty($row['servicenow_ref']) ? htmlspecialchars($row['servicenow_ref'], ENT_QUOTES, 'UTF-8') : '—'; ?> </td>
                                            <td class="text-nowrap"><span class="form-label label label-lg  label-primary label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td class="text-nowrap">
                                                <a class="m-r-5"
                                                    href="index.php?option=COM_INSERT_AMC_COMPLAINTS&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="Edit Details">
                                                    <i class="fa fa-edit p-2 b-radius-5"></i>
                                                </a>
                                                <a class="m-r-5" target="_blank"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye p-2 b-radius-5"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="11">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
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
            <div class="tab-pane PENDING " id="PENDING" role="tabpanel">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap">Sl No.</th>
                                    <th scope="col" style="min-width: 160px;">Complaint Name</th>
                                    <th scope="col" class="text-nowrap">Priority</th>
                                    <th scope="col" class="text-nowrap">Date</th>
                                    <th scope="col" style="min-width: 140px;">Contract</th>
                                    <th scope="col" style="min-width: 130px;">Location</th>
                                    <th scope="col" style="min-width: 130px;">Asset</th>
                                    <th scope="col" style="min-width: 120px;">Schedule</th>
                                    <th scope="col" class="text-nowrap">ServiceNow Ref</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$pending) > 0) {
                                    $i = 1;
                                    foreach ($pending as $row) { ?>
                                        <tr>
                                            <td class="text-nowrap"><?php echo $i++; ?> </td>
                                            <td><?php echo htmlspecialchars($row['complaint_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap" style="font-weight: 900; font-size: 1rem;
                                            ">
                                                <?php
                                                if ($row['complaint_priority'] == 'L') {
                                                    echo 'Low';
                                                } elseif ($row['complaint_priority'] == 'M') {
                                                    echo 'Medium';
                                                } else {
                                                    echo "High";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-nowrap"><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo htmlspecialchars($row['contract_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars($row['location_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListAssetDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListScheduleDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap"><?php echo !empty($row['servicenow_ref']) ? htmlspecialchars($row['servicenow_ref'], ENT_QUOTES, 'UTF-8') : '—'; ?> </td>
                                            <td class="text-nowrap"><span class="form-label label label-lg  label-warning label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td class="text-nowrap">
                                                <a class="m-r-5" target="_blank"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye p-2 b-radius-5"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="11">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
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
            <div class="tab-pane CLOSED " id="CLOSED" role="tabpanel">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-nowrap">Sl No.</th>
                                    <th scope="col" style="min-width: 160px;">Complaint Name</th>
                                    <th scope="col" class="text-nowrap">Priority</th>
                                    <th scope="col" class="text-nowrap">Date</th>
                                    <th scope="col" style="min-width: 140px;">Contract</th>
                                    <th scope="col" style="min-width: 130px;">Location</th>
                                    <th scope="col" style="min-width: 130px;">Asset</th>
                                    <th scope="col" style="min-width: 120px;">Schedule</th>
                                    <th scope="col" class="text-nowrap">ServiceNow Ref</th>
                                    <th scope="col" class="text-nowrap">Status</th>
                                    <th scope="col" class="text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$closed) > 0) {
                                    $i = 1;
                                    foreach ($closed as $row) { ?>
                                        <tr>
                                            <td class="text-nowrap"><?php echo $i++; ?> </td>
                                            <td><?php echo htmlspecialchars($row['complaint_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap" style="font-weight: 900; font-size: 1rem;
                                            ">
                                                <?php
                                                if ($row['complaint_priority'] == 'L') {
                                                    echo 'Low';
                                                } elseif ($row['complaint_priority'] == 'M') {
                                                    echo 'Medium';
                                                } else {
                                                    echo "High";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-nowrap"><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo htmlspecialchars($row['contract_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars($row['location_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListAssetDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td><?php echo htmlspecialchars(amcComplaintListScheduleDisplay($row), ENT_QUOTES, 'UTF-8'); ?> </td>
                                            <td class="text-nowrap"><?php echo !empty($row['servicenow_ref']) ? htmlspecialchars($row['servicenow_ref'], ENT_QUOTES, 'UTF-8') : '—'; ?> </td>
                                            <td class="text-nowrap"><span class="form-label label label-lg  label-success label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td class="text-nowrap">
                                                <a class="m-r-5" target="_blank"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye p-2 b-radius-5"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="11">
                                            <div class="text-center">
                                                <p class="text-muted f-16 fw-bold m-b-30">
                                                    <i class="fa fa-warning"></i> No Records
                                                </p>
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
    </div>
</div>
