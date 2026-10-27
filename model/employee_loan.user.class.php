<?php

class employeeLoanClass extends userClass
{

	function getLoanEmployeesdata($get)	{
		global $callConfig;
		$return  = array();
		$company_id = $get['company_id'];
		$query = "
	        SELECT id, display_name 
	        FROM employees 
	        WHERE company_id = '{$company_id}' 
	          AND is_active = 0
	        ORDER BY display_name ASC";

		$rows = $callConfig->getAllRows($query);
		$return['employees'] = $rows;
		// ------------------------------------------
		// 2. If edit mode: we receive rid (loan_id)
		// ------------------------------------------
		if (!empty($get['rid']) && $get['rid'] > 0) {

			$loan_id = $get['rid'];

			// Fetch main loan details
			$loan_sql = "SELECT * FROM employee_loans
	            WHERE company_id = '{$company_id}'
	              AND id = '{$loan_id}'
	              AND is_active = 0
	            LIMIT 1";

			$loan_row = $callConfig->getRow($loan_sql);
			$return['loan'] = $loan_row ? (array)$loan_row : array();

			// ------------------------------------------
			// 3. Fetch installments for that loan
			// ------------------------------------------
			$inst_sql = "SELECT *
	            FROM employee_loan_installments
	            WHERE company_id = '{$company_id}'
	              AND loan_id = '{$loan_id}'
	              AND is_active = 0
	            ORDER BY installment_no ASC";

			$installments = $callConfig->getAllRows($inst_sql);
			$return['installments'] = $installments;
		}


		return $return;
	}
	function saveEmployeeLoan($get, $post)	{
		global $callConfig;

		$company_id = $get['company_id'];
		$user_id    = $get['user_id'];
		$loan_id    = $post['loan_id'] ?? 0;

		$employee_id   = $post['employee_id'];
		$loan_type     = $post['loan_type'];
		$principal     = $post['amount'];
		$total_amount  = $post['total_amount'];
		$start_date    = $post['start_date'];
		$months        = $post['no_of_months'];
		$interest_rate = $post['interest_rate'];
		$method        = $post['interest_method'];

		// ==============================
		// 1. Prepare loan data
		// ==============================
		$loanData = array(
			"company_id"       => $company_id,
			"employee_id"      => $employee_id,
			"loan_type"        => $loan_type,
			"principal_amount" => $principal,
			"total_amount"     => $total_amount,
			"interest_rate"    => $interest_rate,
			"interest_method"  => $method,
			"start_date"       => $start_date,
			"no_of_months"     => $months,
			"is_active"        => 0,
			"current_status"   => 0
		);

		// ==============================
		// 2. Update OR Insert loan
		// ==============================
		if ($loan_id > 0) {
			// UPDATE
			$loanData["updated_by"] = $user_id;
			$loanData["updated_date"] = date("Y-m-d H:i:s");
			$callConfig->updateRecord("employee_loans", $loanData, "id", $loan_id);

			// Remove old schedule
			$callConfig->executeQuery(
				"UPDATE employee_loan_installments 
	             SET is_active=NULL, updated_by=$user_id, updated_date=NOW() 
	             WHERE loan_id=$loan_id AND is_active=0"
			);
		} else {
			// INSERT
			$loanData["created_by"] = $user_id;
			$loanData["created_date"] = date("Y-m-d H:i:s");

			$resInsert = $callConfig->insertRecord("employee_loans", $loanData);
			$loan_id   = $resInsert['LASTID'];
		}

		// ==============================
		// 3. Insert New Installments
		// ==============================
		if (!empty($post['installment_no'])) {
			foreach ($post['installment_no'] as $idx => $no) {
				$instDate =  $post['installment_date'][$idx];
				$instAmt  = $post['installment_amount'][$idx];

				$insertInst = array(
					"company_id"         => $company_id,
					"loan_id"            => $loan_id,
					"installment_no"     => $no,
					"installment_date"   => $instDate,
					"installment_amount" => $instAmt,
					"created_by"         => $user_id,
					"created_date"       => date("Y-m-d H:i:s"),
					"is_active"          => 0,
					"current_status"     => 0
				);

				$callConfig->insertRecord("employee_loan_installments", $insertInst);
			}
		}
		return array("status" => "success", "loan_id" => $loan_id);
	}
	function employeeDeductions($get)	{
		global $callConfig;
		$return  = array();
		$company_id = $get['company_id'];
		$user_id    = $get['user_id'];
		$loan_id = $get['rid'];

		// Fetch main loan details
		$loan_sql = "SELECT * FROM employee_loans
            WHERE company_id = '{$company_id}'
              AND id = '{$loan_id}'
              AND is_active = 0
            LIMIT 1";

		$loan_row = $callConfig->getRow($loan_sql);

		if (!empty($loan_row)) {
			$inst_sql = "SELECT * FROM employee_loan_installments WHERE company_id = '{$company_id}'  AND loan_id = '{$loan_row->id}' AND is_active = 0 ORDER BY installment_no ASC";
			$installments = $callConfig->getAllRows($inst_sql);


			$query = "SELECT * FROM earnings WHERE code = '{$loan_row->loan_type}' AND company_id = '{$company_id}'AND is_active = 0 LIMIT 1";
			$earnings_row = $callConfig->getRow($query);

			foreach ($installments as $row) {
				$ts = strtotime($row->installment_date);
				$insertInst = array(
					"company_id" => $company_id,
					"pay_year"  => date('Y', $ts),
					"pay_month"  => date('m', $ts),
					"pay_area_id"   => 1,
					"employee_id" => $loan_row->employee_id ?? 0,
					"application_table_name"  => 'deductions',
					"type"   => 'deductions',
					"code" => $earnings_row->code ?? null,
					"type_id"     => $earnings_row->id ?? 0,
					"type_category"   => $earnings_row->category_id ?? 0,
					"claim_amount" => $row->installment_amount ?? 0,
					"approved_amount"  => $row->installment_amount ?? 0,
					"tax_type"   => $earnings_row->taxable ?? 1,
					"tax_value" => $earnings_row->taxable_limit ?? 0,
					"remarks"     => '',
					"filename"   => '',
					"created_by"  => $user_id,
					"created_date"   => date("Y-m-d H:i:s"),
					"status_date" => date("Y-m-d H:i:s"),
					"is_active"   => 0,
					"current_status"  => 0
				);

				$return['res'][] = $callConfig->insertRecord("employee_other_earnings", $insertInst);
			}
		}

		return $return;
	}

	// omkar related code start
	function getpersonalgatewaydetails($get){
	    global $callConfig;
	    $result = [];
	    $company_id  = (int) $get['company_id'];
	    $admin_type  = $get['admin_type'];
	    $employee_id = $get['employee_id'];
	    $rid = $get['rid'];
	    $query = "SELECT id, short_name,location_name FROM locations WHERE company_id = $company_id  AND is_active = 0";
	    $result['locations'] = $callConfig->getAllRows($query);
	    $query = "SELECT id, vehicle_reg_no, model, model_group, vehicle_type FROM transport_vehicle_master WHERE company_id = $company_id AND is_active = 0 ";
	    $result['transport_vehicle_master'] = $callConfig->getAllRows($query);
	    $userCondition = "";  
	    if ($admin_type != 1 && $employee_id > 0) {
	        $userCondition = " AND id = $employee_id ";
	    }
	    $query = "SELECT id, display_name, user_mobile, user_email FROM application_users WHERE company_id = $company_id AND is_active = 0 $userCondition ORDER BY display_name ASC ";
	    $result['query'] = $query;
	    $result['application_users'] = $callConfig->getAllRows($query);
	    $personalquery = "SELECT * FROM transport_personal_gateway WHERE company_id = $company_id AND id = $rid AND is_active = 0";
	    $result['personalquery'] = $personalquery;
	    $result['transport_personal_gateway'] = $callConfig->getRow($personalquery);

	    $result['gateway_types'] = array(['id'=> 1, 'type_name' => 'One Way'],
									['id'=> 2, 'type_name' => 'Return']);
	    $query = "SELECT id, emp_code, display_name, email_id, phone FROM employees WHERE company_id = $company_id AND is_active = '0' ORDER BY display_name ASC";
		$result['employees'] = $callConfig->getAllRows($query);
	    return $result;
	}

	function gettransportgatewaydetails($get)
	{
		global $callConfig;
		$return = [];
		$company_id = $get['company_id'];
		$query = "SELECT id, short_name, location_name FROM locations WHERE company_id = $company_id AND is_active = '0'";
		$return['query'] = $query;
		$result['result'] = $callConfig->getAllRows($query);

		// $query = "SELECT id, vehicle_reg_no, model, model_group, vehicle_type FROM transport_vehicle_master WHERE company_id = $company_id AND is_active = 0";
		$query = "SELECT m.id, m.vehicle_reg_no, m.model, m.model_group, m.vehicle_type FROM transport_vehicle_master m WHERE  m.vehicle_reg_no !='' AND vehicle_reg_no NOT IN ( SELECT vehicle_number FROM transport_gateway WHERE `process_val` =0 AND is_active = 0 ) AND m.company_id = $company_id AND m.is_active =0 AND m.vehicle_type='demo'";
		$result['transport_vehicle_master'] = $callConfig->getAllRows($query);

		$query = "SELECT m.id, m.vehicle_reg_no, m.model, m.model_group, m.vehicle_type FROM transport_vehicle_master m WHERE  m.vehicle_reg_no !='' AND vehicle_reg_no NOT IN ( SELECT vehicle_number FROM transport_gateway WHERE `process_val` =0 AND is_active = 0 ) AND m.company_id = $company_id AND m.is_active =0 AND m.vehicle_type='motorcycle'";
		$result['motor_cycle_list'] = $callConfig->getAllRows($query);
 
		$query = "SELECT id, emp_code, display_name, phone as user_mobile, email_id as user_email FROM employees WHERE company_id = $company_id AND is_active = '0' ORDER BY display_name ASC";
		$result['application_users'] = $callConfig->getAllRows($query);

		return $result;
	} 


	function gettransportvehiclemasterlist($get)
    {
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
 
        $statusQuery = "SELECT id, name, name_value, background_color, color FROM status 
                        WHERE company_id = " . $company_id . " 
                        AND is_active = 0";
        $statusList = $callConfig->getAllRows($statusQuery);
        $return['status_list'] = $statusList;

        $where = "company_id = " . $company_id;
          
        if (isset($get['is_active']) && $get['is_active'] !== '') {
            $where .= " AND is_active = " . intval($get['is_active']);
        } else { 
            $where .= " AND is_active IS NOT NULL";
        }
 
        if (!empty($get['from_date']) && !empty($get['to_date'])) {
            $from_date = $get['from_date'];
            $to_date = $get['to_date'];
            $where .= " AND DATE(created_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
        }
 
        $query = $callConfig->selectQuery('transport_vehicle_master', '*', $where, 'vehicle_reg_no ASC', '', '');
        $vehicles = $callConfig->getAllRows($query);

        $return['vehicles'] = $vehicles;
        $return['query'] = $query;

        return $return;
    }

