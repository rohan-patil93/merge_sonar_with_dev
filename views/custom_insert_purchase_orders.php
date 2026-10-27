<?php
if($_POST['submit'] == 'Add'){
    $url = API."?mode=addinvoietype&table=quotations&method=post";
    $response = file_post_contents($url, $_POST);
    if ($response) {
        header("location:index.php?option=com_view_email");
    }
}
if($_POST['submit'] == 'Update'){

}
$table1 = array('');
$table2 = array(array('item_id'=>''));
if($_GET['pr_id']){
    $table2 = array(''); $i=0;
    $rpurl = API."?mode=getPurchaseRequisitionsData&pr_id=1";
    $rp_data = callAPI($rpurl);
    $rpres = json_decode($rp_data, 1);
    $rpdata = $rpres['data'];
    $items = $rpres['items'];
    $taxes = $rpres['tax'];
    $table1['payment_terms'] = $rpdata['payment_terms'];
    $table1['place_of_supply'] = $rpdata['place_of_supply'];
    $table1['sub_total'] = $rpdata['pr_amount'];
    $table1['tax_amount'] = $rpdata['pr_tax_amount'];
    $table1['grand_total'] = $rpdata['pr_total_amount'];
    foreach($items as $item){
        $itemda['item_id']= $item['item_id'];
        $itemda['item_name']= $item['item_name'];
        $itemda['description']= trim($item['description']);
        $itemda['uom']= $item['purchase_uom'];
        $itemda['quantity']= $item['request_quantity'];
        $itemda['item_price']= $item['item_price'];
        $itemda['tax_amount']= $item['tax_amount'];
        $itemda['total_amount']= $item['total_amount'];
        $table2[$i] = $itemda; $i++;
    }
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(":", $rid);
    $edit_id = $rid[1];
    $table2 = array(''); $i=0;
    $rpurl =  API."?mode=getInvoicesData&in_id=$rid[1]&table=quotations";
    $rid= $rid[1];
    $rp_data = callAPI($rpurl);
    $rpres = json_decode($rp_data, 1);
    $rpdata = $rpres['data'];
    $items = $rpres['items'];
    $taxes = $rpres['taxes'];
     $table1['qo_number'] = $rpdata['qo_number'];
    $table1['qo_date'] = $rpdata['qo_date'];
    $table1['currency_id'] = $rpdata['currency_id'];
    $table1['currency_rate'] = $rpdata['currency_rate'];
    $table1['party_id'] = $rpdata['party_id'];
    $table1['party_name'] = $rpdata['party_name'];
    $table1['party_email'] = $rpdata['party_email'];
    $table1['party_phone'] = $rpdata['party_phone'];
    $table1['party_tax_number'] = $rpdata['party_tax_number'];
    $table1['party_address'] = $rpdata['party_address'];
    $table1['party_ship_adderss'] = $rpdata['party_ship_adderss'];
    $table1['payment_terms'] = $rpdata['payment_terms'];
    $table1['place_of_supply'] = $rpdata['place_of_supply'];
    $table1['sub_total'] = $rpdata['qo_amount'];
    $table1['tax_amount'] = $rpdata['qo_tax_amount'];
    $table1['discount_type'] = $rpdata['discount_type'];
    $table1['discount_value'] = $rpdata['discount_value'];
    $table1['discount_amount'] = $rpdata['discount_amount'];
    $table1['grand_total'] = $rpdata['qo_total_amount'];
    $table1['notes'] = $rpdata['notes'];
    foreach ($taxes as  $taxs) {
       $tax_array[$taxs['qo_item_id']] = $taxs['tax_id'];
    }
    foreach($items as $item){
        $itemda['item_id']= $item['item_id'];
        $itemda['item_name']= $item['item_name'];
        $itemda['description']= trim($item['description']);
        $itemda['uom']= $item['purchase_uom'];
        $itemda['quantity']= $item['item_quantity'];
        $itemda['item_price']= $item['item_price'];
        $itemda['tax_id']= $tax_array[$item['item_id']];
        $itemda['tax_amount']= $item['tax_amount'];
        $itemda['total_amount']= $item['total_amount'];
        $table2[$i] = $itemda; $i++;
    }
}

