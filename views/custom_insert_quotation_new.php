<?php
$checktimeprint =0; //$subt_amount = 0;
if($checktimeprint > 0){
    echo "Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
//echo "<pre>"; print_r($_SESSION['module_setp']);echo "</pre>";
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['dropzone'] = "1";
$_SESSION['apply_js']['from-validation'] = "1";
$option = strtoupper($_GET['option']);
$list = str_replace('INSERT', 'LIST', $option);
if($_POST['submit'] == 'query'){
   //print_r($_POST);  exit;
    $res = Insertqueries($_POST);
     $_POST['submit'] = '';//exit;
    //$_POST = array();
} 
if($_POST['submit'] == 'reply'){
    $res = updatequeries($_POST);
    //$_POST = array();
} 
if(isset($_POST['submit1'])){
    //echo "<pre>";print_r($_FILES); print_r($_POST);echo "</pre>"; 
    
    /**if($_POST['add_mandatory_str'] !=""){
        $add_mandatory_exp = explode(',', $_POST['add_mandatory_str']);
        foreach ((array)$add_mandatory_exp as $key => $value) {
            $add_data[$value] = $_POST[$value];
            if(count((array)$in_date_pic)>0){
                if(in_array($value, (array)$in_date_pic)){
                    $date_at = str_replace('/"', '-', $_POST[$value]);
                    $date_at =  date('Y-m-d', strtotime($date_at));
                    $add_data[$value] = $date_at;
                }
            }
        }
        $_POST['additional_attributes'] = json_encode($add_data,true);
    }**/

    if($_POST['submit1'] == 'Add'){
        $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
        $url = API."?mode=addinvoietype&table=".$_POST['table_name']."&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response2 = json_decode($response,1);
       //echo "<pre>wwwwwwwwwwwwwwwwwwwwwwwww----------"; print_r($response2); echo "</pre>";
    
        if($response) {
            if($_POST['submit_value'] == 'continue'){
                //$LASTID = json_decode($response,1);
                //$nrid = "id:".$LASTID['LASTID'];
                //header("location:index.php?option=".$option."&Rid=".deocdeId($nrid));
                header("location:index.php?option=".$option);
            }else{
                header("location:index.php?option=".$list);
            }
        }
    } //  new item code end
    if($_POST['submit1'] == 'Update'){
        $_POST['updated_by'] = $_SESSION['userid'];
        $_POST['updated_date'] = nowDate();
        $_POST['file_add'] = $_SESSION['file_add'][$_POST['table_name']][$_POST['hid']];
        $_SESSION['file_add'][$_POST['table_name']][$_POST['hid']]= array();
        $url = API."?mode=addinvoietype&table=".$_POST['table_name']."&method=post&type=update";
        $response = file_post_contents($url, $_POST, 'update');
        $fddrfg = json_decode($response,1);
       // echo "<pre>wwwwwwwwwwwwwwwwwwwwwwwww----------"; print_r($fddrfg); echo "</pre>";
        if ($response) {
          //header("location:index.php?option=".$list);
        }
    } //update condition end
} //submit button code end


$originator_tables = array('requisitions', 'petty_cash');
$stock_table = array('stock_requests', 'stock_transfer');
$originator_table_withbill = array('requisitions', 'petty_cash','bills', 'stock_requests');
$table_list = toCreateInvoicePages();
$table_name = $table_list[$option]['table'];
$title =  $table_list[$option]['title'];
$stausresult = statusData($table_name);
$statuse = ViewStatusData();
$table2 = array(array('item_id'=>'0'));
$copy = 0;
$cusdata = $_SESSION['user'];

$geturl = API."?mode=getdynamicquotationdetails&location_id=".$cusdata['location_id'];
$getesponse = callAPI($geturl); 
$result = json_decode($getesponse,1);
//print_r($result);
$cusresponse = $result['application_users'];
$locares = $result['location'];
$dynamic_mapping_tables = $result['dynamic_mapping_tables'];
$posresponse = $result['posresponse'];
$posresponsept = $result['posresponsept'];
$uomlist = $result['uomlist'];
$itemtypelist = $result['itemtypelist'];
$boxlist = $result['boxlist'];
$departmentslist = $result['departments'];
$cr_data = $result['currentcy_data'];
$cost = $result['cost_centersres'];
$locations_data =  $result['location'];
$additional_charges_list =  $result['additional_charges'];
$location_state = $locares['location_state'];
$table1['location_id'] =  $cusdata['location_id'];
$table1['cost_center_id'] =  $cusdata['cost_center'];
$table1['originator'] = $_SESSION['userid'];
if($table1['transaction_location_id']==""){
    $table1['transaction_location_id'] = $cusdata['location_id'];
}
if($table1['transaction_warehouse_id']==""){
    $table1['transaction_warehouse_id'] = $cusdata['default_warehouse_id'];
}
if(in_array($table_name, (array)$originator_table_withbill)){ 
    $table1['party_id'] = $_SESSION['userid'];
    $table1['originator'] = $_SESSION['userid'];
    $table1['party_name'] = $cusdata['first_name'].' '.$cusdata['middle_name'].' '.$cusdata['last_name'];
    $table1['party_email'] = $cusdata['user_email'];
    $table1['party_phone'] =  $cusdata['user_mobile'];
    $table1['party_tax_number'] = $locares['tax_ref'];
    $table1['party_address'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
    $table1['party_ship_adderss'] = $locares['location_address'] ." ".$locares['location_city']." ".$locares['location_state']." ".$locares['location_post_code'];
}else{
    $cusresponse = $result['vendors'];
}
$taxres =  toGetTaxrates();
if(isset($_GET['table']) && $_GET['table'] !=''){
    $table1['ref_type'] = $_GET['table'];
}
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $rtable_name = "";  $stax_amount="0";
    if(isset($_GET['table']) && $_GET['table'] !=''){
        $rtable_name = $_GET['table'] ;
        $rid = $_GET['Rid'];
        if(isset($_GET['linktable']) && $_GET['linktable'] !=''){
        $rtable_name = $_GET['linktable'] ;
        $rid = $_GET['linkRid'];
        }    
    ?>
        <script type="text/javascript">
            window.addEventListener("load", function() {
                setDueDate();
                // setTimeout(function(){ 
                    
                // }, 5000);
            });
        </script>
    <?php
    }else{
        $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $rid = explode(":", $rid);
        $rid= $rid[1];
        $copy = 1;
    }
    $rtable = $rtable_name!='' ? $rtable_name : $table_name;
    $table2 = array(''); $i=0;
    $rpurl =  API."?mode=getInvoicesData&in_id=$rid&table=".$rtable."&copy=".$copy;
    $rp_data = callAPI($rpurl);
    $rpres = json_decode($rp_data, 1);
    //echo "<pre>"; print_r($rpres); echo "</pre>";   
    $rpdata = $rpres['data'];
    $items = $rpres['items'];
   //echo "<input type='hidden' value='".htmlspecialchars(print_r($items, true))."'>";
    $taxes = $rpres['taxes'];
    $files = $rpres['files'];
    $jv_items = $rpres['jv_items'];
    $customer_vendor_gst_billing_details = $rpres['customer_vendor_gst_billing_details'];
   // print_r($rpres['customer_vendor_gst_billing_details']);
    //echo "customer_vendor_gst_billing_details";
    $itemidwithhsn = $rpres['itemidwithhsn'];
    $chart_of_accounts = $rpres['chart_of_accounts'];
    $reftable_items = $rpres['ref_table_details']['items'];
    $debit_notes_cnt = $rpres['debit_notes_cnt'];
    $credit_notes_cnt= $rpres['credit_notes_cnt'];
    $reftableitemlist = array();
    // Added Rohan 2024-01-29
    $additional_charges = json_decode($rpdata['additional_charges'], 1);
    $taxes_ship = $taxes;
    // Added Rohan 2024-01-29
    foreach((array)$additional_charges as $addchrg){
        if($addchrg['shipping_tax']!=0){
            $taxes_ship[] = array('tax_id'=> $addchrg['shipping_tax'],'tax_value'=>number_format($addchrg['shipping_tax'],2),'amount'=>$addchrg['shipping_amount'],'tax_amount'=>$addchrg['shipping_tax_amount']);
        }
    }
    //echo "<pre>"; print_r($taxes_ship); echo "</pre>";
    // End Added Rohan 2024-01-29
    $project_task_details = $rpres['project_task_details'];
    // End Added Rohan 2024-01-29
    if($copy == 0){
        foreach((array)$items as $reftableitems){
            //$reftableitemlist[$reftableitems['item_id']] = $reftableitems;
            $reftableitemlist[$reftableitems['id']] = $reftableitems;
            $reftableitemidlist[] = $reftableitems['item_id'];
            $errormessage[$reftableitems['id']] = $reftableitems;
        }
    }else{
        foreach((array)$reftable_items as $reftableitems){
            $reftableitemlist[$reftableitems['item_id']] = $reftableitems;
            $reftableitemidlist[] = $reftableitems['item_id'];
            $errormessage[$reftableitems['id']] = $reftableitems;
        }
    }
    //  echo "<pre>"; print_r($rpdata); echo "</pre>";
    //echo "<pre>"; print_r($taxes_ship); echo "</pre>";
    /** ROhan Remove list calculate with pendding  **/
    $dutax = array();
    $distax = array();
    foreach ((array)$taxes_ship as  $taxvalue) {
       if(in_array(number_format($taxvalue['tax_value'],2), (array)$dutax)){
           $distax[$taxvalue['tax_value']] ['amount'] =  $distax[$taxvalue['tax_value']]['amount'] + $taxvalue['amount'];
           $distax[$taxvalue['tax_value']] ['tax_amount'] =  $distax[$taxvalue['tax_value']] ['tax_amount'] +$taxvalue['tax_amount'];
       }else{
            $dutax[]= number_format($taxvalue['tax_value'],2);
            $distax[$taxvalue['tax_value']] ['percentage'] =  $taxvalue['tax_value'];
            $distax[$taxvalue['tax_value']] ['amount'] =  $taxvalue['amount'];
            $distax[$taxvalue['tax_value']] ['tax_amount'] =  $taxvalue['tax_amount'];
       }
    }
    //echo "<pre>"; print_r($distax); echo "</pre>";
    // ROhan Remove list calculate with pendding /**/
     
    if($rtable_name != ''){
        $table1['qo_number'] = "";
        $table1['qo_date'] = "";
        $table1['ref_id'] =  $_GET['Rid']!="" ? $_GET['Rid'] : $rid;
        $table1['ref_no'] = $rpdata['transaction_number'];
        $table1['ref_date'] = $rpdata['transaction_date']; 
        $table1['due_date'] = $rpdata['due_date'];
        $table1['ref_type'] = $_GET['table']!="" ? $_GET['table'] : $rtable_name;
        $table1['ref_no'] =  $_GET['linkRid']!="" ? $_GET['linkRid'] : "";
    }else{ 
        $table1['qo_number'] = $rpdata['transaction_number'];
        $table1['qo_date'] =  $rpdata['transaction_date']; 
        $table1['transaction_number'] = $rpdata['transaction_number'];
        $table1['transaction_date'] =  $rpdata['transaction_date'];    
        $table1['due_date'] = $rpdata['due_date'];
        $table1['ref_id'] = $rpdata['ref_id'] ;
        $table1['ref_no'] = $rpdata['ref_no'] ;
        $table1['ref_date'] = $rpdata['ref_date'];
        $table1['ref_type'] = $rpdata['ref_type'];
        $table1['parent_ref_id'] = $rpdata['parent_ref_id'];
        $table1['payment_term_history'] = $rpdata['payment_term_history'];
        $table1['follow_up_level_ids'] = $rpdata['follow_up_level_ids'];
        $table1['advance_payment'] = $rpdata['advance_payment'];
    }
    if($rtable_name != 'requisitions'){ //echo "done11";
        $table1['party_id'] = $rpdata['party_id'];
        $table1['party_name'] = $rpdata['party_name'];
        $table1['party_email'] = $rpdata['party_email'];
        $table1['party_phone'] = $rpdata['party_phone'];
        $table1['party_tax_number'] = $rpdata['party_tax_number'];
        $table1['party_address'] = $rpdata['party_address'];
        $table1['party_ship_adderss'] = $rpdata['party_ship_adderss'];
    }
    if($rid != "" && $copy == 1){
        $table1['originator'] = $rpdata['originator'];
    }
    $table1['transaction_location_id'] = $rpdata['transaction_location_id']!="" ? $rpdata['transaction_location_id'] : $cusdata['location_id'];
    $table1['transaction_warehouse_id'] = $rpdata['transaction_warehouse_id']!="" ? $rpdata['transaction_warehouse_id'] : $cusdata['default_warehouse_id'];
    $table1['request_warehouse_id'] = $rpdata['request_warehouse_id']; 
    $table1['currency_id'] = $rpdata['currency_id'];
    $table1['cost_center'] = $rpdata['cost_center_id'];
    $table1['cost_center_id'] = $rpdata['cost_center_id'];
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
    $listtablestat = array('purchase_orders','goods_receipt_notes');
    if($rtable_name !="" && in_array($rtable_name,$listtablestat)){
        $table1['current_status'] = ST_ACTIVE;
    }
    $table1['notes'] = $rpdata['notes'];
    $table1['printing_notes'] = $rpdata['printing_notes'];
    $table1['shipping_tax'] = $rpdata['shipping_tax'];
    $table1['shipping_amount'] = $rpdata['shipping_amount'];
    $table1['shipping_tax_amount'] = $rpdata['shipping_tax_amount'];
    $table1['taxtype'] = $rpdata['tax_type'];
    $table1['tax_state'] = $rpdata['tax_state'];
    $table1['opex_cpex'] = $rpdata['opex_cpex'];
    $table1['type'] = $rpdata['type'];
    $table1['originator_currency_id'] = $rpdata['originator_currency_id'];
    $table1['supplier_state_id'] = $rpdata['supplier_state_id'];
    $table1['doc_ref_no'] = $rpdata['doc_ref_no'];
    $table1['doc_ref_date'] = $rpdata['doc_ref_date'];
    $table1['cancel_invoice'] = $rpdata['cancel_invoice'];
    $table1['originator_state_id'] = $rpdata['originator_state_id'];
    $table1['supplier_state_id'] = $rpdata['supplier_state_id'];
    $table1['salesmen_id'] = $rpdata['salesmen_id'];
    $table1['total_tds_amount'] = $rpdata['total_tds_amount'];
    $table1['rounding_off'] = $rpdata['rounding_off'];
    $table1['multi_reference'] = $rpdata['multi_reference'];
    $table1['discount_mode'] = $rpdata['discount_mode'];
    $table1['compliance_specification_documents'] = $rpdata['compliance_specification_documents'];
    $table1['security_deposit'] = $rpdata['security_deposit'];
    $table1['advance_payment'] = $rpdata['advance_payment'];
    if(isset($rpdata['additional_attributes']) && $rpdata['additional_attributes']){ //echo "ravi";
        $extara = json_decode($rpdata['additional_attributes'],1);
        foreach ((array)$extara as $extr_key => $extar) {
            $table1[$extr_key] = $extar;
        }
    }
    //echo "<pre>"; print_r($taxes); echo "</pre>";
    foreach ((array)$taxes as  $taxs) {
       $tax_array[$taxs['transaction_item_row_id']] = $taxs['tax_id'];
       $tax_valuearray[$taxs['transaction_item_row_id']] = $taxs['tax_value'];
       $tax_valuenamearray[$taxs['transaction_item_row_id']] = $taxs['tax_name'];
       $tax_valueamountarray[$taxs['transaction_item_row_id']] = $taxs['amount'];  
    }
    foreach((array)$items as $item){
        if(($table_name == 'quotations' || $table_name == 'purchase_orders') && $rtable_name !='' && $table_name != 'purchase_invoices'){
            if(count((array)$reftableitemlist)>0){
               // $subt_amount+=$item['item_price'] * $reftableitemlist[$item['item_id']]['pending_qty'];
                $subt_amount+=$item['item_price'] * $reftableitemlist[$item['id']]['pending_qty'];
            }else{
                $subt_amount+=$item['item_price'] * $item['pending_qty'];
            }
        }
    }

    //echo "rtable_name -- ".$rtable_name." - table_name - ".$table_name;
    $chktxtseq = 1; 
    $duplicateitemscheckary = $duplicateitemsary = array();      
    foreach((array)$items as $item){  
        //dublicate check
        if($duplicateitemscheckary[$item['item_id']] !=""){
            $duplicateitemsary[] = $item['item_name'];
        }
        $duplicateitemscheckary[$item['item_id']] = $item['item_id'];
        //dublicate check

        $itemid[] = $item['item_id'];
        $itemda['transaction_item_row_id']= $item['id'];
        $itemda['ref_transaction_item_table_id']= $rtable_name==""?$item['ref_transaction_item_table_id']:$item['id'];
        $itemda['item_id']= $item['item_id']; 
        $itemda['item_name']= $item['item_name'];       
        $itemda['description']= trim($item['description']);
        $itemda['customer_description']= trim($item['customer_description']);
        $itemda['uom']= $item['purchase_uom'];
        $itemda['quantity']= $item['item_quantity']; 
        $itemda['item_price']= $item['item_price']; 
        $itemda['tax_id']= $tax_array[$item['id']];
        $itemda['tax_value']= $tax_valuearray[$item['id']];
        $itemda['tax_name']= $tax_valuenamearray[$item['id']];
        $itemda['taxabled_amount']=$tax_valueamountarray[$item['id']];
        $itemda['tax_amount']= $item['tax_amount']; 
        $itemda['total_amount']= $item['total_amount']; 
        $itemda['purchase_account']= $item['account_code']; 
        $itemda['req_date']= $item['req_date'];
        $itemda['reason_id']= $item['reason_id'];
        $account_code[] = $item['account_code'];
        $itemda['item_details']= $item['item_details']; // added item details
        $itemda['discount_amount']= $item['discount_amount']; // added discount
        $itemda['invoiced_quantity'] = $item['invoice_item_quantity'] !='' ?$item['invoice_item_quantity'] : $item['item_quantity'];
        
        $itemda['party_sku_number']= $item['party_sku_number']; // added discount
        $itemda['party_sku_name']= $item['party_sku_name']; // added discount
        $itemda['party_sku_concat']= $item['party_sku_concat']; // added discount
        

       if($rtable_name!=""){
        $itemda['damaged_quantity'] = 0;
       }else{
        $itemda['damaged_quantity'] = ($item['invoice_item_quantity']-$item['item_quantity']) !='' ? ($item['invoice_item_quantity']-$item['item_quantity']) : 0;
        }
        if(($table_name == 'quotations' || $table_name == 'purchase_orders' || $table_name == 'purchase_invoices') && $rtable_name !='' && $table_name != 'purchase_invoices'){
            $tax_val = $tax_valuearray[$item['id']] !=''?$tax_valuearray[$item['id']] :0;
            if(count((array)$reftableitemlist) !=0){
                // $itemda['quantity']= $reftableitemlist[$item['item_id']]['pending_qty'];
                // $item['pending_qty'] = $reftableitemlist[$item['item_id']]['pending_qty']; 
                $itemda['quantity']= $reftableitemlist[$item['id']]['pending_qty'];
                $item['pending_qty'] = $reftableitemlist[$item['id']]['pending_qty']; 
            }else{
                $itemda['quantity']= $item['pending_qty']; 
            } 
            $productamount = ($item['item_price'] * $item['pending_qty']);
            if($rpdata['discount_type'] == 'P' && $table1['discount_mode'] != "DI"){
                $newproductamount = $productamount * ($rpdata['discount_value'] /100);
            }
            if($rpdata['discount_type'] == 'A' && $table1['discount_mode'] != "DI"){
                $newproductamount = ($productamount / $subt_amount) * $rpdata['discount_amount'];
            }
            if($table1['discount_mode'] == "DI"){
                $newproductamount = $rpdata['discount_amount'];
            }
            $totaldiscountamt +=$newproductamount;
            $productamount = $productamount-$newproductamount; 
            if($chktxtseq ==1){
                $dutax =$distax = array();
            }
           if(in_array(number_format($tax_val,2), (array)$dutax)){
               $distax[$tax_val] ['amount'] =  $distax[$tax_val]['amount'] + $productamount;
               $distax[$tax_val] ['tax_amount'] =  $distax[$tax_val] ['tax_amount'] +($productamount)*($tax_val/100);
           }else{
                $dutax[]= number_format($tax_val,2);
                $distax[$tax_val] ['percentage'] =  $tax_val;
                $distax[$tax_val] ['amount'] =  $productamount;
                $distax[$tax_val] ['tax_amount'] =  ($productamount)*($tax_val/100);
           }
            //if(isset($distax[$tax_val]['amount'])){ //rohan remove tax part....
                //$distax[$tax_val]['amount'] =  $productamount;
                //$distax[$tax_val]['tax_amount'] = ($productamount)*($tax_val/100);  
            //} //rohan remove tax part....
            $itemda['taxabled_amount'] = $productamount;
            $itemda['tax_amount']= ($productamount)*($tax_val/100);
            $itemda['total_amount']=  $item['item_price'] * $item['pending_qty'];

            //$subt_amount+=$item['item_price'] * $item['pending_qty'];
            $tax_val = $tax_val!=""? $tax_val :0;
            $stax_amount+=($productamount)*($tax_val/100);
        }
        $meantableitemlist[$itemda['ref_transaction_item_table_id']] = $itemda['quantity'];
        $table2[$i] = $itemda; 
        $i++; 
        $chktxtseq++; 

    }
    //echo "<pre>"; print_r($duplicateitemsary); echo "</pre>";
    
    if($subt_amount != ""){
        if($rpdata['discount_type'] == 'P'){
            
            if($subt_amount !="" AND $subt_amount !="0" AND $rpdata['discount_value'] !="" AND $rpdata['discount_value'] !="0"){
                $discount_amt += $subt_amount * ($rpdata['discount_value'] /100);
            }else{
                $discount_amt += 0;
            }
            $table1['discount_amount'] = $discount_amt;
        }
        $table1['sub_total'] = $subt_amount;
        $table1['tax_amount'] = $stax_amount;
        // Added Rohan 2024-01-29
            foreach((array)$additional_charges as $addchrg){
                if($addchrg['shipping_amount'] != '' AND $addchrg['shipping_amount'] != '0'){
                   $shipping_amounttax += $addchrg['shipping_amount'] * ($addchrg['shipping_tax'] /100);
                }
            }
        // End Added Rohan 2024-01-29 
        $shipping_amountt = $rpdata['shipping_amount'] + $shipping_amounttax;
        $table1['grand_total'] = $subt_amount + $shipping_amountt + $stax_amount-$totaldiscountamt;
    } 
    $item_ids = "";
    if(count((array)$itemid) != 0 AND $itemid !=""){
        $item_ids = implode(',', (array)$itemid);
    }
    $acode['account_code']=$account_code;
    $acode['cost_center_id']=$table1['cost_center'];
    $acode['company_id']=$_SESSION['company_id']; 
    $acode['fiscal_year']=$table1['qo_date'];
    //echo "<pre>"; print_r($meantableitemlist); print_r($errormessage); echo "</pre>";
    if(count((array)$meantableitemlist) !=0 AND count((array)$errormessage) != 0){
        foreach((array)$meantableitemlist as $ref_itm_id=>$qytvalue){
            if(isset($errormessage[$ref_itm_id]['pending_qty']) && $qytvalue > $errormessage[$ref_itm_id]['pending_qty'] AND $table1['ref_type'] != 'delivery_challans'){
                $_GET['error_messages'] .= $errormessage[$ref_itm_id]['item_name']." (".$qytvalue." Qty) - ".$_SESSION['error_message'][6030]." - (Qty - ".round($errormessage[$ref_itm_id]['pending_qty']).") Please Check </br>";
            }
        }
    }
}
$_SESSION['application_table_name'] = $table_name;
$_SESSION['transaction_id'] = $rid;
$_SESSION['file_add'][$table_name][$rid]= array();
foreach ((array)$cost as $valu) {
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
foreach ((array)$costress as  $costres) {
    $incost[] = $costres['cost_center_id'];
}
foreach ((array)$cost as $value) {
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
$small_sub = 111;
//////////////////////currency_id//////////////////////////////////
if($table1['currency_id']!="" AND $table1['currency_id']!="0"){
    $table1['currency_id'] = $table1['currency_id'];
    $table1['originator_currency_id'] = $table1['originator_currency_id'];
}elseif($_SESSION['user']['default_currency_id'] !="" AND $_SESSION['user']['default_currency_id'] !="0"){
    $table1['currency_id'] = $_SESSION['user']['default_currency_id'];
    $table1['originator_currency_id'] = $_SESSION['user']['default_currency_id'];
}else{
    $table1['currency_id'] = $_SESSION['config']['default_currency_id'];
    $table1['originator_currency_id'] = $_SESSION['config']['default_currency_id'];
}
//////////////////////currency_id//////////////////////////////////
//////////////////////state//////////////////////////////////
if($table1['originator_state_id']!="" AND $table1['originator_state_id']!="0"){
    $table1['originator_state_id'] = $table1['originator_state_id'];
    $table1['supplier_state_id'] = $table1['supplier_state_id'];
    //echo "wroking --- 1";
}elseif($_SESSION['user']['default_state_id'] !="" AND $_SESSION['user']['default_state_id']!="0"){
    $table1['originator_state_id'] = $_SESSION['user']['default_state_id'];
    
    if($table1['supplier_state_id']!="" AND $table1['supplier_state_id']!="0"){
        $table1['supplier_state_id'] = $table1['supplier_state_id'];
    }else{
        $table1['supplier_state_id'] = $_SESSION['user']['default_state_id'];
    }
   // echo "wroking --- 2";
}else{
    $table1['originator_state_id'] = $_SESSION['user_company']['state'];
    $table1['supplier_state_id'] = $_SESSION['user_company']['state'];
   // echo "wroking --- 3";
}
$table1['currency_rate'] = ($table1['currency_rate'] != "" AND $table1['currency_rate'] !="0")?$table1['currency_rate']:1;  
//////////////////////state//////////////////////////////////
  if(isset($_GET['TD']) AND $_GET['TD'] !=""){
        $table1['transaction_date'] =  $_GET['TD'];    
        $table1['due_date'] =  ($table1['payment_terms'] !="" AND $table1['payment_terms'] !="0")?fromDate(date('Y-m-d', strtotime($_GET['TD'].' + '.$table1['payment_terms'].' days'))):$_GET['TD'];
    }
if($table_name != '' && $rtable_name =='purchase_orders' && $table_name != 'purchase_invoices'){
    $table2 = array();
    foreach((array)$items as $item){
        if($item['pending_qty']>0){
            $itemid[] = $item['item_id'];
            $itemda['item_id']= $item['item_id']; 
            $itemda['item_name']= $item['item_name'];       
            $itemda['description']= trim($item['description']);
            $itemda['customer_description']= trim($item['customer_description']);
            $itemda['uom']= $item['purchase_uom'];
            $itemda['quantity']= $item['pending_qty']; 
            $itemda['item_price']= $item['item_price']; 
            $itemda['tax_id']= $tax_array[$item['id']];
            $itemda['tax_value']= $tax_valuearray[$item['id']];
            $itemda['tax_name']= $tax_valuenamearray[$item['id']];
            $itemda['taxabled_amount']=$tax_valueamountarray[$item['id']];
            $itemda['tax_amount']= ($item['tax_amount']/$item['item_quantity'])*$item['pending_qty'];
            $itemda['total_amount']= ($item['total_amount']/$item['item_quantity'])*$item['pending_qty'];
            $itemda['discount_amount']= ($item['discount_amount']/$item['item_quantity'])*$item['pending_qty'];
            $itemda['purchase_account']= $item['account_code']; 
            $itemda['req_date']= $item['req_date'];
            $itemda['reason_id']= $item['reason_id'];
            $account_code[] = $item['account_code'];
            $itemda['invoiced_quantity'] = $item['pending_qty'];
            $itemda['damaged_quantity'] = 0;            
            $table2[] = $itemda;    
            $sub_total = $sub_total+$itemda['total_amount'];
            $tax_amount = $tax_amount+$itemda['tax_amount'];
            $discount_amount = $discount_amount+$itemda['discount_amount'];        
        }
    }
    $table1['sub_total'] = $sub_total;
    $table1['tax_amount'] = $tax_amount;
    $table1['discount_amount'] = $discount_amount;
    $table1['grand_total'] = ($sub_total+$tax_amount+$table1['shipping_amount']+$table1['shipping_tax_amount'])-$discount_amount;
    $url = API."?mode=tochecktransactionlocationwarehousebefore&trnid=".$rid;
    $checkresponse =  callAPI($url);
    $chkres = json_decode($checkresponse,1);
}
if($checktimeprint > 0){
    echo "DATA Retervied Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
$tax_authorities_rates_split = $rpres['tax_authorities_rates_split'];
// Split Should Come for Tax Autharities split part
if($table1['originator_state_id'] == $table1['supplier_state_id']){ 
    $split_1_name = $tax_authorities_rates_split[0]['split_name'];
    $split_2_name = $tax_authorities_rates_split[1]['split_name'];
    $split_1 = $tax_authorities_rates_split[0]['tax_rate'];
    $split_2 = $tax_authorities_rates_split[1]['tax_rate'];
    $split_1_gl_account_code =  $tax_authorities_rates_split[0]['account_code'];
    $split_2_gl_account_code =  $tax_authorities_rates_split[1]['account_code'];
}else{
    $split_1_name = $tax_authorities_rates_split[2]['split_name'];
    $split_1 = $tax_authorities_rates_split[2]['tax_rate'];
    $split_1_gl_account_code =  $tax_authorities_rates_split[2]['account_code'];
}


$items_chart_of_account_not_provide = $rpres['items_chart_of_account_not_provide'];
$chart_of_account_lock = $rpres['chart_of_account_lock'];


$qoriginatorCurrencyId = $table1['originator_currency_id'] ?? '';
$qcurrencyId = $table1['currency_id'] ?? '';
$qcurrency_rate = $table1['currency_rate'] ?? '';
if ($qoriginatorCurrencyId != '' && $qoriginatorCurrencyId != '0' && $qcurrencyId != '' && $qcurrencyId != '0' && $qoriginatorCurrencyId != $qcurrencyId){
    $currency_conversion_style ='';
}else{
    $currency_conversion_style ='style="display: none;"';
}

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
<script type="text/javascript">
    window.addEventListener("load", function() {
        //setDueDate();
    });
</script>
<script>

var salestablelist = ['enquiries','estimates','sales_orders','delivery_orders','sales_returns','sales_invoices'];    
function duedate(){
    //alert("hai");
    var payment_terms = $('#party_payment_terms').val();
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
           // console.log(response);
        }
    });
}
function currencychange(val,old_val){
    //console.log("currencychange function also work");
    $.ajax({
        url: "ajax.php",
        data:'page=currency_convert&old_val='+old_val+'&val='+val,
        type: "POST",
        success:function(data){
            //console.log(data);
             $('#currency_rate').val(data);
             if(data !=''){
             document.getElementById('exchange').style.display = "block";
             exchangerate();
            }else{
                swal({              
                    title: "6010 : Please Define Exchange Rate",                            
                    type: "warning",           
                    buttonsStyling: false,
                    confirmButtonText: "OK",    
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
    alert('Check');
}
function validateFormold(){
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
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });*/ 
    }else{
     if( x != 2 ){
        
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
        var party_id = document.forms["myForm"]['party_id'].value;
        //alert (party_id);
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
       // alert(res);
         return false;
    }
    }
    /*var data = <?php //echo $mandatory_json; ?>;
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
    //alert(key);

    if(key == '0'){
       // alert('good');
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
       // console.log(data);
        var obj = $.parseJSON(data);
        //alert(obj.payment_terms);
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
    });
    }
}
function GetSelectedTextValue(key) {
    //alert(key);
    if(key == '0'){
       // alert('good');
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
       // console.log(data);
        var obj = $.parseJSON(data);
        document.getElementById('party_name').value=obj.company;
        document.getElementById('party_email').value=obj.email;
        document.getElementById('party_phone').value=obj.phone;
        document.getElementById('party_tax_number').value=obj.gstin;
        document.getElementById('currency_id').value=obj.currency_id;

        var party_address = "";
        if(obj.billing_address == "null"){
            party_address += obj.billing_address;
        }
        if(obj.billing_city == "null"){
            party_address += obj.billing_city;
        }
        if(obj.billing_state == "null"){
            party_address += obj.billing_state;
        }
        if(obj.billing_post_code == "null"){
            party_address += obj.billing_post_code;
        }
        document.getElementById('party_address').value=party_address;

        var party_ship_adderss = "";
        if(obj.shipping_address == "null"){
            party_ship_adderss += obj.shipping_address;
        }
        if(obj.shipping_city == "null"){
            party_ship_adderss += obj.shipping_city;
        }
        if(obj.shipping_state == "null"){
            party_ship_adderss += obj.shipping_state;
        }
        if(obj.shipping_post_code == "null"){
            party_ship_adderss += obj.shipping_post_code;
        }
        document.getElementById('party_ship_adderss').value=party_ship_adderss;
        document.getElementById("party_payment_terms").value = obj.payment_terms;
    },
    error:function (){}
    });
    }
}
function invoicePrefixchange(val){
   // alert(val);
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
    $(document).on('click', '#removeRows', function(){

        $(".itemRow:checked").each(function() {
           // console.log(this);
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
// $(document).on('blur', "[id^=invoiced_quantity_]", function(){
//     //quantityCheck();
    
//     calculateTotal();  
// });
$(document).on('blur', "[id^=quantity_]", function(){
    //quantityCheck();
    discountRestrictions();
    calculateTotal();  
});  
$(document).on('blur', "[id^=price_]", function(){
    var id = $(this).attr("data-rowid");
    inlineCurrencyConvertBaseToCureency(id);
    discountRestrictions();
    calculateTotal();
});
$(document).on('keyup', "[id^=pdiscount_amount_]", function(){
    var id = $(this).attr("data-rowid");
    inline_discount_amount_calculate(id);    
    calculateTotal();   
});
$(document).on('keyup', "[id^=percentage_discount_amount_]", function(){
    var id = $(this).attr("data-rowid");
    inline_discount_percentage_calculate(id);      
    calculateTotal();
});
$(document).on('keyup', ".inline_currency_conversion", function(){
    var id = $(this).attr("data-rowid");
    inlineCurrencyConvertCureencyToBase(id);
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
// $(document).on('change', "#disType", function(){
//         //alert('hai');
//         calculateTotal();
// });
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
function inlineCurrencyConvertCureencyToBase(id){
    var currency_rate  = $("#currency_rate").val();
    currency_rate = currency_rate !=""?currency_rate:1;
    //var amount = $('#price_'+id).val();    
    var pricecurrencyconversion = $('#prce_currency_conversion_'+id).val();
    pricecurrencyconversion = pricecurrencyconversion !=""?pricecurrencyconversion:1;
    $('#price_'+id).val((parseFloat(pricecurrencyconversion) / parseFloat(currency_rate)).toFixed(2)); 
}
function inlineCurrencyConvertBaseToCureency(id) {
    var currency_rate = $("#currency_rate").val();
    currency_rate = currency_rate != "" ? currency_rate : 1;

    var basePrice = $('#price_' + id).val();
    basePrice = basePrice != "" ? basePrice : 0;
    console.log(currency_rate,'    ----   ',basePrice,'   --    ',parseFloat(basePrice) / parseFloat(currency_rate));
    $('#prce_currency_conversion_' + id).val((parseFloat(basePrice) * parseFloat(currency_rate)).toFixed(2));
}
function roundingOffVal(grandtotal){
    var rounding_off = Math.round(parseFloat(grandtotal)) - parseFloat(grandtotal);
    if(isNaN(grandtotal)){ 
    }else{
        grandtotal = parseFloat(grandtotal) + parseFloat(rounding_off);
        $('#rounding_off').text(rounding_off.toFixed(2));
    }
    return grandtotal;
}
function inline_discount_amount_calculate(id){
   // var id = ths.attr("data-rowid");
    var amount = $('#amount_'+id).val();    
    var discountAmount = $('#pdiscount_amount_'+id).val(); 
    if(parseFloat(discountAmount) > parseFloat(amount)){
        $('#pdiscount_amount_'+id).val(amount); 
        discountAmount = amount;
    }
    var percentage = 0;
    if (parseFloat(amount) > 0) {
        percentage = (parseFloat(discountAmount) / parseFloat(amount)) * 100;
    }
    $('#percentage_discount_amount_'+id).val(percentage.toFixed(2));
}
function inline_discount_percentage_calculate(id){
   // var id = ths.attr("data-rowid");
    var amount = $('#amount_'+id).val();    
    var percentage = $('#percentage_discount_amount_'+id).val();  
    if(parseFloat(percentage) > 100){
        $('#percentage_discount_amount_'+id).val(100); 
       var discountAmount = 100;
    }  
    var amount = parseFloat(amount) || 0;
    var percentage = parseFloat(percentage) || 0;
    var discountAmount = 0;
    if (amount > 0 && percentage > 0) {
        discountAmount = (amount * percentage) / 100;
    }
    $('#pdiscount_amount_'+id).val(discountAmount.toFixed(2));
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
        var quantity = $('#quantity_'+id).val();
        var tax = $('#tax_'+id).val();
        //console.log('price'+price+'qty'+quantity+' = tax = '+tax);

        //Start Rohan Added

        if ($.inArray($('#table_name').val(), salestablelist) !== -1) {
            var item_name = $("#item_name_"+id).val();
            if(document.getElementById("item_wa_price_"+id)){ 
                var minimum_margin_per = $('#minimum_margin_'+id).val();
                var minimum_margin_amount = $('#minimum_margin_amount_'+id).val();
                minimum_margin_amount = isNaN(minimum_margin_amount)?0:minimum_margin_amount;
               if(minimum_margin_per !="" && minimum_margin_per !="0" && minimum_margin_per > 0){
                    if(parseFloat(price) < parseFloat(minimum_margin_amount) && minimum_margin_amount !=0 && minimum_margin_amount !="undefined"){
                        var msg12 = item_name+" minimum selling price is "+minimum_margin_amount+". You must sell at or above this price. Please check.";
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", msg12 , "error");
                    }
                }
            }else{
                if($.isNumeric(id)){
                    var item_id = $('#productCode_'+id).val();
                    $.ajax({
                        url: "ajax.php",
                        data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name='+$('#table_name').val()+'&ddate='+$('#transaction_date').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#transaction_date').val(),
                        type: "POST",
                        success:function(data1){
                            var itemdis = JSON.parse(data1);
                            var htmltab = "";
                            if(itemdis.item_wa_price){
                                var itmprc = itemdis.item_wa_price;
                            }else{
                                var itmprc = 0;
                            }
                            if(itemdis.minimum_margin){
                                var min_marge = itemdis.minimum_margin;
                            }else{
                                var min_marge = 0;
                            }
                            htmltab += '<input type="hidden" id="item_wa_price_'+id+'"  value="'+itmprc+'">';
                            htmltab += '<input type="hidden" id="minimum_margin_'+id+'"  value="'+itemdis.minimum_margin+'">';
                            var percentage_cal = parseFloat(itmprc)+parseFloat((itemdis.minimum_margin/100)*itmprc);
                            htmltab += '<input type="hidden" id="minimum_margin_amount_'+id+'"  value="'+percentage_cal+'">';
                            $("#item_validation_details").append(htmltab);
                            console.log(itemdis.minimum_margin,"--- ",itemdis.minimum_margin);
                            if(itemdis.minimum_margin !="" && itemdis.minimum_margin !="0" && itemdis.minimum_margin > 0){
                                if(parseFloat(price) < parseFloat(percentage_cal)){
                                    var msg12 = item_name+" minimum selling price is "+percentage_cal+". You must sell at or above this price. Please check.";
                                    swal("<?php echo ucwords($_SESSION['config']['name']); ?>", msg12 , "error");  
                                }
                            }
                        }
                    });
                }
             }

            // if($.isNumeric(id)){
            //     var minimum_margin_per = $('#minimum_margin_'+id).val();
            //     var minimum_margin_amount = $('#minimum_margin_amount_'+id).val();
            //     if(minimum_margin_per !="" && minimum_margin_per !="0"){
            //         if(price > minimum_margin_amount){
            //             console.log(' -------------------------- ok');
            //         }else{
            //             console.log('Not ---------------------- ok');
            //         }
            //     }
            // }

         }
        //end 




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
         inline_discount_amount_calculate(id);  
        //$('#dis_amount_'+id).val(formatter.format(amount));
        $('#dis_amount_'+id).val(amount); //remove format
        var ptaxRate = 0;
        if(tax != '0' && taxtype != 'O'){
            if(taxtype == 'I'){
                inclusive_tax = tax;
            }
           //console.log("Inside taxtype  "+taxtype+"  inclusive_tax  "+inclusive_tax);
            var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
            //console.log("tax:"+tax+"tax_cal"+tax_cal);
            var tax_cal1 = tax/tax_cal;
            //console.log("taxcal:"+tax_cal1);
            ptaxRate = tax_cal1*amount; 
           // console.log("Ravi1"+ptaxRate);
        }else{
           // console.log("outside esle part exicute"); 
        }
        $('#ptax_amount_'+id).val(ptaxRate);
        $('#p_amount_'+id).val(amount); 
        
       // $('#dis_ptax_amount_'+id).val(formatter.format(ptaxRate)); 
        $('#dis_ptax_amount_'+id).val(ptaxRate); //remove format
       // console.log('amount'+amount +'tax'+ptaxRate);
        var total = ptaxRate+amount;

         //console.log('ptaxRate'+  ptaxRate +':amount'+amount+'sub'+total);
        //$('#total_'+id).val(parseFloat(total));
        subamount += amount ;
        totalAmount += total;           
        totaltaxrate += ptaxRate;
    });
    $('#subTotal').val(parseFloat(subamount).toFixed(2)); 
    $('#dis_subTotal').val(formatter.format(subamount)); 
    $('#dis_subTotalid').text(formatter.format(subamount)); 
    
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
       // console.log("taxAmount"+taxAmount);
        //$('#taxAmount').val(taxAmount);
        $('#dis_taxAmount').val(formatter.format(taxAmount));
        $('#dis_grandtotal').val(formatter.format(grandtotal));
        $('#grndtotalid').text(roundingOffVal(formatter.format(grandtotal)));
        //subTotal = parseFloat(subTotal)+parseFloat(taxAmount);
        var grandtotal = parseFloat(subTotal)+parseFloat(taxAmount);
       // var disType = $('#disType').val();
        var disType  = $("#disType:checked").val();
        var disValue = $('#disValue').val();
        var div_amount_total = $('#div_amount_total').val();
        var table_name = $('#table_name').val();
      //  if(disType && disValue !="") {
            //discounts(); 
            if(disType && disValue !="") {      

                var disValue1 = 0; var disValue2 = 0;
                if(disType=='P'){
                    disValue1 = (disValue/100)*subTotal;
                    disvalue2 = (disValue/100)*parseFloat(div_amount_total);
                }else if(disType=='A'){
                    disValue1 = disValue;
                }
                // console.log("grandtotal"+grandtotal);
                // console.log("disValue"+disValue1);
                grandtotal = parseFloat(grandtotal).toFixed(2) - parseFloat(disValue1).toFixed(2);
                //console.log("grandtotal"+grandtotal);
                $('#discount').val(parseFloat(disValue1).toFixed(2));
                //$('#dis_discount').val(formatter.format(disvalue2));
                $('#dis_discount').val(formatter.format(disValue1));
                $('#dis_discountid').text(formatter.format(disValue1));
            
                $('#dis_grandtotal').val(formatter.format(grandtotal));
                $('#grndtotalid').text(formatter.format(roundingOffVal(grandtotal)));
               // console.log('grandtotal55 - '+formatter.format(grandtotal));
        amountLimitCheck(formatter.format(grandtotal));

                //amountpaid();
            }else{
                $('#discount').val(0);
                $('#dis_discount').val(0);
                $('#dis_discountid').text(0);
            
            } 
        //}  

        var shipping_amount = $('#shipping_amount').val();
       // console.log('123'+grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
        //if(shipping_amount != '' && shipping_tax_amount !='0'){
           // console.log('grandtota -------------0 - '+grandtotal);
        if(shipping_amount != ''){
           /// console.log(grandtotal+"&&&&"+shipping_amount+'&&&&&'+shipping_tax_amount);
            //grandtotal = grandtotal+parseFloat(shipping_amount).toFixed(2)+parseFloat(shipping_tax_amount).toFixed(2);
            grandtotal = parseFloat(grandtotal)+parseFloat(shipping_amount);
            //console.log("grandtotal"+grandtotal);
        }
        // console.log('grandtota -------------1 - '+grandtotal);
        var tds_tax = $('#tds_tax').val();
       // alert(tds_tax); console.log("hai");
        if(tds_tax !=0 && tds_tax !=''){
            var tds_amount = parseFloat(tds_tax)/100;
            tds_amount = tds_amount * parseFloat(subTotal);
           // console.log(tds_amount +'data'+ parseFloat(subTotal));
            $('#tds_amount').val(formatter.format(tds_amount));
            grandtotal = parseFloat(grandtotal) - parseFloat(tds_amount);
        }
        // console.log('grandtota -------------2 - '+grandtotal);
        $('#gdam').val(grandtotal);
        $('#grandtotal').val(grandtotal);
        //$('#dis_grandtotal').val(Intl.NumberFormat().format(grandtotal));
        $('#dis_grandtotal').val(formatter.format(grandtotal));
        $('#grndtotalid').text(formatter.format(roundingOffVal(grandtotal)));
        //console.log('grandtotal33 - '+grandtotal);
        amountLimitCheck(grandtotal);
         
        changeexchange();  
        setDueDate(); // select payments terms
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
    var discount_mode = $('#discount_mode').val();
    var total_discount_val = 0;
    var totalAftertax = $('#gdam').val();
    totalAftertax =  parseFloat(totalAftertax);
   // var disType = $('#disType').val();
    var disType  = $("#disType:checked").val();
        
    var disValue = $('#disValue').val();
    var subTotal = $('#subTotal').val();
     var div_amount_total = 0;
    var disValue1 = 0;
    if(disValue > 0 && discount_mode =="DO") {      
        //console.log('Dicount check1');
        if(disType=='P'){
            //console.log('Dicount check2');
            disValue1 = (parseFloat(disValue)/100)*parseFloat(subTotal);
        }else if(disType=='A'){
            ///console.log('Dicount check3');
            disValue1 = parseFloat(disValue);
        }
    }
    // console.log(" disValue : - "+disValue);
    // console.log(" disValue1 : - "+disValue1);
    var str ='';
    var change_state =  $('#supplier_state_id').val();    
    var old_state =  $('#originator_state_id').val();  //rohan change 
    //var change_state =  $('#party_place_of_supply').val();
    //var old_state =  $('#old_place').val();
    //alert(change_state+""+old_state);
   // console.log(old_state+ "-- "+change_state);
    var taxtype = $('#taxtype').val();
   //var charr = [];
    var check_arr = [];
    var arr2 = [];
    //console.log(old_state+"sjjs"+change_state);
   // for(var i=1; i< count+1; i++) {  //ravi added
    $('.itemRow').each(function(){    
        var id = $(this).attr("data-rowid")
        //var id = i; //ravi added
        //var tax = $('#tax_'+id).val();
        var taxint = $('#tax_'+id).val();
        var tax =0;
        if(taxint !="" && taxint !="0"){
            var tax = parseFloat(taxint).toFixed(2);
        }
        var amount = $('#amount_'+id).val();
        
        if(disValue1 > 0 && discount_mode =="DO"){
            var dis_val = (parseFloat(disValue1)/parseFloat(subTotal))*parseFloat(amount);
            amount = parseFloat(amount)-parseFloat(dis_val);
            div_amount_total=+amount;
            var ptaxRate = 0;
            var inclusive_tax = 0;
            if(tax != '0' && taxtype != 'O'){
                if(taxtype == 'I'){
                    inclusive_tax = parseFloat(tax);
                }
                var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
                var tax_cal1 = tax/tax_cal;
                ptaxRate = tax_cal1*amount;
            }
            $('#ptax_amount_'+id).val(ptaxRate);
            $('#p_amount_'+id).val(amount);
            $('#pdiscount_amount_'+id).val(dis_val.toFixed(2));
            var tax_amount = ptaxRate; 
        }else if(discount_mode =="DI"){
            
            var item_id = $('#productCode_'+id).val();
            var total_amount11 = $('#dis_amount_'+id).val();
            var dis_val = $('#pdiscount_amount_'+id).val();
            dis_val = dis_val !=""?dis_val:0;
            jQuery.ajax({
                url: "ajax.php",
                data:'page=checkiteminlinediscount&item_id='+item_id+'&total_amount='+total_amount11,
                type: "POST",
                success:function(data1){
                    var itemdis = JSON.parse(data1);
                    if(parseInt(itemdis.max_discount) < parseInt(dis_val) && itemdis.max_discount != 0){
                          // $('#pdiscount_amount_'+id).val(itemdis.max_discount);
                           swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Discount amount maximum allowed is "+itemdis.max_discount+", not "+dis_val+". Please check." , "error");  
                          // displaytaxes(); 
                    }
                }
            });
            total_discount_val = parseFloat(total_discount_val)+parseFloat(dis_val);
            console.log('discount inline dis_val - '+dis_val);
            amount = parseFloat(amount)-parseFloat(dis_val);
            console.log('discount inline amount- '+amount);
            div_amount_total=+amount;
            
            var ptaxRate = 0;
            var inclusive_tax = 0;
            if(tax != '0' && taxtype != 'O'){
                if(taxtype == 'I'){
                    inclusive_tax = parseFloat(tax);
                }
                var tax_cal = parseFloat(100)+parseFloat(inclusive_tax);
                var tax_cal1 = tax/tax_cal;
                ptaxRate = tax_cal1*amount;
            }
            $('#ptax_amount_'+id).val(ptaxRate);
            $('#p_amount_'+id).val(amount);
            //$('#pdiscount_amount_'+id).val(dis_val);
            var tax_amount = ptaxRate; 
            //console.log('discount inline');
            //$('#disValue').val();

            
        }else{
            $('#pdiscount_amount_'+id).val(0);
            var tax_amount = $('#ptax_amount_'+id).val();
            tax_amount = parseFloat(tax_amount);
            div_amount_total=+amount;
        }

        if(taxtype == 'I'){
            amount = parseFloat(amount)-parseFloat(tax_amount);
            amount = parseFloat(amount).toFixed(2);
        }
        if(tax_amount !=0){
            //console.log("tax_amount !=0 inside");
            if(check_arr.indexOf(tax)!=-1){
                //console.log("RAVi123");
               
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
                //console.log("tax_amount !=0 Out side");
                var  arr = {};
                check_arr.push(tax);
                arr['id'] = tax;
                arr['percentage'] = tax;
                arr['amount'] = amount;
                arr['tax_amount'] = tax_amount;
                arr2.push(arr);
            }
        }
       
    });
    
    if(discount_mode =="DI"){
        $('#disValue').val(total_discount_val);
    }     
    // console.log('rrrrrrrr11');
    // console.log(arr2);
    // console.log('rrrrrrrr11');

    $('#div_amount_total').val(div_amount_total);
    var shipping_amount_sum =shipping_tax_amount_sum =  0;
        $('.class_shipping_tax').each(function(index, element) {
            //var ptax_amount_amt = $('#ptax_amount_'+id).val();
           // var p_amount_amt = $('#p_amount_'+id).val();


            var adchargerowid = $(this).data("id");
            var shipping_tax_vl = $('#shipping_tax_'+adchargerowid).val();
            var shipping_tax = 0;
            if(shipping_tax_vl !="" && shipping_tax_vl !="0"){
                var shipping_tax = parseFloat(shipping_tax_vl).toFixed(2);
            }
            //console.log("after ------- "+shipping_tax);
            var shipping_amount = $('#shipping_amount_'+adchargerowid).val();
            //var shipping_tax = $('#shipping_tax'+).val();
            //var shipping_amount = $('#shipping_amount').val();
            //if(shipping_tax!=0 && shipping_amount != ''){
            if(shipping_amount != ''){    
                shipping_amount_sum = parseFloat(shipping_amount_sum)+parseFloat(shipping_amount);
                
               var sss =  (parseFloat(shipping_tax)/100)*parseFloat(shipping_amount);
              // console.log("sss  --- "+sss+" ------- "+shipping_tax);
               shipping_tax_amount_sum = parseFloat(shipping_tax_amount_sum)+parseFloat(sss);
                
               $('#shipping_tax_amount_'+adchargerowid).val(sss);
               sss =parseFloat(sss).toFixed(2);
                if(shipping_tax!=0){
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
                        check_arr.push(shipping_tax);
                        arr['id'] = shipping_tax;
                        arr['percentage'] = shipping_tax;
                        arr['amount'] = shipping_amount;
                        arr['tax_amount'] = parseFloat(sss);
                        arr2.push(arr);
                    }
                }
                //console.log(check_arr);
                /*str+= "<tr><td style='width: 67%;text-align:right;'> CGST @ "+shipping_tax+"% on "+shipping_amount+"</td>";
                str+= "<td>  <input  type='text' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+sss+"''> </td></tr>";*/ 
            }
        });  
        //console.log(arr2);
        /**if(shipping_amount_sum !='0'){ 
            var divide_addition_tax = shipping_tax_amount_sum/2;
            for(var i=1; i< count+1; i++) { 
                var id = i;
                var ptax_amount_amt = $('#ptax_amount_'+id).val();
                //var p_amount_amt = $('#p_amount_'+id).val();
                $('#ptax_amount_'+id).val(parseFloat(ptax_amount_amt)+parseFloat(divide_addition_tax));
                //$('#p_amount_'+id).val(parseFloat(p_amount_amt)+parseFloat(divide_addition_tax));
            }   
        } **/
        $('#shipping_amount').val(shipping_amount_sum);
        $('#shipping_tax_amount').val(shipping_tax_amount_sum);
        
        
    // console.log('rrrrrrrr');
    // console.log(arr2);
    // console.log('rrrrrrrr');
    var totaltax = 0;
    for( var k =0; k < arr2.length; k++){
            //console.log('check0:'+k+'data:'+arr2[k]['amount'])
            if(arr2[k]['amount']){
                //console.log('check: '+k+ ' percentage - '+arr2[k]['id']+' cal:'+arr2[k]['amount'])
                if(old_state != change_state){
                    str+= "<tr><td class='p-2'> IGST @ "+arr2[k]['id']+"% on "+arr2[k]['amount']+"</td>";
                    str+= "<td class='p-2 text-end'>  <span id='dis_taxRateid'>"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"</span>  <input  type='hidden' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"''> </td></tr>";
                }else{
                    str+= "<tr><td class='p-2'> CGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                    str+= "<td class='p-2 text-end'> <span id='dis_taxRateid'>"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"</span> <input  type='hidden' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
                     str+= "<tr><td class='p-2'> SGST @ "+parseFloat(arr2[k]['id']/2)+"% on "+arr2[k]['amount']+"</td>";
                    str+= "<td class='p-2 text-end'> <span id='dis_taxRateid'>"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"</span> <input  type='hidden' class='form-control amount' name='dis_taxRate'  readonly placeholder='Tax Amount' value='"+parseFloat(parseFloat(arr2[k]['tax_amount']/2)).toFixed(2)+"''> </td></tr>";
                }
                totaltax = parseFloat(totaltax);
               // console.log("tax:"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"totaltax:"+ typeof totaltax);
                var singletax = parseFloat(arr2[k]['tax_amount']).toFixed(2)
                //console.log("tax:"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"type of totaltax:"+ typeof totaltax+ 'value'+ totaltax+ 'singletax'+ typeof singletax+'value'+singletax );
                 totaltax+=parseFloat(singletax);
                //console.log("tax:"+parseFloat(arr2[k]['tax_amount']).toFixed(2)+"totaltax:"+ typeof totaltax);
            }
    }
    totaltax  = parseFloat(totaltax).toFixed(2);
    
    str +='<tr><td class="p-2">Total Tax</td><td class="text-end p-2"><span id="dis_taxRateid">'+totaltax+'</span> </td></tr>';
    $('#taxAmount').val(totaltax);
    $("#display_tax").html(str);
    $("#display_taxshow").show();
   // $('.custom_dynamic_transaction_table').after(totaltax);
 }
 function discounts(){
     var formatter = new Intl.NumberFormat('en-IN', {
  minimumFractionDigits: 2,
});
    //var disType = $('#disType').val();
    var disType  = $("#disType:checked").val();
        
    var disValue = $('#disValue').val();
    // console.log(disType);
    // console.log(disValue);
    var totalAftertax = $('#subTotal').val();
    var gdam = $('#gdam').val();
    var div_amount_total = $('#div_amount_total').val();
    //console.log(totalAftertax);  
    totalAftertax =  parseFloat(totalAftertax);
   // console.log("totalAftertax -- "+totalAftertax);
    if(disType && disValue !="" && totalAftertax) {      
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
            $('#dis_discountid').text(formatter.format(disValue1));
            $('#dis_grandtotal').val(formatter.format(totalAftertax));
            $('#grndtotalid').text(formatter.format(roundingOffVal(totalAftertax)));
            //console.log('grandtotal11 - '+formatter.format(totalAftertax));
        amountLimitCheck(formatter.format(totalAftertax));
            $('#grandtotal').val(parseFloat(totalAftertax).toFixed(2));
            //amountpaid();
    } 
 }
function changeexchange(){
    //var formatter = new Intl.NumberFormat('en-IN', {minimumFractionDigits: 2,});
    var ex_id = $('#currency_id').val();
    var de_ex_id = $('#originator_currency_id').val();
    var ex_amount = $('#currency_rate').val();
   // var currency_operater = $("#currency_operater").val();
   // currency_operater = currency_operater !=""?currency_operater:"*";
    if(ex_amount == "" || ex_amount == 0){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "<?php echo $_SESSION['error_message']['9066']; ?>",
            type: "error",
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: "Ok",
            confirmButtonClass: "btn-danger",
            closeOnCancel: false
        });
        $(".exchange").css("display","none");
        $(".suppliercurrencycalc_tab").removeClass('active');
        $(".originatercurrencycalc_tab").addClass('active');
        return false;
    }
    if(ex_id != de_ex_id){
        $(".exchange").css("display","table");
        var subtotal = $('#subTotal').val();
        var grandtotal = $('#grandtotal').val();
        var discount = $('#discount').val();
        var taxamount = $('#taxAmount').val();

        $('#ex_rate').text(parseFloat(ex_amount).toFixed(2));
        $('#ex_sub_total').text(parseFloat(subtotal*ex_amount).toFixed(2));
        $('#ex_tax').text(parseFloat(taxamount*ex_amount).toFixed(2));
        $('#ex_discount').text(parseFloat(discount*ex_amount).toFixed(2));
        $('#ex_grand_total').text(parseFloat(grandtotal*ex_amount).toFixed(2));
        $(".inline_currency_conversion").show();
    }else{
        $(".exchange").css("display","none");
        $(".suppliercurrencycalc_tab").removeClass('active');
        $(".originatercurrencycalc_tab").addClass('active');
        var subtotal = $('#subTotal').val();
        var grandtotal = $('#grandtotal').val();
        var discount = $('#discount').val();
        var taxamount = $('#taxAmount').val();
        $('#ex_rate').text(parseFloat(ex_amount).toFixed(2));
        $('#ex_sub_total').text(parseFloat(subtotal).toFixed(2));
        $('#ex_tax').text(parseFloat(taxamount).toFixed(2));
        $('#ex_discount').text(parseFloat(discount).toFixed(2));
        $('#ex_grand_total').text(parseFloat(grandtotal).toFixed(2));
        $(".inline_currency_conversion").hide();
      }
   //   allinputCurrencyConversion();
 }