	function getpersonalgatewaylist($get)	
    {
        global $callConfig;
        $return['get'] = $get;
 
        $company_id = $get['company_id'] ?? $_SESSION['company_id'];
        $work_admin_type = $get['work_admin_type'] ?? '';
        $employee_id = $get['employee_id'] ?? '';
       
        $where = "company_id = " . $company_id . " AND is_active = 0";
 
        if ($work_admin_type != 1 && $employee_id > 0) {
            $where .= " AND JSON_UNQUOTE(JSON_EXTRACT(person_details, '$.employee_id')) = '" . $employee_id . "'";
        }
 
        if (!empty($get['from_date']) && !empty($get['to_date'])) {
            $where .= " AND (
                (entry_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "')
                OR (exit_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "')
            )";
        } elseif (!empty($get['from_date'])) {
            $where .= " AND (entry_date >= '" . $get['from_date'] . "' OR exit_date >= '" . $get['from_date'] . "')";
        } elseif (!empty($get['to_date'])) {
            $where .= " AND (entry_date <= '" . $get['to_date'] . "' OR exit_date <= '" . $get['to_date'] . "')";
        }
 
        $limit = !empty($get['limit']) ? $get['limit'] : '';
        $query = $callConfig->selectQuery("transport_personal_gateway", '*', $where, 'created_date DESC', '', $limit);
        $entries = $callConfig->getAllRows($query);
         
        $formatted_entries = [];
        if ($entries && is_array($entries)) {
            foreach ($entries as $entry) {
                $personData = [];
                if (!empty($entry->person_details)) {
                    $personData = json_decode($entry->person_details, true) ?: [];
                }
                $formatted_entries[] = [
                    'id' => $entry->id,
                    'employee_id' => $personData['employee_id'] ?? '',
                    'employee_name' => $personData['employee_name'] ?? '',
                    'employee_contact' => $personData['employee_contact'] ?? '',
                    'employee_email' => $personData['employee_email'] ?? '',
                    'vehicle_number' => $entry->vehicle_number,
                    'visit_purpose' => $entry->visit_purpose,
                    'location' => $entry->location,
                    'notes' => $entry->notes,
                    'entry_date' => $entry->entry_date,
                    'entry_time' => $entry->entry_time,
                    'exit_date' => $entry->exit_date,
                    'exit_time' => $entry->exit_time,
                    'exit_timestamp' => $entry->exit_timestamp,
                    'method_type' => $entry->method_type ?? 'entry_first',
                    'current_status' => $entry->current_status,
                    'gateway_type' => $entry->gateway_type
                ];
            }
        }
        $return['entries'] = $formatted_entries;
 
        return $return;
    }

	// function getVehicleGatewayList($get)
	// {
	// 	global $callConfig;
	// 	$return['get'] = $get;

	// 	$company_id = $get['company_id'] ?? $_SESSION['company_id'];
	// 	$where = "company_id = " . $company_id . " AND is_active = 0";
 
	// 	if (!empty($get['from_date']) && !empty($get['to_date'])) {
	// 		$where .= " AND ((entry_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "') OR (exit_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'))";
	// 	} elseif (!empty($get['from_date'])) {
	// 		$where .= " AND (entry_date >= '" . $get['from_date'] . "' OR exit_date >= '" . $get['from_date'] . "')";
	// 	} elseif (!empty($get['to_date'])) {
	// 		$where .= " AND (entry_date <= '" . $get['to_date'] . "' OR exit_date <= '" . $get['to_date'] . "')";
	// 	}

	// 	$limit = !empty($get['limit']) ? $get['limit'] : '';
	// 	$query = $callConfig->selectQuery("transport_gateway", '*', $where, 'created_date DESC', '', $limit);
	// 	$rows = $callConfig->getAllRows($query);
		 
	// 	$vehicles = array();
	// 	foreach ($rows as $row) {
	// 		$vehicles[] = array(
	// 			'id' => $row->id,
	// 			'vehicle_number' => $row->vehicle_number,
	// 			'vehicle_type' => $row->vehicle_type,
	// 			'driver_name' => $row->driver_name,
	// 			'driver_contact' => $row->driver_contact,
	// 			'visit_purpose' => $row->visit_purpose,
	// 			'visiting_person' => $row->visiting_person ?? '',
	// 			'location' => $row->location ?? '',
	// 			'method_type' => $row->method_type ?? 'entry_first',
	// 			'entry_date' => $row->entry_date,
	// 			'entry_time' => $row->entry_time,
	// 			'entry_timestamp' => $row->entry_timestamp,
	// 			'exit_date' => $row->exit_date,
	// 			'exit_time' => $row->exit_time,
	// 			'exit_timestamp' => $row->exit_timestamp,
	// 			'current_status' => $row->current_status
	// 		);
	// 	}
	// 	$return['vehicles'] = $vehicles;

	// 	return $return;
	// }


	// function gettransportvehiclemasterlist($get)
    // {
    //     global $callConfig;
    //     $return = array();
    //     $company_id = $get['company_id'];
    //     $statusQuery = "SELECT id, name, name_value, background_color, color FROM status WHERE company_id = " . $company_id . "  AND is_active = 0";
    //     $statusList = $callConfig->getAllRows($statusQuery);
    //     $return['status_list'] = $statusList;

    //     $where = "company_id = ".$company_id;
    //     if (isset($get['is_active']) && $get['is_active'] !== '') {
    //         $where .= " AND is_active = " . intval($get['is_active']);
    //     } else { 
    //         $where .= " AND is_active IS NOT NULL";
    //     }
 
    //     if (!empty($get['from_date']) && !empty($get['to_date'])) {
    //         $from_date = $get['from_date'];
    //         $to_date = $get['to_date'];
    //         $where .= " AND DATE(created_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
    //     }

    //     $query = $callConfig->selectQuery('transport_vehicle_master', '*', $where, 'vehicle_reg_no ASC', '', '');
    //     $vehicles = $callConfig->getAllRows($query);

    //     $return['vehicles'] = $vehicles;
    //     $return['query'] = $query;

    //     return $return;
    // }

	function getpersonalgatewayold($get)
    {
        global $callConfig;
        $return = [];
 
        try {
            $company_id = $get['company_id'] ?? $_SESSION['company_id'];
            $today = date('Y-m-d');
 
            $work_admin_type  = $get['work_admin_type'];
            $employee_id = $get['employee_id'];
           
            $employeeCondition = "";
           
            if ($work_admin_type != 1 && $employee_id > 0) {
                $employeeCondition = "
                    AND JSON_UNQUOTE(
                        JSON_EXTRACT(person_details, '$.employee_id')
                    ) = '{$employee_id}'
                ";
            }
 
            $current_query = "
                SELECT *
                FROM transport_personal_gateway
                WHERE company_id = {$company_id}
                AND is_active = 0
                {$employeeCondition}
                AND (current_status IN ('inside', 'outside', '0', 0) OR current_status IS NULL)
                AND (
                    (method_type = 'entry_first' AND (exit_time IS NULL OR exit_time = '' OR exit_time = '00:00:00'))
                    OR
                    (method_type = 'exit_first' AND (entry_time IS NULL OR entry_time = '' OR entry_time = '00:00:00'))
                    OR
                    (method_type IS NULL AND (exit_time IS NULL OR exit_time = '' OR exit_time = '00:00:00'))
                )
                ORDER BY created_date DESC
            ";
            $current_persons = $callConfig->getAllRows($current_query);
           
            // Parse person_details for each entry
            $formatted_persons = [];
            if ($current_persons && is_array($current_persons)) {
                foreach ($current_persons as $person) {
                    $personData = [];
                    if (!empty($person->person_details)) {
                        $personData = json_decode($person->person_details, true) ?: [];
                    }
                    $formatted_persons[] = [
                        'id' => $person->id,
                        'employee_id' => $personData['employee_id'] ?? '',
                        'employee_name' => $personData['employee_name'] ?? '',
                        'employee_contact' => $personData['employee_contact'] ?? '',
                        'employee_email' => $personData['employee_email'] ?? '',
                        'vehicle_number' => $person->vehicle_number,
                        'visit_purpose' => $person->visit_purpose,
                        'location' => $person->location,
                        'entry_date' => $person->entry_date,
                        'entry_time' => $person->entry_time,
                        'entry_timestamp' => $person->entry_timestamp,
                        'exit_date' => $person->exit_date,
                        'exit_time' => $person->exit_time,
                        'exit_timestamp' => $person->exit_timestamp,
                        'method_type' => $person->method_type ?? 'entry_first',
                        'current_status' => $person->current_status
                    ];
                }
            }
 
            // Get today's entries count
            $entries_query = "
                SELECT COUNT(*) AS count
                FROM transport_personal_gateway
                WHERE company_id = {$company_id}
                AND DATE(created_date) = '{$today}'
                AND is_active = 0
            ";
            $entries_count = $callConfig->getRow($entries_query);
 
            // Get today's exits count (completed records)
            $exits_query = "
                SELECT COUNT(*) AS count
                FROM transport_personal_gateway
                WHERE company_id = {$company_id}
                AND DATE(updated_date) = '{$today}'
                AND current_status = 'completed'
                AND is_active = 0
            ";
            $exits_count = $callConfig->getRow($exits_query);
 
            // Get average duration
            $avg_query = "
                SELECT AVG(TIMESTAMPDIFF(MINUTE, entry_timestamp, exit_timestamp)) AS avg_duration
                FROM transport_personal_gateway
                WHERE company_id = {$company_id}
                AND exit_timestamp IS NOT NULL
                AND exit_timestamp != ''
                AND exit_timestamp != '0000-00-00 00:00:00'
                AND entry_timestamp IS NOT NULL
                AND entry_timestamp != '0000-00-00 00:00:00'
                AND is_active = 0
            ";
            $avg_duration = $callConfig->getRow($avg_query);
 
            $result = [
                'current_persons' => $formatted_persons,
                'statistics' => [
                    'current_count' => count($formatted_persons),
                    'today_entries' => $entries_count->count ?? 0,
                    'today_exits' => $exits_count->count ?? 0,
                    'avg_duration_minutes' => round($avg_duration->avg_duration ?? 0)
                ]
            ];
            return $result;
        } catch (Exception $e) {
            return [
                'current_persons' => [],
                'statistics' => ['current_count' => 0, 'today_entries' => 0, 'today_exits' => 0, 'avg_duration_minutes' => 0],
                'error' => $e->getMessage()
            ];
        }
    }
 	
	function getrecentpersonalactivity($get){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $limit       = $get['limit'] ?? 1;
	    $admin_type  = $get['work_admin_type'];
	    $employee_id = $get['employee_id'];
	 
	    $employeeCondition = "";
	 
	    if ($admin_type != 1 && $employee_id > 0) {
	        $employeeCondition = "
	            AND JSON_UNQUOTE(
	                JSON_EXTRACT(person_details, '$.employee_id')
	            ) = '{$employee_id}'
	        ";
	    }
	 
	    $query = "
	        SELECT *
	        FROM transport_personal_gateway
	        WHERE company_id = {$company_id}
	        AND is_active = 0
	        {$employeeCondition}
	        ORDER BY created_date DESC
	        LIMIT {$limit}
	    ";

	    $activities = $callConfig->getAllRows($query);
	 
	    $formatted_activities = [];

	    if ($activities && is_array($activities)) {
	        foreach ($activities as $activity) {

	            $personData = [];
	            if (!empty($activity->person_details)) {
	                $personData = json_decode($activity->person_details, true) ?: [];
	            }

	            $method_type = $activity->method_type ?? 'entry_first';
	            $type = ($method_type === 'exit_first') ? 'exit' : 'entry';

	            $formatted_activities[] = [
	                'id' => $activity->id,
	                'employee_id' => $personData['employee_id'] ?? '',
	                'employee_name' => $personData['employee_name'] ?? '',
	                'employee_contact' => $personData['employee_contact'] ?? '',
	                'employee_email' => $personData['employee_email'] ?? '',
	                'vehicle_number' => $activity->vehicle_number,
	                'visit_purpose' => $activity->visit_purpose,
	                'location' => $activity->location ?? '',
	                'type' => $type,
	                'method_type' => $method_type,
	                'current_status' => $activity->current_status,
	                'entry_timestamp' => $activity->entry_timestamp,
	                'entry_date' => $activity->entry_date,
	                'entry_time' => $activity->entry_time,
	                'exit_timestamp' => $activity->exit_timestamp,
	                'exit_date' => $activity->exit_date,
	                'exit_time' => $activity->exit_time
	            ];
	        }
	    }

	    return [
	        'status' => 'success',
	        'data' => $formatted_activities
	    ];
	}


	function insertpersonalgateway($get, $post)
	{
		global $callConfig;
 
		$rid = isset($get['rid']) && !empty($get['rid']) ? $get['rid'] : '';
 
		$personDetails = json_encode([
			'employee_id' => $post['employee_id'] ?? '',
			'employee_name' => $post['employee_name'] ?? '',
			'employee_contact' => $post['employee_contact'] ?? '',
			'employee_email' => $post['employee_email'] ?? ''
		]);
 
		$method_type = isset($post['method_type']) && $post['method_type'] !== '' ? $post['method_type'] : 'entry_first';
		$selected_location = $post['location'] ?? '';
		$other_location = ($selected_location === '99999') ? trim($post['other_location'] ?? '') : '';
		 
		 
		$time_value = $post['in_time'] ?? date('H:i:s');
		$date_value = $post['entry_date'] ?? date('Y-m-d');
		$current_status = 0;
		$process_val = 0;

		if($post['requested_by'] == 2){
			$process_val = 1;
			$current_status = 199;
		}
		 
		$fieldnames = array(
			'company_id' => $post['company_id'],
			'user_id' => $post['user_id'],
			'vehicle_number' => $post['vehicle_number'] ?? '',
			'person_details' => $personDetails,
			'visit_purpose' => $post['visit_purpose'],
			'location' => $selected_location,
			'other_location' => $other_location,
			'notes' => $post['notes'] ?? '',
			'method_type' => $method_type,
			'is_active' => '0',
			"gateway_type" => $post['gateway_type'],
			'process_val' => $process_val,
			'requested_by' => isset($post['requested_by']) ? (int)$post['requested_by'] : NULL,
			'authorized_by' => $post['authorized_by'] ?? $get['user_id']
		);
		
		if ($method_type === 'exit_first') { 
			$fieldnames['exit_date'] = $date_value;
			$fieldnames['exit_time'] = $time_value;
			$fieldnames['exit_timestamp'] = date('Y-m-d H:i:s');
			$fieldnames['current_status'] = $current_status;
		} else { 
			$fieldnames['entry_date'] = $date_value;
			$fieldnames['entry_time'] = $time_value;
			$fieldnames['entry_timestamp'] = date('Y-m-d H:i:s');
			$fieldnames['current_status'] = $current_status;
		}
		 
		if (!empty($rid)) {
			$fieldnames['updated_by'] = $get['user_id'] ?? $post['user_id'];
			$fieldnames['updated_date'] = date('Y-m-d H:i:s'); 
			$res = $callConfig->updateRecord('transport_personal_gateway', $fieldnames, 'id', $rid);
		} else {
			$fieldnames['created_by'] = $get['user_id'];
			$fieldnames['created_date'] = date('Y-m-d H:i:s'); 
			$res = $callConfig->insertRecord('transport_personal_gateway', $fieldnames);
		}
		
		return $res;
	}

	function updatepersonalexit($get, $post)
	{
		global $callConfig;

		$res['get'] = $get;
		$res['post'] = $post;
		 
		$entry_id = $post['entry_id']; 
		 
		$method_type = $post['method_type'];
		$time_value = $post['exit_time'] ?? date('H:i:s');
		$date_value = $post['exit_date'] ?? date('Y-m-d');
		
		if ($method_type === 'exit_first') { 
			$fieldnames = array(
				'entry_date' => $date_value,
				'entry_time' => $time_value,
				'entry_timestamp' => date('Y-m-d H:i:s'),
				'exit_notes' => $post['exit_notes'] ?? '',
				'authorized_by' => $post['authorized_by'] ?? '',
				'current_status' => 0,
				'updated_by' => $get['user_id'],
				'updated_date' => date('Y-m-d H:i:s'),
				"gateway_type" => $post['gateway_type'],
				'requested_by' => isset($post['requested_by']) ? (int)$post['requested_by'] : 2,
				'authorized_by' => $post['authorized_by'] ?? $get['user_id']
			);
		} else { 
			$fieldnames = array(
				'exit_date' => $date_value,
				'exit_time' => $time_value,
				'exit_timestamp' => date('Y-m-d H:i:s'),
				'exit_notes' => $post['exit_notes'] ?? '',
				'authorized_by' => $post['authorized_by'] ?? '',
				'current_status' => 0,
				'updated_by' => $get['user_id'],
				'updated_date' => date('Y-m-d H:i:s'),
				"gateway_type" => $post['gateway_type'],
				'requested_by' => isset($post['requested_by']) ? (int)$post['requested_by'] : 2,
				'authorized_by' => $post['authorized_by'] ?? $get['user_id']
			);
		}

		$res['fieldnames'] = $fieldnames;
		$res['entry_id'] = $entry_id;
		
		$res = $callConfig->updateRecord("transport_personal_gateway", $fieldnames, 'id', $post['entry_id']);
		return $res;
	}

	function getvehiclegateway($get)
	{
		global $callConfig;
		$return = [];

		try {
			$company_id = $get['company_id'] ?? $_SESSION['company_id'];
			$today = date('Y-m-d');
 			
			$current_query = "
				SELECT * 
				FROM transport_gateway
				WHERE company_id = {$company_id} 
				AND is_active = 0
				AND (current_status IN ('inside', 'outside', '0', 0) OR current_status IS NULL)
				AND (
					(method_type = 'entry_first' AND (exit_time IS NULL OR exit_time = '' OR exit_time = '00:00:00'))
					OR 
					(method_type = 'exit_first' AND (entry_time IS NULL OR entry_time = '' OR entry_time = '00:00:00'))
					OR
					(method_type IS NULL AND (exit_time IS NULL OR exit_time = '' OR exit_time = '00:00:00'))
				)
				ORDER BY created_date DESC
			";
			$return['current_query'] = $current_query;
			$current_vehicles = $callConfig->getAllRows($current_query);
			$return['current_vehicles'] = $current_vehicles;

			$entries_query = "
				SELECT COUNT(*) AS count
				FROM transport_gateway
				WHERE company_id = {$company_id}
				AND DATE(created_date) = '{$today}'
				AND is_active = 0
			";
			$return['entries_query'] = $entries_query;
			$entries_count = $callConfig->getRow($entries_query);
			$return['today_entries'] = $entries_count->count ?? 0;

			$exits_query = "
				SELECT COUNT(*) AS count
				FROM transport_gateway
				WHERE company_id = {$company_id}
				AND DATE(updated_date) = '{$today}' 
				AND current_status = 'completed'
				AND is_active = 0
			";
			$return['exits_query'] = $exits_query;
			$exits_count = $callConfig->getRow($exits_query);
			$return['today_exits'] = $exits_count->count ?? 0;

			$avg_query = "
				SELECT AVG(TIMESTAMPDIFF(MINUTE, entry_timestamp, exit_timestamp)) AS avg_duration
				FROM transport_gateway
				WHERE company_id = {$company_id} 
				AND exit_timestamp IS NOT NULL
				AND exit_timestamp != '0000-00-00 00:00:00'
				AND entry_timestamp IS NOT NULL
				AND entry_timestamp != '0000-00-00 00:00:00'
				AND is_active = 0
			";
			$return['avg_query'] = $avg_query;
			$avg_duration = $callConfig->getRow($avg_query);
			$return['avg_duration_minutes'] = round($avg_duration->avg_duration ?? 0);
			$result = [
				'current_vehicles' => $current_vehicles,
				'statistics' => ['current_count' => count($current_vehicles), 'today_entries' => $entries_count->count ?? 0, 'today_exits' => $exits_count->count ?? 0, 'avg_duration_minutes' => round($avg_duration->avg_duration ?? 0)],
				'queries' => $return
			];
			error_log("getvehiclegateway: returning result with " . count($current_vehicles) . " vehicles");
			return $result;
		} catch (Exception $e) {
			error_log("getvehiclegateway: Exception - " . $e->getMessage());
			return [
				'current_vehicles' => [],
				'statistics' => ['current_count' => 0, 'today_entries' => 0, 'today_exits' => 0, 'avg_duration_minutes' => 0],
				'error' => $e->getMessage()
			];
		}
	}


	function getrecentvehicleactivity($get)
	{
		global $callConfig;
		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		$limit = $get['limit'];
		$where = "company_id = " . $company_id . " AND is_active = 0";
		$query = $callConfig->selectQuery("transport_gateway", '*', $where, 'created_date DESC', '', $limit);
		$activities = $callConfig->getAllRows($query);

		$formatted_activities = array();
		foreach ($activities as $activity) {
			$method_type = $activity->method_type ?? 'entry_first';
			$entry_time = $activity->entry_time;
			$exit_time = $activity->exit_time;
			 
			if ($entry_time === '00:00:00') $entry_time = null;
			if ($exit_time === '00:00:00') $exit_time = null;
			 
			if ($method_type === 'exit_first') { 
				$type = (!empty($entry_time) && !empty($exit_time)) ? 'entry' : 'exit';
				$latest_timestamp = !empty($activity->entry_timestamp) ? $activity->entry_timestamp : $activity->exit_timestamp;
			} else { 
				$type = !empty($exit_time) ? 'exit' : 'entry';
				$latest_timestamp = !empty($activity->exit_timestamp) ? $activity->exit_timestamp : $activity->entry_timestamp;
			}

			$formatted_activities[] = array(
				'id' => $activity->id, 
				'vehicle_id' => $activity->id, 
				'vehicle_number' => $activity->vehicle_number, 
				'vehicle_type' => $activity->vehicle_type, 
				'driver_name' => $activity->driver_name, 
				'visit_purpose' => $activity->visit_purpose, 
				'location' => $activity->location ?? '', 
				'other_location' => $activity->other_location ?? '',
				'type' => $type, 
				'method_type' => $method_type,
				'timestamp' => $latest_timestamp, 
				'entry_timestamp' => $activity->entry_timestamp, 
				'entry_date' => $activity->entry_date, 
				'entry_time' => $activity->entry_time, 
				'exit_timestamp' => $activity->exit_timestamp, 
				'exit_date' => $activity->exit_date, 
				'exit_time' => $activity->exit_time, 
				'formatted_date' => date('M j, Y', strtotime($latest_timestamp)), 
				'formatted_time' => date('g:i A', strtotime($latest_timestamp))
			);
		}

		return array('status' => 'success', 'data' => $formatted_activities);
	}


	function lookupVehicleDetails($get)
	{
		global $callConfig;
		try {
			$company_id = $get['company_id'];
			$where = "registration_number = '" . $get['vehicle_number'] . "' AND company_id = '" . $company_id . "' AND is_active = 0";
			$query = $callConfig->selectQuery('transports', '*', $where, 'id DESC', '0', '1');
			$vehicleData = $callConfig->getRow($query);
			if ($vehicleData && $vehicleData->id) {
				return array(
					'status' => 'success',
					'message' => 'Vehicle found in company records',
					'data' => array('id' => $vehicleData->id, 'vehicle_name' => $vehicleData->vehicle_name, 'registration_number' => $vehicleData->registration_number, 'vehicle_type' => $vehicleData->vehicle_type, 'vehicle_capacity' => $vehicleData->vehicle_capacity, 'seat_layout' => $vehicleData->seat_layout, 'owner_name' => $vehicleData->owner_name, 'owner_contract' => $vehicleData->owner_contract, 'driver_id' => $vehicleData->driver_id, 'is_company_vehicle' => true)
				);
			} else {
				return array(
					'status' => 'not_found',
					'message' => 'Vehicle not found in company records',
					'data' => $query
				);
			}
		} catch (Exception $e) {
			return array(
				'status' => 'error',
				'message' => 'Error looking up vehicle details: ' . $e->getMessage(),
				'data' => $query
			);
		}
	}


	function insertvehiclegateway($get, $post)
	{
		global $callConfig;
 
		$method_type = isset($post['method_type']) && $post['method_type'] !== '' ? $post['method_type'] : 'entry_first';
		
		$time_value = $post['entry_time'] ?? date('H:i:s');
		$date_value = $post['entry_date'] ?? date('Y-m-d');
		$vehicle_reading = $post['vehicle_reading'] ?? NULL;
 
		$fieldnames = array(
			'company_id' => $post['company_id'], 
			'user_id' => $post['user_id'], 
			'vehicle_number' => strtoupper($post['vehicle_number']), 
			'vehicle_type' => $post['vehicle_type'], 
			'other_vehicle_type' => $post['other_vehicle_type'] ?? '', 
			'person_details' => $post['person_details'] ?? '[]', 
			'driver_name' => $post['driver_name'], 
			'driver_contact' => $post['driver_contact'] ?? '', 
			'visit_purpose' => $post['visit_purpose'], 
			'other_visit_purpose' => $post['other_visit_purpose'] ?? '', 
			'expected_duration' => $post['expected_duration'] ?? '', 
			'visiting_person' => $post['visiting_person'] ?? '', 
			'company_name' => $post['company_name'] ?? '', 
			'location' => $post['location'] ?? '',
			'other_location' => $post['other_location'] ?? '',
			'notes' => $post['notes'] ?? '', 
			'has_goods' => $post['has_goods'] ?? '0', 
			'goods_description' => $post['goods_description'] ?? '', 
			'method_type' => $method_type,
			'is_active' => '0', 
			'created_by' => $get['user_id'], 
			'created_date' => date('Y-m-d H:i:s'),
			"process_val" => 0
		);
		
		if ($method_type === 'exit_first') { 
			$fieldnames['exit_date'] = $date_value;
			$fieldnames['exit_time'] = $time_value;
			$fieldnames['exit_timestamp'] = date('Y-m-d H:i:s');
			$fieldnames['current_status'] = ST_APPROVED;
			$fieldnames['vehicle_exit_reading'] = $vehicle_reading;

		} else { 
			$fieldnames['entry_date'] = $date_value;
			$fieldnames['entry_time'] = $time_value;
			$fieldnames['entry_timestamp'] = date('Y-m-d H:i:s');
			$fieldnames['current_status'] = ST_APPROVED;
			$fieldnames['vehicle_entry_reading'] = $vehicle_reading;

		}
		
		$res = $callConfig->insertRecord('transport_gateway', $fieldnames);
		// if ($res['status'] == 'sucess' && isset($post['vehicle_ownership']) && $post['vehicle_ownership'] == 'company') {
		// 	if (!isset($post['vehicle_found_in_lookup']) || $post['vehicle_found_in_lookup'] !== true) {
		// 		$this->addVehicleToTransportsTable($get, $post);
		// 	}
		// }
		return $res;
	}

	function updatevehicleexit($get, $post)
	{
		global $callConfig;
		 
		$vehicle_id = $post['vehicle_id'] ?? null;
		if (empty($vehicle_id)) {
			return array('status' => 'error', 'message' => 'Vehicle ID is missing in function');
		}
		 
		$method_type = isset($post['method_type']) && $post['method_type'] !== '' ? $post['method_type'] : 'entry_first';
		$time_value = $post['exit_time'] ?? date('H:i:s');
		$date_value = $post['exit_date'] ?? date('Y-m-d');
		
		if ($method_type === 'exit_first') { 
			$fieldnames = array(
				'entry_date' => $date_value,
				'entry_time' => $time_value,
				'entry_timestamp' => date('Y-m-d H:i:s'),
				'exit_notes' => $post['exit_notes'] ?? '',
				'goods_checked' => $post['goods_checked'] ?? '0',
				'security_check' => $post['security_check'] ?? '0',
				'authorized_by' => $post['authorized_by'] ?? '',
				'current_status' => 0,
				'updated_by' => $post['user_id'] ?? $_SESSION['userid'],
				'updated_date' => date('Y-m-d H:i:s')
			);
		} else { 
			$fieldnames = array(
				'exit_date' => $date_value,
				'exit_time' => $time_value,
				'exit_timestamp' => date('Y-m-d H:i:s'),
				'exit_notes' => $post['exit_notes'] ?? '',
				'goods_checked' => $post['goods_checked'] ?? '0',
				'security_check' => $post['security_check'] ?? '0',
				'authorized_by' => $post['authorized_by'] ?? '',
				'current_status' => 0,
				'updated_by' => $post['user_id'] ?? $_SESSION['userid'],
				'updated_date' => date('Y-m-d H:i:s')
			);
		}
		
		$res = $callConfig->updateRecord("transport_gateway", $fieldnames, 'id', $vehicle_id);
		  
		return $res;
	}


	function updatetransportvehiclemaster($get, $post)
    {
        global $callConfig;

        $fieldnames = array(
            'vehicle_reg_no' => strtoupper(trim($post['vehicle_reg_no'])),
            'chassis_no' => strtoupper(trim($post['chassis_no'] ?? '')),
            'engine_no' => strtoupper(trim($post['engine_no'] ?? '')),
            'registered_branch' => $post['registered_branch'] ?? '',
            'colour' => $post['colour'] ?? '',
            'model' => $post['model'] ?? '',
            'model_group' => $post['model_group'] ?? '',
            'km_driven' => intval($post['km_driven'] ?? 0),
            'fuel_type' => $post['fuel_type'] ?? '',
            'average' => $post['average'] ?? '',
            'vehicle_type' => $post['vehicle_type'] ?? '',
            'transportation_type' => intval($post['transportation_type'] ?? 0),
            'responsible_person' => $post['responsible_person'] ?? '',
            'insurance_no' => $post['insurance_no'] ?? '',
            'insurance_exp_date' => !empty($post['insurance_exp_date']) ? $post['insurance_exp_date'] : null,
            'puc_issue_date' => !empty($post['puc_issue_date']) ? $post['puc_issue_date'] : null,
            'registration_name' => $post['registration_name'] ?? '',
            'hypothication_status' => $post['hypothication_status'] ?? '',
            'audit_date' => !empty($post['audit_date']) ? $post['audit_date'] : null,
            'audit_remark' => $post['audit_remark'] ?? '',
            'department_type' => $post['department_type'] ?? '',
            'description' => $post['description'] ?? '',
            'additional_notes' => $post['additional_notes'] ?? '',
            'is_active' => intval($post['is_active'] ?? 0),
            'current_status' => intval($post['current_status'] ?? 0),
            'updated_by' => $post['updated_by'] ?? $get['user_id'],
            'updated_date' => date('Y-m-d H:i:s')
        );

        $res = $callConfig->updateRecord('transport_vehicle_master', $fieldnames, 'id', $post['vehicle_id']);
        return $res;
    }

	function inserttransportvehiclemaster($get, $post)
    {
        global $callConfig;

        $fieldnames = array(
            'company_id' => $post['company_id'],
            'vehicle_reg_no' => strtoupper(trim($post['vehicle_reg_no'])),
            'chassis_no' => strtoupper(trim($post['chassis_no'] ?? '')),
            'engine_no' => strtoupper(trim($post['engine_no'] ?? '')),
            'registered_branch' => $post['registered_branch'] ?? '',
            'colour' => $post['colour'] ?? '',
            'model' => $post['model'] ?? '',
            'model_group' => $post['model_group'] ?? '',
            'km_driven' => intval($post['km_driven'] ?? 0),
            'fuel_type' => $post['fuel_type'] ?? '',
            'average' => $post['average'] ?? '',
            'vehicle_type' => $post['vehicle_type'] ?? '',
            'transportation_type' => intval($post['transportation_type'] ?? 0),
            'responsible_person' => $post['responsible_person'] ?? '',
            'insurance_no' => $post['insurance_no'] ?? '',
            'insurance_exp_date' => !empty($post['insurance_exp_date']) ? $post['insurance_exp_date'] : null,
            'puc_issue_date' => !empty($post['puc_issue_date']) ? $post['puc_issue_date'] : null,
            'registration_name' => $post['registration_name'] ?? '',
            'hypothication_status' => $post['hypothication_status'] ?? '',
            'audit_date' => !empty($post['audit_date']) ? $post['audit_date'] : null,
            'audit_remark' => $post['audit_remark'] ?? '',
            'department_type' => $post['department_type'] ?? '',
            'description' => $post['description'] ?? '',
            'additional_notes' => $post['additional_notes'] ?? '',
            'is_active' => intval($post['is_active'] ?? 0),
            'current_status' => intval($post['current_status'] ?? 0),
            'status_date' => date('Y-m-d H:i:s'),
            'created_by' => $post['created_by'] ?? $get['user_id'],
            'created_date' => date('Y-m-d H:i:s')
        );

        $res = $callConfig->insertRecord('transport_vehicle_master', $fieldnames);
        return $res;
    }


	function getVehicleGatewayList($get)
	{
		global $callConfig;
		$return['get'] = $get;

		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		$where = "company_id = " . $company_id . " AND is_active = 0";
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$where .= " AND ((entry_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "') OR (exit_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'))";
		} elseif (!empty($get['from_date'])) {
			$where .= " AND (entry_date >= '" . $get['from_date'] . "' OR exit_date >= '" . $get['from_date'] . "')";
		} elseif (!empty($get['to_date'])) {
			$where .= " AND (entry_date <= '" . $get['to_date'] . "' OR exit_date <= '" . $get['to_date'] . "')";
		}

		$limit = !empty($get['limit']) ? $get['limit'] : '';
		$query = $callConfig->selectQuery("transport_gateway", '*', $where, 'created_date DESC', '', $limit);
		$rows = $callConfig->getAllRows($query);
		 
		$vehicles = array();
		foreach ($rows as $row) {
			$vehicles[] = array(
				'id' => $row->id,
				'vehicle_number' => $row->vehicle_number,
				'vehicle_type' => $row->vehicle_type,
				'driver_name' => $row->driver_name,
				'driver_contact' => $row->driver_contact,
				'visit_purpose' => $row->visit_purpose,
				'visiting_person' => $row->visiting_person ?? '',
				'location' => $row->location ?? '',
				'other_location' => $row->other_location ?? '',
				'method_type' => $row->method_type ?? 'entry_first',
				'entry_date' => $row->entry_date,
				'entry_time' => $row->entry_time,
				'entry_timestamp' => $row->entry_timestamp,
				'exit_date' => $row->exit_date,
				'exit_time' => $row->exit_time,
				'exit_timestamp' => $row->exit_timestamp,
				'current_status' => $row->current_status
			);
		}
		$return['vehicles'] = $vehicles;

		return $return;
	}

	function deletetransportvehiclemaster($get)
    {
        global $callConfig;
        $company_id = $get['company_id'];
        $rid = $get['rid'];
        $user_id = $get['user_id'];
 
        $query = "UPDATE transport_vehicle_master SET is_active = NULL, updated_by = " . intval($user_id) . ", updated_date = '" . date('Y-m-d H:i:s') . "' WHERE id = " . intval($rid) . " AND company_id = " . intval($company_id);
        $res = $callConfig->executeQuery($query);
        return $res;
    }

	function getfuelentrydata($get)
	{
		global $callConfig;
		$company_id = $get['company_id'];
		$rid = $get['rid'];
		$query = "SELECT * FROM transport_fuel WHERE company_id = {$company_id} AND id = {$rid} AND is_active = 0";
		$res['fueldata'] = $callConfig->getAllRows($query);
		return $res;
	}

	function getdepartmentdetails($get)
	{
		global $callConfig;
		$return = [];
		$company_id = $get['company_id'];
		$query = "SELECT * FROM departments WHERE company_id = $company_id AND is_active = '0'";
		$return['query'] = $query;
		$result = $callConfig->getAllRows($query);
		return $result;
	}

	function getdepartmentemployeesdetails($get)
	{
		global $callConfig;
		$return = [];
		$company_id = $get['company_id'];
		$department_id = $get['department_id'];
		$query = "SELECT * FROM employees WHERE company_id = $company_id AND department_id = $department_id AND is_active = '0'";
		$return['query'] = $query;
		$result = $callConfig->getAllRows($query);
		return $result;
	}


	function getfuelstatistics($get)
	{
		global $callConfig;

		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		$today = $get['today'] ?? date('Y-m-d');
		$month_start = $get['month_start'] ?? date('Y-m-01');

		$today_query = "SELECT COALESCE(SUM(total_amount), 0) as today_fuel_amount, COALESCE(SUM(fuel_quantity), 0) as today_fuel_quantity, COUNT(DISTINCT vehicle_number) as total_vehicles_fueled FROM transport_fuel WHERE company_id = {$company_id}  AND fuel_date = '{$today}' AND is_active = 0";
		$today_stats = $callConfig->getRow($today_query);
		$monthly_query = "SELECT COALESCE(SUM(total_amount), 0) as monthly_fuel_cost FROM transport_fuel WHERE company_id = {$company_id} AND fuel_date >= '{$month_start}'  AND fuel_date <= '{$today}' AND is_active = 0";
		$monthly_stats = $callConfig->getRow($monthly_query);

		return array(
			'status' => 'success',
			'monthly_query' => $monthly_query,
			'today_query' => $today_query,
			'today_fuel_amount' => $today_stats->today_fuel_amount ?? 0,
			'today_fuel_quantity' => $today_stats->today_fuel_quantity ?? 0,
			'monthly_fuel_cost' => $monthly_stats->monthly_fuel_cost ?? 0,
			'total_vehicles_fueled' => $today_stats->total_vehicles_fueled ?? 0
		);
	}



	function gettodayfuelsummary($get)
	{
		global $callConfig;

		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		$date = $get['date'] ?? date('Y-m-d');

		$query = "SELECT * FROM transport_fuel WHERE company_id = {$company_id} AND fuel_date = '{$date}' ";
		$fuel_entries = $callConfig->getAllRows($query);
		return array(
			'status' => 'success',
			'fuel_entries' => $fuel_entries ?? array(),
			'query' => $query,
			'company_id' => $company_id,
			'date' => $date,
			'count' => count($fuel_entries ?? array())
		);
	}

	function getfueldetails($get)
	{
		global $callConfig;
		$fuel_id = $get['fuel_id'];
		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		if (empty($fuel_id)) {
			return array('status' => 'error', 'message' => 'Fuel ID is required.');
		}
		$query = "SELECT * FROM transport_fuel WHERE id = {$fuel_id}  AND company_id = {$company_id} AND is_active = 0";
		$fuel_entry = $callConfig->getRow($query);
		if ($fuel_entry) {
			return array(
				'status' => 'success',
				'fuel_entry' => $fuel_entry
			);
		} else {
			return array('status' => 'error', 'message' => 'Fuel entry not found.');
		}
	}

	function insertfuelentry($get, $post)
	{
		global $callConfig;

		if (
			empty($post['vehicle_number']) || empty($post['vehicle_ownership']) || empty($post['driver_name']) ||
			empty($post['fuel_type']) || empty($post['fuel_quantity']) || empty($post['price_per_liter']) || empty($post['payment_method'])
		) {
			return array('status' => 'error', 'message' => 'Please fill in all required fields.');
		}

		if ($post['fuel_quantity'] <= 0 || $post['fuel_quantity'] > 1000) {
			return array('status' => 'error', 'message' => 'Invalid fuel quantity. Must be between 0.01 and 1000 liters.');
		}

		if ($post['price_per_liter'] <= 0 || $post['price_per_liter'] > 500) {
			return array('status' => 'error', 'message' => 'Invalid price per liter. Must be between ₹0.01 and ₹500.');
		}

		$total_amount = $post['fuel_quantity'] * $post['price_per_liter'];

		$fieldnames = array(
			'company_id' => $post['company_id'],
			'user_id' => $post['user_id'],
			'vehicle_number' => strtoupper($post['vehicle_number']),
			'vehicle_ownership' => $post['vehicle_ownership'],
			'driver_name' => $post['driver_name'],
			'contact_number' => $post['contact_number'] ?? '',
			'department' => $post['department'] ?? $post['department_id'],
			'employee' => $post['employee_id'] ?? null,
			'fuel_type' => $post['fuel_type'],
			'other_fuel_type' => $post['other_fuel_type'] ?? '',
			'fuel_quantity' => $post['fuel_quantity'],
			'price_per_liter' => $post['price_per_liter'],
			'total_amount' => $total_amount,
			'payment_method' => $post['payment_method'],
			'fuel_date' => $post['fuel_date'] ?? date('Y-m-d'),
			'notes' => $post['notes'] ?? '',
			'is_emergency' => $post['is_emergency'] ?? '0',
			'is_advance' => $post['is_advance'] ?? '0',
			'created_date' => date('Y-m-d H:i:s'),
			'updated_date' => date('Y-m-d H:i:s'),
			'created_by' => $post['user_id']
		);

		$res = $callConfig->insertRecord('transport_fuel', $fieldnames);
		return $res;
	}


	function updatefuelentry($get, $post)
	{
		global $callConfig;

		// Validate required fields
		if (
			empty($post['fuel_id']) || empty($post['vehicle_number']) || empty($post['vehicle_ownership']) ||
			empty($post['driver_name']) || empty($post['fuel_type']) || empty($post['fuel_quantity']) ||
			empty($post['price_per_liter']) || empty($post['payment_method'])
		) {
			return array('status' => 'error', 'message' => 'Please fill in all required fields.');
		}

		// Validate fuel quantity and price
		if ($post['fuel_quantity'] <= 0 || $post['fuel_quantity'] > 1000) {
			return array('status' => 'error', 'message' => 'Invalid fuel quantity. Must be between 0.01 and 1000 liters.');
		}

		if ($post['price_per_liter'] <= 0 || $post['price_per_liter'] > 500) {
			return array('status' => 'error', 'message' => 'Invalid price per liter. Must be between ₹0.01 and ₹500.');
		}

		// Calculate total amount
		$total_amount = $post['fuel_quantity'] * $post['price_per_liter'];

		$fieldnames = array(
			'vehicle_number' => strtoupper($post['vehicle_number']),
			'vehicle_ownership' => $post['vehicle_ownership'],
			'driver_name' => $post['driver_name'],
			'contact_number' => $post['contact_number'] ?? '',
			'department' => $post['department'] ?? '',
			'fuel_type' => $post['fuel_type'],
			'other_fuel_type' => $post['other_fuel_type'] ?? '',
			'fuel_quantity' => $post['fuel_quantity'],
			'price_per_liter' => $post['price_per_liter'],
			'total_amount' => $total_amount,
			'payment_method' => $post['payment_method'],
			'fuel_date' => $post['fuel_date'] ?? date('Y-m-d'),
			'notes' => $post['notes'] ?? '',
			'is_emergency' => $post['is_emergency'] ?? '0',
			'is_advance' => $post['is_advance'] ?? '0',
			'updated_date' => date('Y-m-d H:i:s'),
			'updated_by' => $post['user_id']
		);

		$res = $callConfig->updateRecord('transport_fuel', $fieldnames, 'id', $post['fuel_id']);
		return $res;
	}


	function getfuelentrylist($get)
	{
		global $callConfig;
		$company_id = $get['company_id'] ?? $_SESSION['company_id'];
		$where = "company_id = " . $company_id . " AND is_active = 0";

		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$where .= " AND fuel_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		} elseif (!empty($get['from_date'])) {
			$where .= " AND fuel_date >= '" . $get['from_date'] . "'";
		} elseif (!empty($get['to_date'])) {
			$where .= " AND fuel_date <= '" . $get['to_date'] . "'";
		}

		$limit = !empty($get['limit']) ? $get['limit'] : '';
		$query = $callConfig->selectQuery("transport_fuel", '*', $where, 'created_date DESC', '', $limit);
		$fuel_entries = $callConfig->getAllRows($query);

		return array(
			'status' => 'success',
			'fuel_entries' => $fuel_entries ?? array()
		);
	}


	function deletefuelentry($get, $post)
	{
		global $callConfig;

		if (empty($post['fuel_id'])) {
			return array('status' => 'error', 'message' => 'Fuel ID is required.');
		}
		$fieldnames = array(
			'is_active' => '1',
			'updated_date' => date('Y-m-d H:i:s'),
			'updated_by' => $post['user_id']
		);

		$res = $callConfig->updateRecord('transport_fuel', $fieldnames, 'id', $post['fuel_id']);
		return $res;
	}
	// omkar related code end
	// 
	function getEmployeesAttendanceData($get){
	    global $callConfig;
	    $query = "SELECT id, display_name, emp_code FROM employees
	        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY display_name ASC";
	    $data = $callConfig->getAllRows($query);

	    $employee_id = $get['employee_id'] ?? 0;
    	$month       = $get['month'] ?? '';
    	$attendance_data =  array();
    	$holidays =  array();
    	$leaves =  array();
    	$weak_off_arr = array();
	    if ($employee_id && $month) {
	    	// Build month date range (YYYY-MM)
		    $from_date = $month . '-01';
		    $to_date   = date('Y-m-t', strtotime($from_date));
		    $query = "SELECT id AS timesheet_id,employee_id, attendace_date,clock_in,clock_out, leave_type_id, attendance_key_short_code
		        FROM employee_daily_attendances
		        WHERE employee_id = {$employee_id}
		          AND company_id = {$get['company_id']}
		          AND attendace_date BETWEEN '{$from_date}' AND '{$to_date}'
		          AND is_active = 0
		        ORDER BY attendace_date ASC";
		    $attendance_data = $callConfig->getAllRows($query);

		    $year = date('Y', strtotime($from_date)); // 2025

		    $holiday_qry = "SELECT holiday_date,holiday_name FROM holidays
		        WHERE company_id = {$get['company_id']}
		          AND holiday_year = '{$year}'
		          AND holiday_date BETWEEN '{$from_date}' AND '{$to_date}'
		          AND is_active = 0";
		    $holidays = $callConfig->getAllRows($holiday_qry);

		    $leave_qry = "SELECT 
		            el.leave_from,
		            el.leave_to,
		            el.leave_days,
		            el.start_half_day,
		            el.end_half_day,
		            el.unpaid_days,
		            lt.leave_type_name
		        FROM employee_leaves el
		        LEFT JOIN leave_types lt 
		            ON lt.id = el.leave_type_id
		           AND lt.is_active = 0
		        WHERE el.employee_id = {$employee_id}
		          AND el.company_id = {$get['company_id']}
		          AND el.current_status = 199
		          AND el.is_active = 0
		          AND (
		                el.leave_from BETWEEN '{$from_date}' AND '{$to_date}'
		             OR el.leave_to   BETWEEN '{$from_date}' AND '{$to_date}'
		          )
		        ORDER BY el.leave_from ASC";

		    $leaves = $callConfig->getAllRows($leave_qry);

		    $query = "SELECT id, working_days, full_day_hours, half_day_hours, grace_perieod FROM shift_settings WHERE id IN (SELECT shift_setting_id FROM shifts WHERE id IN ( SELECT shift_id FROM `emp_shifts` WHERE company_id = ".$get['company_id']."  AND emp_id = ".$employee_id." AND is_active=0))";
    		$weak_off_arr = $callConfig->getRow($query);

		    $shift_info = array();

	    	if($weak_off_arr->id == ''){
		    	$qry = "SELECT id, working_days, full_day_hours, half_day_hours, grace_perieod FROM attendance_settings 
				        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY id ASC LIMIT 1";
				$weak_off_arr = $callConfig->getRow($qry);
				
				// No shift assigned - use general attendance settings
				$shift_info = array(
					'shift_id' => 0,
					'shift_name' => 'General Attendance (No Shift)',
					'shift_start_time' => '10:00:00',
					'shift_end_time' => '19:00:00',
					'full_day_hours' => $weak_off_arr->full_day_hours ?? '09:00:00',
					'half_day_hours' => $weak_off_arr->half_day_hours ?? '04:30:00',
					'grace_period' => $weak_off_arr->grace_perieod ?? '00:15:00',
					'working_days' => $weak_off_arr->working_days ?? '',
					'has_shift' => false
				);
			} else {
				// Employee has shift assigned
				$shift_info = array(
					'shift_id' => $shift_times->id ?? 0,
					'shift_name' => $shift_times->shift_name ?? 'N/A',
					'shift_start_time' => $shift_times->clock_in ?? '00:00:00',
					'shift_end_time' => $shift_times->clock_out ?? '00:00:00',
					'full_day_hours' => $weak_off_arr->full_day_hours ?? '00:00:00',
					'half_day_hours' => $weak_off_arr->half_day_hours ?? '00:00:00',
					'grace_period' => $weak_off_arr->grace_perieod ?? '00:00:00',
					'working_days' => $weak_off_arr->working_days ?? '',
					'has_shift' => true
				);
			}

		    $emp_qry = "SELECT display_name
			    FROM employees
			    WHERE id = {$employee_id}
			      AND company_id = {$get['company_id']}
			      AND is_active = '0'
			    LIMIT 1";
			 $emp_qry =  "SELECT id, emp_code, display_name, designation_id, (SELECT dg.designation_name FROM designations dg WHERE dg.id = designation_id) as designation_name, department_id, (SELECT dp.department_name FROM departments dp WHERE dp.id = department_id) as department_name, location_id, (SELECT lc.location_name FROM locations lc WHERE lc.id = location_id) as location_name FROM employees WHERE id = {$employee_id} AND company_id = {$get['company_id']} AND is_active = '0' LIMIT 1";
			$emp = $callConfig->getRow($emp_qry);

			$shiftKeysSql = "SELECT * FROM attendance_key_types
		        WHERE company_id = {$get['company_id']}
		        AND leave_category_id IS NULL
		        AND is_active = 0";
	 
		    $shiftKeysData = $callConfig->getAllRows($shiftKeysSql);
		    $shiftKeys = array();
	 
		    foreach ($shiftKeysData as $row) {
		        $shiftKeys[$row->key_short_code] = (array)$row;
		    }
	    }
	    $whr="company_id= '".$get['company_id']."' AND is_active=0";
		$adqry = $callConfig->selectQuery('leave_month_wise','id,salad_from,salad_to,allowed_leave_days',$whr,'','','');
		
	    $app_leave_day = $callConfig->getAllRows($adqry);
	    return array('table'=>$data, 'attendance_data'=>$attendance_data, 'holidays' => $holidays, 'leaves' => $leaves, 'weak_off_arr' => $weak_off_arr,'employee_name' => $emp, 'shift_info' => $shift_info, 'attendance_key_types' => $shiftKeys, 'app_leave_day'=>$app_leave_day);
	}
	function togetalldetailsforemployeevalattendance_old($get){
	    global $callConfig;
	    $return = array();
	    /* -------------------------------------------------
	       Employee Daily Attendance (if id exists)
	    ------------------------------------------------- */
	    $attendance = array();

	    if (!empty($get['id']) && intval($get['id']) > 0) {
	        $qry = "SELECT 
	                    id,  employee_id, attendace_date, clock_in,  clock_out,  leave_type_id
	                FROM employee_daily_attendances
	                WHERE id = {$get['id']}
	                  AND company_id = {$get['company_id']}
	                  AND is_active = 0
	                LIMIT 1";
	        $attendance = $callConfig->getRow($qry);
	    }
	    $return['attendance'] = $attendance;
	       /* -------------------------------------------------
	       Attendance Settings (company level)
	    ------------------------------------------------- */
	    $qry = "SELECT 
	                id, company_id,
	                minimum_hours_required, maximum_hours_cap,
	                full_day_hours, half_day_hours,
	                break_time, grace_perieod,
	                entry_grace_perieod, exit_perieod,
	                working_days, remarks,
	                web_check_inout_enabled, mobile_check_inout_enabled,
	                consider_absent, regularization_days,
	                regularization_days_in, regularization_grace_days,
	                regularization_reason, location_id, location_radius,
	                is_active, current_status, status_date,
	                from_date, to_date, created_date,
	                created_by, updated_by, updated_date
	            FROM attendance_settings
	            WHERE company_id = {$get['company_id']}
	              AND is_active = 0
	            ORDER BY id ASC
	            LIMIT 1";
	    $attendance_settings = $callConfig->getRow($qry);
	    $return['attendance_settings'] = $attendance_settings;

	    $emp_shift = array();
	    $shift = array();

	    if (!empty($get['emp_id'])) {
	        $qry = "SELECT id, shift_id
	                FROM emp_shifts
	                WHERE emp_id = {$get['emp_id']}
	                  AND company_id = {$get['company_id']}
	                  AND is_active = 0
	                ORDER BY id DESC
	                LIMIT 1";
	        $emp_shift = $callConfig->getRow($qry);

	        if ($emp_shift->shift_id!='') {
	            $qry = "SELECT id, company_id, shift_name,clock_in, clock_out
	                    FROM shifts
	                    WHERE id = {$emp_shift->shift_id}
	                      AND company_id = {$get['company_id']}
	                      AND is_active = 0
	                    LIMIT 1";
	            $shift = $callConfig->getRow($qry);

	        }
	    }
	    $return['emp_shift']      = $emp_shift;
    	$return['shift']          = $shift;
	    /* -------------------------------------------------
	       Leave Types
	    ------------------------------------------------- */
	    $qry = "SELECT 
	                id, leave_category_id,
	                leave_type_name, back_ground_color
	            FROM leave_types
	            WHERE company_id = {$get['company_id']}
	              AND is_active = 0";
	    $leave_types = $callConfig->getAllRows($qry);
	    $return['leave_types'] = $leave_types;
	    return $return;
	}
	function saveEmployeeAttendance_old($get, $post){
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];

	    $id              = $post['id'] ?? 0;
	    $employee_id     = $post['emp_id'];
		$type        = $post['presentcheck']; // present | absent | leave

	    // ------------------------------
	    // Normalize input based on type
	    // ------------------------------
	    $attendace_date = $post['timesheet_date'];
	    $clock_in  = null;
	    $clock_out = null;
	    $leave_type_id = null;

	    if ($type === 'present') {
	        $clock_in  = !empty($post['clock_in'])
			    ? date('H:i:s', strtotime($post['clock_in']))
			    : null;

			$clock_out = !empty($post['clock_out'])
			    ? date('H:i:s', strtotime($post['clock_out']))
			    : null;

	    }

	    if ($type === 'absent') {
	        $attendace_date = $post['absent_date'];
	    }

	    if ($type === 'leave') {
	        $attendace_date = $post['leave_date'];
	        $leave_type_id  = $post['leave_type_id'] ?? null;
	    }

	    // ==============================
	    // 1. Prepare main attendance data
	    // ==============================
	    $attendanceData = array(
	        "company_id"      => $company_id,
	        "employee_id"     => $employee_id,
	        "attendace_date"  => $attendace_date,
	        "clock_in"        => $clock_in,
	        "clock_out"       => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"       => 0,
	        "current_status"  => 0
	    );

	    // ==============================
	    // 2. UPDATE OR INSERT main table
	    // ==============================
	    if($id > 0){
	        // UPDATE employee_daily_attendances
	        $attendanceData["updated_by"]   = $user_id;
	        $attendanceData["updated_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->updateRecord(
	            "employee_daily_attendances",
	            $attendanceData,
	            "id",
	            $id
	        );

	    } else {
	        // INSERT employee_daily_attendances
	        $attendanceData["created_by"]   = $user_id;
	        $attendanceData["created_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->insertRecord(
	            "employee_daily_attendances",
	            $attendanceData
	        );
	        $id = $res['LASTID'];
	    }

	    // ==============================
	    // 3. Insert attendance details
	    // ==============================
	    $detailData = array(
	        "company_id"     => $company_id,
	        "employee_id"    => $employee_id,
	        "attendace_date" => $attendace_date,
	        "clock_in"       => $clock_in,
	        "clock_out"      => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"      => 0,
	        "current_status" => 0,
	        "created_by"     => $user_id,
	        "created_date"   => date("Y-m-d H:i:s")
	    );

	    $res1 = $callConfig->insertRecord(
	        "employee_daily_attendance_details",
	        $detailData
	    );

	    return array(
	        "status" => "success",
	        "attendance_id" => $id,
	        'res' => $res,
	        'res1' => $res1
	    );
	}
	function getAutoAttendanceTime_old($get){
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $emp_id     = $get['emp_id'];
	    $date       = $get['date'];

	    // get config
	    $config = $callConfig->getRow(
	        "SELECT shift_mode 
	         FROM config 
	         WHERE is_active=0 
	         LIMIT 1"
	    );
	    $shift_mode = $config->shift_mode;
	    // CASE 1: No shift
	    if ((int)$shift_mode == 1) {

	        $att = $callConfig->getRow(
	            "SELECT full_day_hours 
	             FROM attendance_settings 
	             WHERE company_id=$company_id 
	               AND is_active=0 
	             ORDER BY id ASC LIMIT 1"
	        );

	        // Base clock-in (datetime)
		    $clockInDateTime = $date . ' 10:00:00';

		    // Convert HH:MM:SS → seconds
		    list($h, $m, $s) = explode(':', $att->full_day_hours);
		    $seconds = ($h * 3600) + ($m * 60) + $s;

		    // Calculate clock-out
		    $clockOutTimestamp = strtotime($clockInDateTime) + $seconds;

	        return [
	            'status'    => 'success',
	            // FRONTEND format (datetime-local)
		        'clock_in'  => date('Y-m-d\TH:i', strtotime($clockInDateTime)),
		        'clock_out' => date('Y-m-d\TH:i', $clockOutTimestamp),
	            'full_day_hours' => $att->full_day_hours
	        ];
	    }

	    // CASE 2: Shift based
	    $empShift = $callConfig->getRow(
	        "SELECT shift_id 
	         FROM emp_shifts 
	         WHERE emp_id=$emp_id 
	           AND company_id=$company_id 
	           AND is_active=0 
	         ORDER BY id DESC LIMIT 1"
	    );
	    $shift_id = $empShift->shift_id ?? 0;

	    if (!empty($shift_id)) {

	        $shift = $callConfig->getRow(
	            "SELECT clock_in, shift_setting_id 
	             FROM shifts 
	             WHERE id={$shift_id} 
	               AND company_id=$company_id 
	               AND is_active=0"
	        );
	        $shift_setting_id = $shift->shift_setting_id ?? 0;

	        if($shift_setting_id>0){
		        $shiftSet = $callConfig->getRow(
		            "SELECT full_day_hours 
		             FROM shift_settings 
		             WHERE id={$shift_setting_id} 
		               AND company_id=$company_id 
		               AND is_active=0"
		        );

        // Base clock-in datetime
        $clockInDateTime = $date . ' ' . $shift->clock_in;

        // Convert HH:MM:SS → seconds
        list($h, $m, $s) = explode(':', $shiftSet->full_day_hours);
        $seconds = ($h * 3600) + ($m * 60) + $s;

        // Calculate clock-out
        $clockOutTimestamp = strtotime($clockInDateTime) + $seconds;


		    return [
		            'status'    => 'success',
				// frontend-ready format
	            'clock_in'  => date('Y-m-d\TH:i', strtotime($clockInDateTime)),
	            'clock_out' => date('Y-m-d\TH:i', $clockOutTimestamp)
		        ];
		    }
	    }

	    return ['status' => 'error'];
	}

	function getEmployeesAttendanceDataNew_1($get){
	    global $callConfig;
	    $query = "SELECT id, display_name,emp_code FROM employees
	        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY display_name ASC";
	    $data = $callConfig->getAllRows($query);

	    $employee_id = $get['employee_id'] ?? 0;
    	$month       = $get['month'] ?? '';
    	$attendance_data =  array();
    	$holidays =  array();
    	$leaves =  array();
    	$weak_off_arr = array();
	    if ($employee_id && $month) {
	    	// Build month date range (YYYY-MM)
		    $from_date = $month . '-01';
		    $to_date   = date('Y-m-t', strtotime($from_date));
		    $query = "SELECT id AS timesheet_id,employee_id, attendace_date,clock_in,clock_out, leave_type_id, attendance_key_short_code
		        FROM employee_daily_attendances
		        WHERE employee_id = {$employee_id}
		          AND company_id = {$get['company_id']}
		          AND attendace_date BETWEEN '{$from_date}' AND '{$to_date}'
		          AND is_active = 0
		        ORDER BY attendace_date ASC";
		    $attendance_data = $callConfig->getAllRows($query);

		    $year = date('Y', strtotime($from_date)); // 2025

		    $holiday_qry = "SELECT holiday_date,holiday_name FROM holidays
		        WHERE company_id = {$get['company_id']}
		          AND holiday_year = '{$year}'
		          AND holiday_date BETWEEN '{$from_date}' AND '{$to_date}'
		          AND is_active = 0";
		    $holidays = $callConfig->getAllRows($holiday_qry);

		    $leave_qry = "SELECT 
		            el.leave_from,
		            el.leave_to,
		            el.leave_days,
		            el.start_half_day,
		            el.end_half_day,
		            el.unpaid_days,
		            lt.leave_type_name
		        FROM employee_leaves el
		        LEFT JOIN leave_types lt 
		            ON lt.id = el.leave_type_id
		           AND lt.is_active = 0
		        WHERE el.employee_id = {$employee_id}
		          AND el.company_id = {$get['company_id']}
		          AND el.current_status = 199
		          AND el.is_active = 0
		          AND (
		                el.leave_from BETWEEN '{$from_date}' AND '{$to_date}'
		             OR el.leave_to   BETWEEN '{$from_date}' AND '{$to_date}'
		          )
		        ORDER BY el.leave_from ASC";

		    $leaves = $callConfig->getAllRows($leave_qry);

		    $qry = "SELECT id, working_days, full_day_hours FROM attendance_settings 
		        WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY id ASC LIMIT 1";
		    $weak_off_arr = $callConfig->getRow($qry);

		    // Calculate OT/UT
            $fullDaySeconds = 0;
            if(!empty($weak_off_arr->full_day_hours)){
                $parts = explode(':', $weak_off_arr->full_day_hours);
                $h = isset($parts[0]) ? intval($parts[0]) : 0;
                $m = isset($parts[1]) ? intval($parts[1]) : 0;
                $s = isset($parts[2]) ? intval($parts[2]) : 0;
                $fullDaySeconds = ($h * 3600) + ($m * 60) + $s;
            }

            foreach($attendance_data as $attRow){
                $otSeconds = 0;
                $utSeconds = 0;
                $otString = "00:00";
                $utString = "00:00";

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
                $calculated_hours[$attRow->attendace_date] = array(
                    'ot_seconds' => $otSeconds,
                    'ut_seconds' => $utSeconds,
                    'ot_string'  => $otString,
                    'ut_string'  => $utString
                );
            }

		    $emp_qry = "SELECT display_name
		    FROM employees
		    WHERE id = {$employee_id}
		      AND company_id = {$get['company_id']}
		      AND is_active = '0'
		    LIMIT 1";
			$emp = $callConfig->getRow($emp_qry);
	    }

	    $shiftKeysSql = "SELECT * FROM attendance_key_types
	        WHERE company_id = {$get['company_id']}
	        AND leave_category_id IS NULL
	        AND is_active = 0";
 
	    $shiftKeysData = $callConfig->getAllRows($shiftKeysSql);
	    $shiftKeys = array();
 
	    foreach ($shiftKeysData as $row) {
	        $shiftKeys[$row->key_short_code] = (array)$row;
	    }
	    return array('table'=>$data, 'attendance_data'=>$attendance_data, 'calculated_hours'=> $calculated_hours, 'holidays' => $holidays, 'leaves' => $leaves, 'weak_off_arr' => $weak_off_arr,'employee_name' => $emp, 'attendance_key_types' => $shiftKeys);
	}

	function togetalldetailsforemployeevalattendance($get){
	    global $callConfig;
	    $return = array();
	    /* -------------------------------------------------
	       Employee Daily Attendance (if id exists)
	    ------------------------------------------------- */
	    $attendance = array();

	    if (!empty($get['id']) && intval($get['id']) > 0) {
	        $qry = "SELECT 
	                    id,  employee_id, attendace_date, clock_in,  clock_out,  leave_type_id
	                FROM employee_daily_attendances
	                WHERE id = {$get['id']}
	                  AND company_id = {$get['company_id']}
	                  AND is_active = 0
	                LIMIT 1";
	        $attendance = $callConfig->getRow($qry);
	    }
	    $return['attendance'] = $attendance;
	       /* -------------------------------------------------
	       Attendance Settings (company level)
	    ------------------------------------------------- */
	    $qry = "SELECT 
	                id, company_id,
	                minimum_hours_required, maximum_hours_cap,
	                full_day_hours, half_day_hours,
	                break_time, grace_perieod,
	                entry_grace_perieod, exit_perieod,
	                working_days, remarks,
	                web_check_inout_enabled, mobile_check_inout_enabled,
	                consider_absent, regularization_days,
	                regularization_days_in, regularization_grace_days,
	                regularization_reason, location_id, location_radius,
	                is_active, current_status, status_date,
	                from_date, to_date, created_date,
	                created_by, updated_by, updated_date
	            FROM attendance_settings
	            WHERE company_id = {$get['company_id']}
	              AND is_active = 0
	            ORDER BY id ASC
	            LIMIT 1";
	    $attendance_settings = $callConfig->getRow($qry);
	    $return['attendance_settings'] = $attendance_settings;

	    $emp_shift = array();
	    $shift = array();

	    if (!empty($get['emp_id'])) {
	        $qry = "SELECT id, shift_id
	                FROM emp_shifts
	                WHERE emp_id = {$get['emp_id']}
	                  AND company_id = {$get['company_id']}
	                  AND is_active = 0
	                ORDER BY id DESC
	                LIMIT 1";
	        $emp_shift = $callConfig->getRow($qry);

	        if ($emp_shift->shift_id!='') {
	            $qry = "SELECT id, company_id, shift_name,clock_in, clock_out
	                    FROM shifts
	                    WHERE id = {$emp_shift->shift_id}
	                      AND company_id = {$get['company_id']}
	                      AND is_active = 0
	                    LIMIT 1";
	            $shift = $callConfig->getRow($qry);

	        }
	    }
	    $return['emp_shift']      = $emp_shift;
    	$return['shift']          = $shift;
	    /* -------------------------------------------------
	       Leave Types
	    ------------------------------------------------- */
	    $qry = "SELECT 
	                id, leave_category_id,
	                leave_type_name, back_ground_color
	            FROM leave_types
	            WHERE company_id = {$get['company_id']}
	              AND is_active = 0";
	    $leave_types = $callConfig->getAllRows($qry);
	    $return['leave_types'] = $leave_types;
	    return $return;
	}
	function saveEmployeeAttendanceold08042026($get, $post){
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];

	    $id              = $post['id'] ?? 0;
	    $employee_id     = $post['emp_id'];
		$type        = $post['presentcheck']; // present | absent | leave

	    // ------------------------------
	    // Normalize input based on type
	    // ------------------------------
	    $attendace_date = $post['timesheet_date'];
	    $clock_in  = null;
	    $clock_out = null;
	    $leave_type_id = null;

	    if ($type === 'present') {
	        $clock_in  = !empty($post['clock_in'])
			    ? date('H:i:s', strtotime($post['clock_in']))
			    : null;

			$clock_out = !empty($post['clock_out'])
			    ? date('H:i:s', strtotime($post['clock_out']))
			    : null;

	    }

	    if ($type === 'absent') {
	        $attendace_date = $post['absent_date'];
	    }

	    if ($type === 'leave') {
	        $attendace_date = $post['leave_date'];
	        $leave_type_id  = $post['leave_type_id'] ?? null;
	    }

	    // ==============================
	    // 1. Prepare main attendance data
	    // ==============================
	    $attendanceData = array(
	        "company_id"      => $company_id,
	        "employee_id"     => $employee_id,
	        "attendace_date"  => $attendace_date,
	        "clock_in"        => $clock_in,
	        "clock_out"       => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"       => 0,
	        "current_status"  => 0
	    );
	    if($leave_type_id == 9){
	    	$attendanceData['attendance_key_short_code']='SL';
	    	$attendanceData['clock_in']=$post['shift_clock_in'];
	    	$attendanceData['clock_out']=$post['shift_clock_out'];
	    	$attendanceData['old_clock_times']=$post['main_clock_in']." - ".$post['main_clock_out'];
	    }
	    // ==============================
	    // 2. UPDATE OR INSERT main table
	    // ==============================
	    if($id > 0){
	        // UPDATE employee_daily_attendances
	        $attendanceData["updated_by"]   = $user_id;
	        $attendanceData["updated_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->updateRecord(
	            "employee_daily_attendances",
	            $attendanceData,
	            "id",
	            $id
	        );

	    } else {
	        // INSERT employee_daily_attendances
	        $attendanceData["created_by"]   = $user_id;
	        $attendanceData["created_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->insertRecord(
	            "employee_daily_attendances",
	            $attendanceData
	        );
	        $id = $res['LASTID'];
	    }

	    // ==============================
	    // 3. Insert attendance details
	    // ==============================
	    $detailData = array(
	        "company_id"     => $company_id,
	        "employee_id"    => $employee_id,
	        "attendace_date" => $attendace_date,
	        "clock_in"       => $clock_in,
	        "clock_out"      => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"      => 0,
	        "current_status" => 0,
	        "created_by"     => $user_id,
	        "created_date"   => date("Y-m-d H:i:s")
	    );

	    $res1 = $callConfig->insertRecord(
	        "employee_daily_attendance_details",
	        $detailData
	    );

	    return array(
	        "status" => "success",
	        "attendance_id" => $id,
	        'res' => $res,
	        'res1' => $res1
	    );
	}
	function saveEmployeeAttendance($get, $post){
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];

	    $id              = $post['id'] ?? 0;
	    $employee_id     = $post['emp_id'];
		$type        = $post['presentcheck']; // present | absent | leave

	    // ------------------------------
	    // Normalize input based on type
	    // ------------------------------
	    $attendace_date = $post['timesheet_date'];
	    $clock_in  = null;
	    $clock_out = null;
	    $leave_type_id = null;

	    if ($type === 'present') {
	        $clock_in  = !empty($post['clock_in'])
			    ? date('H:i:s', strtotime($post['clock_in']))
			    : null;

			$clock_out = !empty($post['clock_out'])
			    ? date('H:i:s', strtotime($post['clock_out']))
			    : null;
			    $post['original_status'] = 'P';

	    }

	    if ($type === 'absent') {
	        $attendace_date = $post['absent_date'];
	        $post['original_status'] = 'A';
	    }

	    if ($type === 'leave') {
	        $attendace_date = $post['leave_date'];
	        $leave_type_id  = $post['leave_type_id'] ?? null;

	    }
	    if ($type === 'halfday') {
		    $post['original_status'] = 'Z';

		    $clock_in = !empty($post['shift_clock_in'])
		        ? date('H:i:s', strtotime($post['shift_clock_in']))
		        : null;

		    $clock_out = null;

		    if (!empty($attendace_date) && !empty($post['shift_clock_in']) && !empty($post['shift_clock_out'])) {
		        $shiftInTimestamp  = strtotime($attendace_date . ' ' . $post['shift_clock_in']);
		        $shiftOutTimestamp = strtotime($attendace_date . ' ' . $post['shift_clock_out']);

		        if ($shiftInTimestamp !== false && $shiftOutTimestamp !== false && $shiftOutTimestamp > $shiftInTimestamp) {
		            $halfTimestamp = $shiftInTimestamp + (($shiftOutTimestamp - $shiftInTimestamp) / 2);
		            $clock_out = date('H:i:s', $halfTimestamp);
		        }
		    }
		}


	    // ==============================
	    // 1. Prepare main attendance data
	    // ==============================
	    $attendanceData = array(
	        "company_id"      => $company_id,
	        "employee_id"     => $employee_id,
	        "attendace_date"  => $attendace_date,
	        "clock_in"        => $clock_in,
	        "clock_out"       => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"       => 0,
	        "current_status"  => 0
	    );
	    $attendanceData['attendance_key_short_code']=$post['original_status'] ?? '';
	    if($leave_type_id == 9){
	    	$attendanceData['attendance_key_short_code']='SL';
	    	$attendanceData['clock_in']=$post['shift_clock_in'];
	    	$attendanceData['clock_out']=$post['shift_clock_out'];
	    	$attendanceData['old_clock_times']=$post['main_clock_in']." - ".$post['main_clock_out'];
	    }
	    // ==============================
	    // 2. UPDATE OR INSERT main table
	    // ==============================
	    if($id > 0){
	        // UPDATE employee_daily_attendances
	        $attendanceData["updated_by"]   = $user_id;
	        $attendanceData["updated_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->updateRecord(
	            "employee_daily_attendances",
	            $attendanceData,
	            "id",
	            $id
	        );

	    } else {
	        // INSERT employee_daily_attendances
	        $attendanceData["created_by"]   = $user_id;
	        $attendanceData["created_date"] = date("Y-m-d H:i:s");

	        $res = $callConfig->insertRecord(
	            "employee_daily_attendances",
	            $attendanceData
	        );
	        $id = $res['LASTID'];
	    }

	    // ==============================
	    // 3. Insert attendance details
	    // ==============================
	    $detailData = array(
	        "company_id"     => $company_id,
	        "employee_id"    => $employee_id,
	        "attendace_date" => $attendace_date,
	        "clock_in"       => $clock_in,
	        "clock_out"      => $clock_out,
	        "leave_type_id"  => $leave_type_id,
	        "is_active"      => 0,
	        "current_status" => 0,
	        "created_by"     => $user_id,
	        "created_date"   => date("Y-m-d H:i:s")
	    );

	    $res1 = $callConfig->insertRecord(
	        "employee_daily_attendance_details",
	        $detailData
	    );

	    return array(
	        "status" => "success",
	        "attendance_id" => $id,
	        'res' => $res,
	        'res1' => $res1,
	        'post'=>$post
	    );
	}
	function getAutoAttendanceTime($get){
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $emp_id     = $get['emp_id'];
	    $date       = $get['date'];

	    // get config
	    $config = $callConfig->getRow(
	        "SELECT shift_mode 
	         FROM config 
	         WHERE is_active=0 
	         LIMIT 1"
	    );
	    $shift_mode = $config->shift_mode;
	    // CASE 1: No shift
	    if ((int)$shift_mode == 1) {

	        $att = $callConfig->getRow(
	            "SELECT full_day_hours 
	             FROM attendance_settings 
	             WHERE company_id=$company_id 
	               AND is_active=0 
	             ORDER BY id ASC LIMIT 1"
	        );

	        // Base clock-in (datetime)
		    $clockInDateTime = $date . ' 10:00:00';

		    // Convert HH:MM:SS → seconds
		    list($h, $m, $s) = explode(':', $att->full_day_hours);
		    $seconds = ($h * 3600) + ($m * 60) + $s;

		    // Calculate clock-out
		    $clockOutTimestamp = strtotime($clockInDateTime) + $seconds;

	        return [
	            'status'    => 'success',
	            // FRONTEND format (datetime-local)
		        'clock_in'  => date('Y-m-d\TH:i', strtotime($clockInDateTime)),
		        'clock_out' => date('Y-m-d\TH:i', $clockOutTimestamp),
	            'full_day_hours' => $att->full_day_hours
	        ];
	    }

	    // CASE 2: Shift based
	    $empShift = $callConfig->getRow(
	        "SELECT shift_id 
	         FROM emp_shifts 
	         WHERE emp_id=$emp_id 
	           AND company_id=$company_id 
	           AND is_active=0 
	         ORDER BY id DESC LIMIT 1"
	    );
	    $shift_id = $empShift->shift_id ?? 0;

	    if (!empty($shift_id)) {

	        $shift = $callConfig->getRow(
	            "SELECT clock_in, shift_setting_id 
	             FROM shifts 
	             WHERE id={$shift_id} 
	               AND company_id=$company_id 
	               AND is_active=0"
	        );
	        $shift_setting_id = $shift->shift_setting_id ?? 0;

	        if($shift_setting_id>0){
		        $shiftSet = $callConfig->getRow(
		            "SELECT full_day_hours 
		             FROM shift_settings 
		             WHERE id={$shift_setting_id} 
		               AND company_id=$company_id 
		               AND is_active=0"
		        );

        // Base clock-in datetime
        $clockInDateTime = $date . ' ' . $shift->clock_in;

        // Convert HH:MM:SS → seconds
        list($h, $m, $s) = explode(':', $shiftSet->full_day_hours);
        $seconds = ($h * 3600) + ($m * 60) + $s;

        // Calculate clock-out
        $clockOutTimestamp = strtotime($clockInDateTime) + $seconds;


		    return [
		            'status'    => 'success',
				// frontend-ready format
	            'clock_in'  => date('Y-m-d\TH:i', strtotime($clockInDateTime)),
	            'clock_out' => date('Y-m-d\TH:i', $clockOutTimestamp)
		        ];
		    }
	    }

	    return ['status' => 'error'];
	}
	/**
	 * Get all employees assigned with a specific buddy type
	 * Extracts UNIQUE buddy IDs from the specified column and returns employee data
	 */
	function getBuddyTypeEmployees($get, $post){
		global $callConfig;

		$buddy_type = isset($post['buddy_type']) ? $post['buddy_type'] : '';
		$company_id = isset($post['company_id']) ? $post['company_id'] : '';

		// Validate inputs
		if (empty($buddy_type) || empty($company_id)) {
			return array('success' => false, 'message' => 'Buddy type and company_id are required');
		}

		// Valid buddy type columns
		$valid_buddy_types = array('admin_buddy', 'project_buddy', 'finance_buddy', 'loc_buddy', 'hr_buddy');

		if (!in_array($buddy_type, $valid_buddy_types)) {
			return array('success' => false, 'message' => 'Invalid buddy type');
		}

		// Get DISTINCT/UNIQUE buddy IDs from the specified column
		$query = "SELECT DISTINCT " . $buddy_type . " FROM employees 
				  WHERE company_id='" . $company_id . "' 
				  AND is_active=0
				  AND " . $buddy_type . " IS NOT NULL 
				  AND " . $buddy_type . " != ''";

		$buddy_ids = $callConfig->getAllRows($query);

		if (!$buddy_ids || count($buddy_ids) == 0) {
			return array('success' => true, 'data' => array(), 'count' => 0);
		}

		// Extract unique IDs and build them for IN clause
		$unique_ids = array();
		foreach ($buddy_ids as $row) {
			$id_value = $row->{$buddy_type};
			if (!empty($id_value) && !in_array($id_value, $unique_ids)) {
				$unique_ids[] = intval($id_value);
			}
		}

		if (count($unique_ids) == 0) {
			return array('success' => true, 'data' => array(), 'count' => 0);
		}

		// Get employee data for those unique IDs
		$ids_list = implode(',', $unique_ids);
		$query = "SELECT id, emp_code, display_name as display_name 
				  FROM employees 
				  WHERE company_id='" . $company_id . "' 
				  AND is_active=0 
				  AND id IN (" . $ids_list . ")
				  ORDER BY id ASC";

		$employees = $callConfig->getAllRows($query);

		$employee_data = array();
		if ($employees && count($employees) > 0) {
			foreach ($employees as $emp) {
				$employee_data[] = array(
					'id' => $emp->id,
					'emp_code' => $emp->emp_code,
					'display_name' => $emp->display_name
				);
			}
		}

		return array(
			'success' => true,
			'data' => $employee_data,
			'count' => count($employee_data)
		);
	}

	/**
	 * Get employees affected by buddy reassignment (for preview)
	 */
	function getBuddyEmployees($get, $post){
		global $callConfig;

		$buddy_type = isset($post['buddy_type']) ? $post['buddy_type'] : '';
		$from_buddy = isset($post['from_buddy']) ? $post['from_buddy'] : '';
		$company_id = isset($post['company_id']) ? $post['company_id'] : '';

		if (empty($buddy_type) || empty($from_buddy) || empty($company_id)) {
			return array('success' => false, 'message' => 'Buddy type, from_buddy and company_id are required');
		}

		// Get all employees where the specified buddy field matches from_buddy
		$query = "SELECT e.id, e.emp_code, e.display_name, e.first_name, e.last_name, 
				  e.department_id, e.designation_id, e." . $buddy_type . "
				  FROM employees e
				  WHERE e.company_id='" . $company_id . "'
				  AND e.is_active=0
				  AND e." . $buddy_type . "='" . $from_buddy . "'";

		$employees = $callConfig->getAllRows($query);
        // return $employees;


		$affected_employees = array();
		if ($employees && count((array)$employees) > 0) {
			foreach ($employees as $emp) {
				// Get department name
				$dept_name = '';
				if ($emp->department_id) {
					$dept_query = "SELECT department_name FROM departments WHERE id='" . $emp->department_id . "' AND company_id='".$company_id."' AND is_active=0";
					$dept_row = $callConfig->getRow($dept_query);
					$dept_name = $dept_row ? $dept_row->department_name : '';
				}

				// Get designation name
				$desig_name = '';
				if ($emp->designation_id) {
                    $desig_query = "SELECT designation_name FROM designations WHERE id='" . $emp->designation_id . "' AND company_id='".$company_id."' AND is_active=0";
                    $desig_row = $callConfig->getRow($desig_query);
                    $desig_name = $desig_row ? $desig_row->designation_name : '';
                }


				// Get current buddy name
				$buddy_name = '';
				if ($emp->{$buddy_type}) {
					$buddy_query = "SELECT display_name, first_name, last_name FROM employees WHERE id='" . $emp->{$buddy_type} . "' AND company_id='".$company_id."' AND is_active=0";
					$buddy_row = $callConfig->getRow($buddy_query);
					if ($buddy_row) {
						$buddy_name = $buddy_row->display_name ?: ($buddy_row->first_name . ' ' . $buddy_row->last_name);
					}
				}

				$affected_employees[] = array(
					'id' => $emp->id,
					'emp_code' => $emp->emp_code,
					'display_name' => $emp->display_name ?: ($emp->first_name . ' ' . $emp->last_name),
					'department_name' => $dept_name,
					'designation_name' => $desig_name,
					'current_buddy_name' => $buddy_name
				);
			}
		}

		return array(
			'success' => true,
			'data' => $affected_employees,
			'count' => count($affected_employees)
		);
	}

	/**
	 * Confirm and execute buddy reassignment
	 */
	function confirmBuddyReassignment($get, $post){
		global $callConfig;
        
		$buddy_type = isset($post['buddy_type']) ? $post['buddy_type'] : '';
		$from_buddy = isset($post['from_buddy']) ? $post['from_buddy'] : '';
		$to_buddy = isset($post['to_buddy']) ? $post['to_buddy'] : '';
		$employee_ids = isset($post['employee_ids']) ? json_decode($post['employee_ids'], true) : array();
		$company_id = isset($post['company_id']) ? $post['company_id'] : '';
		$user_id = isset($post['user_id']) ? $post['user_id'] : '';

		// Validate inputs
		if (empty($buddy_type) || empty($from_buddy) || empty($to_buddy)) {
			return array('success' => false, 'message' => 'All fields (buddy_type, from_buddy, to_buddy) are required');
		}

		if ($from_buddy == $to_buddy) {
			return array('success' => false, 'message' => 'From buddy and To buddy cannot be the same');
		}

		if (empty($employee_ids) || !is_array($employee_ids)) {
			return array('success' => false, 'message' => 'No employees selected for reassignment');
		}

		// Update each employee's buddy field
		$updated_count = 0;
		$failed_updates = array();
		$current_date = date('Y-m-d H:i:s');

		foreach ($employee_ids as $emp_id) {
			$update_query = "UPDATE employees 
							SET " . $buddy_type . "='" . $to_buddy . "', 
								updated_by='" . $user_id . "',
								updated_date='" . $current_date . "'
							WHERE id='" . $emp_id . "'";

			$result = $callConfig->executeQuery($update_query);

			if ($result) {
				$updated_count++;
			} else {
				$failed_updates[] = $emp_id;
			}
		}

		// Insert record into buddy change log
		$log_data = array(
			'buddy_type' => $buddy_type,
			'from_buddy' => $from_buddy,
			'to_buddy' => $to_buddy,
			'employee_ids' => json_encode($employee_ids),
			'employee_count' => count($employee_ids),
			'company_id' => $company_id,
			'is_active' => '0',
			'current_status' => '0',
			'created_by' => $user_id,
			'created_date' => $current_date
		);
		
		$log_result = $callConfig->insertRecord('employee_buddy_change_log', $log_data);

		if ($updated_count > 0) {
			return array(
				'success' => true,
				'message' => 'Successfully reassigned ' . $updated_count . ' employee(s)',
				'updated_count' => $updated_count,
				'failed_count' => count($failed_updates),
                'log_result' => $log_result
			);
		} else {
			return array(
				'success' => false,
				'message' => 'Failed to update employees'
			);
		}
	}

	function getEmployeeAttendanceLocationDetails($get){
	    global $callConfig;
	    $return = array(
	        'details' => array(),
	        'employee' => array(),
	        'attendance_date' => $get['attendance_date'] ?? ''
	    );


	    $employee_id = $get['employee_id'] ?? 0;
	    $attendance_date = $get['attendance_date'] ?? '';

	    if (!$employee_id || !$attendance_date) {
	        return $return;
	    }

	    $emp_qry = "SELECT id, display_name, emp_code
	        FROM employees
	        WHERE id = {$employee_id}
	          AND company_id = {$get['company_id']}
	          AND is_active = 0
	        LIMIT 1";
	    $employee = $callConfig->getRow($emp_qry);
	    $return['employee'] = $employee ? (array)$employee : array();

		

	    $details_qry = "SELECT id, employee_id, attendace_date, clock_in, clock_out,
	            check_in_type, check_out_type,
	            clock_in_lat, clock_in_long, clock_out_lat, clock_out_long, created_date
	        FROM employee_daily_attendance_details
	        WHERE employee_id = {$employee_id}
	          AND company_id = {$get['company_id']}
	          AND attendace_date = '{$attendance_date}'
	          AND is_active = 0
	        ORDER BY clock_in ASC, id ASC";
	    $details = $callConfig->getAllRows($details_qry);


	    $total_checkins = 0;
	    $total_checkouts = 0;
	    $total_seconds = 0;

	    foreach ($details as $row) {
	        if (!empty($row->clock_in)) {
	            $total_checkins++;
	        }
	        if (!empty($row->clock_out)) {
	            $total_checkouts++;
	        }
	        if (!empty($row->clock_in) && !empty($row->clock_out)) {
	            $clock_in_ts = strtotime($attendance_date . ' ' . $row->clock_in);
	            $clock_out_ts = strtotime($attendance_date . ' ' . $row->clock_out);
	            if ($clock_out_ts > $clock_in_ts) {
	                $total_seconds += ($clock_out_ts - $clock_in_ts);
	            }
	        }
	    }

	    $return['details'] = array_map(function ($row) {
	        return (array)$row;
	    }, $details);
	    $return['total_checkins'] = $total_checkins;
	    $return['total_checkouts'] = $total_checkouts;
	    $return['total_duration'] = gmdate('H:i:s', $total_seconds);
	    $return['attendance_date'] = $attendance_date;

	    return $return;
	}


	/**
	 * Get all employees and leave types for the dropdown
	 */
	function getLeaveEmployees($get){
	    global $callConfig;
	    $return = array();
	    $company_id = $get['company_id'];
	    
	    // Get all active employees
	    $query = "SELECT id, display_name, emp_code 
	              FROM employees 
	              WHERE company_id = '{$company_id}' 
	              AND is_active = 0 
	              ORDER BY display_name ASC";
	    
	    $employees = $callConfig->getAllRows($query);
	    $return['employees'] = $employees ? array_map(function($row) {
	        return (array)$row;
	    }, $employees) : array();
	    
	    // Get all active leave types
	    $query = "SELECT id, leave_type_name, back_ground_color 
	              FROM leave_types 
	              WHERE company_id = '{$company_id}' 
	              AND is_active = 0 
	              ORDER BY leave_type_name ASC";
	    
	    $leaveTypes = $callConfig->getAllRows($query);
	    $return['leave_types'] = $leaveTypes ? array_map(function($row) {
	        return (array)$row;
	    }, $leaveTypes) : array();
	    
	    $return['status'] = 'success';
	    return $return;
	}

	/**
	 * Get leave balance and allocation data for selected employee
	 */
	function getLeaveBalanceAndAllocations($get){
	    global $callConfig;
	    $return = array();
	    $company_id = $get['company_id'];
	    $employee_id = $get['employee_id'] ?? 0;
	    $leave_year = $get['leave_year'] ?? date('Y');
	    
	    // Get leave balances with leave type details
	    if ($employee_id > 0) {
	        $query = "SELECT 
	                eb.id, 
	                eb.employee_id,
	                eb.leave_type,
	                eb.applicable_leaves,
	                eb.consumed_leaves,
	                eb.unapproved_leaves,
	                eb.leave_year,
	                lt.leave_type_name,
	                lt.back_ground_color
	            FROM emp_leave_balances eb
	            LEFT JOIN leave_types lt ON lt.id = eb.leave_type 
	                AND lt.company_id = '{$company_id}' 
	                AND lt.is_active = 0
	            WHERE eb.employee_id = '{$employee_id}'
	            AND eb.company_id = '{$company_id}'
	            AND eb.leave_year = '{$leave_year}'
	            AND eb.is_active = 0
	            ORDER BY lt.leave_type_name ASC";
	        
	        $balances = $callConfig->getAllRows($query);
	        $return['balances'] = $balances ? array_map(function($row) {
	            return (array)$row;
	        }, $balances) : array();
	        
	        // Get leave allocations with leave type details
	        $query = "SELECT 
	                ea.id,
	                ea.employee_id,
	                ea.leave_type,
	                ea.applicable_leaves,
	                ea.consumed_leaves,
	                ea.note,
	                ea.transaction_date,
	                ea.leave_year,
	                ea.current_status,
	                ea.type,
	                lt.leave_type_name,
	                lt.back_ground_color
	            FROM emp_leave_allocations ea
	            LEFT JOIN leave_types lt ON lt.id = ea.leave_type 
	                AND lt.company_id = '{$company_id}' 
	                AND lt.is_active = 0
	            WHERE ea.employee_id = '{$employee_id}'
	            AND ea.company_id = '{$company_id}'
	            AND ea.leave_year = '{$leave_year}'
	            AND ea.is_active = 0
	            ORDER BY ea.transaction_date ASC";
	        
	        $allocations = $callConfig->getAllRows($query);
	        $return['allocations'] = $allocations ? array_map(function($row) {
	            return (array)$row;
	        }, $allocations) : array();
	        
	        // Get selected employee details
	        $empQuery = "SELECT id, display_name, emp_code 
	                     FROM employees 
	                     WHERE id = '{$employee_id}' 
	                     AND company_id = '{$company_id}' 
	                     AND is_active = 0 
	                     LIMIT 1";
	        $employee = $callConfig->getRow($empQuery);
	        $return['selected_employee'] = $employee ? (array)$employee : array();
	    }
	    
	    $return['status'] = 'success';
	    return $return;
	}

	/**
	 * Save leave allocation and update leave balance
	 */
	function saveLeaveAllocation($get, $post){
	    global $callConfig;
	    $return = array();
	    
	    $company_id = $get['company_id'];
	    $user_id = $get['user_id'];
	    $employee_id = $post['employee_id'] ?? 0;
	    $leave_type_id = $post['leave_type_id'] ?? 0;
	    $applicable_leaves = floatval($post['applicable_leaves'] ?? 0);
	    $consumed_leaves = floatval($post['consumed_leaves'] ?? 0);
	    $transaction_date = $post['transaction_date'] ?? date('Y-m-d');
	    $note = $post['note'] ?? '';
	    $leave_year = $post['leave_year'] ?? date('Y');
	    
	    // Validation
	    if ($employee_id <= 0) {
	        return array('status' => 'error', 'message' => 'Invalid employee selected');
	    }
	    
	    if ($leave_type_id <= 0) {
	        return array('status' => 'error', 'message' => 'Invalid leave type selected');
	    }
	    
	    if ($applicable_leaves == 0 && $consumed_leaves == 0) {
	        return array('status' => 'error', 'message' => 'Please enter at least one leave value');
	    }
	    
	    // Validate transaction date
	    if (!$this->isValidDate($transaction_date)) {
	        return array('status' => 'error', 'message' => 'Invalid transaction date');
	    }
	    
	    // ==============================
	    // 1. Update emp_leave_balances
	    // ==============================
	    $balanceQuery = "SELECT id, applicable_leaves, consumed_leaves 
	                     FROM emp_leave_balances 
	                     WHERE employee_id = '{$employee_id}'
	                     AND leave_type = '{$leave_type_id}'
	                     AND leave_year = '{$leave_year}'
	                     AND company_id = '{$company_id}'
	                     AND is_active = 0
	                     LIMIT 1";
	    
	    $existingBalance = $callConfig->getRow($balanceQuery);
	    
	    if ($existingBalance) {
	        // Update existing balance
	        $newApplicable = floatval($existingBalance->applicable_leaves) + $applicable_leaves;
	        $newConsumed = floatval($existingBalance->consumed_leaves) + $consumed_leaves;
	        
	        $updateData = array(
	            "applicable_leaves" => $newApplicable,
	            "consumed_leaves" => $newConsumed,
	            "updated_by" => $user_id,
	            "updated_date" => date("Y-m-d H:i:s")
	        );
	        
	        $callConfig->updateRecord(
	            "emp_leave_balances",
	            $updateData,
	            "id",
	            $existingBalance->id
	        );
	    } else {
	        // Create new balance record
	        $insertData = array(
	            "company_id" => $company_id,
	            "employee_id" => $employee_id,
	            "leave_type" => $leave_type_id,
	            "applicable_leaves" => $applicable_leaves,
	            "consumed_leaves" => $consumed_leaves,
	            "leave_year" => $leave_year,
	            "blance_year" => $leave_year, // Note: misspelled in DB schema
	            "is_active" => 0,
	            "current_status" => 0,
	            "status_date" => date("Y-m-d H:i:s"),
	            "created_by" => $user_id,
	            "created_date" => date("Y-m-d H:i:s")
	        );
	        
	        $callConfig->insertRecord("emp_leave_balances", $insertData);
	    }
	    
	    // ==============================
	    // 2. Insert into emp_leave_allocations
	    // ==============================
	    $allocationData = array(
	        "company_id" => $company_id,
	        "employee_id" => $employee_id,
	        "leave_type" => $leave_type_id,
	        "applicable_leaves" => $applicable_leaves,
	        "consumed_leaves" => $consumed_leaves,
	        "note" => $note,
	        "type" => 0, // 0 = add leaves
	        "leave_year" => $leave_year,
	        "blance_year" => $leave_year, // Note: misspelled in DB schema
	        "transaction_date" => $transaction_date, // Append time
	        "is_active" => 0,
	        "current_status" => 0,
	        "status_date" => date("Y-m-d H:i:s"),
	        "created_by" => $user_id,
	        "created_date" => date("Y-m-d H:i:s")
	    );
	    
	    $insertResult = $callConfig->insertRecord("emp_leave_allocations", $allocationData);
	    
	    if ($insertResult && isset($insertResult['LASTID']) && $insertResult['LASTID'] > 0) {
	        $return['status'] = 'success';
	        $return['message'] = 'Leave allocation saved successfully';
	        $return['allocation_id'] = $insertResult['LASTID'];
	    } else {
	        $return['status'] = 'error';
	        $return['message'] = 'Error saving leave allocation';
	    }
	    
	    return $return;
	}

	/**
	 * Validate date format
	 */
	private function isValidDate($date, $format = 'Y-m-d'){
	    $d = \DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) === $date;
	}

		/**
	 * Save clock time - Inline editing from attendance report
	 * Handles both insert and update of clock_in/clock_out times
	 * Automatically calculates attendance status code (P/Z/A/MP)
	 * Also inserts/updates record in employee_daily_attendance_details
	 * 
	 * @param array $get - Contains company_id, user_id
	 * @param array $post - Contains rid, employee_id, attendace_date, check_type, clock_value, user_id
	 * @return array - Response with status, clock times, attendance code, and error messages if any
	 */
    function saveClockTime($get, $post) {
        global $callConfig;
 
        $company_id = $get['company_id'] ?? 0;
        $user_id = $post['user_id'] ?? 0;
        $rid = $post['rid'] ?? 0;
        $emp_id = $post['employee_id'] ?? 0;
        $attendace_date = $post['attendace_date'] ?? '';
        $check_type = $post['check_type'] ?? ''; // 'clockin' or 'clockout'
        $clock_value = $post['clock_value'] ?? ''; // HH:MM format 
        
        if (strlen($clock_value) == 5) { // HH:MM format
            $clock_value = $clock_value . ':00';
        }
        $clock_in = '';
        $clock_out = '';
        $attendance_key_short_code = '';
        $old_clock_times = ''; // For audit trail

        $existing = array(); 
        if ($rid > 0) {            
            $query = "SELECT clock_in, clock_out FROM employee_daily_attendances
                    WHERE id = {$rid} AND company_id = {$company_id} AND is_active = 0 LIMIT 1";
            $existing = $callConfig->getRow($query);
            if ($existing) {
                $clock_in = $existing->clock_in ?? '';
                $clock_out = $existing->clock_out ?? '';
               
                // Store old times for audit trail before overwriting
                if ($clock_in || $clock_out) {
                    $old_clock_times = ($clock_in ?: '--') . ' - ' . ($clock_out ?: '--');
                }
            }
            
        }
 		$clock_out_date = $attendace_date; // default
		$clock_in_date  = $attendace_date; // default
        if ($check_type == 'clockin') {
            $clock_in = $clock_value;
        }elseif ($check_type == 'clockout') {
		    $clock_out = $clock_value;
		    if (!empty($existing) && !empty($existing->clock_in)) {
		        $clock_in = $existing->clock_in;
		        // Compare times
		        if (strtotime($clock_out) < strtotime($clock_in)) {
		            // Night shift → next day
		            $clock_out_date = date('Y-m-d', strtotime($attendace_date . ' +1 day'));
		        } else {
		            $clock_out_date = $attendace_date;
		        }

		    } else {
		        $clock_out_date = $attendace_date;
		    }
		}
        $record_id = $rid;
        $data_array = array(
            'company_id' => $company_id,
            'employee_id' => $emp_id,
            'attendace_date' => $attendace_date,
            'clock_in_date' => $clock_in_date,
    		'clock_out_date' => $clock_out_date,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'attendance_key_short_code' => '',
            'old_clock_times'=> $old_clock_times,
            'is_active' => 0,
            'current_status' => 0,
            'updated_by' => $user_id,
            'updated_date' => date('Y-m-d H:i:s')
        );

        if ($rid > 0) {
            $result = $callConfig->updateRecord('employee_daily_attendances', $data_array, 'id', $rid);
        }else{
                $data_array['created_by'] = $user_id;
                $data_array['created_date'] = date('Y-m-d H:i:s');
                $result = $callConfig->insertRecord('employee_daily_attendances', $data_array);
        }
        $post['shift_from'] = $attendace_date;
        $post['shift_to'] =  $attendace_date;
        $post['emp_id'] = $emp_id;
        if ($rid != '') {
            $res = $this->employeeattendancechangebaseonshift($get,$post);
        }
        return array(
            'status' => 'success',
            'record_id' => $record_id,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'attendance_code' => $attendance_key_short_code,
            'message' => 'Time updated successfully in both main and detail tables'
        );
    }

    function employeeattendancechangebaseonshift($get,$post){

	    global $callConfig;
	    
	    $shift_from = str_replace('/"', '-', $post['shift_from']);
		$fromDate =  date('Y-m-d', strtotime($shift_from));
		$shift_to = str_replace('/"', '-', $post['shift_to']);
		$toDate =  date('Y-m-d', strtotime($shift_to));

	    /* Step 1: get attendance rows in date range */
	    $query = "
	        SELECT id, employee_id, clock_in, clock_out, attendace_date, clock_in_date, clock_out_date
	        FROM employee_daily_attendances
	        WHERE attendace_date BETWEEN '{$fromDate}' AND '{$toDate}'
	          AND company_id = {$get['company_id']}
	          AND employee_id = {$post['emp_id']}
	          AND is_active = 0 
	    ";
	    $rows = $callConfig->getAllRows($query);
	    //print_r($rows); exit;
	    $updated = [];

	    foreach($rows as $row){
	    if($row->employee_id!=""){
	        $attendance_id = $row->id;
	        $emp_id        = $row->employee_id;
	        $clock_in      = $row->clock_in ?? '';
	        $clock_out     = $row->clock_out ?? '';

	        $attendance_key_short_code = "";
	        //print_r($row); exit;
	        /* Step 2: MP logic */
	        if(
	            ($clock_in != '' && $clock_in != '00:00:00' && $clock_in != '00:00') &&
	            ($clock_out == '' || $clock_out == '00:00:00' || $clock_out == '00:00')
	        ){
	            $attendance_key_short_code = 'MP';
	        }

	        /* Step 3: Present / Half / Absent logic */
	        if(
	            ($clock_in != '' && $clock_in != '00:00:00' && $clock_in != '00:00') &&
	            ($clock_out != '' && $clock_out != '00:00:00' && $clock_out != '00:00')
	        ){

	            $query = "SELECT id, shift_id 
	                      FROM emp_shifts 
	                      WHERE emp_id = {$emp_id} 
	                        AND company_id = {$get['company_id']} 
	                        AND is_active = 0";
	            $data1 = $callConfig->getRow($query);

	            if(!empty($data1) && $data1->shift_id > 0){

	                $query = "SELECT * 
	                          FROM shifts 
	                          WHERE id = {$data1->shift_id} 
	                            AND company_id = {$get['company_id']} 
	                            AND is_active = 0";
	                $data2 = $callConfig->getRow($query);

	                if(!empty($data2) && $data2->shift_setting_id > 0){

	                    $query = "SELECT * 
	                              FROM shift_settings 
	                              WHERE id = {$data2->shift_setting_id} 
	                                AND company_id = {$get['company_id']} 
	                                AND is_active = 0";
	                    $data3 = $callConfig->getRow($query);
	                    //echo "<pre>"; print_r($row); print_r($data1);print_r($data2);print_r($data3); exit;
	                    if(!empty($data3)){

	                    	$clockInDate = $row->clock_in_date;
	                    	$clockOutDate = $row->clock_out_date;

	                       // $in  = strtotime($clock_in);
	                        //$out = strtotime($clock_out);
	                        $clockInTime  = $clock_in;
	                        $clockOutTime = $clock_out;

	                        $in  = new DateTime($clockInDate . ' ' . $clockInTime);
							$out = new DateTime($clockOutDate . ' ' . $clockOutTime);

	                        if($out > $in){

	                            //$workedMinutes = ($out - $in) / 60;
	                           $workedMinutes =  ($out->getTimestamp() - $in->getTimestamp()) / 60;
	                           
	                            list($fd_h, $fd_m) = explode(':', $data3->minimum_hours_required);
	                            $fullDayMinutes = ((int)$fd_h * 60) + (int)$fd_m;

	                            list($hd_h, $hd_m) = explode(':', $data3->half_day_hours);
	                            $halfDayMinutes = ((int)$hd_h * 60) + (int)$hd_m;

	                            // Grace period (HH:MM:SS) → minutes
								$graceParts = explode(':', $data3->grace_perieod);
								$g_h = isset($graceParts[0]) ? (int)$graceParts[0] : 0;
								$g_m = isset($graceParts[1]) ? (int)$graceParts[1] : 0;
								// ignore seconds safely
								$graceMinutes = ($g_h * 60) + $g_m;

								$nclockIn      = $data2->clock_in;$ngracePeriod  = $data3->grace_perieod;
								/*$clockInTime = new DateTime($nclockIn);
								$clockInTime->modify('+' . $ngracePeriod);
								$check_grace = $clockInTime->format('H:i:s');*/
								$seconds =   (strtotime($nclockIn) - strtotime('today')) + (strtotime($ngracePeriod) - strtotime('today'));
								$check_grace = gmdate('H:i:s', $seconds);
								//echo $nclockIn."//".$ngracePeriod."//";
								//echo "<pre>".$check_grace ; print_r($row); print_r($data1);print_r($data2);print_r($data3); 
								//echo "clockInDate:".$clockInDate; echo "/// clockOutDate:".$clockOutDate; exit;

								$cccheck = strtotime($check_grace);
								$ccin  = strtotime($clock_in);
								$query = "SELECT id,emp_code, designation_id FROM employees WHERE id=".$emp_id;
								$empddata = $callConfig->getRow($query);
								$des = $empddata->designation_id;

								
	                            if($workedMinutes < ($fullDayMinutes - $graceMinutes) && $workedMinutes >= $halfDayMinutes){
	                                $attendance_key_short_code = 'Z'; // Half Day
	                            }elseif($workedMinutes < $halfDayMinutes){
	                                $attendance_key_short_code = 'A'; // Absent
	                            }else{
	                                $attendance_key_short_code = 'P'; // Present
	                            }
	                            if($des!=5){
									if($cccheck<$ccin){
										$attendance_key_short_code = 'Z'; 
									}
								}
	                        }
	                    }
	                }
	            }
	        }

	        /* Step 4: update only attendance_key_short_code by id */
	        // if($attendance_key_short_code != ""){

	            $updateArray = array(
	                'attendance_key_short_code' => $attendance_key_short_code
	            );

	            $res = $callConfig->updateRecord( 'employee_daily_attendances', $updateArray,	                'id',$attendance_id    );
	            //echo "<br>Emp ID".$emp_id."/ clock In".$clockInDate . '-' . $clockInTime."/ clock_out".$clockOutDate . '-' . $clockOutTime."/ short code".$attendance_key_short_code;
	            $updated[] = $attendance_id;
	        // }
	    }
	    
	    } //exit;
	    return array(
	        'updated_count' => count($updated),
	        'updated_ids'   => $updated
	    );
	}
	/**
	 * Calculate attendance code based on clock times
	 * Returns: P (Full Day), Z (Half Day), A (Absent/Below Min), MP (Morning Present)
	 * 
	 * @param string $clock_in - Clock in time (HH:MM:SS)
	 * @param string $clock_out - Clock out time (HH:MM:SS)
	 * @param int $emp_id - Employee ID
	 * @param int $company_id - Company ID
	 * @return string - Attendance code
	 */
	private function calculateAttendanceCode($clock_in, $clock_out, $emp_id, $company_id) {
		global $callConfig;
		$attendance_key_short_code = '';
		// Case A: Only clock_in, no clock_out → MP (Morning Present)
		if (($clock_in != '' && ($clock_in != '00:00:00' && $clock_in != '00:00')) && 
			($clock_out == '' || $clock_out == '00:00:00' || $clock_out == '00:00')) {
			$attendance_key_short_code = 'MP';
		}

		// Case B: Both clock_in and clock_out → Calculate based on hours
		if (($clock_in != '' && ($clock_in != '00:00:00' && $clock_in != '00:00')) && 
			($clock_out != '' && ($clock_out != '00:00:00' && $clock_out != '00:00'))) {

			$query = "SELECT id, designation_id, b_machine_id From employees WHERE id = {$emp_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1";
			$emp_datta= $callConfig->getRow($query);
			$des = $emp_datta->designation_id ?? 0;
			// Fetch employee's shift
			$query = "SELECT id, shift_id FROM emp_shifts 
					WHERE emp_id = {$emp_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1";
			$data1 = $callConfig->getRow($query);

            if(!empty($data1) && $data1->shift_id > 0){
                $query = "SELECT id, shift_setting_id FROM shifts WHERE id = {$data1->shift_id} AND company_id={$company_id} AND is_active=0";
                $data2= $callConfig->getRow($query);
                if(!empty($data2) && $data2->shift_setting_id > 0){
                    $query = "SELECT * FROM shift_settings WHERE id = {$data2->shift_setting_id} AND company_id={$company_id} AND is_active=0";
                    $data3 = $callConfig->getRow($query);
                    if (!empty($data3)) {
                        $in  = strtotime($clock_in);
                        $out = strtotime($clock_out);
                        if ($out > $in) {
                            $workedMinutes = ($out - $in) / 60;
                            list($fd_h, $fd_m) = explode(':', $data3->full_day_hours);
                            $fullDayMinutes = ((int)$fd_h * 60) + (int)$fd_m;
                            list($hd_h, $hd_m) = explode(':', $data3->half_day_hours);
                            $halfDayMinutes = ((int)$hd_h * 60) + (int)$hd_m;
                            // $graceMinutes = (int)$data3->grace_perieod;
                            // Grace period (HH:MM:SS) → minutes
                            $graceParts = explode(':', $data3->grace_perieod);
                            $g_h = isset($graceParts[0]) ? (int)$graceParts[0] : 0;
                            $g_m = isset($graceParts[1]) ? (int)$graceParts[1] : 0;
                            // ignore seconds safely
                            $graceMinutes = ($g_h * 60) + $g_m;

                            $nclockIn      = $data2->clock_in;
                            $ngracePeriod  = $data3->grace_perieod;
                            $seconds =   (strtotime($nclockIn) - strtotime('today')) + (strtotime($ngracePeriod) - strtotime('today'));
                            $check_grace = gmdate('H:i:s', $seconds);
                            $cccheck = strtotime($check_grace);
                            $ccin  = strtotime($clock_in);
                            if ($workedMinutes < ($fullDayMinutes - $graceMinutes) && $workedMinutes >= $halfDayMinutes) {
                                $attendance_key_short_code = 'Z';
                            }elseif ($workedMinutes < $halfDayMinutes) {
                                $attendance_key_short_code = 'A';
                            }else {
                                $attendance_key_short_code = 'P';
                            }
                            if($des!=5){
                                if($cccheck<$ccin){
                                    $attendance_key_short_code = 'Z'; 
                                }
                            }
                        }
                    }
                }
            }
		}

		// Default: Absent
		return $attendance_key_short_code ?: 'A';
	}
	function getvehiclegatewaypending($get){
		global $callConfig;
		try {
			$company_id = $get['company_id'];
			$process_value = "tg.process_val = '0' OR tg.process_val IS NULL";
			$query = "
				SELECT tg.*, emp.display_name
				FROM transport_gateway tg
				LEFT JOIN employees emp ON emp.user_id = tg.created_by AND emp.company_id={$company_id} AND emp.is_active=0 
				WHERE  tg.company_id = {$company_id}
				AND tg.is_active = 0 
				AND tg.current_status IN (0, 199)
				AND {$process_value}
				ORDER BY created_date DESC
			";
			$vehicles = $callConfig->getAllRows($query);
			return ['vehicles' => $vehicles];
		} catch (Exception $e) {
			error_log("getvehiclegatewaypending: Exception - " . $e->getMessage());
			return ['vehicles' => [], 'error' => $e->getMessage()];
		}
	}

	function getvehiclegatewaymissing($get){
		global $callConfig;
		try {
			$company_id = $get['company_id'];
			$process_value = "tg.process_val = 1";
			$query = "
				SELECT tg.*, emp.display_name
				FROM transport_gateway tg
				LEFT JOIN employees emp ON emp.user_id = tg.created_by AND emp.company_id={$company_id} AND emp.is_active=0
				WHERE tg.company_id = {$company_id}
				AND tg.is_active = 0
				AND tg.current_status IN (199)
				AND {$process_value}
				ORDER BY created_date DESC
			";
			$vehicles = $callConfig->getAllRows($query);
			return ['vehicles' => $vehicles];
		} catch (Exception $e) {
			error_log("getvehiclegatewaymissing: Exception - " . $e->getMessage());
			return ['vehicles' => [], 'error' => $e->getMessage()];
		}
	}
	/**
	 * Update vehicle gateway exit/entry time
	 * @param mixed $post POST data containing vehicle_id, exit_date, exit_time, method_type, etc.
	 * @return array Response with status
	 */
	function updatevehiclegatewayexit($get,$post)
	{
		global $callConfig;
		date_default_timezone_set('Asia/Kolkata');
		try {
			$vehicle_id = $post['vehicle_id'] ?? null;
			$exit_date = $post['exit_date'] ?? date('Y-m-d');
			$exit_time = $post['exit_time'] ?? date('H:i:s');
			$company_id = $post['company_id'] ?? $get['company_id'];
			$user_id = $post['user_id'] ?? $get['user_id'];
			$exit_notes = $post['exit_notes'] ?? '';
			$goods_checked = $post['goods_checked'] ?? 0;
			$security_check = $post['security_check'] ?? 0;
			$authorized_by = $post['authorized_by'] ?? '';
			$meater_reading = $post['meater_reading'] ?? NULL;

			if (!$vehicle_id) {
				return ['status' => 'error', 'message' => 'Vehicle ID is required'];
			}

			// Get the vehicle details first
			$query = "SELECT * FROM transport_gateway WHERE id = {$vehicle_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1";
			$vehicle = $callConfig->getRow($query);

			if (!$vehicle) {
				return ['status' => 'error', 'message' => 'Vehicle not found'];
			}

			// Format time to HH:MM:SS if only HH:MM provided
			if (strlen($exit_time) == 5) {
				$exit_time = $exit_time . ':00';
			}

			$exit_timestamp = $exit_date . ' ' . $exit_time;

			// Determine which field to update based on method_type and current state
			$method_type = $post['method_type'] ?? $vehicle->method_type ?? 'entry_first';
			$entry_time_recorded = isset($vehicle->entry_time) && $vehicle->entry_time && $vehicle->entry_time != '00:00:00';
			$exit_time_recorded = isset($vehicle->exit_time) && $vehicle->exit_time && $vehicle->exit_time != '00:00:00';

			$updateData = array(
				"updated_date" => date("Y-m-d H:i:s"),
				"exit_notes" => $exit_notes,
				"goods_checked" => $goods_checked,
				"security_check" => $security_check,
				"authorized_by" => $authorized_by,
				"updated_by" => $user_id,
				"updated_date" => date("Y-m-d H:i:s"),
				"process_val" => 0
			);

			if ($method_type === 'exit_first') {
				// For exit_first: first record is exit_time, second is entry_time
				if ($vehicle->process_val ==1) {
					// Recording exit time
					$updateData['exit_time'] = $exit_time;
					$updateData['exit_date'] = $exit_date;
					$updateData['exit_timestamp'] = $exit_timestamp;
					$updateData['vehicle_exit_reading'] = $meater_reading;
				} else {
					// Recording entry time (return)
					$updateData['entry_time'] = $exit_time;
					$updateData['entry_date'] = $exit_date;
					$updateData['entry_timestamp'] = $exit_timestamp;
					$updateData['vehicle_entry_reading'] = $meater_reading;

				}
			} else {
				// For entry_first: first record is entry_time, second is exit_time
				if ($vehicle->process_val ==1) {
					// Recording entry time
					$updateData['entry_time'] = $exit_time;
					$updateData['entry_date'] = $exit_date;
					$updateData['entry_timestamp'] = $exit_timestamp;
					$updateData['vehicle_entry_reading'] = $meater_reading;
				} else {
					// Recording exit time (return)
					$updateData['exit_time'] = $exit_time;
					$updateData['exit_date'] = $exit_date;
					$updateData['exit_timestamp'] = $exit_timestamp;
					$updateData['vehicle_exit_reading'] = $meater_reading;

				}
			}

			$result = $callConfig->updateRecord('transport_gateway', $updateData, 'id', $vehicle_id);
			$this->toupdatetransportgatewayprocess_val(['id'=>$vehicle_id,'company_id'=>$company_id]);

			if ($result) {
				return ['status' => 'success', 'message' => 'Vehicle exit recorded successfully', 'result' => $result];
			} else {
				return ['status' => 'error', 'message' => 'Failed to update vehicle record'];
			}
		} catch (Exception $e) {
			error_log("updatevehiclegatewayexit: Exception - " . $e->getMessage());
			return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
		}
	}

	function toupdatetransportgatewayprocess_val($get){
	    global $callConfig;
	    try {
	        $id = $get['id'] ?? null;
	        $company_id = $get['company_id'];
	        if(!$id){
	            return ['status'=>'error','message'=>'ID is required'];
	        }
	        // Get record
	        $sql = "SELECT entry_date, entry_time, exit_date, exit_time 
	                FROM transport_gateway 
	                WHERE id='".$id."' 
	                AND company_id='".$company_id."' 
	                AND is_active=0 
	                LIMIT 1";
	        $row = $callConfig->getRow($sql);
	        if(!$row){
	            return ['status'=>'error','message'=>'Record not found'];
	        }
	        // Check valid values
	        $entry_date_valid = !empty($row->entry_date) && $row->entry_date != '0000-00-00';
	        $entry_time_valid = !empty($row->entry_time) && $row->entry_time != '00:00:00';

	        $exit_date_valid  = !empty($row->exit_date) && $row->exit_date != '0000-00-00';
	        $exit_time_valid  = !empty($row->exit_time) && $row->exit_time != '00:00:00';
	        if($entry_date_valid && $entry_time_valid && $exit_date_valid && $exit_time_valid){
	            $updateData = array(
	                "process_val" => 2,
	                "updated_date" => date("Y-m-d H:i:s")
	            );
	            $result = $callConfig->updateRecord('transport_gateway',$updateData,'id',$id);
	            return [
	                'status'=>'success',
	                'message'=>'Process value updated',
	                'result'=>$result
	            ];
	        }
	        return [
	            'status'=>'success',
	            'message'=>'Entry/Exit data not completed yet'
	        ];

	    } catch(Exception $e){
	        error_log("toupdatetransportgatewayprocess_val: ".$e->getMessage());
	        return ['status'=>'error','message'=>$e->getMessage()];
	    }
	}

	function getpersonalgateway($get){
        global $callConfig;
        $return = [];
 
        try {
            $company_id = $get['company_id'] ?? $_SESSION['company_id'];
            $today = date('Y-m-d');
 
            $work_admin_type  = $get['work_admin_type'];
            $employee_id = $get['employee_id'];
           
            $employeeCondition = "";
           
            if ($work_admin_type != 1 && $employee_id > 0) {
                $employeeCondition = "
                    AND JSON_UNQUOTE(
                        JSON_EXTRACT(person_details, '$.employee_id')
                    ) = '{$employee_id}'
                ";
            }
 
            $current_query = "
                SELECT *
                FROM transport_personal_gateway
                WHERE company_id = {$company_id}
                AND is_active = 0
                {$employeeCondition}
                ORDER BY created_date DESC
            ";
            $current_persons = $callConfig->getAllRows($current_query);
           
            // Parse person_details for each entry
            $formatted_persons = [];
            if ($current_persons && is_array($current_persons)) {
                foreach ($current_persons as $person) {
                    $personData = [];
                    if (!empty($person->person_details)) {
                        $personData = json_decode($person->person_details, true) ?: [];
                    }
                    $formatted_persons[] = [
                        'id' => $person->id,
                        'employee_id' => $personData['employee_id'] ?? '',
                        'employee_name' => $personData['employee_name'] ?? '',
                        'employee_contact' => $personData['employee_contact'] ?? '',
                        'employee_email' => $personData['employee_email'] ?? '',
                        'vehicle_number' => $person->vehicle_number,
                        'visit_purpose' => $person->visit_purpose,
                        'location' => $person->location,
                        'entry_date' => $person->entry_date,
                        'entry_time' => $person->entry_time,
						'process_val' => $person->process_val,
                        'entry_timestamp' => $person->entry_timestamp,
                        'exit_date' => $person->exit_date,
                        'exit_time' => $person->exit_time,
                        'exit_timestamp' => $person->exit_timestamp,
                        'method_type' => $person->method_type ?? 'entry_first',
                        'current_status' => $person->current_status,
                        'gateway_type' => $person->gateway_type,
                        'requested_by' => $person->requested_by,
                    ];
                }
            }
 
            
            $result = [
                'current_persons' => $formatted_persons,
            ];
            return $result;
        } catch (Exception $e) {
            return [
                'current_persons' => [],
                'statistics' => ['current_count' => 0, 'today_entries' => 0, 'today_exits' => 0, 'avg_duration_minutes' => 0],
                'error' => $e->getMessage()
            ];
        }
    }

	function updatepersonalgatewayexit($get,$post){
		global $callConfig;
		date_default_timezone_set('Asia/Kolkata');
		try {
			$entry_id = $post['entry_id'] ?? null;
			$method_type = $post['method_type'] ?? 'entry_first';
			$company_id = $post['company_id'] ?? $get['company_id'];
			$user_id = $post['user_id'] ?? $get['user_id'];
			$exit_notes = $post['exit_notes'] ?? '';
			$goods_checked = $post['goods_checked'] ?? 0;
			$security_check = $post['security_check'] ?? 0;
			$authorized_by = $post['authorized_by'] ?? '';
			$gateway_type = $post['gateway_type'] ?? 0;
			$action_status = $post['action_status'] ?? '';
			$process_val = 0;

			if($gateway_type == 1){
				$process_val = 2;
			}
			if ($action_status == 'PENDING') {
				$process_val = 2;
			}

			if (!$entry_id) {
				return ['status' => 'error', 'message' => 'Entry ID is required'];
			}

			// Get the entry details first
			$query = "SELECT * FROM transport_personal_gateway WHERE id = {$entry_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1";
			$entry = $callConfig->getRow($query);

			if (!$entry) {
				return ['status' => 'error', 'message' => 'Entry not found'];
			}

			// Determine which field to update based on method_type and current state
            $method_type = $post['method_type'] ?? $entry->method_type ?? 'entry_first';
            $entry_time_recorded = isset($entry->entry_time) && $entry->entry_time && $entry->entry_time != '00:00:00';
            $exit_time_recorded = isset($entry->exit_time) && $entry->exit_time && $entry->exit_time != '00:00:00';

            // Get date and time from form
            $exit_date = $post['exit_date'] ?? date('Y-m-d');
            $exit_time = $post['exit_time'] ?? date('H:i:s');
            
            // Format time to HH:MM:SS if only HH:MM provided
            if (strlen($exit_time) == 5) {
                $exit_time = $exit_time . ':00';
            }
            
            $exit_timestamp = $exit_date . ' ' . $exit_time;

            $updateData = array(
                "exit_notes" => $exit_notes,
                "goods_checked" => $goods_checked,
                "security_check" => $security_check,
                "authorized_by" => $authorized_by,
                "updated_by" => $user_id,
                "updated_date" => date("Y-m-d H:i:s"),
				"process_val" => $process_val,
				"gateway_type" => $gateway_type
            );

            if ($method_type == 'exit_first') {
                // For exit_first: first record is exit_time, second is entry_time
                if ($entry->process_val == 1) {
                    // Recording exit time
                    $updateData['exit_time'] = $exit_time;
                    $updateData['exit_date'] = $exit_date;
                    $updateData['exit_timestamp'] = $exit_timestamp;

                } else {
                    // Recording entry time (return)
                    $updateData['entry_time'] = $exit_time;
                    $updateData['entry_date'] = $exit_date;
                    $updateData['entry_timestamp'] = $exit_timestamp;

                }
            } else if ($method_type == 'entry_first') {
                // For entry_first: first record is entry_time, second is exit_time
                if ($entry->process_val == 1) {
                    // Recording entry time
                    $updateData['entry_time'] = $exit_time;
                    $updateData['entry_date'] = $exit_date;
                    $updateData['entry_timestamp'] = $exit_timestamp;
                    
                } else {
                    // Recording exit time (departure)
                    $updateData['exit_time'] = $exit_time;
                    $updateData['exit_date'] = $exit_date;
                    $updateData['exit_timestamp'] = $exit_timestamp;

                }
			}else{
				$updateData['exit_time'] = $exit_time;
                $updateData['exit_date'] = $exit_date;
                $updateData['exit_timestamp'] = $exit_timestamp;

			}

			$result = $callConfig->updateRecord('transport_personal_gateway', $updateData, 'id', $entry_id);
			//$this->toupdatetransportpersonalgatewayprocess_val(['id'=>$entry_id,'company_id'=>$company_id]);
			if ($result) {
				return ['status' => 'success', 'message' => 'Record updated successfully', 'entry_id' => $entry_id];
			} else {
				return ['status' => 'error', 'message' => 'Failed to update entry record'];
			}
		} catch (Exception $e) {
			error_log("updatepersonalgatewayexit: Exception - " . $e->getMessage());
			return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
		}
	}
	function toupdatetransportpersonalgatewayprocess_val($get){
	    global $callConfig;
	    try {
	        $id = $get['id'] ?? null;
	        $company_id = $get['company_id'];
	        if(!$id){
	            return ['status'=>'error','message'=>'ID is required'];
	        }
	        // Get record
	        $sql = "SELECT entry_date, entry_time, exit_date, exit_time, process_val
	                FROM transport_personal_gateway 
	                WHERE id='".$id."' 
	                AND company_id='".$company_id."' 
	                AND is_active=0 
	                LIMIT 1";
	        $row = $callConfig->getRow($sql);
	        if(!$row){
	            return ['status'=>'error','message'=>'Record not found'];
	        }
	        if($row->process_val != 2){		        
		        // Check valid values
		        $entry_date_valid = !empty($row->entry_date) && $row->entry_date != '0000-00-00';
		        $entry_time_valid = !empty($row->entry_time) && $row->entry_time != '00:00:00';

		        $exit_date_valid  = !empty($row->exit_date) && $row->exit_date != '0000-00-00';
		        $exit_time_valid  = !empty($row->exit_time) && $row->exit_time != '00:00:00';
		        if($entry_date_valid && $entry_time_valid && $exit_date_valid && $exit_time_valid){
		            $updateData = array(
		                "process_val" => 2,
		                "updated_date" => date("Y-m-d H:i:s")
		            );
		            $result = $callConfig->updateRecord('transport_personal_gateway',$updateData,'id',$id);
		            return [
		                'status'=>'success',
		                'message'=>'Process value updated',
		                'result'=>$result
		            ];
		        }
		        return [
		            'status'=>'success',
		            'message'=>'Entry/Exit data not completed yet'
		        ];
	        }

	    } catch(Exception $e){
	        error_log("toupdatetransportgatewayprocess_val: ".$e->getMessage());
	        return ['status'=>'error','message'=>$e->getMessage()];
	    }
	}
	function updateAttendanceKey($get){
		global $callConfig;

		$company_id = intval($get['company_id'] ?? 0);
		$user_id = intval($get['user_id'] ?? 0);
		$rid = intval($get['rid'] ?? 0);
		$emp_id = intval($get['employee_id'] ?? 0);
		$attendace_date = trim($get['attendace_date'] ?? '');
		$leave_type_id = intval($get['key_short_code'] ?? 0);

		if (!$company_id || !$emp_id || $leave_type_id === 0) {
			return array('status' => 'error', 'message' => 'Missing required data');
		}

		if (!$this->isValidDate($attendace_date, 'Y-m-d')) {
			return array('status' => 'error', 'message' => 'Invalid date format. Use YYYY-MM-DD');
		}

		// Validate leave_type_id: 1=Present, 2=Absent, 4=Earned Leave, 6=Personal Leave, 9=Short Leave
		if (!in_array($leave_type_id, [1, 2, 4, 6, 9])) {
			return array('status' => 'error', 'message' => 'Invalid attendance key');
		}

		// Get existing attendance record if rid not provided

		$existing = $callConfig->getRow(
			"SELECT id,clock_in, clock_out FROM employee_daily_attendances WHERE company_id = {$company_id} AND employee_id = {$emp_id} AND attendace_date = '{$attendace_date}' AND is_active = 0 LIMIT 1"
		);
		if (!empty($existing) && !empty($existing->id)) {
			$rid = intval($existing->id);
		}


		// Fetch employee shift info
		$emp_shift = $callConfig->getRow(
			"SELECT shift_id FROM emp_shifts WHERE emp_id = {$emp_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1"
		);
		$shift_id = $emp_shift->shift_id ?? 0;

		$shift_clock_in = null;
		$shift_clock_out = null;

		if ($shift_id > 0) {
			$shift = $callConfig->getRow(
				"SELECT clock_in, clock_out FROM shifts WHERE id = {$shift_id} AND company_id = {$company_id} AND is_active = 0 LIMIT 1"
			);
			if (!empty($shift)) {
				$shift_clock_in = $shift->clock_in;
				$shift_clock_out = $shift->clock_out;
			}
		}

		$now = date('Y-m-d H:i:s');
		$record_id = $rid;

		// Initialize update data
		$update_data = array(
			'updated_by' => $user_id,
			'updated_date' => $now
		);

		$detail_clock_in = null;
		$detail_clock_out = null;
		$detail_leave_type_id = null;

		// Process based on leave_type_id
		if ($leave_type_id == 1) {
			// Present: Set shift times, clear leave_type_id
			$update_data['clock_in'] = $existing->clock_in ?? $shift_clock_in;
			$update_data['clock_out'] = $existing->clock_out ?? $shift_clock_out;
			$update_data['leave_type_id'] = null;
			$update_data['attendance_key_short_code'] = 'P';
			$update_data['old_clock_times'] = null;

			$detail_clock_in = $shift_clock_in;
			$detail_clock_out = $shift_clock_out;
			$detail_leave_type_id = null;

		} elseif ($leave_type_id == 2) {
			// Absent: Clear clock times and leave_type_id
			$update_data['clock_in'] = null;
			$update_data['clock_out'] = null;
			$update_data['leave_type_id'] = null;
			$update_data['attendance_key_short_code'] = 'A';
			$update_data['old_clock_times'] = null;

			$detail_clock_in = null;
			$detail_clock_out = null;
			$detail_leave_type_id = null;

		} elseif ($leave_type_id == 4) {
			// Earned Leave: Set leave_type_id, clear clock times
			$update_data['clock_in'] = null;
			$update_data['clock_out'] = null;
			$update_data['leave_type_id'] = 4;
			$update_data['attendance_key_short_code'] = null;
			$update_data['old_clock_times'] = null;

			$detail_clock_in = null;
			$detail_clock_out = null;
			$detail_leave_type_id = 4;

		} elseif ($leave_type_id == 6) {
			// Personal Leave: Set leave_type_id, clear clock times
			$update_data['clock_in'] = null;
			$update_data['clock_out'] = null;
			$update_data['leave_type_id'] = 6;
			$update_data['attendance_key_short_code'] = null;
			$update_data['old_clock_times'] = null;

			$detail_clock_in = null;
			$detail_clock_out = null;
			$detail_leave_type_id = 6;

		} elseif ($leave_type_id == 9) {
			// Short Leave: Preserve old clock times, set shift times, set leave_type_id
			$old_clock_in = null;
			$old_clock_out = null;

			if ($rid > 0) {
				$existing_record = $callConfig->getRow(
					"SELECT clock_in, clock_out FROM employee_daily_attendances WHERE id = {$rid} LIMIT 1"
				);
				if (!empty($existing_record)) {
					$old_clock_in = $existing_record->clock_in;
					$old_clock_out = $existing_record->clock_out;
				}
			}

			$old_clock_times_str = '';
			if ($old_clock_in && $old_clock_out) {
				$old_clock_times_str = $old_clock_in . ' - ' . $old_clock_out;
			}

			$update_data['clock_in'] = $shift_clock_in;
			$update_data['clock_out'] = $shift_clock_out;
			$update_data['leave_type_id'] = 9;
			$update_data['attendance_key_short_code'] = 'SL';
			$update_data['old_clock_times'] = $old_clock_times_str;

			$detail_clock_in = $shift_clock_in;
			$detail_clock_out = $shift_clock_out;
			$detail_leave_type_id = 9;
		}

		// Update or Insert main attendance record
		if ($rid > 0) {
			$updated = $callConfig->updateRecord('employee_daily_attendances', $update_data, 'id', $rid);
			if (!$updated) {
				return array('status' => 'error', 'message' => 'Failed to update attendance record');
			}
		} else {
			$insert_data = array(
				'company_id' => $company_id,
				'employee_id' => $emp_id,
				'attendace_date' => $attendace_date,
				'is_active' => 0,
				'current_status' => 0,
				'created_by' => $user_id,
				'created_date' => $now
			);
			// Merge the update_data fields
			$insert_data = array_merge($insert_data, $update_data);
			unset($insert_data['updated_by']);
			unset($insert_data['updated_date']);

			$inserted = $callConfig->insertRecord('employee_daily_attendances', $insert_data);
			if (!$inserted || (is_array($inserted) && empty($inserted['LASTID']))) {
				return array('status' => 'error', 'message' => 'Failed to insert attendance record');
			}
			$record_id = is_array($inserted) && isset($inserted['LASTID']) ? $inserted['LASTID'] : $inserted;
		}

		// Insert new attendance details record
		$detail_data = array(
			'company_id' => $company_id,
			'employee_id' => $emp_id,
			'attendace_date' => $attendace_date,
			'clock_in' => $detail_clock_in,
			'clock_out' => $detail_clock_out,
			'leave_type_id' => $detail_leave_type_id,
			'swipes' => $leave_type_id,
			'is_active' => 0,
			'current_status' => 0,
			'created_by' => $user_id,
			'created_date' => $now
		);

		$detail_error = null;
		try {
			$detail_res = $callConfig->insertRecord('employee_daily_attendance_details', $detail_data);
			if (!$detail_res) {
				$detail_error = 'Failed to insert attendance details record';
			}
		} catch (Exception $e) {
			$detail_error = 'Error inserting attendance details: ' . $e->getMessage();
		}

		if ($detail_error) {
			return array(
				'status' => 'partial_success',
				'record_id' => $record_id,
				'message' => 'Attendance key updated, but detail record insertion had an issue: ' . $detail_error
			);
		}

		return array(
			'status' => 'success',
			'record_id' => $record_id,
			'message' => 'Attendance status updated successfully'
		);
	}
	function gettransportgatewayreport($get){
		global $callConfig;

		$company_id = $get['company_id'] ?? '';
		$from_date = trim($get['from_date'] ?? '');
		$from_time = trim($get['from_time'] ?? '');
		$to_date = trim($get['to_date'] ?? '');
		$to_time = trim($get['to_time'] ?? '');
		$vehicle_number = trim($get['vehicle_number'] ?? '');
		$employee_id = intval($get['employee_id'] ?? 0);

		$employeesQuery = "SELECT id, emp_code, display_name FROM employees WHERE company_id = '" . $company_id . "' AND is_active = 0 ORDER BY display_name ASC";
		$employees = $callConfig->getAllRows($employeesQuery);
		if (!is_array($employees)) {
			$employees = array();
		}

		$vehiclesQuery = "SELECT vehicle_reg_no, model_group, model FROM transport_vehicle_master WHERE company_id = '" . $company_id . "' AND is_active = 0 AND vehicle_reg_no != '' ORDER BY vehicle_reg_no ASC";
		$vehicles = $callConfig->getAllRows($vehiclesQuery);
		if (!is_array($vehicles)) {
			$vehicles = array();
		}

		if ($company_id == '' || $from_date == '' || $from_time == '' || $to_date == '' || $to_time == '') {
			return array('status' => 'error', 'message' => 'Required fields are missing', 'data' => array(), 'employees' => $employees, 'vehicles' => $vehicles);
		}

		$from_datetime = $from_date . ' ' . $from_time . ':00';
		$to_datetime = $to_date . ' ' . $to_time . ':59';

		if (strtotime($from_datetime) === false || strtotime($to_datetime) === false) {
			return array('status' => 'error', 'message' => 'Invalid date or time format', 'data' => array(), 'employees' => $employees, 'vehicles' => $vehicles);
		}

		if (strtotime($from_datetime) > strtotime($to_datetime)) {
			return array('status' => 'error', 'message' => 'From date time should be less than or equal to To date time', 'data' => array(), 'employees' => $employees, 'vehicles' => $vehicles);
		}

		$filter_where = '';
		if ($vehicle_number !== '') {
			$filter_where .= " AND tg.vehicle_number = '" . addslashes($vehicle_number) . "'";
		}

		if ($employee_id > 0) {
			$filter_where .= " AND (tg.driver_name = '" . $employee_id . "' OR ed.id = '" . $employee_id . "')";
		}

		$query = "SELECT
				tg.id,
				tg.vehicle_number,
				COALESCE(NULLIF(tg.vehicle_type, ''), tvm.vehicle_type, '') AS vehicle_type,
				tg.person_details,
				COALESCE(NULLIF(ed.display_name, ''), NULLIF(tg.driver_name, ''), '') AS employee_name,
				tg.driver_name,
				tg.visit_purpose,
				tg.driver_contact,
				tg.method_type,
				tg.gate_pass,
				tg.entry_date,
				tg.entry_time,
				tg.exit_date,
				tg.exit_time,
				tg.current_status,
				tg.location,
				tg.other_location,
				tg.created_date,
				tg.vehicle_entry_reading,
				tg.vehicle_exit_reading,
				tg.exit_notes,
				tg.notes,
				COALESCE(NULLIF(e.display_name, ''), '') AS created_by_name
			FROM transport_gateway tg
			LEFT JOIN employees e ON (e.user_id = tg.created_by OR e.id = tg.created_by) AND e.company_id = tg.company_id AND e.is_active = 0
			LEFT JOIN employees ed ON ed.id = tg.driver_name AND ed.company_id = tg.company_id AND ed.is_active = 0
			LEFT JOIN transport_vehicle_master tvm ON tvm.vehicle_reg_no = tg.vehicle_number AND tvm.company_id = tg.company_id AND tvm.is_active = 0
			WHERE tg.company_id = '" . $company_id . "'
			AND tg.is_active = 0
			" . $filter_where . "
			AND (
				(
					tg.entry_date IS NOT NULL
					AND tg.entry_time IS NOT NULL
					AND tg.entry_date != '0000-00-00'
					AND tg.entry_time != '00:00:00'
					AND CONCAT(tg.entry_date, ' ', tg.entry_time) BETWEEN '" . $from_datetime . "' AND '" . $to_datetime . "'
				)
				OR
				(
					tg.exit_date IS NOT NULL
					AND tg.exit_time IS NOT NULL
					AND tg.exit_date != '0000-00-00'
					AND tg.exit_time != '00:00:00'
					AND CONCAT(tg.exit_date, ' ', tg.exit_time) BETWEEN '" . $from_datetime . "' AND '" . $to_datetime . "'
				)
			)
			ORDER BY tg.created_date DESC";

		$rows = $callConfig->getAllRows($query);
		if (!is_array($rows)) {
			$rows = array();
		}

		foreach ($rows as $index => $row) {
			$person_details = $row->person_details ?? '';

			$additional_employees = '';
			if (!empty($person_details)) {
				$decoded_details = json_decode($person_details, true);
				if (is_array($decoded_details)) {
					$name_list = array();
					foreach ($decoded_details as $person) {
						if (is_array($person)) {
							$person_name = trim($person['name'] ?? '');
							if ($person_name !== '') {
								$name_list[] = $person_name;
							}
						}
					}
					if (!empty($name_list)) {
						$additional_employees = implode(', ', $name_list);
					}
				}
			}

			$rows[$index]->additional_employees = $additional_employees;

		}

		return array(
			'status' => 'success',
			'message' => 'Transport gateway report fetched successfully',
			'data' => $rows,
			'employees' => $employees,
			'vehicles' => $vehicles,
			'query' => $query
		);
	}
	function getEmployeeBuddyAuthorizers($get){
	    global $callConfig;

	    $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
	    $employee_id = isset($get['employee_id']) ? (int) $get['employee_id'] : 0;

	    $buddy_ids = '';
	    $employees = array();

	    if ($company_id > 0 && $employee_id > 0) {
	        $buddyQuery = "SELECT hr_buddy, finance_buddy, admin_buddy, project_buddy, loc_buddy FROM employees WHERE company_id = {$company_id} AND id = {$employee_id} AND is_active = 0 LIMIT 1";
	        $buddyRow = $callConfig->getRow($buddyQuery);

	        if (!empty($buddyRow)) {
	            $buddyIdArray = array();
	            $buddyFields = array('hr_buddy', 'finance_buddy', 'admin_buddy', 'project_buddy', 'loc_buddy');
	            foreach ($buddyFields as $buddyField) {
	                $buddyValue = isset($buddyRow->$buddyField) ? trim((string) $buddyRow->$buddyField) : '';
	                if ($buddyValue !== '' && is_numeric($buddyValue) && (int) $buddyValue > 0) {
	                    $buddyIdArray[] = (int) $buddyValue;
	                }
	            }

	            if (count($buddyIdArray) > 0) {
	                $buddyIdArray = array_values(array_unique($buddyIdArray));
	                $buddy_ids = implode(',', $buddyIdArray);
	                $employeeQuery = "SELECT id, emp_code, display_name FROM employees WHERE company_id = {$company_id} AND is_active = 0 AND id IN ({$buddy_ids}) ORDER BY display_name ASC";
	                $employees = $callConfig->getAllRows($employeeQuery);
	                if (!is_array($employees)) {
	                    $employees = array();
	                }
	            }
	        }
	    }

	    return array(
	        'status' => 'success',
	        'buddy_ids' => $buddy_ids,
	        'employees' => $employees
	    );
	}
}
?>
