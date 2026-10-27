<?php
$_SESSION['apply_js']['multiselest'] = "1";
if (!isset($result) || $result['status'] != 'success') {
    echo '<div class="alert alert-danger">Failed to load production details</div>';
    exit;
}

$work_order = $result['work_order'];
$items = $result['items'];
$hold_reasons = $result['hold_reasons'] ?? array();
$production_type = $_POST['production_type'] ?? 'HOLD'; // From AJAX data
$product_quality_check = (int)($work_order['quality_check'] ?? 1);
$tracking_type = (int)($work_order['tracking_type'] ?? 0);
$product_uom = $work_order['product_uom'] ?? '';
$tracking_labels = array(0 => 'Regular', 1 => 'Serial', 2 => 'Lot');
?>
<script>
$(document).ready(function () {
    $('.js-example-basic-single').select2();
});</script>

<div class="modal-header">
    <h5 class="modal-title">Production <?php echo ucfirst(strtolower($production_type)); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="productionModalForm" method="post" action="">
    <div class="modal-body">
        <!-- Work Order Header Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <span class="form-label">Transaction No</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($work_order['transaction_no']); ?>" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <span class="form-label">Transaction Date</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo date('d-m-Y', strtotime($work_order['transaction_date'])); ?>" readonly>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Planned Start Date</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo date('d-m-Y', strtotime($work_order['planned_start_date'])); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Planned End Date</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo date('d-m-Y', strtotime($work_order['planned_end_date'])); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Ordered Qty</span>
                    <div class="input-group">
                        <input aria-label="Input field" type="text" class="form-control text-right" value="<?php echo htmlspecialchars($work_order['work_order_item_qty']); ?>" readonly>
                        <span class="input-group-text manufacturing-uom-addon"><?php echo htmlspecialchars($product_uom != '' ? $product_uom : 'UOM'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Previously Completed Qty</span>
                    <div class="input-group">
                        <input aria-label="Input field" type="text" class="form-control text-right" value="<?php echo htmlspecialchars($work_order['completed_qty']); ?>" readonly>
                        <span class="input-group-text manufacturing-uom-addon"><?php echo htmlspecialchars($product_uom != '' ? $product_uom : 'UOM'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Completed Qty (This Session) <span class="text-danger">*</span></span>
                    <div class="input-group">
                        <input aria-label="Completed Qty" type="number" class="form-control text-right" id="session_completed_qty" name="completed_qty" min="0"
                               value="0" required>
                        <span class="input-group-text manufacturing-uom-addon"><?php echo htmlspecialchars($product_uom != '' ? $product_uom : 'UOM'); ?></span>
                    </div>
                </div>
            </div>
            <?php if($production_type == 'HOLD'){?>
            <div class="col-md-4 form-group  w-full">
                <label for="hold_reason" class="form-label d-block">Holding Reason</label>
                <select class="js-example-basic-single form-control" name="hold_reason" id="hold_reason" >
                    <?php foreach ((array)$hold_reasons as $row){ ?>
                        <option value="<?php echo $row['genc_detail_code']; ?>"><?php echo $row['genc_detail_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php }else{  ?>
                <input type="hidden" name="hold_reason" id="hold_reason_2" value="">
            <?php   } ?>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Finished Product</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($work_order['product_name'] ?? ''); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Tracking Type</span>
                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($tracking_labels[$tracking_type] ?? 'Regular'); ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <?php if ($product_quality_check == 1) { ?>
                    <span class="form-label d-block">QC Check</span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input js-qc-required" type="radio" name="qc_required" id="qc_required_yes" value="1">
                        <label class="form-check-label" for="qc_required_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input js-qc-required" type="radio" name="qc_required" id="qc_required_no" value="0" checked>
                        <label class="form-check-label" for="qc_required_no">No</label>
                    </div>
                <?php } else { ?>
                    <span class="form-label d-block">QC Check</span>
                    <span class="badge bg-secondary p-2">No QC for this product</span>
                    <input type="hidden" name="qc_required" value="0">
                <?php } ?>
            </div>
        </div>

        <?php if ($product_quality_check == 1 && $tracking_type == 1) { ?>
            <div class="border rounded p-3 mb-4 d-none" id="serialTrackingSection">
                <span class="form-label d-block mb-2 font-weight-bold">Serial Numbers</span>
                <div class="row" id="serialRows"></div>
            </div>
        <?php } ?>

        <?php if ($product_quality_check == 1 && $tracking_type == 2) { ?>
            <div class="border rounded p-3 mb-4 d-none" id="lotTrackingSection">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="form-label mb-0 font-weight-bold">Lot / Batch Details</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addLotRowBtn">+ Add</button>
                </div>
                <div id="lotRows"></div>
                <small class="text-muted">Total lot quantity must equal completed quantity.</small>
            </div>
        <?php } ?>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="form-group">
                    <span class="form-label">Remarks</span>
                    <textarea aria-label="Add remarks..." class="form-control" name="remarks" rows="2" maxlength="255" placeholder="Add remarks..."></textarea>
                </div>
            </div>
        </div>

        <!-- Items Table Section -->
        <div class="table-responsive mt-4">
            <span class="form-label d-block mb-3 font-weight-bold">Materials Details</span>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="th-sno">S.No</th>
                        <th scope="col" class="th-item-name">Item Name</th>
                        <th scope="col" class="th-sku">SKU</th>
                        <th scope="col" class="th-uom">UOM</th>
                        <th scope="col" class="th-requested-qty">Requested Qty</th>
                        <th scope="col" class="th-current-consumed-qty">Current Consumed Qty</th>
                        <th scope="col" class="th-available-qty">Available Qty</th>
                        <th scope="col" class="th-session-consumed-qty">Consumed Qty (This Session) <span class="text-danger">*</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($items)) {
                        foreach ($items as $seq => $item) {
                            $requested = (float)($item['request_qty'] ?? 0);
                            $current_consumed = (float)($item['completed_qty'] ?? 0);
                            $received = (float)($item['stock_receipts_qty'] ?? 0);
                            $available_base = $received > 0 ? $received : $requested;
                            $available = $available_base - $current_consumed;
                            $item_uom = $item['uom_short_code'] ?? '';
                    ?>
                    <tr>
                        <td><?php echo $seq + 1; ?></td>
                        <td><?php echo htmlspecialchars($item['item_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['sku'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['uom_short_code'] ?? ''); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($requested); ?><span class="manufacturing-uom-badge"><?php echo htmlspecialchars($item_uom); ?></span></td>
                        <td class="text-end text-muted"><?php echo htmlspecialchars($current_consumed); ?><span class="manufacturing-uom-badge"><?php echo htmlspecialchars($item_uom); ?></span></td>
                        <td class="text-end font-weight-bold"><?php echo htmlspecialchars($available); ?><span class="manufacturing-uom-badge"><?php echo htmlspecialchars($item_uom); ?></span></td>
                        <td>
                            <input type="hidden" name="item_id[]" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                            <input type="hidden" name="manufacturing_work_order_item_id[]" value="<?php echo htmlspecialchars($item['id']); ?>">
                            <div class="input-group input-group-sm">
                                <input aria-label="Consumed Qty[]" type="number" class="form-control form-control-sm consumed-qty-input text-end"
                                       name="consumed_qty[]" min="0" value="0"
                                       data-max="<?php echo htmlspecialchars($available); ?>" data-uom="<?php echo htmlspecialchars($item_uom, ENT_QUOTES, 'UTF-8'); ?>" step="0.01" required>
                                <span class="input-group-text manufacturing-uom-addon"><?php echo htmlspecialchars($item_uom != '' ? $item_uom : 'UOM'); ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No items found</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>


        <input type="hidden" name="work_order_id" value="<?php echo htmlspecialchars($work_order['id']); ?>">
        <input type="hidden" name="production_type" value="<?php echo htmlspecialchars($production_type); ?>">
        <input type="hidden" name="tracking_type" id="production_tracking_type" value="<?php echo htmlspecialchars($tracking_type); ?>">
        <input type="hidden" id="production_product_uom" value="<?php echo htmlspecialchars($product_uom, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="product_quality_check" id="product_quality_check" value="<?php echo htmlspecialchars($product_quality_check); ?>">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="la la-close"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary" id="submitProductionBtn">
            <i class="la la-check"></i> Submit
        </button>
    </div>
</form>
