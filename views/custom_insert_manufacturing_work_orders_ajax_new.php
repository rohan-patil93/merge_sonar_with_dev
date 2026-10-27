<?php
if ($_POST['gettype'] == "SELECTMANUFACTURINGBOM") {
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API . "?mode=getmanufacturingworkorders&manufacturing_bom_id=" . $_POST['manufacturing_bom_id'] . "&gettype=SELECTMANUFACTURINGBOM";
    $response = callAPI($url);
    $result = json_decode($response, 1);
    $items = $result['items'];
    $manufacturing_boms = $result['manufacturing_boms'];
    $manufacturing_bom_items = $result['manufacturing_bom_items'];
    $manufacturing_work_order_items_by_product = $result['manufacturing_bom_by_items'];
    $manufacturing_bom_operations = $result['manufacturing_bom_operations'];
    $manufacturing_operations = $result['manufacturing_operations'];
    $unit_of_measurements = $result['unit_of_measurements'];
    $vendors = $result['vendors'];
    $warehouses = $result['warehouses'];
    $itemworktype = $result['itemworktype'];
    $work_order_item_qty = $_POST['work_order_item_qty'] != "" ? $_POST['work_order_item_qty'] : 1;
}

$manufacturing_boms_qty = ($manufacturing_boms['bom_quantity'] != "" && $manufacturing_boms['bom_quantity'] != "0") ? $manufacturing_boms['bom_quantity'] : 1;

$workorderitemqty = ($work_order_item_qty / $manufacturing_boms_qty);

$uomNameById = array();
foreach ((array)$unit_of_measurements as $uom) {
    $uomNameById[$uom['id']] = $uom['uom_name'];
}

?>

