<?php 
class pmsUserClass extends userClass{
	function publichAppraisal($get){
		global $callConfig;
		$returnarray = array();
		$pssal ="SELECT * FROM performance_appraisal_setups WHERE company_id =".$get['company_id']." AND id = '".$get['id']."' AND is_active = 0";
		$psres = $callConfig->getRow($pssal);
		
		$condition = "";
		$filtersql = "SELECT ft.id,ft.content, ft.filter_condition FROM filter_tables ft  WHERE ft.company_id = '".$get['company_id']."' AND ft.transaction_id = '".$get['id']."' AND ft.application_tablename ='performance_appraisal_setups'";
		$returnarray['filtersql'] = $filtersql;
		$filterres = $callConfig->getRow($filtersql);
		if($filterres->id !="" || $filterres->content !=""){
			$show_filter_cond = $filterres->filter_condition;
			$show_filter_cond = explode('&',$show_filter_cond);
			foreach($show_filter_cond as $show_filter_co){
				$fieldname = explode("=",$show_filter_co);
				if($fieldname['0']!="check" AND $fieldname['0']!="table"){
					$show_filter_condion[$fieldname['0']]=$fieldname['1'];
				}
			}
			foreach($show_filter_condion as $keys=>$vale){
				if($vale !=""){
					$condition .= ' AND '.$keys.'  IN ('.$vale.')';
				}
			}
		} 
		$employeesql = "SELECT id,user_id, department_id, designation_id, location_id FROM employees WHERE is_active='0' and company_id='".$get['company_id']."' AND (date_of_leaving IS NULL OR date_of_leaving >= '".$psres->self_assessment_start_date."')".$condition; 
		$returnarray['employeesql'] = $employeesql;
		$emplist = $callConfig->getAllRows($employeesql);

		$locdepdesiarray = array();
		$ogoalssql ="SELECT id,job_title_id, department_id, location_id FROM `organization_goals` WHERE company_id =".$get['company_id']." AND appraisal_id = '".$get['id']."' AND is_active = 0";
		$ogoalsres = $callConfig->getAllRows($ogoalssql);
		foreach($ogoalsres as $ogres){
			$locdepdesiarray[$ogres->location_id][$ogres->department_id][$ogres->job_title_id][] = $ogres->id; 
		}
		$empwithuser = array();
		$insert_heade = "INSERT INTO `employee_performance_appraisals` (`company_id`, `appraisal_id`, `employee_id`,`organization_goal_id`,`start_date`,`end_date`, `is_active`, `current_status`, `created_by`, `created_date`) VALUES ";
		foreach($emplist as $empd){
			$organization_goals = array();
			if(isset($locdepdesiarray[$empd->location_id][$empd->department_id][$empd->location_id])){
				$organization_goals = $locdepdesiarray[$empd->location_id][$empd->department_id][$empd->location_id];
			}
			$organization_goal_id = json_encode($organization_goals);
			$insert_heade .= "('".$get['company_id']."','".$get['id']."','".$empd->id."','".$organization_goal_id."','".$psres->self_assessment_start_date."','".$psres->self_assessment_end_date."' ,'0','".ST_ACTIVE."','".$get['user_id']."','".date('Y-m-d h:i:s')."'),";
		}
		$insertsql = rtrim($insert_heade, ',');
		$res = $callConfig->executeQuery($insertsql);

		$update_transaction = array("publish"=>"Y","updated_by"=>$get['user_id'],"updated_date"=>date('Y-m-d h:i:s'));
		$res =$callConfig->updateRecord('performance_appraisal_setups',$update_transaction,'id',$get['id']);
		$returnarray['performance_appraisal_setups'] = $res;



// $insert_array = array('company_id'=>$get['company_id'],'appraisal_id'=>$get['id'],'employee_id'=>$empd->id,'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d h:i:s'));
// 			$insert = $callConfig->insertRecord('performance_appraisal_setups',$insert_array);
// 			$epaid = $insert['LASTID'];

// if(isset($res['LASTID'])){
	// 	$employee_goals = "INSERT INTO `employee_goals`(`company_id`, `employee_id`, `assessment_year`, `indicator_name`, `key_reslut_id`,`key_performance_id`, `weightage`, `model`, `uom`,`assessment_frequency`, `traget`, `is_active`, `current_status`, `created_date`, `created_by`) SELECT '".$post['company_id']."',emp.id,og.assessment_year,og.indicator_name,og.key_reslut_id,og.key_performance_id,og.weightage,og.model, og.uom,og.assessment_frequency ,og.traget,0,0,'".date('Y-m-d h:i:s')."','".$post['user_id']."' FROM organization_goals og, employees emp, appraisal_key_result_areas kra  WHERE og.company_id ='".$post['company_id']."' AND og.assessment_year = '".$post['assessment_year']."' AND kra.appraisal_id = '".$post['appraisal_id']."' AND kra.id = og.key_reslut_id AND og.company_id = emp.company_id AND og.company_id = kra.company_id AND og.job_title_id = emp.job_title_id AND og.department_id = emp.department_id AND og.location_id = emp.location_id AND emp.is_active = 0 AND emp.is_active = og.is_active AND emp.is_active = kra.is_active AND kra.id IN(".$kra_ids.")";
	// 	$res = $callConfig->executeQuery($employee_goals);
	// 	$returnaray['employee_goals'] = $employee_goals;
	// }




/**
		$getupdatesql ="SELECT epah.id,epah.appraisal_id,epah.employee_id,epah.assessment_year,epah.created_by FROM employee_performance_appraisal_headers epah WHERE epah.company_id = '".$get['company_id']."' AND epah.appraisal_id = '".$get['id']."' AND is_active = 0"; 
		$getupdateres = $callConfig->getAllRows($getupdatesql);
		$returnarray['getupdateres'] = $getupdateres;
		foreach($getupdateres as $getupdatere){
			$update_emp_goal = "UPDATE employee_goals eg, appraisal_key_result_areas kra SET eg.epa_header_id = '".$getupdatere->id."' WHERE eg.company_id = '".$get['company_id']."' AND eg.key_reslut_id = kra.id AND kra.appraisal_id = '".$get['id']."' AND eg.employee_id = '".$getupdatere->employee_id."' AND eg.assessment_year = '".$getupdatere->assessment_year."' AND eg.company_id = kra.company_id AND eg.is_active = kra.is_active AND eg.is_active = 0";
			$assetupdate = $callConfig->executeQuery($update_emp_goal);
			$returnarray['employee_goals'][] = $update_emp_goal;
			if(isset($empwithuser[$getupdatere->employee_id])){
				$user_ids = array('0'=>$empwithuser[$getupdatere->employee_id]);
				$option = "COM_INSERT_PERFORMANCE_APPRAISALS";
				$msg = "performanc appraisal - ".$getupdatere->id."(".$getupdatere->assessment_year.")";
				$notdataa = array('company_id'=>$get['company_id'],'notification_message'=>$msg, 'id'=>$getupdatere->id,'application_table_name'=>'employee_performance_appraisal_headers', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
				$sendnotification = $this->tosendnotifiations($notdataa);
			}

		}  **/
	return $returnarray;
	}
	function inserPerformanaceAppraiser($post){
	global $callConfig;
	$returnaray = array();
	$insertfields = "";
	$inserexc = "N";
	foreach($post['emp_goal_id'] as $gkey => $gid){
		$up_array_emp = array('self_achievement'=>$post['self_achievement'][$gkey],'achievement_percent'=>$post['achievement_percent'][$gkey],'approved_achievement'=>$post['approved_achievement'][$gkey],'approved_score'=>$post['approved_score'][$gkey]);
		$resupdate[] = $callConfig->updateRecord('employee_goals',$up_array_emp,'id',$gid);
	}
	$up_array = array('is_active'=>NULL);
		//$resupdate[] = $callConfig->updateRecord('employee_performance_appraisals',$up_array,'appraisal_id',$post['appraisal_id']);
	$insertsql = "INSERT INTO employee_performance_appraisal_details (company_id, epa_header_id, appraisal_id, employee_id, assessment_year, header_id, filed_1, filed_2, filed_3, filed_4, filed_5, filed_6, is_active, current_status, created_date, created_by) VALUES ";
	// used how meny time loop run
	$newewarray = array();
	foreach($post['filed'] as $headerid => $headerow){
		foreach($headerow as $kkey=>$valu){
			$newewarray[$headerid] = $valu;
		}
	}	
	// used how meny time loop run
	foreach($newewarray as $header_id => $headerow){
		$field = array();
		foreach($headerow as $key => $hea){
			for($field = 1;$field <= 6;$field++){ 
				$fird[$field] = $post['filed'][$header_id][$field][$key];
			}
			$rowid = $post['row_id'][$header_id][$key];
				if($rowid !=""){  // updated details
					$insert_array = array('filed_1'=>$fird[1], 'filed_2'=>$fird[2], 'filed_3'=>$fird[3], 'filed_4'=>$fird[4], 'filed_5'=>$fird[5], 'filed_6'=>$fird[6],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'),'is_active'=>'0');
					$insert_a[] = $insert_array;
					$resupdate[] = $callConfig->updateRecord('employee_performance_appraisal_details',$insert_array,'id',$rowid);
				}else{
					$inserexc = "Y";
					$insertsql .= "('".$post['company_id']."','".$post['epa_header_id']."','".$post['appraisal_id']."','".$post['employee_id']."','".$post['assessment_year']."','".$header_id."','".$fird[1]."','".$fird[2]."','".$fird[3]."','".$fird[4]."','".$fird[5]."','".$fird[6]."', '0','0','".date('Y-m-d h:i:s')."','".$post['user_id']."'),";
				}
			}
		}
		if($inserexc == 'Y'){	
			$insertsql = rtrim($insertsql, ',');
			$res = $callConfig->executeQuery($insertsql);
			$returnaray['res'] = $res;
		}

		/*employee quetion part*/
		$responsdetailssql = "INSERT INTO performance_responses_details (company_id, employee_id, assessment_id, question_id, response_option, response_remark, is_active, current_status, created_date, created_by) VALUES ";
		foreach($post['response_option'] as $utype=>$ans){
			foreach($ans as $queid=>$answ){
				$textrepons = $post['response_text'][$utype][$queid];
				$resid = $post['response'][$utype][$queid];
				if($resid !=""){
					$insert_array = array('response_option'=>$answ, 'response_remark'=>$textrepons,'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
					//$insert_a[] = $insert_array;
					$callConfig->updateRecord('performance_responses_details',$insert_array,'id',$resid);
				}else{	
					$responsdetailssql .= "('".$post['company_id']."','".$post['employee_id']."','".$post['epa_header_id']."','".$queid."','".$answ."','".$textrepons."','0','0','".date('Y-m-d h:i:s')."','".$post['user_id']."'),";
				}
			}
		}
		$responssql = rtrim($responsdetailssql, ',');
		$responssql = $callConfig->executeQuery($responssql);
		$returnaray['res'] = $responssql;
		/*employee quetion part*/


		/*upodae hr or report to id*/

		$insert_array = array('self_score'=>$post['text_finalkra_total_employee'],'approved_score'=>$post['text_finalkra_total_manager'],'current_status'=>$post['current_status'],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
		$callConfig->updateRecord('employee_performance_appraisal_headers',$insert_array,'id',$post['epa_header_id']);

	//$returnaray['insertsql'] = $insertsql;
		//$this->checkWorkFlowCriteria(array('tablename'=>'employee_performance_appraisal_headers','company_id'=>$post['company_id'],'rid'=>$post['epa_header_id']));

	//$returnaray['uparray'] = $insert_a;
		$returnaray['resupdate'] = $resupdate;
	//$returnaray['post'] = $post;
		return $returnaray;
	}

	// function publichAppraisal($get){
	// 	global $callConfig;
	// 	$returnarray = array();
	// 	$pssal ="SELECT id,name, name, employee_start FROM performance_setup WHERE company_id =".$get['company_id']." AND id = '".$get['id']."' AND is_active = 0";
	// 	$psres = $callConfig->getRow($pssal);

	// 	$update_transaction_id = array("publish"=>"Y","updated_by"=>$get['user_id'],"updated_date"=>date('Y-m-d h:i:s'));
	// 	$res =$callConfig->updateRecord('performance_setup',$update_transaction_id,'id',$get['id']);
	// 	$returnarray['performance_setup'] = $res;
		
	// 	$condition = "";
	// 	$filtersql = "SELECT ft.id,ft.content, ft.filter_condition FROM filter_tables ft  WHERE ft.company_id = '".$get['company_id']."' AND ft.transaction_id = '".$get['id']."' AND ft.application_tablename ='performance_setup'";
	// 	$returnarray['filtersql'] = $filtersql;
	// 	$filterres = $callConfig->getRow($filtersql);
	// 	if($filterres->id !="" || $filterres->content !=""){
	// 		$show_filter_cond = $filterres->filter_condition;
	// 		$show_filter_cond = explode('&',$show_filter_cond);
	// 		foreach($show_filter_cond as $show_filter_co){
	// 			$fieldname = explode("=",$show_filter_co);
	// 			if($fieldname['0']!="check" AND $fieldname['0']!="table"){
	// 				$show_filter_condion[$fieldname['0']]=$fieldname['1'];
	// 			}
	// 		}
	// 		foreach($show_filter_condion as $keys=>$vale){
	// 			if($vale !=""){
	// 				$condition .= ' AND '.$keys.'  IN ('.$vale.')';
	// 			}
	// 		}
	// 	} 
	// 	$employeesql = "SELECT id,user_id FROM employees WHERE is_active='0' and company_id='".$get['company_id']."' AND (date_of_leaving IS NULL OR date_of_leaving >= '".$psres->employee_start."')".$condition; 
	// 	$returnarray['employeesql'] = $employeesql;
	// 	$employee_goals_list = $callConfig->getAllRows($employeesql);
	// 	$empwithuser = array();
	// 	$insert_heade = "INSERT INTO `employee_performance_appraisal_headers` (`company_id`, `appraisal_id`, `employee_id`, `assessment_year`, `is_active`, `current_status`, `created_by`, `created_date`) VALUES ";
	// 	foreach($employee_goals_list as $employee_goals_list){
	// 		$empwithuser[$employee_goals_list->id] = $employee_goals_list->user_id; 
	// 		$insert_heade .= "('".$get['company_id']."','".$get['id']."','".$employee_goals_list->id."','".$get['assessment_year']."','0','".ST_DRAFT."','".$get['user_id']."','".date('Y-m-d h:i:s')."'),";
	// 	}
	// 	$insertsql = rtrim($insert_heade, ',');
	// 	//print_r($empwithuser);
	// 	//$returnarray['insertsql'] = $insertsql;
	// 	$res = $callConfig->executeQuery($insertsql);

	// 	$getupdatesql ="SELECT epah.id,epah.appraisal_id,epah.employee_id,epah.assessment_year,epah.created_by FROM employee_performance_appraisal_headers epah WHERE epah.company_id = '".$get['company_id']."' AND epah.appraisal_id = '".$get['id']."' AND is_active = 0"; 
	// 	$getupdateres = $callConfig->getAllRows($getupdatesql);
	// 	$returnarray['getupdateres'] = $getupdateres;
	// 	foreach($getupdateres as $getupdatere){
	// 		$update_emp_goal = "UPDATE employee_goals eg, appraisal_key_result_areas kra SET eg.epa_header_id = '".$getupdatere->id."' WHERE eg.company_id = '".$get['company_id']."' AND eg.key_reslut_id = kra.id AND kra.appraisal_id = '".$get['id']."' AND eg.employee_id = '".$getupdatere->employee_id."' AND eg.assessment_year = '".$getupdatere->assessment_year."' AND eg.company_id = kra.company_id AND eg.is_active = kra.is_active AND eg.is_active = 0";
	// 		$assetupdate = $callConfig->executeQuery($update_emp_goal);
	// 		$returnarray['employee_goals'][] = $update_emp_goal;
	// 		if(isset($empwithuser[$getupdatere->employee_id])){
	// 			$user_ids = array('0'=>$empwithuser[$getupdatere->employee_id]);
	// 			$option = "COM_INSERT_PERFORMANCE_APPRAISALS";
	// 			$msg = "performanc appraisal - ".$getupdatere->id."(".$getupdatere->assessment_year.")";
	// 			$notdataa = array('company_id'=>$get['company_id'],'notification_message'=>$msg, 'id'=>$getupdatere->id,'application_table_name'=>'employee_performance_appraisal_headers', 'type'=>'ACTION', 'notification_users'=>$user_ids,'option'=>$option);
	// 			$sendnotification = $this->tosendnotifiations($notdataa);
	// 		}

	// 	} 
	// return $returnarray;
	// }
	/**function getPerFormanceAppraisalReport($get){
		global $callConfig;
		$returnaray = array();
		$sql = "SELECT emp.id FROM employees emp, application_users au WHERE emp.company_id = '".$get['company_id']."' AND emp.company_id = au.company_id AND emp.user_id = au.id AND emp.is_active = 0 AND emp.is_active = au.is_active AND (('1' = '".$get['admin_type']."') OR au.reports_to = '".$get['user_id']."' OR au.id = '".$get['user_id']."' OR hr_buddy = '".$get['emp_id']."') AND emp.is_active = 0";
		$retuenres['empsql'] = $sql;
		$empres = $callConfig->getAllRows($sql);
		$empids = array();
		foreach($empres as $empres){
			$empids[] = $empres->id;
		}
		$empid = implode(",", (array)$empids);
		if($get['ajaxyearlist'] == "Y"){   //admin_type
			$condition = "";
			if($get['self'] =="Y"){
				$empid = $get['emp_id'];
				$condition = " AND epah.employee_id = '".$get['emp_id']."'";
			}else{
				$condition = " AND emp.id IN (".$empid.")";
			}

		$pasettingsql = "SELECT ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date FROM performance_setup ps, employee_performance_appraisal_headers epah WHERE ps.company_id = '".$get['company_id']."' AND ps.is_active = 0 AND ps.company_id = epah.company_id AND ps.is_active = epah.is_active AND ps.id = epah.appraisal_id AND epah.employee_id IN (".$empid.") AND ps.assessment_year = '".$get['assessment_year']."' group by ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date";
		$pasettingres = $callConfig->getAllRows($pasettingsql);
		$idds = array();
		foreach($pasettingres as $ress){
			$idds[] = $ress->id;
		}
		$psid = implode(',', (array)$idds);
		$retuenres['performance_setup'] = $pasettingres;
		$epahlsql = "SELECT epah.id, epah.appraisal_id, epah.employee_id, epah.assessment_year, epah.hr_user_id,epah.current_status, emp.emp_code, emp.display_name, emp.image,epah.self_score,epah.approved_score FROM employee_performance_appraisal_headers epah, employees emp WHERE epah.company_id = '".$get['company_id']."' AND epah.company_id = emp.company_id AND epah.employee_id = emp.id AND epah.is_active = emp.is_active AND epah.is_active = 0 AND epah.appraisal_id IN(".$psid.")".$condition; 
		$epahlres = $callConfig->getAllRows($epahlsql);
		$retuenres['employee_performance_appraisal_headers_Sql'] = $epahlsql;
		$retuenres['employee_performance_appraisal_headers'] = $epahlres;
	}else{
		$pasettingsql = "SELECT ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date FROM performance_setup ps, employee_performance_appraisal_headers epah WHERE ps.company_id = '".$get['company_id']."' AND ps.is_active = 0 AND ps.company_id = epah.company_id AND ps.is_active = epah.is_active AND ps.id = epah.appraisal_id AND epah.employee_id IN (".$empid.") group by ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date";
		$pasettingres = $callConfig->getAllRows($pasettingsql);
		$retuenres['performance_setup'] = $pasettingres;
		$pasettingselfsql = "SELECT ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date FROM performance_setup ps, employee_performance_appraisal_headers epah WHERE ps.company_id = '".$get['company_id']."' AND ps.is_active = 0 AND ps.company_id = epah.company_id AND ps.is_active = epah.is_active AND ps.id = epah.appraisal_id AND epah.employee_id = '".$get['emp_id']."' group by ps.id,ps.name,ps.assessment_year,ps.assessment_start_date,ps.assessment_end_date"; 
		$pasettingselfres = $callConfig->getAllRows($pasettingselfsql);
		$retuenres['performance_setup_self'] = $pasettingselfres;


		$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type,epah.id, epah.employee_id, emp.display_name, emp.emp_code FROM transaction_approval_logs tal, employee_performance_appraisal_headers epah, employees emp WHERE tal.company_id =  '".$get['company_id']."' AND tal.company_id = epah.company_id AND tal.company_id = emp.company_id AND  tal.transaction_table = 'employee_performance_appraisal_headers' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = epah.id  AND epah.employee_id = emp.id AND epah.is_active = 0 AND epah.is_active =emp.is_active";
			$transapprlogres = $callConfig->getAllRows($transapprlogsql);
		$retuenres['transaction_approval_logs'] = $transapprlogres;

	}	
	return $retuenres;
}**/
	function getPerFormanceAppraisalReport($get){
		global $callConfig;
		$returnaray = $employees = array();
		$sql = "SELECT emp.id, emp.display_name, emp.emp_code, emp.image FROM employees emp, application_users au WHERE emp.company_id = '".$get['company_id']."' AND emp.company_id = au.company_id AND emp.user_id = au.id AND emp.is_active = 0 AND emp.is_active = au.is_active AND (('1' = '".$get['admin_type']."') OR au.reports_to = '".$get['user_id']."' OR au.id = '".$get['user_id']."' OR hr_buddy = '".$get['emp_id']."') AND emp.is_active = 0";
		$retuenres['empsql'] = $sql;
		$empres = $callConfig->getAllRows($sql);
		$empids = array();
		foreach($empres as $empres){
			$empids[] = $empres->id;
			$employees[$empres->id] = $empres;
		}
		$empid = implode(",", (array)$empids);
		$retuenres['employees'] = $employees;
		if($get['ajaxyearlist'] == "Y"){   //admin_type
			$condition = "";
			if($get['self'] =="Y"){
				$empid = $get['emp_id'];
				$condition = " AND employee_id = '".$get['emp_id']."'";
			}else{
				$condition = " AND employee_id IN (".$empid.")";
			}

			$epasql = "SELECT id, appraisal_id, employee_id, self_total, ro_total, current_status FROM employee_performance_appraisals WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
			$epares = $callConfig->getAllRows($epasql);
			$appraisalids = array();
			foreach($epares as $ress){
				$appraisalids[$ress->appraisal_id] = $ress->appraisal_id;
				$appraisals[$ress->appraisal_id][] = $ress;
			}
			$aprid = implode(',', (array)$appraisalids);
			$retuenres['employee_performance_appraisals'] = $appraisals;


			$performance_appraisal_setups = array();
			$apsql = "SELECT id, performance_appraisal_name, assessment_year FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' AND id IN(".$aprid.") AND is_active = 0";
			$apres = $callConfig->getAllRows($apsql);
			foreach($apres as $ress){
				$performance_appraisal_setups[$ress->id] = $ress;
			}
			$retuenres['performance_appraisal_setups'] = $performance_appraisal_setups;

			// $pasettingsql = "SELECT pas.id, pas.performance_appraisal_name, pas.assessment_year FROM performance_appraisal_setups pas, employee_performance_appraisals epa WHERE pas.company_id = '".$get['company_id']."' AND pas.company_id = epa.company_id AND pas.id = epa.appraisal_id ".$condition." AND pas.is_active = 0 AND pas.is_active = epa.is_active group by pas.id,pas.assessment_year";
			// $pasettingres = $callConfig->getAllRows($pasettingsql);
			// $idds = array();
			// foreach($pasettingres as $ress){
			// 	$idds[] = $ress->id;
			// }
			// $psid = implode(',', (array)$idds);
			// $retuenres['performance_appraisal_setups'] = $pasettingres;

			// $epahlsql = "SELECT epah.id, epah.appraisal_id, epah.employee_id, epah.assessment_year, epah.hr_user_id,epah.current_status, emp.emp_code, emp.display_name, emp.image,epah.self_score,epah.approved_score FROM employee_performance_appraisal_headers epah, employees emp WHERE epah.company_id = '".$get['company_id']."' AND epah.company_id = emp.company_id AND epah.employee_id = emp.id AND epah.is_active = emp.is_active AND epah.is_active = 0 AND epah.appraisal_id IN(".$psid.") ".$condition; 
			// $epahlres = $callConfig->getAllRows($epahlsql);
			// $retuenres['employee_performance_appraisal_headers_Sql'] = $epahlsql;
			// $retuenres['employee_performance_appraisal_headers'] = $epahlres;

		}else{

			$pasettingsql = "SELECT pas.id, pas.performance_appraisal_name, pas.assessment_year FROM performance_appraisal_setups pas, employee_performance_appraisals epa WHERE pas.company_id = '".$get['company_id']."' AND pas.company_id = epa.company_id AND pas.id = epa.appraisal_id AND epa.employee_id IN (".$empid.") AND pas.is_active = 0 AND pas.is_active = epa.is_active group by pas.id,pas.assessment_year";
			$pasettingres = $callConfig->getAllRows($pasettingsql);
			$retuenres['performance_appraisal_setups'] = $pasettingres;
			
			$pasettingselfsql = "SELECT pas.id, pas.performance_appraisal_name, pas.assessment_year FROM performance_appraisal_setups pas, employee_performance_appraisals epa WHERE pas.company_id = '".$get['company_id']."' AND pas.company_id = epa.company_id AND pas.id = epa.appraisal_id AND epa.employee_id ='".$get['emp_id']."' AND pas.is_active = 0 AND pas.is_active = epa.is_active group by pas.id,pas.assessment_year";
			$pasettingselfres = $callConfig->getAllRows($pasettingselfsql);
			$retuenres['performance_appraisal_setups_self'] = $pasettingselfres;

			$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type,epa.id, epa.employee_id, emp.display_name, emp.emp_code FROM transaction_approval_logs tal, employee_performance_appraisals epa, employees emp WHERE tal.company_id =  '".$get['company_id']."' AND tal.company_id = epa.company_id AND tal.company_id = emp.company_id AND  tal.transaction_table = 'employee_performance_appraisals' AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = epa.id  AND epa.employee_id = emp.id AND epa.is_active = 0 AND epa.is_active =emp.is_active";
				$transapprlogres = $callConfig->getAllRows($transapprlogsql);
			$retuenres['transaction_approval_logs'] = $transapprlogres;	
		}	
	return $retuenres;
}

function getPerFormanceAppraisal($get){ //please removed not working code
	global $callConfig;
	$retuenres = array(); //emp_id
	if($get['ajax']=="Y"){  // Ajax Not Call

		$pa_headersql = "SELECT id,appraisal_id,assessment_year,header_name,header_comment,sub_header_name,sub_header_addmore,sub_header_default_row,header_display_setting,dynamic_page,page_name FROM performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND id = '".$get['header_id']."' AND is_active = 0"; 
		$pa_headeres = $callConfig->getRow($pa_headersql);
		$retuenres['performance_appraisal_headers'] = $pa_headeres;

	}else{
		$employeessql = "SELECT emp.*,(select department_name from departments dep where emp.department_id = dep.id)  department_name,(select designation_name from designations des where emp.designation_id = des.id) designation_name,au.reports_to FROM employees emp, application_users au  WHERE emp.id = '".$get['emp_id']."' AND emp.user_id = au.id  AND emp.company_id = au.company_id AND emp.company_id='".$get['company_id']."' AND emp.is_active=0"; 
		$employeesres = $callConfig->getRow($employeessql);
		$retuenres['employee'] = $employeesres;
		
		$reporttosql = "SELECT emp.display_name,(select designation_name from designations des where emp.designation_id = des.id) designation_name FROM employees emp WHERE emp.user_id = '".$employeesres->reports_to."' AND emp.company_id='".$get['company_id']."' AND emp.is_active=0"; 
		$reporttores = $callConfig->getRow($reporttosql);
		$retuenres['report_to'] = $reporttores;


		$krasql = "SELECT eg.*,kra.kra_type,kra.area_name,kra.weightage kra_weightage FROM employee_goals eg, appraisal_key_result_areas kra WHERE eg.company_id = kra.company_id AND eg.company_id = '".$get['company_id']."' AND eg.key_reslut_id = kra.id AND eg.employee_id = '".$get['emp_id']."' AND eg.assessment_year = '".$get['assessment_year']."' AND eg.is_active = 0 AND eg.is_active = kra.is_active"; 
		$krares = $callConfig->getAllRows($krasql);
		$retuenres['kra_list'] = $krares;

		$arssql = "SELECT score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$retuenres['arslabs'] = $arsres;

		$pa_headersql = "SELECT id,appraisal_id,assessment_year,header_name,header_comment,sub_header_name,sub_header_addmore,sub_header_default_row,header_display_setting,dynamic_page,page_name FROM performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND appraisal_id ='".$get['appraisal_id']."' "; 
		$pa_headeres = $callConfig->getAllRows($pa_headersql);
		$retuenres['performance_appraisal_headers'] = $pa_headeres;

		$epappraisalsql = "SELECT id,header_id,filed_1,filed_2,filed_3,filed_4,filed_5 FROM employee_performance_appraisal_details WHERE company_id = '".$get['company_id']."' AND employee_id = '".$get['emp_id']."' AND appraisal_id = '".$get['appraisal_id']."' AND assessment_year = '".$get['assessment_year']."' AND is_active = 0 "; 
		$epappraisalres = $callConfig->getAllRows($epappraisalsql);
		$retuenres['employee_performance_appraisals'] = $epappraisalres;
		$performance_setupsql = "SELECT id,name,publish FROM performance_setup WHERE  company_id = '".$get['company_id']."'  AND  id='".$get['appraisal_id']."' AND is_active = 0 "; 
		$performance_setupres = $callConfig->getRow($performance_setupsql);
		$retuenres['performance_setup'] = $performance_setupres;


		$employee_assessment_que_selected = "select * from ".TBL_PERFORMANCE_SETUP_QUESTIONS." where company_id='".$get['company_id']."' and is_active='0' and assessment_id = ".$get['appraisal_id'];
		$assessment_list_que_selected = $callConfig->getAllRows($employee_assessment_que_selected);
		foreach ($assessment_list_que_selected as $assessqselect) {
			$assessments_que_selected[$assessqselect->id] = $assessqselect;
			$assessqselect->number_of_options = count((array)json_decode($assessqselect->question_option,1));
			if($assessqselect->assign_type==0){
				$emp_questions[$assessqselect->id] = $assessqselect;
			}else if($assessqselect->assign_type==1) {
				$mgr_questions[$assessqselect->id] = $assessqselect;
			}
			else if($assessqselect->assign_type==2) {
				$reviewmgr_questions[$assessqselect->id] = $assessqselect;
			}
			else if($assessqselect->assign_type==3) {
				$peer_questions[$assessqselect->id] = $assessqselect;
			}
		}
		$retuenres['assessment_questions_selected'] = $assessments_que_selected;
		$retuenres['emp_questions'] = $emp_questions;
		$retuenres['mgr_questions'] = $mgr_questions;
		$retuenres['reviewmgr_questions'] = $reviewmgr_questions;
		$retuenres['peer_questions'] = $peer_questions;
	}
	$peresdetailssql = "select id,question_id,response_option,response_remark from performance_responses_details where company_id='".$get['company_id']."' and is_active='0' and assessment_id = ".$get['epa_header_id'];
	$peresdetailsres = $callConfig->getAllRows($peresdetailssql);
	$retuenres['performance_responses_details'] = $peresdetailsres;


	$sendemployedetails = array('employee_id'=>$get['emp_id'],'company_id'=>$get['company_id']);
	$returnresp = $this->getempconnectwithempid($sendemployedetails);
	$retuenres['buddy_details'] = $returnresp;

	$empsql = "select id,user_id from employees where company_id='".$get['company_id']."' and is_active='0' and id = '".$get['emp_id']."'";
	$empres = $callConfig->getRow($empsql);
	$retuenres['employees'] = $empres;


	$pasettingsql = "SELECT appraisal_setting_id FROM performance_appraisal_setting_histories WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND appraisal_id='".$get['appraisal_id']."' AND applicable = 0"; 
	$pasettingres = $callConfig->getAllRows($pasettingsql);
	$retuenres['performance_appraisal_setting_histories'] = $pasettingres;
	return $retuenres;
}








































/*New Data*/
	function insertPerforManceAppraisalSetups($get,$post){
		global $callConfig;
		$returnarray = array();
		$insert_array = array('performance_appraisal_name'=>$post['performance_appraisal_name'],'assessment_year'=>$post['assessment_year'],'url'=>$post['url'],'file_name'=>$post['file_name'],'assessment_start_date'=>$this->dateconvertdbformat($post['assessment_start_date']),'assessment_end_date'=>$this->dateconvertdbformat($post['assessment_end_date']),'self_assessment_start_date'=>$this->dateconvertdbformat($post['self_assessment_start_date']),'self_assessment_end_date'=>$this->dateconvertdbformat($post['self_assessment_end_date']),'manager_assessment_start_date'=>$this->dateconvertdbformat($post['manager_assessment_start_date']),'manager_assessment_end_date'=>$this->dateconvertdbformat($post['manager_assessment_end_date']),'reviewing_start_date'=>$this->dateconvertdbformat($post['reviewing_start_date']),'reviewing_end_date'=>$this->dateconvertdbformat($post['reviewing_end_date']),'action_frequency'=>$post['action_frequency'],'appraisal_setup_type'=>$post['appraisal_setup_type'],'appraisal_bell_curve_id'=>$post['appraisal_bell_curve_id']);
		if($post['rid'] !=""){
			$insert_array['updated_by'] = $post['userid'];
			$insert_array['updated_date'] = date('Y-m-d h:i:s');
			$insert = $callConfig->updateRecord('performance_appraisal_setups',$insert_array,'id',$post['rid']);
			$rid = $post['rid'];
		}else{
			$insert_array['company_id'] = $post['company_id'];
			$insert_array['is_active'] = '0';
			$insert_array['current_status'] = '0';
			$insert_array['created_by'] = $post['userid'];
			$insert_array['created_date'] = date('Y-m-d h:i:s');
			$insert = $callConfig->insertRecord('performance_appraisal_setups',$insert_array);
			$rid = $insert['LASTID'];
		}
		$returnarray['insert'] = $insert;
		$update_transaction_id = array("transaction_id"=>$rid);
		if($get['filtertbl_rowid'] !=""){
			$res_filters =$callConfig->updateRecord(TBL_FILTER_TABLES,$update_transaction_id,'id',$get['filtertbl_rowid']);
			$returnarray['res_filters'] = $res_filters;
		}
		return $returnarray;
	}
	function getPerforManceAppraisalSetups($get){
		global $callConfig;
		$return = array();
		$passql ="SELECT * FROM performance_appraisal_setups WHERE company_id =".$get['company_id']." AND id = '".$get['rid']."' AND is_active = 0";
		$pasres = $callConfig->getRow($passql);
		//$return['performance_appraisal_setups_sql'] = $passql;
		$return['performance_appraisal_setups'] = $pasres;

		$filter_tablessql ="SELECT id FROM filter_tables WHERE company_id =".$get['company_id']." AND transaction_id = '".$get['rid']."' AND application_tablename ='performance_appraisal_setups' AND is_active = 0";
		$filter_tablesres = $callConfig->getRow($filter_tablessql);
		$return['filter_tables'] = $filter_tablesres;

		$passql ="SELECT * FROM performance_appraisal_questions WHERE company_id =".$get['company_id']." AND appraisal_id = '".$get['rid']."' AND is_active = 0";
		$pasres = $callConfig->getAllRows($passql);
		$return['performance_appraisal_questions'] = $pasres;


		$passql ="SELECT id, bell_curve_name FROM appraisal_bell_curves WHERE company_id =".$get['company_id']." AND is_active = 0";
		$pasres = $callConfig->getAllRows($passql);
		$return['appraisal_bell_curves'] = $pasres;
		

		if($get['preview']=="Y"){
			$krasql = "SELECT id,area_name FROM appraisal_key_result_areas WHERE company_id='".$get['company_id']."' AND appraisal_id = '".$get['rid']."' AND is_active=0"; 
			$krares = $callConfig->getAllRows($krasql);
			foreach($krares as $krares11){
				$akraresult[$krares11->id] = $krares11;
				$kraarray[$krares11->id] = $krares11->id;
			}
			$return['appraisal_key_result_areas'] = $akraresult;
			$kraids = implode(",",(array)$kraarray);  

			$kpisql = "SELECT akpi.id,akpi.indicator_name,akpi.key_reslut_id,akpi.weightage,akpi.model,akpi.uom FROM appraisal_key_performance_indicators akpi WHERE akpi.company_id='".$get['company_id']."'AND akpi.is_active=0 AND akpi.key_reslut_id IN(".$kraids.")"; 
			$kpires = $callConfig->getAllRows($kpisql);
			$return['appraisal_key_performance_indicators'] = $kpires;

			$pa_headersql = "SELECT id,appraisal_id,assessment_year,header_name,header_comment,sub_header_name,sub_header_addmore,sub_header_default_row,header_display_setting,dynamic_page,page_name FROM performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND appraisal_id ='".$get['rid']."' "; 
			$pa_headeres = $callConfig->getAllRows($pa_headersql);
			$return['performance_appraisal_headers_sql'] = $pa_headersql;
			$return['performance_appraisal_headers'] = $pa_headeres;


		}
		return $return;

	}
	function insertPerformanceAppraisalQuestions($get,$post){
		global $callConfig;
		$returnarray = array();
		$updateset = array("is_active"=>null);
		$res_filters =$callConfig->updateRecord('performance_appraisal_questions',$updateset,'appraisal_id',$post['appraisal_id']);

		foreach($post['question'] as $key=>$val){
			$display_answer = json_encode($post['display_answer'][$key]);
			$display_question = json_encode($post['display_question'][$key]);
			$question_options = json_encode($post['question_options'][$key]);
			$insert_array = array('appraisal_id'=>$post['appraisal_id'],'question'=>$post['question'][$key],'description'=>$post['description'][$key] ,'answer_type'=>$post['answer_type'][$key] ,'display_answer'=>$display_answer ,'display_question'=>$display_question,'question_options'=>$question_options,'max_ratting'=>$post['max_ratting'][$key],'is_active'=>0,'attachment'=>$post['attachment'][$key]);
			if($post['performance_appraisal_question_id'][$key] !=""){
				$insert_array['updated_by'] = $post['userid'];
				$insert_array['updated_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->updateRecord('performance_appraisal_questions',$insert_array,'id',$post['performance_appraisal_question_id'][$key]);
				$returnarray['insert'][] = $insert;
			}else{
				$insert_array['company_id'] = $post['company_id'];
				$insert_array['current_status'] = '0';
				$insert_array['created_by'] = $post['userid'];
				$insert_array['created_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->insertRecord('performance_appraisal_questions',$insert_array);
				$returnarray['insert'][] = $insert;
			}
		}
		//$returnarray['insert'] = $insert;
		return $return;
	}
	function getKipAndKraDetails($get){// getOrganizationGoalsSetails
		global $callConfig;
		$retuenres = array();
		$kratable  = $get['kratable'];
		$kpitable = $get['kpitable'];
		$kpitasktable = $get['kpitasktable'];
		if($get['kpi_kra_popup'] == "Y"){
			$modellistsql = "SELECT id,kpi_id,task_name,description,task_shot_code,task_frequency,mandatory FROM ".$kpitasktable." WHERE company_id='".$get['company_id']."' AND is_active=0 AND id IN(".$get['kpi_tasks_ids'].")"; 
			$modellistres = $callConfig->getAllRows($modellistsql);
			$retuenres['kpi_taskslist'] = $modellistres;

			$freqlistsql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='TASKFREQUENCY' AND is_active=0"; 
			$freqlistres = $callConfig->getAllRows($freqlistsql);
			$retuenres['task_frequency_list'] = $freqlistres;
		}else{
			$modellistsql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='KPIMODAL' AND is_active=0"; 
			$modellistres = $callConfig->getAllRows($modellistsql);
			$retuenres['modellist'] = $modellistres;

			$uomsql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='KPIUOM' AND is_active=0"; 
			$uomres = $callConfig->getAllRows($uomsql);
			$retuenres['uomllist'] = $uomres;

			if($get['appraisal_id'] !=""){
				$kracondition = " AND appraisal_id = '".$get['appraisal_id']."'";
			}
			$krasql = "SELECT id,area_name,description,weightage FROM ".$kratable." WHERE company_id='".$get['company_id']."' AND is_active=0".$kracondition; 
			$krares = $callConfig->getAllRows($krasql);
			$retuenres['kralist'] = $krares;

			foreach($krares as $krares11){
				$kraarray[] = $krares11->id;
			}
			$kraids = implode(",",(array)$kraarray);  

			$kpisql = "SELECT kpi.id,kpi.indicator_name,kpi.key_reslut_id,kpi.weightage,kpi.model,kpi.uom,kpi.display_sequence,kpi.role_id,(SELECT GROUP_CONCAT(kt.id SEPARATOR ',') AS id FROM ".$kpitasktable." kt WHERE kpi.company_id = kt.company_id AND kpi.id = kt.kpi_id AND kpi.is_active = kt.is_active GROUP BY kt.kpi_id) as kpi_tasks_ids FROM ".$kpitable." kpi WHERE kpi.company_id='".$get['company_id']."'  AND kpi.is_active=0 AND kpi.key_reslut_id IN(".$kraids.")"; 
			$kpires = $callConfig->getAllRows($kpisql);
			$retuenres['kpilist'] = $kpires;

			// $performance_setupsql = "SELECT id,name FROM performance_setup WHERE  company_id = '".$get['company_id']."'  AND  id='".$get['appraisal_id']."' AND is_active = 0 "; 
			// $performance_setupres = $callConfig->getRow($performance_setupsql);
			// $retuenres['performance_setup'] = $performance_setupres;

		}
		return $retuenres;
	}
	function addkra($get,$post){
		global $callConfig;
		$kratable  = $post['kratable'];
		if($post['kra_id'] !=""){
			$insert_array = array('kra_type'=>$post['kra_type'],'area_name'=>$post['area_name'],'description'=>$post['description'],'weightage'=>$post['weightage'],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
			if($post['appraisal_id'] !=""){
				$insert_array['appraisal_id'] = $post['appraisal_id'];
			}
			$insert = $callConfig->updateRecord($kratable,$insert_array,'id',$post['kra_id']);
		}else{
			$insert_array = array('company_id'=>$post['company_id'],'kra_type'=>$post['kra_type'],'area_name'=>$post['area_name'],'description'=>$post['description'],'weightage'=>$post['weightage'],'is_active'=>'0','current_status'=>'0','created_by'=>$post['user_id'],'created_date'=>date('Y-m-d h:i:s'));
			if($post['appraisal_id'] !=""){
				$insert_array['appraisal_id'] = $post['appraisal_id'];
			}
			$insert = $callConfig->insertRecord($kratable,$insert_array);
		}
		return $insert;
	}
	function addorupdatekpi($get,$post){
		global $callConfig; 
		$returnres = array();
		$kratable  = $post['kratable'];
		$kpitable = $post['kpitable'];
		$kpitasktable = $post['kpitasktable'];
		foreach ($post['name'] as  $key1=>$values) {
			$updateset = array("is_active"=>null);
			$upsql[] =$callConfig->updateRecord($kpitable,$updateset,'key_reslut_id',$key1);
			foreach ($values as $key=>$name) {
				if($name !=""){
					$kpitaskid = $post['kpi_tasks_ids'][$key1][$key];
					$insert_array = array('company_id'=>$get['company_id'],'indicator_name'=>$name,'key_reslut_id'=>$key1,'weightage'=>$post['weightage'][$key1][$key],'model'=>$post['model'][$key1][$key],'uom'=>$post['uom'][$key1][$key]);
					if($post['kpi_id'][$key1][$key]!="" ){
						$insert_array['updated_by'] =$get['user_id'];
						$insert_array['updated_date'] =date('Y-m-d h:i:s');
						$insert_array['is_active'] ='0'; 
						$kpiid = $post['kpi_id'][$key1][$key];
						$insert['upd'][] = $callConfig->updateRecord($kpitable,$insert_array,'id',$post['kpi_id'][$key1][$key]);
					}else{
						$insert_array['is_active'] ='0'; 
						$insert_array['current_status'] ='0';
						$insert_array['created_by'] =$get['user_id'];
						$insert_array['created_date']=date('Y-m-d h:i:s');
						$resins = $callConfig->insertRecord($kpitable,$insert_array);
						$insert['ins'][] =$resins;
						$kpiid  = $resins['LASTID'];
					}
					if($kpitaskid !=""){ //any task added this time used only
						$update_query = "UPDATE ".$kpitasktable." SET kpi_id = ".$kpiid." WHERE id IN(".$kpitaskid.")";
						$sql[]  = $update_query;
						$insert['kpitaskup'][]  = $callConfig->executeQuery($update_query);
					}
				}
			}
		}
		$returnre['key_performance_indicators'] = $insert;
		$returnre['sql'] = $sql;
		$returnre['post'] = $post;
		$returnre['upsql'] = $upsql;;
		return $returnre;
	}
	function insertAppraisalKRA($get){
		global $callConfig;
		$returnaray = array();
		$insertkra = "INSERT INTO `appraisal_key_result_areas`(`company_id`, `appraisal_id`,`kra_id`,`area_name`, `description`, `weightage`, `kra_type`, `is_active`, `current_status`, `created_date`, `created_by`)SELECT '".$get['company_id']."','".$get['appraisal_id']."',`id`, `area_name`, `description`, `weightage`, `kra_type`, '0','0','".date('Y-m-d h:i:s')."','".$get['user_id']."' FROM `key_result_areas` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$insertkrares = $callConfig->executeQuery($insertkra);
		$returnara['insertkrares'] =$insertkrares;
		$returnara['insertkraressql'] =$insertkra;

		$insertkpi = "INSERT INTO `appraisal_key_performance_indicators`(`company_id`, `kpi_id`, `indicator_name`, `key_reslut_id`, `weightage`, `model`, `uom`, `display_sequence`, `role_id`, `version_no`, `is_active`, `current_status`,`created_date`, `created_by`) 
		SELECT  '".$get['company_id']."',`id`,`indicator_name`,(SELECT id FROM appraisal_key_result_areas akra WHERE kpi.company_id =akra.company_id AND kpi.key_reslut_id = akra.kra_id AND akra.appraisal_id = '".$get['appraisal_id']."') akra_id, `weightage`, `model`, `uom`, `display_sequence`, `role_id`, `version_no`, '0','0','".date('Y-m-d h:i:s')."','".$get['user_id']."' FROM key_performance_indicators kpi WHERE kpi.company_id = '".$get['company_id']."' AND key_reslut_id IN(SELECT id FROM `key_result_areas` WHERE company_id = '".$get['company_id']."' AND is_active = 0) AND kpi.is_active = 0";
		$insertkpires = $callConfig->executeQuery($insertkpi);
		$returnara['insertkpiresres'] =$insertkpires;
		$returnara['insertkpiressql'] =$insertkpi;
		// $inserttask = "INSERT INTO `appraisal_kpi_tasks`(`company_id`, `kpi_id`, `task_name`, `description`, `task_shot_code`, `task_frequency`, `mandatory`, `is_active`, `current_status`,`created_date`, `created_by`) SELECT `company_id`, (SELECT id FROM appraisal_key_performance_indicators akpi WHERE kpi_tasks.company_id =akpi.company_id AND kpi_tasks.kpi_id = akpi.kpi_id) kpi_ids, `task_name`, `description`, `task_shot_code`, `task_frequency`, `mandatory`, '0','0',`created_date`, `created_by` FROM `kpi_tasks`  WHERE kpi.company_id = '".$get['company_id']."' AND kpi.is_active = 0";
		// $insertres = $callConfig->executeQuery($inserttask);
		// $returnara['insertres'] =$insertres;
		// $returnara['insertressql'] =$inserttask;
		return $returnara;
	}
	function getorganizationgoalcheck($get){
	global $callConfig;
	$where="";
	$returnaray = array();
	$filtersql = "SELECT ft.id,ft.content, ft.filter_condition FROM filter_tables ft  WHERE ft.company_id = '".$get['company_id']."' AND ft.transaction_id = '".$get['appraisal_id']."' AND ft.application_tablename ='performance_setup'";
	$filterres = $callConfig->getRow($filtersql);
	$returnarray['filtersql'] = $filtersql;
	if($filterres->id !="" || $filterres->content !=""){
		$show_filter_cond = $filterres->filter_condition;
		$show_filter_cond = explode('&',$show_filter_cond);
		foreach($show_filter_cond as $show_filter_co){
			$fieldname = explode("=",$show_filter_co);
			if($fieldname['0']!="check" AND $fieldname['0']!="table"){
				$show_filter_condion[$fieldname['0']]=$fieldname['1'];
			}
		}
		foreach($show_filter_condion as $keys=>$vale){
			if($vale !=""){
				if($keys == 'department_id'){
					$dipartcondition .= ' AND dept.id IN ('.$vale.')';
				}elseif($keys == 'location_id'){
					$locationcondition .= ' AND loc.id IN ('.$vale.')';
				}elseif($keys == 'designation_id'){
					$designationcondition .= ' AND jt.id IN ('.$vale.')';
				}
				
			}
		}
	}
	if($get['department_id'] !="" AND $get['department_id'] !="all"){
		$ids = $get['department_id'];
		$where .= " AND dept.id IN(".$ids.")";
	}else{
		$where .= $dipartcondition;
	}
	if($get['designation_id'] !="" AND $get['designation_id'] !="all"){
		$ids = $get['designation_id'];
		$where  .= " AND jt.id IN(".$ids.")";
	}else{
		$where .= $designationcondition;
	}
	if($get['location_id'] !="" AND $get['location_id'] !="all"){
		$ids = $get['location_id'];
		$where  .= " AND loc.id IN(".$ids.")";
	}else{
		$where .= $locationcondition;
	}
	$ogsql = "SELECT og.job_title_id, og.department_id, og.location_id FROM organization_goals og, appraisal_key_result_areas kra WHERE og.company_id = '".$get['company_id']."' AND og.company_id = kra.company_id AND og.key_reslut_id = kra.id AND kra.appraisal_id = '".$get['appraisal_id']."' AND og.is_active = kra.is_active AND  og.is_active =0";
	$ogres = $callConfig->getAllRows($ogsql);
	$returnaray['ogres']=$ogres;
	$inserttask = "SELECT loc.id loc_id,loc.location_name, dept.id dept_id ,dept.department_name , jt.id desg_id, jt.job_title FROM departments dept, job_titles jt, locations loc WHERE loc.company_id = '".$get['company_id']."' AND loc.is_active =0 ".$where;
	$pasettingres = $callConfig->getAllRows($inserttask);
	$returnaray['res']=$pasettingres;
	return $returnaray;
}
function getKRATagging($get){
	global $callConfig;
	$retuenres = array();
	if($get['edit']=='Y'){
		$krasql ="SELECT og.id as ogid,kra.id,og.job_title_id,og.department_id,og.location_id,og.key_performance_id,og.traget,og.assessment_frequency FROM organization_goals og, appraisal_key_result_areas kra WHERE og.company_id = kra.company_id AND og.company_id = '".$get['company_id']."' AND og.key_reslut_id = kra.id AND og.job_title_id = '".$get['des_id']."' AND og.department_id = '".$get['dep_id']."' AND og.location_id = '".$get['loc_id']."' AND og.is_active = 0 AND og.is_active = kra.is_active AND kra.appraisal_id= '".$get['appraisal_id']."' AND kra.appraisal_id= '".$get['appraisal_id']."'";
		$krares = $callConfig->getAllRows($krasql);
		$retuenres['kralid'] = $krares;
	}
	$krasql = "SELECT id,area_name,description FROM appraisal_key_result_areas WHERE company_id='".$get['company_id']."' AND is_active=0 AND appraisal_id= '".$get['appraisal_id']."'"; 
	$krares = $callConfig->getAllRows($krasql);
	$retuenres['kralist'] = $krares;

	$filtersql = "SELECT ft.id,ft.content, ft.filter_condition FROM filter_tables ft  WHERE ft.company_id = '".$get['company_id']."' AND ft.transaction_id = '".$get['appraisal_id']."' AND ft.application_tablename ='performance_setup'";
	$filterres = $callConfig->getRow($filtersql);
	$returnarray['filtersql'] = $filtersql;
	if($filterres->id !="" || $filterres->content !=""){
		$show_filter_cond = $filterres->filter_condition;
		$show_filter_cond = explode('&',$show_filter_cond);
		foreach($show_filter_cond as $show_filter_co){
			$fieldname = explode("=",$show_filter_co);
			if($fieldname['0']!="check" AND $fieldname['0']!="table"){
				$show_filter_condion[$fieldname['0']]=$fieldname['1'];
			}
		}
		foreach($show_filter_condion as $keys=>$vale){
			if($vale !=""){
				if($keys == 'department_id'){
					$dipartcondition .= ' AND id IN ('.$vale.')';
				}elseif($keys == 'location_id'){
					$locationcondition .= ' AND id IN ('.$vale.')';
				}elseif($keys == 'designation_id'){
					$designationcondition .= ' AND id IN ('.$vale.')';
				}
				
			}
		}
	}
	$departsql = "SELECT id,department_name FROM departments WHERE company_id='".$get['company_id']."'  ".$dipartcondition." AND is_active=0"; 
	$departres = $callConfig->getAllRows($departsql);
	$retuenres['department_list'] = $departres;
	
	$desingsql = "SELECT id,job_title as designation_name FROM job_titles WHERE company_id='".$get['company_id']."'  ".$designationcondition." AND is_active=0"; 
	$desingres = $callConfig->getAllRows($desingsql);
	$retuenres['designation_list'] = $desingres;

	$locasql = "SELECT id,location_name FROM locations WHERE company_id='".$get['company_id']."'  ".$locationcondition."  AND is_active=0"; 
	$locares = $callConfig->getAllRows($locasql);
	$retuenres['location_list'] = $locares;
	return $retuenres;
}
function insertGoalData($get,$post){
		global $callConfig;
		$returnaray = array();
		$returnaray['post'] = $post;
		$formtable = '';
		$where = '';
		$select = '';
		// if($post['inserttype'] =="OLD"){
		// 	$updatesql = "UPDATE `organization_goals` SET is_active=NULL WHERE id IN(".$post['organization_goalsidd'].")";
		// 	$returnaray['updatesql'] = $updatesql;
		// 	$callConfig->executeQuery($updatesql);
		// 	$updatesql = "DELETE FROM `employee_goals` WHERE key_performance_id IN(".$post['organization_goalsidd'].")";
		// 	$returnaray['updatesql'] = $updatesql;
		// 	$callConfig->executeQuery($updatesql);	
		// }
		$kra_ids = implode(',', (array)$post['kra_ids']);
		$locatdepdesignatio =  json_decode($post['locatdepdesignatio'],1);
	// if($post['department_id'] !=""){
	// 	$ids = implode(',', json_decode($post['department_id'],1));
	// 	$where .= " AND kra.company_id = dept.company_id AND dept.id IN(".$ids.")";
	// }
	// if($post['designation_id'] !=""){
	// 	$ids = implode(',', json_decode($post['designation_id'],1));
	// 	$where  .= " AND kra.company_id = jt.company_id AND jt.id  IN(".$ids.")";
	// }
	// if($post['location_id'] !=""){
	// 	$ids = implode(',', json_decode($post['location_id'],1));
	// 	$where  .= " AND kra.company_id = loc.company_id AND loc.id  IN(".$ids.")";
	// }
	//$sql = "SELECT  kra.id kra_id,kpi.id kpi_id, kpi.indicator_name, kpi.weightage, kpi.model,kpi.uom ,kpi.display_sequence, dept.id dept_id, jt.id desg_id , loc.id loc_id FROM appraisal_key_result_areas kra, appraisal_key_performance_indicators kpi, departments dept, job_titles jt, locations loc WHERE kra.company_id = '".$post['company_id']."'  AND kra.id IN(".$kra_ids.") AND kra.id = kpi.key_reslut_id ".$where;

	$sql = "SELECT akra.id kra_id,akpi.id kpi_id FROM appraisal_key_result_areas akra, appraisal_key_performance_indicators akpi WHERE akra.company_id = '".$post['company_id']."' AND akra.company_id =akpi.company_id AND akra.id IN(".$kra_ids.") AND akra.id = akpi.key_reslut_id AND akra.is_active = akpi.is_active";
	$returnaray['getemployee_sql'] = $sql;
	$checkmenyrows  = $callConfig->getAllRows($sql);
	if(count((array($checkmenyrows))) !=0){
		$setinsertsql = 1;
		$insertsql = "INSERT INTO `organization_goals`(`company_id`,`appraisal_id`, `key_reslut_id`, `key_performance_id`, `assessment_frequency`, `traget`, `job_title_id`, `department_id`, `location_id`, `is_active`, `current_status`, `created_date`, `created_by`) VALUES ";
		foreach($checkmenyrows as $resultrow){ 
			foreach($locatdepdesignatio as $locid =>$loclist){
				foreach($loclist as $dipid =>$deplist){
					foreach($deplist as $desid =>$deslist){
						$goalid = $post['goalsid'][$resultrow->kra_id][$resultrow->kpi_id];
						if($goalid != ""){
							$insert_array = array('assessment_frequency'=>$post['assessment_frequency'][$resultrow->kra_id][$resultrow->kpi_id],'traget'=>$post['traget'][$resultrow->kra_id][$resultrow->kpi_id],'is_active'=>'0','updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
							$insert = $callConfig->updateRecord('organization_goals',$insert_array,'id',$goalid);

						}else{
							$setinsertsql = 0;
							$insertsql .= "('".$post['company_id']."','".$post['appraisal_id']."','".$resultrow->kra_id."','".$resultrow->kpi_id."','".$post['assessment_frequency'][$resultrow->kra_id][$resultrow->kpi_id]."','".$post['traget'][$resultrow->kra_id][$resultrow->kpi_id]."','".$desid."','".$dipid."','".$locid."','0','0','".date('Y-m-d h:i:s')."','".$post['user_id']."'),";
						}		 
					}
				}	
			}
		}
		if($setinsertsql == '0'){
			$insertsql = rtrim($insertsql, ',');
			$res = $callConfig->executeQuery($insertsql);
			$returnaray['result_sql'] = $insertsql;
			$returnaray['result'] = $res;
			$returnaray['LASTID'] = $res['LASTID'];
		}
	}

	// if(isset($res['LASTID'])){
	// 	$employee_goals = "INSERT INTO `employee_goals`(`company_id`, `employee_id`, `assessment_year`, `indicator_name`, `key_reslut_id`,`key_performance_id`, `weightage`, `model`, `uom`,`assessment_frequency`, `traget`, `is_active`, `current_status`, `created_date`, `created_by`) SELECT '".$post['company_id']."',emp.id,og.assessment_year,og.indicator_name,og.key_reslut_id,og.key_performance_id,og.weightage,og.model, og.uom,og.assessment_frequency ,og.traget,0,0,'".date('Y-m-d h:i:s')."','".$post['user_id']."' FROM organization_goals og, employees emp, appraisal_key_result_areas kra  WHERE og.company_id ='".$post['company_id']."' AND og.assessment_year = '".$post['assessment_year']."' AND kra.appraisal_id = '".$post['appraisal_id']."' AND kra.id = og.key_reslut_id AND og.company_id = emp.company_id AND og.company_id = kra.company_id AND og.job_title_id = emp.job_title_id AND og.department_id = emp.department_id AND og.location_id = emp.location_id AND emp.is_active = 0 AND emp.is_active = og.is_active AND emp.is_active = kra.is_active AND kra.id IN(".$kra_ids.")";
	// 	$res = $callConfig->executeQuery($employee_goals);
	// 	$returnaray['employee_goals'] = $employee_goals;
	// }
	$returnaray['insertsql'] = $insertsql;  
	$returnaray['max'] = $sql;
	$returnaray['checkmenyrows'] = $checkmenyrows; 
	$returnaray['post'] = $post; 
	return $returnaray;
}
function pmsDashboard1($get){
		global $callConfig;
		$return = array();
		$performance_setupsql = "SELECT id, name,assessment_year FROM performance_setup WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY assessment_start_date DESC"; 
		$performance_setupres = $callConfig->getAllRows($performance_setupsql);
		$return['performance_setup'] = $performance_setupres;
		if($get['appraisal_id'] ==""){
			$performance_setup_id = $performance_setupres[0]->id;
		}else{
			$performance_setup_id = $get['appraisal_id'];
		}
		$return['performance_setup_id'] =$performance_setup_id;
		$sql = "SELECT employee_id, hr_user_id, finance_user_id, self_score, approved_score, current_status FROM employee_performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND appraisal_id = '".$performance_setup_id."' AND is_active = 0";
		$res=$callConfig->getAllRows($sql);
		$return['employee_performance_appraisal_headers'] = $res;

		$empsql = "SELECT id,display_name,image FROM employees WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$empres=$callConfig->getAllRows($empsql);
		foreach($empres as $empress){
			$empdeatils[$empress->id] = $empress;
		}
		$return['employees'] = $empdeatils;

		$arssql = "SELECT score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$return['arslabs'] = $arsres;
		return $return;
	}


// function pmsDashboard2($get){
// 	global $callConfig;
// 	$return = array();
// 	$performance_setupsql = "SELECT id, name,assessment_year FROM performance_setup WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY assessment_start_date DESC"; 
// 	$performance_setupres = $callConfig->getAllRows($performance_setupsql);
// 	$return['performance_setup'] = $performance_setupres;
// 	if($get['appraisal_id'] ==""){
// 		$performance_setup_id = $performance_setupres[0]->id;
// 	}else{
// 		$performance_setup_id = $get['appraisal_id'];
// 	}
// 	$return['performance_setup_id'] =$performance_setup_id;
// 	$sql = "SELECT epah.employee_id, epah.hr_user_id, epah.finance_user_id, epah.self_score, epah.approved_score, epah.current_status,emp.display_name,emp.image,emp.joining_date FROM employee_performance_appraisal_headers epah, employees emp WHERE epah.company_id = '".$get['company_id']."' AND epah.appraisal_id = '".$performance_setup_id."' AND epah.hr_user_id = '336' AND epah.employee_id = emp.id AND epah.is_active =emp.is_active AND epah.is_active = 0";
// 	$res1=$callConfig->getAllRows($sql);
// 	foreach($res1 as $res_1){
// 		if($res_1->joining_date >= date('Y-01-01')){
// 			$empidthisyearjoin[$res_1->employee_id] =$res_1->employee_id;
// 		}
// 		$empid[$res_1->employee_id] =$res_1->employee_id;
// 		$return['my_team'][] = $res_1;
// 	}

// 	$return['employee_performance_appraisal_headers'] = $empidthisyearjoin;
// //AND eg.employee_id  IN(".implode(",",(array)$emoid).")
// 	$listorg_goal_sql = "SELECT eg.employee_id,eg.indicator_name,akra.id akra_id,akra.area_name, eg.weightage, eg.assessment_frequency, eg.traget, eg.self_achievement, eg.approved_achievement, eg.approved_score FROM employee_goals eg, appraisal_key_result_areas akra WHERE eg.company_id = '".$get['company_id']."' AND eg.company_id = akra.company_id AND eg.key_reslut_id = akra.id AND akra.appraisal_id = '".$performance_setup_id."' AND eg.employee_id  IN(".implode(",",(array)$empid).") AND eg.is_active = 0 AND eg.is_active = akra.is_active";
// 	$listgoals = $callConfig->getAllRows($listorg_goal_sql);
// 	foreach($listgoals as $listgoal){
// 		if(isset($empidthisyearjoin[$listgoal->employee_id])){
// 			$return['new_hierd_employee_goals'][] = $listgoal;
// 		}

// 	}
// 	$org_goal_sql = "SELECT eg.indicator_name,akra.id akra_id,akra.area_name, eg.weightage, eg.assessment_frequency, eg.traget, eg.self_achievement, eg.approved_achievement, eg.approved_score FROM employee_goals eg, appraisal_key_result_areas akra WHERE eg.company_id = '".$get['company_id']."' AND eg.company_id = akra.company_id AND eg.key_reslut_id = akra.id AND akra.appraisal_id = '".$performance_setup_id."' AND eg.employee_id = '".$get['employee_id']."' AND eg.is_active = 0 AND eg.is_active = akra.is_active";
// 	$listgoal = $callConfig->getAllRows($org_goal_sql);
// 	$return['employee_goals'] = $listgoal;

// 	$arssql = "SELECT score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
// 	$arsres = $callConfig->getAllRows($arssql);
// 	$return['arslabs'] = $arsres;
// 	return $return;
// }

	function pmsDashboard2($get){
		global $callConfig;
		$return = array();
		$return['menu_data'] = $this->toreportempdropdown($get);
		return $return;
	}
	function getEmployeeGoalsDetails($get){
	global $callConfig;
	$retuenres = array();
		//$retuenres['scsdc'] = $get;
	if($get['popup'] == 'Y'){
		$org_goal_sql = "SELECT eg.indicator_name,akra.id akra_id,akra.area_name, eg.weightage, eg.assessment_frequency, eg.traget FROM employee_goals eg, appraisal_key_result_areas akra WHERE eg.company_id = '".$get['company_id']."' AND eg.company_id = akra.company_id AND eg.key_reslut_id = akra.id AND akra.appraisal_id = '".$get['appraisal_id']."' AND eg.employee_id = '".$get['emp_id']."' AND eg.is_active = 0 AND eg.is_active = akra.is_active";
		$listgoal = $callConfig->getAllRows($org_goal_sql);
		$retuenres['listgoal'] = $listgoal;
	}else{
		$org_goal_sql = "SELECT eg.indicator_name,akra.id akra_id,akra.area_name, eg.weightage, eg.assessment_frequency, eg.traget,emp.id,emp.display_name, emp.emp_code, emp.job_title_id,emp.department_id,emp.location_id FROM employee_goals eg, appraisal_key_result_areas akra, employees emp WHERE eg.company_id = '".$get['company_id']."' AND eg.company_id = akra.company_id AND eg.company_id = emp.company_id AND eg.key_reslut_id = akra.id AND akra.appraisal_id = '".$get['appraisal_id']."' AND emp.id = eg.employee_id AND eg.is_active = 0 AND eg.is_active = akra.is_active AND eg.is_active = emp.is_active";
		$retuenres['org_goal_sql'] = $org_goal_sql;
		$listgoal = $callConfig->getAllRows($org_goal_sql);
		$retuenres['employees'] = $listgoal;
		$loc_ids = array();
		$jobt_ids = array();
		$depar_ids = array();
		foreach($listgoal as $listgoals){
			$loc_ids[$listgoals->location_id] = $listgoals->location_id;
			$jobt_ids[$listgoals->job_title_id] = $listgoals->job_title_id;
			$depar_ids[$listgoals->department_id] = $listgoals->department_id;
		}
		$loc_id = implode(",",(array)$loc_ids);
		$jobt_id = implode(",",(array)$jobt_ids);
		$depar_id = implode(",",(array)$depar_ids);
		$departsql = "SELECT id,department_name FROM departments WHERE company_id='".$get['company_id']."'  AND is_active=0 AND id IN(".$depar_id.")"; 
		$departres = $callConfig->getAllRows($departsql);
		$retuenres['department_list'] = $departres;

		$desingsql = "SELECT id,job_title as designation_name FROM job_titles WHERE company_id='".$get['company_id']."'  AND is_active=0 AND id IN(".$jobt_id.")"; 
		$desingres = $callConfig->getAllRows($desingsql);
		$retuenres['designation_list'] = $desingres;

		$locasql = "SELECT id,location_name FROM locations WHERE company_id='".$get['company_id']."'  AND is_active=0 AND id IN(".$loc_id.")"; 
		$locares = $callConfig->getAllRows($locasql);
		$retuenres['location_list'] = $locares;
	}
	return $retuenres;
}
function getOrganizationGoals($get){
	global $callConfig;
	$return = array();
	$return['get'] = $get;
	$departsql = "SELECT id,department_name FROM departments WHERE company_id='".$get['company_id']."'  AND is_active=0"; 
	$departres = $callConfig->getAllRows($departsql);
	$return['department_list'] = $departres;

	$desingsql = "SELECT id,job_title as designation_name FROM job_titles WHERE company_id='".$get['company_id']."'  AND is_active=0 "; 
	$desingres = $callConfig->getAllRows($desingsql);
	$return['designation_list'] = $desingres;

	$locasql = "SELECT id,location_name FROM locations WHERE company_id='".$get['company_id']."'  AND is_active=0"; 
	$locares = $callConfig->getAllRows($locasql);
	$return['location_list'] = $locares;
	if($get['popup'] =="Y"){

		$ogsql = "SELECT key_reslut_id, key_performance_id, assessment_frequency, traget, og.job_title_id, og.department_id, og.location_id FROM organization_goals og WHERE og.company_id = '".$get['company_id']."' AND og.appraisal_id = '".$get['appraisal_id']."'  AND og.job_title_id = '".$get['des_id']."' AND og.department_id = '".$get['dep_id']."' AND og.location_id = '".$get['loc_id']."' AND og.is_active =0";
		$ogres = $callConfig->getAllRows($ogsql);
		$return['organization_goals']=$ogres;
		$key_performance_id =$key_reslut_id  = array();
		foreach($ogres as $ogoal){
			$key_performance_id[$ogoal->key_performance_id] = $ogoal->key_performance_id;
			$key_reslut_id[$ogoal->key_reslut_id] = $ogoal->key_reslut_id;
		}
		$kpiid = implode(',',$key_performance_id);
		$kraid = implode(',',$key_reslut_id);
		$akrasql = "SELECT id, area_name, weightage FROM `appraisal_key_result_areas` WHERE company_id = '".$get['company_id']."' AND appraisal_id = '".$get['appraisal_id']."' AND id IN(".$kraid.")";
		$akrares = $callConfig->getAllRows($akrasql);
		$akraarray = array();
		foreach($akrares as $kra){
			$akraarray[$kra->id] = $kra;
		}
		$return['appraisal_key_result_areas']=$akraarray;

		$akrasql = "SELECT id, indicator_name, weightage, model, uom FROM `appraisal_key_performance_indicators` WHERE company_id = '".$get['company_id']."' AND id IN(".$kpiid.") AND is_active = 0";
		$akrares = $callConfig->getAllRows($akrasql);
		$akraarray = array();
		foreach($akrares as $kpi){
			$akraarray[$kpi->id] = $kpi;
		}
		$return['appraisal_key_performance_indicators']=$akraarray;


	}else{
	
		$ogsql = "SELECT DISTINCT og.job_title_id, og.department_id, og.location_id FROM organization_goals og WHERE og.company_id = '".$get['company_id']."' AND og.appraisal_id = '".$get['appraisal_id']."' AND  og.is_active =0";
		$ogres = $callConfig->getAllRows($ogsql);
		$return['organization_goals']=$ogres;
	}

	return $return;
}

/*New Data*/


	function insertGoals($get,$post){
		global $callConfig;
		$returnarray = array();
		if($post['start_goal'] == "Y"){

			$goalsql = "SELECT id, employee_ids FROM `goals` WHERE company_id = '".$get['company_id']."' AND id = '".$post['rid']."' AND is_active = 0";
			$goalres = $callConfig->getRow($goalsql);
			$employee_ids = json_decode($goalres->employee_ids,1);
			foreach($employee_ids as $emp_id){
				$insert_array = array('company_id'=>$post['company_id'], 'employee_id'=>$emp_id,'goal_id'=>$post['rid'],'percentage_of_completion'=>0,'is_active'=>0,'current_status'=>0,'created_by'=>$post['userid'],'created_date'=>date('Y-m-d h:i:s'));
					$insert = $callConfig->insertRecord('employee_goals',$insert_array);
			}
			$updatearray = array('share'=>0,'share_datetime'=>date('Y-m-d h:i:s'));
			$callConfig->updateRecord('goals',$updatearray,'id',$post['rid']);

		}else{
			$emailid = json_encode($post['employee_ids']);
			$insert_array = array('employee_ids'=>$emailid,'goal_name'=>$post['goal_name'],'goal_start_date'=>$this->dateconvertdbformat($post['goal_start_date']),'goal_end_date'=>$this->dateconvertdbformat($post['goal_end_date']),'descriptions'=>$post['descriptions']);
			if($post['rid'] !=""){
				$insert_array['updated_by'] = $post['userid'];
				$insert_array['updated_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->updateRecord('goals',$insert_array,'id',$post['rid']);
				$rid = $post['rid'];
			}else{
				$insert_array['company_id'] = $post['company_id'];
				$insert_array['is_active'] = '0';
				$insert_array['current_status'] = '0';
				$insert_array['created_by'] = $post['userid'];
				$insert_array['created_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->insertRecord('goals',$insert_array);
				$rid = $insert['LASTID'];
			}
			$returnarray['insert'] = $insert;
		}
		return $returnarray;
	}
	function getGoals($get){
		global $callConfig;
		$return = array();
		$employee_goalssql = "SELECT * FROM goals WHERE company_id='".$get['company_id']."' AND id='".$get['rid']."' AND is_active=0"; 
		$employee_goalsres = $callConfig->getRow($employee_goalssql);
		$return['employee_goals'] = $employee_goalsres;

		$modellistsql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='KPIMODAL' AND is_active=0"; 
		$modellistres = $callConfig->getAllRows($modellistsql);
		$return['modellist'] = $modellistres;

		$uomsql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='KPIUOM' AND is_active=0"; 
		$uomres = $callConfig->getAllRows($uomsql);
		$return['uomllist'] = $uomres;

		$employeessql = "SELECT id, display_name FROM employees WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$employeesres = $callConfig->getAllRows($employeessql);
		$employees = array();
		foreach($employeesres as $empres){
			$employees[$empres->id] = $empres->display_name;
		}
		$return['employees'] = $employees;

		return $return;
	}
	function getFeedBacks($get){
		global $callConfig;
		$return = array();

		$feedbackssql = "SELECT * FROM feedbacks WHERE company_id='".$get['company_id']."' AND id='".$get['rid']."' AND is_active=0"; 
		$feedbacksres = $callConfig->getRow($feedbackssql);
		$return['feedbacks'] = $feedbacksres;


		$frequencyql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='TASKFREQUENCY' AND is_active=0"; 
		$frequencyres = $callConfig->getAllRows($frequencyql);
		$return['frequency'] = $frequencyres;

		$employeessql = "SELECT id, display_name FROM employees WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$employeesres = $callConfig->getAllRows($employeessql);
		$employees = array();
		foreach($employeesres as $empres){
			$employees[$empres->id] = $empres->display_name;
		}
		$return['employees'] = $employees;

		return $return;
	}
	
	function insertFeedBacks($get,$post){
		global $callConfig;
		$returnarray = array();
		if($post['start_feedback'] == "Y"){

			$goalsql = "SELECT id, employee_ids FROM `feedbacks` WHERE company_id = '".$get['company_id']."' AND id = '".$post['rid']."' AND is_active = 0";
			$goalres = $callConfig->getRow($goalsql);
			$employee_ids = json_decode($goalres->employee_ids,1);
			foreach($employee_ids as $emp_id){
				$insert_array = array('company_id'=>$post['company_id'], 'employee_id'=>$emp_id,'feedback_id'=>$post['rid'],'is_active'=>0,'current_status'=>0,'created_by'=>$post['userid'],'created_date'=>date('Y-m-d h:i:s'));
					$insert = $callConfig->insertRecord('employee_feedbacks',$insert_array);
					$returnarray['insert'][] = $insert;
			}
			$updatearray = array('shared'=>0,'shared_datetime'=>date('Y-m-d h:i:s'));
			$callConfig->updateRecord('feedbacks',$updatearray,'id',$post['rid']);

		}else{

			$question_options = json_encode($post['question_options']);
			$employee_ids = json_encode($post['employee_ids']);
			
			$insert_array = array('question'=>$post['question'],'description'=>$post['description'],'answer_type'=>$post['answer_type'],'feedback_type'=>$post['feedback_type'],'question_options'=>$question_options,'max_ratting'=>$post['max_ratting'],'attachment'=>$post['attachment'],'employee_ids'=>$employee_ids,'action_frequency'=>$post['action_frequency']);
			if($post['rid'] !=""){
				$insert_array['updated_by'] = $post['userid'];
				$insert_array['updated_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->updateRecord('feedbacks',$insert_array,'id',$post['rid']);
				$rid = $post['rid'];
			}else{
				$insert_array['company_id'] = $post['company_id'];
				$insert_array['is_active'] = '0';
				$insert_array['current_status'] = '0';
				$insert_array['created_by'] = $post['userid'];
				$insert_array['created_date'] = date('Y-m-d h:i:s');
				$insert = $callConfig->insertRecord('feedbacks',$insert_array);
				$rid = $insert['LASTID'];
			}
			$returnarray['insert'] = $insert;
		}
		return $returnarray;

	}
	function getEmployeePerformanceAppraisals($get){
	global $callConfig;
	$return = array();
	if($get['ajax']=="Y"){  // Ajax Not Call

		$pa_headersql = "SELECT id,appraisal_id,assessment_year,header_name,header_comment,sub_header_name,sub_header_addmore,sub_header_default_row,header_display_setting,dynamic_page,page_name FROM performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND id = '".$get['header_id']."' AND is_active = 0"; 
		$pa_headeres = $callConfig->getRow($pa_headersql);
		$return['performance_appraisal_headers'] = $pa_headeres;

	}else{

		$sql ="SELECT * FROM employee_performance_appraisals WHERE company_id =".$get['company_id']." AND id = '".$get['rid']."' AND is_active = 0";
		$epares = $callConfig->getRow($sql);
		$return['employee_performance_appraisals'] = $epares;
		
		$employeessql = "SELECT emp.id,emp.emp_code,emp.display_name,emp.email_id,emp.user_id, (select department_name from departments dep where emp.department_id = dep.id)  department_name,(select designation_name from designations des where emp.designation_id = des.id) designation_name,au.reports_to FROM employees emp, application_users au  WHERE emp.id = '".$epares->employee_id."' AND emp.user_id = au.id  AND emp.company_id = au.company_id AND emp.company_id='".$get['company_id']."' AND emp.is_active=0"; 
			$employeesres = $callConfig->getRow($employeessql);
			$return['employee'] = $employeesres;

		$sql ="SELECT id,appraisal_id,area_name,description,weightage,kra_type FROM `appraisal_key_result_areas` WHERE company_id =".$get['company_id']." AND appraisal_id = '".$epares->appraisal_id."' AND is_active = 0";
		$epares1 = $callConfig->getAllRows($sql);
		$appraisal_key_result_areas = array();
		foreach($epares1 as $akra){
			$appraisal_key_result_areas[$akra->id]= $akra;
		}
		$return['appraisal_key_result_areas'] = $appraisal_key_result_areas;

		// $sql ="SELECT id,key_reslut_id,indicator_name,weightage,model,uom, display_sequence FROM `appraisal_key_performance_indicators` WHERE company_id =".$get['company_id']." AND appraisal_id = '".$epares->appraisal_id."' AND is_active = 0";
		// $epares2 = $callConfig->getAllRows($sql);
		// $return['appraisal_key_performance_indicators'] = $epares2;
		
		$organization_goal_id = json_decode($epares->organization_goal_id,1);
		$implodeid = implode(',', (array)$organization_goal_id);
		//$ogsql ="SELECT og.id, og.key_reslut_id, og.key_performance_id,  og.assessment_frequency, og.traget, akra.area_name, akra.description, akra.weightage as appraisal_key_result_area_weightage, akpi.indicator_name, akpi.weightage as appraisal_key_performance_indicator_weightage,  akpi.model, akpi.uom FROM organization_goals og, appraisal_key_result_areas akra, appraisal_key_performance_indicators akpi WHERE og.company_id = ".$get['company_id']." AND og.company_id = akra.company_id AND og.company_id = akpi.company_id AND og.appraisal_id ='".$epares->appraisal_id."' AND og.appraisal_id = akra.appraisal_id AND og.key_reslut_id = akra.id AND og.key_performance_id = akpi.id AND og.id IN(".$implodeid.") AND og.is_active = 0 AND og.is_active = akra.is_active";
		$ogsql ="SELECT og.id, og.key_reslut_id, og.key_performance_id,  og.assessment_frequency, og.traget, akpi.indicator_name, akpi.weightage as appraisal_key_performance_indicator_weightage,  akpi.model, akpi.uom FROM organization_goals og, appraisal_key_performance_indicators akpi WHERE og.company_id = ".$get['company_id']." AND og.company_id = akpi.company_id AND og.appraisal_id ='".$epares->appraisal_id."' AND og.key_performance_id = akpi.id AND og.id IN(".$implodeid.") AND og.is_active = 0 AND og.is_active = akpi.is_active";
		$organization_goals = $callConfig->getAllRows($ogsql);
		// $organization_goals = array();
		// foreach($ogres as $og){
		// 	$organization_goals[$og->key_reslut_id][]= $og;
		// }
		$return['organization_goals'] = $organization_goals;	


		$arssql = "SELECT id, score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$return['arslabs'] = $arsres;

		$sendemployedetails = array('employee_id'=>$epares->employee_id,'company_id'=>$get['company_id']);
		$returnresp = $this->getempconnectwithempid($sendemployedetails);
		$return['buddy_details'] = $returnresp;

		$pa_headersql = "SELECT id,appraisal_id,assessment_year,header_name,header_comment,sub_header_name,sub_header_addmore,sub_header_default_row,header_display_setting,dynamic_page,page_name FROM performance_appraisal_headers WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND appraisal_id ='".$epares->appraisal_id."' "; 
			$pa_headeres = $callConfig->getAllRows($pa_headersql);
		$return['performance_appraisal_headers'] = $pa_headeres;

		$epappraisalsql = "SELECT id,header_id,filed_1,filed_2,filed_3,filed_4,filed_5 FROM employee_performance_appraisal_details WHERE company_id = '".$get['company_id']."' AND employee_id = '".$epares->employee_id."' AND appraisal_id = '".$epares->appraisal_id."' AND is_active = 0 "; 
		$epappraisalres = $callConfig->getAllRows($epappraisalsql);
		$return['employee_performance_appraisal_details'] = $epappraisalres;



		$aaqsql = "SELECT id, appraisal_id, question, description, answer_type, display_answer, display_question, question_options, max_ratting FROM performance_appraisal_questions WHERE company_id = '".$get['company_id']."' AND appraisal_id = '".$epares->appraisal_id."' AND is_active = 0 "; 
		$aaqres = $callConfig->getAllRows($aaqsql);
		$return['performance_appraisal_questions'] = $aaqres;


		$employee_goalssql = "SELECT eg.id, eg.percentage_of_completion, go.goal_name, go.descriptions, go.goal_start_date, go.goal_end_date FROM employee_goals eg, goals go WHERE eg.company_id = '".$get['company_id']."' AND eg.employee_id = '".$epares->employee_id."' AND eg.is_active = 0 "; 
		$employee_goalsres = $callConfig->getAllRows($employee_goalssql);
		$return['employee_goals'] = $employee_goalsres;

		$employee_feedbackssql = "SELECT ef.id, ef.employee_id, ef.response_status, ef.response_datetime,fe.question FROM employee_feedbacks ef, feedbacks fe WHERE ef.company_id = '".$get['company_id']."'  AND ef.company_id = fe.company_id AND ef.feedback_id = fe.id AND ef.employee_id = '".$epares->employee_id."' AND ef.is_active = 0 AND ef.is_active = fe.is_active"; 
		$employee_feedbacksres = $callConfig->getAllRows($employee_feedbackssql);
		$return['employee_feedbacks'] = $employee_feedbacksres;

	}
	return $return;
}
	function insertEmployeePerformanceAppraisals($get,$post){
		global $callConfig;
		$return = array();
		$epasql = "SELECT id, appraisal_id,employee_id FROM employee_performance_appraisals WHERE company_id = '".$get['company_id']."' AND id = '".$post['rid']."' AND is_active = 0"; 
		$epares = $callConfig->getRow($epasql);
		$return['employee_performance_appraisals'] = $epares;
		
		$arssql = "SELECT id, start_range,end_range FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$self_appraisal_rating_slab_id = $ro_appraisal_rating_slab_id = "";
		foreach($arsres as $slab){
			if($slab->start_range <= $post['self_total'] && $slab->end_range >= $post['self_total']){ 
				$self_appraisal_rating_slab_id = $slab->id;
		 	}
		 	if($slab->start_range <= $post['ro_total'] && $slab->end_range >= $post['ro_total']){ 
				$ro_appraisal_rating_slab_id = $slab->id;
		 	}
		}					
		$performance_appraisal_questions = json_encode($post['performance_appraisal_questions']);
		$self_achived = json_encode($post['self_achived']);
		$ro_achived= json_encode($post['ro_achived']);
		$insert_array = array('self_achived'=>$self_achived,'ro_achived'=>$ro_achived,'self_total'=>$post['self_total'],'ro_total'=>$post['ro_total'],'performance_appraisal_questions'=>$performance_appraisal_questions,'employee_comment'=>$post['employee_comment'],'ro_comment'=>$post['ro_comment'],'hod_comment'=>$post['hod_comment'],'workflow_approval_level'=>$post['workflow_approval_level'],'self_appraisal_rating_slab_id'=>$self_appraisal_rating_slab_id,'ro_appraisal_rating_slab_id'=>$ro_appraisal_rating_slab_id);
		// $insert_array = array('performance_appraisal_questions'=>$performance_appraisal_questions);
		$insert_array['updated_by'] = $post['userid'];
		$insert_array['updated_date'] = date('Y-m-d h:i:s');
		$insert = $callConfig->updateRecord('employee_performance_appraisals',$insert_array,'id',$post['rid']);
		$return['insert'] = $insert;
		$insertsql = "INSERT INTO employee_performance_appraisal_details (company_id, employee_performance_appraisal_id, appraisal_id, employee_id, header_id, filed_1, filed_2, filed_3, filed_4, filed_5, filed_6, is_active, current_status, created_date, created_by) VALUES ";
		$newewarray = array();
		foreach($post['filed'] as $headerid => $headerow){
			foreach($headerow as $kkey=>$valu){
				$newewarray[$headerid] = $valu;
			}
		}	
		$wherecond = "appraisal_id = '".$epares->appraisal_id."' AND employee_id = '".$epares->employee_id."'";
		$callConfig->updateRecord('employee_performance_appraisal_details',array('is_active'=>null),'','',$wherecond);
		foreach($newewarray as $header_id => $headerow){
			$field = array();
			foreach($headerow as $key => $hea){
				for($field = 1;$field <= 6;$field++){ 
					$fird[$field] = $post['filed'][$header_id][$field][$key];
				}
				$rowid = $post['row_id'][$header_id][$key];
				if($rowid !=""){  // updated details
					$insert_array = array('filed_1'=>$fird[1], 'filed_2'=>$fird[2], 'filed_3'=>$fird[3], 'filed_4'=>$fird[4], 'filed_5'=>$fird[5], 'filed_6'=>$fird[6],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'),'is_active'=>'0');
					$insert_a[] = $insert_array;
					$resupdate[] = $callConfig->updateRecord('employee_performance_appraisal_details',$insert_array,'id',$rowid);
				}else{
					$inserexc = "Y";
					$insertsql .= "('".$post['company_id']."','".$post['rid']."','".$epares->appraisal_id."','".$epares->employee_id."','".$header_id."','".$fird[1]."','".$fird[2]."','".$fird[3]."','".$fird[4]."','".$fird[5]."','".$fird[6]."', '0','0','".date('Y-m-d h:i:s')."','".$post['user_id']."'),";
				}
			}
		}
		if($inserexc == 'Y'){	
			$insertsql = rtrim($insertsql, ',');
			$res = $callConfig->executeQuery($insertsql);
			$return['res'] = $res;
		}
		return $return;
	}
	function getEmployeeGoals($get){
		global $callConfig;
		$return = array();
		$employee_goalssql = "SELECT eg.id, eg.employee_id, eg.percentage_of_completion, go.goal_name, go.descriptions, go.goal_start_date, go.goal_end_date FROM employee_goals eg, goals go WHERE eg.company_id = '".$get['company_id']."' AND eg.id = '".$get['rid']."' AND eg.is_active = 0 "; 
		$employee_goalsres = $callConfig->getRow($employee_goalssql);
		$return['employee_goals_sql'] = $employee_goalssql;
		$return['employee_goals'] = $employee_goalsres;
		return $return;
	}
	function getPMSAdminDashboard($get){
		global $callConfig;
		$return = array();
		$employee_goalssql = "SELECT eg.id, eg.employee_id, eg.percentage_of_completion, go.goal_name, go.descriptions, go.goal_start_date, go.goal_end_date FROM employee_goals eg, goals go WHERE eg.company_id = '".$get['company_id']."' AND eg.is_active = 0 "; 
		$employee_goalsres = $callConfig->getAllRows($employee_goalssql);
		$return['employee_goals'] = $employee_goalsres;
		
		$employeessql = "SELECT id, display_name, image, emp_code FROM employees WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$employeesres = $callConfig->getAllRows($employeessql);
		$employees = array();
		foreach($employeesres as $empres){
			$employees[$empres->id] = $empres;
		}
		$return['employees'] = $employees;

		$cntsql = "SELECT * FROM ((SELECT count(*) as total_employee FROM employees WHERE company_id = '".$get['company_id']."' and is_active =0) as total_employee, (SELECT count(*) as total_appraisals FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' and is_active =0) as total_appraisals, (SELECT count(*) as total_appraisal_shared FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' AND publish = 'Y' AND is_active =0) as total_appraisal_shared, (SELECT count(*) as total_goals FROM goals WHERE company_id = '".$get['company_id']."' and is_active =0) as total_goals, (SELECT count(*) as total_shared_goals FROM goals WHERE company_id = '".$get['company_id']."' AND shared = 0 AND is_active =0) as total_shared_goals, (SELECT count(*) as total_feedbacks FROM feedbacks WHERE company_id = '".$get['company_id']."' and is_active =0) as total_feedbacks, (SELECT count(*) as total_shared_feedbacks FROM feedbacks WHERE company_id = '".$get['company_id']."'  AND shared = 0 AND is_active =0) as total_shared_feedbacks)";
		$cntres = $callConfig->getRow($cntsql);
		$return['total_cnt']  = $cntres;

		$rattingsql ="SELECT sum(if((ro_total >= 0 AND ro_total <= 10),1,0)) as ratting_1,sum(if((ro_total >= 11 AND ro_total <= 20),1,0)) as ratting_2,sum(if((ro_total >= 21 AND ro_total <= 30),1,0)) as ratting_3,sum(if((ro_total >= 31 AND ro_total <= 40),1,0)) as ratting_4,sum(if((ro_total >= 41 AND ro_total <= 50),1,0)) as ratting_5,sum(if((ro_total >= 51 AND ro_total <= 60),1,0)) as ratting_6,sum(if((ro_total >= 61 AND ro_total <= 70),1,0)) as ratting_7,sum(if((ro_total >= 71 AND ro_total <= 80),1,0)) as ratting_8,sum(if((ro_total >= 81 AND ro_total <= 90),1,0)) as ratting_9,sum(if((ro_total >= 91 AND ro_total <= 1000),1,0)) as ratting_10 FROM `employee_performance_appraisals` WHERE company_id =".$get['company_id']." AND appraisal_id = '1' AND is_active = 0";
		$rattingres = $callConfig->getRow($rattingsql);
		$rattingchart = array();
		foreach($rattingres as $ratting){
			$rattingchart[] = $ratting;
		}
		$return['employee_performance_appraisals_ratting'] = $rattingchart;

		$performance_appraisal_setupssql = "SELECT id, performance_appraisal_name, assessment_year, assessment_start_date, assessment_end_date, self_assessment_start_date, self_assessment_end_date, manager_assessment_start_date, manager_assessment_end_date, reviewing_start_date, reviewing_end_date, publish FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$performance_appraisal_setupsres = $callConfig->getAllRows($performance_appraisal_setupssql);
		$return['performance_appraisal_setups'] = $performance_appraisal_setupsres;

		$pasid = array();
		foreach($performance_appraisal_setupsres as $pas){
			$pasid[$pas->id] =$pas->id; 
		}
		$implodeid = implode(",", $pasid);
		$employee_performance_appraisalssql = "SELECT id, appraisal_id, employee_id, self_total, ro_total,current_status FROM employee_performance_appraisals WHERE company_id='".$get['company_id']."' AND appraisal_id IN(".$implodeid.") AND is_active=0"; 
		$employee_performance_appraisalsres = $callConfig->getAllRows($employee_performance_appraisalssql);
		$employee_performance_appraisals = array();
		foreach($employee_performance_appraisalsres as $epas){
			$employee_performance_appraisals[$epas->appraisal_id][] = $epas;
		}
		$return['employee_performance_appraisals'] = $employee_performance_appraisals;
		/////////////////////////////////////////// Start goals///////////////////////////
		$goalssql = "SELECT id, goal_name, descriptions, goal_start_date, goal_end_date, shared FROM goals WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$goalsres = $callConfig->getAllRows($goalssql);
		$return['goals'] = $goalsres;

		$goalid = array();
		foreach($goalsres as $go){
			$goalid[$go->id] =$go->id; 
		}
		$implodegoalid = implode(",", $goalid);
		$employee_goalssql = "SELECT id, employee_id, goal_id, percentage_of_completion FROM employee_goals WHERE company_id='".$get['company_id']."' AND goal_id IN(".$implodegoalid.") AND is_active=0"; 
		$employee_goalsres = $callConfig->getAllRows($employee_goalssql);
		$employee_goals = array();
		foreach($employee_goalsres as $empg){
			$employee_goals[$empg->goal_id][] = $empg;
		}
		$return['all_employee_goals'] = $employee_goals;
		/////////////////////////////////////////// Start goals///////////////////////////
		
		/////////////////////////////////////////// Start Feedbacks///////////////////////////

		$feedbackssql = "SELECT id, question, shared, shared_datetime FROM feedbacks WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$feedbacksres = $callConfig->getAllRows($feedbackssql);
		$return['feedbacks'] = $feedbacksres;
		
		$feedbackid = array();
		foreach($feedbacksres as $go){
			$feedbackid[$go->id] =$go->id; 
		}
		$implodefeedbackid = implode(",", $feedbackid);
		$employee_feedbackssql = "SELECT id, employee_id, feedback_id, response_status, response, response_datetime FROM employee_feedbacks WHERE company_id='".$get['company_id']."' AND feedback_id IN(".$implodefeedbackid.") AND is_active=0"; 
		$employee_feedbacksres = $callConfig->getAllRows($employee_feedbackssql);
		$employee_feedbacks = array();
		foreach($employee_feedbacksres as $empf){
			$employee_feedbacks[$empf->feedback_id][] = $empf;
		}
		$return['employee_feedbacks'] = $employee_feedbacks;


		/////////////////////////////////////////// End Feedbacks///////////////////////////

		$arssql = "SELECT id, score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$return['arslabs'] = $arsres;

		return $return;
	}
	function getPMSEmployeeDashboard($get){
		global $callConfig;
		$return = array();
		//AND JSON_SEARCH(employee_ids, 'one', '".$get['employee_id']."') IS NOT NULL
		$cntsql = "SELECT * FROM ((SELECT count(*) as total_appraisals FROM employee_performance_appraisals WHERE company_id = '".$get['company_id']."' AND employee_id = '".$get['employee_id']."' AND  is_active =0) as total_appraisals, (SELECT count(*) as total_self_goals FROM goals WHERE company_id = '".$get['company_id']."' AND created_by = '".$get['user_id']."' AND is_active =0) as total_self_goals, (SELECT count(*) as total_shared_goals FROM employee_goals WHERE company_id = '".$get['company_id']."' AND employee_id = '".$get['employee_id']."' AND is_active =0) as total_shared_goals, (SELECT count(*) as total_self_feedbacks FROM feedbacks WHERE company_id = '".$get['company_id']."' AND created_by = '".$get['user_id']."' AND is_active =0) as total_self_feedbacks,  (SELECT count(*) as total_feedback FROM employee_feedbacks WHERE company_id = '".$get['company_id']."' AND employee_id = '".$get['employee_id']."' AND is_active =0) as total_feedback)";
		$cntres = $callConfig->getRow($cntsql);
		$return['total_cnt']  = $cntres;
		$return['total_cnt_Sql']  = $cntsql;

		$employee_goalssql = "SELECT eg.id, eg.employee_id, eg.percentage_of_completion, go.goal_name, go.descriptions, go.goal_start_date, go.goal_end_date FROM employee_goals eg, goals go WHERE eg.company_id = '".$get['company_id']."'  AND employee_id = '".$get['employee_id']."' AND eg.is_active = 0 "; 
		$employee_goalsres = $callConfig->getAllRows($employee_goalssql);
		$return['employee_goals'] = $employee_goalsres;

		$employee_performance_appraisalssql = "SELECT epa.id, epa.start_date, epa.end_date, epa.self_total, epa.ro_total, epa.current_status, pas.performance_appraisal_name FROM employee_performance_appraisals epa, performance_appraisal_setups pas WHERE epa.company_id = '".$get['company_id']."' AND epa.company_id = pas.company_id AND epa.appraisal_id = pas.id  AND epa.employee_id = '".$get['employee_id']."' AND epa.is_active =0 AND epa.is_active = pas.is_active"; 
		$employee_performance_appraisalsews = $callConfig->getAllRows($employee_performance_appraisalssql);
		$return['employee_performance_appraisals'] = $employee_performance_appraisalsews;

		$employee_feedbackssql = "SELECT ef.id, ef.employee_id, ef.response_datetime,fe.question FROM employee_feedbacks ef, feedbacks fe WHERE ef.company_id = '".$get['company_id']."'  AND ef.company_id = fe.company_id AND ef.feedback_id = fe.id AND ef.employee_id = '".$get['employee_id']."' AND ef.is_active = 0 AND ef.is_active = fe.is_active"; 
		$employee_feedbacksres = $callConfig->getAllRows($employee_feedbackssql);
		$return['employee_feedbacks'] = $employee_feedbacksres;


		$arssql = "SELECT id, score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$return['arslabs'] = $arsres;

		return $return;
	}
	function getEmployeeFeedBacks($get){
		global $callConfig;
		$return = array();
		$employee_feedbackssql = "SELECT fe.question, fe.description, fe.answer_type, fe.feedback_type, fe.action_frequency, fe.question_options, fe.max_ratting, ef.id, ef.response, ef.response_datetime FROM employee_feedbacks ef, feedbacks fe WHERE ef.company_id = '".$get['company_id']."' AND ef.company_id = fe.company_id AND ef.feedback_id = fe.id AND ef.id = '".$get['rid']."'  AND ef.is_active = 0 AND ef.is_active = fe.is_active"; 
		$employee_feedbacksres = $callConfig->getRow($employee_feedbackssql);
		$return['employee_feedbacks_sql'] = $employee_feedbackssql;
		$return['employee_feedbacks'] = $employee_feedbacksres;
		return $return;
	}
	function getEmployeePerformanceAppraisalsNormalisations($get,$post){
		global $callConfig;
		$return = array();
		$condition = "";
		$return['post'] = $post;
		if($post['submit_btn'] == 'submitnormalizations'){
			foreach($post['employee_performance_appraisal_id'] as $epaid){
				$insert_array = array('normalization_approved_status'=>'0','normalization_ranking'=>$post['normalization_ranking'][$epaid],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
				$return['normalization_sql'][] = $callConfig->updateRecord('employee_performance_appraisals',$insert_array,'id',$epaid);

			}
		}
		if($post['submit_btn'] == 'savenormalizations'){
			foreach($post['employee_performance_appraisal_id'] as $epaid){
				$insert_array = array('normalization_ranking'=>$post['normalization_ranking'][$epaid],'updated_by'=>$post['user_id'],'updated_date'=>date('Y-m-d h:i:s'));
				$return['normalization_sql'][] = $callConfig->updateRecord('employee_performance_appraisals',$insert_array,'id',$epaid);
			}
		}

		
		if($post['submit_btn'] == 'submitnormalizations' || $post['submit_btn'] == 'submitfilter'){ 
			if($post['appraisal_id'] !=""){
				$condition .= " AND appraisal_id = '".$post['appraisal_id']."'";
			}
			if($post['assessment_year'] !=""){
				$passql = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS id FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' AND publish = 'Y' AND assessment_year = '".$post['assessment_year']."' AND is_active = 0 GROUP BY assessment_year"; 
				$pasres = $callConfig->getRow($passql);
				$condition .= " AND appraisal_id IN(".$pasres->id.")";
			}	
			if($post['reports_to'] !=""){ 
				$application_userssql = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS id  FROM application_users WHERE company_id='".$get['company_id']."' AND reports_to = '".$post['reports_to']."' AND is_active=0 GROUP BY reports_to"; 
				$application_usersres = $callConfig->getRow($application_userssql);
				$emp_condition .= " AND user_id IN(".$application_usersres->id.")";
			}
			if($post['location_id'] !=""){
				$emp_condition .= " AND location_id = '".$post['location_id']."'";
			} 
			if($post['department_id'] !=""){
				$emp_condition .= " AND department_id = '".$post['department_id']."'";
			}
			if($post['job_title_id'] !=""){
				$emp_condition .= " AND job_title_id = '".$post['job_title_id']."'";
			}
			if(count((array)$post['emp_id']) !="0"){
				$implodeid = implode(',', $post['emp_id']);
				$emp_condition .= " AND id IN(".$implodeid.")";
			}
			if($post['joining_date'] !=""){
				$emp_condition .= " AND joining_date >= '".$this->dateconvertdbformat($post['joining_date'])."'";
			}
			$employeesidsql = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS id FROM employees WHERE company_id='".$get['company_id']."' ".$emp_condition." AND is_active=0"; 
			$return['reports_to'] = $employeesidsql;
			$employeesids = $callConfig->getRow($employeesidsql);
			//if(isset($employeesids->id)){
				$condition = " AND employee_id IN(".$employeesids->id.")";
			//}
			$epasql = "SELECT * FROM employee_performance_appraisals WHERE company_id = '".$get['company_id']."' AND normalization_status = '0' ".$condition." AND is_active = 0"; 
			$epares = $callConfig->getAllRows($epasql);
			$return['employee_performance_appraisals_sql'] = $epasql;
			$return['employee_performance_appraisals'] = $epares;
		}
		$passql = "SELECT id, performance_appraisal_name, assessment_year, appraisal_bell_curve_id FROM performance_appraisal_setups WHERE company_id = '".$get['company_id']."' AND publish = 'Y' AND is_active = 0"; 
		$pasres = $callConfig->getAllRows($passql);
		$return['performance_appraisal_setups'] = $pasres;

		$employeessql = "SELECT id, display_name, image, emp_code FROM employees WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$employeesres = $callConfig->getAllRows($employeessql);
		$employees = array();
		foreach($employeesres as $empres){
			$employees[$empres->id] = $empres;
		}
		$return['employees'] = $employees;

		$arssql = "SELECT id, score,slabs_name,start_range,end_range,background_color,font_color FROM appraisal_rating_slabs WHERE company_id = '".$get['company_id']."' AND is_active = 0"; 
		$arsres = $callConfig->getAllRows($arssql);
		$return['arslabs'] = $arsres;

		$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$application_usersres = $callConfig->getAllRows($application_userssql);
		$return['application_users'] = $application_usersres;

		$locationssql = "SELECT id, location_name FROM locations WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$locationsres = $callConfig->getAllRows($locationssql);
		$return['locations'] = $locationsres;

		$departmentssql = "SELECT id, department_name FROM departments WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$departmentsres = $callConfig->getAllRows($departmentssql);
		$return['departments'] = $departmentsres;

		$job_titlesql = "SELECT id, job_title FROM job_titles WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$job_titleres = $callConfig->getAllRows($job_titlesql);
		$return['job_title'] = $job_titleres;

		$appraisal_bell_curvessql = "SELECT * FROM appraisal_bell_curves WHERE company_id='".$get['company_id']."' AND id = '".$post['appraisal_bell_curve_id']."' AND is_active=0"; 
		$appraisal_bell_curvesres = $callConfig->getRow($appraisal_bell_curvessql);
		$return['appraisal_bell_curves'] = $appraisal_bell_curvesres;

		return $return;
	}
	function getAppraisalBellCurves($get){
		global $callConfig;
		$return = array();
		$condition = "";
		$return['post'] = $post;
		$appraisal_rating_slabssql = "SELECT id, slabs_name, background_color, font_color FROM appraisal_rating_slabs WHERE company_id='".$get['company_id']."' AND is_active=0"; 
		$appraisal_rating_slabsres = $callConfig->getAllRows($appraisal_rating_slabssql);
		$return['appraisal_rating_slabs'] = $appraisal_rating_slabsres;

		$appraisal_bell_curvessql = "SELECT * FROM appraisal_bell_curves WHERE company_id='".$get['company_id']."' AND id = '".$get['rid']."' AND is_active=0"; 
		$appraisal_bell_curvesres = $callConfig->getRow($appraisal_bell_curvessql);
		$return['appraisal_bell_curves'] = $appraisal_bell_curvesres;
		
		return $return;
	}
	function insertAppraisalBellCurves($get,$post){
		global $callConfig;
		$returnarray = array();
		$returnarray['post'] = $post;
		$slabs = json_encode($post['slabs']);
		$insert_array = array('bell_curve_name'=>$post['bell_curve_name'],'slabs'=>$slabs);
		if($post['rid'] !=""){
			$insert_array['updated_by'] = $post['userid'];
			$insert_array['updated_date'] = date('Y-m-d h:i:s');
			$insert = $callConfig->updateRecord('appraisal_bell_curves',$insert_array,'id',$post['rid']);
			$rid = $post['rid'];
		}else{
			$insert_array['company_id'] = $post['company_id'];
			$insert_array['is_active'] = '0';
			$insert_array['current_status'] = '0';
			$insert_array['created_by'] = $post['userid'];
			$insert_array['created_date'] = date('Y-m-d h:i:s');
			$insert = $callConfig->insertRecord('appraisal_bell_curves',$insert_array);
			$rid = $insert['LASTID'];
		}
		$returnarray['insert'] = $insert;
		return $returnarray;
	}
	function publishCommentAndRank($get,$post){
		global $callConfig;
		$returnarray = array();
		$insert_array['updated_by'] = $post['userid'];
		$insert_array['updated_date'] = date('Y-m-d h:i:s');
		if($post['type']=="C"){
			$insert_array['publish_comment'] = 0;
		}else{
			$insert_array['publish_comment'] = 0;
			$insert_array['publish_rank'] = 0;
		}
		$insert = $callConfig->updateRecord('employee_performance_appraisals',$insert_array,'id',$post['rid']);
		
		return $insert;
	}
}

?>