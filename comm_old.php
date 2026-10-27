<?php 
/*$url = API."?method=post";
$data = ($url,timezoan());*/
function isacStatusDescrption($value){
	$statusurl = API."?mode=singlerowtabledata&fid=name_value&rid=".$value."&table=".TBL_STATUS;
	$statusres = file_get_contents($statusurl, false, $context);
	$stausresult = json_decode($statusres,1);
	//echo "-----".$value."hahha"; //print_r($stausresult);
	$dgc = "#".strval($stausresult["background_color"]);
	$string = '<span style = "color: #'.$stausresult["color"].';    background: '.$dgc.';" class="kt-badge  kt-badge--inline kt-badge--pill">'.$stausresult["name"].'</span>';
	return $string;
}
function errorMessageUseErrorCode($code){ 
	$resurl = API."?mode=singlerowtabledata&table=".TBL_ERRORMESSAGE."&fid=error_number&rid=". $code;
	//echo $resurl; exit;
	$resresponse = file_get_contents($resurl, false, $context);
	$resresponsedata = json_decode($resresponse,1);
	return $resresponsedata['error_description']; 
}
function deocdeId($id){
	return base64_encode(base64_encode($id));
}
function encodeId($id){
	return base64_decode(base64_decode($id));
}
function initials($str) {
	$ret = '';
	foreach (explode(' ', $str) as $word)
		$ret .= strtoupper($word[0]);
	if(strlen($ret)>2){
		$ret = substr($ret, 0,1).substr($ret, -1);
	}
	return strtoupper($ret);
}
function toGetUserDetais(){
	$url = API."?mode=userdetails&userid=".$_SESSION['userid'];
	$res = callAPI($url);
	return json_decode($res,1);;
}
function candidatedetais($email){
	$statusurl = API."?mode=singlerowtabledata&fid=email_id&rid=".$email."&table=candidates";
	//echo $statusurl; exit;
	$statusres = callAPI($statusurl);
	//echo $statusres; exit;
	$stausresult = json_decode($statusres,1);
	return $stausresult;
}
function vendordetails($email){
	$statusurl = API."?mode=singlerowtabledata&fid=email&rid=".$email."&table=vendors";
	//echo $statusurl; exit;
	$statusres = callAPI($statusurl);
	//echo $statusres; exit;
	$stausresult = json_decode($statusres,1);
	return $stausresult;
}
function dyanamicselectdata($table_name, $filed_name){
	$dsurl = API."?mode=dyanamicselectdata&table_name=".$table_name."&filed_namee=". $filed_name;
	//echo $dsurl;
	//$resresponse = file_get_contents($dsurl, false, $context);
	$resresponse = callAPI($dsurl);
	//echo $dsurl; exit;
	$res = json_decode($resresponse,1);
	if($res['type'] != 'function'){
		$return = $res['res'];
	}
	return $return;
}
function dyanamicselectdatatablename($table_name, $filed_name){
	$data1["where"] = array('application_table_name'=> $table_name,'field_name'=> $filed_name,'company_id'=>$_SESSION['company_id']);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=display_attribute_types&data=".$data; 
	//echo $curl;
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	$json_data = json_decode(urldecode($cudata[0]['content_details']),1);
	return $json_data['table_name'];
}
function dyanamicSelectDataTablenameAllFileds($table_name, $filed_name){
	$data1["where"] = array('application_table_name'=> $table_name,'field_name'=> $filed_name,'company_id'=>$_SESSION['company_id']);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=display_attribute_types&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
//	print_r($cudata[0]);
	return $cudata[0];
}
function dyanamicselectreportdata($report_id, $filed_name){
	$dsurl = API."?mode=dyanamicselectreportdata&report_id=".$report_id."&filed_name=". $filed_name;
	//echo $dsurl; exit;
	//$resresponse = file_get_contents($dsurl, false, $context);
	$resresponse = callAPI($dsurl);
	//echo $resresponse;exit;
	$res = json_decode($resresponse,1);
	$return = $res['res'];
	return $return;
}
function dyanamicafterdata($table_name, $filed_name){
	$dsurl = API."?mode=dyanamicafterdata&&table_name=".$table_name."&filed_namee=". $filed_name;
	//echo $dsurl; exit;
	$resresponse = file_get_contents($dsurl, false, $context);
	$res = json_decode($resresponse,1);
	/*if($res['type'] != 'function'){
		$return = $res['res'];
	}*/
	return $res;
}
function imageupload($jnt,$imagename,$hidimage,$exname=""){
	$name = $hidimage;
	//echo "Ravi".$imagename.$array_number; print_r($_FILES); //exit;
	$exname = ($exname =="") ? "file" : $exname;
	if($_FILES[$imagename]['name']!=""){
		$ext=explode(".",$_FILES[$imagename]['name']);
		$name= $exname.'_'.time()."_".uniqid().'.'.$ext[1];
		$path = 'uploads/'.$jnt.'/';
		if(!is_dir($path)) { 			
			$dual_path = trim($path,'/');	
			$dual_path = explode('/', $dual_path);	
			$epath ="";	
			foreach ($dual_path as $dpath) {	
				$epath = $epath.$dpath."/";	
				if(!is_dir($epath)){	
					mkdir($epath); 	
				}	
			}	

		}	
		$res = move_uploaded_file($_FILES[$imagename]['tmp_name'],$path.$name);
		//echo $res."Ravi"; //exit;
	} 
	return $name;
}
function imageuploadwitharray($jnt,$imagename,$hidimage,$exname="",$array_number=''){	
	
	$name = $hidimage;	
	$exname = ($exname =="") ? "file" : $exname;
	//echo $jnt."--".$imagename."--".$hidimage."---".$exname."---".$array_number; print_r($_FILES);
	//echo $jnt;
	//print_r($_FILES[$imagename]['name']);
	if($_FILES[$imagename]['name']!=""){	
	    //echo "man";
		$ext=explode(".",$_FILES[$imagename]['name']);	
		$name= $exname.'_'.time()."_".uniqid().'.'.$ext[1];	
		$path = 'uploads/'.$jnt.'/';	
		//echo $path.$name; //exit;
		if(!is_dir($path)) { 			
			$dual_path = trim($path,'/');	
			$dual_path = explode('/', $dual_path);	
			$epath ="";	
			foreach ($dual_path as $dpath) {	
				$epath = $epath.$dpath."/";	
				if(!is_dir($epath)){	
					mkdir($epath); 	
				}	
			}	

		}	
		move_uploaded_file($_FILES[$imagename]['tmp_name'],$path.$name);	
	} 	
	return $name;	
}
function toGETUsersList(){
	$userurl = API."?mode=gettabledetails&table=".TBL_USERS;
	$usres = callAPI($userurl);
	$userresult = json_decode($usres,1);
	foreach ($userresult as  $value) {
		$name = $value['display_name']!="" ?$value['display_name']:$value['first_name']." ".$value['middle_name']." ".$value['last_name'];
		$status[$value['id']]= $name;
		$imagess[$value['id']]= $value['user_photo'];
	}
	//print_r($status); exit;
	$_SESSION['user_image'] = $imagess;
	return $status;
}

function toGETStatuList(){
	$userurl = API."?mode=gettabledetails&table=".TBL_STATUS;
	$usres = callAPI($userurl);
	$userresult = json_decode($usres,1);
	foreach ($userresult as  $value) {
		$status[$value['name_value']]= $value;
	}
	return $status;
}
function dateformates($pdate,$formate,$sparater){
			//echo $formate;
	$array = array('DD'=>'d','MM'=>'m','YYYY'=>'Y');
	$formates = explode('-', $formate);
	$return = "";
	foreach ($formates as  $value) {
		$return.= $array[$value].$sparater;
	}
	$return = trim($return,$sparater);

	return date($return, strtotime($pdate) );
}
function fromDate($from='') {
	$from = ($from!='0000-00-00' AND $from!='1970-01-01')? $from :'';
	$_SESSION['date_formate'] = $_SESSION['date_formate']!="0"  ? $_SESSION['date_formate'] : 'Y-m-d';
    //$_SESSION['date_formate'] = $_SESSION['date_formate']!="" ? $_SESSION['date_formate'] : 'Y-m-d';
	if($from != ""){
		$from =  date($_SESSION['date_formate'],strtotime($from));
	}else{
		$from = date($_SESSION['date_formate']);
	}
	//echo $from; exit;
	return $from; 
}
function reqDate($to=''){
	if($to != ""){
		$to =  date($_SESSION['date_formate'],strtotime($to));
	}else{
		//$to = date($_SESSION['date_formate'],strtotime(date("Y-m-d", mktime()) . " + 15 days"));
		$to = date($_SESSION['date_formate'],strtotime(date("Y-m-d") . " + 15 days"));
	}
	return $to;
}
function toDate($to=''){
	$to = $to!='0000-00-00' ? $to :'';
	$_SESSION['date_formate'] = $_SESSION['date_formate']!="0"  ? $_SESSION['date_formate'] : 'Y-m-d';
	if($to != ""){
		$to =  date($_SESSION['date_formate'],strtotime($to));
	}else{
		$to = date($_SESSION['date_formate'],strtotime(date("Y-m-d"). " + 5 year"));
	}
	return $to;
}
function displayDate($to=''){
	if($to != ""){
		$to =  date($_SESSION['date_formate'],strtotime($to));
	}else{
		
		$to = "-";
	}
	return $to;
}
function exportname($name) {
	$title = viewTitle($name);
	return "export_".$title."_".time();
}
function statusData($table){
	$statusurl = API."?mode=statusdata&fid=table_name&rid=".$table."&table=".TBL_STATUSPROGRES;
	//echo $statusurl; 
	$statusres = callAPI($statusurl);
	//$statusres = file_get_contents($statusurl, false, $context);
	$stausresult = json_decode($statusres,1);
	return $stausresult;
}
function nowDate(){
	return date("Y-m-d H:i:s");
}
function ViewStatusData(){
	return $_SESSION['status'];
}
function ViewStatusValue($value,$table_name=''){
	$statusurl = API."?mode=singlestatusdata&status=".$value."&table=".$table_name;
	//echo $statusurl; exit;
	$statusres = callAPI($statusurl);
	$stausresult = json_decode($statusres,1);
	//print_r($stausresult); exit;
	return $stausresult;
}
function toGetStatusProgressAction($table_name, $status){
	$statusurl = API."?mode=toGetStatusProgressActionList&table=".$table_name."&status=".$status;
	//echo $statusurl; exit;
	$statusres = callAPI($statusurl);
	$stausresult = json_decode($statusres,1);
	//print_r($stausresult); exit;
	return $stausresult;

}

function dateFormate($daate){
	$date = $daate !=""?$daate:date("Y-m-d");  // Rohan changes Issue year 1970 show 
	return  date($_SESSION['date_formate'], strtotime($date));
}
function viewTitle ($title){
	$array = array('COM_','LIST_','INSERT_','EDIT_','VIEW_','_');
	foreach ($array as  $value) {
		$title = str_replace($value, ' ', $title);
	}
	//echo "ravi";
	//return strtoupper(str_replace('_',' ',str_replace('INSERT_','',str_replace('COM_', '', $title))));
	//strtolower(str)
	return ucwords(strtolower(str_replace('_', ' ', $title)));
}
// function customeummarmonyformateifine($number){
// 	$num = $_SESSION['config']['number_format'] !='' ? $_SESSION['config']['number_format'] :2;
// 	$return = number_format($number, $num, '.', ','); 
// 	return $return!='0.00' ? $_SESSION['config']['currency_symbol']."".$return : '-'; 
// }
function customeummarmonyformateifinecheck($number){
	if($number!=""){
		if((array)$_SESSION['config']['number_format'] !='' && (array)$_SESSION['config']['currency_symbol']){
			$num = $_SESSION['config']['number_format'] !='' ? $_SESSION['config']['number_format'] :2;
			$return = number_format($number, $num, '.', ','); 
			return $return!='0.00' ? $_SESSION['config']['currency_symbol']." ".$return : '-';	
		}else{
			return $number;
		}
	
	}else{
		return "-";
	}
}
function customeummarmonyformateifine($number){
	if($number!=""){
		$formatvalue = customeummarmonyformate($number);
		return $formatvalue;
		// if((array)$_SESSION['config']['number_format'] !='' && (array)$_SESSION['config']['currency_symbol']){
		// 	$num = $_SESSION['config']['number_format'] !='' ? $_SESSION['config']['number_format'] :2;
		// 	$return = number_format($number, $num, '.', ','); 
		// 	return $return!='0.00' ? $_SESSION['config']['currency_symbol']." ".$return : '-';	
		// }else{
		// 	return $number;
		// }
	
	}else{
		return "-";
	}
	
}
function customeummarmonyformate($number){
	//print_r($_SESSION['config']['number_format']);
	if($number !=""){
		$expoldform = explode('.',$_SESSION['config']['number_format']);
		if($expoldform[0]== 1){
			$formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
		    $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
		    $formattedNumber = $formatter->formatCurrency($number, 'INR');
	 		return $formattedNumber;
		}else{
		 	return number_format($number, $expoldform[1], '.', ',');
		}
	}
}
function customeNumberFormate($number=""){
	//print_r($_SESSION['config']['number_format']);
	if($number !="" AND $number != 'NaN'){
		$expoldform = explode('.',$_SESSION['config']['number_format']);
		if($expoldform[0]== 1){
			$formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
		    $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
		    $formattedNumber = $formatter->formatCurrency($number, 'INR');
		    $formattedCurrencyWithoutSymbol = trim(str_replace($formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL), '', $formattedNumber));
	 		return $formattedCurrencyWithoutSymbol;
		}else{
			return number_format($number, $expoldform[1], '.', ',');
		}
	}else{
		return '0.00';	
	}
}

function customePayslipNumberFormate($number){
	//print_r($_SESSION['config']['number_format']);
	if($number!=0){
		$res = customeNumberFormate($number);
	}else{
		$res = "-";
	}
	return $res;
}

