<?php 
class claimUserClass extends userClass{
		function getReimbursementDashboard($get){
			global $callConfig;
			$returnarray = array();
			$employee_id = $get['emp_id'];
			$user_id = $get['user_id'];
			//$exploderole = explode(',', $get['roleshortcode']);
			
			//if(!in_array(CLAIM_EXECUTIVE_ROLE, $exploderole)){
				if($get['admin_type']!='1'){
					$condition = " AND user_id = ".$user_id;
					$tripcondition = " AND tri.user_id = ".$user_id;
					$empexpcondition = " AND empexp.user_id = ".$user_id;
					
					$employeecondition = " AND user_id = ".$user_id;
				}
			//}
			
			$holidayssql = "SELECT holiday_name, holiday_date FROM holidays WHERE company_id = '".$get['company_id']."' AND YEAR(holiday_date) = ".date('Y')." AND is_active = 0";
			$holidaysres = $callConfig->getAllRows($holidayssql);
			$returnarray['holidays'] = $holidaysres;

			$employeessql = "SELECT count(id) as emp_cnt FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0 ".$employeecondition;
			$employeesres = $callConfig->getRow($employeessql);
			$returnarray['employees_cnt'] = $employeesres;


$emp_expensescntsql = "SELECT count(*) as total_bill_cnt, sum(if(current_status=".ST_ACTIVE." OR current_status=".ST_DRAFT.",1,0)) as yettosubmit_cnt, sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND current_status!=".ST_APPROVED.",1,0)) as inprocess_cnt,sum(if(current_status=".ST_APPROVED.",1,0)) as approved_cnt ,sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT.",total_amount,0)) as total_amount, sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT.",pending_amount,0)) as pending_amount ,sum(if(current_status =".ST_APPROVED." AND pending_amount > 0,1,0)) as clearbill_cnt FROM emp_expenses WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
			

			//$emp_expensescntsql = "SELECT count(*) as total_cnt, sum(total_amount) as total_amount	 , sum(pending_amount) as pending_amount ,(sum(total_amount)- sum(pending_amount)) as paid_amount,sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND current_status!=".ST_APPROVED.",1,0)) as inprocess_cnt, sum(if(current_status =".ST_APPROVED.",1,0)) as total_approved_cnt FROM emp_expenses WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
			$emp_expensescntres = $callConfig->getRow($emp_expensescntsql);
			$emp_expensescntres->paid_amount = $emp_expensescntres->total_amount - $emp_expensescntres->pending_amount;
			$returnarray['emp_expenses_cnt_sql'] = $emp_expensescntsql;
			$returnarray['emp_expenses_cnt'] = $emp_expensescntres;

			$self_emp_expensessql = "SELECT empexp.id, empexp.expense_code, empexp.emp_id, empexp.expense_date, empexp.total_amount, empexp.pending_amount, empexp.current_status, empexp.user_id, tri.id as tri, tri.transaction_no FROM emp_expenses empexp LEFT JOIN  trips tri ON empexp.trip_id = tri.id AND tri.company_id = empexp.company_id AND tri.is_active = empexp.is_active WHERE empexp.company_id = '".$get['company_id']."'  ".$empexpcondition." AND empexp.is_active = 0";
			
			$self_emp_expensesres = $callConfig->getAllRows($self_emp_expensessql);
			$returnarray['self_emp_expenses'] = $self_emp_expensesres;

			$self_tripssql = "SELECT tri.id, tri.transaction_no, tri.transaction_date, tri.trip_name, tri.budget_amount, tri.current_status, empexp.id as exp_id, empexp.expense_code FROM trips tri LEFT JOIN emp_expenses empexp ON empexp.trip_id = tri.id AND tri.company_id = empexp.company_id AND tri.is_active = empexp.is_active WHERE tri.company_id = '".$get['company_id']."'  ".$tripcondition." AND tri.is_active = 0";
			$self_tripsres = $callConfig->getAllRows($self_tripssql);
			$returnarray['self_trips_sql'] = $self_tripssql;
			$returnarray['self_trips'] = $self_tripsres;


			//$teamsql = "SELECT emp.id, emp.display_name, (SELECT sum(total_amount) FROM emp_expenses emp_e WHERE emp_e.company_id = emp.company_id AND emp.id = emp_e.emp_id AND current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND is_active = 0) as total_expenses, (SELECT sum(pending_amount) FROM emp_expenses emp_e WHERE emp_e.company_id = emp.company_id AND emp.id = emp_e.emp_id AND current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND is_active = 0) as pending_amount FROM employees emp, application_users au WHERE emp.company_id = 1 AND emp.company_id = au.company_id AND emp.user_id = au.id AND (au.reports_to = '".$get['user_id']."' OR emp.hr_buddy = '".$employee_id."' OR emp.finance_buddy  = '".$employee_id."' OR emp.admin_buddy = '".$employee_id."' OR emp.project_buddy = '".$employee_id."' OR emp.loc_buddy = '".$employee_id."') AND emp.is_active = au.is_active AND emp.is_active = 0"; 
			$teamsql = "SELECT au.id, au.display_name, (SELECT sum(total_amount) FROM emp_expenses emp_e WHERE emp_e.company_id = au.company_id AND au.id = emp_e.user_id AND emp_e.current_status!=".ST_ACTIVE." AND emp_e.current_status!=".ST_DRAFT." AND emp_e.is_active = 0) as total_expenses, (SELECT sum(emp_e.pending_amount) FROM emp_expenses emp_e WHERE emp_e.company_id = au.company_id AND au.id = emp_e.emp_id AND emp_e.current_status!=".ST_ACTIVE." AND emp_e.current_status!=".ST_DRAFT." AND emp_e.is_active = 0) as pending_amount FROM application_users au WHERE au.company_id = '".$get['company_id']."' AND (au.reports_to = '".$get['user_id']."' OR au.hr_connect = '".$employee_id."' OR au.finance_connect  = '".$employee_id."' OR au.admin_connect = '".$employee_id."' OR au.project_connect = '".$employee_id."' OR au.location_connect = '".$employee_id."' OR au.esop_connect = '".$employee_id."' OR au.legal_connect = '".$employee_id."' OR au.approval_manager = '".$employee_id."') AND au.is_active = 0"; 
			$team_emp_expensesres = $callConfig->getAllRows($teamsql);
			$returnarray['team_emp_expenses'] = $team_emp_expensesres;

			$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, emp_e.expense_code, emp_e.expense_date, emp_e.total_amount, emp_e.emp_id, emp_e.id FROM transaction_approval_logs tal, emp_expenses emp_e WHERE tal.company_id = '".$get['company_id']."' AND tal.company_id = emp_e.company_id AND  tal.transaction_table = 'emp_expenses' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = emp_e.id AND emp_e.is_active = 0";
			$transapprlogres = $callConfig->getAllRows($transapprlogsql);
			$returnarray['transaction_approval_logs'] = $transapprlogres;

			$transapprlotripsgsql = "SELECT tal.request_date,tal.response_date, tal.response_type, emp_e.transaction_no, emp_e.transaction_date, emp_e.budget_amount, emp_e.user_id, emp_e.id FROM transaction_approval_logs tal, trips emp_e WHERE tal.company_id = '".$get['company_id']."' AND tal.company_id = emp_e.company_id AND  tal.transaction_table = 'trips' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = emp_e.id AND emp_e.is_active = 0";
			$transapprlotripsgres = $callConfig->getAllRows($transapprlotripsgsql);
			$returnarray['trips_transaction_approval_logs'] = $transapprlotripsgres;


			// $bill_cntsql = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp_id='".$get['emp_id']."'";
			// $bill_cntres = $callConfig->getAllRows($bill_cntsql);
			// $returnarray['bill_cnt'] = $bill_cntres;

			// $vendorssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status = '".ST_APPROVED."' AND emp_id='".$get['emp_id']."'";
			// $vendorsres = $callConfig->getAllRows($vendorssql);
			// $returnarray['bill_approved'] = $vendorsres;
			// foreach($vendorsres as $vendorsre){
			// 	$bill_approved_amonut += $vendorsre->amount;
			// 	$bill_approved_cnt++;
			// }
			// $returnarray['bill_approved_amonut'] = $bill_approved_amonut;
			// $returnarray['bill_approved_cnt'] = $bill_approved_cnt;


			// $pending_approved = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status NOT IN('".ST_ACTIVE."','".ST_APPROVED."','".ST_DRAFT."') AND emp_id='".$get['emp_id']."'";
			// $pending_approvedres = $callConfig->getAllRows($pending_approved);
			// $returnarray['pending_approved'] = $pending_approvedres;
			// $pending_approved_cnt = 0;
			// foreach($pending_approvedres as $pending_approvedre){
			// 	$pending_approved_amt += $pending_approvedre->amount;
			// 	$pending_approved_cnt++;
			// }
			// $returnarray['pending_approved_amt'] = $pending_approved_amt;
			// $returnarray['pending_approved_cnt'] = $pending_approved_cnt;

			// $unsubmited = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status IN('".ST_ACTIVE."','".ST_DRAFT."') AND emp_id='".$get['emp_id']."'";
			// $unsubmitedres = $callConfig->getAllRows($unsubmited);
			// $returnarray['unsubmited'] = $unsubmitedres;
			// $unsubmitmount = 0;
			// foreach($unsubmitedres as $unsubmitedres){
			// 	$unsubmitmount += $unsubmitedres->amount;
			// 	$unsubmited_cnt++;
			// }
			// $returnarray['unsubmited_amt'] = $unsubmitmount;
			// $returnarray['unsubmited_cnt'] = $unsubmited_cnt;
			// $billssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status, reference_date FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp_id='".$get['emp_id']."' ORDER BY id DESC limit ".$get['bill_limit'];
			// $billsres = $callConfig->getAllRows($billssql);
			// $returnarray['billsres'] = $billsres;
			// $get['table'] ='emp_expenses';
			// $status_progress = $this->getStatusProgressList($get);
			// $returnarray['status_progress'] = $status_progress;
			 //print_r($returnarray);
			return $returnarray;
		}
		function getReimbursementDashboardChart($get){
			global $callConfig;
			$exploderole = explode(',', $get['roleshortcode']);
			//if(!in_array(CLAIM_EXECUTIVE_ROLE, $exploderole)){
				
			//}
			if($get['paymentsdashboard'] == 'Y'){
				$employeessql = "SELECT emp.id, emp.user_id FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.finance_buddy = '".$get['emp_id']."' AND emp.is_active = 0 ";
				$employeesres = $callConfig->getAllRows($employeessql);
				$$returnarray['sql'] = $employeessql;
				$returnarray['employees_cnt'] = count((array)$employeesres);
				$empidlist = array();
				foreach($employeesres as $empdetails){
					$empidlist[$empdetails->id] = $empdetails->id;
				}
				$expempid = implode(',', (array)$empidlist);
				$condition = "AND emp_id IN(".$expempid.")";
			}else{
				$condition = " AND emp_id = ".$get['emp_id'];
			}
			$year = $get['reimbursementsyear'];
			$startdate = date($year.'-01-01');
			$lastdate = date($year.'-31-12');
			$montharay = array('1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12');
			$expensessql = "SELECT sum(total_amount) as total_amount, sum(pending_amount) as pending_amount, MONTH(expense_date) as month,YEAR(expense_date) as year FROM `emp_expenses` WHERE company_id = '".$get['company_id']."' ".$condition." AND expense_date >= '".$startdate."' AND expense_date <= '".$lastdate."' AND current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND is_active = 0 GROUP BY YEAR(expense_date), MONTH(expense_date)";
			$expensesres = $callConfig->getAllRows($expensessql);
			foreach($expensesres as $empexp){
				$expensetotal[$empexp->month]= $empexp->total_amount;
				$expensepending[$empexp->month]= $empexp->pending_amount;
			}
			foreach($montharay as $monthary=>$moth){
				$expensetotalamt = isset($expensetotal[$monthary])?$expensetotal[$monthary]:"0";
				$expensependingamt = isset($expensepending[$monthary])?$expensepending[$monthary]:"0";
				$totalpendin = $expensetotalamt-$expensependingamt;
				$expensemontharray[] = array("date"=>date($year.'-'.$moth.'-01'),"market1"=>round($expensetotalamt),"market2"=>round($totalpendin));
			}
			///print_r($expensemontharray);
			return $expensemontharray;
		}




		function getReimbursementPaymentDashboard($get){
			global $callConfig;
			$returnarray = array();
			$employee_id = $get['emp_id'];
			
			$employeecondition = " AND emp.finance_buddy = '".$employee_id."'";	

			$holidayssql = "SELECT holiday_name, holiday_date FROM holidays WHERE company_id = '".$get['company_id']."' AND YEAR(holiday_date) = ".date('Y')." AND is_active = 0";
			$holidaysres = $callConfig->getAllRows($holidayssql);
			$returnarray['holidays'] = $holidaysres;

			$employeessql = "SELECT emp.id, emp.user_id FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND emp.is_active = 0 ".$employeecondition;
			$employeesres = $callConfig->getAllRows($employeessql);
			$$returnarray['sql'] = $employeessql;
			$returnarray['employees_cnt'] = count((array)$employeesres);
			$empidlist = array();
			foreach($employeesres as $empdetails){
				$empidlist[$empdetails->id] = $empdetails->id;
			}
			$expempid = implode(',', (array)$empidlist); 

			//$emp_expensescntsql = "SELECT sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT.",1,0)) as total_cnt , sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT.",total_amount,0)) as total_amount,sum(if(current_status=".ST_APPROVED.",pending_amount,0)) as pending_amount ,sum(if(current_status!=".ST_ACTIVE." AND current_status!=".ST_DRAFT." AND current_status!=".ST_APPROVED.",1,0)) as inprocess_cnt, sum(if(current_status =".ST_APPROVED.",1,0)) as total_approved_cnt FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND emp_id IN(".$expempid.") AND is_active = 0";
			$emp_expensescntsql = "SELECT sum(if(current_status=".ST_APPROVED.",1,0)) as total_cnt , sum(if(current_status=".ST_APPROVED.",total_amount,0)) as total_amount,sum(if(current_status=".ST_APPROVED.",pending_amount,0)) as pending_amount ,sum(if(current_status=".ST_APPROVED." AND pending_amount <= 0,1,0)) as inprocess_cnt, 
			sum(if(current_status =".ST_APPROVED." AND pending_amount > 0,1,0)) as total_approved_cnt FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND emp_id IN(".$expempid.") AND is_active = 0";
			$emp_expensescntres = $callConfig->getRow($emp_expensescntsql);
			$emp_expensescntres->paid_amount = $emp_expensescntres->total_amount - $emp_expensescntres->pending_amount;
			$returnarray['emp_expenses_cnt'] = $emp_expensescntres;

			$self_emp_expensessql = "SELECT id, expense_code, emp_id, expense_date, total_amount, pending_amount, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND emp_id IN(".$expempid.") AND current_status NOT IN(".ST_DRAFT.",".ST_ACTIVE.") AND is_active = 0";
			$self_emp_expensesres = $callConfig->getAllRows($self_emp_expensessql);
			$returnarray['all_emp_expenses'] = $self_emp_expensesres;

			$selftranssql = "SELECT id, transaction_no, payment_date, payment_type, amount, current_status FROM `payments` WHERE company_id = '".$get['company_id']."' AND transaction_type = 'employees' AND originator = '".$get['user_id']."' AND is_active = 0"; 
			$selftransres = $callConfig->getAllRows($selftranssql);
			$returnarray['self_payments'] = $selftransres;


		$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, pay.transaction_no, pay.payment_date, pay.amount, pay.originator,pay.id FROM transaction_approval_logs tal, payments pay WHERE tal.company_id = '".$get['company_id']."' AND tal.company_id = pay.company_id AND  tal.transaction_table = 'payments' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = pay.id AND pay.transaction_type = 'employees' AND pay.is_active = 0";
			$transapprlogres = $callConfig->getAllRows($transapprlogsql);
			$returnarray['transaction_approval_logs'] = $transapprlogres;


			// $bill_cntsql = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp_id='".$get['emp_id']."'";
			// $bill_cntres = $callConfig->getAllRows($bill_cntsql);
			// $returnarray['bill_cnt'] = $bill_cntres;

			// $vendorssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status = '".ST_APPROVED."' AND emp_id='".$get['emp_id']."'";
			// $vendorsres = $callConfig->getAllRows($vendorssql);
			// $returnarray['bill_approved'] = $vendorsres;
			// foreach($vendorsres as $vendorsre){
			// 	$bill_approved_amonut += $vendorsre->amount;
			// 	$bill_approved_cnt++;
			// }
			// $returnarray['bill_approved_amonut'] = $bill_approved_amonut;
			// $returnarray['bill_approved_cnt'] = $bill_approved_cnt;


			// $pending_approved = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status NOT IN('".ST_ACTIVE."','".ST_APPROVED."','".ST_DRAFT."') AND emp_id='".$get['emp_id']."'";
			// $pending_approvedres = $callConfig->getAllRows($pending_approved);
			// $returnarray['pending_approved'] = $pending_approvedres;
			// $pending_approved_cnt = 0;
			// foreach($pending_approvedres as $pending_approvedre){
			// 	$pending_approved_amt += $pending_approvedre->amount;
			// 	$pending_approved_cnt++;
			// }
			// $returnarray['pending_approved_amt'] = $pending_approved_amt;
			// $returnarray['pending_approved_cnt'] = $pending_approved_cnt;

			// $unsubmited = "SELECT id, expense_code, expense_date, amount, reference_no, current_status FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND current_status IN('".ST_ACTIVE."','".ST_DRAFT."') AND emp_id='".$get['emp_id']."'";
			// $unsubmitedres = $callConfig->getAllRows($unsubmited);
			// $returnarray['unsubmited'] = $unsubmitedres;
			// $unsubmitmount = 0;
			// foreach($unsubmitedres as $unsubmitedres){
			// 	$unsubmitmount += $unsubmitedres->amount;
			// 	$unsubmited_cnt++;
			// }
			// $returnarray['unsubmited_amt'] = $unsubmitmount;
			// $returnarray['unsubmited_cnt'] = $unsubmited_cnt;
			// $billssql = "SELECT  id, expense_code, expense_date, amount, reference_no, current_status, reference_date FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp_id='".$get['emp_id']."' ORDER BY id DESC limit ".$get['bill_limit'];
			// $billsres = $callConfig->getAllRows($billssql);
			// $returnarray['billsres'] = $billsres;
			// $get['table'] ='emp_expenses';
			// $status_progress = $this->getStatusProgressList($get);
			// $returnarray['status_progress'] = $status_progress;
			 //print_r($returnarray);
			return $returnarray;
		}
	function getEmpExpenses($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		//if($get['result_type'] =="trip_details"){ // custom_insert_emp_expense_ajax.php

		//}else{
			if($get['id'] !=""){
				$sql = "SELECT * FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND id='".$get['id']."'";
			 	$res = $callConfig->getRow($sql);
			 	$return['emp_expenses'] = $res;
			 	$sql = "SELECT * FROM emp_expenses_details WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp_expenses_id='".$get['id']."'";
			 	$res1 = $callConfig->getAllRows($sql);
			 	$return['emp_expenses_details'] = $res1;

			 	$sql = "SELECT id, display_name, reports_to FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND id='".$res->user_id."'";
			 	$res1 = $callConfig->getRow($sql);
			 	$return['expenses_user_details'] = $res1;
			 	if($res->id !=""){
			 		$tripscondition = "AND id !=".$res->id;
			 	}
			}
			if($res->trip_id !="" OR $get['trip_id'] !=""){
				$get['trip_id'] = $get['trip_id'] !=""?$get['trip_id']:$res->trip_id;
				$citiesid =$cities= array(); 
				$tripssql = "SELECT * FROM trips WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND id='".$get['trip_id']."'";
			 	$tripsres = $callConfig->getRow($tripssql);
			 	$return['tripssql'] = $tripssql;
			 	$return['trips_select'] = $tripsres;

				$drtipsql = "SELECT id, journey_type, origin_location, destination_location, departure_date, departure_time, return_date, return_time, estimated_cost FROM domestic_round_trips WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND trip_id='".$get['trip_id']."'";
			 	$drtipres = $callConfig->getAllRows($drtipsql);
			 	foreach($drtipres as $re1){
			 		$citiesid[$re1->origin_location] = $re1->origin_location;
			 		$citiesid[$re1->destination_location] = $re1->destination_location;
			 	}
			 	$return['domestic_round_trips'] = $drtipres;

				$triphotelssql = "SELECT id, location, check_in, check_out,cost  FROM trip_hotels WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND trip_id='".$get['trip_id']."'";
			 	$triphotelsres = $callConfig->getAllRows($triphotelssql);
			 	foreach($triphotelsres as $re2){
			 		$citiesid[$re2->location] = $re2->location;
			 	}
			 	$return['trip_hotels'] = $triphotelsres;

			 	$localtripssql = "SELECT id, from_location, to_location, trip_date, travel_mode, estimated_cost FROM local_trips WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND trip_id='".$get['trip_id']."'";
			 	$localtripsres = $callConfig->getAllRows($localtripssql);
			 	foreach($localtripsres as $re3){
			 		$citiesid[$re3->from_location] = $re3->from_location;
			 		$citiesid[$re3->to_location] = $re3->to_location;
			 	}
			 	$return['local_trips'] = $localtripsres;

			 	if(count((array)$citiesid) != 0){
				 	$citiessql = "SELECT id, city_name, country_code FROM cities WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND id IN (".implode(',',$citiesid).")";
				 	$citiesres = $callConfig->getAllRows($citiessql);
				 	foreach($citiesres as $city){
				 		$cities[$city->id] = $city;
				 	}
				 }
			 	$return['cities'] = $cities;

	    	}

	    	$query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='VEHICLETYPES' AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        foreach($data as $vtype){
		 		$vehicle_types[$vtype->genc_detail_code] = $vtype->genc_detail_name;
		 	}
	        $return['vehicle_types'] = $vehicle_types;

			$tripssql = "SELECT id, transaction_no, transaction_date, trip_name, budget_amount FROM trips WHERE company_id = '".$get['company_id']."' AND user_id = '".$get['user_id']."' AND current_status =".ST_APPROVED." AND id NOT IN(SELECT trip_id FROM emp_expenses WHERE company_id = '".$get['company_id']."' AND is_active = 0 ".$tripscondition.") AND is_active = 0";
			$tripsres = $callConfig->getAllRows($tripssql);
			$return['trips'] = $tripsres;

			$sql = "SELECT id, category_name, parent_category_id FROM expense_categories WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		 	$res = $callConfig->getAllRows($sql);
		 	$return['all_expense_categories'] = $res;



			$sql = "SELECT rpc.category_id, rpc.max_expense_limit, rpc.receipt_required_limit, rpc.description_mandatory, (SELECT category_name FROM expense_categories ec WHERE ec.company_id =urp.company_id AND ec.is_active = urp.is_active AND ec.id= rpc.category_id ) category_name, (SELECT parent_category_id FROM expense_categories ec WHERE ec.company_id =urp.company_id AND ec.is_active = urp.is_active AND ec.id= rpc.category_id ) parent_category_id  FROM reimbursement_policy_categories rpc, user_reimbursement_policies urp WHERE urp.company_id = '".$get['company_id']."' AND urp.company_id = rpc.company_id AND urp.is_active = 0 AND urp.is_active = rpc.is_active AND urp.user_id = '".$get['user_id']."' AND rpc.policy_id = urp.policy_id";
		 	$res = $callConfig->getAllRows($sql);
		 	$return['expense_categories_sql'] = $sql;
		 	$return['expense_categories'] = $res;
		 	
		 	$sql = "SELECT emp.id, emp.expense_type, emp.name, emp.amount,emp.start_date, emp.end_date FROM expenses_mileages_perdiem emp, user_reimbursement_policies urp WHERE urp.company_id = '".$get['company_id']."' AND urp.is_active = emp.is_active AND urp.company_id = emp.company_id AND urp.is_active = 0 AND emp.policy_id= urp.policy_id AND urp.user_id = '".$get['user_id']."'";
		 	$res = $callConfig->getAllRows($sql);
		 	$return['expenses_mileages_perdiem'] = $res;
		 	
		 	$sql = "SELECT id,genc_detail_code,genc_detail_name,genc_code FROM general_code_details WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND genc_code IN('PAYMETHOD','EXPENSTYPE')";
		 	$res = $callConfig->getAllRows($sql);
		 	$return['general_code_details'] = $res;
		 	
		 	$sql = "SELECT id,name FROM projects WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		 	$res = $callConfig->getAllRows($sql);
		 	$return['projects'] = $res;

		 	$countriessql ="SELECT id, country_name FROM countries WHERE company_id =  {$get['company_id']} AND is_active =0";
	        $countriesres = $callConfig->getAllRows($countriessql);
	        foreach($countriesres as $vtype){
		 		$countries[$vtype->id] = $vtype->country_name;
		 	}
	        $return['countries'] = $countries;

		 //	}

		return $return;
	}

	function togetemployeetripsbyuser($get){
        global $callConfig;
        $query = "SELECT  `id`, `user_id`, `transaction_no`, `transaction_date`, `trip_name`, `trip_type`, `trip_purpose`, `project_id`, `customer_id`, `budget_amount`, `final_amount`, `is_active`, `current_status`, ( SELECT exp.id FROM emp_expenses exp WHERE exp.trip_id=tp.id AND exp.is_active=0 LIMIT 1) as exp_id FROM trips tp WHERE company_id = ".$get['company_id']." AND user_id =".$get['user_id']." AND is_active =0 ORDER BY id DESC";
        //echo $query;
        $ress = $callConfig->getAllRows($query);
        $return['trips'] = $ress;
 
        $get['table'] ="trips";
        $status_progress = $this->getStatusProgressList($get);
        $return['status_progress'] = $status_progress;
 
        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, trp.transaction_no, trp.transaction_date, trp.budget_amount, trp.trip_name, trp.id
        FROM transaction_approval_logs tal, trips trp WHERE tal.company_id = ".$get['company_id']." AND tal.company_id = trp.company_id AND  tal.transaction_table = 'trips' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = trp.id AND trp.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $return['transaction_approval_logs'] = $transapprlogres;
 
        return $return;
    }	

    function togetinprocesstripdetails($get){
        global $callConfig;
        $return = array();
        $cities_arr = array();
        if(isset($get['trip_id']) && $get['trip_id'] != ''){
            $query = "SELECT id,  `trip_name`, transaction_no,  transaction_date, `trip_type`, `trip_purpose`, `project_id`, `customer_id`, `budget_amount`, `final_amount`, user_id, `is_active`, `current_status` FROM trips  WHERE  id={$get['trip_id']} AND  company_id={$get['company_id']} AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['trip'] = $data;
 
            $query ="SELECT id, `trip_id`, `journey_type`, `user_id`, `trip_purpose`, `origin_location`, `destination_location`, `departure_date`, departure_time,`return_date`, return_time,`travel_mode`, `estimated_cost` FROM domestic_round_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['trip_id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            foreach ($data as $row) {
                    if (!empty($row->origin_location))      $cities_arr[] = (int)$row->origin_location;
                    if (!empty($row->destination_location)) $cities_arr[] = (int)$row->destination_location;
                }
            $return['domestic_round_trips'] = $data;
 
            $query ="SELECT id,`trip_id`, `hotel_name`, `check_in`, `check_out`, `location`, `room_type`, `booking_reference`, `cost` , description FROM trip_hotels WHERE company_id={$get['company_id']} AND  trip_id={$get['trip_id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            foreach ($data as $row) {
                if (!empty($row->location)) $cities_arr[] = (int)$row->location;
            }
            $return['trip_hotels'] = $data;
 
            $query ="SELECT id, `trip_id`, `user_id`, `trip_purpose`, `trip_date`, `from_location`, `to_location`, `travel_mode`, `estimated_cost`, `pickup_at`, `dropoff_at`, `car_type`, `driver_needed` FROM local_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['trip_id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            foreach ($data as $row) {
                if (!empty($row->from_location)) $cities_arr[] = (int)$row->from_location;
                if (!empty($row->to_location))   $cities_arr[] = (int)$row->to_location;
            }
            $return['local_trips'] = $data;
            // ---- Deduplicate and fetch cities ----
            $cities_arr = array_unique(array_filter($cities_arr));
            if (!empty($cities_arr)) {
                $ids = implode(',', $cities_arr);
                $query = "SELECT id, city_name FROM cities WHERE id IN ($ids)";
                $data = $callConfig->getAllRows($query);
                $res = array();
                foreach($data as $row){
                    $res[$row->id] = $row->city_name;
                }
                $return['cities'] = $res;
            } else {
                $return['cities'] = [];
            }
 
        }
 
        $query ="SELECT id, project_name FROM projects WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->id] = $row->project_name;
        }
        $return['projects'] = $res;
 
        $query ="SELECT id, display_name FROM customers WHERE company_id =  {$get['company_id']} AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->id] = $row->display_name;
        }
        $return['clients'] = $res;
 
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_LOCAL_VEHICLE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['local_modes'] = $res;
 
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_VEHICL' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['domestic_modes'] = $res;
 
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_TYPE' AND is_active =0 ORDER BY genc_detail_code ASC ";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['journey_types'] = $res;
 
        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_TYPE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $res = array();
        foreach($data as $row){
            $res[$row->genc_detail_code] = $row->genc_detail_name;
        }
        $return['trip_types'] = $res;
        return $return;
    }  
    function insertEMPExpenses($get,$post){
    	global $callConfig;
    	$return = array();
    	$fieldnames=array('company_id'=>$post['company_id'],'user_id'=>$post['user_id'],'emp_id'=>$post['emp_id'],'trip_id'=>$post['trip_id'],'expense_code'=>$post['expense_code'],'amount'=>$post['parent_amount'],'tax_amount'=>$post['parent_tax_amount'],'total_amount'=>$post['parent_total_amount'],'pending_amount'=>$post['parent_total_amount'],'cost_center_id'=>$post['cost_center_id']);
	 	if($post['Rid'] != '0' AND $post['Rid'] != ''){
	 		$fieldnames['updated_date'] = date('Y-m-d h:i:s');
	 		$fieldnames['updated_by'] = $post['user_id'];
	 		$insert_data=$callConfig->updateRecord('emp_expenses',$fieldnames,'id',$post['Rid']);
	 		$return['res'] = $insert_data;
	 		$insert_id = $post['Rid'];
	 	}else{
	 		$transation = $this->toGetApplicationSerialNumber('emp_expenses',$post);
	 		$fieldnames['expense_code'] = $transation;
	 		$expense_date = $this->dateconvertdbformat($post['trans_expense_date']);
	 		$fieldnames['expense_date'] = $expense_date;
	 		$fieldnames['created_by'] = $post['user_id'];
	 		$fieldnames['created_date'] = date('Y-m-d h:i:s');
	 		$fieldnames['is_active'] = '0';
	 		$fieldnames['current_status'] = ST_ACTIVE;
	 		$insert_data=$callConfig->insertRecord('emp_expenses',$fieldnames);
	 		$return['res'] = $insert_data;
	 		$insert_id = $insert_data['LASTID'];
	 	}
	 
	 	if($insert_id!=""){

	 		$upary['updated_by'] = $data['user_id'];
			$upary['updated_date'] = date('Y-m-d H:i:s');
			$upary['is_active'] =null;
			$callConfig->updateRecord("emp_expenses_details",$upary,"emp_expenses_id",$insert_id);

		 	foreach($post['row_sequince'] as $key => $rowsequince){
		 		//expense_type
		 		$bill_date =  $this->dateconvertdbformat($post['expense_date'][$key]);
		 		$expense_type = $post['expense_type_'.$rowsequince];
			 	if($expense_type=='1'){
			 		$category = $post['category_mileage'][$key]; 
			 		$qty  = $post['qty_mileage'][$key];
			 		$rate  = $post['rate_mileage'][$key];
			 	}elseif($expense_type=='2'){
			 		$category = $post['category_perd'][$key]; 
			 		$qty  = $post['qty_perd'][$key];
			 		$rate  = $post['rate_perd'][$key];
			 	}else{	
			 		$category = $post['category_exp'][$key]; 
			 		$qty  = $post['qty_exp'][$key];
			 		$rate  = $post['rate_exp'][$key];
			 	}
			 	$trip_transaction_table = $trip_transaction_id = NULL;
			 	if($post['trip_transaction_table'][$key] !=""){
			 		$trip_transaction_tabled = explode('||', $post['trip_transaction_table'][$key]);
			 		$trip_transaction_table= $trip_transaction_tabled[0];
			 		$trip_transaction_id= $trip_transaction_tabled[1];
			 	}
			 	
			 	$itemfieldnames=array('company_id'=>$post['company_id'],'emp_id'=>$post['emp_id'],'emp_expenses_id'=>$insert_id,'category_id'=>$category,'expense_type'=>$expense_type,'bill_no'=>$post['bill_no'][$key],'description'=>$post['description'][$key],'bill_date'=>$bill_date,'qty'=>$qty,'rate'=>$rate,'tax_amount'=>$post['tax'][$key],'expense_amount'=>$post['amount'][$key],'merchant_name'=>$post['merchant_name'][$key],'project_id'=>$post['project_id'],'payment_mode'=>$post['payment_mode'][$key],'location'=>$post['location'][$key],'bill_file'=>$post['bill_file'][$key],'is_active'=>'0','trip_transaction_table'=>$trip_transaction_table,'trip_transaction_id'=>$trip_transaction_id);	
			 	$returnarray['subarray'] = $itemfieldnames;
			 	if($post['emp_expenses_detail_id'][$key] != ''){
			 		$itemfieldnames['updated_date'] = date('Y-m-d h:i:s');
			 		$itemfieldnames['updated_by'] = $post['user_id'];
			 		$item_id_res=$callConfig->updateRecord('emp_expenses_details',$itemfieldnames,'id',$post['emp_expenses_detail_id'][$key]);
			 		$return['res_detail'] = $item_id_res;
			 	}else{
			 		$itemfieldnames['current_status'] = 0;
			 		$itemfieldnames['created_by'] = $post['user_id'];
			 		$itemfieldnames['created_date'] = date('Y-m-d h:i:s');
			 		$item_id_res=$callConfig->insertRecord('emp_expenses_details',$itemfieldnames);
			 		$return['res_detail'] = $item_id_res;
			 	}

			}
		}		
	 	return $return;
    }
    function getReimbursementPolicies($get){
    	global $callConfig;
    	$return = array();


    	if($get['rid'] !=""){
	        $policiessql = "SELECT * FROM reimbursement_policies WHERE id = '".$get['rid']."' AND company_id = '".$get['company_id']."' AND is_active = 0";
		 	$policiesres = $callConfig->getRow($policiessql);
		 	$return['policies'] = $policiesres;
		 	
		 	$policy_catsql = "SELECT id,policy_id,category_id,max_expense_limit,receipt_required_limit,description_mandatory,start_date,policy_time_period FROM reimbursement_policy_categories WHERE policy_id = '".$get['rid']."' AND company_id = '".$get['company_id']."' AND is_active = 0";
		 	$policy_catres = $callConfig->getAllRows($policy_catsql);
		 	$return['policy_categories'] = $policy_catres;


		 	$policy_catsql = "SELECT * FROM expenses_mileages_perdiem WHERE company_id = '".$get['company_id']."' AND expense_type = 0 AND policy_id = '".$get['rid']."' AND is_active = 0";
		 	$policy_catres = $callConfig->getAllRows($policy_catsql);
		 	$return['expenses_mileages'] = $policy_catres;


		 	$policy_catsql = "SELECT * FROM expenses_mileages_perdiem WHERE company_id = '".$get['company_id']."' AND expense_type = 1 AND policy_id = '".$get['rid']."' AND is_active = 0";
		 	$policy_catres = $callConfig->getAllRows($policy_catsql);
		 	$return['expenses_perdiem'] = $policy_catres;


	 	}

		$expense_categoriesquery ="SELECT id, parent_category_id, category_name, max_expense_limit, receipt_required_limit FROM expense_categories WHERE company_id =  {$get['company_id']} AND is_active =0";
        $expense_categoriesres = $callConfig->getAllRows($expense_categoriesquery);
        $return['expense_categories'] = $expense_categoriesres;

        $vehicleTypesquery ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND genc_code = 'VEHICLETYPES' AND is_active =0";
        $vehicleTypesres = $callConfig->getAllRows($vehicleTypesquery);
        $return['vehicleTypes'] = $vehicleTypesres;

        $countriessql ="SELECT id, country_name FROM countries WHERE company_id =  {$get['company_id']} AND is_active =0";
        $countriesres = $callConfig->getAllRows($countriessql);
        $return['countries'] = $countriesres;

    	return $return;
    }
    function getUserReimbursementPolicies($get,$data){
		global $callConfig;
		$data = $data['where'];
		$condition = "";
		if($data['location_id']!=""){ $condition .=  " AND location_id = '".$data['location_id']."'"; }
		if($data['cost_center_id']!=""){ $condition .=  " AND cost_center = '".$data['cost_center_id']."'"; }
		if($data['emp_name_code']!=""){ $condition .=  " AND  (display_name like '%".$data['emp_name_code']."%' OR  user_email like '%".$data['emp_name_code']."%') "; }
		
		$query2 = "SELECT id, display_name, user_email, user_mobile, user_photo, cost_center, location_id FROM application_users WHERE is_active=0 AND company_id = '".$data['company_id']."'".$condition;
		$datas = $callConfig->getAllRows($query2);
		//$return['users_list_sql'] = $query2;
		$return['users_list'] = $datas;
		
		$location_name = "SELECT id, location_name FROM locations WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$location_namedetails = $callConfig->getAllRows($location_name);
		$return['locations'] = $location_namedetails;

		$cost_centers = "SELECT id, cost_center_name FROM cost_centers WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$cost_centersdetails = $callConfig->getAllRows($cost_centers);
		$return['cost_centers'] = $cost_centersdetails;

		if($get['policy_id'] !=""){
			$user_reimbursement_policiessql = "SELECT id, user_id FROM user_reimbursement_policies WHERE is_active=0 AND company_id = '".$get['company_id']."' AND policy_id = '".$get['policy_id']."'";
			$user_reimbursement_policiesres = $callConfig->getAllRows($user_reimbursement_policiessql);
			$return['user_reimbursement_policies'] = $user_reimbursement_policiesres;
		}

		return $return;
	}
	function getTrevllingPlanData($get){
        global $callConfig;
        $return = array();
        $cities_arr = array();
         if($get['journyid'] != '' AND ($get['trip_type'] =="journy" OR $get['trip_type'] =="hotel")){

        	$sql = "SELECT rpc.category_id, rpc.max_expense_limit, rpc.receipt_required_limit, rpc.description_mandatory  FROM reimbursement_policy_categories rpc, user_reimbursement_policies urp WHERE urp.company_id = '".$get['company_id']."' AND urp.company_id = rpc.company_id AND urp.is_active = 0 AND urp.is_active = rpc.is_active AND urp.user_id = '".$get['user_id']."' AND urp.start_date <= '".$this->dateconvertdbformat($get['transaction_date'])."' AND urp.end_date >= '".$this->dateconvertdbformat($get['transaction_date'])."' AND rpc.category_id = '".$get['journyid']."' AND rpc.policy_id = urp.policy_id"; 
		 	$res = $callConfig->getRow($sql);
		 	//$return['expense_categories_sql'] = $sql;
		 	$return['res'] = $res;

		}elseif($get['journyid'] != '' AND $get['trip_type'] =="localtrip"){ 	
		 	$sql = "SELECT emp.id, emp.expense_type, emp.name, emp.amount as max_expense_limit, emp.start_date, emp.end_date FROM expenses_mileages_perdiem emp, user_reimbursement_policies urp WHERE urp.company_id = '".$get['company_id']."' AND urp.is_active = emp.is_active AND urp.company_id = emp.company_id AND urp.is_active = 0 AND emp.policy_id= urp.policy_id AND urp.user_id = '".$get['user_id']."' AND emp.name = '".$get['journyid']."'";
		 	$res = $callConfig->getRow($sql);
		 	//$return['sql'] = $sql;
		 	$return['res'] = $res;
		}else{ 
	        if(isset($get['id']) && $get['id'] != ''){
	            $query = "SELECT * FROM trips  WHERE  id={$get['id']} AND  company_id={$get['company_id']} AND is_active=0"; 
	            $data = $callConfig->getRow($query);
	            $return['trip'] = $data;

	            $query ="SELECT * FROM domestic_round_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
	            $data = $callConfig->getAllRows($query);
	            foreach ($data as $row) {
	                    if (!empty($row->origin_location))      $cities_arr[] = (int)$row->origin_location;
	                    if (!empty($row->destination_location)) $cities_arr[] = (int)$row->destination_location;
	                }
	            $return['domestic_round_trips'] = $data;

	            $query ="SELECT * FROM trip_hotels WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
	            $data = $callConfig->getAllRows($query);
	            foreach ($data as $row) {
	                if (!empty($row->location)) $cities_arr[] = (int)$row->location;
	            }
	            $return['trip_hotels'] = $data;

	            $query ="SELECT * FROM local_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
	            $data = $callConfig->getAllRows($query);
	            foreach ($data as $row) {
	                if (!empty($row->from_location)) $cities_arr[] = (int)$row->from_location;
	                if (!empty($row->to_location))   $cities_arr[] = (int)$row->to_location;
	            }
	            $return['local_trips'] = $data;
	            // ---- Deduplicate and fetch cities ----
	            $cities_arr = array_unique(array_filter($cities_arr));
	            if (!empty($cities_arr)) {
	                $ids = implode(',', $cities_arr);
	                $query = "SELECT id, city_name FROM cities WHERE id IN ($ids)";
	                $return['cities'] = $callConfig->getAllRows($query);
	            } else {
	                $return['cities'] = [];
	            }
	        }


	        $query ="SELECT id, project_name FROM projects WHERE company_id =  {$get['company_id']} AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        $return['projects'] = $data;

	        $query ="SELECT id, display_name FROM customers WHERE company_id =  {$get['company_id']} AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        $return['clients'] = $data;

	        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='VEHICLETYPES' AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        $return['local_modes'] = $data;

	        // $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_VEHICL' AND is_active =0";
	        // $data = $callConfig->getAllRows($query);
	        // $return['domestic_modes'] = $data;

	        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_TYPE' AND is_active =0 ORDER BY genc_detail_code ASC ";
	        $data = $callConfig->getAllRows($query);
	        $return['journey_types'] = $data;

	        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_TYPE' AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        $return['trip_types'] = $data;

	        $query ="SELECT id, category_name FROM expense_categories WHERE company_id =  {$get['company_id']} AND  parent_category_id='1' AND is_active =0";
	        $data = $callConfig->getAllRows($query);
	        $return['domestic_modes'] = $data;

        }

        return $return;

    }
    function insertAndUpdateTrevllingPlans($get, $post){
        global $callConfig;
        $return = array();
        $now        = date('Y-m-d H:i:s');
        $company_id = (int)($get['company_id'] ?? 0);
        $user_id    = (int)($get['user_id'] ?? 0);

        $rowEmpty = function ($row, $keys) {
            foreach ($keys as $k) if (!empty($row[$k])) return false;
            return true;
        };

        function non_empty_rows(array $rows, array $required, callable $rowEmpty): array
        {
            return array_values(array_filter($rows, fn($r) => !$rowEmpty($r, $required)));
        }
        $trip_id = $post['trip_id'] ?? '';
        // 2) if trip_id present, update trip and mark existing children as inactive
        if ($trip_id && $trip_id != '') {
            $trip_id = (int)$trip_id;
            $trip = [
				'trip_name'      => trim($post['trip_name'] ?? ''),
			    'trip_type'      => trim($post['trip_type'] ?? ''),
			    'trip_purpose'   => trim($post['trip_purpose'] ?? ''),
			    'project_id'     => $post['project_id'] ?? null,
			    'customer_id'    => $post['customer_id'] ?? null,
			    'budget_amount'  => $post['budget_amount'] ?? null,
			    'final_amount'   => $post['final_amount'] ?? null,
                'updated_by'     => $user_id,
                'updated_date'   => $now,
            ];
            $res = $callConfig->updateRecord('trips', $trip, 'id', $trip_id);
            $return['trip_update'] = $res;

            foreach (['domestic_round_trips', 'trip_hotels', 'local_trips'] as $tbl) {
                $upd = ['is_active' => 1, 'updated_by' => $user_id, 'updated_date' => $now];
                $res = $callConfig->updateRecord($tbl, $upd, 'trip_id', $trip_id);
            }

            // 3) reinsert current POST children
            $journey_type = $post['journey_type'];
            $itins  = (array)($post['itinerary'] ?? []);
            $hotels = (array)($post['hotel'] ?? []);
            $cars   = (array)($post['car'] ?? []);

            foreach (non_empty_rows($itins, ['travel_mode', 'origin_location', 'destination_location', 'departure_date', 'departure_time', 'trip_purpose', 'return_date', 'return_time'], $rowEmpty) as $row) {
                $rec = [
                    'company_id'           => $company_id,
                    'trip_id'              => $trip_id,
                    'journey_type'         => $journey_type,
                    'user_id'              => $user_id,
				    'trip_purpose'         => $row['trip_purpose'] ?? null,
				    'origin_location'      => $row['origin_location'] ?? null,
				    'destination_location' => $row['destination_location'] ?? null,
				    'departure_date'       => $row['departure_date'] ? $this->dateconvertdbformat($row['departure_date']) : null,
				    'departure_time'       => $row['departure_time'] ?? null,
				    'return_date'          => $row['return_date'] ? $this->dateconvertdbformat($row['return_date']) : null,
				    'return_time'          => $row['return_time'] ?? null,
				    'travel_mode'          => $row['travel_mode'] ?? null,
                    'estimated_cost'       => $row['estimated_cost'] ?? 0,
                    'is_active'            => 0,
                    'current_status'       => 0,
                    'status_date'          => $now,
                    'created_by'           => $user_id,
                    'created_date'         => $now,
                ];
                $res = $callConfig->insertRecord('domestic_round_trips', $rec);
                $return['domestic_round_trips'][] = $res;
            }

                // --- Trip Hotels ---
            foreach (non_empty_rows($hotels, ['hotel_name', 'location', 'check_in', 'check_out', 'description'], $rowEmpty) as $h) {
                $rec = [
                    'company_id'     => $company_id,
                    'trip_id'        => $trip_id,
				    'hotel_name'          => $h['hotel_name'] ?? null,
				    'check_in'            => $h['check_in'] ? $this->dateconvertdbformat($h['check_in']) : null,
				    'check_out'           => $h['check_out'] ? $this->dateconvertdbformat($h['check_out']) : null,
				    'location'            => $h['location'] ?? null,
				    'room_type'           => null,
				    'booking_reference'   => null,
				    'description'         => $h['description'] ?? null,
				    'cost'      => 		$post['hotel_estimated_cost'] ?? 0,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'status_date'    => $now,
                    'created_by'     => $user_id,
                    'created_date'   => $now,
                ];
                $res = $callConfig->insertRecord('trip_hotels', $rec);
                $return['trip_hotels'][] = $res;
            }
            foreach (non_empty_rows($cars, ['travel_mode', 'from_location', 'to_location', 'pickup_at', 'dropoff_at', 'car_type', 'trip_purpose'], $rowEmpty) as $c) {
                $rec = [
                    'company_id'     => $company_id,
                    'trip_id'        => $trip_id,
                    'user_id'        => $user_id,
				    'trip_purpose'   => $c['trip_purpose'] ?? null,
				    'trip_date'      => $c['pickup_at'] ? $this->dateconvertdbformat($c['pickup_at']) : null,
				    'from_location'  => $c['from_location'] ?? null,
				    'to_location'    => $c['to_location'] ?? null,
				    'travel_mode'    => $c['travel_mode'] ?? null,
				    'pickup_at'      => $c['pickup_at'] ? date('Y-m-d H:i:s', strtotime($c['pickup_at'])) : null,
				    'dropoff_at'     => $c['dropoff_at'] ? date('Y-m-d H:i:s', strtotime($c['dropoff_at'])) : null,
				    'car_type'       => $c['car_type'] ?? null,
				    'driver_needed'  => $c['driver_needed'] ?? null,
				    'estimated_cost' => $c['estimated_cost'] ?? 0,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'status_date'    => $now,
                    'created_by'     => $user_id,
                    'created_date'   => $now,
                ];
                $res = $callConfig->insertRecord('local_trips', $rec);
                $return['local_trips'][] = $res;
            }
        }else{
        	$serial = $this->toGetApplicationSerialNumber('trips',$get);

            $trip = [
                'company_id'     => $company_id,
                'user_id'    	 => $user_id,                
                'transaction_no' => $serial,                
                'transaction_date'=> date('Y-m-d'),                
				'trip_name'      => trim($post['trip_name'] ?? ''),
			    'trip_type'      => trim($post['trip_type'] ?? ''),
			    'trip_purpose'   => trim($post['trip_purpose'] ?? ''),
			    'project_id'     => $post['project_id'] ?? null,
			    'customer_id'    => $post['customer_id'] ?? null,
			    'budget_amount'  => $post['budget_amount'] ?? null,
			    'final_amount'   => $post['final_amount'] ?? null,
                'is_active'      => 0,
                'current_status' => 0,
                'status_date'    => $now,
                'created_by'     => $user_id,
                'created_date'   => $now,
            ];
            $res = $callConfig->insertRecord('trips', $trip);
            $return['trip_insert'] = $res;
            $trip_id = $res['LASTID'] ?? '';

            $journey_type = $post['journey_type'];
            $itins  = (array)($post['itinerary'] ?? []);
            $hotels = (array)($post['hotel'] ?? []);
            $cars   = (array)($post['car'] ?? []);

            // --- Domestic Round Trips ---
            if($trip_id != ''){
                foreach (non_empty_rows($itins, ['travel_mode', 'origin_location', 'destination_location', 'departure_date', 'departure_time','trip_purpose', 'return_date', 'return_time'], $rowEmpty) as $row) {
                    $rec = [
                        'company_id'           => $company_id,
                        'trip_id'              => $trip_id,
                        'journey_type'         => $journey_type,
                        'user_id'              => $user_id,
					    'trip_purpose'         => $row['trip_purpose'] ?? null,
					    'origin_location'      => $row['origin_location'] ?? null,
					    'destination_location' => $row['destination_location'] ?? null,
					    'departure_date'       => $row['departure_date'] ? $this->dateconvertdbformat($row['departure_date']): null,
					    'departure_time'       => $row['departure_time'] ?? null,
					    'return_date'          => $row['return_date'] ? $this->dateconvertdbformat($row['return_date']) : null,
					    'return_time'          => $row['return_time'] ?? null,
					    'travel_mode'          => $row['travel_mode'] ?? null,
                        'estimated_cost'       => $row['estimated_cost'] ?? 0,
                        'is_active'            => 0,
                        'current_status'       => 0,
                        'status_date'          => $now,
                        'created_by'           => $user_id,
                        'created_date'         => $now,
                    ];
                    $res = $callConfig->insertRecord('domestic_round_trips', $rec);
                    $return['domestic_round_trips'][] = $res;
                }

                // --- Trip Hotels ---
                foreach (non_empty_rows($hotels, ['hotel_name', 'location', 'check_in', 'check_out', 'description'], $rowEmpty) as $h) {
                    $rec = [
                        'company_id'     => $company_id,
                        'trip_id'        => $trip_id,
					    'hotel_name'          => $h['hotel_name'] ?? null,
					    'check_in'            => $h['check_in'] ? $this->dateconvertdbformat($h['check_in']) : null,
					    'check_out'           => $h['check_out'] ? $this->dateconvertdbformat($h['check_out']) : null,
					    'location'            => $h['location'] ?? null,
					    'room_type'           => null,
					    'booking_reference'   => null,
					    'description'         => $h['description'] ?? null,
					    'cost'      => $post['hotel_estimated_cost'] ?? 0,
                        'is_active'      => 0,
                        'current_status' => 0,
                        'status_date'    => $now,
                        'created_by'     => $user_id,
                        'created_date'   => $now,
                    ];
                    $res = $callConfig->insertRecord('trip_hotels', $rec);
                    $return['trip_hotels'][] = $res;
                }
                foreach (non_empty_rows($cars, ['travel_mode', 'from_location', 'to_location', 'pickup_at', 'dropoff_at', 'car_type', 'trip_purpose'], $rowEmpty) as $c) {
                    $rec = [
                        'company_id'     => $company_id,
                        'trip_id'        => $trip_id,
                        'user_id'        => $user_id,
					    'trip_purpose'   => $c['trip_purpose'] ?? null,
					    'trip_date'      => $c['pickup_at'] ? $this->dateconvertdbformat($c['pickup_at']) : null,
					    'from_location'  => $c['from_location'] ?? null,
					    'to_location'    => $c['to_location'] ?? null,
					    'travel_mode'    => $c['travel_mode'] ?? null,
					    'estimated_cost' => $c['estimated_cost'] ?? 0,
					    'pickup_at'      => $c['pickup_at'] ? date('Y-m-d H:i:s', strtotime($c['pickup_at'])) : null,
					    'dropoff_at'     => $c['dropoff_at'] ? date('Y-m-d H:i:s', strtotime($c['dropoff_at'])) : null,
					    'car_type'       => $c['car_type'] ?? null,
					    'driver_needed'  => $c['driver_needed'] ?? null,
                        'is_active'      => 0,
                        'current_status' => 0,
                        'status_date'    => $now,
                        'created_by'     => $user_id,
                        'created_date'   => $now,
                    ];
                    $res = $callConfig->insertRecord('local_trips', $rec);
                    $return['local_trips'][] = $res;
                }
            }
        }
        return $return;
    }



}