<?php
$_SESSION['apply_js']['multiselest'] = "1";

$error_message = '';
$dropdown_list = array();
$work_order_details = array();

if (isset($_POST['sbt_subcontractworkorderreceipt'])) {
    $url = API . "?mode=savesubcontractworkorderreceipt&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
    $response = isset($response2['parent_insert_update']) ? $response2['parent_insert_update'] : array();

    if (isset($response['status']) && $response['status'] == 'success') {
        if ($response['LASTID'] == 0) {
            $_SESSION['insert_msg'] = 3001;
        } else {
            $_SESSION['insert_msg'] = 3000;
        }
        header("location:index?option=COM_INSERT_SUBCONTRACT_WORK_ORDER_RECEIPTS");
        exit;
    } else {
        $error_message = isset($response['message']) ? $response['message'] : 'Unable to save receipt';
    }
}

$selected_work_order_id = isset($_POST['subcontract_work_order_id']) ? $_POST['subcontract_work_order_id'] : '';
$api_url = API . "?mode=getsubcontractworkorderreceipts";
if ($selected_work_order_id != '') {
    $api_url .= "&work_order_id=" . $selected_work_order_id;
}
$api_response = callAPI($api_url);
$api_data = json_decode($api_response, 1);

if ($api_data && isset($api_data['status']) && $api_data['status'] == 'success') {
    $dropdown_list = isset($api_data['subcontract_work_orders_list']) ? $api_data['subcontract_work_orders_list'] : array();
    $work_order_details = isset($api_data['work_order_details']) ? $api_data['work_order_details'] : array();
} elseif (!$api_data) {
    $error_message = $error_message != '' ? $error_message : 'Failed to load receipt data';
} else {
    $error_message = $error_message != '' ? $error_message : ($api_data['message'] ?? 'Failed to load receipt data');
}

$display_transaction_number = '';
$display_transaction_date = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : fromDate();
$display_received_qty = isset($_POST['received_qty']) ? $_POST['received_qty'] : '';
$display_notes = isset($_POST['notes']) ? $_POST['notes'] : '';
$display_receipt_amount = isset($_POST['receipt_amount']) ? $_POST['receipt_amount'] : '0.00';
$display_receipt_tax_amount = isset($_POST['receipt_tax_amount']) ? $_POST['receipt_tax_amount'] : '0.00';
$display_receipt_total_amount = isset($_POST['receipt_total_amount']) ? $_POST['receipt_total_amount'] : '0.00';
$display_receipt_tax_type = isset($_POST['receipt_tax_type']) ? $_POST['receipt_tax_type'] : '';
$display_receipt_tax_split_details = isset($_POST['receipt_tax_split_details']) ? $_POST['receipt_tax_split_details'] : '';
$display_receipt_gst_percentage = isset($_POST['receipt_gst_percentage']) ? $_POST['receipt_gst_percentage'] : '0.00';
$display_receipt_hsn_code_id = isset($_POST['receipt_hsn_code_id']) ? $_POST['receipt_hsn_code_id'] : '0';
$display_receipt_hsn_code = isset($_POST['receipt_hsn_code']) ? $_POST['receipt_hsn_code'] : '';
?>

<link rel="stylesheet" href="assets/css/custom_insert_subcontract_work_order_receipts.css">
<script type="text/javascript" src="assets/js/custom_insert_subcontract_work_order_receipts.js"></script>

