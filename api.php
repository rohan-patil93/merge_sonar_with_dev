<?php
header("Access-Control-Allow-Origin: http://contracts.wftracker.com"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");
ini_set('display_errors', 1);
include('dbconfig.php');
include('includes/dbconnection.php');
include('jwt_tokan/vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
date_default_timezone_set('Asia/Kolkata');
$headers = getallheaders();
$infi_token = $headers['Infi-Token'] ?? '';
$method = $_GET['method'];
$mode = $_GET['mode'];
$notchecktokanary = array('login','domainverifye','signup','forgotpassword');
/*if(!in_array($mode, $notchecktokanary) AND $method == 'post'){
	if($infi_token){
		 try {
	    	$decoded = JWT::decode($infi_token, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));  
	    } catch (Exception $e) {
	        http_response_code(401);
	        echo json_encode([
	            'valid' => false,
	            'status'=>'Fail',
	            'message' => $e->getMessage(),
	        ]);
	        exit;
	    }   
	}else{
		http_response_code(401);
	        echo json_encode([
	            'valid' => false,
	            'status'=>'Fail',
	            'message' => 'Token not provided. Please provide the token to proceed.',
	    ]);
	    exit;
	}
}

if($_GET['mode'] != 'login' && isset($_GET['token'])){
	include("model/user_token.class.php");
	$usertkenObj=new UserTokenClass();
	$tokrn_res = $usertkenObj->tocheckusertoken($_GET);
	if($tokrn_res['status']=='Fail'){
		$res = array('status'=>'token_invalid', 'code'=>"TOKEN_INVALID",'msg'=>'Token is invalid');
		echo json_encode($res); exit;
	}
}
foreach($_GET as $getval){
	if(is_array($getval)){
		$res = array('status'=>'Fail', 'code'=>"CHECK_PARAMETER", 'msg'=>'Parameter is incorrect');
		echo json_encode($res); exit;
	}
}*/
include("model/user.class.php");
include("model/integartion.class.php");
include("model/demo.class.php");
include("model/asset_user.class.php");
include("model/claim_user.class.php");
include("model/recruitment_user.class.php");
include("model/pms_user.class.php");
include("model/esop_user.class.php");
include("model/account_user.class.php");
include("model/rental_user.class.php");
include("model/petty_cash.class.php");
include("model/vms.class.php");
include("model/hr_user.class.php");
include("model/space_user.class.php");
//include("model/stocktake_user.class.php");
//include('model/sitesettings.class.php');
/*include("model/bill.class.php");
$billObj=new billClass();*/
$userObj=new userClass();
$intiObj=new integartionClass();
$demoObj=new demoClass();
$assetObj=new assetUserClass();
$claimObj=new claimUserClass();
$recruitmentObj=new recruitmentUserClass();
$pmsObj=new pmsUserClass();
$esopObj=new esopUserClass();
$accountObj=new accountUserClass();
$rentalObj=new rentalUserClass();
$pettycashObj=new pettyCashUserClass();
$vmsObj=new vMSUserClass();
$hrObj=new hrUserClass();
$spaceObj=new spaceUserClass();
//$stocktakeObj=new stocktakeuserClass();
//print_r($_GET);
// echo "Test".$mode."|| -- ".$method; exit;
if ($method == 'post'){
	if($_GET['platform']=='react'){
		$jsonData = file_get_contents('php://input');
		// Decode the JSON data to a PHP associative array
		$_POST = json_decode($jsonData, true);
		if (json_last_error() === JSON_ERROR_NONE) {
	    	$_GET['json_data'] = 'Y';
		} else {
	    	$_GET['json_data'] = 'N';
		}
	}
	//echo "fff".$mode;
	if($mode == 'postApprovalDiscussionMessage'){
		include_once("model/web_view.class.php");
		$wvObj = new webViewClass();
		if($_POST['user_id'] == ''){
			$_POST['user_id'] = $_GET['user_id'];
		}
		if($_POST['company_id'] == ''){
			$_POST['company_id'] = $_GET['company_id'];
		}
		if($_POST['module_id'] == '' && $_GET['module_id'] != ''){
			$_POST['module_id'] = $_GET['module_id'];
		}
		$details = $wvObj->postApprovalDiscussionMessage($_POST);
		echo json_encode($details);
		exit;
	}
	if($mode == 'loginflutter'){
		//print_r($_POST); print_r($_GET);		
		$post = $_POST;
		$data['chrUserName'] = $post['useremail'];
		$data['chrPassword'] = $post['password'];
		$data['api'] = "api call done";
		//print_r($data); exit;
		$res =$userObj->checkAdmin($data);
		//$res['error'] = false;
		echo json_encode($res);
	}
	if($mode == "infisuitejobcandedatesave"){
		if($_POST['extid']!=""){
			$res = $intiObj->infisuitejobcandedatesave($_POST,$_GET);
			echo json_encode($res); exit;
		}
		
	}
	if($mode == "toinsertupdatethejobpostingpublish"){
		$res = $intiObj->toinsertupdatethejobpostingpublish($_POST,$_GET);
		echo json_encode($res); exit;
	}
	if($mode == 'adddata'){
		//echo "<pre>";
		//echo "ha"; exit;
		if($_GET['table'] == TBL_REPORT_ATTRIBUTES){
			$transation = $userObj->toGetApplicationSerialNumber(TBL_REPORT_ATTRIBUTES);
			$_POST['report_code'] = $transation;
		}
		// if($_GET['table'] == 'candidates'){
		// 	$transation = $userObj->toGetApplicationSerialNumber('candidates');
		// 	$_POST['candidate_code'] = $transation;
		// }
		$data = $_POST;
		//print_r($data); echo "faf"; exit;
		$res = $userObj->insertTablesdata($data, $_GET['table']);
		if($_GET['table'] == TBL_USERS && $res['status'] == 'sucess'){
			$res1 = $userObj->NewUserformate($data,$_GET['data_name']);
		}

		// if($res['status'] == 'sucess' AND ($_GET['table'] == 'vendors' || $_GET['table'] == 'vendor_registers')){
		// 	include("model/vendor_portal_user.class.php");
		// 	$vendportObj=new vendorPortelUserClass();
		// 	$vendportObj->getVendorandVendorRegistersSynchAPI($data,$_GET['table'],$res['LASTID']);
        // }

		echo json_encode($res);
	}
	if($mode == 'updateapprovallog'){
		$data = $_POST;
		//print_r($data);
		$res = $userObj->updateapprovallog($data);
		echo json_encode($res);
		
	}
	if($mode == 'updatedynamicapprovallog'){
		$data = $_POST;
		//print_r($data);
		$res = $userObj->updatedynamicapprovallog($data);
		echo json_encode($res);
		
	}
	if ($mode == 'updaterowtabledata') {
		//echo "sada"; exit;
		$rid = $_GET['rid'];
		$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
		$data = $_POST;
		//echo "ads".$fid; exit;
		$res = $userObj->updateTablesdata($data,$_GET['table'],$fid,$rid);

		// if($res['status'] == 'sucess' AND ($_GET['table'] == 'vendors' || $_GET['table'] == 'vendor_registers')){
		// 	include("model/vendor_portal_user.class.php");
		// 	$vendportObj=new vendorPortelUserClass();
		// 	$transupdate = $vendportObj->getVendorandVendorRegistersSynchAPI($data,$_GET['table'],$rid);
		// 	$res['vendor_update'] = $transupdate;
        // }
        if($post['table']=='project_task_details' && $rid =!""){
			$task_query = "SELECT id, project_id, task_name FROM project_task_details WHERE id=".$rid;
			$task_data  = $callConfig->getRow($task_query);
			$task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND  project_task_id=".$rid." AND is_active=0";
			$teams  = $callConfig->getAllRows($task_team_q);
			//print_r($teams); exit;
			foreach((array)$teams as $list){
				$mobilsendnotification = array();
				$mobilsendnotification = $get;
				$mobilsendnotification['message'] ="Someone has udated  to the task details ".$task_data->task_name;
				if(isset($data['is_active']) && $data['is_active']==1){
					$mobilsendnotification['message'] ="Someone has deleted  to the task details ".$task_data->task_name;
				}
				
				$mobilsendnotification['user_id'] =$list->user_id;
				$mobilsendnotification['title'] = 'Task Comment';
				$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['transaction_id'], 'project_id' =>$task_data->project_id, 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
				//print_r($mobilsendnotification); exit;
				$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);
			}

		}

		echo json_encode($res); exit;
	}
	if($mode == 'addinvoietype'){
		$data = $_POST;
		//echo $_POST;
		//print_r();
		//print_r($_POST); exit;
		//$data = json_decode($_GET['data'],1);
		$res = $userObj->insertInvoiceType($data,$_GET['table'],$_GET);
		echo json_encode($res); exit;
	}
	if($mode == 'updaterowbasepimarydata'){
		$rid = $_GET['rid'];
		$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
		$data = $_POST;
		$res = $userObj->updaterowbasepimarydata($data,$_GET['table'],$rid);
		echo json_encode($res); exit;
	}
	
	if($mode == "addbills"){
		//echo "ravi"; exit;
		$res = $userObj->insertBills($_POST,$_GET['type']);
		echo json_encode($res); exit;
	}
	if($mode == "addpccash"){
		//echo "ravi"; exit;
		$res = $userObj->insertPC($_POST,$_GET['type']);
		echo json_encode($res); exit;
	}
	if($mode == 'addletters'){
		$details = $userObj->addletters($_POST,$_GET['type']);
		echo json_encode($details);
	}
	if($mode == 'addjournalvouchers'){
		$res = $userObj->addjournalvouchers($_POST,$_GET['table'],$_GET['type']);
		echo json_encode($res);
	} 
	
	if($mode=='inserttasks'){
		$details = $userObj->inserttasks($_POST,$_GET);
		echo json_encode($details);	
	}
	if($mode == 'displayattributetye'){
		//echo "ravi"; exit;
		//echo "hi".$_GET['data']."<pre>";
		//$post = json_decode(str_replace('Empty','', urldecode($_GET['data'])),1);
		//$post = json_decode(urldecode($_GET['data']),1);
		//print_r($post);
		$post = $_POST;
		$data['application_table_name'] = $post['table'];
		$data['field_name'] = $post['field'];
		$data['display_field'] = $post['value'];
		$data['value_field'] = $post['field_name'];
		$data['content'] = urlencode($post['dis_custom_sql']);
		$data['refer_action'] = $post['refer_action'];	
		$data['refer_field'] = $post['refer_field'];	
		$data['refer_by_action'] = $post['refer_by_action'];	
		$data['refer_by_field'] = $post['refer_by_field'];
		if($post['content'] != ""){
			$data['content'] = $post['content'];
		}
		if($post['type'] == 'static'){
			for($i=1; $i<$post['count']+1; $i++){
				$key = $post['static_value_'.$i];
				$value =  $post['static_lable_'.$i];
				$arra[$key]= $value;
			}
			$data['content'] = json_encode($arra);
		}
		
		if(isset($post['discontent']) && $post['discontent'] != ""){
			$discon = explode('#', $post['discontent'] );
			foreach ($discon as $dis) {
				$val = explode('=', $dis);
				$key = $val[0];
				$value =  $val[1];
				$arra[$key]= $value;
			}
			//print_r($arra);
			for($i=1;$i < (count($discon)/6)+1 ; $i++){
				$array['select'][]=$arra['select1_value_'.$i];
				$array['select'][]=$arra['select2_value_'.$i];
				$array['text'][]=$arra['text1_value_'.$i];
				$array['text'][]=$arra['text2_value_'.$i];
				$array['opration'][]=$arra['oparends1_value_'.$i];
				$array['opration'][]=$arra['oparends2_value_'.$i];
			}
			//print_r($array);
			$array = json_encode($array);
			$data['content'] = $array;
		}

		$data['company_id'] = $post['company_id'];
		$data['attribute_type'] = $post['type'];
		$data['content_details'] = urlencode(json_encode($post));
		print_r($data); //exit;
		
		$whr = "application_table_name='".$post['table']."' and field_name='".$post['field']."'";
		//echo $whr;
		$userObj->deleterows(TBL_DISPLAYATTRIBUTES_TYPE,$whr);
		//exit;
		$res = $userObj->insertTablesdata($data, TBL_DISPLAYATTRIBUTES_TYPE);
		print_r($res);
		echo $res; exit;
	}
	if($mode == 'addorupdatewizardsettings'){
		/*echo "ravi";
		print_r($_POST); print_r($_GET); exit;*/
		$res =$userObj->addorupdatewizardsettings($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'addorupdategoals'){
		$res =$userObj->addorupdategoals($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'addsalary'){
		$res =$userObj->addorupdatesalary($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'addgradesalary'){	
		$res =$userObj->addorupdategradesalary($_POST,$_GET);	
		echo json_encode($res);		
	}
	if($mode == 'addgradesalary'){		
		$res =$userObj->addorupdategradesalary($_POST,$_GET);		
		echo json_encode($res);			
	}
	if($mode == 'addjobpostingapplicatantdata'){
		$res =$userObj->addjobpostingapplicatantdata($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'candidateautoregistation'){
		//echo "api check"; exit;
		$res =$userObj->candidateautoregistation($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'addorupdatecandaitedocument'){
		//echo "api check"; exit;
		$res =$userObj->addorupdatecandaitedocument($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'updatecandaitedocument'){
		//echo "api check"; exit;
		$res =$userObj->updatecandaitedocument($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'toinsertpayrnsummary'){
		//echo "api check"; exit;
		$res =$userObj->toinsertpayrnsummary($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'genratecandidatetoemployee'){
	//echo "ravi"; exit;
		$details = $userObj->genratecandidatetoemployee($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode == 'genratecandidatesalarytemplate'){
		//echo "api check"; exit;
		$res =$userObj->genratecandidatesalarytemplate($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'usernotify'){
		//echo "api check"; exit;
		$res =$userObj->usernotify($_POST,$_GET);
		echo json_encode($res);	
	}
	if($mode == 'employeeclocks'){
        //echo "api check"; exit;
		$res =$userObj->employeeclocks($_POST,$_GET);
		echo json_encode($res);   
	}
	if($mode =="multiinsert"){
		//echo "hai"; exit;
		//print_r($_POST); exit;
		$res = $userObj->testTableInsert($_POST,$_GET['filename'],$get);
		echo $res;
	}
	
	if($mode =="payrollupdateonetime"){
		//echo "hai"; exit;
		//print_r($_POST); exit;
		$res = $userObj->payrollupdateonetime($_POST,$_GET);
		echo $res;
	}
	if($mode =="addorupdateitdeclaration80c"){
		//echo "hai"; exit;
		//print_r($_POST); exit;
		$res = $userObj->addorupdateitdeclaration80c($_POST,$_GET);
		echo $res;
	}
	if($mode=='processtasksettings') {	
		$details = $userObj->processtasksettings($_POST,$_GET);	
		echo json_encode($details); 	
	}	
	if($mode=='gettotalemployeesbyfilter') {	
		$details = $userObj->gettotalemployeesbyfilter($_POST,$_GET);	
		echo json_encode($details);	
	}	
	if($mode=='savetofiltertable') {	
		$details = $userObj->savetofiltertable($_POST,$_GET);	
		echo json_encode($details);	
    	//echo "345";	
	}
	if($mode=='employeedetailsupdate') {
        //echo "employeedetailsupdate";exit;
		$details = $userObj->employeedetailsupdate($_POST,$_GET);
		echo json_encode($details); 
	}

	if($mode=='employeesdisplayfieldsset') {
        //echo "employeedetailsupdate";exit;
		$details = $userObj->employeesdisplayfieldsset($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode=='sendtousersettings'){
		$details = $userObj->saveusersettings($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='getemployeeattendance') {
		//print_r($_POST);
		$details = $userObj->getemployeeattendance($_POST,$_GET);
   	 //exit;
		echo json_encode($details); 
	}
	if($mode=='setuppayroll') {
		$details = $userObj->setuppayroll($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='setuphr') {
		$details = $userObj->setuphr($_POST,$_GET);
		echo json_encode($details);
	}
	
	if($mode=='saveleaveapplication') {
		$details = $userObj->saveleaveapplication($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode=='addeployeeshifts') {
    	//echo "Ravi"; exit;
		$details = $userObj->addeployeeshifts($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='setupvendor'){
		$details = $userObj->setupvendor($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode=='savetermination'){
		$details = $userObj->savetermination($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='insertrecognition'){
		$details = $userObj->insertrecognition($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='updatebulktransferhistory'){
		$details = $userObj->updatebulktransferhistory($_POST,$_GET);
		echo json_encode($details);
	}
		
	if($mode=='insertinquiries'){
		$details = $userObj->insertinquiries($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='addupdateproject'){
		$details = $userObj->addupdateproject($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='addjobpostingdistribute'){
		$details = $userObj->addjobpostingdistribute($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='toinsertempcontracts'){
		$details = $userObj->toinsertempcontracts($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='insertandupdatevendorranks'){
		$details = $userObj->insertandupdatevendorranks($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode == 'updateprojecttimesheets'){
		$details = $userObj->updateprojecttimesheets($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode == 'insertanupdateempfoodoption'){
		$details = $userObj->insertanupdateempfoodoption($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='createassessments'){
		$details = $userObj->createassessments($_POST,$_GET);
		echo json_encode($details);
    	//exit;
	}
	if($mode=='saveassessmentreponses'){
		$details = $userObj->saveassessmentreponses($_POST,$_GET);
		echo json_encode($details);
        	//exit;
	}
	if($mode=='saveperformanceresponse'){
		$details = $userObj->saveperformanceresponse($_POST,$_GET);
		echo json_encode($details);
    	//exit;
	}
	if($mode=='savepeerfeedbackquestions'){
		$details = $userObj->savepeerfeedbackquestions($_POST,$_GET);
		echo json_encode($details);
	}

	if($mode=='createempperformancepeerfeedback'){
		$details = $userObj->createempperformancepeerfeedback($_POST,$_GET);
		echo json_encode($details);
    	//exit;
	}
	
	if($mode=='saveperformanceresponse'){
		$details = $userObj->saveperformanceresponse($_POST,$_GET);
		echo json_encode($details);
    	//exit;
	}
	if($mode=='savepeerfeedbackquestions'){
		$details = $userObj->savepeerfeedbackquestions($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode == 'insertandupdateperformancesetupquestions'){
    	//echo "ravi"; print_r($_POST); exit;
		$details = $userObj->insertandupdateperformancesetupquestions($_POST,$_GET);
		echo json_encode($details);
	}
	
	if($mode == 'insertandupdateempexpenses'){
		$details = $userObj->insertandupdateempexpenses($_POST,$_GET);
		echo json_encode($details);
	}
	
	if($mode == 'insertseriallot'){
		$details = $userObj->insertseriallot($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode == 'togetmyreporserach'){
		$details = $userObj->togetmyreporserach($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode == 'toinsertandpdateesignemployee'){
		//echo "ravi"; print_r($_POST);print_r($_GET);
		$details = $userObj->toinsertandpdateesignemployee($_POST,$_GET);
		echo json_encode($details); 
	}	
	if($mode == 'toinsertandpdateesignemployeeonlyone'){
		//echo "ravi"; print_r($_POST);print_r($_GET);
		$details = $userObj->toinsertandpdateesignemployeeonlyone($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode=='createjobfeedback'){
		//echo "ssss"; exit;
		$details = $userObj->createjobfeedback($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='savejobfeedbackreponses'){
		//echo "ssss"; exit;
		$details = $userObj->savejobfeedbackreponses($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='addorupdategradedailysalary'){
		//echo "ssss"; exit;
		$details = $userObj->addorupdategradedailysalary($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='addremovetransport'){
		//echo "ssss"; exit;
		$details = $userObj->addremovetransport($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode == 'readchekallinbox'){
		$res = $userObj->readchekallinbox($_POST,$_GET);
		echo json_encode($res);
	} 
	if($mode=='addfloderupdatereportid'){
		//echo "ssss"; exit;
		$details = $userObj->addfloderupdatereportid($_POST,$_GET);
		echo json_encode($details);
	}
	if($mode=='togetemployeedetaila'){
		$details = $userObj->togetemployeedetaila($_POST,$_GET);
		echo json_encode($details); 
	}
	if($mode == 'addupdateassetsallocation'){
		$res = $assetObj->addupdateassetsallocation($_POST,$_GET);
		echo json_encode($res);
	}
	if ($mode == 'login'){
		$res =$userObj->checkAdmin($_POST);
		if(isset($res->id)){
			$issuedAt = time();
        	$expirationTime = $issuedAt + 3600;
        	$payload = [
            	'sub' => $res->display_name,
            	'iat' => $issuedAt,
            	'exp' => $expirationTime,
            	'company_id' => $res->display_name,
        		'data_name' => $res->data_name,
        		'userid' => $res->id,
        	]; 
        	$token = JWT::encode($payload, JWT_SECRET_KEY, JWT_ALGORITHM);
        	$res->infi_token = $token;	
		}else{

		}
		echo json_encode($res);
	}
	//$check_postget_array = array('togetemployeeageprofile'=>'togetemployeeageprofile','togetemployeeageprofile'=>'togetemployeeageprofile','addfloderupdatereportid'=>'addfloderupdatereportid','addfloderupdatereportid'=>'addfloderupdatereportid','addremovetransport'=>'addremovetransport','insertupdateesgcpopup'=>'insertupdateesgcpopup',,'insertletters'=>'insertLetters',);
	$vms_post_class_array =  array('savevendoragreements'=>'savevendoragreements', 'indertupdateperdiem'=>'indertupdateperdiem', 'updateprojecttimesheetsblukforhr'=>'updateprojecttimesheetsblukforhr', 'updateprojecttimesheetsblukapprovehr'=>'updateprojecttimesheetsblukapprovehr', 'projecttimesheetsubmittofinace'=>'projecttimesheetsubmittofinace', 'updateprojecttimesheetsblukapprovefinace'=>'updateprojecttimesheetsblukapprovefinace', 'getemployeetimesheetsummary'=>'getemployeetimesheetsummary', 'findbuddyid'=>'findbuddyid','edittimesheetbillablehrs'=>'editTimeSheetBillableHrs', 'addtimesheetentries'=>'addtimesheetentries', 'getBillableAmountTimesheetcalculatio'=>'getBillableAmountTimesheetcalculatio', 'insertupdateemployeebuddies'=>'insertupdateemployeebuddies', 'vrcandpoemployeeassign'=>'vrcandpoemployeeassign', 'insertupdateesgcpopup'=>'insertupdateesgcpopup', 'insertupdatepovrcpopup'=>'insertupdatepovrcpopup', 'addorremoveusersubtable'=>'addorremoveusersubtable','vmssingleemployeedashoard' => 'vmssingleemployeedashoard','insertpmcasignemployee'=>'insertPmcAsignEmployee','getassignemployeelistpopup'=>'getAssignEmployeeListPopup','getmanpowercontarctratperhrs'=>'getmanpowercontarctratperhrs','updateapprovallogprojecttimesheets'=>'updateapprovallogprojecttimesheets','udateleaveforvms'=>'udateleaveforvms', 'gettimesheetsummarydashboard' => 'getTimeSheetSummaryDashboard', 'bugetaccrualvendorrareport' => 'bugetaccrualvendorrareport', 'updaterateandvendorprice' => 'updaterateandvendorprice', 'togeuseracesstable' => 'togeuseracesstable','createvendorfeedback'=>'createvendorfeedback','addvendorfeedbackresponse'=>'addvendorfeedbackresponse','addupdatequestionfeddback'=>'addupdatequestionfeddback', 'insertupdatevendorsetup'=>'insertupdatevendorsetup','addupdateprojecttimesheetmontly'=>'addupdateprojecttimesheetmontly', 'getmilestoneAssignEmployeeListPopup'=>'getmilestoneAssignEmployeeListPopup','insertvmsleaverequest'=>'insertvmsleaverequest', 'dynamicchangestatusmonthprojecttimesheet' => 'dynamicchangestatusmonthprojecttimesheet', 'toupdatepurchaseordertransaction' => 'toupdatepurchaseordertransaction','insertvendorportalapplicationusers'=>'insertVendorPortalApplicationUsers', 
	'updateleaverequest' => 'updateleaverequest', 
	'saveemployeeexit' => 'saveemployeeexit', 
	'updateapprovalstatus' => 'updateapprovalstatus','tosavecommentdataquater'=>'tosavecommentdataquater'
		,'saveApprovedTimesheetOvertime'=>'saveApprovedTimesheetOvertime'
		,'updateManagerTeamLeaveStatus'=>'updateManagerTeamLeaveStatus','insertvmsquarterrateapply'=>'insertvmsquarterrateapply', 'addupdateprojecttimesheetweekly'=>'addupdateprojecttimesheetweekly','dynamicchangestatusweekprojecttimesheet' => 'dynamicchangestatusweekprojecttimesheet','directapplyweeklyleave'=>'directapplyweeklyleave');
	if(isset($vms_post_class_array[$mode])){
		$fun = $vms_post_class_array[$mode];
		$details = $vmsObj->$fun($_POST,$_GET);
		echo json_encode($details);
	}

	if($mode == 'updatestationeryitem'){
		$details = $intiObj->updatestationeryitem($_POST, $_GET);
		echo json_encode($details);
		exit;
	}
	$check_postget_integartion_array =  array('togetemployeeageprofile'=>'togetemployeeageprofile','togetemployeebirthdays'=>'togetemployeebirthdays','togetemployeeeodetails'=>'togetemployeeeodetails','togetreportemploymentstatushistory'=>'togetreportemploymentstatushistory','togetemployeegenderprofile'=>'togetemployeegenderprofile','togetreportsalaryhistory'=>'togetreportsalaryhistory','togetemployeaddtionterminations'=>'togetemployeaddtionterminations', 'togetemployeemployeeheadcount'=>'togetemployeemployeeheadcount', 'togetemployeemployeeturnover'=> 'togetemployeemployeeturnover','togetreportlastpaychange'=>'togetreportlastpaychange', 'togetreportemployeesatisfaction'=>'togetreportemployeesatisfaction','togetreportemployeewelbeing'=>'togetreportemployeewelbeing','togetstandaredapprovedhours'=>'togetstandaredapprovedhours', 'togetstandaredtimeoffuser'=>'togetstandaredtimeoffuser', 'togetstandaredtimeoffbalances'=>'togetstandaredtimeoffbalances', 'togetstandaredtimeoffschedule'=>'togetstandaredtimeoffschedule', 'submitsprojectinsert'=>'submitsprojectinsert', 'updatestatusdynamicprojecttask'=>'updatestatusdynamicprojecttask', 'updatemoretabledata'=>'updatemoretabledata', 'checkandupdatecolorprojecttask'=>'checkandupdatecolorprojecttask', 'insertstatusprocesss'=>'insertstatusprocesss', 'addupdateprojectlabeldata'=>'addupdateprojectlabeldata','updatelabelval'=>'updatelabelval','copynewcard'=>'copynewcard', 'toaddupdatetheprojectteamdata'=>'toaddupdatetheprojectteamdata', 'toinsertthedataonitemquality'=>'toinsertthedataonitemquality', 'updateprojectdependencydetail'=> 'updateprojectdependencydetail', 'addupdateprojectphases' => 'addupdateprojectphases', 'addupdaterolememberdetail' => 'addupdaterolememberdetail','addcouriers'=>'addcouriers', 'updatecouriers'=>'updatecouriers', 'toinserttranspordatarequest'=>'toinserttranspordatarequest', 'addstationery'=>'addstationery', 'updatestationeryrequest'=>'updatestationeryrequest', 'topupstationery'=>'topupstationery', 'addfoodrequest'=>'addfoodrequest', 'savegenralcodedetails'=>'savegenralcodedetails','leaveapplycreateemployee'=>'leaveapplycreateemployee', 'togetemployeeprofessionaltaxexemptions'=>'togetemployeeprofessionaltaxexemptions','insertmoduleprocess'=>'insertmoduleprocess','insertapplicationserialnumbers'=>'insertApplicationSerialNumbers');
	if(isset($check_postget_integartion_array[$mode])){
		$fun = $check_postget_integartion_array[$mode];
		$details = $intiObj->$fun($_POST,$_GET);
		//print_r($_GET);
		echo json_encode($details);
		
	}

	$check_array = array('insertletters'=>'insertLetters','statusprogressaction'=>'insertORUpadateStatusprogressaction','productupload'=>'productupload','budgetinsert'=>'budgetinsert','serchforpos'=>'serchforpos','querynotificationandemail'=>'querynotificationandemail','insertvenderquatation'=>'ToInserttransactions','upadateblukapproval'=>'upadateblukapproval','budgetcheckreftadledata'=>'budgetcheckreftadledata','reportfilter'=>'insertorupdatereportfilter','insertorupdatepermissions'=>'insertorupdatepermissions','insertprintdesigning'=>'insertPrintDesigning','insertdisplayattribute'=>'insertDisplayAttribute','imageuploading'=>'imageuploading','insertautodesigntable'=>'insertAutoDesignTable','insertemphistries'=>'insertEmpHistries','tosendnotifiations'=>'tosendnotifiations','interviewschedule'=>'interviewSchedule','insertjobpost'=>'insertJobPost','insertrole'=>'insertRole','resetnewpassword'=>'resetNewPassword','insertemailcontain'=>'insertEmailContain','insertreportattributesnew'=>'insertReportAttributesNew','insertworkflownew'=>'insertWorkFlowNew','updatedynamicapprovallognew'=>'updateDynamicApprovalLogNew','updatedynamicapprovallognew'=>'updateDynamicApprovalLogNew','insertleaverequest'=>'insertLeaveRequest','insertapplicationtours'=>'insertapplicationtours','inserttickets'=>'insertTickets','getemployeetickets'=>'getEmployeeTickets','insertprojecttasklogs'=>'insertProjectTaskLogs','insertemployeedetails'=>'insertemployeedetails','insertpayareaasignemployee'=>'insertpayareaasignemployee','insertpayrolladminasignemployee'=>'insertpayrolladminasignemployee','insertvendorinvitation'=>'insertVendorInvitation','insertexpensespolicies'=>'insertexpensespolicies','insertexpensespoliciescategory'=>'insertExpensesPoliciesCategory','insertexpensesmileagesperdiem'=>'insertExpensesMileagesPerDiem','insertstocktakes'=>'insertStockTakes','insertstocktakesitems'=>'insertStockTakesItems','getstocktakeadjustments'=>'getStockTakeAdjustments','insertcompliance'=>'insertCompliance','insertcompliancetaskanswer'=>'insertComplianceTaskAnswer','insertapplicationuser'=>'insertapplicationuser','insertjobtitlecompetencies'=>'insertJobTitleCompetencies','inserperformanaceappraiserheader'=>'inserPerformanaceAppraiserHeader','udateleaveforvms' => 'udateleaveforvms', 'loginafterdetails' => 'loginafterdetails', 'togetemployeeuserids' => 'togetemployeeuserids','insertperformancesetup'=>'insertPerformanceSetup','insertfeeds'=>'insertFeeds','inserthrservices'=>'insertHRServices','insertcompanysubscriptionplans'=>'insertCompanySubscriptionPlans','insertcompanysubscription'=>'insertCompanySubscription','insertregistiredgroups'=>'insertRegistiredGroups','insertadditionalserviceslicense'=>'insertAdditionalServicesLicense','insertcandidatedetails'=>'insertCandidateDetails','insertuserinvitation'=>'insertUserInvitation','assignecandidatejobpostingpopup'=>'assigneCandidateJobPostingPopup','insertcandidatespopup'=>'insertCandiDatesPopup','insertcandidatelogs'=>'insertCandidateLogs','sendcandidatemailpopup'=>'sendCandidatEmailPopup','candidaterequestdocs'=>'candidateRequestDocs','insertcandidateaccessright'=>'insertCandidateAccessRight','insertcandidateschedule'=>'insertCandidateSchedule','getcandidateschedule'=>'getCandidateSchedule','updatemoduleselection'=>'UpdateModuleSelection','submitstatusdynamicworkflow'=>'submitStatusDynamicWorkFlow','insertdocumentaccesssetups'=>'insertDocumentAccessSetups','inserttransactionaccesspermission'=>'insertTransActionAccessPermission','updatetransactionactivityschedule'=>'updatetransactionactivityschedule','updateonboardingtaskstatus'=>'updateOnBoardingTaskStatus','insertcommentjobposting'=>'insertCommentJobPosting','inserttagjobposting'=>'inserttagjobposting', 'addorupdateitdeclarationotherincome'=>'addorupdateitdeclarationotherincome','insertpayments'=>'insertPayments','submitsetup'=>'submitSetup','insertapplicationemptytransactions'=>'insertApplicationEmptyTransactions','updateprojecttaskdetailsfollowup'=>'updateProjectTaskDetailsFollowUp','insertdynamiccancelinvoice'=>'insertDynamicCancelInvoice','checkemailidallreadyavilableornot'=>'checkEmailidAllReadyAvilableOrNot','updateapplicationusers'=>'updateApplicationUsers','updateapplicationfavourites'=>'updateApplicationFavourites','insertemployeesdisplayfields'=>'insertEmployeesDisplayFields','updateemployees'=>'updateEmployees','insertempbanks'=>'insertEMPBanks','insertvendorcustomeritems'=>'insertVendorCustomerItems','insertreceipts'=>'insertReceipts','insertitems'=>'insertItems','insertitemcategories'=>'insertItemCategories','insertclonetransactions'=>'insertCloneTransactions','getitemsearch'=>'getItemSearch','insertimportexceldata'=>'insertImportExcelData','insertdynamicinvoice'=>'insertdynamicinvoice','insertskucode' => 'insertskucode','checkalreadyavailableornotcustomerorvendordetails'=>'checkalreadyavailableornotcustomerorvendordetails','submitmultireferencedocumnets'=>'submitMultiReferenceDocumnets');
	if(isset($check_array[$mode])){
		$fun = $check_array[$mode];
		// echo $fun; exit;
		$details = $userObj->$fun($_POST);
		echo json_encode($details);
	}
	$check_array = array('intertequipments'=>'intertEquipments');
	if(isset($check_array[$mode])){
		require_once "phpqrcode/qrlib.php";
		$fun = $check_array[$mode];
		$details = $spaceObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}
	$check_array = array('insertasset'=>'insertAsset');
	if(isset($check_array[$mode])){
		require_once "phpqrcode/qrlib.php";
		$fun = $check_array[$mode];
		$details = $assetObj->$fun($_POST,$_GET);
		echo json_encode($details);
	}
	$check_array_getpost = array('insertapplicationuserinvitations'=>'insertApplicationUserInvitations','cancelpayments'=>'cancelPayments');
	if(isset($check_array_getpost[$mode])){
		$fun = $check_array_getpost[$mode];
		//echo $fun; exit;
		$details = $userObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}
	// $demo_class_array =  array();
	// if(isset($demo_class_array[$mode])){
	// 	$fun = $demo_class_array[$mode];
	// 	$details = $demoObj->$fun($_POST,$_GET);
	// 	echo json_encode($details);
	// }

	$asset_class_array =  array('insertassetallocation'=>'insertAssetAllocation','assetaccepts'=>'assetAccepts','insertassetreturn'=>'insertAssetReturn','insertassetrequest'=>'insertAssetRequest','insertassetextension'=>'insertAssetExtension','updateassetallocation'=>'updateAssetAllocation','assetallocationaws'=>'assetAllocationAws','assetacceptreectvms'=>'assetacceptreectvms','getemployeeassetandassetsdata' => 'getemployeeassetandassetsdata','togetassetsempassetdetails' => 'togetassetsempassetdetails', 'togetassestdashboarddetails' => 'togetassestdashboarddetails','insertassetverification'=>'insertAssetVerification','updateassetverificationstatus'=>'updateAssetVerificationStatus','insertassetrequesttransfers'=>'insertAssetRequestTransfers');
	if(isset($asset_class_array[$mode])){
		$fun = $asset_class_array[$mode];
		$details = $assetObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}	


	$esop_class_array =  array('insertcompanyvaluations'=>'insertCompanyValuations','insertesopplans'=>'insertESOPPlans','insertesopallocations'=>'insertESOPAllocations');
	if(isset($esop_class_array[$mode])){
		$fun = $esop_class_array[$mode];
		$details = $esopObj->$fun($_POST,$_GET);
		echo json_encode($details);
	}


	$account_class_array =  array('insertfinanacereportgroups'=>'insertFinanaceReportGroups','insertchartofaccounts'=>'insertChartOfAccounts','inserttocheaders'=>'insertTOCHeaders','insertnotes'=>'insertNotes','insertjournals'=>'insertJournals','inserttaxauthorities'=>'insertTaxAuthorities','insertfiscalpositions'=>'insertFiscalPositions','insertdeferredrevenues'=>'insertDeferredRevenues','insertfollowuplevels'=>'insertFollowUpLevels','insertreconciliations'=>'insertReconciliations', 'budgetinsertaccount'=>'budgetinsertaccount','insertjournalvouchers'=>'insertJournalVouchers','insertbankvouchers'=>'insertBankVouchers','insertcashvouchers'=>'insertCashVouchers','insertdebitandcreditnotes'=>'insertDebitAndCreditNotes');
	if(isset($account_class_array[$mode])){
		$fun = $account_class_array[$mode];
		$details = $accountObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$rental_class_array =  array('insertpropertyowners'=>'insertPropertyOwners','insertpropertytenantdetails'=>'insertPropertyTenantDetails','insertcustomers'=>'insertCustomers','updatepropertytenantbillings'=>'updatePropertyTenantBillings','insertdebitnote'=>'insertDebitNote','insertcreditnote'=>'insertCreditNote','inserttenantopeningbalance'=>'insertTenantOpeningBalance','updatepropertytenantdetails'=>'updatePropertyTenantDetails','updateRentalInvoiceData'=>'updateRentalInvoiceData');
	if(isset($rental_class_array[$mode])){
		$fun = $rental_class_array[$mode];
		$details = $rentalObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$pms_class_array =  array('insertperformanceappraisalsetups'=>'insertPerforManceAppraisalSetups','insertperformanceappraisalquestions'=>'insertPerformanceAppraisalQuestions','addkra'=>'addkra','addkpidata'=>'addorupdatekpi','insertgoaldata'=>'insertGoalData','insertgoals'=>'insertGoals','insertfeedbacks'=>'insertFeedBacks','inserperformanaceappraiser'=>'inserPerformanaceAppraiser','insertemployeeperformanceappraisals'=>'insertEmployeePerformanceAppraisals','getemployeeperformanceappraisalsnormalisations'=>'getEmployeePerformanceAppraisalsNormalisations','insertappraisalbellcurves'=>'insertAppraisalBellCurves','publishcommentandrank'=>'publishCommentAndRank');
	if(isset($pms_class_array[$mode])){
		$fun = $pms_class_array[$mode];
		$details = $pmsObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$pettycash_class_array =  array('insertpettycashrequests'=>'insertPettyCashRequests','insertpettycash'=>'insertPettyCash');
	if(isset($pettycash_class_array[$mode])){
		$fun = $pettycash_class_array[$mode];
		$details = $pettycashObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$hr_class_array =  array('Insertimportswapinout'=>'InsertImportSwapInOut','inserttempattendaceform'=>'inserttempattendaceform','insertwithoutofftempattendancedetails'=>'insertWithOutOffTempAttendanceDetails', 'saveshiftemployee'=>'saveshiftemployee', 'togetleaveemployedetails'=>'togetleaveemployedetails','holdpayroll_exclusions'=>'holdpayroll_exclusions', 'insertpayrollexcludd'=>'insertpayrollexcludd','insertcustomerinvitation'=>'insertcustomerinvitation','savedataforprofessionaltaxexemptions'=>'savedataforprofessionaltaxexemptions','insertemployeeterminations'=>'insertEmployeeTerminations', 'tovmsobjothrsidecornjobvendors'=>'tovmsobjothrsidecornjobvendors', 'addsalarycontractemployeepay'=>'addsalarycontractemployeepay', 'inserttaxslab'=>'inserttaxslab', 'Resetthrpayrolldatabasedonmonthandyearandpayarea' => 'Resetthrpayrolldatabasedonmonthandyearandpayarea');
	if(isset($hr_class_array[$mode])){
		$fun = $hr_class_array[$mode];
		$details = $hrObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$recruitment_class_array =  array('candidateofferAccept'=>'candidateofferAccept');
	if(isset($recruitment_class_array[$mode])){
		$fun = $recruitment_class_array[$mode];
		$details = $recruitmentObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	//$vendor_porter_class_array =  array('insertapplicationtickets'=>'insertApplicationTickets','updateapplicationtickets'=>'updateApplicationTickets', 'insertvendorregistrationrequests'=>'insertVendorRegistrationRequests','updatesendverificationprofilestatus'=>'updateSendVerificationProfileStatus','updatevendorprofileupdatestatus'=>'updateVendorProfileUpdateStatus','updatevendorverifications'=>'updateVendorVerifications','insertvendordisplayfields'=>'insertVendorDisplayFields', 'getdynamicthirdpartyapicall'=>'getDynamicThirdPartyAPICall','insertupdatevendorverificationdetails' => 'insertupdatevendorverificationdetails','insertvendorandvendorregisterssynchapi'=>'insertVendorandVendorRegistersSynchAPI');
	$vendor_porter_class_array =  array('insertapplicationtickets'=>'insertApplicationTickets','updateapplicationtickets'=>'updateApplicationTickets', 'insertvendorregistrationrequests'=>'insertVendorRegistrationRequests','updatesendverificationprofilestatus'=>'updateSendVerificationProfileStatus','updatevendorprofileupdatestatus'=>'updateVendorProfileUpdateStatus','updatevendorverifications'=>'updateVendorVerifications','insertvendordisplayfields'=>'insertVendorDisplayFields', 'togetcontractemployeepaydetails'=>'togetcontractemployeepaydetails','togetcontractorctcreportdetails'=>'togetcontractorctcreportdetails', 'getdynamicthirdpartyapicall'=>'getDynamicThirdPartyAPICall','insertupdatevendorverificationdetails' => 'insertupdatevendorverificationdetails','insertvendorandvendorregisterssynchapi'=>'insertVendorandVendorRegistersSynchAPI','msmeconfirmationupdate'=>'MSMEConfirmationUpdate','insertvendorsandvendorregisters'=>'insertVendorsAndVendorRegisters','sendemailapiwithoutlogdocumentverification'=>'sendEmailAPIWithoutLogDocumentVerification','insertapplicationuserinvitations'=>'insertApplicationUserInvitations','getvendoradmindashboardpost'=>'getVendorAdminDashboardPost');
	if(isset($vendor_porter_class_array[$mode])){
		include("model/vendor_portal_user.class.php");
		$hrObj=new vendorPortelUserClass();
		$fun = $vendor_porter_class_array[$mode];
		$details = $hrObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$space_class_array =  array('insertbuildings'=>'insertBuildings','insertbuildingfloorcabins'=>'insertBuildingFloorCabins','insertequipmentallocations'=>'insertEquipmentAllocations', 'insertequipmentincidents'=>'insertEquipmentIncidents','insertbuildingfloorspaceallocations'=>'insertBuildingFloorSpaceAllocations');
	if(isset($space_class_array[$mode])){
		$fun = $space_class_array[$mode];
		$details = $spaceObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}
	$mobilecheck_array = array('toaddupdateusermobiledetails'=>'toaddupdateusermobiledetails', 'toaddmobileempleave'=>'toaddmobileempleave', 'toupdatemobileempleave'=>'toupdatemobileempleave','toinsertfieldmapuser'=>'toinsertfieldmapuser', 'tocreatecomplanatedetails'=>'tocreatecomplanatedetails', 'toupdatecomplanatedetails'=>'toupdatecomplanatedetails', 'toaddcomplaintshisorydetails'=>'toaddcomplaintshisorydetails', 'mobileuserregistation'=>'mobileuserregistation', 'toaddmobfielddailyplans'=>'toaddmobfielddailyplans', 'toaddmobfieldwork'=>'toaddmobfieldwork', 'toaddmobilehelpdetails'=>'toaddmobilehelpdetails', 'toupdatedynamicfielddata'=>'toupdatedynamicfielddata', 'togetpostvaldynmicdetials'=>'togetpostvaldynmicdetials', 'dynamicinsertformobile'=>'dynamicinsertformobile', 'toinsertpurchaseorderonmobile'=>'toinsertpurchaseorderonmobile','insertmobilereceipts'=>'insertMobileReceipts','insertmobileemployeeleaves'=>'insertMobileEmployeeLeaves','addupdaterolememberdetailformobile'=>'addupdaterolememberdetailformobile','insertbookingagainstreceipt'=>'insertBookingAgainstReceipt','insertbookingagainstsalesinvoices'=>'insertBookingAgainstSalesInvoices', 'toinserttripexpencesdetails'=>'toinserttripexpencesdetails','toupdatetripexpencesdetails' => 'toupdatetripexpencesdetails', 'toaddmultipletypeannocumetcomment' => 'toupdatetripexpencesdetails', 'toaddalltypelikdidcomother'=> 'toaddalltypelikdidcomother','insertmobidynamicmappingtransactions'=>'insertMobiDynamicMappingTransactions', 'toaddpersonaldataonmobile'=> 'toaddpersonaldataonmobile','toupdatepersonaldataonmobile'=>'toupdatepersonaldataonmobile', 'saveAttendanceRegularizationRequestData' => 'saveAttendanceRegularizationRequestData', 'savevehiclegatepass' => 'savevehiclegatepass', 'manageStationeryCart' => 'manageStationeryCart', 'createStationeryRequest' => 'createStationeryRequest', 'insertAssetRequests' =>'insertAssetRequests','insertWorkFromHomeRequests' => 'insertWorkFromHomeRequests', 'toupdateprojecttaskteamremove'=>'toupdateprojecttaskteamremove',  'toaddprojecttaskdetails'=>'toaddprojecttaskdetails','toinsertpurchaseordermobile'=>'toinsertpurchaseordermobile','adddataprojecttask'=>'adddataprojecttask','toinsertupdateprojecttasktimlog'=>'toinsertupdateprojecttasktimlog', 'updatedynamicfiedrojecttaskdetail' => 'updatedynamicfiedrojecttaskdetail');

	if(isset($mobilecheck_array[$mode])){
		//echo "RRRR"; exit;
		include("model/mobile_user.class.php");
		$mobileObj = new mobileUserClass();

		if($mode == "insertmobilereceipts"){
			$file_uploads_path = "uploads/receipts/";
			if($_FILES['receipt_image']['error'] != "4"){
				$temp = explode(".", $_FILES['receipt_image']['name']);
				$Filename=  "file_".time()."_".uniqid();
				$newfilename = $Filename.'.' .end($temp);
				if(move_uploaded_file($_FILES['receipt_image']["tmp_name"], $file_uploads_path.$newfilename)){
					$_POST['receipt_image'] = $newfilename;
				}
			}else{
				$_POST['receipt_image'] = $_POST['old_receipt_image'];
			} 
		}

		$fun = $mobilecheck_array[$mode];
		//print_r($_GET); print_r($_POST); exit;
		$details = $mobileObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$amc_account_class_array =  array(
		'insertamcaccountdetails' => 'insertAmcAccountDetails',
		'insertamcregistrationdetails' => 'insertAmcRegistrationDetails','updateamcservicedetailsandchecklist' => 'updateAmcServiceDetailsAndCheckList','insertstationerydata' => 'insertstationerydata'
	);
	if (isset($amc_account_class_array[$mode])) {
		include("model/amc_account_details.class.php");
		$amcObj = new amcAccountDetailsClass();
		$fun = $amc_account_class_array[$mode];
		$details = $amcObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	$phpmysqlclass_array = array('postphpmysqldetails'=>'postPHPMYSQLDetails','exicutephpmysql'=>'exicutePHPMYSQL');
	if(isset($phpmysqlclass_array[$mode])) {
		include("model/phpmysql.class.php");
		phpMysqlClass::requireSuperAdmin(); // RBAC: super-admin only, enforced server-side
		$phpmysqlObj=new phpMysqlClass();
		$fun = $phpmysqlclass_array[$mode];
		$details = $phpmysqlObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}

	/*if($mode=='toinsertfieldmapuser'){
		//echo "ssss"; exit;
		$details = $mobileObj->toinsertfieldmapuser($_GET,$_POST,$_FILES);
		///echo json_encode($details);
	}*/

	$tailly_array1 = array('checkapidatavalidateornot'=>'checkAPIDataValidateOrNot','apidetailstoretodatabase'=>'aPIDetailStoretoDatabase');
	if(isset($tailly_array1[$mode])) {
		include("model/tally.class.php");
		$tallyObj=new tallyClass();
		$fun = $tailly_array1[$mode];
		$details = $tallyObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}


	$tailly_array = array('topostmasterdetails' => 'topostmasterdetails');
	if (isset($tailly_array[$mode])) {
		$jsonData = file_get_contents('php://input');

		// Decode the JSON data to a PHP associative array
		$data = json_decode($jsonData, true);
		if (json_last_error() === JSON_ERROR_NONE) {
	    	$_GET['json_data'] = 'Y';
		} else {
	    	$_GET['json_data'] = 'N';
		}
		//echo "<pre>";  print_r($data); print_r($_POST); print_r($_GET); echo "</pre>"; exit;
		include("model/tally.class.php");
		$tallyObj=new tallyClass();
		$fun = $tailly_array[$mode];
		$details = $tallyObj->$fun($_GET, $data);
		echo json_encode($details);
	}
	$react_post = array('dynamicadddatareact'=>'dynamicadddatareact', 'dynamicupdateatareact'=>'dynamicupdateatareact','insertcontracts'=>'insertContracts','insertworkflowreact'=>'insertworkflowreact','insertpermissionreact'=>'insertpermissionreact','insertworkflowactivityreact'=>'insertworkflowactivityreact','insertcontrolversionreact'=>'insertcontrolversionreact','insertactivityreact'=>'insertactivityreact','insertCustomersreact'=>'insertCustomersreact','insertVendorsreact'=>'insertVendorsreact','cancelcontractreact' => 'cancelcontractreact','insertcontractsreact'=>'insertcontractsreact','insertcontractrechargedetails'=>'insertContractRechargeDetails','importContractCSV'=>'importContractCSV','insertcontractdata'=>'insertcontractdata','renewcontractreact' => 'renewcontractreact', 'getcontractslistpost'=>'getContractsListPost',
		// added by koti start
		'assigncontractamctechnician' => 'assignContractAmcTechnician',
		'savecontractamcassettechnicians' => 'saveContractAmcAssetTechnicians',
		'savecontractamcchecklist' => 'saveContractAmcChecklist',
		'confirmcontractamcfmvisit' => 'confirmContractAmcFmVisit',
		'updatecontractamcscheduledetails' => 'updateContractAmcScheduleDetails',
		'saveamcrepairorder' => 'saveAmcRepairOrder',
		// added by koti end
		'amcinsertcontractsreact'=>'AMCInsertContractsReact',
		'generatecontractsexceltemplate'=>'generateContractsExcelTemplate',
		'validatecontractsexcel'=>'validateContractsExcel',
		'importcontractsexcel'=>'importContractsExcel',
		'getcontractsexcelbatch'=>'getContractsExcelBatch',
		'saveuserassignedcontracttypes'=>'saveuserassignedcontracttypes',
		'softdeleteuserassignedcontracttypes'=>'softdeleteuserassignedcontracttypes'
	);
	if(isset($react_post[$mode])) {
		
		//echo "Api call check "; print_r($data); print_r($_GET);  exit;
		include("model/react.class.php");
		$reactObj=new reactClass();
		$fun = $react_post[$mode];
		$details = $reactObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	if($mode=="insertdocumnentreact"){
		include("model/react.class.php");
		$reactObj=new reactClass();
		$res =$reactObj->insertdocumnentreact($_GET, $_POST, $_FILES);
		echo json_encode($res);
	}
	$post_array = array('insertcrmleads'=>'insertCRMLeads','insertcrmleaddetails'=>'insertCRMLeadDetails','dynamicupdatestatus'=>'dynamicUpdateStatus','leadconverttocontacts'=>'leadConvertToContacts','insertcontactdata' => 'insertcontactdata','insertleaddata' => 'insertleaddata', 'importleadcontactCSV' => 'importleadcontactCSV','insertFeasibilityBomAndChecklist' => 'insertFeasibilityBomAndChecklist','checkcompanynameavilableornotincustomer'=>'checkCompanyNameAvilableOrNotInCustomer', 'insertFeasibilityfeildslist' => 'insertFeasibilityfeildslist');
	if(isset($post_array[$mode])){
		include("model/crm_user.class.php");
		$crmuserObj=new crmuserClass();
		$fun = $post_array[$mode];
		$details = $crmuserObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}
	$onbording_emp_class_array =  array('assigntasktoemployeesmanagement'=>'assignTasktoEmployeesManagement','updatesendverificationdocumentsem'=>'updateSendVerificationDocuments');
	if(isset($onbording_emp_class_array[$mode])){
		include("model/employee_management_user.class.php");
		$emObj=new employeeManagementUserClass();
		$fun = $onbording_emp_class_array[$mode];
		$details = $emObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	if($mode == "imageuploadtoreact"){
		$images = explode("," , $_POST['image_names']);
		/*print_r($_POST);		
		print_r($images);
		print_r($_GET);
		 print_r($_FILES); exit;	*/
		 $get = $_GET;
		$file_uploads_path = "uploads/all/";
		foreach($images as $img){
			$image = trim($img);			
			//print_r($_FILES); //exit;
			$temp = explode(".", $_FILES[$image]['name']);
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$insert_data = array('company_id'=>$_GET['company_id'],'current_status'=>'0','is_active'=>'0','file_name'=> $_FILES[$image]['name'],'actual_file_name'=>$newfilename,'file_size_mb'=>$_FILES[$image]['size'],'application_table_name'=>$get['table_name'],'transaction_id'=>$get['transaction_id']);
			$res = $userObj->insertTablesdata($insert_data, 'application_attachments');
			if(move_uploaded_file($_FILES[$image]["tmp_name"], $file_uploads_path.$newfilename)){
				//echo "<br> yesss";
				$res[$image] = $newfilename;
			}/*else{
				echo "<br> Nooo";
			}*/
		}
		echo json_encode($res);exit; 
	}


	$csr_array = array(
		'tovCSRObjOthrSideCornJob' => 'tovCSRObjOthrSideCornJob','insertUsersData' => 'insertUsersData',
		'insertOrUpdateCSRData' => 'insertOrUpdateCSRData',	'insertOrUpdateSingleTableData' => 'insertOrUpdateSingleTableData',	'insertSyncLog' => 'insertSyncLog',
		'insertBulkCsrPoUpload' => 'insertBulkCsrPoUpload',
		'insertBulkCsrInvoiceUpload' => 'insertBulkCsrInvoiceUpload',
		'insertCsrProjectDocument' => 'insertCsrProjectDocument',
		'updateCsrProjectDocument' => 'updateCsrProjectDocument',
		'saveCsrProjectTracking' => 'saveCsrProjectTracking',
		'saveCsrProposalProjectDates' => 'saveCsrProposalProjectDates'
	);
	if (isset($csr_array[$mode])) {
		include_once("model/csr.class.php");
		$csrObj = new CSRClass();
		$fun = $csr_array[$mode];
		$details = $csrObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	$erp_class_array =  array('insertkittings'=>'insertKittings','insertdekittings'=>'insertDeKittings','checkitemcategoryaccountdata'=>'checkitemcategoryaccountdata');
	if(isset($erp_class_array[$mode])){
		include("model/erp_user.class.php");
		$erpObj=new erpUserClass();
		$fun = $erp_class_array[$mode];
		$details = $erpObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	$claim_class_array = array('insertempexpenses'=>'insertEMPExpenses','getuserreimbursementpolicies'=>'getUserReimbursementPolicies','insertAndUpdateTrevllingPlans' => 'insertAndUpdateTrevllingPlans');
	if(isset($claim_class_array[$mode])){
		$fun = $claim_class_array[$mode];
		$details = $claimObj->$fun($_GET,$_POST);
		echo json_encode($details);
	}

	// $security_arr = array('insertVisitorBundle' => 'insertVisitorBundle', 'saveVisitorOut' => 'saveVisitorOut');
	// if (isset($security_arr[$mode])) {
	// 	include_once("model/security.class.php");
	// 	$SecurityObj = new SecurityClass();
	// 	$fun = $security_arr[$mode];
	// 	$details = $SecurityObj->$fun($_GET, $_POST);
	// 	echo json_encode($details);
	// }
//Omkar Added
	$security_arr = array('insertVisitorBundle' => 'insertVisitorBundle', 'saveVisitorOut' => 'saveVisitorOut','gettexttodyanmicsearching'=>'getTextToDyanmicSearching');
	if (isset($security_arr[$mode])) {
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$fun = $security_arr[$mode];
		$details = $SecurityObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
//Omkar Added
	$manufacturing_arr = array('insertmanufacturingboms' => 'insertManufacturingBOMS','insertmanufacturingworkorders'=>'insertManufacturingWorkOrders');
    if (isset($manufacturing_arr[$mode])) {
        include_once("model/manufacturing.class.php");
        $manufacturingObj = new manufacturingUserClass();
        $fun = $manufacturing_arr[$mode];
        $details = $manufacturingObj->$fun($_GET, $_POST);
        echo json_encode($details);
    }
	$manfacturing_forecast_arr = array(
        'insertandupdatemanufacturingforecaste' => 'insertandupdatemanufacturingforecaste',
        'insertandupdatemanufacturingfirmorder' => 'insertandupdatemanufacturingfirmorder',
        'insertmanufacturingproductnote' => 'insertmanufacturingproductnote',
        'insertstocktransfer' => 'insertstocktransfer',
        'insertstocktransferreceipt' => 'insertstocktransferreceipt',
        'insertmanufacturingproductionstart' => 'insertManufacturingProductionStart',
        'updatemanufacturingproductiondata' => 'updateManufacturingProductionData',
        'resumemanufacturingproduction' => 'resumeManufacturingProduction',
    );
	if (isset($manfacturing_forecast_arr[$mode])) {
		include_once("model/manfacturing_forecast.class.php");
		$mfObj = new manfacturingForecastUserClass();
		$fun = $manfacturing_forecast_arr[$mode];
		$details = $mfObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	$manufacturing_process_qc_arr = array(
        'saveprocessqc' => 'saveprocessqc',
        'saveprocessqcitemdraft' => 'saveprocessqcitemdraft',
        'completeprocessqcitem' => 'completeprocessqcitem',
    );
    if (isset($manufacturing_process_qc_arr[$mode])) {
        include_once("model/integartion.class.php");
        $intiObj = new integartionClass();
        $fun = $manufacturing_process_qc_arr[$mode];
        $details = $intiObj->$fun($_POST, $_GET);
        echo json_encode($details);
    }
	// $mobile_view_arr = array('insertandupdatemanufacturingforecaste' => 'insertandupdatemanufacturingforecaste', 'insertandupdatemanufacturingfirmorder'=>'insertandupdatemanufacturingfirmorder', 'insertmanufacturingproductnote' => 'insertmanufacturingproductnote');
	// if (isset($mobile_view_arr[$mode])) {
	// 	include_once("model/manfacturing_forecast.class.php");
	// 	$mfObj = new manfacturingForecastUserClass();
	// 	$fun = $mobile_view_arr[$mode];
	// 	$details = $mfObj->$fun($_GET, $_POST);
	// 	echo json_encode($details);
	// }
	if ($mode == 'getBuddyTypeEmployees') {
		include_once("model/employee_loan.user.class.php");
		$elObj = new employeeLoanClass();
		$res = $elObj->getBuddyTypeEmployees($_GET, $_POST);
		echo json_encode($res);
		exit;
	}
	if ($mode == 'get_buddy_employees') {
		include_once("model/employee_loan.user.class.php");
		$elObj = new employeeLoanClass();
		$res = $elObj->getBuddyEmployees($_GET, $_POST);
		echo json_encode($res);
		exit;
	}
	if ($mode == 'confirm_buddy_reassignment') {
		include_once("model/employee_loan.user.class.php");
		$elObj = new employeeLoanClass();
		$res = $elObj->confirmBuddyReassignment($_GET, $_POST);
		echo json_encode($res);
		exit;
	}
	$emp_loan_arr = array('saveemployeeloan' => 'saveemployeeloan','insertprocessdetails' => 'insertprocessdetails','insertvehiclegateway' => 'insertvehiclegateway','updatevehicleexit' => 'updatevehicleexit', 'insertfuelentry' => 'insertfuelentry','updatefuelentry' => 'updatefuelentry','deletefuelentry' => 'deletefuelentry','insertpersonalgateway' => 'insertpersonalgateway', 'updatepersonalexit' => 'updatepersonalexit','inserttransportvehiclemaster' => 'inserttransportvehiclemaster', 'updatetransportvehiclemaster' => 'updatetransportvehiclemaster', 'deletetransportvehiclemaster' => 'deletetransportvehiclemaster','saveEmployeeAttendance' => 'saveEmployeeAttendance', 'saveLeaveAllocation' => 'saveLeaveAllocation', 'saveClockTime' => 'saveClockTime','updatevehiclegatewayexit' => 'updatevehiclegatewayexit', 'updatepersonalgatewayexit' => 'updatepersonalgatewayexit');
	if (isset($emp_loan_arr[$mode])) {
		include_once("model/employee_loan.user.class.php");
		$elObj = new employeeLoanClass();
		$fun = $emp_loan_arr[$mode];
		$details = $elObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	$vms_document_post_array = array(
		'savedocumentmanagement' => 'saveDocumentManagement',
	);
	if (isset($vms_document_post_array[$mode])) {
		include_once("model/vms_document.user.class.php");
		$vmsDocObj = new VmsDocumentClass();
		$fun = $vms_document_post_array[$mode];
		$details = $vmsDocObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}
	$uniform_arr = array('saveUniformStock' => 'saveUniformStock', 'saveUniformTopup' => 'saveUniformTopup', 'checkUniformItemNameExists' => 'checkUniformItemNameExists', 'saveStationeryRequest' => 'saveStationeryRequest', 'saveMultipleStationeryRequests' => 'saveMultipleStationeryRequests', 'searchUniformAdmin' => 'searchUniformAdmin', 'saveEmpJobGradeAndUniforms' => 'saveEmpJobGradeAndUniforms' );
	if (isset($uniform_arr[$mode])) {
		include_once("model/uniform.user.class.php");
		$elObj = new uniformClass();
		$fun = $uniform_arr[$mode];
		$details = $elObj->$fun($_GET, $_POST);
		echo json_encode($details);
	}

	$atten_arr = array('attendaceswdatainsert' => 'attendaceswdatainsert', 'saveEmployeeBiometricRawData' => 'saveEmployeeBiometricRawData');
    if (isset($atten_arr[$mode])) {
        include_once("model/attendance_user.class.php");
        $attObj = new attendanceUserClass();
        $fun = $atten_arr[$mode];
        $details = $attObj->$fun($_GET, $_POST);
        echo json_encode($details);
    }
    $survey_poll_post_arr = array(
    'savesurveyreponses' => 'savesurveyreponses',
    'createsurveys' => 'createsurveys'
	);
	if (isset($survey_poll_post_arr[$mode])) {
	    include_once("model/survey_poll.user.class.php");
	    $surveyPollObj = new surveyPollUserClass();
	    $fun = $survey_poll_post_arr[$mode];
	    $details = $surveyPollObj->$fun($_POST, $_GET);
	    if ($mode == 'createsurveys') {
	        echo json_encode(10);
	    } else {
	        echo json_encode($details);
	    }
	}
   
    $purchasing_arr = array('insertsubcontractworkorders' => 'insertSubcontractWorkOrders', 'saveissuerawmaterialstovendor' => 'saveIssueRawMaterialsToVendor', 'savesubcontractworkorderreceipt' => 'saveSubcontractWorkOrderReceipt', 'getsubcontractworkorderreceipttaxpreview' => 'getSubcontractWorkOrderReceiptTaxPreview');
    if (isset($purchasing_arr[$mode])) {
        include_once("model/purchasing_user.class.php");
        $purchObj = new purchasingUserClass();
        $fun = $purchasing_arr[$mode];
        $details = $purchObj->$fun($_GET, $_POST);
        echo json_encode($details);
    }
    $api_arr = array('aitoaddcandidatedetails'=>'aitoaddcandidatedetails','startcandidatescreeningquestionsandanswers'=>'startCandidateScreeningQuestionsAndAnswers','endcandidatescreeningquestionsandanswers'=>'endCandidateScreeningQuestionsAndAnswers','uploaddocumentwithai'=>'uploadDocumentWithAI');
    if (isset($api_arr[$mode])) {
        include_once("model/aicall.user.class.php");
        $aiObj = new aicallUserClass();
        $fun = $api_arr[$mode];
        if($fun == 'aitoaddcandidatedetails'){
        	$File_Uploads_Path = "uploads/all/";
			$fileuploadeddetaials = $_FILES['resume'];
			if($fileuploadeddetaials['error'] != "4"){
			    $temp = explode(".", $fileuploadeddetaials['name']);
			    $Filename=  "resume_with_ai_".time()."_".uniqid();
			    $upload_transactions_file = $Filename.'.' .end($temp);
			    $fullPath = SITEROOT.$File_Uploads_Path.$upload_transactions_file;
			   move_uploaded_file($fileuploadeddetaials["tmp_name"], $fullPath);
			}
			$_POST['resume_file'] = $upload_transactions_file;
        }
        if($fun == 'uploadDocumentWithAI'){
        	$folder_name = $_POST['folder_name'];
        	if($folder_name !=""){
	        	$File_Uploads_Path = "uploads/candidate_resume_with_rm/".$folder_name."/";
				$fileuploadeddetaials = $_FILES['resume'];
				if($fileuploadeddetaials['error'] != "4"){
				    $temp = explode(".", $fileuploadeddetaials['name']);
				    $Filename=  "resume_with_ai_".time()."_".uniqid();
				    $upload_transactions_file = $Filename.'.' .end($temp);
				    $fullPath = SITEROOT.$File_Uploads_Path.$upload_transactions_file;
				  	if(move_uploaded_file($fileuploadeddetaials["tmp_name"], $fullPath)){
						$details = array('stause'=>'success','message'=>'Files uploaded successfully.');
				  	}else{
				  		$details = array('stause'=>'success','message'=>'Oops! File upload failed.');
				  	}
				}
			}else{
				$details = array('stause'=>"fail", 'message'=>'Please provide the folder name.');
			}
        }else{
        	$details = $aiObj->$fun($_GET, $_POST);
        }	
        echo json_encode($details);
    }
}else{

	//echo $mode; exit;
	if ($mode == 'login'){
	/*$post['name'] = $_GET['name'];
	$post['password'] = $_GET['password'];*/
	//print_r($_GET); exit;
	//echo $name."/".$password;
	$data = json_decode(urldecode($_GET['data']),1);
	//print_r($data); exit;
	$res =$userObj->checkAdmin($data);
	//print_r($res);
	//return $res;
	echo json_encode($res);
}
if($mode == 'emailconfirm'){
	$id = $_GET['confirm'];
	$res =$userObj->confrimbutton($id);
	echo json_encode($res);
}
if($mode == 'resetpassword'){
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->reSetPassword($data);
	echo json_encode($res);
}
if($mode == 'forgotpassword'){
	//echo "ravi"; exit;
	$email = $_GET['data'];
	$res = $userObj->forgotpassword($email);
	echo json_encode($res);
}
if ($mode == 'logout'){
	$data = $_GET['user_id'];
	//print_r($data);
	$res =$userObj->logout($data);
	echo json_encode($res);
}

if($mode == 'ajaxCheck'){
	$table_name = $_GET['table_name'];
	$filedname = $_GET['filedname'];
	$value = urldecode($_GET['value']);
	$whr = $filedname."='".$value."'";
	$res = $userObj->getrowTablesData($table_name,'*',$whr);
	//print_r($res);
	if(count($res)>0){
		$return = 'Not Available';
	}else{
		$return = 'Available';
	}
	echo $return;

}
if ($mode == 'signup') {
	$data = json_decode($_GET['data'],1);
	//print_r($data); exit;
	$res=$userObj->insertAdminUser($data);
	echo json_encode($res); 
	//echo $data; exit;
}
if ($mode == 'userdetails') {
	$userid = $_GET['userid'];
	$res = $userObj->getAdminUserData($userid);
	echo json_encode($res);
}
if ($mode == 'updateuserdetails') {
	$data = json_decode(urldecode($_GET['data']),1);
	//print_r($data); exit;
	$res = $userObj->updateAdminUser($data);
	echo json_encode($res);
}
if ($mode == 'addrole') {
	$data = json_decode($_GET['data'],1);
	//print_r($data); exit;
	$res = $userObj->addrole($data);
	echo $res;
}
if ($mode == 'roledetails') {
	$res = $userObj->getRoleData();
	//print_r($res);
	echo json_encode($res);
}
if ($mode == 'onerole') {
	$rid = $_GET['rid'];
	$res = $userObj->getoneRoleData($rid);
	echo json_encode($res);
}
if ($mode == 'updaterole') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateRole($data);
	echo $res;
}
if ($mode == 'bill_histories') {
	$res = $userObj->getBillHistoryData();
	echo json_encode($res);
}
if($mode == 'module_histories'){
	$res = $userObj->getModuleHistories();
	echo json_encode($res);
}
if($mode == 'sessions'){
	$res = $userObj->getSessions();
	echo json_encode($res);
}
if($mode == 'revenuesDetaills'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_REVENUES);
	echo json_encode($res);
}
if($mode == 'addcategories'){
	//echo TBL_REVENUES;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertCategories($data);
	echo $res;
}
if ($mode == 'rowcategories') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_CATEGORIES,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatecategories') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updatecategories($data);
	echo $res;
}
if($mode == 'categoriesdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_CATEGORIES);
	echo json_encode($res);
}
if($mode == 'addcompanies'){
	//echo "aaaa";
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertCompanies($data);
	echo $res;
}
if ($mode == 'rowcompanies') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_COMPANIES,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatecompanies') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateCompanies($data);
	echo $res;
}
if($mode == 'companiesdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_COMPANIES);
	echo json_encode($res);
}
if($mode == 'addconfig'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertConfig($data);
	echo $res;
}
if ($mode == 'rowconfig') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_CONFIG,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updateconfig') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateConfig($data);
	echo $res;
}
if($mode == 'configdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_CONFIG);
	echo json_encode($res);
}
if($mode == 'addcurrencies'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertCurrencies($data);
	echo $res;
}
if ($mode == 'rowcurrencies') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_CURRENCIES,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatecurrencies') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateCurrencies($data);
	echo $res;
}
if($mode == 'currenciesdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_CURRENCIES);
	echo json_encode($res);
}
if($mode == 'addcustomers'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertCustomer($data);
	echo $res;
}
if ($mode == 'rowcustomers') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_CUSTOMERS,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatecustomers') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateCustomer($data);
	echo $res;
}
if($mode == 'customersdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_CUSTOMERS);
	echo json_encode($res);
}
if($mode == 'addmediables'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertMediables($data);
	echo $res;
}
if ($mode == 'rowmediables') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_MEDIABLES,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatemediables') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateMediables($data);
	echo $res;
}
if($mode == 'mediablesdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_MEDIABLES);
	echo json_encode($res);
}
if($mode == 'addnotifications'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertNotifications($data);
	echo $res;
}
if ($mode == 'rownotifications') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_NOTIFICATIONS,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updatenotifications') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateNotifications($data);
	echo $res;
}
if($mode == 'notificationsdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_NOTIFICATIONS);
	echo json_encode($res);
}
if($mode == 'additems'){
	//echo "aaaa"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->insertItems($data);
	echo $res;
}
if ($mode == 'rowitems') {
	$rid = $_GET['rid'];
	$whr = "id=".$rid;
	$res = $userObj->getrowTablesData(TPREFIX.TBL_ITEMS,'*',$whr);
	echo json_encode($res);
}
if ($mode == 'updateitems') {
	//echo "ravi"; exit;
	$data = json_decode($_GET['data'],1);
	$res = $userObj->updateItems($data);
	echo $res;
}
if($mode == 'itemsdetails'){
	//echo TBL_REVENUES;
	$res = $userObj->getAllTablesData(TPREFIX.TBL_ITEMS);
	echo json_encode($res);
}
if($mode == 'displayattributes'){
	//echo "data"; exit;
	$page_name = $_GET['page_name'];
	$whr = "page_name='".$page_name."'";
	//echo $whr;
	//$whr = "id=2";
	$res = $userObj->getrowTablesData(TPREFIX.TBL_DISPLAYATTRIBUTES,'*',$whr);
	//print_r($res);
	echo json_encode($res);
	//echo "1";	
}
if($mode == 'displayattributesview'){
	$view = $_GET['view'];
	$id = $_GET['fid']!="" ? $_GET['fid'] : "view";
	if($_GET['display_list_type'] != ""){
		$condition = "AND display_list_type = '".$_GET['display_list_type']."'";
	}
	$whr = $id."='".$view."'".$condition;
	$res = $userObj->getrowTablesData(TBL_DISPLAYATTRIBUTES,'*',$whr);
	echo json_encode($res);	
}
if($mode == 'rolesdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_ROLES);
	echo json_encode($res);	
}
if($mode == 'recurringdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_RECURRING);
	echo json_encode($res);	
}
if($mode == 'permissionsdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_PERMISSIONS);
	echo json_encode($res);	
}
if($mode == 'sitesettingsdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_SITESETTINGS);
	echo json_encode($res);	
}
if($mode == 'paymentdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_PAYMENTS);
	echo json_encode($res);	
}
if($mode == 'transfersdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_TRANSFERS);
	echo json_encode($res);	
}
if($mode == 'vendorsdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_VENDORS);
	echo json_encode($res);	
}
if($mode == 'taxdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_TAXVW);
	echo json_encode($res);	
}
if($mode == 'useretails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_USERVW);
	echo json_encode($res);	
}
if($mode == 'billitemdetails'){
	$res = $userObj->getAllTablesData(TPREFIX.TBL_BILLITEMVW);
	echo json_encode($res);	
}
if($mode == 'adddata'){
	//echo "ha"; //exit;
	$decode = urldecode($_GET['data']);
	//echo $decode;
	$data = json_decode($decode,1);
	//print_r($data); echo "faf"; //exit;
	$res = $userObj->insertTablesdata($data, $_GET['table']);
	if($_GET['table'] == 'isac_items'){
			//echo "gga"; exit;
		$data1['item_id'] = $res;
		$res = $userObj->updateTablesdata($data1,TBL_ITEM_SPECIFICATIONS,'sku',$data['sku']);
	}
	echo $res;
}
if($mode == 'addemail'){
	//echo "haooooo"; exit;
	$post = json_decode($_GET['data'],1);
	$data['name'] = $post['name'];
	$data['email'] = $post['email'];
	$data['is_active'] = $post['is_active'];
	$res = $userObj->insertTablesdata($data, $_GET['table']);
	$egdata['email_id'] = $res;
	$egdata['email_groups_id'] = $post['email_group'];
	$res = $userObj->insertTablesdata($egdata,TPREFIX.TBL_EMAILGROUPSEMAIL);
	echo $res;
}
if($mode == 'gettabledetails'){
	$whr ='';
	$field = '*';
	if($_GET['fid'] !='' && $_GET['rid'] !=''){
		
		$rid = $_GET['rid'] !='' ? $_GET['rid'] : '';
		$fid = $_GET['fid'] !='' ? $_GET['fid'] : '';
		$whr = $fid."='".$rid."'";
	}
	if($_GET['orderby'] !=''){
		$orderby = urldecode($_GET['orderby']);
	}
	if($_GET['field'] !='' && $_GET['field'] !=''){
		
		$field = $_GET['field'] !='' ? $_GET['field'] : '*';
		
	}
	//echo "hai".$orderby;
	$res = $userObj->getAllTablesData($_GET['table'],'*',$whr,$orderby);
	//print_r($res);
	echo json_encode($res);	
}
if ($mode == 'singlerowtabledata') {
	$rid = $_GET['rid'];
	$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
	$whr = $fid."='".$rid."'";
	$res = $userObj->getrowTablesData($_GET['table'],'*',$whr);
	//print_r($res);exit;
	echo json_encode($res);
}

if ($mode == 'updaterowtabledata') {
	//echo "sada"; exit;
	$rid = $_GET['rid'];
	$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
	$data = json_decode(urldecode($_GET['data']),1);
	//echo "ads".$fid; exit;
	$res = $userObj->updateTablesdata($data,$_GET['table'],$fid,$rid);
	echo json_encode($res);
}
if ($mode == 'addemailgroupemail') {
	$data = json_decode($_GET['data'],1);
	//print_r($data); exit;
	$insert_data['email_groups_id'] =  $data['email_groups_id'];
	$emails = explode(",",$data['email']);
	foreach ($emails as $email) {
		$insert_data['email_id'] = $email;
		//print_r($insert_data); exit;
		$res = $userObj->insertTablesdata($insert_data, TPREFIX.TBL_EMAILGROUPSEMAIL);
	}
	echo $res;
}
if ($mode == 'addbill') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	$data['company_id'] =3;
	$res = $userObj->insertBillTables($data);
	echo $res;
}
if ($mode == 'addestimates') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	/*$data['company_id'] =3;*/
	$res = $userObj->insertEstimatesTables($data);
	echo $res;
}
if ($mode == 'adddebitnote') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	/*$data['company_id'] =3;*/
	$res = $userObj->insertdebitnoteTables($data);
	echo $res;
} 
if ($mode == 'addcreaditnote') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	/*$data['company_id'] =3;*/
	$res = $userObj->insertcreaditnoteTables($data);
	echo $res;
}
if ($mode == 'adddquotation') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	/*$data['company_id'] =3;*/
	$res = $userObj->insertquotationTables($data);
	echo $res;
} 
/*if($mode == 'billdetails'){
	$res = biillData(2);
	echo $res;
}*/
if ($mode == 'showtables') {
	$data = json_decode($_GET['data'],1);
	//print_r($data);
	$data['company_id'] =3;
	$res = $userObj->listofTables($_GET['data_name']);
	echo json_encode($res);
}
if ($mode == 'listofTableFieldsonly') {
	$res = $userObj->listofTableFieldsonly($_GET['table']);
	echo json_encode($res);
}
if ($mode == 'showtableFields') {
	$data = json_decode($_GET['data'],1);
	$res = $userObj->listofTableFields();
	echo json_encode($res);

}
if($mode =="displayattribute"){
	$table = $_GET['dis_table'];
	$rid = $_GET['rid'];
	if($table !=''){
		$whr = "application_table_name='".$table."' AND company_id='".$_GET['company_id']."' AND display_list_type ='".$_GET['display_list']."'";
	}else {
		$whr = "id='".$rid."'";
	}
	//echo $whr;exit;
	$res = $userObj->getrowTablesData(TBL_DISPLAYATTRIBUTES,'*',$whr);
	//print_r($res); exit;
	$attribute = json_decode($res->list_attributes,1);
	//print_r($attribute); exit;
	if($table ==''){
		$table = $res->application_table_name;
	}
	$displayattribute = $userObj->listofTableFields($table,$attribute);
	//print_r($displayattribute);
	if(isset($res->list_attributes)){
		//echo "test1";
		$res->list_attributes = $displayattribute;
	}else{
		//echo "test2";
		$res['list_attributes'] = $displayattribute;
	}
	//$res->list_attributes = $displayattribute;
	echo json_encode($res);

}

if($mode == "getspcificattributes"){
	//echo "hai";
	$tablename = $_GET['table'];
	$rid = explode(",", $_GET['rid']);
	$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
	//print_r($rid);
	$res = $userObj->getINqueryData($tablename,'',$fid,$rid);
	echo json_encode($res);
}
if($mode == 'statusdata'){
	$tablename = $_GET['table'];
	$rid = $_GET['rid'];
	$fid = $_GET['fid'] !='' ? $_GET['fid'] : 'id';
	$whr = $fid."='".$rid."'";
	$res = $userObj->getrowTablesData($_GET['table'],'*',$whr);
	$status = json_decode($res->status,1);
	$status = implode(",", $status);
	$result =  $userObj->getStatusSeq($status);
	//print_r($result);
	echo json_encode($result);
}
if($mode == 'selectgroup'){
	$tablename = $_GET['table'];
	$reslut = $userObj->getAllTablesData($_GET['table']);
	foreach($reslut as $resl){
		$res[$resl->Parent_Account_Code][$resl->Account_Code]= $resl->Account_Name;
	}
	//print_r($res);
	echo json_encode($res);
}
if($mode == 'invoicenumber'){
	$result =  $userObj->maacInvoicenumber($_GET['company_id']);
	echo $result->number+1;
	//SELECT max(`order_number`) as number FROM `isac_invoices` where `company_id` =1
}
if($mode == 'changepassword'){
	$data = json_decode($_GET['data'],1);
	//print_r($data); exit;
	//$whr = "='".$rid."'and";
	//$res = $userObj->getrowTablesData($_GET['table'],'*',$whr);
	$result =  $userObj->changepassword($data);
	echo $result;
}
if($mode == 'dyanamicselectdata'){
//TBL_DISPLAYATTRIBUTES_TYPE
	//echo "hai";; exit;
	$result = $userObj->getDyanamicSelectData($_GET['table_name'], $_GET['filed_namee']);
	//print_r($result);
	echo json_encode($result); exit;
}
if($mode == 'dyanamicautoselectdata'){
	$result = $userObj->getDyanamicAutoSelectData($_GET['table_name'], $_GET['filed_namee'],$_GET['words']);
	echo json_encode($result); exit;
}
if($mode == 'dyanamicselectreportdata'){
	$result = $userObj->getDyanamicReportSelectData($_GET['report_id'], $_GET['filed_name']);
	//print_r($result);
	echo json_encode($result); exit;
}
if($mode == 'dyanamicafterdata'){
//TBL_DISPLAYATTRIBUTES_TYPE
	//echo "hai";; exit;
	$result = $userObj->getDyanamicafterData($_GET['table_name'], $_GET['filed_namee']);
	//print_r($result);
	echo json_encode($result); exit;
}
if($mode == 'filednames'){
	$result = $userObj->listofTableFieldsByUsedTable($_GET['table'],$_GET['data_name']);

	echo json_encode($result);
}

if($mode == 'notificationinsert'){
	$post = json_decode(urldecode($_GET['data']),1);
	//print_r($post); exit;
	$data['Notification_Message'] = $post['notification_message'];
	$data['Notification_details'] = json_encode($post);
	//print_r($data); exit;
	$notification_id = $userObj->insertTablesdata($data, TBL_NOTIFICATIONS);
	//echo $notification_id;
	$data1['Notification_id'] = $notification_id;
	$data1['Notification_Type'] = 'Action';
	$users =  explode(',', $post['notification_users']);
	foreach ($users as $key => $value) {
		$data1['User_id'] = $value;	
		//print_r($data1); exit;
		$res = $userObj->insertTablesdata($data1, TBL_USER_NOTIFICATON);
	}
	echo $notification_id;
}
if($mode == 'toGetNotigficationListByUsingUserId'){
	$user_id = $_GET['user_id'];
	//echo $user_id; exit;
	$notificationdetails = $userObj->toGetNotigficationList($user_id,$_GET['type'],$_GET);
	echo json_encode($notificationdetails);
}
if($mode == 'displayrolepermission'){
	$role_id = $_GET['rid'];
	//echo $role_id; exit;
	$rolepermission = $userObj->displayrolepermission($role_id);
	echo json_encode($rolepermission);
	
}
if($mode == 'toGetRolePermissions'){
	$table = $_GET['dis_table'];
	$tablerole = $userObj->toGetRolePermissions($table);
	echo json_encode($tablerole);
}
if($mode == 'getrolepermission'){
	$user_id = $_GET['user_id'];
	$rolepermission = $userObj->toGetRoledata($user_id,$_GET);
	echo json_encode($rolepermission);
}
if($mode == 'checkinvoicedetails'){
	$customer_id = $_GET['customer_id'];
	$details = $userObj->toGetblanceInvoices($customer_id);
	//print_r($details);
	echo json_encode($details);
}
// if($mode == 'checkbilldetails'){
// 	$customer_id = $_GET['customer_id'];
// 	$details = $userObj->toGetblancebill($customer_id,$get);
// 	//print_r($details);
// 	echo json_encode($details);
// }
if($mode == 'receiptinsert'){
	$data = json_decode(urldecode($_GET['data']),1);
	$details = $userObj->toInsertandupateinvoice($data);
	//print_r($details);
	echo json_encode($details);
}
if($mode == 'getemailgroupdetails'){
	$details = $userObj->getemailgroupdetails($data);
	echo json_encode($details);
}
if($mode == 'getgroupemaildetails'){
	$details = $userObj->getgroupemaildetails($_GET['group_id']);
	echo json_encode($details);
}
if($mode == 'checkAndInsertapplicationpages'){
	//echo $_GET['table'];
	$details = $userObj->checkAndInsertapplicationpage($_GET['table'], $_GET['company_id']);
	echo json_encode($details);
}
if($mode == 'viewdattributetype'){
	$details = $userObj->viewdattributetype($_GET['table'],$_GET['field'],$_GET['data_name']);
	echo json_encode($details);
}
if($mode == 'togetprimarykey'){
	$details = $userObj->togetprimarykey($_GET['table'], $_GET['data_name']);
	echo json_encode($details);
}
if($mode == 'togetdatabasepaimairy'){
	$details = $userObj->togetdatabasepaimairy($_GET['table'], $_GET['data_name'],$_GET['rid']);
	echo json_encode($details);
}
if($mode == "togetmastertablesdetais"){
	$details = $userObj->togetmastertablesdetais();
	echo json_encode($details);
}
if($mode == "morefilednames"){
	$table = json_decode(json_decode($_GET['table'],1));
	$details = $userObj->morefilednames($table,$_GET['data_name']);
	echo json_encode($details);
}
if($mode == "togetreportdata"){
	//echo $_GET['data'];
	$data = json_decode(urldecode($_GET['data']),1);
	//print_r($data);
	$details = $userObj->togetreportdata($data);
	echo json_encode($details);

}
if($mode == 'gettabledetailswithconditions'){
   // echo "hai";//exit;
	$data = urldecode($_GET['data']);
	$data = json_decode($data,1);
	//print_r($data); exit;
	$whr = $data['where'];
	$whr_in = $data['where_in'];
	$whr_in_key = $data['whr_in_key'];
	$where = '';	$fieldnames = '*'; $order = ''; $limit1=''; $limit2='';
	if($whr !=''){
		foreach ($whr as $key => $value) {
			$where.= $key." = '".$value."' AND ";		
		}

		$where = trim($where,'AND ');
		//echo $where;
		//$where = implode(' AND ', $where);
	}
	if($whr_in_key !=''){
		
		$whr_in_con = " AND ".$whr_in_key." IN (".implode(',',$whr_in).")";
		$where.= $whr_in_con;
		//echo $where;
		//$where = implode(' AND ', $where);
	}
	//echo $where; exit;
	if($data['field'] !=''){
		$fieldnames =$data['field'];
	}
	if($data['order'] !=''){
		$order = $data['order'];
	}
	if($data['limit'] !=''){
		$limit = $data['limit'];
		$limit = explode(",", $limit);
		$limit1 = $limit[0];
		$limit2 = $limit[1];
	}
	//echo "hai".$order;
	$details = $userObj->getAllTablesData($_GET['table'],$fieldnames,$where,$order,$limit1,$limit2);
	//echo "<pre>"; print_r($details); exit;
	if($_GET['table'] == 'currencies'){
		$i = 0;
		foreach($details as $dd){
			$led[$i]['id']= $dd->id;
    	    //$led[$i]['currency_list']= base64_encode($dd->currency_list);
			$led[$i]['currency_name']= $dd->currency_short_code;
			$led[$i]['currency_short_code']= $dd->currency_short_code;
			
			$i++;}
			$details = $led;
		}
	//	echo "<pre>"; print_r($details);
		echo json_encode($details);	exit;
	}
	if($mode == 'getPurchaseRequisitionsData'){
		$pr_id = $_GET['pr_id'];
		$details = $userObj->getPurchaseRequisitionsData($pr_id);
		echo json_encode($details);

	}
	if($mode == 'getInvoicesData'){
		$in_id = $_GET['in_id'];
		$table = $_GET['table'];
		$details = $userObj->getInvoicesData($in_id,$table, $_GET);
		echo json_encode($details);

	}
	if($mode == 'toGetStatusProgressActionList'){
		$status = $_GET['status'];
		$table = $_GET['table'];
		$details = $userObj->toGetStatusProgressActionList($table, $status);
		echo json_encode($details);

	}
	if($mode == 'toGetPendingRequisitionQty'){
		$details = $userObj->toGetPendingRequisitionQty($_GET);
		echo json_encode($details);
	}
	if($mode == 'toupdatestationerydetails'){
		$transaction_id = $_GET['transaction_id'];
		$details = $userObj->toupdatestationerydetails($transaction_id,$_GET);
		echo json_encode($details);
	}
	if($mode == 'tocheckDAO'){
		$data['transaction_type']= $_GET['table'];
		$data['total_amount'] = $_GET['amount'];
		$data['status'] = $_GET['status'];
		$data['status_old_value'] = $_GET['status_old_value'];
		$data['opex_cpex'] = $_GET['opex_cpex'];
		$data['category_type'] = $_GET['category_type'];
		$data['cost_center'] = $_GET['cost_center'];
		$details = $userObj->tocheckDAOValues($data);
		echo json_encode($details);
	}
	if($mode == 'getplaceofsuppl'){
		$details = $userObj->getplaceofsupply($_GET['company_id']);
		echo json_encode($details);
	}
	if($mode == 'notification_read'){
		$details = $userObj->notificationRead($_GET['id'], $_GET['user_id']);
		echo json_encode($details);
	}
	if($mode == 'togetpcdata'){
    //echo "Hello Test";
		$details = $userObj->togetpcdata($_GET['company_id'], $_GET['user_id']);
		echo json_encode($details);
	}
	if($mode == 'pettycashbranchamount'){
		$data = json_decode($_GET['data'],1);
	//print_r($data);
		$details = $userObj->pettycashbranchamount($data);
		echo json_encode($details);
	}
	if($mode == 'togetlatestannouncement'){
		$details = $userObj->getrowTablesData('company_announcements', '*' , "current_status = 0", 'id DESC', '0', '1');
		echo json_encode($details);
	}

	if($mode == 'tocreatejounalvoucherbaseallcases'){
		//echo "----------------"; exit;
		$details = $userObj->tocreatejounalvoucherbaseallcases($_GET['table_name'],$_GET['id']);
		echo json_encode($details);
	}

	if($mode == 'insertpaymentstojv'){
		//echo "----------------"; exit;
		$details = $userObj->insertpaymentstojv($_GET['transaction_id'],$_GET);
		echo json_encode($details);
	}
	if($mode == 'dyanamicserialnumber'){
		$details = $userObj->toGetApplicationSerialNumber($_GET['table'],$_GET); 
		echo json_encode($details);	
	}
	if($mode == "infisuitejobcandedatesave"){
		if($_POST['extid']!=""){
			$res = $intiObj->infisuitejobcandedatesave($_GET,$_GET);
			echo json_encode($res); exit;
		}
	}
	if($mode=='sendmailwithsmtp'){ 
		//echo "-------------------"; exit;
		$details = $userObj->sendmailwithsmtp('shilpa.duttapattanayak@microchip.com', 'Test mail', 'body', '',''); 
		echo json_encode($details);	
	}
	if($mode == 'updatetransactiontcs'){
		$details = $userObj->updateTransactionTCS($_GET['transaction_table'],$_GET['transaction_id'],$_GET);
		echo json_encode($details);	
	}
	$vms_get_class_array =  array('toGetEmployessWorkorderswthTimesheet'=>'toGetEmployessWorkorderswthTimesheet', 'togetverticaldata'=>'togetverticaldata', 'togetcoscentergraphdata'=>'togetcoscentergraphdata', 'togetvendorgraphdata'=>'togetvendorgraphdata', 'togetteammeberattdences'=>'togetteammeberattdences', 'togetteammeberemployeeids'=>'togetteammeberemployeeids', 'togetvendorwiseprojecttimesheetdata'=>'togetvendorwiseprojecttimesheetdata', 'customadmdashboardmodule3'=>'customadmdashboardmodule3', 'bugetvendorrareports'=>'bugetvendorrareports', 'toGetblukProjectTimesheet'=>'toGetblukProjectTimesheet', 'toGetblukProjectTimesheetlastmonths'=> 'toGetblukProjectTimesheetlastmonths', 'toGetblukHrProjectTimesheet'=>'toGetblukHrProjectTimesheet', 'toGetblukFinaceProjectTimesheet'=>'toGetblukFinaceProjectTimesheet', 'toGetblukHrProjectTimesheetlastmonths'=>'toGetblukHrProjectTimesheetlastmonths', 'togetteamcount'=>'togetteamcount', 'togetteamlisttohr'=>'togetteamlisttohr', 'gettimesheetsummary'=>'gettimesheetsummary','getempconnect'=>'getempconnect', 'emplist'=>'empList', 'getbugetamountesignvendorwise'=>'getbugetamountesignvendorwise','deletetimesheets'=>'deleteTimeSheets','togetemployeesesignvenderratecarddetail'=>'togetemployeesesignvenderratecarddetail', 'getexpireagreementslist'=>'getExpireAgreementsList','timesheethrsdashboard'=>'timeSheetHrsDashboard','nextmonthprojection'=>'nextMonthProjection','togetvendoremployeedetails'=>'togetvendoremployeedetails','gettimesheetworkentryedit'=>'getTimeSheetWorkEntryWdit','esigncunsumebugetchage'=>'esigncunsumebugetchage', 'gettimesheetentries'=>'gettimesheetentries','toretriveminhorsandweksanddayebasedondates'=>'toretriveminhorsandweksanddayebasedondates', 'togetesignvendororderdata'=>'togetesignvendororderdata','getprojecttimesheetworkorderid'=>'getprojecttimesheetworkorderid', 'togetempvmstransactionslist'=>'togetempvmstransactionslist','getcheckexpirydetails'=>'getcheckexpirydetails','yearlyprojecttimesheetreports'=>'yearlyprojecttimesheetreports','gettimesheetsubmitpending'=>'getTimesheetSubmitPending','gettimesheetsholidaysleaveoff'=>'getTimesheetsHolidaysLeaveOff','getapprovalmanagers'=>'getapprovalmanagers','getweecklytimesheetsummary'=>'getWeecklyTimeSheetSummary','getweecklybulkapprovaltimesheet'=>'getWeecklyBulkApprovalTimesheet','changestatusweeklybulkapproval'=>'changestatusweeklybulkapproval','updateprojecttimesheetsblukforhrweekly'=>'updateprojecttimesheetsblukforhrweekly', 'togetvmsemployeedetails'=>'togetvmsemployeedetails','togetesignpolistpopup'=>'togetesignpolistpopup','getvendorratecardidordetailsid'=>'getvendorratecardidordetailsid','getweecklystatustimesheetsummary'=>'getWeecklyStatusTimeSheetSummary','checkvalll'=>'checkvalll','togetananthdownloadreport'=>'togetananthdownloadreport','admindashboarddetails'=>'admindashboarddetails','togettransactionlogtalbe'=>'togettransactionlogtalbe','getcountpendingtimesheet'=>'getcountpendingtimesheet','bugetvendorrareport'=>'bugetvendorrareport', 'tocalworkingdays'=>'tocalworkingdays','getvendors'=>'getvendors', 'getvendorfeedbackdata'=>'getvendorfeedbackdata', 'getvendorfeedbackresponsedata'=>'getvendorfeedbackresponsedata', 'getvendorfeedbackeditdetails'=>'getvendorfeedbackeditdetails','togetempvmsdetails'=>'togetempvmsdetails','togetvendorfeedbackpopdetails'=>'togetvendorfeedbackpopdetails','vendorreviewalldetails'=>'vendorreviewalldetails','CreateNewversionvendorsetup'=>'CreateNewversionvendorsetup','togetvendorfeedbacksetupdetails'=>'togetvendorfeedbacksetupdetails', 'getmonthlytimesheetentries'=>'getmonthlytimesheetentries', 'replacevendor'=>'replacevendor',  'addupdateempassignmilestone'=>'addupdateempassignmilestone','admindashboarddetails_newdashboard'=>'admindashboarddetails_newdashboard','vendorstaqadmindashboard' => 'vendorstaqAdminDashboard', 'vendorlistforadminmanag'=>'vendorlistforadminmanag', 'togetretrivemanageremployeeleaves'=>'togetretrivemanageremployeeleaves', 'togetretrivemanageremployetermination'=>'togetretrivemanageremployetermination', 'vmssingleemployeedashoard_new'=>'vmssingleemployeedashoard_new', 'togetemployeemonthattendancedetails'=>'togetemployeemonthattendancedetails', 'togetemployeelistleaedata'=>'togetemployeelistleaedata','geteexitcontrantractoremployeedata' => 'geteexitcontrantractoremployeedata',
	 'getworkflowassigndata' => 'getworkflowassigndata', 
	 'getexitemployeeapprovalpopupdata' => 'getexitemployeeapprovalpopupdata',
	 'getidcndcexitformdata' => 'getidcndcexitformdata', 
	 'togetaccourreportquatrly'=>'togetaccourreportquatrly',
	 'getQuarterlyAccrualRevenue' => 'getQuarterlyAccrualRevenue',
	 'getQuarterlyAccrualRevenueById' => 'getQuarterlyAccrualRevenueById',
	 'togetlastvscurrentquarterlyaccrual' => 'togetlastvscurrentquarterlyaccrual', 
	 'getApprovedTimesheetEditData' => 'getApprovedTimesheetEditData',
	 'getManagerTeamLeaves' => 'getManagerTeamLeaves','getweeklytimesheetentries'=>'getweeklytimesheetentries');
	if(isset($vms_get_class_array[$mode])){
		$fun = $vms_get_class_array[$mode];
		$details = $vmsObj->$fun($_GET);
		echo json_encode($details);
	}

	$check_array = array('projecttaskchart'=>'projectTaskChart','updateprojecttaskstatuse'=>'updateProjectTaskStatuse','getprojecttaskemplist'=>'getProjectTaskEmpList','getlistcreatdocumentsfields'=>'getListCreatDocumentsFields','gettablerow'=>'getListTableRow','getprintdesigning'=>'getPrintDesigning','gettableattributes'=>'getTableAttributes','getlisttable'=>'getListTable','getattribute'=>'getAttribute','fileuploadingdelete'=>'fileUploadingDelete','filleuploadinglist'=>'filleUploadingList','getjobpostingsreport'=>'getjobpostingsreport','getempreport'=>'getempreport','getemprequests'=>'getemprequests','togetVendorAgreementexpirelist'=>'togetVendorAgreementexpirelist','getpeerfeedbackquestionsandoptions'=>'getpeerfeedbackquestionsandoptions','getperformanceemployees'=>'getperformanceemployees','getempperformancepeerfeedback'=>'getempperformancepeerfeedback','getperformanceassessment'=>'getperformanceassessment','employeedashboard'=>'employeedashboard','togetepfecrsummary'=>'togetepfecrsummary','togetesisummary'=>'togetesisummary','togetepfsummary'=>'togetepfsummary','togetpayslipctcreports'=>'togetpayslipctcreports','togetstateinsurance'=>'togetstateinsurance','togetpercentofempprovidentfunds'=>'togetpercentofempprovidentfunds','togetsumofempsalaryoncomponentidbtwdates'=>'togetsumofempsalaryoncomponentidbtwdates','togetsumofempsalaryoncomponentid'=>'togetsumofempsalaryoncomponentid','togetsalarytotaldetails'=>'togetsalarytotaldetails','getcandidatededata'=>'getcandidatededata','gettimesheedays'=>'gettimesheedays','getjobsreport'=>'getjobsreport','getempassessment'=>'getempassessment','getassessmentreport'=>'getassessmentreport','getassessmentresult'=>'getassessmentresult','getassessmentlist'=>'getassessmentlist','getassessmentsummary'=>'getassessmentsummary','getsharedassessmentdetails'=>'getsharedassessmentdetails','getassessmentdetails'=>'getassessmentdetails','getemployeeworkordertimesheethorus'=>'getemployeeworkordertimesheethorus','getbudgetlist'=>'getbudgetlist','getbudgetfilterdetails'=>'getbudgetfilterdetails','getvmsbudgetutilization'=>'getvmsbudgetutilization','gettimesheetscalendar'=>'gettimesheetscalendar','togetjobdetails'=>'togetjobdetails','getcontractemployees'=>'getcontractemployees','getvendorwisereport'=>'getvendorwisereport','NewUserEmailresent'=>'NewUserEmailresent','getprojecttimesheets'=>'getprojecttimesheets','togetprojectdetails'=>'togetprojectdetails','toGetVendorDashBoardData'=>'toGetVendorDashBoardData','employeeshift'=>'employeeshift','toGetInboxCountUsingUserId'=>'toGetInboxCountUsingUserId','getfilteremployeenames'=>'getfilteremployeenames','updatebulktransferemployees'=>'updatebulktransferemployees','getbulkchangecategories'=>'getbulkchangecategories','toGetTransactionLogdata'=>'toGetTransactionLogdata','getrecognitionsettings'=>'getrecognitionsettings','getfilterdetails'=>'getfilterdetails','getheadcountreport'=>'getheadcountreport','wrokflowlist'=>'wrokflowlist','getterminatesettings'=>'getterminatesettings','getannouncements'=>'getannouncements','getvendorsettings'=>'getvendorsettings','tosearchemployee'=>'tosearchemployee','tosearchempleavesbydate'=>'tosearchempleavesbydate','togetleaveform'=>'togetleaveform','getconfigsettings'=>'getconfigsettings','togetallpayslipdetails'=>'togetallpayslipdetails','toimportthedataintotables'=>'toimportthedataintotables','getsalarygroupsitreportdata'=>'getsalaryreportinggroupsdata','togetnoteslist'=>'togetnoteslist','getemployeeinfoforupdation'=>'getemployeeinfoforupdation','tasksendmail'=>'tasksendmail','gettasksdetails'=>'gettasksdetails','singlestatusdata'=>'singlestatusdata','payrolldetails'=>'payrolldetails','togetkpitaskstatus'=>'togetkpitaskstatus','togetkpitaskhistories'=>'togetkpitaskhistories','togetemployeeslist'=>'togetemployeeslist','togetempleavebalances'=>'togetempleavebalances','togetpresentationpagedetails'=>'togetpresentationpagedetails','togetpresentationdetails'=>'togetpresentationdetails','togetemployeeattendanacecalucater'=>'togetemployeeattendanacecalucater','togetcurrentmonthsalaryrevisions'=>'togetcurrentmonthsalaryrevisions','togetcurrentmonthexitemployes'=>'togetcurrentmonthexitemployes','togetpayslipdetails'=>'togetpayslipdetails','togetpayrolledetails'=>'togetpayrolledetails','updateonlystatus'=>'updateonlystatus','employeegoalscheckandadd'=>'employeegoalscheckandadd','goalscheckandadd'=>'goalscheckandadd','togetsalarycatdetaillist'=>'togetsalarycatdetaillist','togetcandidatedocumentlist'=>'togetcandidatedocumentlist','gettabledetailswithtransationlogconditions'=>'employeeDetails','employeedetails'=>'employeeDetails','toGetItemcostingtyewithdata'=>'toGetItemcostingtyewithdata','togetstatusprogressactiondetails'=>'togetstatusprogressactiondetails','togetgenralledgerlistdata'=>'togetgenralledgerlistdata','pendingquerieslist'=>'pendingQuerieslist','togetbudgetuserdeails'=>'togetbudgetuserdeails','togetstatusprogresstables'=>'togetstatusprogresstables','dasboaradrequisition1coscenterdetails'=>'dasboaradrequisition1coscenterdetails','dasboaradrequisition1details'=>'dasboaradrequisition1details','montlypettycashdetails'=>'montlypettycashdetails','dailypettycashdaydetails'=>'dailypettycashdaydetails','dailypettycashmonth'=>'dailypettycashmonth','dailypettycashdetails'=>'dailypettycashformonth','togetuserpettycashboxdetails'=>'togetuserpettycashboxdetails','toGetTaxrates'=>'toGetTaxrates','togetpettycashsummary'=>'toGetPettyCashSummary','togetbluckstatusdata'=>'toGetBluckStatusData','toGetBluckApprovaldata'=>'toGetBluckApprovaldata','pettycashdetails'=>'pettycashdetails','getchartofaccountdetailsuseglaccountuser'=>'getchartofaccountdetailsuseglaccountuser','getapprovallogdata'=>'getApprovalLogData','getmyreportlist'=>'getMyReportList','deletemyreport'=>'deleteMyReport','getchartjobreport'=>'getChartJobReport','getlistchartjobreport'=>'getListChartJobReport','getheadcountcurrentreport'=>'getHeadCountCurrentReport','getlistheadcountcurrentreport'=>'getListHeadCountCurrentReport','getchartcandidatesreport'=>'getChartCandidatesReport','getlistchartcandidatesreport'=>'getListchartcandidatesreport','tocheckesginemployee'=>'tocheckesginemployee','blukapprovalvendorchart'=>'blukapprovalvendorchart','processtaskschecduleview'=>'processTaskSchecduleView','processtaskschecduleoverview'=>'processTaskSchecduleOverView','getlistjobpostingslist'=>'getListJobPostingsList','getjoboverview'=>'getJobOverView','updatecandidateshortlist'=>'updateCandidateShortList','getcandidatefeedbackresult'=>'getcandidatefeedbackresult','candidateactivityreports'=>'candidateActivityReports','togetfeedbackcandidate'=>'togetfeedbackcandidate','getlist'=>'getList','applicationprocesslist'=>'applicationProcessList','applicationprocesspages'=>'applicationProcessPages','applictionprocelist'=>'applictionProceList','updateapplicationprocesspages'=>'updateApplicationProcessPages','userlistwithrole'=>'userlistwithrole','getfeedbackcandidatereport'=>'getfeedbackcandidatereport','getriviewsdata'=>'getRiviewsData','getcitydatasautotype'=>'getcitydatasautotype','gettrackingdynamic'=>'gettrackingdynamic','getfamilytreelist'=>'getFamilyTreeList','getemployeetreelist'=>'getEmployeeTreeList','getchartofaccountreelist'=>'getCharTofAccounTreeList','myreportviewsql'=>'myreportViewSql','reportfilterselectdata'=>'reportFilterSelectData','checkcriteria'=>'checkCriteria','checkwhoshouldapproves'=>'checkWhoShouldApproves','customgetstatusworkflow'=>'customGetStatusWorkFlow','updatedynamicapprovallognew'=>'updateDynamicApprovalLogNew','testingfunction'=>'testingfunction','checkemployeeleaveavailableornot'=>'checkEmployeeLeaveAvailableOrNot','cronjobleave'=>'cronJobLeave','checkleaveapplicable'=>'checkLeaveApplicable','checkleavebalanceorhalfday'=>'checkLeaveBalanceOrHalfDay','tocerateapplicationuser'=>'tocerateapplicationuser','getemployeeworkingentrys'=>'getEmployeeWorkingEntrys','edittimesheetbillablehrs'=>'editTimeSheetBillableHrs','tocreatemutltiusercreate'=>'tocreatemutltiusercreate','getmonthlyctcreports'=>'getMonthlyCTCReports','insertcheckpayroll'=>'insertCheckPayRoll','dyanamicreferfunc'=>'dyanamicReferFunc', 'closenotificationinfo'=>'closeNotificationInfo', 'tocerateapplicationuserbasedonalltype'=>'tocerateapplicationuserbasedonalltype','togetptanualstatment'=>'togetPtAnualStatment','getemployeectcreports'=>'getEmployeeCTCReports','closenotificationinfo'=>'closeNotificationInfo','bugetvendorrareporttest'=>'bugetvendorrareporttest','updateticketdetails'=>'updateTicketDetails','getviewticketdetails'=>'getViewTicketDetails','getheadcountreportemployefilter'=>'getheadcountreportEmployeFilter11','getlitsattendancesapi'=>'getLitsAttendancesApi','getleavewithstatus'=>'getLeaveWithStatus','togetpayrollpayarea'=>'togetpayrollpayarea','payareapayrolldetails'=>'payareapayrolldetails','listoutpayareapayrolldetails'=>'listoutpayareapayrolldetails','getgrosssalarywithregim'=>'getgrosssalarywithregim','getmyreportfields'=>'getMyReportfields','getadmindashboard'=>'getAdminDashboard','gettablefilter'=>'getTableFilter','getemployeeexpencesdetails'=>'getEmployeeExpencesDetails','getexpensespoliciesdetails'=>'getExpensesPoliciesDetails','assignemployeelistpolicy'=>'assignEmployeeListPolicy','getuserinvitations'=>'getUserInvitations','updatestatusvendordocuments'=>'updatestatusvendordocuments','reaprovevendordoc'=>'reAproVevendorDoc','getstocktakes'=>'getStockTakes','getstocktakesuom'=>'getstocktakesuom','getstocktakeadjustments'=>'getStockTakeAdjustments','startstocktakes'=>'startstocktakes','getstocktakelocations'=>'getStockTakeLocations','softdeletedrecord'=>'softDeletedRecord','getjobpostings'=>'getJobPostings','getassetallocationvms'=>'getAssetAllocationVms','getcompliancedetails'=>'getComplianceDetails','setcompliance'=>'setCompliance','setcomplianceallocation'=>'setComplianceAllocation','getcompliancesdashbord'=>'getCompliancesDashbord','updateticketfollowers'=>'updateTicketFollowers','getstatusworkflow'=>'getStatusWorkFlow','startworkflow'=>'startWorkFlow','getuserstatusstep'=>'getUserStatusStep','getstatusprogresslist'=>'getStatusProgressList','insertemployeesalary'=>'insertemployeesalary','closeallnotifications'=>'closeallnotifications','closesinglenotifications'=>'closesinglenotifications','getnewemployeedashboarddetails'=>'getnewemployeedashboarddetails','getwrokflowsteps'=>'getWrokflowSteps','checkworkflowcriteria'=>'checkWorkFlowCriteria','insertleaverequest'=>'insertLeaveRequest','gettocheckleavetypecriteria'=>'getToCheckLeaveTypeCriteria','loginafterdetails'=>'loginafterdetails','togettransactionlogempitdeclaration'=>'togettransactionlogempitdeclaration','updatepormanace'=>'updatePormanace','addkra'=>'addKra','getorganizationgoalsdetails'=>'getOrganizationGoalsSetails','getjobtitlecompetencies'=>'getJobTitleCompetencies','getperformanaceappraiserheader'=>'getPerformanaceAppraiserHeader','getpeerfeedback'=>'getPeerFeedback','getperformanceappraisalsettings'=>'getPerFormanceAppraisalAettings','getviewperformanceappraisalsettig'=>'getViewPerFormanceAppraisalSettig','getpayrunsummarysalary'=>'getPayRunSummarySalary','getemployeenotattendance'=>'getEmployeeNotAttendance','getindexmobiledetails'=>'getIndexMobileDetails','getdashboardmobiledetails'=>'getDashboardMobileDetails','getemployeehrservice'=>'getEmployeeHRService','togettransactionLoginbox'=>'toGetTransactionLogInbox','getleaveavailability'=>'getLeaveAvailability','getleavecalender'=>'getLeaveCalender','getdynamictabledetails'=>'getDynamicTableDetails','getholidayscalendor'=>'getHolidaysCalendor','getsubscriptionusers'=>'getSubscriptionUsers','employeeattendaceupload'=>'employeeattendaceupload','employeesalaryupload'=>'employeesalaryupload','employeesalarycomponetsupload'=>'employeesalarycomponetsupload','insertemployeectcrrohan'=>'insertemployeectcrrohan','getcandidates'=>'getCandidates','getcandidatedetails'=>'getCandidateDetails','getrecruitmentdashboard'=>'getRecruitmentDashboard','getcandidateoverviews'=>'getCandidateOverViews','getcandidatelogs'=>'getCandidateLogs','getapplicationrole'=>'getApplicationRole','getnewjoinjobcandidates'=>'getnewjoinjobcandidates','getlistofjobcandidate'=>'getlistofjobcandidate','getviewalldetilarecruitmentdashboard'=>'getviewalldetilarecruitmentdashboard','getjobhiringsource'=>'getJobHiringSource','getcandidateemailpopup'=>'getCandidateEmailPopup','getjobpostingpagedetails'=>'getJobPostingPageDetails','getrecruitngmanagerdashboard'=>'getRecruitngManagerDashboard','getcandidateaccessright'=>'getCandidateAccessRight','getworkflowconfiguration'=>'getWorkFlowConfiguration','domainverifye'=>'domainVerifye','insertaudittrail'=>'insertAuditTrail','getaudittrails'=>'getAuditTrails','getdynamicmoduledashboard'=>'getDynamicModuleDashboard','payrolldashboard'=>'payRollDashboard', 'getreportsalesbycustomer'=> 'getReportSalesByCustomer','getdynamicworkflowsteps'=>'getDynamicWorkFlowSteps','submitstatusdynamicworkflow'=>'submitStatusDynamicWorkFlow','getdynamicquotationdetails'=>'getDynamicQuotationDetails','gettopupdetails'=>'getTopUpdetails','dynamiccheckqtyvalidation'=>'dynamicCheckQtyValidation','getdynamictransactioninvoices'=>'getDynamicTransactionInvoices','getdynamicpopupvendorsassignrequisition'=>'getDynamicpopUpVendorsAssignRequisition','getdocumentaccesssetups'=>'getDocumentAccessSetups','getapplicationroles'=>'getApplicationRoles', 'currencyconversionrate'=>'currencyConversionRate', 'userandcurrencydetails' => 'userAndCurrencyDetails', 'tochecktransactionlocationwarehousebefore' => 'tochecktransactionlocationwarehousebefore','getdisplayattributesnewversion'=>'getDisplayAttributesNewVersion','getcandidategenerateemployee'=>'getCandidateGenerateEmployee','getemployeeonboardingtask'=>'getEmployeeOnboardingTask','getvendordashboard'=>'getVendorDashboard','checkbilldetails'=>'toGetblancebill','getpayments'=>'getPayments','getbillsstackdashboardchart'=>'getBillsStackDashboardChart','togethremployeedetails'=>'togethremployeedetails','sendmailwithpdf'=>'sendMailWithPDF','getsetupdetails'=>'getSetupDetails','replicateListofCommonTables'=>'replicateListofCommonTables','dynamicapplicationemptytransactions'=>'dynamicApplicationEmptyTransactions','getapplicationemptytransactions'=>'getApplicationEmptyTransactions', 'getadditionalcharge'=>'getAdditionalCharge','setfollowuplevels'=>'setfollowUpLevels','getdynamicinvoiceprint'=>'getDynamicInvoicePrint','cronjobfollowuplevels'=>'cronJobFollowUpLevels','getprojecttaskdetailsfolloups'=>'getProjectTaskDetailsFolloUps','newcandidateautoregistation'=>'newcandidateautoregistation','getsalarytemplatecomponents'=>'getSalaryTemplateComponents','tocreateprocesstaskintotaskdatail'=>'tocreateprocesstaskintotaskdatail','vendorregistartiontasksssigne'=>'vendorRegistartionTaskAssigne','getapplicationusers'=>'getApplicationUsers','gettransactionactivityschedules'=>'getTransactionActivitySchedules','gettransactionactivityschedulesdashboartd'=>'getTransactionActivitySchedulesDashboartd', 'togenrateholdemployees'=>'togenrateholdemployees','emailfunctioncheck'=> 'emailfunctioncheck','getitempricehistoriesbyitemid'=>'getitempricehistoriesbyitemid','getitemsorders'=>'getItemsOrders','getpartydetails'=>'getPartyDetails','getapplicationuserinvitations'=>'getApplicationUserInvitations','setcronjobworkflowconfigurationsnew'=>'setCronJobWorkFlowConfigurationsNew','allocateemployeeseat'=>'allocateEmployeeSeat','getemployeesdisplayfields'=>'getEmployeesDisplayFields','getapplicationreports'=>'getApplicationReports','getemployees'=>'getEmployees','getempbanks'=>'getEMPBanks','toinvoitesendmail'=>'toInvoiteSendMail','userpasswordreset'=>'userPasswordReset','getpurchaseinvoiceandsupplireinvoicedashboard'=>'getPurchaseInviceandSupplireInvoiceDashboard','getvendorcustomeritems'=>'getVendorCustomerItems','updatetransactiontds'=>'updateTransactionTDS','gerreferancetabledetails'=>'gerReferanceTableDetails','getreceipts'=>'getReceipts', 'tosendotpmessage'=>'tosendotpmessage', 'togetotpmobiledetails'=>'togetotpmobiledetails', 'toupdatemobilenumber'=>'toupdatemobilenumber', 'toverifymobileotp'=> 'toverifymobileotp','emailinvoicesend'=>'emailInvoiceSend','getitems'=>'getItems','getitemcategories'=>'getItemCategories','getclonetransactions'=>'getCloneTransactions', 'getslatrackinglogreports'=>'getSLSTrackingLogReports','getitemsearch'=>'getItemSearch','getimportexceldata'=>'getImportCxcelData','getdynamicinvocedetails'=>'getdynamicinvocedetails', 'gettranslations'=>'gettranslations' ,'tocreateusertoken'=>'tocreateusertoken', 'tosendmessagetomobile'=>'tosendmessagetomobile', 'tosendwhatsappdata'=>'tosendwhatsappdata','getskudetails'=>'getskudetails','getdynamicinviceparty'=>'getDynamicInviceParty','getmultireferencepagedetails'=>'getMultiReferencePageDetails', 'sendotptoverifymobile' => 'sendotptoverifymobile', 'verifyotpsendtomobile' => 'verifyotpsendtomobile', 'tocreateuserwithusermodulelist' => 'tocreateuserwithusermodulelist','getaudittrails_new'=>'getAuditTrails_new','tosendmailtest'=>'tosendmailtest','getmodulerolelogs'=>'getModuleRoleLogs','getfailedloginattempts'=>'getFailedLoginAttempts','getfailedloginattemptdetails'=>'getFailedLoginAttemptDetails','getcancelinvoicepopup'=>'getCancelInvoicePopUp','proformainvoicethroughsalesinvoicescreated'=>'proformaInvoiceThroughSalesInvoicesCreated', 'tocallmobilenotificationmob'=>'tocallmobilenotificationmob');	   
 
	if(isset($check_array[$mode])){
		//echo "api data"; exit;
		$fun = $check_array[$mode];
		$details = $userObj->$fun($_GET);
		echo json_encode($details);
	}
 	$integcheck_array = array('getviewalldetilarecruitmentdashboardineg'=>'getviewalldetilarecruitmentdashboardineg', 'togetmyreportswithdetails' => 'togetmyreportswithdetails', 'togetcandidatefunnel' => 'togetcandidatefunnel', 'togetcandidatebydisabilitygender'=>'togetcandidatebydisabilitygender','toinsertreportviewdata'=>'toinsertreportviewdata','togetcandidatebyracegender'=>'togetcandidatebyracegender', 'togetcandidatesources'=>'togetcandidatesources','togetcandidatebyveterangender'=>'togetcandidatebyveterangender','toinsertapplication_table_histories'=>'toinsertapplication_table_histories', 'togetleavelstemployeeajax'=>'togetleavelstemployeeajax','togetprojectinsertdata'=>'togetprojectinsertdata', 'togetprojectkanbanboarddetails'=>'togetprojectkanbanboarddetails', 'togetprojecttaskdetailspopup' => 'togetprojecttaskdetailspopup', 'togetprojectdashboarddetails'=>'togetprojectdashboarddetails', 'togettopprojecttasklist'=>'togettopprojecttasklist', 'togettopprojectworkspacelist'=>'togettopprojectworkspacelist', 'togetlistprojecttaskdetailspopup'=>'togetlistprojecttaskdetailspopup', 'todatainsertall'=>'todatainsertall', 'togetprojectviewalldetails'=> 'togetprojectviewalldetails', 'togetuseralldetailsforuser' =>'togetuseralldetailsforuser','togetprojecteamaddetails'=>'togetprojecteamaddetails','togetunassigntaskslist'=>'togetunassigntaskslist','tocheckprojectteamuncompletetasks'=>'tocheckprojectteamuncompletetasks','togetprojecttasktimelog'=>'togetprojecttasktimelog',  'getitemqualitychecknewdetails'=>'getitemqualitychecknewdetails', 'togetqualitychecmappingindetails'=>'togetqualitychecmappingindetails','togetqualitychecmappinginwithitemmadetails'=>'togetqualitychecmappinginwithitemmadetails','togetdatawithseriallotnumber'=> 'togetdatawithseriallotnumber','tocheckandisertthedatafortablequalitycheck'=>'tocheckandisertthedatafortablequalitycheck','toudatedepartmentandcoscenter'=>'toudatedepartmentandcoscenter', 'togetdyanamicselectdatatablename'=>'togetdyanamicselectdatatablename', 'togenratesalarytemplates'=>'togenratesalarytemplates', 'togetreportvillvendors'=>'togetreportvillvendors', 'togetreportvillbaseidvendors' => 'togetreportvillbaseidvendors', 'togetprojectdnewashboarddetails'=>'togetprojectdnewashboarddetails','togetprojectmahinematerial'=>'togetprojectmahinematerial','togetprojecttaskmahinematerial'=>'togetprojecttaskmahinematerial', 'togetcourier'=>'togetcourier','togetofficemanagement'=>'togetofficemanagement','addstationerycartqty'=>'addstationerycartqty','togetuserstationerycart'=>'togetuserstationerycart','tocreateuserstationeryrequest'=>'tocreateuserstationeryrequest','togetuserliststationerycart'=>'togetuserliststationerycart','togetuserrequestcartdetails'=>'togetuserrequestcartdetails', 'togetallslectedtrasportdetails'=>'togetallslectedtrasportdetails','searchcourierdata'=>'searchcourierdata', 'searchstationerydata'=>'searchstationerydata', 'updatetranspotfrom'=>'updatetranspotfrom', 'togetinsertdetalsfoods'=>'togetinsertdetalsfoods', 'updatefoodrequest'=>'updatefoodrequest','searchstationeryreport'=>'searchstationeryreport', 'togetofficemanagementdashboard' =>'togetofficemanagementdashboard', 'tochangeprojectowner'=>'tochangeprojectowner','searchtransportbus'=>'searchtransportbus', 'reportofyearmonthalldetails'=>'reportofyearmonthalldetails', 'togetemployeeform16adetails'=>'togetemployeeform16adetails', 'assignemptofrom16a'=>'assignemptofrom16a', 'getassignfrom16aemployeelist'=>'getassignfrom16aemployeelist','togetreleasedocumentlistdetails'=>'togetreleasedocumentlistdetails','togetdcumentdashboarddetails'=>'togetdcumentdashboarddetails', 'togetinsertemptaxdepositdetails' => 'togetinsertemptaxdepositdetails', 'toinsertupdateemptaxdeposit' => 'toinsertupdateemptaxdeposit', 'togetueremailactivation'=>'togetueremailactivation', 'toinsertuseremailactivation'=>'toinsertuseremailactivation', 'togetgeneralcodedetails'=>'togetgeneralcodedetails','togetimplodesdetailsount'=>'togetimplodesdetailsount','toimportthedataintotablesemployeessalary'=>'toimportthedataintotablesemployeessalary', 'gnrolepermision'=>'gnrolepermision', 'toinsertapplication_table_histories'=>'toinsertapplication_table_histories','togetjobtitleexploc'=>'togetjobtitleexploc','togetjobtitleexp'=>'togetjobtitleexp', 'togetcarddetailsemp'=>'togetcarddetailsemp', 'toinsertapplicationpageprocess'=>'toinsertapplicationpageprocess', 'togetanddifflisttablefields'=>'togetanddifflisttablefields','getmoduleprocesslist' => 'getmoduleprocesslist','getapplicationserialnumbers'=>'getApplicationSerialNumbers', 'tobeforeaddmeetingrommbook'=>'tobeforeaddmeetingrommbook', 'getDynamicCities' => 'getDynamicCities','getCompanyAnnouncements' => 'getCompanyAnnouncements','tocreateuserstationeryrequestnewhr' => 'tocreateuserstationeryrequestnewhr', 'getStationeryAvailableItemsData' => 'getStationeryAvailableItemsData', 'getEmployeeWiseIssuanceDetails' => 'getEmployeeWiseIssuanceDetails', 'getMonthlyStationeryReport' => 'getMonthlyStationeryReport', 'getStationeryStockBalance' => 'getStationeryStockBalance', 'getTotalItemsIssuedCategoryWise' => 'getTotalItemsIssuedCategoryWise', 'getStationeryStockInOutDatewise' => 'getStationeryStockInOutDatewise', 'getprojectemployeetaskreport'=>'getProjectEmployeeTaskReport','togenratesalarytemplatesoot'=>'togenratesalarytemplatesoot');
 	if(isset($integcheck_array[$mode])){
 		//echo "api-".$mode; exit;
		$fun = $integcheck_array[$mode];
		//echo $fun; exit;
		$details = $intiObj->$fun($_GET);
		echo json_encode($details);
	}


$demo_class_array =  array('getstandardmissiongreport'=>'getStandardMissiongReport','generatetenantinvoicePDF'=>'generateTenantInvoicePDF');
if(isset($demo_class_array[$mode])){
	$fun = $demo_class_array[$mode];
	$details = $demoObj->$fun($_GET);
	echo json_encode($details);
}

$asset_class_array =  array('getassetallocation'=>'getAssetAllocation','getassetandattributes'=>'getAssetAndAttributes','getassettransferrequests'=>'getassetTransferRequests','getassetverifications'=>'getAssetVerifications');
if(isset($asset_class_array[$mode])){
	$fun = $asset_class_array[$mode];
	$details = $assetObj->$fun($_GET);
	echo json_encode($details);
}

$claim_class_array =  array('getreimbursementdashboard'=>'getReimbursementDashboard','getreimbursementdashboardchart'=>'getReimbursementDashboardChart','getreimbursementpaymentdashboard'=>'getReimbursementPaymentDashboard','getempexpenses'=>'getEmpExpenses','togetemployeetripsbyuser'=>'togetemployeetripsbyuser','togetinprocesstripdetails'=>'togetinprocesstripdetails','getreimbursementpolicies'=>'getReimbursementPolicies', 'getTrevllingPlanData' => 'getTrevllingPlanData');
if(isset($claim_class_array[$mode])){
	$fun = $claim_class_array[$mode];
	$details = $claimObj->$fun($_GET);
	echo json_encode($details);
}

$recruitment_class_array =  array('getcandidateofferdetails'=>'getCandidateOfferDetails','getcandidateinterviewpopup'=>'getCandidateInterviewPopUp','candidatesofferhistories'=>'candidatesofferhistories','getcandidatemaster'=>'getCandidateMaster','getjobpostinglist'=>'getJobPostingList','getrecruitmentintegdashboard'=>'getrecruitmentintegdashboard','getcandidatescreeningquestions'=>'getCandidateScreeningQuestions');
if(isset($recruitment_class_array[$mode])){
	$fun = $recruitment_class_array[$mode];
	$details = $recruitmentObj->$fun($_GET);
	echo json_encode($details);
}


$pms_class_array =  array('publichappraisal'=>'publichAppraisal','getperformanceappraisalreport'=>'getPerFormanceAppraisalReport','getperformanceappraisal'=>'getPerFormanceAppraisal','getperformanceappraisalsetups'=>'getPerforManceAppraisalSetups','getkipandkradetails'=>'getKipAndKraDetails','insertappraisalkra'=>'insertAppraisalKRA','getorganizationgoalcheck'=>'getorganizationgoalcheck','getkratagging'=>'getKRATagging','pmsdashboard1' => 'pmsDashboard1', 'pmsdashboard2'=>'pmsDashboard2','getemployeegoalsdetails'=>'getEmployeeGoalsDetails','getorganizationgoals'=>'getOrganizationGoals','getgoals'=>'getGoals','getfeedbacks'=>'getFeedBacks','getemployeeperformanceappraisals'=>'getEmployeePerformanceAppraisals', 'getpmsadmindashboard'=>'getPMSAdminDashboard','getemployeegoals'=>'getEmployeeGoals','getpmsemployeedashboard'=>'getPMSEmployeeDashboard','getemployeefeedbacks'=>'getEmployeeFeedBacks','getappraisalbellcurves'=>'getAppraisalBellCurves');
if(isset($pms_class_array[$mode])){
	$fun = $pms_class_array[$mode];
	$details = $pmsObj->$fun($_GET);
	echo json_encode($details);
}
$esop_class_array =  array('getcompanyvaluations'=>'getCompanyValuations','getesopplans'=>'geteSopPlans','getesopallocations'=>'getESOPAllocations','getesopreports'=>'getESOPReports','getesopadmindashboard'=>'getESOPAdminDashboard','getvaluationreport'=>'getValuationReport','getesopdetails'=>'getESOPDetails');
if(isset($esop_class_array[$mode])){
	$fun = $esop_class_array[$mode];
	$details = $esopObj->$fun($_GET);
	echo json_encode($details);
}

$account_class_array =  array('getfinanacereportgroups'=>'getFinanaceReportGroups','selectchartofaccont'=>'selectChartOfAccont','getchartofaccounts'=>'getChartOfAccounts','gettocheader'=>'getTOCHeader','gettextofcontentreports'=>'getTextOfContentReports','getfinanacereports'=>'getFinanaceReports','getnotes'=>'getNotes','getjournals'=>'getJournals','gettaxauthorities'=>'getTaxAuthorities','getfiscalpositions'=>'getFiscalPositions','getdeferredrevenues'=>'getDeferredRevenues','getfollowuplevels'=>'getFollowUpLevels','getreconciliations'=>'getReconciliations','togetbudgetuserdeailsaccount'=>'togetbudgetuserdeailsaccount','getjournalvouchers'=>'getJournalVouchers','getbankvouchers'=>'getBankVouchers','getcashvouchers'=>'getCashVouchers','getgeneralledger'=>'getGeneralLedger','gettrialbalancesreport'=>'getTrialBalancesReport','insertaccountbalances'=>'insertAccountBalances','getpurchaseledger'=>'getPurchaseLedger','getsalesledger'=>'getSalesLedger','getprofitandlossreports'=>'getProfitAndLossReports','getbalancesheetreport'=>'getBalanceSheetReport','getcashflowreports'=>'getCashFlowReports','getdebitandcreditnotes'=>'getDebitAndCreditNotes','getdynamicmappingtableinvoices'=>'getDynamicMappingTableInvoices','getcustomerledger'=>'getCustomerLedger','getcustomerstatements'=>'getCustomerStatements','getcustomerledger'=>'getCustomerLedger','getcustomerstatements'=>'getCustomerStatements','getenquiryvendororcustomer'=>'getEnquiryVendorOrCustomer','getvendorledger'=>'getVendorledger','getvendorstatements'=>'getVendorstatements','chartofaccountlockandunlock'=>'chartOfAccountLockAndUnlock');
if(isset($account_class_array[$mode])){
	$fun = $account_class_array[$mode];
	$details = $accountObj->$fun($_GET);
	echo json_encode($details);
}

$rental_class_array =  array('getpropertyowners'=>'getPropertyOwners','getpropertytenantdetails'=>'getPropertyTenantDetails','getajaxpropertyownerdetails'=>'getAjaxPropertyOwnerDetails','insertrentalinvoices'=>'insertRentalInvoices','getrentaladmindashboard'=>'getRentalAdminDashboard','getrentaldashboardchart'=>'getRentalDashboardChart','getpropertytenantbillings'=>'getPropertyTenantBillings','gettenantcustomers'=>'getTenantCustomers','getcustomersdetails'=>'getCustomersDetails','renatalinvoicesend'=>'renatalInvoiceSend','getownerreports'=>'getOwnerReports','gettenantreports'=>'getTenantReports','gettenantbillingreports'=>'gettenantBillingReports','gettenantproperty'=>'getTenantProperty','getrentalinvoices'=>'getRentalInvoices','getrentalinvoiceprint'=>'getRentalInvoicePrint','getownerinvoicereports'=>'getOwnerInvoiceReports','getrentalinvoice'=>'getrentalinvoice');
if(isset($rental_class_array[$mode])){
	$fun = $rental_class_array[$mode];
	$details = $rentalObj->$fun($_GET);
	echo json_encode($details);
}

$pettycash_class_array =  array('pettycashdashboard'=>'pettyCashDashboard','getpettycashrequests'=>'getPettyCashRequests','getpettycashtopupdashboardchart'=>'getPettyCashTopUpDashboardChart','getpettycashdashboardchart'=>'getPettyCashDashboardChart','getpettycash'=>'getPettyCash','getexpensepettycash'=>'getExpensePettyCash','getpettycashboxtopuprequests'=>'getPettyCashBoxTopUpRequests');
if(isset($pettycash_class_array[$mode])){
	$fun = $pettycash_class_array[$mode];
	$details = $pettycashObj->$fun($_GET);
	echo json_encode($details);
}

$hr_class_array =  array('getattendancedata'=>'getAttendanceData','getemployeeworkattendances'=>'getEmployeeWorkAttendances','insertemployeeworkattendances'=>'insertEmployeeWorkAttendances','getimportswapinout'=>'getImportSwapInOut', 'togetlocationemployeeattendance'=>'togetlocationemployeeattendance', 'togetpayadminpayrealist'=>'togetpayadminpayrealist', 'togetuserrolelist'=>'togetuserrolelist', 'employeeleaveapplyattendance'=>'employeeleaveapplyattendance', 'togetnewinsertemloyeedetails'=>'togetnewinsertemloyeedetails','togetcurrentmonthjoinemployes'=>'togetcurrentmonthjoinemployes','togetpayrollexitemployes'=>'togetpayrollexitemployes','togetpayrollstepsdetails'=>'togetpayrollstepsdetails','tocheckpayrolldetailsfull'=>'tocheckpayrolldetailsfull','togetprofessionaltaxexemptionsdetails'=>'togetprofessionaltaxexemptionsdetails', 'getholdsalrylist'=>'getholdsalrylist','togetversonyearitdecration'=>'togetversonyearitdecration','getemployeeleavebalancereports'=>'getEmployeeLeaveBalanceReports', 'togetitemrelateddetails'=>'togetitemrelateddetails', 'gettaxslabdata'=> 'gettaxslabdata','togetnewsalarydetails'=>'togetnewsalarydetails', 'togetemloyeesalarydetails'=>'togetemloyeesalarydetails','getResignedEmployeeData' => 'getResignedEmployeeData', 'getResignedEmployeeHistoryData' => 'getResignedEmployeeHistoryData', 'getitadmindashboard'=>'getitadmindashboard','togetalldetailsforemployeeval'=>'togetalldetailsforemployeeval','getemployeeworkattendances_new'=>'getemployeeworkattendances_new', 'createExcelDataForEmployeeMonthlyAttedance' => 'createExcelDataForEmployeeMonthlyAttedance', 'getEmployeeMissingDetails' => 'getEmployeeMissingDetails', 'togetpayareaconfigdetailsbasedonemp_id'=>'togetpayareaconfigdetailsbasedonemp_id','getemployeeworkattendancespayareawise'=>'getemployeeworkattendancespayareawise', 'insertemployeeworkattendancespayareawise'=> 'insertemployeeworkattendancespayareawise', 'togetcontractemployeepaydetails'=>'togetcontractemployeepaydetails', 'togetcontractorctcreportdetails'=>'togetcontractorctcreportdetails','getattendanceaudithistorybyrid'=>'getAttendanceAuditHistoryByRid','getEmployeeReportData'=>'getEmployeeReportData', 'togetdocumentlistfile'=>'togetdocumentlistfile','togetemployeesassementyear'=>'togetemployeesassementyear');
if(isset($hr_class_array[$mode])){
	$fun = $hr_class_array[$mode];
	$details = $hrObj->$fun($_GET);
	echo json_encode($details);
}

//$vendor_porter_class_array =  array('getapplicationtickets'=>'getApplicationTickets','vendorapicallforvmslist'=>'vendorapicallforvmslist','vmsvendorupdatetoapi'=>'vmsvendorupdatetoapi','getvendorregistrationrequests'=>'getVendorRegistrationRequests','getvendordisplayfields'=>'getVendorDisplayFields','getvendorandvendorregisterssynchapi'=>'getVendorandVendorRegistersSynchAPI','insertvendorandvendorregisterssynchapi'=>'insertVendorandVendorRegistersSynchAPI','getvendorsdetailsreports'=>'getVendorsDetailsReports','getvendorsdetails'=>'getVendorsDetails');
$vendor_porter_class_array =  array('getapplicationtickets'=>'getApplicationTickets','vendorapicallforvmslist'=>'vendorapicallforvmslist','vmsvendorupdatetoapi'=>'vmsvendorupdatetoapi','getvendorregistrationrequests'=>'getVendorRegistrationRequests','getvendordisplayfields'=>'getVendorDisplayFields','getvendorandvendorregisterssynchapi'=>'getVendorandVendorRegistersSynchAPI','insertvendorandvendorregisterssynchapi'=>'insertVendorandVendorRegistersSynchAPI','getvendorsdetailsreports'=>'getVendorsDetailsReports','getvendorsdetails'=>'getVendorsDetails','getvendoradmindashboard'=>'getVendorAdminDashboard','getinvitationvendors'=>'getInvitationVendors');
if(isset($vendor_porter_class_array[$mode])){
	include("model/vendor_portal_user.class.php");
	$vpuObj=new vendorPortelUserClass();
	$fun = $vendor_porter_class_array[$mode];
	$details = $vpuObj->$fun($_GET);
	echo json_encode($details);
}

$erp_class_array =  array('togetinventorysummaryrepot'=>'togetinventorysummaryrepot', 'togeticommittedstockdetailsrepot'=>'togeticommittedstockdetailsrepot', 'togetstocksummaryrepot'=> 'togetstocksummaryrepot', 'togetactivepurchaseorderrepot'=>'togetactivepurchaseorderrepot', 'togetproductsalesrepot'=>'togetproductsalesrepot', 'togetinventoryvaluationsummaryrepot'=>'togetinventoryvaluationsummaryrepot', 'togetfifocostlottrackingrepot'=>'togetfifocostlottrackingrepot', 'togetpurchasesbyitemrepot'=>'togetpurchasesbyitemrepot', 'togetreceiveshistoryrepot'=>'togetreceiveshistoryrepot', 'togetpurchasebyvendorsrepot'=>'togetpurchasebyvendorsrepot', 'togetpaymentreceivedrepot'=>'togetpaymentreceivedrepot', 'togetthreewaymatch'=>'togetthreewaymatch', 'itemwarehouseledger'=> 'itemwarehouseledger', 'togetstockvaluesummaryrepot'=> 'togetStockValueSummaryRepot','togetvendorbalances' => 'togetvendorbalances', 'togetbilldetails' => 'togetbilldetails', 'togetvendorcreditdetails' => 'togetvendorcreditdetails','togetpaymentsmade' => 'togetpaymentsmade', 'togetpodetails' => 'togetpodetails','getbreakingdownitemprocess'=>'getBreakingDownItemProcess','togetsalesbyitemrepot'=>'togetSalesByItemRepot', 'togetorderfulfilmentbyitemrepot'=>'toGetOrderFulfilmentByItemRepot', 'togetcustomerbalances'=>'togetCustomerBalances','getstockbalanceavailability'=>'getStockBalanceAvailability','getkittings'=>'getKittings','getdekittings'=>'getDekittings','getdailyandweeklytransactions'=>'getDailyAndWeeklyTransactions',   
	'getopenpodetails' => 'getOpenPOReport',
		'geturgentemergencypurchasedetails' => 'getUrgentEmergencyPurchaseDetails',
		'getstockagingreportdetails' => 'getStockAgingReportDetails',
		'getdebitorcreditnoteregisterdetails' => 'getDebitOrCreditNoteRegisterDetails',
		'getapreconciliationreport' => 'getAPReconciliationReport',
		'getaccruedliabilitiesreport' => 'getAccruedLiabilitiesReport',
		'getpaymentproposalreport' => 'getPaymentProposalReport',
		'getadvancepaymentreport' => 'getAdvancePaymentReport',
		'getvendorpaymentagingreport' => 'getvendorpaymentagingreport',
		'getvendorreconciliationstatement' => 'getVendorReconciliationStatement',
		'getpendinginvoicegenerationreport' => 'getPendingInvoiceReport',
		'getsalestrendreport' => 'getSalesTrendReport',
		'getdayssalesoutstandingreport' => 'getDaysSalesOutstandingReport',
		'getaragingreport' => 'getARAgingReport',
		'getpricevariancereport' => 'getPriceVarianceReport',
		'getdebitorcreditmemoregisterdetails' => 'getDebitOrCreditMemoRegisterDetails','togetpurchaserequisition' => 'togetpurchaserequisition', 'togetpovsgrndata' => 'togetpovsgrndata',
    'togetpendinggrn' => 'togetpendinggrn', 'togetmaterialshortagebasedonpurchaseorder' => 'togetmaterialshortagebasedonpurchaseorder','togetmaterialshortagebasedonsalesorder' => 'togetmaterialshortagebasedonsalesorder', 'togetduplicateinvoicedata' => 'togetduplicateinvoicedata', 'togetwithholdingtax' => 'togetwithholdingtax', 'togetopensalesorders' => 'togetopensalesorders',
    'togetordervsshipmentdata' => 'togetordervsshipmentdata' , 'togetspendanalysisbyvendor' => 'togetspendanalysisbyvendor', 'togetspendanalysisbycategory' => 'togetspendanalysisbycategory', 'togetcancelledoramendedorders' => 'togetcancelledoramendedorders', 'togetsalesbyproduct' => 'togetsalesbyproduct','togetsalesbycustomer' => 'togetsalesbycustomer', 'togetsalesbyregion' => 'togetsalesbyregion', 'togetdeliverypendingreport' => 'togetdeliverypendingreport', 'togetcustomerbalancereport' => 'togetcustomerbalancereport', 'togetcollectionsreport' => 'togetcollectionsreport', 'togetcustomercreditutilizationreport' => 'togetcustomercreditutilizationreport', 'togetblockedorderscreditreport' => 'togetblockedorderscreditreport', 'togetcustomerpaymenthistoryreport' => 'togetcustomerpaymenthistoryreport', 'togettaxreportbyregion' => 'togettaxreportbyregion', 'togettopcustomercontributionreport' => 'togettopcustomercontributionreport', 'togetprofitabilityreport' => 'togetprofitabilityreport',
    'togetfreightcostreport' => 'togetfreightcostreport','getcashapplicationreport' => 'getCashApplicationReport',
		'gettopvendorspendreport' => 'getTopVendorSpendReport',
		'getdiscountrebateutilizationreport' => 'getDiscountAndRebateUtilizationReport','getbankreconciliations'=>'getBankReconciliations');
if(isset($erp_class_array[$mode])){
	include("model/erp_user.class.php");
	$erpObj=new erpUserClass();
	$fun = $erp_class_array[$mode];
	$details = $erpObj->$fun($_GET);
	echo json_encode($details);
}


$space_class_array =  array('getbuildings'=>'getBuildings','getbuildingfloors'=>'getBuildingFloors','getequipmentallocations'=>'getEquipmentAllocations', 'getequipmentincidents'=>'getequipmentIncidents','getequipments'=>'getEquipments','apiaccountbalancesupdate'=>'apiaccountbalancesupdate', 'approvedapaceallocationsstatus'=>'approvedApaceAllocationsStatus', 'tocallmobilenotification'=>'tocallmobilenotification');
if(isset($space_class_array[$mode])){
	$fun = $space_class_array[$mode];
	$details = $spaceObj->$fun($_GET);
	echo json_encode($details);
}

$mobilecheck_array = array('mobileaprovelistwithmodle'=>'mobileaprovelistwithmodle', 'togetuserdetailsbaseonuserid'=>'togetuserdetailsbaseonuserid', 'togetuserdetailsbaseonuserid'=>'togetuserdetailsbaseonuserid', 'togetbaseonmoduluelistandapprovals'=>'togetbaseonmoduluelistandapprovals', 'togetdynamicinvoicedetails'=>'togetdynamicinvoicedetails','togetconfigmobiledata'=>'togetconfigmobiledata', 'togetrecallstatusdata'=>'togetrecallstatusdata','togetcreatedapproverejectinvoicedetails'=>'togetcreatedapproverejectinvoicedetails', 'togetapproveinvoicedetails'=>'togetapproveinvoicedetails', 'togetrecjectinvoicedetails'=>'togetrecjectinvoicedetails', 'togetcreatedinvoicedetails'=>'togetcreatedinvoicedetails', 'notificationsforuserid'=>'notificationsforuserid', 'countLines'=>'countLines', 'togetemployeeattendatailsbasedondate'=>'togetemployeeattendatailsbasedondate', 'mobileemployeeclocks'=>'mobileemployeeclocks',				'listgetmonthattendancedetails'=>'listgetmonthattendancedetails', 'togetyearlymonthwisedataperce' =>'togetyearlymonthwisedataperce', 'togetmobileleavedetails'=>'togetmobileleavedetails', 'todeletemobileempleave'=>'todeletemobileempleave', 'togetfieldworkusermappoints'=>'togetfieldworkusermappoints', 'togenrateqrcodebasetype'=>'togenrateqrcodebasetype','togetemloyeemodeleraiodcricle'=>'togetemloyeemodeleraiodcricle','togetcreatecomplientdetails'=>'togetcreatecomplientdetails', 'togetapproalcomplientdetails'=>'togetapproalcomplientdetails', 'togenrateqrcodebaseontypelist'=>'togenrateqrcodebaseontypelist', 'togetbulidingfloors'=>'togetbulidingfloors', 'tocancelthecomplients'=>'tocancelthecomplients', 'tocheckpermissiontoqrcodeapproval'=>'tocheckpermissiontoqrcodeapproval', 'togetcomplientsbyiddetails'=> 'togetcomplientsbyiddetails', 'todeletecomplaintbasedonid'=>'todeletecomplaintbasedonid','togetsalaryyearmonthlist' => 'togetsalaryyearmonthlist',  'togetsalarydetailsyearbasemonthlist' => 'togetsalarydetailsyearbasemonthlist','mobileregistationcheckotp'=>'mobileregistationcheckotp', 'togetmobfielddailyplans'=>'togetmobfielddailyplans', 'togetmobfieldworkdetails'=>'togetmobfieldworkdetails', 'togetmobmanagerfielddailyplans'=>'togetmobmanagerfielddailyplans', 'togetmobmanagerfieldworkdetails'=>'togetmobmanagerfieldworkdetails', 'toinactiveasedontableid'=>'toinactiveasedontableid', 'tocheckuserfieldwortype'=>'tocheckuserfieldwortype','togetworkfielddataperdate'=>'togetworkfielddataperdate', 'togetworkfielddatapermonth'=>'togetworkfielddatapermonth', 'map_out_time'=>'map_out_time', 'togetsecurtydetails'=>'togetsecurtydetails', 'togetuserfieldworkdata'=>'togetuserfieldworkdata', 'togetlistfiledworkdatabasedate'=>'togetlistfiledworkdatabasedate', 'togetholidayslistbaseyear'=>'togetholidayslistbaseyear', 'togetweekdayslist'=>'togetweekdayslist', 'togetfieldworkdailyplansfrmobile'=>'togetfieldworkdailyplansfrmobile', 'togetgeolocationresaonlist'=>'togetgeolocationresaonlist', 'togetuserslistformobile'=>'togetuserslistformobile', 'togetuserssecurtydaydetailswithhistory'=>'togetuserssecurtydaydetailswithhistory', 'togetrelatedworkusermapointmob'=>'togetrelatedworkusermapointmob', 'dyanamicgenccodedetailformobile'=>'dyanamicgenccodedetailformobile', 'togetproductformitemonmobile'=>'togetproductformitemonmobile', 'togetproductcustomeronmobile'=>'togetproductcustomeronmobile', 'getmobiletransactionreceipts'=>'getMobileTransactionReceipts', 'getmobilereceipts'=>'getMobileReceipts','getmoblieuserrelateddetails'=>'getMoblieUserRelatedDetails', 'tokengenrateformobile'=>'tokengenrateformobile','toretrivewebviewlogin'=>'toretrivewebviewlogin','getMobileexpensedetails'=>'getMobileExpenseDetails', 'getMobileOrders' => 'getMobileOrders',	'getMobileOrderInfo'=> 'getMobileOrderInfo','getmobilecrmdetails'=>'getMobileCRMDetails','togetdeliveryschedulesdetails'=>'togetdeliveryschedulesdetails','getfacelivenessresponse'=>'getFaceLivenessResponse','deliverdorderlistbasepartyid'=>'deliverdorderlistbasepartyid','getmobileallemployeeleaves'=>'getMobileAllEmployeeLeaves','getmobilesingleemployeeleaves'=>'getMobileSingleEmployeeLeaves','getmobilereportingmanageremployeeleave'=>'getMobileReportingManagerEmployeeLeave','getinsertemployeeleaves'=>'getInsertEmployeeLeaves','checkworkflowstatuswithapprove'=>'checkWorkFlowStatusWithApprove','transportrequestcreate' => 'transportrequestcreate', 'togetdrivelistalldata'=>'togetdrivelistalldata','togetinprocesstripdetailswithexpencess'=>'togetinprocesstripdetailswithexpencess','togetdynamicresaonlist'=>'togetdynamicresaonlist', 'togetemployeeexpensesbyuser'=>'togetemployeeexpensesbyuser', 'togettaskdaworkspacenoprojectandtask'=>'togettaskdaworkspacenoprojectandtask', 'togetprojecteditdetailsval'=>'togetprojecteditdetailsval', 'togetmobileprojectsview'=>'togetmobileprojectsview', 'todisplaytasklistview'=>'todisplaytasklistview', 'togettaskcreatelistdata'=>'togettaskcreatelistdata', 'togetmobiletaskdetailsall'=>'togetmobiletaskdetailsall', 'togetmobileprojectdetails'=>'togetmobileprojectdetails', 
	'touserwisetaskbaseoninputmobile'=> 'touserwisetaskbaseoninputmobile', 'togetchatdatabasetableandtransactionid'=>'togetchatdatabasetableandtransactionid', 'togetchatdiscussdatadetails'=>'togetchatdiscussdatadetails', 'mobileuserlogout'=>'mobileuserlogout', 'togetmobiletripdetails'=>'togetmobiletripdetails', 'togettripsdetails'=>'togettripsdetails', 'togettripdetailsbasedid'=>'togettripdetailsbasedid','togetmobileannouncementcategories'=>'togetmobileannouncementcategories', 'togetmoblieannouncementbasecat'=>'togetmoblieannouncementbasecat', 'togetalltypeannucementdetailsbyanuid'=>'togetalltypeannucementdetailsbyanuid','getmobileprocurementmodulesdata'=>'getMobileProcurementModulesData', 'tocheckuserisactiveststus'=>'tocheckuserisactiveststus', 'togetpersonalgatewaydetails'=> 'togetpersonalgatewaydetails', 'getAttendanceRegularizationRequestData' =>  'getAttendanceRegularizationRequestData', 'getTransportVehicleGatePassDeatails' => 'getTransportVehicleGatePassDeatails', 'togetmobileadminhrdashboard'=>'togetmobileadminhrdashboard', 'togetmobileadminhrdashboardcheck'=>'togetmobileadminhrdashboardcheck', 'getStationeryCategoriesWithItems' => 'getStationeryCategoriesWithItems', 'searchStationeryItemsByCategory' => 'searchStationeryItemsByCategory', 'getUserStationeryCart' => 'getUserStationeryCart',  'togetmobileadminhrviewtopemplist'=>'togetmobileadminhrviewtopemplist', 'togetemployeeidentitycarddetails'=>'togetemployeeidentitycarddetails', 'togetassetsdetails' => 'togetassetsdetails', 'togetAssetRequestDetails' => 'togetAssetRequestDetails', 'togetWorkFromHomeRequestDetails' =>'togetWorkFromHomeRequestDetails','getUserMobileStatusDashboard'=>'getUserMobileStatusDashboard', 'togetmobilemanagerdashboard' => 'togetmobilemanagerdashboard', 'backviewtogetmobilemanagerdashboard' => 'backviewtogetmobilemanagerdashboard', 'togetmobilemanagerviewtopemplist' => 'togetmobilemanagerviewtopemplist', 'getPendingLeaveDetailsByEmpId' => 'getPendingLeaveDetailsByEmpId', 'getResignedEmployeeAssetDetailsByEmpId' => 'getResignedEmployeeAssetDetailsByEmpId', 'togetjobpostingdetails'=>'togetjobpostingdetails', 'togetjobpostingdetailsbyid'=>'togetjobpostingdetailsbyid', 'tocandidatedetailsbyid'=>'tocandidatedetailsbyid','togetmoduleuserroleslist'=>'togetmoduleuserroleslist', 'togettaskgrpsdetails'=>'togettaskgrpsdetails', 'tgetcurrenttasklogdata'=>'tgetcurrenttasklogdata');
	if(isset($mobilecheck_array[$mode])){
		//echo "RRRR"; exit;
		include("model/mobile_user.class.php");
		//echo "uuuu"; exit;
		$mobileObj = new mobileUserClass();
		$fun = $mobilecheck_array[$mode];
		$details = $mobileObj->$fun($_GET);
		echo json_encode($details);
	}
	$amc_account_class_array = array(
		'getamcaccountdetails' => 'getAmcAccountDetails',
		'getalldetails' => 'getAllDetails',
		'getproductdetails' => 'getProductDetails',
		'getallproductdetails' => 'getAllProductDetails',
		'getamcregistrationdetails' => 'getAmcRegistrationDetails',
		'getcustomerdetails' => 'getCustomerDetails',
		'createschedule' => 'createSchedule',
		'getservicescheduledetails' => 'getServiceScheduleDetails',
		'updateamcserviceschedule' => 'updateAmcServiceSchedule',
		'getamcservicedetails' => 'getAmcServiceDetails', 'togetuserfielddashboard'=>'togetuserfielddashboard', 'togetoprationlistbasedonmonthly'=>'togetoprationlistbasedonmonthly', 'getamcchecklistdetails' => 'getAmcCheckListDetails',
        'getqrgenerationdetails' => 'getqrgenerationdetails',
        // 'getamccomplaintsdetails' => 'getamccomplaintsdetails',
        // 'getamccomplaintslistdetails' => 'getamccomplaintslistdetails' ,
        'getstationeryrelateddata' => 'getstationeryrelateddata', 'getamcdashboarddata' => 'getamcdashboarddata', 'getamcschedulesbydate' => 'getamcschedulesbydate'
	);
	if (isset($amc_account_class_array[$mode])) {
		include("model/amc_account_details.class.php");
		$amcObj = new amcAccountDetailsClass();
		$fun = $amc_account_class_array[$mode];
		$details = $amcObj->$fun($_GET);
		echo json_encode($details);
	}
	$phpmysqlclass_array = array('getphpmysqldetails' => 'getPHPMYSQLDetails','getfieldlist'=>'getfieldlist');
	if (isset($phpmysqlclass_array[$mode])) {
		include("model/phpmysql.class.php");
		phpMysqlClass::requireSuperAdmin(); // RBAC: super-admin only, enforced server-side
		$phpmysqlObj=new phpMysqlClass();
		$fun = $phpmysqlclass_array[$mode];
		$details = $phpmysqlObj->$fun($_GET);
		echo json_encode($details);
	}

	$tally_array = array('tosenditem_categoriestotally' => 'tosenditem_categoriestotally', 'togetapiidbasemapdetail'=>'togetapiidbasemapdetail','getapimastersdetails' => 'getApiMastersDetails','getapihits'=>'getAPIHits','tocheckdynamictransactionall'=>'tocheckdynamictransactionall');
	if (isset($tally_array[$mode])) {
		include("model/tally.class.php");
		$tallyObj=new tallyClass();
		$fun = $tally_array[$mode];
		$details = $tallyObj->$fun($_GET);
		echo json_encode($details);
	}
	$csr_array = array(
    'insertData' => 'insertData',
    'getCompletedProjects' => 'getCompletedProjects',
    'gettracckingperentage' => 'getTracckingPerentage',
    'getcommittemembers' => 'getCommitteMembers',
    'getcsrdata' => 'getCsrData',
    'getapprovedmembers' => 'getApprovedMembers',
    'getcsrsetting' => 'getCsrSetting',
    'getCompaniesData' => 'getCompaniesData',
    'getMapPoints' => 'getMapPoints',
    'getFieldWorkDailyPlanData' => 'getFieldWorkDailyPlanData',
    'fetchFieldWorkDailyPlans' => 'fetchFieldWorkDailyPlans',
    'updateFieldWorkDailyPlans' => 'updateFieldWorkDailyPlans',
    'getApplicationUsersData' => 'getApplicationUsersData',
    'getFieldWorkUserMapPointsData' => 'getFieldWorkUserMapPointsData',
    'getUsersMapPointsData' => 'getUsersMapPointsData',
    'getEmployeeData' => 'getEmployeeData',
    'checkExistingData' => 'checkExistingData',
	'getCsrProjectTracking' => 'getCsrProjectTracking',
	'getcsrProposalData' => 'getcsrProposalData',
	'getAdminDashBordData' => 'getAdminDashBordData',
	'getApplicationUsersDataforInsert' => 'getApplicationUsersDataforInsert',
	'getCSRHistory' => 'getCSRHistory',
	'getCSRYearlyBudget' => 'getCSRYearlyBudget',
	'getCSRContractors' => 'getCSRContractors',
	'getAllCSRData' => 'getAllCSRData',
	'updateCSRTableData' => 'updateCSRTableData',
	'getUsersData' => 'getUsersData',
	'getCSRRelatedData' => 'getCSRRelatedData',
	'getSingleTableData' => 'getSingleTableData',
	'getCsrViewRelatedData' => 'getCsrViewRelatedData',
	'getCsrInvoiceDetails' => 'getCsrInvoiceDetails',
	'getCsrMasterData' => 'getCsrMasterData',
	'getCsrProjectSummaryReport' => 'getCsrProjectSummaryReport',
	'getCsrPoVsActualsSummary' => 'getCsrPoVsActualsSummary',
	'getCsrUnspentCalculationReport' => 'getCsrUnspentCalculationReport',
	'getCsrSummaryReport' => 'getCsrSummaryReport',
	'getVerticalsData' => 'getVerticalsData',
	'getCsrYearlySummaryReport' => 'getCsrYearlySummaryReport',
	'getCSRProjects' => 'getCSRProjects',
	'getCsrFirstDashboardData' => 'getCsrFirstDashboardData',
	'getCsrProjectMonitoringDashboardData' => 'getCsrProjectMonitoringDashboardData',
	'getCsrImpactSnapshotData' => 'getCsrImpactSnapshotData',
	'getCsrDashboardPopupProjects' => 'getCsrDashboardPopupProjects',
	'getCsrApprovedProjectsForDocuments' => 'getCsrApprovedProjectsForDocuments',
	'getCsrProjectDocuments' => 'getCsrProjectDocuments',
	'getCsrProjectTrackingManage' => 'getCsrProjectTrackingManage',
	'getCsrProposalProjectDates' => 'getCsrProposalProjectDates',
	'togetdailyoperationsdashboard' => 'togetdailyoperationsdashboard',
	'getCsrObligationDashboardData' => 'getCsrObligationDashboardData',
	'getfieldworkoperationsreport' => 'getFieldWorkOperationsReport',
	'getempexpensesreport'=>'getEmpExpensesReport'
);
	if (isset($csr_array[$mode])) {
		include("model/csr.class.php");
		$csrObj = new CSRClass();
		$fun = $csr_array[$mode];
		$details = $csrObj->$fun($_GET);
		echo json_encode($details);
	}

// $stocktakecheck_array = array('getstocktakes'=>'getStockTakes','getstocktakesuom'=>'getstocktakesuom','getstocktakeadjustments'=>'getStockTakeAdjustments','startstocktakes'=>'startstocktakes','getstocktakelocations'=>'getStockTakeLocations');
//  	if(isset($stocktakecheck_array[$mode])){
//  		//echo "api-".$mode; exit;
// 		$fun = $stocktakecheck_array[$mode];
// 		//echo $fun; exit;
// 		$details = $stocktakeObj->$fun($_GET);
// 		echo json_encode($details);
// 	}

$complaints_array = array(
    'getAllComplaintsData' => 'getAllComplaintsData',
    'gettableData' => 'getTableData',
    'getComplaintsData' => 'getComplaintsData',
    'getImagesData' => 'getImagesData',
    'getMobileUsersData' => 'getMobileUsersData',
    'getMobileRegistredUsersData' => 'getMobileRegistredUsersData',
    'getComplaintsResponseData' => 'getComplaintsResponseData',
    'getAllLocationsData' => 'getAllLocationsData',
);
if (isset($complaints_array[$mode])) {
    include("model/complaints.class.php");
    $ComplaintsObj = new ComplaintsClass();
    $fun = $complaints_array[$mode];
    $details = $ComplaintsObj->$fun($_GET);
    echo json_encode($details);
}
$react_post = array('dynamicdeletebyridreact'=>'dynamicdeletebyridreact', 'dynamiclistatreact'=>'dynamiclistatreact', 'dyanamicautosuggictionselectdata'=>'dyanamicautosuggictionselectdata','modulelistreact'=>'modulelistreact','getreactmenulist'=>'getReactMenuList','getcontractslist'=>'getContractsList','geteditcontracts'=>'getEditContracts', 'applicationpagenamedetails'=>'applicationpagenamedetails','dynamicdeletebyridreact'=>'dynamicdeletebyridreact','dynamiclistatreact'=>'dynamiclistatreact','dyanamicautosuggictionselectdata'=>'dyanamicautosuggictionselectdata','modulelistreact'=>'modulelistreact','getreactmenulist'=>'getReactMenuList','getcontractslist'=>'getContractsList','geteditcontracts'=>'getEditContracts','getdocumentlistreact'=>'getdocumentlistreact','getuserslistreact'=>'getuserslistreact','getEditworkflowreact'=>'getEditworkflowreact','getAssociatedcontract'=>'getAssociatedcontract','getEditpermissionreact'=>'getEditpermissionreact','getListworkflowactivityreact'=>'getListworkflowactivityreact','getactivityreact'=>'getactivityreact','getcustomerlist'=>'getcustomerlist','getvendorslist'=>'getvendorslist','getcustomerdetailsreact'=>'getcustomerdetailsreact','getpermissiondetailsreact'=>'getpermissiondetailsreact','getdeleterowreact'=>'getdeleterowreact','getfullpermissiondetailsreact'=>'getfullpermissiondetailsreact','getdashboardreact'=>'getdashboardreact','getEditdocumnentreact'=>'getEditdocumnentreact','getDocumentDetailsReact'=>'getDocumentDetailsReact', 'getcontractdetailsreact' => 'getcontractdetailsreact','getdepartmentslist' => 'getdepartmentslist', 'getcontracttypeslist' => 'getcontracttypeslist','sendcronjobcontractexpirynotifications'=>'sendCronJobContractExpiryNotifications',
	// added by koti start
	'getcontractslistamc' => 'getContractsListAmc',
	'getcustomercontracts' => 'getCustomerContracts',
	'getvendorcontracts' => 'getVendorContracts',
	'getcontractamcscheduledrilldown' => 'getContractAmcScheduleDrilldown',
	'getcontractamctechnicianassignments' => 'getContractAmcTechnicianAssignments',
	'getcontractamcchecklist' => 'getContractAmcChecklist',
	'getcontractamcfmdashboarddata' => 'getContractAmcFmDashboardData',
	'getcontractamcfmconfirmdetails' => 'getContractAmcFmConfirmDetails',
	'getcontractamcscheduledetails' => 'getContractAmcScheduleDetails',
	'getamcassetsbylocationcategory' => 'getAmcAssetsByLocationCategory',
	'getcontractamcdashboarddata' => 'getContractAmcDashboardData',
	'getamcportalhomedata' => 'getAmcPortalHomeData',
	'runcontractamcremindercron' => 'runContractAmcReminderCron',
	'runcontractamcescalationcron' => 'runContractAmcEscalationCron',
	'getamccontractassetschedules' => 'getAmcContractAssetSchedules',
	'getamccontractassetcalendarevents' => 'getAmcContractAssetCalendarEvents',
	'getppminhousereportdata' => 'getPpmInhouseReportData',
	'getvendoramctrackerreportdata' => 'getVendorAmcTrackerReportData',
	'getvendoramcschedulereportdata' => 'getVendorAmcScheduleReportData',
	'getamcrepairorderformdata' => 'getAmcRepairOrderFormData',
	'getamcrepairorderslist' => 'getAmcRepairOrdersList',
	'getamccontractfromassetlocation' => 'resolveContractFromAssetLocationApi',
	'getamccontractschedulesforasset' => 'getAmcContractSchedulesForAsset',
	'getamcrepairordercontractassets' => 'getAmcRepairOrderContractAssets',
	'getamccomplaintsdetails' => 'getAmcComplaintsDetails',
	'getamccomplaintslistdetails' => 'getAmcComplaintsListDetails',
	'getamccomplaintsreportdata' => 'getAmcComplaintsReportData',
	// added by koti end
	'getcontractdetailsreactamc'=>'getcontractdetailsreactamc',
	'getusercontracttypelist'=>'getusercontracttypelist',
	// --- AMC Admin Landing Dashboard  ---
	'getamcadminlandingdashboarddata' => 'getAmcAdminLandingDashboardData',
	'getamcadminlandingcontractspopup' => 'getAmcAdminLandingContractsPopupList',
	'getamcadminlandingvisitslist' => 'getAmcAdminLandingVisitsList',
	'generatecontractsexceltemplate'=>'generateContractsExcelTemplate',
	'getcontractsexcelbatch'=>'getContractsExcelBatch',
	'getuserassignedcontracttypeslist'=>'getuserassignedcontracttypeslist',
	'getuserassignedcontracttypesformdata'=>'getuserassignedcontracttypesformdata'

	);
	if(isset($react_post[$mode])) {
		include("model/react.class.php");
		//echo "----------Check-----"; exit;
		$reactObj=new reactClass();
		$fun = $react_post[$mode];
		$details = $reactObj->$fun($_GET);
		echo json_encode($details);
	}
	$check_array = array('getcrmleads'=>'getCRMLeads','setstatecity'=>'setStateCity','getlistcontactleaddata' => 'getlistcontactleaddata','getleadcontactsourcetype' => 'getleadcontactsourcetype','getFeasiblityData' => 'getFeasiblityData', 'getFeasibilityFields' => 'getFeasibilityFields','getFeasibilityReport' => 'getFeasibilityReport','checkBOMReturns'  => 'checkBOMReturns','closeFeasibility' => 'closeFeasibility','getFeasibilityDashboard' => 'getFeasibilityDashboard',	'updateReturnDate' => 'updateReturnDate','createsalesorderswithfeasibilty'=>'createSalesOrdersWithFeasibilty', 'getFeasibilityTableAndFields' => 'getFeasibilityTableAndFields', 'reOpenFeasibility' => 'reOpenFeasibility', 'compareDatabaseSchemas' => 'compareDatabaseSchemas');
	if(isset($check_array[$mode])){
		include("model/crm_user.class.php");
		$crmuserObj=new crmuserClass();
		$fun = $check_array[$mode];
		$details = $crmuserObj->$fun($_GET);
		echo json_encode($details);
	}

	$onbording_emp_class_array =  array('getemdmindashboard'=>'getEMAminDashboard','getemtaskassignelist'=>'getEMTaskAssigneList','getemtasksendforapprove'=>'getEMTaskSendForApprove');
	if(isset($onbording_emp_class_array[$mode])){
		include("model/employee_management_user.class.php");
		$emObj=new employeeManagementUserClass();
		$fun = $onbording_emp_class_array[$mode];
		$details = $emObj->$fun($_GET);
		echo json_encode($details);
	}
	$security_arr = array('getVisitorsReportData' => 'getVisitorsReportData', 'getVisitorsDashboardData' => 'getVisitorsDashboardData', 'getVisitorsDataBasedOnDate' => 'getVisitorsDataBasedOnDate', 'getVisitorPassDetails' => 'getVisitorPassDetails', 'getVisitorInsertRelatedData' => 'getVisitorInsertRelatedData', 'getEmployeePassDetails' => 'getEmployeePassDetails', 'getVisitorsByPhoneAndEmpCode' => 'getVisitorsByPhoneAndEmpCode', 'getVisitorsSearch' => 'getVisitorsSearch', 'getVisitorsList' => 'getVisitorsList','getvisitormaterialgatepassdata'  => 'getvisitormaterialgatepassdata','getvisitorworkpermitdata'=> 'getvisitorworkpermitdata','getVisitorDetails' => 'getVisitorDetails','getReferenceTransactions' => 'getReferenceTransactions','getHistoricalVisitorProfileSearch' => 'getHistoricalVisitorProfileSearch','getVisitorProfilesReportData' => 'getVisitorProfilesReportData', 'getVisitorProfileDetails' => 'getVisitorProfileDetails');
    if (isset($security_arr[$mode])) {
        include_once("model/security.class.php");
        $SecurityObj = new SecurityClass();
        $fun = $security_arr[$mode];
        $details = $SecurityObj->$fun($_GET);
        echo json_encode($details);
    }

    $report_user_class_arr = array('getdynamicdownloadpdfinvoices' => 'getDynamicDownloadPDFInvoices','togetlistamendedorders' => 'togetlistamendedorders','getsalessummaryreport' => 'getsalessummaryreport', 'getsalesregisterreport' => 'getsalesregisterreport', 'getordersalesreport' => 'getordersalesreport','togetstandardtransactiondetailsreports'=>'toGetStandardTransactionDetailsReports','togetstandarddaystockbalancereport'=>'toGetStandardDayStockBalanceReport','getprojecttasktimelogreport'=>'getProjectTaskTimeLogReport','getvisitorsmovementsreport' => 'getVisitorsMovementsReport','getvendorsmovementsreport' => 'getVendorsMovementsReport','getemployeetempidreport' => 'getEmployeeTempIdReport','getinwardcouriersreport' => 'getInwardCouriersReport','getoutwardcouriersreport' => 'getOutwardCouriersReport');
    if (isset($report_user_class_arr[$mode])) {
        include_once("model/report_user.class.php");
        $reportObj = new reportUserClass();
        $fun = $report_user_class_arr[$mode];
        $details = $reportObj->$fun($_GET);
        echo json_encode($details);
    }

    $manufacturing_arr = array('getmanufacturingboms' => 'getManufacturingBOMS','getmanufacturingworkorders'=>'getManufacturingWorkOrders');
    if (isset($manufacturing_arr[$mode])) {
        include_once("model/manufacturing.class.php");
        $manufacturingObj = new manufacturingUserClass();
        $fun = $manufacturing_arr[$mode];
        $details = $manufacturingObj->$fun($_GET);
        echo json_encode($details);
    }
    $manfacturing_forecast_arr = array(
        'getmanufacturingforecastedata' => 'getmanufacturingforecastedata',
        'getmanufacturingfirmordersdata' => 'getmanufacturingfirmordersdata',
        'getmfgdashboarddetails' => 'getmfgdashboarddetails',
        'getmfgadmindashboarddetails' => 'getmfgadmindashboarddetails',
        'getmfgplannerdashboarddetails' => 'getmfgplannerdashboarddetails',
        'getmfgsupervisordashboarddetails' => 'getmfgsupervisordashboarddetails',
        'getmanufacturingworkordersdetails' => 'getmanufacturingworkordersdetails',
        'getmanufacturingworkorderslogs' => 'getmanufacturingworkorderslogs',
        'getstockrequests' => 'getstockrequests',
        'getstocktransfers' => 'getstocktransfers',
        'getmanufacturingproductiondetails' => 'getManufacturingProductionDetails',
        'getmanufacturingworkorderqcdata' => 'getManufacturingWorkOrderQcData',
        'getmanufacturingproductionsummaryreport' => 'getManufacturingProductionSummaryReport',
        'getmanufacturingworkorderstatusreport' => 'getManufacturingWorkOrderStatusReport',
        'getmanufacturingmaterialrequirementconsumptionreport' => 'getManufacturingMaterialRequirementConsumptionReport',
        'getmanufacturingstockrequesttransferreceiptreport' => 'getManufacturingStockRequestTransferReceiptReport',
        'getmanufacturingproductionperformancereport' => 'getManufacturingProductionPerformanceReport',
        'getmanufacturingbomreport' => 'getManufacturingBomReport',
        'getmanufacturingqcsummaryrejectionreport' => 'getManufacturingQcSummaryRejectionReport',
        'getmanufacturingworkordertimelinedelayreport' => 'getManufacturingWorkOrderTimelineDelayReport',
    );
	if (isset($manfacturing_forecast_arr[$mode])) {
		include_once("model/manfacturing_forecast.class.php");
		$mfObj = new manfacturingForecastUserClass();
		$fun = $manfacturing_forecast_arr[$mode];
		$details = $mfObj->$fun($_GET);
		echo json_encode($details);
	}
	$manufacturing_process_qc_arr = array(
        'getprocessqcprocesses' => 'getprocessqcprocesses',
        'getprocessqctransactions' => 'getprocessqctransactions',
        'getprocessqcmapping' => 'getprocessqcmapping',
        'getprocessqcworkspace' => 'getprocessqcworkspace',
        'getprocessqcitemform' => 'getprocessqcitemform',
        'getprocessqcsummary' => 'getprocessqcsummary',
    );
    if (isset($manufacturing_process_qc_arr[$mode])) {
        include_once("model/integartion.class.php");
        $intiObj = new integartionClass();
        $fun = $manufacturing_process_qc_arr[$mode];
        $details = $intiObj->$fun($_GET);
        echo json_encode($details);
    }

	$web_view_arr = array('dynamicdatashowbasedontable' => 'dynamicdatashowbasedontable','getdynamicinfodata' => 'getdynamicinfodata', 'getRegularizationRequestList' => 'getRegularizationRequestList', 'togetRegularizationRequestById' => 'togetRegularizationRequestById', 'toSaveDataEmployeeDailyAttendanceByRequestById' => 'toSaveDataEmployeeDailyAttendanceByRequestById', 'togetTransportPersonalGatewayData' => 'togetTransportPersonalGatewayData', 'togetTransportVehicleGatePassData' => 'togetTransportVehicleGatePassData', 'togetEmployeeLeaveData' => 'togetEmployeeLeaveData', 'togetStationeryRequestWebview' => 'togetStationeryRequestWebview',
		'togetAssetRequestWebview' => 'togetAssetRequestWebview', 'togetWorkFromHomeRequestWebview' => 'togetWorkFromHomeRequestWebview', 'getApprovalDiscussionMessages' => 'getApprovalDiscussionMessages');
	if (isset($web_view_arr[$mode])) {
		include_once("model/web_view.class.php");
		$wvObj = new webViewClass();
		$fun = $web_view_arr[$mode];
		$details = $wvObj->$fun($_GET);
		echo json_encode($details);
	}
	$emp_loan_arr = array('getloanemployeesdata' => 'getloanemployeesdata','getscannerqrdetails' => 'getscannerqrdetails',
	'gettableandprocessdetails' => 'gettableandprocessdetails',
	'gettablefields' => 'gettablefields', 'getfulltabledetails'=>'getfulltabledetails','gettransportgatewaydetails' => 'gettransportgatewaydetails', 'lookup_vehicle_details' => 'lookupVehicleDetails', 'getvehiclegateway' => 'getvehiclegateway','getrecentvehicleactivity' => 'getrecentvehicleactivity','getvehiclegatewaylist' => 'getVehicleGatewayList',
'getfuelentrydata' => 'getfuelentrydata','getdepartmentdetails' => 'getdepartmentdetails',
'getdepartmentemployeesdetails' => 'getdepartmentemployeesdetails',
'getfuelstatistics' => 'getfuelstatistics',
'gettodayfuelsummary' => 'gettodayfuelsummary',
'getfueldetails' => 'getfueldetails',
'getfuelentrylist' => 'getfuelentrylist',
'getpersonalgatewaydetails' => 'getpersonalgatewaydetails',
'gettransportvehiclemaster' => 'gettransportvehiclemaster',
		'getpersonalgatewaylist' => 'getpersonalgatewaylist',
		'gettransportvehiclemasterlist' => 'gettransportvehiclemasterlist',		
		'getpersonalgateway' => 'getpersonalgateway',
		'getrecentpersonalactivity' => 'getrecentpersonalactivity',
		'lookup_vehicle_details' => 'lookupVehicleDetails',
		'getvehiclegatewaylist' => 'getVehicleGatewayList', 
		'gettransportvehiclemasterdetails' => 'gettransportvehiclemasterdetails',
	'getEmployeesAttendanceData' => 'getEmployeesAttendanceData',
'togetalldetailsforemployeevalattendance' => 'togetalldetailsforemployeevalattendance',
 'getAutoAttendanceTime' => 'getAutoAttendanceTime', 'getEmployeeAttendanceLocationDetails' => 'getEmployeeAttendanceLocationDetails', 'getLeaveEmployees' => 'getLeaveEmployees', 'getLeaveBalanceAndAllocations' => 'getLeaveBalanceAndAllocations',
'getvehiclegatewaypending' => 'getvehiclegatewaypending', 'getvehiclegatewaymissing' => 'getvehiclegatewaymissing',  'updateAttendanceKey' => 'updateAttendanceKey', 'gettransportgatewayreport' => 'gettransportgatewayreport', 'getEmployeeBuddyAuthorizers' => 'getEmployeeBuddyAuthorizers');
	if (isset($emp_loan_arr[$mode])) {
		include_once("model/employee_loan.user.class.php");
		$elObj = new employeeLoanClass();
		$fun = $emp_loan_arr[$mode];
		$details = $elObj->$fun($_GET);
		echo json_encode($details);
	}
	$uniform_arr = array('getUniformStockData' => 'getUniformStockData', 'getUniformItemsForTopup' => 'getUniformItemsForTopup', 'loadUniformItemsByLocation' => 'loadUniformItemsByLocation', 'checkUniformCombination' => 'checkUniformCombination', 'getEmployeeUniformData' => 'getEmployeeUniformData', 'getUniformDashboardData' => 'getUniformDashboardData', 'getUniformRequestItems' => 'getUniformRequestItems','getStationeryRequestView' => 'getStationeryRequestView', 'getStationeryRequestList' => 'getStationeryRequestList','deleteStationeryRequest' => 'deleteStationeryRequest');
	if (isset($uniform_arr[$mode])) {
		include_once("model/uniform.user.class.php");
		$elObj = new uniformClass();
		$fun = $uniform_arr[$mode];
		$details = $elObj->$fun($_GET);
		echo json_encode($details);
	}
	$vms_document_array = array(
    'getdocumentmanagementdata'          => 'getDocumentManagementData',
    'getdocumentmanagementlist'          => 'getDocumentManagementList',
    'getdocumentmanagementdashboard'     => 'getDocumentManagementDashboard',
    'getdocumentmanagementuserdashboard' => 'getDocumentManagementUserDashboard',
	);
	if (isset($vms_document_array[$mode])) {
	    include_once("model/vms_document.user.class.php");
	    $vmsDocObj = new VmsDocumentClass();
	    $fun = $vms_document_array[$mode];
	    $details = $vmsDocObj->$fun($_GET);
	    echo json_encode($details);
	}
	$purchasing_arr = array('getsubcontractworkorders' => 'getSubcontractWorkOrders', 'getissuerawmaterialstovendor' => 'getIssueRawMaterialsToVendor', 'getsubcontractworkorderissues' => 'getSubContractWorkOrderIssues', 'getsubcontractworkorderreceipts' => 'getSubcontractWorkOrderReceipts', 'getsubcontractworkorderreceipthistory' => 'getSubcontractWorkOrderReceiptHistory');
    if (isset($purchasing_arr[$mode])) {
        include_once("model/purchasing_user.class.php");
        $purchObj = new purchasingUserClass();
        $fun = $purchasing_arr[$mode];
        $details = $purchObj->$fun($_GET);
        echo json_encode($details);
    }
    $atten_arr = array('getEmployeeDailyAttendanceSummary' => 'getEmployeeDailyAttendanceSummary', 'toCallPullEmployeeAttendance'=>'toCallPullEmployeeAttendance', 'getBiometricMachineLastUsedReport' => 'getBiometricMachineLastUsedReport', 'fixWrongInPullEmployeeAttendance' => 'fixWrongInPullEmployeeAttendance', 'updateAttendanceDataoOfThreeBiometricMachines' => 'updateAttendanceDataoOfThreeBiometricMachines', 
    	'torunnewattendancecodes'=>'torunnewattendancecodes');
    if (isset($atten_arr[$mode])) {
        //echo "reaa"; exit;
        include_once("model/attendance_user.class.php");
        $attObj = new attendanceUserClass();
        $fun = $atten_arr[$mode];
        $details = $attObj->$fun($_GET);
        echo json_encode($details);
    }
    $dashboard_arr = array('togetHRAdminDashboardData'=>'togetHRAdminDashboardData','getEmployeeSelfDashboard'=>'getEmployeeSelfDashboard', 'getPayrollDashboardSummary'=>'getPayrollDashboardSummary');
    if (isset($dashboard_arr[$mode])) {
        include_once("model/dashboard.user.class.php");
        $dashObj = new dashboardUserClass();
        $fun = $dashboard_arr[$mode];
        $details = $dashObj->$fun($_GET);
        echo json_encode($details);
    }
    $survey_poll_get_arr = array(
    'getsurveydetails' => 'getsurveydetails',
    'getsharedsurveydetails' => 'getsharedsurveydetails',
    'getsurveysummary' => 'getsurveysummary',
    'getsurveylist' => 'getsurveylist',
    'getsurveyresult' => 'getsurveyresult',
    'getsurveyreport' => 'getsurveyreport',
    'getempsurvey' => 'getempsurvey',
    'getsurveyadmindashboard' => 'getsurveyadmindashboard',
    'getemployeesurveydashboard' => 'getemployeesurveydashboard',
    'totestcodems'=>'totestcodems'
	);
	if (isset($survey_poll_get_arr[$mode])) {
	    include_once("model/survey_poll.user.class.php");
	    $surveyPollObj = new surveyPollUserClass();
	    $fun = $survey_poll_get_arr[$mode];
	    $details = $surveyPollObj->$fun($_GET);
	    echo json_encode($details);
	}

    $api_arr = array('aijobtitlecandidates'=>'aijobtitlecandidates');
    if (isset($api_arr[$mode])) {
        include_once("model/aicall.user.class.php");
        $aiObj = new aicallUserClass();
        $fun = $api_arr[$mode];
        $details = $aiObj->$fun($_GET);
        echo json_encode($details);
    }
    //omkar added code 

	if ($mode == 'approveVisitor') {
		$rid    = (int)($_GET['rid'] ?? 0);
		$status = $_GET['status'] ?? '';
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$res = $SecurityObj->approveVisitorByHost($rid, $status);
		echo json_encode($res);
		exit;
	}

	if ($mode == 'checkVisitorApproval') {
		$rid = (int)($_GET['rid'] ?? 0);
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$row = $SecurityObj->getVisitorStatus($rid);
		echo json_encode($row);
		exit;
	}

	if ($mode == 'manualConfirmVisitor') {
		$rid = (int)($_GET['rid'] ?? $_POST['rid'] ?? 0);
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$res = $SecurityObj->manualConfirm($rid);
		echo json_encode($res);
		exit;
	}

	if ($mode == 'resendApprovalEmail') {
		$rid = (int)($_GET['rid'] ?? $_POST['rid'] ?? 0);
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$res = $SecurityObj->resendApprovalEmail($rid);
		echo json_encode($res);
		exit;
	}

	if ($mode == 'finalizeVisitorCheckin') {
		$rid         = (int)($_GET['rid']         ?? $_POST['rid']         ?? 0);
		$gate_id     = (int)($_GET['gate_id']     ?? $_POST['gate_id']     ?? 0);
		$company_id  = (int)($_GET['company_id']  ?? $_POST['company_id']  ?? 0);
		$user_id     = (int)($_GET['user_id']     ?? $_POST['user_id']     ?? 0);
		$location_id = (int)($_GET['location_id'] ?? $_POST['location_id'] ?? 1);
		include_once("model/security.class.php");
		$SecurityObj = new SecurityClass();
		$res = $SecurityObj->finalizeCheckin($rid, $gate_id, $company_id, $user_id, $location_id);
		echo json_encode($res);
		exit;
	}
    //omkar added code
	$api_arr = array('aitogetrecruitmentdata'=>'aiToGetRecruitmentData','getuserconfiguration'=>'getUserConfiguration');
    if (isset($api_arr[$mode])) {
        include_once("model/aicall.user.class.php");
        $aiObj = new aicallUserClass();
        $fun = $api_arr[$mode];
        $details = $aiObj->$fun($_GET);
        echo json_encode($details);
    }

}// End GET Method    