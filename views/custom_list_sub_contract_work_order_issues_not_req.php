<?php
$issues_list = array();
$api_error_message = '';

$url = API . "?mode=getsubcontractworkorderissues&method=get";
$api_response = callAPI($url);
$api_data = json_decode($api_response, 1);

if($api_data && $api_data['status'] == 'success'){
    $issues_list = $api_data['issues_list'] ?? array();
} else {
    $api_error_message = $api_data['message'] ?? 'Failed to load issue records';
}
?>

<link rel="stylesheet" href="assets/css/custom_list_sub_contract_work_order_issues.css">

<div class="page-body">
    <div class="card issue-list-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Sub Contract Work Order Issues</h5>
            <span class="badge badge-primary issue-count-badge"><?php echo count((array)$issues_list); ?></span>
        </div>
        <div class="card-body">
            <?php if($api_error_message != '') { ?>
                <div class="alert alert-danger mb-3">
                    <?php echo $api_error_message; ?>
                </div>
            <?php } ?>

            <div class="table-responsive">
                <table class="table table-hover table-bordered issue-list-table nowrap">
                    <thead>
                        <tr>
                            <th scope="col">Sl No.</th>
                            <th scope="col">Issue No</th>
                            <th scope="col">Issue Date</th>
                            <th scope="col">Ref No</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Product</th>
                            <th scope="col">Request Warehouse</th>
                            <th scope="col">Party Warehouse</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count((array)$issues_list) > 0) {
                            $i = 1;
                            foreach($issues_list as $row) {
                                $status_class = 'badge-primary';
                                $status_label = 'Active';
                                if(isset($row['current_status']) && $row['current_status'] == ST_APPROVED){
                                    $status_class = 'badge-success';
                                    $status_label = 'Approved';
                                } elseif(isset($row['current_status']) && $row['current_status'] == ST_REJECTED){
                                    $status_class = 'badge-danger';
                                    $status_label = 'Rejected';
                                } elseif(isset($row['current_status']) && $row['current_status'] != ST_ACTIVE){
                                    $status_class = 'badge-warning';
                                    $status_label = 'In Progress';
                                }
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row['transaction_number'] != '' ? $row['transaction_number'] : 'ISSUE-' . $row['id']; ?></td>
                                <td><?php echo $row['transaction_date'] != '' ? fromDate($row['transaction_date']) : ''; ?></td>
                                <td><?php echo $row['ref_no'] ?? ''; ?></td>
                                <td><?php echo $row['party_name'] ?? ''; ?></td>
                                <td><?php echo $row['product_name'] ?? ''; ?></td>
                                <td><?php echo $row['request_warehouse_name'] ?? ''; ?></td>
                                <td><?php echo $row['party_warehouse_name'] ?? ''; ?></td>
                                <td class="text-right"><?php echo number_format($row['work_order_total_amount'] ?? 0, 2); ?></td>
                                <td><span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
                                <td>
                                    <a class="issue-action-link" target="_blank" href="index.php?option=COM_VIEW_SUB_CONTRACT_WORK_ORDER_ISSUES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>" title="View Details">
                                        <i class="fa fa-eye p-2 b-radius-5"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="11">
                                    <div class="issue-empty-state text-center">
                                        <p class="text-muted mb-2">No issue records found</p>
                                        <img src="assets/images/work.png" alt="No records" class="issue-empty-image">
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

<script src="assets/js/custom_list_sub_contract_work_order_issues.js"></script>