function customonlynumberformate($number){
	$expoldform = explode('.',$_SESSION['config']['number_format']);
	$num = $expoldform[1] !='' ? $expoldform[1] :2;
	$return = number_format($number, $num, '.', ',');
	return $return!='0.00' ? $return : '0.00'; 
}
function invoicePrefix(){
	if($_SESSION['configdata']['invoice_perfix']=='YYYY'){
		$return = date("Y");
	}elseif($_SESSION['configdata']['invoice_perfix']=='MM-YYYY'){
		$return = date("m-Y");
	}elseif($_SESSION['configdata']['invoice_perfix']=='FAYEAR'){
		$year = date("Y");	
		if(date("m")>3){
			$return = $year."-".substr( $year+1, -2);
		}else{
			$return = ($year-1)."-".substr( $year, -2);
		}
	}else{
		$return = "";
	}
	return $return;
}
function itemAttributesDisplayType(){
	$array = array();
	$array["text"]="Text";
	$array["dop_down"]="Drop Down";
	//$array["checkbox"]="Check Box";
	$array["radio"]="Radio";
	return $array;
}
function  dates(){
	$from = fromDate();
	$to= toDate(); 
	echo '<div class="form-group  row">
	<label class="col-form-label col-lg-3 col-sm-12">From Date </label>
	<div class="col-lg-9 col-xl-6">
	<div class="input-group date">
	<div class="input-group-append">
	<span class="input-group-text">
	<i class="la la-calendar-check-o"></i>
	</span>
	</div>
	<input type="text" class="form-control" name="From_Date" value="'. $from.'"  id="kt_datepicker_4_3" />
	</div>
	</div>
	</div>
	<div class="form-group  row">
	<label class="col-form-label col-lg-3 col-sm-12">To Date </label>
	<div class="col-lg-9 col-xl-6">
	<div class="input-group date">
	<div class="input-group-append">
	<span class="input-group-text">
	<i class="la la-calendar-check-o"></i>
	</span>
	</div>
	<input type="text" class="form-control"  name="To_Date" value="'. $to.'"  id="kt_datepicker_4_3_1" />
	</div>
	</div>
	</div>';
}
function file_post_contents($url, $data, $type = '',$username = null, $password = null){
	//echo $url; exit;
	if(!strpos($url,'data_name')){
		$url .= "&data_name=".$_SESSION['DB_NAME'];
	}
	// if(!strpos($url,'extra_paramiter')){
	// 	$url .= "&extra_paramiter=".json_encode($_GET);
	// }
	$url .= "&user_id=".$_SESSION['userid']."&company_id=".$_SESSION['company_id']."&module_id=".$_SESSION['work_module_setup_id']."&module_group_id=".$_SESSION['default_module_group_id']."&ipaddress=".$_SERVER['REMOTE_ADDR']."&page_option=".$_REQUEST['option'];
	
	
	//$url = $url."&data_name=".$_SESSION['DB_NAME']."&user_id=".$_SESSION['userid']."&company_id=".$_SESSION['company_id']."&module_id=".$_SESSION['module_setp'][0]['module_id']."&module_group_id=".$_SESSION['default_module_group_id']."&ipaddress=".$_SERVER['REMOTE_ADDR']."&page_option=".$_GET['option'];
	
	//echo $url;  print_r($_REQUEST); //exit;
	//echo $type; //exit;
	$postdata = http_build_query($data);
	$opts = array('http' =>array(
		'method'  => 'POST',
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'content' => $postdata
	)
);
	$context = stream_context_create($opts);
	$return = file_get_contents($url, false, $context);
	//echo $return; //exit;
	//print_r($return);// exit;
	$cahek_dd = json_decode($return,1);
	if($cahek_dd['status'] == 'sucess'){
		if($type == 'insert'){	$_SESSION['check'] = '3000'; }
		if($type == 'update'){	$_SESSION['check'] = '3001'; }
	}
	//print_r($return); exit;
	return $return;
}
function toGetErrorMessages(){
	//$errorurl = API."?mode=gettabledetails&table=".TBL_ERRORMESSAGE;
	//$erroresponse = callAPI($errorurl);
	//$erroresponse = json_decode($erroresponse,1);
	//print_r($erroresponse)
	
	$wh1["where"] = array('company_id'=>$_SESSION['company_id']);
	$wh1["field"] = "id,message_number,message_description";
	$data1 = urlencode(json_encode($wh1));
    $errorurl = API."?mode=gettabledetailswithconditions&table=".TBL_ERRORMESSAGE."&data=".$data1; 
    $erroresponse = callAPI($errorurl);
    $erroresponse = json_decode($erroresponse,1);
	foreach ($erroresponse as  $errordata) {
		$error_data[$errordata['message_number']] =$errordata['message_description'];
	}
	return $error_data;
}
function toGetRolePermisssionsByUserId($user_id){
	$rolesurl = API."?mode=getrolepermission&user_id=".$user_id;
	//echo $rolesurl; exit;
	$roledata = callAPI($rolesurl);
	//exit;
	//echo $roledata; exit;
	$roledata = json_decode($roledata,1);
	return $roledata;
}
function timezoan() {
	$array = array();
	$array["Pacific/Midway"]="(GMT-11:00) Midway Island, Samoa";
	$array["America/Adak"]="(GMT-10:00) Hawaii-Aleutian";
	$array["Etc/GMT+10"]="(GMT-10:00) Hawaii";
	$array["Pacific/Marquesas"]="(GMT-09:30) Marquesas Islands";
	$array["Pacific/Gambier"]="(GMT-09:00) Gambier Islands";
	$array["America/Anchorage"]="(GMT-09:00) Alaska";
	$array["America/Ensenada"]="(GMT-08:00) Tijuana, Baja California";
	$array["Etc/GMT+8"]="(GMT-08:00) Pitcairn Islands";
	$array["America/Los_Angeles"]="(GMT-08:00) Pacific Time (US & Canada)";
	$array["America/Denver"]="(GMT-07:00) Mountain Time (US & Canada)";
	$array["America/Chihuahua"]="(GMT-07:00) Chihuahua, La Paz, Mazatlan";
	$array["America/Dawson_Creek"]="(GMT-07:00) Arizona";
	$array["America/Belize"]="(GMT-06:00) Saskatchewan, Central America";
	$array["America/Cancun"]="(GMT-06:00) Guadalajara, Mexico City, Monterrey";
	$array["Chile/EasterIsland"]="(GMT-06:00) Easter Island";
	$array["America/Chicago"]="(GMT-06:00) Central Time (US & Canada)";
	$array["America/New_York"]="(GMT-05:00) Eastern Time (US & Canada)";
	$array["America/Havana"]="(GMT-05:00) Cuba";
	$array["America/Bogota"]="(GMT-05:00) Bogota, Lima, Quito, Rio Branco";
	$array["America/Caracas"]="(GMT-04:30) Caracas";
	$array["America/Santiago"]="(GMT-04:00) Santiago";
	$array["America/La_Paz"]="(GMT-04:00) La Paz";
	$array["Atlantic/Stanley"]="(GMT-04:00) Faukland Islands";
	$array["America/Campo_Grande"]="(GMT-04:00) Brazil";
	$array["America/Goose_Bay"]="(GMT-04:00) Atlantic Time (Goose Bay)";
	$array["America/Glace_Bay"]="(GMT-04:00) Atlantic Time (Canada)";
	$array["America/St_Johns"]="(GMT-03:30) Newfoundland";
	$array["America/Araguaina"]="(GMT-03:00) UTC-3";
	$array["America/Montevideo"]="(GMT-03:00) Montevideo";
	$array["America/Miquelon"]="(GMT-03:00) Miquelon, St. Pierre";
	$array["America/Godthab"]="(GMT-03:00) Greenland";
	$array["America/Argentina/Buenos_Aires"]="(GMT-03:00) Buenos Aires";
	$array["America/Sao_Paulo"]="(GMT-03:00) Brasilia";
	$array["America/Noronha"]="(GMT-02:00) Mid-Atlantic";
	$array["Atlantic/Cape_Verde"]="(GMT-01:00) Cape Verde Is.";
	$array["Atlantic/Azores"]="(GMT-01:00) Azores";
	$array["Europe/Belfast"]="(GMT) Greenwich Mean Time : Belfast";
	$array["Europe/Dublin"]="(GMT) Greenwich Mean Time : Dublin";
	$array["Europe/Lisbon"]="(GMT) Greenwich Mean Time : Lisbon";
	$array["Europe/London"]="(GMT) Greenwich Mean Time : London";
	$array["Africa/Abidjan"]="(GMT) Monrovia, Reykjavik";
	$array["Europe/Amsterdam"]="(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna";
	$array["Europe/Belgrade"]="(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague";
	$array["Europe/Brussels"]="(GMT+01:00) Brussels, Copenhagen, Madrid, Paris";
	$array["Africa/Algiers"]="(GMT+01:00) West Central Africa";
	$array["Africa/Windhoek"]="(GMT+01:00) Windhoek";
	$array["Asia/Beirut"]="(GMT+02:00) Beirut";
	$array["Africa/Cairo"]="(GMT+02:00) Cairo";
	$array["Asia/Gaza"]="(GMT+02:00) Gaza";
	$array["Africa/Blantyre"]="(GMT+02:00) Harare, Pretoria";
	$array["Asia/Jerusalem"]="(GMT+02:00) Jerusalem";
	$array["Europe/Minsk"]="(GMT+02:00) Minsk";
	$array["Asia/Damascus"]="(GMT+02:00) Syria";
	$array["Europe/Moscow"]="(GMT+03:00) Moscow, St. Petersburg, Volgograd";
	$array["Africa/Addis_Ababa"]="(GMT+03:00) Nairobi";
	$array["Asia/Tehran"]="(GMT+03:30) Tehran";
	$array["Asia/Dubai"]="(GMT+04:00) Abu Dhabi, Muscat";
	$array["Asia/Yerevan"]="(GMT+04:00) Yerevan";
	$array["Asia/Kabul"]="(GMT+04:30) Kabul";
	$array["Asia/Yekaterinburg"]="(GMT+05:00) Ekaterinburg";
	$array["Asia/Tashkent"]="(GMT+05:00) Tashkent";
	$array["Asia/Kolkata"]="(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi";
	$array["Asia/Katmandu"]="(GMT+05:45) Kathmandu";
	$array["Asia/Dhaka"]="(GMT+06:00) Astana, Dhaka";
	$array["Asia/Novosibirsk"]="(GMT+06:00) Novosibirsk";
	$array["Asia/Rangoon"]="(GMT+06:30) Yangon (Rangoon)";
	$array["Asia/Bangkok"]="(GMT+07:00) Bangkok, Hanoi, Jakarta";
	$array["Asia/Krasnoyarsk"]="(GMT+07:00) Krasnoyarsk";
	$array["Asia/Hong_Kong"]="(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi";
	$array["Asia/Irkutsk"]="(GMT+08:00) Irkutsk, Ulaan Bataar";
	$array["Australia/Perth"]="(GMT+08:00) Perth";
	$array["Australia/Eucla"]="(GMT+08:45) Eucla";
	$array["Asia/Tokyo"]="(GMT+09:00) Osaka, Sapporo, Tokyo";
	$array["Asia/Seoul"]="(GMT+09:00) Seoul";
	$array["Asia/Yakutsk"]="(GMT+09:00) Yakutsk";
	$array["Australia/Adelaide"]="(GMT+09:30) Adelaide";
	$array["Australia/Darwin"]="(GMT+09:30) Darwin";
	$array["Australia/Brisbane"]="(GMT+10:00) Brisbane";
	$array["Australia/Hobart"]="(GMT+10:00) Hobart";
	$array["Asia/Vladivostok"]="(GMT+10:00) Vladivostok";
	$array["Australia/Lord_Howe"]="(GMT+10:30) Lord Howe Island";
	$array["Etc/GMT-11"]="(GMT+11:00) Solomon Is., New Caledonia";
	$array["Asia/Magadan"]="(GMT+11:00) Magadan";
	$array["Pacific/Norfolk"]="(GMT+11:30) Norfolk Island";
	$array["Asia/Anadyr"]="(GMT+12:00) Anadyr, Kamchatka";
	$array["Pacific/Auckland"]="(GMT+12:00) Auckland, Wellington";
	$array["Etc/GMT-12"]="(GMT+12:00) Fiji, Kamchatka, Marshall Is.";
	$array["Pacific/Chatham"]="(GMT+12:45) Chatham Islands";
	$array["Pacific/Tongatapu"]="(GMT+13:00) Nuku'alofa";
	$array["Pacific/Kiritimati"]="(GMT+14:00) Kiritimati";
	return $array;
}
function callAPI($page){
	/*if(!strpos($page,'extra_paramiter')){
		$page .= "&extra_paramiter=".json_encode($_GET);
	}*/
	if(!strpos($page,'page_option')){
		$page .= "&page_option=".$_GET['option'];
	}
	
	if($_SESSION['userid'] == 726){
		$page .= "&check_secure=1";
	}

	$url = $page."&data_name=".$_SESSION['DB_NAME']."&user_id=".$_SESSION['userid']."&company_id=".$_SESSION['company_id']."&module_id=".$_SESSION['work_module_setup_id']."&module_group_id=".$_SESSION['default_module_group_id']."&ipaddress=".$_SERVER['REMOTE_ADDR']."&working_role_short_code=".$_SESSION['working_role_short_code']."&token=".$_SESSION['user_token'];
	//$url = $page."&data_name=".$_SESSION['DB_NAME']."&user_id=".$_SESSION['userid']."&company_id=".$_SESSION['company_id']."&module_id=".$_SESSION['module_setp'][0]['module_id']."&module_group_id=".$_SESSION['default_module_group_id']."&ipaddress=".$_SERVER['REMOTE_ADDR']."&page_option=".$_GET['option'];
	// echo $url;  //exit;
	//extra_paramiter=".json_encode($_GET)
	$res = file_get_contents($url, false, $context);
	//echo $res; //exit;
	//echo "man";
	$datares = json_decode($res,1);
	/*if(isset($datares['status']) && $datares['status']=='token_invalid'){
		header("Location: token_invalid.php");exit;
	}*/
	return $res;
}
function toGetApplicationPagesList(){
	$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
	$wh1['field'] = "application_table_name,list_page_name,insert_page_name,edit_page_name,view_page_name,approval_page_name,insert_page_type,edit_page_type,view_page_type,list_page_type";
	$data1 = urlencode(json_encode($wh1));
    $pages = API."?mode=gettabledetailswithconditions&table=".TBL_APPLICATION_PAGES."&data=".$data1; 
    //$pages = API."?mode=gettabledetails&table=".TBL_APPLICATION_PAGES;
	$page_list = callAPI($pages);
	$res = json_decode($page_list,1);
	return $res;
}
function logout(){
	$pages = API."?mode=logout&user_id=".$_SESSION['userid'];
	//echo $pages;exit;
	$page_list = callAPI($pages);

	$res = json_decode($page_list,1);
	return $res;
}
function toGetTableNameBaseApplicationPagesList($type, $type_name, $case=''){
	//echo "asdlaslda";
	if($case == ''){
		$type_name = strtoupper($type_name);
	}
	foreach ($_SESSION['application_pages'] as $page){
		if($page[$type] == $type_name){
			$data = $page; 
			break;
		}	
	}
	//print_r($data);
	return $data;
}
function toGetMenuDetailsBaseMenuPageName($page){
	//echo $page;
	//echo "<pre>";print_r($_SESSION['application_menu']);
	foreach ($_SESSION['application_menu'] as $pages){
		//print_r($page); //exit;
		//echo $pages['Page_Name']."==". $page."</br>";
		if($pages['page_name'] == $page){
			//echo "ravi"; 
			$data = $pages;
			break;
		}	
	}
	return $data;
}
function toGetIdformateApplicationPagesList(){
	foreach ($_SESSION['application_pages'] as $page){
		$data[$page['id']] = $page;
	}
	return $data;
}
function toGetApplicationMenuListold(){

	$vdata1['order'] = "display_priority ASC";
	$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'aplication_module'=>$_SESSION['config']['application_module'],'is_active'=>0);
	$vdata = urlencode(json_encode($vdata1));
	$menu = API."?mode=gettabledetailswithconditions&table=application_menu&data=".$vdata; 

	$page_list = callAPI($menu);
	//print_r($vdata1); echo  $page_list; exit;
	$res = json_decode($page_list,1);
	return $res;
}
function toGETModuleList(){
		//$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
		
		$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0,'module_id'=>$_SESSION['work_module_setup_id']);
		$vdata = urlencode(json_encode($vdata1));
		$menu = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$vdata; 
		$page_list = callAPI($menu);
		$res = json_decode($page_list,1);
		return $res;
	}