// function allinputCurrencyConversion(){
//     var currency_rate = $('#currency_rate').val();
//     currency_rate  = (currency_rate !="" || currency_rate !="0")?currency_rate:1;
//     $('.itemRow').each(function(){    
//         var id = $(this).attr("data-rowid")
//         var price = $("#price_"+id).val();
//         var price_currency_conversion = (price/currency_rate);
//         console.log(id,' --- ',price,' ------- ',price_currency_conversion);
//         $("#price_currency_conversion_"+id).val(parseFloat(price_currency_conversion).toFixed(2));
//     });

//  }
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
    var transaction_item_row_id = $('#transaction_item_row_id').val();
    if(table_name != 'requisitions'){
    var id = fid.split('_');
    id = id[1];
    var quantity  = $('#quantity_'+id).val();
    var quantity_old = $('#quantity_old_'+id).val();
    if (parseFloat(quantity_old) < parseFloat(quantity)){
        // swal({              
        //     title: "Quantity alert ",              
        //     text: "6030 : Quantity cannot be more than "+quantity_old,              
        //     type: "warning",           
        //     buttonsStyling: false,
        //     confirmButtonText: "OK",    
        //     confirmButtonClass: "btn btn-danger", 
        //     }, function () {
        //         $('#quantity_'+id).val(quantity_old);
        //         calculateTotal();
        //     });                            
        }
    }
    checkqtyvalidation(fid);
    baseUOMConversion(id);
}
function checkqtyvalidation(fid){
    var table_name = $('#table_name').val() || "";
    var ref_table = $('#ref_type').val() || "";
     if(ref_table == 'delivery_challans'){
        return true;
    }
    var ref_id = $('#ref_id').val();
    var id = fid.split('_');
    id = id[1];
    var quantity  = $('#quantity_'+id).val(); 
    var item_id = $('#productCode_'+id).val();
    var ref_transaction_item_table_id = $('#ref_transaction_item_table_id_'+id).val();
    // console.log("checkqtyvalidation Start --- checkkkkk");
    // console.log(ref_table+" -- "+ref_id+" - "+quantity+" -- "+item_id+" --- "+ref_transaction_item_table_id);
    // console.log("checkqtyvalidation End--- checkkkkk");
    jQuery.ajax({
        url: "ajax.php",
        data:'page=dynamiccheckqtyvalidation&ref_table='+ref_table+'&ref_id='+ref_id+'&quantity='+quantity+'&item_id='+item_id+'&table_name='+table_name+'&ref_transaction_item_table_id='+ref_transaction_item_table_id,
        type: "POST",
        success:function(data){
            var decoderes = JSON.parse(data);
            if(parseInt(decoderes.pending_qty) < parseInt(quantity)){
                swal({              
                    title: "Quantity alert ",              
                    text: "6030 : Quantity cannot be more than "+decoderes.pending_qty,
                    type: "warning",           
                    buttonsStyling: false,
                    confirmButtonText: "OK",    
                    confirmButtonClass: "btn btn-danger", 
                    }, function () {
                        $('#quantity_'+id).val(decoderes.pending_qty);
                        calculateTotal();

                });   
            }
        }
    });
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
              //  console.log(data);
                //console.log(quantity);
                if (parseFloat(data) < quantity){
                    swal({              
                        title: "Quantity on hand "+parseFloat(data),              
                        text: "6020 : Do you want to change the quantity to "+parseFloat(data)+" ?",              
                        type: "warning",           
                        buttonsStyling: false,
                        confirmButtonText: "Yes, change!",    
                        confirmButtonClass: "btn btn-danger",
                        showCancelButton: true,     
                        cancelButtonText: " No, proceed",      
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
function submit(val){
   // console.log(document.getElementById('myForm').name);
    document.getElementById('myForm').submit();
}
function changestatus(val,val2){
    //$('#status_values').val(val);
        var x = val;
        var table_name = document.getElementById('table_name').value;
        var table_id =document.getElementById('hid').value;
        var tranid = document.getElementById('transaction_number').value;
        var table = document.getElementById('table_name');
        var val_sub = "234";
    if(val2 == 2){
        jQuery.ajax({
            url: "views/custom_quotation_vender_pop.php",
            data:'table='+table_name+'&field='+x+'&rid='+table_id+'&tranid='+table_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
                $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
                   //submit();
                }); 
                $(".js-example-basic-multiple").select2(); 
            },
            error:function (){}
        });
    }else if(val2 == 1){
        //alert("hai");
        jQuery.ajax({
            url: "views/custom_quotation_pop.php",
            data:'table='+table_name+'&field='+x+'&rid='+table_id+'&tranid='+tranid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
                $("#kt_modal_KTDatatable_remote").on("hidden.bs.modal", function () {
                   submit();
                });           
             },
            error:function (){}
        });
    }else if(val2 == 3){
        var  str = '';
        str = 'table='+table_name+'&new_table='+'purchase_orders'+'&vender_id='+$('#kt_select2_4').val()+'&tranid='+$('#hid').val()+'&hid='+$('#hid').val()+'&user_id='+$('#user_id').val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertvenderquatation&'+str,
            success: function(response) {
               //console.log(response);
                //submit();
            }
        });

    }else{
        submit(); 
    }
      
     //alert("man");
    // document.getElementById("form_id").submit();
        //submit('22');
     //validateForm();
    //document.myForm.submit();
    //document.getElementById("myForm").submit();
 }
