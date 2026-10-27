<?php 
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
 $page = $_POST['page'];
 $response = 'Empty';
 if ($page == 'login') { 
	if ($_POST['username'] !=''){
		$url = API."?mode=ajaxCheck&table_name=".TPREFIX.TBL_USERS."&filedname=user_name&value=".$_POST['username'];
		$response = file_get_contents($url, false, $context);
	}
	echo $response; exit;
 }
 if ($page == 'login_email'&& $_POST['email']!='') { 
	$url = API."?mode=ajaxCheck&table_name=".TPREFIX.TBL_USERS."&filedname=email&value=".$_POST['email'];
	$response = file_get_contents($url, false, $context);
	echo $response; exit;
 }
 if ($page == 'login_phone'&& $_POST['phone']!='') { 
	$url = API."?mode=ajaxCheck&table_name=".TPREFIX.TBL_USERS."&filedname=email&value=".$_POST['phone'];
	$response = file_get_contents($url, false, $context);
	echo $response; exit;
 }
 if ($page == 'bank'&& $_POST['bankname']!='') { 
	$url = API."?mode=ajaxCheck&table_name=".TBL_BANKS."&filedname=Bank_Name&value=".urlencode($_POST['bankname']);
	$response = file_get_contents($url, false, $context);
	echo $response; exit;
 }
 if ($page == 'bill_customer'&& $_POST['cus_id']!='') { 
	$url = API."?mode=singlerowtabledata&table=".TBL_CUSTOMERS."&fid=id&rid=".$_POST['cus_id'];
	$response = callAPI($url);
	//$res = json_decode($response,1);
	//print_r($res);
	//$result = "<address><p>".$res['name']."</p><p>".$res['address']."</p><p>".$res['phone']."</p><p>".$res['email']."</p></address>";
	echo $response; exit;
 }
 if ($page == 'invoice'&& $_POST['item_id']!='') { 
	$url = API."?mode=singlerowtabledata&table=".TBL_ITEMS_BALANCE."&fid=item_id&rid=".$_POST['item_id'];
	//echo $url;
	$response = file_get_contents($url, false, $context);
	//echo $response;
	$res = json_decode($response,1);
	echo $res['qty']; exit;
 }
 if ($page == 'vendor'&& $_POST['ven_id']!='') { 
	$url = API."?mode=singlerowtabledata&table=".TBL_VENDOR."&fid=id&rid=".$_POST['ven_id'];
	//echo $url;
	$response = callAPI($url);
	//$res = json_decode($response,1);
	//print_r($res);
	//$result = "<address><p>".$res['name']."</p><p>".$res['address']."</p><p>".$res['phone']."</p><p>".$res['email']."</p></address>";
	echo $response; exit;
 }
 if ($page == 'dis_att_type'&& $_POST['table']!='') { 
	$url = API."?mode=filednames&table=".$_POST['table'];
	echo $url;
	$response = callAPI($url);
	//$response = file_get_contents($url, false, $context);
	echo $response;
	$response = json_decode($response,1);

	$str = '';
	foreach ($response as $key => $res) {
		$str.= "<option value = '$res'>$res</option>";
	}
	echo $str; exit;
 }
 if ($page == 'dis_submit') { 
	//print_r($_POST);
	$data = urlencode(json_encode($_POST));
	//echo "<br>".$data;
	$url = API."?mode=displayattributetye&method=post";
	//echo $url; //exit;
	//print_r($_POST);
	//$response = callAPI($url);
	$response = file_post_contents($url, $_POST);
	//$response = file_get_contents($url, false, $context);
	echo $response; exit;
}
if ($page == 'custom_left_join'&& $_POST['table']!='') { 
	$url = API."?mode=filednames&table=".$_POST['table'];
	echo $url;
	$response = callAPI($url);
	//$response = file_get_contents($url, false, $context);
	echo $response;
	$response = json_decode($response,1);

	$str = '';
	foreach ($response as $key => $res) {
		$str.= "<option value = '$res'>$res</option>";
	}
	echo $str; exit;
}
if ($page == 'custom_right_join'&& $_POST['table']!='') { 
	$url = API."?mode=morefilednames&table=".json_encode($_POST['table']);
	echo $url;
	$response = callAPI($url);
	//$response = file_get_contents($url, false, $context);
	echo $response;
	$response = json_decode($response,1);

	$str = "<option value ='0'>Select any field</option>";
	foreach ($response as $key => $value) {
		foreach ($value as  $res) {
			$str.= "<option value = '$key.$res'>$key . $res</option>";
		}
	}
	echo $str; exit;
}
if ($page == 'custom_query_join'&& $_POST['table']!='') { 
	$tables = json_decode($_POST['table'],1);
	$data = json_decode($_POST['data'],1);
	$i=0; $str='';
	if(count($tables)>0){
		$str = "SELECT * FROM ";
		foreach ($tables as $table) {
			if(count($data)>0 && count($tables)>1 && $i >0){
				$dat = $data[$table];
				//$str.= $table." ";
				//foreach($data as $key=>$dat){
				if($dat['left_field']!=''){
					$str.= $dat['join']." ".$table." ON ".$table.".".$dat['left_field']." = ".$dat['right_field']." " ;
				}
				//}
				
			}else{
				if($i>0){
					break;
				}
				$str.= $table." ";
				
			}
			$i++;
		}
	}
	echo $str; exit;
}
if ($page == 'save_report_filter') { 
	//print_r($_POST); //exit;
	$array = array();
	$array['report_id'] = $_POST['report_id'];
	$array['field_name'] = $_POST['field'];
	$array['filter_type'] = $_POST['type'];
	$array['company_id'] = $_SESSION['company_id'];
	$array['value_field'] = $_POST['field_name'];
	$array['display_field'] = $_POST['value'];

	$array['content'] = urlencode($_POST['dis_custom_sql']);
	$array['content_details'] = urlencode (json_encode($_POST)); 
	$url = API."?mode=reportfilter&method=post";
	$response = file_post_contents($url, $array);
	//$response = file_get_contents($url, false, $context);
	//print_r($array);
	echo $response; exit;
}
if($page == 'ItemDetails'&& $_POST['item_id']!=''){
	//print_r($_POST); exit;
	$url = API."?mode=toGetItemcostingtyewithdata&table=".$_POST['table_name']."&item_id=".$_POST['item_id']."&date=".$_POST['ddate']."&party_id=".$_POST['party_id'];
	//echo $url;exit;
	$response = callAPI($url); 
	$response = json_decode($response,1);
	//$data1["field"] = "ex_rate";
	//`` =1 AND `` =1 AND `` = '7650076576500050' AND `fiscal_year` = '20202021'
	//print_r($response);
	$data1["where"] = array('company_id'=> $_POST['company_id'],'cost_center_id'=> $_POST['cost_center_id'],'account_code'=>$response['purchase_account'],'fiscal_year' => FISCALYEAR);
	//print_r($data1);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=budget_balances&data=".$data; 
	//echo $curl;
	$cudata = callAPI($curl);
	//echo $cudata;  exit;
	//print_r($cudata);
	//$churl = API."?mode=budgetdataformonth&fid=id&rid=".$_POST['item_id'];
	$cudata = json_decode($cudata,1); 
	//print_r($cudata); exit;
	$fy = explode('/', $_POST['fiscal_year']);
	$month = fiscalmonth($fy[0]);
	$val = 'distribution_m'.$month;
	$val2 = 'rq_m'.$month;
	$response ['a'] = customeummarmonyformate($cudata[0][$val] - $cudata[0][$val2]);
	$response ['b'] = customeummarmonyformate($cudata[0][$val]);
	$response ['c'] = customeummarmonyformate($cudata[0][$val2]);
	$response ['am'] = $cudata[0][$val] - $cudata[0][$val2];
	$response ['bm'] = $cudata[0][$val];
	$response ['cm'] = $cudata[0][$val2];
	$response  = json_encode($response,1);
	echo $response; exit;
}
if($page == 'chartofaccount' && $_POST['item_id']!=''){
	$url = API."?mode=singlerowtabledata&table=".TBL_CHART_OF_ACCOUNTS."&fid=id&rid=".$_POST['item_id'];
	$response = callAPI($url); 
	echo $response; exit;
}
if($page =='userDetails'&& $_POST['user_id']!=''){
	//echo "hah11a"; exit;
	$url = API."?mode=singlerowtabledata&table=".TBL_USERS."&fid=id&rid=".$_POST['user_id'];
	$response = callAPI($url); 
	//echo $response;
	$res = json_decode($response,1);
	$locurl = API."?mode=singlerowtabledata&table=".TBL_LOCATIONS."&fid=id&rid=".$res['locaion_id'];
	$locares = callAPI($locurl); 
	//echo $locares;
	$locares = json_decode($locares,1);
	$data['display_name'] = $res['first_name'].' '.$res['middle_name'].' '.$res['last_name'];
	$data['email'] = $res['user_email'];
	$data['phone'] = $res['user_mobile'];
	$data['cost_center'] = $res['cost_center'];
	$data['gstin'] = $locares['tax_ref'];
	$data['billing_address'] = $locares['location_address'];
	$data['billing_city'] = $locares['location_city'];
	$data['billing_state'] = $locares['location_state'];
	$data['billing_post_code'] = $locares['location_post_code'];
	echo json_encode($data); exit;
}
if($page =='changestatus'){
	//echo "haha1"; exit;
	$farray  = array('current_status' => $_POST['field']);
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_POST['user_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'company_id'=> $_SESSION['company_id']);
	//print_r($sarray);
	/*if($_POST['hid'] != 3){
		$furl = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=".$_POST['table']."&method=post";
		$response1 = file_post_contents($furl, $farray);	
	}*/
	$url = API."?mode=updateapprovallog&table=".TBL_TRANSACTION_APPROVAL_LOGS."&method=post";
	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page == 'currency_convert'){
	//echo "haha"; exit;
	$from_currency= $_POST['old_val'];
	$to_currency = $_POST['val'];
	$data1["field"] = "ex_rate";
	$data1["where"] = array('from_currency'=> $from_currency,'to_currency'=> $to_currency);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=".TBL_CURRENCY_CONVERTOR."&data=".$data; 
	//echo $curl;
	$cudata = callAPI($curl);
	$res = json_decode($cudata, 1);
	echo $res['0']['ex_rate']; exit;
} 
if($page == 'pettycashdetails' && $_POST['item_id']!=''){
	$url = API."?mode=singlerowtabledata&table=petty_cash_expenses&fid=id&rid=".$_POST['item_id'];
	$response = callAPI($url); 
	$response  = json_decode($response,1);
	$curl = API."?mode=getpettycashbuget&table=budget_balances&branch_id=".$_POST['item_id']; 
	//echo $curl;
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	//print_r($cudata);
	$pclimit = $cudata['pc_limit'] !="" ? $cudata['pc_limit'] : 0;
	$consumed = $cudata['consumed'] !="" ? $cudata['consumed'] : 0;
	//echo $pclimit."##".$consumed;
	$response ['a'] = customeummarmonyformate($pclimit - $consumed);
	$response ['b'] = customeummarmonyformate($pclimit);
	$response ['c'] = customeummarmonyformate($consumed);
	$response ['am'] = $pclimit - $consumed;
	$response ['bm'] = $pclimit;
	$response ['cm'] = $consumed;
	$response  = json_encode($response,1);
	echo $response; exit;
}
if($page == 'blukapproval'){
	
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];

	//print_r($_POST); exit;
	$url = API."?mode=upadateblukapproval&method=post";
	$response = file_post_contents($url, $_POST);
	//$response = file_get_contents($url, false, $context);
	echo $response; exit;

	/*$url = API."?mode=singlerowtabledata&table=petty_cash_expenses&fid=id&rid=".$_POST['item_id'];
	$response = callAPI($url); */
	//echo $response; exit;
}
if($page == 'insertvenderquatation'){
	
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertvenderquatation&method=post";
	//print_r($_POST);
	echo $response = file_post_contents($url, $_POST);
}
if($page == 'pettycashbranchamount'){
	//print_r($_POST); exit;
	$curl = API."?mode=pettycashbranchamount&data=".json_encode($_POST); 
	//echo $curl;
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	echo $cudata; exit;
}
if($page == 'remove_file'){
	//print_r($_POST); exit;
	$curl = API."?mode=updaterowtabledata&table=".TBL_APPLICATION_ATTACHMENTS."&rid=".$_POST['file_id']."&data=".urlencode(json_encode(array('is_active'=>1))); 
	//echo $curl;
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	echo $cudata; exit;
}
if($page == 'statusprogrssacton'){
	$data1["where"] = array('company_id'=> $_POST['company_id'],'table_name'=> $_POST['table_name']);
	//print_r($data1);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$data; 
	//echo $curl."</br>";
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	//echo $cudata;
	$str = '';
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['name']."</option>";
	}
	echo $str;
}
if($page == 'dynamicchangestatus'){
	$farray  = array('current_status' => $_POST['field']);
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'], 'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	//print_r($sarray); exit;
	$url = API."?mode=updatedynamicapprovallog&method=post";
	//print_r($sarray);
	//echo $url; exit;
	$response = file_post_contents($url, $sarray);
	if($_POST['table']=='project_timesheets' && $_POST['extra']==''){
		echo "Data";
		$url = API."?mode=updateprojecttimesheets&method=post";
		$response = file_post_contents($url, $sarray);
	}
	if($_POST['table']=="candidates" AND $_POST['status']==ST_REJECTED){
		$historyurl = API."?mode=candidatesofferhistories&candidate_id=".$_POST['hid']."&company_id=".$_SESSION['company_id']."&status_date=".$_POST['date']."&current_status=".$_POST['status']."&is_active=0&comments=".$_POST['comment'];
		$histroy = callAPI($historyurl);
	}
	//print_r($_POST);
	echo $response; exit;
}
if($page == 'current_status_drop'){

	//print_r($_POST);
	$lists = toGetStatusProgressAction($_POST['table'], $_POST['current_status']);
	//print_r($lists); echo "ravi";
	$appruser_list_data = togetaprovaluserlist($_POST['table'], $_POST['id']);
	$appruser_list = $appruser_list_data['user'];
	//print_r($appruser_list_data); echo "ravi";
	$tablewr["where"] = array('id'=> $_POST['id']);
	$tablecon = urlencode(json_encode($tablewr));
	$tableurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table']."&data=".$tablecon; 
	//echo $tableurl;
	$tabledata = callAPI($tableurl);
	$tabledata = json_decode($tabledata,1);
	//print_r($tabledata); echo "ravi";
	$str ="";
	// print_r($lists); exit;

	foreach ((array)$lists as $list) { 
	  
		$str .= '<a href="#!" class="dropdown-item waves-effect waves-light" onclick="statuspopup('. $list['status'] .','. $_POST['id'] .')" name="status" id="status" value="'. $list['status'] .'">'.$list['action_label'].'</a>';
	}
	$str_d=$str;
	if(count((array)$appruser_list)>0){ //echo "1"; //print_r($_SESSION);
	if(!in_array($_SESSION['userid'], $appruser_list)){
		$str = '<a href="#!" class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9002'].'</a>';
	}

	}else{ //echo "2;";
	if($_SESSION['userid'] != $tabledata[0]['created_by']){
		$str = '<a href="#!"  class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9002'].'</a>';
	}
}
if($_SESSION['userid'] == $tabledata[0]['created_by'] && $_POST['current_status']==ST_SEND_BACK){
	$str = $str_d;
}
	if($str == ""){ //echo "3";
	$str = '<a href="#!"  class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9003'].'</a>';
}

echo $str; exit;
}
if($page == 'current_status_drop_table'){
	//print_r($_POST);

	$lists = toGetStatusProgressAction($_POST['table'], $_POST['current_status']);
	$appruser_list_data = togetaprovaluserlist($_POST['table'], $_POST['id']);
	$appruser_list = $appruser_list_data['user'];
	//print_r($appruser_list_data); echo "ravi";
	$tablewr["where"] = array('id'=> $_POST['id']);
	$tablecon = urlencode(json_encode($tablewr));
	$tableurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table']."&data=".$tablecon; 
	//echo $tableurl;
	$tabledata = callAPI($tableurl);
	$tabledata = json_decode($tabledata,1);
	//print_r($tabledata); echo "ravi";
	$str ="";
	// print_r($lists); exit;
	$table_name = "'".$_POST['table']."'";
	foreach((array)$lists as $list) { 
	  //$str .= ' <div  class="btn btn-primary"  name="status" id="status" value="'. $list[status] .'">'.$list[action_label].'</div>';
		$str .= '<a href="#!"  class="dropdown-item waves-effect waves-light" onclick="statuspopup('. $list['status'] .','. $_POST['id'] .','.$table_name.')" name="status" id="status" value="'. $list['status'] .'">'.$list['action_label'].'</a>';
	}
	if(count((array)$appruser_list)>0){
		if(!in_array($_SESSION['userid'], (array)$appruser_list)){
			$str = '<a href="#!"  class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9002'].'</a>';
		}
	}else{
		if($_SESSION['userid'] != $tabledata[0]['requested_by']){ 
			$str = '<a href="#!"  class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9002'].'</a>';
		}
	}
	if($str == ""){
		$str = '<a href="#!"  class="dropdown-item waves-effect waves-light text-danger">'.$_SESSION['error_message']['9003'].'</a>';
	}
	echo $str; exit; 
}
if($page == 'changekpiroleid'){
	//print_r($_POST);
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'role_id'=> $_POST['id']);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=key_performance_indicators&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value = '0'>Select Any One </option>";$vers=array(); $new_ver=1;
	foreach ($cudata as  $data) {
		if(!in_array($data['version_no'], $vers)){
			$vers[]=$data['version_no'];
			$str.= "<option value = '".$data['version_no']."'>Version ".$data['version_no']."</option>";
			$new_ver = $data['version_no']+1;
		}
	}
	$str.= "<option value = '".$new_ver."'>New Version</option>";
	echo $str;
}
if($page == 'task_submit'){
	echo "<pre>"; print_r($_POST);
	$url = API."?mode=inserttasks&method=post";
	$response = file_post_contents($url, $_POST);
	echo $response; exit;
}
if($page == 'goalscheckandadd'){
	$url = API."?mode=goalscheckandadd&assemnt_year=".$_POST['assemnt_year']."&role_id=".$_POST['role_id']."&version=".$_POST['version'];
	//echo $url;
	$data = callAPI($url); 
	echo $data;
}
if($page == 'employeegoalscheckandadd'){
	$url = API."?mode=employeegoalscheckandadd&assemnt_year=".$_POST['assemnt_year']."&role_id=".$_POST['role_id'];
	//echo $url;
	echo $data = callAPI($url);
}
if($page == 'dynmictabledata'){
	$str = "<option value = ''>Select Any One</option>";
	$tablewr["where"] = array('is_active'=> 0);
	if(isset($_POST['rid'])){
		$tablewr["where"] [$_POST['rid']] = $_POST['fid'];
	}
	$tablecon = urlencode(json_encode($tablewr));
	$tableurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table']."&data=".$tablecon; 
	//echo $tableurl;
	$tabledata = callAPI($tableurl);
	$tabledata = json_decode($tabledata,1);
	foreach ($tabledata as  $data) {
		$str.= "<option value = '".$data[$_POST['field']]."'> ".$data[$_POST['value']]."</option>";
	}
	echo $str;
}
if($page == 'dynamictabledata'){
	$tablewr["where"] = array('is_active'=> 0,'id'=>$_POST['fid']);
	//print_r($tablewr);
	$tablecon = urlencode(json_encode($tablewr));
	$tableurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table']."&data=".$tablecon; 
	//echo $tableurl;
	$tabledata = callAPI($tableurl);
	$tabledata = json_decode($tabledata,1)[0];
	echo $tabledata['amount'];
}
if($page == 'candidateautoregistation'){

	$tableurl = API."?mode=candidateautoregistation&method=post"; 
	//echo $tableurl;print_r($_POST);
	$response = file_post_contents($tableurl, $_POST);
	echo $response;
	//echo "ajax";
}	
if($page == 'updateonlystatus'){
	$tableurl = API."?mode=updateonlystatus&table=".$_POST['table']."&status=".$_POST['status']."&hid=".$_POST['hid']; 
	echo $tableurl;
	$return = callAPI($tableurl);
	echo $return;
}
if($page == 'usernotify'){
   // print_r($_POST); exit;
	$tableurl = API."?mode=usernotify&method=post"; 
	//$tableurl = API."?mode=usernotify&send_id=".$_POST['user_id']."&comments=".$_POST['comment']; 
	//echo $tableurl; exit;
	$return = file_post_contents($tableurl, $_POST);
	echo $return;
}
if($page == 'employeeclocks'){
	//print_r($_POST);
	date_default_timezone_set('Asia/Kolkata');
	$_POST['emp_id']=$_SESSION['emp_id'];;
	$_POST['date'] = date('Y-m-d');
	$_POST['time'] = date('H:i:s');
	$tableurl = API."?mode=employeeclocks&method=post";
	//echo $tableurl;
	print_r($_POST); 
	//$tableurl = API."?mode=usernotify&send_id=".$_POST['user_id']."&comments=".$_POST['comment']; 
	//echo $tableurl; exit;
	$return = file_post_contents($tableurl, $_POST);//exit;
	echo $return;
}
if($page == 'dynmicupdatetable'){
	$table = $_POST['table'];
	$id =$_POST['id'];
	unset($_POST['table']);
	unset($_POST['id']);
	unset($_POST['page']);
	print_r($_POST);
	$tableurl = API."?mode=updaterowtabledata&method=post&rid=".$id."&table=".$table;
	//echo  $tableurl;exit;
	$return = file_post_contents($tableurl, $_POST);
	echo $return; exit;
}

if($page == 'tocheckpayrolldetailsfull'){
	$_POST['company_id'] = $_SESSION['company_id'];
	print_r($_POST); //exit;
	echo $tableurl = API."?mode=tocheckpayrolldetailsfull&payroll_steps_cat=".$_POST['payroll_steps_cat']."&pay_year=".$_POST['pay_year']."&pay_month=".$_POST['pay_month']."&pay_area_id=".$_POST['pay_area_id']; 
	$return  = callAPI($tableurl);
	echo $return;
}
if($page == 'holdpayroll_exclusions'){
	print_r($_POST); //exit;
	$tableurl = API."?mode=holdpayroll_exclusions&method=post"; 
	$return = file_post_contents($tableurl, $_POST);
	echo $return;
}
if($page == 'insertpayrollexcludd'){
	//print_r($_POST); //exit;
	$tableurl = API."?mode=insertpayrollexcludd&method=post"; 
	$return = file_post_contents($tableurl, $_POST);
	//echo $return;
	$emda = json_decode($return,1);
	$str ="";
	$str.='<tr id="excluded_'.$emda['id'].'">
            <td class="pr-0">
              <div class="d-flex align-items-center">  
                <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">           
                  <span class="symbol-label font-size-h4 font-weight-bold">'.initials($emda['display_name']).'</span>       
                </div>              
              </div>
            </td>            <td class="pl-0">
              <span class="text-muted font-weight-bold text-muted d-block">'.$emda['emp_code'].'</span>
            </td>
            <td>
              <span class="text-muted font-weight-bold">'.$emda['display_name'].'</span>
            </td>
            <td>
              <button type="button" onclick="notifyuser('.$emda['user_id'].')" class="btn btn-success">Notify</button>
            </td>
            <td class="pr-0 text-right">
              <a href="#" class="btn btn-icon btn-light btn-hover-primary btn-sm" onclick="deleteonerow('.$emda['id'].',3)">
                <i class="ti-trash f-20 text-danger"></i>
            </a>
          </td>
        </tr>';
	echo $str;
}

if($page == 'toinsertquery'){
	unset($_POST['page']);
	unset($_POST['comment']);
	$return = Insertqueries($_POST);
	return $return;
}

if($page=='sendtofiltertable'){
	$tableurl = API."?mode=savetofiltertable&method=post";
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	 // print_r($_POST);
	$return = file_post_contents($tableurl, $_POST);
	// echo "step 2 :".$return;
	$returnval = json_decode($return,1);
	 //print_r($returnval);
	echo $returnval['LASTID'];exit;
}
if($page=='selectedfiles') {
	$return = json_decode($_POST,1);
	$fileslist = $_POST['files'];
	$files = json_decode($fileslist,1);
	$return = "";$i = 0;
	foreach ($files as $key => $value) {
		$i++;
		$return = $return."<div class='ml-10' id='".$i."'>".$value."</div>";
	}
	echo $return;
}
if($page=='selectednames'){
	$nameslist = $_POST['names'];
	if($nameslist==ALLEMPLOYEES) {
		$return = "<div class='ml-10'>".$nameslist."</div>";
	} else {
		$names = json_decode($nameslist,1);
		$list_names = $names['matches_names'];
		$return = "";$i = 0;
		foreach ($list_names as $key => $value) {
			$i++;
			$return = $return."<div class='ml-10' id='".$i."'>".$value['display_name']."</div>";
		}
	}
	echo $return;
}
if($page == 'tasksendmail'){
	$url = API."?mode=tasksendmail&task_id=".$_POST['task_id'];
	$data = callAPI($url);
	//exit;
	echo $data; exit;	
}
if($page == 'selectedfilter'){
	$tableurl = API."?mode=gettotalemployeesbyfilter&method=post";
	$return = file_post_contents($tableurl, $_POST);

	echo $return;exit;
	
}
if($page=='selectedsetup'){
	$tableurl = API."?mode=sendtousersettings&method=post"; 
	$_POST['load_date'] = nowDate();
	$_POST['user_id'] = $_SESSION['user_id'];
	$response = file_post_contents($tableurl, $_POST);
	echo $response; 
}
if($page=='selectedattendancelist'){
	$nameslist = $_POST['names'];
	$attendance_month = $_POST['month'];
	$attendance_year = $_POST['year'];
	$shiftkeys = json_decode($_POST['shiftkeys'],1);
	if($nameslist!=ALLEMPLOYEES) {
		$names = json_decode($nameslist,1);
		$nameslist = $names['matches_names'];
		foreach ($nameslist as $value) {
			$emplist[$value['id_value']] = $value['display_name'];
		}
	}else{
		$emplist = '';
	}
	$postdata['emplist'] = $emplist;
	$postdata['month'] = $attendance_month;
	$postdata['year'] = $attendance_year;
	$tableurl = API."?mode=getemployeeattendance&method=post";
	$empattendance = file_post_contents($tableurl, $postdata);
	$mon = date("F", mktime(0, 0, 0, $attendance_month, 10));
	$getmaxdays=mktime(0, 0, 0, $attendance_month, '01', $attendance_year);
	$maxdays = cal_days_in_month(CAL_GREGORIAN, $attendance_month, $attendance_year);
	$empattendance_data = json_decode($empattendance,1);
	$employee = $empattendance_data['emp_names'];
	$empattendance=$empattendance_data['emp_attendance'];
	$return ='<table class="work-entry text-center" width="100%"><thead><tr><th rowspan="0">Work Entries</th>	<th colspan="31" align="center">'.$mon. " ".$attendance_year.'</th>	</tr><tr>';
	for($i=1;$i<=$maxdays;$i++) { 
		$return .= '<th>'.$i.'</th>';
	}
	$return .= '</tr>	</thead>';
	foreach ($empattendance as $key => $empvalue) {
		$return .= '<tr><th rowspan="2">'.$employee[$key].'</th>';
		foreach ($empvalue as $key => $sessiontime) {
			$showflag = $sessiontime['first_reg']==1? 'flag-icon':'';
			$return .= '<td class="bg-'.strtolower($sessiontime['S1displaybgcolor']).' text-'.strtolower($sessiontime['S1displaytextcolor']).' '.$showflag.'">'.$sessiontime['S1displayvalue'].'</td>';
		}
		$return .= '</tr>';
		foreach ($empvalue as $key => $sessiontime) {
			$showflag = $sessiontime['second_reg']==1? 'flag-icon':'';
			$return .= '<td class="bg-'.strtolower($sessiontime['S2displaybgcolor']).' text-'.strtolower($sessiontime['S2displaytextcolor']).' '.$showflag.'">'.$sessiontime['S2displayvalue'].'</td>';
		}
		$return .= '</tr>';
	}
	echo $return;
}
if($page == 'setupcomponents'){
	//print_r($_POST); 
	if(isset($_POST['start_date'])){ $_POST['start_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['start_date']))); }
	if(isset($_POST['end_date'])){ $_POST['end_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['end_date']))); }
	 if(isset($_POST['courier_date'])){ $_POST['courier_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['courier_date']))); }
	 if(isset($_POST['publish_date'])){ $_POST['publish_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['publish_date']))); }


	$table_name = $_POST['table_name'];
	$id = $_POST['id']; 
	unset($_POST['table_name']);
	unset($_POST['page']);
	unset($_POST['id']);
	if($id==''){
		$_POST['created_date'] = nowDate();
		$_POST['is_active'] = 0;
		$_POST['created_by'] = $_SESSION['userid'];
		$_POST['company_id'] = $_SESSION['company_id'];
		//print_r($_POST);
		$url = API."?mode=adddata&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST);
	} else {
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=updaterowtabledata&rid=".$id."&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST, 'update');
	}
	if($table_name=='project_teams' && $_POST['role_id']=="1" && $id==""){
		$url = API."?mode=updaterowtabledata&rid=".$_POST['project_id']."&table=projects&method=post";
		$updata = array('assigner_id'=>$_POST['user_id']);
		$return = file_post_contents($url, $updata, 'update');
	}
	echo $return;
}



if($page=='getcomponents'){
	$cudata='';
	if($_POST['id']!=''){
		$data1["where"] = array('id'=> $_POST['id']);
		$data = urlencode(json_encode($data1));
		$post="&data=".$data;
		echo $curl = API."?mode=gettabledetailswithconditions&table=".$_POST['table_name'].$post; 
		$cudata = callAPI($curl);
	}
	echo $cudata;
}
if($page=='getselectedlist'){
	$cudata='';
	if($_POST['id']!=''){
		$data1["where"] = array($_POST['field']=> $_POST['id'], 'is_active' => 0);
		if($_POST['table_name'] == "states"){ $data1["order"] = "state_name"; }
		if($_POST['table_name'] == "cities"){ $data1["order"] = "city_name"; }
		$data = urlencode(json_encode($data1));
		$post="&data=".$data;
		$curl = API."?mode=gettabledetailswithconditions&table=".$_POST['table_name'].$post; 
		$cudata = callAPI($curl);
	}
	echo $cudata;
}
if($page=='getCountrylist'){
	$cudata='';
	$url = API."?mode=gettabledetailswithconditions&table=countries";
	$cudata = callAPI($url); 
	echo $cudata;
	
}

if($page=='getannouncements'){
	$cudata='';
	$data1["where"] = array('userid'=>$_POST['userid'],'announcement_id'=>$_POST['announcement_id'],'msg_type'=>$_POST['msg_type'],'company_id'=> $_SESSION['company_id'],'is_active'=>'0');
	$data = urlencode(json_encode($data1,1));
	$post="&data=".$data;
	$url = API."?mode=gettabledetailswithconditions&table=".$_POST['table_name'].$post;
	$cudata = callAPI($url); 
	$cudata = json_decode($cudata,1);
	$resdata['rowid'] = $cudata[0]['id'];
	//$resdata['liketype'] = $cudata[0]['liketype'];
	$resdata['like_type'] = $cudata[0]['like_type'];
	echo json_encode($resdata); exit; 
}

if($page == 'summaryannouncements'){
	$table_name = $_POST['table_name'];
	$id = $_POST['id']; 
	unset($_POST['table_name']);
	unset($_POST['page']);
	unset($_POST['id']);
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['current_status'] =0;
	$_POST['status_date'] = nowDate();
	if($id=='null'){
		$_POST['created_date'] = nowDate();
		$_POST['is_active'] = 0;
		$_POST['created_by'] = $_SESSION['userid'];
		$url = API."?mode=adddata&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST);
	} else {
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=updaterowtabledata&rid=".$id."&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST, 'update');
	}
	echo $return;
}

if($page=='getleavingdate'){
	$effective_date = $_POST['effective_date'];
	$effectdate = date('Y-m-d', strtotime($effective_date));
	$return = date('Y-m-d', strtotime($effective_date.' + '.$_POST['numberofdays'].' days'));
	echo fromDate($return);
}
if($page == 'updatestatuscandidatesign'){
	$_POST['candidate_id'] = $_POST['hid'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=candidateofferAccept&method=post";
	$offeracceres = file_post_contents($url, $_POST);
	$return = json_decode($offeracceres,1);
	//print_r($return);
/**
	$farray  = array('current_status' => $_POST['field']);
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	
	$historyurl = API."?mode=candidatesofferhistories&candidate_id=".$_POST['hid']."&company_id=".$_SESSION['company_id']."&status_date=".$_POST['date']."&current_status=".$_POST['status']."&is_active=0&comments=".$_POST['comment'];
	$histroy = callAPI($historyurl);
	
	$url = API."?mode=updatedynamicapprovallog&method=post";
	$response = file_post_contents($url, $sarray);
	
	$tableurl = API."?mode=candidateofferAccept&candidate_id=".$_POST['hid']."&table=candidates";

	$return = callAPI($tableurl);
	//print_r($return);
	$sign = array();
	$sign['signature']= $_POST['sign'];
	$sign['current_status']= ST_LOIACCEPTED;
	
	$tableurl = API."?mode=updaterowtabledata&method=post&rid=".$_POST['hid']."&table=candidates";
	$return = file_post_contents($tableurl, $sign);    **/
	//echo $response; exit;
}
if($page == 'updatejobpostcandidate'){
	if($_POST['type']=="process"){ 
		$sign['job_selection_process_step_id']= $_POST['type_id']; 
	}else{
		$sign['ratting']= $_POST['rate']; 
	}
	$tableurl = API."?mode=updaterowtabledata&method=post&rid=".$_POST['job_can_id']."&table=job_posting_candidates";
	$return = file_post_contents($tableurl, $sign);
	$sarray = array();
	$sarray  = array('comments' => $_POST['comment'], 'type' => $_POST['type'], 'company_id' => $_SESSION['company_id'], 'job_posting_candedate_id' => $_POST['job_can_id'],'value' => $_POST['type_id'],'is_active' => 0,'current_status'=> 0,'created_by'=>$_SESSION['userid']);
	//print_r($sarray);
	$url = API."?mode=adddata&table=candidate_history&method=post";
	$return = file_post_contents($url, $sarray);
}
if($page == 'datediffval'){
	$date2 = $_POST['date2'];
	$date2 = str_replace('/', '-', $date2);
	$date1 = $_POST['date1'];
	$date1 = str_replace('/', '-', $date1);
	echo (strtotime($date2) - strtotime($date1)) / (60 * 60 * 24)+1; 
}
/**if($page == 'datediffval'){
	echo (strtotime($_POST['date2']) - strtotime($_POST['date1'])) / (60 * 60 * 24);
} **/
if($page=='confirmrecognition'){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['is_active'] = '0';
	$rowid = $_POST['rowid'];
	unset($_POST['rowid']);
	unset($_POST['page']);
	if($rowid!=""){
		$row = "&RID=".$rowid;
		$_POST['updated_date'] = nowDate();
		$_POST['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=insertrecognition&rid=".$rowid."&method=post";
		$response = file_post_contents($url, $_POST, 'update');
	}else{ 
		$_POST['current_status'] ='0';
		$_POST['created_date'] = nowDate();
		$_POST['created_by'] = $_SESSION['userid'];
		//print_r($_POST);
		$url = API."?mode=insertrecognition&method=post";
		$response = file_post_contents($url, $_POST, 'insert');
	}
	echo $response; 
}
if($page=='confirmbulktransfer'){
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['created_date'] = date('Y-m-d');
	$transfer_data = json_decode($_POST['transfer_data'],1);
	$empids = $transfer_data['transfer_empids'];
	$oldvalues = $transfer_data['oldvalue'];
	$rowids = $transfer_data['previous_record_rowid'];
	//print_r($rowids);
	foreach ($empids as $key => $value) {
		$transfer_details['emp_id'] = $value;
		$transfer_details['old_value'] = $oldvalues[$value];
		$transfer_details['previous_record_rowid'] =  $rowids[$value];
		$transfers[] = $transfer_details;
	}
	unset($_POST['transfer_data']);
	unset($_POST['page']);
	$_POST['transfer_list'] = $transfers;

	$url = API."?mode=updatebulktransferhistory&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$url = API."?mode=updatebulktransferemployees&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page=='getapplicationtablevalues'){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
$data = urlencode(json_encode($data1));
$curl = API."?mode=gettabledetailswithconditions&table=".$_POST['application_tablename']."&data=".$data;
//exit;
$cudata = callAPI($curl);
$cudata = json_decode($cudata,1);
$str = "<label class='mb-2'>Transfer To</label> <select class='form-control form-select js-example-basic-single mt-2' onchange='selected_bulk_category(this.value)'> ";
foreach ($cudata as $key => $res) {
$optionval = $res[$_POST['application_table_fieldname']];
$optionid = $res['id'];
$str.= "<option value ='$optionid'>$optionval</option>";
}
$str .= "</select> <span class='form-text text-muted'>Select Transfer To</span>";
echo $str;
}
if($page == 'leavedatecheck'){
	$leave_cat_id = $_POST['leave_cat'];
	//echo $leave_cat_id."***";
	$vdata1["where"] = array('is_active'=> 0);
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=leave_categories&data=".$vdata; 
	$configesponse = callAPI($vurl);
	$cate = json_decode($configesponse,1);
	foreach($cate as $cat){
		$ca[$cat['id']] = $cat;
	}
	//print_r($ca);
	$days=(strtotime($_POST['date2']) - strtotime($_POST['date1'])) / (60 * 60 * 24);
	//echo $days;
	$vdataee["where"] = array('is_active'=> 0,'employee_id'=>$_SESSION['emp_id']);
	$vdata = urlencode(json_encode($vdataee));
	$vurl = API."?mode=gettabledetailswithconditions&table=emp_leave_balances&data=".$vdata; 
	$empbalances = callAPI($vurl);
	$empbalances = json_decode($empbalances,1);
	//print_r($vdata);
	foreach ($empbalances as $empbalance) {
		$balance[$empbalance['leave_type']] = $empbalance;
	}
	print_r($balance);
	echo $ca[$leave_cat_id]['allowed_leave_days']."&&&".$balance[$leave_cat_id]['consumed_leaves']."********";
	$check_leave = $ca[$leave_cat_id]['allowed_leave_days']-$balance[$leave_cat_id]['consumed_leaves'];
	echo $check_leave;
	if($check_leave < $days){
		echo $_SESSION['error_message']['4009'];
	}else{
		echo "";
	} exit;
}
if($page=='removeqidfromsession') {
	$unsetqid = $_POST['qid'];
	unset($_SESSION['questions_list'][$unsetqid]);
	
}
if($page=='notestarttime'){
	$_SESSION['response_start_time'] = date('H:i:s');
}

if($page=='formsurveyquestions'){
	$uniq_qid = uniqid();
	$questions_list = array('question_id'=>'','rid'=>$_POST['rid'],'title'=>$_POST['title'],'type'=>$_POST['type'],'question'=>$_POST['question'],'require_response'=>$_POST['require_response'],'allow_multiple_selections'=>$_POST['allow_multiple_selections'],'option_data'=>explode(',',$_POST['option_data']));
	$questiondetails = json_encode($questions_list,1);
	$_SESSION['questions_list'][$uniq_qid] = $questiondetails;

	$display_question = '';
	$totalquestion = count($_SESSION['questions_list']);
	$value_id = 1;
	foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
		$value = json_decode($qvalue,1);
		$allow_multiple_selections_status = $value['allow_multiple_selections']=='0'?'checked' :'';
		$require_response_status = $value['require_response']=='0'?'checked' :'';
		$options = $value['option_data'];
		$totaloption = count($options);
		$title = $value['title'];
		$deleteqid = $value['uniq_qid'];
		$question_id = $value['question_id'];
		$rid = $value['rid'];

		$display_question .= ' <div class="mt-5" id="'.$qkey.'"> <div class="col-lg-12 table-bordered">
		<input type="hidden" name="totalquestion" value="'.$totalquestion.'">
		<input type="hidden" name="question_id'.$value_id.'" value="'.$question_id.'">
		<input type="hidden" name="rid" value="'.$rid.'">
		<input type="hidden" name="value_id[]" value="'.$value_id.'">

		<input type="hidden" name="options_display_type'.$value_id.'" value="'.$value['type'].'">

		<div class="modal-header" >
		<h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
		<i aria-hidden="true" class="ti-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$qkey."'".')"></i>
		</div>
		<div class="card-body mt-3">
		<div class="form-group text-left">
		<label>Question Text: <span class="text-danger"></span></label>
		<textarea id="survey_question'.$value_id.'" name="survey_question'.$value_id.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
		</div>
		<div class="form-group text-left">
		<div class="checkbox-inline">';
		if($value['type']==1 || $value['type']==3)	{ 
			$display_question .='
			<label class="checkbox checkbox-primary">
			<input type="checkbox" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" '.$allow_multiple_selections_status.'/>
			<span></span>
			Allow multiple selections
			</label>';
		}
		else{
			$display_question .='<input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="1"/>';
		}
		$display_question .='
		<label class="checkbox checkbox-primary">
		<input type="checkbox" name="require_response'.$value_id.'" id="require_response'.$value_id.'" '.$require_response_status.'>
		<span></span>
		Require a Response
		</label>
		</div>
		</div>';
		if($value['type']==1 || $value['type']==3)	{ 
			$display_question .= '<div id="options'.$value_id.'">';
			foreach ($options as $opkey => $opvalue) { 
				$display_question .= '<div id="optionlist'.$value_id.$opkey.'">
				<div class="form-group row" id="'.$opkey.'">
				<div class="col-lg-1"></div>
				<div class="col-lg-8">
				<div class="input-group input-icon-right">
				
		<input type="text" class="form-control display-inline width1" name="survey_question_option'.$value_id.'[]"  placeholder="Option" value="'.$opvalue.'">
		<span class="input-group-text addon-style">
			<label class=""><i class="ti-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$value_id."'".')"></i> </label>
		</span>

				
				
				</div>
				</div>
				</div>
				';
			}
			$display_question .= '</div>';
			$op = count($options)+1;
			$display_question .= '<div class="form-group">
			<input type="hidden" value="'.$opkey.'" id="option_row"/>
			<button class="btn btn-success"  onclick="addnewoptionrows('."'".$value_id."'".')" id="addNewRows" type="button">+ Add More Options</button>
			</div>';
		}	
		if($value['type']==2)	{ 
			$display_question .= '	<div class="form-group text-left mt-10">
			<label>Scale from 1 to: <span class="text-danger">*</span></label>
			<input type="number" name="survey_question_option'.$value_id.'[]"  class="form-control" placeholder="'.$options[0].'" value="'.$options[0].'" > 
			</div>';
		} 	
		if($value['type']==4)	{ 
			$display_question .= '	<input type="hidden" name="survey_question_option'.$value_id.'[]"  class="form-control" value=""> ';
		} 

		$display_question .= '</div> </div>  </div>';
		$value_id ++;

	}

	$response = $questiondetails;
	echo $display_question;
}
if($page=='filternamelist'){
	if($_POST['filterdata']=='All Employees'){
		$namelist = "";
	}else{
		$filterdata = json_decode($_POST['filterdata'],1);
		$filternamelist = $filterdata['matches_names'];
		foreach($filternamelist as $ids){
			$shared_idslist[]= $ids['display_name'];
		}
		$namelist = implode(',', $shared_idslist);
	}
	echo $namelist;
}
if($page=='insertinquiries'){
	print_r($_POST);
	for ($i=1; $i < $_POST['count'] ; $i++) { 
		$id = 'id_'.$i;
		$senddata['id'] []= $_POST['id_'.$i];
		$senddata['qty'][] = $_POST['qty_'.$i];
	}
	$url = API."?mode=insertinquiries&method=post";
	$response = file_post_contents($url, $senddata);
	//$data = json_decode($_POST['data']);
	print_r($senddata);
} 
if($page=='addtimesheetentries'){
	$table_name = TBL_PROJECT_TIMESHEETS;
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	unset($_POST['page']);
	$url= API."?mode=adddata&table=".$table_name."&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page=='resenduserdetails'){
	$url = API."?mode=NewUserEmailresent&sent_id=".$_POST['id'];
	$cudata = callAPI($url); 
	echo $cudata; exit;
}
if($page=='dynamictablerowdata'){
	$url = API."?mode=singlerowtabledata&table=".$_POST['table_name']."&fid=id&rid=".$_POST['item_id'];
	//echo $url;
	$response = callAPI($url); 
	$response  = json_decode($response,1);
	$str = $response['total_min_experience'].",".$response['total_max_experience'];
	echo $str; exit;
}
if($page=='insertanupdateempfoodoption'){
	//echo "Ravi ";exit;
	print_r($_POST); //exit;
	$url= API."?mode=insertanupdateempfoodoption&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page == 'updatedynamicfied'){
	print_r($_POST);
	if($_POST['hid']=="undefined"){
		$post = array('employee_id'=>$_SESSION['emp_id'],'company_id'=>$_SESSION['company_id'],'location_id'=>1,'current_status'=>0,'is_active'=>'0','created_by'=>$_SESSION['userid']);
		echo "yes";print_r($post);
		$url= API."?mode=adddata&table=".$_POST['table']."&method=post";
		echo $url; print_r($post);
		$return = file_post_contents($url, $post);
		
	}else{
		$post[$_POST['fid']] = $_POST['rid'];	
		$url= API."?mode=updaterowtabledata&table=".$_POST['table']."&rid=".$_POST['hid']."&method=post";
		echo $url; print_r($post);
		$return = file_post_contents($url, $post);
	}
	echo $return;
}

if($page=='noteassessmentstarttime'){
	$_SESSION['assesment_response_start_time'] = date('H:i:s');
}
if($page=='formassessmentquestions'){
	$typeofquestion= array('MCQ'=>'1','MCA'=>'2','FT'=>'3');
	$optionsdisplaytype= array('1'=>'MCQ','2'=>'MCA','3'=>'FT');

	$uniq_qid = uniqid();
	$questions_list = array('question_id'=>'','rid'=>$_POST['rid'],'title'=>$_POST['title'],'type'=>$_POST['type'],'question'=>$_POST['question'],'option_data'=>explode(',',$_POST['option_data']),'checkedstatus'=>explode(',',$_POST['checkedstatus']),'marks'=>$_POST['marks']);
	//print_r($questions_list);
	$questiondetails = json_encode($questions_list,1);
	$_SESSION['questions_list'][$uniq_qid] = $questiondetails;

	$display_question = '';
	$value_id = 1;
	foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
		$value = json_decode($qvalue,1);
		$options = $value['option_data'];
		$checkedstatus = $value['checkedstatus'];
		$marks = $value['marks'];
		$title = $value['title'];
		$deleteqid = $value['uniq_qid'];
		$question_id = $value['question_id'];
		$rid = $value['rid'];
		$display_question .= ' <div class="mt-5" id="'.$qkey.'"> <div class="col-lg-12 table-bordered">
		<input type="hidden" name="question_id'.$value_id.'" value="'.$question_id.'">
		<input type="hidden" name="rid" value="'.$rid.'">
		<input type="hidden" name="value_id[]" value="'.$value_id.'">
		<input type="hidden" name="options_display_type'.$value_id.'" value="'.$optionsdisplaytype[$value['type']].'">
		<div class="modal-header" >
		<h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
		<i aria-hidden="true" class="ti-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$qkey."'".')"></i>
			
		</div>
		<div class="card-body mt-3">
		<div class="form-group text-left row">
		<div class="col-lg-8">
		<label>Question Text: <span class="text-danger"></span></label>
		<textarea id="assessment_question'.$value_id.'" name="assessment_question'.$value_id.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
		</div>';
		$display_question .= '<div class="col-lg-4">
		<label>Marks <span class="text-danger">*</span></label>
		<input type="text" class="form-control" id="markfield" name="marks'.$value_id.'" value="'.$marks.'"  >
		</div>'; 

		$display_question .= '</div>';
		if($value['type']== $typeofquestion['MCQ'] || $value['type']== $typeofquestion['MCA'])	{ 
			$display_question .= '<div class="col-lg-12 mt-4 mb-4">	Mark the Correct Answer	</div>';
			$display_question .= '<div id="options'.$value_id.'">';
			foreach ($options as $opkey => $opvalue) { 
				$checkstatus = $checkedstatus[$opkey];
				$display_question .= '<div id="optionlist'.$value_id.$opkey.'">
				<div class="form-group row" id="'.$opkey.'">
				<div class="col-lg-12 row ml-5"> <div class="">  '; 
				if($value['type']== $typeofquestion['MCQ'] ) {
					$display_question .= '<label class="radio radio-primary mt-4" >
					<input type="radio"  name="assessment_correct_option'.$value_id.'['.$opkey.']" id="singlechoice'.$value_id.$opkey.'" '.$checkstatus.'>"
					<span></span>
					</label>

					<input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="0" />';

				} else {
					$display_question .= '<label class="checkbox checkbox-primary mt-4">
					<input type="checkbox"  name="assessment_correct_option'.$value_id.'['.$opkey.']"  id="multiplechoice'.$value_id.$opkey.'" '.$checkstatus.'>
					<span></span>
					</label>
					<input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="1" />';
				} 
				$display_question .= '
				
				</div><div class="col-lg-8"> 
				
							 <div class="input-group input-group-button"> 
		<input type="text" class="form-control display-inline width1" name="assessment_question_option'.$value_id.'['.$opkey.']"  placeholder="Option" value="'.$opvalue.'">
		<span class="input-group-text addon-style">
			<label class=""><i class="ti-trash close text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$value_id."'".')"></i> </label>
		</span>
	</div>
				
				</div>';
				$display_question .= '</div>';
			}
			$display_question .= '</div>';
			$op = count($options)+1;
			$display_question .= '<div class="form-group">
			<input type="hidden" value="'.$opkey.'" id="option_row"/>
			<button class="btn btn-success"  onclick="addnewoptionrows('."'".$value_id."'".",'".$value['type']."'".')" id="addNewRows" type="button">+ Add More Options</button>
			</div>';
		}	
		if($value['type']==$typeofquestion['FT'])	{ 
			$display_question .= '	<input type="hidden" name="assessment_question_option'.$value_id.'[]"  class="form-control" value=""> ';
		} 

		$display_question .= '</div> </div>  </div></div>';
		$value_id ++;

	}

	$response = $questiondetails;
	echo $display_question;	
}
if($page=='getcategorybyfilter'){
	$categoryid = "id:".$_POST['categoryid'];
	$drid = deocdeId($categoryid);
	echo $drid;		
}
if($page=='add_perdiem'){
	$url= API."?mode=indertupdateperdiem&method=post";
		//echo $url; print_r($_POST);
	$return = file_post_contents($url, $_POST);	
	echo $return; exit;
}
if($page == 'updateprojecttimesheetsblukforhr'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'],'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	$url = API."?mode=updateprojecttimesheetsblukforhr&method=post";
	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page == 'updateprojecttimesheetsblukapprovehr'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'],'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	//echo "Ravi"; print_r($sarray);
	$url = API."?mode=updateprojecttimesheetsblukapprovehr&method=post";
	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page == 'projecttimesheetsubmittofinace'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'],'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	print_r($sarray);
	$url = API."?mode=projecttimesheetsubmittofinace&method=post";
	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page == 'updateprojecttimesheetsblukapprovefinace'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'],'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	print_r($sarray);
	$url = API."?mode=updateprojecttimesheetsblukapprovefinace&method=post";
	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page=='selectetimesheetlist'){
	$nameslist = $_POST['names'];
	$attendance_month = $_POST['month'];
	$attendance_year = $_POST['year'];
	$shiftkeys = json_decode($_POST['shiftkeys'],1);
	if($nameslist!=ALLEMPLOYEES) {
		$names = json_decode($nameslist,1);
		$nameslist = $names['matches_names'];
		foreach ($nameslist as $value) {
			$emplist[$value['id_value']] = $value['display_name'];
		}
	}else{
		$emplist = '';
	}
	$postdata['emplist'] = $emplist;
	$postdata['month'] = $attendance_month;
	$postdata['year'] = $attendance_year;
	$tableurl = API."?mode=getemployeetimesheetsummary&method=post";
	$empattendance = file_post_contents($tableurl, $postdata);
	$mon = date("F", mktime(0, 0, 0, $attendance_month, 10));
	$getmaxdays=mktime(0, 0, 0, $attendance_month, '01', $attendance_year);
	$maxdays = cal_days_in_month(CAL_GREGORIAN, $attendance_month, $attendance_year);
	$empattendance_data = json_decode($empattendance,1);
	$employee = $empattendance_data['emp_names'];
	$empattendance=$empattendance_data['emp_attendance'];
	//print_r($empattendance); exit;
	$return ='<table class="work-entry text-center" width="100%"><thead><tr><th rowspan="0">Work Entries</th>	<th colspan="31" align="center">'.$mon. " ".$attendance_year.'</th>	</tr><tr>';
	for($i=1;$i<=$maxdays;$i++) { 
		$return .= '<th>'.$i.'</th>';
	}
	$return .= '</tr>	</thead>';
	foreach ($empattendance as $key => $empvalue) {
		$return .= '<tr><th rowspan="2">'.$employee[$key].'</th>';
		foreach ($empvalue as $key => $sessiontime) {
			$return .= '<td class="bg-'.strtolower($sessiontime['S1displaybgcolor']).' text-'.strtolower($sessiontime['S1displaytextcolor']).' '.$showflag.'">'.$sessiontime['S1displayvalue'].'</td>';
		}
		$return .= '</tr>';
		foreach ($empvalue as $key => $sessiontime) {
			$return .= '<td class="bg-'.strtolower($sessiontime['S2displaybgcolor']).' text-'.strtolower($sessiontime['S2displaytextcolor']).' '.$showflag.'">'.$sessiontime['S2displayvalue'].'</td>';
		}
		$return .= '</tr>';
	}
	echo $return;
}
if($page == 'insertseriallot'){
	print_r($_POST);
	$url = API."?mode=insertseriallot&method=post";
	$response = file_post_contents($url, $_POST);
	echo $response; exit;
}
// if ($page == 'imageuploading'){
// 	$url = API."?mode=imageuploading&method=post";
// 	//echo "Step1";
// 	//print_r($_FILES);
// 	$filedetails=array();
// 	$filedetails['originalname'] = basename($_FILES['file']['name']);
// 	$filedetails['filesize'] =basename($_FILES['file']['size']);     
// 	//echo "Step2";print_r($filedetails); echo "Step3";
// 	$name = time().basename($_FILES['file']['name']);
// 	if(move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/attachments/".$name)) {
// 		//echo "Step4";echo "if uplodes"; 
// 		 $filedetails['filename'] = $name;	
// 	}
// 	//$filarray = filleuploading($_FILES);
// 	//echo "Step5"; print_r($filedetails); exit;
// 	$_POST['fila']=$filedetails;
// 	$_POST['company_id']=$_SESSION['company_id'];
// 	$response = file_post_contents($url, $_POST);
// 	echo $response; exit;
// }



if ($page == 'imageuploading'){
	$url = API."?mode=imageuploading&method=post";
	$errors     = array();
	$maxsize    = $_POST['file_size'];
	$acceptable = array('txt','jpeg','jpg','gif','png','docx','doc','csv','text','pdf');
	$message = "";
$wh1["where"] = array('company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['applicationTableName'],'transaction_id'=>$_POST['transactionId'],'file_name'=>$_FILES['file']['name'],'is_active'=>0);
$wh1["field"] = "id";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=application_attachments&data=".$data1; 
$data_details1 = callAPI($url1);
$data_details1 = json_decode($data_details1,1);
if(!empty($data_details1)){
		$errors[1] = $_SESSION['error_message'][3025];
}else{
	if(($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
		 $errors[1] =  $_SESSION['error_message'][3021].' '.round(($_POST['file_size']/1000000),1)." MB";
	}
	
	$string = $_FILES['file']['name'];
	$exploded = explode('.', $string);
	$exploded = end($exploded);
	if(!in_array($exploded, $acceptable) && (!empty($_FILES["file"]["type"]))) {
		$errors[1] =  $_SESSION['error_message'][3022];
	}
	if($_POST['file_limit'] < $_POST['total_files']){
		  $errors[1]  =  $_SESSION['error_message'][3023].' '.$_POST['file_limit'].' file(s)';
	}
}
if(!empty($errors)){
	echo json_encode(array('status'=>$errors[1]));
}else{
	$filedetails=array();
	$filedetails['originalname'] = basename($_FILES['file']['name']);
	$filedetails['filesize'] =basename($_FILES['file']['size']);      
	$name = time().basename($_FILES['file']['name']);
		if(move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/attachments/".$name)) { 
		 $filedetails['filename'] = $name;	
	}
	//$filarray = filleuploading($_FILES);
	$_POST['fila']=$filedetails;
	$_POST['company_id']=$_SESSION['company_id'];
	$response = file_post_contents($url, $_POST);
	echo $response; exit;
	
}
}



if ($page == 'fileuploadingdelete'){
	$pages = API."?mode=fileuploadingdelete&isactive=".$_POST['isactive']."&id=".$_POST['id'];
	$page_list = callAPI($pages);
	$res = json_decode($page_list,1);
	echo $res; exit;
}
if ($page == 'elementortype'){
	textboxes($_POST['id'],$_POST['type'],""); 
	exit;
}

if($page == "fileuploadingstatusdisplay"){
	$message .= '<div class="col-lg-6 fileuploadui" id="fileuploaduiid">
	<label class="fileuplodinglablelui">File Upload</label>
	<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
	<div class="dropzone-msg dz-message needsclick">
	<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
	<span class="dropzone-msg-desc">Upload up to 10 files</span>
	</div></div></div></div>
	<div class="col-lg-6 fileuploaddeleteui" id="fileuploaddeleteuiid">
	<label class="">Delete list</label>
	<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
	</div></div></div>';
	echo $message;			
}

if($page == "elementordesign"){
	$filedTypes ="";
	foreach($filed_array as $key => $value){
		$filedTypes .='<option value="'.$key.'" >'.$value.'</option>';
	} 
	$message ='<div class="card card_count deleterow'.$_POST['id'].'">
	<div class="card-header" id="headingOne'.$_POST['id'].'">
	<div class="card-title collapsed textboxclass'.$_POST['id'].'" data-toggle="collapse" data-target="#collapseOnee'.$_POST['id'].'">
	<i class="flaticon2-cancel-music" onclick="deleterowinputtype('.$_POST['id'].')"></i> Name
	</div>
	</div>
	<div id="collapseOnee'.$_POST['id'].'" class="collapse" data-parent="#accordionExample5">
	<div class="card-body">
	<div class="example-preview">
	<ul class="nav nav-pills nav-fill">
	<li class="nav-item">
	<a class="nav-link active" id="content_'.$_POST['id'].'" data-toggle="tab" href="#content-'.$_POST['id'].'"   >
	<span class="nav-text">Content</span>
	</a>
	</li>
	<li class="nav-item">
	<a class="nav-link" id="advanced_'.$_POST['id'].'" data-toggle="tab" href="#advanced-'.$_POST['id'].'"  aria-controls="profile" >
	<span class="nav-text">Advanced</span>
	</a>
	</li>
	</ul>
	<div class="tab-content mt-5" id="myTabContent4">
	<div class="tab-pane fade show active" id="content-'.$_POST['id'].'" role="tabpanel" aria-labelledby="content_1">
	<div class="form-group row">
	<label class="col-6 col-form-label">Field Name</label>
	<div class="col-6">
	<input class="form-control elementor_fieldname space elementor_fieldname'.$_POST['id'].'" data-id="'.$_POST['id'].'"  type="text" name="fieldname[]" value="" />
	</div>
	</div>

	<div class="form-group row">
	<label  class="col-6 col-form-label ">Field Type</label>
	<div class="col-6">
	<select class="form-control elementor_fieldtype elementor_fieldtype'.$_POST['id'].'" name="elementorfieldtype[]" data-elementory_id="inputbox'.$_POST['id'].'" data-id="'.$_POST['id'].'"  data-select2-id="" tabindex="-1" aria-hidden="true">
	<option value="varchar" >String</option>
	<option value="text" >Description</option>
	<option value="int" >Number</option>
	<option value="decimal">Decimal</option>
	<option value="date">Date</option>
	<option value="datetime" >Date Time</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Position</label>
	<div class="col-6">
	<input class="form-control" type="text" name="display_postion[]" value="'.$_POST['count'].'" />
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Label & Input One Line</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" data-id="'.$_POST['id'].'" class="elementory_labelandinputtype" data-elementory_id="inputbox'.$_POST['id'].'" data-label="inputlabel'.$_POST['id'].'" data-div="divbox'.$_POST['id'].'"  
	name="labelandinputineline['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>

	<div class="form-group row">
	<label  class="col-6 col-form-label ">Type</label>
	<div class="col-6">
	<select class="form-control elementor_text_box_type" onchange="Getinserttype(this.id,this.value)" id="type" name="elementorytexttype[]" data-elementory_id="inputbox'.$_POST['id'].'" data-id="'.$_POST['id'].'"  data-select2-id="" tabindex="-1" aria-hidden="true">'.$filedTypes.'
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Label</label>
	<div class="col-6">
	<input class="form-control elementory_lable elementory_lable'.$_POST['id'].'" data-labletext="labletext'.$_POST['id'].'" data-elementory_id="inputlabel'.$_POST['id'].'" data-id="'.$_POST['id'].'"  type="text" value="label"  name="elementorylabel[]" />
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Display</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" class="display_status" data-id="'.$_POST['id'].'" checked name="display['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Insert Display List</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" checked  class="display_status" data-id="'.$_POST['id'].'" name="insert_display_list['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Accordion</label>
	<div class="col-6">
	<select class="form-control" name="accordion[]" >
	<option value="N" >Normal</option>
	<option value="R" >Rigth</option>
	<option value="L" >Left</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Wizard</label>
	<div class="col-6">
	<select class="form-control" id="exampleSelect1" name="insert_view_wizard[]">
	<option value="1" selected>1- Wizard </option>
	<option value="2" >2- Wizard </option>
	<option value="3" >3- Wizard </option>
	<option value="4">4- Wizard </option>
	<option value="5">5- Wizard </option>
	<option value="6" >6- Wizard </option>
	<option value="7" >7- Wizard </option>
	<option value="8" >8- Wizard </option>
	<option value="9" >9- Wizard </option>
	<option value="10" >10- Wizard </option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Placeholder</label>
	<div class="col-6">
	<input class="form-control elementory_placeholder  elementory_placeholder'.$_POST['id'].'" type="text" value="" name="elementorplacehilder[]" data-elementory_id="inputbox'.$_POST['id'].'" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Required</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" data-requerd= "labletextrequerd'.$_POST['id'].'" data-label="inputlabel'.$_POST['id'].'" data-div="divbox'.$_POST['id'].'" class="elementory_required" data-elementory_id="inputbox'.$_POST['id'].'" name="elementoryrequerd['.$_POST['increment'].']"/>
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Column Width</label>
	<div class="col-6">
	<select class="form-control elementory_width" data-id="'.$_POST['id'].'" data-elementory_id="divbox'.$_POST['id'].'" name="elementorycolunmwidth[]"  data-select2-id="" tabindex="-1" aria-hidden="true">'.optiontype(6,$widtharray).'</select>
	</div>
	</div>
	</div>
	<div class="tab-pane fade" id="advanced-'.$_POST['id'].'" role="tabpanel" aria-labelledby="advanced_1">
	<div class="form-group row">
	<label  class="col-5 col-form-label">Default Value</label>
	<div class="col-7">
	<div class="input-group">
	<input type="text" class="form-control valuesdefoult" data-id="'.$_POST['id'].'" name="valuesdefoult[]" value="" >
	<div class="input-group-append">
	<button class="btn btn-secondary" type="button">
	<i class="flaticon-coins"></i></button>
	</div>
	</div>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">ID Name</label>
	<div class="col-7">
	<input class="form-control elementory_id" name="elementoryid[]" type="text" data-elementory_id="inputbox'.$_POST['id'].'" value="id name" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<div class="col-12">
	<p class="text-muted font-size-xs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s</p>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Shortcode</label>
	<div class="col-7">
	<input class="form-control" type="text" value="" id=""/>
	</div>
	</div>
	</div>
	</div>
	</div></div></div></div>';
	$inputtype ='<div class="divbox'.$_POST['id'].'  col-lg-6 mt-5" id="divid'.$_POST['id'].'" >
	<label class="textcapital col-form-label text-lg-right inputlabel'.$_POST['id'].'"><span class="labletext'.$_POST['id'].'">labe</span><span class="text-danger labletextrequerd'.$_POST['id'].'" style="display:none"  > * </span></label>	
	<div class="inputtypediv'.$_POST['id'].'" >			
	<input type="text"  class="form-control form-control-lg inputbox'.$_POST['id'].'" name="inputtype'.$_POST['id'].'" placeholder="" />
	</div></div>';

	echo json_encode(array('setting'=>$message,'textfileds'=>$inputtype));
}

if($page== "elementordesignbutton"){
	$message = '<div class="card buttons_count mt-3 deletebutton'.$_POST['id'].'">
	<div class="card-header" id="headingOnebutton'.$_POST['id'].'">
	<div class="card-title collapsed title'.$_POST['id'].'" data-toggle="collapse" data-target="#collapseOneebutton'.$_POST['id'].'" aria-expanded="false">
	<i class="flaticon2-cancel-music" onclick="deletebutton('.$_POST['id'].')"></i> Save
	</div>
	</div>
	<div id="collapseOneebutton'.$_POST['id'].'" class="collapse" data-parent="#accordionExample5" style="">
	<div class="card-body">
	<div class="form-group row">
	<label  class="col-5 col-form-label">Function Type</label>
	<div class="col-7">
	<select class="form-control function_type function_type'.$_POST['id'].'" data-id="'.$_POST['id'].'" data-select2-id="" name="functiontype[]" tabindex="-1" aria-hidden="true">
	<option value="save" data-select2-id="">save</option>
	<option value="saveandcontinue" data-select2-id="">Save & continue</option>
	<option value="update" data-select2-id="" >Update</option>
	<option value="print" data-select2-id="">Print</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Size</label>
	<div class="col-7">
	<select class="form-control button_size button_sizes'.$_POST['id'].'" id="" data-id="'.$_POST['id'].'" name="buttonsize[]" data-select2-id="" tabindex="-1" aria-hidden="true">
	<option value="btn-xs" data-select2-id="">Extra Small</option>
	<option value="btn-sm" data-select2-id="">Small</option>
	<option value="" data-select2-id="" selected>Medium</option>
	<option value="btn-lg" data-select2-id="">Large</option>
	<option value="btn-lg btn-block" data-select2-id="">Extra Large</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Column Width</label>
	<div class="col-7">
	<select class="form-control buttonwidth buttonwidth'.$_POST['id'].'" id="" data-id="'.$_POST['id'].'" name="buttonwidth[]" data-select2-id="" tabindex="-1" aria-hidden="true">'.optiontype(6,$widtharray).'</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Button Position</label>

	<input type="hidden"  id="buttonposition'.$_POST['id'].'" name="buttonposition[]" value="text-left">

	<div class="col-7">
	<button type="button"  value="text-left" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="flaticon2-soft-icons"></i>
	</button>
	<button type="button" value="text-right" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="fas fa-align-right"></i>
	</button>
	<button type="button" value="text-center" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="fas fa-align-justify"></i>
	</button>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Button Name</label>
	<div class="col-7">
	<input class="form-control buttonname buttonname'.$_POST['id'].'" name="buttonname[]" data-id="'.$_POST['id'].'" type="text" value="save" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Form Class</label>
	<div class="col-7">
	<input class="form-control button_class button_class'.$_POST['id'].'" name="buttonclass[]" data-id="'.$_POST['id'].'" type="text"  value="save" id=""/>
	</div></div></div></div></div>';

	$button = '<div class="col-lg-6" id="buttondiv'.$_POST['id'].'">

	<button type="button" class="btn btn-primary mr-2 textcapital" id="buttonid'.$_POST['id'].'"><i class="la la-check"></i>Save</button>
	</div>';		
	echo json_encode(array('setting'=>$message,'textfileds'=>$button));

}


//start auto ui designs
if($page == "gettableavailable"){

	$url = API."?mode=gettablerow&tablename=".$_POST['table']."&fields=".$_POST['fields']."&where=".$_POST['where']."&list=1"; 
	$collfunction = callAPI($url);
	// $jsondecode = json_decode($collfunction,1);
	echo $collfunction;
}
if($page == "getfildsarraycreatedocuments"){
	$inputFildsArray = getListCreatDocumentsFields();
	echo json_encode($inputFildsArray);
}
//print designing
if($page == "printdesigningtextfields"){
	$tableattributes= getListAttributes($_POST['tablename']);
	$fileds="";
	foreach($tableattributes as $result){ 
		$fields .="<option value='".$result['Field']."'>".$result['Field']."</option>"; 
	}
	$text = '<div class="card">
	<div class="card-header" id="headingOnee'.$_POST['num'].'">
	<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOnee'.$_POST['num'].'">
	Text Fields
	</div>
	</div>
	<div id="collapseOnee'.$_POST['num'].'" class="collapse" data-parent="#accordionExample5">
	<div class="card-body">
	<div class="">
	<div class="tab-content mt-5" id="myTabContent'.$_POST['num'].'">
	<div class="tab-pane fade show active btn-sm" id="content-1" role="tabpanel" aria-labelledby="content_1">
	<div class="row mt-2">
	<label  class="col-5 col-form-label">Label</label>
	<div class="col-7">
	<input class="form-control textfiledslabel"  data-id="'.$_POST['num'].'" name="label[]" name="textfiledslabel[]" type="text" value="label" >
	</div>
	</div>
	<div class="row mt-2">
	<label  class="col-5 col-form-label">Field</label>
	<div class="col-7">
	<select class="form-control" name="textfileds[]">'.$fields.'</select>
	</div>
	</div>
	<div class="row mt-2">
	<label  class="col-5 col-form-label">Column Width</label>
	<div class="col-7">
	<select class="form-control textfiledswidth textfiledswidth'.$_POST['num'].'" data-id="'.$_POST['num'].'" name="textfiledswidth[]">
	'.optiontype('4',$widtharray).'
	</select>
	</div>
	</div>
	<div class="row mt-2">
	<label  class="col-5 col-form-label">Single Line</label>
	<div class="col-7">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" name="textfiledinline['.$_POST['num'].']" checked class="textfiledinline" data-id="'.$_POST['num'].'" ><span></span>
	</label>
	</span>
	</div>
	</div></div></div></div></div></div></div>';
	$textfileds = '<div class="d-flex mb-3 col-lg-4" id="textfiledid'.$_POST['num'].'"><span class="mr-15 font-weight-bolder" id="textfiledlabel'.$_POST['num'].'">Label :</span><span  class="text-right " id="textfiledvalue'.$_POST['num'].'">eg. details function</span></div>';					
	echo json_encode(array('dropdown'=>$text,'textfileds'=>$textfileds));
}
//end print designing
//Start project task
if($page=="projecttaskchart"){
	$url = API."?mode=projecttaskchart&empid=".$_POST['empid']; 
	$task = callAPI($url);
	$jsontask = json_decode($task,1);
	$status = array("0"=>array("Active","primary"),"3"=>array("Inprogess","info"),"5"=>array("Ready","warning"),"4"=>array("Complete","success"));
	$priority = array("label-danger"=>"high","label-warning"=>"medium","label-success"=>"low");
	//$priority = array("light-danger"=>"high","text-warning"=>"medium","text-success"=>"low");
	$taskarray =array();
	foreach($status as $key=>$status){
		$pusharray = array();
		$new_array = filterArrayValue($jsontask,'current_status',$key);
		foreach($new_array as $values){
			//$statuscolor = filterArrayValue(ViewStatusData(),'name',"Active");
			$prioritycolor = array_search($values['priority'], $priority);
			if($values['image']!=""){
				$img = '<img alt="Pic" src="assets/media/users/300_24.jpg" />';	
			}else{
				$img = "<h1>".initials($values['display_name'])."</h1>";
			}
			$pusharray[]= array("title"=>'<div class="d-flex align-items-center" ><div class="symbol symbol-success mr-3">'.$img.'</div> <div class="d-flex flex-column  align-items-start"><span class=" font-weight-bold mb-1 ">'.$values['task_name'].'</span> <span class="label label-inline  font-weight-bold '.$prioritycolor.'">'.strtoupper($values['priority']).'</span></div></div>','taskid'=>$values['id'],'tab'=>'xyz');	
		}
		$taskarray[] = array("id"=>"_".$status[0],'title'=>$status[0],'class'=>$status[1],'item'=>$pusharray);
	}
	echo json_encode($taskarray);
}

if($page=="updateprojecttaskstatuse"){
	$status = array("0"=>"Active","3"=>"Inprogess","5"=>"Ready","4"=>"Complete");
	$statuse = array_search($_POST['dropclass'], $status);
	$url = API."?mode=updateprojecttaskstatuse&id=".$_POST['taskid']."&status=".$statuse; 
	$task = callAPI($url);
	echo json_encode($task);
}
//End  project task
if($page=="deletemyreport"){
	$url = API."?mode=deletemyreport&reportid=".$_POST['id']; 
	$task = callAPI($url);
	$jsontask = json_decode($task,1);

}
//start job_posting_chart
if($page=="getchartjobreport"){
	$url = API."?mode=getchartjobreport&from=".$_POST['from']."&to=".$_POST['to']; 
	echo $task = callAPI($url);
}
//END job_posting_chart
//start custom_view_head_count_current_report
if($page=="getheadcountcurrentreport"){
	$url = API."?mode=getheadcountcurrentreport&from=".$_POST['from']."&to=".$_POST['to']; 
	echo $task = callAPI($url);
}
//END custom_view_head_count_current_report

//start COM_VIEW_CHART_CANDIDED_REPORT
if($page=="getchartcandidatesreport"){
	$url = API."?mode=getchartcandidatesreport&from=".$_POST['from']."&to=".$_POST['to']; 
	echo $task = callAPI($url);
}
//END COM_VIEW_CHART_CANDIDED_REPORT
if($page=="esign_employee"){
	//print_r($_POST);
	$url = API."?mode=toinsertandpdateesignemployee&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page=="toinsertandpdateesignemployeeonlyone"){
	//print_r($_POST);
	$url = API."?mode=toinsertandpdateesignemployeeonlyone&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page=="insertemphistries"){
	//print_r($_POST);
	$_POST['company_id']=$_SESSION['company_id'];	
	$_POST['user_id']=$_SESSION['userid'];	
	$url = API."?mode=insertemphistries&method=post";
	echo $response = file_post_contents($url, $_POST);
}
if($page=="deletetimesheets"){
	//print_r($_POST);
	$url = API."?mode=deletetimesheets&id=".$_POST['id']; 
	$task = callAPI($url);
	echo $task;
}
if($page=="getbugetamountesignvendorwise"){
	$curl = API."?mode=getbugetamountesignvendorwise&extra=".$_POST['extra']."&emp_ids=".$_POST['rid'];
	$cudata = callAPI($curl);
	echo $cudata;
	//echo 1;
}
 if($page=="emp_exit_survey"){
	//echo "<pre>"; print_r($_POST);
	$commondata = array('duration_taken'=>0,'partial_complete'=>0,'category'=>$_POST['category'],'employee_id'=>$_POST['emp_id'],'survey_id'=>$_POST['survey_id'],'response_start_date'=>date('Y-m-d'),'response_start_time'=>$_POST['response_start_time'],'response_end_date'=>date('Y-m-d'),'response_end_time'=>date('H:i:s'),'created_date'=>date('Y-m-d'),'created_by'=>$_SESSION['userid'],'is_active'=>'0','company_id'=>$_SESSION['company_id'],'current_status'=>ST_COMPLETE);
	//print_r($commondata);
	$data['survey_response'] = $commondata;
	unset($commondata['duration_taken']);
	unset($commondata['partial_complete']);
	unset($commondata['category']);
	
	foreach($_POST['value_id'] as $q_no) {
	   $ans_data= $_POST['response_option'.$q_no];
	   $commondata['question_id']=$_POST['questionid'.$q_no];
	   $commondata['response_option']= json_encode($ans_data,1);
	   $data['survey_response_details'][] = $commondata;
	}
	//echo "<pre>"; print_r($data); exit;
	$url = API."?mode=savesurveyreponses&table=survey_responses&method=post";
	$retunn_inserted_qid = file_post_contents($url,$data,'insert'); 
	echo $retunn_inserted_qid; exit;
 }
 if($page=='add_share_data'){
	$data_post = array('company_id'=>$_SESSION['company_id'],'module_setup_id'=>$_SESSION['work_module_setup_id'],'application_table'=>$_POST['share_table'],'transaction_id'=>$_POST['share_table_id'],'share_id'=>$_POST['share_id'],'comments'=>$_POST['comments'],'is_active'=>'0','current_status'=>'0','created_by'=>$_SESSION['userid'],'created_date'=>date("Y-m-d H:i:s"));
	print_r($data_post);
	$url= API."?mode=adddata&table=application_share&method=post";
	$return = file_post_contents($url, $data_post);	
	$url= API."?mode=tosendnotifiations&method=post";
	$option = "COM_JOB_CANDIDATE_POPUP&job_post_can_id=".$_POST['share_table_id'];
	$notdataa = array('company_id'=>$_SESSION['company_id'], 'notification_message'=>"Candidate Profile is Shared", 'transaction_number'=>$notarry['share_table_id'], 'application_table_name'=>$_POST['share_table'], 'type'=>'ACTION', 'notification_users'=>array($_POST['share_id']),'option'=>$option);
	print_r($notdataa);
	$response = file_post_contents($url, $notdataa);
	echo $return; exit;
}
if($page=="interviewivoitesbt"){
	$_POST['company_id'] = $_SESSION['company_id'];
	  $url = API."?mode=interviewschedule&method=post";
	  //echo $url; print_r($_POST);
	  $response = file_post_contents($url, $_POST);
	  $result = json_decode($response,1);
	  //print_r($result);
	  echo $response;
}
if($page=="insertcommentjobposting"){

	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$_POST['job_selection_step_id']=$_POST['job_selection_step_id'];
	$_POST['job_posting_candedate_id']=$_POST['job_post_can_id'];
	$url = API."?mode=insertcommentjobposting&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');

  //   $url = API."?mode=insertcommentjobposting&value=".$_POST['value']."&company_id=".$_SESSION['company_id']."&job_posting_candedate_id=".$_POST['job_post_can_id']."&job_selection_step_id=".$_POST['job_selection_step_id']; 
		// echo  $data_details = callAPI($url);
}
if($page=="inserttagjobposting"){
	$value  = json_encode($_POST['value']);
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$_POST['value']=$value;
	$_POST['job_selection_step_id']=$_POST['job_selection_step_id'];
	$_POST['job_posting_candedate_id']=$_POST['job_post_can_id'];
	$url = API."?mode=inserttagjobposting&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');	
}

if($page=="updatecandidateshortlist"){
	$url = API."?mode=updatecandidateshortlist&cand_id=".$_POST['cand_id']."&status=".$_POST['status']."&user_role=".$_POST['user_role']."&candidate_id=".$_POST['candidate_id']; 
	echo  $data_details = callAPI($url);		
}
if($page=='formcandidatefeedbackquestions'){
	$uniq_qid = uniqid();
	$questions_list = array('question_id'=>'','rid'=>$_POST['rid'],'title'=>$_POST['title'],'type'=>$_POST['type'],'question'=>$_POST['question'],'require_response'=>$_POST['require_response'],'allow_multiple_selections'=>$_POST['allow_multiple_selections'],'requir_ratting'=>$_POST['requir_ratting'],'option_data'=>explode(',',$_POST['option_data']));
	$questiondetails = json_encode($questions_list,1);
	$_SESSION['questions_list'][$uniq_qid] = $questiondetails;

	$display_question = '';
	$totalquestion = count($_SESSION['questions_list']);
	$value_id = 1;
	foreach ($_SESSION['questions_list'] as $qkey => $qvalue) {
		$value = json_decode($qvalue,1);
		$allow_multiple_selections_status = $value['allow_multiple_selections']=='0'?'checked' :'';
		$require_response_status = $value['require_response']=='0'?'checked' :'';
		$options = $value['option_data'];
		$totaloption = count($options);
		$title = $value['title'];
		$deleteqid = $value['uniq_qid'];
		$question_id = $value['question_id'];
		$rid = $value['rid'];
		//$required_ratting = $value['requir_ratting'];

		$display_question .= ' <div class="mt-5" id="'.$qkey.'"> <div class="col-lg-12 table-bordered">
		<input type="hidden" name="totalquestion" value="'.$totalquestion.'">
		<input type="hidden" name="question_id'.$value_id.'" value="'.$question_id.'">
		<input type="hidden" name="rid" value="'.$rid.'">
		<input type="hidden" name="value_id[]" value="'.$value_id.'">

		<input type="hidden" name="options_display_type'.$value_id.'" value="'.$value['type'].'">

		<div class="modal-header" >
		<h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
		<i aria-hidden="true" class="ti-trash close text-white"  data-dismiss="modal" aria-label="Close" onclick="deletequestion('."'".$qkey."'".')"></i>
		
		</div>
		<div class="card-body mt-3">
		<div class="row col-lg-12">
		<div class="form-group col-lg-8 text-left">
		<label>Question Text: <span class="text-danger"></span></label>
		<textarea id="survey_question'.$value_id.'" name="survey_question'.$value_id.'" class="form-control" placeholder=""> '.$value['question'].' </textarea>
		</div>
		<div class="form-group col-lg-4 text-left">
			<label>Required Ratting(Out of 5): <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="required_ratting'.$value_id.'" id="required_ratting'.$value_id.'" value="'.$value['requir_ratting'].'">
		</div>
		</div>
		<div class="form-group text-left">
		<div class="checkbox-inline">';
		if($value['type']==1 || $value['type']==3)	{ 
			$display_question .='
			<label class="checkbox checkbox-primary">
			<input type="checkbox" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" '.$allow_multiple_selections_status.'/>
			<span></span>
			Allow multiple selections
			</label>';
		}
		else{
			$display_question .='<input type="hidden" name="allow_multiple_selections'.$value_id.'" id="allow_multiple_selections'.$value_id.'" value="1"/>';
		}
		$display_question .='
		<label class="checkbox checkbox-primary">
		<input type="checkbox" name="require_response'.$value_id.'" id="require_response'.$value_id.'" '.$require_response_status.'>
		<span></span>
		Require a Response
		</label>
		</div>
		</div>';
		if($value['type']==1 || $value['type']==3)	{ 
			$display_question .= '<div id="options'.$value_id.'">';
			foreach ($options as $opkey => $opvalue) { 
				$display_question .= '<div id="optionlist'.$value_id.$opkey.'">
				<div class="form-group row" id="'.$opkey.'">
				<div class="col-lg-1"></div>
				<div class="col-lg-8">
				<div class="input-group input-icon-right">
				<input type="text" class="form-control display-inline" name="survey_question_option'.$value_id.'[]"  placeholder="Option" value="'.$opvalue.'">
				<div class="input-group-append"><span class="input-group-text bg-primary"><span>
				<i class="ti-trash flaticon-delete text-white" onclick="delnewoptionrows('."'".$opkey."'".','."'".$value_id."'".')"></i>
				</span></span></div></div>
				</div>
				</div>
				</div>
				';
			}
			$display_question .= '</div>';
			$op = count($options)+1;
			$display_question .= '<div class="form-group">
			<input type="hidden" value="'.$opkey.'" id="option_row"/>
			<button class="btn btn-success"  onclick="addnewoptionrows('."'".$value_id."'".')" id="addNewRows" type="button">+ Add More Options</button>
			</div>';
		}	
		if($value['type']==2)	{ 
			$display_question .= '	<div class="form-group text-left mt-10">
			<label>Scale from 1 to: <span class="text-danger">*</span></label>
			<input type="number" name="survey_question_option'.$value_id.'[]"  class="form-control" placeholder="'.$options[0].'" value="'.$options[0].'" > 
			</div>';
		} 	
		if($value['type']==4)	{ 
			$display_question .= '	<input type="hidden" name="survey_question_option'.$value_id.'[]"  class="form-control" value=""> ';
		} 

		$display_question .= '</div> </div>  </div>';
		$value_id ++;

	}

	$response = $questiondetails;
	echo $display_question;
}
if($page=="job_posting_candidate_response"){
	//print_r($_POST);
	$url = API."?mode=savejobfeedbackreponses&method=post";
	$retunn_inserted_qid = file_post_contents($url,$_POST,'insert'); 
	echo $retunn_inserted_qid; exit;
}
if($page=="insertrole"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id']=$_SESSION['company_id']; 
	$url = API."?mode=insertrole&method=post";
		echo $response = file_post_contents($url, $_POST); 
		exit;  
		//$resutlvalue = json_decode($response,1);
}

if($page=="getjobdecription"){
	  $wh1["where"] = array('id'=>$_POST['id']);
	  $wh1["field"] = "id,job_title,job_description";
	  //$wh1['order'] = "id DESC";
	  $data1 = urlencode(json_encode($wh1));
	  $url1 = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$data1; 
	  echo $data_details1 = callAPI($url1);
}
if($page=="resetnewpassword"){

		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['userid'] = $_SESSION['userid'];
	$_POST['Rid'] = $_POST['Rid'];
		$url = API."?mode=resetnewpassword&method=post";
		$response = file_post_contents($url, $_POST);
		echo $response;
}
if($page == "getfildsarrayemailcontains"){
	$table_name = "email_content";
$wh1["where"] = array('apply_table_name'=>$table_name,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$wh1["field"] = "id,fields,label,application_table_name,preview_value";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=create_documents_fields&data=".$data1; 
$jsondecode = callAPI($url1);
$jsondecode = json_decode($jsondecode,1);
$inputFildsArray =array();
foreach($jsondecode as $result){
	$inputFildsArray["{[".$result['application_table_name'].".".$result['fields']."]}"] = array(ucwords($result['label']),ucwords($result['preview_value']));
}
echo json_encode($inputFildsArray);
}
// start treeeeeeeee
//user list tree
if($page=="getfamilytreelist"){
	  $url = API."?mode=getfamilytreelist&user_ids=".$_POST['user_id']; 
	  $task = callAPI($url);
	  echo $task;
}
//End user list tree

//employee list tree
if($page=="getemployeetreelist"){
	  $url = API."?mode=getemployeetreelist&user_ids=".$_POST['user_id']; 
	  $task = callAPI($url);
	  echo $task;
}
//End employee list tree
//Chart of account list tree
if($page=="getchartofaccountreelist"){
	  $url = API."?mode=getchartofaccountreelist&user_ids=".$_POST['user_id']; 
	  $task = callAPI($url);
	  echo $task;
}
//End Chart of account list tree
if($page=="selecttablenamefiledlist"){
$return = array();
$aray = explode(",",$_POST['table']);
$option = "";
$returntable = "";    
foreach($aray as $aray){
	$option .= '<div class="col-lg-3 tablenameclass'.$aray.'"><label>'.ucwords(str_replace('_', ' ', $aray)).'*</label><select class="form-select js-example-basic-multiple tablefiledsselect" onchange="buildQuery()" data-table="'.$aray.'" name="tablefiledsselect['.$aray.'][]" multiple required>';
		$res = getListAttributes($aray);
		foreach($res  as $ress){
			$option .= "<option value='".$aray.".".$ress['Field']."'>".$ress['Field']."</option>";
		}
		$option .="</select></div>"; 
}
//echo "hello";
echo json_encode(array('option'=>$option));
}
if($page=="selectfiledlists"){
	$return = array();
	$aray = explode(",",$_POST['table']);
	$option = "";
	foreach($aray as $aray){
			$res = getListAttributes($aray);
			foreach($res  as $ress){
				$option .= "<option value='".$aray.".".$ress['Field']."'>".$aray.".".$ress['Field']."</option>";
			}
	}
	//echo $option;
	echo json_encode(array('option'=>$option));
}
/**
if($page=="selecttablenamefiledlist"){
$return = array();
$aray = explode(",",$_POST['table']);
$option = "";
$returntable = "";
foreach($aray as $aray){
	$option .= '<div class="col-lg-3 tablenameclass'.$aray.'"><label>'.$aray.'</label><select class="form-select js-example-basic-multiple tablefiledsselect" onchange="buildQuery()" data-table="'.$aray.'" name="tablefiledsselect['.$aray.'][]" multiple>';
		$res = getListAttributes($aray);
		foreach($res  as $ress){
			$option .= "<option value='".$aray.".".$ress['Field']."'>".$ress['Field']."</option>";
		}
		$option .="</select></div>"; 
}
echo json_encode(array('option'=>$option));
}

if($page=="selectfiledlists"){
	$return = array();
	$aray = explode(",",$_POST['table']);
	$option = "";
	foreach($aray as $aray){
			$res = getListAttributes($aray);
			foreach($res  as $ress){
				$option .= "<option value='".$aray.".".$ress['Field']."'>".$aray.".".$ress['Field']."</option>";
			}
	}
	echo json_encode(array('option'=>$option));
}
**/
if($page=="selectworkflowfiledlist"){
$option = "";
	$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['table_name']);
$wh1["field"] = "id,list_attributes";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$data1; 
$pagenamesapi = callAPI($url1);
$pagenamesrow = json_decode($pagenamesapi,1);
$listfields = json_decode($pagenamesrow[0]['list_attributes'],1);
	foreach($listfields as $key=>$aray){
				$valuefield = $key."-".$_POST['table_name']."-".$aray['type'];
				$option .= "<option value='".$valuefield."'>".$aray['lable']."</option>";
	}
	echo json_encode(array('option'=>$option));
}


if($page=="getworkflowdisplayattributevalues"){
	$option = "";
	//print_r($_POST);
	$listval = dyanamicselectdata($_POST['table_name'],$_POST['field_name']);
	foreach($listval as $key=>$aray){
				$option .= "<option value='".$key."'>".$aray."</option>";
	}
	echo json_encode(array('option'=>$option));
}

if($page=="getapprovetypelist"){
	$option = "<option value=''>Select Any</option>";
	$val = $_POST['val'];
	if($val=="user"){
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "id,display_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			$listval[$lists['id']] =  $lists['display_name'];
		}
	}elseif($val == 'email_groups'){
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "id ,name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=email_groups&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			$listval[$lists['id']] =  $lists['name'];
		}
	}elseif($val == 'access_levels' || $val == 'location_wise'){
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "id ,role_short_code,name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=application_roles&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			$listval[$lists['role_short_code']] =  $lists['name'];
		}
	}elseif($val == 'role_levels' || $val =='user_hierarchical'){
		$wh1["where"] = array('genc_code'=>"HIERARCHICALLEVEL",'is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "genc_detail_code ,genc_detail_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			if($lists['genc_detail_name'] != "Others"){
				$listval[$lists['genc_detail_code']] =  $lists['genc_detail_name'];
			}
		}
	}elseif($val == 'buddy'){
		$wh1["where"] = array('genc_code'=>"EMPLOYEEBUDDIES",'is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "genc_detail_code ,genc_detail_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			if($lists['genc_detail_name'] != "Others"){
				$listval[$lists['genc_detail_code']] =  $lists['genc_detail_name'];
			}
		}
	}elseif($val == 'individual_employee_role'){
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "id,role_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=employment_roles&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		foreach($list as $lists){
			$listval[$lists['id']] =  $lists['role_name'];
		}
	}elseif($val == 'custom_field'){

		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['process_name']);
		$wh1["field"] = "id,list_attributes";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$data1; 
		$pagenamesapi = callAPI($url1);
		$pagenamesrow = json_decode($pagenamesapi,1);
		$listfields = json_decode($pagenamesrow[0]['list_attributes'],1);
		foreach($listfields as $key=>$aray){
			$listval[$key] =  $aray['lable'];
		}
	}
	if(count((array)$listval) > 0){
			foreach($listval as $key=>$aray){
					$option .= "<option value='".$key."'>".$aray."</option>";
			}
		}
		$fieldoption = "<option value=''>Select Any</option>";
		if($val == 'location_wise'){
			$whf["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['process_name']);
			$whf["field"] = "id,list_attributes";
			$dataf = urlencode(json_encode($whf));
			$urlf = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$dataf; 
			$pagenamesapif = callAPI($urlf);
			$pagenamesrowf = json_decode($pagenamesapif,1);
			$listfieldsf = json_decode($pagenamesrowf[0]['list_attributes'],1);
			if(count((array)$listfieldsf) > 0){
				foreach($listfieldsf as $key=>$aray){
					$fieldoption .= "<option value='".$key."'>".$aray['lable']."</option>";
				}
			}
		}
	echo json_encode(array('option'=>$option, 'field_option'=>$fieldoption));
}

if($page=="gettasklistworkflow"){
	$list = getList('process_task_settings');
	$list = json_decode($list,1);
	foreach($list as $aray){
				$option .= "<option value='".$aray['id']."'>".$aray['task_name']."</option>";
	}
	echo json_encode(array('option'=>$option));
}
if($page=="commanworkflowsubmit"){
	if($_POST['type']=="afteraprovalemailnotification"){
		$postdata['send_aproval_email_to'] = $_POST['send_aproval_email_to'];
		$postdata['after_approval_email'] = $_POST['after_approval_email'];
	}else if($_POST['type']=="afterrejectionemailnotification"){
		$postdata['send_rejection_email_to'] = $_POST['send_rejection_email_to'];
		$postdata['after_rejection_email'] = $_POST['after_rejection_email'];
	}else if($_POST['type']=="afteraprovalwebhook"){
		$postdata['after_approval_call_webhook'] = $_POST['after_approval_call_webhook'];
	}else if($_POST['type']=="afteraprovalcallfunction"){
		$postdata['after_approve_call_function'] = $_POST['after_approve_call_function'];
	}else if($_POST['type']=="afterrejectionwebhook"){
		$postdata['after_rejection_call_webhook'] = $_POST['after_rejection_call_webhook'];
	}else if($_POST['type']=="afterrejectioncallfunction"){
		$postdata['after_rejection_call_function'] = $_POST['after_rejection_call_function'];
	}else if($_POST['type']=="afteraprovassigntask"){
		$ret = array();
		foreach($_POST['after_approval_task'] as $keyy=>$task){
			$ret[$keyy] = $task;
		}
		$postdata['after_approval_task'] = json_encode($ret);
	}else if($_POST['type']=="afterrejectionassigntask"){
		$ret = array();
		foreach($_POST['after_rejection_task'] as $keyy=>$task){
			$ret[$keyy] = $task;
		}
		$postdata['after_rejection_task'] = json_encode($ret);
	}else if($_POST['type']=="custom_process"){
		$ret = array();
		foreach($_POST['customsteps'] as $keyu => $steps){
			$f = $keyu+1;
			$ret['step_'.$f] = $steps;
			$approveupdatestatus[] = array("status"=>'-1','followup_days'=>'0','auto_forword_days'=>'0','work_flow_field'=>'','user_id'=>$steps);
		}
		$postdata['approval_type'] = $_POST['approval_type'];
		$postdata['sub_approve_type'] = json_encode($ret);
		$postdata['approve_update_status'] = json_encode($approveupdatestatus);
	}else if($_POST['type']=="aprove_updatefields"){
		$ret = array();
		foreach($_POST['status'] as $key => $filelds){	
			$ret[] = array("status"=>$_POST['status'][$key],'followup_days'=>$_POST['followup_days'][$key],'auto_forword_days'=>$_POST['auto_forword_days'][$key],'work_flow_field'=>$_POST['work_flow_field'][$key]);
		}
		$postdata['approve_update_status'] = json_encode($ret,1);
	}else if($_POST['type']=="rejection_updatefields"){
		$ret = array();
		foreach($_POST['status'] as $key => $filelds){	
			$ret[] = array("status"=>$_POST['status'][$key],'followup_days'=>$_POST['followup_days'][$key],'auto_forword_days'=>$_POST['auto_forword_days'][$key],'work_flow_field'=>$_POST['work_flow_field'][$key]);
		}
		$postdata['rejection_update_status'] = json_encode($ret,1);
	}
	$postdata['id'] = $_POST['Rid'];
	$url = API."?mode=insertworkflownew&method=post";
	$postdata['updated_by'] = $_SESSION['userid'];
	//print_r($postdata);
	$response = file_post_contents($url,$postdata,'insert');
	if($_POST['type'] == 'aprove_updatefields' || $_POST['type']=='custom_process'){
		echo json_encode($ret);
	}else{
		echo $response; 
	}
}
if($page=="insertprocesstasksettings"){
	$company_id = $_SESSION['company_id'];
		$url = API."?mode=gettasksdetails&company_id=".$company_id."&is_active=0";
		$response = callAPI($url);
		$tasks_details = json_decode($response,1);
		$_POST['created_date'] = nowDate();
		$_POST['created_by'] = $_SESSION['userid'];
		$_POST['current_status'] = 0;
		if($_POST['due_date']=="no_action") {
		  $_POST['no_action'] = '1';
		}else if($_POST['due_date']=="on_the_day") {
		  $_POST['on_the_day'] = '1';
		}
		$task_list = $tasks_details['task_list'][$_POST['process_id']];
		$_POST['display_color'] = $task_list['display_color'];
		$_POST['display_background_color'] = $task_list['display_background_color'];
		$_POST['row_id']=$_POST['Rid_id'];
		foreach ($_POST['checklist'] as  $key=>$value) {
			$listdata[$key]['downloadfile']	 = $_POST['hidden_file'][$key];
			$listdata[$key]['assigned_to']	 = $_POST['assigned_to'][$key];
			$listdata[$key]['approval_to']	 = $_POST['approval_to'][$key];
		 if($value!=""){
			if(isset($_FILES['file'.$key])){
				//$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$_POST['candidate_id']."/Documents";
				$compay_file = imageuploadwitharray("all",'file'.$key,'','',$key);	
				$listdata[$key]['downloadfile']= $compay_file;
			}
			$checkboxid = $_POST['checkboxid'][$key];
			$listdata[$key]['mandatory_checklist'] = $_POST['mandatory_checklist'][$checkboxid] == "Y"?"Y":"N";
			$listdata[$key]['attachment_requerd'] = $_POST['attachment_requerd'][$checkboxid] == "Y"?"Y":"N";
			
		   $listdata[$key]['name'] = $value;
		   $listdata[$key]['check_value'] = "";
		 }
		}

		$_POST['check_list'] = json_encode($listdata,1);
		
		 $url = API."?mode=processtasksettings&method=post";
		$response = file_post_contents($url,$_POST,'insert');
		echo $response; //exit;
}
if($page == 'dynamicchangestatusnew'){
	$pagename = getPagenametotable($_POST['table'],'approval_page_name');//."&pagename=".$pagename;  
	$_POST['pagename'] = $pagename;
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['emp_id'] = $_SESSION['emp_id'];
	$url = API."?mode=updatedynamicapprovallognew&method=post";
	echo $result = file_post_contents($url,$_POST,'insert');
}
if($page == 'checkemployeeleaveavailableornot'){
	$leavecheckurl = API."?mode=checkemployeeleaveavailableornot&emp_id=".$_SESSION['emp_id']."&leavetype=".$_POST['leave_type_id']."&leave_from=".$_POST['leave_from']."&leave_to=".$_POST['leave_to']."&rid=".$_POST['rid']."&old_leave_days=".$_POST['old_leave_days']."&start_half_day=".$_POST['start_half_day']."&end_half_day=".$_POST['end_half_day'];
	$leavecheckresponse = callAPI($leavecheckurl);
	$details = json_decode($leavecheckresponse,1);
	if($_POST['leave_from']!="" AND $_POST['leave_to']!=""){	
		$date2 = $_POST['leave_to'];
		$date2 = str_replace('/', '-', $date2);
		$date1 = $_POST['leave_from'];
		$date1 = str_replace('/', '-', $date1);
		$details['leave_days'] = (strtotime($date2) - strtotime($date1)) / (60 * 60 * 24)+1;
	}
	$details_res = json_encode($details);
	echo $details_res;
}
if($page == 'leavedaystoworkdays'){
	if($_POST['leave_from']!="" AND $_POST['leave_to']!=""){	
		$date2 = $_POST['leave_to'];
		$date2 = str_replace('/', '-', $date2);
		$date1 = $_POST['leave_from'];
		$date1 = str_replace('/', '-', $date1);
		$details['leave_days'] = (strtotime($date2) - strtotime($date1)) / (60 * 60 * 24)+1;
	}
	$details_res = json_encode($details);
	echo $details_res;
}
if($page=="selectleavetypehalfdayavailableornot"){
	$leavecheckurl = API."?mode=checkleavebalanceorhalfday&emp_id=".$_SESSION['emp_id']."&categorytype=".$_POST['value'];
	$leavecheckresponse = callAPI($leavecheckurl);
	echo $leavecheckresponse;
}
if($page=="submittocheckcriteria"){
 $newarray  = ['tablename'=>$_POST['tablename'],'rid'=>$_POST['id'],'exicute_type'=>$_POST['exicute_type']];
  $checkcriteria = checkWhoShouldApprove($newarray);
echo $checkcriteria;
}
if($page=="createuser"){
	$str = "";
	$role = $_POST['user_application_role'];
	if(isset($_POST['approval_manager_id'])!=""){
		$role = $_POST['user_application_role'];
		$str = "approval_manager_id=".$_POST['approval_manager_id']."&reporting_manager_id=".$_POST['reporting_manager_id'];
	}
	$checkurl = API."?mode=tocerateapplicationuser&empid=".$_POST['emp_id']."&role_id=".$role."&".$str;
	$ccdata = callAPI($checkurl);
	echo $ccdata;
}
if($page == "getstatvaluewithlist"){
	$option = "";
	$chk["where"] = array('is_active'=>0);
	 $wh1["field"] = "id,state_name";
	$chks = urlencode(json_encode($chk));
	$sateurl = API."?mode=gettabledetailswithconditions&table=states&data=".$chks; 
	$satedata =  callAPI($sateurl);
	$satedata = json_decode($satedata,1);
	foreach($satedata as $satedatas){
	$selected = "";
	if($_POST['state_id'] ==$satedatas['id']){
		$selected = "selected";
	}
		$option .="<option value='".$satedatas['id']."'  ".$selected.">".$satedatas['state_name']."</option>"; 
	}
	echo $option;
}
if($page=="insertcheckpayroll"){
	$url1 = API."?mode=insertcheckpayroll&id=".$_POST['id']."&year=".$_POST['year']."&month=".$_POST['month']; 
	$data_details1 = callAPI($url1);
	echo $data_details1;
}
if($page=="vrcdata"){
	//print_r($_POST);
	$wh1["where"] = array('company_id'=>$_SESSION['company_id'],'transaction_id'=>$_POST['vid'],'item_id'=>$_POST['item_id'],'is_active'=>0);
	//print_r($wh1);
	$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=vrc_items&data=".$data1; 
	$data_details1 = callAPI($url1);
	$data_details1 = json_decode($data_details1,1);
	//print_r($data_details1);
	//echo $data_details1['0']['min_exp'].",".$data_details1['0']['max_exp'];
	echo json_encode($data_details1['0']);
}
if($page=='edittimesheetbillablehrs'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id']=$_SESSION['company_id'];   
	  $url = API."?mode=edittimesheetbillablehrs&method=post";
	  echo $response = file_post_contents($url, $_POST);
}
if($page=="dyanamicreferfunc"){
	$respons = dyanamicreferFunc($_POST['val'],$_POST['referfield'],$_POST['table']);
	$option = '<option value="">Select '.ucwords(str_replace("_"," ",$_POST['referfield'])).'</option>';
	foreach ($respons as $key => $value) {
		$option .="<option value=".$key.">".$value."</option>"; 
	}
	echo $option;
	exit;
}
if($page=='insertpmcasignemployee'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id']=$_SESSION['company_id']; 
	$url = API."?mode=insertpmcasignemployee&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page=="getdaterangempc"){
	$return = array();
	$eefectivedate = $_POST['effectiveDate'] ==""?date($_SESSION['date_formate']):$_POST['effectiveDate'];
	if($_POST['vall']=="PM"){
		$return = array('effectiveDate'=>$eefectivedate,'expire_date'=>date($_SESSION['date_formate'], strtotime($eefectivedate."+1 months")));
	}else if($_POST['vall']=="PY"){
		$return = array('effectiveDate'=>$eefectivedate,'expire_date'=>date($_SESSION['date_formate'], strtotime($eefectivedate."+1 years")));

	}else if($_POST['vall']=="PS"){
		$return = array('effectiveDate'=>$eefectivedate,'expire_date'=>date($_SESSION['date_formate'], strtotime($eefectivedate."+6 months")));
	
	}else if($_POST['vall']=="PT"){
	$return = array('effectiveDate'=>$eefectivedate,'expire_date'=>date($_SESSION['date_formate'], strtotime($eefectivedate."+3 months")));
	}else{
	$return = array('effectiveDate'=>'','expire_date'=>'');	
	}
	echo json_encode($return);
}
if($page == 'getworkorderperhrstime'){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['id']);
	$wh1["field"] = "id,per_hrs_rate";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=mpc_items&data=".$data1;
	$data_details1 = callAPI($url1);
	$data_details1 = json_decode($data_details1,1);
	echo $data_details1[0]['per_hrs_rate'];
}
if($page=="closenotificationinfo"){	
   $url1 = API."?mode=closenotificationinfo&id=".$_POST['id']; 	
	$data_details1 = callAPI($url1);	
	//echo $data_details1;	
	exit;	
}
if($page == 'current_status_drop_project_timesheets'){
	$str = '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9040'].'</div>';
	//print_r($_POST);
	$user_id = $_POST['user_id'];
	if($_SESSION['userid'] == $user_id){
	   $approval_manager_id = $_SESSION['user']['approval_manager'];
	   $reports_to = $_SESSION['user']['reports_to'];
	}else{
	   $url = API."?mode=singlerowtabledata&table=application_users&fid=id&rid=".$user_id;
	   //echo $url;
	   $response = callAPI($url); 
	   $response  = json_decode($response,1);
	   //print_r($response);
	   $approval_manager_id = $response['approval_manager'];
	   $reports_to = $response['reports_to'];	   
	}
	if($approval_manager_id !="" && $reports_to !=""){
	   $appruser_list_data = togetaprovaluserlist($_POST['table'], $_POST['id']);
	   $appruser_list = $appruser_list_data['user'];
	   $tablewr["where"] = array('id'=> $_POST['id']);
	   $tablecon = urlencode(json_encode($tablewr));
	   $tableurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table']."&data=".$tablecon; 
	   $tabledata = callAPI($tableurl);
	   $tabledata = json_decode($tabledata,1);
	   $str ="";	
	   $checkapprovemanager = checkApproveManager($user_id,$_POST['current_status']);
	   $lists = toGetStatusProgressAction($_POST['table'], $_POST['current_status']);
	   //echo "test"; print_r($lists);
	   if($checkapprovemanager == ST_APPROVAL_MANAGER AND ($_POST['current_status']==ST_ACTIVE || $_POST['current_status']==ST_SEND_BACK )){
		foreach ($lists as $list){ 
			$str .= '<div class="dropdown-item" onclick="statuspopupActiveStatusOnly('.ST_APPROVAL_MANAGER.','. $_POST[id] .')" name="status" id="status" value="'.ST_APPROVAL_MANAGER.'">'.$list[action_label].'</div>';	
		}
	   }elseif($checkapprovemanager == ST_APPROVAL_PENDING AND ($_POST['current_status']==ST_ACTIVE || $_POST['current_status']==ST_SEND_BACK)){
		foreach ($lists as $list){ 
			$str .= '<div class="dropdown-item" onclick="statuspopupActiveStatusOnly('.ST_APPROVAL_PENDING.','. $_POST[id] .')" name="status" id="status" value="'.ST_APPROVAL_PENDING.'">'.$list[action_label].'</div>';	
		}
	   }else{
		foreach ($lists as $list){
			$str .= '<div class="dropdown-item" onclick="statuspopup('.$list[status].','. $_POST[id] .')" name="status" id="status" value="'. $list[status] .'">'.$list[action_label].'</div>';	
		}
	   }
	   $str_d=$str;
		   if(count($appruser_list)>0){ //echo "1"; //print_r($_SESSION);
		 if(!in_array($user_id, $appruser_list)){
			$str = '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9002'].'</div>';
		  }
	   }else{ //echo "2";
		if($user_id != $tabledata[0]['created_by'] && $_SESSION['userid'] != $tabledata[0]['created_by']){
			$str = '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9002'].'</div>';
		}
	  }
	  if($user_id == $tabledata[0]['created_by'] && $_POST['current_status']==ST_SEND_BACK){
		$str = $str_d;
	  }
	  if($str == ""){ //echo "3";
		$str = '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9003'].'</div>';
	  }
	}
	echo $str; exit;
}
if($page == 'dynamicchangestatusprojecttimesheets'){
	$farray  = array('current_status' => $_POST['field']);
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'], 'emp_id' => $_SESSION['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'hid' => $_POST['hid'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'updated_by'=>$_SESSION['userid']);
	//print_r($sarray); exit;
	$url = API."?mode=updateapprovallogprojecttimesheets&method=post";

	$response = file_post_contents($url, $sarray);
	echo $response; exit;
}
if($page == 'setdynamichelptext'){
	$table_name = $_POST['table_name'];
		$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'application_table_name'=>$table_name,'display'=>'1');
		$wh1['order'] = "sequence ASC";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=application_tours&data=".$data1; 
		$data_details1 = callAPI($url1);
		$json = json_decode($data_details1,1);
		$i=1;
		foreach($json as $jsons){
		$field = "";
		if($jsons['field_type'] == 'multi_select' || $jsons['field_type'] == 'array'){
			$field = $jsons['reference_field_name'].'[]';
		}else{
			$field = $jsons['reference_field_name'];
		}
		$listfieldarray[$field] = array('sequence'=>$i,'helptext'=>$jsons['help_text'],'type'=>$jsons['field_type']);
		$i++;
}
	echo json_encode($listfieldarray);
}
if($page == 'vrcandpoemployeeassign'){
	print_r($_POST);
	$url = API."?mode=vrcandpoemployeeassign&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page == 'insertupdatepovrcpopup'){
	print_r($_POST);
	$url = API."?mode=insertupdatepovrcpopup&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page == 'insertupdateesgcpopup'){
	print_r($_POST);
	$url = API."?mode=insertupdateesgcpopup&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page=="addorremoveusersubtable"){
	print_r($_POST);
	$url = API."?mode=addorremoveusersubtable&method=post"; 	
	$return = file_post_contents($url, $_POST, 'update');
	exit;	
}
if($page=="closenotificationinfo"){
	$url1 = API."?mode=closenotificationinfo&id=".$_POST['id']; 
	$data_details1 = callAPI($url1);
	//echo $data_details1;
	exit;
} 
//attendance form leave 
if($page == 'inserttempattendaceform'){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=inserttempattendaceform&method=post";
	$response = file_post_contents($url, $_POST);
	$json = json_decode($response,1);
	$listfieldarray = array('return'=>$_POST,'leavedate'=>$json);
	echo json_encode($listfieldarray);
	
}
if($page == "getavailableleaveemployee"){
$url1 = API."?mode=checkleavebalanceorhalfday&categorytype=".$_POST['leavetype']."&emp_id=".$_POST['emp_id']."&leave_year=".$_POST['year']."&month=".$_POST['month']; 
		 $data_details1 = callAPI($url1);
	  $result = json_decode($data_details1,1);
	echo json_encode(array("applicable_leaves"=>$result['leave_details']['applicable_leaves'],"applicable_leaves"=>$result['leave_details']['applicable_leaves'],"consumed_leaves"=>$result['leave_details']['consumed_leaves'],"available_leaves"=>$result['leave_details']['applicable_leaves']-$result['leave_details']['consumed_leaves']-$result['templeaves'],"halfday"=>$result['leave_details']['halfday'],"templeaves"=>$result['templeaves']));

}
if($page == "insertemployeeworkattendances"){
		echo $url1 = API."?mode=insertemployeeworkattendances&month=".$_POST['month']."&year=".$_POST['year']."&monthstartday=".$_POST['monthstartday']."&admin_type=".$_SESSION['admin_type']."&emp_id=".$_SESSION['emp_id']; 
	   echo  $data_details1 = callAPI($url1);
}
//attendance form leave 
//Tickets Start
if($page=='inserttickets'){
	$filedetails=array();
	if(isset($_FILES['file'])){
	$filedetails['originalname'] = basename($_FILES['file']['name']);
	$filedetails['filesize'] =basename($_FILES['file']['size']);      
	$name = time().basename($_FILES['file']['name']);
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/attachments/".$name)) {
			$filedetails['filename'] = $name;
		}
	}
	$_POST['fila']=$filedetails;
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	unset($_POST['file']);
	$url = API."?mode=inserttickets&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; 
}
if($page == 'updateticketdetails'){
$url = API."?mode=updateticketdetails&id=".$_POST['id']."&filed_name=".$_POST['filed_name']."&value=".$_POST['value']."&uid=".$_POST['uid'];
$rseponse = callAPI($url);
echo $rseponse;
}
//Tickets end
//insert employees
if($page == "insertemployeedetails"){

	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$_POST['display_name'] = $_POST['display_name']!=""?$_POST['display_name']:$_POST['first_name']." ".$_POST['last_name'];
	//print_r($_POST);
	$url = API."?mode=insertemployeedetails&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response,1);
	//echo "--------"; print_r($res);
	echo $response;
}
//insert employees
if($page=='insertpayareaasignemployee'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id']=$_SESSION['company_id']; 
	print_r($_POST);
	$url = API."?mode=insertpayareaasignemployee&method=post";
			$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page=='insertpayrolladminasignemployee'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id']=$_SESSION['company_id']; 
	//print_r($_POST);
	$url = API."?mode=insertpayrolladminasignemployee&method=post";
	$response = file_post_contents($url,$_POST);
	echo $response; exit;
}
if($page == 'dynamicchangestatusweeklybulkapproval'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'], 'response_date' => date('Y-m-d'),'table' => $_POST['table'],'emp_id' => $_POST['emp_id'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'weekstartdate'=>$_POST['weekstartdate'],'weekenddate'=>$_POST['weekenddate'],'timesheetid'=>$_POST['timesheetid'],'updated_by'=>$_SESSION['userid']);
	//print_r($_POST); exit;
	$data = urlencode(json_encode($sarray));
	$url = API."?mode=changestatusweeklybulkapproval&data=".$data;
	$rseponse = callAPI($url);
	echo $rseponse; //exit;
}
if($page == 'updateprojecttimesheetsblukforhrweekly'){
	$sarray  = array('comments' => $_POST['comment'], 'response_type' => $_POST['field'], 'user_id' => $_SESSION['userid'],'emp_id' => $_POST['emp_id'], 'response_date' => $_POST['date'],'table' => $_POST['table'],'extra' => $_POST['extra'],'company_id'=> $_SESSION['company_id'],'status'=>$_POST['status'],'weekstartdate'=>$_POST['weekstartdate'],'weekenddate'=>$_POST['weekenddate'],'timesheetid'=>$_POST['timesheetid'],'updated_by'=>$_SESSION['userid']);
	$data = urlencode(json_encode($sarray));
	$url = API."?mode=updateprojecttimesheetsblukforhrweekly&data=".$data;
	$rseponse = callAPI($url);
	echo $rseponse; //exit;
}
if($page =="getweekinmonth"){
	$mess = "";
	$weekly_date = get_weeks($_POST['year'],$_POST['month']);
	foreach($weekly_date as $keyweek=>$weekly_date){
		if(date("dmY",strtotime($startingdate)) == date("dmY",strtotime($weekly_date[0]))){
			$class = 'nav-link bg-primary me-6 active';
		}else{                  
			$class = 'btn btn-light';
		}
		$mess .= '<div class="float-left m-r-5 b-radius-5 nav-item ">';
		$mess .='<a class="'.$class.' nav-link  me-6 active " style="font-size: 12px !important;" href="index.php?option=COM_LIST_WEEKLY_BULK_APPROVAL_TIMESHEETS&SDATE='.$weekly_date[0].'&EDATE='.$weekly_date[count($weekly_date)-1].'">Week - '.$keyweek;
		$mess .='</br><span class="f-10">'.date("d D",strtotime($weekly_date[0])).'</span></a>';
		$mess .='</div>';
	  } 
	  echo $mess;
}
if($page == "insertstocktakesitems"){
	$uomaray= array();
	foreach($_POST['popupqty'] as $key=>$value){
		$uomaray[$key]['qty'] = $value;
		$uomaray[$key]['conversion_ratio'] = $_POST['conversion_ratio'][$key];
		$uomaray[$key]['uom_id'] = $_POST['popuouomid'][$key];
	}	
	$uom_details = json_encode($uomaray);
	$_POST['uom_details'] = json_encode($uomaray);
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertstocktakesitems&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $uom_details;
}
if($page == "startstocktakes"){
	$url1 = API."?mode=startstocktakes&userid=".$_POST['userid']."&sid=".$_POST['sid']; 
	echo $data_details1 = callAPI($url1);
}
if($page == "getstocktakelocations"){
	$url1 = API."?mode=getstocktakelocations&selectitems=".$_POST['selectitems']; 
	$location = callAPI($url1);
	$locations = json_decode($location,1);
	foreach($locations as $locatio){
		$option .="<option value='".$locatio['location_id']."'>".$locatio['location_name']."</option>"; 
	}
	echo $option;
}
if($page =="softdeletedrecord"){
	$url1 = API."?mode=softdeletedrecord&id=".$_POST['id']."&table=".$_POST['table']; 
	echo $recorddeleted = callAPI($url1);
}
/* asset allocation */
if($page =="insertasset"){
	//print_r($_POST);
	//print_r($_FILES);
	$File_Uploads_Path = "uploads/assets/";
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			//$filename = $newfilename;
			$_POST['asset_img'] = $newfilename;
		}
	}else{
		$_POST['asset_img'] = $_POST['oldattachment'];
	} 
	$custom_field= array();
	foreach($_POST['fieldname'] as $key=>$value){
		$custom_field[$key]['fieldname'] = $value;
		$custom_field[$key]['fieldtype'] = $_POST['fieldtype'][$key];
		$custom_field[$key]['fieldvalue'] = $_POST['fieldvalue'][$key];
	}
	unset($_POST['fieldname']);	
	unset($_POST['fieldtype']);	
	unset($_POST['fieldvalue']);	
	//$_POST['custom_field'] = json_encode($custom_field);
	$_POST['custom_field'] =urlencode(json_encode($custom_field));
	//$_POST['asset_img'] = $filename;
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertasset&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page=="insertassetallocation"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertassetallocation&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	//print_r($response);
}
if($page=="insertassetallocationsingle"){
	print_r($_POST);
	$File_Uploads_Path = "uploads/assets/";
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['asset_img'] = $newfilename;
		}
	}else{
		$_POST['asset_img'] = $_POST['oldattachment'];
	} 
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$_POST['singleasset']="Y";
	$url = API."?mode=insertassetallocation&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page =="assetaccepts"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=assetaccepts&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
}
if($page =="insertassetreturn"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertassetreturn&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
}
if($page=="insertassetrequest"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	//print_r($_POST);
	$url = API."?mode=insertassetrequest&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
}
if($page == "selectAssetCategory"){
	$url1 = API."?mode=getassetandattributes&cat_id=".$_POST['catgoryid']; 
	$assets = callAPI($url1);
	$json = json_decode($assets,1);
	$checkattributes = "";
	$option = '<option value="">Select Any One</option>';
	foreach($json['assetlist'] as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['asset_description']."</option>"; 
	}
	$checked = $_POST['check']=="N"?"":"checked";
	foreach($json['asset_attributes'] as $asset_attributes){
		$checkattributes .='<div class="checkbox-fade fade-in-primary mt-2">
									  <label class="form-label">
										<input type="checkbox" name="attribute[]" '.$checked.' value="'.$asset_attributes['id'].'" >
										<span class="cr">
										  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
										</span>
										<span>'.$asset_attributes['name'].'</span>
									  </label>
									</div>'; 
	}
	echo  json_encode(array('option'=>$option,'asset_attributes'=>$checkattributes));
}
if($page == "insertassetextension"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertassetextension&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
}
if($page=="updateassetallocation"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=updateassetallocation&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$json = json_decode($response,1);
	print_r($json);
}

/* asset allocation */

if($page == "getmodulesetupoption"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'module_group_id'=>$_POST['gmid']);
	$wh1['field'] = "id,module_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['module_name']."</option>"; 
	}
	echo $option;
}

//bill demo 
if($page =="insertempexpenses"){
	$File_Uploads_Path = "uploads/reimbursement/";
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$filename = $newfilename;
		}
	}
	if($filename ==""){
		$filename = $_POST['oldattachment'];
	}   
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['bill_file'] = $filename;
	$url = API."?mode=insertandupdateempexpenses&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	echo deocdeId("id:".$response['LASTID']); 
}
if($page=="assignemployeelistpolicy"){
	$empurl = API."?mode=assignemployeelistpolicy&emp_id=".$_POST['emp_id']."&policy_number=".$_POST['policy_number']."&newcreate=".$_POST['newcreate'];
	echo $empres = callAPI($empurl);
	//echo $empcos = json_decode($empres,1);
}
if($page=="cehckdaterange"){
	$selectdate = strtotime($_POST['selectdate']);
$startdate = strtotime($_POST['startdate']);
$enddate = strtotime($_POST['enddate']);
	if($selectdate >= $startdate && $selectdate <= $enddate){
	   echo "1";
	}else{
	   echo "0";  
	}  
}
if($page == 'updatestatusvendordocuments'){
	print_r($_POST);
	$tableurl = API."?mode=updatestatusvendordocuments&table=".$_POST['table']."&status=".$_POST['status']."&hid=".$_POST['hid']."&vendor_register_id=".$_POST['vendor_register_id']."&user_idd=".$_POST['user_id']; 
	$return = callAPI($tableurl);
	echo $return;
}
 
if($page == 'reaprovevendordoc'){
	$tableurl = API."?mode=reaprovevendordoc&useridd=".$_POST['userid']."&vendor_id=".$_POST['vendor_id']."&hid=".$_POST['hid']; 
	$return = callAPI($tableurl);
	echo $return;
}
//bill demo

if($page=="assetallocationaws"){
	print_r($_POST);
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=assetallocationaws&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	print_r($response);
	echo $return;
}

if($page =="assetacceptreectvms"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	print_r($_POST);
	$url = API."?mode=assetacceptreectvms&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	print_r($response);
}
if($page == "getsubcategroy"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'parent_category_id'=>$_POST['cat_id']);
	$wh1['field'] = "id,category_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=tasks_categories&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['category_name']."</option>"; 
	}
	echo $option;
}
if($page == "getsubacts"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'parent_category_id'=>$_POST['act_id']);
	$wh1['field'] = "id,category_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=act_categories&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['category_name']."</option>"; 
	}
	echo $option;
}
if($page =="setcompliance"){
	$url1 = API."?mode=setcompliance&id=".$_POST['id'];  
	$data_details1 = callAPI($url1);
	$response = json_decode($data_details1,1);
	print_r($response);
}
if($page =="setcomplianceallocation"){
	$url1 = API."?mode=setcomplianceallocation&start_date=".$_POST['start_date']."&end_date=".$_POST['end_date'];  
	$data_details1 = callAPI($url1);
	$response = json_decode($data_details1,1);
	print_r($response);
}
if($page =="insertcompliancetaskanswer"){
	//print_r($_POST);
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertcompliancetaskanswer&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
	//$response = json_decode($response,1);
	//print_r($response);
}
if($page == "updateticketfollowers"){
	//print_r($_POST);
	$_POST['popupfollow_id'] = implode(',',$_POST['popupfollow_id']);
	$url1 = API."?mode=updateticketfollowers&rid=".$_POST['rid']."&popupfollow_id=".$_POST['popupfollow_id'];  
	$data_details1 = callAPI($url1);
	$response = json_decode($data_details1,1);
	print_r($response);
}
if($page=="startworkflow"){
	//print_r($_POST);
	$pagename = getPagenametotable($_POST['transaction_table'],'approval_page_name');//."&pagename=".$pagename;  
	$url1 = API."?mode=startworkflow&rid=".$_POST['rid']."&tablename=".$_POST['transaction_table']."&level=1&emp_id=".$_SESSION['emp_id']."&pagename=".$pagename;  
	echo $data_deta = callAPI($url1);
	//echo $response = json_decode($data_deta,1);
	//$response = json_decode($data_deta,1);
	//print_r($response);
}
if($page =="addremovetransport"){
	//print_r($_POST);
	echo $url = API."?mode=addremovetransport&method=post";
	$response = file_post_contents($url,  $_POST['data'], 'insert');
	echo $rseponse; exit;
}
if($page=="closeallnotifications"){
   print_r($_POST);
   $url1 = API."?mode=closeallnotifications&type=".$_POST['type']."&user_id=".$_POST['user_id'];
   $data_details1 = callAPI($url1);
   exit;
}
if($page =="getemployeementlevel"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'employment_band_id'=>$_POST['id']);
	$wh1['field'] = "id,level_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=employment_levels&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['level_name']."</option>"; 
	}
	echo $option;
}
if($page=="reportfloderlist"){	
	$data12["where"]['is_active'] = '0';
	$data12["where"]['parent_id'] = $_POST['report_id'];
	$data = urlencode(json_encode($data12));
	$itemurl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$data; 
	$item_data = callAPI($itemurl);
	$row = json_decode($item_data, 1);
	
	$str = "";
	$str.='<input type="hidden" id="page_id" value="'.$_POST['report_id'].'"/>
		<input type="hidden" id="ppage_name" value="'.$_POST['page_name'].'"/>';
	foreach ($row as  $value) {
		$page_name = "'".$value['page_name']."'";
		if($value['header'] == ""){
			$str.='<div class="col-lg-12 row mb-3 f-16">
				<div class=" col-lg-1">
					<i class="ti-folder"></i>
				</div>
				<div class=" col-lg-10">
				<a href="javascript:void(0)">
					'.
					  $value['page_name'].'
					  </a> 
				</div>
				<div class=" col-lg-1" onclick="listchangebase('.$value['id'].','.$page_name.')">
					<a href="javascript:void(0)"><i class="ti-angle-down"></i></a>
				</div>
			</div>';
		}
	}
	echo $str; exit;	
}
if($page =="addfloderupdatereportid"){
	$url = API."?mode=addfloderupdatereportid&method=post";
	$response = file_post_contents($url,  $_POST, 'insert');
	echo $rseponse; 
	
}
if($page=="checkandaddmontlock"){
	//print_r($_POST); exit;
	$yearmonth = explode("-",$_POST['yearmonth']);
	$vdata1["where"] = array('application_table_name'=> $_POST['application_table_name'],'month'=>$yearmonth['1'],'year'=>$yearmonth['0'],'is_active'=>'0');
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=application_monthly_lock&data=".$vdata; 
	$res = callAPI($vurl);
	$resdata = json_decode($res,1);
	//print_r($vdata1); print_r($resdata); print_r($_SESSION['error_message']);
	$return = "success";
	if($resdata['0']['id']==""){
		//echo "--------------dd-----";
		$vurl = API."?mode=getcountpendingtimesheet&yearmonth=".$_POST['yearmonth']; 
		$res = callAPI($vurl);
		$countdata = json_decode($res,1);
		if($countdata['pcount']==0){
			$data['application_table_name'] = $_POST['application_table_name'];
			$data['month'] = $yearmonth['1'];
			$data['year'] = $yearmonth['0'];
			if($_POST['hid']!=""){
				$data['updated_date'] = nowDate();
				$data['updated_by'] = $_SESSION['userid'];
				$url = API."?mode=updaterowtabledata&rid=".$_POST['hid']."&table=application_monthly_lock&method=post";
			$return = file_post_contents($url, $_POST, 'update');
			}else{
				$data['company_id'] = $_SESSION['company_id'];
				$data['current_status'] = 0;
				$data['is_active'] = 0;
				$data['status_date'] = nowDate();
				$data['from_date'] = date('Y-m-d');
				$data['to_date'] = date('Y-m-d');
				$url = API."?mode=adddata&table=application_monthly_lock&method=post";
				$res = file_post_contents($url, $data, 'insert');
			}
		}else{
			$return = $_SESSION['error_message']['9045'];
		}
	}else{
		//echo "--------------xx-----";
		$return = $_SESSION['error_message']['9044'];
	}
	echo  $return; exit;	
}
//Rohan Added 22-11-2022
if($page =="updatepormanace"){
	$url1 = API."?mode=updatepormanace&id=".$_POST['id']."&applicable=".$_POST['applicable']."&appraisal_id=".$_POST['appraisal_id'];  
	echo $data_deta = callAPI($url1);
}
if($page =="addkra"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=addkra&method=post";
	echo $response = file_post_contents($url,  $_POST, 'insert');
}
if($page =="getkpalist"){
	//$kpaidlist = implode(',',$_POST['selected_kpa']);
	$krakpiid = $_POST['organization_goalsidd'];
	$url1 = API."?mode=getorganizationgoalsdetails&kra_list=Y&kpaidlist=".$_POST['selected_kpa']."&krakpiid=".$krakpiid."&appraisal_id=".$_POST['appraisal_id'];  
	$response = callAPI($url1);
	$kipandkradetails = json_decode($response,1);
	$modellist  = $kipandkradetails['modellist'];
	$upmllist = $kipandkradetails['uomllist'];
	$kralist = $kipandkradetails['kralist'];
	$kpilistres = $kipandkradetails['kpilist'];
	$frequencyllist = $kipandkradetails['frequencylist'];
	foreach($kpilistres as $kpilistre){
		$kpilist[$kpilistre['key_reslut_id']][] = $kpilistre;
	}
	$kpiderails = array();
	foreach($kipandkradetails['kralid'] as $kralist0){
		$krakpiid[$kralist0['id']] = $kralist0['id'];
		$organization_goalsidd[$kralist0['ogid']] = $kralist0['ogid'];
		$kpiderails[$kralist0['key_performance_id']] = $kralist0;
	}
	include("views/custom_insert_organization_goal_ajax.php");
}
if($page == "checkassemntyearcopetency"){
	$url = API."?mode=getjobtitlecompetencies&checkassetyear=Y&assessment_year=".$_POST['assemntyearcopetency']; 
	echo $response = callAPI($url);
	//$json = json_decode($data_details1,1);
}
if($page == "updatejoblevelcompetency"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['type'] = "update";
	$url = API."?mode=insertjobtitlecompetencies&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response1 = json_decode($response,1);
	//$json = json_decode($data_details1,1);
}

if($page == "inserperformanaceappraiserheader"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['new_entry'] = "Y";
	$url = API."?mode=inserperformanaceappraiserheader&method=post";
	$response0 = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response0,1);
	$lastid = $response1['exicute']['LASTID'];
	$_POST['rid'] = $lastid;
	include("views/custom_insert_performance_appraisal_headers_ajax.php");
//	$returnarray = array("LATSID"=>$lastid,'INSERT_PAGE'=>$html_page);
	//echo $returnarray;
}
if($page == "updateperformanaceappraiserheader"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=inserperformanaceappraiserheader&method=post";
	$response0 = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response0,1);
	$lastid = $response1['exicute']['LASTID'];
	include("views/custom_insert_performance_appraisal_headers_ajax.php");
}
if($page == "publichappraisal"){
	$url1 = API."?mode=publichappraisal&id=".$_POST['id']."&assessment_year=".$_POST['assessment_year'];  
	echo $data_deta = callAPI($url1);

}
if($page == "insertappraisalkra"){
	$url1 = API."?mode=insertappraisalkra&appraisal_id=".$_POST['appraisal_id'];  
	echo $data_deta = callAPI($url1);

}
if($page == "getorganizationgoalcheck"){
	$arrays = array();
	$functionsetshowkra =  "onclick='setkraandkpifun()'";
	$functionsetgoal =  "onclick='checkalllocation()'";
	$htmlview = '<div class="col-lg-12 row  mb-3"><div class="col-lg-6"><div class="checkbox-fade fade-in-primary m-l-20"><label class="form-label"><input class="form-control itemRow select_all_goalsset" type="checkbox"  '.$functionsetgoal.'> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>Select All</label></div></div><div class="col-lg-6 text-right"><button type="button"  '.$functionsetshowkra.'  class="btn btn-primary" ><i class="fa fa-share"></i> Set Target</button> </div></div>';
	$loc_id = array();
	$dep_id= array();
	$des_id = array();
	$locdepdesigary = array();
	$url1 = API."?mode=getorganizationgoalcheck&appraisal_id=".$_POST['appraisal_id']."&location_id=".$_POST['location_id']."&department_id=".$_POST['department_id']."&designation_id=".$_POST['designation_id'];  
	$data_detar = callAPI($url1);
	$data_deta = json_decode($data_detar,1);
	foreach($data_deta['ogres'] as $ogres){
		$locdepdesigary[$ogres['location_id']][$ogres['department_id']][$ogres['job_title_id']] = "Y";
	}
	foreach($data_deta['res'] as $data_detares){
		$idname  = "loc_".$data_detares['loc_id']."_dep_".$data_detares['dept_id']."_des_".$data_detares['desg_id']; 
		$name  = "locatdepdesignatio[".$data_detares['loc_id']."][".$data_detares['dept_id']."][".$data_detares['desg_id']."]";
			$function = 'checkperformanace("'.$idname.'")';
			$style="style='opacity: 0.4;'";
		if($locdepdesigary[$data_detares['loc_id']][$data_detares['dept_id']][$data_detares['desg_id']] == "Y"){	
			$htmlview .='<div class="col-lg-2" onclick="editKrAandKip('.$data_detares['loc_id'].','.$data_detares['dept_id'].','.$data_detares['desg_id'].')">
							<div class="card social-card" '.$style.'>
								<div class="inputGroup text-right" >
									<label for="'.$idname.'" class="text-right">
										<p  class="text-facebook f-w-700 f-12 mb-0 text-capitalize">'.$data_detares['location_name'].'</p>
										<p class="f-10 mb-0 text-capitalize">'.$data_detares['department_name'].'</p>
										<p class="f-10 mb-0 text-capitalize">'.$data_detares['job_title'].'</p>
									</label>
								</div>
							</div>
						</div>';			
		}else{
			$htmlview .='<div class="col-lg-2">
							<div class="card social-card">
								<div class="inputGroup text-right">
									<input id="'.$idname.'" name="'.$name.'" type="checkbox" value="0" onchange="'.$function.'"  class="ceckbox_goalsset" />
									<label for="'.$idname.'" class="text-right">
										<p  class="text-facebook f-w-700 f-12 mb-0 text-capitalize">'.$data_detares['location_name'].'</p>
										<p class="f-10 mb-0 text-capitalize">'.$data_detares['department_name'].'</p>
										<p class="f-10 mb-0 text-capitalize">'.$data_detares['job_title'].'</p>
									</label>
								</div>
							</div>
						</div>'; 
		}
	}	
	echo $htmlview;
}
if($page == "updatekrakpitagging"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertgoaldata&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response,1);
}
//Rohan Added 22-11-2022
//Rohan Added 10-01-2023
if($page == "insertcompanysubscriptionplans"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertcompanysubscriptionplans&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response,1);
	print_r($response1);
}
if($page == "insertadditionalserviceslicense"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertadditionalserviceslicense&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response,1);
	print_r($response1);
}
if($page == "assigneduserlicens"){
	$url1 = API."?mode=assigneduserlicens&company_subscr_id=".$_POST['company_subscr_id']."&checktype=".$_POST['checktype']."&userid=".$_POST['userid'];  
	echo $data_deta = callAPI($url1);
	$response1 = json_decode($response,1);
	print_r($response1);
}



//end Rohan Added 10-01-2023

//mobile ajax----------------------------

if($page== "insertemployeeleave"){
	$data['hr_user_id'] = $_POST['hr_user_id'];
	$data['finance_user_id'] = $_POST['finance_user_id'];
	$data['leave_from'] = date("Y-m-d",strtotime($_POST['leave_from'])); 
	$data['start_half_day'] = $_POST['start_half_day'];
	$data['leave_to'] = date("Y-m-d",strtotime($_POST['leave_to']));
	$data['end_half_day'] = $_POST['end_half_day'];
	$data['leave_type_id'] = $_POST['leave_type_id'];
	$data['special_request'] = $_POST['special_request'];
	$data['leave_days'] = $_POST['days'];	
	$data['employee_id'] = $_SESSION['emp_id'];
	$data['company_id'] = $_SESSION['company_id'];
	$data['rid'] = $_POST['rid'];
	$data['user_id'] = $_SESSION['userid'];
	$data['applying_late'] = ((strtotime(date("d-m-Y")) - strtotime($_POST['leave_from'])) / (60 * 60 * 24));
	$data['current_status'] = '0';
	$data['is_active'] = '0';
	if($_POST['submit']=='Add'){
		$data['status_date'] = nowDate();
		$data['created_date'] = nowDate();
		$data['created_by'] = $_SESSION['userid'];	
		$url = API."?mode=insertleaverequest&table=".$table_name."&method=post";//custom leave policy url
		$response1 = file_post_contents($url, $data, 'insert');
		$res = json_decode($response1,1);
		if($res['leavestatus']['leaveaplye']=='1'){
			$msgg = str_replace("{[value]}",$res['leavestatus']['error_msg'],$_SESSION['error_message'][$res['leavestatus']['error_code']]);
		}else{
			$response = $res['leaveinsert'];
			if($response['status'] == 'fail'){
				if(isset($_SESSION['error'][$response['error_no']])){
					$dis_err = explode(",", $response['other']);
					foreach ($dis_err as $va) {
						$dis_er [] = $dis_labels[$va];
					}
					$msgg = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", $dis_er);
				}else{
					$msgg = $response['message'];
				}
			}else{
				$msgg = $_SESSION['error_message']['3000'];	
			}	
		}
		echo json_encode(array('leavestatus'=>$res['leavestatus'],'leaveinsert'=>$res['leaveinsert'],'msg'=>$msgg));
	}	
}

//mobile ajax----------------------------


if($page =="insertcandidatedetails"){
	print_r($_POST);
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	if($_POST['edittype'] == "workexperience"){
		foreach($_POST['company_name'] as $seq=>$comname){
			if(isset($_FILES['file'.$seq])){
				//echo "indie--- ";
				$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$_POST['candidate_id']."/Documents";
				$compay_file = imageuploadwitharray($path,'file'.$seq,'','',$key);	
				$_POST['hidden_file'][$seq] = $compay_file;
				//print_r($compay_file);
			}
		}
	}
	if($_POST['edittype'] == "education"){
		foreach($_POST['institute_name'] as $seq=>$comname){
			if(isset($_FILES['file'.$seq])){
				$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$_POST['candidate_id']."/Documents";
				$compay_file = imageuploadwitharray($path,'file'.$seq,'','',$key);	
				$_POST['hidden_qualification_file'][$seq] = $compay_file;
			}
		}
	}
	echo $url = API."?mode=insertcandidatedetails&method=post&extra_paramiter=".$_POST['extra_paramiter'];
	$response = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response,1);
	print_r($response1);
}	
if($page=="insertcandidatespopup"){
		$File_Uploads_Path = "uploads/all/";
		if($_FILES['file']['error'] != "4"){
			  $temp = explode(".", $_FILES['file']['name']);
			  $Filename=  "file_".time()."_".uniqid();
			  $newfilename = $Filename.'.' .end($temp);
			  if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
				$_POST['old_files'] = $newfilename;
			}
		}
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertcandidatespopup&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response1 = json_decode($response,1);
	if($response1['status'] !="fail"){
		$response1['decode_id'] =  deocdeId("id:".$response1['LASTID']);
	}
	echo json_encode($response1);
	//print_r($response1);

	//   $_POST['current_status'] = 0;

 //   $_POST['is_active'] = 0;
 //   $_POST['status_date'] = nowDate();
 //   $_POST['from_date'] = date('Y-m-d');
 //   $_POST['to_date'] = date('Y-m-d');
 //   $_POST['display_name'] = $_POST['first_name']." ".$_POST['last_name'];
 //      unset($_POST['page']);
 //      $_POST['created_date'] = nowDate();
 //      $_POST['created_by'] = $_SESSION['userid'];   
 //      print_r($_POST);
 //      // $url = API."?mode=adddata&table=candidates&method=post";
 //      // $response = file_post_contents($url, $_POST, 'insert');
 //      // $response1 = json_decode($response,1);
 // //  	if($response1['status'] !="fail"){
	// // 	$response1['decode_id'] =  deocdeId("id:".$response1['LASTID']);
	// // }else{
	// 	$url2 = API."?mode=adddata&table=job_posting_candidates&method=post";
 //      	$response2 = file_post_contents($url2, $_POST, 'insert');
 //      	$response2 = json_decode($response2,1);
 //      	print_r($response2);
	//}	
	//echo json_encode($response1);
}
if($page=="assignecandidatejobpostingpopup"){
		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['user_id'] = $_SESSION['userid'];
		$url = API."?mode=assignecandidatejobpostingpopup&method=post";
		$response = file_post_contents($url, $_POST, 'insert');
		$response1 = json_decode($response,1);
		print_r($response1);
}
if($page == "cnadidateverified"){
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['candidate_verified'] = $_POST['type'];
	$url = API."?mode=updaterowtabledata&rid=".$_POST['candidate_id']."&table=candidates&method=post";
	$return = file_post_contents($url, $data, 'update');
	print_r($return);
}
if($page =="updatetransactionactivityschedule"){
	if($_FILES['file']['error'] != "4" AND isset($_FILES['file']['error'])){
		$transaction_table = $_POST['transaction_table'] !=""?$_POST['transaction_table']:"candidates";
	   	$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_table."/".$_POST['assigned_to']."/Documents";
		$filename = imageuploadwitharray($path,'file'.$seq,'','',$key);	
		$_POST['attachment_file'] = $filename;
	}else{
		$_POST['attachment_file'] = $_POST['old_attachments'];
	} 
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	
	$url = API."?mode=updatetransactionactivityschedule&method=post";
	echo $response = file_post_contents($url, $_POST, 'insert');
}
if($page == "getcandidateemailtemplate"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'template_reference_id'=>$_POST['temp_id']);
	$wh1['field'] = "id,email_subject_to,email_content_to";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=email_content&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$json = $json[0];
	echo json_encode($json);
	//print_r($json);
}
if($page == "sendcandidatemailpopup"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=sendcandidatemailpopup&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	print_r($response);
}
if($page=="candidaterequestdocs"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=candidaterequestdocs&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	print_r($response);
}
if($page == "setarraystatus"){
	$mes = "";
	$statusarray = array();
	if($_POST['oldarray'] !=""){
		$statusarray = json_decode($_POST['oldarray'],1);
	}
	$statusarray[$_POST['selectkey']] = $_POST['selectval'];
	$num = 1;
	foreach($statusarray as $valstatuarray){
		$mes .="<label class='form-label label label-md label-primary m-1'>".$num.":".$valstatuarray."</label>";
		$num++;
	}
	echo json_encode(array('status_array'=>$statusarray,'label'=>$mes));
}
if($page=="insertadittrails"){
	//print_r($_POST);
	$extra_details = array("child_option"=>$_POST['child_option']);
	$res = insertAuditTrail($_POST['parent_option'],json_encode($_POST['extra_paramiter']),json_encode($extra_details));
	//print_r($res);
}
/*if($page=="togettimezonecountries"){
	$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'id'=>$_POST['id']);
	$vdata1["field"] = "id,time_zones";
	$vdata = urlencode(json_encode($vdata1));
	e$vurl = API."?mode=gettabledetailswithconditions&table=".$_POST['table_name']."&data=".$vdata; 
	$cudata = callAPI($vurl);
	$timezones = json_decode($cudata['0']['time_zones'],1);
	$str = "";
	foreach($timezones as $timezone){
		$str.= "<option value = '$timezone[zoneName]'>$timezone[zoneName]</option>";
	}
	echo $str;
}*/
if($page =="getlocationpettycashtransaction"){
	$url1 = API."?mode=pettycashdashboard&startdate=".$startdate."&enddate=".$enddate."&location_transaction=Y&location_id=".$_POST['loc_id']; 
	$resultapi = callAPI($url1);
	$result = json_decode($resultapi,1);
	print_r($result);	
}
if($page == "displayattributesfieldoptionlist"){
	$option ="";
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['transactiontablename']);
	$wh1['field'] = "id,list_attributes";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$fieldlist =  json_decode($json[0]['list_attributes'],1);
	$option = '<option value="">Select Any One</option>';
	foreach($fieldlist as $feldname=>$listfield){
		$option .="<option value='".$feldname."'>".$listfield['lable']."</option>"; 
	}
	echo $option;	
}
if($page == "submitstatusdynamicworkflow"){

	// $url1 = API."?mode=submitstatusdynamicworkflow&transaction_table=".$_POST['table_name']."&current_status=".$_POST['current_status']."&transaction_no=".$_POST['transaction_id']."&next_status=".$_POST['next_status']; 
	// echo $resultapi = callAPI($url1);


	$_POST['transaction_table'] = $_POST['table_name'];
	$_POST['current_status'] = $_POST['current_status'];
	$_POST['transaction_no'] = $_POST['transaction_id'];
	$_POST['next_status'] = $_POST['next_status'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=submitstatusdynamicworkflow&method=post";
	// echo "<pre>".$url ; print_r($_POST); exit;
	
	$response = file_post_contents($url, $_POST, 'update');
	echo $response;



}
if($page=="submitsprojectinsert"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	//print_r($_POST);
	$url = API."?mode=submitsprojectinsert&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$response = json_decode($response,1);
	echo $response['LASTID'];
	//print_r($response); //exit;
}
if($page=="updatestatusdynamicprojecttask"){
	print_r($_POST);
	$data['company_id'] = $_SESSION['company_id'];
	$data['user_id'] = $_SESSION['userid'];
	if(isset($_POST['dropid'])){
		$dropid = explode("_",$_POST['dropid']);
		$itemid = explode("_",$_POST['itemid']);
		$data['field'] = 'current_status';
		$data['fielddata'] = $dropid['1'];
		$data['update_id'] = $itemid['1'];
		$data['table_name'] = 'project_task_details';
	}else{
		$data['field'] = $_POST['field'];
		$data['fielddata'] = $_POST['fielddata'];
		$data['update_id'] = $_POST['update_id'];
		$data['table_name'] = $_POST['table_name'];
	}
	//print_r($_POST);
	print_r($data);
	$url = API."?mode=updatestatusdynamicprojecttask&method=post";
	$response = file_post_contents($url, $data, 'Update');
	echo $response;	
}
if($page=="insertprojecttaskdetails"){
	$table_name = "project_task_details";
	if($_POST['table_name']!=""){
		$table_name = $_POST['table_name'];
	}
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	unset($_POST['page']);
	unset($_POST['table_name']);
	$url= API."?mode=toaddprojecttaskdetails&table=".$table_name."&method=post";
	$return = file_post_contents($url, $_POST);
	
	if($table_name == "project_task_details"){
		$res = json_decode($return,1);
		$str = "";
		$str.='<div class="todo p-2" id="tsakid_'.$res['LASTID'].'" draggable="true" onclick="taskdetailspopup('.$res['LASTID'].')"><i class="ti-pencil float-end f-12"></i><p class="mb-2 mt-2">'.$_POST['task_name'].'</p> <div class="align-items-center"><i class="ti-eye m-r-5 text-muted" data-bs-toggle="tooltip" data-placement="bottom" title="You are watching this card"></i><span class="float-end user-name">'.initials($_SESSION['login_details']['allusers']['name'][$_POST['assigen_id']]).'</span></div></div>';
		echo $str;
	}else{
		echo $str;
	}
}
if($page == 'updatedynamicfiedrojecttaskdetail'){
	print_r($_POST);
	$url = API."?mode=updatedynamicfiedrojecttaskdetail&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo $response;	
}

if($page == 'dynamiccheckqtyvalidation'){
	$url1 = API."?mode=dynamiccheckqtyvalidation&ref_table=".$_POST['ref_table']."&ref_id=".$_POST['ref_id']."&quantity=".$_POST['quantity']."&item_id=".$_POST['item_id']."&table_name=".$_POST['table_name']."&ref_transaction_item_table_id=".$_POST['ref_transaction_item_table_id'];
	$respons = callAPI($url1);
	echo $respons;
	//$res = json_decode($respons,1);
	//print_r($res);
}
if($page == 'updatemoretabledata'){
	$url = API."?mode=updatemoretabledata&table_name=".$_POST['table_name']."&hid=".$_POST['update_id']."&method=post";
	unset($_POST['page']);
	unset($_POST['table_name']);
	unset($_POST['update_id']);
	$response = file_post_contents($url, $_POST, 'Update');
}
if($page == 'checkandupdatecolorprojecttask'){
	//print_r($_POST);
	$url = API."?mode=checkandupdatecolorprojecttask&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	//echo $response;
	if(strpos($response,'add')){ $res = 1; }else{ $res = 2;}
	//echo json_decode($res,1);
	echo $res;
}
if($page == 'insertstatusprocesss'){
	print_r($_POST);
	$url = API."?mode=insertstatusprocesss&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo json_decode($response,1);
	print_r($response);
	echo $response;
}
if($page == 'addupdatelabeldata'){
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=addupdateprojectlabeldata&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo json_decode($response,1);
}
if($page == 'updatelabelval'){
	$_POST['updated_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	print_r($_POST);
	$url = API."?mode=updatelabelval&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo json_decode($response,1);
}
if($page == 'copynewcard'){
	print_r($_POST);
	$url = API."?mode=copynewcard&method=post";
	$response = file_post_contents($url, $_POST, 'Insert');
	echo json_decode($response,1);
}
if($page == 'fileattachupload'){
	print_r($_POST); print_r($_FILES);
	/*$targetDir = 'uploads/attachments/'; // Folder where the uploaded files will be stored
	$targetFile = $targetDir . basename($_FILES['file']['name']);
	// Move the uploaded file to the target directory
	if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
		echo "File uploaded successfully.";
	}else {
		echo "Error uploading file.";
	}*/
}
if($page == 'customunassignprjecttaskdetails'){
	$curl = API."?mode=togetunassigntaskslist&project_id=".$_POST['project_id']; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = '';
	if(count($cudata)>0){
		foreach ($cudata as  $data) {
			$str.= "<option value = '".$data['id']."'>".$data['task_name']."</option>";
		}
	}
	echo $str;
}
if($page == 'tocheckprojectteamuncompletetasks'){
	$curl = API."?mode=tocheckprojectteamuncompletetasks&rowid=".$_POST['rowid']; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1); 
	$msg = "Do you want to remove member from this project? There are ".$cudata['cc']." tasked assigned to this user";
	echo $msg;
}
if($page == 'toaddupdatetheprojectteamdata'){
	print_r($_POST); 
	$table_name = $_POST['table_name'];
	$id = $_POST['id']; 
	unset($_POST['table_name']);
	unset($_POST['page']);
	unset($_POST['id']);
	if($id==''){
		$_POST['created_date'] = nowDate();
		$_POST['is_active'] = 0;
		$_POST['created_by'] = $_SESSION['userid'];
		$_POST['company_id'] = $_SESSION['company_id'];
		//print_r($_POST);
		$url = API."?mode=toaddupdatetheprojectteamdata&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST);
	} else {
		$_POST['updated_date'] = nowDate();
		$_POST['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=toaddupdatetheprojectteamdata&rid=".$id."&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST, 'update');
	}
	/*if($table_name=='project_teams' && $_POST['role_id']=="1" && $id==""){
		$url = API."?mode=updaterowtabledata&rid=".$_POST['project_id']."&table=projects&method=post";
		$updata = array('assigner_id'=>$_POST['user_id']);
		$return = file_post_contents($url, $updata, 'update');
	}*/
	echo $return;
}
if($page == 'toinsertupdateprojecttasktimlog'){
	$_SESSION['project_task_log'] =	1;
	$_SESSION['project_task_start'] = $post['starttime'];
	$_SESSION['project_task_end'] =	$post['endtime'];
	$curl = API."?mode=toinsertupdateprojecttasktimlog&rowid=".$_POST['rowid']; 
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	   //print_r($_POST);
	$url = API."?mode=toinsertupdateprojecttasktimlog&table=".$table_name."&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page == "getdynamicreftype"){
	$option ="";
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'current_status'=>ST_APPROVED);
	$wh1['field'] = "id,transaction_number";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=".$_POST['key']."&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $listfield){
		$option .="<option value='".$listfield['id']."'>".$listfield['transaction_number']."</option>"; 
	}
	echo $option;	
}

if($page == 'toinsertthedataonitemquality'){
	print_r($_POST);
	$url = API."?mode=toinsertthedataonitemquality&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo json_decode($response,1);
}
if($page == 'currencyconversionrate'){
	//print_r($_POST);
	$url1 = API."?mode=currencyconversionrate&trandate=".$_POST['trandate']."&originator_currency_id=".$_POST['originator_currency_id']."&currency_id=".$_POST['currency_id']; 
	$respons = callAPI($url1);
	$result = json_decode($respons,1);
	//print_r($result);
	$res = $result['currency_convertor'];
	if(isset($res['id'])){
		$resultary =array("rate"=>$res['ex_rate'],"operater"=>$result['operater']);
	}else{
		$resultary =array("rate"=>0,'operater'=>'*');
	}
	if($resultary['rate'] == 0 AND $_POST['originator_currency_id'] == $_POST['currency_id']){
		$resultary =array("rate"=>1,'operater'=>'*');
	}
	echo json_encode($resultary);
}
if($page == "userandcurrencydetails"){
	$url1 = API."?mode=userandcurrencydetails&selectuserid=".$_POST['user_id']."&currency_id=".$_POST['currency_id']."&trandate=".$_POST['trandate']; 
	$respons = callAPI($url1);
	echo $respons;
	//$result = json_decode($respons,1);
	//print_r($result);
}
if($page == "checkhsncodegst"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['values']);
	$wh1['field'] = "id,gst";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=hsn_and_sac_codes&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	if($json[0]['gst'] !=""){
		$gst = $json[0]['gst'];
	}else{
		$gst = 0;
	}
	echo json_encode(array('gst'=>$gst));
}
if($page == "gettransactionwarehouse"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'location_id'=>$_POST['id']);
	$wh1['field'] = "id,warehouse_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=warehouses&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['warehouse_name']."</option>"; 
	}

	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['id']);
	$wh1['field'] = "id,location_state";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=locations&data=".$data1; 
	$data_details1 = callAPI($url1);
	$loc = json_decode($data_details1,1);

	echo json_encode(array("option"=>$option,"locations"=>$loc[0]));
}
if($page ==  "assigntaskschedule"){
	$employee_id = $_POST['emp_id'];
	$url = API."?mode=getemployeeonboardingtask&employee_id=".$employee_id."&taskassign=Y&submit=Y"; 
	$api = callAPI($url);
	$result = json_decode($api,1);
	print_r($result);
}
if($page == "updateonboardingtaskstatus"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updateonboardingtaskstatus&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	//$result = json_decode($response,1);
	//print_r($result);
	echo $response;
}
if($page =="insertcrmleads"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//echo "<pre>"; print_r($_POST);
	$File_Uploads_Path = "uploads/all/";
	$_POST['image'] = $_POST['hid_image'];
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['image'] = $newfilename;
		}
	}
	//print_r($_POST);
	$url = API."?mode=insertcrmleads&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	//$result = json_decode($response,1);
	//print_r($result);
	echo $response;
}
if($page=="insertcrmleaddetails"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//$_POST['crm_lead_id'] = $_POST['crm_lead_id'];
	//print_r($_POST);
	$url = API."?mode=insertcrmleaddetails&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	//$result = json_decode($response,1);
	//print_r($result);
	echo $response;
}
if($page=="dynamicupdatestatus"){
	//print_r($_POST);
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=dynamicupdatestatus&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	$result = json_decode($response,1);
	//print_r($result);
	echo $response;
}
if($page=="insertassetverification"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertassetverification&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	$result = json_decode($response,1);
	print_r($result);
	//echo $response;
}
if($page=="getreimbursementdashboardchart"){
	$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
	$url = API."?mode=getreimbursementdashboardchart&reimbursementsyear=".$_POST['reimbursementsyear']."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&roleshortcode=".$roleshortcode."&paymentsdashboard=".$_POST['paymentsdashboard']; 
	echo $api = callAPI($url);
}
if($page=="getbillsstackdashboardchart"){
	$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
	$url = API."?mode=getbillsstackdashboardchart&reimbursementsyear=".$_POST['reimbursementsyear']."&transaction_table=".$_POST['transaction_table']."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&roleshortcode=".$roleshortcode; 
	echo $api = callAPI($url);
}



if($page =="updateassetverificationstatus"){
	$File_Uploads_Path = "uploads/assets/";
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['asset_img'] = $newfilename;
		}
	}else{
		$_POST['asset_img'] = $_POST['oldattachment'];
	} 
	$custom_field= array();
	foreach($_POST['fieldname'] as $key=>$value){
		$custom_field[$key]['fieldname'] = $value;
		$custom_field[$key]['fieldtype'] = $_POST['fieldtype'][$key];
		$custom_field[$key]['fieldvalue'] = $_POST['fieldvalue'][$key];
	}
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=updateassetverificationstatus&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page=="getesopplansbucketsoption"){
	$return = array();
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['esop_plan_id']);
	$wh1['field'] = "id,date_of_grant";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=esop_plans&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json1 = json_decode($data_details1,1);
	$res = $json1[0];

	$origin = new DateTime(dateconvertdbformat($_POST['date_of_joining']));
	$target = new DateTime($res['date_of_grant']);
	$interval = $origin->diff($target);
	$return['tenure'] = $interval->format('%y.%m');

	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'esop_plans_id'=>$_POST['esop_plan_id']);
	$wh1['field'] = "id,bucket_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=esop_plan_buckets&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $result){
		$option .="<option value='".$result['id']."'>".$result['bucket_name']."</option>"; 
	}
	$return['option'] = $option;
	echo json_encode($return);
}
if($page=="getesopplansbuckets"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['esop_bucket_id']);
	//$wh1['field'] = "id,bucket_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=esop_plan_buckets&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$res = $json[0];
	$vesting_detail = json_decode($res['vesting_schedule'],1);


	$html .= '<div id="addmorediv-1"> 
			<div class="row">
				<div class="form-group col-lg-4">
					<label>Allocation Date <span class="text-danger">*</span></label>
				</div>
				<div class="form-group col-lg-3">
					<label>Percentage <span class="text-danger">*</span></label>
				</div>
				<div class="form-group col-lg-4">
					<label>Till Date <span class="text-danger">*</span></label>
				</div>
			</div>';
				$totaldis = 0;
			  $mk = 1; foreach($vesting_detail as $vd){
				  $totaldis +=$vd['vesting_percentage'];

	$html .= '<div class="row" id="deleteidvesting_'.$mk.'">
					<div class="form-group col-lg-4">
					   <input type="text" class="form-control datepicker " name="vesting_date[]" value="'.fromDate($vd['vesting_date']).'" required>
					</div>
					<div class="form-group col-lg-3">
						<input type="number" class="form-control text-right vestingpercentage_1" name="vesting_percentage[]" value="'.$vd['vesting_percentage'].'" onblur="calculateVestingPercentage('.$mk.')" required>
					</div>
					<div class="form-group col-lg-4">
					   <input type="text" class="form-control datepicker" name="till_date[]" value="'.fromDate($vd['till_date']).'" required>
					</div>
					<div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRowVestage('.$mk.')"></i> </div>
				</div>';
		 $mk++; }
		$html .= '</div>';
		$pending = (100-$totaldis);
		$html .= '<div class="card-footer pt-0">
			<button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMore(1)"><i class="fa fa-check m-r-10"></i>Add More</button>
			<span class="float-end ">
				<span class="text-danger">Un-Schedule (%) : </span>
				<span id="totaldistributionpending_1">'.$pending.'</span>
				<span class="text-warning">Total (%) : </span>
				<span id="totaldistributionqty_1">'.$totaldis.'</span>
			</span>
		</div>';

   $return = array('vesting_schedule'=>$html,'bucket_details'=>$res);
	$json = json_encode($return);
	echo $json;
 }
if($page=="getcompanycontributionusers"){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$_POST['id'],'user_type'=>$_POST['user_type']);
	$wh1['field'] = "id,display_name,emp_code,date_of_joining,designation,annual_ctc,user_email,user_id,esop_connect";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=company_contribution_users&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$res = $json[0];
	$json = json_encode($res);
	echo $json;
 }
 if($page == "updatedynamiccurrentstataus"){
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['current_status'] = $_POST['status'];
	echo $url = API."?mode=updaterowtabledata&fid=id&rid=".$_POST['rid']."&table=".$_POST['table']."&method=post";
	$return = file_post_contents($url, $data, 'update');
	echo $return;
 }
 if($page == "updateprojectdependencydetail"){
	print_r($_POST);
	$url = API."?mode=updateprojectdependencydetail&method=post";
	$return = file_post_contents($url, $_POST, 'insert');
	echo $return;
 }
 if($page == "addupdateprojectphases"){
	//print_r($_POST);
	$url = API."?mode=addupdateprojectphases&method=post";
	$return = file_post_contents($url, $_POST, 'insert');
	$data = json_decode($return,1);
	echo $data['LASTID'];
 }
 if($page == "addupdaterolememberdetail"){
	print_r($_POST);
	$url = API."?mode=addupdaterolememberdetail&method=post";
	$return = file_post_contents($url, $_POST, 'insert');
	echo $return;
 }
 if($page == "insertesopnewrecord"){
	$sarray =array('company_id'=>$_SESSION['company_id'],'first_name'=>'ESOP','last_name'=>'ESOP','display_name'=>'ESOP','user_type'=>'E','is_active'=>'0','current_status'=>'current_status','created_by'=>$_SESSION['userid'],'created_date'=>date("Y-m-d H:i:s"));
	$url = API."?mode=adddata&table=company_contribution_users&method=post";
	$return = file_post_contents($url, $sarray);
	echo $return;
 }
 //////////////////////account///////////////////////////////////////////
 if($page == "insertfinanacereportgroups"){
  //print_r($_POST);
	$url = API."?mode=insertfinanacereportgroups&method=post";
	echo $return = file_post_contents($url, $_POST, 'insert');
	//print_r($return);
}
if($page == "selectchartofaccont"){
  //print_r($_POST);
	$list = "";
	$url = API."?mode=selectchartofaccont&chart_of_account_codes=".$_POST['chart_of_account_codes']."&Rid=".$_POST['Rid'];
	$return =  callAPI($url);
	$cudata = json_decode($return,1);
	//print_r($cudata);
	$chilaccount = $cudata['chart_of_accounts_child'];
	$alreadyused = $cudata['chart_of_accounts_already_used'];
	$groupaccountlist = $cudata['finance_report_chart_of_account'];
	$list = "";
	$checkall = "";
	 //print_r($groupaccountlist);
	//if(count($groupaccountlist) =="0"){
	  $checkall = "checked";
   // }
	
	foreach($cudata['chart_of_accounts_parent'] as $cudatas){
		$alreadwork = "";
		if(isset($alreadyused[$cudatas['account_code']])){
		  $alreadwork = '<i class="ti-alert float-end  text-danger"></i>';
		} 
		$singlecheck = (isset($groupaccountlist[$cudatas['account_code']]))?"checked":"";
		$list .='<div class="col-xl-12 mt-2"><h4 class="sub-title"><div class="checkbox-fade fade-in-primary"><label class="form-label" style="pointer-events: none;"><input type="checkbox" name="chart_of_account_code[]" value="'.$cudatas['account_code'].'" '.$checkall.' '.$singlecheck.' readonly><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>'.$cudatas['account_name'].'</label></div>'.$alreadwork.'</h4><ul>'; 
		foreach($chilaccount[$cudatas['account_code']] as $chilaccounts){
		  $alreadwork = "";
		  if(isset($alreadyused[$chilaccounts['account_code']])){
			$alreadwork = '<i class="ti-alert float-end  text-danger"></i>';
		  }
		  $singlecheck = (isset($groupaccountlist[$chilaccounts['account_code']]))?"checked":"";
		  $list .='<li><div class="checkbox-fade fade-in-primary m-l-20"><label class="form-label"><input type="checkbox" value="'.$chilaccounts['account_code'].'" name="chart_of_account_code[]" '.$checkall.' '.$singlecheck.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>'.$chilaccounts['account_name'].'</label></div>'.$alreadwork.'</li>';
		}
		$list .= '</ul></div>';
	}
	echo  $list;
}
if($page == "insertchartofaccounts"){
  //print_r($_POST);
	$url = API."?mode=insertchartofaccounts&method=post";
	echo $return = file_post_contents($url, $_POST, 'insert');
	//print_r($return);
}
if($page == "inserttocheaders"){
  //print_r($_POST);
	$_POST['header_contain'] = json_encode(urlencode($_POST['header_contain']));
	$url = API."?mode=inserttocheaders&method=post";
	echo $return = file_post_contents($url, $_POST, 'insert');
}
if($page =="insertnotes"){
	$url = API."?mode=insertnotes&method=post";
	echo $return = file_post_contents($url, $_POST, 'insert');
}
if($page =="geteditnotes"){
	$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','id'=>$_POST['id']);
  $data1["field"] = 'id,parent_id,note_number,note_name,description';
  $data = urlencode(json_encode($data1));
  $curl = API."?mode=gettabledetailswithconditions&table=notes&data=".$data; 
  $cudata = callAPI($curl);
  $cudata = json_decode($cudata,1);
  $cudata = $cudata[0];
  echo json_encode($cudata);
}
if($page == "getdepreciationtable"){
	//print_r($_POST);
	$returnary = array();
	$depreciation_method = $_POST['depreciation_method'];
	$duration = $_POST['duration'];
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
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$capitalization_date.'">'.$capitalization_date.'</td>
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
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.$depreciationdate.'</td>
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
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.$depreciationdate.'</td>
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
				<td class="p-2"><input type="hidden" name="depreciation_date[]" value="'.$depreciationdate.'">'.$depreciationdate.'</td>
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
if($page == "storepdfsendmail"){
	//print_r($_POST);
	$data = $_POST['data'];
	$b64 = $data;
	# Decode the Base64 string, making sure that it contains only valid characters
	$bin = base64_decode($b64, true);
	# Perform a basic validation to make sure that the result is a valid PDF file
	# Be aware! The magic number (file signature) is not 100% reliable solution to validate PDF files
	# Moreover, if you get Base64 from an untrusted source, you must sanitize the PDF contents
	if (strpos($bin, '%PDF') !== 0) {
	  throw new Exception('Missing the PDF file signature');
	}
	# Write the PDF contents to a local file
	file_put_contents('uploads/rental_invoices/'.time().'_file.pdf', $bin);
}
if($page == "getpropertytypes"){
	$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','parent_id'=>$_POST['property_type_id']);
	$data1["field"] = 'id,property_type_name';
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=property_types&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$html = '';
	$html .= '<option value="">Select Any One</option>';
	foreach($cudata as $ppt){ 
			$html .='<option value="'.$ppt['id'].'">'.$ppt['property_type_name'].'</option>'; 
	 } 
	echo $html;
}
if($page == "getajaxpropertyownerdetails"){
	$url1 = API."?mode=getajaxpropertyownerdetails&ownerid=".$_POST['ownerid'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$property_owner_details = $result['property_owner_details'];
	// $property_owner_detail_areas = $result['property_owner_detail_areas'];
	// foreach($property_owner_detail_areas as $poda){
	// 	$property_owner_detail_areas_ary[$poda['property_owner_detail_id']][] = $poda;
	// }
	$html = '';
	$html .= '<option value="">Select Any One</option>';
	foreach($property_owner_details as $pod){
		$html .='<option value="'.$pod['id'].'">'.$pod['property_name'].'</option>'; 
	}
	/**foreach($property_owner_details as $pod){
		$html .= '<div class="col-lg-6">
				<label class="form-label d-block f-w-600">'.$pod['property_name'].'</label>';
		foreach($property_owner_detail_areas_ary[$pod['id']] as $poda){					
			$html .= '<div class="checkbox-fade fade-in-primary">
						<label class="form-label">
							<input type="checkbox" class="podalist" onchange="getPropertyOwnerDetailAreas()" value="'.$poda['id'].'">
							<span class="cr">
							  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
							</span>
							<span style="display: table;">'.$poda['property_area_name'].'</span>
						</label>
					</div>';
		}
		$html .= '</div>';
	}**/
	echo $html;

}
if($page == 'getajaxpropertyownerdetailareas'){
	$url1 = API."?mode=getajaxpropertyownerdetails&rid=".$_POST['rid']."&ownerdetailid=".$_POST['ownerdetailid'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$property_owner_detail_areas = $result['property_owner_detail_areas'];
	$html = '';
	foreach($property_owner_detail_areas as $poda){					
		$html .= '<div class="col-lg-4"><div class="checkbox-fade fade-in-primary">
					<label class="form-label">
						<input type="checkbox" class="podalist" onchange="getPropertyOwnerDetailAreas()" name="property_owner_area_ids[]" value="'.$poda['id'].'">
						<span class="cr">
						  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
						</span>
						<span style="display: table;">'.$poda['property_area_name'].'</span>
					</label>
				</div></div>';
	}
	echo $html;
}
if($page == 'getajaxpropertyownerdetailareasselection'){
	$url1 = API."?mode=getajaxpropertyownerdetails&rid=".$_POST['rid']."&ownerdetailareaid=".$_POST['ownerdetailareaid'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$property_owner_detail_areas = $result['property_owner_detail_areas'];
	//print_r($property_owner_detail_areas);
	$property_tenant_details= $result['property_tenant_details'];
	$propertyoda = json_decode($property_tenant_details['property_owner_detail_areas'],1);
	$areaidrent = array();
	//print_r($result);
	foreach($propertyoda as $arearent){
		$areaidrent[$arearent['property_area_id']] = $arearent['rent'];
		$areaidpersqrent[$arearent['property_area_id']] = $arearent['per_sq_ft_discount'];
	} 	//print_r($areaidrent);
	//$html .= '<tabel><thead><tr><td>Area Name</td><td>Area Sq. Ft</td></tr></thead><tbody>';
	$html .='<table class="table table-hover " width="100%"><thead><tr>
									<td>Area Name</td>
									<td class="text-right">Area Sq. Ft</td>
									<td class="">UOM</td>
									<td class="text-right">Per Sq. Ft Rate</td>
									<td class="text-right">Discount</td>
									<td class="text-right">Rent (Monthly)</td>
								</tr></thead><tbody>';
	foreach($property_owner_detail_areas as $poda){
	$per_sq_ft_discount = 0;
	$rent =  $poda['property_area']*$poda['per_sq_ft_rate']; 	
		if(isset($areaidpersqrent[$poda['id']])){
			$per_sq_ft_discount = $areaidpersqrent[$poda['id']];
			$rent = $areaidrent[$poda['id']];
		}
		$html .= '<tr><input type="hidden" name="property_area_id[]" value="'.$poda['id'].'"><input type="hidden" name="property_area_name[]" value="'.$poda['property_area_name'].'"><td class="p-2">'.$poda['property_area_name'].'</td>
		<td class="text-right p-2"><input type="hidden" class="form-control text-right" name="property_area[]" id="property_area_'.$poda['id'].'" value="'.$poda['property_area'].'" >'.$poda['property_area'].'</td>
		<td class="p-2"><input type="hidden" class="form-control text-right" name="uom[]" value="'.$poda['uom'].'" id="uom_'.$poda['id'].'">'.$poda['uom'].'</td>
		<td class="text-right p-2"><input type="hidden" class="form-control text-right" name="per_sq_ft_rate[]" value="'.$poda['per_sq_ft_rate'].'" id="per_sq_ft_rate_'.$poda['id'].'">'.customeummarmonyformate($poda['per_sq_ft_rate']).'</td>
		<td class="p-2"><input type="number" class="form-control text-right" name="per_sq_ft_discount[]" id="per_sq_ft_discount_'.$poda['id'].'" value="'.$per_sq_ft_discount.'" onblur="calculateRent('.$poda['id'].')"></td>
		<td class="p-2"><input type="text" class="form-control text-right calcrent" name="rent[]" value="'.$rent.'" id="rent_'.$poda['id'].'" readonly></td>
		</tr>';
	}
	$html .= '</tbody><tabel>';
	echo $html;
}
if($page=='adddaystodate'){
	$effective_date = $_POST['date'];
	$numberofdays = $_POST['numberofdays'];
	$return = date('Y-m-d', strtotime($effective_date.' + '.$numberofdays.' days'));
	echo fromDate($return);
}
if($page=='insertrentalinvoices'){
	$url1 = API."?mode=insertrentalinvoices&rid=".$_POST['rid'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	print_r($result);
}
if($page== "getcustomerdetails"){
	$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','id'=>$_POST['custid']);
	$data1["field"] = 'id,email,phone,display_name,billing_address,billing_post_code,company';
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=customers&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$res = $cudata[0];
	//print_r($res);
	echo json_encode($res);
}
if($page=="getrentaldashboardchart"){
	$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
	$url = API."?mode=getrentaldashboardchart&reimbursementsyear=".$_POST['reimbursementsyear']."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&roleshortcode=".$roleshortcode; 
	echo $api = callAPI($url);
}
if($page=="updatepropertytenantbillings"){
	$_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
   	$url = API."?mode=updatepropertytenantbillings&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    //print_r($res);
    if(isset($res['rental_invoices'])){
    	$rental_invoice_id = $res['rental_invoices']['rental_invoice_id'];
    	$res['rental_invoice_id'] = $rental_invoice_id;
    	$res['decode_rental_invoice_id'] = deocdeId("id:".$rental_invoice_id);
    }
   	echo  json_encode($res);
}
if($page=="renatalinvoicesend"){
	$url = API."?mode=renatalinvoicesend&property_tenant_billings_id=".$_POST['property_tenant_billings_id']."&bill_type=".$_POST['bill_type']; 
	echo $api = callAPI($url);
}
if($page=="addstationerycartqty"){
	$url = API."?mode=addstationerycartqty&qty=".$_POST['qty']."&item_id=".$_POST['item_id']; 
	$api = callAPI($url);
	$json = json_decode($api,1);
	echo $json['count_cart'];
}
if($page=="tocreateuserstationeryrequest"){
	// print_r($_POST);
	if($_POST['request_date'] =='undefined'){
		$_POST['request_date'] = $_POST['require_date'];
	}
	$_POST['request_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['request_date']))); 
	$_POST['require_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['require_date']))); 

	// print_r($_POST); exit;
	$url = API."?mode=tocreateuserstationeryrequest&deliver_type=".$_POST['deliver_type']."&qty=".$_POST['total_qty']."&request_date=".$_POST['request_date']."&require_date=".$_POST['require_date']."&building_id=".$_POST['building_id']."&building_floor_id=".$_POST['building_floor_id']."&building_floor_space_id=".$_POST['building_floor_space_id']."&location_id=".$_POST['location_id']."&st_emp_id=".$_POST['emp_id']."&st_current_status=".$_POST['st_current_status']."&other_name=".$_POST['other_name']; 
	$api = callAPI($url);
	echo json_decode($api,1);
}
if($page=="addcouriers"){
	 if(isset($_POST['courier_date'])){ 
		$_POST['courier_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['courier_date']))); 
	 }
	 print_r($_POST);
	$table_name = $_POST['table_name'];
	$id = $_POST['id']; 
	unset($_POST['table_name']);
	unset($_POST['page']);
	unset($_POST['id']);
	
	if($id==''){
		$_POST['created_date'] = nowDate();
		$_POST['is_active'] = 0;
		$_POST['created_by'] = $_SESSION['userid'];
		$_POST['company_id'] = $_SESSION['company_id'];
		//print_r($_POST);
		$url = API."?mode=addcouriers&table=".$table_name."&method=post";
		echo $url;
		$return = file_post_contents($url, $_POST);
	} else {
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=updatecouriers&rid=".$id."&table=".$table_name."&method=post";
		echo $url;
		$return = file_post_contents($url, $_POST, 'update');
	}
	echo $return;
}
if($page=="toinserttranspordatarequest"){
	print_r($_POST);
	
	if(isset($_POST['transport_date'])){ 
		$_POST['transport_date'] = $_POST['transport_date']."-01"; 
	 }
	$table_name = $_POST['table_name'];
	$id = $_POST['id']; 
	unset($_POST['table_name']);
	unset($_POST['page']);
	unset($_POST['id']);
	
	if($id==''){
		$_POST['created_date'] = nowDate();
		$_POST['is_active'] = 0;
		$_POST['created_by'] = $_SESSION['userid'];
		$_POST['company_id'] = $_SESSION['company_id'];
		//print_r($_POST);
		$url = API."?mode=toinserttranspordatarequest&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST);
	} else {
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$url = API."?mode=updatecouriers&rid=".$id."&table=".$table_name."&method=post";
		$return = file_post_contents($url, $_POST, 'update');
	}
	echo $return;
}

if($page=="gettenantproperty"){
	

	$url1 = API."?mode=gettenantproperty&type=".$_POST['type']."&valu=".$_POST['valu']."&bill_type=".$_POST['bill_type'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$html = '';
	$property_tenant_details = $result['result'];
	//echo "<pre>"; print_r($result);
	if($_POST['type'] == 'getcustid'){
		$html .= '<option value="">Select Any One</option>';
		foreach($property_tenant_details as $pod){
			$html .='<option value="'.$pod['id'].'">'.$pod['value']."-".ucwords($pod['company_name'])." (".date("d-M-Y",strtotime($pod['rent_commencement_date'])).')</option>'; 
		}
	}elseif($_POST['type'] == 'getproperty_tenant_detail_id'){
		$html .= '<option value="">Select Any One</option>';
		foreach($property_tenant_details as $pod){
			$html .='<option value="'.$pod['id'].'">'.date("d-M-Y",strtotime($pod['month_start_date'])).'</option>'; 
			//$html .='<option value="'.$pod['id'].'">'.date("d-M-Y",strtotime($pod['month_start_date'])).' - '.customeummarmonyformate(($pod['total_amount']!='' || $pod['total_amount'] !=0)?$pod['total_amount']:0).'</option>'; 
		}
	}elseif($_POST['type'] == 'getproperty_tenant_billings_id'){
		$property_tenant_billing_id = $_POST['valu'];
		if($_POST['bill_type'] =="U"){
			$bill_detail = json_decode($property_tenant_details['utility_details'],1);
			$transaction_date = $property_tenant_details['utility_transaction_date'];
			$due_date = $property_tenant_details['utility_due_date'];
			$additional_notes = $property_tenant_details['utility_additional_notes'];
		}else{
			$bill_detail = json_decode($property_tenant_details['bill_detail'],1);
			$transaction_date = $property_tenant_details['rent_transaction_date'];
			$due_date = $property_tenant_details['rent_due_date'];
			$additional_notes = $property_tenant_details['rent_additional_notes'];
		}
		
		$html .='<input type="hidden" name="property_tenant_billing_idd" id="property_tenant_billing_idd" value="'.$property_tenant_billing_id.'"><form method="post" id="form_id_new_'.$property_tenant_billing_id.'">';
		$html .='<div class="row">';
		$html .='<div class="col-lg-4"><label class="form-label d-block">Transaction Date</label><input type="text" class="datepicker form-control" name="transaction_date" value="'.fromDate($transaction_date).'"></div>';
		$html .='<div class="col-lg-4"><label class="form-label  d-block">Due Date</label><input type="text" class="datepicker form-control" name="due_date" value="'.fromDate($due_date).'"></div>';
		$html .='<div class="col-lg-4"><label class="form-label  d-block">Additional Notes</label><textarea class="form-control" placeholder="Additional Notes" name="additional_notes">'.$additional_notes.'</textarea></div>';
		$html .='</div>';

		$html .='<table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
					<thead>
						<tr>
							<th width="5%">#</th>
							<th width="25%">Utility</th>
							<th width="30%" class="text-right" colspan="4">Amount</th>
							<th width="30%">Remark</th>
						</tr>
					</thead>
					<tbody>';
						 $i=1;foreach($bill_detail as $bd){ 
						 	$redonlytext = $extrlebl = '';
							if($bd['transaction_table']=='property_owner_detail_areas' OR $bd['utility_bill_auto_and_fix']=='F'){
								$redonlytext = 'readonly';
								$extrlebl = "<span>Rent (".$bd['monthly_rent'].")+ Tax (".$bd['rent_tax'].")</span>";
							}
							if($bd['utility_bill_auto_and_fix']=='M'){
								$redonlytext = 'readonly';
							}

							$html2 .='<tr>';
							$html2 .='<td  class="p-1">'.$i.'</td>
								<td class="p-1"><span class="d-block">'.$bd['name'].'</span>'.$extrlebl.'</td>';
							if($bd['parent_id'] != 0){
								if($bd['condition'] =="="){  // only enter value this used
									$html2 .='<td class="p-1" colspan="3">';
									$html2 .='<input class="form-control text-right" type="number" name="rate[]" value="'.$bd['rate'].'" id="rentinvpopup_rate_'.$bd['poda_and_rbt_id'].'" onblur="rentalInvoicePopupCalculattion('.$bd['poda_and_rbt_id'].','.$bd['parent_id'].')">';
									
								//$html2 .='<input class="form-control text-right" type="number" name="qty[]" value="'.$bd['qty'].'" id="rentinvpopup_qty_'.$bd['poda_and_rbt_id'].'" onblur="rentalInvoicePopupCalculattion('.$bd['poda_and_rbt_id'].','.$bd['parent_id'].')">';
									$html2 .='<input type="hidden" id="rentinvpopup_condition_'.$bd['poda_and_rbt_id'].'" value="'.$bd['condition'].'">';
									$html2 .='<input type="hidden" name="qty[]" value="1" id="rentinvpopup_rate_'.$bd['poda_and_rbt_id'].'">';
									$html2 .='</td>';
								}else{
									$html2 .='<td class="p-1" ><input class="form-control text-right" type="number" name="qty[]" value="'.$bd['qty'].'" id="rentinvpopup_qty_'.$bd['poda_and_rbt_id'].'" onblur="rentalInvoicePopupCalculattion('.$bd['poda_and_rbt_id'].','.$bd['parent_id'].')"></td>';
									$html2 .='<td class="p-1" ><input type="hidden" id="rentinvpopup_condition_'.$bd['poda_and_rbt_id'].'" value="'.$bd['condition'].'"><label class="form-label badge badge-md bg-success"><span class="f-16">'.$bd['condition'].'</span></label></td>';
									$html2 .='<td class="p-1" ><input class="form-control text-right" type="number" name="rate[]" value="'.$bd['rate'].'" id="rentinvpopup_rate_'.$bd['poda_and_rbt_id'].'" onblur="rentalInvoicePopupCalculattion('.$bd['poda_and_rbt_id'].','.$bd['parent_id'].')"></td>';
								}
								if($bd['rent'] !="" AND $bd['rent'] !="0"){
									$rent =$bd['rent'];	
								}else{
									$rent = calculateStringCalculation($bd['qty'].$bd['condition'].$bd['rate']);	
								}	
								$html2 .='<td class="p-1" ><input class="form-control text-right checkamountnotepmty_new_'.$property_tenant_billing_id.' rentinvpopup_child_rent_'.$bd['parent_id'].'" type="number" name="rent[]" value="'.round($rent).'" id="rentinvpopup_rent_'.$bd['poda_and_rbt_id'].'" readonly></td>';
							}else{
								$html2 .='<td class="p-1" colspan="4"><input type="hidden" name="qty[]"><input type="hidden" name="rate[]"><input class="form-control text-right checkamountnotepmty_new_'.$property_tenant_billing_id.'" type="number" name="rent[]" value="'.$bd['rent'].'" '.$redonlytext.' id="rentinvpopup_parent_rent_'.$bd['poda_and_rbt_id'].'"></td>';
							}
							$html2 .='<td class="p-1">
									<textarea class="form-control" name="remarks[]">'.$bd['remarks'].'</textarea>
								</td>';
							$html2 .='</tr>';
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
	$subtotal = $_POST['subtotal'];
	$taxamount = $_POST['taxamount'];
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
	  			$duesubtotal = ($row['value'] / 100)*$subtotal;
	  			$duetaxamount = ($row['value'] / 100)*$taxamount;
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
	  			$extradescount = "<spam class='d-block f-10 f-w-600'>Before Date ".fromDate($before_date)." (".customeummarmonyformate($discountwithamt).")  </span>";
	  		}
	  		$due_dates = date('Y-m-d', strtotime($transaction_date.' + '.$days.' days'));
	  		$html .='<tr> <td class="p-2">'.fromDate($due_dates).'</td><td class="p-2 text-right">'.customeummarmonyformate($duetypediscountamt).' '.$extradescount;
	  		
	  		$html .='<input type="hidden" name="payment_term_history['.$i.'][due_date]" value="'.$due_dates.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][amount]" value="'.$duetypediscountamt.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][extra_date]" value="'.$before_date.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][extra_amt]" value="'.$discountwithamt.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][percentage]" value="'.$row['value'].'">
	  			<input type="hidden" name="payment_term_history['.$i.'][without_tax_amount]" value="'.$duesubtotal.'">
	  			<input type="hidden" name="payment_term_history['.$i.'][tax_amount]" value="'.$duetaxamount.'">';
	  		$html .='</td></tr>';	

	  $i++;	}
	  	$html .='</table></td>';
	} 	
	$returnary['payment_terms'] = $html;
	$due_date = date('Y-m-d', strtotime($transaction_date.' + '.$due_days.' days'));
	$returnary['due_date'] = fromDate($due_date);
	echo json_encode($returnary);
}
if($page=="searchcourierdata"){
	
	//$url1 = API."?mode=searchcourierdata&".$_POST['search']; 
	$tb_in_courier = array('SL.NO','Date','From','To ','Courier Company','Courier Ref No' ,'In Time','Description','Qty', 'Invoice No', 'Tracking','Emp Receiver Name','Receiver Name');
	$url1 = API."?mode=searchcourierdata&courier_type=".$_POST['courier_type']."&courier_ref_no=".$_POST['courier_ref_no']."&courier_company=".$_POST['courier_company']."&courier_user_id=".$_POST['courier_user_id']."&courier_date=".$_POST['courier_date']."&from_detail=".$_POST['from_detail']."&to_detail=".$_POST['to_detail']."&courier_to_date=".$_POST['courier_to_date']; 
	$data_details1 = callAPI($url1);
	$data =  json_decode($data_details1,1);

	$_SESSION['download_data'] = array();

	//print_r($data);
	$html = '';
	$html .='<table class="table table-framed">
	 <thead>.';
	 foreach($tb_in_courier as $th){ 
        $html .='<th>'.$th.'</th>';
      }
      $html .='  <th></th> </thead><tbody>';
	if(count((array)$data['courier'])>0){
	$csvData = array();
	$sl=0; 
	foreach((array)$data['courier'] as $down){
	$sl++; 
	$csvData[] = array(
		'SL.NO' => $sl,
		'Date' => fromDate($down['courier_date']),
		'From' => $down['from_detail'],
		'To' => $down['to_detail'],
		'Courier Company' => $down['courier_company'],
		'Courier Ref No (Tracking Number)' => $down['courier_ref_no'],
		'Contact Number'=> $down['courier_mobile'],
		'In Time' => $down['courier_time'],
		'Address'=> $down['courier_adderss'],
		'Purpose' => $down['description'],
		'Qty' => $down['courier_qty'],
		'Invoice No' => $down['invoice_no'],
		'Tracking Status' => $data['status']['couriers'][$down['current_status']],
		'Employee Code' =>$data['emps'][$down['requested_by']]['emp_code'],
		'Employee Receiver Name' => $data['emps'][$down['requested_by']]['display_name'],
		'Created By' => $_SESSION['users'][$down['created_by']]
	);
	$html .='
	<tr>
        <td>'.$sl.'</td>
        <td>'.fromDate($down['courier_date']).'</td>
        <td>'.$down['from_detail'].'</td>
        <td>'.$down['to_detail'].'</td>
        <td>'.$down['courier_company'].'</td>
        <td>'.$down['courier_ref_no'].'</td>
        <td>'.$down['courier_time'].'</td>
        <td>'.$down['description'].'</td>
        <td>'.$down['courier_qty'].'</td>
         <td>'.$down['invoice_no'].'</td>
        <td>
            <label class="form-label label label-warning">'.$data['status']['couriers'][$down['current_status']].'</label>
        </td>
        <td>'. $data['emps'][$down['requested_by']]['emp_code'].' - '.$data['emps'][$down['requested_by']]['display_name'].'</td>
        <td>'.$_SESSION['users'][$down['created_by']].'</td>

    </tr>';
       // <td>'.$data['deliver_types'][$down['deliver_type']].'</td>
	
	}
	$_SESSION['download_data'] = $csvData;
	$_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
	$_SESSION['csv_header']['title'] = 'Courier Report';
	$_SESSION['csv_header']['date_time'] = date('d-m-Y H:i:s');
	$html = '<div class="text-end mt-3"><a class="btn btn-primary btn-sm" href="custom_view_download.php?page=Courier_Report&format=csv" target="_blank">CSV</a></div>'.$html;
	}else{
	$html .=' 	<h6 class="text-center mt-3"> No Data Found</h6><img class="mw-100 " alt="" src="assets/images/work.png">';
	}
	echo $html;
}

if($page=="searchstationery"){
	
	//$url1 = API."?mode=searchcourierdata&".$_POST['search']; 
	$url1 = API."?mode=searchstationerydata&request_date=".$_POST['request_date']."&require_date=".$_POST['require_date']."&stationery_request_ref_no=".$_POST['stationery_request_ref_no']."&stationery_user_id=".$_POST['stationery_user_id']; 
	$data_details1 = callAPI($url1);
	$data =  json_decode($data_details1,1);
	$html = '';
	$html .='<table class="table table-framed">
				<tbody>';
	if(count((array)$data['stationery_requests'])>0){
	foreach((array)$data['stationery_requests'] as $down){
	$request_no = "'".$down['request_no']."'";
	
	$html .='<tr><td width="10%" scope="row">';
	if($data['emps'][$down['user_id']]['image']!=""){
	$html .=' <a href=""><img  src="/uploads/all/'.$data['emps'][$down['user_id']]['image'].'" class="img-radius img-40" alt=""></a>';
			}else{ 
	$html .='<a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1">'.initials($data['emps'][$down['user_id']]['display_name']).'</span></a>';
		}
	$html .='       </td>
					<td>
						<a class="d-block text-dark f-w-600 f-14" href="">'.$data['emps'][$down['user_id']]['display_name'].'</a>
						<span class="text-muted f-14 f-w-600">'.$data['emps'][$down['user_id']]['email_id'].'</span>
					</td>

					<td>

						<a class="d-block text-dark f-w-600 f-14" href="">'.$down['request_no'].'</a>
						<span class="text-muted f-14 f-w-600">'.displayDate($down['request_date']).'</span>
					</td>
					<td>
						<a class="d-block text-dark f-w-600 f-14" href="">'.displayDate($down['require_date']).'</a>
						<span class="text-muted f-14 f-w-600">'. $down['request_comment'].'</span>
					</td>
					<td>
						<label class="form-label label label-warning">'.$data['status']['couriers'][$down['current_status']].'</label></br>
					   <span class="text-muted f-14 f-w-600">'.$data['deliver_types'][$down['deliver_type']].'</span> 
					</td>
					<td>
						<button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('.$request_no.')" >
							<i class="ti-pencil-alt"></i>
						</button>
					</td>
				</tr>';
	//$html .=' <tr><td colspan="5">'.$data['courier_query'].'</td></tr>';
	}
	}else{
	$html .=' 	<h6 class="text-center mt-3"> No Data Found</h6><img class="mw-100 " alt="" src="assets/images/work.png">';
	}
	echo $html;
}

if($page=="addstationery"){
	unset($_POST['page']);
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=addstationery&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page=="updatestationeryrequest"){
	print_r($_POST);
	echo $url = API."?mode=updatestationeryrequest&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page=="topupstationery"){
	//unset($_POST['page']);
	print_r($_POST);
	$url = API."?mode=topupstationery&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page=="updatetranspotfrom"){
	//unset($_POST['page']);
	print_r($_POST);
	$url = API."?mode=updatetranspotfrom&current_status=".$_POST['current_status']."&update_id=".$_POST['update_id'];
	$return = callAPI($url);
	echo $return;
}
if($page=="addfoodrequest"){
	unset($_POST['page']);
	$_POST['created_date'] = nowDate();
	$_POST['is_active'] = 0;
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=addfoodrequest&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
if($page=="updatefoodrequest"){
	//unset($_POST['page']);
	print_r($_POST);
	$url = API."?mode=updatefoodrequest&current_status=".$_POST['current_status']."&update_id=".$_POST['update_id'];
	$return = callAPI($url);
	echo $return;
}
if($page =="searchstationeryreport"){

    $url1 = API."?mode=searchstationeryreport&list_date=".$_POST['list_date']."&item_id=".$_POST['item_id'];
    $data_details1 = callAPI($url1);
    $data = json_decode($data_details1, true);

    if(count((array)$data) > 0){

        $html = '
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th class="text-end">In Qty</th>
                        <th class="text-end">Out Qty</th>
                        <th class="text-end">Run Qty</th>
                    </tr>
                </thead>
                <tbody>';

        $k = 1;

        // Opening Row
        if($data[0]['request_no'] != 'Opening'){

            $item_ledge = $data[0];
            $open = ($item_ledge['run_qty'] - $item_ledge['in_qty']) + $item_ledge['out_qty'];

            $html .= '
                <tr>
                    <td>'.$k.'</td>
                    <td>Opening</td>
                    <td>'.displayDate($_POST['list_date']).'</td>
                    <td class="text-end">'.$open.'</td>
                    <td class="text-end">0</td>
                    <td class="text-end">'.$open.'</td>
                </tr>';

            $k++;
        }

        // Ledger Rows
        foreach((array)$data as $item_ledge){

            $html .= '
                <tr>
                    <td>'.$k.'</td>
                    <td>'.$item_ledge['request_no'].'</td>
                    <td>'.displayDate($item_ledge['created_date']).'</td>
                    <td class="text-end">'.$item_ledge['in_qty'].'</td>
                    <td class="text-end">'.$item_ledge['out_qty'].'</td>
                    <td class="text-end">'.$item_ledge['run_qty'].'</td>
                </tr>';

            $k++;
        }

        $html .= '
                </tbody>
            </table>
        </div>';

    } else {

        $html = '
        <div class="text-center mt-3">
            <h6>No Data Found</h6>
            <img class="mw-100" alt="" src="assets/images/work.png">
        </div>';
    }

    echo $html;
}
if($page=='searchtransportbus'){

	echo $url1 = API."?mode=searchtransportbus&pickup=".$_POST['pickup']."&droping=".$_POST['droping']."&pickdate=".$_POST['pickdate']; 
	$data_details1 = callAPI($url1);
	$data =  json_decode($data_details1,1);
	echo "--------------"; print_r($data); echo "***************";
	$html = "";
	if(count((array)$data['routes'])>0){
		foreach((array)$data['routes'] as $route){ 
	        $listroute[$route['id']] = $route;
		    $html.= '<div class="card">
		            <div class="card-body">
		                <div class="row mt-3">
		                    <div class="col-lg-3">
		                        <h4 class="f-w-700 ">'.$bus[$route['company_transport_id']]['vehicle_name'].'</h4>
		                        <p>Seat Layout '.$bus[$route['company_transport_id']]['seat_layout'].'</p>
		                        <span class="text-danger">'.$bus[$route['company_transport_id']]['vehicle_type'].'</span>
		                    </div>
		                    <div class="col-lg-5">
		                        <div class="row">
		                            <div class="col-lg-4">
		                                <h4 class="f-w-700 f-20">'.date("h:i A", strtotime($route['arrival_time'])).'</h4>
		                                <p class="f-16 f-w-500">'.$route['route_starts'].'</p>
		                            </div>
		                            <div class="col-lg-3 text-center">
		                                <i class="ti-arrow-right text-primary f-w-700"></i>
		                            </div>
		                            <div class="col-lg-5">
		                                <h4 class="f-w-700 f-20">'.date("h:i A", strtotime($route['departure_time'])).'</h4>
		                                <p class="f-16 f-w-500">'.$route['route_ends'].'</p>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="col-lg-4 text-right">
		                        <h4 class="f-w-700 f-22 text-success">&#8377 '.$route['route_charges_monthly'].' </h4>
		                        <p>Off Days: <label class="form-label label label-success">'.implode(" , ",json_decode($route['off_days'],1)).'</label></p>
		                        <button class="btn btn-primary" onclick="viewlocation('.$route['id'].')">Select Location</button>
		                    </div>
		                </div>
		            </div>
		            <input type="hidden" name="" id="view_location_'.$route['id'].'" value="1"> 
		            <div class="card-body" style="display: none;" id="dis_location_'.$route['id'].'" >
		                <div class="row col-lg-12">';
		                    foreach((array)$data['transport_schedule'][$route['id']] as $transport_schedule){ 
		    $html.= '                 <div class="col-lg-4 p-2">'.$transport_schedule['stop_name'].' </div>
		                        <div class="col-lg-2">
		                            <button onclick="viewrequest('.$transport_schedule['id'].')" class="btn btn-info" style="font-size: 12px; padding: 5px;" > 
		                            Request </button>
		                        </div>';
		                     } 
		    $html.= '   </div>
		            </div>
		        </div>';
	    } 
    }else{
		$html .=' 	<h6 class="text-center mt-3"> No Data Found</h6><img class="mw-100 " alt="" src="assets/images/work.png">';
	}
    echo $html;
}
if($page == "updateemployeefeedbacks"){
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['response_status'] = '0';
	$data['response_datetime'] = nowDate();
	if($_POST['answer_type'] == 'MC'){
		$data['response'] = json_encode($_POST['response']);
	}else{
		$data['response'] = $_POST['response'];
	}
	$url = API."?mode=updaterowtabledata&fid=id&rid=".$_POST['rid']."&table=employee_feedbacks&method=post";
	$return = file_post_contents($url, $data, 'update');
	echo $return;
}
if($page == "updatenormalizations"){
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['normalization_status'] = '0';
	$url = API."?mode=updaterowtabledata&fid=id&rid=".$_POST['rid']."&table=employee_performance_appraisals&method=post";
	$return = file_post_contents($url, $data, 'update');
	echo $return;
}
if($page=="getappraisalbellcurves"){
	$curl = API."?mode=getappraisalbellcurves&rid=".$_POST['rid']; 
	$cudata = callAPI($curl);
	$result = json_decode($cudata,1);
	$appraisal_bell_curves = $result['appraisal_bell_curves'];
	$appraisal_rating_slabs = $result['appraisal_rating_slabs'];
	foreach($appraisal_rating_slabs as $ars){
		$slabdetail[$ars['id']] = $ars;
	}
	$slabs = json_decode($appraisal_bell_curves['slabs'],1);
	$html = "<input type='hidden' name='appraisal_bell_curve_id' value='".$_POST['rid']."'>";
	foreach($slabs as $slab){
			$slabdetails =$slabdetail[$slab['appraisal_rating_slab_id']];
			$html  .= '<span class="label label-lg label-inline f-w-700" style="background-color :'.$slabdetails['background_color'].'">'.$slabdetails['slabs_name']." - ".$slab['percentage'].'%</span>';
	}	
	echo $html;							
}
if($page=="assignemptofrom16a"){
	echo $curl = API."?mode=assignemptofrom16a&list_id=".$_POST['list_id']."&assement=".$_POST['assement_year']."&month=".$_POST['month']."&empids=".implode(',',$_SESSION['document'][$_POST['assement_year']][$_POST['list_id']]); 
	$cudata = callAPI($curl);
	$result = json_decode($cudata,1);	
}
if($page=="addsessiondocumenttypeyear"){
	print_r($_POST['document']);
	if(in_array($_POST['emp_id'],(array)$_SESSION['document'][$_POST['assement_year']][$_POST['list_id']])){
		unset($_SESSION['document'][$_POST['assement_year']][$_POST['list_id']][$_POST['emp_id']]);
	}else{
		$_SESSION['document'][$_POST['assement_year']][$_POST['list_id']][$_POST['emp_id']] = $_POST['emp_id'];
	}
	print_r($_SESSION['document']);
}
if($page=="getpettycashtopupdashboardchart"){
	$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
	$url = API."?mode=getpettycashtopupdashboardchart&reimbursementsyear=".$_POST['reimbursementsyear']."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&roleshortcode=".$roleshortcode; 
	echo $api = callAPI($url);
}
if($page=="getpettycashdashboardchart"){
	$roleshortcode = implode(",",(array)$_SESSION['role_ids']);
	$url = API."?mode=getpettycashdashboardchart&reimbursementsyear=".$_POST['reimbursementsyear']."&emp_id=".$_SESSION['emp_id']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&roleshortcode=".$roleshortcode; 
	echo $api = callAPI($url);
}
if($page=="insertpettycash"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=insertpettycash&method=post";
	$response1 = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response1,1);
	print_r($res);
	//echo $response1;
}
if($page == "publishcommentandrank"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=publishcommentandrank&method=post";
	$response1 = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response1,1);
	print_r($res);
}
if($page == "getState"){
	$wh1["where"] = array('is_active'=>0,'country_id'=>$_POST['c_id']);
	$wh1["field"] = "id, state_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=states&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($result as $res){
		$option .="<option value='".$res['id']."'>".$res['state_name']."</option>"; 
	}
	echo $option;
}
if($page == "getcity"){
	$wh1["where"] = array('is_active'=>0,'state_id'=>$_POST['c_id']);
	$wh1["field"] = "id, city_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=cities&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($result as $res){
		$option .="<option value='".$res['id']."'>".$res['city_name']."</option>"; 
	}
	echo $option;
}
if($page == "updatemodulestatus"){
	if($_POST['module_id'] !="" AND $_POST['statuse'] !=""){
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$data['is_active'] = $_POST['statuse'];
		$url = API."?mode=updaterowtabledata&fid=module_id&rid=".$_POST['module_id']."&table=module_setup&method=post";
		$return = file_post_contents($url, $data, 'update');
	}
	$ptapi = API."?mode=getsetupdetails"; 
	$callptapi = callAPI($ptapi);
	$result = json_decode($callptapi,1);
	$modulelists =  $result['module_setup'];
	$setup_pages = $result['setup_pages'];
	$setuppages = array();
	$seqpageary = array();
	foreach($modulelists as $modulelist){
		if($modulelist['id'] != '1' AND $modulelist['linking'] == '1'){
			$allmodule[$modulelist['module_id']] = $modulelist;
		}
		if($modulelist['is_active'] == '0'){
			$activemodule[$modulelist['module_id']] = $modulelist['module_id'];
			$setuppa = json_decode($modulelist['setup_pages'],1);
			if(count((array)$setuppa) !='0'){
				$seqpageary = array_unique(array_merge($seqpageary,$setuppa));		
			}
		} 
	}
	sort($seqpageary);
	$module_setup_linkings =  $result['module_setup_linkings'];
	foreach($module_setup_linkings as $linking){
		$linkingmodules[$linking['parent_module_id']][] = $linking;
	}
	foreach((array)$setup_pages as $setpage){
		if($setpage['parent_sequence'] == '0'){
			$setuppages[$setpage['sequence']][] = $setpage;
		}else{
			$setuppages[$setpage['parent_sequence']][] = $setpage;
		}
	}
	$pagedetail = $setuppages[$seqpageary[0]][0];	
	$firstnextseqstartnumber = $pagedetail['sequence'];
	
	//$nextseqstartnumber = $pagedetail['sequence'];
	$previousseqnumber = "6";
	$lastsetup_page_id = $_POST['nextpageseq'];
	foreach((array)$seqpageary as $nextkey=>$spage){
	//foreach($seqpageary as $spage){
		$pagedetail = $setuppages[$spage];
		//$nextseqstartnumber = $pagedetail[0]['sequence'];
		$pagedetail = $setuppages[$spage];
		$nextkeys = $nextkey+1;
		if(isset($seqpageary[$nextkeys])){
			$nextseqstartnumber = $setuppages[$seqpageary[$nextkeys]][0]['sequence'];
		}else{
			$nextseqstartnumber = '';
		}

		//include("setups/".$pagedetail[0]['page_name'].".php");
		//$includedData .= include "setups/".$pagedetail[0]['page_name'].".php";
		ob_start();
		include "setups/".$pagedetail[0]['page_name'].".php";
		$includedData .= ob_get_clean();
		$previousarray = end($pagedetail);
		$previousseqnumber = $previousarray['sequence'];
	}

	$returnary = array('nextseqstartnumber'=>$firstnextseqstartnumber,'html'=>$includedData);
	echo json_encode($returnary);
}
if($page=="submitsetup"){
	$File_Uploads_Path = "uploads/all/";
	if(isset($_POST['hid_image'])){
		if($_FILES['file']['error'] == "0"){
			$temp = explode(".", $_FILES['file']['name']);
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
				//$filename = $newfilename;
				$_POST['display_logo'] = $newfilename;
			}
		}else{
			$_POST['display_logo'] = $_POST['hid_image'];
		} 
	}
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=submitsetup&method=post";
	$response1 = file_post_contents($url, $_POST, 'insert');
	$res = json_decode($response1,1);
	print_r($res);
}
if($page == "getaddmoresetupdepartment"){
	$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
	$wh1["field"] = "id, division_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=divisions&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$html ="";
	$option = '<option value="">Select Any One</option>';
	foreach($result as $res){
		$option .="<option value='".$res['id']."'>".$res['division_name']."</option>"; 
	}
	$html = '<div class="row mt-3" id="'.$_POST['rowid'].'"><div class="col-lg-6"><input type="text" class="form-control" name="department_name[]"></div><div class="col-lg-5"><select class="js-example-basic-single" name="division_id[]">'.$option.'</select></div><div class="col-lg-1 p-t-25"><i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''.$_POST['rowid'].'\')"></i></div></div>';
	echo $html;
}

if($page == "getaddmoresetupcostcenter"){
	$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
	$wh1["field"] = "id, department_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=departments&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$html ="";
	$option = '<option value="">Select Any One</option>';
	foreach($result as $res){
		$option .="<option value='".$res['id']."'>".$res['department_name']."</option>"; 
	}
	$html = '<div class="row mt-3" id="'.$_POST['rowid'].'"><div class="col-lg-6"><input type="text" class="form-control" name="cost_center_name[]"></div><div class="col-lg-5"><select class="js-example-basic-single" name="department_id[]">'.$option.'</select></div><div class="col-lg-1 p-t-25"><i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''.$_POST['rowid'].'\')"></i></div></div>';
	echo $html;
}
if($page == "getaddmoresetupwarehouse"){
	$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
	$wh1["field"] = "id, location_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=locations&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$html ="";
	$option = '<option value="">Select Any One</option>';
	foreach($result as $res){
		$option .="<option value='".$res['id']."'>".$res['location_name']."</option>"; 
	}
	$html = '<div class="row mt-3" id="'.$_POST['rowid'].'">';
	
	$html .= '<div class="col-lg-5"><input type="text" class="form-control" name="warehouse_name[]"></div>';
	$html .= '<div class="col-lg-2"><input type="text" class="form-control" name="warehouse_short_name[]"></div>';
	$html .= '<div class="col-lg-3"><select class="js-example-basic-single" name="location_id[]">'.$option.'</select></div>';

	$html .= '<div class="col-lg-1"><i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''.$_POST['rowid'].'\')"></i></div></div>';
	echo $html;
}
if($page == "getaddmoresetupapplicationusers"){
	$ptapi = API."?mode=getsetupdetails"; 
	$callptapi = callAPI($ptapi);
	$result = json_decode($callptapi,1);
	$module_setup =  $result['module_setup'];
	foreach($module_setup as $msetup){
	  $module[$msetup['module_id']] = $msetup['display_name'];
	}
	$application_roles =  $result['application_roles'];
	$role_idd =array(SUPERADMIN_ROLE,ADMIN_ROLE);
	foreach($application_roles as $rolesre){
	    if(!in_array($rolesre['role_short_code'], $role_idd)){
	        $roles[$rolesre['application_module']][] = $rolesre;
	    }
	}
	$html ="";
	//$option = '<option value="">Select Any One</option>';
	$option = '';
	foreach($roles as $key=>$rolesd){
        $option .='<optgroup label="'.$module[$key].'">';
       	foreach($rolesd as $rolesr){ 
        	$option .='<option value="'.$rolesr['role_short_code'].'">'.$rolesr['display_name'].'</option>';
        }
      	$option .='</optgroup>';
     } 
    $errorkeyid =  "error_".$_POST['rowid'];
    $keyid =  "email_".$_POST['rowid'];
    $funclick = "checkdomainname('".$keyid."','email_id','".$errorkeyid."')";
	$html = '<div class="row mt-3" id="'.$_POST['rowid'].'">';
	
	$html .= '<div class="col-lg-3"><input type="text" class="form-control" name="first_name[]"><input type="hidden" name="user_type[]" value="1"></div>';
	
	$html .= '<div class="col-lg-3"><input type="text" class="form-control" name="last_name[]"></div>';

	$html .= '<div class="col-lg-5"><input type="text" class="form-control mb-2 allemails" data-errorcls="'.$errorkeyid.'" name="user_email[]" id="'.$keyid.'" onblur="'.$funclick.'"> <span class="text-danger f-12" id="'.$errorkeyid.'"  style="display:none">Email Id Already Register</span> <select class="form-control js-example-basic-single mt-2" name="role_id[]" required="" onblur="">'.$option.'</select></div>';

	$html .= '<div class="col-lg-1"><i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''.$_POST['rowid'].'\')"></i></div></div>';
	echo $html;
}
if($page=="insertworkattendancelogs"){
	$data['user_id'] = $_SESSION['userid'];
	$data['month_year'] = $_POST['month_year'];
	$data['type'] = "D";
	$data['created_date'] = nowDate();
	$data['is_active'] = 0;
	$data['created_by'] = $_SESSION['userid'];
	$data['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=adddata&table=work_attendance_logs&method=post";
	$return = file_post_contents($url, $data);
	echo $return;
}
if($page == "checkdomainavailability"){
	if($_POST['field']=="domain_name"){
		$wh1["where"] = array('is_active'=>'0',$_POST['field']=>$_POST['domain']);
		$wh1['field'] = "id";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=".MAINDB.".domain_registrations&data_name=".MAINDB."&data=".$data1; 
	}else{
		$wh1["where"] = array('is_active'=>'0','user_email'=>$_POST['domain']);
		$wh1['field'] = "id";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=".MAINDB.".user_registered_groups&data_name=".MAINDB."&data=".$data1; 
	}
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$json = $json[0];
	if(isset($json['id'])){
		echo json_encode(array("res"=>"true"));
	}else{
		echo json_encode(array("res"=>"false"));
	}
}
if($page=="inserttenantopeningbalance"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=inserttenantopeningbalance&method=post";
	$return1 = file_post_contents($url, $_POST);
	$result = json_decode($return1,1);
	print_r($result);
	//echo $return;
}
if($page=="updatepropertytenantdetails"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updatepropertytenantdetails&method=post";
	$return1 = file_post_contents($url, $_POST);
	$result = json_decode($return1,1);
	//print_r($result);
	$decode_rid =  deocdeId("id:".$result['unlock']['LASTID']);
	$rentalinvoice = array('rid'=>$decode_rid,"result"=>$result['unlock'],"return"=>$result);
	echo json_encode($rentalinvoice);
	//echo $return;
}
if($page=="getdatetorentalinvoices"){
	$html = "";
	$url1 = API."?mode=getpropertytenantdetails&un_lock_date=".$_POST['un_lock_date']."&rid=".$_POST['rid'];  
	$data_details1 = callAPI($url1);
	$result =  json_decode($data_details1,1);
	$rental_invoices = $result['rental_invoices'];
	$affectedinvoice = 'N';
	$html .= '<div class="card"><div class="card-header"><h5>Affected Invoice</h5></div>';
	$html .= '<div class="card-body">';
	foreach($rental_invoices as $rentinv){
		$affectedinvoice = 'Y';
		$html .= "<p class='text-danger'> <span class='f-w-600'>Inv No : </span>".$rentinv['transaction_number']." - ".fromDate($rentinv['transaction_date'])." (".customeummarmonyformateifine($rentinv['total_amount']).")</p>";
	}
	$html .= '</div>';
	$rentalinvoice = array("affectedinvoice"=>$affectedinvoice,"html"=>$html);
	echo json_encode($rentalinvoice);
}
if($page=="insertempshiftsdata"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=saveshiftemployee&method=post";
	$return = file_post_contents($url, $_POST);
	//$result = json_decode($return1,1);
	//print_r($result);
	echo $return;
}
if($page=="newcandidateautoregistation"){
	echo $curl = API."?mode=newcandidateautoregistation&candidate_id=".$_POST['candidate_id']; 
	$cudata = callAPI($curl);
	$result = json_decode($cudata,1);	
}
if($page=="savegenralcodedetails"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	//echo json_encode($_POST);
    $curl = API."?mode=savegenralcodedetails&method=post"; 
	$response = file_post_contents($curl, $_POST);
	echo $response; exit;
}
if($page=="gnrolepermision"){
	$curl = API."?mode=gnrolepermision&code=".$_POST['code']."&role=".$_POST['role']; 
	$cudata = callAPI($curl);
	$result = json_decode($cudata,1);
	echo $result['status']; exit;
}	
	
if($page=="submitcandidatedocuments"){
	$path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$_POST['candidate_id']."/Documents";
	if($_FILES['document_file']['error'] != "4"){
	   	$data['actual_file_name'] = imageuploadwitharray($path,'document_file'.$seq,'','',$key);
		$data['file_size_mb'] = $_FILES['document_file']['size'];
	}
	$data['file_name'] = $_POST['document_name'];
	$data['application_table_name'] = 'candidates';
	$data['transaction_id'] = $_POST['candidate_id'];
	$data['is_active'] = '0';
	$data['current_status'] = '0';
	$data['created_date'] = nowDate();
	$data['company_id'] = $_SESSION['company_id'];
	$data['created_by'] = $_SESSION['userid'];
	$url = API."?mode=adddata&table=application_attachments&method=post";
	$return = file_post_contents($url, $data);
	$returnresul['respons'] = json_decode($return,1);

	$wh1["where"] = array('company_id'=>$_SESSION['company_id'],'application_table_name'=>'candidates','transaction_id'=>$_POST['candidate_id'],'is_active'=>'0');
	$wh1["field"] = "id, file_name, actual_file_name, created_date";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=application_attachments&data=".$data1;
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$returnmessage = '';
	$returnmessage .='<table class="table table-hover m-b-0" style="font-size:13px;"><body>';
	 foreach($result as $res){
       	$returnmessage .='<tr>
            <td class="w-b-w-s p-2">
              <a href="uploads/'.$path.'/'.$res['actual_file_name'].'" download>
                <span class="d-block"><i class="ti-user"></i>'.$res['file_name'].'</span>
                <span class="d-block"><i class="ti-alarm-clock"></i>'.$res['created_date'].'</span>
              </a>
            </td>
          </tr>';
	}
	$returnmessage .= '</body></table>';
	$returnresul['all_attchaments'] = $returnmessage;
	echo json_encode($returnresul);
}
if($page == "checkemailidallreadyavilableornot"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=checkemailidallreadyavilableornot&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}	
if($page == "checkcompanynameallreadyavilableornot"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=getinvitationvendors&method=post";
	$return = file_post_contents($url, $_POST);
	$result = json_decode($return,1);
	if(isset($result['company_name']['id']) AND $result['company_name']['id'] !=""){
		echo json_encode(array("company_name"=>"1",'allresult'=>$result));
	}else{
		echo json_encode(array("company_name"=>"0",'allresult'=>$result));
	}
}
if($page == "createvendorregistertovendor"){
	$url = API."?mode=getvendoradmindashboard&transaction_table=".$_POST['transaction_table']."&vendor_id=".$_POST['vendor_id']."&vendorcreate=Y";
	$apicall = callAPI($url);
	$result = json_decode($apicall,1);
	print_r($result);
}		
if($page == "updateapplicationusers"){
	if($_POST['formtype'] == 'application_users_form'){
		$File_Uploads_Path = "uploads/all/";
		if($_FILES['file']['error'] != "4"){
			$temp = explode(".", $_FILES['file']['name']);
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
				//$filename = $newfilename;
				$_POST['user_photo'] = $newfilename;
			}
		}else{
			$_POST['user_photo'] = $_POST['old_user_photo'];
		} 
	}elseif($_POST['formtype'] == "application_user_module_roles"){
	}elseif($_POST['formtype'] == "remove_application_user_module_roles"){	
	}
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updateapplicationusers&method=post";
	$respons = file_post_contents($url, $_POST);
	echo $respons;
	//$result = json_decode($respons,1);
	//print_r($result);
}
if($page == "insertbuildings"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	foreach ($_POST['floor_name'] as  $key=>$value) {
		if($value!=""){
			$floor_image = $_POST['old_floor_image'][$key];
			if(isset($_FILES['file'.$key])){
				$floor_image = imageuploadwitharray("building_floors",'file'.$key,'','',$key);	
			}
			$listdata['floor_image'][]= $floor_image;
			$listdata['floor_name'][] = $_POST['floor_name'][$key];
			$listdata['building_floors_id'][] = $_POST['building_floors_id'][$key];
		}
	}
	$_POST['building_floors'] = $listdata;
	$_POST['building_image'] = $_POST['old_image'];
	//print_r($_FILES);
	if($_FILES['building_image']['error'] != 4 AND isset($_FILES['building_image']['error'])){
		$temp = explode(".", basename($_FILES['building_image']['name']));
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		$_POST['building_image'] = $newfilename;
		move_uploaded_file($_FILES['building_image']['tmp_name'], 'uploads/all/'.$newfilename);
	}
	$url = API."?mode=insertbuildings&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	$response = json_decode($response,1);
	$decode_rid =  deocdeId("id:".$response['result']['LASTID']);
	$result = array('decode_rid'=>$decode_rid,"rid"=>$response['result']['LASTID'],"full_result"=>$response);
	echo json_encode($result);
}
if($page == "insertbuildingfloorcabins"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	// foreach ($_POST['floor_name'] as  $key=>$value) {
	// 	if($value!=""){
	// 		$floor_image = $_POST['old_floor_image'][$key];
	// 		if(isset($_FILES['file'.$key])){
	// 			$floor_image = imageuploadwitharray("building_floors",'file'.$key,'','',$key);	
	// 		}
	// 		$listdata['floor_image'][]= $floor_image;
	// 		$listdata['floor_name'][] = $_POST['floor_name'][$key];
	// 		$listdata['building_floors_id'][] = $_POST['building_floors_id'][$key];
	// 	}
	// }
	// $_POST['building_floors'] = $listdata;
	$url = API."?mode=insertbuildingfloorcabins&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	$response = json_decode($response,1);
	//print_r($response);
	$decode_rid =  deocdeId("id:".$response['result']['LASTID']);
	$result = array("decode_rid"=>$decode_rid,"building_floor_cabin_id"=>$response['result']['LASTID'],"full_result"=>$response);
	echo json_encode($result);
}
if($page == "getequipmenttypes"){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'equipment_category_id'=> $_POST['equipment_category_id'],'is_active'=>'0');
	$data1["field"] = "id,equipment_types_name";
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=equipment_types&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value=''>Select Any One </option>";
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['equipment_types_name']."</option>";
	}
	echo $str;
}
if($page == "getequipments"){
	if($_POST['equipment_id'] !=""){
		$data1["where"] = array('company_id'=> $_SESSION['company_id'],'id'=> $_POST['equipment_id'],'is_active'=>'0');
		$data1["field"] = "id,equipment_name,employee_seating";
		$data = urlencode(json_encode($data1));
		$curl = API."?mode=gettabledetailswithconditions&table=equipments&data=".$data; 
		$cudata = callAPI($curl);
		$cudata = json_decode($cudata,1);
		$cudata = $cudata[0];
		echo json_encode($cudata,1);
	}else{
		$data1["where"] = array('company_id'=> $_SESSION['company_id'],'equipment_type_id'=> $_POST['equipment_type_id'],'is_active'=>'0');
		$data1["field"] = "id,equipment_name";
		$data = urlencode(json_encode($data1));
		$curl = API."?mode=gettabledetailswithconditions&table=equipments&data=".$data; 
		$cudata = callAPI($curl);
		$cudata = json_decode($cudata,1);
		$str = "<option value=''>Select Any One </option>";
		foreach ($cudata as  $data) {
			$str.= "<option value = '".$data['id']."'>".$data['equipment_name']."</option>";
		}
		echo $str;
	}
}
if($page == "insertequipmentallocations"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertequipmentallocations&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	$response = json_decode($response,1);
	$result = array("error"=>$response['error'],"error_message"=>$_SESSION['error_message'][$response['error']],"equipment_allocation_id"=>$response['result']['LASTID'],"full_result"=>$response);
	echo json_encode($result);
}

if($page == "addvendorfeedbackresponse"){
	//print_r($_POST); exit;
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=addvendorfeedbackresponse&emp_id=".$_SESSION['emp_id']."&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	echo $response;
}
if($page == "leaveapplycreateemployee"){
	//print_r($_POST); exit;
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	echo $url = API."?mode=leaveapplycreateemployee&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	echo $response;
}
if($page == "gettransactionactivityschedulecheckList"){
	$task_id = $_POST['task_id'];
	$url = API."?mode=gettransactionactivityschedules&task_id=".$task_id."&result_type=checklist";
	$response = callAPI($url);
	$result = json_decode($response,1);
	$transaction_activity_schedule = $result['transaction_activity_schedule'];
	//echo "<pre>"; print_r($transaction_activity_schedule); echo "</pre>";
	$check_list = json_decode($transaction_activity_schedule['check_list'], 1);
	$alldone = $requerdcnt =$mandatory = 0; 
	foreach($check_list as $check_lits){
		if($check_lits['checklist_completed'] =='Y'){
			$alldone++; // solve count
		}
		if($check_lits['mandatory_checklist'] =='Y'){
			$requerdcnt++; // mandatory count
		}
		if($check_lits['mandatory_checklist']=='Y' AND $check_lits['checklist_completed'] !='Y'){
			$mandatory++; // all mandatory solve requerd
		}
	}
	
	$visibleclckstatus =array(ST_ACTIVE,ST_INPROGRESS,ST_SEND_BACK,ST_CALLBACK);
	$custom_readonly = $error_message = '';
	 
	if($transaction_activity_schedule['assigned_to'] == $_SESSION['userid']){ //not approval message show
		if(!in_array($transaction_activity_schedule['current_status'], $visibleclckstatus)){
			$error_message = '<span class="text-danger"><i class="ti-alert"></i>'.$_SESSION['error_message']['9116'].'</span>';
			$custom_readonly = 'custom_readonly';
		}
		if($transaction_activity_schedule['current_status']==ST_APPROVED){
			$error_message="";
			$custom_readonly = 'custom_readonly';
		}
	}else{
		$error_message = '<span class="text-danger"><i class="ti-alert"></i>'.$_SESSION['error_message']['9115'].'</span>';
		$custom_readonly = 'custom_readonly';
	} 
	$workflowsteps = getWorkflowStatus('transaction_activity_schedule',$transaction_activity_schedule['id'],$transaction_activity_schedule['current_status']);
	//print_r($workflowsteps);
	$workflow_btn = "";
	$transaction_table = $transaction_activity_schedule['transaction_table'];
	if($requerdcnt <= $alldone AND $mandatory == '0' AND ($transaction_activity_schedule['approval_user_id'] == $_SESSION['userid'] || ($transaction_activity_schedule['assigned_to'] == $_SESSION['userid'] AND $transaction_activity_schedule['current_status'] == ST_SEND_BACK AND ($transaction_table == 'employees' || $transaction_table == 'vendor_registers' || $transaction_table == 'vendors')) || ($transaction_activity_schedule['assigned_to'] == $_SESSION['userid'] AND $transaction_table != 'vendor_registers' AND $transaction_table != 'vendors' AND $transaction_table != 'employees'))){ 
	//if($requerdcnt <= $alldone AND $mandatory == '0'){ 
		$workflow_btn = $workflowsteps['workflow_btn'];
		//echo "statr workflow";
	}else{
		//echo "not statr workflow";
	}
	$html .= '<div class="card">'.$workflowsteps['current_status_ribbon'];
	$html .= '<div class="card-header"><h5>Declaration</h5><div class="card-header-right  table-card-header m-r-50">'.$workflow_btn.'</div></div>';
	if($workflowsteps['breadcrumb'] !=""){ 
		$html .= '<div class="">'.$workflowsteps['breadcrumb'].'</div>';
	}else{
		$html .= '<div class="mt-5"></div>';
	}
	$html .= '<div class="card-body custom-scroll" style="height: 500px;">';
	
	$html .= '<div >';
	$html .= $error_message;
    $html .= '<table width="100%" class="table table-hover table-md">';
    $html .= '<tbody>';
  	
  	foreach($check_list as $positionkey=>$list){   
    	$checkboxcl=$classcheck="";
    	if($list['checklist_completed']=="Y"){ 
	      	$taskstatus++; 
	      	$classcheck="check-task check-task done-task";
	      	$checkboxcl = "checked disabled";
	    } 
	    $html .= '<tr>';
	    $onclickfunction = $extraparam = "";
	   if($transaction_activity_schedule['assigned_to'] == $_SESSION['userid'] AND in_array($transaction_activity_schedule['current_status'], $visibleclckstatus)){
	    	//$onclickfunction = 'onclick="getSingleCheckList('.$transaction_activity_schedule['id'].','.$positionkey.')"';
	    	$onclickfunctionclass = 'getSingleCheckList';
	    	$extraparam = "data-task_id='".$transaction_activity_schedule['id']."' data-positionkey='".$positionkey."'";
	    	
	    }
	    $html .= '<td width="90%" class="w-b-w-s p-b-10 '.$custom_readonly.'"><div class="to-do-list"><div class="checkbox-fade fade-in-primary m-b-0" id="checklist_div_'.$transaction_activity_schedule['id'].'_'.$positionkey.'"><label class="mb-0 form-label check-task '.$onclickfunctionclass.'  '.$classcheck.'" '.$extraparam.' id="checklist_label_'.$transaction_activity_schedule['id'].'_'.$positionkey.'" '.$onclickfunction.'><input type="checkbox" value="" '.$checkboxcl.' class="" id="checklist_checkbox_'.$transaction_activity_schedule['id'].'_'.$positionkey.'"><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary" ></i></span><span style="display: table;">'.$list['name']; 
	    if($list['mandatory_checklist']=="Y"){ 
			$html .= '<span class="text-danger">*</span>';
		} 
		$html .= '</span></label>';

		if($list['comment'] !=""){ 
			$html .= '<span class="d-block"><span class="ti-notepad"></span> &nbsp;'.$list['comment'].'</span>';
	 	} 
		$html .= '</div></div></td>'; 

		$html .= '<td width="10%"><span class="float_end">';
            if(trim($list['attachment_name']) !="" AND strlen($list['attachment_name']) !='0'){ 
             	$path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_activity_schedule['transaction_table']."/".$transaction_activity_schedule['assigned_to']."/Documents/";
            	//$html .= '<a href="'.$path.$list['attachment_name'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download Attachment" download><i class="ti-cloud-down m-l-20"></i></a>';
            	$html .= '<a class="dynamicFilePriviews" data-pathurl="'.$path.$list['attachment_name'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Attachment"><i class="ti-eye"></i></a>';
            	
            } 
             if(trim($list['downloadfile']) !="" AND strlen($list['downloadfile']) !='0'){ 
              $html .= '<a href="uploads/all/'.$list['downloadfile'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download Document" download><i class="ti-exchange-vertical  m-l-20"></i></a>'; 
            }
            if($tas['link_page'] == '0'){ 
            	$html .= '<a href="'.$tas['page_url'].'" target="_blank" class="text-primary UpdateTransactionActivitySchedule1" data-seqid="'.$tas['id'].'"><h6 class="mb-0 f-14" ><i class="ti-new-window"></i></h6></a>';
            }
           $html .= '</span></td>';
	    $html .= '</tr>';
    } 
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	echo $html;
}
if($page == 'gettransactionactivityschedulesinglechecklist'){
	$task_id = $_POST['task_id'];
	$url = API."?mode=gettransactionactivityschedules&task_id=".$task_id."&result_type=singlechecklist";
	$response = callAPI($url);
	$result = json_decode($response,1);
	$transaction_activity_schedule = $result['transaction_activity_schedule'];
	$display_attributes = $result['display_attributes'];
	$file_upload = json_decode($display_attributes['file_upload'],1);
	//print_r($file_upload);
	$check_list = json_decode($transaction_activity_schedule['check_list'], 1);
	$checklist_position_detail = $check_list[$_POST['checklist_position']];
	
	$fileaccetformat = implode(',',(array)$file_upload['accepted_files']);
	$accepted_files =$accepted_file_message= "";
	if($fileaccetformat !=""){
		$accepted_files = 'accept="'.$fileaccetformat.'"';
		$accepted_file_message =  'Allowed File Types '.$fileaccetformat.'. ';
	}
	if($file_upload['file_size'] !=""){
		$accepted_files = 'accept="'.$fileaccetformat.'"';
		$accepted_file_message .=  '</br> Maximum Upload Size Is - '.$file_upload['file_size'].' Bytes';
	}
	$html .= '<div class="card">';
	$html .= '<div class="card-header"><h5>Task</h5><div class="card-header-right"></div></div>';

	$html .= '<div class="card-body"><form id="submitsinglechecklistform">';
	$html .= '<div class="form-group row">
		<div class="col-lg-12  form-group">
			<label class="form-label">'.$checklist_position_detail['name'].' <span class="text-danger">*</span> </label>
			<textarea class="form-control" name="comment" id="comment">'.$checklist_position_detail['comment'].'</textarea>
		</div>';
		$stylcss = "display:none";
		if($checklist_position_detail['attachment_requerd']=='Y'){
			$stylcss = "display:block";
		}  
		//

	

	$html .= '<div class="col-lg-10  form-group" style="'.$stylcss.'">
			<label class="form-label">Attchements<span class="text-danger">*</span></label>
			<span class="fileinputvalidation_error text-danger d-block"></span>
			<input class="form-control mb-2 fileinputvalidation" id="popupfile" type="file" '.$accepted_files.' data-size="'.$file_upload['file_size'].'"><input class="form-control" type="hidden" value="'.$checklist_position_detail['attachment_name'].'" name="old_attachments" id="old_attachments">';
	
	$html .= '<span class="text-danger d-block f-12">'.$accepted_file_message.'</span>';

	$html .= '</div><div class="col-lg-2"><label class="form-label d-block">File</label>';
	if(trim($checklist_position_detail['attachment_name']) !="" AND strlen($checklist_position_detail['attachment_name']) !='0'){ 
     	$path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_activity_schedule['transaction_table']."/".$transaction_activity_schedule['assigned_to']."/Documents/";
    	//$html .= '<a href="'.$path.$checklist_position_detail['attachment_name'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Old Attachments Download" class=" text-primary" download><i class="ti-cloud-down"></i> &nbsp; Old Attachment Download</a>';
    	$html .= '<a class="dynamicFilePriviews" data-pathurl="'.$path.$checklist_position_detail['attachment_name'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Old Attachments Preview"><i class="ti-eye"></i></a>';
    }else{
    	$html .= ' - ';
    } 
	if(trim($checklist_position_detail['downloadfile']) !="" AND strlen($checklist_position_detail['downloadfile']) !='0'){ 
	    $html .= '<a href="uploads/all/'.$checklist_position_detail['downloadfile'].'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download Document" download><i class="ti-exchange-vertical m-l-10"></i></a>'; 
	}
	
	$html .= '</div></div>';

	$html .= '<input type="hidden" name="attachment_type" id="attachment_type" value="'.$check_list[$_POST['checklist_position']]['attachment_requerd'].'">
		<input type="hidden" name="aryposition" id="aryposition" value="'.$_POST['checklist_position'].'">
		<input type="hidden" name="rid" id="riddd" value="'.$transaction_activity_schedule['id'].'">';
	$html .= '<input type="hidden" name="transaction_table" id="transaction_table" value="'.$transaction_activity_schedule['transaction_table'].'">';
	$html .= '<input type="hidden" name="assigned_to" value="'.$transaction_activity_schedule['assigned_to'].'">';
	$html .= "<input type='hidden' name='check_list' id='check_list' value='".$transaction_activity_schedule['check_list']."'>";

	$html .= '<button type="button" class="btn btn-primary m-b-20 submitSingleCheckListBtn"   id="submittaskanswer" ><i class="ti-check"></i>
			Submit</button><button type="button" class="btn btn-warning m-b-20 m-l-10 cancelSingleCheckListBtn" ><i class="ti-back-left"></i>
			close</button>';
	$html .= '</form></div>';
	$html .= '</div>';
	echo $html;
}
if($page == 'addupdatequestionfeddback'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=addupdatequestionfeddback&emp_id=".$_SESSION['emp_id']."&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	$res = json_decode($response,1);
	$question_id=$res['question_id'];
	$str = '';
	$_POST['change_id'] = $_POST['change_id']!="" ? $_POST['change_id'] : $question_id;
	if($_POST['question_id']==""){ $str.= '<div class="" id="ques_id_'.$question_id.'">';   }
	$str.= '<div class="p-20 z-depth-top-0 m-t-10 row" id="" >
                <div class="col-lg-11">
                    <h6>'.$_POST['question'].'</h6>
                </div>
            	<div class="col-lg-1 text-right">
                    <i class="ti-pencil-alt cursor-pointer" onclick="addQuestion('.$_POST['cat_id'].','.$question_id.','.$_POST['change_id'].')"></i>
                </div>
                <div class="col-lg-12">';
    if($_POST['question_type']==4){
    	$str.='<textarea id="" class="form-control mb-2" readonly></textarea>';
    }
    if($_POST['question_type']==2){
    	$str.='<div class="rating f-12 text-warning mt-2 d-inline-block">';
     	for($n=1;$n<=$_POST['question_option']['2']['0'];$n++){ 
          $str.='<i class="fa fa-star f-16 text-warning" ></i>';                  
       	}
    	$str.='</div>';
    }
    if($_POST['question_type']==3){
    	$str.='<div class="">';
	    foreach ($_POST['question_option']['3'] as $option){ 
	    	$str.='<b> 
	        <div class="radio-group required float-left ml-2">
	            <input type="radio" value="" name="response" >
	            <label for="q1">'.$option.' </label> 
	        </div>
	    	</b>';
		}
	}
    $str.='</div>';
    	
	if($_POST['question_id']==""){ $str.= '</div>'; }
	echo $str;
}
if($page == 'createnewversionvendorfeedback'){
	$url = API."?mode=CreateNewversionvendorsetup&setup_id=".$_POST['setup_id'];
	$response = callAPI($url);
	$result = json_decode($response,1);
	$view = deocdeId("id:".$result['main']['LASTID']);
	echo $view; exit;
}
if($page == "intertequipments"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['equipment_image'] = $_POST['old_equipment_image'];
	if($_FILES['equipment_image']['error'] != 4 AND isset($_FILES['equipment_image']['error'])){
		$temp = explode(".", basename($_FILES['equipment_image']['name']));
		$Filename=  "equipment_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		$_POST['equipment_image'] = $newfilename;
		move_uploaded_file($_FILES['equipment_image']['tmp_name'], 'uploads/equipments/'.$newfilename);
	}
	$url = API."?mode=intertequipments&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	echo $response;
}

if($page == "getremaingequipmentslist"){
	$url = API."?mode=getequipmentallocations&pagetype=getremaingequipmentslist&equipment_type_id=".$_POST['equipment_type_id']; 
	$response = callAPI($url);
	$result = json_decode($response,1);
	$equipments_list = $result['equipments_list'];
	$str = "<option value=''>Select Any One </option>";
	foreach ($equipments_list as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['equipment_name']."</option>";
	}
	echo $str;
}
if($page == "getequipmenttypespopup"){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'equipment_category_id'=> $_POST['equipment_category_id'],'is_active'=>'0');
	$data1["field"] = "id, equipment_types_name, seating_equipment";
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=equipment_types&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value=''>Select Any One </option>";
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."||".$data['seating_equipment']."'>".$data['equipment_types_name']."</option>";
	}
	echo $str;
}
if($page == "insertemployeepopup"){
	$emp_id = $_POST['emp_id'];
	$_POST['image'] = $_POST['old_image'];
	if($_FILES['user_image']['error'] != 4 AND isset($_FILES['user_image']['error'])){
		$temp = explode(".", basename($_FILES['user_image']['name']));
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		$_POST['image'] = $newfilename;
		move_uploaded_file($_FILES['user_image']['tmp_name'], 'uploads/all/'.$newfilename);
	}
	unset($_POST['emp_id']);
	unset($_POST['page']);
	unset($_POST['old_image']);
	unset($_POST['user_image']);
	$_POST['display_name'] = $_POST['first_name']." ".$_POST['last_name'];
	if($emp_id!=""){
		$_POST['updated_date'] = nowDate();
		$_POST['updated_by'] = $_SESSION['userid'];
		if($_POST['is_active'] =="Y"){
			$_POST['is_active'] ="0";
		}else{
			$_POST['is_active'] =1;
			$updatespace['updated_date'] = nowDate();
			$updatespace['updated_by'] = $_SESSION['userid'];
			$updatespace['is_active'] =1;
			$url = API."?mode=updaterowtabledata&fid=emp_id&rid=".$emp_id."&table=building_floor_space_allocations&method=post";
			file_post_contents($url, $updatespace, 'update');
		}
		$url = API."?mode=updaterowtabledata&rid=".$emp_id."&table=employees&method=post";
		$return = file_post_contents($url, $_POST, 'update');
	}else{
		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['current_status'] = 0;
		$_POST['is_active'] = 0;
		$_POST['status_date'] = nowDate();
		$_POST['from_date'] = date('Y-m-d');
		$_POST['to_date'] = date('Y-m-d');
		$url = API."?mode=adddata&table=employees&method=post";
		$return = file_post_contents($url, $_POST, 'insert');
	}
 	echo $return;
}
if($page == "getemployeeemailidalreadyavailableornot"){
	$wh1["where"] = array('is_active'=>'0','email_id'=>$_POST['email_id']);
	$wh1['field'] = "id";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=employees&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$json = $json[0];
	if(isset($json['id']) AND $_POST['emp_id']!=$json['id']){
		echo json_encode(array("res"=>"true"));
	}else{
		echo json_encode(array("res"=>"false"));
	}
}
if($page == "savedataforprofessionaltaxexemptions"){
	//print_r($_POST); //exit;
	$url = API."?mode=savedataforprofessionaltaxexemptions&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	echo $response;
}
if($page == "insertemployeeterminations"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertemployeeterminations&method=post";
	$response = file_post_contents($url,$_POST,'insert');
	echo $response;
}
if($page == "insertholdsalriesparoll"){
	//print_r($_POST);
	$url = API."?mode=togenrateholdemployees&empids=".$_POST['empids']."&pay_year=".$_POST['pay_year']."&pay_month=".$_POST['pay_month']."&pay_area_id=".$_POST['pay_area_id'];
	//echo $url;
	$response = callAPI($url);
}
if($page == "insertapplicationtickets"){
	$File_Uploads_Path = "uploads/all/";
	if($_FILES['file']['error'] != "4"){
		$temp = explode(".", $_FILES['file']['name']);
		$Filename=  "file_".time()."_".uniqid();
		$newfilename = $Filename.'.' .end($temp);
		if(move_uploaded_file($_FILES['file']["tmp_name"], $File_Uploads_Path.$newfilename)){
			$_POST['attachment'] = $newfilename;
		}
	}else{
		//$_POST['attachment'] = $_POST['oldattachment'];
	} 
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=insertapplicationtickets&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "updateapplicationtickets"){
	$_POST['company_id']=$_SESSION['company_id'];
	$_POST['user_id']=$_SESSION['userid'];
	$url = API."?mode=updateapplicationtickets&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "checkvendorinstructions"){
	$transaction_table = $_POST['transaction_table'];
	$transaction_id = $_POST['transaction_id'];
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['instruction_status'] = 'Y';
	$url = API."?mode=updaterowtabledata&rid=".$transaction_id."&table=".$transaction_table."&method=post";
	echo $return = file_post_contents($url, $data, 'update');
}
if($page == "getitemsorders"){
	$url = API."?mode=getitemsorders&item_id=".$_POST['item_id']."&table_name=".$_POST['table_name']; 
	$response = callAPI($url);
	$result = json_decode($response,1);
	$transactions = $result['res'];
	/**$str = '<div class="card">
        <div class="card-header">
            <h5>Item History</h5>
            <div class="card-header-right">
                <i class="ti-close" onclick="closePreviousOrder()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
            </div>
        </div>';**/
    $str = '<div class="modal-header"><h4 class="modal-title">Item History</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>';    
    $str .= '<div class="modal-body">';
    if(count((array)$transactions) != 0){
	    $str .='<table class=" table table-hover ">';
	    $str .= '<tr><th>Trans. No.</th><th>Party</th><th>Date</th><th>Qty</th><th>Price</th><th>Amount</th></tr>';  
		foreach ($transactions as  $trns){
	       $str .= '<tr>
	            <td class="p-2"><a href="index?option='.$_POST['option'].'&Rid='.deocdeId("id:".$trns['id']).'" target="_blank" class="text-primary">'.$trns['transaction_number'].'</a></td>
	            <td class="w-b-w-s p-2">'.$trns['party_name'].'</td>
	            <td  class="p-2">'.$trns['transaction_date'].'</td>
	            <td class="p-2">'.customeNumberFormate($trns['item_quantity']).'</td>
	            <td class="p-2">'.customeummarmonyformate($trns['item_price']).'</td>
	            <td class="p-2">'.customeummarmonyformate($trns['item_amount']).'</td>
	        </tr>';
		}
		$str .= '</table>';
	}else{
		$str .= '<div class="card-px text-center "><p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> No Item History</p><div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div>';
	}
	$str .= '</div>';
    //$str .= '</div>';
	echo $str;
}
if($page == 'updateapplicationfavourites'){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=updateapplicationfavourites&method=post";
	$return = file_post_contents($url, $_POST, 'insert');
	echo $return;
}
if($page == "updatesendverificationprofilestatus"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updatesendverificationprofilestatus&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	// $url = API."?mode=updaterowtabledata&rid=".$transaction_id."&table=".$transaction_table."&method=post";
	// echo $return = file_post_contents($url, $data, 'update');
}
if($page == "updatevendorprofileupdatestatus"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updatevendorprofileupdatestatus&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	// $url = API."?mode=updaterowtabledata&rid=".$transaction_id."&table=".$transaction_table."&method=post";
	// echo $return = file_post_contents($url, $data, 'update');
}
if($page == "getpropertyownerdetails"){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'property_owner_id'=> $_POST['property_owner_id'],'is_active'=>'0');
	$data1["field"] = "id, property_name";
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=property_owner_details&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value=''>All Property</option>";
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['property_name']."</option>";
	}
	echo $str;
}
if($page == "projecttimesheetmontly"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST); 
	$url = API."?mode=addupdateprojecttimesheetmontly&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "updatevendorverifications"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updatevendorverifications&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "addupdateempassignmilestone"){
	//print_r($_POST);
	$url = API."?mode=addupdateempassignmilestone&empid=".$_POST['emp_id']."&transaction_id=".$_POST['transaction_id']."&item_id=".$_POST['item_id'];
	//echo $url;
	$response = callAPI($url);
	echo  $response; exit;
}
if($page == "allocateemployeeseat"){
	$url = API."?mode=allocateemployeeseat&equipment_allocation_id=".$_POST['equipment_allocation_id']."&emp_id=".$_POST['emp_id'];
	$response = callAPI($url);
	echo  $response; exit;
}
if($page == "updateemployees"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_FILES);
	//print_r($_POST);
	if($_POST['transaction_table'] == "emp_work_experiences"){
		foreach ($_POST['old_work_experience_attachment'] as $key=>$value){
			$file = $value;
			if(isset($_FILES['file'.$key]['error'])){
				$file = imageuploadwitharray("all",'file'.$key,'','',$key);	
			}
			$_POST['we_attachment'][]= $file;
		}
	}elseif($_POST['transaction_table'] == "emp_educations"){
		foreach ($_POST['old_educations_attachment'] as $key=>$value){
			$file = $value;
			if(isset($_FILES['file'.$key]['error'])){
				$file = imageuploadwitharray("all",'file'.$key,'','',$key);	
			}
			$_POST['we_attachment'][]= $file;
		}
	}elseif($_POST['transaction_table'] == "employees"){
		if($_FILES['profile_image']['error'] != 4 AND isset($_FILES['profile_image']['error'])){
			$temp = explode(".", basename($_FILES['profile_image']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$_POST['image'] = $newfilename;
			move_uploaded_file($_FILES['profile_image']['tmp_name'], 'uploads/all/'.$newfilename);
		}
		//Aadhar
		$attached_files = array();
		if($_FILES['aadhar_attachment']['error'] != 4 AND isset($_FILES['aadhar_attachment']['error'])){
			$temp = explode(".", basename($_FILES['aadhar_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['aadhar_attachment'] = $newfilename;
			move_uploaded_file($_FILES['aadhar_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['aadhar_attachment'] = $_POST['old_aadhar_attachment'];
		}
		//passport
		if($_FILES['passport_attachment']['error'] != 4 AND isset($_FILES['passport_attachment']['error'])){
			$temp = explode(".", basename($_FILES['passport_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['passport_attachment'] = $newfilename;
			move_uploaded_file($_FILES['passport_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['passport_attachment'] = $_POST['old_passport_attachment'];
		}
		//driving_licence
		if($_FILES['driving_licence_attachment']['error'] != 4 AND isset($_FILES['driving_licence_attachment']['error'])){
			$temp = explode(".", basename($_FILES['driving_licence_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['driving_licence_attachment'] = $newfilename;
			move_uploaded_file($_FILES['driving_licence_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['driving_licence_attachment'] = $_POST['old_driving_licence_attachment'];
		}
		//pan_attachment
		if($_FILES['pan_attachment']['error'] != 4 AND isset($_FILES['pan_attachment']['error'])){
			$temp = explode(".", basename($_FILES['pan_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['pan_attachment'] = $newfilename;
			move_uploaded_file($_FILES['pan_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['pan_attachment'] = $_POST['old_pan_attachment'];
		}
		//pf_attachment
		if($_FILES['pf_attachment']['error'] != 4 AND isset($_FILES['pf_attachment']['error'])){
			$temp = explode(".", basename($_FILES['pf_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['pf_attachment'] = $newfilename;
			move_uploaded_file($_FILES['pf_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['pf_attachment'] = $_POST['old_pf_attachment'];
		}
		//pf_attachment
		if($_FILES['pf_uan_attachment']['error'] != 4 AND isset($_FILES['pf_uan_attachment']['error'])){
			$temp = explode(".", basename($_FILES['pf_uan_attachment']['name']));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$attached_files['pf_uan_attachment'] = $newfilename;
			move_uploaded_file($_FILES['pf_uan_attachment']['tmp_name'], 'uploads/all/'.$newfilename);
		}else{
			$attached_files['pf_uan_attachment'] = $_POST['old_pf_uan_attachment'];
		}
		$_POST['attached_files'] = json_encode($attached_files);

	}
	$url = API."?mode=updateemployees&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	
}
if($page == "getselectskills"){
	$wh1["where"] = array('is_active'=>'0','id'=>$_POST['skill_id']);
	$wh1['field'] = "max_ratting";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=skills&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$max_ratting = ($result[0]['max_ratting'] !="" || $result[0]['max_ratting'] !="0")?$result[0]['max_ratting']:10;
	$str = "";
	for($i=1; $i <= $max_ratting; $i++){
		$str.= "<option value = '".$i."'>".$i."</option>";
	}
	echo $str;	
}
if($page == "insertempbanks"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertempbanks&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "payrollupdateonetime"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	//print_r($_POST);
	$url = API."?mode=payrollupdateonetimeajax&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	//echo $response;
}
if($page == "insertbuildingfloorspaceallocations"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertbuildingfloorspaceallocations&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "userpasswordreset"){
	$url = API."?mode=userpasswordreset&resetpass_user_id=".$_POST['resetpass_user_id'];
	$response = callAPI($url);
	echo $response;
}
if($page == "getemployeelistwithbuildingdetailfilter"){
	$url = API."?mode=getequipmentallocations&pagetype=getemployeelistwithbuildingdetailfilter&value=".$_POST['value']; 
	$response = callAPI($url);
	$result = json_decode($response,1);
	$employee_fillter = $result['employee_fillter'];
	$building_floor_spaces = $result['building_floor_spaces'];
	$str = '';
	$no_image = "uploads/all/no-image.jpg";
	if(count((array)$employee_fillter) != 0){
		foreach($employee_fillter as $emplist){
			$str.= '<div class="align-middle mt-2">';
			if($emplist['image']!=""){
	        	$str.= '<img src="uploads/all/'.$emplist['image'].'"   class="img-30 align-middle m-r-15 seating_emp_image" onerror="this.src='.$no_image.';">';
			}else{
	        	$str.= '<span  alt="user image" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" >'.initials($emplist['display_name']).'</span>';
	        } 
	        $str.= '<div class="d-inline-block align-middle">';
	       	$str.= '<p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;">'.$emplist['display_name'];
	       	$str.= '</p>';
	       	$str.= '<p class="f-10 mb-0">'.$emplist['emp_code'].'</p>';
	       	$str.= '</div>';
	       	       	if($building_floor_spaces[$emplist['id']]['cabin_name'] !=""){
	       		$str.= '<span class="f-10 mb-0 float-end text-primary cursor-pointer mr-5" onclick="ShowBuildingFloorSpaceAllocations('.$building_floor_spaces[$emplist['id']]['building_floor_id'].','.$building_floor_spaces[$emplist['id']]['id'].')"><i class="ti-location-pin"></i> View Location ('.$building_floor_spaces[$emplist['id']]['cabin_name'].')</span>';
	       	}else{
	       		$str.= '<span class="f-10 mb-0 text-danger float-end mr-5"><i class="ti-alert"></i>  Unknown location</span>';
	       	}
			$str.= '</div>'; 
		}
	}else{
		$str.= '<div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Employees</p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>   
        </div>';
    }
	echo $str;
}
if($page == "getgrnitems"){
	$wh1["where"] = array('is_active'=>'0','id'=>$_POST['grn_id']);
	$wh1['field'] = "id, doc_ref_no, doc_ref_date, transaction_number, transaction_date";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=goods_receipt_notes&data=".$data1; 
	$data_details1 = callAPI($url1);
	$mainresult = json_decode($data_details1,1);
	$mainresult = $mainresult[0];

	$wh1["where"] = array('is_active'=>'0','transaction_id'=>$_POST['grn_id']);
	$wh1['field'] = "id, item_id, invoice_item_quantity, item_quantity";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=grn_items&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$result_cnt = count((array)$result);
	$returnary = array('result_cnt'=>$result_cnt,'result'=>$result,'main_result'=>$mainresult);
	echo json_encode($returnary);
}
if($page == "getdeliveryorderitems"){
	$wh1["where"] = array('is_active'=>'0','id'=>$_POST['grn_id']);
	$wh1['field'] = "id, doc_ref_no, doc_ref_date, transaction_number, transaction_date";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=delivery_orders&data=".$data1; 
	$data_details1 = callAPI($url1);
	$mainresult = json_decode($data_details1,1);
	$mainresult = $mainresult[0];

	$wh1["where"] = array('is_active'=>'0','transaction_id'=>$_POST['grn_id']);
	$wh1['field'] = "id, item_id, invoice_item_quantity, item_quantity";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=do_items&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$result_cnt = count((array)$result);
	$returnary = array('result_cnt'=>$result_cnt,'result'=>$result,'main_result'=>$mainresult);
	echo json_encode($returnary);
}
if($page == "insertvendorcustomeritems"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertvendorcustomeritems&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "getpendingadvanecepayments"){
	$ref_table  = $_POST['ref_table'];
	$ref_id  = $_POST['ref_id'];
	$wh1["where"] = array('is_active'=>'0','id'=>$ref_id);
	$wh1['field'] = "id, advance_payment, pending_advance_payment";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=".$ref_table."&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$result2 = $result[0];
	$result_cnt = count((array)$result);
	$returnary = array('result_cnt'=>$result_cnt,'result'=>$result2);
	echo json_encode($returnary);
}
if($page == "getchildchartofaccount"){
	$accountno = $_POST['accountno'];
	$wh1["where"] = array('is_active'=>'0','parent_account_code'=>$accountno);
	$wh1['field'] = "id,account_name,account_code,parent_account_code";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=chart_of_accounts&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$result_cnt = count((array)$result);
	$str = "<option value=''>All Accounts</option>";
	foreach ($result as  $coa){
		$str.= "<option value = '".$coa['account_code']."'>".$coa['account_name']." (".$coa['account_code'].")"."</option>";
	}
	$returnary = array('result_cnt'=>$result_cnt,'option'=>$str);
	echo json_encode($returnary);
}
if($page == "insertdebitandcreditnotes"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertdebitandcreditnotes&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "getdebitcreditnotereftypelist"){
	$ref_type = $_POST['ref_type'];
	$wh1["where"] = array('cancel_invoice'=>'N','is_active'=>'0','current_status'=>ST_APPROVED);
	$wh1['field'] = "id,transaction_number,transaction_date";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=".$ref_type."&data=".$data1; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$result_cnt = count((array)$result);
	$str = "<option value=''>Select Any One</option>";
	foreach ($result as  $coa){
		$str.= "<option value = '".$coa['id']."'>".$coa['transaction_number']." (".$coa['transaction_date'].")"."</option>";
	}
	$returnary = array('result_cnt'=>$result_cnt,'option'=>$str);
	echo json_encode($returnary);
}

if($page=="emailinvoicesend"){
	$url = API."?mode=emailinvoicesend&transaction_id=".$_POST['transaction_id']."&transaction_table=".$_POST['transaction_table']."&template_reference_id=".$_POST['template_reference_id']; 
	echo $api = callAPI($url);
}
if($page == "approvedapaceallocationsstatus"){
	$url = API."?mode=approvedapaceallocationsstatus&building_floor_space_allocation_id=".$_POST['building_floor_space_allocation_id']; 
	echo $api = callAPI($url);

}
if($page == "getenquiryvendororcustomer"){
	$url = API."?mode=getenquiryvendororcustomer&value=".$_POST['value']."&searchvendorcustomertype=".$_POST['searchvendorcustomertype']; 
	$response = callAPI($url);
	$result = json_decode($response,1);
	$search_result = $result['search_result'];
	//echo "<pre>"; print_r($result); echo "</pre>";
	$str = '';
	$no_image = "uploads/all/no-image.jpg";
	if(count((array)$search_result) != 0){
		foreach($search_result as $vencust){
			$str.= '<div class="align-middle mt-2">';
			if($vencust['image']!=""){
	        	$str.= '<img src="uploads/all/'.$vencust['image'].'"   class="img-30 align-middle m-r-15 seating_emp_image" onerror="this.src='.$no_image.';">';
			}else{
	        	$str.= '<span  alt="user image" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" >'.initials($vencust['company']).'</span>';
	        } 
	        $str.= '<div class="d-inline-block align-middle">';
	       	$str.= '<p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;">'.$vencust['company'].' - '.$vencust['display_name'];
	       	$str.= '</p>';
	       	$str.= '<p class="f-10 mb-0">'.$vencust['email'].'</p>';
	       	$str.= '</div>';

	       	$str.= '<span class="f-10 mb-0 float-end text-primary cursor-pointer mr-5" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Click To Open Details" onclick="getEnquiryVendororCustomerPartyDetailspopup('.$vencust['id'].',\''.$_POST['searchvendorcustomertype'].'\')"><i class="ti-eye"></i> View</span>';

			$str.= '</div>'; 
		}
	}else{
		$str.= '<div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No '.ucwords($_POST['searchvendorcustomertype']).'</p>   
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>   
        </div>';
    }
	echo $str;
}
if($page=="insertclonetransactions"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertclonetransactions&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if ($page == 'getamcregistrationdetails') {
	$cudata = '';
	if ($_POST['id'] != '') {
		$url = API . "?mode=getamcregistrationdetails&method=get&id=" . $_POST['id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
}
if ($page == 'createschedule') {
	$cudata = '';
	if ($_POST['id'] != '') {
		$url = API . "?mode=createschedule&method=get&id=" . $_POST['id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
}
if ($page == 'updateamcserviceschedule') {
	$data = json_decode($_POST['sendData'], true); // Decode JSON data
	$table_name = $_POST['table_name'];
	$rid = $data['record_id'];

	$data['updated_date'] = date('Y-m-d H:i:s');
	$data['updated_by'] = $_SESSION['userid'];
	unset($data['record_id']);
	// Prepare the API URL
	$url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post";
	$response = file_post_contents($url, $data, 'update');
	echo json_encode($response);
}
if ($page == 'getamcservicedetails') {
	$cudata = '';
	if ($_POST['id'] != '') {
		$url = API . "?mode=getamcservicedetails&method=get&id=" . $_POST['id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
}
if($page=="getdisplayattributesfieldslist"){
	$option = "";
	$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'application_table_name'=>$_POST['table_name']);
	$wh1["field"] = "id,list_attributes";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$data1; 
	$pagenamesapi = callAPI($url1);
	$pagenamesrow = json_decode($pagenamesapi,1);
	$listfields = json_decode($pagenamesrow[0]['list_attributes'],1);
	foreach($listfields as $key=>$aray){
		$valuefield = $aray['lable']."-".$aray['type'];
		$option .= "<option value='".$key."'>".$valuefield."</option>";
	}
	echo json_encode(array('option'=>$option));
}
if($page == "checkapidatavalidateornot"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=checkapidatavalidateornot&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	$result = json_decode($response,1);
	//echo "<pre>"; print_r($result); echo "</pre>";
	$api_hits = $result['api_hits'];
	//$api_hits_response_details123 = json_decode($api_hits['api_response_details'],1); 
	$api_hits_response_details = $result['result'];
	
		//print_r($api_hits_response_details);
	$api_masters = $result['api_masters'];
	$api_code = $api_masters['api_code'];
	$api_masters_response_details = json_decode($api_masters['api_response_details'],1); 

	$result_cnt = count((array)$result['result']);
	$html_res = "";
	if(count((array)$result['result']) != 0){

		if($api_code == "SENDPAYMENTS" || $api_code == "RECEIVERECEIPTS"){




			$html_res .= '<table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" id="htmlajaxapihits"><tr><th></th><th>Transaction No.</th><th>Transaction Date</th><th class="text-right">Total</th><th >Required*</th><th>Status</th></tr>'; 
			foreach($result['result'] as $trans_id=>$tableslist){ 
					$arychunks = array_chunk($tableslist['Ledger_Entries'], 2);
					$recepttotalamt = 0;
					foreach($arychunks as $partydetailandtransdetail){
						$recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'];
					}	

				$trcolor = "";
				if($tableslist['response']['status'] != 1062){
					$trcolor = "bg-warning";
					$status_transaction_res = count((array)$tableslist['valuenotavailablefieldary']) == '0'?"Success":"Failed";
				}else{
					$trcolor="";
					$status_transaction_res =$tableslist['response']['error'];
				}	

				$html_res .= '<tr class="main '.$trcolor.'">';
				$html_res .= '<td class="cursor-pointer"><i class="ti-angle-double-down" onclick="opensubtable('.$trans_id.')"></i><input type="hidden" class="showclassval" id="showhide'.$trans_id.'" value="0"></td>';
				$html_res .= '<td>'.$tableslist['VOUCHERNUMBER'].'</td>';
				$html_res .= '<td>'.$tableslist['InvDate'].'</td>';
				$html_res .= '<td class="text-right">'.$recepttotalamt.'</td>';

				$summarydetails = "";
				$sq = 1;
				if(count((array)$tableslist['valuenotavailablefieldary']) !=0){
					foreach($tableslist['valuenotavailablefieldary'] as $fild=>$fval){
						if($fval !=""){
							$summarydetails .="<span class='d-block'>".$sq.") ".ucwords(str_replace('_',' ',$fild))." = ".$fval."</span>";
						}else{
							$summarydetails .="<span class='d-block'>".$sq.") ".ucwords(str_replace('_',' ',$fild))."</span>";
						}
						$sq++;
					}
					$html_res .= '<td>'.$summarydetails.'</td>';
				}else{
					$html_res .= '<td>-</td>';
				}
				$html_res .= '<td>'.$status_transaction_res.'</td>';

				$html_res .= '</tr>';
				$html_res .= '<tr class="data showclass'.$trans_id.' showclassdiv">';
				$html_res .= '<td colspan="20">';
				$html_res .= '<div class="row">';
				$html_res .= '<div class="col-lg-12">';
				$html_res .= '<table class="table table-td-6"><thead><tr><th>Transaction No.</th><th>Party_Name</th><th class="text-right">Total</th></tr></thead><tbody>';
				foreach($arychunks as $tableslist1){
					$html_res .= '<tr>';
					$html_res .= '<td class="w-b-w-s">'.$tableslist1[0]['Bill_Details']['Bill_NO'].'</td>';
					$html_res .= '<td class="w-b-w-s">'.$tableslist1[0]['Party_Name'].'</td>';
					$html_res .= '<td class="text-right">'.customeummarmonyformate($tableslist1[0]['AMOUNT']).'</td>';	
					$html_res .= '</tr>';
				} 
					$html_res .= '</tbody>';
					$html_res .= '</table>';
					$html_res .= '</div>';
					$html_res .= '</div>';
					$html_res .= '</td>';
					$html_res .= '</tr>';
				} 	
				$html_res .= '</table>';





		}elseif($api_code == "RECEIVEPURCHASEORDERS" || $api_code == "RECEIVEPURCHASEINVOICES" || $api_code == "RECEIVEGOODSRECEIPTNOTES" || $api_code == "RECEIVESALESORDERS" || $api_code == "RECEIVESALESINVOICES" || $api_code == "RECEIVEDELIVERYORDERS" || $api_code == "RECEIVEDEBITNOTES" || $api_code == "RECEIVECREDITNOTES"){
			
			$html_res .= '<table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc"><tr><th></th><th>Transaction No.</th><th>Transaction Date</th><th>Party Name</th><th class="text-right">Amount</th><th class="text-right">Tax</th><th class="text-right">Total</th><th >Required*</th><th>Status</th></tr>';

				foreach($result['result'] as $trans_id=>$tableslist){ 
					if($tableslist['Voucher_Amount']!=""){
						$invtotalamt = str_replace(",","",$tableslist['Voucher_Amount']);
					}
					$totaltax = 0;
					foreach($tableslist['Ledger_Entries'] as $ledgerentr){
						if($ledgerentr['Ledger_Name'] == "CGST" || $ledgerentr['Ledger_Name'] == "SGST" || $ledgerentr['Ledger_Name'] == "IGST"){
							$totaltax += $ledgerentr['AMOUNT'];
						}
					}
					$transaction = $tableslist['response']['transaction']; 
					$transaction_res = $tableslist['response']['transaction_res']; 
					$trcolor = "";
					if($tableslist['response']['status'] != 1062){
						$trcolor = "bg-warning";
						$status_transaction_res = count((array)$tableslist['valuenotavailablefieldary']) == '0'?"Success":"Failed";
					}else{
						$trcolor="";
						$status_transaction_res =$tableslist['response']['error'];
					}	 
					$html_res .= '<tr class="main '.$trcolor.'">';
					$html_res .= '<td class="cursor-pointer">
							<i class="ti-angle-double-down" onclick="opensubtable('.$trans_id.')"></i>
							<input type="hidden" class="showclassval" id="showhide'.$trans_id.'" value="0">
						</td>';
					$html_res .= '<td>'.$tableslist['VOUCHERNUMBER'].'</td>';
					$html_res .= '<td>'.$tableslist['InvDate'].'</td>';
				
					$html_res .= '<td class="'.$trcolor.'">'.$tableslist['PARTYNAME'].'</td>';
					
					$html_res .= '<td class="'.$trcolor.' text-right">'.customeummarmonyformate($invtotalamt-$totaltax).'</td>';
			
					$html_res .= '<td class="'.$trcolor.' text-right">'.customeummarmonyformate($totaltax).'</td>';
					
					$html_res .= '<td class="'.$trcolor.' text-right">'.customeummarmonyformate($invtotalamt).'</td>';
				
					$summarydetails = "";
					$sq = 1;
					if(count((array)$tableslist['valuenotavailablefieldary']) !=0){
						foreach($tableslist['valuenotavailablefieldary'] as $fild=>$fval){
							if($fval !=""){
								$summarydetails .="<span class='d-block'>".$sq.") ".ucwords(str_replace('_',' ',$fild))." = ".$fval."</span>";
							}else{
								$summarydetails .="<span class='d-block'>".$sq.") ".ucwords(str_replace('_',' ',$fild))."</span>";
							}
							$sq++;
						}
						$html_res .= '<td>'.$summarydetails.'</td>';
					}else{
						$html_res .= '<td>-</td>';
					}
					$html_res .= '<td class="text-right">'.$status_transaction_res.'</td>';
					$html_res .= '</tr>';
					
					$html_res .= '<tr class="data showclass'.$trans_id.' showclassdiv"><td colspan="20"><div class="row"><div class="col-lg-12"><table class="table table-td-6"><thead><tr><th>Item</th><th>SKU</th><th>Account No</th><th class="text-right">Quantity</th><th class="text-right">Price</th><th class="text-right">Amount</th><th class="text-right">Total</th></tr></thead><tbody>';
					foreach($tableslist['Item_Entries'] as $seqkey=>$tableslist1){
						$transactionitem = $tableslist['response']['transaction_item'][$seqkey]; 
						$html_res .= '<tr>';
						$html_res .= '<td class="w-b-w-s">'.$tableslist1['Stock_Item'].'</td>';
						$html_res .= '<td >'.$tableslist1['part_no'].'</td>';
						$html_res .= '<td >'.$transactionitem['account_code'].'</td>';
						$html_res .= '<td class="text-right">'.customeummarmonyformate($tableslist1['QUANTITY']).'</td>';
						$html_res .= '<td class="text-right">'.customeummarmonyformate($tableslist1['RATE']).'</td>';
						$html_res .= '<td class="text-right">'.customeummarmonyformate($tableslist1['AMOUNT']).'</td>';
						$html_res .= '<td class="text-right">'.$transactionitem['total_amount'].'</td>';
						$html_res .= '</tr>';
					} 
					$html_res .= '</tbody></table></div>';
					
					//$html_res .= '<div class="col-lg-3 col-md-3 col-sm-6 col-6"><div class="card" style="background: #f5f6f7;"><div class="row"><div class="col-lg-6 col-md-6 col-sm-6 col-6"><div class="card-header"><h5>Order Summary</h5></div></div></div><div class="card-body"><table class="table table-sm mb-0">';
					//$html_res .= '<tr><th>Sub Total </th><td align="right">'.($invtotalamt-$totaltax).'</td></tr>';
					//$html_res .= '<tr><th>Tax</th><td align="right">'.$totaltax.'</td></tr>';
					//$html_res .= '<tr class="bg-primary"><th>Total</th><td align="right"><span class="f-18">'.$invtotalamt.'</span></td></tr>';
					//$html_res .= '</table></div></div></div></div></td></tr>';
					$html_res .= '</div></td></tr>';
				} 	
				$html_res .= '</table>';

		}else{
			$html_res .= '<table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">';
			$html_res .= '<thead>';
			$html_res .= '<tr>';
			$html_res .= '<th>#</th>';
			foreach($api_masters_response_details as $keyfield=>$mappingfield){
				$html_res .= '<th>'.$keyfield.'</th>';
			} 
			$html_res .= '<th>Status</th>';
			$html_res .= '<th>Error</th>';
			$html_res .= '<th>Query</th>';
			$html_res .= '</tr>';
			$html_res .= '</thead>';
			$html_res .= '<tbody>';
			$im=1; 
			foreach($api_hits_response_details as $apijitrdetail){
				$html_res .= '<tr>';
				$html_res .= '<td class="p-2">'.$im.'</td>';
				foreach($api_masters_response_details as $keyfield=>$mappingfield){
					$html_res .= '<td class="p-2">'.$apijitrdetail[$keyfield].'</td>';
				}
				$errorstr = $apijitrdetail['response']['error'] !=""?$apijitrdetail['response']['error']:"-";
				$html_res .= '<td class="p-2"><span class="d-block">'.$apijitrdetail['response']['status'].'</span></td>';
				$html_res .= '<td class="p-2 w-b-w-s"><span class="d-block">'.$errorstr.'</span></td>';
				$html_res .= '<td class="p-2 w-b-w-s"><span class="d-block">'.$apijitrdetail['response']['query'].'</span></td>';
				$html_res .= '</tr>';
				$im++; 
			}
			$html_res .= '</tbody>';
			$html_res .= '</table>';
		}	




	}
	$returnary = array('result_cnt'=>$result_cnt,'html_res'=>$html_res,'result'=>$result);
	echo json_encode($returnary);
	//print_r($result);
}
if($page == "insertassetrequesttransfers"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertassetrequesttransfers&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	//$result = json_decode($response,1);
}


if ($page == 'insertCSR') {

    $fileUploadsPath = "uploads/csr/";
    $uploadedFiles = []; 
    $table_name = $_POST['table_name'];
	foreach ($_FILES as $key => $file) {
	    if ($file['error'] != UPLOAD_ERR_NO_FILE) { // Check if file is provided.
	        $temp = explode(".", $file['name']);
	        $uniqueFilename = "file_" . time() . "_" . uniqid() . "." . end($temp);
	        
	        // Move the file to the desired location.
	        if (move_uploaded_file($file['tmp_name'], $fileUploadsPath . $uniqueFilename)) {
	            $uploadedFiles[$key] = $uniqueFilename;
	        } else {
	            $uploadedFiles[$key] = null; // Indicate failure for this file.
	        }
	    } else {
	        $uploadedFiles[$key] = $_POST[$key.'_old']; // File not provided.
	    }
		unset($_POST[$key.'_old']);
	}

	// Add filenames to POST data for further processing if needed.
	foreach ($uploadedFiles as $key => $filename) {
	    $_POST[$key] = $filename ?: ""; // Use empty string if upload failed or file not provided.
	}

	unset($_POST['table_name'],$_POST['page'],$_POST['groups'],$_POST['payment_schedule_description'],$_POST['payment_schedule_percent'],$_POST['payment_schedule_amount'],$_POST['payment_schedule_deliverables'],$_POST['source_income'],$_POST['income'],$_POST['expenditure'],$_POST['assets'],$_POST['liabilities'],$_POST['project_description'],$_POST['focus_area'],$_POST['location'],$_POST['period'],$_POST['value'],$_POST['beneficiaries'],$_POST['partner_name'],$_POST['tracking_name'],$_POST['description'],$_POST['start_date'],$_POST['end_date'],$_POST['tracking_budget'],$_POST['payment_schedule_payment']);

	$data = $_POST;
	$data['current_status'] = '0';
	$data['status_date'] = nowDate();
	$data['created_date'] = date('Y-m-d H:i:s');
	$data['company_id'] = $_SESSION['company_id'];
	$data['created_by'] = $_SESSION['userid'];
	if ($table_name == 'temp_csr_proposal_data') {
		$data['orgininaor'] = $_SESSION['userid'];
	}

	$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
	$response = file_post_contents($url, $data, 'insert');
	echo json_encode($response);
}
if ($page == 'insertmasters') {
	$data = json_decode($_POST['sendData'], true);
	$table_name = $_POST['table_name'];
	$data['current_status'] = '0';
	$data['status_date'] = nowDate();
	$data['created_date'] = date('Y-m-d H:i:s');
	$data['company_id'] = $_SESSION['company_id'];
	$data['created_by'] = $_SESSION['userid'];
	$data['orgininaor'] = $_SESSION['userid'];
	$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
	$response = file_post_contents($url, $data, 'insert');
	echo json_encode($response);
}
if ($page == 'singlerowtabledata') {
	$cudata = '';
	$url = API . "?mode=singlerowtabledata&method=get&rid=" . $_POST['id'] . "&table=" . $_POST['table_name'] . "&fid=" . $_POST['fid'];
	$cudata = callAPI($url);

	echo $cudata;
}

if ($page == 'insertData') {
	// $url = API . "?mode=insertData&method=get&rid=" . $_POST['rid'];
	$url = API . "?mode=insertData&method=get&rid=" . $_POST['rid']."&old_id=".$_POST['old_id'];
	$response = callAPI($url);
	echo json_encode($response);
}
if ($page == 'updatemasters') {
	$data = json_decode($_POST['sendData'], true);
	$table_name = $_POST['table_name'];
	$rid = $data['id'];
	$fid = $data['fid'];
	$data['updated_date'] = date('Y-m-d H:i:s');
	$data['updated_by'] = $_SESSION['userid'];
	unset($data['id'], $data['fid']);
	$url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=" . $fid;
	$response = file_post_contents($url, $data, 'update');
	echo json_encode($response);
}
if($page=="insertdynamiccancelinvoice"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertdynamiccancelinvoice&method=post";
	$return = file_post_contents($url, $_POST);
	$res = json_decode($return,1);
	echo $return;
}

// MAP Point
if ($page == 'getFieldWorkDailyPlanData') {
	$manager_ids = $_POST['manager_id'] ?? [];
	$user_ids = $_POST['user_id'] ?? [];
	$from_date = $_POST['from_date'] ?? '';
	$to_date = $_POST['to_date'] ?? '';
	if (is_array($manager_ids)) {
	    $manager_ids_string = implode(',', $manager_ids);
	} else {
	    $manager_id_string = $manager_ids;
	}
	if (is_array($user_ids)) {
	    $user_ids_string = implode(',', $user_ids);
	} else {
	    $user_ids_string = $user_ids;
	}
	
	$cudata = '';
	$url = API . "?mode=getFieldWorkDailyPlanData&method=get&userids=".urlencode($user_ids_string)."&from_date=".$from_date. "&to_date=" . $to_date."&manager_ids=".urlencode($manager_ids_string);
	$cudata = callAPI($url);
	echo $cudata;
}
if ($page == 'fetchFieldWorkDailyPlans') {
	$table = $_POST['table'] ?? '';
	$userid = $_POST['user_id'] ?? '';
	$date = $_POST['date'] ?? '';
	$cudata = '';
	$url = API . "?mode=fetchFieldWorkDailyPlans&method=get&table=" . $table . "&userid=" . $userid . "&date=" . $date;
	$cudata = callAPI($url);
	echo $cudata;
}

if ($page == 'getFieldWorkUserMapPointsData') {
	$user_ids = $_POST['user_id'] ?? [];
	$from_date = $_POST['from_date'] ?? '';
	$to_date = $_POST['to_date'] ?? '';
	if (is_array($user_ids)) {
	    $user_ids_string = implode(',', $user_ids);
	} else {
	    $user_ids_string = $user_ids;
	}
	$cudata = '';
	$url = API . "?mode=getFieldWorkUserMapPointsData&method=get&userids=" . urlencode($user_ids_string) . "&from_date=" . $from_date . "&to_date=" . $to_date;
	$cudata = callAPI($url);
	echo $cudata;
}

if ($page == 'getMapPoints') {
	$userid = $_POST['user_id'] ?? [];
	$map_date = $_POST['map_date'] ?? '';
	$user_field_work_type = $_POST['user_field_work_type'] ?? '';
	$url = API . "?mode=getMapPoints&method=get&userid=" . $userid . "&map_date=" . $map_date . "&user_field_work_type=" . $user_field_work_type;
	$result = callAPI($url);
	echo $result;
}

if ($page == 'getEmployeeData') {
	$user_ids = $_POST['user_id'] ?? [];
	if (is_array($user_ids)) {
	    $user_ids_string = implode(',', $user_ids);
	} else {
	    $user_ids_string = $user_ids;
	}
	$cudata = '';
	$url = API . "?mode=getEmployeeData&method=get&userids=".urlencode($user_ids_string);
	$cudata = callAPI($url);
	echo $cudata;
}

if ($page == 'checkExistingData') {
	$url = API."?mode=checkExistingData&method=get&userid=".$_POST['user_id']."&date=".$_POST['date'];
	$response = callAPI($url);
	echo json_encode($response);
}

//Complaint
if ($page == 'gettableData') {
	$table_name = $_POST['table'];
	$id = $_POST['id'];
	$column = $_POST['column'];
	$cudata = '';
	$url = API . "?mode=gettableData&method=get&table=$table_name&id=$id&column=$column";
	$cudata = callAPI($url);
	echo $cudata;
}
if($page == "getbuildingoptionlist"){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'building_id'=> $_POST['building_id'],'is_active'=>'0');
	$data1["field"] = "id, floor_name";
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=building_floors&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value=''>All Building Floor</option>";
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['floor_name']."</option>";
	}
	echo $str;
}
if($page =="leadconverttocontacts"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=leadconverttocontacts&method=post";
	$response = file_post_contents($url, $_POST, 'Update');
	echo $response;
}
if($page =="margecompanywithcontact"){
	$crm_contact_id = $_POST['crm_contact_id'];
	$crm_company_id = $_POST['crm_company_id'];
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['crm_company_id'] = $crm_company_id;
	$url = API."?mode=updaterowtabledata&rid=".$crm_contact_id."&table=crm_contacts&method=post";
	echo $return = file_post_contents($url, $data, 'update');
}
if($page =="setdefaultcontactwithcompany"){
	$crm_contact_id = $_POST['crm_contact_id'];
	$crm_company_id = $_POST['crm_company_id'];
	$data['updated_date'] = nowDate();
	$data['updated_by'] = $_SESSION['userid'];
	$data['default_crm_contact_id'] = $crm_contact_id;
	$url = API."?mode=updaterowtabledata&rid=".$crm_company_id."&table=crm_companies&method=post";
	echo $return = file_post_contents($url, $data, 'update');
}


if($page == "getcrmcontactslist"){
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'crm_company_id'=> $_POST['crm_company_id'],'is_active'=>'0');
	$data1["field"] = "id, display_name";
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=crm_contacts&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$str = "<option value=''>Select Any One</option>";
	foreach ($cudata as  $data) {
		$str.= "<option value = '".$data['id']."'>".$data['display_name']."</option>";
	}
	echo $str;
}
if($page == "toaddupdateearningdata"){
	$table_name = "earnings";
	if(isset($_POST['submit'])){
		//echo "<pre>";print_r($_POST); exit;
		$_POST['payroll_calculation_type'] = $_POST['calculation_type'];
		$mandatory_str = explode(",",$_POST['mandatory_str']);
		if(isset($_POST['code_select']) && $_POST['code_select']!="" && $_POST['code_select']!="OT123"){
			$_POST['code'] = $_POST['code_select'];
		}
		foreach($mandatory_str as $mandatory){
			$data[$mandatory] = $_POST[$mandatory];
		}
		$formu['count'] = count((array)$_POST['lcode']);
		for($i=0;$i<count((array)$_POST['lcode']);$i++){
			$formul['lcode'] = $_POST['lcode'][$i];
			$formul['rcode'] = $_POST['rcode'][$i];
			$formul['loperand'] = $_POST['loperand'][$i];
			$formul['roperand'] = $_POST['roperand'][$i];
			$ff[] = $formul;
		}
		$formu['details'] = $ff;
		$data['company_id'] = $_SESSION['company_id'];
		$data['formula_details'] = json_encode($formu);
		$data['from_date'] = $_POST['from_date']!=""?date("Y-m-d", strtotime($_POST['from_date'])):date("Y-m-d");
		$data['to_date'] = $_POST['to_date']!=""?date("Y-m-d", strtotime($_POST['to_date'])):date("Y-m-d", strtotime('+55 years'));

		//print_r($data);
		if($_POST['submit'] == 'Add'){

			$data['current_status'] = $data['current_status']!="" ? $data['current_status'] : '0';
			$data['created_date'] = nowDate();
			$data['created_by'] = $_SESSION['userid'];
			$data['is_active'] = '0';
			$url = API."?mode=adddata&table=".$table_name."&method=post";
			$response = file_post_contents($url, $data, 'insert');
		}
		if($_POST['submit'] == 'Update'){
			$_SESSION['check'] = 1;
			$data['updated_date'] = nowDate();
			$data['updated_by'] = $_SESSION['userid'];
			$url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
			$response = file_post_contents($url, $data, 'update');
		}
	}
	$return = array('name'=> $_POST['name'], 'code'=>$_POST['code'],'ptype'=> $_POST['ptype'], 'prow'=>$_POST['prow'],'rid'=>$_POST['hid']);
	echo json_encode($return); exit;
}
if($page =="leadandcontactassigning"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = CRMAPI."?mode=leadandcontactassigning&method=post";
	$return = file_post_contents($url, $_POST);
	$res = json_decode($return,1);
	echo $return;
}
if($page == "cancelpayments"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=cancelpayments&method=post";
	$return = file_post_contents($url, $_POST);
	$res = json_decode($return,1);
	echo $return;
}
if($page ==  "assigneemdtaskschedule"){
	$employee_id = $_POST['emp_id'];
	$finance_tax_year = $_POST['finance_tax_year']; // 2022 means -> 01/04/2022 To 31/03/2023
	$url = API."?mode=getemtaskassignelist&employee_id=".$employee_id."&taskassign=Y&submit=Y&finance_tax_year=".$finance_tax_year; 
	$api = callAPI($url);
	$result = json_decode($api,1);
	print_r($result);
}
if($page=="assigntasktoemployeesmanagement"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=assigntasktoemployeesmanagement&method=post";
	$return = file_post_contents($url, $_POST);
	$res = json_decode($return,1);
	echo $return;
}
if($page == "updatesendverificationdocumentsem"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=updatesendverificationdocumentsem&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	// $url = API."?mode=updaterowtabledata&rid=".$transaction_id."&table=".$transaction_table."&method=post";
	// echo $return = file_post_contents($url, $data, 'update');
}
if($page=="selectadditionalcharge"){
    $curl = API."?mode=getsetupdetails&getsetupdetails=Y&additional_charge_id=".$_POST['additional_charge_id']; 
	echo $cudata = callAPI($curl);
    //$res = json_decode($cudata,1);
   	//echo  json_encode($res);
}
if($page=="submitextendagreement"){
    $curl = API."?mode=insertrentalinvoices&rid=".$_POST['rid']."&extend_agreement_month=".$_POST['extend_agreement_month']; 
	echo $cudata = callAPI($curl);
    //$res = json_decode($cudata,1);
   	//echo  json_encode($res);
}
if($page == 'dynamicchangestatusmonthprojecttimesheet'){
	
	$url = API."?mode=dynamicchangestatusmonthprojecttimesheet&method=post";
	//echo $url; print_r($sarray); exit;
	$response = file_post_contents($url, $_POST);
	echo $response; exit;
}
if($page=="getstockbalanceavailability"){
    $curl = API."?mode=getstockbalanceavailability&transaction_table=".$_POST['transaction_table']."&transaction_id=".$_POST['transaction_id']; 
	$cudata = callAPI($curl);
    echo $cudata;
    //$res = json_decode($cudata,1);
   	//echo  json_encode($res);
}


// Start Feasiblity Study Part added Kote - 25-06-2025
if($page == 'insertFeasibility'){
	$data = $_POST;
	unset($data['page']);
	unset($data['feasibility_check']);
	unset($data['transaction_table']);

	$datefieldarray = array('evaluation_date','start_date','end_date');
	foreach($data as $key=>$keyval){
		if(in_array($key,$datefieldarray)){
			$datesplit = str_replace('/"', '-',$keyval);
			$dateformat =  date('Y-m-d', strtotime($datesplit));
			$data[$key] = $dateformat;
		}
	}
	if (isset($data['rid']) && $data['rid'] != '') {
		$rid = $data['rid'];
		unset($data['rid']);
		$table_name = $data['table_name'];
		unset($data['table_name']);
		$fid = $data['fid'] ?? '';
		unset($data['fid']);
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $_SESSION['userid'];
		$url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=" . $fid;
		$response = file_post_contents($url, $data, 'update');
		echo $response;
	}else{
		$rid = '';
		unset($data['rid']);
		$table_name = $data['table_name'];
		unset($data['table_name']);
		$data['current_status'] = 0;
		$data['is_active'] = 0;
		$data['status_date'] = date('Y-m-d');
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['company_id'] = $_SESSION['company_id'];
		$data['created_by'] = $_SESSION['userid'];

		$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
		$response = file_post_contents($url, $data, 'insert');

		if($table_name == 'feasibility_requirements'){
			$updatadata = array();
			$transaction_table = $_POST['transaction_table'] !=""?$_POST['transaction_table']:"crm_contacts";
			$lead_id = $_POST['lead_id'];
			$updatadata['feasibility_check'] = "Y";
			$updatadata['updated_date'] = date('Y-m-d H:i:s');
			$updatadata['updated_by'] = $_SESSION['userid']; 
			$url2 = API . "?mode=updaterowtabledata&rid=" . $lead_id . "&table=" . $transaction_table . "&method=post";
			file_post_contents($url2, $updatadata, 'update');		
		}
		echo $response;
	}
	exit;
}if($page == 'getFeasibilityFields'){
	$cudata = '';
	if ($_POST['id'] != '') {
		$url = API . "?mode=getFeasibilityFields&rid=" . $_POST['id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}
if($page == 'insertFeasibilityBomAndChecklist'){
	$table_name = $_POST['table_name'];
	$url = API . "?mode=insertFeasibilityBomAndChecklist&table=" . $table_name . "&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	exit;

}
if($page == 'uploadFeasibilityFiles'){
	$uploadedFiles = [];
	$files = $_FILES;
	$table_name = $_POST['table_name'];
	$transaction_id = $_POST['feasibility_id'] ?? '';
    // Check if the $files array is structured as a single file or multiple files
    foreach ($files as $key => $fileArray) {
        // Handle single file uploads (non-array structure)
        if (!is_array($fileArray['name'])) {
            $fileArray['name'] = [$fileArray['name']];
            $fileArray['type'] = [$fileArray['type']];
            $fileArray['tmp_name'] = [$fileArray['tmp_name']];
            $fileArray['error'] = [$fileArray['error']];
            $fileArray['size'] = [$fileArray['size']];
        }
        // Process each file in the array
        foreach ($fileArray['name'] as $index => $fileName) {
            // Check for upload errors
            if ($fileArray['error'][$index] != UPLOAD_ERR_NO_FILE) {
                // Extract file details
                $actualFileName = $fileName;
                $temp = explode(".", $actualFileName);
                // $uniqueFilename = "file_" . time() . "_" . uniqid() . "." . end($temp);
                $randomNumber = mt_rand(10000, 99999);
                $microtime = microtime(true);
                $dateTime = date('Ymd_His', (int)$microtime) . sprintf('%03d', ($microtime - floor($microtime)) * 1000);
                $uniqueFilename = "file_{$randomNumber}_{$dateTime}." . end($temp);
                $fileSizeBytes = $fileArray['size'][$index];
                $fileSizeMB = round($fileSizeBytes / (1024 * 1024), 2);
                // Prepare upload directory
                $destinationPath = "uploads/" . $uniqueFilename;
                // Ensure the directory exists  
                if (!is_dir('uploads/')) {
                    mkdir('uploads/', 0777, true);
                }
                // Move uploaded file
                if (move_uploaded_file($fileArray['tmp_name'][$index], $destinationPath)) {
                    $data = [
                        'file_name' => $uniqueFilename,
                        'actual_file_name' => $actualFileName,
                        'file_size_mb' => $fileSizeMB,
                        'application_table_name' => $table_name,
                        'transaction_id' => $transaction_id,
                        'is_active' => '0',
                        'status_date' => date('Y-m-d'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $_SESSION['userid'],
                        'company_id' => $_SESSION['company_id']
                    ];

                    $url = API . "?mode=adddata&table=application_attachments&method=post";
                    $file_res = file_post_contents($url, $data, 'insert');
                    $uploadedFiles[] = [
						'file' => $actualFileName,
						'saved_as' => $uniqueFilename,
						'status' => $file_res['status'] ?? 'unknown'
					];
                }
            }
        }
    }
    echo json_encode(['status' => 'success', 'files' => $uploadedFiles]);
	exit;
}
if ($page == 'checkBOMReturns') {
	$cudata = '';
	if ($_POST['feasibility_id'] != '') {
		$url = API . "?mode=checkBOMReturns&feasibility_id=" . $_POST['feasibility_id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}
if ($page == 'closeFeasibility') {
		$cudata = '';
	if ($_POST['feasibility_id'] != '') {
		$url = API . "?mode=closeFeasibility&feasibility_id=" . $_POST['feasibility_id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}

// if ($page == 'updateReturnDate') {
//     $bom_id = $_POST['bom_id'] ?? 0;
//     $actual_return_by = $_POST['actual_return_by'] ?? '';
//     $response = ['status' => 'error', 'message' => 'Invalid request'];

// 	if ($bom_id > 0 && $actual_return_by != '') {
// 		$url = API . "?mode=updateReturnDate&bom_id=".$bom_id."&actual_return_by=".$actual_return_by;
// 		$response = callAPI($url);
// 	}
// 	echo $response;
// 	exit;
// }


if ($page == 'insertFeasibilityfeildslist') {
	$table_name = $_POST['table_name'];
	$url = API . "?mode=insertFeasibilityfeildslist&table=" . $table_name . "&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	exit;
}
if($page == 'getFeasibilityTableAndFields'){
	$cudata = '';
	if ($_POST['id'] != '') {
		$url = API . "?mode=getFeasibilityTableAndFields&rid=" . $_POST['id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}

if($page == 'reOpenFeasibility'){
	$cudata = '';
	if ($_POST['feasibility_id'] != '') {
		$url = API . "?mode=reOpenFeasibility&feasibility_id=" . $_POST['feasibility_id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}


if($page == "createsalesorderswithfeasibilty"){
	$url = API . "?mode=createsalesorderswithfeasibilty&feasibility_id=".$_POST['feasibility_id']."&crm_contact_id=".$_POST['crm_contact_id'];
	$cudata = callAPI($url);
	echo $cudata;
}
if($page == "checkcompanynameavilableornotincustomer"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=checkcompanynameavilableornotincustomer&method=post";
	$return = file_post_contents($url, $_POST);
	$result = json_decode($return,1);
	if($result['customers']['id'] !=""){
		echo json_encode(array("company_name"=>"Y",'allresult'=>$result));
	}else{
		echo json_encode(array("company_name"=>"N",'allresult'=>$result));
	}
}
// End Feasiblity Study Part added Kote - 25-06-2025
if($page == "submitemploymenttype"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=submitemploymenttype&method=post";
	//echo $url; print_r($_POST); 
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;exit;
}
if($page == "insertapplicationserialnumbers"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=insertapplicationserialnumbers&method=post";
	//echo $url; print_r($_POST); 
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;exit;
}
// Contracts given by Omkar
if ($_POST['page'] === 'insertrechargeplan') {
	$url = API . "?mode=insertcontractrechargedetails&method=post";
	$response = file_post_contents($url, $_POST);
	$result = json_decode($response, true);
	echo json_encode($result);
	exit;
}
if ($page == 'add_party') {
    $partyType = $_POST['party_type'] ?? '';
    $partyName = $_POST['party_name'] ?? '';

    if (empty($partyType) || empty($partyName)) {
        echo json_encode(['success' => false, 'message' => 'Party type and name are required.']);
        exit;
    }

    $partyData = [
        'party_name' => $_POST['party_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'company' => $_POST['company'],
        'address' => $_POST['address'],
        'bp_code' => $_POST['bp_code'],
    ];

    $mode = $partyType === "vendor" ? "insertVendorsreact" : "insertCustomersreact";
    $url = API . "?mode={$mode}&method=post";

    $response = file_post_contents($url, $partyData, 'insert');
    $result = json_decode($response, true);

	

    $isSuccess = false;
    if (is_array($result)) {
        $isSuccess = !empty($result['res']) || (isset($result['status']) && ($result['status'] === 'success' || $result['status'] === 'sucess'));
        $msg = $result['message'] ?? $result['msg'] ?? 'Operation successful';
    }

    echo json_encode(['success' => $isSuccess, 'result' => $result]);
    exit;
}


if ($page == 'load_parties') {
	$partyType = $_POST['party_type'] ?? '';
	if ($partyType == 'vendor') {
		$url = API . "?mode=getvendorslist&method=get";
	} else {
		$url = API . "?mode=getcustomerlist&method=get";
	}
	echo callAPI($url);
	exit;
}
if ($page == 'load_recharge_history') {
    $contract_id = $_POST['contract_id'];
    $url = API . "?mode=getcontractrechargedetails&method=get&contract_id=" . $contract_id . "&company_id=" . $company_id;
    $data = callAPI($url);
    // Convert to array if JSON string
    if (!is_array($data)) {
        $data = json_decode($data, true);
    }
    if (isset($data['result']) && is_array($data['result'])) {
        foreach ($data['result'] as &$row) {
            foreach (['activation_date', 'expiry_date', 'recharge_date'] as $field) {
                if (!empty($row[$field])) {
                    $row[$field] = fromDate($row[$field]);  // format using your function
                }
            }
        }
    }
    echo json_encode($data);
    exit;
}


if($page == 'tosavemrttingroom'){
	
	if (isset($_POST['submit']) && $_POST['submit'] == 'insert') {
		$table_name = 'meeting_room_bookings';
		unset($_POST['submit']);
		unset($_POST['page']);
		$_POST['request_date'] = date('Y-m-d');
		$_POST['user_id'] = $_SESSION['userid'];
		$_POST['current_status'] = '0';
		$_POST['is_active'] = '0';
		$_POST['status_date'] = date('Y-m-d H:i:s');
		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['created_date'] = date('Y-m-d H:i:s');
		$_POST['created_by'] = $_SESSION['userid'];
		$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
		//echo "<pre>".$url; print_r($_POST); echo "</pre>";
		$response = file_post_contents($url, $_POST, 'insert');
		$response = json_decode($response, 1);
		// print_r($response);
	}
	if (isset($_POST['submit']) && $_POST['submit'] == 'update') {
		$table_name = 'meeting_room_bookings';
		unset($_POST['submit']);
		unset($_POST['page']);
		$rid = $_POST['id'];
		unset($_POST['id']);
		$_POST['updated_date'] = date('Y-m-d H:i:s');
		$_POST['updated_by'] = $_SESSION['userid'];
		$url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
		$response = file_post_contents($url, $_POST, 'update');
		$response = json_decode($response, 1);
		// print_r($response);
	}
	echo $response; exit;
}
if($page == 'getDynamicCities'){
	$cudata = '';
	// return $_POST;
	if ($_POST['q'] != '') { 
		$text = urlencode($_POST['q']);
		$url = API . "?mode=getDynamicCities&method=get&q=".$text."&pagination=".$_POST['pagination'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}
if($page =="getTrevllingPlanData"){
	$journyid = $_POST['journyid'];
	$trip_id = $_POST['trip_id'];
	$trip_type = $_POST['trip_type'];
	$transaction_date = $_POST['transaction_date'];
	$url = API . "?mode=getTrevllingPlanData&method=get&id=".$trip_id."&journyid=".$journyid."&trip_type=".$trip_type."&transaction_date=".$transaction_date;
	$result = callAPI($url);
	$response = json_decode($result, 1);
	if($response['res'] !=""){ 
		echo json_encode(array("response"=>"Y",'limit'=>$response['res']['max_expense_limit'],'allresult'=>$response));
	}else{
		echo json_encode(array("response"=>"N",'limit'=>0,'allresult'=>$response));
	}
	//echo $result;
	//$result = json_decode($result, 1);
}
if( $page=='todisplayfloorbasedbuildid'){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'building_id'=>$_POST['build_id']);
	$wh1['field'] = "id,floor_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=building_floors&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['floor_name']."</option>"; 
	}
	echo $option;

}
if( $page=='todisplayspacebasedfloorid'){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'building_floor_id'=>$_POST['floor_id']);
	$wh1['field'] = "id,cabin_name";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=building_floor_spaces&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$option = '<option value="">Select Any One</option>';
	foreach($json as $locatio){
		$option .="<option value='".$locatio['id']."'>".$locatio['cabin_name']."</option>"; 
	}
	echo $option;

}
// added by koti
// if($page == 'getVisitorsByPhoneAndEmpCode'){
// 	$cudata = '';
// 	// return $_POST;
// 	if ($_POST['phone']!='' || $_POST['employee_code']!='') { 
// 		$url = API . "?mode=getVisitorsByPhoneAndEmpCode&method=get&phone=".$_POST['phone']."&employee_code=".$_POST['employee_code']."&location_id=".$_POST['location_id'];
// 		$cudata = callAPI($url);
// 	}
// 	echo $cudata;
// 	exit;
// }

// if($page == 'getVisitorsSearch'){
// 	$cudata = '';
// 	// return $_POST;
// 	if ($_POST['text'] !='' || $_POST['date']!='') { 
// 		$url = API . "?mode=getVisitorsSearch&method=get&text=".$_POST['text']."&date=".$_POST['date']."&location_id=".$_POST['location_id'];
// 		$cudata = callAPI($url);
// 	}
// 	echo $cudata;
// 	exit;
// }
// end add by koti
if($page == 'getdynamicinviceparty'){
	$url = API . "?mode=getdynamicinviceparty&method=get&transaction_table=".$_POST['transaction_table']."&party_id=".$_POST['party_id'];
	$cudata = callAPI($url);
	echo $cudata;
	exit;
}
if($page == "checkalreadyavailableornotcustomerorvendordetails"){
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API."?mode=checkalreadyavailableornotcustomerorvendordetails&method=post";
    $return = file_post_contents($url, $_POST);
    echo $return;
}
if($page =="checkitemcategoryaccountdata"){
	$url = API."?mode=checkitemcategoryaccountdata&method=post";
	echo $return = file_post_contents($url, $_POST, 'insert');
}
if($page=="checkiteminlinediscount"){
	$item_id = $_POST['item_id'];
	$total_amount = $_POST['total_amount'] !=""?$_POST['total_amount']:0;
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$item_id);
	$wh1['field'] = "id,minimum_discount";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=items&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	$resu = $json[0];
	if($resu['minimum_discount'] !=""){
		$minimum_discount_per = $resu['minimum_discount']!=""?$resu['minimum_discount']:0;
		$discount_amount = ($total_amount * $minimum_discount_per) / 100;
		$final_price = $total_amount - $discount_amount;
		echo json_encode(array('all_result'=>$resu,'max_discount'=>$final_price));
	}else{
		echo json_encode(array('all_result'=>$resu,'max_discount'=>0));
	}
}
if($page == "toupdateanoumentpolsdata"){

    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API."?mode=toaddalltypelikdidcomother&method=post";
    print_r($_POST); echo $url;
    $return = file_post_contents($url, $_POST);
    echo $return;
}
if($page == "getmultireferencepagedetails"){
    $url1 = API."?mode=getmultireferencepagedetails&gettype=".$_POST['gettype']."&value=".$_POST['value']."&reference_document_type=".$_POST['reference_document_type']."&party_id=".$_POST['party_id']."&transaction_id=".$_POST['transaction_id']."&multi_ref_transaction_id=".$_POST['multi_ref_transaction_id']."&multi_ref_transaction_table=".$_POST['multi_ref_transaction_table']."&document_type=".$_POST['document_type'];
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	$alreday_selected_transaction_id = $result['alreday_selected_transaction_id'];
	$alreday_selected_transaction_item_id = $result['alreday_selected_transaction_item_id'];
	$alreday_selected_transaction_item_tax_id = $result['alreday_selected_transaction_item_tax_id'];

	$alreday_selected_transaction_table = $result['alreday_selected_transaction_table'];
	$alreday_selected_transaction_party_id = $result['alreday_selected_transaction_party_id'];
	if($_POST['gettype'] == 'document_type'){
		//Document list
		$option = '<option value="">Select Any One</option>';
		foreach($result['dynamic_reference_tables'] as $drtres){ 
			$selectedlist = "";
			if($alreday_selected_transaction_table == $drtres['transaction_table']){
				$selectedlist = "selected";
			}
			$option .="<option value='".$drtres['transaction_table']."' ".$selectedlist.">".ucwords(str_replace('_',' ',$drtres['transaction_table']))."</option>"; 
		}
		//Party List
		$option1 = '<option value="">Select Any One</option>';
		foreach($result['vendor_or_customer_list'] as $drtres){
			$selectedlist = "";
			if($alreday_selected_transaction_party_id == $drtres['id']){
				$selectedlist = "selected";
			}
			$option1 .="<option value='".$drtres['id']."' ".$selectedlist.">".$drtres['company']." - ".ucwords(str_replace('_',' ',$drtres['display_name']))."</option>"; 
		}

		$return = json_encode(array('all_result'=>$result,'option'=>$option,'vendor_or_customer_list'=>$option1,'party_table'=>$result['party_table']));
	}elseif($_POST['gettype'] == 'transaction_list'){
		
		$iseq = 1;
		$tables = '<table class="table table-hover m-b-0"><thead><tr><th>#</th><th>Transaction No.</th><th>Amount</th></tr></thead><tbody>';
		if(count((array)$result['transaction_list']) != 0){
			foreach($result['transaction_list'] as $drtres){
				$checkeddlist = "";
				if(isset($alreday_selected_transaction_id[$drtres['id']])){
					$checkeddlist = "checked";
				}
				$tables .="<tr><td class='p-2'>
				<div class='checkbox-fade fade-in-primary'>
	              <label class='form-label'>
	                <input type='checkbox' class='transaction_lists' onchange='selectTransactions()' value='".$drtres['id']."' ".$checkeddlist.">
	                <span class='cr'>
	                  <i class='cr-icon icofont icofont-ui-check txt-primary'></i>
	                </span>
	                <span>".$iseq."</span>
	              </label>
	            </div></td>"; 
				$tables .="<td class='p-2'>".ucwords(str_replace('_',' ',$drtres['transaction_number']))." <span class='d-block'>".fromDate($drtres['transaction_date'])."</span></td>"; 
				$tables .="<td class='p-2'>".$drtres['total_amount']."</td><tr>"; 
				$iseq++;
			}
		}else{
			$tables .="<tr><td class='p-2 text-center' colspan='3'><i class='text-warning ti-alert'></i> No Transaction</td></tr>";
		}
		$tables .= '</tbody></table>';
		$return = json_encode(array('all_result'=>$result,'tables'=>$tables));
	}elseif($_POST['gettype'] == 'transaction_item_list'){


		$iseq = 1;
		//$tables = '<table class="table table-hover m-b-0"><thead><tr><th>#</th><th>Trans. No.</th><th>Item Name</th><th>Order (Pend) Qty</th><th>Price</th><th>Tax</th><th>Total</th><th>Invoice Qty</th><th>Tax</th><th>Total</th></tr></thead><tbody>';
		$tables = '<table class="table table-hover m-b-0"><thead><tr>';
		$tables .= "<th><div class='checkbox-fade fade-in-primary'>
                      <label class='form-label'>
                        <input type='checkbox' class='check_all_check_box' onchange='selectAllTransactionsItems()'>
                        <span class='cr'>
                          <i class='cr-icon icofont icofont-ui-check txt-primary'></i>
                        </span>
                        <span>All</span>
                      </label>
                    </div></th>";
		$tables .= '<th>Trans. No.</th><th>Item Name</th><th>Order (Pend) Qty</th><th>Price</th><th>Tax</th><th>Total</th></tr></thead><tbody>';
		foreach($result['transaction_item_list'] as $drtres){
			$checkeddlist = "";
			if(isset($alreday_selected_transaction_item_id[$drtres['id']])){
				$checkeddlist = "checked";
			}
			$tables .="<tr>";
			$tables .="<td class='p-2'>
			<input type='hidden' name='old_transaction_item_table_id[".$drtres['id']."]'  value='".$alreday_selected_transaction_item_id[$drtres['id']]."'>
			<input type='hidden' name='old_transaction_item_tax_table_id[".$drtres['id']."]'  value='".$alreday_selected_transaction_item_tax_id[$drtres['id']]."'>
			

			<div class='checkbox-fade fade-in-primary'>
                      <label class='form-label'>
                        <input type='checkbox' class='check_transaction_item_list' value='".$drtres['id']."'  onchange='selectTransactionsItems()' name='transaction_item_list[]' ".$checkeddlist.">
                        <span class='cr'>
                          <i class='cr-icon icofont icofont-ui-check txt-primary'></i>
                        </span>
                        <span>".$iseq."</span>
                      </label>
                    </div></td>"; 
			$tables .="<td class='p-2'>".$drtres['transaction_number']."</td>"; 
			$tables .="<td class='w-b-w-s p-2'>".ucwords($drtres['item_name'])."</td>"; 
			$tables .="<td class='p-2'>".$drtres['item_quantity']." <span class='text-danger'>(".$drtres['pending_qty'].")</span> <input type='hidden' id='pending_qty_".$drtres['id']."'  value='".$drtres['pending_qty']."'><input type='hidden' id='item_quantity_".$drtres['id']."' value='".$drtres['item_quantity']."'></td>";
			
			$tables .="<td class='p-2'>".customeummarmonyformate($drtres['item_price'])."<input type='hidden' id='item_price_".$drtres['id']."'  value='".$drtres['item_price']."'></td>"; 
			
			$tables .="<td class='p-2'>".customeummarmonyformate($drtres['tax_amount']);
			if($drtres['tax_value'] !=""){
				$tables .= "<span class='d-block f-10'>".$drtres['tax_name']." - ".$drtres['tax_value']."%</span>";
			}
			$tables .= "<input type='hidden' id='tax_amount_".$drtres['id']."'  value='".$drtres['tax_amount']."'>";
			$tables .= "<input type='hidden' id='tax_value_".$drtres['id']."'  value='".$drtres['tax_value']."'>";
			$tables .="</td>";
			
			$tables .="<td class='p-2'>".customeummarmonyformate($drtres['total_amount'])."<input type='hidden' id='total_amount_".$drtres['id']."'  value='".$drtres['total_amount']."'></td>";
			
			//$tables .="<td class='p-2'>";
			//$tables .="<input type='number' class='form-control' id='invoice_qty_".$drtres['id']."' name='invoice_qty[]' onblur='allCalculation()' max='".$drtres['pending_qty']."' readonly>";
			//$tables .="<span class='d-block removeerrormsg text-danger' id='invoice_qty_error_".$drtres['id']."'></span>";
			//$tables .="</td>";
			
			//$tables .="<td class='p-2'><span id='label_invoice_tax_".$drtres['id']."'>".customeummarmonyformate(0)."</span></td>";
			//$tables .="<td class='p-2'><span id='label_invoice_total_".$drtres['id']."'>".customeummarmonyformate(0)."</span></td>";
			$tables .="<tr>"; 
			$iseq++;
		}
		$tables .= '</tbody></table>';
		$return = json_encode(array('all_result'=>$result,'tables'=>$tables));
	}

    echo $return;
}
if($page == "submitmultireferencedocumnets"){
	$_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API."?mode=submitmultireferencedocumnets&method=post";
    $callapi = file_post_contents($url, $_POST);
    $result = json_decode($callapi,1);
    $nrid = "id:".$result['transaction_id'];
    $return = json_encode(array('all_result'=>$result,'transaction_result'=>$result['insert_data'],'rid'=>deocdeId($nrid)));
    echo $return;
}
// added by koti CSR start

if ($page == 'save_po_details') {
	$return = array();
	$table_name = $_POST['table_name'];
	$tracking_id = $_POST['tracking_id'] ?? '';
	$project_id = $_POST['project_id'] ?? '';
	$poNos = $_POST['po_no'] ?? [];
	foreach ($poNos as $i => $poNo) {
        $id = $_POST['id'][$i] ?? '';
		$data_array = array(
			'tracking_id' => $tracking_id,
			'project_id' => $project_id,
			'financial_year' => $_POST['financial_year'][$i] ?? '',
			'po_no' => $poNos[$i] ?? '',
			'po_date' => isset($_POST['po_date'][$i]) && $_POST['po_date'][$i] != '' ? date('Y-m-d', strtotime($_POST['po_date'][$i])) : '',
			'po_amount' => $_POST['po_amount'][$i] ?? '',
			'po_value_ex' => $_POST['po_value_ex'][$i] ?? '',
			'gst' => $_POST['gst'][$i] ?? '',
			'po_value_in' => $_POST['po_value_in'][$i] ?? '',
			'business_code' => $_POST['business_code'][$i] ?? '',
			'business_name' => $_POST['business_name'][$i] ?? '',
			'po_details' => $_POST['po_details'][$i] ?? '',
			'remarks' => $_POST['remarks'][$i] ?? ''
		);
		
		$data = array_map('trim', $data_array);
		 // Skip empty rows
		if (empty(array_filter($data))) {
			continue;
		}
		if (!empty($id)) {
			$rid = $id;
			$fid = 'id';
			$data['updated_date'] = date('Y-m-d H:i:s');
			$data['updated_by'] = $_SESSION['userid'];
			$url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=" . $fid;
			$response = file_post_contents($url, $data, 'update');
			$return[] = $response;				
		} else {
			$data['current_status'] = '0';
			$data['is_active'] = '0';
			$data['status_date'] = date('Y-m-d H:i:s');
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['company_id'] = $_SESSION['company_id'];
			$data['created_by'] = $_SESSION['userid'];
			$data['originator'] = $_SESSION['userid'];
			// ➕ Insert
			$url = API . "?mode=adddata&table=" . $table_name . "&method=post";
			$response = file_post_contents($url, $data, 'insert');
			$return[] = $response;
		}
	}
	echo json_encode($return);
}

if ($page == 'save_invoice_details') {
    $return = array();
    $table_name = $_POST['table_name'];
    $tracking_id = $_POST['tracking_id'] ?? '';
    $project_id = $_POST['project_id'] ?? '';
    $invNos = $_POST['inv_no'] ?? [];
    foreach ($invNos as $i => $invNo) {
        $id = $_POST['id'][$i] ?? '';
        $data_array = array(
            'tracking_id'    => $tracking_id,
            'project_id'     => $project_id,
            'financial_year' => $_POST['financial_year'][$i] ?? '',
            'inv_no'         => $_POST['inv_no'][$i] ?? '',
            'csr_po_details_id'  => $_POST['csr_po_details_id'][$i] ?? '',
            'inv_date'       => !empty($_POST['inv_date'][$i]) ? date('Y-m-d', strtotime($_POST['inv_date'][$i])) : '',
            'paid_date'      => !empty($_POST['paid_date'][$i]) ? date('Y-m-d', strtotime($_POST['paid_date'][$i])) : '',
            'paid_amount'    => $_POST['paid_amount'][$i] ?? '',
            'paid_bank'      => $_POST['paid_bank'][$i] ?? '',
            'taxable_value'  => $_POST['taxable_value'][$i] ?? '',
            'gst_percent'    => $_POST['gst_percent'][$i] ?? '',
            'gst_amount'     => $_POST['gst_amount'][$i] ?? '',
            'total_spent'    => $_POST['total_spent'][$i] ?? '',
            'tds_deducted'   => $_POST['tds_deducted'][$i] ?? '',
            'net_payable'    => $_POST['net_payable'][$i] ?? ''
        );
        $data = array_map('trim', $data_array);
        // Skip empty row
        if (empty(array_filter($data))) {
            continue;
        }
        if (!empty($id)) {
            // 🔁 Update
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $_SESSION['userid'];
            $url = API . "?mode=updaterowtabledata&rid=$id&table=$table_name&method=post&fid=id";
            $response = file_post_contents($url, $data, 'update');
        } else {
            // ➕ Insert
            $data['current_status'] = '0';
            $data['is_active'] = '0';
            $data['status_date'] = date('Y-m-d H:i:s');
            $data['created_date'] = date('Y-m-d H:i:s');
            $data['company_id'] = $_SESSION['company_id'];
            $data['created_by'] = $_SESSION['userid'];
            $data['originator'] = $_SESSION['userid'];
            $url = API . "?mode=adddata&table=$table_name&method=post";
            $response = file_post_contents($url, $data, 'insert');
        }
        $return[] = $response;
    }
    echo json_encode($return);
}
// added by koti CSR end
//omkar added with documents verifications
if ($page == "insertvendorsandvendorregisters") {
	$data['company_id'] = $_SESSION['company_id'];
	$data['created_date'] = date('Y-m-d H:i:s');
	$data['created_by'] = $_SESSION['userid'] ?? '';
	$data['user_id'] = $_SESSION['userid'] ?? '';
	$data['is_active'] = 0;
	$data['current_status'] = 0;
	if ($_POST['verification_type'] == "PAN") { 
		$data['company'] = trim($_POST['company_name']);
		$data['email'] = trim($_POST['company_email']);
		$data['display_name'] = trim($_POST['display_name']);
		$data['pan_no'] = trim($_POST['pan_no']);
		$data['transaction_table'] = trim($_POST['transaction_table']);
		$data['transaction_id'] = trim($_POST['vendor_id']);
		$name = explode(' ', $_POST['display_name']);
		$firstname = '';
		$middlename = '';
		$lastname = '';
		$count = count($name);
		if ($count == 1) {
			$firstname = $name[0];
		} elseif ($count == 2) {
			$firstname = $name[0];
			$lastname = $name[1];
		} elseif ($count >= 3) {
			$firstname = $name[0];
			$lastname = array_pop($name);
			$middlename = implode(' ', array_slice($name, 1));
		}
		$data['first_name'] = $firstname;
		$data['middle_name'] = $middlename;
		$data['last_name'] = $lastname;
		$data['vendor_verifications'] = json_encode(array("PAN" => array("status" => "N"), "GSTIN" => array("status" => "N"), "TAN" => array("status" => "N"), "BANK" => array("status" => "N"), "MSME" => array("status" => "N")));;
		$data['verification_type'] = $_POST['verification_type'];
		$data['api_hit_id'] = $_POST['api_hit_id'];
		$url = API . "?mode=insertupdatevendorverificationdetails&method=post";
		$res = file_post_contents($url, $data, 'insert');
		$result = json_decode($res, true);
		$nrid = "id:" . $result['LASTID']; 
		$return = json_encode(array('all_result' => $result, 'rid' => deocdeId($nrid)));
	} elseif($_POST['verification_type'] == "GSTIN"){
		 
		$verrifyary['user_id'] = $_SESSION['userid'];
		$verrifyary['company_id'] = $_SESSION['company_id'];
		$verrifyary['transaction_table'] = $_POST['transaction_table'];
		$verrifyary['address'] = $_POST['address'];
		$verrifyary['transaction_id'] = $_POST['vendor_id'];
		$verrifyary['verification_type'] = $_POST['verification_type'];
		$verrifyary['api_hit_id'] = $_POST['api_hit_id'];
		$verrifyary['gstin'] = $_POST['gstin'];
		$verrifyary['address'] = $_POST['address'];
		$verrifyary['post_code'] = $_POST['post_code'];
		$verrifyary['state'] = $_POST['state'];
		$verrifyary['city'] = $_POST['city'];
		$verrifyary['location'] = $_POST['location'];
		$url = API . "?mode=insertupdatevendorverificationdetails&method=post";
		$verificatiores = file_post_contents($url, $verrifyary, 'insert');
		$verificatioresult = json_decode($verificatiores, true);
		// Add success status for locking mechanism
		if ($verificatioresult && isset($verificatioresult['LASTID'])) {
			$verificatioresult['status'] = 'success';
		}
		 
		$return = json_encode(array('result' => $verificatioresult));
	} elseif($_POST['verification_type'] == "MSME"){
		 
		$verrifyary['user_id'] = $_SESSION['userid'];
		$verrifyary['company_id'] = $_SESSION['company_id'];
		$verrifyary['transaction_table'] = $_POST['transaction_table'];
		$verrifyary['transaction_id'] = $_POST['vendor_id'];
		$verrifyary['verification_type'] = $_POST['verification_type'];
		$verrifyary['api_hit_id'] = $_POST['api_hit_id'];
		$verrifyary['msme_number'] = $_POST['msme_number'];
		$url = API . "?mode=insertupdatevendorverificationdetails&method=post";
		$verificatiores = file_post_contents($url, $verrifyary, 'insert');
		$verificatioresult = json_decode($verificatiores, true);
		// Add success status for locking mechanism
		if ($verificatioresult && isset($verificatioresult['LASTID'])) {
			$verificatioresult['status'] = 'success';
		}
		$return = json_encode(array('result' => $verificatioresult));
	}elseif($_POST['verification_type'] == "TAN"){
		 
		$verrifyary['user_id'] = $_SESSION['userid'];
		$verrifyary['company_id'] = $_SESSION['company_id'];
		$verrifyary['transaction_table'] = $_POST['transaction_table'];
		$verrifyary['transaction_id'] = $_POST['vendor_id'];
		$verrifyary['verification_type'] = $_POST['verification_type'];
		$verrifyary['api_hit_id'] = $_POST['api_hit_id'];
		$verrifyary['tan_number'] = $_POST['tan_number'];
		$url = API . "?mode=insertupdatevendorverificationdetails&method=post";
		$verificatiores = file_post_contents($url, $verrifyary, 'insert');
		$verificatioresult = json_decode($verificatiores, true);
		// Add success status for locking mechanism
		if ($verificatioresult && isset($verificatioresult['LASTID'])) {
			$verificatioresult['status'] = 'success';
		}
		$return = json_encode(array('result' => $verificatioresult));
	}elseif($_POST['verification_type'] == "BANK"){
		 
		$verrifyary['user_id'] = $_SESSION['userid'];
		$verrifyary['company_id'] = $_SESSION['company_id'];
		$verrifyary['transaction_table'] = $_POST['transaction_table'];
		$verrifyary['transaction_id'] = $_POST['vendor_id'];
		$verrifyary['verification_type'] = $_POST['verification_type'];
		$verrifyary['api_hit_id'] = $_POST['api_hit_id'];
		$verrifyary['ifsc_code'] = $_POST['ifsc_code'];
		$verrifyary['bank_account_no'] = $_POST['bank_account_no'];
		$url = API . "?mode=insertupdatevendorverificationdetails&method=post";
		$verificatiores = file_post_contents($url, $verrifyary, 'insert');
		$verificatioresult = json_decode($verificatiores, true);
		// Add success status for locking mechanism
		if ($verificatioresult && isset($verificatioresult['LASTID'])) {
			$verificatioresult['status'] = 'success';
		}
		$return = json_encode(array('result' => $verificatioresult));
	}
	echo $return;
}

//End omkar added with documents verifications

if ($page == 'insertqrgeneration') {

    $table_name = $_POST['table_name'];
    $rid = $_POST['record_id'] ?? '';
    // 1. Save the uploaded QR image
    $qr_filename = imageupload('all', 'qr_file', '', 'qr');
    // 2. Prepare data for insert
    unset(
        $_POST['table_name'],$_POST['page'],$_POST['record_id']
    );
    $data = $_POST;
    $data['qr_code']    = $qr_filename;
    // print_r($data); exit;
    if($rid == ''){
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        $data['status_date']    = date('Y-m-d H:i:s');
        $data['created_date']   = date('Y-m-d H:i:s');
        $data['company_id']     = $_SESSION['company_id'];
        $data['created_by']     = $_SESSION['userid'];
        // 3. Insert using your common API
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
    }else{
        $fid = 'id';
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post&fid=" . $fid;
        $response = file_post_contents($url, $data, 'update');
    }
    // 4. Return response
    echo $response;
    exit;
 
}
if($page == "insertvendorandvendorregisterssynchapi"){
	$url = API . "?mode=insertvendorandvendorregisterssynchapi";
	$cudata = callAPI($url);
	echo $cudata;
}
if($page == 'getmanufacturingboms'){
	$url = API."?mode=getmanufacturingboms&item_id=".$_POST['item_id']."&manufacturing_operation_id=".$_POST['manufacturing_operation_id']."&gettype=".$_POST['gettype']; 
	$response = callAPI($url); 
	$response1 = json_decode($response,1);

	if($_POST['gettype'] == "SELECTMANUFACTURINGOPERATION"){
		$htmloption = '<option value="">Select Any One</option>';
		foreach($response1['SELECTMANUFACTURINGOPERATION_MACHINE'] as $machinlist){
			$htmloption .="<option value='".$machinlist['id']."'>".$machinlist['operation_name']."</option>";
		}
		$response1['SELECT_OPTIONS'] = $htmloption;
	}
	if($_POST['gettype'] == "SELECTBOMITEMID"){
		$htmloption = '<option value="">Select Any One</option>';
		foreach($response1['alternate_items'] as $machinlist){
			$htmloption .="<option value='".$machinlist['id']."'>".$machinlist['item_name']."</option>";
		}
		$response1['alternate_items_options'] = $htmloption;
	}
	
	echo json_encode($response1); exit;
}
if($page == 'getmanufacturingworkorders'){
	$url = API."?mode=getmanufacturingworkorders&manufacturing_firm_order_id=".$_POST['manufacturing_firm_order_id']."&work_order_item_id=".$_POST['work_order_item_id']."&gettype=".$_POST['gettype']; 
	$response = callAPI($url); 
	//$response = json_decode($response,1);
	echo $response; exit;
}
if ($page == 'gettablestructure') {
    $table_name = $_POST['table_name'];
    $data_name = $_SESSION['DB_NAME'] ?? 'infisuite';
    $url = API . "?mode=filednames&table=" . $table_name . "&data_name=" . $data_name;
    $response = callAPI($url);
    echo $response;
    exit;
}
if ($page == 'getfulltabledetails') {
	//print_r($_POST);
    $table_name = $_POST['table_name'];
    $field_mappings = $_POST['field_mappings'];
    //$url = API . "?mode=getfulltabledetails&method=get&table_name=" . $table_name . "&field_mappings=" . $field_mappings;
    //print_r($_POST);
    $url = API . "?mode=getfulltabledetails&method=get&process_id=".$_POST['process_id']."&table_name=" . $table_name ;
    //echo $url;
    $data = callAPI($url);
   echo $data;
    // Check if data is already JSON string, if so just echo it directly
    /*if (is_string($data) && (strpos($data, '{') === 0 || strpos($data, '[') === 0)) {
        echo $data;
    } else {
        echo json_encode($data);
    }*/
    exit;
}
// gateway code Start

if ($page == 'get_personal_entry_details') {
    if (empty($_POST['entry_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Entry ID is required.']);
        exit;
    }
    try {
        $entry_id = intval($_POST['entry_id']);
        $url = API . "?mode=singlerowtabledata&table=transport_personal_gateway&fid=id&rid=" . $entry_id;
        $response = callAPI($url);
        $entryData = json_decode($response, true);

        if ($entryData && !empty($entryData)) {
            $personData = [];
            if (!empty($entryData['person_details'])) {
                $personData = json_decode($entryData['person_details'], true) ?: [];
            }
            
            $formattedData = [
                'employee_name' => $personData['employee_name'] ?? 'N/A',
                'employee_contact' => $personData['employee_contact'] ?? 'N/A',
                'employee_email' => $personData['employee_email'] ?? 'N/A',
                'visit_purpose' => ucfirst($entryData['visit_purpose'] ?? 'N/A'),
                'vehicle_number' => $entryData['vehicle_number'] ?? 'N/A',
                'location' => $entryData['location'] ?? 'N/A',
                'entry_date' => $entryData['entry_date'] ?? date('Y-m-d'),
                'entry_time' => $entryData['entry_time'] ?? date('H:i'),
                'exit_time' => $entryData['exit_time'] ?? null,
                'notes' => $entryData['notes'] ?? ''
            ];

            echo json_encode([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Entry not found.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch entry details: ' . $e->getMessage()
        ]);
    }
    exit;
}
if ($page == 'get_current_persons') {
    try {
        $company_id = $_SESSION['company_id'];
        $employee_id = $_SESSION['userid'];
        $work_admin_type = $_POST['work_admin_type'] ?? 0;
        $url = API . "?mode=getpersonalgateway&method=get&company_id=" . $company_id . "&work_admin_type=" . $work_admin_type . "&employee_id=" . $employee_id;
        //echo $url;
        $response = callAPI($url);
        $data = json_decode($response, true);
       
        echo json_encode([
            'status' => 'success',
            'data' => $data['current_persons'] ?? []
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch current persons.'
        ]);
    }
    exit;
}
if ($page == 'get_personal_statistics') {
    try {
        $company_id = $_SESSION['company_id'];
        $employee_id = $_SESSION['userid'];
        $work_admin_type = $_POST['work_admin_type'] ?? 1;
        $url = API . "?mode=getpersonalgateway&method=get&company_id=" . $company_id . "&work_admin_type=" . $work_admin_type . "&employee_id=" . $employee_id;
        $response = callAPI($url);
        $data = json_decode($response, true);
       
        echo json_encode([
            'status' => 'success',
            'data' => $data['statistics'] ?? [
                'current_count' => 0,
                'today_entries' => 0,
                'today_exits' => 0,
                'avg_duration_minutes' => 0
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch statistics.'
        ]);
    }
    exit;
}
if ($page == 'get_personal_recent_activity') {
    try {
        $company_id = $_SESSION['company_id'];
        $employee_id = $_SESSION['userid'];
        $url = API . "?mode=getrecentpersonalactivity&method=get&company_id=" . $company_id . "&limit=10&work_admin_type=".$_POST['work_admin_type']."&employee_id=" . $employee_id;
        //echo $url;
        $response = callAPI($url);
        $data = json_decode($response, true);
        
        echo json_encode([
            'status' => 'success',
            'data' => $data['data'] ?? []
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch recent activity.'
        ]);
    }
    exit;
}
if ($page == 'insert_personal_entry') {
    if (empty($_POST['employee_id']) || empty($_POST['visit_purpose']) || empty($_POST['location'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['created_by'] = $_SESSION['userid'];
    $_POST['created_date'] = date('Y-m-d H:i:s');
    $_POST['is_active'] = 0;
     
    $method_type = isset($_POST['method_type']) && $_POST['method_type'] !== '' ? $_POST['method_type'] : 'entry_first';
    $_POST['method_type'] = $method_type;
    $_POST['entry_date'] = $_POST['entry_date'] ?? date('Y-m-d'); 
    $time_value = $_POST['in_time'] ?? date('H:i:s'); 
    if ($method_type === 'exit_first') { 
        $_POST['exit_time'] = $time_value;
        unset($_POST['entry_time']); 
        $_POST['current_status'] = 'outside';
    } else { 
        $_POST['entry_time'] = $time_value;
        unset($_POST['exit_time']);  
        $_POST['current_status'] = 'inside';
    } 
    $rid = isset($_POST['entry_id']) && !empty($_POST['entry_id']) ? $_POST['entry_id'] : '';
    $url = API . "?mode=insertpersonalgateway&method=post&rid=" . $rid;
    $response = file_post_contents($url, $_POST, 'insert');
    echo $response;
    exit;
}
if ($page == 'update_personal_exit_old') {
    if (empty($_POST['entry_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Entry ID is required.']);
        exit;
    }
    
    $method_type = $_POST['method_type'] ?? 'entry_first';
    $_POST['updated_by'] = $_SESSION['userid'];
    $_POST['updated_date'] = date('Y-m-d H:i:s');
    $_POST['user_id'] = $_SESSION['userid'];
    
    $time_value = $_POST['exit_time'] ?? date('H:i:s');
    $date_value = $_POST['exit_date'] ?? date('Y-m-d');
    
    if ($method_type === 'exit_first') {
        $_POST['entry_time'] = $time_value;
        $_POST['entry_date'] = $date_value;
        $_POST['entry_timestamp'] = date('Y-m-d H:i:s');
        $_POST['current_status'] = 'completed';
    } else {
        $_POST['exit_time'] = $time_value;
        $_POST['exit_date'] = $date_value;
        $_POST['exit_timestamp'] = date('Y-m-d H:i:s');
        $_POST['current_status'] = 'completed';
    }
    
    $url = API . "?mode=updatepersonalexit&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    echo $response;
    exit;
}
if ($page == 'get_vehicle_details') {
	if (empty($_POST['vehicle_id'])) {
		echo json_encode([
			'status' => 'error',
			'message' => 'Vehicle ID is required.'
		]);
		exit;
	}
	try {
		$vehicle_id = intval($_POST['vehicle_id']);
		$company_id = $_SESSION['company_id'];

		$url = API . "?mode=singlerowtabledata&table=transport_gateway&fid=id&rid=" . $vehicle_id;
		$response = callAPI($url);
		error_log("AJAX: API response for vehicle details: " . $response);
		// echo $response;
		$vehicleData = json_decode($response, true);
		$emp_ids_array = !empty($vehicleData['emp_ids']) ? explode(',', $vehicleData['emp_ids']) : [];
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
		$wh1["field"] = "id,display_name";
		$wh1["whr_in_key"] = "id";
		$wh1["where_in"] = $emp_ids_array;
		$wh1["field"] = "id,display_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=employees&data=".$data1; 
		$jsondecode = callAPI($url1);
		$emp_list = json_decode($jsondecode,1) ?? array();

		if ($vehicleData && !empty($vehicleData)) {
			$formattedData = ['vehicle_number' => $vehicleData['vehicle_number'] ?? '', 'vehicle_type' => ucfirst($vehicleData['vehicle_type'] ?? ''), 'driver_name' => $vehicleData['driver_name'] ?? '', 'driver_contact' => $vehicleData['driver_contact'] ?? '', 'person_details' => $vehicleData['person_details'] ?? '', 'visit_purpose' => ucfirst($vehicleData['visit_purpose'] ?? ''), 'visiting_person' => $vehicleData['visiting_person'] ?? '', 'company_name' => $vehicleData['company_name'] ?? '', 'location' => $vehicleData['location'] ?? 'N/A', 'entry_date' => $vehicleData['entry_date'] ?? date('Y-m-d'), 'entry_time' => $vehicleData['entry_time'] ?? date('H:i'), 'exit_time' => $vehicleData['exit_time'] ?? null, 'has_goods' => !empty($vehicleData['has_goods']) && $vehicleData['has_goods'] == 1, 'goods_description' => $vehicleData['goods_description'] ?? '', 'notes' => $vehicleData['notes'] ?? '', 'vehicle_entry_reading' => $vehicleData['vehicle_entry_reading'] ?? '', 'vehicle_exit_reading' => $vehicleData['vehicle_exit_reading'] ?? '', 'other_location' => $vehicleData['other_location'] ?? '', 'emp_list' => $emp_list];
			echo json_encode([
				'status' => 'success',
				'data' => $formattedData,

			]);
		}
	} catch (Exception $e) {
		error_log("AJAX: Exception in get_vehicle_details: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch vehicle details: ' . $e->getMessage()
		]);
	}
	exit;
}
if ($page == 'get_current_vehicles') {
	try {
		$url = API . "?mode=getvehiclegateway&method=get&company_id=" . $_SESSION['company_id'];
		$response = callAPI($url);
		$vehicleData = json_decode($response, true);
		$result = [
			'status' => 'success',
			'data' => $vehicleData['current_vehicles'] ?? []
		];
		echo json_encode($result);
	} catch (Exception $e) {
		error_log("AJAX: Exception in get_current_vehicles: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch current vehicles.'
		]);
	}
	exit;
}
if ($page == 'get_vehicle_statistics') {
	try {
		$url = API . "?mode=getvehiclegateway&method=get&company_id=" . $_SESSION['company_id'];
		$response = callAPI($url);
		$vehicleData = json_decode($response, true);
		$result = [
			'status' => 'success',
			'data' => $vehicleData['statistics'] ?? [
				'current_count' => 0,
				'today_entries' => 0,
				'today_exits' => 0,
				'avg_duration_minutes' => 0
			]
		];
		error_log("AJAX: Statistics result: " . json_encode($result));
		echo json_encode($result);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch statistics.'
		]);
	}
	exit;
}
if ($page == 'get_recent_activity') {
	try {
		$url = API . "?mode=getrecentvehicleactivity&method=get&company_id=" . $_SESSION['company_id'] . "&limit=10";
		$response = callAPI($url);
		$activityData = json_decode($response, true);
		if (isset($activityData['data'])) {
			echo json_encode([
				'status' => 'success',
				'data' => $activityData['data']
			]);
		} else {
			echo json_encode([
				'status' => 'success',
				'data' => $activityData['activities'] ?? []
			]);
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch recent activity.'
		]);
	}
	exit;
} 
if ($page == 'lookup_vehicle_details' && $_POST['vehicle_number'] != '') {
	$url = API . "?mode=lookup_vehicle_details" . "&method=get" . "&vehicle_number=" . $_POST['vehicle_number'] . "&company_id=" . $_SESSION['company_id'];
	$response = callAPI($url);
	echo $response;
	exit;
}
if ($page == 'insert_vehicle_entry') {
	if (empty($_POST['vehicle_number']) || empty($_POST['vehicle_type']) || empty($_POST['driver_name']) || empty($_POST['visit_purpose']) || empty($_POST['location'])) {
		echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields including location.']);
		exit;
	}
	
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['created_by'] = $_SESSION['userid'];
	$_POST['created_date'] = date('Y-m-d H:i:s');
	$_POST['is_active'] = 0;
	
	$method_type = isset($_POST['method_type']) && $_POST['method_type'] !== '' ? $_POST['method_type'] : 'entry_first';
	$_POST['method_type'] = $method_type;
	$_POST['entry_date'] = $_POST['entry_date'] ?? date('Y-m-d');
	
	$time_value = $_POST['entry_time'] ?? date('H:i:s');
	
	if ($method_type === 'exit_first') {
		$_POST['exit_time'] = $time_value;
		$_POST['exit_timestamp'] = date('Y-m-d H:i:s');
		unset($_POST['entry_time']);
		unset($_POST['entry_timestamp']);
		$_POST['current_status'] = 'outside';
	} else {
		$_POST['entry_time'] = $time_value;
		$_POST['entry_timestamp'] = date('Y-m-d H:i:s');
		unset($_POST['exit_time']);
		unset($_POST['exit_timestamp']);
		$_POST['current_status'] = 'inside';
	}
	
	$url = API . "?mode=insertvehiclegateway&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
	exit;
}
if ($page == 'insert_transport_vehicle_master') { 
    if (empty($_POST['vehicle_reg_no']) || empty($_POST['vehicle_type'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle registration number and type are required.']);
        exit;
    }
 
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['created_by'] = $_SESSION['userid'];
    $_POST['created_date'] = date('Y-m-d H:i:s');
    $_POST['vehicle_reg_no'] = strtoupper(trim($_POST['vehicle_reg_no']));
     
    $_POST['is_active'] = $_POST['is_active'] ?? '0';
    $_POST['current_status'] = $_POST['current_status'] ?? '0';
    $_POST['km_driven'] = $_POST['km_driven'] ?? 0;
    $_POST['transportation_type'] = $_POST['transportation_type'] ?? '0';

    $url = API . "?mode=inserttransportvehiclemaster&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    echo $response;
    exit;
}
if ($page == 'update_transport_vehicle_master') { 
    if (empty($_POST['vehicle_id']) || empty($_POST['vehicle_reg_no']) || empty($_POST['vehicle_type'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID, registration number and type are required.']);
        exit;
    }
 
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['updated_by'] = $_SESSION['userid'];
    $_POST['updated_date'] = date('Y-m-d H:i:s');
    $_POST['vehicle_reg_no'] = strtoupper(trim($_POST['vehicle_reg_no']));

    $url = API . "?mode=updatetransportvehiclemaster&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    echo $response;
    exit;
}
if ($page == 'refresh_vehicle_table') {
	if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
		$from_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
		$to_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
	} else {
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-t');
	}

	$url = API . "?mode=getvehiclegatewaylist&method=get&company_id=" . $_SESSION['company_id'];
	$url .= "&from_date=" . urlencode($from_date);
	$url .= "&to_date=" . urlencode($to_date);

	$response = callAPI($url);
	$data = json_decode($response, true) ?: [];
	$vehicles = $data['vehicles'] ?? [];

	function formatVehicleDurationRefresh($vehicle)
	{
		$method_type = $vehicle['method_type'] ?? 'entry_first';
		$entry_time = $vehicle['entry_time'] ?? null;
		$exit_time = $vehicle['exit_time'] ?? null;
		
		if ($entry_time === '00:00:00') $entry_time = null;
		if ($exit_time === '00:00:00') $exit_time = null;
		
		if ($method_type == 'exit_first') {
			if (!empty($entry_time) && !empty($exit_time)) {
				$exitTime = strtotime($vehicle['exit_date'] . ' ' . $exit_time);
				$entryTime = strtotime($vehicle['entry_date'] . ' ' . $entry_time);
				$duration = round(($entryTime - $exitTime) / 60);
			} else if (!empty($vehicle['exit_timestamp'])) {
				$duration = round((time() - strtotime($vehicle['exit_timestamp'])) / 60);
			} else {
				$duration = 0;
			}
		} else {
			if (!empty($vehicle['exit_timestamp'])) {
				$duration = round((strtotime($vehicle['exit_timestamp']) - strtotime($vehicle['entry_timestamp'])) / 60);
			} else {
				$duration = round((time() - strtotime($vehicle['entry_timestamp'])) / 60);
			}
		}
		
		if ($duration < 0) $duration = abs($duration);
		return $duration >= 60 ? floor($duration / 60) . 'h ' . ($duration % 60) . 'm' : $duration . 'm';
	}

	$vehicleTypes = [
		'demo' => 'Demo',
		'car' => 'Car',
		'truck' => 'Truck',
		'van' => 'Van',
		'motorcycle' => 'Motorcycle',
		'bus' => 'Bus',
		'trailer' => 'Trailer',
		'other' => 'Other'
	];

	$tableHTML = '';
	$sl = 1;
	if (!empty($vehicles)) {
		foreach ($vehicles as $vehicle) {
			$method_type = $vehicle['method_type'] ?? 'entry_first';
			$in_time = $vehicle['entry_time'] ?? null;
			$out_time = $vehicle['exit_time'] ?? null;
			$record_date = $vehicle['entry_date'] ?? $vehicle['exit_date'] ?? null;
			
			if ($in_time === '00:00:00') $in_time = null;
			if ($out_time === '00:00:00') $out_time = null;
			
			if ($method_type == 'exit_first') {
				$is_complete = !empty($in_time) && !empty($out_time);
				$status_bg = $is_complete ? '#6c757d' : '#dc3545';
				$status_icon = $is_complete ? 'ti-check' : 'ti-export';
				$status_text = $is_complete ? 'Completed' : 'Outside';
				$action_btn_class = 'success';
			} else {
				$is_complete = !empty($out_time);
				$status_bg = $is_complete ? '#6c757d' : '#28a745';
				$status_icon = $is_complete ? 'ti-check' : 'ti-import';
				$status_text = $is_complete ? 'Completed' : 'Inside';
				$action_btn_class = 'danger';
			}
			
			$duration = formatVehicleDurationRefresh($vehicle);
			$status_color = '#fff';

			$tableHTML .= '<tr>';
			$tableHTML .= '<td>' . $sl++ . '</td>';
			$tableHTML .= '<td><strong>' . htmlspecialchars($vehicle['vehicle_number']) . '</strong></td>';
			$tableHTML .= '<td>' . htmlspecialchars($vehicleTypes[$vehicle['vehicle_type']] ?? ucfirst($vehicle['vehicle_type'])) . '</td>';
			$tableHTML .= '<td>' . htmlspecialchars($vehicle['driver_name']) . '</td>';
			$tableHTML .= '<td>' . htmlspecialchars($vehicle['driver_contact'] ?? '-') . '</td>';
			$tableHTML .= '<td>';
			$tableHTML .= htmlspecialchars(ucfirst($vehicle['visit_purpose']));
			if (!empty($vehicle['visiting_person'])) {
				$tableHTML .= '<br><small class="text-muted">To: ' . htmlspecialchars($vehicle['visiting_person']) . '</small>';
			}
			$tableHTML .= '</td>';
			$tableHTML .= '<td>' . htmlspecialchars($vehicle['location'] ?? '-') . '</td>';
			$tableHTML .= '<td>';
			if ($method_type == 'exit_first') {
				$tableHTML .= '<span class="badge badge-danger" style="padding:4px 8px;"><i class="ti-export"></i> Exit First</span>';
			} else {
				$tableHTML .= '<span class="badge badge-success" style="padding:4px 8px;"><i class="ti-import"></i> Entry First</span>';
			}
			$tableHTML .= '</td>';
			$tableHTML .= '<td>' . (!empty($record_date) ? date('d-M-Y', strtotime($record_date)) : '-') . '</td>';
			$tableHTML .= '<td>' . (!empty($in_time) ? date('g:i A', strtotime($in_time)) : '<span class="text-muted">Pending</span>') . '</td>';
			$tableHTML .= '<td>' . (!empty($out_time) ? date('g:i A', strtotime($out_time)) : '<span class="text-muted">Pending</span>') . '</td>';
			$tableHTML .= '<td>' . $duration . '</td>';
			$tableHTML .= '<td>';
			$tableHTML .= '<span class="badge" style="background-color: ' . $status_bg . '; color: ' . $status_color . '; padding:6px 10px; border-radius:6px;">';
			$tableHTML .= '<i class="' . $status_icon . '"></i> ' . $status_text;
			$tableHTML .= '</span>';
			$tableHTML .= '</td>';
			$tableHTML .= '<td>';
			$tableHTML .= '<button type="button" class="btn btn-sm btn-info btn-round" onclick="printVehicleSlip(' . $vehicle['id'] . ')" title="Print Slip">';
			$tableHTML .= '<i class="ti-printer"></i>';
			$tableHTML .= '</button>';
			if (!$is_complete) {
				$tableHTML .= '<button type="button" class="btn btn-sm btn-' . $action_btn_class . ' btn-round" onclick="recordAction(' . $vehicle['id'] . ', \'' . htmlspecialchars($vehicle['vehicle_number']) . '\', \'' . $method_type . '\')" title="Record">';
				$tableHTML .= '<i class="' . ($method_type == 'exit_first' ? 'ti-import' : 'ti-export') . '"></i>';
				$tableHTML .= '</button>';
			}
			$tableHTML .= '</td>';
			$tableHTML .= '</tr>';
		}
	} else {
		$tableHTML .= '<tr>';
		$tableHTML .= '<td colspan="14">';
		$tableHTML .= '<div class="card-px text-center py-20 m-t-30">';
		$tableHTML .= '<p class="text-muted f-16 fw-bold m-b-30">';
		$tableHTML .= '<i class="ti-alert text-warning"></i> No Vehicle Records Found';
		$tableHTML .= '</p>';
		$tableHTML .= '<div class="text-center px-4">';
		$tableHTML .= '<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">';
		$tableHTML .= '</div>';
		$tableHTML .= '</div>';
		$tableHTML .= '</td>';
		$tableHTML .= '</tr>';
	}

	echo '<!DOCTYPE html><html><body><table><tbody>' . $tableHTML . '</tbody></table></body></html>';
	exit;
}
if ($page == 'get_transport_vehicle_master_details') {
    if (empty($_POST['vehicle_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID is required.']);
        exit;
    }

    try {
        $vehicle_id = intval($_POST['vehicle_id']);
        $company_id = $_SESSION['company_id'];

        $url = API . "?mode=gettransportvehiclemasterdetails&method=get&rid=" . $vehicle_id . "&company_id=" . $company_id;
        $response = callAPI($url);
        $vehicleData = json_decode($response, true);

        if ($vehicleData && isset($vehicleData['data']) && !empty($vehicleData['data'])) {
            echo json_encode([
                'status' => 'success',
                'data' => $vehicleData['data']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Vehicle not found.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch vehicle details: ' . $e->getMessage()
        ]);
    }
    exit;
}
if ($page == 'delete_transport_vehicle_master') {
    if (empty($_POST['vehicle_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID is required.']);
        exit;
    }

    try {
        $vehicle_id = intval($_POST['vehicle_id']);
        $company_id = $_SESSION['company_id'];
        $user_id = $_SESSION['userid'];

        $url = API . "?mode=deletetransportvehiclemaster&method=post&rid=" . $vehicle_id . "&company_id=" . $company_id . "&user_id=" . $user_id;
        $response = callAPI($url);
        $result = json_decode($response, true);

        if ($result && (isset($result['status']) && ($result['status'] == 'success' || $result['status'] == 'sucess'))) {
            echo json_encode(['status' => 'success', 'message' => 'Vehicle deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'Failed to delete vehicle.']);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete vehicle: ' . $e->getMessage()
        ]);
    }
    exit;
}
// gateway code end
if($page=="saveEmployeeAttendance"){
    $post = $_POST;
    $url = API."?mode=saveEmployeeAttendance&method=post";
    $response = file_post_contents($url, $post, 'insert');
    echo $response;
    exit;
}
if($page=="getAutoAttendanceTime"){
	$cudata = '';
	$url = API . "?mode=getAutoAttendanceTime&method=get&id=".$_POST['id']."&emp_id=".$_POST['emp_id']."&date=".$_POST['date'];
	$cudata = callAPI($url);
	echo $cudata;
	exit;
}
if($page == "getbaseuomconversion"){
	$quantity = ($_POST['quantity'] !="")?$_POST['quantity']:0;
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'parent_uom_id'=>$_POST['baseuom_id'],'child_uom_id'=>$_POST['uom_id']);
	$wh1['field'] = "id,conversion_ratio";
	$data1 = urlencode(json_encode($wh1));
	$url1 = API."?mode=gettabledetailswithconditions&table=uom_conversions&data=".$data1; 
	$data_details1 = callAPI($url1);
	$json = json_decode($data_details1,1);
	if(count((array)$json) != 0){
		$conuom = $json[0];
		$conversion_ratio = ($conuom['conversion_ratio']!=0 AND $conuom['conversion_ratio']!="")?$conuom['conversion_ratio']:"1";
		echo json_encode(array('status'=>'Success','res'=>($quantity*$conversion_ratio),'res'=>($quantity*$conversion_ratio)));
	}else{
		echo json_encode(array('status'=>'Fail','res'=>$quantity,'res'=>'NA - '.$quantity));
	}

}

// Buddy Reassignment - Get employees by buddy type
if ($page == 'get_employees_by_buddy_type') {
 	$_POST['company_id'] = $_SESSION['company_id'];
 	$url = API . "?method=post&mode=getBuddyTypeEmployees";
	$response = file_post_contents($url, $_POST);
 	echo $response;
 	exit;
}
 
 // Buddy Reassignment - Get affected employees for preview
if ($page == 'get_buddy_employees') {
 	$_POST['company_id'] = $_SESSION['company_id'];
 	$url = API . "?method=post&mode=get_buddy_employees";
 	$response = file_post_contents($url, $_POST);
 	echo $response;
 	exit;
}
 
 // Buddy Reassignment - Confirm and update
if ($page == 'confirm_buddy_reassignment') {
 	$_POST['company_id'] = $_SESSION['company_id'];
 	$_POST['user_id'] = $_SESSION['userid'];
 	$url = API . "?method=post&mode=confirm_buddy_reassignment";
 	$response = file_post_contents($url, $_POST);
 	echo $response;
 	exit;
}


// Leave Balance Management Handlers
if ($page == 'load_leave_employees') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=get&mode=getLeaveEmployees";
	$response = callAPI($url . "&company_id=" . $_POST['company_id'] . "&user_id=" . $_POST['user_id']);
	echo $response;
	exit;
}

if ($page == 'load_leave_balance_data') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=get&mode=getLeaveBalanceAndAllocations";
	$response = callAPI($url . "&company_id=" . $_POST['company_id'] . "&user_id=" . $_POST['user_id'] . 
	                     "&employee_id=" . $_POST['employee_id'] . "&leave_year=" . $_POST['leave_year']);
	echo $response;
	exit;
}

if ($page == 'save_leave_allocation') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=post&mode=saveLeaveAllocation";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}

// Inline clock time editing
if($page=="saveClockTime"){
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API."?mode=saveClockTime&method=post";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}


if ($page == 'get_pending_vehicles') {
	try {
		$url = API . "?mode=getvehiclegatewaypending&method=get&company_id=" . $_SESSION['company_id'];
		// echo $url; // Debugging line to check the URL being called
		$response = callAPI($url);
		$vehicleData = json_decode($response, true);
		$result = [
			'status' => 'success',
			'data' => $vehicleData['vehicles'] ?? []
		];
		echo json_encode($result);
	} catch (Exception $e) {
		error_log("AJAX: Exception in get_pending_vehicles: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch pending vehicles.'
		]);
	}
	exit;
}

if ($page == 'get_missing_vehicles') {
	try {
		$url = API . "?mode=getvehiclegatewaymissing&method=get&company_id=" . $_SESSION['company_id'];
		// echo $url; // Debugging line to check the URL being called
		$response = callAPI($url);
		$vehicleData = json_decode($response, true);
		$result = [
			'status' => 'success',
			'data' => $vehicleData['vehicles'] ?? []
		];
		echo json_encode($result);
	} catch (Exception $e) {
		error_log("AJAX: Exception in get_missing_vehicles: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to fetch missing vehicles.'
		]);
	}
	exit;
}

if ($page == 'update_vehicle_exit') {
	try {
		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['user_id'] = $_SESSION['userid'];
		$url = API . "?method=post&mode=updatevehiclegatewayexit";
		$response = file_post_contents($url, $_POST);
		echo $response;
	} catch (Exception $e) {
		error_log("AJAX: Exception in update_vehicle_exit: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to update vehicle exit.'
		]);
	}
	exit;
}

// personal gate way
if ($page == 'update_personal_exit') {
	try {
		$_POST['company_id'] = $_SESSION['company_id'];
		$_POST['user_id'] = $_SESSION['userid'];
		$url = API . "?method=post&mode=updatepersonalgatewayexit";
		$response = file_post_contents($url, $_POST);
		echo $response;
	} catch (Exception $e) {
		error_log("AJAX: Exception in update_personal_exit: " . $e->getMessage());
		echo json_encode([
			'status' => 'error',
			'message' => 'Failed to update personal exit.'
		]);
	}
	exit;
}

// Attendance key update
if($page=="updateAttendanceKey"){
	$rid = intval($_POST['rid'] ?? 0);
	$emp_id = intval($_POST['employee_id'] ?? 0);
	$att_date = $_POST['attendace_date'] ?? '';
	$key_short_code = $_POST['key_short_code'] ?? '';
	$company_id = intval($_SESSION['company_id']);
	$user_id = intval($_SESSION['userid']);

	$url = API . "?mode=updateAttendanceKey&method=get"
		. "&rid={$rid}"
		. "&employee_id={$emp_id}"
		. "&attendace_date=" . urlencode($att_date)
		. "&key_short_code=" . urlencode($key_short_code)
		. "&company_id={$company_id}"
		. "&user_id={$user_id}";

	$response = callAPI($url);
	echo $response;
	exit;
}

if($page=="tocreateuserstationeryrequestnewhr"){
	// print_r($_POST);
	if($_POST['request_date'] =='undefined'){
		$_POST['request_date'] = $_POST['require_date'];
	}
	$_POST['request_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['request_date']))); 
	$_POST['require_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['require_date']))); 

	// print_r($_POST); exit;
	$url = API."?mode=tocreateuserstationeryrequestnewhr&deliver_type=".$_POST['deliver_type']."&qty=".$_POST['total_qty']."&request_date=".$_POST['request_date']."&require_date=".$_POST['require_date']."&building_id=".$_POST['building_id']."&building_floor_id=".$_POST['building_floor_id']."&building_floor_space_id=".$_POST['building_floor_space_id']."&location_id=".$_POST['location_id']."&st_emp_id=".$_POST['emp_id']."&st_current_status=".$_POST['st_current_status']."&other_name=".$_POST['other_name']; 
	$api = callAPI($url);
	echo json_decode($api,1);
}

if($page=="payroll_reset_pay_area"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?method=post&mode=Resetthrpayrolldatabasedonmonthandyearandpayarea";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}

if ($page == 'employee_missing_details') {
	$action = isset($_POST['action']) ? $_POST['action'] : '';

	if ($action == 'create_pay_area_employee') {
		try {
			$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
			$company_id = isset($_SESSION['company_id']) ? intval($_SESSION['company_id']) : 0;
			$user_id = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;
			$pay_area_id = 1;

			if ($emp_id <= 0 || $company_id <= 0) {
				echo json_encode(array('status' => 'error', 'message' => 'Invalid employee or company context.'));
				exit;
			}

			$exists_filter = array(
				'where' => array(
					'company_id' => $company_id,
					'emp_id' => $emp_id,
					'pay_area_id' => $pay_area_id,
					'is_active' => 0
				)
			);
			$exists_url = API . "?mode=gettabledetailswithconditions&table=pay_area_employees&data=" . urlencode(json_encode($exists_filter));
			$exists_response = callAPI($exists_url);
			if ($exists_response === false) {
				$last_error = error_get_last();
				echo json_encode(array(
					'status' => 'error',
					'message' => 'Exists-check API call failed.',
					'api_url' => $exists_url,
					'php_error' => $last_error
				));
				exit;
			}
			$exists_rows = json_decode($exists_response, true);

			if (is_array($exists_rows) && count($exists_rows) > 0) {
				echo json_encode(array('status' => 'info', 'message' => 'Pay area mapping already exists.'));
				exit;
			}

			$insert_payload = array(
				'emp_id' => $emp_id,
				'pay_area_id' => $pay_area_id,
				'efective_from_date' => date('Y-m-d'),
				'efective_to_date' => '',
				'current_status' => 0,
				'created_date' => date('Y-m-d H:i:s'),
				'is_active' => 0,
				'created_by' => $user_id,
				'company_id' => $company_id
			);

			$insert_url = API . "?mode=adddata&table=pay_area_employees&method=post";
			$insert_response = file_post_contents($insert_url, $insert_payload, 'insert');
			if ($insert_response === false) {
				$last_error = error_get_last();
				echo json_encode(array(
					'status' => 'error',
					'message' => 'Insert API call failed.',
					'api_url' => $insert_url,
					'payload' => $insert_payload,
					'php_error' => $last_error
				));
				exit;
			}
			$insert_result = json_decode($insert_response, true);

			if (is_array($insert_result) && isset($insert_result['status']) && ($insert_result['status'] == 'sucess' || $insert_result['status'] == 'success')) {
				echo json_encode(array('status' => 'success', 'message' => 'Pay area mapping created successfully.'));
				exit;
			}

			if (is_array($insert_result)) {
				echo json_encode(array(
					'status' => 'error',
					'message' => isset($insert_result['message']) ? $insert_result['message'] : 'Insert API returned error status.',
					'api_response' => $insert_result
				));
				exit;
			}

			echo json_encode(array(
				'status' => 'error',
				'message' => 'Insert API did not return valid JSON.',
				'raw_response' => $insert_response
			));
			exit;
		} catch (Throwable $e) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Exception while creating pay area mapping.',
				'error' => $e->getMessage(),
				'line' => $e->getLine(),
				'file' => basename($e->getFile())
			));
			exit;
		}
	}

	if ($action == 'create_user_with_module_list') {
		try {
			$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
			$company_id = isset($_SESSION['company_id']) ? intval($_SESSION['company_id']) : 0;
			$user_id = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;

			$create_url = API . "?mode=tocreateuserwithusermodulelist&empid=" . $emp_id . "&user_id=" . $user_id . "&company_id=" . $company_id;
			$create_response = callAPI($create_url);

			$create_result = json_decode($create_response, true);
			if (is_array($create_result)) {
				echo json_encode(array(
					'status' => 'success',
					'message' => 'User created and linked successfully.',
					'api_response' => $create_result
				));
				exit;
			}
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Create-user API did not return valid JSON.',
				'raw_response' => $create_response
			));
			exit;
		} catch (Throwable $e) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Exception while creating user mapping.',
				'error' => $e->getMessage(),
				'line' => $e->getLine(),
				'file' => basename($e->getFile())
			));
			exit;
		}
	}
}

if ($page == 'submit_issue_raw_materials_to_vendor') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=post&mode=saveissuerawmaterialstovendor";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}



if($page == 'cancel_vehicle_entry'){
	$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
	if($vehicle_id <= 0){
		echo json_encode(array('status' => 'error', 'message' => 'Invalid vehicle entry id.'));
		exit;
	}
	$data = array(
		'is_active'    => '1',
		'updated_date' => date('Y-m-d H:i:s'),
		'updated_by'   => $_SESSION['userid']
	);
	$url = API."?mode=updaterowtabledata&rid=".$vehicle_id."&table=transport_gateway&method=post";
	$response = file_post_contents($url, $data, 'update');
	echo $response;
	exit;
}

if($page == 'cancel_personal_entry'){
	$entry_id = isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
	if($entry_id <= 0){
		echo json_encode(array('status' => 'error', 'message' => 'Invalid personal entry id.'));
		exit;
	}
	$data = array(
		'is_active'    => '1',
		'updated_date' => date('Y-m-d H:i:s'),
		'updated_by'   => $_SESSION['userid']
	);
	$url = API."?mode=updaterowtabledata&rid=".$entry_id."&table=transport_personal_gateway&method=post";
	$response = file_post_contents($url, $data, 'update');
	echo $response;
	exit;
}

if ($page == 'get_personal_gateway_authorizers') {
	$company_id = (int) $_SESSION['company_id'];
	$employee_id = isset($_POST['employee_id']) ? (int) $_POST['employee_id'] : 0;
	$url = API . "?mode=getEmployeeBuddyAuthorizers&method=get&company_id=" . $company_id . "&employee_id=" . $employee_id;
	$response = callAPI($url);
	echo $response;
	exit;
}

if($page == "insertemployeeworkattendancespayareawise"){
		$url1 = API."?mode=insertemployeeworkattendancespayareawise&month=".$_POST['month']."&year=".$_POST['year']."&monthstartday=".$_POST['monthstartday']."&admin_type=".$_SESSION['admin_type']."&emp_id=".$_SESSION['emp_id']."&pay_area_id=".$_POST['pay_area_id']; 
	   echo  $data_details1 = callAPI($url1);
}

if ($page == 'get_subcontract_work_order_receipt_tax_preview') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=post&mode=getsubcontractworkorderreceipttaxpreview";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}
if ($page == 'get_subcontract_work_order_receipt_history') {
	$work_order_id = isset($_POST['subcontract_work_order_id']) ? $_POST['subcontract_work_order_id'] : '';
	$url = API . "?mode=getsubcontractworkorderreceipthistory&work_order_id=".$work_order_id;
	$response = callAPI($url);
	echo $response;
	exit;
}

if ($page == 'save_po_excel_upload') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['originator'] = $_SESSION['userid'];

	$url = API . "?mode=insertBulkCsrPoUpload&method=post";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}

if ($page == 'save_invoice_excel_upload') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['originator'] = $_SESSION['userid'];

	$url = API . "?mode=insertBulkCsrInvoiceUpload&method=post";
	$response = file_post_contents($url, $_POST);
	echo $response;
	exit;
}
if ($page == 'tosavecommentdataquater') { 
	print_r($_POST);
	$url = API."?mode=tosavecommentdataquater&method=post";
	$response = callAPI($url);
	$response = file_post_contents($url, $_POST,'insert');
	echo $response; exit;
}
if($page == 'saveemployeeexit'){
	$post_arr = $_POST;
	$url = API."?mode=saveemployeeexit&method=post";
	$post_arr['user_id'] = $_SESSION['userid'];
	$post_arr['company_id'] = $_SESSION['company_id'];
	$post_arr['status_date'] = date('Y-m-d H:i:s');
	//echo $url; print_r($sarray); //exit;
	$response = file_post_contents($url, $post_arr,'insert');
	echo $response; exit;
}
if ($page == 'geteexitcontrantractoremployeedata') { 
	$url = API."?mode=geteexitcontrantractoremployeedata&method=get&status=".urlencode($_POST['status'])."&search=".urlencode($_POST['search']);
	$response = callAPI($url);
	echo $response; exit;
}
if($page == 'getEmployeesByLocation'){
	$where = array('company_id'=> $_SESSION['company_id'],'is_active'=> 0, 'employee_type' => 1);
	  if($_POST['location_id'] != '' && $_POST['location_id'] != 99){
	    $where['location_id'] = $_POST['location_id'];
	  }
	  $data1["field"] = "id,display_name,emp_code";
	  $data1["where"] = $where;	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=employees&data=".$data;
	$response = callAPI($curl);
	echo $response; exit; 
}
if ($page == 'saveApprovedTimesheetOvertime') { 
	// print_r($_POST);
	$url = API."?mode=saveApprovedTimesheetOvertime&method=post";
	// echo $url;
	$response = file_post_contents($url, $_POST,'insert');
	echo $response; exit;
}

if($page == 'cancel_leave_request'){
	$rid = isset($_POST['rid']) ? (int)$_POST['rid'] : 0;
	if($rid <= 0){
		echo json_encode(array('status' => 'error', 'message' => 'Invalid leave request id.'));
		exit;
	}
	$table_name = 'employee_leaves';
	$data = array(
		'is_active' => '1',
		'updated_date' => date('Y-m-d H:i:s'),
		'updated_by' => $_SESSION['userid']
	);
	// print_r($data);
	$url = API."?mode=updaterowtabledata&rid=".$rid."&table=".$table_name."&method=post";
	$response = file_post_contents($url, $data, 'update');
	echo $response;
	exit;
}

if ($page == 'manager_team_leave_status') {
	$_POST['company_id'] = $_SESSION['company_id'];
	$_POST['user_id'] = $_SESSION['userid'];
	$url = API . "?method=post&mode=updateManagerTeamLeaveStatus";
	$response = file_post_contents($url, $_POST, 'update');
	echo $response;
	exit;
}
// if ($page == 'getreferencetransactions') { 
// 	$url = API . "?mode=getVisitorInsertRelatedData&vendor_supplier_document_type=" . $_POST['vendor_supplier_document_type'];
// 	$results = callAPI($url);
// 	$result = json_decode($results, 1);
// 	$document_transactions = $result['document_transactions'];
// 	$htmloption = '<option value="">Select Any One</option>';
// 	if(count((array)$document_transactions ) != 0 ){
// 		foreach($document_transactions as $doctrans){
// 			$htmloption .="<option value='".$doctrans['id']."'>".$doctrans['transaction_number']." - ".$doctrans['transaction_date']." - ".$doctrans['party_name']."</option>";
// 		}
// 	}else{
// 		$htmloption = '<option value="">No Documents</option>';
// 	}
// 	echo $htmloption;
// }

// added by koti start
if ($page == 'getcustomercontracts') {
	$customer_id = $_POST['customer_id'] ?? '';
	$status = $_POST['status'] ?? '';
	$url = API . "?mode=getcustomercontracts&method=get&customer_id=" . $customer_id . "&status=" . $status;
	echo callAPI($url);
	exit;
}
if ($page == 'getvendorcontracts') {
	$vendor_id = $_POST['vendor_id'] ?? '';
	$status = $_POST['status'] ?? '';
	$url = API . "?mode=getvendorcontracts&method=get&vendor_id=" . $vendor_id . "&status=" . $status;
	echo callAPI($url);
	exit;
}
if ($page == 'assigncontractamctechnician') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API . "?mode=assigncontractamctechnician&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'savecontractamcassettechnicians') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API . "?mode=savecontractamcassettechnicians&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'savecontractamcchecklist') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API . "?mode=savecontractamcchecklist&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'getcontractamcfmdashboardsection') {
	$section = trim((string) ($_POST['section'] ?? ''));
	$pageNum = max(1, (int) ($_POST['page_num'] ?? 1));
	$search = trim((string) ($_POST['search'] ?? ''));
	$query = http_build_query(array(
		'mode' => 'getcontractamcfmdashboarddata',
		'method' => 'get',
		'company_id' => $_SESSION['company_id'] ?? 0,
		'user_id' => $_SESSION['userid'] ?? 0,
		'admin_type' => $_SESSION['admin_type'] ?? '',
		'working_role_short_code' => $_SESSION['working_role_short_code'] ?? '',
		'section' => $section,
		'page' => $pageNum,
		'search' => $search,
	));
	$url = API . '?' . $query;
	echo callAPI($url);
	exit;
}
if ($page == 'confirmcontractamcfmvisit') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$fileError = '';
	$fileName = '';
	if (!empty($_FILES['fm_confirmation_file']['name'])) {
		$acceptable = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx');
		$originalName = basename($_FILES['fm_confirmation_file']['name']);
		$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
		if (!in_array($ext, $acceptable)) {
			$fileError = 'Invalid file type';
		} elseif ($_FILES['fm_confirmation_file']['error'] != 0) {
			$fileError = 'File upload failed';
		} else {
			$uploadDir = 'uploads/attachments/';
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}
			$fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
			if (!move_uploaded_file($_FILES['fm_confirmation_file']['tmp_name'], $uploadDir . $fileName)) {
				$fileError = 'Unable to save uploaded file';
				$fileName = '';
			}
		}
	} else {
		$fileError = 'Job done document is required';
	}
	if ($fileError != '') {
		echo json_encode(array('status' => 'error', 'message' => $fileError));
		exit;
	}
	$_POST['fm_confirmation_file'] = $fileName;
	$url = API . "?mode=confirmcontractamcfmvisit&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'updatecontractamcscheduledetails') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API . "?mode=updatecontractamcscheduledetails&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'getcontractamcscheduledetails') {
	$schedule_id = $_POST['schedule_id'] ?? '';
	$url = API . "?mode=getcontractamcscheduledetails&method=get&schedule_id=" . $schedule_id;
	echo callAPI($url);
	exit;
}
if ($page == 'getamcassetsbylocationcategory') {
	$location_id = (int) ($_POST['location_id'] ?? 0);
	$category_id = (int) ($_POST['category_id'] ?? 0);
	$url = API . "?mode=getamcassetsbylocationcategory&method=get&location_id=" . $location_id . "&category_id=" . $category_id;
	echo callAPI($url);
	exit;
}
if ($page == 'saveamcrepairorder') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API . "?mode=saveamcrepairorder&method=post&admin_type=" . ($_SESSION['admin_type'] ?? '') . "&working_role_short_code=" . urlencode($_SESSION['working_role_short_code'] ?? '');
	echo file_post_contents($url, $_POST, 'update');
	exit;
}
if ($page == 'getamccontractfromassetlocation') {
	$asset_id = (int) ($_POST['asset_id'] ?? 0);
	$location_id = (int) ($_POST['location_id'] ?? 0);
	$url = API . "?mode=getamccontractfromassetlocation&method=get&asset_id=" . $asset_id . "&location_id=" . $location_id;
	echo callAPI($url);
	exit;
}
if ($page == 'getamccontractschedulesforasset') {
	$contract_id = (int) ($_POST['contract_id'] ?? 0);
	$asset_id = (int) ($_POST['asset_id'] ?? 0);
	$url = API . "?mode=getamccontractschedulesforasset&method=get&contract_id=" . $contract_id . "&asset_id=" . $asset_id;
	echo callAPI($url);
	exit;
}
if ($page == 'getamcrepairordercontractassets') {
	$contract_id = (int) ($_POST['contract_id'] ?? 0);
	$url = API . "?mode=getamcrepairordercontractassets&method=get&contract_id=" . $contract_id;
	echo callAPI($url);
	exit;
}
	if ($page == 'amc_asset_schedule_history') {
		$contractId = (int) ($_POST['contract_id'] ?? 0);
		$assetRowId = (int) ($_POST['contract_amc_asset_id'] ?? 0);
		$companyId = (int) ($_SESSION['company_id'] ?? 0);
		$url = API . "?mode=getamccontractassetschedules&method=get&contract_id={$contractId}&contract_amc_asset_id={$assetRowId}&company_id={$companyId}";
		$response = callAPI($url);
		$scheduleData = json_decode($response, true);
		if (!is_array($scheduleData)) {
			$scheduleData = array('schedules' => array(), 'asset' => array());
		}
		ob_start();
		include __DIR__ . '/views/amc_contract_asset_schedule_history.php';
		echo ob_get_clean();
		exit;
	}

	if ($page == 'get_amc_contract_asset_calendar_events') {
		$contractId = (int) ($_POST['contract_id'] ?? 0);
		$assetRowId = (int) ($_POST['contract_amc_asset_id'] ?? 0);
		$companyId = (int) ($_SESSION['company_id'] ?? 0);
		$url = API . "?mode=getamccontractassetcalendarevents&method=get&contract_id={$contractId}&contract_amc_asset_id={$assetRowId}&company_id={$companyId}";
		echo callAPI($url);
		exit;
	}
// added by koti end







//Omkar Security code 

if ($page == 'getVisitorsByPhoneAndEmpCode') {
	$cudata = '';
	if ($_POST['phone'] != '' || $_POST['employee_code'] != '') {
		$url = API . "?mode=getVisitorsByPhoneAndEmpCode&method=get&phone=" . $_POST['phone'] . "&employee_code=" . $_POST['employee_code'] . "&location_id=" . $_POST['location_id'];
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}
if ($page == 'getVisitorsSearch') {
	$cudata = '';
	if ($_POST['text'] != '' || $_POST['date'] != '') {
		$visit_type = $_POST['visit_type'] ?? '';
		$url = API . "?mode=getVisitorsSearch&method=get&text=" . urlencode($_POST['text']) . "&date=" . urlencode($_POST['date']) . "&location_id=" . urlencode($_POST['location_id']) . "&visit_type=" . urlencode($visit_type);
		$cudata = callAPI($url);
	}
	echo $cudata;
	exit;
}

if ($page == 'getreferencetransactions') {
	$doc_type = $_POST['vendor_supplier_document_type'] ?? '';
	$company_id = $_SESSION['company_id'] ?? 1;
	$str = '<option value="">Select Any One</option>';
	if (in_array($doc_type, ['purchase_orders', 'purchase_invoices'])) {
		$url = API . "?mode=getReferenceTransactions&method=get&doc_type=" . urlencode($doc_type) . "&company_id=" . urlencode($company_id);
		$res_json = callAPI($url);
		$res = json_decode($res_json, true);
		if ($res) {
			foreach ($res as $row) {
				$str .= "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['transaction_number']) . "</option>";
			}
		}
	}
	echo $str;
	exit;
}

if ($page == 'getHistoricalVisitorProfileSearch') {
	$query = isset($_POST['query']) ? trim($_POST['query']) : '';
	$visit_type = isset($_POST['visit_type']) ? trim($_POST['visit_type']) : '';
	$company_id = $_SESSION['company_id'] ?? 1;
	$location_id = $_POST['location_id'] ?? ($_SESSION['location_id'] ?? 1);

	$results = [];
	if ($query !== '' && $visit_type !== '') {
		$url = API . "?mode=getHistoricalVisitorProfileSearch&method=get&query=" . urlencode($query) . "&visit_type=" . urlencode($visit_type) . "&company_id=" . urlencode($company_id) . "&location_id=" . urlencode($location_id);
		$res_json = callAPI($url);
		$results = json_decode($res_json, true);
		//print_r($results);
		if (!is_array($results)) {
			$results = [];
		}
	}
	echo json_encode($results);
	exit;
}

// if ($page == 'getreferencetransactions') {
// 	$doc_type   = $_POST['vendor_supplier_document_type'] ?? '';
// 	$company_id = $_SESSION['company_id'] ?? 1;
// 	$str        = '<option value="">Select Any One</option>';
// 	if (in_array($doc_type, ['purchase_orders', 'purchase_invoices'])) {
// 		require_once 'model/db.class.php';
// 		$_localExtra = [
// 			'user_id' => $_SESSION['userid'] ?? 0, 'company_id' => $company_id,
// 			'ip_address' => '', 'option' => '', 'extra_paramiter' => '',
// 			'extra_details' => '', 'module_id' => 0, 'working_role_short_code' => '',
// 		];
// 		$_localDB = new configClass(MAINDB, $_localExtra);
// 		$sql = "SELECT id, transaction_number FROM " . $doc_type . " WHERE company_id = '" . $company_id . "' AND is_active = 0 ORDER BY transaction_number ASC";
// 		$res = $_localDB->getAllRows($sql);
// 		if ($res) {
// 			foreach ($res as $row) {
// 				$str .= "<option value='" . $row->id . "'>" . htmlspecialchars($row->transaction_number) . "</option>";
// 			}
// 		}
// 	}
// 	echo $str;
// 	exit;
// }
if ($page == 'updateReturnDate') {
	$bom_id           = $_POST['bom_id'] ?? 0;
	$actual_return_by = $_POST['actual_return_by'] ?? '';
	$response         = ['status' => 'error', 'message' => 'Invalid request'];
	if ($bom_id > 0 && $actual_return_by != '') {
		$url      = API . "?mode=updateReturnDate&bom_id=" . $bom_id . "&actual_return_by=" . $actual_return_by;
		$response = callAPI($url);
	}
	echo $response;
	exit;
}
if ($page == 'insertVisitorBundle') {
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	

    // 1) Photo: Check webcam first, then file upload, then fallback to existing
    $existingImage = $_POST['existing_image'] ?? '';
    $webcamImage = $_POST['webcam_image'] ?? '';
    $newImageName = '';
 
    if (!empty($webcamImage) && strpos($webcamImage, 'data:image') === 0) {
        // Webcam capture processing
        $parts = explode(',', $webcamImage);
        if (isset($parts[1])) {
            $decodedData = base64_decode($parts[1]);
            $newImageName = 'webcam_' . time() . '_' . uniqid() . '.jpg';
            $targetDir = 'uploads/visitors/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            if (file_put_contents($targetDir . $newImageName, $decodedData) === false) {
                $newImageName = $existingImage;
            }
        } else {
            $newImageName = $existingImage;
        }
    } elseif (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $newImageName = imageupload('visitors', 'image_path', $_POST['image_path'] ?? '');
    } else {
        $newImageName = $existingImage;
    }
    $_POST['image_path'] = $newImageName;
 
    // 2) Normalize visiting_company_id and process files for vendor_supplier
    $visit_type = $_POST['visit_type'] ?? '';
    if ($visit_type === 'delivery') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_delivery'] ?? '';
    } elseif ($visit_type === 'service') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_service'] ?? '';
    } elseif ($visit_type === 'employee' && ($_POST['emp_temp_enable'] ?? 0) == 1) {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_employee_temp'] ?? '';
    } elseif ($visit_type === 'doctor') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_doctor'] ?? '';
    } elseif ($visit_type === 'vendor_supplier') {
        $_POST['visiting_company_id'] = $_POST['vendor_supplier_transaction_no'] ?? '';
 
        $uploaded_proof_files = [];
        if (isset($_FILES['vendor_supplier_proof_document_file']['name']) && is_array($_FILES['vendor_supplier_proof_document_file']['name'])) {
            foreach ($_FILES['vendor_supplier_proof_document_file']['name'] as $idx => $fname) {
                if ($_FILES['vendor_supplier_proof_document_file']['error'][$idx] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($fname, PATHINFO_EXTENSION);
                    $newfilename = 'proof_' . time() . '_' . uniqid() . '.' . $ext;
                    $path = 'uploads/visitors/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    if (move_uploaded_file($_FILES['vendor_supplier_proof_document_file']['tmp_name'][$idx], $path . $newfilename)) {
                        $uploaded_proof_files[$idx] = $newfilename;
                    } else {
                        $uploaded_proof_files[$idx] = $_POST['existing_vendor_supplier_proof_document_file'][$idx] ?? '';
                    }
                } else {
                    $uploaded_proof_files[$idx] = $_POST['existing_vendor_supplier_proof_document_file'][$idx] ?? '';
                }
            }
        }
 
        $proof_types = isset($_POST['vendor_supplier_proof_type']) && is_array($_POST['vendor_supplier_proof_type']) ? implode(',', array_filter($_POST['vendor_supplier_proof_type'])) : ($_POST['vendor_supplier_proof_type'] ?? '');
        $proof_nos = isset($_POST['vendor_supplier_proof_document_no']) && is_array($_POST['vendor_supplier_proof_document_no']) ? implode(',', array_filter($_POST['vendor_supplier_proof_document_no'])) : ($_POST['vendor_supplier_proof_document_no'] ?? '');
        $proof_files = implode(',', array_filter($uploaded_proof_files));
 
        $_POST['vendor_supplier_proof_type'] = $proof_types;
        $_POST['vendor_supplier_proof_document_no'] = $proof_nos;
        $_POST['vendor_supplier_proof_document_file'] = $proof_files;
 
        $newDocFile = '';
        if (isset($_FILES['vendor_supplier_document_file']) && $_FILES['vendor_supplier_document_file']['error'] === UPLOAD_ERR_OK) {
            $newDocFile = imageupload('visitors', 'vendor_supplier_document_file', $_POST['existing_vendor_supplier_document_file'] ?? '');
        } else {
            $newDocFile = $_POST['existing_vendor_supplier_document_file'] ?? '';
        }
        $_POST['vendor_supplier_document_file'] = $newDocFile;
    } elseif ($visit_type === 'visitor' || $visit_type === 'others') {
        $_POST['visiting_company_id'] = $_POST['visiting_company_id_visitor'] ?? '';
    }
 
    $type = (int)($_POST['rid'] ?? 0) > 0 ? 'update' : 'insert';
    $url = API . "?mode=insertVisitorBundle&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    echo $resp;
    exit;
}
if ($page == 'checkVisitorApproval') {
	$rid = $_POST['rid']!=""?$_POST['rid']:$_GET['rid'];
	$url      = API . "?mode=checkVisitorApproval&rid=".$rid;
	$response = callAPI($url);
	echo $response;
	exit;
}
if ($page == 'manualConfirmVisitor') {
	$rid = $_POST['rid']!=""?$_POST['rid']:$_GET['rid'];
	$url = API . "?mode=manualConfirmVisitor&rid=".$rid;
	$response = callAPI($url);
	echo $response;
	exit;
}
if ($page == 'resendApprovalEmail') {
	$rid = $_POST['rid']!=""?$_POST['rid']:$_GET['rid'];
	$url = API . "?mode=resendApprovalEmail&rid=".$rid;
	$response = callAPI($url);
	echo $response;
	exit;
}

if ($page == 'finalizeVisitorCheckin') {
	$url = API . "?mode=finalizeVisitorCheckin&rid=".$_POST['rid']."&gate_id=".$_POST['gate_id']."&company_id=".$_POST['company_id']."&user_id=".$_POST['user_id']."&location_id=".$_POST['location_id'];
	$response = callAPI($url);
	echo $response;
	exit;
}

if ($page == 'add_custom_visitor_option') {
    $option_type = isset($_POST['option_type']) ? trim($_POST['option_type']) : '';
    $option_value = isset($_POST['option_value']) ? trim($_POST['option_value']) : '';
    $company_id = $_SESSION['company_id'] ?? 1;
    $user_id = $_SESSION['userid'] ?? 1;
    if (empty($option_type) || empty($option_value)) {
        echo json_encode(['status' => 'error', 'message' => 'Option type and value are required']);
        exit;
    }
    $ins = [
        'company_id' => $company_id,
        'option_type' => $option_type,
        'option_value' => $option_value,
        'option_label' => $option_value,
        'is_active' => 0,
        'created_by' => $user_id,
        'created_date' => date('Y-m-d H:i:s')
    ];

    $api_url = API . "?mode=adddata&table=visitor_options&method=post";
    $response = file_post_contents($api_url, $ins, 'insert');
    $result = json_decode($response, true);
    if (!empty($result) && ($result['status'] === 'success' || $result['status'] === 'sucess')) {
        echo json_encode([
            'status' => 'success',
            'id' => $result['LASTID'] ?? 0,
            'option_value' => $option_value,
            'option_label' => $option_value
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save option in database']);
    }
    exit;
}
//End Omkar Security code 

// Office Module start
if($page=="getstationeryitemsbycategory"){
	$html = '<option value="">Select Item</option>';
	if($_POST['category_id']!="" && $_POST['location_id']!=""){
		$vdata1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'category_id'=>$_POST['category_id'],'location_id'=>$_POST['location_id']);
		
		$vdata1["field"] = "id,item_name";
		$vdata1['order'] = "item_name ASC";
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=gettabledetailswithconditions&table=stationery_items&data=".$vdata;
		$data_details = callAPI($vurl);
		$itemdata = json_decode($data_details,1);
		if(count((array)$itemdata) > 0){
			foreach((array)$itemdata as $item){
				$html .= '<option value="'.$item['id'].'">'.$item['item_name'].'</option>';
			}
		}
	}
	echo $html;
	exit;
}

// Office Module end
//webivew
if($page == "postapprovaldiscussionmessage"){
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    if($_SESSION['work_module_setup_id'] != ''){
        $_POST['module_id'] = $_SESSION['work_module_setup_id'];
    }
    $url = API."?mode=postApprovalDiscussionMessage&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    echo $response;
    exit;
}
if($page == "getapprovaldiscussionmessages"){
	$url = API."?mode=getApprovalDiscussionMessages"
		."&transaction_table=".$_POST['transaction_table']
		."&transaction_id=".$_POST['transaction_id'];
	if($_POST['last_message_id'] != ""){
		$url .= "&last_message_id=".$_POST['last_message_id'];
	}
	echo callAPI($url);
	exit;
}
//webview
//ingegration class
if($page=="updatestationeryitem"){
	unset($_POST['page']);
	$url = API."?mode=updatestationeryitem&method=post";
	$return = file_post_contents($url, $_POST);
	echo $return;
}
// ingegration class

// manufacturing

if ($page == 'get_items_by_category') {
	$item_cat_id = $_POST['item_cat_id'];
	$selected_item_id = $_POST['item_id'] ?? '0';
	$vdataee["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
	if($item_cat_id != '0'){
		$vdataee["where"]['category_id'] = $item_cat_id;
	}
	$vdataee['field'] = "id,item_name";
	$vdata = urlencode(json_encode($vdataee));
	$vurl = API."?mode=gettabledetailswithconditions&table=items&data=".$vdata; 
	$empbalances = callAPI($vurl);
	$empbalances = json_decode($empbalances,1);

	$selected = ($selected_item_id == '0') ? 'selected' : '';
    echo '<option ' . $selected . ' value="0">All</option>';
    foreach ((array)$empbalances as $item) {
        $selected = ($selected_item_id == $item['id']) ? 'selected' : '';
        echo '<option ' . $selected . ' value="' . $item['id'] . '">' . $item['item_name'] . '</option>';
    }
	exit;  
}
if($page == 'getprocessqctransactions'){
    $url = API."?mode=getprocessqctransactions&process=".urlencode($_POST['process']);
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page == 'getprocessqcmapping'){
    $url = API."?mode=getprocessqcmapping&process=".urlencode($_POST['process'])."&table=".urlencode($_POST['process'])."&tranid=".$_POST['tranid']."&item_id=".$_POST['item_id'];
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page == 'getprocessqcworkspace'){
    $url = API."?mode=getprocessqcworkspace&process=".urlencode($_POST['process'])."&tranid=".$_POST['tranid']."&item_id=".$_POST['item_id']."&status=".urlencode($_POST['status'] ?? 'all')."&search=".urlencode($_POST['search'] ?? '')."&page_no=".intval($_POST['page_no'] ?? 1)."&limit=".intval($_POST['limit'] ?? 25);
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page == 'getprocessqcitemform'){
    $url = API."?mode=getprocessqcitemform&process=".urlencode($_POST['process'])."&tranid=".$_POST['tranid']."&item_id=".$_POST['item_id']."&progress_id=".$_POST['progress_id'];
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page == 'getprocessqcsummary'){
    $url = API."?mode=getprocessqcsummary&process=".urlencode($_POST['process'])."&tranid=".$_POST['tranid']."&item_id=".$_POST['item_id'];
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page == 'saveprocessqc'){
    $url = API."?mode=saveprocessqc&method=post";
    $response = file_post_contents($url, $_POST, 'Update');
    echo $response;
    exit;
}
if($page == 'saveprocessqcitemdraft'){
    $url = API."?mode=saveprocessqcitemdraft&method=post";
    $response = file_post_contents($url, $_POST, 'Update');
    echo $response;
    exit;
}
if($page == 'completeprocessqcitem'){
    $url = API."?mode=completeprocessqcitem&method=post";
    $response = file_post_contents($url, $_POST, 'Update');
    echo $response;
    exit;
}


if ($page == 'manufacturing_production_start') {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['created_by'] = $_SESSION['userid'];
    $url = API . "?mode=insertmanufacturingproductionstart&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    echo $response;
    exit;
}
if ($page == 'get_production_details') {
    $_POST['work_order_id'] = $_POST['work_order_id'] ?? 0;
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API . "?mode=getmanufacturingproductiondetails&method=get&work_order_id=" . $_POST['work_order_id'] . "&company_id=" . $_POST['company_id'];
    $response = callAPI($url);
    $result = json_decode($response, 1);

    if ($result['status'] === 'success') {
        include('views/custom_insert_manufacturing_product_modal.php');
    } else {
        echo json_encode($result);
    }
    exit;
}
if ($page == 'manufacturing_production_hold' || $page == 'manufacturing_production_complete') {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['created_by'] = $_SESSION['userid'];
    $_POST['production_type'] = ($page == 'manufacturing_production_hold') ? 'HOLD' : 'COMPLETED';
    $url = API . "?mode=updatemanufacturingproductiondata&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    echo $response;
    exit;
}
if ($page == 'manufacturing_production_resume') {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['created_by'] = $_SESSION['userid'];
    $url = API . "?mode=resumemanufacturingproduction&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    echo $response;
    exit;
}
// manufacturing

//contract excel import
if ($page == 'download_contracts_excel_template') {
	$companyId = (int) ($_SESSION['company_id'] ?? 0);
	$userId = (int) ($_SESSION['userid'] ?? 0);
	$url = API . "?mode=generatecontractsexceltemplate&method=post&company_id={$companyId}&user_id={$userId}";
	$response = file_post_contents($url, array('company_id' => $companyId), 'insert');
	$result = json_decode(trim((string) $response), true);
	if (!is_array($result)) {
		$result = array('status' => 'error', 'message' => 'Invalid template response');
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($result);
	exit;
}

if ($page == 'validate_contracts_excel') {
	$companyId = (int) ($_SESSION['company_id'] ?? 0);
	$userId = (int) ($_SESSION['userid'] ?? 0);
	$uploadDir = __DIR__ . '/uploads/contracts/excel_import/';
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}
	$tempPath = '';
	$fileName = trim((string) ($_POST['file_name'] ?? ''));
	$contractsJson = '';
	$locationsJson = '';
	$assetsJson = '';
	$milestonesJson = '';

	if (isset($_FILES['excel_file']) && is_array($_FILES['excel_file']) && (int) ($_FILES['excel_file']['error'] ?? 1) == 0) {
		$origName = basename((string) ($_FILES['excel_file']['name'] ?? 'import.xlsx'));
		$fileName = $origName;
		$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
		if ($ext != 'xlsx') {
			echo json_encode(array('status' => 'error', 'message' => 'Only .xlsx files are allowed.', 'errors' => array('Only .xlsx files are allowed.')));
			exit;
		}
		$tempPath = $uploadDir . 'batch_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
		if (!move_uploaded_file($_FILES['excel_file']['tmp_name'], $tempPath)) {
			echo json_encode(array('status' => 'error', 'message' => 'Failed to store uploaded file.', 'errors' => array('Failed to store uploaded file.')));
			exit;
		}
		require_once __DIR__ . '/includes/contracts_excel_helper.php';
		try {
			$parsed = cex_parse_contracts_workbook($tempPath);
			$contractsJson = json_encode($parsed['contracts'] ?? array());
			$locationsJson = json_encode($parsed['locations'] ?? array());
			$assetsJson = json_encode($parsed['assets'] ?? array());
			$milestonesJson = json_encode($parsed['milestones'] ?? array());
		} catch (Exception $e) {
			if ($tempPath != '' && file_exists($tempPath)) {
				@unlink($tempPath);
			}
			echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'errors' => array($e->getMessage())));
			exit;
		}
		if ($tempPath != '' && file_exists($tempPath)) {
			@unlink($tempPath);
		}
	} else {
		$contractsJson = (string) ($_POST['contracts_data'] ?? '');
		$locationsJson = (string) ($_POST['locations_data'] ?? '');
		$assetsJson = (string) ($_POST['assets_data'] ?? '');
		$milestonesJson = (string) ($_POST['milestones_data'] ?? '');
	}

	$postData = array(
		'company_id' => $companyId,
		'file_name' => $fileName,
		'contracts_data' => $contractsJson,
		'locations_data' => $locationsJson,
		'assets_data' => $assetsJson,
		'milestones_data' => $milestonesJson,
	);
	$url = API . "?mode=validatecontractsexcel&method=post&company_id={$companyId}&user_id={$userId}";
	$response = file_post_contents($url, $postData, 'insert');
	header('Content-Type: application/json; charset=utf-8');
	echo trim((string) $response);
	exit;
}

if ($page == 'import_contracts_excel') {
	$companyId = (int) ($_SESSION['company_id'] ?? 0);
	$userId = (int) ($_SESSION['userid'] ?? 0);
	$postData = $_POST;
	$postData['company_id'] = $companyId;
	$url = API . "?mode=importcontractsexcel&method=post&company_id={$companyId}&user_id={$userId}";
	$response = file_post_contents($url, $postData, 'insert');
	header('Content-Type: application/json; charset=utf-8');
	echo trim((string) $response);
	exit;
}

if ($page == 'get_contracts_excel_batch') {
	$companyId = (int) ($_SESSION['company_id'] ?? 0);
	$userId = (int) ($_SESSION['userid'] ?? 0);
	$batchId = (int) ($_POST['batch_id'] ?? 0);
	$url = API . "?mode=getcontractsexcelbatch&method=post&company_id={$companyId}&user_id={$userId}&batch_id={$batchId}";
	$postData = array('batch_id' => $batchId, 'company_id' => $companyId);
	$response = file_post_contents($url, $postData, 'insert');
	header('Content-Type: application/json; charset=utf-8');
	echo trim((string) $response);
	exit;
}

if ($page == 'createrecruitingmanagerdashboardcandidatesfolder') {
	$folder = "uploads/candidate_resume_with_rm/resume_rm_".$_SESSION['userid'];
	if (!file_exists($folder)) {
	    if (mkdir($folder, 0777, true)) {
	        chmod($folder, 0777); // Set permissions (may be affected by umask)
	         $result = array('status' => 'Success', 'message' => 'Folder created successfully.'); 
	    } else {
	        $result = array('status' => 'Fail', 'message' => 'Failed to create folder.'); 
	    }
	} else {
		    $result = array('status' => 'Fail', 'message' => 'Folder already exists.'); 
	}
	echo json_encode($result);
}

if($page == "uploadresumefiles"){
	if (!empty($_FILES['resumes_files']['name'][0])) {
		echo $uploadDir = "uploads/candidate_resume_with_rm/resume_rm_".$_SESSION['userid']."/";
	    foreach ($_FILES['resumes_files']['tmp_name'] as $key => $tmpName) {
	        $fileName = basename($_FILES['resumes_files']['name'][$key]);
	        $targetFile = $uploadDir . time() . "_" . $fileName;
	        //move_uploaded_file($tmpName, $targetFile);
	         if (move_uploaded_file($tmpName, $targetFile)) {
	            echo "Uploaded: " . $fileName . "<br>";
	        } else {
	            echo "Failed: " . $fileName . "<br>";
	        }
	    }
	}
}

//contract excel import 

//workflow new
if($page=="getlocationwiseusers"){
	$option = "";
	$location_id = $_POST['location_id'];
	if($location_id != ""){
		$wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'location_id'=>$location_id);
		$wh1["field"] = "id,display_name";
		$data1 = urlencode(json_encode($wh1));
		$url1 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data1; 
		$jsondecode = callAPI($url1);
		$list = json_decode($jsondecode,1);
		if(count((array)$list) > 0){
			foreach($list as $lists){
				$option .= "<option value='".$lists['id']."'>".$lists['display_name']."</option>";
			}
		}
	}
	echo json_encode(array('option'=>$option));
}

//workflow new
if ($page == 'proformainvoicethroughsalesinvoicescreated'){
    $url = API . "?mode=proformainvoicethroughsalesinvoicescreated&transaction_table=" . $_POST['transaction_table'] . "&transaction_no=" . $_POST['transaction_no'];
    $response = callAPI($url);
    echo $response;
    exit;
}
if($page== "chartofaccountlockandunlock"){
	$url = API . "?mode=chartofaccountlockandunlock&ca_status=".$_POST['ca_status']."&ca_id=".$_POST['ca_id'];
    $response = callAPI($url);
    echo $response;
    exit;
}
//Rohan Added Weekly Timesheets code
if($page == "projecttimesheetweekly"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=addupdateprojecttimesheetweekly&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}
if($page == "directapplyweeklyleave"){
	$_POST['user_id'] = $_SESSION['userid'];
	$_POST['company_id'] = $_SESSION['company_id'];
	$url = API."?mode=directapplyweeklyleave&method=post";
	$response = file_post_contents($url, $_POST, 'insert');
	echo $response;
}