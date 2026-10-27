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
    	}else{
    		$country_id = $vendor_registration_requestsres->country_id;
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

		$insertary=array('company_id'=>$post['company_id'],'company_name'=>$post['company_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$display_name,'user_email'=>strtolower($post['user_email']),'user_mobile'=>$post['user_mobile'],'legal_connect'=>$post['legal_connect'],'note'=>$post['note'],'registration_questions'=>$registration_questionsss,'web_site'=>$post['web_site'],'facsimile_number'=>$post['facsimile_number'],'country_id'=>$post['country_id'],'state_id'=>$post['state_id']);
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
			$option = "COM_INSERT_VENDORS&VTYPE=VR";
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

		$updateary=array('verification_profile_users'=>json_encode($verification_profile_users), 'send_verification_profile_status'=>'Y','updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"));
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
	        $insertuserarray = ['company' => $post['company'], 'email' => $post['email'], 'company_id' => $get['company_id'], 'display_name' => $post['display_name'], 'pan_no' => $post['pan_no'], 'first_name' => $post['first_name'], 'middle_name' => $post['middle_name'], 'last_name' => $post['last_name'], 'previous_verifications' => $previousres->previous_verifications ];
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
	            $updateary = ['previous_verifications' => json_encode($existingHistory), 'vendor_verifications' => json_encode($vendor_verification), 'pan_no' => $post['pan_no'], 'updated_by' => $post['user_id'], 'updated_date' => $now ];
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
	 		return $post;
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
	        $updateary = ['vendor_verifications' => json_encode($vendor_verification), 'previous_verifications' => json_encode($existingHistory), 'bank_name' => $post['bank_name'], 'bank_account_no' => $post['bank_account_no'], 'ifsc_code' => $post['ifsc_code'], 'updated_by' => $post['user_id'], 'updated_date' => $now ];
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
	        curl_close($ch);
	        return [ 'success' => false,'error' => curl_error($ch)];
	    }
	    curl_close($ch);
	    $return = json_decode($response, true);
	    if (!is_array($return)) {
	        return ['success' => false,'raw_response' => $response];
	    }
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
}
?>