function toGetApplicationMenuList(){
	foreach($_SESSION['module_setp'] as $modu){
		$module[]=$modu['module_id'];
	}
	$_SESSION['module_list'] = $module;
	$vdata1['order'] = "display_priority ASC";
	$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
	$vdata1['where_in'] = $module;
	$vdata1['whr_in_key'] = "aplication_module";
	$vdata = urlencode(json_encode($vdata1));
	$menu = API."?mode=gettabledetailswithconditions&table=application_menu&data=".$vdata;
	$page_list = callAPI($menu);
	//print_r($vdata1); echo  $page_list; exit;
	$res = json_decode($page_list,1);
	return $res;
}
function toGETNavDetailslastest($type){
	$menu  = $_SESSION['application_menu'];
	//echo $type."<pre>"; print_r($_SESSION['role_permission']); echo $type." Menus:"; print_r($menu); //exit;
	//echo "<pre>".$type." Menus:"; print_r($menu);
	foreach ($menu as $key => $value) {
		//echo $value['page_name']."||".$type ."||". $value['parent_menu']."||".$res."</br>"; 		
		if($value['menu_location'] == $type && $value['parent_menu'] == 0){
			//$res = checkPremission($value['page_name']);
			//echo "<br>SHANKAR".$value['page_name']."||".$value['menu_label']."Check Premission:".$res;
			//if($res==1){
			$child=getParentidmenus($value['id'],1);
			$array[$value['id']]['id'] =  $value['id'];
			$array[$value['id']]['Label'] =  $value['menu_label'];
			$array[$value['id']]['Menu_Icon'] =  $value['menu_icon'];
			$array[$value['id']]['Page_Name'] =  $value['page_name'];
			$array[$value['id']]['child'] = $child;
			//}
		}
	}
	//echo $type."<pre>";print_r($array);exit;
	//echo "<pre>check"; print_r($_SESSION['role_permission']);
	
	return $array;
}
function menurecheck($data){
  $array= array();
  foreach($data as $key=>$dat){
	if(count((array)$dat['child'])>0){
	   //$array[$key]=$dat;
	    foreach($dat['child'] as $ddkey=>$dd){
		 	if(count((array)$dd['child'])>0){
				$array[$key]['id'] =  $dat['id'];
				$array[$key]['Label'] =  $dat['Label'];
				$array[$key]['Menu_Icon'] =  $dat['Menu_Icon'];
				$array[$key]['Page_Name'] =  $dat['Page_Name'];
				$array[$key]['dashboard'] = $dat['dashboard'];
		    		$array[$key]['child'][$ddkey]=$dd;
			}else{
			   if($dd['Page_Name']!=""){
				$array[$key]['id'] =  $dat['id'];
				$array[$key]['Label'] =  $dat['Label'];
				$array[$key]['Menu_Icon'] =  $dat['Menu_Icon'];
				$array[$key]['Page_Name'] =  $dat['Page_Name'];
				$array[$key]['dashboard'] = $dat['dashboard'];
				$array[$key]['child'][$ddkey]=$dd;
	 		    }
			}
	    }
	    
	}else{
	  if($dat['Page_Name']!=""){
		$array[$key]=$dat;
	  }
	}
   }
   return $array;
}
function toGETNavDetails($type){
	$menu  = $_SESSION['application_menu'];
	//echo $type."<pre>";print_r($menu); //exit;

	foreach ($menu as $key => $value) { 
		if($value['menu_location'] == $type && $value['parent_menu'] == 0){
			
			if($_SESSION['config']['role_permission']==0){
			  //echo "Ravi";
			  //echo "if the loopt".$key;	
			  if(in_array($value['page_name'],(array)$_SESSION['application_no_permission_pages']['option'])){
			  	$res = 1;
			  }else{
			  	$res = checkPremission($value['page_name']);
			  }
			  
			}else{
			  $res = 1;
			}
			$child = getParentidmenus($value['id'],1);
			if(count((array)$child)>0){
				$res = 1;
			}
		}
		if($value['menu_location'] == $type && $value['parent_menu'] == 0 && $res == 1){
			$array[$value['id']]['id'] =  $value['id'];
			$array[$value['id']]['Label'] =  $value['menu_label'];
			$array[$value['id']]['Menu_Icon'] =  $value['menu_icon'];
			$array[$value['id']]['Page_Name'] =  $value['page_name'];
			$array[$value['id']]['dashboard'] =  $value['dashboard'];
			$array[$value['id']]['child'] = $child;
		} 
	}
	if($_SESSION['config']['role_permission']==0){
		$array = menurecheck($array);
		// if($type=='LN' && $_SESSION['admin_type'] != 1){
		// 	$array = tocheckmenurolepermssion($array);
		// }
	}
	 
	return $array;
}
function tocheckmenurolepermssion($data){
  $array= array();
  $menu_role_id = $_SESSION['menu_role_id'];
 
  foreach($data as $key=>$dat){
	
	if(count((array)$dat['child'])>0 && in_array($dat['id'],(array)$menu_role_id)){
	   $array[$key]=$dat;
	    foreach($dat['child'] as $ddkey=>$dd){
		 	if(count((array)$dd['child'])>0 && in_array($dd['id'],(array)$menu_role_id)){
				$array[$key]['id'] =  $dat['id'];
				$array[$key]['Label'] =  $dat['Label'];
				$array[$key]['Menu_Icon'] =  $dat['Menu_Icon'];
				$array[$key]['Page_Name'] =  $dat['Page_Name'];
		    		$array[$key]['child'][$ddkey]=$dd;
			}
	    }
	    
	}elseif(in_array($dat['id'],(array)$menu_role_id)){
	  if($dat['Page_Name']!=""){
		$array[$key]=$dat;
	  }
	}
   }

   return $array;

}
function checkPremission($name,$table=""){
	$return = 0;
	//echo  $name; //exit;
	//echo "<pre>"; print_r($_SESSION['role_permission']);print_r($_SESSION['role_permission']);
	//rohan added page with paramiter passe tis time not working 
	if(strpos($name,'&')){
		$explode = explode('&', $name);
		$name = $explode[0];
	}
	//print_r($value);
		//	rohan adde
	$commanpages = array('COM_INSERT_DYNAMIC_IMPORT','COM_INSERT_DYNAMIC_FIELD_MAPPING');
	if(in_array($name,$commanpages)){
		$permission_import = $_SESSION['role_permission'][$table]['permission_import'];
		if($permission_import == '0' AND $_SESSION['admin_type'] !='1'){
			$return =1; 
		}
	}else{
		foreach ($_SESSION['application_pages'] as  $value) {
			if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name){
				//$table = $table !=""?$table:$value['application_table_name'];
				$table = $value['application_table_name'] !=""?$value['application_table_name']:$table;
				if($value['list_page_name']==$name){	$type = 'permission_list';	}
				if($value['insert_page_name']==$name){	$approvaltype = 'permission_approval';	}
				if($value['insert_page_name']==$name){	$type = 'permission_insert'; }
				if($value['edit_page_name']==$name){	$type = 'permission_update';	}
				if($value['view_page_name']==$name){	$type = 'permission_list';	}
			}
		}
		//echo "table - ".$table." type - ".$type;
		if(isset($_SESSION['role_permission'][$table][$type]) && $_SESSION['role_permission'][$table][$type] == 0){
			$return =1;
		}
		if($return == 0){
			if( isset($_SESSION['role_page_permission'][$name]) && $_SESSION['role_page_permission'][$name]['permission_list'] == 0){
				$return =1;	
			}
		}

		//approval check
		if(isset($_SESSION['role_permission'][$table][$approvaltype]) && $_SESSION['role_permission'][$table][$approvaltype] == 0){
			$return =1;
		}
	}
	//print_r($_SESSION['role_permission']);	
	//echo $table." -- ".$type." --- ".$return; exit;
	//approval check
	return $return; 
}
function checkPremissionlastet($name){
	$return = 0;
	//echo  "Ravi".$name."<br>"; //exit;
	//echo "<pre>check"; print_r($_SESSION['role_permission']);
	foreach ($_SESSION['application_pages'] as  $value) {
		if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name){
			$table = $value['application_table_name'];
			if($value['list_page_name']==$name){ $lname= $value['list_page_name'];	$type = 'permission_list';	}
			if($value['insert_page_name']==$name){ $iname= $value['insert_page_name']; $type = 'permission_insert';	}
			if($value['edit_page_name']==$name){	$ename= $value['edit_page_name']; $type = 'permission_update';	}
			if($value['view_page_name']==$name){	$vname= $value['view_page_name']; $type = 'permission_list';	}
			
		}
	}
	//echo $type."--".$name; echo $table; //exit;
	if($_SESSION['role_permission'][$name][$type] == 1 || $_SESSION['role_permission'][$lname][$type] == 1 || $_SESSION['role_permission'][$iname][$type] == 1 || $_SESSION['role_permission'][$ename][$type] == 1 || $_SESSION['role_permission'][$vname][$type] == 1 ){
		$return =1;
	}
	//echo "<br>".$name."----".$return; //exit;
	return $return; //exit;
}
function toGetTableNameUsingPageName($name){
	$return = "";
	foreach ($_SESSION['application_pages'] as  $value) {
		if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name){
			$return = $value['application_table_name'];
		}	
	}
	return $return; //exit;
}
function getParentidmenus($pid,$len){
	$menu  = $_SESSION['application_menu'];
	$array1 = array();
	foreach ($menu as $key => $value) {
		//$res = checkPremission($value['page_name']);
	
		$res = 1;
		if($value['parent_menu'] == $pid && $_SESSION['config']['role_permission']==0){
			$child = array();
			$child = getParentidmenuscheck($value['id'],$len+1);
			if(count((array)$child)>0){
	 			$res=1;
			}else{
			  if(in_array($value['page_name'],(array)$_SESSION['application_no_permission_pages']['option'])){
			  	$res = 1;
			  }else{
			  	$res=checkPremission($value['page_name']);
			  }
			}
		}

		if($value['parent_menu'] == $pid && $res == 1){
			$array1[$value['id']]['id'] =  $value['id'];
			$array1[$value['id']]['Label'] =  $value['menu_label'];
			$array1[$value['id']]['Menu_Icon'] =  $value['menu_icon'];
			$array1[$value['id']]['Page_Name'] =  $value['page_name'];
			$array1[$value['id']]['dashboard'] =  $value['dashboard'];
			$array1[$value['id']]['child'] = getParentidmenus($value['id'],$len+1);
		}
	}
	return $array1; 
}
function getParentidmenuscheck($pid,$len){
	$menu  = $_SESSION['application_menu'];
	foreach ($menu as $key => $value) {
	if($value['parent_menu'] == $pid){
		$array1[$value['id']]['id'] =  $value['id'];
		$array1[$value['id']]['Label'] =  $value['menu_label'];
		$array1[$value['id']]['Menu_Icon'] =  $value['menu_icon'];
		$array1[$value['id']]['Page_Name'] =  $value['page_name'];
		//$array1[$value[id]]['child'] = getParentidmenus($value['id'],$len+1);
		break;
	}
	}
	return $array1;
}
function togetnotificationuserslist(){
	return "3";
}
function toGetNotigficationListByUsingUserId($user_id,$type=""){
	$url_noti = API."?mode=toGetNotigficationListByUsingUserId&user_id=$user_id&type=$type";
	$res = callAPI($url_noti);
	$res = json_decode($res,1);
	return $res;
}
function toGetNotigficationListByUsingUserIdAndGrpId($user_id,$type="",$defaultgrpid=""){
	$url_noti = API."?mode=toGetNotigficationListByUsingUserId&user_id=$user_id&type=$type";
	$res = callAPI($url_noti);
	$res = json_decode($res,1);
	$notifiction = array();
	$grpsum = array();
	foreach($res as $restl){
		$grpsum[$restl['module_group_id']]++;
		if($restl['module_group_id'] == $defaultgrpid AND $defaultgrpid !=""){
			$notifiction[] = $restl;
		}
	}
	$retrun['grpsum'] = $grpsum;
	$retrun['notifiction'] = $notifiction;
	return $retrun;
}
function toGetInboxCountUsingUserId(){
	$url_noti = API."?mode=toGetInboxCountUsingUserId"; 
	$res = callAPI($url_noti);
	$res = json_decode($res,1);
	return $res;
}
function notificationdatesdiff($date,$cond="",$ladate=""){	
	$date2 = strtotime($date); 
	if($ladate!=""){
		$date1 = strtotime($ladate); 
	}else{
		$date1 = strtotime(date("Y-m-d h:i:sa")); 
	}
// Formulate the Difference between two dates 
	$diff = abs($date2 - $date1); 
// To get the year divide the resultant date into // total seconds in a year (365*60*60*24) 
	$years = floor($diff / (365*60*60*24));
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
// To get the day, subtract it with years and 
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
// To get the hour, subtract it with years, 
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
	$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the 
// resultant date into total seconds i.e. 60 
	$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes 
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60)); 
// Print the result 
//printf("%d years, %d months, %d days, %d hours, "	. "%d minutes, %d seconds",$years, $months, $days, $hours, $minutes, $seconds);
	$str='';
	if($years >0){
		$str .= $years." years, ";
	}
	if($years >0){
		$str .= $months." months, ";
	}
	if($days >0){
		$str .= $days." days ";
	}
	if($cond !='N' || $str==""){
		if($hours >0){
			$str .= $hours." hrs, ";
		}
		if($minutes >0){
			$str .= $minutes." min";
		}else{
			$str .="1 min";
		}
	}
	return trim($str,",");
}
function numberToWordFormateRupees($number){

	$no = floor($number);
	$point = round($number - $no, 2) * 100;
	$hundred = null;
	$digits_1 = strlen($no);
	$i = 0;
	$str = array();
	$words = array('0' => '', '1' => 'one', '2' => 'two',
		'3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
		'7' => 'seven', '8' => 'eight', '9' => 'nine',
		'10' => 'ten', '11' => 'eleven', '12' => 'twelve',
		'13' => 'thirteen', '14' => 'fourteen',
		'15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
		'18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
		'30' => 'thirty', '40' => 'forty', '50' => 'fifty',
		'60' => 'sixty', '70' => 'seventy',
		'80' => 'eighty', '90' => 'ninety');
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
	while ($i < $digits_1) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) {
			$plural = (($counter = count((array)$str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? $words[$number] .
			" " . $digits[$counter] . $plural . " " . $hundred
			:
			$words[floor($number / 10) * 10]
			. " " . $words[$number % 10] . " "
			. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
	}
	$str = array_reverse((array)$str);
	$result = implode('', (array)$str);
	$points = ($point) ?
	". " . $words[$point / 10] . " " . 
	$words[$point = $point % 10] : '';
	if($result !=''){
		$result = $result . "Rupees  ";
	}
	if($points !=''){
		$result.= $points . " Paise  ";
	}
	echo ucwords($result." only");
}
function toGetCountryNameBaseCountryID($id){
	//echo "hai||".$id; 
	$cu = API."?mode=singlerowtabledata&rid=$id&table=".TBL_COUNREIES;
	//echo $cu;
	$res = callAPI($cu);
	//echo $res;
	$res = json_decode($res,1);
	//print_r($res);
	return $res['country_name'];
}
function toGETConfigartion(){
	$configurl = API."?mode=singlerowtabledata&fid=company_id&rid=".$_SESSION['company_id']."&table=".TBL_CONFIG;
	//echo $configurl; 
	$configesponse = callAPI($configurl);
	$configdata = json_decode($configesponse,1);
	//print_r($configdata); exit;
	return $configdata;
}
function toGetTaxrates(){
	/*$configurl = API."?mode=toGetTaxrates";
	$configesponse = callAPI($configurl);
	$configdata = json_decode($configesponse,1);
	foreach($configdata as $data){
		$array[$data['id']] = $data['tax_rate']*100 . "Percentage";
	}
	$configurl = API."?mode=gettabledetails&table=".TBL_TAX_AUTHORITIES_RATES;
	$configesponse = callAPI($configurl);
	$configdata = json_decode($configesponse,1);
	foreach($configdata as $data){
		$array[$data['id']] = $data['tax_rate']*100 . " %";
	}
	return $array;*/
	//return array('2.5'=>"2.5% Percentage",'5'=>"5% Percentage",'10'=>"10% Percentage", '18'=>"18% Percentage",'20'=>"20% Percentage",'28'=>"28% Percentage");
	return array('2.5'=>"2.5%",'5'=>"5%",'10'=>"10%", '18'=>"18%",'20'=>"20%",'28'=>"28%");
}
function toCreateInvoicePages(){
	if(count((array)$_SESSION['invoice_pages']) > 0){ 
		$configesponse = $_SESSION['invoice_pages'];
	}else{ 
		$configurl = API."?mode=gettabledetails&table=".TBL_DYNAMIC_MAPPING_TABLES;
	 	$configesponse = callAPI($configurl);
		$configesponse = json_decode($configesponse,1);
		$_SESSION['invoice_pages'] = $configesponse;
	}
	foreach($configesponse AS $con){
		$table_list[$con['option_url']] ['table']= $con['application_table_name'];
		$table_list[$con['option_url']] ['title']= $con['page_title'];
		$table_list[$con['option_url']] ['display_table']= $con['display_table'];
		$table_list[$con['option_url']] ['display_page']= $con['display_page'];
	}

    //print_r($table_list); exit;
	return $table_list;
}
function toGetpageOptionName($table, $page_option=""){
	$vdata1["where"] = array('application_table_name'=> $table);
	if($page_option!=""){
		$vdata1["where"]['option_url'] =  $page_option;
	}
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_DYNAMIC_MAPPING_TABLES."&data=".$vdata; 
	$configesponse = callAPI($vurl);
	$return = json_decode($configesponse,1);
	//print_r($return);exit;
	return $return[0];

}
function toGETactionloglastetrecards(){
	$vdata1['order'] = "id DESC";
	$vdata1['limit'] = "0,5";
	$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'user_id'=>$_SESSION['userid']);
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_ACTION_LOG."&data=".$vdata; 
   // echo $vurl;
	$configesponse = callAPI($vurl);
	return json_decode($configesponse,1);
	
}
function togetaprovaluserlist($table_name, $rid){
	$vdata1["where"] = array('transaction_table'=> $table_name,'transaction_id'=>$rid,'company_id'=>$_SESSION['company_id']);
	$vdata = urlencode(json_encode($vdata1));
   // print_r($vdata1);
	$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_TRANSACTION_APPROVAL_LOGS."&data=".$vdata; 
	$res = callAPI($vurl);
	//echo $table_name."|||||".$rid; exit;
	$res = json_decode($res,1);
	//echo $res;
	foreach($res as $data){
		//print_r($data);
		if($data['response_type']=="0"){
			$user[]= $data['user_id'];
		}
		$approvaldata[$data['user_id']] = $data;
	}
	$return['user']= $user;
	$return['details']= $approvaldata;
	return $return;
}
function toGetPendingRequisitionQty($table){
	//echo "hai adasd asd ad asd as d asd asd asdas d asd asdasd as d asd as d as d asdasdasd asd"; exit;
	$vurl = API."?mode=toGetPendingRequisitionQty&table=".$table; 
	//echo $vurl; //exit;
	$res = callAPI($vurl);
	return json_decode($res,1); 
}
function toGetApplicationSerialNumber($table_name){
	//echo $table_name;
	$vdata1["where"] = array('application_table_name'=> $table_name,'is_active'=> 0);
	$vdata = urlencode(json_encode($vdata1));
	$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_APPLICATION_SERIAL_NUMBERS."&data=".$vdata;
   // echo $vurl; 
	$res = callAPI($vurl);
	$res = json_decode($res,1);
	$res = $res[0] ;
	$le = $res['doc_no_length'] ;
	$var = "%0".$le."d";
	$data = $res['pre_fix'].sprintf($var,$res['serial_number']+1).$res['post_fix'];
	return trim($data);
}
function toGetCompanyList(){
	$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_COMPANIES; 
	$res = callAPI($vurl);
	$res = json_decode($res,1);
	foreach($res as $data){
		//print_r($data);
		$datas[$data['id']]= $data['name'];
	}
	return $datas;
}
function NotificationRead($id){
	$data = array('read_status' => 1);
	$data = urlencode(json_encode($data));
	$vurl = API."?mode=notification_read&id=".encodeId($id); 
	//echo $vurl; exit;
	$res = callAPI($vurl);
}
function fiscalmonth($month){
	$array = array('01'=> '10','02'=> '11','03'=> '12','04'=> '1','05'=> '2','06'=> '3','07'=> '4','08'=> '5','09'=> '6','10'=> '7','11'=> '8','12'=> '9');
	return $array[$month];
}
function dateDiffInDays($date1, $date2 = '')  
{ 
	if($date2 ==''){ $date2 = date("Y-m-d h:i:sa"); }
	$diff = strtotime($date2) - strtotime($date1); 
	return abs(round($diff / 86400)); 
}
function Insertqueries($post){
    //print_r($post);//exit;
	//$dats = $post;
	$dats['query_type'] = $post['query_type'];
	$dats['query_text'] = $post['query_text'];
	$dats['table_name'] = $post['table_name'];
	$dats['transaction_id'] = $post['transaction_id'];
	$dats['parent_query'] = $post['parent_query'];
	$dats['created_by'] = $post['created_by'];
	$dats['new_reply'] = $post['new_reply'];
	$dats['company_id'] = $_SESSION['company_id'];
	$dats['is_active'] = 0;
	$dats['current_status'] = 0;
    //$dats['created_date'] = nowDate();
	$post['option'] =  $_GET['option'];
	$dats['originator'] = $post['send_id'];
	$post['company_id'] =  $_SESSION['company_id']; 
	unset($dats['submit']);
	unset($dats['trno']);
	unset($dats['send_id']);
    //print_r($dats);exit;
	$url = API."?mode=adddata&table=".TBL_TRANSACTION_QUERIES."&method=post";
	$response = file_post_contents($url, $dats, 'insert');
   /* echo $response;
   print_r($post); exit;*/
   $post['user_id'] =  $_SESSION['userid']; 
   $notiurrl = API."?mode=querynotificationandemail&method=post";
   $res = file_post_contents($notiurrl, $post, 'insert');
   return $res;
}
function updatequeries($da){
	$data['new_reply'] = $da['query_reply'];
	$data['updated_by'] = $da['user_id'];
	$data['updated_date'] = nowDate();

	//print_r($data); exit;
	$url = API."?mode=updaterowtabledata&rid=".$da['reply_id']."&table=".TBL_TRANSACTION_QUERIES."&method=post";
	//echo $url; exit;
	$response = file_post_contents($url, $data, 'update');
	return $response;
}
function insertatchments($data){
	/*$url = API."?mode=adddata&table=".TBL_APPLICATION_ATTACHMENTS."&method=post";

	//echo $url; exit;
	$response = file_post_contents($url, $data, 'update');
	//echo $data; exit;*/
	$vdata = urlencode(json_encode($data));
	$vurl = API."?mode=adddata&table=".TBL_APPLICATION_ATTACHMENTS."&data=".$vdata;
    //echo $vurl;  exit;
	$res = callAPI($vurl);
	return $response;
}

function validategstin($gstNumber){
	$GSTINFORMAT_REGEX = "/[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9A-Za-z]{1}[Z]{1}[0-9a-zA-Z]{1}/";
	if(preg_match($GSTINFORMAT_REGEX, $gstNumber)){
		if(gst_error_check($gstNumber) == $gstNumber){
			return(1);
		}else{
			return(0);
		}

	}else{
		return(0);
	}	
}
function gst_error_check($gst_number){
	$GSTN_CODEPOINT_CHARS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$input =  str_split($gst_number);
	$inputChars = $input;
	unset($inputChars[14]); 
	$factor = 2;
	$sum = 0;
	$checkCodePoint = 0;
	$cpChars = str_split($GSTN_CODEPOINT_CHARS);
	$mod = count((array)$cpChars);
	for ($i = count((array)$inputChars) - 1; $i >= 0; $i--) {
		$codePoint = -1;
		for ($j = 0; $j < count((array)$cpChars); $j++) {
			if ($cpChars[$j] == $inputChars[$i]) {
				$codePoint = $j;
			}
		}
		$digit = $factor * $codePoint;
		$factor = ($factor == 2) ? 1 : 2;
		$x =  ($digit % $mod);
			#echo $x;
		$digit = (int) ($digit / $mod) + (int) ($digit % $mod);
		$sum += $digit;
	}
	$checkCodePoint = ($mod - ($sum % $mod)) % $mod;
	$inputChars = implode('', (array)$inputChars);
	return($inputChars.$cpChars[$checkCodePoint]);
}
function emaildatareplaceNew($data,$val){
	foreach($data as $fieldkey=>$result){
		$inputFildsArray["{".trim($fieldkey)."}"] = trim($result);
	}
	$str = "";
	foreach($inputFildsArray as $key => $value){
		$repl =  str_replace(trim($key), trim($value), trim($val));
		$val = $repl;
	}
	return $val;
}
function emaildatareplacedd($data, $mesg){
	print_r($data);
	$mes1= strpos($mesg,'{[')+2;
	$mes2= strpos($mesg,']}');
	if($mes2 == ''){ $mes2=0; }
		//$mesg= "11qww".$mes1."ppp".$mes2; //exit;
	echo $mes1."###".$mes2; 
	if($mes1>0 && $mes2>0){
		    echo "Ravi";//exit;
		    $str =''; $sstr ='';
		    $str = strtolower(substr($mesg,$mes1,$mes2-$mes1));
		    $sstr = "{[".$str."]}";
		    $mesg = str_replace($sstr, $data[$str], $mesg);
		    $mesg = emaildatareplace($data, $mesg);

		} 
		//echo $mesg; exit;
		return $mesg;
	}
	function emaildatareplace($data, $mesg){
		//print_r($data);
		$mes1= strpos($mesg,'{[')+2;
		$mes2= strpos($mesg,']}');
		if($mes2 == ''){ $mes2=0; }
		//$mesg= "11qww".$mes1."ppp".$mes2; //exit;
		//echo $mes1."###".$mes2;
		$count = substr_count((array)$mesg, "{[")+2;
		for($i=0;$i<$count;$i++){
			if($mes1>0 && $mes2>0){
		 //   echo "Ravi";//exit;
				$str =''; $sstr ='';
				$str = strtolower(substr($mesg,$mes1,$mes2-$mes1));
				$sstr = "{[".$str."]}";
				$mesg = str_replace($sstr, $data[$str], $mesg);
			//$mesg = emaildatareplace($data, $mesg);
				$mes1= strpos($mesg,'{[')+2;
				$mes2= strpos($mesg,']}');
			} else{
				break;
			}
		}
		//echo $mesg; exit;
		return $mesg;
	}
	function emailnumberschange($mesg){

		$mes1= strpos($mesg,'[')+1;
		$mes2= strpos($mesg,']');
		if($mes2 == ''){ $mes2=0; }
	//echo "11qww".$mes1."ppp".$mes2;
		if($mes1>0 && $mes2>0){
			$str =''; $sstr ='';
			$str = substr($mesg,$mes1,$mes2-$mes1);
			$sstr = "[".$str."]";
			$mesg = str_replace($sstr, $this->numberToWordFormateRupees($str), $mesg);
			$mesg = $this->emaildatareplace($data, $mesg);	
		} 
		return $mesg;
	}
	function dyanamicserialnumber($table){
		$vurl = API."?mode=dyanamicserialnumber&table=".$table;
    //echo $vurl;  exit;
		$res = callAPI($vurl);
		return json_decode($res);
	}
	function toGetclockdata(){
		$vdata1["where"] = array('is_active'=> '0', 'employee_id'=> $_SESSION['emp_id'], 'attendace_date'=>date('Y-m-d'));
 		//print_r($vdata1); exit;
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=gettabledetailswithconditions&table=employee_daily_attendances&data=".$vdata; 
		//echo " Before Clock in out details - ".date('m/d/Y h:i:s a')."</br>";
		$configesponse = callAPI($vurl);
		$return = json_decode($configesponse,1);
		//print_r($return);exit;
		//echo " After Clock in out details - ".date('m/d/Y h:i:s a')."</br>";
		return $return[0];
	}
	function toGetEmployeeData(){
		$vdata1["where"] = array('is_active'=> '0', 'user_id'=>$_SESSION['userid']);
 	//print_r($vdata1); exit;
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata; 
		$configesponse = callAPI($vurl);
		$return = json_decode($configesponse,1);
	//print_r($return);exit;
		return $return[0];
	}
	function phonenumberdisplay($result){

		return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $result);
	//return $result;
	}
	function toupdateuserconfirmedbasicinfo(){
		$updata['confirmed_basic_ino'] = '0';
		$url = API."?mode=updaterowtabledata&rid=".$_SESSION['userid']."&table=".TBL_USERS."&method=post";
		$response = file_post_contents($url, $updata, 'update');
	}
	function toupdateemployee($name){

		$updata['image'] = $name;
	//print_r($data); exit;
		$url = API."?mode=updaterowtabledata&rid=".$_SESSION['emp_id']."&table=employees&method=post";
	///echo $url; exit;
		$response = file_post_contents($url, $updata, 'update');
		return $response; 
	}
	function todisplaystatuslist($data){
  		//print_r($data); exit;
		$wlist =array();
		if($data['table']!="" && $data['current_status']!=""){
			$wlist_url =  API."?mode=wrokflowlist&table=".$data['table']."&amount=".$data['grand_total'];
	   		// echo $wlist_url; exit;
			$wlist = callAPI($wlist_url);
			$wlist = json_decode($wlist,1);
		}
		$dis_str='';
		if(count((array)$wlist)>0){
			
			$dis_str.='<div class="p-3"><div class="breadcrumb">';
			$checkcurrent_status = $data['current_status'];
			if($checkcurrent_status == ST_INACTIVE || $checkcurrent_status== ST_DRAFT ){
				$checkcurrent_status =-1;
			}
			$wlcount = count((array)$wlist);$cw=1;$ork=0;
			foreach ($wlist as $pwlist) {
				if($pwlist['name']!=""){
					$orcolor =""; $oricon="";
					if($checkcurrent_status >=$pwlist['status']){    
						$orcolor = "text-success";
						$oricon = '<i class="far fa-check-circle text-success"></i> &nbsp;';
					}else{
						if($ork==0){
							$orcolor = "text-primary";
							$oricon = '<i class="far fa-hourglass text-primary"></i> &nbsp;';
							$ork =1;
						}
					}
					$dis_str.='<a href="#"><span class="breadcrumb__inner"><span class="breadcrumb__title '.$orcolor.'">';         
					$dis_str.=$oricon.$pwlist['action_label'];
					$dis_str.='</span> </span></a>';
					$cw+1;
				}
			} 
			$dis_str.='</div> </div>';
		} 
		return $dis_str;
	}
	
	function todisplaythree($data){
		//print_r($data);
		$diss="";
		if($data['id']!="" && $data['id']!="0"){
			if($data['encode']=='no'){
				$data['value_id'] = $data['id'];
				$data['transaction_number'] = $data['id'];
				$data['transaction_id'] = $data['id'];
			}else{
				$rid =  encodeId($data['id']);
				$rid = urldecode($rid);
				$erid = explode(':', $rid);
				$data['value_id'] = $erid[1];
				$data['transaction_number'] = $erid[1];
				$data['transaction_id'] = $erid[1];
			}
			//print_r($data);
			echo '<div class="container-fluid "><div class="card card-custom"><div class="card-body"><div class="row">';
			if($data['dis_query']=='yes'){	$diss.= todisplayquerylist($data); }
			if($data['dis_timeline']=='yes'){ $diss.= todisplaytimeline($data); }
			if($data['dis_comments']=='yes'){	$diss.= todiplaycomments($data); }
			if($data['dis_track']=='yes'){	$diss.= todisplaytracking($data); }
			$diss.='</div></div></div></div>';
		}
		return $diss;
	}
	function todisplayquerylist($data){
    	//print_r($data); exit;
		$question_display ='Y';
		$replay_display = 'N';
		$trnsation_no = $data['transaction_number'];
		if($_SESSION['userid'] == $data['originator']){
			$question_display ='N';
			$replay_display = 'Y';
		}
		echo '<div class="col-lg-6">';
		include_once("views/custom_query.php"); 
		echo '</div>'; 
	}
	function todisplaytimeline($data){
  		// print_r($data);
		$vdata1["where"] = array('transaction_primary_1'=> $data['value_id'],'transaction_table'=>$data['table'],'company_id'=>$_SESSION['company_id']);
		$vdata1["field"] = "id,transaction_type,user_id,action_log,action_date,status_value";
		$vdata1['order'] = "id ASC";
		$vdata = urlencode(json_encode($vdata1));
		$vurl = API."?mode=gettabledetailswithconditions&table=".TBL_ACTION_LOG."&data=".$vdata; 
		//print_r($vdata1); echo $vurl;
		$vdata_details = callAPI($vurl);
		$vdata_details = json_decode($vdata_details,1);
    		//print_r($vdata1);
		$dis_str ='';
   		 //$dis_str.='<div class="col-lg-12">';
		$dis_str.='<div class="accordion accordion-solid mt-2 accordion-toggle-arrow col-lg-6" id="accordionExample2">                    
		<div class="card latest-update-card acc-bgc">
		<div class="card-header" id="headingTwo2">
		<div class="card-title scale_active collapsed f-16 f-w-600" data-bs-toggle="collapse" data-target="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2" href="#collapseTwo2">
		Transaction Timeline <!--<b class="f-24 float-right">+</b>-->
		</div>
		</div>
		<div id="collapseTwo2" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionExample2">';
		$dis_str.='
		<div class="latest-update-card">                                            
            <div class="card-block">
                <div class="" style=""><div class="" style="">
                    <div class="latest-update-box custom-scroll" style="height:400px">';
                    foreach ($vdata_details as  $vdvalue) {
        				$dis_str.='  <div class="row  p-b-30">
                            <div class="col-auto text-end update-meta p-r-0">';
                            	if($_SESSION['user_image'][$vdvalue['user_id']]==""){
                               $dis_str.=' <div class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['user_id']].'"> <span class="font-size-h3 font-weight-boldest ">'.initials($_SESSION['users'][$vdvalue['user_id']]).'</span></div>';
                           		}else{ 
                           			$dis_str.=' <img src="uploads/all/'.$_SESSION['user_image'][$vdvalue['user_id']].'" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['user_id']].'">';
                           		}
                            $dis_str.='  </div>
                            <div class="col p-l-5">
                               
                                <p class="text-muted m-b-0"><span style="font-size:14px;">'.$_SESSION['users'][$vdvalue['user_id']].'&nbsp; Record Status Updated to '.$_SESSION['status'][$vdvalue['status_value']]['name'].'</span></br>'.date("M d, Y g:i A", strtotime($vdvalue['action_date'])).'</p>
                            </div>
                        </div>';
             		}
            $dis_str.='</div></div></div></div></div></div></div></div>';
		return $dis_str;
	}
	function todiplaycomments($data){

		$appruser_list_data = togetaprovaluserlist($data['table'], $data['value_id']);
		$appruser_list = $appruser_list_data['user'];
		$appruser_data =  $appruser_list_data['details'];
		$dis_str ='';
    		//	print_r($appruser_data);
		if(count((array)$appruser_data) > 0){
			$stausresult = ViewStatusData();

			$dis_str.='<div class="col-lg-6">  
			<div class="accordion accordion-solid mt-2 accordion-toggle-arrow" id="accordionExample2">
			<div class="card acc-bgc">
			<div class="card-header acc-bgc" id="headingTwo21">
			<div class="card-title scale_active collapsed f-16 f-w-600" data-bs-toggle="collapse" data-target="#collapseTwo21" aria-expanded="false" aria-controls="collapseTwo21" href="#collapseTwo21">
			Approval Logs <!--<b class="f-24 float-right">+</b>-->
			</div>
			</div>
			<div id="collapseTwo21" class="collapse" aria-labelledby="headingTwo21" data-parent="#accordionExample2">';

			$dis_str.='
		<div class="latest-update-card">                                            
            <div class="card-block">
                <div class="" style=""><div class="" style="">
                    <div class="latest-update-box">';
                    foreach ($appruser_data as  $lists) { 
                    	if($lists['response_type'] == 0){ $list_date = $lists['request_date']; }else{  $list_date = $lists['response_date']; }
                    	if($lists['response_type'] == 0){ $sss ='Yet to respond'; }else{ $sss =$stausresult[$lists['response_type']]['name']; }
        				$dis_str.='  <div class="row  p-b-30">
                            <div class="col-auto text-end update-meta p-r-0">';
                            	if($_SESSION['user_image'][$lists['user_id']]==""){
                               $dis_str.=' <div class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$lists['user_id']].'"> <span class="font-size-h3 font-weight-boldest ">'.initials($_SESSION['users'][$lists['user_id']]).'</span>   </div>';
                           		}else{
                           			$dis_str.=' <img src="uploads/all/'.$_SESSION['user_image'][$lists['user_id']].'" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$lists['user_id']].'">';
                           		}
                            $dis_str.='  </div>
                            <div class="col p-l-5">
                                <a href="#!"><h6>'.$_SESSION['users'][$lists['user_id']].' - Record Status is '.$sss.'</h6></a>
                                <p class="text-muted m-b-0">&nbsp;'.date("M d, Y g:i A", strtotime($list_date)).'</p>';
                                if($lists['comments']!=""){
                            $dis_str.='    <p class="text-muted m-b-0"><span class="f-w-600">Comments:</span>&nbsp;'.$lists['comments'].'</p>';
                        		}
                           $dis_str.=' </div>
                        </div>';
             		}
            $dis_str.='</div></div></div></div></div></div></div></div></div>';
		} 
		return $dis_str;
	}
	// function todisplaytracking($data){
	// 	//$vurl = API."?mode=gettrackingdynamic"; 
	// 	$vurl = API."?mode=gettrackingdynamic&table=".$data['table']."&transaction_id=".$data['transaction_id'];
	// 	//echo $vurl;
	// 	$vdata_details = callAPI($vurl);
	// 	$vdata_details = json_decode($vdata_details,1);
	// 	print_r($vdata_details);	
	// 	$dis_str ='';
	// 	$dis_str.='<div class="accordion accordion-solid mt-2 accordion-toggle-arrow col-lg-6" id="accordionExample4">                    
	// 	<div class="card latest-update-card acc-bgc">
	// 	<div class="card-header " id="headingTwo2">
	// 	<div class="card-title collapsed f-16 f-w-600" data-bs-toggle="collapse" data-target="#collapseTwo6" aria-expanded="false" aria-controls="collapseTwo6" href="#collapseTwo6">
	// 	Transaction Tracking <b class="f-24 float-right">+</b>
	// 	</div>
	// 	</div>
	// 	<div id="collapseTwo6" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionExample4">';
	// 	$dis_str.='
	// 	<div class="latest-update-card">                                            
 //            <div class="card-block">
 //                <div class="" style=""><div class="" style="">
 //                    <div class="latest-update-box">';
 //                    foreach ($vdata_details as  $vdvalue) {
	// 		$rid = deocdeId("id:".$vdvalue['trn_id']);
 //                    	$view = "index.php?option=".$vdvalue['option_url']."&Rid=".$rid;
 //        				$dis_str.='  <div class="row  p-b-30">
 //                            <div class="col-auto text-end update-meta p-r-0">';
 //                            	if($_SESSION['user_image'][$vdvalue['user_id']]==""){
 //                               $dis_str.=' <div class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['user_id']].'"> <span class="font-size-h3 font-weight-boldest ">'.initials($_SESSION['users'][$vdvalue['user_id']]).'</span>   </div>';
 //                           		}else{
 //                           			$dis_str.=' <img src="uploads/all/'.$_SESSION['user_image'][$vdvalue['user_id']].'" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['user_id']].'">';
 //                           		}
 //                            $dis_str.='  </div>
 //                            <div class="col p-l-5">
 //                                <a href="'.$view.'" target="_blank"><h6>'.$vdvalue['table'].' -  '.$vdvalue['trn_no'].'</h6></a>
 //                                <p class="text-muted m-b-0">&nbsp;'.date("M d, Y g:i A", strtotime($vdvalue['trn_date'])).'</p>
 //                            </div>
 //                        </div>';
 //             		}
 //            $dis_str.='</div></div></div></div></div></div></div></div>';
	// 	return $dis_str;
		
	// }
	function todisplaytracking($data){
		//$vurl = API."?mode=gettrackingdynamic"; 
		$vurl = API."?mode=gettrackingdynamic&table=".$data['table']."&transaction_id=".$data['transaction_id'];
		//echo $vurl;
		$vdata_details = callAPI($vurl);
		$vdata_details = json_decode($vdata_details,1);
		// echo "<pre>";
		// print_r($vdata_details);
		// echo "<pre>";
		$dis_str ='';
		$dis_str.='<div class="accordion accordion-solid mt-2 accordion-toggle-arrow col-lg-6" id="accordionExample4">                    
		<div class="card latest-update-card acc-bgc">
		<div class="card-header" id="headingTwo2">
		<div class="card-title scale_active collapsed f-16 f-w-600" data-bs-toggle="collapse" data-target="#collapseTwo6" aria-expanded="false" aria-controls="collapseTwo6" href="#collapseTwo6">
		Transaction Tracking <!--<b class="f-24 float-right">+</b>-->
		</div>
		</div>
		<div id="collapseTwo6" class="collapse p-2" aria-labelledby="headingTwo2" data-parent="#accordionExample4">';
		$dis_str.='<div class="row">';
	if(count((array)$vdata_details) !=0){	
		foreach($vdata_details as $trantable=>$vdata_detail){
			
$dis_str.='<div class="col-lg-6 ">
			<div class="card">
				<div class="card-header">
					<h5>'.ucwords(str_replace("_"," ",($trantable))).'</h5>
				</div>';
		$dis_str.='<div class="card-body"><table width="100%">';
		$dis_str.='<tr><th>Number</th><th>Date</th><th>Originator</th></tr>';
		 foreach($vdata_detail as $vdatd){
		 	$redirecturl = getPagenametotable($trantable,'insert_page_name');
			$rid = deocdeId("id:".$vdatd['id']);
 			$url = "index.php?option=".$redirecturl."&Rid=".$rid;
			$dis_str.='<tr>
            		<td><a href="'.$url.'" class="text-primary" target="_blank">'.$vdatd['transaction_number'].'</a></td>
            		<td>'.fromDate($vdatd['transaction_date']).'</td>
            		<td>'.$_SESSION['users'][$vdatd['originator']].'</td>
            	</tr>';
        	}	
       $dis_str.='</table></div>';

$dis_str.='</div></div>';
}
}else{
	$dis_str.='<div class="card-px text-center ">
		<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>   
		<div class="text-center px-4">
			<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		</div>   
	</div>';
}
$dis_str.='<div>';


            $dis_str.='</div></div></div></div></div></div></div></div>';
		return $dis_str;
		
	}
	function todisplaycandidateofferhistroy($data){
		$candhistry["where"] = array('is_active'=>0,'candidate_id'=>$data['candidate_id'],'company_id'=>$_SESSION['company_id']);
		$candhistry['order']  = "id DESC";
		$candhistry["field"] ="id,current_status,status_date,comments,created_by";
		$candhistryenod = urlencode(json_encode($candhistry));
		$candhistryurl = API."?mode=gettabledetailswithconditions&table=candidate_offer_histories&data=".$candhistryenod; 
		$candhistrydata = callAPI($candhistryurl);
		$vdata_details = json_decode($candhistrydata,1);
		$dis_str ='';
		$dis_str.='<div class="accordion accordion-solid mt-2 accordion-toggle-arrow col-lg-12" id="accordionExample2">                    
		<div class="card latest-update-card acc-bgc">
		<div class="card-header " id="headingTwo2">
		<div class="card-title collapsed f-16 f-w-600" data-bs-toggle="collapse" data-target="#collapseTwo88" aria-expanded="false" aria-controls="collapseTwo88" href="#collapseTwo88">
		Candidate Histores<b class="f-24 float-right">+</b>
		</div>
		</div>
		<div id="collapseTwo88" class="collapse" aria-labelledby="headingTwo88" data-parent="#accordionExample2">';
		$dis_str.='
		<div class="latest-update-card">                                            
		<div class="card-block">
		<div class="" style=""><div class="" style="">
		<div class="latest-update-box">';
		foreach ($vdata_details as  $vdvalue) {  
			$dis_str.='  <div class="row  p-b-30">
			<div class="col-auto text-end update-meta p-r-0">';
			if($vdvalue['current_status']==ST_APPROVED){
				$dis_str.=' <div class="img-radius img-50 align-top m-r-15 text-center userimageround bg-c-green text-white" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['created_by']].'"> <span class="fa fa-thumbs-up f-20 mt-2"></span>   </div>';
			}else{
				$dis_str.=' <div class="img-radius img-50 align-top m-r-15 text-center userimageround bg-c-red text-white" data-bs-toggle="tooltip" data-placement="bottom" title="'.$_SESSION['users'][$vdvalue['created_by']].'"> <span class="fa fa-thumbs-down f-20 mt-2"></span>   </div>';
				
			}
			$dis_str.='  </div>
			<div class="col p-l-5">
			<a href="#!"><h6>'.$_SESSION['users'][$vdvalue['created_by']].' </br><small> '.$vdvalue['comments'].'</small></h6></a>
			<p class="text-muted m-b-0">&nbsp;'.date("M d, Y", strtotime($vdvalue['status_date'])).'</p>
			</div>
			</div>';
		}
		$dis_str.='</div></div></div></div></div></div></div>';
		return $dis_str;
	}
	function lableandbgcolor($sb_bp){
		if($sb_bp >100){ $bbg_color = "danger";}
		elseif($sb_bp < 100){ $bbg_color = "warning"; }
		//elseif($sb_bp >60){ $bbg_color = "success"; }
		else{ $bbg_color = "success"; }		
		return $bbg_color;				
	}
	function lableandleave($sb_bp){

		if($sb_bp >2){ $bbg_color = "danger";}
		elseif($sb_bp >1){ $bbg_color = "warning"; }
		else{ $bbg_color = "success"; }		
		return $bbg_color;				
	}
	function filleuploading($file){
		$filedetails=array();
		$filedetails['originalname'] = basename($file['file']['name']);
		$filedetails['filesize'] =basename($file['file']['size']);      
		$name = time().basename($file['file']['name']);
		if(move_uploaded_file($file["file"]["tmp_name"], "uploads/attachments/".$name)) {
			$filedetails['filename'] = $name;
		}
		return $filedetails;
	}


	function filleUploadingList($transactiontablename,$id,$isactive=''){
		$pages = API."?mode=filleuploadinglist&id=".$id."&isactive=".$isactive."&transactiontablename=".$transactiontablename;
		$page_list = callAPI($pages);
		$res = json_decode($page_list,1);
		return $res;
	}
	// function fileUplodingUi($fileuploading){
	// 	$aplicationid =  $fileuploading['transaction_id'];
	// 	$trasnsactiontablename =  $fileuploading['application_table_name'];
	// 	$isactive =  $fileuploading['is_active'];
	// 	$dropwidthwidth =  (isset($fileuploading['width']) and $fileuploading['width'] !='')? $fileuploading['width']:'6';
	// 	$lable = $fileuploading['lable'];
	// 	$message="";
	// 	$images="";
	// 	$deleteimages="";
	// 	$values =  filleUploadingList($trasnsactiontablename,$aplicationid,$isactive);
	// 	$empty = (!empty($values))? " dz-started" : "cd";
	// 	foreach($values as $result){ 
	// 		$images .='<div class="delete'.$result['id'].' dz-preview dz-processing dz-image-preview   dz-success dz-complete ">  <div class="dz-image"><img data-dz-thumbnail="" alt="'.$result['file_name'].'" src="uploads/attachments/'.$result['actual_file_name'].'" style="height: 120px; width: 100%;"></div>  <div class="dz-details">    <div class="dz-size"><span data-dz-size=""><strong>0.6</strong> MB</span></div>    <div class="dz-filename"><span data-dz-name="">'.$result['file_name'].'</span></div>  </div>  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;"></span></div>  <div class="dz-error-message"><span data-dz-error-message=""></span></div>  <div class="dz-success-mark">    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">      <title>Check</title>      <defs></defs>      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>      </g>    </svg>  </div> 
	// 		<a class="dz-remove" onclick="removeattachfile('.$result['id'].',\''.$result['actual_file_name'].'\')" data-dz-remove="">Remove file</a></div>';
	// 	} 
	// 	$message .= '<div class="form-group col-lg-12 row"><div class="form-group col-lg-'.$dropwidthwidth.'">
	// 	<label>'.ucwords($lable).'</label>
	// 	<div>
	// 	<div class="dropzone dropzone-default dropzone-primary digits '.$empty.'" id="kt_dropzone_2">
	// 	<div class="dropzone-msg dz-message needsclick">
	// 	<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
	// 	<span class="dropzone-msg-desc">Upload up to 10 files</span>
	// 	</div>';
	// 	$message .= $images;
	// 	$message .='</div></div></div>';
	// 	if(isset($fileuploading['deleteview']) and $fileuploading['deleteview'] !='' and $fileuploading['deleteview'] =='1'){
	// 		$widthdelete =  (isset($fileuploading['deletewidth']) and $fileuploading['deletewidth'] !='')? $fileuploading['deletewidth']:'6';
	// 		$deletelist =  filleUploadingList($trasnsactiontablename,$aplicationid,'1');
	// 		if(is_array($deletelist)){ foreach($deletelist as $list){
	// 			$deleteimages .= '<div class="col-md-2 dz-preview dz-processing dz-image-preview dz-success dz-complete divss del'.$list['id'].'" onclick="reused(\''.$list['actual_file_name'].'\','.$list['id'].')"><a class="dleteimages">reused</a><div class="dz-image"><img src="uploads/attachments/'.$list['actual_file_name'].'" style="  width: 100%;" ></div></div>';

	// 		}} 
	// 		$message .='<div class=" form-group col-lg-'.$widthdelete.'">
	// 		<label class="">Delete List</label>
	// 		<div class="">
	// 		<div class="dropzone dropzone-default dropzone-primary deletesrow" >';
	// 		$message .= $deleteimages;
	// 		$message .=	'</div></div></div>';	
	// 	}
	// 	$message .=	'<input type="hidden" id="application_table_name" value="'.$fileuploading[application_table_name].'">
	// 	<input type="hidden" id="transaction_id" value="'.$fileuploading[transaction_id].'">  </div>';
	// 	echo $message;	
	// }

