<?php

$cudata = '';
$url = API . "?mode=getamccomplaintslistdetails&method=get";
$cudata = callAPI($url);
$cudata = json_decode($cudata, 1);
$tableData = $cudata['table'] ?? array();
$active = $pending = $closed = array();
$active_cnt = $pending_cnt = $closed_cnt = 0;
foreach ($tableData  as $row) {
    if ($row['current_status'] == 101) {
        $active[] = $row;
    }
    if (!empty($row['current_status']) && $row['current_status'] > 101 && $row['current_status'] < 199) {
        $pending[] = $row;
    }
    if (!empty($row['current_status']) && $row['current_status'] == 199) {
        $closed[] = $row;
    }
}
$active_cnt = count((array)$active);
$pending_cnt = count((array)$pending);
$closed_cnt = count((array)$closed);

?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>AMC Complaints List <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$tableData) ?? 0; ?></span></h5>
            <div class="card-header-right d-flex align-items-center gap-3">
                <a href="index.php?option=COM_INSERT_AMC_COMPLAINTS" class="btn btn-success  desktop-view">
                    <i class="icofont icofont-plus text-white"></i>
                AMC Complaint</a>

                <ul class="nav md-tabs infisuite_tab">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12  ACTIVE active" data-bs-toggle="tab" href="#ACTIVE" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Received
                            <span class="form-label label label-xs label-success m-l-10"><?php echo $active_cnt; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 PENDING" data-bs-toggle="tab" href="#PENDING" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">In Progress
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
                <div class="card-body p-4 ">
                    <div class="row">
                        <table class="table  table-hover nowrap ">
                            <thead>
                                <tr>
                                    <th scope="col">Sl No.</th>
                                    <th scope="col">Complaint Name</th>
                                    <th scope="col">Complaint Type</th>
                                    <th scope="col">Priority</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Property</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$active) > 0) {
                                    $i = 1;
                                    foreach ($active as $row) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?> </td>
                                            <td><?php echo $row['complaint_name']; ?> </td>
                                            <td><?php echo $row['complaint_type']; ?> </td>

                                            <td style="font-weight: 900; font-size: 1rem;">
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
                                            <td><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo $row['item_name'] ?? $row['asset_code'] . '-' . $row['asset_description']; ?> </td>
                                            <td><span class="form-label label label-lg  label-primary label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td>
                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye  p-2 b-radius-5"></i>
                                                </a>

                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>&RS=1" title="Copy Details">
                                                    <i class="fa fa-edit  p-2 b-radius-5"></i>
                                                </a>

                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="9">
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
                <div class="card-body p-4 ">
                    <div class="row">
                        <table class="table  table-hover nowrap ">
                            <thead>
                                <tr>
                                    <th scope="col">Sl No.</th>
                                    <th scope="col">Complaint Name</th>
                                    <th scope="col">Complaint Type</th>
                                    <th scope="col">Priority</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Property</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$pending) > 0) {
                                    $i = 1;
                                    foreach ($pending as $row) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?> </td>
                                            <td><?php echo $row['complaint_name']; ?> </td>
                                            <td><?php echo $row['complaint_type']; ?> </td>

                                            <td style="font-weight: 900; font-size: 1rem;
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
                                            <td><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo $row['item_name'] ?? $row['asset_code'] . '-' . $row['asset_description']; ?> </td>
                                            <td><span class="form-label label label-lg  label-warning label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td>
                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye  p-2 b-radius-5"></i>
                                                </a>
                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>&RS=1" title="Copy Details">
                                                    <i class="fa fa-edit  p-2 b-radius-5"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="9">
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
                <div class="card-body p-4 ">
                    <div class="row">
                        <table class="table  table-hover nowrap ">
                            <thead>
                                <tr>
                                    <th scope="col">Sl No.</th>
                                    <th scope="col">Complaint Name</th>
                                    <th scope="col">Complaint Type</th>
                                    <th scope="col">Priority</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Property</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count((array)$closed) > 0) {
                                    $i = 1;
                                    foreach ($closed as $row) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?> </td>
                                            <td><?php echo $row['complaint_name']; ?> </td>
                                            <td><?php echo $row['complaint_type']; ?> </td>

                                            <td style="font-weight: 900; font-size: 1rem;
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
                                            <td><?php echo !empty($row['transaction_date']) ? fromDate($row['transaction_date']) : ''; ?> </td>
                                            <td><?php echo $row['item_name'] ?? $row['asset_code'] . '-' . $row['asset_description']; ?> </td>
                                            <td><span class="form-label label label-lg  label-success label-inline f-w-900 " ><?php echo $row['name']; ?></span> </td>
                                            <td>
                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                                    <i class="fa fa-eye  p-2 b-radius-5"></i>
                                                </a>
                                                <a class=" m-r-5"
                                                    href="index.php?option=COM_VIEW_AMC_COMPLAINT_RESPONSES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>&RS=1" title="Copy Details">
                                                    <i class="fa fa-edit  p-2 b-radius-5"></i>
                                                </a>
                                            </td>
                                        </tr>

                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="9">
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