function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    var validate = checkMandatoryArray();
    if(validate == 0){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#popup1").html(loader);
        $('#kt_modal_KTDatatable_remote').modal("show");
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
            },
            error:function (){}
        });
    }
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    var validate = checkMandatoryArray();
    if(validate == 0){
        swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Please note the status of the document will change to "+new_status_name,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false
            },
            function(){
                //console.log("dataa aa");
                jQuery.ajax({
                    url: "ajax.php",
                    data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                    type: "POST",
                    success:function(response){
                        //console.log(response);
                        var res = JSON.parse(response);
                        //console.log(res);
                        //$(".pcoded-main-container").addClass('event_disabled');
                        window.location.href = window.location.href;
                        if(res.transaction_update.status=='sucess'){
                             window.location.href = window.location.href;
                            swal({
                                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                window.location.href = window.location.href;
                            });
                        }else{
                            swal({
                                title: "Oops...",
                                text: res.transaction_update.message,
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: "Ok",
                                confirmButtonClass: "btn-danger",
                                closeOnCancel: false
                            });
                        }
                    },
                    error:function (){}
                });
            }); 
    }
}
function checkMandatoryArray(){
        var mandatory_array = $("#mandatory_array").val();
        var table_name = document.getElementById('table_name').value;
        //console.log(mandatory_array);
        var fieldmsg = "";
        var countitemrow = $('.itemRow').length;
        if(countitemrow == 0){
            swal("Please add at least one Product or Service before Submit." , "error");    
            return 1;
        }
        if(mandatory_array !="" && mandatory_array !='null'){
            var result = JSON.parse(mandatory_array);
            result.forEach(function(res){
                if($("#"+res.field).val() ==""){    
                fieldmsg += res.lable+",";  
                   // console.log('ID: ' + res.field + " value -- "+$("#"+res.field).val());
                }
            });

            $('.itemRow').each(function(){    
                var id = $(this).attr("data-rowid")
                var itmselect = $("#productCode_"+id).val();
               // console.log("itmselect -- "+itmselect);
                if(itmselect =="0"){
                    fieldmsg += " Product Not Seleted,";
                }
                var uom = $("#uom_"+id).val();
                if(uom =="0" || uom ==""){
                    fieldmsg += " UOM Not Seleted,";
                }
                if(table_name=='stock_adjustments'){
                    var qtyy = $("#quantity_"+id).val();
                    if(qtyy =="0" || qtyy ==""){
                        fieldmsg += " Quantity Not Enter,";
                    } 
                    var respon1 = $("#respon_"+id).val();
                    if(respon1 =="0" || respon1 ==""){
                        fieldmsg += " Reason Not Seleted,";
                    } 
                }

            });
            if(fieldmsg !=""){
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", fieldmsg+" Is Mandatory" , "error");    
                return 1;
            }else{
                return 0;
            }
        }else{
             return 0;
        }
        
   }
