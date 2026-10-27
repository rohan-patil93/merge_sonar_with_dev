<?php
if($_POST['gettype'] == "SELECTMANUFACTURINGBOM"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getmanufacturingworkorders&manufacturing_bom_id=".$_POST['manufacturing_bom_id']."&gettype=SELECTMANUFACTURINGBOM";
    $response = callAPI($url);
    $result = json_decode($response,1);
    $items = $result['items'];
    $manufacturing_boms = $result['manufacturing_boms'];
    $manufacturing_bom_items = $result['manufacturing_bom_items'];
    $unit_of_measurements = $result['unit_of_measurements'];
    $itemworktype = $result['itemworktype'];
    $work_order_item_qty = $_POST['work_order_item_qty'] !=""?$_POST['work_order_item_qty']:1;

    $manufacturing_boms_qty = ($manufacturing_boms['bom_quantity']!="" && $manufacturing_boms['bom_quantity']!="0")?$manufacturing_boms['bom_quantity']:1;
    $workorderitemqty = ($work_order_item_qty/$manufacturing_boms_qty);

    $subcontract_work_order_items = array();
    foreach($manufacturing_bom_items as $mbomi){
        $subcontract_work_order_items[] = array(
            'id' => '',
            'item_id' => $mbomi['item_id'],
            'purchase_uom' => $mbomi['uom'],
            'item_quantity' => $workorderitemqty * $mbomi['quantity'],
            'bom_qty' => $mbomi['quantity'],
            'purchase_price' => isset($mbomi['purchase_price']) ? $mbomi['purchase_price'] : 0,
            'description' => $mbomi['description']
        );
    }
    $rid = ""; // AJAX call means new BOM selection, not edit mode
}
else {
    if(!isset($subcontract_work_order_items) || count((array)$subcontract_work_order_items) == 0){
        $subcontract_work_order_items = array(array());
    }
}

?>

<div class="col-lg-12">
    <div class="card mt-5">
        <input type="hidden" name="form_updatetion_type" value="<?php echo ($_POST['gettype'] == "SELECTMANUFACTURINGBOM" || $rid=="")?"insert":"update"; ?>">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Required Components</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover" >
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 20%;">Product Name <span class="requried"></span></th>
                        <th scope="col" class="thheader p-1" style="width: 8%;">UOM <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 8%;">Qty <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 8%;">BOM Qty </th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Price <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Total Amount</th>
                        <th scope="col" class="thheader p-1" style="width: 25%;">Comment</th>
                        <th scope="col" class="thheader p-1 " style="width: 8%;">Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceItem">
                    <?php
                    if(count((array)$subcontract_work_order_items) == 0){
                        $subcontract_work_order_items[] = array();
                    }
                    $seq = 1;
                    foreach($subcontract_work_order_items as $swoi){  ?>
                    <tr id="remove_row_<?php echo $seq; ?>">
                        <input type="hidden" class="manufacturing_bom_items_seq_no" value="<?php echo $seq; ?>">
                        <input type="hidden" name="subcontract_work_order_item_id[]" value="<?php echo $swoi['id']; ?>">
                        <th scope="col" class="p-1 w-full">
                            <select aria-label="Select option" class="js-example-basic-single item-selection" id="item_id_<?php echo $seq; ?>" name="item_id[]" onchange="selectProductName('<?php echo $seq; ?>')" required>
                                <option value="">Select Any one</option>
                                <?php foreach ($items as $item){
                                    $item_uom = isset($item['purchase_uom']) ? $item['purchase_uom'] : '';
                                    $item_price = isset($item['purchase_price']) ? $item['purchase_price'] : 0;
                                ?>
                                    <option value="<?php echo $item['id']; ?>" data-uom="<?php echo $item_uom; ?>" data-price="<?php echo $item_price; ?>" <?php echo $swoi['item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                                <?php } ?>
                            </select>
                        </th>
                        <th scope="col" class="p-1 w-full">
                            <select aria-label="Select option" class="js-example-basic-single" id="uom_<?php echo $seq; ?>" name="uom[]" required>
                                <option value="">Select Any one</option>
                                <?php foreach ($unit_of_measurements as $uom){  ?>
                                    <option value="<?php echo $uom['id']; ?>"  <?php echo $swoi['purchase_uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                                <?php }  ?>
                            </select>
                        </th>
                        <th scope="col" class="p-1">
                            <input aria-label="Enter number" type="number" class="form-control text-right" id="request_qty_<?php echo $seq; ?>" name="request_qty[]" min="1" value="<?php echo $swoi['item_quantity'] ? $swoi['item_quantity'] : 1; ?>" onchange="calculateItemTotal('<?php echo $seq; ?>')" oninput="calculateItemTotal('<?php echo $seq; ?>')" required>
                        </th>
                        <th scope="col" class="p-1 text-right">
                            <span><?php echo $swoi['bom_qty'] ? $swoi['bom_qty'] : ''; ?></span>
                            <input type="hidden" class="form-control text-right" id="bom_qty_<?php echo $seq; ?>" name="bom_qty[]" value="<?php echo $swoi['bom_qty'] ? $swoi['bom_qty'] : ''; ?>" readonly>
                        </th>
                        <th scope="col" class="p-1">
                            <input aria-label="Enter number" type="number" class="form-control text-right" id="item_price_<?php echo $seq; ?>" name="item_price[]" min="0" step="0.01"
                            value="<?php echo $swoi['item_price'] ?? $swoi['purchase_price'] ?? 0; ?>" onchange="calculateItemTotal('<?php echo $seq; ?>')" oninput="calculateItemTotal('<?php echo $seq; ?>')"  required>
                        </th>
                        <th scope="col" class="p-1">
                            <input aria-label="Enter number" type="number" class="form-control text-right" id="item_total_amount_<?php echo $seq; ?>" name="item_total_amount[]" min="0" step="0.01" value="<?php echo $swoi['item_amount'] ??  0; ?>" readonly>
                        </th>
                        <th scope="col">
                            <textarea aria-label="Text area"  id="description_<?php echo $seq; ?>" name="description[]" class="form-control" autocomplete="off"><?php echo $swoi['description']; ?></textarea>
                        </th>
                        <th scope="col" class="p-1">
                            <i class="ti-trash cursor-pointer" onclick="removerows('remove_row_<?php echo $seq; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                        </th>
                    </tr>
                    <?php $seq++;
} ?>
                </tbody>

            </table>
             <div class="row mt-2">
                <div class="col-xs-12">
                    <button class="btn btn-success" onclick="addMoreBomChild()" type="button" <?php echo $rid!="" || isset($_POST['manufacturing_bom_id']) && $_POST['manufacturing_bom_id'] !="" ?"disabled":""; ?> >+ Add More</button>
                </div>
            </div>
        </div>
    </div>
</div>
