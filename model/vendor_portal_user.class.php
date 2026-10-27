<?php 
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;	
class vendorPortelUserClass extends userClass{
	function getApplicationTickets($get){
		global $callConfig;
		if($get['page_type'] == "LT"){
			$application_ticketssql = "SELECT id, transaction_number, originator, resolving_user_id, issue_name, issue_description, priority, attachment, request_datetime, response_datetime, completion_datetime, current_status FROM application_tickets WHERE company_id = '".$get['company_id']."' AND originator = '".$get['user_id']."' AND  is_active =0";
			$application_ticketsres=$callConfig->getAllRows($application_ticketssql);
			$return['application_tickets'] = $application_ticketsres;
		}elseif($get['page_type'] == "PT"){
			$application_ticketssql = "SELECT id, transaction_number, originator, resolving_user_id, issue_name, issue_description, priority, attachment, request_datetime, response_datetime, completion_datetime, current_status FROM application_tickets WHERE company_id = '".$get['company_id']."' AND resolving_user_id = '".$get['user_id']."' AND  is_active =0";
			$application_ticketsres=$callConfig->getAllRows($application_ticketssql);
			$return['application_tickets'] = $application_ticketsres;
		}
		$prioritysql = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '".$get['company_id']."' AND genc_code = 'PRIORITY' AND  is_active =0";
		$priorityres=$callConfig->getAllRows($prioritysql);
		$priority = array();
		foreach($priorityres as $pt){
			$priority[$pt->genc_detail_code] = $pt->genc_detail_name;
		}
		$return['priority'] = $priority;
		return $return;
	}
	function insertapplicationtickets($get,$post){
		global $callConfig;
		$return['post']= $post;
		$transaction_number = $this->toGetApplicationSerialNumber('application_tickets');
		$adminsql = "SELECT id, display_name FROM `application_users` WHERE company_id = '".$get['company_id']."' AND JSON_SEARCH(role_id, 'one', '".ADMIN_ROLE."') IS NOT NULL AND  is_active =0";
		$adminres=$callConfig->getRow($adminsql);
		$resolving_user_id = $adminres->id;

		$insertary=array('company_id'=>$post['company_id'],'transaction_number'=>$transaction_number,'module_setup_id'=>$callConfig->module_id,'originator'=>$post['user_id'],'resolving_user_id'=>$resolving_user_id,'issue_name'=>$post['issue_name'],'issue_description'=>$post['issue_description'],'priority'=>$post['priority'],'attachment'=>$post['attachment'],'request_datetime'=>date('Y-m-d h:i:s'),'is_active'=>'0','current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$res = $callConfig->insertRecord('application_tickets',$insertary);

		$usersql = "SELECT id, display_name, user_email FROM `application_users` WHERE company_id = '".$get['company_id']."' AND id = '".$get['user_id']."' AND  is_active =0";
		$userres=$callConfig->getRow($usersql);	

		$emailarray['user_email_id'] = $userres->user_email;
		$emailarray['user_display_name'] = $userres->display_name;
		$emailarray['to_company_id'] = $post['company_id'];
		$emailarray['session_company_id'] = $post['company_id'];
		$emailarray['created_by'] = $post['user_id'];
		$emailarray['issue_name'] = $post['issue_name'];
		$emailarray['transaction_number'] = $transaction_number;

		$msg = "Ticket Submited For Your Review - ".$userres->display_name;
		$user_ids  = array($adminres->id);
		$option = "";
		$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$res['LASTID'],'application_table_name'=>'application_tickets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option,'assigner_id'=>$post['user_id']);
		$emails = $this->sendNotificationAndEmails($notdataa,$emailarray,59);

		$notdataa['type'] = "Ticket Submited To - ".$adminres->display_name;
		$notdataa['type'] = "INFORMATION";
		$notdataa['notification_users'] = array($post['user_id']);
		$emails = $this->tosendnotifiations($notdataa);
		return $res;
	}
	function updateApplicationTickets($get,$post){
		global $callConfig;
		$updateary=array('current_status'=>$post['status'],'updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"));
		if($post['status'] == '1'){
			$updateary['response_datetime'] = date('Y-m-d h:i:s');	
		}elseif($post['status'] == ST_CANCEL){
			$updateary['cancel_datetime'] = date('Y-m-d h:i:s');	
		}elseif($post['status'] == ST_APPROVED){
			$updateary['completion_datetime'] = date('Y-m-d h:i:s');	
		}
		//$res = $callConfig->updateRecord('application_users',$updateary,'id',$appuserre->id);
		return $res;
	}
	function vendorapicallforvmslist($get){
		global $callConfig;
		$return['get'] = $get;
		$query = "SELECT * From vendors WHERE vendor_import = 1 AND is_active =0";
		$data = $callConfig->getAllRows($query);
		$return['details'] = $data;
		//update query 
		$query = "UPDATE vendors SET vendor_import_date = '".date('Y-m-d h:i:s')."'  WHERE vendor_import = 1 AND is_active =0";
		$daa = $callConfig->executeQuery($query);
		return $return;
	}
	function vmsvendorupdatetoapi($get){
		global $callConfig;
		$return['get'] = $get;
		if(isset($get['upload_ids']) && count((array)$get['upload_ids']) > 0){	
			if(isset($get['delete_mode']) && $get['delete_mode'] =='Y'){
				$upquery = "DELETE FROM `vendors` WHERE  id IN (".$get['upload_ids'].")";
				$return['data'] = $callConfig->executeQuery($upquery);
			}else{
				$exp = explode(",",$get['upload_ids']);
				$jsdata = json_encode($get);
				foreach($exp as $ex){
					$new_id = 1;
					$upquery = "UPDATE `vendors` SET vendor_import =0, 	vendor_imported_date= '".date('Y-m-d h:i:s')."', new_ref_id= ".$new_id.", vendor_imported_details = '".$jsdata."', is_active = 1 WHERE id IN (".$ex.")";
					$res[] = $callConfig->executeQuery($upquery);
				}
				$return['data'] = $res;
			}
		}		
		return $return;
	}
	function getVendorRegistrationRequests($get){
		global $callConfig;
		if($get['viewpage'] == "popup"){
			
		}elseif($get['rid'] !=""){
			$vendor_registration_requestssql = "SELECT * FROM vendor_registration_requests WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active = 0";
			$vendor_registration_requestsres = $callConfig->getRow($vendor_registration_requestssql);
			$return['vendor_registration_requests'] =$vendor_registration_requestsres;
		}else{
			$vendor_registration_requestssql = "SELECT * FROM vendor_registration_requests WHERE company_id = '".$get['company_id']."'  AND (`originator` = '".$get['user_id']."' OR ('".$get['admin_type']."'='1' AND  `share_pdf` = 'Y')) AND is_active = 0";
			$vendor_registration_requestsres = $callConfig->getAllRows($vendor_registration_requestssql);
			$return['vendor_registration_requests'] =$vendor_registration_requestsres;
		}
		$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND JSON_SEARCH(role_id, 'one', '".VENDOR_LEGAL_ROLE."') IS NOT NULL ";
		//(JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR 
		$application_usersres = $callConfig->getAllRows($application_userssql);
		foreach($application_usersres as $aus){
			$vendor_legal_application_users[$aus->id] = $aus;
		}
		$return['vendor_legal_application_users'] = $vendor_legal_application_users;
		
		$countriessql = "SELECT id, country_name, time_zones FROM countries WHERE company_id = '".$get['company_id']."' AND is_active='0'";
		$countriesres = $callConfig->getAllRows($countriessql);
		foreach($countriesres as $cuntry){
			$countries[$cuntry->id] = $cuntry;
		}
		$return['countries'] = $countries;


    	if($get['country_id'] !=""){
    		$country_id = $get['country_id'];
    	}elseif($get['country_id'] !=""){
    		$country_id = $vendor_registration_requestsres->country_id;
    	}else{
    		$country_id = 101;
    	}
    	$statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND country_id = '".$country_id."' AND is_active=0";
    	$statesres = $callConfig->getAllRows($statessql);
    	foreach($statesres as $states1){
			$states[$states1->id] = $states1;
		}
		$return['states'] = $states;
    	$return['statessql'] = $statessql;

    	$configsql = "SELECT * FROM `config` WHERE company_id = '".$get['company_id']."' AND is_active=0";
    	$configres = $callConfig->getRow($configsql);
    	$return['config'] = $configres;

    	$vendor_registerssql = "SELECT id, vendor_registration_request_id FROM `vendor_registers` WHERE company_id = '".$get['company_id']."' AND vendor_registration_request_id IS NOT NULL AND is_active=0";
    	$vendor_registerres = $callConfig->getAllRows($vendor_registerssql);
    	$return['vendor_registers'] = $vendor_registerres;

    	$vendor_typessql = "SELECT id, type_name FROM vendor_types WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $vendor_typesres = $callConfig->getAllRows($vendor_typessql);
        $return['vendor_types'] = $vendor_typesres;

		return $return;
	}
	 function generateVendorRegistartationRequestPDF($get){
        global $callConfig;
        $return = array();
        $transaction_id = $get['transaction_id'];
        $transaction_table = $get['transaction_table'];
        $get['rid'] = $transaction_id;
        $postarray = $this->getVendorRegistrationRequests($get);
        //$postarray['callapi'] = 'Y';
        $api_url = SITEURL.'views/custom_insert_vendor_registration_request_popup.php';
        $postdata = http_build_query($postarray);
        $opts = array('http' =>array(
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $content = file_get_contents($api_url, false, $context);
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->set_option('margin-top', '10mm');
        $dompdf->set_option('margin-right', '10mm');
        $dompdf->set_option('margin-bottom', '10mm');
        $dompdf->set_option('margin-left', '10mm');
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = $transaction_table."_".$transaction_id."_".time().'.pdf';
        $sFilePath = SITEROOT.'/uploads/vendor/'.$filename;
        $output = $dompdf->output();
        file_put_contents($sFilePath, $output);  
        $return['pdf_filename'] = $filename;
        $updateary = array('request_datetime'=>date("Y-m-d H:i:s"),'share_pdf'=>'Y','pdf_name'=>$filename,'updated_date'=>date("Y-m-d H:i:s"));
        $callConfig->updateRecord($transaction_table,$updateary,'id',$transaction_id);
        return $filename;
    }
	function insertVendorRegistrationRequests($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post; 
		$display_name = $post['first_name']." ".$post['last_name'];
		$registration_questions =$post['registration_questions'];
		$registration_questionsss = json_encode($registration_questions);

		$insertary=array('company_id'=>$post['company_id'],'company_name'=>$post['company_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'user_email'=>strtolower($post['user_email']),'user_mobile'=>$post['user_mobile'],'legal_connect'=>$post['legal_connect'],'note'=>$post['note'],'registration_questions'=>$registration_questionsss,'web_site'=>$post['web_site'],'vendor_type_id'=>$post['vendor_type_id'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id']);
		if($post['rid'] !=""){
			$insertary['updated_by'] = $post['user_id'];	
			$insertary['updated_date'] = date('Y-m-d h:i:s');
			$res = $callConfig->updateRecord('vendor_registration_requests',$insertary,'id',$post['rid']);
			$vrid =  $post['rid'];
		}else{
			$insertary['originator'] = $post['user_id'];
			$insertary['is_active'] = 0;
			$insertary['current_status'] = 0;
			$insertary['created_by'] = $post['user_id'];	
			$insertary['created_date'] = date('Y-m-d h:i:s');
			$res = $callConfig->insertRecord('vendor_registration_requests',$insertary);	
			$vrid =  $res['LASTID'];
		}

		$return['vendor_registration_requests'] = $res; 
		if($post['submit_type'] == "SNE"){
			$usersql = "SELECT id, display_name, user_email FROM `application_users` WHERE company_id = '".$post['company_id']."' AND id = '".$post['legal_connect']."' AND  is_active =0";
			$userres=$callConfig->getRow($usersql);	
		

			$adminusersql = "SELECT id, display_name, user_email FROM `application_users` WHERE  company_id = '".$post['company_id']."' AND JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL AND  is_active =0";
			$adminuserres=$callConfig->getRow($adminusersql);	
			
			$createrusersql = "SELECT id, display_name, user_email FROM `application_users` WHERE company_id = '".$post['company_id']."' AND id = '".$post['user_id']."' AND  is_active =0";
			$createruserres=$callConfig->getRow($createrusersql);
			
			$get['transaction_id'] = $vrid;
         	$get['transaction_table'] = 'vendor_registration_requests';
        	$generatepdf = $this->generateVendorRegistartationRequestPDF($get);
            $attachment_file_path = SITEROOT."/uploads/vendor/".$generatepdf;
          	//$pdfContent = file_get_contents($attachment_file_path);
        	//$return['pdfContent'] = $generatepdf; 
			$emailarray['user_email_id'] = $userres->user_email;
			$emailarray['user_display_name'] = $userres->display_name;
			$emailarray['creater_display_name'] = $createruserres->display_name;
			$emailarray['to_company_id'] = $post['company_id'];
			$emailarray['session_company_id'] = $post['company_id'];
			$emailarray['created_by'] = $post['user_id'];
			$emailarray['vendor_registration_requests_company_name'] = $post['company_name'];

			$emailarray['attachment'] = "Y";
	        $emailarray['attachment_file_path'] = $attachment_file_path;
	        $return['emailarray'] = $emailarray;

			$msg = "New Vendor Registration Requests Submited From - ".$createruserres->display_name;
			$user_ids[]  = $userres->id;
			if($adminuserres->id !=""){
				$user_ids[]  = $adminres->id;
			}
			$option = "";
			//$notdataa['type'] = "INFORMATION";
			$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$res['LASTID'],'application_table_name'=>'vendor_registration_requests', 'type'=>'INFORMATION', 'notification_users'=>$user_ids,'option'=>$option,'assigner_id'=>$post['user_id']);
			$emails = $this->sendNotificationAndEmails($notdataa,$emailarray,60);
			$return['emails'] =$emails;
			$notdataa['notification_message'] = "New Vendor Registration Requests Successfully Submitted : ".$post['company_name'];
			$notdataa['option'] = "";
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_users'] = array($post['user_id']);
			$emails = $this->tosendnotifiations($notdataa);

		}
		return $return;
	}
	function updateSendVerificationProfileStatus($get,$post){
		global $callConfig;
		$return = array();
		$transaction_table = $post['transaction_table'];
		$transaction_id = $post['transaction_id'];
		//Start Profle Review
		$vendorsql = "SELECT id, display_name, user_id, hr_connect, finance_connect, purchase_connect, admin_connect, location_connect, project_connect, legal_connect FROM ".$transaction_table." WHERE company_id = '".$post['company_id']."' AND id = '".$transaction_id."' AND  is_active =0";
		$vendorres=$callConfig->getRow($vendorsql);	
		$user_ids = $verification_profile_users =  array();
		if($vendorres->hr_connect !=""){
			$user_ids[$vendorres->hr_connect] = $vendorres->hr_connect;
			$verification_profile_users[$vendorres->hr_connect] = array('complete'=>'N');
		}
		if($vendorres->finance_connect !=""){
			$user_ids[$vendorres->finance_connect] = $vendorres->finance_connect;
			$verification_profile_users[$vendorres->finance_connect]  = array('complete'=>'N');
		}
		if($vendorres->purchase_connect !=""){
			$user_ids[$vendorres->purchase_connect] = $vendorres->purchase_connect;
			$verification_profile_users[$vendorres->purchase_connect]  =array('complete'=>'N');
		}
		if($vendorres->admin_connect !=""){
			$user_ids[$vendorres->admin_connect] = $vendorres->admin_connect;
			$verification_profile_users[$vendorres->admin_connect]  =array('complete'=>'N');
		}
		if($vendorres->location_connect !=""){
			$user_ids[$vendorres->location_connect] = $vendorres->location_connect;
			$verification_profile_users[$vendorres->location_connect]  = array('complete'=>'N');
		}
		if($vendorres->project_connect !=""){
			$user_ids[$vendorres->project_connect] = $vendorres->project_connect;
			$verification_profile_users[$vendorres->project_connect]  = array('complete'=>'N');
		}
		if($vendorres->legal_connect !=""){
			$user_ids[$vendorres->legal_connect] = $vendorres->legal_connect;
			$verification_profile_users[$vendorres->legal_connect]  = array('complete'=>'N');
		}
		$msg = "Vendor Details Submited For Your Review - ".$vendorres->display_name;
		$option = "COM_INSERT_VENDORS";
		if($transaction_table == 'vendor_registers'){
			$option = "COM_INSERT_VENDOR_REGISTERS&VTYPE=VR";
		}
		$notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>$vendorres->id,'application_table_name'=>'vendor_registration_requests', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option,'assigner_id'=>$post['user_id']);
		$emails = $this->tosendnotifiations($notdataa);

	//Profile Verifications email Code Start	
		$impoduserid = implode(',',(array)$user_ids);
		$appruseridsql = "SELECT id, display_name, user_email FROM application_users WHERE company_id = '".$post['company_id']."' AND id IN(".$impoduserid.") AND  is_active =0";
		$appruseridres=$callConfig->getAllRows($appruseridsql);	
		foreach($appruseridres as $approvwer){ 
			$emailarray['approval_user_email_id'] = $approvwer->user_email;
			$emailarray['user_display_name'] = $approvwer->display_name;
			$emailarray['creater_display_name'] = $vendorres->display_name;
			$emailarray['to_company_id'] = $post['company_id'];
			$emailarray['session_company_id'] = $post['company_id'];
			$emailarray['created_by'] = $post['user_id'];
	        $return['emailarray'] = $emailarray;
	        $emptyaray = array();
			$emails = $this->sendNotificationAndEmails($emptyaray,$emailarray,62);
		}
	//Profile Verifications email Code End	

		$notdataa['notification_message'] = "You Are Profile Review Requests Successfully Submitted";
		$notdataa['option'] = "";
		$notdataa['type'] = "INFORMATION";
		$inforid = array(); 
		$inforid[$vendorres->user_id] = $vendorres->user_id;
		$inforid[$post['user_id']] = $post['user_id'];
		$notdataa['notification_users'] = $inforid;
		$emails = $this->tosendnotifiations($notdataa);

		$updateary=array('verification_profile_users'=>json_encode($verification_profile_users), 'send_verification_profile_status'=>'Y','updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"),'current_status'=>ST_INPROGRESS);
		$res = $callConfig->updateRecord($transaction_table,$updateary,'id',$transaction_id);  
		//End Profle Review
		//Start All task Sedn to Approve
		$tasquery = "SELECT id FROM `transaction_activity_schedule` WHERE company_id = '".$post['company_id']."' AND transaction_table = '".$transaction_table."' AND assigned_to = '".$post['user_id']."' AND transaction_id = '".$transaction_id."' AND current_status = '".ST_INPROGRESS."' AND is_active = 0";
		$tasres = $callConfig->getAllRows($tasquery);
		$i = 0;
		foreach($tasres as $tas){
			$post['transaction_table'] = 'transaction_activity_schedule';
			$post['current_status'] = ST_INPROGRESS;
			$post['transaction_no'] = $tas->id;
			$post['next_status'] = ST_APPROVED1;
			$post['comment'] = 'Please Approve';
			if($i != 0){
				$post['requester_send'] = "N";
			}
			$this->submitStatusDynamicWorkFlow($post);
			$i++;
		}
		//End All task Sedn to Approve
		return $res;
	}
	function updateVendorProfileUpdateStatus($get,$post){
		global $callConfig;
		$return = array();
		$transaction_table = $_POST['transaction_table'];
		$transaction_id = $_POST['transaction_id'];

		$vendorsql = "SELECT id, display_name, user_id, verification_profile_users FROM ".$transaction_table." WHERE company_id = '".$post['company_id']."' AND id = '".$transaction_id."' AND  is_active =0";
		$vendorres=$callConfig->getRow($vendorsql);	
		$verification_profile_users = json_decode($vendorres->verification_profile_users,1);
		$verification_profile_users[$post['user_id']]['complete'] = 'Y';
		$verification_profile_users[$post['user_id']]['status'] = $post['status'];
		$verification_profile_users[$post['user_id']]['updatedatetime'] = date("Y-m-d H:i:s");
		$verification_profile_users[$post['user_id']]['comment'] = $post['profile_review_comments'];
		$updateary=array('verification_profile_users'=>json_encode($verification_profile_users),'updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"));
		$res = $callConfig->updateRecord($transaction_table,$updateary,'id',$transaction_id);
		return $res;
	}
	function updateVendorVerifications($get,$post){
		global $callConfig;
		$return = array();
		$transaction_table = $_POST['transaction_table'];
		$transaction_id = $_POST['transaction_id'];
		$verification_type = $_POST['verification_type'];
		$vendorsql = "SELECT id, display_name, user_id, vendor_verifications FROM ".$transaction_table." WHERE company_id = '".$post['company_id']."' AND id = '".$transaction_id."' AND  is_active =0";
		$return['vendorsql']= $vendorsql;
		$vendorres=$callConfig->getRow($vendorsql);
		$return['vendorres']= $vendorres;	
		$vendor_verification = json_decode($vendorres->vendor_verifications,1);
		$vendor_verification[$post['verification_type']]['status'] = 'Y';
		$vendor_verification[$post['verification_type']]['verification_user_id'] = $post['user_id'];
		$vendor_verification[$post['verification_type']]['verification_datetime'] = date("Y-m-d H:i:s");
		$vendor_verification[$post['verification_type']]['api_hit_id'] = $post['api_hit_id'];
		$updateary=array('vendor_verifications'=>json_encode($vendor_verification),'updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"));
		$res = $callConfig->updateRecord($transaction_table,$updateary,'id',$transaction_id);
		$return['res']= $res;
		return $return;
	}
	function checkOneLogin($get,$post){
		global $callConfig;
	   	$where="user_email='".$post['chrUserName']."'  and is_active ='0'";
	   	$query=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'*',$where,'','','');
	   	$row=$callConfig->getRow($query);
	   	if($row->id!="" && $row->email_active==1) {
		   	$whr=" id='".$row->zqr_connect."' and  is_active='0'";
		   	$query_db=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
			$row_db=$callConfig->getRow($query_db);
			$row->db = $row_db->db_name;
			if($post['onelogin'] == "Y"){
				$wheres ="user_email='".$post['chrUserName']."' and is_active ='0'";
			}else{
				$wheres ="user_email='".$post['chrUserName']."' and user_password='".$callConfig->passwordEncrypt($post['chrPassword'])."' and is_active ='0'";
			}
		   	$query_user=$callConfig->selectQuery($row_db->db_name.'.application_users','*',$wheres,'','','');
			$user = $callConfig->getRow($query_user);
		   	if($user->id != ''){
		   		$user->db = $row_db->db_name;
				$timm =date("Y-m-d H:i:s");
				$fieldnames=array('company_id'=>$user->company_id,'user_id'=>$user->id,'login_from'=>$timm,'login_ip'=>$post['ip'],'login_browser'=>$post['browser']);
				$query_last = "SELECT * FROM ".$row_db->db_name.".application_user_logins where user_id =".$user->id." ORDER BY id DESC LIMIT 0,1";
		   		$last = $callConfig->getRow($query_last);
		   		$user->lastlogin =$last->login_from;
		   		$user->reset_password = $row->reset_password;
		   		$res=$callConfig->insertRecord($row_db->db_name.'.application_user_logins',$fieldnames);
				unset($user->user_password);
				$return = $user;
		   	}else{
		   		$return ['error'] = '6002';
		   	}
	   	}else{
		   	if($row->email_active === '0') {
		   		$return ['error'] = '6004';
		   	}else{
		   		$return ['error'] = '6005';
		   	}
	   	}
	   return $return;		
	}
	function getVendorDisplayFields($get){
		global $callConfig;
		$return = array();
		if($get['Role'] != ""){
			$condition = " AND role_short_code = '".$get['Role']."'";
		}elseif($get['Rid'] != ""){
			$condition = " AND id = '".$get['Rid']."'";	
		}
		$vendor_display_fieldssql = "SELECT * FROM vendor_display_fields WHERE company_id = '".$get['company_id']."' ".$condition."  AND is_active = 0";
		$vendor_display_fieldsres=$callConfig->getRow($vendor_display_fieldssql);
		$return['vendor_display_fieldssql'] = $vendor_display_fieldssql;
		$return['vendor_display_fields'] = $vendor_display_fieldsres;

		$application_rolessql = "SELECT name, role_short_code FROM application_roles WHERE company_id = '".$get['company_id']."' AND application_module = 35 AND is_active = 0";
		$application_rolesres=$callConfig->getAllRows($application_rolessql);
		$return['application_roles'] = $application_rolesres;
		$return['sql'] = $application_rolessql;
		return $return;
	}
	function insertVendorDisplayFields($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;	
		$fielddata = json_encode($post['fieldgrpaccess']);
		$dashboard_field_list = json_encode($post['dashboard_field_list']);
		$return['post'] = $post;	
		if(isset($post['Rid']) AND $post['Rid'] !=""){
			$post_array= array("role_short_code"=>$post['role_short_code'],"field_list"=>$fielddata,"dashboard_field_list"=>$dashboard_field_list,"updated_by"=>$post['user_id'],"updated_date"=>date('Y-m-d H:i:s'));
			$res=$callConfig->updateRecord('vendor_display_fields',$post_array,'id',$post['Rid']);	
		}else{
			$post_array = array("company_id"=>$post['company_id'],"role_short_code"=>$post['role_short_code'],"field_list"=>$fielddata,"dashboard_field_list"=>$dashboard_field_list,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
			$res = $callConfig->insertRecord('vendor_display_fields',$post_array);	
		}
		$return['result'] = $res;
		return $return;
	}

	//omkar vdocument verification code added -----


	function insertupdatevendorverificationdetails($get, $post){
	    global $callConfig;
	    $return = array();
	    $transaction_table = $post['transaction_table'];
	    $transaction_id = $post['transaction_id'];
	    $verification_type = $post['verification_type'];
	    $now = date("Y-m-d H:i:s");
	    if ($transaction_id != "") {
	        $previoussql = "SELECT id, company_id, user_id, email, company, bank_account_no, ifsc_code, pan_no, gstin, tan_number, msme_number, vendor_verifications, previous_verifications FROM " . $transaction_table . " WHERE company_id = '" . $post['company_id'] . "' AND id = '" . $transaction_id . "' AND is_active = 0";
	        $previousres = $callConfig->getRow($previoussql);
	    }
	    $existingHistory = [];
	    if (!empty($previousres->previous_verifications)) {
	        $decoded = json_decode($previousres->previous_verifications, true);
	        if (is_array($decoded)) $existingHistory = $decoded;
	    }
	    $vendor_verification = [];
	    if (!empty($previousres->vendor_verifications)) {
	        $decoded_vendor = json_decode($previousres->vendor_verifications, true);
	        if (is_array($decoded_vendor)) $vendor_verification = $decoded_vendor;
	    }
	    if ($verification_type == "PAN") {
	        if (!empty($previousres->pan_no)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'pan_no', 'value' => $previousres->pan_no ];
	        }
	        $insertuserarray = ['company' => $post['company'], 'email' => $post['email'], 'company_id' => $get['company_id'], 'display_name' => $post['display_name'], 'pan_no' => $post['pan_no'], 'first_name' => $post['first_name'], 'middle_name' => $post['middle_name'], 'last_name' => $post['last_name'], 'previous_verifications' => $previousres->previous_verifications,'business_type' => $post['company_type']];
	        if (empty($transaction_id)) {

	        	$post['vendor_verifications'][$verification_type] = ['status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	            $insertuserarray['vendor_verifications'] = json_encode($post['vendor_verifications']);
	            $insertuserarray['is_active'] = 0;
	            $insertuserarray['current_Status'] = 0;
	            $insertuserarray['created_by'] = $post['user_id'];
	            $insertuserarray['created_date'] = $now;
	            $res = $callConfig->insertRecord($transaction_table, $insertuserarray);
	          //  $transaction_id = is_array($insert_id) && isset($insert_id['LASTID']) ? $insert_id['LASTID'] : $insert_id;
	            $return['LASTID'] = $res['LASTID'];
	        } else {
	            $vendor_verification[$verification_type] = ['status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	            $updateary = ['previous_verifications' => json_encode($existingHistory), 'vendor_verifications' => json_encode($vendor_verification), 'pan_no' => $post['pan_no'], 'updated_by' => $post['user_id'], 'updated_date' => $now,'business_type' => $post['company_type']];
	            $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	            $return['LASTID'] = $transaction_id;
	        }  
	        $return['res'] = $res;
	    } elseif ($verification_type == "MSME") {
	        if (!empty($previousres->msme_number)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'msme_number', 'value' => $previousres->msme_number ];
	        }
	        $vendor_verification[$verification_type] = ['status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	        $updateary = ['vendor_verifications' => json_encode($vendor_verification), 'previous_verifications' => json_encode($existingHistory), 'msme_register_yes_no' => "Y", 'msme_number' => $post['msme_number'], 'updated_by' => $post['user_id'], 'updated_date' => $now ];
	        $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	        $return['res'] = $res;
	    } elseif ($verification_type == "GSTIN") {
	        if (!empty($previousres->gstin)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'gstin', 'value' => $previousres->gstin ];
	        }
	        $vendor_verification[$verification_type] = [ 'status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	        $state = $post['state'];
	        $statesql = "SELECT id, state_name, country_id FROM states WHERE company_id = '" . $post['company_id'] . "' AND state_name = '" . $state . "' AND is_active = 0";
	        $stateres = $callConfig->getRow($statesql);
	        $city = $post['city'];
	        $location = $post['location'];
	        $citysql = "SELECT id, city_name FROM cities WHERE company_id = '" . $post['company_id'] . "' AND is_active = 0 AND ( city_name LIKE '%" . $city . "%' OR city_name LIKE '%" . $location . "%' )";
	        $cityres = $callConfig->getRow($citysql);
	        $shipping_country = $stateres->country_id;
	        $billing_country  = $stateres->country_id;
	        $shipping_address = $post['address'];
	        $billing_address  = $post['address'];
	        $billing_post_code = $post['post_code'];
	        $shipping_post_code = $post['post_code'];
	        $billing_state = $stateres->id;
	        $shipping_state = $stateres->id;
	        $billing_city = $cityres->id;
	        $shipping_city = $cityres->id;
	        $gstin = $post['gstin'];
	        $transaction_id = $post['transaction_id'];
	        $gst_type = $post['gst_type'];
	        $gst_default = $post['gst_default'];
	 
	        if ($gst_type == "new") {
	            $is_default = ($gst_default == 'new') ? 'Y' : 'N';
	            $insertuserarray = ['company_id' => $post['company_id'], 'transaction_table' => $post['transaction_table'], 'customer_vendor_id' => $transaction_id, 'gstin' => $gstin, 'shipping_country' => $shipping_country, 'billing_country' => $billing_country, 'shipping_address' => $shipping_address, 'billing_address' => $billing_address, 'billing_post_code' => $billing_post_code, 'shipping_post_code' => $shipping_post_code, 'billing_state' => $billing_state, 'shipping_state' => $shipping_state, 'billing_city' => $billing_city, 'shipping_city' => $shipping_city, 'is_default' => $is_default, 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date("Y-m-d H:i:s"), ];
	            $insert_id = $callConfig->insertRecord('customer_vendor_gst_billing_details', $insertuserarray);
	            $updateary = ['vendor_verifications' => json_encode($vendor_verification), 'previous_verifications' => json_encode($existingHistory),'gstin' => $gstin, 'user_id' => $post['user_id'], 'updated_by' => $post['user_id'], 'updated_date' => date("Y-m-d H:i:s"),];
	            if ($is_default == 'Y') {
	                $updateary = ['billing_address' => $billing_address, 'shipping_address' => $shipping_address, 'shipping_country' => $shipping_country, 'billing_country' => $billing_country, 'billing_post_code' => $billing_post_code, 'shipping_post_code' => $shipping_post_code, 'billing_state' => $billing_state, 'shipping_state' => $shipping_state, 'billing_city' => $billing_city, 'shipping_city' => $shipping_city,'vendor_verifications' => json_encode($vendor_verification),'gstin' => $gstin,'user_id' => $post['user_id'], 'updated_by' => $post['user_id'], 'updated_date' => date("Y-m-d H:i:s")];
	            }
	            $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	        } else {
	            $gstdetailsql = "SELECT id FROM customer_vendor_gst_billing_details WHERE company_id = '" . $post['company_id'] . "' AND gstin = '" . $gstin . "' AND is_active = 0";
	            $gstdetail = $callConfig->getRow($gstdetailsql);
	            if ($gstdetail->id == "") {
	                $insertuserarray = ['company_id' => $post['company_id'], 'transaction_table' => $post['transaction_table'], 'customer_vendor_id' => $transaction_id, 'gstin' => $gstin, 'shipping_country' => $shipping_country, 'billing_country' => $billing_country, 'shipping_address' => $shipping_address, 'billing_address' => $billing_address, 'billing_post_code' => $billing_post_code, 'shipping_post_code' => $shipping_post_code, 'billing_state' => $billing_state, 'shipping_state' => $shipping_state, 'billing_city' => $billing_city, 'shipping_city' => $shipping_city, 'is_default' => 'Y', 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date("Y-m-d H:i:s"), ];
	                $insert_id = $callConfig->insertRecord('customer_vendor_gst_billing_details', $insertuserarray);
	            }
	            $updateary = [ 'vendor_verifications' => json_encode($vendor_verification),'previous_verifications' => json_encode($existingHistory),'gstin' => $gstin, 'user_id' => $post['user_id'], 'updated_by' => $post['user_id'], 'updated_date' => date("Y-m-d H:i:s"), 'billing_address' => $billing_address, 'shipping_address' => $shipping_address, 'shipping_country' => $shipping_country, 'billing_country' => $billing_country, 'billing_post_code' => $billing_post_code, 'shipping_post_code' => $shipping_post_code, 'billing_state' => $billing_state, 'shipping_state' => $shipping_state, 'billing_city' => $billing_city, 'shipping_city' => $shipping_city, ];
	            $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	        }
	        $return['res'] = $res;
	    } elseif ($verification_type == "TAN") {
	        if (!empty($previousres->tan_number)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'tan_number', 'value' => $previousres->tan_number ];
	        }
	        $vendor_verification[$verification_type] = ['status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	        $updateary = ['vendor_verifications' => json_encode($vendor_verification), 'previous_verifications' => json_encode($existingHistory), 'tan_number' => $post['tan_number'], 'updated_by' => $post['user_id'], 'updated_date' => $now ];
	        $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	        $return['res'] = $res;
	    } elseif ($verification_type == "BANK") {
	        if (!empty($previousres->bank_account_no)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'bank_account_no', 'value' => $previousres->bank_account_no ];
	        }
	        if (!empty($previousres->ifsc_code)) {
	            $existingHistory[] = ['date' => $now, 'field' => 'ifsc_code', 'value' => $previousres->ifsc_code ];
	        }
	        $vendor_verification[$verification_type] = ['status' => 'Y', 'verification_user_id' => $post['user_id'], 'verification_datetime' => $now, 'api_hit_id' => $post['api_hit_id'] ];
	       // $updateary = ['vendor_verifications' => json_encode($vendor_verification), 'previous_verifications' => json_encode($existingHistory), 'bank_name' => $post['bank_name'], 'bank_account_no' => $post['bank_account_no'], 'ifsc_code' => $post['ifsc_code'], 'updated_by' => $post['user_id'], 'updated_date' => $now ];
	        $ifsc_code = $post['ifsc_code'];
	        $bank_account_no = $post['bank_account_no'];
	        $bank_name = $post['bank_name'];
	        $account_type = $post['account_type'];
	        $updateary = array('vendor_verifications' => json_encode($vendor_verification),'previous_verifications' => json_encode($existingHistory), 'bank_name' => $bank_name, 'bank_account_no' => $bank_account_no, 'ifsc_code' => $ifsc_code, 'account_type' => $account_type, 'updated_by' => $post['user_id'], 'updated_date' => date("Y-m-d H:i:s"));
	        $res = $callConfig->updateRecord($transaction_table, $updateary, 'id', $transaction_id);
	        $return['res'] = $res;
	    }
	 
	    return $return;
	}
	//End omkar vdocument verification code added -----
	function insertVendorandVendorRegistersSynchAPI($get){
		global $callConfig;
		$return = array();
		$outsidedatabase = "infistaqin_infisuite_3";
		$transaction_table = "vendors";
		$urlDev = "https://dev.infistaq.in/api.php?mode=getvendorandvendorregisterssynchapi&company_id=1&user_id=1&data_name=".$outsidedatabase;
		
		//$urlDev = "https://partnerconnect.microchip.com/api.php/?mode=getvendorandvendorregisterssynchapi&company_id=1&user_id=1&data_name=vendorstaq_partner";
		$fieldgetsql = "SHOW COLUMNS FROM ".$transaction_table;
		$fieldgetres = $callConfig->getAllRows($fieldgetsql);
		$dataDev = $this->getApiData($urlDev,$postdata);
		if(count($dataDev['vendors']) != 0){
			foreach($dataDev['vendors'] as $venddata){

				$odmapsql = "SELECT id,internal_mapping FROM outside_api_data_mappings WHERE mapping_system_name = '".$outsidedatabase."' AND transaction_table = '".$transaction_table."' AND external_mapping = '".$venddata['id']."' AND is_active = 0";
				$odmapres = $callConfig->getRow($odmapsql);
				//$return['odmapres'] = $odmapsql;
				if($odmapres->internal_mapping !=""){
					$insertfieldary  = array();
					foreach($fieldgetres as $field){
					 	if(!in_array($field->Field, (array)$notupdateinsert)){
							$insertfieldary[$field->Field] = $venddata[$field->Field];
						}
					}
					$callConfig->updateRecord($transaction_table, $insertfieldary, 'id', $odmapres->internal_mapping);
				}else{
					$insertfieldary  = array();
					foreach($fieldgetres as $field){
					 	if(!in_array($field->Field, (array)$notupdateinsert)){
							$insertfieldary[$field->Field] = $transaction[$field->Field];
						}
					}
					$insertven = $callConfig->insertRecord($transaction_table, $insertfieldary);
					$return['insertven'] = $insertven;
					if($insertven['status'] == 'sucess'){
						$post_array = array("company_id"=>1,"mapping_system_name"=>$outside_db_name,"transaction_table"=>$transaction_table,"internal_mapping"=>$insertven['LASTID'],"external_mapping"=>$transaction_id,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
						$callConfig->insertRecord('outside_api_data_mappings',$post_array);
					}
				}

			}
		}
		return $return;
	}
	function getvendorandvendorregisterssynchapi(){
		global $callConfig;
		$return = array();
		$vendorsql = "SELECT * FROM vendors";
		$vendorres = $callConfig->getAllRows($vendorsql);
		$return['vendors'] = $vendorres;
		return $return;
	}
	function getApiData($url, $postData = null) {
	    $ch = curl_init($url);
	    if (!empty($postData)) {
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable SSL verify for dev
	    $response = curl_exec($ch);
	    if (curl_errno($ch)) {
	        return [ 'success' => false,'error' => curl_error($ch)];
	    }
	    $return = json_decode($response, true);
	    if (!is_array($return)) {
	        return ['success' => false,'raw_response' => $response];
	    }
	    return $return;
	}
	function getVendorsDetailsReports($get){
		global $callConfig;
		$return = array();

		if($get['transaction_table'] !=""){
			$conditionwh = "";
			if($get['vendor_types_id'] !=""){
				$conditionwh .= " AND vendor_types_id =".$get['vendor_types_id'];
			}
			if($get['location_id'] !=""){
				$conditionwh .= " AND location_id =".$get['location_id'];
			}
			if($get['vendor_connect_user'] !=""){
				$conditionwh .= " AND (hr_connect =".$get['vendor_connect_user']." OR finance_connect =".$get['vendor_connect_user']." OR purchase_connect =".$get['vendor_connect_user']." OR admin_connect =".$get['vendor_connect_user']." OR project_connect =".$get['vendor_connect_user']." OR legal_connect =".$get['vendor_connect_user'].")";
			}
			$locsql = "SELECT * FROM ".$get['transaction_table']." WHERE company_id = '".$get['company_id'] ."' AND is_active = 0 ".$conditionwh;
			$locres = $callConfig->getAllRows($locsql);
			$return['vendorlist'] = $locres;
		}
		$locsql = "SELECT id, location_name FROM locations WHERE  company_id = '".$get['company_id'] ."' AND is_active = 0";
		$locres = $callConfig->getAllRows($locsql);
		foreach($locres as $loc){
			$locations[$loc->id] = $loc; 
		}
		$return['locations'] = $locations;

		$ausql = "SELECT id, display_name FROM application_users WHERE  company_id = '".$get['company_id'] ."' AND is_active = 0";
		$aures = $callConfig->getAllRows($ausql);
		foreach($aures as $aur){
			$application_users[$aur->id] = $aur; 
		}
		$return['application_users'] = $application_users;

		$vendor_typessql = "SELECT id, type_name FROM vendor_types WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $vendor_typesres = $callConfig->getAllRows($vendor_typessql);
        foreach($vendor_typesres as $vt){
			$vendor_types[$vt->id] = $vt; 
		}
        $return['vendor_types'] = $vendor_types;

        $countriessql = "SELECT id, country_name FROM countries WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $countriesres = $callConfig->getAllRows($countriessql);
        foreach($countriesres as $sres){
			$countries[$sres->id] = $sres; 
		}
        $return['countries'] = $countries;


        $statessql = "SELECT id, state_name FROM states WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $statesres = $callConfig->getAllRows($statessql);
        foreach($statesres as $sres){
			$states[$sres->id] = $sres; 
		}
        $return['states'] = $states;

        $citiessql = "SELECT id, city_name FROM cities WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $citiesres = $callConfig->getAllRows($citiessql);
        foreach($citiesres as $cres){
			$cities[$cres->id] = $cres; 
		}
        $return['cities'] = $cities;

        $application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_ADMIN_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_admin_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_HR_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_hr_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_FINANCE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_finanace_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PROJECT_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_project_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LOCATION_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_location_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PURCHASE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_purchase_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LEGAL_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_legal_application_users'] =$application_usersres;
			


		return $return;
	}
/**
	function getVendorandVendorRegistersSynchAPI($post,$transaction_table,$transaction_id){
		global $callConfig;
		$return = array();
		//$dbmargeary = array('infistaqin_infisuite_2'=>array(array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_3')));
		$dbmargeary = array('infistaqin_infisuite_2'=>array(array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_3'),array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_4')),'infistaqin_infisuite_3'=>array(array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_2'),array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_4')),'infistaqin_infisuite_4'=>array(array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_2'),array('url'=>'https://dev.infistaq.in','db_name'=>'infistaqin_infisuite_3')));
		$vendorsql = "SELECT * FROM ".$transaction_table." WHERE id = ".$transaction_id;
		$vendorres = $callConfig->getRow($vendorsql);
		
		$db_name = $callConfig->dbname;
		foreach($dbmargeary[$db_name] as $subdb){
			$urlDev = $subdb['url']."/api.php/?mode=insertvendorandvendorregisterssynchapi&method=post&company_id=1&user_id=1&data_name=".$subdb['db_name'];
			//$postdata['urlDev'] = $urlDev;
			$postdata['outside_db_name'] = $db_name;
			$postdata['transaction'] = $vendorres;
			$postdata['transaction_table'] = $transaction_table;
			$postdata['transaction_id'] = $transaction_id;
			//$dataDev = $this->getApiData($urlDev,$postdata);
			$return['dataSelf'][] = $dataDev;
		} 
		return $return;
	}
	**/
	

/**

	function insertVendorandVendorRegistersSynchAPI($get,$post){
		global $callConfig;
		$return = array();
		//$return['get'] = $get;
		//$return['post'] = $post;
		$notupdateinsert = array('id','user_id','updated_by','created_by','vendor_id');
		$db_name = $callConfig->dbname;
		$transaction_table = $post['transaction_table'];
		$transaction_id = $post['transaction_id'];
		$outside_db_name = $post['outside_db_name'];
		$transaction = $post['transaction'];

		$odmapsql = "SELECT id,internal_mapping FROM outside_api_data_mappings WHERE mapping_system_name = '".$outside_db_name."' AND transaction_table = '".$transaction_table."' AND external_mapping = '".$transaction_id."' AND is_active = 0";
		$odmapres = $callConfig->getRow($odmapsql);
		$return['odmapres'] = $odmapsql;

		$fieldgetsql = "SHOW COLUMNS FROM ".$transaction_table;
		$fieldgetres = $callConfig->getAllRows($fieldgetsql);
		
		if($odmapres->internal_mapping !=""){
			$insertfieldary  = array();
			foreach($fieldgetres as $field){
			 	if(!in_array($field->Field, (array)$notupdateinsert)){
					$insertfieldary[$field->Field] = $transaction[$field->Field];
				}
			}
			$callConfig->updateRecord($transaction_table, $insertfieldary, 'id', $odmapres->internal_mapping);
		}else{
			$insertfieldary  = array();
			foreach($fieldgetres as $field){
			 	if(!in_array($field->Field, (array)$notupdateinsert)){
					$insertfieldary[$field->Field] = $transaction[$field->Field];
				}
			}
			$insertven = $callConfig->insertRecord($transaction_table, $insertfieldary);
			$return['insertven'] = $insertven;
			if($insertven['status'] == 'sucess'){
				$post_array = array("company_id"=>1,"mapping_system_name"=>$outside_db_name,"transaction_table"=>$transaction_table,"internal_mapping"=>$insertven['LASTID'],"external_mapping"=>$transaction_id,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$callConfig->insertRecord('outside_api_data_mappings',$post_array);
			}
		}
		return $return;
	} **/

	function MSMEConfirmationUpdate($get,$post){
		global $callConfig;
		$return = array();
		$msme_confirmation = $post['msme_confirmation']=="Y"?"Y":"N";
		$post_array = array("company_id"=>$post['company_id'],"transaction_table"=>$post['transaction_table'],"transaction_id"=>$post['transaction_id'],"confirmation_status"=>$msme_confirmation,"confirmation_date"=>date('Y-m-d'),"msme_number"=>$post['msme_confirmation_msme_number'],"remarks"=>$post['remarks'],"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
		$return['res'] = $callConfig->insertRecord('msme_confirmation_logs',$post_array);
		return $return;
	}
	function insertVendorsAndVendorRegisters($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$transaction_table = $get['transaction_table']!=""?$get['transaction_table']:'vendors';
		$process_type = $post['transaction_table'] =="vendors"?"VEDR":"VR";
		$post_array = $post;
		$rid = $post['rid'];
		unset($post_array['rid']);
		unset($post_array['doc_rowid']);
		unset($post_array['doc_document_id']);
		unset($post_array['doc_from_date']);
		unset($post_array['doc_to_date']);
		unset($post_array['hidden_doc_filename']);
		unset($post_array['customer_vendor_gst_billing_details']);
		unset($post_array['doc_filename']);
		if($rid !=""){
			$vendor_register_id =$rid; 
			$return['trans_res'] = $callConfig->updateRecord($transaction_table, $post_array, 'id', $vendor_register_id);

			$updatequery = "UPDATE customer_vendor_gst_billing_details SET is_active=NULL WHERE transaction_table = '".$transaction_table."' AND customer_vendor_id = '".$vendor_register_id."'";
    		$callConfig->executeQuery($updatequery);

		}else{
			$inserttransaction = $callConfig->insertRecord($transaction_table,$post_array);
			$return['trans_res'] = $inserttransaction;
			$vendor_register_id = 	$inserttransaction['LASTID'];
			//vendor task
			if($vendor_register_id !=""){
				$transaction_date = date('Y-m-d');
				$ausql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$vendor_register_id."' AND is_active = 0";
				$aures = $callConfig->getRow($ausql);
				$return['aures'] =$aures;
				//$venregary = array('hr_connect'=>$get['user_id'],'admin_connect'=>$get['user_id'],'finance_connect'=>$get['user_id'],'project_connect'=>$get['user_id'],'location_connect'=>$get['user_id'],'esop_connect'=>$get['user_id'],'legal_connect'=>$get['user_id'],'user_id'=>$get['user_id']);
				//$aures = (object) $venregary;
				$aures->user_id = $post['created_by'];

				$tasquery = "SELECT task_id, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$vendor_register_id."' AND is_active = 0";
				$tasres = $callConfig->getAllRows($tasquery);
				$alreadytaskid = array();
				foreach($tasres  as $roleres){
					$alreadytaskid[$roleres->task_id][$roleres->assigned_to] = "Y"; 
				}	
				$return['tasquery'] =$tasquery;
				$return['alreadytaskid'] =$alreadytaskid;
				$whr1 = "company_id = '".$get['company_id']."' AND is_active = 0 AND process_type='".$process_type."'";
				$task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
				$task_data = $callConfig->getAllRows($task_query);	 
				$return['task_query'] =$task_query;
				$return['task_data'] =$task_data;
					foreach ($task_data as $key => $tasks) {
						$days_ago = $transaction_date;
						if($tasks->action_no =='1'){
							if($tasks->action_frequency == 'DY'){	
								$days_ago = $transaction_date;
							}elseif($tasks->action_frequency == 'WK'){	
								$day = 7; 	
							}elseif($tasks->action_frequency == 'MON'){
								$day = 30; 
							}
							if($tasks->before_or_after == 'BF'){ 
								$day = "-".$day; 
							}else{ 
								$day = "+".$day; 
							}
							$days_ago = date('Y-m-d', strtotime($day.' days', strtotime($transaction_date)));
						}else{
							$days_ago = $transaction_date;
						}
						if($tasks->assigned_to =="HB"){
							$assigned_to = $aures->hr_connect;
						}elseif($tasks->assigned_to =="AB"){
							$assigned_to = $aures->admin_connect;
						}elseif($tasks->assigned_to =="FB"){
							$assigned_to = $aures->finance_connect;
						}elseif($tasks->assigned_to =="PB"){
							$assigned_to = $aures->project_connect;
						}elseif($tasks->assigned_to =="LB"){
							$assigned_to = $aures->location_connect;
						}elseif($tasks->assigned_to =="ESOP"){
							$assigned_to = $aures->esop_connect;
						}elseif($tasks->assigned_to =="LEGAL_BUDDY"){
							$assigned_to = $aures->legal_connect;
						}else{
							$assigned_to = $aures->user_id;
						}
						if(!isset($alreadytaskid[$tasks->id][$assigned_to])){
							if($tasks->approval_type =="HB"){
								$approval_user_id = $aures->hr_connect;
							}elseif($tasks->approval_type =="AB"){
								$approval_user_id = $aures->admin_connect;
							}elseif($tasks->approval_type =="FB"){
								$approval_user_id = $aures->finance_connect;
							}elseif($tasks->approval_type =="PB"){
								$approval_user_id = $aures->project_connect;
							}elseif($tasks->approval_type =="LB"){
								$approval_user_id = $aures->location_connect;
							}elseif($tasks->approval_type =="ESOP"){
								$approval_user_id = $aures->esop_connect;
							}elseif($tasks->approval_type =="LEGAL_BUDDY"){
								$approval_user_id = $aures->legal_connect;
							}else{
								$approval_user_id = $aures->user_id;
							}
							$planned_finish = date('Y-m-d', strtotime($tasks->planned_day.' days', strtotime($days_ago)));
							//$planned_finish = date('Y-m-d', strtotime($tasks->planned_day.' + '.$days_ago.' days'));
							
							if($approval_user_id !="" AND $assigned_to !=""){
								$task_array = array('company_id'=>$get['company_id'],'transaction_table'=>$transaction_table,'transaction_id'=>$vendor_register_id,'task_id'=>$tasks->id,'assigned_to'=>$assigned_to,'approval_user_id'=>$approval_user_id,'action_date'=>$days_ago,'revised_action_date'=>$days_ago,'actual_action_date'=>$days_ago,'check_list'=>$tasks->check_list,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'process_id'=>$tasks->process_id,'task_name'=>$tasks->task_name);
								$res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
								$return['transaction_activity_schedule'][] =$task_array;
								$return['transaction_activity_schedule_insert'][] =$res;
							}
							$return['checkedcsddsfvdfv'][] =$approval_user_id." --- ".$assigned_to;
						}
					}
				}
			//vendor task
		}

		foreach($post['customer_vendor_gst_billing_details'] as $detai){
			$custvend = $detai;
			unset($custvend['customer_vendor_gst_billing_details_id']);
			$custvend['customer_vendor_id'] = $vendor_register_id;
			if($detai['customer_vendor_gst_billing_details_id'] !=""){
				 $upd = $callConfig->updateRecord('customer_vendor_gst_billing_details', $custvend, 'id', $detai['customer_vendor_gst_billing_details_id']);
				 $return['customer_vendor_gst_billing_details'][] = $upd;
			}else{
				$upda = $callConfig->insertRecord('customer_vendor_gst_billing_details',$custvend);
				$return['customer_vendor_gst_billing_details'][] = $upda;
			}
		}

		foreach($post['doc_filename'] as $key=>$docdetai){

			$fields = array('company_id'=>$get['company_id'],'vendor_user_id'=>$vendor_register_id,'document_id'=>$post['doc_document_id'][$key],'from_date'=>$this->dateconvertdbformat($post['doc_from_date'][$key]),'to_date'=>$this->dateconvertdbformat($post['doc_to_date'][$key]),'filename'=>$docdetai,'category_id'=>0,'is_active'=>'0','current_status'=>'0');
			if($post['doc_rowid'][$key]!=""){ 
				$fields['created_by'] = $get['user_id'];
				$fields['created_date'] = date("Y-m-d H:i:s");
				$docres=$callConfig->updateRecord('vendor_documents',$fields,'id',$post['doc_rowid'][$key]);
		 		$return['vendor_documents'][] = $docres;
			}else{
				$fields['updated_by'] = $get['user_id'];
				$fields['updated_date'] = date("Y-m-d H:i:s");
				$docres=$callConfig->insertRecord('vendor_documents',$fields);
				$return['vendor_documents'][] = $docres;					
			}
		}

		return $return;
	}
	function sendEmailAPIWithoutLogDocumentVerification($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$api_result = json_decode($post['api_result'],1);
		$emailarray['user_email_id'] = $post['user_email'];
		$emailarray['to_company_id'] = $post['company_id'];
		$emailarray['session_company_id'] = $post['company_id'];
		$emailarray['created_by'] = $post['user_id'];
		$extramsg .= "<table width='100%'><tbody>";
		if (empty($api_result['result']) || count((array)$api_result['result']) == 0) {
			$extramsg .= "<tr><th style='text-align: left; width:50%'> Response:</th><td style='text-align: left; width:50%'>".$api_result['response_message']."</td></tr>";
		}else{
			foreach($api_result['result'] as $resukey=>$resu){
				$extramsg .= "<tr><th style='text-align: left; width:50%'>".ucwords(str_replace('_',' ',$resukey)).":</th><td style='text-align: left; width:50%'>".$resu."</td></tr>";
			}
		}
		$extramsg .= "<tbody>"; 
		$emailarray['extra_informations'] = $extramsg;
		$notdataa = array();
		$emails = $this->sendNotificationAndEmails($notdataa,$emailarray,74);
		return $emails;
	}
	function insertApplicationUserInvitations($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$return['get'] = $get;
		$transaction_table = $post['transaction_table'] !=""?$post['transaction_table']:"vendor_registers";
		$process_type = $post['transaction_table'] =="vendors"?"VEDR":"VR";
		$role_id = json_encode(array($post['role_id']));
		$connectuser = $post['connect'];
		$connect_users = json_encode((array)$connectuser);
		$display_name = $post['first_name']." ".$post['last_name'];
		$passrd = 'PensAndPencils123';
		$password = $callConfig->passwordEncrypt($passrd);	
		$applicationuserary = array('company_id'=>$post['company_id'],'user_name'=>$display_name,'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'],'user_type'=>$post['user_type'],'user_password'=>$password,'role_id'=>$role_id,'hr_connect'=>$connectuser['hr_connect'],'finance_connect'=>$connectuser['finance_connect'],'admin_connect'=>$connectuser['admin_connect'],'project_connect'=>$connectuser['project_connect'],'location_connect'=>$connectuser['location_connect'],'legal_connect'=>$connectuser['legal_connect'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'user_activated'=>'Y');
		$res=$callConfig->insertRecord('application_users',$applicationuserary);
		$retresult['application_users'] = $res;
		if(isset($res['LASTID'])){
			$query = "SELECT application_module,id,role_short_code FROM application_roles where company_id = '".$post['company_id']."' AND role_short_code IN('".$post['role_id']."') AND is_active = 0";
			$roleres = $callConfig->getAllRows($query);
			$retresult['application_roles'] = $roleres;
			$retresult['application_roles_sql'] = $query;
			foreach($roleres as $rolere){
				$insertarray = array("company_id"=>$post['company_id'],"user_id"=>$res['LASTID'],"module_setup_id"=>$rolere->application_module,"application_module_id"=>$rolere->application_module,"role_id"=>$rolere->id,"role_short_code"=>$rolere->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$insert = $callConfig->insertRecord('application_user_module_roles',$insertarray);
				$rolesids[] = $rolere->role_short_code;	
				$user_module_id = $rolere->application_module;	
			}
			$applicationuserinvoiteary = array('company_id'=>$post['company_id'],'company_name'=>$post['company_name'],'user_name'=>$post['user_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'],'user_type'=>$post['user_type'],'user_id'=>$res['LASTID'],'role_id'=>$post['role_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'transaction_table'=>$transaction_table);
			$res1=$callConfig->insertRecord('application_user_invitations',$applicationuserinvoiteary);
			$web_site = $facsimile_number = $country_id= $state_id = "";
			if($post['vendor_registration_request_id'] !=""){
				$query1 = "SELECT id, web_site, facsimile_number, country_id, state_id FROM vendor_registration_requests where company_id = '".$post['company_id']."' AND id = '".$post['vendor_registration_request_id'] ."' AND is_active = 0";
				$res11 = $callConfig->getRow($query1);
				$web_site = $res11->web_site;
				$facsimile_number = $res11->facsimile_number;
				$country_id = $res11->country_id;
				$state_id = $res11->state_id;
			}	
			$vendor_verifications = json_encode(array("PAN"=>array("status"=>"N"), "GSTIN"=>array("status"=>"N"), "TAN"=>array("status"=>"N"), "BANK"=>array("status"=>"N"),'MSME'=>array("status"=>"N")));
			$msme_register_yes_no = $post['msme_register_yes_no'] =="Y"?"Y":"N";
			$vendor_registersary = array('company_id'=>$post['company_id'],'user_id'=>$res['LASTID'],'company'=>$post['company_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'email'=>$post['user_email'],'phone'=>$post['user_mobile'],'hr_connect'=>$connectuser['hr_connect'],'finance_connect'=>$connectuser['finance_connect'],'admin_connect'=>$connectuser['admin_connect'],'project_connect'=>$connectuser['project_connect'],'location_connect'=>$connectuser['location_connect'],'legal_connect'=>$connectuser['legal_connect'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'originator'=>$post['user_id'],'vendor_registration_request_id'=>$post['vendor_registration_request_id'], 'company_registration_country'=>$country_id,'fax'=>$facsimile_numberm,'website_url'=>$web_site,'vendor_verifications'=>$vendor_verifications,'vendor_type_id'=>$post['vendor_type_id'],'msme_register_yes_no'=>$msme_register_yes_no,'msme_number'=>$post['msme_number']);
			$vendregistres=$callConfig->insertRecord($transaction_table,$vendor_registersary);
			$vendor_register_id = 	$vendregistres['LASTID'];
			$retresult['vendor_register_id'] = $vendor_register_id;
			//vendor task
			if($vendor_register_id !=""){
				$transaction_date = date('Y-m-d');
				$ausql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$vendor_register_id."' AND is_active = 0";
				$aures = $callConfig->getRow($ausql);
				$retresult['aures'] =$aures;

				$tasquery = "SELECT task_id, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$vendor_register_id."' AND is_active = 0";
				$tasres = $callConfig->getAllRows($tasquery);
				$alreadytaskid = array();
				foreach($tasres  as $roleres){
					$alreadytaskid[$roleres->task_id][$roleres->assigned_to] = "Y"; 
				}	
				$retresult['tasquery'] =$tasquery;
				$retresult['alreadytaskid'] =$alreadytaskid;
				$whr1 = "company_id = '".$get['company_id']."' AND is_active = 0 AND process_type='".$process_type."'";
				$task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
				$task_data = $callConfig->getAllRows($task_query);	 
				$retresult['task_query'] =$task_query;
				$retresult['task_data'] =$task_data;
					foreach ($task_data as $key => $tasks) {
						$days_ago = $transaction_date;
						if($tasks->action_no =='1'){
							if($tasks->action_frequency == 'DY'){	
								$days_ago = $transaction_date;
							}elseif($tasks->action_frequency == 'WK'){	
								$day = 7; 	
							}elseif($tasks->action_frequency == 'MON'){
								$day = 30; 
							}
							if($tasks->before_or_after == 'BF'){ 
								$day = "-".$day; 
							}else{ 
								$day = "+".$day; 
							}
							$days_ago = date('Y-m-d', strtotime($day.' days', strtotime($transaction_date)));
						}else{
							$days_ago = $transaction_date;
						}
						if($tasks->assigned_to =="HB"){
							$assigned_to = $aures->hr_connect;
						}elseif($tasks->assigned_to =="AB"){
							$assigned_to = $aures->admin_connect;
						}elseif($tasks->assigned_to =="FB"){
							$assigned_to = $aures->finance_connect;
						}elseif($tasks->assigned_to =="PB"){
							$assigned_to = $aures->project_connect;
						}elseif($tasks->assigned_to =="LB"){
							$assigned_to = $aures->location_connect;
						}elseif($tasks->assigned_to =="ESOP"){
							$assigned_to = $aures->esop_connect;
						}elseif($tasks->assigned_to =="LEGAL_BUDDY"){
							$assigned_to = $aures->legal_connect;
						}else{
							$assigned_to = $aures->user_id;
						}
						if(!isset($alreadytaskid[$tasks->id][$assigned_to])){
							if($tasks->approval_type =="HB"){
								$approval_user_id = $aures->hr_connect;
							}elseif($tasks->approval_type =="AB"){
								$approval_user_id = $aures->admin_connect;
							}elseif($tasks->approval_type =="FB"){
								$approval_user_id = $aures->finance_connect;
							}elseif($tasks->approval_type =="PB"){
								$approval_user_id = $aures->project_connect;
							}elseif($tasks->approval_type =="LB"){
								$approval_user_id = $aures->location_connect;
							}elseif($tasks->approval_type =="ESOP"){
								$approval_user_id = $aures->esop_connect;
							}elseif($tasks->approval_type =="LEGAL_BUDDY"){
								$approval_user_id = $aures->legal_connect;
							}else{
								$approval_user_id = $aures->user_id;
							}
							$planned_finish = date('Y-m-d', strtotime($tasks->planned_day.' days', strtotime($days_ago)));
							//$planned_finish = date('Y-m-d', strtotime($tasks->planned_day.' + '.$days_ago.' days'));
							if($approval_user_id !="" AND $assigned_to !=""){
								$task_array = array('company_id'=>$get['company_id'],'transaction_table'=>$transaction_table,'transaction_id'=>$vendor_register_id,'task_id'=>$tasks->id,'assigned_to'=>$assigned_to,'approval_user_id'=>$approval_user_id,'action_date'=>$days_ago,'revised_action_date'=>$days_ago,'actual_action_date'=>$days_ago,'check_list'=>$tasks->check_list,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'process_id'=>$tasks->process_id,'task_name'=>$tasks->task_name);
								$res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
								$return['transaction_activity_schedule'][] =$task_array;
								$return['transaction_activity_schedule_insert'][] =$res;
							}
						}
					}
				}
			//vendor task

			$retresult['vendregistres'] = $vendregistres;
			$retresult['application_user_invitations'] = $res1;
			$emailarray['user_company_id'] = $post['company_id'];
			$emailarray['session_company_id'] = $post['company_id'];
			$emailarray['send_user_id'] = array($res['LASTID']);
			$emailarray['creater_user_id'] = array($post['user_id']);
			$emailarray['to_company_id'] = $post['company_id'];
			$emailarray['user_display_name'] = $display_name;
			$emailarray['user_email_id'] = $post['user_email'];
			$emailarray['user_phone_number'] = $post['user_mobile'];
			$emailarray['default_module_id'] = $user_module_id;
			$emailarray['user_id'] =$res['LASTID'];
			$emailarray['created_by'] = $post['user_id'];
			$emailarray['user_email'] = $post['user_email'];
			$emailarray['company_id'] = $post['company_id'];
			$emailarray['template_reference_id'] = 3;
			$emill = $this->NewUserformate($emailarray,$post['data_name']);

			// $get['process_type'] = 'VR';
			// $get['transaction_id'] = $res['LASTID']; // vendor user id
			// $retresult['task_creation'] = $this->tocreateprocesstaskintotaskdatail($get); // task creation
		}
	return $retresult;
	} 	
	function getInvitationVendors($get){
		global $callConfig;
		$return =array();
		if(isset($get['company_name'])){
			$sql = "SELECT id FROM `application_user_invitations` WHERE company_id = '".$get['company_id']."' AND company_name = '".$get['company_name']."' AND user_type = 3 AND is_active = 0";
			$invitationres = $callConfig->getRow($sql);
			if($invitationres->id !=""){
				$return['company_name'] = $invitationres;
			}else{
				$sql = "SELECT id FROM `vendor_registers`  WHERE company_id = '".$get['company_id']."' AND company = '".$get['company_name']."' AND is_active = 0";
				$venregres = $callConfig->getRow($sql);
				if($venregres->id !=""){
					$return['company_name'] = $venregres;
				}else{ 
					$sql = "SELECT id FROM `vendors`  WHERE company_id = '".$get['company_id']."' AND company = '".$get['company_name']."' AND is_active = 0";
					$vendors = $callConfig->getRow($sql);
					if($vendors->id !=""){
						$return['company_name'] = $vendors;
					}
				}
			}
		}else{
			$sql = "SELECT id,display_name,application_module, role_short_code FROM application_roles WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$res = $callConfig->getAllRows($sql);
			$return['application_roles'] = $res;

			$module_setupsql = "SELECT module_id, module_name FROM module_setup WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$module_setupres = $callConfig->getAllRows($module_setupsql);
			foreach($module_setupres as $moduleres){
			  $module[$moduleres->module_id] = $moduleres->module_name;
			}
			$return['module_setup'] = $module;

			// $ausql = "SELECT id,display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			// $aures = $callConfig->getAllRows($ausql);
			// $return['application_users'] = $aures;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_ADMIN_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_admin_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_HR_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_hr_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_FINANCE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_finanace_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PROJECT_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_project_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LOCATION_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_location_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PURCHASE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_purchase_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LEGAL_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_legal_application_users'] =$application_usersres;
			
			if($get['VRRid'] !=""){
				$sql = "SELECT * FROM vendor_registration_requests WHERE company_id = '".$get['company_id']."' AND id = '".$get['VRRid']."' AND is_active = 0";
				$res = $callConfig->getRow($sql);
				$return['vendor_registration_requests'] = $res;
			}
			$vendor_typessql = "SELECT id, type_name FROM vendor_types WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
	        $vendor_typesres = $callConfig->getAllRows($vendor_typessql);
	        $return['vendor_types'] = $vendor_typesres;

		}

		return $return;
	}
	function getVendorAdminDashboardPost($get, $post){
		return $this->getVendorAdminDashboard($post);
	}
	function getVendorAdminDashboard($get){
		global $callConfig;
		if($get['vendorcreate'] == 'Y'){
			$return['registervendors'] = $this->registervendors($get['vendor_id'],$get);
		}else{
			$return['get'] = $get;
			$dashboard_transaction_table = "vendor_registers";
			if($get['entervendornamefind'] !=""){
				$extracondi = " AND (company LIKE '%".$get['entervendornamefind']."%' OR display_name LIKE '%".$get['entervendornamefind']."%')";
			}
			if($get['limit_select'] !=0){
	 			
	 			$cntsql = "SELECT COUNT(id) as cnt FROM ".$dashboard_transaction_table." ven WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1' ) AND is_active = 0".$extracondi;
	     		$cntres =  $callConfig->getRow($cntsql);
	     		$return['total_vendors'] = $cntres->cnt;
	     		$return['total_record_cnt'] = $cntres;
	     		$respons_data_cnt =  ($cntres->cnt!="" AND $cntres->cnt !=0)?$cntres->cnt:0;

		     	$divide_limit = $respons_data_cnt/$get['limit_select'];
				$total_pages = ceil($respons_data_cnt / $get['limit_select']);
				$current_page = isset($get['current_page']) ? (int)$get['current_page'] : 1;
				$current_page = max(1, min($current_page, $total_pages));
				$start_from = ($current_page - 1) * $get['limit_select'];
				$return['total_pages'] = $total_pages;
				
				
				$dynmictablersql = "SELECT id,user_id,display_name,created_date,image,email,created_by,company, send_verification_profile_status, verification_profile_users, current_status, send_verification_profile_status, vendor_id, originator, vendor_type_id ".$getvrid." FROM ".$dashboard_transaction_table." ven WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1' ) AND is_active = 0  ".$extracondi." LIMIT ".$start_from.", ".$get['limit_select'];
				$vendorres = $callConfig->getAllRows($dynmictablersql);
				$return['vendors'] = $vendorres;
			}else{
				$dynmictablersql = "SELECT id,user_id,display_name,created_date,image,email,created_by,company, send_verification_profile_status, verification_profile_users, current_status, send_verification_profile_status, vendor_id, originator, vendor_type_id ".$getvrid." FROM ".$dashboard_transaction_table." ven WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1' ) AND is_active = 0".$extracondi;
				$vendorres = $callConfig->getAllRows($vendorsql);
				$return['vendors'] = $vendorres;
				$return['total_vendors'] = count((array)$vendorres);	
				$return['total_record_cnt'] = count((array)$vendorres);
				
			}


			
			//$getvrid = ", (SELECT vr.id FROM vendor_registers vr  WHERE vr.company_id =  '".$get['company_id']."' AND ven.id = vr.vendor_id  AND vr.is_active = 0 limit 1) as vr_id";
			//$vendorsql = "SELECT id,user_id,display_name,created_date,image,email,created_by,company, send_verification_profile_status, verification_profile_users, current_status, send_verification_profile_status, vendor_id, originator, vendor_type_id ".$getvrid." FROM ".$dashboard_transaction_table." ven WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1' ) AND is_active = 0";
			
			
			//$vendorres = $callConfig->getAllRows($vendorsql);	
			// $return['vendors'] = $vendorres;	
			// $return['total_vendors'] = count((array)$vendorres);	
			$vendoruserid =$registervid =  array();
			foreach($vendorres as $venre){
				$vendoruserid[$venre->user_id] = $venre->user_id;
				$registervid[$venre->id] = $venre->id;
			}
			$return['onboarduserid'] = $registervid;

			$totalvrsql = "SELECT COUNT(CASE WHEN (vend.current_status = ".ST_ACTIVE." AND is_active = 0)  THEN 1 END) AS active_cnt, COUNT(CASE WHEN (vend.current_status = ".ST_APPROVED.") THEN 1 END) AS approved_cnt, COUNT(CASE WHEN (vend.current_status = ".ST_INPROGRESS."  AND is_active = 0) THEN 1 END) AS inprogress_cnt  FROM ".$dashboard_transaction_table." vend WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1')";
			$totalvrres = $callConfig->getRow($totalvrsql);
			$return['transaction_vendor_cnt'] = $totalvrres;

/**	
			$vendorregisterssql = "SELECT id,user_id,display_name,created_date,image,email,created_by,company, verification_profile_users FROM `vendor_registers` WHERE company_id =  '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1' ) AND vendor_id IS NULL AND is_active = 0";
			$vendorregistersres = $callConfig->getAllRows($vendorregisterssql);	
			$return['vendor_registers'] = $vendorregistersres;
			$vendoruserid =$registervid =  array();
			foreach($vendorregistersres as $venre){
				$vendoruserid[$venre->user_id] = $venre->user_id;
				$registervid[$venre->id] = $venre->id;
			}
			$return['onboarduserid'] = $registervid;		
		$thismonthsql = "SELECT count(id) as cnt FROM vendor_registers WHERE company_id = '".$get['company_id']."' AND created_date >='".$get['startdate']."' AND created_date <= '".$get['enddate']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1') AND is_active = 0";
			$thismonthsres = $callConfig->getRow($thismonthsql);
			$return['this_month_invite_cnt'] = $thismonthsres->cnt;
			
			$totalvrsql = "SELECT count(id) as cnt FROM vendor_registers WHERE company_id = '".$get['company_id']."' AND (`hr_connect` = '".$get['user_id']."' OR `finance_connect`  = '".$get['user_id']."' OR `purchase_connect`= '".$get['user_id']."' OR `admin_connect` = '".$get['user_id']."' OR  `project_connect` = '".$get['user_id']."' OR `location_connect` = '".$get['user_id']."' OR `legal_connect` = '".$get['user_id']."' OR `created_by` = '".$get['user_id']."' OR '".$get['admin_type']."'='1') AND is_active = 0";
			$totalvrres = $callConfig->getRow($totalvrsql);
			$return['total_registration_vendors'] = $totalvrres->cnt;
**/
			if($get['admin_type']!='1'){
				$condition_task = " AND (tas.assigned_to = '".$get['user_id']."' OR tas.approval_user_id = '".$get['user_id']."')";
			}
			$transaction_activity_schedulesql = "SELECT tas.assigned_to, tas.transaction_id, count(*) as total_task_cnt, sum(if(tas.current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(tas.current_status=".ST_APPROVED1.",1,0)) as review_cnt, sum(if(tas.current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(tas.current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt, sum(if(tas.action_date < tas.completion_date,1,0)) as delayed_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = '".$dashboard_transaction_table."' AND tas.transaction_id IN(".implode(',', $registervid).")  AND tas.is_active = 0  ".$condition_task." GROUP BY tas.assigned_to,tas.transaction_id";
			$transaction_activity_scheduleres = $callConfig->getAllRows($transaction_activity_schedulesql);
			$return['transaction_activity_scheduleres_sql'] = $transaction_activity_schedulesql;
			$return['transaction_activity_scheduleres'] = $transaction_activity_scheduleres;
			
			$tasks_categoriessql = "SELECT id,category_name FROM tasks_categories where company_id = '".$get['company_id']."' AND is_active = 0";
			$tasks_categoriesres = $callConfig->getAllRows($tasks_categoriessql);	
			$return['tasks_categories'] = $tasks_categoriesres;	
		

			$action_transaction_activity_schedulesql = "SELECT tas.id, tas.transaction_table, tas.transaction_id, tas.task_id, tas.assigned_to, tas.approval_user_id, tas.action_date, tas.revised_action_date, tas.actual_action_date, tas.completion_date, tas.approval_date, tas.action_flag, tas.comments, tas.note, tas.check_list, tas.finish_date, tas.current_status, tas.task_name, tas.process_id, tal.request_date, tal.response_date, tal.comments, tal.response_type FROM transaction_activity_schedule tas, transaction_approval_logs tal WHERE tas.company_id = '".$get['company_id']."' AND tas.company_id = tal.company_id AND tas.transaction_table = '".$dashboard_transaction_table."' AND tas.transaction_id IN(".implode(',', $registervid).") AND tas.id = tal.transaction_id AND tal.transaction_table ='transaction_activity_schedule' AND tal.user_id = '".$get['user_id']."' AND tal.response_type ='0' AND tal.call_back ='1' AND tas.is_active = 0";
			$action_transaction_activity_scheduleres = $callConfig->getAllRows($action_transaction_activity_schedulesql);
			$return['action_transaction_activity_schedule'] =$action_transaction_activity_scheduleres;


			$vendor_registration_requestssql = "SELECT id, company_name, display_name, user_email, share_pdf, request_datetime FROM vendor_registration_requests WHERE company_id = '".$get['company_id']."' AND (`originator` = '".$get['user_id']."' OR ('".$get['admin_type']."'='1' AND  `share_pdf` = 'Y')) AND id NOT In(SELECT vendor_registration_request_id FROM ".$dashboard_transaction_table." WHERE company_id = '".$get['company_id']."' AND vendor_registration_request_id IS NOT NULL AND is_active = 0) AND is_active = 0";
			$vendor_registration_requestsres = $callConfig->getAllRows($vendor_registration_requestssql);
			$return['vendor_registration_requests'] =$vendor_registration_requestsres;


			$msmelogssql = "SELECT msmecl.*,(SELECT ven.company FROM vendors ven WHERE ven.company_id = msmecl.company_id AND msmecl.transaction_table = 'vendors' AND msmecl.transaction_id = ven.id AND ven.is_active = 0) as vendor_name,(SELECT venr.company FROM vendor_registers venr WHERE venr.company_id = msmecl.company_id AND msmecl.transaction_table = '".$dashboard_transaction_table."' AND msmecl.transaction_id = venr.id AND venr.is_active = 0) as vendor_register_name FROM msme_confirmation_logs msmecl WHERE msmecl.company_id = '".$get['company_id']."' AND msmecl.confirmation_status IS NULL AND current_status = 0 AND msmecl.is_active =0";
			$msmelogsres = $callConfig->getAllRows($msmelogssql);
			$return['msme_confirmation_logs'] =$msmelogsres;


		}
		return $return;
	}
	function getVendorsDetails($get){
		global $callConfig;
		$return = array();
		$return['get']= $get;
		$type = $get['transaction_table'] !=""?$get['transaction_table']:$get['table'];
		
		$vendorslistsql = "SELECT id,display_name FROM ".$type." WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$vendorslistres = $callConfig->getAllRows($vendorslistsql);
		$return['vendorslist'] = $vendorslistres;
		
		// $vendorssql = "SELECT * FROM vendors WHERE company_id = '".$get['company_id']."' AND id = '".$get['id']."' AND is_active = 0";
		// $vendorres = $callConfig->getRow($vendorssql);
		// $return['vendors'] = $vendorres;
		if($get['id']!=""){
		//if($get['transaction_table'] =="vendor_registers" AND $get['id']!=""){
			// if($get['id'] !=""){
			 	$vendregcond= " AND id = '".$get['id']."' ";
			// }else{
			// 	$vendregcond= " AND user_id = '".$get['user_id']."' ";
			// }


			$vendor_registerssql = "SELECT * FROM ".$type." WHERE company_id = '".$get['company_id']."'  AND is_active = 0 ".$vendregcond;
			$vendorres = $callConfig->getRow($vendor_registerssql);
			$return['vendors_sql'] = $vendor_registerssql;
			$return['vendors'] = $vendorres;
			
			$taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name, tas.approval_user_id,  tas.assigned_to  FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = '".$type."' AND transaction_id= '".$vendorres->id."' ".$comdition." AND tas.is_active = 0";
			$taskactivityres = $callConfig->getAllRows($taskactivitysql);
			$return['sql'] = $taskactivitysql;
			$return['transaction_activity_schedule'] = $taskactivityres;
				
			$tasks_cntssql = "SELECT count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = '".$type."'  AND transaction_id= '".$vendor_registersres->id."'  ".$comdition." AND tas.is_active = 0";
			$tasks_cntsres = $callConfig->getRow($tasks_cntssql);	
			$return['task_cnt'] = $tasks_cntsres;

			$vendor_documentsssql = "SELECT * FROM vendor_documents WHERE company_id = '".$get['company_id']."' AND vendor_user_id = '".$vendorres->id."' AND is_active = 0";
			$vendor_documentssres = $callConfig->getAllRows($vendor_documentsssql);
			$return['vendor_documents_sql'] = $vendor_documentsssql;
			$return['vendor_documents'] = $vendor_documentssres;

			
			// if($vendorres->id !=""){
			// 	$vendorssql = "SELECT id FROM vendors WHERE company_id = '".$get['company_id']."' AND email = '".$vendorres->email."' AND is_active = 0";
			// 	$venid = $callConfig->getRow($vendorssql);
			// 	$vendorres->created_vendor_id  =$venid->id; 
			// 	$return['vendor_registers'] = $vendorres;
			// 	$return['vendor_registers_sql'] = $vendor_registerssql;

			// 	// if($get['admin_type']!='1'){
			// 	// 	$comdition = " AND tas.assigned_to = '".$get['user_id']."'";
			// 	// }
			// 	$taskactivitysql = "SELECT tas.id,tas.action_date,tas.revised_action_date, tas.actual_action_date, tas.action_flag, tas.check_list, tas.current_status, tas.process_id, tas.task_name, tas.approval_user_id,  tas.assigned_to  FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers' AND transaction_id= '".$vendorres->id."' ".$comdition." AND tas.is_active = 0";
			// 	$taskactivityres = $callConfig->getAllRows($taskactivitysql);
			// 	$return['sql'] = $taskactivitysql;
			// 	$return['transaction_activity_schedule'] = $taskactivityres;
				
			// 	$tasks_cntssql = "SELECT count(*) as total_task_cnt, sum(if(current_status=".ST_APPROVED.",1,0)) as approve_cnt, sum(if(current_status=".ST_COMPLETE.",1,0)) as review_cnt, sum(if(current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, sum(if(current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'vendor_registers'  AND transaction_id= '".$vendor_registersres->id."'  ".$comdition." AND tas.is_active = 0";
			// 	$tasks_cntsres = $callConfig->getRow($tasks_cntssql);	
			// 	$return['task_cnt'] = $tasks_cntsres;
			// }
		}
	
		$vendor_agreementssql = "SELECT * FROM vendor_agreements WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$vendorres->id."' AND is_active = 0";
		$vendor_agreementsres = $callConfig->getAllRows($vendor_agreementssql);
		$return['vendor_agreements_sql'] = $vendor_agreementssql;
		$return['vendor_agreements'] = $vendor_agreementsres;

		$paymentmethodsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETHOD' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$paymentmethodres=$callConfig->getAllRows($paymentmethodsql);
		foreach($paymentmethodres as $pm){
			$paymentmethod[$pm->genc_detail_code] = $pm->genc_detail_name;
		}
		$return['payment_method'] = $paymentmethod;

		$salestypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'SALESTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$salestyperes=$callConfig->getAllRows($salestypesql);
		foreach($salestyperes as $st){
			$salestype[$st->genc_detail_code] = $st->genc_detail_name;
		}
		$return['salestype'] = $salestype;

		$paymenttermsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETERM' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$paymenttermres=$callConfig->getAllRows($paymenttermsql);
		foreach($paymenttermres as $pt){
			$pmtype[$pt->genc_detail_code] = $pt->genc_detail_name;
		}
		$return['paymenttype'] = $pmtype;

		$empsql = "SELECT display_name,email_id,billable,transaction_number FROM employees WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$get['id']."' AND is_active = 0";
		$empres = $callConfig->getAllRows($empsql);
		$return['employess'] = $empres;

		// $whrcat ="company_id = '".$get['company_id']."' AND is_active=0";
    	// $query=$callConfig->selectQuery('countries','id,country_name',$whrcat,'','','');
    	// $components6= (array)$callConfig->getAllRows($query);
    	// $return['countries'] = (array)$components6;

    	// $statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND (country_id = '".$vendorres->billing_country."' || country_id = '".$vendorres->shipping_country."' || country_id = '".$vendorres->bank_country."') AND is_active=0";
    	// $statesres = $callConfig->getAllRows($statessql);
    	// $return['states'] = $statesres;

    	// $citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '".$get['company_id']."' AND (state_id = '".$vendorres->billing_state."' || state_id = '".$vendorres->shipping_state."' || state_id = '".$vendorres->bank_state."') AND is_active=0";
    	// $citiesres = $callConfig->getAllRows($citiessql);
    	// $return['cities'] = $citiesres;

    	$tasks_categoriessql = "SELECT id,category_name FROM tasks_categories where company_id = '".$get['company_id']."' AND is_active = 0";
		$tasks_categoriesres = $callConfig->getAllRows($tasks_categoriessql);	
		$return['tasks_categories'] = $tasks_categoriesres;	

		$vendortypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$vendortyperes=$callConfig->getAllRows($vendortypesql);
		foreach($vendortyperes as $vt){
			$vendor_type[$vt->genc_detail_code] = $vt->genc_detail_name;
		}
		$return['vendor_type'] = $vendor_type;

		$vcitsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'VENDORCONTACTINFOTYP' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$vcitres=$callConfig->getAllRows($vcitsql);
		foreach($vcitres as $pm){
			$vendorcontactinfotype[$pm->genc_detail_code] = $pm->genc_detail_name;
		}
		$return['vendor_contact_info_type'] = $vendorcontactinfotype;

		$company_typessql = "select id,company_type_name FROM company_types WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$company_typesres = $callConfig->getAllRows($company_typessql);
		$return['company_types'] = $company_typesres;	

		$incotermssql = "select id,incoterm_name FROM incoterms WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$incotermsres = $callConfig->getAllRows($incotermssql);
		$return['incoterms'] = $incotermsres;	

		$payment_termssql = "SELECT id, payment_term_name FROM payment_terms WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$payment_termsres = $callConfig->getAllRows($payment_termssql);
		$return['payment_terms'] = $payment_termsres;

		$batypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'BANKACCTTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
		$batyperes=$callConfig->getAllRows($batypesql);
		foreach($batyperes as $pm){
			$bankacoounttype[$pm->genc_detail_code] = $pm->genc_detail_name;
		}
		$return['bank_acoount_type'] = $bankacoounttype;

		$tasquery = "SELECT COUNT(id) as total_task, SUM(if(current_status = '".ST_APPROVED."',1,0)) as total_approved  FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND approval_user_id = '".$get['user_id']."' AND is_active = 0";
		$tasres = $callConfig->getRow($tasquery);
		$return['transaction_activity_schedule_cnt'] = $tasres;

		$tasquery = "SELECT id, task_name, check_list, current_status, transaction_table, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
		$tasres = $callConfig->getAllRows($tasquery);
		$return['transaction_activity_schedule_list'] = $tasres;

		if($get['edittype'] == "SendVerificationProfile"){
			$tasquery = "SELECT * FROM `transaction_activity_schedule` WHERE company_id = '".$get['company_id']."' AND transaction_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."' AND is_active = 0";
			$tasres = $callConfig->getAllRows($tasquery);
			$return['transaction_activity_schedule'] = $tasres;
		}

		$spihitsql = "SELECT * FROM api_hits WHERE company_id = '".$get['company_id']."' AND application_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."'  AND is_active = 0 ORDER BY response_date_time DESC";
		$spihitres=$callConfig->getAllRows($spihitsql);
		$return['api_hits'] = $spihitres;

		$vendor_display_fieldssql = "SELECT id, field_list FROM vendor_display_fields WHERE company_id = '".$get['company_id']."' AND role_short_code = '".$get['working_role_short_code']."' AND is_active = 0";
		$vendor_display_fieldsres=$callConfig->getRow($vendor_display_fieldssql);
		$return['vendor_display_fields_sql'] = $vendor_display_fieldssql;
		$return['vendor_display_fields'] = $vendor_display_fieldsres;
		

		//Start Koteswar added this code  - 26-09-2025
		
		$gstregistrationtypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'GSTREGISTRATIONTYPE' AND company_id = '".$get['company_id']."' AND is_active = 0";
        $gstregistrationtyperes=$callConfig->getAllRows($gstregistrationtypesql);
        foreach($gstregistrationtyperes as $st){
            $gstregistrationtype[$st->genc_detail_code] = $st->genc_detail_name;
        }
        $return['gstregistrationtype'] = $gstregistrationtype;
       
        $countryid = $statesid =  array();
        $cvgstbdsql = "SELECT * FROM `customer_vendor_gst_billing_details` WHERE company_id = '".$get['company_id']."' AND customer_vendor_id = '".$get['id']."' AND transaction_table = '".$type."' AND is_active=0";
        $cvgstbdres = $callConfig->getAllRows($cvgstbdsql);
        if(!empty($cvgstbdres)){
            foreach($cvgstbdres as $cvgst){
                if($cvgst->billing_country !="" AND $cvgst->billing_country !="0"){
                    $countryid[$cvgst->billing_country] = $cvgst->billing_country;
                }
                if($cvgst->billing_state !="" AND $cvgst->billing_state !="0"){
                    $statesid[$cvgst->billing_state] = $cvgst->billing_state;
                }
                if($cvgst->shipping_country !="" AND $cvgst->shipping_country !="0"){
                    $countryid[$cvgst->shipping_country] = $cvgst->shipping_country;
                }
                if($cvgst->shipping_state !="" AND $cvgst->shipping_state !="0"){
                    $statesid[$cvgst->shipping_state] = $cvgst->shipping_state;
                }
            }
        }
        if(!empty($vendorres)){
            if($vendorres->bank_country !="" AND $vendorres->bank_country !="0"){
                $countryid[$vendorres->bank_country] = $vendorres->bank_country;
            }
            if($vendorres->bank_state !="" AND $vendorres->bank_state !="0"){
                $statesid[$vendorres->bank_state] = $vendorres->bank_state;
            }
        }
        $return['customer_vendor_gst_billing_details_sql'] = $cvgstbdsql;
        $return['customer_vendor_gst_billing_details'] = $cvgstbdres;
 
        $whrcat ="company_id = '".$get['company_id']."' AND is_active=0";
        $query=$callConfig->selectQuery('countries','id,country_name',$whrcat,'','','');
        $components6= (array)$callConfig->getAllRows($query);
        $return['countries'] = (array)$components6;
        $statesres = array();
        if(!empty($countryid)){
            $statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND country_id IN (".implode(',',$countryid).") AND is_active=0";
            $statesres = $callConfig->getAllRows($statessql);      
        }
        $return['states'] = $statesres;
 
        $citiesres = array();
        if(!empty($statesid)){
            $citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '".$get['company_id']."' AND state_id IN (".implode(',',$statesid).") AND is_active=0";
            $citiesres = $callConfig->getAllRows($citiessql);
        }
        $return['cities'] = $citiesres;
        //End Koteswar added this code  - 26-09-2025
		
        $vlistquery = "SELECT id, company_id, user_id,email, company, bank_account_no, ifsc_code, pan_no, gstin, tan_number, msme_number, vendor_verifications  FROM $type WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0 ORDER BY id DESC";
        $vlistrows = $callConfig->getAllRows($vlistquery);
        $return['rows_sql'] = $vlistquery;
        $return['rows'] = $vlistrows;
 
        $vidquery = "SELECT id, company_id, user_id,email, company, bank_account_no, ifsc_code, pan_no, gstin, tan_number, msme_number, vendor_verifications  FROM $type WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $get['id'] . "' AND is_active = 0 ORDER BY id DESC";
        $vidrow = $callConfig->getRow($vidquery);
        $return['data'] = $vidrow;

        $vendor_typessql = "SELECT id, type_name FROM vendor_types WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
        $vendor_typesres = $callConfig->getAllRows($vendor_typessql);
        $vendor_types = array();
		foreach($vendor_typesres as $pm){
			$vendor_types[$pm->id] = $pm;
		}
		$return['vendor_types'] = $vendor_types;

        $accounttypequery = "SELECT * FROM `general_code_details` WHERE genc_code LIKE '%BANKACCTTYPE%'";
		$accounttypedata = $callConfig->getAllRows($accounttypequery);
		$return['accounttyperes'] = $accounttypedata;

		$companytypequery = "SELECT id, company_type_name FROM `company_types` WHERE is_active = '0' ORDER BY `company_type_name`";
		$companytypedata = $callConfig->getAllRows($companytypequery);
		$return['companytyperes'] = $companytypedata;


        $application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_ADMIN_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_admin_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_HR_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_hr_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_FINANCE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_finanace_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PROJECT_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_project_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LOCATION_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_location_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_PURCHASE_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_purchase_application_users'] =$application_usersres;

			$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND id != '".$get['user_id']."' AND is_active = 0 AND (JSON_SEARCH(role_id, 'one', '".VENDOR_MODULE_ADMIN_ROLE."') IS NOT NULL OR JSON_SEARCH(role_id, 'one', '".VENDOR_LEGAL_ROLE."') IS NOT NULL) ";
			$application_usersres = $callConfig->getAllRows($application_userssql);
			$return['vendor_legal_application_users'] =$application_usersres;
			
			$locationssql = "SELECT id, location_name FROM locations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$locationsres=$callConfig->getAllRows($locationssql);
			$locations = array();
			foreach($locationsres as $pm){
				$locations[$pm->id] = $pm;
			}
			$return['locations'] = $locations;
			
			$document_listssql = "SELECT id,document_name FROM document_lists WHERE company_id = '".$get['company_id']."' AND category_id = 3 AND is_active = 0";
			$document_listsres = $callConfig->getAllRows($document_listssql);
			$return['document_lists'] = $document_listsres;

			if($get['VRRid'] !=""){
				$sql = "SELECT * FROM vendor_registration_requests WHERE company_id = '".$get['company_id']."' AND id = '".$get['VRRid']."' AND is_active = 0";
				$res = $callConfig->getRow($sql);
				$return['vendor_registration_requests'] = $res;
			}

		return $return;
	}

}
?>