function viewApprovalAllUsers(id= ""){
    $(".custom-popup-open").hide();
   $("."+id).show('slow');
}

function OpenAdditionalCharges(){
    $(".custom-popup-additionalcharges-open").show();
}
function closeAdditionalCharges(){
    $(".custom-popup-additionalcharges-open").hide();
}
var achargemxid = 100;
function addAdditionalCharges(){
    achargemxid = achargemxid+1;
    var additional_charge_id = $("#additional_charge_id").val();
    var additional_charge_amount = $("#additional_charge_amount").val();
    if (additional_charge_id === null || additional_charge_id =="" || additional_charge_amount =="" || additional_charge_amount =="0"){
        $('#error_msg').text('*Please Re-Select Additional Charge/Enter Amount');
    }else{
        $('#error_msg').text(' ');
        var additional_charge_amount = $("#additional_charge_amount").val();
        var selectElement = $("#additional_charge_id");
        selectElement.find('option[value="'+additional_charge_id+'"]').prop('disabled', true);

        //console.log("additional_charge_amount "+additional_charge_amount);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=selectadditionalcharge&additional_charge_id='+additional_charge_id+'&additional_charge_amount='+additional_charge_amount,
            type: "POST",
            success:function(data){
                var result = JSON.parse(data);
                var additional_charges = result.additional_charges; 

                var rowid = "row_additional_charges_"+achargemxid;

                var html = '<tr id="'+rowid+'"><td class="amount text-start p-2" width="40%" style="width:40%"> <i class="ti-trash text-danger" onclick="removeAdditionalCharges(\''+rowid+'\',\''+additional_charges.id+'\')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove"></i> '+additional_charges.charge_name+'</td><td class="p-2 text-right" width="60%" style="width:40%">'+additional_charge_amount+' ('+additional_charges.gst+'%)<input type="hidden" class="class_shipping_tax" data-id="'+achargemxid+'" name="shipping_tax_ac[]" id="shipping_tax_'+achargemxid+'" value="'+additional_charges.gst+'"><input type="hidden" name="shipping_amount_ac[]" id="shipping_amount_'+achargemxid+'" value="'+additional_charge_amount+'"><input type="hidden" name="shipping_tax_amount_ac[]" id="shipping_tax_amount_'+achargemxid+'" value=""><input type="hidden" name="charge_name_ac[]" value="'+additional_charges.charge_name+'"><input type="hidden" name="charge_id_ac[]" value="'+additional_charges.id+'"></td></tr>';
                //console.log("additional_charge_amount "+additional_charge_amount+" - "+html);
                $('#rowadditionalcharges').before(html);
                $(".custom-popup-additionalcharges-open").hide();
               calculateTotal();
            },
            error:function (){}
        });
    }
   
    //$('#rowadditionalcharges tr').after('<tr>...</tr><tr>...</tr>');
   // $('#rowadditionalcharges').prepend('<td>My new table cell</td>');
    //$('<tr><td>My new table cell</td></tr>').appendTo('#rowadditionalcharges');    
}