function fileUplodingUi($fileuploading){
		$aplicationid =  $fileuploading['transaction_id'];
		$transaction_number =  $fileuploading['transaction_number'];
		$trasnsactiontablename =  $fileuploading['application_table_name'];
		$isactive =  $fileuploading['is_active'];
		$dropwidthwidth =  (isset($fileuploading['width']) and $fileuploading['width'] !='')? $fileuploading['width']:'6';
		$lable = $fileuploading['lable']!="" ? $fileuploading['lable'] : "File Upload";
		$message="";
		$images="";
		$deleteimages="";
		$values =  filleUploadingList($trasnsactiontablename,$aplicationid,$isactive);
		$empty = (!empty($values))? " dz-started" : "cd";
		 $acceptable = array('jpeg','jpg','JPG','gif','png');
		$validationfunction ="";
		$validation_style= "";
		if($fileuploading['file_limit']=="" OR $fileuploading['file_size'] ==""){
 	 		$validationfunction = "onclick='validationfunction()'";
 	 		$validation_style  ="style='pointer-events: none;'";
		}
		foreach($values as $result){ 

 
    $string = $result['actual_file_name'];
	$exploded = explode('.', $string);
	$exploded = end($exploded);
 	
 	if(!in_array($exploded, (array)$acceptable)){
    	$imge_path = "uploads/attachments/document_icon.png";
	}else{
		$imge_path = "uploads/attachments/".$result['actual_file_name'];
	}
	$uploadedimagepath = "uploads/attachments/".$result['actual_file_name'];

			$images .='<div class="delete'.$result['id'].' dz-preview dz-processing dz-image-preview   dz-success dz-complete ">  <div class="dz-image"><img data-dz-thumbnail="" alt="'.$result['file_name'].'" src="'.$imge_path.'" style="height: 120px; width: 100%;"></div>  <div class="dz-details">    <div class="dz-size"></div>    <div class="dz-filename"><a href="'.$uploadedimagepath.'" target="_blank"><span data-dz-name="">'.$result['file_name'].'</span></a></div>  </div>  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;"></span></div>  <div class="dz-error-message"><span data-dz-error-message=""></span></div>  <div class="dz-success-mark">    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">      <title>Check</title>      <defs></defs>      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>      </g>    </svg>  </div> 
			<a class="dz-remove" onclick="removeattachfile('.$result['id'].',\''.$result['actual_file_name'].'\')" data-dz-remove="">Remove file</a></div>';
		} 
		$message .= '<div class="form-group col-lg-12 row" '.$validationfunction.'><div class="form-group col-lg-'.$dropwidthwidth.'" '.$validation_style.'>
		<label>'.ucwords($lable).'</label>
		<div>
		<div class="dropzone dropzone-default dropzone-primary digits '.$empty.'" id="kt_dropzone_2">
		<div class="dropzone-msg dz-message needsclick mb-2 mt-1" style="display:block">
		<i class="fa fa-upload f-30 mb-2"></i>
		<h3 class="dropzone-msg-title  f-16">Drag & Drop files here</h3>
		<span class="dropzone-msg-desc  f-14">Upload up to '.$fileuploading['file_limit'].' files</span>
		</div>';
		$message .= $images;
		$message .='</div></div></div>';
		if(isset($fileuploading['deleteview']) and $fileuploading['deleteview'] !='' and $fileuploading['deleteview'] =='1'){
			$widthdelete =  (isset($fileuploading['deletewidth']) and $fileuploading['deletewidth'] !='')? $fileuploading['deletewidth']:'6';
			$deletelist =  filleUploadingList($trasnsactiontablename,$aplicationid,'1');
			if(is_array($deletelist)){ foreach($deletelist as $list){

	$string = $list['actual_file_name'];
	$exploded = explode('.', $string);
	$exploded = end($exploded);
 	
 	if(!in_array($exploded, (array)$acceptable)){
    	$imge_path = "uploads/document_icon.png";
	}else{
		$imge_path = "uploads/attachments/".$list['actual_file_name'];
	}


$deleteimages .= '<div class="col-md-2 dz-preview dz-processing dz-image-preview dz-success text-center dz-complete divss del'.$list['id'].'" style=" width:fit-content;"><a onclick="reused(\''.$list['actual_file_name'].'\','.$list['id'].')" class="dleteimages f-14 cursor-pointer">Reused</a><div class="dz-image">
<a href="uploads/attachments/'.$list['actual_file_name'].'" target="_blank">
<img src="'.$imge_path.'" style=" width: 100%;" >
</a>
</div></div>';
			}} 
			$message .='<div class=" form-group col-lg-'.$widthdelete.'" '.$validation_style.'>
			<label class="">Delete List</label>
			<div class="">
			<div class="dropzone dropzone-default dropzone-primary deletesrow" >';
			$message .= $deleteimages;
			$message .=	'</div></div></div>';	
		}
		$message .=	'<input type="hidden" id="application_table_name" value="'.$fileuploading['application_table_name'].'">
		<input type="hidden" id="transaction_id" value="'.$fileuploading['transaction_id'].'">
<input type="hidden" id="file_size" value="'.$fileuploading['file_size'].'"> <input type="hidden" id="file_limit" value="'.$fileuploading['file_limit'].'"> 
<input type="hidden" id="max_file_upload_message" value="'.$_SESSION['error_message']['3023'].' '.$fileuploading['file_limit'].' file(s)">
<input type="hidden" id="validation_message" value="'.$_SESSION['error_message']['3023'].'">		 
 <input type="hidden" id="transaction_number" value="'.$transaction_number.'"></div>';
		echo $message;	
	}

	$filed_array = array('text' => 'Simple Text', 'touchspin' => 'Touch Spin', 'date_pick' => 'Date','month_year'=>'Month','time_pick'=>'Time' ,'color_pick' => 'Color', 'email'=> 'Email','phone'=> 'Phone','number'=> 'Quantity','currency'=> 'Currency', 'password'=> 'Password', 'textarea' => 'Text Area', 'checkbox' => 'Check Box', 'switch' => 'Switch', 'radio' => 'Radio', 'select'=> 'List','auto_select'=>'Auto Select' ,'multi_select'=> 'Multi Select','rating'=>'Rating','image' => 'Photo','editor' => 'Editor','slider'=>'Slider','number_type'=> 'Number','read_only'=> 'Read Only Text','disable_select'=> 'Disable List','hidden'=> 'Hidden', 'serial_no'=>'Serial Number');

	$widtharray = array('1' => 'col-lg-1','2' => 'col-lg-2','3' => 'col-lg-3','4' => 'col-lg-4','5' => 'col-lg-5','6' => 'col-lg-6','7' => 'col-lg-7','8' => 'col-lg-8','9' => 'col-lg-9','10' => 'col-lg-10','11' => 'col-lg-11','12' => 'col-lg-12');

	function optiontype($id,$filed_array){
		$filedTypes ="";
		foreach($filed_array as $key => $value){
			$select = ($id == $key)?"selected":"";
			$filedTypes .='<option value="'.$key.'" '.$select.'>'.$value.'</option>';
		}
		return $filedTypes; 
	}
	
	function buttonsList($id,$type="",$value="",$class="",$size=""){
		$buttons = array("saveandcontinue"=>'<button type="button" class="btn btn-primary textcapital '.$size.' mr-2" id="buttonid'.$id.'" value="'.$value.'"><i class="la la-check-circle"></i>Save &amp; continue</button>',
			"save"=>'<button type="button" class="btn btn-primary textcapital '.$size.' mr-2" id="buttonid'.$id.'" value="'.$value.'"><i class="la la-check"></i>Save</button>',
			"update"=>'<button type="button" class="btn btn-primary textcapital '.$size.' mr-2" id="buttonid'.$id.'" value="'.$value.'"><i class="flaticon2-pen mr-5"></i>Update</button>',
			"print"=>'<button type="button" class="btn btn-primary textcapital '.$size.' mr-2" id="buttonid'.$id.'" value="'.$value.'"><i class="icon-2x flaticon-technology"></i>Print</button>');
		echo $buttons[$type];
	}
	//print designing start
	function getListTables($database=""){
		$url = API."?mode=getlisttable&db=".$database; 
		$collfunction = callAPI($url);
		$jsondecode = json_decode($collfunction,1);
		return $jsondecode;
	}
	function getListAttributes($tablename=""){
		$url = API."?mode=gettableattributes&table=".$tablename; 
		$collfunction = callAPI($url);
		$jsondecode = json_decode($collfunction,1);
		return $jsondecode;
	}
 	//print designing End
	function textboxes($s,$type="",$placeholder="",$values=""){
		$textfield = array(
			"serial_no"=>'<input type="text"  value="'.$values.'" class="form-control form-control-lg  inputbox'.$s.'"  placeholder="'.$placeholder.'" />',
			"number"=>'<input type="text"  value="'.$values.'" class="form-control form-control-lg  inputbox'.$s.'"  placeholder="'.$placeholder.'" />',
			"disable_select"=>'<select class="form-control inputbox'.$s.' form-select js-example-basic-single" placeholder="'.$placeholder.' disabled="disabled"><option>1</option></select>',
			"read_only"=>'<input type="text" value="'.$values.'" class="form-control inputbox'.$s.'" placeholder="'.$placeholder.'" readonly="readonly" >',
			"number_type"=>'<input class="form-control inputbox'.$s.'" value="'.$values.'" placeholder="'.$placeholder.'" id="example-number-input">',
			"currency"=>'<input type="text" class="form-control form-control-lg autonumber inputbox'.$s.'" data-a-sign="Rs. "  name=""  value="">',
			"rating"=>' <div class="stars stars-example-fontawesome-o"> <select id="example-fontawesome-o" name="rating" data-current-rating="5.6" autocomplete="off"><option value="" label="0"></option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option> <option value="9">9</option> <option value="10">10</option> </select> </div>',
			"multi_select"=>'<select class="form-control form-select js-example-basic-multiple" id="kt_select2_3" name="param" multiple="" >
			<optgroup label="Alaskan/Hawaiian Time Zone">
			<option value="AK" selected="selected" data-select2-id="10">Alaska</option>
			<option value="HI">Hawaii</option>
			</optgroup>
			</select>',
			"select"=>'<select class="form-control inputbox'.$s.' form-select js-example-basic-single" ><option>Select Option</option></select>',
			"switch"=>'<span class="switch switch-icon inputbox'.$s.'"><label><input type="checkbox" class=
			"js-switch-s'.$s.'" checked="checked" name="select"><span></span></label></span>',
			"radio"=>'<div class="radio-inline inputbox'.$s.'"><label class="radio m-l-5"><input type="radio" checked="checked" name="radios3" class="m-r-5"><span></span>Option 1</label><label class="radio m-l-5"><input type="radio" name="radios3" class="m-r-5"><span></span>Option 2</label><label class="radio"></div>',
			"checkbox"=>'<div class="checkbox-inline inputbox'.$s.'"><label class="checkbox"><input type="checkbox" checked="checked" name="Checkboxes3"><span></span>Option 1</label><label class="checkbox"><input type="checkbox" name="Checkboxes3"><span></span>Option 2</label><label class="checkbox"></div>',

			"image"=>'<div class="custom-file"><input type="file" class="custom-file-input inputbox'.$s.'" id="customFile"><label class="custom-file-label" for="customFile">Choose file</label></div>',
			"time_pick"=>'<input class="form-control" type="time" value="13:45:00" id="example-time-input inputbox'.$s.'">',
			"month_year"=>'<input class="form-control" type="month" value="2011-08" id="example-month-input inputbox'.$s.'">',
			"date_pick"=>'<input class="form-control" type="date" value="2011-08-19" id="example-date-input inputbox'.$s.'">',
			"password"=>'<input type="password" class="form-control form-control-lg  inputbox'.$s.'" name="" value="" placeholder="'.$placeholder.'">',
			"phone"=>'<input type="text" class="form-control form-control-lg  inputbox'.$s.'" name="phone" value="'.$values.'" placeholder="'.$placeholder.'">',
			"email"=>'<input type="text" class="form-control form-control-lg  inputbox'.$s.'" name="phone" value="'.$values.'" placeholder="">',
			"textarea"=>'<textarea class="form-control inputbox'.$s.'" placeholder="'.$placeholder.'" rows="3">'.$values.'</textarea>',
			"text"=>'<input type="text"  class="form-control form-control-lg  inputbox'.$s.'" value="'.$values.'" placeholder="'.$placeholder.'" />');
		echo $textfield[$type];
	}
	//start auto ui designs
	function getTableRow($table,$fields="",$where="",$list="0"){
		$url = API."?mode=gettablerow&tablename=".$table."&fields=".$fields."&where=".$where."&list=".$list; 
		$collfunction = callAPI($url);
		$jsondecode = json_decode($collfunction,1);
		return $jsondecode;
	}
	//End  auto ui designs
	function getListCreatDocumentsFields(){
		$url = API."?mode=getlistcreatdocumentsfields"; 
		$collfunction = callAPI($url);
		$jsondecode = json_decode($collfunction,1);
		$inputFildsArray =array();
		foreach($jsondecode as $result){
			$inputFildsArray["{[".$result['application_table_name'].".".$result['fields']."]}"] = array(ucwords($result['label']),ucwords($result['preview_value']));
		}
		return $inputFildsArray;
	}
	 //print designing
	function filterArrayValue($array,$columnsname,$filter){
		$matches = array();
		foreach($array as $a){
			if($a[$columnsname] == $filter)
				$matches[]=$a;
		}
		return $matches;
	}

	function getProjectTaskEmpList($projectid){
		$url = API."?mode=getprojecttaskemplist&projectid=".$projectid; 
		$collfunction = callAPI($url);
		$jsondecode = json_decode($collfunction,1);
		return $jsondecode;
	}
	function permisions($role_data,$option){
		$option = strtolower($option);
		$array = (explode("_",$option,3)); 
		$return = $role_data[$array[2]];
		if($array[1]=="insert"){
			if($return['permission_insert']==1 && $return['permission_update']==1){
				header("Location: index.php?option=com_404");
			}
		}
		if($array[1]=="list"){
			if($return['permission_view']==1){
				header("Location: index.php?option=com_404");
			}
		}
		return $return;
	}
	function getList($tablename="",$id=""){
		$url = API."?mode=getlist&tablename=".$tablename."&id=".$id; 
		$collfunction = callAPI($url);
    //$jsondecode = json_decode($collfunction,1);
		return $collfunction;
	}
	function userlistwithrole($data){
		global $callConfig;	
		$sql = "SELECT * FROM `application_users` WHERE role_id like '%".$data['roleid']."%'";
		$row=$callConfig->getAllRows($sql);
		return $row;
	}
