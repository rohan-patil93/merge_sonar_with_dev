<?php
$itemurl = API."?mode=pettycashdetails";
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
?>
<script type="text/javascript">
    function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input aria-label="Item Name[]" type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }
        $.ajax({
        url: "ajax.php",
        data:'page=pettycashdetails&item_id='+item_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            var price =  parseFloat(obj.purchase_price).toFixed(2);
            document.getElementById('productName_'+id).value=obj.description;

            document.getElementById('item_name_'+id).value=obj.expense_name;
            document.getElementById('account_code_'+id).value=obj.gl_account;
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
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input aria-label="Checkbox" type="checkbox" class="itemRow" data-rowid="'+count+'"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';
        htmlRows += '<td id="other_'+count+'"><select aria-label="Product Code[]" class="form-control form-select js-example-basic-single"  name= "productCode[]" id="productCode__2"+count+'" onchange="itemChange(this.value,'+count+')" > <option value="0"> Select Any Category </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[expense_name]; ?> </option> <?php
} ?><option  value = "-1"> Other</option> </select> <input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control"> </td>';
        htmlRows += '<td><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control"></textarea></td><input type="hidden" name="account_code[]" id="account_code_'+count+'" class="form-control">  ';
        htmlRows += '<td> <input type="hidden" name="quantity[]" id="quantity__2"+count+'"    class="form-control amount" value="1"> <input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div></td>';
        htmlRows += '<td><select aria-label="Tax[]" class="form-control form-select js-example-basic-single"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php
} ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php
} ?>  </select><div style="display: none;color: red;" id="tax_err_'+count+'"> Enter Tax</div>';

        htmlRows += '<input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount__2"+count+'" class="form-control price amount">  <input type="hidden" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value=""></td>';
         htmlRows += '<td><input aria-label="Doc Ref No[]" type="text" name="doc_ref_no[]"  id="doc_ref_no_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';
        htmlRows += ' <td><input aria-label="Doc Ref Date[]" type="date" class="form-control"  value=""  name="doc_ref_date[]"  id="kt_datepicker_3'+count+'" /> </td>';


        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    });

    });
</script>
<table class=" table table-hover" id="invoiceItem">
<tr>
    <th scope="col" class="thheader" style="width: 2%;">
        <div class="checkbox-fade fade-in-primary">
            <label class="kt-checkbox">
                <input aria-label="Check All" id="checkAll"  type="checkbox">
                <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            </label>
        </div>
    </th>
    <th scope="col" class="" style="width: 16%;">Category <span class="requried"></span></th>
    <th scope="col" class="" style="width: 18%;">Description</th>
    <th scope="col" class="thheader" style="width: 11%;">Amount <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Tax <span class="requried">*</span></th>
    <th scope="col" class="thheader" style="width: 8%;">Doc Ref No </th>
    <th scope="col" class="thheader" style="width: 8%;">Doc Ref Date </th>

</tr>
<?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) { //print_r($tab2); ?>
<tr>
    <td>
       <div class="checkbox-fade fade-in-primary ">
          <label class="form-label">
            <input aria-label="Checkbox" type="checkbox" class="itemRow" data-rowid="<?php echo $i; ?>">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
          </label>
        </div>
    </td>

    <td id='other_<?php echo $i; ?>'>
        <?php if($tab2['item_id'] >= 0){ ?>
        <select aria-label="Product Code[]" class="form-control form-select js-example-basic-single"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> Select Any Category  </option>
            <?php $like_item_check = 0 ; foreach ($itemres as $item) {
                if($tab2['item_id']== $item['id']) { $like_item_check = 1;
} ?>
            <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['expense_name']; ?>
            <?php } ?>
             <?php if($like_item_check == 0){ ?>
                    <option  selected value = <?php echo $tab2['item_id']; ?>   > <?php echo $tab2['item_name']; ?>
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
        <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
    <?php }else { ?>
        <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
        <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['item_id']; ?>">
    <?php } ?>
    </td>
    <td>
        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
        <input type="hidden" name="account_code[]" id="account_code_<?php echo $i; ?>" class="form-control">
    </td>
     <?php if($tab2['total_amount'] > 0){ $data_content = '<div class="dropdown-item"><i class="flaticon-coins"></i> A :'.customeummarmonyformate($response_code[$tab2['purchase_account']][a]).'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> B :'.customeummarmonyformate($response_code[$tab2['purchase_account']][b]).'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> C :'.customeummarmonyformate($response_code[$tab2['purchase_account']][c]).'</div>'; if($tab2['total_amount'] < $response_code[$tab2['account_code']][a]){ $sty = "style='color:#F00'";
}
}else{ $data_content = '<div class="dropdown-item">No Item Selected</div>';
}
             ?>
    <td>
        <input type="hidden" name="quantity[]" id="quantity_<?php echo $i; ?>"    class="form-control amount" value="1">

        <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">
        </div>


        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
    </td>
    <td>
        <select aria-label="Tax[]" class="form-control form-select js-example-basic-single"  name= "tax[]" id="tax_<?php echo $i; ?>"  >
            <option value="0"> Select Any Tax Rate </option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>

        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input type="hidden" name="dis_amount[]" readonly id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
    </td>
    <td>
         <input aria-label="Doc Ref No[]" type="text" name="doc_ref_no[]"  id="doc_ref_no_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['doc_ref_no']; ?>" autocomplete="off">
    </td>
    <td>
       <input aria-label="Select date" type="date" class="form-control"  value="<?php echo $tab2['doc_ref_date']; ?>"  name="doc_ref_date[]"  id="kt_datepicker_3<?php echo $i; ?>" />
    </td>
</tr>

<?php $i++; ?>
<?php } ?>
<?php } ?>
    </table>
