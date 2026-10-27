<?php 
include('includes/session.php');
//print_r($_SESSION); exit;	
include('dbconfig.php');
include('comm.php');
 $page = $_POST['page'];
 $response = 'Empty';
// if($page=="selectadditionalcharge"){
//     $curl = API."?mode=getsetupdetails&getsetupdetails=Y&additional_charge_id=".$_POST['additional_charge_id']; 
// 	echo $cudata = callAPI($curl);
//     //$res = json_decode($cudata,1);
//    	//echo  json_encode($res);
// }
if($page=="updateprojecttaskdetailsfollowup"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updateprojecttaskdetailsfollowup&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}



 /**
if($page=="updatepropertytenantbillings"){
	$_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
   	$url = API."?mode=updatepropertytenantbillings&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    if(isset($res['rental_invoices'])){
    	$rental_invoice_id = $res['rental_invoices']['rental_invoice_id'];
    	$res['rental_invoice_id'] = $rental_invoice_id;
    	$res['decode_rental_invoice_id'] = deocdeId("id:".$rental_invoice_id);
    }
   	echo  json_encode($res);
}
if($page=="gettenantproperty"){
	$url1 = API."?mode=gettenantproperty&type=".$_POST['type']."&valu=".$_POST['valu'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$html = '';
	$property_tenant_details = $result['result'];
	if($_POST['type'] == 'getcustid'){
		$html .= '<option value="">Select Any One</option>';
		foreach($property_tenant_details as $pod){
			$html .='<option value="'.$pod['id'].'">'.$pod['value'].'</option>'; 
		}
	}elseif($_POST['type'] == 'getproperty_tenant_detail_id'){
		$html .= '<option value="">Select Any One</option>';
		foreach($property_tenant_details as $pod){
			$html .='<option value="'.$pod['id'].'">'.$pod['month_start_date'].'-'.customeummarmonyformate(($pod['total_amount']!=''|| $pod['total_amount'] !=0)?$pod['total_amount']:0).'</option>'; 
		}
	}elseif($_POST['type'] == 'getproperty_tenant_billings_id'){
		$property_tenant_billing_id = $_POST['valu'];
		$bill_detail = json_decode($property_tenant_details['bill_detail'],1);
		$html .='<input type="hidden" name="property_tenant_billing_idd" id="property_tenant_billing_idd" value="'.$property_tenant_billing_id.'"><form method="post" id="form_id_new_'.$property_tenant_billing_id.'">';
		$html .='<table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
					<thead>
						<tr>
							<th width="5%">#</th>
							<th width="37%">Utility</th>
							<th width="18%" class="text-right">Amount</th>
							<th width="30%">Remark</th>
						</tr>
					</thead>
					<tbody>';
						 $i=1;foreach($bill_detail as $bd){ 
						 	$redonlytext = $extrlebl = '';
							if($bd['transaction_table']=='property_owner_detail_areas' OR $bd['utility_bill_auto_and_fix']=='F'){
								$redonlytext = 'readonly';
								$extrlebl = "<span>Rent (".$bd['monthly_rent'].")+ Tax (".$bd['monthly_tax'].")</span>";
							}
							$html2 .='<tr>
								<td  class="p-1">'.$i.'</td>
								<td class="p-1"><span class="d-block">'.$bd['name'].'</span>'.$extrlebl.'</td>
								<td class="p-1"><input class="form-control text-right checkamountnotepmty_new_'.$property_tenant_billing_id.'" type="number" name="rent[]" value="'.$bd['rent'].'" '.$redonlytext.'></td>
								<td class="p-1">
									<textarea class="form-control" name="remarks[]">'.$bd['remarks'].'</textarea>
								</td>
							</tr>';
		 			 	$i++; }
		 		$html .=$html2;	 	
		 		$html .='</tbody></table>';
	}
	echo $html;
}
if($page=="insertdebitnote"){
	$_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
   	$url = API."?mode=insertdebitnote&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
   	echo  $response1;
}
if($page=="insertcreditnote"){
	$_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
   	$url = API."?mode=insertcreditnote&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
   	echo  $response1;
}
if($page=="getassetcategorydetails"){
	$returnary = array();
	 $data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','id'=>$_POST['category_id']);
	  $data1["field"] = 'id,category_description,depreciation_account,disposal_account,Journal_id,duration,duration_type,default_depn_type';
	  $data = urlencode(json_encode($data1));
	  $curl = API."?mode=gettabledetailswithconditions&table=asset_categories&data=".$data; 
	  $cudata = callAPI($curl);
	  $cudata = json_decode($cudata,1);
	  $cudata = $cudata[0];
	  $returnary = $cudata;
	  //print_r($cudata);


	
	$depreciation_method = $cudata['default_depn_type'];
	$duration = $cudata['duration'];
	$capitalization_date = $_POST['capitalization_date'];
	$depn_rate = $_POST['depn_rate'];
	 
	$end_of_life=date('Y-m-d', strtotime('+'.$duration.' year', strtotime($capitalization_date)));
	$returnary['end_of_life'] =fromDate($end_of_life);
	$seq = 1;
	$html = '<table class="table table-hover m-b-0" style="font-size:13px;"><thead>
					<tr>
						<th>#</th>
						<th>Depreciation Date</th>
						<th class="text-right">Depreciation</th>
						<th class="text-right">Accumulated Depreciation</th>
						<th class="text-right">Book Value</th>
					</tr>
			</thead><tbody>';
			$html .= '<tr><td class="p-2">'.$seq.'</td>
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$capitalization_date.'">'.fromDate($capitalization_date).'</td>
				<td class="p-2 text-right"><input type="hidden" name="per_year_deprciation[]" value="0">0</td>
				<td class="p-2 text-right"><input type="hidden" name="accumulated_depreciation[]" value="0">0</td>
				<td class="text-right p-2"><input type="hidden" name="depreciation_price[]" value="'.$depn_rate.'">'.customeummarmonyformate($depn_rate).'</td>
			</tr>';
		
	$depreciationprice = $depn_rate;
	if($depreciation_method == 'SL'){
		$peryeardeprciation = $depn_rate;
		if($duration !=0 AND $duration !=''){
			$peryeardeprciation = ($depn_rate/$duration);
		}else{
			$duration = 1;
		}
		for($i=1;$i<=$duration; $i++){
		 	$depreciationdate=date('Y-m-d', strtotime('+'.$i.' year', strtotime($capitalization_date)));
			$depreciationprice = $depreciationprice - $peryeardeprciation;
			$accumulateddep = $depn_rate-$depreciationprice;
			$html .= '<tr><td class="p-2">'.$seq+$i.'</td>
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.fromDate($depreciationdate).'</td>
				<td class="p-2 text-right"><input type="hidden" name="per_year_deprciation[]" value="'.$peryeardeprciation.'">'.customeummarmonyformate($peryeardeprciation).'</td>
				<td class="p-2 text-right"><input type="hidden" name="accumulated_depreciation[]" value="'.$accumulateddep.'">'.customeummarmonyformate($accumulateddep).'</td>
				<td class="text-right p-2"><input type="hidden" name="depreciation_price[]" value="'.$depreciationprice.'">'.customeummarmonyformate($depreciationprice).'</td>
			</tr>';	
			//echo $i." -- ".$depreciationdate." -- ".$depreciationprice." --- ";
		}


	}elseif($depreciation_method == 'DDBD'){

		$peryearpercentage = ((100/$duration)*2);
		for($i=1;$i<=$duration; $i++){
		 	$depreciationdate=date('Y-m-d', strtotime('+'.$i.' year', strtotime($capitalization_date)));
		 	$peryearlessprice = (($depreciationprice/100)*$peryearpercentage)*2;
			$depreciationprice = $depreciationprice -$peryearlessprice;
			
			$accumulateddep = $depn_rate-$depreciationprice;
			$html .= '<tr><td class="p-2">'.$seq+$i.'</td>
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.fromDate($depreciationdate).'</td>
				<td class="p-2 text-right"><input type="hidden" name="per_year_deprciation[]" value="'.$peryearlessprice.'">'.customeummarmonyformate($peryearlessprice).'</td>
				<td class="p-2 text-right"><input type="hidden" name="accumulated_depreciation[]" value="'.$accumulateddep.'">'.customeummarmonyformate($accumulateddep).'</td>
				<td class="text-right p-2"><input type="hidden" name="depreciation_price[]" value="'.$depreciationprice.'">'.customeummarmonyformate($depreciationprice).'</td>
			</tr>';	
		}


	}else{

		$peryearpercentage = ((100/$duration)*2);
		for($i=1;$i<=$duration; $i++){
		 	$depreciationdate=date('Y-m-d', strtotime('+'.$i.' year', strtotime($capitalization_date)));
		 	$peryearlessprice = (($depreciationprice/100)*$peryearpercentage);
			$depreciationprice = $depreciationprice -$peryearlessprice;
			
			$accumulateddep = $depn_rate-$depreciationprice;
			$html .= '<tr><td class="p-2">'.$seq+$i.'</td>
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.fromDate($depreciationdate).'</td>
				<td class="p-2 text-right"><input type="hidden" name="per_year_deprciation[]" value="'.$peryearlessprice.'">'.customeummarmonyformate($peryearlessprice).'</td>
				<td class="p-2 text-right"><input type="hidden" name="accumulated_depreciation[]" value="'.$accumulateddep.'">'.customeummarmonyformate($accumulateddep).'</td>
				<td class="text-right p-2"><input type="hidden" name="depreciation_price[]" value="'.$depreciationprice.'">'.customeummarmonyformate($depreciationprice).'</td>
			</tr>';	
		}
	}	

	$html .= '</tbody></table>';
	$returnary['depreciation_table'] =$html;
	echo json_encode($returnary);
}
if($page=="getpaymentterms"){
	$paymentamt = 0;
	$returnary = array();
	$transaction_date = $_POST['transaction_date'];
	$payment_term_id = $_POST['payment_term_id'];
	$grandtotal  =  $_POST['grandtotal'];
	$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','id'=>$payment_term_id);
  	$data1["field"] = 'id, content';
  	$data = urlencode(json_encode($data1));
  	$curl = API."?mode=gettabledetailswithconditions&table=payment_terms&data=".$data; 
  	$data = callAPI($curl);
  	$res = json_decode($data,1);
  	$result = $res[0];
  	$content = json_decode(str_replace("'", '', urldecode($result['content'])),1); 
  	$due_days = 0;
  	$html = "";
  	$i=1;
  	if($grandtotal !="0" AND $grandtotal !=""){
	  	$html .= '<td colspan="2" class="p-2"><table class=" table table-bordered mb-0">';
	  	foreach($content as $row){
	  		if($row['due_type'] == "P"){
	  			$duetypediscountamt = ($row['value'] / 100)*$grandtotal;	
	  		}elseif($row['due_type'] == "F"){
	  			$duetypediscountamt = $row['value'];	
	  		}elseif($row['due_type'] == "B"){
	  			$duetypediscountamt = $remainingamt;	
	  		}
	  		$paymentamt = $paymentamt+$duetypediscountamt;
	  		if($grandtotal < $paymentamt){
	  			if($duetypediscountamt >= $remainingamt){
	  				$duetypediscountamt = $remainingamt;
	  			}else{
	  				break;
	  			}
	  		}else{
	  			$remainingamt = $grandtotal-$paymentamt;
	  		}
			if($due_days ==""){
	  			$due_days = $row['days'];
	  		}
	  		$days = $row['days'];
	  		$extradescount = $before_date = $discountwithamt="";
	  		if($row['discount'] !="" AND $row['discount'] !="0" AND $row['discount_day'] !="" AND $row['discount_day'] !="0"){
	  			$finalday = $days-$row['discount_day'];
	  			$before_date = date('Y-m-d', strtotime($transaction_date.' + '.$finalday.' days'));
	  			$discountamt = ($row['discount'] / 100)*$duetypediscountamt;
	  			$discountwithamt = $duetypediscountamt-$discountamt;	
	  			$extradescount = "<spam class='d-block f-10 f-w-600'>Before Date ".$before_date." (".customeummarmonyformate($discountwithamt).")  </span>";
	  		}
	  		$due_dates = date('Y-m-d', strtotime($transaction_date.' + '.$days.' days'));
	  		$html .='<tr> <td class="p-2">'.fromDate($due_dates).'</td><td class="p-2 text-right">'.customeummarmonyformate($duetypediscountamt).' '.$extradescount;
	  		
	  		$html .='<input type="hidden" name="payment_term_history['.$i.'][due_date]" value="'.$due_dates.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][amount]" value="'.$duetypediscountamt.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][extra_date]" value="'.$before_date.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][extra_amt]" value="'.$discountwithamt.'">';
	  		$html .='</td></tr>';	

	  $i++;	}
	  	$html .='</table></td>';
	} 	
	$returnary['payment_terms'] = $html;
	$due_date = date('Y-m-d', strtotime($transaction_date.' + '.$due_days.' days'));
	$returnary['due_date'] = fromDate($due_date);
	echo json_encode($returnary);
}	
	**/