$cusurl = API."?mode=gettabledetails&table=".TBL_VENDOR;
$cusresponse = callAPI($cusurl);
$cusresponse = json_decode($cusresponse,1);
$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PLACEOFSUPPLY&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponse = callAPI($typeurl);
$posresponse = json_decode($typeresponse,1);
$data1["field"] = "id,item_name,itme_description,purchase_price,purchase_uom";
$data = urlencode(json_encode($data1));
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);

$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PAYMETERM&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponsept = callAPI($typeurl);
$posresponsept = json_decode($typeresponsept,1);

$uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=UOM&table=".TBL_GENRAL_CODE_DETAILS;
$uomlist = callAPI($uomurl);
$uomlist = json_decode($uomlist,1);

$crurl = API."?mode=gettabledetailswithconditions&table=".TBL_CURRENCIES."&data=".$crdata;
$cr_data = callAPI($crurl);
$cr_data = json_decode($cr_data, 1);

$taxres =  toGetTaxrates();
?>
<style type="text/css">
    input[type="text"]::placeholder {
                text-align: center;
            }
    .amount {
        text-align: right;
    }
    .thheader {
        text-align: center;
    }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" integrity="sha384-E7gp+UYBLS2XewcxoJbfi0UpGMHSvt9XyI9bH4YIw5GDGW8AlC+2J7bVBBlMFC6p" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha384-pPttEvTHTuUJ9L2kCoMnNqCRcaMPMVMsWVO+RLaaaYDmfSP5//dP6eKRusbPcqhZ" crossorigin="anonymous"></script>
