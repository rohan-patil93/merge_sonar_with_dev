<?php
$url = API . "?mode=getamcrepairorderslist&method=get&admin_type=".$_SESSION['admin_type'];
$data = json_decode(callAPI($url), true)?? [];
$rows = $data['rows'] ?? array();
?>
<div class="page-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>AMC Repair Orders <span class="badge bg-primary"><?php echo count((array)$rows); ?></span></h5>
            <a href="index.php?option=COM_INSERT_AMC_REPAIR_ORDER" class="btn btn-success btn-sm"><i class="ti-plus"></i> New Repair Order</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">RO No.</th>
                            <th scope="col">Date</th>
                            <th scope="col">Contract</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Source</th>
                            <th scope="col">ServiceNow</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$rows) == 0) { ?>
                        <tr><td colspan="9" class="text-center text-muted">No repair orders</td></tr>
                        <?php } else {
                            $i = 1;
                            foreach ($rows as $row) {
                                $row = is_array($row) ? $row : (array) $row;
                                ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo !empty($row['repair_date']) ? date('d-M-Y', strtotime($row['repair_date'])) : '—'; ?></td>
                            <td><?php echo htmlspecialchars($row['contract_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['vendor_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['source_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['servicenow_ref'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge" style="background-color:<?php echo htmlspecialchars($row['background_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8'); ?>;color:<?php echo htmlspecialchars($row['color'] ?? '#fff', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['status_name'] ?? 'Active', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <?php
                                $repairEditUrl = 'index.php?option=COM_INSERT_AMC_REPAIR_ORDER&Rid=' . deocdeId('id:' . ($row['id'] ?? ''));
                                if (!empty($row['schedule_id'])) {
                                    $repairEditUrl .= '&schedule_id=' . (int) $row['schedule_id'];
                                }
                                ?>
                            <td>
                                <a href="<?php echo htmlspecialchars($repairEditUrl, ENT_QUOTES, 'UTF-8'); ?>" title="Edit"><i class="ti-pencil text-info"></i></a>
                            </td>
                        </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
