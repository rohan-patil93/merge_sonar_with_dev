<?php
class surveyPollUserClass extends userClass
{
	/*------- SURVEY SURVEY Start ---*/
	function getsurveydetails($get){

		global $callConfig;
		$whr="company_id=".$get['company_id']." and is_active='0'";
		$query=$callConfig->selectQuery('employees','id,display_name',$whr,'','','');
		$details['employeedetails'] = $callConfig->getAllRows($query);

		$whr="company_id=".$get['company_id']." and is_active='0' and id=".$get['survey_id'];
		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP,'*',$whr,'','','');
		$details['survey_details'] = $callConfig->getRow($query);

		$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id'];
		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'*',$whr,'','','');
		$details['survey_qustion_details'] = $callConfig->getAllRows($query);
        	//print_r($whr); exit;

		$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id'];
		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTION_OPTIONS,'*',$whr,'','','');
		$details['survey_option_details'] = $callConfig->getAllRows($query);

		$whr1="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_TYPES'"." AND genc_detail_code!='".EXITE_INTERVIEW_TYPE."'";
		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr1,'','','');
		$details['survey_types'][1] = $callConfig->getAllRows($query);


		$whr1="company_id=".$get['company_id']." and is_active='0' and genc_code='POLL_TYPES'";
		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr1,'','','');
		$details['survey_types'][2] = $callConfig->getAllRows($query);

		$whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
		$details['qustion_types'] = $callConfig->getAllRows($query);
     //  echo '<pre>'; print_r($details['survey_qustion_details']); exit;
		return $details;
	}


	function createsurveys($post,$get){

		global $callConfig;
		$rowid=$get['rowid'];
		$table_setup = $get['table_setup'];
		$table_questions = $get['table_questions'];
		$table_options = $get['table_options'];
		$main = $post['main'];
		$quesion_data = $post['qustion'];

		$whr="company_id=".$get['company_id']." and is_active='0' and id=".$main['survey_assigned_to'];
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
		// $main['assigned_to_employees'] = json_encode($shared_idslist,1);
		$main['assigned_to_employees'] = json_encode((array)$shared_idslist);
		$main['assigned_to_count']  = count((array)$shared_idslist);
    	//print_r($main);exit;
		if($rowid=="") {
			$array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date']);
			$res=$callConfig->insertRecord($table_setup,$main);
			$s_id = $res['LASTID'];
			foreach($shared_ids1 as $ids){
				$response_array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date'],'employee_id'=>$ids['id_value'],'survey_id'=>$s_id,'category'=>$main['category']);
				$res=$callConfig->insertRecord(TBL_SURVEY_RESPONSES,$response_array);
			}
			foreach ($quesion_data as $key => $value) {
				$value['survey_id'] = $s_id;
				$survey_question_option['survey_question_option'] = $value['survey_question_option'];
				$survey_question_option['survey_id'] = $s_id;
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
			$qrespons=$callConfig->updateRecord($table_questions,$setisactive,'survey_id',$rowid);
			$qrespons=$callConfig->updateRecord($table_options,$setisactive,'survey_id',$rowid);
			$qrespons=$callConfig->updateRecord(TBL_SURVEY_RESPONSES,$setisactive,'survey_id',$rowid);
			foreach($shared_ids1 as $ids){
				$response_array = array('company_id'=>$main['company_id'],'is_active'=>'0','created_by'=>$main['created_by'],'created_date'=>$main['created_date'],'employee_id'=>$ids['id_value'],'survey_id'=>$rowid,'category'=>$main['category']);
				$res=$callConfig->insertRecord(TBL_SURVEY_RESPONSES,$response_array);
			}
			foreach ($quesion_data as $key => $value) {
    			//if(qustionid!='') thn update is_active=0 and values questintabe
    			//update is_active=0 and valus in option table conditoon survey id and question id
    			//else = insert to table is_active=0
				$value['survey_id'] = $rowid;
				$survey_question_option['survey_question_option'] = $value['survey_question_option'];
				$survey_question_option['survey_id'] = $rowid;
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
    	function getsharedsurveydetails($get){
    		global $callConfig;
    		$whr="company_id=".$get['company_id']." and is_active='0' and id=".$get['survey_id'];
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP,'*',$whr,'','','');
    		$details['survey_details'] = $callConfig->getRow($query);

    		$whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
    		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
    		$details['qustion_types'] = $callConfig->getAllRows($query);

    		$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id'];
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'*',$whr,'','','');
    		$survey_qustion_details = $callConfig->getAllRows($query);
    		$value_id = 0;
    		foreach ($survey_qustion_details as $value) {
    			$value_id++;
    			$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id']." and question_id=".$value->id;
    			$query_ans=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTION_OPTIONS,'*',$whr,'','','');
    			$survey_answer_details = $callConfig->getRow($query_ans);
    			$disp_question[$value_id]['queid'] = $value->id;
    			$disp_question[$value_id]['survey_question'] = $value->survey_question;
    			$disp_question[$value_id]['options_display_type'] = $value->options_display_type;
    			$disp_question[$value_id]['require_response'] = $value->require_response;
    			$disp_question[$value_id]['allow_multiple_selections'] = $value->allow_multiple_selections;
    			$disp_question[$value_id]['survey_question_option'] = $survey_answer_details->survey_question_option;
    		}

    		$queryresponse = "SELECT * FROM ".TBL_SURVEY_RESPONSE_DETAILS." WHERE is_active=0 and company_id=".$get['company_id']." and survey_id=".$get['survey_id']." and employee_id=".$get['emp_id'] ; 
    		$response = $callConfig->getAllRows($queryresponse);
    		foreach($response as $result){
    			$resultdata[$result->question_id] = $result->response_option;
    		}
    		$details['resultdata'] = $resultdata;
    		$details['questions'] = $disp_question;


    		return $details;
    	}
    	function savesurveyreponses($post, $get) {
    		global $callConfig;
    	// set is_active 1 bsed on survey id and empid
    		$updatearray = array('is_active'=>'1');
    		$whr = "survey_id=".$post['survey_response']['survey_id']." and employee_id=".$post['survey_response']['employee_id'];
    		$res=$callConfig->updateRecord(TBL_SURVEY_RESPONSES,array('is_active'=>'1'),'','',$whr);
    		$res=$callConfig->updateRecord(TBL_SURVEY_RESPONSE_DETAILS,array('is_active'=>'1'),'','',$whr);
    		$qresponseid=$callConfig->insertRecord(TBL_SURVEY_RESPONSES,$post['survey_response']);

    		for($i=0;$i<count((array)$post['survey_response_details']);$i++){
    			$post['survey_response_details'][$i]['survey_response_id'] = $qresponseid['LASTID'];
    			$qresponse=$callConfig->insertRecord(TBL_SURVEY_RESPONSE_DETAILS,$post['survey_response_details'][$i]);
    		}
    		return $qresponse;
    	}
    	function getsurveysummary($get){
    		global $callConfig;
			
    		if($get['survey_type']==EXITE_INTERVIEW_TYPE){
    			$condition = " AND survey_type ='".$get['survey_type']."'";
    		}else{
    			$condition = " AND survey_type!='".EXITE_INTERVIEW_TYPE."'";
    		} 
    		$tag1 = $get['category'] ==1 ?'Surveys' : 'Polls';
    		$tag2= $get['category'] ==1 ?'Surveyed' : 'Collectors';

    		$queryresponse = "SELECT count(id) as total_surveys, sum(assigned_to_count) as collectors  FROM ".TBL_SURVEY_SETUP." WHERE is_active=0 and company_id=".$get['company_id']." and category=".$get['category'] ; 
    		$sresponse = $callConfig->getRow($queryresponse); 
    		$details['summary'][] = array('data'=>$sresponse->total_surveys,'name'=>$tag1,'bgcolor'=>'primary');
    		$details['summary'][] = array('data'=>$sresponse->collectors,'name'=>$tag2,'bgcolor'=>'secondary');
    		$queryresponse = "SELECT count(id) as total_responses,round(sum(duration_taken)/count(id),2) as avg_duration_taken  FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and category=".$get['category']." and current_status=".ST_COMPLETE ; 
    		$response = $callConfig->getRow($queryresponse);
    		$details['summary'][] = array('data'=>$response->total_responses==''?'0':$response->total_responses,'name'=>'Responses','bgcolor'=>'success');
			
    		$queryresponse = "SELECT count(id) as partial  FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and category=".$get['category']." and current_status=".ST_INPROGRESS; 
    		$presponse = $callConfig->getRow($queryresponse);

    		$details['summary'][] = array('data'=>$presponse->partial==''?'0':$presponse->partial,'name'=>'Partial','bgcolor'=>'warning');
    //	$details['summary'][] = array('data'=>($sresponse->total_surveys-$response->total_responses)==''?'0':($sresponse->collectors-$response->total_responses),'name'=>'Incomplete','bgcolor'=>'info');
			
    		$details['summary'][] = array('data'=>$response->avg_duration_taken==''?'0':$response->avg_duration_taken,'name'=>'Avg Time','bgcolor'=>'dark');
    		if($sresponse->total_surveys>0){
    			$rate = round(($response->total_responses/$sresponse->total_surveys*100),2);
    		}else{
    			$rate = "0";
    		}
    		$details['rate']= $rate." %";
    		

    		$whr="company_id=".$get['company_id']." and is_active='0' and category=".$get['category'].$condition;
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP,'*',$whr,'','','');
    		$response = $callConfig->getAllRows($query);
			// return $response;
    		foreach($response as $svalues){
    			$surveyvalues = array('survey_id'=>$svalues->id,'survey_name'=>$svalues->survey_name,'survey_start_date'=>date('d M, Y',strtotime($svalues->survey_start_date)),'survey_end_date'=>date('d M, Y',strtotime($svalues->survey_end_date)),'current_status'=>$svalues->current_status);
    			$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$svalues->id;
    			$questions=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'count(id) as totalquestion',$whr,'','','');
    			$qresponse = $callConfig->getRow($questions);
    			$surveyvalues['totalquestion'] = $qresponse->totalquestion;
    			$surveyed = $svalues->assigned_to_count;
    			$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$svalues->id." and category=".$get['category']." and current_status=".ST_COMPLETE  ;
    			$questions=$callConfig->selectQuery(TBL_SURVEY_RESPONSES,'count(id) as totalresponse',$whr,'','','');
    			$qresponse = $callConfig->getRow($questions);
				
				
    			if($qresponse->totalresponse && $qresponse->totalresponse>0 && $surveyed>0){
    				$completionrate = round(($qresponse->totalresponse/$surveyed*100),2);
    			}else{
    				$completionrate = "0";
    			}

    			$surveyvalues['totalresponse'] = $qresponse->totalresponse == '' ? '0' : $qresponse->totalresponse;
    			$surveyvalues['completionrate'] = $completionrate == '' ? "0 %" : $completionrate." %";

    			if($completionrate==100){
    				$surveyvalues['completionratecolor'] = "success";
    			}else if($completionrate>=80){
    				$surveyvalues['completionratecolor'] = "primary";
    			}
    			else if($completionrate>=60 && $completionrate <80){
    				$surveyvalues['completionratecolor'] = "warning";
    			}
    			else if($completionrate>=50 && $qresponse->completionrate <60){
    				$surveyvalues['completionratecolor'] = "info";
    			}
    			else {
    				$surveyvalues['completionratecolor'] = "danger";
    			}
    			$details['survey_details'][] = $surveyvalues;
				
    		}
			return $details;
			
    		$whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
    		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
    		$details['qustion_types'] = $callConfig->getAllRows($query);
    		foreach($details['qustion_types'] as $qustion_types){
    			$qtype[$qustion_types->genc_detail_code] = $qustion_types->genc_detail_name;

    			$whr="company_id=".$get['company_id']." and is_active='0' and options_display_type=".$qustion_types->genc_detail_code;
    			$questions=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'count(id) as totaltypequestion',$whr,'','','');
    			$qresponse = $callConfig->getRow($questions);
    			$survey_qtypes[$qustion_types->genc_detail_name] = $qresponse->totaltypequestion;
    		}
    		$details['survey_qtypes'] = $survey_qtypes;
    		return $details;

    	}

    	function getsurveylist($get){
    		global $callConfig;
    		$queryresponse = "SELECT * FROM ".TBL_SURVEY_SETUP." WHERE is_active=0 and company_id=".$get['company_id']." and id=".$get['survey_id']." and category=".$get['category'] ; 
    		$response = $callConfig->getRow($queryresponse);
    		$details['overview'] = $response;

    		$queryresponse = "SELECT id,display_name FROM ".TBL_EMPLOYEES." WHERE is_active=0 and company_id=".$get['company_id']; 
    		$response = $callConfig->getAllRows($queryresponse);
    		foreach($response as $emp){
    			$employees[$emp->id] = $emp->display_name;
    		}
    		$details['employees'] = $employees;

    		$queryresponse = "SELECT count(id) as total_surveys_responses FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and survey_id=".$get['survey_id']." and current_status=".ST_COMPLETE;
    		$response = $callConfig->getRow($queryresponse);
    		$details['no_of_responds'] = $response->total_surveys_responses==''? '0': $response->total_surveys_responses;

    		$queryresponse = "SELECT count(id) as total_responses FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and survey_id=".$get['survey_id'];
    		$response = $callConfig->getRow($queryresponse);
    		$total_responses = $response->total_responses==''? '0': $response->total_responses;
    		if($total_responses>0){
    			$details['rate']= round($details['no_of_responds']/$total_responses *100 ,2);
    		}else
    		{
    			$details['rate'] = "0 ";
    		}
    		$queryresponse = "SELECT * FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and survey_id=".$get['survey_id']." group by employee_id"; 
    		$response = $callConfig->getAllRows($queryresponse);
    		$details['response'] = $response;
    	//echo "<pre>"; print_r($details);
    		return $details;
    	}


    	function getsurveyresult($get){
    		global $callConfig;
    		$queryresponse = "SELECT * FROM ".TBL_SURVEY_RESPONSE_DETAILS." WHERE is_active=0 and company_id=".$get['company_id']." and survey_id=".$get['survey_id']." and employee_id=".$get['survey_emp_id'] ; 
    		$response = $callConfig->getAllRows($queryresponse);
    		foreach($response as $result){
    			$resultdata[$result->question_id] = $result->response_option;
    		}
    		$details['resultdata'] = $resultdata;


    		$whr="company_id=".$get['company_id']." and is_active='0' and id=".$get['survey_id'];
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP,'*',$whr,'','','');
    		$details['survey_details'] = $callConfig->getRow($query);

    		$whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
    		$query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
    		$details['qustion_types'] = $callConfig->getAllRows($query);

    		$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id'];
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'*',$whr,'','','');
    		$survey_qustion_details = $callConfig->getAllRows($query);
    		foreach ($survey_qustion_details as $value) {
    			$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id']." and question_id=".$value->id;
    			$query_ans=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTION_OPTIONS,'*',$whr,'','','');
    			$survey_answer_details = $callConfig->getRow($query_ans);
    			$disp_question[$value->id]['survey_question'] = $value->survey_question;
    			$disp_question[$value->id]['options_display_type'] = $value->options_display_type;
    			$disp_question[$value->id]['require_response'] = $value->require_response;
    			$disp_question[$value->id]['allow_multiple_selections'] = $value->allow_multiple_selections;
    			$disp_question[$value->id]['survey_question_option'] = $survey_answer_details->survey_question_option;

    		}

    		$details['questions'] = $disp_question;

    		return $details;
    	}	


    	function getsurveyreport($get){
    		global $callConfig;
    		$Qtypes = array('MultilpleChoice'=>'1','Rating'=>'2','Ranking'=>'3','FreeText'=>'4');

    		$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id'];
    		$query=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'*',$whr,'','','');
    		$survey_qustion_details = $callConfig->getAllRows($query);
    		$maxoptions = 0;
    		foreach ($survey_qustion_details as $value) {
    			$detailoptions = array();
    			$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id']." and question_id=".$value->id;
    			$query_ans=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTION_OPTIONS,'*',$whr,'','','');
    			$survey_answer_details = $callConfig->getRow($query_ans);
    			$details['question_id'][$value->id] = $value->id;
    			$details['survey_question'][$value->id] = $value->survey_question;
    			$qtype = $value->options_display_type;
    			if($qtype==$Qtypes['MultilpleChoice'] || $qtype==$Qtypes['Ranking']){
    				$detailoptions = json_decode($survey_answer_details->survey_question_option,1);
    			}
    			else if($qtype==$Qtypes['Rating']){
    				$rating = json_decode($survey_answer_details->survey_question_option,1)[0];
    				for($no=1; $no<=$rating; $no++){
    					$detailoptions[] = $no;
    				}
    			}else{
    				$freetext = json_decode($survey_answer_details->survey_question_option,1)[0];
    				$detailoptions[] = "Remarks";
    			}
    			$optionid = 0;
    			$maxoptions = count((array)$detailoptions) > $maxoptions ? count((array)$detailoptions) : $maxoptions;
    			foreach($detailoptions as $optionsdata) { 
    				$details['option'][$value->id]['option'][] = $optionsdata;
    				$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$get['survey_id']." and question_id=".$value->id;
    				$query_opt=$callConfig->selectQuery(TBL_SURVEY_RESPONSE_DETAILS,'*',$whr,'','','');
    				$query_opt_details = $callConfig->getAllRows($query_opt);
    				$count=0;
    				if($qtype!=4){
    					foreach($query_opt_details as $opt){ 
    						$response = json_decode($opt->response_option,1); 
    						if(in_array($optionsdata, (array)$response)){
    							$count = $count+1;;
    						}
							// else{
    						// 	$count=0;
    						// }
    					}
    				}else{
    					foreach($query_opt_details as $opt){ 
    						$response = json_decode($opt->response_option,1)[0]; 
    						if($response!=''){
    							$count = $count+1;;
    						}else{
    							$count=0;
    						}
    					}
    				}
    				$details['option'][$value->id]['answer'][$optionid] = $count;
    				$optionid++;
    			}
    		}
    		$details['maxoptions'] = $maxoptions;
    	//echo "<pre>"; print_r($details);
    		return $details;
    	}

    	function getempsurvey($get){
    		global $callConfig;
    		$tag1 = $get['category'] ==1 ?'Surveys' : 'Polls';

    		$queryresponse = "SELECT survey_id,current_status FROM ".TBL_SURVEY_RESPONSES." WHERE is_active=0 and company_id=".$get['company_id']." and category=".$get['category']." and employee_id=".$get['emp_id']; 
    		$survey_list = $callConfig->getAllRows($queryresponse);

    		foreach($survey_list as $list){
    			$whr="company_id=".$get['company_id']." and is_active='0' and category=".$get['category']." and id=".$list->survey_id." AND survey_type!='".EXITE_INTERVIEW_TYPE."'";
    			$query=$callConfig->selectQuery(TBL_SURVEY_SETUP,'*',$whr,'','','');
    			$response = $callConfig->getAllRows($query);
    	//print_r($response);

    			foreach($response as $svalues){
    				$survyidlist[] = $svalues->id;
    				$surveyvalues = array('survey_id'=>$svalues->id,'survey_name'=>$svalues->survey_name,'survey_start_date'=>date('d M, Y',strtotime($svalues->survey_start_date)),'survey_end_date'=>date('d M, Y',strtotime($svalues->survey_end_date)),'current_status'=>$list->current_status);
    				$whr="company_id=".$get['company_id']." and is_active='0' and survey_id=".$svalues->id;
    				$questions=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'count(id) as totalquestion',$whr,'','','');
    				$qresponse = $callConfig->getRow($questions);
    				$surveyvalues['totalquestion'] = $qresponse->totalquestion;

    				if($list->current_status == ST_COMPLETE){ 
    					$surveyvalues['completionrate'] = ST_COMPLETE;
    					$surveyvalues['completionratecolor'] = "success";
    				}else if (date('Y-m-d') > $svalues->survey_end_date && strtotime($enddatatime = $svalues->survey_end_date." ".$svalues->survey_end_time) < strtotime(date('Y-m-d H:i:s'))) {
                			$surveyvalues['completionratecolor'] = "warning"; // Expired
                			$surveyvalues['completionrate'] = ST_INACTIVE;
                		} else {
                			$surveyvalues['completionrate'] = ST_ACTIVE;
                			$surveyvalues['completionratecolor'] = "primary";
                		}
                		$details['survey_details'][] = $surveyvalues;
                	}
                }

                $survyidlist = implode(',',(array)$survyidlist);

                $details['summary'][] = array('data'=>count((array)$survey_list),'name'=>$tag1,'bgcolor'=>'primary');

                $queryresponse = "SELECT count(id) as total_responses, round(sum(duration_taken)/count(id),2) as avg_duration_taken FROM ".TBL_SURVEY_RESPONSES." WHERE employee_id=".$get['emp_id']." and is_active=0 and company_id=".$get['company_id']." and category=".$get['category']." and survey_id in (".$survyidlist.") and current_status=".ST_COMPLETE; 
                $response = $callConfig->getRow($queryresponse);

                $details['summary'][] = array('data'=>($response->total_responses)==''?'0':($response->total_responses),'name'=>'Complete','bgcolor'=>'success');

                $queryresponse = "SELECT count(id) as partial  FROM ".TBL_SURVEY_RESPONSES." WHERE employee_id=".$get['emp_id']." and is_active=0 and company_id=".$get['company_id']." and category=".$get['category']." and survey_id in (".$survyidlist.") and current_status=".ST_INPROGRESS; 
                $presponse = $callConfig->getRow($queryresponse);

                $details['summary'][] = array('data'=>$presponse->partial==''?'0':$presponse->partial,'name'=>'Partial','bgcolor'=>'warning');

                $details['summary'][] = array('data'=>(count((array)$survey_list)-$response->total_responses)==''?'0':(count((array)$survey_list)-$response->total_responses),'name'=>'Incomplete','bgcolor'=>'info');

                $details['summary'][] = array('data'=>$response->avg_duration_taken==''?'0':$response->avg_duration_taken,'name'=>'Avg Time','bgcolor'=>'dark');
                if(count((array)$survey_list)>0){
                	$details['rate']= round($response->total_responses/count((array)$survey_list) *100 ,2)." %";

                }else{
                	$details['rate']= "0 %";

                }

                $whr2="company_id=".$get['company_id']." and is_active='0' and genc_code='SURVEY_QTYPE'";
                $query=$callConfig->selectQuery('general_code_details','genc_detail_code,genc_detail_name',$whr2,'','','');
                $details['qustion_types'] = $callConfig->getAllRows($query);
                foreach($details['qustion_types'] as $qustion_types){
                	$qtype[$qustion_types->genc_detail_code] = $qustion_types->genc_detail_name;

                	$whr="survey_id in (".$survyidlist.") and company_id=".$get['company_id']." and is_active='0' and options_display_type=".$qustion_types->genc_detail_code;
                	$questions=$callConfig->selectQuery(TBL_SURVEY_SETUP_QUESTIONS,'count(id) as totaltypequestion',$whr,'','','');
                	$qresponse = $callConfig->getRow($questions);
                	$survey_qtypes[$qustion_types->genc_detail_name] = $qresponse->totaltypequestion;
                }
                $details['survey_qtypes'] = $survey_qtypes;
    //	echo "<pre>"; print_r($details);exit;

                return $details;
            }
	function resolveSurveyAdminDashboardStatus($survey, $questionCount){
		$endTime = $survey->survey_end_time != "" ? $survey->survey_end_time : "12:00:00";
		$startTime = $survey->survey_start_time != "" ? $survey->survey_start_time : "12:00:00";
		$endTs = strtotime($survey->survey_end_date." ".$endTime);
		$startTs = strtotime($survey->survey_start_date." ".$startTime);
		$nowTs = time();
		if($survey->current_status == ST_ACTIVE){
			return array('code'=>'draft','label'=>'Draft','color'=>'warning');
		}
		if($endTs != false && $endTs < $nowTs){
			return array('code'=>'completed','label'=>'Completed','color'=>'secondary');
		}

		if($startTs != false && $endTs != false && $nowTs >= $startTs && $nowTs <= $endTs){
			return array('code'=>'running','label'=>'Running','color'=>'success');
		}

		return array('code'=>'other','label'=>'','color'=>'secondary');
	}

	function mapSurveyAdminDashboardRow($survey, $questionCount, $typeMap, &$filterLabelCache, $totalEmployeeCount){
		global $callConfig;

		$statusInfo = $this->resolveSurveyAdminDashboardStatus($survey, $questionCount);
		$categoryLabel = 'General';
		if($survey->survey_type != "" && isset($typeMap[$survey->survey_type])){
			$categoryLabel = $typeMap[$survey->survey_type];
		}

		$assignedLabel = '';
		$filterId = $survey->survey_assigned_to;
		if($filterId != "" && !isset($filterLabelCache[$filterId])){
			$filterQuery = "SELECT filter_condition FROM ".TBL_FILTER_TABLES." WHERE company_id='".$survey->company_id."' AND is_active='0' AND id='".$filterId."'";
			$filterRow = $callConfig->getRow($filterQuery);
			$filterLabelCache[$filterId] = $filterRow->filter_condition != "" ? $filterRow->filter_condition : '';
		}
		if($filterId != "" && $filterLabelCache[$filterId] != ""){
			$assignedLabel = $filterLabelCache[$filterId];
		}else if((int)$survey->assigned_to_count > 0 && $totalEmployeeCount > 0 && (int)$survey->assigned_to_count == $totalEmployeeCount){
			$assignedLabel = 'All Employees';
		}else if((int)$survey->assigned_to_count > 0){
			$assignedLabel = (int)$survey->assigned_to_count.' Employees';
		}else{
			$assignedLabel = 'Not Assigned';
		}

		$startDate = $survey->survey_start_date != "" ? date('M d, Y', strtotime($survey->survey_start_date)) : '';
		$endDate = $survey->survey_end_date != "" ? date('M d, Y', strtotime($survey->survey_end_date)) : '';

		return array(
			'survey_id'=>$survey->id,
			'survey_name'=>$survey->survey_name,
			'category_label'=>$categoryLabel,
			'assigned_to_label'=>$assignedLabel,
			'survey_start_date'=>$startDate,
			'survey_end_date'=>$endDate,
			'status'=>$statusInfo['code'],
			'status_label'=>$statusInfo['label'],
			'status_color'=>$statusInfo['color']
		);
	}

	function getsurveyadmindashboard($get){
		global $callConfig;

		$category = $get['category'] != "" ? $get['category'] : '1';
		$companyId = $get['company_id'];
		$typeGencCode = $category == '2' ? 'POLL_TYPES' : 'SURVEY_TYPES';

		$details = array(
			'summary'=>array('total'=>0,'active'=>0,'completed'=>0,'draft'=>0),
			'completion_rate'=>array(
				'percent'=>0,
				'completed_count'=>0,
				'total_count'=>0,
				'trend_percent'=>0,
				'trend_direction'=>'flat'
			),
			'responses_by_department'=>array(),
			'monthly_response_trend'=>array(),
			'recent_surveys'=>array()
		);

		$typeMap = array();
		$typeQuery = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id='".$companyId."' AND is_active='0' AND genc_code='".$typeGencCode."'";
		$typeRows = $callConfig->getAllRows($typeQuery);
		if(count((array)$typeRows) > 0){
			foreach($typeRows as $typeRow){
				$typeMap[$typeRow->genc_detail_code] = $typeRow->genc_detail_name;
			}
		}

		$empCountQuery = "SELECT count(id) as total_employees FROM employees WHERE company_id='".$companyId."' AND is_active='0'";
		$empCountRow = $callConfig->getRow($empCountQuery);
		$totalEmployeeCount = (int)$empCountRow->total_employees;

		$surveyQuery = "SELECT * FROM ".TBL_SURVEY_SETUP." WHERE is_active='0' AND company_id='".$companyId."' AND category='".$category."' AND survey_type!='".EXITE_INTERVIEW_TYPE."' ORDER BY created_date DESC";
		// echo $surveyQuery; exit;
		$surveyRows = $callConfig->getAllRows($surveyQuery);

		$surveyMeta = array();
		$completedEndLast30 = 0;
		$completedEndPrev30 = 0;
		$nowTs = time();
		$last30Start = strtotime('-30 days', $nowTs);
		$prev30Start = strtotime('-60 days', $nowTs);
		// echo count((array)$surveyRows); exit;

		if(count((array)$surveyRows) > 0){
			foreach($surveyRows as $surveyRow){
				$questionQuery = "SELECT count(id) as totalquestion FROM ".TBL_SURVEY_SETUP_QUESTIONS." WHERE company_id='".$companyId."' AND is_active='0' AND survey_id='".$surveyRow->id."'";
				$questionRow = $callConfig->getRow($questionQuery);
				$questionCount = (int)$questionRow->totalquestion;

				$statusInfo = $this->resolveSurveyAdminDashboardStatus($surveyRow, $questionCount);
				$details['summary']['total']++;
				if($statusInfo['code'] == 'running'){
					$details['summary']['active']++;
				}else if($statusInfo['code'] == 'completed'){
					$details['summary']['completed']++;
				}else if($statusInfo['code'] == 'draft'){
					$details['summary']['draft']++;
				}

				$endTime = $surveyRow->survey_end_time != "" ? $surveyRow->survey_end_time : "12:00:00";
				$endTs = strtotime($surveyRow->survey_end_date." ".$endTime);
				if($endTs != false && $endTs < $nowTs){
					if($endTs >= $last30Start){
						$completedEndLast30++;
					}else if($endTs >= $prev30Start && $endTs < $last30Start){
						$completedEndPrev30++;
					}
				}

				$surveyMeta[] = array(
					'survey'=>$surveyRow,
					'question_count'=>$questionCount,
					'created_ts'=>strtotime($surveyRow->created_date)
				);
			}
		}

		$details['completion_rate']['completed_count'] = $details['summary']['completed'];
		$details['completion_rate']['total_count'] = $details['summary']['total'];
		if($details['summary']['total'] > 0){
			$details['completion_rate']['percent'] = round(($details['summary']['completed'] / $details['summary']['total']) * 100);
		}

		if($completedEndPrev30 > 0){
			$trendDelta = round((($completedEndLast30 - $completedEndPrev30) / $completedEndPrev30) * 100);
		}else if($completedEndLast30 > 0){
			$trendDelta = 100;
		}else{
			$trendDelta = 0;
		}
		$details['completion_rate']['trend_percent'] = abs($trendDelta);
		if($trendDelta > 0){
			$details['completion_rate']['trend_direction'] = 'up';
		}else if($trendDelta < 0){
			$details['completion_rate']['trend_direction'] = 'down';
		}else{
			$details['completion_rate']['trend_direction'] = 'flat';
		}

		$deptQuery = "SELECT IFNULL(d.department_name, 'Unassigned') as department, count(sr.id) as response_count
			FROM ".TBL_SURVEY_RESPONSES." sr
			LEFT JOIN employees e ON e.id = sr.employee_id AND e.company_id = sr.company_id AND e.is_active = '0'
			LEFT JOIN departments d ON d.id = e.department_id AND d.company_id = sr.company_id AND d.is_active = '0'
			WHERE sr.company_id='".$companyId."' AND sr.is_active='0' AND sr.category='".$category."' AND sr.current_status='".ST_COMPLETE."'
			GROUP BY e.department_id, d.department_name
			ORDER BY response_count DESC";
		$deptRows = $callConfig->getAllRows($deptQuery);
		if(count((array)$deptRows) > 0){
			foreach($deptRows as $deptRow){
				$details['responses_by_department'][] = array(
					'department'=>$deptRow->department,
					'count'=>(int)$deptRow->response_count
				);
			}
		}

		$monthKeys = array();
		for($m = 11; $m >= 0; $m--){
			$monthTs = strtotime('-'.$m.' months', strtotime(date('Y-m-01')));
			$monthKeys[date('Y-m', $monthTs)] = date('M', $monthTs);
		}

		$trendStartDate = date('Y-m-01', strtotime('-11 months'));
		$monthlyQuery = "SELECT DATE_FORMAT(response_end_date, '%Y-%m') as month_key, count(id) as response_count
			FROM ".TBL_SURVEY_RESPONSES."
			WHERE company_id='".$companyId."' AND is_active='0' AND category='".$category."' AND current_status='".ST_COMPLETE."'
			AND response_end_date >= '".$trendStartDate."'
			GROUP BY month_key
			ORDER BY month_key ASC";
		$monthlyRows = $callConfig->getAllRows($monthlyQuery);
		$monthlyCounts = array();
		if(count((array)$monthlyRows) > 0){
			foreach($monthlyRows as $monthlyRow){
				$monthlyCounts[$monthlyRow->month_key] = (int)$monthlyRow->response_count;
			}
		}
		foreach($monthKeys as $monthKey => $monthLabel){
			$details['monthly_response_trend'][] = array(
				'month'=>$monthLabel,
				'count'=>isset($monthlyCounts[$monthKey]) ? $monthlyCounts[$monthKey] : 0
			);
		}

		$filterLabelCache = array();
		if(count((array)$surveyMeta) > 0){
			usort($surveyMeta, function($a, $b){
				if($a['created_ts'] == $b['created_ts']){
					return 0;
				}
				return ($a['created_ts'] > $b['created_ts']) ? -1 : 1;
			});

			$recentLimit = 0;
			foreach($surveyMeta as $metaRow){
				if($recentLimit >= 5){
					break;
				}
				$details['recent_surveys'][] = $this->mapSurveyAdminDashboardRow(
					$metaRow['survey'],
					$metaRow['question_count'],
					$typeMap,
					$filterLabelCache,
					$totalEmployeeCount
				);
				$recentLimit++;
			}
		}

		return $details;
	}

	function loadSurveyEmployeeDashboardProgressMaps($companyId, $empId, $surveyIds){
		global $callConfig;

		$questionCounts = array();
		$answeredCounts = array();

		if(count((array)$surveyIds) == 0){
			return array('question_counts'=>$questionCounts, 'answered_counts'=>$answeredCounts);
		}

		$surveyIdIn = implode(',', array_map('intval', $surveyIds));

		$questionQuery = "SELECT survey_id, count(id) as total FROM ".TBL_SURVEY_SETUP_QUESTIONS." WHERE company_id='".$companyId."' AND is_active='0' AND survey_id IN (".$surveyIdIn.") GROUP BY survey_id";
		$questionRows = $callConfig->getAllRows($questionQuery);
		if(count((array)$questionRows) > 0){
			foreach($questionRows as $questionRow){
				$questionCounts[$questionRow->survey_id] = (int)$questionRow->total;
			}
		}

		$answeredQuery = "SELECT survey_id, count(DISTINCT question_id) as answered FROM ".TBL_SURVEY_RESPONSE_DETAILS." WHERE company_id='".$companyId."' AND is_active='0' AND employee_id='".$empId."' AND survey_id IN (".$surveyIdIn.") GROUP BY survey_id";
		$answeredRows = $callConfig->getAllRows($answeredQuery);
		if(count((array)$answeredRows) > 0){
			foreach($answeredRows as $answeredRow){
				$answeredCounts[$answeredRow->survey_id] = (int)$answeredRow->answered;
			}
		}

		return array('question_counts'=>$questionCounts, 'answered_counts'=>$answeredCounts);
	}

	function resolveSurveyEmployeeDashboardStatus($responseRow, $surveyRow, $progressPercent){
		$endTime = $surveyRow->survey_end_time != "" ? $surveyRow->survey_end_time : "12:00:00";
		$endTs = strtotime($surveyRow->survey_end_date." ".$endTime);
		$nowTs = time();
		$isExpired = ($endTs != false && $endTs < $nowTs);

		if($responseRow->current_status == ST_COMPLETE){
			return array(
				'code'=>'completed',
				'label'=>'Completed',
				'color'=>'success',
				'is_expired'=>0,
				'action'=>'view_result',
				'action_label'=>'View Result'
			);
		}

		if($isExpired){
			return array(
				'code'=>'expired',
				'label'=>'Expired',
				'color'=>'warning',
				'is_expired'=>1,
				'action'=>'expired',
				'action_label'=>'Expired'
			);
		}

		if($responseRow->current_status == ST_INPROGRESS){
			return array(
				'code'=>'in_progress',
				'label'=>'In Progress',
				'color'=>'primary',
				'is_expired'=>0,
				'action'=>'resume',
				'action_label'=>'Resume'
			);
		}

		if($progressPercent == 0){
			return array(
				'code'=>'not_started',
				'label'=>'Not Started',
				'color'=>'muted',
				'is_expired'=>0,
				'action'=>'start',
				'action_label'=>'Start Survey'
			);
		}

		return array(
			'code'=>'in_progress',
			'label'=>'In Progress',
			'color'=>'primary',
			'is_expired'=>0,
			'action'=>'resume',
			'action_label'=>'Resume'
		);
	}

	function matchesEmployeeSurveyDashboardFilter($responseRow, $isExpired, $statusFilter){
		if($statusFilter == '' || $statusFilter == 'assigned'){
			return true;
		}
		if($statusFilter == 'completed'){
			return $responseRow->current_status == ST_COMPLETE;
		}
		if($statusFilter == 'in_progress'){
			return $responseRow->current_status == ST_INPROGRESS;
		}
		if($statusFilter == 'pending'){
			return ($responseRow->current_status != ST_COMPLETE && $responseRow->current_status != ST_INPROGRESS && $isExpired);
		}
		return true;
	}

	function mapSurveyEmployeeDashboardTableRow($responseRow, $surveyRow, $progressPercent, $typeMap, $category){
		$statusInfo = $this->resolveSurveyEmployeeDashboardStatus($responseRow, $surveyRow, $progressPercent);
		$categoryLabel = 'General';
		if($surveyRow->survey_type != "" && isset($typeMap[$surveyRow->survey_type])){
			$categoryLabel = $typeMap[$surveyRow->survey_type];
		}

		$startTime = $surveyRow->survey_start_time != "" ? $surveyRow->survey_start_time : "12:00:00";
		$endTime = $surveyRow->survey_end_time != "" ? $surveyRow->survey_end_time : "12:00:00";
		$startDate = $surveyRow->survey_start_date != "" ? date('M d, Y', strtotime($surveyRow->survey_start_date." ".$startTime)) : '';
		$endDate = $surveyRow->survey_end_date != "" ? date('M d, Y', strtotime($surveyRow->survey_end_date." ".$endTime)) : '';

		$actionLabel = $statusInfo['action_label'];
		if($statusInfo['action'] == 'start'){
			$actionLabel = $category == '2' ? 'Start Poll' : 'Start Survey';
		}

		return array(
			'survey_id'=>$surveyRow->id,
			'survey_name'=>$surveyRow->survey_name,
			'category_label'=>$categoryLabel,
			'survey_start_date'=>$startDate,
			'survey_end_date'=>$endDate,
			'status'=>$statusInfo['code'],
			'status_label'=>$statusInfo['label'],
			'status_color'=>$statusInfo['color'],
			'action'=>$statusInfo['action'],
			'action_label'=>$actionLabel,
			'current_status'=>$responseRow->current_status != "" ? $responseRow->current_status : ST_ACTIVE,
			'end_ts'=>strtotime($surveyRow->survey_end_date." ".$endTime)
		);
	}

	function resolveSurveyEmployeeId($get){
		global $callConfig;

		$empId = isset($get['emp_id']) ? trim($get['emp_id']) : '';
		if($empId != ''){
			return $empId;
		}

		$userId = isset($get['user_id']) ? trim($get['user_id']) : '';
		$companyId = isset($get['company_id']) ? trim($get['company_id']) : '';
		if($userId == '' || $companyId == ''){
			return '';
		}

		$empQuery = "SELECT id FROM employees WHERE company_id='".$companyId."' AND user_id='".$userId."' AND is_active='0' LIMIT 1";
		$empRow = $callConfig->getRow($empQuery);
		if($empRow && $empRow->id != ""){
			return $empRow->id;
		}

		return '';
	}

	function getemployeesurveydashboard($get){
		global $callConfig;

		$category = $get['category'] != "" ? $get['category'] : '1';
		$companyId = $get['company_id'];
		$empId = $this->resolveSurveyEmployeeId($get);
		$displayTag = $category == '2' ? 'polls' : 'surveys';

		$statusFilter = isset($get['status_filter']) ? trim($get['status_filter']) : '';
		$responseMode = isset($get['response_mode']) ? trim($get['response_mode']) : '';
		$tableOnlyMode = ($responseMode == 'table');
		$collectRows = ($statusFilter != '' || $tableOnlyMode);
		if($collectRows && $statusFilter != 'assigned' && $statusFilter != 'in_progress' && $statusFilter != 'completed' && $statusFilter != 'pending'){
			$statusFilter = 'assigned';
		}
		if($tableOnlyMode && $statusFilter == ''){
			$statusFilter = 'assigned';
		}

		$typeGencCode = $category == '2' ? 'POLL_TYPES' : 'SURVEY_TYPES';
		$typeMap = array();
		$typeQuery = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id='".$companyId."' AND is_active='0' AND genc_code='".$typeGencCode."'";
		$typeRows = $callConfig->getAllRows($typeQuery);
		if(count((array)$typeRows) > 0){
			foreach($typeRows as $typeRow){
				$typeMap[$typeRow->genc_detail_code] = $typeRow->genc_detail_name;
			}
		}

		$details = array(
			'summary'=>array('assigned'=>0,'in_progress'=>0,'completed'=>0,'pending'=>0),
			'completion_rate'=>array(
				'percent'=>0,
				'completed_count'=>0,
				'required_count'=>0,
				'message_title'=>'Great job!',
				'message_text'=>"You've completed 0 of 0 required ".$displayTag."."
			),
			'upcoming_deadlines'=>array(),
			'recent_activity'=>array()
		);
		if($collectRows){
			$details['rows'] = array();
		}

		$assignmentQuery = "SELECT sr.*, ss.survey_name, ss.survey_start_date, ss.survey_end_date, ss.survey_start_time, ss.survey_end_time, ss.survey_type
			FROM ".TBL_SURVEY_RESPONSES." sr
			INNER JOIN ".TBL_SURVEY_SETUP." ss ON ss.id = sr.survey_id AND ss.company_id = sr.company_id AND ss.is_active='0'
			WHERE sr.company_id='".$companyId."' AND sr.employee_id='".$empId."' AND sr.category='".$category."' AND sr.is_active='0'
			AND ss.survey_type!='".EXITE_INTERVIEW_TYPE."'";
		$assignmentRows = $callConfig->getAllRows($assignmentQuery);

		$surveyIds = array();
		if(count((array)$assignmentRows) > 0){
			foreach($assignmentRows as $assignmentRow){
				$surveyIds[] = (int)$assignmentRow->survey_id;
			}
		}
		$progressMaps = $this->loadSurveyEmployeeDashboardProgressMaps($companyId, $empId, $surveyIds);
		$questionMap = $progressMaps['question_counts'];
		$answeredMap = $progressMaps['answered_counts'];

		$deadlineRows = array();
		$activityRows = array();
		$tableRows = array();
		$nowTs = time();

		if(count((array)$assignmentRows) > 0){
			foreach($assignmentRows as $assignmentRow){
				$surveyRow = (object)array(
					'id'=>$assignmentRow->survey_id,
					'survey_name'=>$assignmentRow->survey_name,
					'survey_start_date'=>$assignmentRow->survey_start_date,
					'survey_end_date'=>$assignmentRow->survey_end_date,
					'survey_start_time'=>$assignmentRow->survey_start_time,
					'survey_end_time'=>$assignmentRow->survey_end_time,
					'survey_type'=>$assignmentRow->survey_type
				);

				$surveyId = (int)$assignmentRow->survey_id;
				$questionCount = isset($questionMap[$surveyId]) ? (int)$questionMap[$surveyId] : 0;
				$answered = isset($answeredMap[$surveyId]) ? (int)$answeredMap[$surveyId] : 0;
				$progressPercent = 0;
				if($questionCount > 0 && $answered > 0){
					$progressPercent = (int)round(($answered / $questionCount) * 100);
				}

				$endTime = $surveyRow->survey_end_time != "" ? $surveyRow->survey_end_time : "12:00:00";
				$endTs = strtotime($surveyRow->survey_end_date." ".$endTime);
				$isExpired = ($endTs != false && $endTs < $nowTs);

				if(!$tableOnlyMode){
					$details['summary']['assigned']++;
					if($assignmentRow->current_status == ST_COMPLETE){
						$details['summary']['completed']++;
					}else if($assignmentRow->current_status == ST_INPROGRESS){
						$details['summary']['in_progress']++;
					}else if($isExpired){
						$details['summary']['pending']++;
					}
				}

				if($collectRows && $this->matchesEmployeeSurveyDashboardFilter($assignmentRow, $isExpired, $statusFilter)){
					$tableRows[] = $this->mapSurveyEmployeeDashboardTableRow($assignmentRow, $surveyRow, $progressPercent, $typeMap, $category);
				}

				if(!$tableOnlyMode){
					if($assignmentRow->current_status == ST_COMPLETE){
						$activityRows[] = array(
							'type'=>'completed',
							'title'=>'Completed: '.$assignmentRow->survey_name,
							'subtitle'=>date('M d, Y', strtotime($assignmentRow->response_end_date)),
							'icon_class'=>'fa fa-check f-w-600 bg-c-green',
							'sort_ts'=>strtotime($assignmentRow->response_end_date." ".($assignmentRow->response_end_time != "" ? $assignmentRow->response_end_time : "00:00:00"))
						);
					}else{
						if($assignmentRow->current_status == ST_INPROGRESS){
							$activityRows[] = array(
								'type'=>'resumed',
								'title'=>'Resumed: '.$assignmentRow->survey_name,
								'subtitle'=>date('M d, Y', strtotime($assignmentRow->response_start_date)),
								'icon_class'=>'ti-pencil-alt bg-c-blue',
								'sort_ts'=>strtotime($assignmentRow->response_start_date." ".($assignmentRow->response_start_time != "" ? $assignmentRow->response_start_time : "00:00:00"))
							);
						}else if($progressPercent == 0){
							$activityRows[] = array(
								'type'=>'assigned',
								'title'=>'Assigned: '.$assignmentRow->survey_name,
								'subtitle'=>date('M d, Y', strtotime($assignmentRow->created_date)),
								'icon_class'=>'ti-timer bg-c-yellow',
								'sort_ts'=>strtotime($assignmentRow->created_date)
							);
						}

						if(!$isExpired){
							$daysLeft = $endTs != false ? (int)ceil(($endTs - $nowTs) / 86400) : 0;
							if($daysLeft < 0){
								$daysLeft = 0;
							}
							$urgency = 'info';
							if($daysLeft <= 3){
								$urgency = 'danger';
							}else if($daysLeft <= 7){
								$urgency = 'warning';
							}
							$deadlineRows[] = array(
								'survey_id'=>$assignmentRow->survey_id,
								'survey_name'=>$assignmentRow->survey_name,
								'due_date'=>date('M d, Y', $endTs),
								'days_left'=>$daysLeft,
								'days_left_label'=>$daysLeft.' Day'.($daysLeft == 1 ? '' : 's').' Left',
								'urgency'=>$urgency,
								'sort_ts'=>$endTs
							);
						}
					}
				}
			}
		}

		if($collectRows && count((array)$tableRows) > 0){
			usort($tableRows, function($a, $b){
				if($a['end_ts'] == $b['end_ts']){
					return 0;
				}
				return ($a['end_ts'] < $b['end_ts']) ? -1 : 1;
			});
			foreach($tableRows as $tableRow){
				$row = $tableRow;
				unset($row['end_ts']);
				$details['rows'][] = $row;
			}
		}

		if($tableOnlyMode){
			return array('rows'=>isset($details['rows']) ? $details['rows'] : array());
		}

		$details['completion_rate']['completed_count'] = $details['summary']['completed'];
		$details['completion_rate']['required_count'] = $details['summary']['assigned'];
		if($details['summary']['assigned'] > 0){
			$details['completion_rate']['percent'] = (int)round(($details['summary']['completed'] / $details['summary']['assigned']) * 100);
		}
		$details['completion_rate']['message_text'] = "You've completed ".$details['completion_rate']['completed_count']." of ".$details['completion_rate']['required_count']." required ".$displayTag.".";

		if(count((array)$deadlineRows) > 0){
			usort($deadlineRows, function($a, $b){
				if($a['sort_ts'] == $b['sort_ts']){
					return 0;
				}
				return ($a['sort_ts'] < $b['sort_ts']) ? -1 : 1;
			});
			$deadlineLimit = 0;
			foreach($deadlineRows as $deadlineRow){
				if($deadlineLimit >= 3){
					break;
				}
				unset($deadlineRow['sort_ts']);
				$details['upcoming_deadlines'][] = $deadlineRow;
				$deadlineLimit++;
			}
		}

		if(count((array)$activityRows) > 0){
			usort($activityRows, function($a, $b){
				if($a['sort_ts'] == $b['sort_ts']){
					return 0;
				}
				return ($a['sort_ts'] > $b['sort_ts']) ? -1 : 1;
			});
			$activityLimit = 0;
			foreach($activityRows as $activityRow){
				if($activityLimit >= 5){
					break;
				}
				unset($activityRow['sort_ts']);
				$details['recent_activity'][] = $activityRow;
				$activityLimit++;
			}
		}

		return $details;
	} 
	function totestcodems($get){
		global $callConfig;
		$start = date("Y-m-d", strtotime($get['fromdate']."-01"));
		$end = date("Y-m-t", strtotime($start));
		//echo $start."-".$end;
		$query = "SELECT  emp_id , work_order_id, work_order_detail_id, effective_from_date, effective_to_date,vrc_id, vrc_item_id FROM emp_work_order WHERE company_id =".$get['company_id']." AND effective_from_date <= '".$end."' AND ( effective_to_date IS NULL   OR effective_to_date >= '".$start."') AND is_active = 0 ";
    	$work_orders = $callConfig->getAllRows($query);
    	foreach($work_orders as $work_order){
    		$emps[]= $work_order->emp_id;
    		$work_order_ids[]= $work_order->work_order_id;
    		$work_order_detail_ids[]= $work_order->work_order_detail_id;
    		$vrc_ids[]= $work_order->vrc_id;
    		$vrc_item_ids[]= $work_order->vrc_item_id;
    		$empwork[$work_order->emp_id]= (array)$work_order;
    	}
    	if(count(array($emps))>0){
	    	$query = "SELECT id as employee_id, emp_code, display_name as employee_name , department_id, (SELECT dp.department_name FROM departments dp WHERE dp.id = department_id) as department_name , vertical_id, (SELECT tc.vertical_name FROM verticals tc WHERE tc.id = vertical_id) as verticle_name , location_id, (SELECT lc.location_name FROM locations lc WHERE lc.id = location_id) as location_name , billable as emp_billable, cost_center_id, cost_center_id as cost_center,(SELECT cc.cost_center_name FROM cost_centers cc WHERE cc.id = cost_center_id) as cost_center_name, (total_experiance + (
              DATEDIFF(NOW(), joining_date) / 365 ) ) exp_joining_date, joining_date, COALESCE(total_experiance, '0') AS total_experiance, job_title_id AS job_title_id, ( SELECT jt.job_title FROM  job_titles jt  WHERE  jt.company_id = company_id  AND jt.id =job_title_id) job_title, approval_manager_id as reports_to , '' as remark FROM `employees` WHERE id IN (".implode(",",$emps).")";
	    	$emp_data = $callConfig->getAllRows($query);
    	}
    	$query = "SELECT emp_id, sum(billable_hrs) as billable_hrs , sum(nonbillable_hrs) as nonbillable_hrs, max(current_status) as current_status FROM `project_timesheets` WHERE company_id =".$get['company_id']." AND timesheet_date >= '".$start."' AND timesheet_date <= '".$end."' AND is_active = 0 GROUP BY emp_id";
    	$time_data = $callConfig->getAllRows($query);
    	foreach($time_data as $time){
    		$timesheet[$time->emp_id] =(array)$time;
    	}
    	if(count(array($vrc_ids))>0){
    		$query = "SELECT id, customer_id FROM vendor_rate_cards WHERE company_id =".$get['company_id']." AND id IN (".implode(",",$vrc_ids).")";
    		$ratecards = $callConfig->getAllRows($query);
    		foreach($ratecards as $rate){
    			$card[$rate->id]=(array)$rate;
    			$vendors[] = $rate->customer_id;
    		}
    		$query = "SELECT id, transaction_id, item_id, purchase_uom, min_exp, max_exp, item_price, experience_base FROM vrc_items WHERE company_id =".$get['company_id']." AND id IN (".implode(",",$vrc_item_ids).")";
    		$ivrcs = $callConfig->getAllRows($query);
    		foreach($ivrcs as $ivrc){
    			$vrc[$ivrc->id]=(array)$ivrc;
    		}
    	}
    	if(count(array($work_order_ids))>0){
    		/*$query = "SELECT * FROM manpower_contracts WHERE company_id =".$get['company_id']." AND id IN (".implode(",",$work_order_ids).")";
    		$contarcts = $callConfig->getAllRows($query);
    		foreach($contarcts as $contarct){
    			$po[$contarct->id]=$contarct;
    		}*/
    		$query = "SELECT  id, transaction_id, item_id, purchase_uom, min_exp, max_exp, item_price FROM mpc_items WHERE company_id =".$get['company_id']." AND id IN (".implode(",",$work_order_detail_ids).")";
    		$impcs = $callConfig->getAllRows($query);
    		foreach($impcs as $impc){
    			$mpc[$impc->id]=(array)$impc;
    		}
    	}

    	$query = "SELECT id, IFNULL(company, display_name) as display_name FROM vendors WHERE company_id =".$get['company_id']." AND id IN (".implode(",",$vendors).")";
    	$vendordata = $callConfig->getAllRows($query);
		foreach($vendordata as $vendor){
			$vend[$vendor->id] = (array)$vendor;
		}
		$query = "SELECT `employee_id`, SUM( DATEDIFF( LEAST( el.leave_to, last_day(el.leave_from) ), el.leave_from ) + DATEDIFF( el.leave_to, GREATEST( el.leave_from, DATE_SUB( LAST_DAY(el.leave_to), INTERVAL DAY( LAST_DAY(el.leave_to) ) -1 DAY ) ) ) )- SUM( CASE WHEN el.start_half_day = 1 THEN 0.5 ELSE 0 END + CASE WHEN el.end_half_day = 1 THEN 0.5 ELSE 0 END ) leave_days FROM employee_leaves el WHERE company_id =1 AND ( ( el.leave_FROM >= '".$start."'  AND el.leave_FROM <= '".$end."' ) OR ( el.leave_to >= '".$start."'  AND el.leave_to <= '".$end."' )) GROUP BY el.employee_id";
		$leae_data = $callConfig->getAllRows($query);
		foreach((array)$leae_data as $leae_da){
			$lea[$leae_da->employee_id] = $leae_da->leave_days;
		}
    	$get['from_date'] = $start;
    	$get['to_date'] = $end;
    	$loc_holiday = $this->toretriveminhorsandweksanddayebasedondateswork($get);
    	foreach($emp_data as $emp){
    		$emp_all = (array)$emp;
    		$emp_id = $emp->employee_id;
    		//print_r($empwork[$emp->employee_id]); 
    		$emp_rate_card_id = $empwork[$emp_id]['vrc_id'];
    		///print_r($card[$emp_rate_card_id]); 
			$emp_vendor_id = $card[$emp_rate_card_id]['customer_id'];
			$rate_uom =  $vrc[$empwork[$emp_id]['vrc_item_id']]['purchase_uom']; 
			$rate_amount = $vrc[$empwork[$emp_id]['vrc_item_id']]['item_price'];
			$mpc_uom =  $mpc[$empwork[$emp_id]['work_order_detail_id']]['purchase_uom']; 
			$mpc_amount = $mpc[$empwork[$emp_id]['work_order_detail_id']]['item_price'];
			$location_id = $emp->location_id;
			
			//echo "<pre>";print_r($loc_holiday[$location_id]); exit;
    		$emp_all['vendor_id'] = $emp_vendor_id;
    		$emp_all['vendor_name'] = $vend[$emp_vendor_id]['display_name'];
    		$emp_all['billable_hrs'] = isset($timesheet[$emp_id]['billable_hrs']) ? $timesheet[$emp_id]['billable_hrs'] :0 ;
    		$emp_all['nonbillable_hrs'] = isset($timesheet[$emp_id]['nonbillable_hrs']) ? $timesheet[$emp_id]['nonbillable_hrs'] :0;
    		$emp_all['current_status'] = isset($timesheet[$emp_id]['current_status']) ? $timesheet[$emp_id]['current_status'] :0;
    		if($rate_uom == 'PD'){
				$rate_amount = $rate_amount* $loc_holiday[$location_id]['count_working_days'];				
			}
			if($mpc_uom == 'PD'){
				$mpc_amount = $mpc_amount* $loc_holiday[$location_id]['count_working_days'];				
			}
			$amount = round (($emp_all['billable_hrs']/8) * ($rate_amount/$loc_holiday[$location_id]['count_working_days']));
			$main_amount = round(($emp_all['billable_hrs']/8) * ($mpc_amount/$loc_holiday[$location_id]['count_working_days']));
			$non_amount = round(($emp_all['nonbillable_hrs']/8) * ($rate_amount/$loc_holiday[$location_id]['count_working_days']));

    		$emp_all['vrc_uom'] = $rate_uom;
    		$emp_all['mpc_uom'] = $mpc_uom;
    		$emp_all['min_exp'] = $vrc[$empwork[$emp_id]['vrc_item_id']]['min_exp'];
    		$emp_all['max_exp'] = $vrc[$empwork[$emp_id]['vrc_item_id']]['max_exp'];
    		$emp_all['vrc_budgeted_amount'] = $rate_amount;
    		$emp_all['vrc_billable_amount'] = $amount;
    		$emp_all['billable_amount'] = $main_amount;
    		$emp_all['budgeted_amount'] = $mpc_amount;
    		$emp_all['nonbillable_amount'] = $non_amount;
    		$emp_all['work_order_id'] = $empwork[$emp_id]['work_order_id'];
    		$emp_all['work_order_detail_id'] = $empwork[$emp_id]['work_order_detail_id'];
    		$emp_all['vrc_id'] = $empwork[$emp_id]['vrc_id'];
    		$emp_all['vrc_item_id'] = $empwork[$emp_id]['vrc_item_id'];
    		$emp_all['mpc_item_price'] = $mpc[$empwork[$emp_id]['work_order_detail_id']]['item_price'];
    		$emp_all['vrc_item_price'] = $vrc[$empwork[$emp_id]['vrc_item_id']]['item_price'];
    		$emp_all['experience_base'] = $vrc[$empwork[$emp_id]['vrc_item_id']]['experience_base'];
    		$emp_all['leave_days'] = isset($lea[$emp_id]) ? $lea[$emp_id] :0;
    		$joining_timestamp = strtotime($emp->joining_date);
			$current_exp = (time() - $joining_timestamp) / (365 * 24 * 60 * 60);
			$experience = $emp->total_experiance + $current_exp;
			$remark = ($experience >= $min_exp && $experience <= $max_exp) ? '' : 'Experience Mismatch';
    		$emp_all['remark'] = $remark;
    		//echo $emp_vendor_id; exit;
    		$return[] = $emp_all;
    	}
    	return $return;
    	//echo "<pre>tuiop";  print_r($loc_holiday);print_r($emp_data); exit;
	}
	function toretriveminhorsandweksanddayebasedondateswork($get){
		global $callConfig;
		$from = $get['from_date'];
		$to = $get['to_date'];
		$location_sql = "SELECT id FROM locations WHERE is_active=0 AND main_location =0 AND  company_id=".$get['company_id'];
		$locations = $callConfig->getAllRows($location_sql);
		$holidays = "SELECT holiday_date, location_id FROM holidays WHERE company_id=".$get['company_id']." AND holiday_date >='".$get['from_date']."' AND holiday_date <='".$get['to_date']."' AND is_active=0 ";
		//echo $holidays;//exit;
		$holidaysdata = $callConfig->getAllRows($holidays);
		foreach ((array)$holidaysdata as $hkey=>$hvalue) {
			$holidaydates[$hvalue->location_id][] = $hvalue->holiday_date;
			//$locations_holidays[$hvalue->location_id] = $hvalue->location_id;
		}
		//print_r($holidaydates); //exit;
		$attendancewhurl1 = "SELECT id,minimum_hours_required,working_days FROM attendance_settings WHERE is_active=0 and company_id=".$get['company_id'];
		$attendance_settings = $callConfig->getRow($attendancewhurl1);
		//print_r($attendance_settings); exit;
		$testarrau['attendance_settings'] = $attendance_settings;
		$minimum_hours = explode(':',$attendance_settings->minimum_hours_required);
		$minimum_hours_required = $minimum_hours[0].'.'.$minimum_hours[1];
		$weekly_working_days = json_decode($attendance_settings->working_days,1);
		$work_days=0;
		$datediff = strtotime($to) - strtotime($from);
		$datediff = floor($datediff/(60*60*24));
		//print_r($locations); exit;
		foreach($locations as $location){
			//print_r($location);
		$work_days = 0; $offdays =0;
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
				}else{
					$offdays =  $offdays+1;	
				}
			}
			$return[$location->id] = array('holidaydates'=>$holidaydates[$location->id],'count_hoilydays'=>count($holidaydates[$location->id]),'minimum_hours'=>$minimum_hours_required,'week_days'=>$weekly_working_days,'count_week'=>count($weekly_working_days),'count_working_days'=>$work_days,'work_dates'=>$work_dates,'offdays'=>$offdays,'work_hrs'=>$work_days*$minimum_hours_required);
		}	
		//print_r($return);
		return 	$return;
	}
}

