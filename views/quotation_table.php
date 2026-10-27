<?php
    $itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
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
        data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name='+$('#table_name').val()+'&ddate='+$('#transaction_date').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#transaction_date').val(),
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
            var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> &nbsp;&nbsp; A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> &nbsp; &nbsp; B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> &nbsp;&nbsp; C :'+obj.c+'</div>';

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
        var newdateid = 'kt_datepicker_3'+count;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input aria-label="Checkbox" type="checkbox" class="itemRow"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';

 htmlRows += '<td id="other__2"+count+'"><select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice"  name= "productCode[]" id="productCode__3"+count+'" onchange="itemChange(this.value,'+count+')" data-tablename="items"  data-fieldsname="item_name" > <option value="0"> Select Any Item </option></select> <input type="hidden" name="item_name[]" id="item_name__3"+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account__2"+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval__2"+count+'" class="form-control" value=""><textarea aria-label="Product Name[]" name="productName[]" id="productName__2"+count+'" class="form-control mt-2"></textarea></td>';


        htmlRows += '<td><select aria-label="Uom[]" class="form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2[uom] == $uom[genc_detail_code]){ ?>selected <?php
} ?>  value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div></td> ';
        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div></td>';
        htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div></td>';
        htmlRows += '<td><select aria-label="Tax[]" class="form-select js-example-basic-single"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php
} ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php
} ?>  </select><div style="display: none;color: red;" id="tax_err_'+count+'"> Enter Tax</div> </td>';

        htmlRows += '<td><div> <div style="float: left;width: 90%;"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount__2"+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></div>';
        htmlRows +='<div class="dropdown dropdown-inline" style="padding: 3px;"><button type="button" class="btn btn-hover-brand btn-elevate-hover btn-icon btn-sm btn-icon-md btn-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i>  </button> <div class="dropdown-menu dropdown-menu-right" id="budget_'+count+'">   <div class="dropdown-item" >&nbsp;No Item Selected</div>          </div></div></div><input aria-label="Input field" type="text" class="form-control datepicker  m-t-20"  value="<?php echo reqDate(); ?>"  name="req_date[]" autocomplete="off" id="kt_datepicker_3'+count+'" /></td>';


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
     $("#"+newdateid).datepicker({
            rtl: KTUtil.isRTL(),
            orientation: "bottom right",
           /* templates: arrows,*/
            todayHighlight: true,format: DateFormate,
            /*templates: arrows*/
        });
    });
 });
</script>
<div class="table-responsive">
    <table class="table table-hover " id="invoiceItem">
        <tr>
            <th scope="col" class="thheader" style="width: 1%;">
                <div class="checkbox-fade fade-in-primary">
                    <label class="kt-checkbox">
                        <input aria-label="Check All" id="checkAll"  type="checkbox">
                        <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                    </label>
                </div>
            </th>

            <th scope="col" class="" style="width: 20%;">Items/Service <span class="requried"></span></th>

            <th scope="col" class="thheader" style="width: 8%;">UOM <span class="requried">*</span></th>
            <th scope="col" class="thheader" style="width: 8%;">Qty <span class="requried">*</span></th>
            <th scope="col" class="thheader" style="width: 12%;">Rate <span class="requried">*</span></th>
            <th scope="col" class="thheader" style="width: 8%;">Tax <span class="requried">*</span></th>

            <th scope="col" class="thheader" style="width: 15%;">Amount</th>
        </tr>
        <?php if(count((array)$table2)>0){ $i=1;
        foreach ($table2 as $tab2) { //print_r($tab2); ?>
        <tr>
            <td>
                <div class="checkbox-fade fade-in-primary ">
                  <label class="form-label">
                    <input aria-label="Checkbox" type="checkbox" class="itemRow">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                  </label>
                </div>
            </td>

            <td id='other_<?php echo $i; ?>' style="width: 150px;">
                <?php if($tab2['item_id'] >= 0){ ?>


                <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="items"  data-fieldsname="item_name" name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                 <option value="0"> Select Any Item  </option>
                <?php $like_item_check = 0; foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']) { $like_item_check = 1;
}
if($tab2['item_id']== $item['id']){  ?>
                 <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['item_name']; ?>
                    <?php } ?>
                    <?php } ?>
                    <option value="-1">Other</option>

                </select>

                <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
                <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
                <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']][a]; ?>">
                <?php }else { ?>
                <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
                <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['productCode']; ?>">
                <?php } ?>
                <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2 " autocomplete="off"><?php echo $tab2['description']; ?></textarea>
            </td>

             <td>
                <select aria-label="Uom[]" class="form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
                    <option value="0"> Select UOM  </option>
                    <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
            </td>
            <td>
                <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
                <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
                <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
            </td>
            <td>
                <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">

                <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
            </td>
            <td>
                <select aria-label="Tax[]" class="form-select js-example-basic-single"  name= "tax[]" id="tax_<?php echo $i; ?>"  >
                    <option value="0">Select Tax</option>
                    <?php foreach ($taxres as $key => $tax) {  ?>
                    <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
            </td>
            <td>
                 <?php if($tab2['total_amount'] > 0){ $data_content = '<div class="dropdown-item"><i class="flaticon-coins"></i>&nbsp;&nbsp; A :'.customeummarmonyformate($response_code[$tab2['purchase_account']][a]).'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i>&nbsp;&nbsp; B :'.customeummarmonyformate($response_code[$tab2['purchase_account']][b]).'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i>&nbsp;&nbsp; C :'.customeummarmonyformate($response_code[$tab2['purchase_account']][c]).'</div>'; if($tab2['total_amount'] > $response_code[$tab2['purchase_account']][a]){ $sty = "style='color:#F00'";
}
}else{ $data_content = '<div class="dropdown-item">No Item Selected</div>';
}
                     ?>
                <div>
                    <div style="float: left;width: 90%;">
                        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
                        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
                        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
                        <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
                        <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
                        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
                    </div>

                    <div class="dropdown dropdown-inline" style="padding: 3px;" >
                        <button type="button" class="btn btn-hover-brand btn-elevate-hover btn-icon btn-sm btn-icon-md btn-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-briefcase"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" id='budget_<?php echo $i; ?>'>
                           <?php echo $data_content;?>
                        </div>
                    </div>

                </div>
                <input aria-label="Input field" type="text" class="form-control  m-t-20 datepicker"  value="<?php echo reqDate($tab2['req_date']); ?>"  name="req_date[]"  id="kt_datepicker_3<?php echo $dateid; ?>" />
            </td>

        </tr>

        <?php $i++; ?>
        <?php } ?>
        <?php } ?>
    </table>
</div>
