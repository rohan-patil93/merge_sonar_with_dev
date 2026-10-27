<style type="text/css">
    table th, table td
    {
        width: 100px;
        padding: 5px;
        border: 1px solid #ccc;
    }
    .selected
    {
        background-color: #666;
        color: #fff;
    }
</style>
<?php
if($_POST['submit'] == 'Add'){

        $url = API."?mode=addbill&data=".json_encode($_POST,1);
        echo $url; exit;

    }
    if($_POST['submit'] == 'Update'){

    }
$cusurl = API."?mode=gettabledetails&table=".TPREFIX.TBL_CUSTOMERS;
$cusresponse = file_get_contents($cusurl, false, $context);
$cusresponse = json_decode($cusresponse,1);
$cus_id =   encodeId($_GET['Rid']);
if($cus_id!=''){
    $url = API."?mode=singlerowtabledata&table=".TPREFIX.TBL_CUSTOMERS."&fid=id&rid=".$cus_id;
    $response = file_get_contents($url, false, $context);
    $cusdata= json_decode($response,1);
}
$invurl = API."?mode=invoicenumber&company_id=1";
$invoicenumber = file_get_contents($invurl, false, $context);
$inoicePrefix= invoicePrefix();
$from =  fromDate();
$taxurl = API."?mode=gettabledetails&fid=company_id&rid=".$_SESSION['company_id']."&table=".TPREFIX.TBL_TAXES;
$taxres = file_get_contents($taxurl, false, $context);
$taxres = json_decode($taxres,1);
$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=CIPerFix&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponse = file_get_contents($typeurl, false, $context);
$perfixresponse = json_decode($typeresponse,1);
$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PLACEOFSUPPLY&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponse = file_get_contents($typeurl, false, $context);
$posresponse = json_decode($typeresponse,1);
$itemurl = API."?mode=gettabledetails&table=".TPREFIX.TBL_ITEMS;
$itemres = file_get_contents($itemurl, false, $context);
$itemres = json_decode($itemres,1);
?>
<style type="text/css">
    input[type="text"]::placeholder {

                /* Firefox, Chrome, Opera */
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
        $("#loaderIcon").show();
        $.ajax({
        url: "ajax.php",
        data:'page=bill_customer&cus_id='+key,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            document.getElementById('party_name').value=obj.display_name;
            document.getElementById('party_email').value=obj.email;
            document.getElementById('party_phone').value=obj.phone;
            document.getElementById('party_tax_number').value=obj.gstin;
            document.getElementById('party_payment_terms').value=obj.payment_terms;
            document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById('party_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
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
        htmlRows += '<td><input aria-label="Input field" class="form-control" type="text" value="'+count+'" > </td>';
        htmlRows += '<td><select aria-label="Product Code[]" class="form-control"  name= "productCode[]" id="productCode_'+count+'" onchange="quantityCheck(this.id)"> <option value="0"> Select Any Prodcut </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[name]; ?> </option> <?php
} ?><option  value = "+add"> <b> + ADD </b></option> </select> </td>';
        htmlRows += '<td><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control"></textarea></td>';
        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" class="form-control quantity amount" onblur="quantityCheck(this.id)" autocomplete="off"></td>';
        htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '<td><select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_'+count+'"> <option> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $tax[id]){ ?> selected  <?php
} ?> value = <?php echo $tax[rate] ?>><?php echo $tax[name]; ?> </option> <?php
} ?>  </select> </td>';
        htmlRows += '<td><input aria-label="Amount[]" type="text" name="amount[]" id="amount__2"+count+'" class="form-control price amount" autocomplete="off"></td>';
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
        var ptaxRate = (tax/100)*amount
        var total = ptaxRate+amount;
        $('#total_'+id).val(parseFloat(total));
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
        amountpaid();
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
                <!--begin::Portlet-->
                <div class="kt-portlet">
                                        <div class="kt-portlet__head">
                                            <div class="kt-portlet__head-label">
                                                <h3 class="kt-portlet__head-title">
                                                    Customer Invoice
                                                </h3>
                                            </div>
                                            <div class="kt-portlet__head-toolbar">
                                            <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                                    <i class="flaticon2-print"></i>

                                             </a>
                                            <div class="btn-group">
                                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Genrate
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#"> Dedit Note </a>
                                                    <a class="dropdown-item" href="#">Creadit Note</a>
                                                </div>
                                            </div>
                                                <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                                    <i class="la la-arrow-left"></i>
                                                    <span class="kt-hidden-mobile">Back</span>
                                                </a>
                                            </div>
                                        </div>


                                        <form class="kt-form kt-form--label-right">
                                            <div class="kt-portlet__body">
                                                <div class="form-group row">
                                                    <div class="col-lg-4">
                                                        <span class="">Customer:</span>
                                                        <select aria-label="Party" class="form-control" id="kt_select2_11" name= "party_id" onchange="GetSelectedTextValue(this.value)" >
                                                                <option> Select Any Customer </option>
                                                                <?php foreach ($cusresponse as $key => $cus) {  ?>
                                                                <option  <?php if($cus_id == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span>Customer Name:</span>
                                                        <input aria-label="Party Name" readonly class="form-control"  type='text' id="party_name" name="party_name"    value="<?php echo $cusdata['name']; ?>" />

                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div style="float: left;width: 50% ;padding: 3px;">
                                                            <span>Tax Number:</span>
                                                            <div class="input-group">
                                                                <input aria-label="Party Tax Number" type='text' id="party_tax_number" name="party_tax_number" class="form-control" readonly value="<?php echo $cusdata['tax_number']; ?>" />

                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 50%;padding: 3px;">
                                                            <span class="">Phone:</span>
                                                            <div class="kt-input-icon kt-input-icon--right">
                                                                <input aria-label="Party Phone" type='text' id="party_phone" name="party_phone" class="form-control" readonly value="<?php echo $cusdata['phone']; ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-lg-4">
                                                        <span class="">Invoice Mail To:</span>
                                                        <input aria-label="Party Email" type='text' class="form-control" id="party_email" readonly name="party_email"  value="<?php echo $cusdata['email']; ?>"/>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <span>Billed Address:</span>
                                                        <div class="kt-input-icon kt-input-icon--right">
                                                            <textarea aria-label="Enter your address" type="text" name="party_address" id="party_address" class="form-control" placeholder="Enter your address"></textarea>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span>Ship To Address:</span>
                                                        <div class="kt-input-icon kt-input-icon--right">
                                                            <textarea aria-label="Enter your address" type="text" class="form-control" id="party_ship_adderss" name='party_ship_adderss' placeholder="Enter your address"></textarea>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-lg-4">
                                                        <div style="float: left;width: 33%;padding: 3px;">
                                                            <span class="">Payment Terms:</span>
                                                            <div class="kt-input-icon kt-input-icon--right">
                                                                <select aria-label="Payment Terms" class="form-control" id="kt_select2_11_2" name="payment_terms" id="party_payment_terms">
                                                                <option> Select Any Place </option>
                                                                <?php foreach ($posresponse as $key => $pos) {  ?>
                                                                    <option   value = <?php echo $pos['code'] ?>   > <?php echo $pos['id']; ?> </option>
                                                                <?php } ?>
                                                                </select>


                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 33% ;padding: 3px;">
                                                            <span>Tax Rate:</span>
                                                            <div class="input-group">
                                                                <select aria-label="Tax" class="form-control" id="kt_select2_11_3" name= "tax"  >
                                                                <option> Select Any Tax Rate </option>
                                                                <?php foreach ($taxres as $key => $tax) {  ?>
                                                                <option  <?php if($_SESSION['configdata']['default_tax_rate'] == $tax['id']){ ?> selected  <?php
} ?> value = <?php echo $tax['rate'] ?>   > <?php echo $tax['name']; ?> </option>
                                                                <?php } ?>
                                                            </select>

                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 33%;padding: 3px;">
                                                            <span class="">Place Of Supply</span>
                                                            <div class="kt-input-icon kt-input-icon--right" >
                                                                <select aria-label="Place Of Supply" class="form-control" id="kt_select2_11_4" name="place_of_supply">
                                                                <option> Select Any Place </option>
                                                                <?php foreach ($posresponse as $key => $pos) {  ?>
                                                                    <option   value = <?php echo $pos['code'] ?>   > <?php echo $pos['description']; ?> </option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div style="float: left;width: 33%;padding: 3px;">
                                                            <span class="">Invoice Prefix:</span>
                                                            <div class="kt-input-icon kt-input-icon--right">
                                                                <input type="hidden" id='invoicePre' value="<?php echo $_SESSION['configdata']['invoice_perfix']; ?>">
                                                                <input aria-label="Custom text" class="form-control" type='text' onBlur="changeinvoicenumber(this.value)" placeholder="Custom text" value='<?php echo $inoicePrefix; ?>' name="invoice_pre" <?php if($inoicePrefix!=''){ ?>  disabled="disabled" <?php
} ?>  />
                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 33% ;padding: 3px;">
                                                            <span>Invoice Number:</span>
                                                            <div class="input-group">
                                                                <input class="form-control" type='hidden' id="ord_no" value='<?php echo $invoicenumber; ?>' name="invoice_serial_number"/>
                                                            <input aria-label="Inv No"  class="form-control" type='text' id="inv_no" value='<?php  echo $inoicePrefix."/".$invoicenumber; ?>' readonly  name="invoice_number" id="invoicenumber" />

                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 33%;padding: 3px;">
                                                            <span class="">Invoice Date:</span>
                                                            <div class="kt-input-icon kt-input-icon--right">
                                                                <input aria-label="Input field"  class="form-control" type='text' onchange="invoicePrefixchange(this.value)" value='<?php  echo $from; ?>' name="invoiced_at" id="kt_datepicker_4_3"  />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div style="float: left;width: 50% ;padding: 3px;">
                                                            <span>Order Number:</span>
                                                            <div class="input-group">
                                                                <input aria-label="Order Number"  class="form-control" type='text' id="" value=''  name="order_number" />

                                                            </div>
                                                        </div>
                                                        <div style="float: left;width: 50%;padding: 3px;">
                                                            <span class="">Order Date:</span>
                                                            <div class="kt-input-icon kt-input-icon--right">
                                                                <input aria-label="Input field"  class="form-control" type='text' value='<?php  echo $from; ?>' name="order_at" id="kt_datepicker_4_3_1" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>


                                    </div>
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                 Item(s)
                            </h3>
                        </div>
                    </div>


                        <div class="kt-portlet__body">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="invoiceItem">
                                        <tr>
                                            <th scope="col" class="" style="width: 2%;">
                                                    <label class="kt-checkbox">
                                                        <input aria-label="Check All" id="checkAll"  type="checkbox">
                                                        <span></span>
                                                    </label>
                                            </th>
                                            <th scope="col" class="thheader" style="width: 5%;">#</th>
                                            <th scope="col" class="thheader" style="width: 12%;">Product/Service</th>
                                            <th scope="col" class="thheader" style="width: 24%;">Description</th>
                                            <th scope="col" class="thheader" style="width: 10%;">Quantity</th>
                                            <th scope="col" class="thheader" style="width: 10%;">Rate</th>
                                            <th scope="col" class="thheader" style="width: 10%;">Tax</th>
                                            <th scope="col" class="thheader" style="width: 10%;">Amount</th>

                                        </tr>
                                        <tr>
                                            <td><label class="kt-checkbox"><input aria-label="Checkbox" class="form-control itemRow" type="checkbox"> <span></span> </label></td>
                                            <td><input aria-label="Input field" class="form-control" type="text" value="1" > </td>
                                            <td>
                                                <select aria-label="Product Code[]" class="form-control"  name= "productCode[]" id="productCode_1" onchange="quantityCheck(this.id)" >
                                                    <option value="0"> Select Any Prodcut  </option>
                                                    <?php foreach ($itemres as $item) {  ?>
                                                    <option  value = <?php echo $item['id'] ?>   > <?php echo $item['name']; ?> </option>
                                                    <?php } ?>
                                                    <option  value = "+add"> <b> + ADD </b></option>
                                                </select>

                                            </td>
                                            <td><textarea aria-label="Product Name[]"  name="productName[]" id="productName_1" class="form-control"></textarea></td>
                                            <td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_1" class="form-control amount" onblur="quantityCheck(this.id)" autocomplete="off"></td>
                                            <td><input aria-label="Price[]" type="text" name="price[]" id="price_1" class="form-control price amount" autocomplete="off"></td>

                                            <td>
                                                <select aria-label="Tax[]" class="form-control"  name= "tax[]" id="tax_1"  >
                                                    <option> Select Any Tax Rate </option>
                                                    <?php foreach ($taxres as $key => $tax) {  ?>
                                                    <option  <?php if($_SESSION['configdata']['default_tax_rate'] == $tax['id']){ ?> selected  <?php
} ?> value = <?php echo $tax['rate'] ?>   > <?php echo $tax['name']; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input aria-label="Amount[]" type="text" name="amount[]" id="amount_1_2" class="form-control amount" autocomplete="off"></td>

                                        </tr>
                                    </table>
                                </div>
                            </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                            <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
                                            <button class="btn btn-success" id="addRows" type="button">+ Add More</button>
                                        </div>
                                    </div>
                                    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer balance">
                                        <tr>
                                            <th scope="col" class="amount"><span>Sub Total</span></th>
                                            <td><span><input aria-label="Subtotal" value="" type="text" class="form-control amount" name="subTotal" id="subTotal" placeholder="Subtotal"></span></td>
                                        </tr>

                                        <tr>
                                            <th scope="col" class="amount"><span >Tax Amount </span></th>
                                            <td><span >
                                                <input aria-label="Tax Amount" value="" type="text" class="form-control amount" name="taxRate" id="taxAmount" placeholder="Tax Amount">
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
                                                            <option value="A">Amount</option>
                                                        </select>
                                                    </div>
                                                    <div style="float: left;width: 33%;padding:3px;">
                                                        <input aria-label="value" value="" type="text" class="form-control amount" placeholder="value" name="discount_value" id="disValue">

                                                    </div>
                                                </div>
                                            </th>
                                            <td><span >
                                                <input aria-label="Discount Amount" value="" type="text" class="form-control amount" name="discount_amount" id="discount" placeholder="Discount Amount">
                                            </span></td>
                                        </tr>
                                        <tr>
                                            <th scope="col" class="amount" ><span>Grand Total</span></th>
                                            <td><input aria-label="Grand Total" value="" type="text" class="form-control amount" name="grandtotaol" id="grandtotal" placeholder="Grand Total"></td>
                                        </tr>
                                        <tr>
                                            <th scope="col" class="amount" ><span >Amount Paid</span></th>
                                            <td><input aria-label="Amount Paid" value="" type="text" class="form-control amount" name="amountPaid" id="amountPaid" placeholder="Amount Paid"></td>
                                        </tr>
                                        <tr>
                                            <th scope="col" class="amount" ><span >Amount Due</span></th>
                                            <td><input aria-label="Amount Due" value="" type="text" class="form-control amount" name="amountDue" id="amountDue" placeholder="Amount Due"></td>
                                        </tr>

                                    </table>

                                </div>

                            </div>
                        </div>
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
                        <button id="kt_login_signin_submit_continue" class="btn btn-brand"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit_3" class="btn btn-brand"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } ?>
            </div>
            <div class="col-lg-6 kt-align-right">
                <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
            </div>
        </div>
    </div>
</div>

                </div>
                <!--end::Portlet-->
            </div>
        </div>
        </form>
    </div>
</div>
    <!-- end:: Content -->
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" integrity="sha384-lifoBlbdwizTl3Yoe612uhI3AcOam/QtWkozF7SuiACaf5UJl5reOYu4MigVxrCH" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js" integrity="sha384-q8pVyAK2InF9wi3dnZ5Wkzj906k+Rvjw1V125dWmOTXWa9vnbqqdWRqUSY5AioHg" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/smoothness/jquery-ui.css"  integrity="sha384-eX7Mg1s071G94N7MUCaAGEadC1UuTJPIvW8SM0AuD3D60GlC1lp1S7zEOPE0S2zl" crossorigin="anonymous"/>

<script type="text/javascript">
$(function () {
    $("#tblLocations").sortable({
        items: 'tr:not(tr:first-child)',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                if (index > 0) {
                    $(this).find("td").eq(2).html(index);
                }
            });
        }
    });
});
</script>