<!-- SECTION 1: REQUIRED COMPONENTS (BOM) -->
<div class="col-lg-12">
    <div class="card mt-5">
        <input type="hidden" name="form_updatetion_type" value="<?php echo ($_POST['gettype'] == "SELECTMANUFACTURINGBOM" || $rid == "") ? "insert" : "update"; ?>">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Required Components</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 18%;">Product Name</th>
                        <th scope="col" class="p-1" style="width: 8%;">UOM</th>
                        <th scope="col" class="p-1 text-right" style="width: 10%;">BOM Qty</th>
                        <th scope="col" class="p-1 text-right" style="width: 10%;">Qty</th>
                        <th scope="col" class="p-1 text-right" style="width: 10%;">Scrap %</th>
                        <th scope="col" class="p-1" style="width: 18%;">Manufacturing</th>
                        <?php if ($rid != "") { ?>
                          <th scope="col" class="p-1 text-right" style="width: 10%;">Request</th>
                          <th scope="col" class="p-1 text-right" style="width: 10%;">Received</th>
                          <th scope="col" class="p-1 text-right" style="width: 10%;">Pending</th>
                        <?php } ?>
                        <th scope="col" class="p-1" style="width: 20%;">Description</th>
                    </tr>
                </thead>
                <tbody id="invoiceItem">
                    <?php
                    if (count((array)$manufacturing_bom_items) == 0) {
                        $manufacturing_bom_items[] = array();
                    }
                    $seq = 1;
                    foreach ($manufacturing_bom_items as $mbomi) {
                        $isWipComponent = ($mbomi['stocking_type'] == 'W');
                        $isSubcontract = ($isWipComponent && $mbomi['party_id'] != '');
                        ?>
                        <tr id="remove_row_<?php echo $seq; ?>">
                            <input type="hidden" class="manufacturing_bom_items_seq_no" value="<?php echo $seq; ?>">
                            <input type="hidden" name="manufacturing_work_order_item_id[]" value="<?php echo $mbomi['id']; ?>">
                            <input type="hidden" id="item_id_<?php echo $seq; ?>" name="item_id[]" value="<?php echo $mbomi['item_id']; ?>">
                            <input type="hidden" id="uom_<?php echo $seq; ?>" name="uom[]" value="<?php echo $mbomi['uom']; ?>">
                            <input type="hidden" id="bom_manufacturing_operation_id_<?php echo $seq; ?>" name="bom_manufacturing_operation_id[]" value="<?php echo $mbomi['bom_manufacturing_operation_id']; ?>">
                            <input type="hidden" id="bom_qty_<?php echo $seq; ?>" name="bom_qty[]" value="<?php echo $rid != "" ? $mbomi['bom_qty'] : $mbomi['quantity']; ?>">
                            <input type="hidden" id="rate_<?php echo $seq; ?>" name="rate[]" value="<?php echo $mbomi['rate'] != "" ? $mbomi['rate'] : 0; ?>">
                            <input type="hidden" id="amount_<?php echo $seq; ?>" name="amount[]" value="<?php echo $mbomi['amount'] != "" ? $mbomi['amount'] : 0; ?>">
                            <input type="hidden" id="scrap_<?php echo $seq; ?>" name="scrap[]" value="<?php echo $mbomi['scrap']; ?>">
                            <input type="hidden" id="request_qty_<?php echo $seq; ?>" name="request_qty[]" value="<?php echo $rid != "" ? $mbomi['request_qty'] : ($workorderitemqty > 0 ? ($workorderitemqty * $mbomi['quantity']) : $mbomi['quantity']); ?>" data-original-qty="<?php echo $rid != "" ? $mbomi['bom_qty'] : $mbomi['quantity']; ?>">

                            <!-- Product Name -->
                            <td class="p-1">
                                <span><?php echo $mbomi['item_name']; ?></span>
                            </td>

                            <!-- UOM -->
                            <td class="p-1">
                                <?php $uom_name = $uomNameById[$mbomi['uom']] ?? ''; ?>
                                <span><?php echo $uom_name; ?></span>
                            </td>

                            <!-- BOM Qty -->
                            <td class="p-1 text-right">
                                <span><?php echo $rid != "" ? $mbomi['bom_qty'] : $mbomi['quantity']; ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                            </td>

                            <!-- Qty (request_qty updated by js) -->
                            <td class="p-1 text-right">
                                <span id="request_qty_display_<?php echo $seq; ?>"><?php echo $rid != "" ? $mbomi['request_qty'] : ($workorderitemqty > 0 ? ($workorderitemqty * $mbomi['quantity']) : $mbomi['quantity']); ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                            </td>

                            <!-- Scrap % -->
                            <td class="p-1 text-right">
                                <span><?php echo $mbomi['scrap']; ?></span>
                            </td>

                            <td class="p-1 manufacturing-method-cell">
                                <?php if ($isWipComponent) { ?>
                                    <select aria-label="Manufacturing Method[]" class="form-control js-manufacturing-method" name="manufacturing_method[]" id="manufacturing_method_<?php echo $seq; ?>" data-seq="<?php echo $seq; ?>">
                                        <option value="INHOUSE" <?php echo !$isSubcontract ? "selected" : ""; ?>>In-house</option>
                                        <option value="SUBCONTRACT" <?php echo $isSubcontract ? "selected" : ""; ?>>Subcontract</option>
                                    </select>
                                    <div class="manufacturing-subcontract-fields <?php echo $isSubcontract ? "" : "d-none"; ?>" id="subcontract_fields_<?php echo $seq; ?>">
                                        <select aria-label="Party []" class="js-example-basic-single subcontract-select" name="party_id[]" id="party_id_<?php echo $seq; ?>" data-subcontract-required="1">
                                            <option value="">Sub Contractor</option>
                                            <?php foreach ((array)$vendors as $vendor) { ?>
                                                <option value="<?php echo $vendor['id']; ?>" <?php echo $mbomi['party_id'] == $vendor['id'] ? "selected" : ""; ?>><?php echo $vendor['display_name'] != "" ? $vendor['display_name'] : $vendor['company']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <select aria-label="Warehouse []" class="js-example-basic-single subcontract-select mt-1" name="warehouse_id[]" id="warehouse_id_<?php echo $seq; ?>" data-subcontract-required="1">
                                            <option value="">Sub Contractor Warehouse</option>
                                            <?php foreach ((array)$warehouses as $warehouse) { ?>
                                                <option value="<?php echo $warehouse['id']; ?>" <?php echo $mbomi['warehouse_id'] == $warehouse['id'] ? "selected" : ""; ?>><?php echo $warehouse['warehouse_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="manufacturing-inhouse-note <?php echo $isSubcontract ? "d-none" : ""; ?>" id="inhouse_note_<?php echo $seq; ?>">Child work order will be created after approval.</div>
                                <?php } else { ?>
                                    <input type="hidden" name="manufacturing_method[]" value="INHOUSE">
                                    <input type="hidden" id="party_id_<?php echo $seq; ?>" name="party_id[]" value="<?php echo $mbomi['party_id']; ?>">
                                    <input type="hidden" id="warehouse_id_<?php echo $seq; ?>" name="warehouse_id[]" value="<?php echo $mbomi['warehouse_id']; ?>">
                                    <span class="text-muted">Not applicable</span>
                                <?php } ?>
                            </td>

                            <?php if ($rid != "") { ?>
                              <!-- Request -->
                              <td class="p-1 text-right">
                                <span><?php echo $mbomi['stock_request_qty'] ?? 0; ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                              </td>

                              <!-- Received -->
                              <td class="p-1 text-right">
                                <span><?php echo $mbomi['stock_receipts_qty'] ?? 0; ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                              </td>

                              <!-- Pending -->
                              <td class="p-1 text-right">
                                <span><?php echo ($mbomi['stock_request_qty'] ?? 0) - ($mbomi['stock_receipts_qty'] ?? 0); ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                              </td>
                            <?php } ?>

                            <!-- Description (only editable field) -->
                            <td class="p-1">
                                <textarea aria-label="Text area" id="description_<?php echo $seq; ?>" name="description[]" class="form-control" autocomplete="off"><?php echo $mbomi['description']; ?></textarea>
                            </td>
                        </tr>
                    <?php $seq++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION 2: BY-PRODUCTS -->
<div class="col-lg-12">
    <div class="card mt-5">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">By-Products</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 35%;">Product Name</th>
                        <th scope="col" class="p-1" style="width: 15%;">UOM</th>
                        <th scope="col" class="p-1 text-right" style="width: 15%;">Qty</th>
                        <th scope="col" class="p-1" style="width: 35%;">Description</th>
                    </tr>
                </thead>
                <tbody id="byitem_invoiceitems">
                    <?php
                    if (count((array)$manufacturing_work_order_items_by_product) == 0) {
                        $manufacturing_work_order_items_by_product[] = array();
                    }
                    $seqs = 23000;
                    foreach ($manufacturing_work_order_items_by_product as $byitm) {
                    ?>
                        <tr id="byitem_remove_row_<?php echo $seqs; ?>">
                            <input type="hidden" name="manufacturing_work_order_items_by_product_id[]" value="<?php echo $byitm['id']; ?>">
                            <input type="hidden" id="byitem_item_id_<?php echo $seqs; ?>" name="byitem_item_id[]" value="<?php echo $byitm['byitem_item_id']; ?>">
                            <input type="hidden" id="byitem_uom_<?php echo $seqs; ?>" name="byitem_uom[]" value="<?php echo $byitm['byitem_uom']; ?>">

                            <td class="p-1 w-full">
                                <span><?php echo $byitm['item_name']; ?></span>
                            </td>
                            <td class="p-1 w-full ">
                                <?php $uom_name = $uomNameById[$byitm['byitem_uom']] ?? ''; ?>
                                <span><?php echo $uom_name; ?></span>
                            </td>
                            <td class="p-1 text-right">
                                <span id="byitem_quantity_display_<?php echo $seqs; ?>"><?php echo $rid != "" ? $byitm['byitem_quantity'] : ($workorderitemqty > 0 ? ($workorderitemqty * $byitm['byitem_quantity']) : $byitm['byitem_quantity']); ?></span>
                                <span class="manufacturing-uom-badge"><?php echo $uom_name; ?></span>
                                <input type="hidden" id="byitem_quantity_<?php echo $seqs; ?>" name="byitem_quantity[]" value="<?php echo $rid != "" ? $byitm['byitem_quantity'] : ($workorderitemqty > 0 ? ($workorderitemqty * $byitm['byitem_quantity']) : $byitm['byitem_quantity']); ?>" data-original-qty="<?php echo $byitm['byitem_quantity']; ?>">
                            </td>
                            <td class="p-1">
                                <textarea aria-label="Text area" id="byitem_description_<?php echo $seqs; ?>" name="byitem_description[]" class="form-control" autocomplete="off"><?php echo $byitm['byitem_description']; ?></textarea>
                            </td>
                        </tr>
                    <?php $seqs++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SECTION 3: REQUIRED OPERATIONS -->
<div class="col-lg-12">
    <div class="card mt-5">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Required Operations</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 15%;">Operation <span class="requried"></span></th>
                        <th scope="col" class="p-1" style="width: 15%;">Machine <span class="requried"></span></th>
                        <th scope="col" class="thheader p-1" style="width: 10%;">Time (minutes) <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Manpower <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Cost per Hours <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 15%;"> Total <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Note <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="invoiceItemOperation">
                    <?php
                    if (count((array)$manufacturing_bom_operations) == 0) {
                        $manufacturing_bom_operations[] = array();
                    }
                    $seqq = 1;
                    foreach ($manufacturing_bom_operations as $mbomi) {  ?>
                        <tr id="remove_rows_<?php echo $seqq; ?>">
                            <input type="hidden" name="manufacturing_work_order_operation_id[]" value="<?php echo $mbomi['id']; ?>">
                            <input type="hidden" class="manufacturing_bom_operation_seq_no" value="<?php echo $seqq; ?>">
                            <input type="hidden" id="manufacturing_operation_id_<?php echo $seqq; ?>" name="manufacturing_operation_id[]" value="<?php echo $mbomi['manufacturing_operation_id']; ?>">

                            <input type="hidden" id="operation_time_<?php echo $seqq; ?>" name="operation_time[]" value="<?php echo $mbomi['operation_time']; ?>">
                            <input type="hidden" id="operation_manpower_<?php echo $seqq; ?>" name="operation_manpower[]" value="<?php echo $mbomi['operation_manpower']; ?>">
                            <input type="hidden" id="operation_cost_per_hours_<?php echo $seqq; ?>" name="operation_cost_per_hours[]" value="<?php echo $mbomi['operation_cost_per_hours'] ?? 1; ?>">
                            <input type="hidden" id="operation_total_<?php echo $seqq; ?>" name="operation_total[]" value="<?php echo $mbomi['operation_total'] ?? 0; ?>">

                            <td class="p-1 w-full">
                                <?php
                                $operation_name = '';
                                foreach ($manufacturing_operations as $op) {
                                    if ($op['id'] == $mbomi['manufacturing_operation_id']) {
                                        $operation_name = $op['operation_name'];
                                        break;
                                    }
                                }
                                ?>
                                <span><?php echo $operation_name; ?></span>
                            </td>

                            <td class="p-1 w-full">
                                <select aria-label="Manufacturing Operation Machine []" class="js-example-basic-single js-operation-machine-select" name="manufacturing_operation_machine_id[]" id="manufacturing_operation_machine_id_<?php echo $seqq; ?>" data-seq="<?php echo $seqq; ?>" data-type="MACHINE">
                                    <option value="">Select Any one</option>
                                    <?php foreach ((array)$manufacturing_operations as $item) { ?>
                                        <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['manufacturing_operation_machine_id'] == $item['id'] ? "selected" : ""; ?>><?php echo $item['operation_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>

                            <td class="p-1 text-right">
                                <span id="operation_time_display_<?php echo $seqq; ?>"><?php echo $mbomi['operation_time']; ?></span>
                            </td>
                            <td class="p-1 text-right">
                                <span id="operation_manpower_display_<?php echo $seqq; ?>"><?php echo $mbomi['operation_manpower']; ?></span>
                            </td>
                            <td class="p-1 text-right">
                                <span id="operation_cost_per_hours_display_<?php echo $seqq; ?>"><?php echo $mbomi['operation_cost_per_hours'] ?? 1; ?></span>
                            </td>
                            <td class="p-1 text-right">
                                <span id="operation_total_display_<?php echo $seqq; ?>"><?php echo $mbomi['operation_total'] ?? 0; ?></span>
                            </td>
                            <td class="p-1">
                                <textarea aria-label="Operation Note[]" class="form-control" name="operation_note[]" id="operation_note_<?php echo $seqq; ?>" autocomplete="off"><?php echo $mbomi['operation_note']; ?></textarea>
                            </td>
                        </tr>
                    <?php $seqq++;
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