function convert_Rupee_Nomber($amount){
    /*$length = strlen($amount);
    if($length>=6 && $length <=7){
        echo round($amount/100000,2).' Lac';
    }else if($length>=8 && $length <=9){
        echo round($amount/10000000,2).' Cr.';
    }else if($length>=4 && $length <=5){
        echo round($amount/1000,2).' K';
    }else{ echo $amount; }*/
    echo round($amount/100000,2).' L';
}
function convert_Rupee_Number($amount){


    //99,99,99,99,999.99
	if("99,99,99,99,999.99" == "99,99,99,99,999.99"){
	    if(($amount/10000000) >= 1){
	    	
	        echo round($amount/10000000,2).' Cr.';
	    }elseif(($amount/100000) >= 1){
	        echo round($amount/100000,2).' Lc';  //millian 
	    }elseif(($amount/1000) >= 1){
	        echo round($amount/1000,2).' K';
	    }else{ echo round($amount); }
	}else{
		if(($amount/1000000000) >= 1){
	        echo round($amount/1000000000,2).' B.';
	    }elseif(($amount/1000000) >= 1){
	        echo round($amount/1000000,2).' M';  //millian 
	    }elseif(($amount/1000) >= 1){
	        echo round($amount/1000,2).' K';
	    }else{ echo round($amount); }
	}  
}
function dyanamicselectAutoSelect($table_name, $filed_name,$word){
	$dsurl = API."?mode=dyanamicautoselectdata&table_name=".$table_name."&filed_namee=".$filed_name."&words=".$word;
	$resresponse = callAPI($dsurl);
	$res = json_decode($resresponse,1);
	return $res;
}
function customNanAndEmptyValidation($val){
if(is_nan($val) OR $val==""){ return "0"; }else{ return $val; }
}
function reportFilterSelectData($report_id, $filed_name){
	$dsurl = API."?mode=reportfilterselectdata&report_id=".$report_id."&filed_name=". $filed_name;
	$resresponse = callAPI($dsurl);
	$res = json_decode($resresponse,1);
	if($res['type'] != 'function'){
		$return = $res['res'];
	}
	return $return;
}
//workflow
function checkWhoShouldApprove($val){   // find who should approve
	$ssql = API."?mode=checkwhoshouldapproves&tablename=".$val['tablename']."&rid=".$val['rid']."&exicute_type=".$val['exicute_type']."&emp_id=".$_SESSION['emp_id']; 
	$rowreturn = callAPI($ssql);
	//$rowreturns = json_decode($rowreturn,1);	
	return $rowreturn;
}
function checkcriteria($val){
	$checkcriteriaurl = API."?mode=checkcriteria&tablename=".$val['tablename']."&rid=".$val['rid']."&exicute_type=".$val['exicute_type']."&emp_id=".$_SESSION['emp_id'];  
	$return = callAPI($checkcriteriaurl);
	$return = json_decode($return,1);
	if(!empty($return)){
		return checkWhoShouldApprove($val);
	}else{
		return "No WorkFlow";
	}
	//return $return;
}
function getstatusprogresslist($table){
	$url = API."?mode=getstatusprogresslist&table=".$table; 
    $responce = callAPI($url);
	$result = json_decode($responce,1);
	return $result;
}
function customgetstatusworkflow($table,$id,$currnet_status,$created_by){
	$stausresult = ViewStatusData();
	$url = API."?mode=getuserstatusstep&table=".$table."&transaction_id=".$id."&type=".$type."&currnet_status=".$currnet_status; 
    $responce = callAPI($url);
	$result = json_decode($responce,1);
	$statusList = getstatusprogresslist($table);
	foreach($statusList as $statusLists){
		$statuse[$statusLists['status']] = $statusLists['action_label'];
	}
		if(isset($result['approve'])){ 
		$approve_display_status = $result['approve'];
		//$acceptstatus = $approve_display_status['status']=="-1"?"Approve11":$stausresult[$approve_display_status['status']]['name'];
		$acceptstatus = $approve_display_status['status']=="-1"?"Approval":$statuse[$approve_display_status['status']];
		?>
		<button class="btn" style="background-color: <?php echo $stausresult[$approve_display_status['status']]['background_color'] ?>; color: <?php echo $stausresult[$approve_display_status['status']]['color'] ?>; " onclick="statuspopupnew(<?php echo $id; ?>,'<?php echo $table; ?>',<?php echo $approve_display_status['status']; ?>,<?php echo $approve_display_status['transaction_id']; ?>,<?php echo $approve_display_status['workflow_id']; ?>,'approve')">
  	<?php echo $acceptstatus; ?>
  	</button>
 <?php } 
 if(isset($result['reject'])){ 
		$reject_display_status = $result['reject'];
		$rejectstatus = $reject_display_status['status']=="-1"?"Reject":$stausresult[$reject_display_status['status']]['name'];
		?>
		 <button class="btn" style="background-color: <?php echo $stausresult[$reject_display_status['status']]['background_color'] ?>; color: <?php echo $stausresult[$reject_display_status['status']]['color'] ?>; " onclick="statuspopupnew(<?php echo $id; ?>,'<?php echo $table; ?>',<?php echo $reject_display_status['status']; ?>,<?php echo $reject_display_status['transaction_id']; ?>,<?php echo $reject_display_status['workflow_id']; ?>,'reject')">
  <?php echo $rejectstatus; ?>
  </button>
 <button class="btn" style="background-color: <?php echo $stausresult[ST_SEND_BACK]['background_color'] ?>; color: <?php echo $stausresult[ST_SEND_BACK]['color'] ?>; " onclick="statuspopupnew(<?php echo $id; ?>,'<?php echo $table; ?>',<?php echo ST_SEND_BACK; ?>,<?php echo $reject_display_status['transaction_id']; ?>,<?php echo $reject_display_status['workflow_id']; ?>,'')"><?php echo $stausresult[ST_SEND_BACK]['name']; ?></button>
 <?php } 
	if(($currnet_status==$statusList[0]['status'] OR $currnet_status==ST_SEND_BACK) AND $created_by == $_SESSION['userid']){ 
 ?>
	 <button class="btn btn-primary"  onclick="startWorkflow(<?php echo $id; ?>,'<?php echo $table; ?>')"><?php echo $statuse[$result['staus']['status']];?> </button> 
	<?php	}
	//return $result;
$statusVal = $stausresult[$currnet_status];
if($id !=""){
?>
<label class="form-label label label-md label-primary" style="background-color: <?php echo $statusVal['background_color'] ?>; color: <?php echo $statusVal['color'] ?>; "><?php echo $statusVal['name']; ?></label>
<?php
}
}

