<?php 
class webViewClass extends userClass{
	function dynamicdatashowbasedontable($get){
		global $callConfig;
		$table_name = $get['table_name'];
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];

	    $sql = "SELECT id, page_title FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= '$table_name' AND is_active = 0 ";
	    $res = $callConfig->getRow($sql);
	   	$return['main_data'] = $res;

	   	$query = "SELECT * FROM $table_name t1 WHERE t1.originator=$user_id AND t1.company_id = $company_id AND t1.is_active = 0";
        //echo $query;
        $ress = $callConfig->getAllRows($query);
        $return['data'] = $ress;
 
        $get['table'] =$table_name;
        $status_progress = $this->getStatusProgressList($get);
        $return['status_progress'] = $status_progress;
 
        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, t1.transaction_number, t1.transaction_date, t1.total_amount, t1.party_name, t1.id
        FROM transaction_approval_logs tal, $table_name t1 WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = t1.company_id AND  tal.transaction_table = '$table_name' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = t1.id AND t1.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $return['transaction_approval_logs'] = $transapprlogres;
	   	return $return;

	}

	function getdynamicinfodata($get){
		global $callConfig;
		$return = array();
		$table_name = $get['table'];
		$rid = $get['rid'];
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];

        if(isset($rid) && $rid != ''){
        	$configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, billing_address, contact_address  FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
	        $configres=$callConfig->getRow($configsql);
	        $return['config'] = $configres;

        	$sql = "SELECT id, page_title,item_table, tax_table FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= '$table_name' AND item_table !='' AND tax_table!='' AND is_active = 0 ";
	    	$res = $callConfig->getRow($sql);
	    	$return['mapping_table'] = $res;
	    	$child_table = $res->item_table;
	    	$tax_table = $res->tax_table;

            $query = "SELECT t1.* FROM $table_name t1 WHERE t1.id=$rid  AND t1.company_id=$company_id AND t1.is_active=0";
            $data = $callConfig->getRow($query);
            $return['data'] = $data;
            if($child_table != ''){
            	$query = "SELECT t2.*, concat(uoms.uom_short_code,'-',uoms.uom_name) unit_name FROM $child_table t2 
            	LEFT JOIN  unit_of_measurements uoms ON uoms.id = t2.purchase_uom AND uoms.company_id=$company_id AND uoms.is_active=0
            	WHERE t2.transaction_id=$rid AND  t2.company_id=$company_id AND t2.is_active=0";
            	$data = $callConfig->getAllRows($query);
            	$return['items'] = $data;
            	//$return['items_sql'] = $query;
            }
            if($tax_table != ''){
            	$query = "SELECT t3.* FROM $tax_table t3 WHERE t3.transaction_id=$rid AND  t3.company_id=$company_id AND t3.is_active=0";
            	$data = $callConfig->getAllRows($query);

            	$return['taxes'] = $data;
            	//$return['taxes_sql'] = $query;
            }
        }
		return $return;

	}

	function getRegularizationRequestList($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];

		$sql = "SELECT id, page_title FROM dynamic_mapping_tables WHERE company_id = $company_id AND application_table_name= 'attendenace_regularization_requests' AND is_active = 0 ";
	    $res = $callConfig->getRow($sql);
	   	$return['main_data'] = $res;
 
        $get['table'] ='attendenace_regularization_requests';
        $status_progress = $this->getStatusProgressList($get);
        $return['status_progress'] = $status_progress;
 
        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, t1.request_date, t1.regularization_date, t1.regularization_of_checkout, t1.remarks, t1.id, emp.display_name
        FROM transaction_approval_logs tal, attendenace_regularization_requests t1
        LEFT JOIN employees emp ON emp.id = t1.employee_id AND emp.company_id = {$get['company_id']} AND emp.is_active=0
        WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = t1.company_id AND  tal.transaction_table = 'attendenace_regularization_requests' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = t1.id AND t1.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $return['transaction_approval_logs'] = $transapprlogres;

		$whr = '';
		if($get['emp_id'] != ''){
			$whr .= " AND employee_id={$get['emp_id']}";
		}
		$query ="SELECT arr.*, emp.display_name from attendenace_regularization_requests arr
			LEFT JOIN employees emp ON emp.id = arr.employee_id AND emp.company_id = arr.company_id AND emp.is_active=0
		 where arr.company_id={$company_id} AND arr.is_active=0 $whr";
	    $res = $callConfig->getAllRows($query);
	    $return['data'] = $res;

		return $return;
	}

	function togetRegularizationRequestById($get){
		global $callConfig;
		$return = array();
		$rid = $get['id'];
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];

        if(isset($rid) && $rid != ''){

            $query = "SELECT arr.*, emp.display_name, atrreason.reason_name  
            FROM attendenace_regularization_requests arr
            LEFT JOIN employees emp ON emp.id = arr.employee_id AND emp.company_id = arr.company_id AND emp.is_active=0
            LEFT JOIN attedenace_regularizaton_reasons atrreason ON atrreason.id = arr.regularization_reason AND atrreason.company_id = arr.company_id AND atrreason.is_active=0
            WHERE arr.id=$rid  AND arr.company_id=$company_id AND arr.is_active=0";
            $data = $callConfig->getRow($query);
            $return['data'] = $data;
        }
		return $return;
	}

	function toSaveDataEmployeeDailyAttendanceByRequestById($get){
		global $callConfig;
		$return = array();
		$rid = $get['transaction_id'];
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];

		if(isset($rid) && $rid!=''){
	    	$query ="select * from attendenace_regularization_requests where id={$rid} AND company_id={$company_id} AND is_active=0";
		    $data = $callConfig->getRow($query);

		    $query ="select id, attendace_date from employee_daily_attendances where attendace_date={$data->regularization_date} AND employee_id={$data->employee_id}  AND company_id={$company_id} AND is_active=0";
		    $data1 = $callConfig->getRow($query);

		    $attendanceData = array(
		        "company_id"      => $company_id,
		        "employee_id"     => $data->employee_id ?? null,
		        "attendace_date"  => $data->regularization_date ?? null,
		        "clock_in"        => $data->regularization_of_checkin ?? null,
		        "clock_out"       => $data->regularization_of_checkout ?? null,
		        "is_active"       => 0,
		        "current_status"  => 199
		    );

		    if($data1->id != ''){
		    	$attendanceData["updated_by"]   = $user_id;
	        	$attendanceData["updated_date"] = date("Y-m-d H:i:s");
	        	$res = $callConfig->updateRecord("employee_daily_attendances",$attendanceData,"id",$data1->id);
            	$return['res'] = $res;
		    }else{
		    	$attendanceData["created_by"]   = $user_id;
		        $attendanceData["created_date"] = date("Y-m-d H:i:s");
		        $res = $callConfig->insertRecord("employee_daily_attendances",$attendanceData);
            	$return['res'] = $res;		        
		    }
		    
		    $detailData = array(
		        "company_id"     => $company_id,
		        "employee_id"    => $data->employee_id ?? null,
		        "attendace_date" => $data->regularization_date ?? null,
		        "clock_in"       => $data->regularization_of_checkin ?? null,
		        "clock_out"      => $data->regularization_of_checkout ?? null,
		        "is_active"      => 0,
		        "current_status" => 0,
		        "created_by"     => $user_id,
		        "created_date"   => date("Y-m-d H:i:s")
		    );

		    $res1 = $callConfig->insertRecord("employee_daily_attendance_details",$detailData);
            $return['res1'] = $res1;
	    }
		return $return;
	}
	function togetTransportPersonalGatewayData($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		// return $get;

		$rid = $get['rid'];
        if(isset($rid) && $rid != ''){
            $query = "SELECT tpg.*, au.display_name, au.email_id as email FROM transport_personal_gateway tpg
            LEFT JOIN employees au ON au.id = tpg.user_id AND au.company_id = tpg.company_id AND au.is_active=0
            WHERE tpg.id=$rid  AND tpg.company_id=$company_id AND tpg.is_active=0";
            $data = $callConfig->getRow($query);
            $return['data'] = $data;
        }else{
	        $get['table'] ='transport_personal_gateway';
	        $status_progress = $this->getStatusProgressList($get);
	        $return['status_progress'] = $status_progress;
	        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, t1.visit_purpose, t1.location, t1.vehicle_number, t1.id, t1.entry_date, t1.entry_time, au.display_name, au.email_id as email, t1.person_details
	        FROM transaction_approval_logs tal, transport_personal_gateway t1
	        LEFT JOIN employees au ON au.id = t1.user_id AND au.company_id = {$get['company_id']} AND au.is_active=0
	        WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = t1.company_id AND  tal.transaction_table = 'transport_personal_gateway' AND tal.user_id = '".$user_id."' AND tal.transaction_id = t1.id AND t1.is_active = 0";
	        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
	        $return['transaction_approval_logs'] = $transapprlogres;

			$whr = '';
			if($user_id != ''){
				$whr .= " AND tpg.created_by={$user_id}";
			}
			$query ="SELECT tpg.*, au.display_name, au.email_id as email from transport_personal_gateway tpg
				LEFT JOIN employees au ON au.id = tpg.user_id AND au.company_id = tpg.company_id AND au.is_active=0
			 where tpg.company_id={$company_id} AND tpg.is_active=0 $whr";
		    $res = $callConfig->getAllRows($query);
		    $return['data'] = $res;
	    }
		return $return;
	}

	function togetTransportVehicleGatePassData($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		// return $get;

		$rid = $get['rid'];
        if(isset($rid) && $rid != ''){
            $query = "SELECT tpg.*, au.display_name, au.email_id as email, GROUP_CONCAT(emp.display_name) as emp_names FROM transport_gateway tpg
            LEFT JOIN employees au ON au.id = tpg.driver_name AND au.company_id = tpg.company_id AND au.is_active=0
            LEFT JOIN employees emp ON FIND_IN_SET(emp.id, tpg.emp_ids) AND emp.company_id = tpg.company_id AND emp.is_active = 0
            WHERE tpg.id=$rid  AND tpg.company_id=$company_id AND tpg.is_active=0 GROUP BY tpg.id";
            $data = $callConfig->getRow($query);

            $vehicle_reg_no = $data->vehicle_number ?? '';
            $data->vehicle_id = '';
            if($vehicle_reg_no != ''){
	            $query1 = "SELECT id FROM transport_vehicle_master WHERE vehicle_reg_no LIKE '%{$vehicle_reg_no}%' AND company_id={$company_id} AND is_active=0";
	            $data1 = $callConfig->getRow($query1);
	            $data->vehicle_id = $data1->id ?? '';
            }

            $return['data'] = $data;
        }else{
	        $get['table'] ='transport_gateway';
	        $status_progress = $this->getStatusProgressList($get);
	        $return['status_progress'] = $status_progress;
	 
	        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, t1.vehicle_number, t1.vehicle_type, t1.driver_name, t1.id, t1.driver_contact, t1.gate_pass, au.display_name, au.email_id as email, GROUP_CONCAT(emp.display_name) as emp_names
	        FROM transaction_approval_logs tal, transport_gateway t1
	        LEFT JOIN employees au ON au.id = t1.driver_name AND au.company_id = {$get['company_id']} AND au.is_active=0
	        LEFT JOIN employees emp ON FIND_IN_SET(emp.id, t1.emp_ids) AND emp.company_id = t1.company_id AND emp.is_active = 0
	        WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = t1.company_id AND  tal.transaction_table = 'transport_gateway' AND tal.user_id = '".$user_id."' AND tal.transaction_id = t1.id AND t1.is_active = 0 GROUP BY t1.id";
	        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
	        $return['transaction_approval_logs'] = $transapprlogres;

			$whr = '';
			if($user_id != ''){
				$whr .= " AND tpg.user_id={$user_id}";
			}
			$query ="SELECT tpg.*, au.display_name, au.email_id as email, GROUP_CONCAT(emp.display_name) as emp_names from transport_gateway tpg
				LEFT JOIN employees au ON au.id = tpg.driver_name AND au.company_id = tpg.company_id AND au.is_active=0
				LEFT JOIN employees emp ON FIND_IN_SET(emp.id, tpg.emp_ids) AND emp.company_id = tpg.company_id AND emp.is_active = 0
			 where tpg.company_id={$company_id} AND tpg.is_active=0 $whr GROUP BY tpg.id";
		    $res = $callConfig->getAllRows($query);
		    $return['data'] = $res;
	    }
		return $return;
	}
	function togetEmployeeLeaveData($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$employee_id = $get['employee_id'] ?? '';

		$rid = $get['rid'];
        if(isset($rid) && $rid != ''){
            $query = "SELECT el.*, emp.display_name, emp.email_id as email, lt.leave_type_name, lt.back_ground_color, lt.icon
            FROM employee_leaves el
            LEFT JOIN employees emp ON emp.id = el.employee_id AND emp.company_id = el.company_id AND emp.is_active=0
            LEFT JOIN leave_types lt ON lt.id = el.leave_type_id AND lt.company_id = el.company_id AND lt.is_active=0
            WHERE el.id=$rid AND el.company_id=$company_id AND el.is_active=0";
            $data = $callConfig->getRow($query);
            $return['data'] = $data;
        }else{
	        $get['table'] ='employee_leaves';
	        $status_progress = $this->getStatusProgressList($get);
	        $return['status_progress'] = $status_progress;
	        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, el.leave_from, el.leave_to, el.leave_days, el.id, el.leave_type_id, emp.display_name, emp.email_id as email, lt.leave_type_name, lt.back_ground_color, lt.icon
	        FROM transaction_approval_logs tal, employee_leaves el
	        LEFT JOIN employees emp ON emp.id = el.employee_id AND emp.company_id = el.company_id AND emp.is_active=0
	        LEFT JOIN leave_types lt ON lt.id = el.leave_type_id AND lt.company_id = el.company_id AND lt.is_active=0
	        WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = el.company_id AND tal.transaction_table = 'employee_leaves' AND tal.user_id = '".$user_id."' AND tal.transaction_id = el.id AND el.is_active = 0";
	        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
	        $return['transaction_approval_logs'] = $transapprlogres;

			$whr = '';
			if($employee_id != ''){
				$whr .= " AND el.employee_id={$employee_id}";
			}
			$query ="SELECT el.*, emp.display_name, emp.email_id as email, lt.leave_type_name, lt.back_ground_color, lt.icon from employee_leaves el
				LEFT JOIN employees emp ON emp.id = el.employee_id AND emp.company_id = el.company_id AND emp.is_active=0
				LEFT JOIN leave_types lt ON lt.id = el.leave_type_id AND lt.company_id = el.company_id AND lt.is_active=0
			 where el.company_id={$company_id} AND el.is_active=0 $whr ORDER BY el.id DESC";
		    $res = $callConfig->getAllRows($query);
		    $return['data'] = $res;
	    }
		return $return;
	}
	function togetStationeryRequestWebview($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$rid = $get['rid'] ?? '';

		if (isset($rid) && $rid != '') {
			$query = "SELECT sr.id, sr.request_no, sr.request_date, sr.require_date, sr.request_comment,
				sr.current_status, sr.user_id,
				emp.display_name AS employee_name,
				l.location_name
			FROM stationery_requests sr
			LEFT JOIN employees emp ON emp.user_id = sr.user_id AND emp.company_id = sr.company_id AND emp.is_active=0
			LEFT JOIN locations l ON l.id = sr.location_id AND l.company_id = sr.company_id AND l.is_active=0
			WHERE sr.company_id={$company_id} AND sr.id={$rid} AND sr.is_active=0";
			$data = $callConfig->getRow($query);
			$return['data'] = $data;

			$itemSql = "SELECT sc.id, sc.item_id, sc.qty, sc.color_id, sc.size_id, 
				si.item_name, si.category_id,
				scateg.name AS category_name
			FROM stationery_cart sc
			LEFT JOIN stationery_items si ON si.id = sc.item_id AND si.company_id = sc.company_id
			LEFT JOIN stationery_categories scateg ON scateg.id = si.category_id AND scateg.company_id = si.company_id
			WHERE sc.company_id={$company_id} AND sc.request_no='{$data->request_no}' AND sc.is_active=0";
			$items = $callConfig->getAllRows($itemSql);
			$return['items'] = $items;

			return $return;
		}

		$get['table'] = 'stationery_requests';
		$status_progress = $this->getStatusProgressList($get);
		$return['status_progress'] = $status_progress;

		$transapprlogsql = "SELECT tal.request_date, tal.response_date, tal.response_type,
			sr.request_no, sr.request_date, sr.require_date, sr.id,
			emp.display_name, emp.email_id as email
		FROM transaction_approval_logs tal, stationery_requests sr
		LEFT JOIN employees emp ON emp.user_id = sr.user_id AND emp.company_id = {$company_id} AND emp.is_active=0
		WHERE tal.company_id = {$company_id} AND tal.company_id = sr.company_id
			AND tal.transaction_table = 'stationery_requests'
			AND tal.user_id = '{$user_id}'
			AND tal.transaction_id = sr.id
			AND sr.is_active = 0";
		$transapprlogres = $callConfig->getAllRows($transapprlogsql);
		$return['transaction_approval_logs'] = $transapprlogres;

		$whr = '';
		if ($user_id != '') {
			$whr .= " AND sr.user_id={$user_id}";
		}
		$query = "SELECT sr.id, sr.request_no, sr.request_date, sr.require_date, sr.current_status,
			emp.display_name AS employee_name,
			l.location_name
		FROM stationery_requests sr
		LEFT JOIN employees emp ON emp.user_id = sr.user_id AND emp.company_id = sr.company_id AND emp.is_active=0
		LEFT JOIN locations l ON l.id = sr.location_id AND l.company_id = sr.company_id AND l.is_active=0
		WHERE sr.company_id={$company_id} AND sr.is_active=0 {$whr}
		ORDER BY sr.id DESC";
		$res = $callConfig->getAllRows($query);
		$return['data'] = $res;

		return $return;
	}
	function togetAssetRequestWebview($get){
		global $callConfig;
		
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$rid = $get['rid'] ?? '';

		if (isset($rid) && $rid != '') {
			$query = "SELECT ar.*, ac.category_description AS category_name, loc.location_name,
				au.display_name AS originator_name, au.user_email AS originator_email,
				apu.display_name AS approval_name, apu.user_email AS approval_email
			FROM asset_requests ar
			LEFT JOIN asset_categories ac ON ac.id = ar.category_id AND ac.company_id = ar.company_id AND ac.is_active=0
			LEFT JOIN locations loc ON loc.id = ar.location_id AND loc.company_id = ar.company_id AND loc.is_active=0
			LEFT JOIN application_users au ON au.id = ar.originator AND au.company_id = ar.company_id AND au.is_active=0
			LEFT JOIN application_users apu ON apu.id = ar.approval_user_id AND apu.company_id = ar.company_id AND apu.is_active=0
			WHERE ar.company_id={$company_id} AND ar.id={$rid} AND ar.is_active=0";
			$data = $callConfig->getRow($query);
			$return['data'] = $data;

			$attributes = array();
			$attribute_ids = array();
			if (!empty($data->attribute)) {
				$attribute_ids = array_filter(array_map('trim', explode(',', (string)$data->attribute)));
			}
			if (count($attribute_ids) > 0) {
				$attribute_ids = array_map('intval', $attribute_ids);
				$attr_ids = implode(',', $attribute_ids);
				$attr_query = "SELECT id, name FROM asset_attributes WHERE company_id={$company_id} AND is_active=0 AND id IN ({$attr_ids})";
				$attributes = $callConfig->getAllRows($attr_query);
			}
			$return['attributes'] = $attributes;

			return $return;
		}

		$get['table'] = 'asset_requests';
		$status_progress = $this->getStatusProgressList($get);
		$return['status_progress'] = $status_progress;

		$transapprlogsql = "SELECT tal.request_date, tal.response_date, tal.response_type,
			ar.request_datetime, ar.asset_required_start_date, ar.quantity, ar.id, ar.transaction_no,
			ac.category_description AS category_name,
			loc.location_name,
			au.display_name, au.user_email AS email
		FROM transaction_approval_logs tal, asset_requests ar
		LEFT JOIN asset_categories ac ON ac.id = ar.category_id AND ac.company_id = ar.company_id AND ac.is_active=0
		LEFT JOIN locations loc ON loc.id = ar.location_id AND loc.company_id = ar.company_id AND loc.is_active=0
		LEFT JOIN application_users au ON au.id = ar.originator AND au.company_id = ar.company_id AND au.is_active=0
		WHERE tal.company_id = {$company_id} AND tal.company_id = ar.company_id
			AND tal.transaction_table = 'asset_requests'
			AND tal.user_id = '{$user_id}'
			AND tal.transaction_id = ar.id
			AND ar.is_active = 0";
		$transapprlogres = $callConfig->getAllRows($transapprlogsql);
		$return['transaction_approval_logs'] = $transapprlogres;

		$whr = '';
		if ($user_id != '') {
			$whr .= " AND ar.originator={$user_id}";
		}
		$query = "SELECT ar.*, ac.category_description AS category_name,
			loc.location_name,
			au.display_name AS originator_name, au.user_email AS originator_email
		FROM asset_requests ar
		LEFT JOIN asset_categories ac ON ac.id = ar.category_id AND ac.company_id = ar.company_id AND ac.is_active=0
		LEFT JOIN locations loc ON loc.id = ar.location_id AND loc.company_id = ar.company_id AND loc.is_active=0
		LEFT JOIN application_users au ON au.id = ar.originator AND au.company_id = ar.company_id AND au.is_active=0
		WHERE ar.company_id={$company_id} AND ar.is_active=0 {$whr}
		ORDER BY ar.id DESC";
		$res = $callConfig->getAllRows($query);
		$return['data'] = $res;

		return $return;
	}
	function togetWorkFromHomeRequestWebview($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$rid = $get['rid'] ?? '';

		if (isset($rid) && $rid != '') {
			$query = "SELECT wfh.id, wfh.transaction_no, wfh.request_type, wfh.effective_from_date, 
				wfh.effective_to_date, wfh.number_days, wfh.special_request, wfh.current_status,
				wfh.status_date, wfh.created_date, wfh.created_by,
				emp.display_name AS employee_name, emp.email_id AS employee_email
			FROM request_work_from_home wfh
			LEFT JOIN employees emp ON emp.id = wfh.emp_id AND emp.company_id = wfh.company_id AND emp.is_active=0
			WHERE wfh.company_id={$company_id} AND wfh.id={$rid} AND wfh.is_active=0";
			$data = $callConfig->getRow($query);
			$return['data'] = $data;
			if (!$data) {
				return $return;
			}

			return $return;
		}

		$get['table'] = 'request_work_from_home';
		$status_progress = $this->getStatusProgressList($get);
		$return['status_progress'] = $status_progress;

		$transapprlogsql = "SELECT tal.request_date, tal.response_date, tal.response_type,
			wfh.transaction_no, wfh.effective_from_date, wfh.effective_to_date, wfh.number_days, wfh.id,
			emp.display_name AS employee_name, emp.email_id as email
		FROM transaction_approval_logs tal, request_work_from_home wfh
		LEFT JOIN employees emp ON emp.id = wfh.emp_id AND emp.company_id = {$company_id} AND emp.is_active=0
		WHERE tal.company_id = {$company_id} AND tal.company_id = wfh.company_id
			AND tal.transaction_table = 'request_work_from_home'
			AND tal.user_id = '{$user_id}'
			AND tal.transaction_id = wfh.id
			AND wfh.is_active = 0";
		$transapprlogres = $callConfig->getAllRows($transapprlogsql);
		$return['transaction_approval_logs'] = $transapprlogres;

		$whr = '';
		if ($user_id != '') {
			$whr .= " AND wfh.created_by={$user_id}";
		}
		$query = "SELECT wfh.id, wfh.transaction_no, wfh.request_type, wfh.effective_from_date, 
			wfh.effective_to_date, wfh.number_days, wfh.current_status, wfh.created_date,
			emp.display_name AS employee_name
		FROM request_work_from_home wfh
		LEFT JOIN employees emp ON emp.id = wfh.emp_id AND emp.company_id = wfh.company_id AND emp.is_active=0
		WHERE wfh.company_id={$company_id} AND wfh.is_active=0 {$whr}
		ORDER BY wfh.id DESC";
		$res = $callConfig->getAllRows($query);
		$return['data'] = $res;

		return $return;
	}
	function canAccessApprovalDiscussion($get){
		global $callConfig;
		$return = array('can_view' => false, 'can_post' => false);
		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$transaction_table = $get['transaction_table'];
		$transaction_id = $get['transaction_id'];

		if ($company_id == '' || $user_id == '' || $transaction_table == '' || $transaction_id == '') {
			return $return;
		}

		$transactionsql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$company_id."' AND id = '".$transaction_id."' AND is_active = 0";
		$transactionres = $callConfig->getRow($transactionsql);
		if (!$transactionres->id) {
			return $return;
		}

		$allowed_users = array();
		if ($transactionres->created_by != '') {
			$allowed_users[$transactionres->created_by] = 1;
		}
		if ($transactionres->originator != '') {
			$allowed_users[$transactionres->originator] = 1;
		}

		$wfget = array(
			'company_id' => $company_id,
			'transaction_table' => $transaction_table,
			'transaction_no' => $transaction_id,
			'current_status' => $transactionres->current_status
		);
		$wfres = $this->getDynamicWorkFlowSteps($wfget);
		if ($wfres['transaction_owner_id'] != '') {
			$allowed_users[$wfres['transaction_owner_id']] = 1;
		}
		if (count((array)$wfres['application_user_module_roles']) > 0) {
			foreach ((array)$wfres['application_user_module_roles'] as $role_user_id => $role_name) {
				$allowed_users[$role_user_id] = 1;
			}
		}

		$talsql = "SELECT user_id, assigner_user_id FROM transaction_approval_logs WHERE company_id = '".$company_id."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$transaction_id."'";
		$talres = $callConfig->getAllRows($talsql);
		if (count((array)$talres) > 0) {
			foreach ($talres as $talrow) {
				if ($talrow->user_id != '') {
					$allowed_users[$talrow->user_id] = 1;
				}
				if ($talrow->assigner_user_id != '') {
					$allowed_users[$talrow->assigner_user_id] = 1;
				}
			}
		}
		if (isset($transactionres->user_id) && $transactionres->user_id != '') {
			$allowed_users[$transactionres->user_id] = 1;
		}

		if (isset($transactionres->employee_id) && $transactionres->employee_id != '') {
			$empsql = "SELECT user_id FROM employees WHERE company_id = '".$company_id."' AND id = '".$transactionres->employee_id."' AND is_active = 0";
			$empres = $callConfig->getRow($empsql);
			if ($empres->user_id != '') {
				$allowed_users[$empres->user_id] = 1;
			}
		}


		$msgsql = "SELECT DISTINCT created_by FROM approval_discussion_messages WHERE company_id = '".$company_id."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$transaction_id."' AND is_active = 0";
		$msgres = $callConfig->getAllRows($msgsql);
		if (count((array)$msgres) > 0) {
			foreach ($msgres as $msgrow) {
				if ($msgrow->created_by != '') {
					$allowed_users[$msgrow->created_by] = 1;
				}
			}
		}

		if (isset($allowed_users[$user_id])) {
			$return['can_view'] = true;
			$return['can_post'] = true;
		}
		return $return;
	}

	function getApprovalDiscussionMessages($get){
		global $callConfig;
		$return = array('status' => 'success', 'can_view' => false, 'can_post' => false, 'messages' => array());

		$access = $this->canAccessApprovalDiscussion($get);
		$return['can_view'] = $access['can_view'];
		$return['can_post'] = $access['can_post'];
		if ($access['can_view'] != true) {
			$return['status'] = 'error';
			return $return;
		}

		$company_id = $get['company_id'];
		$user_id = $get['user_id'];
		$transaction_table = $get['transaction_table'];
		$transaction_id = $get['transaction_id'];

		$sql = "SELECT adm.id, adm.message_text, adm.created_date, adm.created_by, au.display_name AS sender_name
			FROM approval_discussion_messages adm
			LEFT JOIN application_users au ON au.id = adm.created_by AND au.company_id = adm.company_id AND au.is_active = 0
			WHERE adm.company_id = '".$company_id."'
			AND adm.transaction_table = '".$transaction_table."'
			AND adm.transaction_id = '".$transaction_id."'
			AND adm.is_active = 0";
		if ($get['last_message_id'] != '') {
			$sql .= " AND adm.id > '".$get['last_message_id']."'";
		}
		$sql .= " ORDER BY adm.id ASC";
		// $return['sql'] = $sql;
		
		$rows = $callConfig->getAllRows($sql);
		$messages = array();
		if (count((array)$rows) > 0) {
			foreach ($rows as $row) {
				$messages[] = array(
					'id' => $row->id,
					'message_text' => $row->message_text,
					'created_date' => $row->created_date,
					'created_by' => $row->created_by,
					'sender_name' => $row->sender_name != '' ? $row->sender_name : 'User',
					'is_current_user' => ($row->created_by == $user_id) ? 1 : 0
				);
			}
		}
		$return['messages'] = $messages;
		return $return;
	}

	function postApprovalDiscussionMessage($post){
		global $callConfig;
		$return = array('status' => 'error', 'message' => 'Unable to send message.');

		$post['user_id'] = $post['user_id'] != '' ? $post['user_id'] : $_GET['user_id'];
		$post['company_id'] = $post['company_id'] != '' ? $post['company_id'] : $_GET['company_id'];
		if ($post['module_id'] == '' && $_GET['module_id'] != '') {
			$post['module_id'] = $_GET['module_id'];
		}

		$access = $this->canAccessApprovalDiscussion($post);
		if ($access['can_post'] != true) {
			$return['message'] = 'You are not authorized to post in this discussion.';
			return $return;
		}

		$message_text = trim($post['message_text']);
		if ($message_text == '') {
			$return['message'] = 'Message cannot be empty.';
			return $return;
		}
		if (strlen($message_text) > 2000) {
			$return['message'] = 'Message is too long (max 2000 characters).';
			return $return;
		}

		$insert_array = array(
			'company_id' => $post['company_id'],
			'module_id' => $post['module_id'] != '' ? $post['module_id'] : null,
			'transaction_table' => $post['transaction_table'],
			'transaction_id' => $post['transaction_id'],
			'message_text' => $message_text,
			'is_active' => 0,
			'created_by' => $post['user_id'],
			'created_date' => date('Y-m-d H:i:s')
		);
		$res = $callConfig->insertRecord('approval_discussion_messages', $insert_array);
		if ($res['status'] != 'sucess') {
			$return['message'] = 'Failed to save message.';
			return $return;
		}

		$last_id = $res['LASTID'];
		$usersql = "SELECT display_name FROM application_users WHERE id = '".$post['user_id']."' AND company_id = '".$post['company_id']."' AND is_active = 0";
		$userres = $callConfig->getRow($usersql);
		$sender_name = $userres->display_name != '' ? $userres->display_name : 'User';

		$return['push_sent'] = $this->sendApprovalDiscussionMobilePush($post, $sender_name, $message_text);

		$return['status'] = 'success';
		$return['message'] = 'Message sent.';
		$return['data'] = array(
			'id' => $last_id,
			'message_text' => $message_text,
			'created_date' => $insert_array['created_date'],
			'created_by' => $post['user_id'],
			'sender_name' => $sender_name,
			'is_current_user' => 1
		);
		return $return;
	}
	function getApprovalDiscussionNotifyUsers($get){
		global $callConfig;
		$notify_users = array();
		$company_id = $get['company_id'];
		$transaction_table = $get['transaction_table'];
		$transaction_id = $get['transaction_id'];

		if ($company_id == '' || $transaction_table == '' || $transaction_id == '') {
			return $notify_users;
		}

		$transactionsql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$company_id."' AND id = '".$transaction_id."' AND is_active = 0";
		$transactionres = $callConfig->getRow($transactionsql);
		if (!$transactionres->id) {
			return $notify_users;
		}

		if ($transactionres->created_by != '') {
			$notify_users[$transactionres->created_by] = 1;
		}
		if ($transactionres->originator != '') {
			$notify_users[$transactionres->originator] = 1;
		}

		$wfget = array(
			'company_id' => $company_id,
			'transaction_table' => $transaction_table,
			'transaction_no' => $transaction_id,
			'current_status' => $transactionres->current_status
		);
		$wfres = $this->getDynamicWorkFlowSteps($wfget);
		if ($wfres['transaction_owner_id'] != '') {
			$notify_users[$wfres['transaction_owner_id']] = 1;
		}

		$talsql = "SELECT user_id, assigner_user_id FROM transaction_approval_logs WHERE company_id = '".$company_id."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$transaction_id."'";
		$talres = $callConfig->getAllRows($talsql);
		if (count((array)$talres) > 0) {
			foreach ($talres as $talrow) {
				if ($talrow->user_id != '') {
					$notify_users[$talrow->user_id] = 1;
				}
				if ($talrow->assigner_user_id != '') {
					$notify_users[$talrow->assigner_user_id] = 1;
				}
			}
		}

		if (isset($transactionres->user_id) && $transactionres->user_id != '') {
			$notify_users[$transactionres->user_id] = 1;
		}

		if (isset($transactionres->employee_id) && $transactionres->employee_id != '') {
			$empsql = "SELECT user_id FROM employees WHERE company_id = '".$company_id."' AND id = '".$transactionres->employee_id."' AND is_active = 0";
			$empres = $callConfig->getRow($empsql);
			if ($empres->user_id != '') {
				$notify_users[$empres->user_id] = 1;
			}
		}

		$msgsql = "SELECT DISTINCT created_by FROM approval_discussion_messages WHERE company_id = '".$company_id."' AND transaction_table = '".$transaction_table."' AND transaction_id = '".$transaction_id."' AND is_active = 0";
		$msgres = $callConfig->getAllRows($msgsql);
		if (count((array)$msgres) > 0) {
			foreach ($msgres as $msgrow) {
				if ($msgrow->created_by != '') {
					$notify_users[$msgrow->created_by] = 1;
				}
			}
		}

		return $notify_users;
	}
	function sendApprovalDiscussionMobilePush($post, $sender_name, $message_text){
		$push_sent = 0;
		$push_messages = array();
		$return = array();
		$recipients = $this->getApprovalDiscussionNotifyUsers($post);
		if (isset($recipients[$post['user_id']])) {
			unset($recipients[$post['user_id']]);
		}

		if (count((array)$recipients) == 0) {
			return $push_sent;
		}

		include_once("model/mobile_user.class.php");
		$mobObj = new mobileUserClass();

		$title = 'Approval Discussion';
		$body = $sender_name . ': ' . $message_text;
		if (strlen($body) > 120) {
			$body = substr($body, 0, 117) . '...';
		}

		if (count((array)$recipients) > 0) {
			foreach ($recipients as $recipient_id => $recipient_flag) {
				$push_messages[] = $mobObj->tocallmobilenotificationmob(array(
					'company_id' => $post['company_id'],
					'user_id' => $recipient_id,
					'title' => $title,
					'message' => $body
				));
				$push_sent++;
			}
		}
		$return['push_messages'] = $push_messages;
		$return['push_sent'] = $push_sent;

		return $return;
	}

}
?>