function removeAdditionalCharges(rowid,charge_id){
    $("#"+rowid).remove();
     var selectElement = $("#additional_charge_id");
    selectElement.find('option[value="'+charge_id+'"]').prop('disabled', false);
     calculateTotal();
}
function getApplicationTaskLogsPopUp(rid){
    jQuery.ajax({
        url: "views/custom_insert_application_task_logs_popup.php",
        data:'rid='+rid,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}
function cancelInvoices(transaction_table,transaction_id){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $('#popup1').html(loader);
    $('#kt_modal_KTDatatable_remote').modal("show");
    jQuery.ajax({
        url: "views/custom_insert_invoice_cancellations_popup.php",
        data:'transaction_table='+transaction_table+'&transaction_id='+transaction_id,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                dateFormat: date_format,
                minDate: 0,
                maxDate: "+30D",
                setDate: new Date(),
                changeMonth: true,
                changeYear: true,
            }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
            var elemdefault = document.querySelector('.js-primary-sm-popup');
            var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });

        },
        error:function (){}
    });
}
function getPartyDetails(part_table){
        var party_id = $("#party_id").val();
        $.ajax({
            url: "views/custom_view_party_detail_popup.php",
            data:'part_table='+part_table+'&party_id='+party_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
        });    
    }
function getSameRefDocuments(){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#samerefdocuments").html(loader);
    var option = "<?php echo $_GET['option']; ?>";
    var application_table_name = document.getElementById('table_name').value;
    var transaction_id =document.getElementById('hid').value;
    var ref_table = $('#ref_type').val();
    var ref_id = $('#ref_id').val();
     $.ajax({
            url: "views/custom_list_dynamic_transaction_invoices.php",
            data:'application_table_name='+application_table_name+'&transaction_id='+transaction_id+'&option='+option+'&ref_table='+ref_table+'&ref_id='+ref_id+'&refrance_res=Y',
            type: "POST",
            success:function(data){
                $('#samerefdocuments').html(data);
            }
        }); 
}

function getItemSearch(){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#searchitem").html(loader);
    var option = "<?php echo $_GET['option']; ?>";
    var application_table_name = document.getElementById('table_name').value;
    var transaction_id =document.getElementById('hid').value;
    var ref_table = $('#ref_type').val();
    var ref_id = $('#ref_id').val();
     $.ajax({
            url: "views/custom_list_item_search.php",
            data:'application_table_name='+application_table_name+'&transaction_id='+transaction_id+'&option='+option,
            type: "POST",
            success:function(data){
                $('#searchitem').html(data);
                $(".js-example-basic-single").select2(); 
            }
        }); 
}


function openAdvancePaymentsPopup(){
    $(".custom-popup-open").hide();
    $(".popup_advance_payment").show();
    var application_table_name = document.getElementById('table_name').value;
    var transaction_id =document.getElementById('hid').value;
    var ref_table = $('#ref_type').val();
    var ref_id = $('#ref_id').val();
    $.ajax({
        url: "ajax.php",
        data:'page=getpendingadvanecepayments&application_table_name='+application_table_name+'&transaction_id='+transaction_id+'&ref_table='+ref_table+'&ref_id='+ref_id,
        type: "POST",
        success:function(data){
            //console.log(data);
            var res = JSON.parse(data);
            if(res.result_cnt != 0){
                $("#pendingadvancepayments").html(res.result.pending_advance_payment);
                $("#pendingadvancepaymentstext").val(res.result.pending_advance_payment);
            }
           // $('#samerefdocuments').html(data);
        }
    });     
}
function enterAdjustAdvancePayment(adv_amt){
   var grandtotal =  $("#grandtotal").val();
   var pendingadvancepayments =  $("#pendingadvancepaymentstext").val();
   //console.log(pendingadvancepayments+" ----  "+adv_amt+" --- "+grandtotal);
   if(parseFloat(pendingadvancepayments) >= parseFloat(adv_amt)){
        if(parseFloat(grandtotal) <= parseFloat(adv_amt)){
            //console.log("not available grandtotal");
            $("#advance_payment_text").text(0);
            $("#advance_payment_textbox").val(0);
            $("#enteradjustadvancepayment").val(0);
        }else{
            $("#advance_payment_text").text(adv_amt);
            $("#advance_payment_textbox").val(adv_amt);
        }
   }else{
        $("#advance_payment_text").text(0);
        $("#advance_payment_textbox").val(0);
        $("#enteradjustadvancepayment").val(0);
       
   // console.log("not available advance payments");
   }
   setDueDate();
}
function closeAdjustAdvancePayment(){
    $(".custom-popup-open").hide();
}

function OpenInsertDebitCreditNotePopUp(transaction_table,transaction_id,note_type){
    jQuery.ajax({
        url: "views/custom_insert_debit_and_credit_note_popup.php",
        data: "transaction_table="+transaction_table+"&transaction_id="+transaction_id+"&note_type="+note_type,
        type: "POST",
        success:function(data){
             $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");

            // $('#popupm').html(data);
            // $('#kt_modal_KTDatatable_remotem').modal("show");
            $(".js-example-basic-single").select2();
        }
    });
}