//end work flow
function todisplayviewfiled($value,$type){
	if(is_array($_SESSION['field_secruity'][$type]) && $value!=""){
		if($_SESSION['field_secruity'][$type]['field_type'] == 'email'){
			//echo "ste3 ".$value;
			$cemail = explode('@',$value);
			$value = $cemail['0'];
			//echo "ste113 ".$value."--".$cemail['1'];
		}
		$prefix = $_SESSION['field_secruity'][$type]['display_field_prefix_no']; 
		$postfix = $_SESSION['field_secruity'][$type]['display_field_suffix_no'];
		$replacement = $_SESSION['field_secruity'][$type]['replace_character'];
		$fixed=$prefix+$postfix;
		$value_count = strlen($value);		
		if($value_count>$fixed){
			$str_array =str_split($value);

			 foreach($str_array as $key => $char) {
			 		//echo "--".$key."--".$char;
				 if($key <$prefix|| $key > $value_count-1-$postfix) { $str[$key] =$char;}
				 else{  $str[$key] = $replacement; }
			 }
			 $str = implode("",(array)$str);
			 if($_SESSION['field_secruity'][$type]['field_type']=="email"){
				$str.="@".$cemail['1'];
			}		
		}else{
			for($i=0;$i<$value_count;$i++){
				$str[]=$replacement;
			}
			$str = implode("",(array)$str);
			//$str = substr_replace($value,$replacement,0,$value_count);
			if($_SESSION['field_secruity'][$type]['field_type']=="email"){
				$str.=$cemail['1'];
			}
		}
	}else{
		$str =$value;
	}	
	return $str;
}
function toGETFieldSecruityList(){
	$userurl = API."?mode=gettabledetails&table=field_secruity_configurations" ;
	$usres = callAPI($userurl);
	$fieldresult = json_decode($usres,1);
	foreach ($fieldresult as  $value){
		$result[$value['configuiration_code']]= $value;
	}
	return $result;
}
function gettableStatusArray($table){
$wh1f["where"] = array('table_name'=>$table,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$wh1f["field"] = "id,name,status";
$data12 = urlencode(json_encode($wh1f));
$url12 = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$data12; 
$statusapi = callAPI($url12);
$statusrows = json_decode($statusapi,1);
foreach($statusrows as $statusrow){
  $getstatusarray[$statusrow['status']] = $statusrow['name'];
  $getstatusarray['full'][$statusrow['status']] = $statusrow;
}
return $getstatusarray;
}
function checkTableName($name){
$table ="no";
foreach ($_SESSION['application_pages'] as $value) {
if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name){
$table = $value['application_table_name'];
if($value['list_page_name']==$name){ $type = 'permission_list'; }
if($value['insert_page_name']==$name){ $type = 'permission_insert'; }
if($value['edit_page_name']==$name){ $type = 'permission_update'; }
if($value['view_page_name']==$name){ $type = 'permission_list'; }
}
}
return $table; //exit;
}
function dyanamicreferFunc($val, $filed_name,$table){	
	$dsurl = API."?mode=dyanamicreferfunc&table_name=".$table."&filed_namee=". $filed_name."&val=".$val;	
	$resresponse = callAPI($dsurl);	
	$res = json_decode($resresponse,1);	
	if($res['type'] != 'function'){	
		$return = $res['res'];	
	}	
	return $return;	
}
function customeummarmonyformateround($number){
	$num = $_SESSION['configdata']['number_format'] !='' ? $_SESSION['configdata']['number_format'] :2;
	$return = number_format($number,0, '.', ',');
	return $return!='0.00' ? $return : '-'; 
}
function custombuttonserp($erp,$emp_id){
	$str= "";
	$str.= '<a  target="_blank" class="m-r-5" href="index.php?option=COM_QO_PRINT&rid='.$erp[$emp_id]['esgin_budget_approval_id'].'&table=esgin_budget_approvals&title=Esgin Budget Approvals"><button class="btn waves-effect waves-light btn-info btn-icon" data-bs-toggle="tooltip" data-placement="bottom" title="Esign Transtion No:'.$erp[$emp_id]['etn'].'" style="width:20px;height:20px;line-height:0px;font-size:10px;" >
		E</button></a>';
	$str.= '<a target="_blank"  class="m-r-5" href="index.php?option=COM_QO_PRINT&rid='.$erp[$emp_id]['vrc_id'].'&table=vendor_rate_cards&title=Vendor Rate Cards"><button class="btn waves-effect waves-light btn-warning btn-icon"  data-bs-toggle="tooltip" data-placement="bottom" title="Rate Card Transtion No: '.$erp[$emp_id]['vtn'].'" style="width:20px;height:20px;line-height:0px;font-size:10px;">R</button></a>';
	$str.= '<a target="_blank"class="m-r-5" href="index.php?option=COM_QO_PRINT&rid='.$erp[$emp_id]['work_order_id'].'&table=manpower_contracts&title=Purchase Order"><button class="btn waves-effect waves-light btn-danger btn-icon"  data-bs-toggle="tooltip" data-placement="bottom" title="PO Transtion No:'.$erp[$emp_id]['mtn'].'" style="width:20px;height:20px;line-height:0px;font-size:10px;">P</button></a>';
	return $str;
}
function headerlables($data){
	$str= "";
	$str.= '<div class="d-flex flex-column w-100 mr-2">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="text-muted mr-2 font-size-sm font-weight-bold">Billable</span>
		<span class="text-muted font-size-sm font-weight-bold">Budget</span>
	</div>
	<div class="progress progress-xs w-100">
		<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<div class="text-center">'.$data["name"].'</div>
	</div>';
	return $str;
}
function checkApproveManager($id,$status){
	$returnres = ST_APPROVAL_PENDING;
	if($status == ST_ACTIVE){
		$wh1f["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'id'=>$id);
		$wh1f["field"] = "approval_manager,reports_to";
		$data12 = urlencode(json_encode($wh1f));
		$url12 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data12; 
		$statusapi = callAPI($url12);
		$statusrows = json_decode($statusapi,1);
		if($statusrows[0]['approval_manager'] != $statusrows[0]['reports_to']){
			$returnres = ST_APPROVAL_MANAGER;
		}
	}else{
		$returnres = ST_APPROVAL_PENDING;
	}
	return  $returnres;
}
function checkTimesheetsSendStatus(){
	$wh1f["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'id'=>$_SESSION['userid']);
	$wh1f["field"] = "reports_to,approval_manager";
	$data12 = urlencode(json_encode($wh1f));
	$url12 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data12; 
	$statusapi = callAPI($url12);
	$statusrows = json_decode($statusapi,1);
	if($statusrows[0]['approval_manager'] == $statusrows[0]['reports_to']){
		$returnres = '8';
	}else{
		$returnres = '10';
	}
	return  $returnres;
}
function getTimeSheetNotSubmitedDeatils(){
	$starttimeshhets = $_SESSION['config']['default_timesheet_date']==""?'01':$_SESSION['config']['default_timesheet_date'];
	$startmonthdate = date("Y-m-".$starttimeshhets);
	$endmonthdate = date("Y-m-".$starttimeshhets,strtotime("+1 month"));
	$endmonthdate = date("Y-m-d", strtotime("-1 days", strtotime($endmonthdate)));
	$prestartWeek = date('Y-m-d',strtotime("last week Monday"));
	$preendWeek = date('Y-m-d',strtotime("last week Sunday"));
	if(strtotime($startmonthdate) >= strtotime($prestartWeek)){
	    $prestartWeek   =$startmonthdate;
	}
	if(strtotime($endmonthdate) < strtotime($preendWeek)){
	    $preendWeek =$endmonthdate;
	}
	if(in_array('3',(array)$_SESSION['role_ids'])){ 
	   $timesheetpendinemployee = API."?mode=gettimesheetsubmitpending&hrtype=1&startingdate=".$prestartWeek."&endingdate=".$preendWeek; 
	}else{
	    $timesheetpendinemployee = API."?mode=gettimesheetsubmitpending&emp_id=".$_SESSION['emp_id']."&startingdate=".$prestartWeek."&endingdate=".$preendWeek; 
	}   
	$restimesheetpendinemployee = callAPI($timesheetpendinemployee);
	$restimesheetpendinemployee = json_decode($restimesheetpendinemployee,1);
	if($restimesheetpendinemployee['type'] == "timesheet"){
	    $restimesheetpendinemployee['mes'] = $_SESSION['error_message'][$restimesheetpendinemployee['timesheetsmessagecode']]." (".$restimesheetpendinemployee['date'].")"; 
	}else{
	    $restimesheetpendinemployee['mes'] = $_SESSION['error_message'][$restimesheetpendinemployee['timesheetsmessagecode']]." (".$restimesheetpendinemployee['cnt_employee'].")"; 
	}
	return $restimesheetpendinemployee; 
}
function get_weeks($year, $month){
    $days_in_month = date("t", mktime(0, 0, 0, $month, 1, $year));
    $weeks_in_month = 1;
    $weeks = array();
	for ($day=1; $day<=$days_in_month; $day++){
		
        $week_day = date("w", mktime(0, 0, 0, $month, $day, $year));//0..6 starting sunday
		$weeks[$weeks_in_month][] = $year."-".$month."-".$day;
		if ($week_day == 0) {
            $weeks_in_month++;
        }
    }
    return $weeks;
}
function dynamictabledetails($table,$display=""){
	$wh1f["where"] = array('company_id'=>$_SESSION['company_id']);
	$data12 = urlencode(json_encode($wh1f));
	$url12 = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$data12; 
	$tabledata = callAPI($url12);
	$tabledata = json_decode($tabledata,1);
	$dis = $display !="" ? $display : 'id';
	foreach($tabledata as $table){
		$return [$table[$dis]] = $table;
	}
	return $return;
}
function pagePermisionCheck($option,$permisiontype){
	$check = '1';
	if($_SESSION['role_page_permission'][$option][$permisiontype]=='0'){
		$check = '0';
	}
	return $check;
}
function getUserRoles(){
	$role_id = array();  
	if($_SESSION['admin_type'] == 1){
		$application_user_roleswher["where"] = array('is_active'=> '0','module_group_id'=>'0','user_id'=>$_SESSION['userid']);
	}else{
		$application_user_roleswher["where"] = array('is_active'=> '0','module_group_id'=>$_SESSION['work_module_setup_id'],'user_id'=>$_SESSION['userid']);
	}
	$application_user_roleswher["field"] = "role_id";
	$vdatadeco = urlencode(json_encode($application_user_roleswher)); 
	$augurl = API."?mode=gettabledetailswithconditions&table=application_user_roles&data=".$vdatadeco; 
	$application_user_roles = callAPI($augurl);
	$application_user_roles   = json_decode($application_user_roles,1);
	foreach($application_user_roles as $application_user_roles){
		$role_id[] = $application_user_roles['role_id'];
	}
	return $role_id;
}
function nearestDateFirstShow($arry,$field){
  $nearstartholiday = array();
  $nearendholiday  =array();
  $holdyadate  =array();
  foreach ($arry as $key => $value){
	  //$dateggd[] = date('Y').'-'.date('m-d',strtotime($value[$field])); 
    if(strtotime(date('Y').'-'.date('m-d',strtotime($value[$field]))) >= strtotime(date("Y-m-d"))){
		//$value[$field.'_1']  = date('Y').'-'.date('m-d',strtotime($value[$field]));
        $nearstartholiday[] = $value;
    }else{
		//$value[$field.'_1']  = date('Y').'-'.date('m-d',strtotime($value[$field]));
        $nearendholiday[] = $value;	
    }
  } 
    $resultary = array_merge($nearstartholiday, $nearendholiday);
	//$resultary['new'] =  $nearstartholiday; 
	//$resultary['old'] =  $nearendholiday; 
    return $resultary;
}


function todisplayWorkflowstatuslist($data){
  		$wlist =array();
		if($data['table']!="" && $data['current_status']!=""){
			$wlist_url =  API."?mode=getwrokflowsteps&table=".$data['table']."&rid=".$data['rid'];
	   		$wlist = callAPI($wlist_url);
			$wlistres = json_decode($wlist,1);
			$wlist = $wlistres['steps'];
			$logres = $wlistres['log'];
			
		}$dis_str='';
		if(count((array)$wlist)>0){
			$dis_str.='<div class="p-3"><div class="breadcrumb">';
			$checkcurrent_status = $data['current_status'];
			$wlcount = count((array)$wlist);$cw=1;$ork=0;
			$activearray[] = array('action_label'=>'Active','status'=>ST_ACTIVE);
			$newarray = array_merge($activearray,$wlist);
			$newarray[30] = array('action_label'=>'Reject','status'=>ST_REJECTED);
			if($checkcurrent_status == ST_INACTIVE || $checkcurrent_status== ST_DRAFT || $checkcurrent_status== ST_SEND_BACK ){
				$checkcurrent_status =ST_ACTIVE;
			}
			
			//$newarray = array_push($newarrayr,$reject);
			///echo "<pre>"; print_r($newarray);
			foreach ($newarray as $pwlist) {
				if($pwlist['action_label']!=""){
					$orcolor =""; $oricon="";
				/**	if($ork==1){
						$orcolor = "text-warning";
						$oricon = '<i class="far fa-hourglass text-warning"></i> &nbsp;';
						$ork = 0;
					}
					if($checkcurrent_status == $pwlist['status']){    
						$orcolor = "text-primary";
						$oricon = '<i class="far fa-check-circle text-success"></i> &nbsp;';
						$ork = 1;
					}
					**/
					$username = "";
					if($pwlist['status'] != '0' AND $pwlist['status'] != '10'){ //not created by show only
						if($logres[$pwlist['status']] != ""){
							$username .= "(".$_SESSION['users'][$logres[$pwlist['status']]].")";
						}
					}else{ //created by show only
						if($logres['c'] != ""){
							$username = "(".$_SESSION['users'][$logres['c']].")"; 
						} 
					}
					
					
					if($checkcurrent_status >=$pwlist['status']){    
						if($pwlist['status'] != ST_REJECTED){
							$orcolor = "text-success";
							$oricon = '<i class="far fa-check-circle text-success"></i> &nbsp;';
						}
					}else{
						if($ork==0 AND $checkcurrent_status != ST_REJECTED){
							$orcolor = "text-primary";
							$oricon = '<i class="far fa-hourglass text-warning"></i> &nbsp;';
							$ork =1;
							if($pwlist['status'] != 0 AND $pwlist['status'] != '10'){
								$username = "(".$_SESSION['users'][$logres[0]].")";
							}
						}elseif($checkcurrent_status == $pwlist['status']){
							$orcolor = "text-primary";
							$oricon = '<i class="far fa-hourglass text-warning"></i> &nbsp;';
							if($pwlist['status'] != 0 AND $pwlist['status'] != '10'){
								$username = "(".$_SESSION['users'][$logres[0]].")";
							}
						}
						
					} 
					
					$dis_str.='<a href="#"><span class="breadcrumb__inner"><span class="breadcrumb__title '.$orcolor.'">';         
					$dis_str.=$oricon.$pwlist['action_label']."</br>".$username;
					$dis_str.='</span> </span></a>';
					$cw+1;
				}
			} 
			$dis_str.='</div> </div>';
		} 
		return $dis_str;
	}
function dateconvertdbformat($date){
	$dateformat = "";
	if($date !=""){
		$datesplit = str_replace('/"', '-',$date);
    	$dateformat =  date('Y-m-d', strtotime($datesplit));
	}
 	return $dateformat;   
}
function getPagenametotable($table_name,$page_type){
	$pages = array();
	foreach($_SESSION['application_pages'] as $page){
		$pages[$page['application_table_name']] = $page;
	}
	
 	return $pages[$table_name][$page_type];   
}
function togetlogindetails(){
	$url = API."?mode=loginafterdetails"; 
	$che = callAPI($url);
    $return = json_decode($che,1);
	return $return;
}
function emptytextdashShow($val){
	if($val!=""){
		return $val;
	}else{
		return " - ";
	}
}
function gettTimeZone($datetimezone="",$timezone){
	  date_default_timezone_set(INTERVIEWER_TIMEZONE);
	  $returnresult['timezonename'] = $timezone;
	  $date_time = new DateTime($datetimezone);
	  $date_time->setTimezone(new DateTimeZone(INTERVIEWER_TIMEZONE));
	  $returnresult['interviewer_timezone_details'] = $date_time->format('Y-m-d H:i:s');
	  
	  $destination_tz = new DateTimeZone($timezone);
	  $date_time->setTimezone($destination_tz);
	  $returnresult['timezonedatetimestring'] = strtotime($date_time->format('Y-m-d H:i:s'));
	  $returnresult['timezonedatetimecheck'] = $date_time->format('YmdHiA');
	  $returnresult['timezonedatetime'] = $date_time->format('Y-m-d H:i:s');
	  $returnresult['timezonetime12'] = $date_time->format('h:i A');
	  $returnresult['timezonetime24'] = $date_time->format('H:i');
	  return $returnresult;
}
function DynamicPhoneNumberFormt($phone_number){
	$formatted_number = $phone_number;
	if(isset($_SESSION['user']['countries']) AND $_SESSION['user']['countries']['phone_number_digit'] !=""){
		$pregacode = $_SESSION['user']['countries'];	
		$formatted_number = preg_replace($pregacode['phone_number_digit'], $pregacode['phone_number_view'], $phone_number);
	}elseif(isset($_SESSION['config']['countries'])  AND $_SESSION['config']['countries']['phone_number_digit'] !=""){
		$pregacode = $_SESSION['config']['countries'];	
		$formatted_number = preg_replace($pregacode['phone_number_digit'], $pregacode['phone_number_view'], $phone_number);
	}


	//$pregacode = DEFAULT_PHONE_NUMBER_FORMAT;
	//print_r($pregacode);
	//$formatted_number = preg_replace($pregacode['digit_format'], $pregacode['view_format'], $phone_number);
	//$formatted_number = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "($1) $2-$3", $phone_number);
	return $formatted_number;
}
function insertAuditTrail($parent_option ="",$extra_paramiter="",$extra_details =""){
	if($extra_paramiter ==""){
		$extra_paramiter = json_encode($_GET);
	}
	if($parent_option !=""){
		$paramiter .="&page_option=".$parent_option; 
	}
	$url = API."?mode=insertaudittrail&extra_paramiter=".$extra_paramiter."&extra_details=".$extra_details.$paramiter; 
	$che = callAPI($url);
    $return = json_decode($che,1);
	return $return;
}
 function getTheDay($date)
{
	$curr_date=strtotime(date("Y-m-d H:i:s"));
	$the_date=strtotime($date);
	$diff=floor(($curr_date-$the_date)/(60*60*24));
	switch($diff){
	    case 0:
	        return "Today";
	        break;
	    case 1:
	        return "Yesterday";
	        break;
	    case -1:
	        return "Tomorrow";
	        break;
	    default:
	        return $diff." Days ago";
	}
}
function viewTitlePage($title){
	//$array = array('COM_','LIST_','INSERT_','EDIT_','VIEW_','_');
	$array = array('com','COM_','_');
	foreach ($array as  $value) {
		$title = str_replace($value, ' ', $title);
	}
	return ucwords(strtolower(str_replace('_', ' ', $title)));
}
function getOptionPageTableName($name){
	foreach ($_SESSION['application_pages'] as  $value) {
		if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name){
			
			$pageSetails = $value;
		}
	}
	return $pageSetails; 
}
function convertamountnumber($ammunt){
  $amun =explode($_SESSION['config']['currency_symbol'],$ammunt);
  if(isset($amun[1])){
  	$replaceval = str_replace(',', '',$amun[1]);
  }else{
  	$replaceval = str_replace(',', '',$amun[0]);
  }
  return $replaceval;
}
function convertSecondsToHMS($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remainingSeconds = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $remainingSeconds);
}
function togetprojecttasktimelog() {
    $url = API."?mode=togetprojecttasktimelog&user_id=".$_SESSION['userid']; 
	$api = callAPI($url);
    $result = json_decode($api,1);
    return $result;
}
function getWorkflowStatus($table_name,$transaction_no,$current_status){
	//echo "table_name:".$table_name."--transaction_no:".$transaction_no."---current_status:".$current_status;
	$url = API."?mode=getdynamicworkflowsteps&transaction_table=".$table_name."&transaction_no=".$transaction_no."&current_status=".$current_status; 
	$api = callAPI($url);
    $result = json_decode($api,1);
   //echo "<pre>"; print_r($result); echo "</pre>";
    $workflowsteps = $result['workflowsteps'];
    $workflow_details = $result['workflow_details'];
    $status_progress = $result['status_progress'];
    $breadcrumb =$result['breadcrumb'];
    $verifystatusflow = $result['breadcrumb'];
    $verifystatusflow[ST_APPROVED] = ST_APPROVED;
    $nextstepkeyvalue = $result['nextstepkeyvalue'];
    $transaction_owner_id = $result['transaction_owner_id'];
    $transaction_approval_logs = $result['transaction_approval_logs'];
    $talapproved = $result['transaction_approval_logs_approved'];
    $application_user_module_roles = $result['application_user_module_roles'];
    $statuse = ViewStatusData();
    $current_transactionauthprized=$breadcrumb[$current_status];
  //  if($verifystatusflow['$current_status'])
   // $current_transactionauthprized['updatebtn'];
   // print_r($current_transactionauthprized);
   // $workflow_btn.='<label class="form-label label-lg label label-inverse-info" style="border:1px solid '.$statuse[$current_status]['background_color'].'; color: '.$statuse[$current_status]['background_color'].'">'.$status_progress[$current_status]['name'].'</label>';

	$current_status_ribbon = '<div class="ribbon ribbon-top-right"><span class="color-primary" style="background-color: '.$statuse[$current_status]['background_color'].'; color: '.$statuse[$current_status]['color'].'; z-index: 1;">'.$status_progress[$current_status]['name'].'</span></div>';

    if(!isset($result['error'])){
	 /**
	    $dis_str.='<div class="p-3"><div class="breadcrumb">';
	    $successicons = '<i class="far fa-check-circle text-success f-18 position-absolute m-t-10" style="left: 5px;"></i> &nbsp;';
	    foreach($nextstepkeyvalue as $statuskey => $nextstatus){
	    	$userdetails = $breadcrumb[$statuskey]['aproval_user'];
	    	$icons = $successicons;
	    	$display_name = "";
	    	if($statuskey == '0'){
	    		$display_name =  $_SESSION['users'][$transaction_owner_id];
	    	}else{
	    		if(count((array)$userdetails) == 1){
	    			$display_name = $userdetails[0]['permision'].' - '.$userdetails[0]['display_name'];
	    		}else{
	    			$multiuserlistfun = "viewApprovalAllUsers('".json_encode($userdetails)."')";
	    			//$display_name =  ucwords(str_replace("_"," ",($breadcrumb[$statuskey]['sub_approve_type']))).' <i class="ti-arrow-circle-down" onclick="'.$multiuserlistfun.'"></i>';
	    			//$display_name .=  ucwords(str_replace("_"," ",($breadcrumb[$statuskey]['sub_approve_type'])));
	    		$mk=1;	foreach($userdetails as $userdetail){
	    				$display_name .=  '<span class="d-block">'.$mk.'. '.$userdetail['display_name'].' ';	
	    				if(isset($talapproved[$userdetail['user_id']])){
	    					$display_name .=  '<i class="far fa-check-circle text-success f-10"></i>';
	    				}else{
	    					$display_name .=  '<i class="far fa-hourglass text-warning f-10"></i>';		
	    				}
	    				$display_name .=  '</span>';
	    			$mk++; }
	    			
	    		}	
	    	}
	    	if($statuskey == $current_status){
	    		$successicons = "";
	    		$icons = "<i class='far fa-hourglass text-warning f-18 position-absolute m-t-10' style='left: 5px;'></i>&nbsp;  ";
	    	}
			$dis_str.='<a href="#!" text-dark><span class="breadcrumb__inner" >';
			$dis_str.=  '<span class="breadcrumb__title">'.$icons.$status_progress[$statuskey]['name']."<span class='d-block f-12'>".$display_name."</span>";
			$dis_str.='</span> </span></a>';
		}
		$dis_str.='<a href="#"><span class="breadcrumb__inner"><span class="breadcrumb__title">';
		if($current_status == ST_APPROVED){
			$dis_str .= '<i class="far fa-check-circle text-success f-18 position-absolute m-t-10" style="left: 5px;"></i> &nbsp;';
		}
		$dis_str.=  "Approved";
		$dis_str.=  "</a>";
		$dis_str.='</div> </div>';

**/
//////////////////////////////////////////////////////////////////////////////////////

		$dis_str.='<ul class="breadcrumbs p-3">';
		$successicons = '<i class="far fa-check-circle text-success f-18 position-absolute m-t-10" style="left: 5px;" id="custom-popup"></i> &nbsp;';
		if(count((array)$nextstepkeyvalue) >=5){
			$breadcrumbwidthcall = "addwidth";
		}
		//if($workflow_details['workflow_level'] !=0){ // not Workflow Direct Approved
		foreach($nextstepkeyvalue as $statuskey => $nextstatus){
			if($statuskey != ST_APPROVED){
		    	$userdetails = $breadcrumb[$statuskey]['aproval_user'];
		    	$icons = $successicons;
		    	if($statuskey == '0' || ($statuskey == '3' AND $table_name == 'transaction_activity_schedule')){
		    		$display_name =  $_SESSION['users'][$transaction_owner_id];
		    	}else{
		    		if(count((array)$userdetails) == 1){
		    			if($workflow_details['breadcrumb_view_field_type'] == "R"){ // Role Show
		    				$display_name = $application_user_module_roles[$userdetails[0]['user_id']];
		    			}else{
		    				$display_name =  $userdetails[0]['permision'].' - '.$userdetails[0]['display_name'];
		    			}
		    		}else{
		    			//$multiuserlistfun = "viewApprovalAllUsers('".json_encode($userdetails)."')";
		    			$multiuserlistfun = "viewApprovalAllUsers('open-slide_popup".$statuskey."')";
		    			$display_name =  ucwords(str_replace("_"," ",($breadcrumb[$statuskey]['sub_approve_type']))).' <i class="ti-arrow-circle-down" onclick="'.$multiuserlistfun.'"></i>';
		    		}	
		    	}
		    	$activeclass ="";
		    	if($statuskey == $current_status){
		    		$successicons = "";
		    		$icons = "<i class='far fa-hourglass text-warning f-18 position-absolute m-t-10' style='left: 5px;'></i>&nbsp;  ";
		    		$activeclass = "active";
		    	}
		    	 $dis_str.='<li class="breadcrumbs_active '.$activeclass.' '.$breadcrumbwidthcall.'"><a href="#!"><span class="d-block">'.$icons.$status_progress[$statuskey]['name'].'</span><span class="d-block">'.$display_name.'</span></a>';

		    	 $dis_str .= '<div class="custom-popup-open open-slide_popup'.$statuskey.' text-start" style="left: 0px; top: auto; display: none; width: 350px;">
			    	 	<div class="card"><div class="card-header">
							<h5>Approval User</h5>
							<div class="card-header-right">
								<i class="ti-close" onclick="viewApprovalAllUsers()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
							</div>
						</div>';
				$dis_str .= '<div class="card-body">'; 
				$mk=1; 
				foreach($userdetails AS $userdetail){
					if(isset($talapproved[$userdetail['user_id']])){
						$icons =  '<i class="far fa-check-circle text-success f-16"></i>';
					}else{
						$icons =  '<i class="far fa-hourglass text-warning f-16"></i>';		
					}
					$dis_str .= '<p class="f-16 mb-4">'.$icons." ".$userdetail['permision'].' '.$mk.'. '.$userdetail['display_name'].'</p>';
				$mk++;} 
				
				$dis_str .= '</div>';
				$dis_str .= '</div>';
				$dis_str .= '</li>';



				//$dis_str.='<a href="#"><span class="breadcrumb__inner"><span class="breadcrumb__title">';
				
				// if($current_status == ST_APPROVED){
				// 	$dis_str .= '<i class="far fa-check-circle text-success f-18 position-absolute m-t-10" style="left: 5px;"></i> &nbsp;';
				// }
				// $dis_str.=  "Approved";
			// $dis_str.=  "</a>";
			}
		}	
		//}
		if($current_status == ST_APPROVED){
			$activeclassr = "active";
			$successiconsr = '<i class="far fa-check-circle text-success f-18 position-absolute m-t-10" style="left: 5px;" id="custom-popup"></i> &nbsp;';
		}
		$dis_str.='<li class="breadcrumbs_active '.$activeclassr.' '.$breadcrumbwidthcall.'"><a href="#!">
		<span class="d-block">'.$successiconsr.' Approved</span></a></li>';
		$dis_str.='</ul>';

///////////////////////////////////////////////////////

		if($current_status != ST_APPROVED AND $current_status != ST_REJECTED){
		$workflow_btn .= '<div class="btn-group workflow-btn" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">';
	    if($current_status == ST_ACTIVE OR $current_status == ST_SEND_BACK OR $current_status == ST_CALLBACK OR $workflow_details['workflow_trigger_status'] == $current_status){  //Active Time Show Button
	    	if($transaction_owner_id ==$_SESSION['userid']){
	    		if($current_status == ST_SEND_BACK OR $current_status == ST_CALLBACK){
	    			//$workflowsteps[$current_status]['current_status'] = ST_ACTIVE;
	    				$current_status = $workflow_details['workflow_trigger_status']; // direct use accept
		    			$nextstatus = $workflow_details['workflow_trigger_status']; // direct use accept
		    		// 	$current_status = ST_ACTIVE;
		    		// 	$nextstatus = ST_ACTIVE;
	    		}else{
	    			$nextstatus = $workflow_details['workflow_trigger_status'];
	    		}
	    		//$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".ST_ACTIVE."','".$workflowsteps[$current_status]['workflow_level_status']."','".$status_progress[$workflowsteps[$current_status]['current_status']]['name']."','".$status_progress[$workflowsteps[$current_status]['current_status']]['action_label']."')";
	    		//$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".ST_ACTIVE."','".$workflowsteps[$current_status]['workflow_level_status']."','".$status_progress[$workflowsteps[$current_status]['current_status']]['name']."','".$status_progress[$workflowsteps[$current_status]['current_status']]['action_label']."')";

	    		$dynamicfunction  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$nextstatus."','".$workflowsteps[$current_status]['workflow_level_status']."')";

	    		$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp; '.$status_progress[$workflowsteps[$current_status]['current_status']]['action_label'].'</button>';
	    	}else{
	    		$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light"><span class="fa fa-info text-white"></span> &nbsp;'.$_SESSION['error_message']['9002'].'</button>';
	    	}
	    }else{
	    	if($transaction_owner_id ==$_SESSION['userid']){ //ST_CALLBACK
	    		$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".$current_status."','".ST_CALLBACK."','".$status_progress[$workflowsteps[$current_status]['current_status']]['name']."','".$status_progress[ST_CALLBACK]['action_label']."')";
	    		$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-retweet text-white"></span> &nbsp; '.$status_progress[ST_CALLBACK]['name'].'</button>';
	    	
	    	}elseif(isset($transaction_approval_logs[$_SESSION['userid']])){	
	    			$next_step_owner = $_SESSION['userid'];
	    //	}elseif($current_transactionauthprized['user_id'] ==$_SESSION['userid']){

	    		//$dynamicfunction  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".$workflowsteps[$current_status]['workflow_level_status']."')";
	    		//$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp; '.$status_progress[$workflowsteps[$current_status]['current_status']]['action_label'].'</button>';
	    		$dynamicfunction  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".$nextstepkeyvalue[$current_status]."')";

		    	$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp; '.$status_progress[$nextstepkeyvalue[$current_status]]['action_label'].'</button>';
			  	
			  	$dynamicfunctionreject  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".ST_REJECTED."')";
			  	$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="'.$dynamicfunctionreject.'"> <span class="fa fa-thumbs-down text-white"></span> Reject</button>';

			  	$dynamicfunctionsendback  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".ST_SEND_BACK."')";
				$workflow_btn .= '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="'.$dynamicfunctionsendback.'"> <span class="fa fa-retweet text-white"></span> Send back</button>';
			}else{ //NOT AUTHORIZED
	    		//$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light"><span class="fa fa-info text-white"></span> &nbsp;'.$_SESSION['error_message']['9002'].'</button>';
	    	}
	    }
		$workflow_btn .= '</div>';
	}
		$returnarray = array('breadcrumb'=>$dis_str,'workflow_btn'=>$workflow_btn,'current_transaction_details'=>$current_transactionauthprized,'current_status_ribbon'=>$current_status_ribbon,'transaction_owner_id'=>$transaction_owner_id,'all_steps_workflow'=>$breadcrumb,'next_status'=>$nextstepkeyvalue[$current_status],'next_step_owner'=>$next_step_owner);	
	}else{

		$workflow_btn .= '<div class="btn-group workflow-btn" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">';
		if($result['error'] == '9061' AND $transaction_owner_id ==$_SESSION['userid'] AND $current_status == ST_DRAFT){
			$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".$current_status."','".ST_ACTIVE."','".$status_progress[$current_status]['name']."','".$status_progress[ST_ACTIVE]['name']."')";
			
			$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp;'.$status_progress[ST_DRAFT]['action_label'].'</button>';
		}elseif($result['error'] == '9063' OR $result['error'] == '9062' OR $result['error'] == '9060'){
			
			$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light"><span class="fa fa-info text-white"></span> &nbsp;'.$_SESSION['error_message'][$result['error']].'</button>';
			
		}
		$workflow_btn .= '</div>';
		$returnarray = array('workflow_btn'=>$workflow_btn,'current_transaction_details'=>$current_transactionauthprized,'current_status_ribbon'=>$current_status_ribbon,'transaction_owner_id'=>$transaction_owner_id,'next_status'=>$nextstepkeyvalue[$current_status]);	
	}	
	return $returnarray;
	
}


