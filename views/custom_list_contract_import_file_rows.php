<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $files = json_decode($_POST['file_data'], true);
    $payload = [
        'files' => $files,
    ];
    $url = API . "?mode=insertcontractdata&method=post";
    $response = file_post_contents($url, $payload, 'insert');
    $result = json_decode($response, true);


    $insertion_results = null;
    if (!empty($result['summary'])) {
        $insertion_results = $result['summary'];
    }

    if (!empty($result['error_message'])) {
        $_SESSION['upload_msg'] = $result['error_message'];
        $_SESSION['upload_msg_type'] = 'error';

        // Show immediate notification
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                tostarnotify(
                    "top",
                    "right",
                    "fa fa-exclamation-circle",
                    "form-label label label-danger",
                    "animated flipInX",
                    "animated flipOutX",
                    "' . addslashes($result['error_message']) . '",
                    ""
                );
            });
        </script>';
    }
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$vurl = API . "?mode=getcontractdata&method=get&rid=" . $rid;
$payload = ['files' => [['id' => $rid]]];
$che = file_post_contents($vurl, $payload, 'preview');
$return = json_decode($che, true);


$contract_rows = $return['contract_rows'] ?? [];
$recharge_rows = $return['recharge_rows'] ?? [];
$files_info = $return['crm_import_files'] ?? [];
$files = [['id' => $rid]];

$validation_summary = $return['validation_summary'] ?? [];
$contract_summary = $validation_summary['contract_summary'] ?? [];
$recharge_summary = $validation_summary['recharge_summary'] ?? [];

if (!empty($_SESSION['upload_msg'])) {
    $msgType = $_SESSION['upload_msg_type'];
    $msg = addslashes($_SESSION['upload_msg']);

    $_SESSION['upload_msg'] = '';
    $_SESSION['upload_msg_type'] = '';
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify(
                    'top',
                    'right',
                    'fa <?php echo ($msgType === "success") ? "fa-check-circle" : "fa-exclamation-circle"; ?>',
                    'form-label label label-<?php echo ($msgType === "success") ? "success" : "danger"; ?>',
                    'animated flipInX',
                    'animated flipOutX',
                    '<?php echo $msg; ?>',
                    ''
                );
            }
        };
    </script>
