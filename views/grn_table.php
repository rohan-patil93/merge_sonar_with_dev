<?php
    $itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
    $item_data = callAPI($itemurl);
    $itemres = json_decode($item_data, 1);
    foreach ($itemres as $num_type) {
        $pop_type[$num_type['id']] = $num_type['tracking_type'];
    }
    $random = $_SESSION['userid']."_".time()."_".uniqid();
    $valrandom = "'".$random."'";
?>
<script type="text/javascript">
    function itemqualitychecks(item_id){
        var table_name = document.getElementById('table_name').value;
        var tran_id =document.getElementById('hid').value;
        jQuery.ajax({
            url: "views/custom_list_approve_quality_check_popup.php",
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
    function damagedqty(id,type){
        console.log("Check ID :"+id+"&type :"+type);
        var invoiced_quantity = $('#invoiced_quantity_'+id).val();
        $('#quantity_'+id).val(invoiced_quantity);
        var quantity = invoiced_quantity;
        var order_quantity = $('#order_quantity_'+id).val();
         var damagedqtys = 0;
         console.log("invoiced_quantity ID :"+invoiced_quantity+"&quantity :"+quantity+"order_quantity ID :"+order_quantity+"&damagedqty :"+damagedqtys);
         var msg = "";
          damagedqtys = parseFloat(invoiced_quantity)-parseFloat(quantity);
        if(type=="invoiced_quantity"){
            if(parseFloat(order_quantity)<parseFloat(invoiced_quantity)){
              msg = 'Invoiced quantity cannot be more than Order Quantity';
              damagedqtys = parseFloat(0);
             $('#invoiced_quantity_'+id).val(order_quantity);
             $('#quantity_'+id).val(order_quantity);
           }
        }
        if(type=="quantity"){
            if(parseFloat(invoiced_quantity)<parseFloat(quantity)){
                msg = 'Received quantity cannot be more than invoiced quantity';
                $('#quantity_'+id).val(invoiced_quantity);
                damagedqtys = parseFloat(0);
            }
        }
        if(msg!=""){
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", msg, "warning");
        }
        $('#damaged_quantity_'+id).val(parseFloat(damagedqtys).toFixed(2));
        $('#iddamaged_quantity_'+id).text(parseFloat(damagedqtys).toFixed(2));
        calculateTotal();
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
            document.getElementById('item_name_'+id).value=obj.sku+" - "+obj.item_name;
            document.getElementById('purchase_account_'+id).value=obj.purchase_account;
            var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
            var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> C :'+obj.c+'</div>';

            document.getElementById('budget_'+id).innerHTML = text;
            document.getElementById('purchase_account_aval_'+id).value=obj.am;

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
            var invquantity = "invoiced_quantity";
            htmlRows += '<tr>';
            htmlRows += '<td class="p-2"><label class="kt-checkbox"> <input aria-label="Checkbox"  class="itemRow" type="checkbox" data-rowid="'+count+'">  <span> </span> </label> </td>';
            htmlRows += '<td id="other_'+count+'" class="p-2"><select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' selectitemorservice" name= "productCode[]" id="productCode__2"+count+'" onchange="itemChange(this.value,'+count+')"  data-tablename="items"  data-fieldsname="item_name" > <option value="0"> Select Any Item </option></select><input type="hidden" name="transaction_item_row_id[]" id="transaction_item_row_id_'+count+'" value=""><input type="hidden" name="ref_transaction_item_table_id[]" id="ref_transaction_item_table_id_'+count+'" value=""><input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control mt-2"></textarea></td>';
            htmlRows += '<td class="p-2"><select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'" onchange="baseUOMConversion('+count+')"><option value="0">UOM  </option> <?php  foreach ($uomlist as $uom) {  ?> <option value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div></td> ';
            htmlRows += '<td class="p-2 "><input aria-label="Invoiced Quantity[]" type="text" name="invoiced_quantity[]" id="invoiced_quantity_'+count+'" onblur="damagedqty('+count+',\''+invquantity+'\')"  class="form-control quantity amount" autocomplete="off"><p><input type="hidden" id="hide_baseuomtext_'+count+'" value=""><span class="float-left f-10">Base Conv :</span><span class="float-right f-10 " id="baseuomtext_'+count+'">NA</span></p><div style="display: none;color: red;" id="quantity_err_'+count+'" > Enter QTY</div></td>';
            htmlRows += '<td class="p-2 event_disabled"><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="damagedqty('+count+',\''+invquantity+'\')"  class="form-control quantity amount mb-2"  autocomplete="off" readonly><p class="text-danger text-right d-none"><span class="d-block">Damaged Qty </span><span id="iddamaged_quantity_'+count+'">0</span></p><input type="hidden" name="damaged_quantity[]" id="damaged_quantity_'+count+'"  class="form-control quantity amount"> <div style="display: none;color: red;" id="quantity_err__2"+count+'"> Enter QTY</div></td>';
            htmlRows += '<td class="p-2"><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div>';
            if(discount_mode == 'DO'){
                htmlRows += '<input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_'+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
            }else{
                htmlRows += '<input type="text" name="pdiscount_amount[]" value="0" class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount__2"+count+'" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">';
            }
            htmlRows += '</td>';

             htmlRows += '<td class="p-1 text-center" ><div id="hsn_and_sac_code_id_'+count+'" class="f-12 m-b-20"><span class="d-block">-</span><span class="d-block">-</span></div></td>';

            htmlRows += '<td class="p-2"><input type="hidden" id="compliance_specification_documents_'+count+'" value=""><input type="hidden" name="tax[]" id="tax__2"+count+'" value="<?php echo $tab2['tax_value']; ?>">  <input type="hidden" name="taxid[]" id="taxid_'+count+'" value=""> <input type="hidden" name="tax_name[]" id="tax_name_'+count+'" value=""><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';
            htmlRows += '<td class="p-2"><div  class="btn btn-primary"  id="">lot</div></td>';
            htmlRows += '</tr>';
            $('#invoiceItem').append(htmlRows);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                dateFormat: date_format,
                yearRange: "-60:+10",
                setDate: new Date(),
                changeMonth: true,
                changeYear: true
            });
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
                }, // let our custom formatter work
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
    })