<script>
     function GetSelectedTextValue(key) {
        if(key == '0'){
            document.getElementById('party_name').value="";
            document.getElementById('party_email').value="";
            document.getElementById('party_phone').value="";
            document.getElementById('party_tax_number').value="";
            document.getElementById('party_address').value="";
            document.getElementById('party_ship_adderss').value="";

            document.getElementById('party_payment_terms').value="0";
        }else{
        $("#loaderIcon").show();
        $.ajax({
        url: "ajax.php",
        data:'page=vendor&ven_id='+key,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            document.getElementById('party_name').value=obj.display_name;
            document.getElementById('party_email').value=obj.email;
            document.getElementById('party_phone').value=obj.phone;
            document.getElementById('party_tax_number').value=obj.gstin;
            document.getElementById('currency_id').value=obj.currency_id;
            document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById('party_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
            document.getElementById("party_payment_terms").value = obj.payment_terms;
        },
        error:function (){}
        });
        }
    }
    function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input aria-label="Item Name[]" type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'">');

        }
        $.ajax({
        url: "ajax.php",
        data:'page=ItemDetails&item_id='+item_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            document.getElementById('productName_'+id).value=obj.itme_description;
            document.getElementById('uom_'+id).value=obj.purchase_uom;
            document.getElementById('price_'+id).value=obj.purchase_price;
            document.getElementById('item_name_'+id).value=obj.item_name;
        },
        error:function (){}
        });
    }
    function invoicePrefixchange(val){
        alert(val);
    }
    function changeinvoicenumber(value){
        var ord = document.getElementById('ord_no').value;
        document.getElementById('inv_no').value=value+'/'+ord;
    }
   $(document).ready(function(){
    $(document).on('click', '#checkAll', function() {
        $(".itemRow").prop("checked", this.checked);
    });
    $(document).on('click', '.itemRow', function() {
        if ($('.itemRow:checked').length == $('.itemRow').length) {
            $('#checkAll').prop('checked', true);
        } else {
            $('#checkAll').prop('checked', false);
        }
    });
    var count = $(".itemRow").length;
    $(document).on('click', '#addRows', function() {
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><label class="kt-checkbox"> <input aria-label="Checkbox"  class="itemRow" type="checkbox">  <span> </span> </label> </td>';
        htmlRows += '<td id="other_'+count+'"><select aria-label="Product Code[]" class="form-control"  name= "productCode[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')" > <option value="0"> Select Any Prodcut </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[item_name]; ?> </option> <?php
} ?><option  value = "-1"> Other</option> </select> </td>';
        htmlRows += '<td><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control"></textarea></td> <input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control">';
        htmlRows += '<td><select aria-label="Uom[]" class="form-control"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2[uom] == $uom[genc_detail_code]){ ?>selected <?php
} ?>  value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php
} ?></select></td> ';
        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" class="form-control quantity amount"  autocomplete="off"></td>';
        htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '<td><select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php
} ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php
} ?>  </select> </td>';

        htmlRows += '<td><input aria-label="Ptax Amount[]" type="text" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '<td><input aria-label="Amount[]" type="text" readonly name="amount[]" id="amount__2"+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    });
    $(document).on('click', '#removeRows', function(){

        $(".itemRow:checked").each(function() {
            console.log(this);
            $(this).closest('tr').remove();
        });
        $('#checkAll').prop('checked', false);
        calculateTotal();
    });

    $(document).on('blur', "[id^=quantity_]", function(){
        calculateTotal();

    });
    $(document).on('blur', "[id^=price_]", function(){
        calculateTotal();
    });
    $(document).on('change', "[id^=tax_]", function(){
        calculateTotal();
    });

    $(document).on('blur', "#disValue", function(){
            discounts();
            amountpaid();
    });
    $(document).on('change', "#disType", function(){
            discounts();
            amountpaid();
    });
    $(document).on('blur', "#amountPaid", function(){
        amountpaid();
    });
    $(document).on('click', '.deleteInvoice', function(){
        var id = $(this).attr("id");
        if(confirm("Are you sure you want to remove this?")){
            $.ajax({
                url:"action.php",
                method:"POST",
                dataType: "json",
                data:{id:id, action:'delete_invoice'},
                success:function(response) {
                    if(response.status == 1) {
                        $('#'+id).closest("tr").remove();
                    }
                }
            });
        } else {
            return false;
        }
    });
});
function calculateTotal(){
    var totalAmount = 0;
    var totaltaxrate = 0;
    var subamount = 0;
    $("[id^='price_']").each(function() {
        var id = $(this).attr('id');
        id = id.replace("price_",'');
        var price = $('#price_'+id).val();
        var quantity  = $('#quantity_'+id).val();
        var tax = $('#tax_'+id).val();
        if(!quantity) {
            quantity = 1;
        }
        var amount = price*quantity;
        $('#amount_'+id).val(parseFloat(amount));
        var ptaxRate = 0;
        if(tax != '0'){
            ptaxRate = (tax/100)*amount
        }
        $('#ptax_amount_'+id).val(parseFloat(ptaxRate));

        var total = ptaxRate+amount;
        subamount += amount ;
        totalAmount += total;
        totaltaxrate += ptaxRate;
    });
    $('#subTotal').val(parseFloat(subamount));
    var taxRate = $("#taxRate").val();
    var subTotal = $('#subTotal').val();
    if(subTotal) {
        var  taxAmount = totaltaxrate;
        $('#taxAmount').val(taxAmount);
        var grandtotal = totalAmount;
        $('#gdam').val(grandtotal);
        $('#grandtotal').val(grandtotal);
        var disType = $('#disType').val();
        var disValue = $('#disValue').val();
        if(disType && disValue) {
            discounts();
        }
    }
 }
 function discounts(){
    var disType = $('#disType').val();
    var disValue = $('#disValue').val();
    console.log(disType);
    console.log(disValue);
    var totalAftertax = $('#gdam').val();
    if(disType && disValue && totalAftertax) {
            var disValue1 = 0;
            if(disType=='P'){
                disValue1 = (disValue/100)*totalAftertax;
            }else if(disType=='A'){
                disValue1 = disValue;
            }
            totalAftertax = totalAftertax - disValue1;
            $('#discount').val(disValue1);
            $('#grandtotal').val(totalAftertax);
    }
 }
 function amountpaid(){
    var amountPaid = $('#amountPaid').val();
        var totalAftertax = $('#grandtotal').val();
        if(amountPaid && totalAftertax) {
            totalAftertax = totalAftertax-amountPaid;
            $('#amountDue').val(totalAftertax);
        } else {
            $('#amountDue').val(totalAftertax);
        }
 }
 function quantityCheck(fid){
    var id = fid.split('_');
    id = id[1];
    var quantity  = $('#quantity_'+id).val();
        var product = $('#productCode_'+id).val();
        if (product != 0 && quantity !=''){
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "ajax.php",
                data:'page=invoice&item_id='+product,
                type: "POST",
                success:function(data){
                    console.log(data);
                    console.log(quantity);
                    if (parseFloat(data) < quantity){
                        swal.fire({
                            title: "Quantity on hand "+parseFloat(data),
                            text: "Do you want to change the quantity to "+parseFloat(data)+" ?",
                            type: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, change!",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, proceed",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#quantity_'+id).val(parseFloat(data));

                                calculateTotal();
                            }
                        });
                    }
                },
                error:function (){}
            });
        }
 }
