<?php
$option = strtoupper($_GET['option']);
$list = str_replace('INSERT', 'LIST', $option);

if($_POST['submit'] == 'query'){
    $res = Insertqueries($_POST);
     $_POST['submit'] = '';
}
if($_POST['submit'] == 'reply'){
    $res = updatequeries($_POST);
}
if($_POST['submit1'] == 'Add'){
    $_POST['created_by'] = $_SESSION['userid'];
    $_POST['created_date'] = nowDate();
    $url = API."?mode=addinvoietype&table=".$_POST['table_name']."&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
     if ($response) {
        if ($_POST['submit_value'] == 'continue'){
                $LASTID = json_decode($response,1);
                $nrid = "id:".$LASTID['LASTID'];
                header("location:index.php?option=".$option."&Rid=".deocdeId($nrid));
            }else{
                header("location:index.php?option=".$list);
            }
    }
}
if($_POST['submit1'] == 'Update'){
    $_POST['updated_by'] = $_SESSION['userid'];
    $_POST['updated_date'] = nowDate();

    if($_POST['status_values'] == ST_PURCHASE_ORDER_GENRATED){
        $retun_list = toGetpageOptionName('purchase_orders');
        header("location:index.php?option=".$retun_list['option_url']."&table=".$_POST['table_name']."&Rid=".$_POST['hid']);
    }
    if($_POST['status_values'] == ST_APPROVAL_PENDING){
        $checkurl = API."?mode=tocheckDAO&table=".$_POST['table_name']."&status=".$_POST['status_values']."&amount=".$_POST['grandtotal']."&status_old_value=".$_POST['status_old_value']."&cost_center=".$_POST['cost_center'];
        $originator_tab = array('bills', 'petty_cash');
        if(!in_array($_POST['table_name'], (array)$originator_tab)){
             $checkurl.="&opex_cpex=".$_POST['opex_cpex']."&category_type=".$_POST['type'];
        }
        $check_data = callAPI($checkurl);
        $check_data = json_decode($check_data,1);
        if(!isset($check_data['id']) || $check_data['id'] == ''){
            header("location:index.php?option=".$option."&error=9001&Rid=".deocdeId('id:'.$_POST['hid'])); exit;
        }
    }
    $_POST['file_add'] = $_SESSION['file_add'][$_POST['table_name']][$_POST['hid']];
    $_SESSION['file_add'][$_POST['table_name']][$_POST['hid']]= array();
    $url = API."?mode=addinvoietype&table=".$_POST['table_name']."&method=post&type=update";
    $response = file_post_contents($url, $_POST, 'update');
    if ($response) {
        header("location:index.php?option=".$list);
    }
}
$originator_tables = array('requisitions', 'petty_cash');
$stock_table = array('stock_request', 'stock_transfer');
$originator_table_withbill = array('requisitions', 'petty_cash','bills', 'stock_request');
$table_list = toCreateInvoicePages();
$table_name = $table_list[$option]['table'];

$title =  $table_list[$option]['title'];
$stausresult = statusData($table_name);
$table2 = array(array('item_id'=>'0'));
$copy = 0;
$cusurl = API."?mode=gettabledetails&fid=company_id&rid=".$_SESSION['company_id']."&table=".TBL_USERS;
$cusresponse = callAPI($cusurl);
$cusresponse = json_decode($cusresponse,1);

foreach ($cusresponse as  $cust) {
        if($cust['id'] == $_SESSION['userid']){
            $cusdata = $cust;
            break;
        }
    }
    $res = json_decode($response,1);
    $locurl = API."?mode=singlerowtabledata&table=".TBL_LOCATIONS."&fid=id&rid=".$cusdata['location_id'];
    $locares = callAPI($locurl);
    $locares = json_decode($locares,1);
    $location_state = $locares['location_state'];
    $table1['location_id'] =  $cusdata['location_id'];
    $table1['cost_center'] =  $cusdata['cost_center'];
