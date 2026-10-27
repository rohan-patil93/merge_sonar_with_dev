<?php
//echo "<pre>"; print_r($_SESSION['status']); exit;
///echo "<pre>";print_r($_SESSION);
$option = strtoupper($_GET['option']);
$list = str_replace('INSERT', 'LIST', $option);
$page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);
$add_url = $page_details['insert_page_name'];
$view_url = $page_details['list_page_name'];
$table_name = 'petty_cash';

if($_POST['submit'] == 'Add'){
    //echo "<pre>";print_r($_FILES); print_r($_POST);  exit;
    $_POST['created_by'] = $_SESSION['userid'];
    $url = API."?mode=addpccash&table=".$_POST['table_name']."&method=post";
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
if($_POST['submit'] == 'Update'){
   // echo "<pre>";print_r($_FILES); print_r($_POST);  exit;
    $_POST['updated_by'] = $_SESSION['userid'];
    if($_POST['status_values'] ==  ST_QUOTATION_GENERATED){
        $retun_list = toGetpageOptionName('quotations');
        header("location:index.php?option=".$retun_list['option_url']."&table=".$_POST['table_name']."&Rid=".$_POST['hid']);
    }
    if($_POST['status_values'] == ST_PURCHASE_ORDER_GENRATED){
        $retun_list = toGetpageOptionName('purchase_orders');
        header("location:index.php?option=".$retun_list['option_url']."&table=".$_POST['table_name']."&Rid=".$_POST['hid']);
    }
    if($_POST['status_values'] == ST_APPROVAL_PENDING){
        $checkurl = API."?mode=tocheckDAO&table=".$_POST['table_name']."&status=".$_POST['status_values']."&amount=".$_POST['grandtotal']."&status_old_value=".$_POST['status_old_value']."&opex_cpex=".$_POST['opex_cpex']."&category_type=".$_POST['type']."&cost_center=".$_POST['cost_center'];
       //echo $checkurl; exit; 
        $check_data = callAPI($checkurl);
        //echo $checkurl; exit;
        $check_data = json_decode($check_data,1);
        if(!isset($check_data['id']) || $check_data['id'] == ''){
            //print_r($check_data);
            header("location:index.php?option=".$option."&error=9001&Rid=".deocdeId('id:'.$_POST['hid'])); exit;
        }
        //print_r($check_data); exit;
    }
    $url = API."?mode=addpccash&table=".$_POST['table_name']."&method=post&type=update";
    //echo $url; exit;
    $response = file_post_contents($url, $_POST, 'update');
    if ($response) {
        header("location:index.php?option=".$list);
    }
}
$title = viewTitle($_GET['option']);
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
    //echo "<pre>".$_SESSION['userid']."ravi"; print_r($cusdata); exit;
    $res = json_decode($response,1);
    $locurl = API."?mode=singlerowtabledata&table=".TBL_LOCATIONS."&fid=id&rid=".$cusdata['locaion_id'];
    $locares = callAPI($locurl); 
    $locares = json_decode($locares,1);
    $location_state = $locares['location_state'];
    $table1['cost_center'] =  $cusdata['cost_center'];
if($table_name == 'requisitions'){ 
    //echo $locares;
    
    $table1['customer_id'] = $_SESSION['userid'];
    $table1['customer_name'] = $cusdata['first_name'].' '.$cusdata['middle_name'].' '.$cusdata['last_name'];
    $table1['customer_email'] = $cusdata['user_email'];
    $table1['customer_phone'] =  $cusdata['user_mobile'];
    $table1['cost_center'] =  $cusdata['cost_center'];
    $table1['customer_tax_number'] = $locares['tax_ref'];
    $table1['customer_address'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
    $table1['customer_ship_adderss'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
} else{
    $cusurl = API."?mode=gettabledetails&table=".TBL_VENDOR; 
    $cusresponse = callAPI($cusurl); 
    $cusresponse = json_decode($cusresponse,1);
}
$taxres =  toGetTaxrates();
//print_r($taxres); exit;
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
   //echo "data"; exit;
    $rtable_name = $table_name; $subt_amount = ""; $stax_amount="";

        $rid =  encodeId($_GET['Rid']);
        //echo $rid;
        $rid = urldecode($rid);
        $rid = explode(":", $rid);
        $rid= $rid[1];
        $copy = 1;
        $vdata1["where"] = array('transaction_primary_1'=> $rid,'transaction_table'=>$rtable_name);
        $vdata1["field"] = "id,transaction_type,user_id,action_log,action_date,status_value";
        $vdata1['order'] = "id DESC";
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=".TBL_ACTION_LOG."&data=".$vdata; 
        $vdata_details = callAPI($vurl);
        $vdata_details = json_decode($vdata_details,1);
        //echo "<pre>"; print_r($vdata_details); exit;

    $rtable = $rtable_name!='' ? $rtable_name : $table_name;
    $table2 = array(''); $i=0;
    $rpurl =  API."?mode=getInvoicesData&in_id=$rid&table=$rtable";
    
    //echo $rpurl; exit;
    $rp_data = callAPI($rpurl);
    $rpres = json_decode($rp_data, 1);
    // echo "<pre>"; print_r($rpres); exit;
    $rpdata = $rpres['data'];
    $items = $rpres['items'];
    $taxes = $rpres['taxes'];
    $taxes_ship = $taxes;
    if($rpdata['shipping_tax_amount'] != 0){
    $taxes_ship[] = array('tax_id'=> $rpdata['shipping_tax'],'tax_value'=> $rpdata['shipping_tax'],'amount'=>$rpdata['shipping_amount'],'tax_amount'=>$rpdata['shipping_tax_amount']);
    }
    //echo"<pre>"; print_r($taxes_ship); //exit;
    $dutax = array();
    $distax = array();
    foreach ($taxes_ship as  $taxvalue) {
        //print_r($dutax);    
        $taxvalue['tax_value'] = number_format($taxvalue['tax_id'],2) ;
       if(in_array(number_format($taxvalue['tax_value'],2), $dutax)){
        //echo "ravi"; exit;
           $distax[$taxvalue['tax_value']] ['amount'] =  $distax[$taxvalue['tax_value']] ['amount'] + $taxvalue['amount'];
           $distax[$taxvalue['tax_value']] ['tax_amount'] =  $distax[$taxvalue['tax_value']] ['tax_amount'] +$taxvalue['tax_amount'];
       }else{
            $dutax []= number_format($taxvalue['tax_value'],2);
            $distax[$taxvalue['tax_value']] ['percentage'] =  $taxvalue['tax_value'];
            $distax[$taxvalue['tax_value']] ['amount'] =  $taxvalue['amount'];
            $distax[$taxvalue['tax_value']] ['tax_amount'] =  $taxvalue['tax_amount'];
       }
    }
    //echo"<pre>"; print_r($distax); exit;
    //echo $rtable;
    if($rtable_name != ''){
       $table1['qo_number'] = "";
       $table1['qo_date'] = "";
       $table1['ref_id'] = $rid;
       $table1['ref_no'] = $rpdata['transaction_number'];
       $table1['ref_date'] = $rpdata['transaction_date'];
       $table1['ref_type'] = $rtable_name;
    }else{ 
       $table1['qo_number'] = $rpdata['transaction_number'];
       $table1['qo_date'] = $rpdata['transaction_date']; 
       $table1['ref_id'] = $rpdata['ref_id'] ;
       $table1['ref_no'] = $rpdata['ref_no'] ;
       $table1['ref_date'] = $rpdata['ref_date'];
       $table1['ref_type'] = $rpdata['ref_type'];
    }
    //print_r($table1); exit;
    //echo $rtable_name;
    if($rtable_name != 'requisitions'){ //echo "done11";
        $table1['customer_id'] = $rpdata['party_id'];
        $table1['customer_name'] = $rpdata['party_name'];
        $table1['customer_email'] = $rpdata['party_email'];
        $table1['customer_phone'] = $rpdata['party_phone'];
        $table1['customer_tax_number'] = $rpdata['party_tax_number'];
        $table1['customer_address'] = $rpdata['party_address'];
        $table1['customer_ship_adderss'] = $rpdata['party_ship_adderss'];
    }
     $table1['transaction_number'] = $rpdata['transaction_number'];
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
    $table1['payment_method'] = $rpdata['payment_method'];
    $table1['doc_ref_no'] = $rpdata['doc_ref_no'];
    $table1['doc_ref_date'] = $rpdata['doc_ref_date'];
    $table1['due_date'] = $rpdata['due_date'];
    foreach ($taxes as  $taxs) {
       $tax_array[$taxs['transaction_item_id']] = $taxs['tax_id'];
    }
   // print_r($taxes);
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
        $itemda['doc_ref_no']= $item['doc_ref_no']; 
        $itemda['doc_ref_date']= $item['doc_ref_date'];
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
    //echo "<pre>";print_r($item_ids);exit;
}
//$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PLACEOFSUPPLY&table=".TBL_GENRAL_CODE_DETAILS;
$typeurl = API."?mode=getplaceofsuppl";
$typeresponse = callAPI($typeurl);   
$posresponse = json_decode($typeresponse,1);
$data1["field"] = "id,item_name,item_description,purchase_price,purchase_uom";
$data = urlencode(json_encode($data1));
/*$itemwr["where"] = array('account_type'=> 'E','is_active'=> 0);
$itemurl = urlencode(json_encode($itemwr));
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_CHART_OF_ACCOUNTS."&data=".$itemurl;*/
$itemurl = API."?mode=pettycashdetails";
//$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data; 
//echo $itemurl;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
//print_r($itemres); exit;
$typeurl = API."?mode=gettabledetails&fid=genc_code&rid=PAYMETERM&table=".TBL_GENRAL_CODE_DETAILS;
$typeresponsept = callAPI($typeurl);   
$posresponsept = json_decode($typeresponsept,1);

/*$uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=UOM&table=".TBL_GENRAL_CODE_DETAILS;
$uomlist = callAPI($uomurl);
$uomlist = json_decode($uomlist,1);
*/
/*$itemtypeurl =  API."?mode=gettabledetails&fid=genc_code&rid=STKTYP&table=".TBL_GENRAL_CODE_DETAILS;
$itemtypelist = callAPI($itemtypeurl);
$itemtypelist = json_decode($itemtypelist,1);*/

$paymentmethodurl =  API."?mode=gettabledetails&fid=genc_code&rid=PAYMETHOD&table=".TBL_GENRAL_CODE_DETAILS;
$paymentmethod = callAPI($paymentmethodurl);
$paymentmethod_data = json_decode($paymentmethod,1);

$crdata["field"] = "id,currency_list";
$crdata = urlencode(json_encode($crdata));
$crurl = API."?mode=gettabledetailswithconditions&table=".TBL_CURRENCIES."&data=".$crdata; 
//echo $crurl; exit;
$cr_data = callAPI($crurl);
$cr_data = json_decode($cr_data, 1);
//print_r($cr_data); exit;
if($table_name == 'requisitions'){
    $costcenterurl = API."?mode=gettabledetails&table=".TBL_COST_CENTERS;
    
    //print_r($table1); exit;
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
    //echo "<pre>"; print_r($costress); //exit;
    foreach ($costress as  $costres) {
        $incost[] = $costres['cost_center_id'];
    }
    //print_r($incost);
    foreach ($cost as $value) {
       if($value['parent_cost_center_id'] == 0){
            $costs[$value['cost_center_name']][$value['id']]= $value['cost_center_name'];
       }else{
            $name = $array[$value['parent_cost_center_id']];
            if(in_array($value['id'],$incost)){
                $costs[$name][$value['id']]= $value['cost_center_name'];
            }
       }
    }
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
function duedate(){
    //alert("hai");
    var payment_terms = $('#customer_payment_terms').val();
    var tt = $('#kt_datepicker_4_3').val();
    //alert(tt+"###"+payment_terms);
    var date = new Date(tt);
    //alert(date);
    var newdate = new Date(date);
    //alert(newdate);
    newdate.setDate(newdate.getDate() + parseInt(payment_terms));       
    var dd = newdate.getDate();
    var mm = newdate.getMonth() + 1;
    var y = newdate.getFullYear();
    var due = mm + '/' + dd + '/' + y;
    //alert(someFormattedDate);
    $('#due_date').val(due);
    
}

 function currencychange(val,old_val){
    alert(val+"||"+old_val);
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
             //id="" onchange="exchangerate()" 
            
        },
        error:function (){}
    });
 }
    function toredriacte(id,url_option,table){
        alert(id);
        window.location.href = 'index.php?option='+url_option+'&Rid='+id+'&table='+table;
    }
    function validateForm(){
        var res = 'N';
        var x = document.getElementById("status_values").value;
        var table_name = document.forms["myForm"]['table_name'].value;
        var table_id = document.forms["myForm"]['hid'].value;
        var tranid = document.forms["myForm"]['invoice_number'].value;
        if(x == 20 ||  x == 30 || 100 < x < 110 || x == 199 ){
            //alert("hai");
            var comments = document.forms["myForm"]['comments'].value;
            
            if(comments == ''){
                document.forms["myForm"]['comments'].focus();
                return false;
            }
            //var table = document.getElementById('table_name');
            //alert("hai");
           /* jQuery.ajax({
                url: "views/custom_quotation_pop.php",
                data:'table='+table_name+'&field='+x+'&rid='+table_id+'&tranid='+tranid,
                type: "POST",
                success:function(data){
                    $('#popup1').html(data);
                    $('#kt_modal_KTDatatable_remote').modal({ show: true});
                },
                error:function (){}
            });*/
            
            
        }else{
        if( x == 0 ||  x == 6){
            
            if( table_name == 'requisitions'){
                var cost_center = document.forms["myForm"]['cost_center'].value;
                if(cost_center == 0){
                     res = 'Y';
                     //alert('cost center');
                    document.getElementById('cost_center_err').style.display = "block";
                }else{
                     document.getElementById('cost_center_err').style.display = "none";
                }
            }
            var customer_id = document.forms["myForm"]['customer_id'].value;
            //alert (customer_id);
            if(customer_id == 0){
                 res = 'Y';
                document.getElementById('customer_id_err').style.display = "block";
            }else{
                 document.getElementById('customer_id_err').style.display = "none";
            }
            var customer_place_of_supply = document.forms["myForm"]['customer_place_of_supply'].value;
            if(customer_place_of_supply == 0){
                 res = 'Y';
                document.getElementById('customer_place_of_supply_err').style.display = "block";
            }else{
                 document.getElementById('customer_place_of_supply_err').style.display = "none";
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
            alert(res);
             return false;
        }
        }
        /*var data = <?php echo $mandatory_json; ?>;
        var res = 'N'; 
    //  alert(data);
        if(typeof(data) == 'object'){
            for(var item in data) {
                //alert(data[item]);
                var x ='';
                var x = document.forms["myForm"][data[item]].value;
                x = x.trim();
                if (x == ""){
                    res = 'Y';
                    document.getElementById(data[item]+'_err').style.display = "block";
                } else {
                    document.getElementById(data[item]+'_err').style.display = "none";
                }
            }
            if (res == 'Y'){
                //alert('pass');
                return false;
            }
        }*/
    }
    function GetSelectedTextValueEmployee(key) {
        alert(key);

        if(key == '0'){
           // alert('good');
            document.getElementById('customer_name').value="";
            document.getElementById('customer_email').value="";
            document.getElementById('customer_phone').value="";
            document.getElementById('customer_tax_number').value="";
            document.getElementById('customer_address').value="";
            document.getElementById('customer_ship_adderss').value="";
            document.getElementById('cost_center').value="";
            document.getElementById('customer_payment_terms').value="0";
        }else{
        $("#loaderIcon").show();
        $.ajax({
        url: "ajax.php",
        data:'page=userDetails&user_id='+key,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            //alert(obj.payment_terms);
            document.getElementById('customer_name').value=obj.display_name;
            document.getElementById('customer_email').value=obj.email;
            document.getElementById('customer_phone').value=obj.phone;
            document.getElementById('customer_tax_number').value=obj.gstin;
            document.getElementById('currency_id').value=obj.currency_id;
            document.getElementById('cost_center').value=obj.cost_center;
            document.getElementById('customer_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById('customer_ship_adderss').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById("customer_payment_terms").value = obj.payment_terms;
        },
        error:function (){}
        });
        }
    }
     function GetSelectedTextValue(key) {
        //alert(key);
        if(key == '0'){
           // alert('good');
            document.getElementById('customer_name').value="";
            document.getElementById('customer_email').value="";
            document.getElementById('customer_phone').value="";
            document.getElementById('customer_tax_number').value="";
            document.getElementById('customer_address').value="";
            document.getElementById('customer_ship_adderss').value="";

            document.getElementById('customer_payment_terms').value="0";
        }else{
        $("#loaderIcon").show();
        $.ajax({
        url: "ajax.php",
        data:'page=vendor&ven_id='+key,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            //alert(obj.payment_terms);
            document.getElementById('customer_name').value=obj.display_name;
            document.getElementById('customer_email').value=obj.email;
            document.getElementById('customer_phone').value=obj.phone;
            document.getElementById('customer_tax_number').value=obj.gstin;
            document.getElementById('currency_id').value=obj.currency_id;
            document.getElementById('customer_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
            document.getElementById('customer_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
            document.getElementById("customer_payment_terms").value = obj.payment_terms;
            duedate();
        },
        error:function (){}
        });
        }
    }
    function itemChange(item_id, id){
        //alert(item_id+'##'+id);
        if(item_id == '-1'){
            //alert('check');
            $("#other_"+id).html('<input type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }
       // alert(item_id+'##'+id);
        $.ajax({
        url: "ajax.php",
        data:'page=pettycashdetails&item_id='+item_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            var price =  parseFloat(obj.purchase_price).toFixed(2);
            //price = price.toPrecision(2);
            document.getElementById('productName_'+id).value=obj.description;
            /*document.getElementById('uom_'+id).value=obj.purchase_uom;
            document.getElementById('price_'+id).value=price;*/
            document.getElementById('item_name_'+id).value=obj.expense_name;
            document.getElementById('account_code_'+id).value=obj.gl_account;
            
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
        //alert(ord);
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
        htmlRows += '<td><label class="kt-checkbox"> <input  class="itemRow" type="checkbox">  <span> </span> </label> </td>';    
    //    htmlRows += '<td><input class="form-control" type="text" value="'+count+'" > </td>';      
        htmlRows += '<td id="other_'+count+'"><select class="form-control"  name= "productCode[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')" > <option value="0"> Select Any Category </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[id]; ?>><?php echo $item[expense_name]; ?> </option> <?php } ?><option  value = "-1"> Other</option> </select> <input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"  autocomplete="off"> </td>';
        //
        //htmlRows += '<td><input type="text" name="productCode[]" id="productCode_'+count+'" class="form-control" autocomplete="off"></textarea></td>';          
        htmlRows += '<td><textarea name="productName[]" id="productName_'+count+'" class="form-control" autocomplete="off"></textarea></td><input type="hidden" name="account_code[]" id="account_code_'+count+'" class="form-control"  autocomplete="off">  ';  
        //htmlRows += '<td><select class="form-control"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2[uom] == $uom[genc_detail_code]){ ?>selected <?php } ?>  value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php } ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div></td> '; 
       // htmlRows += '<td><input type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div></td>';           
        htmlRows += '<td> <input type="hidden" name="quantity[]" id="quantity_'+count+'"    class="form-control amount" value="1"  autocomplete="off"> <input type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div></td>';    
        //htmlRows += '<td><input type="text" name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off"></td>';    
        htmlRows += '<td><select class="form-control"  name= "tax[]" id="tax_'+count+'"> <option value="0"> Select Any Tax Rate </option><?php foreach ($taxres as $key => $tax) {  ?>      <option <?php if($_SESSION[configdata][default_tax_rate] == $key){ ?> selected  <?php } ?> value = <?php echo $key?>><?php echo $tax; ?> </option> <?php } ?>  </select><div style="display: none;color: red;" id="tax_err_'+count+'"> Enter Tax</div>'; 

        //htmlRows += '<td> </td>'; 
        htmlRows += '<input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off">  <input type="hidden" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';
         htmlRows += '<td><input type="text" name="doc_ref_no[]"  id="doc_ref_no_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';
        htmlRows += ' <td><div class="input-group date"><input type="text" class="form-control"  value=""  name="doc_ref_date[]"  id="kt_datepicker_3'+count+'" /> <div class="input-group-append"><span class="input-group-text"><i class="la la-calendar"></i></span></div></div></td>';
        //htmlRows += '<td><input type="text" name="total[]" id="total_'+count+'" class="form-control total amount" autocomplete="off"></td>';    
      //  htmlRows +='<td> <div class="input-group date">            <input type="text" class="form-control"  value=""  name="req_date[]"  id="kt_datepicker_3'+count+'" /> <div class="input-group-append">  <span class="input-group-text"> <i class="la la-calendar"></i></span> </div></div></td>'; 

       /* htmlRows += '<td> <input type="text" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"></td>';
        htmlRows += '<td><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off">  <input type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"></td>';*/
        //htmlRows += '<td><input type="text" name="total[]" id="total_'+count+'" class="form-control total amount" autocomplete="off"></td>';          
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
    /*$(document).on('change', "[id^=productCode_]", function(){
        $("[id^='productCode_']").each(function() {
        var id = $(this).attr('id');
        id = id.replace("productCode_",'');
        var ids = $('#price_'+id).val();

        });
        
    });*/
    $(document).on('blur', "[id^=quantity_]", function(){
        //quantityCheck();
        calculateTotal();
        
    }); 
    $(document).on('blur', "[id^=price_]", function(){
        calculateTotal();
    });
    $(document).on('change', "[id^=tax_]", function(){
        calculateTotal();
    });     
    /*$(document).on('blur', "#taxRate", function(){        
        calculateTotal();
    });*/
    $(document).on('blur', "#disValue", function(){
            //alert('hai');
            calculateTotal();
    });
    $(document).on('change', "#disType", function(){
            //alert('hai');
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
       // alert(id);
        var price = $('#price_'+id).val();
        var quantity  = $('#quantity_'+id).val();
        var tax = $('#tax_'+id).val();
        console.log('price'+price+'qty'+quantity);
        if(!quantity) {
            quantity = 1;
        }
        var amount = price*quantity;
        var inclusive_tax = 0;
        $('#amount_'+id).val(amount);
        $('#dis_amount_'+id).val(formatter.format(amount));
        var ptaxRate = 0;
        if(tax != '0' && taxtype != 'O'){
            if(taxtype == 'I'){
                inclusive_tax = tax;
            }
            console.log("taxtype"+taxtype+"inclusive_tax"+inclusive_tax);
            var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
            //console.log("tax:"+tax+"tax_cal"+tax_cal);
            var tax_cal1 = tax/tax_cal;
            //console.log("taxcal:"+tax_cal1);
            ptaxRate = tax_cal1*amount; 
           // console.log("Ravi1"+ptaxRate);
        }
        $('#ptax_amount_'+id).val(ptaxRate);
        $('#p_amount_'+id).val(amount); 
        
        $('#dis_ptax_amount_'+id).val(formatter.format(ptaxRate)); 
        console.log('amount'+amount +'tax'+ptaxRate);
        var total = ptaxRate+amount;

         //console.log('ptaxRate'+  ptaxRate +':amount'+amount+'sub'+total);
        //$('#total_'+id).val(parseFloat(total));
        subamount += amount ;
        totalAmount += total;           
        totaltaxrate += ptaxRate;
    });
    $('#subTotal').val(parseFloat(subamount).toFixed(2)); 
    $('#dis_subTotal').val(formatter.format(subamount)); 
    var taxRate = $("#taxRate").val();
    var subTotal = $('#subTotal').val();    
   // console.log( 'tax'+  totaltaxrate +':grand'+totalAmount+'sub'+subamount ); 
   displaytaxes();
    if(subTotal) {
        //var taxAmount = subTotal*taxRate/100;
        //var disType = $('#disType').val();
        var shipping_tax_amount = $('#shipping_tax_amount').val();
        if(taxtype == 'E'){
            var  taxAmount = $('#taxAmount').val();
        }else{
            var  taxAmount = shipping_tax_amount;
        }
        console.log("taxAmount"+taxAmount);
        //$('#taxAmount').val(taxAmount);
        $('#dis_taxAmount').val(formatter.format(taxAmount));
        $('#dis_grandtotal').val(formatter.format(grandtotal));
        //subTotal = parseFloat(subTotal)+parseFloat(taxAmount);
        var grandtotal = parseFloat(subTotal)+parseFloat(taxAmount);
        var disType = $('#disType').val();
        var disValue = $('#disValue').val();
        var div_amount_total = $('#div_amount_total').val();
        if(disType && disValue) {
            //discounts(); 
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
                //$('#dis_discount').val(formatter.format(disvalue2));
                $('#dis_discount').val(formatter.format(disValue1));
                $('#dis_grandtotal').val(formatter.format(grandtotal));
                //amountpaid();
            } 
        }  
        var shipping_amount = $('#shipping_amount').val();
        console.log('123'+grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
        if(shipping_amount != '' && shipping_tax_amount !='0'){
            console.log(grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
            //grandtotal = grandtotal+parseFloat(shipping_amount).toFixed(2)+parseFloat(shipping_tax_amount).toFixed(2);
            grandtotal = parseFloat(grandtotal)+parseFloat(shipping_amount);
            //console.log("grandtotal"+grandtotal);
        }
        $('#gdam').val(grandtotal);
        $('#grandtotal').val(grandtotal);
        //$('#dis_grandtotal').val(Intl.NumberFormat().format(grandtotal));
        $('#dis_grandtotal').val(formatter.format(grandtotal));
         
         //changeexchange();  
              
       // amountpaid(); 
    }
 }
/* function discounts1(){
    if(disType && disValue && totalAftertax) {      
        var disValue1 = 0;
        if(disType=='P'){
            disValue1 = (disValue/100)*totalAftertax;
        }else if(disType=='A'){
            disValue1 = disValue;
        }
        var subTotal = $('#subTotal').val(); 
        for(var i=1; i< count+1; i++) { 
            var id = i;
            var tax = $('#tax_'+id).val();
            //var tax_amount = $('#ptax_amount_'+id).val();
            var amount = $('#amount_'+id).val();
            dicamount = amount-(disValue1/subTotal)*amount;
        }
    }

 }*/
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
    var change_state =  $('#customer_place_of_supply').val();
    var old_state =  $('#old_place').val();
    var taxtype = $('#taxtype').val();
   //var charr = [];
    var check_arr = [];
    var arr2 = [];
    //console.log(old_state+"sjjs"+change_state);
    for(var i=1; i< count+1; i++) { 
        //console.log("1");
        var id = i;
        //var tax = $('#tax_'+id).val();
        var tax = $('#tax_'+id).val();
        var amount = $('#amount_'+id).val();
        if(disValue1 > 0){
            console.log("mann snani");
            console.log(disValue1);
            var dis_val = (parseFloat(disValue1)/parseFloat(subTotal))*parseFloat(amount);
            amount = parseFloat(amount)-parseFloat(dis_val);
            console.log(amount);
            div_amount_total=+amount;
            var ptaxRate = 0;
            var inclusive_tax = 0;
            if(tax != '0' && taxtype != 'O'){
                if(taxtype == 'I'){
                    inclusive_tax = parseFloat(tax);
                }
                var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
                var tax_cal1 = tax/tax_cal;
                console.log("taxcal:"+tax_cal1);
                /*if(taxtype == 'I'){
                    console.log("sad");
                    //amount = parseFloat(amount)-parseFloat(disValue1);
                    amount = parseFloat(amount).toFixed(2);
                }*/
                ptaxRate = tax_cal1*amount;
                //ptaxRate =(parseFloat(tax)/(100+tax))*amount;
                //ptaxRate =((parseFloat(tax)/(100+inclusive_tax)))*amount; 
                //console.log("Ravi2"+ptaxRate);
            }
            $('#ptax_amount_'+id).val(ptaxRate);
            $('#p_amount_'+id).val(amount);
            $('#pdiscount_amount_'+id).val(ptaxRate);
            var tax_amount = ptaxRate; 
        }else{
            var tax_amount = $('#ptax_amount_'+id).val();
            tax_amount = parseFloat(tax_amount);
            div_amount_total=+amount;
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
                   // console.log("RAVi789");
                   // console.log(JSON.stringify(arr2[j]));
                   if(arr2[j]['id'] == tax){
                        //console.log("RAVio098");
                        //console.log(arr2[j]);
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
        /*str+= "<tr><td style='width: 67%;text-align:right;'> CGST @ "+shipping_tax+"% on "+shipping_amount+"</td>";
        str+= "<td>  <input  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+sss+"''> </td></tr>";
*/
    }
    var totaltax = 0;
    for( var k =0 ; k < arr2.length; k++){
            
            if(old_state != change_state){
                str+= "<tr><td style='width: 67%;text-align:right;'> IGST @ "+arr2[k]['id']+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"''> </td></tr>";
            }else{
                str+= "<tr><td style='width: 67%;text-align:right;'> CGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
                 str+= "<tr><td style='width: 67%;text-align:right;'> SGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                str+= "<td>  <input  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
            }

        totaltax+=parseFloat(arr2[k]['tax_amount']);
    }
   console.log("Hello world!");
   console.log(arr2);
    //console.log(str);
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
            //amountpaid();
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
   // alert('hai');
    var quantity  = $('#quantity_'+id).val();
    var quantity_old = $('#quantity_old_'+id).val();
    //alert(quantity+'|||||'+quantity_old);
    if (parseFloat(quantity_old) < parseFloat(quantity)){
        //alert('data');
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

  //  alert(quantity+"|||"+quantity_old);
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
                                /*document.getElementById('quantity_1').focus();*/
                                calculateTotal();
                            }
                        });                             
                    }
                },
                error:function (){}
            });
        }
 }
 function changestatus(val){
     $('#status_values').val(val);

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
<form class="kt-form kt-form--label-right" name="myForm" action="" method="post" enctype="multipart/form-data"  onsubmit="return validateForm()">
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
         <?php //$table1['originator'] =1;
            $nmm= ViewStatusValue($table1['current_status']); 
            $lists = toGetStatusProgressAction($table_name, $table1['current_status']); 
             
              $appruser_list_data = togetaprovaluserlist($table_name, $rid);
                //print_r($appruser_list_data); exit;
                $appruser_list = $appruser_list_data['user'];
                $appruser_data =  $appruser_list_data['details'];
            if($_SESSION['userid'] != $table1['originator']){
                //echo "ravi";
                $small_sub = 0;
               
               // print_r($appruser_list_data); exit;
                if(!in_array($_SESSION['userid'], $appruser_list)){
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
                 //$commentss[] = $appruser_data[$_SESSION['userid']];
                $commentss = $appruser_data;
                if($table1['current_status'] == ST_ACTIVE || $table1['current_status'] == ST_DRAFT || $table1['current_status'] == ST_SEND_BACK || $table1['current_status'] == ST_QUOTATION_GENERATED || $table1['current_status'] == ST_PURCHASE_ORDER_GENRATED){
                   $lists = $lists;
                }else{
                     $lists = array();
                }
            }
               //print_r($commentss);
               /* $lists = json_decode($listjs,1);*/ ?>
        <div class="btn-group">
            <button class="btn btn-primary btn-sm dropdown-toggle" style="background: #<?php echo $nmm['background_color']; ?>;border: #<?php echo $nmm['background_color']; ?>; color:#<?php echo $nmm['color']; ?>  " type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $nmm['name']; ?>
            </button>
            <?php if(count((array)$lists)>0){ ?>
            <div class="dropdown-menu">

                <?php foreach ($lists as $list) {  ?> 
                <button class="dropdown-item" onclick="changestatus(this.value)" name="status" id="status" value="<?php echo $list['status']; ?>"><?php echo $list['action_label']; ?></button>
                <?php  } ?>
                <!-- <a class="dropdown-item" href="#">Invoice</a> --> 
            </div>
           <?php  } ?>
        </div>
       <!--  <div class="btn-group" style="padding: 15px;">
            <select class="form-control kt-select2" id="status" name="status">
                <?php foreach ($lists as $list) {  ?> 
                    <option  value="<?php echo $selectvalue['name_value']; ?>"><?php echo $list['action_label']; ?></option>
                <?php  } ?>
            </select>
          </div> -->
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
//if($table1['qo_number'] !=''){
   $tran_no =  $table1['qo_number'];
//}
 ?>
<div class="form-group col-lg-2">
    <label>Transaction Number</label>
    <div class="input-group">
        <input class="form-control" type='hidden' id="ord_no" value='<?php echo $invoicenumber; ?>' name="invoice_serial_number"/>
    <input  class="form-control" readonly type='text' id="inv_no" value='<?php echo $table1['transaction_number']; ?>'   name="invoice_number" id="invoice_number" />

    </div>
</div>

<div class="form-group col-lg-2">
    <label class="">Transaction Date</label>
    <div class="input-group date">
        <?php $from = fromDate($table1['qo_date']); ?>
        <input  class="form-control" type='text' onblur="duedate()" autocomplete="off" value='<?php  echo $from; ?>' name="invoiced_at" id="kt_datepicker_4_3"  />
        <div class="input-group-append">
            <span class="input-group-text"> <i class="la la-calendar"></i></span>
         </div>
    </div>
</div>

<div class="form-group col-lg-4">
    <label class="">Originator <span class="requried">*</span></label>
    <select class="form-control" id="customer_id" name= "customer_id" onchange="GetSelectedTextValueEmployee(this.value)" >
            <option value="0"> Select Any EMployee </option>
            <?php foreach ($cusresponse as $key => $cus) {  ?>
            <option  <?php if($table1['customer_id'] == $cus['id']){ ?> selected  <?php } ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['first_name']," ".$cus['middle_name']."".$cus['last_name']; ?> 
            </option>
        <?php } ?>
    </select>
    <div style="display: none;color: red;" id="<?php echo 'customer_id_err'; ?>"> Please Select Employee  </div> 
</div>
<?php if($table_name == 'requisitions'){ ?>
<div class="form-group col-lg-4">
    <label> Cost Center <span class="requried">*</span></label>
    <select class="form-control" id="cost_center" name="cost_center">
            <option value="0"> Select any  </option>
            <?php foreach ($costs as  $k1 =>$pos) {  ?>
                <optgroup label="<?php echo $k1; ?>">
                
                <?php foreach ($pos as $k2=> $pos1) {  ?>
                <option  <?php if($table1['cost_center'] == $k2){ ?> selected  <?php } ?>  value ="<?php echo $k2; ?>"> <?php echo $pos1; ?> </option>
            <?php } ?>
            </optgroup>
        <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'cost_center_err'; ?>"> Please enter Cost Center  </div> 
</div> 
<div class="form-group col-lg-4">
    <label class="">Vendor <span class="requried">*</span></label>
    <select class="form-control" id="customer_id" name= "customer_id" onchange="GetSelectedTextValue(this.value)" >
            <option value="0"> Select Any Vendor </option>
            <?php foreach ($cusresponse as $key => $cus) {  ?>
            <option  <?php if($table1['customer_id'] == $cus['id']){ ?> selected  <?php } ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?> 
            </option>
        <?php } ?>
    </select>
     <div style="display: none;color: red;" id="<?php echo 'customer_id_err'; ?>"> Please Select Vendor  </div> 
</div>

<?php } ?>
<div class="form-group col-lg-4">
    <label>Name</label>
    <input readonly class="form-control"  type='text' id="customer_name" name="customer_name"    value="<?php echo $table1['customer_name']; ?>" />
     <input readonly class="form-control"  type='hidden' id="cost_center" name="cost_center"    value="<?php echo $table1['cost_center']; ?>" />

</div>

<div class="form-group col-lg-2">
    <label>Tax Number</label>
    <div class="input-group">
        <input type='text' id="customer_tax_number" name="customer_tax_number" class="form-control" readonly value="<?php echo $table1['customer_tax_number']; ?>" />
    </div>
</div>

<div class="form-group col-lg-2">
    <label class="">Phone</label>
    <div class="kt-input-icon kt-input-icon--right">
        <input type='text' id="customer_phone" name="customer_phone" class="form-control" readonly value="<?php echo $table1['customer_phone']; ?>" />
        <input type='hidden'  name="company_id" class="form-control" readonly value="<?php echo $_SESSION['company_id']; ?>" />
    </div>
</div>

<div class="form-group col-lg-4">
    <label class=""> Mail To</label>
    <input type='text' class="form-control" id="customer_email" readonly name="customer_email"  value="<?php echo $table1['customer_email']; ?>"/>
</div>

<div class="form-group col-lg-4">
    <label>Billing Address</label>
    <div class="kt-input-icon kt-input-icon--right">
        <textarea type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Enter your address"><?php echo $table1['customer_address'] ?></textarea>
        
    </div>
</div>

<div class="form-group col-lg-4">
    <label>Shipping Address</label>
    <div class="kt-input-icon kt-input-icon--right">
        <textarea type="text" class="form-control" id="customer_ship_adderss" name='customer_ship_adderss' placeholder="Enter your address"><?php echo $table1['customer_address'] ?></textarea>
        
    </div>
</div>

    <?php $table1['place_of_supply'] = $table1['place_of_supply'] !='' ? $table1['place_of_supply'] : $location_state; ?>
    <div style="float: left;width: 33%;padding: 3px;">
        <label class="">Place Of Supply <span class="requried">*</span></label>
        <div class="kt-input-icon kt-input-icon--right" >
            <select class="form-control" name="place_of_supply" id="customer_place_of_supply" onchange="displaytaxes()">
            <option value="0"> Select Any Place </option>
            <?php foreach ($posresponse as $key => $pos) {  ?>
                <option   <?php if($table1['place_of_supply'] == $pos['id']){ ?>selected <?php } ?>  value ="<?php echo $pos['id']; ?>" > <?php echo $pos['state_name']; ?> </option>
            <?php } ?>
            </select>

            <input type="hidden" name="old_place" id="old_place" value="<?php echo $location_state;?>">
             <div style="display: none;color: red;" id="<?php echo 'customer_place_of_supply_err'; ?>"> Please enter Place Of Supply</div> 
        </div>
    </div>



 <?php
    //print_r($_SESSION); exit;
        $table1['currency_id'] =  $table1['currency_id'] !="" ? $table1['currency_id'] : $_SESSION['config']['default_currency_id'];?>
<div  class="form-group col-lg-3">
    <label class="">Currency</label>
    <div class="kt-input-icon kt-input-icon--right" >
        <select class="form-control" onchange="currencychange(this.value,<?php echo  $_SESSION['config']['default_currency_id'];  ?>)"  name="currency_id" id="currency_id">
        <option value="0"> Select Any Currency </option>
        <?php
        foreach ($cr_data as  $crpos) {  ?>
            <option  <?php if($table1['currency_id'] == $crpos['id']){ ?> selected  <?php } ?>   value ="<?php echo $crpos['id']; ?>" > <?php echo base64_decode($crpos['currency_list']); ?> </option>
        <?php } ?>
        </select>
        <input type='hidden' class="form-control" id="config_currency_id"  name=""  value="<?php echo $_SESSION['config']['default_currency_id']; ?>"/>
    </div>
</div>

<div class="form-group col-lg-1">
    <label class="">Ex Rate</label>

    <input type='text' class="form-control" id="currency_rate" onchange="exchangerate()"  name="currency_rate"  value="<?php echo customeummarmonyformate($table1['currency_rate']); ?>"/>
    <?php $orgi = $table1['originator'] != "" ? $table1['originator'] : $_SESSION['userid']; ?>
    <input type="hidden" name="originator" value = <?php echo $orgi; ?> > 
</div>



<div class="form-group col-lg-4">
    <div style="float: left;width: 50%;padding: 3px;">
        <label class="">Payment Terms</label>
        <div class="kt-input-icon kt-input-icon--right">
             <select class="form-control"  name="payment_terms" onchange="duedate()" id="customer_payment_terms">
                <option value="0"> Select Any Term </option>
            <?php foreach ($posresponsept as $key => $pos) {  ?>
                <option   <?php if($table1['payment_terms'] == $pos['genc_detail_code']){ ?>selected <?php } ?>  value ="<?php echo $pos['genc_detail_code']; ?>" > <?php echo $pos['genc_detail_name']; ?> </option>
            <?php } ?>
            </select>
        </div>
    </div>

    <div style="float: left;width: 50% ;padding: 3px;">
        <label>Due Date:</label>
          <?php $duee = $table1['due_date'] !='' ?  fromDate($table1['due_date']) :  ''; ?>
        <input type='text' class="form-control" id="due_date"   name="due_date"  value=" <?php  echo $duee; ?>" readonly />
    </div>
</div>  
<div class="form-group col-lg-4">
     <label class="">Payment Method</label>
        <div class="kt-input-icon kt-input-icon--right">
             <select class="form-control"  name="payment_method" id="payment_method">
                <option value="0"> Select Any Payment Method </option>
            <?php foreach ($paymentmethod_data as $method) {  ?>
                <option   <?php if($table1['payment_method'] == $method['genc_detail_code']){ ?>selected <?php } ?>  value ="<?php echo $method['genc_detail_code']; ?>" > <?php echo $method['genc_detail_name']; ?> </option>
            <?php } ?>
            </select>
        </div>
</div> 
<!-- <div class="form-group col-lg-4">
    <div style="float: left;width: 50% ;padding: 3px;">
        <label>Document Ref No:</label>
        <input type='text' class="form-control" id="doc_ref_no"   name="doc_ref_no"  value="<?php  echo $table1['doc_ref_no']; ?>"  />
    </div>

    <div style="float: left;width: 50% ;padding: 3px;">  
        <label>Document Ref Date:</label>
        <div class="input-group date">
        <?php $doc = $table1['doc_ref_date'] !='' ? fromDate($table1['doc_ref_date']) :  ''; ?>
        <input  class="form-control" type='text' onblur="duedate()" autocomplete="off" value='<?php  echo $doc; ?>' name="doc_ref_date" id="kt_datepicker_31"  />
        <div class="input-group-append">
            <span class="input-group-text"> <i class="la la-calendar"></i></span>
         </div>
    </div>
    </div>
</div>  -->

</div></div>
<!--End::Page frist part-->

<!--begin::Page second part -->

<div class="kt-portlet__head">
        <div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title"> Category</h3></div>
    <div class="kt-portlet__head-label" style="float: right;">
         <div class="form-group col-lg-12">
            <select class="form-control" id="taxtype" name= "tax_type" onchange="calculateTotal()" >
                    <option  <?php if($table1['taxtype'] == 'E'){ ?>selected <?php } ?> value="E"> Exclusive </option>
                    <option  <?php if($table1['taxtype'] == 'I'){ ?>selected <?php } ?> value="I"> Inclusive </option>
                    <option  <?php if($table1['taxtype'] == 'O'){ ?>selected <?php } ?> value="O"> Out Of scope </option>
            </select>
        </div>
     </div>
</div>
<div class="kt-portlet__body">
<div class="form-group row">
<div class="col-lg-12">
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="invoiceItem">   
<tr>
    <th class="thheader" width="2%"> 
            <label class="kt-checkbox">
                <input id="checkAll"  type="checkbox">
                <span></span>
            </label>
    </th>
    <th class="" width="16%">Category <span class="requried"></span></th>
    <th class="" width="20%">Description</th>
    <th class="thheader" width="8%">Amount <span class="requried">*</span></th>
    <th class="thheader" width="8%">Tax <span class="requried">*</span></th>
    <th class="thheader" width="8%">Doc Ref No <span class="requried">*</span></th>
    <th class="thheader" width="8%">Doc Ref Date <span class="requried">*</span></th>
   
</tr>    
<?php if(count((array)$table2)>0){ $i=1; 
 foreach ($table2 as $tab2) { //print_r($tab2); ?>
<tr>
    <td>
        <label class="kt-checkbox"><input class="form-control itemRow" type="checkbox"> <span></span> </label>
    </td>

    <td id='other_<?php echo $i; ?>'> 
        <?php if($tab2['item_id'] >= 0){ ?>
        <select class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> Select Any Category  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php } ?> value = <?php echo $item['id']; ?>   > <?php echo $item['expense_name']; ?> 
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
        <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
    <?php }else { ?>
        <input type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
        <input type="hidden" name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_id']; ?>" autocomplete="off">
    <?php } ?>
    </td>
    <td>
        <textarea  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
        <input type="hidden" name="account_code[]" id="account_code_<?php echo $i; ?>" class="form-control"  autocomplete="off">
    </td>
    <td>
        <input type="hidden" name="quantity[]" id="quantity_<?php echo $i; ?>"    class="form-control amount" value="1"  autocomplete="off"> 
        <input type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">

        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
    </td>
    <td>
        <select class="form-control"  name= "tax[]" id="tax_<?php echo $i; ?>"  >
            <option value="0"> Select Any Tax Rate </option>
            <?php foreach ($taxres as $key => $tax) {  ?>
            <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
            <?php } ?>
        </select>
        <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
        
        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input type="hidden" name="dis_amount[]" readonly id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>" autocomplete="off">
    </td>
    <td>
         <input type="text" name="doc_ref_no[]"  id="doc_ref_no_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['doc_ref_no']; ?>" autocomplete="off">
    </td>
    <td>
        <div class="input-group date">
            <input type="text" class="form-control"  value="<?php echo $tab2['doc_ref_date']; ?>"  name="doc_ref_date[]"  id="kt_datepicker_3<?php echo $i; ?>" />
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="la la-calendar"></i>
                </span>
            </div>
        </div>
    </td>
</tr>

<?php $i++; } } ?>                      
    </table>
</div>
</div>
<!--END::Page second part -->
<!--begin::Page third part -->
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
            <button class="btn btn-success" id="addRows" type="button">+ Add More</button>
        </div>
    </div>
    <div style="float: left;width: 50%; padding: 15px;">
       <div>
            <div style="float: left;width: 48%;padding: 1%;">
                <label class="">Internal Note(s) </label>
                <textarea style="height: 120px;" name="notes" class="form-control" ><?php echo $table1['notes']; ?></textarea>
            </div>
            <div style="float: left;width: 48%;padding: 1%;">
                <label class="">Printing Note(s) </label>
                <textarea style="height: 120px;" name="printing_notes" class="form-control" ><?php echo $table1['printing_notes']; ?></textarea>
            </div>
        </div>
          <?php if($small_sub == 0  ){ ?>
            <div style="margin-top: 10px;clear:both;">
                <label class="">Comment(s)</label>
                <textarea style="height: 120px;" name="comments" class="form-control" id="comments" ><?php echo $comments; ?></textarea>
            </div>
            <div class="btn-group" style="margin-top: 10px;float: right;">
                     <button id="kt_llogin_signup_submit"  class="btn btn-brand" >Submit</button>
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
                            foreach ($commentss as  $lists) { //print_r($vdvalue); ?>
                            <div class="kt-timeline-v2__item">
                                <!-- <span class="kt-timeline-v2__item-time"> </span> -->
                                <div class="kt-timeline-v2__item-cricle">
                                    <?php if($lists['response_type'] == '3'){ ?>
                                    <i class="fa fa-genderless kt-font-success"></i>
                                    <?php } else{ ?>
                                    <i class="fa fa-genderless kt-font-warning"></i>
                                    <?php } ?>
                                </div>
                                <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                    <b> <?php echo date("M d, Y g:i A", strtotime($lists['response_date'])); ?></b>
                                    <?php echo " ".$_SESSION['users'][$lists['user_id']]." with Status "; ?><span style="color: <?php echo "#".$stausresult[$lists['response_type']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$lists['response_type']]['background_color']); ?>;" class="kt-badge  kt-badge--inline kt-badge--pill"><?php if($lists['response_type'] == 0){ echo "Yet to respond"; }else{ echo $stausresult[$lists['response_type']]['name']; }?></span>
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
            <th class="amount"><span>Sub Total</span></th>
            <td><span><input  type="text" class="form-control amount" readonly value="<?php echo customeummarmonyformate($table1['sub_total']); ?>" name="dis_subTotal" id="dis_subTotal" placeholder="Subtotal" >
            <input  type="hidden" class="form-control amount" readonly value="<?php echo $table1['sub_total']; ?>" name="subTotal" id="subTotal" placeholder="Subtotal" ></span></td>
        </tr>
         <tr>
            <th class="amount">
                <div> 
                    <div style="float: left;width: 33%;padding: 10px;">
                        <span >Discount </span>
                    </div>
                    <div style="float: left;width: 33%;padding: 3px;">  
                        <select class="form-control" id="disType" name="discount_type" onchange="calculateTotal()">
                            <option value="P">Percentage</option>
                            <option <?php if($table1['discount_type'] == 'A'){ ?> selected <?php } ?> value="A">Amount</option>
                        </select>
                    </div>
                    <div style="float: left;width: 33%;padding:3px;" >
                        <input value="<?php echo number_format($table1['discount_value'],2); ?>" type="text" class="form-control amount" placeholder="value" name="discount_value" id="disValue" >
                        
                    </div>
                </div>  
            </th>
            <td><span >
                <input value="<?php echo customeummarmonyformate($table1['discount_amount']); ?>"  readonly type="text" class="form-control amount" name="dis_discount_amount" id="dis_discount"  placeholder="Discount Amount">
                 <input value="<?php echo $table1['discount_amount']; ?>"  readonly type="hidden" class="form-control amount" name="discount_amount" id="discount" placeholder="Discount Amount">
            </span></td>
        </tr>
        <tr>
            <th class="amount">
                <div> 
                    <div style="float: left;width: 50%;padding: 10px;">
                        <span >Shipping</span>
                    </div>
                    <div style="float: left;width: 50%;padding: 3px;">  
                        <select class="form-control"  name="shipping_tax" id="shipping_tax" onchange="calculateTotal()" >
                            <option value="0"> Select Any Tax Rate </option>
                            <?php foreach ($taxres as $key => $tax) {  ?>
                            <option  <?php if($table1['shipping_tax'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>  
            </th>
            <td><span >
                <input type="hidden" name="shipping_tax_amount" id="shipping_tax_amount" value="0">
                <input  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="shipping_amount" onblur="calculateTotal()" placeholder="shipping Amount">
            </span></td>
        </tr>
        <!-- <tr>
            <th class="amount"><span >Tax Amount </span></th>
            <td><span >
                <input  value="<?php echo customeummarmonyformate($table1['tax_amount']); ?>" type="text" class="form-control amount" name="dis_taxRate" id="dis_taxAmount" readonly placeholder="Tax Amount">
                
               
            </span></td>
        </tr> -->
        <tr>
            <td colspan="2" >
                <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
                    <tbody id='display_tax'>
                        <?php  if(count((array)$taxes)){
                        foreach ($distax as $ktax) { ?>
                        <?php if($table1['tax_state'] == 'S'){ ?>
                        <tr>
                            <td style="width: 67%;text-align:right;">  CGST @ <?php echo customeummarmonyformate($ktax['percentage']/2); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']/2); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                        <tr>
                            <td style="width: 67%;text-align:right;">  CGST @ <?php echo customeummarmonyformate($ktax['percentage']/2); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']/2); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                    <?php } else{?> 
                        <tr>
                            <td style="width: 67%;text-align:right;">  IGST @ <?php echo customeummarmonyformate($ktax['percentage']); ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?> :               </td>
                            <td>  <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']); ?>" type="text" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            </td>
                        </tr>
                    <?php }?>
                        <?php } } ?>
                    </tbody>
                </table>
            </td>
        <tr>
        <tr>
            <th class="amount" ><span>Grand Total</span></th>
             <input  type="hidden" class="form-control amount" placeholder="value" name="gdam" id="gdam">
              <input  type="hidden" class="form-control amount" placeholder="value" name="div_amount_total" id="div_amount_total">
             <input  value="<?php echo $table1['tax_amount']; ?>" type="hidden" class="form-control amount" name="taxRate" id="taxAmount" readonly placeholder="Tax Amount">
            <td><input  value="<?php echo customeummarmonyformate($table1['grand_total']); ?>" type="text" class="form-control amount" name="dis_grandtotal" id="dis_grandtotal" readonly placeholder="Grand Total"><input  value="<?php echo $table1['grand_total']; ?>" type="hidden" class="form-control amount" name="grandtotal" id="grandtotal" readonly placeholder="Grand Total"></td>
        </tr>
        <tr style="display: none;">
            <th class="amount" ><span >Amount Paid</span></th>
            <td><input value="" type="text" class="form-control amount" name="amountPaid" id="amountPaid" placeholder="Amount Paid"></td>
        </tr>
        <tr style="display: none;">
            <td  ><span >Amount Due</span></td>
            <td><input value="" type="text" class="form-control amount" name="amountDue" id="amountDue" placeholder="Amount Due"></td>
        </tr>
        <?php if($table1['currency_id'] != $_SESSION['config']['default_currency_id'] && $table1['currency_id'] != ''){ $css = ""; }else{ $css = 'style="display: none;"'; }?>
        <tr  >
            <td colspan="2" >
                <table  id='exchange' <?php echo $css; ?> class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" style="float: right;">
                    <tr>
                        <!-- <th class="amount">Exchange Rate</th> -->  
                        <th class="amount">Sub total</th>
                        <th class="amount">Tax</th>
                        <th class="amount">Discount</th>
                        <th class="amount">Grand Total </th>
                    </tr>
                    <tr>
                        <!-- <td><input value="<?php echo $table1['currency_rate']; ?>" type="text" readonly  class="form-control amount" name="" id="ex_rate" ></td> -->
                        <td><input value="<?php echo customeummarmonyformate($table1['sub_total']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_sub_total" ></td>
                        <td><input value="<?php echo customeummarmonyformate($table1['tax_amount']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_tax" ></td>
                        <td><input value="<?php echo customeummarmonyformate($table1['discount_amount']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_discount" ></td>
                        <td> <input value="<?php echo customeummarmonyformate($table1['grand_total']*$table1['currency_rate']); ?>" type="text" readonly class="form-control amount" name="" id="ex_grand_total"> </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</div>
</div>
</div>
<div class="form-group row">
    <label class="col-form-label col-lg-3 col-sm-12">&nbsp;</label>
    <input type="hidden" class="form-control" name="file" >
</div>
<!-- <div class="form-group row">
    <label class="col-form-label col-lg-3 col-sm-12">&nbsp;</label>
    <div class="col-lg-4 col-md-9 col-sm-12">
        <div class="dropzone dropzone-default dropzone-brand" id="kt_dropzone_2" >
            <div class="dropzone-msg dz-message needsclick">
                <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                <span class="dropzone-msg-desc">Upload up to 10 files</span>
                <input type="hidden" name="file[]" >
            </div>
        </div>
    </div>
</div> -->
<!--END::Page third part -->
<!--begin::Submit part -->
<?php 
$check_array = array('0', '2', '13');
$disable = ''; if(!in_array($table1['current_status'], $check_array)){ $disable = "disabled" ;} ?>
<div class="kt-portlet__foot">
    <div class="kt-form__actions">
        <div class="row">
            <div class="col-lg-4">
                 <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
                 <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $copy == '1'){ ?>
                        <input type="hidden" id="hid"name="hid" value="<?php echo $rid; ?>" />
                        <input type="hidden" name="hid_itemds" value="<?php echo $item_ids; ?>" />
                        <input type="hidden" name="submit" value="Update" />
                        <button id="kt_llogin_signup_submit" <?php echo $disable; ?> class="btn btn-brand" ><i class="la la-check"></i>Upadate</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } else{ ?>
                        <input type="hidden" name="submit" value="Add" />
                        <button id="kt_login_signin_submit" class="btn btn-brand"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit" class="btn btn-brand"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } ?>  
            </div>
            <div class="col-lg-6">   
            <?php if(count((array)$vdata_details) > 0){ ?>
                <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample2">
                    
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
                                        <?php foreach ($vdata_details as  $vdvalue) { //print_r($vdvalue); ?>
                                        <div class="kt-timeline-v2__item">
                                            <!-- <span class="kt-timeline-v2__item-time"> </span> -->
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
<!--END::Submit part -->
 </div> </div></div>
 </form>
</div></div></div></div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" style="max-width: 800px;">
                <div class="modal-content" style="min-height: 50px;width: 800px;" id="popup1">
                    
                </div>
            </div>

        </div>


<!--  -->