<?php
$table_name = 'csr_impact_snapshot';

function csrImpactSnapshotListText($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrImpactSnapshotListValue($row, $key) {
    return is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
}

$url = API . "?mode=getCsrImpactSnapshotData&method=get";
$response = callAPI($url);
$impactData = json_decode($response, true);
$impactSnapshots = $impactData['impact_snapshots'] ?? array();
?>

<link rel="stylesheet" href="assets/css/custom_list_csr_impact_snapshot.css">

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>CSR Impact Snapshot</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_INSERT_CSR_IMPACT_SNAPSHOT" class="btn btn-success mb-2 desktop-view">
                    <i class="icofont icofont-plus icon-white"></i>
                    Impact Snapshot
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Sr. No</th>
                            <th scope="col">Financial Year</th>
                            <th scope="col">Project</th>
                            <th scope="col" class="text-end">Student Supported</th>
                            <th scope="col" class="text-end">Villages Covered</th>
                            <th scope="col" class="text-end">Health Camps</th>
                            <th scope="col" class="text-end">Trees Planted</th>
                            <th scope="col" class="text-end">Women Benefited</th>
                            <th scope="col">Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$impactSnapshots) > 0) { ?>
                            <?php $i = 1; foreach ($impactSnapshots as $row) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo csrImpactSnapshotListText(csrImpactSnapshotListValue($row, 'financial_year')); ?></td>
                                    <td><?php echo csrImpactSnapshotListText(csrImpactSnapshotListValue($row, 'project_name') ?: '-'); ?></td>
                                    <td class="text-end"><?php echo number_format((int)csrImpactSnapshotListValue($row, 'student_supported')); ?></td>
                                    <td class="text-end"><?php echo number_format((int)csrImpactSnapshotListValue($row, 'villages_covered')); ?></td>
                                    <td class="text-end"><?php echo number_format((int)csrImpactSnapshotListValue($row, 'health_camps_conducted')); ?></td>
                                    <td class="text-end"><?php echo number_format((int)csrImpactSnapshotListValue($row, 'trees_planted')); ?></td>
                                    <td class="text-end"><?php echo number_format((int)csrImpactSnapshotListValue($row, 'women_benefited')); ?></td>
                                    <td><?php echo csrImpactSnapshotListValue($row, 'created_date') != '' ? fromDate(csrImpactSnapshotListValue($row, 'created_date')) : ''; ?></td>
                                    <td>
                                        <a class="text-primary" href="index.php?option=COM_INSERT_CSR_IMPACT_SNAPSHOT&Rid=<?php echo deocdeId('id:' . csrImpactSnapshotListValue($row, 'id')); ?>">
                                            <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                        </a>
                                        <a class="text-primary" href="index.php?option=COM_INSERT_CSR_IMPACT_SNAPSHOT&Rid=<?php echo deocdeId('id:' . csrImpactSnapshotListValue($row, 'id')); ?>&view=1">
                                            <i class="fa fa-eye btn-primary p-2 b-radius-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px no-record-img" alt="" src="assets/images/work.png">
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