if(in_array($table_name, (array)$originator_table_withbill)){

    $table1['party_id'] = $_SESSION['userid'];
    $table1['party_name'] = $cusdata['first_name'].' '.$cusdata['middle_name'].' '.$cusdata['last_name'];
    $table1['party_email'] = $cusdata['user_email'];
    $table1['party_phone'] =  $cusdata['user_mobile'];

    $table1['party_tax_number'] = $locares['tax_ref'];
    $table1['party_address'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
    $table1['party_ship_adderss'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
} else{
    $cusurl = API."?mode=gettabledetails&table=".TBL_VENDOR;
    $cusresponse = callAPI($cusurl);
    $cusresponse = json_decode($cusresponse,1);
}
$taxres =  toGetTaxrates();
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $rtable_name = ""; $subt_amount = ""; $stax_amount="";
    if(isset($_GET['table']) && $_GET['table'] !=''){
        $rtable_name = $_GET['table'] ;
        $rid = $_GET['Rid'];
    } else{
        $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $rid = explode(":", $rid);
        $rid= $rid[1];
        $copy = 1;
        $vdata1["where"] = array('transaction_primary_1'=> $rid,'transaction_table'=>$table_name);
        $vdata1["field"] = "id,transaction_type,user_id,action_log,action_date,status_value";
        $vdata1['order'] = "id DESC";
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=".TBL_ACTION_LOG."&data=".$vdata;
        $vdata_details = callAPI($vurl);
        $vdata_details = json_decode($vdata_details,1);

   }
    $rtable = $rtable_name!='' ? $rtable_name : $table_name;
    $table2 = array(''); $i=0;
    $rpurl =  API."?mode=getInvoicesData&in_id=$rid&table=$rtable";

    $rp_data = callAPI($rpurl);
    $rpres = json_decode($rp_data, 1);
    $rpdata = $rpres['data'];
    $items = $rpres['items'];
    $taxes = $rpres['taxes'];
    $files = $rpres['files'];
    $taxes_ship = $taxes;
    if($rpdata['shipping_tax']!=0){
    $taxes_ship[] = array('tax_id'=> $rpdata['shipping_tax'],'tax_value'=> $rpdata['shipping_tax'],'amount'=>$rpdata['shipping_amount'],'tax_amount'=>$rpdata['shipping_tax_amount']);
    }
    $dutax = array();
    $distax = array();
    foreach ($taxes_ship as  $taxvalue) {
        $taxvalue['tax_value'] = number_format($taxvalue['tax_id'],2) ;
       if(in_array(number_format($taxvalue['tax_value'],2), (array)$dutax)){
           $distax[$taxvalue['tax_value']] ['amount'] =  $distax[$taxvalue['tax_value']] ['amount'] + $taxvalue['amount'];
           $distax[$taxvalue['tax_value']] ['tax_amount'] =  $distax[$taxvalue['tax_value']] ['tax_amount'] +$taxvalue['tax_amount'];
       }else{
            $dutax []= number_format($taxvalue['tax_value'],2);
            $distax[$taxvalue['tax_value']] ['percentage'] =  $taxvalue['tax_value'];
            $distax[$taxvalue['tax_value']] ['amount'] =  $taxvalue['amount'];
            $distax[$taxvalue['tax_value']] ['tax_amount'] =  $taxvalue['tax_amount'];
       }
    }
    if($rtable_name != ''){
       $table1['qo_number'] = "";
       $table1['qo_date'] = "";
       $table1['ref_id'] = $rid;
       $table1['ref_no'] = $rpdata['transaction_number'];
       $table1['ref_date'] = $rpdata['transaction_date'];
       $table1['ref_type'] = $rtable_name;
    }else{
       $table1['qo_number'] = $rpdata['transaction_number'];
       $table1['qo_date'] =  $rpdata['transaction_date'];
       $table1['ref_id'] = $rpdata['ref_id'] ;
       $table1['ref_no'] = $rpdata['ref_no'] ;
       $table1['ref_date'] = $rpdata['ref_date'];
       $table1['ref_type'] = $rpdata['ref_type'];
    }
    if($rtable_name != 'requisitions'){
        $table1['party_id'] = $rpdata['party_id'];
        $table1['party_name'] = $rpdata['party_name'];
        $table1['party_email'] = $rpdata['party_email'];
        $table1['party_phone'] = $rpdata['party_phone'];
        $table1['party_tax_number'] = $rpdata['party_tax_number'];
        $table1['party_address'] = $rpdata['party_address'];
        $table1['party_ship_adderss'] = $rpdata['party_ship_adderss'];
    }
    $table1['originator'] = $rpdata['originator'];
    $table1['currency_id'] = $rpdata['currency_id'];
    $table1['cost_center'] = $rpdata['cost_center_id'];
    $table1['currency_rate'] = $rpdata['currency_rate'];
    $table1['payment_terms'] = $rpdata['payment_terms'];
    $table1['place_of_supply'] = $rpdata['place_of_supply'];
    $table1['sub_total'] = $rpdata['amount'];
    $table1['tax_amount'] = $rpdata['tax_amount'];
    $table1['discount_type'] = $rpdata['discount_type'];
    $table1['discount_value'] = $rpdata['discount_value'];
    $table1['discount_amount'] = $rpdata['discount_amount'];
    $table1['grand_total'] = $rpdata['total_amount'];
    $table1['is_active'] = $rpdata['is_active'];
    $table1['current_status'] = $rpdata['current_status'];
    $table1['notes'] = $rpdata['notes'];
    $table1['printing_notes'] = $rpdata['printing_notes'];
    $table1['shipping_tax'] = $rpdata['shipping_tax'];
    $table1['shipping_amount'] = $rpdata['shipping_amount'];
    $table1['shipping_tax_amount'] = $rpdata['shipping_tax_amount'];
    $table1['taxtype'] = $rpdata['tax_type'];
    $table1['tax_state'] = $rpdata['tax_state'];
    $table1['opex_cpex'] = $rpdata['opex_cpex'];
    $table1['type'] = $rpdata['type'];
    foreach ($taxes as  $taxs) {
       $tax_array[$taxs['transaction_item_id']] = $taxs['tax_id'];
    }
    foreach($items as $item){
        $itemid[] = $item['item_id'];
        $itemda['item_id']= $item['item_id'];
        $itemda['item_name']= $item['item_name'];
        $itemda['description']= trim($item['description']);
        $itemda['uom']= $item['purchase_uom'];
        $itemda['quantity']= $item['item_quantity'];
        $itemda['item_price']= $item['item_price'];
        $itemda['tax_id']= $tax_array[$item['item_id']];
        $itemda['tax_amount']= $item['tax_amount'];
        $itemda['total_amount']= $item['total_amount'];
        $itemda['purchase_account']= $item['account_code'];
        $itemda['req_date']= $item['req_date'];
        $account_code[] = $item['account_code'];
        $itemda['invoiced_quantity'] = $item['invoiced_quantity'] !='' ?$item['invoiced_quantity'] : $item['item_quantity'];
        $itemda['damaged_quantity'] = $item['damaged_quantity'] !='' ?$item['damaged_quantity'] : 0;
        if($table_name == 'quotations' && $rtable_name !=''){
            $tax_val = $tax_array[$item['item_id']] !='' ?$tax_array[$item['item_id']] :0;
            $itemda['quantity']= $item['pending_qty'];
            $itemda['tax_amount']= ($item['item_price'] * $item['pending_qty'])*($tax_val/100);
            $itemda['total_amount']=  $item['item_price'] * $item['pending_qty'];
            $subt_amount+=$item['item_price'] * $item['pending_qty'];
            $stax_amount+=($item['item_price'] * $item['pending_qty'])*($tax_val/100);
        }
        $table2[$i] = $itemda;
        $i++;
    }
    if($subt_amount != ""){
        $table1['sub_total'] = $subt_amount;
        $table1['tax_amount'] = $stax_amount;
        if($rpdata['discount_type'] == 'P'){
            $table1['discount_amount'] = $subt_amount * ($rpdata['discount_value'] /100);
        }
        if($rpdata['discount_type'] == 'A'){
            $table1['discount_amount'] = ($rpdata['discount_amount'] / $rpdata['total_amount']) * $subt_amount;
        }
        $table1['grand_total'] = $subt_amount + $stax_amount - $table1['discount_amount']  ;
    }
    $item_ids = "";
    $item_ids = implode(',', (array)$itemid);
    $acode['account_code']=$account_code;
    $acode['cost_center_id']=$table1['cost_center'];
    $acode['company_id']=$_SESSION['company_id'];
    $acode['fiscal_year']=$table1['qo_date'];
    $acurl = API."?mode=budgetcheckreftadledata&method=post";
    $response_code = file_post_contents($acurl, $acode, 'insert');
    $response_code = json_decode($response_code,1);
}

$typeurl = API."?mode=getplaceofsuppl";
$typeresponse = callAPI($typeurl);
$posresponse = json_decode($typeresponse,1);
$data1["field"] = "id,item_name,item_description,purchase_price,purchase_uom,tracking_type";
$data = urlencode(json_encode($data1));

$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PAYMETERM&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponsept = callAPI($typeurl);
$posresponsept = json_decode($typeresponsept,1);

$uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=UOM&table=".TBL_GENRAL_CODE_DETAILS;
$uomlist = callAPI($uomurl);
$uomlist = json_decode($uomlist,1);

$itemtypeurl =  API."?mode=gettabledetails&fid=genc_code&rid=TRANSACTIONTYPE&table=".TBL_GENRAL_CODE_DETAILS;
$itemtypelist = callAPI($itemtypeurl);
$itemtypelist = json_decode($itemtypelist,1);

$box_url =  API."?mode=gettabledetails&table=petty_cash_boxes";
$boxlist = callAPI($box_url);
$boxlist = json_decode($boxlist,1);

$departmentsurl = API."?mode=gettabledetails&table=".TBL_DEPARTMENTS;
$departmentslist =  callAPI($departmentsurl);
$departmentslist = json_decode($departmentslist,1);

$crdata["field"] = "id,currency_list";
$crdata = urlencode(json_encode($crdata));
$crurl = API."?mode=gettabledetailswithconditions&table=".TBL_CURRENCIES."&data=".$crdata;
$cr_data = callAPI($crurl);
$cr_data = json_decode($cr_data, 1);
$_SESSION['application_table_name'] = $table_name;
$_SESSION['transaction_id'] = $rid;
$_SESSION['file_add'][$table_name][$rid]= array();
    $costcenterurl = API."?mode=gettabledetails&table=".TBL_COST_CENTERS;

    $cost = callAPI($costcenterurl);
    $cost = json_decode($cost,1);
    foreach ($cost as $valu) {
         if($valu['parent_cost_center_id'] == 0){
            $array[$valu['id']] = $valu['cost_center_name'];
        }
    }
    $coos_user_id = $_GET['Rid']!='' ? $table1['originator'] : $_SESSION['userid'];
    $data2["field"] = "cost_center_id";
    $data2["where"] = array('user_id'=> $coos_user_id,'is_active'=> '0');
    $costdata = urlencode(json_encode($data2));
    $costurl = API."?mode=gettabledetailswithconditions&table=".TBL_USERS_COST_CENTERS."&data=".$costdata;
    $costdata = callAPI($costurl);
    $costress = json_decode($costdata, 1);
    foreach ($costress as  $costres) {
        $incost[] = $costres['cost_center_id'];
    }
    foreach ($cost as $value) {
       if($value['parent_cost_center_id'] == 0){
            $costs[$value['cost_center_name']][$value['id']]= $value['cost_center_name'];
       }else{
            $name = $array[$value['parent_cost_center_id']];
            if(in_array($value['id'],(array)$incost)){
                $costs[$name][$value['id']]= $value['cost_center_name'];
            }
       }
    }
$petty_cash_style = "";
if($table_name == 'petty_cash' || $table_name == 'bills') {
    $petty_cash_style = "style='display:none;'";
}
if(in_array($table_name, (array)$stock_table)){
    $locations_url = API."?mode=gettabledetails&table=".TBL_LOCATIONS;
    $locations_data = callAPI($locations_url);
    $locations_data = json_decode($locations_data, 1);
}

$small_sub = 111;

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
function duedate(){
    var payment_terms = $('#party_payment_terms').val();
    var tt = $('#kt_datepicker_4_3').val();
    var date = new Date(tt);
    var newdate = new Date(date);
    newdate.setDate(newdate.getDate() + parseInt(payment_terms));
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var y = newdate.getFullYear();
    var due = mm + '/' + dd + '/' + y;
    $('#due_date').val(due);

}
function removefile(val){
    $('#removefile_'+val).css('display','none');
    str = 'file_id='+val;
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=remove_file&'+str,
        success: function(response) {
            console.log(response);
        }
    });
}
 function currencychange(val,old_val){
    $.ajax({
        url: "ajax.php",
        data:'page=currency_convert&old_val='+old_val+'&val='+val,
        type: "POST",
        success:function(data){
            console.log(data);
             $('#currency_rate').val(data);
             if(data !=''){
             document.getElementById('exchange').style.display = "block";
             exchangerate();
            }else{
                swal.fire({
                            title: "6010 : Please Define Exchange Rate",
                            type: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                            confirmButtonClass: "btn btn-danger"
                        })
            }

        },
        error:function (){}
    });
 }
    function toredriacte(id,url_option,table){
        window.location.href = 'index.php?option='+url_option+'&Rid='+id+'&table='+table;
    }
    function validateForm(){
        var res = 'N';
        var x = document.getElementById("status_values").value;
        var table_name = document.forms["myForm"]['table_name'].value;
        var table_id = document.forms["myForm"]['hid'].value;
        var tranid = document.forms["myForm"]['invoice_number'].value;
        if(x == 20 ||  x == 30 || 100 < x < 110 || x == 199 ){
            var comments = document.forms["myForm"]['comments'].value;

            if(comments == ''){
                document.forms["myForm"]['comments'].focus();
                return false;
            }


        }else{
         if( x != 2 ){

            if( table_name == 'requisitions'){
                var cost_center = document.forms["myForm"]['cost_center'].value;
                if(cost_center == 0){
                     res = 'Y';
                    document.getElementById('cost_center_err').style.display = "block";
                }else{
                     document.getElementById('cost_center_err').style.display = "none";
                }
            }
            var party_id = document.forms["myForm"]['party_id'].value;
            if(party_id == 0){
                 res = 'Y';
                document.getElementById('party_id_err').style.display = "block";
            }else{
                 document.getElementById('party_id_err').style.display = "none";
            }
            var party_place_of_supply = document.forms["myForm"]['party_place_of_supply'].value;
            if(party_place_of_supply == 0){
                 res = 'Y';
                document.getElementById('party_place_of_supply_err').style.display = "block";
            }else{
                 document.getElementById('party_place_of_supply_err').style.display = "none";
            }
            var count = $(".itemRow").length;
            for(var i=1; i< count+1; i++){
                var uom = document.forms["myForm"]['uom_'+i].value;
                if(uom == 0){
                     res = 'Y';
                    document.getElementById('uom_err_'+i).style.display = "block";
                }else{
                     document.getElementById('uom_err_'+i).style.display = "none";
                }
                var quantity = document.forms["myForm"]['quantity_'+i].value;
                if(quantity == '' ){
                     res = 'Y';
                    document.getElementById('quantity_err_'+i).style.display = "block";
                }else if(quantity == '0'){
                     document.getElementById('quantity_err_'+i).style.display = "block";
                }else{
                     document.getElementById('quantity_err_'+i).style.display = "none";
                }
                var price = document.forms["myForm"]['price_'+i].value;
                if(price == ''){
                     res = 'Y';
                    document.getElementById('price_err_'+i).style.display = "block";
                }else{
                     document.getElementById('price_err_'+i).style.display = "none";
                }
                var tax = document.forms["myForm"]['tax_'+i].value;
                if(tax == 0){
                     res = 'Y';
                    document.getElementById('tax_err_'+i).style.display = "block";
                }else{
                     document.getElementById('tax_err_'+i).style.display = "none";
                }
            }
        }
        if (res == 'Y'){
             return false;
        }
        }

    }
    function GetSelectedTextValueEmployee(key) {

        if(key == '0'){
            document.getElementById('party_name').value="";
            document.getElementById('party_email').value="";
            document.getElementById('party_phone').value="";
            document.getElementById('party_tax_number').value="";
            document.getElementById('party_address').value="";
            document.getElementById('party_ship_adderss').value="";
            document.getElementById('cost_center').value="";
            document.getElementById('party_payment_terms').value="0";
        }else{
        $("#loaderIcon").show();
        $.ajax({
        url: "ajax.php",
        data:'page=userDetails&user_id='+key,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            document.getElementById('party_name').value=obj.display_name;
            document.getElementById('party_email').value=obj.email;
            document.getElementById('party_phone').value=obj.phone;
            document.getElementById('party_tax_number').value=obj.gstin;
            document.getElementById('currency_id').value=obj.currency_id;
            document.getElementById('cost_center').value=obj.cost_center;
            document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById('party_ship_adderss').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById("party_payment_terms").value = obj.payment_terms;
            duedate();
        },
        error:function (){}
        });
        }
    }
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

    function invoicePrefixchange(val){
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
            calculateTotal();
    });
    $(document).on('change', "#disType", function(){
            calculateTotal();
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
function exchangerate(){
    calculateTotal();
}
function calculateTotal(){
    var totalAmount = 0;
    var totaltaxrate = 0;
    var subamount = 0;
    var formatter = new Intl.NumberFormat('en-IN', {
  minimumFractionDigits: 2,
});
    var taxtype = $('#taxtype').val();
    $("[id^='price_']").each(function() {
        var id = $(this).attr('id');
        id = id.replace("price_",'');
        var price = $('#price_'+id).val();
        var quantity  = $('#quantity_'+id).val();
        var tax = $('#tax_'+id).val();
        console.log('price'+price+'qty'+quantity);
        if(!quantity) {
            quantity = 1;
            $('#quantity_'+id).val('1');
        }
        var amount = price*quantity;
        var inclusive_tax = 0;
        $('#amount_'+id).val(amount);
        var aval_budget = $('#purchase_account_aval_'+id).val();
        if(aval_budget < amount){
                $('#dis_amount_'+id).css('color', '#f00');
        }else{
             $('#dis_amount_'+id).css('color', '#000');
        }
        $('#dis_amount_'+id).val(formatter.format(amount));
        var ptaxRate = 0;
        if(tax != '0' && taxtype != 'O'){
            if(taxtype == 'I'){
                inclusive_tax = tax;
            }
            console.log("taxtype"+taxtype+"inclusive_tax"+inclusive_tax);
            var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
            var tax_cal1 = tax/tax_cal;
            ptaxRate = tax_cal1*amount;
        }
        $('#ptax_amount_'+id).val(ptaxRate);
        $('#p_amount_'+id).val(amount);

        $('#dis_ptax_amount_'+id).val(formatter.format(ptaxRate));
        console.log('amount'+amount +'tax'+ptaxRate);
        var total = ptaxRate+amount;

        subamount += amount ;
        totalAmount += total;
        totaltaxrate += ptaxRate;
    });
    $('#subTotal').val(parseFloat(subamount).toFixed(2));
    $('#dis_subTotal').val(formatter.format(subamount));
    var taxRate = $("#taxRate").val();
    var subTotal = $('#subTotal').val();
   displaytaxes();
    if(subTotal) {
        var shipping_tax_amount = $('#shipping_tax_amount').val();
        if(taxtype == 'E'){
            var  taxAmount = $('#taxAmount').val();
        }else{
            var  taxAmount = shipping_tax_amount;
        }
        console.log("taxAmount"+taxAmount);
        $('#dis_taxAmount').val(formatter.format(taxAmount));
        $('#dis_grandtotal').val(formatter.format(grandtotal));
        var grandtotal = parseFloat(subTotal)+parseFloat(taxAmount);
        var disType = $('#disType').val();
        var disValue = $('#disValue').val();
        var div_amount_total = $('#div_amount_total').val();
        var table_name = $('#table_name').val();
        if(disType && disValue) {
            if(disType && disValue) {

                var disValue1 = 0; var disValue2 = 0;
                if(disType=='P'){
                    disValue1 = (disValue/100)*subTotal;
                    disvalue2 = (disValue/100)*parseFloat(div_amount_total);
                }else if(disType=='A'){
                    disValue1 = disValue;
                }
                console.log("grandtotal"+grandtotal);
                console.log("disValue"+disValue1);
                grandtotal = parseFloat(grandtotal).toFixed(2) - parseFloat(disValue1).toFixed(2);
                console.log("grandtotal"+grandtotal);
                $('#discount').val(parseFloat(disValue1).toFixed(2));
                $('#dis_discount').val(formatter.format(disValue1));
                $('#dis_grandtotal').val(formatter.format(grandtotal));
            }
        }
        var shipping_amount = $('#shipping_amount').val();
        console.log('123'+grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
        if(shipping_amount != '' && shipping_tax_amount !='0'){
            console.log(grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
            grandtotal = parseFloat(grandtotal)+parseFloat(shipping_amount);
        }
        var tds_tax = $('#tds_tax').val();
        if(tds_tax !=0 && tds_tax !=''){
            var tds_amount = parseFloat(tds_tax)/100;
            tds_amount = tds_amount * parseFloat(subTotal);
            console.log(tds_amount +'data'+ parseFloat(subTotal));
            $('#tds_amount').val(formatter.format(tds_amount));
            grandtotal = parseFloat(grandtotal) - parseFloat(tds_amount);
        }
        $('#gdam').val(grandtotal);
        $('#grandtotal').val(grandtotal);
        $('#dis_grandtotal').val(formatter.format(grandtotal));


    }
 }

 function displaytaxes(){
    var count = $(".itemRow").length;
    console.log(count);
    var totalAftertax = $('#gdam').val();
    totalAftertax =  parseFloat(totalAftertax);
    var disType = $('#disType').val();
    var disValue = $('#disValue').val();
    var subTotal = $('#subTotal').val();
     var div_amount_total = 0;
    var disValue1 = 0;
    if(disValue > 0 ) {
        console.log('Dicount check1');
        if(disType=='P'){
            console.log('Dicount check2');
            disValue1 = (parseFloat(disValue)/100)*parseFloat(subTotal);
        }else if(disType=='A'){
            console.log('Dicount check3');
            disValue1 = parseFloat(disValue);
        }
    }
    console.log("DISVAL:"+disValue1);
    var str ='';
    var change_state =  $('#party_place_of_supply').val();
    var old_state =  $('#old_place').val();
    var taxtype = $('#taxtype').val();
    var check_arr = [];
    var arr2 = [];
    for(var i=1; i< count+1; i++) {
        var id = i;
        var tax = $('#tax_'+id).val();
        var amount = $('#amount_'+id).val();
        if(disValue1 > 0){
            console.log("mann snani");
            console.log(disValue1);
            var dis_val = (parseFloat(disValue1)/parseFloat(subTotal))*parseFloat(amount);
            amount = parseFloat(amount)-parseFloat(dis_val);
            console.log(amount);
            div_amount_total = +amount;
            var ptaxRate = 0;
            var inclusive_tax = 0;
            if(tax != '0' && taxtype != 'O'){
                if(taxtype == 'I'){
                    inclusive_tax = parseFloat(tax);
                }
                var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
                var tax_cal1 = tax/tax_cal;
                console.log("taxcal:"+tax_cal1);

                ptaxRate = tax_cal1*amount;
            }
            $('#ptax_amount_'+id).val(ptaxRate);
            $('#p_amount_'+id).val(amount);
            $('#pdiscount_amount_'+id).val(ptaxRate);
            var tax_amount = ptaxRate;
        }else{
            var tax_amount = $('#ptax_amount_'+id).val();
            tax_amount = parseFloat(tax_amount);
            div_amount_total = +amount;
        }

        if(taxtype == 'I'){
            amount = parseFloat(amount)-parseFloat(tax_amount);
            amount = parseFloat(amount).toFixed(2);
        }
        if(tax_amount !=0){
            console.log("RAVi");
            if(check_arr.indexOf(tax)!=-1){
                console.log("RAVi123");

                for(j=0; j< arr2.length ; j++){
                   if(arr2[j]['id'] == tax){
                        arr2[j]['amount'] =  parseFloat(arr2[j]['amount']) + parseFloat(amount);
                        arr2[j]['tax_amount'] =  parseFloat(arr2[j]['tax_amount']) + parseFloat(tax_amount);
                        break;
                   }
                }
            }else{
                var  arr = {};
                check_arr.push(tax);
                arr['id'] = tax;
                arr['percentage'] = tax;
                arr['amount'] = amount;
                arr['tax_amount'] = tax_amount;
                arr2.push(arr);
            }
        }

    }

     $('#div_amount_total').val(div_amount_total);
    var shipping_tax = $('#shipping_tax').val();
    var shipping_amount = $('#shipping_amount').val();
    if(shipping_tax!=0 && shipping_amount != ''){
       var sss =  (parseFloat(shipping_tax)/100)*parseFloat(shipping_amount);
       $('#shipping_tax_amount').val(sss);
       sss =parseFloat(sss).toFixed(2);
        if(check_arr.indexOf(shipping_tax)!=-1){
                for(j=0; j< arr2.length ; j++){
                   if(arr2[j]['id'] == shipping_tax){
                        arr2[j]['amount'] =  parseFloat(arr2[j]['amount']) + parseFloat(shipping_amount);
                        arr2[j]['tax_amount'] =  parseFloat(arr2[j]['tax_amount']) + parseFloat(sss);
                        break;
                   }
                }
        }else{
            var  arr = {};
            check_arr.push(tax);
            arr['id'] = shipping_tax;
            arr['percentage'] = shipping_tax;
            arr['amount'] = shipping_amount;
            arr['tax_amount'] = sss;
            arr2.push(arr);
        }

    }
    var totaltax = 0;
    for( var k =0 ; k < arr2.length; k++){

            if(old_state != change_state){
                str+= "<tr><td style='width: 67%;text-align:right;'> IGST @ "+arr2[k]['id']+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input aria-label="Tax Amount"  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"''> </td></tr>";
            }else{
                str+= "<tr><td style='width: 67%;text-align:right;'> CGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input aria-label="Tax Amount"  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
                 str+= "<tr><td style='width: 67%;text-align:right;'> SGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input aria-label="Tax Amount"  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
            }

        totaltax+=parseFloat(arr2[k]['tax_amount']);
    }
   console.log("Hello world!");
   console.log(arr2);
    totaltax  = parseFloat(totaltax).toFixed(2);
    console.log('Ravi');
    console.log('good');
    console.log('totaltax'+totaltax);

    $('#taxAmount').val(totaltax);
    $("#display_tax").html(str);
 }
 function discounts(){
     var formatter = new Intl.NumberFormat('en-IN', {
  minimumFractionDigits: 2,
});
    var disType = $('#disType').val();
    var disValue = $('#disValue').val();
    console.log(disType);
    console.log(disValue);
    var totalAftertax = $('#subTotal').val();
    var gdam = $('#gdam').val();
    var div_amount_total = $('#div_amount_total').val();
    console.log(totalAftertax);
    totalAftertax =  parseFloat(totalAftertax);
    console.log(totalAftertax);
    if(disType && disValue && totalAftertax) {
            var disValue1 = 0; var disValue2 = 0;
            if(disType=='P'){
                disValue1 = (disValue/100)*totalAftertax;
                 disvalue2 = (disValue/100)*parseFloat(div_amount_total);
            }else if(disType=='A'){
                disValue1 = disValue;
            }
            totalAftertax = parseFloat(gdam) - parseFloat(disValue1);
            $('#discount').val(parseFloat(disValue1).toFixed(2));
            $('#dis_discount').val(formatter.format(disvalue2));
            $('#dis_discount').val(formatter.format(disValue1));
            $('#dis_grandtotal').val(formatter.format(totalAftertax));
            $('#grandtotal').val(parseFloat(totalAftertax).toFixed(2));
    }
 }
 function changeexchange(){
     var formatter = new Intl.NumberFormat('en-IN', {
  minimumFractionDigits: 2,
});
    var ex_id = $('#currency_id').val();
    var de_ex_id = $('#config_currency_id').val();
    if(ex_id != de_ex_id){
        document.getElementById('exchange').style.display = "block";
        var ex_amount = $('#currency_rate').val();
        var subtotal = $('#subTotal').val();
        var grandtotal = $('#grandtotal').val();
        var discount = $('#discount').val();
        var taxamount = $('#taxAmount').val();
        $('#ex_rate').val(formatter.format(ex_amount));
        $('#ex_sub_total').val(formatter.format(subtotal*ex_amount));
        $('#ex_tax').val(formatter.format(taxamount*ex_amount));
        $('#ex_discount').val(formatter.format(discount*ex_amount));
        $('#ex_grand_total').val(formatter.format(grandtotal*ex_amount));
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
 function quantityCheckold(fid){
    var table_name = $('#table_name').val();
    if(table_name != 'requisitions'){
    var id = fid.split('_');
    id = id[1];
    var quantity  = $('#quantity_'+id).val();
    var quantity_old = $('#quantity_old_'+id).val();
    if (parseFloat(quantity_old) < parseFloat(quantity)){
        swal.fire({
            title: "Quantity alert ",
            text: "6030 : Quantity cannot be more than "+quantity_old,
            type: "warning",
            buttonsStyling: false,
            confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
            confirmButtonClass: "btn btn-danger",
        }).then(function(result) {

                $('#quantity_'+id).val(quantity_old);

                calculateTotal();
        });
    }
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
                            text: "6020 : Do you want to change the quantity to "+parseFloat(data)+" ?",
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
 function submit(val){
    console.log(document.getElementById('myForm').name);
    document.getElementById('myForm').submit();
 }
 function changestatus(val,val2){
    $('#status_values').val(val);
        var x = val;
        var table_name = document.getElementById('table_name').value;
        var table_id =document.getElementById('hid').value;
        var tranid = document.getElementById('invoice_number').value;
        var table = document.getElementById('table_name');
        var val_sub = "234";
    if(val2 == 2){

        console.log('table:'+table_name+'field:'+x+'tranid:'+tranid);
        jQuery.ajax({
            url: "views/custom_quotation_vender_pop.php",
            data:'table='+table_name+'&field='+x+'&rid='+table_id+'&tranid='+table_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
                $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
                   submit();
                });
            },
            error:function (){}
        });
    }else if(val2 == 1){
        jQuery.ajax({
            url: "views/custom_quotation_pop.php",
            data:'table='+table_name+'&field='+x+'&rid='+table_id+'&tranid='+tranid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
                $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
                   submit();
                });
             },
            error:function (){}
        });
    }else if(val2 == 3){
        var  str = '';
        str = 'table='+table_name+'&new_table='+'purchase_orders'+'&vender_id='+$('#party_id').val()+'&tranid='+$('#hid').val()+'&hid='+$('#hid').val()+'&user_id='+$('#user_id').val();
        console.log(str);
        alert(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertvenderquatation&'+str,
            success: function(response) {
                console.log(response);
                submit();
            }
        });

    }else{
        submit();
    }

 }
</script>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($_GET['error'] !=''){ ?>
    <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">    <?php echo $_GET['error']." : ".$_SESSION['error_message'][$_GET['error']]; ?>   </div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php } ?>
<div class="row">
<div class="col-lg-12">
<form class="kt-form kt-form--label-right" name="myForm" id="myForm" action="" method="post" enctype="multipart/form-data"  >
<div class="kt-portlet">

<!--begin::Invoice Head -->
 <div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            <?php echo $title; ?>
        </h3>
    </div>
    <div class="kt-portlet__head-toolbar">
     <?php if($rid != "" && $copy == 1){?>
        <a target="_blank" href="index.php?option=COM_QO_PRINT&rid=<?php echo $rid; ?>&table=<?php echo $table_name; ?>&title=<?php echo urlencode($title); ?>" class="btn btn-clean kt-margin-r-10">
                <i class="flaticon2-print"></i>

         </a>
            $nmm= ViewStatusValue($table1['current_status']);
            $lists = toGetStatusProgressAction($table_name, $table1['current_status']);
            $lists1 = $lists;
              $appruser_list_data = togetaprovaluserlist($table_name, $rid);

                $appruser_list = $appruser_list_data['user'];
                $appruser_data =  $appruser_list_data['details'];
            if($_SESSION['userid'] != $table1['originator']){
                $small_sub = 0;

                if(!in_array($_SESSION['userid'], (array)$appruser_list)){
                    $lists = array();
                    $small_sub = 1;
                }else{
                   $aptlv = $appruser_data[$_SESSION['userid']]['response_type'];
                    if( $aptlv !=0){
                        $sss =  ViewStatusValue($aptlv);

                        $nmm['name'] = $sss['name'];
                        $lists = array();
                        $small_sub = 2;
                         $commentss[] = $appruser_data[$_SESSION['userid']];
                    }

                }
            }else{
                $commentss = $appruser_data;
                 if(!($table1['current_status'] == ST_ACTIVE || $table1['current_status'] == ST_DRAFT || $table1['current_status'] == ST_SEND_BACK || $table1['current_status'] == ST_QUOTATION_GENERATED || $table1['current_status'] == ST_PURCHASE_ORDER_GENRATED || $table1['current_status'] == ST_APPROVED)){
                      $lists = array();
                 }
            }
                ?>
        <div class="btn-group">
            <button class="btn btn-primary btn-sm dropdown-toggle" style="background: #<?php echo $nmm['background_color']; ?>;border: #<?php echo $nmm['background_color']; ?>; color:#<?php echo $nmm['color']; ?>  " type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $nmm['name']; ?>
            </button>
            <?php if(count((array)$lists)>0){ ?>
            <div class="dropdown-menu">

                <?php foreach ($lists as $list) {
                    if($list['status'] == ST_QUOTATION_GENERATED ){ ?>
                    <div class="dropdown-item" onclick="changestatus(<?php echo $list['status']; ?>,'2')" name="status" id="status" value="<?php echo $list['status']; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $list['action_label']; ?> </div>
                <?php }elseif( $list['status'] ==  ST_PURCHASE_ORDER_GENRATED || $list['status'] == ST_INVOICE_GENERATED){ ?>
                    <div class="dropdown-item" onclick="changestatus(<?php echo $list['status']; ?>,'3')" name="status" id="status_2" value="<?php echo $list['status']; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $list['action_label']; ?></div>
                <?php }elseif($list['status'] != ST_ACTIVE  && $list['status'] != ST_APPROVAL_PENDING){ ?>
                    <div class="dropdown-item" onclick="changestatus(<?php echo $list['status']; ?>,'1')" name="status" id="status_3" value="<?php echo $list['status']; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $list['action_label']; ?></div>
                <?php }else{?>
                    <div class="dropdown-item" onclick="changestatus(<?php echo $list['status']; ?>,'4')" name="status" id="status_4" value="<?php echo $list['status']; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $list['action_label']; ?></div>

            <?php } ?>
            <?php } ?>

            </div>
           <?php  } ?>
        </div>

    <?php } ?>
        <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
            <i class="la la-arrow-left"></i>
            <span class="kt-hidden-mobile">Back</span>
        </a>
        <input type="hidden" name="status_values" id="status_values" value="<?php echo $table1['current_status']; ?>">
        <input type="hidden" name="status_old_value" id="status_old_value" value="<?php echo $table1['current_status']; ?>">
    </div>
</div>
<!--END::Invoice Head -->

<!--begin::Page frist part-->
<div class="kt-portlet__body">
<div class="form-group row">
<?php
   $tran_no =  $table1['qo_number'];
 ?>

<div>
    <span class="col-form-label col-lg-2 col-sm-12">Transaction Date:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <div class="input-group date">
            <?php $from = fromDate($table1['qo_date']); ?>
            <input aria-label="Input field"  class="form-control" onblur="duedate()" type='text' autocomplete="off" value='<?php  echo $from; ?>' name="invoiced_at" id="kt_datepicker_4_3"  />
            <div class="input-group-append">
                <span class="input-group-text"> <i class="la la-calendar"></i></span>
             </div>
        </div>
    </div>
</div>

 <?php if(in_array($table_name, (array)$originator_tables)){  ?>
   <span class="col-form-label col-lg-2 col-sm-12">Originator:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <select aria-label="Party" class="form-control" id="party_id" name= "party_id" onchange="GetSelectedTextValueEmployee(this.value)" >
            <option value="0"> Select Any EMployee </option>
            <?php foreach ($cusresponse as $key => $cus) {  ?>
            <option  <?php if($table1['party_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['first_name']," ".$cus['middle_name']."".$cus['last_name']; ?>
            </option>
            <?php } ?>
        </select>
    </div>
<?php } elseif(in_array($table_name, (array)$stock_table)){ ?>
    <span class="col-form-label col-lg-2 col-sm-12">Location:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <select aria-label="Transfer Location" class="form-control" id="transfer_location_id" name= "transfer_location_id" >
            <option value="0"> Select Any Location </option>
            <?php foreach ($locations_data as $locatdata) {  ?>
            <option  <?php if($table1['transfer_location_id'] == $locatdata['id']){ ?> selected  <?php
} ?> value = <?php echo $locatdata['id'] ?>   >      <?php echo $locatdata['location_description']; ?>
            </option>
        <?php } ?>
        </select>
    </div>

<?php }else{ ?>
    <span class="col-form-label col-lg-2 col-sm-12">Vendor:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <select aria-label="Party" class="form-control" id="party_id_2" name= "party_id" onchange="GetSelectedTextValue(this.value)" >
            <option value="0"> Select Any Vendor </option>
            <?php foreach ($cusresponse as $key => $cus) {  ?>
            <option  <?php if($table1['party_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?>
            </option>
        <?php } ?>
        </select>
    </div>
<?php } ?>
    <span class="col-lg-2 col-form-label">Transaction Number:</span>
    <div class="col-lg-3">
        <input class="form-control" type='hidden' id="ord_no" value='<?php echo $invoicenumber; ?>' name="invoice_serial_number"/>
        <input aria-label="Input field"  class="form-control" readonly type='text' value='<?php echo $tran_no; ?>'   name="invoice_number" id="invoice_number" />

    </div>
</div>
<div class="form-group row">
    <span class="col-form-label col-lg-2 col-sm-12">Cost Center:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <select aria-label="Cost Center" class="form-control" id="cost_center" name="cost_center">
            <option value="0"> Select any  </option>
            <?php foreach ($costs as  $k1 =>$pos) {  ?>
                <optgroup label="<?php echo $k1; ?>">

                <?php foreach ($pos as $k2=> $pos1) {  ?>
                <option  <?php if($table1['cost_center'] == $k2){ ?> selected  <?php
} ?>  value ="<?php echo $k2; ?>"> <?php echo $pos1; ?> </option>
            <?php } ?>
            </optgroup>
        <?php } ?>
        </select>
    </div>
    <span class="col-form-label col-lg-2 col-sm-12">Transaction Date:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <div class="input-group date">
            <?php $from = fromDate($table1['qo_date']); ?>
            <input aria-label="Input field"  class="form-control" onblur="duedate()" type='text' autocomplete="off" value='<?php  echo $from; ?>' name="invoiced_at" id="kt_datepicker_4_3_2"  />
            <div class="input-group-append">
                <span class="input-group-text"> <i class="la la-calendar"></i></span>
             </div>
        </div>
    </div>
</div>
<div class="form-group row" <?php echo $petty_cash_style; ?>>
    <span class="col-form-label col-lg-2 col-sm-12">Department:</span>
    <div class=" col-lg-3 col-md-9 col-sm-12">
        <select aria-label="Param" class="form-control kt-select2" id="kt_select2_3" name="param">
           <?php foreach ($departmentslist as  $department) { ?>

            <option value="<?php echo $department['id']; ?>"><?php echo $department['department_name']; ?></option>
            <?php } ?>
        </select>
    </div>
     <?php $table1['place_of_supply'] = $table1['place_of_supply'] !='' ? $table1['place_of_supply'] : $location_state; ?>
    <span class="col-lg-2 col-form-label">Place Of Supply:</span>
    <div class="col-lg-3">
        <select aria-label="Place Of Supply" class="form-control" name="place_of_supply" id="party_place_of_supply" onchange="displaytaxes()">
            <option value="0"> Select Any Place </option>
            <?php foreach ($posresponse as $key => $pos) {  ?>
                <option   <?php if($table1['place_of_supply'] == $pos['id']){ ?>selected <?php
} ?>  value ="<?php echo $pos['id']; ?>" > <?php echo $pos['state_name']; ?> </option>
            <?php } ?>
        </select>
        <input type="hidden" name="old_place" id="old_place" value="<?php echo $location_state;?>">

    </div>
</div>
<div class="form-group row">
<?php if($table_name != 'petty_cash'){
        if($table_name != 'bills'){
    ?>
<span class="col-form-label col-lg-2 col-sm-12">Category:</span>
<div class=" col-lg-3 col-md-9 col-sm-12">
    <select aria-label="Type" class="form-control" id="kt_select2_4" name= "type"  >
               <?php foreach ($itemtypelist as $itemtype) {  ?>
                     <option   <?php if($table1['type'] == $itemtype['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $itemtype['genc_detail_code']; ?>" > <?php echo $itemtype['genc_detail_name']; ?> </option>
                <?php } ?>
     </select>
</div>
<?php } ?>
<?php } else { ?>
<span class="col-form-label col-lg-2 col-sm-12">Branch:</span>
<div class=" col-lg-3 col-md-9 col-sm-12">
    <select aria-label="Type" class="form-control" id="kt_select2_4_2" name= "type"  >
               <?php foreach ($boxlist as $itemtype) {  ?>
                     <option   <?php if($table1['id'] == $itemtype['id']){ ?>selected <?php
} ?>  value ="<?php echo $itemtype['id']; ?>" > <?php echo $itemtype['pc_box_code']; ?> </option>
                <?php } ?>
     </select>
</div>
<?php } ?>
<span class="col-lg-2 col-form-label" <?php echo $petty_cash_style; ?>>Type:</span>
<div class="col-lg-3" <?php echo $petty_cash_style; ?>>
    <div class="kt-radio-inline">
        <label class="kt-radio"> <input aria-label="Radio option" type="radio"  <?php if($table1['opex_cpex']== 0){ ?>checked <?php
} ?> name="opex_cpex" value='0'>Opex    <span></span></label>
        <label class="kt-radio"> <input aria-label="Radio option" type="radio"  <?php if($table1['opex_cpex'] == 1){ ?>checked <?php
} ?> name="opex_cpex" value='1'>Cpex    <span></span></label>
    </div>
</div>
</div>
<div class="form-group row"  <?php echo $petty_cash_style; ?>>
    <div class="col-md-6 col-12 mrgn-btm">
        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6_inv">

            <div class="card">
                <div class="card-header" id="headingTwo6">
                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo6" aria-expanded="false" aria-controls="collapseTwo6">
                         Contact Detail
                    </div>
                </div>
                <div id="collapseTwo6" class="collapse" aria-labelledby="headingTwo6" data-parent="#accordionExample6_inv">
                    <div class="card-body">
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Name</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                  <input aria-label="Party Name" readonly class="form-control"  type='text' id="party_name" name="party_name"    value="<?php echo $table1['party_name']; ?>" />
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group row">
                                    <span class="col-form-label col-lg-4 col-sm-12">Billing Address</span>
                                    <div class="col-lg-8 col-md-8 col-sm-12">
                                       <textarea aria-label="Enter your address" type="text" name="party_address" id="party_address" class="form-control" placeholder="Enter your address"><?php echo $table1['party_address'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group row">
                                    <span class="col-form-label col-lg-4 col-sm-12">Shipping Address</span>
                                    <div class="col-lg-8 col-md-8 col-sm-12">
                                       <textarea aria-label="Enter your address" type="text" class="form-control" id="party_ship_adderss" name='party_ship_adderss' placeholder="Enter your address"><?php echo $table1['party_address'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Email Address</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input aria-label="Party Email" type='text' class="form-control" id="party_email" readonly name="party_email"  value="<?php echo $table1['party_email']; ?>"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Phone Number</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input aria-label="Party Phone" type='text' id="party_phone" name="party_phone" class="form-control" readonly value="<?php echo $table1['party_phone']; ?>" />
                                    <input type='hidden'  name="company_id" id="company_id" class="form-control" readonly value="<?php echo $_SESSION['company_id']; ?>" />
                                </div>
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
   <div class="col-md-6 col-12 mrgn-btm-2" >
        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6_2">

            <div class="card">
                <div class="card-header" id="headingTax">
                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTax" aria-expanded="false" aria-controls="collapseTax">
                    Tax/Other Details
                    </div>
                    <input type="hidden" name="location_id" value="<?php echo $table1['location_id']; ?>">
                </div>
                <div id="collapseTax" class="collapse" aria-labelledby="headingTax" data-parent="#collapseTax">
                    <div class="card-body">
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Tax Number</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input aria-label="Party Tax Number" type='text' id="party_tax_number" name="party_tax_number" class="form-control" readonly value="<?php echo $table1['party_tax_number']; ?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Payment Terms</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                     <select aria-label="Payment Terms" class="form-control"  onchange="duedate()"  name="payment_terms" id="party_payment_terms">
                                            <option value="0"> Select Any Term </option>
                                        <?php foreach ($posresponsept as $key => $pos) {  ?>
                                            <option   <?php if($table1['payment_terms'] == $pos['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $pos['genc_detail_code']; ?>" > <?php echo $pos['genc_detail_name']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    $originator = $table1['originator'] !="" ? $table1['originator']  : $_SESSION['userid']; ?>
                                    <input type="hidden" name="originator" value="<?php echo $originator;  ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Currency</span>
                                <?php  $table1['currency_id'] =  $table1['currency_id'] !="" ? $table1['currency_id'] : $_SESSION['config']['default_currency_id'];?>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select aria-label="Select option" class="form-control" onchange="currencychange(this.value,<?php echo  $_SESSION['config']['default_currency_id'];  ?>)"  name="currency_id" id="currency_id">
                                        <option value="0"> Select Any Currency </option>
                                        <?php
                                        foreach ($cr_data as  $crpos) {  ?>
                                            <option  <?php if($table1['currency_id'] == $crpos['id']){ ?> selected  <?php
} ?>   value ="<?php echo $crpos['id']; ?>" > <?php echo base64_decode($crpos['currency_list']); ?> </option>
                                        <?php } ?>
                                    </select>
                                     <input type='hidden' class="form-control" id="config_currency_id"  name=""  value="<?php echo $_SESSION['config']['default_currency_id']; ?>"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Ex Rate</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                   <input aria-label="Currency Rate" type='text' class="form-control" id="currency_rate" onchange="exchangerate()"  name="currency_rate"  value="<?php echo customeummarmonyformate($table1['currency_rate']); ?>"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Due Date:</span>
                                  <?php $duee = $table1['due_date'] !='' ?  fromDate($table1['due_date']) :  ''; ?>
                                   <div class="col-lg-8 col-md-8 col-sm-12">
                                <input aria-label="Due Date" type='text' class="form-control" id="due_date"   name="due_date"  value=" <?php  echo $duee; ?>" readonly />
                            </div>
                            </div>
                            <?php if(!in_array($table_name, (array)$originator_table_withbill)){
                                  $req_list = toGetPendingRequisitionQty($rtable_name);?>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Refernce Number</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                     <select aria-label="Ref" class="form-control"  name="ref_id" id="ref_id" onchange="toredriacte(this.value,<?php echo  "'".$option."'"; ?>,<?php echo  "'".$rtable_name."'"; ?>)">
                                        <option value="0"> Select Any Refernce </option>
                                        <?php foreach ($req_list as  $req) {  ?>
                                            <option  <?php if($table1['ref_id'] == $req['id']){ ?> selected  <?php
} ?>   value ="<?php echo $req['id']; ?>" > <?php echo $req['transaction_number']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <input type='hidden' class="form-control"  name="ref_no"  value="<?php echo $table1['ref_no']; ?>"/>
                                    <input type='hidden' class="form-control"  name="ref_type"  value="<?php echo $table1['ref_type']; ?>"/>
                                </div>

                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Refernce Date</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="kt-input-icon kt-input-icon--right" >
                                        <input aria-label="Ref Date" type='text' class="form-control" readonly id="ref_date"  name="ref_date"  value="<?php echo $table1['ref_date']; ?>"/>
                                        <span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-calendar"></i></span></span>
                                    </div>
                                </div>
                            </div>
                            <?php } if($table_name == 'bills'){?>
                             <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Refernce Number</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">

                                    <input aria-label="Doc Ref No" type='text' class="form-control"  name="doc_ref_no"  value="<?php echo $table1['doc_ref_no']; ?>"/>
                                </div>

                            </div>
                            <div class="form-group row">
                                <span class="col-form-label col-lg-4 col-sm-12">Refernce Date</span>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="kt-input-icon kt-input-icon--right" >
                                        <input aria-label="Doc Ref Date" type='text' class="form-control"  id="kt_datepicker_32"  name="doc_ref_date"  value="<?php echo $table1['doc_ref_date']; ?>"/>
                                        <span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-calendar"></i></span></span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!--End::Page frist part-->

<!--begin::Page second part -->

<div class="kt-portlet__head">
        <div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title"> Items/Services </h3></div>
    <div class="kt-portlet__head-label" style="float: right;">
         <div class="form-group col-lg-12">
            <select aria-label="Tax Type" class="form-control" id="taxtype" name= "tax_type" onchange="calculateTotal()" >
                    <option  <?php if($table1['taxtype'] == 'E'){ ?>selected <?php
} ?> value="E"> Exclusive </option>
                    <option  <?php if($table1['taxtype'] == 'I'){ ?>selected <?php
} ?> value="I"> Inclusive </option>
                    <option  <?php if($table1['taxtype'] == 'O'){ ?>selected <?php
} ?> value="O"> Out Of scope </option>
            </select>
        </div>
     </div>
</div>
<div class="kt-portlet__body1" style="padding-top: 10px;">

<div >
<?php
    $array_table = array('requisition'=>'quotation_table','petty_cash'=>'petty_cash_table','bills'=>'bill_table','goods_receipt_notes'=>'grn_table','stock_receipts'=>'stock_receive_table','stock_adjustments'=>'stock_adjust_table','genral'=>'genral_table','account'=>'account_table');
   $display_table =$array_table[$table_list[$option]['display_table']].'.php';

    include_once $display_table;
    ?>

<!--END::Page second part -->

    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
            <button class="btn btn-success" id="addRows" type="button">+ Add More</button>
        </div>
    </div>
    <div style="float: left;width: 50%; padding: 15px;">
       <div>
            <div style="float: left;width: 48%;padding: 1%;">
                <span class="">Internal Note(s) </span>
                <textarea aria-label="Notes" style="height: 120px;" name="notes" class="form-control" ><?php echo $table1['notes']; ?></textarea>
            </div>
            <div style="float: left;width: 48%;padding: 1%;">
                <span class="">Printing Note(s) </span>
                <textarea aria-label="Printing Notes" style="height: 120px;" name="printing_notes" class="form-control" ><?php echo $table1['printing_notes']; ?></textarea>
            </div>
        </div>
          <?php if($small_sub == 0  ){ ?>
            <div style="margin-top: 10px;clear:both;">
                <span class="">Comment(s)</span>
                <textarea aria-label="Comments" style="height: 120px;" name="comments" class="form-control" id="comments" ><?php echo $comments; ?></textarea>
            </div>
            <div class="btn-group" style="margin-top: 10px;float: right;">
                     <button id="kt_llogin_signup_submit"  class="btn btn-brand" >Submit1</button>
            </div>
        <?php }  ?>


<?php if(count((array)$commentss) > 0){ ?>
<div class="col-lg-12" style="margin-top: 10px;clear:both;">
    <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample2">
        <div class="card">
            <div class="card-header" id="headingTwo21">
                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo21" aria-expanded="false" aria-controls="collapseTwo21">
                    Comments
                </div>
            </div>
            <div id="collapseTwo21" class="collapse" aria-labelledby="headingTwo21" data-parent="#accordionExample2">
                <div class="card-body">
                    <div class="kt-timeline-v2">
                        <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                            <?php
                            $stausresult = ViewStatusData();
                            foreach ($commentss as  $lists) { //print_r($lists); ?>
                            <div class="kt-timeline-v2__item">

                                <div class="kt-timeline-v2__item-cricle">
                                    <?php if($lists['response_type'] == '3'){ ?>
                                    <i class="fa fa-genderless kt-font-success"></i>
                                    <?php } else{ ?>
                                    <i class="fa fa-genderless kt-font-warning"></i>
                                    <?php } ?>
                                </div>
                                <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                    <?php if($lists['response_type'] == 0){ $list_date = $lists['request_date'];
}else{ echo $list_date = $lists['response_date'];
}?>
                                    <b> <?php echo date("M d, Y g:i A", strtotime($list_date)); ?></b>
                                    <?php echo " ".$_SESSION['users'][$lists['user_id']]." with Status "; ?><span style="color: <?php echo "#".$stausresult[$lists['response_type']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$lists['response_type']]['background_color']); ?>;" class="kt-badge  kt-badge--inline kt-badge--pill"><?php if($lists['response_type'] == 0){ echo "Yet to respond";
}else{ echo $stausresult[$lists['response_type']]['name'];
}?></span>
                                   <?php echo "</br>".$lists['comments']; ?>


                                </div>
                            </div>
                            <?php  } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <?php } ?>
</div>
    <div style="float: left;width: 50%">
    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
        <tr>
            <th scope="col" class="amount"><span>Sub Total</span></th>
            <td><span><input aria-label="Input field"  type="text" class="form-control amount" readonly value="<?php echo customeummarmonyformate($table1['sub_total']); ?>" name="dis_subTotal" id="dis_subTotal" placeholder="Subtotal" >
            <input  type="hidden" class="form-control amount" readonly value="<?php echo $table1['sub_total']; ?>" name="subTotal" id="subTotal" placeholder="Subtotal" ></span></td>
        </tr>
         <tr>
            <th scope="col" class="amount">
                <div>
                    <div style="float: left;width: 33%;padding: 10px;">
                        <span >Discount </span>
                    </div>
                    <div style="float: left;width: 33%;padding: 3px;">
                        <select aria-label="Discount Type" class="form-control" id="disType" name="discount_type" onchange="calculateTotal()">
                            <option value="P">Percentage</option>
                            <option <?php if($table1['discount_type'] == 'A'){ ?> selected <?php
} ?> value="A">Amount</option>
                        </select>
                    </div>
                    <div style="float: left;width: 33%;padding:3px;" >
                        <input aria-label="Input field" value="<?php echo number_format($table1['discount_value'],2); ?>" type="text" class="form-control amount" placeholder="value" name="discount_value" id="disValue" >

                    </div>
                </div>
            </th>
            <td><span >
                <input aria-label="Input field" value="<?php echo customeummarmonyformate($table1['discount_amount']); ?>"  readonly type="text" class="form-control amount" name="dis_discount_amount" id="dis_discount"  placeholder="Discount Amount">
                 <input aria-label="Input field" value="<?php echo $table1['discount_amount']; ?>"  readonly type="hidden" class="form-control amount" name="discount_amount" id="discount" placeholder="Discount Amount">
            </span></td>
        </tr>
        <?php if($table_name != 'bills'){ ?>
        <tr>
            <th scope="col" class="amount">
                <div>
                    <div style="float: left;width: 50%;padding: 10px;">
                        <span >Shipping</span>
                    </div>
                    <div style="float: left;width: 50%;padding: 3px;">
                        <select aria-label="Shipping Tax" class="form-control"  name="shipping_tax" id="shipping_tax" onchange="calculateTotal()" >
                            <option value="0"> Select Any Tax Rate </option>
                            <?php foreach ($taxres as $key => $tax) {  ?>
                            <option  <?php if($table1['shipping_tax'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </th>
            <input type="hidden" name="" id="tds_tax" value="0">
            <td><span >
                <input type="hidden" name="shipping_tax_amount" id="shipping_tax_amount" value="0">
                <input aria-label="Input field"  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="shipping_amount" onblur="calculateTotal()" placeholder="shipping Amount">
            </span></td>
        </tr>
    <?php } ?>

        <tr>
            <td colspan="2" >
                <table role="presentation" class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
                    <tbody id='display_tax'>
                        <?php  if(count((array)$taxes)){
                        foreach ($distax as $ktax) { ?>
                        <?php if($table1['tax_state'] == 'S'){ ?>
                        <tr>
                            <td style="width: 67%;text-align:right;">  CGST @ <?php echo customeummarmonyformate($ktax['percentage']/2); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input aria-label="Input field"  value="<?php echo customeummarmonyformate($ktax['tax_amount']/2); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                        <tr>
                            <td style="width: 67%;text-align:right;">  CGST @ <?php echo customeummarmonyformate($ktax['percentage']/2); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input aria-label="Input field"  value="<?php echo customeummarmonyformate($ktax['tax_amount']/2); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                    <?php } else{?>
                        <tr>
                            <td style="width: 67%;text-align:right;">  IGST @ <?php echo customeummarmonyformate($ktax['percentage']); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input aria-label="Input field"  value="<?php echo customeummarmonyformate($ktax['tax_amount']); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                    <?php }?>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php if($table_name == 'bills'){ ?>
        <tr>
            <th scope="col" class="amount">
                <div>
                    <div style="float: left;width: 50%;padding: 10px;">
                        <span >TDS</span>
                    </div>
                    <div style="float: left;width: 50%;padding: 3px;">
                        <select aria-label="Shipping Tax" class="form-control"  name="shipping_tax" id="tds_tax_2" onchange="calculateTotal()" >
                            <option value="0"> Select Any Tax Rate </option>
                            <?php foreach ($tdstaxres as $key => $tax) {  ?>
                            <option  <?php if($table1['shipping_tax'] == $key){ ?> selected  <?php
} ?> value = <?php echo $key; ?>   > <?php echo $tax; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </th>
            <td><span >
                <input type="hidden" name="shipping_tax_amount" id="tds_tax_amount" value="0">
                <input  value=""   type="hidden" class="form-control amount" id="shipping_amount_2" onblur="calculateTotal()" placeholder="TDS Amount">
                <input aria-label="Input field"  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="tds_amount" onblur="calculateTotal()" placeholder="TDS Amount">
            </span></td>
        </tr>
    <?php } ?>
        <tr>
            <th scope="col" class="amount" ><span>Grand Total</span></th>
             <input  type="hidden" class="form-control amount" placeholder="value" name="gdam" id="gdam">
              <input  type="hidden" class="form-control amount" placeholder="value" name="div_amount_total" id="div_amount_total">
             <input aria-label="Input field"  value="<?php echo $table1['tax_amount']; ?>" type="hidden" class="form-control amount" name="taxRate" id="taxAmount" readonly placeholder="Tax Amount">
            <td><input aria-label="Input field"  value="<?php echo customeummarmonyformate($table1['grand_total']); ?>" type="text" class="form-control amount" name="dis_grandtotal" id="dis_grandtotal" readonly placeholder="Grand Total"><input aria-label="Input field"  value="<?php echo $table1['grand_total']; ?>" type="hidden" class="form-control amount" name="grandtotal" id="grandtotal" readonly placeholder="Grand Total"></td>
        </tr>
        <tr style="display: none;">
            <th scope="col" class="amount" ><span >Amount Paid</span></th>
            <td><input aria-label="Amount Paid" value="" type="text" class="form-control amount" name="amountPaid" id="amountPaid" placeholder="Amount Paid"></td>
        </tr>
        <tr style="display: none;">
            <td  ><span >Amount Due</span></td>
            <td><input aria-label="Amount Due" value="" type="text" class="form-control amount" name="amountDue" id="amountDue" placeholder="Amount Due"></td>
        </tr>
        <?php if($table1['currency_id'] != $_SESSION['config']['default_currency_id'] && $table1['currency_id'] != ''){ $css = "";
}else{ $css = 'style="display: none;"';
}?>
        <tr  >
            <td colspan="2" >
                <table  id='exchange' <?php echo $css; ?> class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
                    <tr>

                        <th scope="col" class="amount">Sub total</th>
                        <th scope="col" class="amount">Tax</th>
                        <th scope="col" class="amount">Discount</th>
                        <th scope="col" class="amount">Grand Total </th>
                    </tr>
                    <tr>

                        <td><input aria-label="Input field" value="<?php echo customeummarmonyformate($table1['sub_total']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_sub_total" ></td>
                        <td><input aria-label="Input field" value="<?php echo customeummarmonyformate($table1['tax_amount']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_tax" ></td>
                        <td><input aria-label="Input field" value="<?php echo customeummarmonyformate($table1['discount_amount']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_discount" ></td>
                        <td> <input aria-label="Input field" value="<?php echo customeummarmonyformate($table1['grand_total']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_grand_total"> </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</div>
</div>
</div>

<?php if($rid !=''){ ?>
<div class="form-group row">
    <span class="col-form-label col-lg-3 col-sm-12">File Uoload</span>
    <div class="col-lg-4 col-md-9 col-sm-12">
        <div class="dropzone dropzone-default dropzone-brand" id="kt_dropzone_2" >

<?php if(count((array)$files)>0){  $n=1; foreach ($files as $file_value) { ?>
<div class="dz-preview dz-processing dz-image-preview dz-success dz-complete" id="removefile_<?php echo $file_value['id']; ?>">
    <div class="dz-image">
        <img data-dz-thumbnail="" alt="<?php echo $file_value['actual_file_name']; ?>" src="uploads/121/files/<?php echo $file_value['file_name']; ?>" style="height: 120px;background-color: #ccc;">
    </div>
    <div class="dz-details">
        <div class="dz-size">
            <span data-dz-size="">
                <strong><?php echo $file_value['file_size_mb']; ?></strong> MB
            </span>
        </div>
        <div class="dz-filename">
            <span data-dz-name=""><?php echo $file_value['actual_file_name']; ?></span>
        </div>
    </div>
    <div class="dz-progress">
        <span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;"></span>
    </div>
    <div class="dz-error-message"><span data-dz-errormessage=""></span></div>
    <div class="dz-success-mark">
        <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http:
    </div>
    <button type="button" class="dz-remove" data-dz-remove="" onclick="removefile(<?php echo $file_value['id']; ?>)">Remove</button>
    <a class="dz-remove" target="_blank" href="uploads/121/files/<?php echo $file_value['file_name']; ?>" >Download</a>
</div>
<?php $n++;
}
}else{ ?>

            <div class="dropzone-msg dz-message needsclick">
                <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>

                <span class="dropzone-msg-desc">Upload up to 10 files</span>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<!--begin::Submit part -->
<?php
$check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK);
$disable = ''; if(!in_array($table1['current_status'], (array)$check_array)){ $disable = "disabled" ;
} ?>
<div class="kt-portlet__foot">
    <div class="kt-form__actions">
        <div class="row">
            <div class="col-lg-4">
                 <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
                 <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                 <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                 <input type="hidden" name="old_place1" id="old_place1" value="<?php echo $location_state;?>">
                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $copy == '1'){ ?>
                        <input type="hidden" id="hid"name="hid" value="<?php echo $rid; ?>" />
                        <input type="hidden" name="hid_itemds" value="<?php echo $item_ids; ?>" />
                        <input type="hidden" name="submit1" value="Update" />
                        <button id="kt_llogin_signup_submit_2" <?php echo $disable; ?> class="btn btn-brand" ><i class="la la-check"></i>Upadate</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } else{ ?>
                        <input type="hidden" name="submit1" value="Add" />
                        <button id="kt_login_signin_submit" class="btn btn-brand"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit_continue" class="btn btn-brand"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } ?>
            </div>
            <div class="col-lg-6">
            <?php if(count((array)$vdata_details) > 0){ ?>
                <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample2_2">

                    <div class="card">
                        <div class="card-header" id="headingTwo2">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">
                                Transaction Timeline
                            </div>
                        </div>
                        <div id="collapseTwo2" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionExample2">
                            <div class="card-body">
                                <div class="kt-timeline-v2">
                                    <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                                        <?php foreach ($vdata_details as  $vdvalue) { ?>
                                        <div class="kt-timeline-v2__item">

                                            <div class="kt-timeline-v2__item-cricle">
                                                <?php if($vdvalue['transaction_type'] == 'INSERT'){ ?>
                                                <i class="fa fa-genderless kt-font-success"></i>
                                                <?php } ?>
                                                <?php if($vdvalue['transaction_type'] == 'UPDATE'){ ?>
                                                <i class="fa fa-genderless kt-font-warning"></i>
                                                <?php } ?>
                                            </div>
                                            <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                                <b> <?php echo date("M d, Y g:i A", strtotime($vdvalue['action_date'])); ?></b>
                                                <?php echo " ".$vdvalue['action_log']. " ".$_SESSION['users'][$vdvalue['user_id']]." with Status ".$_SESSION['status'][$vdvalue['status_value']]['name']; ?>

                                            </div>
                                        </div>
                                        <?php  } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
            <div class="col-lg-2 kt-align-right">
                <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--END::Submit part -->
<?php
if($rid != ''){
    $question_display ='Y';
    $replay_display = 'N';
    $trnsation_no = $req['transaction_number'];
    if($_SESSION['userid'] == $originator){
        $question_display ='N';
        $replay_display = 'Y';
    }
    include_once "queries.php";
}?>
 </div> </div>
</div>


</div></div></div></div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" style="max-width: 800px;">
                <div class="modal-content" style="min-height: 50px;width: 800px;" id="popup1">

                </div>
            </div>

        </div>