</script>
<div class="table-responsive">
    <input type="hidden" name="random" value="<?php echo $random; ?>">
    <table class="table table-bordered" id="invoiceItem">
        <tr>
            <th scope="col" class="thheader p-1 text-start" style="width: 5%;">
                <label class="kt-checkbox">
                    <input aria-label="Check All" id="checkAll"  type="checkbox">
                    <span></span>
                </label>
            </th>

            <th scope="col" class="p-1" class="p-1" style="width: 20%;">Items/Service <span class="requried"></span></th>

            <th scope="col" class="thheader p-1" class="p-1" style="width: 10%;">UOM <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1" class="p-1" style="width: 8%;">Invoice Qty <span class="requried">*</span></th>
            <th scope="col" class="thheader p-1" class="p-1" style="width: 10%;">Received Qty <span class="requried">*</span></th>

            <th scope="col" class="thheader p-1" class="p-1" style="width: 15%;">Rate &nbsp;&nbsp;</th>
            <th scope="col" class="thheader p-1" style="width: 10%;">HSN/Tax <span class="requried">*</span></th>


            <th scope="col" class="thheader p-1" class="p-1" style="width: 18%;">Amount</th>
            <th scope="col" class="thheader p-1" class="p-1" style="width: 4%;"></th>

        </tr>
        <?php if(count((array)$table2)>0){ $i=1;
        foreach ($table2 as $tab2) { //print_r($tab2); ?>
        <tr>
            <td class="p-1">
                <label class="kt-checkbox"><input aria-label="Checkbox" class="itemRow" type="checkbox" data-rowid="<?php echo $i; ?>"> <span></span> </label>
            </td>
            <td id='other_<?php echo $i; ?>' class="p-1">
                <?php if($tab2['item_id'] >= 0){ ?>
                    <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice "  data-tablename="items"  data-fieldsname="item_name" name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                        <option value="0"> Select Any Item  </option>
                        <?php $like_item_check = 0; $itmdoclist = "";
                            foreach ($itemres as $item) {
                                if($tab2['item_id']== $item['id']) {
                                    $like_item_check = 1;
                                }
                                if($tab2['item_id']== $item['id']){ $itmdoclist = $item['compliance_specification_documents']; ?>
                                    <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['sku']." - ".$item['item_name']; ?>
                                <?php }
                            } ?>
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
                <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-1" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
            </td>
            <td class="p-1 w-full uom-custom">
                <select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>" onchange="baseUOMConversion('<?php echo $i; ?>')">
                    <option value="0">UOM</option>
                    <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?> selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
            </td>
            <td class="p-1">
                <input aria-label="Invoiced Quantity[]" type="text" name="invoiced_quantity[]" id="invoiced_quantity_<?php echo $i; ?>"  onblur="damagedqty(<?php echo $i; ?>,'invoiced_quantity')"   class="form-control amount" value="<?php echo $tab2['invoiced_quantity']; ?>"  autocomplete="off" >
                <p>
                    <input type="hidden" id="hide_baseuomtext_<?php echo $i; ?>" value="<?php echo $itemres[$tab2['item_id']]['base_uom']?>">
                    <span class="float-left f-10">Base Conv :</span>
                    <span class="float-right f-10 " id="baseuomtext_<?php echo $i; ?>">NA</span>
                </p>
                <input type="hidden" name="order_quantity[]"id="order_quantity_<?php echo $i; ?>"  value="<?php echo $tab2['invoiced_quantity']; ?>" >
            </td>
            <td class="p-1 event_disabled">
                <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="damagedqty(<?php echo $i; ?>,'quantity')"  class="form-control amount mb-2 decimalnumberallowe" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off" <?php echo $tab2['quantity']!=""?"readonly":""; ?>>
                <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>" >
                <p class="text-danger text-right d-none">
                    <span class="d-block">Damaged Qty </span>
                    <span id="iddamaged_quantity_<?php echo $i; ?>"><?php echo $tab2['damaged_quantity']; ?></span>
                </p>
                <input type="hidden" name="damaged_quantity[]" id="damaged_quantity_<?php echo $i; ?>"   class="form-control amount mt-2 decimalnumberallowe" value="<?php echo $tab2['damaged_quantity']; ?>" >
            </td>
            <td class="p-1">
                <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount decimalnumberallowe" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">
                <input aria-label="Input field" type="<?php if($table1['discount_mode'] != 'DI'){ echo "hidden";
} ?>" name="pdiscount_amount[]" value="<?php echo $tab2['discount_amount']; ?>"
 <?php if($table1['discount_mode'] != 'DI'){ echo "readonly";
} ?> class="form-control price amount mt-2 discount_textbox" id="pdiscount_amount_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Discount">


                <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
            </td>
            <td class="p-1 w-full uom-custom">
                <div id="hsn_and_sac_code_id_<?php echo $i; ?>" class="f-12  m-b-20">
                <?php if($tab2['tax_value'] != "" && $tab2['tax_value'] != "0"){ ?>
                    <span class="d-block"><span class="f-w-600">HSN </span>- <?php echo $itemidwithhsn[$tab2['item_id']]; ?></span>
                    <span class="d-block"><span class="f-w-600">Tax </span>- <?php echo $tab2['tax_value']; ?></span>
              <?php }else{ ?><span class="d-block">-</span><span class="d-block">-</span><?php
} ?>
                </div>

            </td>
            <td class="p-1">
                <input type="hidden" id="compliance_specification_documents_<?php echo $i; ?>" value='<?php echo $itmdoclist; ?>'>
                <input type="hidden" name="tax[]" id="tax_<?php echo $i; ?>" value="<?php echo $tab2['tax_value']; ?>">
                 <input type="hidden" name="taxid[]" id="taxid_<?php echo $i; ?>" value="<?php echo $tab2['tax_id']; ?>">
             <input type="hidden" name="tax_name[]" id="tax_name_<?php echo $i; ?>" value="<?php echo $tab2['tax_name']; ?>">

                <input type="hidden" name="dis_ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">

                <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
                 <input type="hidden" name="p_amount[]" value="<?php echo $tab2['taxabled_amount']; ?>" readonly class="form-control" id="p_amount_<?php echo $i; ?>">
                <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly  id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
                <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
            </td>
            <td class="p-1">
                <?php if($pop_type[$tab2['item_id']] ==1 || $pop_type[$tab2['item_id']] ==2  ){ ?>
                    <div  class="btn btn-primary" onclick="serialno(<?php echo $tab2['item_id']; ?>,<?php echo $i; ?>,<?php echo $valrandom; ?> )" id="" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php if($pop_type[$tab2['item_id']] ==1 ){ echo "Serial";
}
if($pop_type[$tab2['item_id']] ==2 ){ echo "Lot";
} ?></div>
                <?php } ?>
                <?php if($_GET['quality'] == '1'){
                $view_item_id =  deocdeId($tab2['item_id']);
                $bill_view =  "index.php?option=COM_ITEM_QUALITY_CHECK&TRNID=".$rid."&Rid=".$view_item_id;
                ?>
                <div class="mt-3">
                    <div class="curssor-pointer" onclick="itemqualitychecks(<?php echo $tab2['item_id']; ?>)" style="cursor: pointer; opacity: unset; pointer-events:visible;" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span><i class="ti-medall text-success f-18 p-2"></i></span>
                    </div>
                </div>
                <?php } ?>
            </td>
        </tr>

    <?php $i++; ?>
    <?php } ?>
    <?php } ?>
    </table>
</div>
