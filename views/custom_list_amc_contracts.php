<?php
$company_id = $_SESSION['company_id'] ?? 0;
echo $url = API . "?mode=getcontractslistamc&method=get&company_id=" . (int) $company_id;
$response = callAPI($url);
$result = json_decode($response, true);

$active_contracts = $result['active_contracts'] ?? array();
$inactive_contracts = $result['inactive_contracts'] ?? array();
$active_count = $result['active_count'] ?? 0;
$inactive_count = $result['inactive_count'] ?? 0;

if (($_SESSION['insert_msg'] ?? '') != '') {
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = '';
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
            }
        }
    </script>
    <?php
}
if (($_SESSION['amc_schedule_warning'] ?? '') != '') {
    $scheduleWarnMsg = $_SESSION['amc_schedule_warning'];
    $_SESSION['amc_schedule_warning'] = '';
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify('top', 'right', 'fa fa-exclamation-triangle', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', <?php echo json_encode($scheduleWarnMsg); ?>, '');
            }
        }
    </script>
    <?php
}
?>

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0"><?= __('Contracts'); ?></h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <ul class="nav md-tabs infisuite_tab mb-0">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#contracts_active" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <?= __('Active'); ?>
                            <span class="form-label label label-xs label-success m-l-10"><?php echo (int) $active_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#contracts_inactive" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <?= __('Inactive'); ?>
                            <span class="form-label label label-xs label-success m-l-10"><?php echo (int) $inactive_count; ?></span>
                        </a>
                    </li>
                </ul>
                <a href="index.php?option=COM_INSERT_AMC_CONTRACT" class="btn btn-success btn-sm desktop-view">
                    <i class="icofont icofont-plus icon-white"></i> <?= __('Add Contract'); ?>
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light btn-sm desktop-view">
                    <i class="icofont icofont-reply"></i> <?= __('Back'); ?>
                </a>
            </div>
        </div>
        <div class="tab-content tabs card-block mt-2">
            <?php
            if (!function_exists('amcContractsGetPartyDisplay')) {
                function amcContractsGetPartyDisplay($row) {
                    $partyType = ucfirst($row['party_type'] ?? '');
                    $partyName = $row['party_name'] ?? '';
                    $partyLabel = $partyType;
                    if ($partyName !== '') {
                        $partyLabel .= ': ' . $partyName;
                    }
                    $partyDisplay = trim($partyLabel);
                    return ($partyDisplay !== '') ? $partyDisplay : '-';
                }
            }

            if (!function_exists('amcContractsRenderSingleRow')) {
                function amcContractsRenderSingleRow($row, $i) {
                    if (is_object($row)) {
                        $row = json_decode(json_encode($row), true);
                    }
                    $isAmc = ($row['is_amc_contract'] ?? 'N') === 'Y';
                    $startDate = !empty($row['start_date']) ? fromDate($row['start_date']) : '-';
                    $endDate = !empty($row['end_date']) ? fromDate($row['end_date']) : '-';
                    $amount = (isset($row['contract_amount']) && $row['contract_amount'] !== '') ? htmlspecialchars($row['contract_amount']) : '-';
                    $status = htmlspecialchars(contractCurrentStatusLabel($row['current_status'] ?? 0, $row['cancel_status'] ?? 'N'));
                    $partyDisplay = amcContractsGetPartyDisplay($row);
                    $editUrl = "index.php?option=COM_INSERT_AMC_CONTRACT&Rid=" . deocdeId('id:' . $row['id']);
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo htmlspecialchars($row['contract_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['transaction_no'] ?? '-'); ?></td>
                        <td>
                            <?php if ($isAmc) { ?>
                                <span class="badge bg-info"><?= __('AMC'); ?></span>
                            <?php } else { ?>
                                <span class="badge bg-secondary"><?= __('Standard'); ?></span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($partyDisplay); ?></td>
                        <td><?php echo $startDate; ?></td>
                        <td><?php echo $endDate; ?></td>
                        <td><?php echo $amount; ?></td>
                        <td><?php echo $status; ?></td>
                        <td>
                            <a href="<?php echo $editUrl; ?>" title="<?= htmlspecialchars(__('Open Contract'), ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            }

            $renderTable = function ($rows, $emptyMsg) {
                ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm f-12">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('S.No.'); ?></th>
                                <th scope="col"><?= __('Contract Name'); ?></th>
                                <th scope="col"><?= __('Contract No.'); ?></th>
                                <th scope="col"><?= __('Type'); ?></th>
                                <th scope="col"><?= __('Party'); ?></th>
                                <th scope="col"><?= __('Start Date'); ?></th>
                                <th scope="col"><?= __('End Date'); ?></th>
                                <th scope="col"><?= __('Amount'); ?></th>
                                <th scope="col"><?= __('Status'); ?></th>
                                <th scope="col"><?= __('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count((array) $rows) > 0) {
                                $i = 1;
                                foreach ($rows as $row) {
                                    amcContractsRenderSingleRow($row, $i++);
                                }
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