<?php
}
?>
<div class="card contract-import-container">
    <div class="card-header table-card-header">
        <h5>Contracts List</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($files_info)): ?>
        <div class="alert alert-info mb-3">
            <h6><i class="fa fa-file"></i> Import File Information:</h6>
            <?php foreach ($files_info as $file): ?>
            <p class="mb-1">
                <strong>File:</strong> <?php echo htmlspecialchars($file['file_name']); ?>
                <span class="text-muted">(<?php echo htmlspecialchars($file['original_file_name']); ?>)</span><br>
                <strong>Type:</strong> <?php echo htmlspecialchars($file['import_type']); ?> |
                <strong>Source:</strong> <?php echo htmlspecialchars($file['source_type']); ?>
            </p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($insertion_results)): ?>
        <div class="alert alert-success mb-3">
            <h6><i class="fa fa-check-circle"></i> Processing Results:</h6>
            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-success text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['contracts_processed'] ?? 0; ?></h4>
                            <small>Total Processed</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-info text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['new_contracts'] ?? 0; ?></h4>
                            <small>New Contracts</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-warning text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['skipped_contracts'] ?? 0; ?></h4>
                            <small>Skipped Contracts</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-secondary text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['recharges_inserted'] ?? 0; ?></h4>
                            <small>Recharges Added</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-primary text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['skipped_non_renewals'] ?? 0; ?></h4>
                            <small>Skipped Recharges</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="card bg-danger text-white mb-2">
                        <div class="card-body text-center p-2">
                            <h4><?php echo $insertion_results['failed'] ?? 0; ?></h4>
                            <small>Failed</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($result['errors'])): ?>
            <div class="mt-2">
                <h6><i class="fa fa-exclamation-triangle"></i> Errors:</h6>
                <ul class="mb-0">
                    <?php foreach ($result['errors'] as $error): ?>
                    <li class="text-danger"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($result['error_message'])): ?>
        <div class="alert alert-danger mb-3">
            <h6><i class="fa fa-exclamation-triangle"></i> Error:</h6>
            <p class="mb-0"><?php echo htmlspecialchars($result['error_message']); ?></p>
        </div>
        <?php endif; ?>

        <form id="salesForm" action="" method="post">
            <input type="hidden" name="contract_data" value='<?php echo htmlspecialchars(json_encode($contract_rows), ENT_QUOTES, 'UTF-8'); ?>' />
            <input type="hidden" name="recharge_data" value='<?php echo htmlspecialchars(json_encode($recharge_rows), ENT_QUOTES, 'UTF-8'); ?>' />
            <input type="hidden" name="file_data" value='<?php echo htmlspecialchars(json_encode($files), ENT_QUOTES, 'UTF-8'); ?>' />

            <?php if (!empty($validation_summary)): ?>
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5><?php echo $validation_summary['total_rows']; ?></h5>
                            <p>Total Rows</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5><?php echo $contract_summary['total_contract_rows'] ?? 0; ?></h5>
                            <p>Contract Rows</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5><?php echo $contract_summary['valid_contract_rows'] ?? 0; ?></h5>
                            <p>Valid Contracts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5><?php echo $recharge_summary['total_recharge_rows'] ?? 0; ?></h5>
                            <p>Recharge Rows</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5><?php echo $recharge_summary['valid_recharge_rows'] ?? 0; ?></h5>
                            <p>Valid Recharges</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5><?php echo $recharge_summary['duplicate_recharge_rows'] ?? 0; ?></h5>
                            <p>Duplicate Recharges</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5><?php echo $contract_summary['empty_field_contract_rows'] ?? 0; ?></h5>
                            <p>Contracts with Empty feilds</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5><?php echo $recharge_summary['empty_field_recharge_rows'] ?? 0; ?></h5>
                            <p>Recharges with Empty fields</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5><?php echo $recharge_summary['not_valid_recharge_rows'] ?? 0; ?></h5>
                            <p>No Valid Renewal Recharges</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            <div class="alert alert-info">
                <strong>Legend:</strong>
                <span class="badge bg-danger">Red rows</span> have empty fields or no valid renewal |
                <span class="badge bg-warning">Yellow rows</span> are duplicate contracts |
                <span class="badge bg-info">Blue rows</span> are duplicate recharges |
                <span class="badge bg-success">Green rows</span> are valid for processing
            </div>

            <?php if (!empty($contract_rows)): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Contract Rows (<?php echo count($contract_rows); ?>)</h5>
                    <small>These rows will create new contracts</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="f-14" style="background-color: #eeeeee;">
                                <tr>
                                    <th scope="col">Sr.No</th>
                                    <th scope="col">Contract Name</th>
                                    <th scope="col">Circuit ID</th>
                                    <th scope="col">RT Number</th>
                                    <th scope="col">Invoice Number</th>
                                    <th scope="col">Party Type</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Row Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($contract_rows as $row) {
                                    $rowClass = '';
                                    $statusBadge = '';

                                    if ($row['row_status'] == 'empty_fields') {
                                        $rowClass = 'table-danger';
                                        $statusBadge = '<span class="badge bg-danger">Empty Fields</span>';
                                    } elseif ($row['row_status'] == 'duplicate') {
                                        $rowClass = 'table-warning';
                                        $statusBadge = '<span class="badge bg-warning">Duplicate</span>';
                                    } else {
                                        $rowClass = 'table-success';
                                        $statusBadge = '<span class="badge bg-success">Valid</span>';
                                    }


                                    echo "<tr class='{$rowClass
}'>
                                        <td>{$i
}</td>
                                        <td>" . htmlspecialchars($row['contract_name'] ?? '') . "</td>
                                        <td>" . htmlspecialchars($row['circuit_id'] ?? '') . "</td>
                                        <td>" . htmlspecialchars($row['rt_number'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($row['invoice_number'] ?? '') . "</td>
                                        <td>" . htmlspecialchars($row['party_type'] ?? '') . "</td>
                                        <td>" . htmlspecialchars(fromDate($row['start_date'] ?? '')) . "</td>
                                        <td>" . htmlspecialchars(fromDate($row['end_date'] ?? '')) . "</td>
                                        <td>" . htmlspecialchars($row['status'] ?? '') . "</td>
                                        <td>{$statusBadge
}</td>
                                    </tr>";
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($recharge_rows)): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Recharge Rows (<?php echo count($recharge_rows); ?>)</h5>
                    <small>These rows will create recharges for existing contracts</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="f-14" style="background-color: #eeeeee;">
                                <tr>
                                    <th scope="col">Sr.No</th>
                                    <th scope="col">Circuit ID</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Validity</th>
                                    <th scope="col">Total Payable</th>
                                    <th scope="col">Row Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($recharge_rows as $row) {
                                    $rowClass = '';
                                    $statusBadge = '';

                                    if ($row['row_status'] == 'empty_fields') {
                                        $rowClass = 'table-danger';
                                        $statusBadge = '<span class="badge bg-danger">Empty Fields</span>';
                                    } elseif ($row['row_status'] == 'duplicate_recharge') {
                                        $rowClass = 'table-info';
                                        $statusBadge = '<span class="badge bg-info">Duplicate Recharge</span>';
                                    } elseif ($row['row_status'] == 'no_valid_renewal') {
                                        $rowClass = 'table-danger';
                                        $statusBadge = '<span class="badge bg-danger">No Valid Renewal</span>';
                                    } else {
                                        $rowClass = 'table-success';
                                        $statusBadge = '<span class="badge bg-success">Valid</span>';
                                    }

                                    echo "<tr class='{$rowClass
}'>
                                        <td>{$i
}</td>
                                        <td>" . htmlspecialchars($row['circuit_id'] ?? '') . "</td>
                                        <td>" . htmlspecialchars(fromDate($row['start_date'] ?? '')) . "</td>
                                        <td>" . htmlspecialchars(fromDate($row['end_date'] ?? '')) . "</td>
                                        <td>" . htmlspecialchars($row['status'] ?? '') . "</td>
                                        <td>" . htmlspecialchars($row['validity'] ?? '') . "</td>
                                        <td>" . htmlspecialchars($row['total_payable'] ?? '') . "</td>
                                        <td>{$statusBadge
}</td>
                                    </tr>";
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($contract_rows) && empty($recharge_rows)): ?>
            <div class="alert alert-warning">
                <h5>No Data Available</h5>
                <p>No contract or recharge data found for validation.</p>
            </div>
            <?php endif; ?>
            <div class="text-right mt-3">
                <?php
                $total_valid = ($contract_summary['valid_contract_rows'] ?? 0) + ($recharge_summary['valid_recharge_rows'] ?? 0);
                $total_empty_contracts = $contract_summary['empty_field_contract_rows'] ?? 0;
                $total_empty_recharges = $recharge_summary['empty_field_recharge_rows'] ?? 0;
                $total_empty = $total_empty_contracts + $total_empty_recharges;
                $total_duplicate_recharges = $recharge_summary['duplicate_recharge_rows'] ?? 0;
                $total_no_valid_renewal = $recharge_summary['not_valid_recharge_rows'] ?? 0;
                $total_issues = $total_empty + $total_duplicate_recharges + $total_no_valid_renewal;

                $can_process = ($total_valid > 0) && ($total_issues == 0);
                ?>

                <!-- Status message -->
                <div class="alert <?php echo $can_process ? 'alert-success' : 'alert-warning'; ?> mb-3">
                    <?php if ($can_process): ?>
                        <i class="fa fa-check-circle"></i> <strong>Ready to Process:</strong> All <?php echo $total_valid; ?> rows are valid and ready for processing.
                    <?php else: ?>
                        <i class="fa fa-exclamation-triangle"></i> <strong>Cannot Process:</strong>
                        <?php if ($total_empty > 0): ?>
                            Found <?php echo $total_empty_contracts; ?> contracts and <?php echo $total_empty_recharges; ?> recharges with empty fields.
                        <?php endif; ?>
                        <?php if ($total_duplicate_recharges > 0): ?>
                            <?php echo $total_empty > 0 ? ' Also found' : 'Found'; ?> <?php echo $total_duplicate_recharges; ?> duplicate recharges.
                        <?php endif; ?>
                        <?php if ($total_no_valid_renewal > 0): ?>
                            <?php echo ($total_empty > 0 || $total_duplicate_recharges > 0) ? ' Also found' : 'Found'; ?> <?php echo $total_no_valid_renewal; ?> recharges with no valid renewal. Please remove these recharges before processing.
                        <?php endif; ?>
                        <?php if ($total_valid == 0): ?>
                            No valid rows found to process.
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($can_process): ?>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa fa-check"></i> Process All Valid Rows (<?php echo $total_valid; ?>)
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                        <i class="fa fa-times"></i> Cannot Process - Fix Issues First
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cells = document.querySelectorAll('td');
    cells.forEach(cell => {
        if (cell.textContent.trim() === '' || cell.textContent.trim() === '-' || cell.textContent.trim() === 'N/A') {
            cell.style.backgroundColor = '#ffebee';
            cell.style.border = '1px solid #f44336';
        }
    });
});
</script>