</script>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
<div class="row">
<div class="col-lg-12">
<form class="kt-form kt-form--label-right"  action="" method="post">
<div class="kt-portlet">

<!--begin::Invoice Head -->
 <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            Vendor Quotation
        </h3>
    </div>
    <div class="kt-portlet__head-toolbar">
     <?php if($edit_id != ""){?>
        <a target="_blank" href="index.php?option=COM_QO_PRINT&rid=<?php echo $edit_id; ?>" class="btn btn-clean kt-margin-r-10">
                <i class="flaticon2-print"></i>

         </a>
        <div class="btn-group">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Genrate
            </button>
            <div class="dropdown-menu">

                <a class="dropdown-item" href="">Purchase order</a>

            </div>
        </div>
    <?php } ?>
        <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
            <i class="la la-arrow-left"></i>
            <span class="kt-hidden-mobile">Back</span>
        </a>
    </div>
</div>
<!--END::Invoice Head -->

<!--begin::Page frist part-->
<div class="kt-portlet__body">
<div class="form-group row">

<div class="form-group col-lg-4">
    <span class="">Vendor</span>
    <select aria-label="Party" class="form-control" id="kt_select2_11" name= "party_id" onchange="GetSelectedTextValue(this.value)" >
            <option value="0"> Select Any Vendor </option>
            <?php foreach ($cusresponse as $key => $cus) {  ?>
            <option  <?php if($table1['party_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="form-group col-lg-4">
    <span>Vendor Name</span>
    <input aria-label="Party Name" readonly class="form-control"  type='text' id="party_name" name="party_name"    value="<?php echo $table1['party_name']; ?>" />
</div>

<div class="form-group col-lg-2">
    <span>Tax Number</span>
    <div class="input-group">
        <input aria-label="Party Tax Number" type='text' id="party_tax_number" name="party_tax_number" class="form-control" readonly value="<?php echo $table1['party_tax_number']; ?>" />
    </div>
</div>

<div class="form-group col-lg-2">
    <span class="">Phone</span>
    <div class="kt-input-icon kt-input-icon--right">
        <input aria-label="Party Phone" type='text' id="party_phone" name="party_phone" class="form-control" readonly value="<?php echo $table1['party_phone']; ?>" />
        <input type='hidden'  name="company_id" class="form-control" readonly value="<?php echo $_SESSION['company_id']; ?>" />
    </div>
</div>

<div class="form-group col-lg-4">
    <span class=""> Mail To</span>
    <input aria-label="Party Email" type='text' class="form-control" id="party_email" readonly name="party_email"  value="<?php echo $table1['party_email']; ?>"/>
</div>

<div class="form-group col-lg-4">
    <span>Billing Address</span>
    <div class="kt-input-icon kt-input-icon--right">
        <textarea aria-label="Enter your address" type="text" name="party_address" id="party_address" class="form-control" placeholder="Enter your address"><?php echo $table1['party_address'] ?></textarea>

    </div>
</div>

<div class="form-group col-lg-4">
    <span>Shipping Address</span>
    <div class="kt-input-icon kt-input-icon--right">
        <textarea aria-label="Enter your address" type="text" class="form-control" id="party_ship_adderss" name='party_ship_adderss' placeholder="Enter your address"><?php echo $table1['party_address'] ?></textarea>

    </div>
</div>

<div class="form-group col-lg-4">
    <div style="float: left;width: 50%;padding: 3px;">
        <span class="">Payment Terms</span>
        <div class="kt-input-icon kt-input-icon--right">
             <select aria-label="Payment Terms" class="form-control"  name="payment_terms" id="party_payment_terms">
                <option value="0"> Select Any Term </option>
            <?php foreach ($posresponsept as $key => $pos) {  ?>
                <option   <?php if($table1['payment_terms'] == $pos['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $pos['genc_detail_code']; ?>" > <?php echo $pos['genc_detail_name']; ?> </option>
            <?php } ?>
            </select>
        </div>
    </div>

    <div style="float: left;width: 50% ;padding: 3px;">
        <span>Tax Rate:</span>
        <div class="input-group">
            <select aria-label="Tax" class="form-control" id="kt_select2_11_2" name= "tax"  >
            <option> Select Any Tax Rate </option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  value ="<?php echo $key; ?>"> <?php echo $tax; ?> </option>
            <?php } ?>
        </select>

        </div>
    </div>
</div>

    <div style="float: left;width: 33%;padding: 3px;">
        <span class="">Place Of Supply</span>
        <div class="kt-input-icon kt-input-icon--right" >
            <select aria-label="Place Of Supply" class="form-control" id="kt_select2_11_3" name="place_of_supply" id="party_place_of_supply">
            <option value="0"> Select Any Place </option>
            <?php foreach ($posresponse as $key => $pos) {  ?>
                <option   <?php if($table1['place_of_supply'] == $pos['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $pos['genc_detail_code']; ?>" > <?php echo $pos['genc_detail_name']; ?> </option>
            <?php } ?>
            </select>
        </div>
    </div>

<div class="form-group col-lg-2">
    <span>Quotation Number</span>
    <div class="input-group">
        <input class="form-control" type='hidden' id="ord_no" value='<?php echo $invoicenumber; ?>' name="invoice_serial_number"/>
    <input aria-label="Inv No"  class="form-control" type='text' id="inv_no" value='<?php echo $table1['qo_number']; ?>'   name="invoice_number" id="invoicenumber" />

    </div>
</div>

<div class="form-group col-lg-2">
    <span class="">Quotation Date</span>
    <div class="kt-input-icon kt-input-icon--right">
        <?php $from = $table1['qo_date']!='' ? $table1['qo_date'] : $from; ?>
        <input aria-label="Input field"  class="form-control" type='text' onchange="invoicePrefixchange(this.value)" value='<?php  echo $from; ?>' name="invoiced_at" id="kt_datepicker_4_3"  />
    </div>
</div>

<div  class="form-group col-lg-3">
    <span class="">Currency</span>
    <div class="kt-input-icon kt-input-icon--right" >
        <select aria-label="Currency" class="form-control"  name="currency_id" id="currency_id">
        <option value="0"> Select Any Currency </option>
        <?php foreach ($cr_data as  $crpos) {  ?>
            <option  <?php if($table1['currency_id'] == $crpos['id']){ ?> selected  <?php
} ?>   value ="<?php echo $crpos['id']; ?>" > <?php echo $crpos['currency_list']; ?> </option>
        <?php } ?>
        </select>
    </div>
</div>

<div class="form-group col-lg-1">
    <span class="">Ex Rate</span>
    <input aria-label="Currency Rate" type='text' class="form-control" id="currency_rate"  name="currency_rate"  value="<?php echo customeummarmonyformate($table1['currency_rate']); ?>"/>
</div>

</div></div>
<!--End::Page frist part-->

<!--begin::Page second part -->

<div class="kt-portlet__head">
        <div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title"> Items/Services </h3></div>
</div>
<div class="kt-portlet__body">
<div class="form-group row">
<div class="col-lg-12">
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="invoiceItem">
<tr>
    <th scope="col" class="thheader" style="width: 2%;">
            <label class="kt-checkbox">
                <input aria-label="Check All" id="checkAll"  type="checkbox">
                <span></span>
            </label>
    </th>

    <th scope="col" class="" style="width: 16%;">Items/Service</th>
    <th scope="col" class="" style="width: 20%;">Description</th>
    <th scope="col" class="thheader" style="width: 8%;">UOM</th>
    <th scope="col" class="thheader" style="width: 6%;">Quantity</th>
    <th scope="col" class="thheader" style="width: 8%;">Rate</th>
    <th scope="col" class="thheader" style="width: 8%;">Tax</th>
    <th scope="col" class="thheader" style="width: 8%;">Tax Amount</th>
    <th scope="col" class="thheader" style="width: 10%;">Amount</th>

</tr>
<?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) { //print_r($tab2); ?>
<tr>
    <td>
        <label class="kt-checkbox"><input aria-label="Checkbox" class="form-control itemRow" type="checkbox"> <span></span> </label>
    </td>
    <td id='other_<?php echo $i; ?>'>
        <select aria-label="Product Code[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> Select Any Item  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id']== $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?>
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
    </td>
    <td><input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['uom']; ?>">
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

    </td>
    <td>
        <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['quantity']; ?>" autocomplete="off">
    </td>
    <td>
        <input aria-label="Price[]" type="text" name="price[]" id="price_1" class="form-control price amount" value="<?php echo customeummarmonyformate($tab2['item_price']); ?>" autocomplete="off">
    </td>
    <td>
        <select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_<?php echo $i; ?>"  >
            <option value="0"> Select Any Tax Rate </option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
            <?php } ?>
        </select>

    </td>
    <td>
        <input aria-label="Ptax Amount[]" type="text" name="ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
    </td>
    <td>
        <input aria-label="Amount[]" type="text" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
    </td>
</tr>

<?php $i++; ?>
<?php } ?>
<?php } ?>
    </table>
</div>
</div>
<!--END::Page second part -->

    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
            <button class="btn btn-success" id="addRows" type="button">+ Add More</button>
        </div>
    </div>
    <div style="float: left;width: 50%; padding: 15px;">
        <span class="">Note(s)</span>
        <textarea aria-label="Notes" style="height: 125px;" name="notes" class="form-control" ><?php echo $table1['notes']; ?></textarea>
    </div>
    <div style="float: left;width: 50%">
    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
        <tr>
            <th scope="col" class="amount"><span>Sub Total</span></th>
            <td><span><input aria-label="Input field"  type="text" class="form-control amount" readonly value="<?php echo customeummarmonyformate($table1['sub_total']); ?>" name="subTotal" id="subTotal" placeholder="Subtotal" ></span></td>
        </tr>

        <tr>
            <th scope="col" class="amount"><span >Tax Amount </span></th>
            <td><span >
                <input aria-label="Input field"  value="<?php echo customeummarmonyformate($table1['tax_amount']); ?>" type="text" class="form-control amount" name="taxRate" id="taxAmount" readonly placeholder="Tax Amount">
                <input  type="hidden" class="form-control amount" placeholder="value" name="gdam" id="gdam">
            </span></td>
        </tr>
        <tr>
            <th scope="col" class="amount">
                <div>
                    <div style="float: left;width: 33%;padding: 10px;">
                        <span >Discount </span>
                    </div>
                    <div style="float: left;width: 33%;padding: 3px;">
                        <select aria-label="Discount Type" class="form-control" id="disType" name="discount_type">
                            <option value="P">Percentage</option>
                            <option <?php if($table1['discount_type']){ ?> selected <?php
} ?> value="A">Amount</option>
                        </select>
                    </div>
                    <div style="float: left;width: 33%;padding:3px;">
                        <input aria-label="Input field" value="<?php echo $table1['discount_value']; ?>" type="text" class="form-control amount" placeholder="value" name="discount_value" id="disValue">

                    </div>
                </div>
            </th>
            <td><span >
                <input aria-label="Input field" value="<?php echo $table1['discount_amount']; ?>" type="text" class="form-control amount" name="discount_amount" id="discount" placeholder="Discount Amount">
            </span></td>
        </tr>
        <tr>
            <th scope="col" class="amount" ><span>Grand Total</span></th>
            <td><input aria-label="Input field"  value="<?php echo customeummarmonyformate($table1['grand_total']); ?>" type="text" class="form-control amount" name="grandtotal" id="grandtotal" readonly placeholder="Grand Total"></td>
        </tr>
    </table>
</div>
</div>
</div>
</div>

<!--begin::Submit part -->
<div class="kt-portlet__foot">
    <div class="kt-form__actions">
        <div class="row">
            <div class="col-lg-6">
                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
                        <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                        <input type="hidden" name="submit" value="Update" />
                        <button id="kt_llogin_signup_submit" class="btn btn-brand" ><i class="la la-check"></i>Upadate</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } else{ ?>
                        <input type="hidden" name="submit" value="Add" />
                        <button id="kt_login_signin_submit" class="btn btn-brand"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit_continue" class="btn btn-brand"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } ?>
            </div>
            <div class="col-lg-6 kt-align-right">
                <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
            </div>
        </div>
    </div>
</div>
<!--END::Submit part -->
 </div> </div></div>
 </form>
</div></div></div></div>
