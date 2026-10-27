<?php
$_SESSION['apply_js']['multiselest'] = "1";
// Load dropdown list on initial page load
$dropdown_list = array();
$parent_record = array();
$child_items = array();
$issues_history = array();
$issue_history_details = array();
$error_message = '';

// Fetch dropdown list
$url = API . "?mode=getissuerawmaterialstovendor";
$api_response = callAPI($url);
$dropdown_data = json_decode($api_response, 1);

if($dropdown_data && $dropdown_data['status'] == 'success'){
    $dropdown_list = $dropdown_data['subcontract_work_orders_list'];
}

if(isset($_POST['search_issue_materials']) && isset($_POST['subcontract_work_order_id']) && $_POST['subcontract_work_order_id'] != ""){
    $rid = $_POST['subcontract_work_order_id'];
    $url = API . "?mode=getissuerawmaterialstovendor&rid=" . $rid;
    $api_response = callAPI($url);
    $detail_data = json_decode($api_response, 1);

    if($detail_data && $detail_data['status'] == 'success'){
        $parent_record = $detail_data['parent'];
        $child_items = $detail_data['child_items'];
        $issues_history = $detail_data['issues_history'] ?? array();
        $issue_history_details = $detail_data['issue_history_details'] ?? array();
    } else {
        $error_message = $detail_data['message'] ?? 'Failed to load data';
    }
}
?>

<link rel="stylesheet" href="assets/css/custom_issue_raw_materials_to_vendor.css">
<script src="assets/js/custom_issue_raw_materials_to_vendor.js"></script>

