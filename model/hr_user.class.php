<?php 
class hrUserClass extends userClass{
	function getAttendanceData($data){
	 	global $callConfig;
	 	$query2 = "SELECT attendace_date,check_in_type,clock_in,check_out_type,clock_out FROM employee_daily_attendance_details WHERE is_active=0 AND company_id = '".$data['company_id']."'  AND attendace_date='".$data['date']."' AND employee_id=".$data['emp_id']." order By clock_in ASC";
	 	$datas = $callConfig->getAllRows($query2);
	 	return $datas;
	}
	function togetdatewiseattendancedata($get){
		global $callConfig;
		$query = "SELECT id, emp_code, display_name, email_id, joining_date, user_id FROM employees WHERE company_id = '".$get['company_id']."' AND  id=".$get['employee_id'];
		$emp_data = $callConfig->getRow($query);
	 	$data['emp_data'] = $emp_data;
	 	$query2 = "SELECT attendace_date,check_in_type,clock_in,check_out_type,clock_out FROM employee_daily_attendances WHERE is_active=0 AND company_id = '".$get['company_id']."'  AND attendace_date='".$get['date']."' AND employee_id=".$get['employee_id']." order By clock_in ASC";
	 	$att_det = $callConfig->getRow($query2);
	 	$data['main_attendancedata'] = $att_det;
	 	$query2 = "SELECT attendace_date,check_in_type,clock_in,check_out_type,clock_out FROM employee_daily_attendance_details WHERE is_active=0 AND company_id = '".$get['company_id']."'  AND attendace_date='".$get['date']."' AND employee_id=".$get['employee_id']." order By clock_in ASC";
	 	//echo $query2;
	 	$att_det = $callConfig->getAllRows($query2);
	 	$data['attendancedata'] = $att_det;
	 	$query = "SELECT id, minimum_hours_required, maximum_hours_cap, full_day_hours, half_day_hours, break_time, working_days  FROM attendance_settings WHERE company_id = '".$get['company_id']."' AND  is_active =0";		
		$att_setting = $callConfig->getRow($query);
	 	$data['att_setting'] = $att_setting;
	 	return $data;
	}
	function getEmployeeWorkAttendances($get){
	 	global $callConfig;
	 	//echo "<pre>";
	 	$emp_list = json_decode($get['emplist'],1);
	 	$discrepancy = array();
	 	$month = $get['month'];
	 	$year = $get['year'];
		$shift_check = SHIFT_CHECK; //1 meaning yes
		$slctdate = date($get['year']."-".$get['month']."-".$get['monthstartday']);
		$startdate = date("Y-m-d",strtotime($slctdate));
		$enddate = date("Y-m-d",strtotime("+1 month",strtotime($slctdate)));  
		$enddate = date("Y-m-d", strtotime("-1 days", strtotime($enddate)));
		$retrunarray = array();
		$work_attendance_logssql = "SELECT month_year, type, created_date, import_swap_in_out_id FROM `work_attendance_logs` WHERE company_id=  '".$get['company_id']."' AND month_year ='".$year."-".$month."' AND is_active = 0 GROUP BY type ORDER BY created_date DESC";
		$work_attendance_logsres = $callConfig->getAllRows($work_attendance_logssql);
		$retrunarray['work_attendance_logs']=$work_attendance_logsres;
		if(empty($emp_list)){
			//$query="SELECT id,display_name,image,emp_code  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND joining_date <= '".$enddate."' AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL)";
			//$query = "SELECT id,display_name,image,emp_code FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

			//$query = "SELECT id,display_name,image,emp_code FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '1' <> '".$get['admin_type']."' THEN ( emp.id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = '".$get['emp_id']."') ) ) ELSE 1 END )";
			$query ="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving FROM `employees` emp WHERE emp.company_id = '".$get['company_id']."' AND emp.employee_type = 1 AND emp.is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving <= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND (emp.hr_buddy = '".$get['emp_id']."' OR emp.finance_buddy = '".$get['emp_id']."' OR emp.admin_buddy = '".$get['emp_id']."' OR emp.project_buddy = '".$get['emp_id']."' OR emp.loc_buddy = '".$get['emp_id']."' OR emp.reporting_manager_id = '".$get['user_id']."' OR (emp.id IN (SELECT	pae.emp_id FROM pay_area_employees pae, payroll_area_admins paa WHERE pae.company_id = paa.company_id AND pae.company_id = emp.company_id AND	pae.pay_area_id = paa.pay_area_id AND pae.is_active = paa.is_active AND	pae.is_active = 0 AND paa.emp_id = '".$get['emp_id']."' ))	OR ( CASE WHEN '1' = '".$get['admin_type']."' THEN 1 END) = 1)";
			
		}else{
			$liemp =implode(",",$emp_list);
			$query="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND joining_date <= '".$enddate."' AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL) AND id IN(".$liemp.")";
		}
		$retrunarray['employees_sql']=$query;
		//echo $query; exit;
		$empdatas = $callConfig->getAllRows($query);
		$retrunarray['empdatas']=$empdatas;
		
		$shiftsql = "SELECT ss.id,emps.emp_id, ss.name, ss.minimum_hours_required,ss.half_day_hours,ss.working_days FROM emp_shifts emps, shift_settings ss WHERE emps.is_active  = ss.is_active AND  emps.company_id =ss.company_id AND  emps.shift_id  = ss.id AND emps.is_active = 0 AND emps.company_id=".$get['company_id'];
		$shiftres = $callConfig->getAllRows($shiftsql);
		foreach($shiftres as $shiftress){
			$shiftresarray[$shiftress->emp_id] = $shiftress;
		}
		foreach ($empdatas as $value){
			if($shift_check == '1' AND $get['shift_categroy'] !=""){
				if($shiftresarray[$value->id]->id ==$get['shift_categroy']){
					$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'emp_code'=>$value->emp_code,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
				}
			}else{
				$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'emp_code'=>$value->emp_code,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
			}
		}
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = json_decode($atsdata->working_days,1);
		
		$shiftcatsql = "SELECT id, shift_name, clock_in, clock_out FROM shifts WHERE is_active = 0 AND company_id=".$get['company_id'];
		$shiftcatres = $callConfig->getAllRows($shiftcatsql);
		
		$shiftkeys = "SELECT id, key_short_code, display_value, color_code, key_name FROM attendance_key_types WHERE company_id=".$get['company_id']." AND leave_category_id IS NULL AND is_active=0";
		$shiftkeysdata = $callConfig->getAllRows($shiftkeys); // working //absent
		foreach ($shiftkeysdata as $skey=>$svalue) {
			$shift[$svalue->key_short_code] = (array)$svalue;
		}

		$attkey = "SELECT lc.id, lc.name, akt.key_short_code, akt.display_value, akt.color_code, akt.key_name FROM leave_categories lc, attendance_key_types akt WHERE lc.company_id = '".$get['company_id']."' AND lc.company_id = akt.company_id AND lc.id = akt.leave_category_id AND lc.is_active = akt.is_active AND lc.is_active = '0'";
		$attkeyres = $callConfig->getAllRows($attkey); // working //absent
		foreach ($attkeyres as $svalue) {
			$shift[$svalue->key_short_code] = (array)$svalue;
		}
		$retrunarray['attendance_key_types'] = $shift;

		$attendancesql = "SELECT attendace_date,employee_id,attendance_key_short_code  FROM `employee_daily_attendances` WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."'";
		$attendanceres = $callConfig->getAllRows($attendancesql); // working //absent
		foreach ($attendanceres as $attendancere) {
			$attendacnearray[$attendancere->employee_id][$attendancere->attendace_date] = $attendancere;
		}

		$leavetypesql = "SELECT lt.id, lc.name, akt.key_short_code FROM leave_types lt, leave_categories lc, attendance_key_types akt WHERE lt.company_id = '".$get['company_id']."' AND lt.company_id = lc.company_id AND lt.company_id= akt.company_id AND lt.leave_category_id = lc.id AND lc.id = akt.leave_category_id AND lt.is_active = 0 AND lt.is_active = lc.is_active AND lt.is_active= akt.is_active";
			$leavetypesres=$callConfig->getAllRows($leavetypesql);
			foreach($leavetypesres as $ltype){
				$leavetype[$ltype->id] = $ltype->key_short_code;
			}
		$retrunarray['leavetype'] = $leavetype;


		$leavessql = "select employee_id,leave_from,leave_to,leave_type_id,start_half_day,end_half_day,datelist 
		from employee_leaves, 
		(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) datelist from
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) as v
		where datelist>=leave_from and datelist <=leave_to AND ((MONTH(leave_from) = '".$month."' AND YEAR(leave_from) = '".$year."') OR (MONTH(leave_to) = '".$month."' AND YEAR(leave_to) = '".$year."') )";
	 	$leavesdetails = $callConfig->getAllRows($leavessql); // working //absent
	 	foreach ($leavesdetails as $leavesdetail) {
	 		$leavesdetailsarray[$leavesdetail->employee_id][$leavesdetail->datelist] = array('type'=>$leavesdetail->leave_type_id,'start_half_day'=>$leavesdetail->start_half_day,'end_half_day'=>$leavesdetail->end_half_day);
	 	}
	 	$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
		$holidaysdata = $callConfig->getAllRows($holidays); //holidays
		foreach ($holidaysdata as $hkey=>$hvalue) {
			$holidaysdatas[] = $hvalue->holiday_date;
		}
		$checkupdate = "SELECT id FROM `employee_daily_attendances` WHERE is_active='0' and company_id=".$get['company_id']." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."'";

