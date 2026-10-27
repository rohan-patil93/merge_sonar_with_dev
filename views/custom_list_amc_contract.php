<?php
$company_id = $_SESSION['company_id'] ?? 0;
$url = API . "?mode=getcontractslistamc&method=get&company_id=" . (int) $company_id;
$response = callAPI($url);
$result = json_decode($response, true);

$active_contracts = $result['active_contracts'] ?? array();
$inactive_contracts = $result['inactive_contracts'] ?? array();
$active_count = $result['active_count'] ?? 0;
$inactive_count = $result['inactive_count'] ?? 0;
?>

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0">Contracts</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <ul class="nav md-tabs infisuite_tab mb-0">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#contracts_active" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            Active
                            <span class="form-label label label-xs label-success m-l-10"><?php echo (int) $active_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#contracts_inactive" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            Inactive
                            <span class="form-label label label-xs label-success m-l-10"><?php echo (int) $inactive_count; ?></span>
                        </a>
                    </li>
                </ul>
                <a href="index.php?option=COM_INSERT_CONTRACTS" class="btn btn-success btn-sm desktop-view">
                    <i class="icofont icofont-plus icon-white"></i> Add Contract
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light btn-sm desktop-view">
                    <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="tab-content tabs card-block mt-2">
            <?php
            $renderTable = function ($rows, $emptyMsg) {
                ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm f-12">
                        <thead>
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Contract Name</th>
                                <th scope="col">Contract No.</th>
                                <th scope="col">Type</th>
                                <th scope="col">Party</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count((array) $rows) > 0) {
                                $i = 1;
                                foreach ($rows as $row) {
                                    if (is_object($row)) {
                                        $row = json_decode(json_encode($row), true);
                                    }
                                    $isAmc = ($row['is_amc_contract'] ?? 'N') === 'Y';
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo htmlspecialchars($row['contract_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['transaction_no'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($isAmc) { ?>
                                                <span class="badge bg-info">AMC</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Standard</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $partyType = ucfirst($row['party_type'] ?? '');
                                            $partyName = $row['party_name'] ?? '';
                                            echo htmlspecialchars(trim($partyType . ($partyName ? ': ' . $partyName : '')) ?: '-');
                                            ?>
                                        </td>
                                        <td><?php echo !empty($row['start_date']) ? fromDate($row['start_date']) : '-'; ?></td>
                                        <td><?php echo !empty($row['end_date']) ? fromDate($row['end_date']) : '-'; ?></td>
                                        <td><?php echo isset($row['contract_amount']) && $row['contract_amount'] !== '' ? htmlspecialchars($row['contract_amount']) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars(contractCurrentStatusLabel($row['current_status'] ?? 0, $row['cancel_status'] ?? 'N')); ?></td>
                                        <td>
                                            <a href="index.php?option=COM_INSERT_AMC_CONTRACT&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="Open Contract">
                                                <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4"><?php echo htmlspecialchars($emptyMsg); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
            };
            ?>
            <div class="tab-pane active" id="contracts_active" role="tabpanel">
                <div class="card-body p-3">
                    <?php $renderTable($active_contracts, 'No active contracts found.'); ?>
                </div>
            </div>
            <div class="tab-pane" id="contracts_inactive" role="tabpanel">
                <div class="card-body p-3">
                    <?php $renderTable($inactive_contracts, 'No inactive contracts found.'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