<?php if ($error_message != '') { ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="card receipt-card">
    <div class="card-header table-card-header">
        <h5>Receive Finished Goods</h5>
        <div class="card-header-right table-card-header">
            <button type="button" class="btn waves-effect waves-light receipt-history-btn" data-action="open-receipt-history" data-work-order-id="<?php echo $selected_work_order_id; ?>" <?php echo $selected_work_order_id == '' ? 'disabled' : ''; ?> title="Receipt History">
                <i class="ti-time"></i>
            </button>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="" id="subcontractWorkOrderReceiptSearchForm">
            <div class="row">
                <div class="col-lg-3">
                <div class="form-group">
                    <span class="form-label">Subcontract Work Order <span class="text-danger">*</span></span>
                    <select aria-label="Subcontract Work Order" name="subcontract_work_order_id" id="subcontract_work_order_id" class="form-control form-select js-example-basic-single" required>
                        <option value="">Select Any One</option>
                        <?php foreach ((array)$dropdown_list as $wo) { ?>
                            <option value="<?php echo $wo['id']; ?>" <?php echo $selected_work_order_id == $wo['id'] ? 'selected' : ''; ?>>
                                <?php echo $wo['transaction_number']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <span class="form-label">&nbsp; &nbsp;</span>
                        <button type="submit" name="search_subcontractworkorderreceipt" class="btn btn-primary border-radius-2 pb-2 d-block">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <?php if (count((array)$work_order_details) > 0) { ?>
            <form method="POST" action="" id="subcontractWorkOrderReceiptForm" class="mt-4">
                <input type="hidden" name="page" value="submit_subcontract_work_order_receipt">
                <input type="hidden" name="sbt_subcontractworkorderreceipt" value="1">
                <input type="hidden" name="subcontract_work_order_id" value="<?php echo $selected_work_order_id; ?>">
                <input type="hidden" name="work_order_item_price" id="work_order_item_price" value="<?php echo isset($work_order_details['work_order_item_price']) ? $work_order_details['work_order_item_price'] : 0; ?>">
                <input type="hidden" name="work_order_item_qty" id="work_order_item_qty" value="<?php echo isset($work_order_details['work_order_item_qty']) ? $work_order_details['work_order_item_qty'] : 0; ?>">
                <input type="hidden" id="pending_receipt_qty" value="<?php echo isset($work_order_details['pending_receipt_qty']) ? $work_order_details['pending_receipt_qty'] : 0; ?>">
                <input type="hidden" name="receipt_amount" id="receipt_amount" value="<?php echo $display_receipt_amount; ?>">
                <input type="hidden" name="receipt_tax_amount" id="receipt_tax_amount" value="<?php echo $display_receipt_tax_amount; ?>">
                <input type="hidden" name="receipt_total_amount" id="receipt_total_amount" value="<?php echo $display_receipt_total_amount; ?>">
                <input type="hidden" name="receipt_tax_type" id="receipt_tax_type" value="<?php echo $display_receipt_tax_type; ?>">
                <input type="hidden" name="receipt_tax_split_details" id="receipt_tax_split_details" value='<?php echo htmlentities($display_receipt_tax_split_details, ENT_QUOTES); ?>'>
                <input type="hidden" name="receipt_gst_percentage" id="receipt_gst_percentage" value="<?php echo $display_receipt_gst_percentage; ?>">
                <input type="hidden" name="receipt_hsn_code_id" id="receipt_hsn_code_id" value="<?php echo $display_receipt_hsn_code_id; ?>">
                <input type="hidden" name="receipt_hsn_code" id="receipt_hsn_code" value="<?php echo $display_receipt_hsn_code; ?>">

                <div class="row">
                    <div class="form-group col-lg-4">
                        <span class="form-label">Receipt Date <span class="text-danger">*</span></span>
                        <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date" value="<?php echo $display_transaction_date; ?>" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Selected Work Order</span>
                        <input aria-label="Input field" type="text" class="form-control" value="<?php echo isset($work_order_details['transaction_number']) ? $work_order_details['transaction_number'] : ''; ?>" readonly>
                    </div>
                </div>

                <div class="receipt-detail-panel" id="receiptDetailPanel">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Vendor</span>
                                <div class="detail-value" id="detail_party_name"><?php echo isset($work_order_details['party_name']) ? $work_order_details['party_name'] : ''; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Vendor Warehouse</span>
                                <div class="detail-value" id="detail_vendor_warehouse_name"><?php echo isset($work_order_details['vendor_warehouse_name']) ? $work_order_details['vendor_warehouse_name'] : ''; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Requested Warehouse</span>
                                <div class="detail-value" id="detail_requested_warehouse_name"><?php echo isset($work_order_details['requested_warehouse_name']) ? $work_order_details['requested_warehouse_name'] : ''; ?></div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Product</span>
                                <div class="detail-value" id="detail_product_name"><?php echo isset($work_order_details['product_name']) ? $work_order_details['product_name'] : ''; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Product SKU</span>
                                <div class="detail-value" id="detail_product_sku"><?php echo isset($work_order_details['product_sku']) ? $work_order_details['product_sku'] : ''; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Product Price</span>
                                <div class="detail-value" id="detail_work_order_item_price"><?php echo isset($work_order_details['work_order_item_price']) ? number_format($work_order_details['work_order_item_price'], 2) : '0.00'; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-box">
                                <span class="detail-label">Product Total Amount</span>
                                <div class="detail-value" id="detail_item_total_amount"><?php echo isset($work_order_details['item_total_amount']) ? number_format((float)$work_order_details['item_total_amount'], 2) : '0.00'; ?></div>
                            </div>
                        </div>

                    </div>

                    <div class="receipt-summary-grid">
                        <div class="summary-tile">
                            <span class="tile-label">Issued Qty</span>
                            <strong id="issued_qty_display"><?php echo isset($work_order_details['work_order_item_qty']) ? number_format($work_order_details['work_order_item_qty'], 2) : '0.00'; ?></strong>
                        </div>
                        <div class="summary-tile">
                            <span class="tile-label">Total Received Qty</span>
                            <strong id="total_received_qty_display"><?php echo isset($work_order_details['total_received_qty']) ? number_format($work_order_details['total_received_qty'], 2) : '0.00'; ?></strong>
                        </div>
                        <div class="summary-tile">
                            <span class="tile-label">Pending Receipt Qty</span>
                            <strong id="pending_receipt_qty_display"><?php echo isset($work_order_details['pending_receipt_qty']) ? number_format($work_order_details['pending_receipt_qty'], 2) : '0.00'; ?></strong>
                        </div>
                        <div class="summary-tile summary-tile-warning">
                            <span class="tile-label">Pending After This Receipt</span>
                            <strong id="balance_after_receipt_display"><?php echo isset($work_order_details['pending_receipt_qty']) ? number_format($work_order_details['pending_receipt_qty'], 2) : '0.00'; ?></strong>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="form-group col-lg-4">
                            <span class="form-label">Received Qty <span class="text-danger">*</span></span>
                            <input aria-label="Received Qty" type="number" step="0.01" min="0.01" class="form-control" name="received_qty" id="received_qty" value="<?php echo $display_received_qty; ?>" required>
                            <small class="text-danger d-none" id="received_qty_error">Invalid received qty.</small>
                        </div>
                        <div class="form-group col-lg-4">
                            <span class="form-label">UOM</span>
                            <input aria-label="Product Uom Display" type="text" class="form-control" id="product_uom_display" value="<?php echo isset($work_order_details['product_uom_name']) && $work_order_details['product_uom_name'] != '' ? $work_order_details['product_uom_name'] : (isset($work_order_details['product_uom']) ? $work_order_details['product_uom'] : ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="receipt-tax-panel" id="receiptTaxPanel">
                        <div class="tax-panel-header">
                            <h6>Tax Preview</h6>
                            <span id="receipt_tax_status">Enter received qty to calculate tax.</span>
                        </div>
                        <div class="receipt-tax-summary">
                            <div class="tax-summary-item">
                                <span>Taxable Amount</span>
                                <strong id="receipt_amount_display"><?php echo number_format((float)$display_receipt_amount, 2); ?></strong>
                            </div>
                            <div class="tax-summary-item">
                                <span>Tax Amount</span>
                                <strong id="receipt_tax_amount_display"><?php echo number_format((float)$display_receipt_tax_amount, 2); ?></strong>
                            </div>
                            <div class="tax-summary-item">
                                <span>Total Amount</span>
                                <strong id="receipt_total_amount_display"><?php echo number_format((float)$display_receipt_total_amount, 2); ?></strong>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered tax-breakdown-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Tax</th>
                                        <th scope="col" class="text-end">Percentage</th>
                                        <th scope="col" class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt_tax_breakdown_body">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No tax calculated.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <span class="form-label">Notes</span>
                            <textarea aria-label="Notes" class="form-control" name="notes" rows="3"><?php echo $display_notes; ?></textarea>
                        </div>
                    </div>

                </div>

                <div class="row mt-4">
                    <div class="col-lg-12 text-start">
                        <button type="submit" class="btn btn-primary" name="sbt_subcontractworkorderreceipt">
                            <i class="ti-check"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="subcontractReceiptHistoryModal" tabindex="-1" aria-labelledby="subcontractReceiptHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subcontractReceiptHistoryModalLabel">Receipt History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="receipt_history_status" class="alert alert-info mb-3">Select a subcontract work order to view history.</div>
                <div id="receipt_history_summary" class="receipt-history-summary d-none">
                    <div class="history-summary-item">
                        <span>Work Order</span>
                        <strong id="history_work_order_no">-</strong>
                    </div>
                    <div class="history-summary-item">
                        <span>Issued Qty</span>
                        <strong id="history_issued_qty">0.00</strong>
                    </div>
                    <div class="history-summary-item">
                        <span>Total Received</span>
                        <strong id="history_total_received_qty">0.00</strong>
                    </div>
                    <div class="history-summary-item">
                        <span>Pending</span>
                        <strong id="history_pending_qty">0.00</strong>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped receipt-history-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Receipt No</th>
                                <th scope="col">Date</th>
                                <th scope="col" class="text-end">Received Qty</th>
                                <th scope="col" class="text-end">Pending Qty</th>
                                <th scope="col" class="text-end">Taxable Amount</th>
                                <th scope="col" class="text-end">Tax Amount</th>
                                <th scope="col" class="text-end">Total Amount</th>
                                <th scope="col">Tax Split</th>
                                <th scope="col">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="receipt_history_body">
                            <tr>
                                <td colspan="9" class="text-center text-muted">No history loaded.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