		if($get['admin_type']!=1){
			$checkupdate.=" AND employee_id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		}
		//echo $checkupdate; exit

		$checkupdates = $callConfig->getRow($checkupdate);

		$tempdetails = "SELECT employee_id,attendance_date,attendance_key_short_code,start_half_day,end_half_day,clock_in,clock_out FROM temp_attendance_details WHERE is_active='0' and company_id=".$get['company_id']." AND MONTH(attendance_date) = '".$month."' AND YEAR(attendance_date) = '".$year."'";
		$tempdetailsdata = $callConfig->getAllRows($tempdetails); //holidays
		foreach ($tempdetailsdata as $tempary) {
			$tempdetailary[$tempary->employee_id][$tempary->attendance_date] = array("date"=>$tempary->attendance_date,"type"=>$tempary->attendance_key_short_code,"start_half_day"=>$tempary->start_half_day,"end_half_day"=>$tempary->end_half_day,'clock_in'=>$tempary->clock_in,'clock_out'=>$tempary->clock_out);
		}
		$min_working_hours = explode(":", $atsdata->minimum_hours_required);
		$half_working_hours = explode(":", $atsdata->half_day_hours);
		$timeshetetrys = array();
		$i = 0;
		$notleaveattendancekey = array('P','R','A','H','O','Z');
		$workingdayattendancekey = array('P','O');
		//echo "<pre>"; print_r($leavesdetailsarray); 
		foreach($empidlist as $ky => $emp){
			$monthworkingdays = 0;
			$actualworkingdays = 0;
			$start = new DateTime($startdate);
			$end = new DateTime($enddate);
			$dates = array();
			$timeshettempoyee = array();
			$emp_shift_days = json_decode($shiftresarray[$ky]->working_days,1);
			
			if(isset($checkupdates->id)){
				$retrunarray['valuedisplaytype'] = "update";
				while($start <= $end){
					$date = $start->format('Y-m-d');
					$dy = $start->format('l');
					if(in_array($date,(array)$holidaysdatas)){
						$dates[$date] = array('day'=>'H','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['H']['color_code'],'display_value'=>$shift['H']['display_value']);
					}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1' AND OFF_CHECK == '0')){
						$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
					}
					//elseif(isset($attendacnearray[$ky][$date]) AND isset($leavesdetailsarray[$ky][$date])){
					elseif(isset($leavesdetailsarray[$ky][$date])){
						$monthworkingdays++;
						$actualworkingdays++;
						$leavtypedetail = $leavesdetailsarray[$ky][$date];
						//print_r($leavtypedetail);
						$leaveshortcode = $leavetype[$leavtypedetail['type']];
						$dates[$date] = array('day'=>$leaveshortcode,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$leaveshortcode]['color_code'],'display_value'=>$shift[$leaveshortcode]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift[$leaveshortcode]['color_code']);
						$discrepancy[$ky] = $emp;
					}elseif(isset($attendacnearray[$ky][$date])){
						$shortkey = $attendacnearray[$ky][$date];
						if($shortkey->attendance_key_short_code == "O"){
							$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
						}elseif($shortkey->attendance_key_short_code == "Z"){
							$dates[$date] = array('day'=>'Z','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['Z']['color_code'],'display_value'=>$shift['Z']['display_value'],'working_color'=>$shift['Z']['color_code']);
						}else{
							$monthworkingdays++;
							$actualworkingdays++;
							$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value']);
						}
					}else{
						///if(isset($leavesdetailsarray[$ky][$date])){
							//$monthworkingdays++;
							//$actualworkingdays++;
							

							//$dates[$date] = array('day'=>$leavetype[],'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								//$discrepancy[$ky] = $emp;


								/**
							if($leavesdetailsarray[$ky][$date]['type'] == '6'){
								$dates[$date] = array('day'=>'S','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['S']['color_code'],'display_value'=>$shift['S']['display_value'],'strthalfday'=>$leavesdetailsarray[$ky][$date]['start_half_day'],'endthalfday'=>$leavesdetailsarray[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								$discrepancy[$ky] = $emp;
							}else{
								$dates[$date] = array('day'=>'V','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								$discrepancy[$ky] = $emp;
							}  **/
						//}else{
						$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value']);
						$discrepancy[$ky] = $emp;
						//}
					}
					if($i=='0'){
						$alldate[]  = $date; 
					}	
					$start->modify('+1 days');
				// if($date == date("Y-m-d")){
				// 	break;
				// }
				}
			}else{
				$retrunarray['valuedisplaytype'] = "insert";
				while($start <= $end){
					$date = $start->format('Y-m-d');
					$dy = $start->format('l');
					if($emp['joining_date'] >= $date || ($emp['date_of_leaving'] <= $date && $emp['date_of_leaving'] !="")){
						$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value']);
					}elseif(in_array($date,(array)$holidaysdatas)){
						$dates[$date] = array('day'=>'H','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['H']['color_code'],'display_value'=>$shift['H']['display_value']);
					}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1') AND OFF_CHECK == '0'){
						$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
					}elseif($tempdetailary[$ky][$date] !=""){
						
						$clockintime = $tempdetailary[$ky][$date]['clock_in'];
						$clockouttime = $tempdetailary[$ky][$date]['clock_out'];
						
						$discrepancy[$ky] = $emp;
						$attendance_key_short_code = $tempdetailary[$ky][$date]['type'];
						
						if(!in_array($attendance_key_short_code, $notleaveattendancekey)){  // leave Show
							$actualworkingdays++;
							$monthworkingdays++;
							$dates[$date] = array('day'=>$attendance_key_short_code,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$attendance_key_short_code]['color_code'],'display_value'=>$shift[$attendance_key_short_code]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift[$attendance_key_short_code]['color_code']);
						}else{ // normal Day Show presdsent and absent
							if(in_array($attendance_key_short_code, $workingdayattendancekey)){  // o,p working day count
								$monthworkingdays++; 
								if($attendance_key_short_code !='O'){  // o,p working day count
									$actualworkingdays++; 

								}
							}
							$dates[$date] = array('day'=>$attendance_key_short_code,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$attendance_key_short_code]['color_code'],'display_value'=>$shift[$attendance_key_short_code]['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime,'working_color'=>$shift['Z']['color_code']);
							
						}

						/**
						if($tempdetailary[$ky][$date]['type']=="A"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime);
						}elseif($tempdetailary[$ky][$date]['type']=="P"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime);
						}elseif($tempdetailary[$ky][$date]['type']=="O"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
						}else{
							$actualworkingdays++;
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>$tempdetailary[$ky][$date]['type'],'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$tempdetailary[$ky][$date]['type']]['color_code'],'display_value'=>$shift[$tempdetailary[$ky][$date]['type']]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);


							/**if($tempdetailary[$ky][$date]['leave_type_id']=="6"){
								$discrepancy[$ky] = $emp;
								$dates[$date] = array('day'=>'S','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['S']['color_code'],'display_value'=>$shift['S']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
							}else{
								$discrepancy[$ky] = $emp;
								$dates[$date] = array('day'=>'V','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
							}	**//**
						}  **/
					}else{
						$monthworkingdays++;
						$actualworkingdays++;
						$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value']);
					}
					if($i=='0'){
						$alldate[]  = $date; 
					}	
					$start->modify('+1 days');
				}
			}
			$timeshetetrys[$ky] = $dates;
			$i++;
			$monthworkignday[$ky] = $monthworkingdays;
			$actualworkingday[$ky] = $actualworkingdays;
		}
		$retrunarray['shifts'] = $shiftcatres;
		$retrunarray['monthworkingdays'] = $monthworkignday;
		$retrunarray['actualworkingdays'] = $actualworkingday;
		$retrunarray['discrepancy'] = $discrepancy;
		$retrunarray['timesheet'] = $timeshetetrys;
		$retrunarray['date'] = $alldate;
		$retrunarray['emplist'] = $empidlist;
		$retrunarray['attendacnearray'] = $attendacnearray;
		$retrunarray['query']=$query;
		//$retrunarray['min_working_hours'] = $min_working_hours[0];
		//$retrunarray['min_working_hours_full'] = $atsdata->minimum_hours_required;
		//$retrunarray['half_working_hours'] = $half_working_hours[0];
		//echo "<pre>"; print_r($retrunarray);
		return $retrunarray;
	}
	function insertEmployeeWorkAttendances($get){
		global $callConfig;
		$submitattendancess = array();
		$shift_check = SHIFT_CHECK; //1 meaning yes
		$month = $get['month'];
		$year = $get['year'];
		$get['month_year'] = $year."-".$month;
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = json_decode($atsdata->working_days,1);
		if($shift_check == 1){
			$shiftsql = "SELECT ss.id,emps.emp_id, ss.name, ss.minimum_hours_required,ss.half_day_hours,ss.working_days FROM emp_shifts emps, shift_settings ss WHERE emps.is_active  = ss.is_active AND  emps.company_id =ss.company_id AND  emps.shift_id  = ss.id AND emps.is_active = 0 AND emps.company_id=".$get['company_id'];
			$shiftres = $callConfig->getAllRows($shiftsql);
			foreach($shiftres as $shiftress){
				$shiftresarray[$shiftress->emp_id] = $shiftress;
			}
		}
		$tempdetails = "SELECT * FROM temp_attendance_details WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(attendance_date) = '".$month."' AND YEAR(attendance_date) = '".$year."'";
		/**if($get['admin_type']!=1){
			$tempdetails.=" AND employee_id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		}**/

		$tempdetailsdata = $callConfig->getAllRows($tempdetails); //holidays
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
		$holidaysdata = $callConfig->getAllRows($holidays); //holidays
		foreach ($holidaysdata as $hkey=>$hvalue) {
			$holidaysdatas[] = $hvalue->holiday_date;
		}

		$attendance_key_types = array();
		$aktypesql = "SELECT key_short_code, leave_category_id FROM attendance_key_types WHERE is_active=0 and company_id=".$get['company_id']." AND leave_category_id !=''";
		$aktyperes = $callConfig->getAllRows($aktypesql); //holidays
		foreach ($aktyperes as $valk) {
			$attendance_key_types[$valk->key_short_code] = $valk->key_short_code;
		}
		//$notleaveattendancekey = array('P','R','A','H','O');
		foreach($tempdetailsdata as $tempary){
			if($tempary->attendance_key_short_code == 'A'){
				$absentarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif($tempary->attendance_key_short_code == 'P' || $tempary->attendance_key_short_code == 'Z'){
				$presentarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif($tempary->attendance_key_short_code == 'O'){
				$offdayarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif(in_array($tempary->attendance_key_short_code, $attendance_key_types)){
				$leavearray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}
		}
		//$query="SELECT id,display_name,image,emp_code  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND date_of_leaving IS NULL";
		$slctdate = date($get['year']."-".$get['month']."-".$get['monthstartday']);
		$startdate = date("Y-m-d",strtotime($slctdate));
		$enddate = date("Y-m-d",strtotime("+1 month",strtotime($slctdate)));  
		$enddate = date("Y-m-d", strtotime("-1 days", strtotime($enddate)));
		$query ="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving FROM `employees` emp WHERE emp.company_id = '".$get['company_id']."' AND emp.employee_type = 1 AND emp.is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving <= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND (emp.hr_buddy = '".$get['emp_id']."' OR emp.finance_buddy = '".$get['emp_id']."' OR emp.admin_buddy = '".$get['emp_id']."' OR emp.project_buddy = '".$get['emp_id']."' OR emp.loc_buddy = '".$get['emp_id']."' OR emp.reporting_manager_id = '".$get['user_id']."' OR (emp.id IN (SELECT	pae.emp_id FROM pay_area_employees pae, payroll_area_admins paa WHERE pae.company_id = paa.company_id AND pae.company_id = emp.company_id AND	pae.pay_area_id = paa.pay_area_id AND pae.is_active = paa.is_active AND	pae.is_active = 0 AND paa.emp_id = '".$get['emp_id']."' ))	OR ( CASE WHEN '1' = '".$get['admin_type']."' THEN 1 END) = 1)";
		
		//$query="SELECT id,display_name,image  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 and is_active =0";
		/**if($get['admin_type']!=1){
			$query.=" AND id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		} **/
		$empdatas = $callConfig->getAllRows($query);
		foreach ($empdatas as $value){
			$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
		}
		$insdailyattendance = "INSERT INTO `employee_daily_attendances`(`shift_settings_id`,`company_id`, `employee_id`, `attendace_date`, `clock_in`, `check_in_type`, `check_out_type`, `clock_out`, `is_active`, `current_status`,`created_date`, `created_by`,`attendance_key_short_code`) VALUES ";
		$insdailyattendancedetails = "INSERT INTO `employee_daily_attendance_details`(`company_id`, `employee_id`, `attendace_date`, `clock_in`, `check_in_type`, `check_out_type`, `clock_out`, `is_active`, `current_status`,`created_date`, `created_by`) VALUES ";

		$returnarray['empidlist'] = $empidlist;
		//echo "<pre>"; print_r($offdayarray);
		foreach($empidlist as $ky => $emp){
			$start = new DateTime($startdate);
			$end = new DateTime($enddate);
			$emp_details = $shiftresarray[$ky];
			$emp_shift_days = json_decode($shiftresarray[$ky]->working_days,1);
			while($start <= $end){
				$date = $start->format('Y-m-d');
				$dy = $start->format('l');
				if($emp['joining_date'] >= $date || ($emp['date_of_leaving'] <= $date && $emp['date_of_leaving'] !="")){
				
				}elseif(in_array($date,(array)$holidaysdatas)){
		//holidays				
				}elseif(isset($absentarray[$ky][$date])){
		//absent part
				}elseif(isset($offdayarray[$ky][$date])){
					$attendance_key_short_code = $offdayarray[$ky][$date]->attendance_key_short_code;	
					$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','".$instarttime."', '1', '1', '0:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
				}elseif(isset($leavearray[$ky][$date])){
				//leave part   		
					//$arrayleave = array('emp_id'=>$ky,'categorytype'=>$leavearray[$ky][$date]->leave_type_id,'leave_year'=>$get['year'],'company_id'=>$get['company_id']);
					//$getleaveid = $this->checkLeaveBalanceOrHalfDay($arrayleave);
					$halfdaysleave = 1;
					if($leavearray[$ky][$date]->start_half_day=='0' || $leavearray[$ky][$date]->start_half_day =='0'){
						$halfdaysleave = 0.5; 
						$attendance_key_short_code = $leavearray[$ky][$date]->attendance_key_short_code; 
						$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '14:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
						$insdailyattendancedetails.="('".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '14:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
					}
						//$updatequeryleave = "UPDATE emp_leave_balances SET consumed_leaves=consumed_leaves+".$halfdaysleave.",updated_date = '".date("Y-m-d H:i:s")."',updated_by = '".$get['user_id']."' WHERE employee_id =".$ky." AND leave_type='".$leavearray[$ky][$date]->leave_type_id."' AND is_active ='0' AND  leave_year =".$year;
					if($leavearray[$ky][$date]->leave_type_id !=""){
						$updatequeryleave = "UPDATE emp_leave_balances SET consumed_leaves=consumed_leaves+".$halfdaysleave.",updated_date = '".date("Y-m-d H:i:s")."',updated_by = '".$get['user_id']."' WHERE employee_id =".$ky." AND leave_type='".$leavearray[$ky][$date]->leave_type_id."'";
						$updateleave = $callConfig->executeQuery($updatequeryleave);
					}
					$returnarray['emp_leave_balances'][] =  $updateleave;
					$insert_leave_array = array('company_id'=>$get['company_id'],'employee_id'=>$ky,'leave_type_id'=>$leavearray[$ky][$date]->leave_type_id,'leave_from'=>$date,'leave_to'=>$date,'leave_days'=>$halfdaysleave,'start_half_day'=>$leavearray[$ky][$date]->start_half_day,'end_half_day'=>$leavearray[$ky][$date]->end_half_day,'created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'199');
					$insertleave=$callConfig->insertRecord('employee_leaves',$insert_leave_array);
					$returnarray['employee_leaves'][] =  $insertleave;
					//}elseif(!in_array($dy, $workingdays)){
				}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1' AND OFF_CHECK == '0')){
				//off days
				}else{
					$starttime = $presentarray[$ky][$date]->clock_in;
					$endtime = $presentarray[$ky][$date]->clock_out;
					$instarttime = $starttime !=""?$starttime:'10:00:00';
					$outendtime = $endtime !=""?$endtime:'18:00:00';
				//working part
					$attendance_key_short_code = $presentarray[$ky][$date]->attendance_key_short_code; 	
					$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','".$instarttime."', '1', '1', '18:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
					$insdailyattendancedetails.="('".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '".$outendtime."','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
				}	
				$start->modify('+1 days');
			}
		}
		$insdailyattendance = substr($insdailyattendance, 0, -1);
		$insdailyattendance .= " ON DUPLICATE KEY UPDATE updated_date ='".date("Y-m-d H:i:s")."'";
		$res = $callConfig->executeQueryString($insdailyattendance);
		$returnarray['employee_daily_attendances'][] =  $insdailyattendance;
		$insdailyattendancedetails = substr($insdailyattendancedetails, 0, -1);
		$res1 = $callConfig->executeQueryString($insdailyattendancedetails);
		$returnarray['employee_daily_attendance_details'][] =  $res1;

		$insertuserarray2=array('company_id'=>$get['company_id'],'import_swap_in_out_id'=>$lastid,'month_year'=>$get['month_year'],'type'=>'S','user_id'=>$get['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$tapres = $callConfig->insertRecord('work_attendance_logs',$insertuserarray2);
		$returnaray['work_attendance_logs'] = $tapres;


		return $returnarray;

	}

	function inserttempattendaceform($get,$data){
		global $callConfig;
		//$date2 = $_POST['date2'];
		$datee = str_replace('/', '-', $data['attendance_date']);
		$formdate = str_replace('/', '-', $data['leave_from']);
		$todate = str_replace('/', '-', $data['leave_to']);
		$absentfrom = str_replace('/', '-', $data['absent_from']);
		$absentto = str_replace('/', '-', $data['absent_to']);
		$datee = date("Y-m-d",strtotime($datee));
		$shift_check = SHIFT_CHECK; //1 meaning yes
		$i = 0;
		//if($data['presentcheck'] == '1' || $data['presentcheck'] == '3'){
		if($data['presentcheck'] == '3'){
			$start = new DateTime($datee);
			$end = new DateTime($datee);
		}else if($data['presentcheck'] == '1'){
			$start = new DateTime($absentfrom);
			$end = new DateTime($absentto);
		}else{
			$start = new DateTime($formdate);
			$end = new DateTime($todate);
		}
		$month =$start->format('m');
		$year =$start->format('Y');
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$data['company_id'];
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = json_decode($atsdata->working_days,1);
		
		$shiftsql = "SELECT ss.id,emps.emp_id, ss.name, ss.minimum_hours_required,ss.half_day_hours,ss.working_days FROM emp_shifts emps, shift_settings ss WHERE emps.is_active  = ss.is_active AND  emps.company_id =ss.company_id AND  emps.shift_id  = ss.id AND emps.is_active = 0 AND emps.company_id='".$data['company_id']."' AND emps.emp_id='".$data['emp_id']."'";
		$shiftres = $callConfig->getRow($shiftsql);
		$emp_shift_days = json_decode($shiftres->working_days,1);
		
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$data['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
		$holidaysdata = $callConfig->getAllRows($holidays); //holidays
		foreach ($holidaysdata as $hkey=>$hvalue){
			$holidaysdatas[] = $hvalue->holiday_date;
		}
		$returnarray['holidaysdatas']  = $holidaysdatas;
		$returnarray['workingdays']  = $workingdays;
		$returnarray['emp_shift_days']  = $emp_shift_days;
		while($start <= $end){
			$date = $start->format('Y-m-d');
			$dy = $start->format('l');
		//if(in_array($date,$holidaysdatas) || !in_array($dy, $workingdays)){
			if(in_array($date,(array)$holidaysdatas) || (!in_array($dy, (array)$workingdays) AND $shift_check == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1')){
				$returnarray['holidaynotworking'][] = $date;
			}else{
				$query2 = "SELECT id FROM temp_attendance_details WHERE company_id = '".$data['company_id']."'  AND attendance_date='".$date."' AND employee_id=".$data['emp_id'];
				$datas = $callConfig->getRow($query2);
				$start_half_day = $data['start_half_day'] =="0"?"0":"1";
				$end_half_day = $data['end_half_day'] =="0"?"0":"1";
				$start_half_days = "";
				$end_half_days = "";
				$halfdaysleave = '1'; 
				if($start_half_day=='0' AND date("Ymd",strtotime($formdate)) == date("Ymd",strtotime($date))){
					$start_half_days = '0';
					$halfdaysleave = '0.5'; 
				}
				if($end_half_day=='0' AND date("Ymd",strtotime($todate)) == date("Ymd",strtotime($date))){
					$end_half_days = '0';
					$halfdaysleave = '0.5'; 
				}
				if(isset($datas->id)){
					$insert_array = array('shift_settings_id'=>$shiftres->id,'company_id'=>$data['company_id'],'employee_id'=>$data['emp_id'],'attendance_date'=>$date,'attendance_key_short_code'=>$data['presentcheck'],'leave_type_id'=>$data['leave_type_id'],'leave_from'=>$date,'leave_to'=>$date,'leave_days'=>$halfdaysleave,'start_half_day'=>$start_half_days,'end_half_day'=>$end_half_days,'is_active'=>'0');	
					$where = "id = '".$datas->id."'";
					if($data['presentcheck'] == '3'){
						$insert_array['is_active'] = "";
					}
					$approval_id=$callConfig->updateRecord("temp_attendance_details",$insert_array,"","",$where);
				}else{
					if($data['presentcheck'] != '3'){
						$insert_array = array('shift_settings_id'=>$shiftres->id,'company_id'=>$data['company_id'],'employee_id'=>$data['emp_id'],'attendance_date'=>$date,'attendance_key_short_code'=>$data['presentcheck'],'leave_type_id'=>$data['leave_type_id'],'leave_from'=>$date,'leave_to'=>$date,'leave_days'=>$halfdaysleave,'created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','start_half_day'=>$start_half_days,'end_half_day'=>$end_half_days);	
						$approval_id=$callConfig->insertRecord('temp_attendance_details',$insert_array);
					}
				}
				$returnarray['leavedate'][] =$date;
			}
			$start->modify('+1 days');
			$i++;
		}	
		return $returnarray;		
	}
	function InsertImportSwapInOut($get,$post){
		global $callConfig;
		$importdata = array_map('str_getcsv', file($post['csvFile']));
		$numRows = count($importdata);
		$numCols = $post['import_type'] =="withoutattendancesetting"?count((array)$importdata[1]):count((array)$importdata[0]);
		$importrows = $post['import_type'] =="withoutattendancesetting"?$importdata[1]:$importdata[0];
		if($post['import_type'] =="withoutattendancesetting"){
			unset($importdata[0]);
		}
		$returnaray['post'] = $post;
		$returnaray['numRows']  =$numRows;
		$returnaray['numCols']  = $numCols;
		$insertuserarray1=array('company_id'=>$post['company_id'],'file_name'=>$post['file_name'],'start_date'=>$this->dateconvertdbformat($post['start_date']),'end_date'=>$this->dateconvertdbformat($post['end_date']),'comment'=>$post['comment'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$tapres = $callConfig->insertRecord('import_swap_in_outs',$insertuserarray1);
		$returnaray['respons'] = $tapres;
		$lastid = $tapres['LASTID'];
		$i=1;
		$coulunarray = array();
		foreach($importrows as $colunm){
			$coulunarray[] = "column".$i; 
			$i++;
		}
		$sql = "INSERT INTO `import_swap_in_out_details`(company_id, import_swap_in_out_id,is_active,".implode(',',$coulunarray).") VALUES";
		foreach ($importdata as $row){
			$rowarray = array();
			foreach ($row as $cell){
				$rowarray[] = htmlspecialchars($cell);
			}
			$sql .="('".$post['company_id']."','".$lastid."','0','".implode("','",$rowarray)."'),";
		}
		$insertsql = rtrim($sql, ',');
		$res = $callConfig->executeQuery($insertsql);
		$returnaray['insertsql'] = $insertsql;
		$returnaray['res'] = $res;
		$post['rid']= $lastid;
		//$resattendance = $this->insertTempImportSwapInOut($post);
		$returnaray['temp_respons'] = $resattendance;

		$insertuserarray2=array('company_id'=>$post['company_id'],'import_swap_in_out_id'=>$lastid,'month_year'=>$post['month_year'],'type'=>'I','user_id'=>$post['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$tapres = $callConfig->insertRecord('work_attendance_logs',$insertuserarray2);
		$returnaray['work_attendance_logs'] = $tapres;


		return $returnaray;
	}
	function getImportSwapInOut($get){
		global $callConfig;
		
		$importinoutsql = "SELECT id,file_name,start_date,end_date FROM import_swap_in_outs WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active = 0";
		$swapinout=$callConfig->getAllRows($importinoutsql);
		$return['importinoutsql'] = $swapinout;

		$employee_terminationssql = "SELECT id,emp_id, employee_leaving_date FROM employee_terminations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$employee_terminationsres=$callConfig->getAllRows($employee_terminationssql);
		$employee_terminations = array();
		foreach($employee_terminationsres as $empterm){
			$employee_terminations[$empterm->emp_id] = $empterm->employee_leaving_date;
		}
		$return['employee_terminations'] = $employee_terminations;


		$sql = "SELECT `column1`, `column2`, `column3`, `column4`, `column5`, `column6`, `column7`, `column8`, `column9`, `column10`, `column11`, `column12`, `column13`, `column14`, `column15`, `column16`, `column17`, `column18`, `column19`, `column20`, `column21`, `column22`, `column23`, `column24`, `column25`, `column26`, `column27`, `column28`, `column29`, `column30`, `column31`, `column32`, `column33`, `column34`, `column35`, `column36`, `column37`, `column38`, `column39`, `column40`, `column41`, `column42`, `column43`, `column44`, `column45`, `column46`, `column47`, `column48`, `column49`, `column50`, `column51`, `column52`, `column53`, `column54`, `column55`, `column56`, `column57`, `column58`, `column59`, `column60`, `column61`, `column62`, `column63`, `column64`, `column65`, `column66`, `column67`, `column68`, `column69` FROM import_swap_in_out_details WHERE company_id = '".$get['company_id']."' AND import_swap_in_out_id = '".$get['rid']."' AND is_active = 0";
		$imposrtfileres=$callConfig->getAllRows($sql);
		$query = "SELECT * FROM attendance_settings WHERE company_id={$get['company_id']} AND is_active=0 ORDER BY id ASC LIMIT 1";
		$data =$callConfig->getRow($query);
		// $working_days_arr = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
		if($data->working_days!=''){
			$working_days_arr = json_decode($data->working_days,1);
		}
		$return['attendance_settings'] = $data;
		

		if($get['import_type'] == "withoutattendancesetting"){
			$empdetails = array();
			$dates = $empid = array();
			$notdates = array('column1','column2','column3');
			foreach($imposrtfileres[0] as $keystr=>$getdate){
				if(!in_array($keystr, $notdates)){
					if($getdate !=""){
						$dates[$keystr] = $getdate;
					}
				}
			}
			unset($imposrtfileres[0]);
			foreach($imposrtfileres as $importres){
				$empid[$importres->column2] = $importres->column2;
			    $offarray = $sundaycnt =$pressentcnt = $absenttcnt= $total_day  =$leaveclcnt =$leaveelcnt=$leavemlcnt =$leaveslcnt =$leavewpcnt =$leavecocnt = $leaveplcnt = $leavemrcnt= $halfdaycnt = $halfdayabsentcnt = 0;
			    foreach($dates as $field=>$columns){
			    	$total_day++;
			    	// $sunday = date("D",strtotime($columns));
			    	$dayName = date("l", strtotime($columns));
			    		
			    	if($employee_terminations[$importres->column2] !="" AND strtotime($employee_terminations[$importres->column2]) <= strtotime($columns)){
			    		$absenttcnt++;
			    	}elseif($importres->$field == "O"){
			    		$offarray++;
			    	}elseif($importres->$field == "P"){
			    		$pressentcnt++;
			    	}elseif($importres->$field == "S"){
			    		$leavecnt++;
			    	}elseif($importres->$field == "A"){
			    		$absenttcnt++;
			    	}elseif($importres->$field == "F"){
			    		$halfdaycnt++;
			    	}elseif($importres->$field == "Z"){
			    		$halfdayabsentcnt++;
			    		$absenttcnt = $absenttcnt+0.50;
			    		$pressentcnt = $pressentcnt+0.50;
			    	}else{
			    		$empdetails[$importres->column2]['leave'][$importres->$field]++;
			    	}
			    	// if($sunday == "Sun"){
			    	// 	$sundaycnt++;
			    	// }
			    	if(!in_array($dayName,$working_days_arr)){
			    		$sundaycnt++;
			    	}
			    	$empdetails[$importres->column2]['offday_cnt'] = $offarray;
			    	$empdetails[$importres->column2]['sunday_cnt'] = $sundaycnt;
			    	$empdetails[$importres->column2]['present_cnt'] = $pressentcnt;
			    	$empdetails[$importres->column2]['absent_cnt'] = $absenttcnt;
			    	$empdetails[$importres->column2]['total_day_cnt'] = $total_day;
			    	$empdetails[$importres->column2]['total_half_day_cnt'] = $halfdaycnt;
			    	$empdetails[$importres->column2]['total_half_day_absent_cnt'] = $halfdayabsentcnt;
			    }
			}
			$return['import_swap_in_out_details'] = $empdetails;
			$empids =  implode(",",$empid); 
			$empsql = "SELECT id,display_name,emp_code,email_id,joining_date FROM employees WHERE company_id = '".$get['company_id']."' AND id IN(".$empids.") AND is_active = 0";
			$empres=$callConfig->getAllRows($empsql);
			foreach($empres as $empress){
				$emplist[$empress->id] = $empress;
			}
			$return['empsql'] = $empsql;
			$return['employees'] = $emplist;

			
			$empleavesql = "SELECT elb.id, elb.employee_id, elb.applicable_leaves, lt.leave_category_id, lc.name, akt.key_short_code FROM emp_leave_balances elb, leave_types lt, leave_categories lc, attendance_key_types akt WHERE elb.company_id = '".$get['company_id']."' AND elb.company_id = lt.company_id AND elb.company_id = lc.company_id AND elb.company_id = akt.company_id AND elb.leave_type = lt.id AND lt.leave_category_id = lc.id AND lc.id = akt.leave_category_id AND employee_id IN(".$empids.") AND elb.is_active = lt.is_active AND elb.is_active = lc.is_active AND elb.is_active = akt.is_active AND elb.is_active = '0' GROUP BY employee_id, leave_type";
			$empleaveres=$callConfig->getAllRows($empleavesql);
			foreach($empleaveres as $empress){
				//$leave_categories[$empress->key_short_code] = $empress;
				$empleavelist[$empress->employee_id][$empress->key_short_code] = $empress;
			}
			$return['emp_leave_balances'] = $empleavelist;


			$leave_categoriessql = "SELECT lc.id, lc.name, akt.key_short_code FROM leave_categories lc, attendance_key_types akt WHERE lc.company_id = '".$get['company_id']."' AND lc.company_id = akt.company_id AND lc.id = akt.leave_category_id AND lc.is_active = '0'";
			$leave_categoriesres=$callConfig->getAllRows($leave_categoriessql);
			foreach($leave_categoriesres as $leavcat){
				$leave_categories[$leavcat->key_short_code] = $leavcat;
			}
			$return['leave_categories'] = $leave_categories;

			
		}else{
			$return['import_swap_in_out_details'] = $imposrtfileres;
		}
		//echo "<pre>"; print_r($empleavesql); print_r($empleavelist);
		// $empsql = "SELECT id,display_name,ref_emp_code FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		// $empres=$callConfig->getAllRows($empsql);
		// foreach($empres as $empress){
		// 	$emplist[$empress->ref_emp_code] = $empress;
		// }
		// $importinoutsql = "SELECT id,file_name,start_date,end_date FROM import_swap_in_outs WHERE company_id = '".$get['company_id']."'  AND is_active = 0";
		// $return['importinoutsql'] = $importinoutsql;



		return $return;
	}
	function insertTempImportSwapInOut($get){
		global $callConfig;
		$sql = "SELECT `column1`, `column2`, `column3`, `column4`, `column5`, `column6`, `column7`, `column8`, `column9`, `column10`, `column11`, `column12`, `column13`, `column14`, `column15`, `column16`, `column17`, `column18`, `column19`, `column20`, `column21`, `column22`, `column23`, `column24`, `column25`, `column26`, `column27`, `column28`, `column29`, `column30`, `column31`, `column32`, `column33`, `column34`, `column35`, `column36`, `column37`, `column38`, `column39`, `column40`, `column41`, `column42`, `column43`, `column44`, `column45`, `column46`, `column47`, `column48`, `column49`, `column50`, `column51`, `column52`, `column53`, `column54`, `column55`, `column56`, `column57`, `column58`, `column59`, `column60`, `column61`, `column62`, `column63`, `column64`, `column65`, `column66`, `column67`, `column68`, `column69` FROM import_swap_in_out_details WHERE company_id = '".$get['company_id']."' AND import_swap_in_out_id = '".$get['rid']."' AND is_active = 0";
			$res=$callConfig->getAllRows($sql);
			$empsql = "SELECT id,display_name,ref_emp_code FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$empres=$callConfig->getAllRows($empsql);
			foreach($empres as $empress){
				$emplist[$empress->ref_emp_code] = $empress;
			}
			//$return['get'] = $get;
			$importinoutsql = "SELECT id,file_name,start_date,end_date FROM import_swap_in_outs WHERE company_id = '".$get['company_id']."'  AND is_active = 0";
			//$return['importinoutsql'] = $importinoutsql;
			$importinoutres=$callConfig->getRow($importinoutsql);
			$departmentwithemp = array();
			$deaprtname = "";
			$empnamed = "";
			$return['res'] = $sql;
				foreach($res as $rows){
					if(strpos(strtoupper(trim($rows->column1)), strtoupper("Department")) !== false) {
						$deaprtname = str_replace('|', '_', str_replace(' ', '_', strtolower(trim($rows->column4))));
					}else{
						if($deaprtname!="" AND $rows->column1 !=""){
							if(strpos(strtoupper(trim($rows->column1)), strtoupper("Employee")) !== false) {
								$explode = explode(":", $rows->column4);
								$empnamed = str_replace(' ', '_', strtoupper(trim($explode[0])));
								$displayempnamed = strtolower(trim($explode[1]));
							}
							if($empnamed!="" AND $rows->column1 !=""){
								$departmentwithemp[$empnamed][] = $rows;
								//$departmentwithemp[$empnamed] = $rows;
							}
						}
					}	
				}
				$return['total_emp_record_excel'] = count((array)$departmentwithemp);
				$employerowdetails = array('0'=>'Employee','1'=>'Status','2'=>'InTime','3'=>'OutTime','4'=>'Duration','5'=>'Late_By','6'=>'Early_by','7'=>'OT');
				$dateaarray = array('01'=>'column3','02'=>'column4','03'=>'column6','04'=>'column7','05'=>'column8','06'=>'column9','07'=>'column11','08'=>'column12','09'=>'column14','10'=>'column15','11'=>'column16','12'=>'column17','13'=>'column18','14'=>'column19','15'=>'column20','16'=>'column21','17'=>'column22','18'=>'column23','19'=>'column24','20'=>'column25','21'=>'column16','22'=>'column27','23'=>'column28','24'=>'column29','25'=>'column30','26'=>'column32','27'=>'column33','28'=>'column34','29'=>'column35','30'=>'column36','31'=>'column37');
				//echo "<pre>"; //print_r($departmentwithemp); echo "</pre>";
			$notrefcode = array();
			$insdailyattendancedetails = "INSERT INTO `temp_attendance_details`(`company_id`, `employee_id`, `attendance_date`, `attendance_key_short_code`, `clock_in`, `clock_out`, `is_active`, `current_status`,`created_date`, `created_by`) VALUES ";
			$insertattendanceempcount = 0;
			$notinsertattendanceempcount = 0;
			$all_emp = $insert_emp = $not_insert_emp = array();
			foreach ($departmentwithemp as $empcodekey => $value){
				$empid = $emplist[$empcodekey]->id;	
				$all_emp[$empcodekey] = $empcodekey;
				if(trim($empid) !=""){
					$attendanceempcount++;
					$insert_emp[$empcodekey] = $empcodekey;
					$start = new DateTime($importinoutres->start_date);
					$end = new DateTime($importinoutres->end_date);
					while($start <= $end){
						$date = $start->format('Y-m-d');
						$dy = $start->format('d');
						$dateaarray1 = $dateaarray[$dy];
						$intime = $value['2']->$dateaarray1;
						$outtime = $value['3']->$dateaarray1;
						if($value['1']->$dateaarray1 == 'P' AND $intime !="" AND $outtime  !=""){
							//echo "<span style='".$bgcolor."'>".$empcodekey." - ".trim($emplist[$empcodekey]->display_name)."- empid - ".$empid."- ".$date." -- ".$value['1']->$dateaarray1." -- ".$value['2']->$dateaarray1." -- ".$value['3']->$dateaarray1." -- ".$value['4']->$dateaarray1." -- ".$value['5']->$dateaarray1." -- ".$value['6']->$dateaarray1."</span></br>";	
							$insdailyattendancedetails .= "('".$get['company_id']."', '".$empid."', '".$date."','3','".$intime."','".$outtime."','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
						}else{
							$insdailyattendancedetails .= "('".$get['company_id']."', '".$empid."', '".$date."','1','".$intime."','".$outtime."','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
						}
						$start->modify('+1 days');
					}
				}else{
					$notinsertattendanceempcount++;
					$not_insert_emp[$empcodekey] = $empcodekey;
				}
			}	
			//echo $insdailyattendancedetails;
			$updaterecord = array('all_emp'=>json_encode((array)$all_emp),'insert_emp'=>json_encode((array)$insert_emp),'not_insert_emp'=>json_encode((array)$not_insert_emp));
			$return['updateisio'][] = $callConfig->updateRecord('import_swap_in_outs',$updaterecord,'id',$get['rid']);

			$return['insert_temp_attendance_emp_count'] = $attendanceempcount;
			$return['not_insert_temp_attendance_emp_count'] = $notinsertattendanceempcount;
			$insdailyattendancedetails = substr($insdailyattendancedetails, 0, -1);
			$insdailyattendancedetails .= " ON DUPLICATE KEY UPDATE clock_in = VALUES(clock_in),clock_out = VALUES(clock_out),attendance_key_short_code = VALUES(attendance_key_short_code)";
			$return['employee_daily_attendance_details_sql'] =  $insdailyattendancedetails;
			$res1 = $callConfig->executeQueryString($insdailyattendancedetails);
			$return['employee_daily_attendance_details'] =  $res1;
			//echo "<pre>";print_r($return);
			return $return;
	}


	function insertWithOutOffTempAttendanceDetails($get,$post){
		global $callConfig;
		$return['post'] = $post;
		$sql = "SELECT `column1`, `column2`, `column3`, `column4`, `column5`, `column6`, `column7`, `column8`, `column9`, `column10`, `column11`, `column12`, `column13`, `column14`, `column15`, `column16`, `column17`, `column18`, `column19`, `column20`, `column21`, `column22`, `column23`, `column24`, `column25`, `column26`, `column27`, `column28`, `column29`, `column30`, `column31`, `column32`, `column33`, `column34`, `column35`, `column36`, `column37`, `column38`, `column39`, `column40`, `column41`, `column42`, `column43`, `column44`, `column45`, `column46`, `column47`, `column48`, `column49`, `column50`, `column51`, `column52`, `column53`, `column54`, `column55`, `column56`, `column57`, `column58`, `column59`, `column60`, `column61`, `column62`, `column63`, `column64`, `column65`, `column66`, `column67`, `column68`, `column69` FROM import_swap_in_out_details WHERE company_id = '".$get['company_id']."' AND import_swap_in_out_id = '".$post['rid']."' AND is_active = 0";
		$imposrtfileres=$callConfig->getAllRows($sql);
		$return['import_swap_in_out_details'] = $imposrtfileres;
		$empdetails = $dates = $empid =  array();
		$notdates = array('column1','column2','column3');
		$notleaveattendancekey = array('P','R','A','H','O','F','Z');
		foreach($imposrtfileres[0] as $keystr=>$getdate){
			if(!in_array($keystr, $notdates)){
				if($getdate !=""){
					$dates[$keystr] = $getdate;
				}
			}
		}
		unset($imposrtfileres[0]);
		foreach($imposrtfileres as $keystr=>$getdate){
			$empid[$getdate->column2] = $getdate->column2;
		}
		$empids =  implode(",",$empid); 
		$empleavesql = "SELECT  elb.employee_id,  elb.leave_type ,lt.leave_category_id, akt.key_short_code FROM emp_leave_balances elb, leave_types lt, leave_categories lc, attendance_key_types akt WHERE elb.company_id = '".$post['company_id']."' AND elb.company_id = lt.company_id AND elb.company_id = lc.company_id AND elb.company_id = akt.company_id AND elb.leave_type = lt.id AND lt.leave_category_id = lc.id AND lc.id = akt.leave_category_id AND employee_id IN(".$empids.") AND elb.is_active = lt.is_active AND elb.is_active = lc.is_active AND elb.is_active = akt.is_active AND elb.is_active = '0' GROUP BY employee_id, leave_type";
		$empleaveres=$callConfig->getAllRows($empleavesql);
		foreach($empleaveres as $empress){
			$empleavelist[$empress->employee_id][$empress->key_short_code] = $empress->leave_type;
		}
		$return['empleavelist'] =  $empleavelist;
		$return['dates'] =  $dates;
		
		$employee_terminationssql = "SELECT id,emp_id, employee_leaving_date FROM employee_terminations WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$employee_terminationsres=$callConfig->getAllRows($employee_terminationssql);
		$employee_terminations = array();
		foreach($employee_terminationsres as $empterm){
			$employee_terminations[$empterm->emp_id] = $empterm->employee_leaving_date;
		}


		$insdailyattendancedetails = "INSERT INTO `temp_attendance_details`(`company_id`, `employee_id`, `attendance_date`,`leave_type_id`,`attendance_key_short_code`, `leave_from`, `leave_to`, `leave_days`, `start_half_day`, `end_half_day`, `is_active`, `current_status`,`created_date`, `created_by`,`clock_in`,`clock_out`) VALUES ";
		foreach($imposrtfileres as $importres){
			$empid[$importres->column2] = $importres->column2;
		    $offarray = $sundaycnt =$pressentcnt = $absenttcnt= $total_day  =$leaveclcnt =$leaveelcnt=$leavemlcnt =$leaveslcnt =$leavewpcnt =$leavecocnt = $leaveplcnt = $leavemrcnt= 0;
		    foreach($dates as $field=>$dateval){
		    	$total_day++;
		    	$sunday = date("D",strtotime($dateval));
		    	$attendance_date = date("Y-m-d",strtotime($dateval));
		    	if($employee_terminations[$importres->column2] !="" AND strtotime($employee_terminations[$importres->column2]) <= strtotime($dateval)){
		    		$insdailyattendancedetails .= "('".$post['company_id']."', '".$importres->column2."', '".$attendance_date."','','A','','','','','','0','0', '".date("Y-m-d H:i:s")."', '".$post['user_id']."','',''),";
    			
    			}elseif(!in_array($importres->$field, $notleaveattendancekey)){  // leave insert
		    		$leave_type_id = $empleavelist[$importres->column2][$importres->$field];
		    		$return['column2'][] =  $importres->column2;
		    		$return['field'][] =  $importres->$field;
		    		$return['leave_type_id'][] =  $leave_type_id;
					$insdailyattendancedetails .= "('".$post['company_id']."', '".$importres->column2."', '".$attendance_date."','".$leave_type_id."','".$importres->$field."','".$attendance_date."','".$attendance_date."','1','1','1','0','0', '".date("Y-m-d H:i:s")."', '".$post['user_id']."','',''),";
					$return['leave_type_id_sql'][] =  $insdailyattendancedetails;
					$return['leave_insert'][] = $importres->column2." -- ".$importres->$field;
				}else{ // normal insert

					if($importres->$field == "F"){
						$leave_type_id = $empleavelist[$importres->column2]['C'];
			    		$return['column2'][] =  $importres->column2;
			    		$return['field'][] =  "C";
			    		$return['leave_type_id'][] =  $leave_type_id;
						$insdailyattendancedetails .= "('".$post['company_id']."', '".$importres->column2."', '".$attendance_date."','".$leave_type_id."','C','".$attendance_date."','".$attendance_date."','1','0','1','0','0', '".date("Y-m-d H:i:s")."', '".$post['user_id']."','',''),";
						$return['leave_type_id_sql'][] =  $insdailyattendancedetails;
						$return['leave_with_half_day_insert'][] = $importres->column2." -- ".$importres->$field;
					
					}elseif($importres->$field == "Z"){
					
						$insdailyattendancedetails .= "('".$post['company_id']."', '".$importres->column2."', '".$attendance_date."','','".$importres->$field."','','','','','','0','0', '".date("Y-m-d H:i:s")."', '".$post['user_id']."','10:00:00','14:00:00'),";
						//$return['all_insert'][] = $importres->column2." -- ".$importres->$field;
					}else{
						$insdailyattendancedetails .= "('".$post['company_id']."', '".$importres->column2."', '".$attendance_date."','','".$importres->$field."','','','','','','0','0', '".date("Y-m-d H:i:s")."', '".$post['user_id']."','',''),";
						$return['all_insert'][] = $importres->column2." -- ".$importres->$field;
					}
					
				}
		    }
		}
		$insdailyattendancedetails = substr($insdailyattendancedetails, 0, -1);
		$insdailyattendancedetails .= " ON DUPLICATE KEY UPDATE attendance_key_short_code = '".$importres->$field."'";
		$return['employee_daily_attendance_details_sql'] =  $insdailyattendancedetails;
		$res1 = $callConfig->executeQueryString($insdailyattendancedetails);
		$insertuserarray2=array('company_id'=>$post['company_id'],'import_swap_in_out_id'=>$post['rid'],'month_year'=>$post['month_year'],'type'=>'R','user_id'=>$post['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
		$tapres = $callConfig->insertRecord('work_attendance_logs',$insertuserarray2);
		$return['work_attendance_logs'] = $tapres;
		return $return;
	}
	function togetlocationemployeeattendance($get){
		global $callConfig;
		//$return = $get;
		$query = "SELECT location_id, id, display_name FROM employees WHERE id=".$get['empid'];
		$edata = $callConfig->getRow($query);
		$return['employee_data'] = $edata;
		$query = "SELECT location_name FROM locations WHERE id =".$edata->location_id;
		$return['location_data'] = $callConfig->getRow($query);
		$query = "SELECT * FROM attendance_settings WHERE location_id =".$edata->location_id." AND is_active =0";
		$return['attendance_data'] = $callConfig->getRow($query);
		$query = "SELECT * FROM shifts WHERE is_active =0";
		$return['shifts'] = $callConfig->getAllRows($query);
		if(count((array)$return['shifts'])>0){
			$query = "SELECT * FROM shift_settings WHERE is_active =0";
			$shift_settings = $callConfig->getAllRows($query);
			foreach((array)$shift_settings as $shift_setting){
				$shiftset[$shift_setting->id] = $shift_setting;
			}
			$return['shift_settings'] = $shiftset;
		}
		//echo "<pre>";
		return $return;
	}
	function saveshiftemployee($get,$post){
		global $callConfig;
		$query = "UPDATE emp_shifts SET is_active =1 WHERE emp_id=".$post['newempid'];
		$res = $callConfig->executeQuery($query);
		$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$post['newempid'],'shift_id'=>$post['shift_id'],'shift_work_day_id'=>$post['shift_setting_id'],'shift_from'=>date('Y-m-d'),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);  
		$res = $callConfig->insertRecord('emp_shifts',$insert_array);
		return $res;
	}
	function employeeleaveapplyattendance($get){
		global $callConfig;
		$query = "SELECT  id, display_name, department_id, designation_id, location_id, gender, employee_type FROM employees WHERE id=".$get['empid'];
		$empdata = $callConfig->getRow($query);

		$query = "SELECT * FROM `leave_types` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$leavedatas = $callConfig->getAllRows($query);

		$query = "SELECT * FROM `leave_type_levels` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$subleavedatas = $callConfig->getAllRows($query);
		foreach($subleavedatas as $subleavedat){
			$sunmain[$subleavedat->leave_type_id] = (array)$subleavedat;
		}

		$query = "SELECT * FROM `filter_tables` WHERE company_id = ".$get['company_id']." AND  `application_tablename` = 'leave_types' AND is_active = 0";
		$filtdata = $callConfig->getAllRows($query);
		foreach($filtdata as $fltdata){
			$fdata[$fltdata->transaction_id] = (array)$fltdata;
		}
		//echo "<pre>"; print_r($fdata);  exit;
		foreach($leavedatas as $leavedata){
			$cond = array();
			$lscond = array();
			$checks = $fdata[$leavedata->id];
			$listcond = $checks['filter_condition'];
			//echo "<pre>".$listcond; print_r($fdata); exit;
			$divdconds = explode("&",$listcond);
			//echo "<pre>".$listcond; print_r($divdconds); exit;
			foreach($divdconds as $divdcond){
				$codd = explode("=",$divdcond);
				$lscond[$codd['0']] = $codd['1'];
				$cond[]= $lscond;
			}
			//echo "<pre>"; print_r($cond); exit;
			foreach($cond as $wcond){
				//print_r($wcond); echo "----".count((array)$cond); exit;
				//echo count((array)$cond)."-----".$wcond['check']."======"; //exit;
				if(count((array)$cond)==1 && isset($wcond['check']) && $wcond['check']==1 ){
					//echo "test 1";
					$maindata[] = $leavedata;
					break;
				}else{
					//echo "test 2";
					if( isset($wcond['department_id']) && $wcond['department_id'] != "" && $wcond['department_id'] == $empdata->department_id ){
						$maindata[] = $leavedata;
						break;
					}
					if( isset($wcond['designation_id']) && $wcond['designation_id'] != "" && $wcond['designation_id'] == $empdata->designation_id ){
						$maindata[] = $leavedata;
						break;
					}
					if( isset($wcond['location_id']) && $wcond['location_id'] != "" && $wcond['location_id'] == $empdata->location_id ){
						$maindata[] = $leavedata;
						break;
					}
					if( isset($wcond['gender']) && $wcond['gender'] != "" && $wcond['gender'] == $empdata->gender ){
						$maindata[] = $leavedata;
						break;
					}
					if( isset($wcond['employee_type']) && $wcond['employee_type'] != "" && $wcond['employee_type'] == $empdata->employee_type ){
						$maindata[] = $leavedata;
						break;
					}
				}
			}
			
		}
		return array('main'=>$maindata,'sub'=>$sunmain);
	}
	function togetuserrolelist($get){
		global $callConfig;
		$query = "SELECT * FROM `application_users` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$return['users'] = $callConfig->getAllRows($query);
		$query = "SELECT * FROM `application_roles` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$return['roles'] = $callConfig->getAllRows($query);
		return $return;
	}
	function togetpayadminpayrealist($get){
		global $callConfig;
		$query = "SELECT * FROM `payroll_admins` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$return['payroll_admins'] = $callConfig->getAllRows($query);
		$query = "SELECT * FROM `pay_areas` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$return['pay_areas'] = $callConfig->getAllRows($query);
		return $return;
	}
	function tocheckpayrolldetailsfull($post){
		global $callConfig;
		$ret = array();
		$checkavlsql = "SELECT id FROM `payroll_steps` WHERE company_id = '".$post['company_id']."' AND category_id = ".$post['payroll_steps_cat']." AND pay_month = '".$post['pay_month']."' AND pay_year = '".$post['pay_year']."' AND pay_area_id = '".$post['pay_area_id']."' AND is_active =0";
		//echo $checkavlsql; exit;
		$check = $callConfig->getRow($checkavlsql);
		$ret['check'] =$check;
		if(!isset($check->id) && $check->id==""){
			$insert_array = array('company_id'=>$post['company_id'],'current_status'=>'0','is_active'=>'0','category_id'=>$post['payroll_steps_cat'],'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'], 'pay_area_id'=>$post['pay_area_id']);
			$insert = $callConfig->insertRecord('payroll_steps',$insert_array);
			$ret['insert'] =$insert;
			$ret['insert_array'] =$insert_array;
		}
		//echo "<pre>"; print_r($ret); exit;
		return $ret;
	}
	function togetnewinsertemloyeedetails($get){
		global $callConfig;
		$query = "SELECT * FROM `employees` WHERE company_id = ".$get['company_id']." AND id= ".$get['empid']." AND is_active = 0";
		$data = $callConfig->getRow($query);
		$return['employee'] = $data;
		$query = "SELECT * FROM `salary_template` WHERE company_id = ".$get['company_id']." AND employee_id= ".$get['empid']." AND is_active = 0";
		$return['salary'] = $callConfig->getRow($query);
		$query = "SELECT * FROM `emp_leave_balances` WHERE company_id = ".$get['company_id']." AND employee_id= ".$get['empid']." AND is_active = 0";
		$return['employee_balance'] = $callConfig->getRow($query);
		$return['application_users'] = array();
		if($data->user_id!=""){
			$query = "SELECT * FROM `application_users` WHERE company_id = ".$get['company_id']." AND id= ".$data->user_id." AND is_active = 0";
			$return['application_users'] = $callConfig->getRow($query);
		}
		$query = "SELECT * FROM `pay_area_employees` WHERE company_id = ".$get['company_id']." AND emp_id= ".$get['empid']." AND is_active = 0";
		$return['pay_area_employees'] = $callConfig->getRow($query);
		return $return;
	}
	function holdpayroll_exclusions($get,$post){
		global $callConfig;
	    //print_r($post); echo "ravi"; exit;
	    $hquery = 'SELECT id,is_active FROM payroll_exclusions WHERE company_id = '.$get['company_id'].'  AND emp_id='.$post['emp_id'].' AND pay_year='.$post['pay_year'].' AND category_id ='.$post['category_id'].' AND pay_month='.$post['pay_month'].' AND pay_area_id='.$post['pay_area_id'].' AND is_active =0';
	    //echo $hquery;
		$hudata = $callConfig->getRow($hquery);
		$return['hquery'] = $hquery;
		$return['hudata'] = $hudata;
		if($hudata->id !=""){ 
			$res=$callConfig->updateRecord('payroll_exclusions', array('is_active'=>1),'id',$hudata->id);
		}else{ 
			$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'],'category_id'=>$post['category_id'],'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'pay_area_id'=>$post['pay_area_id']);
			$return['insert_array'] = $insert_array;
			//	print_r($insert_array);// exit;
			$res = $callConfig->insertRecord('payroll_exclusions',$insert_array);
	        // echo $res;
		}
		return $return;
	}
	function insertpayrollexcludd($get,$post){
		global $callConfig;
		$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'],'category_id'=>$post['category_id'],'pay_year'=>$post['pay_year'],'pay_month'=>$post['pay_month'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'pay_area_id'=>$post['pay_area_id']);
		$return['insert_array'] = $insert_array;
			//	print_r($insert_array);// exit;
		$res = $callConfig->insertRecord('payroll_exclusions',$insert_array);
		$hquery = $callConfig->selectQuery('employees','id,emp_code, display_name, user_id','id='.$post['emp_id'],'','','');
		$hudata = $callConfig->getRow($hquery);
		return $hudata;
	}
	function toinsertpayrollexcuinedata($get,$returns_data,$cat){
		global $callConfig;
		$returns = (array)$returns_data;
		foreach ($returns as  $key=>$value) {
			$value = (array)$value;
			$list_return[$key] = $value;
			$hquery = $callConfig->selectQuery('payroll_exclusions','id','emp_id='.$value['emp_id'].' AND category_id = '.$cat.' AND pay_year='.$get['pay_year'].' AND pay_month='.$get['pay_month'].' AND pay_area_id='.$get['pay_area_id'],'','','');
			$hudata = $callConfig->getRow($hquery);
			if($hudata->id ==""){ 
				$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$value['emp_id'],'category_id'=>$cat,'pay_year'=>$get['pay_year'],'pay_month'=>$get['pay_month'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'pay_area_id'=>$get['pay_area_id']);
				$res = $callConfig->insertRecord('payroll_exclusions',$insert_array);
			}
		}
		return $res;
	}
	
	function togetpayrollstepsdetails($get){
		global $callConfig;
		//echo "<pre>"; print_r($get); exit;
		$get['payroll_steps_cat'] = 1;
		$start = $this->tocheckpayrolldetailsfull($get);
		$dates = $this->payrollfromtodates($get); 
		$date1 = $dates['date1'];
		$date2 = $dates['date2'];
		//echo "<pre>"; print_r($dates); exit;
		/*---------------- Step 1 Start------*/
		$query ="SELECT emp.id emp_id, emp.emp_code, emp.display_name as name,emp.image,emp.bank_id,emp.bank_link,emp.bank_account_no,emp.user_id,emp.pan_number,st.salary_break_up, if(emp.bank_account_no = '', 1, 0) as bank, st.joining_bonus_applicable, st.joining_bonus_amount  FROM employees emp, salary_template st, pay_area_employees pae WHERE emp.company_id = st.company_id AND emp.company_id = pae.company_id  AND emp.company_id = ".$get['company_id']." AND  emp.id = st.employee_id AND pae.emp_id = emp.id AND  emp.employee_type =1 AND pae.pay_area_id = ".$get['pay_area_id']." AND  emp.joining_date >= '".$date1."' AND  emp.joining_date <= '".$date2."' AND emp.is_active =0 AND st.is_active=0";
		$detals['step1']['query'] = $query;
		
		$returns_data = $callConfig->getAllRows($query);
		$detals['step1']['res'] =$this->toinsertpayrollexcuinedata($get, $returns_data, 1);
		$detals['step1']['data'] = $returns_data;
		

		/*---------------- Step 1 End------*/
		/*---------------- Step 2 Start------*/
		$query = "SELECT et.emp_id emp_id,emp.emp_code,  et.settle_on, emp.display_name, emp.image, emp.bank_id, emp.bank_link, emp.bank_account_no, emp.user_id from employee_terminations et,pay_area_employees pae, employees emp where et.company_id = pae.company_id AND et.company_id = emp.company_id AND et.company_id = ".$get['company_id']." AND pae.emp_id = et.emp_id AND emp.id = et.emp_id AND pae.pay_area_id = 1 AND et.releive_date <= '".$date2."' AND et.releive_date >= '".$date1."' AND pae.is_active = 0 AND et.is_active=0";
		
		//echo $query; exit;
		$empexitdata = $callConfig->getAllRows($query);
		$detals['step2']['res'] =$this->toinsertpayrollexcuinedata($get, $empexitdata, 2);
		$detals['step2']['data'] = $empexitdata;
		$detals['step2']['query'] = $query;

		/*---------------- Step 2 End------*/
		/*---------------- Step 3 Start------*/
		$hquery = $callConfig->selectQuery('payroll_exclusions','id, emp_id','category_id = 3 AND pay_year='.$get['pay_year'].' AND pay_month='.$get['pay_month'].' AND pay_area_id='.$get['pay_area_id'],'','','');
		$detals['step3']['data'] = $callConfig->getAllRows($hquery);
		$detals['step3']['query'] = $hquery;

		/*---------------- Step 3 End------*/
		/*---------------- Step 4 Start------*/
		$query = "SELECT et.emp_id emp_id,emp.emp_code,  et.settle_on, emp.display_name, emp.image, emp.bank_id, emp.bank_link, emp.bank_account_no, emp.user_id from employee_terminations et,pay_area_employees pae, employees emp where et.company_id = pae.company_id AND et.company_id = emp.company_id AND et.company_id = ".$get['company_id']." AND pae.emp_id = et.emp_id AND emp.id = et.emp_id AND pae.pay_area_id = 1 AND et.settle_on <= '".$date2."' AND et.settle_on >= '".$date1."' AND pae.is_active = 0 AND et.is_active=0";
		
		//echo $query; exit;
		$empsateldata = $callConfig->getAllRows($query);
		$detals['step4']['res'] =$this->toinsertpayrollexcuinedata($get, $empsateldata, 4);
		$detals['step4']['data'] = $empsateldata;	
		$detals['step4']['query'] = $query;	

		/*---------------- Step 4 End------*/
		/*---------------- Step 5 Start------*/
		$query = "SELECT emp.id  emp_id,emp.emp_code,  et.affected_date, emp.display_name, emp.image, emp.bank_id, emp.bank_link, emp.bank_account_no, emp.user_id, et.ctc_yearly from salary_template et,pay_area_employees pae, employees emp where et.company_id = pae.company_id AND et.company_id = emp.company_id AND et.company_id = ".$get['company_id']." AND pae.emp_id = et.employee_id  AND emp.id = et.employee_id  AND pae.pay_area_id = 1 AND et.affected_date <= '".$date2."' AND et.affected_date >= '".$date1."' AND pae.is_active = 0 AND emp.joining_date < '".$date1."'   AND et.is_active=0";
		
		//echo $query; exit;
		$empsateldata = $callConfig->getAllRows($query);
		$detals['step5']['res'] =$this->toinsertpayrollexcuinedata($get, $empsateldata, 5);
		$detals['step5']['data'] = $empsateldata;	
		$detals['step5']['query'] = $query;
			
		/*---------------- Step 5 End------*/
		/*---------------- Step 6 Start------*/
		/*---------------- Step 6 End------*/
		/*---------------- Step 8 Start------*/
			$hquery = $callConfig->selectQuery('payroll_exclusions','*','category_id = 8 AND pay_year='.$get['pay_year'].' AND pay_month='.$get['pay_month'].' AND pay_area_id='.$get['pay_area_id'].' AND is_active = 0','','','');
			$detals['step8']['data'] = $callConfig->getAllRows($hquery);
			$detals['step8']['query'] = $hquery;
		/*---------------- Step 8 End------*/
		$query = "SELECT id, emp_code, emp_code,  display_name, user_id FROM employees WHERE company_id = ".$get['company_id']." AND employee_type = 1 AND is_active = 0";
		$detals['employes'] = $callConfig->getAllRows($query);

		$query = 'SELECT * FROM payroll_steps WHERE company_id = '.$get['company_id'].' AND pay_year='.$get['pay_year'].' AND pay_month='.$get['pay_month'].' AND pay_area_id='.$get['pay_area_id'].' AND is_active = 0';
		$detals['payroll_steps'] = $callConfig->getAllRows($query);
		$get['type'] = 'earnings';
		$detals['payroll_update_one_time_earnings'] = $this->togetupdateontimeearinings($get);
		$get['type'] = 'deductions';
		$detals['payroll_update_one_time_deductions'] =  $this->togetupdateontimeearinings($get);
		return $detals;

	}
	/*function  tocreateprocesstaskintotaskdatail($get){
		global $callConfig;
		$query = "SELECT * FROM process_task_settings WHERE company_id = ".$get['company_id']."  AND process_type = '".$get['process_type']."' AND is_active = 0";
		$return['process_query'] = $query;
		$tasks = $callConfig->getAllRows($query);
		$return['process_data'] = $tasks;
		$table_array = array('VR'=>'vendors');
		$user_array = array('hr_connect', 'finance_connect', 'admin_connect','project_connect', 'location_connect', 'esop_connect', 'legal_connect');
		if(count((array)$tasks)>0){

			$module_query = "SELECT module_name,module_description FROM module_setup WHERE module_id = ".$get['module_group_id'];
			$module = $callConfig->getRow($module_query);
			$return['module_query'] = $module_query;
			$return['module_data'] = $module;
			$work_spqusery = "SELECT id FROM project_work_spaces WHERE company_id = '".$get['company_id']."' AND  name = '".$module->module_name."' AND is_active = 0";
			$work_spaces = $callConfig->getRow($work_spqusery);
			$return['work_spaces_query'] = $work_spqusery;
			$return['work_spaces_data'] = $work_spaces;
			if($work_spaces->id!=""){
				$work_spaces_id = $work_spaces->id;
			}else{
				$insert_array = array('company_id'=>$get['company_id'],'name'=>$module->module_name, 'is_active'=>0,'current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
				$work_res=$callConfig->insertRecord('project_work_spaces',$insert_array);
				$work_spaces_id = $work_res['LASTID'];
			}
			$return['work_spaces_id'] = $work_spaces_id;
			foreach($tasks as $task){
				$project_query = "SELECT id FROM projects WHERE company_id = '".$get['company_id']."' AND project_work_space_id = ".$work_spaces_id." AND  project_name = '".$task->task_name."' AND is_active = 0";
				$pdata = $callConfig->getRow($project_query);
				$return['project_query'] = $project_query;
				$return['project_data'] = $pdata;
				if($pdata->id!=""){
					$project_id = $work_spaces->id;
				}else{
					$insert_array = array('company_id'=>$get['company_id'],'project_work_space_id'=>$work_spaces_id,'project_name'=>$task->task_name, 'project_type'=>2, 'planned_start'=>date('Y-m-d') ,'is_active'=>0,'current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
					$project_res=$callConfig->insertRecord('projects',$insert_array);
					$project_id = $project_res['LASTID'];
					//$table_name = $table_array[$task->process_type];
				}
				$return['project_id'] = $project_id;
				$team_query = "SELECT id, user_id FROM project_teams WHERE company_id = '".$get['company_id']."' AND project_id = ".$project_id." AND is_active = 0";
				$listteams = $callConfig->getAllRows($team_query);
				$teams = array();
				if(count($listteams)>0){
					foreach((array)$listteams as $listteam){
						$teams[] = $listteam->user_id;
					}
				}
				$return['project_old_team'] = $teams;
				$userquery = "SELECT * FROM application_users WHERE company_id = '".$get['company_id']."' AND id = ".$get['transaction_id']." AND is_active = 0";
					$userdata = $callConfig->getRow($userquery);
					$new_teams = array();
				foreach((array)$user_array as $user_filed){
					$userlist_members[$user_filed] = $userdata->$user_filed;
					if($userdata->$user_filed !="" && !in_array($userdata->$user_filed,(array)$teams)){
						$new_teams[] = $userdata->$user_filed;
					}
				}
				$return['project_new_team'] = $new_teams;
				if(count((array)$new_teams)>0){
					foreach((array)$new_teams as $new_team){
						$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$project_id,'role_id'=>2, 'user_id'=>$new_team ,'is_active'=>0,'current_status'=>0,'created_by'=>$post['created_by'],'created_by'=>$post['created_by']);
						$project_teamres=$callConfig->insertRecord('project_teams',$insert_array);
					}
				}
				$taskdata = $get;
				$taskdata['project_id'] = $project_id;
				$taskdata['task_name'] = $task->task_name." - ".$userdata->display_name;
				$taskdata['assigner'] =  $get['user_id'];
				$taskdata['assigen_id'] =  $get['user_id'];
				$taskdata['checklist'] =  $task->check_list;
				$taskdata['checkuser'] = $userlist_members;
				$return['taskdata'] = $taskdata;
				$return['task'][] = $this->toaddprojecttaskdetailsbyproceetime($taskdata);
			}
		}
		return $return; 
	}
	function toaddprojecttaskdetailsbyproceetime($get){
		global $callConfig;		
		$notification_message = '';
		$notuser2 = array(); $notuser1 = array();
		$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$get['project_id'],'project_phase_id'=>2, 'task_type'=>2, 'task_name'=>$get['task_name'], 'planned_start'=>date('Y-m-d'), 'assigen_id'=>$get['assigner'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
		$res = $callConfig->insertRecord('project_task_details',$insert_array);
		$task_id = $res['LASTID'];	
		$return['task_id'] = $task_id;	
		$transaction_number = $get['project_id'];	
		$checklist = json_decode($get['checklist'],1);
		$check_user = $get['checkuser'];
		$fecharry = array('HB'=>'hr_connect','AB'=>'admin_connect','FB'=>'finance_connect','PB'=>'project_connect','LEGAL_BUDDY'=>'legal_connect','LB'=>'location_connect');
		if(count((array)$checklist)>0){
			foreach($checklist as $check){
				$insert_array = array('company_id'=>$get['company_id'],'	application_tablename'=>'project_task_details','transaction_id'=>$task_id, 'check_name'=>$check['name'], 'planned_start'=>date('Y-m-d'), 'assign_id'=>$get['transaction_id'],'approval_id'=>$check_user[$fecharry[$check['approval_to']]], 'ref_data'=> json_encode($check), 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
				$checkres = $callConfig->insertRecord('application_check_lists',$insert_array);
				$return['checkres'][] = $checkres;
			}
		}
		$whatsup = 'N';
	 	$assgnuser = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$get['user_id']);
		$msg1 = " Task ".$get['task_name']." is updated : Comments added  by ".$assgnuser->display_name;
		$msg2 = $msg1;
		$notuser1[] = $get['user_id'];
		$notuser1[] = $get['assigen_id'];
		if(!isset($get['activity_type'])){
			$project = $callConfig->getRow("SELECT project_name,assigner_id FROM projects WHERE id=".$get['project_id']);
			$transaction_number = $get['project_id'];
			$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$get['assigen_id']);
			$insert_arr =  array('company_id'=>$get['company_id'],'project_id'=>$get['project_id'],'project_task_id'=>$task_id, 'project_role_id'=>2,'user_id'=>$get['transaction_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$restwo = $callConfig->insertRecord('project_task_teams',$insert_arr);
			$insert_arr['project_role_id'] = 1;
			$insert_arr['user_id'] = $project->assigner_id;
			$restwo = $callConfig->insertRecord('project_task_teams',$insert_arr);
			$msg1 = "Task ".$get['task_name']." is assigned to you";
			$msg2 = "New task ".$get['task_name']." is created. Assign by ".$user->display_name;
			$query = "SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND role_id !=2 AND project_id = ".$get['project_id']."  AND is_active = 0";
			$lists = $callConfig->getAllRows($query);
			foreach($lists as $list){
				$notuser2[] = $list->user_id;
			}
		}	
		
		$email_content_id =37;
		$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$transaction_number, 'application_table_name'=>'project_task_details', 'type'=>'INFORMATION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_PROJECT_KANBAN&TAB=3&RID='.$transaction_number,'module_setup_id'=>25, 'whatsup'=>$whatsup);
		
		$ss = $this->tosendnotifiations($notdata1);
		//$rss = $this->tosendnotifiations($notdata2);
		$senddata = array('session_company_id'=>$get['company_id'],'email_content_id'=>$email_content_id, 'project_name'=>$project->project_name,'user_display_name'=>$user->display_name,'cc_users'=>$notuser2,'to_users'=>$notuser1,'task_name'=>$get['task_name'],'assigners_name'=>$assgnuser->display_name);
		$res = $this->emailsendmediater($senddata);
		return $return;
	}*/
	function insertcustomerinvitation($get,$post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		foreach($post['first_name'] as $key=>$value){
			if($post['user_email'][$key] !=""){
				$post['user_name'] = $post['first_name'][$key]." ".$post['last_name'][$key];
				$post['display_name'] = $post['first_name'][$key]." ".$post['last_name'][$key];
				$role_id =  $post['role_id'][$key]; 
				$password = $callConfig->passwordEncrypt('PensAndPencils@123');
				$applicationuserary = array('company_id'=>$post['company_id'],'user_name'=>$post['user_name'],'first_name'=>$post['first_name'][$key],'last_name'=>$post['last_name'][$key],'display_name'=>$post['display_name'],'user_email'=>$post['user_email'][$key],'user_mobile'=>$post['user_phone'][$key],'user_type'=>$post['user_type'][$key],'user_password'=>$password,'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'role_id'=>json_encode($post['role_id'][$key]));
				$res=$callConfig->insertRecord('application_users',$applicationuserary);
				$retresult['application_users_ary'] = $applicationuserary;
				$retresult['application_users'] = $res;
				if(isset($res['LASTID'])){
					$query = "SELECT application_module,id,role_short_code FROM application_roles where role_short_code IN('".$role_id."')";
					$roleres = $callConfig->getAllRows($query);
					$retresult['query'] = $query;
					$insertarray = array("company_id"=>$post['company_id'],"user_id"=>$res['LASTID'],"module_setup_id"=>$rolere->application_module,"application_module_id"=>$rolere->application_module,"role_id"=>$rolere->id,"role_short_code"=>$rolere->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
					$insert = $callConfig->insertRecord('application_user_module_roles',$insertarray);
					$rolesids[] = $rolere->role_short_code;	
					$user_module_id = $rolere->application_module;	
					
					$applicationuserinvoiteary = array('company_id'=>$post['company_id'],'user_name'=>$post['user_name'],'first_name'=>$post['first_name'][$key],'last_name'=>$post['last_name'][$key],'display_name'=>$post['display_name'],'user_email'=>$post['user_email'][$key],'user_mobile'=>$post['user_mobile'][$key],'user_type'=>$post['user_type'][$key],'user_id'=>$res['LASTID'],'role_id'=>json_encode($post['role_id'][$key]),'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
					$res1=$callConfig->insertRecord('application_user_invitations',$applicationuserinvoiteary);
					$customerarray = array('company_id'=>$post['company_id'],'first_name'=>$post['first_name'][$key],'last_name'=>$post['last_name'][$key],'display_name'=>$post['display_name'],'email	'=>$post['user_email'][$key],'phone	'=>$post['user_mobile'][$key],'user_id'=>$res['LASTID'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
					$customr_res=$callConfig->insertRecord('customers',$customerarray);

					$retresult['application_user_invitations'] = $res1;
					$emailarray['user_company_id'] = $post['company_id'];
					$emailarray['session_company_id'] = $post['company_id'];
					$emailarray['send_user_id'] = array($res['LASTID']);
					$emailarray['creater_user_id'] = array($post['user_id']);
					$emailarray['to_company_id'] = $post['company_id'];
					$emailarray['user_display_name'] = $post['display_name'];
					$emailarray['user_email_id'] = $post['user_email'][$key];
					$emailarray['user_phone_number'] = $post['user_mobile'][$key];
					$emailarray['default_module_id'] = $user_module_id;
					$emailarray['user_id'] =$res['LASTID'];
					$emailarray['created_by'] = $post['user_id'];
					$emailarray['user_email'] = $post['user_email'][$key];
					$emailarray['company_id'] = $post['company_id'];
					$emailarray['template_reference_id'] = 3;
					$emill = $this->NewUserformate($emailarray,$post['data_name']);
					$retresult['email'] = $emailarray;
				}
				
			}
		}
	}
	function togetprofessionaltaxexemptionsdetails($get){
		global $callConfig;
		$query = $callConfig->selectQuery('professional_tax_exemptions','*','company_id = '.$get['company_id'].' AND is_active=0','','','');
		$detals['exdata'] = $callConfig->getAllRows($query);
		$detals['emp_fileds'] = $this->listofTableFieldsByUsedTable('employees',$get['data_name']);
		return $detals;

	}
	function savedataforprofessionaltaxexemptions($get,$post){
		global $callConfig;
		if(isset($post['exemptions']) && count((array)$post['exemptions'])>0){
			$upquer = "UPDATE professional_tax_exemptions SET is_active=1 WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$ress = $callConfig->executeQuery($upquer);
			foreach($post['exemptions'] as $key=>$emption){
				$query = "SELECT * FROM professional_tax_exemptions WHERE id=".$key;
				$singledata = $callConfig->getRow($query);
				$updaterecord = array('company_id'=>$get['company_id'],'exemption_name'=>$emption,'exemption_code'=>$post['exemption_code'][$key],'employee_field_name'=>$post['employee_field_name'][$key], 'range_type'=>$post['range_type'][$key], 'minmum_range'=>$post['minmum_range'][$key], 'is_active'=>0);
				$res = $callConfig->updateRecord('professional_tax_exemptions',$updaterecord,'id',$key);
				$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>'professional_tax_exemptions','transaction_id'=>$key,'old_data'=>json_encode($singledata),'new_data'=>json_encode($updaterecord),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
				$res = $callConfig->insertRecord('application_table_histories',$insert_array);
			}
		}
		if(isset($post['newexemptions']) && count((array)$post['newexemptions'])>0){
			foreach($post['newexemptions'] as $key=>$emption){
				$insert_array = array('company_id'=>$get['company_id'],'exemption_name'=>$emption,'exemption_code'=>$post['newexemption_code'][$key],'employee_field_name'=>$post['newemployee_field_name'][$key],  'range_type'=>$post['newrange_type'][$key], 'minmum_range'=>$post['newminmum_range'][$key],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
				$res = $callConfig->insertRecord('professional_tax_exemptions',$insert_array);
			}

		}

	}
	function insertEmployeeTerminations($get,$post){
		global $callConfig;
		$inserttermination = array('company_id'=>$post['company_id'],'emp_id'=>$post['emp_id'],'termination_type'=>$post['termination_type'],'termination_reason'=>$post['termination_type'],'effective_date'=>$this->dateconvertdbformat($post['effective_date']),'employee_leaving_date'=>$this->dateconvertdbformat($post['effective_date']),'is_active'=>'0','current_status'=>'199','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
		$res=$callConfig->insertRecord('employee_terminations',$inserttermination);

		$updaterecord = array('date_of_leaving'=>$this->dateconvertdbformat($post['effective_date']));
		$res1 = $callConfig->updateRecord('employees',$updaterecord,'id',$post['emp_id'],);

		return $res;
	}
	function getholdsalrylist($get){
		global $callConfig;
		$return['get'] = $get;
		$whr = "";
		if($get['admin_type']!=1){
			$query = "SELECT pay_area_id FROM payroll_area_admins WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active = 0";
			$return['payroll_area_admins_query'] = $query;
			$payareardata = $callConfig->getAllRows($query);
			foreach((array)$payareardata as $areardata){
				$arearid[] =  $areardata->pay_area_id;
			}
			if(count((array)$arearid)==0){
				$arearid[] = 0;
			}
			$whr = "AND pe.pay_area_id IN(".implode(",", $arearid).")";
		}
		$query  = "SELECT pe.id as payroll_exclusion_id, pe.emp_id, pe.pay_area_id, pe.pay_year, pe.pay_month, pe.category_id, emp.emp_code, emp.display_name, emp.email_id, emp.image, pa.name pay_area_name FROM payroll_exclusions pe , employees emp, pay_areas pa WHERE pe.company_id = emp.company_id  AND pe.company_id = pa.company_id AND pe.company_id = ".$get['company_id']." AND pe.emp_id = emp.id AND pe.pay_area_id = pa.id AND pe.pay_year = ".$get['pay_year']." AND pe.pay_month = ".$get['pay_month']." AND pe.category_id = 8 ".$whr." AND pe.is_active = 0";
		$return['payroll_exclusion_query'] = $query;
		$return['data'] = $callConfig->getAllRows($query);
		return $return;
	}
	function togetversonyearitdecration($get){
		global $callConfig;
		$return['get'] = $get;
		$query = $callConfig->selectQuery('it_declaration_categories','*','company_id = '.$get['company_id'].' AND is_active=0','','','');
		$return['it_declaration_categories_query'] = $query; 
		$return['it_declaration_categories'] = $callConfig->getAllRows($query);

		$query = $callConfig->selectQuery('it_declaration','*','company_id = '.$get['company_id'].' AND is_active=0','','','');
		$return['it_declaration_query'] = $query;  
		$return['it_declaration'] = $callConfig->getAllRows($query);


		$query = "SELECT * FROM emp_it_declaration WHERE company_id = ".$get['company_id']." AND assessment_year = '".$get['assessment_year']."' AND version_no = ".$get['version_no']." AND emp_id = ".$get['empid']." AND is_active = 0";
		$return['emp_it_declaration_query'] = $query; 
		$return['emp_it_declaration'] = $callConfig->getRow($query);

		$query = "SELECT * FROM emp_it_declaration_80 WHERE company_id = ".$get['company_id']." AND assessment_year = '".$get['assessment_year']."' AND version_no = ".$get['version_no']." AND emp_id = ".$get['empid']." AND is_active = 0";
		$return['emp_it_declaration_80_query'] = $query; 
		$return['emp_it_declaration_80'] = $callConfig->getAllRows($query);

		$query = "SELECT * FROM emp_it_declaration_houserent WHERE company_id = ".$get['company_id']." AND assessment_year = '".$get['assessment_year']."' AND version_no = ".$get['version_no']." AND emp_id = ".$get['empid']." AND is_active = 0";
		$return['emp_it_declaration_houserent_query'] = $query; 
		$return['emp_it_declaration_houserent'] = $callConfig->getRow($query);

		$query = "SELECT * FROM emp_perevious_details WHERE company_id = ".$get['company_id']." AND assessment_year = '".$get['assessment_year']."' AND version_no = ".$get['version_no']." AND emp_id = ".$get['empid']." AND is_active = 0";
		$return['emp_perevious_details_query'] = $query; 
		$return['emp_perevious_details'] = $callConfig->getAllRows($query);	

		$query = "SELECT * FROM emp_it_declaration_other_income WHERE company_id = ".$get['company_id']." AND assessment_year = '".$get['assessment_year']."' AND version_no = ".$get['version_no']." AND emp_id = ".$get['empid']." AND is_active = 0";
		$return['emp_it_declaration_other_income_query'] = $query; 
		$return['emp_it_declaration_other_income'] = $callConfig->getAllRows($query);		

		return $return;
	}
	function tovmsobjothrsidecornjobvendors($get, $post){ 
	 	global $callConfig;
	 	$return['get'] = $get;
	 	$return['post'] = $post;
	 	$data = json_decode($post['data'],1);
	 	//print_r($return); exit;
	 	//print_r($get); exit;
	 	$insert_array = array('company_id'=>$get['company_id'], 'start_date_time'=>date('Y-m-d h:i:s'), 'command'=> 'cronjob/vms_add_vendors.php' , 'is_active'=>'0', 'current_status'=>'0', 'created_date'=>date('Y-m-d h:i:s'));
	 	$ccres = $callConfig->insertRecord('cron_jobs', $insert_array);
	 	$outarray = array('id', 'user_id', 'check_import', 'status_date', 'from_date', 'to_date', 'created_by', 'created_date', 'updated_by', 'updated_date', 'old_ref_id', 'vendor_import_date', 'vendor_import_details');
	 	foreach($data['details'] as $vendorfields ){
	 		$insert_array = array();
	 		$insert_array['old_ref_id'] = $vendorfields['id'];
	 		$insert_array['vendor_import_details'] = json_encode($get);
	 		foreach($vendorfields as $field=>$val){
	 			if(!in_array($field, $outarray)){
	 				$insert_array[$field] = $val;
	 			}
	 		}
	 		$res =  $callConfig->insertRecord('vendors',$insert_array);
	 		//print_r($insert_array);exit;
	 		$insertids[] = $res['LASTID'];
	 		$resids[] = $vendorfields['id'];
	 	}
	 	$return['resids'] = $resids;
	 	$return['insertids'] = $insertids;
	 	$updatequery = "UPDATE cron_jobs SET end_date_time = '".date('Y-m-d h:i:s')."', updated_date ='".date('Y-m-d h:i:s')."'";
	 	$uprrr = $callConfig->executeQuery($updatequery);
	 	return $return;
	}
	function getEmployeeLeaveBalanceReports($get){
		global $callConfig;
		$company_id = $get['company_id'];


		$query = "SELECT id, emp_code, display_name, email_id, joining_date FROM employees WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$employees = $callConfig->getAllRows($query);		
		$return['employees'] = $employees; 
		$filter_condition = "";
		if($get['employee_id_filter'] !=""){
			$filter_condition .= " AND e.id = '".$get['employee_id_filter']."'";
		}
		if($get['employee_filter'] !=""){
			$filter_condition .= " AND (e.display_name LIKE '%".$get['employee_filter']."%' OR e.emp_code LIKE '%".$get['employee_filter']."%')";
		}
		if($get['year_filter'] !=""){
			$filter_condition .= " AND YEAR(ela.transaction_date) = '".$get['year_filter']."'";
		}
	
		$query = "SELECT 
	    ela.employee_id,
	    e.display_name,
	    e.joining_date,
	    ela.leave_type,
	    (
	        SELECT 	leave_type_name
	        FROM 	leave_types lt
	        WHERE	lt.company_id = ela.company_id
	        AND		lt.id = ela.leave_type
	     )leave_type_name,
	     (
	         SELECT IFNULL(elb.applicable_leaves,0)- IFNULL(elb.consumed_leaves,0)
	         FROM 	emp_leave_balances elb
	         WHERE	elb.company_id = ela.company_id
	         AND	elb.employee_id = ela.employee_id
	         AND	elb.leave_type = ela.leave_type
	         AND	elb.blance_year < YEAR(ela.transaction_date)
	         ORDER BY elb.blance_year
	         LIMIT 1
	       )open_balance, 
	    YEAR(ela.transaction_date) AS year,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 1 THEN ela.applicable_leaves ELSE 0 END) AS Jan,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 2 THEN ela.applicable_leaves ELSE 0 END) AS Feb,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 3 THEN ela.applicable_leaves ELSE 0 END) AS Mar,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 4 THEN ela.applicable_leaves ELSE 0 END) AS Apr,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 5 THEN ela.applicable_leaves ELSE 0 END) AS May,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 6 THEN ela.applicable_leaves ELSE 0 END) AS Jun,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 7 THEN ela.applicable_leaves ELSE 0 END) AS Jul,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 8 THEN ela.applicable_leaves ELSE 0 END) AS Aug,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 9 THEN ela.applicable_leaves ELSE 0 END) AS Sep,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 10 THEN ela.applicable_leaves ELSE 0 END) AS Oct,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 11 THEN ela.applicable_leaves ELSE 0 END) AS Nov,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 12 THEN ela.applicable_leaves ELSE 0 END) AS 'Dec',
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 1 THEN ela.consumed_leaves ELSE 0 END) AS Jan_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 2 THEN ela.applicable_leaves ELSE 0 END) AS Feb_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 3 THEN ela.consumed_leaves ELSE 0 END) AS Mar_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 4 THEN ela.consumed_leaves ELSE 0 END) AS Apr_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 5 THEN ela.consumed_leaves ELSE 0 END) AS May_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 6 THEN ela.consumed_leaves ELSE 0 END) AS Jun_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 7 THEN ela.consumed_leaves ELSE 0 END) AS Jul_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 8 THEN ela.consumed_leaves ELSE 0 END) AS Aug_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 9 THEN ela.consumed_leaves ELSE 0 END) AS Sep_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 10 THEN ela.consumed_leaves ELSE 0 END) AS Oct_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 11 THEN ela.consumed_leaves ELSE 0 END) AS Nov_c,
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 12 THEN ela.consumed_leaves ELSE 0 END) AS 'Dec_c',
	    (
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 1 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 2 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 3 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 4 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 5 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 6 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 7 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 8 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 9 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 10 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 11 THEN ela.applicable_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 12 THEN ela.applicable_leaves ELSE 0 END) 
	        )actual_allocated,
	       (
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 1 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 2 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 3 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 4 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 5 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 6 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 7 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 8 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 9 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 10 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 11 THEN ela.consumed_leaves ELSE 0 END) +
	    SUM(CASE WHEN MONTH(ela.transaction_date) = 12 THEN ela.consumed_leaves ELSE 0 END) 
	        )consumed_leave,     
	     (
	         SELECT IFNULL(elb.applicable_leaves,0)- IFNULL(elb.consumed_leaves,0)
	         FROM 	emp_leave_balances elb
	         WHERE	elb.company_id = ela.company_id
	         AND	elb.employee_id = ela.employee_id
	         AND	elb.leave_type = ela.leave_type
	         AND	elb.blance_year = YEAR(ela.transaction_date)
	       )close_balance 
	FROM 
	    emp_leave_allocations ela
	JOIN 
	    employees e ON ela.company_id = e.company_id
	  	AND ela.employee_id = e.id
	WHERE ela.company_id = ".$company_id." ".$filter_condition."
	GROUP BY 
	    ela.employee_id,
	    e.department_id,
	    e.joining_date,
	    ela.leave_type,
	    YEAR(ela.transaction_date)";
	    $emp_leave_balances = $callConfig->getAllRows($query);
	    $return['emp_leave_balances_sql'] = $query;
		$return['emp_leave_balances'] = $emp_leave_balances;
		return $return;	

	}
	function togetcontractemployeepaydetails($get){
		global $callConfig;
		$emp_query = "SELECT id,emp_code, display_name FROM employees WHERE company_id = ".$get['company_id']." AND employee_type = 2 AND is_active =0";
		$return['emp_query'] = $emp_query;
		$return['emp_data'] = $callConfig->getAllRows($emp_query);
		$emp_type_query = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'EMPLOYEETYPES' AND is_active =0";
		$return['emp_type_query'] = $emp_type_query;
		$return['emp_type_data'] = $callConfig->getAllRows($emp_type_query);
		$gst_type_query = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'YESNO' AND is_active =0";
		$return['gst_type_query'] = $gst_type_query;
		$return['gst_type_data'] = $callConfig->getAllRows($gst_type_query);
		$pay_type_query = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'TASKFREQUENCY' AND is_active =0";
		$return['pay_type_query'] = $pay_type_query;
		$return['pay_type_data'] = $callConfig->getAllRows($pay_type_query);

		$tax_deducted_sources_query = "SELECT `id`,  `tds_name`, `tds_description`, `threshold`, `frequency`, `individual_percentage` FROM `tax_deducted_sources` WHERE company_id = ".$get['company_id']." AND tds_name = '194J' AND is_active =0";
		$return['tax_deducted_sources_query'] = $tax_deducted_sources_query;
		$return['tax_deducted_sources_data'] = $callConfig->getAllRows($tax_deducted_sources_query);

		if($get['cep_id']!=""){
			$pay_query = "SELECT * FROM `contract_employee_pay` WHERE company_id = ".$get['company_id']." AND id = ".$get['cep_id']." AND is_active =0";
			$return['pay_query'] = $pay_query;
			$return['row'] = $callConfig->getRow($pay_query);
		}
		return $return;
	}
	function addsalarycontractemployeepay($get, $post){
		global $callConfig;
		if($post['rid']!=""){
			$upquery = "UPDATE contract_employee_pay SET is_active = NULL WHERE id =".$post['rid'];
			$res = $callConfig->executeQuery($upquery);
		}
		unset($post['submit']);
		unset($post['submit_value_exit']);
		unset($post['rid']);
		$res = $callConfig->insertRecord('contract_employee_pay',$post);
		return $res;
	}
	function togetcontractorctcreportdetails($data){		
		global $callConfig;
		$pay_month = $data['pay_month'];
		$pay_year = $data['pay_year'];
		$query = "SELECT emp.id,emp.emp_code, emp.display_name 'employee_name',emp.father_name ,  emp.bank_id, emp.bank_account_no, emp.bank_link, emp.pan_number, emp.pf_no, emp.esi_no, emp.pf_uan, emp.joining_date , emp.email_id,  prs.total_no_of_days 'total_days', prs.total_no_of_leaves 'Leaves', prs.total_lop_days 'LWPs', prs.total_no_of_working_days 'working_days', IF( IFNULL(prs.total_lop_days, 0) = prs.total_no_of_working_days, 0, ( prs.total_no_of_days - IFNULL(prs.total_lop_days, 0) ) ) 'payable_days', prs.salary_standard_amount, prs.salary_actual_amount, prs.one_time_amount,prs.gst_type, prs.gst_percentage, prs.gst_amount, prs.tds_amount FROM employees emp, contract_pay_summary prs WHERE emp.company_id = '".$data['company_id']."' AND emp.company_id = prs.company_id AND emp.id = prs.employee_id AND prs.pay_year = '".$pay_year."' AND prs.pay_month = '".$pay_month."' AND emp.is_active = 0  AND prs.is_active = 0 ORDER BY emp.id, emp.joining_date, emp.display_name";
		$data['query'] = $query;
		$data['data'] = $callConfig->getAllRows($query);
		return $data;
	}
	function inserttaxslab($get,$post){
        global $callConfig;
        if(isset($post['assessment_year_old'])){
            $query="UPDATE tax_slads SET is_active=NULL, updated_by=".$get['user_id']." WHERE assessment_year='".$post['assessment_year_old']."' AND tax_slab_name='".$post['tax_slab_name_old']."' AND is_active = 0";
            $return['query'] = $query;
            $res = $callConfig->executeQuery($query);
           
        }
        foreach($post['slab_from'] as $key=>$val) {
            $insertarray = array('company_id'=>$get['company_id'],'assessment_year'=>$post['assessment_year'],'tax_slab_name'=>$post['tax_slab_name'],'slad_from'=>$post['slab_from'][$key],'slad_to'=>$post['slab_to'][$key],'rate'=>$post['rate'][$key],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
            $res = $callConfig->insertRecord('tax_slads',$insertarray);
            $return['slab'][] = $res;
            $return['insert'][] = $insertarray;
        }
        
        return $return;
    }
    function gettaxslabdata($get){
        global $callConfig;
        $tax_sql = "SELECT id,assessment_year,tax_slab_name,slad_from,slad_to,rate FROM tax_slads WHERE company_id =".$get['company_id']." AND is_active=0 GROUP BY assessment_year,tax_slab_name";
        $tax_res = $callConfig->getAllRows($tax_sql);
        $return['taxdata'] = $tax_res;
        $return['tax_sql'] = $tax_sql;
        return $return;
    }
    function getItems($get){
 
        global $callConfig;
        $itemtypes_sql = "SELECT id,item_type_name FROM item_types WHERE  company_id = '".$get['company_id']."' AND is_active=0";
        $itemtypes_res = $callConfig->getAllRows($itemtypes_sql);
        $return['item_types'] = $itemtypes_res;
        
        $category_sql = "SELECT  id, category_description  FROM  item_categories WHERE company_id = '".$get['company_id']."' AND is_active ='0'  ORDER BY category_description ASC";
        $category_res = $callConfig->getAllRows($category_sql);
        $return['category'] = $category_res;
        
        $costing_sql = "SELECT  genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = '".$get['company_id']."' AND genc_code ='INVCOST' AND is_active ='0'  ORDER BY genc_detail_name ASC";
        $costing_res = $callConfig->getAllRows($costing_sql);
        $return['costing_type'] = $costing_res;
        $uom_sql = "SELECT  id, uom_name  FROM  unit_of_measurements WHERE company_id = '".$get['company_id']."' AND is_active ='0'  ORDER BY uom_name ASC";
        $uom_res = $callConfig->getAllRows($uom_sql);
        $return['uom'] = $uom_res;
        
        $code='CONCAT(code," - ",code_description) as code';
        $hsnsac_sql = "SELECT  id, $code  FROM  hsn_and_sac_codes WHERE company_id = '".$get['company_id']."' AND is_active ='0'  ORDER BY code ASC";
        $hsnsac_res = $callConfig->getAllRows($hsnsac_sql);
        $return['hsnsac'] = $hsnsac_res;
        
        $code='CONCAT(code," - ",code_description) as code_description';
        $hscode_sql = "SELECT  id, $code  FROM  hs_codes WHERE company_id = '".$get['company_id']."' AND is_active ='0'  ORDER BY code_description ASC";
        $hscode_res = $callConfig->getAllRows($hscode_sql);
        $return['hscode'] = $hscode_res;
 
        $account_sql = "SELECT  account_code,account_name  FROM  chart_of_accounts WHERE allow_posting ='0' AND is_active ='0' AND company_id = '".$get['company_id']."'  ORDER BY account_code ASC LIMIT 20";
        $account_res = $callConfig->getAllRows($account_sql);
        $return['account'] = $account_res;
 
        return $return;
    }
   /* function gettaxdata($get){
        global $callConfig;
        $tax_sql = "SELECT id,assessment_year,tax_slab_name,slad_from,slad_to,rate FROM tax_slads WHERE  company_id = ".$get['company_id']."  AND is_active=0 GROUP BY assessment_year,tax_slab_name";
        $tax_res = $callConfig->getAllRows($tax_sql);
        $return['taxdata'] = $tax_res;
        return $return;
    }*/
    function togetnewsalarydetails($get){
    	global $callConfig;
    	$query = "SELECT * FROM earnings WHERE company_id =".$get['company_id']." AND is_active = 0 ORDER BY display_seq ASC";
    	$erans = $callConfig->getAllRows($query);
    	if(count((array)$erans)==0){
    		$inquery = "INSERT INTO `earnings` (`id`, `company_id`, `category_id`, `report_group_id`, `name`, `code`, `payslip_name`, `calculation_type`, `payroll_calculation_type`, `calculation_mode`, `calculation_mode_type`, `calculation_category`, `calculation_link`, `calculation_number`, `calculation_formula`, `varible_pay`, `part_of_ctc`, `display_in_payslip`, `pro_rata`, `editable`, `mandatory`, `display_seq`, `pay_generate_sequence`, `default_display`, `taxable`, `taxable_limit`, `min_range`, `max_range`, `note`, `formula_details`, `display_in_salary_structure`, `display_sequence_ctc_report`, `view_salary_display`, `view_salary_sequence`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) VALUES (NULL, '1', '2', '0', 'Fixed CTC', 'PAY_CTC', 'Total CTC', 'A', 'A', 'STATIC', '1', '0', '0', '0.00', ' ', '0', '1', '0', '0', '1', '1', '0', '10', '0', '0', '0.00', '0.00', '0.00', NULL, '{\"count\":0,\"details\":null}', NULL, '0', '1', '0', '0', '0', '2024-05-26 22:31:21', '2024-06-19', '2079-06-19', '2024-05-26 22:31:21', '1', '2', '2025-02-27 23:12:23'), (NULL, '1', '3', '0', 'Total', 'TOTAL_EARNING_OTHER', 'Total', 'F', 'F', 'STATIC', '1', '0', '0', '0.00', NULL, '0', '0', '1', '0', '1', '1', '1000', '170', '0', '0', '0.00', '0.00', '0.00', NULL, NULL, NULL, '130', '0', '130', '0', '0', '2024-05-29 22:22:52', '2024-06-27', '2079-06-27', '2024-05-29 22:22:52', '2', '1', '2025-02-28 09:01:22'), (NULL, '1', '4', '0', 'Total Deduction', 'TOTAL_DEDUCTION', 'Total Deduction', 'F', 'F', 'STATIC', '2', '0', '0', '0.00', NULL, '0', '0', '1', '0', '1', '1', '2000', '200', '0', '0', '0.00', '0.00', '0.00', NULL, NULL, NULL, '190', '1', '190', '0', '0', '2024-05-29 22:31:32', '2025-02-25', '2080-02-25', '2024-05-29 22:31:32', '2', '1', '2025-02-28 09:01:36'), (NULL, '1', '4', '0', 'Net Salary', 'NETPAYABLE', 'Net Salary', 'F', 'F', 'STATIC', '1', '0', '0', '0.00', NULL, '0', '0', '1', '0', '1', '1', '200', '210', '0', '0', '0.00', '0.00', '0.00', NULL, NULL, NULL, '200', '1', '200', '0', '0', '2024-05-29 22:33:40', '2024-05-30', '2079-05-30', '2024-05-29 22:33:40', '2', NULL, '2025-02-27 12:39:57')";
    		$res = $callConfig->executeQuery($inquery);
    		$erans = $callConfig->getAllRows($query);
    	}

    	$data['eraning_query'] = $query;
    	$data['eraning'] = $erans;
    	$query = "SELECT * FROM salary_stracture WHERE company_id =".$get['company_id']." AND id=1 AND is_active = 0";
    	$sal = $callConfig->getRow($query);
    	$data['salary_stracture'] = $sal;
    	return $data;
    }
    function togetemloyeesalarydetails($get){
    	global $callConfig;
    	$query = "SELECT id, display_name FROM employees WHERE company_id=".$get['company_id']." AND is_active=0";
    	$data['employees'] = $callConfig->getAllRows($query);
    	$query = "SELECT * FROM earnings WHERE company_id =".$get['company_id']." AND part_of_ctc =0 AND is_active = 0 ORDER BY pay_generate_sequence ASC";
    	$erans = $callConfig->getAllRows($query);
    	$data['eraning_query'] = $query;
    	$data['eraning'] = $erans;
    	$query = "SELECT * FROM salary_stracture WHERE company_id =".$get['company_id']." AND id=1 AND is_active = 0";
    	$sal = $callConfig->getRow($query);
    	//--------------ESI List------------//
		$whr="company_id= '".$get['company_id']."' AND is_active=0";
		$esiquery = $callConfig->selectQuery('emp_state_insaurance','*',$whr,'','','');
		$data['esidata'] = $callConfig->getRow($esiquery);
 		//--------------Provident funds List------------//
		$whr="company_id= '".$get['company_id']."' AND is_active=0";
		$pfquery = $callConfig->selectQuery('emp_provident_funds','*',$whr,'','','');
		$data['pfdata']  = $callConfig->getRow($pfquery);
 		//--------------Professional Tax List List------------//
		$whr="company_id= '".$get['company_id']."' AND is_active=0";
		$ptquery = $callConfig->selectQuery('professional_tax','*',$whr,'','','');
		$data['ptalldata'] = $callConfig->getAllRows($ptquery); 
    	return $data;
    }
    function getResignedEmployeeData($get){
        global $callConfig;
        $return = array();
        $query = "SELECT em.id as id , em.display_name as name, em.user_id FROM `employee_terminations` et
            JOIN employees em ON em.id = et.emp_id AND em.is_active = 0
            WHERE em.id NOT IN(select emp_id from user_assets ua where company_id=".$get['company_id']." AND is_active =0 ) AND et.company_id=".$get['company_id']." AND et.is_active=0";
        $data = $callConfig->getAllRows($query);
        $query_res = array();
        foreach ($data as $row) {
            $query_res[] = $row;
        }
        $return['query_res'] = $query_res;

        $query1 = "SELECT * FROM assets 
            WHERE company_id=" . $get['company_id'] . " AND is_active=0";
        $data1 = $callConfig->getAllRows($query1);
        $query1_res = array();
        foreach ($data1 as $row) {
            $query1_res[] = $row;
        }
        $return['assets'] = $query1_res;
        $query2 = "SELECT id, display_name, user_id FROM employees WHERE  company_id=".$get['company_id']." AND id NOT IN (SELECT emp_id FROM user_assets WHERE company_id=".$get['company_id']." AND is_active =0 ) AND is_active=0";
        $empdata = $callConfig->getAllRows($query2);
        $return['emp_query'] = $query2;
        $return['emp_data'] = $empdata;

        return $return;
    }

    function getResignedEmployeeHistoryData($get){
        global $callConfig;
        $return = array();

        $query = "SELECT em.id as id , em.display_name as name, em.user_id FROM `user_assets` ua
            JOIN employees em ON em.id = ua.emp_id AND em.is_active = 0
            WHERE  ua.company_id=".$get['company_id']." AND ua.is_active=0 GROUP BY ua.emp_id";
        $data = $callConfig->getAllRows($query);
        $query_res = array();
        foreach ($data as $row) {
            $query_res[] = $row;
        }
        $return['query_res'] = $query_res;

        $query1 = "SELECT id, asset_code, asset_description FROM assets WHERE company_id=" . $get['company_id'] . " AND is_active=0";
        $data1 = $callConfig->getAllRows($query1);
        $query1_res = array();
        foreach ($data1 as $row) {
            $query1_res[] = $row;
        }
        $return['assets'] = $query1_res;
        if($get['emp_id']!=''){
	        $query2 = "SELECT ua.id, ua.user_id, ua.emp_id, ua.asset_id, ua.received_date, ua.received_note FROM `user_assets` ua WHERE  ua.company_id=".$get['company_id']." AND emp_id = ".$get['emp_id']."  AND ua.is_active=0";
	        $data2 = $callConfig->getAllRows($query2);
	        $query2_res = array();
	        foreach ($data2 as $row) {
	            $query2_res[] = $row;
	        }
	        $return['user_assets'] = $query2_res;
        }
        return $return;
    }
    function getitadmindashboard($get){
    	global $callConfig;
    	$query = "SELECT COUNT(id) cm FROM employees WHERE company_id =".$get['company_id']." AND is_active=0";
    	$tmcc = $callConfig->getRow($query);
    	$return['total_emp_query'] = $query;
    	$return['total_emp_count'] = $tmcc->cm;

    	$query = "SELECT COUNT(id) tm FROM employee_terminations WHERE company_id =".$get['company_id']." AND current_status = ".ST_APPROVED." AND is_active=0";
    	$tmcc = $callConfig->getRow($query);
    	$return['total_emp_ter'] = $query;
    	$return['total_emp_ter_count'] = $tmcc->tm;

    	$query = "SELECT COUNT(et.id) uta  FROM `user_assets` et WHERE et.emp_id IN (select emp_id from employee_terminations where company_id=".$get['company_id']." AND current_status = ".ST_APPROVED."  AND is_active =0 ORDER BY id DESC ) AND et.company_id=".$get['company_id']." AND et.is_active=0";
    	$data = $callConfig->getRow($query);
    	$return['total_emp_uat_query'] = $query;
        $return['total_emp_uat_count'] = $data->uta; 

        $query = "SELECT COUNT(et.id) utna  FROM `user_assets` et WHERE et.emp_id NOT IN(select emp_id from employee_terminations where company_id=".$get['company_id']." AND current_status = ".ST_APPROVED."  AND is_active =0 ) AND et.company_id=".$get['company_id']." AND et.is_active=0 ORDER BY et.id DESC";
    	$data = $callConfig->getRow($query);

        $return['total_emp_uant_query'] = $query;
        $return['total_emp_uant_count'] = $data->utna; 


        $query1 = "SELECT * FROM assets  WHERE company_id=" . $get['company_id'] . " AND is_active=0";
        $data1 = $callConfig->getAllRows($query1);
        $return['assets_query'] = $query1;
        $return['assets'] = $data1;



        $query2 = "SELECT id, display_name, emp_code, user_id FROM employees WHERE  company_id=".$get['company_id']." AND id  IN (SELECT emp_id FROM employee_terminations WHERE company_id=".$get['company_id']." AND current_status = ".ST_APPROVED." AND is_active =0  ) AND is_active=0";
        $empdata = $callConfig->getAllRows($query2);
        $return['emp_ter_query'] = $query2;
        $return['emp_ter_data'] = $empdata;


        $query2 = "SELECT id, display_name, emp_code, user_id FROM employees WHERE  company_id=".$get['company_id']." AND id  IN (SELECT emp_id FROM user_assets WHERE company_id=".$get['company_id']." AND is_active =0 ) AND is_active=0";
        $empdata = $callConfig->getAllRows($query2);
        $return['emp_assest_query'] = $query2;
        $return['emp_asset_data'] = $empdata;

        return $return;
    }
    function getCustomerBillingData($get){
        global $callConfig;
        $return = array();
        $from = $get['month'].'-01';
        $to = $get['month'].'-31';
        $query  =  "SELECT count(emp_type.emp_id) as no_of_employees, c.display_name, c.id,  ceb.total_working_days,total_customer_billing_amount,  total_actual_billing_amount,  total_client_payroll FROM emp_employment_type_histories emp_type
        LEFT JOIN customers c ON c.id = emp_type.customer_id AND c.is_active = 0
        LEFT JOIN customer_employee_billings ceb ON ceb.customer_id = emp_type.customer_id AND ceb.billing_month = '{$get['month']}' AND ceb.is_active =0
        LEFT JOIN  (SELECT billing_id, is_active, sum(customer_billing) as total_customer_billing_amount, sum(actual_billing) as total_actual_billing_amount, sum(client_pay) as total_client_payroll FROM customer_employee_billing_details GROUP BY billing_id) cebd ON cebd.billing_id = ceb.id AND cebd.is_active=0
        WHERE effective_from_date <= '{$to}'
        AND effective_to_date >= '{$from}' AND emp_type.company_id = 1 AND emp_type.is_active = 0 
        GROUP BY emp_type.customer_id";
        $return['query'] = $query;
        $data = $callConfig->getAllRows($query);
        $return['data'] = $data;
        return $return;

    }
    function getCustomerBillingDetails($get){
        global $callConfig;
        $return = array();
        $rid = $get['rid'];
        $customerQuery = "SELECT id, display_name FROM customers WHERE company_id = ".$get['company_id']." AND id = {$rid} AND is_active = 0";
        $customerData = $callConfig->getRow($customerQuery);
        $return['customerData'] = $customerData;
        $from = $get['month'].'-01';
        $to = $get['month'].'-31'; 
        $empHistQuery = "SELECT emp_id FROM emp_employment_type_histories WHERE customer_id = {$rid} AND company_id = ".$get['company_id']." 
            AND is_active = 0 AND effective_from_date <= '$to' AND effective_to_date >= '$from'";
        // $return['empHistQuery'] = $empHistQuery;
        $empHistRows = $callConfig->getAllRows($empHistQuery);
        $empIds = array_column($empHistRows, 'emp_id');
        $return['empHistRows'] = $empHistRows;

        if (!empty($empIds)) {
            $idList = implode(',', array_map('intval', $empIds));
            $empList = $callConfig->getAllRows("SELECT id, display_name FROM employees WHERE company_id = ".$get['company_id']." AND  id IN ($idList) AND is_active = 0");
            $return['empList'] = $empList;

            $billingQuery = "SELECT * FROM customer_employee_billings WHERE  customer_id = {$rid} AND billing_month = '{$get['month']}' AND company_id = ".$get['company_id']." AND is_active = 0";
            $billingData = $callConfig->getRow($billingQuery);
            $return['billingData'] = $billingData ?? array();

            if ($billingData) {
                $billingId = $billingData->id;
                $detailsQuery = "SELECT * FROM customer_employee_billing_details WHERE company_id = ".$get['company_id']." AND  billing_id = {$billingId} AND is_active = 0";
                $detailsData = $callConfig->getAllRows($detailsQuery);
                $return['billingDetailsData'] = $detailsData;
            } else {
                $return['billingDetailsData'] = array();
            }
        }
        return $return;
    }

    function InsertCustomerEmployeeBillings($get, $post){
        global $callConfig;
        $return = array();
        $billing_id = $post['billing_id'] ?? '';
        if ($billing_id && $billing_id != '') {
            // Update existing billing
            $update_array = array(
                'total_working_days' => $post['total_working_days'] ?? '',
                'updated_date' => date("Y-m-d H:i:s"),
                'updated_by' => $get['user_id']
            );
            $res = $callConfig->updateRecord('customer_employee_billings', $update_array, 'id', $billing_id);
            $return['res'] = $res;

            foreach ($post['billing_details_id'] as $key => $val) {
                $update_array = array(
                    'lop_days' => $post['lop_days'][$key] ?? '',
                    'customer_billing' => $post['customer_billing'][$key] ?? '',
                    'actual_billing' => $post['actual_billing'][$key] ?? '',
                    'client_pay' => $post['client_pay'][$key] ?? '',
                    'updated_date' => date("Y-m-d H:i:s"),
                    'updated_by' => $get['user_id']
                );
                $res = $callConfig->updateRecord('customer_employee_billing_details', $update_array, 'id', $val);
                $return['res' . $key] = $res;
            }
        } else {
            $insert_array = array(
                'company_id' => $get['company_id'],
                'customer_id' => $post['customer_id'] ?? '',
                'customer_name' => $post['customer_name'] ?? '',
                'billing_month' => $post['billing_month'] ?? '',
                'total_working_days' => $post['total_working_days'] ?? '',
                'is_active' => 0,
                'current_status' => 0,
                'status_date' => date("Y-m-d H:i:s"),
                'created_date' => date("Y-m-d H:i:s"),
                'created_by' => $get['user_id']
            );
            $res = $callConfig->insertRecord('customer_employee_billings', $insert_array);
            $return['res'] = $res;
            $billing_id = $res['LASTID'];

            foreach ($post['emp_id'] as $key => $val) {
                $insert_array = array(
                    'company_id' => $get['company_id'],
                    'billing_id' => $billing_id,
                    'emp_id' => $val,
                    'emp_name' => $post['emp_name'][$key] ?? '',
                    'lop_days' => $post['lop_days'][$key] ?? '',
                    'customer_billing' => $post['customer_billing'][$key] ?? '',
                    'actual_billing' => $post['actual_billing'][$key] ?? '',
                    'client_pay' => $post['client_pay'][$key] ?? '',
                    'is_active' => 0,
                    'current_status' => 0,
                    'status_date' => date("Y-m-d H:i:s"),
                    'created_date' => date("Y-m-d H:i:s"),
                    'created_by' => $get['user_id']
                );
                $res = $callConfig->insertRecord('customer_employee_billing_details', $insert_array);
                $return['res' . $key] = $res;
            }
        }
        return $return;
    }
    function submitemploymenttype($get, $post){
    	global $callConfig;

    	$query = "SELECT * FROM emp_employment_type_histories WHERE company_id = ".$get['company_id']." AND emp_id = ".$post['emp_id']." AND '".$post['effective_from_date']."' >= effective_from_date  AND '".$post['effective_from_date']."' <= effective_to_date AND is_active = 0";
    	$res = $callConfig->getRow($query);
    	if($res->id!=""){
    		$res = array('status'=>'Fail', 'msg'=> 'The transition date is already assigned in an existing record. Please update the effective dates first.', 'error_no'=>'9161');
    	}else{
    		$insert_array = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'], 'employment_type'=> $post['employment_type'] ,'effective_from_date'=>$post['effective_from_date'],'effective_to_date'=>$post['effective_to_date'],'client_details'=>$post['client_details'],'remarks'=>$post['remarks'],'customer_id'=>$post['customer_id'],'billing_amount'=>$post['billing_amount'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			//$return['insert_array'] = $insert_array;			
			$res = $callConfig->insertRecord('emp_employment_type_histories',$insert_array);
    	}
    	return $res;
    }
    function togetxqwavereportdetils($get){
    	global $callConfig;
    	//print_r($get); exit;
    	$emp_query = "SELECT id, emp_code, display_name, band_id, joining_date, total_experiance, career_start_date FROM employees WHERE company_id=".$get['company_id'];
    	$emp_data = $callConfig->getAllRows($emp_query);
    	//$return['emp_query'] = $emp_query;
    	$return['emp_data'] = $emp_data;
    	$band_query = "SELECT id, band_name, short_code FROM employment_bands WHERE company_id=".$get['company_id'];
    	$band_data = $callConfig->getAllRows($band_query);
    	//$return['band_query'] = $band_query;
    	$return['band_data'] = $band_data;
    	$con_query = "SELECT id, name FROM config WHERE company_id=".$get['company_id'];
    	$con_data = $callConfig->getRow($con_query);
    	$return['con_data'] = $con_data;

    	return $return;
    }
    function togetalldetailsforemployeeval($get){
    	global $callConfig;
    	//$query2 = "SELECT attendace_date, clock_in_date, check_in_type, clock_in, clock_out_date, check_out_type, clock_out FROM employee_daily_attendances WHERE is_active=0 AND company_id = '".$get['company_id']."'  AND attendace_date='".$get['date']."' AND employee_id=".$get['at_emp_id']." order By clock_in ASC";
    	$at_data = array();
    	if($get['att_id'] !=""){
	    	$query2 = "SELECT attendace_date, clock_in_date, check_in_type, clock_in, clock_out_date, check_out_type, clock_out FROM employee_daily_attendances WHERE id=".$get['att_id'];
		 	$at_data = $callConfig->getRow($query2);		 	
	 	}
	 	$return['at_data'] = $at_data;
	 	$query = "SELECT id, leave_category_id, leave_type_name, back_ground_color FROM `leave_types` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$leavedatas = $callConfig->getAllRows($query);
		$return['leave_types'] = $leavedatas;
		
		$query = "SELECT * FROM `attendance_settings` WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$att_set = $callConfig->getRow($query);
		$return['att_set'] = $att_set;
	 	return $return;
    }

	// added by koti start
	function getemployeeworkattendances_new($get){

		global $callConfig;
		$company_id = (int)$get['company_id'];
	    $month      = str_pad($get['month'], 2, '0', STR_PAD_LEFT);
	    $year       = $get['year'];

	    /* ----------------------------------------------------
	       0. Prepare Month Start & End Dates
	    ---------------------------------------------------- */
	    $monthStart = date('Y-m-01', strtotime("$year-$month-01"));
	    $monthEnd   = date('Y-m-t', strtotime($monthStart));

		$slctdate = date($get['year']."-".$get['month']."-".$get['monthstartday']);
	    $startdate = date("Y-m-d",strtotime($slctdate));
		$enddate = date("Y-m-d",strtotime("+1 month",strtotime($slctdate)));  
		$enddate = date("Y-m-d", strtotime("-1 days", strtotime($enddate)));

	    $returnarray = array();

	    /* ----------------------------------------------------
	       1. Attendance Key Types
	    ---------------------------------------------------- */
	    $shiftKeysSql = "SELECT * FROM attendance_key_types
	        WHERE company_id = $company_id
	        AND is_active = 0";

	    $shiftKeysData = $callConfig->getAllRows($shiftKeysSql);
	    $shiftKeys = array();

	    foreach ($shiftKeysData as $row) {
	        $shiftKeys[$row->key_short_code] = (array)$row;
	    }

	    $returnarray['attendance_key_types'] = $shiftKeys;

	     /* ----------------------------------------------------
	       2. Attendance Settings (Weekly Off Logic)
	    ---------------------------------------------------- */
	    $settingsSql = "SELECT * FROM attendance_settings
	        WHERE company_id = $company_id
	        AND is_active = 0
	        ORDER BY id ASC
	        LIMIT 1";

	    $settingsRow = $callConfig->getRow($settingsSql);

	    $workingDays = array();
	    $weekOffDays = array();

	    if (!empty($settingsRow->working_days)) {
	        $workingDays = json_decode($settingsRow->working_days, true);
	    }

	    $allDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
	    $weekOffDays = array_values(array_diff($allDays, $workingDays));

	    $returnarray['attendance_settings'] = [
	        'working_days' => $workingDays,
	        'week_off_days'=> $weekOffDays
	    ];

	    /* ----------------------------------------------------
	       3. Get Employees Valid For This Month
	    ---------------------------------------------------- */
	    $empSql = "SELECT id, emp_code, display_name, image, joining_date, date_of_leaving, location_id FROM employees
	        WHERE company_id = $company_id AND employee_type = 1
	        AND joining_date <= '{$enddate}' AND (date_of_leaving >= '{$startdate}' OR date_of_leaving IS NULL)
	        AND is_active = 0";

	    $employees = $callConfig->getAllRows($empSql);

	    $employeeMap = [];

	    foreach ($employees as $emp) {
	        $employeeMap[$emp->id] = [
	            'employee_id'  => $emp->id,
	            'emp_code'     => $emp->emp_code,
	            'name'         => $emp->display_name,
	            'image'        => $emp->image,
	            'joining_date' => $emp->joining_date,
	            'leaving_date' => $emp->date_of_leaving,
	            'location_id'  => $emp->location_id
	        ];
	    }

	    $returnarray['employees'] = $employeeMap;

	    /* ----------------------------------------------------
	       4. Get Holidays (Year-wise)
	    ---------------------------------------------------- */
	    $holidaySql = "SELECT holiday_date, holiday_name, location_id FROM holidays
	        WHERE company_id = $company_id
	        AND holiday_year = '$year'
	        AND is_active = 0";

	    $holidayRows = $callConfig->getAllRows($holidaySql);
	    $holidays = array();

	    foreach ($holidayRows as $h) {
	        $holidays[$h->holiday_date][] = [
	            'holiday_name' => $h->holiday_name,
	            'location_id'  => $h->location_id
	        ];
	    }

	    $returnarray['holidays'] = $holidays;

	    /* ----------------------------------------------------
	       5. Get Attendance Records For Month
	    ---------------------------------------------------- */
	    $attendanceSql = "SELECT * FROM employee_daily_attendances
	        WHERE company_id = $company_id
	        AND attendace_date BETWEEN '$monthStart' AND '$monthEnd'
	        AND is_active = 0";

	    $attendanceRows = $callConfig->getAllRows($attendanceSql);

	    /* ----------------------------------------------------
	       6. Build Final Attendance Structure
	    ---------------------------------------------------- */
	    $attendanceData = array();

	    // Initialize all employees
	    foreach ($employeeMap as $empId => $emp) {
	        $attendanceData[$empId] = [
	            'employee' => $emp,
	            'days'     => array()
	        ];
	    }

	    // Fill attendance day-wise
	    foreach ($attendanceRows as $row) {
	        $empId = $row->employee_id;
	        $date  = $row->attendace_date;

	        if (!isset($attendanceData[$empId])) {
	            continue;
	        }

	        $attendanceData[$empId]['days'][$date] = [
	            'id'            => $row->id,
	            'employee_id'   => $row->employee_id,
	            'date'            => $date,
	            'clock_in'        => $row->clock_in,
	            'clock_out'       => $row->clock_out,
	            'shift_code'      => $row->shift_code,
	            'attendance_key'  => $row->attendance_key_short_code,
	            'leave_type_id'      => $row->leave_type_id
	        ];
	    }

	    /* ----------------------------------------------------
	       7. Final Response
	    ---------------------------------------------------- */
	    $returnarray['attendance_data'] = $attendanceData;
	    $returnarray['month'] = $month;
	    $returnarray['year']  = $year;
	    $returnarray['month_start'] = $monthStart;
    	$returnarray['month_end'] = $monthEnd;
	    return $returnarray;
	}

    function createExcelDataForEmployeeMonthlyAttedance($get){
        global $callConfig;
        $return = array();
        $data_arr = array();
        $month = $get['month'] ?? '2026-01';
        $company_id = (int)$get['company_id'];
        $monthStart = date('Y-m-01', strtotime("$month-01"));
	    $monthEnd   = date('Y-m-t', strtotime($monthStart));

	    list($year, $month_num) = explode('-', $month);
        $year = (int)$year;
        $month_num = (int)$month_num;
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_num, $year);

        // Pre-build date strings to avoid repeated sprintf calls
        $dates = array();
        for ($day = 1; $day <= $days_in_month; $day++) {
            $dates[$day] = sprintf('%04d-%02d-%02d', $year, $month_num, $day);
        }


        $query = "SELECT emp.id,  emp.emp_code, 
			  (SELECT dg.designation_name FROM designations dg WHERE dg.id = emp.designation_id) AS designation, 
			  (SELECT dp.department_name FROM departments dp WHERE dp.id = emp.department_id) AS department, 
			   emp.display_name, 
			  (SELECT dv.division_name FROM divisions dv WHERE dv.id = emp.division_id) AS division, 
			  (SELECT lc.location_name FROM locations lc WHERE lc.id = emp.location_id) AS location, 
			  (SELECT es.shift_id FROM   emp_shifts es WHERE es.emp_id = emp.id AND es.is_active=0 order by id desc limit 0,1) AS shift 
			FROM 
			  employees emp
			WHERE 
			  emp.company_id = {$company_id} 
			  AND emp.joining_date <= '{$monthEnd}'
			  AND (emp.date_of_leaving >'{$monthStart}' OR emp.date_of_leaving is NULL)
			  AND employee_type = 1
			  AND emp.is_active = 0";

        $employees = $callConfig->getAllRows($query);


        // Fetch attendance data with optimized query
        $query = "SELECT employee_id, attendace_date, attendance_key_short_code, clock_in, clock_out 
                  FROM employee_daily_attendances 
                  WHERE attendace_date BETWEEN '{$year}-{$month_num}-01' AND '{$year}-{$month_num}-{$days_in_month}' 
                  AND company_id = {$company_id} AND is_active=0";
        $data = $callConfig->getAllRows($query);
        
        // Build attendance array: $attandance_arr[emp_id][date]
        $attandance_arr = array();
        foreach ($data as $row){
            $attandance_arr[$row->employee_id][$row->attendace_date] = $row;
        }

        $serial_no = 1;
        foreach ($employees as $employee) {
            $row = array(
                $serial_no++,
                $employee->emp_code ?? '',
                $employee->designation ?? '',
                $employee->department ?? '',
                $employee->display_name ?? '',
                $employee->division ?? '',
                $employee->location ?? '',
                $employee->shift ?? '',
                '' // Timing placeholder
            );
            
            // Add attendance for each date
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date = $dates[$day];
                if (isset($attandance_arr[$employee->id][$date])) {
                    $att = $attandance_arr[$employee->id][$date];
                    $row[] = $att->attendance_key_short_code ?? '';
                    $row[] = $att->clock_in ?? '';
                    $row[] = $att->clock_out ?? '';
                } else {
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                }
            }
            
            $data_arr[] = $row;
        }
        $return['data'] = $data_arr;
        
        $query = "SELECT id, short_code, clock_in, clock_out FROM shifts  WHERE company_id={$company_id} AND is_active=0";
        $data = $callConfig->getAllRows($query);
        $shifts = array();
        foreach ($data as $row) {
        	$shifts[$row->id] = $row;
        }

        $return['shifts'] = $shifts;

        return $return;
    }
	

    function Resetthrpayrolldatabasedonmonthandyearandpayarea($get,$post){
        global $callConfig;
        $whr =  "pay_year='".$post['pay_year']."' and pay_month='".$post['pay_month']."' AND pay_area_id =".$post['pay_area_id'];
        $d1 ="DELETE FROM pay_run_summary WHERE ".$whr;
        $exr[] = $callConfig->executeQuery($d1);
        $d2 ="DELETE FROM pay_run_summary_components WHERE ".$whr;
        $exr[] = $callConfig->executeQuery($d2);
        $d3 ="DELETE FROM payroll WHERE ".$whr;
        $exr[] = $callConfig->executeQuery($d3);
        $d4 ="DELETE FROM payroll_steps WHERE ".$whr;
        $exr[] = $callConfig->executeQuery($d4);
        return $exr;
    }
    function getEmployeeMissingDetails($get){
        global $callConfig;
        $company_id = isset($get['company_id']) ? intval($get['company_id']) : 0;
        $sync_user_id = isset($get['user_id']) ? intval($get['user_id']) : 0;


        $employees_query = "SELECT id, emp_code, display_name, email_id, user_id, phone, b_machine_id
                            FROM employees
                            WHERE company_id = " . $company_id . "
                              AND is_active = 0";
        $employees = $callConfig->getAllRows($employees_query);
        if (!$employees) {
            $employees = array();
        }

        $users_query = "SELECT id, user_name, user_email, display_name
                        FROM application_users
                        WHERE company_id = " . $company_id . "
                          AND is_active = 0";
        $users = $callConfig->getAllRows($users_query);
        if (!$users) {
            $users = array();
        }

        $salary_query = "SELECT employee_id
                         FROM salary_template
                         WHERE company_id = " . $company_id . "
                           AND is_active = 0";
        $salary = $callConfig->getAllRows($salary_query);
        if (!$salary) {
            $salary = array();
        }

        $pay_area_query = "SELECT emp_id
                           FROM pay_area_employees
                           WHERE company_id = " . $company_id . "
                             AND is_active = 0";
        $pay_area = $callConfig->getAllRows($pay_area_query);
        if (!$pay_area) {
            $pay_area = array();
        }

        // Build user maps: usersById and user_emails[email] => id,user_name,email,display_name
        $usersById = array();
        $user_emails = array();
        foreach ($users as $user) {
            $uid = isset($user->id) ? intval($user->id) : 0;
            if ($uid <= 0) {
                continue;
            }

            $email = isset($user->user_email) ? trim((string)$user->user_email) : '';
            $email_key = strtolower($email);

            $usersById[$uid] = array(
                'id' => $uid,
                'user_name' => isset($user->user_name) ? trim((string)$user->user_name) : '',
                'user_email' => $email,
                'display_name' => isset($user->display_name) ? trim((string)$user->display_name) : ''
            );

            if ($email_key != '') {
                $user_emails[$email_key] = $usersById[$uid];
            }
        }

        $sync_summary = array(
            'employee_user_linked' => 0,
            'user_name_synced' => 0,
            'registered_group_inserted' => 0,
            'employee_link_details' => array(),
            'username_sync_details' => array(),
            'registered_group_details' => array(),
            'skipped_operations' => array()
        );

        $employeeLinkUpdates = array(); // emp_id => user_id
        $userNameUpdates = array(); // user_id => ['user_name','user_email']
        $claimed_usernames = array(); // Track usernames already claimed in this batch (lowercase)

        foreach ($employees as $index => $emp) {
            $emp_id = isset($emp->id) ? intval($emp->id) : 0;
            if ($emp_id <= 0) {
                continue;
            }

            $emp_email = isset($emp->email_id) ? trim((string)$emp->email_id) : '';
            $emp_email_key = strtolower($emp_email);
            $emp_user_id = isset($emp->user_id) ? intval($emp->user_id) : 0;
            $emp_code = isset($emp->emp_code) ? trim((string)$emp->emp_code) : '';

            // 1) Collect employee->user linking updates by matching email when user_id is empty.
            if ($emp_user_id <= 0 && $emp_email_key != '' && isset($user_emails[$emp_email_key])) {
                $matched_user_id = intval($user_emails[$emp_email_key]['id']);
                if ($matched_user_id > 0) {
                    $employeeLinkUpdates[$emp_id] = $matched_user_id;
                    $emp_user_id = $matched_user_id;
                    $employees[$index]->user_id = $matched_user_id;
                    // Track details
                    $sync_summary['employee_link_details'][] = array(
                        'emp_id' => $emp_id,
                        'emp_code' => $emp_code,
                        'user_id' => $matched_user_id,
                        'matched_by' => 'email'
                    );
                }
            }

            // Resolve matched user data using user_id, fallback by email map.
            $matched_user = null;
            if ($emp_user_id > 0 && isset($usersById[$emp_user_id])) {
                $matched_user = $usersById[$emp_user_id];
            } else if ($emp_email_key != '' && isset($user_emails[$emp_email_key])) {
                $matched_user = $user_emails[$emp_email_key];
            }

            // 2) Collect user_name sync updates and candidate rows for user_registered_groups.
            if ($matched_user && $emp_code != '') {
                $matched_user_id = intval($matched_user['id']);
                $current_user_name = trim((string)$matched_user['user_name']);
                $target_email = trim((string)$matched_user['user_email']);
                $emp_code_lower = strtolower($emp_code);

                // Only sync if user_name differs AND target emp_code isn't already used by another user
                if ($matched_user_id > 0 && $current_user_name != $emp_code) {
                                        // Check if this user_id is already queued for update in THIS batch
                                        if (isset($userNameUpdates[$matched_user_id])) {
                                            // Skip - this user was already claimed by another employee
                                            $sync_summary['skipped_operations'][] = array(
                                                'type' => 'username_sync',
                                                'emp_id' => $emp_id,
                                                'emp_code' => $emp_code,
                                                'user_id' => $matched_user_id,
                                                'target_username' => $emp_code,
                                                'reason' => 'user_already_queued_for_different_employee'
                                            );
                                            continue; // Skip to next employee
                                        }

                    // Check if emp_code (target user_name) is already used by a different user in DB
                    $username_taken = false;
                    $conflict_reason = '';
                    foreach ($usersById as $existing_uid => $existing_user) {
                        if ($existing_uid != $matched_user_id && 
                            strtolower(trim((string)$existing_user['user_name'])) == $emp_code_lower) {
                            $username_taken = true;
                            $conflict_reason = 'already_exists_in_db';
                            break;
                        }
                    }

                    // Also check if this target username is already claimed by another user in THIS update batch
                    if (!$username_taken && isset($claimed_usernames[$emp_code_lower])) {
                        $username_taken = true;
                        $conflict_reason = 'already_claimed_in_batch';
                    }

                    if (!$username_taken) {
                        $userNameUpdates[$matched_user_id] = array(
                            'user_name' => $emp_code,
                            'user_email' => $target_email
                        );
                        $claimed_usernames[$emp_code_lower] = $matched_user_id;
                        // Track details
                        $sync_summary['username_sync_details'][] = array(
                            'user_id' => $matched_user_id,
                            'old_username' => $current_user_name,
                            'new_username' => $emp_code,
                            'user_email' => $target_email
                        );
                    } else {
                        // Track skipped operation
                        $sync_summary['skipped_operations'][] = array(
                            'type' => 'username_sync',
                            'emp_id' => $emp_id,
                            'emp_code' => $emp_code,
                            'user_id' => $matched_user_id,
                            'target_username' => $emp_code,
                            'reason' => $conflict_reason
                        );
                    }
                }
            }
        }

        // Execute employee user_id link updates in a single CASE query.
        if (count($employeeLinkUpdates) > 0) {
            $empIds = array_keys($employeeLinkUpdates);
            $caseParts = array();
            foreach ($employeeLinkUpdates as $eid => $uid) {
                $caseParts[] = "WHEN " . intval($eid) . " THEN " . intval($uid);
            }
            $link_query = "UPDATE employees
                           SET user_id = CASE id " . implode(' ', $caseParts) . " END
                           WHERE id IN (" . implode(',', array_map('intval', $empIds)) . ")
                             AND company_id = " . $company_id . "
                             AND is_active = 0";
            $link_res = $callConfig->executeQuery($link_query);
            if ($link_res) {
                $sync_summary['employee_user_linked'] = count($employeeLinkUpdates);
            }
        }

        // Execute application_users.user_name updates in a single CASE query.
        if (count($userNameUpdates) > 0) {
            $userIds = array_keys($userNameUpdates);
            $caseParts = array();
            foreach ($userNameUpdates as $uid => $urow) {
                $safe_name = addslashes($urow['user_name']);
                $caseParts[] = "WHEN " . intval($uid) . " THEN '" . $safe_name . "'";
            }

            $user_update_query = "UPDATE application_users
                                  SET user_name = CASE id " . implode(' ', $caseParts) . " END
                                  WHERE id IN (" . implode(',', array_map('intval', $userIds)) . ")
                                    AND company_id = " . $company_id . "
                                    AND is_active = 0";
            $user_update_res = $callConfig->executeQuery($user_update_query);
            if ($user_update_res) {
                $sync_summary['user_name_synced'] = count($userNameUpdates);
            }

            // Prepare bulk lookup for existing user_registered_groups rows (only by email)
            $candidateEmails = array();
            $emailList = array();
            foreach ($userNameUpdates as $urow) {
                $email = trim((string)$urow['user_email']);
                if ($email == '') {
                    continue;
                }
                $email_lower = strtolower($email);
                $candidateEmails[$email_lower] = $urow;
                $emailList[] = addslashes($email);
            }

            $existingEmails = array();
            if (count($candidateEmails) > 0) {
                $emailList = array_unique($emailList);

                $exists_query = "SELECT user_email
                                 FROM " . MAINDB . ".user_registered_groups
                                 WHERE is_active = 0
                                   AND user_email IN ('" . implode("','", $emailList) . "')";
                $exists_rows = $callConfig->getAllRows($exists_query);
                if ($exists_rows && count((array)$exists_rows) > 0) {
                    foreach ($exists_rows as $erow) {
                        $e = strtolower(trim((string)$erow->user_email));
                        $existingEmails[$e] = true;
                    }
                }

                foreach ($candidateEmails as $email_lower => $urow) {
                    if (isset($existingEmails[$email_lower])) {
                        // Track skipped registered_group insertion
                        $sync_summary['skipped_operations'][] = array(
                            'type' => 'registered_group_insert',
                            'user_email' => $urow['user_email'],
                            'user_name' => $urow['user_name'],
                            'reason' => 'email_already_exists_in_registered_groups'
                        );
                        continue; // Already exists, skip
                    }

                    $insert_registered_group = array(
                        'group_id' => 0,
                        'user_email' => addslashes($urow['user_email']),
                        'zqr_text' => '',
                        'zqr_connect' => '',
                        'reset_password' => '',
                        'start_date' => date('Y-m-d'),
                        'expiry_date' => null,
                        'email_active' => '',
                        'email_active_date' => null,
                        'is_active' => 0,
                        'current_status' => 0,
                        'status_date' => date('Y-m-d H:i:s'),
                        'from_date' => date('Y-m-d'),
                        'to_date' => null,
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $sync_user_id,
                        'updated_by' => $sync_user_id,
                        'updated_date' => date('Y-m-d H:i:s')
                    );

                    $insert_res = $callConfig->insertRecord(MAINDB . '.user_registered_groups', $insert_registered_group);
                    if ($insert_res && isset($insert_res['LASTID'])) {
                        $sync_summary['registered_group_inserted']++;
                        // Track details
                        $sync_summary['registered_group_details'][] = array(
                            'id' => $insert_res['LASTID'],
                            'user_email' => $urow['user_email'],
                            'user_name' => $urow['user_name']
                        );
                    }
                }
            }
        }

        // Re-run users query so response includes latest synced user_name values.
        $users = $callConfig->getAllRows($users_query);
        if (!$users) {
            $users = array();
        }

        // Build users_with_name map from refreshed users list.
        $users_with_name = array();
        foreach ($users as $user) {
            $uid = isset($user->id) ? intval($user->id) : 0;
            if ($uid <= 0) {
                continue;
            }

            $users_with_name[$uid] = isset($user->user_name) ? trim((string)$user->user_name) : '';
        }

        // Build missing-details rows once in backend so frontend does not repeat this logic.
        $salary_emp_ids = array();
        foreach ($salary as $salary_row) {
            $sid = isset($salary_row->employee_id) ? intval($salary_row->employee_id) : 0;
            if ($sid > 0) {
                $salary_emp_ids[$sid] = true;
            }
        }

        $pay_area_emp_ids = array();
        foreach ($pay_area as $pay_area_row) {
            $pid = isset($pay_area_row->emp_id) ? intval($pay_area_row->emp_id) : 0;
            if ($pid > 0) {
                $pay_area_emp_ids[$pid] = true;
            }
        }

        $missing_employees = array();
        foreach ($employees as $emp) {
            $emp_id = isset($emp->id) ? intval($emp->id) : 0;
            if ($emp_id <= 0) {
                continue;
            }

            $emp_code = isset($emp->emp_code) ? trim((string)$emp->emp_code) : '';
            $display_name = isset($emp->display_name) ? trim((string)$emp->display_name) : '';
            $email = isset($emp->email_id) ? trim((string)$emp->email_id) : '';
            $emp_user_id = isset($emp->user_id) ? intval($emp->user_id) : 0;
            $b_machine_id = isset($emp->b_machine_id) ? trim((string)$emp->b_machine_id) : '';

            $missing_flags = array();
            if ($email == '') {
                $missing_flags['email'] = 'N';
            }
            if ($b_machine_id == '') {
                $missing_flags['b_machine_id'] = 'N';
            }
            if ($emp_user_id <= 0 || !isset($users_with_name[$emp_user_id]) || $users_with_name[$emp_user_id] == '') {
                $missing_flags['user'] = 'N';
            }
            if (!isset($salary_emp_ids[$emp_id])) {
                $missing_flags['salary'] = 'N';
            }
            if (!isset($pay_area_emp_ids[$emp_id])) {
                $missing_flags['pay_area'] = 'N';
            }

            if (count($missing_flags) > 0) {
                $missing_employees[] = array(
                    'id' => $emp_id,
                    'emp_code' => $emp_code,
                    'display_name' => $display_name,
                    'missing' => $missing_flags
                );
            }
        }


        // Non-compact response retained for backward compatibility.
        return array(
            'status' => 'success',
            'employees' => $employees,
            'users' => $users,
            'salary' => $salary,
            'pay_area' => $pay_area,
            'missing_employees' => $missing_employees
        );
    }
    function togetpayareaconfigdetailsbasedonemp_id($get){
		global $callConfig;
        $company_id = $get['company_id'] ?? 0;
        $employee_id = $get['employee_id'] ?? 0;
        $query = "SELECT pac.* FROM pay_area_config pac INNER JOIN pay_area_employees pae ON pae.pay_area_id = pac.pay_area_id WHERE pae.emp_id = {$employee_id} AND pae.company_id = {$company_id} AND pae.is_active = 0 AND pac.company_id = {$company_id} AND pac.is_active = 0 LIMIT 1";
        $payarea_arr = $callConfig->getRow($query);
        return $payarea_arr;
    }

	function getemployeeworkattendancespayareawise($get){
	 	global $callConfig;
	 	$retrunarray = array();
	 	//echo "<pre>";
	 	$query = "SELECT id, name FROM pay_areas WHERE company_id='{$get['company_id']}' AND is_active=0 AND name IS NOT NULL AND name != '' ";
	 	$pay_areas = $callConfig->getAllRows($query);
		$retrunarray['pay_areas']=$pay_areas;
		$emp_list = array();
	 	$pay_area_id = $get['pay_area_id'] ?? '';

	 	if($pay_area_id != ''){
	 		$query = "SELECT emp_id FROM pay_area_employees WHERE pay_area_id={$pay_area_id} AND company_id='{$get['company_id']}' AND is_active=0";
	 		$emp_ids_list = $callConfig->getAllRows($query);
	 		foreach($emp_ids_list as $row){
	 			$emp_list[] = $row->emp_id;
	 		}
	 	}

	 	// $emp_list = json_decode($get['emplist'],1);
	 	$discrepancy = array();
	 	$month = $get['month'];
	 	$year = $get['year'];
		$shift_check = SHIFT_CHECK; //1 meaning yes
		$slctdate = date($get['year']."-".$get['month']."-".$get['monthstartday']);
		$startdate = date("Y-m-d",strtotime($slctdate));
		$enddate = date("Y-m-d",strtotime("+1 month",strtotime($slctdate)));  
		$enddate = date("Y-m-d", strtotime("-1 days", strtotime($enddate)));
		
		$work_attendance_logssql = "SELECT pay_area_id, month_year, type, created_date, import_swap_in_out_id FROM `work_attendance_logs` WHERE company_id=  '".$get['company_id']."' AND month_year ='".$year."-".$month."' AND is_active = 0 GROUP BY type ORDER BY created_date DESC";
		$work_attendance_logsres = $callConfig->getAllRows($work_attendance_logssql);
		$retrunarray['work_attendance_logs']=$work_attendance_logsres;
		if(count((array)$emp_list)==0 && $pay_area_id == ''){
			//$query="SELECT id,display_name,image,emp_code  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND joining_date <= '".$enddate."' AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL)";
			//$query = "SELECT id,display_name,image,emp_code FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '3' <> '1' THEN ( ('1' = '".$get['admin_type']."') OR (emp.reporting_manager_id = '".$get['user_id']."') OR (emp.hr_buddy = '".$get['emp_id']."') OR (emp.finance_buddy = '".$get['emp_id']."') ) ELSE( emp.vendor_id = ( SELECT id FROM vendors ven2 WHERE ven2.email = '".$get['user_email']."' ) ) END )";

			//$query = "SELECT id,display_name,image,emp_code FROM `employees` emp WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND ( CASE WHEN '1' <> '".$get['admin_type']."' THEN ( emp.id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = '".$get['emp_id']."') ) ) ELSE 1 END )";
			$query ="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving FROM `employees` emp WHERE emp.company_id = '".$get['company_id']."' AND emp.employee_type = 1 AND emp.is_active = 0 AND ( CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving <= '".$enddate."') THEN 1 WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END)= 1 AND (emp.hr_buddy = '".$get['emp_id']."' OR emp.finance_buddy = '".$get['emp_id']."' OR emp.admin_buddy = '".$get['emp_id']."' OR emp.project_buddy = '".$get['emp_id']."' OR emp.loc_buddy = '".$get['emp_id']."' OR emp.reporting_manager_id = '".$get['user_id']."' OR (emp.id IN (SELECT	pae.emp_id FROM pay_area_employees pae, payroll_area_admins paa WHERE pae.company_id = paa.company_id AND pae.company_id = emp.company_id AND	pae.pay_area_id = paa.pay_area_id AND pae.is_active = paa.is_active AND	pae.is_active = 0 AND paa.emp_id = '".$get['emp_id']."' ))	OR ( CASE WHEN '1' = '".$get['admin_type']."' THEN 1 END) = 1)";
			
		}else{
			$liemp =implode(",",$emp_list);
			$query="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND joining_date <= '".$enddate."' AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL) AND id IN(".$liemp.")";
		}
		$retrunarray['employees_sql']=$query;
		//echo $query; exit;
		$empdatas = $callConfig->getAllRows($query);
		$retrunarray['empdatas']=$empdatas;

		// return $retrunarray;
		
		$shiftsql = "SELECT ss.id,emps.emp_id, ss.name, ss.minimum_hours_required,ss.half_day_hours,ss.working_days FROM emp_shifts emps, shift_settings ss WHERE emps.is_active  = ss.is_active AND  emps.company_id =ss.company_id AND  emps.shift_id  = ss.id AND emps.is_active = 0 AND emps.company_id=".$get['company_id'];
		$shiftres = $callConfig->getAllRows($shiftsql);
		foreach($shiftres as $shiftress){
			$shiftresarray[$shiftress->emp_id] = $shiftress;
		}
		foreach ($empdatas as $value){
			if($shift_check == '1' AND $get['shift_categroy'] !=""){
				if($shiftresarray[$value->id]->id ==$get['shift_categroy']){
					$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'emp_code'=>$value->emp_code,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
				}
			}else{
				$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'emp_code'=>$value->emp_code,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
			}
		}
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = json_decode($atsdata->working_days,1);
		
		$shiftcatsql = "SELECT id, shift_name, clock_in, clock_out FROM shifts WHERE is_active = 0 AND company_id=".$get['company_id'];
		$shiftcatres = $callConfig->getAllRows($shiftcatsql);
		
		$shiftkeys = "SELECT id, key_short_code, display_value, color_code, key_name FROM attendance_key_types WHERE company_id=".$get['company_id']." AND leave_category_id IS NULL AND is_active=0";
		$shiftkeysdata = $callConfig->getAllRows($shiftkeys); // working //absent
		foreach ($shiftkeysdata as $skey=>$svalue) {
			$shift[$svalue->key_short_code] = (array)$svalue;
		}

		$attkey = "SELECT lc.id, lc.name, akt.key_short_code, akt.display_value, akt.color_code, akt.key_name FROM leave_categories lc, attendance_key_types akt WHERE lc.company_id = '".$get['company_id']."' AND lc.company_id = akt.company_id AND lc.id = akt.leave_category_id AND lc.is_active = akt.is_active AND lc.is_active = '0'";
		$attkeyres = $callConfig->getAllRows($attkey); // working //absent
		foreach ($attkeyres as $svalue) {
			$shift[$svalue->key_short_code] = (array)$svalue;
		}
		$retrunarray['attendance_key_types'] = $shift;

		$attendancesql = "SELECT attendace_date,employee_id,attendance_key_short_code  FROM `employee_daily_attendances` WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."'";
		$attendanceres = $callConfig->getAllRows($attendancesql); // working //absent
		foreach ($attendanceres as $attendancere) {
			$attendacnearray[$attendancere->employee_id][$attendancere->attendace_date] = $attendancere;
		}

		$leavetypesql = "SELECT lt.id, lc.name, akt.key_short_code FROM leave_types lt, leave_categories lc, attendance_key_types akt WHERE lt.company_id = '".$get['company_id']."' AND lt.company_id = lc.company_id AND lt.company_id= akt.company_id AND lt.leave_category_id = lc.id AND lc.id = akt.leave_category_id AND lt.is_active = 0 AND lt.is_active = lc.is_active AND lt.is_active= akt.is_active";
			$leavetypesres=$callConfig->getAllRows($leavetypesql);
			foreach($leavetypesres as $ltype){
				$leavetype[$ltype->id] = $ltype->key_short_code;
			}
		$retrunarray['leavetype'] = $leavetype;


		$leavessql = "select employee_id,leave_from,leave_to,leave_type_id,start_half_day,end_half_day,datelist 
		from employee_leaves, 
		(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) datelist from
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) as v
		where datelist>=leave_from and datelist <=leave_to AND ((MONTH(leave_from) = '".$month."' AND YEAR(leave_from) = '".$year."') OR (MONTH(leave_to) = '".$month."' AND YEAR(leave_to) = '".$year."') )";
	 	$leavesdetails = $callConfig->getAllRows($leavessql); // working //absent
	 	foreach ($leavesdetails as $leavesdetail) {
	 		$leavesdetailsarray[$leavesdetail->employee_id][$leavesdetail->datelist] = array('type'=>$leavesdetail->leave_type_id,'start_half_day'=>$leavesdetail->start_half_day,'end_half_day'=>$leavesdetail->end_half_day);
	 	}
	 	$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
		$holidaysdata = $callConfig->getAllRows($holidays); //holidays
		foreach ($holidaysdata as $hkey=>$hvalue) {
			$holidaysdatas[] = $hvalue->holiday_date;
		}
		$checkupdate = "SELECT id FROM `employee_daily_attendances` WHERE is_active='0' and company_id=".$get['company_id']." AND MONTH(attendace_date) = '".$month."' AND YEAR(attendace_date) = '".$year."'";

		if($get['admin_type']!=1){
			$checkupdate.=" AND employee_id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		}
		//echo $checkupdate; exit

		$checkupdates = $callConfig->getRow($checkupdate);

		$tempdetails = "SELECT employee_id,attendance_date,attendance_key_short_code,start_half_day,end_half_day,clock_in,clock_out FROM temp_attendance_details WHERE is_active='0' and company_id=".$get['company_id']." AND MONTH(attendance_date) = '".$month."' AND YEAR(attendance_date) = '".$year."'";
		$tempdetailsdata = $callConfig->getAllRows($tempdetails); //holidays
		foreach ($tempdetailsdata as $tempary) {
			$tempdetailary[$tempary->employee_id][$tempary->attendance_date] = array("date"=>$tempary->attendance_date,"type"=>$tempary->attendance_key_short_code,"start_half_day"=>$tempary->start_half_day,"end_half_day"=>$tempary->end_half_day,'clock_in'=>$tempary->clock_in,'clock_out'=>$tempary->clock_out);
		}
		$min_working_hours = explode(":", $atsdata->minimum_hours_required);
		$half_working_hours = explode(":", $atsdata->half_day_hours);
		$timeshetetrys = array();
		$i = 0;
		$notleaveattendancekey = array('P','R','A','H','O','Z');
		$workingdayattendancekey = array('P','O');
		//echo "<pre>"; print_r($leavesdetailsarray); 
		foreach($empidlist as $ky => $emp){
			$monthworkingdays = 0;
			$actualworkingdays = 0;
			$start = new DateTime($startdate);
			$end = new DateTime($enddate);
			$dates = array();
			$timeshettempoyee = array();
			$emp_shift_days = json_decode($shiftresarray[$ky]->working_days,1);
			
			if(isset($checkupdates->id)){
				$retrunarray['valuedisplaytype'] = "update";
				while($start <= $end){
					$date = $start->format('Y-m-d');
					$dy = $start->format('l');
					if(in_array($date,(array)$holidaysdatas)){
						$dates[$date] = array('day'=>'H','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['H']['color_code'],'display_value'=>$shift['H']['display_value']);
					}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1' AND OFF_CHECK == '0')){
						$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
					}
					//elseif(isset($attendacnearray[$ky][$date]) AND isset($leavesdetailsarray[$ky][$date])){
					elseif(isset($leavesdetailsarray[$ky][$date])){
						$monthworkingdays++;
						$actualworkingdays++;
						$leavtypedetail = $leavesdetailsarray[$ky][$date];
						//print_r($leavtypedetail);
						$leaveshortcode = $leavetype[$leavtypedetail['type']];
						$dates[$date] = array('day'=>$leaveshortcode,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$leaveshortcode]['color_code'],'display_value'=>$shift[$leaveshortcode]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift[$leaveshortcode]['color_code']);
						$discrepancy[$ky] = $emp;
					}elseif(isset($attendacnearray[$ky][$date])){
						$shortkey = $attendacnearray[$ky][$date];
						if($shortkey->attendance_key_short_code == "O"){
							$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
						}elseif($shortkey->attendance_key_short_code == "Z"){
							$dates[$date] = array('day'=>'Z','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['Z']['color_code'],'display_value'=>$shift['Z']['display_value'],'working_color'=>$shift['Z']['color_code']);
						}else{
							$monthworkingdays++;
							$actualworkingdays++;
							$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value']);
						}
					}else{
						///if(isset($leavesdetailsarray[$ky][$date])){
							//$monthworkingdays++;
							//$actualworkingdays++;
							

							//$dates[$date] = array('day'=>$leavetype[],'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								//$discrepancy[$ky] = $emp;


								/**
							if($leavesdetailsarray[$ky][$date]['type'] == '6'){
								$dates[$date] = array('day'=>'S','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['S']['color_code'],'display_value'=>$shift['S']['display_value'],'strthalfday'=>$leavesdetailsarray[$ky][$date]['start_half_day'],'endthalfday'=>$leavesdetailsarray[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								$discrepancy[$ky] = $emp;
							}else{
								$dates[$date] = array('day'=>'V','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
								$discrepancy[$ky] = $emp;
							}  **/
						//}else{
						$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value']);
						$discrepancy[$ky] = $emp;
						//}
					}
					if($i=='0'){
						$alldate[]  = $date; 
					}	
					$start->modify('+1 days');
				// if($date == date("Y-m-d")){
				// 	break;
				// }
				}
			}else{
				$retrunarray['valuedisplaytype'] = "insert";
				while($start <= $end){
					$date = $start->format('Y-m-d');
					$dy = $start->format('l');
					if($emp['joining_date'] >= $date || ($emp['date_of_leaving'] <= $date && $emp['date_of_leaving'] !="")){
						$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value']);
					}elseif(in_array($date,(array)$holidaysdatas)){
						$dates[$date] = array('day'=>'H','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['H']['color_code'],'display_value'=>$shift['H']['display_value']);
					}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1') AND OFF_CHECK == '0'){
						$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
					}elseif($tempdetailary[$ky][$date] !=""){
						
						$clockintime = $tempdetailary[$ky][$date]['clock_in'];
						$clockouttime = $tempdetailary[$ky][$date]['clock_out'];
						
						$discrepancy[$ky] = $emp;
						$attendance_key_short_code = $tempdetailary[$ky][$date]['type'];
						
						if(!in_array($attendance_key_short_code, $notleaveattendancekey)){  // leave Show
							$actualworkingdays++;
							$monthworkingdays++;
							$dates[$date] = array('day'=>$attendance_key_short_code,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$attendance_key_short_code]['color_code'],'display_value'=>$shift[$attendance_key_short_code]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift[$attendance_key_short_code]['color_code']);
						}else{ // normal Day Show presdsent and absent
							if(in_array($attendance_key_short_code, $workingdayattendancekey)){  // o,p working day count
								$monthworkingdays++; 
								if($attendance_key_short_code !='O'){  // o,p working day count
									$actualworkingdays++; 

								}
							}
							$dates[$date] = array('day'=>$attendance_key_short_code,'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$attendance_key_short_code]['color_code'],'display_value'=>$shift[$attendance_key_short_code]['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime,'working_color'=>$shift['Z']['color_code']);
							
						}

						/**
						if($tempdetailary[$ky][$date]['type']=="A"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'A','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['A']['color_code'],'display_value'=>$shift['A']['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime);
						}elseif($tempdetailary[$ky][$date]['type']=="P"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value'],'clock_in'=>$clockintime,'clock_out'=>$clockouttime);
						}elseif($tempdetailary[$ky][$date]['type']=="O"){
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>'O','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['O']['color_code'],'display_value'=>$shift['O']['display_value']);
						}else{
							$actualworkingdays++;
							$discrepancy[$ky] = $emp;
							$dates[$date] = array('day'=>$tempdetailary[$ky][$date]['type'],'date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift[$tempdetailary[$ky][$date]['type']]['color_code'],'display_value'=>$shift[$tempdetailary[$ky][$date]['type']]['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);


							/**if($tempdetailary[$ky][$date]['leave_type_id']=="6"){
								$discrepancy[$ky] = $emp;
								$dates[$date] = array('day'=>'S','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['S']['color_code'],'display_value'=>$shift['S']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
							}else{
								$discrepancy[$ky] = $emp;
								$dates[$date] = array('day'=>'V','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['V']['color_code'],'display_value'=>$shift['V']['display_value'],'strthalfday'=>$tempdetailary[$ky][$date]['start_half_day'],'endthalfday'=>$tempdetailary[$ky][$date]['end_half_day'],'working_color'=>$shift['P']['color_code']);
							}	**//**
						}  **/
					}else{
						$monthworkingdays++;
						$actualworkingdays++;
						$dates[$date] = array('day'=>'P','date'=>$date,'billable_hrs'=>$timeshettempoyee[$date]['billable_hrs'],'timesheetid'=>$timeshettempoyee[$date]['id'],'color_code'=>$shift['P']['color_code'],'display_value'=>$shift['P']['display_value']);
					}
					if($i=='0'){
						$alldate[]  = $date; 
					}	
					$start->modify('+1 days');
				}
			}
			$timeshetetrys[$ky] = $dates;
			$i++;
			$monthworkignday[$ky] = $monthworkingdays;
			$actualworkingday[$ky] = $actualworkingdays;
		}
		$retrunarray['shifts'] = $shiftcatres;
		$retrunarray['monthworkingdays'] = $monthworkignday;
		$retrunarray['actualworkingdays'] = $actualworkingday;
		$retrunarray['discrepancy'] = $discrepancy;
		$retrunarray['timesheet'] = $timeshetetrys;
		$retrunarray['date'] = $alldate;
		$retrunarray['emplist'] = $empidlist;
		$retrunarray['attendacnearray'] = $attendacnearray;
		$retrunarray['query']=$query;
		//$retrunarray['min_working_hours'] = $min_working_hours[0];
		//$retrunarray['min_working_hours_full'] = $atsdata->minimum_hours_required;
		//$retrunarray['half_working_hours'] = $half_working_hours[0];
		//echo "<pre>"; print_r($retrunarray);
		return $retrunarray;
	}

	function insertemployeeworkattendancespayareawise($get){
		global $callConfig;
		$returnarray = array();
		$shift_check = SHIFT_CHECK; //1 meaning yes
		$month = $get['month'];
		$year = $get['year'];
		$get['month_year'] = $year."-".$month;
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = json_decode($atsdata->working_days,1);
		if($shift_check == 1){
			$shiftsql = "SELECT ss.id,emps.emp_id, ss.name, ss.minimum_hours_required,ss.half_day_hours,ss.working_days FROM emp_shifts emps, shift_settings ss WHERE emps.is_active  = ss.is_active AND  emps.company_id =ss.company_id AND  emps.shift_id  = ss.id AND emps.is_active = 0 AND emps.company_id=".$get['company_id'];
			$shiftres = $callConfig->getAllRows($shiftsql);
			foreach($shiftres as $shiftress){
				$shiftresarray[$shiftress->emp_id] = $shiftress;
			}
		}
		$tempdetails = "SELECT * FROM temp_attendance_details WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(attendance_date) = '".$month."' AND YEAR(attendance_date) = '".$year."'";
		/**if($get['admin_type']!=1){
			$tempdetails.=" AND employee_id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		}**/

		$tempdetailsdata = $callConfig->getAllRows($tempdetails); //holidays
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND MONTH(holiday_date) = '".$month."' AND YEAR(holiday_date) = '".$year."'";
		$holidaysdata = $callConfig->getAllRows($holidays); //holidays
		foreach ($holidaysdata as $hkey=>$hvalue) {
			$holidaysdatas[] = $hvalue->holiday_date;
		}

		$attendance_key_types = array();
		$aktypesql = "SELECT key_short_code, leave_category_id FROM attendance_key_types WHERE is_active=0 and company_id=".$get['company_id']." AND leave_category_id !=''";
		$aktyperes = $callConfig->getAllRows($aktypesql); //holidays
		foreach ($aktyperes as $valk) {
			$attendance_key_types[$valk->key_short_code] = $valk->key_short_code;
		}
		//$notleaveattendancekey = array('P','R','A','H','O');
		foreach($tempdetailsdata as $tempary){
			if($tempary->attendance_key_short_code == 'A'){
				$absentarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif($tempary->attendance_key_short_code == 'P' || $tempary->attendance_key_short_code == 'Z'){
				$presentarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif($tempary->attendance_key_short_code == 'O'){
				$offdayarray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}elseif(in_array($tempary->attendance_key_short_code, $attendance_key_types)){
				$leavearray[$tempary->employee_id][$tempary->attendance_date] = $tempary;
			}
		}
		//$query="SELECT id,display_name,image,emp_code  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 AND date_of_leaving IS NULL";
		$slctdate = date($get['year']."-".$get['month']."-".$get['monthstartday']);
		$startdate = date("Y-m-d",strtotime($slctdate));
		$enddate = date("Y-m-d",strtotime("+1 month",strtotime($slctdate)));  
		$enddate = date("Y-m-d", strtotime("-1 days", strtotime($enddate)));
		$query ="SELECT id,display_name,image,emp_code, joining_date, date_of_leaving 
		FROM `employees` emp 
		WHERE emp.company_id = '".$get['company_id']."' AND 
		emp.employee_type = 1 AND 
		emp.is_active = 0 AND 
		emp.id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id={$get['pay_area_id']} AND company_id={$get['company_id']} AND is_active=0) AND
		( CASE 
		WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving <= '".$enddate."') THEN 1 
		WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END) = 1 ";

		//$query="SELECT id,display_name,image  FROM `employees` WHERE company_id = '".$get['company_id']."' AND employee_type = 1 and is_active =0";
		/**if($get['admin_type']!=1){
			$query.=" AND id IN (SELECT emp_id FROM pay_area_employees WHERE pay_area_id IN (SELECT pay_area_id FROM payroll_area_admins WHERE emp_id = ".$get['emp_id']."))";
		} **/

		$returnarray['sql'] = $query;
		$empdatas = $callConfig->getAllRows($query);
		foreach ($empdatas as $value){
			$empidlist[$value->id] = array('display_name'=>$value->display_name,'image'=>$value->image,'joining_date'=>$value->joining_date,'date_of_leaving'=>$value->date_of_leaving);
		}
		$returnarray['empdatas'] = $empdatas;
		$returnarray['count'] = count((array)$empdatas);

		
		$insdailyattendance = "INSERT INTO `employee_daily_attendances`(`shift_settings_id`,`company_id`, `employee_id`, `attendace_date`, `clock_in`, `check_in_type`, `check_out_type`, `clock_out`, `is_active`, `current_status`,`created_date`, `created_by`,`attendance_key_short_code`) VALUES ";
		$insdailyattendancedetails = "INSERT INTO `employee_daily_attendance_details`(`company_id`, `employee_id`, `attendace_date`, `clock_in`, `check_in_type`, `check_out_type`, `clock_out`, `is_active`, `current_status`,`created_date`, `created_by`) VALUES ";

		$returnarray['empidlist'] = $empidlist;
		//echo "<pre>"; print_r($offdayarray);
		foreach($empidlist as $ky => $emp){
			$start = new DateTime($startdate);
			$end = new DateTime($enddate);
			$emp_details = $shiftresarray[$ky];
			$emp_shift_days = json_decode($shiftresarray[$ky]->working_days,1);
			while($start <= $end){
				$date = $start->format('Y-m-d');
				$dy = $start->format('l');
				if($emp['joining_date'] >= $date || ($emp['date_of_leaving'] <= $date && $emp['date_of_leaving'] !="")){
				
				}elseif(in_array($date,(array)$holidaysdatas)){
				//holidays				
				}elseif(isset($absentarray[$ky][$date])){
				//absent part
				}elseif(isset($offdayarray[$ky][$date])){
					$attendance_key_short_code = $offdayarray[$ky][$date]->attendance_key_short_code;	
					$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','".$instarttime."', '1', '1', '0:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
				}elseif(isset($leavearray[$ky][$date])){
				//leave part   		
					//$arrayleave = array('emp_id'=>$ky,'categorytype'=>$leavearray[$ky][$date]->leave_type_id,'leave_year'=>$get['year'],'company_id'=>$get['company_id']);
					//$getleaveid = $this->checkLeaveBalanceOrHalfDay($arrayleave);
					$halfdaysleave = 1;
					if($leavearray[$ky][$date]->start_half_day=='0' || $leavearray[$ky][$date]->start_half_day =='0'){
						$halfdaysleave = 0.5; 
						$attendance_key_short_code = $leavearray[$ky][$date]->attendance_key_short_code; 
						$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '14:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
						$insdailyattendancedetails.="('".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '14:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
					}
						//$updatequeryleave = "UPDATE emp_leave_balances SET consumed_leaves=consumed_leaves+".$halfdaysleave.",updated_date = '".date("Y-m-d H:i:s")."',updated_by = '".$get['user_id']."' WHERE employee_id =".$ky." AND leave_type='".$leavearray[$ky][$date]->leave_type_id."' AND is_active ='0' AND  leave_year =".$year;
					if($leavearray[$ky][$date]->leave_type_id !=""){
						$updatequeryleave = "UPDATE emp_leave_balances SET consumed_leaves=consumed_leaves+".$halfdaysleave.",updated_date = '".date("Y-m-d H:i:s")."',updated_by = '".$get['user_id']."' WHERE employee_id =".$ky." AND leave_type='".$leavearray[$ky][$date]->leave_type_id."'";
						$updateleave = $callConfig->executeQuery($updatequeryleave);
					}
					$returnarray['emp_leave_balances'][] =  $updateleave;
					$insert_leave_array = array('company_id'=>$get['company_id'],'employee_id'=>$ky,'leave_type_id'=>$leavearray[$ky][$date]->leave_type_id,'leave_from'=>$date,'leave_to'=>$date,'leave_days'=>$halfdaysleave,'start_half_day'=>$leavearray[$ky][$date]->start_half_day,'end_half_day'=>$leavearray[$ky][$date]->end_half_day,'created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'199');
					$insertleave=$callConfig->insertRecord('employee_leaves',$insert_leave_array);
					$returnarray['employee_leaves'][] =  $insertleave;
					//}elseif(!in_array($dy, $workingdays)){
				}elseif((!in_array($dy, (array)$workingdays) AND $shift_check == '0' AND OFF_CHECK == '0') OR (!in_array($dy, (array)$emp_shift_days) AND $shift_check == '1' AND OFF_CHECK == '0')){
				//off days
				}else{
					$starttime = $presentarray[$ky][$date]->clock_in;
					$endtime = $presentarray[$ky][$date]->clock_out;
					$instarttime = $starttime !=""?$starttime:'10:00:00';
					$outendtime = $endtime !=""?$endtime:'18:00:00';
				//working part
					$attendance_key_short_code = $presentarray[$ky][$date]->attendance_key_short_code; 	
					$insdailyattendance .= "('".$emp_details->id."','".$get['company_id']."', '".$ky."', '".$date."','".$instarttime."', '1', '1', '18:00:00','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."','".$attendance_key_short_code."'),";
					$insdailyattendancedetails.="('".$get['company_id']."', '".$ky."', '".$date."','10:00:00', '1', '1', '".$outendtime."','0','0', '".date("Y-m-d H:i:s")."', '".$get['user_id']."'),";
				}	
				$start->modify('+1 days');
			}
		}
		$insdailyattendance = substr($insdailyattendance, 0, -1);
		$insdailyattendance .= " ON DUPLICATE KEY UPDATE updated_date ='".date("Y-m-d H:i:s")."'";
		$res = $callConfig->executeQueryString($insdailyattendance);
		$returnarray['employee_daily_attendances'][] =  $insdailyattendance;
		$insdailyattendancedetails = substr($insdailyattendancedetails, 0, -1);
		$res1 = $callConfig->executeQueryString($insdailyattendancedetails);
		$returnarray['employee_daily_attendance_details'][] =  $res1;

		$insertuserarray2=array('company_id'=>$get['company_id'],'import_swap_in_out_id'=>$lastid,'month_year'=>$get['month_year'],'type'=>'S','user_id'=>$get['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s"),'pay_area_id'=>$get['pay_area_id']);
		$tapres = $callConfig->insertRecord('work_attendance_logs',$insertuserarray2);
		$returnarray['work_attendance_logs'] = $tapres;


		return $returnarray;

	}
	function getAttendanceAuditHistoryByRid($get){
		global $callConfig;
		$return = array(
			'status' => 'success',
			'message' => 'No history for this',
			'rid' => isset($get['rid']) ? $get['rid'] : '',
			'attendance' => array(),
			'audits' => array(),
			'users' => array()
		);

		$rid = isset($get['rid']) ? trim((string)$get['rid']) : '';
		$company_id = isset($get['company_id']) ? trim((string)$get['company_id']) : '';

		if($rid == '' || $company_id == ''){
			return $return;
		}

		$attendance_sql = "SELECT * FROM employee_daily_attendances WHERE company_id = '".$company_id."' AND id = '".$rid."' AND is_active = 0 LIMIT 1";
		$attendance_res = $callConfig->getRow($attendance_sql);
		$return['attendance'] = (array)$attendance_res;

		if(empty($attendance_res)){
			return $return;
		}

		$audit_sql = "SELECT * FROM audit_trails WHERE company_id = '".$company_id."' AND transaction_table = 'employee_daily_attendances' AND transaction_id = '".$rid."' AND is_active = 0 ORDER BY created_date DESC";
		$audit_res = $callConfig->getAllRows($audit_sql);
		$return['audits'] = json_decode(json_encode($audit_res), true);

		$users_sql = "SELECT id, display_name FROM ".TBL_APPLICATION_USERS." WHERE company_id = '".$company_id."' AND is_active = 0";
		$users_res = $callConfig->getAllRows($users_sql);
		foreach((array)$users_res as $user_row){
			$return['users'][$user_row->id] = $user_row->display_name;
		}

		$return['message'] = count((array)$return['audits']) > 0 ? 'Attendance history fetched successfully' : 'Attendance created record fetched successfully';
		return $return;
	}
	function getEmployeeReportData($get){
        global $callConfig;
        $return = array();

        $company_id = isset($get['company_id']) ? (int) $get['company_id'] : 0;
        if ($company_id <= 0) {
            $return['status'] = 'error';
            $return['message'] = 'Invalid company id';
            return $return;
        }

        $employee_type_query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details
            WHERE genc_code LIKE '%EMPLOYEETYPES%' AND company_id=" . $company_id . " AND is_active=0 ORDER BY genc_detail_name ASC";

        $employees_query = "SELECT id, emp_code, display_name FROM employees
            WHERE company_id=" . $company_id . " AND is_active=0 ORDER BY display_name ASC";
        $departments_query = "SELECT id, department_name FROM departments
            WHERE company_id=" . $company_id . " AND is_active=0 ORDER BY department_name ASC";
        $designations_query = "SELECT id, designation_name FROM designations
            WHERE company_id=" . $company_id . " AND is_active=0 ORDER BY designation_name ASC";
        $locations_query = "SELECT id, location_name FROM locations
            WHERE company_id=" . $company_id . " AND is_active=0 ORDER BY location_name ASC";

        $return['employees'] = (array) $callConfig->getAllRows($employees_query);
        $return['departments'] = (array) $callConfig->getAllRows($departments_query);
        $return['designations'] = (array) $callConfig->getAllRows($designations_query);
        $return['locations'] = (array) $callConfig->getAllRows($locations_query);
        $emp_types = $callConfig->getAllRows($employee_type_query);
        $emp_type_arr = array();
        foreach ($emp_types as $emp_type) {
            $emp_type_arr[(int) $emp_type->genc_detail_code] = $emp_type->genc_detail_name;
        }
        $return['employee_type_map'] = $emp_type_arr;

        $where = array();
        $where[] = "e.company_id=" . $company_id;
        $where[] = "e.is_active=0";

        $employee_id = isset($get['employee_id']) ? (int) $get['employee_id'] : 0;
        if ($employee_id > 0) {
            $where[] = "e.id=" . $employee_id;
        }

        $department_id = isset($get['department_id']) ? (int) $get['department_id'] : 0;
        if ($department_id > 0) {
            $where[] = "e.department_id=" . $department_id;
        }

        $designation_id = isset($get['designation_id']) ? (int) $get['designation_id'] : 0;
        if ($designation_id > 0) {
            $where[] = "e.designation_id=" . $designation_id;
        }

        $location_id = isset($get['location_id']) ? (int) $get['location_id'] : 0;
        if ($location_id > 0) {
            $where[] = "e.location_id=" . $location_id;
        }

        $employee_type = isset($get['employee_type']) ? trim((string) $get['employee_type']) : '';
        if ($employee_type !== '') {
            $where[] = "e.employee_type='" . addslashes($employee_type) . "'";
        }

        $employee_status = isset($get['employee_status']) ? strtolower(trim((string) $get['employee_status'])) : '';
        if ($employee_status === 'active') {
            $where[] = "(e.date_of_leaving IS NULL OR e.date_of_leaving='' OR e.date_of_leaving='0000-00-00')";
        } elseif ($employee_status === 'resigned') {
            $where[] = "(e.date_of_leaving IS NOT NULL AND e.date_of_leaving<>'' AND e.date_of_leaving<>'0000-00-00')";
        }

        $joining_date_from = isset($get['joining_date_from']) ? trim((string) $get['joining_date_from']) : '';
        $joining_date_to = isset($get['joining_date_to']) ? trim((string) $get['joining_date_to']) : '';
        if ($joining_date_from !== '' && $joining_date_to !== '' && $joining_date_from > $joining_date_to) {
            $return['status'] = 'error';
            $return['message'] = 'Join Date From should be less than or equal to Join Date To.';
            $return['report_rows'] = array();
            return $return;
        }

        if ($joining_date_from !== '') {
            $where[] = "DATE(e.joining_date) >= '" . addslashes($joining_date_from) . "'";
        }

        if ($joining_date_to !== '') {
            $where[] = "DATE(e.joining_date) <= '" . addslashes($joining_date_to) . "'";
        }

        $relieving_date_from = isset($get['relieving_date_from']) ? trim((string) $get['relieving_date_from']) : '';
        $relieving_date_to = isset($get['relieving_date_to']) ? trim((string) $get['relieving_date_to']) : '';
        if ($relieving_date_from !== '' && $relieving_date_to !== '' && $relieving_date_from > $relieving_date_to) {
            $return['status'] = 'error';
            $return['message'] = 'Relieving Date From should be less than or equal to Relieving Date To.';
            $return['report_rows'] = array();
            return $return;
        }

        if ($relieving_date_from !== '') {
            $where[] = "(e.date_of_leaving IS NOT NULL AND e.date_of_leaving<>'' AND e.date_of_leaving<>'0000-00-00' AND DATE(e.date_of_leaving) >= '" . addslashes($relieving_date_from) . "')";
        }

        if ($relieving_date_to !== '') {
            $where[] = "(e.date_of_leaving IS NOT NULL AND e.date_of_leaving<>'' AND e.date_of_leaving<>'0000-00-00' AND DATE(e.date_of_leaving) <= '" . addslashes($relieving_date_to) . "')";
        }

        $report_query = "SELECT
                e.id,
                e.emp_code,
                e.display_name,
                e.phone,
                e.employee_type,
                e.joining_date,
                e.date_of_leaving,
                d.department_name,
                dg.designation_name,
                l.location_name
            FROM employees e
            LEFT JOIN departments d ON d.id = e.department_id AND d.company_id = e.company_id AND d.is_active = 0
            LEFT JOIN designations dg ON dg.id = e.designation_id AND dg.company_id = e.company_id AND dg.is_active = 0
            LEFT JOIN locations l ON l.id = e.location_id AND l.company_id = e.company_id AND l.is_active = 0
            WHERE " . implode(' AND ', $where) . "
            ORDER BY e.display_name ASC";

        $report_data = (array) $callConfig->getAllRows($report_query);
        $report_rows = array();
        foreach ($report_data as $row) {
            $employee_type_val = $row->employee_type;
            $employee_type_label = isset($emp_type_arr[(int) $employee_type_val])
                ? $emp_type_arr[(int) $employee_type_val]
                : (string) $employee_type_val;

            $is_resigned = (
                isset($row->date_of_leaving)
                && $row->date_of_leaving !== null
                && trim((string) $row->date_of_leaving) !== ''
                && trim((string) $row->date_of_leaving) !== '0000-00-00'
            );

            $report_rows[] = array(
                'id' => $row->id,
                'emp_code' => $row->emp_code,
                'display_name' => $row->display_name,
                'phone' => $row->phone,
                'employee_type' => $employee_type_val,
                'employee_type_label' => $employee_type_label,
                'department' => $row->department_name,
                'designation' => $row->designation_name,
                'location' => $row->location_name,
                'employee_status' => $is_resigned ? 'Resigned' : 'Active',
                'joining_date' => $row->joining_date,
                'date_of_leaving' => $row->date_of_leaving
            );
        }

        $return['status'] = 'success';
        $return['report_rows'] = $report_rows;
        return $return;
    }
    function togetemployeesassementyear($get){
    	global $callConfig;
    	$query = "SELECT id, emp_code, display_name FROM employees WHERE company_id=".$get['company_id']." AND is_active=0";
    	$emp_data = $callConfig->getAllRows($query);
    	$return['emp_data'] = $emp_data;
    	$assessmentYears = [];
	    $currentYear = date('Y');
	    $currentMonth = date('n');

	    // Assessment year starts from April
	    if ($currentMonth >= 4) {
	        $startYear = $currentYear;
	    } else {
	        $startYear = $currentYear - 1;
	    }

	    for ($i = 0; $i < 5; $i++) {
	        $assessmentYears[] = ($startYear - $i) . '-' . ($startYear - $i + 1);
	    }
	    $return['assessment_years'] = $assessmentYears;
    	return $return;
    }
    function togetdocumentlistfile($get){
    	global $callConfig;
    	$whr = "";
    	if(isset($get['assessment_year']) && $get['assessment_year']!=""){
    		$whr.= " AND d.assessment_year='".$get['assessment_year']."'";
    	}
    	if(isset($get['semp_id']) && $get['semp_id']!=""){
    		$whr.= " AND d.emp_id=".$get['semp_id'];
    	}
    	$query = "SELECT e.display_name, d.assessment_year, d.emp_id, dl.document_name, d.file FROM emp_assign_release_documents d INNER JOIN employees e ON e.id = d.emp_id INNER JOIN document_lists dl ON dl.id = d.document_list_id WHERE d.company_id = ".$get['company_id']." ".$whr;
    	$return['query'] = $query;
    	$rows = $callConfig->getAllRows($query);
    	$return['data'] = $rows;
    	return $return;
    }
    function multistaticinsertEmployeeTerminations($get,$post){
		global $callConfig;
		
		$query = "SELECT id, user_id FROM `employees` WHERE `emp_code` IN ('SM2','SM16','SM84','SM230','SM252','SM278','SM366','SM368','SM425','SM522','SM547','SM558','SM587','SM589','SM599','SM614','SM742','SM743','SM749','SM752','SM814','SM824','SM838','SM891','SM924','SM929','SM933','SM939','SM962','SM977','SM1014','SM1019','SM1028','SM1043','SM1057','SM1059','SM1066','SM1072','SM1075','SM1082','SM1088','SM1089','SM1096','SM1098','SM1109','SM1128','SM1134','SM1141','SM514','SM1160','SM1163','SM1168','SM1174','SM1181','SM1182','SM1185','SM1186','SM1188','SM1191','SM1200','SM1204','SM1210','SM1216','SM1219','SM1224','SM1227','SM1230','SM1236','SM1237','SM1239','SM1240','SM1244','SM1247','SM1249','SM1250','SM1255','SM1256','SM1261','SM1269','SM1278','SM1279','SM1314','SM1304','SM1328','SM1330')";
		$rows= $callConfig->getAllRows($query); 
		foreach($rows as $row){
			$emp_id = $row->id;
			$user_id = $row->user_id;
			$inserttermination = array('company_id'=>'1','emp_id'=>$emp_id,'termination_type'=>'RESIGNED','termination_reason'=>'RESIGNED','effective_date'=>'2026-08-31','employee_leaving_date'=>'2026-08-31','is_active'=>'0','current_status'=>'199','created_by'=>1);
			$res[$emp_id]['employee_terminations'][]=$callConfig->insertRecord('employee_terminations',$inserttermination);

			$updaterecord = array('date_of_leaving'=>'2026-08-31');
			$res[$emp_id]['employees'][]= $callConfig->updateRecord('employees',$updaterecord,'id',$emp_id);
			$res[$emp_id]['application_users'][] = $callConfig->updateRecord('application_users',array('is_active'=>'1'),'id',$user_id);
		}
		return $res;
	}
}
?>