function requestDocumentsCreation(transaction_table,transaction_id,clone_table,party_type=""){
    if(clone_table =="requisitions"){

        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Are you sure you want to create a requisition?',
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
                 $.ajax({
                    url: "ajax.php",
                    type: "POST",
                    data: 'page=insertclonetransactions&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&clone_table='+clone_table+'&party_type='+party_type+'&party_id=',
                    success: function(response){
                        //console.log(response);
                        window.location.href = window.location.href;
                        l
                    }   
                });
            }
        });

    }else{
        jQuery.ajax({
            url: "views/custom_insert_clone_transactions_popup.php",
            data:'transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&clone_table='+clone_table+'&party_type='+party_type,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
                $(".js-example-basic-multiple").select2(); 
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
}

function submitTransactions(btntype){
    $("#submit_value_btn").val(btntype);
    var validate = checkMandatoryArray();
    if(validate == 0){
        //console.log("submit");
        $("#form-validation-nouse").submit();
    }

}
function showAttchmentInAvailableItemTables(){
        var htmlv = "";
        $('.itemRow').each(function(){    
            var rowseq = $(this).attr("data-rowid");
            var data = $("#compliance_specification_documents_"+rowseq).val();
            var item_name = $("#item_name_"+rowseq).val();
            try {
                var attachments = $.parseJSON(data);
                htmlv +='<div class="form-control"><label class="form-label">'+item_name+'</label>'; 
                $.each(attachments, function(index, item) {
                    htmlv +='<input type="hidden" name="compliance_specification_documents['+item.attachment+'][item_name]" value="'+item_name+'"><input type="hidden" name="compliance_specification_documents['+item.attachment+'][attachment_name]" value="'+item.attachment_name+'"><input type="hidden" name="compliance_specification_documents['+item.attachment+'][attachment]" value="'+item.attachment+'">';
                    htmlv +='<div class="checkbox-fade fade-in-primary" >';
                    htmlv +='<label class="form-label"><input type="checkbox" name="compliance_specification_documents['+item.attachment+'][show_print]" class=""><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>'+item.attachment_name+'</label>';
                    htmlv +='</div>';
                    //console.log("Attachment Name: " + item.attachment_name);
                    //console.log("File Path: " + item.attachment);
                });
               
                htmlv +='</div>';
            } catch (e) {
                console.log("JSON Parse error:", e.message);
            }

            
        });
        $("#attchmentshowlist").html(htmlv);
    }

function changeDiscountMode(){
    var discount_mode = $("#discount_mode").val();
    if(discount_mode == "DI"){
        $("#overall_discount_div").addClass('event_disabled');
        $(".discount_textbox").attr("type", "text");
        $(".discount_textbox").attr("readonly", false); 
        $(".percentage_discount_textbox").attr("type", "text");
        $(".percentage_discount_textbox").attr("readonly", false); 
        $('input[name="discount_type"][value="A"]').prop("checked", true);
    }else{
        $("#overall_discount_div").removeClass('event_disabled');
        $(".discount_textbox").attr("type", "hidden");
        $(".discount_textbox").attr("readonly", true); 
        $(".percentage_discount_textbox").attr("type", "hidden");
        $(".percentage_discount_textbox").attr("readonly", true); 
    }
    calculateTotal();
}  

function createMultiReferenceDocumnets(){
    var transaction_table = document.getElementById('table_name').value;
    var transaction_id =document.getElementById('hid').value;
    var party_id = $("#party_id").val();
    jQuery.ajax({
        url: "views/custom_insert_multi_reference_quotation_new.php",
        data:'transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&party_id='+party_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
            $(".js-example-basic-multiple").select2(); 
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}  
<?php if($table1['multi_reference'] == "Y"){ ?>
    window.addEventListener("load", function() {
        console.log('workfing');
        $(".refkey_ref_type").addClass('event_disabled');
        $(".refkey_ref_id").addClass('event_disabled');

        var jsonStr = $('#mandatory_array').val();
        var data = JSON.parse(jsonStr);
        data = data.filter(function(item) {
            return !["ref_type", "ref_id", "ref_no"].includes(item.field);
        });
        $('#mandatory_array').val(JSON.stringify(data)); 
       });
<?php } ?>

window.addEventListener("load", function() {
    allBaseUOMConversion();
});
function allBaseUOMConversion(){
    $('.itemRow').each(function(){  
        var seqid = $(this).attr("data-rowid");
        baseUOMConversion(seqid);
    });
}
function baseUOMConversion(seqid){
    var quantity = $("#quantity_"+seqid).val();
    var baseuom_id = $("#hide_baseuomtext_"+seqid).val();
    var uom_id = $("#uom_"+seqid).val();
    if(baseuom_id !="" && uom_id !="" && quantity !=""){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getbaseuomconversion&quantity='+quantity+'&uom_id='+uom_id+'&baseuom_id='+baseuom_id,
            type: "POST",
            success:function(data){
                var retdata = JSON.parse(data);
                $('#baseuomtext_'+seqid).text(retdata.res); 

            },
            error:function (){}
        });
    }else{
        $('#baseuomtext_'+seqid).text('NA'); 
    }
}
function discountRestrictions(){
    let value = $("#disValue").val();
    value = parseFloat(value.replace(/[^0-9.]/g, '')) || 0;
    var discount_type = $("input[name='discount_type']:checked").val(); 
    if(discount_type == "A"){
        let limit = parseFloat($("#subTotal").val()) || 0;
        if (value > limit) {
            value = limit;
        }
    }else{
        var limit = 100;
        if (value > limit) {
            value = limit;
        }
    }
    $("#disValue").val(value);
    calculateTotal();
}
function clickToConvertproformaInvoicetoSalesInvoices(transaction_table, transaction_no){
    $("#clickToConvertproformaInvoicetoSalesInvoicesid").text("Please Wait........");
    $("#clickToConvertproformaInvoicetoSalesInvoicesid").addClass("disabled");
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: 'Are you sure you want to convert the Proforma Invoice into a Sales Invoice?',
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
            jQuery.ajax({
                url: "ajax.php",
                data:'page=proformainvoicethroughsalesinvoicescreated&transaction_table='+transaction_table+'&transaction_no='+transaction_no,
                type: "POST",
                success:function(data){
                    result = JSON.parse(data);
                    if(result.res.status == "sucess"){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
                       window.location.href = window.location.href;
                    }else{
                        $("#clickToConvertproformaInvoicetoSalesInvoicesid").html('<i class="text-white ti-layers"></i> Convert to Sales Invoice');
                        $("#clickToConvertproformaInvoicetoSalesInvoicesid").removeClass("disabled");
                    }
                },
                error:function (){}
            });
        } else {
            $("#clickToConvertproformaInvoicetoSalesInvoicesid").html('<i class="text-white ti-layers"></i> Convert to Sales Invoice');
            $("#clickToConvertproformaInvoicetoSalesInvoicesid").removeClass("disabled");
        }
    });
    
}
</script>
<?php 
if($checktimeprint > 0){
    echo "Scrpit Done Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
?>
<div class="row">
    <div class="col-lg-9">
        <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#selfdocuments" role="tab" aria-selected="true"><?php echo $title; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#samerefdocuments" role="tab" aria-selected="true" onclick="getSameRefDocuments()">Same Ref Documents</a>
            </li>
             <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#searchitem" role="tab" aria-selected="true" onclick="getItemSearch()">Search Item</a>
            </li>
        </ul>
    </div>
    <div class="col-lg-3 text-right">
        <?php 
        $notrequirdtoshow = array('requisitions', 'enquiries', 'sales_invoices');

        if(!in_array($table_name, $notrequirdtoshow) AND  ($table1['multi_reference'] == "Y" OR $table1['multi_reference'] == "") AND ($table1['current_status'] == ST_DRAFT OR $table1['current_status'] == ST_ACTIVE OR $table1['current_status'] =="") AND $table1['ref_id'] ==""){ ?>
        <button type="button" class="btn btn-primary" onclick="createMultiReferenceDocumnets()"><i class="fa fa-object-group"></i> &nbsp;Multi Reference Documents</button>
        <?php } ?>
    </div>
</div>
<div class="tab-content tabs">
    <div class="tab-pane card-body p-0" id="searchitem" role="tabpanel"> 
        <?php include("views/custom_list_item_search.php"); ?>
    </div>
    <div class="tab-pane card-body p-0" id="samerefdocuments" role="tabpanel"> 
    </div>
    <div class="tab-pane card-body active p-0" id="selfdocuments" role="tabpanel"> 
    <?php 
        if($table1['cancel_invoice'] =="Y"){ ?>
        <div id="dynamiccanceldiv"><span>Cancel Invoice</span></div>
    <?php } ?>
    <div class="flex-column-fluid">
        <div class="container-fluid">
        <?php if(count((array)$duplicateitemsary) != 0){ 
            $stringdublicateitemname =implode(", ",$duplicateitemsary);
         ?>
            <div class="alert alert-info alert-dismissible">
                <strong><?php echo $_SESSION['error_message']['9177']." <b>".$stringdublicateitemname."<b>   Please confirm"; ?> </strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if($_GET['error'] !=''){ ?>
            <div class="alert alert-warning alert-dismissible">
                <strong><?php echo $_GET['error']." : ".$_SESSION['error_message'][$_GET['error']]; ?> </strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php } ?>
    
    <?php if(count((array)$items_chart_of_account_not_provide) !="0" AND isset($_SESSION['module_setp'][4])){ 
            $errmsg = "Please Provide Chart of Account - ";
            foreach($items_chart_of_account_not_provide as $npac){
                $errmsg .= "</br>".$npac['sku']." - ";
                foreach($npac['account_missings'] as $npacname){
                    $errmsg .= str_replace('_',' ',ucwords($npacname)).", ";
                }
            }  ?> 
         <div class="alert alert-danger alert-dismissible">
                <strong><?php echo $errmsg; ?> </strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php } ?>

    <?php $chartofacounterrer = 0;
        if(count((array)$chart_of_account_lock) !="0" AND isset($_SESSION['module_setp'][4])){  $chartofacounterrer = 1;
            $errmsg = "This account is locked. Please check the following account - ";
            foreach($chart_of_account_lock as $npac){
                $errmsg .= "</br>".$npac['sku']." - ";
                foreach($npac['account_lock'] as $npacname){
                    $errmsg .= str_replace('_',' ',ucwords($npacname)).", ";
                }
            }  ?> 
         <div class="alert alert-danger alert-dismissible">
                <strong><?php echo $errmsg; ?> </strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php } ?>



    <?php if($_GET['error_messages'] !="" AND $copy == '1' AND ($table1['current_status'] == ST_DRAFT OR $table1['current_status'] == ST_ACTIVE)){ $disable = "disabled"; ?> 
    <?php //if($_GET['error_messages'] !="" AND ($table1['current_status'] == ST_DRAFT OR $table1['current_status'] == ST_ACTIVE OR $copy == 0)){  $disable = "disabled"; ?>
         <div class="alert alert-danger alert-dismissible">
                <strong><?php echo $_GET['error_messages']; ?> </strong> 
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php } ?>
    <?php 
    if($chkres['status']=='fail'){ 
        foreach((array)$chkres['data'] as $daerr){ 
        $iterr = "";
        $iterr = $daerr['item_name']." ".$_SESSION['error_message'][$daerr['error']];
        if($daerr['error'] == '9070'){
           $iterr = $_SESSION['error_message'][$daerr['error']];
        }
    ?>
        <div class="alert alert-danger alert-dismissible">
            <strong><?php echo $iterr; ?> </strong> 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } } ?>
    <div class="">
        <div class="">
            <?php 
        $workflowsteps = getWorkflowStatus($table_name,$rid,$table1['current_status']);
            ?>
            <form class="kt-form kt-form--label-right" name="myForm" id="form-validation-nouse" action="" method="post" enctype="multipart/form-data"  >
                <div class="kt-portlet">
                    <div class="card">
                        <?php if($rid != "" && $copy == 1){ echo $rtable_name ==""?$workflowsteps['current_status_ribbon']:""; } ?>
                        <div class="card-header table-card-header">
                            <h5><?php echo $title;  ?></h5>
                            <div class="card-header-right table-card-header m-r-50">
                                 <button type="button" onclick="history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i> Back</button>
                 <?php 
                if($rid != "" && $copy == 1){  ?>
                   
             <?php
                    $allpermision = $_SESSION['role_permission'][$table_name];

                    if($allpermision['permission_print'] == '1'){$permission_listbtn = "disabled"; }

?>
                    <?php
                        $printurl = "index.php?option=COM_QO_PRINT&rid=".$rid."&table=".$table_name."&title=".urlencode($title);
                        // $printurl = "index.php?option=COM_QO_PRINT_OMKAR2&rid=".$rid."&table=".$table_name."&title=".urlencode($title);

                        if($table_name =="rental_invoices"){
                            $printurl = "index.php?option=COM_VIEW_RENTAL_INVOICES&rid=".$rid."&table=".$table_name."&title=".urlencode($title);
                        }
                        if($table_name =="utility_rental_invoices"){
                            $printurl = "index.php?option=COM_VIEW_RENTAL_INVOICES&rid=".$rid."&table=".$table_name."&title=".urlencode($title);
                        }
                    ?>
                    <a target="_blank" href="<?php echo $printurl; ?>" class="btn btn-clean kt-margin-r-10" <?php echo $permission_listbtn; ?>>
                                <i class="ti-printer"></i>
                    </a>   
                         
                    <?php  

                    if(($chartofacounterrer == '1' OR $_GET['error_messages']) AND ($table1['current_status'] == ST_DRAFT OR $table1['current_status'] == ST_ACTIVE)){ }else{

                        
                        echo ($rtable_name =="" AND (isset($table2[0]['item_id'])))?$workflowsteps['workflow_btn']:"";
                    
                    }
                        

                    if($dynamic_mapping_tables[$table_name]['cancellation_application_table_name'] !="" AND ($table1['cancel_invoice'] =="N" || $table1['cancel_invoice'] =="") && $table1['originator'] ==$_SESSION['userid']){ ?>
                            <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="cancelInvoices('<?php echo $table_name; ?>','<?php echo $rid; ?>')">
                                <i class="text-white fa fa-times-circle"></i> &nbsp;Cancel <?php echo $title; ?>
                            </button>
                    <?php } ?>  
                    <?php if($table_name == "enquiries" OR $table_name == "estimates" OR $table_name == "sales_orders" AND $table1['current_status'] == ST_APPROVED){ ?>    
                        <div class="dropdown-primary dropdown open">
                            <button class="btn btn-primary dropdown-toggle waves-effect waves-light " type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Request</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <a class="dropdown-item waves-light waves-effect" href="#" onclick="requestDocumentsCreation('<?php echo $table_name; ?>','<?php echo $rid; ?>','requisitions')">Requisitions</a>
                                <a class="dropdown-item waves-light waves-effect" href="#" onclick="requestDocumentsCreation('<?php echo $table_name; ?>','<?php echo $rid; ?>','quotations')">Quotations</a>
                                <a class="dropdown-item waves-light waves-effect" href="#" onclick="requestDocumentsCreation('<?php echo $table_name; ?>','<?php echo $rid; ?>','purchase_orders')">Purchase Order</a>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if($table_name == "proforma_sales_invoices" AND $table1['current_status'] == ST_APPROVED AND $rpres['data']['sales_invoice_id'] ==""){ ?> 
                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="clickToConvertproformaInvoicetoSalesInvoicesid" onclick="clickToConvertproformaInvoicetoSalesInvoices('<?php echo $table_name; ?>','<?php echo $rid; ?>')">
                                <i class="text-white ti-layers"></i> Convert to Sales Invoice
                            </button>
                    <?php } ?>
                <?php }  ?>
                 &nbsp;  &nbsp; 
                    <!-- <input type="hidden" name="status_values" id="status_values" value="<?php //echo  $table1['current_status']!=""?$table1['current_status']:2; ?>"> -->
                <input type="hidden" name="status_values" id="status_values" value="<?php echo  ($table1['current_status']!="" AND $rtable_name =="")?$table1['current_status']:ST_DRAFT; ?>">
                <input type="hidden" name="status_old_value" id="status_old_value" value="<?php echo $table1['current_status']; ?>">


                </div>
            </div>

            <div class="mt-3 mb-3 row">
                <div class="col-lg-8">
                  <?php  echo $rtable_name ==""?$workflowsteps['breadcrumb']:"";?>
                </div>
                <div class="col-lg-4 text-right">
                    <?php 
                    $invoicetables = array('sales_invoices','purchase_invoices','supplier_bills'); 
                    if(in_array($table_name, $invoicetables) AND $table1['current_status'] == ST_APPROVED AND $rid != "" AND $copy == 1){  ?>
                        <a href="index.php?option=COM_INSERT_DEBIT_NOTES&ref_type=<?php echo $table_name; ?>&ref_id=<?php echo $rid; ?>"  class=" cursor-pointer  btn btn-success" > <i class="icofont icofont-plus icon-white"></i> Debit Note - <?php echo $debit_notes_cnt['cnt']; ?></a>
                        <a href="index.php?option=COM_INSERT_CREDIT_NOTES&ref_type=<?php echo $table_name; ?>&ref_id=<?php echo $rid; ?>" class="cursor-pointer  btn btn-danger" ><i class="icofont icofont-plus icon-white"></i> Credit Note - <?php echo $credit_notes_cnt['cnt']; ?></a>
                    <?php } ?> 
                </div>
            </div>
            <?php 
            $visibledfields = array(ST_DRAFT,ST_ACTIVE,ST_CALLBACK,ST_SEND_BACK);
            $dyncss = ""; 
            if($_GET['Rid']!=""){
                $dyncss = "pointer-events: none; opacity: 0.7;"; 
               // echo "status:".$table1['current_status'].",user:". $table1['originator'].",seesion user:".$_SESSION['userid'].",rtable:".$rtable_name;


                if((in_array($table1['current_status'], (array)$visibledfields) && $rtable_name =="" && $table1['originator'] ==$_SESSION['userid']) || ($rtable_name !="" && $table1['current_status'] == ST_ACTIVE) ){
                    $dyncss = "";
                }
                if($rtable_name !="" && $_GET['Rid']!="" ){
                    $dyncss = "";
                }
                if($table1['ref_type'] =="quotations"){
                    $reftabledyncss = "pointer-events: none; opacity: 0.7;";
                }
            }
            /*if(in_array($table1['current_status'], (array)$visibledfields) && $rtable_name =="" OR ($table1['current_status'] == ST_CALLBACK && $table1['originator'] ==$_SESSION['userid']) OR ($rtable_name !="" && $table1['current_status'] == ST_ACTIVE) ){ 
                echo "status:".$table1['current_status'].",user:". $table1['originator'].",seesion user:".$_SESSION['userid'].",rtable:".$rtable_name;
            }else{
                $dyncss = "pointer-events: none; opacity: 0.7;"; 
            } */
            ?>
            <div  style="<?php echo $dyncss." ".$reftabledyncss; ?>">
            
            <?php  
            //if($table1['qo_number'] !=''){
               $tran_no =  $table1['qo_number'];
            //}
               //echo "<pre>"; print_r($table1); echo "</pre>";
               include_once("custom_insert_quotation_topup.php");   
             ?>
       <?php 
                    if($checktimeprint > 0){
    echo "Top fileds Done Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
                    ?>
                    <div class="kt-portlet__head ">
                        <div class="card-header table-card-header p-1">
                            <h5 class="">Products / Services</h5>
                            <div class="card-header-right table-card-header select_custom">
                                <div class="row">
                                <div class="w-full  col-lg-6">
                                    <select class="form-control form-select js-example-basic-single" id="discount_mode" name= "discount_mode" onchange="changeDiscountMode()" >
                                            <option  <?php if($table1['discount_mode'] =="" OR $table1['discount_mode'] == 'DO'){ ?>selected <?php } ?> value="DO"> Discount Overall </option>
                                            <option  <?php if($table1['discount_mode'] == 'DI'){ ?>selected <?php } ?> value="DI"> Discount Inline </option>
                                    </select>
                                </div>
                                <div class="w-full col-lg-6">
                                    <select class="form-control form-select js-example-basic-single " id="taxtype" name= "tax_type" onchange="calculateTotal()" >
                                            <option  <?php if($table1['taxtype'] == 'E'){ ?>selected <?php } ?> value="E"> Exclusive </option>
                                            <option  <?php if($table1['taxtype'] == 'I'){ ?>selected <?php } ?> value="I"> Inclusive </option>
                                            <option  <?php if($table1['taxtype'] == 'O'){ ?>selected <?php } ?> value="O"> Out Of scope </option>
                                    </select>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- <div class="kt-portlet__head-label float-left">
                            <h3 class="kt-portlet__head-title"> Items/Services </h3>
                        </div>
                        <div style="float: right;">
                             
                         </div> -->
                    </div>
                   
                    <div class="kt-portlet__body1" style="padding: 10px;">

                    </div>
                    <div class="row">

                        <?php 
 $notshowsummarytable = array('journal_vouchersr');
  $ammountsummarycss = "style='display:none'";
  $tablediv =  "col-lg-12";
 if(!in_array($table_name, (array)$notshowsummarytable)){
    $tablediv = "col-lg-9 mt-4";
    $ammountsummarydiv = "col-lg-3 mt-4";
    $ammountsummarycss = "";
 }
?> 
                        <div class="<?php echo $tablediv; ?>">
                            <?php 
                            $array_table = array('requisition'=>'quotation_table_new','petty_cash'=>'petty_cash_table','bills'=>'bill_table','goods_receipt_notes'=>'grn_table','stock_receipts'=>'stock_receive_table','stock_adjust_table'=>'stock_adjust_table','genral'=>'genral_table','account'=>'account_table','esgin_table'=>'esgin_table','vrc_table'=>'vrc_table','mpc_table'=>'mpc_table','glod_table'=>'glod_table', 'grn_return_table'=>'grn_return_table','rental_invoices'=>'rental_invoice_table','sale'=>'sale_table','sales_requisition_quotation_order'=>'sales_requisition_quotation_order_table','delivery_order'=>'delivery_order_table');
                             $display_table =$array_table[$table_list[$option]['display_table']].'.php';  
                            //echo "<pre>"; print_r($table1); echo "</pre>";
                            if(isset($table2[0]['item_id'])){
                                include_once($display_table); 
                            }else{
                                ?>

                                <div class="alert alert-warning alert-dismissible">
                                    <strong><?php echo $_SESSION['error_message']['9135']; ?> </strong> 
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php
                                
                            }
                            ?>
                            <div class="row mt-2">
                                <?php //print_r($table1); ?>
                                <div class="col-xs-12">
                                    <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
                                    <?php  
                                        if(!in_array($table_name,array('grn_returns','sales_returns'))){ 
                                    ?>
                                    <button class="btn btn-success" 
                                    <?php if($table1['multi_reference'] == "Y"){ ?>
                                    onclick="createMultiReferenceDocumnets()"
                                <?php }else{ ?>id="addRows"<?php } ?>
                                     type="button">+ Add More</button>
                                    <?php } ?>
                                </div>
                            </div>
                            
                           <div>
                                <div style="float: left;width: 30%;padding: 1%;">
                                    <label class="d-block">Attachment</label>
                                    <div id="attchmentshowlist" class="custom-scroll" style="max-height: 120px; min-height:120px;">
                                        <?php
                                        $compliance_specification_documents = json_decode($table1['compliance_specification_documents'],1);
                                        if(count((array)$compliance_specification_documents) != 0){
                                        
                                            foreach($compliance_specification_documents as $atchmnt){
                                                $csdatchmt[$atchmnt['item_name']][] = $atchmnt;
                                            };

                                            foreach($csdatchmt as $itmname => $atchmnt){ 
                                                
                                        ?>
                                            
                                    <div class="form-control">
                                        <label class="form-label"><?php echo $itmname; ?></label> 
                                        <?php foreach($atchmnt as $atchmntval){    ?>
                                        <input type="hidden" name="compliance_specification_documents[<?php echo $atchmntval['attachment']; ?>][item_name]" value="<?php echo $itmname; ?>">
                                        <input type="hidden" name="compliance_specification_documents[<?php echo $atchmntval['attachment']; ?>][attachment_name]" value="<?php echo $atchmntval['attachment_name']; ?>">
                                        <input type="hidden" name="compliance_specification_documents[<?php echo $atchmntval['attachment']; ?>][attachment]" value="<?php echo $atchmntval['attachment']; ?>">
                                        <div class="checkbox-fade fade-in-primary" >
                                            <label class="form-label"><input type="checkbox" name="compliance_specification_documents[<?php echo $atchmntval['attachment']; ?>][show_print]" class="" <?php echo $atchmntval['show_print']=='on'?"checked":""; ?>><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><?php echo $atchmntval['attachment_name']; ?></label>
                                            </div>
                                            <?php } ?>
                                        
                                    </div>    

                                    <?php  } }else{ ?> 
                                        <span><i class="ti-alert text-warning"></i> No Attachment</span>
                                    <?php } ?>
                                    
                                </div>
                            </div>
                                <div style="float: left;width: 35%;padding: 1%;">
                                    <label class="">Internal Note(s) </label>
                                    <textarea style="height: 120px;" name="notes" class="form-control mob-custom-width" ><?php echo $table1['notes']; ?></textarea>
                                </div>
                                <div style="float: left;width: 35%;padding: 1%;">
                                    <label class="">Printing Note(s) </label>
                                    <textarea style="height: 120px;" name="printing_notes" id="printing_notes" class="form-control mob-custom-width" ><?php echo $table1['printing_notes']; ?></textarea>
                                </div>
                            </div>
                            <div class="table-responsive" style="padding: 1%;">
                                 <?php if(count((array)$jv_items) !=0 AND $table_name == "purchase_invoices" AND 1 != 1){  ?>
                                    <table   class="table table-bordered">
                                        <tr class="bg-primary" >
                                            <th align="center" colspan="5" class="p-2 text-center">Journal Vouchers</th>
                                        </tr>
                                        <tr> 
                                            <th class="p-2 text-left"><span>Account</span></th>
                                            <th class="p-2 text-left"><span>Account Name</span></th>
                                            <th class="p-2 text-left"><span>Particulars</span></th>
                                            <th class="p-2 text-right">Debit</th>
                                            <th class="p-2 text-right">Credit</th>
                                        </tr>
                                        <?php $debitsum = $creditsum = 0; foreach((array)$jv_items as $jvitems){ 
                                            $debitsum = $debitsum +$jvitems['debit_amount'];
                                            $creditsum = $creditsum +$jvitems['debit_amount'];
                                            ?>
                                        <tr> 
                                            <td class=" p-2 text-left"><span><?php echo $jvitems['account_code']; ?></span></td>
                                            <td class=" p-2 text-left"><span><?php echo $chart_of_accounts[$jvitems['account_code']]; ?></span></td>
                                            <td class=" p-2 text-left"><span><?php echo $jvitems['particulars']; ?></span></td>
                                            <td class="p-2 text-right"><span><?php echo customeummarmonyformate($jvitems['debit_amount']); ?> </span></td>
                                            <td class="p-2 text-right"> <span><?php echo customeummarmonyformate($jvitems['credit_amount']); ?> </span></td>
                                        </tr>
                                    <?php } ?>
                                        <tr> 
                                            <th class="p-2 text-left" colspan="3"><span>Total</span></th>
                                            <th class="p-2 text-right"><?php echo customeummarmonyformate($debitsum); ?> </th>
                                            <th class="p-2 text-right"><?php echo customeummarmonyformate($creditsum); ?> </th>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <?php if(count((array)$project_task_details) !=0){  ?>
                                    <div class="mt-3">
                                    <table   class=" table table-bordered">
                                        <tr class="bg-primary" >
                                            <th colspan="6" class="p-2 text-center">Follow Ups</th>
                                        </tr>
                                        <tr> 
                                            <th class="p-2"><span>#</span></th>
                                            <th class="p-2"><span>Task Name</span></th>
                                            <th class="p-2"><span>Task Date</span></th>
                                            <th class="p-2 text-center">Status</th>
                                            <th class="p-2 text-center">Action</th>
                                        </tr>
                                        <?php $i=1; foreach((array)$project_task_details as $atl){
                                            ?>
                                        <tr> 
                                            <td class=" p-2 text-left"><span><?php echo $i; ?></span></td>
                                            <td class=" p-2"><span><?php echo $atl['task_name']; ?></span></td>
                                            <td class=" p-2"><span><?php echo fromDate($atl['task_date']); ?></span></td>
                                            <td class="p-2 text-center">
                                                <label class="form-label label label-md" style="color:<?php echo $statuse[$atl['current_status']]['color']; ?>; background-color:<?php echo $statuse[$atl['current_status']]['background_color']; ?>;">
                                                    <?php echo $statuse[$atl['current_status']]['name']; ?>
                                                </label>
                                            </td>
                                            <td class="p-2 text-center">
                                                <label class="form-label label label-md label-primary" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Details" onclick="getApplicationTaskLogsPopUp('<?php echo $atl['id']; ?>')"><i class="ti-eye"></i></label>
                                                
                                            </td>
                                        </tr>
                                    <?php $i++; } ?>
                                    </table>
                                    </div>
                                <?php } ?>
                             </div>
                            <?php if($small_sub == 0  ){ ?>
                            <div style="margin-top: 10px;clear:both;">
                                <label class="">Comment(s)</label>
                                <textarea style="height: 120px;" name="comments" class="form-control" id="comments" ><?php echo $comments; ?></textarea>
                            </div>
                            <div class="btn-group" style="margin-top: 10px;float: right;">
                                <button id="kt_llogin_signup_submit"  class="btn btn-brand" >Submit1</button>
                            </div>
                            <?php }  ?>           
                        </div>

                        <div class="<?php echo $ammountsummarydiv; ?> p-r-0" <?php echo $ammountsummarycss; ?>>
                            <ul class="nav md-tabs infisuite_tab mb-2 mt-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link f-w-600 f-12 active originatercurrencycalc_tab" data-bs-toggle="tab" href="#originatercurrencycalc" role="tab" aria-selected="true">Amount</a>
                                </li>
                                <li class="nav-item exchange" <?php echo $currency_conversion_style; ?>><a class="nav-link f-w-600 f-12 suppliercurrencycalc_tab" data-bs-toggle="tab" href="#suppliercurrencycalc" role="tab" aria-selected="true">Currency Conversion</a></li>
                            </ul>

                        <div class="tab-content tabs">
                            <div class="tab-pane card-body active p-0 originatercurrencycalc_tab" id="originatercurrencycalc" role="tabpanel"> 
                        <div style="position: sticky;top: 20px;">
                        <table class=" table table-bordered custom_dynamic_transaction_table" >
                            <tr class="bg-primary" ><th colspan="2" class="p-2 text-center">Order Summary</th></tr>
                            <tr>
                                <th class="amount text-start p-2" width="40%" style="width:40%"><span>Sub Total</span></th>
                                <th class="p-2 text-right" width="60%" style="width:40%">
                                    <span id="dis_subTotalid">
                                        <?php echo customeummarmonyformateifine($table1['sub_total']); ?>
                                    </span>
                                    <input  type="hidden" class="form-control amount" readonly value="<?php echo customeummarmonyformate($table1['sub_total']); ?>" name="dis_subTotal" id="dis_subTotal" placeholder="Subtotal" >
                                    <input  type="hidden" class="form-control amount" readonly value="<?php echo $table1['sub_total']; ?>" name="subTotal" id="subTotal" placeholder="Subtotal" >
                                </th>
                            </tr>
                            <tr>
                                <th class="amount text-start p-2 <?php if($table1['discount_mode'] == 'DI'){ echo "event_disabled"; } ?>" id="overall_discount_div">
                                    <div class="form-radio">
                                    <span>Discount </span>
                                    <div class="radio radio-inline m-0">
                                        <label class="d-inline-block m-l-5">
                                            <input type="radio"  name="discount_type" class="calculateTotal_discount" id="disType" onclick="calculateTotal()" value="A" <?php echo $table1['discount_type'] == 'A'?"checked":""; ?> >
                                            <i class="helper"></i><?php echo $_SESSION['config']['currency_symbol']; ?>
                                        </label>
                                        <label class="m-l-10 d-inline-block">
                                            <input type="radio" name="discount_type" class="calculateTotal_discount" id="disType" onclick="calculateTotal()" value="P" <?php echo ($table1['discount_type'] == 'P' OR $table1['discount_type'] =="")?"checked":""; ?>>
                                            <i class="helper"></i> %
                                        </label>
                                    </div>
                                </div>
                                    <input value="<?php echo $table1['discount_value']; ?>" type="text" class="form-control amount mt-2 text-right decimalnumberallowe" onblur="discountRestrictions()"  placeholder="Enter value" name="discount_value" id="disValue" > 
                                </th> 
                                <td class="p-2 align-middle text-right">
                                    <span id="dis_discountid">
                                        <?php echo customeummarmonyformateifine($table1['discount_amount']); ?>
                                    </span>
                                    <input value="<?php echo customeummarmonyformate($table1['discount_amount']); ?>"  readonly type="hidden" class="form-control amount" name="dis_discount_amount" id="dis_discount"  placeholder="Discount Amount">
                                    <input value="<?php echo $table1['discount_amount']; ?>"  readonly type="hidden" class="form-control amount" name="discount_amount" id="discount" placeholder="Discount Amount">
                                </td>
                            </tr> 
                            <?php if($table_name != 'bills'){  ?>
                                <?php $mk=1; foreach((array)$additional_charges as $addchrg){
                                    $alreadyselectac[$addchrg['charge_id']]= $addchrg['charge_id'];
                                    $rowid = "row_additional_charges_".$mk;
                                    ?>
                                    <tr id="<?php echo $rowid; ?>">
                                        <td class="amount text-start p-2" width="40%" style="width:40%">
                                             <i class="ti-trash text-danger" onclick="removeAdditionalCharges('<?php echo $rowid; ?>','<?php echo $addchrg['charge_id'];?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove"></i>
                                             <?php echo $addchrg['charge_name'];?></td>
                                        <td class="p-2 text-right" width="60%" style="width:40%"><?php echo $addchrg['shipping_amount']." (".$addchrg['shipping_tax']."%)";?>
                                            
                                            <input type="hidden" class="class_shipping_tax" data-id="<?php echo $rowid; ?>" name="shipping_tax_ac[]" id="shipping_tax_<?php echo $rowid; ?>" value="<?php echo $addchrg['shipping_tax']!=""?$addchrg['shipping_tax']:0;?>">
                                            <input type="hidden" name="shipping_amount_ac[]" id="shipping_amount_<?php echo $rowid; ?>" value="<?php echo $addchrg['shipping_amount'];?>">
                                            <input type="hidden" name="shipping_tax_amount_ac[]" id="shipping_tax_amount_<?php echo $rowid; ?>" value="<?php echo $addchrg['shipping_tax_amount'];?>">
                                            <input type="hidden" name="charge_name_ac[]" value="<?php echo $addchrg['charge_name'];?>">
                                            <input type="hidden" name="charge_id_ac[]" value="<?php echo $addchrg['charge_id'];?>">
                                        </td>
                                    </tr>
                                <?php $mk++; } ?>

                            <tr id="rowadditionalcharges">
                                 <input type="hidden" name="" id="tds_tax" value="0">
                                 <input  value="<?php echo $table1['shipping_amount']; ?>"   type="hidden" class="form-control amount" name="shipping_amount" id="shipping_amount"  placeholder="shipping Amount">
                                 <input type="hidden" name="shipping_tax_amount" id="shipping_tax_amount" value="<?php echo $table1['shipping_tax_amount']; ?>">
                                <th class="amount text-start p-2" colspan="2" id="custom-popup">
                                    <label class="form-label label label-md label-primary"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Add More Additional Charges" onclick="OpenAdditionalCharges()"><i class="ti-plus"></i> Additional Charges</label>

                                   <div class="custom-popup-open custom-popup-additionalcharges-open text-start" style="display: none; right: 0; top: auto;">
                                        <div class="card">
                                            <div class="card-header">
                                              <h5>Additional Charges</h5>
                                              <div class="card-header-right">
                                                <i class="ti-close" onclick="closeAdditionalCharges()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                                              </div>
                                            </div>
                                            <div class="card-body" > 
                                                <div class="row">
                                                    <div class="form-group col-lg-8">
                                                        <label class="form-label">Charge Name</label>
                                                        <select class="form-select js-example-basic-single"  name="additional_charge_id" id="additional_charge_id"  >
                                                            <?php foreach ((array)$additional_charges_list as $acharges) {  ?>
                                                            <option <?php echo (in_array($acharges['id'], (array)$alreadyselectac))?"disabled":""; ?> value = <?php echo $acharges['id'];  ?>   > <?php echo $acharges['charge_name']; ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="text-danger" id="error_msg"></span>
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        <label class="form-label">Amount</label>
                                                        <input  value="0" type="text" class="form-control amount decimalnumberallowe" id="additional_charge_amount">
                                                    </div>
                                                </div>     
                                            </div>
                                            <div class="card-footer pt-0">
                                                <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="addAdditionalCharges()"> Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <?php  /**  ?>
                            <tr>
                                <th class="amount text-start p-2">
                                    <div class="">  
                                        <select class="form-select js-example-basic-single"  name="shipping_tax" id="shipping_tax"  >
                                            <option value="0">Additional Charges</option>
                                            <?php foreach ($additional_charges as $acharges) {  ?>
                                            <option  <?php if($acharges['id'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $acharges['charge_name']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <input type="hidden" name="" id="tds_tax" value="0">
                                    <label class="form-label badge bg-danger"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Add More Additional Charges"><i class="ti-plus"></i></label>
                                </th>
                                <td class="p-2 align-middle">
                                    <input type="hidden" name="shipping_tax_amount" id="shipping_tax_amount" value="<?php echo $table1['shipping_tax_amount']; ?>">
                                    <input  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="shipping_amount"  placeholder="shipping Amount">
                                </td>
                            </tr>
                            
                            <tr>
                                <th class="amount text-start p-2">
                                    <span >Shipping</span>
                                    <div class="">  
                                        <select class="form-select js-example-basic-single"  name="shipping_tax" id="shipping_tax" onchange="calculateTotal()" >
                                            <option value="0">Tax</option>
                                            <?php foreach ($taxres as $key => $tax) {  ?>
                                            <option  <?php if($table1['shipping_tax'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <input type="hidden" name="" id="tds_tax" value="0">
                                </th>
                                <td class="p-2 align-middle">
                                    <input type="hidden" name="shipping_tax_amount" id="shipping_tax_amount" value="<?php echo $table1['shipping_tax_amount']; ?>">
                                    <input  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="shipping_amount" onblur="calculateTotal()" placeholder="shipping Amount">
                                </td>
                            </tr> 
                            <?php **/ ?>
                        <?php  } ?> 
                        <tr class="p-0" id="display_taxshow" style="<?php echo count((array)$distax) =="0"?"display:none":""?>">
                            <td colspan="2" class="p-2">
                                <?php //echo "<pre>"; print_r($distax);echo "</pre>";  //exit; ?>
                            <table class=" table table-bordered mb-0">
                                <tbody id='display_tax'>
                         <?php  if(count((array)$distax)){
                                    foreach((array)$distax as $ktax){  $tattax +=$ktax['tax_amount']; ?>
                                    <?php //if($table1['tax_state'] == 'S'){ 
                                        if($table1['originator_state_id'] == $table1['supplier_state_id']){ ?>
                                    <tr>
                                        <td class="p-2">  <?php echo $split_1_name; ?>  @ <?php echo $ktax['percentage']*$split_1; ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>                
                                        </td>
                                        <td class="text-end p-2"> 
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']*$split_1); ?></span> 
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']*$split_1); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2">  <?php echo $split_2_name; ?> @ <?php echo $ktax['percentage']*$split_2; ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>               
                                        </td>
                                        <td class="text-end p-2"> 
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']*$split_2); ?></span> 
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']*$split_2); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">           
                                        </td>
                                    </tr>
                                <?php } else{  ?> 
                                    <tr>
                                        <td class="p-2">  <?php echo $split_1_name; ?> @ <?php echo $ktax['percentage']; ?>% on <?php echo customeummarmonyformate($ktax['amount']); ?>         
                                        </td>
                                        <td class="text-end p-2">  
                                            <span id="dis_taxRateid"><?php echo customeummarmonyformateifine($ktax['tax_amount']); ?></span>
                                            <input  value="<?php echo customeummarmonyformate($ktax['tax_amount']); ?>" type="hidden" class="form-control amount" name="dis_taxRate" id="" readonly placeholder="Tax Amount">            
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php } }  ?>
                                        <tr>
                                            <td class="p-2">Total Tax</td>
                                            <td class="text-end p-2"><span id="dis_taxRateid"><?php echo customeummarmonyformateifine($tattax); ?></span> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                            <?php if($table_name == 'bills'){ ?>
                            <tr>
                                <th class="amount p-2">
                                    <div> 
                                        <div style="float: left;width: 50%;padding: 10px;">
                                            <span >TDS</span>
                                        </div>
                                        <div style="float: left;width: 50%;padding: 3px;">  
                                            <select class="form-control"  name="shipping_tax" id="tds_tax" onchange="calculateTotal()" >
                                                <option value="0"> Select Any Tax Rate </option>
                                                <?php foreach ((array)$tdstaxres as $key => $tax) {  ?>
                                                <option  <?php if($table1['shipping_tax'] == $key){ ?> selected  <?php } ?> value = <?php echo $key; ?>   > <?php echo $tax; ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>  
                                </th>
                                <td>
                                    <input type="hidden" name="shipping_tax_amount" id="tds_tax_amount" value="0">
                                    <input  value=""   type="hidden" class="form-control amount" id="shipping_amount" onblur="calculateTotal()" placeholder="TDS Amount">
                                    <input  value="<?php echo $table1['shipping_amount']; ?>"   type="text" class="form-control amount" name="shipping_amount" id="tds_amount" onblur="calculateTotal()" placeholder="TDS Amount">
                                </td>
                            </tr>
                        <?php } ?>
                             <?php 
                            $adpaymenttable = array('sales_orders','sales_invoices','purchase_orders','purchase_invoices'); 
                               
                           // if($table1['advance_payment'] !="" AND $table1['advance_payment'] !="0"){ 
                               if(in_array($table_name, $adpaymenttable)){
                                    
                            ?>
                            <tr >
                                <th class="p-2  text-left" id="custom-popup">Advance Payments 
                                     <div class="custom-popup-open popup_advance_payment text-start" style="top:35px; width: 310px; position: absolute; right :150px">
                                        <div class="card latest-update-card" id="experience_details_div">
                                            <div class="card-header">
                                                <h5>Adjust Advance Payment</h5>
                                                <div class="card-header-right">
                                                  <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeAdjustAdvancePayment()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6 form-group">
                                                        <label class="form-label">Available Advance payments</label>
                                                        <span class="d-block" id="pendingadvancepayments"> </span>
                                                        <input type="hidden"  id="pendingadvancepaymentstext" value="">
                                                    </div>
                                                    <div class="col-lg-6 form-group">
                                                        <label class="form-label">Adjust Payment</label>
                                                        <input type="number" class="form-control" placeholder="" id="enteradjustadvancepayment"  value="" onblur="enterAdjustAdvancePayment(this.value)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </th>
                                <td class="p-2 text-right f-w-600" >
                                     <span ><span id="advance_payment_text"><?php echo $table1['advance_payment']!=""?customeummarmonyformate($table1['advance_payment']):customeummarmonyformate(0); ?> </span>
                                     <?php 
                                     if($table_name == "sales_invoices" OR $table_name == "purchase_invoices"){
                                        //AND $table1['advance_payment']!="" AND $table1['advance_payment']!="0"
                                    ?>
                                     <i class="ti-pencil" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Adjust Advance Payment" onclick="openAdvancePaymentsPopup()"></i>
                                     <input type="hidden" name="advance_payment" id="advance_payment_textbox" value="<?php echo $table1['advance_payment']!=""?$table1['advance_payment']:0; ?>">
                                    <input type="hidden" name="old_advance_payment" id="advance_payment_textbox" value="<?php echo $table1['advance_payment']!=""?$table1['advance_payment']:0; ?>">
                                    <?php } ?>
                                 </span> 
                                   

                                </td>
                            </tr>
                        <?php } //} ?>
                        <?php if($table_name == "sales_invoices"){ ?>
                            <tr >
                                <th class="p-2  text-left">TCS</th>
                                <td class="p-2 text-right f-w-600" ><?php echo $table1['total_tds_amount'] !=""?$table1['total_tds_amount']:0; ?></td>
                            </tr>


                        <?php } ?>
                        <?php if($table1['security_deposit'] !="" AND $table1['security_deposit'] !="0"){ ?>
                            <tr >
                                <th class="p-2  text-left">Security Deposit</th>
                                <td class="p-2 text-right f-w-600" ><?php echo $table1['security_deposit'] !=""?$table1['security_deposit']:0; ?></td>
                            </tr>


                        <?php } ?>
                            <tr>
                                <th class="amount p-2 text-start " >
                                    <span>Grand Total</span>
                                    <input  type="hidden" class="form-control amount" placeholder="value" name="gdam" id="gdam">
                                    <input  type="hidden" class="form-control amount" placeholder="value" name="div_amount_total" id="div_amount_total">
                                    <input  value="<?php echo $table1['tax_amount']; ?>" type="hidden" class="form-control amount" name="taxRate" id="taxAmount" readonly placeholder="Tax Amount">
                                </th>
                                <th class="p-2 text-right">
                                    <span id="grndtotalid"><?php echo customeummarmonyformateifine($table1['grand_total']+$table1['total_tds_amount']); ?> </span>
                                    <input  value="<?php echo customeummarmonyformate($table1['grand_total']); ?>" type="hidden" class="form-control amount" name="dis_grandtotal" id="dis_grandtotal" readonly placeholder="Grand Total"><input  value="<?php echo $table1['grand_total']; ?>" type="hidden" class="form-control amount" name="grandtotal" id="grandtotal" readonly placeholder="Grand Total">
                                </th>
                            </tr>
                            <tr>
                                <th class="amount p-2 text-start "><span >Rounding Off</span></th>
                                <td class="p-2 text-right"><span id="rounding_off"><?php echo $table1['rounding_off']!=""?customeummarmonyformateifine($table1['rounding_off']):0; ?> </span></td>
                            </tr>
                            <tr style="display: none;">
                                <th class="amount" ><span >Amount Paid</span></th>
                                <td><input value="" type="text" class="form-control amount" name="amountPaid" id="amountPaid" placeholder="Amount Paid"></td>
                            </tr>
                            <tr style="display: none;">
                                <td  ><span >Amount Due</span></td>
                                <td><input value="" type="text" class="form-control amount" name="amountDue" id="amountDue" placeholder="Amount Due"></td>
                            </tr>
                            
                            <tr class="p-0" id="display_paymentstermsshow">
                                <?php $payment_term_history = json_decode($table1['payment_term_history'],1); 
                                if(count((array)$payment_term_history) !='0'){
                                ?>
                                <td colspan="2" class="p-2">
                                    <table class=" table table-bordered mb-0">
                                <?php $mjk=1; foreach((array)$payment_term_history as $pth){?>
                                    <tr> 
                                        <td class="p-2"><?php echo fromDate($pth['due_date']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformate($pth['amount']); ?> 
                                        <?php if($pth['extra_date'] !="" AND $pth['extra_amt'] !=""){?>
                                        <span class='d-block f-10 f-w-600'>Before Date <?php echo fromDate($pth['extra_date'])." (".customeummarmonyformate($pth['extra_amt']).")"; ?></span> <?php } ?></td>
                                    </tr>
                                        <?php echo '<input type="hidden" name="payment_term_history['.$mjk.'][due_date]" value="'.$pth['due_date'].'">
                                            <input type="hidden" name="payment_term_history['.$mjk.'][amount]" value="'.$pth['amount'].'">
                                            <input type="hidden" name="payment_term_history['.$mjk.'][extra_date]" value="'.$pth['extra_date'].'">
                                            <input type="hidden" name="payment_term_history['.$mjk.'][extra_amt]" value="'.$pth['extra_amt'].'"><input type="hidden" name="payment_term_history['.$mjk.'][percentage]" value="'.$pth['percentage'].'"><input type="hidden" name="payment_term_history['.$mjk.'][without_tax_amount]" value="'.$pth['without_tax_amount'].'"><input type="hidden" name="payment_term_history['.$mjk.'][tax_amount]" value="'.$pth['tax_amount'].'">';
                                            $mjk++; } ?>
                                    </table>
                                </td>
                                <?php } ?>
                            </tr>
                        </table>
                       </div> 
                   </div>
                    <div class="tab-pane card-body p-0 suppliercurrencycalc_tab" id="suppliercurrencycalc" role="tabpanel">
 <?php //if($table1['currency_id'] != $_SESSION['config']['default_currency_id'] && $table1['currency_id'] != '0'){ $css = ""; }else{ $css = 'style="display: none;"'; } 
//if($table1['originator_currency_id'] != $table1['currency_id'] && $table1['currency_id'] != '0' && $table1['currency_id'] != '' && $table1['originator_currency_id'] != '0' && $table1['originator_currency_id'] != ''){ $css =''; }else{ $css = 'style="display: none;"'; }
     ?>
                        <table   class=" table table-bordered exchange" <?php echo $currency_conversion_style; ?>>
                                    <tr class="bg-primary" >
                                        <th colspan="2" class="p-2 text-center">Order Summary</th>
                                    </tr>
                                    <tr>
                                        <th class="p-2 text-left"><span >Sub total</span ></th>
                                        <td class="p-2 text-right f-w-600">
                                             <span id="ex_sub_total"><?php echo customeummarmonyformate($table1['sub_total']*$table1['currency_rate']); ?></span> 
                                        </td>
                                     </tr> 
                                     <tr> 
                                        <th class=" p-2 text-left"><span>Tax</span></th>
                                        <td class="p-2 text-right">
                                             <span id="ex_tax"><?php echo customeummarmonyformate($table1['tax_amount']*$table1['currency_rate']); ?></span> 
                                        </td>
                                    </tr>
                                    <tr>    
                                        <th class="p-2  text-left">Discount</th>
                                        <td class="p-2 text-right ">
                                             <span id="ex_discount"><?php echo customeummarmonyformate($table1['discount_amount']*$table1['currency_rate']); ?></span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="p-2  text-left">Grand Total </th>
                                        <td class="p-2 text-right f-w-600">
                                             <span id="ex_grand_total"><?php echo customeummarmonyformate(($table1['grand_total']+$table1['total_tds_amount'])*$table1['currency_rate']); ?></span> 
                                        </td>
                                    </tr>
                                    
                            </table>

                    </div>                
               </div>




                </div> 
            </div>      
        </div>
    </div>
    <?php
//print_r($file_data);
if($rid !='' && isset($file_data['file_upload']) && $file_data['file_upload']==0 ){
 $file_array = array('application_table_name'=>$table_name,'transaction_id'=>$rid ,'is_active'=>'0','width'=>$file_data['fileuplodingwidth']!=""?$file_data['fileuplodingwidth']:'6','deletewidth'=>$file_data['fileuplodingdleetewidth']!=""?$file_data['fileuplodingdleetewidth']:'6','deleteview'=>$file_data['fileuplodingdeleteddisplay']!=""?$file_data['fileuplodingdeleteddisplay']:1,'file_size'=>$file_data['file_size']!=""?$file_data['file_size']:'6','file_limit'=>$file_data['number_of_files']!=""?$file_data['number_of_files']:'6','lable'=>$file_data['fileuplodinglabel']);
   fileUplodingUi($file_array);
 } ?>
</div>
 <?php 
                    if($checktimeprint > 0){
    echo "Grand fileds Done Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
                    ?>

<?php 
    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,'');
    if(in_array($table1['current_status'], (array)$check_array) AND $rtable_name =="" OR ($table1['current_status'] == ST_CALLBACK AND $table1['originator'] ==$_SESSION['userid'])){
            $disable = "" ;
     }else{ 
        if($rtable_name !=""){
            $disable = "" ;
        }else{
            $disable = "disabled" ;
        }
         
    }

    ?>
            <div class="card-footer">
                    <div class="row" style="<?php echo $dyncss; ?>">
                        <div class="col-lg-10">
                            <div id="item_validation_details"></div>
                             <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
                             <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                             <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                             <input type="hidden" name="old_place1" id="old_place1" value="<?php echo $location_state;?>">
                            <input type="hidden" name="currency_operater" id="currency_operater" value="<?php echo $currency_operater;?>">
                            <input type="hidden" name="amendment" id="amendment_id" value="N">
                            <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $copy == '1'){ ?>
                                    <input type="hidden" id="hid"name="hid" value="<?php echo $rid; ?>" />
                                    <input type="hidden" name="hid_itemds" value="<?php echo $item_ids; ?>" />
                                    <input type="hidden" name="submit1" value="Update" />
                                    <!-- <button  id="btndisableck1" <?php echo $disable; ?> class="btn btn-primary mr-2"  ><i class="la la-check"></i>Update</button> -->
                                    <button <?php echo $disable; ?> class="btn btn-primary mr-2"  ><i class="la la-check"></i>Update</button>
                                    <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                            <?php } else{ ?>
                                <input type="hidden" id="hid"name="hid" value="0" />
                                    <input type="hidden" name="submit1" value="Add" />
                                    <input type="hidden" name="submit_value" id="submit_value_btn" value="exit" />
                                    <button type="button" class="btn btn-primary mr-2 mt-2"  onclick="submitTransactions('exit')" <?php echo $disable; ?>><i class="la la-check" ></i>Save & exit</button>
                                    
                                    <button type="button" class="btn btn-primary mr-2 mt-2" onclick="submitTransactions('continue')" <?php echo $disable; ?>><i class="la la-check-circle" ></i>Save & continue</button>
                                    

                                    <!-- <button class="btn btn-primary mr-2 mt-2"   name="submit_value" value="exit" <?php echo $disable; ?>><i class="la la-check" ></i>Save & exit</button>
                                    <button  class="btn btn-primary mr-2 mt-2" name="submit_value" value="continue" <?php echo $disable; ?>><i class="la la-check-circle" ></i>Save & continue</button>
                                     -->

                                    <!-- <button id="btndisableck12" class="btn btn-primary mr-2 mt-2"   name="submit_value" value="exit" <?php echo $disable; ?>><i class="la la-check" ></i>Save & exit</button>
                                    <button  class="btn btn-primary mr-2 mt-2" id="btndisableck1"  name="submit_value" value="continue" <?php echo $disable; ?>><i class="la la-check-circle" ></i>Save & continue</button> -->
                                    <button type="reset" class="btn btn-secondary mt-2" onclick="location.href='index.php?option=<?php echo $list; ?>';"><i class="la la-close"></i>Cancel</button>
                            <?php } ?>  
                        </div>
                        
                        <div class="col-lg-2 text-right mt-2 mb-2">
                            <!-- <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button> -->
                        </div> 
                    </div>
            </div>
             <!-- <button  id="btndisableck1" class="btn btn-primary mr-2"  ><i class="la la-check"></i>Update</button> -->
        </form>
    </div> 
    </div>
</div>
 
</div>
<?php 
                    if($checktimeprint > 0){
    echo "Before timelines Check Step - ".$checktimeprint. " Date Time : ".date('m/d/Y h:i:s a')."</br>";
    $checktimeprint++;
}
                    ?>
<!--END::Submit part -->

    <?php 
    $passid = $rpdata['id'];
    if($rtable_name!=""){
        $passid = "";
    }
    echo $todisplaythree = todisplaythree(array('table'=>$table_name,'originator'=>$rpdata['created_by'],'encode'=>'no','id'=>$passid,'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes','dis_track'=>'yes'));  
//echo $todisplaytracking = todisplaytracking('s');
?>
<!-- <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 800px;">
        <div class="modal-content" style="min-height: 50px;width: 800px;" id="popup1">
            
        </div>
    </div>
</div> -->
<script type="text/javascript">
   
</script>
</div></div>
