<?php 
class mobileUserClass extends userClass{

	function togetconfigmobiledata($get){
		global $callConfig;

		$configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address, attendance_enable_clock, shift_mode  FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $configres=$callConfig->getRow($configsql);
	    $configres->date_format = strtolower($configres->date_format);
	    $configres->date_format = str_replace('mm','MM', $configres->date_format);
	    $configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
	    $configres->attendance_enable_clock = $configres->attendance_enable_clock!="" ? $configres->attendance_enable_clock : 1;
	    $configres->view_salary = "Y";
	    if($get['user_id']!=""){
	    	$query = "SELECT * FROM employees WHERE company_id = '".$get['company_id']."' AND user_id = ".$get['user_id']."  AND is_active =0";
	    	$emp_data = $callConfig->getRow($query);
	    	$location_id = $emp_data->location_id;

	    	//echo "<br>".$query."<br>";print_r($emp_data);
	    	if($emp_data->id!=""){
	    		$query = "SELECT location_id FROM emp_locations WHERE company_id =".$get['company_id']." AND emp_id = ".$emp_data->id." AND CURDATE() BETWEEN start_date AND end_date AND is_active = 0";
	    		$emp_loc = $callConfig->getRow($query);
	    		//echo "<br>".$query."<br>";print_r($emp_loc);
	    		if($emp_loc->location_id!=""){
	    			$location_id = $emp_loc->location_id;
	    		}
	    		$clock_event_type = 'Y'; // clockin siwp check
				if(isset($emp_data->clock_event_type) && $emp_data->clock_event_type == 'N'){
					$clock_event_type = 'N';					
				}

				$configres->clock_event_type = $clock_event_type;
				if(isset($emp_data->is_show_payshilp) && $emp_data->is_show_payshilp == 1){					
					$configres->view_salary = "N";	
				}
	    		if($configres->shift_mode != '' && $configres->shift_mode == '0'){
		    		$configres->emp_id = $emp_data->id;
		    		$configres->shift_name = '';
	    			$configres->start_time = '';
	    			$configres->end_time = '';
	    			$query = "SELECT id, shift_id FROM emp_shifts WHERE company_id =".$get['company_id']." AND emp_id = ".$emp_data->id."  AND is_active = 0";
		    		$emp_shifts_data = $callConfig->getRow($query);
		    		if($emp_shifts_data->shift_id!=""){
		    			$query = "SELECT id, shift_name, clock_in AS start_time, clock_out AS end_time  FROM shifts WHERE company_id =".$get['company_id']." AND id = ".$emp_shifts_data->shift_id."  AND is_active = 0";
		    			$shifts_data = $callConfig->getRow($query);
		    			$configres->shift_name = $shifts_data->shift_name;
		    			$configres->start_time = $shifts_data->start_time;
		    			$configres->end_time = $shifts_data->end_time;
		    		}
		    	}else{
		    		$query ="SELECT attendace_date, clock_in, clock_out FROM employee_daily_attendances WHERE company_id =".$get['company_id']." AND employee_id =".$emp_data->id." AND attendace_date ='".date('Y-m-d')."' AND is_active = 0";
						$data = $callConfig->getRow($query);
						$configres->start_time = '00:00:00';
						$configres->end_time = '00:00:00';
						//$configres->shift_mode = 0;
					//	$configres->query =  $query;    			
		    	}
	    	}
	    	if($location_id !=""){
	    		$query = "SELECT id, loc_latitude as location_latitude, loc_longitude as location_longitude FROM locations WHERE company_id = '".$get['company_id']."' AND id = ".$location_id 	." AND is_active =0";
		    	$com_data = $callConfig->getRow($query);
		    	//echo "<br>".$query."<br>";print_r($com_data);
	    	}else{
	    		$query = "SELECT id, location_latitude, location_longitude FROM companies WHERE company_id = '".$get['company_id']."' AND is_active =0";
	    		$com_data = $callConfig->getRow($query);
	    		//echo "<br>".$query."<br>";print_r($com_data);
	    	}
	    }
	    
	    //print_r($com_data);
	    $configres->location_latitude = $com_data->location_latitude;
	    $configres->location_longitude = $com_data->location_longitude;
	    $configres->clock_with_in_time = CLOCK_WITH_IN_TIME;// clock out with out radius
	    $configres->acess_of_pesonal_gateway = ACESS_OF_PESONAL_GATEWAY;// ACESS_OF_PESONAL_GATEWAY
	    $configres->acess_of_transport_gateway = ACESS_OF_TRANSPORT_GATEWAY;


	    // $configres->stationery_inventory = STATIONERY_INVENTORY;
	    $configres->employee_identifier = 'N';
	    $configres->show_assets = 'N';
	    $configres->show_inventory = 'N';
	    $configres->show_wfh = 'Y';

	    $configres->alert_miss_punch = 'Y';
	    $configres->alert_miss_punch_status = 'N';
	    $configres->alert_miss_punch_message = '';
	    // we check yesterday user missed the punch
		$yesterday = date('Y-m-d', strtotime('-1 day'));
	    if($configres->alert_miss_punch == 'Y'){
	    	$query = "SELECT id FROM employee_daily_attendances WHERE employee_id='{$configres->emp_id}' AND attendace_date='{$yesterday}' AND attendance_key_short_code='MP' AND company_id='{$get['company_id']}' AND is_active=0 LIMIT 1";
	    	$miss_punch_data = $callConfig->getRow($query);
	    	if($miss_punch_data->id!=''){
	    		$configres->alert_miss_punch_status = 'Y';
	    		$configres->alert_miss_punch_message = 'Yesterday you missed your punch';
	    	}
	    }

	    $configres->alert_leaves = 'Y';
	    $configres->alert_leaves_status = 'N';
	    $configres->alert_leaves_message = '';

	    // next five days have any leaves are not approved
		$from_date = date('Y-m-d');
		$to_day = date('Y-m-d', strtotime('+5 days'));
	    if($configres->alert_leaves == 'Y'){
	    	$query = "SELECT id, leave_from, leave_to FROM employee_leaves WHERE employee_id='{$configres->emp_id}' AND current_status!=".ST_APPROVED." AND company_id='{$get['company_id']}' AND is_active=0 AND ( (leave_from BETWEEN '{$from_date}' AND '{$to_day}') OR (leave_to BETWEEN '{$from_date}' AND '{$to_day}') OR (leave_from <= '{$from_date}' AND leave_to >= '{$to_day}') ) LIMIT 1";
	    	$leaves_data = $callConfig->getRow($query);
	    	if($leaves_data->id!=''){
	    		$configres->alert_leaves_status = 'Y';
	    		$configres->alert_leaves_message = 'You have leave requests pending approval in the next 5 days';
	    	} 
	    }

		if($get['data_name']=='infisuitein_suwalka'){
			$configres->clock_with_in_time = 'Y';
			$configres->acess_of_pesonal_gateway = 'Y';
			$configres->acess_of_transport_gateway = 'Y';
			$configres->employee_identifier = EMPLOYEE_IDENTIFIER;
	    	$configres->show_assets = SHOW_ASSETS;
	    	$configres->show_inventory = 'Y';
	    	$configres->show_wfh = 'N';
		}

		$configres->employee_identifier = 'Y';



	    $attquery = "SELECT id, minimum_hours_required, location_radius FROM attendance_settings WHERE company_id = '".$get['company_id']."' AND is_active =0";
	    $att_data = $callConfig->getRow($attquery);

	    $configres->day_hours = $att_data->minimum_hours_required;
	    $configres->location_radius = $att_data->location_radius;

	    $configres->location_radius_check = LOCATION_RADIUS_CHECK;
	    $configres->live_location = LIVE_LOCATION;
	    $configres->live_battery = LIVE_BATTERY;
	    $configres->live_internet = LIVE_INTERNET;
	    $configres->live_face_liveness= LIVE_FACE_LIVE_NESS;
	    $configres->live_announcement= MOBILE_ANNOUNCEMENT;
	    //return $configres;
	    $configres->viewLeaveApproval ='Y';
	    $configres->announcement_reslut_view ='Y';
	    if(LIVE_LOCATION==2){
	    	$configres->live_location = 1;
	    	$query = "SELECT MAX(live_lcation) as live_lcation FROM application_roles WHERE company_id ='".$get['company_id']."' AND role_short_code IN (SELECT role_short_code FROM application_user_module_roles WHERE company_id ='".$get['company_id']."' AND user_id ='".$get['user_id']."' AND is_active =0) AND is_active =0";
	    	$role_data = $callConfig->getRow($query);
	    	if($role_data->live_lcation!=""){
	    		$configres->live_location = $role_data->live_lcation;
	    	}
	    }	
	    if(LIVE_LOCATION==3){
	    	$configres->live_location = 1;
	    	$query = "SELECT live_lcation FROM application_users WHERE company_id ='".$get['company_id']."' AND id ='".$get['user_id']."' AND is_active =0";
	    	$role_data = $callConfig->getRow($query);
	    	if($role_data->live_lcation!=""){
	    		$configres->live_location = $role_data->live_lcation;
	    	}

	    }
	    $configres->view_attendance = "Y";
	    $configres->view_leave = "Y";
	   
	    $configres->driver_id = "";
	    $query = "SELECT id FROM drivers WHERE company_id=".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active=0";
	    $dres = $callConfig->getRow($query);
	    if($dres->id!=""){
	    	$configres->driver_id = $dres->id;
	    }
	    $configres->start_leave_date = '2026-02-01';
	    $configres->work_on_duty = 'Y';
	    // $configres->admin_type = 'Y';

	    $configres->admin_type = 'N';
	    if(isset($get['module_id']) && $get['module_id']!="" && isset($get['role_short_code']) && $get['role_short_code']!="" ){
	    	$query ="SELECT id FROM module_setup WHERE company_id =".$get['company_id']." AND role_admin_short_code IN ('".$get['role_short_code']."') AND is_active =0";
	    	$row = $callConfig->getRow($query);
	    	if(count((array)$row)>0){
	    		$configres->admin_type = 'Y';
	    	}
	    }
	    $configres->manager_type = 'Y';
	    if($get['data_name']=='infistaqin_infisuite_2'){
	     	//echo "0000000000";
	    	 $configres->live_face_liveness= 1;
	    }

	    // $configres->show_inventory = 'Y';
	    $return['config'] = $configres;
		return $return;
	}
	function togetmoduleuserroleslist($get){
		global $callConfig;
		$query = "SELECT aur.user_id, aur.role_short_code, ar.display_name AS role_display_name FROM application_user_module_roles aur INNER JOIN application_roles ar ON aur.company_id = ar.company_id AND aur.role_short_code = ar.role_short_code WHERE aur.company_id = ".$get['company_id']." AND aur.user_id = ".$get['user_id']." AND aur.application_module_id = ".$get['module_id']." AND aur.is_active = 0 AND ar.is_active = 0";
		//$return['data_query'] =$query;
		$return['data'] =$callConfig->getAllRows($query);
		return $return;
	}
	function togetconfigmobiledataold($get){
		global $callConfig;
		$configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address, attendance_enable_clock  FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $configres=$callConfig->getRow($configsql);
	    $configres->date_format = strtolower($configres->date_format);
	    $configres->date_format = str_replace('mm','MM', $configres->date_format);
	    $configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
	    $configres->attendance_enable_clock = $configres->attendance_enable_clock!="" ? $configres->attendance_enable_clock : 1;
	    if($get['user_id']!=""){
	    	$query = "SELECT id, location_id FROM employees WHERE company_id = '".$get['company_id']."' AND user_id = ".$get['user_id']."  AND is_active =0";
	    	$emp_data = $callConfig->getRow($query);
	    	$location_id = $emp_data->location_id;
	    	//echo "<br>".$query."<br>";print_r($emp_data);
	    	if($emp_data->id!=""){
	    		$query = "SELECT location_id FROM emp_locations WHERE company_id =".$get['company_id']." AND emp_id = ".$emp_data->id." AND CURDATE() BETWEEN start_date AND end_date AND is_active = 0";
	    		$emp_loc = $callConfig->getRow($query);
	    		//echo "<br>".$query."<br>";print_r($emp_loc);
	    		if($emp_loc->location_id!=""){
	    			$location_id = $emp_loc->location_id;
	    		}
	    	}
	    	if($location_id !=""){
	    		$query = "SELECT id, loc_latitude as location_latitude, loc_longitude as location_longitude FROM locations WHERE company_id = '".$get['company_id']."' AND id = ".$location_id 	." AND is_active =0";
		    	$com_data = $callConfig->getRow($query);
		    	//echo "<br>".$query."<br>";print_r($com_data);
	    	}else{
	    		$query = "SELECT id, location_latitude, location_longitude FROM companies WHERE company_id = '".$get['company_id']."' AND is_active =0";
	    		$com_data = $callConfig->getRow($query);
	    		//echo "<br>".$query."<br>";print_r($com_data);
	    	}
	    }
	    $attquery = "SELECT id, minimum_hours_required, location_radius FROM attendance_settings WHERE company_id = '".$get['company_id']."' AND is_active =0";
	    $att_data = $callConfig->getRow($attquery);
	    $configres->day_hours = $att_data->minimum_hours_required;
	    //print_r($com_data);
	    $configres->location_radius_check = LOCATION_RADIUS_CHECK;
	    $configres->location_radius = $att_data->location_radius;
	    $configres->location_latitude = $com_data->location_latitude;
	    $configres->location_longitude = $com_data->location_longitude;
	    $now_date = date("Y-m-d");
	    if($emp_data->id!=""){
	    	$work_query = "SELECT * FROM request_work_from_home WHERE company_id = '".$get['company_id']."' AND emp_id = '".$emp_data->id."' AND effective_from_date <= '".$now_date."' AND effective_to_date >= '".$now_date."' AND is_active =0";
	    	$workhome_data = $callConfig->getRow($work_query);
	    	if(count((array)$workhome_data)>0){
	    		$configres->location_radius_check = 'N';
	    		//$configres->work_query = $work_query;
	    		//$configres->workhome_data = $workhome_data;
	    	}
	    	
	    }
	    
	    
	   
	    $configres->live_location = LIVE_LOCATION;
	    $configres->live_battery = LIVE_BATTERY;
	    $configres->live_internet = LIVE_INTERNET;
	    $configres->live_face_liveness= LIVE_FACE_LIVE_NESS;

	    if(LIVE_LOCATION==2){
	    	$configres->live_location = 1;
	    	$query = "SELECT MAX(live_lcation) as live_lcation FROM application_roles WHERE company_id ='".$get['company_id']."' AND role_short_code IN (SELECT role_short_code FROM application_user_module_roles WHERE company_id ='".$get['company_id']."' AND user_id ='".$get['user_id']."' AND is_active =0) AND is_active =0";
	    	$role_data = $callConfig->getRow($query);
	    	if($role_data->live_lcation!=""){
	    		$configres->live_location = $role_data->live_lcation;
	    	}
	    }	
	    if(LIVE_LOCATION==3){
	    	$configres->live_location = 1;
	    	$query = "SELECT live_lcation FROM application_users WHERE company_id ='".$get['company_id']."' AND id ='".$get['user_id']."' AND is_active =0";
	    	$role_data = $callConfig->getRow($query);
	    	if($role_data->live_lcation!=""){
	    		$configres->live_location = $role_data->live_lcation;
	    	}

	    }
	    $configres->view_attendance = "Y";
	    $configres->view_leave = "Y";
	    $configres->view_salary = "Y";
	    $configres->driver_id = "";
	    $query = "SELECT id FROM drivers WHERE company_id=".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active=0";
	    $dres = $callConfig->getRow($query);
	    if($dres->id!=""){
	    	$configres->driver_id = $dres->id;
	    }

	    $return['config'] = $configres;
		return $return;
	}

	function mobileaprovelistwithmodle($get){
		global $callConfig;
		$query = "SELECT id,application_module_id,role_id, role_short_code FROM application_user_module_roles WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active =0";
		$datas = $callConfig->getAllRows($query);
		//print_r($datas);
		$all = 'N';
		foreach($datas as $data){
			if($data->role_short_code == 'ADMIN'){
				$all = 'Y';
				break;
			}else{
				$modules[] = $data->application_module_id;
			}
		}
		if($all == 'Y'){
			$whr = "";
		}else{
			$whr = "AND module_id IN(".implode(',',$modules).")";
		}
		$module_query = "SELECT module_id,module_name as display_name, display_name as full_name, module_logo FROM module_setup WHERE company_id = ".$get['company_id']." AND linking =1  AND is_active =0 ".$whr;
		$module_data = $callConfig->getAllRows($module_query);
		$return['modules'] = $module_data;
		$configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address  FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $configres=$callConfig->getRow($configsql);
	    $configres->date_format = strtolower($configres->date_format);
	    $configres->date_format = str_replace('mm','MM', $configres->date_format);
	    $configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
	    $return['config'] = $configres;
		return $return;
	}
	function togetbaseonmoduluelistandapprovals($get){
		global $callConfig;
		//echo "RRRR"; exit;

		$auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get['company_id']."' AND is_active =0";
    	$aures = $callConfig->getAllRows($auquery);
    	$application_users = array();
    	foreach($aures as $userau){
    		$application_users[$userau->id] = $userau;
    	}


		$query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get['company_id']." AND `aplication_module` = ".$get['module_id']." AND `dashboard` = 0 AND `is_active` = 0";
		$datas = $callConfig->getAllRows($query);
		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = "#".str_replace("#","",$data_stat->background_color);
				$arr_status[$data_stat->name_value]['color'] = "#".str_replace("#","",$data_stat->color);
		}
		//print_r($arr_status); exit;
		$str = "";
		foreach($datas as $data){
			$str.= "'".$data->page_name."',";
		}
		$sat  = rtrim($str,",");
		//echo $query."</br>".$sat;
		$ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get['company_id']." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
		$apdatas = $callConfig->getAllRows($ap_query);
		$transtables = array();
		foreach($apdatas as $apdata){
			$list_table[$apdata->list_page_name] = $apdata->application_table_name;
			$transtables[$apdata->application_table_name] = $apdata->application_table_name;

		}
		//echo $ap_query."</br>".$sat;
		$return['list'] = $lists;
		// $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get['company_id']." AND ta.module_setup_id = ".$get['module_id']." AND ta.user_id = ".$get['user_id']." AND ta.response_type = 0";
		// $app_data = $callConfig->getAllRows($approval_query);
		// //print_r($app_data);
		// foreach($app_data as $app){
		// 	$query = "SELECT transaction_number, total_amount, transaction_date,current_status FROM ".$app->transaction_table." WHERE id=".$app->transaction_id;
		// 	$list_data = $callConfig->getRow($query);

		// 	$stquery = "SELECT status FROM `status_progress` WHERE `id` IN (SELECT target_status FROM `status_progress_actions` WHERE `status_progress_id` IN (SELECT id FROM `status_progress` WHERE `table_name` = '".$app->transaction_table."' AND status =".$list_data->current_status.") ) AND  company_id =1  AND status NOT IN (20,30)";
		// 	$stdata = $callConfig->getRow($stquery);
		// 	$data=(array)$app;
		// 	$data['transaction_number']=$list_data->transaction_number;
		// 	$data['total_amount']=$list_data->total_amount;
		// 	$data['transaction_date']=$list_data->transaction_date;
		// 	$data['current_status']=$list_data->current_status;
		// 	$data['approved_status']=$stdata->status;
		// 	//$data['approved_status_query']=$stquery;
		// 	$data['reject_status']="30";
		// 	$data['background_color']=$arr_status[$list_data->current_status]['background_color'];
		// 	$data['text_color']=$arr_status[$list_data->current_status]['color'];
		// 	$data['originator_name']= 'Pabitra Kumar Jena';
		// 	$data['originator_initial']= 'PKJ';
		// 	$data['originator_image']= 'file_1689092069_64ad7fe56a8f4.jpg';			
		// 	$all_data[$app->transaction_table][] =$data;
		// }

		foreach($transtables as $transtab){
			$approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get['company_id']."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get['user_id']."' AND tal.send_back_flag = 0 AND tal.call_back = 1 AND tal.response_type ='0' ORDER BY tal.request_date DESC";
			$app_data=$callConfig->getAllRows($approval_query);
			foreach($app_data as $app){
				$data=(array)$app;
				$data['transaction_table'] = $transtab;
				$data['transaction_id'] = $app->id;

				$get['transaction_no'] = $app->id;
				$get['transaction_table'] = $transtab;
				$get['current_status'] = $app->current_status;
				$get['company_id'] = $get['company_id'];
				$getDynamicWorkFlowSteps = $this->getDynamicWorkFlowSteps($get);
				$status_progress = $getDynamicWorkFlowSteps['status_progress'];
				$nextstepkeyvalue = $getDynamicWorkFlowSteps['nextstepkeyvalue'];
				$data['approved_status']=$nextstepkeyvalue[$app->current_status];
				$data['error'] = $getDynamicWorkFlowSteps->error;
				$data['reject_status']="30";
				$data['status_name']=$status_progress[$app->current_status]->name;
				$data['background_color']=$arr_status[$app->current_status]['background_color'];
				$data['text_color']=$arr_status[$app->current_status]['color'];
				$data['originator_name']= $application_users[$app->originator]->display_name;
				$data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
				$data['originator_image']= $application_users[$app->originator]->user_photo;		
				$all_data[$transtab][] =$data;
			}
		}
		foreach($datas as $data){
			$retd['name'] = $data->menu_label;
			$retd['table'] = $list_table[$data->page_name];
			$retd['data'] = array();
			if(isset($all_data[$list_table[$data->page_name]])){
				$retd['data'] = $all_data[$list_table[$data->page_name]];
			}
			$res[] = $retd;
		}
		//echo "<pre>"; print_r($res);
		return array('approvals'=>$res);
	} 
	function togetuserdetailsbaseonuserid($get){
		global $callConfig;
		$query = "SELECT * FROM application_users WHERE id=".$get['user_id'];
		$row_data = $callConfig->getRow($query);
		$query_employee = "SELECT id FROM ".$row_db->db_name.".employees where user_id =".$row_data->id." AND is_active=0 ";
				//$user->query_employee = $query_employee;
		$employee = $callConfig->getRow($query_employee);
		$row_data->emp_id = $employee->id;
		return $row_data;
	}
	function togetdynamicinvoicedetails($get){
        global $callConfig;
        $return = array();
        $transaction_id = $get['transaction_id'];
        $transaction_table = $get['transaction_table'];
        
        $configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address  FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $configres=$callConfig->getRow($configsql);
        $configres->date_format = strtolower($configres->date_format);
    		$configres->date_format = str_replace('mm','MM', $configres->date_format);
    		$configres->date_format = str_replace('-',  $configres->date_separator, $configres->date_format);
        $return['config'] = $configres;
        
        $tables = $this->toGetInvoicetabledata($transaction_table);
       // print_r($tables);
        
        $rental_invoicessql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$transaction_id."'  AND is_active =0";
        $rental_invoicesres=$callConfig->getRow($rental_invoicessql);
        $return['invoices'] = $rental_invoicesres;
        
        $rental_invoice_itemsaql = "SELECT id, item_id, item_name, (SELECT sku FROM	items itm WHERE item_id = itm.id) sku, description, purchase_uom, (SELECT uom_name FROM unit_of_measurements um	WHERE item_id = um.id) uom, item_price, item_quantity, item_amount, tax_amount, discount_amount,
  			total_amount   FROM ".$tables['item']." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction_id."'  AND is_active =0";
  		//$return['invoice_items_query'] = $rental_invoice_itemsaql;
        $rental_invoice_items=$callConfig->getAllRows($rental_invoice_itemsaql);
        $return['invoice_items'] = $rental_invoice_items;
       

        $billbysql = "SELECT id, display_name, user_email, user_mobile FROM application_users WHERE company_id = '".$get['company_id']."' AND id = '".$rental_invoicesres->originator."'AND is_active =0";
        $billbyres=$callConfig->getRow($billbysql);
        $return['bill_by'] = $billbyres;

        $bank_accountssql = "SELECT ba.id, ba.account_code, ba.bank_account_name, ba.bank_account_number, ba.bank_contact_person, ba.bank_contact_no, ba.bank_address, ba.upi_id, ba.ifsc_code, ba.account_type, (SELECT bank.bank_name FROM banks bank WHERE ba.company_id = bank.company_id AND bank.id =ba.bank_id AND ba.is_active = bank.is_active) as bank_name FROM bank_accounts ba WHERE ba.company_id = '".$get['company_id']."' AND ba.id = '".$configres->default_bank_account_id."' AND ba.is_active =0";
        $bank_accountsres=$callConfig->getRow($bank_accountssql);
        $return['bank_accounts'] = $bank_accountsres;


  		// -------------------- Start project_task_details ---------------------------
		$tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type FROM `tax_authorities_rates_split` WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $tax_authorities_rates_splitres = $callConfig->getAllRows($tax_authorities_rates_splitsql);
        $return['tax_authorities_rates_split']= $tax_authorities_rates_splitres;	
        // -------------------- End project_task_details --------------------------

        return $return;
    }
    function togetrecallstatusdata($get){
    	global $callConfig;
    	$query = "UPDATE `transaction_approval_logs` SET `response_type` = '0' WHERE user_id = 377";
    	$res = $callConfig->executeQuery($query);
    	//echo $query;
    	$query = "UPDATE `requisitions` SET `current_status` = 101 WHERE id IN(411,412,413)";
    	$res = $callConfig->executeQuery($query);
    	//echo $query;
 		return $res;
    }
    function togetcreatedapproverejectinvoicedetails($get){
    	global $callConfig;
    	$query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get['company_id']." AND `aplication_module` = ".$get['module_id']." AND `dashboard` = 0 AND `is_active` = 0";
		$datas = $callConfig->getAllRows($query);
		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = $data_stat->background_color;
				$arr_status[$data_stat->name_value]['color'] = $data_stat->color;
		}
		//print_r($arr_status); exit;
		$str = "";
		foreach($datas as $data){
			$str.= "'".$data->page_name."',";
		}
		$sat  = rtrim($str,",");
		//echo $query."</br>".$sat;
		$ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get['company_id']." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
		$apdatas = $callConfig->getAllRows($ap_query);
		
		foreach($apdatas as $apdata){
			$list_table[$apdata->list_page_name] = $apdata->application_table_name;
		}
		//echo $ap_query."</br>".$sat;
		$return['list'] = $lists;
		 $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get['company_id']." AND ta.module_setup_id = ".$get['module_id']." AND ta.user_id = ".$get['user_id']." AND ta.response_type != 0";
		$app_data = $callConfig->getAllRows($approval_query);
		//print_r($app_data);
		foreach((array)$app_data as $app){
			$query = "SELECT transaction_number, total_amount, transaction_date,current_status FROM ".$app->transaction_table." WHERE id=".$app->transaction_id;
			//echo $query; exit;
			$list_data = $callConfig->getRow($query);
			$data=(array)$app;
			$data['transaction_number']=$list_data->transaction_number;
			$data['total_amount']=$list_data->total_amount;
			$data['transaction_date']=$list_data->transaction_date;
			$data['current_status']=$list_data->current_status;
			$data['background_color']=$arr_status[$list_data->current_status]['background_color'];
			$data['text_color']=$arr_status[$list_data->current_status]['color'];
			$data['status_name']= "Process";
			$data['originator_name']= 'Pabitra Kumar Jena';
			$data['originator_initial']= 'PKJ';
			$data['originator_image']= '';			
			$view = 'Y';
			if($list_data->current_status==30){
				$view = 'N';
			}			
			$all_data[$app->transaction_table][$view][] =$data;
		}
		//echo "<pre>"; print_r($datas); print_r($list_table); exit;
		foreach($datas as $data){
			$talee = $list_table[$data->page_name];
			$cquery = "SELECT id,".$get['module_id']." module_setup_id ,id as transaction_id, '".$talee."' transaction_table ,transaction_number, total_amount, transaction_date, current_status, '#000000' background_color, '#FFFFFF' text_color, 'Pabitra Kumar Jena' originator_name, 'Process' status_name, 'PKJ' originator_initial, 'file_1689092069_64ad7fe56a8f4.jpg' originator_image FROM ".$list_table[$data->page_name]." WHERE company_id =".$get['company_id']." AND (created_by = ".$get['user_id']." OR originator = ".$get['user_id'].") AND is_active =0";

			$create_data = $callConfig->getAllRows($cquery);
			$retd['name'] = $data->menu_label;
			$retd['table'] = $list_table[$data->page_name];
			$retd['data'] = array();
			if(isset($all_data[$list_table[$data->page_name]])){
				$retd['approved_data'] = $all_data[$list_table[$data->page_name]]['Y'];
				$retd['rejected_data'] = $all_data[$list_table[$data->page_name]]['Y'];
			}
			$retd['create_data'] = $create_data;
			$res[] = $retd;
		}
		return array('after_approval'=>$res);
    }
    function togetcreatedinvoicedetails($get){
    	global $callConfig;
    	$query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get['company_id']." AND `aplication_module` = ".$get['module_id']." AND `dashboard` = 0 AND `is_active` = 0";
		$datas = $callConfig->getAllRows($query);
		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = "#".str_replace("#","",$data_stat->background_color);
				$arr_status[$data_stat->name_value]['color'] = "#".str_replace("#","",$data_stat->color);
		}
		//print_r($arr_status); exit;
		$str = "";
		foreach($datas as $data){
			$str.= "'".$data->page_name."',";
		}
		$sat  = rtrim($str,",");
		//echo $query."</br>".$sat;
		$ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get['company_id']." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
		$apdatas = $callConfig->getAllRows($ap_query);
		
		foreach($apdatas as $apdata){
			$list_table[$apdata->list_page_name] = $apdata->application_table_name;
		}
		//echo $ap_query."</br>".$sat;
		foreach($datas as $data){
			$talee = $list_table[$data->page_name];
			$cquery = "SELECT tb.id,".$get['module_id']." module_setup_id ,tb.id as transaction_id, '".$talee."' transaction_table ,tb.transaction_number, tb.total_amount, tb.transaction_date, tb.current_status,  au.display_name originator_name,  sp.name status_name,  au.user_photo  originator_image FROM ".$list_table[$data->page_name]." tb, application_users au, status_progress sp WHERE tb.company_id =".$get['company_id']." AND (tb.created_by = ".$get['user_id']." OR tb.originator = ".$get['user_id'].") AND tb.originator = au.id AND sp.table_name = '".$list_table[$data->page_name]."' AND tb.current_status = sp.status AND tb.is_active =0";
			//echo $cquery; exit;
			$create_datas = $callConfig->getAllRows($cquery);
			foreach($create_datas as $crd){
				$create_data = (array)$crd;
				$create_data['originator_initial'] = $this->initials($crd->originator_name);
				$create_data['background_color'] =$arr_status[$crd->current_status]['background_color'];
				$create_data['text_color'] = $arr_status[$crd->current_status]['color'];
				$create_da[] = $create_data;
			}
			$all_data[$talee] = $create_da;
		}
		foreach($all_data as $table=>$data){
			$retd['name'] = ucwords(str_replace('_',' ',$table));
			$retd['table'] = $table;
			$retd['data'] = array();
			$retd['data'] = $data;
			$res[] = $retd;
		}
		return array('data_created'=>$res);
    }
    function togetapproveinvoicedetails($get){
    	global $callConfig;

    	$auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get['company_id']."' AND is_active =0";
    	$aures = $callConfig->getAllRows($auquery);
    	$application_users = array();
    	foreach($aures as $userau){
    		$application_users[$userau->id] = $userau;
    	}


    	$query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get['company_id']." AND `aplication_module` = ".$get['module_id']." AND `dashboard` = 0 AND `is_active` = 0";
		$datas = $callConfig->getAllRows($query);
		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = "#".str_replace("#","",$data_stat->background_color);
				$arr_status[$data_stat->name_value]['color'] = "#".str_replace("#","",$data_stat->color);
		}
		//print_r($arr_status); exit;
		$str = "";
		foreach($datas as $data){
			$str.= "'".$data->page_name."',";
		}
		$sat  = rtrim($str,",");
		//echo $query."</br>".$sat;
		$ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get['company_id']." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
		$apdatas = $callConfig->getAllRows($ap_query);
		$transtables = array();
		foreach($apdatas as $apdata){
			$list_table[$apdata->list_page_name] = $apdata->application_table_name;
			$transtables[$apdata->application_table_name] = $apdata->application_table_name;
		}
		//print_r($transtables);
		//echo $ap_query."</br>".$sat;
		//$return['list'] = $lists;
		/**
		 $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get['company_id']." AND ta.module_setup_id = ".$get['module_id']." AND ta.user_id = ".$get['user_id']." AND ta.response_type != 0";
		$app_data = $callConfig->getAllRows($approval_query);
		//print_r($app_data); exit;
		
		foreach((array)$app_data as $app){
			
			$query = "SELECT tb.transaction_number, tb.total_amount, tb.transaction_date, tb.current_status, au.display_name originator_name, au.user_photo originator_image, sp.name status_name FROM ".$app->transaction_table."  tb, application_users au, status_progress sp  WHERE tb.id=".$app->transaction_id." AND tb.originator = au.id AND sp.table_name = '".$app->transaction_table."' AND tb.current_status = sp.status";
			//echo $query; exit;
			$list_data = $callConfig->getRow($query);
			print_r($list_data);
			if($list_data->current_status!=30){
			    $data=(array)$app;
				$data['transaction_number']=$list_data->transaction_number;
				$data['total_amount']=$list_data->total_amount;
				$data['transaction_date']=$list_data->transaction_date;
				$data['current_status']=$list_data->current_status;
				$data['background_color']=$arr_status[$list_data->current_status]['background_color'];
				$data['text_color']=$arr_status[$list_data->current_status]['color'];
				$data['status_name']= $list_data->status_name;
				$data['originator_name']= $list_data->originator_name;
				$data['originator_initial']= $this->initials($list_data->originator_name);
				$data['originator_image']= $list_data->originator_image;	
				$all_data[$app->transaction_table][] =$data;		
			}			
			
		}

		**/




		foreach($transtables as $transtab){
			$approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get['company_id']."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get['user_id']."' AND tal.send_back_flag = 0 AND tal.response_type != 30 AND tal.call_back = 1 AND tal.response_type !='0' ORDER BY tal.request_date DESC";
			$app_data=$callConfig->getAllRows($approval_query);
			foreach($app_data as $app){
				$data=(array)$app;
				$data['transaction_table'] = $transtab;
				$data['transaction_id'] = $app->id;
				$data['status_name']=$arr_status[$app->current_status]['name'];
				$data['background_color']=$arr_status[$app->current_status]['background_color'];
				$data['text_color']=$arr_status[$app->current_status]['color'];
				$data['originator_name']= $application_users[$app->originator]->display_name;
				$data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
				$data['originator_image']= $application_users[$app->originator]->user_photo;		
				$all_data[$transtab][] =$data;
			}
		}

		//$all_data['list'][] =$data;
		//echo "<pre>"; print_r($datas); print_r($list_table); exit;
		//$return['data_approved']= array();
		//$return['data_approved']= $all_data;
		foreach($all_data as $table=>$data){
			$retd['name'] = ucwords(str_replace('_',' ',$table));
			$retd['table'] = $table;
			$retd['data'] = array();
			$retd['data'] = $data;
			$res[] = $retd;
		}
		//echo "<pre>"; print_r($res);
		return array('data_approved'=>$res);
    }
    function togetrecjectinvoicedetails($get){
    	global $callConfig;
    	$auquery = "SELECT id, display_name, user_photo FROM application_users WHERE company_id ='".$get['company_id']."' AND is_active =0";
    	$aures = $callConfig->getAllRows($auquery);
    	$application_users = array();
    	foreach($aures as $userau){
    		$application_users[$userau->id] = $userau;
    	}

    	$query = "SELECT page_name,menu_label FROM `application_menu` WHERE `company_id` = ".$get['company_id']." AND `aplication_module` = ".$get['module_id']." AND `dashboard` = 0 AND `is_active` = 0";
		$datas = $callConfig->getAllRows($query);
		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = "#".str_replace("#","",$data_stat->background_color);
				$arr_status[$data_stat->name_value]['color'] = "#".str_replace("#","",$data_stat->color);
		}
		//print_r($arr_status); exit;
		$str = "";
		foreach($datas as $data){
			$str.= "'".$data->page_name."',";
		}
		$sat  = rtrim($str,",");
		//echo $query."</br>".$sat;
		$ap_query = "SELECT application_table_name,list_page_name  FROM `application_pages` WHERE `company_id` = ".$get['company_id']." AND `list_page_name` IN (".$sat.") AND `is_active` = 0";
		$apdatas = $callConfig->getAllRows($ap_query);
		$transtables = array();
		foreach($apdatas as $apdata){
			$list_table[$apdata->list_page_name] = $apdata->application_table_name;
			$transtables[$apdata->application_table_name] = $apdata->application_table_name;
		}
		//echo $ap_query."</br>".$sat;
		$return['list'] = $lists;
		//  $approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get['company_id']." AND ta.module_setup_id = ".$get['module_id']." AND ta.user_id = ".$get['user_id']." AND ta.response_type != 0";
		// $app_data = $callConfig->getAllRows($approval_query);
		//print_r($app_data); exit;
		
		foreach($transtables as $transtab){
			$approval_query = "SELECT  transtable.id, tal.request_date, tal.response_type, tal.send_back_flag, tal.call_back, transtable.transaction_number, transtable.transaction_date, transtable.originator, transtable.party_name, transtable.amount, transtable.tax_amount, transtable.discount_amount, transtable.total_amount,transtable.shipping_amount, transtable.pending_amount, transtable.cost_center_id, transtable.current_status FROM transaction_approval_logs tal, ".$transtab." transtable WHERE tal.transaction_table = '".$transtab."' AND tal.transaction_id = transtable.id AND  tal.company_id = '".$get['company_id']."' AND tal.company_id = transtable.company_id AND tal.user_id = '".$get['user_id']."' AND tal.send_back_flag = 0 AND tal.response_type = 30 AND tal.call_back = 1 AND tal.response_type !='0' ORDER BY tal.request_date DESC";
			$app_data=$callConfig->getAllRows($approval_query);
			foreach($app_data as $app){
				$data=(array)$app;
				$data['transaction_table'] = $transtab;
				$data['transaction_id'] = $app->id;
				$data['status_name']=$arr_status[$app->current_status]['name'];
				$data['background_color']=$arr_status[$app->current_status]['background_color'];
				$data['text_color']=$arr_status[$app->current_status]['color'];
				$data['originator_name']= $application_users[$app->originator]->display_name;
				$data['originator_initial']= $this->mobileInitials($application_users[$app->originator]->display_name);
				$data['originator_image']= $application_users[$app->originator]->user_photo;		
				$all_data[$transtab][] =$data;
			}
		}
		foreach($all_data as $table=>$data){
			$retd['name'] = ucwords(str_replace('_',' ',$table));
			$retd['table'] = $table;
			$retd['data'] = array();
			$retd['data'] = $data;
			$res[] = $retd;
		}
		return array('data_rejected'=>$res);
		//echo "<pre>"; print_r($res);  exit;
		//return array('data_rejected'=>$all_data);
    }
    function toaddupdateusermobiledetails($get, $post){
    	global $callConfig;
    	$update = "update user_mobile_details set is_active = NULL, updated_by = ".$get['user_id']." where user_id ='".$get['user_id']."' AND is_active = 0";
		$update_data=$callConfig->executeQuery($update);
    	$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$get['user_id'],'fcm_token'=>$post['fcm_token'],'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
    	//echo "<pre>"; print_r($insert_array); exit;
		$res=$callConfig->insertRecord('user_mobile_details',$insert_array);
		return $res;
    }
    /*function tocallmobilenotificationmob($get){
    	global $callConfig;

    	$query = "SELECT * FROM user_mobile_details WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active = 0";
    	$data = $callConfig->getRow($query);
    	
    	//echo $data->fcm_token."< br>"; //exit;
    	//$chcek = $this->verifyFirebaseToken();
    	//print_r($get); print_r($data); exit;
    	$return['response'] = "FCM token is empty";
    	
    	if($data->fcm_token!=""){
    		$query = "SELECT * FROM mobile_firebase_authentications WHERE company_id =".$get['company_id']."  AND is_active = 0";
    		$res_data = $callConfig->getRow($query);
    		$auth_token = $res_data->authentication_key;
    		//echo $auth_token; exit;
    		$mesg = $get['message']!="" ? $get['message'] : "Test Notificaiton body For mobile  to API Test3";
    		$title = $get['title']!="" ? $get['title'] : "Test Notificaiton title For mobile  to API Test3";
   			$messageary['token'] = $data->fcm_token;
			$messageary['notification'] = array("body"=>$mesg,"title"=>$title);
			$request['message'] = $messageary;
			$send_req = json_encode($request);
			
	    	$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/infistaq/messages:send',
			  //https://fcm.googleapis.com/v1/projects/infistaq/messages:send
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>$send_req,
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json',
			    'Authorization: Bearer '.$auth_token
			  ),
			));	 
			$response = curl_exec($curl);
			curl_close($curl);
			// echo $response; exit;
			$res = json_decode($response,1);
			if(isset($res['name']) && $res['name']!=""){
				// echo "Yes"; exit;
				$return['response'] = $res;
			}else{
				// echo "No"; exit;
				$accessToken = $this->togenratefirebaseheaderauthentication($get);
				//echo "No ----".$accessToken; exit;
				$upquery = "UPDATE mobile_firebase_authentications SET is_active=1 WHERE company_id=".$get['company_id']." AND is_active =0";
			    $rres = $callConfig->executeQuery($upquery);
			    //echo "N1o ----".$accessToken; exit;
			    $insert_array = array('company_id'=>$get['company_id'], 'authentication_key'=>$accessToken, 'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
			    //echo "N12123o ----".$accessToken; exit;
			    $insert_array = $callConfig->insertRecord('mobile_firebase_authentications', $insert_array);
			    //echo "Noss ----".$accessToken; exit;
			    $mres = $this->tocallmobilenotificationmob($get);
			}
 		}
 		return $return;
    }*/
    function togenratefirebaseheaderauthentication($get){  
    	//echo "No"; exit;
		$url = SITEURL."/cfa_token.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$response = curl_exec($ch);
		/*if (curl_errno($ch)) {
		    echo 'cURL Error: ' . curl_error($ch);
		} else {
		    echo 'Response: ' . $response;
		}*/
		curl_close($ch);
		return $response;

    	/*global $callConfig;
    	$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';
    	//echo $serviceAccountPath; print_r($get); exit;
		$scopes = [
		    'https://www.googleapis.com/auth/userinfo.email',
		    'https://www.googleapis.com/auth/firebase.messaging',
		    'https://www.googleapis.com/auth/firebase.database',
		];
		try {
		    // Authenticate using the service account credentials
		    //echo "Test Work 5"; exit;
		    $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
		    //echo "Test Work 6"; exit;
		    $accessToken = $credentials->fetchAuthToken()['access_token'];
		    echo "Access Token: " . $accessToken . PHP_EOL;  exit;  
		    $upquery = "UPDATE mobile_firebase_authentications SET is_active=1 WHERE company_id=".$get['company_id']." AND is_active =0";
		    $rres = $callConfig->executeQuery($upquery);
		    $insert_array = array('company_id'=>$get['company_id'], 'authentication_key'=>$accessToken, 'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
		    $ins = $callConfig->insertRecord('mobile_firebase_authentications', $insert_array);
		    $mres = $this->tocallmobilenotificationmob($get);
		} catch (Exception $e) {
		    echo 'Error: ' . $e->getMessage() . PHP_EOL;
		}*/
    }
    function verifyFirebaseToken($idToken) {
	    $apiKey = "YOUR_FIREBASE_WEB_API_KEY"; // Replace with your Firebase API Key
	    $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}";

	    $postData = json_encode(["idToken" => $idToken]);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    $response = curl_exec($ch);
	    curl_close($ch);

	    $data = json_decode($response, true);

	    if (isset($data['users'])) {
	        return ['valid' => true, 'uid' => $data['users'][0]['localId']];
	    } else {
	        return ['valid' => false, 'error' => 'Invalid token'];

	    }
	}
	function generateNewToken($refreshToken) {
	    $apiKey = "YOUR_FIREBASE_WEB_API_KEY"; // Replace with your Firebase API Key
	    $url = "https://securetoken.googleapis.com/v1/token?key={$apiKey}";

	    $postData = json_encode([
	    	"grant_type" => "refresh_token",
	    	"refresh_token" => $refreshToken
	    ]);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    $response = curl_exec($ch);
	    curl_close($ch);

	    $data = json_decode($response, true);

	    if (isset($data['id_token'])) {
	    	return ['newToken' => $data['id_token']];
	    } else {
	    	return ['error' => $data['error']['message'] ?? 'Token generation failed'];
	    }
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
	function notificationsforuserid($get){
		global $callConfig;
		$whr = "";
		if($get['module_id']!=""){
			$whr = " AND  nu.module_setup_id=".$get['module_id'];
		}
		$query = "SELECT nu.module_setup_id, nu.user_id, nu.notification_type, nu.notification_id, nu.read_status, nt.notification_message, UPPER(LEFT(nt.notification_message, 2)) AS short_code, nt.application_table_name, nt.transaction_number, nt.notification_datetime, nu.read_status, nu.read_datetime, nu.id as read_id FROM notification_user nu,notifications nt WHERE nu.user_id =".$get['user_id']." AND  nu.notification_id = nt.id ".$whr." ORDER BY nt.notification_datetime DESC ";
		//echo $query."<br>";
		$datas = $callConfig->getAllRows($query);
		foreach($datas as $dd){
			if($dd->read_status==0){
				$data[$dd->notification_type][] = $dd;
			}
			if($dd->read_status==1){
				$data['READ'][$dd->notification_type][] = $dd;
			}else{
				$data['UN_READ'][$dd->notification_type][] = $dd;
			}
		}

		return $data;
	}
	function countLines() {
		$array = array('views', 'model', 'apis', 'cronjob', 'setups', 'phpqrcode', 'partials', 'includes','outsidefile');
		foreach($array as $arr){
		    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($arr));
		    $totalLines = 0;

		    foreach ($files as $file) {
		        if ($file->isFile() && $file->getExtension() == 'php') {
		            $lines = count(file($file->getRealPath()));
		            $totalLines += $lines;
		        }
		    }
		    $lines_array[$arr] = $totalLines;
		    $valtotalLines = $valtotalLines+$totalLines;
	    }
	    $lines_array['Total'] = $valtotalLines;
	    return $lines_array;
	}
	function togetemployeeattendatailsbasedondate($get){
		global $callConfig;
		$return = array('attendace_date'=>$get['date'], 'clock_in_date'=>'', 'clock_in'=>'', 'clock_out_date'=>'', 'clock_out'=>'', 'clock_type'=>'OUT');
		$query ="SELECT attendace_date, clock_in_date, clock_in, clock_out_date, clock_out FROM employee_daily_attendances WHERE company_id =".$get['company_id']." AND employee_id =".$get['emp_id']." AND attendace_date ='".$get['date']."' AND is_active = 0";
		$data = $callConfig->getRow($query);
		if(count((array)$data)>0){
			$return['clock_in'] = $data->clock_in;
			$return['clock_out'] = $data->clock_out;
			$return['clock_in_date'] = $data->clock_in_date;
			$return['clock_out_date'] = $data->clock_out_date;
			if($data->clock_in!=""&&$data->clock_out==""){
				$return['clock_type'] = "IN";
			}
		}
		return $return;
	}
	function mobileemployeeclocks_old($get){
    	global $callConfig;
    	$query = $callConfig->selectQuery('employee_daily_attendances','*',"is_active=0 AND employee_id='".$get['emp_id']."' AND attendace_date='".$get['date']."'",'','','');
		//echo $query ; exit;
    	$data = $callConfig->getRow($query);
		//print_r($data);  exit;
    	if($data->id ==""){
    		//echo "RRRR"; exit;
    		$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'],'attendace_date'=>$get['date'],'clock_in'=>$get['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>'1','device_type'=>'android');
            //echo "<pre>"; print_r($insert_array); exit;
    		$daas = $callConfig->insertRecord('employee_daily_attendances',$insert_array);
	    }
	    $queryd = $callConfig->selectQuery('employee_daily_attendance_details','*'," employee_id='".$get['emp_id']."' AND attendace_date='".$get['date']."' AND clock_out is NULL AND is_active=0",'','','');
		//echo $queryd; exit; 
	    $datad = $callConfig->getRow($queryd);
		//print_r($datad); echo "man".count((array)$datad); exit;
	    if(count((array)$datad) == "0"){
	    	$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'],'attendace_date'=>$get['date'],'clock_in'=>$get['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>'1');
	            //echo "<pre>"; print_r($insert_array); exit;
	    	$daas = $callConfig->insertRecord('employee_daily_attendance_details',$insert_array);
	    	if(count((array)$data)>"0"){
	    		$res=$callConfig->updateRecord('employee_daily_attendances', array('clock_out'=>''),'id',$data->id);
	    	}
	    }else{
	    	$insert_array = array('clock_out'=>$get['time'],'check_out_type'=>'1','in_out'=>1);
	    	//print_r($insert_array); echo "man".count($datad);
	    	$res=$callConfig->updateRecord('employee_daily_attendance_details',$insert_array,'id',$datad->id);
	    	$res=$callConfig->updateRecord('employee_daily_attendances',$insert_array,'id',$data->id);
	    }
	    $return = $this->togetemployeeattendatailsbasedondate($get);
	    return $return;
	}
	function mobileemployeeclocks($get){
    	global $callConfig;
    	$query = $callConfig->selectQuery('employee_daily_attendances','*',"is_active=0 AND employee_id='".$get['emp_id']."' AND attendace_date='".$get['date']."'",'','','');
		//echo $query ; exit;
    	$data = $callConfig->getRow($query);
		//print_r($data);  exit;
    	if($data->id ==""){
    		//echo "RRRR"; exit;
    		$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'],'attendace_date'=>$get['date'],'clock_in_date'=>$get['date'],'clock_in'=>$get['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>'1','device_type'=>'android');
            //echo "<pre>"; print_r($insert_array); exit;
    		$daas = $callConfig->insertRecord('employee_daily_attendances',$insert_array);
	    }
	    $queryd = $callConfig->selectQuery('employee_daily_attendance_details','*'," employee_id='".$get['emp_id']."' AND attendace_date='".$get['date']."' AND clock_out is NULL AND is_active=0",'','','');
		//echo $queryd; exit; 
	    $datad = $callConfig->getRow($queryd);
		//print_r($datad); echo "man".count((array)$datad); exit;
	    if(count((array)$datad) == "0"){
	    	$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'],'attendace_date'=>$get['date'],'clock_in'=>$get['time'],'check_in_type'=>'1','is_active'=>'0','current_status'=>'0','in_out'=>'1', 'clock_in_lat' => $get['clock_in_lat'] ?? '','clock_in_long' => $get['clock_in_long'] ?? '','clock_out_lat' => $get['clock_out_lat'] ?? '','clock_out_long' => $get['clock_out_long'] ?? '');
	            //echo "<pre>"; print_r($insert_array); exit;
	    	$daas = $callConfig->insertRecord('employee_daily_attendance_details',$insert_array);
	    	if(count((array)$data)>"0"){
	    		$res=$callConfig->updateRecord('employee_daily_attendances', array('clock_out'=>''),'id',$data->id);
	    	}
	    }else{
	    	$insert_array = array('clock_out'=>$get['time'],'check_out_type'=>'1','in_out'=>1, 'clock_in_lat' => $get['clock_in_lat'] ?? '','clock_in_long' => $get['clock_in_long'] ?? '','clock_out_lat' => $get['clock_out_lat'] ?? '','clock_out_long' => $get['clock_out_long'] ?? '');
	    	//print_r($insert_array); echo "man".count($datad);
	    	$res=$callConfig->updateRecord('employee_daily_attendance_details',$insert_array,'id',$datad->id);
	    	$insert_array = array('clock_out'=>$get['time'],'check_out_type'=>'1','in_out'=>1,'clock_out_date'=>$get['date']);
	    	$res=$callConfig->updateRecord('employee_daily_attendances',$insert_array,'id',$data->id);
	    }
	    $return = $this->togetemployeeattendatailsbasedondate($get);
	    return $return;
	}
	function listgetmonthattendancedetails($get){
		global $callConfig;
		//print_r($get);
		$fromdate = $get['date']!=""? $get['date'] : date('y-m-01');
		$fromdate = date('y-m-01', strtotime($fromdate));
		$todate = date('Y-m-t', strtotime($fromdate));
		if($get['emp_id']==""){
			$query = "SELECT id FROM employees WHERE user_id=".$get['user_id']." AND is_active=0";
			$eedta = $callConfig->getRow($query);
			$get['emp_id'] = $eedta->id;
		}
		$emp_id = $get['emp_id'];

		$query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get['company_id']." AND is_active =0";
    	$attendances = $callConfig->getRow($query);

    	
    	$query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get['company_id']." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
    	$listholidays = $callConfig->getAllRows($query);
    	foreach((array)$listholidays as $listholiday){
    		$holidays[] = $listholiday->holiday_date;
    	}
    	//echo "<pre>".$query; print_r($attendances); exit;
    	$query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get['company_id']." AND is_active = 0";
		$types = $callConfig->getAllRows($query);
		foreach($types as $ty){
			$type[$ty->key_short_code] = $ty->display_value;
			$return['type'][]=$ty;
		}
    	$query = "SELECT attendace_date,clock_in_date , clock_out_date,  TIME_TO_SEC(TIMEDIFF( CONCAT(clock_in_date, ' ', clock_in), CONCAT(clock_out_date, ' ', clock_out) ))/3600 AS hour_difference, clock_in,  attendance_key_short_code ,clock_out, ROUND(((TIME_TO_SEC(TIMEDIFF(CONCAT(clock_in_date, ' ', clock_in), CONCAT(clock_out_date, ' ', clock_out)))-28800)/3600)*0.6 , 2) as extra_hours, '00:10' as absent_hours  FROM employee_daily_attendances WHERE company_id =".$get['company_id']."  AND employee_id =".$get['emp_id']." AND attendace_date >= '".$fromdate."' AND attendace_date <= '".$todate."' AND is_active=0";
    	$datas = $callConfig->getAllRows($query);
    	$return['att_query'] = $query;
    	foreach($datas as $dat){
    		$dat->display_value = $type[$dat->attendance_key_short_code];
    		$data[$dat->attendace_date] =  $dat;
    		$return['attendance'][]=$dat;
    	}

    	$query = "SELECT id, working_days, full_day_hours FROM shift_settings WHERE id IN (SELECT shift_setting_id FROM shifts WHERE id IN ( SELECT shift_id FROM `emp_shifts` WHERE company_id = ".$get['company_id']."  AND emp_id = ".$get['emp_id']." AND is_active=0))";
    	$weak_off_arr = $callConfig->getRow($query);
    	if($weak_off_arr->id==''){
	    	$qry = "SELECT id, working_days, full_day_hours FROM attendance_settings 
			        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY id ASC LIMIT 1";
			$weak_off_arr = $callConfig->getRow($qry);
		}
		$fullDaySeconds = 0;
        if(!empty($weak_off_arr->full_day_hours)){
            $parts = explode(':', $weak_off_arr->full_day_hours);
            $h = isset($parts[0]) ? intval($parts[0]) : 0;
            $m = isset($parts[1]) ? intval($parts[1]) : 0;
            $s = isset($parts[2]) ? intval($parts[2]) : 0;
            $fullDaySeconds = ($h * 3600) + ($m * 60) + $s;
        }

    	//echo "<pre>".$query;print_r($data); exit;
    	//$query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get['company_id']." AND key_short_code IN ('O','P', 'A', 'H', 'Z',) AND is_active = 0";
    	
    	$working_days = json_decode($attendances->working_days,1);
    	$datediff = strtotime($todate) - strtotime($fromdate);
		$datediff = floor($datediff/(60*60*24));
		$today = date('Y-m-d');

		for($i = 0; $i < $datediff + 1; $i++){
			$otSeconds = 0;
            $utSeconds = 0;
            $otString = "00:00";
            $utString = "00:00";
            $valda =  date("Y-m-d", strtotime($fromdate . ' + ' . $i . 'day'));
			$work_days[$valda] = "N";
			$valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
            $attRow = $data[$valda];
            if(!empty($attRow->clock_in) && !empty($attRow->clock_out)){
                //$ci = strtotime($attRow->clock_in);
                //$co = strtotime($attRow->clock_out);
                $ci = strtotime($attRow->clock_in_date . ' ' . $attRow->clock_in);
				$co = strtotime($attRow->clock_out_date . ' ' . $attRow->clock_out);
                if($ci && $co){
				    $workedSeconds = $co - $ci;

				    if($workedSeconds < 0){
				        $workedSeconds = 0; // safety
				    }
                    //$workedSeconds = $co - $ci;
                    if($fullDaySeconds > 0){
                         if($workedSeconds > $fullDaySeconds){
                             $otSeconds = $workedSeconds - $fullDaySeconds;
                             $otString = gmdate("H:i", $otSeconds);
                         } elseif($workedSeconds < $fullDaySeconds){
                             $utSeconds = $fullDaySeconds - $workedSeconds;
                             $utString = gmdate("H:i", $utSeconds);
                         }
                    }
                }
            }
			
			if(isset($data[$valda]) && $data[$valda]->attendance_key_short_code!=""){
					$work_days[$valda] = $data[$valda]->attendance_key_short_code;
			}else{
				if(in_array($valday, $working_days)){						
					if(!in_array($valda, (array)$holidays)){ 
						if(strtotime($valda)<=strtotime($today)){
							if(isset($data[$valda])){
								$work_days[$valda] = "P";
							}else{
								$work_days[$valda] = "A";
							}
							
						}					
					}else{
						$work_days[$valda] = "H";
					}
				}else{
					$work_days[$valda] = "O";
				}
			}
			//$return[] = $work_days;
			$xyz = array(
			'attendace_date'=> $valda,
			'attendance_key_short_code'=> $work_days[$valda],
			'hour_difference'=> "",
			'clock_in'=> "00:00",
			'clock_out'=> "00:00",
			'extra_hours'=> "00:00",
			'absent_hours'=> "00:00");

			$array = isset($data[$valda]) ? (array)$data[$valda] : $xyz;
			$array['extra_hours'] =$otString;
			$array['absent_hours'] = $utString;
			$array['status'] = $work_days[$valda];
			$array['display_value'] = $type[$work_days[$valda]];
			$array['clock_in'] = $array['clock_in'] !="" ? $array['clock_in'] : '00:00';
			$array['clock_out'] = $array['clock_in'] !="" ? $array['clock_out'] : '00:00';
			// $array = array('date'=>$valda, 'status'=>$work_days[$valda]);
			$return['data'][] = $array;
			$work_days = array();
		}
		//if($get['data_name']=='infisuitein_suwalka'){
			$return['attendance'] = $return['data'];
		//}
		
		//return array('data'=>$work_days, 'type'=>$type, 'attendance'=>$data);
		return $return;
 	}
	function listgetmonthattendancedetails_old($get){
		global $callConfig;
		//print_r($get);
		$fromdate = $get['date']!=""? $get['date'] : date('y-m-01');
		$fromdate = date('y-m-01', strtotime($fromdate));
		$todate = date('Y-m-t', strtotime($fromdate));
		$emp_id = $get['emp_id'];

		$query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get['company_id']." AND is_active =0";
    	$attendances = $callConfig->getRow($query);

    	
    	$query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get['company_id']." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
    	$listholidays = $callConfig->getAllRows($query);
    	foreach((array)$listholidays as $listholiday){
    		$holidays[] = $listholiday->holiday_date;
    	}
    	//echo "<pre>".$query; print_r($attendances); exit;
    	$query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get['company_id']." AND is_active = 0";
		$types = $callConfig->getAllRows($query);
		foreach($types as $ty){
			$type[$ty->key_short_code] = $ty->display_value;
			$return['type'][]=$ty;
		}
    	$query = "SELECT attendace_date, TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))/3600 AS hour_difference, clock_in,  attendance_key_short_code ,clock_out, ROUND(((TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))-28800)/3600)*0.6 , 2) as extra_hours, '00:10' as absent_hours  FROM employee_daily_attendances WHERE company_id =".$get['company_id']."  AND employee_id =".$get['emp_id']." AND attendace_date >= '".$fromdate."' AND attendace_date <= '".$todate."' AND is_active=0";
    	$datas = $callConfig->getAllRows($query);

    	foreach($datas as $dat){
    		$dat->display_value = $type[$dat->attendance_key_short_code];
    		$data[$dat->attendace_date] =  $dat;
    		$return['attendance'][]=$dat;
    	}

    	$query = "SELECT id, working_days, full_day_hours FROM shift_settings WHERE id IN (SELECT shift_setting_id FROM shifts WHERE id IN ( SELECT shift_id FROM `emp_shifts` WHERE company_id = ".$get['company_id']."  AND emp_id = ".$get['emp_id']." AND is_active=0))";
    	$weak_off_arr = $callConfig->getRow($query);
    	if($weak_off_arr->id==''){
	    	$qry = "SELECT id, working_days, full_day_hours FROM attendance_settings 
			        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY id ASC LIMIT 1";
			$weak_off_arr = $callConfig->getRow($qry);
		}
		$fullDaySeconds = 0;
        if(!empty($weak_off_arr->full_day_hours)){
            $parts = explode(':', $weak_off_arr->full_day_hours);
            $h = isset($parts[0]) ? intval($parts[0]) : 0;
            $m = isset($parts[1]) ? intval($parts[1]) : 0;
            $s = isset($parts[2]) ? intval($parts[2]) : 0;
            $fullDaySeconds = ($h * 3600) + ($m * 60) + $s;
        }

    	//echo "<pre>".$query;print_r($data); exit;
    	//$query = "SELECT key_name,key_bgcolor, key_short_code,  color_code, key_text_color, display_value FROM attendance_key_types WHERE company_id =".$get['company_id']." AND key_short_code IN ('O','P', 'A', 'H', 'Z',) AND is_active = 0";
    	
    	$working_days = json_decode($attendances->working_days,1);
    	$datediff = strtotime($todate) - strtotime($fromdate);
		$datediff = floor($datediff/(60*60*24));
		$today = date('Y-m-d');

		for($i = 0; $i < $datediff + 1; $i++){
			$otSeconds = 0;
            $utSeconds = 0;
            $otString = "00:00";
            $utString = "00:00";
            $valda =  date("Y-m-d", strtotime($fromdate . ' + ' . $i . 'day'));
			$work_days[$valda] = "N";
			$valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
            $attRow = $data[$valda];
            if(!empty($attRow->clock_in) && !empty($attRow->clock_out)){
                $ci = strtotime($attRow->clock_in);
                $co = strtotime($attRow->clock_out);
                if($co > $ci){
                    $workedSeconds = $co - $ci;
                    if($fullDaySeconds > 0){
                         if($workedSeconds > $fullDaySeconds){
                             $otSeconds = $workedSeconds - $fullDaySeconds;
                             $otString = gmdate("H:i", $otSeconds);
                         } elseif($workedSeconds < $fullDaySeconds){
                             $utSeconds = $fullDaySeconds - $workedSeconds;
                             $utString = gmdate("H:i", $utSeconds);
                         }
                    }
                }
            }
			
			if(isset($data[$valda]) && $data[$valda]->attendance_key_short_code!=""){
					$work_days[$valda] = $data[$valda]->attendance_key_short_code;
			}else{
				if(in_array($valday, $working_days)){						
					if(!in_array($valda, (array)$holidays)){ 
						if(strtotime($valda)<=strtotime($today)){
							if(isset($data[$valda])){
								$work_days[$valda] = "P";
							}else{
								$work_days[$valda] = "A";
							}
							
						}					
					}else{
						$work_days[$valda] = "H";
					}
				}else{
					$work_days[$valda] = "O";
				}
			}
			//$return[] = $work_days;
			$xyz = array(
			'attendace_date'=> $valda,
			'attendance_key_short_code'=> $work_days[$valda],
			'hour_difference'=> "",
			'clock_in'=> "00:00",
			'clock_out'=> "00:00",
			'extra_hours'=> "00:00",
			'absent_hours'=> "00:00");

			$array = isset($data[$valda]) ? (array)$data[$valda] : $xyz;
			$array['extra_hours'] =$otString;
			$array['absent_hours'] = $utString;
			$array['status'] = $work_days[$valda];
			$array['display_value'] = $type[$work_days[$valda]];
			$array['clock_in'] = $array['clock_in'] !="" ? $array['clock_in'] : '00:00';
			$array['clock_out'] = $array['clock_in'] !="" ? $array['clock_out'] : '00:00';
			// $array = array('date'=>$valda, 'status'=>$work_days[$valda]);
			$return['data'][] = $array;
			$work_days = array();
		}
		if($get['data_name']=='infisuitein_suwalka'){
			$return['attendance'] = $return['data'];
		}
		
		//return array('data'=>$work_days, 'type'=>$type, 'attendance'=>$data);
		return $return;
 	}
 	function togetyearlymonthwisedataperce($get){
 		global $callConfig;
 		$year = $get['year']!="" ? $get['year'] : date('Y');
 		$query = "SELECT id, working_days FROM attendance_settings WHERE company_id =".$get['company_id']." AND is_active =0";
    	$attendances = $callConfig->getRow($query);
 		$working_days = json_decode($attendances->working_days,1);
 		$months = array( 1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
 		$query = "SELECT count(id) as hday, month(holiday_date) hmonth FROM `holidays` WHERE company_id = ".$get['company_id']." AND holiday_year = ".$year." AND is_active =0 GROUP BY hmonth";
 		$hdatas = $callConfig->getAllRows($query);
 		foreach($hdatas as $hdata){
 			$holid[$hdata->hmonth] = $hdata->hday;
 		}
 		$equery = "SELECT MONTH(attendace_date) atmonth, SUM(if((TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))/3600)>0 , 1,0)) dayaaa FROM employee_daily_attendances WHERE company_id =".$get['company_id']." AND employee_id =".$get['emp_id']." AND YEAR(attendace_date) = ".$year." AND is_active=0 GROUP BY atmonth";
    	$eatdatas = $callConfig->getAllRows($equery);
    	//echo $equery;
    	foreach($eatdatas as $eatda){
    		$empattm[$eatda->atmonth] =  $eatda->dayaaa;
    	}
 		$workingDaysByMonth = array();

	    for ($month = 1; $month <= 12; $month++) {
	    	$workingDays = 0;
	    	$startDate = date("Y-m-d", strtotime($year."-".$month."-01"))		;
	    	//echo "-----UUUUUU---".$startDate; exit;
     		$daysInMonth = date('Y-m-t', strtotime($startDate));
     		//echo $startDate."-------------------".$daysInMonth; exit;
     		$datediff = strtotime($daysInMonth) - strtotime($startDate);
			$datediff = floor($datediff/(60*60*24));
			$alldays =0; $week_off = array(); $week_work = array();
		   	for ($day = 0; $day <= $datediff; $day++) {
		   		$valda =  date("Y-m-d", strtotime($startDate . ' + ' . $day . 'day'));
				$valday = ucwords(date("l", strtotime($startDate . ' + ' . $day . 'day')));
				//echo $valda."-------------------".$valda; exit;
				if(in_array($valday, $working_days)){	
					$workingDays++;
					$week_work[]=$day."--".$valday."//".$valda;
				}else{
					$week_off[]=$day."--".$valday."//".$valda;
				}
				$alldays = $alldays++;
	    	}
	    	/*$workingDaysByMonth[$month]['total_days'] = $day;
	    	$workingDaysByMonth[$month]['datediff'] = $datediff;
	    	$workingDaysByMonth[$month]['week_off'] =$week_off;
	    	$workingDaysByMonth[$month]['week_work'] = $week_work;
	    	$workingDaysByMonth[$month]['rm_sun_work_days'] = $workingDays;
	    	$workingDaysByMonth[$month]['holdays'] = $holid[$month];*/
	    	if(isset($holid[$month])){
	    		$workingDays = $workingDays-$holid[$month];
	    	}
	    	$workingDaysByMonth[$month]['month_name'] = $months[$month];
	    	$workingDaysByMonth[$month]['month'] = $month;

	    	

	        $workingDaysByMonth[$month]['work_days'] = $workingDays;
	        $workingDaysByMonth[$month]['present'] = 0;
	        $workingDaysByMonth[$month]['absent'] = 0;
	        $workingDaysByMonth[$month]['precentage'] = 0;
	        $workingDaysByMonth[$month]['extra_hours'] = 0;
	        if(isset($empattm[$month])){
	        		
	        	$workingDaysByMonth[$month]['present'] = intval($empattm[$month]);
	       		$workingDaysByMonth[$month]['absent'] = $workingDays-$empattm[$month];
	        	$workingDaysByMonth[$month]['precentage'] = round(($empattm[$month]/$workingDays)*100);
	        }
	        $return['month_data'][] = $workingDaysByMonth[$month];
	    }	    
	    //return $workingDaysByMonth;
	    return $return;
 	}
 	function togetmobileleavedetails($get){
 		global $callConfig;
 		//echo "---"; print_r($get); exit;
 		$query = "SELECT id, leave_type_name FROM leave_types WHERE company_id =".$get['company_id']."  AND is_active = 0";
 		$leaves = $callConfig->getAllRows($query);
 		foreach($leaves as $leav){
 			$leave[$leav->id] = $leav->leave_type_name; 
 			$leave_type[]=$leav;
 		}
 		$return['type'] = $leave_type;
 		$query = "SELECT * FROM `status_progress` WHERE  company_id =".$get['company_id']." AND table_name = 'employee_leaves' AND is_active = 0";
 		$statusprocess = $callConfig->getAllRows($query);
 		foreach($statusprocess as $statusproc){
 			$status[$statusproc->status] = $statusproc->name;
 		}
 		$query = "SELECT * FROM emp_leave_balances WHERE company_id =".$get['company_id']." AND employee_id =".$get['emp_id']." AND is_active = 0";
 		$leave_balance = $callConfig->getAllRows($query);
 		//print_r($leave); exit;
 		$leave_left = 0; $leave_used=0;
 		foreach($leave_balance as $balance){

 			$leavetypedate['applicable_leaves'] =$balance->leave_type;
 			//print_r($balance); exit;
 			$leavetypedate['name'] = $leave[$balance->leave_type];
 			//print_r($balance); exit;
 			$leavetypedate['applicable_leaves'] = $balance->applicable_leaves-$balance->consumed_leaves;
 			//$leavetypedate['applicable_leaves'] = $balance->consumed_leaves;
 			$leave_type_data[] = $leavetypedate;
 			$leave_left = $leave_left+$balance->applicable_leaves;
 			$leave_used = $leave_used+$balance->consumed_leaves;
 			$leftdd[] = $balance->applicable_leaves."-".$balance->consumed_leaves;
 		}
 		$return['leave_type_data'] = $leave_type_data;	
 		$return['leave_total'] = $leave_left;
 		$return['leave_left'] = $leave_left-$leave_used;
 		$return['leave_used'] = $leave_used;
 		//$return['leftdd'] = $leftdd;
 		$query = "SELECT * FROM employee_leaves WHERE company_id =".$get['company_id']." AND employee_id =".$get['emp_id']." AND is_active = 0";
 		$allleaves = $callConfig->getAllRows($query);
 		foreach($allleaves as $allleav){
 			$app_leave['id'] = $allleav->id;
 			$app_leave['leave_type_id'] = $allleav->leave_type_id;
 			$app_leave['no_of_days'] = $allleav->leave_days;
 			$app_leave['leave_from'] = $allleav->leave_from;
 			$app_leave['leave_to'] = $allleav->leave_to;
 			$app_leave['leave_name'] = $leave[$allleav->leave_type_id];
 			$app_leave['from_time'] = $allleav->from_time;
 			$app_leave['to_time'] = $allleav->to_time;
 			$app_leave['leave_duration_type'] = $allleav->leave_duration_type;
 			$app_leave['special_request'] = $allleav->special_request; 	
 			$app_leave['applied_date'] = date("Y-m-d", strtotime($allleav->application_date)); 			
 			$app_leave['status'] = $allleav->current_status;
 			$app_leave['status_name'] = $status[$allleav->current_status];
 			$all_app_leave[]=$app_leave;
 		}
 		$return['applied_leave'] = $all_app_leave;
 		return $return;
 	}
 	function toaddmobileempleave_old($get, $post){
 		global $callConfig;
 		//print_r($post); exit;
 		$leave_from_at = str_replace('/"', '-', $post['leave_from']);
    	$leave_from_at =  date('Y-m-d', strtotime($leave_from_at));
    	$leave_to_at = str_replace('/"', '-', $post['leave_to']);
    	$leave_to_at =  date('Y-m-d', strtotime($leave_to_at));
    	if($post['ref_id']!=""){
    		$insert_array = array('leave_type_id'=>$post['leave_type_id'],'leave_from'=>$leave_from_at,'leave_to'=>$leave_to_at,'leave_days'=>$post['leave_days'], 'special_request'=>$post['special_request'],'current_status'=>'0','is_active'=>'0','updated_by'=>$get['user_id']);
			$res = $callConfig->updateRecord('employee_leaves',$insert_array,'id',$post['ref_id']);
			$ret['insert'] =$res;
    	}else{
	 		$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'], 'special_request'=>$post['special_request'],'leave_type_id'=>$post['leave_type_id'],'leave_from'=>$leave_from_at,'leave_to'=>$leave_to_at,'leave_days'=>$post['leave_days'],'current_status'=>'0','is_active'=>'0','created_by'=>$get['user_id']);
			$insert = $callConfig->insertRecord('employee_leaves',$insert_array);

			$ret['insert'] =$insert;
		}
		return $ret;
 	}
 	function toaddmobileempleave($get, $post){
 		global $callConfig;
 		//print_r($post); exit;
 		$leave_from_at = str_replace('/"', '-', $post['leave_from']);
    	$leave_from_at =  date('Y-m-d', strtotime($leave_from_at));
    	$leave_to_at = str_replace('/"', '-', $post['leave_to']);
    	$leave_to_at =  date('Y-m-d', strtotime($leave_to_at));
    	if($post['ref_id']!=""){
    		$insert_array = array('leave_type_id'=>$post['leave_type_id'],'leave_from'=>$leave_from_at,'leave_to'=>$leave_to_at,'leave_days'=>$post['leave_days'], 'special_request'=>$post['special_request'],'current_status'=>'0','is_active'=>'0','updated_by'=>$get['user_id'],'from_time'=>$post['from_time'] ?? '','to_time'=>$post['to_time'] ?? '','leave_duration_type'=>$post['leave_duration_type'] ?? '');
			$res = $callConfig->updateRecord('employee_leaves',$insert_array,'id',$post['ref_id']);
			$ret['insert'] =$res;
    	}else{
	 		$insert_array = array('company_id'=>$get['company_id'],'employee_id'=>$get['emp_id'], 'special_request'=>$post['special_request'],'leave_type_id'=>$post['leave_type_id'],'leave_from'=>$leave_from_at,'leave_to'=>$leave_to_at,'leave_days'=>$post['leave_days'],'current_status'=>'0','is_active'=>'0','created_by'=>$get['user_id'],'from_time'=>$post['from_time'] ?? '','to_time'=>$post['to_time'] ?? '','leave_duration_type'=>$post['leave_duration_type'] ?? '');
			$insert = $callConfig->insertRecord('employee_leaves',$insert_array);

			$ret['insert'] =$insert;
		}
		return $ret;
 	}
 	function toupdatemobileempleave($get, $post){
 		global $callConfig; 	
 		$leave_from_at = str_replace('/"', '-', $post['leave_from']);
    	$leave_from_at =  date('Y-m-d', strtotime($leave_from_at));
    	$leave_to_at = str_replace('/"', '-', $post['leave_to']);
    	$leave_to_at =  date('Y-m-d', strtotime($leave_to_at));
 		$insert_array = array('leave_type_id'=>$post['leave_type_id'],'leave_from'=>$leave_from_at,'leave_to'=>$leave_to_at,'leave_days'=>$post['leave_days'], 'special_request'=>$post['special_request'],'current_status'=>'0','is_active'=>'0','created_by'=>$get['user_id']);
		$res = $callConfig->updateRecord('employee_leaves',$insert_array,'id',$post['ref_id']);
		$ret['update'] =$res;
		return $ret;
 	}
 	function todeletemobileempleave($get){
 		global $callConfig; 
 		$update_query = "UPDATE employee_leaves SET is_active = NULL WHERE id =".$get['ref_id'];
 		//echo $update_query;
 		$res = $callConfig->executeQuery($update_query);
 		return $res;
 	}
 	function toinsertfieldmapuser($get,$post){
 		global $callConfig;
 		//echo "------ Test ----";  print_r($get);  print_r($file); exit;
 		$current_status = $post['current_status']!='' ? $post['current_status'] :'0';
 		$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$get['user_id'], 'map_title' => $post['map_title'], 'phone'=>$post['phone'], 'address'=>$post['address'] , 'map_latitude'=>$post['map_latitude'],'map_longitude'=>$post['map_longitude'], 'map_date'=>$post['map_date'],'map_time'=>$post['map_time'],'image'=>'file_1689092069_64ad7fe56a8f4.jpg', 'map_distance'=>$post['map_distance'],'comments'=>$post['comments'],'work_type'=>'ON', 'field_work_daily_plan_id'=> $post['field_work_daily_plan_id'], 'status_radius'=> $post['status_radius'], 'remarks'=> $post['remarks'] ,'current_status'=>$current_status,'is_active'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('field_work_user_map_points',$insert_array);
		$ret['insert'] =$insert;
		return $ret;
 	}
 	function togetfieldworkusermappoints($get){
 		global $callConfig;

 		$query = "SELECT id,map_title, map_latitude, map_longitude, map_date, map_time,'file_1689092069_64ad7fe56a8f4.jpg' as image, map_distance, comments,status_radius, field_work_daily_plan_id, status_radius , remarks  FROM field_work_user_map_points WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND map_date = '".$get['date']."' AND is_active =0";
 		//echo $query;
		$datas = $callConfig->getAllRows($query);
		foreach($datas as $data){
			$ret[]=$data;
		}
		$return['data'] = $ret;
		
		return $return;
 	}
 	function tocreatecomplanatedetails($get, $post){
 		global $callConfig;
 		$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$get['user_id'],'complaint_name'=>$post['complaint_name'],'complaint_type'=>$post['complaint_type'], 'transaction_date'=>$post['transaction_date'] ,'complaint_priority'=>$post['complaint_priority'],'building_id'=>$post['building_id'],'building_floor_id'=>$post['building_floor_id'],'building_floor_space_id'=>$post['building_floor_space_id'],'item_id'=>$post['item_id'],'asset_id'=>$post['asset_id'],'complaint_image'=>$complaint_image, 'remarks'=>$post['remarks'], 'table_name'=>$post['table_name'],'party_id'=>$post['party_id'], 'ref_id'=>$post['ref_id'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
 		$insert = $callConfig->insertRecord('complaints',$insert_array);
 		$ret['insert'] =$insert;
 		/** if(isset($post['images'])){
 			$images = json_decode($post['images'],1);
 			if(count((array)$images)>0){
	 			$post['images'] = $images;
	 			$post['transaction_id'] = $insert['LASTID'];
	 			$post['application_table_name'] = 'complaints';
	 			$res = $this->multipleimagesapplicationattachments($get, $post);
 			}
 		} **/
 		$insert_array = array('company_id'=>$get['company_id'],'transaction_table'=>'complaints','transaction_id'=>$insert['LASTID'],'previous_status'=>0, 'request_date'=>$post['transaction_date'] ,'assigner_user_id'=>$get['user_id'],'user_id'=>$get['user_id'],'module_setup_id'=>$get['module_id']);
 		$insert = $callConfig->insertRecord('transaction_approval_logs',$insert_array);

		$ret['trn_insert'] =$insert;
		$ret['post'] =$post;
		return $ret;
 		//complainant_name, complainant_type, complaint_description, building_id, building_floor_id, building_floor_space_id, item_id, asset_id, complaint_image, remarks
 	}
 	function multipleimagesapplicationattachments($get,$post){
		global $callConfig;
		foreach($post['images'] as $key=>$img){
			$insert_array = array('company_id'=>$get['company_id'],'file_name'=>$img,'actual_file_name'=>$img,'file_size_mb'=>0,'application_table_name'=>$post['application_table_name'],'transaction_id'=>$post['transaction_id'],'is_active' =>'0');
			$res = $callConfig->insertRecord('application_attachments',$insert_array);		
		}
		return $res;
	}
 	function togetapproalcomplientdetails($get){
 		global $callConfig;
 		//print_r($get); exit;
 		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = $data_stat->background_color;
				$arr_status[$data_stat->name_value]['color'] = $data_stat->color;
		}
		$approval_query = "SELECT ta.id, ta.module_setup_id, ta.transaction_id, ta.transaction_table FROM transaction_approval_logs ta WHERE ta.company_id = ".$get['company_id']." AND ta.transaction_table = 'complaints' AND ta.user_id = ".$get['user_id']." AND ta.response_type = 0";
		$app_data = $callConfig->getAllRows($approval_query);
		//print_r($app_data); exit;
		foreach((array)$app_data as $app){
			$query = "SELECT tb.id, tb.complaint_type, tb.transaction_date, tb.current_status, tb.complaint_name, au.display_name originator_name, au.user_photo originator_image, sp.name status_name FROM ".$app->transaction_table."  tb, application_users au, status_progress sp  WHERE tb.id=".$app->transaction_id." AND tb.created_by = au.id AND sp.table_name = '".$app->transaction_table."' AND tb.current_status = sp.status AND tb.is_active=0";
			//echo $query; exit;
			$list_data = $callConfig->getRow($query);
			//if($list_data->current_status==30){
			    $data=(array)$list_data;
				$data['transaction_number']=$list_data->id;
				$data['background_color']=$arr_status[$list_data->current_status]['background_color'];
				$data['text_color']=$arr_status[$list_data->current_status]['color'];
				$data['status_name']= $list_data->status_name;
				$data['originator_name']= $list_data->originator_name;
				$data['originator_initial']= $this->initials($list_data->originator_name);
				$data['originator_image']= $list_data->originator_image;	
				//$all_data[$app->transaction_table][] =$data;		
				$all_data['data'][] =$data;
			//}
		}
		return $all_data; 		
 	}
 	
 	function togetcreatecomplientdetails($get){
 		global $callConfig;
 		$query = "SELECT name_value, name, background_color, color FROM `status` WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
		$data_status = $callConfig->getAllRows($query);
		$arr_status = array();
		foreach($data_status as $data_stat){
				$arr_status[$data_stat->name_value]['background_color'] = $data_stat->background_color;
				$arr_status[$data_stat->name_value]['color'] = $data_stat->color;
		}
		//print_r($arr_status); exit;
 		$query = "SELECT tb.id, tb.complaint_type, tb.transaction_date, tb.current_status, tb.complaint_name, tb.remarks, tb.complaint_priority, au.display_name originator_name, au.user_photo originator_image, sp.name status_name FROM complaints tb, application_users au, status_progress sp  WHERE  tb.created_by = ".$get['user_id']." AND tb.user_id= au.id AND tb.current_status = sp.status  AND sp.table_name = 'complaints' AND tb.current_status = sp.status AND tb.is_active =0";
			//echo $query; exit;
			$list_datas = $callConfig->getAllRows($query);
			//print_r($list_datas); exit;
			foreach((array)$list_datas as $list_data){
			    $data=(array)$list_data;
				$data['transaction_number']=$list_data->id;
				$data['background_color']=$arr_status[$list_data->current_status]['background_color'];
				$data['text_color']=$arr_status[$list_data->current_status]['color'];
				$data['status_name']= $list_data->status_name;
				$data['originator_name']= $list_data->originator_name;
				$data['originator_initial']= $this->initials($list_data->originator_name);
				$data['originator_image']= $list_data->originator_image;	
				$all_data['data'][] =$data;		
			}
		return $all_data;
 	}
 	function togenrateqrcodebasetype($get){
 		
 		$array = array('item'=>'Item', 'building'=> 'Building Location', 'asset'=> 'Asset');
 		$i=10;
 		foreach($array as $key=>$arr){
 			$data['key'] = $key;
 			$data['name'] = $arr;
 			$data['priority'] = $i;
 			$i = $i+10;
 			$return['type'][]= $data;
 		}
 		return $return;
 	}
 	function togenrateqrcodebaseontypelist($get){
 		global $callConfig;
 		$array = array('item'=>'Item', 'building_floor'=> 'Building Location', 'asset'=> 'Asset');
 		if($get['type']=='item'){
 			$query = "SELECT id, sku, item_name, sale_price FROM items WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
			$data = $callConfig->getAllRows($query);
 		}
 		if($get['type']=='building'){
 			$query = "SELECT id, building_name FROM buildings WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
			$data = $callConfig->getAllRows($query);
 		}
 		if($get['type']=='asset'){
 			$query = "SELECT id, asset_code, asset_description FROM assets WHERE `company_id` = ".$get['company_id']." AND `is_active` = 0";
			$data = $callConfig->getAllRows($query);
 		}
 		foreach((array)$data as $da){
 			$dat = (array)$da;
 			$return['data'][]=$dat;
 		}
 		return $return;
 	}
 	function togetbulidingfloors($get){
 		global $callConfig;
 		$query = "SELECT id,  floor_name FROM building_floors WHERE `company_id` = ".$get['company_id']." AND building_id =".$get['building_id']." AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		foreach((array)$data as $da){
 			$dat = (array)$da;
 			$return['data'][]=$dat;
 		}
 		return $return;
 	}
 	function togetemloyeemodeleraiodcricle($get){
 		global $callConfig;
 		//echo "TEstt"; exit;
 		$chek = array('1'=>'quotations','2'=>'goods_receipt_notes','3'=>'sales_orders','26'=>'requisitions');
 		$chek_mod = array('1','2','3','26');
 		if(in_array($get['module_id'],$chek_mod)){
 			$query = "SELECT SUM(IF(response_type!=0,1,0)) approved, SUM(IF(response_type=0,1,0)) pending FROM transaction_approval_logs WHERE transaction_table IN ('".$chek[$get['module_id']]."') AND user_id = ".$get['user_id'];
 			$da_data = $callConfig->getRow($query);
 			$att = $da_data->approved!="" ? $da_data->approved : 0;
 			$abs = $da_data->pending!="" ? $da_data->pending : 0;
 			$total = $att+$abs;
 			$data['first']['name'] = 'Approved'; 
			$data['first']['value'] = $att; 
			$data['first']['percentage'] = $total>0 ?round(($att/$total)*100):0;
			$data['second']['name'] = 'Processing'; 
			$data['second']['value'] = $abs; 
			$data['second']['percentage'] = $total>0 ?round(($abs/$total)*100):0;
			$data['query'] = $query;
 		}else if($get['module_id']==7){
 			$month = date('m');
 			$year = date('Y');
 			$att = 0;
 			$abs = 0;
 			$total =0;
			$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
			$atsdata = $callConfig->getRow($attstc);
			$workingdays = json_decode($atsdata->working_days,1);
			$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
			$holidaysdata = $callConfig->getAllRows($holidays); //holidays
			foreach ($holidaysdata as $hkey=>$hvalue){
				$holidaysdatas[] = $hvalue->holiday_date;
			}
			$empattsql = "SELECT attendace_date FROM employee_daily_attendances WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."' AND employee_id = (SELECT id FROM employees WHERE user_id=".$get['user_id']." AND is_active =0 ) ";
			$empattdata = $callConfig->getAllRows($empattsql); //holidays
			foreach ($empattdata as $attvalue){
				$attendace_dates[] = $attvalue->attendace_date;
			}
			$start_date = date('Y-m-01');
			$end_date = date('Y-m-d');
			//print_r($workingdays); print_r($attendace_dates);echo "start".$start_date.":end".$end_date."<br>"; exit;
			for ($i = strtotime($start_date); $i <= strtotime($end_date); $i = strtotime('+1 day', $i)) {
				$date = date('Y-m-d',$i);
				$dy =  date('l',$i);
				//echo $date.":day:".$dy."<br>";
				if(!in_array($date,(array)$holidaysdatas) && (in_array($dy, (array)$workingdays))) {
					if(in_array($date,(array)$attendace_dates)){
						$att = $att+1;
					}else{
						$abs = $abs+1;
					}
					$total = $total+1;
				}
			}
			$data['first']['name'] = 'Present Days'; 
			$data['first']['value'] = $att; 
			$data['first']['percentage'] = round(($att/$total)*100); 
			$data['second']['name'] = 'Absent  Days'; 
			$data['second']['value'] = $abs; 
			$data['second']['percentage'] = round(($abs/$total)*100);
 		}elseif($get['module_id']==38){
 			$query = "SELECT id,user_field_work_type  FROM application_users WHERE id =".$get['user_id'];
 			$user_data = $callConfig->getRow($query);
 			$ndate = date("Y-m-d");
 			//print_r($user_data);
 			if($user_data->user_field_work_type!=3){
 				$query = "SELECT count(id) as daily_plan FROM field_work_daily_plans WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND plan_date = '".$ndate."' AND current_status=0 AND is_active = 0";
 				$da_data = $callConfig->getRow($query);
 				$query = "SELECT count(id) as work_plan FROM field_work_user_map_points WHERE company_id = ".$get['company_id']." AND field_work_daily_plan_id IN (SELECT id FROM field_work_daily_plans WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND plan_date = '".$ndate."' AND current_status=0 AND is_active = 0)  AND is_active = 0 ";
 				$wk_data = $callConfig->getRow($query);
 				//echo "<pre>"; print_r($da_data); print_r($wk_data); exit;
 				$total = $da_data->daily_plan;
 				$abs = $wk_data->work_plan;
 				$att = $total- $abs;
 				$data['first']['name'] = 'Visited'; 
				$data['first']['value'] = $abs;
				$data['first']['percentage'] = $total >0 ? round(($abs/$total)*100):100; 
				$data['second']['name'] = 'Pending'; 
				$data['second']['value'] =  $att; 
				$data['second']['percentage'] =  $total >0 ? round(($att/$total)*100):0;
 			}else{
 				$st = date('Y-m-01');
 				$en = date('Y-m-t');
 				//$st = "2025-01-01";  $en = "2025-01-31"; 
 				$query = "SELECT COUNT(DISTINCT(map_date)) att_dal FROM field_work_user_map_points WHERE company_id =".$get['company_id']." AND map_date >= '".$st."' AND map_date <= '".$en."' AND user_id = ".$get['user_id']." AND is_active = 0";
 				$att_data = $callConfig->getRow($query);
 				$total = date('t');
 				$abs = $att_data->att_dal;
 				$att = $total- $abs;
 				$data['first']['name'] = 'Present Days'; 
				$data['first']['value'] = $abs;
				$data['first']['percentage'] = round(($abs/$total)*100); 
				$data['second']['name'] = 'Absent  Days'; 
				$data['second']['value'] =  $att; 
				$data['second']['percentage'] = round(($att/$total)*100);
 			}
 			
 		}elseif($get['module_id']==39){ 	
 			$query = "SELECT   d.user_id, COUNT(CASE WHEN ta.current_status = 4 THEN 1 END) AS complete, COUNT(CASE WHEN ta.current_status != 4 THEN 1 END) AS pending,COUNT(*) AS total_assignments FROM drivers d JOIN transport_assign ta ON d.id = ta.driver_id WHERE d.user_id =".$get['user_id']." AND ta.is_active = 0";
 			//echo $query;
 			$res= $callConfig->getRow($query);
 			$total = $res->total_assignments;
 			$abs = $res->complete;
 			$att = $res->pending;
 			$data['first']['name'] = 'Completed'; 
			$data['first']['value'] = $abs;
			$data['first']['percentage'] = $total >0 ? round(($abs/$total)*100):100; 
			$data['second']['name'] = 'Pending'; 
			$data['second']['value'] =  $att; 
			$data['second']['percentage'] = round(($att/$total)*100);

 		}elseif($get['module_id']==12){ 	
 			$query = "SELECT   ta.user_id, COUNT(CASE WHEN ta.current_status = 4 THEN 1 END) AS complete, COUNT(CASE WHEN ta.current_status != 4 THEN 1 END) AS pending,COUNT(*) AS total_assignments FROM  emp_expenses ta WHERE ta.company_id =".$get['company_id']." AND ta.user_id =".$get['user_id']." AND ta.is_active = 0";
 			//echo $query;
 			$res= $callConfig->getRow($query);
 			$total = $res->total_assignments;
 			$abs = $res->complete;
 			$att = $res->pending;
 			$data['first']['name'] = 'Completed'; 
			$data['first']['value'] =  $abs!=""?$abs:0 ; 
			$data['first']['percentage'] = $total >0 ? round(($abs/$total)*100):100; 
			$data['second']['name'] = 'Pending'; 
			$data['second']['value'] =  $att!=""?$att:0 ; 
			$data['second']['percentage'] = $total >0 ? round(($att/$total)*100):100;

 		}else{
 			$data['first']['name'] = 'Approved'; 
			$data['first']['value'] = 8; 
			$data['first']['percentage'] = 73; 
			$data['second']['name'] = 'Processing'; 
			$data['second']['value'] = 3; 
			$data['second']['percentage'] = 27;
 		}
 		//$data = array('frist'=>'18', 'second'=>'3');
 		//print_r($data); exit;
 		return $data; 
 	}
 	function tocheckpermissiontoqrcodeapproval($get){
 		global $callConfig;
 		$list_status['genrate_qr_code'] = 'NO';
 		$list_status['approval'] = 'NO';
 		//if($get['module_id']==34){
 			$roleshort = array('OFFICE_SECURITY_GUARD', 'OFFICE_USER'); 			
 			$query = "SELECT role_short_code FROM application_user_module_roles WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active=0";
 			$data = $callConfig->getAllRows($query);
 			foreach($data as $da){
 				$check[] = $da->role_short_code; 
 				if(!in_array($da->role_short_code,$roleshort)){
 					$list_status['genrate_qr_code'] = 'YES';
 					$list_status['approval'] = 'YES';
 				}
 			}
 			
 		//}
 		$return['data']=$list_status;
 		return $return;
 	}
 	function togetcomplientsbyiddetails($get){
 		global $callConfig;
 		$query = "SELECT * FROM complaints WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND id=".$get['complaint_id']."  AND is_active=0";
 		$data = $callConfig->getRow($query);
 		$return['data']=$data;
 		$return['history'] = array();
 		$query = "SELECT `transaction_date`, `complaint_image`, `remarks` FROM complaint_responses WHERE `company_id` = ".$get['company_id']." AND complaint_id =".$get['complaint_id']." AND is_active = 0";
		$hdata = $callConfig->getAllRows($query);
		if(count((array)$hdata)>0){
			foreach((array)$hdata as $da){
	 			$dat = (array)$da;
	 			$return['history'][]=$dat;
 			}
		}
		
 		
 		return $return;
 	}
	function todeletecomplaintbasedonid($get){
 		global $callConfig;
 		$upquery = "UPDATE complaints SET is_active = NULL, updated_by = ".$get['user_id']."  WHERE company_id =".$get['company_id']." AND id=".$get['complaint_id']."  AND is_active=0";
 		$data = $callConfig->executeQuery($upquery);
 		//$return['data']=$data;
 		return $data;
 	}
 	function toupdatecomplanatedetails($get, $post){
 		global $callConfig;
 		$insert_array = array('complaint_name'=>$post['complaint_name'],'complaint_type'=>$post['complaint_type'], 'complaint_priority'=>$post['complaint_priority'],'building_id'=>$post['building_id'],'building_floor_id'=>$post['building_floor_id'],'building_floor_space_id'=>$post['building_floor_space_id'],'item_id'=>$post['item_id'],'asset_id'=>$post['asset_id'],'complaint_image'=>$complaint_image, 'remarks'=>$post['remarks'],'is_active'=>0,'current_status'=>0,'updated_by'=>$get['user_id']);
 		$res=$callConfig->updateRecord('complaints', $insert_array,'id',$get['complaint_id']);
		$ret['Update'] =$res;
		return $ret;
 		//complaint_name, complaint_type, complaint_description, building_id, building_floor_id, building_floor_space_id, item_id, asset_id, complaint_image, remarks
 	}
 	function toaddcomplaintshisorydetails($get, $post){
 		global $callConfig;
 		if($post['remarks']!=""){
	 		$insert_array = array('company_id'=>$get['company_id'],'complaint_id'=>$post['complaint_id'],'user_id'=>$get['user_id'],'transaction_date'=>$post['transaction_date'] ,'complaint_image'=>$complaint_image, 'remarks'=>$post['remarks'],'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
	 		$insert = $callConfig->insertRecord('complaint_responses',$insert_array);
 		}
 		if($post['change_status']=='Y'){
 			$res=$callConfig->updateRecord('complaints', array('current_status'=>$post['current_status'],'updated_by'=>$get['user_id']),'id',$post['complaint_id']);
 		}
		$ret['insert'] =$insert;
		return $ret;
		//complaint_id,company_id, user_id, transaction_date, complaint_image, remarks, current_status,change_status 
 	}
 	function togetsalaryyearmonthlist($get){
 		global $callConfig;
 		$query = "SELECT pay_year, pay_month FROM pay_run_summary WHERE company_id=".$get['company_id']." AND employee_id = ".$get['emp_id']." AND is_active = 0";
 		$datas = $callConfig->getAllRows($query);
 		$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
   		 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
 		$return['data'] = array();
 		foreach((array)$datas as $da){
 			$dat = (array)$da;
 			$dat['msg'] = "Salary of ".$months[$da->pay_month]." ".$da->pay_year;
 			$return['data'][]=$dat;
 		}
 		//print_r($return); exit;
 		return $return;
 	}
 	function togetsalarydetailsyearbasemonthlist($get){
 		global $callConfig;

 		$query ="SELECT id, emp_code, display_name, designation_id, (SELECT designation_name FROM designations WHERE id = designation_id) designation_name FROM employees WHERE company_id=".$get['company_id']." AND id =".$get['emp_id'];
 		$emp_data = $callConfig->getRow($query);

		$whr1st="company_id= '".$get['company_id']."' AND is_active = '0' and pay_month='".$get['month']."' and pay_year='".$get['year']."' and employee_id='".$get['emp_id']."' ";
		$query1st = $callConfig->selectQuery('pay_run_summary','*',$whr1st,'','','');
		$st1 = $callConfig->getRow($query1st);
		//$data['st1'] = $query1st;
        //echo print_r($st1); exit;
		$whremp="company_id= '".$get['company_id']."' and is_active=0 and id=".$get['emp_id'];
		$queryemp = $callConfig->selectQuery('employees','*',$whremp,'','','');
		$emp = $callConfig->getRow($queryemp);

		//$whr1stm="pay_run_summary_id='".$st1->id."' ";
		
		//$query1stm = $callConfig->selectQuery('pay_run_summary_components','*',$whr1stm,'','','');
		//$stmp = $callConfig->getAllRows($query1stm);
		
		$query1stm= "SELECT * FROM pay_run_summary_components prsc, earnings ern WHERE prsc.pay_run_summary_id='".$st1->id."' AND prsc.component_id = ern.id AND ern.display_in_payslip = 0 ORDER BY ern.display_seq ASC";

		$stmp = $callConfig->getAllRows($query1stm);
		//print_r($stmp); exit;

		$earquery = $callConfig->selectQuery('earnings','id,name,payslip_name,code,display_in_payslip, calculation_mode','','','','');
		$earning = $callConfig->getAllRows($earquery);
		foreach ($earning as $earn) {
			$earns[$earn->id] = $earn->payslip_name;
			$allearns[$earn->id] = (array)$earn;
		}
		//print_r($allearns); exit;
		$actual_amount =0; $dedaction_amount = 0;
//echo "<pre>";print_r($allearns); //exit;
		foreach ($stmp as  $value) {
			if($allearns[$value->component_id]['display_in_payslip']==0){

				if($allearns[$value->component_id]['calculation_mode'] =='ADD'){
					$actuals['name'] = $earns[$value->component_id];
					$actuals['amount'] = $value->salary_component_actual;
					$actuals['code'] = $allearns[$value->component_id]['code'];					
					$actual_amount = $actual_amount+$value->salary_component_actual;
					$actual[] = $actuals;
				}
				if($allearns[$value->component_id]['calculation_mode'] =='MINUS'){
					$dedactions['name'] = $earns[$value->component_id];
					$dedactions['amount'] = $value->salary_component_actual;
					$dedactions['code'] = $allearns[$value->component_id]['code'];
					
					$dedaction_amount = $dedaction_amount+$value->salary_component_actual;
					$dedaction[] = $dedactions;
				}
			}
			//print_r($value); //exit;
		}
		//echo "<pre>"; print_r($actual); print_r($dedaction);  exit;
		$data['emp_data'] = $emp_data;
		$data['earning'] = $actual;
		$data['dedaction'] = $dedaction;
		$data['earning_amount'] = number_format($actual_amount,0);
		$data['dedaction_amount'] = number_format($dedaction_amount,0);
		$data['net'] = number_format( $actual_amount-$dedaction_amount,0);
		
 		//print_r($data); exit;
 		return $data;
 	}
 	function mobileuserregistation($get, $post){
 		global $callConfig;
 		
 		$otp = $this->generateOTP(4);
 		$insert_array = array('user_name'=>$post['user_name'],'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'] ,'user_password'=>md5($post['user_password']), 'remember_token'=>$otp, 'is_active'=>0,'current_status'=>0);
 		//echo "---------"; print_r($insert_array); print_r($get); print_r($post); exit;
	 	$insert = $callConfig->insertRecord(MAINDB.'.mobile_user_registrations',$insert_array);
	 	//print_r($insert); exit;
	 	$insert['token'] = $otp;
	 	if($insert['status'] == 'sucess'){
	 		$insert['status']= 'success';
	 	}
	 	// user_name, user_email, user_mobile, user_password
	 	return $insert;
 	}
 	function mobileregistationcheckotp($get){
 		global $callConfig;
 		//print_r($get);
 		$query = "SELECT id FROM ".MAINDB.".mobile_user_registrations WHERE id=".$get['user_id']." AND remember_token='".$get['otp']."' AND is_active =0";
 		//echo $query; exit;
 		$data = $callConfig->getRow($query);
 		$return['status'] = 'fail';
 		if($data->id!=""){
 			$return['status'] = 'success';
 		}
 		return $return;
 	}
 	function toaddmobfielddailyplans($get, $post){
 		global $callConfig; 
 		$msg = "The selected time slot is already occupied by another scheduled task. Please choose a different time or reschedule the existing task to avoid conflicts";
 		$plan_date = str_replace('/"', '-', $post['plan_date']);
		$plan_date =  date('Y-m-d', strtotime($plan_date));
 		$check =1;
 		if(isset($post['force']) && $post['force']==1){
 			$check =0;
 		}else{
 			$query = "SELECT COUNT(id) cc FROM field_work_daily_plans WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id']." AND plan_date = '".$plan_date."' AND plan_time = '".$post['plan_time']."' AND is_active=0";
 			$ccdata = $callConfig->getRow($query);
 			if($ccdata->cc >0){
 				$check =1;
 			}else{
 				$check =0;	
 			}
 		}
 		if($check ==0){
	 		$insert_array = array('company_id'=>$get['company_id'],'contact_person'=> $post['contact_person'], 'mobile'=> $post['mobile'], 'user_id'=>$post['user_id'], 'plan_date'=>$plan_date, 'plan_time'=>$post['plan_time'], 'plan_longitude'=>$post['plan_longitude'], 'plan_latitude'=>$post['plan_latitude'], 'plan_distance'=>$post['plan_distance'], 'plan_location'=>$post['plan_location'], 'plan_address'=>$post['plan_address'], 'current_status'=>'0','is_active'=>'0','created_by'=>$get['user_id']);
			$insert = $callConfig->insertRecord('field_work_daily_plans',$insert_array);
			//`user_id`, `plan_date`, `plan_time`, `plan_longitude`, `plan_latitude`, `plan_distance`, `plan_location`, `plan_address`
			if($insert['status'] == 'sucess'){
		 		$insert['status']= 'success';
		 	}
		}else{
			$insert['status']= 'fail';
			$insert['message']= $msg;
		}
	 	return $insert;
 	}
 	function togetmobfielddailyplans($get){
 		global $callConfig; 
 		//echo "----"; exit;
 		$query = "SELECT id, user_id, contact_person,  mobile, plan_date, plan_time, plan_longitude, plan_latitude, plan_distance, plan_location, plan_address, plan_type, current_status    FROM field_work_daily_plans WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id']." AND plan_date='".$get['plan_date']."' AND is_active = 0";
 		$datas = $callConfig->getAllRows($query);

 		 $ddquery = "SELECT `company_id`, `map_title`, phone as mobile, address as plan_address, `map_date`, `map_time`, `map_longitude` as plan_longitude, `map_latitude` as  plan_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id']." AND map_date='".$get['plan_date']."' AND is_active = 0";
 		$wrdatas = $callConfig->getAllRows($ddquery);
 		
 		//$workdd = array();
 		foreach($wrdatas as $wrdat){
 			if($wrdat->field_work_daily_plan_id!=""){
 				$workdd[$wrdat->field_work_daily_plan_id] = (array)$wrdat;
 			}else{
 				$nonworkdd[] = (array)$wrdat;
 			}
 			
 		}
 		//print_r($workdd); exit;
 		foreach((array)$datas as $da){
 			$dat = (array)$da;
 			$dat['check_status'] = 'N';
 			if(isset($workdd[$da->id])){
	 			$dat['check_status'] = 'Y';
	 			$dat['client_name'] = $workdd[$da->id]['map_title'];
	 			$dat['work_distance'] = $workdd[$da->id]['map_distance'];
	 			$dat['remarks'] = $workdd[$da->id]['remarks'];
	 			$dat['status_radius'] = $workdd[$da->id]['status_radius'];
	 			$dat['field_work_daily_plan_id'] = $workdd[$da->id]['field_work_daily_plan_id'];
	 			$dat['current_status'] = $workdd[$da->id]['current_status'];
	 			//unset($workdd[$da->id]);
 			}
 			$return['data'][]=$dat;
 		}
 		if(count((array)$nonworkdd)>0){
 			foreach((array)$nonworkdd as $nonworkd){

 				$return['data'][]=$nonworkd;
 			}
 		}
 		$return['radius']=WORK_RAIDUS; 
 		$query = "SELECT sp.status, sp.name, st.background_color, st.color FROM status_progress sp, status st WHERE sp.company_id = st.company_id AND sp.company_id =1 AND sp.status = st.name_value AND sp.table_name = 'field_work_daily_plans' ORDER BY sp.display_priority ASC";
        $status = $callConfig->getAllRows($query);
        $return['status'] = $status;
 		
	 	return $return;	
 	}
 	function toaddmobfieldwork($get, $post){
 		global $callConfig; 
 		$plan_date = str_replace('/"', '-', $post['work_date']);
		$work_date =  date('Y-m-d', strtotime($work_date));
 		$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['user_id'],'client_name'=>$post['client_name'], 'work_date'=>$work_date, 'work_time'=>$post['work_time'], 'work_longitude'=>$post['work_longitude'], 'work_latitude'=>$post['work_latitude'], 'work_location'=>$post['work_location'], 'work_address'=>$post['work_address'],'comments'=>$post['comments'],'field_work_daily_plan_id'=>$post['field_work_daily_plan_id'], 'current_status'=>'0','is_active'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('field_work_details',$insert_array);
		// `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`
		if($insert['status'] == 'sucess'){
	 		$insert['status']= 'success';
	 	}
	 	return $insert;
 	}
 	function togetmobfieldworkdetails($get){
 		global $callConfig; 
 		$query = "SELECT `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`   FROM field_work_details WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id']." AND work_date=".$get['work_date']." AND is_active = 0";
 		$datas = $callConfig->getAllRows($query);
 		foreach((array)$datas as $da){
 			$dat = (array)$da;
 			$return['data'][]=$dat;
 		}
	 	return $return;	
 	}
 	function togetmobmanagerfieldworkdetails($get){
 		global $callConfig; 
 		$query = "SELECT id, display_name, user_email, user_mobile, user_photo FROM application_users WHERE company_id = 1 AND reports_to = ".$get['user_id']." AND is_active =0";
 		$user_datas = $callConfig->getAllRows($query);
 		if(count((array)$user_datas)>0){
 			foreach((array)$user_datas as $user_dat){
	 			$userda[$user_dat->id] = (array)$user_dat;
	 			$userda[$user_dat->id]['child'] = array();
	 			$user_ids[] = $user_dat->id;
	 		}
	 		$query = "SELECT `company_id`, `user_id`, `client_name`, `work_date`, `work_time`, `work_longitude`, `work_latitude`, `work_distance`, `work_location`, `work_address`, `comments`, `field_work_daily_plan_id`   FROM field_work_details WHERE company_id =".$get['company_id']." AND user_id IN (".implode(",",$user_ids).") AND work_date='".$get['work_date']."' AND is_active = 0 ORDER BY id DESC";
 			$datas = $callConfig->getAllRows($query);
	 		foreach((array)$datas as $da){
	 			$dat = (array)$da;
	 			$userda[$da->user_id]['child'][]=$dat;
	 		}
 		}
 		foreach($userda as $dat){
 			$return['data'][] = $dat;
 		}
	 	return $return;	
 	}

 	function toinactiveasedontableid($get){
 		global $callConfig;
 		//print_r($get); exit;
 		$upquery = "UPDATE ".$get['table']." SET is_active = 1 WHERE company_id=".$get['company_id']." AND id=".$get['ref_id'];
 		//echo $upquery;
 		$res = $callConfig->executeQuery($upquery);
 		//print_r($res);
 		//$res=$callConfig->updateRecord($get['table'],array('is_active'=>1),'id',$get['ref_id']);
 		return $res;
 	}

 	function toupdatedynamicfielddata($get,$post){
 		global $callConfig;
 		$table = $get['table'];
 		$value = $post['ref_id'];
 		unset($post['ref_id']);
 		if( isset($post['update_type']) && $post['update_type']!='' && $get['table'] = 'field_work_daily_plans' ){ 	
 			$query = " SELECT * FROM field_work_daily_plans WHERE id=".$value;
 			$feilddata =  $callConfig->getRow($query);
 			$user_id = $post['user_id'] !="" ? $post['user_id'] : $feilddata->user_id;
 			$plan_date = $post['plan_date'] !="" ? $post['plan_date'] : $feilddata->plan_date;
 			$plan_time = $post['plan_time'] !="" ? $post['plan_time'] : $feilddata->plan_time;
 			$insert_array = array('company_id'=>$get['company_id'], 'user_id'=>$user_id, 'contact_person'=>$feilddata->contact_person, 'mobile'=>$feilddata->mobile, 'plan_date'=>$plan_date, 'plan_time'=>$plan_time, 'plan_longitude'=>$feilddata->plan_longitude, 'plan_latitude'=>$feilddata->plan_latitude, 'plan_distance'=>$feilddata->plan_distance, 'plan_location'=>$feilddata->plan_location, 'plan_address'=>$feilddata->plan_address,  'is_active'=>'0', 'current_status'=>'0','created_date'=>date("Y-m-d H:i:s"), 'created_by'=>$get['user_id']);
 			$res=$callConfig->insertRecord('field_work_daily_plans',$insert_array);
 			$uparray = array('comments'=>$post['comments'], 'update_type'=>$post['update_type'], 'ref_id'=>$res['LASTID']);
	 		if($post['update_type']=='REASSIGN'){
	 			$query = "SELECT display_name FROM application_users WHERE id=".$user_id;
 				$userdata =  $callConfig->getRow($query);
	 			$uparray['re_value'] =$userdata->display_name;
	 			$uparray['current_status'] ='28';
	 		}
	 		if($post['update_type']=='RESCHEDULE'){
	 			$uparray['re_value'] = $plan_date." ".$plan_time;
	 			$uparray['current_status'] ='25';
	 		}
	 		$res=$callConfig->updateRecord($table,$uparray,'id',$value);
 		}else{
 			$res=$callConfig->updateRecord($table,$post,'id',$value);
 		} 		
 		return $res;
 	}
 	function tocheckuserfieldwortype($get){
 		global $callConfig;
 		//user_field_work_details
 		// 1: Daily Plan 2 : Daily Work 3: security
 		$get['plan_date'] = $get['plan_date']!=""? $get['plan_date'] :date('Y-m-d');
 		$data = array('todays_plan'=>'Y', 'schedule_work'=>'N','manager'=>'N','report'=>'Y');
 		$query = "SELECT id, user_field_work_type  FROM application_users WHERE company_id=".$get['company_id']." AND id=".$get['user_id'];
 		$user_data  = $callConfig->getRow($query);
 		//print_r($user_data); exit;
 		//$user_data->user_field_work_type=1;
 		$data['field_map_auto_check_in_out'] = FIELD_AUTO_CHECK;
 		$data['user_field_work_type'] = $user_data->user_field_work_type;
 		if($user_data->user_field_work_type==1){
 			$data['schedule_work'] = 'Y';
 			//$data['schedule_work'] = 'Y';
 		}
 		if($user_data->user_field_work_type==2){
 			$data['schedule_work'] = 'N';
 		}
 		if($user_data->user_field_work_type==3){
 			$data['schedule_work'] = 'N'; 
 			$data['report'] = 'N'; 
 			//$get['plan_date'] = date('Y-m-d', strtotime($get['plan_date']));			
 		    $query = " SELECT * FROM user_field_work_details WHERE company_id=".$get['company_id']." AND user_id=".$get['user_id']." AND work_from_date <='".$get['plan_date']."' AND work_to_date >='".$get['plan_date']."' AND is_active = 0";
 		    //echo $query;
 			$feilddata =  $callConfig->getRow($query);
 			$data['user_data'] = (array)$feilddata;
 		}
 		$return['data'] = $data;
 		return $return;
 	}	
 	function togetworkfielddataperdate($get){
 		global $callConfig;
 		$query = "SELECT count(id) count_dp FROM `field_work_daily_plans` WHERE company_id=".$get['company_id']." AND plan_date = '".$get['plan_date']."' AND is_active=0";
 		$dp_res = $callConfig->getRow($query);
 		$count_dp = $dp_res->count_dp;
 		$query = "SELECT count(id) count_dw, sum(IF(field_work_daily_plan_id!='',0,1)) count_dwp, sum(work_distance) work_distance  FROM `field_work_details` WHERE  company_id=".$get['company_id']." AND work_date = '".$get['plan_date']."' AND is_active=0";
 		$dw_res = $callConfig->getRow($query);
 		$return['date']=$get['plan_date'];
 		$return['attend']=$dw_res->count_dw;
 		$return['pending']=$count_dp>0 ? $count_dp-$dw_res->count_dwp:0; 
 		$return['distance']=$dw_res->work_distance;
 		return $return;
 	}
 	function togetworkfielddatapermonth($get){
 		global $callConfig;
 		$formdate = date('Y-m-01',strtotime($get['plan_date']));
 		$todate = date('Y-m-t',strtotime($get['plan_date']));
 		$query = "SELECT plan_date,count(id) count_dp FROM field_work_daily_plans WHERE company_id=".$get['company_id']." AND user_id =".$get['user_id']." AND plan_date >= '".$formdate."' AND plan_date <= '".$todate."' AND current_status =0 AND is_active=0 GROUP BY plan_date";
 		$dp_res = $callConfig->getAllRows($query);
 		foreach((array)$dp_res as $dp_re ){
 			$fwda[$dp_re->plan_date] = $dp_re->count_dp;
 		}
 		
 		 //$query = "SELECT work_date, count(id) count_dw, sum(IF(field_work_daily_plan_id!='',0,1)) count_dwp, sum(work_distance) work_distance  FROM field_work_details WHERE  company_id=".$get['company_id']." AND work_date >= '".$formdate."' AND work_date <= '".$todate."'  AND is_active=0 GROUP BY work_date";
 		$query = "SELECT map_date as work_date, count(id) count_dw, sum(IF(field_work_daily_plan_id!='',0,1)) count_dwp, sum(map_distance) work_distance, status_radius,  min(if(map_title='Check-In',`map_time`, Null)) as clock_in, MAX(if(map_title='Check-Out',`map_time`, Null)) as clock_out FROM field_work_user_map_points WHERE company_id=".$get['company_id']." AND user_id =".$get['user_id']." AND map_date >= '".$formdate."' AND map_date <= '".$todate."' AND is_active=0 GROUP BY map_date";
 		//echo "--".$query;
 		$dw_ress = $callConfig->getAllRows($query);
 		foreach($dw_ress as $dw_res){
 			$return = array();
 			$count_dp = isset($fwda[$dw_res->work_date]) ? $fwda[$dw_res->work_date] :0;
 			$return['date']=$dw_res->work_date;
	 		$return['attend']=$dw_res->count_dw;
	 		$return['pending']= ( $count_dp-$dw_res->count_dwp)>0 ? $count_dp-$dw_res->count_dwp:0;
	 		$return['distance']=$dw_res->work_distance;
	 		$return['status_radius']=$dw_res->status_radius;
	 		$return['clock_in']=$dw_res->clock_in;
	 		$return['clock_out']=$dw_res->clock_out;
	 		$data['data'][]=$return;
 		}
 		return $data;
 	}
 	function toaddmobilehelpdetails($get, $post){
    	global $callConfig;
    	$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$get['user_id'],'name'=>$post['name'], 'email'=>$post['email'], 'phone'=>$post['phone'], 'message'=>$post['message'],'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
    	// post : name, email, phone, message : get : company_id , user_id
		$res=$callConfig->insertRecord('mobile_help',$insert_array);
		return $res;
    }
    function tomapouttimeallcase($get){
 		global $callConfig;
 		//print_r($get); exit;
 		$upquery = "UPDATE field_work_user_map_points SET map_out_time = '".$get['map_out_time']."' WHERE company_id=".$get['company_id']." AND id=".$get['ref_id'];
 		//echo $upquery;
 		$res = $callConfig->executeQuery($upquery);
 		//print_r($res);
 		//$res=$callConfig->updateRecord($get['table'],array('is_active'=>1),'id',$get['ref_id']);
 		return $res;
 	}
 	function togetsecurtydetails($get){
        global $callConfig;
        $return = array();
        $userid = $get['user_id'];
        $map_date = $get['map_date']=!"" ? $get['map_date'] : date('Y-m-d');
        $sev_map_date = date("Y-m-d", strtotime('-7 day', strtotime($map_date)));
        $query = "SELECT user_id, map_date, min(`map_time`) clock_in, MAX(`map_time`) clock_out,  map_latitude, map_longitude  FROM `field_work_user_map_points` WHERE company_id =  ".$get['company_id']."  AND `user_id` = ".$userid." AND `map_date`>= '".$sev_map_date."' AND `map_date`<= '".$map_date."' AND is_active =0 GROUP BY map_date";
        $data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;
    }
    function togetuserfieldworkdata($get){
        global $callConfig;
        $return = array();
        $userid = $get['user_id'];
         $map_date = $get['map_date'];
        $query = "SELECT user_id, map_date, min(if(map_title='Check-In',`map_time`, Null)) as clock_in, MAX(if(map_title='Check-Out',`map_time`, Null)) as clock_out, map_latitude, map_longitude FROM `field_work_user_map_points` WHERE company_id =  ".$get['company_id']."  AND `user_id` = ".$userid." AND  `map_date`= '".$map_date."' AND is_active =0 ";
        $data = $callConfig->getRow($query);
        $return['data'] = array();
        if($data->user_id!=""){
        	$return['data'] = $data;
        }
        
        return $return;
    }
    function togetlistfiledworkdatabasedate($get){
    	global $callConfig;
    	//echo "----"; exit;
        $return = array();
        $userid = $get['user_id'];
        $map_date = $get['map_date'];
        $query = "SELECT au.display_name as user_name, fp.* FROM field_work_daily_plans fp, application_users au WHERE fp.company_id = au.company_id AND fp.company_id = ".$get['company_id']." AND fp.user_id = au.id AND fp.user_id =".$userid."  AND fp.plan_date = '".$map_date."' AND fp.is_active =0";

        //$query = "SELECT * FROM `field_work_daily_plans` WHERE company_id = ".$get['company_id']." AND `user_id` = ".$userid." AND `plan_date` = '".$map_date."' AND is_active =0";
        $datas = $callConfig->getAllRows($query);
        //print_r($datas); exit;
        $ddquery = "SELECT `company_id`, `map_title`, `map_date`, `map_time`, `map_longitude` as plan_longitude, `map_latitude` as  plan_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get['company_id']." AND user_id =".$userid." AND map_date='".$map_date."' AND is_active = 0";
 		$wrdatas = $callConfig->getAllRows($ddquery);
 		//print_r($wrdatas); exit;
 		//$workdd = array();
 		foreach((array)$wrdatas as $wrdat){
 			if($wrdat->field_work_daily_plan_id!=""){
 				$workdd[$wrdat->field_work_daily_plan_id] = (array)$wrdat;
 			}else{
 				$nonworkdd[] = (array)$wrdat;
 			}
 			
 		}
 		//print_r($workdd); exit;
 		foreach((array)$datas as $da){

 			$dat = (array)$da;
 			if(isset($workdd[$da->id])){	
 				
	 			$dat['current_status'] = $workdd[$dat['id']]['current_status'];
	 			//print_r($dat); exit; 
	 			//unset($workdd[$da->id]);
 			}
 			$return['data'][]=$dat;
 		}
        $query = "SELECT sp.status, sp.name, st.background_color, st.color FROM status_progress sp, status st WHERE sp.company_id = st.company_id AND sp.company_id =1 AND sp.status = st.name_value AND sp.table_name = 'field_work_daily_plans' ORDER BY sp.display_priority ASC";
        $status = $callConfig->getAllRows($query);
        $return['status'] = $status;
        return $return;
    }
    function togetholidayslistbaseyear($get){
    	global $callConfig;
    	$year = $get['year']!="" ? $get['year'] : date('Y');

    	$query = "SELECT holiday_year, holiday_date, holiday_name FROM holidays WHERE company_id =".$get['company_id']." AND holiday_year = '".$year."' AND is_active =0";
    	$data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;
    }
    function togetweekdayslist($get){
    	global $callConfig;
    	$attquery = "SELECT id, working_days FROM attendance_settings WHERE company_id = '".$get['company_id']."' AND is_active =0";
    	//echo $attquery;
	    $att_data = $callConfig->getRow($attquery);
	    $return['data'] = json_decode($att_data->working_days,1);
	    return $return;
    }
    function togetfieldworkdailyplansfrmobile($get){
    	global $callConfig;
    	$query = "SELECT 
				  au.id, 
				  au.display_name, 
				  au.user_email, 
				  au.user_field_work_type, 
				  '".$get['date']."' AS map_date, 
				  count(
				    DISTINCT(fwdp.id)
				  ) AS total_plan,
				  count(
				    DISTINCT(fwump.id)
				  ) AS total_work,
				  SUM(
				    IF(
				      fwump.field_work_daily_plan_id = fwdp.id, 
				      1, 0
				    )
				  ) AS visited, 
				  count(
				    DISTINCT(fwump.id)
				  ) - SUM(
				    IF(
				      fwump.field_work_daily_plan_id = fwdp.id, 
				      1, 0
				    )
				  ) AS extra, 
				  count(
				    DISTINCT(fwdp.id)
				  ) - SUM(
				    IF(
				      fwump.field_work_daily_plan_id = fwdp.id, 
				      1, 0
				    )
				  ) AS pending 
				FROM 
				field_work_daily_plans fwdp
				  
				  
				  LEFT JOIN  field_work_user_map_points fwump ON fwdp.user_id = fwump.user_id 
				  AND fwdp.plan_date = fwump.map_date 
				  AND fwdp.company_id = fwump.company_id 
				  AND fwdp.is_active = '0' 
				  LEFT JOIN application_users au ON ( fwdp.user_id = au.id OR  fwump.user_id = au.id)
				WHERE 
				  fwdp.company_id = '" . $get['company_id'] . "'
				  AND fwdp.user_id = '".$get['user_id']."' 
				  AND fwdp.plan_date = '".$get['date']."'
				  AND fwdp.is_active = '0' 
				GROUP BY 
				  fwump.user_id, 
				  fwump.map_date 
				ORDER BY 
				  fwump.map_date";
    		/*$query = "SELECT au.id, 
    				au.display_name, 
    				au.user_email,  
    				au.user_field_work_type, 
    				fwump.map_date AS map_date,
				  count(DISTINCT(fwump.id)) AS vall,
				  SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0))  AS visited,
				  count(DISTINCT(fwump.id)) - SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0)) AS extra,
				  count(DISTINCT(fwdp.id)) - SUM(IF( fwump.field_work_daily_plan_id = fwdp.id,1,0)) AS pending
                FROM 
                    field_work_user_map_points fwump
                LEFT JOIN application_users au ON fwump.user_id = au.id
                LEFT JOIN 
                    field_work_daily_plans fwdp 
                    ON fwdp.user_id = fwump.user_id 
                    AND fwdp.plan_date = fwump.map_date 
                    AND fwdp.company_id = fwump.company_id 
                    AND fwdp.is_active = '0'
                WHERE 
                    fwump.user_id = '".$get['user_id']."'
                    AND fwump.map_date = '".$get['date']."'
                    AND fwump.company_id = '" . $get['company_id'] . "'
                    AND fwump.is_active = '0'
                GROUP BY 
                    fwump.user_id, fwump.map_date
                ORDER BY 
                    fwump.map_date";*/
       //echo $query;
        $data = $callConfig->getAllRows($query);
        $return['data'] = Null;
        if(count((array)$data)>0){
        	$return['data'] = $data;
        }
        
        return $return;
    }
    function togetgeolocationresaonlist($get){
    	//echo "--------------"; exit;
    	global $callConfig;
    	$query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get['company_id']."' and genc_code='GEOLOCATIONREASON' order by id ASC"; 
		$display_details = $callConfig->getAllRows($query);
		$data['data'] = $display_details;
		return $data;
    }
    function togetuserslistformobile($get){
    	global $callConfig;
    	$query = "SELECT id, display_name FROM application_users WHERE company_id=".$get['company_id']." AND is_active=0";
    	$data = $callConfig->getAllRows($query);
    	$return['data'] = $data; 
    	return $return;
    }
    function togetuserssecurtydaydetailswithhistory($get){
    	global $callConfig;
    	
    	$userid = $get['user_id'];
        $map_date = $get['map_date'];
    	$query = "SELECT  `map_title`, `map_date` as work_date , `map_time` as clock_in, Null as clock_out ,`map_longitude` as check_in_longitude, `map_latitude` as  check_in_latitude, `map_distance`,  `comments`, `field_work_daily_plan_id` , status_radius,  remarks, current_status  FROM field_work_user_map_points WHERE company_id =".$get['company_id']." AND user_id =".$userid." AND map_date='".$map_date."' AND is_active = 0 ORDER BY map_time ASC";
    	//echo $query; exit;
    	$datas = $callConfig->getAllRows($query);
    	$i=0; $check=0;
    	foreach((array)$datas as $dat){
    		if( $dat->map_title== 'Check-In'){
    			$da = (array)$dat;
    			unset($da['map_title']);
    			$da['check_out_longitude']=Null;
    			$da['check_out_latitude']=Null;
    			$data[$i]=$da;
    		}else{
    			$data[$i]['clock_out']=$dat->clock_in;
    			$data[$i]['check_out_longitude']=$dat->check_in_longitude;
    			$data[$i]['check_out_latitude']=$dat->check_in_latitude;
    			$i++;
    		}
    	}
    	if(count((array)$data)>0){
    		$res=$data[$i];
    		$res['details'] = $data;
    	}
    	$return['data'] = $res;
		return $return;
    	//echo "<pre>"; print_r($data); 
    }
    function dynamicinsertformobile($get, $post){
    	global $callConfig;
    	$post['company_id'] = $get['company_id'];
    	$post['is_active'] = '0';
    	$post['current_status'] = '0';
    	$post['created_by'] = $get['user_id'];
    	$post['created_date'] = date("Y-m-d H:i:s");
    	//print_r($post); exit;
		$res=$callConfig->insertRecord($get['table'],$post);
		return $res;
    }
    function togetpostvaldynmicdetials($get, $post){
    	global $callConfig;
    	$where = " company_id =".$get['company_id'];
    	if(count((array)$post)>0){
    		
			foreach ($post as $key => $value) {
				$where.= " AND ".$key." = '".$value."'";		
			}
			$where = trim($where,'AND ');
		}
		$where.= " AND is_active =0";
		$query = $callConfig->selectQuery($get['table'],'*',$where,'','','');
		//echo $query;
		$res = $callConfig->getAllRows($query);
		return $res;
    }
    function togetrelatedworkusermapointmob($get){
    	global $callConfig;
    	$query="SELECT map_title, phone, address, map_latitude, map_longitude, map_date FROM `field_work_user_map_points` WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active =0 GROUP BY phone";
    	$res = $callConfig->getAllRows($query);
    	return $res;
    }
    function dyanamicgenccodedetailformobile($get){
    	global $callConfig;
    	$res = array();
    	if($get['genc_code']!=""){
    		$query="SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id =".$get['company_id']." AND genc_code = '".$get['genc_code']."' AND is_active =0";
    		$res = $callConfig->getAllRows($query);
    	}    	
    	return array('data' => $res);
    }

    function togetproductformitemonmobile($get){
    	global $callConfig;
    	$query="SELECT id, item_name, item_description, sale_price, item_image FROM `items` WHERE company_id =".$get['company_id']." AND is_active =0";
    	$res = $callConfig->getAllRows($query);
    	return array('data' => $res);
    }
    function togetproductcustomeronmobile($get){
    	global $callConfig;
    	$query="SELECT id, display_name, phone, email, billing_address, map_latitude, map_longitude  FROM customers WHERE company_id =".$get['company_id']." AND is_active =0";
    	$res = $callConfig->getAllRows($query);
    	return array('data' => $res);
    }
    function toinsertpurchaseorderonmobile($get,$post){
    	global $callConfig;
    	//print_r($get); print_r($post); exit;
    	$insert_array = array('company_id'=>$get['company_id'], 'data'=> json_encode($post), 'get_data'=>json_encode($get),'created_by'=>$get['user_id']);
    	$res=$callConfig->insertRecord('field_mobile_orders',$insert_array);
    	$res=$this->toinsertpurchaseordermobile($get,$post);
		return $res;
    }
    function toinsertpurchaseordermobile($get,$post){
		global $callConfig;
		/*//echo "----------------------"; exit;
		$dataw = $callConfig->getRow("SELECT * FROM purchase_order_dump WHERE id=23");
		//echo "<pre>"; print_r($dataw); 
		$get = json_decode($dataw->data,1);
		$post = json_decode($dataw->get_data,1);*/

		//print_r($post);  print_r($get); exit;
		foreach($post['data'] as $item){
			$item_ids[] = $item['item_id']; 
		}
		//print_r($item_ids);print_r($post);  print_r($get); exit;
		$company_id = $get['company_id'];
		$customer_id = $post['customer_id'];
	 	$popisosichildtablerequirdary = array('item_id','item_name','purchase_uom');
		$application_table = 'sales_orders';
	 	$application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get['user_id']."' AND is_active = 0";
		$application_usersres =  $callConfig->getRow($application_userssql);

		if($customer_id==''){
			if($post['customer_ref_id']!=""){
				$quer = "SELECT * FROM field_work_daily_plans WHERE id=".$post['customer_ref_id'];
				$cuser = $callConfig->getRow($quer);
				$insert_array = array('company_id'=>$get['company_id'], 'title'=>$cuser->contact_person, 'display_name'=>$cuser->contact_person, 'phone'=>$cuser->mobile, 'billing_address'=>$cuser->plan_address, 'customer_longitude'=>$cuser->plan_longitude, 'customer_latitude'=>$cuser->plan_latitude, 'current_status'=>'0', 'is_active'=>'0', 'created_by'=>$get['user_id']);
				$cus_res=$callConfig->insertRecord('customers',$insert_array);
				$customer_id = $cus_res['LASTID'];
			}

		}
		$customer_userssql = "SELECT id, display_name, email, customer_tax_reg_no, phone, billing_address, shipping_address  FROM customers WHERE company_id = '".$company_id."' AND id = '".$customer_id."' AND is_active = 0";
		$customer =  $callConfig->getRow($customer_userssql);


		//echo "<pre>".$customer_userssql; print_r($customer);
		$dmtsql = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND item_table !='' AND tax_table !='' AND is_active = 0";
		$dmtres=$callConfig->getRow($dmtsql);
		//echo "<pre>"; print_r($dmtres);
		$itemssql = "SELECT itm.id, itm.sku, itm.item_name, itm.item_description, itm.purchase_uom, itm.sale_uom, itm.sale_price, itm.purchase_price ,itm.hsn_and_sac_code_id, itm.inventory_asset_account, hsnsac.gst, hsnsac.code, hsnsac.tax_authority_id FROM items itm LEFT JOIN hsn_and_sac_codes hsnsac ON itm.company_id = hsnsac.company_id AND itm.hsn_and_sac_code_id = hsnsac.id AND itm.is_active = hsnsac.is_active WHERE itm.company_id='".$company_id."' AND itm.id IN (".implode(",",$item_ids).") AND itm.is_active = '0'"; 
      	$itemsres = $callConfig->getAllRows($itemssql);
		foreach($itemsres as $itmre){  
			$items[$itmre->id] =  $itmre; //marge two table data
 		}
	 	$transaction_location_id = "";
	 	$transaction_warehouse_id = "";
	 	$originator_currency_id = $application_usersres->default_currency_id;
	 	$originator_state_id = $application_usersres->default_state_id;
	 	$responsary = array();
	 	//echo "<pre>"; print_r($post);  print_r($get); echo $itemssql; print_r($items); exit;
		$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND is_active = 0";
		$displayattributesres =  $callConfig->getRow($displayattributessql);
		//echo "<pre>".$displayattributessql; print_r($displayattributesres); exit;
		$fieldkeywithvalue = array();
		/*$checklists = array('InvDate'=>'transaction_date', 'VOUCHERNUMBER'=>'transaction_number', 'PARTYNAME'=>'party_name', 'STATE'=>'party_address', 'PLACEOFSUPPLY'=>'place_of_supply', 'mobile_number'=>'party_phone', 'email_address'=>'party_email');
		foreach($checklists as $checklis){
			$checkfiled[] = $checklis;
		}*/
		//echo "<pre>".$displayattributessql; print_r($displayattributesres); exit;
		if($displayattributesres->list_attributes !=""){
			$lists = json_decode($displayattributesres->list_attributes,1);
			$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
			foreach ($lists as $key => $value){				
				if(in_array($value['type'], (array)$type_array)){
					$listsechck[] = $key;
					$tablefield = $this->getDyanamicSelectData($application_table, $key); 
					if($tablefield['type'] != 'function'){
						$reverkeyvaluearay = array();
						foreach($tablefield['res'] as $kkey=>$valuu){
							$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
							$reverkeyvaluearay[$removewhinewline] = $kkey;
						}
						$fieldkeywithvalue[$key] = $reverkeyvaluearay;
					}else{
						$reverkeyvaluearay = array();
						foreach($tablefield as $kkey=>$valuu){
							$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
							$reverkeyvaluearay[$removewhinewline] = $kkey;
						}
						$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
					}
		 		}
			 	
			}
		}	
		$transactionssql = "SELECT id, transaction_date, transaction_number, party_name, party_address, place_of_supply, party_phone, party_email FROM ".$application_table." WHERE company_id='".$company_id."' AND is_active = '0'";
      	$transactionres = $callConfig->getAllRows($transactionssql);
		foreach($transactionres as $recpt){  
			$transaction[$recpt->transaction_number] =  $recpt; //marge two table data
 		}
 		//echo "<pre>".$displayattributessql; print_r($displayattributesres); exit;
		
 		$valuenotavailablefieldary = array();
 		$stringary = array();
 		$maintransactionary = $transactionitemtaxary = $transactionitemsql = array();
 		$currency_id = "";
 		$taxarray = array();
		$tax_amount= 0;
		$maintotal_amount = $mainamount = $tax_amount = $tax_percentage   =$maintax_amount_total = 0;
		foreach($post['data'] as $itmss){
			$pur = $items[$itmss['item_id']];
			$tax_percentage = $pur->gst;
			if($tax_percentage !=""){
				$tax_amount = $pur->sale_price*($tax_percentage/100);
				$maintax_amount_total +=$tax_amount;  
				$maintotal_amount += $pur->sale_price+$tax_amount;
				$mainamount +=$pur->sale_price;  
			}else{
				$maintotal_amount += $pur->sale_price;
				$mainamount +=$pur->sale_price; 
			}
		}	
		if($supplier_state_id == $originator_state_id){
			$tax_state = "S";
		}else{
			$tax_state = "I";
		}
		$transaction_number=$this->toGetApplicationSerialNumber($application_table,$post);
		$maintransactionary = array('company_id'=>$company_id,'transaction_number'=>$transaction_number,'transaction_date'=>date('Y-m-d'),'cost_center_id'=>$application_usersres->cost_center,'party_id'=>$customer_id,'party_name'=>$customer->display_name,'party_email'=>$customer->email,'party_tax_number'=>$customer->customer_tax_reg_no,'party_phone'=>$customer->phone,'party_address'=>$customer->billing_address, 'party_ship_adderss	'=>$customer->shipping_address, 'amount'=>$mainamount,'tax_amount'=>$maintax_amount_total,'total_amount'=>$maintotal_amount, 'order_required_date'=>$post['order_required_date'],'pending_amount'=>$maintotal_amount,'transaction_location_id'=>$application_usersres->location_id, 'transaction_warehouse_id'=>$application_usersres->default_warehouse_id, 'originator_currency_id'=>$originator_currency_id, 'currency_id'=>$currency_id, 'currency_rate'=>'1' ,'originator'=>$get['user_id'], 'originator_state_id'=>$originator_state_id,'payment_terms'=>'','tax_state'=>$tax_state,'is_active'=>'0','current_status'=>'199','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
		//echo "<pre>"; print_r($post);  print_r($get);  print_r($maintransactionary); exit;	
		$maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name,$maintransactionary);
		$transaction_id = $maintransactionaryres['LASTID'];
		//$transaction_id =1;
		//echo "<pre>"; print_r($maintransactionaryres);  print_r($dmtres); print_r($post);  print_r($get); exit;
		foreach($post['data'] as $itementry){
			$tax_amount = $tax_percentage  = $amount =$tax_total_amount = 0;	
			$pur = $items[$itementry['item_id']];
			$item_total_amount = $pur->sale_price*$itementry['qty'];
			$tax_id = $tax_name = "";
			if(isset($items[$itementry['item_id']])){
				$item_id = $pur->id;
				$account_code = $pur->inventory_asset_account;
				$item_name = $pur->item_name;
				$description = $pur->item_description;
				$purchase_uom = $pur->purchase_uom;
				$tax_percentage = $pur->gst;
				//echo "<pre>"; print_r($maintransactionary); exit;
				if($tax_percentage !=""){

					$tax_amount = $item_total_amount*($tax_percentage/100);
					$total_amount = $total_amount+$tax_amount;
					$tax_id = $pur->hsn_and_sac_code_id;
					$tax_percentage =$tax_percentage;
					$tax_name = $pur->code;
					$amount =$total_amount;  
					$tax_total_amount =$total_amount; 
					$details =$pur;

					$transactionitemtaxary[] = array('company_id'=>$company_id, 'transaction_id'=>$transaction_id, 'transaction_item_id'=>$item_id, 'tax_name'=>$tax_name, 'tax_value'=>$tax_percentage, 'tax_id'=>$tax_id, 'amount'=>$item_total_amount, 'tax_amount'=>$tax_amount, 'total_amount'=>$total_amount, 'tax_type'=>$tax_state, 'tax_split_details'=>'', 'is_active'=>0, 'current_status'=>0, 'created_date'=>date("Y-m-d H:i:s"), 'created_by'=>0);

				}
			}
			$transactionitemsql[] = array('company_id'=>$company_id, 'transaction_id'=>$transaction_id, 'item_id'=>$item_id, 'account_code'=>$account_code, 'item_name'=>$item_name, 'description'=>$description, 'purchase_uom'=>$purchase_uom, 'item_quantity'=>$itementry['qty'], 'item_price'=>$pur->sale_price, 'item_amount'=>$item_total_amount, 'tax_amount'=>$tax_amount, 'discount_amount'=>'0', 'total_amount'=>$total_amount, 'reason_id'=>'', 'item_st_cost'=>'0','item_fifo_cost'=>'0', 'item_lifo_cost'=>'0', 'item_wa_cost'=>'0', 'approved_quantity'=>$itementry['qty'], 'transaction_type'=>'', 'transaction_qty'=>$itementry['qty'], 'pending_qty'=>$itementry['qty'], 'item_details'=>'', 'tax_deducted_source_id'=>'', 'tds_amount'=>'0', 'is_active'=>'0', 'created_date'=>date("Y-m-d H:i:s"), 'created_by'=>'1'); 
		}
		//echo "<pre>"; print_r($transactionitemsql);  print_r($transactionitemtaxary); exit;
		foreach($transactionitemsql as $transitemarry){
			$transitemarry['transaction_id'] = $transaction_id;
        	$transactionitemres[] = $callConfig->insertRecord($dmtres->item_table,$transitemarry);
    	}
        foreach($transactionitemtaxary as $transitemtaxarry){
        	$transitemtaxarry['transaction_id'] = $transaction_id;
    		$transactionitemtaxres[] = $callConfig->insertRecord($dmtres->tax_table,$transitemtaxarry);
    	}
	    $get['transaction_table'] = $dmtres->application_table_name;
	    $apihitre = $callConfig->getRow("SELECT * FROM ".$dmtres->application_table_name." WHERE id=".$transaction_id);
	    
		$upp = $this->updateTransactionItemsQty($get,$apihitre,"-");
		$stringary['payments_result'] = $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name,$transaction_id);   
		//echo "<pre>"; print_r($maintransactionaryres);  print_r($dmtres); print_r($post);  print_r($get); exit;
			
		/*$stringary['transaction_item_res'] = $transactionitemres;
		$stringary['transaction_item'] = $transactionitemsql;
		$stringary['transaction_item_tax_ary'] = $transactionitemtaxary;
		$stringary['transaction_item_tax'] = $transactionitemtaxres;
		$stringary['status'] = 200; 
		$stringary['query'] = $paymentarray;
		$stringary['transaction'] = $maintransactionary;
		$stringary['transaction_res'] = $maintransactionaryres;
		$stringary['error'] = 'success';
		$stringary['error'] = 'success';
		$apihitre['response'] =$stringary; 
		$apihitre['valuenotavailablefieldary'] =$valuenotavailablefieldary; 
		$responsary[] = $apihitre;*/
 		return $maintransactionaryres; 
	}
	function getMobileReceipts($get){
		global $callConfig;
		$return = array();
		$resciptlist = $this->getReceipts($get);
		$return = $resciptlist['payments'];
		//echo "<pre>"; print_r($resciptlist);
		return $return;
	}
	function getMobileTransactionReceipts($get){
		global $callConfig;
		$return = $transactionary = array();
		$get['party_id'] = urlencode(json_encode($get['party_id']));
		$resultary = $this->toGetblancebill($get);
		$duemount_array =  $resultary['payment_term_transactions_due']; 
		foreach($resultary['transactions'] as $transtable=>$listdata){
			foreach($listdata as $transdata){
				$transdata->payment_term_due_amount = $duemount_array[$transtable][$transdata->id]!=""?$duemount_array[$transtable][$transdata->id]:$transdata->pending_amount;
				$transactionary[$transtable][] = $transdata;
			}
		}
		return $transactionary;	
	}
	function insertMobileReceipts($get,$post){
		global $callConfig;
		
		$return = array();
		$post['customer_id'] = array($post['customer_id']);
		$newary = $post;
		$paymentpostdata22 = $post['payment'];
		foreach($paymentpostdata22 as $res){
			$newary['transaction_table'][$res['transaction_id']] = $res['transaction_table'];
			$newary['transaction_id'][$res['transaction_id']] = $res['transaction_id'];
			$newary['invoice_number'][$res['transaction_id']] = $res['invoice_number'];
			$newary['invoice_total'][$res['transaction_id']] = $res['invoice_total'];
			$newary['pending_amount'][$res['transaction_id']] = $res['pending_amount'];
			$newary['paid_amonut'][$res['transaction_id']] = $res['paid_amount'];
			$newary['tds_paid_amonut'][$res['transaction_id']] = $res['tds_paid_amount'];
			$newary['total_paid_amonut'][$res['transaction_id']] = $res['total_paid_amount'];
			$newary['payment_allocation_id'][$res['transaction_id']] = $res['payment_allocation_id'];
			$newary['transaction_party_id'][$res['transaction_id']] = $res['transaction_party_id'];
			$newary['check'][$res['transaction_id']] = $res['check'];

		}
		$resut = $this->insertReceipts($newary);
		return $newary;
	}
	function getMoblieUserRelatedDetails($get){
		global $callConfig;
		$return = array();
		$userid = $get['user_id'];
		$userdatatype = $get['userdatatype'];
		if($userdatatype == "bank_account_users"){
			$banksql = "SELECT ba.id, ba.account_code, ba.account_type, ba.bank_account_name FROM bank_account_users bau, bank_accounts ba WHERE bau.company_id = '".$get['company_id']."' AND bau.company_id = ba.company_id AND bau.bank_account_id = ba.id AND bau.user_id = '".$userid."' AND bau.is_active =ba.is_active AND ba.is_active = 0";
			$resdata = $callConfig->getAllRows($banksql);
		}
		$return['result'] = $resdata;
		return $return;
	}
	function tokengenrateformobile($get){
		global $callConfig;
		//print_r($get); //exit;
		$token = hash('sha256', uniqid(random_bytes(8), true) . time());
		$insert_array = array('company_id'=>$get['company_id'], 'user_id'=>$get['user_id'], 'type'=>'mobile_view', 'token'=>$token, 'current_status'=>'0', 'is_active'=>'0', 'created_by'=>$get['user_id']);
		$res=$callConfig->insertRecord('web_token',$insert_array);
		$web_page = $get['web_page'] !="" ? $get['web_page'] :'orders';
		//$web_page = 'orders';
		$web_url = SITEURL."webview/index.php?token=".$token."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&type=".$get['type']."&webpage=".$web_page."&emp_id=".$get['emp_id']."&module_id=".$get['module_id']."&data_name=".$get['data_name']."&table_name=".$get['table_name'];
		if($get['rid'] !=""){
			$web_url .= "&Rid=".base64_encode(base64_encode("id:".$get['rid']));
		}
		$return['status'] = "success";
		$return['web_url'] = $web_url;
		return $return;
	} 
	function toretrivewebviewlogin($get){
		global $callConfig;
		$query = "SELECT * FROM application_users WHERE id =".$get['user_id']." AND is_active=0";
		$res = $callConfig->getRow($query);
		return $res;
	}
	function getMobileExpenseDetails($get){
		global $callConfig;
		$return = array();
		$res = $this->getEmployeeExpencesDetails($get);
		//echo "<pre>"; print_r($res);
		if($get['gettype']== 'expense_categories'){
			$return =$res['expense_categories']; 
		}elseif($get['gettype']== 'payment_modes'){
			$return =$res['general_code_details']; 
		}elseif($get['gettype']== 'expense_list'){
			$return =$res['expense_list']; 
		}elseif($get['gettype']== 'expense_details'){
			$return =$res['main_details']; 
		}elseif($get['gettype']== 'expense_mileages_perdiem'){
			$return =$res['expenses_mileages_perdiem']; 
		}
		return $return;
	}
	function getMobileOrders($get){
        global $callConfig;
        $table_name = "sales_orders";
        $sub_table = "so_items";
        $return = array();
        $originator = $get['id'];
        $query = "SELECT so.id, so.party_name, so.transaction_date, so.transaction_number, COALESCE(soi.items, 0) AS items  FROM  ". $table_name ." so
        LEFT JOIN (SELECT transaction_id, count(transaction_id) as items  FROM ".$sub_table." GROUP BY 
        transaction_id) AS soi ON soi.transaction_id = so.id
        where so.originator = '".$originator."' AND  so.company_id = '" . $get['company_id'] . "' AND so.is_active=0";
        // echo $query;
        $query_res = $callConfig->getAllRows($query);
        $mobile_orders = array();
        foreach ($query_res as $row) {
            $mobile_orders[] = $row;
        }
        $return['mobile_orders'] = $mobile_orders;
        return $return;
    }
    function getMobileOrderInfo($get){
        global $callConfig;
        $table_name = "sales_orders";
        $sub_table = "so_items";
        $return = array();
        $return['get'] = $get;
        $transaction_id = $get['id'];
        $query = "SELECT * FROM  ".$table_name." so 
        where so.id=".$transaction_id."  AND  so.company_id = ".$get['company_id']." AND so.is_active=0";

        $return['query'] = $query;
        $query_res = $callConfig->getRow($query);
        $return['order_res'] = $query_res;

        $query1 = "SELECT * FROM  ".$sub_table."
        where transaction_id = ".$transaction_id." AND company_id = " . $get['company_id'] . " AND is_active=0";
        $return['query1'] = $query1;
        $query_res = $callConfig->getAllRows($query1);
        $mobile_orders = array();
        foreach ($query_res as $row) {
            $mobile_orders[] = $row;
        }
        $return['mobile_orders'] = $mobile_orders;
        return $return;
    }
    function getMobileCRMDetails($get){
		global $callConfig;
		$return = array();
		if($get['gettype']== 'countries'){
			$countriessql = "SELECT id,country_name FROM countries WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$return=$callConfig->getAllRows($countriessql); 
		}elseif($get['gettype']== 'states'){
			$statessql = "SELECT id,state_name FROM states WHERE company_id = '".$get['company_id']."' AND country_id = '".$get['country_id']."' AND is_active = 0";
			$return=$callConfig->getAllRows($statessql);
		}elseif($get['gettype']== 'cities'){
			$citiessql = "SELECT id,city_name FROM cities WHERE company_id = '".$get['company_id']."' AND state_id = '".$get['state_id']."' AND is_active = 0";
			$return=$callConfig->getAllRows($citiessql);
		}elseif($get['gettype']== 'crm_leads'){
			$crm_leadssql = "SELECT * FROM crm_leads WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active = 0";
			$return=$callConfig->getRow($crm_leadssql);
		}
		return $return;
	}
	function togetdeliveryschedulesdetails($get){
		global $callConfig;
		$query = "SELECT application_table_name as transaction_table, ref_transaction_number as transaction_number, transaction_id, planned_qty, planned_delivery_date, planned_delivery_time FROM delivery_schedules WHERE id=".$get['ref_id'];
		$return=$callConfig->getRow($query);
		return $return;
	}
	function getFaceLivenessResponse($get){
		global $callConfig;
		$api_hit_id = $get['api_hit_id'];
		$query = "SELECT webhook_response FROM api_hits WHERE id=".$get['api_hit_id'];
		$res=$callConfig->getRow($query);
		$webhook_response = json_decode($res->webhook_response,1);
		//echo "<pre>"; print_r($webhook_response);
		$result = $webhook_response['result'];
		$return['liveness_prediction'] =$result['liveness']['prediction'];
		$return['liveness_face_match_score'] =$result['face_match']['match_score'];
		if($result['liveness']['prediction'] != "real"){
			$return['response_code'] = "102";
			$return['response_message'] = 'liveness Fake';
		}elseif((int)$result['face_match']['match_score'] <= 50){
			$return['response_code'] = "102";
			$return['response_message'] = 'Face Not Match';
		}else{
			$return['response_code'] = "200";
			$return['response_message'] = 'Transaction Successful';
		}
		$return['api_hit_id'] = $api_hit_id;
		return $return;
	}
	function deliverdorderlistbasepartyid($get){
		global $callConfig;
		$query = "SELECT id, transaction_id, ref_transaction_number as transaction_number, application_table_name  FROM delivery_schedules WHERE company_id= ".$get['company_id']." AND party_id = ".$get['party_id']." AND is_active =0";

		$return['data']=$callConfig->getAllRows($query);
		return $return;
	}
	function getMobileAllEmployeeLeaves($get){
		global $callConfig;
		$query = "SELECT emp_code, display_name, leaves FROM `ai_employees` WHERE id < 5 AND company_id= ".$get['company_id']." AND is_active =0";
		$return=$callConfig->getAllRows($query);
		return $return;
	}
	function getMobileSingleEmployeeLeaves($get){
		global $callConfig;
		$empname = $get['empname'];
		$query = "SELECT emp_code, display_name, leaves FROM `ai_employees` WHERE company_id= ".$get['company_id']."  AND display_name LIKE '%".$empname."%'  AND is_active =0";
		$return=$callConfig->getRow($query);
		return $return;
	}
	function insertMobileEmployeeLeaves($get,$post){
		global $callConfig;
		//$return['get'] = $get;
		//$return['post'] = $post;
		$datediff = strtotime($post['leave_to']) - strtotime($post['leave_from']);
		$datediff = floor($datediff/(60*60*24));
		$leave_days = $datediff+1;
		$year = date("Y");
		$query = "SELECT id, (applicable_leaves-(consumed_leaves+unapproved_leaves)) as available_leave FROM emp_leave_balances WHERE leave_type='".$post['leave_type_id']."' AND leave_year ='".$year."' AND employee_id = '".$post['employee_id']."' AND is_active= '0'";
		$result=$callConfig->getRow($query);
		if($result->available_leave > 0){

			$levcatsql = "SELECT leave_category_id FROM leave_types WHERE company_id = '".$get['company_id']."' AND id = '".$post['leave_type_id']."' AND is_active = 0";
			$levcatres=$callConfig->getRow($levcatsql);
		
			$insert_array = array('company_id'=>$get['company_id'], 'employee_id'=>$post['employee_id'], 'leave_type_id'=>$post['leave_type_id'], 'leave_from'=>$this->dateconvertdbformat($post['leave_from']), 'leave_to'=>$this->dateconvertdbformat($post['leave_to']), 'leave_days'=>$leave_days, 'application_date'=>date('Y-m-d H:i:s'), 'special_request'=>$post['special_request'], 'current_status'=>'0', 'is_active'=>'0', 'created_by'=>$post['user_id'],'leave_category_id'=>$levcatres->leave_category_id);
			$res=$callConfig->insertRecord('employee_leaves',$insert_array);
			$transaction_id = $res['LASTID'];
			$res['message'] = 'Record Inserted Successfully';
			$return  = $res; 

	
			if($res['status'] == 'sucess'){
				$emp_leave_balancessql = "UPDATE `emp_leave_balances` SET `consumed_leaves`= consumed_leaves + ".$leave_days." WHERE `leave_type`='".$post['leave_type_id']."' AND leave_year ='".$year."' AND employee_id = '".$post['employee_id']."'  AND is_active= '0'";
				$emp_leave_balancesres = $callConfig->executeQuery($emp_leave_balancessql);
	

				$get['transaction_table'] = "employee_leaves";
				$get['current_status'] = 0;
				$get['transaction_no'] = $transaction_id;
				$get['next_status'] = 101;
				$get['comment'] = "AI";
				$return['workflow'] = $this->submitstatusdynamicworkflow($get);
			}


		}else{
			$return['status'] = 'fail'; 
	        $return['query'] = $query;
	        $return['error'] = 'Leave Balance Not Available';
	        $res['message'] = 'Leave Balance Not Available';
		}
		return $return;
	}
	function getMobileReportingManagerEmployeeLeave($get){
		global $callConfig;
		$useriid = $get['user_id'];
		$leave_from = $get['leave_from'] !=""?$get['leave_from']:'2024-07-11';
		$leave_to = $get['leave_to'] !=""?$get['leave_to']:'2024-07-11';
		$condition = "";
		if($get['getleaveapitype'] == "all_approval_pendding"){
			$condition = " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."','".ST_APPROVED."')";
		
		}elseif($get['getleaveapitype'] == "all_approval_pendding_with_date"){
			$condition .= " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."','".ST_APPROVED."')";
			$condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";

		}elseif($get['getleaveapitype'] == "all_approved"){
			$condition .= " AND empl.current_status IN('".ST_APPROVED."')";
		

		}elseif($get['getleaveapitype'] == "all_approved_with_date"){
			$condition .= " AND empl.current_status IN('".ST_APPROVED."')";
				$condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";
		}else{
			$condition = " AND empl.current_status NOT IN('".ST_ACTIVE."','".ST_DRAFT."')";
			$condition .= " AND ((leave_from >= '".$leave_from."' AND leave_from <= '".$leave_to."') OR (leave_to >= '".$leave_from."' AND leave_to <= '".$leave_to."'))";
		
		}
		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.reporting_manager_id, empl.id as employee_leave_id, empl.leave_type_id, empl.leave_from, empl.leave_to, empl.leave_days, empl.application_date , empl.current_status, (SELECT lc.name FROM leave_categories lc WHERE empl.company_id= lc.company_id AND empl.leave_type_id = lc.id AND lc.is_active =0) as leave_category_name FROM employees emp, employee_leaves empl WHERE emp.company_id= ".$get['company_id']." AND emp.company_id= empl.company_id AND emp.id= empl.employee_id AND emp.reporting_manager_id = '".$useriid."' AND emp.is_active =0 ".$condition;
		$return=$callConfig->getAllRows($query);
		//echo "<pre>"; print_r($return);
		$returnres['data'] = $return;
		return $returnres;
	}
	function getInsertEmployeeLeaves($get){
		global $callConfig;
		$useriid = $get['user_id'];
		$emp_id = $get['emp_id'];
		$returnres['get'] = $get;
		$year = date("Y");
		if($emp_id ==""){
			$getempsql = "SELECT id FROM employees WHERE company_id = '".$get['company_id']."' AND user_id = '".$get['user_id']."' AND is_active = 0";
			$resemp = $callConfig->getRow($getempsql);
			$emp_id = $resemp->id;
		}
		if($get['resulttype'] == "LIST"){
			$get['table'] ="employee_leaves";
			$status_progress = $this->getStatusProgressList($get);
			$returnres['status_progress'] = $status_progress;

			$statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$allstatus = $callConfig->getAllRows($statussql); 
			foreach($allstatus as $value){
					$status[$value->name_value]= $value;
			}
			$return['status'] = $status; 

			$empleavequery = "SELECT el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name FROM employee_leaves el, leave_categories lc WHERE el.company_id = '".$get['company_id']."' AND el.company_id = lc.company_id AND el.leave_category_id = lc.id AND el.is_active = lc.is_active";
			$empleaveres=$callConfig->getAllRows($empleavequery);
			$returnres['employee_leaves'] = $empleaveres;
		}elseif($get['resulttype'] == "APPROVEPENDINGLEAVE"){

			$get['table'] ="employee_leaves";
			$status_progress = $this->getStatusProgressList($get);
			$returnres['status_progress'] = $status_progress;

			$statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$allstatus = $callConfig->getAllRows($statussql); 
			foreach($allstatus as $value){
					$status[$value->name_value]= $value;
			}
			$return['status'] = $status; 


			$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, tal.send_back_flag, tal.call_back, el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name FROM transaction_approval_logs tal, employee_leaves el, leave_categories lc WHERE tal.company_id = '".$get['company_id']."' AND tal.company_id = el.company_id AND tal.transaction_table = 'employee_leaves' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = el.id AND el.company_id = lc.company_id AND el.leave_category_id = lc.id AND el.is_active = lc.is_active AND el.is_active = 0";
			$transapprlogres = $callConfig->getAllRows($transapprlogsql);
			$returnres['transaction_approval_logs'] = $transapprlogres;
			$returnres['transaction_approval_logs_sql'] = $transapprlogsql;



		}elseif($get['resulttype'] == "SINGLELEAVE"){
			$get['table'] ="employee_leaves";
			$status_progress = $this->getStatusProgressList($get);
			$returnres['status_progress'] = $status_progress;

			$statussql = "SELECT id,name,name_value,background_color,color,sequence FROM status WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$allstatus = $callConfig->getAllRows($statussql); 
			foreach($allstatus as $value){
					$status[$value->name_value]= $value;
			}
			$return['status'] = $status; 
			
			$empleavequery = "SELECT el.id, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days, el.application_date, el.current_status, lc.name, emp.display_name, emp.emp_code FROM employee_leaves el, leave_categories lc, employees emp WHERE el.company_id = '".$get['company_id']."' AND el.company_id = lc.company_id AND el.company_id = emp.company_id AND el.id = '".$get['rid']."' AND el.leave_category_id = lc.id AND el.employee_id = emp.id AND el.is_active = lc.is_active AND el.is_active = emp.is_active AND el.is_active = 0";
			$empleaveres=$callConfig->getRow($empleavequery);
			$returnres['employee_leaves'] = $empleaveres;
		}else{	
			$query = "SELECT elb.id, elb.employee_id, elb.applicable_leaves, elb.consumed_leaves, elb.unapproved_leaves, (elb.applicable_leaves-(elb.consumed_leaves+elb.unapproved_leaves)) as available_leave, elb.leave_type as leave_type_id, lt.leave_type_name, lt.leave_category_id, lc.name, lc.description FROM emp_leave_balances elb, leave_types lt, leave_categories lc WHERE elb.company_id = '".$get['company_id']."' AND   elb.company_id = lt.company_id AND  elb.company_id = lc.company_id AND elb.leave_type = lt.id AND lt.leave_category_id = lc.id AND elb.employee_id = '".$emp_id."' AND elb.leave_year = '".$year."' AND (elb.applicable_leaves-(elb.consumed_leaves+elb.unapproved_leaves)) > 0 AND elb.is_active = 0 AND elb.is_active = lt.is_active AND lt.is_active = lc.is_active";
			$returnres['emp_leave_balances_sql'] = $query;
			$return=$callConfig->getAllRows($query);
			$returnres['emp_leave_balances'] = $return;
		}
		return $returnres;
	}
	function checkWorkFlowStatusWithApprove($get){
		global $callConfig;
		//$return['get'] = $get;
		$transaction_no = $get['transaction_no'];
		$transaction_table = $table_name = $get['transaction_table'];
		$current_status = $get['current_status'];
		$userid = $get['user_id'];
		$result = $this->getDynamicWorkFlowSteps($get);
	   	$workflowsteps = $result['workflowsteps'];
	    $workflow_details = $result['workflow_details'];
	   // print_r($workflowsteps);
	    $status_progress = $result['status_progress'];
	    $breadcrumb =$result['breadcrumb'];
	    $verifystatusflow = $result['breadcrumb'];
	    $verifystatusflow[ST_APPROVED] = ST_APPROVED;
	    $nextstepkeyvalue = $result['nextstepkeyvalue'];
	    $transaction_owner_id = $result['transaction_owner_id'];
	    $transaction_approval_logs = $result['transaction_approval_logs'];
	    $talapproved = $result['transaction_approval_logs_approved'];
	    $application_user_module_roles = $result['application_user_module_roles'];
	    $current_transactionauthprized=$breadcrumb[$current_status];
	 	$rejecturl = $sendbackurl = "";
	    if(!isset($result['error'])){
			if($current_status != ST_APPROVED AND $current_status != ST_REJECTED){
				//$workflow_btn .= '<div class="btn-group workflow-btn" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">';
	    		if($current_status == ST_ACTIVE OR $current_status == ST_SEND_BACK OR $current_status == ST_CALLBACK OR $workflow_details->workflow_trigger_status == $current_status){  //Active Time Show Button
	    			if($transaction_owner_id ==$userid){
	    				if($current_status == ST_SEND_BACK OR $current_status == ST_CALLBACK){
	    					$current_status = $workflow_details->workflow_trigger_status; // direct use accept
		    				$nextstatus = $workflow_details->workflow_trigger_status; // direct use accept
		    			}else{
	    					$nextstatus = $workflow_details->workflow_trigger_status;
	    				}
	    				//echo "current_status  - ".$current_status; 
	    				$errorstatus = "success";
	    				$workflow_btn = API."?mode=submitstatusdynamicworkflow&module_id=".$get['module_id']."&data_name=".$get['data_name']."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&comment=AI&next_status=".$nextstatus."&current_status=".$workflowsteps[$current_status]['workflow_level_status']."&transaction_no=".$transaction_no."&transaction_table=".$table_name;

	    				//$dynamicfunction  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$nextstatus."','".$workflowsteps[$current_status]->workflow_level_status."')";

	    				//$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp; '.$status_progress[$workflowsteps[$current_status]->current_status]->action_label.'</button>';
			    	}else{
			    		$errorstatus = "fail";
			    		$workflow_btn .= "You are not authorized";
			    	}
			    	//echo "workflow_btn - ".$workflow_btn;
	    		}else{
	    			if($transaction_owner_id ==$userid){ //ST_CALLBACK
	    				//$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".$current_status."','".ST_CALLBACK."','".$status_progress[$workflowsteps[$current_status]->current_status]->name."','".$status_progress[ST_CALLBACK]->action_label."')";
	    				
	    				//$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-retweet text-white"></span> &nbsp; '.$status_progress[ST_CALLBACK]->name.'</button>';
	    	
	    			}elseif(isset($transaction_approval_logs[$userid])){	
	    				$next_step_owner = $userid;
	    				$errorstatus = "success";
	    				$workflow_btn = API."?mode=submitstatusdynamicworkflow&module_id=".$get['module_id']."&data_name=".$get['data_name']."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&comment=AI&next_status=".$nextstepkeyvalue[$current_status]."&current_status=".$current_status."&transaction_no=".$transaction_no."&transaction_table=".$table_name;



	   		 			//$dynamicfunction  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".$nextstepkeyvalue[$current_status]."')";

		    			//$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp; '.$status_progress[$nextstepkeyvalue[$current_status]]->action_label.'</button>';
			  		
	    				$rejecturl = API."?mode=submitstatusdynamicworkflow&module_id=".$get['module_id']."&data_name=".$get['data_name']."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&comment=AI&next_status=".ST_REJECTED."&current_status=".$current_status."&transaction_no=".$transaction_no."&transaction_table=".$table_name;

			  			//$dynamicfunctionreject  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".ST_REJECTED."')";
			  			//$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="'.$dynamicfunctionreject.'"> <span class="fa fa-thumbs-down text-white"></span> Reject</button>';

	    				$sendbackurl = API."?mode=submitstatusdynamicworkflow&module_id=".$get['module_id']."&data_name=".$get['data_name']."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&comment=AI&next_status=".ST_SEND_BACK."&current_status=".$current_status."&transaction_no=".$transaction_no."&transaction_table=".$table_name;


			  			//$dynamicfunctionsendback  = "submitDynamicWorkflowPopUp('".$table_name."','".$transaction_no."','".$current_status."','".ST_SEND_BACK."')";
						//$workflow_btn .= '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="'.$dynamicfunctionsendback.'"> <span class="fa fa-retweet text-white"></span> Send back</button>';
				}else{ //NOT AUTHORIZED
					$errorstatus = "fail";
	    			$workflow_btn .= "You are not authorized / already approved";
	    		}

	    	}
			//$workflow_btn .= '</div>';
		}
		$returnarray = array('status'=>$errorstatus,'approve_url'=>$workflow_btn,'reject_url'=>$rejecturl,'send_back_url'=>$sendbackurl);	
		}else{



		// $workflow_btn .= '<div class="btn-group workflow-btn" role="group" data-bs-toggle="tooltip" data-placement="top" title="" data-original-title=".btn-xlg">';
		 if($result['error'] == '9061' AND $transaction_owner_id ==$userid AND $current_status == ST_DRAFT){
			$errorstatus = "success";
			$workflow_btn = API."?mode=submitstatusdynamicworkflow&module_id=".$get['module_id']."&data_name=".$get['data_name']."&user_id=".$get['user_id']."&company_id=".$get['company_id']."&comment=AI&next_status=".ST_ACTIVE."&current_status=".$current_status."&transaction_no=".$transaction_no."&transaction_table=".$table_name;

			//$dynamicfunction  = "submitDynamicWorkflow('".$table_name."','".$transaction_no."','".$current_status."','".ST_ACTIVE."','".$status_progress[$current_status]->name."','".$status_progress[ST_ACTIVE]->name."')";
			//$workflow_btn .= '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" onclick="'.$dynamicfunction.'"> <span class="fa fa-thumbs-up text-white"></span> &nbsp;'.$status_progress[ST_DRAFT]->action_label.'</button>';
		 }elseif($result['error'] == '9063' OR $result['error'] == '9062' OR $result['error'] == '9060'){
		 	$errorstatus = "fail";
			$workflow_btn = $result['error'];
			//$workflow_btn .= '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light"><span class="fa fa-info text-white"></span> &nbsp;'.$_SESSION['error_message'][$result['error']].'</button>';
			
		 }
		$returnarray = array('status'=>$errorstatus,'approve_url'=>$workflow_btn,'reject_url'=>$rejecturl,'send_back_url'=>$sendbackurl);	
		}	
		//echo "<pre>"; print_r($returnarray);
		return $returnarray;
	}
	function transportrequestcreate($get){
		global $callConfig;
		//echo "<pre>"; print_r($get); exit;
		$query = "SELECT id, driver_name FROM drivers WHERE company_id = ".$get['company_id']." AND is_active =0";
		$rows = $callConfig->getAllRows($query);
		$return['drivers'] = $rows;
		$query = "SELECT id, vehicle_name, registration_number FROM transports WHERE company_id = ".$get['company_id']." AND is_active =0";
		$rows = $callConfig->getAllRows($query);
		$return['transports'] = $rows;
		return $return;
	}
	function togetdrivelistalldata($get){
		global $callConfig;
		$str="ta.user_id = ".$get['user_id']." AND ";
		if($get['driver_id']!=""){
			$query = "SELECT 
				    t.vehicle_name,
				    t.registration_number,
				    ta.*
				FROM 
				    transport_assign ta
				JOIN 
				    transports t ON ta.transport_id =  t.id 
				WHERE 
					ta.company_id = ".$get['company_id']." AND
				    ta.driver_id = ".$get['driver_id']." AND
					ta.is_active = 0";
		}else{
			$query = "SELECT 
				    ta.*
				FROM 
				    transport_assign ta
				WHERE 
					ta.company_id = ".$get['company_id']." AND
				    ta.user_id = ".$get['user_id']." AND
					ta.is_active = 0";
		}
		
		$rows = $callConfig->getAllRows($query);
		$return['data'] = $rows;
		$query = "SELECT * FROM status WHERE name_value IN (SELECT status FROM status_progress WHERE company_id =".$get['company_id']." AND table_name = 'transport_assign' AND is_active =0) AND is_active =0";
		$rows = $callConfig->getAllRows($query);
		$return['status'] = $rows;
		return $return;
	}
	function togetinprocesstripdetailswithexpencess($get){
		global $callConfig;
		$query = "SELECT id, expense_code, user_id, emp_id, expense_date, tax_amount, total_amount, transport_assign_id, client_reimbursement, description, pending_amount, current_status FROM emp_expenses WHERE company_id = ".$get['company_id']." AND id =".$get['exp_id']." AND is_active =0";
    	//echo $query;
    	$row = $callConfig->getRow($query);

    	if($row->id!=""){
    		//print_r($ee_res); exit;
    		$data=(array)$ress;
    		if($row->transport_assign_id!=""){
    			$query = "SELECT id, `driver_id`,  `transport_id`, `source_place`, `source_latitude`, `source_longitude`, `destination_place`, `destination_latitude`, `destination_longitude`, `start_reading`, `stop_reading`, `start_reading_image`, `stop_reading_image`, `contact_details`, `contact_phone`, `start_date`, `start_time`, `actual_start_date`, `actual_start_time`, `departure_date`, `departure_time`, `remarks` FROM transport_assign WHERE company_id = ".$get['company_id']." AND  id=".$row->transport_assign_id." AND is_active =0";
    			$datares = $callConfig->getRow($query);
    			$datares = (array)$datares;
    			$data = array_merge($data,$datares);
    			if($datares['transport_id'] !=""){
    				$query = "SELECT vehicle_name, registration_number, vehicle_capacity, vehicle_type  FROM transports WHERE company_id = ".$get['company_id']." AND  id=".$row->transport_id." AND is_active =0";
    				$ttres = $callConfig->getRow($query);
    				$ttres = (array)$ttres;
    				$data = array_merge($data,$ttres);   
    			} 			
    		}
    		
    	}
		$row = $callConfig->getRow($query);
		//echo "<pre>".$query;print_r($row); exit;

		if($row->id!=""){
			$return['data'] = (array)$row;
			
			$query = "SELECT 
			    eed.id, 
			    eed.emp_expenses_id, 
			    eed.merchant_name, 
			    eed.bill_no, 
			    eed.bill_date, 
			    eed.bill_file, 
			    eed.description, 
			    eed.payment_mode,
			    (SELECT ge.genc_detail_name FROM general_code_details as ge WHERE ge.genc_detail_code  = eed.payment_mode  AND ge.genc_code= 'PAYMETHOD') as payment_mode_name ,
			   
			    eed.tax_amount, 
			    eed.expense_amount 
			FROM 
				emp_expenses_details AS eed
			WHERE  eed.company_id = ".$get['company_id']." AND eed.emp_expenses_id = ".$row->id." AND eed.is_active =0";
			//echo $sdata; exit;
			$sdata = $callConfig->getAllRows($query);
			//$return['data']['child_query'] = $query;
			$return['data']['child'] =(array)$sdata;
		}
		return $return;
	}
	function togetdynamicresaonlist($get){
    	//echo "--------------"; exit;
    	global $callConfig;
    	$query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get['company_id']."' and genc_code='".$get['code']."' order by id ASC"; 
		$display_details = $callConfig->getAllRows($query);
		$data['data'] = $display_details;
		return $data;
    }
    function togetemployeeexpensesbyuser($get){
    	global $callConfig;
    	
		$billssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status, reference_date FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND (emp_id='".$get['emp_id']."' OR user_id = '".$get['user_id']."') ORDER BY id DESC";
		$billsres = $callConfig->getAllRows($billssql);
		$return['emp_expenses'] = $billsres;
		
		$get['table'] ="trips";
        $status_progress = $this->getStatusProgressList($get);
        $return['status_progress'] = $status_progress;
 
        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, trp.expense_code, trp.expense_date, trp.amount, trp.id
        FROM transaction_approval_logs tal, emp_expenses trp WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = trp.company_id AND  tal.transaction_table = 'emp_expenses' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = trp.id AND trp.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $return['transaction_approval_logs'] = $transapprlogres;

    	return $return;
    }
    function togetemployeeexpensesbyuser_old($get){
    	global $callConfig;
    	// $query = "SELECT ee.id, ta.driver_id, ta.transport_id, ta.source_place, ta.source_latitude, ta.source_longitude, ta.destination_place, ta.destination_latitude, ta.destination_longitude, ta.start_reading, ta.stop_reading, ta.start_reading_image,ta.stop_reading_image, ta.contact_phone, ta.start_date, ta.start_time, ta.actual_start_date, ta.actual_start_time, ta.departure_date, ta.departure_time, ta.remarks, ee.expense_code, ee.transport_assign_id, ee.expense_date, ee.total_amount, ts.vehicle_name, ts.registration_number, ts.vehicle_capacity, ts.vehicle_type FROM emp_expenses ee, transport_assign ta, transports ts  WHERE ee.company_id = ta.company_id AND ee.company_id = ts.company_id AND ee.company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND ee.transport_assign_id = ta.id  AND ta.transport_id = ts.id  AND  transport_assign_id IN (SELECT id FROM transport_assign WHERE company_id =".$get['company_id']."  AND is_active=0)";
    	$query = "SELECT id, expense_code, user_id, emp_id, expense_date, tax_amount, total_amount, transport_assign_id, client_reimbursement, description, pending_amount, current_status FROM emp_expenses WHERE company_id = ".$get['company_id']." AND user_id =".$get['user_id']." AND is_active =0 ORDER BY id DESC";
    	//echo $query;
    	$ress = $callConfig->getAllRows($query);

    	foreach($ress as $ee_res){
    		//print_r($ee_res); exit;
    		$data=(array)$ee_res;
    		if($ee_res->transport_assign_id!=""){
    			$query = "SELECT  `driver_id`,  `transport_id`, `source_place`, `source_latitude`, `source_longitude`, `destination_place`, `destination_latitude`, `destination_longitude`, `start_reading`, `stop_reading`, `start_reading_image`, `stop_reading_image`, `contact_details`, `contact_phone`, `start_date`, `start_time`, `actual_start_date`, `actual_start_time`, `departure_date`, `departure_time`, `remarks` FROM transport_assign WHERE company_id = ".$get['company_id']." AND  id=".$ee_res->transport_assign_id." AND is_active =0";
    			$datares = $callConfig->getRow($query);
    			$datares = (array)$datares;
    			$data = array_merge($data,$datares);
    			if($datares['transport_id'] !=""){
    				$query = "SELECT vehicle_name, registration_number, vehicle_capacity, vehicle_type  FROM transports WHERE company_id = ".$get['company_id']." AND  id=".$ee_res->transport_id." AND is_active =0";
    				$ttres = $callConfig->getRow($query);
    				$ttres = (array)$ttres;
    				$data = array_merge($data,$ttres);   
    			} 			
    		}
    		$alldata[] = $data;
    	}
    		
    	return $alldata;
    }
    function togettaskdaworkspacenoprojectandtask($get){
    	global $callConfig;
    	$uwhr = ""; $cwhr ="";
    	if( $get['role_short_code']!='TASKDA_ADMIN'){
    		//echo "----------";
    		$uwhr = " LEFT JOIN project_task_teams ptt
					    ON ptt.project_task_id = pt.id
					   AND ptt.project_id = p.id				   
					   AND ptt.user_id = ".$get['user_id'];
			$cwhr = "AND  ptt.user_id IS NOT NULL ";
    	}
    	$query = "SELECT 
		    pws.id AS workspace_id,
		    pws.name AS workspace_name,
		    pws.created_by as workspace_creater,
		    COUNT( DISTINCT p.id) AS number_of_projects,
		    COUNT(DISTINCT pt.id) AS number_of_tasks,
		    ROUND(
			        (
			            COUNT(DISTINCT CASE
			                WHEN pt.current_status = 199 THEN pt.id
			            END) * 100.0
			        ) / NULLIF(COUNT(DISTINCT pt.id), 0),
			        2
			    ) AS completion_percentage
		FROM 
		    project_work_spaces pws
		LEFT JOIN 
		    projects p ON p.project_work_space_id = pws.id AND p.is_active=0
		LEFT JOIN 
		    project_task_details pt ON pt.project_id = p.id AND pt.is_active=0
		".$uwhr."
		WHERE 
			pws.company_id = ".$get['company_id']."
			".$cwhr."
			AND pws.is_active=0 
		GROUP BY 
		    pws.id, pws.name
		ORDER BY 
		    pws.name";
		//echo $query;// exit;
		$return = $callConfig->getAllRows($query);
		return $return;
    }
    function togetprojecteditdetailsval($get){
    	global $callConfig;
    	$query = "SELECT id, type_name FROM project_type WHERE company_id =".$get['company_id']." AND is_active=0";
    	$type = $callConfig->getAllRows($query);
    	$return['project_type'] = $type;
    	if($get['rid']!=""){
    		$query = "SELECT * FROM projects WHERE company_id =".$get['company_id']." AND id=".$get['rid'];
    		$data = $callConfig->getRow($query);
    		$return['data'] = $data;
    	}
    	return $return;
    }
    function togetmobileprojectsview($get){
    	global $callConfig;
    	$query = "SELECT p.id, p.project_logo, p.project_name, p.description, p.planned_start, p.planned_finish, p.created_by as project_creater, COUNT(DISTINCT pt.id) AS number_of_project_teams, COUNT(DISTINCT t.id) AS number_of_project_tasks, CASE WHEN COUNT(t.id) = 0 THEN 0 ELSE ROUND( (SUM(CASE WHEN t.current_status = 199 THEN 1 ELSE 0 END) / COUNT(t.id)) * 100, 2 ) END AS percentage_of_tasks_completed FROM projects p LEFT JOIN project_teams pt ON p.id = pt.project_id AND pt.is_active = 0 LEFT JOIN project_task_details t ON p.id = t.project_id AND t.is_active = 0 WHERE p.company_id =".$get['company_id']." AND p.project_work_space_id=".$get['work_space_id']." AND p.is_active =0 GROUP BY p.id";
    	//echo $query; 
    	$works = $callConfig->getAllRows($query);
    	//print_r($works); exit;
    	foreach($works as $work){
    		//print_r($work);
    		$work_ids[] = $work->id;
    	}
    	$return['get'] = $get;
    	if(count($work_ids)>0){
    		$query = "SELECT project_id, count(id) as cc FROM project_phases WHERE company_id =".$get['company_id']." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
			$project_phases = $callConfig->getAllRows($query);    		
			$query = "SELECT project_id,count(id) as cc FROM project_teams WHERE company_id =".$get['company_id']." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
			$project_teams = $callConfig->getAllRows($query);    	
			$query = "SELECT project_id,count(id) as cc FROM project_task_status WHERE company_id =".$get['company_id']." AND project_id IN (".implode(",",$work_ids).") AND is_active =0 GROUP BY project_id";
			$project_task_status = $callConfig->getAllRows($query); 
			foreach($project_task_status as $project_status){
				if($project_status->cc==0){
					$status = array('0'=>'To DO','101'=>'Doing','5'=>'On Hold','199'=>'Completed');
					$k=10;
					foreach($status  as $skey=>$sta){
						$insert_array = array('company_id'=>$get['company_id'], 'project_id'=> $project_status->cc, 'table_name'=>'project_task_details', 'name'=>$sta, 'status'=>$skey, 'action_label'=>$sta,'display_priority'=>$k,'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);	
						$kresult['insert'][] = $insert_array;
						$kresult['res'][] = $callConfig->insertRecord('project_task_status',$insert_array);
						$k = $k+10;
					}
				}
			}
			foreach($project_phases as $project_phase){
				$phase[$project_phase->project_id] = $project_phase->cc;
			}		
			foreach($project_teams as $project_team){
				$team[$project_team->project_id] = $project_team->cc;
			}
    	}
    	foreach($works as $work){
    		$main =(array)$work;
    		$main['projet_value'] = 'N';
    		if($phase[$work->id]>0 && $team[$work->id]>0){
    			$main['projet_value'] = 'Y';
    		}
    		$main['projet_team_cc'] = $team[$work->id];
    		$main['project_phase_cc'] = $phase[$work->id];
    		$pass[]= $main;
    	}
    	$return['data'] = $pass;
    	return $return;
    }
    function todisplaytasklistview($get){
    	global $callConfig;
    	$uwhr = "";
    	if($get['role_short_code']!='TASKDA_ADMIN'){
    		$uwhr = " AND ( p.id IN (SELECT `project_task_id` FROM `project_task_teams` WHERE `user_id` =".$get['user_id']." AND project_id =".$get['project_id']." AND is_active=0 ) OR p.created_by = ".$get['user_id'].")";
    	}
    	$query = "SELECT p.id, p.task_name, p.project_id, p.description, p.current_status, p.assigen_id,au.display_name,t.name as status_name,p.planned_start,p.planned_finish, p.created_by FROM project_task_details p LEFT JOIN application_users au ON p.assigen_id = au.id AND au.is_active = 0 LEFT JOIN project_task_status t ON p.current_status = t.status AND  table_name='project_task_details' AND t.is_active = 0 AND p.project_id=t.project_id  WHERE p.company_id =".$get['company_id']." AND p.project_id=".$get['project_id']." AND p.is_active =0 ".$uwhr." GROUP BY p.id";
    	//echo $query; 
    	$return['data'] = $callConfig->getAllRows($query);
    	return $return;
    }
    function projecttaskstatuscreation($get){
    	global $callConfig;
    	$status = array('0'=>'To DO','101'=>'Doing','5'=>'On Hold','199'=>'Completed');
		$k=10;
		foreach($status  as $skey=>$sta){
			$insert_array = array('company_id'=>$get['company_id'], 'project_id'=> $get['project_id'], 'table_name'=>'project_task_details', 'name'=>$sta, 'status'=>$skey, 'action_label'=>$sta,'display_priority'=>$k,'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);	
			$kresult['insert'][] = $insert_array;
			$kresult['res'][] = $callConfig->insertRecord('project_task_status',$insert_array);
			$k = $k+10;
		}
		return $kresult;
    }
    function togettaskcreatelistdata($get){
    	global $callConfig;
    	//echo "----------------------"; exit;
    	$tt = array('1'=> 'Story', '2'=> 'Task', '3'=> 'Sub Task', '4'=>'Bug');
    	foreach($tt as $tp=>$pp){
    		$task_type[]= array('id'=>$tp,'type'=>$pp);
    	}	
    	$return['task_type'] = $task_type;
    	$query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get['company_id']." AND table_name='project_task_details' AND project_id=".$get['project_id']." AND is_active=0";
    	//echo $query;
    	$project_task_status = $callConfig->getAllRows($query);
    	if(count((array)$project_task_status)<1){
    		$dass = $this->projecttaskstatuscreation($get);
    		$query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get['company_id']." AND table_name='project_task_details' AND project_id=".$get['project_id']." AND is_active=0";
	    	//echo $query;
	    	$project_task_status = $callConfig->getAllRows($query);
    	}
    	$return['project_task_status'] = $project_task_status;
    	$query = "SELECT id, display_name FROM application_users WHERE company_id =".$get['company_id']." AND id IN ( SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND project_id=".$get['project_id']." AND is_active=0 ) AND is_active=0";
    	//echo $query;
    	$project_teams = $callConfig->getAllRows($query);
    	$return['project_teams'] = $project_teams;
    	$query = "SELECT * FROM  application_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get['project_id'].") AND is_active = 0";
		$lableres = $callConfig->getAllRows($query);
		$lab = array();
		foreach($lableres as $lable){
			$lab[$lable->work_id][$lable->id] = $lable;
		}
		$return['labels'] = count((array)$lab[$get['project_id']])>0 ? $lab[$get['project_id']] : $lab['0'];
		$query = "SELECT id, project_phase_name FROM project_phases WHERE company_id =".$get['company_id']." AND project_id=".$get['project_id']." AND is_active=0";
    	//echo $query;
    	$project_phases = $callConfig->getAllRows($query);
    	$return['project_phases'] = $project_phases;
    	if($get['rid']!=""){
    		/*$query = "SELECT * FROM project_task_details WHERE company_id =".$get['company_id']." AND id=".$get['rid']." AND is_active=0";
    		$taskdata = $callConfig->getRow($query);
    		$return['data'] = $taskdata;

    		$query = "SELECT user_id FROM project_task_teams WHERE company_id =".$get['company_id']." AND project_task_id=".$get['rid']." AND is_active=0";
    		$project_task_teams = $callConfig->getAllRows($query);
    		$return['project_task_teams'] = $project_task_teams;
    		$query = "SELECT application_label_id FROM  application_attach_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['rid']." AND is_active=0";
			$attachres = $callConfig->getAllRows($query);
			foreach($attachres as $attachr){
				$alab[] = $attachr->application_label_id;
			}
			$return['attach_labels'] = $alab;*/
    	}
    	return $return;
    }
    function togetmobiletaskdetailsall($get){
    	global $callConfig;
    	$query ="SELECT pt.*, (SELECT project_phase_name  FROM project_phases WHERE id=pt.project_phase_id AND is_active=0 ) as project_phase_name, (SELECT name  FROM project_task_status WHERE  table_name='project_task_details' AND project_id = pt.project_id AND status=pt.current_status AND is_active =0 ) as status_name  FROM project_task_details pt WHERE pt.company_id=".$get['company_id']." AND pt.id=".$get['task_id']." AND pt.is_active=0";
    	//$return['taskdetils_query'] =$query;
    	$data = $callConfig->getRow($query);
    	$get['project_id'] = $data->project_id;
    	$return['taskdetils'] =$data;
    	$query = "SELECT id,user_id, (SELECT au.display_name FROM application_users au  WHERE au.id = user_id) as display_name, project_role_id, (SELECT pr.role_name FROM project_roles pr WHERE pr.id=project_role_id) role_name FROM project_task_teams WHERE company_id =".$get['company_id']." AND project_task_id=".$get['task_id']." AND is_active=0";
    	$project_task_teams = $callConfig->getAllRows($query);
    	$return['project_task_teams'] = $project_task_teams;
    	$query = "SELECT id, display_name FROM application_users WHERE company_id =".$get['company_id']." AND id IN ( SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND project_id=".$get['project_id']." AND is_active=0 ) AND is_active=0";
    	//echo $query;
    	$project_teams = $callConfig->getAllRows($query);
    	$return['project_teams'] = $project_teams;
    				
    	$query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get['company_id']." AND is_active=0";
    	$project_roles = $callConfig->getAllRows($query);
    	$return['project_team_roles'] = $project_roles;
    	$query = "SELECT id, status, name as status_name FROM project_task_status WHERE company_id =".$get['company_id']." AND table_name='project_task_details' AND project_id=".$get['project_id']." AND is_active=0";
    	//echo $query;
    	$project_task_status = $callConfig->getAllRows($query);
    	$return['project_task_status'] = $project_task_status;
    	$query = "SELECT id,application_label_id, (SELECT al.label_code FROM application_labels al WHERE al.id=application_label_id) as label_code FROM  application_attach_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['task_id']." AND is_active=0";
		$attachres = $callConfig->getAllRows($query);
		foreach($attachres as $attachr){
			$alab[] = $attachr;
		}
		$return['attach_labels'] = $alab;
		$query = "SELECT id, label_name, label_code,work_id FROM  application_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get['project_id'].") AND is_active = 0";
		$lableres = $callConfig->getAllRows($query);
		$lab = array();
		foreach($lableres as $lable){
			$lab[$lable->work_id][] = $lable;
		}
		$return['labels'] = count((array)$lab[$get['project_id']])>0 ? $lab[$get['project_id']] : $lab['0'];
		$query = "SELECT id, parent_id, check_name,assign_id, if(current_status!=4,'N','Y') check_status FROM  application_check_lists WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id =".$get['task_id']." AND is_active = 0 ORDER BY parent_id";
		$checkeres = $callConfig->getAllRows($query);
		$checklist = array();
		$i=0;
		foreach($checkeres as $cres){
			if($cres->parent_id==0){
				$main[$i] = (array)$cres;
				$val[$cres->id] =$i;
				$i++;
			}else{
				if(isset($main[$val[$cres->parent_id]])){
					$main[$val[$cres->parent_id]]['child'][] = (array)$cres;
				}
			}

		}
		// print_r($main); print_r($val); exit;
		foreach($main as $ww){
			$pchl[] = $ww;
		}
		$return['checklist'] =$pchl;
		//$query = "SELECT id, comments FROM  project_task_activities WHERE company_id =".$get['company_id']." AND activity_type = 'activity' AND application_table_name = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['task_id']." AND is_active=0";
		$query = "SELECT a.id, a.comments, a.created_date, u.display_name, CONCAT( UPPER(SUBSTRING(SUBSTRING_INDEX(u.display_name, ' ', 1), 1, 1)), UPPER(SUBSTRING(SUBSTRING_INDEX(u.display_name, ' ', -1), 1, 1)) ) AS initials FROM project_task_activities a LEFT JOIN application_users u ON a.created_by = u.id WHERE  a.company_id =".$get['company_id']." AND a.activity_type = 'activity' AND a.application_table_name = '".TBL_PROJECT_TASK_DETAILS."' AND a.transaction_id = ".$get['task_id']." AND a.is_active=0";
		//$return['comment_query'] = $query;
		$comments = $callConfig->getAllRows($query);
		$return['comments'] = $comments;

		$query = "SELECT id,file_name,actual_file_name FROM  application_attachments WHERE company_id =".$get['company_id']." AND application_table_name = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['task_id']." AND is_active=0";
		//$return['attachments_query'] = $query;
		$attachres = $callConfig->getAllRows($query);
		$return['attachments'] = $attachres;
		$crow=$callConfig->getRow("SELECT id,task_start,task_id FROM project_task_time_log WHERE  company_id = ".$get['company_id']." AND task_end is NULL AND is_active = 0 ORDER BY id DESC LIMIT 0,1"); 
		$return['project_task_time_status'] = "N";
		if(count((array)$crow)>0){
			$return['project_task_time_status'] = "Y";
		}
		$return['project_task_time_log'] = $crow;
		$xcrow=$callConfig->getAllRows("SELECT id,task_start,task_end,task_id FROM project_task_time_log WHERE  company_id = ".$get['company_id']." AND  is_active = 0 "); 
		$return['all_project_task_time_status'] = $xcrow;
    	return $return;
    }
    function addupdaterolememberdetailformobile($get,$post){
    	global $callConfig;

		$query = "SELECT id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND project_id = ".$post['project_id']."  AND project_task_id =".$post['task_id']." AND user_id = ".$post['member_ids']." AND is_active = 0";
		$datas= $callConfig->getAllRows($query);
		$upquery = $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date('Y-m-d')." WHERE id=".$post['task_id']);
		if(count((array)$datas)>0){
			$return = array('status'=>'fail','message'=>'user already exits');
			return $return;
		}else{
			$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'], 'project_task_id'=>$post['task_id'], 'project_role_id'=>$post['role_id'],'user_id'=>$post['member_ids'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
			$insert = $callConfig->insertRecord('project_task_teams',$insert_array);

			$task_query = "SELECT id, task_name, project_id FROM project_task_details WHERE company_id = ".$get['company_id']."   AND id =".$post['task_id']." is_active = 0";
			$task_data = $callConfig->getRow($task_query);
			$query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get['company_id']." AND id = ".$get['member_ids']." AND is_active =0";
			$user_data = $callConfig->getRow($query);
			$get['mobile'] = $user_data->user_mobile;

			$message = 'You have been assigned the task '.$task_data->task_name;

			$array = array('company_id'=>$get['company_id'], 'mobile_no'=>$get['mobile'], 'message'=>$message, 'send_no'=> '8179947020', 'recivce_no'=> $get['mobile'],'data_name'=>$get['data_name']);
			
			

			
			$array['user_name'] = $user_data->display_name;
			$array['user_email_id'] = $user_data->user_email;
			$array['session_company_id'] = $user_data->company_id;
			$task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND  project_task_id=".$post['task_id']." AND is_active=0";
			$teams  = $callConfig->getAllRows($task_team_q);
			//print_r($teams); exit;
			foreach($teams as $list){
				$mobilsendnotification = array();
				$mobilsendnotification = $get;
				$mobilsendnotification['message'] ='Someone has added a new user for this task. '.$task_data->task_name;;
				$mobilsendnotification['user_id'] =$list->user_id;
				$mobilsendnotification['title'] = 'Task Assign';
				$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['transaction_id'], 'project_id' =>$task_data->project_id, 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
				//print_r($mobilsendnotification); exit;
				$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);
			}
			$res['msg_otp']['whatsup'] = $this->tosendwhatsappdata($array);
			$notarry = array();
			$emill = $this->sendNotificationAndEmails($notarry,$array,99);
		}

		$return = $insert;
		return $return;
    }
    function togetmobileprojectdetails($get){
    	
    	global $callConfig;
    	$project_id = $get['project_id'];
    	
		$query = "SELECT * FROM projects WHERE company_id =".$get['company_id']." AND id=".$project_id;
		$data = $callConfig->getRow($query);
		$return['project_data'] = $data;

		$query = "SELECT id, type_name FROM project_type WHERE company_id =".$get['company_id']." AND is_active=0";
    	$type = $callConfig->getAllRows($query);
    	$return['project_type'] = $type;

    	$query = "SELECT id, project_phase_name FROM project_phases WHERE company_id =".$get['company_id']." AND project_id=".$project_id." AND is_active=0";
		$project_phases = $callConfig->getAllRows($query);
		$return['project_phases'] = $project_phases;

		/*$query = "SELECT id, project_phase_name FROM project_phases WHERE company_id =".$get['company_id']." AND project_id=".$project_id." AND is_active=0";
		$project_phases = $callConfig->getAllRows($query);
		$return['project_phases'] = $project_phases;*/
		
		$query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get['company_id']." AND is_active=0";
    	$project_roles = $callConfig->getAllRows($query);
    	$return['project_roles'] = $project_roles;

    	$query = "SELECT id,  user_id, (SELECT au.display_name FROM application_users au WHERE au.id = user_id )   as display_name, role_id, (SELECT pr.role_name FROM project_roles pr WHERE pr.id = role_id ) as role_name FROM project_teams WHERE company_id =".$get['company_id']." AND project_id=".$project_id." AND is_active=0";
    	$project_teams = $callConfig->getAllRows($query);
    	//$return['project_teams_qyery'] = $query; 
    	$return['project_teams'] = $project_teams;    	

    	$query = "SELECT id, status, name as status_name, action_label FROM project_task_status WHERE company_id =".$get['company_id']." AND table_name='project_task_details' AND project_id=".$project_id." AND is_active=0";
    	$project_task_status = $callConfig->getAllRows($query);
    	$return['project_task_status'] = $project_task_status;

    	$query = "SELECT id, display_name FROM application_users WHERE company_id =".$get['company_id']." AND role_id LIKE '%TASKDA%'  AND is_active=0";
    	$users = $callConfig->getAllRows($query);
    	$return['users'] = $users; 
		return $return;
    }
    function touserwisetaskbaseoninputmobile($get){
    	global $callConfig;
    	$whr = "AND pt.planned_start <= CURDATE()";
    	if($get['type']=='W'){
    		$whr = "AND pt.planned_start BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
    	}
    	if($get['type']=='M'){
    		$whr = "AND MONTH(pt.planned_start) = MONTH(CURDATE()) AND YEAR(pt.planned_start) = YEAR(CURDATE())";
    	}
    	$uwhr = "";
    	if($get['role_short_code']!='TASKDA_ADMIN'){
    		$uwhr = " AND user_id = ".$get['user_id'];
    	}
    	$query  = "SELECT pt.id as task_id, pt.project_id , pt.task_name, pt.planned_start,pt.planned_finish, pt.current_status, (SELECT name FROM project_task_status pts WHERE pts.project_id = pt.project_id AND pts.table_name = 'project_task_details' AND pts.status = pt.current_status AND pts.is_active = 0 ) as status_name, CASE WHEN pt.planned_finish < CURDATE() THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details pt WHERE pt.company_id = ".$get['company_id']." AND pt.current_status < ".ST_APPROVED." AND (pt.id IN (SELECT project_task_id FROM `project_task_teams` WHERE company_id = ".$get['company_id']." ".$uwhr."  AND is_active =0) OR pt.assigen_id = ".$get['user_id'].")".$whr." AND pt.is_active = 0 ORDER BY pt.updated_date DESC";
    	//$query = "SELECT id as task_id, project_id ,task_name, planned_start, current_status FROM project_task_details			WHERE company_id = ".$get['company_id']." AND	current_status < ".ST_APPROVED." AND (id IN (SELECT project_task_id FROM `project_task_teams` WHERE `user_id` = ".$get['user_id']." AND is_active =0) OR assigen_id = ".$get['user_id'].") ".$whr." AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$asign_query = "SELECT pt.id as task_id, pt.project_id , pt.task_name, pt.planned_start,pt.planned_finish, pt.current_status, (SELECT name FROM project_task_status pts WHERE pts.project_id = pt.project_id AND pts.table_name = 'project_task_details' AND pts.status = pt.current_status AND pts.is_active = 0 ) as status_name, CASE WHEN pt.planned_finish < CURDATE() THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details pt WHERE pt.company_id = ".$get['company_id']." AND pt.current_status < ".ST_APPROVED." AND pt.created_by=".$get['user_id']." AND pt.is_active=0  ORDER BY pt.updated_date DESC";
		$asign_data = $callConfig->getAllRows($asign_query);
    	//$return['query'] = $query; 
		//$return['asign_query'] = $asign_query; 
    	$return['data'] = $data; 
    	$return['create_data'] = $asign_data; 
		return $return;
    }
    function togetchatdatabasetableandtransactionid($get){
    	global $callConfig;
    	$query = "SELECT table_name, transaction_id, query_text, originator, created_date, (SELECT au.display_name FROM application_users au WHERE au.id =originator ) as display_name  FROM `transaction_queries` WHERE company_id =".$get['company_id']." AND  table_name = '".$get['table_name']."' AND transaction_id =".$get['transaction_id']." AND is_active =0";
    	$data = $callConfig->getAllRows($query);
    	return $data;
    }
    function togetchatdiscussdatadetails($get){
    	global $callConfig;
    	
    	$whr = " AND ( user_id =".$get['user_id']." or created_by = ".$get['user_id']." ) ";
    	if($get['role_short_code']=='TASKDA_ADMIN'){
    		//echo "-----";
    		$whr = "";
    	}

    	if($get['chart_type'] == 'project_work_spaces'){
    		$query ="SELECT id, name FROM project_work_spaces WHERE company_id =".$get['company_id']." AND id IN (SELECT project_work_space_id FROM projects WHERE company_id =".$get['company_id']." AND id IN (SELECT project_id FROM project_teams WHERE company_id =".$get['company_id']." ".$whr." AND is_active =0 ) AND is_active =0";
    	} 
    	if($get['chart_type'] == 'projects'){
    		$query ="SELECT id,  project_name as name FROM projects WHERE company_id =".$get['company_id']." AND id IN (SELECT project_id FROM project_teams WHERE company_id =".$get['company_id']." ".$whr." AND is_active =0) AND is_active =0 ";
    	}
    	if($get['chart_type'] == 'project_task_details'){
    		$query ="SELECT id, task_name as name FROM project_task_details WHERE company_id =".$get['company_id']." AND id IN (SELECT project_task_id  FROM project_task_teams  WHERE company_id =".$get['company_id']." ".$whr." AND is_active =0) AND is_active =0 ";
    	}    	
    	//echo $query; exit;
    	$datas = $callConfig->getAllRows($query);    	    	
    	foreach($datas as $dat){
    		$data = (array)$dat;
    		$retur['id'] = $data['id'];
    		$retur['name'] = $data['name'];
    		$retur['data'] = array();
    		$ids[] = $data['id'];
    		$return[] = $retur;
    	}
    	//print_r($ids); print_r($return); exit;
    	if(count((array)$ids)>0){
    		$lastchart = array();
    		$query = "SELECT max(id) as id, query_text, transaction_id, originator, created_date, (SELECT au.display_name FROM application_users au WHERE au.id =originator ) as display_name FROM transaction_queries WHERE company_id = ".$get['company_id']." AND table_name='".$get['chart_type']."' AND transaction_id in (".implode(",", $ids).") AND is_active =0";
    		$lists = $callConfig->getAllRows($query);
    		
    		foreach((array)$lists as $list){
    			$lastchart[$list->transaction_id] = $list;
    		}
    		//echo $query;print_r($lastchart); exit;
    		foreach((array)$return as $ret){
	    		$pass = $ret;
	    		//if(isset($lastchart[$ret['id']])) {
	    			$pass['data'] =$lastchart[$ret['id']];
	    		//}
	    		$res_pass[] = $pass;
	    	}
    	}else{
    		$res_pass = $return;
    	}
    	return $res_pass;
    }
    function mobileInitials($str) {
		$ret = '';
		foreach (explode(' ', $str) as $word)
			$ret .= strtoupper($word[0]);
		if(strlen($ret)>2){
			$ret = substr($ret, 0,1).substr($ret, -1);
		}
		return strtoupper($ret);
	}
	function mobileuserlogout($get){
		global $callConfig;
		$update = "update user_mobile_details set is_active = NULL, updated_by = ".$get['user_id']." where user_id ='".$get['user_id']."' AND is_active = 0";
		$update_data=$callConfig->executeQuery($update);
		$update = "update user_mobile_details set is_active = NULL, updated_by = ".$get['user_id']." where user_id ='".$get['user_id']."' AND is_active = 0";
		$update_data=$callConfig->executeQuery($update);
		$upquery = "UPDATE appliccation_user_login_tokens SET s_active = NULL, updated_by = ".$get['user_id']." WHERE company_id= ".$get['company_id']." AND user_id = ".$get['user_id']." AND login_type = 'Mobile' AND is_active=0";
		$res = $callConfig->executeQuery($upquery);
		return $update_data;
	}
	function togetpersonalgatewaydetails($get){
		global $callConfig;
		$return['get'] = $get;
		//$method_type = array('entry_first'=>'Entry First', 'exit_first'=> 'Exit First');
		
		$query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get['company_id']."' and genc_code='GATE_METHOD_TYPE'"; 
		$display_details = $callConfig->getAllRows($query);
		$return['method_type'] = $display_details;
		$query = "SELECT id, emp_code, CONCAT(emp_code, ' - ', display_name) as display_name, email_id, phone FROM employees WHERE company_id =".$get['company_id']." AND is_active=0";
		$emp_data  = $callConfig->getAllRows($query);
		$return['emp_data'] = $emp_data;
		$visitPurposes = array(
		    'official' => 'Official Work',
		    'meeting' => 'Meeting',
		    'delivery' => 'Delivery',
		    'pickup' => 'Pickup',
		    'personal' => 'Personal',
		    'other' => 'Other');
		$query = "SELECT id, genc_detail_code, genc_detail_name FROM general_code_details where is_active='0' and company_id='".$get['company_id']."' and genc_code='VISIT_PURPOSE'"; 
		$vis_details = $callConfig->getAllRows($query);
		$return['visit_purpose'] = $vis_details;
		
		$query = "SELECT id, vehicle_reg_no, model, model_group FROM transport_vehicle_master WHERE company_id =".$get['company_id']." AND is_active=0";
		$vehicle_reg_no  = $callConfig->getAllRows($query);
		$return['vehicle_reg_no'] = $vehicle_reg_no;
		$query = "SELECT id, location_name FROM locations WHERE company_id =".$get['company_id']." AND is_active=0";
		$location  = $callConfig->getAllRows($query);
		$return['location'] = $location;
		$return['need_vehicle'] = 'N';
		$return['gateway_types'] = array(['id'=> 1, 'type_name' => 'One Way'],
									['id'=> 2, 'type_name' => 'Return']);
		//print_r($get); exit;
		return $return;
	}
	function toaddpersonaldataonmobile($get, $post){
		global $callConfig;

		$person_detai = array('employee_id'=>$post['emp_id'] ?? null,'employee_name'=>$post['employee_name'] ?? null,'employee_contact'=>$post['employee_contact'] ?? null);
		$person_details = json_encode($person_detai,1);
		if($get['rid']!=""){
			// $insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,'location'=>$post['location'] ?? null,'entry_date'=>$post['entry_date'] ?? null,'entry_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get['user_id'], 'created_by'=>$get['user_id'],"gateway_type" => $post['gateway_type'],"process_val" => 1);
			$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,'location'=>$post['location'] ?? null,'exit_date'=>$post['entry_date'] ?? null,'exit_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get['user_id'], 'created_by'=>$get['user_id'],"gateway_type" => $post['gateway_type'],"process_val" => 1);
	    	//echo "<pre>"; print_r($insert_array); exit;
			
			// $res=$callConfig->updateRecord('transport_personal_gateway',$insert_array,'id',$get['rid']); changed by koti
			$res=$callConfig->updateRecord('transport_personal_gateway',$insert_array,'id',$get['rid']);
		}else{
		// $insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,'location'=>$post['location'] ?? null,'entry_date'=>$post['entry_date'] ?? null,'entry_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get['user_id'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'],"gateway_type" => $post['gateway_type'],"process_val" => 1);
		$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'] ?? null,'vehicle_number'=>$post['vehicle_number'] ?? null,'notes'=>$post['notes'] ?? null,'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'] ?? null,'location'=>$post['location'] ?? null,'exit_date'=>$post['entry_date'] ?? null,'exit_time'=>$post['entry_time'] ?? null,'method_type'=>$post['method_type'] ?? null,'authorized_by'=>$get['user_id'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'],"gateway_type" => $post['gateway_type'],"process_val" => 1);
	    	//echo "<pre>"; print_r($insert_array); exit;
			$res=$callConfig->insertRecord('transport_personal_gateway',$insert_array);
		}
		// emp_id, employee_name, employee_contact, vehicle_number, visit_purpose, location, entry_date, entry_time, method_type, 
		return $res;
	}	
	function toupdatepersonaldataonmobile($get, $post){
		global $callConfig;
		$person_detai = array('employee_id'=>$post['emp_id'],'employee_name'=>$post['employee_name'],'employee_contact'=>$post['employee_contact']);
		$person_details = json_encode($person_detai,1);
		$insert_array = array('company_id'=>$get['company_id'],'user_id'=>$post['emp_id'],'vehicle_number'=>$post['vehicle_number'],'person_details'=>$person_details,'visit_purpose'=>$post['visit_purpose'],'location'=>$post['location'],'entry_date'=>$post['entry_date'],'entry_time'=>$post['entry_time'],'method_type'=>$post['method_type'],'authorized_by'=>$get['user_id'], 'created_by'=>$get['user_id']);
    	//echo "<pre>"; print_r($insert_array); exit;
		
		$res=$callConfig->updateRecord('transport_personal_gateway',$insert_array,'id',$get['trn_id']);
		// emp_id, employee_name, employee_contact, vehicle_number, visit_purpose, location, entry_date, entry_time, method_type, 
		return $res;
	}
	function getAttendanceRegularizationRequestData($get){
	    global $callConfig;
	    $return  = array();
	    $company_id = $get['company_id'];
	    $query ="select id, reason_name from attedenace_regularizaton_reasons where company_id={$company_id} AND is_active=0";
	    $res = $callConfig->getAllRows($query);
	    $return['reasons'] = $res;

	    if($get['id']){
	    	$query ="select * from attendenace_regularization_requests where id={$get['id']} AND company_id={$company_id} AND is_active=0";
		    $res = $callConfig->getRow($query);
		    $return['request'] = $res;

	    }
	    return $return;
	}

	function saveAttendanceRegularizationRequestData($get, $post){
		global $callConfig;
	    $return  = array();
	    $insertary = array(
			'company_id' => $get['company_id'],
			'employee_id' => $post['employee_id'],
			'request_date' => $post['request_date'],
			'regularization_date' => $post['regularization_date'],
			'regularization_of_checkin' => $post['regularization_of_checkin'],
			'regularization_of_checkout' => $post['regularization_of_checkout'],
			'regularization_reason' => $post['regularization_reason'],
			'remarks' => $post['remarks'],
			'image' => $post['image'],
			'loc_latitude' => $post['loc_latitude'],
			'loc_longitude' => $post['loc_longitude']
			);
		if ($post['rid'] != "") {
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$insertary['updated_by'] = $get['user_id'];
			$res = $callConfig->updateRecord('attendenace_regularization_requests', $insertary, 'id', $post['rid']);
			$return['res'] = $res;
		} else {
			$insertary['is_active'] = 0;
			$insertary['current_status'] = 0;
			$insertary['status_date'] = date("Y-m-d H:i:s");
			$insertary['created_by'] = $get['user_id'];
			$insertary['created_date'] = date("Y-m-d H:i:s");
			$res = $callConfig->insertRecord('attendenace_regularization_requests', $insertary);
			$return['res'] = $res;
		}
	    return $return;

	}

	function getTransportVehicleGatePassDeatails($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
 
        $statusQuery = "SELECT id, name, name_value, background_color, color FROM status 
                        WHERE company_id = " . $company_id . " 
                        AND is_active = 0";
        $statusList = $callConfig->getAllRows($statusQuery);
        $return['status_list'] = $statusList;

        

        $vehicle_id  = $get['vehicle_id'] ?? '';
        if($vehicle_id != ''){
        	$vehicle = $callConfig->getRow("SELECT * FROM transport_vehicle_master WHERE id={$vehicle_id} AND company_id = {$company_id} AND is_active = 0 ");
	        $return['vehicle'] = $vehicle;

	        $employees = $callConfig->getAllRows("SELECT id, emp_code, CONCAT(emp_code, ' - ', display_name) as display_name, email_id, phone FROM employees WHERE company_id = {$company_id} AND is_active = 0 ");
	        $return['drivers'] = $employees;

	        $return['demo_types'] = array(
			    'demo' => 'Demo Car',
			    'car' => 'Car',
			    'truck' => 'Truck',
			    'van' => 'Van',
			    'motorcycle' => 'Motorcycle',
			    'bus' => 'Bus',
			    'trailer' => 'Trailer',
			    'other' => 'Other'
			);
			$return['gate_pass_types'] = array(
			    'demo' => 'Demo ',
			    'interbranch' => 'Interbranch',
			    'event' => 'Event',
			    'petrolpump' => 'Petrolpump',
			    'replacement' => 'Peplacement',
			    'testdrive_showroom' => 'Testdrive - Showroom',
			    'testdrive_doorstep' => 'Testdrive - Doorstep',
			    'customer_visit' => 'Customer - Visit',
			    'other' => 'Other'
			);

        }else{
        	$where = "" ;
	        if (isset($get['status']) && $get['status'] != '') {
	            $where .= " AND m.current_status = " . intval($get['status']);
	        }
	        if (!empty($get['from_date']) && !empty($get['to_date'])) {
	            $from_date = $get['from_date'];
	            $to_date = $get['to_date'];
	            $where .= " AND m.DATE(created_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
	        }
	 
	        $vehicles = $callConfig->getAllRows("SELECT  m.* ,    IFNULL(
		        (
		            SELECT 
		                CASE 
		                    WHEN tg.process_val = 1 THEN 'OUT'
		                    WHEN tg.process_val = 2 THEN 'IN'
		                    ELSE 'IN'
		                END
		            FROM transport_gateway tg
		            WHERE tg.vehicle_number = m.vehicle_reg_no
		              AND tg.is_active = 0
		            ORDER BY tg.id DESC
		            LIMIT 1
		        ),
		        'IN'
		    ) AS vehicle_status 
		 FROM transport_vehicle_master m WHERE m.company_id = " . $company_id. " AND  m.is_active = 0  AND m.vehicle_reg_no != '' AND m.vehicle_type = 'demo' $where  ORDER BY m.vehicle_reg_no ASC");

	        $return['vehicles'] = $vehicles;
        }

        return $return;
    }

    function savevehiclegatepass($get, $post){
		global $callConfig;
 		
		$method_type = 'exit_first';
		
		$time_value = $post['out_time'] ?? date('H:i:s');
		$date_value = $post['out_date'] ?? date('Y-m-d');
 
		$fieldnames = array(
			'company_id' => $get['company_id'], 
			'user_id' => $get['user_id'], 
			'vehicle_number' => $post['vehicle_number'], 
			'vehicle_type' => $post['demo_type'] ?? '', 
			'driver_name' => $post['customer_name'] ?? '', 
			'driver_contact' => $post['customer_contact'] ?? '', 
			'employee_id' => $post['employee_id'] ?? '', 
			'gate_pass' => $post['gate_pass'] ?? '', 
			'enquriy_no' => $post['enquiry_no'] ?? '', 
			'notes' => $post['remarks'] ?? '', 
			'emp_ids'=> $post['emp_ids']??'',
			'location'=> $post['location']??'',
			'method_type' => $method_type,
			"process_val" => 1
		);
		
		$fieldnames['exit_date'] = $date_value;
		$fieldnames['exit_time'] = $time_value;
		$fieldnames['exit_timestamp'] = date('Y-m-d H:i:s');
		
		if($get['rid'] != ''){
			$fieldnames['updated_by'] = $get['user_id'];
			$fieldnames['updated_date'] = date('Y-m-d H:i:s');
			$res = $callConfig->updateRecord('transport_gateway', $fieldnames, 'id', $get['rid']);

		}else{
			$fieldnames['is_active'] = 0;
			$fieldnames['current_status'] = 0;
			$fieldnames['created_by'] = $get['user_id'];
			$fieldnames['created_date'] = date('Y-m-d H:i:s');
			$res = $callConfig->insertRecord('transport_gateway', $fieldnames);
		}
		
		return $res;
	}

	function insertMobiDynamicMappingTransactions($get, $post){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$return['post'] = $post;

		$transaction_table= $post['transaction_table'];
		$transaction_date = $this->dateconvertdbformat($post['transaction_date']);
		$originator= $post['originator'];
		$party_id= $post['party_id'];
		$place_of_supply= $post['place_of_supply'];
		$amount= $post['amount'];
		$tax_amount= $post['tax_amount'];
		$total_amount= $_POST['total_amount'];
		$doc_ref_no= $_POST['doc_ref_no'];
		$doc_ref_date= $this->dateconvertdbformat($_POST['doc_ref_date']);
		$ref_type= $_POST['ref_type'];
		$ref_id= $_POST['ref_id'];
		$ref_no= $_POST['ref_no'];
		$ref_date = $this->dateconvertdbformat($_POST['ref_date']);
		$payment_terms = $_POST['payment_terms'];
		

		$ref_table_dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$post['company_id']."' AND application_table_name = '".$transaction_table."' AND is_active = 0 AND item_table !='' AND tax_table!=''";
		$ref_table_dmtres=$callConfig->getRow($ref_table_dmtsql);

		if($ref_table_dmtres->item_table !="" AND $ref_table_dmtres->tax_table !=""){

			$originatorsql = "SELECT * FROM application_users WHERE company_id = '".$post['company_id']."' AND id = '".$originator."' AND is_active = 0";
			$originatorres=$callConfig->getRow($originatorsql);


			$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$post['company_id']."' AND application_table_name = '".$transaction_table."' AND field_name = 'party_id'";	
			$parttable_name = $callConfig->getRow($display_attribute_typessql);
			$qquery = urldecode($parttable_name->content);
			$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
			if(isset($explosql[1])){
				$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
			}
			$partytable = trim($explosql[0]); //get Table name
			if($partytable !=""){
				$partysql = "SELECT * FROM ".$partytable." WHERE company_id = ".$post['company_id']." AND id = ".$party_id." AND is_active = 0";
				$partyres = $callConfig->getRow($partysql);
			}


			//$payment_term_historyary[] = array('amount'=>$total_amount,'due_date'=>$transaction_date,'extra_amt'=>'0','extra_date'=>'','percentage'=>'100','tax_amount'=>$tax_amount,'without_tax_amount'=>$total_amount);
			//$payment_term_history = json_encode($payment_term_historyary);

			

			if($place_of_supply !=""){
				$sqlgstbill = "SELECT * FROM `customer_vendor_gst_billing_details` WHERE company_id = ".$post['company_id']." AND id = '".$place_of_supply."' AND is_active = 0";
				$gstbillres = $callConfig->getRow($sqlgstbill);
				$party_tax_number = $gstbillres->gstin!=""?$gstbillres->gstin:$partyres->gstin;
				$party_phone = $gstbillres->phone!=""?$gstbillres->phone:$partyres->phone;
				$party_address = $gstbillres->billing_address!=""?$gstbillres->billing_address:$partyres->billing_address;
				$party_ship_adderss = $gstbillres->shipping_address!=""?$gstbillres->shipping_address:$partyres->shipping_address;
				$supplier_state_id= $gstbillres->shipping_state!=""?$gstbillres->shipping_state:$partyres->shipping_state;
				$party_email= $gstbillres->email!=""?$gstbillres->email:$partyres->email;
			}else{
				$party_tax_number = $partyres->gstin;
				$party_phone = $partyres->phone;
				$party_address = $partyres->billing_address;
				$party_ship_adderss = $partyres->shipping_address;
				$supplier_state_id=  $partyres->shipping_state;
				$party_email = $partyres->email;
			}
			if($originatorres->default_state_id == $supplier_state_id){
				$tax_state = "S";
			}else{
				$tax_state = "I";
			}
			$payment_terms = $payment_terms!=""?$payment_terms:$partyres->payment_terms;


	        $mainfieldsary=array('company_id'=>$post['company_id'],'transaction_date'=>$transaction_date,'transaction_location_id'=>$originatorres->location_id,'transaction_warehouse_id'=>$originatorres->default_warehouse_id,'currency_id'=>$originatorres->default_currency_id,'currency_rate'=>1,'originator'=>$originator,'party_id'=>$partyres->id,'party_name'=>$partyres->company,'party_tax_number'=>$party_tax_number,'party_email'=>$party_email,'party_phone'=>$party_phone,'party_address'=>$party_address,'party_ship_adderss'=>$party_ship_adderss,'place_of_supply'=>$place_of_supply,'payment_terms'=>$payment_terms,'amount'=>$amount,'tax_amount'=>$tax_amount,'total_amount'=>$total_amount,'ref_type'=>$ref_type,'ref_id'=>$ref_id,'ref_no'=>$ref_no,'ref_date'=>$ref_date,'cost_center_id'=>$originatorres->cost_center,'originator_currency_id'=>$originatorres->default_currency_id,'originator_state_id'=>$originatorres->default_state_id,'supplier_state_id'=>$supplier_state_id,'due_date'=>$transaction_date, 'pending_amount'=>$total_amount,'doc_ref_no'=>$doc_ref_no,'doc_ref_date'=>$doc_ref_date,'tax_state'=>$tax_state,'current_status'=>ST_ACTIVE);

	      // $mainfieldsary['payment_term_history'] = $this->getPaymentTermsResult($get,$post,$payment_terms);

	        if($post['discount_amount'] !="" AND $post['discount_amount'] !="0"){
	        	$mainfieldsary['discount_type'] = 'A';
	        	$mainfieldsary['discount_value'] = $post['discount_amount'];
	        	$mainfieldsary['discount_amount'] = $post['discount_amount'];
	        }
	        if($post['rounding_off'] !="" AND $post['rounding_off'] !="0"){
	        	$mainfieldsary['rounding_off'] = $post['rounding_off'];
	        }
	        

	        if($post['additional_charges'] !=""){
	        	$additional_charge = array();
	        	$shipping_tax = $shipping_amount = $shipping_tax_amount = 0;
	        	foreach($post['additional_charges'] as $acharge){
	        		$adicharesql = "SELECT ac.id, ac.charge_name, hsn.gst FROM additional_charges ac LEFT JOIN hsn_and_sac_codes hsn ON ac.hsn_and_sac_code_id = hsn.id WHERE ac.company_id = '".$post['company_id']."' AND ac.charge_name like '%".$acharge['charge_name']."%' AND ac.is_active = 0";
	        		$adichareres = $callConfig->getRow($adicharesql);
	        		$charge_name = $adichareres->charge_name!=""?$adichareres->charge_name:$acharge['charge_name'];
	        		$charge_id = $adichareres->id!=""?$adichareres->id:0;
	        		$shipping_tax_amount = $acharge['tax_amount']+$acharge['charge_amount'];
	        		$additional_charge[] = array("shipping_tax"=>$adichareres->gst,"shipping_amount"=>$acharge['charge_amount'],"shipping_tax_amount"=>$acharge['tax_amount'],"charge_name"=>$charge_name,"charge_id"=>$charge_id);

	        		$shipping_tax +=$acharge['tax_amount'];
	        		$shipping_amount +=$acharge['charge_amount'];
	        		$shipping_tax_amount += $shipping_tax_amount;

	        	}
	        	$mainfieldsary['additional_charges'] = json_encode($additional_charge);
	        	$mainfieldsary['shipping_tax'] = $shipping_tax;
	        	$mainfieldsary['shipping_amount'] = $shipping_amount; 
	        	$mainfieldsary['shipping_tax_amount'] = $shipping_tax_amount;
	        }
	        //
	        if($post['rid'] !=""){
	        	$mainfieldsary['updated_by']=$post['user_id'];
				$mainfieldsary['updated_date']=date('Y-m-d H:i:s');
	        	$insert_data=$callConfig->updateRecord($transaction_table,$mainfieldsary,'id',$post['rid']);
	        	$insert_id = $post['rid'];
	        	$callConfig->updateRecord($ref_table_dmtres->item_table,array('is_active'=>NULL),'transaction_id',$post['rid']);
	        	$callConfig->updateRecord($ref_table_dmtres->tax_table,array('is_active'=>NULL),'transaction_id',$post['rid']);
			}else{

				//start - Serial Number Masters
				$post['transaction_date'] = $invoiced_at;
				$post['party_id'] = $party_id;
				$post['transaction_type'] = '';
				$transaction_number_details = $this->generatingApplicationSerialNumberMasters($transaction_table,$post);
				$mainfieldsary['transaction_number'] = $transaction_number_details['main_transaction_number'];
				$mainfieldsary['monthly_transaction_number'] = $transaction_number_details['monthly_transaction_number'];
				$mainfieldsary['yearly_transaction_number'] = $transaction_number_details['yearly_transaction_number'];
				$mainfieldsary['lifetime_transaction_number'] = $transaction_number_details['lifetime_transaction_number'];
				//End - Serial Number Masters

				//$transaction_number = $this->toGetApplicationSerialNumber($transaction_table,$post);
				//$mainfieldsary['transaction_number'] = $transaction_number;
				$mainfieldsary['is_active']=0;
				$mainfieldsary['current_status']=0;
				$mainfieldsary['created_by']=$post['user_id'];
				$mainfieldsary['created_date']=date('Y-m-d H:i:s');
				$insert_data=$callConfig->insertRecord($transaction_table,$mainfieldsary);
				$insert_id = $insert_data['LASTID'];
				$return['insert_data'] = $insert_data;
			}
		}	

			if($insert_data['status'] == 'sucess'){
				foreach($post['items'] as $itemsdetails){
					$ref_transaction_item_table_id = $itemsdetails['ref_transaction_item_table_id'];
					$ref_transaction_table_id = $itemsdetails['ref_transaction_table_id'];
					$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$itemsdetails['item_id'],'item_name'=>$itemsdetails['item_name'],'description'=>$itemsdetails['description'],'purchase_uom'=>$itemsdetails['purchase_uom'],'item_quantity'=>$itemsdetails['item_quantity'],'item_price'=>$itemsdetails['item_price'],'item_amount'=>$itemsdetails['item_amount'],'tax_amount'=>$itemsdetails['tax_amount'],'total_amount'=>$itemsdetails['total_amount'],'pending_qty'=>$itemsdetails['item_quantity'],'transaction_qty'=>$itemsdetails['item_quantity'],'is_active'=>'0','discount_amount'=>$itemsdetails['discount_amount'],'ref_transaction_item_table_id'=>$ref_transaction_item_table_id,'ref_transaction_table_id'=>$ref_transaction_table_id);
					if($itemsdetails['rid'] !=""){
						$itemfieldnames['updated_by']=$post['user_id'];
						$itemfieldnames['updated_date']=date('Y-m-d H:i:s');
			        	$childtableres=$callConfig->updateRecord($ref_table_dmtres->item_table,$itemfieldnames,'id',$itemsdetails['rid']);
			        	$childtableres_id = $itemsdetails['rid']; 
					}else{
						$itemfieldnames['current_status']=0;
						$itemfieldnames['created_by']=$post['user_id'];
						$itemfieldnames['created_date']=date('Y-m-d H:i:s');	
						$childtableres=$callConfig->insertRecord($ref_table_dmtres->item_table,$itemfieldnames);
						$childtableres_id = $childtableres['LASTID']; 
					
					}
					$return['item_insert_data'][] = $childtableres;
					
					//tax related part Insert
					if($childtableres['status'] == 'sucess' AND $itemsdetails['tax_amount']!="0" AND $itemsdetails['tax_amount']!=""){
						$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$itemsdetails['item_id'],'tax_id'=>$itemsdetails['tax_id'],'tax_name'=>$itemsdetails['tax_name'],'amount'=>$itemsdetails['item_taxable_amount'],'tax_amount'=>$itemsdetails['tax_amount'],'total_amount'=>$itemsdetails['total_amount'],'tax_value'=>$itemsdetails['percentage'],'is_active'=>'0','transaction_item_row_id'=>$childtableres_id);
						
						if($itemsdetails['tax_row_rid'] !=""){
							$taxfield['updated_by']=$post['user_id'];
							$taxfield['updated_date']=date('Y-m-d H:i:s');
				        	$childtabletaxres=$callConfig->updateRecord($ref_table_dmtres->tax_table,$taxfield,'id',$itemsdetails['tax_row_rid']);
						}else{
							$taxfield['current_status']=0;
							$taxfield['created_by']=$post['user_id'];
							$taxfield['created_date']=date('Y-m-d H:i:s');
							$childtabletaxres=$callConfig->insertRecord($ref_table_dmtres->tax_table,$taxfield);
						}
						
						$return['tax_insert_data'][] = $childtabletaxres;
					} 
					//End tax related part Insert
				} /**/
			}	

		return $return;
	}
	

	/**
	function insertMobiDynamicMappingTransactions($get, $post){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$return['post'] = $post;

		$transaction_table= $post['transaction_table'];
		$transaction_date = $this->dateconvertdbformat($post['transaction_date']);
		$originator= $post['originator'];
		$party_id= $post['party_id'];
		$place_of_supply= $post['place_of_supply'];
		$amount= $post['amount'];
		$tax_amount= $post['tax_amount'];
		$total_amount= $_POST['total_amount'];
		$doc_ref_no= $_POST['doc_ref_no'];
		$doc_ref_date= $this->dateconvertdbformat($_POST['doc_ref_date']);
		$ref_type= $_POST['ref_type'];
		$ref_id= $_POST['ref_id'];
		$ref_no= $_POST['ref_no'];
		$ref_date = $this->dateconvertdbformat($_POST['ref_date']);
		

		$ref_table_dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$post['company_id']."' AND application_table_name = '".$transaction_table."' AND is_active = 0 AND item_table !='' AND tax_table!=''";
		$ref_table_dmtres=$callConfig->getRow($ref_table_dmtsql);

		if($ref_table_dmtres->item_table !="" AND $ref_table_dmtres->tax_table !=""){

			$originatorsql = "SELECT * FROM application_users WHERE company_id = '".$post['company_id']."' AND id = '".$originator."' AND is_active = 0";
			$originatorres=$callConfig->getRow($originatorsql);


			$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$post['company_id']."' AND application_table_name = '".$transaction_table."' AND field_name = 'party_id'";	
			$parttable_name = $callConfig->getRow($display_attribute_typessql);
			$qquery = urldecode($parttable_name->content);
			$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
			if(isset($explosql[1])){
				$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
			}
			$partytable = trim($explosql[0]); //get Table name
			if($partytable !=""){
				$partysql = "SELECT * FROM ".$partytable." WHERE company_id = ".$post['company_id']." AND id = ".$party_id." AND is_active = 0";
				$partyres = $callConfig->getRow($partysql);
			}


			$payment_term_historyary[] = array('amount'=>$total_amount,'due_date'=>$transaction_date,'extra_amt'=>'0','extra_date'=>'','percentage'=>'100','tax_amount'=>$tax_amount,'without_tax_amount'=>$total_amount);
			$payment_term_history = json_encode($payment_term_historyary);

			

			if($place_of_supply !=""){
				$sqlgstbill = "SELECT * FROM `customer_vendor_gst_billing_details` WHERE company_id = ".$post['company_id']." AND id = '".$place_of_supply."' AND is_active = 0";
				$gstbillres = $callConfig->getRow($sqlgstbill);
				$party_tax_number = $gstbillres->gstin!=""?$gstbillres->gstin:$partyres->gstin;
				$party_phone = $gstbillres->phone!=""?$gstbillres->phone:$partyres->phone;
				$party_address = $gstbillres->billing_address!=""?$gstbillres->billing_address:$partyres->billing_address;
				$party_ship_adderss = $gstbillres->shipping_address!=""?$gstbillres->shipping_address:$partyres->shipping_address;
				$supplier_state_id= $gstbillres->shipping_state!=""?$gstbillres->shipping_state:$partyres->shipping_state;
				$party_email= $gstbillres->email!=""?$gstbillres->email:$partyres->email;
			}else{
				$party_tax_number = $partyres->gstin;
				$party_phone = $partyres->phone;
				$party_address = $partyres->billing_address;
				$party_ship_adderss = $partyres->shipping_address;
				$supplier_state_id=  $partyres->shipping_state;
				$party_email = $partyres->email;
			}
			if($originatorres->default_state_id == $supplier_state_id){
				$tax_state = "S";
			}else{
				$tax_state = "I";
			}
			$payment_terms = $partyres->payment_terms!=""?$partyres->payment_terms:1;
	        $mainfieldsary=array('company_id'=>$post['company_id'],'transaction_date'=>$transaction_date,'transaction_location_id'=>$originatorres->location_id,'transaction_warehouse_id'=>$originatorres->default_warehouse_id,'currency_id'=>$originatorres->default_currency_id,'currency_rate'=>1,'originator'=>$originator,'party_id'=>$partyres->id,'party_name'=>$partyres->company,'party_tax_number'=>$party_tax_number,'party_email'=>$party_email,'party_phone'=>$party_phone,'party_address'=>$party_address,'party_ship_adderss'=>$party_ship_adderss,'place_of_supply'=>$place_of_supply,'payment_terms'=>$payment_terms,'amount'=>$amount,'tax_amount'=>$tax_amount,'total_amount'=>$total_amount,'ref_type'=>$ref_type,'ref_id'=>$ref_id,'ref_no'=>$ref_no,'ref_date'=>$ref_date,'cost_center_id'=>$originatorres->cost_center,'originator_currency_id'=>$originatorres->default_currency_id,'originator_state_id'=>$originatorres->default_state_id,'supplier_state_id'=>$supplier_state_id,'due_date'=>$transaction_date, 'pending_amount'=>$total_amount,'doc_ref_no'=>$doc_ref_no,'doc_ref_date'=>$doc_ref_date,'tax_state'=>$tax_state,'current_status'=>ST_ACTIVE);

	        if($post['discount_amount'] !="" AND $post['discount_amount'] !="0"){
	        	$mainfieldsary['discount_type'] = 'A';
	        	$mainfieldsary['discount_value'] = $post['discount_amount'];
	        	$mainfieldsary['discount_amount'] = $post['discount_amount'];
	        }
	        if($post['rounding_off'] !="" AND $post['rounding_off'] !="0"){
	        	$mainfieldsary['rounding_off'] = $post['rounding_off'];
	        }

	        //,'payment_term_history'=>$payment_term_history
	        if($post['rid'] !=""){
	        	$mainfieldsary['updated_by']=$post['user_id'];
				$mainfieldsary['updated_date']=date('Y-m-d H:i:s');
	        	$insert_data=$callConfig->updateRecord($transaction_table,$mainfieldsary,'id',$post['rid']);
	        	$insert_id = $post['rid'];
	        	$callConfig->updateRecord($ref_table_dmtres->item_table,array('is_active'=>NULL),'transaction_id',$post['rid']);
	        	$callConfig->updateRecord($ref_table_dmtres->tax_table,array('is_active'=>NULL),'transaction_id',$post['rid']);
			}else{
				$transaction_number = $this->toGetApplicationSerialNumber($transaction_table,$post);
				$mainfieldsary['transaction_number'] = $transaction_number;
				$mainfieldsary['is_active']=0;
				$mainfieldsary['current_status']=0;
				$mainfieldsary['created_by']=$post['user_id'];
				$mainfieldsary['created_date']=date('Y-m-d H:i:s');
				$insert_data=$callConfig->insertRecord($transaction_table,$mainfieldsary);
				$insert_id = $insert_data['LASTID'];
				$return['insert_data'] = $insert_data;
			}
		}	

			if($insert_data['status'] == 'sucess'){

				
				
				foreach($post['items'] as $itemsdetails){
					$ref_transaction_item_table_id = $itemsdetails['ref_transaction_item_table_id'];
					$ref_transaction_table_id = $itemsdetails['ref_transaction_table_id'];
					$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$itemsdetails['item_id'],'item_name'=>$itemsdetails['item_name'],'description'=>$itemsdetails['description'],'purchase_uom'=>$itemsdetails['purchase_uom'],'item_quantity'=>$itemsdetails['item_quantity'],'item_price'=>$itemsdetails['item_price'],'item_amount'=>$itemsdetails['item_amount'],'tax_amount'=>$itemsdetails['tax_amount'],'total_amount'=>$itemsdetails['total_amount'],'pending_qty'=>$itemsdetails['item_quantity'],'transaction_qty'=>$itemsdetails['item_quantity'],'is_active'=>'0','discount_amount'=>$post['discount_amount'],'ref_transaction_item_table_id'=>$ref_transaction_item_table_id,'ref_transaction_table_id'=>$ref_transaction_table_id);
					if($itemsdetails['rid'] !=""){
						$itemfieldnames['updated_by']=$post['user_id'];
						$itemfieldnames['updated_date']=date('Y-m-d H:i:s');
			        	$childtableres=$callConfig->updateRecord($ref_table_dmtres->item_table,$itemfieldnames,'id',$itemsdetails['rid']);
			        	$childtableres_id = $itemsdetails['rid']; 
					}else{
						$itemfieldnames['current_status']=0;
						$itemfieldnames['created_by']=$post['user_id'];
						$itemfieldnames['created_date']=date('Y-m-d H:i:s');	
						$childtableres=$callConfig->insertRecord($ref_table_dmtres->item_table,$itemfieldnames);
						$childtableres_id = $childtableres['LASTID']; 
					
					}
					$return['item_insert_data'][] = $childtableres;
					
					//tax related part Insert
					if($childtableres['status'] == 'sucess' AND $itemsdetails['tax_amount']!="0" AND $itemsdetails['tax_amount']!=""){
						$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$itemsdetails['item_id'],'tax_id'=>$itemsdetails['tax_id'],'tax_name'=>$itemsdetails['tax_name'],'amount'=>$itemsdetails['item_amount'],'tax_amount'=>$itemsdetails['tax_amount'],'total_amount'=>$itemsdetails['total_amount'],'tax_value'=>$itemsdetails['percentage'],'is_active'=>'0','transaction_item_row_id'=>$childtableres_id);
						
						if($itemsdetails['tax_row_rid'] !=""){
							$taxfield['updated_by']=$post['user_id'];
							$taxfield['updated_date']=date('Y-m-d H:i:s');
				        	$childtabletaxres=$callConfig->updateRecord($ref_table_dmtres->tax_table,$taxfield,'id',$itemsdetails['tax_row_rid']);
						}else{
							$taxfield['current_status']=0;
							$taxfield['created_by']=$post['user_id'];
							$taxfield['created_date']=date('Y-m-d H:i:s');
							$childtabletaxres=$callConfig->insertRecord($ref_table_dmtres->tax_table,$taxfield);
						}
						
						$return['tax_insert_data'][] = $childtabletaxres;
					} 
					//End tax related part Insert
				} /**/ /**
			}	
				/**foreach($post['items_id'] as $seqkey=>$itemid){

					$itemfieldnames=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'item_id'=>$post['item_id'][$seqkey],'item_name'=>$post['item_name'][$seqkey],'description'=>$post['description'][$seqkey],'purchase_uom'=>$post['purchase_uom'][$seqkey],'item_quantity'=>$post['item_quantity'][$seqkey],'item_price'=>$post['item_price'][$seqkey],'item_amount'=>$post['item_amount'][$seqkey],'tax_amount'=>$post['tax_amount'][$seqkey],'discount_amount'=>0,'total_amount'=>$post['total_amount'][$seqkey],'pending_qty'=>$post['item_quantity'][$seqkey],'transaction_qty'=>$post['item_quantity'][$seqkey],'created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'is_active'=>'0');
					$childtableres=$callConfig->insertRecord($ref_table_dmtres->item_table,$itemfieldnames);
					
					if($childtableres['status'] == 'sucess' AND $post['tax_amount']!="0" AND $post['tax_amount']!=""){

						$taxfield=array('company_id'=>$post['company_id'],'transaction_id'=>$insert_id,'transaction_item_id'=>$post['item_id'][$seqkey],'tax_id'=>$post['tax_id'][$seqkey],'tax_name'=>$post['tax_name'][$seqkey],'amount'=>$post['item_amount'][$seqkey],'tax_amount'=>$post['tax_amount'][$seqkey],'total_amount'=>$post['total_amount'][$seqkey],'tax_value'=>$post['percentage'][$seqkey],'is_active'=>'0','current_status'=>'0','transaction_item_row_id'=>$childtableres['LASTID']);
						// if($tax_flag == "S"){
						// 	$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code),array('tax_name'=>$split_2_name,'tax_percentage'=>$txperc2,'tax_amount'=>$txvalc1,'account_code'=>$split_2_gl_account_code));  
						// 	$taxfield['tax_split_details'] = json_encode($tax_split_details);
						// }else{
						// 	$tax_split_details = array(array('tax_name'=>$split_1_name,'tax_percentage'=>$txperc1,'tax_amount'=>$txvalc1,'account_code'=>$split_1_gl_account_code)); 
						// 	$taxfield['tax_split_details'] = json_encode($tax_split_details);
						// }
						$itax_id2=$callConfig->insertRecord($ref_table_dmtres->tax_table,$taxfield);
					}
					//tax related part Insert
				}
				**//**

				


		
		return $return;
	}
**/
	function getEmailWithDBAndCompanyID($get){
		global $callConfig;
		$user_email = $get['user_email'];
		$aiemailquery = "SELECT id,zqr_connect, email_id, is_active FROM ".MAINDB.".ai_email_configurations WHERE email_id= '".$user_email."' AND is_active =0";
		$aiemailres = $callConfig->getRow($aiemailquery);
		if($aiemailres->is_active !='0'){
			return array(
	            "status" => "error",
	            "msg" => "Email ID is not activated."
	        );
		}elseif($aiemailres->zqr_connect =='0' || $aiemailres->zqr_connect ==''){
			return array(
	            "status" => "error",
	            "msg" => "Database is not assigned. Please contact the administrator."
	        );
		}elseif($aiemailres->id != ''){

			$dbaquery = "SELECT id,db_name FROM ".MAINDB.".".TBL_DATABASE_ALLOCATIONS." WHERE id= '".$aiemailres->zqr_connect."'";
			$dbares = $callConfig->getRow($dbaquery);
			if($dbares->db_name !=''){
				$db_name = $dbares->db_name;
				$returnary = array('company_id'=>'1','user_id'=>'1','db_name'=>$db_name);
				return array(
		            "status" => "success",
		            "msg" => "Record fetched successfully.",
		            "result"=>$returnary
		        );
			}else{
				return array(
		            "status" => "error",
		            "msg" => "Database not found. Please contact the administrator."
		        );
			}
		}else{
			return array(
	            "status" => "error",
	            "msg" => "Oops! Something went wrong. Please contact the administrator."
	        );
		}
	}
	function GETProcurementModulesDashboardSummaryStock($get){
		global $callConfig;
		$return = array();

		// $query = "SELECT id, display_name, company FROM vendors WHERE company_id = ".$get['company_id']." AND is_active = 0";
		// $vendorsres = $callConfig->getAllRows($query);
		// $return['party'] = $vendorsres;
		$limit_row = $get['limit_row']!=""?$get['limit_row']:100;
		$current_page = $get['current_page']!=""?$get['current_page']:0;
		$current_row = $limit_row * $current_page;
		$current_row = ($current_page != 0)?($current_row-1):0;


		$query = "SELECT id, sku, item_name, item_description, sale_price, purchase_uom, hsn_and_sac_code_id, minimum_level, warehouse_id, danger_level, reorder_qty, reorder_days, maximum_capacity FROM items WHERE company_id = ".$get['company_id']." AND is_active = 0 LIMIT ".$current_row.",".$limit_row;
		$itemsres = $callConfig->getAllRows($query);
		$items = array();
		foreach($itemsres as $itm){
			$items[$itm->id] = $itm; 
		}
		$return['items'] = $items;

		$balance_date = $this->dateconvertdbformat(date('Y-m-d'));
		$dmtsql = "SELECT itm.id item_id, itm.sku, itm.item_name, iwb.balance_date, iwb.opening_qty, iwb.in_qty, iwb.out_qty, iwb.closing_qty as balance_qty FROM items itm, item_wa_balances iwb WHERE itm.company_id = '".$get['company_id']."' AND itm.company_id = iwb.company_id AND itm.id = iwb.item_id AND iwb.balance_date = (SELECT MAX(iwb1.balance_date) FROM item_wa_balances iwb1 WHERE itm.company_id = iwb1.company_id AND iwb1.balance_date <= '".$balance_date."' AND itm.id = iwb1.item_id )";
		$dmtres = $callConfig->getAllRows($dmtsql);
		foreach($dmtres as $itm){
			if(isset($items[$itm->item_id])){
				$items[$itm->item_id]->stock_list = $itm; 
			}
		}
		//$return['stock_list'] = $dmtres;

		$sql = "SELECT poi.item_id, poi.item_name, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN poi.item_quantity ELSE 0 END) AS order_completed_qty, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS only_created_qty, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS inprogress_qty FROM purchase_orders po, po_items poi WHERE po.company_id = '".$get['company_id']."' AND po.company_id =poi.company_id AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N'  OR po.cancel_invoice = '') AND po.id = poi.transaction_id AND po.is_active = 0 AND po.is_active = poi.is_active GROUP BY poi.item_id, poi.item_name, po.current_status";
		$sqlres = $callConfig->getAllRows($sql);
		foreach($sqlres as $itm){
			if(isset($items[$itm->item_id])){
				$items[$itm->item_id]->Purchase_orders = $itm; 
			}
		}
		//$return['Purchase_orders'] = $sqlres;

		$sql = "SELECT poi.item_id, poi.item_name, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN poi.item_quantity ELSE 0 END) AS order_completed_qty, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS only_created_qty, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN poi.item_quantity ELSE 0 END) AS inprogress_qty FROM sales_orders po, so_items poi WHERE po.company_id = '".$get['company_id']."' AND po.company_id =poi.company_id AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N'  OR po.cancel_invoice = '') AND po.id = poi.transaction_id AND po.is_active = 0 AND po.is_active = poi.is_active GROUP BY poi.item_id, poi.item_name, po.current_status";
		$sqlres = $callConfig->getAllRows($sql);
		foreach($sqlres as $itm){
			if(isset($items[$itm->item_id])){
				$items[$itm->item_id]->sales_orders = $itm; 
			}
		}
		//$return['sales_orders'] = $sqlres;

		//echo "<pre>";  print_r($return);

		return $return;
	}



	function GETProcurementModulesDashboardSummaryTest($get){
		global $callConfig;
		$return = array();
		$user_id= $get['user_id'];
		$useridcheck = "Y";
		if($useridcheck == 'Y'){
			$notificationswhere = " AND notiusr.user_id = '".$user_id."'";
			$transaction_approval_logswhere = " AND user_id = '".$user_id."'";
			$transaction_activity_schedulewhere = " AND tas.transaction_id = '".$user_id."'";
		}


		$payment_termssql = "SELECT id, payment_term_name, content FROM payment_terms WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$payment_termsres = $callConfig->getAllRows($payment_termssql);
		$payment_terms = array();
		foreach($payment_termsres as $payterm){
			//$conten = json_decode(urldecode($payterm->content),1);
			//$payterm->content = $conten;
			$payment_terms[$payterm->id] = $payterm->payment_term_name;
		}

	


		$paymentssql = "SELECT pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM receipts pay, receipt_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
		$paymentsres = $callConfig->getAllRows($paymentssql);
		// $payment_term_transactions = array();
		$return['customer_payments'] = $paymentsres;

		$sql1 = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."'  AND is_active =0";
		$data1 = $callConfig->getAllRows($sql1);
		$return['application_users'] = $data1;

		$sql = "SELECT notiusr.notification_id, notiusr.notification_type, notiusr.read_status, noti.notification_message, noti.notification_datetime, noti.application_table_name, noti.transaction_number, noti.notification_details FROM notification_user notiusr, notifications noti WHERE noti.company_id = notiusr.company_id ".$notificationswhere." AND noti.id = notiusr.notification_id AND noti.company_id = '".$get['company_id']."' AND notiusr.notification_type = 'ACTION' AND notiusr.read_status =0 ORDER BY noti.notification_datetime DESC";
		$data = $callConfig->getAllRows($sql);
		$return['notifications'] = $data;

		$transaction_approval_logssql = "SELECT transaction_table,transaction_id, response_type,user_id,request_date,response_date FROM transaction_approval_logs WHERE send_back_flag = 0 AND call_back = 1 AND response_type ='0' ".$transaction_approval_logswhere;
		$transaction_approval_logsres=$callConfig->getAllRows($transaction_approval_logssql);
		$return['transaction_approval_logs'] = $transaction_approval_logsres;

		$taskactivitysql = "SELECT tas.id, tas.action_date, tas.task_name FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."'  ".$transaction_activity_schedulewhere." AND tas.is_active = 0";
		$taskactivityres = $callConfig->getAllRows($taskactivitysql);
		$return['transaction_activity_schedule'] = $taskactivityres;
		//credit_limit

		$sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM purchase_invoices po WHERE po.company_id = '".$get['company_id']."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
		$sqlres = $callConfig->getAllRows($sql);
		$party_purchase_invoices = array();
		foreach($sqlres as $pires){
			$party_purchase_invoices[$pires->party_id] = $pires;
		}	
		//$return['purchase_invoices'] = $party_purchase_invoices;

		$sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM sales_invoices po WHERE po.company_id = '".$get['company_id']."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
		$sqlres = $callConfig->getAllRows($sql);
		$party_sales_invoices = array();
		foreach($sqlres as $sires){
			$party_sales_invoices[$pires->party_id] = $sires;
		}
		//$return['sales_invoices'] = $sqlres;

		$vendorssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `bank_account_name`, `bank_name`, `account_type`, `bank_account_no`, `payment_terms` ,`credit_limit` FROM `vendors` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$vendorsres = $callConfig->getAllRows($vendorssql);
		$vendors = array();
		foreach($vendorsres as $vinres){
			$vinres->party_purchase_invoices = isset($party_purchase_invoices[$vinres->id])?$party_purchase_invoices[$vinres->id]:array();
			$vendors[] = $vinres;
		}
		$return['vendors'] = $vendorsres;
		
		$customerssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `payment_terms` ,`credit_limit` FROM `customers` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
		foreach($customersres as $custres){
			$custres->party_sales_invoices = isset($party_sales_invoices[$custres->id])?$party_sales_invoices[$custres->id]:array();
			$customers[] = $custres;
		}
		$return['customers'] = $customers;


		//purchase_invoices list
		$sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM purchase_invoices pi, pi_items pii WHERE pi.company_id = '".$get['company_id']."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
		$res1 = $callConfig->getAllRows($sql1);
		$purchase_invoices = array();
		foreach($res1 as $resr){
			$resr->payments = array();
			if(isset($payment_terms[$resr->payment_terms])){
				$resr->payment_term_name =$payment_terms[$resr->payment_terms];
			}else{
				$resr->payment_term_name =NULL;
			}
			$purchase_invoices[$resr->id] = $resr;
		}
		foreach($res1 as $resr){
			$purchase_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, 'item_id' => $resr->item_id,'item_name' => $resr->item_name); 
		}

		//sales_invoices list
		$sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM sales_invoices pi, si_items pii WHERE pi.company_id = '".$get['company_id']."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
		$res1 = $callConfig->getAllRows($sql1);
		$sales_invoices = array();
		foreach($res1 as $resr){
			$resr->payments = array();
			if(isset($payment_terms[$resr->payment_terms])){
				$resr->payment_term_name =$payment_terms[$resr->payment_terms];
			}else{
				$resr->payment_term_name =NULL;
			}
			$sales_invoices[$resr->id] = $resr;
		}
		foreach($res1 as $resr){
			$sales_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, 'item_id' => $resr->item_id,'item_name' => $resr->item_name); 
		}
		//sales_invoices list

		$payment_terms_transsql = "SELECT id, transaction_table, transaction_id, party_id, due_date, amount, tax_amount, due_amount, pending_amount FROM payment_term_transactions WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND transaction_table IN('sales_invoices','purchase_invoices')";
		$payment_terms_transres = $callConfig->getAllRows($payment_terms_transsql);
		//$payment_term_transactions = array();
		foreach($payment_terms_transres as $paytermtran){
			if($paytermtran->transaction_table == "purchase_invoices"){
				if(isset($purchase_invoices[$paytermtran->transaction_id])){
					$purchase_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran; 
				}
			}
			if($paytermtran->transaction_table == "sales_invoices"){
				if(isset($sales_invoices[$paytermtran->transaction_id])){
					$sales_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran; 
				}
			}
		}
		$paymentssql = "SELECT paya.id, pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM payments pay, payment_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
		$paymentsres1 = $callConfig->getAllRows($paymentssql);
		$vendor_payments = array();
		foreach($paymentsres1 as $payterm){
			if($payterm->transaction_table == "purchase_invoices"){
				if(isset($purchase_invoices[$payterm->transaction_id])){
					$purchase_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm; 
				}
			}
			if($payterm->transaction_table == "sales_invoices"){
				if(isset($sales_invoices[$payterm->transaction_id])){
					$sales_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm; 
				}
			}
		}
		
		$return['purchase_invoices'] = $purchase_invoices;

		$return['sales_invoices'] = $sales_invoices;
		//purchase_invoices list


	echo "<pre>"; print_r($return);
	//return $return;

	}

function GETProcurementModulesDashboardSummary($get){
		global $callConfig;
		$return = array();
		$user_id= $get['user_id'];
		$useridcheck = "Y";
		if($useridcheck == 'Y'){
			$notificationswhere = " AND notiusr.user_id = '".$user_id."'";
			$transaction_approval_logswhere = " AND user_id = '".$user_id."'";
			$transaction_activity_schedulewhere = " AND tas.transaction_id = '".$user_id."'";
		}


		$payment_termssql = "SELECT id, payment_term_name, content FROM payment_terms WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$payment_termsres = $callConfig->getAllRows($payment_termssql);
		$payment_terms = array();
		foreach($payment_termsres as $payterm){
			//$conten = json_decode(urldecode($payterm->content),1);
			//$payterm->content = $conten;
			$payment_terms[$payterm->id] = $payterm->payment_term_name;
		}

		// $paymentssql = "SELECT pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM payments pay, payment_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
		// $paymentsres = $callConfig->getAllRows($paymentssql);
		// // $payment_term_transactions = array();
		// $return['vendor_payments'] = $paymentsres;


		$paymentssql = "SELECT pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM receipts pay, receipt_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.receipt_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
		$paymentsres = $callConfig->getAllRows($paymentssql);
		// $payment_term_transactions = array();
		$return['customer_payments'] = $paymentsres;

		$sql1 = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."'  AND is_active =0";
		$data1 = $callConfig->getAllRows($sql1);
		$return['application_users'] = $data1;

		$sql = "SELECT notiusr.notification_id, notiusr.notification_type, notiusr.read_status, noti.notification_message, noti.notification_datetime, noti.application_table_name, noti.transaction_number, noti.notification_details FROM notification_user notiusr, notifications noti WHERE noti.company_id = notiusr.company_id ".$notificationswhere." AND noti.id = notiusr.notification_id AND noti.company_id = '".$get['company_id']."' AND notiusr.notification_type = 'ACTION' AND notiusr.read_status =0 ORDER BY noti.notification_datetime DESC";
		$data = $callConfig->getAllRows($sql);
		$return['notifications'] = $data;

		$transaction_approval_logssql = "SELECT transaction_table,transaction_id, response_type,user_id,request_date,response_date FROM transaction_approval_logs WHERE send_back_flag = 0 AND call_back = 1 AND response_type ='0' ".$transaction_approval_logswhere;
		$transaction_approval_logsres=$callConfig->getAllRows($transaction_approval_logssql);
		$return['transaction_approval_logs'] = $transaction_approval_logsres;

		$taskactivitysql = "SELECT tas.id, tas.action_date, tas.task_name FROM transaction_activity_schedule tas WHERE tas.company_id = '".$get['company_id']."'  ".$transaction_activity_schedulewhere." AND tas.is_active = 0";
		$taskactivityres = $callConfig->getAllRows($taskactivitysql);
		//print_r($taskactivityres);
		//$return['transaction_activity_schedule_sql'] = $taskactivitysql;
		$return['transaction_activity_schedule'] = $taskactivityres;
		//credit_limit

		$sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM purchase_invoices po WHERE po.company_id = '".$get['company_id']."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
		$sqlres = $callConfig->getAllRows($sql);
		$party_purchase_invoices = array();
		foreach($sqlres as $pires){
			$party_purchase_invoices[$pires->party_id] = $pires;
		}	
		//$return['purchase_invoices'] = $party_purchase_invoices;

		$sql = "SELECT po.party_id, SUM(CASE WHEN po.current_status = ".ST_APPROVED." THEN (po.amount-po.pending_amount) ELSE 0 END) AS inv_pending_amount, SUM(CASE WHEN po.current_status IN (".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS only_created_amount, SUM(CASE WHEN po.current_status NOT IN (".ST_APPROVED.", ".ST_ACTIVE.", ".ST_DRAFT.") THEN (po.amount)  ELSE 0 END) AS inprogress_amount FROM sales_invoices po WHERE po.company_id = '".$get['company_id']."' AND (po.cancel_invoice IS NULL OR po.cancel_invoice = 'N' OR po.cancel_invoice = '') AND po.is_active = 0 GROUP BY po.party_id";
		$sqlres = $callConfig->getAllRows($sql);
		$party_sales_invoices = array();
		foreach($sqlres as $sires){
			$party_sales_invoices[$pires->party_id] = $sires;
		}
		//$return['sales_invoices'] = $sqlres;

		$vendorssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `bank_account_name`, `bank_name`, `account_type`, `bank_account_no`, `payment_terms` ,`credit_limit` FROM `vendors` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$vendorsres = $callConfig->getAllRows($vendorssql);
		$vendors = array();
		foreach($vendorsres as $vinres){
			$vinres->party_purchase_invoices = isset($party_purchase_invoices[$vinres->id])?$party_purchase_invoices[$vinres->id]:array();
			$vendors[] = $vinres;
		}
		$return['vendors'] = $vendorsres;
		
		$customerssql = "SELECT `id`, `company`, `display_name`, `billing_address`, `billing_city`, `billing_state`, `billing_post_code`, `billing_country`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_post_code`, `shipping_country`, `payment_terms` ,`credit_limit` FROM `customers` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
		foreach($customersres as $custres){
			$custres->party_sales_invoices = isset($party_sales_invoices[$custres->id])?$party_sales_invoices[$custres->id]:array();
			$customers[] = $custres;
		}
		$return['customers'] = $customers;



		//purchase_invoices list
		$sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM purchase_invoices pi, pi_items pii WHERE pi.company_id = '".$get['company_id']."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
		$res1 = $callConfig->getAllRows($sql1);
		$purchase_invoices = array();
		foreach($res1 as $resr){
			$resr->payments = array();
			if(isset($payment_terms[$resr->payment_terms])){
				$resr->payment_term_name =$payment_terms[$resr->payment_terms];
			}else{
				$resr->payment_term_name =NULL;
			}
			$purchase_invoices[$resr->id] = $resr;
		}
		foreach($res1 as $resr){
			$purchase_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, 'item_id' => $resr->item_id,'item_name' => $resr->item_name); 
		}

		//sales_invoices list
		$sql1 = "SELECT pi.id, pi.transaction_number, pi.transaction_date, pi.party_id, pi.payment_terms, pii.id pi_id, pii.item_id, pii.item_name FROM sales_invoices pi, si_items pii WHERE pi.company_id = '".$get['company_id']."' AND pi.current_status = ".ST_APPROVED." AND  pi.id = pii.transaction_id AND  pi.is_active = 0 AND  pi.is_active = pii.is_active";
		$res1 = $callConfig->getAllRows($sql1);
		$sales_invoices = array();
		foreach($res1 as $resr){
			$resr->payments = array();
			if(isset($payment_terms[$resr->payment_terms])){
				$resr->payment_term_name =$payment_terms[$resr->payment_terms];
			}else{
				$resr->payment_term_name =NULL;
			}
			$sales_invoices[$resr->id] = $resr;
		}
		foreach($res1 as $resr){
			$sales_invoices[$resr->id]->items[$resr->pi_id] = array('id' => $resr->pi_id, 'item_id' => $resr->item_id,'item_name' => $resr->item_name); 
		}
		//sales_invoices list

		$payment_terms_transsql = "SELECT id, transaction_table, transaction_id, party_id, due_date, amount, tax_amount, due_amount, pending_amount, advance_offer_date, advance_offer_amount FROM payment_term_transactions WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND transaction_table IN('sales_invoices','purchase_invoices')";
		$payment_terms_transres = $callConfig->getAllRows($payment_terms_transsql);
		//$payment_term_transactions = array();
		foreach($payment_terms_transres as $paytermtran){
			if($paytermtran->transaction_table == "purchase_invoices"){
				if(isset($purchase_invoices[$paytermtran->transaction_id])){
					$purchase_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran; 
				}
			}
			if($paytermtran->transaction_table == "sales_invoices"){
				if(isset($sales_invoices[$paytermtran->transaction_id])){
					$sales_invoices[$paytermtran->transaction_id]->payment_term_transactions[$paytermtran->id] =  $paytermtran; 
				}
			}
		}
		$paymentssql = "SELECT paya.id, pay.transaction_type, pay.payment_date, paya.party_id, paya.transaction_table, paya.transaction_id, paya.transaction_no, paya.total_paid_amonut  FROM payments pay, payment_allocations paya WHERE pay.company_id = 1 AND pay.company_id = paya.company_id AND pay.id = paya.payment_id AND pay.is_active = 0 AND pay.current_status = 199 AND paya.transaction_table IN('sales_invoices','purchase_invoices')";
		$paymentsres1 = $callConfig->getAllRows($paymentssql);
		$vendor_payments = array();
		foreach($paymentsres1 as $payterm){
			if($payterm->transaction_table == "purchase_invoices"){
				if(isset($purchase_invoices[$payterm->transaction_id])){
					$purchase_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm; 
				}
			}
			if($payterm->transaction_table == "sales_invoices"){
				if(isset($sales_invoices[$payterm->transaction_id])){
					$sales_invoices[$payterm->transaction_id]->payments[$payterm->id] =  $payterm; 
				}
			}
		}
		
		$return['purchase_invoices'] = $purchase_invoices;

		$return['sales_invoices'] = $sales_invoices;
		//purchase_invoices list
		
		//echo "<pre>"; print_r($return);
		return $return;

	}


//profit and loss report
function calculateBalances($parentCode, $map, &$total, $type){
    if(empty($map[$parentCode])) return;

    foreach($map[$parentCode] as $coa){
        $code = $coa['account_code'];
        $bal  = $coa['closing_balance'];

        $total[$code][$type] = ($total[$code][$type] ?? 0) + $bal;

        // add to parent chain
        if($parentCode != 0){
            $total[$parentCode][$type] = ($total[$parentCode][$type] ?? 0) + $bal;
        }

        // recursive call
        $this->calculateBalances($code, $map, $total, $type);
    }
}
function processBalances($balances, &$total, $type){
    $map = [];

    foreach($balances as $row){
        $map[$row['parent_account_code']][] = $row;
    }

    $this->calculateBalances(0, $map, $total, $type);
}
function GETProcurementModulesDashboardSummaryProfitLoass($get){

    $get['start_date'] = $this->dateconvertdbformat($get['start_date']);
    $get['end_date']   = $this->dateconvertdbformat($get['end_date']);

    $get['prev_start_date'] = date('Y-m-d', strtotime('-1 year', strtotime($get['start_date'])));
    $get['prev_end_date']   = date('Y-m-d', strtotime('-1 year', strtotime($get['end_date'])));

    include_once("account_user.class.php");
	$accountUserClassonobj = new accountUserClass();

	$result1 = $accountUserClassonobj->getProfitAndLossReports($get);
	$result = json_decode(json_encode($result1), true);

    $totalcoa = [];

    // 🔥 Just 4 calls instead of 300+ lines
    $this->processBalances($result['start_balances'], $totalcoa, 'start_balance');
    $this->processBalances($result['end_balances'], $totalcoa, 'end_balance');
    $this->processBalances($result['prev_start_balances'], $totalcoa, 'prev_start_balance');
    $this->processBalances($result['prev_end_balances'], $totalcoa, 'prev_end_balance');

    // 🔹 Calculations (same logic, cleaner)
    $calc = function($code, $type1, $type2) use ($totalcoa){
        return ($totalcoa[$code][$type1] ?? 0) - ($totalcoa[$code][$type2] ?? 0);
    };

    return [
        "revenue" => $calc('50002700','end_balance','start_balance'),
        "other_income" => $calc('40002000','end_balance','start_balance'),
        "total_income" => $calc('50002700','end_balance','start_balance') + $calc('40002000','end_balance','start_balance'),

        "material" => $calc('50000100','end_balance','start_balance'),
        "employee_benefits_expense" => $calc('50002100','end_balance','start_balance'),
        "finance_costs" => $calc('50003500','end_balance','start_balance'),
        "depreciation_and_amortisation" => $calc('50003600','end_balance','start_balance'),

        "current_tax" => $calc('50003451','end_balance','start_balance'),
        "deferred_tax" => 
            $calc('50003452','end_balance','start_balance') +
            $calc('50003453','end_balance','start_balance'),

        // previous
        "prev_revenue" => $calc('50002700','prev_end_balance','prev_start_balance'),
        "prev_other_income" => $calc('40002000','prev_end_balance','prev_start_balance'),
        "prev_total_income" => $calc('50002700','prev_end_balance','prev_start_balance') + $calc('40002000','prev_end_balance','prev_start_balance'),

        "prev_material" => $calc('50000100','prev_end_balance','prev_start_balance'),
        "prev_employee_benefits_expense" => $calc('50002100','prev_end_balance','prev_start_balance'),
        "prev_finance_costs" => $calc('50003500','prev_end_balance','prev_start_balance'),
        "prev_depreciation_and_amortisation" => $calc('50003600','prev_end_balance','prev_start_balance'),

        "prev_current_tax" => $calc('50003451','prev_end_balance','prev_start_balance'),
        "prev_deferred_tax" => 
            $calc('50003452','prev_end_balance','prev_start_balance') +
            $calc('50003453','prev_end_balance','prev_start_balance'),
    ];
}
//profit and loss report




//start Balance Sheet
function GETProcurementModulesDashboardSummaryBalanceSheets($get){

    // STEP 1: Get data (your existing function)
	$get['balance_date'] = date('Y-m-d',strtotime($get['start_date']));
    $get['prev_balance_date']   = date('Y-m-d',strtotime($get['end_date']));

	include_once("account_user.class.php");
	$accountUserClassonobj = new accountUserClass();
    $result1 = $accountUserClassonobj->getBalanceSheetReport($get);
 	$result = json_decode(json_encode($result1), true);

    $balances = $result['balances'];
	$parentcoa = $parentchildacount = array();
	foreach($balances as $coa){
		if($coa['parent_account_code'] == 0){
			$parentcoa[] = $coa;
		}
		$parentchildacount[$coa['parent_account_code']][] = $coa;
	}
	$parentchildaccount = $totalcoa = $parentlist =  array();

	foreach($parentcoa as $pcoa1){
		$parentchildaccount[$pcoa1['account_code']][] = $pcoa1;
		$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa1['closing_balance'];
		foreach($parentchildacount[$pcoa1['account_code']] as $pcoa2){
			$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
			$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];		
			foreach($parentchildacount[$pcoa2['account_code']] as $pcoa3){
				$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;			
				$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($parentchildacount[$pcoa3['account_code']] as $pcoa4){
					$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;
					
					$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($parentchildacount[$pcoa4['account_code']] as $pcoa5){
						$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;
						
						$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($parentchildacount[$pcoa5['account_code']] as $pcoa6){
							$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
							$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}
	$sharecapital = ($totalcoa['30000210']['start_balance']);
	$reservesandsurplus = ($totalcoa['30000250']['start_balance']);
	$moneyreceivedagainstsharewarrants = ($totalcoa['30000400']['start_balance']);

	$longtermborrowings = ($totalcoa['20001100']['start_balance']);
	$deferredtaxliabilitiess = ($totalcoa['2002200']['start_balance']);
	$otherlongtermliabilities = ($totalcoa['2002300']['start_balance']);
	$longtermprovisions	= ($totalcoa['2002400']['start_balance']);

	$shorttermborrowings = ($totalcoa['2003100']['start_balance']);
	$tradepayables = ($totalcoa['20003500']['start_balance']);
	$othercurrentliabilities = ($totalcoa['2004000']['start_balance']);
	$shorttermprovisions = ($totalcoa['2004300']['start_balance']);

	$propertyplantequipment= ($totalcoa['10001100']['start_balance']); 
	$intangibleassets= ($totalcoa['10001400']['start_balance']+$totalcoa['10001500']['start_balance']+$totalcoa['10001700']['start_balance']);
	$capitalworkinprogress= ($totalcoa['10001600']['start_balance']!=""?$totalcoa['10001600']['start_balance']:0);
	$intangibleassetsunderdevelopment= ($totalcoa['10001700']['start_balance'] !=""?$totalcoa['10001700']['start_balance']:0);

	$noncurrentinvestments= ($totalcoa['10001800']['start_balance']!=""?$totalcoa['10001800']['start_balance']:0);
	$deferredtaxassets= ($totalcoa['10002400']['start_balance']!=""?$totalcoa['10002400']['start_balance']:0);
	$longtermloansandadvances= ($totalcoa['10002300']['start_balance']!=""?$totalcoa['10002300']['start_balance']:0);
	$othernoncurrentassets= ($totalcoa['10002100']['start_balance']!=""?$totalcoa['10002100']['start_balance']:0);
	$currentinvestments= ($totalcoa['10003100']['start_balance']!=""?$totalcoa['10003100']['start_balance']:0);
	$inventories= ($totalcoa['10003400']['start_balance']!=""?$totalcoa['10003400']['start_balance']:0);
	$tradereceivables= ($totalcoa['10003900']['start_balance']!=""?$totalcoa['10003900']['start_balance']:0);
	$cashandbankbalances= ($totalcoa['10005000']['start_balance']!=""?$totalcoa['10005000']['start_balance']:0);
	$shorttermloansandadvances= ($totalcoa['10006000']['start_balance']!=""?$totalcoa['10006000']['start_balance']:0);
	$othercurrentassets= ($totalcoa['10008000']['start_balance']!=""?$totalcoa['10008000']['start_balance']:0);






	$prev_balances = $result['prev_balances'];
	$prev_parentcoa = $prev_parentchildacount = array();
	foreach($prev_balances as $coa){
		if($coa['parent_account_code'] == 0){
			$prev_parentcoa[] = $coa;
		}
		$prev_parentchildacount[$coa['parent_account_code']][] = $coa;
	}
	$parentchildaccount = $totalcoa = $parentlist =  array();
	foreach($prev_parentcoa as $pcoa1){
		$parentchildaccount[$pcoa1['account_code']][] = $pcoa1;
		$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa1['closing_balance'];
		foreach($prev_parentchildacount[$pcoa1['account_code']] as $pcoa2){
			$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
			$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa2['closing_balance'];		
			foreach($prev_parentchildacount[$pcoa2['account_code']] as $pcoa3){
				$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;			
				$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($prev_parentchildacount[$pcoa3['account_code']] as $pcoa4){
					$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;
					
					$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($prev_parentchildacount[$pcoa4['account_code']] as $pcoa5){
						$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;
						
						$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($prev_parentchildacount[$pcoa5['account_code']] as $pcoa6){
							$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
							$totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}
	//Start preview Balances 
	$pre_sharecapital = ($totalcoa['30000210']['pre_start_balance']);
	$pre_reservesandsurplus = ($totalcoa['30000250']['pre_start_balance']);
	$pre_moneyreceivedagainstsharewarrants = ($totalcoa['30000400']['pre_start_balance']);

	$pre_longtermborrowings = ($totalcoa['20001100']['pre_start_balance']);
	$pre_deferredtaxliabilitiess = ($totalcoa['2002200']['pre_start_balance']);
	$pre_otherlongtermliabilities = ($totalcoa['2002300']['pre_start_balance']);
	$pre_longtermprovisions	= ($totalcoa['2002400']['pre_start_balance']);

	$pre_shorttermborrowings = ($totalcoa['2003100']['pre_start_balance']);
	$pre_tradepayables = ($totalcoa['20003500']['pre_start_balance']);
	$pre_othercurrentliabilities = ($totalcoa['2004000']['pre_start_balance']);
	$pre_shorttermprovisions = ($totalcoa['2004300']['pre_start_balance']);

	$pre_propertyplantequipment= ($totalcoa['10001100']['pre_start_balance']); 
	$pre_intangibleassets= ($totalcoa['10001400']['pre_start_balance']+$totalcoa['10001500']['pre_start_balance']+$totalcoa['10001700']['pre_start_balance']);
	$pre_capitalworkinprogress= ($totalcoa['10001600']['pre_start_balance']!=""?$totalcoa['10001600']['pre_start_balance']:0);
	$pre_intangibleassetsunderdevelopment= ($totalcoa['10001700']['pre_start_balance'] !=""?$totalcoa['10001700']['pre_start_balance']:0);

	$pre_noncurrentinvestments= ($totalcoa['10001800']['pre_start_balance']!=""?$totalcoa['10001800']['pre_start_balance']:0);
	$pre_deferredtaxassets= ($totalcoa['10002400']['pre_start_balance']!=""?$totalcoa['10002400']['pre_start_balance']:0);
	$pre_longtermloansandadvances= ($totalcoa['10002300']['pre_start_balance']!=""?$totalcoa['10002300']['pre_start_balance']:0);
	$pre_othernoncurrentassets= ($totalcoa['10002100']['pre_start_balance']!=""?$totalcoa['10002100']['pre_start_balance']:0);
	$pre_currentinvestments= ($totalcoa['10003100']['pre_start_balance']!=""?$totalcoa['10003100']['pre_start_balance']:0);
	$pre_inventories= ($totalcoa['10003400']['pre_start_balance']!=""?$totalcoa['10003400']['pre_start_balance']:0);
	$pre_tradereceivables= ($totalcoa['10003900']['pre_start_balance']!=""?$totalcoa['10003900']['pre_start_balance']:0);
	$pre_cashandbankbalances= ($totalcoa['10005000']['pre_start_balance']!=""?$totalcoa['10005000']['pre_start_balance']:0);
	$pre_shorttermloansandadvances= ($totalcoa['10006000']['pre_start_balance']!=""?$totalcoa['10006000']['pre_start_balance']:0);
	$pre_othercurrentassets= ($totalcoa['10008000']['pre_start_balance']!=""?$totalcoa['10008000']['pre_start_balance']:0);
	//End preview Balances 

	$retarray = [
        "equity_liabilities" => [
            "shareholders_funds" => [
                "share_capital" => [
                    "current" => $sharecapital,
                    "previous" => $pre_sharecapital,
                ],
                "reserves_surplus" => [
                    "current" => $reservesandsurplus,
                    "previous" => $pre_reservesandsurplus
                ],
                "Money_received_against_share_warrants" => [
                    "current" => $moneyreceivedagainstsharewarrants,
                    "previous" => $pre_moneyreceivedagainstsharewarrants
                ]
            ],
            "non_current_liabilities" => [
                "long_term_borrowings" => [
                    "current" => $longtermborrowings,
                    "previous" => $pre_longtermborrowings
                ],
                "deferred_tax_liabilities" => [
                    "current" => $deferredtaxliabilitiess,
                    "previous" => $pre_deferredtaxliabilitiess
                ],
                "other_long_term_liabilities" => [
                    "current" => $otherlongtermliabilities,
                    "previous" => $pre_otherlongtermliabilities
                ],
                "long_term_provisions" => [
                    "current" => $longtermprovisions,
                    "previous" => $pre_longtermprovisions
                ]
            ],

            "current_liabilities" => [
                "short_term_borrowings" => [
                    "current" => $shorttermborrowings,
                    "previous" => $pre_shorttermborrowings
                ],
                "trade_payables" => [
                    "current" => $tradepayables,
                    "previous" => $pre_tradepayables
                ],
                "other_current_liabilities" => [
                    "current" => $othercurrentliabilities,
                    "previous" => $pre_othercurrentliabilities
                ],
                "short_term_provisions" => [
                    "current" => $shorttermprovisions,
                    "previous" => $pre_shorttermprovisions
                ]
            ]
        ],

        "assets" => [

            "non_current_assets" => [
                "property_plant_equipment" => [
                    "current" => $propertyplantequipment,
                    "previous" => $pre_propertyplantequipment
                ],
                "intangible_assets" => [
                    "current" => $intangibleassets,
                    "previous" => $pre_intangibleassets
                    
                ],
                "capital_work_in_progress" => [
                    "current" => $capitalworkinprogress,
                    "previous" => $pre_capitalworkinprogress
                ],
                "intangible_under_dev" => [
                    "current" => $intangibleassetsunderdevelopment,
                    "previous" => $pre_intangibleassetsunderdevelopment
                ],
                "non_current_investments" => [
                    "current" => $noncurrentinvestments,
                    "previous" => $noncurrentinvestments
                ],
                "deferred_tax_assets" => [
                    "current" => $deferredtaxassets,
                    "previous" => $pre_deferredtaxassets
                ],
                "long_term_loans" => [
                    "current" => $longtermloansandadvances,
                    "previous" => $pre_longtermloansandadvances
                ],
                "other_non_current_assets" => [
                    "current" => $othernoncurrentassets,
                    "previous" => $pre_othernoncurrentassets
                ]
            ],

            "current_assets" => [
                "current_investments" => [
                    "current" => $currentinvestments,
                    "previous" => $pre_currentinvestments
                ],
                "inventories" => [
                    "current" => $inventories,
                    "previous" => $pre_inventories
                ],
                "trade_receivables" => [
                   "current" => $tradereceivables,
                    "previous" => $pre_tradereceivables
                ],
                "cash_bank_balance" => [
                    "current" => $cashandbankbalances,
                    "previous" => $pre_cashandbankbalances
                ],
                "short_term_loans" => [
                    "current" => $shorttermloansandadvances,
                    "previous" => $pre_shorttermloansandadvances
                ],
                "other_current_assets" => [
                    "current" => $othercurrentassets,
                    "previous" => $pre_othercurrentassets
                ]
            ]
        ]
    ];

    return $retarray;
}

/**
function GETProcurementModulesDashboardSummaryBalanceSheets($get){

    // STEP 1: Get data (your existing function)
	$get['balance_date'] = date('Y-m-d',strtotime($get['start_date']));
    $get['prev_balance_date']   = date('Y-m-d',strtotime($get['end_date']));

	include_once("account_user.class.php");
	$accountUserClassonobj = new accountUserClass();
    $result = $accountUserClassonobj->getBalanceSheetReport($get);
 
  //  $balances = $result['balances'];
    //$prev_balances = $result['prev_balances'];
    $balances = json_decode(json_encode($result['balances']), true);
$prev_balances = json_decode(json_encode($result['prev_balances']), true);
    // STEP 2: Recursive total calculator
    $calculateTotals = function($balances){
        $children = [];
        $totals = [];
        foreach ($balances as $row) {
            $parent = $row['parent_account_code'] ?? 0;
            $children[$parent][] = $row;
        }
        $calc = function($parent) use (&$calc, &$children, &$totals) {
            $sum = 0;
            if (!empty($children[$parent])) {
                foreach ($children[$parent] as $child) {

                    $code = $child['account_code'];
                    $self = $child['closing_balance'] ?? 0;
                    $childSum = $calc($code);
                    $total = $self + $childSum;
                    $totals[$code] = ($totals[$code] ?? 0) + $total;
                    $sum += $total;
                }
            }
            return $sum;
        };
        $calc(0);
        return $totals;
    };

    // STEP 3: Get totals
    $currentTotals = $calculateTotals($balances);
    $prevTotals    = $calculateTotals($prev_balances);

    // Safe getter
    $val = function($arr, $key){
        return isset($arr[$key]) ? $arr[$key] : 0;
    };
    // STEP 4: Final ARRAY (same as your HTML structure)
    return [
        "equity_liabilities" => [
            "shareholders_funds" => [
                "share_capital" => [
                    "current" => $val($currentTotals,'30000210'),
                    "previous" => $val($prevTotals,'30000210')
                ],
                "reserves_surplus" => [
                    "current" => $val($currentTotals,'30000250'),
                    "previous" => $val($prevTotals,'30000250')
                ],
                "share_warrants" => [
                    "current" => $val($currentTotals,'30000400'),
                    "previous" => $val($prevTotals,'30000400')
                ]
            ],
            "non_current_liabilities" => [
                "long_term_borrowings" => [
                    "current" => $val($currentTotals,'20001100'),
                    "previous" => $val($prevTotals,'20001100')
                ],
                "deferred_tax_liabilities" => [
                    "current" => $val($currentTotals,'2002200'),
                    "previous" => $val($prevTotals,'2002200')
                ],
                "other_long_term_liabilities" => [
                    "current" => $val($currentTotals,'2002300'),
                    "previous" => $val($prevTotals,'2002300')
                ],
                "long_term_provisions" => [
                    "current" => $val($currentTotals,'2002400'),
                    "previous" => $val($prevTotals,'2002400')
                ]
            ],

            "current_liabilities" => [
                "short_term_borrowings" => [
                    "current" => $val($currentTotals,'2003100'),
                    "previous" => $val($prevTotals,'2003100')
                ],
                "trade_payables" => [
                    "current" => $val($currentTotals,'20003500'),
                    "previous" => $val($prevTotals,'20003500')
                ],
                "other_current_liabilities" => [
                    "current" => $val($currentTotals,'2004000'),
                    "previous" => $val($prevTotals,'2004000')
                ],
                "short_term_provisions" => [
                    "current" => $val($currentTotals,'2004300'),
                    "previous" => $val($prevTotals,'2004300')
                ]
            ]
        ],

        "assets" => [

            "non_current_assets" => [
                "property_plant_equipment" => [
                    "current" => $val($currentTotals,'10001100'),
                    "previous" => $val($prevTotals,'10001100')
                ],
                "intangible_assets" => [
                    "current" => 
                        $val($currentTotals,'10001400') +
                        $val($currentTotals,'10001500') +
                        $val($currentTotals,'10001700')
                    ,
                    "previous" => 
                        $val($prevTotals,'10001400') +
                        $val($prevTotals,'10001500') +
                        $val($prevTotals,'10001700')
                    
                ],
                "capital_work_in_progress" => [
                    "current" => $val($currentTotals,'10001600'),
                    "previous" => $val($prevTotals,'10001600')
                ],
                "intangible_under_dev" => [
                    "current" => $val($currentTotals,'10001700'),
                    "previous" => $val($prevTotals,'10001700')
                ],
                "non_current_investments" => [
                    "current" => $val($currentTotals,'10001800'),
                    "previous" => $val($prevTotals,'10001800')
                ],
                "deferred_tax_assets" => [
                    "current" => $val($currentTotals,'10002400'),
                    "previous" => $val($prevTotals,'10002400')
                ],
                "long_term_loans" => [
                    "current" => $val($currentTotals,'10002300'),
                    "previous" => $val($prevTotals,'10002300')
                ],
                "other_non_current_assets" => [
                    "current" => $val($currentTotals,'10002100'),
                    "previous" => $val($prevTotals,'10002100')
                ]
            ],

            "current_assets" => [
                "current_investments" => [
                    "current" => $val($currentTotals,'10003100'),
                    "previous" => $val($prevTotals,'10003100')
                ],
                "inventories" => [
                    "current" => $val($currentTotals,'10003400'),
                    "previous" => $val($prevTotals,'10003400')
                ],
                "trade_receivables" => [
                    "current" => $val($currentTotals,'10003900'),
                    "previous" => $val($prevTotals,'10003900')
                ],
                "cash_bank_balance" => [
                    "current" => $val($currentTotals,'10005000'),
                    "previous" => $val($prevTotals,'10005000')
                ],
                "short_term_loans" => [
                    "current" => $val($currentTotals,'10006000'),
                    "previous" => $val($prevTotals,'10006000')
                ],
                "other_current_assets" => [
                    "current" => $val($currentTotals,'10008000'),
                    "previous" => $val($prevTotals,'10008000')
                ]
            ]
        ]
    ];
}

**/

//End balance Sheets



//Start Cash Flow

function GETProcurementModulesDashboardSummaryCashFlow($get){
    
    include_once("account_user.class.php");
    $obj = new accountUserClass();

    $get['start_date'] = $get['start_date'] !=""?$get['start_date']:date('Y-m-01');
    $get['end_date']   = $get['end_date'] !=""?$get['end_date']:date('Y-m-t');
    $get['prev_start_date'] = date('Y-m-d', strtotime('-1 month', strtotime($get['start_date'])));
    $get['prev_end_date']   = date('Y-m-d', strtotime('-1 month', strtotime($get['end_date'])));
	//$data = getCashFlowArray($get);
    $result1 = $obj->getcashflowreports($get);
    $result = json_decode(json_encode($result1), true);
    
    $start_balances = $result['start_balances'];
	$parentcoa = $parentchildacount = array();
	foreach($start_balances as $coa){
		if($coa['parent_account_code'] == 0){
			$parentcoa[] = $coa;
		}
		$parentchildacount[$coa['parent_account_code']][] = $coa;
	}
	$parentchildaccount = $totalcoa = $parentlist =  array();
	foreach($parentcoa as $pcoa1){
		$parentchildaccount[$pcoa1['account_code']][] = $pcoa1;
		$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa1['closing_balance'];
		foreach($parentchildacount[$pcoa1['account_code']] as $pcoa2){
			$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
			$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];		
			foreach($parentchildacount[$pcoa2['account_code']] as $pcoa3){
				$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;			
				$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($parentchildacount[$pcoa3['account_code']] as $pcoa4){
					$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;
					
					$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($parentchildacount[$pcoa4['account_code']] as $pcoa5){
						$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;
						
						$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($parentchildacount[$pcoa5['account_code']] as $pcoa6){
							$parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
							$totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}

	//End bAlances
	$end_balances = $result['end_balances'];
	$endparentcoa = $endparentchildacount = array();
	foreach($end_balances as $coa){
		if($coa['parent_account_code'] == 0){
			$endparentcoa[] = $coa;
		}
		$endparentchildacount[$coa['parent_account_code']][] = $coa;
	}
	$endparentchildaccount =  $parentlist =  array();
	foreach($endparentcoa as $pcoa1){
		$endparentchildaccount[$pcoa1['account_code']][] = $pcoa1;
		$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa1['closing_balance'];
		foreach($endparentchildacount[$pcoa1['account_code']] as $pcoa2){
			$endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
			$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa2['closing_balance'];		
			foreach($endparentchildacount[$pcoa2['account_code']] as $pcoa3){
				$endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;			
				$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($endparentchildacount[$pcoa3['account_code']] as $pcoa4){
					$endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;
					
					$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($endparentchildacount[$pcoa4['account_code']] as $pcoa5){
						$endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;
						
						$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($endparentchildacount[$pcoa5['account_code']] as $pcoa6){
							$endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
							$totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}

	$depreciation = (($totalcoa['10001300']['end_balance']+$totalcoa['10001500']['end_balance']+$totalcoa['50003600']['end_balance'])-($totalcoa['10001300']['start_balance']+$totalcoa['10001500']['start_balance']+$totalcoa['50003600']['start_balance']));
	$baddebtswrittenoff = (($totalcoa['1004200']['end_balance']+$totalcoa['50003300']['end_balance'])-($totalcoa['1004200']['start_balance']+$totalcoa['50003300']['start_balance']));

	$tradereceivables = (($totalcoa['10003900']['end_balance'])-($totalcoa['10003900']['start_balance']));

	$shorttermloansandadvances = (($totalcoa['10006000']['end_balance'])-($totalcoa['10006000']['start_balance']));

	$longtermloansandadvances = (($totalcoa['10002300']['end_balance'])-($totalcoa['10002300']['start_balance']));

	$othercurrentassets  = (($totalcoa['10008000']['end_balance'])-($totalcoa['10008000']['start_balance']));
	$tradepayables  = (($totalcoa['20003500']['end_balance'])-($totalcoa['20003500']['start_balance']));

	$decriesincreaseinothercurrentliabilities = (($totalcoa['2004000']['end_balance'])-($totalcoa['2004000']['start_balance']));

	$incometaxrefundpaid = (($totalcoa['2004401']['end_balance']+$totalcoa['10002903']['end_balance'])-($totalcoa['2004401']['start_balance']+$totalcoa['10002903']['start_balance']));
	 

	$purchaseofpropertyplantequipmentandintangibleassets = "-";

	$proceedsfromsaleofproperty = "-";

	$netproceedsfromissueofshares = "-";

	$netproceedsfromlongtermborrowings = (($totalcoa['20001100']['end_balance'])-($totalcoa['20001100']['start_balance']));

	$netproceedsfromshorttermborrowings = (($totalcoa['2003100']['end_balance'])-($totalcoa['2003100']['start_balance']));



	$cashandcashequivalentsduringtheyear = (($totalcoa['10005000']['end_balance'])-($totalcoa['10005000']['start_balance']));


	$cashequivalentsatthebeginningoftheyear =($totalcoa['10005000']['start_balance']);
	$cashequivalentsattheendoftheyear =($totalcoa['10005000']['end_balance']);






	///////////////////////////////////  previous year data ///////////////////////////////////////////////



	$prevstart_balances = $result['prev_start_balances'];
	$prev_parentcoa = $prev_parentchildacount = array();
	foreach($prevstart_balances as $coa){
		if($coa['parent_account_code'] == 0){
			$prev_parentcoa[] = $coa;
		}
		$prev_parentchildacount[$coa['parent_account_code']][] = $coa;
	}
	$parentlist =  array();
	foreach($prev_parentcoa as $pcoa1){
		$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa1['closing_balance'];
		foreach($prev_parentchildacount[$pcoa1['account_code']] as $pcoa2){
			$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['prev_start_balance']  +=  $pcoa2['closing_balance'];		
			foreach($prev_parentchildacount[$pcoa2['account_code']] as $pcoa3){
				$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['prev_start_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['prev_start_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($prev_parentchildacount[$pcoa3['account_code']] as $pcoa4){
					$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['prev_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['prev_start_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['prev_start_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($prev_parentchildacount[$pcoa4['account_code']] as $pcoa5){
						$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['prev_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['prev_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['prev_start_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['prev_start_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($prev_parentchildacount[$pcoa5['account_code']] as $pcoa6){
							$totalcoa[$pcoa1['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['prev_start_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}

	//End bAlances
	$prev_end_balances = $result['prev_end_balances'];
	$prevend_balances = $pre_endparentchildacount = array();
	foreach($prev_end_balances as $coa){
		if($coa['parent_account_code'] == 0){
			$prevend_balances[] = $coa;
		}
		$pre_endparentchildacount[$coa['parent_account_code']][] = $coa;
	}
	foreach($prevend_balances as $pcoa1){
		$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa1['closing_balance'];
		foreach($pre_endparentchildacount[$pcoa1['account_code']] as $pcoa2){
			$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa2['closing_balance'];
			$totalcoa[$pcoa2['account_code']]['prev_end_balance']  +=  $pcoa2['closing_balance'];		
			foreach($pre_endparentchildacount[$pcoa2['account_code']] as $pcoa3){
				$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa2['account_code']]['prev_end_balance']  +=  $pcoa3['closing_balance'];
				$totalcoa[$pcoa3['account_code']]['prev_end_balance']  +=  $pcoa3['closing_balance'];			
				//echo "level 3 - ".$pcoa3['account_code']."</br>";
				foreach($pre_endparentchildacount[$pcoa3['account_code']] as $pcoa4){
					$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa2['account_code']]['prev_end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa3['account_code']]['prev_end_balance']  +=  $pcoa4['closing_balance'];
					$totalcoa[$pcoa4['account_code']]['prev_end_balance']  +=  $pcoa4['closing_balance'];
					//echo "level 4 - ".$pcoa4['account_code']."</br>";
					foreach($pre_endparentchildacount[$pcoa4['account_code']] as $pcoa5){
						$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa2['account_code']]['prev_end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa3['account_code']]['prev_end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa4['account_code']]['prev_end_balance']  +=  $pcoa5['closing_balance'];
						$totalcoa[$pcoa5['account_code']]['prev_end_balance']  +=  $pcoa5['closing_balance'];
						//echo "level 5 - ".$pcoa5['account_code']."</br>";
						foreach($pre_endparentchildacount[$pcoa5['account_code']] as $pcoa6){
							$totalcoa[$pcoa1['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa2['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa3['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa4['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa5['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							$totalcoa[$pcoa6['account_code']]['prev_end_balance']  +=  $pcoa6['closing_balance'];
							//echo "level 6 - ".$pcoa6['account_code']."</br>";
						}
					}
				}
			}
		}
	}
	$prev_depreciation = (($totalcoa['10001300']['prev_end_balance']+$totalcoa['10001500']['prev_end_balance']+$totalcoa['50003600']['prev_end_balance'])-($totalcoa['10001300']['prev_start_balance']+$totalcoa['10001500']['prev_start_balance']+$totalcoa['50003600']['prev_start_balance']));
	$prev_baddebtswrittenoff = (($totalcoa['1004200']['prev_end_balance']+$totalcoa['50003300']['prev_end_balance'])-($totalcoa['1004200']['prev_start_balance']+$totalcoa['50003300']['prev_start_balance']));
	$prev_tradereceivables = (($totalcoa['10003900']['prev_end_balance'])-($totalcoa['10003900']['prev_start_balance']));
	$prev_shorttermloansandadvances = (($totalcoa['10006000']['prev_end_balance'])-($totalcoa['10006000']['prev_start_balance']));
	$prev_longtermloansandadvances = (($totalcoa['10002300']['prev_end_balance'])-($totalcoa['10002300']['prev_start_balance']));
	$prev_othercurrentassets  = (($totalcoa['10008000']['prev_end_balance'])-($totalcoa['10008000']['prev_start_balance']));
	$prev_tradepayables  = (($totalcoa['20003500']['prev_end_balance'])-($totalcoa['20003500']['prev_start_balance']));
	$prev_decriesincreaseinothercurrentliabilities = (($totalcoa['2004000']['prev_end_balance'])-($totalcoa['2004000']['prev_start_balance']));
	$prev_incometaxrefundpaid = (($totalcoa['2004401']['prev_end_balance']+$totalcoa['10002903']['prev_end_balance'])-($totalcoa['2004401']['prev_start_balance']+$totalcoa['10002903']['prev_start_balance']));
	$prev_purchaseofpropertyplantequipmentandintangibleassets = "-";
	$prev_proceedsfromsaleofproperty = "-";
	$prev_netproceedsfromissueofshares = "-";
	$prev_netproceedsfromlongtermborrowings = (($totalcoa['20001100']['prev_end_balance'])-($totalcoa['20001100']['prev_start_balance']));
	$prev_netproceedsfromshorttermborrowings = (($totalcoa['2003100']['prev_end_balance'])-($totalcoa['2003100']['prev_start_balance']));
	$prev_cashandcashequivalentsduringtheyear = (($totalcoa['10005000']['prev_end_balance'])-($totalcoa['10005000']['prev_start_balance']));
	$prev_cashequivalentsatthebeginningoftheyear =($totalcoa['10005000']['prev_start_balance']);
	$prev_cashequivalentsattheendoftheyear =($totalcoa['10005000']['prev_end_balance']);



	$returnary = [
        "operating_activities" => [
            [
                "label" => "Profit/(Loss) before tax",
                "current" => 0,
                "previous" => 0
            ],
            [
                "label" => "Depreciation",
                "current" => $depreciation,
                "previous" => $prev_depreciation
            ],
            [
                "label" => "Bad debts written off",
                "current" => $baddebtswrittenoff,
                "previous" => $prev_baddebtswrittenoff
            ],
            [
                "label" => "Trade receivables",
                "current" => $tradereceivables,
                "previous" => $prev_tradereceivables
            ],
            [
                "label" => "Short-term loans and advances",
                "current" => $shorttermloansandadvances,
                "previous" => $prev_shorttermloansandadvances
            ],
            [
                "label" => "Long-term loans and advances",
                "current" => $longtermloansandadvances,
                "previous" => $prev_longtermloansandadvances
            ],
            [
                "label" => "Other current assets",
                "current" => $othercurrentassets,
                "previous" => $prev_othercurrentassets
            ],
            [
                "label" => "Trade payables",
                "current" => $tradepayables,
                "previous" => $prev_tradepayables
            ],
            [
                "label" => "Other current liabilities",
                "current" => $decriesincreaseinothercurrentliabilities,
                "previous" => $prev_decriesincreaseinothercurrentliabilities
            ],
            [
                "label" => "Income tax refund/(paid)",
                "current" => $incometaxrefundpaid,
                "previous" => $prev_incometaxrefundpaid
            ],
        ],

        "investing_activities" => [
            [
                "label" => "Purchase of property",
                "current" => $purchaseofpropertyplantequipmentandintangibleassets,
                "previous" => $prev_purchaseofpropertyplantequipmentandintangibleassets
            ],
            [
                "label" => "Sale of property",
                "current" => $proceedsfromsaleofproperty,
                "previous" => $prev_proceedsfromsaleofproperty
            ]
        ],

        "financing_activities" => [
            [
                "label" => "Issue of shares",
                "current" => $netproceedsfromissueofshares,
                "previous" => $prev_netproceedsfromissueofshares
            ],
            [
                "label" => "Long-term borrowings",
                "current" => $netproceedsfromlongtermborrowings,
                "previous" => $prev_netproceedsfromlongtermborrowings
            ],
            [
                "label" => "Short-term borrowings",
                "current" => $netproceedsfromshorttermborrowings,
                "previous" => $prev_netproceedsfromshorttermborrowings
            ]
        ],

        "summary" => [
            "cash_change" => [
                "current" => $cashandcashequivalentsduringtheyear,
                "previous" => $prev_cashandcashequivalentsduringtheyear
            ],
            "opening_cash" => [
                "current" => $cashequivalentsatthebeginningoftheyear,
                "previous" => $prev_cashequivalentsatthebeginningoftheyear
            ],
            "closing_cash" => [
                "current" => $cashequivalentsattheendoftheyear,
                "previous" => $prev_cashequivalentsattheendoftheyear
            ]
        ]
    ];


    return $returnary;
}

//End Cash Flow


//Start customer ledegr
function GETProcurementCustomerLedger($get){
	
	include_once("account_user.class.php");
    $obj = new accountUserClass();

    $get['from_date'] = $get['start_date'] !=""?$get['start_date']:date('Y-m-01');
    $get['to_date']   = $get['end_date'] !=""?$get['end_date']:date('Y-m-t');
    $get['customer_id']= $get['customer_id'];
 	$result1 = $obj->getVendorledger($get);
    $result = json_decode(json_encode($result1), true);
    $last_account_balances = $result['last_account_balances'];
    $customers = $result['customers'];
    $transactionwithdates = $result['transactionwithdates'];

    ksort($transactionwithdates);

    $debitside  = ['sales_invoices','debit_notes'];
    $creditside = ['credit_notes','receipts'];

    $closingbal = $last_account_balances['closing_balance'];
    $transaction_amt = 0;

    // Calculate transaction amount
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {
            if (in_array($pi['transaction_table'], $debitside)) {
                $closingbal -= $pi['p_total_amount'];
                $transaction_amt -= $pi['p_total_amount'];
            } else {
                $closingbal += $pi['p_total_amount'];
                $transaction_amt += $pi['p_total_amount'];
            }
        }
    }

    // Reset closing balance for row-wise calculation
    $running_balance = $last_account_balances['closing_balance'];

    $ledger = [];

    // Header Data
    $ledger['header'] = [
        "title" => "Customer Ledger",
        "from"  => date('M Y', strtotime($get['from_date'] )),
        "to"    => date('M Y', strtotime($get['to_date'] )),
        "company" => $customers[$post['vendor_id']]['company'] ?? "",
        "customer" => $customers[$post['customer_id']]['display_name'] ?? "",
        "opening_balance" => $last_account_balances['closing_balance'],
        "transaction_amount" => $transaction_amt,
        "closing_balance" => $closingbal,
        "date" => date('Y-m-d'),
        "time" => date('H:i:s')
    ];

    // Opening Row
    $ledger['rows'][] = [
        "type" => "opening",
        "date" => $last_account_balances['balance_date'],
        "transaction_no" => "Opening Balance",
        "name" => "-",
        "ref_no" => "-",
        "ref_date" => "-",
        "debit" => 0,
        "credit" => 0,
        "balance" => $running_balance
    ];

    // Transactions
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {

            $debit = 0;
            $credit = 0;

            if (in_array($pi['transaction_table'], $debitside)) {
                $running_balance -= $pi['p_total_amount'];
                $debit = $pi['p_total_amount'];
            } else {
                $running_balance += $pi['p_total_amount'];
                $credit = $pi['p_total_amount'];
            }

            $ledger['rows'][] = [
                "type" => "transaction",
                "transaction_no" => $pi['transaction_number'] . " - " . $pi['transaction_table'],
                "date" => $pi['transaction_date'],
                "name" => $pi['party_name'],
                "ref_no" => $pi['ref_no'],
                "ref_date" => $pi['ref_date'],
                "debit" => $debit,
                "credit" => $credit,
                "balance" => $running_balance
            ];
        }
    }
    return $ledger;
}


function GETProcurementVendorLedger($get){

	include_once("account_user.class.php");
    $obj = new accountUserClass();

    $get['from_date'] = $get['start_date'] !=""?$get['start_date']:date('Y-m-01');
    $get['to_date']   = $get['end_date'] !=""?$get['end_date']:date('Y-m-t');
    $get['customer_id']= $get['vendor_id'];
 	$result1 = $obj->getVendorledger($get);
    $result = json_decode(json_encode($result1), true);
    $last_account_balances = $result['last_account_balances'];
    $customers = $result['customers'];
    $transactionwithdates = $result['transactionwithdates'];

    ksort($transactionwithdates);

  $debitside = array('supplier_bills','debit_notes');
$creditside = array('credit_notes','payments');
    $closingbal = $last_account_balances['closing_balance'];
    $transaction_amt = 0;

    // Calculate transaction amount
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {
            if (in_array($pi['transaction_table'], $debitside)) {
                $closingbal -= $pi['p_total_amount'];
                $transaction_amt -= $pi['p_total_amount'];
            } else {
                $closingbal += $pi['p_total_amount'];
                $transaction_amt += $pi['p_total_amount'];
            }
        }
    }

    // Reset closing balance for row-wise calculation
    $running_balance = $last_account_balances['closing_balance'];

    $ledger = [];

    // Header Data
    $ledger['header'] = [
        "title" => "Customer Ledger",
        "from"  => date('M Y', strtotime($get['from_date'] )),
        "to"    => date('M Y', strtotime($get['to_date'] )),
        "company" => $customers[$post['vendor_id']]['company'] ?? "",
        "customer" => $customers[$post['vendor_id']]['display_name'] ?? "",
        "opening_balance" => $last_account_balances['closing_balance'],
        "transaction_amount" => $transaction_amt,
        "closing_balance" => $closingbal,
        "date" => date('Y-m-d'),
        "time" => date('H:i:s')
    ];

    // Opening Row
    $ledger['rows'][] = [
        "type" => "opening",
        "date" => $last_account_balances['balance_date'],
        "transaction_no" => "Opening Balance",
        "name" => "-",
        "ref_no" => "-",
        "ref_date" => "-",
        "debit" => 0,
        "credit" => 0,
        "balance" => $running_balance
    ];

    // Transactions
    foreach ($transactionwithdates as $transactions) {
        foreach ($transactions as $pi) {

            $debit = 0;
            $credit = 0;

            if (in_array($pi['transaction_table'], $debitside)) {
                $running_balance -= $pi['p_total_amount'];
                $debit = $pi['p_total_amount'];
            } else {
                $running_balance += $pi['p_total_amount'];
                $credit = $pi['p_total_amount'];
            }

            $ledger['rows'][] = [
                "type" => "transaction",
                "transaction_no" => $pi['transaction_number'] . " - " . $pi['transaction_table'],
                "date" => $pi['transaction_date'],
                "name" => $pi['party_name'],
                "ref_no" => $pi['ref_no'],
                "ref_date" => $pi['ref_date'],
                "debit" => $debit,
                "credit" => $credit,
                "balance" => $running_balance
            ];
        }
    }
    //echo "<pre>"; print_r($ledger);
    return $ledger;
}

//Edn customer ledger
	function getMobileProcurementModulesData($get){
		global $callConfig;
		$return = array();
		//echo "<pre>";
		$gettype = $get['gettype'];
		$transaction_table = $get['transaction_table'];
		$company_id = $get['company_id'];
		if($gettype== "SINGLEITEM"){
			$itemid = $get['item_id'];
			$return['SINGLEITEM'] = $this->togetitemvalues($itemid);
		}elseif($gettype== "GETEMAILUSERWITHCOMPANY"){	
			return $this->getEmailWithDBAndCompanyID($get);

		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYSTOCK"){	
			return $this->GETProcurementModulesDashboardSummaryStock($get);

		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARY"){	
			return $this->GETProcurementModulesDashboardSummary($get);

		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYPROFITLOSS"){	
			return $this->GETProcurementModulesDashboardSummaryProfitLoass($get);

		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYBALANCESHEET"){	
			return $this->GETProcurementModulesDashboardSummaryBalanceSheets($get);
		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYCASHFLOW"){	
			return $this->GETProcurementModulesDashboardSummaryCashFlow($get);
	
		}elseif($gettype== "GETPROCUREMENTCUSTOMERLEDGER"){	
			return $this->GETProcurementCustomerLedger($get);
	

		}elseif($gettype== "GETPROCUREMENTVENDORLEDGER"){	
			return $this->GETProcurementVendorLedger($get);
	

		}elseif($gettype== "GETPROCUREMENTMODULESDASHBOARDSUMMARYTest"){	
			return $this->GETProcurementModulesDashboardSummaryTest($get);


		}elseif($gettype== "GETALREADYADDEDREFRESH"){	
			$doc_ref_no = $get['doc_ref_no'];
			$query = "SELECT id, transaction_date, transaction_number FROM $transaction_table  WHERE company_id=$company_id AND doc_ref_no = '".$doc_ref_no."' AND is_active=0 ";
	        $transaction_details = $callConfig->getRow($query);
	        $return['GETALREADYADDEDREFRESH'] = $transaction_details;

		}elseif($gettype== "getPaymentTermsResult"){	
		//	$ressss = $this->getPaymentTermsResult($get,$get,1); //testing we are added;
		//	print_r($ressss);
			
		}elseif($gettype== "REFTRANSACTIONSTABLES"){
			$query = "SELECT id, transaction_table, reference_table FROM `dynamic_reference_tables` WHERE company_id = ".$get['company_id']." AND reference_table = '".$transaction_table."' AND is_active = 0";
			$queryres = $callConfig->getAllRows($query);
			$return['REFTRANSACTIONSTABLES'] = $queryres;
		}elseif($gettype== "REFTRANSACTIONSLIST"){
			$ref_type = $get['ref_type'];
			$query = "SELECT id, transaction_date, transaction_number FROM $ref_type  WHERE company_id=$company_id AND current_status ='".ST_APPROVED."' AND is_active=0";
	        $transaction_details = $callConfig->getAllRows($query);
	        $return['REFTRANSACTIONSLIST'] = $transaction_details;

		}elseif($gettype== "REFTRANSACTIONS"){	
			$ref_type = $get['ref_type'];
			$ref_id = $get['ref_id'];
			$sql = "SELECT id, page_title,item_table, tax_table FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= '$ref_type' AND item_table !='' AND tax_table !='' AND is_active = 0 ";
		    	$res = $callConfig->getRow($sql);
		    //	$return['mapping_table'] = $res;
		    	$child_table = $res->item_table ?? '';
		    	$tax_table = $res->tax_table ?? '';
	            $query = "SELECT * FROM $ref_type t1 WHERE t1.id=$ref_id  AND t1.company_id=$company_id AND t1.is_active=0";
	            $transaction_details = $callConfig->getRow($query);
	            $return['transaction_details'] = $transaction_details;
	            if($child_table != ''){
	            	$query = "SELECT t2.*, concat(uoms.uom_short_code,'-',uoms.uom_name) unit_name FROM $child_table t2 JOIN  unit_of_measurements uoms ON uoms.id = t2.purchase_uom AND uoms.company_id=$company_id AND uoms.is_active=0 WHERE t2.transaction_id=$ref_id AND  t2.company_id=$company_id AND t2.is_active=0";
	            	$data = $callConfig->getAllRows($query);
	            	$return['transaction_items'] = $data;
	            }
	            if($tax_table != ''){
	            	$query = "SELECT * FROM $tax_table t3 WHERE t3.transaction_id=$ref_id AND  t3.company_id=$company_id AND t3.is_active=0";
	            	$taxes = $callConfig->getAllRows($query);
	            	$return['transaction_item_taxes'] = $taxes;
	            }
		}else{

			$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND field_name = 'party_id'";	
			$parttable_name = $callConfig->getRow($display_attribute_typessql);
			$qquery = urldecode($parttable_name->content);
			$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
			if(isset($explosql[1])){
				$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
			}
			$partytable = trim($explosql[0]); //get Table name
			if($partytable == "vendors"){
				$query = "SELECT id, display_name, company FROM vendors WHERE company_id = ".$get['company_id']." AND is_active = 0";
				$vendorsres = $callConfig->getAllRows($query);
				$return['party'] = $vendorsres;
			}else{
				$partytable = "customers";
				$query = "SELECT id, display_name, company FROM customers WHERE company_id = ".$get['company_id']." AND is_active = 0";
				$customersres = $callConfig->getAllRows($query);
				$return['party'] = $customersres;
			}
			$return['party_table'] = $partytable;
			$uomquery = "SELECT id, uom_name, uom_short_code FROM unit_of_measurements WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$uomqures = $callConfig->getAllRows($uomquery);
			$unit_of_measurements = array();
			foreach($uomqures as $uomre){
				$unit_of_measurements[$uomre->id] =$uomre;
			}
			$return['unit_of_measurements'] = $unit_of_measurements;

		    $queryau = "SELECT id, display_name FROM application_users WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$queryres = $callConfig->getAllRows($queryau);
			$return['application_users'] = $queryres;
	         
	        $queryau = "SELECT id, code, code_description, IF(gst = '' OR gst IS NULL, 0, gst) AS gst FROM hsn_and_sac_codes WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$queryres = $callConfig->getAllRows($queryau);
			$hsn_and_sac_codes = array();
			foreach($queryres as $hsnre){
				$hsn_and_sac_codes[$hsnre->id] =$hsnre;
			}
			$return['hsn_and_sac_codes'] = $hsn_and_sac_codes;    
			
			$payment_termssql = "SELECT id, payment_term_name, content  FROM `payment_terms` WHERE company_id = '".$get['company_id']."' AND is_active =0";
	        $payment_termsres = $callConfig->getAllRows($payment_termssql);
	        $payment_terms = array();
	        foreach($payment_termsres as $payterm){
	        	$payment_terms[$payterm->id] = $payterm;
	        }
	       	$return['payment_terms']= $payment_terms;		

	       	$query = "SELECT id, sku, item_name, item_description, sale_price, purchase_uom, hsn_and_sac_code_id FROM items WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$itemsres = $callConfig->getAllRows($query);
			$items = array();
			foreach($itemsres as $itmres){

				$itmres->purchase_uom_name = $unit_of_measurements[$itmres->purchase_uom]->uom_name;
				$itmres->code = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->code;
				$itmres->code_description = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->code_description;
				$itmres->gst = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->gst;
				$itmres->gst = $hsn_and_sac_codes[$itmres->hsn_and_sac_code_id]->gst;

				
				$items[$itmres->id] = $itmres;
			}
			$return['items'] = $itemsres;

			$adsql = "SELECT id, charge_name  FROM additional_charges WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$adres = $callConfig->getAllRows($adsql);
			$return['additional_charges'] = $adres;


			$rid = $get['rid'];
		 	if(isset($rid) && $rid != ''){
	        	$sql = "SELECT id, page_title,item_table, tax_table FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= '$transaction_table' AND is_active = 0 ";
		    	$res = $callConfig->getRow($sql);
		    	$return['mapping_table'] = $res;
		    	$child_table = $res->item_table ?? '';
		    	$tax_table = $res->tax_table ?? '';

	            $query = "SELECT * FROM $transaction_table t1 WHERE t1.id=$rid  AND t1.company_id=$company_id AND t1.is_active=0";
	            $transaction_details = $callConfig->getRow($query);
	            $return['transaction_details'] = $transaction_details;
	            if($child_table != ''){
	            	$query = "SELECT t2.*, concat(uoms.uom_short_code,'-',uoms.uom_name) unit_name FROM $child_table t2 
	            	JOIN  unit_of_measurements uoms ON uoms.id = t2.purchase_uom AND uoms.company_id=$company_id AND uoms.is_active=0
	            	WHERE t2.transaction_id=$rid AND  t2.company_id=$company_id AND t2.is_active=0";
	            	$data = $callConfig->getAllRows($query);
	            	$return['transaction_items'] = $data;
	            }
	            if($tax_table != ''){
	            	$query = "SELECT * FROM $tax_table t3 WHERE t3.transaction_id=$rid AND  t3.company_id=$company_id AND t3.is_active=0";
	            	$taxes = $callConfig->getAllRows($query);
	            	// $taxes = array();
	            	// foreach ($data as $row) {
	            	//     $taxes[$row->transaction_item_row_id] = $row;
	            	// }
	            	$return['transaction_item_taxes'] = $taxes;
	            }

	            if($transaction_details->party_id !=""){
		            $placeofsupplysql = "SELECT * FROM customer_vendor_gst_billing_details WHERE company_id = $company_id AND transaction_table= '$partytable' AND customer_vendor_id = '".$transaction_details->party_id."'  AND is_active = 0 ";
			    	$placeofsupplyres = $callConfig->getRow($placeofsupplysql);
			    	$return['place_of_supply_list'] = $placeofsupplyres;
			    }

	        }


	        $adsql = "SELECT genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = ".$get['company_id']." AND genc_code ='TRANSACTIONTYPE' AND is_active = 0 ORDER BY id ASC";
			$adres = $callConfig->getAllRows($adsql);
			$return['type'] = $adres;
			
			$adsql = "SELECT  id, cost_center_name  FROM  cost_centers WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$adres = $callConfig->getAllRows($adsql);
			$return['cost_centers'] = $adres;
			
			$adsql = "SELECT id, salesman_name  FROM  salesmen WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$adres = $callConfig->getAllRows($adsql);
			$return['salesmen'] = $adres;

			$adsql = "SELECT id, currency_name  FROM  currencies WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$adres = $callConfig->getAllRows($adsql);
			$return['currencies'] = $adres;


			$return['opex_cpex'] = array('0'=>'OPEX','1'=>'CAPEX');


		}
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function togetmobileadminhrdashboard($get){
	 	global $callConfig;
	 	//echo "<pre>";
	 	$thisyearfirstmonthlastdate = date('Y')."-01-01";
	 	$thisyearfirstmonth = date('Y')."-01";
	 	$edate = date('Y')."-01-01";
	 	$endate = date('Y')."-12-31";
	 	$yy = date('Y');
		//new
		if($get['dash_date']!=""){
			$today_date = $get['dash_date'];
		}else{
			$today_date = date("Y-m-d");
		}
	 	
	 	// yesterday
		$yesterday = date("Y-m-d", strtotime($today_date . " -1 day"));
	 	$day = date("l", strtotime($today_date));

	 	$thismonthstartdate = date("Y-m-d",strtotime('first day of this month', time()));
	 	$thismonthenddate = date("Y-m-d",strtotime('last day of this month', time()));
	 	$thisyear = date("Y");
	 	$thismonth = date("m");

	 	$division_whr = "";
		//$thisyear = 2022;
		//$thismonth = 10;
		//Get Total Eployeees
	 	$sql ="SELECT  emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image,  emp.reporting_manager_id FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

		//$query="SELECT id,display_name  FROM `employees` WHERE company_id = '".$get['company_id']."' and is_active =0";
	 	//$data['topquery'] = $sql;
	 	$empdatas = $callConfig->getAllRows($sql);
	 	$allemplist = array();
	 	$data['todayattendants']['total_emp'] =count((array)$empdatas);
	 	$male_count = 0;
		$female_count = 0;
		$manager_ids = array();
	 	foreach($empdatas as $empdatas1){
	 		$data['todayattendants']['total_emp_ids'][] =$empdatas1;
	 		$allemplist[$empdatas1->id] = $empdatas1;
	 		if($empdatas1->employee_type == '1'){
	 			$data['todayattendants']['regular_emp_ids'][] =$empdatas1;
	 		}elseif($empdatas1->employee_type == '2'){
	 			$data['todayattendants']['contract_emp_ids'][] =$empdatas1;
	 		}
	 		if($empdatas1->gender == 'M'){
			    $male_count++;
			}elseif($empdatas1->gender == 'F'){
			    $female_count++;
			}
			if(!empty($empdatas1->reporting_manager_id)){
		        $manager_ids[$empdatas1->reporting_manager_id] = 1; // unique
		    }

		}
		$total_emp_count = count((array)$empdatas);
		$manager_count = count((array)$manager_ids);
		$employee_count = $total_emp_count - $manager_count;

		if($total_emp_count > 0){
		    $data['todayattendants']['manager_percentage'] = round(($manager_count / $total_emp_count) * 100, 2);
		    $data['todayattendants']['employee_percentage'] = round(($employee_count / $total_emp_count) * 100, 2);
		}else{
		    $data['todayattendants']['manager_percentage'] = 0;
		    $data['todayattendants']['employee_percentage'] = 0;
		}

		$data['todayattendants']['male_count'] = $male_count;
		$data['todayattendants']['female_count'] = $female_count;

		if($total_emp_count > 0){
		    $data['todayattendants']['male_percentage'] = round(($male_count / $total_emp_count) * 100, 2);
		    $data['todayattendants']['female_percentage'] = round(($female_count / $total_emp_count) * 100, 2);
		}else{
		    $data['todayattendants']['male_percentage'] = 0;
		    $data['todayattendants']['female_percentage'] = 0;
		}
		//Get Today Attendance Eployeees
	 	$attendancequery = "SELECT emp.id, emp.display_name,emp.email_id from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
	 	//$data['attendancequery'] = $attendancequery;
	 	$attendata = $callConfig->getAllRows($attendancequery);
	 	$today_count = count((array)$attendata);
	 	$data['todayattendants']['present'] =$today_count;
	 	// $attendata->empcnt;
	 	$data['todayattendants']['present_ids'] = array();
	 	foreach($attendata as $empdatas2){
	 		$data['todayattendants']['present_ids'][] =$empdatas2;
	 		unset($allemplist[$empdatas2->id]);
		}
		foreach($allemplist as $adsaaa){
			$onads[] =  $adsaaa;
		}
	 	$data['totalpressent'] = count((array)$attendata);
	 	$data['todayattendants']['absent_ids'] = $onads;

	 	//Get Yesterday Attendance Eployeees
	 	$query = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$yesterday."' AND eda.is_active = emp.is_active group by emp.id";
	 	$result = $callConfig->getAllRows($query);
	 	$yesterday_count = count((array)$result);
	 	$data['yesterdayattendants']['present'] =$yesterday_count;
		if($yesterday_count > 0){
		    $percent_change = (($today_count - $yesterday_count) / $yesterday_count) * 100;
		} else {
		    $percent_change = ($today_count > 0) ? 100 : 0; // handle divide by zero
		}

		$data['attendance_change_percent'] = round($percent_change, 2);

		//Get Today Planed Leaves Eployeees
	 	$leaves = "SELECT emp.id, emp.display_name,emp.email_id FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$today_date."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";
	 	//$data['leavesql'] = $leaves;
	 
	 	$leavesdata = $callConfig->getAllRows($leaves);
	 	$data['todayattendants']['planned_leave_ids'] = array();
	 	foreach($leavesdata as $empdatas3){
	 		$empdatas3->type = 'employee_leaves';
	 		$data['todayattendants']['planned_leave_ids'][] =$empdatas3;
		}
	 	$empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
	 	$attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0; 
	 	$data['todayattendants']['planned_leave'] =$empcnt;
	 	$data['todayattendants']['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
	 	$data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);

		$pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."')  OR (emp.reporting_manager_id = '".$get['user_id']."')  OR (emp.hr_buddy = '".$get['emp_id']."')  OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

		$pending_leave_data = $callConfig->getAllRows($pending_leave_query);
		$data['todayattendants']['pending_leave_ids'] = array();
		foreach($pending_leave_data as $pending_emp){
			$pending_emp->type = 'employee_leaves';
		    $data['todayattendants']['pending_leave_ids'][] = $pending_emp;
		}
		$data['todayattendants']['pending_leave_count'] = count((array)$pending_leave_data);

		$total_emp_count = count((array)$empdatas);
		$data['last_7_days'] = array();

		for($i = 1; $i <= 7; $i++){
		    $trend_date = date("Y-m-d", strtotime($today_date . " -".$i." day"));
		    $trend_key  = "day".$i;
		    // Present count
		    $attendancequery = "SELECT emp.id FROM employees emp, employee_daily_attendances eda
		        WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr
		        AND (CASE
		                WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
		                WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
		                ELSE 2
		            END
		        ) = 1
		        AND (CASE
		                WHEN '3' <> '1' THEN (
		                    ('1' = '".$get['admin_type']."')
		                    OR (emp.reporting_manager_id = '".$get['user_id']."')
		                    OR (emp.hr_buddy = '".$get['emp_id']."')
		                    OR (emp.finance_buddy = '".$get['emp_id']."')
		                )ELSE (
		                    emp.vendor_id = (
		                        SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'
		                    )) END
		        ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$trend_date."' AND eda.is_active = emp.is_active GROUP BY emp.id";

		    $attendance_data = $callConfig->getAllRows($attendancequery);
		    $present_count = count((array)$attendance_data);

		    // On leave count for that date
		    $next_day = date('Y-m-d', strtotime($trend_date . ' +1 day'));
		    $leavequery = "SELECT emp.id FROM employees emp, employee_leaves el
		        WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr 
		        AND (CASE 
		                WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
		                WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
		                ELSE 2
		            END
		        ) = 1
		        AND (CASE
		                WHEN '3' <> '1' THEN (
		                    ('1' = '".$get['admin_type']."')
		                    OR (emp.reporting_manager_id = '".$get['user_id']."')
		                    OR (emp.hr_buddy = '".$get['emp_id']."')
		                    OR (emp.finance_buddy = '".$get['emp_id']."')
		                )
		                ELSE (
		                    emp.vendor_id = (
		                        SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'
		                    )) END
		        ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$next_day."' AND el.leave_to >= '".$trend_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

		    $leave_data = $callConfig->getAllRows($leavequery);
		    $leave_count = count((array)$leave_data);

		    $absent_count = $total_emp_count - ($present_count + $leave_count);
		    if($absent_count < 0){
		        $absent_count = 0;
		    }

		    $data['last_7_days'][] = array(
		        'key'     => $trend_key,
		        'value'   => $trend_date,
		        'total'   => $total_emp_count,
		        'present' => $present_count,
		        'absent'  => $absent_count,
		        'on_leave'=> $leave_count
		    );
		}

		$resigned_employees_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.employee_leaving_date, COUNT(ea.id) AS asset_count
		FROM employees emp
		INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0 
		LEFT JOIN employee_assets ea ON ea.emp_id = emp.id AND ea.company_id = emp.company_id AND ea.is_active = 0 
		WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 GROUP BY emp.id,emp.display_name,emp.email_id,emp.gender,emp.employee_type,emp.emp_code,emp.phone,emp.image,emp.reporting_manager_id";

		$resigned_employees = $callConfig->getAllRows($resigned_employees_query);
		$data['resigned_employees'] = $resigned_employees;
		$data['resigned_employees_count'] = count((array)$resigned_employees);

		$is_holiday = 'N';
		$holiday_year = date('Y', strtotime($today_date));
		$holiday_query = "SELECT id FROM holidays  WHERE company_id = '".$get['company_id']."' AND holiday_year = '".$holiday_year."' AND holiday_date = '".$today_date."' AND is_active = 0";
		$holiday_result = $callConfig->getAllRows($holiday_query);
		if(count((array)$holiday_result)>0){
		    $is_holiday = 'Y';
		}
		$data['is_holiday'] = $is_holiday;


	 	// Transport gateway 
	 	$data['todayattendants']['transport_getway'] = 0;
	 	$data['todayattendants']['transport_getway_ids'] = array();
	 	$query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0 
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id  AND emp1.is_active = 0
            WHERE  tpg.company_id={$get['company_id']} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";
	 	$transport_getway_data = $callConfig->getAllRows($query);
	 	foreach($transport_getway_data as $transport_getway){
	 		$transport_getway->type = 'transport_gateway';
	 		$data['todayattendants']['transport_getway_ids'][] =$transport_getway;
		}
	 	$transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
	 	$data['todayattendants']['transport_getway'] =$transport_getway_cnt;

	 	// Transport Personal  gateway 
	 	$data['todayattendants']['transport_personal_gateway'] = 0;
	 	$data['todayattendants']['transport_personal_gateway_ids'] = array();
	 	$query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other  FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0";
	 	$transport_personal_gateway = $callConfig->getAllRows($query);
	 	foreach($transport_personal_gateway as $transport_getway){
	 		$transport_getway->type = 'transport_personal_gateway';
	 		$data['todayattendants']['transport_personal_gateway_ids'][] =$transport_getway;
		}
	 	$transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
	 	$data['todayattendants']['transport_personal_gateway'] =$transport_personal_gateway_cnt;

	 	// Leave
	 	$data['todayattendants']['employee_leaves'] = 0;
	 	$data['todayattendants']['employee_leaves_ids'] = array();
	 	$query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other, emp.phone, emp.image, emp.reporting_manager_id  FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$employee_leave->type = 'employee_leaves';
	 		$data['todayattendants']['employee_leaves_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;
	 	// stationery
	 	$data['todayattendants']['stationery'] = 0;
	 	$data['todayattendants']['stationery_ids'] = array();

	 	$query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id  FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get['company_id']."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0";
	 	$stationery_requests = $callConfig->getAllRows($query);
	 	foreach($stationery_requests as $row){
	 		$row->type = 'stationery_requests';
	 		$data['todayattendants']['stationery_ids'][] =$row;
		}
	 	$stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
	 	$data['todayattendants']['stationery'] = $stationery_requests_cnt;

	 	// assets
	 	$data['todayattendants']['assets'] = 0;
	 	$data['todayattendants']['assets_ids'] = array();

	 	$query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id  FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get['company_id']."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0";
	 	$asset_requests = $callConfig->getAllRows($query);
	 	foreach($asset_requests as $row){
	 		$row->type = 'asset_requests';
	 		$data['todayattendants']['assets_ids'][] =$row;
		}
	 	$asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
	 	$data['todayattendants']['assets'] = $asset_requests_cnt;


	 	// attenmiss punch
	 	$data['todayattendants']['miss_punch'] = 0;
	 	$data['todayattendants']['miss_punch_ids'] = array();
	 	$query = "SELECT tg.id as daily_attendances_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other, emp.phone, emp.image, emp.reporting_manager_id  FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0";
	 	$employee_miss_data = $callConfig->getAllRows($query);
	 	foreach($employee_miss_data as $employee_leave){
	 		$employee_leave->type = 'employee_daily_attendances';
	 		$data['todayattendants']['miss_punch_ids'][] =$employee_leave;
		}
	 	$miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
	 	$data['todayattendants']['miss_punch'] =$miss_cnt;
	 	/*$query = "SELECT * FROM `employee_leaves` WHERE company_id = ".$get['company_id']." AND `leave_from` ='".$today_date."' AND is_active = 0";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$data['todayattendants']['employee_leave_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;*/
	 	
	 	$approvals = array();

		$tables_array = array('transport_gateway','transport_personal_gateway','employee_leaves','employee_daily_attendances','asset_requests','stationery_requests');

		// make IN clause properly
		$table_list = "'" . implode("','", $tables_array) . "'";

		$query = "SELECT 
            tal.id AS transaction_approval_log_id,
            tal.transaction_table AS type,
            tal.transaction_id AS rid,
            COALESCE(emp1.display_name, '-') AS requested,
            COALESCE(emp2.display_name, '-') AS pending_approval,
            tal.previous_status AS current_status
          FROM transaction_approval_logs tal
          LEFT JOIN employees emp1 
                 ON tal.assigner_user_id = emp1.user_id
                AND emp1.company_id = tal.company_id
                AND emp1.is_active = 0
          LEFT JOIN employees emp2 
                 ON tal.user_id = emp2.user_id
                AND emp2.company_id = tal.company_id
                AND emp2.is_active = 0
          WHERE tal.response_type = 0
            AND tal.company_id = {$get['company_id']}
            AND tal.response_date IS NULL
            AND tal.transaction_table IN ($table_list)";

		$approval_data = $callConfig->getAllRows($query);
		// label mapping
		$labels = array(
		    'transport_gateway' => 'Transport Gateway',
		    'transport_personal_gateway' => 'Transport Personal Gateway',
		    'employee_leaves' => 'Employee Leaves',
		    'employee_daily_attendances' => 'Employee Daily Attendances',
		    'asset_requests' => 'Asset Requests',
		    'stationery_requests' => 'Stationery Requests'
		);

		foreach ($approval_data as $value) {
		    $type = $value->type;   // because selected as AS type
		    $rid  = $value->rid;

		    // initialize key if not exists
		    if (!isset($approvals[$type])) {
		        $approvals[$type] = array();
		    }

		    $approvals[$type][] = array(
		        'label' => isset($labels[$type]) ? $labels[$type] : ucwords(str_replace('_', ' ', $type)),
		        'requested' => $value->requested,
		        'pending_approval' => $value->pending_approval,
		        'approval_status' => $value->current_status, // or map separately if needed
		        'rid' => $rid,
		        'type' => $type,
		        'transaction_approval_log_id' => $value->transaction_approval_log_id
		    );
		}

		$data['ongoing_operations']['approval_data'] = $approvals;

		$query = "SELECT id,table_name,name,status,tigger,fixable,action_label,display_priority FROM status_progress WHERE company_id={$get['company_id']} AND is_active=0 AND table_name IN ($table_list)";
		$status_data = $callConfig->getAllRows($query);
		$status_arr = array();
		foreach ($status_data as $row) {
		    $status_arr[$row->table_name][$row->status] = $row;
		}
		$data['ongoing_operations']['status_arr'] = $status_arr;

		//Get regular,contact,male,female,manger_count,employees Count Eployeees
	 	$query="SELECT SUM(if(emp.employee_type='1',1,0))as regular, SUM(if(emp.employee_type='2',1,0))as contact, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";
		//$data['query1'] =$query;
	 	$nempdata = $callConfig->getRow($query);
	 	$data['nemp'] = $nempdata;

		//Get Joing Employees	
	 	$mequery= "SELECT count(emp.id) as joinn FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND ( CASE WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy) = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."' AND is_active=0";
	 	//$data['joinsql'] =$mequery;
	 	$medata = $callConfig->getRow($mequery);	
	 	$data['joining'] = $medata->joinn;

		//Get leaving Employees	
	 	$mequery= "SELECT count(emp.id) as realving FROM employees emp WHERE emp.date_of_leaving >= '".$thismonthstartdate."' AND emp.date_of_leaving <= '".$thismonthenddate."' AND emp.date_of_leaving IS NOT NULL AND emp.company_id = '".$get['company_id']."' AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND  is_active=0";
	 	//$data['leaveingsql'] =$mequery;
	 	$medata1 = $callConfig->getRow($mequery);	
	 	$data['relive'] = $medata1->realving;

	 	$query="SELECT SUM(if(emp.gender='M',1,0)) as male, SUM(if(emp.gender='F',1,0))as female, count(DISTINCT(emp.reporting_manager_id)) as manger_count, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END) ";
	 	//AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."'
	 	//$data['thismonthdowsql'] =$query;
	 	$nempdata = $callConfig->getRow($query);
	 	$data['daterangenemp'] = $nempdata;	
	 	$sqqlgraph = "SELECT sum(m12) as j12,
	 	sum(m11) as j11,
	 	sum(m10) as j10,
	 	sum(m9) as j9,
	 	sum(m8) as j8,
	 	sum(m7) as j7,
	 	sum(m6) as j6,
	 	sum(m5) as j5,
	 	sum(m4) as j4,
	 	sum(m3) as j3,
	 	sum(m2) as j2,
	 	sum(m1) as j1,
	 	sum(lm12) as r12,
	 	sum(lm11) as r11,
	 	sum(lm10) as r10,
	 	sum(lm9) as r9,
	 	sum(lm8) as r8,
	 	sum(lm7) as r7,
	 	sum(lm6) as r6,
	 	sum(lm5) as r5,
	 	sum(lm4) as r4,
	 	sum(lm3) as r3,
	 	sum(lm2) as r2,
	 	sum(lm1) as r1
	 	FROM    ( 
	 	SELECT    DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, 
	 	DATE_FORMAT(emp.joining_date, '%Y-%m')dojm, 
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M12,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M11,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M10,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M9,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M8,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M7,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M6,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M5,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M4,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M3,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M2,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M1,
	 	DATE_FORMAT(emp.joining_date, '%Y-%m') monyr,
	 	DATE_FORMAT(emp.date_of_leaving, '%Y-%m')dolm, 
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM12,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM11,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM10,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM9,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM8,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM7,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM6,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM5,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM4,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM3,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM2,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM1,
	 	DATE_FORMAT(emp.date_of_leaving, '%Y-%m') lmonyrl
	 	FROM     employees emp 
	 	WHERE    emp.company_id = 1
	 	AND       (
	 	(emp.joining_date >= '".$thisyearfirstmonthlastdate."' AND emp.joining_date <= '".$endate."') 
	 	OR (emp.date_of_leaving >= '".$thisyearfirstmonthlastdate."' AND emp.date_of_leaving <= '".$endate."')
	 	)  
	 	AND emp.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = (SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'))END))a";

	 	$sqqlgraphres = $callConfig->getRow($sqqlgraph);
		//print_r($sqqlgraphres);
	 	//$data['graphjoinandleavesql'] = $sqqlgraph;
	 	foreach($sqqlgraphres as $sqkey=>$sqq){
	 		$passsqqlgraphres[]=array('id'=>$sqkey,'value'=>$sqq);
	 	}
	 	$data['graphjoinandleave'] = $passsqqlgraphres;
	 	$salarygraph ="SELECT sum(m12) as j12, sum(m11) as j11, sum(m10) as j10, sum(m9) as j9, sum(m8) as j8, sum(m7) as j7, sum(m6) as j6, sum(m5) as j5, sum(m4) as j4, sum(m3) as j3, sum(m2) as j2, sum(m1) as j1 FROM ( SELECT DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m')dojm, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M12, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M11, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M10, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M9, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M8, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M7, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M6, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M5, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M4, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M3, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M2, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M1, DATE_FORMAT(emp.joining_date, '%Y-%m') monyr FROM employees emp, pay_run_summary prs, pay_run_summary_components prsc WHERE emp.company_id = 1 AND emp.id = prs.employee_id AND emp.company_id = prs.company_id AND prsc.company_id = prs.company_id AND prsc.pay_run_summary_id = prs.id AND prsc.employee_id = emp.id AND prsc.component_id = 1 AND (( DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') >= '".$thisyearfirstmonth."') AND ( DATE_FORMAT(CONCAT(prs.pay_year, '-',prs.pay_month, '-','01'), '%Y-%m') <= '".$thisyearfirstmonth."')) AND prsc.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) ) a";
	 	$salarygraphres = $callConfig->getRow($salarygraph);
		//print_r($sqqlgraphres);
	 	foreach($salarygraphres as $sqkey=>$sqq){
	 		$passsalarygraphres[]=array('id'=>$sqkey,'value'=>$sqq);
	 	}
	 	$data['salarygraphres'] = $passsalarygraphres;

	 	$query = "SELECT id,designation_name FROM designations WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$designation_arr = array();
		foreach ($res as $row) {
		    $designation_arr[$row->id] = $row;
		}
		$data['designation_arr'] = $designation_arr;

		$query = "SELECT id,department_name FROM departments WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$department_arr = array();
		foreach ($res as $row) {
		    $department_arr[$row->id] = $row;
		}
		$data['department_arr'] = $department_arr;
	 	return $data;
	}
	function backviewtogetmobileadminhrdashboard($get){
	 	global $callConfig;
	 	//echo "<pre>";
	 	$thisyearfirstmonthlastdate = date('Y')."-01-01";
	 	$thisyearfirstmonth = date('Y')."-01";
	 	$edate = date('Y')."-01-01";
	 	$endate = date('Y')."-12-31";
	 	$yy = date('Y');
		//new
	 	if($get['dash_date']!=""){
			$today_date = $get['dash_date'];
		}else{
			$today_date = date("Y-m-d");
		}
		$day = date("l", strtotime($today_date));
	 	$thismonthstartdate = date("Y-m-d",strtotime('first day of this month', time()));
	 	$thismonthenddate = date("Y-m-d",strtotime('last day of this month', time()));
	 	$thisyear = date("Y");
	 	$thismonth = date("m");
		// return $get;
		//$thisyear = 2022;
		//$thismonth = 10;
		//Get Total Eployeees
	 	$sql ="SELECT id, display_name, email_id, employee_type, '' as other FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

		//$query="SELECT id,display_name  FROM `employees` WHERE company_id = '".$get['company_id']."' and is_active =0";
	 	//$data['topquery'] = $sql;
	 	$empdatas = $callConfig->getAllRows($sql);
	 	$allemplist = array();
	 	$data['todayattendants']['total_emp'] =count((array)$empdatas);
	 	foreach($empdatas as $empdatas1){
	 		$data['todayattendants']['total_emp_ids'][] =$empdatas1;
	 		$allemplist[$empdatas1->id] = $empdatas1;
	 		if($empdatas1->employee_type == '1'){
	 			$data['todayattendants']['regular_emp_ids'][] =$empdatas1;
	 		}elseif($empdatas1->employee_type == '2'){
	 			$data['todayattendants']['contract_emp_ids'][] =$empdatas1;
	 		}

		}
		//Get Today Attendance Eployeees
	 	$attendancequery = "SELECT emp.id, emp.display_name,emp.email_id, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
	 	//$data['attendancequery'] = $attendancequery;
	 	$attendata = $callConfig->getAllRows($attendancequery);
	 	$data['todayattendants']['present'] =count((array)$attendata);
	 	// $attendata->empcnt;
	 	$data['todayattendants']['present_ids'] = array();
	 	foreach($attendata as $empdatas2){
	 		$data['todayattendants']['present_ids'][] =$empdatas2;
	 		unset($allemplist[$empdatas2->id]);
		}
		foreach($allemplist as $adsaaa){
			$onads[] =  $adsaaa;
		}
	 	$data['totalpressent'] = count((array)$attendata);
	 	$data['todayattendants']['absent_ids'] = $onads;

		//Get Today Planed Leaves Eployeees
	 	$prsday = date('Y-m-d', strtotime('+1 day', strtotime($today_date)));
	 	$leaves = "SELECT el.id AS leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$prsday."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";
	 	//echo $leaves ; exit;
	 	// $data['leavesql'] = $leaves;
	 
	 	$leavesdata = $callConfig->getAllRows($leaves);
	 	$data['todayattendants']['planned_leave_ids'] = array();
	 	foreach($leavesdata as $empdatas3){
	 		$empdatas3->type = 'employee_leaves';
	 		$data['todayattendants']['planned_leave_ids'][] =$empdatas3;
		}
	 	$empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
	 	$attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0; 
	 	$data['todayattendants']['planned_leave'] =$empcnt;
	 	$data['todayattendants']['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
	 	$data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);


	 	$pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0  AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."')  OR (emp.reporting_manager_id = '".$get['user_id']."')  OR (emp.hr_buddy = '".$get['emp_id']."')  OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

		$pending_leave_data = $callConfig->getAllRows($pending_leave_query);
		$data['todayattendants']['pending_leave_ids'] = array();
		foreach($pending_leave_data as $pending_emp){
			$pending_emp->type = 'employee_leaves';
		    $data['todayattendants']['pending_leave_ids'][] = $pending_emp;
		}
		$data['todayattendants']['pending_leave_count'] = count((array)$pending_leave_data);
		
	 	// Transport gateway 
	 	$data['todayattendants']['transport_getway'] = 0;
	 	$data['todayattendants']['transport_getway_ids'] = array();
	 	// $query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, CONCAT('Visit Purpose : ', tg.visit_purpose) as other  FROM transport_gateway tg, employees emp WHERE emp.user_id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND (tg.entry_date ='".$today_date."' OR tg.exit_date ='".$today_date."') AND tg.is_active = 0";
	 	$query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0 
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id  AND emp1.is_active = 0
            WHERE  tpg.company_id={$get['company_id']} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";

	 	//$query = "SELECT * FROM `transport_gateway` WHERE company_id = AND `entry_date` = AND is_active = 0";
	 	$transport_getway_data = $callConfig->getAllRows($query);
	 	foreach($transport_getway_data as $transport_getway){
	 		$transport_getway->type = 'transport_gateway';
	 		$data['todayattendants']['transport_getway_ids'][] =$transport_getway;
		}
	 	$transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
	 	$data['todayattendants']['transport_getway'] =$transport_getway_cnt;
	 	
	 	// Transport Personal  gateway 
	 	$data['todayattendants']['transport_personal_gateway'] = 0;
	 	$data['todayattendants']['transport_personal_gateway_ids'] = array();
	 	$query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type,  emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0";
	 	//$query = "SELECT * FROM `transport_personal_gateway` WHERE company_id = ".$get['company_id']." AND `entry_date` ='".$today_date."' AND is_active = 0";
	 	$transport_personal_gateway = $callConfig->getAllRows($query);
	 	foreach($transport_personal_gateway as $transport_getway){
	 		$transport_getway->type = 'transport_personal_gateway';
	 		$data['todayattendants']['transport_personal_gateway_ids'][] =$transport_getway;
		}
	 	$transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
	 	$data['todayattendants']['transport_personal_gateway'] =$transport_personal_gateway_cnt;

	 	// Leave
	 	$data['todayattendants']['employee_leaves'] = 0;
	 	$data['todayattendants']['employee_leaves_ids'] = array();
	 	$query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$employee_leave->type = 'employee_leaves';
	 		$data['todayattendants']['employee_leaves_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;
		// stationery
	 	$data['todayattendants']['stationery'] = 0;
	 	$data['todayattendants']['stationery_ids'] = array();

	 	$query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get['company_id']."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0";
	 	$stationery_requests = $callConfig->getAllRows($query);
	 	foreach($stationery_requests as $row){
	 		$row->type = 'stationery_requests';
	 		$data['todayattendants']['stationery_ids'][] =$row;
		}
	 	$stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
	 	$data['todayattendants']['stationery'] = $stationery_requests_cnt;

	 	// assets
	 	$data['todayattendants']['assets'] = 0;
	 	$data['todayattendants']['assets_ids'] = array();

	 	$query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get['company_id']."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0";
	 	$asset_requests = $callConfig->getAllRows($query);
	 	foreach($asset_requests as $row){
	 		$row->type = 'asset_requests';
	 		$data['todayattendants']['assets_ids'][] =$row;
		}
	 	$asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
	 	$data['todayattendants']['assets'] = $asset_requests_cnt;

	 	// attenmiss punch
	 	$data['todayattendants']['miss_punch'] = 0;
	 	$data['todayattendants']['miss_punch_ids'] = array();
	 	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0";
	 	$employee_miss_data = $callConfig->getAllRows($query);
	 	foreach($employee_miss_data as $employee_leave){
	 		$employee_leave->type = 'employee_daily_attendances';
	 		$data['todayattendants']['miss_punch_ids'][] =$employee_leave;
		}
	 	$miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
	 	$data['todayattendants']['miss_punch'] =$miss_cnt;

 		$query = "SELECT id,designation_name FROM designations WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$designation_arr = array();
		foreach ($res as $row) {
		    $designation_arr[$row->id] = $row;
		}
		$data['designation_arr'] = $designation_arr;

		$query = "SELECT id,department_name FROM departments WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$department_arr = array();
		foreach ($res as $row) {
		    $department_arr[$row->id] = $row;
		}
		$data['department_arr'] = $department_arr;
	 	return $data;
	}
	function togetmobileadminhrviewtopemplist($get){
		global $callConfig;
		//print_r($get); exit;
		$res = $this->backviewtogetmobileadminhrdashboard($get);
		
		$field = $get['field'];
		$resdata = $res['todayattendants'][$field];
		$return['count'] = count($resdata);
		// echo $field;  print_r($resdata); echo "*********"; print_r($res); exit;
		$return['data'] = array_slice($resdata, $get['limit'], $get['lenght']);
		$return['department_arr'] = $res['department_arr'] ?? array();
		$return['designation_arr'] = $res['designation_arr'] ?? array();
		return $return;
	}
	// inventory added by koti start
	/**
	 * API 1: Get all categories, optionally get items for a specific category with cart status
	 * @param array $get - Must contain: company_id, user_id (for cart join), optional: category_id
	 * @return array - Returns categories array, and items array if category_id provided
	 */
	function getStationeryCategoriesWithItems($get) {
	    global $callConfig;
	    
	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    
	    // Always get all categories
	    $cat_query = "SELECT id, name 
	                  FROM stationery_categories 
	                  WHERE company_id = '{$company_id}' 
	                    AND is_active = 0 
	                  ORDER BY name ASC";
	    
	    $categories = $callConfig->getAllRows($cat_query);
	    
	    $result = array(
	        "status" => "success",
	        "categories" => $categories
	    );
	    
	    // If category_id provided, get items with cart status
	    if (isset($get['category_id']) && $get['category_id'] != '') {
	        $category_id = $get['category_id'];
	        
	        $items_query = "SELECT 
	                si.id,
	                si.item_name,
	                si.description,
	                si.image,
	                si.available_qty,
	                si.cost,
	                sc.id AS cart_id,
	                COALESCE(sc.qty, 0) AS cart_qty
	            FROM stationery_items si
	            LEFT JOIN stationery_cart sc 
	                ON sc.item_id = si.id
	                AND sc.company_id = '{$company_id}'
	                AND sc.user_id = '{$user_id}'
	                AND sc.request_no IS NULL
	                AND sc.is_active = 0
	            WHERE si.company_id = '{$company_id}'
	              AND si.category_id = '{$category_id}'
	              AND si.available_qty > 0
	              AND si.is_active = 0
	            ORDER BY si.item_name ASC";
	        
	        $items = $callConfig->getAllRows($items_query);
	        
	        $result['items'] = $items;
	        $result['category_id'] = $category_id;
	    }
	    
	    return $result;
	}

	/**
	 * API 2: Search stationery items by category and item name (LIKE search)
	 * @param array $get - Must contain: company_id, user_id, category_id, item_name
	 * @return array - Returns matching items with cart status
	 */
	function searchStationeryItemsByCategory($get) {
	    global $callConfig;
	    
	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    $category_id = $get['category_id'];
	    $item_name = $get['item_name'] ?? '';
	    
	    // Escape special characters for LIKE query
	    $item_name = $callConfig->real_escape_string($item_name);
	    
	    $query = "SELECT 
	            si.id,
	            si.item_name,
	            si.description,
	            si.image,
	            si.available_qty,
	            si.cost,
	            sc.id AS cart_id,
	            COALESCE(sc.qty, 0) AS cart_qty
	        FROM stationery_items si
	        LEFT JOIN stationery_cart sc 
	            ON sc.item_id = si.id
	            AND sc.company_id = '{$company_id}'
	            AND sc.user_id = '{$user_id}'
	            AND sc.request_no IS NULL
	            AND sc.is_active = 0
	        WHERE si.company_id = '{$company_id}'
	          AND si.category_id = '{$category_id}'
	          AND si.item_name LIKE '%{$item_name}%'
	          AND si.available_qty > 0
	          AND si.is_active = 0
	        ORDER BY si.item_name ASC";
	    
	    $items = $callConfig->getAllRows($query);
	    
	    return array(
	        "status" => "success",
	        "items" => $items,
	        "search_term" => $get['item_name'] ?? ''
	    );
	}

	/**
	 * API 3: Manage cart quantities - Add or Remove items from temporary cart
	 * @param array $get - Must contain: company_id, user_id
	 * @param array $post - Must contain: item_id, qty, cart_type (1=ADD, 0=REMOVE), optional: cart_id
	 * @return array - Returns updated cart status
	 */
	function manageStationeryCart($get, $post) {
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    $item_id = $post['item_id'];
	    $qty = intval($post['qty']);
	    $cart_type = intval($post['cart_type']);
	    $cart_id = isset($post['cart_id']) ? intval($post['cart_id']) : 0;
	    
	    // Validate required parameters
	    if (empty($item_id) || $qty <= 0) {
	        return array(
	            "status" => "error",
	            "msg" => "Invalid item_id or quantity"
	        );
	    }
	    
	    $new_qty = 0;
	    $final_cart_id = $cart_id;
	    
	    // cart_type = 1: ADD (increase qty)
	    if ($cart_type == 1) {
	        // If cart_id is provided, update that specific cart item
	        if (!empty($cart_id)) {
	            $check_query = "SELECT id, qty FROM stationery_cart 
	                            WHERE id = '{$cart_id}'
	                              AND company_id = '{$company_id}' 
	                              AND user_id = '{$user_id}' 
	                              AND item_id = '{$item_id}'
	                              AND request_no IS NULL
	                              AND is_active = 0
	                            LIMIT 1";
	            
	            $existing = $callConfig->getRow($check_query);
	            
	            if ($existing && $existing->id) {
	                // Update existing cart item - increment quantity
	                $new_qty = intval($existing->qty) + $qty;
	                $final_cart_id = $existing->id;
	                
	                $update_array = array(
	                    'qty' => $new_qty,
	                    'updated_by' => $user_id,
	                    'updated_date' => date('Y-m-d H:i:s')
	                );
	                
	                $callConfig->updateRecord('stationery_cart', $update_array, 'id', $final_cart_id);
	            } else {
	                return array(
	                    "status" => "error",
	                    "msg" => "Cart item not found with provided cart_id"
	                );
	            }
	        } else {
	            // No cart_id provided, check if item already exists in temp cart by item_id
	            $check_query = "SELECT id, qty FROM stationery_cart 
	                            WHERE company_id = '{$company_id}' 
	                              AND user_id = '{$user_id}' 
	                              AND item_id = '{$item_id}'
	                              AND request_no IS NULL
	                              AND is_active = 0
	                            LIMIT 1";
	            
	            $existing = $callConfig->getRow($check_query);
	            
	            if ($existing && $existing->id) {
	                // Update existing cart item - increment quantity
	                $new_qty = intval($existing->qty) + $qty;
	                $final_cart_id = $existing->id;
	                
	                $update_array = array(
	                    'qty' => $new_qty,
	                    'updated_by' => $user_id,
	                    'updated_date' => date('Y-m-d H:i:s')
	                );
	                
	                $callConfig->updateRecord('stationery_cart', $update_array, 'id', $final_cart_id);
	                
	            } else {
	                // Insert new cart item
	                $new_qty = $qty;
	                
	                $insert_array = array(
	                    'company_id' => $company_id,
	                    'item_id' => $item_id,
	                    'user_id' => $user_id,
	                    'qty' => $new_qty,
	                    'request_no' => NULL,
	                    'is_active' => 0,
	                    'current_status' => 0,
	                    'created_by' => $user_id,
	                    'created_date' => date('Y-m-d H:i:s')
	                );
	                
	                $result = $callConfig->insertRecord('stationery_cart', $insert_array);
	                $final_cart_id = $result['LASTID'];
	            }
	        }
	        
	    }
	    // cart_type = 0: REMOVE (decrease qty)
	    else if ($cart_type == 0) {
	        // cart_id is required for decrease operation
	        if (empty($cart_id)) {
	            return array(
	                "status" => "error",
	                "msg" => "cart_id is required for decrease operation"
	            );
	        }
	        
	        // Get current quantity
	        $check_query = "SELECT qty FROM stationery_cart 
	                        WHERE id = '{$cart_id}'
	                          AND company_id = '{$company_id}'
	                          AND user_id = '{$user_id}'
	                          AND request_no IS NULL
	                          AND is_active = 0
	                        LIMIT 1";
	        
	        $existing = $callConfig->getRow($check_query);
	        
	        if (!$existing) {
	            return array(
	                "status" => "error",
	                "msg" => "Cart item not found"
	            );
	        }
	        
	        // Calculate new quantity (subtract, but keep at 0 minimum)
	        $current_qty = intval($existing->qty);
	        $new_qty = $current_qty - $qty;
	        
	        if ($new_qty < 0) {
	            $new_qty = 0;
	        }
	        
	        // Update cart item
	        $update_array = array(
	            'qty' => $new_qty,
	            'updated_by' => $user_id,
	            'updated_date' => date('Y-m-d H:i:s')
	        );
	        
	        $callConfig->updateRecord('stationery_cart', $update_array, 'id', $cart_id);
	        $final_cart_id = $cart_id;
	        
	    } else {
	        return array(
	            "status" => "error",
	            "msg" => "Invalid cart_type. Use 1 for ADD, 0 for REMOVE"
	        );
	    }
	    
	    // Get total cart items count
	    $count_query = "SELECT SUM(qty) as total_items 
	                    FROM stationery_cart 
	                    WHERE company_id = '{$company_id}'
	                      AND user_id = '{$user_id}'
	                      AND request_no IS NULL
	                      AND is_active = 0";
	    
	    $count_result = $callConfig->getRow($count_query);
	    $total_cart_items = $count_result ? intval($count_result->total_items) : 0;
	    
	    return array(
	        "status" => "success",
	        "cart_id" => $final_cart_id,
	        "new_qty" => $new_qty,
	        "total_cart_items" => $total_cart_items,
	        "msg" => "Cart updated successfully"
	    );
	}

	/**
	 * API 4: Get User's Cart Items
	 * @param array $get - Must contain: company_id, user_id
	 * @return array - Returns all cart items with item details
	 */
	function getUserStationeryCart($get) {
	    global $callConfig;
	    
	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    
	    // Fetch all cart items with full item details
	    $query = "SELECT 
	            sc.id AS cart_id,
	            sc.item_id AS id,
	            sc.qty AS cart_qty,
	            sc.created_date,
	            si.item_name,
	            si.description,
	            si.image,
	            si.available_qty,
	            si.cost,
	            si.category_id,
	            cat.name AS category_name
	        FROM stationery_cart sc
	        INNER JOIN stationery_items si 
	            ON si.id = sc.item_id
	            AND si.company_id = sc.company_id
	            AND si.is_active = 0
	        LEFT JOIN stationery_categories cat
	            ON cat.id = si.category_id
	            AND cat.company_id = si.company_id
	            AND cat.is_active = 0
	        WHERE sc.company_id = '{$company_id}'
	          AND sc.user_id = '{$user_id}'
	          AND sc.qty>0
	          AND sc.request_no IS NULL
	          AND sc.is_active = 0
	        ORDER BY sc.created_date DESC";
	    
	    $cart_items = $callConfig->getAllRows($query);
	    
	    // Get total cart count
	    $count_query = "SELECT 
	                    COUNT(*) as total_items,
	                    SUM(qty) as total_qty
	                FROM stationery_cart 
	                WHERE company_id = '{$company_id}'
	                  AND user_id = '{$user_id}'
	                  AND request_no IS NULL
	                  AND is_active = 0";
	    
	    $count_result = $callConfig->getRow($count_query);
	    
	    return array(
	        "status" => "success",
	        "cart_items" => $cart_items,
	        "total_items" => $count_result ? intval($count_result->total_items) : 0,
	        "total_qty" => $count_result ? intval($count_result->total_qty) : 0
	    );
	}

	/**
	 * API 5: Create Stationery Request from temporary cart
	 * @param array $get - Must contain: company_id, user_id
	 * @param array $post - Must contain: request_date, require_date, st_emp_id, request_comment, 
	 *                      deliver_type, location_id, st_current_status, and optional: 
	 *                      building_id, building_floor_id, building_floor_space_id, other_name
	 * @return array - Returns request details and status
	 */
	function createStationeryRequest($get, $post) {
	    global $callConfig;
	    $return = array();

	    $return['post'] = $post;
	    $return['get'] = $get;
	    // return  $return;

	    // From GET: company_id and user_id
	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    
	    // From POST: request details
	    $request_date = date('Y-m-d');
	    $require_date = $post['require_date'] ?? date('Y-m-d');
	    $emp_id = $post['st_emp_id'] ?? 0;
	    $request_comment = $post['request_comment'] ?? '';


	    /* ===== 1. Generate Request Serial Number ===== */
	    $get['transaction_date'] = date('Y-m-d');
	    $rnd = $this->toGetApplicationSerialNumber('stationery_requests',$get);

	    if (!$rnd) {
	        return array(
	            "status" => "error",
	            "msg" => "Failed to generate serial number"
	        );
	    }

	    /* ===== 2. Create stationery_request record ===== */
	    $request_array = array(
	        'company_id' => $company_id,
	        'request_no' => $rnd,
	        'request_date' => $request_date,
	        'require_date' => $require_date,
	        'emp_id' => $emp_id,
	        'request_comment' => $request_comment,
	        'user_id' => $user_id,
	        'is_active' => 0,
	        'current_status' => 0,
	        'created_by' => $user_id,
	        'created_date' => date('Y-m-d H:i:s')
	    );

	    $request_res = $callConfig->insertRecord('stationery_requests', $request_array);
	    
	    if (!isset($request_res['LASTID'])) {
	        return array(
	            "status" => "error",
	            "msg" => "Failed to create stationery request",
	            "debug" => $request_res
	        );
	    }

	    $request_id = $request_res['LASTID'];

	    /* ===== 3. Get all temporary cart items (request_no IS NULL) ===== */
	    $cart_query = "SELECT id, item_id, qty, color_id, size_id, uniform_type_id 
	                   FROM stationery_cart 
	                   WHERE company_id = '{$company_id}'
	                     AND user_id = '{$user_id}'
	                     AND request_no IS NULL
	                     AND is_active = 0
	                   ORDER BY id ASC";

	    $cart_items = $callConfig->getAllRows($cart_query);

	    if (empty($cart_items)) {
	        return array(
	            "status" => "error",
	            "msg" => "No items in cart to for this user"
	        );
	    }

	    $processed_items = array();
	    
	    foreach ($cart_items as $item) {
	        $item_id = $item->item_id;
	        $cart_item_id = $item->id;
	        $qty = intval($item->qty);

	        // UPDATE existing cart item with request_no and request_id
	        $update_array = array(
	            'request_no' => $rnd,
	            'request_id' => $request_id,
	            'updated_by' => $user_id,
	            'updated_date' => date('Y-m-d H:i:s')
	        );

	        $callConfig->updateRecord('stationery_cart', $update_array, 'id', $cart_item_id);
	        $processed_items[] = array(
	            'cart_id' => $cart_item_id,
	            'action' => 'updated'
	        );
	    }
	    return array(
	        "status" => "success",
	        "msg" => "Stationery request created successfully",
	        "request_id" => $request_id,
	        "request_no" => $rnd,
	        "processed_items" => $processed_items,
	    );
	}

	// inventory added by koti end
	function togetemployeeidentitycarddetails($get){
		global $callConfig;
		$query = "SELECT id, emp_code, display_name,`pf_no`, `pf_uan`, `esi_no`, `driving_licence`, `driving_licence_expiry_date`, `passport`, `passport_expiry_date`, `adhar_no`, `pan_number`, attached_files FROM employees WHERE id=".$get['emp_id'];
		$data = $callConfig->getRow($query);
		$return['identity']= $data;
		return $return;
	}

	function togetassetsdetails($get){
		global $callConfig;
		$return = array();
		$query = "SELECT id,category_id,name FROM asset_attributes WHERE company_id={$get['company_id']} AND is_active=0";
		$data = $callConfig->getAllRows($query);
		$return['attributes'] = $data;
		$attributes = array();
		foreach ((array)$data as $row) {
		    $attributes[$row->category_id][] = $row;
		}
		
		$query = "SELECT * FROM asset_categories WHERE company_id={$get['company_id']} AND is_active=0";
		$data = $callConfig->getAllRows($query);

		$return['categories'] = $data;
		
		$categories = array();

		foreach ((array)$data as $row) {
		    $categories[$row->id] = (array)$row;
		    $categories[$row->id]['child'] = 'N';
		    $categories[$row->id]['child_data'] = array();

		    if (isset($attributes[$row->id]) && count((array)$attributes[$row->id]) > 0) {
		        $categories[$row->id]['child'] = 'Y';
		        $categories[$row->id]['child_data'] = $attributes[$row->id];
		    }
		}
		$return['data'] = $categories;

		$query = "SELECT * FROM locations WHERE company_id={$get['company_id']} AND is_active=0";
		$data = $callConfig->getAllRows($query);

		$return['locations'] = $data;

		return $return;
	}

	/**
	 * Insert or Edit asset_requests
	 * If $get['rid'] exists, performs UPDATE; otherwise performs INSERT
	 */
	function insertAssetRequests($get, $post){
		global $callConfig;
		$return = array();



		// Create common array for both insert and update
		$insertary = array(
			'company_id' => $get['company_id'] ?? '',
			'category_id' => $post['category_id'] ?? '',
			'originator' => $post['originator'] ?? '',
			'attribute' => $post['attribute'] ?? '',
			'asset_required_start_date' =>  date('Y-m-d'),
			'asset_required_end_date' => $post['asset_required_end_date'] ?? null,
			'location_id' => $post['location_id'] ?? '',
			'delivery_type' => $post['delivery_type'] ?? 'S',
			'delivery_address' => $post['delivery_address'] ?? '',
			'quantity' => $post['quantity'] ?? 0,
			'description' => $post['description'] ?? '',
			'request_datetime' => date('Y-m-d H:i:s'),
			'approval_user_id' => 610, // This should ideally come from the workflow configuration

			'is_active' => 0
		);

		// Check if editing (rid exists)
		if (isset($get['rid']) && $get['rid'] != '') {
			// UPDATE mode
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$insertary['updated_by'] = $get['user_id'];

			$res = $callConfig->updateRecord('asset_requests', $insertary, 'id', $get['rid']);
			$return['res'] = $res;
			$return['status'] = 'success';
			$return['message'] = 'Asset request updated successfully';
			$return['id'] = $get['rid'];
		} else {
			// INSERT mode

			$get['transaction_date'] = date('Y-m-d');
	    	$rnd = $this->toGetApplicationSerialNumber('asset_requests',$get);

			$insertary['transaction_no'] = $rnd;
			$insertary['current_status'] = 0;
			$insertary['status_date'] = date('Y-m-d H:i:s');
			$insertary['created_date'] = date('Y-m-d H:i:s');
			$insertary['created_by'] = $get['user_id'];

			$res = $callConfig->insertRecord('asset_requests', $insertary);
			$return['res'] = $res;
			$return['status'] = 'success';
			$return['message'] = 'Asset request created successfully';
			$return['id'] = $res['LASTID'] ?? '';
		}

		return $return;
	}

	function togetAssetRequestDetails($get){

		global $callConfig;
		$return = array();
		$rid = $get['rid'] ?? '';
		$company_id = $get['company_id'] ?? '';

		if ($rid == '' || $company_id == '') {
			$return['status'] = 'error';
			$return['message'] = 'Request ID and Company ID are required';
			return $return;
		}

		// Get asset request details with related data
		$query = "SELECT ar.*, 
			ac.category_description AS category_name,
			loc.location_name,
			au.display_name AS originator_name, 
			au.user_email AS originator_email,
			apu.display_name AS approval_name, 
			apu.user_email AS approval_email
		FROM asset_requests ar
		LEFT JOIN asset_categories ac ON ac.id = ar.category_id AND ac.company_id = ar.company_id AND ac.is_active=0
		LEFT JOIN locations loc ON loc.id = ar.location_id AND loc.company_id = ar.company_id AND loc.is_active=0
		LEFT JOIN application_users au ON au.id = ar.originator AND au.company_id = ar.company_id AND au.is_active=0
		LEFT JOIN application_users apu ON apu.id = ar.approval_user_id AND apu.company_id = ar.company_id AND apu.is_active=0
		WHERE ar.company_id = {$company_id} AND ar.id = {$rid} AND ar.is_active = 0";

		$data = $callConfig->getRow($query);

		if (!$data) {
			$return['status'] = 'error';
			$return['message'] = 'Asset request not found';
			return $return;
		}

		$return['data'] = $data;
		$return['status'] = 'success';

		// Get attributes if present
		$attributes = array();
		$attribute_ids = array();
		if (!empty($data->attribute)) {
			$attribute_ids = array_filter(array_map('trim', explode(',', (string)$data->attribute)));
		}

		if (count($attribute_ids) > 0) {
			$attribute_ids = array_map('intval', $attribute_ids);
			$attr_ids = implode(',', $attribute_ids);
			$attr_query = "SELECT id, name FROM asset_attributes 
				WHERE company_id = {$company_id} AND is_active = 0 AND id IN ({$attr_ids})";
			$attributes = $callConfig->getAllRows($attr_query);
		}

		$return['attributes'] = $attributes;

		return $return;
	}
	function insertWorkFromHomeRequests($get, $post){
		global $callConfig;
		$return = array();

		$company_id = $get['company_id'] ?? '';
		$user_id = $get['user_id'] ?? '';
		$emp_id = $post['emp_id'] ?? '';
		$request_type = $post['request_type'] ?? '';

		if ($company_id == '') {
			$return['status'] = 'error';
			$return['message'] = 'Company ID is required';
			return $return;
		}

		if ($emp_id == '' || $request_type == '') {
			$return['status'] = 'error';
			$return['message'] = 'emp_id and request_type are required';
			return $return;
		}

		$insertary = array(
			'company_id' => $company_id,
			'emp_id' => $emp_id,
			'request_type' => $request_type,
			'effective_from_date' => $post['effective_from_date'] ?? null,
			'effective_to_date' => $post['effective_to_date'] ?? null,
			'number_days' => $post['number_days'] ?? 0,
			'special_request' => $post['special_request'] ?? '',
			'from_date' => $post['from_date'] ?? null,
			'to_date' => $post['to_date'] ?? null,
			'is_active' => 0
		);

		if (isset($get['rid']) && $get['rid'] != '') {
			$insertary['updated_by'] = $user_id;
			$insertary['updated_date'] = date('Y-m-d H:i:s');

			$res = $callConfig->updateRecord('request_work_from_home', $insertary, 'id', $get['rid']);
			$return['res'] = $res;
			$return['status'] = 'success';
			$return['message'] = 'Work from home request updated successfully';
			$return['id'] = $get['rid'];
		} else {
			$get['transaction_date'] = date('Y-m-d');
	    	$rnd = $this->toGetApplicationSerialNumber('request_work_from_home',$get);

			$insertary['transaction_no'] = $rnd;
			$insertary['current_status'] = 0;
			$insertary['status_date'] = date('Y-m-d H:i:s');
			$insertary['created_date'] = date('Y-m-d H:i:s');
			$insertary['created_by'] = $user_id;

			$res = $callConfig->insertRecord('request_work_from_home', $insertary);
			$return['res'] = $res;
			$return['status'] = 'success';
			$return['message'] = 'Work from home request created successfully';
			$return['id'] = $res['LASTID'] ?? '';
		}

		return $return;
	}

	function togetWorkFromHomeRequestDetails($get){
		global $callConfig;
		$return = array();
		$rid = $get['rid'] ?? '';
		$company_id = $get['company_id'] ?? '';

		if ($rid == '' || $company_id == '') {
			$return['status'] = 'error';
			$return['message'] = 'Request ID and Company ID are required';
			return $return;
		}

		$query = "SELECT rwfh.*, 
			emp.emp_code,
			emp.display_name AS emp_name
		FROM request_work_from_home rwfh
		LEFT JOIN employees emp ON emp.id = rwfh.emp_id AND emp.company_id = rwfh.company_id AND emp.is_active=0
		WHERE rwfh.company_id = {$company_id} AND rwfh.id = {$rid} AND rwfh.is_active = 0";

		$data = $callConfig->getRow($query);

		if (!$data) {
			$return['status'] = 'error';
			$return['message'] = 'Work from home request not found';
			return $return;
		}

		$return['data'] = $data;
		$return['status'] = 'success';

		return $return;
	}

    function getUserMobileStatusDashboard($get) {
        global $callConfig;
        
        try {
            $company_id = $get['company_id'] ?? '';
            
            if (empty($company_id)) {
                return [
                    'status' => 'error',
                    'message' => 'Company ID is required',
                    'data' => null
                ];
            }

            // Query 1: Get all active users from application_users
            $users_query = "SELECT 
                    id,
                    display_name,
                    user_email,
                    user_mobile,
                    role_id,
                    user_activated,
                    current_status,
                    last_logged_in_at,
                    user_type
                FROM " . TBL_USERS . "
                WHERE company_id = '" . $company_id . "' 
                AND user_activated = 0
                AND is_active = 0
                ORDER BY display_name ASC";
            
            $all_users = (array)$callConfig->getAllRows($users_query);
            
            // Query 2: Get unique user IDs who have logged in via mobile
            $mobile_users_query = "SELECT DISTINCT user_id 
                FROM user_mobile_details 
                WHERE company_id = '" . $company_id . "' 
                AND (is_active = 0 OR is_active IS NULL)
                AND user_id IS NOT NULL";
            
            $mobile_users_rows = (array)$callConfig->getAllRows($mobile_users_query);
            
            // Extract mobile user IDs into a simple array
            $mobile_user_ids = array();
            if (!empty($mobile_users_rows)) {
                foreach ($mobile_users_rows as $row) {
                    $mobile_user_ids[] = (int)$row->user_id;
                }
            }
            
            // Calculate counts
            $total_users = count($all_users);
            $mobile_users_count = count($mobile_user_ids);
            $needs_mobile_count = $total_users - $mobile_users_count;
            
            // Format user array with incremental keys (1-indexed for frontend compatibility)
            $formatted_users = array();
            $i = 1;
            foreach ($all_users as $user) {
                $formatted_users[$i] = array(
                    'id' => $user->id,
                    'display_name' => $user->display_name,
                    'user_email' => $user->user_email,
                    'user_mobile' => $user->user_mobile,
                    'role_id' => $user->role_id,
                    'user_activated' => $user->user_activated,
                    'current_status' => $user->current_status,
                    'last_logged_in_at' => $user->last_logged_in_at,
                    'user_type' => $user->user_type
                );
                $i++;
            }
            
            return [
                'status' => 'success',
                'data' => [
                    'all_users' => $formatted_users,
                    'mobile_user_ids' => $mobile_user_ids,
                    'total_users' => $total_users,
                    'mobile_users_count' => $mobile_users_count,
                    'needs_mobile_count' => $needs_mobile_count
                ],
                'message' => 'Data retrieved successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error retrieving data: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

	function togetmobilemanagerdashboard($get){
	 	global $callConfig;
	 	//echo "<pre>";
	 	$thisyearfirstmonthlastdate = date('Y')."-01-01";
	 	$thisyearfirstmonth = date('Y')."-01";
	 	$edate = date('Y')."-01-01";
	 	$endate = date('Y')."-12-31";
	 	$yy = date('Y');
		//new
		if($get['dash_date']!=""){
			$today_date = $get['dash_date'];
		}else{
			$today_date = date("Y-m-d");
		}
	 	
	 	
	 	$day = date("l", strtotime($today_date));

	 	$thismonthstartdate = date("Y-m-d",strtotime('first day of this month', time()));
	 	$thismonthenddate = date("Y-m-d",strtotime('last day of this month', time()));
	 	$thisyear = date("Y");
	 	$thismonth = date("m");
	 	$division_id = 1;
		//$thisyear = 2022;
		//$thismonth = 10;
		//Get Total Eployeees
	 	$sql ="SELECT id, display_name, email_id, employee_type FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

		//$query="SELECT id,display_name  FROM `employees` WHERE company_id = '".$get['company_id']."' and is_active =0";
	 	//$data['topquery'] = $sql;
	 	$empdatas = $callConfig->getAllRows($sql);
	 	$allemplist = array();
	 	$data['todayattendants']['total_emp'] =count((array)$empdatas);
	 	foreach($empdatas as $empdatas1){
	 		$data['todayattendants']['total_emp_ids'][] =$empdatas1;
	 		$allemplist[$empdatas1->id] = $empdatas1;
	 		if($empdatas1->employee_type == '1'){
	 			$data['todayattendants']['regular_emp_ids'][] =$empdatas1;
	 		}elseif($empdatas1->employee_type == '2'){
	 			$data['todayattendants']['contract_emp_ids'][] =$empdatas1;
	 		}

		}
		//Get Today Attendance Eployeees
	 	$attendancequery = "SELECT emp.id, emp.display_name,emp.email_id from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
	 	//$data['attendancequery'] = $attendancequery;
	 	$attendata = $callConfig->getAllRows($attendancequery);
	 	$data['todayattendants']['present'] =count((array)$attendata);
	 	// $attendata->empcnt;
	 	$data['todayattendants']['present_ids'] = array();
	 	foreach($attendata as $empdatas2){
	 		$data['todayattendants']['present_ids'][] =$empdatas2;
	 		unset($allemplist[$empdatas2->id]);
		}
		foreach($allemplist as $adsaaa){
			$onads[] =  $adsaaa;
		}
	 	$data['totalpressent'] = count((array)$attendata);
	 	$data['todayattendants']['absent_ids'] = $onads;
		//Get Today Planed Leaves Eployeees
	 	$leaves = "SELECT emp.id, emp.display_name,emp.email_id FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$today_date."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";
	 	//$data['leavesql'] = $leaves;
	 
	 	$leavesdata = $callConfig->getAllRows($leaves);
	 	$data['todayattendants']['planned_leave_ids'] = array();
	 	foreach($leavesdata as $empdatas3){
	 		$data['todayattendants']['planned_leave_ids'][] =$empdatas3;
		}
	 	$empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
	 	$attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0; 
	 	$data['todayattendants']['planned_leave'] =$empcnt;
	 	$data['todayattendants']['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
	 	$data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
	 	// Transport gateway 
	 	$data['todayattendants']['transport_getway'] = 0;
	 	$data['todayattendants']['transport_getway_ids'] = array();
	 	$query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type FROM transport_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$transport_getway_data = $callConfig->getAllRows($query);
	 	foreach($transport_getway_data as $transport_getway){
	 		$data['todayattendants']['transport_getway_ids'][] =$transport_getway;
		}
	 	$transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
	 	$data['todayattendants']['transport_getway'] =$transport_getway_cnt;

	 	// Transport Personal  gateway 
	 	$data['todayattendants']['transport_personal_gateway'] = 0;
	 	$data['todayattendants']['transport_personal_gateway_ids'] = array();
	 	$query = "SELECT tg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND tg.entry_date ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$transport_personal_gateway = $callConfig->getAllRows($query);
	 	foreach($transport_personal_gateway as $transport_getway){
	 		$data['todayattendants']['transport_personal_gateway_ids'][] =$transport_getway;
		}
	 	$transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
	 	$data['todayattendants']['transport_personal_gateway'] =$transport_personal_gateway_cnt;

	 	// Leave
	 	$data['todayattendants']['employee_leaves'] = 0;
	 	$data['todayattendants']['employee_leaves_ids'] = array();
	 	$query = "SELECT tg.id, emp.id, emp.emp_code, emp.display_name, emp.email_id FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$data['todayattendants']['employee_leaves_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;
	 	// stationery
	 	$data['todayattendants']['stationery'] = 0;
	 	$data['todayattendants']['stationery_ids'] = array();

	 	$query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get['company_id']."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$stationery_requests = $callConfig->getAllRows($query);
	 	foreach($stationery_requests as $row){
	 		$data['todayattendants']['stationery_ids'][] =$row;
		}
	 	$stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
	 	$data['todayattendants']['stationery'] = $stationery_requests_cnt;

	 	// assets
	 	$data['todayattendants']['assets'] = 0;
	 	$data['todayattendants']['assets_ids'] = array();

	 	$query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get['company_id']."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$asset_requests = $callConfig->getAllRows($query);
	 	foreach($asset_requests as $row){
	 		$data['todayattendants']['assets_ids'][] =$row;
		}
	 	$asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
	 	$data['todayattendants']['assets'] = $asset_requests_cnt;


	 	// attenmiss punch
	 	$data['todayattendants']['miss_punch'] = 0;
	 	$data['todayattendants']['miss_punch_ids'] = array();
	 	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.email_id FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0 AND emp.division_id='{$division_id}'";
	 	$employee_miss_data = $callConfig->getAllRows($query);
	 	foreach($employee_miss_data as $employee_leave){
	 		$data['todayattendants']['miss_punch_ids'][] =$employee_leave;
		}
	 	$miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
	 	$data['todayattendants']['miss_punch'] =$miss_cnt;
	 	/*$query = "SELECT * FROM `employee_leaves` WHERE company_id = ".$get['company_id']." AND `leave_from` ='".$today_date."' AND is_active = 0";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$data['todayattendants']['employee_leave_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;*/
	 	

		//Get regular,contact,male,female,manger_count,employees Count Eployeees
	 	$query="SELECT SUM(if(emp.employee_type='1',1,0))as regular, SUM(if(emp.employee_type='2',1,0))as contact, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";
		//$data['query1'] =$query;
	 	$nempdata = $callConfig->getRow($query);
	 	$data['nemp'] = $nempdata;

		//Get Joing Employees	
	 	$mequery= "SELECT count(emp.id) as joinn FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.division_id='{$division_id}' AND ( CASE WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy) = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."' AND is_active=0";
	 	//$data['joinsql'] =$mequery;
	 	$medata = $callConfig->getRow($mequery);	
	 	$data['joining'] = $medata->joinn;

		//Get leaving Employees	
	 	$mequery= "SELECT count(emp.id) as realving FROM employees emp WHERE emp.date_of_leaving >= '".$thismonthstartdate."' AND emp.date_of_leaving <= '".$thismonthenddate."' AND emp.date_of_leaving IS NOT NULL AND emp.company_id = '".$get['company_id']."' AND emp.division_id='{$division_id}' AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND  is_active=0";
	 	//$data['leaveingsql'] =$mequery;
	 	$medata1 = $callConfig->getRow($mequery);	
	 	$data['relive'] = $medata1->realving;

	 	$query="SELECT SUM(if(emp.gender='M',1,0)) as male, SUM(if(emp.gender='F',1,0))as female, count(DISTINCT(emp.reporting_manager_id)) as manger_count, COUNT(emp.id) as em_count FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 AND emp.division_id='{$division_id}' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END) ";
	 	//AND emp.joining_date >= '".$thismonthstartdate."' AND emp.joining_date <= '".$thismonthenddate."'
	 	//$data['thismonthdowsql'] =$query;
	 	$nempdata = $callConfig->getRow($query);
	 	$data['daterangenemp'] = $nempdata;	
	 	$sqqlgraph = "SELECT sum(m12) as j12,
	 	sum(m11) as j11,
	 	sum(m10) as j10,
	 	sum(m9) as j9,
	 	sum(m8) as j8,
	 	sum(m7) as j7,
	 	sum(m6) as j6,
	 	sum(m5) as j5,
	 	sum(m4) as j4,
	 	sum(m3) as j3,
	 	sum(m2) as j2,
	 	sum(m1) as j1,
	 	sum(lm12) as r12,
	 	sum(lm11) as r11,
	 	sum(lm10) as r10,
	 	sum(lm9) as r9,
	 	sum(lm8) as r8,
	 	sum(lm7) as r7,
	 	sum(lm6) as r6,
	 	sum(lm5) as r5,
	 	sum(lm4) as r4,
	 	sum(lm3) as r3,
	 	sum(lm2) as r2,
	 	sum(lm1) as r1
	 	FROM    ( 
	 	SELECT    DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, 
	 	DATE_FORMAT(emp.joining_date, '%Y-%m')dojm, 
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M12,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M11,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END M10,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M9,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M8,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M7,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M6,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M5,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M4,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M3,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M2,
	 	CASE WHEN DATE_FORMAT(emp.joining_date, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
	 	THEN 1
	 	END M1,
	 	DATE_FORMAT(emp.joining_date, '%Y-%m') monyr,
	 	DATE_FORMAT(emp.date_of_leaving, '%Y-%m')dolm, 
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM12,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM11,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) 
	 	THEN 1
	 	END LM10,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM9,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM8,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM7,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM6,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM5,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM4,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM3,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM2,
	 	CASE WHEN DATE_FORMAT(emp.date_of_leaving, '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' )
	 	THEN 1
	 	END LM1,
	 	DATE_FORMAT(emp.date_of_leaving, '%Y-%m') lmonyrl
	 	FROM     employees emp 
	 	WHERE    emp.company_id = 1 AND emp.division_id='{$division_id}'
	 	AND       (
	 	(emp.joining_date >= '".$thisyearfirstmonthlastdate."' AND emp.joining_date <= '".$endate."') 
	 	OR (emp.date_of_leaving >= '".$thisyearfirstmonthlastdate."' AND emp.date_of_leaving <= '".$endate."')
	 	)  
	 	AND emp.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = (SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'))END))a";

	 	$sqqlgraphres = $callConfig->getRow($sqqlgraph);
		//print_r($sqqlgraphres);
	 	//$data['graphjoinandleavesql'] = $sqqlgraph;
	 	foreach($sqqlgraphres as $sqkey=>$sqq){
	 		$passsqqlgraphres[]=array('id'=>$sqkey,'value'=>$sqq);
	 	}
	 	$data['graphjoinandleave'] = $passsqqlgraphres;
	 	$salarygraph ="SELECT sum(m12) as j12, sum(m11) as j11, sum(m10) as j10, sum(m9) as j9, sum(m8) as j8, sum(m7) as j7, sum(m6) as j6, sum(m5) as j5, sum(m4) as j4, sum(m3) as j3, sum(m2) as j2, sum(m1) as j1 FROM ( SELECT DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 12 MONTH), '%Y-%m' )curdt, DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m')dojm, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 11 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M12, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 10 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M11, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 9 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M10, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 8 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M9, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 7 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M8, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 6 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M7, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 5 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M6, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 4 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M5, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 3 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M4, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 2 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M3, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 1 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M2, CASE WHEN DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') = DATE_FORMAT( ('".$thisyearfirstmonthlastdate."' - INTERVAL 0 MONTH), '%Y-%m' ) THEN prsc.salary_component_actual END M1, DATE_FORMAT(emp.joining_date, '%Y-%m') monyr FROM employees emp, pay_run_summary prs, pay_run_summary_components prsc WHERE emp.company_id = 1 AND emp.id = prs.employee_id AND emp.company_id = prs.company_id AND prsc.company_id = prs.company_id AND prsc.pay_run_summary_id = prs.id AND prsc.employee_id = emp.id AND prsc.component_id = 1 AND emp.division_id='{$division_id}' AND (( DATE_FORMAT(CONCAT(prs.pay_year, '-', prs.pay_month, '-','01'), '%Y-%m') >= '".$thisyearfirstmonth."') AND ( DATE_FORMAT(CONCAT(prs.pay_year, '-',prs.pay_month, '-','01'), '%Y-%m') <= '".$thisyearfirstmonth."')) AND prsc.is_active = 0 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) ) a";
	 	$salarygraphres = $callConfig->getRow($salarygraph);
		//print_r($sqqlgraphres);
	 	foreach($salarygraphres as $sqkey=>$sqq){
	 		$passsalarygraphres[]=array('id'=>$sqkey,'value'=>$sqq);
	 	}
	 	$data['salarygraphres'] = $passsalarygraphres;
	 	return $data;
	}
	function backviewtogetmobilemanagerdashboard($get){
	 	global $callConfig;
	 	//echo "<pre>";
	 	$thisyearfirstmonthlastdate = date('Y')."-01-01";
	 	$thisyearfirstmonth = date('Y')."-01";
	 	$edate = date('Y')."-01-01";
	 	$endate = date('Y')."-12-31";
	 	$yy = date('Y');
		//new
	 	if($get['dash_date']!=""){
			$today_date = $get['dash_date'];
		}else{
			$today_date = date("Y-m-d");
		}
		// yesterday
		$yesterday = date("Y-m-d", strtotime($today_date . " -1 day"));
		$day = date("l", strtotime($today_date));
	 	$thismonthstartdate = date("Y-m-d",strtotime('first day of this month', time()));
	 	$thismonthenddate = date("Y-m-d",strtotime('last day of this month', time()));
	 	$thisyear = date("Y");
	 	$thismonth = date("m");

	 	$query = "SELECT division_id FROM employees WHERE (id='{$get['emp_id']}' OR user_id='{$get['user_id']}') AND company_id='{$get['company_id']}' AND is_active=0";

	 	$division_data = $callConfig->getRow($query);
	 	$division_whr = "";
	 	$division_id = 0;
	 	if($division_data->division_id!=''){
	 		$division_id = $division_data->division_id;
	 		$division_whr = " AND emp.division_id=".$division_data->division_id;
	 	}

		//$thisyear = 2022;
		//$thismonth = 10;
		//Get Total Eployeees
	 	$sql ="SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image,  emp.reporting_manager_id FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

		//$query="SELECT id,display_name  FROM `employees` WHERE company_id = '".$get['company_id']."' and is_active =0";
	 	//$data['topquery'] = $sql;
	 	$empdatas = $callConfig->getAllRows($sql);
	 	$allemplist = array();
	 	$data['todayattendants']['total_emp'] =count((array)$empdatas);
	 	$male_count = 0;
		$female_count = 0;
		$manager_ids = array();
	 	foreach($empdatas as $empdatas1){
	 		$data['todayattendants']['total_emp_ids'][] =$empdatas1;
	 		$allemplist[$empdatas1->id] = $empdatas1;
	 		if($empdatas1->employee_type == '1'){
	 			$data['todayattendants']['regular_emp_ids'][] =$empdatas1;
	 		}elseif($empdatas1->employee_type == '2'){
	 			$data['todayattendants']['contract_emp_ids'][] =$empdatas1;
	 		}

	 		if($empdatas1->gender == 'M'){
			    $male_count++;
			}elseif($empdatas1->gender == 'F'){
			    $female_count++;
			}
			if(!empty($empdatas1->reporting_manager_id)){
		        $manager_ids[$empdatas1->reporting_manager_id] = 1; // unique
		    }
		}
		$total_emp_count = count((array)$empdatas);
		$manager_count = count((array)$manager_ids);
		$employee_count = $total_emp_count - $manager_count;

		if($total_emp_count > 0){
		    $data['todayattendants']['manager_percentage'] = round(($manager_count / $total_emp_count) * 100, 2);
		    $data['todayattendants']['employee_percentage'] = round(($employee_count / $total_emp_count) * 100, 2);
		}else{
		    $data['todayattendants']['manager_percentage'] = 0;
		    $data['todayattendants']['employee_percentage'] = 0;
		}

		$data['todayattendants']['male_count'] = $male_count;
		$data['todayattendants']['female_count'] = $female_count;

		if($total_emp_count > 0){
		    $data['todayattendants']['male_percentage'] = round(($male_count / $total_emp_count) * 100, 2);
		    $data['todayattendants']['female_percentage'] = round(($female_count / $total_emp_count) * 100, 2);
		}else{
		    $data['todayattendants']['male_percentage'] = 0;
		    $data['todayattendants']['female_percentage'] = 0;
		}
		//Get Today Attendance Eployeees
	 	$attendancequery = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$today_date."' AND eda.is_active = emp.is_active group by emp.id";
	 	//$data['attendancequery'] = $attendancequery;
	 	$attendata = $callConfig->getAllRows($attendancequery);
	 	$today_count = count((array)$attendata);
	 	$data['todayattendants']['present'] =$today_count;
	 	// $attendata->empcnt;
	 	$data['todayattendants']['present_ids'] = array();
	 	foreach($attendata as $empdatas2){
	 		$data['todayattendants']['present_ids'][] =$empdatas2;
	 		unset($allemplist[$empdatas2->id]);
		}
		foreach($allemplist as $adsaaa){
			$onads[] =  $adsaaa;
		}
	 	$data['totalpressent'] = count((array)$attendata);
	 	$data['todayattendants']['absent_ids'] = $onads;

	 	//Get Yesterday Attendance Eployeees
	 	$query = "SELECT emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, '' as other from employees emp, employee_daily_attendances eda WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$yesterday."' AND eda.is_active = emp.is_active group by emp.id";
	 	$result = $callConfig->getAllRows($query);
	 	$yesterday_count = count((array)$result);
	 	$data['yesterdayattendants']['present'] =$yesterday_count;
		if($yesterday_count > 0){
		    $percent_change = (($today_count - $yesterday_count) / $yesterday_count) * 100;
		} else {
		    $percent_change = ($today_count > 0) ? 100 : 0; // handle divide by zero
		}

		$data['attendance_change_percent'] = round($percent_change, 2);

		//Get Today Planed Leaves Eployeees
	 	$prsday = date('Y-m-d', strtotime('+1 day', strtotime($today_date)));
	 	$leaves = "SELECT el.id AS leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other FROM `employees` emp, employee_leaves el WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') )ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$prsday."' AND el.leave_to >= '".$today_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";
	 	//echo $leaves ; exit;
	 	// $data['leavesql'] = $leaves;
	 
	 	$leavesdata = $callConfig->getAllRows($leaves);
	 	$data['todayattendants']['planned_leave_ids'] = array();
	 	foreach($leavesdata as $empdatas3){
	 		$empdatas3->type = 'employee_leaves';
	 		$data['todayattendants']['planned_leave_ids'][] =$empdatas3;
		}
	 	$empcnt = count((array)$leavesdata)!="0"?count((array)$leavesdata):0;
	 	$attendataempcnt =count((array)$attendata)!="0"?count((array)$attendata):0; 
	 	$data['todayattendants']['planned_leave'] =$empcnt;
	 	$data['todayattendants']['absent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);
	 	$data['totalabsent'] =count((array)$empdatas)-($empcnt+$attendataempcnt);

		$pending_leave_query = "SELECT el.id AS emp_leave_id, emp.id, emp.display_name, emp.email_id, emp.emp_code,emp.phone,emp.image, CONCAT(el.leave_from, ' - ', el.leave_to) as other, el.application_date FROM employees emp, employee_leaves el  WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1 ELSE 2 END ) = 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."')  OR (emp.reporting_manager_id = '".$get['user_id']."')  OR (emp.hr_buddy = '".$get['emp_id']."')  OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE ( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND el.current_status < '".ST_APPROVED."' AND el.is_active = 0";

		$pending_leave_data = $callConfig->getAllRows($pending_leave_query);
		$data['todayattendants']['pending_leave_ids'] = array();
		foreach($pending_leave_data as $pending_emp){
			$pending_emp->type = 'employee_leaves';
		    $data['todayattendants']['pending_leave_ids'][] = $pending_emp;
		}
		$data['todayattendants']['pending_leave_count'] = count((array)$pending_leave_data);

		$total_emp_count = count((array)$empdatas);
		$data['last_7_days'] = array();

		for($i = 1; $i <= 7; $i++){
		    $trend_date = date("Y-m-d", strtotime($today_date . " -".$i." day"));
		    $trend_key  = "day".$i;
		    // Present count
		    $attendancequery = "SELECT emp.id FROM employees emp, employee_daily_attendances eda
		        WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr
		        AND (CASE
		                WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
		                WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
		                ELSE 2
		            END
		        ) = 1
		        AND (CASE
		                WHEN '3' <> '1' THEN (
		                    ('1' = '".$get['admin_type']."')
		                    OR (emp.reporting_manager_id = '".$get['user_id']."')
		                    OR (emp.hr_buddy = '".$get['emp_id']."')
		                    OR (emp.finance_buddy = '".$get['emp_id']."')
		                )ELSE (
		                    emp.vendor_id = (
		                        SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'
		                    )) END
		        ) AND eda.company_id = emp.company_id AND eda.employee_id = emp.id AND eda.attendace_date = '".$trend_date."' AND eda.is_active = emp.is_active GROUP BY emp.id";

		    $attendance_data = $callConfig->getAllRows($attendancequery);
		    $present_count = count((array)$attendance_data);

		    // On leave count for that date
		    $next_day = date('Y-m-d', strtotime($trend_date . ' +1 day'));
		    $leavequery = "SELECT emp.id FROM employees emp, employee_leaves el
		        WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 $division_whr 
		        AND (CASE 
		                WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$endate."') THEN 1
		                WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0) THEN 1
		                ELSE 2
		            END
		        ) = 1
		        AND (CASE
		                WHEN '3' <> '1' THEN (
		                    ('1' = '".$get['admin_type']."')
		                    OR (emp.reporting_manager_id = '".$get['user_id']."')
		                    OR (emp.hr_buddy = '".$get['emp_id']."')
		                    OR (emp.finance_buddy = '".$get['emp_id']."')
		                )
		                ELSE (
		                    emp.vendor_id = (
		                        SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."'
		                    )) END
		        ) AND emp.company_id = el.company_id AND emp.id = el.employee_id AND (el.leave_from <= '".$next_day."' AND el.leave_to >= '".$trend_date."') AND el.current_status = '".ST_APPROVED."' AND el.is_active = 0";

		    $leave_data = $callConfig->getAllRows($leavequery);
		    $leave_count = count((array)$leave_data);

		    $absent_count = $total_emp_count - ($present_count + $leave_count);
		    if($absent_count < 0){
		        $absent_count = 0;
		    }

		    $data['last_7_days'][] = array(
		        'key'     => $trend_key,
		        'value'   => $trend_date,
		        'total'   => $total_emp_count,
		        'present' => $present_count,
		        'absent'  => $absent_count,
		        'on_leave'=> $leave_count
		    );
		}

		$resigned_employees_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.employee_leaving_date, COUNT(ea.id) AS asset_count
		FROM employees emp
		INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0 
		LEFT JOIN employee_assets ea ON ea.emp_id = emp.id AND ea.company_id = emp.company_id AND ea.is_active = 0 
		WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 GROUP BY emp.id,emp.display_name,emp.email_id,emp.gender,emp.employee_type,emp.emp_code,emp.phone,emp.image,emp.reporting_manager_id";

		$resigned_employees = $callConfig->getAllRows($resigned_employees_query);
		$data['resigned_employees'] = $resigned_employees;
		$data['resigned_employees_count'] = count((array)$resigned_employees);

		$is_holiday = 'N';
		$holiday_year = date('Y', strtotime($today_date));
		$holiday_query = "SELECT id FROM holidays  WHERE company_id = '".$get['company_id']."' AND holiday_year = '".$holiday_year."' AND holiday_date = '".$today_date."' AND is_active = 0";
		$holiday_result = $callConfig->getAllRows($holiday_query);
		if(count((array)$holiday_result)>0){
		    $is_holiday = 'Y';
		}
		$data['is_holiday'] = $is_holiday;

	 	// Transport gateway 
	 	$data['todayattendants']['transport_getway'] = 0;
	 	$data['todayattendants']['transport_getway_ids'] = array();
	 	$query = "SELECT tpg.id transport_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id, emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tpg.visit_purpose) as other, GROUP_CONCAT(emp1.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees emp ON (emp.id = tpg.driver_name OR emp.id=tpg.user_id) AND emp.company_id = tpg.company_id AND emp.is_active=0 $division_whr
            LEFT JOIN employees emp1 ON FIND_IN_SET(emp1.id, tpg.emp_ids) AND emp1.company_id = tpg.company_id AND emp1.division_id = '{$division_id}' AND emp1.is_active = 0
            WHERE  tpg.company_id={$get['company_id']} AND tpg.is_active=0 AND ( tpg.entry_date ='".$today_date."' OR tpg.exit_date ='".$today_date."' ) AND tpg.is_active = 0  GROUP BY tpg.id";
	 	$transport_getway_data = $callConfig->getAllRows($query);
	 	foreach($transport_getway_data as $transport_getway){
			$transport_getway->type = 'transport_gateway';
	 		$data['todayattendants']['transport_getway_ids'][] =$transport_getway;
		}
	 	$transport_getway_cnt = count((array)$transport_getway_data)!="0"?count((array)$transport_getway_data):0;
	 	$data['todayattendants']['transport_getway'] =$transport_getway_cnt;

	 	// Transport Personal  gateway 
	 	$data['todayattendants']['transport_personal_gateway'] = 0;
	 	$data['todayattendants']['transport_personal_gateway_ids'] = array();
	 	$query = "SELECT tg.id transport_personal_gateway_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, emp.gender, emp.phone, emp.image, emp.reporting_manager_id, emp.designation_id, emp.department_id, CONCAT('Visit Purpose : ', tg.visit_purpose) as other FROM transport_personal_gateway tg, employees emp WHERE emp.id = tg.user_id AND tg.company_id = ".$get['company_id']."  AND (tg.entry_date ='".$today_date."'  OR tg.exit_date ='".$today_date."') AND tg.is_active = 0 $division_whr";
	 	//$query = "SELECT * FROM `transport_personal_gateway` WHERE company_id = ".$get['company_id']." AND `entry_date` ='".$today_date."' AND is_active = 0";
	 	$transport_personal_gateway = $callConfig->getAllRows($query);
	 	foreach($transport_personal_gateway as $transport_getway){
	 		$transport_getway->type = 'transport_personal_gateway';
	 		$data['todayattendants']['transport_personal_gateway_ids'][] =$transport_getway;
		}
	 	$transport_personal_gateway_cnt = count((array)$transport_personal_gateway)!="0"?count((array)$transport_personal_gateway):0;
	 	$data['todayattendants']['transport_personal_gateway'] =$transport_personal_gateway_cnt;

	 	// Leave
	 	$data['todayattendants']['employee_leaves'] = 0;
	 	$data['todayattendants']['employee_leaves_ids'] = array();
	 	$query = "SELECT tg.id as employee_leave_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,  CONCAT(tg.leave_from, ' - ', tg.leave_to) as other, emp.phone, emp.image, emp.reporting_manager_id FROM employee_leaves tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.leave_from ='".$today_date."' AND tg.is_active = 0 $division_whr";
	 	$employee_leaves_data = $callConfig->getAllRows($query);
	 	foreach($employee_leaves_data as $employee_leave){
	 		$employee_leave->type = 'employee_leaves';
	 		$data['todayattendants']['employee_leaves_ids'][] =$employee_leave;
		}
	 	$transport_personal_gateway_cnt = count((array)$employee_leaves_data)!="0"?count((array)$employee_leaves_data):0;
	 	$data['todayattendants']['employee_leaves'] =$transport_personal_gateway_cnt;
		// stationery
	 	$data['todayattendants']['stationery'] = 0;
	 	$data['todayattendants']['stationery_ids'] = array();

	 	$query = "SELECT sr.id stationery_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id FROM stationery_requests sr, employees emp WHERE emp.id = sr.emp_id AND sr.company_id = ".$get['company_id']."  AND sr.request_date ='".$today_date."' AND sr.is_active = 0 $division_whr";
	 	$stationery_requests = $callConfig->getAllRows($query);
	 	foreach($stationery_requests as $row){
	 		$row->type = 'stationery_requests';
	 		$data['todayattendants']['stationery_ids'][] =$row;
		}
	 	$stationery_requests_cnt = count((array)$stationery_requests)!="0"?count((array)$stationery_requests):0;
	 	$data['todayattendants']['stationery'] = $stationery_requests_cnt;

	 	// assets
	 	$data['todayattendants']['assets'] = 0;
	 	$data['todayattendants']['assets_ids'] = array();

	 	$query = "SELECT ar.id asset_request_id, emp.id , emp.emp_code, emp.display_name, emp.email_id, emp.employee_type, '' as other, emp.phone, emp.image, emp.reporting_manager_id FROM asset_requests ar, employees emp WHERE emp.user_id = ar.originator AND ar.company_id = ".$get['company_id']."  AND ar.asset_required_start_date ='".$today_date."' AND ar.is_active = 0 $division_whr";
	 	$asset_requests = $callConfig->getAllRows($query);
	 	foreach($asset_requests as $row){
	 		$row->type = 'asset_requests';
	 		$data['todayattendants']['assets_ids'][] =$row;
		}
	 	$asset_requests_cnt = count((array)$asset_requests)!="0"?count((array)$asset_requests):0;
	 	$data['todayattendants']['assets'] = $asset_requests_cnt;

	 	// attenmiss punch
	 	$data['todayattendants']['miss_punch'] = 0;
	 	$data['todayattendants']['miss_punch_ids'] = array();
	 	$query = "SELECT tg.id as daily_attendances_id, emp.id, emp.emp_code, emp.display_name, emp.email_id,CONCAT('Clock : ', tg.clock_in, ' - ' , tg.clock_out ) as other, emp.phone, emp.image, emp.reporting_manager_id FROM employee_daily_attendances tg, employees emp WHERE emp.id = tg.employee_id AND tg.company_id = ".$get['company_id']." AND tg.attendace_date ='".$today_date."' AND `attendance_key_short_code` IN ('Z','MP') AND tg.is_active = 0 $division_whr";
	 	$employee_miss_data = $callConfig->getAllRows($query);
	 	foreach($employee_miss_data as $employee_leave){
	 		$employee_leave->type = 'employee_daily_attendances';
	 		$data['todayattendants']['miss_punch_ids'][] =$employee_leave;
		}
	 	$miss_cnt = count((array)$employee_miss_data)!="0"?count((array)$employee_miss_data):0;
	 	$data['todayattendants']['miss_punch'] =$miss_cnt;


	 	$approvals = array();

		$tables_array = array('transport_gateway','transport_personal_gateway','employee_leaves','employee_daily_attendances','asset_requests','stationery_requests');

		// make IN clause properly
		$table_list = "'" . implode("','", $tables_array) . "'";

		$query = "SELECT 
            tal.id AS transaction_approval_log_id,
            tal.transaction_table AS transaction_table,
            tal.transaction_id AS rid,
            COALESCE(emp1.display_name, '-') AS requested,
            COALESCE(emp2.display_name, '-') AS pending_approval,
            tal.previous_status AS current_status
          FROM transaction_approval_logs tal
          LEFT JOIN employees emp1 
                 ON tal.assigner_user_id = emp1.user_id
                AND emp1.company_id = tal.company_id
                AND emp1.is_active = 0
          LEFT JOIN employees emp2 
                 ON tal.user_id = emp2.user_id
                AND emp2.company_id = tal.company_id
                AND emp2.is_active = 0
          WHERE tal.response_type = 0
            AND tal.user_id = '{$get['user_id']}'
            AND tal.company_id = '{$get['company_id']}'
            AND tal.response_date IS NULL
            AND tal.transaction_table IN ($table_list)";

		$approval_data = $callConfig->getAllRows($query);
		// label mapping
		$labels = array(
		    'transport_gateway' => 'Transport Gateway',
		    'transport_personal_gateway' => 'Transport Personal Gateway',
		    'employee_leaves' => 'Employee Leaves',
		    'employee_daily_attendances' => 'Employee Daily Attendances',
		    'asset_requests' => 'Asset Requests',
		    'stationery_requests' => 'Stationery Requests'
		);
		$types = array(
		    'transport_gateway' => 'vehicle_gate_pass_info_by_id',
		    'transport_personal_gateway' => 'transport_personal_gateway_info_by_id',
		    'employee_leaves' => 'employee_leaves_info_by_id',
		    'employee_daily_attendances' => 'attendance_regularization_request_info_by_id',
		    'asset_requests' => 'asset_request_info_by_id',
		    'stationery_requests' => 'stationery_request_info_by_id'
		);

		foreach ($approval_data as $value) {
		    $transaction_table = $value->transaction_table;   // because selected as AS type
		    $rid  = $value->rid;

		    // initialize key if not exists
		    if (!isset($approvals[$types[$transaction_table]])) {
		        $approvals[$types[$transaction_table]] = array();
		    }

		    $approvals[$types[$transaction_table]][] = array(
		        'label' => isset($labels[$transaction_table]) ? $labels[$transaction_table] : ucwords(str_replace('_', ' ', $transaction_table)),
		        'requested' => $value->requested,
		        'pending_approval' => $value->pending_approval,
		        'approval_status' => $value->current_status, // or map separately if needed
		        'rid' => $rid,
		        'transaction_table' => $transaction_table,
		        'type' => $types[$transaction_table] ?? '',
		        'transaction_approval_log_id' => $value->transaction_approval_log_id
		    );
		}

		$data['ongoing_operations']['approval_data'] = $approvals;

		$query = "SELECT id,table_name,name,status,tigger,fixable,action_label,display_priority FROM status_progress WHERE company_id={$get['company_id']} AND is_active=0 AND table_name IN ($table_list)";
		$status_data = $callConfig->getAllRows($query);
		$status_arr = array();
		foreach ($status_data as $row) {
		    $status_arr[$types[$row->table_name]][$row->status] = $row;
		}
		$data['ongoing_operations']['status_arr'] = $status_arr;

		$query = "SELECT id,designation_name FROM designations WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$designation_arr = array();
		foreach ($res as $row) {
		    $designation_arr[$row->id] = $row;
		}
		$data['designation_arr'] = $designation_arr;

		$query = "SELECT id,department_name FROM departments WHERE company_id={$get['company_id']} AND is_active=0";
		$res = $callConfig->getAllRows($query);
		$department_arr = array();
		foreach ($res as $row) {
		    $department_arr[$row->id] = $row;
		}
		$data['department_arr'] = $department_arr;
	 	return $data;
	}
	function togetmobilemanagerviewtopemplist($get){
		global $callConfig;
		//print_r($get); exit;
		$res = $this->backviewtogetmobilemanagerdashboard($get);
		
		$field = $get['field'];
		$resdata = $res['todayattendants'][$field];
		$return['count'] = count($resdata);
		//echo $field;  print_r($resdata); echo "*********"; print_r($res); exit;
		$return['data'] = array_slice($resdata, $get['limit'], $get['lenght']);
		return $return;
	}
	function getPendingLeaveDetailsByEmpId($get){
	    global $callConfig;
	    $return = array();
	    $rid = $get['rid'] ?? '';
	    $table_name = $get['type'] ?? '';
	    $company_id = $get['company_id'] ?? '';
	    if($rid == '' || $table_name == ''){
	        $return['status'] = 0;
	        $return['message'] = 'rid or type missing';
	        $return['data'] = array();
	        return $return;
	    }
	    if($table_name == 'transport_gateway'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM transport_gateway t1, employees emp WHERE t1.id={$rid} AND t1.user_id=emp.user_id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query);
	    	$return['data'] = $result ?? array();
	    }
	    if($table_name == 'transport_personal_gateway'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM transport_personal_gateway t1, employees emp WHERE t1.id={$rid} AND t1.user_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query);
	    	$return['data'] = $result ?? array();
	    }
	    if($table_name == 'employee_leaves'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM employee_leaves t1, employees emp WHERE t1.id={$rid} AND t1.employee_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query);
	    	$return['data'] = $result ?? array();
	    }
	    if($table_name == 'stationery_requests'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM stationery_requests t1, employees emp WHERE t1.id={$rid} AND t1.emp_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query);
	    	$return['data'] = $result ?? array();

	    	$query = "SELECT sc.id, sc.qty, si.id as st_item_id, si.item_name, si.image, si.available_qty, si.minmum_qty FROM  stationery_cart sc, stationery_items si WHERE sc.request_no = '{$result->request_no}' AND sc.item_id=si.id AND sc.company_id={$company_id} AND sc.is_active=0";
	    	$result = $callConfig->getAllRows($query);
	    	$return['items_data'] = $result ?? array();

	    }
	    if($table_name == 'asset_requests'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM asset_requests t1, employees emp WHERE t1.id={$rid} AND t1.originator=emp.user_id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query);
	    	$return['data'] = $result ?? array();

	    	$query = "SELECT * FROM  asset_attributes att WHERE att.id IN ({$result->attribute}) AND att.company_id={$company_id} AND att.is_active=0";
	    	$result = $callConfig->getAllRows($query);
	    	$return['items_data'] = $result ?? array();

	    }
	    if($table_name == 'employee_daily_attendances'){
	    	$query = "SELECT t1.*, emp.emp_code, emp.display_name, emp.email_id, emp.phone, emp.image, emp.reporting_manager_id, emp.employee_type FROM employee_daily_attendances t1, employees emp WHERE t1.id={$rid} AND t1.employee_id=emp.id AND t1.company_id={$company_id} AND t1.is_active=0";
	    	$result = $callConfig->getRow($query) ?? array();
	    	$return['data'] = $result;

	    }
	    return $return;
	}

	function getResignedEmployeeAssetDetailsByEmpId($get){
	    global $callConfig;
	    $return = array();
	    $emp_id = $get['emp_id'] ?? '';
	    $company_id = $get['company_id'] ?? '';
	    if($emp_id == '' || $company_id == ''){
	        $return['status'] = 0;
	        $return['message'] = 'emp_id or company_id missing';
	        $return['employee'] = array();
	        $return['assets'] = array();
	        return $return;
	    }
	    $employee_query = "SELECT emp.id, emp.display_name, emp.email_id, emp.gender, emp.employee_type, '' as other, emp.emp_code, emp.phone, emp.image, emp.reporting_manager_id, et.id as termination_id, et.termination_type, et.termination_reason, et.notice_period_required, et.effective_date, et.noticeperiod_days, et.shortfalldays_in_noticeperiod, et.employee_leaving_date, et.exclude_final_settlement, et.remarks_by_employee, et.current_status
            FROM employees emp
            INNER JOIN employee_terminations et ON et.emp_id = emp.id AND et.company_id = emp.company_id AND et.is_active = 0 
            WHERE emp.company_id = '".$company_id."' AND emp.id = '".$emp_id."' AND emp.is_active = 0 ORDER BY et.id DESC LIMIT 1";
	    $employee_result = $callConfig->getRow($employee_query);

	    $assets_query = "SELECT ea.id, ea.emp_id, ea.asset_name, ea.description
	                    FROM employee_assets ea WHERE ea.company_id = '".$company_id."'  AND ea.emp_id = '".$emp_id."' AND ea.is_active = 0 ORDER BY ea.id DESC";
	    $assets_result = $callConfig->getAllRows($assets_query);
	    $return['status'] = 1;
	    $return['message'] = 'Resigned employee and asset details fetched successfully';
	    $return['employee'] = $employee_result ? $employee_result : array();
	    $return['asset_count'] = count((array)$assets_result);
	    $return['assets'] = $assets_result;
	    return $return;
	}
	function togetjobpostingdetails($get){
		global $callConfig;
		$query = "SELECT jp.id, jp.job_code, jp.job_number, jp.job_name, CASE jp.job_status WHEN 0 THEN 'Open' WHEN 1 THEN 'Close' WHEN 2 THEN 'Hold' ELSE 'Unknown' END AS job_status, CASE jp.employment_mode WHEN 1 THEN 'Full Time' WHEN 2 THEN 'Part Time' WHEN 3 THEN 'Distance Learning Program' WHEN 4 THEN 'Executive Program' WHEN 5 THEN 'Certification' ELSE 'Unknown' END AS employment_mode, jp.location_id, (SELECT loc.location_name FROM locations loc WHERE loc.id = jp.location_id ) AS location_name, requested_date, (SELECT COUNT(jps.id) FROM job_posting_candidates jps WHERE jps.job_posting_id = jp.id AND jps.is_active = 0 ) AS applicant FROM job_postings jp WHERE jp.company_id =".$get['company_id']." AND jp.is_active = 0";
		$rows = $callConfig->getAllRows($query);
		$return['data'] = $rows;
		return $return;
	}
	function togetjobpostingdetailsbyid($get){
		global $callConfig;
		//CAST(FROM_BASE64(jp.job_desc) AS CHAR) AS`job_desc`,
		$query = "SELECT 
				    jp.id,
				    jp.job_code,
				    jp.job_number,
				    jp.job_name,
					jp.`min_exp`,
				    jp.`max_exp`,
				    jp.`no_of_resources`,
				    jp.`min_salary`,
				    jp.`max_salary`,
				    jp.`video_url`,
				    jp.`total_resource_cost`,
				    jp.`job_responsibility`,
				    jp.`job_desc`,
				    CASE jp.job_status
				        WHEN 0 THEN 'Open'
				        WHEN 1 THEN 'Close'
				        WHEN 2 THEN 'Hold'
				        ELSE 'Unknown'
				    END AS job_status,

				    CASE jp.employment_mode
				        WHEN 1 THEN 'Full Time'
				        WHEN 2 THEN 'Part Time'
				        WHEN 3 THEN 'Distance Learning Program'
				        WHEN 4 THEN 'Executive Program'
				        WHEN 5 THEN 'Certification'
				        ELSE 'Unknown'
				    END AS employment_mode,
				    jp.location_id,
					(SELECT loc.location_name FROM locations loc WHERE loc.id = jp.location_id ) AS location_name,
				    requested_date,
				    (SELECT COUNT(jps.id) FROM job_posting_candidates jps WHERE jps.job_posting_id = jp.id AND jps.is_active = 0 )  AS applicant
				FROM job_postings jp WHERE jp.company_id =".$get['company_id']."  AND jp.id=".$get['job_post_id']."  AND jp.is_active = 0 ";
		//$return['query'] = $query;
		$rows = $callConfig->getRow($query);
		$return['data'] = $rows;
		$query = "SELECT 
				jpc.id,
			    jpc.`candidate_name`,
			    jpc.`candidate_email`,
			    jpc.`candidate_phone`,
			    jpc.`current_location`,
			    jpc.`preferred_location`,
			    jpc.`experience`,
			    jpc.`current_salary`,
			    jpc.`candidate_id`,
			    jpc.`created_date`,
			    jpc.`job_selection_process_step_id`,
			    
			    CASE
			        WHEN jpc.candidate_id IS NOT NULL THEN 'Selected'
			        WHEN jpc.job_selection_process_step_id IS NOT NULL THEN
			            (SELECT jsps.select_name
			             FROM job_selection_process_steps jsps
			             WHERE jsps.id = jpc.job_selection_process_step_id)
			        ELSE 'Screening'
			    END AS selection_status

			FROM job_posting_candidates jpc
			WHERE jpc.job_posting_id = ".$get['job_post_id']."  AND jpc.is_active = 0 ";

		$rows = $callConfig->getAllRows($query);
		//$return['can_query'] = $query;
		$return['candidates'] = $rows;
		return $return; 
	}
	function tocandidatedetailsbyid($get){
		global $callConfig;

		$query = "SELECT 
			    jpc.`candidate_name`,
			    jpc.`candidate_email`,
			    jpc.`candidate_phone`,
			    jpc.`current_location`,
			    jpc.`preferred_location`,
			    jpc.`experience`,
			    jpc.`current_salary`,
			    jpc.`candidate_id`,
			    jpc.`created_date`,
			    jpc.`job_selection_process_step_id`,
			    
			    CASE
			        WHEN jpc.candidate_id IS NOT NULL THEN 'Selected'
			        WHEN jpc.job_selection_process_step_id IS NOT NULL THEN
			            (SELECT jsps.select_name
			             FROM job_selection_process_steps jsps
			             WHERE jsps.id = jpc.job_selection_process_step_id)
			        ELSE 'Screening'
			    END AS selection_status

			FROM job_posting_candidates jpc
			WHERE jpc.id = ".$get['candidate_id']."  AND jpc.is_active = 0 ";

		$rows = $callConfig->getRow($query);
		//$return['can_query'] = $query;
		$return['candidates'] = $rows;
		return $return; 
	}
	function togettaskgrpsdetails($get){
		global $callConfig;
			$uewhr = "";
			if($get['role_short_code']!='TASKDA_ADMIN'){
				$uewhr = " AND id IN ( SELECT project_task_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active =0) ";
			}
			$query ="SELECT
			    COUNT(*) AS total_tasks,

			    SUM(CASE
			            WHEN current_status = 199 THEN 1
			            ELSE 0
			        END) AS completed_tasks,

			    SUM(CASE
			            WHEN current_status < 199 THEN 1
			            ELSE 0
			        END) AS pending_tasks,

			    SUM(CASE
			            WHEN current_status < 199
			             AND planned_finish < NOW() THEN 1
			            ELSE 0
			        END) AS overdue_tasks

			FROM project_task_details
			WHERE company_id = ".$get['company_id']."
			  ".$uewhr."
			  AND is_active = 0";
		$row['data'] = $callConfig->getRow($query);
		//$row['data_query'] = $query; 
		return $row;
	}
	function tgetcurrenttasklogdata($get){
		global $callConfig;
		$query = "SELECT ptd.task_name, ptd.planned_finish, CASE WHEN CURDATE() > ptd.planned_finish THEN 'Y' ELSE 'N' END AS overdue FROM project_task_details ptd WHERE company_id =1 AND id IN ( SELECT DISTINCT task_id FROM project_task_time_log WHERE company_id =1 AND user_id = 620 AND is_active = 0 ) AND current_status <199 AND is_active = 0 ORDER BY id";
		$row['data'] = $callConfig->getAllRows($query);
		//$row['data_query'] = $query; 
		return $row;
	}
	function toupdateprojecttaskteamremove($get, $post){
		
		global $callConfig;
		$res=$callConfig->updateRecord($get['table'],$post,'id',$get['rid']);

		$task_query = "SELECT id, task_name FROM project_task_details WHERE company_id = ".$get['company_id']."   AND id IN ( SELECT project_task_id FROM project_task_teams WHERE id=".$get['rid'].") AND  is_active = 0";
		$task_data = $callConfig->getRow($task_query);
		$query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get['company_id']." AND id IN ( SELECT user_id FROM project_task_teams WHERE id=".$get['rid'].") AND is_active =0";
			$user_data = $callConfig->getRow($query);
			$get['mobile'] = $user_data->user_mobile;

			$message = 'You have been unassigned from the task '.$task_data->task_name;

			$array = array('company_id'=>$get['company_id'], 'mobile_no'=>$get['mobile'], 'message'=>$message, 'send_no'=> '8179947020', 'recivce_no'=> $get['mobile'],'data_name'=>$get['data_name']);
			
			$res['msg_otp']['whatsup'] = $this->tosendwhatsappdata($array);

			
			$array['user_name'] = $user_data->display_name;
			$array['user_email_id'] = $user_data->user_email;
			$array['session_company_id'] = $user_data->company_id;
			$notarry = array();
			$emill = $this->sendNotificationAndEmails($notarry,$array,99);
		return $res;
	}


	//trippppppppppppppppppppppppppppppp

	function togetmobiletripdetails($get){
		//print_r($get); exit;
		global $callConfig;
        $return = array();
       
       /** if(isset($get['id']) && $get['id'] != ''){
            $query = "SELECT id,  `trip_name`, `trip_type`, `trip_purpose`, `project_id`, `customer_id`, `budget_amount`, `final_amount` FROM trips  WHERE  id={$get['id']} AND  company_id={$get['company_id']} AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['trip'] = $data;
 
            $query ="SELECT id, `trip_id`, `journey_type`, `user_id`, `trip_purpose`, `origin_location`, `destination_location`, `departure_date`, `return_date`, `travel_mode`, `estimated_cost` FROM domestic_round_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            $return['domestic_round_trips'] = $data;
 
            $query ="SELECT id,`trip_id`, `hotel_name`, `check_in`, `check_out`, `location`, `room_type`, `booking_reference`, `cost` FROM trip_hotels WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            $return['trip_hotels'] = $data;
 
            $query ="SELECT id, `trip_id`, `user_id`, `trip_purpose`, `trip_date`, `from_location`, `to_location`, `travel_mode`, `estimated_cost` FROM local_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            $return['local_trips'] = $data;
        }
 		 $query ="SELECT genc_detail_code, genc_detail_name, description, other as logo FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_TYPE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['trip_types'] = $data;

         $query ="SELECT id, display_name FROM customers WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['clients'] = $data;

         $query ="SELECT id, project_name FROM projects WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['projects'] = $data;

         $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_TYPE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['journey_types'] = $data;

         $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_LOCAL_VEHICLE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['local_modes'] = $data;
 
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_VEHICL' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['domestic_modes'] = $data;
        //$return['domestic_modes_query'] = $query;
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_QUICK_SUGGESTIO' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['trip_quick_suggestions'] = $data;  
       //$return['trip_quick_suggestions_query'] = $query;
        $query ="SELECT id, city_name, state_id FROM cities WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);

        $return['cities'] = $data;	**/
		$claimObj=new claimUserClass();
        $return = $claimObj->getTrevllingPlanData($get);
        $query ="SELECT id, city_name, state_id FROM cities WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);

        $return['cities'] = $data;
 		//echo "<pre>"; print_r($return);
        return $return;
	}
	function toinserttripexpencesdetails($get, $post){
		global $callConfig;

		$claimObj=new claimUserClass();
        $return = $claimObj->insertAndUpdateTrevllingPlans($get, $post);
		
		/**
		//echo "<pre>"; print_r($get); print_r($post); exit;
		$data = json_decode($post['data'],1);
		//echo "<pre>"; print_r($data); exit;
		$serial = $this->toGetApplicationSerialNumber('trips',$get);
		//$data =  $post;
		//echo "serial".$serial; exit;
		//print_r($data); exit;
		$insert_trip = array('company_id'=>$get['company_id'],'transaction_no'=>$serial,'user_id'=>$get['user_id'], 'transaction_date'=>date('Y-m-d'), 'budget_amount'=>'20000', 'trip_name' => $data['trip_name'], 'trip_type' => $data['trip_type'], 'trip_purpose' => $data['work_description'], 'project_id' => $data['project_id'], 'customer_id' => $data['client_id'],  'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'], 'api_data'=> json_encode($data));
		//print_r($insert_trip); exit;
		$res=$callConfig->insertRecord('trips',$insert_trip);
		//print_r($res); exit;
		$return = $res;
		$res['main_insert'] = $insert_trip;
		$trip_id =$res['LASTID'];
		$stops = $data['stops'];
		$rr=1;
		$array = array('flight_legs'=>'1','car_details'=>'2','bus_details'=>'3','own_vehicle_details'=>'4');
		foreach((array)$stops as $key=>$stop){
			$rr =$array[$key];
			foreach($stop as $val){ 
				//foreach($vals as $val){
				//print_r($vals); echo $key;  exit;
				if($key =='flight_legs' || ( isset($val['trip_type']) && $val['trip_type'] =='intercity') ){
					//echo "RRRRRR".$rr."--".$key; print_r($val);
					$insert_trip_dom = array('company_id'=>$get['company_id'], 'trip_id' => $trip_id, 'user_id' => $get['user_id'], 'journey_type' => $rr, 'origin_location' => $val['from_city_id'], 'destination_location' => $val['to_city_id'],'departure_date' => $val['departure_date'], 'departure_time' => $val['departure_time'],'return_date' => $val['return_date'], 'travel_mode' => $rr, 'return_time' => $val['return_time'],   'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
					$res['dom_insert'][] = $insert_trip_dom ;
					$res['dom'][]=$callConfig->insertRecord('domestic_round_trips',$insert_trip_dom);
				}else{
					//echo "CCCCC".$rr."--".$key; print_r($val);
					$departure_date = $val['departure_date'] !="" ? date('Y-m-d H:i:s', strtotime($val['departure_date']." ". $val['departure_time'])) : "";
					$insert_trip_loc = array('company_id'=>$get['company_id'], 'journey_type'=> $rr,'trip_id' => $trip_id, 'user_id' => $get['user_id'], 'trip_purpose' => $val['trip_purpose'], 'trip_date' => $val['departure_date'], 'from_location' => $val['from_city_id'],'to_location' => $val['to_city_id'],'travel_mode' => $rr, 'pickup_at' => $departure_date,'dropoff_at' => $val['dropoff_at'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
					$res['loc_insert'][] = $insert_trip_loc ;
					$res['loc'][]=$callConfig->insertRecord('local_trips',$insert_trip_loc);

				}
			}
			//}
			
		}
		foreach((array)$data['accommodations'] as $hotel){
			$rec = array(
                    'company_id'     => $get['company_id'],
                    'trip_id'        => $trip_id,
				    'hotel_name'     => $hotel['hotel_name'] ?? null,
				    'check_in'            => $hotel['check_in'] ,
				    'check_out'           => $hotel['check_out'],
				    'location'            => $hotel['city_id'] ?? null,
				    'description'         => $hotel['description'] ?? null,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'created_by'     => $user_id
                );
            $res['hotel'][] = $callConfig->insertRecord('trip_hotels', $rec);

		}
		$return['estimation'] = '20000';
		**/

		//print_r($return); exit;
		//return $res;
		return $return;
	}
	function togettripsdetails($get){
		global $callConfig;
		$query = "SELECT tp.*, ( SELECT pt.project_name FROM projects pt WHERE pt.id = tp.project_id) as project_name, ( SELECT cs.display_name FROM customers cs WHERE cs.id = tp.customer_id) as client_name FROM trips tp WHERE tp.company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND tp.is_active =0";
		$return['data'] = $callConfig->getAllRows($query);
		return $return;
	}
	function togettripdetailsbasedid($get){
		global $callConfig;
		$return['get'] = $get;

		
		
		/**$query = "SELECT id,  `trip_name`, transaction_no,  transaction_date, `trip_type`, `trip_purpose`, `project_id`, `customer_id`, `budget_amount`, `final_amount` FROM trips  WHERE    company_id=".$get['company_id']." AND id=".$get['trip_id']." AND is_active=0"; 
		//$return['query'][] = $query;
        $data = $callConfig->getRow($query);
        $return['trip'] = $data;
        $lis = array('1'=>'flight','2'=>'car','3'=>'bus');
 
        $query ="SELECT id, `trip_id`, `journey_type`, `user_id`, `trip_purpose`, `origin_location`, `destination_location`, `departure_date`, departure_time,`return_date`, return_time,`travel_mode`, `estimated_cost` FROM domestic_round_trips WHERE company_id=".$get['company_id']." AND  trip_id=".$get['trip_id']." AND is_active =0";
        //$return['query'][] = $query;
        $data = $callConfig->getAllRows($query);
        foreach ((array)$data as $row) {
               $stop[$lis[$row->journey_type]][] = $row;
        }
        $query ="SELECT id, `trip_id`, `user_id`, `trip_purpose`, `journey_type`, `trip_date` as departure_date, `from_location` as origin_location, `to_location` as destination_location, `travel_mode`, `estimated_cost`, `pickup_at`, `dropoff_at`, `car_type`, `driver_needed` FROM local_trips WHERE company_id=".$get['company_id']." AND  trip_id=".$get['trip_id']." AND is_active =0";
        //$return['query'][] = $query;
        $data = $callConfig->getAllRows($query);
        foreach ((array)$data as $row) {
               $stop[$lis[$row->journey_type]][] = $row;
        }
        $return['stops'] = $stop;
        $query ="SELECT id,`trip_id`, `hotel_name`, `check_in`, `check_out`, `location`, `room_type`, `booking_reference`, `cost` , description FROM trip_hotels WHERE company_id=".$get['company_id']." AND  trip_id=".$get['trip_id']." AND is_active =0";
        //$return['query'][] = $query;
        $data = $callConfig->getAllRows($query);
        $return['trip_hotels'] = $data;
        **/
        return $return;
	}
	function toupdatetripexpencesdetails($get, $post){
		global $callConfig;

		//echo "<pre>"; print_r($get); print_r($post); exit;
		$data = json_decode($post['data'],1);
		$trip_id = $get['trip_id'];
		$insert_trip = array('trip_name' => $data['trip_name'], 'trip_type' => $data['trip_type'], 'trip_purpose' => $data['work_description'], 'project_id' => $data['project_id'], 'customer_id' => $data['client_id'],  'updated_by'=>$get['user_id']);
		$res=$callConfig->updateRecord('trips',$insert_trip,'id',$trip_id);
		$stops = $data['stops'];
		$array = array('flight_legs'=>'1','car_details'=>'2','bus_details'=>'3','own_vehicle_details'=>'4');
		$upres=$callConfig->updateRecord('domestic_round_trips',array('is_active'=>1),'trip_id',$trip_id);
		$upres=$callConfig->updateRecord('local_trips',array('is_active'=>1),'trip_id',$trip_id);
		$upres=$callConfig->updateRecord('trip_hotels',array('is_active'=>1),'trip_id',$trip_id);

		foreach((array)$stops as $key=>$stop){
			$rr =$array[$key];
			foreach($stop as $val){ 
				if($key =='flight_legs' || ( isset($val['trip_type']) && $val['trip_type'] =='intercity') ){
					
					$insert_trip_dom = array('company_id'=>$get['company_id'], 'trip_id' => $trip_id, 'user_id' => $get['user_id'], 'journey_type' => $rr, 'origin_location' => $val['from_city_id'], 'destination_location' => $val['to_city_id'],'departure_date' => $val['departure_date'], 'departure_time' => $val['departure_time'],'return_date' => $val['return_date'], 'travel_mode' => $val['travel_mode'], 'return_time' => $val['return_time'],   'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
					$res['dom_insert'][] = $insert_trip_dom ;
					$res['dom'][]=$callConfig->insertRecord('domestic_round_trips',$insert_trip_dom);
				}else{
					//echo "CCCCC".$rr."--".$key; print_r($val);
					$departure_date = $val['departure_date'] !="" ? date('Y-m-d H:i:s', strtotime($val['departure_date']." ". $val['departure_time'])) : "";
					$insert_trip_loc = array('company_id'=>$get['company_id'], 'journey_type'=> $rr,'trip_id' => $trip_id, 'user_id' => $get['user_id'], 'trip_purpose' => $val['trip_purpose'], 'trip_date' => $val['departure_date'], 'from_location' => $val['from_city_id'],'to_location' => $val['to_city_id'], 'pickup_at' => $departure_date,'dropoff_at' => $val['dropoff_at'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
					$res['loc_insert'][] = $insert_trip_loc ;
					$res['loc'][]=$callConfig->insertRecord('local_trips',$insert_trip_loc);

				}
			}
			//}
			
		}
		foreach((array)$data['accommodations'] as $hotel){
			$rec = array(
                    'company_id'     => $get['company_id'],
                    'trip_id'        => $trip_id,
				    'hotel_name'     => $hotel['hotel_name'] ?? null,
				    'check_in'            => $hotel['check_in'] ,
				    'check_out'           => $hotel['check_out'],
				    'location'            => $hotel['city_id'] ?? null,
				    'description'         => $hotel['description'] ?? null,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'created_by'     => $user_id
                );
            $res['hotel'][] = $callConfig->insertRecord('trip_hotels', $rec);

		}
		$return['estimation'] = '20000';
		return $return;
	}
	
	function toaddprojecttaskdetails($get,$post){
		global $callConfig;		
		//echo "<pre>"; print_r($get); print_r($post); exit;
		$notification_message = '';
		$notuser2 = array(); $notuser1 = array();
		$post['is_active']=0;
		$post['current_status']=0;
		$post['created_by']=$get['user_id'];
		$res = $callConfig->insertRecord($get['table'],$post);	
		$upquery = $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date('Y-m-d')." WHERE id=".$post['transaction_id']);
		if(!isset($post['project_id'])){
			$task_data = $callConfig->getRow("SELECT * FROM project_task_details WHERE id=".$post['transaction_id']);
			$transaction_number =$task_data->project_id;
			$post['project_id'] = $task_data->project_id;
		}else{
			$transaction_number = $post['project_id'];
		}
		$assgnuser = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$get['user_id']);
		$msg1 = " Task ".$post['task_name']." is updated : Comments added  by ".$assgnuser->display_name;
		$msg2 = $msg1;
		$notuser1[] = $get['user_id'];
		$notuser1[] = $task_data->assigen_id;
		if(!isset($_POST['activity_type'])){
			$project = $callConfig->getRow("SELECT project_name,assigner_id FROM projects WHERE id=".$post['project_id']);
			$transaction_number = $post['project_id'];
			$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['assigen_id']);
			$insert_arr =  array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'],'project_task_id'=>$res['LASTID'], 'project_role_id'=>2,'user_id'=>$post['assigen_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$restwo = $callConfig->insertRecord('project_task_teams',$insert_arr);
			$insert_arr['project_role_id'] = 1;
			$insert_arr['user_id'] = $project->assigner_id;
			$restwo = $callConfig->insertRecord('project_task_teams',$insert_arr);
			$msg1 = "Task ".$post['task_name']." is assigned to you";
			$msg2 = "New task ".$post['task_name']." is created. Assign by ".$user->display_name;
			
		}
		$query = "SELECT user_id, ( SELECT user_mobile FROM application_users au WHERE au.id=user_id) as user_mobile FROM project_task_teams WHERE company_id =".$get['company_id']." AND project_task_id = ".$post['transaction_id']."  AND is_active = 0";
	
		$lists = $callConfig->getAllRows($query);
		//echo $query;
		foreach($lists as $list){
			$notuser2[] = $list->user_id;
		}
		//print_r($lists);
			foreach($lists as $list){

				$array = array('company_id'=>$get['company_id'], 'mobile_no'=>$list->user_mobile, 'message'=>$message, 'send_no'=> '8179947020', 'recivce_no'=>$list->user_mobile,'data_name'=>$get['data_name']);
				
				//$res['whatsup'] = $this->tosendwhatsappdatatodatain($array);
				//print_r($res); exit;
				if($post['activity_type']=='activity'){
					//echo "------"; exit;
					$mobilsendnotification = array();
					$mobilsendnotification = $get;
					$mobilsendnotification['message'] ="Someone has added a comment to the task ".$task_data->task_name;
					$mobilsendnotification['user_id'] =$list->user_id;
					$mobilsendnotification['title'] = 'Task Comment';
					$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['transaction_id'], 'project_id' =>$transaction_number, 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
					//print_r($mobilsendnotification); exit;
					$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);

				}

			}
			$email_content_id =37;
			$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$transaction_number, 'application_table_name'=>'project_task_details', 'type'=>'INFORMATION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_PROJECT_KANBAN&TAB=3&RID='.$transaction_number,'module_setup_id'=>25, 'whatsup'=>$whatsup);
			
			$ss = $this->tosendnotifiations($notdata1);
			//$rss = $this->tosendnotifiations($notdata2);
			$senddata = array('session_company_id'=>$get['company_id'],'email_content_id'=>$email_content_id, 'project_name'=>$project->project_name,'user_display_name'=>$user->display_name,'cc_users'=>$notuser2,'to_users'=>$notuser1,'task_name'=>$post['task_name'],'assigners_name'=>$assgnuser->display_name);
			//$res = $this->emailsendmediater($senddata);
			$message = "A new comment has been posted on the task: ".$task_data->task_name;
			$senddata['push_notiction'] = $mobres;
			return $senddata;
		
	}
	function toinsertupdateprojecttasktimlog($get,$post){
		global $callConfig;
		$crow=$callConfig->getRow("SELECT id,task_start,task_id FROM project_task_time_log WHERE  company_id = ".$get['company_id']." AND task_end is NULL AND is_active = 0 ORDER BY id DESC LIMIT 0,1"); 
		$upquery = $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date('Y-m-d')." WHERE id=".$post['task_id']);
		if($post['type'] == 'start'){
			// ------old data---- //
			
			if($crow->id !=""){
				$query = "UPDATE project_task_time_log SET task_end='".$post['starttime']."' WHERE  id=".$crow->id;
				$res['update_old_log']= $callConfig->executeQuery($query);
				$work_time = (strtotime($post['starttime'])-strtotime($crow->task_start) );
				$query = "UPDATE project_task_details SET work_time= work_time+".$work_time." WHERE company_id = ".$get['company_id']." AND id=".$crow->task_id." AND is_active = 0";
				$res['update_old_task']= $callConfig->executeQuery($query);
			}
			// -----old data end---- //
			$insert_array = array('company_id'=>$get['company_id'], 'task_id' => $post['task_id'], 'user_id' => $get['user_id'],'time_date'=>date('Y-m-d'),'task_start'=>$post['starttime'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);			
			$res = $callConfig->insertRecord('project_task_time_log',$insert_array);
		}
		if($post['type'] == 'pause'){
			$query = "UPDATE project_task_time_log SET task_end='".$post['endtime']."' WHERE company_id = ".$get['company_id']." AND task_id=".$post['task_id']." AND  task_end is NULL AND is_active = 0";
			$res['update_log_query'] = $query;
			$res['update_log']= $callConfig->executeQuery($query);
			$work_time = (strtotime($post['endtime']) - strtotime($crow->task_start));
			$res['data']= array('endtime'=>$post['endtime'],'task_start'=>$crow->task_start,'work_time'=>$work_time );
			$query = "UPDATE project_task_details SET work_time= work_time+".$work_time." WHERE company_id = ".$get['company_id']." AND id=".$post['task_id']." AND is_active = 0";
			$res['update_tassk_query'] = $query;
			$res['update']= $callConfig->executeQuery($query);

		}
		$task_query = "SELECT id, project_id, task_name FROM project_task_details WHERE id=".$post['task_id'];
		$task_data  = $callConfig->getRow($task_query);
		$task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND  project_task_id=".$post['task_id']." AND is_active=0";
		$teams  = $callConfig->getAllRows($task_team_q);
		//print_r($teams); exit;
		foreach($teams as $list){
		$mobilsendnotification = array();
		$mobilsendnotification = $get;
		$mobilsendnotification['message'] ="Someone has updated task time log ".$task_data->task_name;
		$mobilsendnotification['user_id'] =$list->user_id;
		$mobilsendnotification['title'] = 'Task log';
		$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['task_id'], 'project_id' =>$task_data->project_id, 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
		//print_r($mobilsendnotification); exit;
		$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);
		}
		return $res;		
	}
	 function adddataprojecttask($get,$post){
    	global $callConfig;
    	//print_r($post); exit;

    	$res = $this->insertTablesdata($post, $get['table']);
    	$post['task_id'] = $res['LASTID'];
    	$post['task_id'] = 100;
    	
    		$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'], 'project_task_id'=>$post['task_id'], 'project_role_id'=>1,'user_id'=>$post['assigen_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
			$insert = $callConfig->insertRecord('project_task_teams',$insert_array); 
			//print_r($insert_array);print_r($insert); exit;
			$task_query = "SELECT id, task_name, created_by FROM project_task_details WHERE company_id = ".$get['company_id']."   AND id =".$post['task_id']." AND  is_active = 0";
			$task_data = $callConfig->getRow($task_query);

			//print_r($task_data); echo $task_query; exit;
			$mobilsendnotification = array();
			$mobilsendnotification = $get;
			$mobilsendnotification['message'] ="Someone has assign new task ".$post['task_name'];
			$mobilsendnotification['user_id'] =$post['assigen_id'];
			$mobilsendnotification['title'] = 'New Task';
			$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['task_id'] , 'project_id' =>$post['project_id'] , 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
			//print_r($mobilsendnotification); echo $task_query; exit;
			$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);
			
			return $task_data;

			$query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get['company_id']." AND id = ".$post['assigen_id']." AND is_active =0";
			$user_data = $callConfig->getRow($query);
			$get['mobile'] = $user_data->user_mobile;

			$message = 'You have been assigned the task '.$task_data->task_name;

			$array = array('company_id'=>$get['company_id'], 'mobile_no'=>$get['mobile'], 'message'=>$message, 'send_no'=> '8179947020', 'recivce_no'=> $get['mobile'],'data_name'=>$get['data_name']);
			
			//print_r($mobilsendnotification); exit;
			
			//$res['msg_otp']['whatsup'] = $this->tosendwhatsappdata($array);
			//$mobilsendnotification = array('company_id'=>$notarry['company_id'],'message'=>$message,'user_id'=>$post['assigen_id'],'title'=>$revalue);
			

			


			$ce_query = "SELECT id, company_id,display_name, user_email, user_mobile FROM application_users WHERE company_id = ".$get['company_id']." AND id = ".$task_data->created_by." AND is_active =0";
			$asg_data = $callConfig->getRow($ce_query);

			$array['user_name'] = $user_data->display_name;
			$array['user_email_id'] = $user_data->user_email;
			$array['session_company_id'] = $user_data->company_id;
			$array['task_name'] =$task_data->task_name;
			$array['default_module_id']=25;
			$array['databasename']=$get['data_name'];
			$array['revicer'][] = $user_data->user_email;
			$array['user_display_name'] = $user_data->display_name;
			$array['assigners_name'] = $asg_data->display_name;
			$notarry = array();
			$notarry = array('company_id'=>$get['company_id'], 'notification_message'=> $message, 'transaction_number'=>$task_data->id, 'application_table_name'=>'project_task_details', 'type'=>'INFORMATION', 'notification_users'=>array($post['assigen_id']),'option'=>'','module_setup_id'=>25);
			//$emill = $this->sendNotificationAndEmails($notarry,$array,36);
			//echo "---qqq";print_r($emill); exit;
		return $res;
    }
    function updatedynamicfiedrojecttaskdetail( $get, $post){
		global $callConfig;
		if($post['field'] == "planned_start" || $post['field']=="planned_finish"){
			$post['fielddata'] = date('Y-m-d', strtotime(str_replace('/"', '-', $post['fielddata'])));
		}
		$query = "UPDATE ".$post['table_name']." SET ".$post['field']."='".$post['fielddata']."' WHERE id=".$post['update_id'];
		//echo $query; exit;
		$res = $callConfig->executeQuery($query);

		if($post['table_name']=='project_task_details'){
			$task_query = "SELECT id, project_id, task_name FROM project_task_details WHERE id=".$post['update_id'];
			$task_data  = $callConfig->getRow($task_query);
			$task_team_q = "SELECT id,  user_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND  project_task_id=".$post['update_id']." AND is_active=0";
			$teams  = $callConfig->getAllRows($task_team_q);
			//print_r($teams); exit;
			foreach($teams as $list){
				$mobilsendnotification = array();
				$mobilsendnotification = $get;
				$mobilsendnotification['message'] ="Someone has udated  to the task details ".$task_data->task_name;
				$mobilsendnotification['user_id'] =$list->user_id;
				$mobilsendnotification['title'] = 'Task Comment';
				$mobilsendnotification['data'] = array('module' => 'TASKDA', 'module_id' => '25', 'click_action' => 'OPEN_TASK','task_id' =>$post['transaction_id'], 'project_id' =>$task_data->project_id, 'event_type' => 'task_assign', 'application_table_name' => 'project_task_details');
				//print_r($mobilsendnotification); exit;
				$mobres = $this->tocallmobilenotificationmob($mobilsendnotification);
			}

		}
		//$work = $this->toinsertactivitydata($post,$get);
		return $res;
	}
	function toinsertactivitydata($post,$get){
		global $callConfig;
		$application_table_name = 'project_task_details';
		if($post['table_name']=='projects'){
			$application_table_name = $post['table_name'];
		}
		$comments = "Update this card ".str_replace("_"," ",$post['field'])." to be ".$post['fielddata'];
		$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>'project_task_details','transaction_id'=>$post['update_id'],'activity_type'=>'task_details','filed_name'=>$post['field'],'new_value'=>$post['fielddata'],'comments'=>$comments ,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('project_task_activities',$insert_array);



		//rohan added
		$passprojecttaskid = $this->passprojectTaskid($post, $get,$post['fielddata']);
		$taskowner = $passprojecttaskid['taskowner'];
		$projectteamuserlist = $passprojecttaskid['projectteamuserlist'];
		$taskfollower = $passprojecttaskid['taskfollower'];
		$taskdetails = $passprojecttaskid['taskdetails'];
		$projectteamuserowner =$projectteamuserlist['1'][0];
		$projectteamuserreviewer =$projectteamuserlist['3']; 
		$status_list = $passprojecttaskid['status_list']; 
		foreach($status_list as $status_lists){
			$statuslist[$status_lists->status] = $status_lists->name;
		}
		$notuser[$projectteamuserowner->id] = $projectteamuserowner->id; // projectowner
		foreach ($projectteamuserreviewer as $uid){
			$notuser[$uid->id] = $uid->id;
		}
		foreach ($taskfollower as $uid){
			$notuser[$uid->id] = $uid->id;
		}
		$notuser[$get['user_id']] = $get['user_id'];
		$datafiled = array('task_name');
		$msg1 =	"Task ".$taskdetails->task_name."  is updated : ".str_replace("_"," ",$post['field'])." is changed.";
		if(in_array($post['field'], $datafiled)){
			$msg1 =	"Task ".$taskdetails->task_name."  is updated : ".str_replace("_"," ",$post['field'])." ( ".$post['fielddata']." ) is changed.";
		}
		$msg1 = "Task ".$taskdetails->task_name." is assigned to ".$taskowner->display_name;
		$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$taskdetails->id, 'application_table_name'=>'project_task_details', 'type'=>'INFORMATION', 'notification_users'=>$notuser,'option'=>'COM_VIEW_PROJECT_KANBAN&RID='.$taskdetails->project_id,'module_setup_id'=>25);
		$ss = $this->tosendnotifiations($notdata1);	
		$owneuser[$taskowner->id] = $taskowner->id; // taskowner
		$notdata1['type'] = "ACTION";
		$notdata1['notification_users'] = $owneuser;
		//$ss = $this->tosendnotifiations($notdata1);
		$email_content_id =38;
		$senddata = array('session_company_id'=>$get['company_id'],'email_content_id'=>$email_content_id, 'project_name'=>$project->project_name,'user_display_name'=>$taskowner->display_name,'cc_users'=>$notuser,'to_users'=>$owneuser,'task_name'=>$taskdetails->task_name,'assigners_name'=>'','planned_start'=>$taskdetails->planned_start,'planned_finish'=>$taskdetails->planned_finish,'current_status'=>$statuslist[$taskdetails->current_status]);
		$res = $this->emailsendmediater($senddata);
		//rohan added
		return $res;
	}
	function passprojectTaskid($post,$get,$task_id){
		global $callConfig;
		$taskid = $post['update_id'];
		$projectteams = array();
		$projectteamuerlist = array();
		//if($post['field'] == "assigen_id"){
			$post['table'] =$post['table_name'];
			$return['status_list'] = $this->getStatusProgressList($post);

			$tasksql ="SELECT id, assigen_id, follower_ids, project_id, task_name, planned_start, planned_finish, current_status FROM project_task_details WHERE company_id = '".$get['company_id']."' AND id=".$post['update_id']." AND is_active = 0";
			$taskres = $callConfig->getRow($tasksql);
			$return['taskdetails'] = $taskres; 
			$projectsql ="SELECT role_id,user_id FROM project_teams WHERE company_id = '".$get['company_id']."' AND project_id=".$taskres->project_id." AND is_active = 0";
			$projectres = $callConfig->getAllRows($projectsql);
			foreach($projectres as $projectres){
				$projectteams[$projectres->user_id] = $projectres->user_id;
				$projectteamsrole[$projectres->user_id] = $projectres->role_id;
			}
			//$return['projectteamdetail'] = $projectres; 
			$projectteamsql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get['company_id']."' AND id IN(".implode(",", $projectteams).") AND is_active = 0";
			$projectteamres = $callConfig->getAllRows($projectteamsql);
			//$return['project_teams'] = $projectteamres; 
			foreach ($projectteamres as $projectteamre) {
				$projectteamuerlist[$projectteamsrole[$projectteamre->id]][] = $projectteamre;
			}
			$return['projectteamuserlist'] = $projectteamuerlist;
			$taskownerusersql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get['company_id']."' AND id=".$taskres->assigen_id." AND is_active = 0";
			$taskowneruser = $callConfig->getRow($taskownerusersql);
			$return['taskowner'] = $taskowneruser; 
			
			$taskfollowerrusersql ="SELECT id,display_name, user_email FROM application_users WHERE company_id = '".$get['company_id']."' AND id IN(".$taskres->follower_ids.") AND is_active = 0";
			$taskfollowerruserres = $callConfig->getAllRows($taskfollowerrusersql);
			$return['taskfollower'] = $taskfollowerruserres; 
		//}
		return $return;
	}
}  
?>