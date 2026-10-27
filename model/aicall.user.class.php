<?php
class aicallUserClass extends userClass{
	function getUserConfiguration($get){
		global $callConfig;
		$emailmobile = $get['emailmobile'];
		$query = "SELECT * FROM ".MAINDB.".ai_configurations WHERE email_id = '".$emailmobile."' OR  mobile LIKE '%".$emailmobile."%'";
		$rowdata = $callConfig->getrow($query);	
	   	if($rowdata->zqr_connect !=""){

		   	$sql = "SELECT * FROM ".MAINDB.".".TBL_DATABASE_ALLOCATIONS." WHERE id='".$rowdata->zqr_connect."' and  is_active='0'";
	   		$row_db=$callConfig->getRow($sql);
		   	$dbname = $row_db->db_name;		
		   	$sql = "SELECT * FROM ".$dbname.".application_users WHERE user_email='".$rowdata->email_id."' AND is_active='0'";
	   		$auser=$callConfig->getRow($sql);
	   		
	   		return array('stause'=>'success', 'data'=>array('data_name'=>$dbname,'company_id'=>$auser->company_id,'user_id'=>$auser->id,'mode'=>$rowdata->mode,'identifier'=>$emailmobile,'recruiter_id'=>$auser->id,'folder'=>'resume_rm_'.$auser->id));
	   	}else{
   			return array('stause'=>"fail", 'message'=>'Data Not Found');
   		}

	}
	function aijobtitlecandidates($get){
		global $callConfig;
		$return = array();
		$post_id = $get['job_post_id'];
		$query = "SELECT `id`, `company_id`, `requested_by`, `requested_date`, `required_date`, `recruitment_manager`, `job_code`, `job_number`, `job_name`,  FROM_BASE64(job_desc) AS job_desc, `min_exp`, `max_exp`, `no_of_resources`, `min_salary`, `max_salary`, `employment_mode`,`question`, `esgin_budget_approval_id`, `total_resource_cost`, `job_responsibility`, `department_id`, `employment_type`, `job_status`, `posting_type`FROM `job_postings` WHERE id=".$post_id;
		$rowdata = $callConfig->getrow($query);
		$return['rowdata'] = (array)$rowdata;

		$query = "SELECT `id`,  `candidate_name`, `candidate_email`, `candidate_phone`, `current_location`, `preferred_location`, `experience`, `current_salary`,  CONCAT('https://dev.infistaq.in/uploads/all/', file) as file, `remarks` FROM `job_posting_candidates` WHERE company_id =1 AND job_posting_id =".$post_id." AND is_active = 0";
		$candates = $callConfig->getAllRows($query);
		$return['rowdata']['candates'] = $candates;

		return $return;
	}
	function aiToGetRecruitmentData($get){
		global $callConfig;
		$return = array();
		$post_id = $get['job_post_id'];
		$gettype = $get['gettype'];
		if($gettype == "getJobPostings"){
			$query = "SELECT jp.id, jp.company_id, jp.job_number, jp.job_name, jp.job_desc, jp.job_responsibility, jp.requested_date, jp.required_date, jp.min_exp, jp.max_exp, jp.no_of_resources, jp.min_salary, jp.max_salary, jp.question, jp.candidate_mandatory_field FROM job_postings jp WHERE jp.is_active = 0";
			$rowdata = $callConfig->getAllrows($query);
			$job_postings = array();
			foreach($rowdata as $rowdef){
				$rowdef->job_desc = base64_decode($rowdef->job_desc);	
				$job_postings[$rowdef->id] = $rowdef;
			}
			$return['job_postings'] = $job_postings;
		}elseif($gettype == "getcandidates"){
			$query = "SELECT can.id, can.first_name, can.last_name, can.display_name, can.address, can.phone, can.email_id FROM candidates can WHERE is_active = 0";
			$rowdata = $callConfig->getAllrows($query);
			$return['candidates'] = $rowdata;
		}elseif($gettype == "getsinglecandidates"){
			$candidate_id = $get['candidate_id'];
			$job_postings = $get['job_postings'];
			$query = "SELECT can.id, can.first_name, can.last_name, can.display_name, can.address, can.phone, can.email_id FROM candidates can WHERE id = '".$candidate_id."' AND is_active = 0";
			$rowdata = $callConfig->getrow($query);
			$return['candidate'] = $rowdata;
		
			$job_postings = $get['job_posting_id'];
			$query = "SELECT jp.id, jp.company_id, jp.job_number, jp.job_name, jp.job_desc, jp.job_responsibility, jp.requested_date, jp.required_date, jp.min_exp, jp.max_exp, jp.no_of_resources, jp.min_salary, jp.max_salary, jp.question, jp.candidate_mandatory_field FROM job_postings jp WHERE jp.id = '".$job_postings."' AND jp.is_active = 0";
			$rowdata = $callConfig->getrow($query);
			$return['job_postings'] = $rowdata;
		}
		return $return;
	}
	function aitoaddcandidatedetails($get, $data){
		global $callConfig;
		$return = array();
		$insert_array = array(
			'company_id'                => $get['company_id'],
		    'first_name'                => $data['first_name'],
		    'middle_name'               => $data['middle_name'],
		    'last_name'                 => $data['last_name'],
		    'display_name'              => $data['display_name'],
		    'address'                   => $data['address'],
		    'present_address'           => $data['present_address'],
		    'height'                    => $data['height'],
		    'weight'                    => $data['weight'],
		    'post_code'                 => $data['post_code'],
		    'phone'                     => $data['phone'],
		    'emergency_contact_number'  => $data['emergency_contact_number'],
		    'email_id'                  => $data['email_id'],
		    'gender'                    => $data['gender'],
		    'disability'                => $data['disability'],
		    'date_of_birth'             => $this->dateconvertdbformat($data['date_of_birth']),
		    'wedding_date'              => $this->dateconvertdbformat($data['wedding_date']),
		    'identification_mark'       => $data['identification_mark'],
		    'hobbies'                   => $data['hobbies'],
		    'father_name'               => $data['father_name'],
		    'total_experiance'          => $data['total_experiance'],
		    'notice_period'             => $data['notice_period'],
		    'excepted_doj'              => $data['excepted_doj'],
		    'candidate_experiances'     => $data['candidate_experiances'],
		    'candidate_qualifications'  => $data['candidate_qualifications'],
		    'employee_dependents'       => $data['employee_dependents'],
		    'current_location'          => $data['current_location'],
		    'reason_for_change'         => $data['reason_for_change'],
		    'relocate'                  => $data['relocate'],
		    'current_ctc'               => $data['current_ctc'],
		    'expected_ctc'              => $data['expected_ctc'],
		    'candidate_awards'          => $data['candidate_awards'],
		    'skills'                    => $data['skills'],
		    'bio'                       => $data['bio'],
		    'is_active'                 => '0',
			'current_status'            => '0',
			'created_by'                => $get['user_id'],
			'file'               => $data['resume_file'],
			'rm_id' => $get['user_id']
		);
		$insert = $callConfig->insertRecord('candidates', $insert_array);
		$res['candidates'] = $insert;
		$candidate_id =  $insert['LASTID'];
		if($data['match_ratio']>50 && $data['job_post_id'] !=''){
			$job_post_id = json_decode($data['job_post_id'],1);
			foreach ($job_post_id as $post_id) {				
				$insertData = array(
				    'company_id'                  => $get['company_id'],
				    'job_posting_id'              => $post_id,
				    'candidate_id'                => $candidate_id,
				    'candidate_name'              => $data['display_name'],
				    'candidate_email'             => $data['email_id'],
				    'candidate_phone'             => $data['phone'],
				    'current_location'            => $data['current_location'],
				    'preferred_location'          => $data['preferred_location'],
				    'experience'                  => $data['total_experiance'],
				    'ratting'                     => $data['ratting'],
				    'remarks'                     => json_encode($data),
				    'is_active'                   => '0',
				    'current_status'              => '0',
				    'created_by'                  => $get['user_id']
				);
			}
			$inserts = $callConfig->insertRecord('job_posting_candidates', $insertData);
			$res['job_posting_candidates'][] = $inserts;
		}
		return $res;
	}



