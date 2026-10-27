<?php
    $itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
    $item_data = callAPI($itemurl);
    $itemres = json_decode($item_data, 1);
    foreach ($itemres as $num_type) {
        $pop_type[$num_type['id']] = $num_type['tracking_type'];
    }
?>
<script type="text/javascript">
    function serialno(id,count){
        var table_name = document.getElementById('table_name').value;
        var qty = document.getElementById('quantity_'+count).value;
         var item_id = document.getElementById('productCode_'+count).value;
        jQuery.ajax({
            url: "views/custom_grnserial_pop.php",
            data:'table='+table_name+'&qty='+qty+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});

             },
            error:function (){}
        });
    }
    function damagedqty(id){
        var invoiced_quantity = $('#invoiced_quantity_'+id).val();
        var quantity = $('#quantity_'+id).val();
        var damagedqty = parseFloat(invoiced_quantity)-parseFloat(quantity);
        $('#damaged_quantity_'+id).val(parseFloat(damagedqty).toFixed(2));
    }
     function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input aria-label="Item Name[]" type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }
        $.ajax({
        url: "ajax.php",
        data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#kt_datepicker_4_3').val(),
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            var price =  parseFloat(obj.purchase_price).toFixed(2);
            document.getElementById('productName_'+id).value=obj.item_description;
            document.getElementById('uom_'+id).value=obj.purchase_uom;
            document.getElementById('price_'+id).value=price;
            document.getElementById('item_name_'+id).value=obj.item_name;
            document.getElementById('purchase_account_'+id).value=obj.purchase_account;
            var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
            var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> C :'+obj.c+'</div>';

            document.getElementById('budget_'+id).innerHTML = text;
            document.getElementById('purchase_account_aval_'+id).value=obj.am;
        },
        error:function (){}
        });
    }
    $(document).ready(function(){
    var count = $(".itemRow").length;
    $(document).on('click', '#addRows', function() {
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><label class="kt-checkbox"> <input aria-label="Checkbox"  class="itemRow" type="checkbox" data-rowid="'+count+'">  <span> </span> </label> </td>';
        htmlRows += '<td id="other_'+count+'"><select aria-label="Product Code[]" class="form-control"  name= "productCode[]" id="productCode__2"+count+'" onchange="itemChange(this.value,'+count+')" > <option value="0"> Select Any Prodcut </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[item_name]; ?> </option> <?php
} ?><option  value = "-1"> Other</option> </select> <input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""></td>';
        htmlRows += '<td><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control"></textarea></td> ';
        htmlRows += '<td><select aria-label="Uom[]" class="form-control"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2[uom] == $uom[genc_detail_code]){ ?>selected <?php
} ?>  value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div></td> ';
        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div></td>';
        htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div></td>';
        htmlRows += '<td><select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php
} ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php
} ?>  </select><div style="display: none;color: red;" id="tax_err_'+count+'"> Enter Tax</div> </td>';

        htmlRows += '<td><div> <div style="float: left;width: 70%;"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount__2"+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></div>';
        htmlRows +='<div class="dropdown dropdown-inline" style="padding: 3px;"><button type="button" class="btn btn-hover-brand btn-elevate-hover btn-icon btn-sm btn-icon-md btn-circle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i>  </button> <div class="dropdown-menu dropdown-menu-right" id="budget_'+count+'">   <div class="dropdown-item" >&nbsp;No Item Selected</div>          </div></div></div></td>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    });
 });
</script>
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="invoiceItem">
<tr>
    <th scope="col" class="thheader" style="width: 2%;">
            <label class="kt-checkbox">
                <input aria-label="Check All" id="checkAll"  type="checkbox">
                <span></span>
            </label>
    </th>

    <th scope="col" class="" style="width: 16%;">Items/Service <span class="requried"></span></th>
    <th scope="col" class="" style="width: 20%;">Description</th>
    <th scope="col" class="thheader" style="width: 8%;">UOM <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Invoice Qty <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Received Qty <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Damaged Qty <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Rate</th>
    <th scope="col" class="thheader" style="width: 8%;">Tax <span class="requried">*</span></th>

    <th scope="col" class="thheader" style="width: 12%;">Amount</th>
    <th scope="col" class="thheader" style="width: 8%;"></th>

</tr>
<?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) { //print_r($tab2); ?>
<tr>
    <td>
        <label class="kt-checkbox"><input aria-label="Checkbox" class="form-control itemRow" type="checkbox" data-rowid="<?php echo $i; ?>"> <span></span> </label>
    </td>

    <td id='other_<?php echo $i; ?>'>
        <?php if($tab2['item_id'] >= 0){ ?>
        <select aria-label="Product Code[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> Select Any Item  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id']== $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?>
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
        <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
        <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
        <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']][a]; ?>">
    <?php }else { ?>
        <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
        <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['productCode']; ?>">
    <?php } ?>
    </td>
    <td>
        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
    </td>
     <td>
        <select aria-label="Uom[]" class="form-control"  name= "uom[]"  id="uom_<?php echo $i; ?>">
            <option value="0"> Select Any UOM  </option>
            <?php foreach ($uomlist as $uom) {  ?>
                 <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
    </td>
    <td>
        <input aria-label="Invoiced Quantity[]" type="text" name="invoiced_quantity[]" id="invoiced_quantity_<?php echo $i; ?>"  onblur="damagedqty(<?php echo $i; ?>)"   class="form-control amount" value="<?php echo $tab2['invoiced_quantity']; ?>"  autocomplete="off">
    </td>
    <td>
        <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="damagedqty(<?php echo $i; ?>)"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
        <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
    </td>

    <td>
        <input aria-label="Damaged Quantity[]" type="text" name="damaged_quantity[]" id="damaged_quantity_<?php echo $i; ?>"   class="form-control amount" value="<?php echo $tab2['damaged_quantity']; ?>"  autocomplete="off">
    </td>

    <td>
        <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">

        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
    </td>
    <td>
        <select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_<?php echo $i; ?>"  >
            <option value="0"> Select Any Tax Rate </option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
    </td>
    <td>
        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly  id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
    </td>
     <?php if($_GET['quality'] == '1'){
        $view_item_id =  deocdeId($tab2['item_id']);
        $bill_view =  "index.php?option=COM_ITEM_QUALITY_CHECK&Rid=".$view_item_id;
     ?>
        <td>
            <a  target="_blank" href="<?php echo $bill_view; ?>" >
                <div class="btn btn-success btn-brand--icon" id="kt_search">
                    <span>Quality Check</span>
                </div>
            </a>
        </td>
    <?php }else{ ?>
     <td>
        <div  class="btn btn-primary" onclick="serialno(<?php echo $tab2['item_id']; ?>,<?php echo $i; ?>)" id="" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php if($pop_type[$tab2['item_id']] ==1 ){ echo "Serial";
}else{ echo "Lot";
} ?></div>
    </td>
    <?php } ?>

</tr>

<?php $i++; ?>
<?php } ?>
<?php } ?>
    </table>
