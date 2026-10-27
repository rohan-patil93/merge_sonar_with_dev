<?php
$transaction_date_check = $_GET['TD'] !=""?$_GET['TD']:date('Y-m-d');
$pageurl = API."?mode=gettopupdetails&transaction_table=".$table_name."&transaction_date_check=".$transaction_date_check;
$apicall = callAPI($pageurl);
$result1 = json_decode($apicall,1);

$ddlist = $result1['display_attributes'];
$field_result = $result1['field_result'];
$fieldtype = $result1['display_attributes_type'];
$doc_access_setups_user = $result1['document_access_setups_user'];
$allowcostcenter = $result1['allowcostcenter'];
$allowdivisions = $result1['allow_divisions'];
$allowdepartments = $result1['allow_departments'];
$allowlocations = $result1['allow_locations'];
$allowwarehouses = $result1['allow_warehouses'];
$originator = $result1['originator'];

$content = $ddlist['help_text'] != '' ? $ddlist['help_text'] : "Help Text";
$file_data = json_decode($ddlist['file_upload'],1);
$ddlists = json_decode($ddlist['list_attributes'],1);
$accord = json_decode($ddlist['additional_attributes'],1);
$listsendquery = array('select', 'auto_select');
$image = array(); $insert_arr = array(); $add_insert_arr = array(); $right = array(); $left = array(); $normal = array();
foreach ($ddlists as $key => $value) {
    if($value['insert_display_list']== 1 ){
        if($value['accordions'] == 'L'){
            $left [$value['display_postion']]['lable'] = $value['lable'];
            $left [$value['display_postion']]['type'] = $value['type'];
            $left [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
            $left [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
            $left [$value['display_postion']]['table'] = $key;
        }elseif($value['accordions'] == 'R'){
            $right [$value['display_postion']]['lable'] = $value['lable'];
            $right [$value['display_postion']]['type'] = $value['type'];
            $right [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
            $right [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
            $right [$value['display_postion']]['table'] = $key;
        }else{
            $dis [$value['display_postion']]['lable'] = $value['lable'];
            $dis [$value['display_postion']]['type'] = $value['type'];
            $dis [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
            $dis [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
            $dis [$value['display_postion']]['table'] = $key;
        }
        if($value['mandatory']==1){
            $mandatory[]=$key;
            $mandatorykeywithlabel[]=array("lable"=>$value['lable'],"field"=>$key);
        }
        if($value['type']=='image'){
            $image[] = $key;
        }
        if($value['add_exter'] != 1){
            $insert_arr[]=$key;
        }else{
            $add_insert_arr[]=$key;
        }
        $dis_label[$key] = $value['lable'];
        if(in_array($value['type'],$listsendquery)){
            $retivearray[] = $key;
        }

    }
}
if(count((array)$customer_vendor_gst_billing_details) !="0"){
    foreach ($customer_vendor_gst_billing_details as $jvalue) {
        $field_result['place_of_supply'][$jvalue['id']] = $jvalue['billing_address'];
    }
}

//customer_vendor_gst_billing_details

if(count((array)$dis) > 0){ ksort($dis);  $disarray['normal']=$dis;
}
if(count((array)$right) > 0){ ksort($right);  $disarray['right']=$right;
}
if(count((array)$left) > 0){ ksort($left);  $disarray['left']=$left;
}
$add_mandatory_str = implode(',', (array)$add_insert_arr);
$role = $table1;

if(isset($retivearray) && count($retivearray)){
    $data12 = urlencode(json_encode($retivearray));
    $url123 = API."?mode=togetdyanamicselectdatatablename&table_name=".$table_name."&data=".$data12;
    $retivefiledtabledata = callAPI($url123);
    $fieldtables = json_decode($retivefiledtabledata,1);
}
if(isset($field_result['ref_type']) && ($table_name =="requisitions" || $table_name =="quotations" || $table_name =="purchase_orders" || $table_name =="sales_orders") && $table1['ref_type'] != ""){
    $field_result['ref_id'] = array();
    $wh12["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'current_status'=>ST_APPROVED);
    $wh12['field'] = "id,transaction_number";
    $data12 = urlencode(json_encode($wh12));
    $url12 = API."?mode=gettabledetailswithconditions&table=".$table1['ref_type']."&data=".$data12;
    $data_details12 = callAPI($url12);
    $jsonres = json_decode($data_details12,1);
    foreach ($jsonres as $jvalue) {
        $field_result['ref_id'][$jvalue['id']] = $jvalue['transaction_number'];
    }
}
if($table1['ref_type'] != "" && $table1['ref_type'] == "subcontract_work_order_receipts"){
    $field_result['ref_id'] = array();
    $wh12["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'current_status'=>ST_APPROVED);
    $wh12['field'] = "id,transaction_number";
    $data12 = urlencode(json_encode($wh12));
    $url12 = API."?mode=gettabledetailswithconditions&table=".$table1['ref_type']."&data=".$data12;
    $data_details12 = callAPI($url12);
    $jsonres = json_decode($data_details12,1);
    foreach ($jsonres as $jvalue) {
        $field_result['ref_id'][$jvalue['id']] = $jvalue['transaction_number'];
    }
}
if($table_name =="purchase_invoices" && $table1['ref_type'] != "" || ($table_name =="purchase_invoices" && $table1['ref_id'] !="")){
    $field_result['ref_no'] = array();
    $trrefid = $table1['ref_id']!=""?$table1['ref_id']:$_GET['Rid'];
    $url12 = API."?mode=gerreferancetabledetails&transaction_table=goods_receipt_notes&ref_id=".$trrefid."&current_status=".ST_APPROVED;
    if($copy == 1){
        $url12 .= '&rid='.$rid;
    }
    $data_details12 = callAPI($url12);
    $jsonres = json_decode($data_details12,1);
    $ggrn = $jsonres['result'];
    foreach ($ggrn as $jvalue) {
        $field_result['ref_no'][$jvalue['id']] = $jvalue['transaction_number']." (".$jvalue['doc_ref_no'].")";
    }
}
if($table_name =="sales_invoices" && $table1['ref_type'] != "" || ($table_name =="sales_invoices" && $table1['ref_id'] !="")){
    $field_result['ref_no'] = array();
    $trrefid = $table1['ref_id']!=""?$table1['ref_id']:$_GET['Rid'];
    $url12 = API."?mode=gerreferancetabledetails&transaction_table=delivery_orders&ref_id=".$trrefid;
    if($copy == 1){
        $url12 .= '&rid='.$rid;
    }
    $data_details12 = callAPI($url12);
    $jsonres = json_decode($data_details12,1);
    $ggrn = $jsonres['result'];
    foreach ($ggrn as $jvalue) {
        $field_result['ref_no'][$jvalue['id']] = $jvalue['transaction_number']." (".$jvalue['doc_ref_no'].")";
    }
}
?>
<?php if($table1['payment_term_history'] !="" || $table1['payment_terms'] !=""){ ?>
 <script type="text/javascript">
    window.addEventListener("load", function() {
        setDueDate();

    });
</script>
<?php } ?>

<script type="text/javascript">
    function selectchange(field, key, table){
        var option = '<?php echo $_GET['option']; ?>';
        var main_table = document.getElementById("status_values").value;
        console.log('field:'+field+',key:'+key+',table'+table+',option'+option+',main_table'+main_table);
        if(field == 'party_id'){
                if(table == 'application_users'){
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
                    $.ajax({
                        url: "ajax.php",
                        data:'page=userDetails&user_id='+key,
                        type: "POST",
                        success:function(data){
                            console.log(data);
                            var obj = $.parseJSON(data);
                                document.getElementById('party_name').value=obj.company;
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
                        })
                }
            }
            if(table == 'vendors'){
                $.ajax({
                    url: "ajax.php",
                    data:'page=getdynamicinviceparty&transaction_table=vendors&party_id='+key,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        var resul = $.parseJSON(data);
                        var obj = resul.party;
                        var customer_vendor_gst_billing_details = resul.customer_vendor_gst_billing_details;
                        console.log(customer_vendor_gst_billing_details);
                        document.getElementById('party_name').value=obj.company;
                        document.getElementById('party_email').value=obj.email;
                        document.getElementById('party_phone').value=obj.phone;
                        document.getElementById('party_tax_number').value=obj.gstin;
                        document.getElementById('currency_id').value=obj.currency_id;
                        document.getElementById('printing_notes').value=obj.vendor_po_notes;

                        var party_address = "";
                     if(obj.billing_address != "null" && obj.billing_address != ""){
                        party_address += obj.billing_address;
                     }
                        document.getElementById('party_address').value=party_address;

                     var party_ship_adderss = "";
                     if(obj.shipping_address != "null" && obj.shipping_address != ""){
                        party_ship_adderss += obj.shipping_address;
                     }
                     document.getElementById('party_ship_adderss').value=party_ship_adderss;

                        console.log(obj.payment_terms);
                    $('#payment_terms').select2();
                    $('#payment_terms').val(obj.payment_terms).trigger('change');
                    var placeofsupply = "";
                    console.log(customer_vendor_gst_billing_details.length);
                        $.each(customer_vendor_gst_billing_details, function(i, row) {
                            var selectcheck = "";
                            if(row.is_default == "Y"){
                                var selectcheck = "selected";
                                $("#supplier_state_id").val(row.billing_state);
                                calculateTotal();
                            }
                            placeofsupply += "<option value='"+row.id+"' "+selectcheck+">"+row.billing_address+"</option>"
                        })

                    $("#place_of_supply").html(placeofsupply);
                    try {
                    //start vendor warehouses
                    var request_warehouse_id = "";
                    var vendor_warehouse = resul.vendor_warehouse;
                    $.each(vendor_warehouse, function(i, row) {
                        var selectcheck = "";
                        if(row.default_warehouse == "0"){
                            var selectcheck = "selected";
                        }
                        request_warehouse_id += "<option value='"+row.id+"' "+selectcheck+">"+row.warehouse_name+"</option>"
                    })
                    $("#request_warehouse_id").html(request_warehouse_id);
                    } catch (error) {
                     console.error("Error loading vendor warehouses:", error);
                    }
                    //end vendor warehouses
                    setDueDate();
                    },
                    error:function (){}
                });
                if(key !="" && key !="0"){
                    $("#party_details").show();
                }else{
                    $("#party_details").hide();
                }
            }
            if(table == 'customers'){
                var htmltemp = "";
                $.ajax({
                    url: "ajax.php",
                    data:'page=getdynamicinviceparty&transaction_table=customers&party_id='+key,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        var resul = $.parseJSON(data);
                        var obj = resul.party;
                        document.getElementById('party_name').value=obj.company;
                        document.getElementById('party_email').value=obj.email;
                        document.getElementById('party_phone').value=obj.phone;
                        document.getElementById('party_tax_number').value=obj.gstin;
                        var customer_vendor_gst_billing_details = resul.customer_vendor_gst_billing_details;
                        console.log(customer_vendor_gst_billing_details);

                        if(obj.currency_id !=null){
                            document.getElementById('currency_id').value=obj.currency_id;
                        }
                        var party_address = "";
                     if(obj.billing_address != "null" && obj.billing_address != ""){
                        party_address += obj.billing_address;
                     }
                        document.getElementById('party_address').value=party_address;

                     var party_ship_adderss = "";
                     if(obj.shipping_address != "null" && obj.shipping_address != ""){
                        party_ship_adderss += obj.shipping_address;
                     }
                     document.getElementById('party_ship_adderss').value=party_ship_adderss;

                     var placeofsupply = "";
                        placeofsupply += "<option value=''>Select Any One</option>";

                        $.each(customer_vendor_gst_billing_details, function(i, row) {
                                console.log(row);
                            var selectcheck = "";
                            if(row.is_default == "Y"){
                                var selectcheck = "selected";
                                $("#supplier_state_id").val(row.billing_state);
                                calculateTotal();
                            }
                            placeofsupply += "<option value='"+row.id+"' "+selectcheck+">"+row.billing_address+"</option>";
                        })

                    $("#place_of_supply").html(placeofsupply);

                            if(obj.payment_terms !=null){
                                document.getElementById("party_payment_terms").value = obj.payment_terms;
                            }
                                htmltemp +="<table style="width: 100%;">";
                                htmltemp +="<tr>";
                                htmltemp +="<th scope="col">Credit Limit</th>";
                                htmltemp +="<th scope="col">Total Invoice</th>";
                                htmltemp +="<th scope="col">Avl Limit</th>";
                                htmltemp +="</tr>";
                                htmltemp +="<tr>";
                                htmltemp +="<td>"+obj.credit_limit+"</td>";
                                htmltemp +="<td>"+obj.pending_amount+"</td>";
                                htmltemp +="<td>"+(parseFloat(obj.credit_limit)-parseFloat(obj.pending_amount))+"</td>";
                                htmltemp +="</tr>";
                                htmltemp +="</table>";
                                $("#creditlimit_details").html(htmltemp);
                        },
                        error:function (){}
                    });
                if(key !="" && key !="0"){
                    $("#party_details").show();

                }else{
                    $("#party_details").hide();
                }

            }

            if(main_table == 'sales_invoices'){
                $.ajax({
                    url: "ajax.php",
                    data:'page=dynmictabledata&table=sales_orders&field=id&value=transaction_number&rid=party_id&fid='+key,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        $("#grade").html("<select aria-label="Pay Template" class='form-control' name='pay_template'>"+data+"</select>");
                    },
                    error:function (){}
                });
            }
        }
        if(field == 'ref_id' || field =='ref_no'){


                    var trandate = $("#transaction_date").val();
                    var table_name = $("#table_name").val();
                    var ref_type = $("#ref_type").val();
                    var ref_id = $("#ref_id").val();
                    console.log('field:'+field+',key:'+key+',table'+table+',option'+option+',ref_type:'+ref_type);
                    if(table =="vendor_rate_cards"){
                        window.location.href = 'index.php?option='+option+'&table='+table+'&Rid='+key+'&TD='+trandate;
                    }else if(table_name == "purchase_invoices" && field =='ref_no'){ //grn data get


                        window.location.href = 'index.php?option='+option+'&table='+ref_type+'&Rid='+ref_id+'&TD='+trandate+'&linktable=goods_receipt_notes&linkRid='+key;
                        $.ajax({
                            url: "ajax.php",
                            data:'page=getgrnitems&grn_id='+key,
                            type: "POST",
                            success:function(data){
                                console.log(data);
                                var res = JSON.parse(data);
                                    console.log(res);
                                if(res.result_cnt != 0){
                                    if(document.getElementById("doc_ref_no")){

                                        document.getElementById("doc_ref_no").value = res.main_result['doc_ref_no'];
                                        document.getElementById("doc_ref_date").value = res.main_result['doc_ref_date'];
                                    }
                                    $.each(res.result, function (index, itemData) {
                                        console.log (itemData);
                                        var grn_items_inv_qty = '<th scope="col" class="text-start p-0">Invoice Qty</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_po_<?php echo $i; ?>">'+itemData['invoice_item_quantity']+'</td>';
                                        $("#grn_items_inv_qty_"+itemData['item_id']).html(grn_items_inv_qty);
                                          var grn_items_qty = '<th scope="col" class="text-start p-0">GRN Qty</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" >'+itemData['item_quantity']+'</td>';
                                        $("#grn_items_qty_"+itemData['item_id']).html(grn_items_qty);
                                        $("#item_history_grn_inv_qty_text_"+itemData['item_id']).val(itemData['invoice_item_quantity']);
                                        $("#item_history_grn_qty_text_"+itemData['item_id']).val(itemData['item_quantity']);


                                    });
                                }
                            }
                        });
                    }else if(table_name == "sales_invoices" && field =='ref_no'){ //grn data get


                        window.location.href = 'index.php?option='+option+'&table='+ref_type+'&Rid='+ref_id+'&TD='+trandate+'&linktable=delivery_orders&linkRid='+key;
                        $.ajax({
                            url: "ajax.php",
                            data:'page=getdeliveryorderitems&grn_id='+key,
                            type: "POST",
                            success:function(data){
                                console.log(data);
                                var res = JSON.parse(data);
                                    console.log(res);
                                if(res.result_cnt != 0){
                                    if(document.getElementById("doc_ref_no")){

                                        document.getElementById("doc_ref_no").value = res.main_result['doc_ref_no'];
                                        document.getElementById("doc_ref_date").value = res.main_result['doc_ref_date'];
                                    }
                                    $.each(res.result, function (index, itemData) {
                                        console.log (itemData);
                                        var grn_items_inv_qty = '<th scope="col" class="text-start p-0">Invoice Qty</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" id="item_history_po_<?php echo $i; ?>">'+itemData['invoice_item_quantity']+'</td>';
                                        $("#grn_items_inv_qty_"+itemData['item_id']).html(grn_items_inv_qty);
                                          var grn_items_qty = '<th scope="col" class="text-start p-0">GRN Qty</th><td class="p-0">:</td><td class="p-0" style="text-align: right;" >'+itemData['item_quantity']+'</td>';
                                        $("#grn_items_qty_"+itemData['item_id']).html(grn_items_qty);
                                        $("#item_history_grn_inv_qty_text_"+itemData['item_id']).val(itemData['invoice_item_quantity']);
                                        $("#item_history_grn_qty_text_"+itemData['item_id']).val(itemData['item_quantity']);


                                    });
                                }
                            }
                        });
                    }else if(ref_type !="" && ref_id !=''){
                        window.location.href = 'index.php?option='+option+'&table='+ref_type+'&Rid='+ref_id+'&TD='+trandate;

                    }else if(ref_type !=""){
                        window.location.href = 'index.php?option='+option+'&table='+ref_type+'&TD='+trandate;
                    }else{
                        window.location.href = 'index.php?option='+option+'&table='+table+'&Rid='+key+'&TD='+trandate;
                    }
            }
            if(field == 'ref_type'){
            var trandate = $("#transaction_date").val();
            $.ajax({
                url: "ajax.php",
                data:'page=getdynamicreftype&field='+field+'&key='+key,
                type: "POST",
                success:function(data){
                    var fun ="selectchange('ref_id',this.value,'"+key+"')";
                    $(".refkey_ref_no").html("<select aria-label="Ref" class='form-control' name='ref_id' data-tablename='ref_id'  data-fieldsname='ref_id' data-pagename='comman_form' name='ref_id' id='ref_id' onchange="+fun+">"+data+"</select>");
                    window.location.href = 'index.php?option='+option+'&table='+key+'&TD='+trandate;
                }
            });
        }
        if(field =="currency_id"){
            var originator_currency_id = $("#originator_currency_id").val();
            var trandate = $("#transaction_date").val();
            console.log("originator_currency_id - "+originator_currency_id+" - key - "+key);
            $.ajax({
                url: "ajax.php",
                data:'page=currencyconversionrate&originator_currency_id='+originator_currency_id+'&currency_id='+key+'&trandate='+trandate,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var currncyrate = JSON.parse(data);

                    if(currncyrate.operater == "/"){
                        $("#currency_rate").val(parseFloat(1/currncyrate.rate).toFixed(4));
                    }else{
                        $("#currency_rate").val(parseFloat(currncyrate.rate).toFixed(4));
                    }
                    changeexchange();
                }
            });

        }

        if(field =="payment_terms"){
            setDueDate();
        }
        if(field =="place_of_supply"){
            $.ajax({
                url: "ajax.php",
                data:'page=getselectedlist&field=id&id='+key+'&table_name=customer_vendor_gst_billing_details',
                //data:'page=getselectedlist&field=id&id='+key+_2'&table_name=cities',
                type: "POST",
                success:function(data){
                    console.log(data);
                    var statedetails = JSON.parse(data);

                    $("#supplier_state_id").val(statedetails[0].billing_state);
                    document.getElementById('party_tax_number').value=statedetails[0].gstin;
                    var party_address = "";
                 if(statedetails[0].billing_address != "null" && statedetails[0].billing_address != ""){
                    party_address += statedetails[0].billing_address;
                 }
                    document.getElementById('party_address').value=party_address;

                 var party_ship_adderss = "";
                 if(statedetails[0].shipping_address != "null" && statedetails[0].shipping_address != ""){
                    party_ship_adderss += statedetails[0].shipping_address;
                 }
                 document.getElementById('party_ship_adderss').value=party_ship_adderss;

                    calculateTotal();
                }
            });
        }
        if(field =="transaction_location_id"){
            $.ajax({
                url: "ajax.php",
                data:'page=gettransactionwarehouse&field=id&id='+key,
                type: "POST",
                success:function(data){
                        $("#transaction_warehouse_id").html(data);
                    }
                });
        }
        if(field =="originator"){
            onchangedate();
             //        url: "ajax.php",
             //        data:'page=userandcurrencydetails&user_id='+key+'&currency_id='+currency_id+'&trandate='+trandate,
             //        type: "POST",
        }
        var table_name = document.getElementById('table_name').value;
        if(field =="type" && table_name=="vendor_rate_cards" ){
            if(key==3){
                $('#showidsow').show();
                $('#showstrightidsow').hide();
            }else{
                $('#showstrightidsow').show();
                $('#showidsow').hide();
            }


        }

        }
        function setDueDate(){
            var payment_term_id = $("#payment_terms").val();
            var taxamount = $("#taxAmount").val();
            var subtotal = $("#subTotal").val();
            if(payment_term_id !=""){
                var transaction_date = $("#transaction_date").val();
                if(document.getElementById("advance_payment_textbox")){
                    var grandtotal1 = $("#grandtotal").val();
                    var advance_payment_textbox = $("#advance_payment_textbox").val();
                    var grandtotal = parseFloat(grandtotal1)-parseFloat(advance_payment_textbox);
                    console.log("Yes");
                }else{
                    var grandtotal = $("#grandtotal").val();

                    console.log("No");
                }
                $.ajax({
                    url: "ajax.php",
                    data:'page=getpaymentterms&transaction_date='+transaction_date+'&payment_term_id='+payment_term_id+'&grandtotal='+grandtotal+'&taxamount='+taxamount+'&subtotal='+subtotal,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        var result = JSON.parse(data);
                        console.log(result);
                        $("#due_date").val(result.due_date);
                        $("#display_paymentstermsshow").html(result.payment_terms);
                    }
                });
            }else{
                $("#display_paymentstermsshow").html('');
            }
        }

        function amountLimitCheck(grandtotal){
              $("#btndisableck1").prop("disabled", false);
              $("#btndisableck12").prop("disabled", false);

              <?php if(count((array)$doc_access_setups_user) !=0 && $doc_access_setups_user['amount'] !=0){
                  ?>
                  var limitamt = '<?php echo $doc_access_setups_user['amount']; ?>';
           if(parseFloat(limitamt) < parseFloat(grandtotal)){
               $("#btndisableck1").prop("disabled", true);
               $("#btndisableck12").prop("disabled", true);
               swal({
                   title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                   text: "<?php echo $_SESSION['error_message']['9064']; ?> - limit Is "+limitamt,
                   type: "warning",
                   buttonsStyling: false,
                   confirmButtonText: "OK",
                   confirmButtonClass: "btn btn-danger",
               }, function () {

               });
           }
       <?php } ?>
   }
   function onchangedate(){
       var oid = $("#originator").val();
       var trandate = $("#transaction_date").val();
       var option = '<?php echo $_GET['option']; ?>';
       var url = "";
       url +='index.php?option='+option;
       <?php if($_GET['table']){ ?>
           url += '&table=<?php echo $_GET['table']; ?>';
       <?php } ?>
       <?php if($_GET['Rid']){ ?>
           url += '&Rid=<?php echo $_GET['Rid']; ?>';
       <?php }?>
       window.location.href = url+'&TD='+trandate+'&oid='+oid;
   }

</script>
<div class="card-body"  >
    <input type="hidden" id="backdate_limit" value="<?php echo $doc_access_setups_user['backdate_limit']; ?>">
    <input type="hidden" name="" value="<?php echo $table_name; ?> " id="ajax_table_name">
    <input type="hidden" name="mandatory_array" value='<?php echo json_encode($mandatorykeywithlabel); ?>' id="mandatory_array">
    <?php $lle=0; $dateid=1;
    foreach ($disarray as $diskey => $disvalue) {
        if($diskey != 'normal'){ if($diskey == 'right' || !isset($disarray['right'])){ ?>
            <div class="form-group row">
            <?php } ?>
            <div class="col-md-6 col-12 mt-3 mrgn-btm">
                <div class="col-md-10" id="accordionExample6_<?php echo $diskey; ?>">
                    <div class="card">
                        <div class="card-header" id="headingTwo6" style="padding: 10px !important;">
                            <div class="accordion-msg waves-effect waves-dark f-w-600 scale_active collapsed" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseTwo6<?php echo $diskey; ?>"  aria-expanded="true" aria-controls="collapseTwo6<?php echo $diskey; ?>" style="padding: 5px;border-top: 0px;">

                                <?php if($accord[$diskey] !=""){ echo  $accord[$diskey];
} else{ echo "Additional Information-".$lle;
} ?>

                            </div>
                        </div>
                        <div id="collapseTwo6<?php echo $diskey; ?>" class="collapse" aria-labelledby="headingTwo6" data-parent="#accordionExample6_<?php echo $diskey; ?>">
                            <div class="card-body">

                                <?php    }  $div=0; foreach ($disvalue as $key => $fullvalue) {

                                    $key = $fullvalue['table'];
                                    $requiredfield = "";
                                    $selectdata = array();
                                    if($fullvalue['type'] != 'hidden'){
                                    if(in_array($key, (array)$mandatory)){ $requiredfield ="form-validation-class";
}
                                ?>
                                <?php if( $diskey == 'normal' && $div%2==0){  ?>
                                    <div class="form-group row align-items-center">
                                    <?php }
if($diskey != 'normal'){
                                    $class1 = "col-lg-4 col-sm-12 col-form-label";
                                    $class2 ="col-lg-8 col-md-8 col-sm-12".$requiredfield;
                                    ?><div class="form-group row">
                                    <?php }else{
                                    $class1 = "col-lg-2 col-form-label text-lg-right text-capitalize";
                                    $class2 ="col-lg-3 ".$requiredfield;?> <?php
} ?>
                                    <?php if( $diskey == 'normal' && $div%2!=0){  ?>
                                        <div class="col-lg-1"></div>
                                    <?php } ?>
                                    <?php
                                    if($fullvalue['type'] != 'hidden'){
                                        ?>
                                    <span  id="<?php echo "label_".$key; ?>" class="<?php echo $class1; ?>"><?php echo $fullvalue['lable'];  if (in_array($key, (array)$mandatory)){ ?>&nbsp;<span class="text-danger">*</span><?php
} ?></span>
                                    <?php } ?>
                                    <?php
                                    $edfields = array('doc_ref_no','doc_ref_date','payment_terms','cost_center_id','currency_id','currency_rate');
                                    if($copy == 0){
                                        $edfields[] = 'transaction_date';
                                    }
                                    $opp =0;
                                        if(($table1['ref_type'] !="" && $table1['ref_id'] !="" && !in_array($rtable_name,(array)$listtablestat) &&  !in_array($key,$edfields))  && $role[$key] !=""){
                                            $opp =1;
                                            if($key=='party_id' && $role[$key] ==0){
                                                $opp = 0;
                                            }
                                            if($key=='ref_id'){ $opp = 0;
}
                                        }
                                    ?>

                                    <div class="<?php echo $class2; ?> <?php echo $role[$key]; ?>"  style="<?php echo $opp ==1 ? "pointer-events: none; opacity: 0.7;":""; ?>">
                                        <?php if($fullvalue['type'] == 'text' || $fullvalue['type'] == 'read_only' || $fullvalue['type'] == 'serial_no' ){
                                            $read_only="";
                                            if($fullvalue['type'] == 'read_only'){ $read_only= "readonly";
}
if($fullvalue['type'] == 'serial_no'){
                                                $read_only= "readonly";
                                                if($role[$key]==""){ $role[$key] = dyanamicserialnumber($table_name);
}
                                            }

                                            ?>
                                            <input aria-label="Input field"  class="form-control"  type='text' <?php echo $read_only; ?> autocomplete="off" value='<?php  echo  $role[$key]; ?>' id="<?php  echo  $key; ?>" name="<?php echo $key?>" />
                                        <?php }elseif($fullvalue['type']=='auto_select' && $key != 'place_of_supply'){ ?>
                 $selectdata =$field_result[$key];

                 $select_table = $fieldtables[$key];

                 <div class="w-full">
                     <select aria-label="Select option"   class="form-select js-data-example-ajax-Comman-form col-lg-4" <?php echo $read_only; ?>  data-tablename="<?php echo $table_name; ?>"  data-fieldsname="<?php echo $key; ?>" data-value="" data-pagename="comman_form" name="<?php echo $key?>" id="<?php echo $key?>" onchange="selectchange(<?php echo "'".$key."'"; ?>,this.value,<?php echo "'".$select_table."'"; ?>)">
                         <option value=""> Select Any one</option>
                         <?php foreach ($selectdata as $selectkey => $selectvalue) {  if($selectkey == $role[$key] ){ ?>
                             <option  selected value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
                         <?php } ?>
                         <?php } ?>
                     </select>
                 </div>

             <?php }elseif($fullvalue['type']=='select' || $fullvalue['type']=='disable_select' || $key == 'place_of_supply') {  ?>

                 $selectdata = array();
                 if($table_name=="rental_invoices" AND ($key == 'parent_ref_id' || $key == 'ref_id')){
                     if($key == 'parent_ref_id'){
                         $rentval = $role['party_id'];
                     }elseif($key == 'ref_id'){
                         $rentval = $role['parent_ref_id'];
                     }
                     $url1 = API."?mode=gettenantproperty&valu=".$rentval."&type=".$key;
                    $data_details1 = callAPI($url1);
                    $result =  json_decode($data_details1,1);
                    $property_tenant_details = $result['result'];
                    foreach($property_tenant_details as $pod){
                        $selectdata[$pod['id']] = $pod['value'];
                    }
                 }else{
                     $selectdata =$field_result[$key];
                     $select_table = $fieldtables[$key];
                 }
                 if(($table_name=="requisitions"  || $table_name=="quotations" || $table_name=="estimates") AND ($key == 'ref_type' || $key == 'ref_id')){
                     $selectdisabled = "event_disabled";
                 }else{
                     $selectdisabled  = "";
                 }
                 ?>

                 <div class="w-full refkey_<?php echo $key?> <?php echo $selectdisabled; ?>">

                     <select aria-label="Select option" class="form-select js-example-basic-single" <?php if($fullvalue['type']=='disable_select'){ echo "disabled";
} ?>  onchange="selectchange(<?php echo "'".$key."'"; ?>,this.value,<?php echo "'".$select_table."'"; ?>)"   name="<?php echo $key; ?>" id="<?php echo $key; ?>" >
                         <option value=""> Select Any one </option>
                         <?php foreach ($selectdata as $selectkey => $selectvalue) {
                             $disabled = "";
                             if(count((array)$doc_access_setups_user) !=0){
                                 $transaction_type = json_decode($doc_access_setups_user['transaction_type'],1);
                                 if($key == 'cost_center_id' && !in_array($selectkey, (array)$allowcostcenter) && count((array)$allowcostcenter) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'no_caheck' && !in_array($selectkey, (array)$allowdivisions) && count((array)$allowdivisions) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'no_check' && !in_array($selectkey, (array)$allowdepartments) && count((array)$allowdepartments) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'transaction_location_id' && !in_array($selectkey, (array)$allowlocations) && count((array)$allowlocations) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'transaction_warehouse_id' && !in_array($selectkey, (array)$allowwarehouses) && count((array)$allowwarehouses) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'originator' && !in_array($selectkey, (array)$originator) && count((array)$originator) !=0 && $selectkey != $_SESSION['userid']){
                                     $originator[$_SESSION['userid']] = $_SESSION['userid'];
                                     $disabled = "disabled";
                                     continue;
                                 }
                                 if($key == 'type' && !in_array($selectkey, (array)$transaction_type) && count((array)$transaction_type) !=0){
                                     $disabled = "disabled";
                                     continue;
                                 }
                             }
                             ?>
                             <option <?php if($selectkey == $role[$key] || ($selectkey == $_GET['Rid'] && $key == 'ref_id')){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>" <?php echo $disabled; ?>><?php echo $selectvalue; ?></option>
                         <?php  } ?>
                     </select>
                     <?php if($key == 'party_id'){ ?>
                         <span class="cursor-pointer text-primary" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Click To Open Party Details" onclick="getPartyDetails('<?php echo $select_table; ?>')" <?php echo ($role[$key]!="" && $role[$key]!="0")?"":"style='display:none'"; ?> id="party_details" style="pointer-events: auto;font-weight: bold;opacity: 1;" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti ti-timer icon-dark"></i> Party Details</span>
                         <div id="creditlimit_details"></div>

                     <?php } ?>
                     <?php if($fullvalue['type']=='disable_select'){ ?>
            <input type="hidden" name="<?php echo $key?>" value=<?php echo $role[$key]; ?> />
        <?php } ?>
                 </div>
                 <?php $select++;
} elseif($fullvalue['type']=='radio') {  ?>
                     <?php
                     $selectdata =$field_result[$key];
                     ?>
                     <div class="col-9 mt-3">
                         <div class="radio-inline">
                             <?php foreach ($selectdata as $selectkey => $selectvalue) {
                                 $disabled = "";
                                 $checkedbn = "";
                                 if(count((array)$doc_access_setups_user) !=0){
                                     if($key == 'opex_cpex' && $doc_access_setups_user['opex_cpex'] !="" && $doc_access_setups_user['opex_cpex'] != $selectkey){
                                         $disabled = "disabled";
                                     }else{
                                         if($role[$key] == ""){
                                             $checkedbn = "checked";
                                         }elseif($selectkey == $role[$key]){
                                              $checkedbn = "checked";
                                         }
                                     }
                                 }else{
                                     if($selectkey == $role[$key]){ $checkedbn = "checked";
}
                                 }
                                 ?>
                                 <label class="radio"> <input aria-label="Radio option" type="radio"  name="<?php echo $key; ?>" value='<?php echo $selectkey; ?>' <?php echo $disabled; ?> <?php echo $checkedbn; ?>>    <span></span><?php echo $selectvalue; ?></label>
                             <?php } ?>
                         </div>
                     </div>
                 <?php }elseif($fullvalue['type']=='textarea') {    ?>
                     <textarea aria-label="Text area" type="text" class="form-control" id="<?php echo $key; ?>" name="<?php echo $key; ?>" ><?php echo $role[$key];?></textarea>
                 <?php }elseif($fullvalue['type']=='touchspin') { ?>
                     <input aria-label="Kt Touchspin 1" id="kt_touchspin_1" type="text" class="form-control" value="1" id="<?php echo $key; ?>" name="<?php echo $key; ?>" placeholder="Select <?php echo $value; ?>">
                 <?php }elseif($fullvalue['type']=='switch') { ?>
                $selectdata =$field_result[$key];
                $k=1;
                foreach ($selectdata as $selectkey => $selectvalue) {
                    if($k < 3){
                        $one = $k==1 ? $selectvalue : $one;
                        $sen = $k==2 ? $selectvalue : $sen;
                    }else{
                        break;
                    }
                    $k++;
                    } ?>
                    <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--warning">
                        <label>
                            <input type="hidden"   name="<?php echo $key; ?>"  value= "<?php echo $one; ?>">
                            <input aria-label="Checkbox" type="checkbox"  value="<?php echo $sen; ?>"  name="<?php echo $key; ?>" <?php if($selectkey == $role[$key] ){ ?> checked <?php
} ?> >
                            <span></span>
                        </label>
                    </span>
                <?php } elseif($fullvalue['type']=='checkbox') {
                    $selectdata =$field_result[$key];
                    if(is_array($role[$key])){
                        $mulcheck = $role[$key];
                    }else{
                        $mulcheck = json_decode($role[$key],1);
                    }
                      ?>
                    <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
                        <?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
                            <label class="kt-checkbox m-r-5">
                                <input aria-label="Checkbox" type="checkbox"  name="<?php echo $key."[]"; ?>" value= "<?php echo $selectkey; ?>" <?php if(in_array($selectkey, (array)$mulcheck)){ ?> checked <?php
} ?>> <?php echo $selectvalue; ?>
                                <span class="cr">
                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                            </label>
                        <?php } ?>
                    </div>

                <?php } elseif($fullvalue['type']=='multi_select') {  $selectdata =$field_result[$key];
                    if(is_array($role[$key])){ $mulcheck = $role[$key];

                    }else{
                        $mulcheck = json_decode($role[$key],1);
                    } ?>
                    <div class="w-full">
                        <select aria-label="Select option" class="form-control kt-select2" id="kt_select2_3<?php echo $mutiselet; ?>" name="<?php echo $key."[]"?>" multiple="multiple">
                            <option value=""> Select Any one <?php echo $value; ?></option>
                            <?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
                                <option <?php if(in_array($selectkey, $mulcheck)){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
                            <?php  } ?>
                        </select>
                    </div>
                    <?php $mutiselet++;
} elseif($fullvalue['type']=='date_pick') { $date_pick[] = $key; ?>
                        <div class="">
                            <?php
                            $date_pic = ($role[$key]!="0000-00-00" && $role[$key]!="1970-01-01" && $role[$key]!="")?fromDate($role[$key]):"";
                            if($key=="transaction_date"){ $date_pic  = fromDate($date_pic);
}
if($key=="due_date"){ $date_pic  = fromDate($date_pic);
}
                            $onckfunction = "";
                            if($key=="transaction_date"){ $onckfunction = "onchangedate()";
}                ?>
                            <input aria-label="Input field" class="form-control datepicker" type="text" value="<?php echo  $date_pic; ?>" dateformat="d M y" id="<?php echo $key; ?>"  name="<?php echo $key; ?>" onchange="<?php echo $onckfunction;?>" >


                </div>
                <?php $dateid++;
} elseif($fullvalue['type']=='email'){ ?>
                    <input aria-label="Kt Inputmask 9" class="form-control" id="kt_inputmask_9" id="<?php  echo  $key; ?>" type="text" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
                <?php } elseif($fullvalue['type']=='phone'){ ?>
                    <input aria-label="Kt Inputmask 3" class="form-control" id="kt_inputmask_3" type="text"  id="<?php  echo  $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
                <?php } elseif($fullvalue['type']=='currency'){  ?>
                    <input aria-label="Kt Inputmask 7" class="form-control" id="kt_inputmask_7" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
                <?php } elseif($fullvalue['type']=='number' || $fullvalue['type']=='number_type'){ ?>
                    <input aria-label="Kt Inputmask 6" class="form-control" id="kt_inputmask_6" type="text"   name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" />
                <?php } elseif($fullvalue['type']=='password'){ ?>
                    <div class="kt-input-icon">
                        <input aria-label="Password" class="form-control"  type="Password" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" >
                        <span class="kt-input-icon__icon kt-input-icon__icon--right" ><span><i toggle="#password-field" id= "<?php echo $key; ?>" onclick="eywview(this.id)" class="la la-eye" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i></span></span>
                    </div>
                <?php } elseif($fullvalue['type']=='color_pick'){  ?>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-paint-brush" aria-hidden="true"></i></span></div>
                        <input aria-label="Input field" class="form-control jscolor" type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" >
                    </div>
                <?php }  ?>
                <span class="messages"></span>
            </div>

            <?php if($diskey == 'normal' && $div%2==1 ){ ?>
            </div>
        <?php  }
if($diskey == 'normal' && $div%2==0 && $div+1 == count((array)$disvalue)){ ?> </div> <?php
}
if($diskey != 'normal'){ ?> </div> <?php
} ?>
        <?php $div++;
}else{ ?>
<input class="form-control" type="hidden" id="<?php echo $key; ?>" name="<?php echo $key; ?>"  value="<?php echo $role[$key]; ?>" >

     <?php     }
}
if($diskey != 'normal'){?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } $lle++;
} ?>

        <input type='hidden'  name="company_id" id="company_id" class="form-control" readonly value="<?php echo $_SESSION['company_id']; ?>" />
        <input type="hidden" name="location_id" value="<?php echo $table1['location_id']; ?>">

        <input type="hidden" name="old_place" id="old_place" value="<?php echo $location_state;?>">
        <input type="hidden" name="add_mandatory_str" id="validateForm" value="<?php echo $add_mandatory_str; ?>">
        <?php  if($table1['transaction_date'] !=""){ $transaction_date = $table1['transaction_date'];
}else{ $transaction_date = date('Y-m-d');
}  ?>
