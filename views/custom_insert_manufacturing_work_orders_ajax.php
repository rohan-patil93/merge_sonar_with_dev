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
    $manufacturing_bom_operations = $result['manufacturing_bom_operations'];
    $manufacturing_operations = $result['manufacturing_operations'];
    $unit_of_measurements = $result['unit_of_measurements'];
    $itemworktype = $result['itemworktype'];
    $work_order_item_qty = $_POST['work_order_item_qty'] !=""?$_POST['work_order_item_qty']:1;
}

$manufacturing_boms_qty = ($manufacturing_boms['bom_quantity']!="" && $manufacturing_boms['bom_quantity']!="0")?$manufacturing_boms['bom_quantity']:1;

$workorderitemqty = ($work_order_item_qty/$manufacturing_boms_qty);

?>

<script>
    var seq = 10222;
    function addMoreBomChild(){
        seq = seq +1;
        var htmlRows = ``;
        htmlRows +=`<tr id="remove_row_`+seq+`">
            <th scope="col" class="p-1 w-full">
                <input type="hidden" class="manufacturing_bom_items_seq_no[]" value="`+seq+`">
                <input type="hidden" name="manufacturing_work_order_item_id[]" value="">
                <select aria-label="Item []" class="js-example-basic-single item-selection" id="item_id_`+seq+`" name="item_id[]" onchange="selectProductName('`+seq+`')" required>
                    <option value="">Select Any one</option>
                    <?php foreach ($items as $item){ ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                    <?php } ?>
                </select>
                <textarea aria-label="Description[]"  id="description_`+seq+`" name="description[]" class="form-control mt-2"></textarea>

            </th>
            <th scope="col" class="p-1 w-full">
                <select aria-label="Uom[]" class="js-example-basic-single" id="uom_`+seq+`" name="uom[]" required>
                    <option value="">Select Any one</option>
                    <?php foreach ($unit_of_measurements as $uom){  ?>
                        <option value="<?php echo $uom['id']; ?>"><?php echo $uom['uom_name']; ?></option>
                    <?php }  ?>
                </select>
            </th>
            <th scope="col" class="p-1">
                <input aria-label="Request Qty[]" type="number" class="form-control text-right" id="request_qty_`+seq+`" name="request_qty[]" min="1" value="" required>
            </th>
            <th scope="col" class="p-1">
                <input aria-label="Bom Qty[]" type="number" class="form-control text-right" id="bom_qty_`+seq+`" name="bom_qty[]" value="" readonly>
            </th>
             <th scope="col" class="p-1">
                <button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="removerows('remove_row_`+seq+`')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            </th>
        </tr>`;
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    var seq = 20222;
    function addMoreBomOperations(){
        seq = seq +1;
        var htmlRows = ``;
        htmlRows +=`<tr id="remove_rows_`+seq+`">
            <td class="p-1 w-full">
        <input type="hidden" name="manufacturing_work_order_operation_id[]" value="">
        <input type="hidden" class="manufacturing_bom_operation_seq_no" value="`+seq+`">
                <select aria-label="Manufacturing Operation []" class="js-example-basic-single" name="manufacturing_operation_id[]" id="manufacturing_operation_id_`+seq+`" onchange="selectManufacturingOperations('`+seq+`')" required>
                <option value="">Select Any one</option>
                <?php foreach ($manufacturing_operations as $item){ ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['operation_name']; ?></option>
                <?php } ?>
            </select>
            </td>
            <td class="p-1">
                <input aria-label="Operation Time[]" type="number" class="form-control text-right" name="operation_time[]" id="operation_time_`+seq+`" min="1" value="1" required>
            </td>
            <td class="p-1">
                <input aria-label="Operation Manpower[]" type="number" class="form-control text-right" name="operation_manpower[]" id="operation_manpower_`+seq+`" min="1" value="1" required>
            </td>
            <td class="p-1">
                <textarea aria-label="Operation Note[]"   class="form-control" name="operation_note[]" id="operation_note_`+seq+`"></textarea>
            </td>
            <td class="p-1">
                <button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="removerows('remove_rows_`+seq+`')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            </td>
        </tr>`;
        $('#invoiceItemOperation').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
</script>
<div class="col-lg-6">
    <div class="card mt-5">
        <input type="hidden" name="form_updatetion_type" value="<?php echo ($_POST['gettype'] == "SELECTMANUFACTURINGBOM" || $rid=="")?"insert":"update"; ?>">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Requird Components</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover" >
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 25%;">Product Name <span class="requried"></span></th>
                        <th scope="col" class="thheader p-1" style="width: 10%;">UOM <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Requird Qty <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 10%;">BOM Qty </th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="invoiceItem">
                    <?php
                    if(count((array)$manufacturing_bom_items) == 0){
                        $manufacturing_bom_items[] = array();
                    }
                    $seq = 1;
                    foreach($manufacturing_bom_items as $mbomi){  ?>
                    <tr id="remove_row_<?php echo $seq; ?>">
                        <input type="hidden" class="manufacturing_bom_items_seq_no" value="<?php echo $seq; ?>">
                        <input type="hidden" name="manufacturing_work_order_item_id[]" value="<?php echo $mbomi['id']; ?>">
                        <th scope="col" class="p-1 w-full">
                            <select aria-label="Select option" class="js-example-basic-single item-selection" id="item_id_<?php echo $seq; ?>" name="item_id[]" onchange="selectProductName('<?php echo $seq; ?>')" required>
                                <option value="">Select Any one</option>
                                <?php foreach ($items as $item){ ?>
                                    <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                                <?php } ?>
                            </select>
                            <textarea aria-label="Text area"  id="description_<?php echo $seq; ?>" name="description[]" class="form-control mt-2" autocomplete="off"><?php echo $mbomi['description']; ?></textarea>

                        </th>
                        <th scope="col" class="p-1 w-full">
                            <select aria-label="Select option" class="js-example-basic-single" id="uom_<?php echo $seq; ?>" name="uom[]" required>
                                <option value="">Select Any one</option>
                                <?php foreach ($unit_of_measurements as $uom){  ?>
                                    <option value="<?php echo $uom['id']; ?>"  <?php echo $mbomi['uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                                <?php }  ?>
                            </select>
                        </th>
                        <th scope="col" class="p-1">
                            <input aria-label="Enter number" type="number" class="form-control text-right" id="request_qty_<?php echo $seq; ?>" name="request_qty[]" min="1" value="<?php echo $rid!=""?$mbomi['request_qty']:($workorderitemqty*$mbomi['quantity']); ?>" >
                        </th>
                        <th scope="col" class="p-1">
                            <input aria-label="Enter number" type="number" class="form-control text-right" id="bom_qty_<?php echo $seq; ?>" name="bom_qty[]" value="<?php echo $rid!=""?$mbomi['bom_qty']:$mbomi['quantity']; ?>" readonly>
                        </th>
                        <th scope="col" class="p-1">
                            <i class="ti-trash" onclick="removerows('remove_row_<?php echo $seq; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        </th>
                    </tr>
                    <?php $seq++;
} ?>
                </tbody>

            </table>
             <div class="row mt-2">
                <div class="col-xs-12">
                    <button class="btn btn-success" onclick="addMoreBomChild()" type="button" <?php echo $rid!=""?"disabled":""; ?> >+ Add More</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-6">
    <div class="card mt-5">
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Requird Operation</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover" >
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 20%;">Operation <span class="requried"></span></th>
                        <th scope="col" class="thheader p-1" style="width: 10%;">Time (minutes) <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Manpower <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 20%;">Note <span class="requried">*</span></th>
                        <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="invoiceItemOperation">
                    <?php
                if(count((array)$manufacturing_bom_operations) == 0){
                    $manufacturing_bom_operations[] = array();
                }
                $seqq = 1;
                foreach($manufacturing_bom_operations as $mbomi){  ?>
                    <tr id="remove_rows_<?php echo $seqq; ?>">
                        <input type="hidden" name="manufacturing_work_order_operation_id[]" value="<?php echo $mbomi['id']; ?>">
                        <input type="hidden" class="manufacturing_bom_operation_seq_no" value="<?php echo $seqq; ?>">

                        <td class="p-1 w-full">
                            <select aria-label="Manufacturing Operation []" class="js-example-basic-single" name="manufacturing_operation_id[]" id="manufacturing_operation_id_<?php echo $seqq; ?>" onchange="selectManufacturingOperations('<?php echo $seqq; ?>')" required>
                            <option value="">Select Any one</option>
                            <?php foreach ($manufacturing_operations as $item){ ?>
                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['manufacturing_operation_id'] == $item['id']?"selected":""; ?>><?php echo $item['operation_name']; ?></option>
                            <?php } ?>
                        </select>
                        </td>
                        <td class="p-1">
                            <input aria-label="Operation Time[]" type="number" class="form-control text-right" name="operation_time[]" id="operation_time_<?php echo $seqq; ?>" min="1" value="<?php echo $mbomi['operation_time']; ?>" onblur="manufacturingCalculation()">
                        </td>
                        <td class="p-1">
                            <input aria-label="Operation Manpower[]" type="number" class="form-control text-right" name="operation_manpower[]" id="operation_manpower_<?php echo $seqq; ?>" min="1" value="<?php echo $mbomi['operation_manpower']; ?>" onblur="manufacturingCalculation()">
                        </td>
                        <td class="p-1">
                            <textarea aria-label="Operation Note[]"   class="form-control" name="operation_note[]" id="operation_note_<?php echo $seqq; ?>" autocomplete="off"><?php echo $tab2['description']; ?><?php echo $mbomi['operation_note']; ?></textarea>
                        </td>
                        <th scope="col" class="p-1">
                            <i class="ti-trash" onclick="removerows('remove_rows_<?php echo $seqq; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        </th>
                    </tr>
                <?php $seqq++;
} ?>
                </tbody>
            </table>
            <div class="row mt-2">
                <div class="col-xs-12">
                    <button class="btn btn-success" onclick="addMoreBomOperations()" type="button" <?php echo $rid!=""?"disabled":""; ?>>+ Add More</button>
                </div>
            </div>
        </div>
    </div>
</div>