<?php if($error_message != '') { ?>
    <div class="alert alert-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header table-card-header">
        <h5>Issue Raw Materials to Vendor</h5>
    </div>
    <div class="card-body">

        <form method="POST" action="" id="issueRawMaterialsForm">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <span class="form-label">Subcontract Work Order <span class="text-danger">*</span></span>
                        <select aria-label="Subcontract Work Order" name="subcontract_work_order_id" class="form-control form-select js-example-basic-single" required>
                            <option value="">Select Any One</option>
                            <?php if(count((array)$dropdown_list) > 0) { ?>
                                <?php foreach($dropdown_list as $wo) { ?>
                                    <option value="<?php echo $wo['id']; ?>"
                                        <?php echo (isset($_POST['subcontract_work_order_id']) && $_POST['subcontract_work_order_id'] == $wo['id']) ? 'selected' : ''; ?>>
                                        <?php echo $wo['transaction_number'] ; ?>
                                    </option>
                                <?php } ?>
                            <?php } else { ?>
                                <option value="" disabled>No approved work orders available</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <span class="form-label">&nbsp; &nbsp;</span>

                        <button type="submit" name="search_issue_materials" class="btn btn-primary border-radius-2 pb-2">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- SECTION 2: Parent Record Display -->
        <?php if(count((array)$parent_record) > 0) { ?>
        <div class="parent-record-section mt-5">
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="card-title border-bottom pb-2"><i class="icofont icofont-file-document"></i> Work Order Details</h6>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Product Name</strong></span>
                                <p class="mb-0"><?php echo $parent_record['product_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Transaction Number</strong></span>
                                <p class="mb-0"><?php echo $parent_record['transaction_number'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Transaction Date</strong></span>
                                <p class="mb-0"><?php echo ($parent_record['transaction_date'] ?? '') ? date('d-m-Y', strtotime($parent_record['transaction_date'])) : ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Vendor</strong></span>
                                <p class="mb-0"><?php echo $parent_record['party_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Request Warehouse</strong></span>
                                <p class="mb-0"><?php echo $parent_record['request_warehouse_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Party Warehouse</strong></span>
                                <p class="mb-0"><?php echo $parent_record['party_warehouse_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Item Price</strong></span>
                                <p class="mb-0"><?php echo number_format($parent_record['work_order_item_price'] ?? 0, 2); ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Total Quantity</strong></span>
                                <p class="mb-0"><?php echo $parent_record['work_order_item_qty'] ?? '0'; ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <span class="form-label"><strong>Total Amount</strong></span>
                                <p class="mb-0"><?php echo number_format($parent_record['work_order_total_amount'] ?? 0, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php if(isset($issues_history) && count((array)$issues_history) > 0) { ?>
                    <div class="issue-history-panel">
                        <h6 class="issue-history-title border-bottom pb-2 mb-2">
                            <i class="icofont icofont-history"></i> Issue History
                            <span class="badge badge-primary ml-2"><?php echo count($issues_history); ?></span>
                        </h6>
                        <?php foreach($issues_history as $hist_idx => $hist) {
                            $hist_status_class = 'badge-primary';
                            $hist_status_label = 'Active';
                            if(isset($hist['current_status']) && $hist['current_status'] == ST_APPROVED){
                                $hist_status_class = 'badge-success'; $hist_status_label = 'Approved';
                            } elseif(isset($hist['current_status']) && $hist['current_status'] == ST_REJECTED){
                                $hist_status_class = 'badge-danger'; $hist_status_label = 'Rejected';
                            } elseif(isset($hist['current_status']) && $hist['current_status'] != ST_ACTIVE){
                                $hist_status_class = 'badge-warning'; $hist_status_label = 'In Progress';
                            }
                            $hist_issue_no = ($hist['transaction_number'] != '') ? $hist['transaction_number'] : 'ISSUE-' . $hist['id'];
                        ?>
                        <div class="issue-history-item">
                            <div class="issue-history-header" data-action="toggle-history" data-index="<?php echo $hist_idx; ?>">
                                <span class="issue-history-arrow"><i class="fa fa-chevron-down"></i></span>
                                <div class="issue-history-summary">
                                    <div class="issue-history-summary-top">
                                        <span class="issue-history-no"><?php echo $hist_issue_no; ?></span>
                                        <span class="badge <?php echo $hist_status_class; ?> issue-history-status"><?php echo $hist_status_label; ?></span>
                                    </div>
                                    <div class="issue-history-meta">
                                        <span><strong>Date:</strong> <?php echo ($hist['transaction_date'] ?? '') ? date('d-m-Y', strtotime($hist['transaction_date'])) : ''; ?></span>
                                        <span><strong>Vendor:</strong> <?php echo $hist['party_name'] ?? ''; ?></span>
                                        <span><strong>Qty:</strong> <?php echo $hist['work_order_item_qty'] ?? '0'; ?></span>
                                        <span><strong>Amount:</strong> <?php echo number_format($hist['work_order_total_amount'] ?? 0, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="issue-history-body" id="issue-history-body-<?php echo $hist_idx; ?>" style="display:none;">
                                <?php $history_issue_items = $issue_history_details[$hist['id']] ?? array(); ?>
                                <?php if(count((array)$history_issue_items) > 0) { ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm issue-history-items-table mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Item</th>
                                                <th scope="col">SKU</th>
                                                <th scope="col">UOM</th>
                                                <th scope="col" class="text-right">Req Qty</th>
                                                <th scope="col" class="text-right">Issued Qty</th>
                                                <th scope="col" class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($history_issue_items as $history_item) { ?>
                                            <tr>
                                                <td><?php echo $history_item['item_name'] ?? ''; ?></td>
                                                <td><?php echo $history_item['sku'] ?? ''; ?></td>
                                                <td><?php echo $history_item['uom_name'] ?? ''; ?></td>
                                                <td class="text-right"><?php echo $history_item['item_quantity'] ?? '0'; ?></td>
                                                <td class="text-right"><?php echo $history_item['issued_quantity'] ?? '0'; ?></td>
                                                <td class="text-right"><?php echo number_format($history_item['issued_amount'] ?? $history_item['total_amount'] ?? 0, 2); ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } else { ?>
                                <p class="text-muted small mb-0">No issue items found for this record.</p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } elseif(isset($issues_history)) { ?>
                    <div class="issue-history-panel">
                        <h6 class="issue-history-title border-bottom pb-2 mb-2">
                            <i class="icofont icofont-history"></i> Issue History
                        </h6>
                        <p class="text-muted small">No previous issues found for this work order.</p>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- SECTION 3: Child Items Table -->
        <?php if(count((array)$child_items) > 0) { ?>
        <div class="child-items-section mt-5">
            <div class="row mb-3">
                <div class="col-12">
                    <h6 class="card-title border-bottom pb-2"><i class="icofont icofont-list"></i> Raw Materials to Issue</h6>
                </div>
            </div>

            <form id="issueItemsForm">
                <input type="hidden" name="page" value="submit_issue_raw_materials_to_vendor">
                <input type="hidden" name="rid" value="<?php echo $parent_record['id'] ?? ''; ?>">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 20%;">Product Name</th>
                            <th scope="col" style="width: 10%;">SKU</th>
                            <th scope="col" style="width: 10%;">UOM</th>
                            <th scope="col" style="width: 10%;">Item Qty</th>
                            <th scope="col" style="width: 10%;">Item Price</th>
                            <th scope="col" style="width: 10%;">Total Amount</th>
                            <th scope="col" style="width: 10%;">Pending Qty</th>
                            <th scope="col" style="width: 10%;">Issue Qty</th>
                            <th scope="col" style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($child_items as $item) { ?>
                        <tr>
                            <td><?php echo $item['item_name'] ?? ''; ?></td>
                            <td><small><?php echo $item['sku'] ?? ''; ?></small></td>
                            <td><?php echo $item['uom_name'] ?? ''; ?></td>
                            <td class="text-right"><?php echo $item['item_quantity'] ?? '0'; ?></td>
                            <td class="text-right"><?php echo number_format($item['item_price'] ?? 0, 2); ?></td>
                            <td class="text-right"><?php echo number_format($item['total_amount'] ?? 0, 2); ?></td>
                            <td class="text-right"><?php echo $item['pending_qty'] ?? '0'; ?></td>
                            <td>
                                <input type="hidden" name="id[]" class="issue-child-id" value="<?php echo $item['id'] ?? ''; ?>">
                                <input type="hidden" name="item_id[]" class="issue-item-id" value="<?php echo $item['item_id'] ?? ''; ?>">
                                <input aria-label="Issues Qty[]" type="number" step="0.01" min="0" name="issues_qty[]" class="form-control issue-qty-input text-right" value="" data-pending-qty="<?php echo (float)($item['pending_qty'] ?? 0); ?>" max="<?php echo (float)($item['pending_qty'] ?? 0); ?>" <?php echo ((float)($item['pending_qty'] ?? 0) == 0) ? 'disabled' : ''; ?>>
                            </td>
                            <td>
                                <?php
                                    $pending   = (float)($item['pending_qty'] ?? 0);
                                    $item_qty  = (float)($item['item_quantity'] ?? 0);
                                    if($pending == $item_qty) {
                                        echo '<span class="badge bg-warning">Pending</span>';
                                    } elseif($pending > 0 && $pending < $item_qty) {
                                        echo '<span class="badge bg-info">Partially Issued</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Issued</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-start">
                <button type="submit" id="submitIssueItemsBtn" class="btn btn-primary border-radius-2"><i class="ti-check"></i> Submit</button>
            </div>
            </form>
        </div>
        <?php } ?>

    </div>
</div>
