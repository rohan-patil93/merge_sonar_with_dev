<?php
$_SESSION['apply_js']['multiselest'] = "1";
$toastMessage = '';
if (isset($_POST['btn_BOM'])) {
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API . "?mode=insertmanufacturingboms&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
    $res = $response2['manufacturing_boms'];
    if($res['status'] == 'sucess'){
        if($res['LASTID'] == 0){
            $msgcode = 3001; //update
            $toastMessage = $_SESSION['error_message']['3001'] ?? '';
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
        }
    }else{
        if(isset($_SESSION['error'][$res['error_no']])){
          $dis_err = explode(",", $res['other']);
          foreach ($dis_err as $va) {
            $dis_er [] = $dis_labels[$va];
          }
          $error_message = $res['error_no'] ." : ".$_SESSION['error'][$res['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
          $error_message = $res['message'];
        }
    }

}
$rid1 = $_GET['Rid'];
$rid = encodeId($rid1);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid = $rid['1'];
$url3 = API . "?mode=getmanufacturingboms&method=get&rid=" . $rid;
$che3 = callAPI($url3);
$result = json_decode($che3, 1);

$items = $result['items'];
$unit_of_measurements = $result['unit_of_measurements'];
$vendors = $result['vendors'];
$locations = $result['locations'];
$warehouses = $result['warehouses'];
$manufacturing_operations = $result['manufacturing_operations'];
$alternate_items = $result['alternate_items'];

$manufacturing_operations_parent =$manufacturing_operations_child = array();
foreach($manufacturing_operations as $manopert){
    if($manopert['parent_id'] == 0 || $manopert['parent_id'] == ''){
        $manufacturing_operations_parent[] = $manopert;
    }else{
        $manufacturing_operations_child[$manopert['parent_id']][] = $manopert;
    }

}
$manufacturing_operations = $result['manufacturing_operations'];

$manufacturing_boms = $result['manufacturing_boms'];
$manufacturing_bom_items = $result['manufacturing_bom_items'];
$manufacturing_bom_by_items = $result['manufacturing_bom_by_items'];
$manufacturing_bom_operations = $result['manufacturing_bom_operations'];
$itemworktype = $result['itemworktype'];
$manufacturing_bom_byitems = $result['manufacturing_bom_byitems'];
?>
<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_boms.css">
<script src="assets/js/custom_insert_manufacturing_boms.js" defer></script>

<div class="page-body" data-config-name="<?= htmlspecialchars(ucwords($_SESSION['config']['name']), ENT_QUOTES, 'UTF-8'); ?>" data-error-9059="<?= htmlspecialchars($_SESSION['error_message']['9059'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-toast-message="<?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8'); ?>">
 <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
<form method="post" class="<?php echo $formdisable; ?>" action="" name="kittingForm">
    <div class="card">
        <?php
          if($manufacturing_boms['current_status'] !=""){
            $workflowsteps = getWorkflowStatus('manufacturing_boms',$manufacturing_boms['id'],$manufacturing_boms['current_status']);
            echo $workflowsteps['current_status_ribbon'];
        }
            ?>
        <div class="card-header table-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">BOM</h5>
            <div class="card-header-right">
                <?php
                    echo $workflowsteps['workflow_btn'];
                ?>
                <?php
                    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
                    if(in_array($manufacturing_boms['current_status'], (array)$check_array)){
                    ?>
                      <?php
                    }else{
                        $disable = "disabled";
                        $formdisable = "event_disabled";
                    }

                ?>
                <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                    <i class="icofont icofont-reply mr-1"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body ">
            <input type="hidden" name="rid" value="<?php echo $rid; ?>">
            <div class="row" >
                <div class="col-md-3 w-full">
                    <label for="final_item_name" class="form-label d-block">Product <span class="text-danger">*</span></label>
                    <select aria-label="Bom Item" class="js-example-basic-single item-selection js-bom-item-root" name="bom_item_id" id="bom_item_id" data-type="ROOT" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($items as $item){  if($item['stocking_type'] == "F" || $item['stocking_type'] == "W"){ ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo $manufacturing_boms['bom_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 w-full">
                    <label for="final_item_name" class="form-label d-block">UOM <span class="text-danger">*</span></label>
                    <select aria-label="Bom Uom" class="js-example-basic-single"  name="bom_uom"  id="bom_uom" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($unit_of_measurements as $uom){  ?>
                            <option value="<?php echo $uom['id']; ?>" <?php echo $manufacturing_boms['bom_uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                        <?php }  ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="bom_quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control js-update-calculation" id="bom_quantity" name="bom_quantity" min="1"
                           value="<?php echo $manufacturing_boms['bom_quantity']; ?>" required>
                </div>
                <div class="col-md-2">
                    <label for="date" class="form-label"> Date <span
                            class="text-danger">*</span></label>
                    <input aria-label="Transaction Date" type="text" class="form-control datepicker" id="transaction_date" name="transaction_date" value="<?php echo fromDate($manufacturing_boms['transaction_date']); ?>" required>
                </div>
                <div class="col-md-1 text-right">
                    <label for="parent_item_total" class="form-label d-block" >Version </label>
                    <span>0.1</span>

                </div>
                <div class="col-md-1 text-right">
                    <label for="parent_item_total" class="form-label d-block" >Total </label>
                    <span id="bom_amount_span"><?php echo customeummarmonyformateifine($manufacturing_boms['bom_amount']); ?></span>
                    <input type="hidden" class="form-control" id="bom_amount" name="bom_amount" value="<?php echo $manufacturing_boms['bom_amount']; ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#bom-product" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">BOM</a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#by-products" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">By-Product</a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#bom-operations" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Operation</a>
            </li>
        </ul>
    </div>
        <div class="tab-content  tabs custom-scroll mt-3" >
            <div class="tab-pane card-body active p-0" id="bom-product" role="tabpanel">
                <div class="card">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">BOM</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body ">
                        <table class="table table-hover " >
                            <thead>
                                <tr>
                                    <th scope="col" class="p-1" style="width: 30%;">Product Name <span class="requried"></span></th>
                                    <th scope="col" class="thheader p-1" style="width: 10%;">UOM <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Qty <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Scrap %</th>
                                    <th scope="col" class="thheader p-1 " style="width: 25%;">Default Vendor</th>

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
                                    <input type="hidden" class="manufacturing_bom_items_seq_no" name="manufacturing_bom_items_seq_no[]" value="<?php echo $seq; ?>">
                                    <input type="hidden" name="manufacturing_bom_items_id[]" value="<?php echo $mbomi['id']; ?>">
                                    <td class="p-1 w-full-30" >
                                        <select aria-label="Select option" class="js-example-basic-single item-selection js-bom-item-select" id="item_id_<?php echo $seq; ?>" name="item_id[]" data-seq="<?php echo $seq; ?>" data-type="BOMITEM" required>
                                            <option value="">Select Any one</option>
                                            <?php foreach ($items as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                                            <?php } ?>
                                        </select>
                                        <textarea aria-label="Text area"  id="description_<?php echo $seq; ?>" name="description[]" class="form-control mt-2" autocomplete="off"><?php echo $mbomi['description']; ?></textarea>
                                    </td>
                                    <td class="p-1 w-ful w-full-10" id="custom-popup">
                                        <select aria-label="Select option" class="js-example-basic-single" id="uom_<?php echo $seq; ?>" name="uom[]" required>
                                            <option value="">Select Any one</option>
                                            <?php foreach ($unit_of_measurements as $uom){  ?>
                                                <option value="<?php echo $uom['id']; ?>"  <?php echo $mbomi['uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                                            <?php }  ?>
                                        </select>

                                        <div class="custom-popup-open popup-width-850 alternate-items-popup text-start" data-seq="<?php echo $seq; ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Alternate Items</h5>
                                                    <div class="card-header-right">
                                                        <i class="ti-close js-close-popup" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-hover " >
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="p-1" style="width: 25%;">Product Name <span class="requried"></span></th>
                                                                <th scope="col" class="thheader p-1" style="width: 15%;">UOM <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Qty <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Rate <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Amount <span class="requried">*</span></th>
                                                                <th scope="col" class="p-1" style="width: 25%;">Description </th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="alternate_invoiceItemalternateitems_<?php echo $seq; ?>">
                                                            <?php
                                                            $alternate_manufacturing_bom_items = json_decode($mbomi['alternate_manufacturing_bom_items'],1);

                                                                if(count((array)$alternate_manufacturing_bom_items) == 0){
                                                                    $alternate_manufacturing_bom_items[] = array();
                                                                }
                                                                $seqs = $seq . 1000;
                                                                foreach($alternate_manufacturing_bom_items as $altermbomi){
                                                                ?>
                                                            <tr id="alternate_remove_row_<?php echo $seqs; ?>">
                                                                <td class="p-1 w-full" >
                                                                    <input type="hidden" class="alternate_manufacturing_bom_items_seq_no" name="alternate_manufacturing_bom_items_seq_no[]" value="<?php echo $seqs; ?>">
                                                                    <select aria-label="Select option" class="js-example-basic-single item-selection js-alternate-item-select" id="alternate_item_id_<?php echo $seqs; ?>" name="alternate_item_id[<?php echo $seq; ?>][]" data-seq="<?php echo $seqs; ?>" data-type="ALTERNATE_BOMITEM" >
                                                                        <option value="">Select Any one</option>
                                                                        <?php if($mbomi['item_id'] !=""){ foreach ($alternate_items[$mbomi['item_id']] as $item){ ?>
                                                                            <option value="<?php echo $item['id']; ?>" <?php echo $altermbomi['alternate_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td class="p-1 w-full ">
                                                                    <select aria-label="Select option" class="js-example-basic-single" id="alternate_uom_<?php echo $seqs; ?>" name="alternate_uom[<?php echo $seq; ?>][]" >
                                                                        <option value="">Select Any one</option>
                                                                        <?php foreach ($unit_of_measurements as $uom){  ?>
                                                                            <option value="<?php echo $uom['id']; ?>"  <?php echo $altermbomi['alternate_uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                                                                        <?php }  ?>
                                                                    </select>
                                                                </td>
                                                                <td class="p-1">
                                                                    <input aria-label="Enter number" type="number" class="form-control text-right js-manufacturing-calc" id="alternate_quantity_<?php echo $seqs; ?>" name="alternate_quantity[<?php echo $seq; ?>][]" min="1" value="<?php echo $altermbomi['alternate_quantity']; ?>" >

                                                                    <input type="hidden" class="form-control text-right js-manufacturing-calc" id="alternate_rate_<?php echo $seqs; ?>" name="alternate_rate[<?php echo $seq; ?>][]" min="1" value="<?php echo $altermbomi['alternate_rate']!=""?$altermbomi['alternate_rate']:0; ?>" readonly>

                                                                    <input type="hidden" class="form-control text-right js-manufacturing-calc" id="alternate_amount_<?php echo $seqs; ?>" name="alternate_amount[<?php echo $seq; ?>][]"  value="<?php echo $altermbomi['alternate_amount']!=""?$altermbomi['alternate_amount']:0; ?>" readonly>
                                                                </td>
                                                                <td class="p-1">
                                                                    <p class="mt-2 text-right f-12">
                                                                        <span class="d-block" id="alternate_rate_text_<?php echo $seqs; ?>"><?php echo $altermbomi['alternate_rate']!=""?$altermbomi['alternate_rate']:0; ?></span>
                                                                    </p>
                                                                </td>
                                                                <td class="p-1">
                                                                    <p class="mt-2 d-block text-right f-12">
                                                                        <span class="d-block" id="alternate_amount_text_<?php echo $seqs; ?>"><?php echo $altermbomi['alternate_amount']!=""?$altermbomi['alternate_amount']:0; ?></span>
                                                                    </p>
                                                                </td>
                                                                <td class="p-1">
                                                                    <textarea aria-label="Text area"  id="alternate_description_<?php echo $seqs; ?>" name="alternate_description[<?php echo $seq; ?>][]" class="form-control" autocomplete="off"><?php echo $altermbomi['alternate_description']; ?></textarea>
                                                                </td>
                                                                <td class="p-1" >
                                                                    <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="alternate_remove_row_<?php echo $seqs; ?>"></i>
                                                                </td>
                                                            </tr>
                                                            <?php $seqs++;
} ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="row mt-2">
                                                        <div class="col-xs-12">
                                                            <button class="btn btn-success js-add-alternate-items" data-seq="<?php echo $seq; ?>" type="button">+ Add More Alternate</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-2" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Operation Process" >
                                            <select aria-label="Bom Manufacturing Operation []" class="js-example-basic-single" name="bom_manufacturing_operation_id[]" id="bom_manufacturing_operation_id_<?php echo $seq; ?>" >
                                                <option value="">Select Any one</option>
                                                <?php foreach ($manufacturing_operations as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['bom_manufacturing_operation_id'] == $item['id']?"selected":""; ?>><?php echo $item['operation_name']; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="p-1">
                                        <input aria-label="Enter number" type="number" class="form-control text-right js-manufacturing-calc" id="quantity_<?php echo $seq; ?>" name="quantity[]" min="1" value="<?php echo $mbomi['quantity']; ?>" >
                                        <p class="mt-2  f-12">
                                            Rate - <span class="d-block" id="rate_text_<?php echo $seq; ?>"><?php echo $mbomi['rate']!=""?$mbomi['rate']:0; ?></span>
                                        </p>
                                        <input type="hidden" class="form-control text-right js-manufacturing-calc" id="rate_<?php echo $seq; ?>" name="rate[]" min="1" value="<?php echo $mbomi['rate']!=""?$mbomi['rate']:0; ?>" readonly>

                                        <input type="hidden" class="form-control text-right js-manufacturing-calc" id="amount_<?php echo $seq; ?>" name="amount[]"  value="<?php echo $mbomi['amount']!=""?$mbomi['amount']:0; ?>" readonly>
                                    </td>

                                    <td class="p-1">
                                        <input aria-label="Enter number" type="number" class="form-control text-right" id="scrap_<?php echo $seq; ?>" name="scrap[]" min="0" value="<?php echo $mbomi['scrap']; ?>" >
                                        <p class="mt-2 d-block f-12">
                                            Amount - <span class="d-block" id="amount_text_<?php echo $seq; ?>"><?php echo $mbomi['amount']!=""?$mbomi['amount']:0; ?></span>
                                        </p>
                                    </td>
                                    <td class="p-1 w-full w-full-30">
                                        <select aria-label="Select option" class="js-example-basic-single" id="party_id_<?php echo $seq; ?>" name="party_id[]" >
                                            <option value="">Select Any one</option>
                                            <?php foreach ($vendors as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['party_id'] == $item['id']?"selected":""; ?>><?php echo $item['display_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="mt-2">
                                            <select aria-label="Select option" class="js-example-basic-single"  id="warehouse_id_<?php echo $seq; ?>" name="warehouse_id[]" >
                                            <option value="">Select Any one</option>
                                            <?php foreach ($warehouses as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['warehouse_id'] == $item['id']?"selected":""; ?>><?php echo $item['warehouse_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        </div>
                                    </td>
                                    <td class="p-1" >
                                        <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="remove_row_<?php echo $seq; ?>"></i>
                                        <i class="ti-exchange-vertical mt-2 d-block cursor-pointer js-open-alternate-items" data-bs-toggle="tooltip" title="" data-bs-original-title="+ Alternate Items" data-seq="<?php echo $seq; ?>"></i>

                                    </td>
                                </tr>
                                <?php $seq++;
} ?>
                            </tbody>
                        </table>
                         <div class="row mt-2">
                            <div class="col-xs-12">
                                <button class="btn btn-success js-add-bom-item" type="button">+ Add More Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane card-body p-0" id="by-products" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>By-Product</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover " >
                            <thead>
                                <tr>
                                    <th scope="col" class="p-1" style="width: 45%;">Product Name <span class="requried"></span></th>
                                    <th scope="col" class="thheader p-1" style="width: 15%;">UOM <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Qty <span class="requried">*</span></th>
                                    <th scope="col" class="p-1" style="width: 25%;">Description </th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="byitem_invoiceitems">
                                <?php

                                    if(count((array)$manufacturing_bom_byitems) == 0){
                                        $manufacturing_bom_byitems[] = array();
                                    }
                                    $seqs = 23000;
                                    foreach($manufacturing_bom_byitems as $byitm){
                                    ?>
                                <tr id="byitem_remove_row_<?php echo $seqs; ?>">
                                    <input type="hidden" name="manufacturing_bom_byitem_item_id[]" value="<?php echo $byitm['id']; ?>">

                                    <td class="p-1 w-full" >
                                        <select aria-label="Select option" class="js-example-basic-single item-selection js-byitem-select" id="byitem_item_id_<?php echo $seqs; ?>" name="byitem_item_id[]" data-seq="<?php echo $seqs; ?>" data-type="BYITEM" >
                                            <option value="">Select Any one</option>
                                            <?php foreach ($items as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $byitm['byitem_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="p-1 w-full ">
                                        <select aria-label="Select option" class="js-example-basic-single" id="byitem_uom_<?php echo $seqs; ?>" name="byitem_uom[]" >
                                            <option value="">Select Any one</option>
                                            <?php foreach ($unit_of_measurements as $uom){  ?>
                                                <option value="<?php echo $uom['id']; ?>"  <?php echo $byitm['byitem_uom'] == $uom['id']?"selected":""; ?>><?php echo $uom['uom_name']; ?></option>
                                            <?php }  ?>
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <input aria-label="Enter number" type="number" class="form-control text-right" id="byitem_quantity_<?php echo $seqs; ?>" name="byitem_quantity[]" min="1" value="<?php echo $byitm['byitem_quantity']; ?>" >
                                    </td>
                                    <td class="p-1">
                                        <textarea aria-label="Text area"  id="byitem_description_<?php echo $seqs; ?>" name="byitem_description[]" class="form-control" autocomplete="off"><?php echo $byitm['byitem_description']; ?></textarea>
                                    </td>
                                    <td class="p-1" >
                                        <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="byitem_remove_row_<?php echo $seqs; ?>"></i>
                                    </td>
                                </tr>
                                <?php $seqs++;
} ?>
                            </tbody>
                        </table>
                        <div class="row mt-2">
                            <div class="col-xs-12">
                                <button class="btn btn-success js-add-byitem" type="button">+ Add More By-Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane card-body p-0" id="bom-operations" role="tabpanel">
                <div class="card">
                    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Operations</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body ">
                        <table class="table table-hover" >
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
                            if(count((array)$manufacturing_bom_operations) == 0){
                                $manufacturing_bom_operations[] = array();
                            }
                            $seqq = 1;
                            foreach($manufacturing_bom_operations as $mbomi){  ?>
                                <tr id="remove_rows_<?php echo $seqq; ?>">
                                    <input type="hidden" name="manufacturing_bom_operation_id[]" value="<?php echo $mbomi['id']; ?>">
                                    <input type="hidden" class="manufacturing_bom_operation_seq_no" name="manufacturing_bom_operation_seq_no[]" value="<?php echo $seqq; ?>">
                                    <td class="p-1 w-full">
                                        <select aria-label="Manufacturing Operation []" class="js-example-basic-single js-operation-select" name="manufacturing_operation_id[]" id="manufacturing_operation_id_<?php echo $seqq; ?>" data-seq="<?php echo $seqq; ?>" data-type="OPERATIONS" required>
                                            <option value="">Select Any one</option>
                                            <?php foreach ($manufacturing_operations_parent as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['manufacturing_operation_id'] == $item['id']?"selected":""; ?>><?php echo $item['operation_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="p-1 w-full" id="custom-popup_2">
                                        <select aria-label="Manufacturing Operation Machine []" class="js-example-basic-single js-operation-machine-select" name="manufacturing_operation_machine_id[]" id="manufacturing_operation_machine_id_<?php echo $seqq; ?>" data-seq="<?php echo $seqq; ?>" data-type="MACHINE">
                                            <option value="">Select Any one</option>
                                            <?php foreach ($manufacturing_operations_child[$mbomi['manufacturing_operation_id']] as $item){ ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo $mbomi['manufacturing_operation_machine_id'] == $item['id']?"selected":""; ?>><?php echo $item['operation_name']; ?></option>
                                            <?php } ?>
                                        </select>

                                        <div class="custom-popup-open popup-width-1000 alternate-machine-popup text-start" data-seq="<?php echo $seqq; ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Alternate Machine</h5>
                                                    <div class="card-header-right">
                                                        <i class="ti-close js-close-popup" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-hover " >
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="p-1" style="width: 20%;">Machine <span class="requried"></span></th>
                                                                <th scope="col" class="thheader p-1" style="width: 10%;">Time (minutes) <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Manpower <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Cost per Hours <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 15%;"> Total <span class="requried">*</span></th>
                                                                <th scope="col" class="thheader p-1 text-right" style="width: 25%;">Note <span class="requried">*</span></th>

                                                                <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="alternate_invoiceItemOperation_<?php echo $seqq; ?>">
                                                            <?php
                                                            $alternate_manufacturing_bom_operations = json_decode($mbomi['alternate_manufacturing_bom_operations'],1);

                                                                if(count((array)$alternate_manufacturing_bom_operations) == 0){
                                                                    $alternate_manufacturing_bom_operations[] = array();
                                                                }
                                                                $seqs = $seqq . 2000;
                                                                foreach($alternate_manufacturing_bom_operations as $altermbomi){
                                                                ?>
                                                            <tr id="alternate_remove_machine_row_<?php echo $seqs; ?>">
                                                                <td class="p-1 w-full">
                                                                    <input type="hidden" class="alternate_manufacturing_bom_operation_seq_no" name="alternate_manufacturing_bom_operation_seq_no[]" value="<?php echo $seqs; ?>">
                                                                    <select aria-label="Select option" class="js-example-basic-single js-alternate-machine-select" name="alternate_manufacturing_operation_machine_id[<?php echo $seqq; ?>][]" id="alternate_manufacturing_operation_machine_id_<?php echo $seqs; ?>" data-seq="<?php echo $seqs; ?>" data-type="ALTERNATE_MACHINE">
                                                                        <option value="">Select Any one</option>
                                                                        <?php foreach ($manufacturing_operations_child[$mbomi['manufacturing_operation_id']] as $item){ ?>
                                                                            <option value="<?php echo $item['id']; ?>" <?php echo $altermbomi['alternate_manufacturing_operation_machine_id'] == $item['id']?"selected":""; ?>><?php echo $item['operation_name']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td class="p-1" >
                                                                    <input aria-label="Enter number" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_time[<?php echo $seqq; ?>][]" id="alternate_operation_time_<?php echo $seqs; ?>" min="1" value="<?php echo $altermbomi['alternate_operation_time']; ?>">
                                                                </td>
                                                                <td class="p-1">
                                                                    <input aria-label="Enter number" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_manpower[<?php echo $seqq; ?>][]" id="alternate_operation_manpower_<?php echo $seqs; ?>" min="1" value="<?php echo $altermbomi['alternate_operation_manpower']; ?>">
                                                                </td>
                                                                <td class="p-1">
                                                                    <input aria-label="Enter number" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_cost_per_hours[<?php echo $seqq; ?>][]" id="alternate_operation_cost_per_hours_<?php echo $seqs; ?>" value="<?php echo $altermbomi['alternate_operation_cost_per_hours']; ?>">
                                                                </td>
                                                                <td class="p-1">
                                                                    <input aria-label="Enter number" type="number" class="form-control text-right" name="alternate_operation_total[<?php echo $seqq; ?>][]" id="alternate_operation_total_<?php echo $seqs; ?>" value="<?php echo $altermbomi['alternate_operation_total']; ?>" readonly>
                                                                </td>
                                                                <td class="p-1">
                                                                    <textarea aria-label="Text area"   class="form-control" name="alternate_operation_note[<?php echo $seqq; ?>][]" id="alternate_operation_note_<?php echo $seqs; ?>" autocomplete="off"><?php echo $tab2['description']; ?><?php echo $altermbomi['alternate_operation_note']; ?></textarea>
                                                                </td>
                                                                <td class="p-1" >
                                                                    <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="alternate_remove_machine_row_<?php echo $seqs; ?>"></i>
                                                                </td>
                                                            </tr>
                                                            <?php $seqs++;
} ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="row mt-2">
                                                        <div class="col-xs-12">
                                                            <button class="btn btn-success js-add-alternate-operations" data-seq="<?php echo $seqq; ?>" type="button">+ Add More Machine</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                    <td class="p-1" >
                                        <input aria-label="Operation Time[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_time[]" id="operation_time_<?php echo $seqq; ?>" min="1" value="<?php echo $mbomi['operation_time']; ?>">
                                    </td>
                                    <td class="p-1">
                                        <input aria-label="Operation Manpower[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_manpower[]" id="operation_manpower_<?php echo $seqq; ?>" min="1" value="<?php echo $mbomi['operation_manpower']; ?>">
                                    </td>
                                    <td class="p-1">
                                        <input aria-label="Operation Cost Per Hours[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_cost_per_hours[]" id="operation_cost_per_hours_<?php echo $seqq; ?>" value="<?php echo $mbomi['operation_cost_per_hours']; ?>">
                                    </td>
                                    <td class="p-1">
                                        <input aria-label="Operation Total[]" type="number" class="form-control text-right" name="operation_total[]" id="operation_total_<?php echo $seqq; ?>" value="<?php echo $mbomi['operation_total']; ?>" readonly>
                                    </td>
                                    <td class="p-1">
                                        <textarea aria-label="Operation Note[]"   class="form-control" name="operation_note[]" id="operation_note_<?php echo $seqq; ?>" autocomplete="off"><?php echo $tab2['description']; ?><?php echo $mbomi['operation_note']; ?></textarea>
                                    </td>
                                    <th scope="col" class="p-1">
                                        <i class="ti-trash text-danger cursor-pointer js-remove-row" data-target="remove_rows_<?php echo $seqq; ?>"></i>
                                        <i class="ti-exchange-vertical cursor-pointer mt-2 d-block js-open-alternate-machine" data-bs-toggle="tooltip" title="" data-bs-original-title="+ Alternate Machine" data-seq="<?php echo $seqq; ?>"></i>
                                    </th>
                                </tr>
                            <?php $seqq++;
} ?>
                            </tbody>
                        </table>
                        <div class="row mt-2">
                            <div class="col-xs-12">
                                <button class="btn btn-success js-add-bom-operations" type="button">+ Add More Operations</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-left">
                <button type="submit" class="btn btn-primary" name="btn_BOM" <?php echo $disable; ?>>
                    <i class="la la-check"></i> Submit
                </button>

            </div>

        </div>
    </form>

<template id="bom-item-row-template">
    <tr id="remove_row___SEQ__">
        <input type="hidden" class="manufacturing_bom_items_seq_no" name="manufacturing_bom_items_seq_no[]" value="__SEQ__">
        <input type="hidden" name="manufacturing_bom_items_id[]" value="">
        <td class="p-1 w-full-30">
            <select aria-label="Item []" class="js-example-basic-single item-selection js-bom-item-select" id="item_id___SEQ__" name="item_id[]" data-seq="__SEQ__" data-type="BOMITEM" required>
                <option value="">Select Any one</option>
                <?php foreach ($items as $item){ ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                <?php } ?>
            </select>
            <textarea aria-label="Description[]" id="description___SEQ__" name="description[]" class="form-control mt-2"><?php echo $tab2['description']; ?></textarea>
        </td>
        <td class="p-1 w-ful w-full-10" id="custom-popup_3">
            <select aria-label="Uom[]" class="js-example-basic-single" id="uom___SEQ__" name="uom[]" required>
                <option value="">Select Any one</option>
                <?php foreach ($unit_of_measurements as $uom){  ?>
                    <option value="<?php echo $uom['id']; ?>"><?php echo $uom['uom_name']; ?></option>
                <?php }  ?>
            </select>

            <div class="custom-popup-open popup-width-850 alternate-items-popup text-start" data-seq="__SEQ__">
                <div class="card">
                    <div class="card-header">
                        <h5>Alternate Items</h5>
                        <div class="card-header-right">
                            <i class="ti-close js-close-popup" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-1" style="width: 25%;">Product Name <span class="requried"></span></th>
                                    <th scope="col" class="thheader p-1" style="width: 15%;">UOM <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Qty <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Rate <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Amount <span class="requried">*</span></th>
                                    <th scope="col" class="p-1" style="width: 25%;">Description </th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="alternate_invoiceItemalternateitems___SEQ__"></tbody>
                        </table>
                        <div class="row mt-2">
                            <div class="col-xs-12">
                                <button class="btn btn-success js-add-alternate-items" data-seq="__SEQ__" type="button">+ Add More Alternate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Operation Process">
                <select aria-label="Bom Manufacturing Operation []" class="js-example-basic-single" name="bom_manufacturing_operation_id[]" id="bom_manufacturing_operation_id___SEQ__">
                    <option value="">Select Any one</option>
                    <?php foreach ($manufacturing_operations as $item){ ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['operation_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </td>
        <td class="p-1">
            <input aria-label="Quantity[]" type="number" class="form-control text-right js-manufacturing-calc" id="quantity___SEQ__" name="quantity[]" min="1" value="" required>
            <p class="mt-2 f-12">Rate - <span class="d-block" id="rate_text___SEQ__">0</span></p>
            <input type="hidden" class="form-control text-right js-manufacturing-calc" id="rate___SEQ__" name="rate[]" min="1" value="1" readonly>
            <input type="hidden" class="form-control text-right js-manufacturing-calc" id="amount___SEQ__" name="amount[]" value="0" readonly>
        </td>
        <td class="p-1">
            <input aria-label="Scrap[]" type="number" class="form-control text-right" id="scrap___SEQ__" name="scrap[]" min="0" value="">
            <p class="mt-2 d-block f-12">Amount - <span class="d-block" id="amount_text___SEQ__">0</span></p>
        </td>
        <td class="p-1 w-full w-full-30">
            <select aria-label="Party []" class="js-example-basic-single" id="party_id___SEQ__" name="party_id[]">
                <option value="">Select Any one</option>
                <?php foreach ($vendors as $item){ ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['display_name']; ?></option>
                <?php } ?>
            </select>
            <div class="mt-2">
                <select aria-label="Warehouse []" class="js-example-basic-single" id="warehouse_id___SEQ__" name="warehouse_id[]">
                    <option value="">Select Any one</option>
                    <?php foreach ($warehouses as $item){ ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['warehouse_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </td>
        <td class="p-1">
            <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="remove_row___SEQ__"></i>
            <i class="ti-exchange-vertical mt-2 d-block cursor-pointer js-open-alternate-items" data-bs-toggle="tooltip" title="" data-bs-original-title="+ Alternate Items" data-seq="__SEQ__"></i>
        </td>
    </tr>
</template>

<template id="byitem-row-template">
    <tr id="byitem_remove_row___SEQ__">
        <input type="hidden" name="manufacturing_bom_byitem_item_id[]" value="">
        <td class="p-1 w-full">
            <select aria-label="Byitem Item []" class="js-example-basic-single item-selection js-byitem-select" id="byitem_item_id___SEQ__" name="byitem_item_id[]" data-seq="__SEQ__" data-type="BYITEM">
                <option value="">Select Any one</option>
                <?php foreach ($items as $item){ ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                <?php } ?>
            </select>
        </td>
        <td class="p-1 w-full">
            <select aria-label="Byitem Uom[]" class="js-example-basic-single" id="byitem_uom___SEQ__" name="byitem_uom[]">
                <option value="">Select Any one</option>
                <?php foreach ($unit_of_measurements as $uom){  ?>
                    <option value="<?php echo $uom['id']; ?>"><?php echo $uom['uom_name']; ?></option>
                <?php }  ?>
            </select>
        </td>
        <td class="p-1">
            <input aria-label="Byitem Quantity[]" type="number" class="form-control text-right" id="byitem_quantity___SEQ__" name="byitem_quantity[]" min="1" value="1">
        </td>
        <td class="p-1">
            <textarea aria-label="Byitem Description[]" id="byitem_description___SEQ__" name="byitem_description[]" class="form-control"></textarea>
        </td>
        <td class="p-1">
            <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="byitem_remove_row___SEQ__"></i>
        </td>
    </tr>
</template>

<template id="bom-operation-row-template">
    <tr id="remove_rows___SEQ__">
        <input type="hidden" name="manufacturing_bom_operation_id[]" value="">
        <input type="hidden" class="manufacturing_bom_operation_seq_no" name="manufacturing_bom_operation_seq_no[]" value="__SEQ__">
        <td class="p-1 w-full">
            <select aria-label="Manufacturing Operation []" class="js-example-basic-single js-operation-select" name="manufacturing_operation_id[]" id="manufacturing_operation_id___SEQ__" data-seq="__SEQ__" data-type="OPERATIONS" required>
                <option value="">Select Any one</option>
                <?php foreach ($manufacturing_operations_parent as $item){ ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['operation_name']; ?></option>
                <?php } ?>
            </select>
        </td>
        <td class="p-1 w-full" id="custom-popup_4">
            <select aria-label="Manufacturing Operation Machine []" class="js-example-basic-single js-operation-machine-select" name="manufacturing_operation_machine_id[]" id="manufacturing_operation_machine_id___SEQ__" data-seq="__SEQ__" data-type="MACHINE">
                <option value="">Select Any one</option>
            </select>

            <div class="custom-popup-open popup-width-1000 alternate-machine-popup text-start" data-seq="__SEQ__">
                <div class="card">
                    <div class="card-header">
                        <h5>Alternate Machine</h5>
                        <div class="card-header-right">
                            <i class="ti-close js-close-popup" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-1" style="width: 20%;">Machine <span class="requried"></span></th>
                                    <th scope="col" class="thheader p-1" style="width: 10%;">Time (minutes) <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">Manpower <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Cost per Hours <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;"> Total <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 25%;">Note <span class="requried">*</span></th>
                                    <th scope="col" class="thheader p-1 text-right" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="alternate_invoiceItemOperation___SEQ__"></tbody>
                        </table>
                        <div class="row mt-2">
                            <div class="col-xs-12">
                                <button class="btn btn-success js-add-alternate-operations" data-seq="__SEQ__" type="button">+ Add More Machine</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td class="p-1">
            <input aria-label="Operation Time[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_time[]" id="operation_time___SEQ__" min="1" value="1" required>
        </td>
        <td class="p-1">
            <input aria-label="Operation Manpower[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_manpower[]" id="operation_manpower___SEQ__" min="1" value="1" required>
        </td>
        <td class="p-1">
            <input aria-label="Operation Cost Per Hours[]" type="number" class="form-control text-right js-manufacturing-calc" name="operation_cost_per_hours[]" id="operation_cost_per_hours___SEQ__" value="1" required>
        </td>
        <td class="p-1">
            <input aria-label="Operation Total[]" type="number" class="form-control text-right" name="operation_total[]" id="operation_total___SEQ__" value="0" readonly>
        </td>
        <td class="p-1">
            <textarea aria-label="Operation Note[]" class="form-control" name="operation_note[]" id="operation_note___SEQ__"><?php echo $tab2['description']; ?></textarea>
        </td>
        <th scope="col" class="p-1">
            <i class="ti-trash text-danger cursor-pointer js-remove-row" data-target="remove_rows___SEQ__"></i>
            <i class="ti-exchange-vertical cursor-pointer mt-2 d-block js-open-alternate-machine" data-bs-toggle="tooltip" title="" data-bs-original-title="+ Alternate Machine" data-seq="__SEQ__"></i>
        </th>
    </tr>
</template>

<template id="alternate-item-row-template">
    <tr id="alternate_remove_row___SEQ__">
        <td class="p-1 w-full">
            <input type="hidden" class="alternate_manufacturing_bom_items_seq_no" name="alternate_manufacturing_bom_items_seq_no[]" value="__SEQ__">
            <select aria-label="Alternate Item [  PARENT  ][]" class="js-example-basic-single item-selection js-alternate-item-select" id="alternate_item_id___SEQ__" name="alternate_item_id[__PARENT__][]" data-seq="__SEQ__" data-type="ALTERNATE_BOMITEM">
                __OPTIONS__
            </select>
        </td>
        <td class="p-1 w-full">
            <select aria-label="Alternate Uom[  PARENT  ][]" class="js-example-basic-single" id="alternate_uom___SEQ__" name="alternate_uom[__PARENT__][]">
                <option value="">Select Any one</option>
                <?php foreach ($unit_of_measurements as $uom){  ?>
                    <option value="<?php echo $uom['id']; ?>"><?php echo $uom['uom_name']; ?></option>
                <?php }  ?>
            </select>
        </td>
        <td class="p-1">
            <input aria-label="Alternate Quantity[  PARENT  ][]" type="number" class="form-control text-right js-manufacturing-calc" id="alternate_quantity___SEQ__" name="alternate_quantity[__PARENT__][]" min="1" value="1">
            <input type="hidden" class="form-control text-right js-manufacturing-calc" id="alternate_rate___SEQ__" name="alternate_rate[__PARENT__][]" min="1" value="0" readonly>
            <input type="hidden" class="form-control text-right js-manufacturing-calc" id="alternate_amount___SEQ__" name="alternate_amount[__PARENT__][]" value="0" readonly>
        </td>
        <td class="p-1">
            <p class="mt-2 text-right f-12"><span class="d-block" id="alternate_rate_text___SEQ__">0</span></p>
        </td>
        <td class="p-1">
            <p class="mt-2 d-block text-right f-12"><span class="d-block" id="alternate_amount_text___SEQ__">0</span></p>
        </td>
        <td class="p-1">
            <textarea aria-label="Alternate Description[  PARENT  ][]" id="alternate_description___SEQ__" name="alternate_description[__PARENT__][]" class="form-control"></textarea>
        </td>
        <td class="p-1">
            <i class="ti-trash cursor-pointer text-danger js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="alternate_remove_row___SEQ__"></i>
        </td>
    </tr>
</template>

<template id="alternate-operation-row-template">
    <tr id="alternate_remove_machine_row___SEQ__">
        <td class="p-1 w-full">
            <input type="hidden" class="alternate_manufacturing_bom_operation_seq_no" name="alternate_manufacturing_bom_operation_seq_no[]" value="__SEQ__">
            <select aria-label="Alternate Manufacturing Operation Machine [  PARENT  ][]" class="js-example-basic-single js-alternate-machine-select" name="alternate_manufacturing_operation_machine_id[__PARENT__][]" id="alternate_manufacturing_operation_machine_id___SEQ__" data-seq="__SEQ__" data-type="ALTERNATE_MACHINE">
                __OPTIONS__
            </select>
        </td>
        <td class="p-1">
            <input aria-label="Alternate Operation Time[  PARENT  ][]" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_time[__PARENT__][]" id="alternate_operation_time___SEQ__" min="1" value="1">
        </td>
        <td class="p-1">
            <input aria-label="Alternate Operation Manpower[  PARENT  ][]" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_manpower[__PARENT__][]" id="alternate_operation_manpower___SEQ__" min="1" value="1">
        </td>
        <td class="p-1">
            <input aria-label="Alternate Operation Cost Per Hours[  PARENT  ][]" type="number" class="form-control text-right js-manufacturing-calc" name="alternate_operation_cost_per_hours[__PARENT__][]" id="alternate_operation_cost_per_hours___SEQ__" value="">
        </td>
        <td class="p-1">
            <input aria-label="Alternate Operation Total[  PARENT  ][]" type="number" class="form-control text-right" name="alternate_operation_total[__PARENT__][]" id="alternate_operation_total___SEQ__" value="0" readonly>
        </td>
        <td class="p-1">
            <textarea aria-label="Alternate Operation Note[  PARENT  ][]" class="form-control" name="alternate_operation_note[__PARENT__][]" id="alternate_operation_note___SEQ__"></textarea>
        </td>
        <td class="p-1">
            <i class="ti-trash text-danger cursor-pointer js-remove-row" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove" data-target="alternate_remove_machine_row___SEQ__"></i>
        </td>
    </tr>
</template>

</div>
