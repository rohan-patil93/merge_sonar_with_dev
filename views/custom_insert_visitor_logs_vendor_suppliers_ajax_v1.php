<?php 
if($_POST['transaction_select'] !=""){
	include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
	$transaction_id = $_POST['transaction_id'];
    $transaction_table = $_POST['transaction_table'];    
    $url = API . "?mode=getVisitorInsertRelatedData&method=get&transaction_id=".$transaction_id."&transaction_table=".$transaction_table;
	$result1 = callAPI($url);
	$result = json_decode($result1, 1);
	$transaction = $result['transaction'];
    $transaction_items = $result['transaction_items'];
    $transaction_item_taxes = $result['transaction_item_taxes'];
    $unit_of_measurements = $result['unit_of_measurements'];
   // echo "<pre>"; print_r($result); echo "</pre>";
$returnhtml = '<div class="row mt-4">
                    <div class="col-4 text-center">
                        <div class="s-caption  text-primary"><i class="ti-notepad f-20"></i></div>
                        <div class="s-cont d-inline-block">
                         	<p class="m-b-0 f-12">Transaction No</p>
                            <h5 class="f-w-700 m-b-0">'.$transaction['transaction_number'].'</h5>
                        </div>
                    </div>
                    <div class="col-4 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-calendar f-20"></i></div>
                        <div class="s-cont d-inline-block">
                        	<p class="m-b-0 f-12">Transaction Date</p>
                            <h5 class="f-w-700 m-b-0">'.$transaction['transaction_date'].'</h5>
                        </div>
                    </div>
                    <div class="col-4 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                        	<p class="m-b-0  f-12">Vendor Name</p>
                            <h5 class="f-w-700 m-b-0">'.$transaction['party_name'].'</h5>
                        </div>
                    </div>
                </div>
<div class="table-responsive">
    <table class="table table-hover" id="invoiceItem"> 
        <tr>
            <th width="35%" class="p-1">Items/Service</th>
            <th class="thheader p-1" width="30%">Description </th>
            <th class="thheader p-1 text-right" width="10%">QTY </th>
            <th class="thheader p-1 text-right" width="15%">Received Qty <span class="requried">*</span></th>
            <th class="thheader p-1" width="5%">UOM <span class="requried">*</span></th>
        </tr>';
if(count((array)$transaction_items) > 0){
    foreach($transaction_items as $tab2){
        $uom_name = isset($unit_of_measurements[$tab2['purchase_uom']]['uom_name'])
            ? $unit_of_measurements[$tab2['purchase_uom']]['uom_name']
            : '';

        $returnhtml .= '
        <tr>
            <td class="p-1">
                <input type="text" class="form-control" name="item_name[]" value="'.htmlspecialchars($tab2['item_name']).'" readonly>
            </td>
            <td class="p-1">
                <input type="text" class="form-control" name="description[]" value="'.htmlspecialchars($tab2['description']).'">
            </td>
            <td class="p-1">
                <input type="text" class="form-control" name="item_quantity[]" value="'.$tab2['item_quantity'].'">
                <input type="hidden" name="item_price[]" value="'.$tab2['item_price'].'">
                <input type="hidden" name="item_amount[]" value="'.$tab2['item_amount'].'">
                <input type="hidden" name="total_amount[]" value="'.$tab2['total_amount'].'">
            </td>
            <td class="p-1">
                <input type="text" class="form-control" name="item_quantity[]" value="'.$tab2['item_quantity'].'">
                </td>
            <td class="p-1">
                <input type="hidden" name="purchase_uom[]" value="'.$tab2['purchase_uom'].'">
                <input type="text" class="form-control" value="'.htmlspecialchars($uom_name).'" readonly>
            </td>
        </tr>';
    }
} else {
    $returnhtml .= '
    <tr>
        <td class="p-1 text-center" colspan="4">
            No Data
        </td>
    </tr>';
}
$returnhtml .= '
    </table>
</div>';

echo json_encode(array('vendor_id'=>$transaction['party_id'],'htmlview'=>$returnhtml));
}else{

    ?>
    <div class="table-responsive">
        <table class="table table-hover" id="invoiceItem"> 
            <tr>
                <th width="35%" class="p-1">Items/Service</th>
                <th class="thheader p-1" width="30%">Description </th>
                <th class="thheader p-1 text-right" width="10%">QTY </th>
                <th class="thheader p-1 text-right" width="15%">Received Qty <span class="requried">*</span></th>
                <th class="thheader p-1" width="5%">UOM <span class="requried">*</span></th>
            </tr>
            <tr>
                <td class="p-1 w-full">
                    <select class="form-select js-example-basic-single" id="item_id[]" >
                        <option value=""> Select Any one </option>';
                        <?php foreach ($items as $itm) { ?>
                             <option value="<?php echo htmlspecialchars($itm['id']); ?>"><?php echo htmlspecialchars($itm['sku'].' - '.$itm['item_name']) ?></option>
                        <?php } ?>
                   </select>
                    <input type="hidden" class="form-control" name="item_name[]" value="'.htmlspecialchars($tab2['item_name']).'" readonly>
                </td>
                <td class="p-1">
                    <input type="text" class="form-control" name="description[]" value="'.htmlspecialchars($tab2['description']).'">
                </td>
                <td class="p-1">
                    <input type="text" class="form-control" name="item_quantity[]" value="'.$tab2['item_quantity'].'">
                    <input type="hidden" name="item_price[]" value="'.$tab2['item_price'].'">
                    <input type="hidden" name="item_amount[]" value="'.$tab2['item_amount'].'">
                    <input type="hidden" name="total_amount[]" value="'.$tab2['total_amount'].'">
                </td>
                <td class="p-1">
                    <input type="text" class="form-control" name="item_quantity[]" value="'.$tab2['item_quantity'].'">
                </td>
                <td class="p-1">
                    <input type="hidden" name="purchase_uom[]" value="'.$tab2['purchase_uom'].'">
                    <input type="text" class="form-control" value="'.htmlspecialchars($uom_name).'" readonly>
                </td>
            </tr>    
        </table>
    </div>
    <div class="row ">
        <div class="col-xs-12">
            <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
            <button class="btn btn-success" type="button">+ Add More</button>
        </div>
    </div>
<?php  } ?>