<?php
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
?>
<script type="text/javascript">
    function getPreviousOrder(itemid,seq){

        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#popup1").html(loader);
        $('#kt_modal_KTDatatable_remote').modal("show");
        var option = '<?php echo $option; ?>';
        $.ajax({
            url: "ajax.php",
            data:'page=getitemsorders&item_id='+itemid+'&table_name='+$('#table_name').val()+'&option='+option,
            type: "POST",
            success:function(data){
                 $('#popup1').html(data);
            }
        });
    }
    function closePreviousOrder(){
        $(".custom-popup-open").hide();
    }
    function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input aria-label="Item Name[]" type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }

        if(item_id != ""){
            $("#quantity_"+id).removeAttr('readonly');
            $("#price_"+id).removeAttr('readonly');
        }
        $.ajax({
            url: "ajax.php",
            data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#kt_datepicker_4_3').val()+'&party_id='+$("#party_id").val(),
            type: "POST",
            success:function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                var price =  parseFloat(obj.purchase_price).toFixed(2);
                document.getElementById('productName_'+id).value=obj.item_description;
                document.getElementById('uom_'+id).value=obj.purchase_uom;
                document.getElementById('price_'+id).value=price;
                document.getElementById('item_name_'+id).value=obj.sku+" - "+obj.item_name;
                document.getElementById('purchase_account_'+id).value=obj.purchase_account;

                var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
                var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> C :'+obj.c+'</div>';
                document.getElementById('purchase_account_aval_'+id).value=obj.am;

                document.getElementById('tax_'+id).value=obj.igst;
                document.getElementById('taxid_'+id).value=obj.hsn_code_id;
                document.getElementById('tax_name_'+id).value=obj.hsn_code;
                var hsncodetax = '<span class="d-block">HSN - '+obj.hsn_code+'</span><span class="d-block">Tax - '+obj.igst+'</span>';
                document.getElementById('hsn_and_sac_code_id_'+id).innerHTML=hsncodetax;

                $('#item_history_detail_btn_'+id).html('<button type="button" class="btn p-0 border-0 bg-transparent m-l-5 text-primary float-end cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Open Item History" onclick="getPreviousOrder('+item_id+','+id+')" tabindex="0"><i class="ti ti-timer icon-dark"></i> Item History</button>');
                if (isNaN(obj.sales_order_qty)  || obj.sales_order_qty ==null) {
                    $('#item_history_so_'+id).text(0.00);
                    $('#item_history_so_text_'+id).val(0.00);
                } else {
                    $('#item_history_so_'+id).text(parseFloat(obj.sales_order_qty).toFixed(2));
                    $('#item_history_so_text_'+id).val(parseFloat(obj.sales_order_qty).toFixed(2));
                }
                if (isNaN(obj.purchase_order_qty)  || obj.purchase_order_qty ==null) {
                    $('#item_history_po_'+id).text(0.00);
                    $('#item_history_po_text_'+id).val(0.00);
                } else {
                    $('#item_history_po_'+id).text(parseFloat(obj.purchase_order_qty).toFixed(2));
                    $('#item_history_po_text_'+id).val(parseFloat(obj.purchase_order_qty).toFixed(2));
                }
                if (isNaN(obj.stock_in_hand)  || obj.stock_in_hand ==null) {
                   $('#item_history_qoh_'+id).text(0.00);
                    $('#item_history_qoh_text_'+id).val(0.00);
                } else {
                   $('#item_history_qoh_'+id).text(parseFloat(obj.stock_in_hand).toFixed(2));
                    $('#item_history_qoh_text_'+id).val(parseFloat(obj.stock_in_hand).toFixed(2));
                }
                if (isNaN(obj.available_qty) || obj.available_qty ==null) {
                    $('#item_history_available_'+id).text(0.00);
                    $('#item_history_available_text_'+id).val(0.00);
                } else {
                   $('#item_history_available_'+id).text(parseFloat(obj.available_qty).toFixed(2));
                    $('#item_history_available_text_'+id).val(parseFloat(obj.available_qty).toFixed(2));
                }
                if (isNaN(obj.minimum_level) || obj.minimum_level ==null) {
                     $('#item_history_min_ql_'+id).text(0.00);
                    $('#item_history_min_ql_text_'+id).val(0.00);
                } else {
                    $('#item_history_min_ql_'+id).text(parseFloat(obj.minimum_level).toFixed(2));
                    $('#item_history_min_ql_text_'+id).val(parseFloat(obj.minimum_level).toFixed(2));
                }
                 if (isNaN(obj.maximum_capacity) || obj.maximum_capacity ==null) {
                      $('#item_history_max_ql_'+id).text(0.00);
                     $('#item_history_max_ql_text_'+id).val(0.00);
                } else {
                     $('#item_history_max_ql_'+id).text(parseFloat(obj.maximum_capacity).toFixed(2));
                     $('#item_history_max_ql_text_'+id).val(parseFloat(obj.maximum_capacity).toFixed(2));
                }
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (obj.hasOwnProperty('compliance_specification_documents')){
                    $("#compliance_specification_documents_"+id).val(obj.compliance_specification_documents);
                    showAttchmentInAvailableItemTables();
                }
                if (isNaN(obj.base_uom) || obj.base_uom ==null || obj.base_uom =="") {
                    $("#hide_baseuomtext_"+id).val('');
                    $("#baseuomtext_"+id).text('NA');
                }else{
                    $("#hide_baseuomtext_"+id).val(obj.base_uom);
                     $("#baseuomtext_"+id).text(0);
                }
                $("#party_sku_concat_"+id).text('Party Code : '+obj.party_sku_number);
                closePreviousOrder();
            },
            error:function (){}
        });
    }
    $(document).ready(function(){
        var count = $(".itemRow").length;
        $(document).on('click', '#addRows', function() {
            count++;
            var htmlRows = '';
            var discount_mode = $('#discount_mode').val();
            htmlRows += '<tr>';
            htmlRows += '<td><input type="hidden" name="tax[]" id="tax_'+count+'" value="<?php echo $tab2['tax_value']; ?>">  <input type="hidden" name="taxid[]" id="taxid_'+count+'" value=""> <input type="hidden" name="tax_name[]" id="tax_name_'+count+'" value=""><label class="kt-checkbox"> <input aria-label="Checkbox"  class="itemRow" type="checkbox" data-rowid="'+count+'">  <span> </span> </label> </td>';
            htmlRows += '<td id="other_'+count+'" class="p-1"><select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice" name= "productCode[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')"   data-tablename="items"  data-fieldsname="item_name" > <option value="0"> Select Any Item </option></select> <input type="hidden" name="transaction_item_row_id[]" id="transaction_item_row_id_'+count+'" value=""><input type="hidden" name="ref_transaction_item_table_id[]" id="ref_transaction_item_table_id_'+count+'" value=""><input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""><span id="party_sku_concat_'+count+'" class="d-block f-10"><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control mt-1"></textarea></td>';

            htmlRows += '<td class="p-1"><select aria-label="Uom[]" class="form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'" onchange="baseUOMConversion('+count+')"><option value="0"> UOM  </option><?php  foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div> <p class="mt-3" id="item_history_detail_btn_'+count+'"></p><div id="custom-popup"><div class="custom-popup-open item_order_details_'+count+' text-start" style="top: -90px; display: none; width:560px; left: 130px;"></div></div></td> ';

             htmlRows += '<td class="p-1"><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)" value="0.00" class="form-control quantity amount decimalnumberallowe"  autocomplete="off" readonly><p><input type="hidden" id="hide_baseuomtext_'+count+'" value=""><span class="float-left f-10">Base Conv :</span><span class="float-right f-10 " id="baseuomtext_'+count+'">NA</span></p><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div> <table class="table table-sm border-none mt-3 f-12"><tr data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Sales Order"><th scope="col" class="text-start p-0">SO</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_so_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Purchase Orders"><th scope="col" class="text-start p-0">PO</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_po_'+count+'">0.00</td></tr></table></td>';

            htmlRows += '<td class="p-1"><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount decimalnumberallowe" value="0.00" autocomplete="off" readonly><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div>';
            if(discount_mode == 'DO'){
                htmlRows += '<input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_'+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
            }else{
                htmlRows += '<input type="text" name="pdiscount_amount[]" value="0" class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_'+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
            }
          htmlRows += '<table class="table table-sm border-none mt-3 f-12"><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Quantity On Hand"><th scope="col" class="text-start p-0">QOH</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_qoh_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Available Qty"><th scope="col" class="text-start p-0">Max</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_available_'+count+'">0.00</td></tr></table></td>';

              htmlRows += '<td class="p-1 text-center" ><div id="hsn_and_sac_code_id_'+count+'" class="f-12 m-b-20"><span class="d-block">-</span><span class="d-block">-</span></div><table class="table table-sm border-none  f-12"><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Minimum Quantity Level"><th scope="col" class="text-start p-0">Min</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_min_ql_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Maximum Quantity Level"><th scope="col" class="text-start p-0">Max</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_max_ql_'+count+'">0.00</td></tr></table><input type="hidden" name="item_details[so][]" id="item_history_so_text_'+count+'" value="<?php echo customeNumberFormate($item_details['so']);?>"><input type="hidden" name="item_details[po][]" id="item_history_po_text_'+count+'" value="0"><input type="hidden" name="item_details[qoh][]" id="item_history_qoh_text_'+count+'" value="0"><input type="hidden" name="item_details[available][]" id="item_history_available_text_'+count+'" value="0"><input type="hidden" name="item_details[min_ql][]" id="item_history_min_ql_text_'+count+'" value="0"><input type="hidden" name="item_details[max_ql][]" id="item_history_max_ql_text_'+count+'" value="0"></td>';

            htmlRows += '<td class="p-1"> <input type="hidden" id="compliance_specification_documents_'+count+'" value=""><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"> <input type="hidden" name="ptax_amount[]" value="" readonly class="form-control" id="ptax_amount_'+count+'"> <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_'+count+'"><input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"><input type="hidden" name="amount[]" readonly id="amount_'+count+'" class="form-control amount" value=""></td>';

            htmlRows += '</tr>';
            $('#invoiceItem').append(htmlRows);
            $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
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
                    q: params.term, // search term
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
                "</div>" +
                "</div></div>";
                return markup;
            }
            function formatRepoSelection(repo) {
                return repo.full_name || repo.text;
            }
        });
});
</script>
<div class="table-responsive">
    <table class="table table-hover" id="invoiceItem">
        <tr>
            <th scope="col" class="thheader p-1" style="width: 1%;">
                <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
                    <label class="kt-checkbox">
                        <input aria-label="Check All" id="checkAll"  type="checkbox">
                        <span class="cr">
                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                    </label>
                </div>
            </th>
            <th scope="col" class="p-1" style="width: 30%;">Items/Service <span class="requried"></span></th>
            <th scope="col" class="thheader p-1" style="width: 9%;">UOM <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Qty <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Rate <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1" style="width: 10%;">HSN/Tax <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1 text-right" style="width: 20%;">Amount</th>
        </tr>
        <?php if(count((array)$table2)>0){ $i=1;
            foreach ($table2 as $tab2) {
                $item_details = json_decode($tab2['item_details'],1);
                ?>
                <tr>
                    <td class="p-1">
                        <input type="hidden" id="compliance_specification_documents_<?php echo $i; ?>" value='<?php echo $itmdoclist; ?>'>
                        <input type="hidden" name="tax[]" id="tax_<?php echo $i; ?>" value="<?php echo $tab2['tax_value']; ?>">
                        <input type="hidden" name="taxid[]" id="taxid_<?php echo $i; ?>" value="<?php echo $tab2['tax_id']; ?>">
                        <input type="hidden" name="tax_name[]" id="tax_name_<?php echo $i; ?>" value="<?php echo $tab2['tax_name']; ?>">
                        <label class="kt-checkbox"><input aria-label="Checkbox" class="itemRow" type="checkbox" data-rowid="<?php echo $i; ?>"> <span></span> </label>
                    </td>

                    <td id='other_<?php echo $i; ?>' class="p-1">
                        <?php if($tab2['item_id'] >= 0){ ?>
                            <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="items"  data-fieldsname="item_name"name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                             <option value="0"> Select Any Item  </option>
                             <?php $like_item_check = 0; $itmdoclist = "";  foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']) { $like_item_check = 1;
}
if($tab2['item_id']== $item['id']){  $itmdoclist = $item['compliance_specification_documents']; ?>
                                 <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['sku']." - ".$item['item_name']; ?>
                             <?php } ?>
                             <?php } ?>
                             <option value="-1">Other</option>
                         </select>
                         <input type="hidden" name="transaction_item_row_id[]" id="transaction_item_row_id_<?php echo $i; ?>"  class="form-control" value="<?php echo $tab2['transaction_item_row_id']; ?>">

                         <input type="hidden" name="ref_transaction_item_table_id[]" id="ref_transaction_item_table_id_<?php echo $i; ?>"  class="form-control" value="<?php echo $tab2['ref_transaction_item_table_id']; ?>">

                         <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
                         <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
                         <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>">
                        <?php }else { ?>
                        <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
                        <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['productCode']; ?>">
                        <?php } ?>
                         <span id="party_sku_concat_<?php echo $i; ?>" class="d-block f-10"><?php echo $tab2['party_sku_number']; ?></span>
                        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
                    </td>

                <td class="p-1 w-full uom-custom">
                    <select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>" onchange="baseUOMConversion('<?php echo $i; ?>')">
                        <option value="0"> UOM </option>
                        <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                     <?php } ?>
                 </select>
                 <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
                  <p class="mt-3" id="item_history_detail_btn_<?php echo $i; ?>">
                    <?php if($tab2['item_name'] !=""){ ?>
                        <span class="m-l-5 text-primary cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Click To Open Item History" onclick="getPreviousOrder('<?php echo $tab2['item_id']; ?>','<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti ti-timer icon-dark"></i> Item History</span>
                    <?php } ?>
                </p>
                <div id="custom-popup_2">
                    <div class="custom-popup-open item_order_details_<?php echo $i; ?> text-start" style="top: -90px; display: none; width:560px; left: 130px;">

                    </div>
                </div>
             </td>
             <td class="p-1">
                <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount decimalnumberallowe" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off" <?php echo $tab2['quantity']==""?"readonly":""; ?>>
                <p>
                    <input type="hidden" id="hide_baseuomtext_<?php echo $i; ?>" value="<?php echo $itemres[$tab2['item_id']]['base_uom']?>">
                    <span class="float-left f-10">Base Conv :</span>
                    <span class="float-right f-10 " id="baseuomtext_<?php echo $i; ?>">NA</span>
                </p>
                <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
                <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
            <table class="table table-sm border-none mt-3 f-12">
                <tr data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Sales Order">
                    <th scope="col" class="text-start p-0">SO</th>
                    <td class="p-0">:</td>
                    <td class="p-0" style="text-align: right;" id="item_history_so_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['so']);?></td>
                </tr>
                <tr data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Purchase Order">
                    <th scope="col" class="text-start p-0">PO</th>
                    <td class="p-0">:</td>
                    <td class="p-0" style="text-align: right;" id="item_history_po_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['po']);?></td>
                </tr>
                <tr id="grn_items_inv_qty_<?php echo $tab2['item_id']; ?>">
                    <th scope="col" class="text-start p-0">Invoice Qty</th>
                    <td class="p-0">:</td>
                    <td class="p-0" style="text-align: right;" ><?php echo customeNumberFormate($item_details['grn_inv_qty']);?></td>
                </tr>
            </table>
            <!-- item history -->
            </td>
            <td class="p-1">
                <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount decimalnumberallowe" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off" <?php echo $tab2['item_price']==""?"readonly":""; ?>>

                <input aria-label="Input field" type="<?php if($table1['discount_mode'] != 'DI'){ echo "hidden";
} ?>" name="pdiscount_amount[]" value="<?php echo $tab2['discount_amount']; ?>"
 <?php if($table1['discount_mode'] != 'DI'){ echo "readonly";
} ?> class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">

                <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
                <!-- item history -->

                 <table class="table table-sm border-none mt-3 f-12">
                    <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Quantity On Hand">
                        <th scope="col" class="text-start p-0">QOH</th>
                        <td class="p-0">:</td>
                        <td class="p-0" style="text-align: right;" id="item_history_qoh_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['qoh']);?></td>
                    </tr>
                    <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Available Qty">
                        <th scope="col" class="text-start p-0">Max</th>
                        <td class="p-0">:</td>
                        <td class="p-0" style="text-align: right;" id="item_history_available_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['available']);?></td>
                    </tr>
                    <tr id="grn_items_qty_<?php echo $tab2['item_id']; ?>" >
                        <th scope="col" class="text-start p-0">GRN Qty</th>
                        <td class="p-0">:</td>
                        <td class="p-0" style="text-align: right;" ><?php echo customeNumberFormate($item_details['grn_qty']);?></td>
                    </tr>
                </table>
            <!-- item history -->
            </td>
            <td class="p-1 w-full text-center" >
                <div id="hsn_and_sac_code_id_<?php echo $i; ?>" class="f-12 m-b-20">
                    <?php if($tab2['tax_value'] != "" && $tab2['tax_value'] != "0"){ ?>
                        <span class="d-block"><span class="f-w-600">HSN </span>- <?php echo $tab2['tax_name']; ?></span>
                        <span class="d-block"><span class="f-w-600">Tax </span>- <?php echo $tab2['tax_value']; ?></span>
                 <?php }else{ ?><span class="d-block">-</span><span class="d-block">-</span><?php
} ?>
                </div>
                <!-- item history -->

                <table class="table table-sm border-none  f-12 ">
                    <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Minimum Quantity Level">
                        <th scope="col" class="text-start p-0">Min</th>
                        <td class="p-0">:</td>
                        <td class="p-0" style="text-align: right;" id="item_history_min_ql_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['min_ql']);?></td>
                    </tr>
                    <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Maximum Quantity Level">
                        <th scope="col" class="text-start p-0">Max</th>
                        <td class="p-0">:</td>
                        <td class="p-0" style="text-align: right;" id="item_history_max_ql_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['max_ql']);?></td>
                    </tr>
                </table>
                <input type="hidden" name="item_details[so][]" id="item_history_so_text_<?php echo $i; ?>" value="<?php echo $item_details['so'];?>">
                <input type="hidden" name="item_details[po][]" id="item_history_po_text_<?php echo $i; ?>" value="<?php echo $item_details['po'];?>">
                <input type="hidden" name="item_details[qoh][]" id="item_history_qoh_text_<?php echo $i; ?>" value="<?php echo $item_details['qoh'];?>">
                <input type="hidden" name="item_details[available][]" id="item_history_available_text_<?php echo $i; ?>" value="<?php echo $item_details['available'];?>">
                <input type="hidden" name="item_details[min_ql][]" id="item_history_min_ql_text_<?php echo $i; ?>" value="<?php echo $item_details['min_ql'];?>">
                <input type="hidden" name="item_details[max_ql][]" id="item_history_max_ql_text_<?php echo $i; ?>" value="<?php echo $item_details['max_ql'];?>">
                <input type="hidden" name="item_details[grn_inv_qty][]" id="item_history_grn_inv_qty_text_<?php echo $tab2['item_id']; ?>" value="<?php echo $item_details['grn_inv_qty'];?>">
                <input type="hidden" name="item_details[grn_qty][]" id="item_history_grn_qty_text_<?php echo $tab2['item_id']; ?>" value="<?php echo $item_details['grn_inv_qty'];?>">

            <!-- item history -->
            </td>
            <td class="p-1">
                <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
                <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['discount_amount']); ?>" readonly class="form-control price amount">
                <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control" id="ptax_amount_<?php echo $i; ?>">
                <input type="hidden" name="p_amount[]" value="<?php echo $tab2['taxabled_amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
                <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
                <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">

            </td>
        </tr>

        <?php $i++;
}
    } ?>
    </table>
</div>