function resequenceMenuList($ary){
	$menucnt = 16;
	$runcnt = 0;
	$recall = array();
	$rowcnt = 0;
	$k = 1;
	foreach($ary as $key=>$child){
		$data= $child['child'];
		$startlabel = $child;
		unset($startlabel['child']);
		$return[$key] = $startlabel;
		if(count((array)$data) <=5){
			foreach($data as $keyid=>$values){
				$return[$key]['child'][$rowcnt][$keyid] = $values; 
				$rowcnt++; 
			}
		}else{	
			foreach($data as $keyid=>$values){
				if($runcnt >= $menucnt-2){
					$runcnt = 0;
					$rowcnt++; 
				}
				$childmenucnt = count((array)$values['child']);
				if($childmenucnt+$runcnt <= $menucnt){
					$return[$key]['child'][$rowcnt][$keyid] = $values; 
					$runcnt += $childmenucnt+1;
				}else{
					$recall[$keyid] = $values; 
				}
				if(count($recall) > 0){
					foreach($recall as $rkeyid=>$rvalues){
						if(count($data) == $k){
							$runcnt = 0;
							$rowcnt++;
						}
						$childmenucnt = count((array)$rvalues['child']);
						if($childmenucnt+$runcnt <= $menucnt){
							$return[$key]['child'][$rowcnt][$rkeyid] = $rvalues; 
							unset($recall[$rkeyid]);
							$runcnt += $childmenucnt+1;
						}else{
							$recall[$rkeyid] = $rvalues; 
						}	
					}	
				}
				$k++;
			}
		}
	}	
	return $return;
}