	function startCandidateScreeningQuestionsAndAnswers($get, $post){
		global $callConfig;
		$return = array();
/**
		$query = "SELECT * FROM api_hits can WHERE id = 613";
		$rowdata = $callConfig->getrow($query);
		$decores = $api_response_details = json_decode(urldecode($rowdata->api_response_details),1);
		print_r($api_response_details);

**/	
		$decores = json_decode($post['data'],1);
		$insertarray = array('company_id'=>$_POST['company_id'],'application_table'=>'candidate_screening_questions_and_answers','transaction_id'=>'','api_master_id'=>1000,'api_url'=>'abc','api_request_parameters'=>json_encode((array)$get),'request_date_time'=>date("Y-m-d H:i:s"),
			'api_response_details'=>urlencode(json_encode($decores)),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$_POST['user_id'],'response_status'=>200);
       	$inserts = $callConfig->insertRecord('api_hits', $insertarray);
        $return['inserts'] = $inserts;
		$screening = $decores;
		foreach ($screening['questions'] as $questions) {				
			$insertData = array('company_id'=>$get['company_id'], 'candidate_id' => $screening['candidate_id'], 'job_posting_id'=>$screening['job_posting_id'],'question_key' => $questions['key'],'questions' => $questions['question'], 'is_active'=>'0','current_status'=> '0','created_by'=>$get['user_id'], 'created_date'=>date('Y-m-d h:i:s'));
			$inserts = $callConfig->insertRecord('candidate_screening_questions_and_answers', $insertData);
			$return['job_posting_candidates'][] = $inserts;
		}

		$where = " candidate_id = '".$screening['candidate_id']."' AND job_posting_id = '".$screening['job_posting_id']."' AND is_active = 0";
 		$insert_array = array('screening_status' =>'Y', 'screening_start_datetime'=>date('Y-m-d h:i:s'), 'updated_by'=>$get['user_id'], 'updated_date'=>date('Y-m-d h:i:s'));
		$callConfig->updateRecord('job_posting_candidates', $insert_array, '', '', $where);

		return $return;
	}
	function endCandidateScreeningQuestionsAndAnswers($get, $post){
		global $callConfig;
		$return = array();
		//$return['post'] = $post;
		//$return['get'] = $get;
/**
		$query = "SELECT * FROM api_hits can WHERE id = 614";
		$rowdata = $callConfig->getrow($query);
		$decores = $api_response_details = json_decode(urldecode($rowdata->api_response_details),1);
		print_r($decores);
		$screening = $decores;
/**//**/
		$decores = json_decode($post['data'],1);
		$insertarray = array('company_id'=>$_POST['company_id'],'application_table'=>'candidate_screening_questions_and_answers','transaction_id'=>'','api_master_id'=>1001,'api_url'=>'abc','api_request_parameters'=>json_encode((array)$get),'request_date_time'=>date("Y-m-d H:i:s"),'api_response_details'=>urlencode(json_encode($decores)),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$_POST['user_id'],'response_status'=>200);
       	$inserts = $callConfig->insertRecord('api_hits', $insertarray);
       	$return['inserts'] = $inserts;
    	$screening = $decores;
		foreach ($screening['answers'] as $messages) {
				$question_key = $messages['key'];
				$answer = $messages['answer'];
				$where = " candidate_id = '".$screening['candidate_id']."' AND job_posting_id = '".$screening['job_posting_id']."' AND question_key = '".$question_key."' AND is_active = 0";
		 		$insert_array = array('answers' => $answer, 'updated_by'=>$get['user_id'], 'updated_date'=>date('Y-m-d h:i:s'));
				$insert_data = $callConfig->updateRecord('candidate_screening_questions_and_answers', $insert_array, '', '', $where);
				$return['job_posting_candidates'][] = $insert_data;			
		}
	
		$recommendation = $screening['evaluation']['recommendation'];
		$summary = $screening['evaluation']['summary'];
		$where = " candidate_id = '".$screening['candidate_id']."' AND job_posting_id = '".$screening['job_posting_id']."' AND is_active = 0";
 		$insert_array = array('screening_end_datetime'=>date('Y-m-d h:i:s'), 'ai_shortlisted'=>$recommendation,'ai_summary'=>$summary,  'updated_by'=>$get['user_id'], 'updated_date'=>date('Y-m-d h:i:s'));
		$return['job_posting_candidates1'] = $callConfig->updateRecord('job_posting_candidates', $insert_array, '', '', $where);

		return $return;
	}

//	,'startCandidateScreeningQuestionsAndAnswers'=>'startcandidatescreeningquestionsandanswers','endcandidatescreeningquestionsandanswers'=>'endCandidateScreeningQuestionsAndAnswers'
	
}
