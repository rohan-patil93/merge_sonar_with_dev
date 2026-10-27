<?php
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
$reasonurl = API."?mode=gettabledetails&table=stock_adjustment_reasons";
$reason = callAPI($reasonurl);
$reasons = json_decode($reason,1);
?>
<script type="text/javascript">
    function hislotserialno(item_id){
        var table_name = document.getElementById('table_name').value;
        var tran_id =document.getElementById('hid').value;
        jQuery.ajax({
            url: "views/custom_item_serial_lot_histories_popup.php.php",
            data:'table='+table_name+'&item_id='+item_id+'&tranid='+tran_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal('show');

           },
           error:function (){}
       });

    }
    function serialno(id,count,random){
        var table_name = document.getElementById('table_name').value;
        var qty = document.getElementById('quantity_'+count).value;
        var item_id = document.getElementById('productCode_'+count).value;
        var tran_id =document.getElementById('hid').value;
        console.log("table_name:"+table_name+"&tran_id:"+tran_id+"&item_id:"+item_id+"&qty"+qty);
        jQuery.ajax({
            url: "views/custom_grnserial_pop.php",
            data:'table='+table_name+'&qty='+qty+'&item_id='+item_id+'&tranid='+tran_id+'&random='+random,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal('show');

           },
           error:function (){}
       });
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
        var discount_mode = $('#discount_mode').val();
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input aria-label="Checkbox" type="checkbox" class="itemRow" data-rowid="'+count+'"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';
        htmlRows += '<td id="other_'+count+'"><select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice" name= "productCode[]" id="productCode__2"+count+'" onchange="itemChange(this.value,'+count+')"  data-tablename="items"  data-fieldsname="item_name" > <option value="0"> Select Any Item </option></select> <input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""> <input type="text" name="productName[]" id="productName_'+count+'" class="form-control mt-2" autocomplete="off" data-bs-toggle="tooltip" data-placement="bottom" title="Description" ></td>';

        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount mb-2"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div><select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div></td> ';

        htmlRows += '<td class="p-1"><input aria-label="Price[]" type="text" name="price[]" id="price__2"+count+'" class="form-control price amount mb-2 decimalnumberallowe" autocomplete="off" value="0.00">';
        if(discount_mode == 'DO'){
            htmlRows += '<input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_'+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
        }else{
            htmlRows += '<input type="text" name="pdiscount_amount[]" value="0" class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount__2"+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
        }
          htmlRows += '<input type="hidden" id="compliance_specification_documents_'+count+'" value=""> <input type="hidden" name="tax[]" id="tax__2"+count+'" value="<?php echo $tab2['tax_value']; ?>"><input type="hidden" name="taxid[]" id="taxid_'+count+'" value=""><input type="hidden" name="tax_name[]" id="tax_name_'+count+'" value="">';

        htmlRows += '<td><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount__3"+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount mb-2" value="" autocomplete="off"><select aria-label="Respon[]" class="form-control form-select js-example-basic-single"  name= "respon[]"  id="respon_'+count+'"><?php foreach ($reasons as $respon) {  ?><option   <?php if($tab2['respon_id'] == $respon['id']){ ?> selected <?php
} ?>  value ="<?php echo $respon['id']."$".$respon['upordown']; ?>" > <?php echo $respon['reason']; ?> </option>    <?php
} ?></select>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $(".js-data-example-ajax-multiselectss"+count).select2({
            ajax: {
             url: "views/custom_getcityautotype.php",
             dataType: 'json',
             delay: 250,
             data: function(params) {
                console.log($(".selectitemorservice").length);
                var stringids = "";
                $(".selectitemorservice").each(function(){
                    stringids +=$(this).val()+",";
                });
                return {
                    q: params.term,
                    page: params.page,
                    tablename: $(this)[0].dataset.tablename,
                    fieldsname: $(this)[0].dataset.fieldsname,
                    stringids:stringids,
                };
            },
            processResults: function(data, params){
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });
        function formatRepo(repo) {
            if (repo.loading) return repo.text;
            var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";
            if (repo.description) {
                markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
            }
            markup += "<div class='select2-result-repository__statistics'>" +
            "</div>" + "</div></div>";
            return markup;
        }
        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }
    });
});
</script>
<div class="table-responsive">
    <table class="table table-hover table-td-6" id="invoiceItem">
        <tr>
            <th scope="col" class="thheader text-start p-1" style="width: 5%;">
                <div class="checkbox-fade fade-in-primary">
                    <label class="kt-checkbox">
                        <input aria-label="Check All" id="checkAll"  type="checkbox">
                        <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                    </label>
                </div>
            </th>
            <th scope="col" class="p-1" style="width: 30%;">Items/Services <span class="requried"></span></th>
            <th scope="col" class="thheader p-1" style="width: 20%;">Qty <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1" style="width: 20%;">Rate <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1" style="width: 20%;">Amount</th>
            <th scope="col" class="p-1" style="width: 5%;"></th>
        </tr>
        <?php if(count((array)$table2)>0){ $i=1;
         foreach ($table2 as $tab2) { //print_r($tab2); ?>
        <tr>
            <td>
                <div class="checkbox-fade fade-in-primary">
                  <label class="form-label">
                    <input aria-label="Checkbox" type="checkbox" class="itemRow" data-rowid="<?php echo $i; ?>">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                  </label>
                </div>
            </td>

            <td id='other_<?php echo $i; ?>' class="w-full">
                <?php if($tab2['item_id'] >= 0){ ?>


                <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="items"  data-fieldsname="item_name" name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                 <option value="0"> Select Any Item  </option>
                <?php $like_item_check = 0; foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']) { $like_item_check = 1;
}
if($tab2['item_id']== $item['id']){  ?>
                 <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['sku']." - ".$item['item_name']; ?>
                    <?php } ?>
                    <?php } ?>
                    <option value="-1">Other</option>
                </select>
                <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
                <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
                <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>">
            <?php }else { ?>
                <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
                <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['productCode']; ?>">
            <?php } ?>
                <textarea aria-label="Product Name[]" type="text"   name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2" autocomplete="off"  data-bs-toggle="tooltip" data-placement="bottom" title="Description"><?php echo $tab2['description']; ?></textarea>
            </td>

             <td class="w-full uom-custom">
                 <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount mb-2" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
                <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
                <select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
                    <option value="0"> UOM  </option>
                    <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
            </td>

            <td class="w-full">

                <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount decimalnumberallowe" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">
                 <input aria-label="Input field" type="<?php if($table1['discount_mode'] != 'DI'){ echo "hidden";
} ?>" name="pdiscount_amount[]" value="<?php echo $tab2['discount_amount']; ?>"
 <?php if($table1['discount_mode'] != 'DI'){ echo "readonly";
} ?> class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">


                <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
                <input type="hidden" id="compliance_specification_documents_<?php echo $i; ?>" value='<?php echo $itmdoclist; ?>'>
                <input type="hidden" name="tax[]" id="tax_<?php echo $i; ?>" value="<?php echo $tab2['tax_value']; ?>">
                <input type="hidden" name="taxid[]" id="taxid_<?php echo $i; ?>" value="<?php echo $tab2['tax_id']; ?>">
                 <input type="hidden" name="tax_name[]" id="tax_name_<?php echo $i; ?>" value="<?php echo $tab2['tax_name']; ?>">

            </td>

            <td class="w-full">
                <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">

                <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
                 <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
                <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount mb-2" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
                <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">

                <select aria-label="Respon[]" class="form-control form-select js-example-basic-single"  name= "respon[]"  id="respon_<?php echo $i; ?>">
                    <?php foreach ($reasons as $respon) {  ?>
                         <option   <?php if($tab2['reason_id'] == $respon['id']){ ?>selected <?php
} ?>  value ="<?php echo $respon['id']."$".$respon['upordown']; ?>" > <?php echo $respon['reason']; ?> </option>
                    <?php } ?>
                </select>

            </td>
             <td>
                <?php if($pop_type[$tab2['item_id']] ==1 || $pop_type[$tab2['item_id']] ==2  ){ ?>
                    <div  class="btn btn-primary" onclick="hislotserialno(<?php echo $tab2['item_id']; ?>,<?php echo $i; ?>,<?php echo $valrandom; ?> )" id="" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php if($pop_type[$tab2['item_id']] ==1 ){ echo "Serial";
}
if($pop_type[$tab2['item_id']] ==2 ){ echo "Lot";
} ?>

                    </div>
                <?php } ?>
            </td>
        </tr>
        <?php $i++; ?>
        <?php } ?>
        <?php } ?>
    </table>
</div>
