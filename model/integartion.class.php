<?php
class integartionClass{ 
	function toinsertupdatethejobpostingpublish($post,$get){
		global $callConfig;
		//5,52,53	
		$returrara['post'] = $post;
		$returrara['get'] = $get;
		$insert_array = array('company_id'=>$post['company_id'],'job_post_id'=>$post['post_id'],'job_hiring_source_id'=>$post['source_id'],'publish_date'=>date('Y-m-d'),'ref_id'=>$post['ref_id'],'acknowledgement_details'=>$post['response_data'],'is_active'=>'0','current_status'=>52,'created_by'=>$get['user_id']);
		if($post['type']=='CREATE'){
			$insert = $callConfig->insertRecord('job_posting_publish_details',$insert_array);
			$upquery =$callConfig->updateRecord('job_postings',array('current_status'=>52),'id',$post['post_id']);
			$insert_array['job_posting_publish_id'] = $insert['LASTID'];
		}else{
			$update_array = array('current_status'=>'0');
			if($post['type']=='MODIFY'){
				$update_array['current_status'] = 52;
			}
			if($post['type']=='DRAFT'){
				$update_array['current_status'] = 5;
				$update_array['is_active'] = 1;
			}
			if($post['type']=='TRASH'){
				$update_array['current_status'] = 53;
				$update_array['un_publish_date'] = date('Y-m-d');				
			}
			$insert_array['job_posting_publish_id'] = $insert['LASTID'];
			$insert_array['current_status'] = $update_array['current_status'];
			$insert=$callConfig->updateRecord('job_posting_publish_details',$update_array,'id',$post['publish_id']);
			if($post['type']=='TRASH'){
				$crow=$callConfig->getRow("SELECT COUNT(id)  cc FROM job_posting_publish_details WHERE job_post_id =".$post['post_id']." AND is_active = 0 AND current_status!=53"); 
				if($crow->cc ==""){
					$upquery =$callConfig->updateRecord('job_postings',array('current_status'=>53),'id',$post['post_id']);
				}
			}
		}
		$insert = $callConfig->insertRecord('job_posting_publish_detail_histories',$insert_array);
		return $returrara;
	}
	function infisuitejobcandedatesave($post,$get){
		global $callConfig;		
		if($post['resume']!=""){
			// Initialize a file URL to the variable
			$url = $post['resume'];
			// Initialize the cURL session
			$ch = curl_init($url);
			// Initialize directory name where
			// file will be save
			$dir = 'uploads/all/';
			// Use basename() function to return
			// the base name of file
			$file_name = "file_".time()."_".uniqid()."_".basename($url);
			// Save file into file location
			$save_file_loc = $dir . $file_name;
			// Open file
			$fp = fopen($save_file_loc, 'wb');
			// It set an option for a cURL transfer
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			// Perform a cURL session
			curl_exec($ch);
			// Closes a cURL session and frees all resources
			curl_close($ch);
			// Close file
			fclose($fp);
		}
		$crow=$callConfig->getRow("SELECT job_post_id FROM job_posting_publish_details WHERE ref_id =".$post['extid']." AND is_active = 0"); 
		$post_id = $crow->job_post_id;
		$postdata = $callConfig->getRow("SELECT requested_by,recruitment_manager FROM job_postings WHERE id =".$post_id." AND is_active = 0"); 
		$excepted_doj = $post['candidate_join_date']!=""?date('Y-m-d', strtotime(str_replace('/"', '-', $post['candidate_join_date']))):"";
		$insertarray1 = array('company_id'=>$get['company_id'],"first_name"=>$post['candidate_name'],"email_id"=>$post['candidate_email'],"phone"=>$post['candidate_phone'],'hiring_source'=>INFISUITESOURCE,"file"=>$file_name, 'address'=>$post['candidate_address'], 'excepted_doj'=>$excepted_doj, 'expected_ctc'=>$post['candidate_expsalary'], "is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$postdata->recruitment_manager,'hm_id'=>$postdata->requested_by,'rm_id'=>$postdata->recruitment_manager);
		$transation = $this->toGetApplicationSerialNumber('candidates');
		$insertarray1['candidate_code'] = $transation;
		$insertarray1['display_name'] = $post['candidate_name'];
		$insert1 = $callConfig->insertRecord('candidates',$insertarray1);
		if($insert1['status'] == "sucess"){
			$insert_array = array('company_id'=>$get['company_id'],'candidate_id'=>$insert1['LASTID'],'job_posting_id'=>$post_id,'candidate_name'=>$post['candidate_name'],'candidate_email'=>$post['candidate_email'],'candidate_phone'=>$post['candidate_phone'],'file'=>$file_name,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'remarks'=>json_encode($post));
			$insert = $callConfig->insertRecord('job_posting_candidates',$insert_array);	
		}
		$return['insert1sql'] = $insertarray1;
		$return['insert1'] = $insert1;
		$return['insert2sql'] = $insertarray;
		$return['insert2'] = $insert;
		$insert['other'] = $return;
		return $insert;
		/*$insert_array = array('company_id'=>$get['company_id'],'job_posting_id'=>$post['extid'],'candidate_name'=>$post['candidate_name'],'candidate_email'=>$post['candidate_email'],'candidate_phone'=>$post['candidate_phone'],'file'=>$post['resume'],'experience'=>$post['experience'],'image'=>$post['resume'],'is_active'=>'0','current_status'=>'0','current_location'=>$post['current_location'],'current_salary'=>$post['current_salary']);
		$insert = $callConfig->insertRecord('job_posting_candidates',$insert_array);	*/
		
	}
	function toGetApplicationSerialNumber($table_name){
		global $callConfig;
		$whr="application_table_name = '".$table_name."'";

		$query=$callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS,'*',$whr,'','','');
		$res = $callConfig->getRow($query);
		$le = $res->doc_no_length;
		$var = "%0".$le."d";
		$data = $res->pre_fix.sprintf($var,$res->serial_number+1).$res->post_fix;
		$update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS,array('serial_number' =>$res->serial_number+1),'id',$res->id);
		return trim($data);
	}
	function getviewalldetilarecruitmentdashboardineg($get){
		global $callConfig;
		//echo "<pre>";
		$reportstartdate = $get['reportstartdate'];
		$reportenddate = $get['reportenddate'];
		$whr = ""; $whrjp="";
		if($get['roleshortcode']==HIRINGMANAGER_ROLE){
			$whr = " AND requested_by = ".$get['user_id'];
			$whrjp = " AND jp.requested_by = ".$get['user_id'];
		}
		if($get['roleshortcode']==RECRUITINGMANAGER_ROLE){
			$whr = " AND recruitment_manager = ".$get['user_id'];
			$whrjp = " AND jp.recruitment_manager = ".$get['user_id'];
		}
		if($get['type']=='ACTIVE'){
			$job_postingssql = "SELECT id, requested_date, job_number, job_name, job_status, no_of_resources, required_date,required_date, current_status FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND requested_date <= '".date('Y-m-d')."' AND current_status=0 AND is_active = 0 ";
			$job_postingsres = $callConfig->getAllRows($job_postingssql);	
			$return['name'] = "Active Job Postings";
			$return['data'] = $job_postingsres;
		}
		/*--------- All Yet to submit  3 Job Details ---------------- */
		if($get['type']=='YET'){
			if($get['roleshortcode']==HIRINGMANAGER_ROLE){
				$job_postingssql = "SELECT id, requested_date, job_number, job_name, no_of_resources, required_date, job_status, current_status, updated_date FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND job_status !='1' AND is_active = 0 ";
				$return['name'] = "Jobs Yet to Publish";
				
			}
			if($get['roleshortcode']==RECRUITINGMANAGER_ROLE){
				$job_postingssql = "SELECT id, requested_date, job_number, job_name, no_of_resources, required_date, job_status, current_status, updated_date FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND job_status !='1' AND  is_active = 0";
				$return['name'] = "Jobs Yet to Publish";
				
			}
			$return['data'] =  $callConfig->getAllRows($job_postingssql);
		}
		if($get['type']=='PUBLISH'){
			$recent_publishsql = "SELECT id, requested_date, job_number, job_name, no_of_resources, required_date, job_status, current_status, updated_date  FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_PUBLISH." AND job_status !='1' AND is_active = 0";
			$return['name'] = "Recently Published";
			$recent_publish = $callConfig->getAllRows($recent_publishsql);	
			$return['data'] = $recent_publish;
		}
		if($get['type']=='UNPUBLISH'){
			$recent_unpublishsql = "SELECT id, requested_date, job_number, job_name, no_of_resources, required_date, job_status, current_status, updated_date  FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_UNPUBLISH." AND job_status !='1' AND is_active = 0";
			$return['name'] = "Recently Un-Published";
			$recent_publish = $callConfig->getAllRows($recent_unpublishsql);	
			$return['data'] = $recent_unpublish;
		}
		return $return;
	}
	function togetmyreportswithdetails($get){
		global $callConfig;
		$whr = " AND module_id IN (".$get['module_list'].") ";
		
		$query = "SELECT id, page_name, report_id, report_icon, report_id, parent_id, page_url, module_id  FROM my_reports WHERE company_id=".$get['company_id']." AND report_type='SR'  ".$whr." AND is_active =0";
		$return['reportdata'] = $callConfig->getAllRows($query);
		
		$query = "SELECT  id, MAX(report_date) report_date, report_id  FROM report_histories WHERE company_id=".$get['company_id']." AND user_id=".$get['user_id']." AND activity = 'Viewed' AND report_id !=0  AND is_active = 0  GROUP BY report_id ORDER By report_date DESC limit 5";
		$hist = $callConfig->getAllRows($query);
		foreach($hist as $stt){
			$view[$stt->report_id] = $stt->report_date;
		}
		$return['view'] = $view;
		return $return;
	}
	function togetcandidatefunnel($get){
		
		global $callConfig;
		$return['get']  = $get;
		$userrolelist = json_decode($get['userrole'],1);
		//$return['userrole']  = $get['userrole'];
		$condition = "";	
		if(in_array(RECRUITINGMANAGER_ROLE, $userrolelist)){
			$condition = " AND jp.recruitment_manager = '".$get['user_id']."'";
		}elseif(in_array(HIRINGMANAGER_ROLE, $userrolelist)){
			$condition = " AND jp.requested_by = '".$get['user_id']."'";
		}
		$query = "SELECT jp.id, jp.job_code, jp.job_name, jp.job_number, jp.requested_date, jp.job_status, jt.job_title , jt.id job_title_id FROM job_postings jp , job_titles jt WHERE jp.company_id = jt.company_id AND jp.company_id=".$get['company_id']." AND jp.job_code = jt.id AND jp.job_status = 0 ".$condition." AND jp.is_active =0";
		$job_postings = $callConfig->getAllRows($query);
		foreach($job_postings as $posts){			
			$post_data[$posts->job_title_id]['id'] =$posts->job_title_id;
			$post_data[$posts->job_title_id]['name'] = $posts->job_title;
			$post_data[$posts->job_title_id]['data'][]=$posts;
		}
		$return['job_postings'] = $post_data;
		//$return['sql']  = $query;

		$query = "SELECT id, reason_name FROM candidate_hold_rejection_reasons WHERE company_id=".$get['company_id']." AND is_active =0";
		$hold_rejection = $callConfig->getAllRows($query);
		foreach($hold_rejection as $hr){
			$holdr[$hr->id] = $hr->reason_name;
		}
		$return['hold_rejection'] = $holdr;

		if($get['post_id']!=""){
			$query = "SELECT * FROM job_postings jp WHERE jp.company_id=".$get['company_id']." AND jp.id=".$get['post_id']." ".$condition." AND jp.is_active =0";
			$job_data = $callConfig->getRow($query);
			$return['sql']  = $query;
			$return['jobpost'] = $job_data;
			$query = "SELECT * FROM status_progress WHERE company_id=".$get['company_id']." AND table_name='candidates' AND is_active =0 ORDER BY status ASC";
			$return['status'] = $callConfig->getAllRows($query);
			$query = "SELECT COUNT(id) as cc FROM job_posting_views WHERE company_id = ".$get['company_id']." AND job_posting_id = ".$get['post_id']." AND is_active = 0";
			$job_view = $callConfig->getRow($query);			
			$return['viewed'] =  $job_view->cc;

			$query = "SELECT ca.id, ca.display_name, ca.email_id, ca.current_status, ca.created_date,jpc.updated_date,ca.updated_date ca_updated_date,jpc.hold_yes_no, jpc.rejection_yes_no, jpc.rejection_reason_id FROM candidates ca, job_posting_candidates jpc WHERE ca.company_id = jpc.company_id AND ca.company_id=".$get['company_id']." AND ca.id = jpc.candidate_id AND jpc.job_posting_id =".$get['post_id']." ".$condition." AND ca.is_active = 0 ORDER BY ca.created_date DESC";
			$return['candidates'] = $callConfig->getAllRows($query);

			$query = "SELECT jp.id, jp.requested_date, COUNT(jp.id) as candi, max(jpc.created_date) as lastdate FROM job_postings jp, job_posting_candidates jpc, candidates ca WHERE jp.company_id = jpc.company_id AND jp.company_id=".$get['company_id']." AND jp.id = jpc.job_posting_id AND jp.job_code =".$job_data->job_code." AND ca.company_id = jp.company_id AND jpc.candidate_id = ca.id AND ca.current_status = ".ST_LOIACCEPTED." ".$condition." GROUP BY jp.id";
			$allposts = $callConfig->getAllRows($query);
			//print_r($allposts); exit;
			$average  = 0;
			if(count($allposts)>0){
				foreach($allposts as $aldata){
					$date1 = strtotime($aldata->requested_date);
					$date2 = strtotime($aldata->lastdate);
					$diff = abs($date2 - $date1);
					$days =  floor($diff/ (60*60*24)); 
					//$average[] = $days/$aldata->candi;
					$avg = $avg+($days/$aldata->candi);
				}
				$average = round($avg/count($allposts));
			}

			
			$return['average'] = $average; 
		}
		return $return;
	}
	function togetcandidatebydisabilitygender($get){
		global $callConfig;
		$query = "SELECT jp.id, jp.job_code, jp.job_name, jp.job_number, jp.requested_date, jp.job_status, jt.job_title , jt.id job_title_id FROM job_postings jp , job_titles jt WHERE jp.company_id = jt.company_id AND jp.company_id=".$get['company_id']." AND jp.job_code = jt.id AND jp.job_status = 0 AND jp.is_active =0";
		$job_postings = $callConfig->getAllRows($query);
		foreach($job_postings as $posts){			
			$post_data[$posts->job_title_id]['id'] =$posts->job_title_id;
			$post_data[$posts->job_title_id]['name'] = $posts->job_title;
			$post_data[$posts->job_title_id]['data'][]=$posts;
		}
		$return['job_postings'] = $post_data;
		$whr ="";
		if($get['post_id']!=""){
			$whr.=" AND jpc.job_posting_id IN(".$get['post_id'].") ";
		}

		$query = "SELECT ca.disability,COUNT(ca.id) gc, sum(if(ca.gender='M',1,0)) ma,  sum(if(ca.gender='F',1,0)) fm ,sum(if(ca.gender='F' AND ca.current_status = 58 ,1,0)) fmhc, sum(if(ca.gender='M'  AND ca.current_status = 58,1,0)) mahc FROM candidates ca ,job_posting_candidates jpc WHERE ca.company_id = jpc.company_id AND ca.company_id = ".$get['company_id']." AND ca.id = jpc.candidate_id ".$whr." AND ca.created_date <= '".$get['todate']."' AND ca.created_date >='".$get['fromdate']."' AND ca.is_active =0 GROUP BY ca.disability";
		$return['data'] = $callConfig->getAllRows($query);
		return $return;
	}
	function toinsertreportviewdata($get){
		global $callConfig;
		$insert_array = array('company_id'=>$get['company_id'],'report_id'=>$get['myreport_id'],'report_date'=>date("Y-m-d"),'activity'=>'Viewed','user_id'=>$get['user_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('report_histories',$insert_array);
	}
	function togetcandidatebyracegender($get){
		global $callConfig;
		$query = "SELECT jp.id, jp.job_code, jp.job_name, jp.job_number, jp.requested_date, jp.job_status, jt.job_title , jt.id job_title_id FROM job_postings jp , job_titles jt WHERE jp.company_id = jt.company_id AND jp.company_id=".$get['company_id']." AND jp.job_code = jt.id AND jp.job_status = 0 AND jp.is_active =0";
		$job_postings = $callConfig->getAllRows($query);
		foreach($job_postings as $posts){			
			$post_data[$posts->job_title_id]['id'] =$posts->job_title_id;
			$post_data[$posts->job_title_id]['name'] = $posts->job_title;
			$post_data[$posts->job_title_id]['data'][]=$posts;
		}
		$return['job_postings'] = $post_data;
		
		$query = "SELECT id, race_code, race_name FROM candidate_races WHERE company_id=".$get['company_id']." AND is_active =0";
		$return['races'] = $callConfig->getAllRows($query);
		$whr ="";
		if($get['post_id']!=""){
			$whr.=" AND jpc.job_posting_id IN(".$get['post_id'].") ";
		}
		$query = "SELECT ca.race_id,COUNT(ca.id) gc, sum(if(ca.gender='M',1,0)) ma,  sum(if(ca.gender='F',1,0)) fm ,sum(if(ca.gender='F' AND ca.current_status = 58 ,1,0)) fmhc, sum(if(ca.gender='M'  AND ca.current_status = 58,1,0)) mahc FROM candidates ca ,job_posting_candidates jpc WHERE ca.company_id = jpc.company_id AND ca.company_id = ".$get['company_id']." AND ca.id = jpc.candidate_id ".$whr." AND ca.created_date <= '".$get['todate']."' AND ca.created_date >='".$get['fromdate']."' AND ca.is_active =0 GROUP BY ca.race_id";
		$return['data'] = $callConfig->getAllRows($query);
		return $return;
	}
	function togetcandidatebyveterangender($get){
		global $callConfig;
		$query = "SELECT jp.id, jp.job_code, jp.job_name, jp.job_number, jp.requested_date, jp.job_status, jt.job_title , jt.id job_title_id FROM job_postings jp , job_titles jt WHERE jp.company_id = jt.company_id AND jp.company_id=".$get['company_id']." AND jp.job_code = jt.id AND jp.job_status = 0 AND jp.is_active =0";
		$job_postings = $callConfig->getAllRows($query);
		foreach($job_postings as $posts){			
			$post_data[$posts->job_title_id]['id'] =$posts->job_title_id;
			$post_data[$posts->job_title_id]['name'] = $posts->job_title;
			$post_data[$posts->job_title_id]['data'][]=$posts;
		}
		$return['job_postings'] = $post_data;
		
		$query = "SELECT id, veteran_code, veteran_name FROM candidate_veterans WHERE company_id=".$get['company_id']." AND is_active =0";
		$return['veterans'] = $callConfig->getAllRows($query);
		$whr ="";
		if($get['post_id']!=""){
			$whr.=" AND jpc.job_posting_id IN(".$get['post_id'].") ";
		}
		$query = "SELECT ca.veteran_id,COUNT(ca.id) gc, sum(if(ca.gender='M',1,0)) ma,  sum(if(ca.gender='F',1,0)) fm ,sum(if(ca.gender='F' AND ca.current_status = 58 ,1,0)) fmhc, sum(if(ca.gender='M'  AND ca.current_status = 58,1,0)) mahc FROM candidates ca ,job_posting_candidates jpc WHERE ca.company_id = jpc.company_id AND ca.company_id = ".$get['company_id']." AND ca.id = jpc.candidate_id ".$whr." AND ca.created_date <= '".$get['todate']."' AND ca.created_date >='".$get['fromdate']."' AND ca.is_active =0 GROUP BY ca.veteran_id";
		$return['data'] = $callConfig->getAllRows($query);
		return $return;
	}
	function togetcandidatesources($get){
		global $callConfig;
		$query = "SELECT id, job_name, job_number, requested_date,job_status FROM job_postings WHERE company_id=".$get['company_id']." AND job_status = 0 AND is_active =0";
		$query = "SELECT jp.id, jp.job_code, jp.job_name, jp.job_number, jp.requested_date, jp.job_status, jt.job_title , jt.id job_title_id FROM job_postings jp , job_titles jt WHERE jp.company_id = jt.company_id AND jp.company_id=".$get['company_id']." AND jp.job_code = jt.id AND jp.job_status = 0 AND jp.is_active =0";
		$job_postings = $callConfig->getAllRows($query);
		foreach($job_postings as $posts){			
			$post_data[$posts->job_title_id]['id'] =$posts->job_title_id;
			$post_data[$posts->job_title_id]['name'] = $posts->job_title;
			$post_data[$posts->job_title_id]['data'][]=$posts;
		}

		$return['job_postings'] = $post_data;

		$query = "SELECT id, hiring_source_name, color_code, hiring_source_logo FROM job_hiring_source WHERE company_id=".$get['company_id']." AND is_active =0 ORDER BY display_priority";
		$job_hiring_source = $callConfig->getAllRows($query);
		foreach($job_hiring_source as $hiring_source){
			$source[$hiring_source->id] = $hiring_source;
		}
		$return['job_hiring_source'] = $source;
		$query = "SELECT id,  reason_name FROM candidate_hold_rejection_reasons WHERE company_id=".$get['company_id']." AND is_active =0";
		$hold_rejection = $callConfig->getAllRows($query);
		foreach($hold_rejection as $hr){
			$holdr[$hr->id] = $hr->reason_name;
		}
		$return['hold_rejection'] = $holdr;
		$query = "SELECT * FROM status_progress WHERE company_id=".$get['company_id']." AND table_name='candidates' AND is_active =0 ORDER BY status ASC";
		$return['status'] = $callConfig->getAllRows($query);

			/*$query = "SELECT COUNT(id) as cc FROM job_posting_views WHERE company_id = ".$get['company_id']." AND job_posting_id = ".$get['post_id']." AND is_active = 0";
			$job_view = $callConfig->getRow($query);			
			$return['viewed'] =  $job_view->cc;*/
			$whr ="";
			if($get['post_id']!=""){
				$whr.=" AND jpc.job_posting_id IN(".$get['post_id'].") ";
			}
			if($get['hiring_source_id']!=""){
				$whr.=" AND jpc.job_hiring_source_id IN(".$get['hiring_source_id'].") ";
			}
			$query = "SELECT ca.id, ca.display_name, ca.email_id, ca.current_status, ca.created_date,jpc.updated_date,ca.updated_date ca_updated_date,jpc.hold_yes_no, jpc.rejection_yes_no, jpc.rejection_reason_id, jpc.job_hiring_source_id FROM candidates ca, job_posting_candidates jpc WHERE ca.company_id = jpc.company_id AND ca.company_id=".$get['company_id']." AND ca.id = jpc.candidate_id ".$whr." AND ca.created_date <= '".$get['todate']."' AND ca.created_date >='".$get['fromdate']."' AND ca.is_active = 0 ORDER BY ca.created_date DESC";
			$return['candidates'] = $callConfig->getAllRows($query);
			return $return;
	}
	function togetemployeeageprofile($post, $get){
		global $callConfig;
		//echo "Ravi----------------------"; exit;
		$return['menu_data'] = $this->toreportempdropdown($get);

		$whr = "";
		$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
		foreach($whrarry  as $wrkey=>$whra){
			if( isset($post[$whra]) && count($post[$whra])>0){
				$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
			}
		}

		$query = "SELECT 
		emp.id, 
		emp.emp_code, 
		emp.display_name, 
		emp.image, 
		CONCAT( FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365)) age,
		(
		SELECT 
		department_name 
		FROM 
		`departments` dep 
		WHERE 
		dep.company_id = emp.company_id 
		AND dep.id = emp.department_id
		AND dep.is_active = emp.is_active
		) department_name, 
		(
		SELECT 
		designation_name 
		FROM 
		`designations` des 
		WHERE 
		des.is_active = emp.is_active 
		AND des.company_id = emp.company_id 
		AND des.id = emp.designation_id
		) designation_name,
		(
		SELECT 
		job_title 
		FROM 
		`job_titles` jbt 
		WHERE 
		jbt.company_id = emp.company_id 
		AND jbt.id = emp.job_title_id
		AND jbt.is_active = emp.is_active 
		) job_title, 
		(
		SELECT 
		lc.location_name 
		FROM 
		locations lc 
		WHERE 
		lc.company_id = emp.company_id 
		AND lc.id = emp.location_id
		AND lc.is_active = emp.is_active 
		) location_name,
		(
		SELECT 
		es.employment_status_name 
		FROM 
		employment_statuses es 
		WHERE 
		es.company_id = emp.company_id 
		AND es.id = emp.employee_type
		AND es.is_active = emp.is_active 
		) employment_status_name,
		(
		SELECT 
		divi.division_name 
		FROM 
		divisions divi 
		WHERE 
		divi.company_id = emp.company_id 
		AND divi.id = emp.division_id
		AND divi.is_active = emp.is_active 
		) division_name
		FROM 
		employees emp
		where 
		emp.company_id = ".$get['company_id']."
		AND (
		CASE WHEN (
		emp.date_of_leaving IS NOT NULL 
		AND emp.date_of_leaving >= now()
		) THEN 1 WHEN (
		emp.date_of_leaving IS NULL 
		AND emp.is_active = 0
		) THEN 1 ELSE 2 END
		)= 1 ".$whr."
		AND emp.is_active = '0' 
		order by 
		emp.display_name ASC";
 		//echo $query; exit;
		$return['emp_query'] = $query;
		$return['emp_data'] = $callConfig->getAllRows($query);
		$query = "SELECT  CONCAT( FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365 / 5 )* 5 + 1, '-', FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365 / 5 )* 5 + 5 ) AS age_range, COUNT(*) AS employee_count FROM employees emp WHERE emp.company_id = ".$get['company_id']." AND emp.date_of_birth <= DATE_SUB(NOW(), INTERVAL 20 YEAR)  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ".$whr."  GROUP BY FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365 / 5 ) HAVING age_range <= '60' ORDER BY age_range ASC";
		$return['age_range'] = $callConfig->getAllRows($query);
		return $return;
	}
	function toreportempdropdown($get){
		global $callConfig;
		$menudata['divisions']=array('table_name'=>'divisions','name'=>'Divisions','field'=>'division_name','data'=>'');
		$menudata['departments']=array('table_name'=>'departments','name'=>'Departments','field'=>'department_name','data'=>'');
		$menudata['locations']=array('table_name'=>'locations','name'=>'Location','field'=>'location_name','data'=>'');
		$menudata['designations']=array('table_name'=>'designations','name'=>'Designations','field'=>'designation_name','data'=>'');
		//$menudata['job_titles']=array('table_name'=>'job_titles','name'=>'Job Title','field'=>'job_title','data'=>'');
		$menudata['employment_statuses']=array('table_name'=>'employment_statuses','name'=>'Employment Statuses','field'=>'employment_status_name','data'=>'');

		foreach($menudata as $menuda){
			$query = "SELECT id,".$menuda['field']." FROM ".$menuda['table_name']." WHERE company_id = ".$get['company_id']." AND is_active = 0";
			$menudetails = $callConfig->getAllRows($query);
		//$menudetail = (array)$menudetails;
			$edata = array();
			foreach($menudetails as $menudeta){
				$filed = $menuda['field'];
			//echo $menudeta->$filed;
			//print_r($menudeta); exit;
				$edata[$menudeta->id] = $menudeta->$filed;;
			}
			$menudata[$menuda['table_name']]['data'] =  $edata;
		}
		return $menudata;
	} 
	function togetemployeebirthdays($post,$get){
		global $callConfig;
		$return['menu_data'] = $this->toreportempdropdown($get);
		$whr = "";
		$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
		foreach($whrarry  as $wrkey=>$whra){
			if(isset($post[$whra]) && count($post[$whra])>0){
				$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
			}
		}
		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.date_of_birth, emp.wedding_date FROM employees emp WHERE emp.company_id = ".$get['company_id']."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ".$whr." AND emp.is_active = 0";
		$return['emp_query'] = $query;
		$return['emp_data'] = $callConfig->getAllRows($query);

	  	/*$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.wedding_date FROM employees emp WHERE emp.company_id = 1 AND emp.wedding_date != '' AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ".$whr." AND emp.is_active = 0";
		$return['emp_wedding_query'] = $query;
	  	$return['emp_wedding_data'] = $callConfig->getAllRows($query);
		*/
  		return  $return;
 	}
 	function togetemployeeeodetails($post, $get){
 		global $callConfig;
 		$return['menu_data'] = $this->toreportempdropdown($get);
 		$whr = "";
 		if($post['fromdate']!="" and $post['todate']!=""){
 			$whr.= " AND emp.joining_date <= '".$post['todate']."' AND  ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving > '".$post['fromdate']."'  ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1";
 		}else{
 			$whr.= " AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ";
 		}

 		$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
 		foreach($whrarry  as $wrkey=>$whra){
 			if(isset($post[$whra]) && count($post[$whra])>0){
 				$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
 			}
 		}
 		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender FROM employees emp WHERE emp.company_id = '".$get['company_id']."' ".$whr." AND emp.is_active = 0";
 		$return['emp_query'] = $query;
 		$return['emp_data'] = $callConfig->getAllRows($query);
 		$query = "SELECT id, race_name FROM races WHERE company_id = ".$get['company_id']." AND is_active =0 ";
 		$return['races'] = $callConfig->getAllRows($query);
 		$query = "SELECT id, veteran_code,	veteran_name FROM veterans WHERE company_id = ".$get['company_id']." AND is_active =0 ";
 		$return['veterans'] = $callConfig->getAllRows($query);
 		$query = "SELECT id, job_title,job_category_id,eeo_category_id FROM job_titles WHERE company_id = ".$get['company_id']." AND is_active =0 ";
 		$return['job_titles'] = $callConfig->getAllRows($query);
 		$query = "SELECT id, eeo_category_name FROM  eeo_categories WHERE company_id = ".$get['company_id']." AND is_active =0 ";
 		$return['eeo_categories'] = $callConfig->getAllRows($query);
 		return $return;
 	}
 	function toinsertapplication_table_histories_over($get){
 		global $callConfig;
 		$insert ="data";
 		for($i=1; $i<111; $i=$i+50){
 			$query = "SELECT * FROM  employees WHERE company_id = ".$get['company_id']." AND id= ".$i;
 			$empdata = $callConfig->getRow($query);
	  	//print_r($empdata); echo "<br>";
 			$empdata = (array)$empdata;
	  	//print_r($empdata); echo "<br>";
 			$jempdata = json_encode($empdata,1);
	  	//echo $jempdata;echo "<br>";
 			$insert_array = array('company_id'=>$get['company_id'], 'application_table_name'=>'employees', 'transaction_id' => $i, 'old_data'=>$jempdata, 'new_data'=>$jempdata, 'is_active'=>'0', 'current_status'=>'0');
 			$insert_res[] = $callConfig->insertRecord('application_table_histories',$insert_array);
	  	//print_r($insert); echo "<br>"; exit;
 		}
 		return $insert_res;
 	}
  	function togetreportemploymentstatushistory($post, $get){
	  	global $callConfig;
	  	$return['menu_data'] = $this->toreportempdropdown($get);
	  	$whr = "";
	  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	  	foreach($whrarry  as $wrkey=>$whra){
	  		if(isset($post[$whra]) && count($post[$whra])>0){
	  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
	  		}
	  	}
	  	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, apth.old_data, apth.new_data, apth.created_date FROM application_table_histories apth, employees emp WHERE apth.company_id = emp.company_id And apth.transaction_id = emp.id AND apth.application_table_name = 'employees' ";
	  	$return['emp_query'] = $query;
	  	$return['emp_data'] = $callConfig->getAllRows($query);
	  	
	  	return $return;
  	}
  	function togetemployeegenderprofile($post, $get){
	  	global $callConfig;
	  	$return['menu_data'] = $this->toreportempdropdown($get);
	  	$whr = "";
	  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	  	foreach($whrarry  as $wrkey=>$whra){
	  		if(isset($post[$whra]) && count($post[$whra])>0){
	  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
	  		}
	  	}
	  	$query = "SELECT 
	  	emp.id, 
	  	emp.emp_code, 
	  	emp.display_name, 
	  	emp.image, 
	  	emp.gender,
	  	(
	  	SELECT 
	  	department_name 
	  	FROM 
	  	`departments` dep 
	  	WHERE 
	  	dep.company_id = emp.company_id 
	  	AND dep.id = emp.department_id
	  	AND dep.is_active = emp.is_active
	  	) department_name, 
	  	(
	  	SELECT 
	  	designation_name 
	  	FROM 
	  	`designations` des 
	  	WHERE 
	  	des.is_active = emp.is_active 
	  	AND des.company_id = emp.company_id 
	  	AND des.id = emp.designation_id
	  	) designation_name,
	  	(
	  	SELECT 
	  	job_title 
	  	FROM 
	  	`job_titles` jbt 
	  	WHERE 
	  	jbt.company_id = emp.company_id 
	  	AND jbt.id = emp.job_title_id
	  	AND jbt.is_active = emp.is_active 
	  	) job_title, 
	  	(
	  	SELECT 
	  	lc.location_name 
	  	FROM 
	  	locations lc 
	  	WHERE 
	  	lc.company_id = emp.company_id 
	  	AND lc.id = emp.location_id
	  	AND lc.is_active = emp.is_active 
	  	) location_name,
	  	(
	  	SELECT 
	  	es.employment_status_name 
	  	FROM 
	  	employment_statuses es 
	  	WHERE 
	  	es.company_id = emp.company_id 
	  	AND es.id = emp.employee_type
	  	AND es.is_active = emp.is_active 
	  	) employment_status_name,
	  	(
	  	SELECT 
	  	divi.division_name 
	  	FROM 
	  	divisions divi 
	  	WHERE 
	  	divi.company_id = emp.company_id 
	  	AND divi.id = emp.division_id
	  	AND divi.is_active = emp.is_active 
	  	) division_name
	  	FROM 
	  	employees emp
	  	where 
	  	emp.company_id = ".$get['company_id']."
	  	AND (
	  	CASE WHEN (
	  	emp.date_of_leaving IS NOT NULL 
	  	AND emp.date_of_leaving >= now()
	  	) THEN 1 WHEN (
	  	emp.date_of_leaving IS NULL 
	  	AND emp.is_active = 0
	  	) THEN 1 ELSE 2 END
	  	)= 1 ".$whr."
	  	AND emp.is_active = '0' 
	  	order by 
	  	emp.display_name ASC";
	  //echo $query;
	  	$return['emp_query'] = $query;
	  	$return['emp_data'] = $callConfig->getAllRows($query);
	  	return $return;
  	}
  	function togetreportsalaryhistory($post, $get){
  		global $callConfig;
  		$return['menu_data'] = $this->toreportempdropdown($get);
  		$whr = "";
  		$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
  		foreach($whrarry  as $wrkey=>$whra){
  			if(isset($post[$whra]) && count($post[$whra])>0){
  				$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
  			}
  		}
  		$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, st.ctc_yearly,st.ctc_monthly,st.affected_date FROM salary_template st, employees emp WHERE st.company_id = emp.company_id AND st.company_id = 1 AND st.employee_id = emp.id AND (
  		CASE WHEN (
  		emp.date_of_leaving IS NOT NULL 
  		AND emp.date_of_leaving >= now()
  		) THEN 1 WHEN (
  		emp.date_of_leaving IS NULL 
  		AND emp.is_active = 0
  		) THEN 1 ELSE 2 END
  		)= 1 ".$whr." AND st.employee_id IN (SELECT employee_id FROM salary_template WHERE company_id =1 GROUP By employee_id  HAVING COUNT(employee_id)>1) ORDER BY emp.display_name";
	  	$return['emp_query'] = $query;
	  	$return['emp_data'] = $callConfig->getAllRows($query);
  		return $return;
  	}	
  	function togetemployeaddtionterminations($post,$get){
	  	global $callConfig;
	  	$return['menu_data'] = $this->toreportempdropdown($get);
	  	$whr = "";
	  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	  	foreach($whrarry  as $wrkey=>$whra){
	  		if(isset($post[$whra]) && count($post[$whra])>0){
	  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
	  		}
	  	}
	  	$query = "SELECT 
	  	emp.id, 
	  	emp.emp_code, 
	  	emp.display_name, 
	  	emp.image, 
	  	emp.gender,
	  	(
	  	SELECT 
	  	department_name 
	  	FROM 
	  	`departments` dep 
	  	WHERE 
	  	dep.company_id = emp.company_id 
	  	AND dep.id = emp.department_id
	  	AND dep.is_active = emp.is_active
	  	) department_name, 
	  	(
	  	SELECT 
	  	designation_name 
	  	FROM 
	  	`designations` des 
	  	WHERE 
	  	des.is_active = emp.is_active 
	  	AND des.company_id = emp.company_id 
	  	AND des.id = emp.designation_id
	  	) designation_name,
	  	(
	  	SELECT 
	  	job_title 
	  	FROM 
	  	`job_titles` jbt 
	  	WHERE 
	  	jbt.company_id = emp.company_id 
	  	AND jbt.id = emp.job_title_id
	  	AND jbt.is_active = emp.is_active 
	  	) job_title, 
	  	(
	  	SELECT 
	  	lc.location_name 
	  	FROM 
	  	locations lc 
	  	WHERE 
	  	lc.company_id = emp.company_id 
	  	AND lc.id = emp.location_id
	  	AND lc.is_active = emp.is_active 
	  	) location_name,
	  	(
	  	SELECT 
	  	es.employment_status_name 
	  	FROM 
	  	employment_statuses es 
	  	WHERE 
	  	es.company_id = emp.company_id 
	  	AND es.id = emp.employee_type
	  	AND es.is_active = emp.is_active 
	  	) employment_status_name,
	  	(
	  	SELECT 
	  	divi.division_name 
	  	FROM 
	  	divisions divi 
	  	WHERE 
	  	divi.company_id = emp.company_id 
	  	AND divi.id = emp.division_id
	  	AND divi.is_active = emp.is_active 
	  	) division_name,
	  	emp.joining_date,
	  	emp.date_of_leaving
	  	FROM 
	  	employees emp
	  	where 
	  	emp.company_id = ".$get['company_id']."
	  	AND( emp.joining_date >= '".$post['fromdate']."'
	  	AND emp.joining_date <= '".$post['todate']."')
	  	OR (emp.date_of_leaving >= '".$post['fromdate']."'
	  	AND emp.date_of_leaving <= '".$post['todate']."')
	  	order by 
	  	emp.display_name ASC";
	  	//echo $query;
	  	$return['emp_query'] = $query;
	  	$return['emp_data'] = $callConfig->getAllRows($query);
	  	return $return;
  	}
  function togetemployeemployeeheadcount($post, $get){
  	global $callConfig;
  	$return['menu_data'] = $this->toreportempdropdown($get);
  	$whr = "";
  	if($post['fromdate']!="" and $post['todate']!=""){
  		$whr.= " AND emp.joining_date <= '".$post['todate']."' ";
  	}
  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
  	foreach($whrarry  as $wrkey=>$whra){
  		if(isset($post[$whra]) && count($post[$whra])>0){
  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
  		}
  	}
  	$query ="SELECT 
  	emp.id, 
  	emp.emp_code, 
  	emp.display_name, 
  	emp.image, 
  	emp.gender,
  	(
  	SELECT 
  	department_name 
  	FROM 
  	`departments` dep 
  	WHERE 
  	dep.company_id = emp.company_id 
  	AND dep.id = emp.department_id
  	AND dep.is_active = emp.is_active
  	) department_name, 
  	(
  	SELECT 
  	designation_name 
  	FROM 
  	`designations` des 
  	WHERE 
  	des.is_active = emp.is_active 
  	AND des.company_id = emp.company_id 
  	AND des.id = emp.designation_id
  	) designation_name,
  	(
  	SELECT 
  	job_title 
  	FROM 
  	`job_titles` jbt 
  	WHERE 
  	jbt.company_id = emp.company_id 
  	AND jbt.id = emp.job_title_id
  	AND jbt.is_active = emp.is_active 
  	) job_title, 
  	(
  	SELECT 
  	lc.location_name 
  	FROM 
  	locations lc 
  	WHERE 
  	lc.company_id = emp.company_id 
  	AND lc.id = emp.location_id
  	AND lc.is_active = emp.is_active 
  	) location_name,
  	(
  	SELECT 
  	es.employment_status_name 
  	FROM 
  	employment_statuses es 
  	WHERE 
  	es.company_id = emp.company_id 
  	AND es.id = emp.employee_type
  	AND es.is_active = emp.is_active 
  	) employment_status_name,
  	(
  	SELECT 
  	divi.division_name 
  	FROM 
  	divisions divi 
  	WHERE 
  	divi.company_id = emp.company_id 
  	AND divi.id = emp.division_id
  	AND divi.is_active = emp.is_active 
  	) division_name,
  	emp.joining_date
  	FROM 
  	employees emp
  	where 
  	emp.company_id = ".$get['company_id']."
  	".$whr."
  	AND emp.is_active = '0' 
  	order by 
  	emp.display_name ASC";
  	$return['emp_query'] = $query;
  	$return['emp_data'] = $callConfig->getAllRows($query);
  	return $return;

  }
  function togetemployeemployeeturnover($post, $get){
  	global $callConfig;
  	$return['menu_data'] = $this->toreportempdropdown($get);
  	$whr = "";
  	if($post['fromdate']!="" and $post['todate']!=""){
  		$whr.= " AND emp.joining_date <= '".$post['todate']."' ";
  	}
  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
  	foreach($whrarry  as $wrkey=>$whra){
  		if(isset($post[$whra]) && count($post[$whra])>0){
  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
  		}
  	}
  	$query ="SELECT 
  	emp.id, 
  	emp.emp_code, 
  	emp.display_name, 
  	emp.image, 
  	emp.gender,
  	(
  	SELECT 
  	department_name 
  	FROM 
  	`departments` dep 
  	WHERE 
  	dep.company_id = emp.company_id 
  	AND dep.id = emp.department_id
  	AND dep.is_active = emp.is_active
  	) department_name, 
  	(
  	SELECT 
  	designation_name 
  	FROM 
  	`designations` des 
  	WHERE 
  	des.is_active = emp.is_active 
  	AND des.company_id = emp.company_id 
  	AND des.id = emp.designation_id
  	) designation_name,
  	(
  	SELECT 
  	job_title 
  	FROM 
  	`job_titles` jbt 
  	WHERE 
  	jbt.company_id = emp.company_id 
  	AND jbt.id = emp.job_title_id
  	AND jbt.is_active = emp.is_active 
  	) job_title, 
  	(
  	SELECT 
  	lc.location_name 
  	FROM 
  	locations lc 
  	WHERE 
  	lc.company_id = emp.company_id 
  	AND lc.id = emp.location_id
  	AND lc.is_active = emp.is_active 
  	) location_name,
  	(
  	SELECT 
  	es.employment_status_name 
  	FROM 
  	employment_statuses es 
  	WHERE 
  	es.company_id = emp.company_id 
  	AND es.id = emp.employee_type
  	AND es.is_active = emp.is_active 
  	) employment_status_name,
  	(
  	SELECT 
  	divi.division_name 
  	FROM 
  	divisions divi 
  	WHERE 
  	divi.company_id = emp.company_id 
  	AND divi.id = emp.division_id
  	AND divi.is_active = emp.is_active 
  	) division_name,
  	emp.joining_date
  	FROM 
  	employees emp
  	where 
  	emp.company_id = ".$get['company_id']."
  	AND (
  	CASE WHEN (
  	emp.date_of_leaving IS NOT NULL 
  	AND emp.date_of_leaving >= now()
  	) THEN 1 WHEN (
  	emp.date_of_leaving IS NULL 
  	AND emp.is_active = 0
  	) THEN 1 ELSE 2 END
  	)= 2
  	".$whr."
  	AND emp.is_active = '0' 
  	order by 
  	emp.display_name ASC";
  	$return['emp_query'] = $query;
  	$return['emp_data'] = $callConfig->getAllRows($query);
  	return $return;

  }
  function togetreportlastpaychange($post, $get){
  	global $callConfig;
  	$return['menu_data'] = $this->toreportempdropdown($get);
  	$whr = "";
  	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
  	foreach($whrarry  as $wrkey=>$whra){
  		if(isset($post[$whra]) && count($post[$whra])>0){
  			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
  		}
  	}
  	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, emp.department_id, emp.designation_id, st.ctc_yearly,st.ctc_monthly,st.affected_date FROM salary_template st, employees emp WHERE st.company_id = emp.company_id AND st.company_id = 1 AND st.employee_id = emp.id AND (
  	CASE WHEN (
  	emp.date_of_leaving IS NOT NULL 
  	AND emp.date_of_leaving >= now()
  	) THEN 1 WHEN (
  	emp.date_of_leaving IS NULL 
  	AND emp.is_active = 0
  	) THEN 1 ELSE 2 END
  )= 1 ".$whr." AND emp.is_active = '0'  ORDER BY emp.display_name, st.affected_date";
  $return['emp_query'] = $query;
  $return['emp_data'] = $callConfig->getAllRows($query);
  return $return;
}
function togetreportemployeesatisfaction($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	$query= "SELECT id,survey_question FROM survey_setup_questions WHERE company_id = 1 AND survey_id=1";
	$ques = $callConfig->getAllRows($query);
	foreach($ques as $que){
		$sur_que[$que->id] = $que->survey_question;
	}
	$return['survey_question'] = $sur_que;
	$query= "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, emp.department_id, emp.designation_id, srd.question_id, srd.response_option, srd.response_start_date FROM employees emp, survey_responses_details srd WHERE srd.company_id = emp.company_id AND srd.company_id = 1 AND srd.employee_id = emp.id AND ".$whr." srd.survey_id = 1";
	$return['survey_response'] = $callConfig->getAllRows($query);
	return $return;
}
function togetreportemployeewelbeing($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	$query= "SELECT id,survey_question,question_title FROM survey_setup_questions WHERE company_id = ".$get['company_id']." AND survey_id=4";
	$ques = $callConfig->getAllRows($query);
	foreach($ques as $que){
		$sur_que[$que->id] = $que;
	}
	$query= "SELECT id, question_id, survey_question_option FROM survey_setup_question_options WHERE company_id = 1 AND survey_id=4";
	$quesop = $callConfig->getAllRows($query);
	foreach($quesop as $quop){
		$sur_que[$quop->question_id]->options = json_decode($quop->survey_question_option,1);
	}
	$return['survey_question'] = $sur_que;
	$query= "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, emp.department_id, emp.designation_id,CONCAT( FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365)) age, emp.division_id,emp.location_id,CONCAT( FLOOR( DATEDIFF(NOW(), emp.joining_date)/ 365)) joinyears, srd.question_id, srd.response_option, srd.response_start_date FROM employees emp, survey_responses_details srd WHERE srd.company_id = emp.company_id AND srd.company_id = ".$get['company_id']." AND srd.employee_id = emp.id AND ".$whr." srd.survey_id=4";
	$return['survey_response'] = $callConfig->getAllRows($query);
	$empquery = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.employee_type, emp.department_id, emp.designation_id,CONCAT( FLOOR( DATEDIFF(NOW(), emp.date_of_birth)/ 365)) age, emp.division_id,emp.location_id,CONCAT( FLOOR( DATEDIFF(NOW(), emp.joining_date)/ 365)) joinyears FROM employees emp WHERE emp.company_id = ".$get['company_id']." AND emp. is_active = 0 ".$whr;
	$return['employees'] = $callConfig->getAllRows($empquery);
	return $return;
}
function togetstandaredapprovedhours($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	if($post['fromdate']!="" and $post['todate']!=""){
		$whr.= " AND eda.attendace_date >= '".$post['fromdate']."' AND eda.attendace_date <= '".$post['todate']."'";
	}

	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	//$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender FROM employees emp WHERE emp.company_id = '".$get['company_id']."' ".$whr." AND emp.is_active = 0";
	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender, eda.attendace_date, eda.clock_in, eda.clock_out, TIMESTAMPDIFF(MINUTE, eda.clock_in , eda.clock_out) as worktime FROM employees emp, employee_daily_attendances eda WHERE emp.company_id = eda.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = eda.employee_id ".$whr."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ".$whr." AND eda.is_active=0 ORDER BY emp.display_name";
	$return['emp_query'] = $query;
	$return['emp_data'] = $callConfig->getAllRows($query);
	return $return;
}
function togetstandaredtimeoffuser($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	if($post['fromdate']!="" and $post['todate']!=""){
		$whr.= " AND el.leave_from >= '".$post['fromdate']."' AND el.leave_to <= '".$post['todate']."'";
	}

	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	//$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender FROM employees emp WHERE emp.company_id = '".$get['company_id']."' ".$whr." AND emp.is_active = 0";
	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days,el.special_request, el.updated_date, el.application_date FROM employees emp, employee_leaves el WHERE emp.company_id = el.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = el.employee_id ".$whr."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1  AND el.current_status = 199 AND el.is_active=0  ORDER BY emp.display_name";
	$return['emp_query'] = $query;
	$return['emp_data'] = $callConfig->getAllRows($query); 
	$query= "SELECT id,leave_type_name FROM leave_types WHERE company_id = '".$get['company_id']."'";
	$ques = $callConfig->getAllRows($query);
	foreach($ques as $que){
		$sur_que[$que->id] = $que->leave_type_name;
	}
	$return['leave_type'] = $sur_que;
	return $return;
}
function togetstandaredtimeoffbalances($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	/*if($post['fromdate']!="" and $post['todate']!=""){
		$whr.= " AND el.leave_from >= '".$post['fromdate']."' AND el.leave_to <= '".$post['todate']."'";
	}*/

	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	
	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender, eb.employee_id, sum(eb.applicable_leaves) applicable_leaves, sum(consumed_leaves) consumed_leaves, '0' as begining,'0' as schedule, '0' as adjustment FROM employees emp, emp_leave_balances eb WHERE emp.company_id = eb.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = eb.employee_id ".$whr."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 AND leave_year=".$_POST['assessment_year']."  AND eb.is_active=0  GROUP BY eb.employee_id  ORDER BY emp.display_name";
	$return['emp_query'] = $query;
	$return['emp_data'] = $callConfig->getAllRows($query); 
	$query= "SELECT id,leave_type_name FROM leave_types WHERE company_id = '".$get['company_id']."'";
	$ques = $callConfig->getAllRows($query);
	foreach($ques as $que){
		$sur_que[$que->id] = $que->leave_type_name;
	}
	$return['leave_type'] = $sur_que;
	return $return;
}
function togetstandaredtimeoffschedule($post, $get){
	global $callConfig;
	$return['menu_data'] = $this->toreportempdropdown($get);
	$whr = "";
	$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
	foreach($whrarry  as $wrkey=>$whra){
		if(isset($post[$whra]) && count($post[$whra])>0){
			$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
		}
	}
	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days,el.special_request, el.updated_date, el.application_date, el.current_status FROM employees emp, employee_leaves el WHERE emp.company_id = el.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = el.employee_id ".$whr."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1  AND el.current_status = 199 AND el.is_active=0  ORDER BY emp.display_name";
	$return['emp_query'] = $query;
	$return['emp_data'] = $callConfig->getAllRows($query); 
	$query= "SELECT id,leave_type_name,back_ground_color FROM leave_types WHERE company_id = '".$get['company_id']."'";
	$ques = $callConfig->getAllRows($query);
	foreach($ques as $que){
		$sur_que[$que->id] = $que->leave_type_name;
	}
	$return['leave_type'] = $sur_que;
	$return['all_leave_type'] = $ques;
	return $return;
}
function togetleavelstemployeeajax($get){
	global $callConfig;
	$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.race_id, emp.veteran_id, emp.job_title_id, emp.gender, el.leave_type_id, el.leave_from, el.leave_to, el.leave_days,el.special_request, el.updated_date, el.application_date, el.current_status, lt.leave_type_name, lt.back_ground_color FROM employees emp, employee_leaves el, leave_types lt WHERE emp.company_id = el.company_id AND  emp.company_id = lt.company_id AND emp.company_id = '".$get['company_id']."' AND emp.id = el.employee_id AND el.leave_type_id = lt.id   AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1  AND el.current_status = 199 AND el.is_active=0  ORDER BY emp.display_name";
	return $callConfig->getAllRows($query);
}
function togetprojectinsertdata($get){
	global $callConfig;
	$query = "SELECT id,display_name FROM customers WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['customers'] = $callConfig->getAllRows($query);
	$query = "SELECT id,display_name,email_id,image FROM employees WHERE company_id =".$get['company_id']." and is_active = 0";
	$return['employees'] = $callConfig->getAllRows($query);
	$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'PRIORITYTYPES' AND is_active = 0";
	$return['priorits'] = $callConfig->getAllRows($query);
	$query = "SELECT id,role_name FROM employment_roles WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['roles'] = $callConfig->getAllRows($query);
	$query = "SELECT id,name FROM project_work_spaces WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['project_work_spaces'] = $callConfig->getAllRows($query);
	
	return $return;
}
function submitsprojectinsert($post, $get){
	global $callConfig;
	if($post['work_space_id']=="" && $post['work_space']!=""){
		$insert_array = array('company_id'=>$get['company_id'], 'name'=>$post['work_space'], 'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
		$work = $callConfig->insertRecord('project_work_spaces',$insert_array);
		$post['work_space_id'] = $work['LASTID'];	
	}
	$planned_start = date("Y-m-d",strtotime(str_replace('/"', '-', $post['planned_start'])));
	$planned_finish = date("Y-m-d",strtotime(str_replace('/"', '-', $post['planned_finish'])));
	$release_date = date("Y-m-d",strtotime(str_replace('/"', '-', $post['release_date'])));
	/*if($post['project_id']!= ""){
		$insert_array = array('project_name'=>$post['project_name'], 'description'=>$post['description'], 'customer_id'=>$post['customer_id'], 'planned_start'=>$planned_start, 'planned_finish'=>$planned_finish, 'priority'=>$post['priority'], 'actual_resources'=>$post['actual_resources'], 'planned_resources'=>$post['planned_resources'], 'billing_method_id'=> $post['billing_method_id'], 'project_cost'=>$post['project_cost'], 'budget_type'=>$post['budget_type'], 'updated_by'=>$get['user_id']);
		$res['update'] = $callConfig->updateRecord('projects',$insert_array,'id',$post['project_id']);
		
	}else{ }*/
		$insert_array = array('company_id'=>$get['company_id'], 'project_work_space_id'=> $post['work_space_id'], 'project_name'=>$post['project_name'], 'description'=>$post['description'],'project_type'=>$post['project_type'], 'customer_id'=>$post['customer_id'], 'assigner_id'=>$post['assigner_id'],'planned_start'=>$planned_start, 'planned_finish'=>$planned_finish, 'priority'=>$post['priority'], 'actual_resources'=>$post['actual_resources'], 'planned_resources'=>$post['planned_resources'], 'billing_method_id'=>$post['billing_method_id'], 'project_cost'=>$post['project_cost'], 'budget_type'=>$post['budget_type'], 'release_date'=>$release_date, 'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
		$result = $callConfig->insertRecord('projects',$insert_array);
		$post['project_id'] = $result['LASTID'];
		$project_id = $result['LASTID'];
		$res['insert'] = $result; 
		/*$status = array('0'=>'To DO','101'=>'Doing','5'=>'On Hold','199'=>'Completed');
		$k=10;
		foreach($status  as $skey=>$sta){
			$insert_array = array('company_id'=>$get['company_id'], 'project_id'=> $result['LASTID'], 'table_name'=>'project_task_details', 'name'=>$sta, 'status'=>$skey, 'action_label'=>$sta,'display_priority'=>$k,'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);	
			$kresult['insert'][] = $insert_array;
			$kresult['res'][] = $callConfig->insertRecord('project_task_status',$insert_array);
			$k = $k+10;
		}*/
		$query = "INSERT INTO `project_phases`(`company_id`, `project_id`, `project_phase_name`, `description`, `is_active`, `current_status`, `created_by`) SELECT `company_id`, ".$project_id.", `project_phase_name`, `description`, `is_active`, `current_status`, ".$get['user_id']." FROM application_project_phases WHERE company_id =".$get['company_id']." AND is_active =0";
		$phase = $callConfig->executeQuery($query);

		$query = "INSERT INTO `project_task_status`( `company_id`, `project_id`, `table_name`, `name`, `status`, `action_label`, `display_priority`, `is_active`, `current_status`, `created_by`) SELECT `company_id`, ".$project_id.", 'project_task_details', `name`, `status`, `action_label`, `display_priority`, `is_active`, `current_status`, ".$get['user_id']." FROM project_status WHERE company_id =".$get['company_id']." AND is_active = 0";
		$pstatus = $callConfig->executeQuery($query);
		
	//$res['task'] = $this->toinsertprojecttasks($post, $get);
	$res['project_team'] = $this->toinsertprojectteams($post, $get);
	return $res['insert'];
}
function toinsertprojecttasks($post, $get){
	global $callConfig;
	foreach($post['task_name'] as $key=>$task){
		/*if($post['task_id'][$key]!=""){
			$insert_array = array('customer_id'=>$post['customer_id'], 'task_name'=>$task, 'description'=>$post['tesk_descrption'][$key], 'priority'=>$post['task_priority'][$key], 'assigen_id'=>$post['task_assigner'][$key]);
 			$res[] = $callConfig->updateRecord(TBL_PROJECT_TASK_DETAILS, $insert_array, 'id', $post['project_id']);
 		}else{}*/
 		$insert_array = array('company_id'=>$get['company_id'], 'task_type'=>2,'project_id'=>$post['project_id'], 'customer_id'=>$post['customer_id'], 'task_name'=>$task, 'description'=>$post['tesk_descrption'][$key], 'priority'=>$post['task_priority'][$key], 'assigen_id'=>$post['task_assigner'][$key],  'is_active'=>$post['id'], 'current_status'=>'0','is_active'=>'0', 'created_by'=>$get['user_id']);
 			$res[] = $callConfig->insertRecord(TBL_PROJECT_TASK_DETAILS,$insert_array);
 		
	}
	return $res;
}
function toinsertprojectteams($post, $get){
	global $callConfig;
	$query = "UPDATE project_teams SET is_active = 1 WHERE  project_id = ".$post['project_id'];
	$dataa = $callConfig->executeQuery($query);
	$insert_array = array('company_id'=>$get['company_id'], 'project_id'=>$post['project_id'], 'role_id'=>'1', 'user_id'=>$post['assigner_id'],  'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
 	$res[] = $callConfig->insertRecord('project_teams',$insert_array);
	foreach($post['project_teams'] as $key=>$team){
		$insert_array = array('company_id'=>$get['company_id'], 'project_id'=>$post['project_id'], 'role_id'=>$post['teamrole_id'][$key], 'user_id'=>$team,  'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
 		$res[] = $callConfig->insertRecord('project_teams',$insert_array);
	}
	return $res;
}

function togetprojectkanbanboarddetails($get){
	global $callConfig;
	$whr = "";
	$pquery = "SELECT id,project_work_space_id,project_name FROM projects WHERE company_id = '".$get['company_id']."' AND id IN (SELECT DISTINCT project_id FROM project_teams WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']."  AND is_active = 0) AND  is_active = 0";
	if($get['admin_type']==1){
		$pquery = "SELECT id,project_work_space_id,project_name FROM projects WHERE is_active = 0";
	}
	if($get['emps']!=""){
		$whr = " AND assigen_id IN (".$get['emps'].")";
		$pquery = "SELECT id,project_work_space_id,project_name FROM projects WHERE id IN (SELECT DISTINCT project_id FROM `project_task_details` WHERE company_id = '".$get['company_id']."'".$whr." AND is_active = 0) AND is_active =0";
	}
	$allprojects = $callConfig->getAllRows($pquery);
	foreach($allprojects as $allproject){
		$workspaceproject[$allproject->project_work_space_id][] = $allproject;
	}
	$return['work_project'] = $workspaceproject;
	if($get['filter_lable']!=""){
		$query = "SELECT transaction_id FROM application_attach_labels WHERE company_id =".$get['company_id']." AND application_tablename ='project_task_details' AND application_label_id IN (".$_GET['filter_lable'].")"; 
		$alllabels  = $callConfig->getAllRows($query);
		foreach($alllabels as $laa){
			$taskla[] = $laa->transaction_id;
		}
		$whr.= " AND id IN (".implode(",",$taskla).")";
		//print_r($whr); 
	}
	if($get['filter_day']!=""){
		if($get['filter_day']=='today'){
			$fdate = date('Y-m-d');
			$ldate = date('Y-m-d');
		}
		if($get['filter_day']=='week'){
			$now = time();
			$day_of_week = (int)date('w', $now);
			$week_start = mktime(0, 0, 0, date('n', $now), date('j', $now)-$day_of_week, date('Y', $now));
			$fdate	= date("Y-m-d", $week_start);
			$week_end = date("Y-m-d",mktime(23, 59, 59, date('n', $week_start), date('j', $week_start)+6, date('Y', $week_start)));
			$ldate = $week_end;
		}
		if($get['filter_day']=='quater'){
			$current_quarter = ceil(date('n') / 3);
			$ncurrent_quarter = $current_quarter+1;
			$fdate = date('Y-m-d', strtotime(date('Y').'-'.(($current_quarter*3)-2).'-1'));
			$ldate = date('Y-m-d', strtotime(date('Y').'-'.(($ncurrent_quarter*3)-2).'-1'));
		}
		if($get['filter_day']=='half'){
			$current_half = ceil(date('n') / 6);
			$fdate = date("Y")."-01-01";
			$ldate = date("Y")."-06-30";
			if($current_half ==2){
				$fdate = date("Y")."-07-01";
				$ldate = date("Y")."-12-31";
			}
		}
		if($get['filter_day']=='yearly'){
			$fdate = date("Y")."-01-01";
			$ldate = date("Y")."-12-31";
		}
		$whr.= " AND planned_start <= '".$fdate."' AND planned_finish >= '".$ldate."'";
	}
	if($get['filter_due']!=""){
		$now = date('Y-m-d');
		if($get['filter_due']=='overdue'){
			$duedate = date('Y-m-d');
			$whr.=" AND planned_finish <'".$duedate."' AND current_status !=199";
		}
		if($get['filter_due']=='nextday'){
			
			$duedate = date("Y-m-d" , strtotime('+1 day', strtotime($now)));
			$whr.=" AND planned_finish ='".$duedate."' AND current_status !=199";
		}
		if($get['filter_due']=='nextweek'){
			$duedate = date("Y-m-d" , strtotime('+7 day', strtotime($now)));
			$whr.=" AND planned_finish > ".$now."  AND planned_finish <='".$duedate."' AND current_status !=199";
		} 
		if($get['filter_due']=='nextmonth'){
			$duedate = date("Y-m-d" , strtotime('+31 day', strtotime($now)));
			$whr.=" AND planned_finish > ".$now."  AND planned_finish <'".$duedate."' AND current_status !=199";
		} 
		if($get['filter_due']=='complete'){
			$duedate = date("Y-m-d" , strtotime('+31 day', strtotime($now)));
			$whr.="  AND current_status =199";
		}
		if($get['filter_due']=='noncomplete'){
			$duedate = date("Y-m-d" , strtotime('+31 day', strtotime($now)));
			$whr.="  AND current_status !=199";
		}
	}	
	//print_r($whr); 
	$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."'  AND project_id = '".$get['project_id']."'  AND project_id = '".$get['project_id']."' AND is_active = 0 ORDER BY display_priority ASC";
	$return['status']  = $callConfig->getAllRows($query);

	$query = "SELECT id,name FROM project_work_spaces WHERE company_id =".$get['company_id']." AND is_active = 0";
	$pjres = $callConfig->getAllRows($query);
	foreach($pjres as $pres){
		$pjeres[$pres->id] = $pres->name;
	}
	$return['project_work_spaces'] = $pjeres;	
	$query = "SELECT * FROM projects WHERE company_id =".$get['company_id']." AND id = ".$get['project_id']." AND is_active = 0";
	$return['projects'] = $callConfig->getRow($query); 
	$query = "SELECT * FROM ".TBL_PROJECT_TASK_DETAILS." WHERE company_id =".$get['company_id']." AND project_id = '".$get['project_id']."'".$whr." AND is_active = 0"; 
	$return['taskdetils'] = $callConfig->getAllRows($query); 	
	$query = "SELECT emp.id, emp.display_name, emp.user_email email_id, emp.user_name , emp.user_photo image FROM project_teams ptt, application_users emp WHERE ptt.company_id = emp.company_id AND ptt.company_id =".$get['company_id']."  AND ptt.user_id = emp.id AND ptt.project_id = ".$get['project_id']."  AND ptt.is_active =0";
	$pttres = $callConfig->getAllRows($query);
	foreach($pttres as $ptres){
		$teamres[$ptres->id] = $ptres;
	}
	$return['project_teams'] = $teamres;
	$query = "SELECT COUNT(id) entotal, transaction_id, SUM(IF(current_status =4,1,0)) comp FROM application_check_lists  WHERE parent_id IN ( SELECT acl.id FROM application_check_lists acl, project_task_details ptd WHERE acl.company_id = ptd.company_id AND acl.company_id =".$get['company_id']." AND acl.application_tablename = 'project_task_details' AND acl.transaction_id = ptd.id AND acl.parent_id =0 AND ptd.project_id = ".$get['project_id']." AND acl.is_active = 0) AND is_active =0 GROUP BY transaction_id";
	$alltaskchck = $callConfig->getAllRows($query);
	foreach($alltaskchck as $checklist){
		$listcheck[$checklist->transaction_id] = $checklist;
	}
	$return['checklist'] = $listcheck;
	$query = "SELECT aal.transaction_id, al.label_code FROM application_attach_labels aal, application_labels al , project_task_details ptd WHERE aal.company_id = al.company_id AND aal.company_id = ptd.company_id AND aal.company_id = ".$get['company_id']." AND aal.application_label_id = al.id AND aal.transaction_id = ptd.id AND ptd.project_id = ".$get['project_id']." AND aal.is_active = 0";
	$pttlares = $callConfig->getAllRows($query);
	foreach($pttlares as $ptlares){
		$tasklab[$ptlares->transaction_id][]=$ptlares->label_code;
	}
	$return['task_label'] = $tasklab;
	$query = "SELECT id,label_name,label_code FROM `application_labels` WHERE  company_id = ".$get['company_id']." AND work_id =".$get['project_id']." AND `application_tablename` = 'project_task_details' AND is_active = 0";
	$allcolor = $callConfig->getAllRows($query);
	if(count($allcolor)==0){
		$query = "SELECT id,label_name,label_code FROM `application_labels` WHERE  company_id = ".$get['company_id']." AND work_id =0 AND `application_tablename` = 'project_task_details' AND is_active = 0";
		$allcolor = $callConfig->getAllRows($query);
	}
	$return['project_label'] = $allcolor;
	$query = "SELECT id,display_name,user_email as email_id,user_photo image FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;

	$query = "SELECT `project_task_id`, `dependence_task_id` FROM `project_task_dependencies` WHERE  `company_id` = ".$get['company_id']." AND  `project_id` = ".$get['project_id']." AND `is_active` =0";
	$projec_task_dependencies = $callConfig->getAllRows($query);
	$return['project_task_dependencies_query'] = $query;
	foreach($projec_task_dependencies as $projec_task_dependency){
		$dppids[$projec_task_dependency->project_task_id][] = $projec_task_dependency->dependence_task_id;
	}
	$return['projec_task_dependencies'] = $dppids;

	$query = "SELECT id, project_phase_name FROM project_phases WHERE  `company_id` = ".$get['company_id']." AND  `project_id` = ".$get['project_id']." AND `is_active` =0";
	$phases = $callConfig->getAllRows($query);
	foreach($phases as $phase){
		$pphase[$phase->id] = $phase;
	}
	$return['phases'] = $pphase;
	return $return;
}

function togetprojecttaskdetailspopup($get){
	global $callConfig;
	$query = "SELECT * FROM ".TBL_PROJECT_TASK_DETAILS." WHERE company_id =".$get['company_id']." AND id = ".$get['task_id'];
	$res = $callConfig->getRow($query); 
	$get['project_id'] = $res->project_id;
	$return['taskdetils'] = $res;
	$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."'  AND project_id = '".$get['project_id']."'  AND project_id = '".$get['project_id']."' AND is_active = 0 ORDER BY display_priority ASC";
	$return['status']  = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  application_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get['project_id'].") AND is_active = 0";
	$lableres = $callConfig->getAllRows($query);
	$lab = array();
	foreach($lableres as $lable){
		$lab[$lable->work_id][$lable->id] = $lable;
	}
	$return['labels'] = count((array)$lab[$get['project_id']])>0 ? $lab[$get['project_id']] : $lab['0'];
	$query = "SELECT application_label_id FROM  application_attach_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['task_id']." AND is_active=0";
	$attachres = $callConfig->getAllRows($query);
	foreach($attachres as $attachr){
		$alab[] = $attachr->application_label_id;
	}
	$return['attach_labels'] = $alab;
	$query = "SELECT id, parent_id, check_name,assign_id, current_status FROM  application_check_lists WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND transaction_id = ".$get['task_id']." AND is_active =0";
	$attachres = $callConfig->getAllRows($query);
	foreach($attachres as $cha){
		if($cha->parent_id==0){
			$checklist[$cha->id] = $cha;
		}else{
			$subchecklist[$cha->parent_id][]=$cha;
		}		
	}
	$return['checklist'] = $checklist;
	$return['subchecklist'] = $subchecklist;
	$query = "SELECT id,transaction_id, activity_type, filed_name, comments, created_date, created_by FROM project_task_activities WHERE  company_id =".$get['company_id']." AND application_table_name = '".TBL_PROJECT_TASK_DETAILS."'  AND transaction_id = ".$get['task_id']." AND is_active =0 ORDER BY id DESC";
	$return['activty'] = $callConfig->getAllRows($query);
	$return['activty_query'] = $query;
	$query = "SELECT emp.id, emp.display_name, emp.user_email email_id, emp.user_name, emp.user_photo image FROM project_teams ptt, application_users emp WHERE ptt.company_id = emp.company_id AND ptt.company_id =".$get['company_id']."  AND ptt.user_id = emp.id AND ptt.project_id = ".$get['project_id']."  AND ptt.is_active =0";
	$pttres = $callConfig->getAllRows($query);
	foreach($pttres as $ptres){
		$teamres[$ptres->id] = $ptres;
	}
	$return['project_teams'] = $teamres;
	$query = "SELECT id,display_name,user_email as email_id,user_photo image FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	$query = "SELECT id,file_name,actual_file_name FROM application_attachments WHERE company_id =".$get['company_id']." AND transaction_id =".$get['task_id']." AND application_table_name='project_task_details' AND is_active =0";
	$return['attachment_query'] = $query;
	$return['attachments'] = $callConfig->getAllRows($query);
	$query = "SELECT * FROM projects WHERE id =".$get['project_id'];
	$return['projects'] = $callConfig->getRow($query);
	$query = "SELECT id,task_start FROM project_task_time_log WHERE company_id = ".$get['company_id']." AND task_end is NULL AND task_id = ".$get['task_id']." AND is_active = 0 ORDER BY id DESC LIMIT 0,1";
	$return['project_task_time_log'] = $callConfig->getRow($query);
	$query = "SELECT id, task_name FROM ".TBL_PROJECT_TASK_DETAILS." WHERE company_id = ".$get['company_id']." AND  project_id = ".$get['project_id']." AND is_active = 0";
	$return['projec_tasks_query'] = $query;
	$return['projec_tasks'] = $callConfig->getAllRows($query);

	$query = "SELECT id, dependence_task_id FROM project_task_dependencies WHERE company_id = ".$get['company_id']." AND  project_id = ".$get['project_id']." AND project_task_id =".$get['task_id']." AND is_active = 0";
	$return['projec_tasks_dependencies_query'] = $query;
	$projec_task_dependencies = $callConfig->getAllRows($query);
	foreach($projec_task_dependencies as $projec_task_dependency){
		$dppids[] = $projec_task_dependency->dependence_task_id;
	}
	$return['projec_task_dependencies'] = $dppids;

	$query = "SELECT id, project_phase_name FROM project_phases WHERE  `company_id` = ".$get['company_id']." AND  `project_id` = ".$get['project_id']." AND `is_active` =0 ";
	$phases = $callConfig->getAllRows($query);
	foreach($phases as $phase){
		$pphase[$phase->id] = $phase;
	}
	$return['phases'] = $pphase;
	$query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get['company_id']." AND is_active = 0 ORDER BY display_priority";
	$return['roles'] = $callConfig->getAllRows($query);

	$query = "SELECT id, project_role_id, user_id FROM project_task_teams WHERE company_id =".$get['company_id']." AND project_task_id = ".$get['task_id']."  AND is_active = 0";
	$return['task_team'] = $callConfig->getAllRows($query);

	$query = "SELECT id, task_name, assigen_id, planned_finish, current_status FROM project_task_details WHERE company_id =".$get['company_id']." AND parent_id = ".$get['task_id']."  AND is_active = 0";
	$return['sub_tasks'] = $callConfig->getAllRows($query);

	$query = "SELECT pts.id, pts.name, pts.status, pts.action_label, pts.display_priority, st.background_color, st.color FROM project_task_status pts, status st WHERE pts.company_id = st.company_id AND pts.status = st.name_value AND pts.company_id =".$get['company_id']." AND pts.table_name = 'project_task_details' AND pts.project_id =  '".$get['project_id']."' AND pts.is_active = 0 ORDER BY pts.display_priority ASC";
	$statuss = $callConfig->getAllRows($query);
	foreach($statuss as $status){
		$ss[$status->status] =$status; 
	}
	$return['project_status'] = $ss; 

	$query = "SELECT id, deliverable,deliverable_owner,deliverable_date FROM project_task_deliverables WHERE company_id =".$get['company_id']." AND project_task_id = ".$get['task_id']."  AND is_active = 0";
	$return['append_deliverables'] = $callConfig->getAllRows($query);

	$query = "SELECT * FROM general_code_details WHERE company_id = ".$get['company_id']." AND genc_code = 'TASKFREQUENCY' AND is_active = 0";
	$return['task_frequency'] = $callConfig->getAllRows($query);

	$query = "SELECT * FROM project_task_machine_material WHERE company_id =".$get['company_id']." AND project_task_id = ".$get['task_id']."  AND is_active = 0";
	$return['project_task_machine_material'] = $callConfig->getAllRows($query);

	$query = "SELECT id,name FROM project_machine_material WHERE company_id =".$get['company_id']." AND project_id = ".$get['project_id']."  AND is_active = 0";
	$machines = $callConfig->getAllRows($query);
	foreach($machines as $machine){
		$project_machine_material[$machine->id] = $machine->name;
	}

	$return['project_machine_material'] = $project_machine_material;

	return $return;
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
function togetprojectdashboarddetails($get){
	global $callConfig;
	$return['get'] = $get;
	$whr = ""; $projwhr = "";$prjcnt = 0; $top = array(); $pttwhr ="";
	
	if($get['role_id']!=""){
		$projectid = array();
		$query = "SELECT DISTINCT project_id FROM project_teams WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']."  AND is_active = 0";
		$teamproject = $callConfig->getAllRows($query);

		foreach($teamproject as $teampro){
			$pps[] = $teampro->project_id;
		}
		if(count((array)$pps)>0){
			$ptwhr = " AND id IN (".implode(",",$pps).")";
			$projwhr =  " AND proj.id IN (".implode(",",$pps).")";
			$pttwhr = " AND project_id IN (".implode(",",$pps).")";
		}else{
			$ptwhr = " AND id IN ('0')";
			$projwhr =  " AND proj.id IN ('0')";
			$pttwhr = " AND project_id IN (0)";
		}
	}
	$query = "SELECT id,project_name,project_logo,assigner_id,planned_start,  planned_finish, project_logo, created_date, current_status  FROM projects WHERE company_id ='".$get['company_id']."'".$ptwhr." AND is_active = 0";
	$projectdata = $callConfig->getAllRows($query);
	$return['project_query'] = $query;
	$return['kanprojects'] = $projectdata;
	foreach($projectdata as $res){
		$prjcnt++;
		$top[$res->current_status] = $top[$res->current_status]+1;
	}
	if($get['role_id']!=""){
	$wquery = "SELECT DISTINCT pws.id, pws.name, pws.created_date FROM  project_work_spaces pws, projects proj WHERE pws.company_id = ".$get['company_id']." AND proj.company_id = pws.company_id AND pws.id = proj.project_work_space_id".$projwhr;
	}else{
		$wquery = "SELECT  pws.id, pws.name, pws.created_date FROM  project_work_spaces pws WHERE pws.company_id = ".$get['company_id']." AND is_active = 0";	
	}
	//$wquery = "SELECT DISTINCT pws.id, pws.name, pws.created_date FROM  project_work_spaces pws, projects proj WHERE pws.company_id = ".$get['company_id']." AND proj.company_id = pws.company_id AND pws.id = proj.project_work_space_id".$projwhr;
	$return['worksopace_query'] = $wquery;
	$return['workspace'] = $callConfig->getAllRows($wquery);
	$top['total'] = $prjcnt;
	$return['toptotal'] = $top;	

	foreach($projectdata as $listdata){
		if($listdata->current_status==0){
			$return['recentlycreated'][] = $listdata;
		}elseif($listdata->current_status==4){
			$return['recentlycompleted'][] = $listdata;
		}else{			
			$return['recentlyinprogress'][] = $listdata;
		}
		$project_assign[$listdata->id] = $listdata->assigner_id; 
		$return['projects'][] = $listdata;
	}

	$return['project_assign'] = $project_assign;
	$query = "SELECT id, project_id, customer_id, task_name, planned_finish ,created_date,assigen_id, current_status FROM `project_task_details` WHERE company_id ='".$get['company_id']."' ".$pttwhr."  AND is_active = 0";
	$ptasks  = $callConfig->getAllRows($query);

	foreach($ptasks as $ptask){		
		$task[$ptask->project_id][] = $ptask;
		$emptask[$ptask->assigen_id][] = $ptask;
	}
	$return['tasksql'] = $query;
	$return['task'] = $task;

	$query = "SELECT id, project_id, user_id, role_id FROM project_teams WHERE company_id = '".$get['company_id']."'".$pttwhr." AND is_active=0";
	$teamsdata = $callConfig->getAllRows($query);
	foreach($teamsdata as $teamsdat){
		$projcetroleuser[$teamsdat->project_id][$teamsdat->role_id][] = $teamsdat->user_id;
	}
	$return['projcetroleuser'] = $projcetroleuser;
	$query = "SELECT id,user_name,display_name, user_email as email_id,user_photo image FROM application_users WHERE id IN ( SELECT DISTINCT(`assigen_id`) emps FROM `project_task_details` WHERE company_id =".$get['company_id']." AND assigen_id !='' ".$ptwhr." AND is_active =0 ) ";
	$return['project_employees'] =  $callConfig->getAllRows($query);
	$return['emloyee_tasks'] = $emptask;
	$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."' AND is_active = 0 ORDER BY display_priority ASC";
	$return['statusprogress']  = $callConfig->getAllRows($query);
	$query = "SELECT id, task_name, planned_start, planned_finish, created_date, current_status FROM `project_task_details` WHERE company_id ='".$get['company_id']."' ".$ptwhr." AND is_active = 0 ORDER BY created_date";
	$return['allprojects'] = $callConfig->getAllRows($query);
	$query = "SELECT id,display_name,user_email as email_id,user_photo image FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	$query = "SELECT * FROM application_users WHERE id NOT IN ( SELECT `assigen_id` FROM `project_task_details` WHERE current_status != ".ST_APPROVED." AND assigen_id is NOT NULL AND is_active = 0) AND role_id LIKE '%TASKDA_%' AND is_active =0";
	$unassigndata = $callConfig->getAllRows($query);
	$return['unassign'] = $unassigndata;
	$query = "SELECT * FROM application_users WHERE id  IN ( SELECT `assigen_id` FROM `project_task_details` WHERE current_status !=".ST_APPROVED." AND assigen_id is NOT NULL AND is_active = 0) AND role_id LIKE '%TASKDA%' AND is_active =0";
	$return['assign'] = $callConfig->getAllRows($query);

	//$query = "SELECT DISTINCT project_id, assigen_id FROM `project_task_details` WHERE company_id = '".$get['company_id']."' AND is_active = 0 GROUP BY assigen_id,project_id";
	$query = "SELECT DISTINCT project_id, user_id FROM `project_teams` WHERE company_id = '".$get['company_id']."' AND is_active = 0 ";
	$userprojects = $callConfig->getAllRows($query);
	foreach( $userprojects as $userproject){
		$userprojec[$userproject->user_id][$userproject->project_id] = $userproject;
	}
	$return['userprojects'] = $userprojec;
	/*$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = 'project_task_details' AND is_active = 0 ORDER BY display_priority ASC";
	$ptstatuss = $callConfig->getAllRows($query);
	foreach($ptstatuss as $ptstatus){
		$procttask[$ptstatus->status] = $ptstatus;
	}
	$return['project_task_status'] = $procttask;*/
	$query = "SELECT id,name,status,action_label,display_priority,project_id FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."' AND is_active = 0 ORDER BY display_priority ASC";
	$status  = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$project_task_status[$statu->project_id][$statu->status] = $statu;
	}
	$return['project_task_status'] = $project_task_status;
	$query = "SELECT id,name,status,action_label,display_priority FROM  status_progress WHERE company_id =".$get['company_id']." AND table_name = 'projects' AND is_active = 0 ORDER BY display_priority ASC";
	$pstatuss = $callConfig->getAllRows($query);
	foreach($pstatuss as $ptstatus){
		$proctt[$ptstatus->status] = $ptstatus;
	}
	$return['project_status'] = $proctt;
	return $return;
}

function togetprojectdashboarddetailsold($get){
	global $callConfig;
	$query = "SELECT count(id) cc,current_status FROM `projects` WHERE company_id =".$get['company_id']." AND is_active = 0 GROUP BY current_status";
	$ret = $callConfig->getAllRows($query);
	foreach($ret as $res){
		$top['total'] =  $top['total']+$res->cc;
		$top[$res->current_status] = $res->cc;
	}
	$return['toptotal'] = $top;
	$return['toptotal_query'] = $query;
	$query = "SELECT id, project_name, project_logo, planned_start, planned_finish, created_date, current_status FROM `projects` WHERE company_id =".$get['company_id']." AND current_status = 0 AND is_active = 0 ORDER BY created_date";
	$return['recentlycreated'] = $callConfig->getAllRows($query);
	$return['created'] = $query;
	$query = "SELECT id, project_name, project_logo, planned_finish, created_date, current_status FROM `projects` WHERE company_id =".$get['company_id']." AND current_status NOT IN(0,4)  AND is_active = 0 ORDER BY created_date";
	$return['recentlyinprogress'] = $callConfig->getAllRows($query);
	$query = "SELECT id, project_name, project_logo, planned_finish, created_date, current_status FROM `projects` WHERE company_id =".$get['company_id']." AND current_status = 4 AND is_active = 0 ORDER BY created_date";
	$return['recentlycompleted'] = $callConfig->getAllRows($query);
	$query = "SELECT id, name,created_date FROM `project_work_spaces` WHERE company_id =".$get['company_id']." AND is_active = 0 ORDER BY created_date";
	$return['worksopace_query'] = $query;
	$return['workspace'] = $callConfig->getAllRows($query);
	$query = "SELECT id, project_name, project_logo, planned_finish, created_date, current_status FROM `projects` WHERE company_id =".$get['company_id']."  AND is_active = 0";
	$return['projects'] = $callConfig->getAllRows($query);
	$query = "SELECT id, project_id, customer_id, task_name, planned_finish, created_date,assigen_id, current_status FROM `project_task_details` WHERE company_id =".$get['company_id']."  AND is_active = 0";
	$ptasks  = $callConfig->getAllRows($query);
	foreach($ptasks as $ptask){		
		$task[$ptask->project_id][] = $ptask;
		$emptask[$ptask->assigen_id][] = $ptask;
	}
	$return['task'] = $task;
	$query = "SELECT id,emp_code,display_name,email_id,image FROM employees WHERE id IN ( SELECT DISTINCT(`assigen_id`) emps FROM `project_task_details` WHERE company_id =".$get['company_id']." AND assigen_id !='' AND is_active =0 ) ";
	$return['project_employees'] =  $callConfig->getAllRows($query);
	$return['emloyee_tasks'] = $emptask;
	$query = "SELECT id,name,status,action_label,display_priority FROM  status_progress WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."' AND is_active = 0 ORDER BY display_priority ASC";
	$return['statusprogress']  = $callConfig->getAllRows($query);
	$query = "SELECT id, task_name, planned_start, planned_finish, created_date, current_status FROM `project_task_details` WHERE company_id =".$get['company_id']." AND is_active = 0 ORDER BY created_date";
	$return['allprojects'] = $callConfig->getAllRows($query);
	$query = "SELECT id,name,status,action_label,display_priority,project_id FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."' AND is_active = 0 ORDER BY display_priority ASC";
	$status  = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$project_task_status[$statu->project_id][$statu->status] = $statu;
	}
	$return['project_task_status'] = $project_task_status;
	$query = "SELECT id,display_name,user_photo as image,user_email FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	return $return;
}
function togettopprojecttasklist($get){
	global $callConfig;
	$query = "SELECT * FROM `projects` WHERE company_id =".$get['company_id']." AND  id IN (SELECT DISTINCT project_id FROM project_teams WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']."  AND is_active = 0) AND is_active = 0";
    if($get['admin_type']==1){
		$query = "SELECT * FROM `projects` WHERE company_id =".$get['company_id']."  AND is_active = 0";
	}

	$return['projects'] = $callConfig->getAllRows($query);
	$query = "SELECT id,display_name,image,email_id FROM employees";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	$query = "SELECT project_id, COUNT(id) total_project, sum(if(current_status =199,1,0)) complete FROM `project_task_details` WHERE company_id = ".$get['company_id']." AND is_active =0 GROUP BY project_id";
	$data = $callConfig->getAllRows($query);
	foreach($data as $dat){
		$ret[$dat->project_id] = $dat;
	}
	$return['tasks'] = $ret;
	return $return;
}
function togettopprojectworkspacelist($get){
	global $callConfig;
	$query = "SELECT * FROM `project_work_spaces` WHERE company_id =".$get['company_id']." AND is_active = 0";
	$pws = $callConfig->getAllRows($query);
	foreach($pws as $pw){
		$project_work_spaces[$pw->id] = $pw;
	}
	$return['project_work_spaces'] = $project_work_spaces;
	$query = "SELECT * FROM `projects` WHERE company_id =".$get['company_id']." AND is_active = 0";
	$projects = $callConfig->getAllRows($query);
	foreach($projects as $project){
		$projectslist[$project->project_work_space_id][$project->id] = $project;
	}
	$return['projects'] =$projectslist;
	$query = "SELECT id,display_name,image,email_id FROM employees";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	return $return;
}
function updatemoretabledata($post, $get){
	global $callConfig;
	$res=$callConfig->updateRecord($get['table_name'],$post,'id',$get['hid']);
	return $res;
}

function checkandupdatecolorprojecttask($post, $get){
	global $callConfig;
	$rres ="add";
	$query = "SELECT * FROM `application_attach_labels` WHERE company_id =".$get['company_id']." AND application_tablename='".$post['application_tablename']."' AND transaction_id=".$post['transaction_id']." AND application_label_id= ".$post['application_label_id']." AND is_active = 0";
	$row = $callConfig->getRow($query);
	$upquery = $callConfig->executeQuery("UPDATE `project_task_details` SET  to_date= ".date('Y-m-d')." WHERE id=".$post['transaction_id']);
	//$row['select_query'] =$query;
	$task_data = $callConfig->getRow("SELECT * FROM project_task_details WHERE id=".$post['transaction_id']);
	$label_data = $callConfig->getRow("SELECT * FROM application_labels WHERE id=".$post['application_label_id']);
	if($row->id !=""){
		$query = "UPDATE application_attach_labels SET is_active=1 WHERE company_id =".$get['company_id']." AND application_tablename='".$post['application_tablename']."' AND transaction_id=".$post['transaction_id']." AND application_label_id= ".$post['application_label_id'];
		//$return['update_query'] =$query;
		$row = $callConfig->executeQuery($query);
		$return['result'] =$row;
		$rres ="mins";
		$msg1 = "Task ".$task_data->task_name." is updated : Label (".$label_data->label_name.") is removed.";
	}else{
		$insert_array = array('company_id'=>$get['company_id'],'application_tablename'=>$post['application_tablename'],'transaction_id'=>$post['transaction_id'],'application_label_id'=>$post['application_label_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('application_attach_labels',$insert_array);
		//$return['insert_query'] =$insert_array;
		$return['result'] =$insert;
		$rres ="add";
		$msg1 = "Task ".$task_data->task_name." is updated : Label (".$label_data->label_name.") is added.";

	}
	$notuser1[] = $get['user_id'];
	$notuser1[] = $task_data->assigen_id;
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$transaction_number, 'application_table_name'=>'project_task_details', 'type'=>'INFORMATION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_PROJECT_KANBAN&TAB=3&RID='.$task_data->project_id,'module_setup_id'=>25);
	//$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$project->id, 'application_table_name'=>'project_teams', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_PROJECT_KANBAN&TAB=3&RID='.$project->id,'module_setup_id'=>25)
	if($get['mobile']!='Y'){
		$ss = $this->tosendnotifiations($notdata1);
	}
	
	return $return;
}

function togetlistprojecttaskdetailspopup($get){
	global $callConfig;
	$return = array();
	if($get['tasks']!=""){
	$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."'  AND project_id = '".$get['project_id']."' AND is_active = 0 ORDER BY display_priority ASC";
	$return['status']  = $callConfig->getAllRows($query);

	
	$query = "SELECT * FROM projects WHERE company_id =".$get['company_id']." AND id = ".$get['project_id']." AND is_active = 0";
	$return['projects'] = $callConfig->getRow($query); 
	$query = "SELECT * FROM ".TBL_PROJECT_TASK_DETAILS." WHERE company_id =".$get['company_id']."  AND id IN(".$get['tasks'].") AND  is_active = 0";
	$return['taskdetils'] = $callConfig->getAllRows($query); 	
	
	$query = "SELECT COUNT(id) entotal, transaction_id, SUM(IF(current_status =4,1,0)) comp FROM application_check_lists  WHERE parent_id IN ( SELECT acl.id FROM application_check_lists acl, project_task_details ptd WHERE acl.company_id = ptd.company_id AND acl.company_id =".$get['company_id']." AND acl.application_tablename = 'project_task_details' AND acl.transaction_id = ptd.id AND acl.parent_id =0 AND ptd.id IN (".$get['tasks'].") AND acl.is_active = 0) AND is_active =0 GROUP BY transaction_id";
	$alltaskchck = $callConfig->getAllRows($query);
	foreach($alltaskchck as $checklist){
		$listcheck[$checklist->transaction_id] = $checklist;
	}
	$return['checklist'] = $listcheck;
	$query = "SELECT aal.transaction_id, al.label_code FROM application_attach_labels aal, application_labels al , project_task_details ptd WHERE aal.company_id = al.company_id AND aal.company_id = ptd.company_id AND aal.company_id = ".$get['company_id']." AND aal.application_label_id = al.id AND aal.transaction_id = ptd.id AND ptd.id IN(".$get['tasks'].") AND aal.is_active = 0";
	$pttlares = $callConfig->getAllRows($query);
	foreach($pttlares as $ptlares){
		$tasklab[$ptlares->transaction_id][]=$ptlares->label_code;
	}
	$return['task_label'] = $tasklab;
	}
	return $return;
}
function todatainsertall(){
	global $callConfig;
	
	$type = array('6475d8ca35876faaa85dab21'=>1, '6475d8ca35876faaa85dab22'=>2, '6475d8ca35876faaa85dab23'=>3, '6475d8ca35876faaa85dab24'=>4, '6475d8ca35876faaa85dab25'=>5);
	$statu = array('6475d8eea65b7b3b34c81663'=>0, '6475d8f236cdc2f429eaeff5'=>3, '6475d8f4dd8d6230b4c5bf34'=>4,'6475d8f8df411a775a61aeff'=>6,'6475d90126fcfc676966df59'=>'10');


	$query = "SELECT * FROM edi_read WHERE application_table_name = 'project_task_details' AND id = 1";
	$rjdata = $callConfig->getRow($query);
	$jdata = $rjdata->content;
	$data = json_decode($jdata,1);
	$k=1;foreach($data as $dat){
		if($k!=1){
			$task_type =2;
			$task_name = $dat['data']['card']['name'];
			if($task_name==""){
				$task_name = "No - Name -".$dat['id'];
			}
			if(isset($dat['data']['list'])){
				$status = $statu[$dat['data']['list']['id']];
			}elseif(isset($dat['data']['listAfter'])){
				$status = $statu[$dat['data']['listAfter']['id']];
			}else{
				$status = $statu[$dat['data']['listBefore']['id']];
			}
			$jsdata = json_encode($dat,1);
			
			$insert_array = array('company_id'=>1,'project_id'=>1,'task_type'=>$task_type,'parent_id'=>0,'task_name'=>$task_name,'description'=>$task_name,'current_status'=>$status,'ref_data'=>$jsdata, 'is_active'=>'0','created_by'=>1);
			$insert[] = $callConfig->insertRecord(TBL_PROJECT_TASK_DETAILS,$insert_array);
			//echo "<pre>"; print_r($insert_array); print_r($dat); echo "</pre>"; exit;			
		}
	$k++; }
	echo "<pre>"; print_r($insert); echo "</pre>"; exit;
}
function insertstatusprocesss($post, $get){
	global $callConfig;
	$query = "SELECT max(`status`) max_status FROM `project_task_status` WHERE  company_id =".$get['company_id']." AND  `table_name` = 'project_task_details'  AND project_id = '".$post['project_id']."' AND status !=199 AND is_active = 0";
	$row = $callConfig->getRow($query);
	$res['query'] = $query;
	$status = $row->max_status;
	$res['status'] = $status;
	if($status < 101){
		$status = 101;
	}else{
		$status = $status+1;
	}
	$res['afterstatus'] = $status;
	$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'],'table_name'=>$post['table_name'], 'name'=>$post['listname'],'action_label'=>$post['listname'], 'status'=>$status, 'display_priority'=>$post['position'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
	$res['insert_array'] = $insert_array;
	$insert = $callConfig->insertRecord('project_task_status',$insert_array);
	return $insert;
}
function togetprojectviewalldetails($get){
	global $callConfig;
	$query="SELECT * FROM projects WHERE  company_id = ".$get['company_id']." AND id =".$get['project_id'];
	$return['project'] = $callConfig->getRow($query);
	$project = $return['project'];
	$query = "SELECT * FROM project_task_details WHERE company_id = ".$get['company_id']." AND project_id =".$get['project_id']." AND is_active = 0";
	$return['project_task_details'] = $callConfig->getAllRows($query);
	foreach($return['project_task_details'] as $task){
		$task_ids[] = $task->id;
		$emp_tasks[$task->assigen_id][] = $task->id;
		$emp_phaase_tasks[$task->project_phase_id][] = $task->id;
		$etasks[$task->id] = $task;

	}
	$return['emp_task'] = $emp_tasks;
	$return['emp_phaase_tasks'] = $emp_phaase_tasks;
	$return['list_task'] = $etasks;
	$query = "SELECT * FROM project_teams WHERE company_id = ".$get['company_id']." AND project_id =".$get['project_id']." AND is_active = 0";
	$return['project_teams'] = $callConfig->getAllRows($query);
	$query = "SELECT * FROM project_type WHERE company_id = ".$get['company_id']."  AND is_active = 0";
	$return['project_types'] = $callConfig->getAllRows($query);
	if(count((array)$task_ids)>0){
		$query = "SELECT * FROM project_task_activities WHERE company_id = ".$get['company_id']." AND application_table_name = 'project_task_details' AND  transaction_id IN (".implode(",",$task_ids).") AND is_active = 0";
		$return['project_task_activities_query'] = $query;
		$project_task_activities = $callConfig->getAllRows($query);
		foreach($project_task_activities  as $task_activities){
			$list_project_task_activities[$task_activities->transaction_id][] = $task_activities;
		}
		$return['project_task_activities'] = $list_project_task_activities;
		$query = "SELECT * FROM application_attach_labels company_id = ".$get['company_id']." AND application_table_name = 'project_task_details' AND  transaction_id IN (".implode(",",$task_ids).") AND is_active = 0";
		$application_attach_labels = $callConfig->getAllRows($query); 
		foreach($application_attach_labels  as $attach_labels){
			$list_attach_labels[$attach_labels->transaction_id] = $attach_labels;
		}
		$return['application_attach_labels'] = $list_attach_labels;
	}
	
	$query = "SELECT * FROM  application_labels WHERE company_id =".$get['company_id']." AND application_tablename = '".TBL_PROJECT_TASK_DETAILS."' AND work_id IN (0,".$get['project_id'].") AND is_active = 0";
	$lableres = $callConfig->getAllRows($query);
	foreach($lableres as $lable){
		$lab[$lable->work_id][$lable->id] = $lable;
	}
	$return['labels'] = $lab;
	$query="SELECT id, name FROM project_work_spaces WHERE  company_id = ".$get['company_id']." AND is_active =0";
	$return['work_spaces'] = $callConfig->getAllRows($query);
	$query="SELECT * FROM project_work_spaces WHERE  company_id = ".$get['company_id']." AND id =".$project->project_work_space_id;
	$return['project_work_spaces'] = $callConfig->getRow($query);
	$query = "SELECT id,display_name,user_email as email_id,user_photo as image, role_id  FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;
	$return['employees'] = $emps;
	$query = "SELECT id,name,status,action_label,display_priority FROM  status_progress WHERE company_id =".$get['company_id']." AND table_name = 'projects' AND is_active = 0 ORDER BY display_priority ASC";
	$return['project_status']  = $callConfig->getAllRows($query);
	
	$query = "SELECT pts.id, pts.name, pts.status, pts.action_label, pts.display_priority, st.background_color, st.color FROM project_task_status pts, status st WHERE pts.company_id = st.company_id AND pts.status = st.name_value AND pts.company_id =".$get['company_id']." AND pts.table_name = 'project_task_details' AND pts.project_id =  '".$get['project_id']."' AND pts.is_active = 0 ORDER BY pts.display_priority ASC";

	$return['task_status']  = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  status_progress WHERE company_id =".$get['company_id']." AND application_table_name	 = 'projects' AND 	transaction_id = ".$get['project_id']." AND is_active = 0 ";
	$return['project_files']  = $callConfig->getAllRows($query);
	$query = "SELECT id,display_name FROM customers WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['customers'] = $callConfig->getAllRows($query);
	$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'PRIORITYTYPES' AND is_active = 0";
	$return['priorits'] = $callConfig->getAllRows($query);
	$query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get['company_id']." AND is_active = 0 ORDER BY display_priority";
	$return['roles'] = $callConfig->getAllRows($query);
	$query = "SELECT id,project_phase_name  FROM project_phases WHERE company_id =".$get['company_id']." AND project_id = '".$get['project_id']."'  AND is_active = 0";
	$return['project_phases'] = $callConfig->getAllRows($query);
	$query = "SELECT count(id) total_task, SUM(if(current_status = ".ST_APPROVED.",1,0)) as complete_task FROM `project_task_details` WHERE company_id = ".$get['company_id']." AND project_id = '".$get['project_id']."' AND is_active = 0";
	$return['graph'] = $callConfig->getRow($query);
	if(count((array)$task_ids)>0){
		$query = "SELECT * FROM `application_attachments` WHERE company_id = ".$get['company_id']." AND application_table_name = 'project_task_details' AND transaction_id IN (".implode(",",$task_ids).")  AND is_active = 0";
		$attaches = $callConfig->getAllRows($query);
		foreach((array)$attaches as $attach){
			$attach_task[$attach->transaction_id][] = $attach;
		}		
	}
	$return['task_attachment'] = $attach_task;
	$query = "SELECT * FROM project_machine_material WHERE company_id =".$get['company_id']." AND project_id = '".$get['project_id']."' AND is_active = 0";
	$return['project_machine_material'] = $callConfig->getAllRows($query);
	return $return;
}
function addupdateprojectlabeldata($post,$get){
	global $callConfig;
	$query = "SELECT * FROM `application_labels` WHERE company_id =".$get['company_id']." AND application_tablename='project_task_details' AND work_id=".$post['project_id']." AND parent_id= ".$post['label_id']." AND is_active = 0";
	$row = $callConfig->getRow($query);
	$return['select_query'] =$query;
	if($row->id !=""){
		$query = "UPDATE application_labels SET is_active=1 WHERE company_id =".$get['company_id']." AND application_tablename='project_task_details' AND work_id=".$post['project_id']." AND parent_id= ".$post['label_id']." AND is_active = 0";
		$return['update_query'] =$query;
		$row = $callConfig->executeQuery($query);
		$rres ="mins";
	}else{
		$insert_array = array('company_id'=>$get['company_id'],'module_setup_id'=>25,'application_tablename'=>'project_task_details','parent_id'=>$post['label_id'],'label_name'=>$post['name'],'label_code'=>$post['code'],'work_id'=>$post['project_id'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('application_labels',$insert_array);
		$return['insert_query'] =$insert_array;
		$return['result'] =$insert;
		$rres ="add";
	}
	return $rres;
}
function updatelabelval($post,$get){
	global $callConfig;
	$query = "UPDATE application_labels SET label_name='".$post['name']."' WHERE company_id =".$get['company_id']." AND application_tablename='project_task_details' AND work_id=".$post['project_id']." AND parent_id= ".$post['label_id']." AND is_active = 0";
		$return['update_query'] =$query;
	$row = $callConfig->executeQuery($query);
	return array('query'=>$query,'res'=>$row);
}
function copynewcard($post, $get){
	global $callConfig;
	if($post['newtaskname']!=""){
		$query = "SELECT * FROM project_task_details WHERE id=".$post['copycardid'];
		$row = $callConfig->getRow($query);
		$insert_array = array('company_id'=>$row->company_id,'project_id'=>$row->project_id,'task_type'=>$row->task_type,'parent_id'=>$row->parent_id,'customer_id'=>$row->customer_id,'follower_ids'=>$row->follower_ids,'task_name'=>$row->task_name,'description'=>$row->description,'priority'=>$row->priority,'planned_start'=>$row->planned_start,'planned_finish'=>$row->planned_finish,'release_date'=>$row->release_date,'assigen_id'=>$row->assigen_id,'tester_id'=>$row->tester_id,'is_review'=>$row->is_review,'review_id'=>$row->review_id,'allow_watch'=>$row->allow_watch,'rate_hours'=>$row->rate_hours,'budget_hours'=>$row->budget_hours,'bill_type'=>$row->bill_type,'ref_data'=>$row->id,'is_active'=>$row->is_active,'current_status'=>$row->current_status,'created_by'=>$row->created_by);
		$insert_data = $callConfig->insertRecord('project_task_details',$insert_array);
		$card_id = $insert_data['LASTID'];
		$res_data['taskdata'] = $insert_data; 
		if($post['newlabel'] == 'on'){
			$query= "INSERT INTO `application_attach_labels`( `company_id`, `application_tablename`, `transaction_id`, `application_label_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_by`, `created_date`, `updated_by`, `updated_date`) SELECT `company_id`, `application_tablename`, ".$card_id.", `application_label_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_by`, `created_date`, `updated_by`, `updated_date` FROM `application_attach_labels` WHERE transaction_id = '".$post['copycardid']."' AND  application_tablename = 'project_task_details' ";
			$res_data['label'] = $callConfig->executeQuery($query);
		}
		if($post['newchecklist'] == 'on'){
			$query = "INSERT INTO `application_check_lists`( `company_id`, `application_tablename`, `transaction_id`, `parent_id`, `check_name`, `assign_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_by`, `created_date`, `updated_by`, `updated_date`) SELECT  `company_id`, `application_tablename`,  ".$card_id.", `parent_id`, `check_name`, `assign_id`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_by`, `created_date`, `updated_by`, `updated_date` FROM `application_check_lists` WHERE transaction_id = '".$post['copycardid']."' AND  application_tablename = 'project_task_details' ";
			$res_data['checklist'] = $callConfig->executeQuery($query);
		}
		if($post['newattachment'] == 'on'){
			$qurey = "INSERT INTO `application_attachments`(`company_id`, `application_table_name`, `transaction_id`, `temp_id`, `file_name`, `actual_file_name`, `email_attachement`, `file_size_mb`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date`) SELECT  `company_id`, `application_table_name`, ".$card_id.", `temp_id`, `file_name`, `actual_file_name`, `email_attachement`, `file_size_mb`, `is_active`, `current_status`, `status_date`, `from_date`, `to_date`, `created_date`, `created_by`, `updated_by`, `updated_date` FROM `application_attachments` WHERE transaction_id = '".$post['copycardid']."' AND  application_table_name = 'project_task_details' ";
			$res_data['attachments'] = $callConfig->executeQuery($query);
		}
		return $res_data;
	}
}

function togetuseralldetailsforuser($get){
	global $callConfig;
	$query = "SELECT au.id user_id, au.display_name, au.user_email, au.user_photo, pt.project_id, pr.project_name, pr.project_logo, pt.id task_id, pt.task_name, pt.planned_start, pt.planned_finish, pt.current_status FROM project_task_details pt, projects pr, application_users au WHERE pt.company_id = pr.company_id AND pt.company_id =".$get['company_id']." AND pt.project_id = pr.id AND pt.assigen_id = au.id AND pt.assigen_id = ".$get['userid']." AND pt.is_active = 0 ORDER BY pt.planned_start DESC";
	$return['data'] = $callConfig->getAllRows($query);
	$query = "SELECT id,name,status,action_label,display_priority FROM  project_task_status WHERE company_id =".$get['company_id']." AND table_name = '".TBL_PROJECT_TASK_DETAILS."' AND is_active = 0 ORDER BY display_priority ASC";
	$status  = $callConfig->getAllRows($query);
	foreach($status as $sta){
		$rstatus[$sta->status] = $sta;
	}
	$return['status'] = $rstatus; 
	return $return; 
}
function togetprojecteamaddetails($get){
	global $callConfig;
	/*$query = "SELECT id,display_name FROM  application_users WHERE company_id =".$get['company_id']." AND is_active = 0 ";
	$return['users']  = $callConfig->getAllRows($query);*/
	$query = "SELECT id,project_id,user_id FROM project_teams WHERE company_id =".$get['company_id']." AND user_id = ".$get['userid']." AND is_active = 0";
	$project_teams = $callConfig->getAllRows($query);
	$return['exist_projects_query'] = $query;

	foreach($project_teams as $project_team){
		$eproject[$project_team->project_id] =  $project_team->project_id;
	}
	$return['exist_projects'] = $eproject;

	$query = "SELECT id, project_name FROM projects WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['projects'] = $callConfig->getAllRows($query);
	$query = "SELECT id, role_name FROM project_roles WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['roles'] = $callConfig->getAllRows($query);
	return $return;	
}
function togetunassigntaskslist($get){
	global $callConfig;
	$query = "SELECT id,project_id,task_name  FROM project_task_details WHERE company_id =".$get['company_id']." AND project_id = ".$get['project_id']." AND  is_active = 0";
	$return = $callConfig->getAllRows($query);
	return $return;
}
function tocheckprojectteamuncompletetasks($get){
	global $callConfig;
	$query = "SELECT * FROM project_teams WHERE id = ".$get['rowid'];
	$data = $callConfig->getRow($query);
	$query = "SELECT count(id) as cc FROM project_task_details WHERE assigen_id = ".$data->user_id." AND project_id =".$data->project_id. " AND current_status !=4 AND is_active = 0";
	$countdata = $callConfig->getRow($query);
	return $countdata;
}

function tosendwhatsappdatatodatain($data){
    	global $callConfig;
    	$query = "SELECT * FROM whatsapp_config WHERE company_id =".$data['company_id']." AND is_active =0";
    	//echo $query;
    	$row = $callConfig->getRow($query);
    	//print_r($row); exit;
    	$authKey = $row->auth_key;
    	$data['send_no'] = $data['send_no']!="" ? $data['send_no'] : COMPANY_SEND_NO;
		$integrated_number = "91".$data['send_no'];
		$recipient_number = "91".$data['recivce_no'];
		$template_name = "meeting_confirmation"; 
		$language_code = "en";
		$template_variables = [
		    "John Doe",          // {{1}} - Recipient's name
		    "INFISUITE",         // {{2}} - Host's name or company
		    $data['message'],   // {{3}} - Topic
		    "2025-08-25",        // {{4}} - Date
		    "11:00 AM",          // {{5}} - Time
		    "123 Main St, Anytown" // {{6}} - Location
		];

		$payload = [
		    "integrated_number" => $integrated_number,
		    "content_type" => "template",
		    "payload" => [
		        "type" => "template",
		        "template" => [
		            "name" => $template_name,
		            "language" => [
		                "code" => $language_code,
		            ],
		            "to_and_components" => [
		                [
		                    "to" => [
		                        $recipient_number
		                    ],
		                    "components" => [
		                        "body_1" => [ "type" => "text", "value" => $template_variables[0] ],
		                        "body_2" => [ "type" => "text", "value" => $template_variables[1] ],
		                        "body_3" => [ "type" => "text", "value" => $template_variables[2] ],
		                        "body_4" => [ "type" => "text", "value" => $template_variables[3] ],
		                        "body_5" => [ "type" => "text", "value" => $template_variables[4] ],
		                        "body_6" => [ "type" => "text", "value" => $template_variables[5] ]
		                    ]
		                ]
		            ]
		        ]
		    ]
		];

		// --- Step 4: Make the cURL request to the Msg91 API ---
		$api_url = $row->api_url;

		$curl = curl_init();
		curl_setopt_array($curl, [
		    CURLOPT_URL => $api_url,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_CUSTOMREQUEST => "POST",
		    CURLOPT_POSTFIELDS => json_encode($payload),
		    CURLOPT_HTTPHEADER => [
		        "accept: application/json",
		        "authkey: " . $authKey,
		        "content-type: application/json"
		    ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
		    $return['status'] = 'Fail';
			  $return['res'] = "cURL Error #:" . $err;
			  $return['get'] = $data;
		} else {
		     $return['status'] = 'Fail';
			$return['res'] = "SMS config is not set";
			$return['get'] = $data;
		}
		return $return;
    }
function toaddupdatetheprojectteamdata($post,$get){
	global $callConfig;		
	$notification_message = '';
	$notuser2 = array(); $notuser1 = array();
	if($get['rid']!=""){
		$res =$callConfig->updateRecord($get['table'],$post,'id',$get['rid']);
		$query = "SELECT * FROM project_teams WHERE id = ".$get['rid'];
		$data = $callConfig->getRow($query);
		$upquery = "UPDATE project_task_details SET assigen_id = NULL WHERE assigen_id = ".$data->user_id." AND project_id =".$data->project_id." AND current_status !=4 AND is_active = 0";
		$ress= $callConfig->executeQuery($upquery);
		$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$data->user_id);
		$project = $callConfig->getRow("SELECT project_name FROM projects WHERE id=".$data->project_id);
		
		$msg1 = "You are now removed from ".$project->project_name;
		$notuser1 = array($post['user_id']);
		$msg2 = ucwords($user->display_name)." Member is removed to this project ".$project->project_name;
		$query = "SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND role_id !=2 AND project_id = ".$data->project_id." AND is_active = 0";
		$lists = $callConfig->getAllRows($query);
		foreach($lists as $list){
			$notuser2[] = $list->user_id;
		}
		$email_content_id =36;
	}else{		
		
		if($get['table']=='project_teams' && $post['role_id']=="1" && $get['rid']==""){
			$upquery =$callConfig->updateRecord('projects',array('assigner_id'=>$post['user_id']),'id',$post['project_id']);
		}
		$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['user_id']);
		$project = $callConfig->getRow("SELECT project_name FROM projects WHERE id=".$post['project_id']);
		
		$msg1 = "Welcome to ".$project->project_name.", you are added as a team member to this project";
		$notuser1 = array($post['user_id']);
		$msg2 = "New Member is added ".ucwords($user->display_name)." to this project ".$project->project_name;
		$query = "SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND role_id !=2 AND project_id = ".$post['project_id']."  AND is_active = 0";
		$lists = $callConfig->getAllRows($query);
		foreach($lists as $list){
			$notuser2[] = $list->user_id;
		}
		$post['company_id'] =  $post['company_id'] !="" ? $post['company_id'] : $get['company_id'];
		$res = $callConfig->insertRecord($get['table'],$post);
		$email_content_id =35;
	}
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$project->id, 'application_table_name'=>'project_teams', 'type'=>'INFORMATION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_PROJECT&TAB=3&RID='.$project->id,'module_setup_id'=>25);
	$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$project->id, 'application_table_name'=>'project_teams', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_PROJECT&TAB=3&RID='.$project->id,'module_setup_id'=>25);
	//$ss = $this->tosendnotifiations($notdata1);
	//$rss = $this->tosendnotifiations($notdata2);
	$senddata = array('session_company_id'=>$get['company_id'],'email_content_id'=>$email_content_id,'project_name'=>$project->project_name,'user_display_name'=>$user->display_name,'cc_users'=>$notuser2,'to_users'=>$notuser1);
	//$res = $this->emailsendmediater($senddata);
	return $res;
}
function updatestatusdynamicprojecttask($post, $get){
	global $callConfig;
	$whatsup = 'N';
	if($post['field']=='current_status' && $post['table_name'] =='project_task_details'){
		$whatsup = 'Y';
	}
	$query = "UPDATE ".$post['table_name']." SET ".$post['field']."=".$post['fielddata']." WHERE id=".$post['update_id'];
	$res = $callConfig->executeQuery($query);
	$query = "SELECT * FROM ".$post['table_name']." WHERE id =".$post['update_id'];
	$data = $callConfig->getRow($query);
	if($post['field'] == 'current_status' && $post['fielddata'] >0 && $post['table_name'] =='project_task_details' ){
		//echo "dayyyy";
		 $query = "UPDATE projects SET current_status = 3 WHERE id=".$data->project_id;
		$res = $callConfig->executeQuery($query);
	}
	$query = "SELECT * FROM project_task_status  WHERE project_id =".$data->project_id." AND is_active = 0";
	$statusdata = $callConfig->getAllRows($query);
	foreach($statusdata as $statusda){
		$stda[$statusda->status] = $statusda->name;
	}

	//echo "---------------------------------"; print_r($stda); exit;
	$notification_message = 'Project task ( '.$data->task_name.' ) status ( '.$stda[$post['fielddata']].' ) is updated';
	$notdataa = array('company_id'=>$get['company_id'], 'notification_message'=> $notification_message, 'transaction_number'=>$post['update_id'], 'application_table_name'=>$post['table_name'], 'type'=>'INFORMATION', 'notification_users'=>array($data->assigen_id,$get['user_id']),'option'=>'COM_VIEW_PROJECT_DASH_BOARD&RID='.$data->project_id, 'whatsup'=>$whatsup);
	$ss = $this->tosendnotifiations($notdataa);
	//$this->toinsertactivitydata($post,$get);
	$comments = "Update this card ".str_replace("_"," ",$post['field'])." to be ".$post['fielddata'];
	$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>'project_task_details','transaction_id'=>$post['update_id'],'activity_type'=>'task_details','filed_name'=>$post['field'],'new_value'=>$post['fielddata'],'comments'=>$comments ,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
	$insert = $callConfig->insertRecord('project_task_activities',$insert_array);

	if($post['table_name'] == 'project_task_details'){
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$data->assigen_id);
	$notuser1 = array($post['user_id']);
	$query = "SELECT user_id FROM project_teams WHERE company_id =".$get['company_id']." AND role_id !=2 AND project_id = ".$data->project_id."  AND is_active = 0";
		$lists = $callConfig->getAllRows($query);
		foreach($lists as $list){
			$notuser2[] = $list->user_id;
		}
	$email_content_id = 38;
	$senddata = array('session_company_id'=>$get['company_id'],'email_content_id'=>$email_content_id,'task_name'=>$data->task_name,'assigners_name'=>$user->display_name,'user_display_name'=>$user->display_name,'cc_users'=>$notuser2,'to_users'=>$notuser1,'planned_start'=>$data->planned_start,'planned_finish'=>$data->planned_finish,'current_status'=>$stda[$post['fielddata']]);
	$res = $this->emailsendmediater($senddata);
	}
	return $res;
}
function emailsendmediater($senddata){
	global $callConfig;	
	$email_send_types = array('TO'=>'to_users','CC'=>'cc_users');
	$query = "SELECT id,user_email FROM application_users";
	$emaildata = $callConfig->getAllRows($query);
	foreach($emaildata as $emaild){
		$useremail[$emaild->id] = $emaild->user_email;
	}
	foreach($email_send_types as $key=>$email_send_type){
		$revicer = array();
		$senddata['email_send_type'] =$key;
		$email_array = $this->emailtemplate($senddata);
		$file_name = 'email'.'_'.time().uniqid().'.'.'html';
		$email_array ['content'] = $file_name;
		foreach($senddata[$email_send_type] as $email_send){
			$revicer[] = $useremail[$email_send];
		}
		// echo $key." - ".$email_send_type;
		// print_r($revicer);
		// echo "</br>";
		$email_array['revicer'] = $revicer;
		$email_array['email_type'] = 1;
		$email = $email_array['mesg'];
		//print_r($email_array); exit;
		$file_path='uploads/email/'.$file_name;
		//file_put_contents($file_path, $email);
		//fclose($file_path);
		$lenn = $this->sendEmailslist($email_array);
		$ress[] = $email_array;
	}
	return $ress;
}
function sendEmailslist($data){
	global $callConfig;
	if($data['databasename'] !=""){
		$databasename = $data['databasename'].".";
	}
		//print_r($data); exit;
	$email_data = array('email_source'=>$data['sender'], 'content'=>$data['content'], 'email_type'=>$data['email_type'], 'is_active'=>0 , 'current_status'=>0,'email_content_ref_id'=>$data['email_content_ref_id'], 'created_by'=>$data['created_by'] ,'created_date'=> date('Y-m-d H:i:s'));   
	foreach ($data['revicer'] as $email) {
		$email_data['email'] = $email;
			//echo "<pre>"; print_r($email_data);
		$email_res=$callConfig->insertRecord($databasename.TBL_EMAIL_LOGS,$email_data);
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			// More headers
		$sender = $data['sender']!="" ? $data['sender']:SUPERADMIN_EMAIL_ID;
		$headers .= 'From:'.$sender . "\r\n";
			//$headers .= 'Cc: myboss@example.com' . "\r\n";
		$subject = ucwords(str_replace('_', ' ', $data['subject']));
		mail($email,$subject,$data['mesg'],$headers);
	}
		//echo "email send sucess";
	return $email_data;
}
function tosendnotifiations($notarry){
	global $callConfig;		
	$notarry['notification_datetime']= 	$notarry['notification_datetime'] !=""?$notarry['notification_datetime'] : date("Y-m-d H:i:s");
	  	//$notarry['notification_datetime']=date("Y-m-d H:i:s");
	$jsnot= json_encode($notarry);
	$NOTIfieldnames=array('company_id'=>$notarry['company_id'],'notification_message'=>$notarry['notification_message'],'notification_details'=>$jsnot,'notification_datetime'=>date("Y-m-d H:i:s"),'transaction_number'=>$notarry['id'],'application_table_name'=>$notarry['application_table_name'],'module_setup_id'=>$notarry['module_setup_id']);
	$noti_res=$callConfig->insertRecord(TBL_NOTIFICATIONS,$NOTIfieldnames);
	$notification_id = $noti_res['LASTID'];
	$whatsup = isset($notarry['whatsup']) ? $notarry['whatsup'] : 'N';
	if($notarry['type']!=""){ 
		$revalue=$notarry['type']; 
	}else{
		$revalue = 'ACTION';
	}
	foreach ($notarry['notification_users'] as  $user_id) {
		$nti_use_fieldnames=array('company_id'=>$notarry['company_id'],'user_id'=>$user_id,'notification_id'=>$notification_id,'notification_type'=>$revalue,'module_setup_id'=>$notarry['module_setup_id']);
		$res=$callConfig->insertRecord(TBL_NOTIFICATON_USER,$nti_use_fieldnames);

		$usersql = "SELECT user_mobile FROM application_users WHERE company_id = '".$notarry['company_id']."' AND id = '".$user_id."' AND is_active =0";
		$userres=$callConfig->getRow($usersql);
		if($userres->user_mobile !=""){
			$this->sendWhatsappMessage($userres->user_mobile,$notarry['notification_message'],$notarry['option'],$whatsup);
		}	
	}	
	return $res;
}
function sendWhatsappMessage($contact,$message,$option,$whatsup=""){
	if($whatsup !="" && $whatsup=='Y'){
		$url = SITEURL."/index.php?option=".$option;
		$params=array(
		'token' => 'dic6laah8z9guqbq',
		'to' => $contact,
		'body' => $message.' '.$url
		);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.ultramsg.com/instance65723/messages/chat",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => http_build_query($params),
		  CURLOPT_HTTPHEADER => array(
		    "content-type: application/x-www-form-urlencoded"
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
	}
}
function emaildatareplace($data,$val){
	foreach($data as $fieldkey=>$result){
		if(!is_array($result)){
			$inputFildsArray["{".trim($fieldkey)."}"] = trim($result);
		}
	}
	$str = "";
	foreach($inputFildsArray as $key => $value){
		$repl =  str_replace(trim($key), trim($value), trim($val));
		$val = $repl;
	}
	return $val;
}
function emailtemplate($send_data){
		global $callConfig;
		if($send_data['databasename'] !=""){
			$databasename = $send_data['databasename'].".";
		}
		$temp = "";
		$configsql = "SELECT name,contact_address,application_module,admin_email,display_logo FROM config WHERE id = 1";
		$return['config'] = $configsql;
		$configres=$callConfig->getRow($configsql);
		$send_data['company_name'] = $configres->name!=""?$configres->name:SUPERADMIN_COMPNAY_NAME;
		$send_data['contact_address'] = $configres->contact_address ;
		if($send_data['email_send_type'] == "TO"){
			$emailcontentsql = "SELECT email_subject_to as email_subject,email_header_to as email_header,email_content_to as email_content_1,button_label, email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['email_content_id'] ."'  AND is_active = 0 ";
		}else{
			$emailcontentsql = "SELECT email_subject_cc as email_subject,email_header_cc as email_header,email_content_cc as email_content_1,button_label,email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['email_content_id'] ."'  AND is_active = 0";
		}
		$return['emailcontentsql'] = $emailcontentsql;
		//print_r($send_data);
		$emailcontentres=$callConfig->getRow($emailcontentsql);
		$subject =  $this->emaildatareplace($send_data, $emailcontentres->email_subject);	
		
		$temp = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">

		<head>

		</head>
		<body yahoo bgcolor="#f6f8f1">
		<table width="70%" style="padding:20px;background-color:#f5f5f5;" align="center" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;padding:15px 0px;">
		<img class="fix" src="'.SITEURL.'/uploads/all/'.$configres->display_logo.'" border="0" alt="" style="width:150px"/>

		</td>
		</tr>
		<tr>
		<td>
		<table width="100%" style="padding:10px;background-color:#FFFFFF;"  >
		<tr>
		<td style="padding-left:32px; padding-top:20px; font-size:14px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
		'.$this->emaildatareplace($send_data, $emailcontentres->email_header).'
		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
		'.$this->emaildatareplace($send_data, $emailcontentres->email_sub_header).'
		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
		<div>'.$this->emaildatareplace($send_data, $emailcontentres->email_content_1).' </div>

		</td>
		</tr>
		<tr>
		<td>
		&nbsp;
		</td>
		</tr>
		<tr>
		<td>
		&nbsp;
		</td>
		</tr>
		
		<tr>
		<td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
		'.$this->emaildatareplace($send_data, $emailcontentres->email_content_2).'

		</td>
		</tr>
		<td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
		'.$this->emaildatareplace($send_data, $emailcontentres->email_fotter).'

		</td>
		</tr>
		</table>
		</body>
		</html>
		';
		$return['subject'] = $subject;
		$return['mesg'] = $temp;
		$return['sender'] =  $configres->admin_email;
		return $return;
	}
	

function togetprojecttasktimelog($get){
	global $callConfig;
	//print_r($get);
	$query = "SELECT id,task_start,task_id FROM project_task_time_log WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND task_id !=0 AND task_end is NULL AND is_active = 0 ORDER BY id DESC LIMIT 0,1"; 
	$log = $callConfig->getRow($query);
	//print_r($log);
	if($log->id!=""){
		$crow=$callConfig->getRow("SELECT work_time FROM project_task_details WHERE  id = ".$log->task_id); 
		$return['id'] = $log->id;
		$return['task_id'] =$log->task_id;
		$return['task_start'] =$log->task_start;
		$return['work_time'] =$crow->work_time;
	}
	return $return;
}
function  getStatusProgressList($data){
	global $callConfig;
	$statussql = "SELECT id,name,status,action_label FROM status_progress WHERE table_name = '".$data['table']."' ORDER BY `id` ASC";
	$statusress = $callConfig->getAllRows($statussql);
	if(!isset($statusress[0]->id)){
		$statusres[] = array('status'=>'0','name'=>'Active');
	}else{
		$statusres = $statusress;
	}
	return $statusres;
}



function getitemqualitychecknewdetails($get){
	global $callConfig;
	$query = "SELECT * FROM  items WHERE id =".$get['item_id'];
	$return['item']  = $callConfig->getRow($query);
	$query = "SELECT * FROM  quality_parameters_mapping WHERE item_id =".$get['item_id']." AND quality_type='".$get['quality_type']."' AND is_active = 0";
	$return['quality_parameters_mapping']  = $callConfig->getRow($query);
	$return['quality_parameters_mapping_qury']  = $query;
	$query = "SELECT id,group_name FROM  quality_parameters_groups";
	$return['quality_parameters_groups']  = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  quality_parameters";
	$item_attributes = $callConfig->getAllRows($query);
	foreach($item_attributes as $item_attribute){
		$attributes[$item_attribute->quality_parameter_group_id][] = $item_attribute;
	}
	$return['quality_parameters'] = $attributes;
	$query = "SELECT * FROM  item_serial_nos WHERE company_id =".$get['company_id']." AND transaction_table='".$get['table']."' AND transaction_id=".$get['trnid']." AND  item_id=".$get['item_id']." AND is_active=0";
	$return['item_serial_nos_query'] = $query; 
	$return['item_serial_nos'] = $callConfig->getAllRows($query);

	$query = "SELECT * FROM  item_lot_nos WHERE company_id =".$get['company_id']." AND transaction_table='".$get['table']."' AND transaction_id=".$get['trnid']." AND  item_id=".$get['item_id']." AND is_active=0";
	$return['item_lot_nos'] = $callConfig->getAllRows($query);

	$query = "SELECT * FROM  item_quality_check WHERE company_id =".$get['company_id']." AND application_table_name='".$get['table']."' AND transaction_id=".$get['trnid']." AND  item_id=".$get['item_id']." AND is_active=0";
	$return['item_quality_check_query'] = $query;
	$return['item_quality_check'] = $callConfig->getAllRows($query);	
	$query = "SELECT * FROM  ".$get['table']." WHERE id =".$get['trnid'];
	$return['transaction_data']  = $callConfig->getRow($query);
	$query = "SELECT * FROM  item_quality_check WHERE company_id =".$get['company_id']." AND application_table_name='".$get['table']."' AND transaction_id=".$get['trnid']." AND  item_id=".$get['item_id']." AND is_active=0";
	$qc_head = $callConfig->getRow($query);
	$return['qc_head'] = $qc_head;


	return 	$return;
}
function toinsertthedataonitemquality($post, $get){
	global $callConfig;
	$query = "SELECT * FROM item_quality_check WHERE company_id = ".$get['company_id']." AND application_table_name='".$post['table']."' AND transaction_id =".$post['tranid']." AND item_id=".$post['item_id']." AND is_active = 0";
	$listdata = $callConfig->getAllRows($query);
	foreach($listdata as $list){
		$data[$list->item_serial_lot_id][$list->item_attribute_id]= $list->specification_result;
		$dataid[$list->item_serial_lot_id][$list->item_attribute_id]= $list->id;
	}
	foreach($post['item_attribute_res'] as $key=>$item_attribute_res){
		foreach($item_attribute_res as $atkey=>$attres){
			$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>$post['table'],'transaction_id'=>$post['tranid'],'item_id'=>$post['item_id'],'item_serial_lot_id'=>$key,'item_attribute_id'=>$atkey,'specification_result'=>$attres,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'remarks'=>$post['item_attribute_remarks'][$key][$atkey],'quality_check'=>$post['item_attribute_pass'][$key][$atkey]);
			if(isset($data[$key][$atkey])){
				if($data[$key][$atkey]!=$attres){
					$upquery = "UPDATE item_quality_check SET is_active=1 WHERE id=".$dataid[$key][$atkey];
					$row = $callConfig->executeQuery($upquery);
					$insert[] = $callConfig->insertRecord('item_quality_check',$insert_array);
				}
			}else{
				$insert[] = $callConfig->insertRecord('item_quality_check',$insert_array);
			}

		}
	}

	$query = "SELECT * FROM item_head_quality_check WHERE company_id = ".$get['company_id']." AND application_table_name='".$post['table']."' AND transaction_id =".$post['tranid']." AND item_id=".$post['item_id']." AND is_active = 0";
	$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>$post['table'],'transaction_id'=>$post['tranid'],'item_id'=>$post['item_id'],'quality_check'=>'0','is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
	$chkc = $callConfig->getRow($query);
	if($chkc->id!=""){
		$upquery = "UPDATE item_head_quality_check SET is_active=1 WHERE id=".$chkc->id;
		$row = $callConfig->executeQuery($upquery);
	}
	$headinsert = $callConfig->insertRecord('item_head_quality_check',$insert_array);
	
	return $insert;
}
function togetqualitychecmappingindetails_old_22_07_2026($get){
	global $callConfig;
	$query = "SELECT id,group_name FROM  quality_parameters_groups";
	$return['groups']  = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  quality_parameters";
	$parameters = $callConfig->getAllRows($query);
	foreach($parameters as $parameter){
		$para[$parameter->quality_parameter_group_id][] = $parameter;
	}
	$return['parameter']  = $para;
	$query = "SELECT id,item_name FROM  items";
	$return['items']  = $callConfig->getAllRows($query);
	return $return;
}
function togetqualitychecmappingindetails($get){
	global $callConfig;
	$query = "SELECT id,group_name FROM  quality_parameters_groups WHERE company_id =".$get['company_id']." AND is_active =0";
	$return['groups']  = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  quality_parameters WHERE company_id =".$get['company_id']." AND is_active =0";
	$parameters = $callConfig->getAllRows($query);
	foreach($parameters as $parameter){
		$para[$parameter->quality_parameter_group_id][] = $parameter;
	}
	$return['parameter']  = $para;
	// $query = "SELECT id,item_name,qc_check,category_id FROM  items WHERE company_id =".$get['company_id']." AND is_active =0";
	// $return['items']  = $callConfig->getAllRows($query);

	$query = "SELECT id,CONCAT(category_description,' - ',category_short_name) AS category_name FROM  item_categories WHERE company_id =".$get['company_id']." AND is_active =0";
	$return['item_categories']  = $callConfig->getAllRows($query);

	return $return;
}
function togetqualitychecmappinginwithitemmadetails($get){
	global $callConfig;
	$query = "SELECT id, item_name FROM items WHERE id IN (SELECT DISTINCT(item_id) FROM `item_quality_parameters_mapping` WHERE company_id =".$get['company_id']." AND is_active =0)";
	$return['items']  = $callConfig->getAllRows($query);
	if($get['item_id']!=""){
		$query = "SELECT * FROM  item_quality_parameters_mapping WHERE company_id = ".$get['company_id']." AND item_id = ".$get['item_id'];
		$data = $callConfig->getRow($query);
		$gp = json_decode($data->specifications,1);
		$query = "SELECT id,group_name FROM  quality_parameters_groups WHERE id IN (".implode(",",$gp['group']).")";
		$return['groups']  = $callConfig->getAllRows($query);
		foreach($gp['parameters'] as $parameters){
			$qp[] = $parameters['id'];
		}
		$query = "SELECT * FROM  quality_parameters WHERE id IN (".implode(",",$qp).")";
		$parameters = $callConfig->getAllRows($query);
		foreach($parameters as $parameter){
			$para[$parameter->quality_parameter_group_id][] = $parameter;
		}
		$return['parameter']  = $para;
	}
	return $return;
}
function togetdatawithseriallotnumber($get){
	global $callConfig;
	$query = "SELECT * FROM  item_serial_lot_histories WHERE company_id =".$get['company_id']." AND transaction_table='".$get['table']."' AND transaction_id=".$get['tranid']." AND  item_id=".$get['item_id']." AND is_active=0";
	$return['item_serial_nos_query'] = $query; 
	$return['item_serial_nos'] = $callConfig->getAllRows($query);
	$query = "SELECT * FROM  items WHERE company_id =".$get['company_id']." AND id='".$get['item_id'];
	$return['item_query'] = $query; 
	$return['items'] = $callConfig->getRow($query);
	return $return;
}
function tocheckandisertthedatafortablequalitycheck($get){
	global $callConfig;
	//print_r($get);
	$query = "SELECT * FROM application_table_quality_check WHERE company_id = ".$get['company_id']." AND application_table_name = '".$get['table']."' AND transaction_id =".$get['tranid']." AND is_active = 0";
	$tbda = $callConfig->getRow($query);
	//print_r($tbda);
	if($tbda->id==""){
		$insert_array = array('company_id'=>$get['company_id'],'application_table_name'=>$get['table'],'transaction_id'=>$get['tranid'],'quality_check'=>'0','is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('application_table_quality_check',$insert_array);
		//print_r($insert);
		$query = "SELECT * FROM application_table_quality_check WHERE company_id = ".$get['company_id']." AND application_table_name = '".$get['table']."' AND transaction_id =".$get['tranid']." AND is_active = 0";
		$tbda = $callConfig->getRow($query);
	}
	return $tbda ;
}
function toudatedepartmentandcoscenter($get){
	global $callConfig;

	$query = 'UPDATE application_user_module_roles amr , application_users au SET au.role_id = '.".'amr.role_short_code'.".' WHERE amr.user_id = au.id';
	//$query = 'UPDATE application_user_module_roles amr , application_users au SET au.role_id = '.json_encode(amr.role_short_code).' WHERE amr.user_id = au.id';
	echo $query;
	//echo $query;
	echo "</br>";

	/*$query = "SELECT id,gender FROM employees WHERE user_id !=101";
	$empdata = $callConfig->getAllRows($query);
	$loc = 1; $locv=1;
	foreach($empdata  as $emp){
		if($loc==7){ $loc=1; $ccc =1;}
		$upquser = "UPDATE  employees SET location_id= ".$loc." WHERE id =".$emp->id;
		$data[] = $callConfig->executeQuery($upquser);
		echo $upquser;
		echo "</br>";
		$loc = $loc+1;
	}*/
	/*$fm=100; $ma = 1;
	foreach($empdata  as $emp){
		if($empdata->gender == 'F'){
			$img = $fm.".jpg";
			$fm= $fm+1;
		}else{
			$img = $ma.".jpg";
			$ma= $ma+1;
		}
		$upquser = "UPDATE  employees SET image= '".$img."' WHERE id =".$emp->id;
		$data[] = $callConfig->executeQuery($upquser);
		echo $upquser;
		echo "</br>";
	}*/
	/*$dep=1; $cos=1; $cdep=1; $ccc=1; $cemp =1;
	//print_r($empdata);
	foreach($empdata  as $emp){
		if($cdep==7){ $dep = $dep+1; $cdep=1; }
		if($ccc==4){ $cos=$cos+1; $ccc =1;}
		$upquser = "UPDATE  employees SET department_id= ".$dep.", 	cost_center_id=".$cos." WHERE id =".$emp->id;
		$data[] = $callConfig->executeQuery($upquser);
		echo $upquser;
		echo "</br>";
		$cdep = $cdep+1;$ccc=$ccc+1;
	}*/
	print_r($data);
}
function togetdyanamicselectdatatablename($get){
	global $callConfig;
	//print_r($get);
	$list = json_decode($get['data'],1);
	//print_r($list);
	$query = "SELECT application_table_name, field_name, content_details FROM display_attribute_types WHERE company_id =".$get['company_id']." AND  application_table_name ='".$get['table_name']."' AND field_name IN ('" . implode ( "', '", $list ) . "')";
	$rdata = $callConfig->getAllRows($query);
	foreach($rdata as $mdata){
		$json_data = json_decode(urldecode($mdata->content_details),1);
		$fielddata[$mdata->field_name] = $json_data['table_name'];
	}
	return $fielddata;
}
function togenratesalarytemplates(){
	global $callConfig;
	$empqual = "SELECT * FROM employees WHERE id>120 AND id<311";
	$empres= $callConfig->getAllRows($empqual);
	foreach($empres as $emp){
		$query_sal = "INSERT INTO `salary_template`( `company_id`, `employee_id`, `emp_code` ,`description`, `yearly_gross_salary`, `ctc_monthly`, `ctc_yearly`, `note`, `is_active`, `current_status`) SELECT  `company_id`, '".$emp->id."', '".$emp->emp_code."' , `description`, `yearly_gross_salary`, `ctc_monthly`, `ctc_yearly`, `note`, `is_active`, `current_status` FROM `salary_template` WHERE id=2";
		$insert_task = $callConfig->executeQuery($query_sal);
		$salay_id = $insert_task['LASTID'];
		$resins['main'][] = $query_sal;
		$resins['main_id'][] = $salay_id;
		$quer_com ="INSERT INTO `salary_template_components`( `company_id`, `salary_template_id`, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status`) select `company_id`, '".$salay_id."' as salary_template_id, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status` FROM salary_template_components where salary_template_id = 2";
		$insert_task = $callConfig->executeQuery($quer_com);
		$resins['sub_qu'][] = $quer_com;
		$resins['sub_id'][] = $insert_task;

	}
	return $resins;
}
function togetreportvillvendors($get){
	global $callConfig;
	//print_r($get);
	$query = "SELECT * FROM supplier_bills WHERE company_id = ".$get['company_id']." AND ( pending_amount IS NULL OR  pending_amount < total_amount ) AND current_status = '".ST_APPROVED."' AND is_active = 0";
	$supplier_bills  = $callConfig->getAllRows($query);
	foreach($supplier_bills as $ven){
		$return['bills'][] = $ven;
	}
	$query1 = "SELECT * FROM sales_invoices WHERE company_id = ".$get['company_id']." AND ( pending_amount IS NULL OR  pending_amount < total_amount ) AND current_status = '".ST_APPROVED."' AND is_active = 0";
	$sales_invoices  = $callConfig->getAllRows($query1);
	foreach($sales_invoices as $ven1){
		$return['bills'][] = $ven1;
	}
	$query = "SELECT id, display_name FROM vendors WHERE company_id = ".$get['company_id'];
	$vendors  = $callConfig->getAllRows($query);
	foreach($vendors as $ven){
		$vendor[$ven->id] = $ven;
	}
	$return['vendors'] = $vendor;
	return $return;
}
function togetreportvillvendors_old($get){
	global $callConfig;
	//print_r($get);
	$query = "SELECT * FROM supplier_bills WHERE company_id = ".$get['company_id']." AND ( pending_amount IS NULL OR  pending_amount < total_amount ) AND is_active = 0";
	$return['bills'] = $callConfig->getAllRows($query);
	$query = "SELECT id, display_name FROM vendors WHERE company_id = ".$get['company_id'];
	$vendors  = $callConfig->getAllRows($query);
	foreach($vendors as $ven){
		$vendor[$ven->id] = $ven;
	}
	$return['vendors'] = $vendor;
	return $return;
}
function togetreportvillbaseidvendors($get){
	global $callConfig;
	//print_r($get);
	$query = "SELECT * FROM supplier_bills WHERE company_id = ".$get['company_id']." AND id IN (".str_replace("-",",",$get['val']).")";
	$return['bills'] = $callConfig->getAllRows($query);
	$query = "SELECT id, display_name FROM vendors WHERE company_id = ".$get['company_id'];
	$vendors  = $callConfig->getAllRows($query);
	foreach($vendors as $ven){
		$vendor[$ven->id] = $ven;
	}
	$return['vendors'] = $vendor;
	return $return;
}
function updateprojectdependencydetail($post, $get){
	global $callConfig;

	$query = "UPDATE project_task_dependencies set is_active = 1 WHERE company_id = ".$get['company_id']." AND project_id = ".$post['project_id']."  AND project_task_id =".$post['task_id']." AND dependence_task_id NOT IN (".$post['dependency_ids'].") AND is_active = 0";
	$dataa = $callConfig->executeQuery($query);
	$return['update'] = $query;
	$query = "SELECT dependence_task_id FROM project_task_dependencies WHERE company_id = ".$get['company_id']." AND project_id = ".$post['project_id']."  AND project_task_id =".$post['task_id']." AND is_active = 0";
	$datas= $callConfig->getAllRows($query);
	$return['select'] = $query;
	$depids = array();
	if(count((array)$depids)>0){
		foreach($datas as $data){
			$depids[] = $data->dependence_task_id;
		}
	}
	$dependency_ids  = explode("," , $post['dependency_ids']);
	foreach($dependency_ids as $dependency_id){
		if(!in_array($dependency_id, $depids)){
			$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'], 'project_task_id'=>$post['task_id'], 'dependence_task_id'=>$dependency_id,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
			$insert[] = $callConfig->insertRecord('project_task_dependencies',$insert_array);
		}
	}
	$return['insert'] = $insert;
	return $return;
}
function addupdaterolememberdetail($post, $get){
	global $callConfig;

	$query = "UPDATE project_task_teams set is_active = 1,updated_by = ".$get['user_id']." WHERE company_id = ".$get['company_id']." AND project_id = ".$post['project_id']."  AND project_task_id =".$post['task_id']." AND project_role_id = ".$post['role_id']." AND user_id NOT IN (".$post['member_ids'].") AND is_active = 0";
	$dataa = $callConfig->executeQuery($query);
	$return['update'] = $query;
	$query = "SELECT id,user_id FROM project_task_teams WHERE company_id = ".$get['company_id']." AND project_id = ".$post['project_id']."  AND project_task_id =".$post['task_id']." AND is_active = 0";
	$datas= $callConfig->getAllRows($query);
	$return['select'] = $query;
	$depids = array();
	if(count((array)$datas)>0){
		foreach($datas as $data){
			$depids[] = $data->user_id;
		}
	}
	$dependency_ids  = explode("," , $post['member_ids']);
	foreach($dependency_ids as $dependency_id){
		if(!in_array($dependency_id, $depids)){
			$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'], 'project_task_id'=>$post['task_id'], 'project_role_id'=>$post['role_id'],'user_id'=>$dependency_id,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
			$insert[] = $callConfig->insertRecord('project_task_teams',$insert_array);
		}
	}
	$return['insert'] = $insert;
	return $return;
}

function addupdateprojectphases($post, $get){
	global $callConfig;
	$insert_array = array('company_id'=>$get['company_id'],'project_id'=>$post['project_id'], 'project_phase_name'=>$post['project_phase_name'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
	$insert = $callConfig->insertRecord('project_phases',$insert_array);
	return $insert;
}
function togetprojectdnewashboarddetails($get){
	global $callConfig;
	$query="SELECT * FROM projects WHERE  company_id = ".$get['company_id']." AND id =".$get['project_id'];
	$return['project'] = $callConfig->getRow($query);
	$project = $return['project'];
	$query = "SELECT SUM(IF(current_status!=199,1,0)) tobe, SUM(IF(current_status!=199,1,0)) yet, SUM(IF((current_status!=199 && current_status!=0) ,1,0)) ing, COUNT(id) total_project, SUM(IF(planned_start<=now(),1,0)) as schdue, SUM(IF(assigen_id!='',1,0)) as schd, SUM(IF(( planned_finish < now() && current_status !=199), 1, 0)) as overdue, SUM(IF(current_status=199,1,0)) complete FROM project_task_details WHERE company_id = ".$get['company_id']." AND project_id =".$get['project_id']." AND is_active = 0";
	$return['graphone'] = $callConfig->getRow($query);
	$query = " SELECT * FROM  project_phases WHERE company_id = ".$get['company_id']." AND project_id = ".$get['project_id']." AND is_active=0";
	$phases = $callConfig->getAllRows($query);
	
	$query = "SELECT project_phase_id, COUNT(id) as total_project, SUM(IF(current_status=199,1,0)) as complete  FROM project_task_details WHERE company_id = ".$get['company_id']." AND project_id =".$get['project_id']." AND is_active = 0 GROUP BY project_phase_id";
	$detailphases = $callConfig->getAllRows($query);
	foreach($detailphases as $pdetaol){
		if($pdetaol->total_project!=0){
			$cphas[$pdetaol->project_phase_id] = round(($pdetaol->complete / $pdetaol->total_project)*100);
		}else{
			$cphas[$pdetaol->project_phase_id] = 100;
		}
		
	}
	foreach($phases as $phase){
		$val =(isset($cphas[$phase->id]) ? $cphas[$phase->id] : 100) ;
		$listphase[$phase->id] = $phase->project_phase_name.", ".$val."%:".$val.":".(100-$val);
	}
	$return['graphtwo'] = $listphase;
	$query = "SELECT pt.assigen_id, COUNT(pt.id) as total_project, SUM(IF(pt.current_status=199,1,0)) as complete, au.display_name  FROM project_task_details pt, application_users  au  WHERE  pt.company_id = au.company_id AND pt.company_id = ".$get['company_id']." AND pt.assigen_id = au.id AND pt.project_id =".$get['project_id']." AND pt.assigen_id IS NOT NULL AND  pt.is_active = 0 GROUP BY pt.assigen_id";
	$return['graphsix'] = $callConfig->getAllRows($query);

	$query = "SELECT id, user_id, rate_hours, budget_rate_hours FROM project_teams WHERE company_id = ".$get['company_id']." AND project_id= ".$get['project_id']." AND is_active = 0";
	$prteams =  $callConfig->getAllRows($query);
	foreach($prteams as $prteam){
		$pridteam[$prteam->user_id] = (array)$prteam;
	}
	//echo "<pre>"; print_r($pridteam); print_r($project); exit;
	$query = "SELECT id, assigen_id, planned_start, planned_finish, actual_start, actual_finish FROM project_task_details WHERE company_id = ".$get['company_id']." AND project_id= ".$get['project_id']." AND is_active = 0";
	$ptime =  $callConfig->getAllRows($query);
	 //print_r($project); exit;
	
	foreach($ptime as $atimr){
		if($atimr->planned_finish !="" && $atimr->planned_finish!=""){
			//echo "<pre>"; print_r($atimr);
			$plandaystr = strtotime($atimr->planned_finish) - strtotime($atimr->planned_start);
			$planday = floor($plandaystr / (60 * 60 * 24));
			$actdaystr = strtotime($atimr->actual_finish) - strtotime($atimr->actual_start);
			$actday = floor($actdaystr / (60 * 60 * 24));
			//echo "plandays:".$planday.",actday:".$actday.", value".$pridteam[$atimr->assigen_id]['rate_hours']; exit;
			$totaltime = $totaltime+ $actday;
			$totalactual = $totalactual+(($actday*8)*$pridteam[$atimr->assigen_id]['rate_hours']);
			$totalplaned = $totalplaned+(($planday*8)*$pridteam[$atimr->assigen_id]['rate_hours']);
		}
	}
	$return['graphfive'] = array( 'actual'=>$totalactual, 'planed'=>$totalplaned,'budget'=>$project->project_budget);
	return $return;
}
function tochangeprojectowner($get){
	global $callConfig;
	$upquery = "UPDATE project_teams SET is_active = 1 WHERE company_id =".$get['company_id']." AND user_id =".$get['old_assinger_id']." AND project_id = ".$get['rid']." AND is_active = 0";
	$callConfig->executeQuery($upquery);
	$insert_array = array('company_id'=>$get['company_id'], 'project_id'=>$get['rid'], 'role_id'=>1, 'user_id'=>$get['assinger_id'],  'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
 	$res= $callConfig->insertRecord('project_teams',$insert_array);
	return $res;

}
function togetprojectmahinematerial($get){
	global $callConfig;
	/*$query = "SELECT * FROM project_task_details WHERE company_id = ".$get['company_id']." AND  id = ".$get['task_id']." AND is_active =0";
	$return['task'] =  $callConfig->getRow($query);*/

	$query = " SELECT  genc_detail_code, genc_detail_name  FROM  general_code_details WHERE  company_id = ".$get['company_id']." AND genc_code ='MACHINETYPE' AND is_active ='0'  ORDER BY genc_detail_name ASC";
	$return['mtypes'] =  $callConfig->getAllRows($query);

	$query = " SELECT  id,  sku, item_name, item_type, item_description, purchase_uom, category_id, sale_price as cost  FROM  items WHERE  company_id = ".$get['company_id']." AND is_active ='0'  ORDER BY item_name ASC";
	$return['items'] =  $callConfig->getAllRows($query);

	$query = " SELECT  `id`, `machine_name`, `description`, `machine_type`, `asset_id`, `cost`  FROM  machines WHERE  company_id = ".$get['company_id']." AND is_active ='0'  ORDER BY machine_name ASC";
	$return['machines'] =  $callConfig->getAllRows($query);

	if($get['id']!=""){
			$query = "SELECT * FROM project_machine_material WHERE company_id = ".$get['company_id']." AND  id = ".$get['id'];
			$return['rowdata'] =  $callConfig->getRow($query);
	}

	return $return;

}
function togetprojecttaskmahinematerial($get){
	global $callConfig;
	$query = "SELECT * FROM project_task_details WHERE company_id = ".$get['company_id']." AND  id = ".$get['task_id'];
	$return['task'] =  $callConfig->getRow($query);	
	$query = "SELECT * FROM project_machine_material WHERE company_id = ".$get['company_id']." AND  project_id = ".$get['project_id'];
	$return['project_machine_material'] =  $callConfig->getAllRows($query);	
	if($get['id']!=""){
			$query = "SELECT * FROM project_task_machine_material WHERE company_id = ".$get['company_id']." AND  id = ".$get['id'];
			$return['rowdata'] =  $callConfig->getRow($query);
	}
	return $return;
}
function togetcourier($get){
	global $callConfig;
	$query = "SELECT id,display_name, emp_code FROM employees";
	$return['employees'] =  $callConfig->getAllRows($query);
	$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'DELIVERTYPE' AND is_active = 0";
	$return['deliver_type'] = $callConfig->getAllRows($query);
	$query = "SELECT id,name, status FROM status_progress WHERE company_id =".$get['company_id']." AND table_name = 'couriers' AND  status > 10 AND is_active = 0";
	$status = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$sts[$statu->status] = $statu->name;
	}
	$return['status'] = $sts;
	if($get['id']!=""){
		$query = "SELECT * FROM couriers WHERE company_id = ".$get['company_id']." AND  id = ".$get['id'];
		$return['rowdata'] =  $callConfig->getRow($query);
	}
	return $return;
}
function togetofficemanagement($get){
	global $callConfig;
	//print_r($get); exit;
	$return['get']  = $get;
	//return $return;
	$query = "SELECT id, name FROM stationery_categories WHERE company_id =".$get['company_id']." AND is_active =0";
	$return['stationery_categories'] =  $callConfig->getAllRows($query);
	$loc_query = "SELECT id, location_name FROM locations WHERE company_id =".$get['company_id']." AND is_active =0";
	$return['loc_data'] =  $callConfig->getAllRows($loc_query);
	$whr = "";
	if($get['cat_id']!=""){
		$whr.=" AND category_id=".$get['cat_id'];
	}
	if($get['search']!=""){
		$whr.=" AND item_name LIKE '%".$get['search']."%' ";
	}
	$query = "SELECT id, item_name, description, image, available_qty FROM stationery_items WHERE company_id =".$get['company_id']." AND location_id = ".$get['location_id']." AND is_active =0".$whr;
	$return['stationery_items'] =  $callConfig->getAllRows($query);
	
	$query = "SELECT * FROM stationery_item_ledger WHERE company_id = ".$get['company_id']." AND item_id (SELECT id, item_name, description, image, available_qty FROM stationery_items WHERE company_id =".$get['company_id']." AND location_id = ".$get['location_id']." AND is_active =0)  AND is_active = 0 ";
	$stationery_ledgers =  $callConfig->getAllRows($query);
	foreach($stationery_ledgers as $stationery_ledger){
		$stationery_item_ledger[$stationery_ledger->item_id][] = $stationery_ledger;
	}
	$return['stationery_item_ledger'] =  $stationery_item_ledger;

	$query = "SELECT sum(qty) as count_cart FROM stationery_cart WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND  request_no IS NULL AND  is_active = 0";
	$cccount = $callConfig->getRow($query);
	$return['cart_count'] =  $cccount->count_cart;
	if($cccount->count_cart!=0){
		$query = "SELECT id, qty,item_id FROM stationery_cart WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']." AND  request_no IS NULL AND  is_active = 0";
		$alldatas =  $callConfig->getAllRows($query);
		foreach($alldatas as $alldata){
			$sitem[$alldata->item_id] = $alldata->qty; 
		}
		$return['sitem'] =  $sitem;
	}
	$whr = " AND user_id = ".$get['user_id'];
	if( isset($get['type']) && $get['type']==SECURITYGUARD_ROLE){
		$whr = "";
	}
	 $query = "SELECT SUM(if(courier_type=1,1,0)) as send, SUM(if(courier_type=2,1,0)) as receive FROM couriers WHERE company_id = ".$get['company_id']." AND location_id = ".$get['location_id']." AND is_active = 0 ".$whr;
	$return['all'] =  $callConfig->getRow($query);
	$query = "SELECT SUM(if(courier_type=1,1,0)) as send, SUM(if(courier_type=2,1,0)) as receive FROM couriers WHERE company_id = ".$get['company_id']." AND location_id = ".$get['location_id']." AND is_active = 0 AND courier_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ".$whr;
	$return['seven'] =  $callConfig->getRow($query);
	$hwr = "";
	if($get['admin_type']!=1){
		$hwr = " AND user_id = ".$get['user_id'];
	}
	$query = "SELECT * FROM couriers WHERE company_id = ".$get['company_id']."  AND courier_type = 2 ".$hwr."  AND location_id = ".$get['location_id']." AND is_active = 0 ORDER BY id LIMIT 0,5 ";
	$return['sidein'] =  $callConfig->getAllRows($query);

	$query = "SELECT * FROM couriers WHERE company_id = ".$get['company_id']." AND courier_type = 1  AND location_id = ".$get['location_id']." AND ".$hwr." is_active = 0 ORDER BY id LIMIT 0,5 ";
	$return['sideout'] =  $callConfig->getAllRows($query);

	//echo "<pre>"; print_r($return); exit;
	$query = "SELECT * FROM couriers WHERE company_id = ".$get['company_id']."  AND location_id = ".$get['location_id']." AND is_active = 0 ".$whr." ORDER BY id DESC";
	$return['down'] =  $callConfig->getAllRows($query);
	$query = "SELECT id,display_name,email_id, image, emp_code  FROM employees WHERE company_id =  ".$get['company_id']."   AND is_active = 0";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;

	$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'DELIVERTYPE' AND is_active = 0";
	$deliver_type = $callConfig->getAllRows($query);

	foreach($deliver_type as $deliver_tp){
		$deliver_types[$deliver_tp->genc_detail_code] = $deliver_tp->genc_detail_name;
	}
	$return['deliver_types'] = $deliver_types;

	$query = "SELECT id,name, status,table_name FROM status_progress WHERE company_id =".$get['company_id']." AND table_name IN( 'couriers','employee_transport_requests', 'employee_food_requests', 'stationery_requests')  AND is_active = 0";
	$status = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$sts[$statu->table_name][$statu->status] = $statu->name;
	}
	$return['status'] = $sts;


	if( isset($get['admin_type'])&& $get['admin_type']==1){
		$query = "SELECT * FROM stationery_requests WHERE company_id =".$get['company_id']." AND  location_id = ".$get['location_id']." AND is_active = 0 ORDER BY id DESC ";
		$return['all_stationery_requests']= $callConfig->getAllRows($query);
		$return['all_stationery_requests_query']= $query;
	}

	$query = "SELECT * FROM stationery_requests WHERE company_id =".$get['company_id']." AND user_id = ".$get['user_id']." AND is_active = 0 ORDER BY id DESC ";
	$return['my_stationery_requests']= $callConfig->getAllRows($query);
	$return['my_stationery_requests_query']= $query;	


	$query = "SELECT * FROM company_transports WHERE company_id =".$get['company_id']." AND is_active = 0";
	$buses = $callConfig->getAllRows($query);
	foreach($buses as $buse){
		$bus[$buse->id] = $buse;
	}
	$return['bus'] = $bus;

	$query = "SELECT * FROM transport_routes WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['routes']= $callConfig->getAllRows($query);
	
	$query = "SELECT * FROM transport_schedule WHERE company_id =".$get['company_id']." AND is_active = 0";
	$transport_schedules = $callConfig->getAllRows($query);
	//echo "<pre>"; print_r($return); exit;
	foreach((array)$transport_schedules as $transport_schedule){
		$tschedule[$transport_schedule->route_id][] = $transport_schedule;
		$listschedlue[] = $transport_schedule->stop_name;
	}
	$return['transport_schedule'] = $tschedule;
	$return['listschedlue'] = array_unique((array)$listschedlue);
	//echo "<pre>"; print_r($return); exit;

	$query = "SELECT * FROM employee_transport_requests WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id']." AND is_active = 0" ;
	$return['user_transport_schedule'] = $callConfig->getAllRows($query);
	
	$query = "SELECT * FROM food_options WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['food_options']= $callConfig->getAllRows($query);

	$query = "SELECT * FROM employee_food_requests WHERE company_id =".$get['company_id']." AND user_id =".$get['user_id'] ;
	$employee_food_requests = $callConfig->getAllRows($query);


	$return['employee_food_requests']= $employee_food_requests;
	$return['employee_food_requests_query']= $query;
	foreach($employee_food_requests as $employee_food){
		if($employee_food->is_active == 0 && $employee_food->current_status == 199){
			$food[]= $employee_food->food_options;
		}		
	}
	$return['food']= $food;
	if( isset($get['admin_type'])&& $get['admin_type']==1){
		$query = "SELECT * FROM employee_transport_requests WHERE company_id =".$get['company_id']." AND is_active = 0";
		$return['all_transport_schedule'] = $callConfig->getAllRows($query);
		$query = "SELECT * FROM employee_food_requests WHERE company_id =".$get['company_id']." AND is_active = 0" ;
		$return['all_employee_food_requests']= $callConfig->getAllRows($query);
	}	

	$query = "SELECT * FROM meeting_room_bookings WHERE company_id =".$get['company_id']." AND is_active = 0";
	$return['all_metting'] = $callConfig->getAllRows($query);
		
	$query = "SELECT id,display_name, user_photo FROM application_users WHERE company_id =".$get['company_id']." AND is_active = 0" ;
	$users = $callConfig->getAllRows($query);
	foreach($users as $user){
		$aluser[$user->id]['display_name'] = $user->display_name;
		$aluser[$user->id]['image'] = $user->user_photo;
	}
	$return['all_users']= $aluser;
	$query ="SELECT id,cabin_name FROM building_floor_spaces WHERE company_id = {$get['company_id']} AND space_type_id IN (SELECT id FROM space_types WHERE company_id =  {$get['company_id']} AND short_code = 'METTING_ROOM' AND is_active =0)";
    $data = $callConfig->getAllRows($query);
    $cabin_res = array();
    foreach ($data as $row) {
        $cabin_res[$row->id] = $row->cabin_name;
    }
    $return['cabin_res'] = $cabin_res;

    $query = "SELECT id, building_name FROM buildings WHERE company_id =".$get['company_id']." AND location_id = ".$get['location_id']." AND  is_active = 0";
    $return['buildings']  = $callConfig->getAllRows($query);
    if($get['admin_type']!=1){    	
    	$query = "SELECT * FROM role_permissions WHERE company_id =".$get['company_id']." AND page_name IN ('COM_LIST_COURIERS', 'COM_LIST_STATIONERY', 'COM_INSERT_STATIONERY', 'COM_LIST_SEATING_POOLS' ) AND role_short_code ='working_role_short_code' AND is_active =0";
    	$wdata = $callConfig->getAllRows($query);
    	foreach((array)$wdata as $wa){
    		$worksub[]=$wa->page_name;
    	}
    }
    $return['worksub']  = $worksub;
	return $return;
}
function addstationerycartqty($get){
	global $callConfig;
	$query = "SELECT id FROM stationery_cart WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']."  AND request_no IS NULL AND item_id = ".$get['item_id']." AND is_active = 0";
	$data =  $callConfig->getRow($query);
	if($data->id!=""){
		$uquery = "UPDATE stationery_cart SET qty = ".$get['qty']." WHERE company_id = ".$get['company_id']." AND id = ".$data->id;
		$edata = $callConfig->executeQuery($uquery);
	}else{
		$insert_array = array('company_id'=>$get['company_id'],'item_id'=>$get['item_id'],'qty'=>$get['qty'],'user_id'=>$get['user_id'],'is_active'=>'0','current_status'=>0,'created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('stationery_cart',$insert_array);
	}
	$query = "SELECT sum(qty) as count_cart FROM stationery_cart WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']."  AND request_no IS NULL AND  is_active = 0";
	$cccount = $callConfig->getRow($query);
	$return['count_cart'] =  $cccount->count_cart;
	return $return;
}
function togetuserstationerycart($get){
	global $callConfig;
	$query = "SELECT * FROM stationery_cart WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']."  AND request_no IS NULL  AND is_active = 0";
	$return['carts']  =  $callConfig->getAllRows($query);
	$query = "SELECT id, item_name, description, image FROM stationery_items";
	$stationery_items =  $callConfig->getAllRows($query);
	foreach($stationery_items as $item){
		$items[$item->id] = $item; 
	}
	$return['item']  = $items;
	return $return;
}
function tocreateuserstationeryrequest($get){
	global $callConfig;
	$get['transaction_date'] = date('Y-m-d');
	$rnd = $this->toGetApplicationSerialNumber('stationery_requests',$get);
	$request_date =  $get['request_date']; 
	$require_date = $get['require_date']; 
	$insert_array = array('company_id'=>$get['company_id'], 'request_no'=>$rnd, 'request_date'=>$request_date, 'require_date'=>$require_date , 'emp_id'=> $get['st_emp_id'] ,'request_comment'=>$get['request_comment'], 'user_id'=>$get['user_id'],'deliver_type'=>$get['deliver_type'],'building_floor_space_id'=>$get['building_floor_space_id'] ,'building_floor_id'=>$get['building_floor_id'],'building_id'=>$get['building_id'], 'location_id'=>$get['location_id'], 'other_name'=>$get['other_name'] ,'is_active'=>'0','current_status'=>0, 'created_by'=>$get['user_id']); 
	$insert = $callConfig->insertRecord('stationery_requests',$insert_array);
	$last_id = $insert['LASTID'];

	$uquery = "UPDATE stationery_cart SET request_no = '".$rnd."', deliver_type = '".$get['deliver_type']."',request_id='{$last_id}'  WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']."  AND request_no IS NULL AND is_active = 0";
	$edata = $callConfig->executeQuery($uquery);
	
	if($get['st_current_status'] == 199 ){		
		$query = "SELECT * FROM stationery_cart WHERE company_id = ".$get['company_id']." AND request_no IN ( SELECT request_no FROM stationery_requests WHERE company_id = ".$get['company_id']." AND id = '".$insert['LASTID']."'  AND  is_active = 0 ) AND   is_active = 0";
		$res['retrive']  = $query;
		//echo "HHHHH"; print_r($res); exit;
		$datas  =  $callConfig->getAllRows($query); 
		foreach($datas as $data){
			$insert_array = array('company_id'=>$get['company_id'],'request_no'=>$data->request_no,'item_id'=>$data->item_id,'in_qty'=>0,'out_qty'=>$data->qty,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$res['insert_array'][] = $insert_array;
			$res['insert_res'][]=$callConfig->insertRecord('stationery_item_ledger',$insert_array);
			$upquey = "UPDATE stationery_items SET available_qty = available_qty-".$data->qty." WHERE id=".$data->item_id;
			$res['update'][]=$callConfig->executeQuery($upquey); 
		}
	}
	return $edata;
}
function addcouriers($post, $get){
	global $callConfig;
	$post['current_status'] = 10;
	$res=$callConfig->insertRecord($get['table'],$post);
	$courier_type = $post['courier_type'];
	
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['user_id']);
	
	if($courier_type==2){
		$email_content_id = 41;
		$msg1 = "We are pleased to inform you that your courier(".$post['courier_company']." - ".$post['courier_ref_no'].") has been successfully received by the company security guard. Now ready for your collection.";
		$notuser1[] = $post['user_id'];
	}
	if($courier_type==1){
		$email_content_id = 42;
		$msg1 = $user->display_name." have a courier package that needs to be sent to ".$post['courier_adderss'];
		$notuser1[] = $post['requested_by'];
	}
	if($courier_type==2){
		$notuser2[] = $get['user_id'];
		$msg2 = "We are informed to the employee (".$user->display_name.").your courier(".$post['courier_company']." - ".$post['courier_ref_no'].") has been successfully received by me.";
	}
	if($courier_type==1){
		$notuser2[] = $get['user_id'];
		$msg2 = "I have sent a courier request to the security guard.";
	}
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'couriers', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT','module_setup_id'=>25);
	$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'couriers', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_OFFICE_MANAGEMENT','module_setup_id'=>25);
	$ss = $this->tosendnotifiations($notdata1);
	$rss = $this->tosendnotifiations($notdata2);
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>$email_content_id, 'user_display_name'=>$user->display_name,'courier_company'=>$post['courier_company'],'courier_ref_no'=>$post['courier_ref_no'],'cc_users'=>$notuser2,'to_users'=>$notuser1,'courier_adderss'=>$post['courier_adderss']);
	$res = $this->emailsendmediater($senddata);
}
function updatecouriers($post, $get){
	global $callConfig;

	$res=$callConfig->updateRecord($get['table'],$post, 'id',$get['rid']);
	$data = $callConfig->getRow("SELECT * FROM couriers WHERE id=".$post['rid']);
	$notuser1[] = $data->created_by;
	$notuser2[] = $get['user_id'];
	if(isset($post['deliver_type']) && $post['deliver_type'] == 'DD' && !isset($post['current_status']) ){
		$msg1 = "A simpler notification to inform a security guard or admin to send a courier(".$data->courier_company." - ".$data->courier_ref_no.")  to your desk";
		$msg2 = "We are pleased to inform you that your courier(".$data->courier_company." - ".$data->courier_ref_no.").Please send to the courier in my desk ";
	}
	if(isset($post['deliver_type']) && $post['deliver_type'] == 'DD' && !isset($post['current_status'])){
		$msg1 = "A simpler notification to inform a security guard or admin to i will pickup the courier(".$data->courier_company." - ".$data->courier_ref_no.") ";
		$msg2 = "We are pleased to inform you that your courier(".$data->courier_company." - ".$data->courier_ref_no.").Please send to the courier in my desk ";
	}
	if(isset($post['current_status'])){
		$msg1 = "A simpler notification to inform a security guard or admin to i was picked the courier(".$data->courier_company." - ".$data->courier_ref_no.") ";
		$msg2 = "We are pleased to inform you that your courier(".$data->courier_company." - ".$data->courier_ref_no.").Please send to the courier in my desk ";
	}
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$data->user_id);
	

	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$data->id, 'application_table_name'=>'couriers', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT','module_setup_id'=>25);
	$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$data->id, 'application_table_name'=>'couriers', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_OFFICE_MANAGEMENT','module_setup_id'=>25);
	$ss = $this->tosendnotifiations($notdata1);
	$rss = $this->tosendnotifiations($notdata2);
	/*$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>41, 'user_display_name'=>$user->display_name,'courier_company'=>$post['courier_company'],'courier_ref_no'=>$post['courier_ref_no'],'cc_users'=>$notuser2,'to_users'=>$notuser1);
	$res = $this->emailsendmediater($senddata);*/
}
function togetuserliststationerycart($get){
	global $callConfig;
	$whr = "";
	if($get['type']==1){
		$whr = " AND user_id = ".$get['user_id'];
	}
	$query = "SELECT * FROM stationery_requests WHERE company_id = ".$get['company_id']."  AND is_active = 0 ".$whr;
	$return['carts']  =  $callConfig->getAllRows($query);
	$query = "SELECT id,name, status FROM status_progress WHERE company_id =".$get['company_id']." AND table_name = 'stationery_requests'  AND is_active = 0";
	$status = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$sts[$statu->status] = $statu->name;
	}
	$return['status'] = $sts;
	return $return;
}
function togetuserrequestcartdetails($get){
	global $callConfig;
	$query = "SELECT *, (SELECT bd.building_name FROM buildings bd WHERE bd.id = building_id ) as building_name, (SELECT bdf.floor_name FROM building_floors bdf WHERE bdf.id = building_floor_id ) as floor_name, (SELECT bds.cabin_name FROM building_floor_spaces bds WHERE bds.id = building_floor_space_id ) as cabin_name,  (SELECT emp.display_name FROM employees emp WHERE emp.id = emp_id ) as display_name FROM stationery_requests WHERE company_id = ".$get['company_id']." AND request_no = '".$get['request_no']."'  AND  is_active = 0";
	$stationery_requests  =  $callConfig->getRow($query);
	$return['stationery_requests']  =  $stationery_requests;

	$query = "SELECT * FROM stationery_cart WHERE company_id = ".$get['company_id']." AND request_no = '".$get['request_no']."'  AND  is_active = 0";
	$return['carts']  =  $callConfig->getAllRows($query);
	$query = "SELECT id, item_name, description, image, available_qty FROM stationery_items";
	$stationery_items =  $callConfig->getAllRows($query);
	foreach($stationery_items as $item){
		$items[$item->id] = $item; 
	}
	$return['item']  = $items;
	$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'DELIVERTYPE' AND is_active = 0";
	$deliver_type = $callConfig->getAllRows($query);

	foreach($deliver_type as $deliver_tp){
		$deliver_types[$deliver_tp->genc_detail_code] = $deliver_tp->genc_detail_name;
	}
	$return['deliver_types'] = $deliver_types;

	$query = "SELECT id,name, status,table_name FROM status_progress WHERE company_id =".$get['company_id']." AND table_name IN( 'stationery_requests')  AND status >10 AND is_active = 0 ORDER By display_priority";
		$status = $callConfig->getAllRows($query);
		foreach($status as $statu){
			$sts[$statu->status] = $statu->name;
		}
	$return['status'] = $sts;
	$old_requests = array();
	if($stationery_requests->id!=''){
		$query = "SELECT * FROM stationery_requests WHERE emp_id='{$stationery_requests->emp_id}' AND id != '{$stationery_requests->id}' AND company_id='{$get['company_id']}' AND is_active = 0 ";
		$old_requests = $callConfig->getAllRows($query);
	}
	$return['old_requests'] = $old_requests;
	return $return;
}
function togetallslectedtrasportdetails($get){
	global $callConfig;
	$return['value']  = array();  
	if($get['rowid']!=""){
		$query = "SELECT * FROM employee_transport_requests WHERE company_id = ".$get['company_id']." AND id = ".$get['rowid']." AND  is_active = 0";
		$resdata =  $callConfig->getRow($query);
		$get['transport_schedule_id'] = $resdata->transport_schedule_id;
		$return['value'] = $resdata;
	}
	$query = "SELECT ts.id transport_schedule_id, trm.id transport_route_id, ct.id company_transport_id, ts.route_number, ts.vehicle_number, ts.stop_name, ts.arrival_time, ts.departure_time, ts.monthly_charges, trm.location_id, trm.route_charges_monthly, trm.off_days,ct.vehicle_name, ct.vehicle_type FROM transport_schedule ts, transport_routes trm , company_transports ct WHERE ts.company_id = trm.company_id AND ts.company_id = ct.company_id AND ts.company_id = ".$get['company_id']." AND ts.route_id = trm.id AND trm.company_transport_id = ct.id AND ts.id =".$get['transport_schedule_id'];
	$return['rowdata']  =  $callConfig->getRow($query);
	$return['rowquery']  =  $query;
	$query = "SELECT id,name, status FROM status_progress WHERE company_id =".$get['company_id']." AND table_name = 'employee_transport_requests'  AND is_active = 0";
	$status = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$sts[$statu->status] = $statu->name;
	}
	$return['status'] = $sts;
	return $return;
}
  
  function toinserttranspordatarequest($post, $get){
	global $callConfig;
	$post['current_status'] = 10;
	$res=$callConfig->insertRecord('employee_transport_requests',$post);

	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['user_id']);
	$notuser1[] = $get['user_id'];
	$notuser2[] = $get['user_id'];
	$msg1 = " I requests  transportation services for an upcoming date (".$post['transport_date'].") onwards.";
	$msg2 = " A simpler notification to inform a  request transportation services for an upcoming date (".$post['transport_date'].") onwards.";
	
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'employee_transport_requests', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=3','module_setup_id'=>25);
	$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'employee_transport_requests', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=3','module_setup_id'=>25);
	$ss = $this->tosendnotifiations($notdata1);
	$rss = $this->tosendnotifiations($notdata2);
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>43, 'user_display_name'=>$user->display_name,'transport_date'=>$post['transport_date'],'cc_users'=>$notuser2,'to_users'=>$notuser1);
	$res = $this->emailsendmediater($senddata);
}
function searchcourierdata($get){
	global $callConfig;
	/*$query = "SELECT id FROM application_users WHERE display_name LIKE '%".$get['search']."%' AND is_active =0";
	$users = $callConfig->getAllRows($query);
	foreach((array)$users as $ausers){
		$user[] = $ausers->id;
	}
	$whr = "";
	if(count((array)$user)>0){
		$whr = " OR user_id IN (".implode(",",$user).")";
	}*/
	$whr = "";
	if($get['courier_type']!=""){
		$whr.= " AND courier_type = '".$get['courier_type']."'";
	}
	if($get['courier_user_id']!=""){
		$whr.= " AND user_id = '".$get['courier_user_id']."'";
	}
	if($get['courier_ref_no']!=""){
		$whr.= " AND courier_ref_no LIKE '%".$get['courier_ref_no']."%'";
	}
	if($get['courier_company']!=""){
		$whr.= " AND courier_company LIKE '%".$get['courier_company']."%'";
	}
	if($get['courier_date']!="" && $get['courier_to_date']!=""){
		$whr.= " AND courier_date >= '".$get['courier_date']."' AND courier_date >= '".$get['courier_date']."'";
	}
	
	if($get['from_detail']!=""){
		$whr.= " AND from_detail LIKE '%".$get['from_detail']."%'";
	}
	if($get['to_detail']!=""){
		$whr.= " AND to_detail LIKE '%".$get['to_detail']."%'";
	}
	$query = "SELECT * FROM `couriers` WHERE company_id = ".$get['company_id']." ".$whr." AND is_active =0";
	$return['courier_query'] = $query;
	$courier = $callConfig->getAllRows($query);
	$return['courier'] = $courier;
	
	//echo "============="; print_r($courier);
    if(count((array)$courier)>0){
		foreach($courier as $cour){
			$allusers[] = $cour->requested_by;
		}

		// $query = "SELECT id,display_name,email_id, image, emp_code  FROM employees WHERE id IN (".implode(",", $allusers).") ";
		$query = "SELECT id,display_name,email_id, image, emp_code  FROM employees WHERE company_id =".$get['company_id']."  AND is_active = 0";
		$users = $callConfig->getAllRows($query);

		foreach($users as $suser){
			$apuser[$suser->id] = $suser;
		}
		$return['emps'] = $apuser;
		
		$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'DELIVERTYPE' AND is_active = 0";
		$deliver_type = $callConfig->getAllRows($query);

		foreach($deliver_type as $deliver_tp){
			$deliver_types[$deliver_tp->genc_detail_code] = $deliver_tp->genc_detail_name;
		}
		$return['deliver_types'] = $deliver_types;
		$query = "SELECT id,name, status,table_name FROM status_progress WHERE company_id =".$get['company_id']." AND table_name IN( 'couriers')  AND is_active = 0";
		$status = $callConfig->getAllRows($query);
		foreach($status as $statu){
			$sts[$statu->table_name][$statu->status] = $statu->name;
		}
		$return['status'] = $sts;
	}
	return $return;
}
function searchstationerydata($get){
	global $callConfig;
	$whr = "";
	
	if($get['request_date']!=""){
		$whr.= " AND request_date = '".$get['request_date']."'";
	}
	if($get['stationery_user_id']!=""){
		$whr.= " AND user_id = '".$get['stationery_user_id']."'";
	}
	if($get['stationery_request_ref_no']!=""){
		$whr.= " AND request_no LIKE '%".$get['stationery_request_ref_no']."%'";
	}
	if($get['require_date']!=""){
		$whr.= " AND require_date = '".$get['require_date']."'";
	}
	
	$query = "SELECT * FROM `stationery_requests` WHERE company_id = ".$get['company_id']." ".$whr." AND is_active =0";
	$return['stationery_requests_query'] = $query;
	$stationery_requests = $callConfig->getAllRows($query);
	$return['stationery_requests'] = $stationery_requests;
	
	//echo "============="; print_r($courier);
    if(count((array)$stationery_requests)>0){
		foreach($stationery_requests as $cour){
			$allusers[] = $cour->user_id;
		}

		$query = "SELECT id, display_name, user_email , user_photo  FROM application_users WHERE id IN (".implode(",", $allusers).") ";
		$users = $callConfig->getAllRows($query);

		foreach($users as $suser){
			$apuser[$suser->id]['id'] = $suser->id;
			$apuser[$suser->id]['display_name'] = $suser->display_name;
			$apuser[$suser->id]['email_id'] = $suser->user_email;
			$apuser[$suser->id]['image'] = $suser->user_photo;
		}
		$return['emps'] = $apuser;
		
		$query = "SELECT id,genc_code,genc_detail_code,genc_detail_name FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code = 'DELIVERTYPE' AND is_active = 0";
		$deliver_type = $callConfig->getAllRows($query);

		foreach($deliver_type as $deliver_tp){
			$deliver_types[$deliver_tp->genc_detail_code] = $deliver_tp->genc_detail_name;
		}
		$return['deliver_types'] = $deliver_types;
		$query = "SELECT id,name, status,table_name FROM status_progress WHERE company_id =".$get['company_id']." AND table_name IN( 'stationery_requests')  AND is_active = 0";
		$status = $callConfig->getAllRows($query);
		foreach($status as $statu){
			$sts[$statu->table_name][$statu->status] = $statu->name;
		}
		$return['status'] = $sts;
	}
	return $return;

}
function addstationery($post, $get){
	global $callConfig;
	$res=$callConfig->insertRecord('stationery_items',$post);
	$insert_id = $res['LASTID'];
	$insert_array = array('company_id'=>$post['company_id'],'request_no'=>'Topup','item_id'=>$insert_id,'in_qty'=>$post['available_qty'],'out_qty'=>0,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
	$insert_array['request_date'] = date('Y-m-d');
	$res=$callConfig->insertRecord('stationery_item_ledger',$insert_array);
	return $res;
}
function updatestationeryrequest($post, $get){
	global $callConfig;
	$array = array();$res =array();
	//echo "HHHHH"; print_r($post); exit;
	if($post['current_status']!=""){
		$array['current_status'] = $post['current_status'];
	}
	if($post['comment']!=""){
		$array['comments'] = $post['comment'];
	}

	$upquery = $callConfig->updateRecord('stationery_requests',$array,'id',$post['update_id']);
	$res['main_query'] = $upquery;

	if($post['current_status'] == 101 || $post['current_status'] == 199 ){		
		$query = "SELECT * FROM stationery_cart WHERE company_id = ".$get['company_id']." AND request_no IN ( SELECT request_no FROM stationery_requests WHERE company_id = ".$get['company_id']." AND id = '".$post['update_id']."'  AND  is_active = 0 ) AND   is_active = 0";
		$res['retrive']  = $query;
		//echo "HHHHH"; print_r($res); exit;
		$datas  =  $callConfig->getAllRows($query); 
		foreach($datas as $data){
			$insert_array = array('company_id'=>$get['company_id'],'request_no'=>$data->request_no,'item_id'=>$data->item_id,'in_qty'=>0,'out_qty'=>$data->qty,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$res['insert_array'][] = $insert_array;
			$res['insert_res'][]=$callConfig->insertRecord('stationery_item_ledger',$insert_array);
			$upquey = "UPDATE stationery_items SET available_qty = available_qty-".$data->qty." WHERE id=".$data->item_id;
			$res['update'][]=$callConfig->executeQuery($upquey); 
		}
	}
	$req = $callConfig->getRow("SELECT * FROM stationery_requests WHERE company_id = ".$get['company_id']." AND id = ".$post['update_id']);
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$req->user_id);
	$status = $callConfig->getRow("SELECT name FROM status_progress WHERE company_id=".$get['company_id']." AND table_name='stationery_requests' AND status = ".$post['current_status']." AND is_active =0");
	$msg1 = "We are pleased to inform you that ".$user->display_name."  requested stationery request(".$req->request_no.")   have been successfully ".$status->name;
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$req->id, 'application_table_name'=>'stationery_requests', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=2','module_setup_id'=>25);
	$notuser1[] = $req->user_id;
	$notuser1[] = $req->request_id;
	$ss = $this->tosendnotifiations($notdata1);
	
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>44, 'user_display_name'=>$user->display_name,'request_no'=>$req->request_no,'status_name'=>$status->name,'cc_users'=>$notuser1,'to_users'=>'');
	$res = $this->emailsendmediater($senddata);
	return $res;
}
function topupstationery($post, $get){
	global $callConfig;
	$insert_array = array('company_id'=>$get['company_id'],'item_id'=>$post['item_id'],'in_qty'=>$post['available_qty'],'out_qty'=>0, 'request_no'=>'Topup', 'comments'=>$post['comment'], 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
	$insert_array['request_date'] = $post['request_date'] ?? date('Y-m-d');
	$res['insert_array'][] = $insert_array;
	$res['insert_res'][]=$callConfig->insertRecord('stationery_item_ledger',$insert_array);
	// $upquey = "UPDATE stationery_items SET available_qty = available_qty+".$post['available_qty']." WHERE id=".$post['item_id'];
	$upquey = "UPDATE stationery_items SET available_qty = available_qty+".$post['available_qty']." WHERE id=".$post['item_id'];
	$res['update'][]=$callConfig->executeQuery($upquey); 
	return $res;
}
function updatetranspotfrom($get){
	global $callConfig;
	global $callConfig;
	$array = array();$res =array();
	//echo "HHHHH"; print_r($post); exit;
	if($get['current_status']!=""){
		$array['current_status'] = $get['current_status'];
	}

	$upquery = $callConfig->updateRecord('employee_transport_requests',$array,'id',$get['update_id']);
	$res['main_query'] = $upquery;

	$req = $callConfig->getRow("SELECT * FROM employee_transport_requests WHERE company_id = ".$get['company_id']." AND id = ".$get['update_id']);
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$req->user_id);
	$status = $callConfig->getRow("SELECT name,action_label FROM status_progress WHERE company_id=".$get['company_id']." AND table_name='employee_transport_requests' AND status = ".$get['current_status']." AND is_active =0");
	$msg1 = "We are pleased to inform you that ".$user->display_name."  requested transport request(".$req->request_no.")   have been successfully ".$status->action_label;
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$req->id, 'application_table_name'=>'employee_transport_requests', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=3','module_setup_id'=>25);
	$notuser1[] = $req->created_by;
	$notuser1[] = $get['user_id'];
	$ss = $this->tosendnotifiations($notdata1);
	
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>43, 'user_display_name'=>$user->display_name,'status_name'=>$status->action_label,'cc_users'=>$notuser1,'to_users'=>'','transport_date'=>$req->transport_date);
	$res = $this->emailsendmediater($senddata);
	return $res;
}
function togetinsertdetalsfoods($get){
	global $callConfig;
	$return['value']  = array();  
	if($get['rowid']!=""){
		$query = "SELECT * FROM employee_food_requests WHERE company_id = ".$get['company_id']." AND id = ".$get['rowid']." AND  is_active = 0";
		$resdata =  $callConfig->getRow($query);
		$get['food_option_id'] = $resdata->food_option_id;
		$return['value'] = $resdata;
	}
	$query = "SELECT  id, food_option FROM food_options WHERE company_id=".$get['company_id']." AND  id = ".$get['food_option_id']." AND is_active = 0";
	$return['food_options'] =  $callConfig->getRow($query);
	$query = "SELECT id,name, status FROM status_progress WHERE company_id =".$get['company_id']." AND table_name = 'employee_food_requests'  AND is_active = 0";
	$status = $callConfig->getAllRows($query);
	foreach($status as $statu){
		$sts[$statu->status] = $statu->name;
	}
	$return['status'] = $sts;
	return $return;
}

function addfoodrequest($post, $get){
	global $callConfig;
	$post['current_status'] = 10;
	$post['start_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $post['start_date'])));
	$post['stop_date'] = date('Y-m-d', strtotime(str_replace('/"', '-', $post['stop_date'])));
	$res=$callConfig->insertRecord('employee_food_requests',$post);

	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$post['user_id']);
	$notuser1[] = $get['user_id'];
	$notuser2[] = $get['user_id'];
	$msg1 = " I requests  food services for an upcoming date (".$post['start_date']." to ".$post['stop_date']." )";
	$msg2 = " A simpler notification to inform a  request transportation services for an upcoming date (".$post['start_date']." to ".$post['stop_date']." )";
	
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'employee_transport_requests', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=4','module_setup_id'=>25);
	$notdata2 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg2, 'transaction_number'=>$res['LASTID'], 'application_table_name'=>'employee_transport_requests', 'type'=>'INFORMATION', 'notification_users'=>$notuser2,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=4','module_setup_id'=>25);
	$ss = $this->tosendnotifiations($notdata1);
	$rss = $this->tosendnotifiations($notdata2);
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>44, 'user_display_name'=>$user->display_name,'start_date'=>$post['start_date'],'stop_date'=>$post['stop_date'],'cc_users'=>$notuser2,'to_users'=>$notuser1);
	$res = $this->emailsendmediater($senddata);
}

function updatefoodrequest($get){
	global $callConfig;
	global $callConfig;
	$array = array();$res =array();
	//echo "HHHHH"; print_r($post); exit;
	if($get['current_status']!=""){
		$array['current_status'] = $get['current_status'];
	}

	$upquery = $callConfig->updateRecord('employee_food_requests',$array,'id',$get['update_id']);
	$res['main_query'] = $upquery;

	$req = $callConfig->getRow("SELECT * FROM employee_food_requests WHERE company_id = ".$get['company_id']." AND id = ".$get['update_id']);
	$user = $callConfig->getRow("SELECT display_name FROM application_users WHERE id=".$req->user_id);
	$status = $callConfig->getRow("SELECT name,action_label FROM status_progress WHERE company_id=".$get['company_id']." AND table_name='employee_food_requests' AND status = ".$get['current_status']." AND is_active =0");
	$msg1 = "We are pleased to inform you that ".$user->display_name."  requested food service request(".$req->start_date." to ".$req->stop_date." )  have been successfully ".$status->action_label;
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg1, 'transaction_number'=>$req->id, 'application_table_name'=>'employee_transport_requests', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_OFFICE_MANAGEMENT&TAB=3','module_setup_id'=>25);
	$notuser1[] = $req->created_by;
	$notuser1[] = $get['user_id'];
	$ss = $this->tosendnotifiations($notdata1);
	
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>45, 'user_display_name'=>$user->display_name,'start_date'=>$req->start_date,'stop_date'=>$req->stop_date,'cc_users'=>$notuser2,'to_users'=>$notuser1);
	$res = $this->emailsendmediater($senddata);
	return $res;
}
function searchstationeryreport($get){
	global $callConfig;
	$whr = "";
	if($get['list_date']!=""){
		$whr.= " AND  DATE(`created_date`) >=  '".$get['list_date']."'";
	}
	if($get['item_id']!=""){
		$whr.= " AND item_id = '".$get['item_id']."'";
	}
	$query = "SELECT * FROM `stationery_item_ledger` WHERE company_id = ".$get['company_id']." ".$whr." AND is_active =0 ORDER By id ASC";
	$datas  =  $callConfig->getAllRows($query);
	return $datas;
}
function togetofficemanagementdashboard($get){
	global $callConfig;
	$whr = "";
	$query = "SELECT sum(if(courier_type=1,1,0)) total_send, sum(if(courier_type=2,1,0)) total_rec,  sum(if(courier_type=1 && current_status != ".ST_APPROVED." ,1,0)) send_pending,  sum(if(courier_type=2 && current_status != ".ST_APPROVED." ,1,0)) rec_pending FROM couriers  WHERE company_id=  ".$get['company_id']."  AND is_active = 0";
	$return['couriers']  =  $callConfig->getRow($query);
	$query = "SELECT count(id) total_stat, sum(if(current_status=10,1,0)) stat_pending FROM stationery_requests WHERE company_id=  ".$get['company_id']."  AND is_active = 0";
	$return['stationery']  =  $callConfig->getRow($query);
	$query = "SELECT count(id) total_emptr, sum(if(current_status=10,1,0)) emptr_pending FROM employee_transport_requests WHERE company_id=  ".$get['company_id']."  AND is_active = 0";
	$return['employee_transport']  =  $callConfig->getRow($query);
	$query = "SELECT count(id) total_empfr, sum(if(current_status=10,1,0)) emptr_pending FROM employee_food_requests WHERE company_id=  ".$get['company_id']."  AND is_active = 0";
	$return['employee_food']  =  $callConfig->getRow($query);

	//$query = "SELECT sum(if(`available_qty` < `minmum_qty`,1,0)) minqty FROM `stationery_items` WHERE company_id=  ".$get['company_id']."  AND is_active = 0";
	$query = "SELECT * FROM `stationery_items` WHERE company_id=  ".$get['company_id']."  AND available_qty < minmum_qty AND is_active = 0";
	$min_sat  =  $callConfig->getAllRows($query);
	$return['all_min_sat']  =  $min_sat;
	$return['min_sat']['minqty'] = count($min_sat);
	$st_date = date("Y-m")."-01";
	$ed_date = date("Y-m-t");
	$query = "SELECT *  FROM employee_food_requests   WHERE company_id=  ".$get['company_id']."  AND `start_date` <=  '".$st_date."'  AND  `stop_date` >=   '".$ed_date."'  AND current_status = ".ST_APPROVED." AND  is_active = 0";
	$fddata=  $callConfig->getAllRows($query);
	$return['emp_food'] = $fddata;
	$return['month_food']['record_count']  = count($fddata);

	$query = "SELECT id,display_name,user_email as email_id,user_photo image FROM application_users";
	$employs = $callConfig->getAllRows($query);
	foreach($employs as $empl){
		$emps[$empl->id] = $empl;
	}
	$return['emps'] = $emps;

	$query = "SELECT * FROM couriers  WHERE company_id=  ".$get['company_id']."  AND is_active = 0";	
	$return['list_couriers']  =  $callConfig->getAllRows($query);

	$query = "SELECT * FROM stationery_requests  WHERE company_id=  ".$get['company_id']."  AND is_active = 0";	
	$return['list_stationery_requests']  =  $callConfig->getAllRows($query);

	$query = "SELECT * FROM employee_transport_requests  WHERE company_id=  ".$get['company_id']."  AND is_active = 0";	
	$return['list_employee_transports']  =  $callConfig->getAllRows($query);

	$query = "SELECT * FROM employee_food_requests  WHERE company_id=  ".$get['company_id']."  AND is_active = 0";	
	$return['list_employee_food']  =  $callConfig->getAllRows($query);

	$query = "SELECT  id, food_option FROM food_options WHERE company_id=".$get['company_id']." AND is_active = 0";
	$food_options =  $callConfig->getAllRows($query);
	foreach($food_options as $food_option){
		$food[$food_option->id] = $food_option; 
	}
	$return['food']  =  $food;
	return $return;	
}
function searchtransportbus($get){
	global $callConfig;
	$return['get'] = $get;

	$whr = "";
	if($get['pickup'] != "" && $get['pickup'] == ""){
		$whr.= " AND route_starts LIKE '%".$get['pickup']."%'";
	}
	if($get['droping'] != "" && $get['droping'] == ""){
		$whr.= " AND route_ends = '".$get['droping']."'";
	}
	if($get['pickup'] != "" && $get['pickup'] != ""){
		$whr.= " AND ( route_starts LIKE '%".$get['pickup']."%' OR route_ends LIKE '%".$get['pickup']."%')";
	}
	$query = "SELECT * FROM company_transports WHERE company_id =".$get['company_id']." AND is_active = 0";
	$buses = $callConfig->getAllRows($query);
	foreach($buses as $buse){
		$bus[$buse->id] = $buse;
	}
	$return['bus'] = $bus;

	$query = "SELECT * FROM transport_routes WHERE company_id =".$get['company_id']." AND is_active = 0 ".$whr;
	$return['routes']= $callConfig->getAllRows($query);
	
	$query = "SELECT * FROM transport_schedule WHERE company_id =".$get['company_id']." AND is_active = 0 ";
	$transport_schedules = $callConfig->getAllRows($query);
	foreach($transport_schedules as $transport_schedule){
		$tschedule[$transport_schedule->route_id][] = $transport_schedule;
		$listschedlue[] = $transport_schedule->stop_name;
	}
	$return['transport_schedule'] = $tschedule;
	$return['listschedlue'] = array_unique($listschedlue);
	return $return;
}
function reportofyearmonthalldetails($get){
	global $callConfig;
	//print_r($get);
	if($get['admin_type']!=1){
		//echo "---------------"; exit;
		$query = " SELECT emp_id FROM pay_area_employees WHERE company_id = ".$get['company_id']." AND pay_area_id IN ( SELECT pay_area_id FROM payroll_area_admins WHERE company_id = ".$get['company_id']." AND emp_id = ".$get['emp_id']." AND is_active =0)";
		$res['payarea_query'] = $query;
		$allemps = $callConfig->getAllRows($query);
		foreach($allemps as $allemp){
			$emps[] = $allemp->emp_id;
		}
		if(count((array)$emps)>0){
		$whr = " AND  id IN (".implode(",",$emps).")";
		$whremp = " AND  emp_id IN (".implode(",",$emps).")";
		}
	}
	//echo "*********"; exit;
	$sdate = $get['year'].'-01-01';
	$gyear = $get['year'];
	$head_query = "SELECT COUNT(id) cc FROM  employees WHERE company_id =".$get['company_id']." AND  joining_date < '".$sdate."' AND is_active = 0".$whr;
	$res['head_query'] = $head_query;
	$headdata = $callConfig->getRow($head_query);
	$res['head_res'] = $headdata;
	$head_in_count = $headdata->cc;

	$head_mnth_query = "SELECT COUNT(id) mc, month(joining_date) month, MONTHNAME(joining_date) AS month_name, sum(if(rejoin_ref_id!='',1,0)) rejoin FROM  employees WHERE YEAR(joining_date) = '".$gyear."' AND is_active = 0 GROUP BY  YEAR(joining_date), MONTH(joining_date)"; 
	$res['head_mnth_query'] = $head_mnth_query;
	$head_month_res = $callConfig->getAllRows($head_mnth_query);
	$res['head_month_res'] = $head_month_res;

	$head_mnth_mins_query = "SELECT COUNT(id) mc, month(date_of_leaving) month, MONTHNAME(date_of_leaving) AS month_name FROM  employees WHERE joining_date > '".$sdate."' AND is_active = 0 GROUP BY  YEAR(date_of_leaving), MONTH(date_of_leaving)";
	$res['head_mnth_query'] = $head_mnth_mins_query;
	$head_month_min_res = $callConfig->getAllRows($head_mnth_mins_query);
	$res['head_month_min_res'] = $head_month_min_res;

	$revoknquery = "SELECT COUNT(id) cc, YEAR(releive_date) year, MONTH(releive_date) month FROM employee_terminations WHERE company_id =".$get['company_id']." AND `releive_date` > '".$sdate."' AND current_status = ".ST_SEND_BACK ." AND is_active = 0 GROUP BY YEAR(releive_date), MONTH(releive_date) ";
	$revokdata = $callConfig->getAllRows($revoknquery);
	foreach($revokdata as $rev){
		$rovek[$rev->month] = $rev->cc;
	}

	$revoknquery = "SELECT COUNT(id) cc,YEAR(end_date) yeear,  MONTH(end_date) month FROM employee_performance_appraisals  WHERE company_id =".$get['company_id']." AND YEAR(releive_date) = '".$get['year']."' AND current_status = ".ST_APPROVED ." AND is_active = 0 GROUP BY YEAR(end_date), MONTH(end_date) ";
	$emp_performance = $callConfig->getAllRows($revoknquery);
	foreach($emp_performance as $performance){
		$perform[$performance->month] = $performance->cc;
	}

	$salaries = "SELECT SUM(IF(salary_reason_id=3,1,0)) salrev , SUM(IF(salary_reason_id=2,1,0)) salprom, SUM(IF(salary_reason_id=4,1,0)) salcorr, YEAR(affected_date) year, MONTH(affected_date) month FROM `salary_template` WHERE company_id = ".$get['company_id']." AND YEAR(affected_date) = '".$get['year']."' AND is_active = 0 GROUP BY YEAR(affected_date), MONTH(affected_date) ";
	$salaries_res = $callConfig->getAllRows($salaries);
	
	foreach($salaries_res as $salres){
		$revision_salary[$salres->month] = $salres->salrev;
		$revision_promotion[$salres->month] = $salres->salprom;
		$salary_correction[$salres->month] = $salres->salcorr;
	}	

	$salarexu = "SELECT COUNT(id) cc,pay_year year,pay_month  monthFROM `salary_template` WHERE company_id = ".$get['company_id']." AND pay_year = '".$get['year']."' AND category_id =9 AND is_active = 0 GROUP BY pay_year,pay_month ";
	$salarexu_res = $callConfig->getAllRows($salarexu);
	
	foreach($salarexu_res as $salres){
		$stop_salary[$salres->month] = $salres->cc;
		
	}	


	$leavequery = "SELECT sum(total_lop_days) cc, pay_year year, pay_month	 month FROM pay_run_summary WHERE company_id =".$get['company_id']." AND YEAR(pay_year) = '".$get['year']."' AND is_active = 0 GROUP BY pay_year, pay_month";
	$lopleaves = $callConfig->getAllRows($leavequery);
	foreach($lopleaves as $lopleave){
		$lopleave[$lopleave->month] = $lopleave->cc;
	}

	
	$earnquery = "SELECT id, payslip_name, report_group_id, display_seq FROM `earnings` WHERE company_id =".$get['company_id']."  ORDER BY display_seq";
	$earns = $callConfig->getAllRows($earnquery);
	foreach($earns as $earn){
		$earn_data[$earn->report_group_id][] = $earn;
	}

	$res['earn_data'] = $earn_data;

	$year = date('Y');
	$month = date('m');
	$payquery = "SELECT SUM(`salary_component_actual`) sca, `calculation_type`,`component_id`,`pay_year`,`pay_month` FROM `pay_run_summary_components` WHERE `pay_year` = '".$year."' AND is_active = 0 GROUP BY `component_id`,`pay_year`,`pay_month`";
	$paydata = $callConfig->getAllRows($payquery);

	foreach((array)$paydata as $payda){
		$payvalue[$payda->pay_month][$payda->component_id] = $payda->sca;
	}

	foreach((array)$head_month_res as $add_month_res){
		$new_add[$add_month_res->month] = $add_month_res->mc;
		$rejoin[$add_month_res->month] = $add_month_res->rejoin;
	}
	foreach((array)$head_month_min_res as $min_month_res){
		$new_min[$min_month_res->month] = $min_month_res->mc;
	}
	/*if($month>3){
		$year = $year;
	    $sdate = $year."-03-01";
	}else{
	    $year = $year-1;
	    $sdate = $year."-03-01";
	}*/
	//echo $sdate;
	for($i=0; $i<12; $i++){ 
       
       $nn = date('n', strtotime($sdate));
       //echo $nn."</br>";
        //echo "Head Count:".$head_in_count."ADD :".$new_add[$nn]."min :".$new_min[$nn]."</br>";
        $head_in_count = intval($head_in_count) + intval($new_add[$nn]) -  intval($new_min[$nn]);
        $head_count[$nn] = $head_in_count;
        $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
    } //exit;


    $res['head_count'] = $head_count;
	$res['addtion_head_count'] = $new_add;
    $res['re_joinee'] = $rejoin;
	$res['revoke'] = $rovek;
	$res['internal_transfer'] = array();
	$res['deletion_head_count'] = $new_min;
	$res['promotion'] = $perform;
	$res['revision_salary'] = $revision_salary;
	$res['revision_promotion'] = $revision_promotion; 
	$res['salary_correction'] = $salary_correction;
	$res['lop'] = $lopleave;
	$res['lop_reversal'] = array();
	$res['location_transfer'] = array();
	$res['stop_salary'] = $stop_salary;
	$res['paydata'] = $payvalue;
	return $res;
	//print_r($get);
}
function togetemployeeform16adetails($get){
	global $callConfig;
	$query = "SELECT * FROM emp_it_form_16a WHERE company_id =".$get['company_id']." AND emp_id=".$get['empid']." AND assessment_year='".$get['assessment_year']."' AND is_active=0";
	$data = $callConfig->getRow($query);
	//print_r($data); //exit;
	if(count((array)$data)==0){
		//echo "----------"; exit;
		$empquery = "SELECT emp.id, emp.display_name, emp.father_name, emp.address, emp.pan_number,( SELECT de.designation_name FROM designations de WHERE de.id = emp.band_id AND de.company_id = emp.company_id ) designation_name, ( SELECT le.location_name FROM locations le WHERE le.id = emp.location_id AND le.company_id = emp.company_id) location_name FROM employees emp WHERE company_id =".$get['company_id']."  AND id=".$get['empid'];

		$emp= $callConfig->getRow($empquery);

		$conquery = "SELECT id, name, admin_email, tan_number, pan_number, contact_address, display_logo, cit_address   FROM config WHERE company_id =".$get['company_id']." AND is_active=0";
		$condata = $callConfig->getRow($conquery);

		$deposquery = "SELECT * FROM emp_tax_deposit_respect WHERE company_id =".$get['company_id']." AND emp_id=".$get['empid']." AND assessment_year='".$get['assessment_year']."' AND is_active=0 ORDER BY deposit_amont_date ASC";
		$deposdata = $callConfig->getAllRows($deposquery);
		///echo "<pre>"; print_r($empdata);  print_r($condata);  print_r($deposdata);  echo "</pre>"; exit;
		$ass = explode("-",$get['assessment_year']);
		$paysummary = "SELECT sum(`salary_component_actual`), `component_id`, `compnent_type`, `pay_year`, `pay_month` FROM pay_run_summary_components WHERE ( (pay_year = ".$ass['0']." AND pay_month >= 4) OR (pay_year = ".$ass['1']." AND pay_month <= 3) ) AND `employee_id` = ".$get['empid']." GROUP BY component_id";
		$paydata = $callConfig->getAllRows($paysummary);
		/*foreach(){
			
		}
*/
		$h=1;
		foreach($deposdata as $res){			
			$certificate_no = $res->certificate_no;
			$certificate_last_update_on = $res->certificate_date;
			$mont = date('m', strtotime($res->deposit_amont_date));
			if($h==1){
				$period_from = $res->deposit_amont_date;
			}
			$period_to = $res->deposit_amont_date;
			//echo $mont; echo "<pre>"; print_r($q1); print_r($res);    echo "</pre>"; exit;
			if(in_array($mont,array('6','4','5'))){
				$q1['code'] = $res->quarterly_receipt_no;
				$q1['taxble_amount'] = $q1['taxble_amount'] + $res->taxble_amount;
				$q1['tax_deducted'] = $q1['tax_deducted'] + $res->tax_deducted;
				$q1['tax_deposited'] = $q1['tax_deposited'] + $res->tax_deposited;
					
			}
			
			if(in_array($mont,array('7','8','9'))){
				$q2['code'] = $res->quarterly_receipt_no;
				$q2['taxble_amount'] = $q2['taxble_amount'] + $res->taxble_amount;
				$q2['tax_deducted'] = $q2['tax_deducted'] + $res->tax_deducted;
				$q2['tax_deposited'] = $q2['tax_deposited'] + $res->tax_deposited;
			}
			if(in_array($mont,array('10','11','12'))){
				$q3['code'] = $res->quarterly_receipt_no;
				$q3['taxble_amount'] = $q3['taxble_amount'] + $res->taxble_amount;
				$q3['tax_deducted'] = $q3['tax_deducted'] + $res->tax_deducted;
				$q3['tax_deposited'] = $q3['tax_deposited'] + $res->tax_deposited;
			}
			if(in_array($mont,array('1','2','3'))){
				$q4['code'] = $res->quarterly_receipt_no;
				$q4['taxble_amount']= $q4['taxble_amount'] + $res->taxble_amount;
				$q4['tax_deducted'] = $q4['tax_deducted'] + $res->tax_deducted;
				$q4['tax_deposited']= $q4['tax_deposited'] + $res->tax_deposited;
			}
			$all[]= $res; $h++;
		}
		
		$employee_reference_no = "";
		$qqq = array('0'=>$q1,'1'=>$q2,'2'=>$q3,'3'=>$q4);
		
		$quarterly_deduction = json_encode($qqq,1);
		$tax_deposited_respect_challan = json_encode($all,1);
		//echo "<pre>"; print_r($qqq);print_r($all); exit;



		$insert_array = array('company_id'=>$get['company_id'], 'emp_id'=>$get['empid'], 'assessment_year'=>$get['assessment_year'], 'employee_name'=>$emp->display_name, 'designation'=>$emp->designation_name , 'garden'=>$emp->father_name , 'employee_address'=>$emp->address, 'certificate_no'=>$certificate_no,'certificate_last_update_on'=>$certificate_last_update_on,'employer_address'=>$emp->address, 'pan_deductor'=>$condata->pan_number, 'tan_deductor'=>$condata->tan_number , 'pan_employee'=>$emp->pan_number ,'employee_reference_no'=>$employee_reference_no, 'cit_details'=>$condata->cit_address,'period_from'=>$period_from, 'period_to'=>$period_to, 'quarterly_deduction'=>$quarterly_deduction, 'tax_deposited_respect_adjustment'=>'', 'tax_deposited_respect_challan'=>$tax_deposited_respect_challan, 'place'=>$emp->location_name, 'place_date'=>$certificate_last_update_on, 'is_active'=>'0','current_status'=>'0', 'created_by'=>$get['user_id']);
		$insert = $callConfig->insertRecord('emp_it_form_16a',$insert_array);
		$query = "SELECT * FROM emp_it_form_16a WHERE company_id =".$get['company_id']." AND emp_id=".$get['empid']." AND assessment_year='".$get['assessment_year']."' AND is_active=0";
		$data = $callConfig->getRow($query);


		//echo "<pre>"; print_r($insert_array); exit;

	}
	$return['data'] = $data;
	return $return;
}
function assignemptofrom16a($get){
	global $callConfig;
	
	$whrstr = ""; $month = 0;
	if($get['month']!='N'){
		$whrstr=" AND document_month = ".$get['month'];
		$month = $get['month'];
	}
	print_r($get);//exit;
	$query = "SELECT page, document_name FROM document_lists WHERE company_id = ".$get['company_id']." AND id = ".$get['list_id'];
	//echo $query; //exit;
	$req = $callConfig->getRow($query);
	//print_r($req);//exit;
	$query = "SELECT id,emp_id FROM emp_assign_release_documents WHERE company_id = ".$get['company_id']. " AND assessment_year = '".$get['assement']."' AND document_list_id = ".$get['list_id']. "  AND emp_id IN (".$get['empids'].") AND is_active = 0".$whrstr;
	//echo $query; exit;
	$sinrows = $callConfig->getAllRows($query);
	//print_r($sinrows);
	$ssemp = array(); $inempids = array();
	foreach($sinrows as $ssin){
		$ssemp[]= $ssin->emp_id;
	}
	$user = $callConfig->getAllRows("SELECT id,display_name,user_id FROM employees WHERE id IN (".$get['empids'].")");
	foreach($user as $use){
		$allusers[$use->id] = $use->user_id;
	}
	$tempids = explode(",",$get['empids']);
	foreach($tempids as $aempid){
		if(!in_array($aempid,$ssemp)){
			$inempids[] = $aempid;
			$inuserids[] = $allusers[$aempid];
		}else{
			$ssuser[]= $allusers[$aempid];
		}
	}
	//print_r($tempids);print_r($inempids); exit;
	if(count((array)$inempids)>0){
		$inquery = "INSERT INTO `emp_assign_release_documents`(  `company_id`, `assessment_year`, `emp_id`, `document_list_id`, `document_month`,`send_date`, `page`, `is_active`, `current_status`,  `created_by`, `created_date`) SELECT `company_id`,  '".$get['assement']."', `id`, ".$get['list_id'].", ".$month.", '".date('Y-m-d')."', '".$req->page."', `is_active`, `current_status`, ".$get['user_id'].", '".date("Y-m-d H:i:s")."' FROM `employees` WHERE company_id = '".$get['company_id']."' AND is_active = 0 AND id IN (".implode(",",$inempids).")";
		//echo $inquery; exit;
			$res = $callConfig->executeQuery($inquery);
		//$insert_array = array('company_id'=>$get['company_id'],'assessment_year'=>$get['assement'],'emp_id'=>$get['empid'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		//$res = $callConfig->insertRecord('emp_assign_release_documents', $insert_array);
	}
	if(count((array)$ssemp)>0){
		$upquery = "UPDATE emp_assign_release_documents SET is_active = 1 WHERE company_id = ".$get['company_id']." AND assessment_year= '".$get['assement']."' AND  document_list_id = ".$get['list_id']."  AND emp_id IN (".implode(",",$ssemp).") AND is_active = 0";
		$res = $callConfig->executeQuery($upquery);
	}

	$notuser1 = $inuserids;
	$notuser2[] = $get['user_id'];
	$msg ="We are pleased to inform you that a new document ( ".$get['assement']." - ".$req->document_name."), is now available for your review and use";
	$msg2 =" We regret to inform you that the release of the document ( ".$get['assement']." - ".$req->document_name.") has been temporarily blocked ";
	$senddata = array('company_id'=>$get['company_id'], 'session_company_id'=>$get['company_id'], 'email_content_id'=>55, 'document_title'=>$get['assement']." - ".$req->document_name,'release_date'=>date('Y-m-d'),'cc_users'=>$notuser2,'to_users'=>$notuser1);
	$notdata1 = array('company_id'=>$get['company_id'], 'notification_message'=> $msg, 'transaction_number'=>$req->id, 'application_table_name'=>'emp_assign_release_documents', 'type'=>'ACTION', 'notification_users'=>$notuser1,'option'=>'COM_VIEW_DOCUMENTS','module_setup_id'=>25);
	if(count((array)$notuser1)>0){
		$ss = $this->tosendnotifiations($notdata1);
		$res = $this->emailsendmediater($senddata);
	}
	
	$notdata1['notification_users'] = $ssuser;
	$notdata1['notification_message'] = $msg2;	
	$senddata['to_users'] = $ssuser;
	if(count((array)$ssuser)>0){
		$ss = $this->tosendnotifiations($notdata1);
		$res = $this->emailsendmediater($senddata);
	}

	
	
	return $res;
}
function getassignfrom16aemployeelist($get){
	global $callConfig;
	$query = "SELECT id, document_name FROM `document_lists` WHERE company_id = ".$get['company_id']." AND category_id = 4 AND is_active = 0 ";
	$lists = $callConfig->getAllRows($query);
	foreach($lists as $list){
		$listdata[$list->id] = $list->document_name;
	}
	$return['listdata'] = $listdata;
	$query = "SELECT `assessment_year`, document_list_id, COUNT(`emp_id`) cc, created_date, `is_active`, `current_status` FROM `emp_assign_release_documents` WHERE company_id = ".$get['company_id']."  AND is_active = 0 GROUP BY assessment_year, document_list_id ";
	$return['data'] = $callConfig->getAllRows($query);
	return $return; 
}
function togetreleasedocumentlistdetails($get){
	global $callConfig;

	$query = "SELECT * FROM `document_lists` WHERE company_id = ".$get['company_id']." AND category_id = 4 AND is_active = 0 ";
	$lists = $callConfig->getAllRows($query);
	$return['cat'] = $lists;
	$query = "SELECT * FROM `emp_assign_release_documents` WHERE company_id = ".$get['company_id']." AND emp_id = ".$get['emp_id']." AND is_active = 0 ";
	$lists = $callConfig->getAllRows($query);
	foreach($lists as $list){
		$data[$list->document_list_id][$list->assessment_year][] = $list;
	}
	$return['data'] = $data;
	return $return; 
}
function togetdcumentdashboarddetails($get){
	global $callConfig;
	$query = "SELECT id, category_name, category_description, created_date, current_status FROM `document_categories` WHERE company_id =".$get['company_id']."  AND is_active = 0";
	$return['categories'] = $callConfig->getAllRows($query);
	$query = "SELECT id, document_name, category_id, parent_document_id, page, current_status, created_date FROM `document_lists` WHERE company_id =".$get['company_id']."  AND is_active = 0";
	$ptasks  = $callConfig->getAllRows($query);
	foreach($ptasks as $ptask){		
		$task[$ptask->category_id][] = $ptask;
	}
	$return['lists'] = $task;
	$return['assign_data'] = $this->getassignfrom16aemployeelist($get);
	$query = "SELECT `id`, `company_id`, `emp_id`, ( SELECT display_name FROM employees emp	WHERE 
	emp.id = emp_id) 'display_name', `assessment_year`, `certificate_no`, `certificate_date`, created_date, current_status FROM `emp_tax_deposit_respect` WHERE company_id =".$get['company_id']."  AND is_active = 0 ORDER by id DESC";
	$return['emp_tax_deposit_respect'] = $callConfig->getAllRows($query);

	return $return;
}
function togetinsertemptaxdepositdetails($get){
	global $callConfig;
	$query = "SELECT id, document_name FROM `document_lists` WHERE company_id = ".$get['company_id']." AND category_id = 4 AND is_active = 0 ";
	$lists = $callConfig->getAllRows($query);
	$return['document_lists'] = $lists;
	$query = "SELECT id, display_name FROM `employees` WHERE company_id = ".$get['company_id']." AND is_active = 0 ";
	$lists = $callConfig->getAllRows($query);
	$return['employees'] = $lists;

	return $return;
}
function togetgeneralcodedetails($get){
	global $callConfig;
	$whr = "";
	if(isset($get['genc_code']) && $get['genc_code']!=""){
		$whr = "AND genc_code ='".$get['genc_code']."'";
	}
	$query = "SELECT id, genc_code, genc_name FROM `general_codes`  WHERE company_id = '".$get['company_id']."'".$whr." AND is_active =0";
	$lists = $callConfig->getAllRows($query);
	$listcode = array();
	$query = "SELECT id, genc_code, genc_detail_code, genc_detail_name, genc_detail_name FROM general_code_details WHERE company_id = '".$get['company_id']."'".$whr." AND is_active =0";
	$list = $callConfig->getAllRows($query);
	foreach($list as $codelist){
		$listcode[$codelist->genc_code][] = $codelist;
	}
	$query = "SELECT * FROM application_roles WHERE company_id = '".$get['company_id']."'AND application_module = ".$get['work_module_setup_id']."  AND is_active =0";
	$roles = $callConfig->getAllRows($query);
	foreach((array)$roles as $role){
		$crole[] = "'".$role->role_short_code."'";
	}
	$query = "SELECT role_short_code FROM general_code_role_permissions WHERE company_id = '".$get['company_id']."'AND role_short_code IN ( ".implode(",",$crole).")  AND is_active =0"; 
	$role_permission = $callConfig->getAllRows($query);
	$mrole = "SELECT role_admin_short_code FROM module_setup WHERE module_id=".$get['work_module_setup_id'];
	$mroledata = $callConfig->getRow($mrole);
	foreach((array)$role_permission as $rpermission){
		$permission[] = $rpermission->role_short_code;
	}	
		$permission[] = $mroledata->role_admin_short_code;
	return array('codes'=>$lists, 'details'=>$listcode, 'roles'=>$roles, 'role_permission'=>$permission, 'role_admin'=>$rpermission->role_short_code ,'cquery'=>$query, 'mrole'=>$mrole);
}
function togetimplodesdetailsount($get){
	global $callConfig;
	//echo "raii"; exit;
	// $query = "SELECT count(id) cc , SUM(if(ref_id is NOT NULL,1,0)) rc FROM edi_data WHERE file_name = '".$get['file_name']."' and ref_application_table = '".$get['table']."'";
	 $query = "SELECT count(id) cc , count(id) rc FROM edi_data WHERE file_name = '".$get['file_name']."'";
	$return = $callConfig->getRow($query);
	return $return;
}
function toimportthedataintotablesemployeessalary($get){
	global $callConfig;
	//echo "<pre>";	print_r($get); exit;
	$whre = "is_implode=0 AND  file_name ='".$get['file_name']."' AND is_active=0";
	$pequery = $callConfig->selectQuery('import_mappings','*',$whre,'','','');
    //echo $pequery;  
	$pedata = $callConfig->getRow($pequery);
	//print_r($pedata); echo "data"; exit;
	if($pedata->id!=""){
		$maps = json_decode($pedata->filed_mapping);
		$maps = (array)$maps;
		$table = $pedata->application_table;
		foreach($maps as $filed=>$aps){
			
			if(strpos($aps,'ARN_')){
				$salmap[$filed] = $aps;
			}else{		
				$empmap[$filed] = $aps;
			}
		}
	}
	//echo "<pre>";	print_r($salmap); exit;
    $query = "SELECT * FROM earnings WHERE company_id = ".$get['company_id']." AND is_active=0"; 
	$earns = $callConfig->getAllRows($query);
	//echo "<pre>"; print_r($salmap); exit;
	foreach($earns as $earn){
		$key = "EARN_".$earn->code;
		$ear[$key] = (array)$earn;
		
	}
	$whre = "file_name ='".$get['file_name']."'";
	$dquery = $callConfig->selectQuery('edi_data','*',$whre,'','','');
    //echo $dquery; //exit;
	$checkddata = $callConfig->getAllRows($dquery);
	$i=1;
	$flip =array_flip($salmap);
	//echo "<pre>"; print_r($checkddata);
	$ctckey = $flip['EARN_CTC'];
	foreach($checkddata as $dd){
		$ctc = $dd->$ctckey;
		$crow=$callConfig->getRow("SELECT id FROM employees WHERE emp_code='".$dd->field2."'" );
		$emp_id = $crow->id;
		//print_r($crow); echo $emp_id; exit;
		$sal_array = array('company_id'=>$get['company_id'],'employee_id'=>$emp_id,'ctc_monthly'=>$ctc,'ctc_yearly'=>$ctc*12,'is_active'=>'0','current_status'=>0,'created_by'=>$get['user_id']);
		//echo "<pre>"; print_r($sal_array); // exit;
    	$salary_template = $callConfig->insertRecord('salary_template',$sal_array);
   		$salary_template_id = $salary_template['LASTID'];
   		$salayid[]=  $salary_template['LASTID'];
		//$salary_template_id = $i;
		foreach($salmap as $keycal=>$sal){
			$month_sal = $dd->$keycal!=""?$dd->$keycal:0;
			//print_r($crow); echo $emp_id; exit;
			$saltemp_array = array('company_id'=>$get['company_id'],'salary_template_id'=>$salary_template_id,'component_id'=>$ear[$sal]['id'],'component_name'=>$ear[$sal]['name'],'component_category'=>$ear[$sal]['category_id'],'component_cat_id'=>$ear[$sal]['category_id'],'component_number'=>$month_sal,'component_type'=>$ear[$sal]['calculation_type'],'monthly_amount'=>$month_sal,'yearly_amount'=>$month_sal*12,'is_active'=>'0','current_status'=>0,'created_by'=>$get['user_id']);
			//echo "<pre>"; print_r($saltemp_array);
			$insert_data = $callConfig->insertRecord('salary_template_components',$saltemp_array); 
			$saltemp_query[] = $saltemp_array;
		} 
		//print_r($crow); echo $emp_id; exit;
		//echo "<pre>"; print_r($saltemp_query); exit;
	}
	return $salayid;
}
	function savegenralcodedetails($post, $get){
		global $callConfig;
		//$post = json_decode($post['data'],1);
		$return['post'] = $post;
		//print_r($post); print_r($post['genc_detail_code']); echo $post['genc_detail_code']['0']; exit;
		$code = $post['code'];
		$upquery = "UPDATE general_code_details SET is_active = 1 WHERE genc_code = '".$post['code']."'";
		$return['upquery'] = $upquery;
		$updata = $callConfig->executeQuery($upquery);
		foreach($post['genc_detail_code'] as $key=>$genc_detail_code){
			$query = "SELECT id FROM general_code_details WHERE company_id =".$get['company_id']." AND genc_code='".$code."' AND genc_detail_code='".$genc_detail_code."' AND genc_detail_name ='".$post['genc_detail_name'][$key]."'";
			$data = $callConfig->getRow($query);
			$return['checkquery'][] = $query;
			$return['checkdata'][] = $data;
			if($data->id!=""){
				$upquery = "UPDATE general_code_details SET is_active = 0 WHERE id = '".$data->id."'";
				$return['next_level'][] = $upquery;
				$updata = $callConfig->executeQuery($upquery);
			}else{
				$insert_array = array('company_id'=>$get['company_id'],'genc_code'=>$code, 'genc_detail_code'=>$genc_detail_code, 'genc_detail_name'=>$post['genc_detail_name'][$key], 'description'=>$post['genc_detail_name'][$key], 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
				$return['next_level'][] = $upquery;
				$insert = $callConfig->insertRecord('general_code_details',$insert_array);
			}
		}
		//print_r($return);  exit;
		return $return;
	}
	function gnrolepermision($get){
		global $callConfig;
		$query = "SELECT id FROM general_code_role_permissions WHERE company_id =".$get['company_id']." AND genc_code='".$get['code']."' AND role_short_code='".$get['role']."' AND is_active =0";
		$data = $callConfig->getRow($query);
		$res['status'] = 'Y';
		if($data->id!=""){
			$upquery = "UPDATE general_code_role_permissions SET is_active = 1 WHERE id = '".$data->id."'";
			$return['next_level'][] = $upquery;
			$updata = $callConfig->executeQuery($upquery);
			$res['status'] = 'N';
		}else{
			$insert_array = array('company_id'=>$get['company_id'],'genc_code'=>$get['code'], 'role_short_code'=>$get['role'], 'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
			$return['next_level'][] = $upquery;
			$insert = $callConfig->insertRecord('general_code_role_permissions',$insert_array);
		}
		return $res;
	}
	function toinsertapplication_table_histories($get){
 		global $callConfig;
 		$return['get'] = $get;
 		$table_name = $get['table_name']; 	
 		$transaction_id = $get['transaction_id'];	
 		$type = $get['type'];
 		$dquery = "SELECT * FROM  dynamic_mapping_tables WHERE company_id = ".$get['company_id']." AND application_table_name= '".$table_name."' AND is_active =0";
 		$return['dynamic_query'] = $dquery;
 		$mapdata = $callConfig->getRow($dquery);
 		$return['dynamic_data'] = $mapdata;
 		if(count((array)$mapdata)>0){
 			$tquery = "SELECT * FROM  ".$table_name." WHERE company_id = ".$get['company_id']." AND id= ".$transaction_id;
	 		$return['tquery'] = $tquery;
	 		$tabledata = $callConfig->getRow($tquery);
	 		$fulldata['table_data'] = (array)$tabledata;
	 		if($mapdata->item_table !=""){
	 			$itquery = "SELECT * FROM  ".$mapdata->item_table." WHERE company_id = ".$get['company_id']." AND transaction_id= ".$transaction_id;
	 			$return['item_table_query'] = $itquery;
	 			$ittabledata = $callConfig->getAllRows($itquery);
	 			$fulldata['item_table_data'] = (array)$ittabledata;
	 		}
	 		if($mapdata->tax_table !=""){
	 			$taxquery = "SELECT * FROM  ".$mapdata->tax_table." WHERE company_id = ".$get['company_id']." AND transaction_id= ".$transaction_id;
	 			$return['tax_table_query'] = $taxquery;
	 			$taxtabledata = $callConfig->getAllRows($taxquery);
	 			$fulldata['tax_table_data'] = (array)$taxtabledata;
	 		}
	 		$jsondata = json_encode($fulldata,1);
 		}else{
 			$query = "SELECT * FROM  ".$table_name." WHERE company_id = ".$get['company_id']." AND id= ".$transaction_id;
	 		$return['query'] = $query;
	 		$data = $callConfig->getRow($query);
	 		$return['data'] = $data;
	 		$arraydata = (array)$data;
		  	$jsondata = json_encode($arraydata,1);
 		}

 		
	  	$return['jsondata'] = $jsondata;
	  	if($type =='insert'){
	  		$insert_array = array('company_id'=>$get['company_id'], 'application_table_name'=>$table_name, 'transaction_id' => $transaction_id, 'old_data'=>$jsondata, 'new_data'=>$jempdata, 'is_active'=>'0', 'current_status'=>'0', 'created_by'=>$get['user_id']);
 			$insert_res = $callConfig->insertRecord('application_table_histories',$insert_array);
	  	}
	  	if($type =='update'){
	  		$squery = "SELECT id FROM  application_table_histories  WHERE company_id = ".$get['company_id']." AND application_table_name= '".$table_name."' AND transaction_id =".$transaction_id." AND new_data IS NULL AND is_active=0";
	  		$return['squery'] = $squery;
	  		$aval  = $callConfig->getRow($squery); 	
	  		$return['aval'] = $aval;		
	  		$upquery =$callConfig->updateRecord('application_table_histories',array('new_data'=>$jsondata),'id',$aval->id);
	  	}
	  	//print_r($insert); echo "<br>"; exit;
 		return $return;
 	}
 	function togetjobtitleexploc($get){
 		global $callConfig;
 		$return['get'] = $get;
 		$jobtitle_query = "SELECT id,job_title FROM job_titles WHERE company_id = ".$get['company_id']." AND id IN (SELECT DISTINCT item_id FROM `vrc_items` WHERE company_id = ".$get['company_id']." AND is_active = 0) AND is_active = 0 ORDER BY id";
 		$return['jobtitle_query'] = $jobtitle_query;
 		$job_data = $callConfig->getAllRows($jobtitle_query);
 		foreach($job_data as $jobdata){
 			$job[$jobdata->id] = $jobdata;
 		}
	 	$return['job_data'] = $job;

	 	$ven_query = "SELECT id, display_name, location_id  FROM vendors_vms WHERE company_id = ".$get['company_id']."  AND is_active = 0";
 		$return['ven_query'] = $ven_query;
 		$ven_data = $callConfig->getAllRows($ven_query);
 		foreach($ven_data as $vendata){
 			$ven[$vendata->location_id][] = $vendata;
 		}
	 	$return['vendor_data'] = $ven;

	 	$loc_query = "SELECT id, location_name  FROM locations WHERE company_id = ".$get['company_id']."  AND is_active = 0";
 		$return['loc_query'] = $loc_query;
 		$loc_data = $callConfig->getAllRows($loc_query);
 		foreach($loc_data as $locdata){
 			$loc[$locdata->id] = $locdata->location_name;
 		}
	 	$return['location'] = $loc;

	 	$vrcquey = "SELECT id,customer_id FROM vendor_rate_cards WHERE company_id = ".$get['company_id']."  AND is_active = 0 ";
	 	$return['vrcquey'] = $vrcquey;
 		$vrc_data = $callConfig->getAllRows($vrcquey);
 		foreach($vrc_data as $vrcdata){
 			$vrc[$vrcdata->id] = $vrcdata->customer_id;
 		}
 		$return['vrcdata'] = $vrc;

	 	$data_query = "SELECT item_id, min_exp, max_exp, item_price, transaction_id FROM `vrc_items` WHERE company_id = ".$get['company_id']." AND item_id>0 AND min_exp IS NOT NULL AND is_active = 0 GROUP BY min_exp, max_exp, item_id ORDER BY item_id ASC";
	 	$return['data_query'] = $data_query;
 		$fulldata = $callConfig->getAllRows($data_query);
 		$return['fulldata'] = $fulldata;
 		foreach($fulldata as $full){
 			$exp = $full->min_exp."-".$full->max_exp;
 			$list_exp[$full->item_id][$exp] = $exp;
 			$data[$full->item_id][$exp][$vrc[$full->transaction_id]]=$full->item_price;
 		}
 		$return['exp'] = $list_exp;
	 	$return['data'] = $data;
 		return $return;
 	}
 	function togetjobtitleexp($get){
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
        transaction_number,
        purchase_uom,
        item_wise_price,
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
                FROM    vendors_vms vend 
                WHERE   vend.company_id = vrc.company_id
                AND     vend.id = vrc.customer_id
            	) vendor_name,
    			vrc.customer_id,
    			vrc.transaction_number,
            	vrcitm.min_exp, 
                vrcitm.max_exp,
                vrcitm.purchase_uom,
                vrcitm.item_price as item_wise_price, 
                CASE
        WHEN vrcitm.purchase_uom ='PH' THEN vrcitm.item_price*240
        WHEN vrcitm.purchase_uom ='PD' THEN vrcitm.item_price*30
        WHEN vrcitm.purchase_uom ='PW' THEN vrcitm.item_price*4
        WHEN vrcitm.purchase_uom ='PY' THEN vrcitm.item_price/12
        WHEN vrcitm.purchase_uom ='PS' THEN vrcitm.item_price/6
        WHEN vrcitm.purchase_uom ='PT' THEN vrcitm.item_price/4   
        ELSE vrcitm.item_price
    	END AS item_price, 
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
 			$data[$full->job_title_id][$exp][$full->vendor_id][]=$full;
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

	 	$ven_query = "SELECT id, display_name, location_id  FROM vendors_vms WHERE company_id = ".$get['company_id']."  AND is_active = 0";
 		$return['ven_query'] = $ven_query;
 		$ven_data = $callConfig->getAllRows($ven_query);
 		foreach($ven_data as $vendata){
 			$ven[$vendata->id] = $vendata->display_name;
 		}
	 	$return['vendor'] = $ven;
 		return $return;
 	}
 	function togetcarddetailsemp($get){
 		global $callConfig;
 		$return['get'] = $get;
 		$query ="SELECT vrc.transaction_number,
	   vrc.effective_from_date,
	   vrc.effective_to_date,
	   vrcitm.id vrcitm_vid,
	   vrcitm.item_name,
	   vrcitm.min_exp,
	   vrcitm.max_exp,
	   vrcitm.item_price,
	   ewo.effective_from_date ewo_effective_from_date,
	   ewo.effective_to_date ewo_effective_to_date,
	   emp.id emp_id,
	   emp.emp_code,
	   emp.display_name,
	   emp.career_start_date,
	   emp.total_experiance,
	   ven.id  vendor_id,
	   ven.display_name vendor_name,
       jb.id job_title_id,
       jb.job_title
		FROM  vendor_rate_cards vrc,
		vrc_items vrcitm
		, emp_work_order ewo, employees emp, vendors_vms ven, job_titles jb
		WHERE  vrc.company_id = vrcitm.company_id
		 AND vrc.company_id = ewo.company_id
	 	AND vrc.company_id = emp.company_id
		AND vrc.id = vrcitm.transaction_id
		AND vrcitm.id = ewo.vrc_item_id
		AND ewo.emp_id = emp.id
		AND vrc.customer_id = ven.id
	    AND vrcitm.item_id = jb.id
		AND vrc.company_id=".$get['company_id']."
	 	AND  vrc.id = ".$get['trn']."
	    AND vrcitm.item_id = ".$get['job_id']."
		AND vrcitm.min_exp = ".$get['min']."
		AND vrcitm.max_exp = ".$get['max']."
		AND vrcitm.is_active = 0";
		$return['fullquery'] = $query;
		$fulldata = $callConfig->getAllRows($query);
	 	$return['fulldata'] = $fulldata;
	 	if(count((array)$fulldata)>0){
	 	$wquery="SELECT vrc.transaction_number, vrc.effective_from_date, vrc.effective_to_date, vrcitm.id vrcitm_vid, vrcitm.item_name, vrcitm.min_exp, vrcitm.max_exp, vrcitm.item_price, ven.id vendor_id, ven.display_name vendor_name FROM vendor_rate_cards vrc, vrc_items vrcitm , vendors_vms ven WHERE vrc.company_id = vrcitm.company_id AND vrc.id = vrcitm.transaction_id AND vrc.customer_id = ven.id AND vrc.company_id=".$get['company_id']."
	 	AND  vrc.id = ".$get['trn']."
	    AND vrcitm.item_id = ".$get['job_id']."
		AND vrcitm.min_exp = ".$get['min']."
		AND vrcitm.max_exp = ".$get['max']."
		AND vrcitm.is_active = 0 ORDER BY vrcitm.item_price ASC";
		$return['wquery'] = $wquery;
		$rela_data = $callConfig->getAllRows($wquery);
	 	$return['rela_data'] = $rela_data;
	 	}else{
	 		$query ="SELECT vrc.transaction_number,
	   vrc.effective_from_date,
	   vrc.effective_to_date,
	   vrcitm.id vrcitm_vid,
	   vrcitm.item_name,
	   vrcitm.min_exp,
	   vrcitm.max_exp,
	   vrcitm.item_price,
	   emp.id emp_id,
	   emp.emp_code,
	   emp.display_name,
	   emp.career_start_date,
	   emp.total_experiance,
	   ven.id  vendor_id,
	   ven.display_name vendor_name,
       jb.id job_title_id,
       jb.job_title
		FROM  vendor_rate_cards vrc,
		vrc_items vrcitm, employees emp, vendors ven, job_titles jb
		WHERE  vrc.company_id = vrcitm.company_id
	 	AND vrc.company_id = emp.company_id
		AND vrc.id = vrcitm.transaction_id
		AND vrc.customer_id = ven.id
	    AND vrcitm.item_id = jb.id
		AND vrc.company_id=".$get['company_id']."
	 	AND  vrc.id = ".$get['trn']."
	    AND vrcitm.item_id = ".$get['job_id']."
		AND vrcitm.min_exp = ".$get['min']."
		AND vrcitm.max_exp = ".$get['max']."
		AND vrcitm.is_active = 0 LIMIT 0,1";
		$return['subquery'] = $query;
		$fulldata = $callConfig->getAllRows($query);
	 	$return['subdata'] = $fulldata;
	 	}
	 	return $return;
 	}
 	function leaveapplycreateemployee($post,$get){
 		global $callConfig;
 		foreach($post['leave'] as $leaveid=>$leave){
    		$inarray = array('company_id'=>$get['company_id'],'blance_year'=>date('Y'),'employee_id'=>$post['emp_id'],'leave_type'=>$leaveid,'applicable_leaves'=>$leave,'consumed_leaves'=>'0','unapproved_leaves'=>'0','weekend_leave_consume'=>'0','holiday_leave_consume'=>'0','leave_year'=>date('Y'),'is_active'=>'0','current_status'=>'0','created_date'=>date('Y-m-d h:i:s'),'created_by'=>$get['user_id']);
			$return[] = $callConfig->insertRecord('emp_leave_balances',$inarray);
		}
		return $return;
 	}
 	function toinsertapplicationpageprocessold($get){
 		global $callConfig;
 		$res = array('get'=>$get);
 		$table_array = array('Projects'=>'COM_VIEW_PROJECT_TOP_DASH_BOARD_DETAILS','Projects Tasks'=>'COM_VIEW_PROJECT_KANBAN','Project View'=>'COM_VIEW_PROJECT');

 		$process_id = 111;
 		foreach($table_array as $table=>$page){ 			
 			$query = "SELECT id, application_page_name FROM application_process_pages WHERE company_id = 1 AND  application_process_id = ".$process_id." AND application_page_name = '".$page."' AND  is_active = 0";
 			$res['query'][] = $query;
 			$data = $callConfig->getRow($query);
 			$res['query_data'][] = $data;
 			if($data->id==""){
 				$insert_array = array('company_id'=>1, 'application_process_id'=>$process_id, 'name'=>strtoupper( str_replace("_"," ",$table)), 'application_page_name'=>$page, 'is_active'=>'0', 'current_status'=>0, 'created_by'=>1, 'created_date'=>date('Y-m-d h:i:s'));
 				$res['insert_array'][] = $insert_array;
 				$insert = $callConfig->insertRecord('application_process_pages', $insert_array);
 				$res['insert_res'][] = $insert;
 			}

 		}
 		return $res;
 	}

 	function toinsertapplicationpageprocess($get){
 		global $callConfig;
 		$res = array('get'=>$get);
 		$query = "SELECT * FROM application_process WHERE  process_name='Reports' AND is_active = 0";
 		$data = $callConfig->getAllRows($query);

 		$query = "SELECT page_name, page_url FROM my_reports WHERE  page_url IS NOT NULL AND is_active = 0";
 		$myreports = $callConfig->getAllRows($query);

 		foreach($myreports as $myreport){
 			$table_array[$myreport->page_name] = $myreport->page_url;
 		}
 		
 		foreach($data as $dat){
 			$process_id = $dat->id;
	 		foreach($table_array as $table=>$page){ 			
	 			$query = "SELECT id, application_page_name FROM application_process_pages WHERE company_id = 1 AND  application_process_id = ".$process_id." AND application_page_name = '".$page."' AND  is_active = 0";
	 			$res['query'][] = $query;
	 			$data = $callConfig->getRow($query);
	 			$res['query_data'][] = $data;
	 			if($data->id==""){
	 				$insert_array = array('company_id'=>1, 'application_process_id'=>$process_id, 'name'=>strtoupper( str_replace("_"," ",$table)), 'application_page_name'=>$page, 'is_active'=>'0', 'current_status'=>0, 'created_by'=>1, 'created_date'=>date('Y-m-d h:i:s'));
	 				$res['insert_array'][] = $insert_array;
	 				$insert = $callConfig->insertRecord('application_process_pages', $insert_array);
	 				$res['insert_res'][] = $insert;
	 			}

	 		}
	 	}
 		return $res;
 	}
 	function togetanddifflisttablefields($get){
 		global $callConfig;
 		$db1 = 'infistaq_infisuite_2';
 		$db2 = 'infistaq_infisuite_live_vms';
 		$query = "SHOW TABLES FROM ".$db1;
 		$data = $callConfig->getAllRows($query);
 		$ff = "Tables_in_".$db1;
 		foreach($data as $dd){
 			$tabone[] = $dd->$ff;
 		}

		$query = "SHOW TABLES FROM ".$db2;
 		$data = $callConfig->getAllRows($query);
 		$ff = "Tables_in_".$db2;
 		foreach($data as $dd){
 			$tabtwo[] = $dd->$ff;
 		}
 		/*foreach($tabone as $tab){
 			if(!in_array($tab,(array)$tabtwo)){
 				$listone[] = $tab;
 			}
 		}
 		foreach($tabtwo as $tab){
 			if(!in_array($tab,(array)$tabone)){
 				$listtwo[] = $tab;
 			}
 		}*/
 		foreach($tabone as $tab){
 			if(in_array($tab,(array)$tabtwo)){
 				$onefileld = array();
 				$twofileld = array();
 				$fff = "DESCRIBE ".$db1.".".$tab;
 				$fdetails = $callConfig->getAllRows($fff);
 				foreach($fdetails as $ffield){
 					$onefileld[] = $ffield->Field;
 				}
 				$fff = "DESCRIBE ".$db2.".".$tab;
 				$fdetails = $callConfig->getAllRows($fff);
 				foreach($fdetails as $ffield){
 					$twofileld[] = $ffield->Field;
 				}
 				
 				foreach($onefileld as $fone){
 					if(!in_array($fone, (array)$twofileld)){
 						$tablefiled[$tab]['inf_extra'][]=$fone;
 					}
 					$checktables[$tab]['filed'][] = $fone;
 					
 				}
 				foreach($twofileld as $fone){
 					if(!in_array($fone, (array)$onefileld)){
 						$tablefiled[$tab]['vms_extra'][]=$fone;
 					}
 				}
 				
 				//$checktables[] = $tab;
 			}
 		}
 		//echo "<pre>---------".$tab; print_r($checktables); exit;
 		//print_r($tabone);
 		echo "<table>";
 		foreach($tablefiled as $key=>$table){
 			if(count((array)$table['vms_extra'])>0){
 			echo "<tr>";
 			echo "<td>";
 			echo  $key; //print_r($table);
 			echo "</td><td></td>";
 			echo "</tr>";
 			foreach($table['vms_extra'] as $inf_extra){
 			echo "<tr> <td></td>";
 			echo "<td >";
 			echo  $inf_extra;
 			echo "</td>";
 			echo "</tr>";
 			}
 			}
 		}
 		echo "</table>"; exit;
 		return array('dbone'=>$tabone,'dbtwo'=>$tabtwo, 'listone'=>$listone, 'listtwo'=>$listtwo, 'checktables'=>$checktables, 'tablefiled'=> $tablefiled);
 	}
 	function togetemployeeprofessionaltaxexemptions($post,$get){
		global $callConfig;
		$return['menu_data'] = $this->toreportempdropdown($get);
		$whr = "";
		$whrarry = array('department_id'=>'departments', 'designation_id'=>'designations', 'location_id'=>'locations', 'employee_type'=>'employment_statuses', 'division_id'=>'divisions');
		foreach($whrarry  as $wrkey=>$whra){
			if(isset($post[$whra]) && count($post[$whra])>0){
				$whr.= " AND emp.".$wrkey." IN(".implode(",",$post[$whra]).") ";
			}
		}
		$query = "SELECT * FROM professional_tax WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$data =  $callConfig->getAllRows($query);
		$return['professional_tax'] = $data;
		foreach($data as $data_pt){
			$stateids[] = $data_pt->state_id;
			$loctionids[] = $data_pt->work_location_id;
		}

		
		$query = "SELECT * FROM professional_tax_exemptions WHERE company_id = ".$get['company_id']." AND is_active = 0";
		$ptexdata =  $callConfig->getAllRows($query);
		$return['ptexdata'] = $ptexdata;

		$query = "SELECT id, state_name FROM states WHERE company_id = ".$get['company_id']." AND id IN (".implode(",",$stateids).") AND is_active = 0";
		$states =  $callConfig->getAllRows($query);
		foreach($states as $state){
			$list_state[$state->id] = $state->state_name;
		}
		$return['states'] = $list_state;
		$query = "SELECT id, location_name FROM locations WHERE company_id = ".$get['company_id']." AND id IN (".implode(",",$loctionids).") AND is_active = 0";
		$locations =  $callConfig->getAllRows($query);
		foreach($locations as $location){
			$list_location[$location->id] = $location->location_name;
		}
		$return['locations'] = $list_location;
		foreach($ptexdata as $ptex){
			$whrs = "";
			if($ptex->range_type=='yes_no'){
				$whrs.= " AND emp.".$ptex->employee_field_name." = 0";
			}
			if($ptex->range_type=='age'){
				$whrs.= " AND emp.".$ptex->employee_field_name." < DATE_SUB(CURDATE(), INTERVAL ".$ptex->minmum_range." YEAR)";
			}
			if($ptex->range_type=='percentage'){
				$whrs.= " AND emp.".$ptex->employee_field_name." > ".$ptex->minmum_range;
			}
			$query = "SELECT emp.id, emp.display_name, emp.image, emp.emp_code, emp.location_id, emp.joining_date FROM employees emp WHERE emp.company_id = ".$get['company_id']." ".$whrs." AND is_active = 0".$whr;
			$list_query[] = $query;
			$emp_data =  $callConfig->getAllRows($query);
			foreach($emp_data as $empdata){
				$list[$ptex->id][$empdata->location_id][] = $empdata;
			}
		}


		$return['list'] = $list;
		$return['list_query'] = $list_query; 
		/*$query = "SELECT emp.id, emp.emp_code, emp.display_name, emp.image, emp.date_of_birth, emp.wedding_date FROM employees emp WHERE emp.company_id = ".$get['company_id']."  AND ( CASE WHEN ( emp.date_of_leaving IS NOT NULL AND emp.date_of_leaving >= now() ) THEN 1 WHEN ( emp.date_of_leaving IS NULL AND emp.is_active = 0 ) THEN 1 ELSE 2 END )= 1 ".$whr." AND emp.is_active = 0";
		$return['emp_query'] = $query;
		$return['emp_data'] = $callConfig->getAllRows($query);*/
  		return  $return;
 	}
 	//Omkar Code added - 18-06-2025
 	function getmoduleprocesslist($get){
		global $callConfig;
		$return['get'] = $get;
		$query = "SELECT id, module_id, module_name FROM module_setup WHERE company_id = '" . $get['company_id'] . "' AND linking = 1 AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['module_setup'] = $data;

		$query = "SELECT id, module_setup_id, process_name FROM application_process WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['application_process'] = $data;

		$query = "SELECT id, application_process_id, name, application_page_name FROM application_process_pages WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['application_process_pages'] = $data;

		$query = "SELECT id, list_page_name FROM application_pages WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['application_pages'] = $data;

		return $return;
	}
	function insertmoduleprocess($post ,$get){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$module_id = $post['module_id'];
		$processes = $post['processes'];
		if (empty($module_id)) {
			return array('status' => 'error', 'message' => 'Module ID is required');
		}
		if (empty($processes)) {
			return array('status' => 'error', 'message' => 'No processes provided');
		}
		$nullProcessData = array(
			'is_active' => null,
			'updated_by' => $get['user_id'],
			'updated_date' => date('Y-m-d H:i:s')
		);
		$whrProcess = "module_setup_id = '" . $module_id . "' AND company_id = '" . $get['company_id'] . "'";
		$nullProcessResult = $callConfig->updateRecord('application_process', $nullProcessData, '', '', $whrProcess);
		$return['null_all_processes'] = $nullProcessResult;
		$allProcessQuery = "SELECT id FROM application_process WHERE module_setup_id = '" . $module_id . "' AND company_id = '" . $get['company_id'] . "'";
		$allProcessResult = $callConfig->getAllRows($allProcessQuery);
		$allProcessIds = array();
		foreach ($allProcessResult as $proc) {
			$allProcessIds[] = $proc->id;
		}
		if (!empty($allProcessIds)) {
			$nullPageData = array(
				'is_active' => null,
				'updated_by' => $get['user_id'],
				'updated_date' => date('Y-m-d H:i:s')
			);
			$whrPages = "application_process_id IN ('" . implode("','", $allProcessIds) . "') AND company_id = '" . $get['company_id'] . "'";
			$nullPageResult = $callConfig->updateRecord('application_process_pages', $nullPageData, '', '', $whrPages);
			$return['null_all_pages'] = $nullPageResult;
		}

		$existingProcessesQuery = "SELECT id FROM application_process WHERE module_setup_id = '" . $module_id . "' AND company_id = '" . $get['company_id'] . "'";
		$existingProcessesResult = $callConfig->getAllRows($existingProcessesQuery);
		$existingProcessIds = array();
		foreach ($existingProcessesResult as $proc) {
			$existingProcessIds[] = $proc->id;
		}

		$submittedProcessIds = array();
		foreach ($processes as $process) {
			if (isset($process['id']) && !empty($process['id'])) {
				$submittedProcessIds[] = $process['id'];
			}
		}
		$processed_processes = array();
		$processed_pages = array();
		foreach ($processes as $process) {
			$process_name = $process['name'];
			$process_id = isset($process['id']) ? $process['id'] : '';
			if (empty($process_name)) {
				continue;
			}
			$processData = array(
				'process_name' => $process_name,
				'module_setup_id' => $module_id,
				'company_id' => $get['company_id'],
				'is_active' => 0
			);
			if (!empty($process_id)) {
				$processData['updated_by'] = $get['user_id'];
				$processData['updated_date'] = date('Y-m-d H:i:s');
				$return['processData'][] = $processData;
				$processResult = $callConfig->updateRecord('application_process', $processData, 'id', $process_id);
				$current_process_id = $process_id;
				$return['process_updated'][] = 'Update';
				$action = 'update';
			} else {
				$processData['created_by'] = $get['user_id'];
				$processData['created_date'] = date('Y-m-d H:i:s');
				$processData['current_status'] = 0;
				$return['processData'] = $processData;
				$processResult = $callConfig->insertRecord('application_process', $processData);
				$current_process_id = $processResult['LASTID'];
				$return['process_insert'][] = 'insert';
				$action = 'insert';
			}

			$processed_processes[] = array(
				'id' => $current_process_id,
				'name' => $process_name,
				'action' => $action,
				'result' => $processResult
			);

			$existingPagesQuery = "SELECT id FROM application_process_pages WHERE application_process_id = '" . $current_process_id . "' AND company_id = '" . $get['company_id'] . "'";
			$existingPagesResult = $callConfig->getAllRows($existingPagesQuery);
			$existingPageIds = array();
			foreach ($existingPagesResult as $page) {
				$existingPageIds[] = $page->id;
			}

			$submittedPageIds = array();
			if (isset($process['pages']) && !empty($process['pages'])) {
				foreach ($process['pages'] as $page) {
					if (isset($page['id']) && !empty($page['id'])) {
						$submittedPageIds[] = $page['id'];
					}
				}
			}

			if (isset($process['pages']) && !empty($process['pages'])) {
				$pages = $process['pages'];

				// Now process each page
				foreach ($pages as $page) {
					$page_name = $page['name'];
					$page_id = isset($page['id']) ? $page['id'] : '';
					$page_type = isset($page['type']) ? $page['type'] : '';
					$page_custom = isset($page['custom']) ? $page['custom'] : '';

					if (empty($page_name)) {
						continue;
					}

					$pageData = array(
						'application_process_id' => $current_process_id,
						'name' => $page_name,
						'application_page_name' => ($page_type === 'other' && !empty($page_custom)) ? $page_custom : $page_type,
						'company_id' => $get['company_id'],
						'is_active' => 0
					);


					if (isset($page_id) && $page_id !== '' && $page_id !== '0') {
						$pageData['updated_by'] = $get['user_id'];
						$pageData['updated_date'] = date('Y-m-d H:i:s');
						$return['pageData'][] = $pageData;
						$pageResult = $callConfig->updateRecord('application_process_pages', $pageData, 'id', $page_id);
						$page_action = 'update';
					} else {
						$pageData['created_by'] = $get['user_id'];
						$pageData['created_date'] = date('Y-m-d H:i:s');
						$pageData['current_status'] = 0;
						$return['pageData'][] = $pageData;
						$pageResult = $callConfig->insertRecord('application_process_pages', $pageData);
						$page_action = 'insert';
					}

					$processed_pages[] = array(
						'id' => !empty($page_id) ? $page_id : $pageResult['LASTID'],
						'name' => $page_name,
						'process_id' => $current_process_id,
						'action' => $page_action,
						'result' => $pageResult
					);
				}
			} else {

				if (!empty($existingPageIds)) {
					$activatePageData = array(
						'is_active' => 0,
						'updated_by' => $get['user_id'],
						'updated_date' => date('Y-m-d H:i:s')
					);
					$whrExistingPages = "application_process_id = '" . $current_process_id . "' AND company_id = '" . $get['company_id'] . "'";
					$activatePageResult = $callConfig->updateRecord('application_process_pages', $activatePageData, '', '', $whrExistingPages);
					$return['activate_existing_pages'][] = array(
						'process_id' => $current_process_id,
						'result' => $activatePageResult
					);
				}
			}
		}

		$return['status'] = 'success';
		$return['message'] = 'Module processes and pages saved successfully';
		$return['module_id'] = $module_id;
		$return['processes'] = $processed_processes;
		$return['pages'] = $processed_pages;
		$return['pagedeta'][] = $pageData;	

		return $return;
	}
	function dateconvertdbformat($date){
		$dateformat = "";
		if($date !=""){
			$datesplit = str_replace('/"', '-',$date);
			$dateformat =  date('Y-m-d', strtotime($datesplit));
		}
		return $dateformat;   
	}

	function generatingApplicationSerialNumberMasters($transaction_table,$get){
		global $callConfig;
		$return = array();
		$transaction_date = $get['transaction_date'];
		$party_id =  $get['party_id'];
		$transaction_type =  $get['transaction_type'];

		$asnsql = "SELECT * FROM application_serial_number_masters WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND is_active = 0";
		$asnres = $callConfig->getRow($asnsql);
		
		if($asnres->id !=""){
			$main_transaction_no_type = $asnres->reset_type;
			$serial_number_position = $asnres->serial_number_position;
			$financial_year_position = $asnres->financial_year_position;
			$numLength   = !empty($asnres->doc_no_length) ? (int)$asnres->doc_no_length : 3;
			    	
			$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND field_name = 'party_id'";	
			$parttable_name = $callConfig->getRow($display_attribute_typessql);
			$qquery = urldecode($parttable_name->content);
			$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
			if(isset($explosql[1])){
				$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
			}
			$partytable = trim($explosql[0]); //get Table name

			$checksequincerow = array();
			$transdate = $transaction_date !=""?$this->dateconvertdbformat($transaction_date):date('Y-m-d');
			$finyaer = date("m",strtotime($transdate)) >= 4 ? date("Y",strtotime($transdate)). '-' . (date("y",strtotime($transdate))+1) : (date("Y",strtotime($transdate)) - 1). '-' . date("y",strtotime($transdate));
			
	    	$additional_settings = json_decode($asnres->additional_settings,1);
	    	$position_with_val = array(); 
	    	foreach($additional_settings as $adsetting){
	    		$position_with_val[$adsetting['positions']] = $adsetting; 
	    	}
	    	
	    	$parts = [];
		    for ($m = 1; $m <= 10; $m++) {
		    	if (!empty($asnres->serial_number) && $serial_number_position == $m){
		            $parts[$m] = $serial;
		        }
		        if (!empty($finyaer) && $asnres->financial_year_check == "Y" && $financial_year_position == $m){
		            $parts[$m] = $finyaer;
		            //$checksequincerow[$m] = $finyaer;
		        }
		        if(isset($position_with_val[$m]) AND $position_with_val[$m]['display_type'] =="STATIC"){
		            if($position_with_val[$m]['static_value'] != ""){
		            	//$parts[$m] = $position_with_val[$m]['static_value'];
		            	$checksequincerow[$m] = $position_with_val[$m]['static_value'];
		            }
		        }elseif(isset($position_with_val[$m]) AND $position_with_val[$m]['display_type'] =="DYNAMIC"){
		            if($position_with_val[$m]['dynamic_value'] != ""){
		            	//$parts[$m] = $position_with_val[$m]['dynamic_value'];
		            	$checksequincerow[$m] = $position_with_val[$m]['dynamic_value'];
		            }
		        }
			}

			$insertwithconditionary = array();
		    $additional_conditions = "";
		    foreach($checksequincerow as $seq_key=>$seq_val){

		    	if($seq_val == "PARTY_NAME"){

					if($partytable !="" AND $party_id !=""){
						$partysql = "SELECT id, short_name FROM ".$partytable." WHERE company_id = ".$get['company_id']." AND id = ".$party_id." AND is_active = 0";
						$partyres = $callConfig->getRow($partysql);
						if($partyres->short_name !=""){
							$additional_conditions .= " AND position_".$seq_key." = '".$partyres->short_name."'";
							$insertwithconditionary["position_".$seq_key] = $partyres->short_name;
							$parts[$seq_key] = $partyres->short_name;
						}else{
							$additional_conditions .= " AND position_".$seq_key." = '".$seq_val."'";
							$insertwithconditionary["position_".$seq_key] = $seq_val;
							$parts[$seq_key] = $seq_val;
						}
					}else{
						$additional_conditions .= " AND position_".$seq_key." = '".$seq_val."'";
						$insertwithconditionary["position_".$seq_key] = $seq_val;
						$parts[$seq_key] = $seq_val;
					}
		    	
		    	}elseif($seq_val == "TRANSACTION_TYPE"){
		    		if($transaction_type !=""){
		    			$additional_conditions .= " AND position_".$seq_key." = '".$transaction_type."'";
		    			$insertwithconditionary["position_".$seq_key] = $transaction_type;
		    			$parts[$seq_key] = $transaction_type;
		    		}else{
		    			$additional_conditions .= " AND position_".$seq_key." = '".$seq_val."'";
		    			$insertwithconditionary["position_".$seq_key] = $seq_val;
		    			$parts[$seq_key] = $seq_val;
		    		}

		    	}elseif($seq_val == "TRANSACTION_LOCATION"){

		    		$locationsql = "SELECT prty.id, loc.short_name FROM ".$partytable." as prty, locations as loc WHERE prty.company_id = ".$get['company_id']." AND prty.company_id = loc.company_id AND prty.location_id = loc.id AND prty.is_active = loc.is_active AND prty.id = ".$party_id." AND prty.is_active = 0";
					$locationres = $callConfig->getRow($locationsql);
					if($locationres->short_name){
						$additional_conditions .= " AND position_".$seq_key." = '".$locationres->short_name."'";
						$insertwithconditionary["position_".$seq_key] = $locationres->short_name;
						$parts[$seq_key] = $locationres->short_name;
					}else{
						$additional_conditions .= " AND position_".$seq_key." = '".$seq_val."'";
						$insertwithconditionary["position_".$seq_key] = $seq_val;
						$parts[$seq_key] = $seq_val;
					}
		    	}else{
		    		$additional_conditions .= " AND position_".$seq_key." = '".$seq_val."'";
		    		$insertwithconditionary["position_".$seq_key] = $seq_val;
		    		$parts[$seq_key] = $seq_val;
		    	}
		    }	

		    ksort($parts);

			$monthlysql = "SELECT * FROM application_serial_number_master_details WHERE company_id = '".$get['company_id']."' AND application_serial_number_master_id = '".$asnres->id."' AND serial_number_reset_type = 'MT' AND from_date <= '".$this->dateconvertdbformat($transaction_date)."' AND to_date >= '".$this->dateconvertdbformat($transaction_date)."' AND is_active = 0".$additional_conditions;
			$monthlyres = $callConfig->getRow($monthlysql);

			if($monthlyres->id !=""){
					$serial_number = $monthlyres->serial_number+1;

					
					$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
					$parts[$serial_number_position] = $serial;
					$callConfig->updateRecord('application_serial_number_master_details',array('serial_number' =>$serial_number),'id',$monthlyres->id);
			}else{

				$from_date = date("Y-m-01",strtotime($transaction_date));
				$to_date = date("Y-m-t",strtotime($transaction_date));

				$serial_number = 1;
				$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
				$parts[$serial_number_position] = $serial;
				$insertary = array('company_id'=>$get['company_id'],'application_serial_number_master_id'=>$asnres->id, 'serial_number_reset_type'=>'MT','from_date'=>$from_date,'to_date'=>$to_date,'serial_number'=>1,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'current_status'=>0, 'is_active'=>0);
				foreach($insertwithconditionary as $fieldkey=>$filedval){
					$insertary[$fieldkey] = $filedval;	
				}
				$return['res'] = $callConfig->insertRecord('application_serial_number_master_details', $insertary);

			}

			$parts[$financial_year_position] = date("M-Y",strtotime($transaction_date));
			$monthly_transaction_number = trim(implode($asnres->separator_symbols, (array)$parts));
			$yearlysql = "SELECT * FROM application_serial_number_master_details WHERE company_id = '".$get['company_id']."' AND application_serial_number_master_id = '".$asnres->id."' AND serial_number_reset_type = 'YR' AND from_date <= '".$this->dateconvertdbformat($transaction_date)."' AND to_date >= '".$this->dateconvertdbformat($transaction_date)."' AND is_active = 0".$additional_conditions;
			$yearlyres = $callConfig->getRow($yearlysql);
			
			if(date("m",strtotime($transaction_date)) >= 4){
                $startyear = date("Y",strtotime($transaction_date));
                $endyear = (date("Y",strtotime($transaction_date))+1);
                $from_date = $startyear."-04-01";
                $to_date = $endyear."-03-31";
            }else{
                $startyear = (date("Y",strtotime($transaction_date)) - 1);
                $endyear = date("Y",strtotime($transaction_date));
                $from_date = $startyear."-04-01";
                $to_date = $endyear."-03-31";
            }

			if($yearlyres->id !=""){
				$serial_number = $yearlyres->serial_number+1;
				$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
				$parts[$serial_number_position] = $serial;
				$callConfig->updateRecord('application_serial_number_master_details',array('serial_number' =>$serial_number),'id',$yearlyres->id);
			}else{
                $serial_number = 1;
				$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
				$parts[$serial_number_position] = $serial;
				$insertary = array('company_id'=>$get['company_id'],'application_serial_number_master_id'=>$asnres->id, 'serial_number_reset_type'=>'YR','from_date'=>$from_date,'to_date'=>$to_date,'serial_number'=>1,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'current_status'=>0, 'is_active'=>0);
				foreach($insertwithconditionary as $fieldkey=>$filedval){
					$insertary[$fieldkey] = $filedval;	
				}
				$return['res'] = $callConfig->insertRecord('application_serial_number_master_details', $insertary);

			}
			$parts[$financial_year_position] = date("Y",strtotime($from_date))."-".date("y",strtotime($to_date))+1;
			$yearly_transaction_number = trim(implode($asnres->separator_symbols, (array)$parts));
			$lifetimesql = "SELECT * FROM application_serial_number_master_details WHERE company_id = '".$get['company_id']."' AND application_serial_number_master_id = '".$asnres->id."' AND serial_number_reset_type = 'LT' AND from_date <= '".$this->dateconvertdbformat($transaction_date)."' AND to_date >= '".$this->dateconvertdbformat($transaction_date)."' AND is_active = 0".$additional_conditions;
			$lifetimeres = $callConfig->getRow($lifetimesql);

			if($lifetimeres->id !=""){
				$serial_number = $lifetimeres->serial_number+1;
				$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
				$parts[$serial_number_position] = $serial;
				$callConfig->updateRecord('application_serial_number_master_details',array('serial_number' =>$serial_number),'id',$lifetimeres->id);
			}else{

			    $from_date = "2020-04-01";
                $to_date = "3020-03-31";
            	$serial_number = 1;
				$serial = str_pad((string)$serial_number, $numLength, '0', STR_PAD_LEFT);
				$parts[$serial_number_position] = $serial;
				$insertary = array('company_id'=>$get['company_id'],'application_serial_number_master_id'=>$asnres->id, 'serial_number_reset_type'=>'LT','from_date'=>$from_date,'to_date'=>$to_date,'serial_number'=>1,'created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'current_status'=>0, 'is_active'=>0);
				foreach($insertwithconditionary as $fieldkey=>$filedval){
					$insertary[$fieldkey] = $filedval;	
				}
				$return['res'] = $callConfig->insertRecord('application_serial_number_master_details', $insertary);
			}
			$parts[$financial_year_position] = date("Y",strtotime($transaction_date));
			$lifetime_transaction_number = trim(implode($asnres->separator_symbols, (array)$parts));
		}
		$main_transaction_number = rand();
		$monthly_transaction_number = $monthly_transaction_number !=""?$monthly_transaction_number:rand();
		$yearly_transaction_number = $yearly_transaction_number !=""?$yearly_transaction_number:rand();
		$lifetime_transaction_number = $lifetime_transaction_number !=""?$lifetime_transaction_number:rand();
		if($main_transaction_no_type=="MT"){
			$main_transaction_number = $monthly_transaction_number;
		}elseif($main_transaction_no_type=="YR"){
			$main_transaction_number = $yearly_transaction_number;
		}elseif($main_transaction_no_type=="LT"){
			$main_transaction_number = $lifetime_transaction_number;
		}
		$returnres = array('main_transaction_number'=>$main_transaction_number,'monthly_transaction_number'=>$monthly_transaction_number, 'yearly_transaction_number'=>$yearly_transaction_number, 'lifetime_transaction_number'=>$lifetime_transaction_number);	
		return 	$returnres;		
	}

	function getApplicationSerialNumbers($get){
		global $callConfig;
		$return = array();
		//echo "sdvdvdfv";
		if($get['process_name'] != ""){
			// $asnsql = "SELECT id, application_table_name, from_date, to_date FROM application_serial_number_masters WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$get['process_name']."' AND is_active = 0";
			// $asnres = $callConfig->getAllRows($asnsql);
			// $return['application_serial_numbers_list'] = $asnres;

			//if($get['rid'] != ""){
				$asnsql = "SELECT * FROM application_serial_number_masters WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$get['process_name']."' AND is_active = 0";
				$asnres = $callConfig->getRow($asnsql);
				$return['application_serial_numbers'] = $asnres;
			//}
		}elseif($get['checknumber'] == "Y"){
			
			//application_serial_number_master_details

			
			$get['transaction_date'] = '2025-05-10';
			$get['party_id'] = '1';
			$get['transaction_type'] = 'R';
			$transaction_number_details = $this->generatingApplicationSerialNumberMasters('purchase_orders',$get);
			//$return['application_serial_numbers'] = $asnres;


			echo "<pre>"; print_r($transaction_number_details);

		}else{
			$apagessql = "SELECT id, application_table_name FROM dynamic_mapping_tables WHERE company_id = '".$get['company_id']."' AND is_active = 0 GROUP BY application_table_name";
			$apagesres = $callConfig->getAllRows($apagessql);
			$return['dynamic_mapping_tables'] = $apagesres;
		}
		return $return;
	}
	/**function insertApplicationSerialNumbers($post, $get){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		$reset_every_financial_year = $post['reset_every_financial_year'] =="Y"?"Y":"N";
		$pre_fix_check = $post['pre_fix_check'] =="Y"?"Y":"N";
		$post_fix_check = $post['post_fix_check'] =="Y"?"Y":"N";
		$financial_year_check = $post['financial_year_check'] =="Y"?"Y":"N";

		$pre_fix_field = $post['pre_fix_type'] == "DYNAMIC"?$post['pre_fix_field']:NULL;
		$post_fix_field = $post['post_fix_type'] == "DYNAMIC"?$post['post_fix_field']:NULL;
		$post_fix = $post['post_fix_type'] == "STATIC"?$post['post_fix']:NULL;
		$pre_fix = $post['pre_fix_type'] == "STATIC"?$post['pre_fix']:NULL;
		
		$from_date = date($post['financial_year']."-04-01");
		$to_date = date(($post['financial_year']+1)."-03-31");	
		$insertary = array('company_id'=>$get['company_id'],'application_table_name'=>$post['process_name'],'serial_number_position'=>$post['serial_number_position'],'pre_fix'=>$pre_fix,'pre_fix_position'=>$post['pre_fix_position'],'post_fix'=>$post_fix,'post_fix_position'=>$post['post_fix_position'],'doc_no_length'=>$post['doc_no_length'],'financial_year'=>$post['financial_year'],'financial_year_position'=>$post['financial_year_position'],'reset_every_financial_year'=>$reset_every_financial_year,'separator_symbols'=>$post['separator_symbols'],'pre_fix_check'=>$pre_fix_check,'post_fix_check'=>$post_fix_check,'financial_year_check'=>$financial_year_check,'pre_fix_type'=>$post['pre_fix_type'],'post_fix_type'=>$post['post_fix_type'],'pre_fix_field'=>$pre_fix_field,'post_fix_field'=>$post_fix_field,'from_date'=>$from_date,'to_date'=>$to_date);

		if($post['rid'] !=""){
			$insertary['updated_by'] = $get['user_id'];
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$return['res'] = $callConfig->updateRecord('application_serial_numbers', $insertary, 'id', $post['rid']);
		}else{
			$insertary['created_by'] = $get['user_id'];
			$insertary['created_date'] = date('Y-m-d H:i:s');
			$insertary['current_status'] = 0;
			$insertary['is_active'] = 0;
			$insertary['serial_number'] = $post['serial_number'];
			$return['res'] = $callConfig->insertRecord('application_serial_numbers', $insertary);
		}
		return $return;

	}**/
	function insertApplicationSerialNumbers($post, $get){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		//$reset_every_financial_year = $post['reset_every_financial_year'] =="Y"?"Y":"N";
		//$pre_fix_check = $post['pre_fix_check'] =="Y"?"Y":"N";
		//$post_fix_check = $post['post_fix_check'] =="Y"?"Y":"N";
		$financial_year_check = $post['financial_year_check'] =="Y"?"Y":"N";

		$additional_settings1 = array();
		foreach($post['additional_settings']['positions'] as $seq=>$vaul){
			$additional_settingsss = array();
			foreach($post['additional_settings'] as $fileds=>$bs){
				$additional_settingsss[$fileds] = $post['additional_settings'][$fileds][$seq];
			}
			$additional_settings1[] = $additional_settingsss;
		}

		//$additional_settings = json_encode($post['additional_settings']);
		$additional_settings = json_encode($additional_settings1);
		//$pre_fix_field = $post['pre_fix_type'] == "DYNAMIC"?$post['pre_fix_field']:NULL;
		//$post_fix_field = $post['post_fix_type'] == "DYNAMIC"?$post['post_fix_field']:NULL;
		//$post_fix = $post['post_fix_type'] == "STATIC"?$post['post_fix']:NULL;
		//$pre_fix = $post['pre_fix_type'] == "STATIC"?$post['pre_fix']:NULL;
		




		$insertary = array('company_id'=>$get['company_id'],'application_table_name'=>$post['process_name'],'doc_no_length'=>$post['doc_no_length'],'separator_symbols'=>$post['separator_symbols'],'serial_number'=>$post['serial_number'],'serial_number_position'=>$post['serial_number_position'] ,'financial_year_check'=>$financial_year_check, 'financial_year_position'=>$post['financial_year_position'],'additional_settings'=>$additional_settings,'reset_type'=>$post['reset_type']);


		if($post['rid'] !=""){
			$insertary['updated_by'] = $get['user_id'];
			$insertary['updated_date'] = date('Y-m-d H:i:s');
			$return['res'] = $callConfig->updateRecord('application_serial_number_masters', $insertary, 'id', $post['rid']);
		}else{
			$insertary['created_by'] = $get['user_id'];
			$insertary['created_date'] = date('Y-m-d H:i:s');
			$insertary['current_status'] = 0;
			$insertary['is_active'] = 0;
			$insertary['serial_number'] = $post['serial_number'];
			$return['res'] = $callConfig->insertRecord('application_serial_number_masters', $insertary);
		}
		return $return;

	}
	function tobeforeaddmeetingrommbook($get){
		global $callConfig;
        $return = array();
        $query ="SELECT id,cabin_name FROM building_floor_spaces WHERE company_id = {$get['company_id']} AND space_type_id IN (SELECT id FROM space_types WHERE company_id =  {$get['company_id']} AND short_code = 'METTING_ROOM' AND is_active =0)";
        $data = $callConfig->getAllRows($query);
        $cabin_res = array();
        foreach ($data as $row) {
            $cabin_res[$row->id] = $row->cabin_name;
        }
        $return['cabin_res'] = $cabin_res;
 
        if(isset($get['id']) && $get['id'] != ''){
            $query = "SELECT id, building_floor_space_id, booking_date, booking_start_time, booking_end_time, request_reason FROM meeting_room_bookings
                WHERE  id= {$get['id']} AND  company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['meeting_rooms_res'] = $data;
        }
        return $return;
	}

	function getTrevllingPlanData_old($get){
        global $callConfig;
        $return = array();
        $cities_arr = array();
        if(isset($get['id']) && $get['id'] != ''){
            $query = "SELECT id,  `trip_name`, transaction_no,  transaction_date, `trip_type`, `trip_purpose`, `project_id`, `customer_id`, `budget_amount`, `final_amount` FROM trips  WHERE  id={$get['id']} AND  company_id={$get['company_id']} AND is_active=0"; 
            $data = $callConfig->getRow($query);
            $return['trip'] = $data;

            $query ="SELECT id, `trip_id`, `journey_type`, `user_id`, `trip_purpose`, `origin_location`, `destination_location`, `departure_date`, departure_time,`return_date`, return_time,`travel_mode`, `estimated_cost` FROM domestic_round_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            foreach ($data as $row) {
                    if (!empty($row->origin_location))      $cities_arr[] = (int)$row->origin_location;
                    if (!empty($row->destination_location)) $cities_arr[] = (int)$row->destination_location;
                }
            $return['domestic_round_trips'] = $data;

            $query ="SELECT id,`trip_id`, `hotel_name`, `check_in`, `check_out`, `location`, `room_type`, `booking_reference`, `cost` , description FROM trip_hotels WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
            $data = $callConfig->getAllRows($query);
            foreach ($data as $row) {
                if (!empty($row->location)) $cities_arr[] = (int)$row->location;
            }
            $return['trip_hotels'] = $data;

            $query ="SELECT id, `trip_id`, `user_id`, `trip_purpose`, `trip_date`, `from_location`, `to_location`, `travel_mode`, `estimated_cost`, `pickup_at`, `dropoff_at`, `car_type`, `driver_needed` FROM local_trips WHERE company_id={$get['company_id']} AND  trip_id={$get['id']} AND is_active =0";
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

        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_LOCAL_VEHICLE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['local_modes'] = $data;

        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_VEHICL' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['domestic_modes'] = $data;

        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_DOMESTIC_TYPE' AND is_active =0 ORDER BY genc_detail_code ASC ";
        $data = $callConfig->getAllRows($query);
        $return['journey_types'] = $data;

        $query ="SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id =  {$get['company_id']} AND  genc_code='TRIP_TYPE' AND is_active =0";
        $data = $callConfig->getAllRows($query);
        $return['trip_types'] = $data;

        return $return;

    }

    function getDynamicCities($get){
        global $callConfig;
        $return = array();
        if(isset($get['q']) && $get['q'] != ''){
            $q = urldecode($get['q']);
            $sql  = "SELECT id,city_name FROM cities WHERE city_name LIKE '%{$q}%'  AND is_active=0 ORDER BY city_name "; 
            $data = $callConfig->getAllRows($sql);
            $return['cities_res'] = $data;
        }
        return $return;
    }

    function insertAndUpdateTrevllingPlans_old($post, $get){
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
				    'departure_date'       => $row['departure_date'] ? date('Y-m-d', strtotime($row['departure_date'])) : null,
				    'departure_time'       => $row['departure_time'] ?? null,
				    'return_date'          => $row['return_date'] ? date('Y-m-d', strtotime($row['return_date'])) : null,
				    'return_time'          => $row['return_time'] ?? null,
				    'travel_mode'          => $row['travel_mode'] ?? null,
                    'estimated_cost'       => null,
                    'is_active'            => 0,
                    'current_status'       => 0,
                    'status_date'          => $now,
                    'created_by'           => $user_id,
                    'created_date'         => $now,
                ];
                $res = $callConfig->insertRecord('domestic_round_trips', $rec);
            }

                // --- Trip Hotels ---
            foreach (non_empty_rows($hotels, ['hotel_name', 'location', 'check_in', 'check_out', 'description'], $rowEmpty) as $h) {
                $rec = [
                    'company_id'     => $company_id,
                    'trip_id'        => $trip_id,
				    'hotel_name'          => $h['hotel_name'] ?? null,
				    'check_in'            => $h['check_in'] ? date('Y-m-d', strtotime($h['check_in'])) : null,
				    'check_out'           => $h['check_out'] ? date('Y-m-d', strtotime($h['check_out'])) : null,
				    'location'            => $h['location'] ?? null,
				    'room_type'           => null,
				    'booking_reference'   => null,
				    'cost'                => null,
				    'description'         => $h['description'] ?? null,
                    'is_active'      => 0,
                    'current_status' => 0,
                    'status_date'    => $now,
                    'created_by'     => $user_id,
                    'created_date'   => $now,
                ];
                $res = $callConfig->insertRecord('trip_hotels', $rec);
            }
            foreach (non_empty_rows($cars, ['travel_mode', 'from_location', 'to_location', 'pickup_at', 'dropoff_at', 'car_type', 'trip_purpose'], $rowEmpty) as $c) {
                $rec = [
                    'company_id'     => $company_id,
                    'trip_id'        => $trip_id,
                    'user_id'        => $user_id,
				    'trip_purpose'   => $c['trip_purpose'] ?? null,
				    'trip_date'      => $c['pickup_at'] ? date('Y-m-d', strtotime($c['pickup_at'])) : null,
				    'from_location'  => $c['from_location'] ?? null,
				    'to_location'    => $c['to_location'] ?? null,
				    'travel_mode'    => $c['travel_mode'] ?? null,
				    'estimated_cost' => null,
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
					    'departure_date'       => $row['departure_date'] ? date('Y-m-d', strtotime($row['departure_date'])) : null,
					    'departure_time'       => $row['departure_time'] ?? null,
					    'return_date'          => $row['return_date'] ? date('Y-m-d', strtotime($row['return_date'])) : null,
					    'return_time'          => $row['return_time'] ?? null,
					    'travel_mode'          => $row['travel_mode'] ?? null,
                        'estimated_cost'       => null,
                        'is_active'            => 0,
                        'current_status'       => 0,
                        'status_date'          => $now,
                        'created_by'           => $user_id,
                        'created_date'         => $now,
                    ];
                    $res = $callConfig->insertRecord('domestic_round_trips', $rec);
                }

                // --- Trip Hotels ---
                foreach (non_empty_rows($hotels, ['hotel_name', 'location', 'check_in', 'check_out', 'description'], $rowEmpty) as $h) {
                    $rec = [
                        'company_id'     => $company_id,
                        'trip_id'        => $trip_id,
					    'hotel_name'          => $h['hotel_name'] ?? null,
					    'check_in'            => $h['check_in'] ? date('Y-m-d', strtotime($h['check_in'])) : null,
					    'check_out'           => $h['check_out'] ? date('Y-m-d', strtotime($h['check_out'])) : null,
					    'location'            => $h['location'] ?? null,
					    'room_type'           => null,
					    'booking_reference'   => null,
					    'cost'                => null,
					    'description'         => $h['description'] ?? null,
                        'is_active'      => 0,
                        'current_status' => 0,
                        'status_date'    => $now,
                        'created_by'     => $user_id,
                        'created_date'   => $now,
                    ];
                    $res = $callConfig->insertRecord('trip_hotels', $rec);
                }
                foreach (non_empty_rows($cars, ['travel_mode', 'from_location', 'to_location', 'pickup_at', 'dropoff_at', 'car_type', 'trip_purpose'], $rowEmpty) as $c) {
                    $rec = [
                        'company_id'     => $company_id,
                        'trip_id'        => $trip_id,
                        'user_id'        => $user_id,
					    'trip_purpose'   => $c['trip_purpose'] ?? null,
					    'trip_date'      => $c['pickup_at'] ? date('Y-m-d', strtotime($c['pickup_at'])) : null,
					    'from_location'  => $c['from_location'] ?? null,
					    'to_location'    => $c['to_location'] ?? null,
					    'travel_mode'    => $c['travel_mode'] ?? null,
					    'estimated_cost' => null,
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
                }
            }
        }
        return $return;
    }
    function getCompanyAnnouncements($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id, announcement_category_name FROM announcement_categories WHERE company_id = {$get['company_id']} AND is_active = 0 ORDER BY id DESC";
        $data = $callConfig->getAllRows($query);
        $return['announcement_categories'] = $data;
        if($get['rid'] && $get['rid'] != ''){
            $query = "SELECT * FROM company_announcements WHERE  id= {$get['rid']} AND  company_id=".$get['company_id']." AND is_active=0";
            $data = $callConfig->getRow($query);
            $return['announcement'] = $data;
        }
        return $return;
    }
	function tocreateuserstationeryrequestnewhr($get){
		global $callConfig;
		$get['transaction_date'] = date('Y-m-d');
		$rnd = $this->toGetApplicationSerialNumber('stationery_requests',$get);
		$request_date =  $get['request_date']; 
		$require_date = $get['require_date']; 
		$insert_array = array('company_id'=>$get['company_id'], 'request_no'=>$rnd, 'request_date'=>$request_date, 'require_date'=>$require_date , 'emp_id'=> $get['st_emp_id'] ,'request_comment'=>$get['request_comment'], 'user_id'=>$get['user_id'],'deliver_type'=>$get['deliver_type'],'building_floor_space_id'=>$get['building_floor_space_id'] ,'building_floor_id'=>$get['building_floor_id'],'building_id'=>$get['building_id'], 'location_id'=>$get['location_id'], 'other_name'=>$get['other_name'] ,'is_active'=>'0','current_status'=>0, 'created_by'=>$get['user_id']); 
		$insert = $callConfig->insertRecord('stationery_requests',$insert_array);
		$last_id = $insert['LASTID'];

		$uquery = "UPDATE stationery_cart SET request_no = '".$rnd."', deliver_type = '".$get['deliver_type']."',request_id='{$last_id}'  WHERE company_id = ".$get['company_id']." AND user_id = ".$get['user_id']."  AND request_no IS NULL AND is_active = 0";
		$edata = $callConfig->executeQuery($uquery);
		
		// if($get['st_current_status'] == 199 ){		
		// 	$query = "SELECT * FROM stationery_cart WHERE company_id = ".$get['company_id']." AND request_no IN ( SELECT request_no FROM stationery_requests WHERE company_id = ".$get['company_id']." AND id = '".$insert['LASTID']."'  AND  is_active = 0 ) AND   is_active = 0";
		// 	$res['retrive']  = $query;
		// 	//echo "HHHHH"; print_r($res); exit;
		// 	$datas  =  $callConfig->getAllRows($query); 
		// 	foreach($datas as $data){
		// 		$insert_array = array('company_id'=>$get['company_id'],'request_no'=>$data->request_no,'item_id'=>$data->item_id,'in_qty'=>0,'out_qty'=>$data->qty,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
		// 		$res['insert_array'][] = $insert_array;
		// 		$res['insert_res'][]=$callConfig->insertRecord('stationery_item_ledger',$insert_array);
		// 		$upquey = "UPDATE stationery_items SET available_qty = available_qty-".$data->qty." WHERE id=".$data->item_id;
		// 		$res['update'][]=$callConfig->executeQuery($upquey); 
		// 	}
		// }
		return $edata;
	}
    function getStationeryAvailableItemsData($get){
        global $callConfig;
        $return = array();
        $query = "SELECT id, name 
            FROM stationery_categories 
            WHERE company_id = ".$get['company_id']." 
            AND is_active = 0
            ORDER BY name ASC";
        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;
        $query = "SELECT id, location_name
            FROM locations
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY location_name ASC";
        $data = $callConfig->getAllRows($query);
        $return['location_res'] = $data;
        if(isset($get['ajax']) && $get['ajax'] =='Y'){
            $whr = "";
            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr = " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['location_id']) && $get['location_id'] != ""){
                $whr .= " AND si.location_id = ".$get['location_id']." ";
            }

            $query = "SELECT 
                    l.location_name AS location_name,
                    sc.name AS category_name,
                    si.item_name,
                    IFNULL(SUM(sil.in_qty),0) AS received,
                    si.available_qty AS current_stock,
                    IFNULL(SUM(sil.out_qty),0) AS consumed,
                    (IFNULL(SUM(sil.in_qty),0) - IFNULL(SUM(sil.out_qty),0)) AS balance
                FROM stationery_items si
                INNER JOIN stationery_categories sc 
                    ON sc.id = si.category_id AND sc.company_id = si.company_id AND sc.is_active=0
                LEFT JOIN locations l
                    ON l.id = si.location_id AND l.company_id = si.company_id AND l.is_active = 0
                LEFT JOIN stationery_item_ledger sil 
                    ON sil.item_id = si.id AND sil.company_id = si.company_id 
                    AND sil.is_active = 0
                WHERE si.company_id = ".$get['company_id']."
                AND si.is_active = 0
                ".$whr."
                GROUP BY si.id
                ORDER BY sc.name, si.item_name";

            $data = $callConfig->getAllRows($query);

            $return['available_items_res'] = $data;
        }
        return $return;
    }
    function getMonthlyStationeryReport($get){
        global $callConfig;

        $return = array();

        /* Categories for filter */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";

        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;
        $query = "SELECT id, location_name
            FROM locations
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY location_name ASC";

        $data = $callConfig->getAllRows($query);
        $return['location_res'] = $data;

        /* Report data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){
            /* Default dates */
            $from_date = $get['from_date'] ?? '';
            $to_date   = $get['to_date'] ?? '';
            if($from_date == ''){
                $from_date = date('Y-m-01');
            }
            if($to_date == ''){
                $to_date = date('Y-m-d');
            }
            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr .= " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['location_id']) && $get['location_id'] != ""){
                $whr .= " AND si.location_id = ".$get['location_id']." ";
            }

            $whr .= " AND DATE(sr.request_date) BETWEEN '".$from_date."' AND '".$to_date."' ";

            $query = "SELECT
                    l.location_name,
                    sc.name AS category_name,
                    si.item_name,
                    sca.qty,
                    sr.request_date,
                    emp.display_name AS issued_by, emp.emp_code AS issued_code
                FROM stationery_requests sr
                INNER JOIN stationery_cart sca
                    ON (sca.request_id = sr.id OR sca.request_no = sr.request_no)
                    AND sca.is_active = 0
                INNER JOIN stationery_items si
                    ON si.id = sca.item_id
                    AND si.is_active = 0
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.is_active = 0
                LEFT JOIN locations l
                    ON l.id = si.location_id
                    AND l.company_id = si.company_id
                    AND l.is_active = 0
                INNER JOIN employees emp
                    ON emp.user_id = sr.created_by
                    AND emp.is_active = 0
                WHERE sr.company_id = ".$get['company_id']."
                AND sr.is_active = 0  AND sr.current_status=199
                ".$whr."
                ORDER BY sr.request_date DESC";

            $data = $callConfig->getAllRows($query);
            $return['monthly_res'] = $data;
            $return['query'] = $query;
        }

        return $return;
    }
    function getStationeryStockBalance($get){
        global $callConfig;

        $return = array();

        /* Categories for filter */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";

        $data = $callConfig->getAllRows($query);
        $return['category_res'] = $data;
        $query = "SELECT id, location_name
            FROM locations
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY location_name ASC";

        $data = $callConfig->getAllRows($query);
        $return['location_res'] = $data;

        /* Stock balance data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){

            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr = " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['location_id']) && $get['location_id'] != ""){
                $whr .= " AND si.location_id = ".$get['location_id']." ";
            }

            $query = "SELECT
                    l.location_name,
                    sc.name AS category_name,
                    si.item_name,
                    si.available_qty,
                    si.minmum_qty
                FROM stationery_items si
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.company_id = si.company_id
                    AND sc.is_active = 0
                LEFT JOIN locations l
                    ON l.id = si.location_id
                    AND l.company_id = si.company_id
                    AND l.is_active = 0
                WHERE si.company_id = ".$get['company_id']."
                AND si.is_active = 0
                ".$whr."
                ORDER BY sc.name, si.item_name";

            $data = $callConfig->getAllRows($query);
            $return['stock_res'] = $data;
        }

        return $return;
    }
    function getTotalItemsIssuedCategoryWise($get){
        global $callConfig;

        $return = array();

        /* Categories */
        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";
        $return['category_res'] = $callConfig->getAllRows($query);

        /* Items */
        $query = "SELECT id, item_name
            FROM stationery_items
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY item_name ASC";
        $return['item_res'] = $callConfig->getAllRows($query);
        $query = "SELECT id, location_name
            FROM locations
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY location_name ASC";
        $return['location_res'] = $callConfig->getAllRows($query);

        /* Report data */
        if(isset($get['ajax']) && $get['ajax'] == 'Y'){

            $from_date = $get['from_date'] ?? '';
            $to_date   = $get['to_date'] ?? '';

            if($from_date == ''){
                $from_date = date('Y-m-01');
            }
            if($to_date == ''){
                $to_date = date('Y-m-d');
            }

            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr .= " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['item_ids']) && $get['item_ids'] != ""){
                $whr .= " AND si.id IN (".$get['item_ids'].") ";
            }
            if(isset($get['location_id']) && $get['location_id'] != ""){
                $whr .= " AND si.location_id = ".$get['location_id']." ";
            }

            $whr .= " AND DATE(sr.request_date)
                      BETWEEN '".$from_date."' AND '".$to_date."' ";

            $query = "SELECT
                    l.location_name,
                    sc.name AS category_name,
                    si.item_name,
                    SUM(sca.qty) AS total_qty
                FROM stationery_requests sr
                INNER JOIN stationery_cart sca
                    ON (sca.request_id = sr.id OR sca.request_no = sr.request_no)
                    AND sca.is_active = 0
                INNER JOIN stationery_items si
                    ON si.id = sca.item_id
                    AND si.is_active = 0
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.is_active = 0 
                LEFT JOIN locations l
                    ON l.id = si.location_id
                    AND l.company_id = si.company_id
                    AND l.is_active = 0
                WHERE sr.company_id = ".$get['company_id']."
                AND sr.is_active = 0  AND sr.current_status=199
                ".$whr."
                GROUP BY si.id, si.location_id
                ORDER BY l.location_name, sc.name, si.item_name";

            $return['issued_res'] = $callConfig->getAllRows($query);
        }

        return $return;
    }
    function getStationeryStockInOutDatewise($get){
        global $callConfig;

        $return = array();

        $query = "SELECT id, name
            FROM stationery_categories
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY name ASC";
        $return['category_res'] = $callConfig->getAllRows($query);

        $query = "SELECT id, location_name
            FROM locations
            WHERE company_id = ".$get['company_id']."
            AND is_active = 0
            ORDER BY location_name ASC";
        $return['location_res'] = $callConfig->getAllRows($query);

        if(isset($get['ajax']) && $get['ajax'] == 'Y'){
            $from_date = isset($get['from_date']) && $get['from_date'] != "" ? $get['from_date'] : date('Y-m-01');
            $to_date = isset($get['to_date']) && $get['to_date'] != "" ? $get['to_date'] : date('Y-m-d');
            $whr = "";

            if(isset($get['category_ids']) && $get['category_ids'] != ""){
                $whr .= " AND si.category_id IN (".$get['category_ids'].") ";
            }
            if(isset($get['location_id']) && $get['location_id'] != ""){
                $whr .= " AND si.location_id = ".$get['location_id']." ";
            }

            $query = "SELECT
                    l.location_name,
                    sc.name AS category_name,
                    si.item_name,
                    si.available_qty,
                    si.minmum_qty,
                    IFNULL(SUM(CASE WHEN DATE(sil.request_date) BETWEEN '".$from_date."' AND '".$to_date."' THEN sil.in_qty ELSE 0 END),0) AS instock,
                    IFNULL(SUM(CASE WHEN DATE(sil.request_date) BETWEEN '".$from_date."' AND '".$to_date."' THEN sil.out_qty ELSE 0 END),0) AS outstock
                FROM stationery_items si
                INNER JOIN stationery_categories sc
                    ON sc.id = si.category_id
                    AND sc.company_id = si.company_id
                    AND sc.is_active = 0
                LEFT JOIN locations l
                    ON l.id = si.location_id
                    AND l.company_id = si.company_id
                    AND l.is_active = 0
                LEFT JOIN stationery_item_ledger sil
                    ON sil.item_id = si.id
                    AND sil.company_id = si.company_id
                    AND sil.is_active = 0
                WHERE si.company_id = ".$get['company_id']."
                AND si.is_active = 0
                ".$whr."
                GROUP BY si.id, si.location_id
                ORDER BY l.location_name, sc.name, si.item_name";

            $return['report_res'] = $callConfig->getAllRows($query);
        }

        return $return;
    }
	// model/integartion.class.php
	function updatestationeryitem($post, $get){
		global $callConfig;
		$update = array();

		if($post['location_id'] != ""){
			$update['location_id'] = $post['location_id'];
		}
		if($post['category_id'] != ""){
			$update['category_id'] = $post['category_id'];
		}
		if($post['item_name'] != ""){
			$update['item_name'] = $post['item_name'];
		}
		$update['cost'] = $post['cost'];
		$update['description'] = $post['description'];

		// optional audit fields if your table has these columns
		$update['updated_by'] = $get['user_id'];
		$update['updated_date'] = date('Y-m-d H:i:s');

		// quantity intentionally not updated
		$id = $post['id'];
		if($id == ""){
			return array('status' => 0, 'msg' => 'Invalid item id');
		}

		// company guard
		$query = "SELECT id FROM stationery_items WHERE id=".$id." AND company_id=".$get['company_id']." AND is_active=0";
		$row = $callConfig->getRow($query);
		if($row->id == ""){
			return array('status' => 0, 'msg' => 'Item not found');
		}

		$res = $callConfig->updateRecord('stationery_items', $update, 'id', $id);
		return array('status' => 1, 'result' => $res);
	}

	//manufacturing related start

	function getProcessQcAllowedProcesses($company_id){
		global $callConfig;
		$processes = array(
			'goods_receipt_notes' => 'Goods Receipt Notes',
			'grn_returns' => 'GRN Returns',
			'delivery_orders' => 'Delivery Orders',
			'sales_returns' => 'Sales Returns',
			'manufacturing_product_notes' => 'Manufacturing Product Notes',
			'manufacturing_work_orders' => 'Manufacturing Work Orders',
			'subcontract_work_order_receipts' => 'Subcontract Work Order Receipts'
		);
		$query = "SELECT DISTINCT process FROM item_quality_parameters_mapping WHERE company_id = ".intval($company_id)." AND process IS NOT NULL AND process != '' AND is_active = 0";
		$rows = $callConfig->getAllRows($query);
		foreach((array)$rows as $row){
			if($row->process != 'all'){
				$processes[$row->process] = ucwords(str_replace('_', ' ', $row->process));
			}
		}
		return $processes;
	}

	function getProcessQcMappingByPriority($company_id, $process, $item_cat_id, $item_id){
		global $callConfig;
		$company_id = intval($company_id);
		$item_cat_id = intval($item_cat_id);
		$item_id = intval($item_id);
		$process = $callConfig->real_escape_string($process);
		$checks = array(
			array("item_cat_id = '{$item_cat_id}' AND process = '{$process}' AND item_id = '{$item_id}'", 'category_process_item'),
			array("item_cat_id = '{$item_cat_id}' AND process = '{$process}' AND (item_id IS NULL OR item_id = 0)", 'category_process'),
			array("item_cat_id = '{$item_cat_id}' AND (process IS NULL OR process = '' OR process = 'all') AND (item_id IS NULL OR item_id = 0)", 'category')
		);
		foreach($checks as $check){
			$query = "SELECT * FROM item_quality_parameters_mapping WHERE company_id = '{$company_id}' AND ".$check[0]." AND is_active = 0 ORDER BY id DESC LIMIT 0,1";

			$row = $callConfig->getRow($query);
			if(!empty($row->id)){
				$row->mapping_match_type = $check[1];
				$row->mapping_query = $query;
				return $row;
			}
		}
		return (object)array();
	}

	function getprocessqcprocesses($get){
		return array('processes'=>$this->getProcessQcAllowedProcesses($get['company_id']));
	}

	function getprocessqctransactions($get){
		global $callConfig;
		$company_id = intval($get['company_id']);
		$process = $get['process'] ?? '';
		if(!preg_match('/^[A-Za-z0-9_]+$/', $process)){
			return array('status'=>'error','message'=>'Invalid process');
		}
		$columns_data = $callConfig->getAllRows("SHOW COLUMNS FROM ".$process);
		$columns = array();
		foreach((array)$columns_data as $col){
			$columns[] = $col->Field;
		}
		if(!in_array('id', $columns)){
			return array('status'=>'error','message'=>'Process table not found');
		}
		$number_fields = array('transaction_no','transaction_number','forecast_no','firm_order_no','qo_number','doc_ref_no');
		$item_fields = array('item_id','work_order_item_id','product_id');
		$date_fields = array('transaction_date','created_date','status_date');
		$number_field = 'id';
		$item_field = '';
		$date_field = 'created_date';
		foreach($number_fields as $field){ if(in_array($field, $columns)){ $number_field = $field; break; } }
		foreach($item_fields as $field){ if(in_array($field, $columns)){ $item_field = $field; break; } }
		foreach($date_fields as $field){ if(in_array($field, $columns)){ $date_field = $field; break; } }
		if($item_field == ''){
			return array('status'=>'error','message'=>'This process does not have a direct item/product field yet');
		}
		$query = "SELECT t.id AS transaction_id, t.".$number_field." AS transaction_no, t.".$date_field." AS transaction_date,
				t.".$item_field." AS item_id, i.item_name, i.sku, i.category_id AS item_cat_id,
				IFNULL(i.quality_check, 0) AS quality_check,
				atqc.id AS qc_header_id,
				IFNULL((SELECT COUNT(1) FROM application_table_quality_check_items pqi WHERE pqi.company_id = t.company_id AND pqi.application_table_name = '".$process."' AND pqi.transaction_id = t.id AND pqi.item_id = t.".$item_field." AND pqi.is_active = 0), 0) AS qc_total_count,
				IFNULL((SELECT COUNT(1) FROM application_table_quality_check_items pqi WHERE pqi.company_id = t.company_id AND pqi.application_table_name = '".$process."' AND pqi.transaction_id = t.id AND pqi.item_id = t.".$item_field." AND pqi.qc_status = 2 AND pqi.is_active = 0), 0) AS qc_passed_count,
				IFNULL((SELECT COUNT(1) FROM application_table_quality_check_items pqi WHERE pqi.company_id = t.company_id AND pqi.application_table_name = '".$process."' AND pqi.transaction_id = t.id AND pqi.item_id = t.".$item_field." AND pqi.qc_status = 3 AND pqi.is_active = 0), 0) AS qc_failed_count,
				IFNULL((SELECT COUNT(1) FROM item_quality_check iqc WHERE iqc.company_id = t.company_id AND iqc.application_table_name = '".$process."' AND iqc.transaction_id = t.id AND iqc.item_id = t.".$item_field." AND iqc.is_active = 0), 0) AS qc_result_count
			FROM ".$process." t
			INNER JOIN items i ON i.company_id = t.company_id AND i.id = t.".$item_field."
			INNER JOIN application_table_quality_check atqc ON atqc.company_id = t.company_id AND atqc.application_table_name = '".$process."' AND atqc.transaction_id = t.id AND atqc.is_active = 0
			WHERE t.company_id = '{$company_id}' AND t.is_active = 0 AND IFNULL(i.quality_check, 0) = 1
			ORDER BY t.id DESC";
		$rows = $callConfig->getAllRows($query);
		foreach((array)$rows as $row){
			if(intval($row->qc_total_count ?? 0) > 0){
				$row->summary_text = intval($row->qc_passed_count ?? 0).' passed / '.intval($row->qc_failed_count ?? 0).' failed';
			}else{
				$row->summary_text = 'Pending QC';
			}
		}
		return array('status'=>'success','data'=>$rows,'query'=>$query);
	}

	function getprocessqcmapping($get){
		global $callConfig;
		$company_id = intval($get['company_id']);
		$process = $get['process'] ?? $get['table'] ?? '';
		$tranid = intval($get['tranid'] ?? 0);
		$item_id = intval($get['item_id'] ?? 0);
		if(!preg_match('/^[A-Za-z0-9_]+$/', $process) || $tranid <= 0 || $item_id <= 0){
			return array('status'=>'error','message'=>'Process, transaction and item are required');
		}
		$item = $callConfig->getRow("SELECT * FROM items WHERE company_id = '{$company_id}' AND id = '{$item_id}'");
		$item_cat_id = intval($item->category_id ?? 0);
		$mapping = $this->getProcessQcMappingByPriority($company_id, $process, $item_cat_id, $item_id);
		$return['mapping'] = $mapping;
		$return = array('status'=>'success','item'=>$item,'item_cat_id'=>$item_cat_id,'mapping'=>$mapping,'has_mapping'=>!empty($mapping->id));
		$return['serial_rows'] = $callConfig->getAllRows("SELECT id, serial_no FROM item_serial_nos WHERE company_id = '{$company_id}' AND transaction_table = '{$process}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND is_active = 0");
		$return['lot_rows'] = $callConfig->getAllRows("SELECT id, lot_no AS serial_no, lot_quantity FROM item_lot_nos WHERE company_id = '{$company_id}' AND transaction_table = '{$process}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND is_active = 0");
		return $return;
	}

	function saveprocessqc($post, $get){
		$post['table'] = $post['process'] ?? ($post['table'] ?? '');
		return $this->toinsertthedataonitemquality($post, $get);
	}

	function getProcessQcHeader($company_id, $process, $tranid, $user_id){
		global $callConfig;
		$company_id = intval($company_id);
		$tranid = intval($tranid);
		$process = $callConfig->real_escape_string($process);
		$header = $callConfig->getRow("SELECT * FROM application_table_quality_check WHERE company_id = '{$company_id}' AND application_table_name = '{$process}' AND transaction_id = '{$tranid}' AND is_active = 0");
		if(empty($header->id)){
			$callConfig->insertRecord('application_table_quality_check', array(
				'company_id'=>$company_id,
				'application_table_name'=>$process,
				'transaction_id'=>$tranid,
				'quality_check'=>1,
				'is_active'=>0,
				'current_status'=>0,
				'created_by'=>intval($user_id),
				'created_date'=>date('Y-m-d H:i:s')
			));
			$header = $callConfig->getRow("SELECT * FROM application_table_quality_check WHERE company_id = '{$company_id}' AND application_table_name = '{$process}' AND transaction_id = '{$tranid}' AND is_active = 0");
		}
		return $header;
	}

	function getProcessQcParameterCount($mapping){
		$spec = json_decode($mapping->specifications ?? '', true);
		if(!is_array($spec) || !isset($spec['parameters']) || !is_array($spec['parameters'])){
			return 0;
		}
		$mandatory = 0;
		foreach($spec['parameters'] as $param){
			if(intval($param['mand'] ?? 0) == 1){
				$mandatory++;
			}
		}
		return $mandatory > 0 ? $mandatory : count($spec['parameters']);
	}

	function getProcessQcSourceRows($company_id, $process, $tranid, $item_id, $item){
		global $callConfig;
		$company_id = intval($company_id);
		$tranid = intval($tranid);
		$item_id = intval($item_id);
		$process = $callConfig->real_escape_string($process);
		$rows = array();
		$serial_rows = $callConfig->getAllRows("SELECT id, serial_no FROM item_serial_nos WHERE company_id = '{$company_id}' AND transaction_table = '{$process}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND is_active = 0 ORDER BY id ASC");
		foreach((array)$serial_rows as $row){
			$rows[] = array('id'=>intval($row->id),'type'=>'SERIAL','no'=>$row->serial_no,'lot_qty'=>0);
		}
		if(count($rows) == 0){
			$lot_rows = $callConfig->getAllRows("SELECT id, lot_no, lot_quantity FROM item_lot_nos WHERE company_id = '{$company_id}' AND transaction_table = '{$process}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND is_active = 0 ORDER BY id ASC");
			foreach((array)$lot_rows as $row){
				$rows[] = array('id'=>intval($row->id),'type'=>'LOT','no'=>$row->lot_no,'lot_qty'=>$row->lot_quantity);
			}
		}
		if(count($rows) == 0){
			$rows[] = array('id'=>0,'type'=>'ITEM','no'=>$item->item_name ?? 'Item QC','lot_qty'=>0);
		}
		return $rows;
	}

	function syncProcessQcItems($company_id, $process, $tranid, $item_id, $header, $mapping, $item){
		global $callConfig;
		$company_id = intval($company_id);
		$tranid = intval($tranid);
		$item_id = intval($item_id);
		$item_cat_id = intval($item->category_id ?? 0);
		$header_id = intval($header->id ?? 0);
		$mapping_id = intval($mapping->id ?? 0);
		$required_count = $this->getProcessQcParameterCount($mapping);
		$process_safe = $callConfig->real_escape_string($process);
		$source_rows = $this->getProcessQcSourceRows($company_id, $process, $tranid, $item_id, $item);
		foreach($source_rows as $source){
			$type = $callConfig->real_escape_string($source['type']);
			$source_id = intval($source['id']);
			$exists = $callConfig->getRow("SELECT id FROM application_table_quality_check_items WHERE company_id = '{$company_id}' AND application_table_name = '{$process_safe}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND item_serial_lot_id = '{$source_id}' AND serial_lot_type = '{$type}' AND is_active = 0");
			if(empty($exists->id)){
				$callConfig->insertRecord('application_table_quality_check_items', array(
					'company_id'=>$company_id,
					'application_table_quality_check_id'=>$header_id,
					'application_table_name'=>$process_safe,
					'transaction_id'=>$tranid,
					'item_id'=>$item_id,
					'item_cat_id'=>$item_cat_id,
					'item_serial_lot_id'=>$source_id,
					'serial_lot_type'=>$type,
					'serial_lot_no'=>$callConfig->real_escape_string($source['no']),
					'lot_qty'=>$source['lot_qty'],
					'mapping_id'=>$mapping_id,
					'qc_status'=>0,
					'required_count'=>$required_count,
					'checked_count'=>0,
					'failed_count'=>0,
					'is_active'=>0,
					'current_status'=>0,
					'created_by'=>intval($_GET['user_id'] ?? 0),
					'created_date'=>date('Y-m-d H:i:s')
				));
			}else{
				$callConfig->updateRecord('application_table_quality_check_items', array(
					'application_table_quality_check_id'=>$header_id,
					'item_cat_id'=>$item_cat_id,
					'mapping_id'=>$mapping_id,
					'required_count'=>$required_count,
					'updated_date'=>date('Y-m-d H:i:s')
				), 'id', $exists->id);
			}
		}
	}

	function getProcessQcContext($get){
		global $callConfig;
		$company_id = intval($get['company_id']);
		$process = $get['process'] ?? $get['table'] ?? '';
		$tranid = intval($get['tranid'] ?? 0);
		$item_id = intval($get['item_id'] ?? 0);
		if(!preg_match('/^[A-Za-z0-9_]+$/', $process) || $tranid <= 0 || $item_id <= 0){
			return array('status'=>'error','message'=>'Process, transaction and item are required');
		}
		$item = $callConfig->getRow("SELECT * FROM items WHERE company_id = '{$company_id}' AND id = '{$item_id}' AND is_active = 0");
		if(empty($item->id)){
			return array('status'=>'error','message'=>'Item not found');
		}
		if(intval($item->quality_check ?? 0) != 1){
			return array('status'=>'error','message'=>'This item is not marked for QC check');
		}
		$mapping = $this->getProcessQcMappingByPriority($company_id, $process, intval($item->category_id ?? 0), $item_id);
		if(empty($mapping->id)){
			return array('status'=>'no_mapping','message'=>'No QC mapping is available for this item/category/process','item'=>$item,'item_cat_id'=>intval($item->category_id ?? 0));
		}
		$header = $this->getProcessQcHeader($company_id, $process, $tranid, $get['user_id'] ?? 0);
		$this->syncProcessQcItems($company_id, $process, $tranid, $item_id, $header, $mapping, $item);
		return array('status'=>'success','process'=>$process,'tranid'=>$tranid,'item_id'=>$item_id,'item'=>$item,'mapping'=>$mapping,'header'=>$header);
	}

	function getProcessQcSummaryData($company_id, $process, $tranid, $item_id){
		global $callConfig;
		$company_id = intval($company_id);
		$tranid = intval($tranid);
		$item_id = intval($item_id);
		$process = $callConfig->real_escape_string($process);
		$row = $callConfig->getRow("SELECT 
			COUNT(1) AS total,
			SUM(CASE WHEN qc_status = 0 THEN 1 ELSE 0 END) AS pending,
			SUM(CASE WHEN qc_status = 1 THEN 1 ELSE 0 END) AS draft,
			SUM(CASE WHEN qc_status = 2 THEN 1 ELSE 0 END) AS passed,
			SUM(CASE WHEN qc_status = 3 THEN 1 ELSE 0 END) AS failed
			FROM application_table_quality_check_items
			WHERE company_id = '{$company_id}' AND application_table_name = '{$process}' AND transaction_id = '{$tranid}' AND item_id = '{$item_id}' AND is_active = 0");
		return array(
			'total'=>intval($row->total ?? 0),
			'pending'=>intval($row->pending ?? 0),
			'draft'=>intval($row->draft ?? 0),
			'passed'=>intval($row->passed ?? 0),
			'failed'=>intval($row->failed ?? 0)
		);
	}

	function getprocessqcsummary($get){
		$context = $this->getProcessQcContext($get);
		if(($context['status'] ?? '') != 'success'){
			return $context;
		}
		return array('status'=>'success','summary'=>$this->getProcessQcSummaryData($get['company_id'], $context['process'], $context['tranid'], $context['item_id']));
	}

	function getprocessqcworkspace($get){
		global $callConfig;
		$context = $this->getProcessQcContext($get);
		if(($context['status'] ?? '') != 'success'){
			return $context;
		}
		$company_id = intval($get['company_id']);
		$process = $callConfig->real_escape_string($context['process']);
		$tranid = intval($context['tranid']);
		$item_id = intval($context['item_id']);
		$status = $get['status'] ?? 'all';
		$search = trim($get['search'] ?? '');
		$page_no = max(1, intval($get['page_no'] ?? 1));
		$limit = intval($get['limit'] ?? 25);
		if(!in_array($limit, array(25,50,100))){ $limit = 25; }
		$where = "pqi.company_id = '{$company_id}' AND pqi.application_table_name = '{$process}' AND pqi.transaction_id = '{$tranid}' AND pqi.item_id = '{$item_id}' AND pqi.is_active = 0";
		if($status !== 'all' && in_array(strval($status), array('0','1','2','3'))){
			$where .= " AND pqi.qc_status = '".intval($status)."'";
		}
		if($search !== ''){
			$search_safe = $callConfig->real_escape_string($search);
			$where .= " AND (pqi.serial_lot_no LIKE '%".$search_safe."%' OR i.item_name LIKE '%".$search_safe."%' OR i.sku LIKE '%".$search_safe."%')";
		}
		$total_row = $callConfig->getRow("SELECT COUNT(1) AS total FROM application_table_quality_check_items pqi LEFT JOIN items i ON i.id = pqi.item_id WHERE ".$where);
		$total = intval($total_row->total ?? 0);
		$total_pages = max(1, intval(ceil($total / $limit)));
		if($page_no > $total_pages){ $page_no = $total_pages; }
		$offset = ($page_no - 1) * $limit;
		$rows = $callConfig->getAllRows("SELECT pqi.*, i.item_name, i.sku, DATE_FORMAT(pqi.updated_date, '%d-%m-%Y %H:%i') AS last_saved FROM application_table_quality_check_items pqi LEFT JOIN items i ON i.id = pqi.item_id WHERE ".$where." ORDER BY pqi.item_id ASC, FIELD(pqi.qc_status, 1, 0, 3, 2), pqi.id ASC LIMIT ".$offset.",".$limit);
		return array(
			'status'=>'success',
			'item'=>$context['item'],
			'mapping'=>$context['mapping'],
			'summary'=>$this->getProcessQcSummaryData($company_id, $process, $tranid, $item_id),
			'rows'=>$rows,
			'page_no'=>$page_no,
			'total_pages'=>$total_pages,
			'total_rows'=>$total
		);
	}

	function getProcessQcGroupedParameters($company_id, $mapping){
		global $callConfig;
		$spec = json_decode($mapping->specifications ?? '', true);
		if(!is_array($spec)){ return array(); }
		$param_meta = array();
		foreach((array)($spec['parameters'] ?? array()) as $param){
			if(isset($param['id'])){
				$param_meta[intval($param['id'])] = $param;
			}
		}
		if(count($param_meta) == 0){ return array(); }
		$ids = implode(',', array_keys($param_meta));
		$params = $callConfig->getAllRows("SELECT * FROM quality_parameters WHERE company_id = ".intval($company_id)." AND id IN (".$ids.") AND is_active = 0 ORDER BY quality_parameter_group_id ASC, id ASC");
		$group_ids = array();
		foreach((array)$params as $param){
			$group_ids[intval($param->quality_parameter_group_id)] = intval($param->quality_parameter_group_id);
		}
		$group_names = array();
		if(count($group_ids) > 0){
			$groups = $callConfig->getAllRows("SELECT id, group_name FROM quality_parameters_groups WHERE company_id = ".intval($company_id)." AND id IN (".implode(',', $group_ids).")");
			foreach((array)$groups as $group){
				$group_names[intval($group->id)] = $group->group_name;
			}
		}
		$return = array();
		foreach((array)$params as $param){
			$gid = intval($param->quality_parameter_group_id);
			if(!isset($return[$gid])){
				$return[$gid] = array('id'=>$gid,'group_name'=>$group_names[$gid] ?? 'QC Parameters','parameters'=>array());
			}
			$meta = $param_meta[intval($param->id)] ?? array();
			$row = (array)$param;
			$row['mand'] = $meta['mand'] ?? 0;
			$row['res'] = $meta['res'] ?? 0;
			$return[$gid]['parameters'][] = $row;
		}
		return array_values($return);
	}

	function getprocessqcitemform($get){
		global $callConfig;
		$context = $this->getProcessQcContext($get);
		if(($context['status'] ?? '') != 'success'){
			return $context;
		}
		$progress_id = intval($get['progress_id'] ?? 0);
		$company_id = intval($get['company_id']);
		$progress = $callConfig->getRow("SELECT * FROM application_table_quality_check_items WHERE company_id = '{$company_id}' AND id = '{$progress_id}' AND is_active = 0");
		if(empty($progress->id)){
			return array('status'=>'error','message'=>'QC row not found');
		}
		$results = $callConfig->getAllRows("SELECT * FROM item_quality_check WHERE company_id = '{$company_id}' AND application_table_name = '".$callConfig->real_escape_string($context['process'])."' AND transaction_id = ".intval($context['tranid'])." AND item_id = ".intval($context['item_id'])." AND item_serial_lot_id = ".intval($progress->item_serial_lot_id)." AND is_active = 0");
		$values = array();
		$remarks = array();
		foreach((array)$results as $result){
			$values[intval($result->item_attribute_id)] = $result->specification_result;
			$remarks[intval($result->item_attribute_id)] = $result->remarks;
		}
		return array(
			'status'=>'success',
			'item'=>$context['item'],
			'progress'=>$progress,
			'groups'=>$this->getProcessQcGroupedParameters($company_id, $context['mapping']),
			'values'=>$values,
			'remarks'=>$remarks
		);
	}

	function getProcessQcOptionLabel($raw_options, $key){
		$options = json_decode($raw_options ?? '', true);
		if(is_array($options) && isset($options[$key])){
			return $options[$key];
		}
		return null;
	}

	function evaluateProcessQcParameter($param, $value){
		$value = trim((string)$value);
		if($value === ''){
			return array('failed'=>0,'message'=>'');
		}
		$display_type = strval($param['display_type'] ?? '');
		$expected = strval($param['res'] ?? '0');
		if($display_type == '1' || $display_type == '2'){
			$expected_label = $this->getProcessQcOptionLabel($param['quality_parameters'] ?? '', $expected);
			$pass = (strval($value) === $expected || ($expected_label !== null && strval($value) === strval($expected_label)));
			return array('failed'=>$pass ? 0 : 1,'message'=>$pass ? '' : 'Expected '.$expected_label);
		}
		if($display_type == '3' || $display_type == '4'){
			if(!is_numeric($value)){
				return array('failed'=>1,'message'=>'Numeric rating is required');
			}
			$number = floatval($value);
			$target = is_numeric($expected) ? floatval($expected) : 0;
			$pass = ($number >= 1 && $number <= 5 && $number >= $target);
			return array('failed'=>$pass ? 0 : 1,'message'=>'Target is '.$target.' or above');
		}
		if($display_type == '5'){
			$range = json_decode($param['quality_parameters'] ?? '', true);
			$min = $range['min'] ?? null;
			$max = $range['max'] ?? null;
			if(!is_numeric($value) || !is_numeric($min) || !is_numeric($max)){
				return array('failed'=>1,'message'=>'Valid range value is required');
			}
			$number = floatval($value);
			$inside = ($number >= floatval($min) && $number <= floatval($max));
			$expects_inside = ($expected === '0' || $expected === '');
			$pass = $expects_inside ? $inside : !$inside;
			return array('failed'=>$pass ? 0 : 1,'message'=>($expects_inside ? 'Expected within ' : 'Expected outside ').$min.' - '.$max);
		}
		$value_lower = strtolower($value);
		$failed = (strpos($value_lower, 'fail') !== false || strpos($value_lower, 'reject') !== false || strpos($value_lower, 'not ok') !== false) ? 1 : 0;
		return array('failed'=>$failed,'message'=>'');
	}

	function saveProcessQcItem($post, $get, $complete){
		global $callConfig;
		$get['process'] = $post['process'] ?? '';
		$get['tranid'] = $post['tranid'] ?? 0;
		$get['item_id'] = $post['item_id'] ?? 0;
		$context = $this->getProcessQcContext($get);
		if(($context['status'] ?? '') != 'success'){
			return $context;
		}
		$company_id = intval($get['company_id']);
		$user_id = intval($get['user_id'] ?? 0);
		$progress_id = intval($post['progress_id'] ?? 0);
		$progress = $callConfig->getRow("SELECT * FROM application_table_quality_check_items WHERE company_id = '{$company_id}' AND id = '{$progress_id}' AND is_active = 0");
		if(empty($progress->id)){
			return array('status'=>'error','message'=>'QC row not found');
		}
		if(in_array(intval($progress->qc_status ?? 0), array(2,3))){
			return array('status'=>'error','message'=>'QC is already completed and locked.');
		}
		$groups = $this->getProcessQcGroupedParameters($company_id, $context['mapping']);
		$required = array();
		$valid_param_ids = array();
		$param_rules = array();
		foreach((array)$groups as $group){
			foreach((array)$group['parameters'] as $param){
				$pid = intval($param['id']);
				$valid_param_ids[$pid] = $pid;
				$param_rules[$pid] = $param;
				if(intval($param['mand'] ?? 0) == 1){
					$required[$pid] = $param['name'];
				}
			}
		}
		$responses = $post['item_attribute_res'] ?? array();
		$remarks = $post['item_attribute_remarks'] ?? array();
		if(!is_array($responses)){ $responses = array(); }
		if(!is_array($remarks)){ $remarks = array(); }
		if($complete){
			foreach($required as $pid=>$name){
				if(!isset($responses[$pid]) || trim((string)$responses[$pid]) === ''){
					return array('status'=>'error','message'=>'Please answer mandatory QC parameter: '.$name);
				}
			}
		}
		$checked_count = 0;
		$failed_count = 0;
		$detail_ids = array();
		$has_required_params = count($required) > 0;
		foreach($responses as $pid=>$value){
			$pid = intval($pid);
			if(!isset($valid_param_ids[$pid]) || trim((string)$value) === ''){
				continue;
			}
			$evaluation = $this->evaluateProcessQcParameter($param_rules[$pid], $value);
			$quality_check = intval($evaluation['failed']);
			$remark = trim((string)($remarks[$pid] ?? ''));
			$is_required_result = (!$has_required_params || isset($required[$pid]));
			if($complete && $is_required_result && $quality_check == 1 && $remark == ''){
				return array('status'=>'error','message'=>'Remarks are required for failed QC check: '.$param_rules[$pid]['name']);
			}
			$old = $callConfig->getRow("SELECT id FROM item_quality_check WHERE company_id = '{$company_id}' AND application_table_name = '".$callConfig->real_escape_string($context['process'])."' AND transaction_id = ".intval($context['tranid'])." AND item_id = ".intval($context['item_id'])." AND item_serial_lot_id = ".intval($progress->item_serial_lot_id)." AND item_attribute_id = ".$pid." AND is_active = 0");
			if(!empty($old->id)){
				$callConfig->executeQuery("UPDATE item_quality_check SET is_active = 1, updated_by = '{$user_id}', updated_date = '".date('Y-m-d H:i:s')."' WHERE id = ".intval($old->id));
			}
			$insert = $callConfig->insertRecord('item_quality_check', array(
				'company_id'=>$company_id,
				'application_table_name'=>$callConfig->real_escape_string($context['process']),
				'transaction_id'=>intval($context['tranid']),
				'item_id'=>intval($context['item_id']),
				'item_serial_lot_id'=>intval($progress->item_serial_lot_id),
				'item_attribute_id'=>$pid,
				'specification_result'=>$callConfig->real_escape_string($value),
				'quality_check'=>$quality_check,
				'remarks'=>$callConfig->real_escape_string($remark),
				'is_active'=>0,
				'current_status'=>0,
				'created_by'=>$user_id,
				'created_date'=>date('Y-m-d H:i:s')
			));
			$detail_ids[] = $insert;
			if($is_required_result){
				$checked_count++;
				if($quality_check == 1){ $failed_count++; }
			}
		}
		$required_count = $this->getProcessQcParameterCount($context['mapping']);
		$qc_status = $complete ? ($failed_count > 0 ? 3 : 2) : ($checked_count > 0 ? 1 : 0);
		$callConfig->updateRecord('application_table_quality_check_items', array(
			'qc_status'=>$qc_status,
			'required_count'=>$required_count,
			'checked_count'=>$checked_count,
			'failed_count'=>$failed_count,
			'inspected_by'=>$user_id,
			'inspected_date'=>date('Y-m-d H:i:s'),
			'updated_by'=>$user_id,
			'updated_date'=>date('Y-m-d H:i:s')
		), 'id', $progress_id);
		$old_head = $callConfig->getRow("SELECT id FROM item_head_quality_check WHERE company_id = '{$company_id}' AND application_table_name = '".$callConfig->real_escape_string($context['process'])."' AND transaction_id = ".intval($context['tranid'])." AND item_id = ".intval($context['item_id'])." AND is_active = 0");
		if(!empty($old_head->id)){
			$callConfig->executeQuery("UPDATE item_head_quality_check SET is_active = 1 WHERE id = ".intval($old_head->id));
		}
		$summary = $this->getProcessQcSummaryData($company_id, $context['process'], $context['tranid'], $context['item_id']);
		$head_quality_check = ($summary['failed'] > 0) ? 2 : (($summary['total'] > 0 && $summary['pending'] == 0 && $summary['draft'] == 0) ? 1 : 0);
		$callConfig->insertRecord('item_head_quality_check', array(
			'company_id'=>$company_id,
			'application_table_name'=>$callConfig->real_escape_string($context['process']),
			'transaction_id'=>intval($context['tranid']),
			'item_id'=>intval($context['item_id']),
			'quality_check'=>$head_quality_check,
			'remarks'=>'Process QC summary: Passed '.$summary['passed'].', Failed '.$summary['failed'].', Draft '.$summary['draft'].', Pending '.$summary['pending'],
			'is_active'=>0,
			'current_status'=>0,
			'created_by'=>$user_id,
			'created_date'=>date('Y-m-d H:i:s')
		));
		$callConfig->updateRecord('application_table_quality_check', array(
			'quality_check'=>$head_quality_check,
			'remarks'=>'Process QC summary: Passed '.$summary['passed'].', Failed '.$summary['failed'].', Draft '.$summary['draft'].', Pending '.$summary['pending'],
			'updated_by'=>$user_id,
			'updated_date'=>date('Y-m-d H:i:s')
		), 'id', $context['header']->id);
		$inventory_posting = array();
		if($complete && $context['process'] == 'manufacturing_product_notes' && $head_quality_check == 1){
			include_once("model/manfacturing_forecast.class.php");
			if(class_exists('manfacturingForecastUserClass')){
				$manufacturingObj = new manfacturingForecastUserClass();
				$inventory_posting = $manufacturingObj->postManufacturingProductNoteInventoryById($company_id, $user_id, intval($context['tranid']));
			}
		}
		$next = $callConfig->getRow("SELECT id FROM application_table_quality_check_items WHERE company_id = '{$company_id}' AND application_table_name = '".$callConfig->real_escape_string($context['process'])."' AND transaction_id = ".intval($context['tranid'])." AND item_id = ".intval($context['item_id'])." AND qc_status IN (0,1) AND is_active = 0 ORDER BY id ASC LIMIT 0,1");
		return array(
			'status'=>'success',
			'message'=>$complete ? 'QC completed successfully.' : 'QC draft saved successfully.',
			'summary'=>$summary,
			'details'=>$detail_ids,
			'inventory_posting'=>$inventory_posting,
			'next_progress_id'=>intval($next->id ?? 0)
		);
	}

	function saveprocessqcitemdraft($post, $get){
		return $this->saveProcessQcItem($post, $get, false);
	}

	function completeprocessqcitem($post, $get){
		return $this->saveProcessQcItem($post, $get, true);
	}

	//manufacturing related end
	function getProjectEmployeeTaskReport($get){
		
		global $callConfig;
		$return = array();
		$return['projects'] = array();
		$return['employees'] = array();
		$return['tasks'] = array();

		$company_id = isset($get['company_id']) ? $get['company_id'] : '';
		if($company_id == ''){
			return $return;
		}
		
		$projects_sql = "SELECT id, project_name FROM projects WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY project_name ASC";
		$projects_res = $callConfig->getAllRows($projects_sql);

		if(count((array)$projects_res) > 0){
			foreach($projects_res as $project_row){
				$return['projects'][] = $project_row;
			}
		}
		
		$employees_sql = "SELECT id, display_name, image FROM employees WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY display_name ASC";
		$employees_res = $callConfig->getAllRows($employees_sql);
		if(count((array)$employees_res) > 0){
			foreach($employees_res as $employee_row){
				$return['employees'][] = $employee_row;
			}
		}

		$filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
		if($filters_only == 'Y'){
			return $return;
		}

		$from_date = isset($get['from_date']) ? $get['from_date'] : '';
		$to_date = isset($get['to_date']) ? $get['to_date'] : '';
		if($from_date == '' || $to_date == ''){
			return $return;
		}

		$where = " ptd.company_id = '".$company_id."' AND p.company_id = '".$company_id."' AND e.company_id = '".$company_id."' AND ptd.is_active = 0 AND p.is_active = 0 AND e.is_active = 0 AND ptd.assigen_id = e.id AND ptd.project_id = p.id AND ptd.planned_start <= '".$to_date."' AND ptd.planned_finish >= '".$from_date."' ";

		$project_id = isset($get['project_id']) ? $get['project_id'] : '';
		if($project_id != ''){
			$where .= " AND ptd.project_id = '".$project_id."' ";
		}

		$employee_id = isset($get['employee_id']) ? $get['employee_id'] : '';
		if($employee_id != ''){
			$where .= " AND ptd.assigen_id = '".$employee_id."' ";
		}

		$tasks_sql = "SELECT e.display_name, e.emp_code, e.image, p.project_name, ptd.task_name, ptd.id AS task_id, ptd.planned_start, ptd.planned_finish, ptd.current_status FROM project_task_details ptd, projects p, employees e WHERE ".$where." ORDER BY e.display_name ASC, p.project_name ASC, ptd.planned_start ASC";
		$return['sql'] = $tasks_sql;
		$tasks_res = $callConfig->getAllRows($tasks_sql);
		if(count((array)$tasks_res) > 0){
			foreach($tasks_res as $task_row){
				$return['tasks'][] = $task_row;
			}
		}
		$query = "SELECT name, action_label,status FROM project_status WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY status ASC";
		$res = $callConfig->getAllRows($query);

		if(count((array)$res) > 0){
			foreach($res as $row){
				$return['project_status'][$row->status] = $row;
			}
		}

		return $return;
	}
	function togenratesalarytemplatesoot($get){
		global $callConfig;
		print_r($get); exit;
		/*$empqual = "SELECT * FROM edi_data_new WHERE id>=1 AND id<66 AND field16 !='' AND field17=''";
		$empres= $callConfig->getAllRows($empqual);
		foreach($empres as $emp){
			$query_sal = "INSERT INTO `salary_template`( `company_id`, `employee_id`, `emp_code` ,`description`, `yearly_gross_salary`, `ctc_monthly`, `ctc_yearly`, `note`, `is_active`, `current_status`) SELECT  `company_id`, '".$emp->file_name."', '".$emp->field3."' , `description`, '".$emp->field16*12."' , '".$emp->field16."' , '".$emp->field16*12."' , `note`, `is_active`, `current_status` FROM `salary_template` WHERE id=2";
			echo $query_sal ; exit; 
			$insert_task = $callConfig->executeQuery($query_sal);
			$salay_id = $insert_task['LASTID'];
			$resins['main'][] = $query_sal;
			$resins['main_id'][] = $salay_id;
			$quer_com ="INSERT INTO `salary_template_components`( `company_id`, `salary_template_id`, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, `monthly_amount`, `yearly_amount`, `is_active`, `current_status`) select `company_id`, '".$salay_id."' as salary_template_id, `component_id`, `component_cat_id`, `component_name`, `component_category`, `component_type`, `component_number`, '".$emp->field16."' , '".$emp->field16*12."', `is_active`, `current_status` FROM salary_template_components where salary_template_id = 2";
			$insert_task = $callConfig->executeQuery($quer_com);
			$resins['sub_qu'][] = $quer_com;
			$resins['sub_id'][] = $insert_task;

		}
		return $resins;*/ 
	}
}
?>