function resequenceMenuListWorking($data){
	$temarray = $data;
	$menucnt = 18;
	$runcnt = 0;
	$recall = array();
	$rowcnt = 0;
	$k = 1;
	foreach($data as $keyid=>$values){
		if($runcnt >= $menucnt-2){
			$runcnt = 0;
			$rowcnt++; 
		}
		$childmenucnt = count((array)$values['child']);
		if($childmenucnt+$runcnt <= $menucnt){
			$return[$rowcnt][$keyid] = $values; 
			$runcnt += $childmenucnt+1;
		}else{
			$recall[$keyid] = $values; 
		}
		if(count($recall) > 0){
			foreach($recall as $rkeyid=>$rvalues){
				if(count($data) == $k){
					$runcnt = 0;
					$rowcnt++;
				}
				$childmenucnt = count((array)$rvalues['child']);
				if($childmenucnt+$runcnt <= $menucnt){
					$return[$rowcnt][$rkeyid] = $rvalues; 
					unset($recall[$rkeyid]);
					$runcnt += $childmenucnt+1;
				}else{
					$recall[$rkeyid] = $rvalues; 
				}	
			}	
		}
		$k++;
	}
	return $return;
}

function dashviewlisthr(){
	$url = API."?mode=getreportnagerandbuddy&userid=".$_SESSION['userid']."&emp_id=".$_SESSION['emp_id'];
	$res = callAPI($url);
	return json_decode($res,1);
}
function getMainDomainName(){
	$url = SITEURL;
	$host = parse_url($url, PHP_URL_HOST);
	$parts = explode('.', $host);
	$mainDomain = $parts[count($parts)-2] . '.' . $parts[count($parts)-1];
	return $mainDomain;
}
function dateyearcalucates($date1,$date2=""){
	if($date2 ==""){
		$date2 = date('Y-m-d');
	}
	// Create DateTime objects from the date strings
	$dateTime1 = new DateTime($date1);
	$dateTime2 = new DateTime($date2);

	// Calculate the difference in years, months, and days
	$interval = $dateTime1->diff($dateTime2);
	$years = $interval->y;
	$months = $interval->m;
	$days = $interval->d;

	// Calculate the difference with decimals
	$decimalYears = round($years + ($months / 12) + ($days / 365),1);
	return $decimalYears;
}
function getApplicationEmptyTransactions($option){
	$url = API."?mode=dynamicapplicationemptytransactions&option=".$option;
	$res = callAPI($url);
	$result = json_decode($res,1); 
	$empty_transaction = $result['empty_transaction'];
	$mesg = "<div class='row'>";
	$show = 1;
	//echo "<pre>"; print_r($empty_transaction); echo "</pre>";
	foreach($empty_transaction as $et){
		if($et['cnt'] == '0'){
			$show = 0;
			$mesg .= '<div class="p-l-10 p-r-10 col-lg-6">
			    <div class="alert alert-info alert-dismissible">
			        <strong class="f-16"><i class="ti-alert "></i></strong> <a href="index.php?option='.$et['option'].'" target="_blank" class="text-primary">'.ucwords($_SESSION['error_message'][$et['application_message_code']].' '.$et['extra_message']).'</a>
			        <button type="button" class="btn-close text-danger" data-bs-dismiss="alert" aria-label="Close"></button>
			    </div>
			</div>';
		}
	}
	$mesg .= "</div>";
	if($show =='0'){
		echo $mesg;
	}
	///echo "<pre>"; print_r($result); echo "</pre>";
}
function getAssessmentYear() {
    $currentMonth = date('n');
    $currentYear = date('Y');
    
    // Assuming the assessment year starts from April
    if ($currentMonth >= 4) {
        // If the current month is April or later, the assessment year is the current year - current year + 1
        return $currentYear.'-'.($currentYear + 1);
    } else {
        // If the current month is before April, the assessment year is the previous year - current year
        return ($currentYear - 1).'-'.$currentYear;
    }
}
function calculateStringCalculation($string){
	$parts = preg_split('/(\+|\=|-|\*|%|\/)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
	$return = null;
	$operator = null;
	if(in_array('%',(array)$parts)){
        $return = ($parts[0] / 100) * $parts[2];
	}elseif(in_array('=',(array)$parts)){
        $return = $parts[0];
	}else{
		foreach ($parts as $part) {
		    if (is_numeric(trim($part))) {
		        switch ($operator) {
		            case '+':
		                $return += $part;
		                break;
		            case '-':
		                $return -= $part;
		                break;
		            case '*':
		                $return *= $part;
		                break;
		            case '/':
		                $return /= $part;
		                break;
		            default:
		                $return = $part;
		        }
		    }else{
		        $operator = trim($part);
		    }
		}
	}
	return $return;
}
function getPagePermision($option="",$transaction_table="",$transaction_type){
	$permissions_transaction = array();
	$permission = array('permission'=>'Y','tooltip'=>'','class'=>'');
	if($_SESSION['permission_check'] == 'Y'){
		if($option !="" || $transaction_table !=""){
			if($transaction_table ==""){ 
				foreach ($_SESSION['application_pages'] as  $value) {
					if($value['list_page_name']==$name || $value['insert_page_name']==$name || $value['edit_page_name']==$name || $value['view_page_name']==$name || $value['approval_page_name']==$name){ 
						$transaction_table = $value['application_table_name'];
					}
				}
			}
			$permissions_transaction = $_SESSION['role_permission'][$transaction_table];
			//echo "<pre>"; print_r($_SESSION['role_page_permission'][$option]); echo "</pre>";
			if($permissions_transaction['permission_view'] != '0' && $_SESSION['role_page_permission'][$option]['permission_view'] !='0' && $transaction_type == 'VIEW'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission','else'=>$transaction_table); 
			}elseif($permissions_transaction['permission_insert'] != '0' && $_SESSION['role_page_permission'][$option]['permission_insert'] !='0' && $transaction_type == 'INSERT'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission'); 
			}elseif($permissions_transaction['permission_import'] != '0' && $_SESSION['role_page_permission'][$option]['permission_import'] !='0' && $transaction_type == 'IMPORT'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_update'] != '0' && $_SESSION['role_page_permission'][$option]['permission_update'] !='0' && $transaction_type == 'UPDATE'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_approval'] != '0' && $_SESSION['role_page_permission'][$option]['permission_approval'] !='0' && $transaction_type == 'APPROVAL'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_list'] != '0'  && $_SESSION['role_page_permission'][$option]['permission_list'] !='0' && $_SESSION['role_page_permission'][$option]['permission_import'] !='0' && $transaction_type == 'LIST'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_delete'] != '0' && $_SESSION['role_page_permission'][$option]['permission_delete'] !='0' && $transaction_type == 'DELETE'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_print'] != '0' && $_SESSION['role_page_permission'][$option]['permission_print'] !='0' && $transaction_type == 'PRINT'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}elseif($permissions_transaction['permission_download'] != '0' && $_SESSION['role_page_permission'][$option]['permission_download'] !='0' && $transaction_type == 'DOWNLOAD'){ 
				$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission');
			}
		}else{
			$permission = array('permission'=>'N','tooltip'=>'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['403'].'"','class'=>'no-permission'); 
		}
		// if(in_array($option,(array)$_SESSION['application_no_permission_pages']['option'])){
		// 	$permission = array('permission'=>'Y','tooltip'=>'','class'=>'');
		// }
	}
	return $permission;
}
function accountingAmontFormat($amount,$symbol=0) {
	if($amount !=""){ 
		if($symbol == 0){
			if ($amount < 0) return '('.customeNumberFormate(abs($amount)).')';
	    	return customeNumberFormate($amount);
		}else{
			if ($amount < 0) return '('.customeummarmonyformate(abs($amount)).')';
	    	return customeummarmonyformate($amount);
		}  
	} 
}
function tocheckcontentempty($str){
	$return = "-";
	if($str!=""){
		$return = ucwords($str);
	}
	return $return;
}
function totalHoursAndMinutesInDateTimeArray($dateTimes){
	$totalSeconds = 0;
	for($i = 0; $i < count($dateTimes) - 1; $i++){
        $start = new DateTime($dateTimes[$i]);
        $end = new DateTime($dateTimes[$i + 1]);
	    $duration = $end->getTimestamp() - $start->getTimestamp();
	    if ($duration > 0) {
	        $totalSeconds += $duration;
	    }
	}
	$days = floor($totalSeconds / 86400);
	$hours = floor(($totalSeconds % 86400) / 3600);
	$minutes = floor(($totalSeconds % 3600) / 60);
	$seconds = $totalSeconds % 60;
	return array('days'=>$days,'hours'=>$hours,'minutes'=>$minutes,'seconds'=>$seconds);
	//echo "Total time: {$days} days, {$hours} hours, {$minutes} minutes, {$seconds} seconds\n";
    // $totalHours = 0;
    // $totalMinutes = 0;
    // for($i = 0; $i < count($dateTimes) - 1; $i++){
    //     $start = new DateTime($dateTimes[$i]);
    //     $end = new DateTime($dateTimes[$i + 1]);
    //     $interval = $start->diff($end);
    //     $totalHours += ($interval->days * 24) + $interval->h;
    //     //$totalMinutes += $interval->i.".".$interval->s;
    //    // $totalMinutes += $interval->i + ($interval->s / 60);
    //     $totalMinutes += $interval->h * 60 + $interval->i + ($interval->s / 60);


    // }
    // $totalHours += intdiv($totalMinutes, 60);
    // $totalMinutes = $totalMinutes % 60; // Remaining minutes after converting to hours
    // return array('hours'=>$totalHours,'minutes'=>$totalMinutes);
}
// function stringXOREncrypt($string) {
// 	$key = "secretkey";
//     $result = '';
//     $key_length = strlen($key);

//     // Allowed characters: A-Z, a-z, 0-9, _, -
//     $allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
//     $allowed_length = strlen($allowed_chars);

//     for ($i = 0; $i < strlen($string); $i++) {
//         // XOR each character with the key
//         $xor_char = ord($string[$i]) ^ ord($key[$i % $key_length]);

//         // Map the XOR result to allowed characters
//         $mapped_char = $allowed_chars[$xor_char % $allowed_length];
//         $result .= $mapped_char;
//     }

//     return $result;
// }

// function stringXORDecrypt($encrypted) {
// 	$key = "secretkey";
//     $result = '';
//     $key_length = strlen($key);

//     // Allowed characters: A-Z, a-z, 0-9, _, -
//     $allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
//     $allowed_length = strlen($allowed_chars);

//     for ($i = 0; $i < strlen($encrypted); $i++) {
//         // Get the index of the encrypted character in allowed characters
//         $index = strpos($allowed_chars, $encrypted[$i]);

//         // Reverse the XOR operation
//         $original_char = chr(($index ^ ord($key[$i % $key_length])) & 0xFF);
//         $result .= $original_char;
//     }

//     return $result;
// }
// function stringXOREncrypt($string) {
// 	$key = "secretkey";
//     $result = '';
//     $key_length = strlen($key);

//     for ($i = 0; $i < strlen($string); $i++) {
//         // XOR each character with the key and map to printable ASCII range
//         $xor_char = ord($string[$i]) ^ ord($key[$i % $key_length]);
//         $printable_char = chr(($xor_char % 94) + 33); // Map to ASCII 33-126
//         $result .= $printable_char;
//     }
//     return $result;
// }
// function stringXORDecrypt($encrypted) {
// 	$key = "secretkey";
//     $result = '';
//     $key_length = strlen($key);
//     for ($i = 0; $i < strlen($encrypted); $i++) {
//         // Reverse mapping from printable range
//         $mapped_value = ord($encrypted[$i]) - 33;
//         $original_char = $mapped_value ^ ord($key[$i % $key_length]);
//         $result .= chr($original_char);
//     }
//     return $result;
// }
function encodeString($input){
    $randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $randomStart = substr(str_shuffle($randomChars), 0, 2);
    $randomEnd = substr(str_shuffle($randomChars), 0, 2);
    $modifiedString = $randomStart . $input . $randomEnd;
    $reversedString = strrev($modifiedString);
    return urlencode($reversedString);
}
function decodeString($encoded){
    $decoded = urldecode($encoded);
    $originalReversed = strrev($decoded);
    $originalString = substr($originalReversed, 2, -2); // Remove 7 chars from start and end
    // Ensure the decoded string is below 50 characters
    //return (strlen($originalString) < 50) ? $originalString : "Error: Decoded string exceeds 50 characters!";
    return $originalString;
}
function crmLastActivity($crmld,$created_date){
    $meg = $crmld['max_updated_date']." ";
    if($crmld['lead_type'] == '2' || $crmld['lead_type'] == '3'){
        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$crmld['comment'];
    }elseif($crmld['lead_type'] == '4'){
        $meg = "<span class='d-block'>".$crmld['phone']." ".$crmld['time']."</span>".$crmld['sms_text'];
    }elseif($crmld['lead_type'] == '5'){
        $meg .= "<span class='d-block'>".$crmld['recipient_email_id']."</span>".$crmld['sms_text'];
    }elseif($crmld['lead_type'] == '7'){ //meeting
        $member = json_decode($crmld['member'],1);
            //($member);
            $membermsg = "";
            foreach($member as $members){
                $membermsg .= "<span class='m-r-5'>".$_SESSION['users'][$members].",</span>";
            }
        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$membermsg." <span class='d-block'>".$crmld['comment']."</span>";
    }elseif($crmld['lead_type'] == '8'){ //visit
         $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
         $meg .= "<span class='d-block'>".$crmld['address']." ".$crmld['post_code']."</span>";
         $meg .=$crmld['comment'];
    }else{
         $meg .= $crmld['comment'] !=""?$crmld['comment']:"-";
    }

    if(!isset($crmld['comment'])){
        $meg = $created_date." Created";
    }
    return $meg; 
}
?>
