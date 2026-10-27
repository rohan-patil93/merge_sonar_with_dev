	
<?php
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;
class userClass{ 
	function checkAdmin($post) {
		global $callConfig;
		$login_type = 'N';
	   //print_r($post); echo "kkkkk"; exit;
	   $params = [$query, $callConfig->selectQuery, $where];
	   $where="user_email='?'  and is_active ='0'";
	   $params = [$where, $post];
	   if($post['zqr_connect'] !=""){
	   		$where .=" AND zqr_connect = '?'";
	   		$params = [$where, $post];
	   }
	   //print_r($where);  exit;
	   $query=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'*',$where,'','','');
	   $params = [$query, $callConfig->selectQuery, $where];
	   //echo $query; exit;
	   $row=$callConfig->getRowNew($query);
	   //print_r($row); exit;
	   if($row->id!="" && $row->email_active==1) {
	   	$whr=" id='?' and  is_active='0'";
	   	$params = [$whr, $row->zqr_connect];
	   	$query_db=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
	   	$params = [$query_db, $callConfig->selectQuery, $whr];
		//echo $query_db; exit;
	   	$row_db=$callConfig->getRowNew($query_db);
		//print_r($row_db);
				//echo "<pre>"; 
	   	$row->db = $row_db->db_name;
	   	$wheres ="user_email='?' and user_password='?' and is_active ='0'";
	   	$params = [$wheres, $post, $callConfig->passwordEncrypt, $post];
	   	$query_user=$callConfig->selectQuery($row_db->db_name.'.application_users','*',$wheres,'','','');
	   	$params = [$query_user, $callConfig->selectQuery, $row_db->db_name, $wheres];
		//echo $query_user;
	   	$user = $callConfig->getRowNew($query_user);
	   	if($user->id != ''){
	   		$login_type = 'U';
	   		$user->db = $row_db->db_name;
			//print_r($post); exit;
	   		$timm =date("Y-m-d H:i:s");
					//echo $timm;
	   		$fieldnames=array('company_id'=>$user->company_id,'user_id'=>$user->id,'login_from'=>$timm,'login_ip'=>$post['ip'],'login_browser'=>$post['browser'], 'php_session'=>$post['php_session']);
					//print_r($fieldnames); exit;
	   		$query_last = "SELECT * FROM ?.application_user_logins where user_id =? ORDER BY id DESC LIMIT 0,1";
	   		$params = [$query_last, $row_db->db_name, $user->id];
	   		$last = $callConfig->getRowNew($query_last);
	   		$user->lastlogin =$last->login_from;
	   		$user->reset_password = $row->reset_password;
	   		$res=$callConfig->insertRecord($row_db->db_name.'.application_user_logins',$fieldnames);
	   		$params = [$res, $callConfig->insertRecord, $row_db->db_name, $fieldnames];
					//print_r($row); exit;
	   		unset($user->user_password);
					//unset($user->remember_token);
	   		$user->data_name = $row_db->db_name;
	   		$query_employee = "SELECT id FROM ?.employees where user_id =? AND is_active=0 ";
	   		$params = [$query_employee, $row_db->db_name, $user->id];
				//$user->query_employee = $query_employee;
				$employee = $callConfig->getRowNew($query_employee);
				$user->emp_id = $employee->id;
	   		if($post['mobile']=='Y'){
				$query_mobile = "SELECT id FROM ?.user_otp_authentication where user_id =? AND current_status =199 AND is_active=0 ";
				$params = [$query_mobile, $row_db->db_name, $user->id];
				$mobile = $callConfig->getRowNew($query_mobile);
				$user->base_url = $this->togetbaseurlcreate($post);
				$user->user_toke = '';
				$user->mobile_verification = 1;
				if($mobile->id !=''){
					$user->mobile_verification = 0;
				}
				
				$user->login_type = $login_type;
			}
			
	   		$return = $user;
	   	}else{
	   		$return ['error'] = '6002';
	   	}
	   }else{

	   	if($row->email_active === '0') {
				//echo "ravi".$row->email_active."fa";
	   		$return ['error'] = '6004';
	   	}else{
				//echo "ravi".$row->email_active."er";
	   		if($post['mobile']=='Y'){
				$wheres ="user_email='?' and user_password='?' and is_active ='0'";
				$params = [$wheres, $post, $callConfig->passwordEncrypt, $post];
				$query_user=$callConfig->selectQuery(MAINDB.'.mobile_user_registrations','*',$wheres,'','','');
				$params = [$query_user, $callConfig->selectQuery, $wheres];
					//echo $query_user;
				$user = $callConfig->getRowNew($query_user);
				if($user->id != ''){
					$login_type = 'M';
					$user->login_type = $login_type;
					$return = $user;
				}else{
					$return ['error'] = '6005';	
				}

			}else{
				$return ['error'] = '6005';
			}
	   		
	   	}
	   }

		//print_r($row);exit;
	   return $return;		

	}
	function togetbaseurlcreate($get){

    	global $callConfig;
    	$user_email = $get['chrUserName'];
    	$query = "SELECT * FROM ".MAINDB.".registered_groups WHERE id in (SELECT group_id FROM ".MAINDB.".user_registered_groups WHERE user_email = '?' AND is_active=0)";
    	$params = [$query, $user_email];
    	$data = $callConfig->getRowNew($query);
    	
   		$subdomain = '';
   		$host = $_SERVER['HTTP_HOST'];
   		//echo "Host: ".$host;//exit;
	    $parts = explode('.', $host);
	    $params = [$parts, $host];
	    if (count($parts) > 2) {
	      $subdomain = $parts[0];
	      if($subdomain == "www"){
	          $subdomain = $parts[1];
	      }
	    }
	    $httpshttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
	    $baseurl = $httpshttp."://".str_replace($subdomain,$data->domain_name,$host);
	    $params = [$baseurl, $httpshttp, $subdomain, $data->domain_name, $host];
	    return $baseurl;
    }
	function loginafterdetails($get){
		global $callConfig;
		//print_r($get); exit;
		//To Get login user Deatails 
		$return['callConfig'] = $callConfig;
		$whrs = "id= ".$get['user_id'];
	/*	$query = $callConfig->selectQuery('application_users','*',$whrs,'','','');
		echo $query; echo "<br>";
		$userrow = $callConfig->getRow($query);
		print_r($userrow);echo "<br>";*/
		$query = $callConfig->selectQueryNew('application_users','*',$whrs,'','','');
		//print_r($query);  echo "<br>";
		$userrow = $callConfig->getRowNew($query);
		//print_r($userrow); exit;
		if($userrow->country_id!=""){
			$whrs2 = "company_id= ".$get['company_id']." AND id=".$userrow->country_id;
			//$whrs2 = "company_id= ? AND id=".$userrow->country_id;
			//$params = [$whrs2, $get, $userrow->country_id];
			$query2 = $callConfig->selectQueryNew('countries','*',$whrs2,'','','');
			print_r($query2); exit;
			$currencysymboll = $callConfig->getRowNew($query2); 
			$userrow->countries  = $currencysymboll;
		}
		
		$return['users'] = $userrow;
		print_r($return); exit;
		$whrs2 = "company_id= ? AND user_id= ? AND is_active = 0";
		$params = [$whrs2, $get, $get];
		$query2 = $callConfig->selectQuery('application_user_module_roles','module_setup_id,role_id,role_short_code',$whrs2,'','','');

		$userrow2 = $callConfig->getAllRowsNew($query2);
		foreach($userrow2 as $userrow3){
			$return['application_user_module_roles'][$userrow3->module_setup_id] = $userrow3;
		}
		
		//To Get Config company Deatails
		$whrs = "company_id= ".$get['company_id'];
		$query = $callConfig->selectQuery('config','*',$whrs,'','','');


		$configrow = $callConfig->getRowNew($query);
		$return['configsetornot'] = (!isset($configrow->id))?"N":"Y";	
		if($return['configsetornot'] =="Y"){
			$whrs1 = "company_id= ? AND id=".$configrow->default_currency_id;
			$params = [$whrs1, $get, $configrow->default_currency_id];
			$query1 = $callConfig->selectQuery('currencies','currency_symbol',$whrs1,'','','');
			$currencysymboll = $callConfig->getRowNew($query1); 
			$configrow->currency_symbol  = $currencysymboll->currency_symbol;
			
			$whrs2 = "company_id= ? AND id=".$configrow->default_country_id;
			$params = [$whrs2, $get, $configrow->default_country_id];
			$query2 = $callConfig->selectQuery('countries','*',$whrs2,'','','');
			$currencysymboll = $callConfig->getRowNew($query2); 
			$configrow->countries  = $currencysymboll;
			$return['config'] = $configrow;
		}
		//To Get notification user Deatails
		//$return['notification'] = $this->toGetNotigficationList($get['user_id'],'',$get);
		//$return['notification'] = $this->toGetInboxCountUsingUserId($get);


		//To Get login user actionlog Deatails
		$whrs = "company_id = ? AND user_id= ".$get['user_id'];
		$params = [$whrs, $get, $get];
		$query = $callConfig->selectQuery('action_log','*',$whrs,'id DESC','0','5');
		$actionlogrow = $callConfig->getRowNew($query); 
		$return['actionlog'] = $actionlogrow;
		//To Get login all users  Deatails

		$query = $callConfig->selectQuery('application_users','id,display_name,first_name,middle_name,last_name,user_photo','','','','');
		$alluserrow = $callConfig->getAllRowsNew($query); 
		foreach ($alluserrow as  $value) {
			$name[$value->id] = $value->display_name!="" ?$value->display_name:$value->first_name." ? ".$value->last_name;
			$params = [$name, $value->id, $value->display_name, $value->display_name, $value->first_name, $value->middle_name, $value->last_name];
			$images[$value->id]= $value->user_photo;
		} 
		$return['allusers'] = array('name'=>$name,'image'=>$images);
		//To Get error message Deatails
		$whrs = "is_active= 0";
		$query = $callConfig->selectQuery('application_messages','message_number,message_description',$whrs,'','','');
		$error_messagerow = $callConfig->getAllRowsNew($query); 
		foreach ($error_messagerow as  $errordata) {
			$error_data[$errordata->message_number] =$errordata->message_description;
		}
		$return['error_message'] = $error_data;

		//To Get error pages Deatails
		$whrs = "is_active= 0";
		$query = $callConfig->selectQuery('application_pages','*',$whrs,'','','');
		$pagesrow = $callConfig->getAllRowsNew($query); 
		$return['application_pages'] = $pagesrow;
		

		$whrs = "company_id = ? AND is_active = 0 ";
		$params = [$whrs, $get];
		$query = $callConfig->selectQuery('module_setup','*',$whrs,'','','');
		$module_setuprow = $callConfig->getAllRowsNew($query); 
		foreach($module_setuprow as $module){
			$module_id[] = $module->module_id;
			$return['module_setup'][$module->module_id] = $module;
		}
		$return['module_id'] = $module_id;
		//To Get module wise menu Deatails		
		//$query = "SELECT * FROM application_menu WHERE company_id =".$get['company_id']." AND aplication_module IN (".implode(",",$module_id).") AND is_active = 0 ORDER BY display_priority ASC";
		
		//$defaultmoduleid = $callConfig->module_id!=""?$callConfig->module_id:$module_id[0];
		$return['callConfig2'] = $userrow2[0];
		if($callConfig->module_id!=""){
			$defaultmoduleid = $callConfig->module_id;
			$working_role_short_code = $callConfig->working_role_short_code;
		}elseif($userrow2[0]->module_setup_id!="" AND $userrow2[0]->module_setup_id !=0){
			$defaultmoduleid = $userrow2[0]->module_setup_id;
			$working_role_short_code =  $userrow2[0]->role_short_code;
		}else{
			$defaultmoduleid = $module_id[0];
		}
		$return['default_module_id'] = $defaultmoduleid;
		$return['working_role_short_code'] = $working_role_short_code;
		$modulearray[] = $defaultmoduleid;
		$linkingmodulesql = "SELECT msl.module_id,ms.module_name FROM module_setup_linkings msl, module_setup ms WHERE msl.company_id =? AND ms.company_id=msl.company_id AND ms.module_id=msl.module_id  AND msl.parent_module_id = '?' AND ms.is_active=msl.is_active AND msl.is_active = 0";
		$params = [$linkingmodulesql, $get, $defaultmoduleid];
		$linkingmoduleres = $callConfig->getAllRowsNew($linkingmodulesql); 
		foreach($linkingmoduleres as $linkingmoduleres){
			$modulearray[] = $linkingmoduleres->module_id;
		}
		//To Get user get role permission Deatails
		$get['defaultmoduleid'] = $defaultmoduleid;
		$get['working_role_short_code'] = $working_role_short_code;
		//$return['rolepermission'] = $this->toGetRoledata($get['user_id'],$get);
		//To Get login module_setup Deatails


		$query = "SELECT * FROM application_menu WHERE company_id =? AND aplication_module IN(".implode(",",(array)$modulearray).") AND is_active = 0 ORDER BY display_priority ASC";
		$params = [$query, $get, $modulearray];
		$menurow = $callConfig->getAllRowsNew($query); 
		$return['application_menu'] = $menurow;
		//To Get  all Status  Deatails
		$query = $callConfig->selectQuery('status','*','','','','');
		$allstatus = $callConfig->getAllRowsNew($query);
		foreach($allstatus as $value){
			$status[$value->name_value]= $value;
		}
		$return['status'] = $status;

		//To Get  all companies  Deatails
		$query = $callConfig->selectQuery('companies','*','','','','');
		$allcompanies = $callConfig->getAllRowsNew($query);
		foreach($allcompanies as $value){
			$companies[$value->id]= $value->name;
			if($userrow->company_id == $value->id){
				$return['user_company'] = $value; 
			}
		}
		$return['companies'] = $companies; 
		
		//To Get login employee Deatails
		$whrs = "company_id =? AND user_id= ? AND is_active=0";
		$params = [$whrs, $get, $get];
		$query = $callConfig->selectQuery('employees','*',$whrs,'','','');
		$employeerow = $callConfig->getRowNew($query); 
		$return['employees'] = $employeerow;
		// To Get employee_daily_attendances Details
		if($employeerow->id != ""){
			$whrs = "company_id =? AND employee_id= ? AND attendace_date ='".date('Y-m-d')."' AND is_active=0";
			$params = [$whrs, $get, $employeerow->id];
			$query = $callConfig->selectQuery('employee_daily_attendances','*',$whrs,'','','');
			$employee_daily_attendances = $callConfig->getRowNew($query); 
		}
		$return['clock_data'] = $employee_daily_attendances;

		// To GET field_secruity_configurations details
		$whrs = "company_id = ? AND is_active=0 ";
		$params = [$whrs, $get];
		$query = $callConfig->selectQuery('field_secruity_configurations','*',$whrs,'','','');
		$allfield = $callConfig->getAllRowsNew($query);
		foreach($allfield as $value){
			$fileds[$value->configuiration_code]= $value;
		}
		$return['field_secruity'] = $fileds;
		// To GET status details
		$whrs = "company_id = ? AND is_active=0 ";
		$params = [$whrs, $get];
		$query = $callConfig->selectQuery('status','id,name_value,name,background_color,color',$whrs,'','','');
		$allstatus = $callConfig->getAllRowsNew($query);
		//print_r($allstatus);

		foreach($allstatus as $value){
			$name_value = $value->name_value;
			$sta[$name_value]['name']= $value->name;
			$sta[$name_value]['background_color']= $value->background_color;
			$sta[$name_value]['color']= $value->color;
		}
		$return['status'] = $status; 
		//Get menu role premssion data
		
		if($employeerow->id!=""){	
			$query = "SELECT * FROM pay_areas WHERE id IN ( SELECT pay_area_id FROM pay_area_employees WHERE company_id =? AND emp_id = '?' AND is_active = 0) "; 		
			$params = [$query, $get, $employeerow->id];
			$return['emp_pay_area'] = $callConfig->getRowNew($query); 
			
			$query = "SELECT count(id) as adminval FROM payroll_admins WHERE company_id =? AND  emp_id = '?' AND is_active = 0"; 		
			$params = [$query, $get, $employeerow->id];
			$pay_admin_emp = $callConfig->getRowNew($query); 
			$return['pay_admin_emp'] = $pay_admin_emp->adminval;
		}
    	$setup_page_logssql = "SELECT setup_page_id,current_status FROM `setup_page_logs` WHERE company_id = '?' AND is_active=0 ORDER BY created_date DESC LIMIT 1";
    	$params = [$setup_page_logssql, $get];
    	$setup_page_logsres = $callConfig->getRowNew($setup_page_logssql);
    	$return['setup_page_logs'] = $setup_page_logsres;

    	$today = date('Y-m-d');
    	$query = "SELECT * FROM emp_clock_status WHERE  company_id =? AND user_id = ? AND  effective_from_date <= '?' AND effective_to_date >= '?' AND is_active = 0";		
    	$params = [$query, $get, $get, $today, $today];
		$return['emp_clock_status'] = $callConfig->getRowNew($query);
		$return['emp_clock_status_query'] = $query;
		return $return;
	}
	/*
	function getMainDomainName(){
		$url = SITEURL;
		$host = parse_url($url, PHP_URL_HOST);
		$parts = explode('.', $host);
		$params = [$parts, $host];
		$mainDomain = $parts[count((array)$parts)-2] . '.' . $parts[count((array)$parts)-1];
		$params = [$mainDomain, $parts, $parts, $parts, $parts];
		return $mainDomain;
	}
	function confrimbutton($id){
		global $callConfig;

		$emailcheckalreadyactivesql = "SELECT urg.id, urg.user_email, da.db_name FROM ".MAINDB.".".TBL_USER_REGISTIRED_GROUPS." urg, ".MAINDB.".database_allocations da WHERE urg.id ='?' AND urg.zqr_connect = da.id  AND urg.email_active = 0";
		$params = [$emailcheckalreadyactivesql, $id];
		$returnarray['emailcheckalreadyactivesql'] = $emailcheckalreadyactivesql;
		$emailcheckalreadyactiveres = $callConfig->getRowNew($emailcheckalreadyactivesql);
		$returnarray['emailcheckalreadyactiveres'] = $emailcheckalreadyactiveres;
		if(isset($emailcheckalreadyactiveres->id)){

			$domainregister = "SELECT id, default_module_id FROM ".MAINDB.".domain_registrations WHERE user_email ='?' AND is_active = 0";
			$params = [$domainregister, $emailcheckalreadyactiveres->user_email];
			$domainregisres = $callConfig->getRowNew($domainregister);
			$returnarray['domainregisres'] = $domainregisres;


			$dbname = $emailcheckalreadyactiveres->db_name;
			$time =date("Y-m-d H:i:s");
			$fied = array('email_active' =>1,'email_active_date' => $time);
			$res=$callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fied,'id',$id);
			$params = [$res, $callConfig->updateRecord, $fied, $id];
			$returnarray['res'] = $res;					
			
			$application_userssql = "SELECT id, company_id, display_name, user_email, user_mobile, role_id FROM ?.application_users WHERE user_email ='?' AND is_active = 0";
			$params = [$application_userssql, $dbname, $emailcheckalreadyactiveres->user_email];
			$application_usersres = $callConfig->getRowNew($application_userssql);
			$returnarray['application_userssql'] = $application_userssql;
			$returnarray['application_usersres'] = $application_usersres;
			$rolelist = json_decode($application_usersres->role_id,1);
			$returnarray['role_id'] = $rolelist;
			//send email -----------------------------------------
			$emailarray['databasename'] = $dbname;
			$emailarray['session_company_id'] = $application_usersres->company_id;
			$emailarray['user_display_name'] = $application_usersres->display_name;
			$emailarray['user_email_id'] = $application_usersres->user_email;
			$emailarray['user_phone_number'] = $application_usersres->user_mobile;
			$emailarray['created_by'] = $application_usersres->id;
			$emailarray['default_module_id'] = $domainregisres->default_module_id;
			if(in_array(ADMIN_ROLES, (array)$rolelist)){
				$emailsend = $this->sendNotificationAndEmails('',$emailarray,2);	
			}else{
				$emailsend = $this->sendNotificationAndEmails('',$emailarray,4);	
			}
			$returnarray['emailsend'] = $emailsend;
		}
		return $returnarray;
	}
	function logout($data){
		global $callConfig;
		$timm =date("Y-m-d H:i:s");
		$fieldnames=array('login_to'=>$timm);
			//print_r($data); exit;
		$res=$callConfig->updateRecord('application_user_logins',$fieldnames,'user_id',$data);
		return $res;
	}
	function genrateRandomString($n) { 
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$randomString = ''; 
		//echo "random :".$n;
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($characters) - 1); 
			$randomString .= $characters[$index]; 
		} 
		//echo "randomout :".$randomString;
		return $randomString; 
	} 
	function forgotpassword($email){
		global $callConfig;
		$whrs="user_email= '?'";
		$params = [$whrs, $email];
		$querys=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'id,zqr_connect',$whrs,'','','');
		$params = [$querys, $callConfig->selectQuery, $whrs];
		//	echo $querys; exit;
		$rowg=$callConfig->getRowNew($querys);
		//print_r($rowg); exit;
		if($rowg->id != ''){
			$whr = "id = '?'";
			$params = [$whr, $rowg->zqr_connect];
			$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
			$params = [$query, $callConfig->selectQuery, $whr];
			$row=$callConfig->getRowNew($query);
			$db_name = $row->db_name;
		//print_r($row); exit;
			$gen_password = $this->genrateRandomString(7);
			$password = $callConfig->passwordEncrypt($gen_password);
			$db_id = $row->id;
		//echo $db_id; exit;
			$fied = array('reset_password' => '0');
			$res1 = $callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fied,'id',$rowg->id);
			$params = [$res1, $callConfig->updateRecord, $fied, $rowg->id];
			$fied = array('user_password' =>$password);
			$whrcon = "user_email='?'";
			$params = [$whrcon, $email];
		//echo $whrcon; exit;
			$res1 = $callConfig->updateRecord($db_name.'.application_users'	,$fied,'','',$whrcon);
			$params = [$res1, $callConfig->updateRecord, $db_name, $fied, $whrcon];
		//echo $res1; exit;
			$ausql = "SELECT id, company_id, display_name, user_email, user_mobile, role_id FROM ?.application_users WHERE user_email ='?' AND is_active = 0";
			$params = [$ausql, $db_name, $email];
			$aures = $callConfig->getRowNew($ausql);

			$apmql = "SELECT id, application_module_id FROM ?.application_user_module_roles WHERE user_id ='?' AND is_active = 0";
			$params = [$apmql, $db_name, $email];
			$apres = $callConfig->getRowNew($apmql);

			$emailarray['to_company_id'] = $aures->company_id;
			$emailarray['session_company_id'] = $aures->company_id;
			$emailarray['user_temp_password'] = $gen_password;
			$emailarray['user_company_id'] = $aures->company_id;
			$emailarray['user_display_name'] = $aures->display_name;
			$emailarray['user_email_id'] = $aures->user_email;
			$emailarray['user_phone_number'] = $aures->user_mobile;
			$emailarray['user_id'] = $aures->id;
			$emailarray['default_module_id'] = $apres->application_module_id;
			$emailarray['created_by'] = $aures->id;
			$emailarray['user_registered_group_id'] = $rowg->id;
			$emailarray['databasename'] = $db_name;
 		//	print_r($emailarray); exit;
			// if(in_array(ADMIN,$roles)){
			// 	$email  = $this->sendNotificationAndEmails($notarry,$emailarray,1);
			// }else{
			$notarry = array();
			$email  =$this->sendNotificationAndEmails($notarry,$emailarray,900);

		//print_r($lenn); exit;
		}else{
			$lenn['error'] = '6005';
		}
		return $lenn;
	}
	function NewUserformate($post,$db_name){
		global $callConfig;
	 	//echo "ravi"; exit;
	 	$db_name = $db_name !=""?$db_name:$callConfig->dbname;
	 	$post['db_name'] = $db_name;
		$whr="db_name = '?'";
		$params = [$whr, $db_name];
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
		$params = [$query, $callConfig->selectQuery, $whr];
		$row=$callConfig->getRowNew($query);
		//print_r($row); exit;
		//$gen_password = $this->genrateRandomString(7);
		$gen_password = "PensAndPencils123";
		$password = $callConfig->passwordEncrypt($gen_password);
		$db_id = $row->id;
		$whrs="zqr_connect= '?'";
		$params = [$whrs, $db_id];
		$querys=$callConfig->selectQuery(MAINDB.'.'.TBL_REGISTIRED_GROUPS,'*',$whrs,'','','');
		$params = [$querys, $callConfig->selectQuery, $whrs];
		$rowg=$callConfig->getRowNew($querys);
		$user_registired_groups = array('group_id'=>$rowg->id,'user_email'=>$post['user_email'],'zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
		//print_r($user_registired_groups); exit;

		$res_rg_group_user = $callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$user_registired_groups);
		$params = [$res_rg_group_user, $callConfig->insertRecord, $user_registired_groups];
		$post['res_rg_group_user'] = $res_rg_group_user;
		//$user_id = base64_encode($res_rg_group_user['LASTID']);
		$fied = array('user_password' =>$password);
		$whrcon = "user_email='?'";
		$params = [$whrcon, $post];
		$res1 = $callConfig->updateRecord($db_name.'.application_users'	,$fied,'','',$whrcon);
		$params = [$res1, $callConfig->updateRecord, $db_name, $fied, $whrcon];
		// $data['email_template'] = 2;
		// $data['email_id'] = $post['user_email'];
		// $data['password'] = $gen_password;
		// $data['user_id'] =  $user_id;
		// $data['email_content_id'] = 1;
		// $email_array = $this->emailtemplate($data);
		// $file_name = 'email'.'_'.time().uniqid().'.'.'html';
		// $email_array ['content'] = $file_name;
		// $email_array['revicer'] = array($post['user_email']);
		// $email_array['email_type'] = 1;
		// $email = $email_array['mesg'];

		//print_r($email_array); exit;
		// $file_path='uploads/email/'.$file_name;
		// file_put_contents($file_path, $email);
		// fclose($file_path);
		// $lenn = $this->sendEmailslist($email_array);
		//print_r($lenn); exit;
		$post['temp_password'] = $gen_password;
		$post['domain_name'] = $rowg->domain_name;
		$post['user_registered_group_id'] = $res_rg_group_user['LASTID'];
		$notarry = array();
		if($post['user_type'] != '5'){
			$useridd[$post['created_by']] = $post['created_by'];
			$msglist = $post['display_name']." User successfully created";
			$params = [$msglist, $post];
			$notarry = array('company_id'=>$post['company_id'],'notification_message'=>$msglist, 'id'=>'','application_table_name'=>'application_users','type'=>'INFORMATION', 'notification_users'=>$useridd,'option'=>'','assigner_id'=>$post['created_by']);
			$emill = $this->sendNotificationAndEmails($notarry,$post,$post['template_reference_id']);
		}
		//$emill = $this->sendNotificationAndEmails($notarry,$post,$post['template_reference_id']);		
		return $emill;
	}
	function NewUserEmailresent($get){
		global $callConfig;
	 	//echo "ravi"; exit;
		$whr = "id = '?'";
		$params = [$whr, $get];
		$query=$callConfig->selectQuery('application_users','*',$whr,'','','');
		//echo $query;
		$row=$callConfig->getRowNew($query);
		$ap_email = $row->user_email;
		//$gen_password = $this->genrateRandomString(7);
		$gen_password = "PensAndPencils123";
		$password = $callConfig->passwordEncrypt($gen_password);

		$whr = "user_email = '?'";
		$params = [$whr, $ap_email];
		$query1=$callConfig->selectQuery(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,'*',$whr,'','','');
		$params = [$query1, $callConfig->selectQuery, $whr];
		//echo $query;
		$usgrow=$callConfig->getRowNew($query1);
		//$user_id = base64_encode($usgrow->id);
		$fied = array('user_password' =>$password);
		$whrcon = "id='?'";
		$params = [$whrcon, $get];
		$res1 = $callConfig->updateRecord('application_users',$fied,'','',$whrcon);

		$rolesid = implode("','",(array)json_decode($row->role_id,1));
		$query1 = "SELECT application_module FROM application_roles WHERE company_id ='?' AND role_short_code IN('?') AND  is_active = 0";
		$params = [$query1, $row->company_id, $rolesid];
		$result1 = $callConfig->getRowNew($query1);

		$emailarray['to_company_id'] = $row->company_id;
		$emailarray['session_company_id'] = $row->company_id;
		$emailarray['temp_password'] = $gen_password;
		$roles = json_decode($userres->role_id,1);
		$emailarray['user_company_id'] = $row->company_id;
		$emailarray['user_display_name'] = $row->display_name;
		$emailarray['user_email_id'] = $row->user_email;
		$emailarray['user_phone_number'] = $row->user_mobile;
		$emailarray['user_id'] = $row->id; 
		$emailarray['default_module_id'] = $result1->application_module;
		$emailarray['created_by'] = $get['user_id'];
		$emailarray['user_registered_group_id'] = $usgrow->id;
		// if(in_array(ADMIN,$roles)){
		// 	$email  = $this->sendNotificationAndEmails($notarry,$emailarray,1);
		// }else{
		$email  =$this->sendNotificationAndEmails($notarry,$emailarray,3);
		//}



		// $data['email_template'] = 2;
		// $data['email_id'] = $ap_email;
		// $data['password'] = $gen_password;
		// $data['user_id'] =  $user_id;
		// $data['email_content_id'] = 1;
		// $email_array = $this->emailtemplate($data);
		// $file_name = 'email'.'_'.time().uniqid().'.'.'html';
		// $email_array ['content'] = $file_name;
		// $email_array['revicer'] = array($ap_email);
		// $email_array['email_type'] = 1;
		// $email = $email_array['mesg'];

		// $file_path='uploads/email/'.$file_name;
		// file_put_contents($file_path, $email);
		// fclose($file_path);
		// $lenn = $this->sendEmailslist($email_array);
		//print_r($lenn); exit;
		return $email;
	}
	function insertAdminUser($post){
		global $callConfig;
		$returnarray['callConfig'] = $callConfig;

		$returnarray = array(); 
		//if($post['email_check']!=1){
			$whr="allcoated_date  IS NULL";
			$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
			$params = [$query, $callConfig->selectQuery, $whr];

			$row=$callConfig->getRowNew($query);

			if(isset($row->id) AND $row->id !=""){
				
				if($post['domain_name'] !=""){
					$subscription_agreement = $post['subscription_agreement'] == "Y"?"0":"1";
					$privacy_policy = $post['privacy_policy'] == "Y"?"0":"1";
					$post_array = array("default_module_id"=>$post['default_module_id'],"first_name"=>$post['first_name'],"last_name"=>$post['last_name'],"company_name"=>$post['company_name'],"phone_number"=>$post['phone_number'],"domain_name"=>$post['domain_name'],"user_email"=>$post['email'],"user_password"=>$post['user_password'],"subscription_agreement"=>$post['subscription_agreement'],"privacy_policy"=>$post['privacy_policy'],"register_ip"=>$post['ip'],"register_browser"=>$post['browser'],"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
					$res = $callConfig->insertRecord(MAINDB.'.domain_registrations',$post_array);
					$params = [$res, $callConfig->insertRecord, $post_array];
					$returnarray['domain_registrations'] = $res;
				}

				$db_id = $row->id;
				$db_name = $row->db_name;

				$subscription_agreement = $post['subscription_agreement'] == "Y"?"0":"1";
				$privacy_policy = $post['privacy_policy'] == "Y"?"0":"1";
				$post_array = array("default_module_id"=>$post['default_module_id'],"first_name"=>$post['first_name'],"last_name"=>$post['last_name'],"company_name"=>$post['company_name'],"phone_number"=>$post['phone_number'],"domain_name"=>$post['domain_name'],"user_email"=>$post['email'],"user_password"=>$post['user_password'],"subscription_agreement"=>$post['subscription_agreement'],"privacy_policy"=>$post['privacy_policy'],"register_ip"=>$post['ip'],"register_browser"=>$post['browser'],"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$res = $callConfig->insertRecord('domain_registrations',$post_array);
			// DB Allocations
				$time = date("Y-m-d H:i:s");
				$fied = array('is_active'=>'0','allcoated_date' =>$time);
				
				$res1 = $callConfig->updateRecord(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,$fied,'id',$db_id);
				$params = [$res1, $callConfig->updateRecord, $fied, $db_id];
				$fieldnames=array('user_name'=>$post['display_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'user_mobile'=>$post['user_mobile'],'user_password'=>$callConfig->passwordEncrypt($post['password']),'user_email'=>$post['email'],'current_status'=>'0','is_active' =>'0');

				$registired_groups_array = array('group_name'=>$post['name'].'_'.$db_id,'primary_email'=>$post['email'],'company_name'=>$post['company_name'],'domain_name'=>$post['domain_name'],'no_of_lisc'=>'1','no_of_used_lisc'=>'1','zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
				$params = [$registired_groups_array, $post, $db_id, $post, $post, $post, $db_id];
				$res_rg_group = $callConfig->insertRecord(MAINDB.'.'.TBL_REGISTIRED_GROUPS,$registired_groups_array);
				$params = [$res_rg_group, $callConfig->insertRecord, $registired_groups_array];

			// user registaion
				$fieldnames['zqr_connect'] = $db_id;
				$res=$callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTRATIONS,$fieldnames);
				$params = [$res, $callConfig->insertRecord, $fieldnames];

		    // TBL USER REGISTIRED GROUPS
				$user_registired_groups = array('group_id'=>$res_rg_group['LASTID'],'user_email'=>$post['email'],'zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
				$res_rg_group_user = $callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$user_registired_groups);
				$params = [$res_rg_group_user, $callConfig->insertRecord, $user_registired_groups];
				
				//company create
				$returnarray['TBL_DATABASE_ALLOCATIONS'] = $row;
				$returnarray['db_name'] = $db_name.'.companies';
				$params = [$returnarray, $db_name];

				$companiessql = array('company_id'=>$post['company_id'],'name'=>$post['company_name'],'incharge_name'=>$post['display_name'],'telephone'=>$post['user_mobile'],'email'=>$post['email'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
				$returnarray['companiessql'] = $companiessql;
				$companiesres=$callConfig->insertRecord($db_name.'.companies',$companiessql);
				$params = [$companiesres, $callConfig->insertRecord, $db_name, $companiessql];
				$companyid = $companiesres['LASTID'];
				$returnarray['companiesres'] = $companiesres;
				
				$updatefied = array('company_id' =>$companyid);
				$res1 = $callConfig->updateRecord($db_name.'.companies',$updatefied,'id',$companyid);
				$params = [$res1, $callConfig->updateRecord, $db_name, $updatefied, $companyid];
				$returnarray['res1'] = $res1;

				$configary = array('company_id'=>$post['company_id'],'name'=>$post['company_name'],'admin_email'=>$post['email'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
				$configres=$callConfig->insertRecord($db_name.'.config',$configary);
				$params = [$configres, $callConfig->insertRecord, $db_name, $configary];
				$returnarray['config'] = $configres;

				//Replicate tables form super DB
				
				//application_users
				$fieldnames=array('company_id'=>$companyid,'first_name'=>$post['name'],'`user_password`'=>$callConfig->passwordEncrypt($post['password']),'user_email'=>$post['email'],'user_activated'=>'Y','current_status'=>'0','is_active' =>'0');
				if($post['display_name'] != ""){
					$fieldnames['display_name'] = $post['display_name'];
					$fieldnames['user_name'] = $post['display_name'];
				}
				if($post['first_name'] != ""){
					$fieldnames['first_name'] = $post['first_name'];
				}
				if($post['last_name'] != ""){
					$fieldnames['last_name'] = $post['last_name'];
				}
				if($post['user_mobile'] != ""){
					$fieldnames['user_mobile'] = $post['user_mobile'];
				}
				$res=$callConfig->insertRecord($db_name.'.application_users',$fieldnames);
				$params = [$res, $callConfig->insertRecord, $db_name, $fieldnames];
				$res_id = $res['LASTID'];
				$returnarray['application_users'] = $res;
				$rtctab = $this->replicateListofCommonTables(array('db_name'=>$db_name,'user_id'=>$res_id));
				$returnarray['replicateListofCommonTables'] = $rtctab;
				//AND $rtctab['completed'] =='Y'
				if(isset($res['LASTID'])){
					$query = "SELECT application_module,id,role_short_code FROM ?.application_roles where role_short_code = 'ADMIN'";	
					$params = [$query, $db_name];

									
					$returnarray['application_roles'] = $query;
					$roleres = $callConfig->getRowNew($query);
					$returnarray['application_rolesroleres'] = $roleres;
					$insertarray = array("company_id"=>$companyid,"user_id"=>$res['LASTID'],"module_setup_id"=>$roleres->application_module,"application_module_id"=>$roleres->application_module,"role_id"=>$roleres->id,"role_short_code"=>$roleres->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
					$insert = $callConfig->insertRecord($db_name.'.application_user_module_roles',$insertarray);
					$params = [$insert, $callConfig->insertRecord, $db_name, $insertarray];
					$rolesids[] = $roleres->role_short_code;	



					$rolesid = json_encode($rolesids);
					$updatefied = array('role_id' =>$rolesid);
					$res1 = $callConfig->updateRecord($db_name.'.application_users',$updatefied,'id',$res['LASTID']);
					$params = [$res1, $callConfig->updateRecord, $db_name, $updatefied, $res];
					$returnarray['application_users_role'] = $res1;

					if($post['default_module_id'] !=""){
						$updatefied = array('is_active' =>'0');
						$res1 = $callConfig->updateRecord($db_name.'.module_setup',$updatefied,'module_id',$post['default_module_id']);
						$params = [$res1, $callConfig->updateRecord, $db_name, $updatefied, $post];
						$returnarray['module_setup'] = $res1;
					}

					$emailarray['user_registered_group_id'] = $res_rg_group_user['LASTID'];
					$emailarray['host_domain_name'] = $post['host_domain_name'];
					$emailarray['databasename'] = $db_name;
					$emailarray['user_company_id'] = $companyid;
					$emailarray['session_company_id'] = $post['company_id'];
					$emailarray['temp_password'] = $post['password']!=""?$post['password']:'PensAndPencils123';
					$emailarray['send_user_id'] = array($res_id);
					$emailarray['creater_user_id'] = array($post['user_id']);
					$emailarray['to_company_id'] = $companyid;
					$emailarray['user_display_name'] = $post['display_name'];
					$emailarray['user_email_id'] = $post['email'];
					$emailarray['admin_email_id'] = $post['email'];
					$emailarray['user_phone_number'] = $post['user_mobile'];
					$emailarray['created_by'] = $post['user_id'];
					$emailarray['domain_name'] = $post['domain_name'];
					$emailarray['default_module_id'] = $post['default_module_id'];
					//echo "Ravi --- ";
					
					//echo "<pre>"; print_r($rtctab); echo "</pre>"; exit;
					$emailsend = $this->sendNotificationAndEmails('',$emailarray,1);
					$returnarray['emailarray'] = $emailarray;
					$returnarray['emailsend'] = $emailsend;

				}

			//}else{
			//	$res_rg_group_user['LASTID']=11;
			//}
	
		}else{
			$returnarray['error'] = '9094';
		}
        return $returnarray;
    }
    function replicateListofCommonTables($post){
   		global $callConfig;
   		//echo "<pre>"; print_r($post); echo "</pre>"; exit;
   		$maxtablecnt = 0; 
   		$comman_table = array('application_roles','module_setup','setup_pages','module_setup_linkings','email_template_types','email_content','application_filters','filter_tables','application_labels','application_menu','application_menu_permission','application_menu_settings','application_messages','application_no_permission_pages','application_pages','application_page_list','application_process','application_process_pages','application_process_role','application_reports','application_serial_numbers','application_tours','blood_groups','document_categories','document_lists','dynamic_mapping_tables','dynamic_reference_tables','general_codes','general_code_details','industries','it_declaration','it_declaration_categories','languages','project_type','races','salary_categories','status','status_progress','status_progress_actions','system_earnings','system_other_earnings','veterans','workflow_configuration','workflow_configurations_new','workflow_configuration_details', 'company_types', 'currencies', 'attendance_key_types','employees_display_fields','candidate_display_fields','property_types','hsn_and_sac_codes','uom','uom_conversions','payment_terms','tax_authorities_rates_split','tax_authorities','tax_computations','chart_of_accounts','journal_settings','journal_type','stock_adjustment_reasons','additional_charges','application_missing_data_check','offer_details','application_query','nationalities','religions','employment_statuses','qualification_levels','job_categories','bulk_transfer_categories','company_key_links','company_key_link_categories','document_categories','training_categories','emp_hr_services','marital_statuses','attedenace_regularizaton_reasons','survey_setup','display_attributes', 'display_attribute_types','role_permissions', 'my_reports', 'emp_state_insaurance', 'emp_state_sub_insaurance', 'emp_provident_funds', 'professional_tax','process_task_settings','tasks_categories'); 

   		foreach($comman_table as $table){
   				
   			$query="show COLUMNS from ".DUMPTABLE.".copy_".$table;
   			$params = [$query, $table];
   			$dataa = $callConfig->getAllRowsNew($query);
   			$fields = array();
   			foreach ($dataa as  $data) {
   				$fields[] = $data->Field;
   			}
   			//echo "<pre>"; print_r($fields); echo "</pre>"; exit;
   			$created_date = "'".date('Y-m-d H:i:s')."'";
   			$params = [$created_date];
   			$mianquery = "INSERT INTO ? (".implode(", ",$fields).") ";
   			$params = [$mianquery, $post, $table, $fields];
   			$subquery =" SELECT ".implode(", ",$fields)." FROM ".DUMPTABLE.".copy_? WHERE company_id = 1";
   			$params = [$subquery, $fields, $table];
   			$subquery = str_replace("created_by", $post['user_id'], $subquery);
   			$subquery = str_replace("created_date", $created_date, $subquery);	

   			$query =  $mianquery." ".$subquery; 
   			$params = [$query, $mianquery, $subquery];
   			//echo "<pre>"; echo $query; echo "</pre>"; exit;
   			$tquery = "TRUNCATE TABLE ?".$table;
   			$params = [$tquery, $post, $table];
   			$tres = $callConfig->executeQuery($tquery);
   			$res[$table]['query'] = $query;
   			$rres = $callConfig->executeQuery($query);
   			$res[$table]['data'] = $rres;
   			//echo $table."----"; print_r($rres); exit;
   			$maxtablecnt++;
   		}
   		if(count((array)$comman_table) == $maxtablecnt){
   			$res['completed']  == "Y";
   		}
   		$res['completed_cnt']  = $maxtablecnt;
   		$res['completed_total_cnt']  = count((array)$comman_table);
   		//echo "Done"; exit;
   		return $res;
   	}	
	function getCountAllTablesDataCount($tablename,$fieldname,$where){
		global $callConfig;
		if($where!="")
			$whr=$where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,'','','');
		return $callConfig->getCount($query);
	}  
	function getAllTablesData($tablename,$fieldnames='',$where='',$order='',$limit1='',$limit2=''){
		global $callConfig;
		$fieldname = ($fieldnames == '') ? '*' : $fieldnames;
		$whr = ($where == '') ? '' : $where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,$order,$limit1,$limit2);
		return $callConfig->getAllRowsNew($query);
	}
	function getrowTablesData($tablename,$fieldnames='',$where='',$order='',$limit1='',$limit2=''){
		global $callConfig;
		$fieldname = ($fieldnames == '') ? '*' : $fieldnames;
		$whr = ($where == '') ? '' : $where;
		$query=$callConfig->selectQuery($tablename,$fieldname,$whr,$order,$limit1,$limit2);
		//echo $fieldname."//".$query;
		//$data = $callConfig->getRowNew($query);
		//print_r($data);
		return $callConfig->getRowNew($query);
	}
	function insertTablesdata($fieldnames, $table)	{
		global $callConfig;
		$res=$callConfig->insertRecord($table,$fieldnames);
		return $res;
	}
	function updateTablesdata($fieldnames, $table, $whr, $value){	
		global $callConfig;
		$res=$callConfig->updateRecord($table,$fieldnames,$whr,$value);
		return $res;
	}
	function getAdminUserData($id){
		global $callConfig;
		$query=$callConfig->selectQuery(TBL_USERS,'*','id='.$id,'','','');
		$data = $callConfig->getRowNew($query);
		return $data;
	}	
	function userAuthentication(){
		if($_SESSION['userid']!="")	{
			header("location:index.php?option=com_dashboard");
		}
	}
	function updateAdminUser($post){
		global $callConfig;
		$fieldnames=array('name'=>$post['name'],'email'=>$post['email'],'phone'=>$post['phone'],'user_photo'=>$post['image_name']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_USERS,$fieldnames,'id',$post['hdn_user_id']);
		return $res;
	}
	function addrole($post) {
		global $callConfig;
		$option = array();
		$option ['add'] = isset($post['add']) ? $post['add'] : "off";
		$option ['edit'] = isset($post['edit']) ? $post['add'] : "off";
		$option ['delete'] = isset($post['delete']) ? $post['add'] : "off";
		$data = json_encode($option);
		$fieldnames=array('name'=>$post['name'],'status'=>$post['status'],'permissions'=>$data);
		//print_r($fieldnames); exit;
		$res=$callConfig->insertRecord(TPREFIX.TBL_ROLE,$fieldnames);
		return $res;
	}
	function updateRole($post){
		//echo "asdasd"; exit;
		global $callConfig;
		$option = array();
		$option ['add'] = isset($post['add']) ? $post['add'] : "off";
		$option ['edit'] = isset($post['edit']) ? $post['add'] : "off";
		$option ['delete'] = isset($post['delete']) ? $post['add'] : "off";
		$data = json_encode($option);
		$fieldnames=array('name'=>$post['name'],'status'=>$post['status'],'permissions'=>$data);
		$res=$callConfig->updateRecord(TPREFIX.TBL_ROLE,$fieldnames,'role_id',$post['role_id']);
		return $res;
	}

	function getRoleData(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_ROLE,'*','','','','');
		return $callConfig->getAllRowsNew($query);

	}
	function getoneRoleData($rid){
		global $callConfig;
		$whr="role_id = ".$rid;
		$query=$callConfig->selectQuery(TPREFIX.TBL_ROLE,'*',$whr,'','','');
		//echo $query; exit;
		return $callConfig->getRowNew($query);

	}
	function getSessions() {
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_SESSIONS,'*','','','','');
		return $callConfig->getAllRowsNew($query);
	}
	function getBillHistoryData(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_BILLHISTORIES_VW,'*','','','','');
		return $callConfig->getAllRowsNew($query);
	}
	function getModuleHistories(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_MODULEHISTORIES_VW,'*','','','','');
		return $callConfig->getAllRowsNew($query);
	}
	function getRevenuesDetaills(){
		global $callConfig;
		$query=$callConfig->selectQuery(TPREFIX.TBL_REVENUES,'*','','','','');
		return $callConfig->getAllRowsNew($query);
	}
	function insertCategories($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'type'=>$post['type'],'color'=>$post['color'],'enabled'=>$post['enable']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_CATEGORIES,$fieldnames);
		return $res;
	}
	function updatecategories($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'type'=>$post['type'],'color'=>$post['color'],'enabled'=>$post['enable']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_CATEGORIES,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertCompanies($post){
		global $callConfig;
		$fieldnames=array('domain'=>$post['domain'],'enabled'=>$post['enabled']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_COMPANIES,$fieldnames);
		return $res;
	}
	function updateCompanies($post){
		global $callConfig;
		$fieldnames=array('domain'=>$post['domain'],'enabled'=>$post['enabled']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_COMPANIES,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertConfig($post){
		global $callConfig;
		$fieldnames=array('name'=>$post['name'],'liscense_number'=>$post['liscense_number'],'admin_email'=>$post['admin_email'],'tax_number'=>$post['tax_number'],'phone_number'=>$post['phone_number'],'billing_address'=>$post['billing_address'],'contact_address'=>$post['contact_address'],'enabled'=>$post['enabled'],'display_logo'=>$post['display_logo'],'financial_start'=>$post['financial_start'],'time_zone'=>$post['time_zone'],'date_format'=>$post['date_format'],'percentage_position'=>$post['percentage_position'],'number_format'=>$post['number_format'],'default_currency_id'=>$post['default_currency_id'],'default_language_id'=>$post['default_language_id'],'default_payment_mode'=>$post['default_payment_mode'],'default_pettycash_acct'=>$post['default_pettycash_acct'],'default_cash_acct'=>$post['default_cash_acct']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_CONFIG,$fieldnames);
		return $res;
	}
	function updateConfig($post){
		global $callConfig;
		$fieldnames=array('name'=>$post['name'],'liscense_number'=>$post['liscense_number'],'admin_email'=>$post['admin_email'],'tax_number'=>$post['tax_number'],'phone_number'=>$post['phone_number'],'billing_address'=>$post['billing_address'],'contact_address'=>$post['contact_address'],'enabled'=>$post['enabled'],'display_logo'=>$post['display_logo'],'financial_start'=>$post['financial_start'],'time_zone'=>$post['time_zone'],'date_format'=>$post['date_format'],'percentage_position'=>$post['percentage_position'],'number_format'=>$post['number_format'],'default_currency_id'=>$post['default_currency_id'],'default_language_id'=>$post['default_language_id'],'default_payment_mode'=>$post['default_payment_mode'],'default_pettycash_acct'=>$post['default_pettycash_acct'],'default_cash_acct'=>$post['default_cash_acct']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_CONFIG,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertCurrencies($post){
		global $callConfig;
		$fieldnames=array('name'=>$post['name'],'code'=>$post['code'],'rate'=>$post['rate'],'enabled'=>$post['enabled'],'precisions'=>$post['precision'],'symbol'=>$post['symbol'],'symbol_first'=>$post['symbol_first'],'decimal_mark'=>$post['decimal_mark'],'thousands_separator'=>$post['thousands_separator']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_CURRENCIES,$fieldnames);
		return $res;
	}
	function updateCurrencies($post){
		global $callConfig;
		$fieldnames=array('name'=>$post['name'],'code'=>$post['code'],'rate'=>$post['rate'],'enabled'=>$post['enabled'],'precisions'=>$post['precision'],'symbol'=>$post['symbol'],'symbol_first'=>$post['symbol_first'],'decimal_mark'=>$post['decimal_mark'],'thousands_separator'=>$post['thousands_separator']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_CURRENCIES,$fieldnames,'id',$post['id']);
		return $res;
	}
	
	function insertCustomer($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_CUSTOMERS,$fieldnames);
		return $res;
	}
	function updateCustomer($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_CUSTOMERS,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertMediables($post){
		global $callConfig;
		$fieldnames=array('media_id'=>$post['media_id'],'mediable_type'=>$post['mediable_type'],'mediable_id'=>$post['mediable_id'],'tag'=>$post['tag'],'order'=>$post['order']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_MEDIABLES,$fieldnames);
		return $res;
	}
	function updateMediables($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'email'=>$post['email'],'tax_number'=>$post['tax_number'],'phone'=>$post['phone'],'address'=>$post['address'],'website'=>$post['website'],'currency_code'=>$post['currency_code'],'enabled'=>$post['enabled']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_MEDIABLES,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertNotifications($post){
		global $callConfig;
		$fieldnames=array('type'=>$post['type'],'notifiable_id'=>$post['notifiable_id'],'notifiable_type'=>$post['notifiable_type'],'data'=>$post['data']);
		$res=$callConfig->insertRecord(TPREFIX.TBL_NOTIFICATIONS,$fieldnames);
		return $res;
	}
	function updateNotifications($post){
		global $callConfig;
		$fieldnames=array('type'=>$post['type'],'notifiable_id'=>$post['notifiable_id'],'notifiable_type'=>$post['notifiable_type'],'data'=>$post['data']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_NOTIFICATIONS,$fieldnames,'id',$post['id']);
		return $res;
	}
	function updateItems($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['purchase_price'],'quantity'=>$post['quantity'],'category_id'=>$post['category_id'],'tax_id'=>$post['tax_id'],'item_image'=>$post['company_id'],'name'=>$post['name'],'sku'=>$post['sku'],'description'=>$post['description'],'sale_price'=>$post['sale_price'],'purchase_price'=>$post['item_image'],'item_image2'=>$post['item_image2'],'item_image3'=>$post['item_image3'],'enabled'=>$post['enabled']);
		$res=$callConfig->updateRecord(TPREFIX.TBL_ITEMS,$fieldnames,'id',$post['id']);
		return $res;
	}
	function insertBillTables($post){
		global $callConfig;
		$fieldnames=array('company_id'=>$post['company_id'],'bill_number'=>$post['invoice_no'],'order_number'=>$post['invoice_no'],'bill_status_code'=>'SSS','billed_at'=>$post['invoice_date'],'due_at'=>$post['invoice_date'],'amount'=>$post['total'],'currency_code'=>'INR','vendor_name'=>$post['vendor_name'],'vendor_email'=>$post['vendor_email'],'vendor_tax_number'=>$post['vendor_tax_number'],'vendor_phone'=>$post['vendor_phone'],'paid_amount'=>$post['paid']);
		$bill_id=$callConfig->insertRecord(TPREFIX.TBL_BILLS,$fieldnames);
		$bill_id = 2;  
		foreach ($post['item_name'] as $key => $item) {
			$itemfieldnames=array('company_id'=>$post['company_id'],'bill_id'=>$bill_id,'item_id'=>$key+1,'name'=>$item,'sku'=>$key+1,'quantity'=>$post['item_qty'][$key],'price'=>$post['item_rate'][$key],'total'=>$post['item_qty'][$key]*$post['item_rate'][$key],'tax'=>'0','description'=>$post['item_description'][$key]);
		 		//print_r($itemfieldnames); exit;
			$billitem_id=$callConfig->insertRecord(TPREFIX.TBL_BILLITEMS,$itemfieldnames);
		}
		return $bill_id;
	}
	function insertBillTables11($post){
		global $callConfig;
		print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'invoice_number'=>$post['invoice_number'],'invoice_serial_number'=>$post['invoice_serial_number'],'order_at'=>$post['order_at'],'order_number'=>$post['order_number'],'invoiced_at'=>$post['invoiced_at'],'party_id'=>$post['party_id'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes']);
		$bill_id=$callConfig->insertRecord(TPREFIX.TBL_BILLS,$fieldnames);
		foreach ($post['productCode'] as $key => $item) {
			$itemfieldnames=array('company_id'=>$post['company_id'],'invoice_id'=>$bill_id,'item_id'=>$item['productCode'],'name'=>$item['productName'],'sku'=>$item['productCode'],'quantity'=>$item['quantity'],'price'=>$item['price'],'total'=>$item['price']+$item['tax_amount'],'tax'=>'0','description'=>$post['item_description']);
		 		//print_r($itemfieldnames); exit;
			$billitem_id=$callConfig->insertRecord(TPREFIX.TBL_BILLITEMS,$itemfieldnames);
		}
		return $bill_id;
	}
	function listofTables($db){
		global $callConfig;
		//$query="show TABLES";
		$query="SELECT table_name FROM information_schema.tables WHERE table_schema ='?'";
		$params = [$query, $db];
		//echo $query; exit;
		$dataa = $callConfig->getAllRowsNew($query);
		//print_r($dataa);
		$tables = array();
		foreach ($dataa as  $data) {
			if ($data->table_name!="") {	
				$tables[] = $data->table_name;	
			}else{	
				$tables[] = $data->TABLE_NAME;	
			}
		}
		return $tables;
	}
	function listofTableFields($table, $content=''){
		global $callConfig;
		$additional_attributes = 0;
		$query="show COLUMNS from ".$table;
		//echo $query;
		$dataa = $callConfig->getAllRowsNew($query);
		//print_r($dataa); exit;
		//echo "<pre>";print_r($content); exit;
		$fields = array(); $add=10;
		foreach ($dataa as  $data) {
			$fields[$data->Field] = $content[$data->Field];
			if(!isset($content[$data->Field])){
			if(!isset($content[$data->Field]['display_postion'])){
				$fields[$data->Field]['display_postion'] =  $add;
			}

			if(!isset($content[$data->Field]['display_list'])){
				$fields[$data->Field]['display_list'] =  2;
			}
			if(!isset($content[$data->Field]['lable'])){
				$fields[$data->Field]['lable'] =   ucwords(str_replace('_',' ',$data->Field));
			}
			if(!isset($content[$data->Field]['mandatory'])){
				$fields[$data->Field]['mandatory'] =  2;
			}
			if(!isset($content[$data->Field]['insert_display_list'])){
				$fields[$data->Field]['insert_display_list'] =  2;
			}
			if(!isset($content[$data->Field]['type'])){
				$fields[$data->Field]['type'] =  'text';
			}
			if(!isset($content[$data->Field]['insert_view_width'])){
				$fields[$data->Field]['insert_view_width'] =  6;
			}
			
			if(!isset($content[$data->Field]['insert_view_wizard'])){
				$fields[$data->Field]['insert_view_wizard'] =  1;
			}
			if(!isset($content[$data->Field]['add_custom_field'])){
				$fields[$data->Field]['add_custom_field'] =  2;
			}
			if(!isset($content[$data->Field]['accordions'])){
				$fields[$data->Field]['accordions'] =  'N';
			}

			
			}$add= $add+10;
			if($data->Field == "additional_attributes"){
				$additional_attributes =1;
			}
		}
		if($additional_attributes == 1 && $content != ""){
			foreach ($content as  $conkey=>$con) {
				if(isset($con['add_exter']) && $con['add_exter'] == 1 ){
					//echo "<pre>";print_r($con); exit;
					$fields[$conkey] = $con;
				}
			}
		}
		return json_encode($fields);	
	}
	function testTableInsert($data, $filename,$get){
		global $callConfig;
		$edrdquery=$callConfig->selectQuery('edi_read','*','file_name="?"','','','');
		$params = [$edrdquery, $callConfig->selectQuery, $filename];
		//echo $edrdquery; exit;
		$edrddata = $callConfig->getRowNew($edrdquery);
		
		$lines = explode("\n", $edrddata->content);
		$data = [];

		foreach ($lines as $line) {
		    if (trim($line) !== '') {
		        $data[] = explode(',', $line);;
		    }
		}
		$header = $edrddata->header;
		$header_data = json_decode($header,1);
		$count = count((array)$header_data);
		$fstr = " (file_name,";
		for($i=1; $i<= $count; $i++){
			$fstr.= "field?,";
			$params = [$fstr, $i];
		}
		$fstr = trim($fstr,",");
		$fstr.= ") ";
		$rstr = "";
		unset($data[0]);
		foreach ($data as  $feilddata) {
			$rstr.= "('?',";
			$params = [$rstr, $filename];
			$i =1;
			foreach ($feilddata as  $value) {
				$key ='field?_datatype';
				$params = [$key, $i];
				if ($edi_dump[$key] == 'date') { 
					$value = $this->dateformate($edi_master['EDI_Date_Formater'],$value);

				}
				$rstr.="'?',";
				$params = [$rstr, $value];
				$i++;
			}
			$rstr = trim($rstr,",");
			$rstr.= "),";
		}
		$rstr =  trim($rstr,",");
		 //echo $fstr;
		$query = "INSERT INTO edi_data ?VALUES?;";
		$params = [$query, $fstr, $rstr];

		echo $query; exit;
		$dataa = $callConfig->executeQuery($query);
		$n=1;
		$demo['name'] = $filename;
		$demo['company_id'] = $get['company_id'];
		$demo['application_table_name'] = $edrddata->application_table_name;
		$demo['is_active'] = '0';
		$demo_field = array();
		foreach ($header_data as  $demovalue) {
			$fstrd ='';
			$fstrd = "field".$n;
			$demo_field[$fstrd] = $demovalue;
			$n++;
		}
		$demo['field_data']  = json_encode($demo_field);
		$es_id=$callConfig->insertRecord('edi_dump',$demo);
		return $es_id;
	}
	function dateformate($str,$value){
		$sty= strpos($str,'yyyy');
		$std= strpos($str,'dd');
		$stm= strpos($str,'mm');
		$value = substr($value,$sty,4).'-'.substr($value,$stm,2).'-'.substr($value,$std,2);
		$params = [$value, $value, $sty, $value, $stm, $value, $std];
		return $value;
	}
	function getINqueryData ($tablename,$fields,$where,$ids) {
		global $callConfig;
		//echo $tablename; exit;
		$fields = ($fields == '') ? '*' : $fields;
		//echo $fields; exit;
		$query=$callConfig->selectIN($tablename,$fields,$where,$ids);
		//echo $tablename."//".$query;  exit;
		return $callConfig->getAllRowsNew($query);

	}
	function getStatusSeq($ids){
		global $callConfig;
		$order = "sequence ASC";
		$query=$callConfig->selectIN(TBL_STATUS,'*','name_value',$ids,$order);
		return $callConfig->getAllRowsNew($query);
	}
	function maacInvoicenumber($company_id){
		global $callConfig;
		$query=	"SELECT max(`invoice_serial_number`) as number FROM isac_invoices where `company_id` =".$company_id;
		//echo $query; //exit;
		return $callConfig->getRowNew($query);
	}
	function reSetPassword($data){
		global $callConfig;
		//echo "raviw"; exit;
		$dbquery = "select db_name FROM ".MAINDB.".database_allocations where id IN (SELECT zqr_connect FROM ".MAINDB.".user_registered_groups  WHERE user_email ='?')";
		$params = [$dbquery, $data];
		$dbrow =  $callConfig->getRowNew($dbquery);
		//echo $dbquery;print_r($dbrow);echo "lll"; exit;
		$whr ="user_email='?' and user_password='?' and is_active ='0'";
		$params = [$whr, $data, $callConfig->passwordEncrypt, $data];
		$query=$callConfig->selectQuery($dbrow->db_name.".".TBL_USERS,'*',$whr,'','','');
		$params = [$query, $callConfig->selectQuery, $dbrow->db_name, $whr];
		//echo $query; exit;
		$data['db_name']= $dbrow->db_name;
		$row=  $callConfig->getRowNew($query);
		//print_r($data); exit;
		if($row->id !=""){
			$data['hdn_user_id'] = $row->id;
			$res = $this->changepassword($data);
			$fieldnames=array('reset_password'=>1);
			//print_r($fieldnames); exit;
			$whrcon = "user_email ='?'";
			$params = [$whrcon, $data];
			$res3=$callConfig->updateRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$fieldnames,'','',$whrcon);
			$params = [$res3, $callConfig->updateRecord, $fieldnames, $whrcon];

		}else{
			$res['error'] = '6009';
		}
		return $res;
	}
	function changepassword($post){
		global $callConfig;
		//print_r($post);
		$fieldnames=array('user_password'=>$callConfig->passwordEncrypt($post['new_password']));
		//print_r($fieldnames); exit;
		$res=$callConfig->updateRecord($post['db_name'].".".TBL_USERS,$fieldnames,'id',$post['hdn_user_id']);
		$params = [$res, $callConfig->updateRecord, $post, $fieldnames, $post];
		return $res;
	}
	function getDyanamicafterData($table_name, $filed_name){
		global $callConfig;
		//echo "data"; exit;
		$filed_name = $filed_name."_after";
		$params = [$filed_name, $filed_name];
		$whr = "table_name = '?' AND field_name = '?'";
		$params = [$whr, $table_name, $filed_name];
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
		$data = $callConfig->getRowNew($query);
		$array ['lable'] = $data->display;
		$array ['value'] = $data->content;
		$array ['width'] = $data->value;
		$type = explode('#', $data->type);
		$array ['type'] = $type[0];
		$array ['display'] = $type[1];
		//print_r($array);
		//echo $query; exit;
		return $array;
	}
	function getDyanamicReportSelectData($report_id, $filed_name){
		global $callConfig;
		//echo "data"; exit;
		$whr = "report_id= '?' AND field_name = '?'";
		$params = [$whr, $report_id, $filed_name];
		$query=$callConfig->selectQuery(TBL_REPORT_FILTERS,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRowNew($query);
		$display = $data->display_field;
		$value = $data->value_field;
		//print_r($data); exit;
		$array['filter_type'] = $data->filter_type;
		if($data->filter_type != 'static'){
			//echo $data->content; exit;
			//echo urldecode($data->content); exit;
			$custom_sql = $callConfig->getAllRowsNew(urldecode($data->content));
			// echo "<pre>"; print_r($custom_sql);
			foreach ($custom_sql as $custom){
				//print_r($custom);
				 // echo $custom->NULL."### ".$custom->ALL_Groups;echo "<br>";
				$array1[$custom->NULL] = $custom->ALL_Groups;
				//print_r($array1);
			}
			$array['res'] = $array1;
			//print_r($array);
		}else{
			$array['res'] = json_decode($data->content,1);
		}
		//print_r($array); exit;
		return $array;
	}
	function getDyanamicSelectData($table_name, $filed_name,$val=""){
		global $callConfig;
		//echo "data"; exit;
		$whr = "application_table_name= '?' AND field_name = '?'";
		$params = [$whr, $table_name, $filed_name];
		$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
		//echo $query; exit;
		$data = $callConfig->getRowNew($query);
		$display = $data->display_field;
		$value = $data->value_field;
		//print_r($data); exit;
		$array['type'] = $data->attribute_type;
		if($data->attribute_type == 'custom_sql'){
			//echo $data->content; exit;
			$qquery = urldecode($data->content);
			if($data->refer_by_action =='0' and $val!=""){    
				$condition = " AND  ? = '?'";
				$params = [$condition, $data->refer_by_field, $val];
			}  
			if(strpos($qquery,'WHERE')){
				$cwhr = "AND company_id=? ORDER BY";
				$params = [$cwhr, $data->company_id, $condition];
			}else{
				$cwhr = "WHERE company_id=? ORDER BY";
				$params = [$cwhr, $data->company_id, $condition];
			}
			$qquery = str_replace('ORDER BY', $cwhr, $qquery);
			$custom_sql = $callConfig->getAllRowsNew($qquery);
			//print_r($custom_sql);
			foreach ($custom_sql as $custom){
				//print_r($custom);
				$array1[$custom->$value] = $custom->$display;
			}
			$array['res'] = $array1;
			//print_r($array);
		}else{
			$array['res'] = json_decode($data->content,1);
		}
		//print_r($array); exit;
		return $array;

	}
	function listofTableFieldsByUsedTable($table,$db){
		global $callConfig;
		$query="SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS   WHERE TABLE_SCHEMA = '?' AND TABLE_NAME = '?'";
		$params = [$query, $db, $table];
		//echo $query;
		$datas = $callConfig->getAllRowsNew($query);
		foreach ($datas as $key => $da) {
			//print_r($da);
			$data[]=  $da->COLUMN_NAME;
		}
		return $data;	
	}
	function deleterows($table_name,$whr){
		global $callConfig;
		//echo "delete";
		$data = $callConfig->deleteRecord($table_name,$whr);
		return $data;
	}
	function toGetNotigficationList($user_id,$type="",$get){
		global $callConfig;
        $sql = "SELECT notiusr.notification_id, notiusr.notification_type, notiusr.read_status, noti.notification_message, noti.notification_datetime, noti.application_table_name, noti.transaction_number, noti.notification_details FROM notification_user notiusr, notifications noti WHERE noti.company_id = notiusr.company_id AND noti.id = notiusr.notification_id AND noti.company_id = '?' AND notiusr.user_id = '?'";
		$params = [$sql, $get, $user_id];
		$data = $callConfig->getAllRowsNew($sql);
		foreach ($data as $value) {
			$return = array();
			$return['id'] = $value->notification_id;
			$return['Notification_Type'] = $value->notification_type;
			$return['Notification_Message'] = $value->notification_message;
			$return['Notification_DateTime'] = $value->notification_datetime;
			$return['Notification_Table'] = $value->application_table_name;
			$return['Notification_Table_Id'] = $value->transaction_number;
			$return['Notification_Read'] = $value->read_status;
			$notidetails = json_decode($value->notification_details,1);
			$return['option'] = $notidetails['option'];
			$return['option_id'] = $notidetails['id'];
			$res[] = $return;
		}
		return  $res;
	}
	function displayrolepermission($post){
		global $callConfig;
		$role_id = $_GET['rid'];
		$db = $_GET['data_name'];
		$whr = "role_id = '?'";
		$params = [$whr, $role_id];
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'*',$whr,'','','');
		//echo $query;
		$pemissions = $callConfig->getAllRowsNew($query);
		//echo "<pre>"; //print_r($pemissions);
		foreach($pemissions as $pemission){
			$table_name = $pemission->table_name;
			$data[$table_name]['permission_view'] = $pemission->permission_view;
			$data[$table_name]['permission_insert'] = $pemission->permission_insert;
			$data[$table_name]['permission_update'] = $pemission->permission_update;
			$data[$table_name]['permission_delete'] = $pemission->permission_delete;
		}
		//print_r($data);
		$tables = $this->listofTables($db);
		//print_r($tables);
		foreach ($tables as $table) {
			$data1[$table]['permission_view'] = isset($data[$table]['permission_view']) ? $data[$table]['permission_view'] : 0;
			$data1[$table]['permission_insert'] = isset($data[$table]['permission_insert']) ? $data[$table]['permission_insert'] : 0;
			$data1[$table]['permission_delete'] = isset($data[$table]['permission_delete']) ? $data[$table]['permission_delete'] : 0;
		}
		//print_r($data1);
		return $data1;
	}
	function toGetRolePermissions($table){
		global $callConfig;
		$whr = "table_name = '?'";
		$params = [$whr, $table];
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'*',$whr,'','','');
		$pemissions = $callConfig->getAllRowsNew($query);
		foreach($pemissions as $pemission){
			$role_id = $pemission->role_id;
			$data[$role_id]['permission_view'] = $pemission->permission_view;
			$data[$role_id]['permission_insert'] = $pemission->permission_insert;
			$data[$role_id]['permission_update'] = $pemission->permission_update;
			$data[$role_id]['permission_delete'] = $pemission->permission_delete;
		}
		$query=$callConfig->selectQuery(TBL_ROLES,'*','','','','');
		$roles = $callConfig->getAllRowsNew($query);
		//print_r($roles);
		//print_r($tables);
		foreach ($roles as $role) {
			//print_r($role);
			$roleid = $role->id;
			$data1[$roleid]['permission_view'] = isset($data[$roleid]['permission_view']) ? $data[$roleid]['permission_view'] : 0;
			$data1[$roleid]['permission_insert'] = isset($data[$roleid]['permission_insert']) ? $data[$roleid]['permission_insert'] : 0;
			$data1[$roleid]['permission_update'] = isset($data[$roleid]['permission_update']) ? $data[$roleid]['permission_update'] : 0;
			$data1[$roleid]['permission_delete'] = isset($data[$roleid]['permission_delete']) ? $data[$roleid]['permission_delete'] : 0;
			$data1[$roleid]['role_name'] = $role->display_name;
		}
		//echo "<pre>"; print_r($data1);
		return $data1;
	}
	function toGetRoledata($user_id,$get){
		global $callConfig;
		//echo "data"; exit;
		//echo "<pre>";
		$whr = "company_id = '?' AND id = '?' AND is_active = '0'";
		$params = [$whr, $get, $user_id];
		$query=$callConfig->selectQuery(TBL_USERS,'role_id',$whr,'','','');
		$data = $callConfig->getRowNew($query);
		$role_id = $data->role_id;
		$role_id = json_decode($role_id,1);
		$role_id = implode("','", (array)$role_id);
		$row_query = "SELECT * FROM application_roles where company_id = '?' AND role_short_code IN('?') AND is_active = '0'";
		$params = [$row_query, $get, $role_id];
		$row_data = $callConfig->getAllRowsNew($row_query);
		
		$roleidlist = array();
		$row_query1 = "SELECT role_short_code FROM application_roles where company_id = '?' AND role_short_code IN('?') AND application_module = '?' AND is_active = '0'";
		$params = [$row_query1, $get, $get, $get];
		$row_data1 = $callConfig->getAllRowsNew($row_query1);
		foreach($row_data1 as $resrow){
			$roleidlist[$resrow->role_short_code] = $resrow->role_short_code;
		}
		$strrole_id = implode("','", (array)$roleidlist);

		$query = "SELECT `table_name`,page_name,  MIN(permission_view) as permission_list,MIN(permission_insert) as permission_insert ,MIN(permission_update) as permission_update,MIN(permission_delete) as permission_delete, MIN(permission_print) as permission_print, MIN(permission_import) as permission_import, MIN(permission_download) as permission_download, MIN(permission_view) as permission_view, MIN(permission_export) as permission_export, MIN(permission_approval) as permission_approval, MIN(permission_analytic) as permission_analytic FROM role_permissions where role_short_code IN('?')  AND is_active=0  Group BY table_name, page_name ";
		$params = [$query, $strrole_id];
		$return['role_short_code'] = $query;
		//echo $query; exit;
		$rolesdata = $callConfig->getAllRowsNew($query);
		///echo "<pre>"; print_r($rolesdata); exit;
		foreach ($rolesdata as  $value) { 
			$roles[$value->table_name]['permission_list'] = $value->permission_list;
			$roles[$value->table_name]['permission_insert'] = $value->permission_insert;
			$roles[$value->table_name]['permission_update'] = $value->permission_update;
			$roles[$value->table_name]['permission_delete'] = $value->permission_delete;
			$roles[$value->table_name]['permission_print'] = $value->permission_print;
			$roles[$value->table_name]['permission_import'] = $value->permission_import;
			$roles[$value->table_name]['permission_download'] = $value->permission_download;
			$roles[$value->table_name]['permission_view'] = $value->permission_view;
			$roles[$value->table_name]['permission_export'] = $value->permission_export;
			$roles[$value->table_name]['permission_approval'] = $value->permission_approval;
			$roles[$value->table_name]['permission_analytic'] = $value->permission_analytic;
		}
		foreach ($rolesdata as  $value){
			$page_permission[$value->page_name]['permission_list'] = $value->permission_list;
			$page_permission[$value->page_name]['permission_insert'] = $value->permission_insert;
			$page_permission[$value->page_name]['permission_update'] = $value->permission_update;
			$page_permission[$value->page_name]['permission_delete'] = $value->permission_delete;
			$page_permission[$value->page_name]['permission_print'] = $value->permission_print;
			$page_permission[$value->page_name]['permission_import'] = $value->permission_import;
			$page_permission[$value->page_name]['permission_download'] = $value->permission_download;
			$page_permission[$value->table_name]['permission_view'] = $value->permission_view;
			$page_permission[$value->table_name]['permission_export'] = $value->permission_export;
			$page_permission[$value->table_name]['permission_approval'] = $value->permission_approval;
			$page_permission[$value->table_name]['permission_analytic'] = $value->permission_analytic;
		}
		#Added Rohan
		$nopagepermisionsql = "SELECT transaction_table,page_option FROM application_no_permission_pages where company_id = '?' AND is_active = '0'";
		$params = [$nopagepermisionsql, $get];
		$nopagepermisionres = $callConfig->getAllRowsNew($nopagepermisionsql);
		foreach($nopagepermisionres as $nopagepermisionres1){
			$nopagepermision['table'][$nopagepermisionres1->transaction_table] = $nopagepermisionres1->transaction_table;
			$nopagepermision['option'][$nopagepermisionres1->page_option] = $nopagepermisionres1->page_option;
		}
		$return['application_no_permission_pages'] = $nopagepermision;
		$tapquery = "SELECT tap.role_id, tap.role_short_code, tap.application_process_page_id, tap.transaction_table, tap.option_url, tapu.user_id, tapu.permission_view, tapu.permission_insert, tapu.permission_update, tapu.permission_delete, tapu.permission_print, tapu.permission_import, tapu.permission_export, tapu.permission_download, tapu.permission_approval, tapu.permission_analytic, tapu.approved_type FROM transaction_access_permission tap, transaction_access_permission_users tapu WHERE tap.company_id = '?' AND tap.company_id = tapu.company_id AND tap.role_short_code IN('?') AND tap.id = tapu.transaction_access_permission_id AND tapu.user_id = '?'  AND tap.is_active = tapu.is_active AND tap.is_active=0";
		$params = [$tapquery, $get, $strrole_id, $user_id];
		$tapres = $callConfig->getAllRowsNew($tapquery);
		foreach($tapres as $taprow){
			//$exclude_user = json_decode($taprow->permission_users,1);
			
			$page_permission[$taprow->option_url]['permission_list'] = $taprow->permission_view == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_list'] = $taprow->permission_view == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_insert'] =$taprow->permission_insert == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_insert'] = $taprow->permission_insert == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_update'] = $taprow->permission_update == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_update'] = $taprow->permission_update == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_delete'] = $taprow->permission_delete == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_delete'] = $taprow->permission_delete == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_print'] = $taprow->permission_print == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_print'] = $taprow->permission_print == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_import'] = $taprow->permission_import == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_import'] = $taprow->permission_import == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_download'] = $taprow->permission_download == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_download'] = $taprow->permission_download == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_approval'] = $taprow->permission_approval == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_approval'] = $taprow->permission_approval == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_analytic'] = $taprow->permission_analytic == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_analytic'] = $taprow->permission_analytic == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_view'] = $taprow->permission_view == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_view'] = $taprow->permission_view == '0'?"0":"1";
			$page_permission[$taprow->option_url]['permission_export'] = $taprow->permission_export == '0'?"0":"1";
			$roles[$taprow->transaction_table]['permission_export'] = $taprow->permission_export == '0'?"0":"1";
		}
		



		$allroles = array();
		$allsql = "SELECT * FROM application_roles where company_id = '?' AND is_active = '0'";
		$params = [$allsql, $get];
		$allres = $callConfig->getAllRowsNew($allsql);
		foreach($allres as $roll){
			$allroles[$roll->role_short_code] = $roll;
		}
		$return['all_roles'] = $allroles;
		$return['permission'] = $roles;
		$return['page_permission'] = $page_permission;
		//$return['page_permission_new'] = $page_permission_new;
		$return['role'] = $row_data;
		$return['transaction_access_permission'] = $tapres;
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function toGetblanceInvoices($customer_id){
		global $callConfig;
		$query = "SELECT * FROM invoices WHERE due_amount != 0 AND party_id = ".$customer_id;
		$data = $callConfig->getAllRowsNew($query);
		return $data;
	}
	function toGetblancebill($get){
		global $callConfig;
		$return =$returnpaymets = $pttrans = $pttransdue =  array();
		//$returnres['get'] = $get;
		$partyids = json_decode(urldecode($get['party_id']),1);
 		$partyid = implode(',', (array)$partyids);
 		
 		//$return['partyid'] = $partyid;
		if($get['transaction_type'] == 'customers'){
			if($get['payment_process'] == "AP"){
				$gettransactionarray[] = array('sales_orders'=>array());
			}else{
				$gettransactionarray[] = array('sales_invoices'=>array('debit_notes','credit_notes'),'rental_invoices'=>array('debit_notes','credit_notes'),'utility_rental_invoices'=>array('debit_notes','credit_notes'));
			}			
		}elseif($get['transaction_type'] == 'vendors'){
			if($get['payment_process'] == "AP"){
				$gettransactionarray[] = array('purchase_orders'=>array());
			}else{
				$gettransactionarray[] = array('supplier_bills'=>array('debit_notes','credit_notes'),'purchase_invoices'=>array('debit_notes','credit_notes'));
			}
		} 
		//$return['gettransactionarray'] = $gettransactionarray;
		if($get['transaction_type'] == 'employees'){
			$query = "SELECT *, expense_code as transaction_number FROM emp_expenses WHERE company_id = '?' AND emp_id IN (?) AND pending_amount > 0 AND is_active = 0 AND current_status = '".ST_APPROVED."'";
			$params = [$query, $get, $partyid];
			$data = $callConfig->getAllRowsNew($query);
			if(count((array)$data) !=0){
				$return['emp_expenses'] = $data;
			}
		}else{
			foreach ($gettransactionarray as $masttable){
				foreach($masttable as $mastertable=>$transtable){
					$query = "SELECT * FROM ? WHERE company_id = '?' AND party_id IN (?) AND due_date <= '?' AND pending_amount > 0 AND is_active = 0 AND current_status = '".ST_APPROVED."'";
					$params = [$query, $mastertable, $get, $partyid, $this->dateconvertdbformat, $get];
					//$returnres['query333'][] = $query;
					$data = $callConfig->getAllRowsNew($query);
					$masterid = array();
					if(count((array)$data) !=0){
						foreach($data as $ddt){
							$masterid[$ddt->id] = $ddt->id;
						}
						$return[$mastertable] = $data;
					}
					if(count((array)$masterid) !="0"){
						$masterids = implode(',', (array)$masterid);

						if($get['transaction_type'] == 'customers'){	
							$alreadypaysql = "SELECT pay.id, pay.transaction_no, pay.payment_date, pay.current_status, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.paid_amonut, paya.tds_amount FROM receipts pay, receipt_allocations paya WHERE pay.company_id = '?' AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND paya.transaction_table = '?' AND paya.transaction_id IN(?) AND pay.is_active = paya.is_active";
							$params = [$alreadypaysql, $get, $mastertable, $masterids];
						}else{
							$alreadypaysql = "SELECT pay.id, pay.transaction_no, pay.payment_date, pay.current_status, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.paid_amonut, paya.tds_amount FROM payments pay, payment_allocations paya WHERE pay.company_id = '?' AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND paya.transaction_table = '?' AND paya.transaction_id IN(?) AND pay.is_active = paya.is_active";
							$params = [$alreadypaysql, $get, $mastertable, $masterids];
						}
						$alreadypayres = $callConfig->getAllRowsNew($alreadypaysql);
						foreach($alreadypayres as $ddt){
							$returnpaymets[$mastertable][$ddt->id][] = $ddt;
						}
						//$returnpaymets[$mastertable] = $alreadypayres

						$query22 = "SELECT * FROM payment_term_transactions WHERE company_id = '?' AND transaction_id IN (?) AND transaction_table = '?' AND pending_amount > 0 AND is_active = 0";
						$params = [$query22, $get, $masterids, $mastertable];
						//$returnres['query22'][] = $query22;
						$data22 = $callConfig->getAllRowsNew($query22);
						foreach($data22 as $ptt){
							if($ptt->due_date <= $this->dateconvertdbformat($get['due_date'])){
	 							$pttransdue[$mastertable][$ptt->transaction_id] +=$ptt->pending_amount;
							//}elseif($ptt->advance_offer_date <= $this->dateconvertdbformat($get['due_date'])){
							//	$pttransdue[$mastertable][$ptt->transaction_id] +=$ptt->advance_offer_amount;
							}
							$pttrans[$mastertable][$ptt->transaction_id][] = $ptt;
						}
					}
					foreach ($transtable as $subtranstable){
						$query = "SELECT * FROM ? WHERE company_id = '?' AND party_id IN (?) AND due_date <= '?' AND ref_type = '?' AND pending_amount > 0 AND is_active = 0 AND current_status = '".ST_APPROVED."'";
						$params = [$query, $subtranstable, $get, $partyid, $this->dateconvertdbformat, $get, $mastertable];
						$data = $callConfig->getAllRowsNew($query);
						$subtabid = array();
						if(count((array)$data) !=0){
							foreach($data as $ddt){
								$subtabid[$ddt->id] = $ddt->id;
							}
							$return[$subtranstable] = $data;
						}
						if(count((array)$subtabid) !="0"){
							$subtabids = implode(',', (array)$subtabid);
							if($get['transaction_type'] == 'customers'){		
								$alreadypaysql1 = "SELECT pay.id, pay.transaction_no, pay.payment_date, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.paid_amonut, paya.tds_amount FROM receipts pay, receipt_allocations paya WHERE pay.company_id = '?' AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND paya.transaction_table = '?' AND paya.transaction_id IN(?) AND pay.is_active = paya.is_active";
								$params = [$alreadypaysql1, $get, $subtranstable, $subtabids];
							}else{
									$alreadypaysql1 = "SELECT pay.id, pay.transaction_no, pay.payment_date, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.paid_amonut, paya.tds_amount FROM payments pay, payment_allocations paya WHERE pay.company_id = '?' AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND paya.transaction_table = '?' AND paya.transaction_id IN(?) AND pay.is_active = paya.is_active";
									$params = [$alreadypaysql1, $get, $subtranstable, $subtabids];
								
							}
							$alreadypayres1 = $callConfig->getAllRowsNew($alreadypaysql1);
							foreach($alreadypayres1 as $ddt){
								$returnpaymets[$subtranstable][$ddt->id][] = $ddt;
							}

						}
					}
				}
			}
		}
		$returnres['transactions'] = $return;
		$returnres['transaction_payment_allocations'] = $returnpaymets;
		$returnres['payment_term_transactions'] = $pttrans;
		$returnres['payment_term_transactions_due'] = $pttransdue;
		//echo "<pre>"; print_r($returnres);
		return $returnres;
	}

	function toInsertandupateinvoice($post){
		global $callConfig;
		//echo "hai";
		//print_r($post); //exit;
		$fieldnames=array('company_id'=>$post['company_id'],'amount'=>$post['total_amount'],'payment_method'=>$post['paytype'],'reference'=>$post['ref_number']);
		//print_r($fieldnames); exit;
		$receiptid = $callConfig->insertRecord(TBL_INVOICE_PAYMENTS,$fieldnames);
		//echo  $receiptid = 1; //exit;
		foreach ($post['details'] as $key => $item) {
			$invoice_number = $item['invoice_number']== 'unallocated'? 0 : $item['invoice_number'];
			$itemfieldnames=array('receipt_id'=> $receiptid,'invoice_id'=> $invoice_number,'paid_amount'=>$item['paid']);
		 		//print_r($itemfieldnames); exit;
			$billitem_id=$callConfig->insertRecord(TBL_INVOICE_ALLOCATED,$itemfieldnames);
			if($item['invoice_number'] != 'unallocated'){
				$field = array('paid_amount'=>$item['paid'],'due_amount'=>$item['due']);
				$res=$callConfig->updateRecord(TBL_INVOICE,$field,'invoice_number',$item['invoice_number']);
			}
		}	

	}
	function insertEstimatesTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_ESTIMATES,$fieldnames);  
		foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
			$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
			$item_id=$callConfig->insertRecord(TBL_ESTIMATE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
			$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
			$itax_id=$callConfig->insertRecord(TBL_ESTIMATE_TAXES,$taxfield);
		}
		return $es_id;
	}
	function insertdebitnoteTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_DEBIT_NOTE,$fieldnames);  
		foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
			$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
			$item_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
			$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
			$itax_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEM_TAXES,$taxfield);
		}
		return $es_id;
	}
	function insertcreaditnoteTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_DEBIT_NOTE,$fieldnames);  
		foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
			$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
			$item_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
			$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
			$itax_id=$callConfig->insertRecord(TBL_DEBIT_NOTE_ITEM_TAXES,$taxfield);
		}
		return $es_id;
	}
	function insertquotationTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_QUOTATIONS,$fieldnames);  
		foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
			$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
			$item_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
			$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
			$itax_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEM_TAXES,$taxfield);
		}
		return $es_id;
	}
	function insertpurchaseTables($post){
		global $callConfig;
			//echo "<pre>";print_r($post); exit;
		$fieldnames=array('company_id'=>$post['company_id'],'estimate_number'=>$post['invoice_number'],'estimated_at'=>$post['invoiced_at'],'amount'=>$post['invoiced_at'],'amount'=>$post['grandtotaol'],'currency_code'=>'INR','party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'sub_total'=>$post['subTotal'],'tax_amount'=>$post['taxRate']);
		//print_r($fieldnames); echo "ijul"; exit;
		//$es_id = 1;
		$es_id=$callConfig->insertRecord(TBL_QUOTATIONS,$fieldnames);  
		foreach ($post['productCode'] as $key => $item) {
		 	//echo $item."##".$key;// exit;
			$itemfieldnames=array('estimate_id'=>$es_id,'item_id'=>$item,'name'=>$item,'quantity'=>$post['quantity'][$key],'price'=>$post['price'][$key],'total'=>$post['amount'][$key],'tax'=>$post['ptax_amount'][$key],'description'=>$post['productName'][$key]);
		 	//print_r($itemfieldnames); exit;
			$item_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEMS,$itemfieldnames);
		 	//$item_id = 5;
			$taxfield=array('estimate_id'=>$es_id,'estimate_item_id'=>$item_id,'tax_id'=>1,'value'=>$post['tax'][$key],'amount'=>$post['ptax_amount'][$key]);
		 	//print_r($taxfield); exit;
			$itax_id=$callConfig->insertRecord(TBL_QUOTATIONS_ITEM_TAXES,$taxfield);
		}
		return $es_id;
	}
	function togetnameandphotoonuseridbaseonemailids($post){
		global $callConfig;
		//echo "<pre>";print_r($post);
		$user = array();
		$ids = implode(",", (array)$post);
		$query= "Select id,name,email,user_id from email where id in (?) ";
		$params = [$query, $ids];
		$emails = $callConfig->getAllRowsNew($query);
		foreach ($emails as  $value) {
			$user[$value->id]['name'] = $value->name;
			$user[$value->id]['image'] = "";
			$user[$value->id]['email'] = $value->email;

			if($value->user_id !=0){
				$whr = "id = '?'";
				$params = [$whr, $value->user_id];
				$query2=$callConfig->selectQuery(TBL_USERS,'display_name,user_photo',$whr,'','','');
				$owner = $callConfig->getRowNew($query2);
				$user[$value->id]['name'] = $owner->display_name;
				$user[$value->id]['image'] = $owner->user_photo;
			}
		}
		return $user;
	}
	# To Get Email group Details with number emails  with user pics
	function getemailgroupdetails(){
		global $callConfig;
		$query=$callConfig->selectQuery(TBL_EMAILGROUPS,'*','','','','');
		$emailgroup = $callConfig->getAllRowsNew($query);
		foreach ($emailgroup as  $value) {
			$group = array();
			$group['id'] = $value->id;
			$group['name'] = $value->name;
			$group['description'] = $value->description;
			$group['start_date'] = $value->status_date;
			$group['status'] = $value->current_status;
			$group['owner_id'] = $value->owner;
			$user_ids[] = $value->owner;
			$whr = "email_groups_id = '?'";
			$params = [$whr, $value->id];
			$query1=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'id,email_id',$whr,'','','');
			//echo $query1; exit;
			$emailids = array();
			$email = $callConfig->getAllRowsNew($query1);
			foreach ($email as $xvalue) { //print_r($xvalue);
				if($i<7){
					$emailids[]= $xvalue->email_id;
				}else{
					break;
				}
				$i++;
			}
			$email_users =$this->togetnameandphotoonuseridbaseonemailids($emailids);
			$group['count'] = count((array)$email);
			$group['users'] = $email_users;
			$whr1  =  "id = '?'";
			$params = [$whr1, $value->owner];
			$query2=$callConfig->selectQuery(TBL_USERS,'first_name',$whr1,'','','');
			//echo $query2;
			$owner = $callConfig->getRowNew($query2);
			$group['owner'] = $owner->first_name;
			$groups[] = $group;
		}
		//echo "<pre>"; print_r($groups);
		return $groups;
	}
	function insertorupdatepermissions($post){
		global $callConfig;
		//echo "hai";
		//echo "<pre>";print_r($post);
		$role_id = $post['role'];
		$whr =  "role_id = '?'";
		$params = [$whr, $role_id];
		$query=$callConfig->selectQuery(TBL_ROLE_PERMISSIONS,'id,table_name',$whr,'','','');
		//echo $query;
		$tables = $callConfig->getAllRowsNew($query);
		foreach ($tables as  $table) {
			$role_tables[] = $table->table_name;
			$roledata[$table->table_name] = $table->id;
		}
		//print_r($role_tables); exit;
		foreach ($post['data'] as $key => $data) {
			$id = $roledata[$key];
			$field=array('table_name'=>$key,'permission_view'=>$data['view'],'permission_insert'=>$data['insert'],'permission_update'=>$data['update'],'permission_delete'=>$data['delete'],'role_id'=>$role_id,'Current_Status'=>0);
			if(in_array($key, (array)$role_tables)){ 
				//echo "updat# ".$key."<br>";
				$res=$callConfig->updateRecord(TBL_ROLE_PERMISSIONS,$field,'id',$id);
			}else{
				//echo "insert # ".$key."<br>";
				$res=$callConfig->insertRecord(TBL_ROLE_PERMISSIONS,$field);
			}
			
		}	
		//exit;
		return $res;
	}
	#To Get  group  Email Details with number groups  
	function getgroupemaildetails($group_id){
			global $callConfig;
			//$group_id = 1;
			$query=$callConfig->selectQuery(TBL_EMAILGROUPS,'id,name','','','','');
			$emailgroup = $callConfig->getAllRowsNew($query);
			foreach ($emailgroup as  $value) {
				$group[$value->id] = $value->name;
			}
			//print_r($group);
			$whr = "email_groups_id = '?'";
			$params = [$whr, $group_id];
			$query1=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'*',$whr,'','','');
			$emails = $callConfig->getAllRowsNew($query1);
			//print_r($emails);
			foreach ($emails as $key => $value) {
				$whrs = "id = '?'";
				$params = [$whrs, $value->email_id];
				$query3=$callConfig->selectQuery(TBL_EMAIL,'*',$whrs,'','','');
				$email_data = $callConfig->getRowNew($query3);
			//foreach ($emailgroup as  $email_data) {
				$data['id'] = $email_data->id;
				$data['name'] = $email_data->name;
				$data['email'] = $email_data->email;
				$data['status'] = $email_data->Current_Status;
				$whre= "email_id = '?'";
				$params = [$whre, $email_data->id];
				$query11=$callConfig->selectQuery(TBL_EMAILGROUPSEMAIL,'email_groups_id',$whre,'','','');
				//echo $query11;
				$email_grops_list = $callConfig->getAllRowsNew($query11);
				$list_group = array();
				foreach ($email_grops_list as $key => $lisst) {
					$list_group[$lisst->email_groups_id] = $group[$lisst->email_groups_id];
				}
				$data['list'] = $list_group;
				$return [] = $data;
			//}

			} 
		//echo "<pre>"; print_r($return); exit;
			return $return;
		}
	function checkAndInsertapplicationpage($table, $company_id){
			global $callConfig;
			$whr="application_table_name='?' AND company_id= '?'" ;
			$params = [$whr, $table, $company_id];
			//echo $whr; exit;
			$query=$callConfig->selectQuery(TBL_APPLICATION_PAGES,'*',$whr,'','','');
			//echo $query; exit;
			$data = $callConfig->getRowNew($query);
			$res = $data->id;
			if($data->id ==''){
			//echo "rr";
				$field=array('company_id'=>$company_id,'application_table_name'=>$table,'list_page_name'=>strtoupper("COM_LIST_".$table),'insert_page_name'=>strtoupper("COM_INSERT_".$table),'edit_page_name'=>strtoupper("COM_EDIT_".$table),'view_page_name'=>strtoupper("COM_view_".$table),'list_page_type'=>1,'insert_page_type'=>1,'edit_page_type'=>1,'view_page_type'=>1,'Current_Status'=>0,'is_active'=>'0');
				$res=$callConfig->insertRecord(TBL_APPLICATION_PAGES,$field);
			}
			//print_r($data);
			return $res;
		} 
	function viewdattributetype($table, $field, $db){
			global $callConfig;
			$whr="application_table_name='?' AND field_name='?'";
			$params = [$whr, $table, $field];
			//echo $whr; exit;
			$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
			//	echo $query; exit;
			$data = $callConfig->getRowNew($query);
				//echo $data->application_table_name;
			$table_str = urldecode($data->content);
			$table_str = trim($table_str);
			$table_name_data = explode('FROM', $table_str);
			$table_name_data = explode(' ',trim($table_name_data['1']));
			//print_r($table_name_data);
			$table_name_field = $table_name_data['0'];
			//print_r($table_name_data);
			$table_name_field = $table_name_data['0'];
			$data->list = $this->listofTableFieldsByUsedTable($table_name_field,$db);
			//echo "<pre>"; print_r($data); exit;
			return $data;
		}
	function togetprimarykey($table,$db){
			global $callConfig;
			//$query = "SELECT k.column_name, k.table_schema, k.table_name, k.constraint_name FROM information_schema.table_constraints t JOIN information_schema.key_column_usage k where k.constraint_schema = t.constraint_schema and k.table_schema = t.table_schema and k.table_name = t.table_name and t.constraint_type='PRIMARY KEY' AND k.constraint_name = 'PRIMARY' AND t.table_schema='".$db."' AND t.table_name='".$table."'";
			$query = "SELECT COLUMN_NAME as column_name
				FROM information_schema.columns
				WHERE TABLE_SCHEMA = '".$db."'
				  AND TABLE_NAME = '".$table."' 
				  AND COLUMN_KEY = 'PRI'";

			$data = $callConfig->getAllRowsNew($query);
			//print_r($data);
			//$return = array();
			foreach ($data as  $value) {
				if($value->column_name!=""){	
					$return [] = $value->column_name;	
				}else{	
					$return [] = $value->COLUMN_NAME;	
				}
			}
			return $return;
		}
	function togetdatabasepaimairy($tablename, $db, $data){
			global $callConfig;
			$data =  explode(",", urldecode($data));
			$whr = "";
			foreach ($data as $value) {
				$key = explode(":", $value);
				$whr.= $key[0]."='?' AND ";
				$params = [$whr, $key, $key];
			}
			$whr = trim($whr, "AND ");
			//echo $whr; exit;
			$query=$callConfig->selectQuery($tablename,'*',$whr,'','','');
			return $callConfig->getRowNew($query);
			//return $query;
		}
	function updaterowbasepimarydata($fieldnames,$table,$rid){
			global $callConfig;
			//echo "<br>".$rid;
			$data =  explode(",", urldecode($rid));
			$whr = "";
			foreach ($data as $value) {
				$key = explode(":", $value);
				$whr.= $key[0]."='?' AND ";
				$params = [$whr, $key, $key];
			}
			$whr = trim($whr, "AND ");
			$res=$callConfig->updateRecord($table,$fieldnames,'','',$whr);
			return $res;
		}
	function morefilednames($tables,$db){
			global $callConfig;
			$array = array();
			//print_r($tables);
			foreach ($tables as $table) {
			//echo $table. "||||".$db."<br>";
				$array[$table] = $this->listofTableFieldsByUsedTable($table,$db);
			}
			return $array;
		}
	function togetmastertablesdetais(){
			global $callConfig;
			$query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES,'*','','application_table_name ASC','','');
			$lists = $callConfig->getAllRowsNew($query);
			$query11=$callConfig->selectQuery(TBL_STATUS,'*','','','','');
			$statulists = $callConfig->getAllRowsNew($query11);
			foreach ($statulists as  $statulist) {
				$stas[ $statulist->name_value] = $statulist->name;
			//$stasv[$statulist->id] = $statulist->name_value;
				$stasq[ $statulist->name_value] = $statulist->sequence;
			}
		//print_r($stasv);
			foreach($lists as $list){
				$array = array(); $arr1 = array();
				$array['application_table_name'] = $list->application_table_name;
				$array['help_text'] = $list->help_text;
				$whr =  "table_name = '?'";
				$params = [$whr, $list->application_table_name];
				$query1=$callConfig->selectQuery(TBL_STATUSPROGRES,'status',$whr,'','','');
			//echo $query1;
				$status =  $callConfig->getRowNew($query1);
				$status = explode(",", $status->status);

			//print_r($status);
			//SELECT count(*) as total , SUM(IF(`current_status` = 1 , 1,0)) as c1,SUM(IF(`current_status` = 2 , 1,0)) as ss FROM account_groups
				$str = 'SELECT ';
				foreach ($status as   $value) {
				$str.=" SUM(IF(`current_status` = ? , 1,0)) as c?, ";//.$value;
				$params = [$str, $value, $value, $value];
			}
			$str.="count(*) as total FROM ".$list->application_table_name;
			//echo $str;
			$val =  $callConfig->getRowNew($str);
			//print_r($val); exit;
			$arr3 ['status_name'] = "total";
			$arr3 ['count'] = $val->total;
			$arr1[] = $arr3;
			foreach ($status as   $value) {
				$arr ['status_name'] = $stas[$value];
				$vv = 'c'.$value;
				$arr ['count'] = $val->$vv;
				$arr1[$stasq[$value]] = $arr;
			}
			krsort($arr1);
			$array['status'] = $arr1;
			//echo "<pre>"; print_r($array); exit;
			$data[] = $array;
		}
		//echo "<pre>";print_r($data);
		return $data;
	}
	function togetreportdata($data){
		global $callConfig;
		//print_r($data);
		$whr =  "id = '?'";
		$params = [$whr, $data];
		//echo $whr;
		$query=$callConfig->selectQuery(TBL_REPORT_ATTRIBUTES,'*',$whr,'','','');
		//echo $query; exit;
		$report_data =  $callConfig->getRowNew($query);
		//print_r($report_data);
		$report_query = urldecode($report_data->content);
		$attributes = json_decode($report_data->attributes,1);

		foreach ($attributes as $key => $att) {
			//print_r($value); exit;
			if($att['filter'] == 1){
				$filter['lable'] = $att['lable'];
				$filter['filter_type'] = $att['filter_type'];
				$filter['field'] =  $key;
				$filters[]=$filter;
			}
			if($att['display_list'] == 1){
				$dis[] =  $key." as ". str_replace(".", "_", $key);
				$params = [$dis, $key, $key];
			}
		}
		
		//$str = " WHERE ";
		foreach ($filters as  $filt) {
			//print_r($data);
			$field = str_replace(".", "_", $filt["field"]);
			$params = [$field, $filt];
			//echo $data[$field];
			if ($data[$field]!="") {
				if($filt["filter_type"] =='text'){
					$str[]=$filt['field']."  LIKE '%?%'";
					$params = [$str, $filt, $data, $field];
				}
				if($filt["filter_type"] =='select'){
					$str[]=$filt['field']."  = '?'";
					$params = [$str, $filt, $data, $field];
				}
				if($filt["filter_type"] =='date_pick'){
					$str[]=$filt['field']."  < '". date("Y-m-d",strtotime($data[$field]))."'";
					$params = [$str, $filt, $data, $field];
				}
			}
		}
		$strs = '';
		if(count((array)$str)>0){
			$strs = " WHERE ".implode(' AND ', (array)$str);
		}
		$cus_query =  $report_query.$strs;
		$dis_fields = implode(", ", (array)$dis);
		//echo $dis_fields."<br>";
		$cus_query = str_replace("SELECT * FROM", "SELECT ? FROM",$cus_query);
		$params = [$cus_query, $dis_fields, $cus_query];
		//echo $cus_query;
		$lists = $callConfig->getAllRowsNew($cus_query);
		return $lists;

	}
	function insertorupdatereportfilter($data){ 
		global $callConfig;
		//print_r($data); exit;

		$whr = "report_id='?' and field_name='?'";
		$params = [$whr, $data, $data];
		//echo $whr;
		$this->deleterows(TBL_REPORT_FILTERS,$whr);
		//exit;
		$res = $this->insertTablesdata($data, TBL_REPORT_FILTERS);

	}
	function getPurchaseRequisitionsData($id){
		global $callConfig;
		$where = "id = '?'";	
		$params = [$where, $id];
		$query=$callConfig->selectQuery(TBL_PURCHASE_REQUISITIONS,'*',$where,'','','');
		$data = $callConfig->getRowNew($query);
		$pr['data'] = $data;
		//print_r($pr);
		//$pr = $pr->0;
		$whr = "pr_id = '?'";	
		$params = [$whr, $id];
		$pr['items'] = $this->getAllTablesData(TBL_PURCHASE_REQUISITIONS_ITEMS,'*',$whr,'');
		$pr['taxes'] = $this->getAllTablesData(TBL_PURCHASE_REQUISITIONS_ITEMS_TAXES,'*',$whr,'');
		return $pr;
	}
	function toGetDynamicInvoicetables(){ 
		global $callConfig;
		$where = " item_table != '' AND tax_table != '' AND is_active = 0";
		$configesponse = $this->getAllTablesData(TBL_DYNAMIC_MAPPING_TABLES,'*',$where);
		foreach($configesponse AS $con){
			$table_list[$con->application_table_name] ['item']= $con->item_table;
			$table_list[$con->application_table_name] ['tax']= $con->tax_table;
			$table_list[$con->application_table_name] ['option'] = $con->option_url;
			$table_list [$con->application_table_name] ['cancellation_application_table_name'] = $con->cancellation_application_table_name;
		}
	    //print_r($table_list); exit;
		return $table_list;
	}
	function toGetInvoicetables(){ 
		global $callConfig;
		$configesponse = $this->getAllTablesData(TBL_DYNAMIC_MAPPING_TABLES,'*','','');
		foreach($configesponse AS $con){
			$table_list[$con->application_table_name] ['item']= $con->item_table;
			$table_list[$con->application_table_name] ['tax']= $con->tax_table;
			$table_list [$con->application_table_name] ['option'] = $con->option_url;
			$table_list [$con->application_table_name] ['cancellation_application_table_name'] = $con->cancellation_application_table_name;
			
		}
	    //print_r($table_list); exit;
		return $table_list;
	}
	function toGetDynamicTables($table,$option=''){ 
		global $callConfig;
		$where = " application_table_name = '?' AND is_active = 0";
		$params = [$where, $table];
		if($option !=''){
			$where .= " AND option_url = '?'";
			$params = [$where, $option];
		}
		$configesponse = $this->getAllTablesData(TBL_DYNAMIC_MAPPING_TABLES,'*',$where);
		$con = $configesponse[0];
		$table_list[$table] ['item']= $con->item_table;
		$table_list[$table] ['tax']= $con->tax_table;
		$table_list [$table] ['option'] = $con->option_url;
		$table_list [$table] ['cancellation_application_table_name'] = $con->cancellation_application_table_name;
		// foreach($configesponse AS $con){
		// 	$table_list[$con->application_table_name] ['item']= $con->item_table;
		// 	$table_list[$con->application_table_name] ['tax']= $con->tax_table;
		// 	$table_list [$con->application_table_name] ['option'] = $con->option_url;
		// }
	    //print_r($table_list); exit;
		return $table_list;
	}
	function togetitemvalues($item_d){
		global $callConfig;
		if($item_d !=""){
			$whr="id='?'" ;
			$params = [$whr, $item_d];
			$query=$callConfig->selectQuery(TBL_ITEMS,'*',$whr,'','','');
			$data = $callConfig->getRowNew($query);
			if($data->company_id !=""){
				$sosql = "SELECT sum(si.pending_qty) as pending_qty FROM sales_orders so, so_items si WHERE so.company_id = '?' AND so.company_id = si.company_id AND so.id = si.transaction_id AND so.current_status ='".ST_APPROVED."' AND si.item_id = '?' AND so.is_active = si.is_active AND so.is_active = 0";	
				$params = [$sosql, $data->company_id, $item_d];
				$sores = $callConfig->getRowNew($sosql);
				
				$data->sales_order_qty = $sores->pending_qty !=""?$sores->pending_qty:0;

				$posql = "SELECT sum(si.pending_qty) as pending_qty FROM purchase_orders so, po_items si WHERE so.company_id = '?' AND so.company_id = si.company_id AND so.id = si.transaction_id AND so.current_status ='".ST_APPROVED."' AND si.item_id = '?' AND so.is_active = si.is_active AND so.is_active = 0";	
				$params = [$posql, $data->company_id, $item_d];
				$pores = $callConfig->getRowNew($posql);	

				$configsql = "SELECT id , default_inventory_account FROM config where company_id = '?'";
				$params = [$configsql, $data->company_id];
				$configres = $callConfig->getRowNew($configsql);
				if(isset($configres->default_inventory_account)){
					$data->config_inventory_asset_account = $configres->default_inventory_account;
				}
				$isbsql = "SELECT closing_qty,balance_date FROM `item_st_balances` WHERE company_id = '?' AND item_id = '?' ORDER BY balance_date DESC limit 1";
				$params = [$isbsql, $callConfig->company_id, $item_d];
				$isbres = $callConfig->getRowNew($isbsql);
				if($isbres->closing_qty !=""){
					$data->stock_in_hand = $isbres->closing_qty !=""?$isbres->closing_qty:0;
				}
				if($isbres->balance_date !=""){
					$data->balance_date = $isbres->balance_date !=""?$isbres->balance_date:0;
				}
				if($isbres->pending_qty !=""){
					$data->purchase_order_qty = $pores->pending_qty !=""?$pores->pending_qty:0;
					if($isbres->pending_qty !=""){
						$available_qty = ($isbres->closing_qty + $pores->pending_qty)-$sores->pending_qty;
					}
				}else{
					$data->purchase_order_qty = 0;
				}
				
				$data->available_qty = $available_qty !=""?$available_qty:0;

				
				if($data->category_id !=""){
					$icsql = "SELECT id , inventory_asset_account, inventory_adjustment_account, inventory_cost_variance_account, cogs_account, purchase_expense_account, sales_revenue_account, sales_discount_account, sales_return_account, purchase_price_variance_account, freight_expense_account, packaging_expense_account, sales_tax_payable_account, purchase_tax_payable_account, promotions_and_discounts_account, sales_allowances_account, deferred_revenue_account, ap_account, ar_account, accrued_expenses_account, prepaid_expenses_account, wip_asset_account, goods_received_not_invoiced_account, invoiced_not_received_account FROM item_categories where id = '?' AND company_id = '?' AND is_active = 0";
					$params = [$icsql, $data->category_id, $data->company_id];
					$icategory = $callConfig->getRowNew($icsql);
					$data->item_cat_inventory_asset_account = $icategory->inventory_asset_account;
					$data->item_cat_goods_received_not_invoiced_accountt = $icategory->goods_received_not_invoiced_account;
				} 
			}
			
			if($data->hsn_and_sac_code_id !=""){
				$sql = "SELECT id , code, gst, code_description FROM `hsn_and_sac_codes` where id = '?'";
				$params = [$sql, $data->hsn_and_sac_code_id];
				$data2 = $callConfig->getRowNew($sql);
				$data->hsn_code_id = $data2->id;
				$data->hsn_code = $data2->code;		
				$data->hsn_code_description = $data2->code_description;
				$data->igst = $data2->gst;
			}
		}
		return $data;
	}
	function togetitemwadayrate($item_d, $company_id){
		global $callConfig;
		$day_rate = 0;
		if($company_id !="" AND $item_d !=""){
			$whrstp1="company_id = '?'  AND item_id= '?'";
			$params = [$whrstp1, $company_id, $item_d];
			$query1=$callConfig->selectQuery('item_wa_balances','day_rate',$whrstp1,'balance_date DESC','0','1');
			//echo $query1;
			$data = $callConfig->getRowNew($query1);
			$day_rate=$data->day_rate;	
		}
		return $day_rate;
	}
	function toGetInvoicetabledata($table){ 
		global $callConfig;

		$where = "application_table_name = '?'";
		$params = [$where, $table];
		//echo $where; exit;	
		$query = $callConfig->selectQuery(TBL_DYNAMIC_MAPPING_TABLES,'*',$where,'','','');
		//echo $query; exit;
		$data = $callConfig->getRowNew($query);
		$table_list ['item']= $data->item_table;
		$table_list['tax']= $data->tax_table;
		$table_list['option'] = $data->option_url;
		$table_list['particulars'] = $data->particulars;
		$table_list['cancellation_application_table_name'] = $data->cancellation_application_table_name;
	    //print_r($table_list); exit;
		return $table_list;
	}
	function toGetApplicationSerialNumber($table_name){
		global $callConfig;
		$whr="application_table_name = '?'";
		$params = [$whr, $table_name];
		$query=$callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS,'*',$whr,'','','');
		$res = $callConfig->getRowNew($query);
		$le = $res->doc_no_length;
		$var = "%0?d";
		$params = [$var, $le];
		$data = $res->pre_fix.sprintf($var,$res->serial_number+1).$res->post_fix;
		if(isset($res->id)){
			$update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS,array('serial_number' =>$res->serial_number+1),'id',$res->id);
		}
		return trim($data);
	}
	function updaterandomvalueforlotandseries($post, $trnid){
		global $callConfig;
		$query = "UPDATE item_lot_nos SET transaction_id =? WHERE random_no = '?'";
		$params = [$query, $trnid, $post];
		$res = $callConfig->executeQuery($query);
		$query = "UPDATE item_serial_nos SET transaction_id =? WHERE random_no = '?'";
		$params = [$query, $trnid, $post];
		$res = $callConfig->executeQuery($query);
	}
	function insertInvoiceType($post, $table, $get){
		global $callConfig;
		$return = array();
		$sales_tables = array('enquiries','estimates','delivery_orders','sales_orders','sales_returns');
		$return['post'] =$post; 
		$return['table'] =$table; 
		$return['get'] =$get; 
		$type = $get['type']; 
		$fieldarray  = $this->toGetDynamicTables($table,$get['page_option']);
		$return['fieldarray'] =$fieldarray; 
		
		if(count((array)$post['file_add'])>0){
			$file_upload = $this->multiplefileupload($post['file_add']);
		}//exit;


		// tax splite details Start	
		$tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type, sales_account_code FROM `tax_authorities_rates_split` WHERE company_id = '?' AND is_active =0";
		$params = [$tax_authorities_rates_splitsql, $post];
        $tax_authorities_rates_splitres1 = $callConfig->getAllRowsNew($tax_authorities_rates_splitsql);
        if(count((array)$tax_authorities_rates_splitres1) !=0){
	       	$tax_authorities_rates_splitres = json_decode(json_encode($tax_authorities_rates_splitres1), true);
			$return['tax_authorities_rates_splitres'] = $tax_authorities_rates_splitres;
	       
			if($post['originator_state_id'] == $post['supplier_state_id']){ 
				$tax_flag = "S";
				$split_1_name = $tax_authorities_rates_splitres[0]['split_name'];
			    $split_2_name = $tax_authorities_rates_splitres[1]['split_name'];
			    $split_1 = $tax_authorities_rates_splitres[0]['tax_rate'];
			    $split_2 = $tax_authorities_rates_splitres[1]['tax_rate'];
			    if(in_array($table, (array)$sales_tables)){
				    $split_1_gl_account_code =  $tax_authorities_rates_splitres[0]['sales_account_code'];
				    $split_2_gl_account_code =  $tax_authorities_rates_splitres[1]['sales_account_code'];
				}else{
				    $split_1_gl_account_code =  $tax_authorities_rates_splitres[0]['account_code'];
				    $split_2_gl_account_code =  $tax_authorities_rates_splitres[1]['account_code'];
				}
			}else{
				$tax_flag = "I";
				$split_1_name = $tax_authorities_rates_splitres[2]['split_name'];
			    $split_1 = $tax_authorities_rates_splitres[2]['tax_rate'];
			    if(in_array($table, (array)$sales_tables)){
			    	$split_1_gl_account_code =  $tax_authorities_rates_splitres[2]['sales_account_code'];
				}else{
					$split_1_gl_account_code =  $tax_authorities_rates_splitres[2]['account_code'];
				}
			}
			$return['split_1_name'] = $split_1_name;
			$return['split_1'] = $split_1;
		}
		// tax splite details End
		$txperc1 = 0;
		foreach ($post['productCode'] as $key => $item) {
			if($post['tax'][$key] !='0' AND $post['tax'][$key]!=''){ 
				if($tax_flag == "S"){
					$txperc1 = $post['tax'][$key]*$split_1;
					$txvalc1 += $post['ptax_amount'][$key]*$split_1;
					$txperc2 = $post['tax'][$key]*$split_2;
					$txvalc2 += $post['ptax_amount'][$key]*$split_2;
				}else{
					$txperc1 = $post['tax'][$key]*$split_1;
					$txvalc1 += $post['ptax_amount'][$key]*$split_1;
				}
			}
		}
		
		$total_tax_split_details = array();
		if($txperc1 == '0'){
			if($tax_flag == "S"){
				$total_tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txperc2,'tax_amount'=>$txvalc2,'account_code'=>$split_2_gl_account_code));  
			}else{
				$total_tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code)); 
			}
		}
		$total_tax_split_details_json = json_encode($total_tax_split_details);

		// additional Charges Start
		$additional_charges = array();
		foreach($post['shipping_tax_ac'] as $keyid=>$stacvalue){
			$additional_charges[] = array('shipping_tax'=>$post['shipping_tax_ac'][$keyid],'shipping_amount'=>$post['shipping_amount_ac'][$keyid],'shipping_tax_amount'=>$post['shipping_tax_amount_ac'][$keyid],'charge_name'=>$post['charge_name_ac'][$keyid],'charge_id'=>$post['charge_id_ac'][$keyid]); 
		}
		$additional_charges_json  = json_encode((array)$additional_charges);
		// additional Charges End
		

		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT;
		$status_main = $status;
		$invoiced_at = $this->dateconvertdbformat($post['transaction_date']);
		$doc_ref_date = $this->dateconvertdbformat($post['doc_ref_date']);
		$due_date = $this->dateconvertdbformat($post['due_date']);
		$post['party_id'] = $post['party_id']!=""?$post['party_id']:0;
		$post['originator'] = $post['originator']!=""?$post['originator']:$post['user_id'];

		$rounding_off = round($post['grandtotal']) - $post['grandtotal'];
		$post['grandtotal'] = $post['grandtotal'] + $rounding_off;



		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['transaction_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_type'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'due_date'=>$due_date,'transaction_location_id'=>$post['transaction_location_id'],'transaction_warehouse_id'=>$post['transaction_warehouse_id'],'pending_amount'=>$post['grandtotal'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$doc_ref_date,'originator_currency_id'=>$post['originator_currency_id'],'originator_state_id'=>$post['originator_state_id'],'supplier_state_id'=>$post['supplier_state_id'],'additional_charges'=>$additional_charges_json,'cost_center_id'=>$post['cost_center_id'],'total_tax_split_details'=>$total_tax_split_details_json,'rounding_off'=>$rounding_off);
		//empty check valaidation part added With azure
			$fieldnames['discount_value'] = ($fieldnames['discount_value']!="" AND is_null($fieldnames['discount_value'])  !=1)?$fieldnames['discount_value']:0;
			$fieldnames['discount_amount'] = ($fieldnames['discount_amount']!="" AND is_null($fieldnames['discount_amount'])  !=1)?$fieldnames['discount_amount']:0;
			$fieldnames['total_amount'] = ($fieldnames['total_amount']!="" AND is_null($fieldnames['total_amount'])  !=1)?$fieldnames['total_amount']:0;
			$fieldnames['shipping_tax'] = ($fieldnames['shipping_tax']!="" AND is_null($fieldnames['shipping_tax']) !=1)?$fieldnames['shipping_tax']:0;
			$fieldnames['shipping_amount'] = ($fieldnames['shipping_amount']!="" AND is_null($fieldnames['shipping_amount'])  !=1)?$fieldnames['shipping_amount']:0;
			$fieldnames['shipping_tax_amount'] = ($fieldnames['shipping_tax_amount']!="" AND is_null($fieldnames['shipping_tax_amount'])  !=1)?$fieldnames['shipping_tax_amount']:0;
			$fieldnames['total_tds_amount'] = ($fieldnames['total_tds_amount']!="" AND is_null($fieldnames['total_tds_amount'])  !=1)?$fieldnames['total_tds_amount']:0;
			$fieldnames['advance_payment'] = ($fieldnames['advance_payment']!="" AND is_null($fieldnames['advance_payment'])  !=1)?$fieldnames['advance_payment']:0;
			$fieldnames['pending_advance_payment'] = ($fieldnames['pending_advance_payment']!="" AND is_null($fieldnames['pending_advance_payment'])  !=1)?$fieldnames['pending_advance_payment']:0;
			$fieldnames['tax_state'] = $tax_flag !=""?$tax_flag:"I";
		//empty check valaidation part added With azure
		

		//Advance payment parts Start	
		$advancepaytableadd = array('sales_invoices','purchase_invoices');
		if(in_array($table, (array)$advancepaytableadd)){
			$post['advance_payment'] = $post['advance_payment']!=""?$post['advance_payment']:0;
			$fieldnames['pending_amount'] = $fieldnames['total_amount']-$post['advance_payment'];
			$fieldnames['advance_payment'] = $post['advance_payment']!=""?$post['advance_payment']:0;
			$old_advance_payment = $post['old_advance_payment'] !=""?$post['old_advance_payment']:0;
			
			$updatesql = "UPDATE ? SET pending_advance_payment=(pending_advance_payment+?',updated_date='".date('Y-m-d H:i:s')."' WHERE id = ".$post['ref_id'];
			$params = [$updatesql, $post, $old_advance_payment, $post, $post, $post];
			$return['pending_advance_payment'] = $updatesql;
			$insert_data1['advance_payment']= $post['advance_payment'];
			$insert_data1['old_advance_payment'] = $old_advance_payment;
			$insert_data1['pending_advance_payment'] = $updatesql;
			$insert_data1['pending_advance_payment_update'] = $callConfig->executeQuery($updatesql); 
		}
		//Advance payment parts End

		if(isset($post['payment_term_history']) AND $post['payment_term_history'] !=""){
			$payment_term_history = json_encode($post['payment_term_history']);
			$fieldnames['payment_term_history'] = $payment_term_history;
		}
		if(isset($post['follow_up_level_ids']) AND $post['follow_up_level_ids'] !=""){
			$follow_up_level_ids = json_encode($post['follow_up_level_ids']);
			$fieldnames['follow_up_level_ids'] = $follow_up_level_ids;
		}
		if(isset($post['additional_attributes'])){
			$fieldnames['additional_attributes'] = $post['additional_attributes'];
		}
		if($table == 'stock_requests'){
			$fieldnames['location_id'] = $post['location_id'];
			$fieldnames['transfer_location_id'] = $post['transfer_location_id'];
		}  
		if(isset($post['salesmen_id'])){
			$fieldnames['salesmen_id'] = $post['salesmen_id'];
		}
		$fieldnames['compliance_specification_documents'] = json_encode((array)$post['compliance_specification_documents']);
		//Main Table Insert Code Start
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			//start added records sla_tracking_logs
				$slaarray = $post;
				$slaarray['transaction_table'] =$table; 
				$slaarray['transaction_id'] =$post['hid']; 
				$slaarray['remarks'] ='UPDATE';
				$slaarray['previous_status'] =$status; 
				$this->insertSLATrackingLogs($slaarray);
			//End Added records sla_tracking_logs
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
		}else{
			$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			$fieldnames['is_active'] = '0';
			$transation = $this->toGetApplicationSerialNumber($table);
			$fieldnames['transaction_number'] = $transation;
		 	$insert_data=$callConfig->insertRecord($table,$fieldnames);
			$insert_id = $insert_data['LASTID'];
			//start added records sla_tracking_logs
				$slaarray = $post;
				$slaarray['transaction_table'] =$table; 
				$slaarray['transaction_id'] =$insert_id; 
				$slaarray['remarks'] ='INSERT';
				$slaarray['previous_status'] =$status;
				$this->insertSLATrackingLogs($slaarray);
			//End Added records sla_tracking_logs
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			if(($table =='goods_receipt_notes' || $table =='delivery_orders') && $post['random']){
				$ram = $this->updaterandomvalueforlotandseries($post, $insert_id);
			}
		}
		$return['res'] =$res; 
		//Main Table Insert Code End
		if($insert_id > 0){
			if($type == 'update'){
				// update all items table start
				$updaterecord=array('is_active'=>NULL,'updated_date'=>date('Y-m-d h:i:s'),'updated_by'=>$post['user_id']);
				$callConfig->updateRecord($fieldarray[$table]['item'],$updaterecord,'transaction_id',$insert_id);
				$callConfig->updateRecord($fieldarray[$table]['tax'],$updaterecord,'transaction_id',$insert_id);
				// update all items table end
			}
			foreach ($post['productCode'] as $key => $item){
				$litemids[] = $item;
				if($post['doc_ref_date'][$key] !=''){
					$item_doc_ref_date =  $this->dateconvertdbformat($post['doc_ref_date'][$key]);
				}
				$req_date = $this->dateconvertdbformat($post['req_date'][$key]);
				$mainitem = $this->togetitemvalues($item);
				$day_rate = $this->togetitemwadayrate($item, $post['company_id']);
				$fifo_day_rate = $this->togetitemfifodayrate($itemres->item_id, $get['company_id']);
				$lifo_day_rate = $this->togetitemlifodayrate($itemres->item_id, $get['company_id']);
				$wa_day_rate = $day_rate;
				$wa_day_rate = ($wa_day_rate != '0' AND $wa_day_rate !="")?$wa_day_rate:$post['price'][$key];
				$fifo_day_rate = ($fifo_day_rate != '0' AND $fifo_day_rate !="")?$fifo_day_rate:$post['price'][$key];
				$lifo_day_rate = ($lifo_day_rate != '0' AND $lifo_day_rate !="")?$lifo_day_rate:$post['price'][$key];
				$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'req_date'=>$req_date,'account_code'=>$post['purchase_account'][$key],'item_wa_cost'=>$wa_day_rate,'item_fifo_cost'=>$fifo_day_rate,'item_lifo_cost'=>$lifo_day_rate,'pending_qty'=>$post['quantity'][$key],'is_active'=>'0','current_status'=>'0','ref_transaction_item_table_id'=>$post['ref_transaction_item_table_id'][$key]);

				if(in_array($table, (array)$sales_tables)){
					$itemfieldnames['item_st_cost'] = $mainitem->sale_price; 
				}else{
					$itemfieldnames['item_st_cost'] = $mainitem->purchase_price; 
				}
				if($table == 'petty_cash' ){
					$itemfieldnames['doc_ref_no'] = $post['doc_ref_no'][$key];
					$itemfieldnames['doc_ref_date'] = $item_doc_ref_date;
				}
				if($table == 'goods_receipt_notes' OR $table == 'delivery_orders'){
					$itemfieldnames['invoice_item_quantity'] = $post['invoiced_quantity'][$key];
				}
				if($table == 'requisitions' ){
					$itemfieldnames['customer_description'] = $post['customer_description'][$key];
				}
				if($table == 'stock_adjustments'){
					$reason = explode('$', $post['respon'][$key]);
					$itemfieldnames['reason_id'] = $reason[0];
					$reason_value = $reason[1];
				}
				
				$item_detaileeee = array();
				foreach($post['item_details'] as $qkey => $qkem) {
					$item_detaileeee[$qkey] = $qkem[$key];
				}
				$item_details  = json_encode($item_detaileeee);
				$itemfieldnames['item_details'] = $item_details;
				

				$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['taxid'][$key],'tax_name'=>$post['tax_name'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key],'is_active'=>'0','current_status'=>'0');
				if($post['tax'][$key] !='0' AND $post['tax'][$key]!=''){
					if($tax_flag == "S"){
						$txper1 = $post['tax'][$key]*$split_1;
						$txval1 = $post['ptax_amount'][$key]*$split_1;
						$txper2 = $post['tax'][$key]*$split_2;
						$txval2 = $post['ptax_amount'][$key]*$split_2;
						$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txper2,'tax_amount'=>$txval2,'account_code'=>$split_2_gl_account_code));  
						$taxfield['tax_split_details'] = json_encode($tax_split_details);
					}else{
						$txper1 = $post['tax'][$key]*$split_1;
						$txval1 = $post['ptax_amount'][$key]*$split_1;
						$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code)); 
						$taxfield['tax_split_details'] = json_encode($tax_split_details);
					}
				}
				$return['taxsss'][] = $taxfield;

				if($type == 'update'){
					//Item table Insert data Start
					$transaction_item_row_id = $post['transaction_item_row_id'][$key];
					$where = "id = '?'";
					$params = [$where, $transaction_item_row_id];
					$itemquery=$callConfig->selectQuery($fieldarray[$table]['item'],'*',$where,'','','');
					$data = $callConfig->getRowNew($itemquery);
					
					$itemfieldnames['approved_quantity'] = $post['quantity'][$key];
					$itemfieldnames['pending_qty'] = $post['quantity'][$key];
					//if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] || $data->approved_quantity != $itemfieldnames['approved_quantity'] ){
						if($data->id !=''){
							$itemfieldnames['updated_date'] = date('Y-m-d h:i:s');
							$itemfieldnames['updated_by'] = $get['user_id'];
							$res=$callConfig->updateRecord($fieldarray[$table]['item'],$itemfieldnames,'id',$data->id);
							$return['item_id'][] =$res;
						}else{
							$itemfieldnames['created_date'] = date('Y-m-d h:i:s');
							$itemfieldnames['created_by'] = $get['user_id'];
							$item_transaction=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
							$transaction_item_row_id = $item_transaction['LASTID'];
						 	$return['item_id'][] =$item_id;
						}
					//}
					//Item table Insert data End 
					//Tax table Insert data Start
					if($post['tax'][$key]!='0' AND $post['tax'][$key]!=''){		
						$where = "transaction_id = '?' AND transaction_item_row_id	= '?'";
						$params = [$where, $insert_id, $transaction_item_row_id];
						$itemquery=$callConfig->selectQuery($fieldarray[$table]['tax'],'*',$where,'','','');
						$data_tax = $callConfig->getRowNew($itemquery);
						if($data_tax->id !=''){
							//if($data->tax_amount != $itemfieldnames['tax_amount']){
								$taxfield['updated_date'] = date('Y-m-d h:i:s');
								$taxfield['updated_by'] = $get['user_id'];
								$where = "id = '?'";
								$params = [$where, $data_tax->id];
								$res=$callConfig->updateRecord($fieldarray[$table]['tax'],$taxfield,'','',$where);
							//}
						}else{
							$taxfield['created_date'] = date('Y-m-d h:i:s');
							$taxfield['created_by'] = $get['user_id'];
							$taxfield['transaction_item_row_id'] = $transaction_item_row_id; 
							$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
						}
					}
					//Tax table Insert data End
				}else{
					//Item table Insert data Start
					$itemfieldnames['created_date'] = date('Y-m-d h:i:s');
					$itemfieldnames['created_by'] = $get['user_id'];
					$item_transaction=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames); 
					$transaction_item_row_id = $item_transaction['LASTID'];
					//Item table Insert data End 
					//Tax table Insert data Start
					if($post['tax'][$key]!='0' AND $post['tax'][$key]!=''){
						$taxfield['created_date'] = date('Y-m-d h:i:s');
						$taxfield['created_by'] = $get['user_id'];
						$taxfield['transaction_item_row_id'] = $transaction_item_row_id; 
						$itax_id=$callConfig->insertRecord($fieldarray[$table]['tax'],$taxfield);
						$return['itax_id'] =$itax_id; 	
					}
					//Tax table Insert data Start
				}
			}   // Product loop close
		}	
		
		$insert_data['table'] = $table;
		//TCS Part In Sales Invoice Only
		if($table == 'sales_invoices'){
			$insert_data['sales_invoices'] = $this->updateTransactionTCS($table,$insert_id,$post);		
		}
		//TCS Part In Sales Invoice Only
		if($post['amendment'] == "Y"){
			$insert_data['insert_amendment'] = $this->InsertAmendment($table,$insert_id,$post);		
		}  
		$return['insert_data'] =$insert_data;
		return $insert_data;
	}
	
	function invoiceUpdates($table,$len){
		global $callConfig;
		
		if(count((array)$len)>1){
			//echo "ravi"; exit;
			
			//$average_balance = $this->averagebalance($len);
			//echo "<pre>";print_r($len); exit;
			//print_r($len); //exit;
			//$len = array_merge($len,$average_balance);
			//print_r($len); exit;
			$GRN_loc_item_ledgerst = $this->insertorupdateitem_ledgerst($len);
			$GRN_loc_item_ledgerwa = $this->insertorupdateitem_ledgerwa($len);
			//$GRN_loc_item_ledgerlifo = $this->insertorupdateitem_ledgerlifo($len);

			//$GRN_loc_item_ledgerfifo = $this->insertorupdateitem_ledgerfifo($len);

			$GRN_loc_item_wa_balances = $this->insertoupdatelocitemwabalances($len);
			$GRN_loc_item_st_balances = $this->insertoupdatelocitemstbalances($len);
			$GRN_loc_item_wa_balances = $this->insertoupdateitemwabalances($len);
			$GRN_loc_item_st_balances = $this->insertoupdateitemstbalances($len);
			$GRN_loc_item_fifio = $this->insertorupdateitemfifo($len);
			//print_r($len); exit;
			//$createudate = tocreatejounalvoucherbaseallcases()
		}
	}
	function tocreatejounalvoucherbaseallcases($table_name,$ref_id,$type=''){
		global $callConfig;	

		$defaultaccount = array('inventory_asset_account' => '1003809', 'inventory_adjustment_account'=>'50000106', 'cogs_account' => '50000151', 'purchase_expense_account'=>'20003507', 'sales_revenue_account'=>'40001101', 'sales_return_account'=>'10003700', 'ap_account'=>'20003501', 'ar_account'=>'1004001', 'goods_received_not_invoiced_account'=>'20003506', 'goods_sold_not_invoiced_account'=>'1004003','default_cash_acct'=>'10005101', 'default_purchase_discount_account'=>'40001109', 'jv_suspense_acc'=>'200037018', 'tds_clearing_account'=>'20003701', 'tds_payables'=>'20003702', 'tcs_clearing_account'=>'20003701', 'tcs_payables'=>'20003716', 'default_sales_discount_account'=>'50002714', 'default_round_off_account'=>'5002557', 'config_inventory_asset_account'=>'1003809'); 
		$rounddigits = 2;
		$whr="id=".$ref_id;
		$query1=$callConfig->selectQuery($table_name,'*',$whr,'','','');
		$row = $callConfig->getRowNew($query1);
		$row = (array)$row;
		
		$partysql = "SELECT au.*, (SELECT department_id FROM cost_centers cc WHERE au.company_id = cc.company_id AND au.cost_center = cc.id AND au.is_active = cc.is_active) as department_id FROM application_users au WHERE au.company_id = '?' AND au.id = '?' AND au.is_active = 0";	
		$params = [$partysql, $row, $row];
		$user = $callConfig->getRowNew($partysql);

		$transaction_departsql = "SELECT department_id FROM cost_centers WHERE company_id = '?' AND id = '?' AND is_active = 0";
		$params = [$transaction_departsql, $row, $row];
		$transdepartres = $callConfig->getRowNew($transaction_departsql);

		$whrs = "company_id= ? AND is_active = 0";
		$params = [$whrs, $row];
		$query = $callConfig->selectQuery('config','*',$whrs,'','','');
		$config = $callConfig->getRowNew($query);
		
		$suspense_account = $config->jv_suspense_acc!=""?$config->jv_suspense_acc:$defaultaccount['jv_suspense_acc'];
		$default_round_off_account = $config->default_round_off_account!=""?$config->default_round_off_account:$defaultaccount['default_round_off_account'];

		// $whr = "application_table_name= '".$table_name."' AND field_name = '".$filed_name."'";
		// $query=$callConfig->selectQuery(TBL_DISPLAYATTRIBUTES_TYPE,'*',$whr,'','','');
		// $data = $callConfig->getRowNew($query);

		$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '?' AND application_table_name = '?' AND field_name = 'party_id'";	
		$params = [$display_attribute_typessql, $row, $table_name];
		$parttable_name = $callConfig->getRowNew($display_attribute_typessql);
		$qquery = urldecode($parttable_name->content);
		$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
		if(isset($explosql[1])){
			$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
		}
		$partytable = trim($explosql[0]); //get Table name


		if($table_name == 'debit_notes' OR $table_name == 'credit_notes'){
			if($row['ref_type'] == "purchase_invoices" || $row['ref_type'] == "supplier_bills" || $row['ref_type'] == "purchase_invoice_cancellations"  || $row['ref_type'] == "supplier_bill_cancellations"){
				$partytable = "vendors";
			}elseif($row['ref_type'] == "sales_invoices" || $row['ref_type'] == "sales_invoice_cancellations"){
				$partytable = "customers";
			}	
		}	
		$partysql = "SELECT account_code FROM ? WHERE company_id = '?' AND id  = '?' AND is_active = 0";	
		$params = [$partysql, $partytable, $row, $row];
		$partyres = $callConfig->getRowNew($partysql);
		$party_account_number = $partyres->account_code; //get customers and vendor id
		$tables = $this->toGetInvoicetabledata($table_name);  // Get Transaction Tables
		$particulars = $tables['particulars'];
		$taxitems = $hsnsacid= $hsntax = $taxwithaccount = array();
		
		if($ref_id !=""){
			$whr2="transaction_id=? AND is_active = 0"; // get Trnsaction Items List
			$params = [$whr2, $ref_id];
			$taxitemsql=$callConfig->selectQuery($tables['tax'],'company_id,transaction_id, transaction_item_id,tax_id,tax_amount,tax_split_details',$whr2,'','','');
			$taxitemres = $callConfig->getAllRowsNew($taxitemsql);	
			foreach($taxitemres as $titem){
				$taxitems[$titem->transaction_item_id] = $titem;
			}

			$whr2="transaction_id=? AND is_active = 0"; // get Trnsaction Items List
			$params = [$whr2, $ref_id];
			$query2=$callConfig->selectQuery($tables['item'],'*',$whr2,'','','');
			$itemrows = $callConfig->getAllRowsNew($query2);
			$total_item = count((array)$itemrows); 
			if($total_item !=0){
				$peritemaditional_charge = $row['shipping_tax_amount']/$total_item;
			}else{
				$peritemaditional_charge = $row['shipping_tax_amount'];
			}
			$itemsitemid = array(); 
			foreach($itemrows as $itemrow) {
				$itemsitemid[$itemrow->item_id] = $itemrow->item_id;
			}
			$items =$itemcatid =  array();
			if(count((array)$itemsitemid) !=0){
				$itemsitemids = implode(',', (array)$itemsitemid);
				// Get All Items Accounts
				$itemssql = "SELECT id, category_id, inventory_asset_account, inventory_adjustment_account, inventory_cost_variance_account, cogs_account, purchase_expense_account, sales_revenue_account, sales_discount_account, sales_return_account, purchase_price_variance_account, freight_expense_account, packaging_expense_account, sales_tax_payable_account, purchase_tax_payable_account, promotions_and_discounts_account, sales_allowances_account, deferred_revenue_account, ap_account, ar_account, accrued_expenses_account, prepaid_expenses_account, wip_asset_account, goods_received_not_invoiced_account, invoiced_not_received_account, goods_sold_not_invoiced_account FROM items WHERE company_id = '?' AND id IN(?) AND is_active = 0";	
				$params = [$itemssql, $row, $itemsitemids];
				$itemsres = $callConfig->getAllRowsNew($itemssql);
				foreach($itemsres as $itm){
					$items[$itm->id] = $itm;
					$itemcatid[$itm->category_id] = $itm->category_id;
				}	
			}
			if(count((array)$itemcatid) !=0){
				$itemcatids = implode(',', (array)$itemcatid);
				// get All Items Category Accounts
				$item_categoriessql = "SELECT id, inventory_asset_account, inventory_adjustment_account, inventory_cost_variance_account, cogs_account, purchase_expense_account, sales_revenue_account, sales_discount_account, sales_return_account, purchase_price_variance_account, freight_expense_account, packaging_expense_account, sales_tax_payable_account, purchase_tax_payable_account, promotions_and_discounts_account, sales_allowances_account, deferred_revenue_account, ap_account, ar_account, accrued_expenses_account, prepaid_expenses_account, wip_asset_account, goods_received_not_invoiced_account, invoiced_not_received_account, goods_sold_not_invoiced_account FROM item_categories WHERE company_id = '?' AND id IN(?) AND is_active = 0";	
				$params = [$item_categoriessql, $row, $itemcatids];
				$item_categoriesres = $callConfig->getAllRowsNew($item_categoriessql);
				$item_categories = array();	
				foreach($item_categoriesres as $itcat){
					$item_categories[$itcat->id] = $itcat;
				}
			}
		}

		

		$transaction_location_id = $row['transaction_location_id'] !=""?$row['transaction_location_id']:$user->location_id;
		$department_id = $transdepartres->department_id!=""?$transdepartres->department_id:$user->department_id;
		$cost_center_id = $row['cost_center_id'] !=""?$row['cost_center_id']:$user->cost_center;

		$notadded_tax = array('goods_receipt_notes','delivery_orders','credit_notes','debit_notes');
		$notadded_discount = array('goods_receipt_notes','delivery_orders','credit_notes','debit_notes');
		$notadded_additionalcharge = array('goods_receipt_notes','delivery_orders','credit_notes','debit_notes');
		$notadded_party = array('goods_receipt_notes','delivery_orders');
		$goodrecnotinventryaccount = array('goods_receipt_notes','supplier_bills','purchase_invoices','delivery_orders','sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations','sales_invoice_cancellations');

		// $part_account_credit = array('supplier_bills');
		// $part_account_debit = array('goods_receipt_notes');
		
		$tax_debit_item = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');
		$tax_credit_item = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations');
		$discount_debit_item = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations');
		$discount_credit_item = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');
		$additional_charge_debit = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');
		$additional_charge_credit = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations');
		$tds_debit = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');
		$tds_credit = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations');

		$party_debit = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations');
		$party_credit = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');

		$coasql = "SELECT account_code FROM `chart_of_accounts` WHERE company_id = '?' AND allow_posting = 0 AND is_active = 0";	
		$params = [$coasql, $row];
		$coares = $callConfig->getAllRowsNew($coasql);
		$chartofaccount = array();
		foreach($coares as $coa){
			$chartofaccount[$coa->account_code] = $coa->account_code;
		}
		$total_debit_amt = $total_credit_amt = 0;
		foreach ($itemrows as $key => $itemrow){ // all entry with transaction items
			$itemrow = (array)$itemrow;
			$itemtabledetails = $items[$itemrow['item_id']];
			$itemcategoorytabledetails = $item_categories[$itemtabledetails->category_id];

			$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>$itemrow['item_id'], 'particulars'=>$particulars, 'item_name'=>$itemrow['item_name'],  'description'=>$itemrow['description'],  'purchase_uom'=>$itemrow['purchase_uom'],  'item_quantity'=>$itemrow['item_quantity'],  'item_price'=>$itemrow['item_price'],  'item_amount'=>$itemrow['item_amount'],  'tax_amount'=>$itemrow['tax_amount'],  'discount_amount'=>$itemrow['discount_amount'],  'total_amount'=>$itemrow['total_amount'],  'name_type'=>'C',  'doc_ref_no'=>$itemrow['doc_ref_no'],  'doc_ref_date'=>$itemrow['doc_ref_date'],  'req_date'=>$itemrow['req_date'],  'approved_quantity'=>$itemrow['approved_quantity'],  'transaction_type'=>$itemrow['transaction_type'],  'transaction_qty'=>$itemrow['transaction_qty'],  'pending_qty'=>$itemrow['pending_qty'],  'is_active'=>'0',  'current_status'=>'0','created_by'=>$itemrow['created_by']);

			if($table_name == 'goods_receipt_notes'){
				//goods_received_not_invoiced_account
				$itemaccount_code = "";
				if($itemtabledetails->goods_received_not_invoiced_account !="" AND $itemtabledetails->goods_received_not_invoiced_account !="0"){
					$itemaccount_code = $itemtabledetails->goods_received_not_invoiced_account;
				}elseif($itemcategoorytabledetails->goods_received_not_invoiced_account !="" AND $itemcategoorytabledetails->goods_received_not_invoiced_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->goods_received_not_invoiced_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['goods_received_not_invoiced_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}

			 	$jvary['credit_amount'] = round($itemrow['item_amount'],$rounddigits);
			 	$jvary['debit_amount'] = 0;
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;
				//inventory_asset_account
				$itemaccount_code = "";
				if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemtabledetails->inventory_asset_account;
				}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
				$jvary['account_code'] = $itemaccount_code;
				$jvary['credit_amount'] = 0;
			 	$jvary['debit_amount'] = round($itemrow['item_amount'],$rounddigits);
				$itemref[$itemaccount_code][] = $jvary;
			
			}elseif($table_name == 'delivery_orders'){
				//goods_received_not_invoiced_account
				$itemaccount_code = "";
				if($itemtabledetails->default_costing_type == "W"){
					$sales_profitt_st_item_codt = ($itemrow['item_wa_cost'] !="" AND $itemrow['item_wa_cost'] !="0")?$itemrow['item_wa_cost']:$itemrow['item_price'];
				}elseif($itemtabledetails->default_costing_type == "F"){
					$sales_profitt_st_item_codt = ($itemrow['item_fifo_cost'] !="" AND $itemrow['item_fifo_cost'] !="0")?$itemrow['item_fifo_cost']:$itemrow['item_price'];
				}else{
					$sales_profitt_st_item_codt = ($itemrow['item_lifo_cost'] !="" AND $itemrow['item_lifo_cost'] !="0")?$itemrow['item_lifo_cost']:$itemrow['item_price'];
				}
				
				$sales_profitt_item_qty = ($itemrow['item_quantity'] !="" AND $itemrow['item_quantity'] !="0")?$itemrow['item_quantity']:1;
				
				$sales_profitt_item_amt = $sales_profitt_st_item_codt*$sales_profitt_item_qty;

				if($itemtabledetails->goods_sold_not_invoiced_account !="" AND $itemtabledetails->goods_sold_not_invoiced_account !="0"){
					$itemaccount_code = $itemtabledetails->goods_sold_not_invoiced_account;
				}elseif($itemcategoorytabledetails->goods_sold_not_invoiced_account !="" AND $itemcategoorytabledetails->goods_sold_not_invoiced_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->goods_sold_not_invoiced_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['goods_sold_not_invoiced_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
			 	$jvary['credit_amount'] = 0;
			 	$jvary['debit_amount'] = round($sales_profitt_item_amt,$rounddigits);
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;
				//inventory_asset_account
				$itemaccount_code = "";
				if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemtabledetails->inventory_asset_account;
				}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
				$jvary['account_code'] = $itemaccount_code;
				$jvary['credit_amount'] = round($sales_profitt_item_amt,$rounddigits);
			 	$jvary['debit_amount'] = 0;
				$itemref[$itemaccount_code][] = $jvary;
			}elseif($table_name == 'supplier_bills' OR $table_name == 'purchase_invoices' OR $table_name == 'sales_invoice_cancellations'){
				//goods_received_not_invoiced_account
				$itemaccount_code = "";
				if($itemtabledetails->goods_received_not_invoiced_account !="" AND $itemtabledetails->goods_received_not_invoiced_account !="0"){
					$itemaccount_code = $itemtabledetails->goods_received_not_invoiced_account;
				}elseif($itemcategoorytabledetails->goods_received_not_invoiced_account !="" AND $itemcategoorytabledetails->goods_received_not_invoiced_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->goods_received_not_invoiced_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['goods_received_not_invoiced_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
			 	$jvary['credit_amount'] = 0;
			 	$jvary['debit_amount'] = round($itemrow['item_amount'],$rounddigits);
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;
			}elseif($table_name == 'sales_invoices' OR $table_name == 'purchase_invoice_cancellations' OR $table_name == 'supplier_bill_cancellations'){
				//goods_received_not_invoiced_account
				$itemaccount_code = "";
				if($itemtabledetails->sales_revenue_account  !="" AND $itemtabledetails->sales_revenue_account  !="0"){
					$itemaccount_code = $itemtabledetails->sales_revenue_account ;
				}elseif($itemcategoorytabledetails->sales_revenue_account  !="" AND $itemcategoorytabledetails->sales_revenue_account  !="0"){
					$itemaccount_code = $itemcategoorytabledetails->sales_revenue_account ;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['sales_revenue_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
			 	$jvary['credit_amount'] = round($itemrow['item_amount'],$rounddigits);
			 	$jvary['debit_amount'] = 0;
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;




				////////////////////Sales Profit entry //////////////////////////////
				$itemaccount_code = "";
				if($itemtabledetails->default_costing_type == "W"){
					$sales_profitt_st_item_codt = ($itemrow['item_wa_cost'] !="" AND $itemrow['item_wa_cost'] !="0")?$itemrow['item_wa_cost']:$itemrow['item_price'];
				}elseif($itemtabledetails->default_costing_type == "F"){
					$sales_profitt_st_item_codt = ($itemrow['item_fifo_cost'] !="" AND $itemrow['item_fifo_cost'] !="0")?$itemrow['item_fifo_cost']:$itemrow['item_price'];
				}else{
					$sales_profitt_st_item_codt = ($itemrow['item_lifo_cost'] !="" AND $itemrow['item_lifo_cost'] !="0")?$itemrow['item_lifo_cost']:$itemrow['item_price'];
				}
				$sales_profitt_item_qty = ($itemrow['item_quantity'] !="" AND $itemrow['item_quantity'] !="0")?$itemrow['item_quantity']:1;
				//$sales_profitt_st_item_codt = ($itemrow['item_st_cost'] !="" AND $itemrow['item_st_cost'] !="0")?$itemrow['item_st_cost']:$itemrow['item_price'];
				$sales_profitt_item_amt = $sales_profitt_st_item_codt*$sales_profitt_item_qty;
				if($itemtabledetails->goods_sold_not_invoiced_account  !="" AND $itemtabledetails->goods_sold_not_invoiced_account  !="0"){
					$itemaccount_code = $itemtabledetails->goods_sold_not_invoiced_account ;
				}elseif($itemcategoorytabledetails->goods_sold_not_invoiced_account  !="" AND $itemcategoorytabledetails->goods_sold_not_invoiced_account  !="0"){
					$itemaccount_code = $itemcategoorytabledetails->goods_sold_not_invoiced_account ;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['goods_sold_not_invoiced_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
			 	$jvary['credit_amount'] = round($sales_profitt_item_amt,$rounddigits);
			 	$jvary['debit_amount'] = 0;
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;



				/// Reverser Profit entry
				$itemaccount_code = "";
				if($itemtabledetails->cogs_account  !="" AND $itemtabledetails->cogs_account  !="0"){
					$itemaccount_code = $itemtabledetails->cogs_account ;
				}elseif($itemcategoorytabledetails->cogs_account  !="" AND $itemcategoorytabledetails->cogs_account  !="0"){
					$itemaccount_code = $itemcategoorytabledetails->cogs_account ;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['cogs_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
			 	$jvary['credit_amount'] = 0;
			 	$jvary['debit_amount'] = round($sales_profitt_item_amt,$rounddigits);
			 	$jvary['account_code'] = $itemaccount_code;
				$itemref[$itemaccount_code][] = $jvary;

				//////////////////// Sales Profit entry //////////////////////////////
			}elseif($table_name == 'debit_notes'){


				$itemaccount_code = "";
				if($itemrow['account_code'] !="" AND $itemrow['account_code'] !="0"){
					$itemaccount_code = $itemrow['account_code'];
				}
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}
				if($row['ref_type'] == "purchase_invoices" || $row['ref_type'] == "supplier_bills" || $row['ref_type'] == "sales_invoice_cancellations"){
				 	$jvary['credit_amount'] = 0;
				 	$jvary['debit_amount'] =  round($itemrow['item_amount'],$rounddigits);
				 	$jvary['account_code'] = $itemaccount_code;
					$itemref[$itemaccount_code][] = $jvary;
					$party_credit[] = 'debit_notes';
				}elseif($row['ref_type'] == "sales_invoices" || $row['ref_type'] == 'purchase_invoice_cancellations' || $row['ref_type'] == 'supplier_bill_cancellations'){
				 	$jvary['credit_amount'] = round($itemrow['item_amount'],$rounddigits);
				 	$jvary['debit_amount'] = 0;
				 	$jvary['account_code'] = $itemaccount_code;
					$itemref[$itemaccount_code][] = $jvary;
					$party_debit[] = 'debit_notes';
				}
			}elseif($table_name == 'credit_notes'){
				$itemaccount_code = "";
				if($itemrow['account_code'] !="" AND $itemrow['account_code'] !="0"){
					$itemaccount_code = $itemrow['account_code'];
				}
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}
				if($row['ref_type'] == "purchase_invoices" || $row['ref_type'] == "supplier_bills" || $row['ref_type'] == "sales_invoice_cancellations"){
					$jvary['credit_amount'] = round($itemrow['item_amount'],$rounddigits);
				 	$jvary['debit_amount'] = 0;
				 	$jvary['account_code'] = $itemaccount_code;
					$itemref[$itemaccount_code][] = $jvary;
					$party_debit[] = 'debit_notes';
				}elseif($row['ref_type'] == "sales_invoices" || $row['ref_type'] == 'purchase_invoice_cancellations' || $row['ref_type'] == 'supplier_bill_cancellations'){
				 	$jvary['credit_amount'] = 0;
				 	$jvary['debit_amount'] =  round($itemrow['item_amount'],$rounddigits);
				 	$jvary['account_code'] = $itemaccount_code;
					$itemref[$itemaccount_code][] = $jvary;
					$party_credit[] = 'debit_notes';
				}
			}else{

			}
		}
		foreach($itemref as $refaccountno=>$list){
			$item_amount = 0;
			$creditamount = 0;
			$debitamount = 0;
			foreach($list as $detis){
				$creditamount += $detis['credit_amount'];
				$debitamount += $detis['debit_amount'];
			}
			$witemlistary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$row['transaction_id'], 'account_code'=>$refaccountno,'particulars'=>$particulars, 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by'],'credit_amount'=>$creditamount,'debit_amount'=>$debitamount);
			$witemlist[]  = $witemlistary;
			$total_debit_amt += $debitamount;
			$total_credit_amt += $creditamount;
		}

		//tax ammount -------------------------------
		if(!in_array($table_name, $notadded_tax)){
			foreach($taxitemres as $txtval){ 
				$tax_split_details = json_decode($txtval->tax_split_details, 1);

				foreach($tax_split_details as $tsd){
					if($tsd['account_code'] !="" AND $tsd['account_code'] !="0"){
						$gsttax_acount = $tsd['account_code'];
					}
					if($chartofaccount[$gsttax_acount] =="" || $gsttax_acount ==""){ // chart Of Account Not Found Suspens Account Enter data
						$gsttax_acount =$suspense_account;
					}
					$taxary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$txtval->transaction_item_id, 'particulars'=>$tsd['tax_name'], 'item_name'=>$tsd['tax_name'],  'description'=>$tsd['tax_name'],  'item_price'=>$tsd['tax_amount'],  'item_amount'=>$tsd['tax_amount'],  'total_amount'=>$tsd['tax_amount'],  'name_type'=>'C', 'is_active'=>'0',  'current_status'=>'0','created_by'=>$txtval->created_by,'account_code'=>$gsttax_acount);
					if(in_array($table_name, $tax_debit_item)){
						$taxary['credit_amount'] = 0;
				 		$taxary['debit_amount'] = round($tsd['tax_amount'],$rounddigits);
					}elseif(in_array($table_name, $tax_credit_item)){
						$taxary['credit_amount'] = round($tsd['tax_amount'],$rounddigits);
				 		$taxary['debit_amount'] = 0;
					}
					$taxitemref[$gsttax_acount][] = $taxary;
					$itemref[$gsttax_acount][] = $taxary;
				}
			}

			foreach($taxitemref as $refaccountno=>$list){
				$item_amount = 0;
				$creditamount = 0;
				$debitamount = 0;
				$taxparticulars  = "";
				foreach($list as $detis){
					$creditamount += $detis['credit_amount'];
					$debitamount += $detis['debit_amount'];
					$taxparticulars  = $detis['particulars'];
				}
				$witemlistary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$row['transaction_id'], 'account_code'=>$refaccountno,'particulars'=>$taxparticulars, 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by'],'credit_amount'=>$creditamount,'debit_amount'=>$debitamount);
				$witemlist[]  = $witemlistary;
				$total_debit_amt += $debitamount;
				$total_credit_amt += $creditamount;
			}
		}

		//End tax ammount -------------------------------
			
		//Discount ammount -------------------------------
		if(!in_array($table_name, $notadded_discount)){
			if($row['discount_amount'] !='0' AND $row['discount_amount'] !=''){
				
			if($table_name == 'sales_invoices' || $table_name == 'sales_invoice_cancellations'){
				if($config->default_sales_discount_account !="" AND $config->default_sales_discount_account !="0"){
					$default_purchase_discount_account = $config->default_sales_discount_account;
				}
				$default_purchase_discount_account!=""?$default_purchase_discount_account:$defaultaccount['default_sales_discount_account'];
			}else{
				if($config->default_purchase_discount_account !="" AND $config->default_purchase_discount_account !="0"){
					$default_purchase_discount_account = $config->default_purchase_discount_account;
				}
				$default_purchase_discount_account!=""?$default_purchase_discount_account:$defaultaccount['default_purchase_discount_account'];
			}
				if($chartofaccount[$default_purchase_discount_account] =="" || $default_purchase_discount_account ==""){ // chart Of Account Not Found Suspens Account Enter data
					$default_purchase_discount_account =$suspense_account;
				}
				$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>'', 'particulars'=>'Discount', 'item_name'=>'Discount',  'description'=>'Discount',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'req_date'=>$row['req_date'],  'approved_quantity'=>'',  'transaction_type'=>$row['transaction_type'],  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$row['created_by']);
				$jvary['account_code'] = $default_purchase_discount_account;
				

				$discount_ary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$default_purchase_discount_account,'particulars'=>'Discount', 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by']);
				if(in_array($table_name, $discount_credit_item)){ // item leave reverse
				 	$discount_ary['credit_amount'] = round($row['discount_amount'],$rounddigits); // item table entry
				 	$discount_ary['debit_amount'] = 0; // item table entry
				 	$jvary['credit_amount'] = round($row['discount_amount'],$rounddigits);
			 		$jvary['debit_amount'] = 0;
			 	
					$total_credit_amt += round($row['discount_amount'],$rounddigits);
				}elseif(in_array($table_name, $discount_debit_item)){   // item leave reverse
				 	$discount_ary['credit_amount'] = 0; // item table entry
				 	$discount_ary['debit_amount'] = round($row['discount_amount'],$rounddigits); // item table entry
				 	$jvary['credit_amount'] = 0;
			 		$jvary['debit_amount'] = round($row['discount_amount'],$rounddigits);
				 	$total_debit_amt += round($row['discount_amount'],$rounddigits);
				}	
				$itemref[$default_purchase_discount_account][] = $jvary; 
				$witemlist[]  = $discount_ary;
			}
		} 
		//End Discount ammount ------------------------------- 
		//additional_charges ammount -------------------------------
		if(!in_array($table_name, (array)$notadded_additionalcharge)){
			$additional_charges = json_decode($row['additional_charges'],1);
			$additional_charges_id = $additional_charges_list = $addchargwithacountandamt =  array();
			if(count((array)$additional_charges) !='0'){
				foreach($additional_charges as $achrg){
					$additional_charges_id[$achrg['charge_id']] = $achrg['charge_id'];
				}
			}
			if(count((array)$additional_charges) != 0){
				$additional_charges_ids = implode(',', (array)$additional_charges_id);
				echo $adichrgsql = "SELECT ac.id, ac.charge_name, ac.account_code, hsn.code, hsn.code_description, hsn.gst, hsn.tax_authority_id FROM additional_charges ac, hsn_and_sac_codes hsn WHERE ac.company_id = '?' AND ac.company_id = hsn.company_id AND ac.hsn_and_sac_code_id = hsn.id AND ac.id IN(?) AND ac.is_active = hsn.is_active AND ac.is_active = 0";
				$params = [$adichrgsql, $row, $additional_charges_ids];
				$adichrgres = $callConfig->getAllRowsNew($adichrgsql);
				foreach($adichrgres as $achrg){
					$additional_charges_list[$achrg->id] = $achrg;
				}
				//echo "<pre>"; print_r($additional_charges); //print_r($additional_charges_list);
				foreach($additional_charges as $valu){ //debit row
					$addchrgdetail = $additional_charges_list[$valu['charge_id']];
					if($addchrgdetail->account_code !="" AND $addchrgdetail->account_code !="0"){
						$additional_charges_acc =$addchrgdetail->account_code;
					}
					if($chartofaccount[$additional_charges_acc] =="" || $additional_charges_acc ==""){ // chart Of Account Not Found Suspens Account Enter data
						$additional_charges_acc =$suspense_account;
					}
					$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>'', 'particulars'=>'Additional Charges', 'item_name'=>'Additional Charges',  'description'=>'Additional Charges',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'req_date'=>$row['req_date'],  'approved_quantity'=>'',  'transaction_type'=>$row['transaction_type'],  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$row['created_by']);
					$jvary['account_code'] = $additional_charges_acc;
				 	

					$witemlistary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$row['id'], 'account_code'=>$additional_charges_acc,'particulars'=>$valu['charge_name'], 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by']);
					
					if(in_array($table_name, $additional_charge_credit)){ // item leave reverse
					 	$jvary['debit_amount'] = 0;
				 		$jvary['credit_amount'] = round($valu['shipping_amount'],$rounddigits);
				 		$witemlistary['debit_amount'] = 0;// item table entry
					 	$witemlistary['credit_amount'] =round($valu['shipping_amount'],$rounddigits);
				 		$total_credit_amt += round($valu['shipping_amount'],$rounddigits); 
					}elseif(in_array($table_name, $additional_charge_debit)){   // item leave reverse
						$witemlistary['debit_amount'] = round($valu['shipping_amount'],$rounddigits); // item table entry
					 	$witemlistary['credit_amount'] =0;
					 	$jvary['debit_amount'] = round($valu['shipping_amount'],$rounddigits);
				 		$jvary['credit_amount'] = 0;
				 		$total_debit_amt += round($valu['shipping_amount'],$rounddigits);
					}
					$itemref[$additional_charges_acc][] = $jvary;
					$witemlist[]  = $witemlistary;
				}
			} // check count array additional_charges
		}
		//End additional Charges ammount -------------------------------
		//Start Party Accounts -------------------------------
		if(!in_array($table_name, $notadded_party)){


			//tds added account
			if($row['total_tds_amount'] !='0' AND $row['total_tds_amount'] !=''){
				if($config->tds_clearing_account !="" AND $config->tds_clearing_account !="0"){
					$tds_clearing_account =$config->tds_clearing_account;
				}
				$tds_clearing_account!=""?$tds_clearing_account:$defaultaccount['tds_clearing_account'];
				if($chartofaccount[$tds_clearing_account] =="" || $tds_clearing_account ==""){ // chart Of Account Not Found Suspens Account Enter data
					$tds_clearing_account =$suspense_account;
				}
				$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>'', 'particulars'=>'TDS', 'item_name'=>'TDS',  'description'=>'TDS',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>$row['total_tds_amount'],  'item_amount'=>$row['total_tds_amount'],  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>$row['total_tds_amount'],  'name_type'=>'C',  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'req_date'=>$row['req_date'],  'approved_quantity'=>'',  'transaction_type'=>$row['transaction_type'],  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$row['created_by']);
				$jvary['account_code'] = $tds_clearing_account;


				$witemlistary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$tds_clearing_account,'particulars'=>'TDS', 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],'party_id'=>$row['party_id'],'created_by'=>$row['created_by']); // credit row
				if(in_array($table_name, $tds_debit)){
					$jvary['credit_amount'] = 0;
					$jvary['debit_amount'] =round($row['total_tds_amount'],$rounddigits);
					$witemlistary['credit_amount'] =  0;// item table entry
				 	$witemlistary['debit_amount'] = round($row['total_tds_amount'],$rounddigits);
				 	$total_debit_amt += round($row['total_tds_amount'],$rounddigits);
				}elseif(in_array($table_name, $tds_credit)){
					$jvary['credit_amount'] = round($row['total_tds_amount'],$rounddigits);
					$jvary['debit_amount'] =0;
					$witemlistary['credit_amount'] = round($row['total_tds_amount'],$rounddigits);
			 		$witemlistary['debit_amount'] = 0;
					$total_credit_amt += round($row['total_tds_amount'],$rounddigits);
				}
				$witemlist[]  = $witemlistary;
				$itemref[$tds_clearing_account][] = $jvary;
				$row['total_amount'] = round($row['total_amount'],$rounddigits)+round($row['total_tds_amount'],$rounddigits);
			} 
			//End tds added account

			if($party_account_number !="" AND $party_account_number !="0"){
				$party_accountt =$party_account_number;
			}
			if($chartofaccount[$party_accountt] =="" || $party_accountt ==""){ // chart Of Account Not Found Suspens Account Enter data
				$party_accountt =$suspense_account;
			}
			$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>'', 'particulars'=>'Party Account', 'item_name'=>'Party Account',  'description'=>'Party Account',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'req_date'=>$row['req_date'],  'approved_quantity'=>'',  'transaction_type'=>$row['transaction_type'],  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$row['created_by']);
			$jvary['account_code'] = $party_accountt;


			$witemlistary =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$party_accountt,'particulars'=>'Party Account', 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by']); // credit row
			if(in_array($table_name, (array)$party_debit)){
				$jvary['credit_amount'] = 0;
				$jvary['debit_amount'] =round($row['total_amount'],$rounddigits);
				$witemlistary['credit_amount'] =  0;// item table entry
			 	$witemlistary['debit_amount'] = round($row['total_amount'],$rounddigits);
			 	$total_debit_amt += round($row['total_amount'],$rounddigits);
			}elseif(in_array($table_name, $party_credit)){
				$jvary['credit_amount'] = round($row['total_amount'],$rounddigits);
					$jvary['debit_amount'] =0;
				$witemlistary['credit_amount'] = round($row['total_amount'],$rounddigits);
		 		$witemlistary['debit_amount'] = 0;
		 		$total_credit_amt +=round($row['total_amount'],$rounddigits);
			}
			$witemlist[]  = $witemlistary;
			$itemref[$party_accountt][] = $jvary;
		}	
		//Start Suspense Account One Entry Added
		// $tot_debit_amount = $tot_credit_amount = 0;
		// foreach($witemlist as $itelm){
		// 	$tot_debit_amount += $itelm['debit_amount']!=""?$itelm['debit_amount']:0;
		// 	$tot_credit_amount += $itelm['credit_amount']!=""?$itelm['credit_amount']:0;
		// }
		// if($default_round_off_account !="" AND $default_round_off_account !="0"){
		// 	$round_off_account =$default_round_off_account;
		// }
		// if($chartofaccount[$round_off_account] =="" || $round_off_account ==""){ // chart Of Account Not Found Suspens Account Enter data
		// 	$round_off_account =$suspense_account;
		// }
		$round_off_account =$suspense_account;
		$tot_debit_amount = $total_debit_amt;
		$tot_credit_amount = $total_credit_amt;
		$suspensearry =array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$round_off_account,'particulars'=>'Round Off Account', 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by']); // credit row

		$jvary = array('company_id'=>$row['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$row['transaction_id'],  'transaction_item_id'=>$transaction_item_id['code'],  'item_id'=>'', 'particulars'=>'Round Off Account', 'item_name'=>'Round Off Account',  'description'=>'Round Off Account',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'req_date'=>$row['req_date'],  'approved_quantity'=>'',  'transaction_type'=>$row['transaction_type'],  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$row['created_by'],'account_code'=>$round_off_account);

		if(round($tot_debit_amount,2) > round($tot_credit_amount,2)){
		//if($tot_debit_amount > $tot_credit_amount){	
			$suspencamount = round($tot_debit_amount,2) - round($tot_credit_amount,2);
			//$suspencamount = $tot_debit_amount - $tot_credit_amount;
			$total_credit_amt +=round($suspencamount,$rounddigits);
			$suspensearry['credit_amount'] = $suspencamount;
			$jvary['credit_amount'] = $suspencamount;
			$suspensearry['debit_amount'] = 0;
			$jvary['debit_amount'] = 0;
			$itemref[$round_off_account][] = $jvary;	
			$witemlist[] =$suspensearry;
		}elseif(round($tot_debit_amount,2) < round($tot_credit_amount,2)){
		//}elseif($tot_debit_amount < $tot_credit_amount){
			$suspencamount = round($tot_credit_amount,2) - round($tot_debit_amount,2);
			//$suspencamount = $tot_credit_amount - $tot_debit_amount;
			$total_debit_amt += round($suspencamount,$rounddigits);
			$suspensearry['debit_amount'] = $suspencamount;
			$jvary['debit_amount'] = $suspencamount;
			$suspensearry['credit_amount'] = 0;
			$jvary['credit_amount'] = 0;
			$itemref[$round_off_account][] = $jvary;	
			$witemlist[] =$suspensearry;
		} 

		//End Suspense Account One Entry Added
		$senddata = array('company_id'=>$row['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=> $cost_center_id,  'transaction_number'=>'',  'transaction_date'=>$row['transaction_date'],  'currency_id'=>$row['currency_id'],  'currency_rate'=>$row['currency_rate'],  'originator'=>$row['originator'],  'party_id'=>$row['party_id'],  'party_name'=>$row['party_name'],  'party_email'=>$row['party_email'],  'party_tax_number'=>$row['party_tax_number'],  'party_phone'=>$row['party_phone'],  'party_address'=>$row['party_address'],  'party_ship_adderss'=>$row['party_ship_adderss'],  'place_of_supply'=>$row['place_of_supply'],  'payment_terms'=>$row['payment_terms'],  'notes'=>$row['notes'],  'printing_notes'=>$row['printing_notes'],  'parent_id'=>$row['parent_id'],'amount'=>'',  'tax_amount'=>'',  'type'=>$row['type'],  'tax_type'=>$row['tax_type'],  'discount_type'=>$row['discount_type'],  'discount_value'=>$row['discount_value'],  'discount_amount'=>$row['discount_amount'],  'total_amount'=>$row['total_amount'], 'shipping_tax'=>$row['shipping_tax'],  'shipping_amount'=>$row['shipping_amount'],  'shipping_tax_amount'=>$row['shipping_tax_amount'],  'tax_state'=>$row['tax_state'],  'opex_cpex'=>$row['opex_cpex'],  'doc_ref_no'=>$row['doc_ref_no'],  'doc_ref_date'=>$row['doc_ref_date'],  'due_date'=>$row['due_date'],  'ref_type'=>$table_name,  'ref_id'=>$ref_id,  'ref_no'=>$row['transaction_number'],  'ref_date'=>$row['transaction_date'],   'is_active'=>'0',  'status_date'=>$row['status_date'],  'from_date'=>$row['from_date'],  'to_date'=>$row['to_date'],  'created_date'=>$row['created_date'],  'created_by'=>$row['created_by'],'credit_amount'=>$total_credit_amt, 'debit_amount'=>$total_debit_amt,'debit_tax'=>0,'credit_tax'=>'0','current_status'=>ST_APPROVED); // master table entry



		//echo "<pre>";print_r($witemlist); 
		$postdata = array('main'=>$senddata,'items'=>$witemlist,'itemref'=>$itemref,'update_id'=>'','ref_id'=>$ref_id,'company_id'=>$row['company_id']);
		//echo "<pre>"; print_r($postdata); exit;
		//echo "working 11 ";
		$addjournalvouchers = $this->addjournalvouchers($postdata, $table_name);
		//echo "<pre>";print_r($addjournalvouchers);
		//echo "working 132 ";
		return $addjournalvouchers; 
	} 
	function addjournalvouchers($post, $table, $type='',$mtable = ''){
		global $callConfig;
		$return = array();
		if($mtable ==""){
			$mtable = 'journal_vouchers';
		}
		//$mtable = 'journal_vouchers';
		$fieldarray  = $this->toGetInvoicetables();
		$return['post']= $post;
		$chartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ? AND is_active =0";
		$params = [$chartofaccountssql, $post];
        $chartofaccountsres = $callConfig->getAllRowsNew($chartofaccountssql);
        $chart_of_accounts = array();
        foreach($chartofaccountsres as $cca){
        	$chart_of_accounts[$cca->account_code] = $cca;
        }
		//deleted jv all updated items 
		if($post['ref_id'] !=""){
			$oldjvsql = "SELECT id, transaction_number FROM ? WHERE  is_active = 0 AND ref_type = '?' AND ref_id = ".$post['ref_id'];
			$params = [$oldjvsql, $mtable, $table, $post];
			$oldjvres = $callConfig->getRowNew($oldjvsql);
			$return['oldjvres'] = $oldjvsql;
		}
		if(isset($oldjvres->id)){
			$return['delete'] = "Y";
			$oldjvid = $oldjvres->id; 
			$transation = $oldjvres->transaction_number; 
			// $return[] = $callConfig->executeQuery("DELETE FROM journal_vouchers WHERE id = '".$oldjvid."'");
			// $return[] =$callConfig->executeQuery("DELETE FROM jv_items WHERE transaction_id = '".$oldjvid."'");
			// $return[] =$callConfig->executeQuery("DELETE FROM general_ledger WHERE voucher_id = '".$oldjvid."'");
			// $return[] = $callConfig->executeQuery("DELETE FROM jv_items_references WHERE transaction_id = '".$oldjvid."'");
			$return[] = $callConfig->executeQuery("DELETE FROM ? WHERE id = '?'");
			$params = [$return, $callConfig->executeQuery, $mtable, $oldjvid];
			$return[] =$callConfig->executeQuery("DELETE FROM ? WHERE transaction_id = '?'");
			$params = [$return, $callConfig->executeQuery, $fieldarray, $mtable, $oldjvid];
			$return[] =$callConfig->executeQuery("DELETE FROM general_ledger WHERE voucher_id = '?'");
			$params = [$return, $callConfig->executeQuery, $oldjvid];
			$return[] = $callConfig->executeQuery("DELETE FROM ?_references WHERE transaction_id = '?'");
			$params = [$return, $callConfig->executeQuery, $fieldarray, $mtable, $oldjvid];
		}else{
			$return['delete'] = "N";
		}
		////deleted jv all updated items 
		$return['delete'] = $type;	
		if($type == 'update'){
			
		}else{
			$transation = $transation !=""?$transation:$this->toGetApplicationSerialNumber($mtable);
			$return['transation'] = $transation;
			//$transation = $this->toGetApplicationSerialNumber($mtable);
			$fieldnames = $post['main'];
			$fieldnames['transaction_number'] = $transation;
			$insert_data=$callConfig->insertRecord($mtable,$fieldnames);
			$return['insert_data'] = $insert_data;
			$insert_id = $insert_data['LASTID'];
		}
		$mitems = $post['items']; 
		$refitmes = $post['itemref']; 
		if($type == 'update'){
			
		}else{
			foreach($mitems as $mitem){				
				$mitem['transaction_id'] = $insert_id;
				$account_type = $chart_of_accounts[$mitem['account_code']]->account_type;
				$account_name = $chart_of_accounts[$mitem['account_code']]->account_name;
				$mitem['account_type'] = $account_type;
				$mitem['account_name'] = $account_name;
				$mitem_res=$callConfig->insertRecord($fieldarray[$mtable]['item'],$mitem);
				$return['mitem_res'] = $mitem_res;
				$transaction_item_id = $mitem_res['LASTID'];
				
				
				$genralledge = array('company_id' => $mitem['company_id'], 'account_code' => $mitem['account_code'], 'voucher_id' => $insert_id, 'voucher_ref_no' => $transation, 'voucher_date' => $fieldnames['transaction_date'], 'cost_center_id' => $mitem['cost_center_id'], 'location_id' => $mitem['transaction_location_id'], 'department_id' => $fieldnames['department_id'], 'debit_amount' => $mitem['debit_amount'], 'credit_amount' => $mitem['credit_amount'], 'transaction_id' =>$insert_id, 'transaction_ref_no' => $transation, 'transaction_date' =>  $fieldnames['transaction_date'], 'is_active' => '0', 'current_status' => '0', 'created_by' => $mitem['created_by'],'account_type'=>$account_type,'account_name'=>$account_name);
				$genledgitem_id=$callConfig->insertRecord('general_ledger',$genralledge);
				$return['genledgitem_id'] = $genledgitem_id;

				
				$balc_debit_amoun = $mitem['debit_amount'] !=""?$mitem['debit_amount']:0;
				$balc_credit_amountn = $mitem['credit_amount'] !=""?$mitem['credit_amount']:0;
				$balanceary = array('company_id'=>$mitem['company_id'],'account_code'=>$mitem['account_code'],'date'=>$this->dateconvertdbformat($fieldnames['transaction_date']),'debit_amount'=>$balc_debit_amoun,'credit_amount'=>$balc_credit_amountn,'account_type'=>$account_type,'location_id'=>$mitem['transaction_location_id'],'department_id'=>$fieldnames['department_id'],'cost_center_id'=>$mitem['cost_center_id']);
	            $return['balanceenty_ary'][] = $balanceary;

	            $accountObj=new accountUserClass();
	            $updatebalances = $accountObj->insertAccountBalances($balanceary);
	            //if($mitem['debit_amount']>0){

				foreach($refitmes[$mitem['account_code']] as $item){  // refrance all entries one account code multi items
					$item['transaction_id'] = $insert_id;
					$item['transaction_item_id'] = $transaction_item_id;
					$refftable = $fieldarray[$mtable]['item']."_references";
					$params = [$refftable, $fieldarray, $mtable];
					$item_id=$callConfig->insertRecord($refftable,$item);
				}
				//}
			}
		} 
		return $return;		
	} 
	
	function addjournalvouchersold($post, $table, $type=''){
		global $callConfig;
		//echo "hah";
		echo "<pre>"; print_r($post); exit;
		$mtable = 'journal_vouchers';
		$fieldarray  = $this->toGetInvoicetables();
		if($post['old_place'] == $post['place_of_supply']){ 
			$tax_flag = "S";
		}else{
			$tax_flag = "I";
		}
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' && $status > ST_DRAFT){
			$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value'],'company_id'=>$post['company_id']);
			$status = $this->ToCheckStatusApprovalLog($sarray);
		}
		$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
		$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
		$diff_amount = 0; $diff_credit = 0; $diff_debit=0;
		if($post['grandtotal_credit'] != $post['grandtotal_debit']){
			if($post['grandtotal_credit'] > $post['grandtotal_debit']){  

				$diff_amount = $post['grandtotal_credit'] - $post['grandtotal_debit'];
				$post['grandtotal_debit'] = $post['grandtotal_credit'];
				$diff_debit = $diff_amount;
			}
			if($post['grandtotal_credit'] < $post['grandtotal_debit']){  

				$diff_amount = $post['grandtotal_debit'] - $post['grandtotal_credit'];
				$post['grandtotal_credit'] = $post['grandtotal_debit'];
       			//echo  $post['grandtotal_debit']."###".$post['grandtotal_credit']."&&&&".$diff_amount;
				$diff_credit = $diff_amount;
			}
		}
      

		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subtotal_debit'],'tax_amount'=>$post['grandtotal_debit']-$post['subtotal_debit'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal_debit'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'due_date'=>$due_date,'debit_amount'=>$post['subtotal_debit'], 'credit_amount'=>$post['subtotal_credit'],'credit_tax'=>$post['grandtotal_credit'] - $post['subtotal_credit'],'debit_tax'=>$post['grandtotal_debit']-$post['subtotal_debit']);
		$fieldnames['cost_center_id'] = $post['cost_center'];	
		//echo "<pre>"; print_r($fieldnames); echo "</pre>"; exit;
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			$insert_data=$callConfig->updateRecord($mtable,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			//$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'update');
		}else{
			$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			//echo "<pre>";print_r($fieldnames); exit;

			$transation = $this->toGetApplicationSerialNumber($mtable);

			$fieldnames['transaction_number'] = $transation;
		 	//print_r($fieldnames); //exit;
			$insert_data=$callConfig->insertRecord($mtable,$fieldnames);

			$insert_id = $insert_data['LASTID'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			//$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'insert');
		}


		//print_r($post['file_add']); echo $insert_id; exit;
		if(count((array)$post['file_add'])>0){
			$file_upload = $this->multiplefileupload($post['file_add']);
		}//exit;
		$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>3,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);
		if($insert_id > 0){
			//echo "data"; exit;
			foreach ($post['accountcode'] as $key => $item) {
			///echo $item; exit;
				if($post['debit'][$key] !=0 ){
					$item_price = $post['debit'][$key];
				}else{
					$item_price = $post['credit'][$key];
				}
				$litemids[] = $item; 
				$names = explode('#',$post['name'][$key] );
				$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['account_name'][$key],'description'=>$post['description'][$key],'purchase_uom'=>'','item_quantity'=>'1','item_price'=>$item_price,'item_amount'=>$item_price,'tax_amount'=>$post['taxes_amount'][$key],'total_amount'=>$item_price,'account_code'=>$post['account_code'][$key],'debit_amount'=>$post['debit'][$key],'credit_amount'=>$post['credit'][$key],'location'=>$post['location'][$key],'name_id'=>$names['0'],'name_type'=>$names['1']);

				$genralledger[] = array('company_id'=>$post['company_id'],'account_code'=>$post['account_code'][$key],'account_type'=>$post['account_type'][$key],'voucher_type'=>$post['description'][$key],'voucher_id'=>$insert_id,'voucher_date'=>$invoiced_at,'cost_center_id'=>$post['cost_center'],'location_id'=>$post['location'][$key],'department_id'=>$post['department_id'],'debit_amount'=>$post['debit'][$key],'credit_amount'=>$post['credit'][$key],'balance_amount'=>'0','transaction_type'=>$fieldnames['ref_type'],'transaction_id'=>$fieldnames['ref_id'],'transaction_ref_no'=>$fieldnames['ref_no'],'transaction_date'=>$fieldnames['ref_date']);
		 		//echo "<pre>"; print_r($genralledger); exit;
				$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['taxes'][$key],'amount'=>$item_price,'tax_amount'=>$post['taxes_amount'][$key],'total_amount' =>$item_price);
		 		//echo "<pre>"; print_r($itemfieldnames);print_r($taxfield); exit;
				if($type == 'update'){
					$where = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
					$params = [$where, $insert_id, $item];
					$itemquery=$callConfig->selectQuery($fieldarray[$mtable]['item'],'*',$where,'','','');
					$data = $callConfig->getRowNew($itemquery);
				
					$itemfieldnames['approved_quantity'] = $post['quantity'][$key];
					$itemfieldnames['pending_qty'] = $post['quantity'][$key];
									if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] || $data->approved_quantity != $itemfieldnames['approved_quantity'] ){
						if($data->id !=''){
							$res=$callConfig->updateRecord($fieldarray[$mtable]['item'],$itemfieldnames,'id',$data->id);

						}else{
							$item_id=$callConfig->insertRecord($fieldarray[$mtable]['item'],$itemfieldnames);
						}
					}
					$where = "transaction_id = '?' AND transaction_item_id	= '?' AND is_active = 0";
					$params = [$where, $insert_id, $item];
					$itemquery=$callConfig->selectQuery($fieldarray[$mtable]['tax'],'*',$where,'','','');
					$data_tax = $callConfig->getRowNew($itemquery);
					if(count((array)$data_tax) > 0){
						if($data->tax_amount != $itemfieldnames['tax_amount']){
							$where = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
							$params = [$where, $insert_id, $item];
							$res=$callConfig->updateRecord($fieldarray[$mtable]['tax'],$taxfield,'','',$where);
						}
					}else{
						$itax_id=$callConfig->insertRecord($fieldarray[$mtable]['tax'],$taxfield);
					}
					
				}else{
					$item_id=$callConfig->insertRecord($fieldarray[$mtable]['item'],$itemfieldnames);
					
					if($post['taxes'][$key]!=0){
						$itax_id=$callConfig->insertRecord($fieldarray[$mtable]['tax'],$taxfield);
					}
				}
			}
			if($type == 'update' ){
				$diffid = array_diff($itemids,$litemids);
				if(count((array)$diffid) > 0){
					foreach ($diffid as  $value_id) {
						$taxfieldde=array('is_active'=>1);
						$wheredd = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
						$params = [$wheredd, $insert_id, $value_id];
						$wheredd1 = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
						$params = [$wheredd1, $insert_id, $value_id];
						$res=$callConfig->updateRecord($fieldarray[$mtable]['item'],$taxfieldde,'','',$wheredd);
						$res=$callConfig->updateRecord($fieldarray[$mtable]['tax'],$taxfieldde,'','',$wheredd1);
					}
				}
			}
		}
		//if($diff_amount !=0){
		//	$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$post['diff_acount'],'item_name'=>'','description'=>'','purchase_uom'=>'','item_quantity'=>'1','item_price'=>$diff_amount,'item_amount'=>$diff_amount,'tax_amount'=>0,'total_amount'=>$diff_amount,'account_code'=>$post['diif_account_code'],'debit_amount'=>$diff_debit,'credit_amount'=>$diff_credit,'location'=>$post['location'][$key],'name_id'=>$names['0'],'name_type'=>$names['1']);
		//	$item_id=$callConfig->insertRecord($fieldarray[$table]['item'],$itemfieldnames);
		//}
		//if($status > ST_DRAFT){
		 		//echo "data"; exit;
		// 		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		// }
		
		//$ledger = $this->toinsertgenralledger();
		 return $insert_data;
	}
	function insertorupdateitem_ledgerst($post){
			global $callConfig;
		//echo "<pre>"; print_r($post);
			$whr="location_id='?' AND item_id = '?' AND document_type='?' AND document_no='?'";
			$params = [$whr, $post, $post, $post, $post];
			$query1=$callConfig->selectQuery('item_ledger_st','*',$whr,'','','');
		//echo $query1;
			$row = $callConfig->getRowNew($query1);
		//print_r($row); //exit;
			if($row->id !=''){
		   // echo "ravi for"; //exit;
				$insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'],'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])),'is_active'=>0);
				$insert_data=$callConfig->updateRecord('item_ledger_st',$insert,'id',$row->id); 
		}else{ //exit;
			if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		  // echo "ravi2"; //exit; 
				$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['item_st_cost'],'total_cost'=> $post['item_st_cost']*($post['in_qty']+$post['out_qty']),'out_qty' => $post['out_qty'], 'out_value' => $post['out_value'],'is_active'=>0);
				$res=$callConfig->insertRecord('item_ledger_st',$insert);
			}
		}
		return $res;
	}
	function insertorupdateitem_ledgerwa($post){
		global $callConfig;
		if($post['location_id'] !="" AND  $post['item_id'] !="" AND $post['document_type']!="" AND $post['document_no'] !=""){
			$whr="location_id='?' AND item_id = '?' AND document_type='?' AND document_no='?' AND is_active=0";
			$params = [$whr, $post, $post, $post, $post];
			$query1=$callConfig->selectQuery('item_ledger_wa','*',$whr,'','','');
			//echo $query1;
			$row = $callConfig->getRowNew($query1);
		}
		//print_r($row); //exit;
		if($row->id !=''){
		    //echo "ravi for"; //exit;
			$insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'] ,'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])),'is_active'=>'0','purchase_uom' => $post['purchase_uom'],'purchase_qty' => $post['purchase_qty'],'purchase_base_uom' => $post['purchase_base_uom'],'sales_uom' => $post['sales_uom'],'sales_qty' => $post['sales_qty'],'sale_base_uom' => $post['sale_base_uom'],'is_active'=>0);
			$insert_data=$callConfig->updateRecord('item_ledger_wa',$insert,'id',$row->id); 
		}else{ //exit;
			if($post['out_qty']!=0 || $post['in_qty'] !=0){
    		   //echo "ravi2"; exit; 
				$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'], 'item_cost' => $post['item_wa_cost'],'total_cost'=> $post['item_wa_cost']*($post['in_qty']+$post['out_qty']),'out_qty' => $post['out_qty'], 'out_value' => $post['out_value'],'is_active'=>'0','purchase_uom' => $post['purchase_uom'],'purchase_qty' => $post['purchase_qty'],'purchase_base_uom' => $post['purchase_base_uom'],'sales_uom' => $post['sales_uom'],'sales_qty' => $post['sales_qty'],'sale_base_uom' => $post['sale_base_uom'],'is_active'=>0);
				$res=$callConfig->insertRecord('item_ledger_wa',$insert);
			}
		}
		return $res;
	}
	function itemledgerlifochange($post){
		global $callConfig;
		$whr= "item_id = '?' AND document_type='?' AND document_no='?'";
		$params = [$whr, $post, $post, $post];
		$query1=$callConfig->selectQuery('item_ledger_lifo','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRowNew($query1);
		if($row->id !=''){
			if($post['out_qty']!=0 ){
				$this->itemlifoupdates($post);
			}else{
				$whr21="item_id = '?' AND document_type='?' AND document_no='?'";
				$params = [$whr21, $post, $post, $post];
				$query21=$callConfig->selectQuery('item_lifo_balances','*',$whr21,'','',''); 
				$row21 = $callConfig->getRowNew($query21);
				$updatequery =  "UPDATE item_lifo_balances SET in_qty = ?,closing_qty= ?, in_value = ? WHERE id = '?'";
				$params = [$updatequery, $row->in_qty, $post, $row->closing_qty, $post, $row->in_value, $post, $row->id];
				$callConfig->executeQuery($updatequery);
			}
		}else{
			if($post['out_qty']!=0 || $post['in_qty'] !=0){
				if($post['out_qty']!=0 ){
					$this->itemlifoupdateledge($post);
				}else{
					$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty'],'is_active'=>0);
					$res=$callConfig->insertRecord('item_lifo_balances',$insert);

				}
			}
		}
		
	}
	function insertorupdateitem_ledgerlifo($post){
		global $callConfig;
		if($post['location_id'] !="" AND $post['item_id'] !="" AND $post['document_type'] !="" AND $post['document_no']!=""){
			$whr="location_id='?' AND item_id = '?' AND document_type='?' AND document_no='?'";
			$params = [$whr, $post, $post, $post, $post];
			$query1=$callConfig->selectQuery('loc_item_ledger_lifo','*',$whr,'','','');
			//echo $query1;
			$row = $callConfig->getRowNew($query1);
		}
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
			$insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('loc_item_ledger_lifo',$insert,'id',$row->id);
			if($post['out_qty']!=0 ){
				$this->itemlifoupdateledge($post);
			}else{
				$whr21="item_id = '?' AND document_type='?' AND document_no='?'";
				$params = [$whr21, $post, $post, $post];
				$query21=$callConfig->selectQuery('item_lifo_balances','*',$whr21,'','',''); 
				$row21 = $callConfig->getRowNew($query21);
				$updatequery =  "UPDATE loc_item_lifo_balances SET in_qty = ?,closing_qty= ?, in_value = ? WHERE id = '?'";
				$params = [$updatequery, $row->in_qty, $post, $row->closing_qty, $post, $row->in_value, $post, $row->id];
				$callConfig->executeQuery($updatequery);
			}
		}else{ //exit;
			if($post['out_qty']!=0 || $post['in_qty'] !=0){
				if($post['out_qty']!=0 ){
					$this->itemlifoupdateledge($post);
				}else{
					$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['in_value']/$post['in_qty'],'total_cost'=> $post['in_value'],'out_qty' => $post['out_qty'], 'out_value' => $post['out_value'],'is_active'=>0);
					$res=$callConfig->insertRecord('item_ledger_lifo',$insert);
					$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty'],'is_active'=>0);
					$res=$callConfig->insertRecord('item_lifo_balances',$insert);
					$insert['location_id'] = $post['location_id'];
					$insert['ware_house_id'] = $post['transaction_warehouse_id'];
					$res=$callConfig->insertRecord('loc_item_lifo_balances',$insert);
				}
			}
		}
		return $res;
	}
	function  itemlifoupdateledge($post){
		global $callConfig;
		$count = $post['out_qty'];
		$itemcount = $post['out_qty'];
		if($count > 0){
			$locquery = "SELECT * FROM loc_item_lifo_balances WHERE item_id =? AND company_id=? AND location_id =? AND closing_qty >0 ORDER By balance_date DESC,id DESC";   
			$params = [$locquery, $post, $post, $post];
			$query = "SELECT * FROM item_lifo_balances WHERE item_id =? AND company_id=? AND closing_qty >0 ORDER By balance_date DESC,id DESC";   
			$params = [$query, $post, $post];
		}else{
			$locquery = "SELECT * FROM loc_item_lifo_balances WHERE item_id =? AND location_id =? AND company_id=? AND AND (closing_qty = 0 OR closing_qty!=in_qty) ORDER By balance_date,id DESC";
			$params = [$locquery, $post, $post, $post];
			$query = "SELECT * FROM item_lifo_balances WHERE item_id =? AND company_id=? AND  (closing_qty = 0 OR closing_qty!=in_qty) ORDER By balance_date DESC,id DESC";
			$params = [$query, $post, $post];
		}
	   // echo "<br>"; echo $query;
	   // echo "<br>"; echo $locquery;
		$rows = $callConfig->getAllRowsNew($locquery);
		$itemrows = $callConfig->getAllRowsNew($query);
	    //echo "<pre>"; print_r($rows);
	    //echo "<pre>"; print_r($itemrows);
		foreach($itemrows as $itemdata){
			if($itemcount > 0){        
				if($itemdata->closing_qty < $itemcount){
					$itemremaning_count = $itemcount- $itemdata->closing_qty;
					$count = $itemdata->closing_qty;
				}else{
					$itemremaning_count =0;
				}
			}else{
				if($itemdata->closing_qty < -(-$itemcount)){
					$remaning_count = $itemcount+ $itemdata->closing_qty;
					$itemcount = "-".$itemdata->closing_qty;
				}else{
					$itemremaning_count =0;
				}
			}
			$cc = $itemdata->out_qty+$itemcount;
			$ccc = $itemdata->closing_qty-$itemcount;
			$updatequery121 =  "UPDATE item_lifo_balances SET out_qty = ?,closing_qty= ? WHERE id = '?'";
			$params = [$updatequery121, $cc, $ccc, $itemdata->id];
	      // echo $updatequery121;
	       $callConfig->executeQuery($updatequery121);//exit
	       if($itemremaning_count == 0){ 
	       	break;
	       }
	       $itemcount = $itemremaning_count;
	   }
	   foreach($rows as $row){
	        //echo $count;
	   	if($count > 0){        
	   		if($row->closing_qty < $count){
	   			$remaning_count = $count- $row->closing_qty;
	   			$count = $row->closing_qty;
	   		}else{
	   			$remaning_count =0;
	   		}
	   	}else{
	   		if($row->closing_qty < -(-$count)){
	   			$remaning_count = $count+ $row->closing_qty;
	   			$count = "-".$row->closing_qty;
	   		}else{
	   			$remaning_count =0;
	   		}
	   	}
	      // echo "<pre>"; print_r($post);
	   	$cc = $row->out_qty+$count;
	   	$ccc = $row->closing_qty-$count;
	   	$updatequery =  "UPDATE loc_item_lifo_balances SET out_qty = ?,closing_qty= ? WHERE id = '?'";
	   	$params = [$updatequery, $cc, $ccc, $row->id];
	      // echo "<br>"; echo $updatequery;
	   	$callConfig->executeQuery($updatequery);
	   	$bdate = $post['transaction_date'];
	   	$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $row->day_rate,'total_cost'=>$row->day_rate*$count ,'out_qty' => $count, 'out_value' => ($post['out_value']/$post['out_qty'])*$count,'is_active'=>0);
	   	$res=$callConfig->insertRecord('item_ledger_lifo',$insert);
	   	if($remaning_count == 0){ 
	   		break;
	   	}
	   	$count = $remaning_count;
	    }//exit;
	    return $res;
	}
	function insertorupdateitem_ledgerfifo($post){
		global $callConfig;
		//echo "<pre>"; print_r($post);
		$whr="location_id='?' AND item_id = '?' AND document_type='?' AND document_no='?'";
		$params = [$whr, $post, $post, $post, $post];
		$query1=$callConfig->selectQuery('loc_item_ledger_fifo','*',$whr,'','','');
		//echo $query1;
		$row = $callConfig->getRowNew($query1);
		//print_r($row); //exit;
		if($row->id !=''){
		   // echo "ravi for"; //exit;
			$insert = array('document_date' =>$post['transaction_date'],'in_qty' =>$row->in_qty+$post['in_qty'] ,'in_value' =>$row->in_value+$post['in_value'], 'out_qty' => $row->out_qty+$post['out_qty'], 'out_value' => $row->out_value+$post['out_value'],'total_cost'=> $row->item_cost *( ($row->in_qty+$post['in_qty']) + ($row->out_qty+$post['out_qty'])));
			$insert_data=$callConfig->updateRecord('item_ledger_fifo',$insert,'id',$row->id);
			if($post['out_qty']!=0 ){
				$this->itemlifoupdateledge($post);
			}else{
				$whr21="item_id = '?' AND document_type='?' AND document_no='?'";
				$params = [$whr21, $post, $post, $post];
				$query21=$callConfig->selectQuery('item_fifo_balances','*',$whr21,'','',''); 
				$row21 = $callConfig->getRowNew($query21);
				$updatequery =  "UPDATE item_fifo_balances SET in_qty = ?,closing_qty= ?, in_value = ? WHERE id = '?'";
				$params = [$updatequery, $row->in_qty, $post, $row->closing_qty, $post, $row->in_value, $post, $row->id];
				$callConfig->executeQuery($updatequery);
			}
		}else{ //exit;
			if($post['out_qty']!=0 || $post['in_qty'] !=0){
				if($post['out_qty']!=0 ){
					$this->itemfifoupdateledge($post);
				}else{
					$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $post['in_value']/$post['in_qty'],'total_cost'=> $post['in_value'],'out_qty' => $post['out_qty'], 'out_value' => $post['out_value'],'is_active'=>0);
					$res=$callConfig->insertRecord('item_ledger_fifo',$insert);
					$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$post['transaction_date'] ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['in_value'],'day_rate' => $post['in_value']/$post['in_qty'],'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'],'closing_qty'=>$post['in_qty'],'is_active'=>0);
					$res=$callConfig->insertRecord('item_fifo_balances',$insert);
					$insert['location_id'] = $post['location_id'];
					$insert['ware_house_id'] = $post['transaction_warehouse_id'];
					$res=$callConfig->insertRecord('loc_item_fifo_balances',$insert);
				}
			}
		}
		return $res;
	}
	function  itemfifoupdateledge($post){
		global $callConfig;
		$count = $post['out_qty'];
		$itemcount = $post['out_qty'];
		if($count > 0){
			$locquery = "SELECT * FROM loc_item_fifo_balances WHERE item_id =? AND location_id =? AND company_id=? AND closing_qty >0 ORDER By balance_date,id ASC";   
			$params = [$locquery, $post, $post, $post];
			$query = "SELECT * FROM item_fifo_balances WHERE item_id =? AND company_id=? AND closing_qty >0 ORDER By balance_date,id ASC";   
			$params = [$query, $post, $post];

		}else{
			$locquery = "SELECT * FROM loc_item_fifo_balances WHERE item_id =? AND location_id =? AND company_id=? AND (closing_qty = 0 OR closing_qty!=in_qty)  ORDER By balance_date,id ASC";   
			$params = [$locquery, $post, $post, $post];
			$query = "SELECT * FROM item_fifo_balances WHERE item_id =? AND company_id=? AND (closing_qty = 0 OR closing_qty!=in_qty)  ORDER By balance_date,id ASC";   
			$params = [$query, $post, $post];
		}
		$rows = $callConfig->getAllRowsNew($locquery);
		$itemrows = $callConfig->getAllRowsNew($query);
	    //echo $query;
	    //print_r($rows); echo "ravi";
		foreach($itemrows as $itemdata){
			if($itemcount > 0){        
				if($itemdata->closing_qty < $itemcount){
					$itemremaning_count = $itemcount- $itemdata->closing_qty;
					$count = $itemdata->closing_qty;
				}else{
					$itemremaning_count =0;
				}
			}else{
				if($itemdata->closing_qty < -(-$itemcount)){
					$remaning_count = $itemcount+ $itemdata->closing_qty;
					$itemcount = "-".$itemdata->closing_qty;
				}else{
					$itemremaning_count =0;
				}
			}
			$cc = $itemdata->out_qty+$itemcount;
			$ccc = $itemdata->closing_qty-$itemcount;
			$updatequery121 =  "UPDATE item_fifo_balances SET out_qty = ?,closing_qty= ? WHERE id = '?'";
			$params = [$updatequery121, $cc, $ccc, $itemdata->id];
	       //echo $updatequery121;
	       $callConfig->executeQuery($updatequery121);//exit
	       if($itemremaning_count == 0){ 
	       	break;
	       }
	       $itemcount = $itemremaning_count;
	   }

	   foreach($rows as $row){
	   	if($count > 0){        
	   		if($row->closing_qty < $count){
	   			$remaning_count = $count- $row->closing_qty;
	   			$count = $row->closing_qty;
	   		}else{
	   			$remaning_count =0;
	   		}
	   	}else{
	   		if($row->closing_qty < -(-$count)){
	   			$remaning_count = $count+ $row->closing_qty;
	   			$count = "-".$row->closing_qty;
	   		}else{
	   			$remaning_count =0;
	   		}
	   	}
	   	$cc  =$row->out_qty+$count;
	   	$ccc = $row->closing_qty-$count;
	   	$updatequery1 =  "UPDATE loc_item_fifo_balances SET out_qty = ?,closing_qty= ? WHERE id = '?'";
	   	$params = [$updatequery1, $cc, $ccc, $row->id];
	       //echo $updatequery;
	       $callConfig->executeQuery($updatequery1);//exit;
	       $bdate = $post['transaction_date'];
	       $insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'document_no' =>$post['document_no'] ,'document_type' =>$post['document_type'] ,'document_ref_no' =>$post['document_ref_no'] ,'document_date' =>$post['transaction_date'],'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'item_cost' => $row->day_rate,'total_cost'=>$row->day_rate*$count ,'out_qty' => $count, 'out_value' => ($post['out_value']/$post['out_qty'])*$count);
	       $res=$callConfig->insertRecord('item_ledger_fifo',$insert);
	       if($remaning_count == 0){
	       	break;
	       }
	       $count = $remaning_count;
	   }

	   return $res; 
	}

	
	function insertorupdateitemfifo($post){
		global $callConfig;
		$bdate = date('Y-m-d');
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		$res=$callConfig->insertRecord('loc_item_fifo_lifo_balances',$insert);
		return $res;
	}	
	function averagebalance($post){
		//print_r($post);
		global $callConfig;
		$bdate = date('Y-m-d');
		$whrstp="company_id = '?' AND location_id='?' AND item_id= '?' AND balance_date= '?'";
		$params = [$whrstp, $post, $post, $post, $bdate];
		$query=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRowNew($query);
		if($data->id ==''){
			$whrstp1="company_id = '?' AND location_id='?' AND item_id= '?";
			$params = [$whrstp1, $post, $post, $post];
			$query1=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp1,'id DESC','0','1');
			$data = $callConfig->getRowNew($query1);
		}
		$old_qty = $data->opening_qty-$data->out_qty+$data->in_qty;
		$old_rate = $data->day_rate;
		$return['avrage'] = (($old_rate*$old_qty)+($post['in_qty']*$post['in_value']))/($old_qty+$post['in_qty']);
		$return['running_qty'] = $old_qty+$post['in_qty'];
		$return['day_value'] = $return['avrage'] *  $return['running_qty'];
		$return['day_rate'] = $return['avrage'];
		$return['opening_qty'] = '0';
		$return['out_qty'] = '0';
		$return['closing_qty'] = '0';
		$return['opening_value'] = '0';
		$return['opening_rate'] = '0'; 
		$return['out_value'] = '0';

		return $return;
	}
	function insertoupdatelocitemwabalances($post){
		global $callConfig;
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'], 'ware_house_id'=>$post['transaction_warehouse_id'],'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '?' AND location_id='?' AND item_id= '?' AND balance_date = '?'";
		$params = [$whrstp, $post, $post, $post, $bdate];
		$query=$callConfig->selectQuery('loc_item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRowNew($query);
		
		$query2 = "SELECT * FROM loc_item_wa_balances WHERE company_id = '?' AND location_id='?' AND item_id= '?'  AND balance_date <= '?' ORDER BY balance_date DESC LIMIT 0,1";
		$params = [$query2, $post, $post, $post, $bdate];
		//echo $query2;
		$data2 = $callConfig->getRowNew($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data->day_value-($data->day_rate*$post['out_qty']));
				$insert['out_value'] = ($data->day_rate*$post['out_qty']);	
			}
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('loc_item_wa_balances',$insert,'id',$data->id); 
		}else{
			$data2->closing_qty !=""?$data2->closing_qty:0;
			$data2->day_value !=""?$data2->day_value:0;
			$data2->day_rate !=""?$data2->day_rate:0;
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data2->day_value-($data2->day_rate*$post['out_qty']));	
				$insert['out_value'] = ($data2->day_rate*$post['out_qty']);
			}
			$insert['opening_rate'] = $data2->day_rate;
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			$insert['ware_house_id'] = $post['transaction_warehouse_id'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_wa_balances',$insert);
		}

		$updatequery = "update loc_item_wa_balances SET opening_qty = opening_qty+?, closing_qty = closing_qty+?, opening_value = opening_value+?, opening_rate = opening_value/opening_qty, day_value = day_value+?, day_rate = day_value/closing_qty where item_id = ? AND location_id='?' AND balance_date > '?'";
		$params = [$updatequery, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $post, $bdate];
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdatelocitemstbalances($post){
		global $callConfig;
		$post['in_value'] = $post['in_qty']*$post['item_st_cost'];
		$post['out_value'] = $post['out_qty']*$post['item_st_cost'];
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'location_id' =>$post['location_id'] ,'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '?' AND location_id='?' AND item_id= '?' AND balance_date = '?'";
		$params = [$whrstp, $post, $post, $post, $bdate];
		$query=$callConfig->selectQuery('loc_item_st_balances','*',$whrstp,'','','');
		$data = $callConfig->getRowNew($query);
		$query2 = "SELECT * FROM loc_item_st_balances WHERE company_id = '?' AND location_id='?' AND item_id= '?'  AND balance_date <= '?' ORDER BY balance_date DESC LIMIT 0,1";
		$params = [$query2, $post, $post, $post, $bdate];
		//echo $query2;
		$data2 = $callConfig->getRowNew($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data->day_value-($data->day_rate*$post['out_qty']));
				$insert['out_value'] = ($data->day_rate*$post['out_qty']);	
			}
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('loc_item_st_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data2->day_value-($data2->day_rate*$post['out_qty']));	
				$insert['out_value'] = ($data2->day_rate*$post['out_qty']);
			}
			$insert['opening_rate'] = $data2->day_rate;
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			$insert['ware_house_id'] = $post['transaction_warehouse_id'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('loc_item_st_balances',$insert);
		}

		$updatequery = "update loc_item_st_balances SET opening_qty = opening_qty+?, closing_qty = closing_qty+?, opening_value = opening_value+?, opening_rate = opening_value/opening_qty, day_value = day_value+?, day_rate = day_value/closing_qty where item_id = ? AND location_id='?' AND balance_date > '?'";
		$params = [$updatequery, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $post, $bdate];
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdateitemwabalances($post){
		global $callConfig;
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '?' AND  item_id= '?' AND balance_date = '?'";
		$params = [$whrstp, $post, $post, $bdate];
		$query=$callConfig->selectQuery('item_wa_balances','*',$whrstp,'','','');
		$data = $callConfig->getRowNew($query);
		$query2 = "SELECT * FROM item_wa_balances WHERE company_id = '?'  AND item_id= '?'  AND balance_date <= '?' ORDER BY balance_date DESC LIMIT 0,1";
		$params = [$query2, $post, $post, $bdate];
		//echo $query2;
		$data2 = $callConfig->getRowNew($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data->day_value-($data->day_rate*$post['out_qty']));
				$insert['out_value'] = ($data->day_rate*$post['out_qty']);	
			}
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('item_wa_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data2->day_value-($data2->day_rate*$post['out_qty']));	
				$insert['out_value'] = ($data2->day_rate*$post['out_qty']);
			}
			$insert['opening_rate'] = $data2->day_rate;
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('item_wa_balances',$insert);
		}

		$updatequery = "update item_wa_balances SET opening_qty = opening_qty+?, closing_qty = closing_qty+?, opening_value = opening_value+?, opening_rate = opening_value/opening_qty, day_value = day_value+?, day_rate = day_value/closing_qty where item_id = ? AND balance_date > '?'";
		$params = [$updatequery, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $bdate];
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function insertoupdateitemstbalances($post){
		global $callConfig;
		$post['in_value'] = $post['in_qty']*$post['item_st_cost'];
		$post['out_value'] = $post['out_qty']*$post['item_st_cost'];
		$bdate = $post['transaction_date'];
		$insert = array('company_id' =>$post['company_id'] , 'item_id' =>$post['item_id'] ,'balance_date' =>$bdate ,'in_qty' =>$post['in_qty'] ,'in_value' =>$post['in_value'],'out_qty' =>$post['out_qty'] ,'out_value' =>$post['out_value'],'day_value' => $post['day_value'],'day_rate' => $post['day_rate']);
		//echo "<pre>"; print_r($insert);
		$whrstp="company_id = '?' AND  item_id= '?' AND balance_date = '?'";
		$params = [$whrstp, $post, $post, $bdate];
		$query=$callConfig->selectQuery('item_st_balances','*',$whrstp,'','','');
		$data = $callConfig->getRowNew($query);
		$query2 = "SELECT * FROM item_st_balances WHERE company_id = '?'  AND item_id= '?'  AND balance_date <= '?' ORDER BY balance_date DESC LIMIT 0,1";
		$params = [$query2, $post, $post, $bdate];
		//echo $query2;
		$data2 = $callConfig->getRowNew($query2);
		//print_r($data2);// exit;
		if($data->id !=''){
			$insert['in_qty'] = $insert['in_qty'] + $data->in_qty;
			$insert['in_value'] = $insert['in_value'] + $data->in_value;
			$insert['out_qty'] = $insert['out_qty'] + $data->out_qty;
			$insert['out_value'] = $insert['out_value'] + $data->out_value;
			$insert['closing_qty'] = $data->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['day_value'] = $data->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data->day_value-($data->day_rate*$post['out_qty']));
				$insert['out_value'] = ($data->day_rate*$post['out_qty']);		
			}
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];	
			//echo "<pre>"; print_r($insert); //exit;
			$res=$callConfig->updateRecord('item_st_balances',$insert,'id',$data->id); 
		}else{
			$insert['opening_qty'] = $data2->closing_qty;
			$insert['closing_qty'] = $data2->closing_qty+$post['in_qty']-$post['out_qty'];
			$insert['opening_value'] = $data2->day_value;
			$insert['day_value'] = $data2->day_value+$post['in_value']-$post['out_value'];
			if($post['out_qty'] > 0){
				$insert['day_value'] = ($data2->day_value-($data2->day_rate*$post['out_qty']));	
				$insert['out_value'] = ($data2->day_rate*$post['out_qty']);	
			}
			$insert['opening_rate'] = $data2->day_rate;
			if($insert['closing_qty'] !="" AND $insert['closing_qty']!="0"){
				$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			}else{
				$insert['day_rate'] = 0;
			}
			//$insert['day_rate'] = $insert['day_value']/$insert['closing_qty'];
			//print_r($insert); //exit;
			$res=$callConfig->insertRecord('item_st_balances',$insert);
		}

		$updatequery = "update item_st_balances SET opening_qty = opening_qty+?, closing_qty = closing_qty+?, opening_value = opening_value+?, opening_rate = opening_value/opening_qty, day_value = day_value+?, day_rate = day_value/closing_qty where item_id = ? AND balance_date > '?'";
		$params = [$updatequery, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $insert, $bdate];
		$callConfig->executeQuery($updatequery);
		//print_r($res); exit;
		return $res;
	}
	function UpdateBudgetBlance($data){
		global $callConfig;
		$tr_date = explode('-', $data['transaction_date']);
		$month = $this->fiscalmonth($tr_date[1]);
		$field = 'rq_m'.$month;
		$itemsquery  = "SELECT account_code,sum(item_amount) as amount FROM ? WHERE transaction_id = ? AND company_id =? AND is_active = 0  GROUP BY account_code";
		$params = [$itemsquery, $data, $data, $data];
		//echo $itemsquery; exit;
		$rows = $callConfig->getAllRowsNew($itemsquery);

		foreach ($rows as $row) {
			$updatequery = "UPDATE	budget_balances set ? = ?+ ? WHERE company_id = ? AND cost_center_id =? AND account_code = '?' AND fiscal_year ='".FISCALYEAR."'"; 
			$params = [$updatequery, $field, $field, $row->amount, $data, $data, $row->account_code];
			//echo $updatequery; exit;
			$updateblance = $callConfig->executeQuery($updatequery);
		}
		return $updatequery;
	}
	function fiscalmonth($month){
		global $callConfig;
		$array = array('01'=> '10','02'=> '11','03'=> '12','04'=> '1','05'=> '2','06'=> '3','07'=> '4','08'=> '5','09'=> '6','10'=> '7','11'=> '8','12'=> '9');
		return $array[$month];
	}
	function insertandupdatedataapplicationtag($data, $id, $option){
		global $callConfig;

		$appv_fieldnames=array('transaction_id'=>$id,'transaction_number'=>$data['transaction_number'],'transaction_date'=>$data['transaction_date'],'transaction_amount'=>$data['total_amount'],'transaction_status'=>$data['current_status'],'option_url'=>$option);
		//echo "<pre>"; print_r($appv_fieldnames); //exit;
		$whr="transaction_id = ? AND transaction_number='?'";
		$params = [$whr, $id, $data];
		$query=$callConfig->selectQuery(TBL_APPLICATION_TAGS,'*',$whr,'','','');
		//echo $query; //exit;
		$row = $callConfig->getRowNew($query);
		if(count((array)$row) > 0){
			//echo "updata";
			$res=$callConfig->updateRecord(TBL_APPLICATION_TAGS,$appv_fieldnames,'id',$row->id);
		}else{
			$res=$callConfig->insertRecord(TBL_APPLICATION_TAGS,$appv_fieldnames);
		}
		//print_r($row); exit;
		return $res; 

	}
	function tocheckDAOValues($data){
		global $callConfig;
		$whr="process_type='?'";
		$params = [$whr, $data];
		$query1=$callConfig->selectQuery(TBL_WORKFLOW_CONFIGURATION,'*',$whr,'','','');
		$row = $callConfig->getRowNew($query1);
		//print_r($row);
		if($row->tigger_doa == 0){
			$where = "";
			if($data['opex_cpex']!=""){
				$where.= "AND opex_capex = '?'";
				$params = [$where, $data];
			}
			if($data['category_type']!=""){
				$where.= "AND  transaction_category = '?'";
				$params = [$where, $data];
			}
			if($data['cost_center']!=""){
				$where.= "AND cost_center_id = '?'";
				$params = [$where, $data];
			}
			$query = "SELECT id , mail_group_id, doa_condition, approval_type, approval_number FROM ". TBL_DOA ." WHERE transaction_type = '?' and from_transaction_amount <= '?' and to_transaction_amount >= '?' and  is_active =0 ".$where;
			$params = [$query, $data, $data, $data, $where];
			//echo $query;
			$data1 = $callConfig->getRowNew($query);
		}else{
			$data1['id'] = 1;
		}
		return $data1;
	}
	
	function togetemailslist($data){
		global $callConfig;
		$emailstr = "";
		$email_id = array(); $email_group_id = array();
		$data1 = $this->tocheckDAOValues($data);
		if(count((array)$data1) > 0){
			$email_group_id[] = $data1->mail_group_id;
		}
		$whrstp="table_name = '?' AND status='?'";
		$params = [$whrstp, $data, $data];
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		//echo $querystp;
		$resstp = $callConfig->getRowNew($querystp);
		//print_r($resstp);
		$whrstpA="target_status = '?'";
		$params = [$whrstpA, $resstp->id];
		$querystpA=$callConfig->selectQuery(TBL_STATUSPROGRESACTIONS,'*',$whrstpA,'','','');
		//echo $querystpA; exit;
		$resstpA = $callConfig->getRowNew($querystpA);
		$stpaemails_group= json_decode($resstpA->email_groups, 1);
		if(count((array)$stpaemails_group) > 0){
			foreach ($stpaemails_group as $stpavalue) {
				$email_group_id[] = $stpavalue;
			}
		}
		$emil_lists = implode(",",(array)$email_group_id);
		//echo $emil_lists; exit;
		//
		$whr = "email_groups_id IN (?)";	
		$params = [$whr, $emil_lists];
		$email_ids = $this->getAllTablesData(TBL_EMAIL_GROUP_EMAILS,'*',$whr,'');

		foreach ($email_ids as $emails_id) {
			$email_id [] = $emails_id->email_id;
		}
		$email_id = implode(",", (array)$email_id);
		$query1="SELECT email,user_id FROM ".TBL_EMAIL." WHERE id in (?)";
		$params = [$query1, $email_id];
		//echo $query1; exit;
		$emailids = $callConfig->getAllRowsNew($query1);
		foreach ($emailids as $email) {
			$return ['email'] []= $emailids->email;
			if($email ->user_id !=0){
				$return ['user_id'][] = $email->user_id;
			}
		}
		//print_r($return); exit;
		$return ['doa_id'] = $data1->id;
		return $return;
	}
	function tosendmailtest($get){

		$data =$get;
		$data['content']= "Test  API call done workingg";
		$data['email_type'] =0;
		$data['email_content_ref_id']=1;
		$data['created_by']=1;
		$data['mesg']= "Test  API call done workingg";
		$data['subject']= "Test  API";
		
		//$data['revicer'] = array('ravishankar.tangudu@infisuite.com'); 
		$data['revicer'] = array('ravishankar.tangudu@gmail.com'); 
		
		//$data['revicer'] = array('testmail@madilu.infisuite.in');
		//echo "<pre>"; print_r($data); exit;
		$return = $this->sendEmailslist($data);
		return $return;
	}
	function sendEmailslist($data){
		//echo "<pre>"; print_r($data); exit;
		global $callConfig;
		if($data['databasename'] !=""){
			$databasename = $data['databasename'].".";
			$params = [$databasename, $data];
		}
		//print_r($data); exit;
		$email_data = array('email_source'=>$data['sender'], 'content'=>$data['content'], 'email_type'=>$data['email_type'], 'is_active'=>0 , 'current_status'=>0,'email_content_ref_id'=>$data['email_content_ref_id'], 'created_by'=>$data['created_by'] ,'created_date'=> date('Y-m-d H:i:s'));   
		//$smtp = $callConfig->getRowNew("SELECT * FROM ".DUMPTABLE.".smtp_details where is_active= 0");
		foreach ($data['revicer'] as $email) {
			$attachment_data = $data['attachment_data'];
			$attachment_file_name = $data['attachment_file_name'];
			$email_data['email'] = $email;
			$email_res=$callConfig->insertRecord($databasename.TBL_EMAIL_LOGS,$email_data);
			$emailmsg= $data['mesg'];
			$boundary = md5(uniqid(time()));
			$headers = "MIME-Version: 1.0\r\n";
			$sender = $data['sender']!="" ? $data['sender']:SUPERADMIN_EMAIL_ID;
			$headers .= 'From:'.$sender . "\r\n";
			$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
			// Create the email message
			$message_body = "--$boundary\r\n";
			$message_body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
			$message_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
			$message_body .= $emailmsg . "\r\n\r\n";
			if($data['attachment'] == "Y"){
				$message_body .= "--$boundary\r\n";
				$message_body .= "Content-Type: application/pdf; name=\"" .$attachment_file_name. "\"\r\n";
				$message_body .= "Content-Transfer-Encoding: base64\r\n";
				$message_body .= "Content-Disposition: attachment; filename=\"" .$attachment_file_name. "\"\r\n\r\n";
				$message_body .= base64_encode($attachment_data) . "\r\n\r\n";
			}
			$message_body .= "--$boundary--";
			
			// More headers
			//$headers .= 'Cc: myboss@example.com' . "\r\n";
			$subject = ucwords(str_replace('_', ' ', $data['subject']));
			//mail($email,$subject,$message_body,$headers); 
			$email_data['email_msg'] = $this->sendmailwithsmtp($email,$subject,$data['mesg'],"",$data);
		}
		//echo "email send sucess";
		return $email_data;
	}
	function sendmailwithsmtp($email, $subject, $mesg, $smtp,$data){
		global $callConfig;
		//print_r($smtp); exit;
		//echo $email."+----------------------------"; exit;
		//echo DUMPTABLE." ------------------ SELECT * FROM ".DUMPTABLE.".smtp_details where is_active= 0";
		$smtp = $callConfig->getRowNew("SELECT * FROM ".DUMPTABLE.".smtp_details where is_active= 0");
		$params = [$smtp, $callConfig->getRowNew];
		//echo "smtp"; print_r($smtp); exit;
		if($smtp->id!=""){
			require_once("SMTP/class.phpmailer.php");
			$mail = new PHPMailer;
			$mail->IsSMTP();                                      // Set mailer to use SMTP
			$mail->Host = $smtp->host;                 // Specify main and backup server
			//$mail->Host = "Give IP Address";                 // If the above does not work.
			$mail->Port = $smtp->port;                                    // Set the SMTP port
			$mail->SMTPAuth = true;       
			//$mail->SMTPDebug  = 1;                        // Enable SMTP authentication
			$mail->Username = $smtp->user_name;                // SMTP username
			$mail->Password = $smtp->password;                  // SMTP password
			$mail->SMTPSecure = "ssl";                            // Enable encryption, 'ssl' also accepted
			$mail->From = $smtp->from_email;
			$mail->FromName = $smtp->from_name;
			$mail->AddAddress($email);  // Add a recipient
			$res['to_email'] = $email;

			if(isset($data['attachment']) && $data['attachment'] == "Y"){
    			$mail->addAttachment($data['attachment_file_path']);  
    		}
			$mail->IsHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    =  $mesg;
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			$res['error_email_msg'] = 'Message has been sent'; 
			if(!$mail->Send()) {
				//print_r($mail);
				$res['error_email_msg'] = 'Message could not be sent.';
				$params = [$res];
				$res['error_email_'] =  'Mailer Error: ' . $mail->ErrorInfo;
				//exit;
			}
			
		}else{
			$res['error_email_msg'] = 'SMTP Details are Empty';
		}
		return $res;
	}
	function checkhierarchicallevel($data){
		global $callConfig;
		$whrstp="company_id = '?' AND process_type='?'";
		$params = [$whrstp, $data, $data];
		$query=$callConfig->selectQuery(TBL_WORKFLOW_CONFIGURATION,'*',$whrstp,'','','');
		$res = $callConfig->getRowNew($query);
		//echo "<pre>"; print_r($res);
		$return['doa'] = $res->tigger_doa == 0 ? 1 : 2;
		if($res->trigger_workflow == 1){
			if($res->custom_workflow == 1){
				$level = $res->hierarchical_level;
				//echo $level;
				$where = "company_id = '?' AND transaction_table = '?' AND transaction_id='?' AND send_back_flag=0 AND response_type !=0 AND doa_id = 0";	
				$params = [$where, $data, $data, $data];
				$query1=$callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$where,'','','');
				$reslut = $callConfig->getAllRowsNew($query1);
				//	print_r($reslut);
				if(count((array)$reslut) < $level ){
					//echo "ravi";
					//print_r($reslut);
					//$return['status'] = '0';
					$query3 = "SELECT id,user_email FROM ".TBL_USERS." WHERE id IN (SELECT reports_to FROM ".TBL_USERS." WHERE id=?)";
					$params = [$query3, $data];
					//echo $query3;
					$ret = $callConfig->getRowNew($query3);
					//print_r($ret);
					$return['email'] = $ret->user_email;
					$return['user_id'] = array($ret->id);
					$return['doa'] = 2;
					$return['doa_id'] =0;
				}
			}else{
				if($return['doa'] != 1){
					print_r($data);
					$whrstp="table_name = '?' AND status='?'";
					$params = [$whrstp, $data, $data];
					$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
					//echo $querystp;
					$resstp = $callConfig->getRowNew($querystp);
					$whrstp="table_name = '?' AND status='0'";
					$params = [$whrstp, $data];
					$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
					//echo $querystp;
					$resstpb = $callConfig->getRowNew($querystp);
					//print_r($resstp);
					$whrstpA="target_status = '?' AND  status_progress_id = '?'";
					$params = [$whrstpA, $resstp->id, $resstpb->id];
					$querystpA=$callConfig->selectQuery(TBL_STATUSPROGRESACTIONS,'*',$whrstpA,'','','');
					//echo $querystpA; //exit;
					$resstpA = $callConfig->getRowNew($querystpA);
					$querystpA = " select * From status_progress_actions  where status_progress_id in ( select id from status_progress where table_name = '?' and status = ?) and target_status in ( select id From status_progress where status = ? and table_name = '?')";
					$params = [$querystpA, $data, $data, $data, $data];
					$resstpA = $callConfig->getRowNew($querystpA);
					//echo $querystpA; exit;
					//print_r($resstpA);
					
					$query3 = "SELECT id,user_email FROM ".TBL_USERS." WHERE id IN (select ".$resstpA ->work_flow_field." From ? where id= ? )";
					$params = [$query3, $resstpA, $data, $data];
					//echo $query3;
					$ret = $callConfig->getRowNew($query3);
					//print_r($ret);
					$return['email'] = $ret->user_email;
					$return['user_id'] = array($ret->id);
					$return['doa'] = 2;
					$return['doa_id'] =0;
				}
			}

		}
		//print_r($return); exit;
		return $return;
	} 

	function toSendEmailNotificationsBaseDAO($data){
		//echo "<pre>"; print_r($data); exit;
		global $callConfig;
		$email_data = $this->checkhierarchicallevel($data);
		//print_r($email_data);exit;
		if($email_data['doa'] == 1){
			$email_data = $this-> togetemailslist($data);
		}
		//print_r($email_data);exit;
		$emails[] = $email_data['email'];
		//$EMAIL FUNTION
		$users = $email_data['user_id'];
		$query1="SELECT email,user_id FROM ".TBL_EMAIL." WHERE user_id in (?)";
		$params = [$query1, $data];
		//echo $query1; exit;
		$emailids = $callConfig->getRowNew($query1);
		//print_r($emailids);exit;
		$emails[] = $email_ids->email;
		//print_r($email_data);exit;
		$whrstp="table_name = '?' AND status='?'";
		$params = [$whrstp, $data, $data];
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		//echo $querystp; //exit;
		$resstp = $callConfig->getRowNew($querystp);
		//print_r($resstp); exit;
		$data['action_label'] =  $resstp->action_label;
		$data['email_template'] =  3;
		//print_r($data); exit;
		$data['email_content_id'] = 3;
		$email_array = $this->emailtemplate($data);
		
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$params = [$file_name];
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		//fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);

		$notarry['notification_message'] = ucwords($data['transaction_type'])." ?: ".ucwords($resstp->action_label) ;
		$params = [$notarry, $data, $data, $resstp->action_label];
		//echo "ravi"; exit;
		$notarry['notification_users'] = count((array)$users);
		$notarry['id'] = $data['Rid'];
		$notarry['option'] = $data['option'];
		//print_r($notarry); exit;
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'application_table_name'=>$data['transaction_type'],'application_table_name'=>$notarry['application_table_name']);
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		//$notification_id = 13;
		//echo "ravi".$notification_id ; print_r($users);	  exit;
		foreach ($users as $user) {
			//$user_id = $user->id;
			$appv_fieldnames=array('company_id'=>$data['company_id'],'module_setup_id'=>$callConfig->module_id,'transaction_table'=>$data['transaction_type'],'transaction_id'=>$data['Rid'],'user_id'=>$user,'request_date'=>date("Y-m-d H:i:s"),'doa_id' => $email_data['doa_id']);
			if($data['status'] == ST_APPROVAL_PENDING || $data['status'] == ST_APPROVED1 || $data['status'] == ST_APPROVED2 || $data['status'] == ST_APPROVED3 || $data['status'] == ST_APPROVED4 ||$data['status'] == ST_APPROVED5 || $data['status'] == ST_APPROVED6 || $data['status'] == ST_APPROVED7 || $data['status'] == ST_APPROVED8 || $data['status'] == ST_APPROVED9){
				$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
				$revalue = 'ACTION';
			}else{
				$revalue = 'INFORMATION';
			}
			$nti_use_fieldnames=array('user_id'=>$user,'notification_id'=>$notification_id,'notification_type'=>$revalue);
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
			//echo "<pre>"; print_r($nti_use_fieldnames);
		}
		
		if($data['originator'] != ''){
			$nti_use_fieldnames=array('user_id'=>$data['originator'],'notification_id'=>$notification_id,'notification_type'=>'ACTION');
		//print_r($nti_use_fieldnames); exit;
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}
		//echo "ravi"; exit;
		return $noti_res;
	}
	function getInvoicesData($id,$table){
		global $callConfig;	
		if($table !=""){
			$fieldarray  = $this->toGetInvoicetabledata($table);
		}		
		if($id !=""){
			$where = "id = '?'  AND is_active = 0";	
			$params = [$where, $id];
			$query=$callConfig->selectQuery($table,'*',$where,'','','');
			$data = $callConfig->getRowNew($query);
		}
		$pr['data'] = $data;
		//print_r($pr);
		//$pr = $pr->0;
		if($id !=""){
			$whr = "transaction_id = '?' AND is_active = 0";	
			$params = [$whr, $id];
			$itemsre = $this->getAllTablesData($fieldarray['item'],'*',$whr,'');
		}
		$itemlist = $itemresult = array();
		//$pr['items'] = $itemsre;
		if(count((array)$itemsre) != 0){
			foreach ($itemsre as $itlist) {
				if($data->current_status != ST_APPROVED AND $itlist->item_id !=""){
					$transaction_item_tab_detail  = json_decode($itlist->item_details,1);
					if($itlist->item_id !=""){
						$itaares = $this->togetitemvalues($itlist->item_id);
					}
					$item_details_before_approval = json_encode(array('so'=>$itaares->sales_order_qty,'po'=>$itaares->purchase_order_qty,'qoh'=>$itaares->stock_in_hand,'available'=>$itaares->available_qty,'min_ql'=>$itaares->minimum_level,'max_ql'=>$itaares->maximum_capacity,'grn_inv_qty'=>$transaction_item_tab_detail['grn_inv_qty'],'grn_qty'=>$transaction_item_tab_detail['grn_qty']));
					$itlist->item_details  = $item_details_before_approval;
				}
				$itemresult[] = $itlist;
				$itemlist[] = $itlist->item_id;
			} 
		}
		$pr['items'] = $itemresult;
		//it.company_id = 1 AND
		if(count((array)$itemlist) != 0){
			$hsnquery = "SELECT it.id, hsn.code FROM items it, hsn_and_sac_codes hsn WHERE it.company_id = hsn.company_id AND it.id IN(".implode(",",$itemlist).") AND it.hsn_and_sac_code_id = hsn.id AND it.is_active = hsn.is_active AND it.is_active = 0";
			$params = [$hsnquery, $itemlist];
			$hsndata = $callConfig->getAllRowsNew($hsnquery);
			foreach ($hsndata as $hsnres) {
				$hsnresult[$hsnres->id] = $hsnres->code;
			}
		}
		$pr['itemidwithhsn'] =$hsnresult;
		if($id !="" AND $fieldarray['tax']!=""){
			$whrs = "transaction_id = '?' AND is_active = 0";	
			$params = [$whrs, $id];
			$pr['taxes'] = $this->getAllTablesData($fieldarray['tax'],'*',$whrs,'');
		}
		if($id !="" AND $table!=""){
			$whrs1 = "transaction_id = '?' AND application_table_name = '?' AND is_active != 1";
			$params = [$whrs1, $id, $table];
			$pr['files'] = $this->getAllTablesData(TBL_APPLICATION_ATTACHMENTS,'*',$whrs1,'');
		}

		//added rohan - to ref table details
		if($data->ref_type !="" AND $data->ref_id !=""){
			$fieldarray  = $this->toGetInvoicetabledata($data->ref_type);
			$where = "id = '?'  AND is_active = 0";	
			$params = [$where, $data->ref_id];
			$query=$callConfig->selectQuery($data->ref_type,'*',$where,'','','');
			$data1 = $callConfig->getRowNew($query);
			$reftable['data'] = $data1;
			
			$whr = "transaction_id = '?' AND is_active = 0";	
			$params = [$whr, $data->ref_id];
			$reftable['items'] = $this->getAllTablesData($fieldarray['item'],'id,item_id,item_quantity,approved_quantity,pending_qty,item_name',$whr,'');
			$pr['ref_table_details'] = $reftable;
			
			if($data1->ref_type !="" AND $data1->ref_id !=""){
				$fieldarray  = $this->toGetInvoicetabledata($data1->ref_type);
				$whr = "transaction_id = '?' AND is_active = 0";	
				$params = [$whr, $data1->ref_id];
				$secondreftable['items'] = $this->getAllTablesData($fieldarray['item'],'transaction_id,item_id,item_quantity,approved_quantity,pending_qty,item_name',$whr,'');
				$pr['2_ref_table_details'] = $secondreftable;
			}
		}
		//End added rohan
		// -------------------- journal_vouchers ---------------------------
		if($id !=""){
			$jvsql = "SELECT id, transaction_number FROM journal_vouchers WHERE  ref_id = ? AND is_active = 0";
			$params = [$jvsql, $id];
			$jvres = $callConfig->getRowNew($jvsql);
			if($jvres->id !=""){
				$jv_itemssql = "SELECT id,account_code,debit_amount,credit_amount, particulars FROM jv_items WHERE transaction_id = ? AND is_active = 0";
				$params = [$jv_itemssql, $jvres->id];
				$jv_itemres = $callConfig->getAllRowsNew($jv_itemssql);
				$pr['jv_items']  = $jv_itemres;
			}
		}

		// -------------------- End journal_vouchers ---------------------------
		
		// -------------------- Start chart_of_accounts ---------------------------
		if($data->company_id !=""){
			 $chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = '?' AND is_active =0 AND allow_posting = 0 ";
			 $params = [$chartofaccountssql, $data->company_id];
	        $chartofaccountsres = $callConfig->getAllRowsNew($chartofaccountssql);
	        $chart_of_accounts = array();
	        foreach($chartofaccountsres as $coaccount){
	        	$chart_of_accounts[$coaccount->account_code] = $coaccount->account_name; 
	        }
	        $pr['chart_of_accounts']= $chart_of_accounts;
        }	
        // -------------------- End chart_of_accounts ---------------------------

        // -------------------- Start chart_of_accounts ---------------------------
		 // $additional_chargessql = "SELECT id,charge_name FROM `additional_charges` WHERE is_active =0";
		//      $additional_chargesres = $callConfig->getAllRowsNew($additional_chargessql);
		//      $pr['additional_charges']= $additional_chargesres;	
        // -------------------- End chart_of_accounts ---------------------------


        // -------------------- Start project_task_details ---------------------------
        if($data->company_id !="" AND $data->id!="" AND $table !=""){
			$project_task_detailssql = "SELECT id,task_name,planned_start,current_status FROM `project_task_details` WHERE company_id = '?' AND ref_table = 'follow_up_levels' AND transaction_table = '?' AND transaction_id = '?' AND is_active =0";
			$params = [$project_task_detailssql, $data->company_id, $table, $data->id];
	        $project_task_detailsres = $callConfig->getAllRowsNew($project_task_detailssql);
	        $pr['project_task_details']= $project_task_detailsres;	
        }	
        // -------------------- End project_task_details --------------------------

         // -------------------- Start project_task_details ---------------------------
        if($data->company_id !=""){
			$tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type FROM `tax_authorities_rates_split` WHERE company_id = '?' AND is_active =0";
			$params = [$tax_authorities_rates_splitsql, $data->company_id];
	        $tax_authorities_rates_splitres = $callConfig->getAllRowsNew($tax_authorities_rates_splitsql);
	        $pr['tax_authorities_rates_split']= $tax_authorities_rates_splitres;
	    }	
        // -------------------- End project_task_details --------------------------
	    if($data->company_id !="" AND $id!="" AND $table !=""){
	        $debit_notessql = "SELECT count(id) as cnt FROM debit_notes WHERE company_id = '?' AND ref_type = '?' AND ref_id = '?' AND is_active = 0";
	        $params = [$debit_notessql, $data->company_id, $table, $id];
			$debit_notesres = $callConfig->getRowNew($debit_notessql);
			$pr['debit_notessql']  = $debit_notessql;
			$pr['debit_notes_cnt']  = $debit_notesres;
	        
			$credit_notessql = "SELECT count(id) as cnt FROM credit_notes WHERE company_id = '?' AND ref_type = '?' AND ref_id = '?' AND is_active = 0";
			$params = [$credit_notessql, $data->company_id, $table, $id];
			$credit_notesres = $callConfig->getRowNew($credit_notessql);
			$pr['credit_notes_cnt']  = $credit_notesres;
		}
		return $pr;
	}
	function toGetStatusProgressActionList($table, $status){
		global $callConfig;
		//echo $table."|||".$status;
		$whr = "table_name = '?' AND status = '?'";
		$params = [$whr, $table, $status];
		$statusp = $this->getrowTablesData(TBL_STATUSPROGRES,'*',$whr,'');	
		$status_id = $statusp->id;
		$whrs = "status_progress_id = '?' AND is_active = 0";
		$params = [$whrs, $status_id];
		$datas = $this->getAllTablesData(TBL_STATUSPROGRESACTIONS,'*',$whrs,'');
		foreach ($datas as  $data) {
		//	print_r($data);
			$whr1 = "id = '?'";
			$params = [$whr1, $data->target_status];
			//echo $whr1;
			$status = $this->getrowTablesData(TBL_STATUSPROGRES,'*',$whr1,'');	
			$len[] = $status;
			//print_r($len);
		}

		//echo $status_id; exit;
		//print_r($status);
		return $len;
	}


	function updateapprovallog($post){
		global $callConfig;
		$whr = "";
		$fieldnames = array('comments'=>$post['comments'],'response_date'=>$post['response_date'],'response_type'=>$post['response_type']);
		//print_r($post);// exit;
		$whr = "transaction_table = '?' AND transaction_id='?' AND user_id='?' AND send_back_flag= 0";
		$params = [$whr, $post, $post, $post];
	//	echo  $whr; exit;
		$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldnames,'','',$whr);
		if($post['response_type'] != ST_SEND_BACK ){ 
		    //echo "ravi"; exit;
			$data = array('transaction_type'=>$post['response_date'],'total_amount'=>$post['amount'],'status'=>6,'status_old_value'=>0,'company_id' => $post['company_id']);
			$return = $this->ToCheckStatusApprovalLog($post);
			//echo $return; exit;
			//$return = $post['response_type'];
		}else{
			$fieldname = array('response_date'=>$post['response_date'],'send_back_flag'=>1);
		//print_r($post); exit;ZZ
			$whrs = "transaction_table = '?' AND transaction_id='?' AND send_back_flag= 0";
			$params = [$whrs, $post, $post];
			$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldname,'','',$whrs);
			$return = $post['response_type'];
		} //exit;
		return $return;
	}
	function toGetPendingRequisitionQty($post){
		global $callConfig;
		$fieldarray  = $this->toGetInvoicetabledata($post['table']);
		$query = "SELECT rq.id ,sum(reqi.pending_qty)as pending_qty,rq.transaction_number FROM ? rq, ? reqi WHERE rq.id = reqi.transaction_id AND rq.current_status = '".ST_APPROVED."' GROUP BY rq.id,rq.transaction_number having sum(reqi.pending_qty) > 0";
		$params = [$query, $post, $fieldarray];
		//echo $query;
		$data = $callConfig->getAllRowsNew($query);
		return $data;
	}
	function emailnumberschange($mesg){
		global $callConfig;
		$mes1= strpos($mesg,'[')+1;
		$mes2= strpos($mesg,']');
		if($mes2 == ''){ $mes2=0; }
		//echo "11qww".$mes1."ppp".$mes2;
		if($mes1>0 && $mes2>0){
			$str =''; $sstr ='';
			$str = substr($mesg,$mes1,$mes2-$mes1);
			$sstr = "[?]";
			$params = [$sstr, $str];
			$mesg = str_replace($sstr, $this->numberToWordFormateRupees($str), $mesg);
			$mesg = $this->emaildatareplace($data, $mesg);	
		} 
		return $mesg;
	}
	function numberToWordFormateRupees($number){
		global $callConfig;
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
		$str = array_reverse($str);
		$result = implode('', $str);
		$points = ($point) ?
		". " . $words[$point / 10] . " " . 
		$words[$point = $point % 10] : '';
		if($result !=''){
			$result = $result . "Rupees  ";
		}
		if($points !=''){
			$result.= $points . " Paise  ";
		}
		return ucwords($result." only");
	}
	function getplaceofsupply($company_id){
		global $callConfig;
		$query = "select  * from states where country_id in ( select base_country from companies where company_id =?)";
		$params = [$query, $company_id];
		$data = $callConfig->getAllRowsNew($query);
		return $data;
	}
	function notificationRead($id, $user_id){
		global $callConfig;
		$query = "select  * from ".TBL_NOTIFICATIONS." where id in ( select notification_id from ".TBL_NOTIFICATON_USER." where id =?)";
		$params = [$query, $id];
		//echo $query; exit;
		$data = $callConfig->getRowNew($query);
		$query1 = "select  id from ".TBL_NOTIFICATIONS." where  application_table_name = '?' AND transaction_number ='?'";
		$params = [$query1, $data->application_table_name, $data->transaction_number];
		//echo $query1; exit;
		$datas = $callConfig->getAllRowsNew($query1);
		foreach ($datas as $val) {
			$dd[]=$val->id;
		}
		$imp = implode(",", (array)$dd);
		echo $imp;
		$lsquery = "UPDATE ".TBL_NOTIFICATON_USER." SET read_status =1 WHERE notification_id in (?) AND user_id = ".$user_id;
		$params = [$lsquery, $imp, $user_id];
		//echo $lsquery; exit;
		$res = $callConfig->executeQuery($lsquery);
		return $res;

	}
	function ToCheckStatusApprovalLog($post){
		global $callConfig;
		//print_r($post); exit;
		$data['transaction_type'] = $post['table'];
		$data['total_amount'] = $post['amount'];
		$data['status'] = $post['response_type'];
		$data['status_old_value'] = $post['status_old'];
		$post['transaction_type'] = $post['table'];
		$post['Rid'] = $post['hid'];
		$post['updated_by'] = $post['user_id'];
		$workflow = $this->checkhierarchicallevel($post);
		//print_r($workflow); exit;
		if($workflow['doa'] == 1){
			$res = $this->tocheckDAOValues($data);
			$return = $post['status_old'];
			//print_r($res); exit;
			if($res->doa_condition == 1){
				$where = "transaction_table = '?' AND transaction_id='?' AND send_back_flag=0 AND doa_id != 0 ";	
				$params = [$where, $post, $post];
				$query=$callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$where,'','','');
				$maindata = $callConfig->getAllRowsNew($query);
				$count= count((array)$maindata);
				$doaval = $res->approval_type;
				$doanum = $res->approval_number;
				foreach ($maindata as $mvalue) {
					//if($mvalue->response_type == 13){	$val['sent_back'][] = $mvalue->id;	}

					if($mvalue->response_type == ST_REJECTED){	$val['reject'][]  =	$mvalue->id;	}
					else if($mvalue->response_type == 0){	$val['pending'][] =	$mvalue->id;	}
					else{	$val['approve'][] =	$mvalue->id;	}
				}
				if($doaval == 'A'){
					if($count == count((array)$val['approve'])){ $return = ST_APPROVED; }
					if(count((array)$val['reject'])>0){ $return = ST_REJECTED; }
				}
				if($doaval == 'M'){

					if($doanum <= count((array)$val['approve']) ){ $return = ST_APPROVED;  }
					elseif (count((array)$val['reject'])<= 0) {
						$return = $post['status_old'];
					}else{
						$check = count((array)$val['approve'])+count((array)$val['pending']);
						if($check < $doanum ){ $return = ST_REJECTED; }
					}
					 //echo $count."|||".$return; 
				}
				if($doaval == 'L'){
					$return = $post['response_type'];
				}
			}else{
				$return = $post['response_type'];
			}
		}
		if($res->doa_condition == 0){
			$return = $post['response_type'];
		}
		if($res->doa_condition == 2){
			$return = ST_APPROVED;
		}
		return $return;
	}
	function ToInserttransactions($data){
		global $callConfig;
		//print_r($data); exit;
		$fieldarray  = $this->toGetDynamicInvoicetables();
		//print_r($fieldarray);
		$vendors = explode(",", $data['vender_id']);
		$transaction_id = $data['tranid'];
		$transaction_table = $data['table'];
		$insert_table = $data['new_table'];
		$party_type = $data['party_type'];
		$whr = "id = '?' AND is_active = 0";	
		$params = [$whr, $transaction_id];
		$query1=$callConfig->selectQuery($transaction_table,'*',$whr,'','','');
		$post = $callConfig->getRowNew($query1);
		// taxe part
		$tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type FROM `tax_authorities_rates_split` WHERE company_id = '?' AND is_active =0";
		$params = [$tax_authorities_rates_splitsql, $post->company_id];
        $tax_authorities_rates_splitres1 = $callConfig->getAllRowsNew($tax_authorities_rates_splitsql);
       	$tax_authorities_rates_splitres = json_decode(json_encode($tax_authorities_rates_splitres1), true);
		$return['tax_authorities_rates_splitres'] = $tax_authorities_rates_splitres;
       // taxes part
		$whe = "transaction_id = '?' AND (pending_qty !=0 OR pending_qty !='') AND is_active = 0";
		$params = [$whe, $transaction_id];
		if($data['allowitemid'] !=""){
			$whe .= " AND id IN(?)";
			$params = [$whe, $data];
		}	
		$query_item=$callConfig->selectQuery($fieldarray[$transaction_table]['item'],'*',$whe,'','','');
		$item_datas = $callConfig->getAllRowsNew($query_item);
		$subt_amount = 0; $stax_amount=0; $discount_amount = 0; $total_amount = 0;
		$wher = "transaction_id = '?' AND is_active = 0";	
		$params = [$wher, $transaction_id];
		 $query_tax=$callConfig->selectQuery($fieldarray[$transaction_table]['tax'],'*',$wher,'','','');
		$tax_data = $callConfig->getAllRowsNew($query_tax);
		foreach($tax_data as $tax_dat){
			$item_tax[$tax_dat->transaction_item_row_id][] = $tax_dat;
		}
		//print_r($tax_data);print_r($item_tax);exit;
		$insert_id = 999;
		foreach($item_datas as $item_data){
			$pqty = $item_data->pending_qty;
			$fqty = $item_data->item_quantity;
			$itemfieldnames[$item_data->id]=array('company_id'=>$item_data->company_id,'transaction_id'=>$insert_id,'item_id'=>$item_data->item_id,'account_code'=>$item_data->account_code,'item_name'=>$item_data->item_name,'description'=>$item_data->description,'purchase_uom'=>$item_data->purchase_uom,'item_quantity'=>$item_data->pending_qty,'item_price'=>$item_data->item_price,'item_amount'=>($item_data->item_amount/$fqty)*$pqty,'tax_amount'=>($item_data->tax_amount/$fqty)*$pqty,'discount_amount'=>($item_data->discount_amount/$fqty)*$pqty,'total_amount'=>($item_data->total_amount/$fqty)*$pqty,'pending_qty'=>$item_data->pending_qty,'transaction_qty'=>$item_data->pending_qty,'created_by'=>$data['user_id'],'created_date'=>date('Y-m-d H:i:s'),'is_active'=>'0','ref_transaction_item_table_id'=>$item_data->id);
			if($insert_table == "quotations" || $insert_table == "purchase_orders"){
				$itemfieldnames[$item_data->id]['doc_ref_type']=$fieldarray[$transaction_table]['item'];
				$itemfieldnames[$item_data->id]['doc_ref_id']=$item_data->id;
				$itemfieldnames[$item_data->id]['doc_ref_no']=$post->transaction_number;
				$itemfieldnames[$item_data->id]['doc_ref_date']=$post->transaction_date;
			}
			$subt_amount = $subt_amount+(($item_data->item_amount/$fqty)*$pqty);
			$stax_amount = $stax_amount+(($item_data->tax_amount/$fqty)*$pqty);
			$discount_amount = $discount_amount+(($item_data->discount_amount/$fqty)*$pqty);
			//$total_amount = $total_amount+(($item_data->total_amount/$fqty)*$pqty);
			foreach($item_tax[$item_data->id] as $tax_data){
				$itemtaxenty = array('company_id'=>$tax_data->company_id,'transaction_id'=>$insert_id,'transaction_item_id'=>$tax_data->transaction_item_id,'tax_name'=>$tax_data->tax_name,'tax_value'=>($tax_data->tax_value/$fqty)*$pqty,'tax_id'=>$tax_data->tax_id,'amount'=>($tax_data->amount/$fqty)*$pqty,'tax_amount'=>($tax_data->tax_amount/$fqty)*$pqty,'total_amount' =>($tax_data->total_amount/$fqty)*$pqty,'is_active'=>0,'created_by'=>$data['user_id'],'created_date'=>date('Y-m-d H:i:s'));

				$tax_amt = ($tax_data->tax_amount/$fqty)*$pqty;
				$tax_value = $tax_data->tax_value;
				if($post->originator_state_id == $post->supplier_state_id){ 
					$tax_flag = "S";
					$split_1_name = $tax_authorities_rates_splitres[0]['split_name'];
				    $split_2_name = $tax_authorities_rates_splitres[1]['split_name'];
				    $split_1 = $tax_authorities_rates_splitres[0]['tax_rate'];
				    $split_2 = $tax_authorities_rates_splitres[1]['tax_rate'];
				    $split_1_gl_account_code =  $tax_authorities_rates_splitres[0]['account_code'];
				    $split_2_gl_account_code =  $tax_authorities_rates_splitres[1]['account_code'];
				    $txper1 = $tax_value*$split_1;
					$txval1 = $tax_amt*$split_1;
					$txper2 = $tax_value*$split_2;
					$txval2 = $tax_amt*$split_2;
					$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txper2,'tax_amount'=>$txval2,'account_code'=>$split_2_gl_account_code));  
					$itemtaxenty['tax_split_details'] = json_encode($tax_split_details);
				}else{
					$tax_flag = "I";
					$split_1_name = $tax_authorities_rates_splitres[2]['split_name'];
				    $split_1 = $tax_authorities_rates_splitres[2]['tax_rate'];
				    $split_1_gl_account_code =  $tax_authorities_rates_splitres[2]['account_code'];
				    $txper1 = $tax_value*$split_1;
					$txval1 = $tax_amt*$split_1;
					$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txper1,'tax_amount'=>$txval1,'account_code'=>$split_1_gl_account_code)); 
					$itemtaxenty['tax_split_details'] = json_encode($tax_split_details);
				}
				$itemtaxfieldnames[$item_data->id][]=$itemtaxenty;
			}
		}
		$stax_amount = $stax_amount+$post->shipping_tax_amount;
		$total_amount = $subt_amount + $post->shipping_amount + $stax_amount-$discount_amount;
		$fieldnames=array('company_id'=>$post->company_id,'transaction_number'=>"",'transaction_date'=>date("Y-m-d"),'currency_id'=>$post->currency_id,'currency_rate'=>$post->currency_rate,'originator'=>$data['user_id'],'party_id'=>"",'party_name'=>"",'party_email'=>"",'party_tax_number'=>"",'party_phone'=>"",'party_address'=>"",'party_ship_adderss'=>"",'place_of_supply'=>$post->place_of_supply,'payment_terms'=>$post->payment_terms,'notes'=>$post->notes,'printing_notes'=>$post->printing_notes,'amount'=>$subt_amount,'tax_amount'=>$stax_amount,'type'=>$post->type,'tax_type'=>$post->tax_type, 'discount_type'=>$post->discount_type,'discount_value'=>$post->discount_value,'discount_amount'=>$discount_amount, 'total_amount'=>$total_amount, 'shipping_tax'=>$post->shipping_tax,'shipping_amount'=>$post->shipping_amount,'shipping_tax_amount'=>$post->shipping_tax_amount,'tax_state'=>$post->tax_state,'opex_cpex'=>$post->opex_cpex,'ref_type'=>$transaction_table,'ref_id'=>$transaction_id,'ref_no'=>$post->transaction_number,'ref_date'=>$post->transaction_date,'cost_center_id'=>$post->cost_center_id,'current_status'=> ST_DRAFT,'created_by'=>$data['user_id'],'created_date'=>date('Y-m-d H:i:s'),'originator_currency_id'=>$post->originator_currency_id,'originator_state_id'=>$post->originator_state_id,'supplier_state_id'=>$post->supplier_state_id,'payment_term_history'=>$post->payment_term_history,'due_date'=>$post->due_date,'pending_amount'=>$total_amount,'is_active'=>'0');
		//print_r($fieldnames);print_r($itemfieldnames);print_r($itemtaxfieldnames);
		foreach ($vendors as $vendor){
			$transation = $this->toGetApplicationSerialNumber($insert_table);
			$fieldnames['transaction_number'] = $transation;
			$where2 = "id =". $vendor;	
			if($party_type == "customers"){
				$query2=$callConfig->selectQuery('customers','*',$where2,'','','');
			}else{
				$query2=$callConfig->selectQuery(TBL_VENDOR,'*',$where2,'','','');
			}
			$vendor_data = $callConfig->getRowNew($query2);
			
			$fieldnames['party_id'] = $vendor;
			$fieldnames['party_name'] = $vendor_data->company;
			$fieldnames['party_email'] = $vendor_data->email;
			$fieldnames['party_tax_number'] = $vendor_data->vendor_tax_reg_no;
			$fieldnames['party_phone'] = $vendor_data->phone;
			$fieldnames['party_address'] = $vendor_data->billing_address;
			$fieldnames['party_ship_adderss'] = $vendor_data->shipping_address;
			$fieldnames['payment_terms'] = $fieldnames['payment_terms']!=""?$fieldnames['payment_terms']:$vendor_data->payment_terms;

			if($transaction_table == 'sales_orders' && $insert_table == 'purchase_orders'){
				$fieldnames['amount'] = 0;
				$fieldnames['tax_amount'] = 0;
				$fieldnames['discount_value'] = 0;
				$fieldnames['discount_amount'] = 0;
				$fieldnames['total_amount'] = 0;
				$fieldnames['shipping_tax'] = 0;
				$fieldnames['shipping_amount'] = 0;
				$fieldnames['shipping_tax_amount'] = 0;
				$fieldnames['pending_amount'] = 0;
			}
			$insert_data=$callConfig->insertRecord($insert_table,$fieldnames);
			$insert_id = $insert_data['LASTID'];
			$res['main'] = $fieldnames;
			$res['main_res'] = $insert_data;
			foreach ($itemfieldnames as $key => $item_data){
				$item_data['transaction_id'] = $insert_id;

				if($transaction_table == 'sales_orders' && $insert_table == 'purchase_orders'){
					$item_data['item_price'] = 0;
					$item_data['item_amount'] = 0;
					$item_data['tax_amount'] = 0;
					$item_data['discount_amount'] = 0;
					$item_data['total_amount'] = 0;
				}

				$item_id=$callConfig->insertRecord($fieldarray[$insert_table]['item'],$item_data);
				$transaction_item_row_id = $item_id['LASTID'];
				$res['item'][] = $item_data;
				$res['item_res'][] = $item_id;
				//$ref_transaction_item_table_id = $item_data['id'];
				if($transaction_table != 'sales_orders' && $insert_table != 'purchase_orders'){
					foreach($itemtaxfieldnames[$key] as $taxfield){
						$taxfield['transaction_id'] = $insert_id;
						$taxfield['transaction_item_row_id'] = $transaction_item_row_id;
						$itax_id=$callConfig->insertRecord($fieldarray[$insert_table]['tax'],$taxfield);
						$res['tax'][] = $taxfield;
						$res['tax_res'][] = $itax_id;
					}
				}
			}
		}
		return $res;
	}
	function togetpcdata($companyid,$ownerid){
        global $callConfig; //echo "<pre>";
        $whr1 = "company_id = '?' AND owner_id='?' AND is_active = 0";	
        $params = [$whr1, $companyid, $ownerid];
        $query1=$callConfig->selectQuery(TBL_PETTY_CASH_BOX_OWNER,'id,authorized_expenses,pc_limit,pc_box_id',$whr1,'','','');
		//echo $query1; exit;
        $data1 = $callConfig->getAllRowsNew($query1);
       	//print_r($data1); exit;
       // $dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
        $returns = array();
        foreach ($data1 as $detail1) {
        	$return = array();
        	$return ['id'] = $detail1->id;
        	$return ['pc_limit'] = $detail1->pc_limit;
        	$whr2 = "company_id = '?' AND id='?' AND is_active = 0";	
        	$params = [$whr2, $companyid, $detail1->authorized_expenses];
        	$query2 =$callConfig->selectQuery(TBL_PETTY_CASH_EXPENSES,'id,expense_name,description,gl_account',$whr2,'','','');
			//echo $query2; exit;
        	$data2 = $callConfig->getRowNew($query2);
        	$whr3 = "company_id = '?' AND id='?' AND is_active = 0";	
        	$params = [$whr3, $companyid, $detail1->pc_box_id];
        	$query3 =$callConfig->selectQuery(TBL_PETTY_CASH_BOXES,'id,topup_frequency,topup_start_date',$whr3,'','','');
			//echo $query3; exit;
        	$data3 = $callConfig->getRowNew($query3);

        	$dates = $this->toGetFrequencyDate($data3->topup_start_date, $data3->topup_frequency);
        	$return ['expenss_id'] = $data2->id;
        	$return ['expense_name'] = $data2->expense_name;
        	$return ['expenss_description'] = $data2->description;
        	$return ['expense_gl_account'] = $data2->gl_account;
        	$return ['consume'] = 8500;
        	$return ['available'] = $return ['pc_limit'] - $return ['consume'];
        	$return ['republish_date'] = date("d-m-Y",strtotime('2020-05-26'));
        	$return ['expense_gl_account'] = $data2->gl_account;
        	$returns[] = $return;
        }  
        return $returns;
    }
    function toGetFrequencyDate($fdate, $frequency){
    	global $callConfig; 
    	$days = 30/$frequency;
    	//echo $days;
    	$dates = explode("-", $fdate);
    	//print_r($dates);
    	$f_date = $dates['2'];
    	$new_date = date('d');
    	$new_month = date('m');
    	$new_year = date('Y');
    	for($i =0; $i<$frequency; $i++){

    		if($f_date >31){
    			$f_date =  $f_date;
    			$new_month = $new_month+1;
    			if($new_month > 12){
    				$new_month =1;
    				$new_year = $new_year+1;
    			}
    		}
    		$date[$i]['from'] =$new_year."-?-".$f_date;
    		$params = [$date, $i, $new_year, $new_month, $f_date];
    		$f_date = $f_date+$days;
    		if($f_date >31){
    			$f_date =  $f_date;
    			$new_month = $new_month+1;
    			if($new_month > 12){
    				$new_month =1;
    				$new_year = $new_year+1;
    			}
    		}
    		$date[$i]['to'] =$new_year."-?-".$f_date;
    		$params = [$date, $i, $new_year, $new_month, $f_date];
    		if((strtotime($date[$i]['from']) < strtotime(date("Y-m-d")) )&&( strtotime(date("Y-m-d")) < strtotime($date[$i]['to']))){
    			$return = $date[$i];
    		}
    		//echo $f_date;

    	}
    	//print_r($date);
    	//echo "F date:".$fdate."###".$frequency; exit;
    	return $return;
    }
    function insertBills($post,$type){
    	global $callConfig; 
    	//echo "<pre>"; print_r($post); exit;
    	//echo $post['invoiced_at'];
    	$table = 'bills';
    	$bill_items = 'bill_items';
    	$bill_item_taxes= 'bill_item_taxes';
    	if($post['old_place'] == $post['place_of_supply']){ 
    		$tax_flag = "S";
    	}else{
    		$tax_flag = "I";
    	}
    	$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
    	$status_main = $status;
    	if($type == 'update' ){
    		$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value']);
    		$status = $this->updateapprovallog($sarray);
			//print_r($sarray); exit;
    	} 	
    	$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
    	$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
    	$doc_ref_date = str_replace('/"', '-', $post['doc_ref_date']);
    	$doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
    	$due_date = str_replace('/"', '-', $post['due_date']);
    	$due_date =  date('Y-m-d', strtotime($due_date)); 
    	$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'payment_method'=>$post['payment_method'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$doc_ref_date,'due_date'=>$due_date);
    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	if($type == 'update'){
    		$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
    		$itemids = explode(',', $post['hid_itemds']);
    		$insert_id = $post['hid'];
    	}else{
    		$transation = $this->toGetApplicationSerialNumber('bills');
    		$fieldnames['transaction_number'] = $transation;
    		
    		$insert_data=$callConfig->insertRecord('bills',$fieldnames);
    		$insert_id = $insert_data['LASTID'];
			//$insert_id =2;
    	}

    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>'COM_INSERT_BILLS' ,'Rid'=>$insert_id,'email_template'=>3,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>'','category_type'=>'','cost_center'=>$post['cost_center']);

		 //echo "<pre>"; print_r($insert_data); //exit;
    	if($insert_id > 0){
			//echo "data"; exit;
    		foreach ($post['productCode'] as $key => $item) {
				//echo $item; exit;
    			$litemids[] = $item;
    			$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'req_date'=>$post['req_date'][$key]);
			 	//print_r($itemfieldnames); exit;
    			$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['tax'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key]);
    			if($type == 'update'){
    				$where = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
    				$params = [$where, $insert_id, $item];
    				$itemquery=$callConfig->selectQuery($bill_items,'*',$where,'','','');
    				$data = $callConfig->getRowNew($itemquery);
    				if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] ){
    					if($data->id !=''){
    						$res=$callConfig->updateRecord($bill_items,$itemfieldnames,'id',$data->id);	
    					}else{
    						$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
    					}
    				}
    				$where = "transaction_id = '?' AND transaction_item_id	= '?' AND is_active = 0";
    				$params = [$where, $insert_id, $item];
    				$itemquery=$callConfig->selectQuery($bill_item_taxes,'*',$where,'','','');
    				$data_tax = $callConfig->getRowNew($itemquery);
    				if(count((array)$data_tax) > 0){
    					if($data->tax_amount != $itemfieldnames['tax_amount']){
    						$where = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
    						$params = [$where, $insert_id, $item];
    						$res=$callConfig->updateRecord($bill_item_taxes,$taxfield,'','',$where);
    					}
    				}else{
    					$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
    				}

    			}else{
    				$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
    				if($post['tax'][$key]!=0){
    					$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
    				}
				 	 //print_r($item_id);  print_r($itax_id); 
    			}
    		}
    		if($type == 'update' ){
    			$diffid = array_diff($itemids,$litemids);
    			if(count((array)$diffid) > 0){
    				foreach ($diffid as  $value_id) {
    					$taxfieldde=array('is_active'=>1);
    					$wheredd = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
    					$params = [$wheredd, $insert_id, $value_id];
    					$wheredd1 = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
    					$params = [$wheredd1, $insert_id, $value_id];
    					$res=$callConfig->updateRecord($bill_items,$taxfieldde,'','',$wheredd);
    					$res=$callConfig->updateRecord($bill_item_taxes,$taxfieldde,'','',$wheredd1);
    				}
    			}
    			if($status > ST_DRAFT){
		 		//echo "data"; exit;
    				$res =$this->toSendEmailNotificationsBaseDAO($email_data);
			 	}//print_r($itemids); exit;
			 	if($status != ST_CANCEL && $status != ST_DRAFT){
			 		$iu = $this->insertandupdatedataapplicationtag($fieldnames,$insert_id,'COM_INSERT_BILLS');
			 	} 	
			 }
		} //exit;
		return $insert_data;
    }

    function insertPC($post,$type){
    	global $callConfig; 
    	//echo "<pre>"; print_r($post); exit;
    	//echo $post['invoiced_at'];
    	$table = 'petty_cash';
    	$bill_items = 'pc_items';
    	$bill_item_taxes= 'pc_item_taxes';
    	if($post['old_place'] == $post['place_of_supply']){ 
    		$tax_flag = "S";
    	}else{
    		$tax_flag = "I";
    	}
    	$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
    	$status_main = $status;
    	if($type == 'update' ){
    		$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$post['grandtotal'],'status_old' => $post['status_old_value']);
    		$status = $this->updateapprovallog($sarray);
			//print_r($sarray); exit;
    	} 	
    	$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
    	$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
    	$doc_ref_date = str_replace('/"', '-', $post['doc_ref_date']);
    	$doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
    	$due_date = str_replace('/"', '-', $post['due_date']);
    	$due_date =  date('Y-m-d', strtotime($due_date)); 
    	$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'currency_id'=>$post['currency_id'],'currency_rate'=>$post['currency_rate'],'party_id'=>$post['party_id'],'party_name'=>$post['party_name'],'party_email'=>$post['party_email'],'party_tax_number'=>$post['party_tax_number'],'party_phone'=>$post['party_phone'],'party_address'=>$post['party_address'],'party_ship_adderss'=>$post['party_ship_adderss'],'place_of_supply'=>$post['place_of_supply'],'payment_terms'=>$post['payment_terms'],'notes'=>$post['notes'],'amount'=>$post['subTotal'],'tax_amount'=>$post['taxRate'], 'discount_type'=>$post['discount_type'],'discount_value'=>$post['discount_value'],'discount_amount'=>$post['discount_amount'], 'total_amount'=>$post['grandtotal'], 'current_status'=> $status, 'ref_id'=>$post['ref_id'], 'ref_no'=>$post['ref_no'], 'ref_date'=>date("Y-m-d",strtotime($post['ref_date'])),'ref_type'=>$post['ref_date'],'originator'=>$post['originator'],'printing_notes'=>$post['printing_notes'],'type'=>$post['type'],'tax_type'=>$post['tax_type'],'shipping_tax'=>$post['shipping_tax'],'shipping_amount'=>$post['shipping_amount'],'shipping_tax_amount'=>$post['shipping_tax_amount'],'printing_notes'=>$post['printing_notes'],'tax_state'=>$tax_flag,'opex_cpex'=>$post['opex_cpex'],'payment_method'=>$post['payment_method'],'due_date'=>$due_date);
    	//echo "<pre>"; print_r($fieldnames); exit;
    	$post['cost_center'] = $post['party_id'];
    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	if($type == 'update'){
    		$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
    		$itemids = explode(',', $post['hid_itemds']);
    		$insert_id = $post['hid'];
    	}else{
    		$transation = $this->toGetApplicationSerialNumber($table);
    		$fieldnames['transaction_number'] = $transation;
    		
    		$insert_data=$callConfig->insertRecord($table,$fieldnames);
		 	//echo "<pre>"; print_r($insert_data); exit;
    		$insert_id = $insert_data['LASTID'];
			//$insert_id =2;
    	}
    	$fieldnames['cost_center_id'] = $post['cost_center'];
    	$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>'COM_INSERT_PETTY_CASH' ,'Rid'=>$insert_id,'email_template'=>3,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>'','category_type'=>'','cost_center'=>$post['cost_center']);

		 //echo "<pre>"; print_r($email_data); exit;
    	if($insert_id > 0){
			//echo "data"; exit;
    		foreach ($post['productCode'] as $key => $item) {
				//echo $item; exit;
    			$doc_ref_date = $post['doc_ref_date'][$key];
    			if($doc_ref_date !=''){
    				$doc_ref_date = str_replace('/"', '-',$doc_ref_date);
    				$doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
    			}
    			$litemids[] = $item;
    			$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$item,'item_name'=>$post['item_name'][$key],'description'=>$post['productName'][$key],'purchase_uom'=>$post['uom'][$key],'item_quantity'=>$post['quantity'][$key],'item_price'=>$post['price'][$key],'item_amount'=>$post['amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount'=>$post['amount'][$key],'discount_amount'=>$post['pdiscount_amount'][$key],'doc_ref_no'=>$post['doc_ref_no'][$key],'doc_ref_date'=>$doc_ref_date,'account_code'=>$post['account_code'][$key]);
			 	//echo "<pre>"; print_r($itemfieldnames); exit;
    			$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$item,'tax_id'=>$post['tax'][$key],'amount'=>$post['p_amount'][$key],'tax_amount'=>$post['ptax_amount'][$key],'total_amount' =>$post['amount'][$key],'tax_type' =>$tax_flag, 'tax_value'=>$post['tax'][$key]);
    			if($type == 'update'){
    				$where = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
    				$params = [$where, $insert_id, $item];
    				$itemquery=$callConfig->selectQuery($bill_items,'*',$where,'','','');
    				$data = $callConfig->getRowNew($itemquery);
    				if($data->description != $itemfieldnames['description'] || $data->purchase_uom != $itemfieldnames['purchase_uom']  || $data->item_quantity != $itemfieldnames['item_quantity'] || $data->item_price != $itemfieldnames['item_price'] ){
    					if($data->id !=''){
    						$res=$callConfig->updateRecord($bill_items,$itemfieldnames,'id',$data->id);	
    					}else{
    						$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
    					}
    				}
    				$where = "transaction_id = '?' AND transaction_item_id	= '?' AND is_active = 0";
    				$params = [$where, $insert_id, $item];
    				$itemquery=$callConfig->selectQuery($bill_item_taxes,'*',$where,'','','');
    				$data_tax = $callConfig->getRowNew($itemquery);
    				if(count((array)$data_tax) > 0){
    					if($data->tax_amount != $itemfieldnames['tax_amount']){
    						$where = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
    						$params = [$where, $insert_id, $item];
    						$res=$callConfig->updateRecord($bill_item_taxes,$taxfield,'','',$where);
    					}
    				}else{
    					$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
    				}

    			}else{
    				$item_id=$callConfig->insertRecord($bill_items,$itemfieldnames);
    				if($post['tax'][$key]!=0){
    					$itax_id=$callConfig->insertRecord($bill_item_taxes,$taxfield);
    				}
				 	 //echo "<pre>";print_r($item_id);  print_r($itax_id); exit;
    			}
    		}
    		if($type == 'update' ){
    			$diffid = array_diff($itemids,$litemids);
    			if(count((array)$diffid) > 0){
    				foreach ($diffid as  $value_id) {
    					$taxfieldde=array('is_active'=>1);
    					$wheredd = "transaction_id = '?' AND item_id = '?' AND is_active = 0";
    					$params = [$wheredd, $insert_id, $value_id];
    					$wheredd1 = "transaction_id = '?' AND transaction_item_id = '?' AND is_active = 0";
    					$params = [$wheredd1, $insert_id, $value_id];
    					$res=$callConfig->updateRecord($bill_items,$taxfieldde,'','',$wheredd);
    					$res=$callConfig->updateRecord($bill_item_taxes,$taxfieldde,'','',$wheredd1);
    				}
    			}
    			if($status > ST_DRAFT){
		 		//echo "data"; exit;
    				$res =$this->toSendEmailNotificationsBaseDAO($email_data);
			 	}//print_r($itemids); exit;
			 	if($status != ST_CANCEL && $status != ST_DRAFT){
			 		$iu = $this->insertandupdatedataapplicationtag($fieldnames,$insert_id,'COM_INSERT_PETTY_CASH');
			 	} 	
			 }
		} //exit;
		return $insert_data;
    }



    function getApprovalLogData($data){
    	global $callConfig;
    	//echo "<pre>"; print_r($data); exit;
    	$table = $_GET['table'];
    	$whr = "user_id = '?' AND response_type = '0' AND send_back_flag = 0";	
    	$params = [$whr, $data];
    	$query = $callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'DISTINCT(transaction_table) as button',$whr,'','','');
		//echo $query; exit;
    	$data1 = $callConfig->getAllRowsNew($query);
       	//print_r($data); echo $data[0]->button;exit;
    	$return['button'] = $data1;
    	if(count((array)$data) >0){
    		$table = $table!='' ? $table : $data1[0]->button;
    		$return['table'] = $table;
    		$whr2 = "user_id = '?' AND response_type = '0' AND send_back_flag = 0 AND transaction_table ='?'";	
    		$params = [$whr2, $data, $table];

    		$query2 = $callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$whr2,'','','');
			//echo $query2; exit;
			//echo $query; exit;
    		$data2 = $callConfig->getAllRowsNew($query2);
	       	//print_r($data2); exit;
    		foreach ($data2 as  $dat) {
    			$tran_ids[] = $dat->transaction_id;
    		}
    		$tran_id = implode(',', (array)$tran_ids);
	       	//echo $tran_id ; exit;
    		$fields = "id,transaction_number, transaction_date,  amount, discount_amount, shipping_amount, tax_amount";
    		if($table == 'bills'){
    			$fields.=",party_name";
    		}else{
    			$fields.=",party_name as party_name";
    		}
    		$query3 = "SELECT ? from ? WHERE id in (?)";
    		$params = [$query3, $fields, $table, $tran_id];
	       	//echo $query3; exit;
    		$data3 = $callConfig->getAllRowsNew($query3);
	       	//echo "<pre>";print_r($data3); exit;
    	}
    	$return['data'] = $data3;
    	return $return;
    }
    function getchartofaccountdetailsuseglaccountuser($data){
    	global $callConfig;
    	$query = "select coa.* from chart_of_accounts coa, gl_account_users gau where coa.company_id = gau.company_id and coa.account_code = gau.account_code and  gau.company_id =?  and gau.user_id = ".$data['user_id'];
    	$params = [$query, $data, $data];
    	$data = $callConfig->getAllRowsNew($query);
    	return $data;
    }
    function pettycashdetails($data){
    	global $callConfig;
    	$query = "select * From petty_cash_expenses where id in ( select authorized_expenses from petty_cash_box_owner where owner_id =?)";
    	$params = [$query, $data];
    	$data = $callConfig->getAllRowsNew($query);
    	return $data;
    }
    function budgetcheckreftadledata($data){
    	global $callConfig;
    	//echo"<pre>"; print_r($data); exit;
    	$str = '';
    	foreach ($data['account_code'] as  $code) {
    		$str.="'?',";
    		$params = [$str, $code];
    	}
    	$str = trim($str,",");
    	$query= "Select * from budget_balances where company_id=? AND cost_center_id=? AND fiscal_year='". FISCALYEAR."' AND account_code in (?)";
    	$params = [$query, $data, $data, $str];
    	//echo $query;
    	$data1 = $callConfig->getAllRowsNew($query);
    	//echo"<pre>"; print_r($data1); exit; 
    	//echo $data['fiscal_year'];

    	$fy = explode('-', $data['fiscal_year']);
    	$array = array('01'=> '10','02'=> '11','03'=> '12','04'=> '1','05'=> '2','06'=> '3','07'=> '4','08'=> '5','09'=> '6','10'=> '7','11'=> '8','12'=> '9');
    	//print_r($fy); exit;

    	$month = $array[$fy[1]];
    	$val = 'distribution_m'.$month;
    	$val2 = 'rq_m'.$month;

		//echo $val."++".$val2."dafaefav".$data['fiscal_year'];print_r($fy);	 exit;
    	$return = array();
		//echo"<pre>"; print_r($data1); exit;
    	foreach ($data1 as  $cudata) {
    		//echo $val.'%%%'.$val2;
    		$response = array();
    		//print_r($cudata);
    		$response ['a'] = $cudata->$val - $cudata->$val2;
    		$response ['b'] = $cudata->$val;
    		$response ['c'] = $cudata->$val2;
    		$return[$cudata->account_code] = $response ;
    	}
    	//echo"<pre>"; print_r($return); exit; 
    	return $return;
    }
    function toGetBluckApprovaldata($post){
    	global $callConfig;
    	//echo "<pre>"; print_r($data);exit;
    	$whr1 = "company_id = ? AND is_active = 0";	
    	$params = [$whr1, $post];
    	$query1=$callConfig->selectQuery(TBL_BULK_APPROVAL_BASE,'id,function_name,description',$whr1,'','','');
		//echo $query1; exit;
    	$approval_tables = $callConfig->getAllRowsNew($query1);

    	$approval_data = array();$data = array();$return = array(); $gcdata = array();
    	$str = '';
    	foreach ($approval_tables as $approval_table) {
    		$data[$approval_table->function_name]['table']=$approval_table->function_name;
    		$data[$approval_table->function_name]['description']=$approval_table->description; 
    		$data[$approval_table->function_name]['count']=10;
    		$str.= "'?',";
    		$params = [$str, $approval_table->function_name];
    	}
    	$return['data'] = $data;
    	$str = trim($str,",");
    	$query2="select stp.* from status st , status_progress stp where st.name_value = stp.status and stp.table_name in (? ) order by stp.table_name, st.sequence";
    	$params = [$query2, $str];
			//echo $query2; exit;
    	$status_progress = $callConfig->getAllRowsNew($query2);
    	$i=0;
    	foreach ($status_progress as  $status_progres) {
			//$status_prog  =  array();
    		$status_prog[$status_progres->table_name][$i]['status_progress_id'] = $status_progres->id;
    		$status_prog[$status_progres->table_name][$i]['status_name'] = $status_progres->name;
    		$status_prog[$status_progres->table_name][$i]['status_action_label'] = $status_progres->action_label;
    		$status_prog[$status_progres->table_name][$i]['status_status_id'] = $status_progres->status;
    		$i++;
    	}
    	$return['status'] = $status_prog;
    	$whr3 = "company_id = ? AND genc_code='TRANSACTIONTYPE' AND is_active = 0";	
    	$params = [$whr3, $post];
    	$query3=$callConfig->selectQuery(TBL_GENRAL_CODE_DETAILS,'*',$whr3,'','','');
		//echo $query3; exit;
    	$gc_data = $callConfig->getAllRowsNew($query3);
    	foreach ($gc_data as $gc) {
    		$gcdata[$gc->genc_detail_code] = $gc->description;
    	}
    	$return['genc_code'] = $gcdata;
    	$query4 = "SELECT  bat.transaction_type, `current_status`, count(*) as count1 FROM bulk_approval_transactions bat, transaction_approval_logs tal where  bat.company_id = tal.company_id and   bat.transaction_type = 
    	tal.transaction_table and  bat.transaction_id = tal.transaction_id  and  tal.user_id =? AND tal.response_type = 0 group by bat.transaction_type, bat.current_status";
    	$params = [$post];
		//echo $query4; exit;
    	$approval_count = $callConfig->getAllRowsNew($query4);
    	foreach ($approval_count as $count) {
    		$approval_count_data [$count->transaction_type] [$count->current_status] = $count->count1;
    	}
    	$return['count'] = $approval_count_data;	
    	$query4=$callConfig->selectQuery(TBL_USERS,'id,display_name,user_photo','','','','');
		//echo $query3; exit;
    	$user_data = $callConfig->getAllRowsNew($query4);
    	foreach ($user_data as  $user) {
    		$users[$user->id]['name'] = $user->display_name;
    		$users[$user->id]['photo'] = $user->user_photo;
    	}
    	$return['user'] = $users;
		//echo "<pre>"; print_r($approval_count_data); exit;
    	return $return;
    }
    function toGetBluckStatusData($data){
    	global $callConfig;
    	//echo "<pre>"; print_r($data);
    	$query2="SELECT bat.id, bat.company_id, bat.transaction_type, bat.transaction_id, bat.cost_center_id, bat.transaction_number, bat.transaction_date, bat.currency_id, bat.currency_rate, bat.originator, bat.party_id, bat.party_name, bat.party_email, bat.party_tax_number, bat.party_phone, bat.party_address, bat.party_ship_adderss, bat.place_of_supply, bat.payment_terms, bat.notes, bat.printing_notes, bat.parent_id, bat.amount, bat.tax_amount, bat.type, bat.tax_type, bat.discount_type, bat.discount_value, bat.discount_amount, bat.total_amount, bat.shipping_tax, bat.shipping_amount, bat.shipping_tax_amount, bat.tax_state, bat.opex_cpex, bat.ref_type, bat.ref_id, bat.ref_no, bat.ref_date, bat.old_ref_no, bat.old_ref_date, bat.current_status, tal.id, tal.`response_type`, tal.`user_id`, tal.`request_date`, tal.`send_back_flag`, tal.`response_date`, tal.`comments`, tal.`doa_id` FROM bulk_approval_transactions bat, transaction_approval_logs tal where bat.company_id = tal.company_id and    bat.transaction_type = tal.transaction_table and bat.transaction_id = tal.transaction_id and bat.current_status='?' and bat.transaction_type ='?' and  tal.user_id=? AND tal.response_type = 0" ;
    	$params = [$query2, $data, $data, $data];
		//echo $query2; exit;
    	$data = $callConfig->getAllRowsNew($query2);
    	return $data;
    }
    function upadateblukapproval($data){
    	global $callConfig;
    	$ids = trim($data['rid'],',');
    	$table = $data['table'];
    	$status = $data['status'];
    	$ids = explode(",", $ids);
    	//print_r($ids); exit;
    	foreach ($ids as $id) {
    		$array['table_name'] = $table; 
    		$array['status'] = $status; 
    		$array['hid'] = $id; 
    		$array['comments'] = '';
    		$array['user_id'] = $data['user_id'];
    		$array['company_id'] = $data['company_id'];
    		//print_r($array); exit;
    		$res = $this->upadteablukprrovals($array); 
    	}
    	return $res;
    }
    function upadteablukprrovals($post){
    	global $callConfig;
    	$where = "id = '?'";
    	$params = [$where, $post];
    	$itemquery=$callConfig->selectQuery($post['table_name'],'*',$where,'','','');
    	$data = $callConfig->getRowNew($itemquery);
    	//$status_main = $data->current_status;
    	$sarray  = array('comments' => $post['comments'], 'response_type' => $post['status'], 'user_id' => $post['user_id'], 'response_date' => date("Y-m-d"),'table' => $post['table_name'],'hid' => $post['hid'],'amount'=>$data->total_amount,'status_old' => $data->current_status);
    	$status = $this->updateapprovallog($sarray);
		//print_r($sarray); echo $post['table_name']; exit;
    	$suc_data=$callConfig->updateRecord($post['table_name'],array('current_status' => $status),'id',$post['hid']);
    	$wheredd1 = "transaction_id = '?' AND transaction_type = '?'";
    	$params = [$wheredd1, $post, $post];
    	$res=$callConfig->updateRecord('bulk_approval_transactions',array('current_status' => $status),'','',$wheredd);
    	$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$data->transaction_number,'transaction_type'=>$post['table_name'], 'transaction_date'=>$data->transaction_date, 'amount'=>$data->amount, 'tax_amount'=>$data->tax_amount , 'discount_amount'=>$data->discount_amount, 'total_amount'=>$data->total_amount ,'action_label'=>'Submit for Approval', 'option'=>'COM_LIST_BLUK_APPROVAL_TRANSACTIONS' ,'Rid'=>$data->id,'email_template'=>3,'status_old_value'=>$data->current_status,'status'=>$post['status'],'updated_by'=>$post['user_id'],'originator'=>$data->originator,'opex_cpex'=>$data->opex_cpex,'category_type'=>$data->type,'cost_center'=>$data->cost_center_id);
    	//print_r($email_data); exit;
    	if($status > ST_DRAFT){
		 	//echo "data"; exit;
    		$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		}//print_r($itemids); exit;]
		return $res;
	}
	function addAndUpdateBulkApprovalTransaction($data , $type){
		global $callConfig;
		
		if($type == 'insert'){
			$return = $callConfig->insertRecord('bulk_approval_transactions',$data);
		}else{ 			
			$wheredd = "transaction_id = '?' AND transaction_type = '?'"; 
			$params = [$wheredd, $data, $data];
			$return = $callConfig->updateRecord('bulk_approval_transactions',$data,'','',$wheredd);
		}
		return $return;
	}
	function toGetPettyCashSummary($post){
		global $callConfig;
		//echo "ravi"; exit;
		$query = "SELECT po.`company_id`, po.`pc_box_id`, po.`owner_id`, po.`pc_type_id`, po.`currency_id`, po.`pc_limit`, po.`assigner_id`, po.`authorizer_id`, po.`authorized_expenses`, po.`gl_account_assignment`, po.`gl_account_pc_cash`, po.`tax_group_id`, po.`is_active`, pc.expense_name FROM petty_cash_box_owner po , petty_cash_expenses pc
		WHERE po.`company_id` =  pc.company_id and po.`authorized_expenses` = pc.id  and po.`owner_id` = ? and po.`company_id` =  ?  and po.is_active = pc. is_active and po.is_active = 0";
		$params = [$post, $post];
		//$query = "SELECT `company_id`, `pc_box_id`, `owner_id`, `pc_type_id`, `currency_id`, `pc_limit`, `assigner_id`, `authorizer_id`, `authorized_expenses`, `gl_account_assignment`, `gl_account_pc_cash`, `tax_group_id`, `is_active` FROM `petty_cash_box_owner` WHERE `company_id` =  ".$post['company_id']." and `owner_id` = ".$post['user_id']." and is_active = 0";
		$data1 = $callConfig->getAllRowsNew($query);
		//echo "<pre>"; print_r($data1); exit;
		$query2 = "select pc.id,pc.transaction_number, pc.transaction_date, pci.item_id, pci.item_name, pci.item_amount from petty_cash pc, pc_items pci where pc.company_id = pci.company_id and pc.id = pci.transaction_id and pc.company_id = ?  and pc.originator = ".$post['user_id'];
		$params = [$query2, $post, $post];
		$data2 = $callConfig->getAllRowsNew($query2);
		//echo "<pre>"; print_r($data2); exit;
		$expense_data = array();  $ex_data = array();
		foreach ($data1 as $owner) {
			$expense = array();
			$expense['id'] = $owner->authorized_expenses;
			$expense['pc_limit'] = $owner->pc_limit;
			$expense['name'] = $owner->expense_name;
			$expense_data[] = $expense;
			//$ex_data[$owner->authorized_expenses] = array();
		}
		foreach ($data2 as $value) {

			$ex_data[$value->id][$value->item_id] = $value->item_amount;
			$message[$value->id] ['tr_no']= $value->transaction_number;
			$message[$value->id] ['tr_date']= $value->transaction_date;
			$message[$value->id] ['total']= 0;

			//$sum_arry[$value->item_id] = 0;
		}
		foreach ($ex_data as $key=>$exx) { //echo $key; //print_r($exx);
			$ex_de = array();
			$total = 0;
			foreach ($expense_data as  $ex) {
				
				$val = $exx[$ex['id']] !="" ? $exx[$ex['id']] : "0.00";
				$ex_de[$ex['id']]= $val;

				$sum_arry[$ex['id']] = $sum_arry[$ex['id']]+$val;
				$total = $total+$val;
			}	
			$message[$key] ['total']= $total;
			//echo"<pre>"; print_r($ex_de);
			$ex_detal[$key] = $ex_de;
		}

		//echo "<pre>"; print_r($expense_data);print_r($ex_detal);print_r($message);print_r($sum_arry); exit;
		$return = array();
		$return['header'] = $expense_data;
		$return['data'] = $ex_detal;
		$return['message'] = $message;
		$return['sum'] = $sum_arry;
		return $return;
	}
	function querynotificationandemail($data){
		global $callConfig;
		//print_r($data); exit;
		if($data['new_reply'] == 'N'){
			$notarry['notification_message'] = ucwords(str_replace('_', ' ',$data['table_name']))." ?: ".ucwords("query is raised") ;
			$params = [$notarry, $data, $data];
		}else{
			$notarry['notification_message'] = ucwords(str_replace('_', ' ',$data['table_name']))." ?: ".ucwords("query is replied") ;
			$params = [$notarry, $data, $data];
		}
		$notarry['notification_users'] = "1";
		$notarry['id'] = $data['transaction_id'];
		$notarry['option'] = $data['option'];
		$notarry['notification_datetime']=date("Y-m-d H:i:s");
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('company_id'=>$data['company_id'],'notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'transaction_number'=>$data['transaction_id'],'application_table_name'=>'transaction_queries');
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		$revalue = 'ACTION';
		$nti_use_fieldnames=array('company_id'=>$data['company_id'],'user_id'=>$data['send_id'],'notification_id'=>$notification_id,'notification_type'=>$revalue);
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		$nti_use_fieldnames=array('company_id'=>$data['company_id'],'user_id'=>$data['user_id'],'notification_id'=>$notification_id,'notification_type'=>$revalue);
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		$data['email_template'] =  1;
		$data['email_content_id'] = 25;
		$data['transaction_type'] = $data['table_name'];
		$data['transaction_number'] = $data['trno'];
		$email_array = $this->emailtemplate($data);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$params = [$file_name];
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		//fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		return $res;
	}
	
	function multiplefileupload($post){
		global $callConfig;
		foreach ($post as $value) {
			$res=$callConfig->insertRecord(TBL_APPLICATION_ATTACHMENTS,$value);
		}
		return $res;
	}
	function togetuserpettycashboxdetails($post){
		global $callConfig;
		$query = "SELECT * FROM petty_cash_boxes WHERE id IN(SELECT DISTINCT(`pc_box_id`) FROM `petty_cash_box_owner` WHERE `owner_id` = ?)";
		$params = [$query, $post];
		$data = $callConfig->getAllRowsNew($query);
        //print_r($data); exit;
		return $data;
	}
	function pettycashbranchamount($post){
		global $callConfig;
		$whrs="pc_box_id= '?' AND owner_id = '?' AND pc_type_id='?'";
		$params = [$whrs, $post, $post, $post];
		//echo $whrs;
		$query=$callConfig->selectQuery('petty_cash_box_owner','*',$whrs,'','','');
		$data = $callConfig->getRowNew($query);
		$return = $data->pc_limit;
		$whr1="id= '?'";
		$params = [$whr1, $post];
		$query1=$callConfig->selectQuery('petty_cash_boxes','*',$whr1,'','','');
		$data1 = $callConfig->getRowNew($query1);
		if($return == ""){
			$return = $data1->topup_amount;
		}
		//print_r($data);
		if($data->topup_type == 2){
			$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
	       	//print_r($dates); exit;
			$query3 = "SELECT sum(pc.amount) as tatal FROM petty_cash pc WHERE  pc.transaction_date BETWEEN '?'' and  '?'' and   pc.originator =  ? and   pc.branch_code =".$post['branch_id'];
			$params = [$query3, $dates, $dates, $post, $post];
			//echo $query3; exit;
			$data3 = $callConfig->getRowNew($query3);
			if($data3->tatal == ""){
				$return = $return;
			}else{
				$return = $data3->tatal;
			}
		}
		return $return;
	}
	function getpettycashbuget($post){
		global $callConfig;
		//print_r($post);
		$whr1="id= '?'";
		$params = [$whr1, $post];
		$query1="SELECT * from  petty_cash_boxes where id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =? and `authorized_expenses` =?)";
		$params = [$query1, $post, $post];
		$data1 = $callConfig->getRowNew($query1);
		$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$query = "SELECT pcbo.company_id, pcbo.pc_box_id,  pcbo.pc_type_id,  pcbo.pc_limit, pcbo.authorized_expenses, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id = ? and  pc.transaction_date between '?' and '?') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =? AND  pcbo.pc_box_id = ? AND  pcbo.owner_id = ? and pcbo.authorized_expenses=".$post['branch_id'];
		$params = [$query, $post, $dates, $dates, $post, $data1->id, $post, $post];
		//echo $query; exit;
		$data3 = $callConfig->getRowNew($query);
		return $data3;
	}
	function dailypettycashformonth($post){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 180 day"));
		$day = 180;
		//print_r($dates); exit;	
		for ($i=0 ; $i<6 ; $i++) {
			$day = $day-30;
			$to =  date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - ? day"));
			$params = [$to, $day];
			$query = "SELECT pcbo.company_id,  sum(pcbo.pc_limit) pc_limit, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id   and  pc.transaction_date between '?' and '?') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =? AND  pcbo.from_date <= '?' AND  pcbo.to_date >= '?'  AND  pcbo.owner_id = ".$post['user_id'];
			$params = [$query, $from, $to, $post, $from, $from, $post];
			//echo $query."<br>"; exit;
			$data3 = $callConfig->getRowNew($query);
			$return[$i]['pc_limit']=  $data3->pc_limit !="" ? $data3->pc_limit : 0;
			$return[$i]['consumed']=  $data3->consumed !="" ? $data3->consumed : 0;
			$return[$i]['list']=  date('M-y',strtotime($to));
			$return[$i]['date']=  $from;
			$from = $to;
		}
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dailypettycashmonth($post){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($dates); exit;
		$to =  date('Y-m-d');
		$query = "SELECT pcbo.company_id,  sum(pcbo.pc_limit) pc_limit, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id   and  pc.transaction_date between '?' and '?') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =? AND  pcbo.owner_id = ".$post['user_id'];
		$params = [$query, $from, $to, $post, $post];
		//echo $query."<br>";
		$data3 = $callConfig->getRowNew($query);
		$return['pc_limit']=  $data3->pc_limit !="" ? $data3->pc_limit : 0;
		$return['consumed']=  $data3->consumed !="" ? $data3->consumed : 0;
		$return['date']=  $from;
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dailypettycashdaydetails(){
		global $callConfig;
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($dates); exit;
		$to =  date('Y-m-d');
		$query = "SELECT sum(pcitm.item_amount) as amount, pcitm.item_id, pcitm.item_name FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id and  pc.transaction_date between '?' and '?' AND pcitm.item_id != ".TOP_UP." GROUP BY pcitm.item_id" ;
		$params = [$query, $from, $to];
		//echo $query."<br>";
		$data3 = $callConfig->getAllRowsNew($query);
		//print_r($data3);
		//echo "<pre>"; print_r($return);
		return $data3; 
	}
	function montlypettycashdetails($post){
		global $callConfig;
		//print_r($post);
		$query1="SELECT  id as pc_box_id, pc_box_code, pc_box_description FROM petty_cash_boxes WHERE id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =? AND `company_id` =? ORDER BY pc_box_id)";
		$params = [$query1, $post, $post];
		//echo $query1; exit;
		$datas1 = $callConfig->getAllRowsNew($query1);
		//print_r($datas1); exit;
		//$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$to = date('Y-m-d');
		$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
		//print_r($datas1); //exit;	

		foreach ($datas1 as $data1) {
			$main ['name'] = $data1->pc_box_code;
			$main ['description'] = $data1->pc_box_description;
			$main ['id'] = $data1->pc_box_id;
			$query = "SELECT sum(pcitm.item_amount) as amount, pcitm.item_id, pcitm.item_name FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id and  pc.transaction_date between '?' and '?' AND pcitm.item_id IN ( SELECT `authorized_expenses`  FROM  petty_cash_box_owner where `owner_id` =? AND `company_id` =? AND pc_box_id = ? ) GROUP BY pcitm.item_id";
			$params = [$query, $from, $to, $post, $post, $data1->pc_box_id];
			//echo $query."<br>"; 
			$data3 = $callConfig->getAllRowsNew($query);
			$i=0;
			$return = array();
			foreach ($data3 as $data31) {
				$query4="SELECT `pc_limit`  FROM  petty_cash_box_owner where `owner_id` =? AND `company_id` =? ORDER BY pc_box_id AND pc_box_id = ? AND authorized_expenses = ".$data31->item_id;
				$params = [$query4, $post, $post, $data1->pc_box_id, $data31->item_id];
				$data4 = $callConfig->getRowNew($query4);
				$return[$i]['pc_limit']=  $data4->pc_limit !="" ? $data4->pc_limit : 0;
				$return[$i]['consumed']=  $data31->amount !="" ? $data31->amount : 0;
				$return[$i]['name']=  $data31->item_id !="-1" ? $data31->item_name : "Other";
				$i++;
			}
			$main ['data']= $return;
			$return_main[] = $main;
		} 
		//echo "<pre>"; print_r($return);
		return $return_main; 
	}
	function dailypettycashdetails($post){
		global $callConfig;
		//print_r($post);
		$whr1="id= '?'";
		$params = [$whr1, $post];
		$query1="SELECT * from  petty_cash_boxes where id IN (SELECT `pc_box_id`  FROM  petty_cash_box_owner where `owner_id` =? ORDER BY id)";
		$params = [$query1, $post];
		//echo $query1; exit;
		$datas1 = $callConfig->getAllRowsNew($query1);
		//print_r($datas1); exit;
		//$dates = $this->toGetFrequencyDate($data1->topup_start_date, $data1->topup_frequency);
		$dates['to'] = date('Y-m-d');
		$dates['from'] = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 365 day"));
		//print_r($dates); exit;	
		foreach ($datas1 as $data1) {
			$query = "SELECT pcbo.company_id, pcbo.pc_box_id,  pcbo.pc_type_id,  pcbo.pc_limit, pcbo.authorized_expenses, ( SELECT sum(pcitm.item_amount) FROM petty_cash pc,  pc_items pcitm WHERE pc.id = pcitm.transaction_id and pc.company_id = pcitm.company_id  and pcitm.item_id  in (SELECT `authorized_expenses`  FROM  petty_cash_box_owner where `owner_id` =? and pc_box_id = ? ) and  pc.transaction_date between '?' and '?') consumed FROM  petty_cash_box_owner pcbo WHERE pcbo.company_id =? AND  pcbo.pc_box_id = ? AND  pcbo.owner_id = ".$post['user_id'];
			$params = [$query, $post, $data1->id, $dates, $dates, $post, $data1->id, $post];
		//echo $query."<br>"; 
			$data3 = $callConfig->getRowNew($query);
			$return[$data1->id]=  $data3;
		}
		//echo "<pre>"; print_r($return);
		return $return; 
	}
	function dasboaradrequisition1details($post){
		global $callConfig;
		$to = date('Y-m-d');
		$month = date('m');
		$year = date('Y');
		if($month > 2){
			$from = $year."-3-1";
			$params = [$from, $year];
		}else{
			$year = $year-1;
			$from = $year."-3-1";
			$params = [$from, $year];
		}
		//echo $from."#".$to;
		$query1="SELECT cost_center_id,transaction_number,total_amount,current_status from requisitions where company_id = '?' AND originator = '?'AND  current_status NOT IN (".ST_DRAFT.",".ST_CANCEL.") AND transaction_date between '?' and '?'" ;
		$params = [$query1, $post, $post, $from, $to];
		//echo $query1; exit;
		$active_only =0;  $active = 0; $pending=0; $approve=0;
		$datas1 = $callConfig->getAllRowsNew($query1);
		foreach ($datas1 as  $data) {
			if($data->current_status == ST_ACTIVE){
				$active_only = $active_only+$data->total_amount;
				//echo "active_only=".$data->total_amount;
			}
			else if($data->current_status == ST_APPROVED || $data->current_status == ST_QUOTATION_GENERATED || $data->current_status == ST_PURCHASE_ORDER_GENRATED  ){
				$approve = $approve+$data->total_amount;
				//echo "approve=".$data->total_amount;
			}else{
				$pending = $approve+$data->total_amount;
				//echo "pending=".$data->total_amount;
			}
			$active = $active+$data->total_amount;
			//echo "<br>";	
		}
		$return['active_only'] = $active_only;
		$return['pending'] = $pending;
		$return['active'] = $active;
		$return['approve'] = $approve;
		$return['budget'] = 400000;
		//print_r($return); exit;
		return $return;
	}
	function dasboaradrequisition1coscenterdetails($post){
		global $callConfig;
		$query12 = "SELECT id,cost_center_name from cost_centers where id in (SELECT `cost_center_id` FROM `user_cost_centers` where user_id = ? AND company_id = ? )";
		$params = [$query12, $post, $post];
		$data2 = $callConfig->getAllRowsNew($query12);
		$to = date('Y-m-d');
		$month = date('m');
		$year = date('Y');
		if($month > 2){
			$from = $year."-03-01";
			$params = [$from, $year];
		}else{
			$year = $year-1;
			$from = $year."-03-01";
			$params = [$from, $year];
		}
		$query1="SELECT rq.cost_center_id,rq.transaction_number,rqit.item_id,rqit.item_name,rqit.description,rqit.account_code,sum(rqit.total_amount) total_amount from requisitions rq, rq_items rqit where rq.id = rqit.transaction_id AND  rq.company_id = '?' AND rq.originator = '?' AND  rq.current_status NOT IN (".ST_DRAFT.",".ST_CANCEL.") AND rq.transaction_date between '?' and '?' GROUP BY rqit.item_id, rq.cost_center_id" ;
		$params = [$query1, $post, $post, $from, $to];
		//echo $query1;
		$data3 = $callConfig->getAllRowsNew($query1);
		$i = 0;
		foreach ($data3 as  $value) {
			$data[$value->cost_center_id][$i] = (array) $value;
			$data[$value->cost_center_id][$i]['budget'] = 100000;	
			$i++;
		}
		foreach ($data2 as  $dat) {
			$return[$dat->id] = (array)	$dat;
			//$return[$dat->id]['nsm'] = "ravi";
			$return[$dat->id]['data'] = $data[$dat->id];
		}
		return $return;
	}
	function togetstatusprogresstables($post){
		global $callConfig;
		$query = "SELECT DISTINCT(table_name) as table_name FROM status_progress WHERE company_id = '?'";
		$params = [$query, $post];
		$data = $callConfig->getAllRowsNew($query);
		//echo "<pre>"; print_r($data);
		return $data;
	}
	function togetbudgetuserdeails($post){
		global $callConfig;
		$query = "SELECT * FROM chart_of_accounts WHERE account_code IN (SELECT account_code FROM gl_account_users WHERE user_id = ?)";
		$params = [$query, $post];
		$data = $callConfig->getAllRowsNew($query);
		foreach ($data as $value) {
			$return [$value->accgro_group_id][] = (array)$value;
			$group_ids[] = $value->accgro_group_id;
		}
		$group_ids = implode(",", (array)$group_ids);
		$query2 = "SELECT id,group_name,parent_group_id,accsec_section_id FROM account_groups WHERE id in(?)";
		$params = [$query2, $group_ids];
		$data2 = $callConfig->getAllRowsNew($query2);
		foreach ($data2 as  $value2) {
			$returns1  = (array)$value2;
			$returns1['details'] = $return[$value2->id];
			$returns[$value2->accsec_section_id][] = $returns1;
			$account_section[] = $value2->accsec_section_id;
		}

		$account_section = implode(",", (array)$account_section);
		$query3 = "SELECT id,section_name,section_description FROM account_sections WHERE id in(?)";
		$params = [$query3, $account_section];
		$data3 = $callConfig->getAllRowsNew($query3);
		foreach ($data3 as  $value3) {
			$main[$value3->id] = (array)$value3;
			$main[$value3->id]['data'] = $returns [$value3->id];
		}
		//echo "<pre>".$account_section; print_r($main);
		return $main;
	}
	function budgetinsert($post){
		global $callConfig;		
		//print_r($post); exit;
		foreach ($post['data'] as $value) {
			$value['company_id'] = $post['company_id'];
			//$value['account_code'] = $post['account_code'];
			$value['budget_version'] = $post['budget_version'];
			$value['budget_date'] = $post['budget_date'];
			$value['cost_center_id'] = $post['cost_center_id'];
			$value['fiscal_year'] = $post['fiscal_year'];
			$value['is_active'] = 0;
			$value['created_by'] = $post['user_id'];
			$value['current_status'] = 0;
			$value['action_if_exceed_yearly'] = $post['action_if_exceed_yearly'];
			$value['action_if_exceed_quaterly'] = $post['action_if_exceed_quaterly'];
			$value['action_if_exceed_halfyearly'] = $post['action_if_exceed_halfyearly'];
			$value['action_if_exceed_month'] = $post['action_if_exceed_month'];
			$data = $this->insertorupdatebudget($value);
		}
	}
	function insertorupdatebudget($post){
		global $callConfig;
		$whr1="company_id= '?' and budget_version='?' and cost_center_id='?' and account_code='?' and fiscal_year='?'";
		$params = [$whr1, $post, $post, $post, $post, $post];
		$query2 = $callConfig->selectQuery('budget','*',$whr1,'','','');
		//echo $query2; exit;
		$data = $callConfig->getRowNew($query2);
		if($data->id !=''){
			$suc_data=$callConfig->updateRecord('budget',$post,'id',$data->id);
		}else{
			$res=$callConfig->insertRecord('budget',$post);
		}
		return $res;
	}
	function pendingQuerieslist($post){
		global $callConfig;
		$query="select id, table_name, transaction_id, parent_count, query_type from ( SELECT tq1.id, tq1.table_name, tq1.query_type, tq1.transaction_id, ( select count(*) from transaction_queries tq2 WHERE tq2.company_id = tq1.company_id AND tq2.parent_query = tq1.id AND tq2.company_id = ? ) parent_count FROM transaction_queries tq1 WHERE originator = ? AND tq1.new_reply = 'N' ) a where parent_count =0";
		$params = [$query, $post, $post];
		//echo $query;
		$data = $callConfig->getAllRowsNew($query);
		//print_r($data);
		$query2 = "SELECT * FROM transaction_approval_logs WHERE company_id = ? AND response_type = 0 AND user_id =? AND send_back_flag = 0";
		$params = [$query2, $post, $post];
		$data2 = $callConfig->getAllRowsNew($query2);
		$return['query']= $data;
		$return['approval']= $data2;
		return $return;
	}
	function addletters($post,$type=''){
		global $callConfig;
		//echo "ravi";
		//echo "<pre>"; print_r($post); exit;
		$oldwhrs="id= '?'";
		$params = [$oldwhrs, $post];
		$old_query=$callConfig->selectQuery('letters','*',$oldwhrs,'','','');
		$old_data = $callConfig->getRowNew($old_query);
		//print_r($old_data); exit;
		$status = $post['status_values'] != '' ? $post['status_values'] : ST_DRAFT ;
		$status_main = $status;
		if($type == 'update' && $status > ST_DRAFT){
			if( $post['status_old_value'] !=  $post['current_status']){
				$sarray  = array('comments' => $post['comments'], 'response_type' => $status, 'user_id' => $post['updated_by'], 'response_date' => date("Y-m-d"),'table' => 'letters','hid' => $post['hid'],'amount'=>0,'status_old' => $post['status_old_value'],'company_id'=>$post['company_id']);
				$status = $this->ToCheckStatusApprovalLog($sarray);
			//print_r($sarray); exit;
			}
		}
		$table = 'letters';
		
		$invoiced_at = str_replace('/"', '-', $post['invoiced_at']);
		$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
		$doc_ref_date = str_replace('/"', '-', $post['ref_date']);
		$doc_ref_date =  date('Y-m-d', strtotime($doc_ref_date));
		$fieldnames=array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_date'=>$invoiced_at,'version_number'=>$post['version_no'],'originator'=> $post['party_id'],'ref_no'=>$post['ref_no'],'ref_date'=>$doc_ref_date,'subject'=>$post['subject'],'address_to'=>$post['address_to'],'content'=>$post['content'], 'current_status'=> $status);
		if($type == 'update'){
			$fieldnames['updated_date'] = $post['updated_date'];
			$fieldnames['updated_by'] = $post['updated_by'];
			if($old_data->content != $post['content']){
				//echo "change"; exit;
				$dd = $this->dumpaddletters($post);
				$fieldnames['version_number'] = $post['version_no']+0.01;
			}
			//echo "<pre>";print_r($fieldnames); exit;
			$insert_data=$callConfig->updateRecord($table,$fieldnames,'id',$post['hid']);
			$itemids = explode(',', $post['hid_itemds']);
			$insert_id = $post['hid'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'update');
		}else{
			$fieldnames['created_by'] = $post['created_by'];
			$fieldnames['created_date'] = $post['created_date'];
			//echo "<pre>";print_r($fieldnames); exit;
			$transation = $this->toGetApplicationSerialNumber($table);
			$fieldnames['transaction_number'] = $transation;

		 	//echo "<pre>"; print_r($fieldnames); exit;
			$insert_data=$callConfig->insertRecord($table,$fieldnames);
		 	//print_r($insert_data); exit;
			$insert_id = $insert_data['LASTID'];
			$fieldnames['transaction_type'] = $table;
			$fieldnames['transaction_id'] = $insert_id;
			$res = $this->addAndUpdateBulkApprovalTransaction($fieldnames,'insert');
		}
		$email_data = array('company_id'=>$post['company_id'],'transaction_number'=>$post['invoice_number'],'transaction_type'=>$table, 'transaction_date'=>$invoiced_at, 'amount'=>$post['subTotal'], 'tax_amount'=>$post['taxRate'] , 'discount_amount'=>$post['discount_amount'] , 'total_amount'=>$post['grandtotal'] ,'action_label'=>'Submit for Approval', 'option'=>$fieldarray[$table]['option'] ,'Rid'=>$insert_id,'email_template'=>3,'status_old_value'=>$post['status_old_value'],'status'=>$status_main,'updated_by'=>$post['updated_by'],'originator'=>$fieldnames['originator'],'opex_cpex'=>$post['opex_cpex'],'category_type'=>$post['type'],'cost_center'=>$post['cost_center']);

		if($status > ST_DRAFT){
			$res =$this->toSendEmailNotificationsBaseDAO($email_data);
		}
		return $insert_data;
	}
	function dumpaddletters($post){
		global $callConfig;
		$query= " INSERT INTO letter_dumps (`transaction_id`, `company_id`, `transaction_number`, `transaction_date`, `version_number`, `originator`, `customer_id`, `ref_id`, `ref_no`, `ref_date`, `subject`, `address_to`, `content`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `id`, `company_id`, `transaction_number`, `transaction_date`, `version_number`, `originator`, `customer_id`, `ref_id`, `ref_no`, `ref_date`, `subject`, `address_to`, `content`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM  letters Where id = '?'";
		$params = [$query, $post];
		$insert_data=$callConfig->executeQuery($query);
		//echo $query; //exit;
		return $insert_data;
	}
	function productupload($data){
		global $callConfig;
		//echo "<pre>"; print_r($data); exit;
		foreach ($data['data'] as  $value) {
		 
			for($k=0; $k<count((array)$data['names']); $k++){
				//	echo $data['names'][$k]."####".$value[$k]."<br>";
				$main[$data['names'][$k]] = $value[$k];
			}
			$main['vendor_id'] = $data['vendor_id'];
				//echo "<pre>"; print_r($main); exit;
			$last[] = $main; 
		}
			//echo "<pre>";	print_r($last); exit;
		foreach ($last as  $fieldnames) {
			$insert_data=$callConfig->insertRecord('product_upload',$fieldnames);
		}
	}
	function togetgenralledgerlistdata($post){
		global $callConfig;
		if($post['from']!='' && $post['to'] !=''){
			$from = $post['from'];
			$to = $post['to'];
		}else{
			$from = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 30 day"));
			$to = date('Y-m-d');
		}

			//$query = "SELECT * FROM `general_ledger` WHERE `created_date` > '".$from."' AND `created_date` < '".$to."'";
		$query = "SELECT * FROM general_ledger WHERE `created_date` between '? 00:00:00' and '? 23:59:59'";
		$params = [$query, $from, $to];
		if($post['account_code']!=''){
			$query.=" AND account_code in (?)";
			$params = [$query, $post];
		}
		$data = $callConfig->getAllRowsNew($query);
		$darray = array(); 
		foreach ($data as  $value) {
			if(in_array($value->account_code, (array)$darray)){
					//echo "ravi";
				$larray[$value->account_code]['debit_amount'] = $larray[$value->account_code]['debit_amount'] + $value->debit_amount;
				$larray[$value->account_code]['credit_amount'] = $larray[$value->account_code]['credit_amount'] + $value->credit_amount; 
			}else{
					//echo "Shankar";
				$darray[] = $value->account_code;
				$marray['RecordID'] = '1';
				$marray['acuount_code'] = $value->account_code;
				$marray['acuount_name'] = 'account_type';
				$marray['debit_amount'] = $value->debit_amount;
				$marray['credit_amount'] = $value->credit_amount;
				$marray['blance'] = '1000'; 
				$marray['type'] = $value->account_type;
				$marray['Actions'] = 'http://dev.mylejar.com/login.php';
				$larray[$value->account_code] = $marray;
				$larray[$value->account_code]['Orders'] =  array();
			}
			$carray['transaction_date'] = $value->transaction_date;
			$carray['transaction_type'] = $value->transaction_date;
			$carray['number'] = $value->transaction_ref_no;
			$carray['debit_amount'] =  $value->debit_amount;
			$carray['credit_amount'] = $value->credit_amount;
			$carray['blance'] = $value->balance_amount;
			$carray['Status'] = '1';
			$carray['Actions'] = 'https://www.google.com/doodles/';

				//'Actions'=>'http://dev.mylejar.com/login.php';
			$larray[$value->account_code]['Orders'][] = $carray;
		}
		$k=1;
		foreach ($larray as $mvalue) {
			$mvalue['RecordID'] =  $k;
			$maindata [] = $mvalue;
			$k++;
		}
			//echo "<pre>"; print_r($maindata); print_r($data);
		return $maindata;
	}
	function insertORUpadateStatusprogressaction($post){
		global $callConfig;
		$modulelist = $post['module_setup_id'];		
		$whr1="company_id= '?' and status_progress_id='?'";
		$params = [$whr1, $post, $post];
		$query2 = $callConfig->selectQuery('status_progress_actions','*',$whr1,'','','');
		$progress = $callConfig->getAllRowsNew($query2);
		$statuaction= array(); $rechack= array();
		foreach ($progress as $progres) {
			$statuaction[] = $progres->target_status;
			$modulesetupid[] = $progres->module_setup_id;
		}
		foreach($post['target'] as $target){
			foreach($modulelist as $module_id){
				if(in_array($target, (array)$statuaction) AND in_array($modulesetupid, (array)$module_id)){
					$update = "update status_progress_actions set is_active = 0,module_setup_id = '?',work_flow_field= '?' where status_progress_id ='?' and target_status = '?'";
					$params = [$update, $modulelist, $post, $post, $post, $post, $target];
					$update_data=$callConfig->executeQuery($update);
				}else{
					$insert_filed = array('company_id'=>$post['company_id'],"module_setup_id"=>$module_id,'status_progress_id'=>$post['status_progress'],'target_status'=>$target,'email_groups'=>$post['email_groups'],'email_template_id'=>3,'work_flow_field'=>$post['work_flow_field'],'notify_users'=>$post['notify_users'],'current_status'=>$post['current_status'],'status_date'=>$post['status_date'],'from_date'=>$post['from_date'],'to_date'=>$post['to_date'],'created_date'=>$post['created_date'],'created_by'=>$post['created_by'],'is_active'=>'0'); 
					$insert_data=$callConfig->insertRecord('status_progress_actions',$insert_filed);

				}
			}
			$rechack[] = $target;
		}
		$chek = implode(',', (array)$rechack);
		$lupdate = "update status_progress_actions set is_active = 1 where status_progress_id ='?' and target_status not in (?)";
		$params = [$lupdate, $post, $chek];
		$update_data=$callConfig->executeQuery($lupdate);
			//exit;
		return $update_data;
	}
	function togetstatusprogressactiondetails(){
		global $callConfig;
		$query11 = "SELECT * FROM status_progress WHERE `id` IN  (SELECT status_progress_id FROM status_progress_actions GROUP BY status_progress_id)";
		$data11 = $callConfig->getAllRowsNew($query11);
		foreach ($data11 as $data1) {
			$array['id'] =  $data1->id;
			$array['table_name'] =  $data1->table_name;
			$array['name'] =  $data1->name;
			$array['action_label'] =  $data1->action_label;
			$query12 = "SELECT  action_label FROM status_progress WHERE `id` IN (Select `target_status` from status_progress_actions WHERE status_progress_id =? )";
			$params = [$query12, $data1->id];
			$data12 = $callConfig->getAllRowsNew($query12);
				//print_r($data12); exit;
			$sub = array();
			foreach ($data12 as  $data2) {
				$sub[] = $data2->action_label;
			}	
			$array['statu_labels'] = implode(',', (array)$sub);
			$mainarray[] = $array;
		}
		return $mainarray;
			//echo "<pre>"."ravi"; print_r($mainarray);
	}
	function toGetItemcostingtyewithdata($post){
		global $callConfig;
			//$whr1="company_id= '".$post['company_id']."' and id='".$post['status_progress']."'";
		
		$query = "SELECT itmc.`default_costing_type` FROM items itm,item_categories itmc where itmc.company_id = itm.company_id AND itm.category_id = itmc.id and itm.id=".$post['item_id'];
		$data = $callConfig->getRowNew($query);
		$return = $this->togetitemvalues($post['item_id']);
		if($return->purchase_price !=""){
			$return->item_st_price = $return->purchase_price;
		}

		if($return->inventory_asset_account !=""){
			$return->purchase_account = $return->inventory_asset_account;
		}elseif($return->item_cat_inventory_asset_account !=""){
			$return->purchase_account = $return->inventory_asset_account;
		}elseif($return->config_inventory_asset_account !=""){
			$return->purchase_account = $return->config_inventory_asset_account;
		}	
		if($post['date'] !="" AND $post['item_id'] !=""){
			$query2 = "SELECT  `day_rate` FROM item_wa_balances WHERE `item_id`=? AND company_id =? AND `balance_date`<='?'  ORDER BY  balance_date DESC LIMIT 0,1";
			$params = [$query2, $post, $post, $this->dateconvertdbformat, $post];
				//echo $query2;
			$wadata = $callConfig->getRowNew($query2);
			if($wadata->day_rate !=""){
				$return->item_wa_price = $wadata->day_rate;
			}
		}

		$sales_tables = array('enquiries','estimates','delivery_orders','sales_orders','sales_returns');
		
		if($data->default_costing_type == 'W' AND !in_array($post['table'], (array)$sales_tables)){
			$return->purchase_price = $wadata->day_rate!=""?$wadata->day_rate:$return->purchase_price;
		}elseif(in_array($post['table'], (array)$sales_tables)){ 
			$return->purchase_price = $return->sale_price;
		} 

		if($return->purchase_price !=""){
			$return->purchase_price = $return->purchase_price !=""?$return->purchase_price:0;
		}
			
		return $return;
	}
	function employeeDetails($data){
		global $callConfig;
		$query1 = "SELECT *,(SELECT ms.marital_status_name FROM marital_statuses ms WHERE employees.marital_status = ms.id AND employees.company_id = ms.company_id) as marital_status_name, (SELECT bg.blood_group FROM blood_groups bg WHERE employees.blood_group = bg.id AND employees.company_id = bg.company_id) as blood_group_name FROM employees WHERE id = ? AND is_active=0 AND company_id=".$data['company_id'];
		$params = [$query1, $data, $data];
		$data1 = $callConfig->getRowNew($query1);
		$data['report_id'] = $data1->reporting_manager_id;
		$return['emp_data'] = (array)$data1;

		$query12 = "SELECT * FROM application_users WHERE id = ? AND is_active=0 AND company_id=".$data['company_id'];
		$params = [$query12, $data1->user_id, $data];
		$data12 = $callConfig->getRowNew($query12);
		$data['report_id'] = $data12->reports_to;
		$data['user_id'] = $data12->id;
		$array =  array('departments'=>'id,department_name','application_users'=>'id,display_name','verticals'=>'id,vertical_name','designations'=>'id,designation_name','locations'=>'id,location_name','employment_bands'=>'id,band_name','employment_levels'=>'id,employment_band_id,level_name','employment_roles'=>'id,role_name','application_roles'=>'id,display_name','marital_statuses'=>'id,marital_status_name','blood_groups'=>'id,blood_group');
		foreach ($array as  $arr =>$fieldval) {
			$loopdata = $this->togettablebasedataPro($arr,$data,$fieldval);
			$return[$arr] =  (array)$loopdata;
		}
		$return['teams'] = $this->togetemployeeteamdata($data);
		$return['asserts'] = $this->togetemployeeassertdata($data);

		$vendor_display_fieldssql = "SELECT id, field_list FROM employees_display_fields WHERE company_id = '?' AND role_id = '?' AND is_active = 0";
		$params = [$vendor_display_fieldssql, $data, $data];
		$vendor_display_fieldsres=$callConfig->getRowNew($vendor_display_fieldssql);
		$return['vendor_display_fieldssql'] = $vendor_display_fieldssql;
		$return['vendor_display_fields'] = $vendor_display_fieldsres;

		return $return;
	}
	function togettablebasedata($table,$data ){
		global $callConfig;
		$whr = "is_active = 0 AND company_id = ".$data['company_id'];
			//echo $where;
		$query=$callConfig->selectQuery($table,'*',$whr,'','','');
			//echo $tablename."//".$query;exit;
		return $callConfig->getAllRowsNew($query);
	}
	function togettablebasedataPro($table,$data,$fieldval){
		global $callConfig;
		$whr = "is_active = 0 AND company_id = ".$data['company_id'];
			//echo $where;
		$query=$callConfig->selectQuery($table,$fieldval,$whr,'','','');
			//echo $tablename."//".$query;exit;
		return $callConfig->getAllRowsNew($query);
	}
	function togetemployeeteamdata($data){
		global $callConfig;
		$report_id =$data['report_id'];
		$by_emp_ids = array();
		$query22 = "SELECT id FROM application_users WHERE reports_to = ? AND is_active=0 AND company_id=".$data['company_id'];
		$params = [$query22, $data, $data];
		$data22 = $callConfig->getAllRowsNew($query22);
		if($report_id != ""){	
			$by_emp_ids[] = $report_id;
		}
		foreach ($data22 as $emp_ids) {
			$by_emp_ids[] =  $emp_ids->id;
			$by_emp[] =  $emp_ids->id;
		}
		$str_by_emp_ids = implode(",", (array)$by_emp_ids);
		$query23 = "SELECT id,`department_id`,`designation_id`,`location_id`,`display_name`,`email_id`,`phone`,`image`,`user_id`,billable,on_off_site,work_from_home FROM employees WHERE user_id IN (?)";
		$params = [$query23, $str_by_emp_ids];
			//echo $query23;
		$data23 = $callConfig->getAllRowsNew($query23);
			//print_r($data23); //exit;	
		foreach ($data23 as  $data23_data) {
			$array23[$data23_data->user_id] = (array)$data23_data;
		}
			
			//echo $report_id;
			//print_r($array23);
			$teams ['reproter'] = $array23[$report_id];
			unset($array23[$report_id]);
			$teams ['teams'] = $array23;
			return $teams;
	}
	function togetemployeeassertdata($data){
		global $callConfig;
		$query24 = "SELECT id, asset_id, start_date, end_date, remarks, current_status FROM emp_assets WHERE emp_id = ? AND is_active=0 AND company_id=".$data['company_id'];
		$params = [$query24, $data, $data];
		$data24 = $callConfig->getAllRowsNew($query24);
		foreach ($data24 as $data24_data) {
			$assets_data[$data24_data->id] = (array)$data24_data;
			$assert_ids[] = $data24_data->assets_id;
		}
		if(count((array)$assert_ids) !=0 ){
			$str_asserts_ids = implode(",", (array)$assert_ids);
			$query25 = "SELECT `id`,`asset_code`,`asset_description`,`long_description` FROM assets  WHERE id IN (?)";
			$params = [$query25, $str_asserts_ids];
			$data25 = $callConfig->getAllRowsNew($query25);	
			foreach ($data25 as  $data25_data) {
				$array25[$data25_data->id] = (array)$data25_data;
			}
			$data24 = (array)$data24;
			foreach ($assets_data as $key => $asset) {
				$assets_data[$key]['assert'] = $array25[$asset['assets_id']];
			}
		}
		return $assets_data;
	}
	function updatedynamicapprovallog($post){
		global $callConfig;
		$statusarray = array(ST_ACTIVE, ST_DRAFT, ST_INACTIVE);
		//print_r($statusarray); print_r($post); //exit;		
		$post['response_date'] = $post['response_date']!=""? $post['response_date']: date('Y-m-d H:i:s');
		if(in_array($post['status'], (array)$statusarray)){
			//echo "hai"; exit;
			$res=$callConfig->updateRecord($post['table'],array('current_status'=>$post['status']),'id',$post['hid']);
		}else{
			//echo "ravi"; exit;
			
			$whr = "";
			$fieldnames = array('comments'=>$post['comments'],'response_date'=>$post['response_date'],'response_type'=>$post['status']);
			$whr = "transaction_table = '?' AND transaction_id='?' AND user_id='?' AND send_back_flag= 0";
			$params = [$whr, $post, $post, $post];
			$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$fieldnames,'','',$whr);
			if($post['response_type'] != ST_SEND_BACK ){ 

				$query11= "SELECT * FROM ? WHERE id= '?'";
				$params = [$query11, $post, $post];
				$data11 = $callConfig->getRowNew($query11);
				$data11 = (array)$data11;
				//$post['status_old'] = $data11->current_status;
				$query22= "SELECT * FROM doa WHERE `transaction_type` ='?' and doa_field_name !='' ORDER BY id LIMIT 1";
				$params = [$query22, $post];
				$data22 = $callConfig->getRowNew($query22);
				$post['status_old'] = $data11['current_status'];
				$post['amount'] =  $data11[$data22->doa_field_name];
				$post['originator']  = $data11['created_by'];
				$post['status_old'] = $data11['current_status'];
				$post['amount'] =  $data11[$data22->doa_field_name];
				$post['originator']  = $data11['created_by'];
				$post['transaction_type'] = $post['table'];
				$post['transaction_number'] = $post['hid'];
				if(isset($data11['transaction_number'])){ $post['transaction_number'] = $data11['transaction_number']; }
				$post['total_amount'] = $post['amount'];
				$post['status'] = $post['status'];
				$post['status_old_value'] = $post['status_old'];
				$post['Rid'] = $post['hid'];

				//print_r($post); exit;
				//$return = $this->ToCheckStatusApprovalLogdynamic($post);
				
				$val = $this->toSendEmailNotificationsBaseDAOdynamic($post);
				if($val != ""){
					$res=$callConfig->updateRecord($post['table'],array('current_status'=>$post['status']),'id',$post['hid']);
				}
			}
			if($post['table'] == 'vendor_registers' && $post['status'] == ST_APPROVED){
				$rr = $this->registervendors($post['hid']);
			}
			if($post['table'] == 'manpower_contracts' && $post['status'] == ST_APPROVED){
				$array = array('mpc_id'=>$post['hid'],'user_id'=>$post['user_id'],'company_id'=>$post['company_id']);

				$rr = $this->savevendortojobpostings($array);
			}
			if($post['table'] == 'vendor_rate_cards' && $post['status'] == ST_APPROVED){
				$array = array('mpc_id'=>$post['hid'],'user_id'=>$post['user_id'],'company_id'=>$post['company_id']);
			    //echo "ravi"; print_r($array); //exit;
				$rr = $this->updatejobmanpowerrq($array);
			}
			if($post['table'] == 'project_timesheets' && ($post['status'] == ST_APPROVED1 || $post['status'] == ST_SEND_BACK)){

				$array = array('emp_ids'=>$post['hid'],'user_id'=>$post['user_id'],'company_id'=>$post['company_id'],'status'=>$post['status'],'extra'=>$post['extra'],'company_id'=>$post['company_id'],'emp_id'=>$post['emp_id']);
			    //echo "ravi"; print_r($array); //exit;
				$rr = $this->updateprojecttimesheetsbluk($array);
			}
			if($post['table'] == 'employee_terminations' && $post['status'] == ST_APPROVAL_PENDING){
				$array = array('trn_id'=>$post['hid'],'user_id'=>$post['user_id'],'company_id'=>$post['company_id']);
			    //echo "ravi"; print_r($array); //exit;
				$rr = $this->exitprocesssteps($array);
			}
			if($post['table'] == 'project_tasks' && $post['status'] == ST_APPROVAL_PENDING){
				$array = array('id'=>$post['hid'],'user_id'=>$post['user_id'],'company_id'=>$post['company_id']);
				$rr = $this->taskassignttoadmin($array);
			}
		}
		return $res;
	}
	function taskassignttoadmin($post){
		global $callConfig; 
		$usql = "SELECT emp.user_id FROM project_tasks pt,project_users pu, employees emp  WHERE pu.company_id = '?' AND pt.id = '?' and pu.is_active = 0 AND pu.company_id = emp.company_id AND pu.is_active = emp.is_active AND pu.company_id = pt.company_id AND pu.is_active = pt.is_active AND pu.emp_id = emp.id AND pt.project_id = pu.id";
		$params = [$usql, $post, $post];
		$uid = $callConfig->getRowNew($usql);
		
		$inserttransactionapprovelog =array('company_id'=>$post['company_id'],'module_setup_id'=>$callConfig->module_id,'transaction_table'=>'project_tasks','transaction_id'=>$post['id'],'response_type'=>'','user_id'=>$uid->user_id,'request_date'=>date("Y-m-d H:i:s"));
		$ress=$callConfig->insertRecord("transaction_approval_logs",$inserttransactionapprovelog);
		$msg = "Project Tasks ? : One Issue Occurs";
		$params = [$msg, $post];
		$user_ids  = array($uid->user_id);
		$option = "COM_LIST_TICKETS";
		$notdataa = array('company_id'=>'1','notification_message'=>$msg, 'id'=>$post['id'],'application_table_name'=>'project_tasks', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
		$sendnotification = $this->tosendnotifiations($notdataa);
		return $post;
	}
	
	function updateprojecttimesheetsbluk($get){
		global $callConfig; 
		$quer = "SELECT hr_buddy FROM `employee_buddies` WHERE `employee_id`=".$get['emp_id'];
		$hrb = $callConfig->getRowNew($quer);
		$query = "UPDATE project_timesheets SET hr_id=? WHERE emp_id IN (?) AND MONTH(timesheet_date)=MONTH('?')" ;
		$params = [$query, $hrb->hr_buddy, $get, $get, $get];
		echo $query;
		$upp = $callConfig->executeQuery($query); 
		$qq = "SELECT user_id FROM employees WHERE id IN (?)";
		$params = [$qq, $get];
		$usdata = $callConfig->getAllRowsNew($qq);
		foreach($usdata as $udata){
			$user_ids[] = $udata->user_id;
		}
		$option = "COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=".date('Y',strtotime($get['extra']))."&PAY_MONTH=".date('m',strtotime($get['extra']));
		$params = [$option, $get, $get];
		if($get['status']==ST_SEND_BACK){
			$msg = date('M Y',strtotime($get['extra'])).' Your Time Sheet is Send Back  by Hiring Manager';
			$params = [$msg, $get];
		}else{
			$msg = date('M Y',strtotime($get['extra'])).' Your Time Sheet is Approved by Hiring Manager';
			$params = [$msg, $get];
		}
		
		$notdataa = array('company_id'=>$get['company_id'], 'notification_message'=>$msg, 'transaction_number'=>$notarry['id'], 'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
		//print_r($notdataa);
		$ss = $this->tosendnotifiations($notdataa);
		exit;
	}
	function ToCheckStatusApprovalLogdynamic($post){
		global $callConfig;
		$workflow = $this->checkhierarchicallevel($post);
		//print_r($workflow); exit;
		if($workflow['doa'] == 1){
			$res = $this->tocheckDAOValues($data);
			$return = $post['status_old'];
			//print_r($res); exit;
			if($res->doa_condition == 1){
				$where = "transaction_table = '?' AND transaction_id='?' AND send_back_flag=0 AND doa_id != 0 ";	
				$params = [$where, $post, $post];
				$query=$callConfig->selectQuery(TBL_TRANSACTION_APPROVAL_LOGS,'*',$where,'','','');
				$maindata = $callConfig->getAllRowsNew($query);
				$count= count((array)$maindata);
				$doaval = $res->approval_type;
				$doanum = $res->approval_number;
				foreach ($maindata as $mvalue) {
					//if($mvalue->response_type == 13){	$val['sent_back'][] = $mvalue->id;	}
					if($mvalue->response_type == ST_REJECTED){	$val['reject'][]  =	$mvalue->id;	}
					else if($mvalue->response_type == 0){	$val['pending'][] =	$mvalue->id;	}
					else{	$val['approve'][] =	$mvalue->id;	}
				}
				if($doaval == 'A'){
					if($count == count((array)$val['approve'])){ $return = ST_APPROVED; }
					if(count((array)$val['reject'])>0){ $return = ST_REJECTED; }
				}
				if($doaval == 'M'){

					if($doanum <= count((array)$val['approve']) ){ $return = ST_APPROVED;  }
					elseif (count((array)$val['reject'])<= 0) {
						$return = $post['status_old'];
					}else{
						$check = count((array)$val['approve'])+count((array)$val['pending']);
						if($check < $doanum ){ $return = ST_REJECTED; }
					}
					 //echo $count."|||".$return; 
				}
				if($doaval == 'L'){
					$return = $post['response_type'];
				}
			}else{
				$return = $post['response_type'];
			}
		}else{
			$return = $post['response_type'];
		}
		return $return;
	}
	function toSendEmailNotificationsBaseDAOdynamic($data){
		//echo "<pre>"; print_r($data);// exit;
		global $callConfig;
		$email_data = $this->checkhierarchicallevel($data);
		//echo "man"; print_r($email_data);exit;bnm
		if($email_data['doa'] == 1){
			$email_data = $this-> togetemailslist($data);
		}
		//print_r($email_data);exit;transaction_date
		$emails[] = $email_data['email'];
		print_r($emails);
		//$EMAIL FUNTION
		$users = $email_data['user_id'];
		print_r($users); //exit;
		$query1="SELECT user_email,id FROM application_users WHERE id in (?)";
		$params = [$query1, $data];
		echo $query1;// exit;
		$emailids = $callConfig->getRowNew($query1);
		//print_r($emailids);//exit; 
		//echo $emailids->user_email;
		$emails[] = $emailids->user_email;
		//print_r($emails);exit;
		$whrstp="table_name = '?' AND status='?'";
		$params = [$whrstp, $data, $data];
		$querystp=$callConfig->selectQuery(TBL_STATUSPROGRES,'*',$whrstp,'','','');
		//echo $querystp; //exit;
		$resstp = $callConfig->getRowNew($querystp);
		//print_r($resstp); exit;
		$data['action_label'] =  $resstp->action_label;
		$data['email_template'] =  $resstp->email_template_id=="0"?3:$resstp->email_template_id;
		$data['transaction_date']  = $data['response_date']!=""?$data['response_date']:date('Y-M-d') ;
		$data['email_content_id'] = 3;
		print_r($data);// exit;
		$email_array = $this->emailtemplate($data);
		
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$params = [$file_name];
		$email_array ['content'] = $file_name;
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		file_put_contents($file_path, $email);
		//fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);

		//$notarry['notification_message'] = ucwords(str_replace("_"," ",($data['transaction_type']])))." ".$data['transaction_number']. ": ".ucwords($resstp->action_label) ;
		$notarry['notification_message'] = ucwords(str_replace("_"," ",($data['transaction_type'])))." ?submitted by ".ucwords($resstp->action_label)." for your Approval";
		$params = [$notarry, $data, $data, $resstp->action_label];
		//echo "ravi"; exit;
		$notarry['notification_users'] = count((array)$users);
		$notarry['id'] = $data['Rid'];
		$notarry['option'] = $data['option'];
		//print_r($notarry); exit;
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('company_id'=>$data['company_id'],'notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'application_table_name'=>$data['transaction_type'],'transaction_number'=>$data['Rid']);
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		//$notification_id = 13;
	//	echo "ravi".$notification_id ; print_r($users);	  exit;
		foreach ($users as $user) {
			//$user_id = $user->id;
			$appv_fieldnames=array('company_id'=>$data['company_id'],'module_setup_id'=>$callConfig->module_id,'transaction_table'=>$data['transaction_type'],'transaction_id'=>$data['Rid'],'user_id'=>$user,'request_date'=>date("Y-m-d H:i:s"),'doa_id' => $email_data['doa_id']);
			$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
			$revalue = 'ACTION';
			$nti_use_fieldnames=array('company_id'=>$data['company_id'],'user_id'=>$user,'notification_id'=>$notification_id,'notification_type'=>$revalue);
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
			//echo "<pre>"; print_r($nti_use_fieldnames);
		}
		
		if($data['originator'] != ''){
			$notarry['notification_message'] = ucwords(str_replace("_"," ",($data['transaction_type'])))." ?: The document is submitted for Approval";
			$params = [$notarry, $data, $data];
			$NOTIfieldnames=array('company_id'=>$data['company_id'],'notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'application_table_name'=>$data['transaction_type'],'transaction_number'=>$data['Rid']);
			$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
			$notification_id = $noti_res['LASTID'];

			$nti_use_fieldnames=array('company_id'=>$data['company_id'],'user_id'=>$data['originator'],'notification_id'=>$notification_id,'notification_type'=>'ACTION');
		//print_r($nti_use_fieldnames); exit;
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}
		//echo "ravi"; exit;
		return $noti_res;
	}
	function addorupdatewizardsettings($post,$urldata){
		global $callConfig; 
		$update = "update application_user_widgets set is_active = 1 where  user_id='?'";
		$params = [$update, $urldata];
		$update_data=$callConfig->executeQuery($update);
		//echo "<pre>"; print_r($post); print_r($urldata); exit;
		foreach ($post['name'] as  $page_id) {
			$whr1="company_id= '?' and user_id='?' and widget_id='?'";
			$params = [$whr1, $urldata, $urldata, $page_id];
			$query2 = $callConfig->selectQuery('application_user_widgets','*',$whr1,'','','');
			//echo $query2; exit;
			$data = $callConfig->getRowNew($query2);
			if($data->id != ""){
				$update = "update application_user_widgets set is_active = 0 where id ='?'";
				$params = [$update, $data->id];
				$update_data=$callConfig->executeQuery($update);
			}else{
				$insert_array = array('company_id'=>$urldata['company_id'],'user_id'=>$urldata['user_id'],'widget_id'=>$page_id,'is_active'=>0,'current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
				$approval_id=$callConfig->insertRecord('application_user_widgets',$insert_array);
			}
			$ids[] = $page_id;
		}
		//$ids = implode(",", (array)$ids);		
		//$update = "update application_user_widgets set is_active = 1  where id NOT IN(?)";
		//$params = [$update, $ids];
		//$data = $callConfig->executeQuery($update);
		return $data;
	}
	function addorupdatekpi_comment($post,$get){
		global $callConfig; 
		foreach ($post['name'] as  $key=>$name) {
			$insert_array = array('company_id'=>$get['company_id'],'indicator_name'=>$name,'key_reslut_id'=>$post['result'][$key],'weightage'=>$post['weightage'][$key],'model'=>$post['model'][$key],'uom'=>$post['uom'][$key],'role_id'=>$post['role_id'],'version_no'=>$post['version_no']);
			if($post['kpi_id'][$key]!=0 && $post['kpi_id'][$key]!="" ){
				$insert_array['updated_by'] =$post['created_by'];$insert_array['updated_date'] =$post['created_date'];
				$inserttasks = $callConfig->updateRecord('key_performance_indicators',$insert_array,'id',$post['kpi_id'][$key]);

			}else{
				$insert_array['is_active'] ='0'; $insert_array['current_status'] =0 ;$insert_array['created_by'] =$post['created_by'];
				$insert = $callConfig->insertRecord('key_performance_indicators',$insert_array);
			}
		}
		return $insert;
	} 
	function togetcandidatedocumentlist($data){
		global $callConfig; 
		$query="SELECT * FROM document_lists WHERE id in (SELECT document_list_id FROM `candidate_documents` WHERE `candidate_id` = ? AND company_id = ? AND is_active = 0)";
		$params = [$query, $data, $data];
		$data = $callConfig->getAllRowsNew($query);
		return $data;
	}
	
	function togetsalarycatdetaillist($post){
		global $callConfig;
		$type = $post['type']!="" ? $post['type'] : "M";
		$whr1="company_id= '?' AND is_active=0 AND part_of_ctc=0 AND salary_type ='?'";
		$params = [$whr1, $post, $type];
		//$whr1="company_id= '".$post['company_id']."' AND is_active=0 AND part_of_ctc=0";
		$query2 = $callConfig->selectQuery('salary_categories','*',$whr1,'display_seq ASC','','');
		$data = $callConfig->getAllRowsNew($query2);
		$whr="company_id= '?' AND is_active=0";
		$params = [$whr, $post];
		 $query = $callConfig->selectQuery('earnings','*',$whr,'display_seq ASC','','');
		$details =$callConfig->getAllRowsNew($query);
		$return = array();
		foreach ($data as $dat) {
			$return[$dat->id] = (array)$dat;
		}
		foreach ($details as  $value) {
			if(isset($return[$value->category_id])){
				$return[$value->category_id]['data'][] = $value;	
			}		
		}
		return $return;
	}
	function inserttasks_comments($post, $get){
			global $callConfig;
			//echo "ravi"; print_r($post); //exit; 
			$array_id = array('kpi_tasks'=>'kpi_id','organization_tasks'=>'goal_id','employee_kpi_tasks'=>'emp_goal_id');
			$update_query = "UPDATE ? SET is_active = 1 WHERE ?= '?'";
			$params = [$update_query, $post, $array_id, $post, $post];
			$data = $callConfig->executeQuery($update_query);
			$update_query1 = "UPDATE employee_kpi_tasks SET is_active = 1 WHERE kpi_id= '".$post['datakpi_id']."'";
			$data = $callConfig->executeQuery($update_query1);
			
			for( $i=1;$i<$post['count']+1; $i++) {
				if($post['name_'.$i] !=""){
					$insert_array = array('company_id'=>$get['company_id'],'task_name'=>$post['name_'.$i],'description'=>$post['descrption_'.$i],'task_shot_code'=>$post['short_code_'.$i],'task_frequency'=>$post['frequency_'.$i],'mandatory'=>$post['mandatory_'.$i],'is_active'=>'0','current_status'=>'0','created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
					$insert_array[$array_id[$post['table']]]=$post['kpi_id'];
				//print_r($insert_array); exit;
				if($post['table'] == 'kpi_tasks'){ 	$insert_array['kpi_id']=$post['kpi_id']; }
				if($post['table'] == 'organization_tasks'){ 	$insert_array['goal_id']=$post['kpi_id']; } 
				if($post['table'] == 'employee_kpi_tasks'){ 	$insert_array['emp_goal_id']=$post['kpi_id']; }*
				$insert = $callConfig->insertRecord($post['table'],$insert_array);
				 //$insert = $callConfig->insertRecord('employee_kpi_tasks',$insert_array);
			}
		}
		return $insert;
	}
	function goalscheckandadd($data){
		global $callConfig;
			//echo "<pre>"; print_r($data);
		$whr1="company_id= '?' and role_id='?' and version_no='?' and is_active=0";
		$params = [$whr1, $data, $data, $data];
		$query2 = $callConfig->selectQuery('key_performance_indicators','*',$whr1,'','','');
			//echo $query2; exit; 
		$row = $callConfig->getAllRowsNew($query2);
		$pdatequery = "Update organization_goals SET is_active=1 where  company_id= '?' and role_id='?' and assessment_year='?'";
		$params = [$pdatequery, $data, $data, $data];
			//echo $pdatequery; exit;
		$ll = $callConfig->executeQuery($pdatequery);		
		foreach ($row as $value) {
			$insert_array = array('company_id'=>$data['company_id'],'assessment_year'=>$data['assemnt_year'],'indicator_name'=>$value->indicator_name,'key_reslut_id'=>$value->key_reslut_id,'weightage'=>$value->weightage,'model'=>$value->model,'uom'=>$value->uom,'display_sequence'=>$value->display_sequence,'role_id'=>$value->role_id,'is_active'=>'0','current_status'=>'0','created_by'=>$data['user_id']);

				//print_r($insert_array); exit;
			$insert = $callConfig->insertRecord('organization_goals',$insert_array);
			$returnary['organization_goals'][] = $insert;
			$query_task = "INSERT INTO organization_tasks( `company_id`, `goal_id`, `task_name`, `description`, `task_shot_code`, `task_frequency`, `mandatory`, `is_active`, `current_status`, `created_by`) SELECT  `company_id`, ?  as  'kp_id' , `task_name`, `description`, `task_shot_code`, `task_frequency`, `mandatory`, `is_active`, `current_status`, ? as 'created_by' FROM `kpi_tasks` WHERE kpi_id =? and is_active = 0";
			$params = [$query_task, $insert, $data, $value->id];
				//echo $query_task; echo "</br>"; 
			$returnary['sql'][] = $query_task;
			$insert_task = $callConfig->executeQuery($query_task);
			$returnary['res'][] = $insert_task;
		}
		return $returnary;

	}
	function employeegoalscheckandadd($data){
		global $callConfig;
			//echo "<pre>"; print_r($data);
		$cfquery = $callConfig->selectQuery('config','default_kpi_weekly_task_day,default_kpi_monthly_task_day','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);

		$whr1="company_id= '?' and role_id='?'  and  assessment_year='?' and is_active=0";
		$params = [$whr1, $data, $data, $data];
		$query2 = $callConfig->selectQuery('organization_goals','*',$whr1,'','','');
			//echo $query2; exit;
		$row = $callConfig->getAllRowsNew($query2);
			//print_r($row); exit;
		$whre="company_id= '?' and job_title_id='?' AND date_of_leaving IS NULL and is_active=0";
		$params = [$whre, $data, $data];
		$querys = $callConfig->selectQuery('employees','*',$whre,'','','');
		$emprow = $callConfig->getAllRowsNew($querys);
			//print_r($emprow); exit;
		$returnary['organization_goals'] =$querys;
		foreach ($emprow as $emp) {

			$pdatequery = "Update employee_goals SET is_active=1 where  company_id= '?' and employee_id='?' and assessment_year='?'";
			$params = [$pdatequery, $data, $emp->id, $data];
			//echo $pdatequery; exit;
			$ll = $callConfig->executeQuery($pdatequery);		
			foreach ($row as $value) {
				$insert_array = array('company_id'=>$data['company_id'],'assessment_year'=>$data['assemnt_year'],'indicator_name'=>$value->indicator_name,'key_reslut_id'=>$value->key_reslut_id,'weightage'=>$value->weightage,'model'=>$value->model,'uom'=>$value->uom,'display_sequence'=>$value->display_sequence,'employee_id'=>$emp->id,'assessment_frequency'=>$value->assessment_frequency,'traget'=>$value->traget,'is_active'=>'0','current_status'=>'0','created_by'=>$data['user_id'],'role_id'=>$data['role_id']);

				//print_r($insert_array); exit;
				$insert = $callConfig->insertRecord('employee_goals',$insert_array);
				$returnary['employee_goals'][] = $insert;
				$query_task = "INSERT INTO employee_kpi_tasks( `company_id`, `emp_goal_id`, `task_name`, `description`, `task_shot_code`, `task_frequency`, `is_active`, `current_status`, `created_by`,`kpi_id`,`mandatory`) SELECT  `company_id`, ?  as  'emp_goal_id' , `task_name`, `description`, `task_shot_code`, `task_frequency`, `is_active`, `current_status`, ? as 'created_by',`goal_id`,`mandatory` FROM `organization_tasks` WHERE goal_id =? and is_active = 0";
				$params = [$query_task, $insert, $data, $value->id];
				//echo $query_task ; exit;
				$returnary['sql'][] = $query_task;
				$insert_task = $callConfig->executeQuery($query_task);
				$returnary['res'][] = $insert_task;
				$send_details = array('emp_id'=>$emp->id,'emp_goal_id'=>$insert['LASTID'],'company_id'=>$data['company_id'],'assessment_year'=>$data['assemnt_year'],'user_id'=>$data['user_id'],'default_kpi_monthly_task_day'=>$cfdata->default_kpi_monthly_task_day,'default_kpi_weekly_task_day'=>$cfdata->default_kpi_weekly_task_day);
				$res = $this->toinsertemployeehistory($send_details);
				$returnary['employee_kpi_task_histories'][] = $res;
			}
		}
		return  $returnary;
	}
	function toinsertemployeehistory($data){
		global $callConfig; 
		
		$whre="company_id= '?' and  emp_goal_id='?' and is_active=0";
		$params = [$whre, $data, $data];
		$querys = $callConfig->selectQuery('employee_kpi_tasks','*',$whre,'','','');
		$goalrows = $callConfig->getAllRowsNew($querys);
		$returnarr['employee_kpi_tasks'] = $goalrows;
		$years = explode('-', $data['assessment_year']);
		$end_date = $years['1']."-03-31";
		$params = [$end_date, $years];
		$start_date = $years['0']."-04-01";
		$params = [$start_date, $years];
		$output['statdate1'] = $start_date;
		if(strtotime($start_date) < strtotime(date('Y-m-d'))){
			$start_date = date('Y-m-d');
		}
		$output['statdate2'] = $start_date;
		$output['end_date']= $end_date;
		foreach($goalrows as $goalrow){
			$insert_array = array('company_id'=>$data['company_id'],'emp_kpi_task_id'=>$goalrow->id,'emp_id'=>$data['emp_id'],'name'=>$goalrow->task_name,'task_date'=>$value->weightage,'is_active'=>'0','current_status'=>'0','created_by'=>$data['user_id']);
			//$output = array();
			for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime('+1 day', $i)) {
				$insert_array['task_date'] = date('Y-m-d',$i);
				$output['task_frequency'][]= $goalrow->task_frequency;
				if($goalrow->task_frequency == 'D'){
					$insert = $callConfig->insertRecord('employee_kpi_task_histories',$insert_array);
				}
				if($goalrow->task_frequency == 'W'){
					$output[] = date('N', $i);
					if(date('N', $i) == $data['default_kpi_weekly_task_day']){
						$insert = $callConfig->insertRecord('employee_kpi_task_histories',$insert_array);
					}
				}
				if($goalrow->task_frequency == 'M'){
					if(date('d', $i) == $data['default_kpi_monthly_task_day']){
						$insert = $callConfig->insertRecord('employee_kpi_task_histories',$insert_array);
					}
				}
			}
			$returnarr['insert_array'][] = $insert_array;
			$returnarr['insert'][] = $insert;
			
		}
		return $returnarr;
	}

	function addorupdategoals($post,$get){
		global $callConfig; 
		foreach ($post['id'] as  $key=>$id) {
			$insert_array = array('indicator_name'=>$post['name'][$key],'key_reslut_id'=>$post['result'][$key],'weightage'=>$post['weightage'][$key],'model'=>$post['model'][$key],'uom'=>$post['uom'][$key],'assessment_frequency'=>$post['assessment_frequency'][$key],'traget'=>$post['traget'][$key]);
				//print_r($insert_array); exit;
			$insert_data=$callConfig->updateRecord('organization_goals',$insert_array,'id',$id);
		}
		return $insert_data;
	}	

	function addorupdatesalary($post, $urldata){
		global $callConfig; 
			//echo "<pre>";print_r($post); exit;
		$whre="company_id= '?' and is_active=0";
		$params = [$whre, $post];
		$querys = $callConfig->selectQuery('earnings','*',$whre,'','','');
		$earrows = $callConfig->getAllRowsNew($querys);
		$ctc_id = '0';
		foreach($earrows as $earrow){
			$earns[$earrow->code]=$earrow->id;
			if($earrow->code == 'TDS'){
				$tds_id = $earrow->id;
				$arraytds = $earrow;
			}
		}
		if($post['check']=='update'){
			$insert_data=$callConfig->updateRecord('salary_template',array('is_active'=>1),'id',$post['rid']);
		}
		$affected_date = str_replace('/"', '-', $post['affected_date']);
		$affected_date =  date('Y-m-d', strtotime($affected_date));
		if(isset($post['employee_id']) && $post['employee_id']!=""){
			$whre="id= ".$post['employee_id'];
			$query = $callConfig->selectQuery('employees','id, emp_code',$whre,'','','');
			$empdata = $callConfig->getAllRowsNew($query);
		}

		$gross_early='0.00'; $ctc_m ="0.00"; $ctc_y="0.00";
		$ctc_y = $post['ctc_val']; 
		$ctc_m= $post['ctc_val']/12;
		foreach ($post['id'] as $key => $id) {
			if($earns['GS']==$id){ 
				$gross_early = $post['yearly_amount'][$key];
			}
			if($earns['GRATUITY']==$id){ 
				$gratuity = $post['yearly_amount'][$key];
			}
			if($earns['LTA LTA']==$id){ 
				$lta = $post['yearly_amount'][$key];
			}
			if($earns['MIP']==$id){ 
				$mip = $post['yearly_amount'][$key];
			}
			
		}
		//------------------------------//
		$slab_query = $callConfig->selectQuery('tax_slads','*',"is_active=0 AND assessment_year='2024-2025'",'','','');
			//echo $slab_query; exit;
		$slab_data = $callConfig->getAllRowsNew($slab_query);
		$tax_slab =(array)$slab_data;
		$calslab_total = ($gross_early+$mip+$gratuity)-$lta;
		foreach ($tax_slab as $slab) {
			$slab =(array)$slab;
			if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
				$cal_amount = $calslab_total - ($slab['slad_from']-1);
				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
				break;
			}else{
				$cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
			}
		}
		$tdsss = 'N';
		//-------------------------------//

		$sal_array = array('company_id'=>$post['company_id'],'employee_id'=>$post['employee_id'],'candidate_id'=>$post['candidate_id'],'emp_code'=>$empdata->emp_code, 'yearly_gross_salary'=>$gross_early,'ctc_monthly'=>$ctc_m,'ctc_yearly'=>$ctc_y,'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by'],'description'=>$post['description'],'note'=>$post['note'],'affected_date'=>$affected_date,'variable_yearly'=>$post['variable_yearly'],'fixed_ctc_yearly'=>$post['fixed_ctc_yearly'],'vp_with_ctc'=>$post['vp_with_ctc_val'],'joining_bonus_applicable'=>$post['joining_bonus_applicable'],'joining_bonus_amount'=>$post['joining_bonus_amount'], 'joining_bonus_tax_type'=>$post['joining_bonus_tax_type']);
			//echo "<pre>";print_r($sal_array); exit;
		$insert = $callConfig->insertRecord('salary_template',$sal_array);
			//echo "<pre>";
		foreach ($post['id'] as $key => $id) {
			if($tds_id == $id){
				$post['monthly_amount'][$key] = $cal_intax/12;
				$post['yearly_amount'][$key] = $cal_intax;
				$tdsss = 'Y';
			}
			$saltemp_array = array('company_id'=>$post['company_id'],'salary_template_id'=>$insert['LASTID'],'component_id'=>$id,'component_name'=>$post['name'][$key],'component_category'=>$post['cat_cat'][$key],'component_type'=>$post['cat_type'][$key],'component_number'=>$post['cat_num'][$key],'monthly_amount'=>$post['monthly_amount'][$key],'yearly_amount'=>$post['yearly_amount'][$key],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by']);
			$insert_data = $callConfig->insertRecord('salary_template_components',$saltemp_array); 
		}
		if($tdsss == 'N'){
			$saltemp_array = array('company_id'=>$post['company_id'],'salary_template_id'=>$insert['LASTID'],'component_id'=>$tds_id,'component_name'=>$arraytds->name, 'component_category'=>$arraytds->category_id,'component_type'=>$arraytds->calculation_type,'component_number'=>$arraytds->calculation_number,'monthly_amount'=>$cal_intax/12,'yearly_amount'=>$cal_intax,'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by']);
			$insert_data = $callConfig->insertRecord('salary_template_components',$saltemp_array); 
		}
		//candidate insert salary this time trigger Only
		$return['post'] = $post;
		if($post['candidate_id'] !="" AND $post['send_mail'] != 'N'){

			$ctcsql = "SELECT stc.component_name, stc.yearly_amount, ear.code FROM salary_template_components stc, earnings ear WHERE  stc.company_id = '?' AND stc.company_id = ear.company_id AND  ear.code = 'CTC' AND stc.component_id = ear.id AND stc.salary_template_id = '?' AND stc.is_active = 0 AND stc.is_active = ear.is_active";
			$params = [$ctcsql, $post, $insert];
			$ctcres = $callConfig->getRowNew($ctcsql);
			$upcandidate = array('reporting_date'=>$this->dateconvertdbformat($post['reporting_date']),'offer_validity'=>$this->dateconvertdbformat($post['offer_validity']),'salary_template_id'=>$insert['LASTID'],'offer_ctc'=>$ctcres->yearly_amount);
			$candidatesres = $this->updateTablesdata($upcandidate, 'candidates', 'id',$post['candidate_id']);
			$return['candidates'] = $candidatesres;

			$insert_array = array('company_id'=>$post['company_id'],'candidate_id'=>$post['candidate_id'],'offer_amount'=>$ctc_y,'pay_template_id'=>$insert['LASTID'],'comments'=>$post['description'],'reporting_date'=>$this->dateconvertdbformat($post['reporting_date']),'offer_validity'=>$this->dateconvertdbformat($post['offer_validity']),'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d h:i:s'));
			$candidate_offer_historiesres = $callConfig->insertRecord('candidate_offer_histories',$insert_array);
			$return['candidate_offer_histories'] = $candidate_offer_historiesres;

			$candiquery = $callConfig->selectQuery('candidates','*','id='.$post['candidate_id'],'','','');
			$cadndidata = $callConfig->getRowNew($candiquery);

			$job_titlesql = $callConfig->selectQuery('job_titles','job_title','id='.$cadndidata->job_tittle_id,'','','');
			$job_titleres = $callConfig->getRowNew($job_titlesql);

			$replacearray = array("candidates.display_name"=>$cadndidata->display_name,"candidates.job_title"=>$job_titleres->job_title,"candidates.expected_ctc"=>$cadndidata->offer_ctc,"candidates.reporting_date"=>$cadndidata->reporting_date,"candidates.email_id"=>$cadndidata->email_id,"candidates.offer_validity"=>$cadndidata->offer_validity); 
			$replacearray['user_company_id'] = $cadndidata->company_id;
			$replacearray['session_company_id'] = $cadndidata->company_id;
			$replacearray['creater_user_id'] = array($post['user_id']); 
			$replacearray['to_company_id'] = $cadndidata->company_id;
			
			$rm_users_sql = "SELECT display_name, user_email, user_mobile FROM application_users WHERE company_id = '?'  AND is_active = 0 AND id='?'";
			$params = [$rm_users_sql, $cadndidata->company_id, $cadndidata->rm_id];
			$rm_users_res = $callConfig->getRowNew($rm_users_sql);
			$replacearray['rm_display_name'] = $rm_users_res->display_name;
			$replacearray['rm_email_id'] = $rm_users_res->user_email;
			$replacearray['rm_contact_number'] = $rm_users_res->user_mobile;
			$replacearray['created_by'] = $get['user_id'];
			$emill = $this->sendNotificationAndEmails('',$replacearray,10); 
		}
		//End candidate insert salary this time trigger Only

		//$inserttasks = $callConfig->updateRecord('salary_template',array('yearly_gross_salary'=>$gross_early,'ctc_yearly'=>$ctc_y,'ctc_monthly'=>$ctc_m),'id',$insert['LASTID']);
		//return $return;
		return $insert;
	}
	function addorupdategradesalary($post, $urldata){
		global $callConfig; 
		$whre="company_id= '?' and  level_id='?' and is_active=0";
		$params = [$whre, $urldata, $post];
		$querys = $callConfig->selectQuery('employees','id',$whre,'','','');
		$emprows = $callConfig->getAllRowsNew($querys);
		$affected_date = date('Y-m-d',strtotime($post['affected_date']));
		$sal_array = array('company_id'=>$post['company_id'],'grade_id'=>$post['grade_id'],'annual_ctc'=>$post['ctc_y'],'company_cost_monthly'=>$post['total_month'],'company_cost_yearly'=>$post['total_year'],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by'],'description'=>$post['description'],'note'=>$post['note'],'affected_date'=>$affected_date);
		$insert = $callConfig->insertRecord('grade_salary_template',$sal_array);
		foreach ($post['id'] as $key => $id) {
			$saltemp_array = array('company_id'=>$post['company_id'],'grade_salary_template_id'=>$insert['LASTID'],'component_id'=>$id,'component_name'=>$post['name'][$key],'component_category'=>$post['cat_cat'][$key],'component_type'=>$post['cat_type'][$key],'component_number'=>$post['cat_num'][$key],'monthly_amount'=>$post['monthly_amount'][$key],'yearly_amount'=>$post['yearly_amount'][$key],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by']);
			$insert_data = $callConfig->insertRecord('grade_salary_template_components',$saltemp_array); 
		}
		foreach($emprows as $emp){
			$sal_array = array('company_id'=>$post['company_id'],'employee_id'=>$emp->id,'annual_ctc'=>$post['ctc_y'],'company_cost_monthly'=>$post['total_month'],'company_cost_yearly'=>$post['total_year'],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by'],'description'=>$post['description'],'note'=>$post['note'],'affected_date'=>$affected_date);
			$insert = $callConfig->insertRecord('salary_template',$sal_array);
			foreach ($post['id'] as $key => $id) {
				$saltemp_array = array('company_id'=>$post['company_id'],'salary_template_id'=>$insert['LASTID'],'component_id'=>$id,'component_name'=>$post['name'][$key],'component_category'=>$post['cat_cat'][$key],'component_type'=>$post['cat_type'][$key],'component_number'=>$post['cat_num'][$key],'monthly_amount'=>$post['monthly_amount'][$key],'yearly_amount'=>$post['yearly_amount'][$key],'is_active'=>'0','current_status'=>0,'created_by'=>$post['created_by']);
				$insert_data = $callConfig->insertRecord('salary_template_components',$saltemp_array); 
			}

		}
		return $insert;
	}
	function addjobpostingapplicatantdata($post,$get){
		global $callConfig; 
		$res=$callConfig->updateRecord('job_posting_candidates',array('is_active'=>'1'),'job_posting_id',$post['job_posting']);
		foreach ($post['name'] as  $key=>$name) {
			$insert_array = array('company_id'=>$get['company_id'],'job_posting_id'=>$post['job_posting'],'candidate_name'=>$name,'candidate_email'=>$post['email'][$key],'candidate_phone'=>$post['phone'][$key],'remarks'=>$post['remarks'][$key],'job_selection_process_step_id'=>$post['process'][$key],'file'=>$post['file_name'][$key],'vendor_id'=>$post['vendor_id']);
			$insert_array['is_active'] = '0';  $insert_array['current_status']='0';
		//	print_r($insert_array); exit;
			if(isset($post['id'][$key])){
				$insert=$callConfig->updateRecord('job_posting_candidates',$insert_array,'id',$post['id'][$key]);
			}else{
				$insert_array['is_active'] = '0'; $insert_array['current_status']= 0; $insert_array['current_status']='0'; $insert_array['created_by']= $post['created_by'];$insert_array['created_by'] =$post['created_by'];
				$insert = $callConfig->insertRecord('job_posting_candidates',$insert_array);
				}//exit;
				
		}
		return $insert;
	}	
	function candidateautoregistation($post,$get){
		global $callConfig; 
		$allreadycreatesql = "SELECT id FROM ".MAINDB.".user_registered_groups WHERE user_email ='?'";
		$params = [$allreadycreatesql, $post];
		$allreadycreateres =  $callConfig->getAllRowsNew($allreadycreatesql);
		if(count((array)$allreadycreateres)>0){
			return array('status' =>"fail",'type'=>'email');
		}else{
			$whre="user_mobile='?' and is_active=0";
			$params = [$whre, $post];
			$querym = $callConfig->selectQuery('application_users','*',$whre,'','','');
			$empmrow = $callConfig->getAllRowsNew($querym);
			if(count((array)$empmrow)>0){
				return array('status' =>"fail",'type'=>'mobile');
			}else{
				$role = json_encode(array(CANDIDATE_ROLE));
				$insert_array = array('company_id'=>$get['company_id'],'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'],'display_name'=>$post['display_name'],'user_type'=>'4','role_id'=>$role,'current_status'=>'0','is_active' =>'0',"created_date"=>date('Y-m-d H:i:s'),"created_by"=>$post['user_id']);

				//$insert_array = array('company_id'=>$get['company_id'],'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'],'display_name'=>$post['display_name'],'user_type'=>'4');
				$insert = $callConfig->insertRecord('application_users',$insert_array);
				//$data = $this->NewUserformate($post,$get['data_name']);
				$userid = $insert['LASTID'];

				$query = "SELECT application_module,id,role_short_code FROM application_roles where company_id = '?' AND role_short_code ='".CANDIDATE_ROLE."' AND is_active = 0 ";
				$params = [$query, $post];
				$roleres = $callConfig->getRowNew($query);
				$insertarray = array("company_id"=>$get['company_id'],"user_id"=>$userid,"module_setup_id"=>$roleres->application_module,"application_module_id"=>$roleres->application_module,"role_id"=>$roleres->id,"role_short_code"=>$roleres->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$insert = $callConfig->insertRecord('application_user_module_roles',$insertarray);

				$emailarray['user_type'] = $post['user_type'];
				$emailarray['user_company_id'] = $get['company_id'];
				$emailarray['session_company_id'] = $get['company_id'];
				$emailarray['send_user_id'] = array($insert['LASTID']);
				$emailarray['creater_user_id'] = array($post['user_id']);
				$emailarray['to_company_id'] = $get['company_id'];
				$emailarray['user_display_name'] = $post['display_name'];
				$emailarray['user_email_id'] = $post['user_email'];
				$emailarray['user_phone_number'] = $cddata->phone;
				$emailarray['default_module_id'] = 23;
				$emailarray['user_id'] =$lastuserid;
				$emailarray['created_by'] = $post['user_id'];
				$emailarray['user_email'] = $post['user_email'];
				$emailarray['company_id'] = $get['company_id'];
				$emailarray['template_reference_id'] = 3;
				$data = $this->NewUserformate($emailarray,$get['data_name']);
				
				// $can_seraal = $this->toGetApplicationSerialNumber('candidates');
				// $query = $callConfig->selectQuery('job_posting_candidates','*','candidate_email='.$post['user_email'],'','','');
				// $jcdata = $callConfig->getRowNew($query);
				// $insert_candate = array('company_id'=>$get['company_id'],'first_name'=>$post['display_name'],'display_name'=>$post['display_name'],'candidate_code'=>$can_seraal,'email_id'=>$post['user_email'],'phone'=>$post['user_mobile'],'hr_connect'=>$get['user_id'],'created_by'=>$insert['LASTID'],'job_tittle_id'=>$post['job_title_id'],'vendor_id'=>$jcdata->vendor_id);
				// $insert = $callConfig->insertRecord('candidates',$insert_candate);
				// $res = $callConfig->executeQuery("update job_posting_candidates set candidate_id=".$insert['LASTID']." where candidate_email =".$post['user_email']);
			}
		}
		return $insert;
	}
	// function addorupdatecandaitedocument($post,$get){
	// 	global $callConfig;
	// 	//print_r($post); exit;
	// 	$category_id = isset($post['category_id']) ? $post['category_id'] :1;
	// 	foreach ($post['document'] as  $document) {
	// 		$insert_array = array('company_id'=>$get['company_id'],'candidate_id'=>$post['candidate_id'],'category_id'=>$category_id,'document_list_id'=>$document,'is_active'=>'0','current_status'=>'0');
	// 		//echo "<pre>"; print_r($insert_array); 
	// 		$daas = $callConfig->insertRecord('candidate_documents',$insert_array); //exit;
	// 	}
	// 	return $daas;
	// }
	function addorupdatecandaitedocument($post,$get){
		global $callConfig;
		//print_r($post); exit;
		//$category_id = isset($post['category_id']) ? $post['category_id'] :1;
		foreach ($post['category_id'] as $category_id){
			// $where = "candidate_id = '".$post['candidate_id']."' AND category_id = '".$category_id."'";
			// $res=$callConfig->updateRecord("candidate_documents",array('is_active'=>null,'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d h:i:s')),"","",$where);
			// $canddocssql = "SELECT id,document_list_id FROM candidate_documents WHERE company_id = '".$get['company_id']."' AND category_id = '".$category_id."' AND candidate_id = '".$post['candidate_id']."'";
			// $canddocsres = $callConfig->getAllRowsNew($canddocssql);
			// foreach($canddocsres as $canddocsre){
			// 	$candidate_documents[$canddocsre->document_list_id] =$canddocsre->id; 
			// }
			foreach ($post['document'][$category_id] as $document){
				// if(isset($candidate_documents[$document])){
				// 	$res=$callConfig->updateRecord('candidate_documents',array('is_active'=>'0','updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d h:i:s')),'id',$candidate_documents[$document]);
				// }else{
				$insert_array = array('company_id'=>$get['company_id'],'candidate_id'=>$post['candidate_id'],'category_id'=>$category_id,'document_list_id'=>$document,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
					$daas = $callConfig->insertRecord('candidate_documents',$insert_array); //exit;
				//}
			}	
		}
		return $daas;
	}
	function updatecandaitedocument($post,$get){
			global $callConfig;
			$return = array();
			//echo "<pre>";
			//print_r($post);// exit;
			$return['post'] = $post;
			foreach ($post['documents'] as  $document) {
				$insert_array = array('document_shared'=>$document['name'],'current_status'=>ST_COMPLETE);
			//print_r($insert_array);
				$insert=$callConfig->updateRecord('candidate_documents',$insert_array,'id',$document['id']);
		} //exit;
		
		$designationsquery = $callConfig->selectQuery('candidates','display_name,hr_connect','id='.$post['candidate_id'],'','','');
		$cdata = $callConfig->getRowNew($designationsquery);
		$notarry['notification_message'] = "Candidate ? shared the documents Transaction ID : ".$document['id'];
		$params = [$notarry, $cdata->display_name, $document];
		$notarry['notification_users'] = array('0'=>$cdata->hr_connect);
		$notarry['id'] = $post['candidate_id'];
		$notarry['company_id']= $get['company_id'];
		$notarry['application_table_name'] = "candidates";
		$notarry['option'] = "COM_INSERT_CANDIDATE";
		$this->tosendnotifiations($notarry);
		return $return;
	}
	function tosendnotifiations($notarry){
		global $callConfig;		
		$notarry['notification_datetime']= 	$notarry['notification_datetime'] !=""?$notarry['notification_datetime'] : date("Y-m-d H:i:s");
	  	//$notarry['notification_datetime']=date("Y-m-d H:i:s");
		$jsnot= json_encode($notarry);
		$NOTIfieldnames=array('company_id'=>$notarry['company_id'],'module_setup_id'=>$callConfig->module_id,'notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'transaction_number'=>$notarry['id'],'application_table_name'=>$notarry['application_table_name']);
		$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
		$notification_id = $noti_res['LASTID'];
		if($notarry['type']!=""){ 
			$revalue=$notarry['type']; 
		}else{
			$revalue = 'ACTION';
		}
		foreach ($notarry['notification_users'] as  $user_id) {
			$nti_use_fieldnames=array('company_id'=>$notarry['company_id'],'module_setup_id'=>$callConfig->module_id,'user_id'=>$user_id,'notification_id'=>$notification_id,'notification_type'=>$revalue);
			$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);
		}
		$ids_list= implode(",",(array)$notarry['notification_users'] );
		$user_query = "Select user_email From application_users Where id IN(?)";
		$params = [$user_query, $ids_list];
		$email_ids = $callConfig->getAllRowsNew($user_query);
		foreach ($email_ids as  $value) {
			$emails[] = $value->user_email;
		}
		$email_array['mesg'] = $notarry['notification_message'];
		$email_array['revicer'] = $emails;
		$email_array['email_type'] = 0;		
		//$lenn = $this->sendEmailslist($email_array);
		return $res;
	}
	function updateonlystatus($get){
		global $callConfig;
		$whr = "";
		$fieldnames = array('current_status'=>$get['status']);
		$res=$callConfig->updateRecord($get['table'],$fieldnames,'id',$get['hid']);


		$designationsquery = $callConfig->selectQuery('candidate_documents','*','id='.$get['hid'],'','','');
		$cddata = $callConfig->getRowNew($designationsquery);
		if($get['status'] == ST_REJECTED){
			$notarry['notification_message'] = "Doument is Rejected";
		}else{
			$notarry['notification_message'] = "Doument is Approved";	
		}
		$notarry['notification_users'] = array('0'=>$cdata->candidate_id);
		$notarry['id'] = $post['candidate_id'];
		$notarry['company_id']= $get['company_id'];
		$notarry['option'] = "COM_LIST_CANDIDATE_DOCUMENTS";
		$this->tosendnotifiations($notarry);
		return $res;
	}

	function toinsertpayrnsummary($post, $get){
		global $callConfig;
		//echo "<pre>";print_r($post); exit;
		$whr1="company_id= '?' and is_active=0 and user_type!=4";
		$params = [$whr1, $get];
		$query2 = $callConfig->selectQuery('application_users','*',$whr1,'','','');
		$users = $callConfig->getAllRowsNew($query2);
		//print_r($users); exit;
		$queryemp = $callConfig->selectQuery('employees','*','employee_type =1 AND is_active=0','','','');
		//echo $queryemp;
		$emps = $callConfig->getAllRowsNew($queryemp);
		//echo "<pre>";print_r($emps); exit;

		$whr1st="company_id= '?' and pay_month='?' and pay_year='?'";
		$params = [$whr1st, $get, $post, $post];
		$query1st = $callConfig->selectQuery('pay_run_summary','id,revision_no',$whr1st,'id DESC','','');
		//echo $query1st;
		$st1 = $callConfig->getRowNew($query1st);
		//echo "<pre>";print_r($st1); exit;

		if($st1->id !=''){
			$version = $st1->revision_no+1;
		}else{ $version = 1; }
		$datess = $this->payrollfromtodates($get);
		$date1 = $datess['date1']; $date2 = $datess['date2'];
		$cfquery = $callConfig->selectQuery('config','default_payroll_day,show_leave_member','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);
		
		$daystr	= strtotime($date2) - strtotime($date1);
		$total_working_days = round($daystr / (60 * 60 * 24))+1;
		//print_r($cfdata); echo $total_working_days; exit;
		$data = $callConfig->getAllRowsNew($query2);
		$whr="company_id= '?' AND is_active=0";
		$params = [$whr, $get];
		$query = $callConfig->selectQuery('earnings','*',$whr,'display_seq ASC','','');
		$details =$callConfig->getAllRowsNew($query);
		foreach($details as $detail){
			$earn_compontnent[$detail->id] = $detail->calculation_mode;
			$earn_pro_rata[$detail->id] = $detail->pro_rata;
		}
		$whr33="company_id= '?' and is_active='0'";
		$params = [$whr33, $get];
		$query33 = $callConfig->selectQuery('earnings','*',$whr33,'','','');
		$eransdetails =$callConfig->getAllRowsNew($query33);
		foreach($eransdetails as $erans){
			$errdata[$erans->id] = $erans->calculation_mode=="ADD" ? '0' : '1';
			if($erans->calculation_mode=="ADD"){
				$inaa[] = $erans->id;
			}
		}
		//print_r($emps); exit;
		$v=1;
		foreach ($emps as $emp) {
			//print_r($earn_compontnent);
			//if($emp->id==70){
			//$emp->id=48;
			//print_r($emp);//exit;
			$whrst="is_active=0 and employee_id=".$emp->id;
			//echo $whrst;
			$queryst = $callConfig->selectQuery('salary_template','*',$whrst,'','','');
			$datast = $callConfig->getRowNew($queryst);
			
			if($datast->id!=''){
			   //print_r($datast); exit;
				$whrstc="is_active=0 and salary_template_id=$datast->id";
				$querystc = $callConfig->selectQuery('salary_template_components','*',$whrstc,'','','');
				$datastc = $callConfig->getAllRowsNew($querystc);
				//print_r($datastc); exit;
				$gross_aount = $datast->yearly_gross_salary;
				$gross_month_amount = $datast->company_cost_monthly;
				$emp_termination_query = "SELECT * FROM employee_terminations WHERE `releive_date`>='?' AND `releive_date`<='?' AND current_status =".ST_APPROVED." AND emp_id=".$emp->id;
				$params = [$emp_termination_query, $date1, $date2, $emp->id];
				//echo $emp_termination_query;
				$emp_termination_data = $callConfig->getRowNew($emp_termination_query);
				if($emp_termination_data->id!="" && $emp->joining_date > $date1){ 
					$leaves = $this->togetemployeeleavecalucate($emp->id,$emp->joining_date,$emp_termination_data->releive_date);
					$total_lop_days = $total_working_days - ($leaves['calender_work_days']-$leaves['total_lop_days']);
				}elseif($emp->joining_date > $date1){ //echo "step2"; exit;
				$leaves = $this->togetemployeeleavecalucate($emp->id,$emp->joining_date,$date2);
				$total_lop_days = $total_working_days - ($leaves['calender_work_days']-$leaves['total_lop_days']);
				}elseif($emp_termination_data->id!=""){ //echo "step3";
				$leaves = $this->togetemployeeleavecalucate($emp->id,$date1,$emp_termination_data->releive_date);
				$total_lop_days = $total_working_days - ($leaves['calender_work_days']-$leaves['total_lop_days']);
			}else{ 
				if($cfdata->show_leave_member==1){ //echo "hai";
				$leaves = $this->togetemployeeleavecalucate($emp->id,$date1,$date2);
				$total_no_of_leaves =$leaves['total_no_of_leaves']; 
				$total_lop_days=$leaves['total_lop_days']; 
				$total_no_of_working_days=$total_working_days-$total_no_of_leaves;
			}else{
				$total_no_of_leaves =0; 
				$total_lop_days=0; 
				$total_no_of_working_days=$total_working_days-$total_no_of_leaves;
			}
		}
		$total_month_working_days = $leaves['total_month_working_days'];
				//echo "ravi"; print_r($leaves); exit;
				$date_of_leaving = $emp->date_of_leaving!=""?$emp->date_of_leaving:$emp_termination_data->releive_date;
				
				$insert_array =  array('company_id'=>$get['company_id'] ,'revision_no'=>$version,'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'location_id'=>$emp->location_id,'employee_id'=>$emp->id,'designation_id'=>$emp->designation_id,'father_name'=>$emp->father_name,'date_of_birth'=>$emp->date_of_birth, 'date_of_leaving'=>$date_of_leaving, 'date_of_joining'=>$emp->joining_date,'contact_details'=>$emp->phone,'bank_id'=>$emp->bank_id,'bank_link'=>$emp->bank_link,'bank_account_no'=>$emp->bank_account_no,'cover_under_pf'=>$emp->cover_under_pf,'cover_under_esi'=>$emp->cover_under_esi,'total_no_of_leaves'=>$total_no_of_leaves,'total_no_of_days'=>$total_working_days ,'total_lop_days'=>$total_lop_days,'total_no_of_working_days'=>$total_month_working_days ,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
				//print_r($insert_array); exit;
				$insert = $callConfig->insertRecord('pay_run_summary',$insert_array); 
				//print_r($insert);
				//$array = array('emp_id'=>$emp->id,'gross_aount'=>$gross_aount,'date1'=>$date1,'date2'=>$date2); 
				//print_r($array); 
				//$incomtax = $this->togetemployeeincometax($array);
				//print_r($incomtax); exit;
				
				foreach ($datastc as  $stc) {
					
					if($stc->component_id!="15" && $stc->component_id!=16){
						if($earn_pro_rata[$stc->component_id]=="0" && $insert_array['total_lop_days'] >0){
							if($insert_array['total_lop_days']==$total_month_working_days){ $insert_array['total_lop_days'] = $total_working_days; }
							$act =($stc->monthly_amount/$total_working_days) * ($total_working_days-$insert_array['total_lop_days']);
						}else{
							$act = $stc->monthly_amount;
						}
					//echo $stc->component_id."||".$act;
					//if($stc->component_id!==6 && intval($act)==200 ){ $act=300; }	

					//echo "<br>".$act; exit;
						$salary_component_type=3;
						if($earn_compontnent[$stc->component_id] =='ADD'){ $salary_component_type =  '0';}
						if($earn_compontnent[$stc->component_id] =='MINUS'){ $salary_component_type =  '1';}
					//$salary_component_type =  $earn_compontnent[$stc->component_id] !='ADD' ? '1' : '0';
					//$inaa=array('11','12','20');
						$cp =0;	if (in_array($stc->component_id, (array)$inaa)){ $cp =1; }
						$insert_componet_array =  array('company_id'=>$get['company_id'] ,'revision_no'=>$version, 'pay_run_summary_id'=>$insert['LASTID'], 'location_id'=>$emp->location_id,'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'location_id'=>$user->location_id,'employee_id'=>$emp->id,'component_id'=>$stc->component_id,'compnent_type'=>0,'salary_component_standard'=>$stc->monthly_amount,'salary_component_actual'=>$act,'salary_component_type'=>$salary_component_type,'compnent_type'=>$salary_component_type,'location_id'=>$emp->location_id,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
						$insert_componets = $callConfig->insertRecord('pay_run_summary_components',$insert_componet_array);
					//print_r($insert_componet_array); print_r($insert_componets); echo "<br>"; exit;
					}
					
				}

				$pass = array('emp_id'=>$emp->id,'date1'=>$date1,'date2'=>$date2,'pay_month'=>$post['pay_month'],'pay_year'=>$post['pay_year']);
				$row = $this->employeeadvancepaymntsamountwithupdate($pass);
				if($row->adv_amount==""){$row->adv_amount=0; }
				$salary_component_type=1;
				$insert_componet_array =  array('company_id'=>$get['company_id'] ,'revision_no'=>$version, 'pay_run_summary_id'=>$insert['LASTID'], 'location_id'=>$emp->location_id,'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'location_id'=>$user->location_id,'employee_id'=>$emp->id,'component_id'=>15,'compnent_type'=>0,'salary_component_standard'=>0,'salary_component_actual'=>$row->adv_amount,'salary_component_type'=>$salary_component_type,'compnent_type'=>$salary_component_type,'location_id'=>$emp->location_id,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
				$insert_componets = $callConfig->insertRecord('pay_run_summary_components',$insert_componet_array);
				
				$array = array('emp_id'=>$emp->id,'gross_aount'=>$gross_aount,'date1'=>$date1,'date2'=>$date2); 
				$incomtax = $this->togetemployeeincometax($array);
				$insert_componet_array =  array('company_id'=>$get['company_id'] ,'revision_no'=>$version, 'pay_run_summary_id'=>$insert['LASTID'], 'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'location_id'=>$emp->location_id,'employee_id'=>$emp->id,'component_id'=>16,'compnent_type'=>$salary_component_type,'salary_component_standard'=>0,'salary_component_actual'=>$incomtax,'salary_component_type'=>$salary_component_type,'is_active'=>'0','current_status'=>'0');
				$insert_componets = $callConfig->insertRecord('pay_run_summary_components',$insert_componet_array);
				$earn = $this->togetemployrrearndetails($array);

				$earn = $this->togetemployrrearndetails($array);
				if(count((array)$earn)>0){
					foreach ($earn as $ear) {
						$insert_componet_array =  array('company_id'=>$get['company_id'] ,'revision_no'=>$version, 'pay_run_summary_id'=>$insert['LASTID'], 'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'location_id'=>$user->location_id,'employee_id'=>$emp->id,'component_id'=>$earn['earning_id'],'compnent_type'=>0,'salary_component_standard'=>$ear['approved_amount'],'salary_component_actual'=>$ear['approved_amount'],'salary_component_type'=>$ear['salary_component_type'],'is_active'=>'0','current_status'=>'0');
						$insert_componets = $callConfig->insertRecord('pay_run_summary_components',$insert_componet_array);
					}
				}
				
			}
		 $v++;	} //exit;
		//}
		return $total_working_days;
	}


	function togetpayrolledetails($post){
		global $callConfig;
		$post['month'] = '12';
		$post['year'] = '2020';
		$post['revision_no'] = '1';
		$return['dash1'] = $this->togetpayrollgrosspayanddeductions($post);
		return $return;
	}
	function togetpayrollgrosspayanddeductions($post){
		global $callConfig;
		$query = "SELECT SUM(if(compnent_type!=4,`salary_component_actual`,0)) as actual ,SUM(if(compnent_type =4,`salary_component_actual`,0)) as deductions, sum(salary_component_actual) as total FROM pay_run_summary_components where  `pay_month`='?' and `pay_year`='?' and `revision_no`='?'";
		$params = [$query, $post, $post, $post];
		$data = $callConfig->getRowNew($query);
		return $data;
		//echo $query;
	}
	function togetpayslipdetails($post){
		global $callConfig;
		//		echo "<pre>";
		//print_r($post);
		$whr1st="company_id= '?' AND is_active = '0' and pay_month='?' and pay_year='?' and employee_id='?' ";
		$params = [$whr1st, $post, $post, $post, $post];
		$query1st = $callConfig->selectQuery('pay_run_summary','*',$whr1st,'','','');
		$st1 = $callConfig->getRowNew($query1st);
		$data['st1'] = $query1st;

		$whremp="company_id= '?' and is_active=0 and id=".$post['employe_id'];
		$params = [$whremp, $post, $post];
		$queryemp = $callConfig->selectQuery('employees','*',$whremp,'','','');
		$emp = $callConfig->getRowNew($queryemp);

		//$whr1stm="pay_run_summary_id='".$st1->id."' ";
		
		//$query1stm = $callConfig->selectQuery('pay_run_summary_components','*',$whr1stm,'','','');
		//$stmp = $callConfig->getAllRowsNew($query1stm);
		
		$query1stm= "SELECT * FROM pay_run_summary_components prsc, earnings ern WHERE prsc.pay_run_summary_id='?' AND prsc.component_id = ern.id AND ern.display_in_payslip = 0 ORDER BY ern.display_seq ASC";
		$params = [$query1stm, $st1->id];

		$stmp = $callConfig->getAllRowsNew($query1stm);
		//print()t_r($stmp);
		$bankquery = $callConfig->selectQuery('banks','id,bank_name','id='.$st1->bank_id,'','','');;
		$bankdata = $callConfig->getRowNew($bankquery);

		$locationquery = $callConfig->selectQuery('locations','id,location_name','id='.$st1->location_id,'','','');;
		$locationdata = $callConfig->getRowNew($locationquery);

		$departmentsquery = $callConfig->selectQuery('departments','id,department_name','id='.$emp->department_id,'','','');;
		$departmentsdata = $callConfig->getRowNew($departmentsquery);

		$designationsquery = $callConfig->selectQuery('designations','id,designation_name','id='.$st1->designation_id,'','','');;
		$designationsdata = $callConfig->getRowNew($designationsquery);

		$data['month']= date("F", mktime(0, 0, 0, $post['month'], 10));
		$data['year']= $post['year'];
		$data['emp_number'] = $emp->emp_code;
		$data['emp_name'] = $emp->display_name;
		$data['pan_number'] = $emp->pan_number;
		$data['pf_uan'] = $emp->pf_uan;
		$data['bank_id'] = $st1->bank_id;
		$data['bank'] = $bankdata->bank_name; 
		$data['bank_account_no'] = $st1->bank_account_no;
		$data['doj'] = date("dS F Y",strtotime($st1->date_of_joining));
		$data['pf'] = $emp->pf_no; 
		$data['location'] = $locationdata->location_name;
		$data['department'] = $departmentsdata->department_name;
		$data['lop'] = $st1->total_lop_days; 
		$data['std'] = $st1->total_no_of_days;
		$data['work'] = $st1->total_no_of_working_days;
		$data['designation'] = $designationsdata->designation_name; 
		
		// $earquery = $callConfig->selectQuery('earnings','id,name,payslip_name','','','','');
		// $earning = $callConfig->getAllRowsNew($earquery);
		// foreach ($earning as $earn) {
		// 	$earns[$earn->id] = $earn->payslip_name;
		// }
		// $actual_amount =0; $dedaction_amount = 0;
		// foreach ($stmp as  $value) {
		// 	if($value->salary_component_type !=1){
		// 		$actuals['name'] = $earns[$value->component_id];
		// 		$actuals['amount'] = $value->salary_component_actual;
		// 		$actual_amount = $actual_amount+$value->salary_component_actual;
		// 		$actual[] =$actuals;
		// 	}else{
		// 		$dedactions['name'] = $earns[$value->component_id];
		// 		$dedactions['amount'] = $value->salary_component_actual;
		// 		$dedaction_amount = $dedaction_amount+$value->salary_component_actual;
		// 		$dedaction[] = $dedactions;
		// 	}
		// 	//print_r($value); exit;
		// }
		$earquery = $callConfig->selectQuery('earnings','id,name,payslip_name,display_in_payslip, calculation_mode','','','','');
		$earning = $callConfig->getAllRowsNew($earquery);
		foreach ($earning as $earn) {
			$earns[$earn->id] = $earn->payslip_name;
			$allearns[$earn->id] = (array)$earn;
		}
		$actual_amount =0; $dedaction_amount = 0;
		foreach ($stmp as  $value) {
			if($allearns[$value->component_id]['display_in_payslip']==0){
				if($allearns[$value->component_id]['calculation_mode'] =='ADD'){
					$actuals['name'] = $earns[$value->component_id];
					$actuals['amount'] = $value->salary_component_actual;
					$actual_amount = $actual_amount+$value->salary_component_actual;
					$actual[] =$actuals;
				}
				if($allearns[$value->component_id]['calculation_mode'] =='MINUS'){
					$dedactions['name'] = $earns[$value->component_id];
					$dedactions['amount'] = $value->salary_component_actual;
					$dedaction_amount = $dedaction_amount+$value->salary_component_actual;
					$dedaction[] = $dedactions;
				}
			}
			//print_r($value); exit;
		}
		$data['actual'] = $actual;
		$data['dedaction'] = $dedaction;
		$data['actual_amount'] = number_format($actual_amount,0);
		$data['dedaction_amount'] = number_format($dedaction_amount,0);
		$data['net'] = number_format( $actual_amount-$dedaction_amount,0); 
		$data['sql']= $query1stm;
		// print_r($data);
		// exit;
		return $data;
	}
	function genratecandidatetoemployee($post,$get){
		global $callConfig;
		$returnarray = array();
		$returnarray['post'] = $post; 
		$candidate_id=$post['candidate_id'];
		$cd_query = $callConfig->selectQuery('candidates','*','id='.$candidate_id,'','','');
		$cddata = $callConfig->getRowNew($cd_query);
		$emp_code = $this->toGetApplicationSerialNumber('employees');
		$user_id = $userdata->id;
		$where = "trim(user_email) = '?'";
		$params = [$where, $cddata->email_id];
		$res=$callConfig->updateRecord('application_users',array('is_active'=>'1'),'','',$where);
		$returnarray['update_old_user_email'] = $res;
		$where = "trim(user_email) = '?'";
		$params = [$where, $cddata->email_id];
		$res=$callConfig->updateRecord(MAINDB.'.user_registered_groups',array('is_active'=>'1'),'','',$where);
		$params = [$res, $callConfig->updateRecord, $where];
		$returnarray['update_old_user_email_group'] = $res;
		if($post['submit_employee'] == "E"){
			$post['user_type'] == "5";
		}else{
			$post['user_type'] == "1";
		}
		$role = json_encode(array(EMPLOYEE_ONBOARDING,EMPLOYEE_ROLE));
		$insert_array = array('company_id'=>$get['company_id'],'first_name'=>$cddata->first_name,'middle_name'=>$cddata->middle_name,'last_name'=>$cddata->last_name,'user_email'=>$post['email'],'user_mobile'=>$cddata->phone,'display_name'=>$cddata->display_name,'user_type'=>$post['user_type'],'role_id'=>$role);
		$insert = $callConfig->insertRecord('application_users',$insert_array); 
		$returnarray['new_user_create'] = $insert;
		$lastuserid = $insert['LASTID'];
		if($insert['status'] == "sucess" OR $post['submit_employee'] == "E"){
			$query = "SELECT application_module,id,role_short_code FROM application_roles where company_id = '?' AND (role_short_code ='".EMPLOYEE_ONBOARDING."' OR role_short_code ='".EMPLOYEE_ROLE."') AND is_active = 0";
			$params = [$query, $get];
			$roleresult = $callConfig->getAllRowsNew($query);
			foreach($roleresult  as $roleres){ 
				$insertarray = array("company_id"=>$get['company_id'],"user_id"=>$lastuserid,"module_setup_id"=>$roleres->application_module,"application_module_id"=>$roleres->application_module,"role_id"=>$roleres->id,"role_short_code"=>$roleres->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$insert = $callConfig->insertRecord('application_user_module_roles',$insertarray);
			}
			$emailarray['user_type'] = $post['user_type'];
			$emailarray['user_company_id'] = $get['company_id'];
			$emailarray['session_company_id'] = $get['company_id'];
			$emailarray['send_user_id'] = array($res['LASTID']);
			$emailarray['creater_user_id'] = array($post['user_id']);
			$emailarray['to_company_id'] = $get['company_id'];
			$emailarray['user_display_name'] = $cddata->display_name;
			$emailarray['user_email_id'] = $post['email'];
			$emailarray['user_phone_number'] = $cddata->phone;
			$emailarray['default_module_id'] = 7;
			$emailarray['user_id'] =$lastuserid;
			$emailarray['created_by'] = $post['user_id'];
			$emailarray['user_email'] = $post['email'];
			$emailarray['company_id'] = $get['company_id'];
			$emailarray['template_reference_id'] = 3;
			$data = $this->NewUserformate($emailarray,$get['data_name']);
			$query_task = "INSERT INTO employees(`company_id`, `emp_code`, `first_name`, `middle_name`, `last_name`, `display_name`,  `email_id`, `address`, `post_code`, `gender`, `marital_status`, `phone`,  `date_of_birth`, `adhar_no`, `father_name`, `spouse_name`,  `total_experiance`,  `notice_period`, `user_id`, `pan_number`, `is_active`,`vendor_id`,`employee_type`, joining_date , `hr_buddy`, `finance_buddy`, `admin_buddy`, `project_buddy`, `loc_buddy`,relevant_experience,department_id, job_title_id) SELECT  `company_id`, '?' as emp_code, `first_name`, `middle_name`, `last_name`, `display_name`, '?' as email_id,`address`, `post_code`, `gender`, `marital_status`, '?' as  phone, `date_of_birth`, `adhar_no`,  `father_name`, `spouse_name`, `total_experiance`, `notice_period`, '?' as user_id,`pan_number`,'0' as is_active ,vendor_id,'?' as  etype, excepted_doj,'?',candidate_experiances, department_id, job_tittle_id FROM `candidates` WHERE id =? and is_active = 0";
			$params = [$query_task, $emp_code, $post, $post, $lastuserid, $post, $post, $post, $post, $post, $post, $candidate_id];
			$insert_task = $callConfig->executeQuery($query_task);
			$returnarray['employee_create_sql'] = $insert_task;
			$returnarray['employee_create'] = $insert_task;
			if($insert_task['status'] == "sucess"){
				$emp_id=$insert_task['LASTID'];
				$where = "id= '?'";
				$params = [$where, $candidate_id];
				$res33=$callConfig->updateRecord('candidates',array('employee_id'=>$emp_id),'','',$where);
				$returnarray['update_candidate'] =$res33;
				$res=$callConfig->updateRecord('salary_template',array('employee_id'=>$emp_id),'candidate_id',$candidate_id);
				$company_deatils = json_decode($cddata->candidate_experiances,1);
				$returnarray['company_deatils'] = $company_deatils;
				foreach ($company_deatils as $expekey => $expe) { 
					$postdata_main = array();
					$postdata_main['emp_id'] = $emp_id;
					$postdata_main['company_name'] = $expe['company_name'];
					$postdata_main['job_descrption'] = $expe['job_descrption'];
					$postdata_main['work_from'] = $expe['work_from'];
					$postdata_main['work_to'] = $post['work_to'];
					$postdata_main['created_by'] = $get['user_id'];
					$res=$callConfig->insertRecord('emp_work_experiences',$postdata_main);
					$returnarray['insert_company_deatils'][] = $res;
				}
				$employee_dependents = json_decode($cddata->employee_dependents,1);
				$returnarray['employee_dependents'] = $employee_dependents;
				foreach ($employee_dependents as $depkey => $dep) { 
					$postdata_dep = array();
					$postdata_dep['emp_id'] = $emp_id;
					$postdata_dep['dependent_name'] = $dep['dependent_name'];
					$postdata_dep['dependent_relationship_type'] = $dep['dependent_relationship_type'];
					$postdata_dep['dependent_relationship'] = $dep['dependent_relationship'];
					$postdata_dep['dependent_date_of_birth'] = $dep['dependent_date_of_birth'];
					$postdata_dep['created_by'] = $get['user_id'];
					$res=$callConfig->insertRecord('emp_dependents',$postdata_dep);
					$returnarray['emp_dependents'][] = $res;
				}
				$candidate_qualifications = json_decode($cddata->candidate_qualifications,1);
				$returnarray['emp_educations'] = $candidate_qualifications;
				foreach ($candidate_qualifications as $edukey => $edu) { 
					$postdata_edu	 = array();
					$postdata_edu['emp_id'] = $emp_id;
					$postdata_edu['institute_name'] = $edu['dependent_name'];
					$postdata_edu['education_type'] = $edu['education_type'];
					$postdata_edu['start_date'] = $edu['start_date'];
					$postdata_edu['end_date'] = $edu['end_date'];
					$postdata_edu['score'] = $edu['score'];
					$postdata_dep['created_by'] = $get['user_id'];
					$res=$callConfig->insertRecord('emp_educations',$postdata_edu);
					$returnarray['emp_educations_res'][] = $res;
				}
				$source = "uploads/?/Documents";
				$params = [$source, $get, $get, $candidate_id];
				$destination = "uploads/?/Documents";
				$params = [$destination, $get, $get, $emp_id];
				$candocu = $callConfig->selectQuery('candidate_documents','document_shared',"candidate_id='?' and document_shared != '' and is_active = 0",'','','');
				$params = [$candocu, $callConfig->selectQuery, $candidate_id];
				$candocudata = $callConfig->getAllRowsNew($candocu);
				$returnarray['candocudata'][] = $candocudata;
				//echo "<pre>";print_r($candocudata); //exit;
				foreach ($candocudata as $docu) {
					$move = $this->movefileonedirtoanotherdir($source, $destination,$docu->document_shared);
				}
			}
		}
		return $returnarray;
	}
	function movefileonedirtoanotherdir($source, $destination,$file,$other=''){
		$path = $destination;
		//echo $source."######".$destination."######".$file; exit;
		if(!is_dir($path)) { 		
			$dual_path = trim($path,'/');
			$dual_path = explode('/', $dual_path);
			$epath ="";
			foreach ($dual_path as $dpath) {
				$epath = $epath?"/";
				$params = [$epath, $epath, $dpath];
				if(!is_dir($epath)){
					mkdir($epath); 
				}
			}
		}
		$returns = copy($source."/".$file, $destination."/".$file);
		$params = [$returns, $source, $file, $destination, $file];
		return $returns;
	}
	function genratecandidatesalarytemplate_comment($post,$get){
		global $callConfig;
		$offer_post['job_grade']=$post['job_grade'];
		$offer_post['pay_template']=$post['pay_template'];
		$offer_post['reporting_date']= date('Y-m-d',strtotime($_POST['reporting_date']));
		$offer_post['offer_validity']=date('Y-m-d',strtotime($_POST['offer_validity']));
		$tempa = $this->updateTablesdata($offer_post, 'candidates', 'id',$post['candidate_id']);
		$cd_query = $callConfig->selectQuery('job_grade_templates','*','id='.$post['pay_template'],'','','');
		$cddata = $callConfig->getRowNew($cd_query);
		
		$candiquery = $callConfig->selectQuery('candidates','*','id='.$post['candidate_id'],'','','');
		$cadndidata = $callConfig->getRowNew($candiquery);

		$whr1 = "is_active = 0 AND on_the_day=1 AND before_or_after='BF'";
		$task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
		$task_data = $callConfig->getAllRowsNew($task_query);		

		foreach ($task_data as $key => $tasks) {
			if($tasks->action_frequency == 'DY'){	$day = $tasks->on_the_day; 	}
			if($tasks->action_frequency == 'WK'){	$day = 7; 	}
			if($tasks->action_frequency == 'MON'){	$day = 30; 	}
			$days_ago = date('Y-m-d', strtotime('-? days', strtotime($offer_post['reporting_date'])));
			$params = [$days_ago, $day, $offer_post];
			$task_array = array('company_id'=>$get['company_id'],'transaction_table'=>'candidates','transaction_id'=>$post['candidate_id'],'task_id'=>$tasks->id,'assigned_to'=>$cadndidata->hr_connect,'action_date'=>$days_ago,'revised_action_date'=>$days_ago,'actual_action_date'=>$days_ago,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
		}

		$query = $callConfig->selectQuery('salary_template','*','id='.$cddata->salary_template_id,'','','');
		$data = $callConfig->getRowNew($cd_query);
		$query_sal = "INSERT INTO `salary_template`( `company_id`, `candidate_id`, `description`, `annual_ctc`, `company_cost_monthly`, `company_cost_yearly`, `note`, `is_active`, `current_status`) SELECT  `company_id`, '?'  as candidate_id, `description`, `annual_ctc`, `company_cost_monthly`, `company_cost_yearly`, `note`, `is_active`, `current_status` FROM `salary_template` WHERE id='?'";
		$params = [$query_sal, $post, $cddata->salary_template_id];
		$insert_task = $callConfig->executeQuery($query_sal);
		$salay_id=$insert_task['LASTID'];
		$quer_com ="INSERT INTO `salary_template_components`( `company_id`, `salary_template_id`, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status`) select `company_id`, '?' as salary_template_id, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status` FROM salary_template_components where salary_template_id = '?'";
		$params = [$quer_com, $salay_id, $cddata->salary_template_id];
		$insert_task1 = $callConfig->executeQuery($quer_com);
		return $insert_task;
	}
	function genratecandidatesalarytemplate($post,$get){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$offer_post['job_grade']=$post['job_grade'];
		$offer_post['pay_template']=$post['pay_template'];
		//$expected_ctc = $post['expected_ctc'] !=""?$post['expected_ctc']:$post['CTC'];
		
		$offer_post['current_status']=ST_LOIGENERATED;
		//$offer_post['duration']=$post['duration'];
		$offer_post['reporting_date']= $this->dateconvertdbformat($_POST['reporting_date']);
		$offer_post['offer_validity']=$this->dateconvertdbformat($_POST['offer_validity']);
		

		$cd_query = $callConfig->selectQuery('job_grade_templates','*','id='.$post['pay_template'],'','','');
		$cddata = $callConfig->getRowNew($cd_query);
		

		if($post['salary_template'] == "Y"){
			$post['send_mail'] = 'N';
			$addorupdatesalary = $this->addorupdatesalary($post, $get);
			$return['addorupdatesalary'] = $addorupdatesalary;
			$salary_template_id = $addorupdatesalary['LASTID'];
		
		}else{
			//create Salary Templates
			$query_sal = "INSERT INTO `salary_template`( `company_id`, `candidate_id`, `description`, `ctc_yearly`,ctc_monthly, `note`, `is_active`, `current_status`) SELECT  `company_id`, '?'  as candidate_id, `description`, `ctc_yearly`,ctc_monthly, `note`, `is_active`, `current_status` FROM `salary_template` WHERE id='?'";
			$params = [$query_sal, $post, $cddata->salary_template_id];
			$insert_task = $callConfig->executeQuery($query_sal);
			$return['query_sal'] = $query_sal;
			$salay_id=$insert_task['LASTID'];
			
			$quer_com ="INSERT INTO `salary_template_components`( `company_id`, `salary_template_id`, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status`) select `company_id`, '?' as salary_template_id, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status` FROM salary_template_components where salary_template_id = '?'";
			$params = [$quer_com, $salay_id, $cddata->salary_template_id];
			$insert_task1 = $callConfig->executeQuery($quer_com);
			$return['salary_template_components'] = $quer_com;
			$salary_template_id = $cddata->salary_template_id;
		}
		//End create Salary Templates
		$ctcsql = "SELECT stc.component_name, stc.yearly_amount, ear.code FROM salary_template_components stc, earnings ear WHERE  stc.company_id = '?' AND stc.company_id = ear.company_id AND  ear.code = 'CTC' AND stc.component_id = ear.id AND stc.salary_template_id = '?' AND stc.is_active = 0 AND stc.is_active = ear.is_active";
		$params = [$ctcsql, $post, $salary_template_id];
		$ctcres = $callConfig->getRowNew($ctcsql);
		$offer_post['offer_ctc']=$ctcres->yearly_amount;

		$offer_post['salary_template_id'] = $salary_template_id;
		$tempa = $this->updateTablesdata($offer_post, 'candidates', 'id',$post['candidate_id']);
		$return['offer_post'] = $offer_post;


		
		//crteate candidates to users
		
		$insert_array = array('company_id'=>$get['company_id'],'candidate_id'=>$post['candidate_id'],'offer_amount'=>$ctcres->yearly_amount,'job_grade'=>$post['job_grade'],'pay_template_id'=>$salary_template_id,'reporting_date'=>$this->dateconvertdbformat($_POST['reporting_date']),'offer_validity'=>$this->dateconvertdbformat($_POST['offer_validity']),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
		$insert = $callConfig->insertRecord('candidate_offer_histories',$insert_array);

	
		//if($allreadycreateres->id ==""){
			//$candidateary = array("user_email"=>$cadndidata->email_id,"user_mobile"=>$cadndidata->phone,"display_name"=>$cadndidata->display_name,"job_title_id"=>$cadndidata->job_tittle_id,'candidate_id'=>$post['candidate_id'],'company_id'=>$post['company_id'],'user_id'=>$get['user_id']);
			$get['candidate_id'] = $post['candidate_id'];
			// //$this->candidateautoregistation($candidateary,$get);
			$this->newcandidateautoregistation($get);
			

			$candiquery = $callConfig->selectQuery('candidates','*','id='.$post['candidate_id'],'','','');
			$cadndidata = $callConfig->getRowNew($candiquery);
		
		//}
       //crteate candidates to users 
		//task creatins

		$tasquery = "SELECT task_id, assigned_to FROM `transaction_activity_schedule` WHERE company_id = '?' AND transaction_table = 'candidates' AND transaction_id = '?' AND is_active = 0";
		$params = [$tasquery, $post, $post];
		$tasres = $callConfig->getAllRowsNew($tasquery);
		$alreadytaskid = array();
		foreach($tasres  as $roleres){
			$alreadytaskid[$roleres->task_id][$roleres->assigned_to] = "Y"; 
		}	
		$return['tasquery'] =$tasquery;
		$return['alreadytaskid'] =$alreadytaskid;
		$whr1 = "company_id = '?' AND is_active = 0 AND process_type='CR'";
		$params = [$whr1, $get];
		$task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
		$task_data = $callConfig->getAllRowsNew($task_query);	 
		$return['task_query'] =$task_query;
		$transaction_date = $offer_post['reporting_date'];
			foreach ($task_data as $key => $tasks) {
				$assigned_to = $approval_user_id = "";
				$days_ago = $transaction_date;
				if($tasks->action_no =='1'){
					//$return['action_frequency'][] = $tasks->action_frequency;
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
					$params = [$days_ago, $day, $transaction_date];
				}else{
					$days_ago = $transaction_date;
				}
				if($tasks->assigned_to =="RM"){
					$assigned_to = $cadndidata->rm_id;
				}elseif($tasks->assigned_to =="HM"){
					$assigned_to = $cadndidata->hm_id;
				}elseif($tasks->assigned_to =="CAND"){
					$assigned_to = $cadndidata->user_id;
				}
				if(!isset($alreadytaskid[$tasks->id][$assigned_to])){
					if($tasks->approval_type =="HM"){
						$approval_user_id = $cadndidata->hm_id;
					}else{
						$approval_user_id = $cadndidata->rm_id;
					}
					if($assigned_to !="" AND $approval_user_id !=""){
						$planned_day = $tasks->planned_day !=""?$tasks->planned_day:0;
						$finish_date = date('Y-m-d', strtotime($planned_day.' days', strtotime($days_ago)));
						$params = [$finish_date, $planned_day, $days_ago];
						$task_array = array('company_id'=>$get['company_id'],'transaction_table'=>'candidates','transaction_id'=>$cadndidata->id,'task_id'=>$tasks->id,'assigned_to'=>$assigned_to,'approval_user_id'=>$approval_user_id,'action_date'=>$days_ago,'check_list'=>$tasks->check_list,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'finish_date'=>$finish_date,'process_id'=>$tasks->process_id,'task_name'=>$tasks->task_name);
						$res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
						$return['transaction_activity_schedule'][] =$res;
					}
					// $event_array = array('company_id'=>$get['company_id'],'event_category_id'=>'4','user_id'=>$assigned_to,'event_name'=>$tasks->task_name,'event_short_name'=>$tasks->task_short_name,'event_description'=>$tasks->description,'start_date'=>$days_ago,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
					// $everes = $callConfig->insertRecord('employee_events',$event_array);
					// $return['employee_events'][] =$everes;
					// $return['transaction_activity_schedule'][] = $task_array;
					//$notiarray = array('notification_message'=>'Schedule Event '.$tasks->task_name,'notification_users'=>array('0'=>$cadndidata->hr_connect),'id'=>$res['LASTID'],'application_table_name'=>'transaction_activity_schedule','company_id'=>$get['company_id'],'option'=>'COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULE','notification_datetime'=>$days_ago);
					//$this->tosendnotifiations($notiarray);
				}
			}
		//End task creatins
		
		//$query = $callConfig->selectQuery('salary_template','*','id='.$cddata->salary_template_id,'','','');
		//$data = $callConfig->getRowNew($cd_query);
	

		//notification
		$job_titlesql = $callConfig->selectQuery('job_titles','job_title','id='.$cadndidata->job_tittle_id,'','','');
		$job_titleres = $callConfig->getRowNew($job_titlesql);

		$replacearray = array("candidates.display_name"=>$cadndidata->display_name,"candidates.job_title"=>$job_titleres->job_title,"candidates.expected_ctc"=>$cadndidata->offer_ctc,"candidates.reporting_date"=>$cadndidata->reporting_date,"candidates.email_id"=>$cadndidata->email_id,"candidates.offer_validity"=>$cadndidata->offer_validity); 
		$replacearray['user_company_id'] = $cadndidata->company_id;
		$replacearray['session_company_id'] = $cadndidata->company_id;
		$replacearray['creater_user_id'] = array($post['user_id']);
		$replacearray['to_company_id'] = $cadndidata->company_id;
		
		$rm_users_sql = "SELECT display_name, user_email, user_mobile FROM application_users WHERE company_id = '?'  AND is_active = 0 AND id='?'";
		$params = [$rm_users_sql, $cadndidata->company_id, $cadndidata->rm_id];
		$rm_users_res = $callConfig->getRowNew($rm_users_sql);
		$replacearray['rm_display_name'] = $rm_users_res->display_name;
		$replacearray['rm_email_id'] = $rm_users_res->user_email;
		$replacearray['rm_contact_number'] = $rm_users_res->user_mobile;
		$replacearray['created_by'] = $get['user_id'];
		$emill = $this->sendNotificationAndEmails('',$replacearray,10); 
		//notifiction

		$return['emill'] = $emill;
		$return['replacearray'] = $replacearray;
		return $return;
	}

	function usernotify($post,$get){
		global $callConfig;
		$notarry['notification_message'] = $post['comment'];
		$notarry['notification_users'] = array('0'=>$post['send_id']);
		$notarry['id'] = $post['send_id'];
		$notarry['company_id']= $get['company_id'];
		$notarry['option'] = $get['option'];
		$notarry['type'] = "INFORMATION";
		$this->tosendnotifiations($notarry);
	}
	function payrollfromtodates($get){
		global $callConfig;
		if($get['pay_area_id']!=""){
			$cfquery = "SELECT pay_run_period FROM pay_areas WHERE company_id =? AND id=? AND is_active = 0";
			$params = [$cfquery, $get, $get];
			$cfdata = $callConfig->getRowNew($cfquery);
			$payday = $cfdata->pay_run_period;
		}
 		if($payday =="" || !isset($get['pay_area_id'])||$get['pay_area_id']==""){
			$cfquery = $callConfig->selectQuery('config','default_payroll_day','id=1','','','');
			$cfdata = $callConfig->getRowNew($cfquery);
			$payday = $cfdata->default_payroll_day;
		}
		
		$date2= $get['pay_year']."-?-".$payday;
		$params = [$date2, $get, $get, $payday];
		if($payday>28){
			$date2= $get['pay_year']."-?-28";
			$params = [$date2, $get, $get];
			$month=$get['pay_month']-1;
			$date1= $get['pay_year']."-?-28";
			$params = [$date1, $get, $month];
			$date1=date("Y-m-d", strtotime('last day of', strtotime($date1)));
			$date2=date("Y-m-d", strtotime('last day of', strtotime($date2)));
		}else{
			$date1 = date("Y-m-d", strtotime('-1 months', strtotime($date2)));
		}
		//echo "last work day:".$date2;
		//$date2 = date("Y-m-d", strtotime('+0 day', strtotime($date2)));
		
		//echo "<br>last work day last month:".$date1;
		$date1 = date("Y-m-d", strtotime('+1 day', strtotime($date1)));
		//echo "<br>last work day last month add one day:".$date1;
		$return = array('date1'=>$date1,'date2'=>$date2);
		//print_r($return); exit;
		return  $return;
	}
	
	function togetcurrentmonthexitemployes($get){
		global $callConfig;
		$cfquery = $callConfig->selectQuery('config','default_payroll_day','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);

		//$date1=date('Y')."-".date("m", strtotime("-1 Months"))."-".$cfdata->default_payroll_day;
		$date2= $get['pay_year']."-?-".$cfdata->default_payroll_day;
		$params = [$date2, $get, $get, $cfdata->default_payroll_day];
		$date1 = date("Y-m-d", strtotime('-1 months', strtotime($date2)));
		$query = "SELECT emp_id,settle_on from employee_terminations where  `settle_on` <= '?' AND `settle_on` > '?'";
		$params = [$query, $date2, $date1];
		//echo $query;
		$empexitdata = $callConfig->getAllRowsNew($query);
		//print_r($empexitdata); exit;
		foreach ($empexitdata as $exit) {
			$emp_ids[] = $exit->emp_id;
			$returns[$exit->emp_id]['final_settlement_date']= $exit->settle_on;
		}
		$emp_id = implode(',', (array)$emp_ids);
		//print_r($emp_id);
		$query2 = "SELECT id,display_name,image,bank_id,bank_link,bank_account_no,user_id from employees where id IN (?)";
		$params = [$query2, $emp_id];
		$empdatas =  $callConfig->getAllRowsNew($query2);
	   	//echo $query2; exit;

		foreach ($empdatas as  $empdata) {
			$returns[$empdata->id]['id'] = $empdata->id;
			$returns[$empdata->id]['name'] = $empdata->display_name; 
			$returns[$empdata->id]['user_id'] = $empdata->user_id; 
			$returns[$empdata->id]['image'] = $empdata->image;
		}
		foreach ($returns as  $key=>$value) {
			$hquery = $callConfig->selectQuery('payroll_exclusions','id','emp_id=? AND category_id = 5 AND pay_year=? AND pay_month='.$get['pay_month'],'','','');
			$params = [$hquery, $callConfig->selectQuery, $value, $get, $get];
	    	//echo $hquery;
			$hudata = $callConfig->getRowNew($hquery);
			//print_r($hudata);
			$returns[$key]['table_id'] = $hudata->id;
			if($hudata->id ==""){ 
				$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$value['id'],'category_id'=>'5','pay_year'=>$get['pay_year'],'pay_month'=>$get['pay_month'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
				// print_r($insert_array);// exit;
				$res = $callConfig->insertRecord('payroll_exclusions',$insert_array);
	           // print_r($res);
				$returns[$key]['table_id'] = $res['LASTID'];
			}
		}
		return $returns;
		
	}
	function togetcurrentmonthsalaryrevisions($get){
		global $callConfig;
		//echo "ravi"; exit;
		$cfquery = $callConfig->selectQuery('config','default_payroll_day','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);
		$date2=date('Y')."-".date('m')."-".$cfdata->default_payroll_day;
		$params = [$date2, $cfdata->default_payroll_day];
		$date1 = date("Y-m-d", strtotime('-1 months', strtotime($date2)));
		$query = "SELECT `employee_id`,annual_ctc from salary_template where  `is_active`=0 AND `affected_date` <= '?' AND `affected_date` > '?'";
		$params = [$query, $date2, $date1];
		//echo $query;
		$empexitdata = $callConfig->getAllRowsNew($query);
		foreach ($empexitdata as $exit) {
			$emp_ids[] = $exit->employee_id;
			$returns[$exit->employee_id]['annual_ctc']= $exit->annual_ctc;
		}
		$emp_id = implode(',', (array)$emp_ids);
		$query2 = "SELECT id,display_name,image,bank_id,bank_link,bank_account_no,user_id from employees where id IN (?)";
		$params = [$query2, $emp_id];
		//echo $query2;
		$empdatas =  $callConfig->getAllRowsNew($query2);
		foreach ($empdatas as  $empdata) {
			$returns[$empdata->id]['id'] = $empdata->id;
			$returns[$empdata->id]['name'] = $empdata->display_name; 
			$returns[$empdata->id]['user_id'] = $empdata->user_id; 
			$returns[$empdata->id]['image'] = $empdata->image;
		}
		$query3 = "SELECT id,employee_id,annual_ctc,MAX(affected_date) as affected_date from salary_template where employee_id IN (?) AND is_active=1  group by employee_id";
		$params = [$query3, $emp_id];
		//echo $query2;
		$empdata3 =  $callConfig->getAllRowsNew($query3);
		foreach ($empdata3 as  $empdat) {
			$returns[$empdat->employee_id]['p_annual_ctc'] = $empdat->annual_ctc;
		}
		foreach ($returns as  $key=>$value) {
			$hquery = $callConfig->selectQuery('payroll_exclusions','id','emp_id=? AND category_id = 6 AND pay_year=? AND pay_month='.$get['pay_month'],'','','');
			$params = [$hquery, $callConfig->selectQuery, $value, $get, $get];
	    	//echo $hquery;
			$hudata = $callConfig->getRowNew($hquery);
			//print_r($hudata);
			$returns[$key]['table_id'] = $hudata->id;
			if($hudata->id ==""){ 
				$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$value['id'],'category_id'=>'6','pay_year'=>$get['pay_year'],'pay_month'=>$get['pay_month'],'is_active'=>'0','comments'=>json_encode($value),'current_status'=>'0','created_by'=>$get['user_id']);
				// print_r($insert_array);// exit;
				$res = $callConfig->insertRecord('payroll_exclusions',$insert_array);
	           // print_r($res);
				$returns[$key]['table_id'] = $res['LASTID'];
			}
		}
		return $returns;
	}
	function togetcurrentmonthsalaryrevisions1($get){
		global $callConfig;
		//echo "ravi 123"; exit;
		$cfquery = $callConfig->selectQuery('config','default_payroll_day','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);
		$date2=date('Y')."-".date('m')."-".$cfdata->default_payroll_day;	
		$params = [$date2, $cfdata->default_payroll_day];
		$date1 = date("Y-m-d", strtotime('-1 months', strtotime($date2)));
		$query = "SELECT emp_id,approved_date from  salary_revisions where  `approved_date` <= '?' AND `approved_date` > '?'";
		$params = [$query, $date2, $date1];
		//echo $query;
		$empexitdata = $callConfig->getAllRowsNew($query);
		//print_r($empexitdata); exit;
		foreach ($empexitdata as $exit) {

			$emp_ids[] = $exit->emp_id;
			$returns[$exit->emp_id]['approved_date']= $exit->approved_date;
		}
		$emp_id = implode(',', (array)$emp_ids);
		//print_r($emp_id);
		$query2 = "SELECT id,display_name,image,bank_id,bank_link,bank_account_no from employees where id IN (?)";
		$params = [$query2, $emp_id];
		$empdatas =  $callConfig->getAllRowsNew($query2);
	   	//echo $query2; exit;

		foreach ($empdatas as  $empdata) {
			$returns[$empdata->id]['id'] = $empdata->id;
			$returns[$empdata->id]['name'] = $empdata->display_name; 
			$returns[$empdata->id]['image'] = $empdata->image;
		}
		return $returns;
	}
	function togetemployeeattendanacecalucater($post){
		global $callConfig;
		//echo "<pre>";
		$emp_id = $post['emp_id'];
		$cfquery = $callConfig->selectQuery('attendance_settings','*','is_active=0','','','');
		$attdata = $callConfig->getRowNew($cfquery);


		$cfquery = $callConfig->selectQuery('config','default_payroll_day','id=1','','','');
		$cfdata = $callConfig->getRowNew($cfquery);
		$date1=date('Y')."-".date("m", strtotime("-1 Months"))."-".$cfdata->default_payroll_day;
		$params = [$date1, $cfdata->default_payroll_day];
		$date2=date('Y')."-".date('m')."-".$cfdata->default_payroll_day;
		$params = [$date2, $cfdata->default_payroll_day];
		$query = "SELECT * from  employee_daily_attendances where is_active=0 AND employee_id = ? AND  `attendace_date` <= '?' AND `attendace_date` > '?'";
		$params = [$query, $emp_id, $date2, $date1];
		//echo $query;
		//print_r($attdata); exit;
		$attend = array();
		$empattdata = $callConfig->getAllRowsNew($query);
		foreach ($empattdata as  $empdata) {
			$att['indate'] = $empdata->attendace_date;
			$att['work'] = '100';
			$attend[] = $att;
			$clock_in_ex = explode(":", $clock_in);
			$clock_in_sec = $clock_in_ex['0'] * 3600 +  $clock_in_ex['1'] * 60 + $clock_in_ex['0'] * 60;
			$clock_out_ex = explode(":", $clock_out);
			$clock_out_sec = $clock_out_ex['0'] * 3600 +  $clock_out_ex['1'] * 60 + $clock_out_ex['0'] * 60;
			//$clock_in = strtotime($empdata->clock_in);
			//$clock_out = strtotime($empdata->clock_out);
			// $diff = $clock_out - $clock_in;
			// date("h:i:s",$diff)."<br>";
			// strtotime($attdata->minimum_hours_required)."//". date("h:i:s",strtotime($attdata->minimum_hours_required)) ."<br>";
			// strtotime($attdata->break_time)."<br>";
			//  strtotime($attdata->grace_perieod)."<br>";
			$clockcheck = strtotime($attdata->minimum_hours_required)  - strtotime($attdata->break_time) ;
			echo $clockcheck."MAA<br>";
			$clockcheck = $clockcheck - strtotime($attdata->grace_perieod);
			echo $clockcheck."<br>";
			echo date("h:i:s",$clockcheck);
			//calucate minmum hours - grace 
			exit;

			// if($clockcheck>){

			// }
		}
		print_r($empattdata); exit;

	}
	function togetpresentationdetails($get){
		global $callConfig;
		//echo "<pre>";
		//print_r($get);
		//echo "man"; exit;
		$preurl = $callConfig->selectQuery('presentation','*','id='.$get['presentation_id'],'display_sequence ASC','','');
		$predata= $callConfig->getRowNew($preurl);
		//print_r($predata);
		$cfquery = $callConfig->selectQuery('presentation','*','is_active=0 AND parent_id='.$get['presentation_id'],'display_sequence ASC','','');
		$prdata = $callConfig->getAllRowsNew($cfquery);
		$returns = array();	
		foreach ($prdata as $pdata) {
			$pcquery = $callConfig->selectQuery('presentation_content','*','is_active=0 AND presentation_id='.$pdata->id,'display_sequence ASC','','');
			//echo $pcquery; exit;
			$pcdata = $callConfig->getAllRowsNew($pcquery);
			$return = $pdata;
			$return->data = $pcdata; 
			$returns[] = $return;
		}
		$return1['presentation'] = $predata;
		$return1['presentation_data'] = $returns;
		//print_r($returns);
		return $return1;
	}
	function togetemployrrearndetails($post){
		global $callConfig;
		$queryearnings ="SELECT * from  employee_other_earnings where is_active=0 AND employee_id = ? AND  `earning_date` <= '?' AND `earning_date` > '?'";
		$params = [$queryearnings, $post, $get, $get];
		$dataearnings = $callConfig->getAllRowsNew($queryearnings);
		//print_r($dataearnings);
		$tax_amout =0;
		foreach ($dataearnings as  $earn) {
			$earns_array['earning_id'] = $earn->earning_id;
			$earns_array['earning_type'] = $earn->earning_type;
			$earns_array['approved_amount'] = $earn->approved_amount;
			$earns_array['claim_amount'] = $earn->claim_amount;
			$earns_array['salary_component_type'] = '1';
			if( $earn['application_table_name']== 'earnings'){
				$earns_array['salary_component_type'] = '0';
			}
			if($earn->tax_value != '0.00'){
				if($earn->tax_type == "P"){
					$tax_amout = $tax_amout+ ($earn->approved_amount*($earn->tax_value/100));
				}else{
					$tax_amout = $tax_amout+$earn->tax_value;
				}
			}
			$earns_data[] = $earns_array;
		}
		
		return $earns_data;
	}
	function togetpresentationpagedetails($get){
		global $callConfig;

		$cfquery = $callConfig->selectQuery('presentation','*','is_active=0 AND id=? AND parent_id='.$get['presentation_id'],'display_sequence ASC','','');
		$params = [$cfquery, $callConfig->selectQuery, $get, $get];
		$prdata = $callConfig->getAllRowsNew($cfquery);
		$returns = array();  
		foreach ($prdata as $pdata) {
			$pcquery = $callConfig->selectQuery('presentation_content','*','is_active=0 AND presentation_id='.$get['page_id'],'display_sequence ASC','','');
           // echo $pcquery; exit;
			$pcdata = $callConfig->getAllRowsNew($pcquery);
			$return = $pdata;
			$return->data = $pcdata;
			$returns[] = $return;
		}
       // print_r($returns); exit;
		return $returns;
	}
	function addorupdateitdeclaration80c($post,$get){
		global $callConfig;
        //echo "ravi<pre>"; print_r($post); exit;
		$query = "SELECT id FROM emp_it_declaration WHERE company_id= '?' and emp_id='?' and assessment_year='?' AND version=?' AND is_active = 0";
		$params = [$query, $get, $post, $post, $post];
		$edata = $callConfig->getRowNew($query);
		if($edata->id ==""){
		
			$insert_array = array('company_id'=> $get['company_id'], 'emp_id'=> $post['emp_id'], 'version_no'=> $post['version_no'], 'taxable_income'=>$post['version_no'] , 'annual_tax'=>$post['annual_tax'] , 'potential_amount'=>$post['potential_amount'] , 'declaration_tax'=>$post['declaration_tax'] , 'gap_tax'=>$post['gap_tax'] , 'assessment_year'=>$post['assessment_year'] , 'current_status'=>'0' , 'is_active'=>'0' ,'created_by'=>$get['user_id'] ,'created_date'=>date('Y-m-d H:i:s'));
			$res = $callConfig->insertRecord('emp_it_declaration', $insert_array);
		}


		$updatequery = "Update emp_it_declaration_80 SET is_active=1 where  company_id= '?' and emp_id='?' and assessment_year='?' AND version=?'";
		$params = [$updatequery, $get, $post, $post, $post];
		$data = $callConfig->executeQuery($updatequery);
		foreach ($post['itd8id'] as $key=>$itd8id) {
			if($post['itd8amount'][$key] !=0){
				$insert_array = array('company_id'=>$get['company_id'],'assessment_year'=>$post['assessment_year'],'emp_id'=>$post['emp_id'],'version'=>$post['version'],'particular'=>$itd8id,'type'=>$post['itd8catid'][$key],'amount'=>$post['itd8amount'][$key],'max_amount'=>$post['max_amount'][$key],'is_active'=>'0','current_status'=>'0','created_date'=>$post['created_date'],'created_by'=>$post['created_by']);
	            //echo "<pre>"; print_r($insert_array); exit;
	            $daas = $callConfig->insertRecord('emp_it_declaration_80',$insert_array); //exit;
	        }
        }
        return $daas;
    }
    function addorupdateitdeclarationotherincome($post){
		global $callConfig;
        //echo "ravi<pre>"; print_r($post); exit;

		$updatequery = "Update emp_it_declaration_other_income SET is_active=1 where  company_id= '?' and emp_id='?' and assessment_year='?' AND version=".$post['version'];
		$params = [$updatequery, $post, $post, $post, $post];
		$data = $callConfig->executeQuery($updatequery);
		foreach ($post['othername'] as $key=>$othername) {
			if($othername !="" && $post['amount'][$key]!=""){
			$insert_array = array('company_id'=>$post['company_id'],'assessment_year'=>$post['assessment_year'],'emp_id'=>$post['emp_id'],'version'=>$post['version'],'particular'=>$othername,'type'=>3,'amount'=>$post['amount'][$key],'is_active'=>'0','current_status'=>'0','created_date'=>$post['created_date'],'created_by'=>$post['created_by']);
            //echo "<pre>"; print_r($insert_array); exit;
            $daas = $callConfig->insertRecord('emp_it_declaration_other_income',$insert_array); //exit;
            }
        }
        return $daas;
    }
    function togetsalarytotaldetails($get){
    	global $callConfig;
    	$ctquery = $callConfig->selectQuery('salary_categories','*','is_active=0','','','');
		//echo $pcquery; exit;
    	$ctdata = $callConfig->getAllRowsNew($ctquery);
    	foreach ($ctdata as $value) {
    		$salcat[$value->id] = $value;
    	}
    	$ssquery = $callConfig->selectQuery('earnings','*','is_active=0','','','');
		//echo $pcquery; exit;
    	$ssdata = $callConfig->getAllRowsNew($ssquery);
    	foreach ($ssdata as $ssdatas) {
    		$ssdataes[strtolower($ssdatas->code)] = $ssdatas;
    	}

    	$pcquery = $callConfig->selectQuery('salary_template','*','is_active=0 AND employee_id='.$get['emp_id'],'','','');
		//echo $pcquery; exit;
    	$pcdata = $callConfig->getRowNew($pcquery);
    	$squery = $callConfig->selectQuery('salary_template_components','*','is_active=0 AND salary_template_id='.$pcdata->id,'','','');
		//echo $pcquery; exit;
    	$sdata = $callConfig->getAllRowsNew($squery);

    	$hquery = $callConfig->selectQuery('emp_it_declaration_houserent','*','is_active=0 AND emp_id=? AND version_no='.$get['version_no'],'','','');
    	$params = [$hquery, $callConfig->selectQuery, $get, $get];
		//echo $hquery; exit;
    	$hudata = $callConfig->getRowNew($hquery);

    	$query80 ="SELECT type , sum(if(max_amount = 0, amount, if(max_amount > amount, amount, max_amount))) tax FROM `emp_it_declaration_80` WHERE is_active=0 And version =? And emp_id =? GROUP BY type";
    	$params = [$query80, $get, $get];
		//echo $query80; exit;
    	$data80 = $callConfig->getAllRowsNew($query80);
		//print_r($data80); exit;
    	$itdcquery =$callConfig->selectQuery('it_declaration_categories','*','is_active=0 AND parent_category_id !=0','','','');
		//echo $hquery; exit;
    	$itdcdata = $callConfig->getAllRowsNew($itdcquery);
    	foreach ($itdcdata as $itdc) {
    		$itdcval[$itdc->id] = $itdc->category_name;
    	}
    	foreach ($data80 as $it80) {
    		$itdc80['name'] = $itdcval[$it80->type];
    		$itdc80['amount'] = $it80->tax;
    		$itdc80s[] = $itdc80;
    	}
    	$slab_query = $callConfig->selectQuery('tax_slads','*',"is_active=0 AND assessment_year='2020-2021'",'','','');
		//echo $slab_query; exit;
    	$slab_data = $callConfig->getAllRowsNew($slab_query);

		//print_r($slab_data); exit;
    	$data = $pcdata;
    	$data->data = $sdata;
    	$maindata['category'] = $salcat;
    	$maindata['components'] = $ssdataes;
    	$maindata['house_rent'] = $hudata;
    	$maindata['tax80c'] = $itdc80s;
    	$maindata['slads'] = $slab_data;
    	$maindata['salary'] = $data;

		//print_r($data); exit;
    	return $maindata;
    }	
    function togetemployeeincometax($post){
    	global $callConfig;
	    //echo "<pre>";	print_r($post); exit;
	    // $array('emp_id'=>$emp->id,'gross_aount'=>$code_data['GS'],'date1'=>$date1,'date2'=>$date2,'valdata'=>$codeactual,'actualval'=>$code_data, 'joining_date'=>$emp->joining_date,'joining_bonus_applicable'=>$datast->joining_bonus_applicable, 'joining_bonus_tax_type'=>$datast->joining_bonus_tax_type, 'joining_bonus_amount'=>$datast->joining_bonus_amount, 'code_data'=>$code_data);
    	$slab_query = $callConfig->selectQuery('tax_slads','*',"is_active=0 AND assessment_year='2024-2025'",'','','');
		//echo $slab_query; exit;
    	$slab_data = $callConfig->getAllRowsNew($slab_query);
    	$tax_slab =(array)$slab_data;
        //print_r($slab_data);
    	$it_declaration_query= $callConfig->selectQuery('emp_it_declaration','*',"is_active=0 AND emp_id = ? AND assessment_year='?'",'','','');
    	$params = [$it_declaration_query, $callConfig->selectQuery, $post, $post];
		//echo $slab_query; exit;
    	$it_declaration = $callConfig->getRowNew($it_declaration_query);
		//print_r($it_declaration);
    	$incomtax = $post['gross_aount'];
    	
		//echo $calslab_total;print_r($it_declaration);print_r($tax_slab); echo $calslab_total; exit;
    	$cal_intax = 0;

    	if(count((array)$it_declaration) >0){
    		if((array)$it_declaration_query->id !=""){
    		$incomtax = $post['gross_aount']-$it_declaration_query->declaration_tax;
    		}
    		$calslab_total = $incomtax; 
    		foreach ($tax_slab as $slab) {
    			$slab =(array)$slab;
    			if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
    				$cal_amount = $calslab_total - ($slab['slad_from']-1);
    				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
    				break;
    			}else{
    				$cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
    				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
    			}
    		}
    	}else{
    		$calslab_total = $post['gross_aount'];  
    		$codeactual  = $post['valdata'];
    		$actualval = $post['actualval'];

    		//$calslab_total = ($codeactual['GS']+17000)-50000;
    		$calslab_total = $codeactual['GS'];
    		$tottal_amount_tax = $calslab_total;
    		$work_month = $this->tocalucalatemonthno($post['joining_date']);
    		
    		//echo "</br>step1--".$calslab_total; 
			//echo "</br>step2--".$work_month; 
    		if($work_month<12){
    			$monthly_calslab_total= ($calslab_total/12);
    			$calslab_total= ($monthly_calslab_total*$work_month);
    			//echo "</br>step3--".$calslab_total; 
    			$join_day = date('j', strtotime($post['joining_date']));
    			if($join_day!=1){
    				$lastmanday = date('t', strtotime($post['joining_date']));
    				//echo "</br>step3-1-lastmanday-".$lastmanday; 
    				$remanday = $join_day-1;;
    				//echo "</br>step3-2-remanday-".$remanday;
    				$calslab_total = $calslab_total - (($monthly_calslab_total/$lastmanday)*$remanday);
    				//echo "</br>step4--".$calslab_total; 
    			}
    			if(isset($codeactual['MIP'])){
    				$calslab_total = $calslab_total + (($codeactual['MIP']/12)*$work_month);
    				//echo "</br>step4-1-".$calslab_total; 
    				$calslab_total = $calslab_total -50000;
    				//echo "</br>step4-2-".$calslab_total; 
    			}
    		}else{
    			if(isset($codeactual['MIP'])){
    				$calslab_total = ($codeactual['GS']+$codeactual['MIP']);
    				$calslab_total = $calslab_total -50000;
    			}
    		}
    		//echo "<pre>step5".$calslab_total; exit;
    		//$calslab_total =$calslab_total-50000;
    		if($post['emp_id']<10){ 
    			$calslab_total = $calslab_total + $actualval['GRATUITY'];
    		}
    		//echo "BBB<pre>".$calslab_total;
    		//print_r($codeactual);print_r($tax_slab); echo $calslab_total; exit;
    		foreach ($tax_slab as $slab) {
    			$slab =(array)$slab;
    			if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
    				$cal_amount = $calslab_total - ($slab['slad_from']-1);
    				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
    				break;
    			}else{
    				$cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
    				$cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
    			}
    		}

    	}
    	if($cal_intax<=25000){
    		$cal_intax = 0;
    	}else{
    		$cal_mon_intax = $cal_intax/$work_month;
    		if($work_month<12 && strtotime($post['date1']) <= strtotime($post['joining_date']) &&  $post['date2'] >= strtotime($post['joining_date'])){
				$cal_mon_pp = $cal_intax/$calslab_total;
				$cal_mon_intax = $post['gross_aount'] *$cal_mon_pp;
			}     		
    	}
    	$tax_val = array(); 

    	$tax_query = $callConfig->selectQuery('extra_taxes','*',"is_active=0",'','','');
    	$tax_data = $callConfig->getAllRowsNew($tax_query);
    	
    	if(count((array)$tax_data)>0){	
    		foreach($tax_data as $tax_dat){
    			if($tax_dat->component_code =='TAXABLE_INCOME_STD' || $tax_dat->component_code =='INCOME_TAX_PER_MONTH'|| $tax_dat->component_code =='SURCHARGES_CESS'){
    				if($tax_dat->component_code =='TAXABLE_INCOME_STD'){
    					$tax_val['TAXABLE_INCOME_STD'] = $calslab_total;
    				}
    				if($tax_dat->component_code =='INCOME_TAX_PER_MONTH'){
    					$tax_val['INCOME_TAX_PER_MONTH'] = $cal_mon_intax;
    				}
    				if($tax_dat->component_code =='SURCHARGES_CESS'){
    					if(!isset($tax_val['TAXABLE_INCOME_STD'])){
    						$tax_val['TAXABLE_INCOME_STD'] = $calslab_total;
    					}
    					if(!isset($tax_val['EDUCATION_CESS'])){
    						$tax_val['EDUCATION_CESS'] = 0;
    					}
    					if(!isset($tax_val['EDUCATION_CESS'])){
    						$tax_val['EDUCATION_CESS'] = 0;
    					}
    					if(!isset($tax_val['INCOME_TAX_PER_MONTH'])){
    						$tax_val['INCOME_TAX_PER_MONTH'] = $cal_mon_intax;
    					}
    					if($tax_val['TAXABLE_INCOME_STD'] >= $tax_dat->check_value){
    						//echo "YYYY";
    						$tax_val['SURCHARGES_CESS'] = ($tax_val['INCOME_TAX_PER_MONTH']+$tax_val['EDUCATION_CESS'])*($tax_dat->rate/100);
    					}
    				}
    			}else{
    				$tax_val[$tax_dat->component_code] = ($tax_val[$tax_dat->applicability_tax])*($tax_dat->rate/100);
    			}
    		}
    	}
    	//echo $work_month."/<pre>".$post['joining_date']."///";echo $cal_intax."////"; echo $cal_mon_intax."////"; echo $calslab_total."++++++++".$tottal_amount_tax; exit;
    	//print_r($tax_val);echo $cal_intax."////"; echo $cal_mon_intax."////"; echo $calslab_total; exit;

    	$tax_val['TDS'] = $tax_val['INCOME_TAX_PER_MONTH']+$tax_val['EDUCATION_CESS']+$tax_val['SURCHARGES_CESS'];
    	//echo "<pre>"; print_r($post); print_r($tax_val);
    	if(isset($post['code_data']['ARREAR_ITAX']) && $post['code_data']['ARREAR_ITAX']!=0){
    		$tax_val['TDS'] = $tax_val['TDS']+$post['code_data']['ARREAR_ITAX'];
    	}


    	//echo "<pre>return".$cal_intax."montly tax".$cal_intax/12; print_r($tax_val); exit;
    	return $tax_val;
    }
	
    function employeeclocks($post,$get){
    	global $callConfig;
    	$query = $callConfig->selectQuery('employee_daily_attendances','*',"is_active=0 AND employee_id='?' AND attendace_date='?'",'','','');
    	$params = [$query, $callConfig->selectQuery, $post, $post];
		//echo $query ;
    	$data = $callConfig->getRowNew($query);
		//print_r($data); 
    	if($data->id ==""){ echo "ravi";
    	$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$post['emp_id'],'attendace_date'=>$post['date'],'clock_in'=>$post['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>$post['in_out'],'device_type'=>$post['device_type']);
           // echo "<pre>"; print_r($insert_array); exit;
    	$daas = $callConfig->insertRecord('employee_daily_attendances',$insert_array);
		}else{
			$insert_array = array('clock_out'=>$post['time'],'check_out_type'=>'1','in_out'=>$post['in_out'],'device_type'=>$post['device_type']);
			$res=$callConfig->updateRecord('employee_daily_attendances',$insert_array,'id',$data->id);
		}
		$queryd = $callConfig->selectQuery('employee_daily_attendance_details','*',"is_active=0 AND attendace_date='?' And clock_out is NULL",'','','');
		$params = [$queryd, $callConfig->selectQuery, $post];
			//echo $queryd ;
		$datad = $callConfig->getRowNew($queryd);
			//print_r($datad); echo "man".count($datad);
		if(count((array)$datad) == "0"){
			$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$post['emp_id'],'attendace_date'=>$post['date'],'clock_in'=>$post['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>$post['in_out']);
				//echo "<pre>"; print_r($insert_array); exit;
			$daas = $callConfig->insertRecord('employee_daily_attendance_details',$insert_array);
			$res=$callConfig->updateRecord('employee_daily_attendances', array('clock_out'=>''),'id',$data->id);
		}else{
			$insert_array = array('clock_out'=>$post['time'],'check_out_type'=>'1','in_out'=>$post['in_out']);
			$res=$callConfig->updateRecord('employee_daily_attendance_details',$insert_array,'id',$datad->id);

		}
		return $res;
			//echo $res; exit;
	}
	function togetempleavebalances($get)
	{
		global $callConfig;
		$itemsquery  = "SELECT sum(consumed_leaves) as consumed FROM emp_leave_balances where DATE_FORMAT(status_date,'%Y%M')='?' AND employee_id =? AND is_active = 0 ";
		$params = [$itemsquery, $get, $get];
			//echo $itemsquery; exit;
		$data = $callConfig->getAllRowsNew($itemsquery);
		return $data;
	}

	function payrollupdateonetime($post,$get){
		global $callConfig;
			//echo "<pre>";
		$erquery = $callConfig->selectQuery($post['type'],'*',"is_active=0",'','','');
		$erdata = $callConfig->getAllRowsNew($erquery);
		foreach ($erdata as $er) {
			$earns[$er->name] = (array)$er;
				//$earns[] = $earn;
		}
		$row_data = $post['data']['0'];
			//print_r($row_data);
		unset($post['data']['0']);
		foreach ($post['data'] as $data) {
			$emp_code = $data['employ_code']['0'];
			$equery = $callConfig->selectQuery('employees','id',"emp_code='?'",'','','');
			$params = [$equery, $callConfig->selectQuery, $emp_code];
			$edata = $callConfig->getRowNew($equery);
			$emp_id = $edata->id;
			if($emp_id!=''){
				foreach ($row_data as $key=>$value) {
					if($key !=0 && $data[$key] != "" && $data[$key]!='0'){
						$whr = "pay_year=? AND pay_month = ? AND application_table_name ='?' AND employee_id=? And type_id =? AND company_id = ? AND is_active=0";
						$params = [$whr, $post, $post, $post, $emp_id, $earns, $value, $get];
						$hquery = $callConfig->selectQuery('employee_other_earnings','id',$whr,'','','');
							//echo $hquery; exit;
						$hudata = $callConfig->getRowNew($hquery);

						if($hudata->id !=""){ 
							$res=$callConfig->updateRecord('employee_other_earnings',array('is_active'=>1),'id',$hudata->id);
						}
						$insert_array = array('company_id'=>$get['company_id'],'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'employee_id'=>$emp_id,'application_table_name'=>$post['type'],'type_id'=>$earns[$value]['id'],'type_category'=>$earns[$value]['category_id'],'claim_amount'=>$data[$key],'approved_amount'=>$data[$key],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
						$res = $callConfig->insertRecord('employee_other_earnings',$insert_array);
					}
				}
			}
		}
		return $res;
	}
	function payrolldetails($get){
		global $callConfig;
			//echo "<pre>"; print_r($get);
		//$datess = $this->payrollfromtodates($get);
		//$work_days=$this->toinsertpayrnsummarynew($get,$get);
		$whr = "pay_year=? AND pay_month = ? AND company_id = ? AND is_active=0";
		$params = [$whr, $get, $get, $get];
		$hquery = $callConfig->selectQuery('payroll','*',$whr,'','','');
		$hudata = $callConfig->getRowNew($hquery);
		//print_r($hudata); exit;
		if($hudata->id !=""){ 
			$return = $this->payrolldisplaydata($get);
		}else{
			$whre = "pay_year=? AND pay_month = ? AND company_id = ? AND is_active=0";
			$params = [$whre, $get, $get, $get];
			$pequery = $callConfig->selectQuery('payroll_exclusions','*',$whre,'','','');
			//echo $pequery;
			$pedata = $callConfig->getAllRowsNew($pequery);
			//print_r($pedata); exit;
			if(count((array)$pedata)<1){
				$return['status'] = '0';
			}else{
				if($get['payroll']='process'){
					$work_days=$this->toinsertpayrnsummarynew($get,$get);
					//print_r($work_days); exit;
					$assspquery ="SELECT sum(if(category_id =3, 1,0)) addition, sum(if(category_id =4, 1,0)) separation, sum(if(category_id = 1, 1,0)) exclusion, sum(if(category_id = 5, 1,0)) settlements FROM `payroll_exclusions` WHERE  company_id = ? AND is_active=0 And pay_year ='?' AND pay_month ='?'";
					$params = [$assspquery, $get, $get, $get];
					//echo $assspquery;
					$countdata = $callConfig->getRowNew($assspquery);
					$countdata = (array)$countdata;
					//print_r($countdata);

					$payrunquery = "SELECT sum(if(compnent_type =0,salary_component_actual ,0)) gross_pay, sum(if(compnent_type =1,salary_component_actual ,0)) deductions FROM pay_run_summary_components WHERE  company_id = ? AND is_active=0 And pay_year ='?' AND pay_month ='?'";
					$params = [$payrunquery, $get, $get, $get];
					$counpayrun = $callConfig->getRowNew($payrunquery);
					$counpayrun = (array)$counpayrun;
					$insert_array = array('company_id'=>$get['company_id'],'pay_year'=>$get['pay_year'],'pay_month'=>$get['pay_month'],'gross_pay'=>$counpayrun['gross_pay'],'deductions'=>$counpayrun['deductions'],'work_days'=>$work_days,'addition'=>$countdata['addition'],'separation'=>$countdata['separation'],'exclusion'=>$countdata['exclusion'],'settlements'=>$countdata['settlements'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
					$res = $callConfig->insertRecord('payroll',$insert_array);
					//print_r($insert_array); exit;
					$return = $this->payrolldisplaydata($get);
				}else{
					$return['status'] = '1';
				}
			}
		}
			//print_r($return); exit;
		return $return;
	}
	function regenratepayrolldetails($get){
		global $callConfig;
		$query = "UPDATE `pay_run_summary` SET is_active=1 WHERE  `company_id`=? AND `pay_year`=? AND `pay_month`=? AND is_active=0";
		$params = [$query, $get, $get, $get];
		$query2 = "UPDATE `pay_run_summary_components` SET is_active=1 WHERE  `company_id`=? AND `pay_year`=? AND `pay_month`=? AND is_active=0";
		$params = [$query2, $get, $get, $get];
		$query3 = "UPDATE `payroll` SET is_active=1 WHERE  `company_id`=? AND `pay_year`=? AND `pay_month`=? AND is_active=0";
		$params = [$query3, $get, $get, $get];
		$query4 = "UPDATE `payroll_exclusions` SET is_active=1 WHERE  `company_id`=? AND `pay_year`=? AND `pay_month`=? AND is_active=0";
		$params = [$query4, $get, $get, $get];
		$query5 = "UPDATE `emp_salary_advance` SET salary_applicable=0 WHERE  `company_id`=? AND `pay_year`=? AND `pay_month`=? AND is_active=0";
		$params = [$query5, $get, $get, $get];
		$data = $callConfig->executeQuery($query);
		$data = $callConfig->executeQuery($query2);
		$data = $callConfig->executeQuery($query3);
		$data = $callConfig->executeQuery($query4);
		$data = $callConfig->executeQuery($query5);
		return $data;
	}
	function payrolldisplaydata($get){
		global $callConfig;
			//echo "<pre>"; print_r($get);
		$whr = "pay_year=? AND pay_month = ? AND company_id = ? AND is_active=0";
		$params = [$whr, $get, $get, $get];
		$hquery = $callConfig->selectQuery('payroll','*',$whr,'','','');
			//echo $hquery;
		$hudata = $callConfig->getRowNew($hquery);
		$whre = "pay_year=? AND pay_month = ? AND company_id = ? AND is_active=0";
		$params = [$whre, $get, $get, $get];
		$pequery = $callConfig->selectQuery('payroll_exclusions','*',$whre,'','','');
		$pedata = $callConfig->getAllRowsNew($pequery);
		$return['status'] = '3';
		$return['payroll'] = $hudata;
		$return['exclusion'] = $pedata;
		return $return;
	}
	*/
}
?>