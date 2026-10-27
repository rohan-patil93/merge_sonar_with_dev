<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$post = $_POST;
$post['admin_type'] = $_SESSION['admin_type'];
$post['user_id'] = $_SESSION['userid'];
$post['company_id'] = $_SESSION['company_id'];
$post['api_type'] = 'listpage';
$url = API . "?mode=getcontractslistpost&method=post";
$api = file_post_contents($url, $post, 'update');
$result = json_decode($api, 1);
$payment_terms = $result['payment_terms'];
$locations = $result['locations'];
$agreement_type = $result['agreement_type'];
$departments = $result['departments'];
$contract_types = $result['contract_types'];
$vendors = $result['vendors'];
$stausresult = ViewStatusData();
$contracttodaydate = new DateTime();
$contracts = $result['contracts'];
$company_name = $_SESSION['config']['name'] ?? '';
$generated_at = date('d-m-Y H:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contracts - InfiSuite</title>
    <meta charset="UTF-8">
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; padding: 10px; color: #333; }
        h3 { margin: 0 0 4px 0; font-size: 14px; }
        .meta { margin: 0 0 10px 0; font-size: 9px; color: #666; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; color: #fff; font-size: 8px; }
        .badge-amc { background: #17a2b8; }
        .badge-standard { background: #6c757d; }
        .badge-active { background: #17a2b8; }
        .badge-expired { background: #dc3545; }
        .empty-msg { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <h3>Contracts List</h3>
    <?php if ($company_name != "") { ?>
        <p class="meta">Company: <?php echo htmlspecialchars($company_name); ?> | Generated: <?php echo $generated_at; ?></p>
    <?php } else { ?>
        <p class="meta">Generated: <?php echo $generated_at; ?></p>
    <?php } ?>
<?php if (count((array)$contracts) > 0) { ?>
    <table>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Contract No.</th>
                <th scope="col">Contract Type</th>
                <th scope="col">Contract Name</th>
                <th scope="col">Vendor</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Agreements Type</th>
                <th scope="col">Location</th>
                <th scope="col">Department</th>
                <th scope="col">Contract Category</th>
                <th scope="col">Payment Terms</th>
                <th scope="col">Status</th>
                <th scope="col">Approval Status</th>
                <th scope="col">View Schedule</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sq = 1;
        foreach ($contracts as $contra) {
            $isAmc = ($contra['is_amc_contract'] ?? 'N') == 'Y';
            $locationsid = json_decode($contra['locations'], 1);
            $locationnames = "";
            if (count((array)$locationsid) > 0) {
                foreach ($locationsid as $locid) {
                    $locationnames .= $locations[$locid]['location_name'] . ", ";
                }
            }
            $transaction_no = $contra['transaction_no'] != "" ? $contra['transaction_no'] : "-";
            $contract_name = $contra['contract_name'] != "" ? $contra['contract_name'] : "-";
            $vendor_name = $vendors[$contra['party_id']]['company'] != "" ? $vendors[$contra['party_id']]['company'] : "-";
            $agreement_name = $agreement_type[$contra['typeofaggrements']]['genc_detail_name'] != "" ? $agreement_type[$contra['typeofaggrements']]['genc_detail_name'] : "-";
            $department_name = $departments[$contra['department_id']]['department_name'] != "" ? $departments[$contra['department_id']]['department_name'] : "-";
            $category_name = $contract_types[$contra['type_of_contract_id']]['genc_detail_name'] != "" ? $contract_types[$contra['type_of_contract_id']]['genc_detail_name'] : "-";
            $payment_name = $payment_terms[$contra['contract_payment_terms']]['payment_term_name'] != "" ? $payment_terms[$contra['contract_payment_terms']]['payment_term_name'] : "-";
            $is_expired = ($contra['end_date'] && new DateTime($contra['end_date']) < $contracttodaydate);
            $approval_color = $stausresult[$contra['current_status']]['color'];
            $approval_bg = $stausresult[$contra['current_status']]['background_color'];
            $approval_name = $stausresult[$contra['current_status']]['name'];
        ?>
            <tr>
                <td><?php echo $sq; ?></td>
                <td><?php echo htmlspecialchars($transaction_no); ?></td>
                <td>
                    <?php if ($isAmc) { ?>
                        <span class="badge badge-amc">AMC</span>
                    <?php } else { ?>
                        <span class="badge badge-standard">Standard</span>
                    <?php } ?>
                </td>
                <td><?php echo htmlspecialchars($contract_name); ?></td>
                <td><?php echo htmlspecialchars($vendor_name); ?></td>
                <td><?php echo fromDate($contra['start_date']); ?></td>
                <td><?php echo fromDate($contra['end_date']); ?></td>
                <td><?php echo htmlspecialchars($agreement_name); ?></td>
                <td><?php echo htmlspecialchars($locationnames); ?></td>
                <td><?php echo htmlspecialchars($department_name); ?></td>
                <td><?php echo htmlspecialchars($category_name); ?></td>
                <td><?php echo htmlspecialchars($payment_name); ?></td>
                <td>
                    <?php if ($is_expired) { ?>
                        <span class="badge badge-expired">Expired</span>
                    <?php } else { ?>
                        <span class="badge badge-active">Active</span>
                    <?php } ?>
                </td>
                <td>
                    <span class="badge" style="color: <?php echo $approval_color; ?>; background: <?php echo $approval_bg; ?>;"><?php echo htmlspecialchars($approval_name); ?></span>
                </td>
                <td><?php echo $isAmc ? "AMC" : "—"; ?></td>
            </tr>
        <?php
            $sq++;
        }
        ?>
        </tbody>
    </table>
<?php } else { ?>
    <p class="empty-msg">No Contracts</p>
<?php } ?>
</body>
</html>
