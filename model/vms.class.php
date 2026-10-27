<?php 
class vMSUserClass extends userClass{
	function toGetEmployessWorkorderswthTimesheet($get){
		global $callConfig;
		$query = "select distinct mpc.id, mpc.transaction_number, (select display_name from vendors where id = mpc.customer_id) vendor_name, mpc.customer_id vendor_id, emp.display_name, emp.id as emp_id, emp.image,emp.email_id,( select job_title from job_titles where id = emp.job_title_id) designation, item_price monthlyrate, pt.timesheet_date, pt.billable_hrs, (item_price/160) *pt.billable_hrs chargable_amount from manpower_contracts mpc, mpc_items, employees emp, emp_work_order ewo, project_timesheets pt WHERE mpc.company_id = mpc_items.company_id AND mpc.id = mpc_items.transaction_id AND mpc.id = 4 AND emp.company_id =ewo.company_id AND emp.id = ewo.emp_id AND ewo.work_order_id = mpc.id AND emp.job_title_id = mpc_items.item_id AND pt.company_id = ewo.company_id AND pt.work_order = ewo.work_order_id AND pt.emp_id = emp.id order by 1";
		$returns = $callConfig->getAllRows($query);
		$returns = (array)$returns;
		//print_r($returns);
		foreach($returns as $return){
			$return = (array)$return;
			$month_year = date('Y-m',strtotime($return['timesheet_date']));
			if($data[$return['emp_id']]['id']==""){
				$data[$return['emp_id']] = $return;
			}
			$data[$return['emp_id']][$month_year]['chargable_amount']=$return['chargable_amount'];
			$data[$return['emp_id']][$month_year]['billable']=$return['billable_hrs'];
		}
		return $data;
	}
	function togetverticaldata($get){
		global $callConfig;
		$query="select emp.vertical_id , (select vertical_name from verticals vlc where vlc.id = emp.vertical_id) verticallame, sum(pt.billable_hrs)/160 billabehours, SUM((item_price/160) *pt.billable_hrs) chargable_amount from manpower_contracts mpc, mpc_items, employees emp, emp_work_order ewo, project_timesheets pt WHERE mpc.company_id = mpc_items.company_id AND mpc.id = mpc_items.transaction_id AND emp.company_id =ewo.company_id AND emp.id = ewo.emp_id AND ewo.work_order_id = mpc.id AND emp.job_title_id = mpc_items.item_id AND (pt.timesheet_date BETWEEN mpc_items.effective_date and mpc_items.expire_date) AND pt.company_id = ewo.company_id AND pt.work_order = ewo.work_order_id AND pt.emp_id = emp.id GROUP BY verticallame";
		$returns = $callConfig->getAllRows($query);
	   // print_r($returns);
		$total_billabe = 0;  $total_charge = 0;
		foreach($returns as $return){
			$total_billabe = $total_billabe+$return->billabehours;
			$total_charge = $total_charge+$return->chargable_amount;
		}
	    // echo $total_billabe."#".$total_charge;
		foreach($returns as $return){
			$return->bill = number_format(($return->billabehours/$total_billabe)*100,1);
			$return->charg = number_format(($return->chargable_amount/$total_charge)*100,1);
			$data[] =  $return;
		}
		return $data;
	}	
	function togetcoscentergraphdata($get){
		global $callConfig;
		$query="select DATE_FORMAT(pt.timesheet_date, '%Y-%m') as monthdate, (select cost_center_name from cost_centers cn where cn.id = au.cost_center) cost_center_name, au.cost_center, sum(pt.billable_hrs)/160 billabehours, SUM((item_price/160) *pt.billable_hrs)/1000 chargable_amount from manpower_contracts mpc, mpc_items, employees emp, emp_work_order ewo, project_timesheets pt, application_users au WHERE mpc.company_id = mpc_items.company_id AND mpc.id = mpc_items.transaction_id AND emp.company_id =ewo.company_id AND emp.id = ewo.emp_id AND ewo.work_order_id = mpc.id AND emp.job_title_id = mpc_items.item_id AND emp.user_id = au.id AND emp.company_id = au.company_id AND (pt.timesheet_date BETWEEN mpc_items.effective_date and mpc_items.expire_date) AND pt.company_id = ewo.company_id AND pt.work_order = ewo.work_order_id AND pt.emp_id = emp.id GROUP BY monthdate , au.cost_center";
		$returns = $callConfig->getAllRows($query);
		return $returns;
	}
	function togetvendorgraphdata($get){
		global $callConfig;
		$query="select DATE_FORMAT(pt.timesheet_date, '%Y-%m') as monthdate, ( select vn.display_name from vendors vn where vn.id = emp.vendor_id ) vendor_name, emp.vendor_id, sum(pt.billable_hrs)/ 160 billabehours, SUM( (item_price / 160) * pt.billable_hrs )/1000 chargable_amount from manpower_contracts mpc, mpc_items, employees emp, emp_work_order ewo, project_timesheets pt WHERE mpc.company_id = mpc_items.company_id AND mpc.id = mpc_items.transaction_id AND emp.company_id = ewo.company_id AND emp.id = ewo.emp_id AND ewo.work_order_id = mpc.id AND emp.job_title_id = mpc_items.item_id AND ( pt.timesheet_date BETWEEN mpc_items.effective_date and mpc_items.expire_date ) AND pt.company_id = ewo.company_id AND pt.work_order = ewo.work_order_id AND pt.emp_id = emp.id GROUP BY monthdate, vendor_name,emp.vendor_id";
		$returns = $callConfig->getAllRows($query);
		return $returns;
	}
	function togetteammeberattdences($get){
		global $callConfig;
		$query="select emp.id, emp.display_name, eda.attendace_date,eda.clock_in, eda.clock_out from employee_daily_attendances eda, employees emp, application_users au WHERE eda.employee_id = emp.id AND emp.user_id = au.id AND au.reports_to = ".$get['user_id']." GROUP BY eda.attendace_date,emp.id";
		$returns = $callConfig->getAllRows($query);
		return $returns;
	}
	function togetteammeberemployeeids($get){
		global $callConfig;
		$query="select emp.id emp_id, emp.display_name, from  employees emp, application_users au WHERE  emp.user_id = au.id AND au.reports_to = ".$get['user_id']." GROUP BY emp.id";
		$returns = $callConfig->getAllRows($query);
		return $returns;
	}
	function togetvendorwiseprojecttimesheetdata($get){
		global $callConfig;
		$query = " SELECT vendor_id, vendor_name, employee_id, employee_name, SUM(billable_hrs)/8 billable_hrs,SUM(nonbillable_hrs)/8 nonbillable_hrs,leave_days_from + leave_days_to as leave_days_from_to , current_status, sum(item_price) budgeted_amount, SUM(billable_hrs * (item_price/(20*8))) as billable_amount,
		SUM(nonbillable_hrs * (item_price/(20*8))) as nonbillable_amount,exp, job_title from ( SELECT
		emp.vendor_id as vendor_id,
		vend.display_name as vendor_name,
		emp.id employee_id, (DATEDIFF (now(), emp.first_working_date)/365) exp,(
		select job_title
		from job_titles jt
		where jt.id = emp.job_title_id
		and jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		(
		SELECT SUM(billable_hrs)
		FROM project_timesheets pt
		WHERE pt.company_id = emp.company_id
		AND pt.emp_id = emp.id
		AND MONTH(pt.timesheet_date) = '04'
		AND YEAR(pt.timesheet_date) = YEAR(CURRENT_DATE())
		)billable_hrs,
		(
		SELECT SUM(nonbillable_hrs)
		FROM project_timesheets pt
		WHERE pt.company_id = emp.company_id
		AND pt.emp_id = emp.id
		AND MONTH(pt.timesheet_date) = '04'
		AND YEAR(pt.timesheet_date) = YEAR(CURRENT_DATE())
		)nonbillable_hrs,
		vrcitems.item_price,
			-- SUM(pt.billable_hrs) as billable_hours,
			-- SUM(pt.nonbillable_hrs) as nonbillable_hrs,
			-- SUM(vrcitems.item_price) budgeted_amount,
			-- SUM(pt.billable_hrs * (vrcitems.item_price/(20*8))) as billable_amount,
			-- SUM(pt.nonbillable_hrs * (vrcitems.item_price/(20*8))) as nonbillable_hrs,
			(
			SELECT DATEDIFF (least(el.leave_to,last_day(el.leave_from)),el.leave_from)
			FROM employee_leaves el
			WHERE el.company_id = emp.company_id
			AND el.employee_id = emp.id
			AND MONTH(el.leave_from) = '04'
			AND YEAR(el.leave_from) = YEAR(CURRENT_DATE())
			) leave_days_from,
			(
			SELECT DATEDIFF (el2.leave_to, greatest(el2.leave_from,DATE_SUB(LAST_DAY(el2.leave_to),INTERVAL DAY(LAST_DAY(el2.leave_to))-
			1 DAY)))
			FROM employee_leaves el2
			WHERE el2.company_id = emp.company_id
			AND el2.employee_id = emp.id
			AND MONTH(el2.leave_to) = '04'
			AND YEAR(el2.leave_to) = YEAR(CURRENT_DATE())
			) leave_days_to,
			(
			SELECT sts.name
			FROM status sts
			WHERE sts.name_value = emp.current_status
			AND sts.company_id = emp.company_id
			) current_status
			FROM employees emp, vendors vend, manpower_contracts vrc, mpc_items vrcitems, emp_work_order ewo
			WHERE emp.company_id = 1
			AND emp.is_active = '0'
			AND vend.company_id = emp.company_id
			AND vend.id = emp.vendor_id
			AND vrc.company_id = emp.company_id
			AND vrcitems.company_id = vrc.company_id
			AND vrcitems.transaction_id = vrc.id
			AND vrc.customer_id = emp.vendor_id
			AND ewo.company_id = emp.company_id
			AND ewo.emp_id = emp.id
			AND vrcitems.transaction_id = ewo.work_order_id
			AND vrcitems.id = ewo.work_order_detail_id
		)abc GROUP by vendor_id, vendor_name, employee_id, employee_name, current_status";
		$employee_list = $callConfig->getAllRows($query);		
		return $employee_list;
	}
	function customadmdashboardmodule3($get){
		global $callConfig;
		$company_id = $get['company_id'];
		//$months = strtotime("-11 month", strtotime($get['fromdate']));
		$months = strtotime($get['fromdate']);
		$end = strtotime($get['todate']);
		$condition = "";
		if($get['admin_type'] != '1'){
			$condition = "  AND au.reports_to ='".$get['user_id']."'";
		} 
		while($months <= $end)
		{
			$yy= date('Y', $months);
			$mm= date('m', $months);
			$yymm= date('Y-m', $months);
			$startdate= date('Y-m'.'-01', $months);
			$enddate= date('Y-m-t', $months);
			$query="SELECT
			company_id,
			vendor_id,
			vendor_name,
			vertical_id,
			verticle_name,
			cost_center_id,
			cost_center_name,
			department_id,
			department_name,
			employee_id,
			user_id,
			display_name,
			job_title_id,
			job_title,
			SUM(po_amt) po_total,
			SUM(ratecard_amt) actual_total
			FROM
			(
			SELECT emp.company_id,
			emp.vendor_id,
			(
			SELECT vend.display_name
			FROM vendors vend
			WHERE vend.company_id = emp.company_id
			AND vend.id = emp.vendor_id
			) vendor_name,
			emp.vertical_id,
			(
			SELECT vert.vertical_name
			FROM verticals vert
			WHERE vert.company_id = emp.company_id
			AND vert.id = emp.vertical_id
			) verticle_name,
			emp.cost_center_id,
			(
			SELECT cc.cost_center_name
			FROM cost_centers cc
			WHERE cc.company_id = emp.company_id
			AND cc.id = emp.cost_center_id
			) cost_center_name,
			emp.department_id,
			(
			SELECT dept.department_name
			FROM departments dept
			WHERE dept.company_id = emp.company_id
			AND dept.id = emp.department_id
			) department_name,
			emp.id employee_id,
			emp.user_id,
			emp.display_name,
			emp.job_title_id,
			(
			SELECT jt.job_title
			FROM job_titles jt
			WHERE jt.company_id = emp.company_id
			AND jt.id = emp.job_title_id
			) job_title,
			pt.bill_amount AS po_amt,
			pt.vrc_billable_amount AS ratecard_amt
			FROM employees emp, project_timesheets pt, application_users au
			WHERE emp.company_id = pt.company_id
			AND emp.id = pt.emp_id
			AND emp.joining_date <= '".$enddate."' 
			AND (emp.date_of_leaving >= '".$startdate."' OR emp.date_of_leaving IS NULL)
			AND emp.company_id = '".$company_id."'
			AND MONTH(pt.timesheet_date) = '".$mm."'
			AND YEAR(pt.timesheet_date) = '".$yy."'
			AND emp.company_id = au.company_id
			AND emp.user_id = au.id ".$condition."
			AND pt.is_active = 0
			)a
			GROUP BY company_id,
			vendor_id,
			vendor_name,
			vertical_id,
			verticle_name,
			cost_center_id,
			cost_center_name,
			department_id,
			department_name,
			employee_id,
			user_id,
			display_name,
			job_title_id,
			job_title";
			$query111[] = $query;
			$employee_list = $callConfig->getAllRows($query);	
			//$employee_lists[$i] = array();
			$employee_lists[$yymm]=$employee_list;	
			$months = strtotime("+1 month", $months);
		}
			//echo "<pre>"; print_r($query111);
		return $employee_lists;
	}	
	function savevendoragreements($post, $get) {
		global $callConfig;
		$postdata['company_id'] = $post['company_id'];
		$postdata['vendor_id'] = $post['vendor_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		//print_r($post);exit;
		foreach ($post['ag_type'] as $agkey => $ag) { 
			$postdata['agreement_name'] = $post['ag_type'][$agkey];
			$postdata['agreement_ref_no'] = $post['ag_number'][$agkey];
			$postdata['signed_date'] = $post['ag_sin_date'][$agkey];
			$postdata['expiry_date'] = $post['ag_ex_date'][$agkey];
			$postdata['file_url'] = $post['file_url'][$agkey];
			//print_r($postdata); 
			if(!empty($post['ag_rowid'][$agkey]))
			{
    		// update
				$row_id = $post['ag_rowid'][$agkey];
				$postdata['updated_date'] = $post['updated_date'];
				$postdata['updated_by'] = $post['updated_by'];
				$res=$callConfig->updateRecord(TBL_VENDOR_AGREEMENTS,$postdata,'id',$row_id);
			}else {
    		//insert
			//echo "<pre>";print_r($postdata);
				$postdata['created_date'] = $post['created_date'];
				$postdata['created_by'] = $post['created_by'];
				$res=$callConfig->insertRecord(TBL_VENDOR_AGREEMENTS,$postdata);
			}

		}
	}
	function bugetvendorrareports($get){
		global $callConfig;
		if($get['fromdate']!=""){
		$ym = explode("-", $get['fromdate']);
		$month = $ym[1];
		$year = $ym[0];
		}else{
		$month = date('m');
		$year = date('Y');
		}
		$query="SELECT DISTINCT
		vendor_id,
		vendor_name,
		contact_person,
		employee_id,
		employee_name,
		department_id,
		location_id,
		vertical_id,
		cost_center_id,
		job_title,
		emp_billable,
		billable_hrs,
		nonbillable_hrs,
		leave_days_from + leave_days_to AS leave_days,
		experience_base,
		current_status,
		l_item_price budgeted_amount,
		l_billable_amount billable_amount,
		vrc_item_price vrc_budgeted_amount,
		vrc_billable_amount, 
		(
		SUM(nonbillable_hrs) *(
		SUM(l_billable_amount) / SUM(billable_hrs)
		)
		) AS nonbillable_amount,
		(
		DATEDIFF(NOW(), `first_working_date`) / 365) exp_first_working,
		(
		total_experiance +(
		DATEDIFF(NOW(), `joining_date`) / 365)
		) exp_joining_date,
		min_exp,
		max_exp,
		mpc_uom,
		vrc_uom,
		joining_date,
		total_experiance,
		CASE WHEN(
		total_experiance +(
		DATEDIFF(NOW(), `joining_date`) / 365)
		) BETWEEN min_exp AND max_exp THEN '' ELSE 'Experiance Missmatch'
		END remark
		FROM
		(
		SELECT
		emp.vendor_id AS vendor_id,
		vend.display_name AS vendor_name,
		vend.contact_person,
		emp.id employee_id,
		emp.first_working_date,
		COALESCE(emp.joining_date, CURRENT_DATE) AS joining_date,
		emp.department_id AS department_id,
		emp.location_id AS location_id,
		emp.vertical_id AS vertical_id,
		emp.cost_center_id AS cost_center_id,
		emp.billable AS emp_billable,
		COALESCE(emp.total_experiance, '0') AS total_experiance,
		mpcitems.purchase_uom as mpc_uom,
		vriitems.purchase_uom as vrc_uom, 
		vriitems.experience_base,
		(
		SELECT
		job_title
		FROM
		job_titles jt
		WHERE
		jt.id = emp.job_title_id AND jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		(
		SELECT
		SUM(billable_hrs)
		FROM
		project_timesheets pt
		WHERE
		pt.company_id = emp.company_id AND pt.emp_id = emp.id AND MONTH(pt.timesheet_date) = '".$month."'  AND YEAR(pt.timesheet_date) = '".$year."'
		) billable_hrs,
		(
		SELECT
		SUM(nonbillable_hrs)
		FROM
		project_timesheets pt
		WHERE
		pt.company_id = emp.company_id AND pt.emp_id = emp.id AND MONTH(pt.timesheet_date) = '".$month."'  AND YEAR(pt.timesheet_date) = '".$year."'
		) nonbillable_hrs,
		(
		SELECT
		SUM(bill_amount)
		FROM
		project_timesheets pt
		WHERE
		pt.company_id = emp.company_id AND pt.emp_id = emp.id AND MONTH(pt.timesheet_date) = '".$month."'  AND YEAR(pt.timesheet_date) = '".$year."'
		) l_billable_amount,
		CASE WHEN mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price
		END l_item_price,
		(
		SELECT
		SUM(vrc_billable_amount)
		FROM
		project_timesheets pt
		WHERE
		pt.company_id = emp.company_id AND pt.emp_id = emp.id AND MONTH(pt.timesheet_date) = '".$month."' AND YEAR(pt.timesheet_date) = '".$year."'
		) vrc_billable_amount,
		CASE WHEN vriitems.purchase_uom = 'PT' THEN vriitems.item_price / 3 WHEN vriitems.purchase_uom = 'PS' THEN vriitems.item_price / 6 WHEN vriitems.purchase_uom = 'PY' THEN vriitems.item_price / 12 ELSE vriitems.item_price
		END vrc_item_price,
		(
		SELECT
		DATEDIFF(
		LEAST(
		el.leave_to,
		last_day(el.leave_from)
		),
		el.leave_from
		)
		FROM
		employee_leaves el
		WHERE
		el.company_id = emp.company_id AND el.employee_id = emp.id AND MONTH(el.leave_from) = '".$month."'  AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from,
		(
		SELECT
		DATEDIFF(
		el2.leave_to,
		GREATEST(
		el2.leave_from,
		DATE_SUB(
		LAST_DAY(el2.leave_to),
		INTERVAL DAY(LAST_DAY(el2.leave_to)) - 1 DAY
		)
		)
		)
		FROM
		employee_leaves el2
		WHERE
		el2.company_id = emp.company_id AND el2.employee_id = emp.id AND MONTH(el2.leave_to) = '".$month."'  AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to,
		vriitems.min_exp,
		vriitems.max_exp,
		(
		SELECT
		sts.name
		FROM
		status
		sts
		WHERE
		sts.name_value = emp.current_status AND sts.company_id = emp.company_id
		) current_status
		FROM
		employees emp,
		vendors vend,
		manpower_contracts vrc,
		mpc_items mpcitems,
		emp_work_order ewo,
		vrc_items vriitems,
		eba_vendors_allocations ebava,
		application_users au
		WHERE
		emp.company_id = 1 AND emp.is_active = '0' AND vend.company_id = emp.company_id AND vend.id = emp.vendor_id AND vrc.company_id = emp.company_id AND mpcitems.company_id = vrc.company_id AND mpcitems.transaction_id = vrc.id AND vrc.customer_id = emp.vendor_id AND ewo.company_id = emp.company_id AND ewo.emp_id = emp.id AND mpcitems.transaction_id = ewo.work_order_id AND mpcitems.id = ewo.work_order_detail_id AND vriitems.company_id = ewo.company_id AND vriitems.transaction_id = ewo.vrc_id AND vriitems.id = ewo.vrc_item_id AND ebava.company_id = ewo.company_id AND ewo.is_active = ebava.is_active AND vrc.is_active = vriitems.is_active AND ewo.is_active = vrc.is_active AND emp.company_id = au.company_id AND au.id = emp.user_id AND CASE WHEN 3 != '".$get['user_type']."' THEN(
		('1' = '".$get['admin_type']."') 
		OR(au.reports_to = '".$get['user_id']."') 
		OR(emp.hr_buddy ='".$get['emp_id']."') 	
		OR(emp.finance_buddy = '".$get['emp_id']."')
		OR(emp.admin_buddy  ='".$get['emp_id']."') 	
		OR(emp.project_buddy  = '".$get['emp_id']."')
		OR(emp.loc_buddy   = '".$get['emp_id']."')
		)

		) ELSE(
		vend.id =(
		SELECT
		id
		FROM
		vendors ven2
		WHERE
		ven2.email = '".$get['user_email']."'
		)
		)
		END
		) abc
		GROUP BY
		vendor_id,
		vendor_name,
		l_item_price,
		employee_id,
		employee_name,
		job_title,
		current_status,
		min_exp,
		max_exp,
		mpc_uom,
		vrc_uom
		,vrc_item_price,experience_base";
		//echo $query; exit;
		$return = $callConfig->getAllRows($query);
		return $return;
	}
	function bugetvendorrareport_newold($get){
		global $callConfig;
		if($get['fromdate']!=""){
		$ym = explode("-", $get['fromdate']);
		$month = $ym[1];
		$year = $ym[0];
		}else{
		$month = date('m');
		$year = date('Y');
		}
		$emp_id = $get['employee_id']!="" ? $get['employee_id'] : $get['emp_id'];
		$user_id = $get['application_user_id']!="" ? $get['application_user_id'] : $get['user_id'];
		$startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
		$enddate = date("Y-m-t",strtotime($year."-".$month."-01"));
		$query="SELECT DISTINCT	emp.vendor_id,
		IFNULL( (
		    SELECT	vend.display_name
		    FROM	vendors vend
		    WHERE	vend.company_id = emp.company_id
		    AND		vend.id = emp.vendor_id
		), 'Uassigned') vendor_name, 
		emp.id as employee_id, 
		emp.display_name as employee_name, 
		emp.department_id,
		emp.location_id,
		emp.vertical_id,
		(
		    SELECT	dept.department_name	
		    FROM 	departments dept
		    WHERE	dept.company_id = emp.company_id
		    AND		dept.id = emp.department_id
		)department_name,
		au.cost_center as cost_center_id,
		(
		    SELECT	cc.cost_center_name
		    FROM 	cost_centers cc
		    WHERE	cc.company_id = au.company_id
		    AND		cc.id = au.cost_center
		)cost_center_name,
		emp.job_title_id as job_title,
		(
		    SELECT	jt.job_title
		    FROM 	job_titles jt
		    WHERE	jt.company_id = emp.company_id
		    AND		jt.id = emp.job_title_id
		) job_title,
		emp.billable as emp_billable,
		empptsumm.billable_hrs, 
		empptsumm.nonbillable_hrs,
		empleaves.leave_days,
		vrc_vrcitems.experience_base,
		(
			SELECT	sts.name
			FROM	status sts
			WHERE	sts.company_id = emp.company_id
			AND 	sts.name_value = emp.current_status 
		)current_status,
		(
		    SELECT		
		    	CASE 
		            WHEN mpcitems.purchase_uom = 'PT' 
		                THEN mpcitems.item_price / 3 
		            WHEN mpcitems.purchase_uom = 'PS' 
		                THEN mpcitems.item_price / 6 
		            WHEN mpcitems.purchase_uom = 'PY' 
		                THEN mpcitems.item_price / 12 
		            ELSE mpcitems.item_price
				END
		    FROM	manpower_contracts mpc,	mpc_items mpcitems
		    WHERE	mpc.company_id = emp.company_id
		    AND		mpc.company_id = mpcitems.company_id
		    AND		mpc.id = mpcitems.transaction_id
		    AND		mpc.id = empwo.work_order_id
		AND		mpcitems.id = empwo.work_order_detail_id
		AND mpcitems.effective_date <=  '".$startdate."'	
		AND mpcitems.expire_date >= '".$enddate."'
		)budgeted_amount,
		empptsumm.billable_amount,
		(
		    SELECT		
		    	CASE 
		            WHEN vrcitems.purchase_uom = 'PT' 
		                THEN vrcitems.item_price / 3 
		            WHEN vrcitems.purchase_uom = 'PS' 
		                THEN vrcitems.item_price / 6 
		            WHEN vrcitems.purchase_uom = 'PY' 
		                THEN vrcitems.item_price / 12 
		            ELSE vrcitems.item_price
				END
		    FROM	vendor_rate_cards vrc,	vrc_items vrcitems
		    WHERE	vrc.company_id = emp.company_id
		    AND		vrc.company_id = vrcitems.company_id
		    AND		vrc.id = vrcitems.transaction_id
		    AND		vrc.id = empwo.vrc_id
		AND		vrcitems.id = empwo.vrc_item_id
		AND vrc.effective_from_date  <=  '".$startdate."'	
		AND vrc.effective_to_date >= '".$enddate."'
		 )vrc_budgeted_amount,
		vrc_billable_amount,
		(empptsumm.nonbillable_hrs * empptsumm.rate_per_hrs) as nonbillable_amount,

		(
		    emp.total_experiance +(
		        DATEDIFF(NOW(), emp.joining_date) / 365)
		)exp_joining_date,
		vrc_vrcitems.min_exp,
		vrc_vrcitems.max_exp,
		mpc_mpcitems.mpcitm_purchase_uom as mpc_uom,
			vrc_vrcitems.vrcitm_purchase_uom as vrc_uom,
		emp.joining_date,
		COALESCE(emp.total_experiance, '0') AS total_experiance,
		CASE WHEN(
		        total_experiance +(
		            DATEDIFF(NOW(), `joining_date`) / 365)
		        ) BETWEEN min_exp AND max_exp THEN '' ELSE 'Experiance Missmatch'
			END remark,
		au.reports_to,    
			empwo.work_order_id,
		empwo.work_order_detail_id,
		empwo.vrc_id,
		empwo.vrc_item_id,
		mpc_mpcitems.mpc_item_price,	
		vrc_vrcitems.vrc_item_price
		FROM	employees emp
		LEFT JOIN (	SELECT	pt.company_id, pt.emp_id, pt.work_order, SUM(billable_hrs) billable_hrs, SUM(bill_amount) billable_amount, SUM(nonbillable_hrs) nonbillable_hrs, SUM(vrc_billable_amount) vrc_billable_amount, round(pt.rate_per_hrs) as rate_per_hrs
				FROM	project_timesheets pt
				WHERE	pt.is_active = 0 AND pt.timesheet_date >='".$startdate."' AND pt.timesheet_date <='".$enddate."'
		      	GROUP BY pt.company_id, pt.emp_id, pt.work_order, round(pt.rate_per_hrs)
			)empptsumm on (	empptsumm.company_id = emp.company_id AND empptsumm.emp_id = emp.id )
		JOIN application_users au on ( emp.company_id = au.company_id and emp.user_id = au.id)
		LEFT JOIN  (	SELECT	el.company_id, el.employee_id, el.leave_from, el.leave_to,
						DATEDIFF(LEAST(el.leave_to,last_day(el.leave_from)),el.leave_from)+ 
						DATEDIFF(el.leave_to,GREATEST(el.leave_from,DATE_SUB(LAST_DAY(el.leave_to),INTERVAL DAY(LAST_DAY(el.leave_to)) - 1 DAY))) leave_days
				FROM	employee_leaves el
				WHERE	((el.leave_from >='".$startdate."' AND el.leave_from <='".$enddate."') OR (el.leave_to>='".$startdate."' AND el.leave_to <='".$enddate."'))
			)empleaves on ( empleaves.company_id = emp.company_id AND empleaves.employee_id = emp.id )
		LEFT JOIN	(
						SELECT	ewo.company_id, ewo.emp_id, ewo.work_order_id, ewo.work_order_detail_id, ewo.vrc_id, ewo.vrc_item_id,effective_from_date, effective_to_date
						FROM	emp_work_order ewo
					)empwo on ( empwo.company_id = emp.company_id AND empwo.emp_id = emp.id AND empptsumm.work_order = empwo.work_order_id AND empwo.effective_from_date <='".$startdate."' AND empwo.effective_to_date >='".$enddate."' )  
		LEFT JOIN	(
						SELECT mpc.company_id, mpc.id work_order_id, mpcitems.id work_order_detail_id, mpcitems.purchase_uom mpcitm_purchase_uom,		
							CASE 
								WHEN mpcitems.purchase_uom = 'PT' 
									THEN mpcitems.item_price / 3 
								WHEN mpcitems.purchase_uom = 'PS' 
									THEN mpcitems.item_price / 6 
								WHEN mpcitems.purchase_uom = 'PY' 
									THEN mpcitems.item_price / 12 
								ELSE mpcitems.item_price
							END mpc_item_price
						FROM	manpower_contracts mpc,	mpc_items mpcitems
						WHERE	mpc.company_id = mpcitems.company_id
						AND		mpc.id = mpcitems.transaction_id
					)mpc_mpcitems on ( mpc_mpcitems.company_id = emp.company_id AND mpc_mpcitems.work_order_id = empwo.work_order_id AND  mpc_mpcitems.work_order_detail_id = empwo.work_order_detail_id)
		LEFT JOIN	(
						SELECT vrc.company_id, vrc.id vrc_id, vrcitems.id vrc_detail_id, vrcitems.purchase_uom vrcitm_purchase_uom, 
		    					vrcitems.experience_base, vrcitems.max_exp, vrcitems.min_exp, 		
							CASE 
								WHEN vrcitems.purchase_uom = 'PT' 
									THEN vrcitems.item_price / 3 
								WHEN vrcitems.purchase_uom = 'PS' 
									THEN vrcitems.item_price / 6 
								WHEN vrcitems.purchase_uom = 'PY' 
									THEN vrcitems.item_price / 12 
								ELSE vrcitems.item_price
							END vrc_item_price
						FROM	vendor_rate_cards vrc,	vrc_items vrcitems
						WHERE	vrc.company_id = vrcitems.company_id
						AND		vrc.id = vrcitems.transaction_id
					)vrc_vrcitems on ( vrc_vrcitems.company_id = emp.company_id AND vrc_vrcitems.vrc_id = empwo.vrc_id AND  vrc_vrcitems.vrc_detail_id = empwo.vrc_item_id)
		WHERE emp.vendor_id IS NOT NULL
		AND	(
		CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$startdate."') 
			THEN 1
		WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
			THEN 1
		ELSE	2
		END
		)=1
		AND 	(
			CASE	WHEN '3' <> '".$get['user_type']."'  
						THEN(
								 ('1' = '".$get['admin_type']."')
		OR(au.reports_to = '".$user_id."')
		OR(emp.hr_buddy ='".$emp_id."')
		OR(emp.finance_buddy = '".$emp_id."')
		OR(emp.admin_buddy  ='".$emp_id."')
		OR(emp.project_buddy  = '".$emp_id."')
		OR(emp.loc_buddy   = '".$emp_id."')							
							)
						ELSE(
							emp.vendor_id =
								(
									SELECT
										id
									FROM
										vendors ven2
									WHERE
										ven2.email = '".$get['user_email']."'
								)
						)
				END
		) ORDER By 2, 4";
		//	echo $query; exit;
		$return = $callConfig->getAllRows($query);
		return $return;
	}
	function indertupdateperdiem($post, $get){
	 	global $callConfig;
			//echo "<pre>tyr";print_r($post);exit;
	 	if($post['rowid']!=""){
	 		$update = "update vrc_per_diem set is_active = 1  where vrc_item_id =".$post['rowid'];
	 		$data = $callConfig->executeQuery($update);
	 	}
	 	for ($i=1; $i <$post['count']+1 ; $i++) { 
	 		$country="country_".$i;
	 		$currenc="currenc_".$i;
	 		$pre_diem="pre_diem_".$i;
	 		$travel="travel_".$i;
	 		$itemfieldnames=array('company_id'=>$get['company_id'],'vrc_item_id'=>$post['rowid'],'country_id'=>$post[$country],'currency_id'=>$post[$country],'per_diem'=>$post[$pre_diem],'travel_expenses'=>$post[$travel],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));	
				//print_r($itemfieldnames);
	 		$item_id=$callConfig->insertRecord('vrc_per_diem',$itemfieldnames);
	 	}
	}
	function  toGetblukProjectTimesheet($get){
		global $callConfig;
		//echo "data";
		$month = $get['month'];
		$year = $get['year'];
		$startdate = $get['year']."-".$get['month']."-01";
		$endtdate = date("Y-m-t",strtotime($startdate));
		$company_id = $get['company_id'];
		$str  = "";
		if($get['vendor_id']!='0' || $get['emp_name']!='0' || $get['approval_manager']!='0' ){
		$str = " WHERE";
		if($get['vendor_id']!='0'){
		$str.= " vendor_id=".$get['vendor_id'];
		}
		if($get['emp_name']!='0'){
		if($get['vendor_id']!='0'){
		$str.= " AND";
		}
		$str.= "  employee_name LIKE '%".$get['emp_name']."%' ";
		}
		if($get['approval_manager']!='0'){
		if($get['vendor_id']!='0' || $get['emp_name']!='0'){
		$str.= " AND";
		}
		$str.= " approval_manager = ".$get['approval_manager'];
		}
		}
		$query = "SELECT
		vendor_id,
		vendor_name,
		employee_id,
		location_id,
		approval_manager,
		employee_name,
		emp_billable,
		MAX(emp_image) emp_image,
		job_title,
		SUM(billable_hrs) billable_hrs,
		SUM(nonbillable_hrs) nonbillable_hrs,
		leave_days_from + leave_days_to AS leave_days,
		vrc_max_leave,
		pt_status_id,
		current_status,
		purchase_uom,
		vrc_purchase_uom,
		MAX(l_item_price) budgeted_amount,
		MAX(vrc_item_price) vrc_budgeted_amount,
		SUM(bill_amount) AS billable_amount,
		SUM(vrc_billable_amount) AS vrc_billable_amount,
		SUM(nonbillable_hrs * rate_per_hrs) AS nonbillable_amount
		FROM
		(
		SELECT	emp.vendor_id AS vendor_id,
		vend.display_name AS vendor_name,
		emp.id employee_id,
		emp.location_id,
		emp.first_working_date,
		emp.joining_date,
		emp.total_experiance,
		au.approval_manager,
		vrcitems.max_leave AS vrc_max_leave,
		(
		SELECT	job_title
		FROM	job_titles jt
		WHERE	jt.id = emp.job_title_id 
		AND		jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		emp.billable emp_billable,
		emp.image emp_image,
		pt.billable_hrs billable_hrs,
		pt.nonbillable_hrs nonbillable_hrs,
		pt.rate_per_hrs rate_per_hrs,
		pt.bill_amount bill_amount,
		pt.vrc_billable_amount vrc_billable_amount,
		CASE WHEN mpcitems.purchase_uom = 'PT' 
		THEN mpcitems.item_price / 3 
		WHEN mpcitems.purchase_uom = 'PS' 
		THEN mpcitems.item_price / 6 
		WHEN mpcitems.purchase_uom = 'PY' 
		THEN mpcitems.item_price / 12 
		WHEN mpcitems.purchase_uom = 'PM' 
		THEN mpcitems.item_price 
		ELSE mpcitems.item_price
		END l_item_price,
		CASE WHEN vrcitems.purchase_uom = 'PT' 
		THEN vrcitems.item_price / 3 
		WHEN vrcitems.purchase_uom = 'PS' 
		THEN vrcitems.item_price / 6 
		WHEN vrcitems.purchase_uom = 'PY' 
		THEN vrcitems.item_price / 12 
		WHEN vrcitems.purchase_uom = 'PM' 
		THEN vrcitems.item_price 
		ELSE vrcitems.item_price
		END vrc_item_price,
		mpcitems.purchase_uom purchase_uom,
		mpcitems.item_price,
		vrcitems.purchase_uom vrc_purchase_uom,
		vrcitems.item_price vrc_item_prices,
		(
		SELECT	DATEDIFF(LEAST(el.leave_to,last_day(el.leave_from)),el.leave_from)
		FROM	employee_leaves el
		WHERE	el.company_id = emp.company_id AND el.employee_id = emp.id AND MONTH(el.leave_from) = '".$month."' AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from,
		(
		SELECT	DATEDIFF(el2.leave_to,GREATEST(el2.leave_from,DATE_SUB(LAST_DAY(el2.leave_to),INTERVAL DAY(LAST_DAY(el2.leave_to)) - 1 DAY)))
		FROM	employee_leaves el2
		WHERE	el2.company_id = emp.company_id AND el2.employee_id = emp.id AND MONTH(el2.leave_to) = '".$month."' AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to,
		vrcitems.min_exp,
		vrcitems.max_exp,
		pt.current_status AS pt_status_id,
		(
		SELECT	sts.name
		FROM	status sts
		WHERE	sts.name_value = pt.current_status 
		AND		sts.company_id = pt.company_id
		) current_status
		FROM
		employees emp,
		vendors vend,
		manpower_contracts mpc,
		mpc_items mpcitems,
		vendor_rate_cards vrc,
		vrc_items vrcitems,
		emp_work_order ewo,
		application_users au,
		project_timesheets pt
		WHERE	emp.company_id = '".$company_id."' 
		AND emp.is_active = '0' 
		AND emp.billable != 3
		AND vend.company_id = emp.company_id 
		AND vend.id = emp.vendor_id
		AND mpc.company_id = emp.company_id 
		AND mpcitems.company_id = mpc.company_id 
		AND mpcitems.transaction_id = mpc.id 
		AND mpcitems.is_active = mpc.is_active 
		AND mpc.customer_id = emp.vendor_id 
		AND ewo.company_id = emp.company_id 
		AND ewo.emp_id = emp.id 
		AND mpcitems.transaction_id = ewo.work_order_id 
		AND mpcitems.id = ewo.work_order_detail_id 
		AND vrcitems.company_id = vrc.company_id 
		AND vrcitems.transaction_id = vrc.id 
		AND vrcitems.is_active = vrc.is_active 
		AND vrcitems.company_id = mpc.company_id 
		AND vrcitems.transaction_id = ewo.vrc_id 
		AND vrcitems.id = ewo.vrc_item_id 
		AND emp.company_id = au.company_id
		AND au.id = emp.user_id
		AND emp.company_id = pt.company_id
		AND emp.id = pt.emp_id
		AND ewo.is_active = 0
		AND ewo.effective_from_date <= '".$startdate."'
		AND ewo.effective_to_date >= '".$endtdate."'
		AND au.reports_to = ".$get['user_id']."
		AND MONTH(pt.timesheet_date) = '".$month."'
		AND YEAR(pt.timesheet_date) = '".$year."'
		)abc ".$str."
		GROUP BY
		vendor_id,
		vendor_name,
		employee_id,
		location_id,
		employee_name,
		current_status,
		pt_status_id,
		purchase_uom,
		vrc_purchase_uom,
		vrc_max_leave";	
		//echo $query;		   
		$return = $callConfig->getAllRows($query);  
		return $return;
	}
	function  toGetblukProjectTimesheetlastmonths($get){
		global $callConfig;
		//echo "data";
		$month_date = $get['year']."-".$get['month']."-01";
		for($i=1; $i<2; $i++){
		$mont_date = date("Y-m-d", strtotime($month_date."-".$i." month"));
		$month = date('m',strtotime($mont_date));
		$year = date('Y',strtotime($mont_date));
		$query ="SELECT
		COUNT(DISTINCT(vendor_id)) vendor_ids,
		COUNT(DISTINCT(employee_id)) employee_ids,
		SUM(billable_hrs)billable_hrs,
		SUM(nonbillable_hrs) nonbillable_hrs,
		pt_status_id,
		current_status,
		MAX(l_item_price) budgeted_amount,
		SUM(bill_amount) as billable_amount,
		SUM(nonbillable_hrs * rate_per_hrs) as nonbillable_amount
		from (
		SELECT
		emp.vendor_id as vendor_id,
		vend.display_name as vendor_name,
		emp.id employee_id,
		emp.first_working_date,
		emp.joining_date,
		emp.total_experiance,
		(
		select job_title
		from job_titles jt
		where jt.id = emp.job_title_id
		and jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		pt.billable_hrs billable_hrs,
		pt.nonbillable_hrs nonbillable_hrs,
		pt.rate_per_hrs rate_per_hrs,
		pt.bill_amount bill_amount,
		CASE
		WHEN 	vrcitems.purchase_uom = 'PT'
		THEN vrcitems.item_price/3
		WHEN	vrcitems.purchase_uom = 'PS'
		THEN vrcitems.item_price/6
		WHEN	vrcitems.purchase_uom = 'PY'
		THEN vrcitems.item_price/12
		WHEN	vrcitems.purchase_uom = 'PM'
		THEN vrcitems.item_price
		WHEN	vrcitems.purchase_uom = 'PH'
		THEN vrcitems.item_price*(8*22)
		WHEN	vrcitems.purchase_uom = 'PD'
		THEN vrcitems.item_price*22
		WHEN	vrcitems.purchase_uom = 'PW'
		THEN vrcitems.item_price*(22/5)
		ELSE
		vrcitems.item_price
		END	l_item_price,
		vrcitems.purchase_uom purchase_uom, 
		vrcitems.item_price,
		(
		SELECT DATEDIFF (least(el.leave_to,last_day(el.leave_from)),el.leave_from)
		FROM employee_leaves el
		WHERE el.company_id = emp.company_id
		AND el.employee_id = emp.id
		AND MONTH(el.leave_from) = '".$month."'
		AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from,
		(
		SELECT DATEDIFF (el2.leave_to, greatest(el2.leave_from,DATE_SUB(LAST_DAY(el2.leave_to),INTERVAL DAY(LAST_DAY(el2.leave_to))-
		1 DAY)))
		FROM employee_leaves el2
		WHERE el2.company_id = emp.company_id
		AND el2.employee_id = emp.id
		AND MONTH(el2.leave_to) = '".$month."'
		AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to,
		vri.min_exp,
		vri.max_exp,
		pt.current_status as pt_status_id,
		(
		SELECT sts.name
		FROM status sts
		WHERE sts.name_value = pt.current_status
		AND sts.company_id = pt.company_id
		) current_status
		FROM employees emp, vendors vend, manpower_contracts vrc, mpc_items vrcitems, emp_work_order ewo, vrc_items vri, application_users au, project_timesheets pt
		WHERE emp.company_id = 1
		AND emp.is_active = '0'
		AND vend.company_id = emp.company_id
		AND vend.id = emp.vendor_id
		AND vrc.company_id = emp.company_id
		AND vrcitems.company_id = vrc.company_id
		AND vrcitems.transaction_id = vrc.id
		AND vrc.customer_id = emp.vendor_id
		AND ewo.company_id = emp.company_id
		AND ewo.emp_id = emp.id
		AND vrcitems.transaction_id = ewo.work_order_id
		AND vrcitems.id = ewo.work_order_detail_id
		AND vri.company_id = ewo.company_id
		AND vri.transaction_id = ewo.vrc_id
		AND vri.id =  ewo.vrc_item_id
		AND emp.company_id = au.company_id
		AND au.id = emp.user_id		
		AND emp.company_id = pt.company_id
		AND emp.id = pt.emp_id
		AND au.reports_to = ".$get['user_id']."
		AND MONTH(pt.timesheet_date) = '".$month."'
		AND YEAR(pt.timesheet_date) = '".$year."'
		)abc
		GROUP by
		current_status,
		pt_status_id";
		//echo $query; //exit;
		$return = $callConfig->getAllRows($query);  
		$keyy = $year."-".$month;
		$data[$keyy] = $return;
		}
		return $data;
	}
	function  toGetblukHrProjectTimesheet($get){
		global $callConfig;
		//echo "data";
		$company_id = $get['company_id'];
		$month = $get['month'];
		$year = $get['year'];
		$startdate = $get['year']."-".$get['month']."-01";
		$endtdate = date("Y-m-t",strtotime($startdate));
		$query = "SELECT
		vendor_id,
		vendor_name,
		employee_id,
		location_id,
		employee_name,
		emp_billable,
		reports_to,
		reports_to_name,
		user_id,
		MAX(emp_image) emp_image,
		job_title,
		SUM(billable_hrs) billable_hrs,
		SUM(nonbillable_hrs) nonbillable_hrs,
		leave_days_from + leave_days_to AS leave_days,
		vrc_max_leave,
		pt_status_id,
		current_status,
		purchase_uom,
		vrc_purchase_uom,
		MAX(l_item_price) budgeted_amount,
		MAX(vrc_item_price) vrc_budgeted_amount,
		SUM(bill_amount) AS billable_amount,
		SUM(vrc_billable_amount) AS vrc_billable_amount,
		SUM(nonbillable_hrs * rate_per_hrs) AS nonbillable_amount
		FROM
		(
		SELECT
		emp.vendor_id AS vendor_id,
		vend.display_name AS vendor_name,
		emp.id employee_id,
		emp.location_id,
		emp.first_working_date,
		emp.joining_date,
		emp.total_experiance,
		vrc_items.max_leave AS vrc_max_leave,
		emp.user_id user_id,
		(
		SELECT
		job_title
		FROM
		job_titles jt
		WHERE
		jt.id = emp.job_title_id AND jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		emp.billable emp_billable,
		emp.image emp_image,
		au.reports_to reports_to,
		(
		SELECT
		display_name
		FROM
		application_users aur
		WHERE
		aur.id = au.reports_to
		) reports_to_name,
		pt.billable_hrs billable_hrs,
		pt.nonbillable_hrs nonbillable_hrs,
		pt.rate_per_hrs rate_per_hrs,
		pt.bill_amount bill_amount,
		pt.vrc_billable_amount vrc_billable_amount,
		CASE WHEN mpc_items.purchase_uom = 'PT' THEN mpc_items.item_price / 3 WHEN mpc_items.purchase_uom = 'PS' THEN mpc_items.item_price / 6 WHEN mpc_items.purchase_uom = 'PY' THEN mpc_items.item_price / 12 WHEN mpc_items.purchase_uom = 'PM' THEN mpc_items.item_price ELSE mpc_items.item_price
		END l_item_price,
		CASE WHEN vrc_items.purchase_uom = 'PT' THEN vrc_items.item_price / 3 WHEN vrc_items.purchase_uom = 'PS' THEN vrc_items.item_price / 6 WHEN vrc_items.purchase_uom = 'PY' THEN vrc_items.item_price / 12 WHEN vrc_items.purchase_uom = 'PM' THEN vrc_items.item_price ELSE vrc_items.item_price
		END vrc_item_price,
		mpc_items.purchase_uom purchase_uom,
		mpc_items.item_price,
		vrc_items.purchase_uom vrc_purchase_uom,
		vrc_items.item_price vrc_item_prices,
		(
		SELECT
		DATEDIFF(
		LEAST(
		el.leave_to,
		last_day(el.leave_from)
		),
		el.leave_from
		)
		FROM
		employee_leaves el
		WHERE
		el.company_id = emp.company_id AND el.employee_id = emp.id AND MONTH(el.leave_from) = '".$month."' AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from,
		(
		SELECT
		DATEDIFF(
		el2.leave_to,
		GREATEST(
		el2.leave_from,
		DATE_SUB(
		LAST_DAY(el2.leave_to),
		INTERVAL DAY(LAST_DAY(el2.leave_to)) -1 DAY
		)
		)
		)
		FROM
		employee_leaves el2
		WHERE
		el2.company_id = emp.company_id AND el2.employee_id = emp.id AND MONTH(el2.leave_to) = '".$month."' AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to,
		vrc_items.min_exp,
		vrc_items.max_exp,
		pt.current_status AS pt_status_id,
		(
		SELECT
		sts.name
		FROM
		status
		sts
		WHERE
		sts.name_value = pt.current_status AND sts.company_id = pt.company_id
		) current_status
		FROM
		employees emp,
		vendors vend,
		manpower_contracts mpc,
		mpc_items mpc_items,
		vendor_rate_cards vrc,
		vrc_items vrc_items,
		emp_work_order ewo,
		application_users au,
		project_timesheets pt,
		employee_buddies empbuddy
		WHERE
		emp.company_id = '".$company_id."' 
		AND emp.is_active = '0' 
		AND emp.billable != 3
		AND vend.company_id = emp.company_id 
		AND vend.id = emp.vendor_id 
		AND mpc.company_id = emp.company_id 
		AND mpc_items.company_id = mpc.company_id 
		AND mpc_items.transaction_id = mpc.id 
		AND mpc.customer_id = emp.vendor_id 
		AND ewo.company_id = emp.company_id 
		AND ewo.emp_id = emp.id 
		AND mpc_items.transaction_id = ewo.work_order_id 
		AND mpc_items.id = ewo.work_order_detail_id
		AND vrc.company_id = vrc_items.company_id
		AND vrc.id = vrc_items.transaction_id
		AND vrc.is_active = vrc_items.is_active
		AND vrc.is_active = emp.is_active	
		AND vrc_items.company_id = mpc.company_id 
		AND vrc_items.transaction_id = ewo.vrc_id 
		AND vrc_items.id = ewo.vrc_item_id 
		AND emp.company_id = au.company_id 
		AND au.id = emp.user_id 
		AND emp.company_id = pt.company_id 
		AND emp.id = pt.emp_id 
		AND emp.is_active = ewo.is_active
		AND emp.is_active = vrc.is_active
		AND emp.is_active = mpc_items.is_active
		AND empbuddy.company_id = emp.company_id 
		AND empbuddy.employee_id = emp.id 
		AND empbuddy.is_active = 0
		AND ewo.effective_from_date <= '".$startdate."'
		AND ewo.effective_to_date >= '".$endtdate."'
		AND empbuddy.hr_buddy = '".$get['emp_id']."'
		AND MONTH(pt.timesheet_date) = '".$month."' 
		AND YEAR(pt.timesheet_date) = '".$year."'
		) abc
		GROUP BY
		vendor_id,
		vendor_name,
		employee_id,
		location_id,
		employee_name,
		current_status,
		pt_status_id,
		purchase_uom,
		vrc_purchase_uom,
		vrc_max_leave";
		// echo $query;
		$return = $callConfig->getAllRows($query);  
		return $return;
	}
	function  toGetblukFinaceProjectTimesheet($get){
		global $callConfig;
		$company_id = $get['company_id'];
		//echo "data";
		$month = $get['month'];
		$year = $get['year'];
		$startdate = $get['year']."-".$get['month']."-01";
		$endtdate = date("Y-m-t",strtotime($startdate));
		$query ="SELECT 
		vendor_id, 
		vendor_name, 
		employee_id,
		location_id,  
		employee_name, 
		emp_billable, 
		reports_to, 	
		reports_to_name, 	
		user_id, 
		MAX(emp_image) emp_image, 
		job_title, 
		SUM(billable_hrs) billable_hrs, 
		SUM(nonbillable_hrs) nonbillable_hrs, 
		leave_days_from + leave_days_to as leave_days, 
		vrc_max_leave,
		pt_status_id, 
		current_status, 
		purchase_uom, 
		vrc_purchase_uom, 
		MAX(l_item_price) budgeted_amount, 
		MAX(vrc_item_price) vrc_budgeted_amount, 
		SUM(bill_amount) as billable_amount, 
		SUM(vrc_billable_amount) as vrc_billable_amount, 
		SUM(nonbillable_hrs * rate_per_hrs) as nonbillable_amount 
		from 
		(
		SELECT 
		emp.vendor_id as vendor_id, 
		vend.display_name as vendor_name, 
		emp.id employee_id,
		emp.location_id,	  
		emp.first_working_date, 
		emp.joining_date, 
		emp.total_experiance,
		vrc_items.max_leave AS vrc_max_leave,	  
		emp.user_id user_id,
		(
		select 
		job_title 
		from 
		job_titles jt 
		where 
		jt.id = emp.job_title_id 
		and jt.company_id = emp.company_id
		) job_title, 
		emp.display_name employee_name, 
		emp.billable emp_billable, 
		emp.image emp_image,
		au.reports_to reports_to, 	
		(	
		select 	
		display_name 	
		from 	
		application_users aur 	
		where 	
		aur.id = au.reports_to	
		) reports_to_name,
		pt.billable_hrs billable_hrs, 
		pt.nonbillable_hrs nonbillable_hrs, 
		pt.rate_per_hrs rate_per_hrs, 
		pt.bill_amount bill_amount, 
		pt.vrc_billable_amount vrc_billable_amount, 
		CASE WHEN mpc_items.purchase_uom = 'PT' THEN mpc_items.item_price / 3 WHEN mpc_items.purchase_uom = 'PS' THEN mpc_items.item_price / 6 WHEN mpc_items.purchase_uom = 'PY' THEN mpc_items.item_price / 12 WHEN mpc_items.purchase_uom = 'PM' THEN mpc_items.item_price ELSE mpc_items.item_price END l_item_price, 
		CASE WHEN vrc_items.purchase_uom = 'PT' THEN vrc_items.item_price / 3 WHEN vrc_items.purchase_uom = 'PS' THEN vrc_items.item_price / 6 WHEN vrc_items.purchase_uom = 'PY' THEN vrc_items.item_price / 12 WHEN vrc_items.purchase_uom = 'PM' THEN vrc_items.item_price ELSE vrc_items.item_price END vrc_item_price, 
		mpc_items.purchase_uom purchase_uom, 
		mpc_items.item_price, 
		vrc_items.purchase_uom vrc_purchase_uom, 
		vrc_items.item_price vrc_item_prices, 
		(
		SELECT 
		DATEDIFF (
		least(
		el.leave_to, 
		last_day(el.leave_from)
		), 
		el.leave_from
		) 
		FROM 
		employee_leaves el 
		WHERE 
		el.company_id = emp.company_id 
		AND el.employee_id = emp.id 
		AND MONTH(el.leave_from) = '".$month."'
		AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from, 
		(
		SELECT 
		DATEDIFF (
		el2.leave_to, 
		greatest(
		el2.leave_from, 
		DATE_SUB(
		LAST_DAY(el2.leave_to), 
		INTERVAL DAY(
		LAST_DAY(el2.leave_to)
		)-1 DAY
		)
		)
		) 
		FROM 
		employee_leaves el2 
		WHERE 
		el2.company_id = emp.company_id 
		AND el2.employee_id = emp.id 
		AND MONTH(el2.leave_to) = '".$month."'
		AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to, 
		vrc_items.min_exp, 
		vrc_items.max_exp, 
		pt.current_status as pt_status_id, 
		(
		SELECT 
		sts.name 
		FROM 
		status sts 
		WHERE 
		sts.name_value = pt.current_status 
		AND sts.company_id = pt.company_id
		) current_status 
		FROM 
		employees emp, 
		vendors vend, 
		manpower_contracts mpc, 
		mpc_items mpc_items, 
		vendor_rate_cards vrc,
		vrc_items vrc_items, 
		emp_work_order ewo, 
		application_users au, 
		project_timesheets pt,
		employee_buddies empbuddy 
		WHERE 
		emp.company_id = '".$company_id."'
		AND emp.is_active = '0' 
		AND emp.billable != 3
		AND vend.company_id = emp.company_id 
		AND vend.id = emp.vendor_id 
		AND mpc.company_id = emp.company_id 
		AND mpc_items.company_id = mpc.company_id 
		AND mpc_items.transaction_id = mpc.id 
		AND vrc_items.company_id = mpc.company_id 
		AND mpc.customer_id = emp.vendor_id 
		AND ewo.company_id = emp.company_id 
		AND ewo.emp_id = emp.id 
		AND mpc.id = ewo.work_order_id 
		AND mpc_items.id = ewo.work_order_detail_id 
		AND vrc.id = ewo.vrc_id 
		AND vrc_items.id = ewo.vrc_item_id 
		AND emp.company_id = au.company_id 
		AND au.id = emp.user_id 
		AND emp.company_id = pt.company_id 
		AND emp.id = pt.emp_id 
		AND emp.is_active = ewo.is_active
		AND emp.is_active = vrc_items.is_active
		AND emp.is_active = mpc_items.is_active
		AND empbuddy.company_id = emp.company_id 	
		AND empbuddy.employee_id = emp.id 
		AND empbuddy.is_active = 0
		AND ewo.effective_from_date <= '".$startdate."'
		AND ewo.effective_to_date >= '".$endtdate."'	  
		AND empbuddy.finance_buddy = '".$get['emp_id']."'
		AND MONTH(pt.timesheet_date) = '".$month."' 
		AND YEAR(pt.timesheet_date) = '".$year."'
		) abc 
		GROUP by 
		vendor_id, 
		vendor_name, 
		employee_id,
		location_id,  
		employee_name, 
		current_status, 
		pt_status_id, 
		purchase_uom, 
		vrc_purchase_uom,
		vrc_max_leave";
		$return = $callConfig->getAllRows($query);  
		return $return;
	} 
	function  toGetblukHrProjectTimesheetlastmonths($get){
		global $callConfig;
		//echo "data";
		$month_date = $get['year']."-".$get['month']."-01";
		for($i=1; $i<7; $i++){
		$mont_date = date("Y-m-d", strtotime($month_date."-".$i." month"));
		$month = date('m',strtotime($mont_date));
		$year = date('Y',strtotime($mont_date));
		$query ="SELECT
		SUM(billable_hrs)billable_hrs,
		SUM(nonbillable_hrs) nonbillable_hrs,
		pt_status_id,
		current_status,
		reporting_manger,
		MAX(item_price) budgeted_amount,
		SUM(billable_hrs * (item_price/(20*8))) as billable_amount,
		SUM(nonbillable_hrs * (item_price/(20*8))) as nonbillable_amount
		from (
		SELECT
		emp.vendor_id as vendor_id,
		vend.display_name as vendor_name,
		emp.id employee_id,
		emp.first_working_date,
		emp.joining_date,
		emp.total_experiance,
		(
		select job_title
		from job_titles jt
		where jt.id = emp.job_title_id
		and jt.company_id = emp.company_id
		) job_title,
		emp.display_name employee_name,
		pt.billable_hrs billable_hrs,
		pt.nonbillable_hrs nonbillable_hrs,
		vrcitems.item_price,
		(
		SELECT DATEDIFF (least(el.leave_to,last_day(el.leave_from)),el.leave_from)
		FROM employee_leaves el
		WHERE el.company_id = emp.company_id
		AND el.employee_id = emp.id
		AND MONTH(el.leave_from) = '".$month."'
		AND YEAR(el.leave_from) = '".$year."'
		) leave_days_from,
		(
		SELECT DATEDIFF (el2.leave_to, greatest(el2.leave_from,DATE_SUB(LAST_DAY(el2.leave_to),INTERVAL DAY(LAST_DAY(el2.leave_to))-
		1 DAY)))
		FROM employee_leaves el2
		WHERE el2.company_id = emp.company_id
		AND el2.employee_id = emp.id
		AND MONTH(el2.leave_to) = '".$month."'
		AND YEAR(el2.leave_to) = '".$year."'
		) leave_days_to,
		vri.min_exp,
		vri.max_exp,
		pt.current_status as pt_status_id,
		(
		SELECT sts.name
		FROM status sts
		WHERE sts.name_value = pt.current_status
		AND sts.company_id = pt.company_id
		) current_status,
		(
		SELECT aumgr.display_name
		FROM application_users aumgr
		WHERE aumgr.id = au.reports_to
		AND aumgr.company_id = au.company_id
		) reporting_manger,
		(
		SELECT emphrb.display_name
		FROM employees emphrb
		WHERE emphrb.id = empbuddy.hr_buddy
		AND emphrb.company_id = empbuddy.company_id
		) hr_buddy,
		(
		SELECT empfrb.display_name
		FROM employees empfrb
		WHERE empfrb.id = empbuddy.finance_buddy
		AND empfrb.company_id = empbuddy.company_id
		) finance_buddy
		FROM employees emp, vendors vend, manpower_contracts vrc, mpc_items vrcitems, emp_work_order ewo, vrc_items vri, application_users au, project_timesheets pt, employee_buddies empbuddy
		WHERE emp.company_id = 1
		AND emp.is_active = '0'
		AND vend.company_id = emp.company_id
		AND vend.id = emp.vendor_id
		AND vrc.company_id = emp.company_id
		AND vrcitems.company_id = vrc.company_id
		AND vrcitems.transaction_id = vrc.id
		AND vrc.customer_id = emp.vendor_id
		AND ewo.company_id = emp.company_id
		AND ewo.emp_id = emp.id
		AND vrcitems.transaction_id = ewo.work_order_id
		AND vrcitems.id = ewo.work_order_detail_id
		AND vri.company_id = ewo.company_id
		AND vri.transaction_id = ewo.vrc_id
		AND vri.id =  ewo.vrc_item_id
		AND emp.company_id = au.company_id
		AND au.id = emp.user_id        
		AND emp.company_id = pt.company_id
		AND emp.id = pt.emp_id
		AND empbuddy.company_id = emp.company_id
		AND empbuddy.employee_id = emp.id
		AND empbuddy.is_active = 0
		AND 
		CASE
		WHEN 1 = '1' THEN
		(empbuddy.hr_buddy = '".$get['emp_id']."')
		WHEN 2 = '1' THEN
		(empbuddy.finance_buddy = '".$get['emp_id']."')
		ELSE
		au.reports_to =  '".$get['user_id']."'
		END
		AND MONTH(pt.timesheet_date) = '".$month."'
		AND YEAR(pt.timesheet_date) = '".$year."' 
		)abc
		GROUP by
		reporting_manger,
		current_status,
		pt_status_id";
		// echo $query;
		$return = $callConfig->getAllRows($query);  
		$keyy = $year."-".$month;
		$data[$keyy] = $return;
		}
		return $data;
	}
  	function updateprojecttimesheetsblukforhr($get,$geet){
	  	global $callConfig; 
	  	$quer = "SELECT `hr_buddy` FROM `employee_buddies` WHERE `employee_id`=".$get['emp_id'];
	  	$hrb = $callConfig->getRow($quer);
	  	//print_r($hrb); exit;
	  	$empquery = "SELECT id,user_id,display_name,email_id,reporting_manager_id,approval_manager_id FROM employees WHERE id IN ( SELECT emp_id FROM project_timesheets WHERE hr_id=".$hrb->hr_buddy." AND  current_status=101 AND MONTH(timesheet_date)=MONTH('".$get['extra']."')) AND reporting_manager_id = ".$geet['user_id'];
	  	$listemployes = $callConfig->getAllRows($empquery);
	  	$query = "UPDATE project_timesheets SET  hr_submit= '".date('Y-m-d h:i:s')."',current_status=".$get['status']." WHERE hr_id=".$hrb->hr_buddy." AND  current_status=101 AND MONTH(timesheet_date)=MONTH('".$get['extra']."') AND emp_id IN ( SELECT id FROM employees  WHERE reporting_manager_id = ".$geet['user_id']." )" ;
			//echo $query;
	  	$upp = $callConfig->executeQuery($query);
	  	$quere = "SELECT user_id,display_name,email_id FROM `employees` WHERE `id`=".$hrb->hr_buddy;
	  	$urb = $callConfig->getRow($quere);
	  	$user_ids[] = $urb->user_id;
	  	//echo "test1"; exit;
	  	//print_r($user_ids);
	  	$msg = date('M Y',strtotime($get['extra']))." Timesheet sumitted for your approval by Hiring Manager";
	  	$option = "COM_BULK_HR_PROJECT_TIMESHEET&PAY_YEAR=".date('Y',strtotime($get['extra']))."&PAY_MONTH=".date('m',strtotime($get['extra']));
	  	$notdataa = array('company_id'=>$geet['company_id'], 'notification_message'=>$msg, 'transaction_number'=>$notarry['id'], 'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			//print_r($notdataa);
	  	/*$ss = $this->tosendnotifiations($notdataa);*/
		$month_year =  date('M Y',strtotime($get['extra']));
	  	$approvequery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$geet['company_id']."' AND id =".$geet['user_id'];
		$approvemanagerdata = $callConfig->getRow($approvequery);

	  	$mail_data = array('month-year'=>$month_year,'approval_manager_name'=>ucwords($approvemanagerdata->display_name),'hr_buddy_name'=>ucwords($urb->display_name),'month_year'=>$month_year);
	  	$mail_data['email_content_id'] = 12;
	  	//print_r($mail_data);
		$email_array = $this->emailtemplate($mail_data);
		//print_r($email_array); 		
		$email_array['revicer'] = array($urb->email_id);
		$lenn = $this->sendEmailslist($email_array);
	  	$email['hr'] =  array($urb->email_id); 
	  	//print_r($email);
	  	//exit;
	  	//echo "test2"; exit;

	  	//---------------------------User Names -------------------//
	  	
	  	
	  	$allemp_ids = explode(",",$post['hid']);
	  	//$mail_data = array('month-year'=>$month_year,'approval_manager_name'=>ucwords($approvemanagerdata->display_name),'hr_buddy_name'=>ucwords($urb->display_name),'month_year'=>$month_year);
	  	foreach($listemployes as $sinemp){
	  		if($sinemp->reporting_manager_id != $sinemp->approval_manager_id){
	  			$reporting_mangers[] = $sinemp->reporting_manager_id;
	  		}
	  		$approval_managers[] = $sinemp->approval_manager_id;
	  		$mail_data['user_name'] = $sinemp->display_name;
	  		$mail_data['email_content_id'] = 9;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($sinemp->email_id);
			$email['user'] =  array($sinemp->email_id);
			$lenn = $this->sendEmailslist($email_array);
			$user_emailids[] = $sinemp->user_id;
	  	}

	  	
	  	//echo "test3"; exit;
	  	//--------------------------- Reportmanager Names -------------------//
	  	if(count($reporting_mangers)>0){
		  	$reportmangerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE  company_id = '".$geet['company_id']."' AND id IN (".implode(",",$reporting_mangers).") AND is_active = 0 ";
		  	$reportmangers = $callConfig->getAllRows($reportmangerquery);
		  	foreach($reportmangers as $reportmanger){
		  		$mail_data['reporting_manager_name'] = ucwords($reportmanger->display_name);
		  		$mail_data['email_content_id'] = 10;
				$email_array = $this->emailtemplate($mail_data);		
				$email_array['revicer'] = array($reportmanger->user_email);
				$lenn = $this->sendEmailslist($email_array);
				$email['reportmanger'] =  array($reportmanger->user_email);
				$user_emailids[] = $reportmanger->id;
		  	}	  	
	  	}

	  	//echo "test4"; exit;
	  	//--------------------------- Aprovalmanager Names -------------------//
	  	if(count($approval_managers)>0){
		  	 $approvalmanagerquery = "SELECT id,display_name,user_name,user_email FROM application_users  WHERE  company_id = '".$geet['company_id']."' AND id IN (".implode(",",$approval_managers).") AND is_active = 0";

		  	$approvemanagers = $callConfig->getAllRows($approvalmanagerquery);
		  	//print_r($approvemanagers);
		  	foreach($approvemanagers as $approvemanager){
		  		$mail_data['approval_manager_name'] = ucwords($approvemanager->display_name);
		  		$mail_data['email_content_id'] = 11;
				$email_array = $this->emailtemplate($mail_data);		
				$email_array['revicer'] = array($approvemanager->user_email);
				$lenn = $this->sendEmailslist($email_array);
				$email['approvetmanger'][] =  array($approvemanager->user_email);
				$user_emailids[] = $approvemanager->id;
		  	}
	  	}
	  	$notdataa['notification_users'] = $user_emailids;
		$notdataa['type'] = "INFORMATION";
		$notdataa['notification_message'] = "Timesheets Forward for ".$urb->display_name." approval ".$month_year;
		$nott[]=$notdataa;
		$this->tosendnotifiations($notdataa);
	  	//echo "test5"; exit;
	  	//--------------------------- HR Buddy Names -------------------//
	  	
	  	$notdataa['notification_users'] = array($urb->user_id);
		$notdataa['type'] = "ACTION";
		$notdataa['notification_message'] = "Timesheets submited for your approval ".$month_year;
		$this->tosendnotifiations($notdataa);
	  	//echo "test6"; exit;
	  	//print_r($email);  print_r($$urb); print_r($reporting_mangers); print_r($approval_managers); exit;
	  	$listquries[] = $quer;$listquries[] = $empquery;$listquries[] = $quere;$listquries[] = $approvequery;$listquries[] = $reportmangerquery;$listquries[] = $approvalmanagerquery;
	  	$nott[]=$notdataa;
	  	return array('queries'=>$listquries,'upp'=>$upp,'email'=>$email,'notification'=>$nott);
  	}
  	function updateprojecttimesheetsblukapprovehr($post,$get){
	  	global $callConfig; 
	  	$quer = "SELECT `finance_buddy` FROM `employee_buddies` WHERE `employee_id`=".$post['emp_id'];
	  	$hrb = $callConfig->getRow($quer);
	  	$query = "UPDATE project_timesheets SET finance_id=".$hrb->finance_buddy.",current_status=".$post['status']." WHERE emp_id IN (".$post['hid'].") AND MONTH(timesheet_date)=MONTH('".$post['extra']."')" ;
	  	$upp = $callConfig->executeQuery($query); 
  	}
  	function projecttimesheetsubmittofinace($post,$get){
		global $callConfig; 
		$quer = "SELECT `finance_buddy` FROM `employee_buddies` WHERE `employee_id`=".$post['emp_id'];
		$hrb = $callConfig->getRow($quer);
		$empquery = "SELECT id,user_id,display_name,email_id,reporting_manager_id,approval_manager_id,hr_buddy,finance_buddy FROM employees WHERE id IN ( SELECT emp_id FROM project_timesheets WHERE finance_id=".$hrb->finance_buddy." AND  current_status=103 AND MONTH(timesheet_date)=MONTH('".$post['extra']."') AND hr_id = ".$get['emp_id']." )";
		$listemployes = $callConfig->getAllRows($empquery);
		//echo $empquery; print_r($listemployes); exit;
		$query = "UPDATE project_timesheets SET  finance_submit= '".date('Y-m-d h:i:s')."',current_status=".$post['status']." WHERE finance_id=".$hrb->finance_buddy." AND  current_status=103 AND MONTH(timesheet_date)=MONTH('".$post['extra']."')  AND hr_id = ".$get['emp_id'];
		//echo $query;
		$upp = $callConfig->executeQuery($query);
		$quere = "SELECT user_id,display_name,email_id,user_id FROM `employees` WHERE `id`=".$hrb->finance_buddy;
		$urb = $callConfig->getRow($quere);

		$option = "COM_BULK_FINANCE_PROJECT_TIMESHEET&PAY_YEAR=".date('Y',strtotime($post['extra']))."&PAY_MONTH=".date('m',strtotime($post['extra']));
		$msg = date('M Y',strtotime($post['extra']))." Your Time Sheet is Approved by HR";
		$notdataa = array('company_id'=>$get['company_id'], 'notification_message'=>$msg, 'transaction_number'=>'', 'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
		$month_year =  date('M Y',strtotime($post['extra']));
		$hrbquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id =".$get['user_id'];
		$hrbdata = $callConfig->getRow($hrbquery);
		$mail_data = array('month-year'=>$month_year,'hr_buddy_name'=>$hrbdata->display_name,'finance_buddy_name'=>$urb->display_name,'month_year'=>$month_year);
		//---------------------------User Names -------------------//
		foreach($listemployes as $sinemp){
		if($sinemp->reporting_manager_id != $sinemp->approval_manager_id){
		$reporting_mangers[$sinemp->reporting_manager_id] = $sinemp->reporting_manager_id;
		}
		$approval_managers[$sinemp->approval_manager_id] = $sinemp->approval_manager_id;
		$hrbuddies[$sinemp->hr_buddy]= $sinemp->hr_buddy;
		$mail_data['user_name'] = $sinemp->display_name;
		$mail_data['email_content_id'] = 13;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($sinemp->email_id);
		$lenn = $this->sendEmailslist($email_array);
		$email['user'] =  array($sinemp->email_id);
		$user_emailids[] = $sinemp->user_id;
		}
		//print_r($email_array);print_r($reporting_mangers);print_r($approval_managers); print_r($user_emailids); exit;
		//--------------------------- Reportmanager Names -------------------//
		if(count($reporting_mangers)>0){
		$reportmangerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id IN (".implode(",",$reporting_mangers).")";
		$reportmangers = $callConfig->getAllRows($reportmangerquery);
		foreach($reportmangers as $reportmanger){
		$mail_data['reporting_manager_name'] = $reportmanger->display_name;
		$mail_data['email_content_id'] = 14;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($reportmanger->user_email);
		$lenn = $this->sendEmailslist($email_array);
		$email['reportmanger'] =  array($reportmanger->user_email);
		$user_emailids[] = $reportmanger->id;
		}
		}
		//print_r($email_array);print_r($reporting_mangers);print_r($approval_managers); print_r($user_emailids); exit;
		//--------------------------- Aprovalmanager Names -------------------//
		if(count($approval_managers)>0){
		$approvalmanagerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id IN (".implode(",",$approval_managers).")";
		$approvemanagers = $callConfig->getAllRows($approvalmanagerquery);
		foreach($approvemanagers as $approvemanager){
		$mail_data['approval_manager_name'] = $approvemanager->display_name;
		$mail_data['email_content_id'] = 15;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($approvemanager->user_email);
		$email['approvetmanger'][] =  array($approvemanager->user_email);
		$lenn = $this->sendEmailslist($email_array);
		$user_emailids[] = $approvemanager->id;
		}
		}

		//--------------------------- HR Buddy Names -------------------//

		$mail_data['email_content_id'] = 16;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($hrbdata->user_email);
		$lenn = $this->sendEmailslist($email_array);
		$user_emailids[] = $hrbdata->id;
		$email['hr'][] =  array($hrbdata->user_email);

		$notdataa['notification_users'] = $user_emailids;
		$notdataa['type'] = "INFORMATION";
		$notdataa['notification_message'] = "Timesheets Forward for ".$urb->display_name." approval ".$month_year;
		$nott[]=$notdataa;
		$this->tosendnotifiations($notdataa);
		//--------------------------- Finance Buddy Names -------------------//

		$mail_data['email_content_id'] = 17;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($urb->email_id);
		$lenn = $this->sendEmailslist($email_array);
		$email['fin'][] =  array($urb->email_id);
		$notdataa['notification_users'] = array($urb->user_id);
		$notdataa['type'] = "ACTION";
		$notdataa['notification_message'] = "Timesheets submited for your approval ".$month_year;
		$this->tosendnotifiations($notdataa);
		/*print_r($hrbdata);
		print_r($reporting_mangers);print_r($approval_managers); print_r($user_emailids);
		print_r($email); exit;*/
		$nott[]=$notdataa;
		return array('queries'=>$listquries,'upp'=>$user_emailids,'email'=>$email,'notification'=>$nott);
	}
	function updateprojecttimesheetsblukapprovefinace($post,$get){
		global $callConfig; 
		$empquery = "SELECT id,user_id,display_name,email_id,reporting_manager_id,approval_manager_id,hr_buddy,finance_buddy FROM employees WHERE id IN ( SELECT emp_id FROM project_timesheets WHERE emp_id IN (".$post['hid'].") AND MONTH(timesheet_date)=MONTH('".$post['extra']."') )";
		$listemployes = $callConfig->getAllRows($empquery);
		//echo $empquery; print_r($listemployes); exit;
		$query = "UPDATE project_timesheets SET current_status=".$post['status']." WHERE emp_id IN (".$post['hid'].") AND MONTH(timesheet_date)=MONTH('".$post['extra']."')" ;
		//echo $query;	
		$upp = $callConfig->executeQuery($query);

		$quere = "SELECT user_id,display_name,email_id,user_id FROM `employees` WHERE `id`=".$get['emp_id'];
		$urb = $callConfig->getRow($quere);
		$month_year =  date('M Y',strtotime($post['extra']));
		$option = "COM_BULK_FINANCE_PROJECT_TIMESHEET&PAY_YEAR=".date('Y',strtotime($post['extra']))."&PAY_MONTH=".date('m',strtotime($post['extra']));
		$notdataa = array('company_id'=>$get['company_id'], 'notification_message'=>$msg, 'transaction_number'=>'', 'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
		$mail_data = array('month-year'=>$month_year,'approval_manager_name'=>'','hr_buddy_name'=>'','finance_buddy_name'=>$urb->display_name,'month_year'=>$month_year);

		//---------------------------User Names -------------------//
		foreach($listemployes as $sinemp){
		if($sinemp->reporting_manager_id != $sinemp->approval_manager_id){
		$reporting_mangers[$sinemp->reporting_manager_id] = $sinemp->reporting_manager_id;
		}
		$approval_managers[$sinemp->approval_manager_id] = $sinemp->approval_manager_id;
		$hrbuddies[]= $sinemp->hr_buddy;
		$mail_data['user_name'] = $sinemp->display_name;
		$mail_data['email_content_id'] = 18;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($sinemp->email_id);
		$lenn = $this->sendEmailslist($email_array);
		$email['user'] =  array($sinemp->email_id);
		$user_emailids[] = $sinemp->user_id;
		}
		//print_r($email_array);print_r($reporting_mangers);print_r($approval_managers); print_r($user_emailids); exit;
		//--------------------------- Reportmanager Names -------------------//
		if(count($reporting_mangers)>0){
		$reportmangerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id IN (".implode(",",$reporting_mangers).")";
		$reportmangers = $callConfig->getAllRows($reportmangerquery);
		foreach($reportmangers as $reportmanger){
		$mail_data['reporting_manager_name'] = $reportmanger->display_name;
		$mail_data['email_content_id'] = 19;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($reportmanger->user_email);
		$lenn = $this->sendEmailslist($email_array);
		$email['reportmanger'] =  array($reportmanger->user_email);
		$user_emailids[] = $reportmanger->id;
		}
		}
		//print_r($email_array);print_r($reporting_mangers);print_r($approval_managers); print_r($user_emailids); exit;
		//--------------------------- Aprovalmanager Names -------------------//
		if(count($approval_managers)>0){
		$approvalmanagerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id IN (".implode(",",$approval_managers).")";
		$approvemanagers = $callConfig->getAllRows($approvalmanagerquery);
		foreach($approvemanagers as $approvemanager){
		$mail_data['approval_manager_name'] = $approvemanager->display_name;
		$mail_data['email_content_id'] = 20;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($approvemanager->user_email);
		$email['approvetmanger'][] =  array($approvemanager->user_email);
		$lenn = $this->sendEmailslist($email_array);
		$user_emailids[] = $approvemanager->id;
		}
		}

		//--------------------------- HR Buddy Names -------------------//
		if(count($hrbuddies)>0){
		$hrbuddiesquery = "SELECT id,display_name,email_id,user_id FROM employees WHERE is_active = 0 AND company_id = '".$get['company_id']."' AND id IN (".implode(",",$hrbuddies).")";
		$hrbbdata = $callConfig->getAllRows($hrbuddiesquery);
		foreach($hrbbdata as $hrbata){
		$mail_data['hr_buddy_name'] = $hrbata->display_name;
		$mail_data['email_content_id'] = 21;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($hrbata->email_id);
		$lenn = $this->sendEmailslist($email_array);
		$user_emailids[] = $hrbata->user_id;
		$email['hr'][] =  array($hrbata->email_id);
		}
		}

		//--------------------------- Finance Buddy Names -------------------//

		$mail_data['email_content_id'] = 22;
		$email_array = $this->emailtemplate($mail_data);		
		$email_array['revicer'] = array($urb->email_id);
		$lenn = $this->sendEmailslist($email_array);
		$email['fin'][] =  array($urb->email_id);
		$user_emailids[] = $urb->user_id;
		$notdataa['notification_users'] = $user_emailids;
		$notdataa['type'] = "INFORMATION";
		$notdataa['notification_message'] = "Timesheets is Approved by ".$urb->display_name." for ".$month_year;
		$nott[]=$notdataa;
		$this->tosendnotifiations($notdataa);
		$this->tosendnotifiations($notdataa);

		$nott[]=$notdataa;
		return array('queries'=>$listquries,'upp'=>$user_emailids,'email'=>$email,'notification'=>$nott);
	}
	function togetteamcount($get){
		global $callConfig;
		$joidate = $get['year']."-".$get['month']."-01";
		$joidate =  date("Y-m-d",strtotime("+1 month", strtotime($joidate)));
		$year = $get['year'];
		$month = $get['month'];
		$startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
		$enddate = date("Y-m-t",strtotime($year."-".$month."-01"));
		$str  = "";
		if($get['vendor_id']!='0' || $get['emp_name']!='0'){
		//$str = "";
		if($get['vendor_id']!='0'){
		$str.= " AND emp.vendor_id=".$get['vendor_id'];
		}
		if($get['emp_name']!='0'){

		$str.= " AND  emp.display_name LIKE '%".$get['emp_name']."%' ";
		}
		/*if($get['approval_manager']!='0'){
		$str.= " AND approval_manager=".$get['approval_manager'];
		}*/
		}
		$company_id = $get['company_id'];
		$query = "SELECT
		emp.id,
		emp.display_name,
		emp.first_name,
		emp.email_id,
		emp.image,
		emp.user_id,
		au.approval_manager,
		emp.job_title_id,
		emp.vendor_id,
		emp.date_of_leaving,
		emp.billable emp_billable,
		(
		SELECT
		job_title
		FROM
		job_titles jt
		WHERE
		jt.company_id = emp.company_id AND jt.id = emp.job_title_id AND jt.is_active = 0
		) job_title /*,
		(
		SELECT CASE WHEN
		mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price
		END
		FROM
		emp_work_order ewo,
		mpc_items mpcitems
		WHERE
		ewo.company_id = emp.company_id AND mpcitems.transaction_id = ewo.work_order_id AND mpcitems.id = ewo.work_order_detail_id AND ewo.emp_id = emp.id AND ewo.is_active = 0 AND ewo.effective_from_date <= '".$startdate."' 
		AND ewo.effective_to_date >= '".$enddate."' 
		) budgeted_amount,
		(
		SELECT
		mpcitems.purchase_uom
		FROM
		emp_work_order ewo,
		mpc_items mpcitems
		WHERE
		ewo.company_id = emp.company_id AND mpcitems.transaction_id = ewo.work_order_id AND mpcitems.id = ewo.work_order_detail_id AND ewo.emp_id = emp.id AND ewo.is_active = 0 AND ewo.effective_from_date <= '".$startdate."' 
		AND ewo.effective_to_date >= '".$enddate."' 
		) purchase_uom,
		(
		SELECT CASE WHEN
		vrcitems.purchase_uom = 'PT' THEN vrcitems.item_price / 3 WHEN vrcitems.purchase_uom = 'PS' THEN vrcitems.item_price / 6 WHEN vrcitems.purchase_uom = 'PY' THEN vrcitems.item_price / 12 ELSE vrcitems.item_price
		END
		FROM
		emp_work_order ewo,
		vrc_items vrcitems
		WHERE
		ewo.company_id = emp.company_id AND vrcitems.transaction_id = ewo.vrc_id AND vrcitems.id = ewo.vrc_item_id AND ewo.emp_id = emp.id AND ewo.is_active = 0 AND ewo.effective_from_date <= '".$startdate."' AND ewo.effective_to_date >= '".$enddate."' 
		) vrc_budgeted_amount,
		(
		SELECT
		vrcitems.purchase_uom
		FROM
		emp_work_order ewo,
		vrc_items vrcitems
		WHERE
		ewo.company_id = emp.company_id AND vrcitems.transaction_id = ewo.vrc_id AND vrcitems.id = ewo.vrc_item_id AND ewo.emp_id = emp.id AND ewo.is_active = 0 AND ewo.effective_from_date <= '".$startdate."' AND ewo.effective_to_date >= '".$enddate."' 
		) l_rate_card_item_uom */
		FROM
		employees emp,  application_users au
		WHERE
		au.id = emp.user_id AND
		emp.company_id = '".$company_id."'
		AND user_id IN(
		SELECT
		id
		FROM
		application_users
		WHERE
		reports_to = ".$get['user_id']." AND is_active = '0'
		) 
		AND emp.joining_date < '".$joidate."'
		AND	(
		CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$joidate."') 
		THEN 1
		WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
		THEN 1
		ELSE	2
		END
		)=1 ".$str;
		//$query = "SELECT id,display_name,first_name,email_id,image,user_id,job_title_id,billable emp_billable,(select job_title from job_titles jt	where jt.id = job_title_id ) job_title FROM employees WHERE user_id IN (SELECT id FROM application_users WHERE reports_to=".$get['user_id'].") AND is_active=0"; 
		$data = $callConfig->getAllRows($query);
		$return['data'] = $data;
		$return['team'] = count($data);
		$return['sql'] = $query;
		return $return;
	}
	function togetteamlisttohr($get){
		global $callConfig;
		$query = "SELECT id, display_name, reports_to, ( SELECT display_name FROM application_users au2 WHERE au2.id = au.reports_to AND au2.company_id = au.company_id )reports_to_name FROM `application_users` au WHERE au.id in ( SELECT user_id FROM employees WHERE id IN ( SELECT employee_id FROM employee_buddies eb WHERE eb.company_id = au.company_id AND eb.hr_buddy =".$get['emp_id']."))";
		//$query = "SELECT id,display_name,first_name,image,job_title_id,(select job_title from job_titles jt where jt.id = job_title_id) job_title FROM employees WHERE user_id IN (SELECT id FROM application_users WHERE reports_to=".$get['emp_id'].")";

		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function getemployeetimesheetsummary($post,$get){
		global $callConfig;
		$emp_list = $post['emplist'];
		$month = $post['month'];
		$year = $post['year']; 
		if(empty($emp_list)){
		$query="SELECT id,display_name  FROM `employees` WHERE company_id = '".$get['company_id']."' and is_active =0";
		$empdatas = $callConfig->getAllRows($query);
		foreach ($empdatas as $value) {
		$empidlist[$value->id] = $value->display_name;
		}
		}else{
		foreach ($emp_list as $key=>$value) {
		$empidlist[$key]=$value;
		}
		}
		//print_r($empidlist); exit;
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$atsdata = $callConfig->getRow($attstc);

		$shiftkeys = "SELECT * FROM attendance_key_types WHERE is_active=0 and company_id=".$get['company_id'];
		$shiftkeysdata = $callConfig->getAllRows($shiftkeys);
		foreach ($shiftkeysdata as $skey=>$svalue) {
		$shift[$svalue->key_short_code] = (array)$svalue;
		}
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id'];
		$holidaysdata = $callConfig->getAllRows($holidays);
		foreach ($holidaysdata as $hkey=>$hvalue) {
		$holidaysdatas[] = $hvalue->holiday_date;
		}

		$gencshiftkey = "SELECT * FROM general_code_details WHERE is_active=0 and company_id=".$get['company_id']." and genc_code='ATTENDANCE_KEY_TYPES'"; 
		$gencshiftkeydata = $callConfig->getAllRows($gencshiftkey);
		foreach ($gencshiftkeydata as $gkey => $gvalue) {
		if($gvalue->genc_detail_code=='H'){
		$genc_holiday_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='O'){
		$genc_off_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='W'){
		$genc_work_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='S'){
		$genc_sick_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='R'){
		$genc_partial_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='V'){
		$genc_vacation_shiftkey= $gvalue->genc_detail_code;
		}
		else if($gvalue->genc_detail_code=='A'){
		$genc_absence_shiftkey= $gvalue->genc_detail_code;
		}
		}

		$min_working_hours = explode(":", $atsdata->minimum_hours_required);
		$min_working_sec = (number_format($min_working_hours['0'])*3600)+(number_format($min_working_hours['1'])*60)+number_format($min_working_hours['2']); 
		$office_starttime = $atsdata->office_start_time;
		$grace_perieod = $atsdata->grace_perieod;
		$office_starttime = strtotime($office_starttime)+strtotime($grace_perieod);
		$office_starttime_hrs = explode(":", $office_starttime);
		$office_starttime_sec = (((number_format($office_starttime_hrs['0'])*3600)+number_format($office_starttime_hrs['1']))*60)+number_format($office_starttime_hrs['2']); 
		$half_working_hours = explode(":", $atsdata->half_day_hours);
		$half_working_sec = (number_format($half_working_hours['0'])*3600)+(number_format($half_working_hours['1'])*60)+number_format($half_working_hours['2']); 
		$workingdays = $atsdata->working_days;
		$working_days = json_decode($workingdays,1);
		$today_day= date('j');
		$current_month= date('m');
		$from=mktime(0, 0, 0, $month, '01', $year);
		$from_date =  date("Y-m-d", $from);
		$to=mktime(0, 0, 0, $month, $today_day, $year);
		$to_date = $month==$current_month ? date("Y-m-d", $to) : date('Y-m-t', strtotime($from_date));
		foreach ($empidlist as $key => $empvalue) {
		$attendancequery = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs, timesheet_date from  project_timesheets where is_active=0 AND emp_id = ".$key." AND  `timesheet_date` >= '".$from_date."' AND `timesheet_date` <= '".$to_date."' group by timesheet_date";
		unset($days);
		$attendance = $callConfig->getAllRows($attendancequery);
		foreach ($attendance as $emptime) {
		//print_r($emptime);
		$days[$emptime->timesheet_date]['date'] = $emptime->timesheet_date;
		$days[$emptime->timesheet_date]['billable_hrs'] = $emptime->billable_hrs; 
		$days[$emptime->timesheet_date]['nonbillable_hrs'] = $emptime->nonbillable_hrs; 
		}
		//echo "<pre>"; print_r($days);
		$datediff = strtotime($to_date) - strtotime($from_date);
		$datediff = floor($datediff/(60*60*24));
		for($i = 0; $i <= $datediff ; $i++){
		$valda =  date("Y-m-d", strtotime($from_date . ' + ' . $i . 'day'));
		$valday = strtolower(date("D", strtotime($from_date . ' + ' . $i . 'day')));
		//Holidays
		$empattendance['S1displayvalue'] = $days[$valda]['billable_hrs'];
		$empattendance['S1displaybgcolor'] = 'light-success';
		$empattendance['S1displaytextcolor'] = 'DARK';
		$empattendance['S2displayvalue'] = $days[$valda]['nonbillable_hrs'];
		$empattendance['S2displaybgcolor'] = 'light-warning';
		$empattendance['S2displaytextcolor'] = 'DARK';

		$empattendance_data[$i]=$empattendance; 
		}
		$empattendance_details[$key]=$empattendance_data; 
		}
		$empattendancedata['emp_names'] = $empidlist;
		$empattendancedata['emp_attendance'] = $empattendance_details;
		//echo "<pre>"; print_r($empattendance_details);exit;
		return $empattendancedata;
	}
	function gettimesheetsummary($get) {
		global $callConfig;
		$emp_id = $get['emp_id'];
		$prevMon = date("Y-m-d", strtotime('monday this week', strtotime($get['fromdate'])));
		$presun = date("Y-m-d", strtotime('sunday this week', strtotime($get['todate'])));
		$end_year_todate = date('Y-m-d', strtotime('last day of december this year', strtotime($get['fromdate'])));
		$start_year_fromdate = date('Y-m-d', strtotime('first day of january this year', strtotime($get['fromdate'])));
		$start_month_fromdate = $get['fromdate'];
		$end_month_todate = $get['todate'];
		$current_month = date('m',strtotime($get['fromdate']));
		$current_year = date('Y',strtotime($get['fromdate']));
		$qtr_start_date = date('Y-m-d', strtotime('-2 Months', strtotime($get['fromdate'])));
		$qtr_end_date = date('Y-m-d', strtotime($get['fromdate']));
		//Yearly
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND company_id = '".$get['company_id']."' AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_year_fromdate."' and `timesheet_date` <= '".$end_year_todate."'";
		$allresponseyear = $callConfig->getRow($queryresponse);
		$totalhrs['0'] = $allresponseyear->billable_hrs;
		//Qtrly
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND company_id = '".$get['company_id']."' AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$qtr_start_date."' and `timesheet_date` <= '".$qtr_end_date."'";
		$allresponse = $callConfig->getRow($queryresponse);
		$totalhrs['1'] = $allresponse->billable_hrs;

		// MOnthly 
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND company_id = '".$get['company_id']."' AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$end_month_todate."'";
		$allresponse = $callConfig->getRow($queryresponse);
		$totalhrs['2'] = $allresponse->billable_hrs;
		$totalhrs['3'] = $allresponse->nonbillable_hrs;

		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,task_name from  project_timesheets where company_id = '".$get['company_id']."' AND is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$end_month_todate."' group by task_name";
		//echo "?/: ".$queryresponse."<br>";
		$taskresponse = $callConfig->getAllRows($queryresponse);
		foreach($taskresponse as $task){
		if($task->task_name!=''){
		$tasks[$task->task_name] = $task->billable_hrs;
		}
		}
		$details['tasks'] = $tasks;
		$details['summary'] = $totalhrs;
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs,timesheet_date,max(current_status) as current_status from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$get['todate']."' group by timesheet_date";
		//echo "main:  ".$queryresponse."<br>";
		$allresponseweek = $callConfig->getAllRows($queryresponse);
		foreach($allresponseweek as $dayhrs){
		$datewise[$dayhrs->timesheet_date]['date'] = $dayhrs->timesheet_date;
		$datewise[$dayhrs->timesheet_date]['billhrs'] = $dayhrs->billable_hrs;
		$datewise[$dayhrs->timesheet_date]['nonbillhrs'] = $dayhrs->nonbillable_hrs;
		$datewise[$dayhrs->timesheet_date]['current_status'] = $dayhrs->current_status;
		}
		$todate = strtotime($get['todate']);
		$latodate = strtotime($presun);
		$fromdate =strtotime( $prevMon." + 0 day");
		$datediff = abs($latodate - $fromdate); 
		$datecount= round($datediff / (60 * 60 * 24));
		for($i=0; $i<$datecount; $i++){
		$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
		$week_fromdate = $d;
		if($i%7==0){
		$td = date("Y-m-d", strtotime( $d." +6 days") );
		$week_todate = $td;
		$i+=6;
		}else{
		$week_todate = $todate;
		}
		$weekrows['weekfrom'] =  $week_fromdate;
		$weekrows['weekto'] = $week_todate;
		$totalbillable =0;
		$totalnonbillable =0;
		foreach($datewise as $tdate=>$data){
		if($tdate >= $week_fromdate && $tdate <= $week_todate ){
		$totalbillable += $data['billhrs'];
		$totalnonbillable += $data['nonbillhrs'];
		$current_status = $data['current_status'];
		}

		}
		$weekrows['billhrs'] = $totalbillable;
		$weekrows['nonbillhrs'] = $totalnonbillable;
		$weekrows['status'] = $current_status;
		$summarydetails[] = $weekrows;	    	
		}
		$details['summarydetails'] = $summarydetails;
		return $details;
	}
	function gettimesheetsummaryoldd($get) {
		global $callConfig;
		$emp_id = $get['emp_id'];
		$prevMon = date("Y-m-d", strtotime('monday this week', strtotime($get['fromdate'])));
		$presun = date("Y-m-d", strtotime('sunday this week', strtotime($get['todate'])));
		$end_year_todate = date('Y-m-d', strtotime('last day of december this year', strtotime($get['fromdate'])));
		$start_year_fromdate = date('Y-m-d', strtotime('first day of january this year', strtotime($get['fromdate'])));
		$start_month_fromdate = $get['fromdate'];
		$end_month_todate = $get['todate'];
		$current_month = date('m',strtotime($get['fromdate']));
		$current_year = date('Y',strtotime($get['fromdate']));
		$qtr_start_date = date('Y-m-d', strtotime('-2 Months', strtotime($get['fromdate'])));
		$qtr_end_date = date('Y-m-d', strtotime($get['fromdate']));
		//Yearly
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_year_fromdate."' and `timesheet_date` <= '".$end_year_todate."'";
		$allresponseyear = $callConfig->getRow($queryresponse);
		$totalhrs['0'] = $allresponseyear->billable_hrs;
		//Qtrly
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$qtr_start_date."' and `timesheet_date` <= '".$qtr_end_date."'";
		$allresponse = $callConfig->getRow($queryresponse);
		$totalhrs['1'] = $allresponse->billable_hrs;

		// MOnthly 
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$end_month_todate."'";
		$allresponse = $callConfig->getRow($queryresponse);
		$totalhrs['2'] = $allresponse->billable_hrs;
		$totalhrs['3'] = $allresponse->nonbillable_hrs;

		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,task_name from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$end_month_todate."' group by task_name";
		//echo "?/: ".$queryresponse."<br>";
		$taskresponse = $callConfig->getAllRows($queryresponse);
		foreach($taskresponse as $task){
		if($task->task_name!=''){
		$tasks[$task->task_name] = $task->billable_hrs;
		}
		}
		$details['tasks'] = $tasks;
		$details['summary'] = $totalhrs;
		$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs,timesheet_date,max(current_status) as current_status from  project_timesheets where is_active=0 AND emp_id = ".$emp_id." AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$get['todate']."' group by timesheet_date";
		//echo "main:  ".$queryresponse."<br>";
		$allresponseweek = $callConfig->getAllRows($queryresponse);
		foreach($allresponseweek as $dayhrs){
		$datewise[$dayhrs->timesheet_date]['date'] = $dayhrs->timesheet_date;
		$datewise[$dayhrs->timesheet_date]['billhrs'] = $dayhrs->billable_hrs;
		$datewise[$dayhrs->timesheet_date]['nonbillhrs'] = $dayhrs->nonbillable_hrs;
		$datewise[$dayhrs->timesheet_date]['current_status'] = $dayhrs->current_status;
		}
		$todate = strtotime($get['todate']);
		$latodate = strtotime($presun);
		$fromdate =strtotime( $prevMon." + 0 day");
		$datediff = abs($latodate - $fromdate); 
		$datecount= round($datediff / (60 * 60 * 24));
		for($i=0; $i<$datecount; $i++){
		$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
		$week_fromdate = $d;
		if($i%7==0){
		$td = date("Y-m-d", strtotime( $d." +6 days") );
		$week_todate = $td;
		$i+=6;
		}else{
		$week_todate = $todate;
		}
		$weekrows['weekfrom'] =  $week_fromdate;
		$weekrows['weekto'] = $week_todate;
		$totalbillable =0;
		$totalnonbillable =0;
		foreach($datewise as $tdate=>$data){
		if($tdate >= $week_fromdate && $tdate <= $week_todate ){
		$totalbillable += $data['billhrs'];
		$totalnonbillable += $data['nonbillhrs'];
		$current_status = $data['current_status'];
		}

		}
		$weekrows['billhrs'] = $totalbillable;
		$weekrows['nonbillhrs'] = $totalnonbillable;
		$weekrows['status'] = $current_status;
		$summarydetails[] = $weekrows;	    	
		}
		$details['summarydetails'] = $summarydetails;
		return $details;
	}
	function getempconnect($get) {
		global $callConfig;
		$employee_id = $get['employee_id'];
		$company_id = $get['company_id'];
		$empdata = "select * from employees where id=".$employee_id." and is_active='0' and company_id='".$company_id."'"; 
		$edata = $callConfig->getRow($empdata);
		$location_id =$edata->location_id;

		$hrconnect_data = "select * from employee_buddies where employee_id='".$employee_id."' and is_active='0' and company_id='".$company_id."'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$hr_connect_id = $hrconnect_details->hr_buddy;
		$finance_connect_id = $hrconnect_details->finance_buddy;
		$admin_connect_id = $hrconnect_details->admin_buddy;
		if($hr_connect_id==0 || $hr_connect_id==""){
		$hrconnect_data = "select * from locations where company_id='".$company_id."' and is_active='0' and id='".$location_id."'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$hr_connect_id = $hrconnect_details->hr_connect;
		if($hr_connect_id==0 || $hr_connect_id=="" ) {
		$hrconnect_data = "select * from company where company_id='".$company_id."' and is_active='0'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$hr_connect_id = $hrconnect_details->hr_connect;
		}
		if($hr_connect_id==0 || $hr_connect_id=="" ) {
		$hr_connect_id = "NOT-CONFIGURED";
		}
		}

		if($finance_connect_id==0 || $finance_connect_id==""){
		$hrconnect_data = "select * from locations where company_id='".$company_id."' and is_active='0' and id='".$location_id."'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$finance_connect_id = $hrconnect_details->finance_connect;
		if($finance_connect_id==0 || $finance_connect_id=="" ) {
		$hrconnect_data = "select * from company where company_id='".$company_id."' and is_active='0'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$finance_connect_id = $hrconnect_details->finance_connect;
		}
		if($finance_connect_id==0 || $finance_connect_id=="" ) {
		$finance_connect_id = "NOT-CONFIGURED";
		}
		}

		if($admin_connect_id==0 || $admin_connect_id==""){
		$hrconnect_data = "select * from locations where company_id='".$company_id."' and is_active='0' and id='".$location_id."'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$admin_connect_id = $hrconnect_details->admin_connect;
		if($admin_connect_id==0 || $admin_connect_id=="" ) {
		$hrconnect_data = "select * from company where company_id='".$company_id."' and is_active='0'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		$admin_connect_id = $hrconnect_details->admin_connect;
		}
		if($admin_connect_id==0 || $admin_connect_id=="" ) {
		$admin_connect_id = "NOT-CONFIGURED";
		}
		}

		$connectdetails = array('hr_connect'=>$hr_connect_id,'finance_connect'=>$finance_connect_id,'admin_connect'=>$admin_connect_id);
		return $connectdetails;
	}
	function empList($data){
		global $callConfig;
		$sql = "SELECT e.display_name,e.email_id,e.billable,ee.esgin_budget_approval_id,es.transaction_number FROM employees e left join `esgin_employees` ee on e.id = ee.emp_id left JOIN esgin_budget_approvals es on ee.esgin_budget_approval_id=es.id where e.vendor_id = '".$data['vendarid']."'";
		$datas = $callConfig->getAllRows($sql);
		return $datas;
	}
	function getbugetamountesignvendorwise($get){
		global $callConfig;
		$from_date = date('Y',strtotime($get['extra']))."-".date('m',strtotime($get['extra']))."-01";
		$to_date = date('Y',strtotime($get['extra']))."-".date('m',strtotime($get['extra']))."-31";
		/*$query = "SELECT vendor_id,sum(bill_amount*(billable_hrs/8)) bidget FROM project_timesheets WHERE emp_id IN (".$get['emp_ids'].") AND `timesheet_date`>= '".$from_date."' AND  `timesheet_date` <= '".$to_date."' GROUP By vendor_id";
		//echo $query;
		$bud_data = $callConfig->getAllRows($query);
		foreach($bud_data as $bud){
			$ven_bud[$bud->vendor_id] =  number_format($bud->bidget, 2, '.', '');
		}
		//echo "ravi"; print_r($ven_bud);
		$query1 = "SELECT * FROM esign_budget_consumed Where is_active=0";
		$ven_data = $callConfig->getAllRows($query1);
		foreach($ven_data as $esign_data){
			$esig_ven[$esign_data->vendor_id] =  number_format($esign_data->available, 2, '.', '');
		}
		//echo "Shankar";print_r($esig_ven);
		$check=0;
		foreach($ven_bud as $key=>$venn){
			//echo $key."/".$esig_ven[$key]."<".$venn."</br>";
			if($esig_ven[$key] < $venn){
				$check = 1; break;
				$aa['vendor_id'] = $key;
			}
		}*/
		//$query = "SELECT pt.esgin_budget_approval_id esign_id, eba.id esign_detail_id, pt.vendor_id, sum(pt.vrc_billable_amount) actual_amount FROM project_timesheets pt, eba_vendors_allocations eba WHERE pt.company_id = eba.company_id AND pt.esgin_budget_approval_id = eba.transaction_id AND pt.vendor_id = eba.item_id AND pt.company_id = ".$get['company_id']." AND emp_id IN (".$get['emp_ids'].") AND `timesheet_date` >= '".$from_date."' AND `timesheet_date` <= '".$to_date."'  GROUP By pt.esgin_budget_approval_id, eba.id, pt.vendor_id";

		$query = "SELECT eba.id esign_id,ebava.id esign_detail_id, ebava.item_id vendor_id, sum(pt.vrc_billable_amount) actual_amount FROM project_timesheets pt, esgin_budget_approvals eba, eba_vendors_allocations ebava,  esgin_employees ese WHERE  pt.company_id = eba.company_id AND eba.company_id = ebava.company_id AND eba.id = ebava.transaction_id AND eba.company_id = ese.company_id  AND ese.esgin_budget_approval_id = eba.id  AND ese.eba_vendors_allocation_id = ebava.id AND pt.company_id = ".$get['company_id']." AND pt.emp_id = ese.emp_id AND ese.valid_from = eba.effective_from_date AND ese.valid_to = eba.effective_to_date AND pt.timesheet_date >= eba.effective_from_date AND pt.timesheet_date <= eba.effective_to_date AND pt.is_active = 0 AND pt.is_active = ese.is_active AND pt.emp_id IN (".$get['emp_ids'].") AND pt.timesheet_date >=  '".$from_date."' AND pt.timesheet_date <= '".$to_date."' GROUP By eba.id, ebava.id,ebava.item_id";		

		$bud_data = $callConfig->getAllRows($query);
		foreach($bud_data as $bud){
			$ven_bud = array();
			$ven_bud['vendor'] =  $bud->vendor_id;
			$ven_bud['esign_id'] =  $bud->esign_id;
			$ven_bud['esign_detail_id'] =  $bud->esign_detail_id;
			$ven_bud['actual_amount'] =  round(number_format($bud->actual_amount, 2, '.', ''));
			$ven_budget[] = $ven_bud;
		}
		//echo "ravi"; print_r($ven_bud);

		$query1 = "SELECT * FROM esign_budget_consumed Where is_active=0";
		$ven_data = $callConfig->getAllRows($query1);
		foreach($ven_data as $esign_data){
			$esig_ven[$esign_data->vendor_id][$esign_data->esign_id][$esign_data->esign_detail_id] =  round(number_format($esign_data->available, 2, '.', ''));
		}
		//echo "Shankar";print_r($esig_ven);


		$check=0;
		foreach($ven_budget as $ven){
			//echo $key."/".$esig_ven[$key]."<".$venn."</br>"; budget < actual
			if($esig_ven[$ven['vendor']][$ven['esign_id']][$ven['esign_detail_id']] < $ven['actual_amount']){
				
				$aa['vendor_id'] = $ven['vendor'];
				$aa['esign_id'] = $ven['esign_id'];
				$aa['esign_detail_id'] = $ven['esign_detail_id'];
				$aa['actual_amount'] = $ven['actual_amount'];
				$aa['budget_amount'] = $esig_ven[$ven['vendor']][$ven['esign_id']][$ven['esign_detail_id']];
				$check = 1; break;
			}
		}
		$aa['check'] = $check;
		return $check;
		//return $query;
	}
	function deleteTimeSheets($data){
		global $callConfig;
		$sql = "UPDATE `project_timesheets` SET `is_active`= NULL WHERE id in(".$data['id'].")";
		$res=$callConfig->executeQueryString($sql);
		return $sql;
	}
	function togetemployeesesignvenderratecarddetail($get){
		global $callConfig;
		$query = "SELECT * from employees WHERE id=".$get['emp_id'];
		$emp_data = $callConfig->getRow($query);
		$query2 = "SELECT * from esgin_employees WHERE is_active=0 AND emp_id=".$get['emp_id'];
		$esg_data = $callConfig->getRow($query2);
		$query3 = "SELECT * from vendor_rate_cards  WHERE is_active=0 AND item_id=".$emp_data->vendor_id;
		$vrc_data = $callConfig->getRow($query3);
	}
	function getExpireAgreementsList($data){
		global $callConfig;
		$sql = "SELECT va.id,va.agreement_name,va.expiry_date,v.display_name FROM `vendor_agreements` va LEFT JOIN vendors v on va.vendor_id = v.id WHERE va.expiry_date < '".date('Y-m-d')."'";
		$datas = $callConfig->getAllRows($sql);
		return $datas;
	}
	function timeSheetHrsDashboard($data){
		global $callConfig;	
		for ($i=0; $i<6; $i++){ 
			$ssdate = strtotime("-".$i." month",time()); 	
			$startdate = date('Y-m-d',strtotime('first day of this month', $ssdate));
			$enddate =  date('Y-m-d',strtotime('last day of this month', $ssdate));
			$sql = "SELECT sum(billable_hrs) as sum FROM `project_timesheets` pt LEFT JOIN employees e on pt.emp_id = e.id WHERE pt.company_id='".$data['company_id']."' and pt.is_active=0 and pt.timesheet_date  >= '".$startdate."' and pt.timesheet_date <= '".$enddate."' and e.vendor_id = '".$data['vendor_id']."'";
			$row=$callConfig->getRow($sql);
			$string[]=round($row->sum); 
		}
			$return =  array("timesheet_total_hrs"=>$string);
			return $return;
	}
	function nextMonthProjection($data){
		global $callConfig;	
		$nextstartdate = date('Y-m-d',strtotime('first day of next month'));
 		$nextlastdate =  date('Y-m-d',strtotime('last day of next month'));
		$startdate = date('Y-m-d',strtotime('first day of this month'));
		$enddate =  date('Y-m-d',strtotime('last day of this month'));
		$sql = "SELECT sum(eva.item_quantity) as sum FROM `esgin_budget_approvals` eba LEFT JOIN eba_vendors_allocations eva on eba.id = eva.transaction_id WHERE eba.company_id = '".$data['company_id']."' and eba.is_active = 0 and (eba.effective_from_date  >= '".$nextstartdate."' and eba.effective_from_date <= '".$nextlastdate."' OR eba.effective_to_date  >= '".$nextstartdate."' and eba.effective_to_date <= '".$nextlastdate."')";
		$row=$callConfig->getRow($sql);
		$sql2 = "SELECT sum(eva.item_quantity) as sum FROM `esgin_budget_approvals` eba LEFT JOIN eba_vendors_allocations eva on eba.id = eva.transaction_id WHERE eba.company_id = '".$data['company_id']."' and eba.is_active = 0 and (eba.effective_from_date  >= '".$startdate."' and eba.effective_from_date <= '".$enddate."' OR eba.effective_to_date  >= '".$startdate."' and eba.effective_to_date <= '".$enddate."')";
		$row2=$callConfig->getRow($sql2);
		$thismonth = $row2->sum ==""?"0":$row2->sum;
		$return =  array("nextmonthprojects"=>round($row->sum),"thismonthprojects"=>round($thismonth));
		return $return;
	}
	function togetvendoremployeedetails($get){
		global $callConfig;
		$query = "SELECT * FROM employees WHERE vendor_id =".$get['vendor_id']." AND is_active=0";
		$vendors =$callConfig->getAllRows($query);
		$total_review_count= array(); $main = array();
		foreach ($vendors as $key=>$employee) {
			$pdata = array();
			
			$query1 = "SELECT SUM(`bill_amount`) as bill_amount,sum(`billable_hrs`) as billable_hrs, sum(`nonbillable_hrs`) as nonbillable_hrs FROM `project_timesheets` WHERE `emp_id`=".$employee->id." AND `timesheet_date`>='".$get['fromdate']."' AND `timesheet_date` <= '".$get['todate']."' AND is_active=0";
			//echo $query1;
			$data1 = $callConfig->getRow($query1);
			$pdata['bill_amount']=$data1->bill_amount;
			$pdata['billable_hrs']=$data1->billable_hrs;
			$pdata['nonbillable_hrs']=$data1->nonbillable_hrs;
			$pdata['emp_id']=$employee->id;
			$pdata['name']=$employee->display_name;
			$pdata['email_id']=$employee->email_id;
			$pdata['image']=$employee->image;
			$main[] = $pdata;
		}
		return $main;
	}
	function findbuddyid($checkcriteria,$data){
		global $callConfig;
		$buddy_id =0;
		$employee_id = $data['emp_id'];
		$company_id = $data['company_id'];
		$empdata = "select * from employees where id=".$employee_id." and is_active='0' and company_id='".$company_id."' and is_active =0"; 
		$edata = $callConfig->getRow($empdata);
		$location_id =$edata->location_id;
		$hrconnect_data = "select * from employee_buddies where employee_id='".$employee_id."' and is_active='0' and company_id='".$company_id."'";
		$hrconnect_details = $callConfig->getRow($hrconnect_data);
		if($checkcriteria['workflow_configuration']->sub_approve_type=="AB"){
			$buddy_id = $hrconnect_details->admin_buddy;
		}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="FB"){
			$buddy_id = $hrconnect_details->finance_buddy;
		}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="HB"){
			$buddy_id = $hrconnect_details->hr_buddy;
		}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="PB"){
			$buddy_id = $hrconnect_details->project_buddy;
		}
		if($buddy_id==0 || $buddy_id==""){
			$hrconnect_data = "select * from locations where company_id='".$company_id."' and is_active='0' and id='".$location_id."'";
			$hrconnect_details = $callConfig->getRow($hrconnect_data);
			if($checkcriteria['workflow_configuration']->sub_approve_type=="AB"){
				$buddy_id = $hrconnect_details->admin_connect;
			}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="FB"){
				$buddy_id = $hrconnect_details->finance_connect;
			}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="HB"){
				$buddy_id = $hrconnect_details->hr_connect;
			}elseif($checkcriteria['workflow_configuration']->sub_approve_type=="PB"){
				$buddy_id = $hrconnect_details->project_connect;
			}
			if($buddy_id==0 || $buddy_id=="") {
				$hrconnect_data = "select * from config where company_id='".$company_id."' and is_active='0'";
				$hrconnect_details = $callConfig->getAllRows($hrconnect_data);
				$buddy_id = $hrconnect_details[0]->default_hr;
			}
		}
		return $buddy_id;
	}
	function editTimeSheetBillableHrs($data,$get){
		global $callConfig;
		foreach($data['popbillablehorse'] as $key=>$value){
			$fields = array('billable_hrs' =>$data['popbillablehorse'][$key],'updated_by'=>$data['user_id']);
			$whrcon = "id='".$data['poptimesheetid'][$key]."'";
			$res1 = $callConfig->updateRecord('project_timesheets',$fields,'','',$whrcon);
			$insertary = array('is_active'=>'0','company_id'=>$data['company_id'],'project_timesheet_id'=>$data['poptimesheetid'][$key],'billable_hrs'=>$data['popbillablehorse'][$key],'billable_hrs_old'=>$data['popbillablehorseold'][$key],'current_status'=>'0','created_by'=>$data['user_id'],'timesheet_date'=>$data['timesheet_date'][$key]);
			$res = $callConfig->insertRecord('project_timesheet_logs',$insertary);
		}
		return $res1;
	}
	function getTimeSheetWorkEntryWdit($data){
		global $callConfig;
		$attstc = "SELECT id,timesheet_date,task_name,billable_hrs FROM project_timesheets WHERE company_id = '".$data['company_id']."' and is_active = 0 AND id IN(".$data['id'].")";
		$atsdata = $callConfig->getAllRows($attstc);
		return $atsdata;
	}
	function insertPmcAsignEmployee($data,$get){
		global $callConfig;
		$emp_id = $_POST['emp_id'];
		$returnarray = array();
		$workcheck = "SELECT id,work_order_id FROM emp_work_order WHERE company_id= '".$data['company_id']."' AND work_order_id !=".$data['workorder_id']." AND is_active= '0' AND emp_id=".$emp_id;
		$workcheckdata = $callConfig->getRow($workcheck);
		if($workcheckdata->id !="" AND $data['newcreate'] !="Y"){
			$returnarray['other_work_order'] = 'Y';
		}else{
			if($data['newcreate']=="Y"){
				$insert_array['is_active']= NULL;
				$insert_arrayold = array('is_active'=>NULL,'updated_by'=>$data['user_id'],'updated_date'=>date('Y-m-d H:i:s'));  
				$where = "id = '".$workcheckdata->id."'";
				$res=$callConfig->updateRecord("emp_work_order",$insert_array,"","",$where);
				$returnarray['other_work_order_update'] = 'Y- '.$workcheckdata->id;
			}
			$returnarray['other_work_order'] = 'N';
			$query2 = "SELECT * FROM emp_work_order WHERE company_id= '".$data['company_id']."' AND work_order_id=".$data['workorder_id']." AND emp_id=".$emp_id;
			$datas = $callConfig->getRow($query2);
			if($datas->id!=""){
				if($data['inserttype'] == "N"){
					$insert_array['is_active']= NULL;
				}else{
					$insert_array = array('work_order_id'=>$data['workorder_id'],'work_order_detail_id'=>$data['workorder_detail_id'],'vrc_id'=>$data['vrc_id'],'vrc_item_id'=>$data['vrcdetail_id'],'price'=>$data['item_price'],'effective_from_date'=>$data['effective_date'],'effective_to_date'=>$data['expire_date'],'emp_id'=>$emp_id,'is_active'=>'0','current_status'=>'0','updated_by'=>$data['user_id'],'updated_date'=>date('Y-m-d H:i:s'));  
				}
				$where = "id = '".$datas->id."'";
				$res=$callConfig->updateRecord("emp_work_order",$insert_array,"","",$where);
				$returnarray['update'] = 'Y-'.$datas->id;
			}else{
				$insert_array = array('company_id'=>$data['company_id'],'work_order_id'=>$data['workorder_id'],'work_order_detail_id'=>$data['workorder_detail_id'],'vrc_id'=>$data['vrc_id'],'vrc_item_id'=>$data['vrcdetail_id'],'price'=>$data['item_price'],'effective_from_date'=>$data['effective_date'],'effective_to_date'=>$data['expire_date'],'emp_id'=>$emp_id,'is_active'=>'0','current_status'=>'0','created_by'=>$data['user_id'],'created_date'=>date('Y-m-d H:i:s'));  
				$res = $callConfig->insertRecord('emp_work_order',$insert_array);
				$returnarray['insert'] = 'Y';
			}
		}		
		return $returnarray;
	}
	function getAssignEmployeeListPopup($data,$get){
		global $callConfig;
		$data = $data['where'];
		$condition = "";
		if($data['department_id']!=""){  $condition .= "AND department_id = '".$data['department_id']."'"; }
		if($data['designation_id']!=""){ $condition .=  " AND designation_id = '".$data['designation_id']."'";  }
		if($data['location_id']!=""){ $condition .=  " AND location_id = '".$data['location_id']."'"; }
		if($data['vendor_id']!=""){ $condition .=  " AND vendor_id = '".$data['vendor_id']."'"; }
		if($data['cost_center_id']!=""){ $condition .=  " AND cost_center_id = '".$data['cost_center_id']."'"; }
		if($data['emp_name_code']!=""){ $condition .=  " AND  (display_name like '%".$data['emp_name_code']."%' OR  emp_code like '%".$data['emp_name_code']."%') "; }
		$query2 = "SELECT * FROM employees WHERE is_active=0 AND company_id = '".$data['company_id']."'".$condition;
		$datas = $callConfig->getAllRows($query2);
		$returnarray['emp_list'] = $datas;
		
		$dep = "SELECT id, department_name FROM departments WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$depdetails = $callConfig->getAllRows($dep);
		$returnarray['department'] = $depdetails;
		
		$designation = "SELECT id, designation_name FROM designations WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$designationdeatils = $callConfig->getAllRows($designation);
		$returnarray['designation'] = $designationdeatils;
		
		$location_name = "SELECT id, location_name FROM locations WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$location_namedetails = $callConfig->getAllRows($location_name);
		$returnarray['locations'] = $location_namedetails;
		
		$vendors = "SELECT id, company, display_name FROM vendors WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$vendorsdetails = $callConfig->getAllRows($vendors);
		$returnarray['vendors'] = $vendorsdetails;
		
		$cost_centers = "SELECT id, cost_center_name FROM cost_centers WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$cost_centersdetails = $callConfig->getAllRows($cost_centers);
		$returnarray['cost_centers'] = $cost_centersdetails;
		return $returnarray;
	}
  	function getmanpowercontarctratperhrs($post, $get){
    	global $callConfig;
    	$key = ''; $effective_date = ''; $expire_date = '';
    	$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$post['company_id'];
    	$holidaysdata = $callConfig->getAllRows($holidays);
    	foreach ($holidaysdata as $hkey=>$hvalue) {
    		$holidaysdatas[] = $hvalue->holiday_date;
    	}
    	$attendancewhurl1 = "SELECT id,minimum_hours_required,working_days FROM attendance_settings WHERE is_active=0 and company_id=".$post['company_id'];
    	$attendance_settings = $callConfig->getRow($attendancewhurl1);
    	$minimum_hours = explode(':',$attendance_settings->minimum_hours_required);

    	if($post['uom'][$key] !=""){
    		$uom = $post['uom'][$key];
    		$price= $post['amount'][$key];
    		$minimum_hours_required = $minimum_hours[0].'.'.$minimum_hours[1];
    		$total_working_day = count(json_decode($attendance_settings->working_days,1));
    		$working_days = json_decode($attendance_settings->working_days,1);
    		$workday =0;
			// $holiday_dates = json_decode($post['holiday_dates'],1);
    		if($uom == 'PH'){
    			$perhrsrate = $price;
    		}else if($uom == 'PD'){
    			$perhrsrate = $price/$minimum_hours_required;
    		}else if($uom == 'PW'){
    			$perhrsrate = $price/($minimum_hours_required*$total_working_day);
    		}else{
    			$dateFrom = new DateTime($effective_date);
    			$dateTo = new DateTime($expire_date);
    			$datenames = [];
    			while($dateFrom <= $dateTo){
    				if(in_array($dateFrom->format('D'),$working_days) AND !in_array($dateFrom->format('Y-m-d'), $holidaysdatas)){
    					$workday++;
    				}
    				$dateFrom->modify('+1 days');
    			}
    			$perhrsrate = $price/($minimum_hours_required*$workday);
    		}
    	}
    	return round($perhrsrate,2);
    }
    function esigncunsumebugetchage($get){
		global $callConfig;
		$bquery = "SELECT pt.esgin_budget_approval_id esign_id, eba.id esign_detail_id, pt.vendor_id, sum(pt.vrc_billable_amount) actual_amount FROM project_timesheets pt, eba_vendors_allocations eba WHERE pt.company_id = eba.company_id AND pt.esgin_budget_approval_id = eba.transaction_id AND pt.vendor_id = eba.item_id AND pt.company_id = ".$get['company_id']." AND emp_id IN (".$get['emp_ids'].") AND MONTH(timesheet_date)=MONTH('".$get['extra']."')  GROUP By pt.esgin_budget_approval_id, eba.id, pt.vendor_id";
		
		$besign = $callConfig->getAllRows($bquery);
		foreach($besign as $vendoramount){
			$updatequery = "UPDATE esign_budget_consumed SET consumed=consumed+".$vendoramount->actual_amount.",available=available-".$vendoramount->actual_amount." WHERE company_id = ".$get['company_id']."	AND vendor_id =".$vendoramount->vendor_id." AND esign_id  = ".$vendoramount->esign_id." AND esign_detail_id  = ".$vendoramount->esign_detail_id;
			$res1 = $callConfig->executeQuery($updatequery);
		}
	}
	function gettimesheetentries($get){
     	global $callConfig;
    	$days = explode(',',$get['selected_week']);
		foreach($days as $date) {
    		$quer_5="SELECT *  FROM ".TBL_PROJECT_TIMESHEETS." WHERE company_id='".$get['company_id']."' and is_active=0 and emp_id=".$get['emp_id']." and timesheet_date='".$date."'";
    		$empdatas  = $callConfig->getAllRows($quer_5);
    		$k=0; foreach($empdatas as $task) {
	   		$tasks[$k] = $task->task_name;
    			$timesheetdata[$k][$task->timesheet_date] = $task;
    			$stauseandid[$k][$task->id] = $task->current_status;
    			$workorderid = $task->work_order; 
    			$k++;}
    		}
    		$details['days'] = $days;
    		$details['task'] = $tasks;
    		$details['timesheets'] = $timesheetdata;
    		$details['stauseandid'] = $stauseandid;
    		//$query = "SELECT sum(if(current_status=".ST_APPROVED.",billable_hrs,0)) as approve , sum(billable_hrs) as submit from project_timesheets where emp_id =".$get['emp_id']." And work_order =".$workorderid." AND  is_active = '0' AND `timesheet_date` >= '".$get['startingdate']."' AND `timesheet_date` < '".$get['endingdate']."'";
    		$query = "SELECT sum(if(current_status=".ST_APPROVED.",billable_hrs,0)) as approve , sum(billable_hrs) as submit from project_timesheets where emp_id =".$get['emp_id']." AND  is_active = '0' AND `timesheet_date` >= '".$get['startingdate']."' AND `timesheet_date` < '".$get['endingdate']."'";
    		$res = $callConfig->getRow($query);
    		$details['approve_hrs'] = $res->approve;
    		$details['billable_hrs'] = $res->submit;
    		return $details;
    }
	function addtimesheetentries($post, $get){
        global $callConfig;
        $testarrau = array();
        $check = 1;
    	$bilableamtcalculation = $this->getBillableAmountTimesheetcalculatio($post,$get);
    	$testarrau[] =$bilableamtcalculation;
    	$mpc_rate_per_hrs = round($bilableamtcalculation['mpc_rate_per_hrs'],2);
    	$vrc_rate_per_hrs = round($bilableamtcalculation['vrc_rate_per_hrs'],2);
    	$esignid = $bilableamtcalculation['esigndata']->esgin_budget_approval_id;
    	$vrcid = $bilableamtcalculation['esigndata']->vrc_id;
    	//$esignid = $vrcid = '0';
		$queryemp = $callConfig->selectQuery('config','esign_time_sheet_lock,esign_buget_approval_lock','company_id =1 AND is_active=0','','','');
		$config = $callConfig->getRow($queryemp);
		if($config->esign_time_sheet_lock == 1){
			$esignid=1;
		}
        if($esignid !="" AND  $post['timesheetdata']['0']['work_order'] !=""){	
        	for($i=0;$i<count($post['timesheetdata']);$i++){
        		$rowid = $post['timesheetdata'][$i]['rowid'];
        		unset($post['timesheetdata'][$i]['rowid']);
        		$post['timesheetdata'][$i]['esgin_budget_approval_id'] = $esignid!="" ? $esignid:'0';
        		$post['timesheetdata'][$i]['vendor_rate_card_id'] = $vrcid!=""?$vrcid: 0 ;
        		$post['timesheetdata'][$i]['rate_per_hrs'] = $mpc_rate_per_hrs;		
        		$testarrau['allow_overtime']= $bilableamtcalculation['mpcitemddetails']->allow_overtime;
				$minimum_hours_required = $bilableamtcalculation['minimum_hours_required'];
				$testarrau['minimum_hours_required']= $minimum_hours_required;
        		if($bilableamtcalculation['mpcitemddetails']->allow_overtime == '1'){
        			$bilablehrsget = $post['timesheetdata'][$i]['billable_hrs'];
        			if($minimum_hours_required < $post['timesheetdata'][$i]['billable_hrs']){
        				$bilablehrsget = $minimum_hours_required;
        			}
        		}else{
        			$bilablehrsget = $post['timesheetdata'][$i]['billable_hrs'];
        		}
        		$testarrau['bilablehrsget'][] =$bilablehrsget;
        		$post['timesheetdata'][$i]['bill_amount'] = round($mpc_rate_per_hrs*$bilablehrsget,2);
        		$post['timesheetdata'][$i]['vrc_billable_amount'] = round($vrc_rate_per_hrs*$bilablehrsget,2);

        		$testarrau['mpc_rate_per_hrs'][] =$post['timesheetdata'][$i]['bill_amount'];
        		$testarrau['vrc_rate_per_hrs'][] =$post['timesheetdata'][$i]['vrc_billable_amount'];
        		if($rowid==""){
        			$check_data = $post['timesheetdata'][$i];
        			$check_query = "SELECT sum(billable_hrs) hrs FROM `project_timesheets` WHERE company_id = ".$check_data['company_id']." AND emp_id = ".$check_data['emp_id']." AND timesheet_date = ".$check_data['timesheet_date']." AND is_active = 0";
        			$check_val = $callConfig->getRow($check_query);
        			if($check_val->hrs !="" && $check_val->hrs >= 8 ){
        				$testarrau['error'] = '9110';
        			}else{
        			$post['timesheetdata'][$i]['created_date'] = date('Y-m-d H:i:s');
         			$post['timesheetdata'][$i]['created_by'] = $get['user_id'];

        			$qresponse=$callConfig->insertRecord(TBL_PROJECT_TIMESHEETS,$post['timesheetdata'][$i]);
					$testarrau['lastid'] = $qresponse['LASTID'];  
					}
        		}else{
        			$post['timesheetdata'][$i]['updated_date'] = date('Y-m-d H:i:s');
        			$post['timesheetdata'][$i]['updated_by'] = $get['user_id'];
        			$qresponse=$callConfig->updateRecord(TBL_PROJECT_TIMESHEETS,$post['timesheetdata'][$i],'id',$rowid);
        		}
        	}
        }else{
        	$testarrau['error'] = '404';
        }
    	return $testarrau;
    	//return $post;
    } 
	function getBillableAmountTimesheetcalculatio($post,$get){
    	global $callConfig;
    	$testarrau = array();
    	$query ="SELECT ewo.work_order_id, ewo.vrc_id, ese.esgin_budget_approval_id,ewo.work_order_detail_id FROM `emp_work_order` ewo, esgin_employees ese WHERE ewo.company_id = ese.company_id AND ewo.emp_id = ese.emp_id AND ewo.company_id = '".$get['company_id']."' AND ewo.emp_id = '".$post['timesheetdata']['0']['emp_id']."' AND ewo.is_active = ese.is_active AND ese.is_active = 0 and ewo.work_order_id = '".$post['timesheetdata']['0']['work_order']."'";
    	$esigndetails = $callConfig->getRow($query);
    	$testarrau['esigndata'] = $esigndetails;
    	$mpcitem ="SELECT mi.purchase_uom,mi.item_price as item_amount,vi.allow_overtime ,vi.max_leave,vi.purchase_uom as vrci_purchase_uom,vi.item_price as vrci_item_price,ewo.vrc_item_id FROM `emp_work_order` ewo left JOIN mpc_items mi ON ewo.work_order_detail_id = mi.id LEFT join vrc_items vi ON ewo.vrc_item_id = vi.id WHERE ewo.work_order_id = '".$post['timesheetdata']['0']['work_order']."' AND ewo.emp_id = '".$post['timesheetdata']['0']['emp_id']."' AND ewo.is_active = '0' AND ewo.company_id='".$get['company_id']."'";
    	$mpcitemddetails = $callConfig->getRow($mpcitem);
    	$testarrau['mpcitemddetails'] = $mpcitemddetails;
            	
		$employeesql ="SELECT location_id FROM employees where company_id = '".$get['company_id']."' AND id = '".$post['timesheetdata']['0']['emp_id']."' AND is_active = 0";
		$employeerow = $callConfig->getRow($employeesql);
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND location_id= '".$employeerow->location_id."'";
    	$holidaysdata = $callConfig->getAllRows($holidays);
    	foreach ((array)$holidaysdata as $hkey=>$hvalue) {
    		$holidaysdatas[] = $hvalue->holiday_date;
    	}
    	$attendancewhurl1 = "SELECT id,minimum_hours_required,working_days FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
    	$attendance_settings = $callConfig->getRow($attendancewhurl1);
    	$testarrau['attendance_settings'] = $attendance_settings;
    	$minimum_hours = explode(':',$attendance_settings->minimum_hours_required);
    	$minimum_hours_required = $minimum_hours[0].'.'.$minimum_hours[1];
    	$total_working_day = count(json_decode($attendance_settings->working_days,1));
    	$working_days = json_decode($attendance_settings->working_days,1);
    	$leavecnt = 0;
    	$leavesqql = "SELECT employee_id, '".$post['startingdate']."' config_from_date, '".$post['endingdate']."' config_to_date, leave_from_date, leave_to_date, CASE WHEN ((leave_from_date between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (leave_to_date - Leave_from_date)+1 WHEN ((leave_from_date between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date not between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (date('".$post['endingdate']."') - Leave_from_date)+1 WHEN ((leave_from_date not between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (leave_to_date - date('".$post['startingdate']."'))+1 END leave_days from ( SELECT employee_id, leave_from AS leave_from_date, leave_to AS leave_to_date, company_id FROM `employee_leaves` where (`leave_from` >= '".$post['startingdate']."' and leave_from <= '".$post['endingdate']."') OR (`leave_to` >= '".$post['startingdate']."' and leave_to <= '".$post['endingdate']."') AND current_status = '".ST_APPROVED."' AND employee_id = '".$post['timesheetdata']['0']['emp_id']."' ) a";
    	$leavedatas = $callConfig->getAllRows($leavesqql);
    	foreach ((array)$leavedatas as $leaverow) {
    		$leavecnt += $leaverow->leave_days;
    	}
    	$testarrau['startdate'] = $post['startingdate'];
    	$testarrau['enddate'] = $post['endingdate'];
    	//$testarrau['enddate']  = $leavesqql;
    	if(TIMESHEET_RATE_CALCULATION_TYPE == 1){
    		$totaldays=((strtotime($post['endingdate']) - strtotime($post['startingdate'])) / (60 * 60 * 24))+1;
    	}else{
    		$workday = 0;
    		$strtsate = $post['startingdate'];
    		$enddate =$post['endingdate'];
    		$dateFrom = new DateTime($strtsate);
    		$dateTo = new DateTime($enddate);
    		$datenames = [];
    		$calculationdays =0;
    		$maxleaveused = $mpcitemddetails->max_leave;
    		while($dateFrom <= $dateTo){
    			$calculationdays++; 
    			if(in_array($dateFrom->format('l'),(array)$working_days) AND !in_array($dateFrom->format('Y-m-d'), (array)$holidaysdatas)){
    				$testarrau['working_dates'][] = $dateFrom->format('Y-m-d');
					//$testarrau['days'][] = $dateFrom->format('l');
    				$workday++;
    			}
    			$dateFrom->modify('+1 days');
    		}
    		$lev = $leavecnt;
    		$maxlim = round($mpcitemddetails->max_leave);
    		$testarrau['maxleave'] = $maxlim;
    		$testarrau['totalleave'] = $lev;
    		$leavedays = 0;
    		if($maxlim <= $lev){  $leavedays = $lev-$maxlim;  $workday = $workday-$leavedays; }
    		$totaldays = $workday;	
    	}
    	$testarrau['mpc_rate_per_hrs'] = $this->ratePerHrs($mpcitemddetails->purchase_uom ,$mpcitemddetails->item_amount,$total_working_day,$minimum_hours_required,$totaldays);
    	$testarrau['vrc_rate_per_hrs'] = $this->ratePerHrs($mpcitemddetails->vrci_purchase_uom ,$mpcitemddetails->vrci_item_price,$total_working_day,$minimum_hours_required,$totaldays);
    	
    	$testarrau['totaldays'] = $totaldays;
		$testarrau['minimum_hours_required'] = $minimum_hours_required;
    	$testarrau['calculationdays'] = $calculationdays;
    	return $testarrau;
    }
    function ratePerHrs($purchase_uom,$item_amount,$total_working_day,$minimum_hours_required,$totaldays){
    	$rate_per_hrs = 0;
    	if($purchase_uom =="PH"){
    		$rate_per_hrs = $item_amount;
    	}elseif($purchase_uom =="PD"){
    		$rate_per_hrs = $item_amount/$minimum_hours_required;  
    	}elseif($purchase_uom =="PW"){
    		$monthamount = $item_amount/$total_working_day;
    		$rate_per_hrs = $monthamount/($minimum_hours_required*$totaldays);  
    	}elseif($purchase_uom =="PM"){
    		$monthamount = $item_amount/$totaldays;
    		$rate_per_hrs = ($monthamount/$minimum_hours_required);  
    	}elseif($purchase_uom =="PT"){
    		$monthamount = ($item_amount/3);
    		$rate_per_hrs = $monthamount/($minimum_hours_required*$totaldays);  
    	}elseif($purchase_uom =="PS"){
    		$monthamount = ($item_amount/6);
    		$rate_per_hrs = $monthamount/($minimum_hours_required*$totaldays);  
    	}elseif($purchase_uom =="PY"){
    		$monthamount = ($item_amount/12);
    		$rate_per_hrs = $monthamount/($minimum_hours_required*$totaldays);  
    	}
    	return $rate_per_hrs;
    }
	function toretriveminhorsandweksanddayebasedondates($get){
		global $callConfig;
		$from = $get['from_date'];
		$to = $get['to_date'];
		$location_sql = "SELECT id FROM locations WHERE is_active=0 and company_id=".$get['company_id'];
		$locations = $callConfig->getAllRows($location_sql);
		$holidays = "SELECT holiday_date, location_id FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND holiday_date >='".$get['from_date']."' AND holiday_date <='".$get['to_date']."'";
		//echo $holidays;
		$holidaysdata = $callConfig->getAllRows($holidays);
		foreach ($holidaysdata as $hkey=>$hvalue) {
			$holidaydates[$hvalue->location_id][] = $hvalue->holiday_date;
			//$locations_holidays[$hvalue->location_id] = $hvalue->location_id;
		}
		//print_r($holidaydates);
		$attendancewhurl1 = "SELECT id,minimum_hours_required,working_days FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$attendance_settings = $callConfig->getRow($attendancewhurl1);
		$testarrau['attendance_settings'] = $attendance_settings;
		$minimum_hours = explode(':',$attendance_settings->minimum_hours_required);
		$minimum_hours_required = $minimum_hours[0].'.'.$minimum_hours[1];
		$weekly_working_days = json_decode($attendance_settings->working_days,1);
		$work_days=0;
		$datediff = strtotime($to) - strtotime($from);
		$datediff = floor($datediff/(60*60*24));
		foreach($locations as $location){
		$work_days = 0;
		$work_dates = array();
			for($i = 0; $i < $datediff+1; $i++){			
				$valda =  date("Y-m-d", strtotime($from . ' + ' . $i . 'day'));
				$valday = ucwords(date("l", strtotime($from . ' + ' . $i . 'day')));	
				//echo $valday."step1".$valday."--";  		
				if(in_array($valday, $weekly_working_days)){
					if(!in_array($valda, $holidaydates[$location->id])){ //echo "step2";
						$work_days = $work_days+1;
						$work_dates[] = $valda;
					}
				}
			}
			$return[$location->id] = array('holidaydates'=>$holidaydates[$location->id],'count_hoilydays'=>count($holidaydates[$location->id]),'minimum_hours'=>$minimum_hours_required,'week_days'=>$weekly_working_days,'count_week'=>count($weekly_working_days),'count_working_days'=>$work_days,'work_dates'=>$work_dates);
		}	
		//print_r($return);
		return 	$return;
	}	 
	function togetesignvendororderdata($data){
    	global $callConfig;
    	$query ="SELECT
    	eba.id esign_id,
    	vrc.id vendor_rate_card_id,		
    	vrci.id vendor_rate_card_item_id,
    	vrci.item_price bill_rate,
    	vrci.purchase_uom uom,
    	IF(vrci.purchase_uom!='PM', vrci.item_price/(12*60),vrci.item_price/160) hour_bill
    	FROM
    	esgin_employees ese,
    	esgin_budget_approvals eba,
    	employees emp,
    	vendor_rate_cards vrc,
    	vrc_items vrci
    	WHERE
    	ese.company_id = eba.company_id
    	AND ese.is_active = 0
    	AND ese.emp_id = ".$data['emp_id']."
    	AND ese.company_id = eba.company_id
    	AND ese.esgin_budget_approval_id = eba.id
    	AND emp.company_id = ese.company_id
    	AND emp.id = ".$data['emp_id']."
    	AND vrc.company_id = ese.company_id
    	AND vrc.ref_id = ese.esgin_budget_approval_id
    	AND vrci.transaction_id = vrc.id
    	AND vrci.item_id = emp.job_title_id";
		//echo $query; exit;
    	$return = $callConfig->getRow($query);
    	$return = (array)$return;
    	return $return;
    }
    function getprojecttimesheetworkorderid($data){
		global $callConfig;
		$workordersql = "SELECT mpc.id,mpc.transaction_number  
		FROM `emp_work_order` ewo, manpower_contracts mpc  
		WHERE   ewo.company_id='".$data['company_id']."'
		AND ewo.company_id = mpc.company_id
		AND ewo.work_order_id = mpc.id
		AND ewo.emp_id='".$data['emp_id']."'
		AND ewo.work_order_detail_id !='' 
		AND ewo.work_order_id !='' 
		AND ewo.vrc_id !='' 
		AND ewo.vrc_item_id !=''
		AND ewo.is_active = 0 
		AND ewo.effective_from_date <= '".$data['startdate']."'
		AND ewo.effective_to_date >= '".$data['enddate']."'";
		$workorderlist = $callConfig->getAllRows($workordersql);
		return $workorderlist;
	}
	function togetempvmstransactionslist($get){
		global $callConfig;
		$month = $get['month'];
		$year = $get['year'];
		$startdate = $get['year']."-".$get['month']."-01";
		$endtdate = date("Y-m-t",strtotime($startdate));
		$query = "SELECT emp.id, emp.display_name, po.work_order_id , po.vrc_id, esg.esgin_budget_approval_id ,esb.transaction_number etn, mc.transaction_number mtn, vc.transaction_number vtn FROM employees emp, emp_work_order po, esgin_employees esg, esgin_budget_approvals esb, manpower_contracts mc,vendor_rate_cards vc WHERE po.emp_id = emp.id AND esg.emp_id = emp.id AND esb.id = esg.esgin_budget_approval_id AND mc.id = po.work_order_id AND vc.id = po.vrc_id AND emp.is_active=0 AND po.is_active=0 AND esg.is_active=0 AND esg.valid_from < '".$endtdate."' AND esg.valid_to >= '".$startdate."'";
		$details  = $callConfig->getAllRows($query);
		//print_r($details);
		foreach ($details as $detail){
			$return[$detail->id] =$detail;
		}
		return $return;
	}
	function getcheckexpirydetails($get){
		global $callConfig;
		$result = array();
		$conditionemp = "";
		$conditionoth = "";
		$conrec = 0;
		$formdate=Date('Y-m-d');
		$validation_display=json_decode(EXPIRY_DOCUMENT_LIST,1);
		$todate=Date('Y-m-d', strtotime('+'.EXPIRY_DOCUMENT_DAYS.' days'));
		if($get['adminrole']=='0'){
			$conditionemp = ' AND id= "'.$get['emp_id'].'"';
			$conditionoth = ' AND emp_id= "'.$get['emp_id'].'"';
		}
		if($get['adminrole']=='1'){
			if($validation_display['manpower_contracts']=='Y'){
				$emp = "SELECT id,effective_to_date,transaction_number  FROM `manpower_contracts` WHERE effective_to_date >= '".$formdate."' AND effective_to_date <= '".$todate."' AND is_active = 0 AND company_id = '".$get['company_id']."' ";
				$empres = $callConfig->getAllRows($emp);
				foreach ($empres as $key => $value){
					$conrec++;
					$reamainleaveday = ((strtotime($value->effective_to_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
					$result['comman'][] = array('id'=>$value->id,'option'=>'COM_INSERT_MANPOWER_CONTRACTS','effective_to_date'=>$value->effective_to_date,'display_name'=>$value->transaction_number,'remainingdays'=>$reamainleaveday,'msg'=>'purchase order'); 
				}
			}
			if($validation_display['esgin_budget_approvals']=='Y'){
				$esign = "SELECT id,effective_to_date,transaction_number  FROM `esgin_budget_approvals` WHERE effective_to_date >= '".$formdate."' AND effective_to_date <= '".$todate."' AND is_active = 0 AND company_id = '".$get['company_id']."' ";
				$esinv = $callConfig->getAllRows($esign);
				foreach ($esinv as $esinv) {
					$conrec++;
					$reamainleaveday = ((strtotime($esinv->effective_to_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
					$result['comman'][] = array('id'=>$esinv->id,'option'=>'COM_INSERT_ESGIN_BUDGET_APPROVALS','effective_to_date'=>$esinv->effective_to_date,'display_name'=>$esinv->transaction_number,'remainingdays'=>$reamainleaveday,'msg'=>'Esign Budget Approval'); 
				}
			}
			if($validation_display['vendor_rate_cards']=='Y'){	
				$vrc = "SELECT id,effective_to_date,transaction_number  FROM `vendor_rate_cards` WHERE effective_to_date >= '".$formdate."' AND effective_to_date <= '".$todate."' AND is_active = 0 AND company_id = '".$get['company_id']."' ";
				$vrcv = $callConfig->getAllRows($vrc);
				foreach ($vrcv as $vrcv) {
					$conrec++;
					$reamainleaveday = ((strtotime($vrcv->effective_to_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
					$result['comman'][] = array('id'=>$vrcv->id,'option'=>'COM_INSERT_VENDOR_RATE_CARDS','effective_to_date'=>$vrcv->effective_to_date,'display_name'=>$vrcv->transaction_number,'remainingdays'=>$reamainleaveday,'msg'=>'Vendor Rate Card'); 
				}
			}
			if($validation_display['vendor_agreements']=='Y'){	
				$vendor_agreement_url= "SELECT id,expiry_date,agreement_name  FROM `vendor_agreements` WHERE expiry_date >= '".$formdate."' AND expiry_date <= '".$todate."' AND is_active = 0 AND company_id = '".$get['company_id']."' ";
				$vendor_agreement_urlv = $callConfig->getAllRows($vendor_agreement_url);
				foreach ($vendor_agreement_urlv as $vendor_agreement_urlv) {
					$conrec++;
					$reamainleaveday = ((strtotime($vendor_agreement_urlv->expiry_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
					$result['comman'][] = array('id'=>$vendor_agreement_urlv->id,'option'=>'COM_INSERT_VENDORS','expiry_date'=>$vendor_agreement_urlv->expiry_date,'display_name'=>$vendor_agreement_urlv->agreement_name,'remainingdays'=>$reamainleaveday,'msg'=>'Vendor Agreement'); 
				}
			}

		}	
		if($validation_display['emp_vpn']=='Y'){
			$vpn = "SELECT ev.id,ev.expiry_date,ev.emp_id,emp.display_name  FROM `emp_vpn` ev left join employees emp on ev.emp_id = emp.id WHERE ev.expiry_date >= '".$formdate."' AND ev.expiry_date <= '".$todate."' AND ev.is_active = 0 AND ev.company_id = '".$get['company_id']."' ".$conditionoth;
			$vpnv = $callConfig->getAllRows($vpn);
			foreach ($vpnv as $vpnv) {
				$conrec++;
				$reamainleaveday = ((strtotime($vpnv->expiry_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;

				$result['emp'][] = array('id'=>$vpnv->id,'option'=>'EMP_VPN','effective_to_date'=>$vpnv->expiry_date,'emp_id'=>$vpnv->emp_id,'remainingdays'=>$reamainleaveday,'display_name'=>$vpnv->display_name,'msg'=>'VPN'); 
			}
		}
		if($validation_display['emp_visa']=='Y'){	
			$visa = "SELECT ev.id,ev.emp_id,ev.visa_expiry_date,emp.display_name FROM `emp_visa` ev left join employees emp on ev.emp_id = emp.id  WHERE ev.visa_expiry_date >= '".$formdate."' AND ev.visa_expiry_date <= '".$todate."'  AND ev.is_active = 0 AND ev.company_id = '".$get['company_id']."' ".$conditionoth;
			$visav = $callConfig->getAllRows($visa);
			foreach ($visav as $visav) {
				$conrec++;
				$reamainleaveday = ((strtotime($visav->visa_expiry_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;	
				$result['emp'][]  = array('id'=>$visav->id,'option'=>'COM_INSERT_EMP_VISA','effective_to_date'=>$visav->visa_expiry_date,'emp_id'=>$visav->emp_id,'remainingdays'=>$reamainleaveday,'display_name'=>$visav->display_name,'msg'=>'Visa'); 
			}
		}
		if($validation_display['emp_assets']=='Y'){	
			$emp_assetssql = "SELECT emp_a.id,emp_a.end_date,asset.asset_code,emp.id as emp_id,emp.display_name FROM `emp_assets` emp_a, assets asset, employees emp WHERE emp_a.company_id = '".$get['company_id']."' AND emp_a.is_active = 0 AND emp_a.asset_id = asset.id and emp_a.emp_id = emp.id AND emp_a.is_active = emp.is_active AND emp_a.is_active = asset.is_active AND  emp_a.end_date >= '".$formdate."' AND emp_a.end_date <= '".$todate."' ".$conditionoth;
			$emp_assetres = $callConfig->getAllRows($emp_assetssql);
			foreach ($emp_assetres as $emp_assetresult) {
				$conrec++;
				$reamainleaveday = ((strtotime($emp_assetresult->end_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;	
				$result['emp'][]  = array('id'=>$emp_assetresult->id,'option'=>'COM_LIST_EMP_ASSETS','effective_to_date'=>$emp_assetresult->end_date,'emp_id'=>$emp_assetresult->emp_id,'remainingdays'=>$reamainleaveday,'display_name'=>$emp_assetresult->display_name,'msg'=>'Assets'); 
			}
		}
		if($validation_display['passport']=='Y'){	
			$passporturl = "SELECT id,display_name,passport_expiry_date FROM employees WHERE passport_expiry_date >= '".$formdate."' AND passport_expiry_date <='".$todate."'  AND is_active = 0 AND company_id = '".$get['company_id']."'".$conditionemp;
			$passporturlv = $callConfig->getAllRows($passporturl);
			foreach ($passporturlv as $passporturlv) {
				$conrec++;
				$reamainleaveday = ((strtotime($passporturlv->passport_expiry_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
				$result['emp'][]  = array('id'=>$passporturlv->id,'option'=>'','effective_to_date'=>$passporturlv->passport_expiry_date,'emp_id'=>$passporturlv->id,'display_name'=>$passporturlv->display_name,'remainingdays'=>$reamainleaveday,'msg'=>'Passport'); 
			}
		}
		if($validation_display['driving_licence']=='Y'){		
			$drivinglicens = "SELECT id,display_name,driving_licence_expiry_date FROM employees WHERE driving_licence_expiry_date >= '".$formdate."' AND driving_licence_expiry_date <='".$todate."'  AND is_active = 0 AND company_id = '".$get['company_id']."'".$conditionemp;
			$drivinglicensv = $callConfig->getAllRows($drivinglicens);
			foreach ($drivinglicensv as $drivinglicensv) {
				$conrec++;
				$reamainleaveday = ((strtotime($drivinglicensv->driving_licence_expiry_date)-strtotime(date("d-m-Y"))) / (60 * 60 * 24))+1;
				$result['emp'][]  = array('id'=>$drivinglicensv->id,'option'=>'','effective_to_date'=>$drivinglicensv->driving_licence_expiry_date,'emp_id'=>$drivinglicensv->id,'display_name'=>$drivinglicensv->display_name,'remainingdays'=>$reamainleaveday,'msg'=>'Driving License'); 
			}
		}	
		$result['cnt'] = $conrec;
		return $result;
	}
	function updateapprovallogprojecttimesheets($post){
		global $callConfig;
		//print_r($post); exit;
		$projecttssql = "SELECT current_status,created_by FROM project_timesheets WHERE is_active = 0 AND company_id = '".$post['company_id']."' AND id='".$post['hid']."'";
		$projecttsres = $callConfig->getRow($projecttssql);
		$user_id = $projecttsres->created_by;
		$userdetailsurl = "SELECT id,reports_to,approval_manager FROM application_users WHERE is_active = 0 AND company_id = '".$post['company_id']."' AND id='".$projecttsres->created_by."'";
		$old_status = $projecttsres->current_status;
		$new_status = $post['status'];
		//$userdetailsurl = "SELECT id,reports_to,approval_manager FROM application_users WHERE is_active = 0 AND company_id = '".$post['company_id']."' AND id='".$post['user_id']."'";
		$userdetails = $callConfig->getRow($userdetailsurl);
		$reporting_manager_id = $userdetails->approval_manager;
		$approval_manager_id = $userdetails->reports_to;
			if($post['status'] == '8'){
				$userid = $userdetails->approval_manager;
				
				$manager_id = $userdetails->approval_manager;
			}else{
				$userid = $userdetails->reports_to;
				
				$manager_id = $userdetails->reports_to;
			}
		$updatequeryn = "UPDATE project_timesheets SET current_status=".$post['status']." WHERE  id =".$post['hid']; 
         $detailsn = $callConfig->executeQuery($updatequeryn);

		$appv_fieldnames=array('company_id'=>$post['company_id'],'transaction_table'=>$post['table'],'transaction_id'=>$post['hid'],'user_id'=>$userid,'request_date'=>date("Y-m-d H:i:s"),'send_back_flag' => '0');
		$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);

		$update_array_log = array('response_type '=>$post['status'],'response_date'=>date('Y-m-d H:i:s'));
		$where = "transaction_table = '".$post['table']."' AND transaction_id = '".$post['hid']."' AND user_id = '".$post['user_id']."'";
		$res=$callConfig->updateRecord(TBL_TRANSACTION_APPROVAL_LOGS,$update_array_log,"","",$where);

		$updateprojecttimesheet = $this->updateprojecttimesheets($post,$post);

		/*$upadate_array = array('updated_by'=>$post['user_id'],'updated_date'=>date("Y-m-d H:i:s"),'current_status'=>$post['status']);
		$inserttasks = $callConfig->updateRecord('project_timesheets',$upadate_array,'id',$post['hid']);*/
		$ndcndfc['approval_id'] =  $approval_id;
		$ndcndfc['task'] =  $inserttasks;
		//$msg = $post['table']." - ".$post['hid'];

		$notdataa = array('company_id'=>$post['company_id'], 'id'=>$post['hid'],'notification_message'=>$msg, 'id'=>$post['hid'],'application_table_name'=>$post['table'], 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>"COM_INSERT_PROJECT_TIMESHEETS",'action_label'=>'', 'transaction_type'=>$post['table'], 'transaction_number'=>$post['hid'], 'transaction_date'=>date('Y-m-d'),'status'=>$post['status']);

		$usermangerquery = "SELECT id,display_name,user_name,user_email FROM application_users WHERE is_active = 0 AND company_id = '".$post['company_id']."' AND id IN (".$user_id.",".$reporting_manager_id.",".$approval_manager_id.")";
		$usermangerdata = $callConfig->getAllRows($usermangerquery);
		foreach($usermangerdata as $usermangerdetail){
			$allthreeuser[$usermangerdetail->id] = $usermangerdetail->display_name;
			$allthreeuseremail[$usermangerdetail->id] = $usermangerdetail->user_email;
		}
		
		$configquery = "SELECT date_format,date_separator FROM config WHERE company_id=".$get['company_id']." is_active=0";
		$config_data = $callConfig->getRow($configquery);
		$date_latcount = count($updateprojecttimesheet)-1;
		$from_date = $this->dateformates($updateprojecttimesheet['0'],$config_data->date_format,$config_data->date_separator);
		$to_date = $this->dateformates($updateprojecttimesheet[$date_latcount],$config_data->date_format,$config_data->date_separator);
		
		$mail_data = array('user_name'=>$allthreeuser[$user_id],'from_date'=>$from_date,'to_date'=>$to_date,'reporting_manager_name'=>$allthreeuser[$reporting_manager_id],'approval_manager_name'=>$allthreeuser[$approval_manager_id]);

		if($post['status'] == '8' && $old_status == '0'){
			$mail_data['manager_name'] = $allthreeuser[$reporting_manager_id];
			$mail_data['email_content_id'] = 3;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$user_id]);
			$email_array['sender'] = $allthreeuseremail[$reporting_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($user_id);
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date;
			$this->tosendnotifiations($notdataa);
			$mail_data['email_content_id'] = 4;
			$email_array = $this->emailtemplate($mail_data);
			$email_array['sender'] = $allthreeuseremail[$reporting_manager_id];
			$email_array['revicer'] = array($allthreeuseremail[$reporting_manager_id]);
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($reporting_manager_id);
			$notdataa['type'] = "ACTION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date." by ".$allthreeuser[$user_id];
			$this->tosendnotifiations($notdataa);
		}else if($post['status'] == '10' && $old_status == '0'){
			$mail_data['manager_name'] = $allthreeuser[$reporting_manager_id];
			$mail_data['email_content_id'] = 3;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$user_id]);
			$email_array['sender'] = $allthreeuseremail[$approval_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($user_id);
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date;
			$this->tosendnotifiations($notdataa);
			$mail_data['email_content_id'] = 5;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$approval_manager_id]);
			$email_array['sender'] = $allthreeuseremail[$approval_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($approval_manager_id);
			$notdataa['type'] = "ACTION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date." by ".$allthreeuser[$user_id];
			$this->tosendnotifiations($notdataa);
		}else if($post['status'] == '10' && $old_status == '8'){
			$mail_data['manager_name'] = $allthreeuser[$reporting_manager_id];
			$mail_data['email_content_id'] = 6;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$user_id]);
			$email_array['sender'] = $allthreeuseremail[$approval_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($user_id);
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date;
			$this->tosendnotifiations($notdataa);
			$mail_data['email_content_id'] = 7;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$reporting_manager_id]);
			$email_array['sender'] = $allthreeuseremail[$approval_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($reporting_manager_id);
			$notdataa['type'] = "INFORMATION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date." by ".$allthreeuser[$user_id];
			$this->tosendnotifiations($notdataa);
			$mail_data['email_content_id'] = 8;
			$email_array = $this->emailtemplate($mail_data);		
			$email_array['revicer'] = array($allthreeuseremail[$approval_manager_id]);
			$email_array['sender'] = $allthreeuseremail[$approval_manager_id];
			$lenn = $this->sendEmailslist($email_array);
			$notdataa['notification_users'] = array($approval_manager_id);
			$notdataa['type'] = "ACTION";
			$notdataa['notification_message'] = "Timesheets submited for approval ".$from_date."-".$to_date." by ".$allthreeuser[$user_id];

		}

		return $ndcndfc;
		//return $updateprojecttimesheet;
	}
	function yearlyprojecttimesheetreports($data){
		global $callConfig;
		// return $data;
		$last_date = date('Y-m-t');
		$frist_date = date('Y-m-01');
		$fl_date = date('Y-m-01', strtotime("-13 months"));
	 	$returnarray = array();
		$filname = $data['reportfieldname'];
		$filempname = $data['reportempfieldname'];
		
		$query ="SELECT ".$filempname.",  dept_name, 
		  sum(m12) as s12 , 
		  sum(m11) as s11 , 
		  sum(m10) as s10 , 
		  sum(m9) as s9 , 
		  sum(m8) as s8 , 
		  sum(m7) as s7 , 
		  sum(m6) as s6 , 
		  sum(m5) as s5 , 
		  sum(m4) as s4 , 
		  sum(m3) as s3 , 
		  sum(m2) as s2 , 
		  sum(m1) as s1
		FROM 
		  (
		    SELECT 
		      emp.".$filempname.", 
		      DATE_FORMAT(
		        ('2022-07-31' - INTERVAL 12 MONTH), 
		        '%Y-%m'
		      ), 
		      DATE_FORMAT(pt.timesheet_date, '%Y-%m'), 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 11 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M12, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 10 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M11, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 9 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M10, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 8 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M9, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 7 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M8, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 6 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M7, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 5 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M6, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 4 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M5, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 3 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M4, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 2 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M3, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 1 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M2, 
		      CASE WHEN DATE_FORMAT(pt.timesheet_date, '%Y-%m') = DATE_FORMAT(
		        ('".$last_date."' - INTERVAL 0 MONTH), 
		        '%Y-%m'
		      ) THEN sum(pt.vrc_billable_amount) END M1, 
		      IFNULL(
		        (
		          SELECT 
		            dept.".$filname." 
		          FROM 
		            ".$data['reporttable']." dept 
		          WHERE 
		            dept.company_id = emp.company_id 
		            AND dept.id = emp.".$filempname."
		        ), 
		        'Uassigned'
		      ) dept_name, 
		      DATE_FORMAT(pt.timesheet_date, '%Y-%m') monyr, 
		      sum(pt.bill_amount) as po_billable_amount, 
		      sum(pt.vrc_billable_amount) as vrc_billable_amount 
		    FROM 
		      employees emp, 
		      project_timesheets pt 
		    WHERE 
		      emp.company_id = pt.company_id 
		      AND emp.id = pt.emp_id
		      AND emp.billable != 3

		      AND	(
				CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$last_date."') 
					THEN 1
				WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
					THEN 1
				ELSE	2
				END
			)=1 
		      AND pt.timesheet_date >= '".$fl_date."' 
		      AND pt.timesheet_date <= '".$last_date."'
		      AND pt.is_active = 0 
		    GROUP BY
		     emp.company_id, 
		      emp.".$filempname.", 
		      pt.timesheet_date
		  ) a 
		GROUP BY 
		  ".$filempname.", 
		  dept_name
		ORDER by  2";
			// echo $query;
		$return = $callConfig->getAllRows($query);
		// echo "<pre>"; print_r($return);
		
		foreach($return as $res){
			$returnarray['headlist'][$res->$filempname ] = $res->dept_name;
			for($i=0;$i<12;$i++){
				$var = "s".($i+1);
				//echo $i."/".date('M Y',strtotime('-'.$i.' month',strtotime($frist_date)))."/<br>";
			  $returnarray['months'][date("Ym",strtotime('-'.$i.' month',strtotime($frist_date)))] = date('M Y',strtotime('-'.$i.' month',strtotime($frist_date)));
			 //	$valda =  date("Y-m-d", strtotime($from . ' + ' . $i . 'day')); 
			 // $returnarray['list'][$res->$filempname ][date("Ym",strtotime('-'.$i.' month',strtotime($frist_date)))]['po_billable_amount'] = "";
			  $returnarray['list'][$res->$filempname ][date("Ym",strtotime('-'.$i.' month',strtotime($frist_date)))]['po_billable_amount']=$res->$var;
			 $returnarray['list'][$res->$filempname ][date("Ym",strtotime('-'.$i.' month',strtotime($frist_date)))]['vrc_billable_amount']=$res->$var;
			}
		}
		return $returnarray;
	}
	function yearlyprojecttimesheetreportsold($data){
		global $callConfig;
	 	$returnarray = array();
		$filname = $data['reportfieldname'];
		$filempname = $data['reportempfieldname'];
		$getvendor="SELECT id,".$filname." FROM ".$data['reporttable']." WHERE company_id = '".$data['company_id']."' AND is_active = 0";
		$getvendorlist = $callConfig->getAllRows($getvendor);
		foreach($getvendorlist as $getvendorlists){
			$returnarray['headlist'][$getvendorlists->id] = $getvendorlists->$filname; 
			$months = strtotime("-11 month", strtotime($data['fromdate']));
			$end = strtotime($data['fromdate']);
			while($months <= $end)
			{
				$returnarray['months'][date('Ym', $months)] = date('M Y', $months);
			    $ym = explode("-", date('Y-m', $months));
				$year = $ym[0];
				$month = $ym[1];
				$query="SELECT sum(pt.bill_amount) as po_billable_amount,sum(pt.vrc_billable_amount) as vrc_billable_amount FROM `employees` emp, project_timesheets pt WHERE emp.id = pt.emp_id AND emp.is_active = pt.is_active AND emp.company_id = pt.company_id AND emp.".$filempname." = '".$getvendorlists->id."' AND emp.is_active = 0 AND emp.company_id = '".$data['company_id']."' AND MONTH(pt.timesheet_date) = '".$month."' AND YEAR(pt.timesheet_date) = '".$year."'";
			    $return = $callConfig->getRow($query);
			    $returnarray['list'][$getvendorlists->id][date('Ym', $months)] = $return;
			    $months = strtotime("+1 month", $months);
			}
		}
		return $returnarray;
	}
	function getTimesheetSubmitPending($get){
		global $callConfig;
		$returnarray['timesheetsmessagecode']= '0';
		if($get['hrtype'] == '1'){
		$sqlmanager = "SELECT COUNT(id) as ecnt_mp_id, count_emp FROM ( SELECT id,( SELECT COUNT(id) AS cnt 	FROM project_timesheets WHERE emp_id = emp.id AND company_id = '".$get['company_id']."' AND `timesheet_date` >= '".$get['startingdate']."' AND `timesheet_date` <= '".$get['endingdate']."' AND is_active = 0 AND current_status IN('10','101','102','103','104','199') ) count_emp FROM  `employees` emp WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND emp.user_id IN( SELECT id FROM application_users au WHERE au.reports_to = '".$get['user_id']."' ) GROUP BY id) a WHERE count_emp = '0'";
				$slwres = $callConfig->getRow($sqlmanager);
				if($slwres->ecnt_mp_id > 0){ 
					$returnarray['timesheetsmessagecode'] = '9032';	
					$returnarray['cnt_employee'] = $slwres->ecnt_mp_id;
					$returnarray['option'] = "COM_BULK_HR_PROJECT_TIMESHEET";			
				}
			}else{	
		$query = "Select current_status,MAX(id) as id from project_timesheets  WHERE company_id = '".$get['company_id']."' AND emp_id = ".$get['emp_id']." AND `timesheet_date` >= '".$get['startingdate']."' AND `timesheet_date` <= '".$get['endingdate']."'  AND is_active = 0 GROUP by current_status";
	    	$resurts = $callConfig->getAllRows($query);
			foreach($resurts as $resurt){
				$resarray['currentstaus'][] = $resurt->current_status;
				$resarray['id'][] = $resurt->id; 
			}
			$returnarray['option'] = "COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=".date('d-m-Y',strtotime($get['endingdate']));
			$returnarray['date'] = $get['endingdate'];
			$returnarray['type'] = "timesheet";
			
			if(in_array("0", $resarray['currentstaus']) || in_array("20", $resarray['currentstaus'])){
				$returnarray['timesheetsmessagecode'] = '9031';
			}else{
				$returnarray['timesheetsmessagecode'] = '9030';	
			}
		}	
		return $returnarray;
	}
	function getTimesheetsHolidaysLeaveOff($data){
			global $callConfig;
			$returnarray = array();
			$start = $data['startingdate'];
			$end = $data['endingdate'];
			$empsql = "SELECT id,display_name,billable,location_id FROM employees WHERE is_active=0 and company_id='".$data['company_id']."' and id=".$data['emp_id'];
			$empres = $callConfig->getRow($empsql);
			$returnarray['empdetails'] = array('id'=>$empres->id,'display_name'=>$empres->display_name,'billable'=>$empres->billable);

			$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$data['company_id']." AND location_id = '".$empres->location_id."' AND holiday_date >= '".$start."' AND holiday_date <= '".$end."'";
			$holidaysdata = $callConfig->getAllRows($holidays); //holidays
			foreach ($holidaysdata as $hkey=>$hvalue) {
				$holidaysdatas[] = $hvalue->holiday_date;
			}
			$returnarray['holidaydetails'] = $holidaysdatas;

			$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 and company_id=".$data['company_id'];
			$atsdata = $callConfig->getRow($attstc);
			$workingdays = json_decode($atsdata->working_days,1);
			$returnarray['workingdaysdetails'] = $workingdays;
			$returnarray['min_workig_hurse'] = $atsdata->minimum_hours_required;

			$workordersql = "SELECT ewo.work_order_id,vrci.allow_overtime FROM `emp_work_order` ewo left Join vrc_items vrci ON ewo.vrc_item_id = vrci.id WHERE ewo.work_order_detail_id !='' AND ewo.work_order_id !='' AND ewo.vrc_id !='' AND ewo.vrc_item_id !='' AND ewo.is_active = 0 AND ewo.emp_id='".$data['emp_id']."' AND ewo.company_id='".$data['company_id']."'";
			$workorderlist = $callConfig->getAllRows($workordersql);
			foreach ($workorderlist as $workorderlists){
				$workorderslist[$workorderlists->work_order_id] = $workorderlists->allow_overtime;
			}
			$returnarray['work_order_list'] =$workorderslist;

		$leavessql = "select employee_id,leave_from,leave_to,leave_type_id,datelist 
		from employee_leaves, 
		(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) datelist from
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) as v
		where datelist>=leave_from and datelist <=leave_to AND employee_id = '".$data['emp_id']."' AND current_status = '199' AND ((MONTH(leave_from) = '".date('m',strtotime($start))."' AND YEAR(leave_from) = '".date('Y',strtotime($start))."') OR (MONTH(leave_to) = '".date('m',strtotime($end))."' AND YEAR(leave_to) = '".date('Y',strtotime($end))."'))";

		 	$leavesdetails = $callConfig->getAllRows($leavessql); // working //absent
		 	foreach ($leavesdetails as $leavesdetail) {
		 		$leavesdetailsarray[] = $leavesdetail->datelist;
		 	}
			$returnarray['leaves'] =$leavesdetailsarray;
			return $returnarray;
	}
	function getapprovalmanagers($data){
		global $callConfig;
		$sql = "SELECT id FROM `application_users` where approval_manager = '".$data['user_id']."'";
		$data = $callConfig->getAllRows($sql);
		foreach ($data as $dataid) {
		$userid[] = $dataid->id;
		}
		$str_by_emp_ids = implode(",", $userid);
		$query23 = "SELECT id,`department_id`,`designation_id`,`location_id`,`display_name`,`email_id`,`phone`,`image`,`user_id`,billable,on_off_site,work_from_home FROM `employees` WHERE is_active=0 AND user_id IN (".$str_by_emp_ids.")";
		$data23 = $callConfig->getAllRows($query23);
		return $data23;
	}
	function udateleaveforvms(){
		global $callConfig;	
		$tables = array('multi_table_import_data','multi_table_import_data_bangalore','multi_table_import_data_chennai','multi_table_import_data_hydrabad');
		foreach($tables as $table){
			$query = "SELECT emp_id , sum(leaves) sl , month FROM ".$table." GROUP BY emp_id ,month HAVING sl >0";
			$empdatas = $callConfig->getAllRows($query);
			//echo "<pre>"; print_r($empdatas);
			foreach($empdatas as $empdata){
				if($empdata->month=='jan'){	$ldate = "2022-01";	}
				if($empdata->month=='feb'){	$ldate = "2022-02";	}
				if($empdata->month=='mar'){	$ldate = "2022-03";	}
				$eequery = "SELECT id  FROM employees WHERE emp_code='".$empdata->emp_id."'";
				//echo $eequery;
				$eeee = $callConfig->getRow($eequery);
				//print_r($eeee);	
				$pquery = "SELECT * FROM `project_timesheets` WHERE emp_id =".$eeee->id." AND timesheet_date LIKE '%".$ldate."%' AND billable_hrs !=0 ORDER BY timesheet_date LIMIT 0,".$empdata->sl; 
				$prdatas = $callConfig->getAllRows($pquery);
				$earray = array();
				foreach($prdatas as $prdata){
					$earray[]= $prdata->id;
				}
				$eids = implode(",",$earray);
			//	$upquery = "UPDATE project_timesheets SET bill_amount=0.00, vrc_billable_amount= 0.00, billable_hrs=0.00 WHERE id IN (".$eids.")";
			//	echo $upquery."<br>";
				$aa[] = $callConfig->executeQuery($upquery);
			}
		}
		return $aa;
	    }
	    function updateesignconsumedata($get){
		global $callConfig;
		$query = "SELECT * FROM esgin_budget_approvals WHERE id=".$get['trn_id'];
		$da = $callConfig->getRow($query);
		$query = "SELECT * FROM eba_vendors_allocations WHERE transaction_id=".$get['trn_id'];
		$datas = $callConfig->getAllRows($query);
		foreach($datas as $data){
			$fieldnames = array('company_id'=>$get['company_id'], 'vendor_id'=>$data->item_id, 'esign_id'=>$data->transaction_id, 'esign_detail_id'=>$data->id, 'esign_ref_no'=>$da->transaction_number, 'Allocated'=>$data->total_amount, 'consumed'=>'0', 'available'=>$data->total_amount, 'effective_from_date'=>$da->effective_from_date, 'effective_to_date'=>$da->effective_to_date, 'is_active'=>'0');
			$res=$callConfig->insertRecord('esign_budget_consumed',$fieldnames);
		}
		return $res;
	}
	function getWeecklyTimeSheetSummary($get){
		   global $callConfig;
		   $company_id = $get['company_id'];
		   $startdate = $get['year']."-".$get['month']."-01";
		   $enddate = date("Y-m-t",strtotime($startdate));
		   $reproting_manager = "";
		   if($get['reproting_manager'] != '0'){
			$reproting_manager = "  AND au.reports_to='".$get['reproting_manager']."'";
		   }
		   $query = "SELECT emp_id, display_name, reports_to, approval_manager,SUM(totalhrs) total_hrs, SUM(w1) 1week, SUM(w2) 2week, SUM(w3) 3week, SUM(w4) 4week, SUM(w5) 5week
		FROM(
		SELECT emp_id, display_name, reports_to, approval_manager, timesheet_week, week_of_month, sum(billable_hrs) totalhrs,
		CASE WHEN week_of_month = 1
		THEN sum(billable_hrs)
		END W1,
		CASE WHEN week_of_month = 2
		THEN sum(billable_hrs)
		END W2,
		CASE WHEN week_of_month = 3
		THEN sum(billable_hrs)
		END W3,
		CASE WHEN week_of_month = 4
		THEN sum(billable_hrs)
		END W4,
		CASE WHEN week_of_month = 5
		THEN sum(billable_hrs)
		END W5
		FROM (
		SELECT pt.emp_id, emp.display_name, au.reports_to, au.approval_manager, pt.timesheet_date, pt.billable_hrs,
		CONCAT(DATE_FORMAT(MIN(DATE((pt.timesheet_date)) - INTERVAL (DATE_FORMAT((pt.timesheet_date), '%w') + 6) % 7 DAY), '%M %d'),
		' - ',
		DATE_FORMAT(MIN(DATE((pt.timesheet_date)) - INTERVAL (DATE_FORMAT((pt.timesheet_date), '%w') + 6) % 7 DAY) + INTERVAL 6 DAY, '%M %d')) AS timesheet_week, FLOOR((DAYOFMONTH(pt.timesheet_date) - 1) / 7) + 1 week_of_month
		FROM project_timesheets pt, employees emp, application_users au
		WHERE emp.company_id = '".$company_id."'
		AND emp.company_id = pt.company_id
		AND emp.id = pt.emp_id
		AND emp.company_id = au.company_id
		AND emp.user_id = au.id
		AND emp.is_active = au.is_active
		AND emp.is_active = pt.is_active
		AND emp.billable != 3
		AND joining_date <= '".$enddate."'
		AND	(
				CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') 
					THEN 1
				WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
					THEN 1
				ELSE	2
				END
			)=1 
		AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL)
		AND pt.current_status = '".ST_APPROVED."'
		AND pt.timesheet_date <= '".$enddate."'
		AND pt.timesheet_date >= '".$startdate."'".$reproting_manager."
		group by pt.emp_id, emp.display_name, au.reports_to, au.approval_manager,pt.timesheet_date, pt.billable_hrs
		)a
		group by emp_id, display_name, reports_to, approval_manager, timesheet_week, week_of_month
		) b
		GROUP BY emp_id, display_name, reports_to, approval_manager";
		$result = $callConfig->getAllRows($query);
		foreach($result as $row){
			$retrunarray[$row->reports_to][$row->approval_manager][$row->emp_id] = $row;
		}
		 return $retrunarray; 
	}
	function getWeecklyBulkApprovalTimesheet($get){
			global $callConfig;
		   $company_id = $get['company_id'];
		   $approval_manager = $get['user_id'];
		   $startdate = $get['startingdate'];
		   $enddate = $get['endingdate'];
		   $reproting_manager = "";
		   if($get['reproting_manager'] != '0'){
			$reproting_manager = "  AND au.reports_to='".$get['reproting_manager']."'";
		   }
		   $query = "SELECT emp_id, emp_code, image, display_name, reports_to, approval_manager, min(timesheet_id) as min_timesheet_id,  max(timesheet_id) as max_timesheet_id, current_status, (SUM(d1)+SUM(d2)+SUM(d3)+SUM(d4)+SUM(d5)+SUM(d6)+SUM(d7))total, SUM(d1) as d1, SUM(d2) as d2, SUM(d3) as d3, SUM(d4) as d4, SUM(d5) as d5, SUM(d6) as d6, SUM(d7) as d7
			FROM
			(
			SELECT pt.emp_id,emp.emp_code, emp.image, emp.display_name, au.reports_to, au.approval_manager, pt.id as timesheet_id, pt.current_status as current_status,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date)) DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D1,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+1 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D2 ,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+2 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D3,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+3 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D4,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+4 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D5,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+5 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D6,
			CASE WHEN DATE_ADD(pt.timesheet_date, INTERVAL(-WEEKDAY(pt.timesheet_date))+6 DAY) = pt.timesheet_date
			THEN pt.billable_hrs
			END D7
			FROM project_timesheets pt, employees emp, application_users au
			WHERE emp.company_id = pt.company_id
			AND emp.id = pt.emp_id
			AND emp.company_id = au.company_id
			AND emp.user_id = au.id
			AND emp.is_active = au.is_active
			AND emp.is_active = pt.is_active
			AND pt.is_active = 0
			AND emp.billable != 3
			AND emp.joining_date <= '".$enddate."' 
			AND (emp.date_of_leaving >= '".$startdate."' OR emp.date_of_leaving IS NULL)
			AND au.approval_manager = '".$approval_manager."'
			AND pt.company_id = '".$company_id."'
			AND pt.timesheet_date <= '".$enddate."'
			AND pt.timesheet_date >= '".$startdate."'
			)a
			GROUP BY emp_id, emp_code,image, display_name, reports_to, approval_manager, current_status";
			$result = $callConfig->getAllRows($query);
			foreach($result as $row){
				$retrunarray[] = $row->min_timesheet_id;
			}
			$timesheetsyattosubmitsql = "SELECT emp.id,emp.emp_code, emp.image, emp.display_name FROM `employees` emp, application_users au  WHERE emp.user_id = au.id   AND emp.is_active = au.is_active AND emp.company_id = au.company_id AND emp.is_active = 0 AND emp.billable != 3  AND emp.company_id = '".$company_id."' AND au.approval_manager = '".$approval_manager."' AND emp.id NOT IN(SELECT pt.emp_id from project_timesheets pt WHERE pt.is_active = 0 AND pt.company_id = '".$company_id."' AND pt.timesheet_date <= '".$enddate."' AND pt.timesheet_date >= '".$startdate."')";
			$timesheetsyattosubmitres = $callConfig->getAllRows($timesheetsyattosubmitsql);
			foreach($timesheetsyattosubmitres as $row2){
				$retrunyattusubmit[$row2->id] =  $row2;
			}
		//	$retrunarry = array("res"=>$result,"log"=>$retrunlog,'yattosubmit'=>$retrunyattusubmit,'sqlapproval'=>$query,'yattosubmitsql'=>$timesheetsyattosubmitsql);
			$retrunarry = array("res"=>$result,'yattosubmit'=>$retrunyattusubmit,'sqlapproval'=>$query,'yattosubmitsql'=>$timesheetsyattosubmitsql);
			return $retrunarry;
	}      
	function changestatusweeklybulkapproval($get){
		global $callConfig;
		$data = json_decode(urldecode($get['data']),1);
		$empid_arr = explode(",",$data['emp_id']);
		$timesheets_arr = explode(",",$data['timesheetid']);
		$emp_time_array = array_combine($empid_arr,$timesheets_arr);
		$uquey = "UPDATE transaction_approval_logs SET response_type = '".$data['status']."', response_date = '".date('Y-m-d')."'  WHERE transaction_id IN(".$data['timesheetid'].") AND transaction_table = '".$data['table']."'";
		$dataa = $callConfig->executeQuery($uquey);
		$query = "UPDATE project_timesheets pt SET pt.current_status='".$data['status']."' WHERE pt.emp_id IN(".$data['emp_id'].") AND pt.timesheet_date >= '".$data['weekstartdate']."' AND pt.timesheet_date <= '".$data['weekenddate']."' AND pt.company_id = '".$get['company_id']."'";
		$upp = $callConfig->executeQuery($query);
		$userdetailsurl = "SELECT au.reports_to, emp.user_id, emp.id, emp.display_name FROM application_users au, employees emp WHERE emp.is_active = au.is_active AND emp.company_id = au.company_id  AND emp.user_id = au.id AND emp.id IN(".$data['emp_id'].") AND emp.is_active = 0 AND emp.company_id = '".$get['company_id']."'";
		$userdetails = $callConfig->getAllRows($userdetailsurl);
		foreach((array)$userdetails as $udata){
			$user_ids[] = $udata->user_id;
			$sendbackarray[$udata->id] = $udata->user_id;
			$report_to[] = $udata->report;
			$report_tosendnotification[$udata->id] = $udata->reports_to;
			$report_toempdisplay_name[$udata->id] = $udata->display_name;
			
		}
		if($data['status']==ST_SEND_BACK){
			
			foreach((array)$sendbackarray as $empidkey=>$useridd){
				$appv_fieldnames=array('company_id'=>$data['company_id'],'transaction_table'=>$data['table'],'transaction_id'=>$emp_time_array[$empidkey],'user_id'=>$useridd,'request_date'=>date("Y-m-d H:i:s"),'send_back_flag' => '0','response_type'=>'0');
				$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
			}
			$option = "COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=".date('Y',strtotime($data['weekstartdate']))."&PAY_MONTH=".date('m',strtotime($data['weekstartdate']));
			$msg = date('d M Y',strtotime($data['weekstartdate'])).' Your Time Sheet is Send Back by Reporting';
			$notdataa = array('company_id'=>$data['company_id'], 'notification_message'=>$msg, 'transaction_number'=>'', 'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$ss = $this->tosendnotifiations($notdataa);
		
		}else{
			//$msg = date('d M Y',strtotime($data['weekstartdate'])).' Your Time Sheet is Approved by Reporting';
			foreach((array)$report_tosendnotification as $empidkey=>$report_to){
				$appv_fieldnames=array('company_id'=>$data['company_id'],'transaction_table'=>$data['table'],'transaction_id'=>$emp_time_array[$empidkey],'user_id'=>$report_to,'request_date'=>date("Y-m-d H:i:s"),'send_back_flag' => '0','response_type'=>'0');
				$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
				$msg = $report_toempdisplay_name[$empidkey]." Project Timesheets ".date('d M',strtotime($data['weekstartdate']))." To ".date('d M',strtotime($data['weekenddate']))." : Submit For Approval";
				$user_ids  = array($report_to);
				$option = "COM_INSERT_PROJECT_TIMESHEETS";
				$notdataa = array('company_id'=>$data['company_id'],'notification_message'=>$msg, 'id'=>$emp_time_array[$empidkey],'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
				$sendnotification = $this->tosendnotifiations($notdataa);
			}
		}

		return $notdataa;
	}
	function updateprojecttimesheetsblukforhrweekly($get){
		global $callConfig; 
		$data = json_decode(urldecode($get['data']),1);
		$empid_arr = explode(",",$data['emp_id']);
		$timesheets_arr = explode(",",$data['timesheetid']);
		$emp_time_array = array_combine($empid_arr,$timesheets_arr);
		$userdetailsurl = "SELECT au.reports_to, emp.id FROM application_users au, employees emp WHERE emp.is_active = au.is_active AND emp.company_id = au.company_id  AND emp.user_id = au.id AND emp.id IN(".$data['emp_id'].") AND emp.is_active = 0 AND emp.company_id = '".$get['company_id']."'";
		$userdetails = $callConfig->getAllRows($userdetailsurl);
		
		foreach($userdetails as $udata){
			$user_ids[$udata->id] = $udata->reports_to;
		}
		
		foreach($user_ids as $empidkey=>$report_to){
			$appv_fieldnames=array('company_id'=>$data['company_id'],'transaction_table'=>$data['table'],'transaction_id'=>$emp_time_array[$empidkey],'user_id'=>$report_to,'request_date'=>date("Y-m-d H:i:s"),'send_back_flag' => '0','response_type'=>'0');
			$approval_id=$callConfig->insertRecord(TBL_TRANSACTION_APPROVAL_LOGS,$appv_fieldnames);
			
			$msg = "Project Timesheets ".date('d M',strtotime($data['weekstartdate']))." To ".date('d M',strtotime($data['weekenddate']))." : Submit For Approval";
			$user_ids  = array($report_to);
			$option = "COM_INSERT_PROJECT_TIMESHEETS";
			$notdataa = array('company_id'=>$data['company_id'],'notification_message'=>$msg, 'id'=>$emp_time_array[$empidkey],'application_table_name'=>'project_timesheets', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
			$sendnotification = $this->tosendnotifiations($notdataa);
		}
		return 	$approval_id;
  	}
  	/*************VMS single page Start*************************************************************/ 
    function togetvmsemployeedetails($get){
		global $callConfig;
		//$whr = "id=".$get['emp_id']." AND is_active=0";
		if($get['employe_id']!=""){
			$whr = "id=".$get['employe_id']." AND is_active=0";
		}else if($get['emp_code']!=""){
			$whr = "emp_code='".$get['emp_code']."' AND is_active=0";
		}else{
			$whr = "id='' AND is_active=0";
		}
		$equery = $callConfig->selectQuery('employees','*',$whr,'','','');
		$emp = $callConfig->getRow($equery);	
		$return['employee'] = $emp;
		$whr = "id=".$emp->user_id." AND is_active=0";
		$equery = $callConfig->selectQuery('application_users','*',$whr,'','','');
		$return['user'] = $callConfig->getRow($equery);
		$whr = "is_active=0";
		$equery = $callConfig->selectQuery('locations','*',$whr,'','','');
		$return['locations'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('application_roles','*',$whr,'','','');
		$return['application_roles'] = $callConfig->getAllRows($equery);
		$equery = $callConfig->selectQuery('job_titles','id,job_title',$whr,'','','');
		$return['job_titles'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('departments','*',$whr,'','','');
		$return['departments'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('designations','*',$whr,'','','');
		$return['designations'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('cost_centers','*',$whr,'','','');
		$return['cost_centers'] = $callConfig->getAllRows($equery);
		$equery = $callConfig->selectQuery('vendors','*',$whr,'','','');
		$return['vendors'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('esgin_budget_approvals','*',$whr,'','','');
		$return['esign'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('vendor_rate_cards','*',$whr,'','','');
		$return['vrc'] = $callConfig->getAllRows($equery);
		$equery = $callConfig->selectQuery('manpower_contracts','*',$whr,'','','');
		$return['mpc'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('eba_vendors_allocations','*',$whr,'','','');
		$return['esign_items'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('vrc_items','*',$whr,'','','');
		$return['vrc_items'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('mpc_items','*',$whr,'','','');
		$return['mpc_items'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('application_users','id,display_name',$whr,'','','');
		$return['application_users'] = $callConfig->getAllRows($equery);
		$equery = $callConfig->selectQuery('employees','id,display_name',$whr,'','','');
		$return['employee_list'] = $callConfig->getAllRows($equery);
		$whr = "employee_id=".$emp->id." AND is_active=0";
		$equery = $callConfig->selectQuery('employee_buddies','*',$whr,'','','');
		//echo $equery;exit;
		$return['employee_buddies'] = $callConfig->getRow($equery);
		$whr = "emp_id=".$emp->id." AND is_active=0";
		$equery = $callConfig->selectQuery('esgin_employees','*',$whr,'','','');
		$return['esgin_employees'] = $callConfig->getAllRows($equery);	
		$equery = $callConfig->selectQuery('emp_work_order','*',$whr,'','','');
		$return['emp_work_order'] = $callConfig->getAllRows($equery); 
		$return['emp_work_order'] = $callConfig->getAllRows($equery);
		$equery = $callConfig->selectQuery('emp_assets','id,asset_id,allocation_type,start_date,end_date,remarks,current_status',$whr,'','','');
		$return['emp_assets'] = $callConfig->getAllRows($equery); 
		$equery = $callConfig->selectQuery('assets','id,asset_code,asset_description,category_id,serial_no','','','','');
		$listassets = $callConfig->getAllRows($equery); 
		foreach($listassets as $listasset){
			$assets[$listasset->id] = $listasset;
		}
		$return['assets'] = $assets;
		$whr = "company_id=".$get['company_id']." AND is_active=0";
		$equery = $callConfig->selectQuery('milestone_contracts','*',$whr,'','','');
		$return['msc'] = $callConfig->getAllRows($equery);
		$return['msc_query'] = $equery;
		$equery = $callConfig->selectQuery('ms_items','*',$whr,'','','');
		$return['msc_item_query'] = $equery;
		$return['ms_items'] = $callConfig->getAllRows($equery);
		$whr = "company_id=".$get['company_id']." AND emp_id =".$emp->id." AND is_active=0";
		$equery = $callConfig->selectQuery('emp_assign_milestone','*',$whr,'','','');
		$return['emp_stone'] = $callConfig->getAllRows($equery); 
		

		return 	$return;
	}
	function insertupdateemployeebuddies($post,$get){
		global $callConfig;
		$emp_id = $post['employee_id'];
		$query2 = "SELECT * FROM employee_buddies WHERE is_active=0 AND  employee_id=".$emp_id;
		//echo $query1."</br>";
		$datas = $callConfig->getRow($query2);
		//print_r($insert_array);
		if($datas->id!=""){
			$remove_array = array('is_active'=>1,'updated_by'=>$get['user_id']);
			$where = "id = '".$datas->id."'";
			$res=$callConfig->updateRecord("employee_buddies",$remove_array,"","",$where);
		}
		$insert_array = array('company_id'=>$get['company_id'],'hr_buddy'=>$post['hr_buddy'],'employee_id'=>$emp_id,'finance_buddy'=>$post['finance_buddy'],'admin_buddy'=>$post['admin_buddy'],'loc_buddy'=>$post['loc_buddy'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$res = $callConfig->insertRecord('employee_buddies',$insert_array);
		return $res;
	}
	function vrcandpoemployeeassign($post,$get){
		global $callConfig;
		$inarray = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'],'work_order_id'=>0,'work_order_detail_id'=>0,'price'=>$post['company_id'],'effective_from_date'=>'','effective_to_date'=>'','is_active'=>'0','current_status'=>'0','created_date'=>$post['company_id'],'created_by'=>$get['user_id'],'vrc_id'=>0,'vrc_item_id'=>0);
		if($post['type']=='vrc'){
			$where = "vrc_id = ".$post['rid']." AND vrc_item_id = ".$post['item']." AND work_order_id is NULL AND work_order_detail_id is NULL AND is_active = 0";
			$swhr = "vrc_id is NULL AND vrc_item_id is NULL AND is_active = 0";
			$inarray['vrc_id'] = $post['rid'];
			$inarray['vrc_item_id'] = $post['item'];
			$uparray['vrc_id'] = $post['rid'];
			$uparray['vrc_item_id'] = $post['item'];
			
		}
		if($post['type']=='mpc'){
			$where = "vrc_id is NULL AND vrc_item_id is NULL AND work_order_id = ".$post['rid']." AND work_order_detail_id = ".$post['item']." AND is_active = 0";
			$swhr = "work_order_id  is NULL AND work_order_detail_id is NULL AND is_active = 0";
			$inarray['work_order_id'] = $post['rid'];
			$inarray['work_order_detail_id'] = $post['item'];
			$iwhr = "transaction_id = ".$post['rid']." AND id = ".$post['item']." AND is_active = 0";
			$query7 = "SELECT * FROM mpc_items WHERE ".$iwhr;
			$item = $callConfig->getRow($query7);
			$inarray['effective_from_date'] = $item->effective_date ;
			$inarray['effective_to_date'] = $item->expire_date;	
			$inarray['price'] = $item->item_price;	
			$uparray['price'] = $item->item_price;	
			$uparray['effective_from_date'] = $item->effective_date ;
			$uparray['effective_to_date'] = $item->expire_date;	
			$uparray['work_order_id'] = $post['rid'];
			$uparray['work_order_detail_id'] = $post['item'];
		}
		$query2 = "SELECT * FROM emp_work_order WHERE ".$where;
		$datas = $callConfig->getRow($query2);
		if($datas->id!=""){ echo "step 1";
			$update_array = array('is_active'=>1,'updated_by'=>$get['user_id']);
			$where = "id = '".$datas->id."'";
			$res=$callConfig->updateRecord("emp_work_order",$update_array,"","",$where);
		}else{ echo "step 2";
			echo $query3 = "SELECT * FROM emp_work_order WHERE ".$swhr;
		    $datas3 = $callConfig->getRow($query3);
			if($datas3->id!=""){ echo "step 3";
				$where = "id = '".$datas3->id."'";
				$res=$callConfig->updateRecord("emp_work_order",$uparray,"","",$where);
			}else{ echo "step 4";
				$res = $callConfig->insertRecord('emp_work_order',$inarray);
			}
		}
		return $res;
	}
	function insertupdateesgcpopup($post,$get){
		global $callConfig;
		$valid_from = date('Y-m-d', strtotime(str_replace('/', '-', $post['valid_from'])));
		$valid_to =  date('Y-m-d', strtotime(str_replace('/', '-', $post['valid_to'])));
		echo $query2 = "SELECT id FROM esgin_employees WHERE  emp_id =".$post['emp_id']." AND valid_to >'".$valid_from."' AND is_active = 0";
		$datas = $callConfig->getAllRows($query2);
		if(count($datas)>0){
			foreach($datas as $ids){
				$esids[]=$ids->id;
			}
			$uquey = "UPDATE esgin_employees SET valid_to ='".date('Y-m-d',strtotime('-1 days', strtotime($valid_from)))."' WHERE id IN (".implode(",",$esids).")";
			$dataa = $callConfig->executeQuery($uquey);
		}
		$inarray = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'],'esgin_budget_approval_id'=>$post['esg_id'],'eba_vendors_allocation_id'=>$post['esg_item_id'],'valid_from'=>$valid_from,'valid_to'=>$valid_to,'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d h:i:s'),'created_by'=>$get['user_id']);
		$res = $callConfig->insertRecord('esgin_employees',$inarray);
		return $res;
	}
	function insertupdatepovrcpopup($post,$get){
		global $callConfig;
		$valid_from = date('Y-m-d', strtotime(str_replace('/', '-', $post['valid_from'])));
		$valid_to =  date('Y-m-d', strtotime(str_replace('/', '-', $post['valid_to'])));
		$return = array('status'=>'Fail','error'=>'9049');
		$que = "SELECT id,joining_date FROM  employees WHERE emp_id =".$post['emp_id'];
		$empjoin = $callConfig->getRow($que);
		if( strtotime($valid_from) < strtotime($empjoin->joining_date)){
			$return = array('status'=>'Fail','error'=>'9047');
		}else{		
			 $return = array('status'=>'Fail','error'=>'9047');
			$querr = "SELECT count(id) as cu FROM `project_timesheets` WHERE emp_id =".$post['emp_id']." AND `timesheet_date` >= '".$valid_from."' AND `timesheet_date` <= '".$valid_to."' AND is_active = 0";
			$counts = $callConfig->getRow($querr);
			if($counts->cu ==0){	
				$query2 = "SELECT id FROM emp_work_order WHERE  emp_id =".$post['emp_id']." AND effective_to_date >'".$valid_from."' AND is_active = 0";
				$datas = $callConfig->getAllRows($query2);
				if(count($datas)>0){
					foreach($datas as $ids){
						$esids[]=$ids->id;
					}
					$uquey = "UPDATE emp_work_order SET effective_to_date ='".date('Y-m-d',strtotime('-1 days', strtotime($valid_from)))."' WHERE id IN (".implode(",",$esids).")";
					$dataa = $callConfig->executeQuery($uquey);
				}
				$inarray = array('company_id'=>$get['company_id'],'emp_id'=>$post['emp_id'],'vrc_id'=>$post['vrc_id'],'vrc_item_id'=>$post['vrc_item_id'],'work_order_id'=>$post['mpc_id'],'work_order_detail_id'=>$post['mpc_item_id'],'effective_from_date'=>$valid_from,'effective_to_date'=>$valid_to,'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d h:i:s'),'created_by'=>$get['user_id']);
				$return = $callConfig->insertRecord('emp_work_order',$inarray);
				$return = array('status'=>'success','error'=>'3000');
			}	
		}
		return $return;
	}
	function togetesignpolistpopup($post){
		global $callConfig;
		if($post['type']=='vrc'){			
	    	$vquery = "SELECT * FROM vendor_rate_cards WHERE id=".$post['rid']." AND is_active = 0";
	    	$vdata = $callConfig->getRow($vquery);
			$pquery = "SELECT * FROM manpower_contracts WHERE transaction_number='".$vdata->transaction_number."' AND is_active = 0";
			$pdata = $callConfig->getRow($pquery);
			 $viquery = "SELECT * FROM vrc_items WHERE transaction_id=".$post['rid']." AND id = ".$post['item']." AND is_active = 0";
			$vidata = $callConfig->getRow($viquery);
			$miquery = "SELECT * FROM mpc_items WHERE transaction_id='".$pdata->id."'  AND is_active = 0";
			$midata = $callConfig->getAllRows($miquery);
			$jtquery = "SELECT * FROM job_titles WHERE  is_active = 0";
			$jtdata = $callConfig->getAllRows($jtquery);
			foreach($jtdata as $jdata){
				$job[$jdata->id] = $jdata->job_title;
			}
			$return['vrcmain'] = 	$vdata;
			$return['sub_vrc'] = 	$vidata;
			$return['mpcmain'] = 	$pdata;
			$return['sub_mpc'] = 	$midata;
			$return['job'] 	   = 	$job;	
		}
		if($post['type']=='mpc'){			
			$pquery = "SELECT * FROM manpower_contracts WHERE id=".$post['rid']." AND is_active = 0";
			$pdata = $callConfig->getRow($pquery);
	    	$vquery = "SELECT * FROM vendor_rate_cards WHERE id='".$pdata->ref_no."' AND is_active = 0";
	    	$vdata = $callConfig->getRow($vquery);
	    	$miquery = "SELECT * FROM mpc_items WHERE transaction_id=".$post['rid']." AND id = ".$post['item']." AND is_active = 0";
			$midata = $callConfig->getRow($miquery);
			$viquery = "SELECT * FROM vrc_items WHERE transaction_id='".$vdata->id."' AND  is_active = 0";
			$vidata = $callConfig->getAllRows($viquery);
			$jtquery = "SELECT * FROM job_titles WHERE  is_active = 0";
			$jtdata = $callConfig->getAllRows($jtquery);
			foreach($jtdata as $jdata){
				$job[$jdata->id] = $jdata->job_title;
			}
			$return['vrcmain'] = 	$vdata;
			$return['sub_vrc'] = 	$vidata;
			$return['mpcmain'] = 	$pdata;
			$return['sub_mpc'] = 	$midata;
			$return['job'] 	   = 	$job;	
		}
		return $return;
	}
	function getvendorratecardidordetailsid($get){
		global $callConfig; 
		//	print_r($get); exit;
		$condition = "";
		if($get['old_ref_no']!="" AND ($get['vrc_id']=="" || $get['vrc_id']=="0")){
			$condition .= " AND vrc.transaction_number = '".$get['old_ref_no']."'";
		}
		if($get['vrc_id']!="" and $get['vrc_id']!="0"){
			$condition .= " AND vrc.id = '".$get['vrc_id']."'";
		}
		if($get['item_id'] !=""){
			$condition .= " AND vrci.item_id = '".$get['item_id']."'";
		}
		$sql = "SELECT vrc.transaction_number,vrc.id as vrc_id,vrci.id vrc_item_id, vrci.min_exp, vrci.max_exp, vrci.item_id,(select jt.job_title from job_titles jt WHERE jt.id = vrci.item_id AND jt.company_id = vrci.company_id) job_title FROM vendor_rate_cards vrc, vrc_items vrci WHERE vrc.company_id = '".$get['company_id']."' AND vrc.company_id = vrci.company_id AND vrc.id = vrci.transaction_id  AND vrc.is_active = 0 AND vrc.is_active = vrci.is_active ".$condition;
		$res = $callConfig->getAllRows($sql);
		return $res;	
	}
 	function getWeecklyStatusTimeSheetSummary($get){
	   global $callConfig;
	  //print_r($get); exit; 
	  $company_id = $get['company_id'];
	   $startdate = $get['year']."-".$get['month']."-01";
	   $enddate = date("Y-m-t",strtotime($startdate));
	   $reproting_manager = "";
	   if($get['reproting_manager'] != '0'){
		$reproting_manager = "  AND au.reports_to='".$get['reproting_manager']."'";
	   }
	   if($get['admin']=='0'){
		   $reproting_manager = "  AND au.reports_to='".$get['user_id']."'";
	   }
	   $query = "SELECT emp_id, display_name, reports_to, approval_manager, SUM(totalhrs) total_hrs, 
		SUM(0w1) YS_week_1, SUM(8w1) RM_week_1, SUM(10w1) AM_week_1, SUM(102w1) HR_week_1, SUM(104w1) FN_week_1, SUM(199w1) AP_week_1,
		IFNULL(SUM(0w1),0) + IFNULL(SUM(8w1),0) + IFNULL(SUM(10w1),0) + IFNULL(SUM(102w1),0) + IFNULL(SUM(104w1),0) + IFNULL(SUM(199w1),0) TOT_week_1,
		SUM(0w2) YS_week_2, SUM(8w2) RM_week_2, SUM(10w2) AM_week_2, SUM(102w2) HR_week_2, SUM(104w2) FN_week_2, SUM(199w2) AP_week_2,
		IFNULL(SUM(0w2),0) + IFNULL(SUM(8w2),0) + IFNULL(SUM(10w2),0) + IFNULL(SUM(102w2),0) + IFNULL(SUM(104w2),0) + IFNULL(SUM(199w2),0) TOT_week_2,
		SUM(0w3) YS_week_3, SUM(8w3) RM_week_3, SUM(10w3) AM_week_3, SUM(102w3) HR_week_3, SUM(104w3) FN_week_3, SUM(199w3) AP_week_3,
        IFNULL(SUM(0w3),0) + IFNULL(SUM(8w3),0) + IFNULL(SUM(10w3),0) + IFNULL(SUM(102w3),0) + IFNULL(SUM(104w3),0) + IFNULL(SUM(199w3),0) TOT_week_3,
		SUM(0w4) YS_week_4, SUM(8w4) RM_week_4, SUM(10w4) AM_week_4, SUM(102w4) HR_week_4, SUM(104w4) FN_week_4, SUM(199w4) AP_week_4,
        IFNULL(SUM(0w4),0) + IFNULL(SUM(8w4),0) + IFNULL(SUM(10w4),0) + IFNULL(SUM(102w4),0) + IFNULL(SUM(104w4),0) + IFNULL(SUM(199w4),0) TOT_week_4,
		SUM(0w5) YS_week_5, SUM(8w5) RM_week_5, SUM(10w5) AM_week_5, SUM(102w5) HR_week_5, SUM(104w5) FN_week_5, SUM(199w5) AP_week_5,
        IFNULL(SUM(0w5),0) + IFNULL(SUM(8w5),0) + IFNULL(SUM(10w5),0) + IFNULL(SUM(102w5),0) + IFNULL(SUM(104w5),0) + IFNULL(SUM(199w5),0) TOT_week_5
		FROM(
		SELECT emp_id, display_name, reports_to, approval_manager, timesheet_week, week_of_month, sum(billable_hrs) totalhrs,
					CASE WHEN current_status < '".ST_APPROVAL_MANAGER."' AND week_of_month = 1
						THEN sum(billable_hrs)
					END 0W1,
					CASE WHEN current_status = '".ST_APPROVAL_MANAGER."' AND week_of_month = 1
						THEN sum(billable_hrs)
					END 8W1,
					CASE WHEN current_status IN ('".ST_APPROVAL_PENDING."', '".ST_APPROVED1."') AND week_of_month = 1
						THEN sum(billable_hrs)
					END 10W1,
					CASE WHEN current_status IN ('".ST_APPROVED2."', '".ST_APPROVED3."') AND week_of_month = 1
						THEN sum(billable_hrs)
					END 102W1,
					CASE WHEN current_status = '".ST_APPROVED4."' AND week_of_month = 1
						THEN sum(billable_hrs)
					END 104W1,
					CASE WHEN current_status = '".ST_APPROVED."' AND week_of_month = 1
						THEN sum(billable_hrs)
					END 199W1,
					CASE WHEN current_status < '".ST_APPROVAL_MANAGER."' AND week_of_month = 2
						THEN sum(billable_hrs)
					END 0W2,
					CASE WHEN current_status = '".ST_APPROVAL_MANAGER."' AND week_of_month = 2
						THEN sum(billable_hrs)
					END 8W2,
					CASE WHEN current_status IN ('".ST_APPROVAL_PENDING."', '".ST_APPROVED1."')  AND week_of_month = 2
						THEN sum(billable_hrs)
					END 10W2,
					CASE WHEN current_status = '".ST_APPROVED2."' AND week_of_month = 2
						THEN sum(billable_hrs)
					END 102W2,
					CASE WHEN current_status = '".ST_APPROVED4."' AND week_of_month = 2
						THEN sum(billable_hrs)
					END 104W2,
					CASE WHEN current_status = '".ST_APPROVED."' AND week_of_month = 2
						THEN sum(billable_hrs)
					END 199W2,
					CASE WHEN current_status < '".ST_APPROVAL_MANAGER."' AND week_of_month = 3
						THEN sum(billable_hrs)
					END 0W3,
					CASE WHEN current_status = '".ST_APPROVAL_MANAGER."' AND week_of_month = 3
						THEN sum(billable_hrs)
					END 8W3,
					CASE WHEN current_status IN ('".ST_APPROVAL_PENDING."', '".ST_APPROVED1."')  AND week_of_month = 3
						THEN sum(billable_hrs)
					END 10W3,
					CASE WHEN current_status = '".ST_APPROVED2."' AND week_of_month = 3
						THEN sum(billable_hrs)
					END 102W3,
					CASE WHEN current_status = '".ST_APPROVED4."' AND week_of_month = 3
						THEN sum(billable_hrs)
					END 104W3,
					CASE WHEN current_status = '".ST_APPROVED."' AND week_of_month = 3
						THEN sum(billable_hrs)
					END 199W3,
					CASE WHEN current_status < '".ST_APPROVAL_MANAGER."' AND week_of_month = 4
						THEN sum(billable_hrs)
					END 0W4,
					CASE WHEN current_status = '".ST_APPROVAL_MANAGER."' AND week_of_month = 4
						THEN sum(billable_hrs)
					END 8W4,
					CASE WHEN current_status IN ('".ST_APPROVAL_PENDING."', '".ST_APPROVED1."')  AND week_of_month = 4
						THEN sum(billable_hrs)
					END 10W4,
					CASE WHEN current_status = '".ST_APPROVED2."' AND week_of_month = 4
						THEN sum(billable_hrs)
					END 102W4,
					CASE WHEN current_status = '".ST_APPROVED4."' AND week_of_month = 4
						THEN sum(billable_hrs)
					END 104W4,
					CASE WHEN current_status = '".ST_APPROVED."' AND week_of_month = 4
						THEN sum(billable_hrs)
					END 199W4,
					CASE WHEN current_status < '".ST_APPROVAL_MANAGER."' AND week_of_month = 5
						THEN sum(billable_hrs)
					END 0W5,
					CASE WHEN current_status = '".ST_APPROVAL_MANAGER."' AND week_of_month = 5
						THEN sum(billable_hrs)
					END 8W5,
					CASE WHEN current_status IN ('".ST_APPROVAL_PENDING."', '".ST_APPROVED1."')  AND week_of_month = 5
						THEN sum(billable_hrs)
					END 10W5,
					CASE WHEN current_status = '".ST_APPROVED2."' AND week_of_month = 5
						THEN sum(billable_hrs)
					END 102W5,
					CASE WHEN current_status = '".ST_APPROVED4."' AND week_of_month = 5
						THEN sum(billable_hrs)
					END 104W5,
					CASE WHEN current_status = '".ST_APPROVED."' AND week_of_month = 5
						THEN sum(billable_hrs)
					END 199W5				

			FROM (
					SELECT pt.emp_id, emp.display_name, au.reports_to, au.approval_manager, pt.timesheet_date, pt.billable_hrs,
					CONCAT(DATE_FORMAT(MIN(DATE((pt.timesheet_date)) - INTERVAL (DATE_FORMAT((pt.timesheet_date), '%w') + 6) % 7 DAY), '%M %d'),
					' - ',
					DATE_FORMAT(MIN(DATE((pt.timesheet_date)) - INTERVAL (DATE_FORMAT((pt.timesheet_date), '%w') + 6) % 7 DAY) + INTERVAL 6 DAY, '%M %d')) AS timesheet_week, FLOOR((DAYOFMONTH(pt.timesheet_date) - 1) / 7) + 1 week_of_month, pt.current_status
					FROM project_timesheets pt, employees emp, application_users au
					WHERE emp.company_id = '".$company_id."'
					AND emp.company_id = pt.company_id
					AND emp.billable != 3
					AND emp.id = pt.emp_id
					AND emp.company_id = au.company_id
					AND emp.user_id = au.id
					AND emp.is_active = au.is_active
					AND emp.is_active = pt.is_active
					AND joining_date <= '".$enddate."'
					AND	(
				CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') 
					THEN 1
				WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
					THEN 1
				ELSE	2
				END
			)=1 
					AND (date_of_leaving >= '".$startdate."' OR date_of_leaving IS NULL)
					AND pt.timesheet_date <= '".$enddate."' 
					AND pt.timesheet_date >= '".$startdate."' ".$reproting_manager."
					group by pt.emp_id, emp.display_name, au.reports_to, au.approval_manager,pt.timesheet_date, pt.billable_hrs, pt.current_status
				)a
			group by emp_id, display_name, reports_to, approval_manager, timesheet_week, week_of_month, a.current_status
		) b
		GROUP BY emp_id, display_name, reports_to, approval_manager";
		$result = $callConfig->getAllRows($query);
		foreach($result as $row){
			$retrunarray[$row->reports_to][$row->approval_manager][$row->emp_id] = $row;
		}
		//echo "<pre>";
		//print_r($retrunarray);
		 return $retrunarray; 
	}
	function checkvalll($get){
		global $callConfig;
		$query = " SELECT id,emp_code,cost_center_id FROM `employees` WHERE `cost_center_id` = 0 AND emp_code NOT LIKE 'I%'";
		$datas = $callConfig->getAllRows($query);	
		$tables = array('multi_table_import_data','multi_table_import_data_bangalore','multi_table_import_data_chennai','multi_table_import_data_hydrabad','bangalore_062022','bangalore_13072022','bangalore_18072022');
		foreach($datas as $data){
			foreach($tables as $table){
			 $squery = "SELECT * FROM ".$table." WHERE emp_id LIKE'%".$data->emp_code."%'";
			 $sdatas = $callConfig->getRow($squery);
			 if(count($sdatas)>0){
			 	$data->cost_center_name = $sdatas->cost_center;
				$cquery = "SELECT * FROM cost_centers WHERE cost_center_name LIKE'%".$sdatas->cost_center."%'";
			 	$cdatas = $callConfig->getRow($cquery);
				$data->cost_center_id = $cdatas->id;
				echo $upppp = "UPDATE employees SET cost_center_id=".$cdatas->id." WHERE id =".$data->id;
				$dataa = $callConfig->executeQuery($upppp);
			 	break;
			 }
			}
			//print_r($data); echo "<br>";
		}
		return $data;
	}
	function togetananthdownloadreport($get){
		global $callConfig;
		
		$month = $get['pay_month'];
		$year = $get['pay_year'];
		$startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
		$enddate = date("Y-m-t",strtotime($year."-".$month."-01")); 
		$query ="SELECT
		  emp.id as employee_id,
		  emp.emp_code,
		  emp.display_name as employee_name,
		  emp.vendor_id,
		  (
		    SELECT    vend.display_name
		    FROM    vendors vend
		    WHERE    vend.company_id = emp.company_id
		    AND        vend.id = emp.vendor_id
		  ) vendor_name,
		  emp.department_id,
		  (
		    SELECT    dept.department_name
		    FROM    departments dept
		    WHERE    dept.company_id = emp.company_id
		    AND        dept.id = emp.department_id
		  ) department_name,
		  emp.location_id,
		  (
		    SELECT    loc.location_name
		    FROM    locations loc
		    WHERE    loc.company_id = emp.company_id
		    AND        loc.id = emp.location_id
		  ) location_name,
		  emp.vertical_id,
		  (
		    SELECT    vert.vertical_name
		    FROM    verticals vert
		    WHERE    vert.company_id = emp.company_id
		    AND        vert.id = emp.location_id
		  ) vert_name,
		  au.cost_center as cost_center_id,
		  (
		    SELECT    cc.cost_center_name
		    FROM    cost_centers cc
		    WHERE    cc.company_id = au.company_id
		    AND        cc.id = au.cost_center
		  ) cost_center_name,
		  emp.job_title_id as job_title,
		  (
		    SELECT    jt.job_title
		    FROM    job_titles jt
		    WHERE    jt.company_id = emp.company_id
		    AND        jt.id = emp.job_title_id
		  ) job_title,
		  emp.billable as emp_billable,
		  empptsumm.billable_hrs,
		  empptsumm.nonbillable_hrs,
		  empleaves.leave_days,
		  vrc_vrcitems.experience_base,
		  (
		    SELECT    sts.name
		    FROM    status sts
		    WHERE    sts.company_id = emp.company_id
		    AND        sts.name_value = emp.current_status
		  ) current_status,
		  (
		    SELECT    CASE WHEN mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN    mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price END
		    FROM    manpower_contracts mpc, mpc_items mpcitems
		    WHERE    mpc.company_id = emp.company_id
		      AND    mpc.company_id = mpcitems.company_id
		      AND    mpc.id = mpcitems.transaction_id
		      AND    mpc.id = empwo.work_order_id
		      AND    mpcitems.id = empwo.work_order_detail_id
		      AND    mpcitems.effective_date <= '".$startdate."'
		      AND    mpcitems.expire_date >= '".$enddate."'
		  ) budgeted_amount,
		  empptsumm.billable_amount,
		  (
		    SELECT
		      CASE WHEN vrcitems.purchase_uom = 'PT' THEN vrcitems.item_price / 3 WHEN vrcitems.purchase_uom = 'PS' THEN vrcitems.item_price / 6 WHEN vrcitems.purchase_uom = 'PY' THEN vrcitems.item_price / 12 ELSE vrcitems.item_price END
		    FROM
		      vendor_rate_cards vrc,
		      vrc_items vrcitems
		    WHERE
		      vrc.company_id = emp.company_id
		      AND vrc.company_id = vrcitems.company_id
		      AND vrc.id = vrcitems.transaction_id
		      AND vrc.id = empwo.vrc_id
		      AND vrcitems.id = empwo.vrc_item_id
		      AND vrc.effective_from_date <= '".$startdate."'
		      AND vrc.effective_to_date >= '".$enddate."'
		  ) vrc_budgeted_amount,
		  vrc_billable_amount,
		  (
		    empptsumm.nonbillable_hrs * empptsumm.rate_per_hrs
		  ) as nonbillable_amount,
		  (
		    emp.total_experiance +(
		      DATEDIFF(NOW(), emp.joining_date) / 365
		    )
		  ) exp_joining_date,
		  vrc_vrcitems.min_exp,
		  vrc_vrcitems.max_exp,
		  mpc_mpcitems.mpcitm_purchase_uom as mpc_uom,
		  vrc_vrcitems.vrcitm_purchase_uom as vrc_uom,
		  emp.joining_date,
		  COALESCE(emp.total_experiance, '0') AS total_experiance,
		  CASE WHEN(
		    total_experiance +(
		      DATEDIFF(NOW(), `joining_date`) / 365
		    )
		  ) BETWEEN min_exp
		  AND max_exp THEN '' ELSE 'Experiance Missmatch' END remark,
		  au.reports_to,
		  empwo.work_order_id,
		  empwo.work_order_detail_id,
		  empwo.vrc_id,
		  empwo.vrc_item_id,
		  mpc_mpcitems.mpc_item_price,
		  vrc_vrcitems.vrc_item_price
		FROM
		  employees emp
		  LEFT JOIN (
		    SELECT
		      pt.company_id,
		      pt.emp_id,
		      pt.work_order,
		      SUM(billable_hrs) billable_hrs,
		      SUM(bill_amount) billable_amount,
		      SUM(nonbillable_hrs) nonbillable_hrs,
		      SUM(vrc_billable_amount) vrc_billable_amount,
		      pt.rate_per_hrs
		    FROM
		      project_timesheets pt
		    WHERE
		      pt.timesheet_date >= '".$startdate."'
		      AND pt.timesheet_date <= '".$enddate."'
		    GROUP BY
		      pt.company_id,
		      pt.emp_id,
		      pt.work_order,
		      pt.rate_per_hrs
		  ) empptsumm on (
		    empptsumm.company_id = emp.company_id
		    AND empptsumm.emp_id = emp.id
		  )
		  JOIN application_users au on (
		    emp.company_id = au.company_id
		    and emp.user_id = au.id
		  )
		  LEFT JOIN (
		    SELECT
		      el.company_id,
		      el.employee_id,
		      el.leave_from,
		      el.leave_to,
		      DATEDIFF(
		        LEAST(
		          el.leave_to,
		          last_day(el.leave_from)
		        ),
		        el.leave_from
		      )+ DATEDIFF(
		        el.leave_to,
		        GREATEST(
		          el.leave_from,
		          DATE_SUB(
		            LAST_DAY(el.leave_to),
		            INTERVAL DAY(
		              LAST_DAY(el.leave_to)
		            ) -1 DAY
		          )
		        )
		      ) leave_days
		    FROM
		      employee_leaves el
		    WHERE
		      (
		        (
		          el.leave_from >= '".$startdate."'
		          AND el.leave_from <= '".$enddate."'
		        )
		        OR (
		          el.leave_to >= '".$startdate."'
		          AND el.leave_to <= '".$enddate."'
		        )
		      )
		  ) empleaves on (
		    empleaves.company_id = emp.company_id
		    AND empleaves.employee_id = emp.id
		  )
		  LEFT JOIN (
			SELECT 
          ewo.company_id, 
          ewo.emp_id, 
          ewo.work_order_id, 
          ewo.work_order_detail_id, 
          ewo.vrc_id, 
          ewo.vrc_item_id, 
          ewo.effective_from_date, 
          ewo.effective_to_date, 
          ewo.is_active 
        FROM 
          emp_work_order ewo
      ) empwo on (
        empwo.company_id = emp.company_id 
        AND empwo.emp_id = emp.id 
        AND empwo.is_active = 0 
        AND empwo.effective_from_date <= '".$startdate."' 
        AND empwo.effective_to_date >= '".$enddate."'
		  )
		  LEFT JOIN (
		    SELECT
		      mpc.company_id,
		      mpc.id work_order_id,
		      mpcitems.id work_order_detail_id,
		      mpcitems.purchase_uom mpcitm_purchase_uom,
		      CASE WHEN mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price END mpc_item_price
		    FROM
		      manpower_contracts mpc,
		      mpc_items mpcitems
		    WHERE
		      mpc.company_id = mpcitems.company_id
		      AND mpc.id = mpcitems.transaction_id
		  ) mpc_mpcitems on (
		    mpc_mpcitems.company_id = emp.company_id
		    AND mpc_mpcitems.work_order_id = empwo.work_order_id
		    AND mpc_mpcitems.work_order_detail_id = empwo.work_order_detail_id
		  )
		  LEFT JOIN (
		    SELECT
		      vrc.company_id,
		      vrc.id vrc_id,
		      vrcitems.id vrc_detail_id,
		      vrcitems.purchase_uom vrcitm_purchase_uom,
		      vrcitems.experience_base,
		      vrcitems.max_exp,
		      vrcitems.min_exp,
		      CASE WHEN vrcitems.purchase_uom = 'PT' THEN vrcitems.item_price / 3 WHEN vrcitems.purchase_uom = 'PS' THEN vrcitems.item_price / 6 WHEN vrcitems.purchase_uom = 'PY' THEN vrcitems.item_price / 12 ELSE vrcitems.item_price END vrc_item_price
		    FROM
		      vendor_rate_cards vrc,
		      vrc_items vrcitems
		    WHERE
		      vrc.company_id = vrcitems.company_id
		      AND vrc.id = vrcitems.transaction_id
		  ) vrc_vrcitems on (
		    vrc_vrcitems.company_id = emp.company_id
		    AND vrc_vrcitems.vrc_id = empwo.vrc_id
		    AND vrc_vrcitems.vrc_detail_id = empwo.vrc_item_id
		  )
		WHERE
		  emp.vendor_id IS NOT NULL
		  AND	(
		CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') 
			THEN 1
		WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
			THEN 1
		ELSE	2
		END
		)=1
		  AND (
		    CASE WHEN '3' <> '1' THEN(
		      ('1' = '0')
		      OR (au.reports_to = '179')
		      OR(
		        fn_employee_buddy_id(emp.company_id, emp.id, 'HR') = '186'
		      )
		      OR(
		        fn_employee_buddy_id(emp.company_id, emp.id, 'FIN') = '186'
		      )
		    ) ELSE(
		      emp.vendor_id = (
		        SELECT    id
		        FROM    vendors ven2
		        WHERE    ven2.email = 'ananthapadmanabha.pai@microchip.com'
		      )
		    ) END
		  )
		  ORDER BY employee_name";
		//echo $query;
    		$return = $callConfig->getAllRows($query);
  		return  $return;
	}
	function getTimeSheetSummaryDashboard($get){
		global $callConfig;
	  	$emp_id = $get['emp_id'];
	  	$prevMon = date("Y-m-d", strtotime('monday this week', strtotime($get['fromdate'])));
	  	$presun = date("Y-m-d", strtotime('sunday this week', strtotime($get['todate'])));
	  	$end_year_todate = date('Y-m-d', strtotime('last day of december this year', strtotime($get['fromdate'])));
	  	$start_year_fromdate = date('Y-m-d', strtotime('first day of january this year', strtotime($get['fromdate'])));
	  	$start_month_fromdate = $get['fromdate'];
	   	$end_month_todate = $get['todate'];
	  	$current_month = date('m',strtotime($get['fromdate']));
	  	$current_year = date('Y',strtotime($get['fromdate']));
		$qtr_start_date = date('Y-m-d', strtotime('-2 Months', strtotime($get['fromdate'])));
		$qtr_end_date = date('Y-m-d', strtotime($get['fromdate']));
			//Yearly
			$queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where  company_id = '".$get['company_id']."' AND  is_active=0  AND  `timesheet_date` >= '".$start_year_fromdate."' and `timesheet_date` <= '".$end_year_todate."' AND emp_id IN(SELECT emp.id FROM application_users au,employees emp  WHERE au.approval_manager = '".$get['user_id']."' AND emp.user_id = au.id  AND emp.joining_date <= '".$get['todate']."' AND (emp.date_of_leaving >=  '".$start_month_fromdate."' OR emp.date_of_leaving IS NULL) AND emp.company_id = '".$get['company_id']."' AND emp.company_id = au.company_id) ";
		    $allresponseyear = $callConfig->getRow($queryresponse);
		    $totalhrs['0'] = $allresponseyear->billable_hrs;
				//Qtrly
		    $queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs from  project_timesheets where  company_id = '".$get['company_id']."' AND is_active=0  AND  `timesheet_date` >= '".$qtr_start_date."' and `timesheet_date` <= '".$qtr_end_date."' AND emp_id IN(SELECT emp.id FROM application_users au,employees emp  WHERE au.approval_manager = '".$get['user_id']."' AND emp.user_id = au.id  AND emp.joining_date <= '".$get['todate']."' AND (emp.date_of_leaving >=  '".$start_month_fromdate."' OR emp.date_of_leaving IS NULL) AND emp.company_id = '".$get['company_id']."' AND emp.company_id = au.company_id)";
		    $allresponse = $callConfig->getRow($queryresponse);
		    $totalhrs['1'] = $allresponse->billable_hrs;
			// MOnthly 
		     $queryresponse = "SELECT sum(billable_hrs) as billable_hrs,sum(nonbillable_hrs) as nonbillable_hrs ,sum(billable_hrs)+sum(nonbillable_hrs) as hourstotal from  project_timesheets where  company_id = '".$get['company_id']."' AND is_active=0  AND  `timesheet_date` >= '".$start_month_fromdate."' and `timesheet_date` <= '".$end_month_todate."' AND emp_id IN(SELECT emp.id FROM application_users au,employees emp  WHERE au.approval_manager = '".$get['user_id']."' AND emp.user_id = au.id  AND emp.joining_date <= '".$get['todate']."' AND (emp.date_of_leaving >=  '".$start_month_fromdate."' OR emp.date_of_leaving IS NULL) AND emp.company_id = '".$get['company_id']."' AND emp.company_id = au.company_id)";
		    $allresponse = $callConfig->getRow($queryresponse);
		    $totalhrs['2'] = $allresponse->billable_hrs;
		    $details['summary'] = $totalhrs;
			$queryresponse = "SELECT emp.id,emp.emp_code,emp.display_name,emp.image,emp.email_id, sum(pt.billable_hrs) as billable_hrs,sum(pt.nonbillable_hrs) as nonbillable_hrs  from project_timesheets pt,employees emp,application_users au  where pt.is_active=0 AND pt.company_id = '".$get['company_id']."' AND pt.timesheet_date >=  '".$start_month_fromdate."' and pt.timesheet_date <= '".$get['todate']."'  AND emp.joining_date <= '".$get['todate']."'  AND (emp.date_of_leaving >=  '".$start_month_fromdate."' OR emp.date_of_leaving IS NULL)  AND au.approval_manager = '".$get['user_id']."' AND pt.company_id = emp.company_id AND emp.user_id = au.id AND pt.emp_id = emp.id AND emp.company_id = au.company_id group by emp.id,emp.display_name,emp.image";
		    $allresponseweek = $callConfig->getAllRows($queryresponse);
		    $details['summarydetails'] = $allresponseweek;
		    return $details;
	}
	function admindashboarddetails($data){
		global $callConfig;	
		$startdate = date('Y-m-d',strtotime('first day of this month', time()));
		$enddate =  date('Y-m-d',strtotime('last day of this month', time()));
		$prevstartdate = date('Y-m-d',strtotime('first day of previous month'));
		$prevenddate =  date('Y-m-d',strtotime('last day of previous month'));
		$loccondition = "";
		if($data['location_id']!="" && $data['location_id']!="0"){
			$loccondition = "  OR (emp.location_id = ".$data['location_id_value'].")";
		}
		if($data['location_id']!="" && $data['location_id']=="0"){
			$loccondition = "  OR (emp.location_id IN (".$data['location_id_value']."))";
		}
		$condition = "
		(
	    CASE WHEN (
	      emp.date_of_leaving IS NOT NULL 
	      AND emp.date_of_leaving >= '".$startdate."'
	    ) THEN 1 WHEN (
	      emp.date_of_leaving IS NULL 
	      AND emp.is_active = 0
	    ) THEN 1 ELSE 2 END
	  )= 1 
		AND (
	        CASE WHEN '3' <> '".$data['user_type']."'
	            THEN(
	                ('1' = '".$data['admin_type']."') OR (au.reports_to = '".$data['user_id']."') OR (emp.hr_buddy = '".$data['employee_id']."') OR (emp.finance_buddy = '".$data['employee_id']."') OR (emp.admin_buddy = '".$data['employee_id']."') OR (emp.loc_buddy = '".$data['employee_id']."')".$loccondition."
	      ) ELSE
	          ( emp.vendor_id = (
	                              SELECT  id
	                              FROM    vendors ven2
	                              WHERE   ven2.email = '".$data['user_email']."'
	                            )
	          ) END
	          )";
		$sql = "SELECT emp.id,emp.date_of_leaving,emp.joining_date, emp.vendor_id, emp.billable FROM employees emp,application_users au where  emp.company_id='".$data['company_id']."' AND emp.user_id = au.id AND emp.company_id = au.company_id AND emp.employee_type = 2 AND ".$condition;
		$row=$callConfig->getAllRows($sql);
		$resign = 0;
		$join = 0;
		$preresign = 0;
		$prejoin = 0;
		$totalactive = 0;
		$totalvendor = array();
		$type_billable = array();
		foreach($row as $emplist){
			$totalactive++;
			$type_billable[$emplist->billable] = $type_billable[$emplist->billable]+1;
			if($emplist->joining_date <= $enddate AND $emplist->joining_date >= $startdate ){
				$join++;
			}
			if($emplist->date_of_leaving <= $enddate AND $emplist->date_of_leaving >= $startdate){
				$resign++;
			}
			if($emplist->joining_date <= $prevenddate AND $emplist->joining_date >= $prevstartdate ){
				$prejoin++;
			}
			if($emplist->date_of_leaving <= $prevenddate AND $emplist->date_of_leaving >= $prevstartdate){
				$preresign++;
			}
			if($emplist->vendor_id != "" && $emplist->vendor_id != "0" ){
			$totalvendor[$emplist->vendor_id] = $emplist->vendor_id;
			}
		}
		//echo "total - ".$totalactive."- join - ".$join." -resign : ".$resign."- prejoin - ".$prejoin." -preresign : ".$preresign;
		//$return =   array("total_emp"=>$sql,"this_month_join"=>$sql1,"previous_month_join"=>$sql3,"this_month_leaving"=>$sql4,"previous_month_leaving"=>$sql5);
		$return =   array("total_emp"=>$totalactive,"this_month_join"=>$join,"previous_month_join"=>$prejoin,"this_month_leaving"=>$resign,"previous_month_leaving"=>$preresign,"total_ven"=>count($totalvendor),"sql"=>$sql);
		return $return;
	}
	function bugetaccrualvendorrareport($get){
		global $callConfig;
		if($get['fromdate']!=""){
			$ym = explode("-", $get['fromdate']);
			$month = $ym[1];
			$year = $ym[0];
		}else{
			$month = date('m');
			$year = date('Y');
		}
		$startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
		$enddate = date("Y-m-t",strtotime($year."-".$month."-01"));
		$query="SELECT	emp.vendor_id,
		   IFNULL( (
	            SELECT	vend.display_name
	            FROM	vendors vend
	            WHERE	vend.company_id = emp.company_id
	            AND		vend.id = emp.vendor_id
	        ), 'Uassigned') vendor_name, 
			emp.id as employee_id, 
	        emp.display_name as employee_name, 
			emp.department_id,
			emp.location_id,
			emp.vertical_id,
	        (
	            SELECT	dept.department_name	
	            FROM 	departments dept
	            WHERE	dept.company_id = emp.company_id
	            AND		dept.id = emp.department_id
	        )department_name,
			au.cost_center as cost_center_id,
	        (
	            SELECT	cc.cost_center_name
	            FROM 	cost_centers cc
	            WHERE	cc.company_id = au.company_id
	            AND		cc.id = au.cost_center
	        )cost_center_name,
	        emp.job_title_id as job_title,
	        (
	            SELECT	jt.job_title
	            FROM 	job_titles jt
	            WHERE	jt.company_id = emp.company_id
	            AND		jt.id = emp.job_title_id
	        ) job_title,
	        emp.billable as emp_billable, 
			empptsumm.billable_hrs, 
			empptsumm.nonbillable_hrs,
			empleaves.leave_days,
			vrc_vrcitems.experience_base,
			(
				SELECT	sts.name
				FROM	status sts
				WHERE	sts.company_id = emp.company_id
				AND 	sts.name_value = emp.current_status 
			)current_status,
			(
	 			SELECT 
	          CASE WHEN mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price END 
	        FROM 
	          manpower_contracts mpc, 
	          mpc_items mpcitems 
	        WHERE 
	          mpc.company_id = emp.company_id 
	          AND mpc.company_id = mpcitems.company_id 
	          AND mpc.id = mpcitems.transaction_id 
	          AND mpc.id = empwo.work_order_id 
	          AND mpcitems.id = empwo.work_order_detail_id 
	          AND mpcitems.effective_date <= '".$startdate."' 
	          AND mpcitems.expire_date >= '".$enddate."'
	 	       )budgeted_amount,
			empptsumm.billable_amount,
	        (
			SELECT 
	          CASE WHEN vrcitems.purchase_uom = 'PT' THEN vrcitems.item_price / 3 WHEN vrcitems.purchase_uom = 'PS' THEN vrcitems.item_price / 6 WHEN vrcitems.purchase_uom = 'PY' THEN vrcitems.item_price / 12 ELSE vrcitems.item_price END 
	        FROM 
	          vendor_rate_cards vrc, 
	          vrc_items vrcitems 
	        WHERE 
	          vrc.company_id = emp.company_id 
	          AND vrc.company_id = vrcitems.company_id 
	          AND vrc.id = vrcitems.transaction_id 
	          AND vrc.id = empwo.vrc_id 
	          AND vrcitems.id = empwo.vrc_item_id 
	          AND vrc.effective_from_date <= '".$startdate."'
	          AND vrc.effective_to_date >= '".$enddate."'      
	        )vrc_budgeted_amount,
			vrc_billable_amount,
			(empptsumm.nonbillable_hrs * empptsumm.rate_per_hrs) as nonbillable_amount,
		
	        (
	            emp.total_experiance +(
	                DATEDIFF(NOW(), emp.joining_date) / 365)
	        )exp_joining_date,
			vrc_vrcitems.min_exp,
			vrc_vrcitems.max_exp,
			mpc_mpcitems.mpcitm_purchase_uom as mpc_uom,
	 		vrc_vrcitems.vrcitm_purchase_uom as vrc_uom,
			emp.joining_date,
			COALESCE(emp.total_experiance, '0') AS total_experiance,
			CASE WHEN(
	                total_experiance +(
	                    DATEDIFF(NOW(), `joining_date`) / 365)
	                ) BETWEEN min_exp AND max_exp THEN '' ELSE 'Experiance Missmatch'
				END remark,
	        au.reports_to,    
	 		empwo.work_order_id,
			empwo.work_order_detail_id,
			empwo.vrc_id,
			empwo.vrc_item_id,
	        mpc_mpcitems.mpc_item_price,	
	        vrc_vrcitems.vrc_item_price
			FROM	employees emp
			LEFT JOIN (	SELECT	pt.company_id, pt.emp_id, pt.work_order, SUM(billable_hrs) billable_hrs, SUM(bill_amount) billable_amount, SUM(nonbillable_hrs) nonbillable_hrs, SUM(vrc_billable_amount) vrc_billable_amount, round(pt.rate_per_hrs) as rate_per_hrs
					FROM	project_timesheets pt
					WHERE	pt.is_active = 0 AND pt.timesheet_date >='".$startdate."' AND pt.timesheet_date <='".$enddate."'
	              	GROUP BY pt.company_id, pt.emp_id, pt.work_order, round(pt.rate_per_hrs)
				)empptsumm on (	empptsumm.company_id = emp.company_id AND empptsumm.emp_id = emp.id )
			JOIN application_users au on ( emp.company_id = au.company_id and emp.user_id = au.id)
			LEFT JOIN  (	SELECT	el.company_id, el.employee_id, el.leave_from, el.leave_to,
							DATEDIFF(LEAST(el.leave_to,last_day(el.leave_from)),el.leave_from)+ 
							DATEDIFF(el.leave_to,GREATEST(el.leave_from,DATE_SUB(LAST_DAY(el.leave_to),INTERVAL DAY(LAST_DAY(el.leave_to)) - 1 DAY))) leave_days
					FROM	employee_leaves el
					WHERE	((el.leave_from >='".$startdate."' AND el.leave_from <='".$enddate."') OR (el.leave_to>='".$startdate."' AND el.leave_to <='".$enddate."'))
				)empleaves on ( empleaves.company_id = emp.company_id AND empleaves.employee_id = emp.id )
			LEFT JOIN	(	SELECT 
	          ewo.company_id, 
	          ewo.emp_id, 
	          ewo.work_order_id, 
	          ewo.work_order_detail_id, 
	          ewo.vrc_id, 
	          ewo.vrc_item_id, 
	          ewo.effective_from_date, 
	          ewo.effective_to_date, 
	          ewo.is_active 
	        FROM 
	          emp_work_order ewo
	      ) empwo on (
	        empwo.company_id = emp.company_id 
	        AND empwo.emp_id = emp.id 
	        AND empwo.is_active = 0 
	        AND empwo.effective_from_date <= '".$startdate."' 
	        AND empwo.effective_to_date >='".$enddate."'
								
					 )  
			LEFT JOIN	(
							SELECT mpc.company_id, mpc.id work_order_id, mpcitems.id work_order_detail_id, mpcitems.purchase_uom mpcitm_purchase_uom,		
								CASE 
									WHEN mpcitems.purchase_uom = 'PT' 
										THEN mpcitems.item_price / 3 
									WHEN mpcitems.purchase_uom = 'PS' 
										THEN mpcitems.item_price / 6 
									WHEN mpcitems.purchase_uom = 'PY' 
										THEN mpcitems.item_price / 12 
									ELSE mpcitems.item_price
								END mpc_item_price
							FROM	manpower_contracts mpc,	mpc_items mpcitems
							WHERE	mpc.company_id = mpcitems.company_id
							AND		mpc.id = mpcitems.transaction_id
						)mpc_mpcitems on ( mpc_mpcitems.company_id = emp.company_id AND mpc_mpcitems.work_order_id = empwo.work_order_id AND  mpc_mpcitems.work_order_detail_id = empwo.work_order_detail_id)
			LEFT JOIN	(
							SELECT vrc.company_id, vrc.id vrc_id, vrcitems.id vrc_detail_id, vrcitems.purchase_uom vrcitm_purchase_uom, 
	            					vrcitems.experience_base, vrcitems.max_exp, vrcitems.min_exp, 		
								CASE 
									WHEN vrcitems.purchase_uom = 'PT' 
										THEN vrcitems.item_price / 3 
									WHEN vrcitems.purchase_uom = 'PS' 
										THEN vrcitems.item_price / 6 
									WHEN vrcitems.purchase_uom = 'PY' 
										THEN vrcitems.item_price / 12 
									ELSE vrcitems.item_price
								END vrc_item_price
							FROM	vendor_rate_cards vrc,	vrc_items vrcitems
							WHERE	vrc.company_id = vrcitems.company_id
							AND		vrc.id = vrcitems.transaction_id
						)vrc_vrcitems on ( vrc_vrcitems.company_id = emp.company_id AND vrc_vrcitems.vrc_id = empwo.vrc_id AND  vrc_vrcitems.vrc_detail_id = empwo.vrc_item_id)
	 		WHERE emp.vendor_id IS NOT NULL
				AND	(
						CASE WHEN (emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= '".$enddate."') 
							THEN 1
						WHEN (emp.date_of_leaving IS NULL AND emp.is_active = 0)
							THEN 1
						ELSE	2
						END
					)=1
				 AND 	(
				CASE	WHEN '3' <> '".$get['user_type']."'  
							THEN(
									('1' = '".$get['admin_type']."') OR (au.reports_to = '".$get['user_id']."') 
									OR(fn_employee_buddy_id(emp.company_id, emp.id, 'HR') ='".$get['emp_id']."') 	
									OR(fn_employee_buddy_id(emp.company_id, emp.id, 'FIN') = '".$get['emp_id']."')
								)
						ELSE(
								emp.vendor_id =
									(
										SELECT
											id
										FROM
											vendors ven2
										WHERE
											ven2.email = '".$get['user_email']."'
									)
							)
					END
			) ORDER By 2, 4";
		//echo $query;
		$return = $callConfig->getAllRows($query);
		return $return;
	}
	function togettransactionlogtalbe($get){
		global $callConfig;
		$query = "SELECT DISTINCT(transaction_table) FROM transaction_approval_logs WHERE  user_id=".$get['user_id'];
		$tables = $callConfig->getAllRows($query);

		$query = "SELECT * FROM transaction_approval_logs WHERE user_id=".$get['user_id']." AND transaction_table = '".$get['table']."'";
		$mydata = $callConfig->getAllRows($query);
        //print_r($mydata);
		foreach($mydata as $data){
			$ids[] = $data->transaction_id;
		}
		$query = "SELECT * FROM ".$get['table']." WHERE  id IN (".implode(",",$ids).") AND is_active=0 ";
		$tabledata = $callConfig->getAllRows($query);
		return array('tables'=>$tables,'transaction'=>$mydata,'data'=>$tabledata);
	}
	function getcountpendingtimesheet($get){
		global $callConfig;
		$query = "SELECT count(id) as pcount FROM project_timesheets WHERE company_id = ".$get['company_id']." AND timesheet_date LIKE '".$get['yearmonth']."%' AND current_status > 0 AND current_status < 199 AND is_active = 0";
		$mydata = $callConfig->getRow($query);
		return $mydata;
	}
	function updaterateandvendorprice(){
          global $callConfig;		
		 $query = "SELECT DISTINCT(emp_id) emp_id,vendor_rate_card_id, work_order FROM project_timesheets WHERE emp_id IN (SELECT id FROM employees WHERE location_id =1 AND is_active =0) AND is_active = 0 AND timesheet_date LIKE '2022-11-%'";
		   $ptdt = $callConfig->getAllRows($query);
	      //	print_r($ptdt); exit;
		   foreach($ptdt as $ptd){
			$query = "SELECT * FROM emp_work_order WHERE emp_id = ".$ptd->emp_id." AND work_order_id".$ptd->work_order_id." AND vrc_id=".$ptd->vendor_rate_card_id." AND is_active = 0";
			$ewr = $callConfig->getRow($query);
			if($ewr->id !=""){
				$vrc_item_id = $ewr->vrc_item_id;
				$mpc_id = $ewr->work_order_detail_id;
				$mquery = "SELECT * FROM mpc_items WHERE id=".$mpc_id;
				$mpcdata = $callConfig->getRow($mquery);
				$mrate = $mpcdata->item_price;
				if($mpcdata->purchase_uom=='PH'){ $hmrate = $mrate; }elseif($mpcdata->purchase_uom=='PD'){ $hmrate = $mrate/8; }else{$hmrate = $mrate/152;}
				$vquery = "SELECT * FROM vrc_items WHERE id=".$vrc_item_id;
				$vrcdata = $callConfig->getRow($vquery);
				$vrate = $vrcdata->item_price;
				if($vrcdata->purchase_uom=='PH'){ $hvrate = $vrate; }elseif($vrcdata->purchase_uom=='PD'){ $hvrate = $vrate/8; }else{$hvrate = $vrate/152;}
				print_r($ewr);print_r($mpcdata);print_r($vrcdata); echo $hmrate."/".$hvrate;
				echo $uquery = "UPDATE project_timesheets SET bill_amount = billable_hrs * ".$hmrate.", vrc_billable_amount= billable_hrs *".$hvrate.", rate_per_hrs=".$hmrate." WHERE emp_id=".$ptd->emp_id." AND timesheet_date LIKE '2022-10-%'";
				$upp = $callConfig->executeQuery($uquery);
			} 
	    }
	}
	function togeuseracesstable($get){
		global $callConfig;
		$query = $callConfig->selectQuery($get['main_table'],'*','is_active=0','','','');
		$main_data = $callConfig->getAllRows($query);
		$query = $callConfig->selectQuery($get['sub_table'],'*','is_active=0','','','');
		$sub_data = $callConfig->getAllRows($query);
		return array('main_data'=>$main_data, 'sub_data'=>$sub_data);
	}
	function addorremoveusersubtable($post,$get){
		global $callConfig;
		//print_r($post);//print_r($get);exit;
		 $query = "SELECT * FROM ".$post['main_table']." WHERE company_id = ".$get['company_id']." AND  user_id = ".$post['user_id']." AND ".$post['main_reort_id']."=".$post['report_id']." AND is_active = 0";
		//echo $query; //exit; 
		$data = $callConfig->getRow($query);
		print_r($data);
		if($data->id!=""){
			//echo "data"; 
			//$query=$callConfig->updateRecord($post['main_table'],array('is_active'=>'1','updated_by'=>$post['user_id']),'id',$data->id);
			$query= "UPDATE ".$post['main_table']." SET is_active=1, updated_by=".$get['user_id']." WHERE id = ".$data->id;
			//echo $query;
			$qres = $callConfig->executeQuery($query);
		}else{
			//echo "check"; 
			$distribute = array('company_id'=>$get['company_id'],'user_id'=>$post['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$distribute[$post['main_reort_id']] = $post['report_id'];
	
			//print_r($distribute);
           		$qres=$callConfig->insertRecord($post['main_table'],$distribute);
		}
		return $qres;
	}
	function bugetvendorrareport($get){
        global $callConfig;
		//echo "Check Test";
        if($get['fromdate']!=""){
                $ym = explode("-", $get['fromdate']);
                $month = $ym[1];
                $year = $ym[0];
        }else{
                $month = date('m');
                $year = date('Y');
        }
        $emp_id = $get['employee_id']!="" ? $get['employee_id'] : $get['emp_id'];
        $user_id = $get['application_user_id']!="" ? $get['application_user_id'] : $get['user_id'];
        $startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
        $enddate = date("Y-m-t",strtotime($year."-".$month."-01"));
        $loccondition = "";
        if($get['location_id']!="" && $get['location_id']!="0"){
                $loccondition = "  AND (emp.location_id = ".$get['location_id_value'].")";
        }

		$query= "SELECT 
		  customer_id as vendor_id,
		  vendor_name, 
		  employee_id,
		  emp_code, 
		  display_name as employee_name,
		  department_id, 
		  location_id,
		  vertical_id,
		  department_name, 
		  location_name,
		  job_title_id,
		  job_title,
		  emp_billable,
		  billable_hrs, 
		  nonbillable_hrs, 
		  leave_days,
		  experience_base,
		  current_status,
		  budgeted_amount, 
		  billable_amount, 
		  vrc_budgeted_amount,
		  vrc_billable_amount, 
		  ( nonbillable_hrs * rate_per_hrs ) as nonbillable_amount,  
		  exp_joining_date, 
		  min_exp,
		  max_exp,
		  mpc_uom, 
		  vrc_uom, 
		  joining_date,
		  total_experiance,
		  remark, 
		  reports_to, 
		  work_order_id, 
		  work_order_detail_id, 
		  vrc_id, 
		  vrc_item_id, 
		  mpc_item_price, 
		  vrc_item_price,
		  verticle_name, 
		  cost_center_name 
		FROM 
		  (
			SELECT 
			  * 
			FROM 
			  (
				SELECT 
				  'Q1' AS 'Q', 
				  emp.id emp_id, 
				  emp.company_id emp_company_id, 
				  (
					SELECT 
					  IFNULL(vend.company, vend.display_name) 
					FROM 
					  vendors vend 
					WHERE 
					  vend.company_id = vrc.company_id 
					  AND vend.id = vrc.customer_id
				  ) vendor_name, 
				  vrc.customer_id, 
				  emp.emp_code, 
				  emp.id employee_id, 
				  emp.display_name, 
				  emp.department_id, 
				  (
					SELECT 
					  dept.department_name 
					FROM 
					  departments dept 
					WHERE 
					  dept.company_id = emp.company_id 
					  AND dept.id = emp.department_id
				  ) department_name, 
				  emp.location_id, 
				  (
					SELECT 
					  loc.location_name 
					FROM 
					  locations loc 
					WHERE 
					  loc.company_id = emp.company_id 
					  AND loc.id = emp.location_id
				  ) location_name, 
				  emp.vertical_id, 
				  (
					SELECT 
					  vc.vertical_name 
					FROM 
					  verticals vc 
					WHERE 
					  vc.company_id = emp.company_id 
					  AND vc.id = emp.vertical_id
				  ) verticle_name, 
				  (
					SELECT 
					  cc.cost_center_name 
					FROM 
					  cost_centers cc 
					WHERE 
					  cc.company_id = au.company_id 
					  AND cc.id = au.cost_center
				  ) cost_center_name, 
				  emp.job_title_id AS job_title_id, 
				  (
					SELECT 
					  jt.job_title 
					FROM 
					  job_titles jt 
					WHERE 
					  jt.company_id = emp.company_id 
					  AND jt.id = emp.job_title_id
				  ) job_title, 
				  emp.billable AS emp_billable, 
				  (
					SELECT 
					  DATEDIFF(
						LEAST(
						  el.leave_to, 
						  last_day(el.leave_from)
						), 
						el.leave_from
					  ) + DATEDIFF(
						el.leave_to, 
						GREATEST(
						  el.leave_from, 
						  DATE_SUB(
							LAST_DAY(el.leave_to), 
							INTERVAL DAY(
							  LAST_DAY(el.leave_to)
							) -1 DAY
						  )
						)
					  ) leave_days 
					FROM 
					  employee_leaves el 
					WHERE 
					  emp.company_id = el.company_id 
					  AND emp.id = el.employee_id 
					  AND (
						(
						  el.leave_FROM >= '".$startdate."' 
						  AND el.leave_FROM <= '".$enddate."'
						) 
						OR (
						  el.leave_to >= '".$startdate."' 
						  AND el.leave_to <= '".$enddate."'
						)
					  )
				  ) leave_days, 
				  vrcitems.experience_base, 
				  (
					SELECT 
					  sts.name 
					FROM 
					  status sts 
					WHERE 
					  sts.company_id = emp.company_id 
					  AND sts.name_value = emp.current_status
				  ) current_status, 
				  CASE WHEN mpcitems.purchase_uom = 'PT' THEN mpcitems.item_price / 3 WHEN mpcitems.purchase_uom = 'PS' THEN mpcitems.item_price / 6 WHEN mpcitems.purchase_uom = 'PY' THEN mpcitems.item_price / 12 ELSE mpcitems.item_price END budgeted_amount, 
				  CASE WHEN vrcitems.purchase_uom = 'PT' THEN vrcitems.item_price / 3 WHEN vrcitems.purchase_uom = 'PS' THEN vrcitems.item_price / 6 WHEN vrcitems.purchase_uom = 'PY' THEN vrcitems.item_price / 12 ELSE vrcitems.item_price END vrc_budgeted_amount, 
				  (
					emp.total_experiance + (
					  DATEDIFF(NOW(), emp.joining_date) / 365
					)
				  ) exp_joining_date, 
				  vrcitems.min_exp, 
				  vrcitems.max_exp, 
				  mpcitems.purchase_uom AS mpc_uom, 
				  vrcitems.purchase_uom AS vrc_uom, 
				  emp.joining_date, 
				  COALESCE(emp.total_experiance, '0') AS total_experiance, 
				  CASE WHEN (
					total_experiance + (
					  DATEDIFF(NOW(), `joining_date`) / 365
					)
				  ) BETWEEN vrcitems.min_exp 
				  AND vrcitems.max_exp THEN '' ELSE 'Experience Mismatch' END remark, 
				  au.reports_to, 
				  ewo.work_order_detail_id, 
				  ewo.vrc_id, 
				  ewo.vrc_item_id, 
				  ewo.work_order_id, 
				  mpcitems.item_price mpc_item_price, 
				  vrcitems.item_price vrc_item_price 
				FROM 
				  employees emp 
				  JOIN application_users au ON emp.company_id = au.company_id 
				  AND emp.user_id = au.id 
				  JOIN vendor_rate_cards vrc ON emp.company_id = vrc.company_id 
				  JOIN vrc_items vrcitems ON vrc.company_id = vrcitems.company_id 
				  AND vrc.id = vrcitems.transaction_id 
				  JOIN manpower_contracts mpc ON emp.company_id = mpc.company_id 
				  JOIN mpc_items mpcitems ON mpc.company_id = mpcitems.company_id 
				  AND mpc.id = mpcitems.transaction_id 
				  JOIN emp_work_order ewo ON vrc.id = ewo.vrc_id 
				  AND vrcitems.id = ewo.vrc_item_id 
				  AND emp.company_id = ewo.company_id 
				  AND emp.id = ewo.emp_id 
				  AND mpc.id = ewo.work_order_id 
				  AND mpcitems.id = ewo.work_order_detail_id 
				WHERE 
				  ewo.effective_from_date <= '".$enddate."' 
				  AND ewo.effective_to_date >= '".$startdate."' 
				  AND emp.employee_type = 2 
				  AND emp.billable != 2
				  AND (
					CASE WHEN (
					  emp.date_of_leaving IS NOT NULL 
					  AND emp.date_of_leaving >= '".$startdate."'
					) THEN 1 WHEN (
					  emp.date_of_leaving IS NULL 
					  AND emp.is_active = 0
					) THEN 1 ELSE 2 END
				  ) = 1 
				  AND (
					CASE WHEN '3' <> '1' THEN (
					  ('1' = '1') 
					  OR (au.reports_to = '".$user_id."') 
					  OR (emp.hr_buddy = '".$emp_id."') 
					  OR (emp.finance_buddy = '".$emp_id."') 
					  OR (emp.admin_buddy = '".$emp_id."') 
					  OR (emp.project_buddy = '".$emp_id."') 
					  OR (emp.loc_buddy = '".$emp_id."')
					) ELSE (
					  emp.vendor_id = (
						SELECT 
						  id 
						FROM 
						  vendors ven2 
						WHERE 
						  ven2.email = '".$get['user_email']."'
					  )
					) END
				  )
			  ) alltables 
			  JOIN (
				SELECT 
				  pt.company_id AS pt_company_id, 
				  pt.emp_id AS pt_emp_id, 
				  pt.work_order, 
				  SUM(billable_hrs) billable_hrs, 
				  SUM(bill_amount) billable_amount, 
				  SUM(nonbillable_hrs) nonbillable_hrs, 
				  SUM(vrc_billable_amount) vrc_billable_amount, 
				  ROUND(pt.rate_per_hrs) AS rate_per_hrs 
				FROM 
				  project_timesheets pt 
				WHERE 
				  pt.is_active = 0 
				  AND pt.current_status > 0 
				  AND pt.timesheet_date >=  '".$startdate."' 
				  AND pt.timesheet_date <= '".$enddate."' 
				GROUP BY 
				  pt.company_id, 
				  pt.emp_id, 
				  pt.work_order , ROUND(pt.rate_per_hrs)
				  ) empptsumm ON empptsumm.pt_company_id = alltables.emp_company_id 
			  AND empptsumm.pt_emp_id = alltables.emp_id 
			  AND empptsumm.work_order = alltables.work_order_id 
			UNION 
			SELECT 
			  distinct * 
			FROM 
			  (
				SELECT 
				  'Q2' AS 'Q2', 
				  emp.id emp_id, 
				  emp.company_id emp_company_id, 
				  (
					SELECT 
					  IFNULL(vend.company, vend.display_name) 
					FROM 
					  vendors vend 
					WHERE 
					  vend.company_id = emp.company_id 
					  AND vend.id = emp.vendor_id
				  ) vendor_name, 
				  emp.vendor_id, 
				  emp.emp_code, 
				  emp.id employee_id, 
				  emp.display_name, 
				  emp.department_id, 
				  (
					SELECT 
					  dept.department_name 
					FROM 
					  departments dept 
					WHERE 
					  dept.company_id = emp.company_id 
					  AND dept.id = emp.department_id
				  ) department_name, 
				  emp.location_id, 
				  (
					SELECT 
					  loc.location_name 
					FROM 
					  locations loc 
					WHERE 
					  loc.company_id = emp.company_id 
					  AND loc.id = emp.location_id
				  ) location_name, 
				  emp.vertical_id, 
				  (
					SELECT 
					  vc.vertical_name 
					FROM 
					  verticals vc 
					WHERE 
					  vc.company_id = emp.company_id 
					  AND vc.id = emp.vertical_id
				  ) verticle_name, 
				  (
					SELECT 
					  cc.cost_center_name 
					FROM 
					  cost_centers cc 
					WHERE 
					  cc.company_id = au.company_id 
					  AND cc.id = au.cost_center
				  ) cost_center_name, 
				  emp.job_title_id AS job_title_id, 
				  (
					SELECT 
					  jt.job_title 
					FROM 
					  job_titles jt 
					WHERE 
					  jt.company_id = emp.company_id 
					  AND jt.id = emp.job_title_id
				  ) job_title, 
				  emp.billable AS emp_billable, 
				  (
					SELECT 
					  DATEDIFF(
						LEAST(
						  el.leave_to, 
						  last_day(el.leave_from)
						), 
						el.leave_from
					  ) + DATEDIFF(
						el.leave_to, 
						GREATEST(
						  el.leave_from, 
						  DATE_SUB(
							LAST_DAY(el.leave_to), 
							INTERVAL DAY(
							  LAST_DAY(el.leave_to)
							) -1 DAY
						  )
						)
					  ) leave_days 
					FROM 
					  employee_leaves el 
					WHERE 
					  emp.company_id = el.company_id 
					  AND emp.id = el.employee_id 
					  AND (
						(
						  el.leave_FROM >=  '".$startdate."' 
						  AND el.leave_FROM <= '".$enddate."'
						) 
						OR (
						  el.leave_to >= '".$startdate."' 
						  AND el.leave_to <= '".$enddate."'
						)
					  )
				  ) leave_days, 
				  0 AS experience_base, 
				  (
					SELECT 
					  sts.name 
					FROM 
					  status sts 
					WHERE 
					  sts.company_id = emp.company_id 
					  AND sts.name_value = emp.current_status
				  ) current_status, 
				  0 AS budgeted_amount, 
				  0 AS vrc_budgeted_amount, 
				  (
					emp.total_experiance + (
					  DATEDIFF(NOW(), emp.joining_date) / 365
					)
				  ) exp_joining_date, 
				  0 AS min_exp,				 
				  0 AS max_exp,
				  '' AS mpc_uom,
				  ' ' AS vrc_uom,
				  emp.joining_date, 
				  COALESCE(emp.total_experiance, '0') AS total_experiance, 
				  ' ' remark, 
				  au.reports_to, 
				  ' ' AS work_order_detail_id, 
				  ' ' AS vrc_id, 
				  ' ' AS vrc_item_id,
				  ' ' AS work_order_id,
				  ' ' AS mpc_item_price,
				  ' ' AS vrc_item_price,
				  emp.company_id, 
				  emp.id, 
				  0 AS pt_work_order, 
				  0 AS billable_hrs, 
				  0 AS billable_amount, 
				  0 AS nonbillable_hrs, 
				  0 AS vrc_billable_amount, 
				  0 AS rate_per_hrs 
				FROM 
				  employees emp 
				  JOIN application_users au ON emp.company_id = au.company_id 
				  AND emp.user_id = au.id 
				  AND NOT EXISTS (
					SELECT 
					  'X' 
					FROM 
					  project_timesheets pt 
					WHERE 
					  pt.company_id = emp.company_id 
					  AND pt.emp_id = emp.id 
					  AND pt.is_active = 0 
					  AND pt.current_status > 0 
					  AND pt.timesheet_date >= '".$startdate."' 
					  AND pt.timesheet_date <= '".$enddate."' 
					LIMIT 
					  1
				  ) AND emp.employee_type = 2 
				  AND emp.billable != 2
				  AND (
					CASE WHEN (
					  emp.date_of_leaving IS NOT NULL 
					  AND emp.date_of_leaving >= '".$startdate."'
					) THEN 1 WHEN (
					  emp.date_of_leaving IS NULL 
					  AND emp.is_active = 0
					) THEN 1 ELSE 2 END
				  ) = 1 
				  AND (
					CASE WHEN '3' <> '1' THEN (
					  ('1' = '1') 
					  OR (au.reports_to = '".$user_id."') 
					  OR (emp.hr_buddy = '".$emp_id."') 
					  OR (emp.finance_buddy = '".$emp_id."') 
					  OR (emp.admin_buddy = '".$emp_id."') 
					  OR (emp.project_buddy = '".$emp_id."') 
					  OR (emp.loc_buddy = '".$emp_id."')
					) ELSE (
					  emp.vendor_id = (
						SELECT 
						  id 
						FROM 
						  vendors ven2 
						WHERE 
						  ven2.email = '".$get['user_email']."'
					  )
					) END
				  )
			  ) alltables
		  ) a 
		ORDER BY 
		  1,3";
		//	echo $query; exit;
	        $return = $callConfig->getAllRows($query);
        	return $return;
	}
	function  vmssingleemployeedashoard($get){
		global $callConfig;
		//print_r($get);
		$fristdate = date("Y-m-01", strtotime( date( 'Y-m-01' )." -6 months")); 
		$lastdate = date("Y-m-t"); 
		$query ="SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND timesheet_date >= '".$fristdate."' AND timesheet_date <= '".$lastdate."' AND is_active = 0 GROUP BY week, startofweek, lastofweek, month, year, current_status ORDER BY startofweek ASC";
		$queries['timesheet'] = $query;
		$lists = $callConfig->getAllRows($query);
		foreach($lists as $list){
			$timesheet[$list->year."-".$list->month][$list->week][$list->current_status] = $list->hrs;
			$weekdates[$list->year."-".$list->month][$list->week]['start'] = $list->startofweek;
			$weekdates[$list->year."-".$list->month][$list->week]['end'] = $list->lastofweek;
		}
		$query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year,  sum(billable_hrs) hrs  FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status NOT IN (".ST_APPROVED.",".ST_ACTIVE.") GROUP BY week, startofweek, lastofweek, month, year ORDER BY startofweek  DESC LIMIT 0,10";

		$queries['submit'] = $query;
		$sumit = $callConfig->getAllRows($query);
		$query = "SELECT MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_APPROVED." GROUP BY  month,year ORDER BY month DESC LIMIT 0, 10";
		$queries['approve'] = $query;
		$approve = $callConfig->getAllRows($query);
		 $query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_ACTIVE."   GROUP BY week, startofweek, lastofweek, month, year, current_status ORDER BY startofweek  DESC LIMIT 0,10";
		$create = $callConfig->getAllRows($query);

		$queries['create'] = $query;
		$query = "SELECT id,display_name,location_id FROM employees WHERE id=".$get['empid'];
		$employee = $callConfig->getRow($query);
		 $query = "SELECT * FROM holidays WHERE holiday_date >= '".$fristdate."' AND holiday_date <= '".$lastdate."' AND location_id = ".$employee->location_id." AND is_active = 0";
		$holidays = $callConfig->getAllRows($query);
		foreach($holidays as $holiday){
			$n = date("n",strtotime($holiday->holiday_date));
			$holileave[$n] = $holileave[$n]+1;
		}
		$fdate=$fristdate;
		for($i=1;$i<190;$i++){
			//echo $fdate."<br>";
			if(strtotime($fdate)<=strtotime($lastdate)){
				$n = date("n",strtotime($fdate));
				$day = date("w",strtotime($fdate));
				if($day!=0 && $day!=6 ){
					$workdays[$n] = $workdays[$n]+1;
				}
			}else{
				break;
			}
			$fdate = date('Y-m-d', strtotime("+1 day", strtotime($fdate)));
		}
		foreach($workdays as $key=>$work){
			$nonetworkindays[$key] = $work-$holileave[$key];
		}
		 $query = "SELECT * FROM holidays WHERE assessment_year >= '".date("Y")."' AND location_id = ".$employee->location_id." AND holiday_date >= '".date('Y-m-d')."' AND  is_active = 0";
		$assmentholidays = $callConfig->getAllRows($query);
		return array('timesheet'=>$timesheet, 'submited'=>$sumit, 'approved'=>$approve, 'create'=>$create,'employee'=>$employee, 'holidays'=>$holidays,'holileave'=>$holileave, 'workdays'=>$workdays, 'nonetworkindays'=>$nonetworkindays, 'assmentholidays'=>$assmentholidays, 'weekdates'=>$weekdates,'queries'=>$queries);
	}
	function tocalworkingdays($data){
		global $callConfig;
		if($get['fromdate']!=""){
			$ym = explode("-", $get['fromdate']);
			$month = $ym[1];
			$year = $ym[0];
		}else{
			$month = date('m');
			$year = date('Y');
		}
		$from = $year."-".$month."-01";
		$to = date("Y-m-t", strtotime($from));
		$attstc = "SELECT * FROM attendance_settings WHERE is_active=0 ";
		$atsdata = $callConfig->getRow($attstc);
		$workingdays = $atsdata->working_days;
		$working_days = json_decode($workingdays,1);
		//echo "<pre>"; print_r($working_days);
		$holidayquery = "SELECT * FROM holidays WHERE holiday_date >='".$from."' AND holiday_date <= '".$to."' AND is_active=0";
		$holidaydata = $callConfig->getAllRows($holidayquery);
		foreach((array)$holidaydata  as $holidayda){
			$holidays[$holidayda->location_id][] = $holidayda->holiday_date;
		}
		//print_r($holidays);
		$datediff = strtotime($to) - strtotime($from);
		$datediff = floor($datediff/(60*60*24));
		for($i = 0; $i < $datediff + 1; $i++){
			$valda =  date("Y-m-d", strtotime($from . ' + ' . $i . 'day'));
			$valday = ucwords(date("l", strtotime($from . ' + ' . $i . 'day')));
			if(in_array($valday, (array)$working_days)){ 
				if(!in_array($valda, (array)$holidays)){ 
					//echo "step2";
					$work_days = $work_days+1;
				}
			}
		}
		return $work_days;

	}
	function getvendors($get){
		global $callConfig;
		$condition = "";
		$sortby = " ORDER BY display_name ASC";
		$limit = "LIMIT 10";
		//$perfect_match = $get['perfect_match']=="0":"AND":"OR":
		if($get['filter'] =="Y"){
			if($get['location_id'] !=""){
				$condition .=" AND location_id = '".$get['location_id']."'";	
			}

			if($get['vendor_name'] !=""){
				$condition .=" AND display_name like '%".$get['vendor_name']."%'";
			}
			if($get['vendor_email'] !=""){
				$condition .=" AND email like '%".$get['vendor_email']."%'";
			}

			if($get['gstin'] !=""){
				$condition .=" AND gstin like '%".$get['gstin']."%'";
			}

			if($get['vendor_tax_reg_no'] !=""){
				$condition .=" AND vendor_tax_reg_no like '%".$get['vendor_tax_reg_no']."%'";
			}
			if($get['cst_reg_no'] !=""){
				$condition .=" AND cst_reg_no like '%".$get['cst_reg_no']."%'";
			}
			if($get['pan_no'] !=""){
				$condition .=" AND pan_no like '%".$get['alphabet']."%'";
			}
			if($get['alphabet'] !=""){
				$condition .=" AND display_name like '".$get['alphabet']."%'";
			}
			
			
			if($get['sort_by'] !=""){
				$sortby =" ORDER BY ".$get['sort_by']." ASC ";	
			}
			if($get['limit'] !=""){
				$limit =" limit ".$get['limit'];	
			}
			
			if($get['perfect_match'] !="0"){
				$condition ="";	
			}
			
		}
		$candidatessql = "SELECT * FROM vendors WHERE company_id='".$get['company_id']."' ".$condition." AND is_active='0' ".$sortby." ".$limit; 
		$candidatesres = $callConfig->getAllRows($candidatessql);
		$return['vendor_sql'] = $candidatessql;
		$return['candidates'] = $candidatesres;
		$return['sql'] = $candidatessql;
		
		$locationssql = "SELECT id,location_name FROM locations WHERE company_id='".$get['company_id']."' AND is_active='0'"; 
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['locations'] = $locationsres;
		
		$locationssql = "SELECT id,job_title FROM job_titles WHERE company_id='".$get['company_id']."' AND is_active='0'"; 
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['job_titles'] = $locationsres;

		$genderssql = "select genc_detail_code,genc_detail_name from general_code_details where company_id='".$get['company_id']."' AND genc_code='GENDER' AND is_active='0'"; 
		$display_details = $callConfig->getAllRows($genderssql);
		$return['genders'] = $display_details;

		$jobtypesql = "select genc_detail_code,genc_detail_name from general_code_details where company_id='".$get['company_id']."' AND genc_code='EMPLOYEETYPES' AND is_active='0'"; 
		$jobtyperes = $callConfig->getAllRows($jobtypesql);
		$return['jobtype'] = $jobtyperes; 

		$job_posting_candidatesql = "SELECT count(id) as cnt,candidate_id FROM `job_posting_candidates` WHERE company_id = '".$get['company_id']."' group by candidate_id"; 
		$job_posting_candidateres = $callConfig->getAllRows($job_posting_candidatesql);
		$return['job_posting_candidates'] = $job_posting_candidateres; 

		$email_logssql = "SELECT email, email_content_ref_id, COUNT(id)as cnt FROM `email_logs` WHERE company_id = '".$get['company_id']."' GROUP BY email,email_content_ref_id"; 
		$email_logsres = $callConfig->getAllRows($email_logssql);
		foreach($email_logsres as $email_logsre){
			$emaillogsres[$email_logsre->email][$email_logsre->email_content_ref_id] = $email_logsre->cnt;
		}
		$return['email_logs'] = $emaillogsres; 

		$ratesql = "SELECT vendor_id, AVG(`over_all_ratting`) rating FROM `vendor_feedback_responses` WHERE company_id = '".$get['company_id']."' AND is_active = 0 GROUP BY vendor_id"; 
		$rateres = $callConfig->getAllRows($ratesql);
		foreach($rateres as $rater){
			$vendor_rate[$rater->vendor_id] = $rater->rating;
		}
		$return['vendor_rate'] = $vendor_rate; 
		
		return $return;
	}
	function createvendorfeedback($post,$get){
		global $callConfig;
		$rowid=$get['rowid'];
		$table_setup = "vendor_feedback_setup";
		$table_questions = "vendor_feedback_setup_questions";
		$table_options = "vendor_feedback_setup_question_options";
		$main = $post['main'];
		$quesion_data = $post['qustion'];
		/*$whr="company_id=".$get['company_id']." and is_active='0' and id=".$main['survey_assigned_to'];
		$query=$callConfig->selectQuery(TBL_FILTER_TABLES,'*',$whr,'','','');
		$shared_content_details = $callConfig->getRow($query);
		$shared_list = $shared_content_details->content;
		$shared_list = rtrim($shared_list,'"');
		$shared_list = ltrim($shared_list,'"');
		$shared_ids = json_decode($shared_list,1);
		$assigned_to_employees = json_encode($shared_ids['matches_names'],1);
		$shared_list1 = rtrim($assigned_to_employees,'"');
		$shared_list1 = ltrim($shared_list1,'"');
		$shared_ids1 = json_decode($shared_list1,1);
		foreach($shared_ids1 as $ids){
			$shared_idslist[]= $ids['id_value'];
		}
		$main['assigned_to_employees'] = json_encode($shared_idslist,1);
		$main['assigned_to_count']  = count((array)$shared_idslist);*/
    	//print_r($main);exit;
		if($rowid=="") {
			$array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date']);			
			$res=$callConfig->insertRecord($table_setup,$main);
			$s_id = $res['LASTID'];
			/*foreach($shared_ids1 as $ids){
				$response_array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date'],'employee_id'=>$ids['id_value'],'survey_id'=>$s_id,'category'=>$main['category']);
				$res=$callConfig->insertRecord(TBL_SURVEY_RESPONSES,$response_array);
			}*/
			foreach ($quesion_data as $key => $value) {
				$value['transaction_id'] = $s_id;
				$survey_question_option['question_option'] = $value['survey_question_option'];
				$survey_question_option['transaction_id'] = $s_id;
				unset($value['survey_question_option']);
				$quesion_array = array_merge($value,$array);
				$qres=$callConfig->insertRecord($table_questions,$quesion_array);
				$survey_question_option['question_id'] = $qres['LASTID'];
				$answer_array = array_merge($survey_question_option,$array);
				$qrespons=$callConfig->insertRecord($table_options,$answer_array);
			}
			$retunn_survey_id = $s_id;
		} else {

			$array = array('company_id'=>$main['company_id'],'is_active'=>'0','updated_by'=>$main['updated_by'],'updated_date'=>$main['updated_date']);			
			$res=$callConfig->updateRecord($table_setup,$main,'id',$rowid);
    		// two update querys - qustiontable and option table is_aactive=1
			$setisactive = array('is_active'=>'1');
			$qrespons=$callConfig->updateRecord($table_questions,$setisactive,'transaction_id',$rowid);
			$qrespons=$callConfig->updateRecord($table_options,$setisactive,'transaction_id',$rowid);
			//$qrespons=$callConfig->updateRecord(TBL_SURVEY_RESPONSES,$setisactive,'survey_id',$rowid);
			/*foreach($shared_ids1 as $ids){
				$response_array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date'],'employee_id'=>$ids['id_value'],'survey_id'=>$rowid,'category'=>$main['category']);
				$res=$callConfig->insertRecord(TBL_SURVEY_RESPONSES,$response_array);
			}*/
			foreach ((array)$quesion_data as $key => $value) {
    			//if(qustionid!='') thn update is_active=0 and values questintabe
    			//update is_active=0 and valus in option table conditoon survey id and question id
    			//else = insert to table is_active=0
				$value['transaction_id'] = $rowid;
				$survey_question_option['question_option'] = $value['survey_question_option'];
				$survey_question_option['transaction_id'] = $rowid;
				$question_id = $value['question_id'];//exit;
				unset($value['survey_question_option']);
				unset($value['question_id']);
				unset($value['rid']);
				$quesion_array = array_merge($value,$array);
				if($question_id!=''){
					$qres=$callConfig->updateRecord($table_questions,$quesion_array,'id',$question_id);
					//$survey_question_option['question_id'] = $question_id;
					$answer_array = array_merge($survey_question_option,$array);
					$qrespons=$callConfig->updateRecord($table_options,$answer_array,'question_id',$question_id);
				}else{
					$qres=$callConfig->insertRecord($table_questions,$quesion_array);
					$survey_question_option['question_id'] = $qres['LASTID'];
					$answer_array = array_merge($survey_question_option,$array);
					$qrespons=$callConfig->insertRecord($table_options,$answer_array);
				}
			}
			$retunn_survey_id = $rowid;
		}
		return $retunn_survey_id;
    }
    function getvendorfeedbackeditdetails($get){

		global $callConfig;
		$details['get'] = $get;
		$whr="company_id=".$get['company_id']." and is_active='0' and id=".$get['survey_id'];
		$query=$callConfig->selectQuery('vendor_feedback_setup','*',$whr,'','','');
		$details['survey_details'] = $callConfig->getRow($query);

		$whr="company_id=".$get['company_id']." and is_active='0' and transaction_id=".$get['survey_id'];
		$query=$callConfig->selectQuery('vendor_feedback_setup_questions','*',$whr,'','','');
		$details['survey_qustion_details'] = $callConfig->getAllRows($query);
        	//print_r($whr); exit;

		$whr="company_id=".$get['company_id']." and is_active='0' and transaction_id=".$get['survey_id'];
		$query=$callConfig->selectQuery('vendor_feedback_setup_question_options','*',$whr,'','','');
		$details['survey_option_details'] = $callConfig->getAllRows($query);
			$whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
		$details['qustion_types'] = $callConfig->getAllRows($query);



     	//  echo '<pre>'; print_r($details['survey_qustion_details']); exit;
		return $details;
	}
    function getvendorfeedbackdata($get){
		global $callConfig; 
		$whr="company_id=".$get['company_id']." AND id = ".$get['setup_id']." AND is_active='0'";
		$query=$callConfig->selectQuery('vendor_feedback_setup','*',$whr,'','','');
		$details['feedback_setup_details'] = $callConfig->getRow($query);
		$details['feedback_setup_detail_query'] = $query;

		$whr="company_id=".$get['company_id']." AND vendor_feedback_setup_id = ".$get['setup_id']." AND is_active='0'";
		$query=$callConfig->selectQuery('vendor_feedback_categories','id, category_name name',$whr,'','','');
		$details['feedback_details'] = $callConfig->getAllRows($query);
		$details['feedback_detail_query'] = $query; 

		$whr="company_id=".$get['company_id']." AND is_active='0'";
		$query=$callConfig->selectQuery('vendor_feedback_setup_questions','*',$whr,'','','');
		$qustion_details = $callConfig->getAllRows($query);

		$query_ans=$callConfig->selectQuery('vendor_feedback_setup_question_options','*',$whr,'','','');
		$answer_details = $callConfig->getAllRows($query_ans);

		foreach ($qustion_details as $value) {
			$disp_question[$value->transaction_id][$value->id]['question'] = $value->question;
			$disp_question[$value->transaction_id][$value->id]['options_display_type'] = $value->options_display_type;
			$disp_question[$value->transaction_id][$value->id]['require_response'] = $value->require_response;
			$disp_question[$value->transaction_id][$value->id]['allow_multiple_selections'] = $value->allow_multiple_selections;
		}
		foreach ($answer_details as $value) {
			$disp_question[$value->transaction_id][$value->question_id]['question_option'] = $value->question_option;
		}

		$details['questions'] = $disp_question;

		return $details;
	}
    function addvendorfeedbackresponse($post, $get){
    	global $callConfig;

    	$update_query = "UPDATE vendor_feedback_responses SET is_active = NULL WHERE company_id =".$get['company_id']." AND  vendor_feedback_setup_id = ".$post['setup_id']." AND vendor_id =".$post['vendor_id']." AND response_by_user_id=".$get['user_id']." AND current_status = 2";
    	$update = $callConfig->executeQuery($update_query);

    	$inarray = array('company_id'=>$get['company_id'],'vendor_feedback_setup_id'=>$post['setup_id'],'vendor_id'=>$post['vendor_id'],'over_all_ratting'=>$post['overall_rating'],'response_start_date'=>date('Y-m-d'),'response_by_user_id'=>$get['user_id'],'response_by_emp_id'=>$get['emp_id'],'is_active'=>'0','current_status'=>$post['save_type'],'created_date'=>date('Y-m-d h:i:s'),'created_by'=>$get['user_id']);
    	$res = $callConfig->insertRecord('vendor_feedback_responses',$inarray);
    	$transaction_id = $res['LASTID'];
    	
    	foreach($post['response_option'] as $questionid=>$option){
    		$inarray = array('company_id'=>$get['company_id'],'vendor_id'=>$post['vendor_id'], 'feed_back_setup_id'=>$post['setup_id'],'vendor_feedback_category_id'=>$post['response_cat'][$questionid],'transaction_id'=>$transaction_id,'question_id'=>$questionid,'response_option'=>$option,'response_start_date'=>date('Y-m-d'),'response_by_user_id'=>$get['user_id'],'response_by_emp_id'=>$get['emp_id'],'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d h:i:s'),'created_by'=>$get['user_id']);
			$return = $callConfig->insertRecord('vendor_feedback_responses_details',$inarray);
		}
		return $return;
    }
    function getvendorfeedbackresponsedata($get){
		global $callConfig; 
		
		$rquery = "SELECT * FROM vendor_feedback_responses  WHERE company_id = ".$get['company_id']." AND response_by_user_id = ".$get['user_id']." AND vendor_id = ".$get['vendor_id']." AND current_status = 199 AND is_active = 0";
		$response = $callConfig->getAllRows($rquery);
		$details['responses'] = $response;
		$details['responses_query'] = $rquery;

		foreach($response as $rsp){
			$fb_ids[] = $rsp->vendor_feedback_setup_id;
			$respon_ids[] = $rsp->id;
		}

		$rdquery = "SELECT id, transaction_id, question_id, feed_back_setup_id, vendor_feedback_category_id,response_option  FROM vendor_feedback_responses_details WHERE transaction_id IN (".implode(",",$respon_ids).")";
		$alldetails = $callConfig->getAllRows($rdquery);
		foreach($alldetails as $alldetail){
			$response_details[$alldetail->transaction_id][$alldetail->vendor_feedback_category_id][$alldetail->question_id] = $alldetail;
		}
		$details['response_details'] = $response_details;
		$vquery = "SELECT id,display_name FROM vendors WHERE id=".$get['vendor_id'];
		$details['vendor'] = $callConfig->getRow($vquery);
		$vquery = "SELECT id,display_name FROM application_users WHERE id=".$get['user_id'];
		$details['user'] = $callConfig->getRow($vquery);


		$whr="company_id=".$get['company_id']." AND  id IN (".implode(",",$fb_ids).")";
		$query=$callConfig->selectQuery('vendor_feedback_setup','id, feedback_name',$whr,'','','');
		$details['vendor_feedback_setup'] = $query;

		$feedback_setup_details = $callConfig->getAllRows($query);
		foreach($feedback_setup_details as $feedback_setup){
			$feedback_set[$feedback_setup->id]=$feedback_setup;
		}
		$details['feedback_setup_details'] = $feedback_set;

		$whr="company_id=".$get['company_id']." AND  vendor_feedback_setup_id IN (".implode(",",$fb_ids).")";
		$query=$callConfig->selectQuery('vendor_feedback_categories','id, vendor_feedback_setup_id, category_name',$whr,'','','');		
		$details['vendor_feedback_categories'] = $query;
		$feedback_details = $callConfig->getAllRows($query);
		foreach($feedback_details as $fdetail){
			$fbdetails[$fdetail->vendor_feedback_setup_id][$fdetail->id] = $fdetail;
			$catids[]= $fdetail->id;
		}
		$details['feedback_details'] = $fbdetails;
		$whr="company_id=".$get['company_id']." AND  transaction_id IN (".implode(",",$catids).")";;
		$query=$callConfig->selectQuery('vendor_feedback_setup_questions','*',$whr,'','','');
		$qustion_details = $callConfig->getAllRows($query);

		$query_ans=$callConfig->selectQuery('vendor_feedback_setup_question_options','*',$whr,'','','');
		$answer_details = $callConfig->getAllRows($query_ans);

		foreach ($qustion_details as $value) {
			$disp_question[$value->transaction_id][$value->id]['question'] = $value->question;
			$disp_question[$value->transaction_id][$value->id]['options_display_type'] = $value->options_display_type;
			$disp_question[$value->transaction_id][$value->id]['require_response'] = $value->require_response;
			$disp_question[$value->transaction_id][$value->id]['allow_multiple_selections'] = $value->allow_multiple_selections;
		}
		foreach ($answer_details as $value) {
			$disp_question[$value->transaction_id][$value->question_id]['question_option'] = $value->question_option;
		}
		$details['questions'] = $disp_question;

		

		return $details;
	}
    function getvendorfeedbackresponsedataold($get){
		global $callConfig; 
		$whr="company_id=".$get['company_id']." AND is_active='0'";
		$query=$callConfig->selectQuery('vendor_feedback_setup','id, name',$whr,'','','');
		$feedback_details = $callConfig->getAllRows($query);
		foreach($feedback_details as $fdetail){
			$fbdetails[$fdetail->id] = $fdetail;
		}
		$details['feedback_details'] = $fbdetails;
		$whr="company_id=".$get['company_id'];
		$query=$callConfig->selectQuery('vendor_feedback_setup_questions','*',$whr,'','','');
		$qustion_details = $callConfig->getAllRows($query);

		$query_ans=$callConfig->selectQuery('vendor_feedback_setup_question_options','*',$whr,'','','');
		$answer_details = $callConfig->getAllRows($query_ans);

		foreach ($qustion_details as $value) {
			$disp_question[$value->id]['question'] = $value->question;
			$disp_question[$value->id]['options_display_type'] = $value->options_display_type;
			$disp_question[$value->id]['require_response'] = $value->require_response;
			$disp_question[$value->id]['allow_multiple_selections'] = $value->allow_multiple_selections;
		}
		foreach ($answer_details as $value) {
			$disp_question[$value->question_id]['question_option'] = $value->question_option;
		}
		$details['questions'] = $disp_question;

		$rquery = "SELECT vfr.id fd_id, vfr.vendor_id, vfr.over_all_ratting, vfr.response_start_date, au.display_name, au.id user_id FROM vendor_feedback_responses vfr, application_users au WHERE vfr.company_id = au.company_id AND vfr.company_id = 1 AND vfr.response_by_user_id = au.id AND vfr.vendor_id = ".$get['vendor_id']." AND vfr.is_active = 0";
		$response = $callConfig->getAllRows($rquery);
		$details['responses'] = $response;

		foreach($response as $rsp){
			$fb_ids[] = $rsp->fd_id;
		}

		$rdquery = "SELECT id, transaction_id, question_id, feed_back_setup_id, response_option  FROM vendor_feedback_responses_details WHERE transaction_id IN (".implode(",",$fb_ids).")";
		$alldetails = $callConfig->getAllRows($rdquery);
		foreach($alldetails as $alldetail){
			$response_details[$alldetail->transaction_id][$alldetail->feed_back_setup_id][$alldetail->question_id] = $alldetail;
		}
		$details['response_details'] = $response_details;
		$vquery = "SELECT id,display_name FROM vendors WHERE id=".$get['vendor_id'];
		$details['vendor'] = $callConfig->getRow($vquery);

		return $details;
	}
	function togetempvmsdetails($get){
		global $callConfig;
		$query = "SELECT 	
		emp.id as employee_id, 
        emp.emp_code,
	    emp.display_name as employee_name, 
        emp.joining_date,
        emp.email_id,
		emp.vendor_id,
		   IFNULL( (
	            SELECT	vend.display_name
	            FROM	vendors vend
	            WHERE	vend.company_id = emp.company_id
	            AND		vend.id = emp.vendor_id
	        ), 'Unassigned') vendor_name, 
			
			emp.department_id,
            (
	            SELECT	dept.department_name	
	            FROM 	departments dept
	            WHERE	dept.company_id = emp.company_id
	            AND		dept.id = emp.department_id
	        )department_name,
			emp.location_id,
            (
	            SELECT	loc.location_name	
	            FROM 	locations loc
	            WHERE	loc.company_id = emp.company_id
	            AND		loc.id = emp.location_id
	        )location_name,
			emp.vertical_id,
	         (
	            SELECT	vr.vertical_name	
	            FROM 	verticals vr
	            WHERE	vr.company_id = emp.company_id
	            AND		vr.id = emp.vertical_id
	        )vertical_name,
			
	        emp.job_title_id as job_title_id,
	        (
	            SELECT	jt.job_title
	            FROM 	job_titles jt
	            WHERE	jt.company_id = emp.company_id
	            AND		jt.id = emp.job_title_id
	        ) job_title,
            
            emp.approval_manager_id,
            (
	            SELECT	au.display_name
	            FROM 	application_users au
	            WHERE	au.company_id = emp.company_id
	            AND		au.id = emp.approval_manager_id
	        ) approval_manager,
             emp.reporting_manager_id,
            (
	            SELECT	au.display_name
	            FROM 	application_users au
	            WHERE	au.company_id = emp.company_id
	            AND		au.id = emp.reporting_manager_id
	        ) reporting_manager,
             emp.hr_buddy hr_id,
            (
	            SELECT	em.display_name
	            FROM 	employees em
	            WHERE	em.company_id = emp.company_id
	            AND		em.id = emp.hr_buddy
	        ) hr_buddy,
             emp.finance_buddy finance_id,
            (
	            SELECT	em.display_name
	            FROM 	employees em
	            WHERE	em.company_id = emp.company_id
	            AND		em.id = emp.finance_buddy
	        ) finance_buddy,
             emp.admin_buddy admin_id,
            (
	            SELECT	em.display_name
	            FROM 	employees em
	            WHERE	em.company_id = emp.company_id
	            AND		em.id = emp.admin_buddy
	        ) admin_buddy           
		FROM employees emp 
		WHERE 
			emp.id = ".$get['empid'];
		$return['emp_detail_query'] = $query;
		$return['emp_detail'] = $callConfig->getRow($query);
		$wquery = "SELECT 	
		ewo.id as work_order_id, 
        ewo.emp_id employee_id,
	    ewo.effective_from_date, 
        ewo.effective_to_date,
       	ewo.work_order_id mpc_id,
            (
	            SELECT	mc.transaction_number	
	            FROM 	manpower_contracts mc
	            WHERE	mc.company_id = ewo.company_id
	            AND		mc.id = ewo.work_order_id
	        ) mpc_transaction_number,
		ewo.vrc_id,
            (
	            SELECT	vrc.transaction_number	
	            FROM 	vendor_rate_cards vrc
	            WHERE	vrc.company_id = ewo.company_id
	            AND		vrc.id = ewo.vrc_id
	        ) vrc_transaction_number,
			ewo.work_order_detail_id mpc_item_id,
	         (
	            SELECT	mpci.item_price
	            FROM 	mpc_items mpci
	            WHERE	mpci.company_id = ewo.company_id
	            AND		mpci.id = ewo.work_order_detail_id
	        ) mpc_amount,
			
	       ewo.vrc_item_id ,
	         (
	            SELECT	vrci.item_price	
	            FROM 	vrc_items vrci
	            WHERE	vrci.company_id = ewo.company_id
	            AND		vrci.id = ewo.vrc_item_id
	        ) vrc_amount
		FROM emp_work_order ewo 
		WHERE 
		ewo.emp_id = ".$get['empid']."
	    AND is_active =0
	    ORDER BY effective_from_date DESC";
	    $return['work_order_query'] = $query;
		$return['work_order_detail'] = $callConfig->getAllRows($wquery);
		$lnquery = "SELECT ewo.id as Ln_id, ewo.emp_id employee_id, ewo.valid_from effective_from_date, ewo.valid_to effective_to_date, ewo.esgin_budget_approval_id , ( SELECT eba.transaction_number FROM esgin_budget_approvals eba WHERE eba.company_id = ewo.company_id AND eba.id = ewo.esgin_budget_approval_id ) transaction_number, ewo.eba_vendors_allocation_id, ( SELECT mpci.item_price FROM eba_vendors_allocations mpci WHERE mpci.company_id = ewo.company_id AND mpci.id = ewo.eba_vendors_allocation_id ) amount FROM esgin_employees ewo WHERE ewo.emp_id = 297 AND is_active =0 ORDER BY effective_from_date DESC";
		$return['ln_query'] = $lnquery;
		$return['ln_detail'] = $callConfig->getAllRows($lnquery);
		return $return;
	}
	function togetvendorfeedbackpopdetails($get){
		global $callConfig;
		$gencshiftkey = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE is_active=0 and company_id=".$get['company_id']." and genc_code='FEEDBACK_QTYPE'"; 
		$gencshiftkeydata = $callConfig->getAllRows($gencshiftkey);
		foreach ($gencshiftkeydata as $gvalue) {
			$gencode_detail[$gvalue->genc_detail_code] = $gvalue->genc_detail_name;
		}
		$return['gen'] = $gencode_detail;
		$return['question'] = array();
		if($get['qid'] !=""){
			$query = "SELECT vq.id, vq.question, vq.options_display_type, vqp.question_id, vqp.question_option FROM vendor_feedback_setup_questions vq, vendor_feedback_setup_question_options vqp WHERE vq.company_id = vqp.company_id AND  vq.company_id =".$get['company_id']."  AND vq.id = vqp.question_id AND vq.is_active = 0 AND vqp.is_active= vq.is_active AND vq.id = ".$get['qid'];
			$return['question'] = $callConfig->getRow($query);
		}
		return $return;
	}
	function addupdatequestionfeddback($post, $get){
		global $callConfig;
		$res['question_id'] = $post['question_id'];
		if($post['question_id']!="") {
			$updatares=$callConfig->updateRecord('vendor_feedback_setup_questions',array('is_active'=>1),'id',$post['question_id']);
			$upquery = "UPDATE vendor_feedback_setup_question_options SET is_active =1 WHERE transaction_id = ".$post['cat_id']." AND question_id =".$post['question_id'];
			$upres = $callConfig->executeQuery($upquery);
		}
			$array = array('company_id'=>$get['company_id'],'transaction_id'=> $post['cat_id'], 'question'=>$post['question'], 'options_display_type'=>$post['question_type'], 'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d h:i:s'));			
			$res=$callConfig->insertRecord('vendor_feedback_setup_questions',$array);
			$qus_id = $res['LASTID'];
			$question_option = json_encode($post['question_option'][$post['question_type']]);
			$array = array('company_id'=>$get['company_id'],'transaction_id'=> $post['cat_id'], 'question_id'=>$qus_id, 'question_option'=>$question_option, 'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d h:i:s'));			
			$res=$callConfig->insertRecord('vendor_feedback_setup_question_options',$array);
			$res['question_id'] = $qus_id;

		
		return $res;
	}
	function vendorreviewalldetails($get){
		global $callConfig;

		$list_array = array('locations'=>'location_name','vendors'=>'company','departments'=>'department_name','designations'=>'designation_name','application_roles'=>'display_name','employees'=>'display_name');
		foreach($list_array as $listkey => $list){
			$query = "SELECT id,".$list." as name FROM ".$listkey. " WHERE company_id =".$get['company_id']." AND is_active = 0";
			$return[$listkey] = $callConfig->getAllRows($query);
		}
		if($get['setup_id']!=""){
			$whr = "company_id=".$get['company_id']." AND id=".$get['setup_id']." AND is_active='0'";
			$queryemp = $callConfig->selectQuery('vendor_feedback_setup','*',$whr,'','','');
			$return['setup'] = $callConfig->getRow($queryemp);

			$whr = "company_id=".$get['company_id']." AND vendor_feedback_setup_id=".$get['setup_id']." AND is_active='0'";
			$queryemp = $callConfig->selectQuery('vendor_feedback_categories','*',$whr,'','','');
			$catdata = $callConfig->getAllRows($queryemp);
			$return['categories'] = $catdata;
			foreach($catdata as $cat){
				$cat_id[] = $cat->id;			
			}
			if(count((array)$cat_id)>0){
			$whr="company_id=".$get['company_id']." AND transaction_id IN (".implode(",",$cat_id).") AND is_active='0'";
			$query=$callConfig->selectQuery('vendor_feedback_setup_questions','*',$whr,'','','');
			$qustion_details = $callConfig->getAllRows($query);

			$query_ans=$callConfig->selectQuery('vendor_feedback_setup_question_options','*',$whr,'','','');
			$answer_details = $callConfig->getAllRows($query_ans);

			foreach ($qustion_details as $value) {
				$disp_question[$value->transaction_id][$value->id]['question'] = $value->question;
				$disp_question[$value->transaction_id][$value->id]['options_display_type'] = $value->options_display_type;
				$disp_question[$value->transaction_id][$value->id]['require_response'] = $value->require_response;
				$disp_question[$value->transaction_id][$value->id]['allow_multiple_selections'] = $value->allow_multiple_selections;
			}
			foreach ($answer_details as $value) {
				$disp_question[$value->transaction_id][$value->question_id]['question_option'] = $value->question_option;
			}
			}
			$return['questions'] = $disp_question;
		}		
		return $return;
	}
	function insertupdatevendorsetup($post, $get){
		global $callConfig;
		if($post['rid']!=""){
			$start_date = date('Y-m-d', strtotime(str_replace('/', '-', $post['start_date'])));
			$end_date =  date('Y-m-d', strtotime(str_replace('/', '-', $post['to_date'])));
			$insert_array = array('company_id'=>$get['company_id'],'feedback_name'=>$post['plan_name'],'version_no'=>$post['version_no'],'allow_multiple_times'=>$post['multiple_time'],'description'=>$post['note'],'start_date'=>$start_date,'end_date'=>$end_date, 'applicable_locations'=>json_encode($post['applicable_locations']), 'applicable_vendors'=>json_encode($post['applicable_vendors']), 'provide_locations'=>json_encode($post['provide_locations']), 'provide_departments'=>json_encode($post['provide_departments']), 'provide_designations'=>json_encode($post['provide_designations']), 'provide_roles'=>json_encode($post['provide_roles']), 'provide_employees'=>json_encode($post['provide_employees']), 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$returndata = $callConfig->updateRecord('vendor_feedback_setup',$insert_array,'id',$post['rid']);
			$return['res'] = array('status'=>'sucess','LASTID'=>0);
		}else{
			 
			$start_date = date('Y-m-d', strtotime(str_replace('/', '-', $post['start_date'])));
			$end_date =  date('Y-m-d', strtotime(str_replace('/', '-', $post['to_date'])));
			$insert_array = array('company_id'=>$get['company_id'],'feedback_name'=>$post['plan_name'],'version_no'=>$post['version_no'],'allow_multiple_times'=>$post['multiple_time'],'description'=>$post['note'],'start_date'=>$start_date,'end_date'=>$end_date, 'applicable_locations'=>json_encode($post['applicable_locations']), 'applicable_vendors'=>json_encode($post['applicable_vendors']), 'provide_locations'=>json_encode($post['provide_locations']), 'provide_departments'=>json_encode($post['provide_departments']), 'provide_designations'=>json_encode($post['provide_designations']), 'provide_roles'=>json_encode($post['provide_roles']), 'provide_employees'=>json_encode($post['provide_employees']), 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$returndata = $callConfig->insertRecord('vendor_feedback_setup',$insert_array);
			$return['res'] = array('status'=>'sucess','LASTID'=>$returndata['LASTID']);
		}
		$return['returndata'] = $returndata;
		return $return;
	}
	function CreateNewversionvendorsetup($get){
		global $callConfig;

		$query = "SELECT id, version_no, ref_id, parent_ref_id, current_status FROM vendor_feedback_setup WHERE id=".$get['setup_id'];
		$data = $callConfig->getRow($query);

		$allqueries[] = $query;
		$parent_ref_id =  $get['setup_id'];

		if($data->current_status ==52){
			$parent_ref_id =  $get['setup_id'];
		}else if($data->parent_ref_id !="" ){
			$parent_ref_id = $data->parent_ref_id;
		}
		//echo $parent_ref_id;  exit;
		$query = "SELECT count(id) as cc FROM vendor_feedback_setup WHERE parent_ref_id=".$parent_ref_id;
		
		$ccdata = $callConfig->getRow($query);
		if($data->current_status!=52){
			$version_val = $ccdata->cc+2;
		}else{
			$version_val = $ccdata->cc+1;
		}
		
		$versions = explode('.',$data->version_no);
		$version = $versions['0'].".".$version_val;
		//echo $parent_ref_id."/".$version; exit;
		$query = "INSERT INTO `vendor_feedback_setup`( `company_id`, `feedback_name`, `version_no`, `description`, `start_date`, `end_date`, `allow_multiple_times`, `applicable_locations`, `applicable_vendors`, `provide_locations`, `provide_departments`, `provide_designations`, `provide_roles`, `provide_employees`, `publish_status`, `publish_by`, `publish_date`, `ref_id`, `parent_ref_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `company_id`, `feedback_name`, ".$version.", `description`, `start_date`, `end_date`, `allow_multiple_times`, `applicable_locations`, `applicable_vendors`, `provide_locations`, `provide_departments`, `provide_designations`, `provide_roles`, `provide_employees`, `publish_status`, `publish_by`, `publish_date`, ".$get['setup_id']." , ".$parent_ref_id.", `is_active`, '0', `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM vendor_feedback_setup WHERE  id=".$get['setup_id'];
		$allqueries[] = $query;
		$main = $callConfig->executeQuery($query);
		$new_set_up_id =  $main['LASTID'];
		$query = "SELECT id,vendor_feedback_setup_id FROM vendor_feedback_categories WHERE vendor_feedback_setup_id=".$get['setup_id']." AND is_active=0";
		$allqueries[] = $query;
		$catdata = $callConfig->getAllRows($query);
		foreach($catdata as $cat){
			$query = "INSERT INTO `vendor_feedback_categories`( `company_id`, `vendor_feedback_setup_id`, `category_name`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `company_id`, ".$new_set_up_id." , `category_name`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM vendor_feedback_categories WHERE id =".$cat->id;
			$allqueries[] = $query;
			$catrun = $callConfig->executeQuery($query);
			$new_cat_id =  $catrun['LASTID'];
			$query = "SELECT id,transaction_id FROM vendor_feedback_setup_questions WHERE transaction_id=".$cat->id." AND is_active=0";
			$allqueries[] = $query;
			$qusdata = $callConfig->getAllRows($query);
			foreach($qusdata as $qus){
				$query = "INSERT INTO `vendor_feedback_setup_questions`( `company_id`, `transaction_id`, `question`, `options_display_type`, `require_response`, `allow_multiple_selections`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `company_id`, ".$new_cat_id." , `question`, `options_display_type`, `require_response`, `allow_multiple_selections`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM vendor_feedback_setup_questions WHERE  id =".$qus->id;
				$querun = $callConfig->executeQuery($query);
				$allqueries[] = $query;
				$new_ques_id =  $querun['LASTID'];
				$query = "INSERT INTO `vendor_feedback_setup_question_options`( `company_id`, `transaction_id`, `question_id`, `question_option`, `question_option_icon`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`)  SELECT  `company_id`, ".$new_cat_id.", ".$new_ques_id.", `question_option`, `question_option_icon`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM vendor_feedback_setup_question_options WHERE  question_id =".$qus->id;
				$allqueries[] = $query;
				$querun = $callConfig->executeQuery($query);
			}
		}
		//return array('main'=>$main, 'allqueries'=>$allqueries);
		return array('main'=>$main);
	}
	function togetvendorfeedbacksetupdetails($get){
		global $callConfig;
		$vendor_id = $get['vendor_id'];
		$user_id = $get['user_id'];
		$now = date('Y-m-d');
		$emp_id = $get['emp_id'];
		$query = "SELECT * FROM vendor_feedback_setup WHERE company_id = ".$get['company_id']." AND current_status = 52 AND start_date <='".$now."' AND end_date >= '".$now."' AND is_active =0";
		$lists = $callConfig->getAllRows($query);
		$emp_query = "SELECT * FROM employees WHERE id=".$emp_id;
		$emp_data = $callConfig->getRow($emp_query);
		$view = 0; $list_setup = array();
		foreach($lists as $list){
			//echo "<pre>"; print_r($list);
			$applicable_locations = json_decode($list->applicable_locations,1);			
			if(in_array('0',(array)$applicable_locations)){
				$view = 1;
			}else{
				$applicable_vendors = json_decode($list->applicable_vendors,1);
				if(!in_array('0',(array)$applicable_vendors)){
					if(in_array($vendor_id,(array)$applicable_vendors)){
						$view = 1;
					}
				}else{
					$query = "SELECT id, display_name FROM vendors WHERE location_id IN (".implode(",",$applicable_locations).") AND id =".$vendor_id;
					$qdata = $callConfig->getRow($query);
					if($qdata->id!=""){
						$view = 1;
					}
				}
			}
			if($view==1){
				$provide_locations = json_decode($list->provide_locations,1);
				$provide_departments = json_decode($list->provide_departments,1);
				$provide_designations = json_decode($list->provide_designations,1);
				$provide_roles = json_decode($list->provide_roles,1);
				$provide_employees = json_decode($list->provide_employees,1);	
				

				/*-----------------Data--------------*/
				if(in_array('0',(array)$provide_roles)){					
					if(in_array('0',(array)$provide_roles)){
						$list_setup[] = $list;
					}else{

						if(in_array($emp_data->location_id,(array)$provide_locations) && in_array('0',(array)$provide_departments)){
							$list_setup[] = $list;
						}else{
							if(in_array($emp_data->department_id,(array)$provide_departments) && in_array('0',(array)$provide_designations)){
								$list_setup[] = $list;
							}else{
								if(in_array($emp_data->designation_id,(array)$provide_designations) && in_array('0',(array)$provide_employees)){
									$list_setup[] = $list;
								}else{
									if(in_array($emp_data->designation_id,(array)$provide_employees)){
										$list_setup[] = $list;
									}
								}
							}
						}
					}
				}else{
					$query = "SELECT id, user_id FROM application_user_module_roles WHERE role_id IN (".implode(",",$provide_roles).") AND user_id =".$user_id;
					$qdata = $callConfig->getAllRows($query);
					if(count((array)$qdata)>0){
						$list_setup[] = $list;
					}
				}
			}
		}
		$return['get'] = $get;
		$return['list'] = $list_setup;
		$query = "SELECT vendor_feedback_setup_id, over_all_ratting, current_status FROM vendor_feedback_responses WHERE company_id =".$get['company_id']." AND response_by_user_id =".$user_id." AND vendor_id = ".$vendor_id." AND is_active =0 GROUP BY vendor_feedback_setup_id ORDER BY id ASC";
		$return['ratting_query'] = $query;
		$ratt = $callConfig->getAllRows($query);
		foreach($ratt as $rat){
			$ratting[$rat->vendor_feedback_setup_id] = $rat->over_all_ratting;
			$reviewstatus[$rat->vendor_feedback_setup_id] = $rat->current_status;
		}
		$return['ratting'] = $ratting;
		$return['reviewstatus'] = $reviewstatus;
		return $return;
	}
	function 	old($get){
		global $callConfig;
 		$return['get'] = $get;
 		$whr=  "";
 		if($get['vendor_id']!='0'){
	 		$whr.=  " AND  customer_id ='".$get['vendor_id']."'";
	 	}
	 	if($get['job_title_id']!='0'){
	 		$whr.=  " AND  item_id ='".$get['job_title_id']."'";
	 	}
 		$data_query = "SELECT	item_id job_title_id,
		IFNULL(job_title, 'Not Defined') job_title,
		customer_id vendor_id,
		IFNULL(vendor_name, 'Not Defined') vendor_name,
        min_exp, 
        max_exp, 
        company_id,
        transaction_id,
        item_price	
		FROM (
    	SELECT  vrcitm.item_id,
                (
                    SELECT  jt.job_title 
                    FROM    job_titles jt 
                    WHERE   jt.company_id = vrc.company_id
                    AND     jt.id = vrcitm.item_id
                ) job_title,
                (
                SELECT  vend.company 
                FROM    vendors vend 
                WHERE   vend.company_id = vrc.company_id
                AND     vend.id = vrc.customer_id
            	) vendor_name,
    			vrc.customer_id,
            	vrcitm.min_exp, 
                vrcitm.max_exp, 
                vrcitm.item_price, 
                vrcitm.company_id,
                vrcitm.transaction_id
		    FROM    vendor_rate_cards vrc, `vrc_items` vrcitm
		    WHERE   vrc.company_id = vrcitm.company_id
		    AND 	vrc.company_id = ".$get['company_id']."
		    AND     vrc. id = vrcitm.transaction_id
		    AND     vrcitm.min_exp IS NOT NULL 
		    AND     vrc.is_active = 0
		    AND     vrcitm.is_active = 0
		    AND     vrc.effective_from_date >= '".$get['fromdate']."'
		    AND     vrc.effective_to_date <=  '".$get['todate']."'
		   
		)a 
		WHERE
			company_id = '".$get['company_id']."' ".$whr;
	 	$return['data_query'] = $data_query;
 		$fulldata = $callConfig->getAllRows($data_query);
 		$return['fulldata'] = $fulldata;
 		foreach($fulldata as $full){
 			$exp = $full->min_exp." - ".$full->max_exp;
 			if( $full->vendor_name !="Not Defined"){
 				$vendor[$full->vendor_id] = $full->vendor_name;
  			}
 			$job_title[$full->job_title_id] = $full->job_title;
 			$list_exp[$full->job_title_id][$exp] = $exp;
 			$data[$full->job_title_id][$exp][$full->vendor_id]=$full;
 			if(!isset($min[$full->job_title_id][$exp])){
 				$min[$full->job_title_id][$exp] = 0;
 				$max[$full->job_title_id][$exp] = 0;

 			}
 			if($min[$full->job_title_id][$exp]==0 || $min[$full->job_title_id][$exp] > $full->item_price ){
 					$min[$full->job_title_id][$exp] = $full->item_price;
 			}
 			if($max[$full->job_title_id][$exp]==0 || $max[$full->job_title_id][$exp] < $full->item_price ){
 					$max[$full->job_title_id][$exp] = $full->item_price;
 			}
 			$ave[$full->job_title_id][$exp][] = $full->item_price;
 		}
 		$return['min'] = $min;
 		$return['max'] = $max;
 		$return['ave'] = $ave;
 		//$return['vendor'] = $vendor;
 		//$return['job_title'] = $job_title;
 		$return['data'] = $data;
 		$jobtitle_query = "SELECT id,job_title FROM job_titles WHERE company_id = ".$get['company_id']." AND is_active = 0";
 		$return['jobtitle_query'] = $jobtitle_query;
 		$job_data = $callConfig->getAllRows($jobtitle_query);
 		foreach($job_data as $jobdata){
 			$job[$jobdata->id] = $jobdata->job_title;
 		}
	 	$return['job_title'] = $job;

	 	$ven_query = "SELECT id, display_name, location_id  FROM vendors WHERE company_id = ".$get['company_id']."  AND is_active = 0";
 		$return['ven_query'] = $ven_query;
 		$ven_data = $callConfig->getAllRows($ven_query);
 		foreach($ven_data as $vendata){
 			$ven[$vendata->id] = $vendata->display_name;
 		}
	 	$return['vendor'] = $ven;
 		return $return;
	}
	function getmonthlytimesheetentries($get){
     	global $callConfig;
     	//echo "Ravi"; print_r($get); exit;
     	$ymdate = $get['year']."-".$get['month']."-";
     	$valid_from = $ymdate."01";
     	$valid_from = date('Y-m-01', strtotime($valid_from));
     	$valid_to = date('Y-m-t', strtotime($valid_from));
     	//echo "Ravi"; print_r($get); exit;	
     	$atquery="SELECT *  FROM attendance_settings WHERE company_id='".$get['company_id']."' AND is_active=0 LIMIT 0,1";
    	$attendance  = $callConfig->getRow($atquery);

    	//$query2 = "SELECT * FROM emp_work_order WHERE  emp_id =".$get['empid']." AND effective_to_date >'".$valid_from."' AND is_active = 0";
    	$workordersql = "SELECT ewo.id,mpc.transaction_number  
		FROM `emp_work_order` ewo, manpower_contracts mpc  
		WHERE   ewo.company_id='".$get['company_id']."'
		AND ewo.company_id = mpc.company_id
		AND ewo.work_order_id = mpc.id
		AND ewo.emp_id='".$get['empid']."'
		AND ewo.work_order_detail_id !='' 
		AND ewo.work_order_id !='' 
		AND ewo.vrc_id !='' 
		AND ewo.vrc_item_id !=''
		AND ewo.is_active = 0 
		AND ewo.effective_from_date <= '".$valid_from."'
		AND ewo.effective_to_date >= '".$valid_to."'";
    	$details['workorder_query'] = $workordersql;
		$workorder = $callConfig->getAllRows($workordersql);    	

     	//$hquery="SELECT *  FROM holidays WHERE company_id='".$get['company_id']."' AND holiday_date LIKE '".$ymdate."%' AND is_active=0";
     	$hquery = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get['company_id']." AND holiday_date >= '".$valid_from."' AND holiday_date <= '".$valid_to."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$get['empid'].") AND is_active =0";
    	$holidays  = $callConfig->getAllRows($hquery);
    	foreach((array)$holidays as $holida){
    		$workholiday[$holida->holiday_date] = $holida;
    		$listholidays[] = $holida->holiday_date;
    	}
    	//echo "Ravi w"; print_r($get); exit;
    	$equery="SELECT *  FROM employee_leaves WHERE company_id='".$get['company_id']."' AND leave_from >= '".$valid_from."' AND leave_to <= '".$valid_to."' AND is_active=0";
    	$leaves  = $callConfig->getAllRows($equery);
    	foreach((array)$leaves as $leav){
    		$workleave_from[$leav->leave_from] = $leav;
    		$ssdate = $leav->leave_from;
    		$eedate	= $leav->leave_to;
    		for($i=1; $i<=$leav->leave_days; $i++){
    			$leave[] = date('Y-m-d',strtotime($ssdate));
    			$ssdate = date('Y-m-d', strtotime("+1 day", strtotime($ssdate)));
    		} 		
    	}
    	//echo "Ravi Q"; print_r($get); exit;
     	$quer_5="SELECT *  FROM ".TBL_PROJECT_TIMESHEETS." WHERE company_id='".$get['company_id']."' AND emp_id=".$get['empid']." AND timesheet_date LIKE '".$ymdate."%' AND is_active=0";
    	$empdatas  = $callConfig->getAllRows($quer_5);
    	foreach((array)$empdatas as $empda){
    		$workdata[$empda->timesheet_date] = $empda;
    		$timesheet[] = $empda->timesheet_date;
    	}
    	//echo "Ravi E "; print_r($get); exit;
    	$details['attendance'] = $attendance;
		$details['holidays'] = $workholiday;
    	$details['listholidays'] = $listholidays;
    	$details['workleaves'] = $workleave_from;
    	$details['leave'] = $leave;
    	$details['leave_query'] = $equery;
    	$details['timesheet'] = $workdata;
    	$details['timesheet_date'] = $timesheet;
    	$details['workorder'] = $workorder;
    	$details['no_days'] = date('t', strtotime($valid_to));
    	$details['no_leaves'] = count((array)$leave);
    	$get['employee_id'] = $get['empid'];
    	$get['date'] = $valid_from;
    	//print_r($get); exit;
    	$all_data = $this->togetemployeemonthattendancedetails($get);
    	$details['all_data'] = $all_data;
    	return $details;
    }
    function addupdateprojecttimesheetmontly($post, $get){
    	global $callConfig;
    	//print_r($get); print_r($post); exit;

    	$bilableamtcalculation = $this->getBillableAmountTimesheetcalculationday($post, $get);
    	//return $post;
    	$mpc_rate_per_hrs = round($bilableamtcalculation['mpc_rate_per_hrs'],2);
        $vrc_rate_per_hrs = round($bilableamtcalculation['vrc_rate_per_hrs'],2);
        $esignid = $bilableamtcalculation['esigndata']->esgin_budget_approval_id;
        $vrcid = $bilableamtcalculation['esigndata']->vrc_id;
    	$query = "SELECT * FROM emp_work_order WHERE id=".$post['work_order'];
    	$datas = $callConfig->getRow($query);
    	$data = (array)$datas;
    	$hrs = '0'; $nonhrs ='0';
    	if($post['type']=='p'){
    		$hrs = 8;
    	}elseif($post['type']=='z'){
    		$hrs = 4;
    	}elseif($post['type']=='a'){
    		$hrs = '0';
    	}
    	if($bilableamtcalculation['billable_type']!=0){
    			$nonhrs = $hrs;
    			$hrs = 0;
    	}
    	//return $post;
    	$upquery = "UPDATE project_timesheets SET is_active = NULL WHERE company_id =".$get['company_id']." AND timesheet_date='".$post['date']."' AND emp_id =".$post['emp_id']." AND is_active =0";
    	$res = $callConfig->executeQuery($upquery);
    	$insert_array = array('company_id'=>$get['company_id'],'timesheet_date'=>$post['date'],'task_name'=>'','work_order'=>$data['work_order_id'],'vendor_rate_card_id'=>$data['vrc_id'],'rate_per_hrs'=>$mpc_rate_per_hrs,'bill_amount'=>$mpc_rate_per_hrs*$hrs,'vrc_billable_amount'=>$vrc_rate_per_hrs*$hrs,'billable_hrs'=>$hrs,'nonbillable_hrs'=>$nonhrs,'emp_id'=>$post['emp_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s')); 
    	 //print_r($insert_array); exit;
    	$exe = $callConfig->insertRecord('project_timesheets', $insert_array);
    	$insert_array['employee_id'] = $post['emp_id'];
    	$insert_array['date'] = $post['date'];
    	//return $post;
    	$redata = $this->togetemployeemonthattendancedetails($insert_array);
    	return $redata;
    }
    function togetemployeemonthattendancedetails($get){
    	global $callConfig;

    	$emp_id = $get['employee_id'];
    	if(!isset($get['date'])){
    		$get['date'] = $get['month']."-".$get['year']."-01";
    	}
    	$fromdate = date('Y-m-01', strtotime($get['date']));
    	$todate = date('Y-m-t', strtotime($fromdate));
    	//echo "<pre>".$fromdate."---".$todate; print_r($get); exit;
    	$query = "SELECT working_days FROM attendance_settings WHERE company_id =".$get['company_id']." AND is_active =0";
    	$attendances = $callConfig->getRow($query);

    	$query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get['company_id']." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
    	$listholidays = $callConfig->getAllRows($query);
    	foreach((array)$listholidays as $listholiday){
    		$holidays[] = $listholiday->holiday_date;
    	}
    	//echo $query; print_r($holidays); exit;
    	$query = "SELECT leave_from,leave_to FROM employee_leaves WHERE company_id =".$get['company_id']." AND employee_id = ".$emp_id." AND  is_active =0";
    	$leaves = $callConfig->getRow($query);

    	foreach((array)$leaves as $leave){    		
    		for ($d = strtotime($start_date); $d <= strtotime($end_date); $d = strtotime('+1 day', $i)) {
    			$listleavedays[] = date('Y-m-d',$d);    			
    		}    		
    	}
		
		$woklog_query = "SELECT timesheet_date, billable_hrs FROM  project_timesheets WHERE company_id =".$get['company_id']." AND emp_id = ".$emp_id."  AND timesheet_date >= '".$fromdate."' AND timesheet_date <= '".$todate."'  AND  is_active =0";
    	$work_log = $callConfig->getAllRows($woklog_query);

    	foreach((array)$work_log as $log){
    		$att[]=$log->timesheet_date;
    		$attval[$log->timesheet_date]=$log->billable_hrs;
    	}    	

    	$working_days = json_decode($attendances->working_days,1);
    	//echo "<pre>".$fromdate."---".$todate; print_r($attendances);print_r($weekdays); print_r($get); exit;
    	$datediff = strtotime($todate) - strtotime($fromdate);
		$datediff = floor($datediff/(60*60*24));
		$absent =0; $per=0;
		//echo "<pre>".$fromdate."---".$todate."////".$datediff; print_r($working_days); exit;
		// print_r($working_days);
	    	for($i = 0; $i < $datediff + 1; $i++){
				$valda =  date("Y-m-d", strtotime($fromdate . ' + ' . $i . 'day'));
				$valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
				//echo "--".$valday."<br>";
				if(in_array($valday, (array)$working_days)){						
						if(!in_array($valda, (array)$holidays)){ 
							if(!in_array($valda, (array)$listleavedays)){
								if(isset($attval[$valda])){
									if($attval[$valda]==8){
										$per= $per+1;
										$pre_days[] = $valda;
									}else if($attval[$valda]==4){
										$per= $per+0.5;
										$absent = $absent+0.5;
										$pre_days[] = $valda;
									}else{
										$absent = $absent+1;
										$absent_days[] = $valda;
									}
								}
							}else{
								$viewleave[] = $valda;
							}
						}else{
							$view_holiday[]=$valda;
						}
				}else{
					$weekoff[] = $valda;
				}

			}
		$totaldays = $datediff + 1;
		$return['total_days'] = $totaldays;
		$return['weekoff_days'] = count((array)$weekoff);
		$return['holi_days'] = count((array)$view_holiday);
		$return['leave_days'] = count((array)$viewleave);
		$return['work_days'] = $totaldays-$return['weekoff_days']-$return['holi_days'];
		$return['present_days'] = $per;
		$return['absent_day'] = $absent;
		//echo "<pre>"; print_r($weekoff);print_r($pre_days);print_r($absent_days);
		return $return;
    }
    function getBillableAmountTimesheetcalculationday($post, $get){
        global $callConfig;
        //echo "New"; print_r($post); exit;
        /*$query ="SELECT ewo.work_order_id, ewo.vrc_id, ese.esgin_budget_approval_id,ewo.work_order_detail_id FROM `emp_work_order` ewo, esgin_employees ese WHERE ewo.company_id = ese.company_id AND ewo.emp_id = ese.emp_id AND ewo.company_id = '".$get['company_id']."' AND ewo.emp_id = '".$post['emp_id']."' AND ewo.is_active = ese.is_active AND ese.is_active = 0 and ewo.work_order_id = '".$post['work_order_id']."'";
        $esigndetails = $callConfig->getRow($query);
        $testarrau['esigndata'] = $esigndetails*/;

         $post['startingdate'] = date('Y-m-01',strtotime($post['date'])); 
         $post['endingdate'] = date('Y-m-t',strtotime($post['date']));;	
        $mpcitem ="SELECT mi.purchase_uom,mi.item_price as item_amount,vi.allow_overtime ,vi.max_leave,vi.purchase_uom as vrci_purchase_uom,vi.item_price as vrci_item_price,ewo.vrc_item_id FROM `emp_work_order` ewo left JOIN mpc_items mi ON ewo.work_order_detail_id = mi.id LEFT join vrc_items vi ON ewo.vrc_item_id = vi.id WHERE ewo.id = '".$post['work_order']."' AND ewo.emp_id = '".$post['emp_id']."' AND ewo.is_active = '0' AND ewo.company_id='".$get['company_id']."'";
        $mpcitemddetails = $callConfig->getRow($mpcitem);
        $testarrau['mpcitemddetails_query'] = $mpcitem;
        $testarrau['mpcitemddetails'] = $mpcitemddetails;
        //echo "New"; print_r($testarrau); exit;
		$employeesql ="SELECT billable,location_id FROM employees where company_id = '".$get['company_id']."' AND id = '".$post['emp_id']."' AND is_active = 0";
		$employeerow = $callConfig->getRow($employeesql);
        
		$holidays = "SELECT holiday_date FROM holidays WHERE is_active=0 and company_id=".$get['company_id']." AND location_id= '".$employeerow->location_id."'";
        $holidaysdata = $callConfig->getAllRows($holidays);
        foreach ((array)$holidaysdata as $hkey=>$hvalue) {
        		$holidaysdatas[] = $hvalue->holiday_date;
        }

        $attendancewhurl1 = "SELECT id,minimum_hours_required,working_days FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
        $attendance_settings = $callConfig->getRow($attendancewhurl1);
        $testarrau['attendance_settings'] = $attendance_settings;
        $minimum_hours = explode(':',$attendance_settings->minimum_hours_required);
        $minimum_hours_required = $minimum_hours[0].'.'.$minimum_hours[1];

        $total_working_day_js = json_decode($attendance_settings->working_days,1);
        $total_working_day = count((array)$total_working_day_js);
        $totaldays = $total_working_day;

        $working_days = json_decode($attendance_settings->working_days,1);
        $leavecnt = 0;
        $leavesqql = "SELECT employee_id, '".$post['startingdate']."' config_from_date, '".$post['endingdate']."' config_to_date, leave_from_date, leave_to_date, CASE WHEN ((leave_from_date between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (leave_to_date - Leave_from_date)+1 WHEN ((leave_from_date between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date not between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (date('".$post['endingdate']."') - Leave_from_date)+1 WHEN ((leave_from_date not between '".$post['startingdate']."' and '".$post['endingdate']."') AND (leave_to_date between '".$post['startingdate']."' and '".$post['endingdate']."')) THEN (leave_to_date - date('".$post['startingdate']."'))+1 END leave_days from ( SELECT employee_id, leave_from AS leave_from_date, leave_to AS leave_to_date, company_id FROM `employee_leaves` where (`leave_from` >= '".$post['startingdate']."' and leave_from <= '".$post['endingdate']."') OR (`leave_to` >= '".$post['startingdate']."' and leave_to <= '".$post['endingdate']."') AND current_status = '".ST_APPROVED."' AND employee_id = '".$post['timesheetdata']['0']['emp_id']."' ) a";
         $leavedatas = $callConfig->getAllRows($leavesqql);

            	foreach ((array)$leavedatas as $leaverow) {
            		$leavecnt += $leaverow->leave_days;
            	}
            	$testarrau['startdate'] = $post['startingdate'];
            	$testarrau['enddate'] = $post['endingdate'];
            	//$testarrau['enddate']  = $leavesqql;
            	if(TIMESHEET_RATE_CALCULATION_TYPE == 1){
            		$totaldays=((strtotime($post['endingdate']) - strtotime($post['startingdate'])) / (60 * 60 * 24))+1;
            	}else{
            		$workday = 0;
            		$strtsate = $post['startingdate'];
            		$enddate =$post['endingdate'];
            		$dateFrom = new DateTime($strtsate);
            		$dateTo = new DateTime($enddate);
            		$datenames = [];
            		$calculationdays =0;
            		$maxleaveused = $mpcitemddetails->max_leave;
            		while($dateFrom <= $dateTo){
            			$calculationdays++; 
            			if(in_array($dateFrom->format('l'),(array)$working_days) AND !in_array($dateFrom->format('Y-m-d'), (array)$holidaysdatas)){
            				$testarrau['working_dates'][] = $dateFrom->format('Y-m-d');
							//$testarrau['days'][] = $dateFrom->format('l');
            				$workday++;
            			}
            			$dateFrom->modify('+1 days');
            		}
            		$lev = $leavecnt;
            		$maxlim = round($mpcitemddetails->max_leave);
            		$testarrau['maxleave'] = $maxlim;
            		$testarrau['totalleave'] = $lev;
            		$leavedays = 0;
            		if($maxlim <= $lev){  $leavedays = $lev-$maxlim;  $workday = $workday-$leavedays; }
            		$totaldays = $workday;	
            	}

    	$testarrau['mpc_rate_per_hrs'] = $this->ratePerHrs($mpcitemddetails->purchase_uom ,$mpcitemddetails->item_amount,$total_working_day,$minimum_hours_required,$totaldays);
    	$testarrau['vrc_rate_per_hrs'] = $this->ratePerHrs($mpcitemddetails->vrci_purchase_uom ,$mpcitemddetails->vrci_item_price,$total_working_day,$minimum_hours_required,$totaldays);
    	
    	$testarrau['totaldays'] = $totaldays;
		$testarrau['minimum_hours_required'] = $minimum_hours_required;
    	$testarrau['calculationdays'] = $calculationdays;
    	$testarrau['billable_type'] = $employeerow->billable;
    	//echo "ayy"; print_r($testarrau); exit;
    	return $testarrau;
    }
    function getmilestoneAssignEmployeeListPopup($data,$get){
		global $callConfig;
		$data = $data['where'];
		$condition = "";
		if($data['department_id']!=""){  $condition .= "AND department_id = '".$data['department_id']."'"; }
		if($data['designation_id']!=""){ $condition .=  " AND designation_id = '".$data['designation_id']."'";  }
		if($data['location_id']!=""){ $condition .=  " AND location_id = '".$data['location_id']."'"; }
		if($data['vendor_id']!=""){ $condition .=  " AND vendor_id = '".$data['vendor_id']."'"; }
		if($data['cost_center_id']!=""){ $condition .=  " AND cost_center_id = '".$data['cost_center_id']."'"; }
		if($data['emp_name_code']!=""){ $condition .=  " AND  (display_name like '%".$data['emp_name_code']."%' OR  emp_code like '%".$data['emp_name_code']."%') "; }
		$query2 = "SELECT * FROM employees WHERE  company_id = '".$data['company_id']."'".$condition." AND  is_active = 0";
		$datas = $callConfig->getAllRows($query2);
		$returnarray['emp_list'] = $datas;
		
		$dep = "SELECT id, department_name FROM departments WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$depdetails = $callConfig->getAllRows($dep);
		$returnarray['department'] = $depdetails;
		
		$designation = "SELECT id, designation_name FROM designations WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$designationdeatils = $callConfig->getAllRows($designation);
		$returnarray['designation'] = $designationdeatils;
		
		$location_name = "SELECT id, location_name FROM locations WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$location_namedetails = $callConfig->getAllRows($location_name);
		$returnarray['locations'] = $location_namedetails;
		
		$vendors = "SELECT id, company, display_name FROM vendors WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$vendorsdetails = $callConfig->getAllRows($vendors);
		$returnarray['vendors'] = $vendorsdetails;
		
		$cost_centers = "SELECT id, cost_center_name FROM cost_centers WHERE is_active=0 AND company_id = '".$data['company_id']."'";
		$cost_centersdetails = $callConfig->getAllRows($cost_centers);
		$returnarray['cost_centers'] = $cost_centersdetails;

		$query = "SELECT emp_id FROM emp_assign_milestone WHERE company_id =".$get['company_id']." AND transaction_id =".$get['transaction_id']." AND ms_item_id =".$get['item_id']."  AND is_active = 0";
		$emplist = $callConfig->getAllRows($query);
		$emps = array();
		if(count((array)$emplist)>0){
			foreach($emplist as $empli){
				$emps[]=$empli->emp_id;
			}
		}
		$returnarray['emp_ids'] =$emps;
		$returnarray['emp_query'] =$query2;
		return $returnarray;
	}
	function addupdateempassignmilestone($get){
		global $callConfig;
		//echo "haiii"; print_r($get);
		$query = "SELECT id FROM emp_assign_milestone WHERE company_id =".$get['company_id']." AND transaction_id =".$get['transaction_id']." AND ms_item_id =".$get['item_id']." AND emp_id =".$get['empid']." AND is_active = 0";
		$data = $callConfig->getRow($query);
		$res['status'] = "sucess";
		if($data->id !=""){
			$update_query = "UPDATE emp_assign_milestone SET is_active = NULL WHERE company_id =".$get['company_id']." AND transaction_id =".$get['transaction_id']." AND ms_item_id =".$get['item_id']." AND emp_id =".$get['empid']." AND is_active = 0";
			$rr = $callConfig->executeQuery($update_query);
			$res['check'] = "1";
		}else{
			$insert_array = array('company_id'=>$get['company_id'],'transaction_id'=>$get['transaction_id'],'ms_item_id'=>$get['item_id'],'emp_id'=>$get['empid'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
			$rr = $callConfig->insertRecord('emp_assign_milestone', $insert_array);
			$res['check'] = "0";
		}
		return $res;
	}
	function insertvmsleaverequest($data){
		global $callConfig;
		$returnarray = array();
		$returnarray['post'] = $data;
		$rid = $data['rid'];
		$userid = $data['user_id'];
		unset($data['rid']);unset($data['user_id']);
		if($rid!=""){
			$emp_leavesql = "select leave_days,employee_id,leave_type_id From employee_leaves WHERE company_id = '".$data['company_id']."' AND is_active = 0 AND id = '".$rid."'";
			$emp_leaveres = $callConfig->getRow($emp_leavesql);
			$returnarray['emp_leaveres'] = $emp_leavesql;
		}
		$newdata = $data;
		$newdata['emp_id'] = $data['employee_id'];
		//$newdata['leavetype'] = $data['leave_type_id'];
		$newdata['rid'] = $rid;
		
			/*$data['unpaid_days'] = $checkleave['unpaid_days'];
			$data['weekend_leave_consume'] = $checkleave['weeckendandnumberdate'];
			$data['holiday_leave_consume'] = $checkleave['holidaysnumberdate'];*/
			if($rid!=""){
				$data['created_date'] = date('Y-m-d H:i:s');
				$data['created_by'] = $userid;
				$res=$callConfig->updateRecord('employee_leaves',$data,'id',$rid);
				$res['LASTID'] = $rid;
			}else{	
				$data['updated_date'] = date('Y-m-d H:i:s');
				$data['updated_by'] = $userid;
				$res=$callConfig->insertRecord('employee_leaves',$data);
				//$this->checkWorkFlowCriteria(array('tablename'=>'employee_leaves','company_id'=>$data['company_id'],'rid'=>$res['LASTID'])); //check workflow criteria
			}
			$returnarray['leaveinsert'] = $res;	 
		
		
		return $returnarray;
	}
	function admindashboarddetails_newdashboard($data){
		global $callConfig;	
		$startdate = date('Y-m-d',strtotime('first day of this month', time()));
		$enddate =  date('Y-m-d',strtotime('last day of this month', time()));
		$prevstartdate = date('Y-m-d',strtotime('first day of previous month'));
		$prevenddate =  date('Y-m-d',strtotime('last day of previous month'));
		$loccondition = "";
		//if($data['location_id']!="" && $data['location_id']!="0"){
		//	$loccondition = "  OR (emp.location_id = ".$data['location_id_value'].")";
		//}
		$lastdate = date("Y-m-t"); 
		$prevmonthfdate = date("Y-m-01", strtotime( date( 'Y-m-01' )." -1 months"));
		$emp_query = "SELECT emp.id, emp.emp_code, emp.email_id, emp.image, emp.display_name, emp.designation_id, emp.location_id, ( SELECT des.designation_name FROM designations des WHERE des.company_id = emp.company_id AND des.id = emp.designation_id )designation_name, ( SELECT au.display_name FROM application_users au WHERE au.company_id = emp.company_id AND au.id = emp.approval_manager_id )reporting_manager FROM employees emp WHERE emp.company_id = '".$data['company_id']."' AND  emp.id =".$data['employee_id'];
		$employee = $callConfig->getRow($emp_query);

		


		if($data['location_id']!="" && $data['location_id']!="0" && $data['location_id_value']!=""){
			$loccondition = " AND (emp.location_id IN (".$data['location_id_value']."))";
		}
		$condition = "
		(
	    CASE WHEN (
	      emp.date_of_leaving IS NOT NULL 
	      AND emp.date_of_leaving >= '".$startdate."'
	    ) THEN 1 WHEN (
	      emp.date_of_leaving IS NULL 
	      AND emp.is_active = 0
	    ) THEN 1 ELSE 2 END
	  )= 1 
		AND (
	        CASE WHEN '3' <> '".$data['user_type']."'
	            THEN(
	                ('1' = '".$data['admin_type']."') OR (au.reports_to = '".$data['application_user_id']."') OR (au.approval_manager = '".$data['application_user_id']."') OR (emp.hr_buddy = '".$data['employee_id']."')  OR (emp.finance_buddy = '".$data['employee_id']."') OR (emp.admin_buddy = '".$data['employee_id']."') OR (emp.loc_buddy = '".$data['employee_id']."')
	      ) ELSE
	          ( emp.vendor_id = (
	                              SELECT  id
	                              FROM    vendors ven2
	                              WHERE   ven2.email = '".$data['user_email']."'
	                            )
	          ) END
	          )";

	    $query = "SELECT DATE_FORMAT(pt.timesheet_date, '%Y-%m') AS month_year, SUM(pt.billable_hrs) AS total_hours, SUM(pt.bill_amount) AS total_amount FROM  project_timesheets pt,   employees emp,  application_users au WHERE pt.company_id = emp.company_id AND pt.company_id = au.company_id AND pt.company_id ='".$data['company_id']."' AND pt.emp_id = emp.id  AND emp.user_id = au.id AND pt.timesheet_date BETWEEN '".$prevmonthfdate."' AND '".$lastdate."' AND ".$condition." AND pt.is_active = 0 GROUP BY month_year ORDER BY month_year";
		$huor_query = $query;
		$huor_data = $callConfig->getAllRows($query);
		$hr_dis_data['previous_month'] = 0;
		$hr_dis_data['current_month'] = 0;
		$hr_dis_data['previous_month_bill'] = 0;
		$hr_dis_data['current_month_bill'] = 0;
		foreach($huor_data as $huor_da){
			$ppym = date('Y-m',strtotime($prevmonthfdate));
			if($ppym == $huor_da->month_year){
				$hr_dis_data['previous_month'] = $huor_da->total_hours;
				$hr_dis_data['previous_month_bill'] = $huor_da->total_amount;
			}else{
				$hr_dis_data['current_month']  =$huor_da->total_hours;
				$hr_dis_data['current_month_bill'] = $huor_da->total_amount;
			}
		}


		$sql = "SELECT emp.id,emp.date_of_leaving,emp.joining_date, emp.vendor_id, emp.billable FROM employees emp,application_users au where  emp.company_id='".$data['company_id']."' AND emp.user_id = au.id AND emp.company_id = au.company_id  ".$loccondition." AND  emp.employee_type = 2 AND ".$condition;
		$row=$callConfig->getAllRows($sql);
		$resign = 0;
		$join = 0;
		$preresign = 0;
		$prejoin = 0;
		$totalactive = 0;
		$totalvendor = array();
		$type_billable = array();
		foreach($row as $emplist){
			$totalactive++;
			$type_billable[$emplist->billable] = $type_billable[$emplist->billable]+1;
			if($emplist->joining_date <= $enddate AND $emplist->joining_date >= $startdate ){
				$join++;
			}
			if($emplist->date_of_leaving <= $enddate AND $emplist->date_of_leaving >= $startdate){
				$resign++;
			}
			if($emplist->joining_date <= $prevenddate AND $emplist->joining_date >= $prevstartdate ){
				$prejoin++;
			}
			if($emplist->date_of_leaving <= $prevenddate AND $emplist->date_of_leaving >= $prevstartdate){
				$preresign++;
			}
			if($emplist->vendor_id != "" && $emplist->vendor_id != "0" ){
			$totalvendor[$emplist->vendor_id] = $emplist->vendor_id;
			}
		}
		//echo "total - ".$totalactive."- join - ".$join." -resign : ".$resign."- prejoin - ".$prejoin." -preresign : ".$preresign;
		//$return =   array("total_emp"=>$sql,"this_month_join"=>$sql1,"previous_month_join"=>$sql3,"this_month_leaving"=>$sql4,"previous_month_leaving"=>$sql5);
		//$data['fromdate']='2024-05';
		$report_data = $this->bugetvendorrareport($data);
		$data['start_date'] = $startdate;
		$data['end_date'] = $enddate;
		$data['month'] = date('m', strtotime($startdate));
		$data['year'] = date('Y', strtotime($enddate));;
		$gdata =$this->vendorstaqAdminDashboard($data);
		$return =   array("total_emp"=>$totalactive,"this_month_join"=>$join,"previous_month_join"=>$prejoin,"this_month_leaving"=>$resign,"previous_month_leaving"=>$preresign,"total_ven"=>count($totalvendor), 'type_billable'=>$type_billable, "sql"=>$sql, 'emp_query' => $emp_query,"emp_data"=>$employee,'huor_query'=>$huor_query, 'hr_dis_data'=>$hr_dis_data, 'report_data'=>$report_data, "mainemp"=>$gdata['mainemp'],'joining'=>$gdata['joining'],'resign'=>$gdata['resign'],'ssql'=>$query, "get"=>$data);
		return $return;
}

function  vendorstaqAdminDashboard($get){
	global $callConfig;
	$month = $get['month'];
	$year = $get['year'];

	$startdate = $get['start_date'];
	$endtdate = $get['end_date'];
	$company_id = $get['company_id'];
	$employee_id = $get['employee_id'];
	$application_user_id = $get['application_user_id'];
	$str  = "";

	if($get['vendor_id']!='0' || $get['emp_name']!='0' || $get['approval_manager']!='0' ){
		$str = " WHERE";
		if($get['vendor_id']!='0'){
			$str.= " vendor_id=".$get['vendor_id'];
		}
		if($get['emp_name']!='0'){
			if($get['vendor_id']!='0'){
			   $str.= " AND";
			}
			$str.= "  employee_name LIKE '%".$get['emp_name']."%' ";
		}
		if($get['approval_manager']!='0'){
			if($get['vendor_id']!='0' || $get['emp_name']!='0'){
			   $str.= " AND";
			}
			$str.= " approval_manager = ".$get['approval_manager'];
		}
	}

	$query = "SELECT  company_id,
		vendor_id,
		pt_month,
	joining_date,
		date_of_leaving,
		vendor_name,
		vertical_id,
		verticle_name,
		cost_center_id,
		cost_center_name,
		department_id,
		department_name,
		employee_id,
		user_id,
		display_name,
		job_title_id,
		job_title,
		SUM(po_amt) po_total,
		SUM(ratecard_amt) actual_total
	FROM
	(
		SELECT  emp.company_id,
				emp.vendor_id,
				EXTRACT(YEAR_MONTH FROM pt.timesheet_date)  pt_month,
				emp.joining_date,
				emp.date_of_leaving,
	  (
		SELECT  vend.display_name
		FROM    vendors vend
		WHERE   vend.company_id = emp.company_id
		AND     vend.id = emp.vendor_id
	  ) vendor_name,
	  emp.vertical_id,
	  (
		SELECT  vert.vertical_name
		FROM    verticals vert
		WHERE   vert.company_id = emp.company_id
		AND     vert.id = emp.vertical_id
	  ) verticle_name,
	  emp.cost_center_id,
	  (
		SELECT  cc.cost_center_name
		FROM    cost_centers cc
		WHERE   cc.company_id = emp.company_id
		AND     cc.id = emp.cost_center_id
	  ) cost_center_name,
	  emp.department_id,
	  (
		SELECT  dept.department_name
		FROM    departments dept
		WHERE   dept.company_id = emp.company_id
		AND     dept.id = emp.department_id
	  ) department_name,
	  emp.id employee_id,
	  emp.user_id,
	  emp.display_name,
	  emp.job_title_id,
	  (
		SELECT  jt.job_title
		FROM    job_titles jt
		WHERE   jt.company_id = emp.company_id
		AND     jt.id = emp.job_title_id
	  ) job_title,
	  pt.bill_amount AS po_amt,
	  pt.vrc_billable_amount AS ratecard_amt
	FROM  employees emp,
		  project_timesheets pt,
		  application_users au
	WHERE emp.company_id = pt.company_id
	AND   emp.id = pt.emp_id
	AND   emp.joining_date <= '".$endtdate."'
	AND   emp.company_id = '1'
	AND	  pt.timesheet_date >= '".$startdate."'    
	AND	  pt.timesheet_date <= '".$endtdate."'    
	AND   emp.company_id = au.company_id
	AND   emp.user_id = au.id
	AND	  emp.employee_type = 2
	AND   pt.is_active = 0
	AND emp.is_active = 0
	AND (
		CASE WHEN '3' <> '".$get['user_type']."'
			THEN(
				
	('1' = '".$get['admin_type']."') OR (au.reports_to = '".$application_user_id."') OR (emp.hr_buddy = '".$employee_id."') OR (emp.finance_buddy  = '".$employee_id."') OR (emp.admin_buddy = '".$employee_id."') OR (emp.project_buddy = '".$employee_id."') OR (emp.loc_buddy = '".$employee_id."')
	  ) ELSE
		  ( emp.vendor_id = (
							  SELECT  id
							  FROM    vendors ven2
							  WHERE   ven2.email = '".$get['user_email']."'
							)
		  ) END
		  )
	) a
	GROUP BY  company_id,
		  vendor_id,
		  pt_month,
		  vendor_name,
		  vertical_id,
		  verticle_name,
		  cost_center_id,
		  cost_center_name,
		  department_id,
		  department_name,
		  employee_id,
		  user_id,
		  display_name,
		  job_title_id,
		  job_title";
	//echo $query;
	$return = $callConfig->getAllRows($query); 
	//print_r($get); exit;
	$begin = new DateTime($startdate);
	$end   = new DateTime($endtdate);
	$postarray =array();
	$thismongthjoin =array();
	$thismonthresigning =array();
	for($i = $begin; $i <= $end; $i->modify('+1 month')){
	$sdane = $i->format("Y-m-d");
	$edate = $i->format('Y-m-t');
	$checkmonthyear = $i->format('Ym');
	$resign = 0;
	$join = 0;
	foreach($return as $emplist){
		if($emplist->pt_month == $checkmonthyear AND $emplist->joining_date <= $edate AND $emplist->date_of_leaving >= $edate){
			$postarray[$i->format('Y-m')][] = $emplist;
		}
		if($emplist->pt_month == $checkmonthyear AND $emplist->joining_date <= $edate AND $emplist->joining_date >= $sdane){
			$join++;
		}
		
		if($emplist->pt_month == $checkmonthyear AND $emplist->date_of_leaving <= $edate AND $emplist->date_of_leaving >= $sdane){
			$resign++;
		}
		
	}
	$thismongthjoin[$i->format('Y-m')] = $join;
	$thismonthresigning[$i->format('Y-m')]  = $resign;
	}
	$returnarry = array("mainemp"=>$postarray,'joining'=>$thismongthjoin,'resign'=>$thismonthresigning,'sql'=>$query);
	return $returnarry;
}

function vendorlistforadminmanag($get){
		global $callConfig;
		$query = "SELECT id, display_name, image, from_date, is_active FROM vendors WHERE company_id=".$get['company_id'];
		$data = $callConfig->getAllRows($query);
		return $data;
	}
function togetretrivemanageremployeeleaves($get){
 	global $callConfig;
 	//$get['fromdate'] = date("Y-m-01", strtotime($get['year_month']));
 	//$get['todate'] = date("Y-m-t", strtotime($get['fromdate']));
 	$get['todate'] = date("Y-m-t", strtotime(date('Y-m-d')));
 	$get['fromdate'] = date("Y-m-01", strtotime( $get['todate']." -7 months"));
 	if($get['admin_type']==1){
 		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, el.leave_from, el.leave_to from employees emp, employee_leaves el where emp.company_id = el.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = el.employee_id AND el.leave_from >='".$get['fromdate']."' AND el.leave_from <='".$get['todate']."' AND el.is_active = 0";
 	}else{
 		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, el.leave_from, el.leave_to from employees emp,application_users au, employee_leaves el where emp.company_id = au.company_id AND emp.company_id = el.company_id AND emp.company_id = '".$get['company_id']."' AND au.approval_manager = ".$get['user_id']." AND emp.user_id = au.id AND emp.id = el.employee_id AND el.leave_from >='".$get['fromdate']."' AND el.leave_from <='".$get['todate']."' AND el.is_active = 0";
	}
	    $details['leave_query'] = $query;
	    $details['data'] = $callConfig->getAllRows($query);
	return $details;
}

function togetretrivemanageremployetermination($get){
 	global $callConfig;
 	$get['fromdate'] = date("Y-m-01", strtotime($get['year_month']));
 	$get['todate'] = date("Y-m-t", strtotime($get['fromdate']));
 	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, el.termination_reason, el.employee_leaving_date, el.id termination_id from employees emp,application_users au, employee_terminations el where emp.company_id = au.company_id AND emp.company_id = el.company_id AND emp.company_id = '".$get['company_id']."' AND au.approval_manager = ".$get['user_id']." AND emp.user_id = au.id AND emp.id = el.emp_id AND el.effective_date >='".$get['fromdate']."' AND el.effective_date <='".$get['todate']."' AND el.is_active = 0 ";
	$details['termination_query'] = $query;
	$details['data'] = $callConfig->getAllRows($query);
	return $details;
}
function  vmssingleemployeedashoard_new($get){
		global $callConfig;
		//print_r($get);
		$fristdate = date("Y-m-01", strtotime( date( 'Y-m-01' )." -6 months")); 
		
		$lastdate = date("Y-m-t"); 
		$prevmonthfdate = date("Y-m-01", strtotime( date( 'Y-m-01' )." -1 months"));
		//$empquery = "SELECT emp.id, emp.emp_code, emp.email_id, emp.image, emp.display_name, emp.designation_id, des.designation_name, emp.location_id , au.display_name reporting_manager FROM employees emp, designations des, application_users au WHERE emp.company_id = des.company_id AND emp.company_id = au.company_id AND emp.company_id = '".$get['company_id']."' AND emp.approval_manager_id = au.id AND emp.designation_id = des.id AND emp.id =".$get['empid'];
		$empquery ="SELECT emp.id, emp.emp_code, emp.email_id, emp.image, emp.display_name, emp.designation_id, emp.location_id, ( SELECT des.designation_name FROM designations des WHERE des.company_id = emp.company_id AND des.id = emp.designation_id )designation_name, ( SELECT au.display_name FROM application_users au WHERE au.company_id = emp.company_id AND au.id = emp.approval_manager_id )reporting_manager FROM employees emp WHERE emp.company_id = '".$get['company_id']."' AND  emp.id =".$get['empid'];
		$employee = $callConfig->getRow($empquery);

		$query = "SELECT DATE_FORMAT(timesheet_date, '%Y-%m') AS month_year, SUM(billable_hrs) AS total_hours FROM project_timesheets WHERE timesheet_date BETWEEN '".$prevmonthfdate."' AND '".$lastdate."' AND emp_id =".$get['empid']." GROUP BY month_year ORDER BY month_year";
		$huor_query = $query;
		$huor_data = $callConfig->getAllRows($query);
		$hr_dis_data['previous_month'] = 0;
		$hr_dis_data['current_month'] = 0;
		foreach($huor_data as $huor_da){
			$ppym = date('Y-m',strtotime($prevmonthfdate));
			if($ppym == $huor_da->month_year){
				$hr_dis_data['previous_month'] = $huor_da->total_hours;
			}else{
				$hr_dis_data['current_month']  =$huor_da->total_hours;
			}
		}

	 	//  $query = "SELECT WEEK(timesheet_date) week, DATE(DATE_ADD(timesheet_date, INTERVAL(1-DAYOFWEEK(timesheet_date)) DAY)) as startofweek, DATE(DATE_ADD(timesheet_date, INTERVAL(7-DAYOFWEEK(timesheet_date)) DAY)) as lastofweek,MONTH(timesheet_date) month,YEAR(timesheet_date) year,timesheet_date,sum(billable_hrs) hrs,current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND timesheet_date >= '".$fristdate."' AND timesheet_date <= '".$lastdate."' AND is_active = 0 GROUP BY week , current_status ORDER BY timesheet_date ASC";
		$query ="SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND timesheet_date >= '".$fristdate."' AND timesheet_date <= '".$lastdate."' AND is_active = 0 GROUP BY week, startofweek, lastofweek, month, year, current_status ORDER BY startofweek ASC";
		$queries['timesheet'] = $query;
		$lists = $callConfig->getAllRows($query);
		foreach($lists as $list){
			$timesheet[$list->year."-".$list->month][$list->week][$list->current_status] = $list->hrs;
			$weekdates[$list->year."-".$list->month][$list->week]['start'] = $list->startofweek;
			$weekdates[$list->year."-".$list->month][$list->week]['end'] = $list->lastofweek;
		}
		//	$query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year,  sum(billable_hrs) hrs  FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status NOT IN (".ST_APPROVED.",".ST_ACTIVE.") GROUP BY week,year ORDER BY created_date  DESC LIMIT 0,10";
		//$query = "SELECT * FROM `project_timesheets` WHERE `emp_id` = ".$get['empid']." AND current_status = 10 AND is_active =0 ORDER BY updated_date DESC LIMIT 0,3";
		$query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year,  sum(billable_hrs) hrs  FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status NOT IN (".ST_APPROVED.",".ST_ACTIVE.") GROUP BY week, startofweek, lastofweek, month, year ORDER BY startofweek  DESC LIMIT 0,10";

		$queries['submit'] = $query;
		$sumit = $callConfig->getAllRows($query);
		//$query = "SELECT MONTH(timesheet_date) month, YEAR(timesheet_date) year, timesheet_date, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_APPROVED." GROUP BY year,month ORDER BY created_date DESC LIMIT 0, 10";
		//$query = "SELECT * FROM `project_timesheets` WHERE `emp_id` = ".$get['empid']." AND current_status = 199 AND is_active =0 ORDER BY updated_date DESC LIMIT 0,3";
		$query = "SELECT MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_APPROVED." GROUP BY  month,year ORDER BY month DESC LIMIT 0, 10";
		$queries['approve'] = $query;
		$approve = $callConfig->getAllRows($query);
		//	$query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year, timesheet_date, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_ACTIVE."  GROUP BY week,year ORDER BY created_date  DESC LIMIT 0,10";
		//$query = "SELECT * FROM `project_timesheets` WHERE `emp_id` = ".$get['empid']." AND current_status = 199 AND is_active =0 ORDER BY updated_date DESC LIMIT 0,3";
		 $query = "SELECT WEEK(timesheet_date) week, DATE( DATE_ADD( timesheet_date, INTERVAL( 1 - DAYOFWEEK(timesheet_date) ) DAY ) ) as startofweek, DATE( DATE_ADD( timesheet_date, INTERVAL( 7 - DAYOFWEEK(timesheet_date) ) DAY ) ) as lastofweek, MONTH(timesheet_date) month, YEAR(timesheet_date) year, sum(billable_hrs) hrs, current_status FROM project_timesheets WHERE emp_id = ".$get['empid']." AND is_active = 0 AND current_status = ".ST_ACTIVE."   GROUP BY week, startofweek, lastofweek, month, year, current_status ORDER BY startofweek  DESC LIMIT 0,10";
		$create = $callConfig->getAllRows($query);

		$queries['create'] = $query;
		
		 $query = "SELECT * FROM holidays WHERE holiday_date >= '".$fristdate."' AND holiday_date <= '".$lastdate."' AND location_id = ".$employee->location_id." AND is_active = 0";
		$holidays = $callConfig->getAllRows($query);
		foreach($holidays as $holiday){
			$n = date("n",strtotime($holiday->holiday_date));
			$holileave[$n] = $holileave[$n]+1;
		}
		$fdate=$fristdate;
		for($i=1;$i<190;$i++){
			//echo $fdate."<br>";
			if(strtotime($fdate)<=strtotime($lastdate)){
				$n = date("n",strtotime($fdate));
				$day = date("w",strtotime($fdate));
				if($day!=0 && $day!=6 ){
					$workdays[$n] = $workdays[$n]+1;
				}
			}else{
				break;
			}
			$fdate = date('Y-m-d', strtotime("+1 day", strtotime($fdate)));
		}
		foreach($workdays as $key=>$work){
			$nonetworkindays[$key] = $work-$holileave[$key];
		}
		 $query = "SELECT * FROM holidays WHERE assessment_year >= '".date("Y")."' AND location_id = ".$employee->location_id." AND holiday_date >= '".date('Y-m-d')."' AND  is_active = 0";
		$assmentholidays = $callConfig->getAllRows($query);

		$query = "SELECT * FROM holidays WHERE holiday_date >= '".$fristdate."' AND location_id = ".$employee->location_id." AND  is_active = 0";
		$assmentholiydays = $callConfig->getAllRows($query);
		//echo "<pre>"; print_r($assmentholiydays); 
		$holicountmy = array();

		foreach($assmentholiydays as $assmentholiday){
			$daymoonty = date("Y-m", strtotime($assmentholiday->holiday_date));
			//echo  $daymoonty."<br>";
			$holicountmy[$daymoonty] = $holicountmy[$daymoonty]+1;
		}
		//print_r($holicountmy); exit;
		for($k=0;$k<12;$k++){
			$ccdd =  date("Y-m-01", strtotime( date( $fristdate )." +".$k." months")); 
			$ccmm = date('Y-m',strtotime($ccdd));
			
			$numDays = date('t',strtotime($ccdd));
			$sundayCount =0;
			for ($day = 0; $day < $numDays; $day++) {
		        $nefdate = date("Y-m-d", strtotime( "+".$day." day", strtotime($ccdd)));
		        if ( in_array(date('w', strtotime($nefdate)), array('0','6') )) {
		            $sundayCount++;
		        }
		    }
		    $cc_da[$ccmm]  = $numDays-$sundayCount-$holicountmy[$ccmm];
		}

		return array('timesheet'=>$timesheet, 'empquery'=>$empquery , 'submited'=>$sumit, 'approved'=>$approve, 'create'=>$create,'employee'=>$employee, 'holidays'=>$holidays,'holileave'=>$holileave, 'workdays'=>$workdays, 'nonetworkindays'=>$nonetworkindays, 'assmentholidays'=>$assmentholidays, 'weekdates'=>$weekdates,'queries'=>$queries,'huor_query'=>$huor_query, 'huor_data'=>$huor_data, 'hr_dis_data'=> $hr_dis_data,'count_days'=>$cc_da);
	}
	function dynamicchangestatusmonthprojecttimesheet($post, $get){
		global $callConfig;
		$squery = "SELECT id FROM  project_timesheets WHERE company_id = ".$get['company_id']." AND current_status =0 AND   MONTH(timesheet_date)= ".$post['month']." AND  YEAR(timesheet_date)= ".$post['year']." AND  is_active = 0 LIMIT 0,1";
		$data = $callConfig->getRow($squery);
		$query = "UPDATE project_timesheets SET current_status = ".$post['status']." WHERE company_id = ".$get['company_id']." AND current_status =0 AND   MONTH(timesheet_date)= ".$post['month']." AND  YEAR(timesheet_date)= ".$post['year']." AND  is_active = 0" ;
		$res = $callConfig->executeQuery($query);
				$post['status_old'] = 0;
				$post['amount'] =  0;
				$post['originator']  = $get['user_id'];
				$post['status_old'] = 0;
				$post['amount'] =  0;
				$post['originator']  = $get['user_id'];
				$post['transaction_type'] = 'project_timesheets';
				$post['transaction_number'] = $post['hid'];
				$post['transaction_number'] = $data->id; 
				$post['total_amount'] = 0;
				$post['status'] = $post['status'];
				$post['status_old_value'] = 0;
				$post['Rid'] = $data->id;
				$post['user_id'] = $get['user_id'];
				$post['option'] = 'COM_VIEW_MONTHLY_WORK_ATTENDANCES';
				$post['updated_by'] = $get['user_id'];
				$post['company_id'] = $get['company_id'];
		if($post['status']==8){ 
			$post['hit_val'] = 'Y';
			$uquery = "SELECT approval_manager FROM  application_users WHERE company_id = ".$get['company_id']." AND id=".$get['user_id'];
			$udata = $callConfig->getRow($uquery);
			$post['send_user_id'] = $udata->approval_manager;
		}
		$rres = $this->toSendEmailNotificationsBaseDAOdynamic($post);

	}
	function togetemployeelistleaedata($get){
		global $callConfig;
		$whr = "";
		if( isset($get['admin_type']) && $get['admin_type']!=1){
			$whr = " AND el.employee_id=".$get['employee_id'];
		}
		$query = "SELECT emp.display_name, el.* FROM employee_leaves el, employees emp WHERE el.company_id = emp.company_id AND el.company_id = 1 AND el.employee_id = emp.id ".$whr." AND el.is_active = 0";
		$return = $callConfig->getAllRows($query);
		return $return;
	}
	function getmonthlybulkapprovaltimesheet($get){
		global $callConfig;
		//echo "<pre>"; print_r($get);
		$query = "SELECT id, emp_code, display_name, email_id, joining_date, date_of_leaving, image FROM employees WHERE company_id = ".$get['company_id']." AND user_id IN (SELECT id FROM application_users WHERE company_id = ".$get['company_id']." AND approval_manager = ".$get['user_id']." AND is_active =0) AND joining_date >= ".$get['endingdate']." AND  (
			 CASE WHEN (
			 	date_of_leaving IS NOT NULL 
			 AND date_of_leaving <= '".$get['endingdate']."'
			 ) THEN 1 WHEN (
			  date_of_leaving IS NULL 
			 AND  is_active = 0
			 ) THEN 1 ELSE 2 END
			 )= 1 AND is_active =0";
		$user_lists = $callConfig->getAllRows($query);
		//echo "<pre>"; print_r($user_lists);
		foreach($user_lists as $user_list){
			$empids[] = $user_list->id; 
		}
		if(count((array)$empids)>0){
			$query = "SELECT emp_id, sum(if(current_status=8,billable_hrs,0)) pending, sum(if(current_status>8,billable_hrs,0)) approve, sum(if(current_status=0,billable_hrs,0)) active FROM project_timesheets WHERE company_id = ".$get['company_id']." AND emp_id IN (".implode("," , $empids).") AND timesheet_date >= '".$get['startingdate']."' AND timesheet_date <= '".$get['endingdate']."' AND is_active = 0 GROUP BY emp_id";
			$listdata = $callConfig->getAllRows($query);
		}

		$todate =$get['endingdate'];
		$fromdate =$get['startingdate'];

		$query = "SELECT working_days FROM attendance_settings WHERE company_id =".$get['company_id']." AND is_active =0";
    	$attendances = $callConfig->getRow($query);

    	$query = "SELECT holiday_date FROM `holidays` WHERE company_id =".$get['company_id']." AND holiday_date >= '".$fromdate."' AND holiday_date <= '".$todate."' AND location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") AND is_active =0";
    	$listholidays = $callConfig->getAllRows($query);
    	foreach((array)$listholidays as $listholiday){
    		$holidays[] = $listholiday->holiday_date;
    	}

    	
    	$working_days = json_decode($attendances->working_days,1);
    	$datediff = strtotime($todate) - strtotime($fromdate);
		$datediff = floor($datediff/(60*60*24));
		$absent =0; $per=0; $work_days = 0;
		for($i = 0; $i < $datediff + 1; $i++){
			$valda =  date("Y-m-d", strtotime($fromdate . ' + ' . $i . 'day'));
			$valday = ucwords(date("l", strtotime($fromdate . ' + ' . $i . 'day')));
			if(in_array($valday, $working_days)){						
				if(!in_array($valda, (array)$holidays)){ 
					$work_days = $work_days+1;	
				}else{
					$view_holiday[]=$valda;
				}
			}else{
				$weekoff[] = $valda;
			}

		}
		$squery ="SELECT id, status, name, action_label FROM status_progress WHERE company_id=".$get['company_id']." AND table_name ='project_timesheets' AND is_active = 0";
		$sdata = $callConfig->getAllRows($squery);
		foreach($sdata as $sdd){
			$status_progress[$sdd->status]['name'] = $sdd->name;
			$status_progress[$sdd->status]['action_label'] = $sdd->action_label;
		}

		$query ="SELECT * FROM status WHERE company_id = ".$get['company_id']." AND  is_active =0";
		$status = $callConfig->getAllRows($query);
		foreach($status as $statu){
			$liststatus[$statu->name_value] = $statu;
		}
		$return['emp_details'] = $user_lists;
		$return['emp_ids'] = $empids;
		$return['data'] = $listdata;
		$return['work_days'] = $work_days;
		$return['status_progress'] = $status_progress;
		$return['status'] = $liststatus;
		return $return;
	}

	/// function new data
	
	function insertVendorPortalApplicationUsers($post){
		global $callConfig;
		$post['user_name'] = $post['first_name']." ".$post['last_name'];
		$post['display_name'] = $post['first_name']." ".$post['last_name'];
		$js_role_id = json_encode(array($post['role_id']));
		if($post['rid'] !=""){
			$updatearray = array('role_id'=>$js_role_id,"updated_date"=>date('Y-m-d h:i:s'),"updated_by"=>$post['user_id']);
			$retresult['respons'] = $callConfig->updateRecord('vendor_portal_application_users',$updatearray,'user_email',$post['user_email']);

		}else{
			$applicationuserary = array('company_id'=>$post['company_id'],'user_name'=>$post['user_name'],'first_name'=>$post['first_name'],'last_name'=>$post['last_name'],'display_name'=>$post['display_name'],'user_email'=>$post['user_email'],'user_mobile'=>$post['user_mobile'],'user_type'=>$post['user_type'],'user_password'=>'dcdsfedrgd4','is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'),'role_id'=>$js_role_id);
			$res=$callConfig->insertRecord('vendor_portal_application_users',$applicationuserary);
			$retresult['application_users'][] = $res;
			$retresult['application_users_insert_ary'][] = $applicationuserary;
		}
		return $retresult;
	}
	
	
	function toupdatepotrnnuberwithrcid($post,$get){
		global $callConfig;
		$upquery ="UPDATE vendor_rate_cards SET transaction_number = '".$post['transaction_number']."' WHERE  company_id =".$get['company_id']." AND id =".$post['transaction_id'];
		$res['query'][] = $upquery;
		$res['res'][] = $callConfig->executeQuery($upquery);
		$upquerys ="UPDATE manpower_contracts SET transaction_number = '".$post['transaction_number']."' WHERE  company_id =".$get['company_id']." AND transaction_number =".$post['old_transaction_number'];
		$res['query'][] = $upquerys;
		$res['res'][] = $callConfig->executeQuery($upquerys);
		return $res;
	}
	
	function toupdatealltimebasedonmonthly($get){
		global $callConfig;
		$start = $get['ym']."-01";
    		$end   = date("Y-m-t",strtotime($start));
		//echo $start."__".$end ; exit;
    		$allday = 0;$wo=0;
   		 while ($start <= $end) {
        		$dayOfWeek =  date("N",strtotime($start)); 
      			if ($dayOfWeek == 6 || $dayOfWeek == 7) {
           			 $wo++;
        		} 
			$allday++;
        		$start = date("Y-m-d", strtotime($start . " +1 day"));
    		}
		//echo $allday;print_r($get);exit;
		$dw = array('1'=> $allday-$wo,'2'=> $allday-$wo,'3'=> $allday-$wo);
		$query = "SELECT location_id, COUNT(id) as cc FROM `holidays` WHERE holiday_date Like '".$get['ym']."-%' GROUP BY location_id";
		$hdays =  $callConfig->getAllRows($query);
		foreach($hdays as $hday){
		  $dw[$hday->location_id] = $allday-($wo+$hday->cc); 
		}
		$whr_emp = "";
		//$whr_emp = " AND pt.emp_id IN (406,475,574,618,639,833,852,869,901,622,923,914,915)";
		//echo $allday;print_r($dw);print_r($get);exit;
		//$query = "SELECT DISTINCT emp_id FROM `project_timesheets` WHERE company_id =1 AND timesheet_date LIKE '%".$get['ym']."-%' AND is_active =0";
		$query = "SELECT DISTINCT pt. emp_id, emp.location_id FROM `project_timesheets` pt, employees emp WHERE emp.id = pt.emp_id AND pt.company_id =1  AND pt.timesheet_date LIKE '%".$get['ym']."-%' AND pt.is_active =0".$whr_emp;
		$emp_datas = $callConfig->getAllRows($query);
		//	echo $query; print_r($emp_datas); exit;
		foreach($emp_datas as $emp_data){
			$dd = $dw[$emp_data->location_id];
			$query = "SELECT pt.emp_id, pt.rate_per_hrs, pt.billable_hrs ,pt.bill_amount, round(((vr.item_price/ ".$dd.")/8),2) as val, vr.item_price, vr.purchase_uom FROM  project_timesheets pt, emp_work_order ew, vrc_items vr WHERE pt.emp_id = ew.emp_id AND pt.work_order = ew.work_order_id AND ew.vrc_item_id = vr.id AND pt.timesheet_date LIKE '%".$get['ym']."-%' AND pt.is_active =0 AND ew.is_active =0  AND pt.emp_id =".$emp_data->emp_id;

			$emp_val = $callConfig->getRow($query);
			if($emp_val->purchase_uom=='PM'){
				$rate_hr =  round((($emp_val->item_price/ $dd)/8),2);
			}
			 if($emp_val->purchase_uom=='PD'){
                                $rate_hr =  round((($emp_val->item_price)/8),2);
                        }
			 if($emp_val->purchase_uom=='PH'){
                                $rate_hr =  $emp_val->item_price;
                        }

			//echo $dd."----".$query; print_r($emp_data);// exit;
		//	$upquery = "UPDATE project_timesheets pt SET  pt.rate_per_hrs = ".$rate_hr." WHERE pt.emp_id = ".$emp_data->emp_id." AND pt.timesheet_date LIKE '%".$get['ym']."-%' AND pt.is_active =0";
			$upquery = "UPDATE project_timesheets pt SET  pt.rate_per_hrs = ".$rate_hr.", bill_amount = billable_hrs *".$rate_hr.", vrc_billable_amount = billable_hrs *".$rate_hr." WHERE pt.emp_id = ".$emp_data->emp_id." AND pt.timesheet_date LIKE '%".$get['ym']."-%' AND pt.is_active =0";

			//echo $upquery; print_r($emp_val); echo $rate_hr;   exit;
			$updata[] = $callConfig->executeQuery($upquery);
		}
		return $updata; 
	}	
	function toaccrualbasedonmonthreport($get){
    	global $callConfig;
    	if($get['fromdate']!=""){
			$ym = explode("-", $get['fromdate']);
			$month = $ym[1];
			$year = $ym[0];
		}else{
			$month = date('m');
			$year = date('Y');
		}
	 	$startdate = date("Y-m-d",strtotime($year."-".$month."-01"));
		$enddate = date("Y-m-t",strtotime($startdate));
		$start = $startdate;
		$end = $enddate;
		while ($start <= $end) {
			$dayOfWeek =  date("N",strtotime($start)); 
			if ($dayOfWeek == 6 || $dayOfWeek == 7) {
				$wo++;
			} 
			$allday++;
			$start = date("Y-m-d", strtotime($start . " +1 day"));
		}
		
		$dw = array('allday'=> $allday,'weekof'=> $wo, 'holidays'=>'0','workdays'=> $allday-$wo);
		$local_dw['1'] = $dw;
		$local_dw['2'] = $dw;
		$local_dw['3'] = $dw;
		$hquery = "SELECT location_id, COUNT(id) as cc FROM `holidays` WHERE holiday_date Like '".$get['fromdate']."-%' GROUP BY location_id";
		$hdays =  $callConfig->getAllRows($hquery);
		foreach($hdays as $hday){
		 
		  $local_dw[$hday->location_id]['holidays'] = $hday->cc;
		  $local_dw[$hday->location_id]['workdays'] = $dw['workdays']-$hday->cc; 
		}
		
		$query = "
			SELECT 
			  DISTINCT ewo.emp_id, 
			  e.display_name AS employee_name, 
			  e.location_id, 
			  v.id As vendor_id, 
			  v.display_name AS vendor_name, 
			  vi.item_price, 
			  vi.purchase_uom As uom, 
			  (
			        SELECT SUM(pt.vrc_billable_amount) 
			        FROM project_timesheets pt 
			        WHERE pt.emp_id = ewo.emp_id
			          AND pt.timesheet_date >= '".$startdate."' 
			          AND pt.timesheet_date <= '".$enddate."'
				  AND pt.is_active=0 
			    ) AS bill,
			   (
			        SELECT SUM(pt.billable_hrs)/8 as work_day 
			        FROM project_timesheets pt 
			        WHERE pt.emp_id = ewo.emp_id
			         AND pt.timesheet_date >= '".$startdate."' 
			         AND pt.timesheet_date <= '".$enddate."'
		                  AND pt.is_active=0
			    ) AS work_day,
			  vrc.transaction_number 
			FROM 
			  emp_work_order ewo 
			  INNER JOIN employees e ON e.id = ewo.emp_id 
			  INNER JOIN vendor_rate_cards vrc ON ewo.vrc_id = vrc.id 
			  INNER JOIN vrc_items vi ON vi.transaction_id = vrc.id 
			  AND vi.id = ewo.vrc_item_id 
			  INNER JOIN vendors v ON v.id = vrc.customer_id 
			WHERE 
			  ewo.company_id = 1 
			  AND ewo.effective_from_date <= '".$startdate."' 
			  AND ewo.effective_to_date >= '".$enddate."' 
			ORDER BY 
			  ewo.emp_id";
		$data = $callConfig->getAllRows($query); 
		$return['query']=$query;
		$return['days']=$local_dw;
		$return['data']=$data;
    	//echo "<pre>"; print_r($get);echo $hquery; print_r($local_dw); echo $query; print_r($data); exit;
    	return $return;
    } 
	function toupdatepurchaseordertransaction($post,$get){
	    global $callConfig;
	    $return = array('get' => $get, 'post' => $post);

	    $transaction_id        = $post['transaction_id'];
	    $transaction_number    = trim($post['transaction_number']); 
	    $transaction_record_id = $post['transaction_record_id'];
	    $ref_total_amount      = trim($post['ref_total_amount']);
	    $items                 = $post['items'];

	    //  BACKUP OLD DATA FIRST
    	$this->backupApprovalData($transaction_record_id, $get['company_id'], $get['user_id']);
	    // ================================
	    // 1️⃣ Fetch Old Vendor Header
	    // ================================
	    $query = "SELECT transaction_number, ref_total_amount 
	              FROM vendor_rate_cards 
	              WHERE id = {$transaction_record_id} 
	              AND company_id='{$get['company_id']}' 
	              AND is_active = 0";
	    $old_header = $callConfig->getRow($query);
	    $old_transaction_no = $old_header->transaction_number;

	    // ================================
	    // 2️⃣ Update vendor_rate_cards if needed
	    // ================================
	    $condt = "updated_by = '{$get['user_id']}', updated_date = '". date('Y-m-d H:i:s')."'";
	    if($transaction_number != $old_header->transaction_number){
	        $condt .= ", transaction_number = '{$transaction_number}'";
	    }
	    if($ref_total_amount != "" && $ref_total_amount != $old_header->ref_total_amount){
	        $condt .= ", ref_total_amount = '{$ref_total_amount}'";
	    }
	    if(($transaction_number != $old_header->transaction_number) || 
	       ($ref_total_amount != "" && $ref_total_amount != $old_header->ref_total_amount)){
	        $upquery = "UPDATE vendor_rate_cards SET {$condt} 
	                    WHERE id = {$transaction_record_id} AND is_active = 0";
	        $res1 = $callConfig->executeQuery($upquery);
	        $return['vrc_update'] = $res1;
	    }

	    // ================================
	    // 3️⃣ Update vrc_items (as before)
	    // ================================
	    $item_query = "SELECT id, item_id, item_quantity, min_exp, max_exp
	                   FROM vrc_items 
	                   WHERE transaction_id = {$transaction_record_id} 
	                   AND company_id='{$get['company_id']}' 
	                   AND is_active = 0";
	    $old_items = $callConfig->getAllRows($item_query);
	    $old_qty_map = [];
	    $old_items_data = [];
	    foreach($old_items as $oi){
	        $old_qty_map[$oi->id] = $oi->item_quantity;
	        $old_items_data[$oi->id] = $oi;
	    }

	    $updated_items = [];
	    $itemQtyMap = [];
	    foreach($items as $row){
	        $item_id  = $row['id'];
	        $new_qty  = $row['quantity'];
	        $old_qty  = $old_qty_map[$item_id];
	        $itemQtyMap[$row['id']] = $row['quantity'];

	        if($new_qty != $old_qty){
	            $upquery = "UPDATE vrc_items 
	                        SET item_quantity = '{$new_qty}', 
	                            updated_by = '{$get['user_id']}', 
	                            updated_date = '". date('Y-m-d H:i:s')."'
	                        WHERE id = '{$item_id}' AND is_active = 0";
	            $callConfig->executeQuery($upquery);
	            $updated_items[] = $item_id;
	        }
	    }
	    $return['updated_vrc_items'] = $updated_items;

	    // ================================
	    // 4️⃣ Find linked manpower_contract
	    // ================================
	    $manpower_contract = $callConfig->getRow("
	        SELECT id, transaction_number, ref_total_amount 
	        FROM manpower_contracts 
	        WHERE transaction_number = '{$old_transaction_no}' 
	        AND company_id='{$get['company_id']}' 
	        AND is_active = 0
	    ");
	    if($manpower_contract){
	        $mpc_id = $manpower_contract->id;

	        // =================================
	        // 5️⃣ Update manpower_contracts header
	        // =================================
	        $mpc_condt = "updated_by = '{$get['user_id']}', updated_date = '". date('Y-m-d H:i:s')."'";
	        if($transaction_number != $manpower_contract->transaction_number){
	            $mpc_condt .= ", transaction_number = '{$transaction_number}'";
	        }
	        if($ref_total_amount != "" && $ref_total_amount != $manpower_contract->ref_total_amount){
	            $mpc_condt .= ", ref_total_amount = '{$ref_total_amount}'";
	        }

	        if(($transaction_number != $manpower_contract->transaction_number) || 
	           ($ref_total_amount != "" && $ref_total_amount != $manpower_contract->ref_total_amount)){
	            $upquery = "UPDATE manpower_contracts 
	                        SET {$mpc_condt} 
	                        WHERE id = {$mpc_id} AND is_active = 0";
	            $callConfig->executeQuery($upquery);
	        }

	        // =================================
	        // 6️⃣ Update mpc_items using min_exp+max_exp+item_id
	        // =================================
	        foreach($updated_items as $vrc_item_id){
	            $vrc_data = $old_items_data[$vrc_item_id];
	            $vrc_itemid = $vrc_data->item_id;
	            $min_exp = $vrc_data->min_exp;
	            $max_exp = $vrc_data->max_exp;
	            $new_qty = $itemQtyMap[$vrc_item_id] ?? '';

	            $upquery = "UPDATE mpc_items 
	                        SET item_quantity = '{$new_qty}', 
	                            updated_by = '{$get['user_id']}', 
	                            updated_date = '". date('Y-m-d H:i:s')."'
	                        WHERE transaction_id = {$mpc_id} 
	                        AND item_id = '{$vrc_itemid}' 
	                        AND min_exp = '{$min_exp}'
	                        AND max_exp = '{$max_exp}'
	                        AND is_active = 0";
	            $res1 = $callConfig->executeQuery($upquery);
	    		$return['res1'][] = $upquery;
	        }
	    }

		$query = "UPDATE vendor_rate_cards 
		          SET total_amount = IFNULL((SELECT SUM(total_amount) FROM vrc_items WHERE transaction_id = {$transaction_record_id}), 0)
		          WHERE id = {$transaction_record_id} AND is_active = 0";
		$callConfig->executeQuery($query);

		$query = "UPDATE manpower_contracts 
		          SET total_amount = IFNULL((SELECT SUM(total_amount) FROM mpc_items WHERE transaction_id = {$mpc_id}), 0)
		          WHERE id = {$mpc_id} AND is_active = 0";
		$callConfig->executeQuery($query);

	    return $return;
	}
	function backupApprovalData($transaction_record_id, $company_id, $user_id){
	    global $callConfig;

	    // 1️⃣ Get VRC header
	    $vrc_main = $callConfig->getRow("
	        SELECT * FROM vendor_rate_cards
	        WHERE id = {$transaction_record_id} AND company_id = {$company_id} AND is_active = 0
	    ");

	    // 2️⃣ Get VRC items
	    $vrc_items = $callConfig->getAllRows("
	        SELECT * FROM vrc_items
	        WHERE transaction_id = {$transaction_record_id} AND company_id = {$company_id} AND is_active = 0
	    ");

	    // 3️⃣ Find related MPC record
	    $mpc = $callConfig->getRow("
	        SELECT * FROM manpower_contracts
	        WHERE transaction_number = '{$vrc_main->transaction_number}'
	        AND company_id = {$company_id} AND is_active = 0
	    ");

	    $mpc_id = $mpc ? $mpc->id : 0;

	    // 4️⃣ Get MPC items
	    $mpc_items = [];
	    if($mpc_id){
	        $mpc_items = $callConfig->getAllRows("
	            SELECT * FROM mpc_items
	            WHERE transaction_id = {$mpc_id} AND company_id = {$company_id} AND is_active = 0
	        ");
	    }

	    // 5️⃣ Encode JSON
	    $vrc_data = json_encode([
	        'vrc_main'  => $vrc_main,
	        'vrc_items' => $vrc_items
	    ]);

	    $mpc_data = json_encode([
	        'mpc_main'  => $mpc,
	        'mpc_items' => $mpc_items
	    ]);

	    // 6️⃣ Insert into approval_backup
	    $insquery = "INSERT INTO approval_backup (company_id, vrc_data, mpc_data, created_by, created_date)
	                 VALUES ('{$company_id}', '".addslashes($vrc_data)."', '".addslashes($mpc_data)."', '{$user_id}', '".date('Y-m-d H:i:s')."')";
	    $callConfig->executeQuery($insquery);
	}  
	function geteexitcontrantractoremployeedata($get){
		global $callConfig;
		$return = array();

		$search = trim($get['search'] ?? '');
		if(!isset($get['status']) || $get['status'] == ''){
			$return['active_emp_res'] = $this->activeempdata($get);
			$return['emp_resign_approval_peding_re'] = $this->empresignapprovalpendingdata($get);
			$return['resigned_emp_res'] = $this->resignedempdata($get);
			$return['approval_pending_res'] = $this->approvalpendingdata($get);
        	$return['approval_completed_res'] = $this->approvalcompleteddata($get);
		}
		if($get['status'] == 'ACTIVE'){
			$response = $this->activeempdata($get,$search);
			$return['active_emp_res'] = $response;
		}
		if($get['status'] == 'PENDING'){
			$response = $this->empresignapprovalpendingdata($get,$search);
			$return['emp_resign_approval_peding_re'] = $response;
		}
		if($get['status'] == 'APPROVED'){
			$response = $this->resignedempdata($get,$search);
			$return['resigned_emp_res'] = $response;
		}
		if($get['status'] == 'APPROVAL_PENDING'){
        $return['approval_pending_res'] = $this->approvalpendingdata($get, $search);
	    }
	    if($get['status'] == 'APPROVAL_COMPLETED'){
	        $return['approval_completed_res'] = $this->approvalcompleteddata($get, $search);
	    }
		$query = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE genc_code LIKE '%SEPERATION_MODE%' ";
		$response = $callConfig->getAllRows($query);
		$return['clearance_process_res'] = $response;

	    return $return;
	}
	function activeempdata($get, $search=''){
		global $callConfig;
		$role_arr   = json_decode($get['role_id']);
	    $role_id    = $role_arr[0];
	    $user_id  =  $get['user_id'];
	    $company_id = $get['company_id'];

	    $whr = "";
	    if($role_id == 2){
	    	$query = "SELECT id FROM employees WHERE reporting_manager_id ='{$user_id}' AND company_id = '{$company_id}' AND is_active = 0";
	    	$rows = $callConfig->getAllRows($query);
			$emp_ids = array_column($rows, 'id');   // extract only IDs

			if (!empty($emp_ids)) {
			    $id_list = implode(",", $emp_ids);
			    $whr .= " AND id IN ($id_list)";
			} else {
			    $whr .= " AND id IN (0)";  // no results
			}

	    }
	    if($role_id == 3){
	    	$query = "SELECT id FROM employees WHERE approval_manager_id ='{$user_id}' AND company_id = '{$company_id}' AND is_active = 0";
	    	$rows = $callConfig->getAllRows($query);
	    	$emp_ids = array_column($rows, 'id');   // extract only IDs

			if (!empty($emp_ids)) {
			    $id_list = implode(",", $emp_ids);
			    $whr .= " AND id IN ($id_list)";
			} else {
			    $whr .= " AND id IN (0)";  // no results
			}
	    }
		$query = "SELECT id, display_name, email_id, emp_code FROM employees WHERE date_of_leaving IS NULL  AND company_id = {$get['company_id']} AND is_active = 0 AND employee_type=2 $whr";
		if ($search != '') {
	        $search = addslashes($search);
	        $query .= " AND (display_name LIKE '%$search%' OR email_id LIKE '%$search%' OR emp_code LIKE '%$search%')";
	    }
		$response = $callConfig->getAllRows($query);
	    return $response;
	}
	function empresignapprovalpendingdata($get, $search=''){
	   	global $callConfig;
	    $query = "SELECT 
	                et.id AS termination_id, 
	                et.emp_id, 
	                et.effective_date AS resign_date, 
	                et.employee_leaving_date AS last_working_date, 
	                emps.display_name, 
	                emps.email_id, 
	                emps.emp_code,
	                -- Role-wise approvals
	                SUM(CASE WHEN tal.doa_id = 1 AND tal.response_type = 199 THEN 1 ELSE 0 END) AS admin_approved,
	                SUM(CASE WHEN tal.doa_id = 2 AND tal.response_type = 199 THEN 1 ELSE 0 END) AS it_admin_approved,
	                SUM(CASE WHEN tal.doa_id = 3 AND tal.response_type = 199 THEN 1 ELSE 0 END) AS it_team_approved,
	                SUM(CASE WHEN tal.doa_id = 4 AND tal.response_type = 199 THEN 1 ELSE 0 END) AS final_approved
	              FROM employee_terminations et
	              LEFT JOIN employees emps 
	                ON emps.id = et.emp_id 
	               AND emps.company_id = et.company_id 
	               AND emps.is_active = 0
	              LEFT JOIN transaction_approval_logs tal
	                ON tal.transaction_id = et.id 
	               AND tal.company_id = et.company_id 
	               AND tal.transaction_table = 'employee_terminations'
	              WHERE et.company_id = {$get['company_id']} 
	                AND et.current_status < 199 
	                AND et.is_active = 0";

	    if ($search != '') {
	        $search = addslashes($search);
	        $query .= " AND (emps.display_name LIKE '%$search%' 
	                      OR emps.email_id LIKE '%$search%' 
	                      OR emps.emp_code LIKE '%$search%')";
	    }

	    $query .= " GROUP BY et.id ORDER BY et.id DESC";
	    $response = $callConfig->getAllRows($query);
	    return $response;
	}
	function resignedempdata($get, $search=''){
		global $callConfig;
		$query = "SELECT et.id as termination_id, et.emp_id, et.effective_date as resign_date, et.employee_leaving_date as last_working_date, emps.display_name, emps.email_id, emps.emp_code FROM employee_terminations et
	    LEFT JOIN employees emps ON emps.id = et.emp_id AND emps.company_id = et.company_id AND emps.is_active = 0
	    WHERE  et.company_id = '{$get['company_id']}' AND et.current_status = 199 AND et.is_active = 0";

	    if ($search != '') {
		    $search = addslashes($search);
		    $query .= " AND (emps.display_name LIKE '%$search%' OR emps.email_id LIKE '%$search%' OR emps.emp_code LIKE '%$search%')";
		}
		$response = $callConfig->getAllRows($query);
	    return $response;
	}
	function approvalpendingdata($get, $search = ''){
	    global $callConfig;
	    $empRow = $callConfig->getRow("SELECT id FROM employees WHERE user_id = '{$get['user_id']}' AND company_id = '{$get['company_id']}' AND is_active=0");
		$employee_id = $empRow->id ?? 0;
	    $query = "
	        SELECT DISTINCT 
	            tal.id AS tranaction_approval_id,
	            tal.doa_id,
	            emp.display_name,
	            emp.email_id,
	            emp.emp_code,
	            et.id AS termination_id,
	            et.termination_type,
	            et.effective_date AS resignation_date,
	            et.employee_leaving_date AS leaving_date,
	            et.remarks_by_employee
	        FROM transaction_approval_logs tal
	        INNER JOIN employee_terminations et 
	            ON et.id = tal.transaction_id 
	            AND tal.company_id = et.company_id 
	            AND et.is_active = 0
	        INNER JOIN employees emp 
	            ON emp.id = et.emp_id
	            AND emp.company_id = et.company_id 
	            AND emp.is_active = 0
	        WHERE tal.transaction_table = 'employee_terminations'
	          AND tal.response_type = 101
	          AND tal.company_id = '{$get['company_id']}'
	          AND tal.user_id = '{$employee_id}'
	          AND NOT EXISTS (
	                SELECT 1 FROM transaction_approval_logs t2
	                WHERE t2.transaction_id = tal.transaction_id
	                  AND t2.company_id = tal.company_id
	                  AND t2.doa_id = tal.doa_id
	                  AND t2.response_type = 199
	          )
	    ";

	    if ($search != '') {
	        $search = addslashes($search);
	        $query .= " AND (emp.display_name LIKE '%$search%' 
	                      OR emp.email_id LIKE '%$search%' 
	                      OR emp.emp_code LIKE '%$search%')";
	    }

	    $query .= " ORDER BY et.id DESC";
	    $response = $callConfig->getAllRows($query);

	    return $response;
	}
	function approvalcompleteddata($get, $search = ''){
	    global $callConfig;
	    $empRow = $callConfig->getRow("SELECT id FROM employees WHERE user_id = '{$get['user_id']}' AND company_id = '{$get['company_id']}' AND is_active=0");
		$employee_id = $empRow->id ?? 0;
	    $query = "SELECT DISTINCT 
	            tal.id AS tranaction_approval_id,tal.doa_id,emp.id as emp_id, emp.display_name,emp.email_id,emp.emp_code,et.id AS termination_id,et.termination_type,et.effective_date AS resignation_date,et.employee_leaving_date AS leaving_date,et.remarks_by_employee
	        FROM transaction_approval_logs tal
	        INNER JOIN employee_terminations et 
	            ON et.id = tal.transaction_id 
	            AND tal.company_id = et.company_id 
	            AND et.is_active = 0
	        INNER JOIN employees emp 
	            ON emp.id = et.emp_id
	            AND emp.company_id = et.company_id 
	            AND emp.is_active = 0
	        WHERE tal.transaction_table = 'employee_terminations'
	          AND tal.company_id = '{$get['company_id']}'
	          AND tal.user_id = '{$employee_id}'
	          AND EXISTS (
	                SELECT 1 FROM transaction_approval_logs t2
	                WHERE t2.transaction_id = tal.transaction_id
	                  AND t2.company_id = tal.company_id
	                  AND t2.doa_id = tal.doa_id
	                  AND t2.response_type = 199)";

	    if ($search != '') {
	        $search = addslashes($search);
	        $query .= " AND (emp.display_name LIKE '%$search%' 
	                      OR emp.email_id LIKE '%$search%' 
	                      OR emp.emp_code LIKE '%$search%')";
	    }

	    $query .= " ORDER BY et.id DESC";
	    $response = $callConfig->getAllRows($query);
	    return $response;
	}
	function saveemployeeexit($post,$get){
		global $callConfig;
		$return = array();
		// return $post;
		$query = "UPDATE employees SET date_of_leaving='{$post['last_working_date']}', updated_by = '{$post['user_id']}',  updated_date = '{$post['status_date']}'  WHERE id={$post['emp_id']}";
		$res = $callConfig->executeQuery($query);
		$return['res'] = $res;

		$notice_period_required = $post['notice_period_required'] ?? 30;
		$termination_reason = $post['termination_reason'] ?? 'RESIGNED';
		$termination_type = $post['termination_type'] ?? 'RESIGNED';
		$diff_days = (strtotime($post['last_working_date']) - strtotime($post['resign_date'])) / (60*60*24);
		$diff_days = abs(round($diff_days));
		$res1 = array();
		$insert_arr = array('company_id'=>$post['company_id'],'termination_reason'=>$termination_reason,'termination_type'=>$termination_type,'notice_period_required'=>$notice_period_required,'effective_date'=>$post['resign_date'],'employee_leaving_date'=>$post['last_working_date'],'emp_id'=>$post['emp_id'],'noticeperiod_days'=>$diff_days,'shortfalldays_in_noticeperiod'=>abs($notice_period_required - $diff_days),'remarks_by_employee'=>$post['remarks'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d H:i:s'));
		$res1=$callConfig->insertRecord('employee_terminations',$insert_arr);
		$return['res1']= $res1;
		// $result = $this->insertExitClearanceApprovals([
		//     'company_id'     => $post['company_id'],
		//     'transaction_id' => $res1['LASTID'] ?? 1,
		//     'employee_id'    => $post['emp_id'] // initiator
		// ]);
		// $return['result'] = $result;
		return $return;
	}
	function getworkflowassigndata($get) {
	    global $callConfig;
	    $return = array();
	    // Build query
	    $query = "SELECT id, CONCAT(short_name, '-', location_name) AS location_full_name
	        FROM locations WHERE company_id = {$get['company_id']}  AND is_active = 0 ORDER BY location_name ASC";
	    $result = $callConfig->getAllRows($query);
	    $return['locations'] = $result;

	    if($get['rid'] != ''){
			$query = "SELECT id, assign_type, location_id, employee_id FROM exit_clearance_assignments WHERE company_id={$get['company_id']} AND is_active=0";
			$rows = $callConfig->getAllRows($query);
			$grouped = array('admin'=>array(), 'it_admin'=>array(), 'it_team'=>array(), 'final'=>array());
			// handle result safely
		    if (!empty($rows) && is_array($rows)) {
		        foreach ($rows as $r) {
		            $type = $r->assign_type ?? '';
		            if (isset($grouped[$type])) {
		                $grouped[$type][] = [
		                    'id'          => $r->id ?? '',
		                    'location_id' => $r->location_id ?? '',
		                    'employee_id' => $r->employee_id ?? ''
		                ];
		            }
		        }
		    }
			$return['data'] = $grouped;
		}
	    return $return;
	}
	function insertExitClearanceApprovals($get){
	    global $callConfig;

	    // Fetch all active assignments for this company
	    $query = "SELECT id, assign_type, location_id, employee_id FROM exit_clearance_assignments WHERE company_id = '{$get['company_id']}' AND is_active = 0 ";
	    $rows = $callConfig->getAllRows($query);
	    if (empty($rows)) {
	        return array('status' => 'error', 'message' => 'No active exit clearance assignments found');
	    }
	    // Group by assign_type
	    $grouped = [
	        'admin'    => array(),
	        'it_admin' => array(),
	        'it_team'  => array(),
	        'final'    => array()
	    ];

	    // Combine sections
	    foreach ($rows as $r) {
	        $type = $r->assign_type ?? '';
	        if (isset($grouped[$type])) {
	            $grouped[$type][] = [
	                'location_id' => $r->location_id,
	                'employee_id' => $r->employee_id
	            ];
	        }
	    }

	    // Common static values
	    $company_id        = $get['company_id'];
	    $transaction_table = 'employee_terminations';
	    $transaction_id    = $get['transaction_id'];
	    $response_type     = 101;
	    $request_date      = date('Y-m-d H:i:s');
	    $read_status       = 0;


	    $empRows = $callConfig->getAllRows("SELECT id, display_name, email_id FROM employees WHERE company_id = '{$get['company_id']}' AND is_active = 0");
	    $empList = array();
	    if (!empty($empRows) && is_array($empRows)) {
            foreach ($empRows as $e) {
                $empList[$e->id] = [
                    'display_name' => $e->display_name ?? '',
                    'email'        => $e->email_id ?? ''
                ];
            }
        }
	    $initiator_name  = $empList[$get['employee_id']]['display_name'] ?? "Employee #{$get['employee_id']}";
	    $initiator_email = $empList[$get['employee_id']]['email']  ?? '';

	    $notifications = array();
	    $emails = array();

	     $sectionNames = array(
	     	'admin'    => 'Admin Department',
	        'it_admin' => 'IT Administration',
	        'it_team'  => 'IT Team',
	        'final'    => 'Final Review'
	    );
	    $doaTypeMap = array(
		    'admin'    => 1,
		    'it_admin' => 2,
		    'it_team'  => 3,
		    'final'    => 4
		);

	    // Process each section
	    foreach ($grouped as $type => $records) {
	        if (!empty($records)) {
	            $sectionName = $sectionNames[$type];
	            foreach ($records as $row) {
	                $user_id = $row['employee_id'];
	                $location_id = $row['location_id'];

	                $approver_name  = $empList[$user_id]['display_name'] ?? "Employee #{$user_id}";
	                $approver_email = $empList[$user_id]['email'] ?? '';

	                // 7️⃣ Insert into transaction_approval_logs
	                $insertData = [
	                    'company_id'        => $company_id,
	                    'transaction_table' => $transaction_table,
	                    'transaction_id'    => $transaction_id,
	                    'response_type'     => $response_type,
	                    'user_id'           => $user_id,
	                    'request_date'      => $request_date,
	                    'send_back_flag'    => 0,
	                    'response_date'     => NULL,
	                    'comments'          => NULL,
	                    'doa_id'            => $doaTypeMap[$type] ?? 0,
	                    'workflow_id'       => NULL,
	                    'note'              => $sectionName,
	                    'ref_field'         => $location_id,
	                    'read_status'       => $read_status
	                ];
	                $callConfig->insertRecord('transaction_approval_logs', $insertData);

	                // 8️⃣ Skip notifications for final section
	                if ($type !== 'final') {
	                    $notifMsg = "Hi {$approver_name}, you have a new exit clearance approval request "
	                              . "from {$initiator_name} under {$sectionName}. "
	                              . "Transaction ID: {$transaction_id}. Please review it.";

	                    $emailSubject = "Exit Clearance Request – {$sectionName}";
	                    $emailBody = "
	                        <p>Dear {$approver_name},</p>
	                        <p>You have been assigned an <b>Exit Clearance Approval</b> task under <b>{$sectionName}</b>.</p>
	                        <p>
	                          Transaction ID: <b>{$transaction_id}</b><br>
	                          Requested by: <b>{$initiator_name}</b>
	                        </p>
	                        <p>Please log in to the HR portal to review and take necessary action.</p>
	                        <br>
	                        <p>Regards,<br>HR & Admin Team</p>
	                    ";

	                    $notifications[$user_id] = $notifMsg;
	                    $emails[$user_id] = $emailBody;

	                    // Send Notification
	                    if (function_exists('sendNotification')) {
	                        // $this->sendNotification($user_id, $notifMsg, $transaction_id, $company_id);
	                    }

	                    // Send Email
	                    if (function_exists('sendEmail')) {
	                        // $this->sendEmail($approver_email, $emailSubject, $emailBody);
	                    }
	                }
	            }
	        }
	    }

	    return [
	        'status' => 'success',
	        'message' => 'Approval logs, notifications, and emails processed successfully',
	        'notifications' => $notifications,
	        'emails' => $emails
	    ];
	}
	function sendNotification($employee_id, $message, $transaction_id, $company_id) {
	    global $callConfig;
	    $data = [
	        'employee_id' => $employee_id,
	        'company_id'  => $company_id,
	        'message'     => $message,
	        'transaction_id' => $transaction_id,
	        'created_at'  => date('Y-m-d H:i:s'),
	        'status'      => 0
	    ];
	    $callConfig->insertRecord('notifications', $data);
	}
	function sendEmail($toEmail, $subject, $htmlContent) {
	    // Example basic PHPMailer or mail() logic
	    $headers  = "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
	    $headers .= "From: HR Portal <noreply@yourdomain.com>\r\n";
	    mail($toEmail, $subject, $htmlContent, $headers);
	}
	function updateapprovalstatus($post){
		global $callConfig;
	    $return = array();

	    // ===== Basic validation =====
	    if (empty($post['tranaction_approval_id'])) {
	        return array('status' => 'error', 'message' => 'Transaction approval ID missing');
	    }

	    $id             = (int)$post['tranaction_approval_id'];
	    $doa_id         = (int)$post['doa_id'];
	    $response_type  = (int)$post['response_type'];
	    $comments       = addslashes(trim($post['comments'] ?? ''));
	    $updated_by     = (int)$post['updated_by'];
	    $updated_date   = $post['updated_date'] ?? date('Y-m-d H:i:s');

	    // ===== Prepare update query =====
	    $query = "UPDATE transaction_approval_logs SET response_type = '{$response_type}',response_date = '{$updated_date}',comments = '{$comments}',read_status = 1 WHERE id = {$id}";
	    $res = $callConfig->executeQuery($query);

	    if ($res) {
	        // ===== Optional: update parent employee_terminations status if all approvals done =====
			if ($response_type == 199) {
			    $tranRow = $callConfig->getRow("SELECT transaction_id, company_id FROM transaction_approval_logs WHERE id={$id} LIMIT 1");
			    if (!empty($tranRow)) {
			        $transaction_id = $tranRow['transaction_id'];
			        $company_id     = $tranRow['company_id'];
			        // ---- Get approval status grouped by role (doa_id)
			        $roles = [1, 2, 3]; // admin, it_admin, it_team, final
			        $approvedRoles = [];

			        foreach ($roles as $roleId) {
			            $row = $callConfig->getRow("
			                SELECT COUNT(*) AS cnt 
			                FROM transaction_approval_logs 
			                WHERE transaction_table='employee_terminations'
			                  AND transaction_id={$transaction_id}
			                  AND company_id={$company_id}
			                  AND doa_id={$roleId}
			                  AND response_type=199
			            ");
			            if (($row['cnt'] ?? 0) > 0) {
			                $approvedRoles[] = $roleId; // at least one approver in that role approved
			            }
			        }
			        // ---- If all 4 roles approved → mark termination as completed
			        if (count($approvedRoles) === count($roles)) {
			            $callConfig->executeQuery("
			                UPDATE employee_terminations 
			                SET current_status=199, updated_by='{$updated_by}', updated_date='{$updated_date}'
			                WHERE id={$transaction_id} AND company_id={$company_id} AND is_active=0
			            ");
			        }
			    }
			}
	        // If rejected (201) → mark termination as rejected
	        if ($response_type == 201) {
	            $tranRow = $callConfig->getRow("SELECT transaction_id, company_id FROM transaction_approval_logs WHERE id={$id} LIMIT 1");
	            if (!empty($tranRow)) {
	                $transaction_id = $tranRow['transaction_id'];
	                $company_id     = $tranRow['company_id'];
	                $callConfig->executeQuery("
	                    UPDATE employee_terminations 
	                    SET current_status = 201, updated_by='{$updated_by}', updated_date='{$updated_date}' 
	                    WHERE id = {$transaction_id} AND company_id = {$company_id} AND is_active=0
	                ");
	            }
	        }

	        $return['status'] = 'success';
	        $return['message'] = 'Approval status updated successfully';
	    } else {
	        $return['status'] = 'error';
	        $return['message'] = 'Failed to update approval status';
	    }

	    return $return;
	}
	function getexitemployeeapprovalpopupdata($get){
		global $callConfig;
	    $transaction_approval_id = intval($get['transaction_approval_id'] ?? 0);
	    $query = "SELECT 
	            tal.id AS tranaction_approval_id,
	            tal.doa_id,
	            et.id AS termination_id,
	            emp.display_name,
	            emp.email_id,
	            emp.emp_code,
	            et.termination_type,
	            et.effective_date AS resignation_date,
	            et.employee_leaving_date AS leaving_date,
	            et.remarks_by_employee
	        FROM transaction_approval_logs tal
	        INNER JOIN employee_terminations et 
	            ON et.id = tal.transaction_id
	           AND tal.company_id = et.company_id
	           AND et.is_active = 0
	        INNER JOIN employees emp 
	            ON emp.id = et.emp_id
	           AND emp.company_id = et.company_id
	           AND emp.is_active = 0
	        WHERE tal.id = {$transaction_approval_id}
	        LIMIT 1";

	    $row = $callConfig->getRow($query);

	    $return = [];
	    if($row){
	        $return['data'] = [
	            'display_name' => $row->display_name,
	            'email_id' => $row->email_id,
	            'emp_code' => $row->emp_code,
	            'termination_type' => $row->termination_type,
	            'resignation_date' => $row->resignation_date,
	            'leaving_date' => $row->leaving_date,
	            'remarks_by_employee' => $row->remarks_by_employee
	        ];
	    } else {
	        $return['data'] = null;
	    }
	    return $return;
	}
	function getidcndcexitformdata($get) {
	    global $callConfig;

	    $employee_id = intval($get['employee_id'] ?? 0);
	    if ($employee_id <= 0) {
	        return ['status' => 'error', 'message' => 'Invalid employee ID'];
	    }

	    // Fetch employee details
	    $empQuery = "
	        SELECT 
	            e.id, e.company_id, e.emp_code,
	            e.display_name,
	            e.first_name, e.middle_name, e.last_name,
	            e.designation_id, d.department_name, e.vertical_id,
	            e.approval_manager_id, e.reporting_manager_id,
	            e.joining_date,
	            e.employee_type, e.email_id, e.address, e.post_code,
	            e.phone, e.alternate_number, e.other_mail,
	            e.bank_account_no, e.bank_id, e.bank_link, e.pan_number, e.location_id,
	            e.vendor_id, e.is_active
	        FROM employees e
	        LEFT JOIN departments d ON d.id = e.department_id AND d.company_id = {$get['company_id']} AND d.is_active = 0
	        WHERE e.id = {$employee_id} AND e.company_id = {$get['company_id']} AND e.is_active = 0 LIMIT 1";
	        // exit($empQuery);
	    
	    $employee = $callConfig->getRow($empQuery);

	    if (!$employee) {
	        return ['status' => 'error', 'message' => 'Employee record not found'];
	    }

	    $company_id = $employee->company_id;

	    // Fetch termination details for this employee
	    $termQuery = "
	        SELECT 
	            id, emp_id, termination_type, effective_date, employee_leaving_date, 
	            remarks_by_employee, created_date
	        FROM employee_terminations 
	        WHERE emp_id = {$employee_id} AND company_id = {$company_id} AND is_active = 0
	        ORDER BY id DESC LIMIT 1";
	    $termination = $callConfig->getRow($termQuery);

	    // Fallback defaults
	    $termination_type = !empty($termination->termination_type) ? $termination->termination_type : 'Termination';
	    $resignation_date = !empty($termination->effective_date) ? $termination->effective_date : null;
	    $leaving_date = !empty($termination->employee_leaving_date) ? $termination->employee_leaving_date : null;
	    $termination_id = $termination->id ?? 0;

	    // Clearance sections (map doa_id → section)
	    $sections = [
	        4 => 'Supervisor/Manager Clearance', // final
	        2 => 'Facilities/Security',           // it_admin
	        3 => 'IT',                            // it_team
	        1 => 'Third Party Coordinator'        // admin
	    ];

	    $clearances = [];

	    if ($termination_id > 0) {
	        foreach ($sections as $doa_id => $section_name) {
	            $clrQuery = "
	                SELECT 
	                    tal.response_type, 
	                    emp.display_name AS approver_name
	                FROM transaction_approval_logs tal
	                LEFT JOIN employees emp 
	                    ON emp.id = tal.user_id 
	                    AND emp.company_id = tal.company_id 
	                    AND emp.is_active = 0
	                WHERE tal.transaction_table = 'employee_terminations'
	                  AND tal.transaction_id = {$termination_id}
	                  AND tal.company_id = {$company_id}
	                  AND tal.doa_id = {$doa_id}
	                ORDER BY tal.id DESC LIMIT 1";
	            
	            $clrRow = $callConfig->getRow($clrQuery);

	            $approved = ($clrRow && $clrRow->response_type == 199) ? 1 : 0;
	            $approver_name = $clrRow->approver_name ?? '';

	            $clearances[] = [
	                'section' => $section_name,
	                'doa_id' => $doa_id,
	                'approved' => $approved,
	                'approver_name' => $approver_name
	            ];
	        }
	    }

	    // Final structured data
	    $return = [
	        'status' => 'success',
	        'employee' => [
	            'display_name' => trim($employee->display_name ?? ($employee->first_name . ' ' . $employee->last_name)),
	            'emp_code' => $employee->emp_code ?? '',
	            'designation_id' => $employee->designation_id ?? '',
	            'department_name' => $employee->department_name ?? '',
	            'vertical_id' => $employee->vertical_id ?? '',
	            'joining_date' => $employee->joining_date ?? '',
	            'email_id' => $employee->email_id ?? '',
	            'address' => $employee->address ?? '',
	            'post_code' => $employee->post_code ?? '',
	            'phone' => $employee->phone ?? '',
	            'alternate_number' => $employee->alternate_number ?? '',
	            'bank_account_no' => $employee->bank_account_no ?? '',
	            'ifsc_code' => $employee->ifsc_code ?? '',
	            'employee_type' => !empty($employee->employee_type) ? $employee->employee_type : 'Third Party Contractor',
	        ],
	        'termination' => [
	            'termination_type' => $termination_type,
	            'resignation_date' => $resignation_date,
	            'leaving_date' => $leaving_date,
	            'remarks_by_employee' => $termination->remarks_by_employee ?? '',
	            'termination_id' => $termination_id
	        ],
	        'clearances' => $clearances,
	        'signature' => [
	            'date' => $resignation_date,
	            'signature' => ''
	        ]
	    ];

	    return $return;
	}
	function togetaccourreportquatrly($get){
		global $callConfig;
		$quarter    = $get['quarter'];
		 $year       = $get['year'];
		$company_id = $get['company_id'];
		$qarray = array('Q1'=>'1,2,3','Q2'=>'4,5,6','Q3'=>'7,8,9','Q4'=>'10,11,12');
		$qut = array('Q1'=>1,'Q2'=>2,'Q3'=>3,'Q4'=>4);
		$qut_val = $qut[$quarter];
		$get['quarterly'] =$qarray[$quarter];
		$ex_month = explode(",",$get['quarterly']);
		$query = "SELECT COUNT(*) as cc_val FROM quarterly_accrual_revenue qar WHERE  qar.company_id = '{$company_id}' AND qar.year = '{$year}' AND qar.quarter = '{$quarter}' AND qar.is_active = 0";
		//echo $query;
		$cc_res = $callConfig->getRow($query);	
		foreach($ex_month as $exm){
			$xstart = $year."-".$exm."-01";
			$start = date("Y-m-d",strtotime($xstart));
 	    	$end   = date("Y-m-t",strtotime($start));
			//echo $start."__".$end ; exit;
	    	$allday = 0;$wo=0;
	   		 while ($start <= $end) {
        		$dayOfWeek =  date("N",strtotime($start)); 
      			if ($dayOfWeek == 6 || $dayOfWeek == 7) {
           			 $wo++;
        		} 
				$allday++;
        		$start = date("Y-m-d", strtotime($start . " +1 day"));
    		}
			//echo $allday;print_r($get);exit;
			$dw = array('1'=> $allday-$wo,'2'=> $allday-$wo,'3'=> $allday-$wo);
			$query = "SELECT location_id, COUNT(id) as cc FROM `holidays` WHERE holiday_date Like '".$get['ym']."-%' GROUP BY location_id";
			$hdays =  $callConfig->getAllRows($query);
			foreach($hdays as $hday){
			  $dw[$hday->location_id] = $allday-($wo+$hday->cc); 
			}
			$mallloc[$exm]=$dw;
		}
		//return $mallloc;
		if($cc_res->cc_val==0){
			$fdate = $year."-".$ex_month['0']."-01";
			$wdate = $year."-".$ex_month['2']."-01";
			$ldate = date("Y-m-t", strtotime($wdate));
			$query = "SELECT 
					    e.emp_id,
					    e.vrc_id,
					    e.vrc_item_id,
					    e.work_order_id,
					    e.work_order_detail_id,
					    e.effective_from_date,
					    e.effective_to_date,
					    emp.location_id,
					    vrc.customer_id,
					    vi.item_id,
					    vi.item_price,
					    vi.purchase_uom
					FROM emp_work_order e
					JOIN (
					    SELECT 
					        emp_id, 
					        MAX(effective_to_date) AS max_date
					    FROM emp_work_order
					    WHERE company_id = ".$company_id."
					    GROUP BY emp_id
					) x ON e.emp_id = x.emp_id 
					   AND e.effective_to_date = x.max_date
					JOIN employees emp ON e.emp_id = emp.id
					JOIN vendor_rate_cards vrc ON e.vrc_id = vrc.id
					JOIN vrc_items vi ON e.vrc_item_id = vi.id
					WHERE 
					    (emp.date_of_leaving >= '".$fdate."' OR emp.date_of_leaving IS NULL)
					    AND emp.is_active = 0
					    AND emp.employee_type =2
					ORDER BY e.emp_id ASC;
					";

			$empdatas = $callConfig->getAllRows($query);
			$prscquery = "INSERT INTO quarterly_accrual_revenue ( `company_id`, `year`, `quarter`, `vendor_id`, `emp_id`, `job_title_id`, `vrc_id`, `vrc_item_id`, `bill_rate`, `uom`, `month1_amount`, `month2_amount`, `month3_amount`, `month1_days`, `month2_days`, `month3_days`, `month1_old_amount`, `month2_old_amount`, `month3_old_amount`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ";
			foreach($empdatas as $empda ){
					//$insert_componet_arr['pay_area_id'] = $pay_area_id;
					///$insert_componets = $callConfig->insertRecord('pay_run_summary_components',$insert_componet_arr);
					//print_r($insert_componet_arr);print_r($insert_componets);  exit;
				$empdata = (array)$empda;
				$loc = $empdata['location_id']!="" ? $empdata['location_id'] : 1;
				$mday1 =$mallloc[$ex_month['0']][$loc];
				$mday2 =$mallloc[$ex_month['1']][$loc];
				$mday3 =$mallloc[$ex_month['2']][$loc];
				$amount1 = $empdata['item_price'];
				$amount2 = $empdata['item_price'];
				$amount3 = $empdata['item_price'];
				if($empdata['purchase_uom']=='PD'){
					$amount1 = $empdata['item_price']*$mday1;
					$amount2 = $empdata['item_price']*$mday2;
					$amount3 = $empdata['item_price']*$mday3;
				}				
							
				$prscquery.="('".$company_id."','".$year."','".$qut_val."','".$empdata['customer_id']."','".$empdata['emp_id']."','".$empdata['item_id']."','".$empdata['vrc_id']."','".$empdata['vrc_item_id']."','".$empdata['item_price']."','".$empdata['purchase_uom']."','".$amount1."','".$amount2."','".$amount3."','".$mday1."','".$mday2."','".$mday3."','".$amount1."','".$amount2."','".$amount3."','0','0','".date("Y-m-d H:i:s")."','".$get['user_id']."'),";
			} 
			$prscquery = trim($prscquery,",").";";
			//echo $prscquery; exit;
			$res1 = $callConfig->executeQuery($prscquery);
		}
		return $res1;
	}
	function getQuarterlyAccrualRevenue($get) {
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];
	    $year       = $get['year'];
	    $quarter    = $get['quarter'];
	    $role_arr   = json_decode($get['role_id']);
	    $role_id    = $role_arr[0];
	    $whr = "";
	    if($role_id == 2){
	    	$query = "SELECT id FROM employees WHERE reporting_manager_id ='{$user_id}' AND company_id = '{$company_id}' AND is_active = 0";
	    	$rows = $callConfig->getAllRows($query);
			$emp_ids = array_column($rows, 'id');   // extract only IDs

			if (!empty($emp_ids)) {
			    $id_list = implode(",", $emp_ids);
			    $whr .= " AND e.id IN ($id_list)";
			} else {
			    $whr .= " AND e.id IN (0)";  // no results
			}

	    }
	    if($role_id == 3){
	    	$query = "SELECT id FROM employees WHERE approval_manager_id ='{$user_id}' AND company_id = '{$company_id}' AND is_active = 0";
	    	$rows = $callConfig->getAllRows($query);
	    	$emp_ids = array_column($rows, 'id');   // extract only IDs

			if (!empty($emp_ids)) {
			    $id_list = implode(",", $emp_ids);
			    $whr .= " AND e.id IN ($id_list)";
			} else {
			    $whr .= " AND e.id IN (0)";  // no results
			}
	    }


	    // -----------------------------
	    // 1. Determine quarter months
	    // -----------------------------
	    if ($quarter == "Q1") {
		    $quarter = 1;
		}
		elseif ($quarter == "Q2") {
		    $quarter = 2;
		}
		elseif ($quarter == "Q3") {
		    $quarter = 3;
		}
		else { // Q4
		    $quarter = 4;
		}


	    // -----------------------------
	    // 2. Main query with joins
	    // -----------------------------
	    $sql = "SELECT qar.id,
	            qar.company_id,
	            qar.year,
	            qar.quarter,

	            qar.vendor_id,
	            v.display_name AS vendor_name,

	            qar.emp_id,
	            e.display_name AS employee_name,

	            qar.job_title_id,
	            jt.job_title AS job_title_name,

	            qar.bill_rate,
	            qar.uom AS uom_id,
	            u.genc_detail_code,
	            u.genc_detail_name,

	            qar.month1_amount,
	            qar.month2_amount,
	            qar.month3_amount,

	            qar.month1_days,
	            qar.month2_days,
	            qar.month3_days,

	            qar.month1_old_amount,
	            qar.month2_old_amount,
	            qar.month3_old_amount,

	            qar.comments,
	            qar.created_date,
	            qar.created_by

	        FROM quarterly_accrual_revenue qar
	        LEFT JOIN vendors v 
	            ON qar.vendor_id = v.id 
	            AND v.company_id = qar.company_id 
	            AND v.is_active = 0

	        JOIN employees e 
	            ON qar.emp_id = e.id 
	            AND e.company_id = qar.company_id 
	            AND e.is_active = 0  $whr

	        LEFT JOIN job_titles jt 
	            ON qar.job_title_id = jt.id 
	            AND jt.company_id = qar.company_id 
	            AND jt.is_active = 0

	        LEFT JOIN general_code_details u 
	            ON qar.uom = u.genc_detail_code  AND u.genc_code = 'JOBUOM'
	            AND u.company_id = qar.company_id 
	            AND u.is_active = 0

	        WHERE qar.company_id = '{$company_id}'
	          AND qar.year = '{$year}'
	          AND qar.quarter = '{$quarter}'
	          AND qar.is_active = 0

	        ORDER BY v.display_name, e.display_name ASC";

	    $rows = $callConfig->getAllRows($sql);


	    return $rows;
	}
	function getQuarterlyAccrualRevenueById($get) {
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $id         = $get['rid'];

	    $return = array();

	    // -----------------------------------
	    // 1. Fetch main record
	    // -----------------------------------
	    $sql = "SELECT qar.*,
	            v.display_name AS vendor_name,
	            e.display_name AS employee_name,
	            jt.job_title AS job_title_name,
	            u.genc_detail_code,
	            u.genc_detail_name
	        FROM quarterly_accrual_revenue qar
	        LEFT JOIN vendors v 
	               ON v.id = qar.vendor_id 
	              AND v.company_id = qar.company_id 
	              AND v.is_active = 0
	        LEFT JOIN employees e
	               ON e.id = qar.emp_id 
	              AND e.company_id = qar.company_id 
	              AND e.is_active = 0
	        LEFT JOIN job_titles jt
	               ON jt.id = qar.job_title_id 
	              AND jt.company_id = qar.company_id 
	              AND jt.is_active = 0
	        LEFT JOIN general_code_details u 
	            ON qar.uom = u.genc_detail_code  AND  genc_code = 'JOBUOM'
	            AND u.company_id = qar.company_id 
	            AND u.is_active = 0
	        WHERE qar.company_id = '{$company_id}'
	          AND qar.id = '{$id}'
	          AND qar.is_active = 0
	        LIMIT 1";

	    $row = $callConfig->getRow($sql);
	    $return['record'] = $row ? (array)$row : array();

	    // -----------------------------------
	    // 2. Vendor List
	    // -----------------------------------
	    $sql_vendor = "SELECT id, display_name 
	        FROM vendors
	        WHERE company_id = '{$company_id}'
	          AND is_active = 0
	        ORDER BY display_name ASC";
	    $return['vendors'] = $callConfig->getAllRows($sql_vendor);

	    // -----------------------------------
	    // 3. Employee List
	    // -----------------------------------
	    $sql_emp = "SELECT id, display_name
	        FROM employees
	        WHERE company_id = '{$company_id}'
	          AND is_active = 0
	        ORDER BY display_name ASC";
	    $return['employees'] = $callConfig->getAllRows($sql_emp);

	    // -----------------------------------
	    // 4. Job Title List
	    // -----------------------------------
	    $sql_jobs = "SELECT id, job_title 
	        FROM job_titles
	        WHERE company_id = '{$company_id}'
	          AND is_active = 0
	        ORDER BY job_title ASC";
	    $return['job_titles'] = $callConfig->getAllRows($sql_jobs);

	    // -----------------------------------
	    // 5. UOM List
	    // -----------------------------------
	    $sql_uom = "SELECT genc_detail_code, genc_detail_name
	        FROM general_code_details
	        WHERE genc_code = 'JOBUOM' AND company_id = '{$company_id}'
	          AND is_active = 0
	        ORDER BY genc_detail_name ASC";
	    $return['uoms'] = $callConfig->getAllRows($sql_uom);

	    return $return;
	}
	function saveQuarterlyAccrualRevenue($get, $post) {
	    global $callConfig;
	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];
	    $id         = $post['id']; // we send 'id' from frontend
	    if ($id <= 0) {
	        return array("status" => "error", "message" => "Invalid ID");
	    }

	    $old_row = $callConfig->getRow("SELECT * FROM quarterly_accrual_revenue WHERE id = '{$id}' AND company_id = '{$company_id}' AND is_active=0");

	    if (!$old_row) {
	        return array("status" => "error", "message" => "Record Not Found");
	    }

	    $old_row->transaction_id = $old_row->id; // map original id
	    $old_row->month1_old_amount = $old_row->month1_amount; 
	    $old_row->month2_old_amount = $old_row->month2_amount; 
	    $old_row->month3_old_amount = $old_row->month3_amount; 
	    $old_row->created_by = $user_id; 
	    $old_row->created_date = date("Y-m-d H:i:s"); 
	    unset($old_row->id); // remove primary key so history table creates a new ID

	    $row = $callConfig->insertRecord("quarterly_accrual_revenue_history", $old_row);
	    $history_id = $row['LASTID'];


	    $update = array(
	        "bill_rate"           => $post['bill_rate'] ?? null,
	        "month1_amount"       => $post['month1_amount'] ?? null,
	        "month2_amount"       => $post['month2_amount'] ?? null,
	        "month3_amount"       => $post['month3_amount'] ?? null,
	        "comments"            => $post['comments'] ?? null,
	        "updated_by"          => $user_id,
	        "updated_date"        => date("Y-m-d H:i:s"),
	    );


	  	$res =  $callConfig->updateRecord("quarterly_accrual_revenue", $update, "id", $id);

	    $history_update = array(
	        "month1_amount" => $post['month1_amount'],
	        "month2_amount" => $post['month2_amount'],
	        "month3_amount" => $post['month3_amount'],
	        "updated_by"    => $user_id,
	        "updated_date"  => date("Y-m-d H:i:s")
	    );

	   $res1=$callConfig->updateRecord("quarterly_accrual_revenue_history",$history_update,"id",$history_id);

	    return array("status" => "success", "id" => $id, 'res' => $res, 'res1' => $res1);
	}
	function togetlastvscurrentquarterlyaccrual($get){
		// return $get;
		global $callConfig;
		$quarter = $get['quarter'];
		if ($quarter == "Q1") {
		    $quarter = 1;
		}
		elseif ($quarter == "Q2") {
		    $quarter = 2;
		}
		elseif ($quarter == "Q3") {
		    $quarter = 3;
		}
		else { // Q4
		    $quarter = 4;
		}
		$whr_vendor_id = '';
		$vendor_ids = [];

		if(!empty($get['vendor_id'])){
		    $vendor_ids[] = intval($get['vendor_id']);
		}elseif(!empty($get['manager_id'])){
		    $manager_id = intval($get['manager_id']);
		    $query = "SELECT DISTINCT e.vendor_id
		        FROM employees e
		        INNER JOIN application_users u 
		            ON e.user_id = u.id
		        WHERE u.reports_to = $manager_id
		          AND u.company_id = ".$get['company_id']."
		          AND u.is_active = 0
		          AND e.company_id = ".$get['company_id']."
		          AND e.is_active = 0
		          AND e.vendor_id != ''";
		    $result = $callConfig->getAllRows($query);
		    foreach($result as $row){
		        $vendor_ids[] = $row->vendor_id;
		    }
		}
		if(!empty((array)$vendor_ids)){
		    $vendor_ids = array_unique($vendor_ids);
		    $vendor_ids_str = implode(",", $vendor_ids);
		    $whr_vendor_id = " AND vendor_id IN ($vendor_ids_str) ";
		}
		$year = $get['year'];
		$return['get'] = $get;	
		
		$query = "SELECT id, display_name FROM vendors WHERE company_id ='".$get['company_id']."' AND is_active =0";
		$vens = $callConfig->getAllRows($query);
		foreach($vens as $vend){
			$vendors[$vend->id] = $vend->display_name;
		}
		$return['vendors'] = $vendors;
		// return $return;

		$query = "SELECT vendor_id FROM quarterly_accrual_revenue WHERE company_id =".$get['company_id']." AND year = '".$year."' AND quarter = ".$quarter." AND vendor_id!='' AND is_active = 0 ";
		// Special logic ONLY for filter_vendors query
		if(!empty($get['manager_id'])){

		    $manager_id = intval($get['manager_id']);

		    $mgr_query = "SELECT DISTINCT e.vendor_id
		                  FROM employees e
		                  INNER JOIN application_users u ON e.user_id = u.id
		                  WHERE u.reports_to = $manager_id
		                    AND u.company_id = ".$get['company_id']."
		                    AND u.is_active = 0
		                    AND e.company_id = ".$get['company_id']."
		                    AND e.is_active = 0
		                    AND e.vendor_id != ''";

		    $mgr_result = $callConfig->getAllRows($mgr_query);

		    $mgr_vendor_ids = [];

		    foreach($mgr_result as $row){
		        $mgr_vendor_ids[] = $row->vendor_id;
		    }

		    if(!empty($mgr_vendor_ids)){
		        $mgr_vendor_ids = array_unique($mgr_vendor_ids);
		        $mgr_vendor_ids_str = implode(",", $mgr_vendor_ids);
		        $query .= " AND vendor_id IN ($mgr_vendor_ids_str) ";
		    }else{
		        $query .= " AND 1=0 ";
		    }
		}
		$query .= " GROUP BY vendor_id";
		$filter_vendors = $callConfig->getAllRows($query);
		$return['query'] = $query;
		$return['filter_vendors'] = $filter_vendors;
		

		$query = "SELECT vendor_id, COUNT(id) as no_emp, ( SUM(`month1_amount`)+sum(`month2_amount`)+sum(`month3_amount`)) total FROM `quarterly_accrual_revenue` WHERE company_id =".$get['company_id']." AND `year` = '".$year."' AND `quarter` = ".$quarter." AND vendor_id!='' $whr_vendor_id AND is_active = 0 GROUP BY `vendor_id`";
		//echo $query; exit;
		$quacl = $callConfig->getAllRows($query);
		$current_budget = array();
		foreach($quacl as $qua){
			$current_budget[$qua->vendor_id] = $qua;		
		}

		$return['current_budget'] = $current_budget;
		$query = "SELECT vendor_id,comments FROM last_current_quarterly_accrual_comments WHERE company_id =".$get['company_id']." AND `quarter_year` = '".$year."' AND `quarter` = ".$quarter." AND  is_active = 0";
		$return['comm_query'] = $query;
		$quacc = $callConfig->getAllRows($query);
		foreach($quacc as $qua){
			$comm_data[$qua->vendor_id] = $qua->comments;		
		}

		$return['comm_data'] = $comm_data;
		
		if($quarter ==1){
			$quarter =4;
			$year =$year-1;
		}else{
			$quarter =$quarter-1;
		}
		$query = "SELECT vendor_id, COUNT(id) as no_emp, ( SUM(`month1_amount`)+sum(`month2_amount`)+sum(`month3_amount`)) total FROM `quarterly_accrual_revenue` WHERE company_id =".$get['company_id']." AND `year` = '".$year."' AND `quarter` = ".$quarter." AND vendor_id!='' $whr_vendor_id AND is_active = 0 GROUP BY `vendor_id`";
		//echo $query; exit;
		$quac2 = $callConfig->getAllRows($query);
		foreach($quac2 as $qua){
			$last_budget[$qua->vendor_id] = $qua;		
		}
		$return['last_budget'] = $last_budget;
		//echo "<pre>"; print_r($return);exit;
		$ft = array('1'=>array('f'=>'01-01','t'=>'03-31'), '2'=>array('f'=>'04-01','t'=>'06-30'), '3'=>array('f'=>'07-01','t'=>'09-30'), '4'=>array('f'=>'10-01','t'=>'12-31'));
		$from = $year."-".$ft[$quarter]['f'];
		$to = $year."-".$ft[$quarter]['t'];
		$query = "SELECT vendor_id, sum(vrc_billable_amount) as total, count(DISTINCT(emp_id)) as no_emp FROM `project_timesheets` WHERE company_id= ".$get['company_id']." AND timesheet_date >= '".$from."' AND timesheet_date <= '".$to."' AND is_active =0 $whr_vendor_id GROUP BY vendor_id";
		$quac3 = $callConfig->getAllRows($query);
		foreach($quac3 as $qua){
			$last_act[$qua->vendor_id] = $qua;		
		}
		$return['last_act'] = $last_act;

		
		// Get unique reports_to IDs
		$query = "
		    SELECT DISTINCT m.id, m.display_name
		    FROM application_users u
		    INNER JOIN application_users m 
		        ON u.reports_to = m.id
		    WHERE u.company_id = ".$get['company_id']."
		      AND u.reports_to IS NOT NULL
		      AND u.reports_to != ''
		      AND u.is_active = 0
		      AND m.is_active = 0
		";

		$manager_list = $callConfig->getAllRows($query);
		$managers = [];

		foreach($manager_list as $m){
		    $managers[$m->id] = $m->display_name;
		}
		$return['managers'] = $managers;

		return $return;
	}
	function tosavecommentdataquater($get, $post){
		global $callConfig;
		if($post['val']!=""){
		$upquery = "UPDATE last_current_quarterly_accrual_comments SET is_active = NULL WHERE company_id =".$get['company_id']." AND vendor_id='".$post['vendor_id']."' AND quarter_year =".$post['year']."  AND quarter =".$post['quarter']." AND is_active =0";
    	$res = $callConfig->executeQuery($upquery);
    	$insert_array = array('company_id'=>$get['company_id'],'vendor_id'=>$post['vendor_id'],'quarter_year'=>$post['year'],'quarter'=>$post['quarter'],'comments'=>$post['val'], 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s')); 
    	 //print_r($insert_array); exit;
    	$exe = $callConfig->insertRecord('last_current_quarterly_accrual_comments', $insert_array);
    	}
    	return $exe;
	}
	function getApprovedTimesheetEditData($get){
        global $callConfig;
        $rid = isset($get['rid']) ? $get['rid'] : '';
        $startdate = isset($get['startdate']) ? $get['startdate'] : '';
        $enddate = isset($get['enddate']) ? $get['enddate'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';

        if ($rid == '' || $startdate == '' || $enddate == '') {
            return array();
        }

        $where = "pt.emp_id='" . $rid . "' AND pt.timesheet_date BETWEEN '" . $startdate . "' AND '" . $enddate . "' AND pt.is_active = 0";
        if ($company_id != '') {
            $where .= " AND pt.company_id='" . $company_id . "'";
        }

        $query = "SELECT pt.id, pt.timesheet_date, pt.billable_hrs, e.display_name "
            . "FROM project_timesheets pt "
            . "LEFT JOIN employees e ON e.id = pt.emp_id AND e.company_id = pt.company_id AND e.is_active = 0 "
            . "WHERE " . $where . " ORDER BY pt.timesheet_date ASC";

        $data = $callConfig->getAllRows($query);
        return $data;
    }
    function saveApprovedTimesheetOvertime($get, $post){

        global $callConfig;
        $ids = isset($post['timesheet_id']) ? $post['timesheet_id'] : array();
        $company_id = isset($post['company_id']) ? $post['company_id'] : '';
        $user_id = isset($post['user_id']) ? $post['user_id'] : '';

        if (!is_array($ids) || count($ids) == 0) {
            return array('status' => 'fail', 'message' => 'No timesheet ids');
        }

        $ids = array_map('intval', $ids);
        $ids_list = implode(',', $ids);
        if ($ids_list == '') {
            return array('status' => 'fail', 'message' => 'Invalid timesheet ids');
        }

        $where = "id IN (" . $ids_list . ")";
        if ($company_id != '') {
            $where .= " AND company_id='" . $company_id . "'";
        }

        $query = "SELECT id, emp_id, timesheet_date, billable_hrs, rate_per_hrs, company_id FROM project_timesheets WHERE " . $where;
        $rows = $callConfig->getAllRows($query);

        if (!$rows || count($rows) == 0) {
            return array('status' => 'fail', 'message' => 'No timesheet records found');
        }

        $updated_date = date('Y-m-d H:i:s');
        $updated_by = $user_id != '' ? $user_id : 0;

        $updated_count = 0;
        $inserted_count = 0;
        $errors = array();

        foreach ($rows as $row) {
            $row_id = $row->id;
            $overtime = 0;
            if (isset($post['overtime_hrs'][$row_id]) && $post['overtime_hrs'][$row_id] !== '') {
                $overtime = floatval($post['overtime_hrs'][$row_id]);
            }

            // Skip if no overtime
            if ($overtime <= 0) {
                continue;
            }

            $current_billable = floatval($row->billable_hrs);
            $rate_per_hrs = floatval($row->rate_per_hrs);
            $new_billable = $current_billable + $overtime;
            $bill_amount = $rate_per_hrs * $new_billable;

            // Update project_timesheets using updateRecord
            $update_array = array(
                'billable_hrs' => $new_billable,
                'bill_amount' => $bill_amount,
                'vrc_billable_amount' => $bill_amount,
                'updated_by' => $updated_by,
                'updated_date' => $updated_date
            );
            $update_res = $callConfig->updateRecord('project_timesheets', $update_array, 'id', $row_id);

            if ($update_res) {
                $updated_count++;

                // Insert overtime record
                $insert_array = array(
                    'company_id' => $row->company_id,
                    'employee_id' => $row->emp_id,
                    'timesheet_date' => $row->timesheet_date,
                    'overtime_hrs' => $overtime,
                    'project_timesheet_id' => $row_id,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_by' => $updated_by,
                    'created_date' => $updated_date
                );
                $insert_res = $callConfig->insertRecord('project_timesheet_overtime', $insert_array);

                if ($insert_res && isset($insert_res['LASTID'])) {
                    $inserted_count++;
                } else {
                    $errors[] = "Failed to insert overtime record for timesheet ID: " . $row_id;
                }
            } else {
                $errors[] = "Failed to update timesheet ID: " . $row_id;
            }
        }

        if (count($errors) > 0) {
            return array(
                'status' => 'partial',
                'message' => 'Some records failed to save',
                'updated' => $updated_count,
                'inserted' => $inserted_count,
                'errors' => $errors
            );
        }

        if ($updated_count == 0 && $inserted_count == 0) {
            return array('status' => 'fail', 'message' => 'No overtime hours entered');
        }

        return array(
            'status' => 'success',
            'message' => 'Overtime saved successfully',
            'updated' => $updated_count,
            'inserted' => $inserted_count
        );
    }
    function getManagerTeamLeaves($get){
        global $callConfig;

        $company_id = isset($get['company_id']) ? (int)$get['company_id'] : 0;
        $user_id = isset($get['user_id']) ? (int)$get['user_id'] : 0;

        $employees_count_query = "SELECT COUNT(*) AS cnt FROM employees WHERE company_id = " . $company_id . " AND approval_manager_id = " . $user_id . " AND is_active = 0";
        $employees_count_row = $callConfig->getRow($employees_count_query);
        $employees_count = isset($employees_count_row->cnt) ? (int)$employees_count_row->cnt : 0;

        $query = "SELECT 
                    e.id AS employee_id,
                    e.emp_code,
                    e.display_name,
                    e.department_id,
                    e.designation_id,
                    el.id AS employee_leave_id,
                    el.leave_type_id,
                    lt.leave_type_name,
                    lt.back_ground_color AS leave_type_color,
                    el.leave_from,
                    el.leave_to,
                    el.leave_days,
                    el.application_date,
                    el.special_request,
                    el.details,
                    el.current_status
                FROM employees e
                INNER JOIN employee_leaves el ON e.id = el.employee_id
                    AND el.company_id = " . $company_id . "
                    AND el.is_active = 0
                    AND el.current_status = 0
                LEFT JOIN leave_types lt ON lt.id = el.leave_type_id
                    AND lt.company_id = " . $company_id . "
                    AND lt.is_active = 0
                WHERE e.company_id = " . $company_id . "
                    AND e.approval_manager_id = " . $user_id . "
                    AND e.is_active = 0
                ORDER BY el.application_date DESC, el.leave_from DESC";

        $rows = $callConfig->getAllRows($query);
        return array(
            'status' => 'success',
            'message' => count((array)$rows) > 0 ? 'Manager team leave data fetched successfully' : 'No leave data found',
            'total' => count((array)$rows),
            'employees_count' => $employees_count,
            'data' => $rows
        );
    }
    function updateManagerTeamLeaveStatus($post,$get){
        global $callConfig;
        $return = array();
        $company_id = isset($post['company_id']) ? (int)$post['company_id'] : 0;
        $user_id = isset($post['user_id']) ? (int)$post['user_id'] : 0;
        $emp_id = isset($post['emp_id']) ? (int)$post['emp_id'] : 0;
        $leave_id = isset($post['leave_id']) ? (int)$post['leave_id'] : 0;
        $approval_status = isset($post['approval_status']) ? (int)$post['approval_status'] : 0;

        if ($emp_id <= 0 || $leave_id <= 0) {
            return array('status' => 'error', 'message' => 'Required data is missing.');
        }

        $leave_query = "SELECT el.id, el.leave_from, el.leave_to, el.current_status, el.is_active
            FROM employee_leaves el
            WHERE el.company_id = " . $company_id . "
                AND el.id = " . $leave_id . "
                AND el.employee_id = " . $emp_id . "
                AND el.is_active = 0";
        $leave_row = $callConfig->getRow($leave_query);

        if (empty((array)$leave_row) || !isset($leave_row->id)) {
            return array('status' => 'error', 'message' => 'Leave request not found or access denied.');
        }
        
        if ((int)$approval_status == 199) {
            $leave_update = $callConfig->updateRecord('employee_leaves', array('current_status' => 199, 'updated_by'=>$user_id, 'updated_date' => date('Y-m-d H:i:s')), 'id', $leave_id);
            $return['leave_update'] = $leave_update;
            $leave_from = $leave_row->leave_from;
            $leave_to = $leave_row->leave_to;
            $timesheet_update_query = "UPDATE project_timesheets  SET bill_amount = 0, vrc_billable_amount = 0, billable_hrs = 0 WHERE company_id = " . $company_id . "  AND emp_id = " . $emp_id . "  AND is_active = 0  AND timesheet_date BETWEEN '" . $leave_from . "' AND '" . $leave_to . "'";

            $timesheet_update = $callConfig->executeQuery($timesheet_update_query);
            $return['timesheet_update'] = $timesheet_update ?? array();

            return array('return' => $return, 'status' => 'success', 'message' => 'Leave approved successfully.');
        }

        if ((int)$approval_status == 40) {
            $leave_update = $callConfig->updateRecord('employee_leaves', array('is_active' => 1, 'current_status' => 40, 'updated_by'=>$user_id, 'updated_date' => date('Y-m-d H:i:s')), 'id', $leave_id);
            $return['leave_update'] = $leave_update;
            
            return array('return' => $return, 'status' => 'success', 'message' => 'Leave request cancled successfully.');
        }
        if ((int)$approval_status == 30) {
	        $leave_update = $callConfig->updateRecord('employee_leaves', array('current_status' => $approval_status, 'updated_by'=>$user_id, 'updated_date' => date('Y-m-d H:i:s')), 'id', $leave_id);
	        $return['leave_update'] = $leave_update;

	        return array('return' => $return, 'status' => 'success', 'message' => 'Leave request rejected successfully.');
	    }
	    return $return;
    }
	function getFinancialYearQuarterDates($financial_year, $quarter) {
		$res = array('status' => 'fail', 'msg' => 'Invalid financial year or quarter');
		$financial_year = trim((string)$financial_year);
		$quarter = strtoupper(trim((string)$quarter));

		if ($financial_year == '' || $quarter == '') {
			return $res;
		}

		$start_year = 0;
		$end_year = 0;

		if (preg_match('/^([0-9]{4})-([0-9]{2})$/', $financial_year, $matches)) {
			$start_year = (int)$matches[1];
			$end_year = (int)('20' . $matches[2]);
		} elseif (preg_match('/^([0-9]{4})-([0-9]{4})$/', $financial_year, $matches)) {
			$start_year = (int)$matches[1];
			$end_year = (int)$matches[2];
		}

		if ($start_year <= 0 || $end_year <= 0) {
			return $res;
		}

		$quarters = array(
			'Q1' => array('start_date' => $start_year . '-04-01', 'end_date' => $start_year . '-06-30'),
			'Q2' => array('start_date' => $start_year . '-07-01', 'end_date' => $start_year . '-09-30'),
			'Q3' => array('start_date' => $start_year . '-10-01', 'end_date' => $start_year . '-12-31'),
			'Q4' => array('start_date' => $end_year . '-01-01', 'end_date' => $end_year . '-03-31'),
		);

		if (!isset($quarters[$quarter])) {
			return $res;
		}

		$res['status'] = 'sucess';
		$res['msg'] = '';
		$res['start_date'] = $quarters[$quarter]['start_date'];
		$res['end_date'] = $quarters[$quarter]['end_date'];
		return $res;
	}
	function insertvmsquarterrateapply($post, $get) {
		global $callConfig;
		$res = array('status' => 'fail', 'msg' => 'Unable to save quarter rate apply request');

		$from_financial_year = trim((string)$post['from_financial_year']);
		$from_quarter = strtoupper(trim((string)$post['from_quarter']));
		$to_financial_year = trim((string)$post['to_financial_year']);
		$to_quarter = strtoupper(trim((string)$post['to_quarter']));
		$extension = trim((string)$post['extension']);
		$company_id = $post['company_id'];

		if ($from_financial_year == '' || $from_quarter == '' || $to_financial_year == '' || $to_quarter == '' || $extension == '' || $company_id == '') {
			$res['msg'] = 'All fields are required';
			return $res;
		}

		

		$dup_query = "SELECT id FROM vms_quarter_rate_apply
			WHERE company_id = '" . $callConfig->real_escape_string($company_id) . "'
			AND from_financial_year = '" . $callConfig->real_escape_string($from_financial_year) . "'
			AND from_quarter = '" . $callConfig->real_escape_string($from_quarter) . "'
			AND to_financial_year = '" . $callConfig->real_escape_string($to_financial_year) . "'
			AND to_quarter = '" . $callConfig->real_escape_string($to_quarter) . "'
			AND is_active = 0
			LIMIT 1";
		$existing_row = $callConfig->getRow($dup_query);
		if ($existing_row && $existing_row->id != '') {
			$res['status'] = 'duplicate';
			$res['msg'] = 'This quarter apply mapping already exists';
			$res['existing_id'] = $existing_row->id;
			return $res;
		}

		$from_dates = $this->getFinancialYearQuarterDates($from_financial_year, $from_quarter);
		if ($from_dates['status'] != 'sucess') {
			$res['msg'] = 'Invalid from financial year or quarter';
			return $res;
		}

		$to_dates = $this->getFinancialYearQuarterDates($to_financial_year, $to_quarter);
		if ($to_dates['status'] != 'sucess') {
			$res['msg'] = 'Invalid to financial year or quarter';
			return $res;
		}

		$postdata = array(
			'company_id' => $company_id,
			'from_financial_year' => $from_financial_year,
			'from_quarter' => $from_quarter,
			'from_start_date' => $from_dates['start_date'],
			'from_end_date' => $from_dates['end_date'],
			'to_financial_year' => $to_financial_year,
			'to_quarter' => $to_quarter,
			'to_start_date' => $to_dates['start_date'],
			'to_end_date' => $to_dates['end_date'],
			'extension' => $extension,
			'is_active' => '0',
			'current_status' => '0',
			'status_date' => $post['status_date'],
			'created_by' => $post['created_by'],
			'created_date' => $post['created_date'],
		);

		$insert_res = $callConfig->insertRecord('vms_quarter_rate_apply', $postdata);
		if ($insert_res['status'] == 'sucess') {
			$res['status'] = 'sucess';
			$res['msg'] = 'Quarter rate apply request saved successfully';
			$res['id'] = $insert_res['LASTID'];
		} else {
			$res['msg'] = 'Database insert failed';
		}

		return $res;
	}

	//Rohan Added Code weekly timehseet Insert

	function getweeklytimesheetentries($get){
		global $callConfig;
		$company_id = !empty($get['company_id']) ? intval($get['company_id']) : 1;
		$empid = !empty($get['empid']) ? intval($get['empid']) : (!empty($get['emp_id']) ? intval($get['emp_id']) : 0);

		if(!empty($get['start_date']) && !empty($get['end_date'])){
			$start_date = date('Y-m-d', strtotime($get['start_date']));
			$end_date = date('Y-m-d', strtotime($get['end_date']));
		} elseif(!empty($get['date'])){
			$d = strtotime($get['date']);
			$start_date = date('Y-m-d', strtotime('monday this week', $d));
			$end_date = date('Y-m-d', strtotime('sunday this week', $d));
		} else {
			$start_date = date('Y-m-d', strtotime('monday this week'));
			$end_date = date('Y-m-d', strtotime('sunday this week'));
		}

		$atquery = "SELECT * FROM attendance_settings WHERE company_id='".$company_id."' AND is_active=0 LIMIT 0,1";
		$attendance = $callConfig->getRow($atquery);
		$working_days_raw = isset($attendance->working_days) ? json_decode($attendance->working_days, 1) : array('Monday','Tuesday','Wednesday','Thursday','Friday');
		$working_days = array();
		foreach((array)$working_days_raw as $wday){
			$working_days[] = strtolower($wday);
		}

		$hquery = "SELECT holiday_date, holiday_name FROM `holidays` WHERE company_id =".$company_id." AND holiday_date >= '".$start_date."' AND holiday_date <= '".$end_date."' AND (location_id IN (SELECT location_id FROM employees WHERE id=".$empid.") OR location_id = 0 OR location_id IS NULL) AND is_active =0";
		$holidays_res = $callConfig->getAllRows($hquery);
		$workholiday = array();
		$listholidays = array();
		foreach((array)$holidays_res as $holida){
			$workholiday[$holida->holiday_date] = $holida;
			$listholidays[] = $holida->holiday_date;
		}

		$equery = "SELECT el.*, lc.name as leave_type_name, lc.back_ground_color as leave_color 
		           FROM employee_leaves el 
		           LEFT JOIN leave_categories lc ON lc.id = el.leave_type_id 
		           WHERE el.company_id='".$company_id."' AND el.employee_id='".$empid."' 
		             AND el.leave_from <= '".$end_date."' AND el.leave_to >= '".$start_date."' 
		             AND el.is_active=0 AND el.current_status NOT IN (30, 40)";
		$leaves_res = $callConfig->getAllRows($equery);
		$workleave_from = array();
		$leave_dates = array();
		$approved_leaves_info = array();
		$pending_leave_dates = array();
		$pending_leaves_info = array();

		foreach((array)$leaves_res as $leav){
			$workleave_from[$leav->leave_from] = $leav;
			$is_appr = ($leav->current_status == 199 || $leav->current_status == 101);
			$cur_d = strtotime($leav->leave_from);
			$last_d = strtotime($leav->leave_to);
			while($cur_d <= $last_d){
				$f_d = date('Y-m-d', $cur_d);
				if($f_d >= $start_date && $f_d <= $end_date){
					if($is_appr){
						$leave_dates[] = $f_d;
						$approved_leaves_info[$f_d] = (array)$leav;
					} else {
						$pending_leave_dates[] = $f_d;
						$pending_leaves_info[$f_d] = (array)$leav;
					}
				}
				$cur_d = strtotime("+1 day", $cur_d);
			}
		}
		$leave_dates = array_values(array_unique($leave_dates));
		$pending_leave_dates = array_values(array_unique($pending_leave_dates));

		$quer_5 = "SELECT * FROM project_timesheets WHERE company_id='".$company_id."' AND emp_id=".$empid." AND timesheet_date >= '".$start_date."' AND timesheet_date <= '".$end_date."' AND is_active=0";
		$empdatas = $callConfig->getAllRows($quer_5);
		$timesheet_data = array();
		$timesheet_dates = array();
		foreach((array)$empdatas as $empda){
			$timesheet_data[$empda->timesheet_date] = (array)$empda;
			$timesheet_dates[] = $empda->timesheet_date;
		}

		$work_days_count = 0;
		$present_count = 0;
		$half_day_count = 0;
		$absent_count = 0;
		$leave_count = 0;
		$holiday_count = 0;
		$weekoff_count = 0;

		for($i = 0; $i < 7; $i++){
			$cdate = date('Y-m-d', strtotime("+$i days", strtotime($start_date)));
			$cday = strtolower(date('l', strtotime($cdate)));
			$is_weekoff = !in_array($cday, $working_days);
			$is_holiday = in_array($cdate, $listholidays);
			$is_leave = in_array($cdate, $leave_dates) || in_array($cdate, $pending_leave_dates);
			
			if($is_weekoff){
				$weekoff_count++;
			} elseif($is_holiday){
				$holiday_count++;
			} elseif($is_leave){
				$leave_count++;
			} else {
				$work_days_count++;
				if(isset($timesheet_data[$cdate])){
					$b_hrs = floatval($timesheet_data[$cdate]['billable_hrs'] ?? 0);
					if($b_hrs == 8){
						$present_count++;
					} elseif($b_hrs == 4){
						$half_day_count++;
					} else {
						$absent_count++;
					}
				}
			}
		}
		$filled_count = $present_count + $half_day_count + $absent_count;
		$can_submit = ($work_days_count > 0 && ($filled_count + $leave_count) >= $work_days_count);

		$details = array();
		$details['start_date'] = $start_date;
		$details['end_date'] = $end_date;
		$details['prev_week'] = date('Y-m-d', strtotime('-1 week', strtotime($start_date)));
		$details['next_week'] = date('Y-m-d', strtotime('+1 week', strtotime($start_date)));
		$details['attendance'] = $attendance;
		$details['working_days'] = $working_days;
		$details['holidays'] = $workholiday;
		$details['listholidays'] = $listholidays;
		$details['workleaves'] = $workleave_from;
		$details['leave'] = $leave_dates;
		$details['approved_leave_dates'] = $leave_dates;
		$details['approved_leaves_info'] = $approved_leaves_info;
		$details['pending_leave_dates'] = $pending_leave_dates;
		$details['pending_leaves_info'] = $pending_leaves_info;
		$details['timesheet'] = $timesheet_data;
		$details['timesheet_date'] = $timesheet_dates;
		$details['all_data'] = array(
			'work_days' => $work_days_count,
			'present_days' => $present_count,
			'half_days' => $half_day_count,
			'absent_day' => $absent_count,
			'leave_days' => $leave_count,
			'holiday_days' => $holiday_count,
			'weekoff_days' => $weekoff_count,
			'filled_days' => $filled_count,
			'can_submit' => $can_submit ? 1 : 0
		);
		return $details;
	}
	function addupdateprojecttimesheetweekly($post, $get){
		global $callConfig;
		$company_id = !empty($get['company_id']) ? intval($get['company_id']) : 1;
		$emp_id = intval($post['emp_id']);
		$date = $post['date'];
		$type = strtolower($post['type'] ?? '');
		$user_id = !empty($get['user_id']) ? intval($get['user_id']) : 1;

		$start_date = !empty($post['start_date']) ? $post['start_date'] : date('Y-m-d', strtotime('monday this week', strtotime($date)));
		$end_date = !empty($post['end_date']) ? $post['end_date'] : date('Y-m-d', strtotime('sunday this week', strtotime($date)));
		$week_get = array(
			'company_id' => $company_id,
			'empid' => $emp_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		);

		// Prevent attendance on active/pending/approved leave date
		$check_leave = $callConfig->getRow("SELECT id, current_status FROM employee_leaves WHERE company_id = '".$company_id."' AND employee_id = '".$emp_id."' AND leave_from <= '".$date."' AND leave_to >= '".$date."' AND current_status NOT IN (30, 40) AND is_active = 0");
		if($check_leave && !empty($check_leave->id)){
			$week_details = $this->getweeklytimesheetentries($week_get);
			$res = $week_details['all_data'];
			$res['status'] = 'fail';
			$is_appr = ($check_leave->current_status == 101 || $check_leave->current_status == 199);
			$res['message'] = $is_appr ? 'Attendance cannot be marked on an approved leave date.' : 'A leave application is already recorded for this date.';
			return $res;
		}

		$hrs = 0;
		$nonhrs = 0;
		if($type == 'p'){
			$hrs = 8;
		} elseif($type == 'z'){
			$hrs = 4;
		} elseif($type == 'a'){
			$hrs = 0;
		}

		$upquery = "UPDATE project_timesheets SET is_active = NULL WHERE company_id =".$company_id." AND timesheet_date='".$date."' AND emp_id =".$emp_id." AND is_active =0";
		$callConfig->executeQuery($upquery);

		$insert_array = array(
			'company_id' => $company_id,
			'timesheet_date' => $date,
			'task_name' => '',
			'work_order' => 0,
			'esgin_budget_approval_id' => 0,
			'vendor_rate_card_id' => 0,
			'rate_per_hrs' => 0,
			'bill_amount' => 0,
			'vrc_billable_amount' => 0,
			'billable_hrs' => $hrs,
			'nonbillable_hrs' => $nonhrs,
			'emp_id' => $emp_id,
			'is_active' => '0',
			'current_status' => '0',
			'created_by' => $user_id,
			'created_date' => date('Y-m-d H:i:s')
		);
		$callConfig->insertRecord('project_timesheets', $insert_array);

		$start_date = !empty($post['start_date']) ? $post['start_date'] : date('Y-m-d', strtotime('monday this week', strtotime($date)));
		$end_date = !empty($post['end_date']) ? $post['end_date'] : date('Y-m-d', strtotime('sunday this week', strtotime($date)));
		$week_get = array(
			'company_id' => $company_id,
			'empid' => $emp_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		);
		$week_details = $this->getweeklytimesheetentries($week_get);
		return $week_details['all_data'];
	}

	function directapplyweeklyleave($post, $get){
		global $callConfig;
		$company_id = !empty($get['company_id']) ? intval($get['company_id']) : (!empty($_SESSION['company_id']) ? intval($_SESSION['company_id']) : 1);
		$emp_id = !empty($post['emp_id']) ? intval($post['emp_id']) : (!empty($get['emp_id']) ? intval($get['emp_id']) : 0);
		$date = !empty($post['date']) ? trim($post['date']) : (!empty($get['date']) ? trim($get['date']) : '');
		$user_id = !empty($get['user_id']) ? intval($get['user_id']) : (!empty($_SESSION['userid']) ? intval($_SESSION['userid']) : 1);

		$start_date = !empty($post['start_date']) ? $post['start_date'] : date('Y-m-d', strtotime('monday this week', strtotime($date)));
		$end_date = !empty($post['end_date']) ? $post['end_date'] : date('Y-m-d', strtotime('sunday this week', strtotime($date)));
		$week_get = array(
			'company_id' => $company_id,
			'empid' => $emp_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		);

		if(empty($emp_id)){
			return array('status' => 'fail', 'message' => 'Invalid or missing employee ID.');
		}
		if(empty($date) || !strtotime($date)){
			return array('status' => 'fail', 'message' => 'Invalid or missing date.');
		}

		// 1. Do not allow future-date leave applications
		if(strtotime($date) > strtotime(date('Y-m-d'))){
			return array('status' => 'fail', 'message' => 'Leave cannot be applied for future dates.');
		}

		// 2. Check Weekly Off
		$atquery = "SELECT * FROM attendance_settings WHERE company_id='".$company_id."' AND is_active=0 LIMIT 0,1";
		$attendance = $callConfig->getRow($atquery);
		$working_days_raw = isset($attendance->working_days) ? json_decode($attendance->working_days, 1) : array('Monday','Tuesday','Wednesday','Thursday','Friday');
		$working_days = array();
		foreach((array)$working_days_raw as $wday){
			$working_days[] = strtolower($wday);
		}
		$cday = strtolower(date('l', strtotime($date)));
		if(!in_array($cday, $working_days)){
			return array('status' => 'fail', 'message' => 'Selected date is a weekly off.');
		}

		// 3. Check Holiday
		$hquery = "SELECT id, holiday_name FROM holidays WHERE company_id = ".$company_id." AND holiday_date = '".$date."' AND (location_id IN (SELECT location_id FROM employees WHERE id=".$emp_id.") OR location_id = 0 OR location_id IS NULL) AND is_active = 0";
		$holiday = $callConfig->getRow($hquery);
		if($holiday && !empty($holiday->id)){
			return array('status' => 'fail', 'message' => 'Selected date is a holiday (' . ($holiday->holiday_name ?? 'Holiday') . ').');
		}

		// 4. Check Duplicate Leave Record
		$check_leave = $callConfig->getRow("SELECT id, current_status FROM employee_leaves WHERE company_id = '".$company_id."' AND employee_id = '".$emp_id."' AND leave_from <= '".$date."' AND leave_to >= '".$date."' AND is_active = 0 AND current_status NOT IN (30, 40)");
		if($check_leave && !empty($check_leave->id)){
			return array('status' => 'fail', 'message' => 'A leave application already exists for this date.');
		}

		// 5. Check if attendance already exists (Do not overwrite existing attendance entries)
		$check_att = $callConfig->getRow("SELECT id, billable_hrs, current_status FROM project_timesheets WHERE company_id = '".$company_id."' AND emp_id = '".$emp_id."' AND timesheet_date = '".$date."' AND is_active = 0");
		if($check_att && !empty($check_att->id)){
			return array('status' => 'fail', 'message' => 'Attendance is already recorded for this date. Leave cannot overwrite existing attendance.');
		}

		// Retrieve employee buddy info if available
		$emp_row = $callConfig->getRow("SELECT hr_buddy, finance_buddy FROM employees WHERE id = '".$emp_id."'");
		$hr_user_id = !empty($emp_row->hr_buddy) ? intval($emp_row->hr_buddy) : 0;
		$finance_user_id = !empty($emp_row->finance_buddy) ? intval($emp_row->finance_buddy) : 0;

		$now = date('Y-m-d H:i:s');
		$insert_leave = array(
			'company_id' => $company_id,
			'employee_id' => $emp_id,
			'leave_type_id' => 0,              // Static leave_type_id = 0 as required
			'leave_from' => $date,
			'leave_to' => $date,
			'leave_days' => 1.00,
			'applying_late' => 0,
			'start_half_day' => 0,
			'end_half_day' => 0,
			'special_request' => 'Direct weekly leave',
			'application_date' => $now,
			'narrative' => '',
			'unpaid_days' => 0,
			'weekend_leave_consume' => 0,
			'holiday_leave_consume' => 0,
			'hr_user_id' => $hr_user_id,
			'finance_user_id' => $finance_user_id,
			'leave_category_id' => 0,
			'is_active' => '0',
			'current_status' => '0',           // Pending manager approval
			'status_date' => $now,
			'from_date' => $date,
			'to_date' => $date,
			'created_date' => $now,
			'created_by' => $user_id,
			'updated_date' => $now,
			'updated_by' => $user_id
		);

		$res = $callConfig->insertRecord('employee_leaves', $insert_leave);

		$week_details = $this->getweeklytimesheetentries($week_get);
		$return = $week_details['all_data'];
		$return['status'] = 'success';
		$return['leave_id'] = $res['LASTID'] ?? 0;
		$return['message'] = 'Leave applied successfully for ' . date('d M Y', strtotime($date)) . '.';
		$return['date'] = $date;
		$return['week_details'] = $week_details;
		return $return;
	}

	function dynamicchangestatusweekprojecttimesheet($post, $get){
		global $callConfig;
		$company_id = !empty($get['company_id']) ? intval($get['company_id']) : 1;
		$emp_id = intval($post['emp_id']);
		$start_date = $post['start_date'];
		$end_date = $post['end_date'];
		$status = intval($post['status']);
		$user_id = !empty($get['user_id']) ? intval($get['user_id']) : 1;

		$squery = "SELECT id FROM project_timesheets WHERE company_id = ".$company_id." AND emp_id = ".$emp_id." AND current_status = 0 AND timesheet_date >= '".$start_date."' AND timesheet_date <= '".$end_date."' AND is_active = 0 LIMIT 0,1";
		$data = $callConfig->getRow($squery);

		$query = "UPDATE project_timesheets SET current_status = ".$status." WHERE company_id = ".$company_id." AND emp_id = ".$emp_id." AND current_status = 0 AND timesheet_date >= '".$start_date."' AND timesheet_date <= '".$end_date."' AND is_active = 0";
		$callConfig->executeQuery($query);

		$post['status_old'] = 0;
		$post['amount'] = 0;
		$post['originator'] = $user_id;
		$post['transaction_type'] = 'project_timesheets';
		$post['transaction_number'] = isset($data->id) ? $data->id : 0; 
		$post['total_amount'] = 0;
		$post['status_old_value'] = 0;
		$post['Rid'] = isset($data->id) ? $data->id : 0;
		$post['user_id'] = $user_id;
		$post['option'] = 'COM_VIEW_WEEKLY_WORK_ATTENDANCES';
		$post['updated_by'] = $user_id;
		$post['company_id'] = $company_id;

		if($status == 8){ 
			$post['hit_val'] = 'Y';
			$uquery = "SELECT approval_manager FROM application_users WHERE company_id = ".$company_id." AND id=".$user_id;
			$udata = $callConfig->getRow($uquery);
			$post['send_user_id'] = $udata->approval_manager ?? 0;
		}
		if(method_exists($this, 'toSendEmailNotificationsBaseDAOdynamic')){
			$rres = $this->toSendEmailNotificationsBaseDAOdynamic($post);
		}
		return array('status' => 'success', 'msg' => 'Timesheet submitted successfully');
	}

} 
?>