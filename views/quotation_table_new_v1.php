<?php 
    $itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data; 
    //echo $itemurl;
    $item_data = callAPI($itemurl);
    $itemlistres = json_decode($item_data, 1);
    foreach ($itemlistres as $itemval){
        $itemres[$itemval['id']] = $itemval; 
    }
  
?>
<script type="text/javascript">
     function getPreviousOrder(itemid,seq){
        $(".custom-popup-open").hide();
        var option = '<?php echo $option; ?>';
        $.ajax({
            url: "ajax.php",
            data:'page=getitemsorders&item_id='+itemid+'&table_name='+$('#table_name').val()+'&option='+option,
            type: "POST",
            success:function(data){
                $(".item_order_details_"+seq).html(data);
                $(".item_order_details_"+seq).show('slow');
            }
        });

       
    }
    function closePreviousOrder(){
        $(".custom-popup-open").hide();
    }
     function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');
        }
        if(item_id == '-0'){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9171']; ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    document.getElementById('amendment_id').value="Y";
                }
            });
            
        }
        $.ajax({
            url: "ajax.php",
            data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name='+$('#table_name').val()+'&ddate='+$('#transaction_date').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#transaction_date').val(),
            type: "POST",
            success:function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                var price =  parseFloat(obj.purchase_price).toFixed(2);
               // var price =  (obj.purchase_price !="" && obj.purchase_price !="null")?parseFloat(obj.purchase_price).toFixed(2):0;
                console.log("price --- "+price);
                //price = price.toPrecision(2);
                document.getElementById('productName_'+id).value=obj.item_description;
                //document.getElementById('uom_'+id).value=obj.purchase_uom;
                console.log(obj.purchase_uom);
               // $('#uom_'+id).select2().select2("val", obj.purchase_uom);
                //$("#uom_"+id).select2().select2('val',obj.purchase_uom);
                $("#uom_"+id).select2().val(obj.purchase_uom).trigger("change");
                document.getElementById('price_'+id).value=price;
                document.getElementById('item_name_'+id).value=obj.item_name;
                document.getElementById('purchase_account_'+id).value=obj.purchase_account;
                //document.getElementById('budget_'+id).data-content=obj.a;
                var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
                var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> &nbsp;&nbsp; A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> &nbsp; &nbsp; B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> &nbsp;&nbsp; C :'+obj.c+'</div>';
                //document.getElementById('budget_'+id).setAttribute("data-content",dd);
                
                document.getElementById('budget_'+id).innerHTML = text;
                document.getElementById('purchase_account_aval_'+id).value=obj.am;
                //document.getElementById('hsn_and_sac_code_id_'+id).innerHTML=obj.hsn_code;
               // document.getElementById('taxtext_'+id).innerHTML=obj.igst;
                document.getElementById('tax_'+id).value=obj.igst;
                document.getElementById('taxid_'+id).value=obj.hsn_code_id; 
                document.getElementById('tax_name_'+id).value=obj.hsn_code;
                var hsncodetax = '<span class="d-block"><span class="f-w-600">HSN </span>- '+obj.hsn_code+'</span><span class="d-block"><span class="f-w-600">Tax - </span>'+obj.igst+'</span>';
                document.getElementById('hsn_and_sac_code_id_'+id).innerHTML=hsncodetax; 
                

                // var item_details = '<p class="mb-0"><span class="f-w-600">SO</span>:<span>'+obj.sales_order_qty+'</span><span class="f-w-600 m-l-5">QOH</span>:<span>'+obj.stock_in_hand+'</span><span class="f-w-600 m-l-5">PO</span>:<span>'+obj.purchase_order_qty+'</span><span class="f-w-600 m-l-5">Available</span>:<span>'+obj.available_qty+'</span></p>'; 
                // item_details += '<p class="mb-0"><span class="f-w-600">Min Qty Limit</span>:<span>'+obj.minimum_level+'</span><span class="f-w-600 m-l-5">Max Qty Limit</span>:<span>'+obj.maximum_capacity+'</span></p>';
                // item_details += '<p class="mb-0"><span class="f-w-600">Min Order Qty</span>:<span>'+obj.reorder_qty+'</span><span class="m-l-5 text-primary float-end cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Open Item Histories" onclick="getPreviousOrder('+item_id+','+id+')"><i class="ti-server"></i> Item Histories</span></p>';
                //  item_details += '<div id="custom-popup"><div class="custom-popup-open item_order_details_'+id+' text-start" style="top: -75px; display: none; width:560px; left: 270px;"></div></div>';
                // $('#item_details_'+id).html(item_details);

                $('#item_history_detail_btn_'+id).html('<span class="m-l-5 text-primary float-end cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Open Item History" onclick="getPreviousOrder('+item_id+','+id+')"><i class="ti ti-timer icon-dark"></i> </span>');
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
                closePreviousOrder();
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
        htmlRows += '<td class="p-1"><div class="checkbox-fade fade-in-primary " style="display: inherit;margin: 0px;"><label class="form-label" > <input type="checkbox" class="itemRow" data-rowid="'+count+'"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';  
    //    htmlRows += '<td><input class="form-control" type="text" value="'+count+'" > </td>';      
     //   htmlRows += '<td id="other_'+count+'"><select class="form-select js-example-basic-single"  name= "productCode[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')" > <option value="0"> Select Any Prodcut </option><?php /**foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[item_name]; ?> </option> <?php } **/ ?><option  value = "-1"> Other</option> </select> <input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"  autocomplete="off"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value="" autocomplete="off"><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value="" autocomplete="off"><textarea name="productName[]" id="productName_'+count+'" class="form-control mt-2" autocomplete="off"></textarea></td>';
        //
       
 htmlRows += '<td id="other_'+count+'" class="p-1"><select class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice"  name= "productCode[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')" data-tablename="items" data-fieldsname="item_name" data-category="category_id"  data-categoryaccess="<?php echo ($doc_access_setups_user['item_category'] !="" AND is_array($doc_access_setups_user['item_category']))?implode(',',json_decode($doc_access_setups_user['item_category'],1)):""; ?>" data-refitemid="<?php echo (count((array)$reftableitemidlist) != 0 AND is_array($reftableitemidlist))?implode(',',$reftableitemidlist):""; ?>"> <option value="0"> Select Any One Item </option></select> <input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"  autocomplete="off"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value="" autocomplete="off"><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value="" autocomplete="off"><textarea name="productName[]" id="productName_'+count+'" class="form-control mt-2" autocomplete="off"></textarea> <div id ="item_details_'+count+'"></div></td>';
     
  


        //htmlRows += '<td><input type="text" name="productCode[]" id="productCode_'+count+'" class="form-control" autocomplete="off"></textarea></td>';          
        //htmlRows += '<td></td> ';       
        htmlRows += '<td class="p-1"><select class="form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'"><option value="0"> UOM  </option><?php  foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php } ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option> <?php } ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div> <textarea  name="customer_description[]" placeholder="Customer Description" id="customer_description_'+count+'" class="form-control mt-2 " autocomplete="off"></textarea>  </td> '; 
        
        

        htmlRows += '<td class="p-1"><input type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div> <table class="table table-sm border-none mt-3 f-12"><tr data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Sales Order"><th class="text-start p-0">SO</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_so_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Purchase Orders"><th class="text-start p-0">PO</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_po_'+count+'">0.00</td></tr></table></td>';           
        
        htmlRows += '<td class="p-1"><input type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div><table class="table table-sm border-none mt-3 f-12"><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Quantity On Hand"><th class="text-start p-0">QOH</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_qoh_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Available Qty"><th class="text-start p-0">AVL</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_available_'+count+'">0.00</td></tr></table></td>';   
 
 

        //htmlRows += '<td><input type="text" name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off"></td>';    
       // htmlRows += '<td class="p-1"><select class="form-select js-example-basic-single"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Tax </option><?php /**foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php } ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php } **/  ?>  </select><div style="display: none;color: red;" id="tax_err_'+count+'"> Enter Tax</div> </td>'; 

         htmlRows += '<td class="p-1 text-center" ><div id="hsn_and_sac_code_id_'+count+'" class="f-12 m-b-20"><span class="d-block">-</span><span class="d-block">-</span></div><table class="table table-sm border-none  f-12"><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Minimum Quantity Level"><th class="text-start p-0">Min</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_min_ql_'+count+'">0.00</td></tr><tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Maximum Quantity Level"><th class="text-start p-0">AVL</th><td class="p-0">:</td><td class="p-0" align="right" id="item_history_max_ql_'+count+'">0.00</td></tr></table><input type="hidden" name="item_details[so][]" id="item_history_so_text_<?php echo $i; ?>" value="<?php echo customeNumberFormate($item_details['so']);?>"><input type="hidden" name="item_details[po][]" id="item_history_po_text_'+count+'" value="0"><input type="hidden" name="item_details[qoh][]" id="item_history_qoh_text_'+count+'" value="0"><input type="hidden" name="item_details[available][]" id="item_history_available_text_'+count+'" value="0"><input type="hidden" name="item_details[min_ql][]" id="item_history_min_ql_text_'+count+'" value="0"><input type="hidden" name="item_details[max_ql][]" id="item_history_max_ql_text_'+count+'" value="0"></td>';

            

        //htmlRows += '<td> </td>'; 
        htmlRows += '<td class="p-1"><input type="hidden" name="tax[]" id="tax_'+count+'" value="<?php echo $tab2['tax_value']; ?>">  <input type="hidden" name="taxid[]" id="taxid_'+count+'" value=""> <input type="hidden" name="tax_name[]" id="tax_name_'+count+'" value=""> <div> <div style="float: left;width: 90%;"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off">  <input type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></div>'; 
        htmlRows +='<div class="dropdown dropdown-inline" style="padding: 3px;"><button type="button" class="btn btn-hover-brand btn-elevate-hover btn-icon btn-sm btn-icon-md btn-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-briefcase"></i>  </button> <div class="dropdown-menu dropdown-menu-right" id="budget_'+count+'">   <div class="dropdown-item" >&nbsp;No Item Selected</div> </div></div></div><div style="float: left;width: 90%;"><input type="text" class="form-control datepicker  m-t-20"  value="<?php echo reqDate(); ?>"  name="req_date[]" autocomplete="off" id="kt_datepicker_3'+count+'" /></div><div style="float: left;width: 10%;"><p class="mt-4" id="item_history_detail_btn_'+count+'"></p><div id="custom-popup"><div class="custom-popup-open item_order_details_'+count+' text-start" style="top: -90px; display: none; width:560px;"></div></div></div></td>';  
        //htmlRows +='<div style="padding: 6px;cursor: pointer;"  id="budget_'+count+'" data-toggle="kt-popover" title="Budget" data-content="No Item Selected" data-toggle="kt-tooltip" title=""><i class="fa fa-briefcase" style="padding: 5px;"></i></div></div></td>';
        //htmlRows += '<td><input type="text" name="total[]" id="total_'+count+'" class="form-control total amount" autocomplete="off"></td>';    
       // htmlRows +='<td> <div class="input-group date">   </div></td>'; 

       /* htmlRows += '<td> <input type="text" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '<td><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off">  <input type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';*/
        //htmlRows += '<td><input type="text" name="total[]" id="total_'+count+'" class="form-control total amount" autocomplete="off"></td>';          
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
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
                    extracat: $(this)[0].dataset.category,
                    extracat1: $(this)[0].dataset.categoryaccess,
                    refitemid: $(this)[0].dataset.refitemid,
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
       templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
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
<?php  echo "<pre>"; print_r($table2); echo "</pre>";  ?> 
<table class="table table-hover table-responsive" id="invoiceItem"> 
    <tr>
        <th class="thheader p-1" width="1%"> 
            <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
                <label class="kt-checkbox">
                    <input id="checkAll"  type="checkbox">
                    <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                </label>
            </div>
        </th>
        <!--  <th class="thheader" width="5%">#</th> -->
        <th width="25%" class="p-1">Items/Service <span class="requried"></span></th>
        <th class="thheader p-1" width="14%">UOM <span class="requried">*</span></th>
        <th class="thheader p-1 text-right" width="15%">Qty <span class="requried">*</span></th>
        <th class="thheader p-1 text-right" width="15%">Rate <span class="requried">*</span></th>
        <th class="thheader p-1" width="10%">HSN/Tax <span class="requried">*</span></th>
        <!--  <th class="thheader" width="8%">Tax Amount</th> -->   
        <th class="thheader p-1 text-right" width="20%">Amount</th>   
    </tr>    
    <?php if(count((array)$table2)>0){ $i=1; 
     foreach ($table2 as $tab2) {
     $item_details = json_decode($tab2['item_details'],1);
                
                   ?>
    <tr>
        <td class="p-1">
            <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
              <label class="form-label">
                <input type="checkbox" class="itemRow" data-rowid="<?php echo $i; ?>">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
              </label>
            </div>
        </td>

        <td class="p-1" id='other_<?php echo $i; ?>' style=""> 
            <?php if($tab2['item_id'] >= 0){

             ?>

           
            <?php  /** ?>
            <select class="form-select js-example-basic-single"   name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                <option value="0"> Select Any Item  </option>
                <?php $like_item_check = 0; foreach ($itemres as $item) {
                        if($tab2['item_id']== $item['id']) { $like_item_check = 1; }  ?>

                <option <?php if($tab2['item_id']== $item['id']){ ?>selected <?php } ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?> 
                <?php } ?>
                <?php if($like_item_check == 0 && $tab2['item_id'] != 0){ ?>  
                        <option  selected value = <?php echo $tab2['item_id']; ?>   > <?php echo $tab2['item_name']; ?> 
                <?php } ?>
                </option> <option value="-1">Other</option>
            </select>

            <?php **/  //print_r($doc_access_setups_user['item_category']); ?>



            <select class="form-select js-data-example-ajax-multiselect selectitemorservice"  data-tablename="items"  data-fieldsname="item_name" data-category="category_id"  data-categoryaccess="<?php echo ($doc_access_setups_user['item_category'] !="" AND is_array($doc_access_setups_user['item_category']))?implode(',',json_decode($doc_access_setups_user['item_category'],1)):""; ?>" data-refitemid="<?php echo (count((array)$reftableitemidlist) != 0 AND is_array($reftableitemidlist))?implode(',',$reftableitemidlist):""; ?>" name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>"> 
             <option value="0"> Select Any One Item </option>
            <?php $like_item_check = 0;   
            foreach ($itemres as $item){    
                if($tab2['item_id']== $item['id']) { $like_item_check = 1; }   
                if($tab2['item_id']== $item['id']){  ?>
                    <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['item_name']; ?> 
                <?php } }  ?> 
                <option value="-1">Other</option>

            </select>

            <input type="hidden" name="item_table_row_id[]" id="item_table_row_id_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_table_row_id']; ?>" autocomplete="off">
            <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off"> 
            <input type="hidden" name="purchase_account[]" id="purchase_account_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" autocomplete="off">
            <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_<?php echo $i; ?>" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>" autocomplete="off">


            <?php }else { ?>
            <input type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
            <input type="hidden" name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['productCode']; ?>" autocomplete="off">
            <?php } ?>
            <textarea  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2 " autocomplete="off"><?php echo $tab2['description']; ?></textarea>
        </td>
  <td class="p-1 w-full">
        <select class="form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
            <option value="0">UOM  </option>
            <?php foreach ($uomlist as $uom) {  ?>
                 <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php } ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>        
        <textarea  name="customer_description[]" placeholder="Customer Description" id="customer_description_<?php echo $i; ?>" class="form-control mt-2 " autocomplete="off"><?php echo $tab2['customer_description']; ?></textarea>
        
    </td>       
    <td class="p-1">
        <input type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off"> 
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
        <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
        <!-- item history -->
        <!-- <p class="mb-0  mt-3 f-12" data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Sales Order"><span class="f-w-600 float-start">SO</span>:<span class="float-end" id="item_history_so_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['so']);?></span></p>
        <p class="mb-0 f-12" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Purchase Orders"><span class="f-w-600 float-start">PO</span>:<span class="float-end" id="item_history_po_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['po']);?></span></p> -->

        <table class="table table-sm border-none mt-3 f-12">
            <tr data-bs-toggle="tooltip" data-placement="bottom"  data-bs-original-title="Sales Order">
                <th class="text-start p-0">SO</th>
                <td class="p-0">:</td>
                <td class="p-0" align="right" id="item_history_so_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['so']);?></td>
            </tr>
            <tr>
                <th class="text-start p-0">PO</th>
                <td class="p-0">:</td>
                <td class="p-0" align="right" id="item_history_po_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['po']);?></td>
            </tr>
        </table>

        <!-- item history -->
    </td> 
    <td class="p-1">
        <input type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">

        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
        <!-- item history -->
            <!-- <p class="mb-0 mt-3 f-12" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Quantity On Hand"><span class="f-w-600 float-start">QOH</span>:<span class="float-end" id="item_history_qoh_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['qoh']);?></span></p>
            <p class="mb-0 f-12" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Available Qty"><span class="f-w-600 float-start">Available</span>:<span class="float-end" id="item_history_available_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['available']);?></span></p> -->
            <table class="table table-sm border-none mt-3 f-12">
                <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Quantity On Hand">
                    <th class="text-start p-0">QOH</th>
                    <td class="p-0">:</td>
                    <td class="p-0" align="right" id="item_history_qoh_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['qoh']);?></td>
                </tr>
                <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Available Qty">
                    <th class="text-start p-0">AVL</th>
                    <td class="p-0">:</td>
                    <td class="p-0" align="right" id="item_history_available_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['available']);?></td>
                </tr>
            </table>
        <!-- item history -->
    </td>
    <td class="p-1 w-full text-center" >
        <div id="hsn_and_sac_code_id_<?php echo $i; ?>" class="f-12  m-b-20">
        <?php if($tab2['tax_value'] != "" AND $tab2['tax_value'] != "0"){ ?>
            <span class="d-block"><span class="f-w-600">HSN </span>- <?php echo $itemidwithhsn[$tab2['item_id']]; ?></span>
            <span class="d-block"><span class="f-w-600">Tax </span>- <?php echo $tab2['tax_value']; ?></span>
      <?php }else{ ?><span class="d-block">-</span><span class="d-block">-</span><?php } ?>
        </div>
        <!-- item history -->
            <!-- <p class="mb-0 m-t-20 f-12" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Minimum Quantity Level"><span class="f-w-600 float-start">Min</span>:<span class="float-end" id="item_history_min_ql_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['min_ql']);?></span></p>
            <p class="mb-0 f-12" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Maximum Quantity Level"><span class="f-w-600 float-start">Max</span>:<span class="float-end" id="item_history_max_ql_<?php // echo $i; ?>"><?php // echo customeNumberFormate($item_details['max_ql']);?></span></p> -->

            <table class="table table-sm border-none  f-12 ">
                <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Minimum Quantity Level">
                    <th class="text-start p-0">Min</th>
                    <td class="p-0">:</td>
                    <td class="p-0" align="right" id="item_history_min_ql_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['min_ql']);?></td>
                </tr>
                <tr data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Maximum Quantity Level">
                    <th class="text-start p-0">Max</th>
                    <td class="p-0">:</td>
                    <td class="p-0" align="right" id="item_history_max_ql_<?php echo $i; ?>"><?php echo customeNumberFormate($item_details['max_ql']);?></td>
                </tr>
            </table>

            <input type="hidden" name="item_details[so][]" id="item_history_so_text_<?php echo $i; ?>" value="<?php echo $item_details['so'];?>">
            <input type="hidden" name="item_details[po][]" id="item_history_po_text_<?php echo $i; ?>" value="<?php echo $item_details['po'];?>">
            <input type="hidden" name="item_details[qoh][]" id="item_history_qoh_text_<?php echo $i; ?>" value="<?php echo $item_details['qoh'];?>">
            <input type="hidden" name="item_details[available][]" id="item_history_available_text_<?php echo $i; ?>" value="<?php echo $item_details['available'];?>">
            <input type="hidden" name="item_details[min_ql][]" id="item_history_min_ql_text_<?php echo $i; ?>" value="<?php echo $item_details['min_ql'];?>">
            <input type="hidden" name="item_details[max_ql][]" id="item_history_max_ql_text_<?php echo $i; ?>" value="<?php echo $item_details['max_ql'];?>">

        <!-- item history -->
        <?php /** ?>
        <select class="form-select js-example-basic-single"  name= "tax[]" id="tax_<?php echo $i; ?>" >
            <option value="0">No Tax</option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  <?php if($tab2['tax_value'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
    <?php **/ ?>
    </td>
        <td class="p-1">
            <input type="hidden" name="tax[]" id="tax_<?php echo $i; ?>" value="<?php echo $tab2['tax_value']; ?>">
            <input type="hidden" name="taxid[]" id="taxid_<?php echo $i; ?>" value="<?php echo $tab2['tax_id']; ?>"> 
             <input type="hidden" name="tax_name[]" id="tax_name_<?php echo $i; ?>" value="<?php echo $tab2['tax_name']; ?>">
             <?php if($tab2['total_amount'] > 0){ $data_content = '<div class="dropdown-item"><i class="flaticon-coins"></i>&nbsp;&nbsp; A :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['a']).'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i>&nbsp;&nbsp; B :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['b']).'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i>&nbsp;&nbsp; C :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['c']).'</div>'; if($tab2['total_amount'] > $response_code[$tab2['purchase_account']]['a']){ $sty = "style='color:#F00'";} }else{ $data_content = '<div class="dropdown-item">No Item Selected</div>'; }
                 ?>
            <div> 
                <div style="float: left;width: 90%;">
            
                    <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
                    
                    <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
                    
                    <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
                    
                    <input type="hidden" name="p_amount[]" value="<?php echo $tab2['taxabled_amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
                    
                    <input type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
                    <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>" autocomplete="off">
                </div>

                <div class="dropdown dropdown-inline" style="padding: 3px;" >
                    <button type="button" class="btn btn-hover-brand btn-elevate-hover btn-icon btn-sm btn-icon-md btn-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-briefcase"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" id='budget_<?php echo $i; ?>'>
                       <?php echo $data_content;?>
                    </div>
                </div>


                <!-- <div style="padding: 6px;cursor: pointer;"  id='budget_<?php echo $i; ?>' data-toggle="kt-popover" title="Budget" data-content="<?php echo $data_content;?>"data-toggle="kt-tooltip" title=""><i class="fa fa-briefcase" style="padding: 5px;"></i></div> -->
            </div>
             <div style="float: left;width: 90%;">
            <input type="text" class="form-control  m-t-20 datepicker date_custom_width"  value="<?php echo reqDate($tab2['req_date']); ?>"  name="req_date[]"  id="kt_datepicker_3<?php echo $dateid; ?>" />
            </div>
            <div style="float: left;width: 10%;">
                <p class="mt-4" id="item_history_detail_btn_<?php echo $i; ?>">
                    <?php if($tab2['item_name'] !=""){ ?>
                        <span class="m-l-5 text-primary cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Click To Open Item History" onclick="getPreviousOrder('<?php echo $tab2['item_id']; ?>','<?php echo $i; ?>')"><i class="ti ti-timer icon-dark"></i></span>
                    <?php } ?>
                </p>
                <div id="custom-popup">
                    <div class="custom-popup-open item_order_details_<?php echo $i; ?> text-start" style="top: -90px; display: none; width:560px;">
                        
                    </div>
                </div>
            </div>
        </td>
    
    </tr>

    <?php $i++; } } ?>                      
</table>
