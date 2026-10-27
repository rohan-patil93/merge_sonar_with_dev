<?php 
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;
class recruitmentUserClass extends userClass{
	function candidatesofferhistories($data){
		global $callConfig;
		$sql = "SELECT id,annual_ctc from salary_template where candidate_id='".$data['candidate_id']."' AND company_id = '".$data['company_id']."' and is_active = 0";	
		$val=$callConfig->getRow($sql);
		$fieldnames=array('candidate_id'=>$data['candidate_id'],'company_id'=>$data['company_id'],'status_date'=>date('Y-m-d'),'current_status'=>$data['current_status'],'is_active'=>'0','offer_amount'=>$val->annual_ctc,'created_by'=>$data['user_id'],'comments'=>$data['comments']);
		$res=$callConfig->insertRecord('candidate_offer_histories',$fieldnames);
		return $res;
	}
	function candidateofferAccept($get,$post){
		global $callConfig; 
		$return = array();
		$return['get'] = $get;
		$return['post'] = $post;
		$candupdateary = array('current_status'=>ST_LOIACCEPTED,'signature'=>$post['sign']);
		$updatecandidates=$callConfig->updateRecord('candidates',$candupdateary,'id',$post['candidate_id']);
		$return['updatecandidates'] = $updatecandidates;

		$candupdateary = array('current_status'=>ST_LOIACCEPTED);
		$updatecandidates=$callConfig->updateRecord('job_posting_candidates',$candupdateary,'candidate_id',$post['candidate_id']);
		$return['job_posting_candidates'] = $updatecandidates;


		$sql = "SELECT id,annual_ctc from salary_template where candidate_id='".$post['candidate_id']."' AND company_id = '".$post['company_id']."' and is_active = 0";	
		$val=$callConfig->getRow($sql);
		$fieldnames=array('candidate_id'=>$post['candidate_id'],'company_id'=>$post['company_id'],'status_date'=>date('Y-m-d'),'current_status'=>$post['current_status'],'is_active'=>'0','offer_amount'=>$val->annual_ctc,'created_by'=>$post['user_id'],'comments'=>$post['comments']);
		$res=$callConfig->insertRecord('candidate_offer_histories',$fieldnames);

		$return['candidateOfferGenerateToPDFAndSend'] = $this->candidateOfferGenerateToPDFAndSend($post);

		return $return;
	}

	function candidateOfferGenerateToPDFAndSend($get){
		global $callConfig; 
		$candidate_id = $get['candidate_id'];
		$candidatessql = "SELECT can.*, (SELECT jt.job_title FROM job_titles jt WHERE jt.company_id = can.company_id AND jt.id = can.job_tittle_id AND  jt.is_active =can.is_active) as job_title FROM candidates can WHERE can.company_id = '".$get['company_id']."' AND can.id = '".$candidate_id."'  AND can.is_active =0";
        $candidatesres=$callConfig->getRow($candidatessql);
        $return['candidatesres'] = $candidatesres;
      	if($candidatesres->offer_latter_pdf_name !=""){
            $pdfContent = file_get_contents(SITEROOT."/uploads/candidates_offer_latters/".$candidatesres->offer_latter_pdf_name);
            $attachment_file_name = $candidatesres->offer_latter_pdf_name;
        }else{
            $generatepdf = $this->generateOfferLatterPDF($get);
            $return['generatepdf'] = $generatepdf;
            $return['path'] =SITEROOT."/uploads/candidates_offer_latters/".$generatepdf;
            $attachment_file_name = $generatepdf;
           $pdfContent = file_get_contents(SITEROOT."/uploads/candidates_offer_latters/".$generatepdf);
        } 
        $emailarray = array();
        foreach($candidatesres as $ckey=>$cval){
			$emailarray["candidates.".$ckey] = $cval;
		}
		$rm_users_sql = "SELECT * FROM application_users WHERE company_id = '".$get['company_id']."'  AND is_active = 0 AND id='".$candidatesres->rm_id."'";
		$rm_users_res = $callConfig->getRow($rm_users_sql);

		$rm_users_sql = "SELECT id,display_name,user_email FROM application_users WHERE company_id = '".$get['company_id']."'  AND is_active = 0 AND id='".$candidatesres->rm_id."'";
		$rm_users_res = $callConfig->getRow($rm_users_sql);

		//$emailarray['candidates.job_title'] = $candidatesres->email_id;
		
		$emailarray['rm_email_id'] = $rm_users_res->user_email;
		$emailarray['rm_display_name'] = $rm_users_res->display_name;
        $emailarray['to_company_id'] = $get['company_id'];
        $emailarray['session_company_id'] = $get['company_id'];
        $emailarray['transaction_table'] = 'candidates';
        $emailarray['transaction_id'] = $candidate_id;
        $emailarray['created_by'] = $get['user_id'];
        $emailarray['attachment'] = "Y";
        $emailarray['attachment_file_name'] = $attachment_file_name;
        $emailarray['attachment_data'] = $pdfContent;
        $notarry = array();
        $email  =$this->sendNotificationAndEmails($notarry,$emailarray,'57');
        $retrun['email'] = $email;

        $user_ids1  = array($rm_users_sql->id, $candidatesres->user_id);
        $msg1 = $candidatesres->display_name." Offer Accepted";
		$crfeatebynotdataa = array('company_id'=>$candidatesres->company_id, 'notification_message'=>$msg1, 'id'=>$candidatesres->id, 'application_table_name'=>'candidates', 'type'=>'INFORMATION', 'notification_users'=>$user_ids1,'option'=>$option);
		$notres1 = $this->tosendnotifiations($crfeatebynotdataa);
        return $return;
	}
	function generateOfferLatterPDF($get){
        global $callConfig;
        $return = array();
        $candidate_id = $get['candidate_id'];
        $transaction_table = 'candidates';
        $get['rid'] = $candidate_id;
        $postarray = $this->getcandidateofferdetails($get);
        $postarray['callapi'] = 'Y';
        $api_url = SITEURL.'views/custom_view_candidate_offer_latter_pdf.php';
        $postdata = http_build_query($postarray);
        $opts = array('http' =>array(
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $content = file_get_contents($api_url, false, $context);
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->set_option('margin-top', '10mm');
        $dompdf->set_option('margin-right', '10mm');
        $dompdf->set_option('margin-bottom', '10mm');
        $dompdf->set_option('margin-left', '10mm');
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = $transaction_table."_".$candidate_id."_".time().'.pdf';
        $sFilePath = SITEROOT.'/uploads/candidates_offer_latters/'.$filename;
        $output = $dompdf->output();
        file_put_contents($sFilePath, $output);  
        $return['pdf_filename'] = $filename;
        $updateary = array('offer_latter_pdf_name'=>$filename,'updated_date'=>date("Y-m-d H:i:s"));
        $callConfig->updateRecord($transaction_table,$updateary,'id',$candidate_id);
        return $filename; 
       // return $content;
    }

	/**
	function candidateofferAccept($get){
		global $callConfig; 
		$returnary = array();
		$candiquery = $callConfig->selectQuery('candidates','*','id='.$get['candidate_id'],'','','');
		$cadndidata = $callConfig->getRow($candiquery);
		$returnary['candiquery'] = $candiquery;

		$whr1 = "is_active = 0 AND  process_type='O'";
		$task_query = $callConfig->selectQuery('process_task_settings','*',$whr1,'','','');
		$task_data = $callConfig->getAllRows($task_query);	 
		$returnary['task_query'] = $task_query;
		foreach($task_data as $tasks1){
			$taskid[$tasks1->id] = $tasks1->id;
		}
		/**
		$taskfilterary = array();
		if(count((array)$taskid) != 0){
			$taskfiltersql = "SELECT * FROM filter_tables WHERE company_id = '".$get['company_id']."' AND transaction_id IN(".implode(',',$taskid).") AND is_active = 0";
			$taskfilterres = $callConfig->getAllRows($taskfiltersql);
			foreach($taskfilterres as $tasks2){
				$taskfilterary[$tasks2->transaction_id] = $tasks2;
			}
		} **/ /**
		$returnary['taskfilterary'] = $taskfilterary;

		foreach ($task_data as $key => $tasks){
			/**$task_array = array();
				// Start check employee validate or Not For this Task
				$filterres = $taskfilterary[$tasks->id];
				$assignempchek = 0;
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
							$convertary = explode(",",$vale);
							if(!in_array($employees->$keys,$convertary)){
								$assignempchek = 1;
							}
						}
					}
				}	 **/ /**
			$days_ago = $cadndidata->reporting_date;
			$approval_id = $assigned_to = "";
			if($tasks->assigned_to == 'RM' AND $cadndidata->rm_id !=""){
				$assigned_to = $cadndidata->rm_id;
			}elseif($tasks->assigned_to == 'CAND' AND $cadndidata->user_id !=""){
				$assigned_to = $cadndidata->user_id;
			}elseif($tasks->assigned_to == 'HM' AND $cadndidata->hm_id !=""){
				$assigned_to = $cadndidata->hm_id;
			}else{
				$assigned_to = $cadndidata->hr_connect;
			}
			if($tasks->approval_type == 'RM' AND $cadndidata->rm_id !=""){
				$approval_id =$cadndidata->rm_id;
			}elseif($tasks->approval_type == 'CAND' AND $cadndidata->user_id !=""){
				$approval_id = $cadndidata->user_id;
			}elseif($tasks->approval_type == 'HM' AND $cadndidata->hm_id !=""){
				$approval_id = $cadndidata->hm_id;
			}else{
				$approval_id = $cadndidata->hr_connect;
			}
			if($assigned_to !="0" AND $assigned_to !="" AND $approval_id !="0" AND $approval_id !=""){
				if($tasks->on_the_day !=1 ){
					if($tasks->action_frequency == 'DY'){	$day = $tasks->on_the_day; 	}
					if($tasks->action_frequency == 'WK'){	$day = 7; 	}
					if($tasks->action_frequency == 'MON'){	$day = 30; 	}
					if($tasks->before_or_after == 'BF'){ $day = "-".$day; }else{ $day = "+".$day; }
					$days_ago = date('Y-m-d', strtotime($day.' days', strtotime($cadndidata->reporting_date)));
				}
				$task_array = array('company_id'=>$get['company_id'],'transaction_table'=>'candidates','transaction_id'=>$get['candidate_id'],'task_id'=>$tasks->id,'assigned_to'=>$assigned_to,'approval_user_id'=>$approval_id,'action_date'=>$days_ago,'revised_action_date'=>$days_ago,'actual_action_date'=>$days_ago,'action_flag'=>'0', 'check_list'=>$tasks->check_list,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id']);
				//print_r($task_array);
				$res = $callConfig->insertRecord('transaction_activity_schedule',$task_array);
				$returnary['res'][] = $res;
				$notiarray = array('notification_message'=>'Schedule Event '.$tasks->task_name,'notification_users'=>array('0'=>$assigned_to),'id'=>$res['LASTID'],'application_table_name'=>'transaction_activity_schedule','company_id'=>$get['company_id'],'option'=>'COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULE','notification_datetime'=>$days_ago);
				$this->tosendnotifiations($notiarray);
			}
		}
		return $returnary;
	} **/
	function getCandidateOfferDetails($get){
		global $callConfig;

		$application_userssql = "SELECT id,display_name, user_email, user_mobile FROM application_users WHERE company_id = '".$get['company_id']."' AND JSON_SEARCH(role_id, 'one', '".ADMIN_ROLE."') IS NOT NULL AND is_active =0";
		$application_usersres=$callConfig->getRow($application_userssql);
		$returnresult['admin_application_users'] = $application_usersres; 

		$candidatessql = "SELECT * FROM candidates WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active =0";
		$candidatesres=$callConfig->getRow($candidatessql);
		$returnresult['candidates'] = $candidatesres; 

		$rmapplication_userssql = "SELECT id,display_name, user_email, user_mobile FROM application_users WHERE company_id = '".$get['company_id']."' AND id = '".$candidatesres->rm_id."' AND is_active =0";
		$rmapplication_usersres=$callConfig->getRow($rmapplication_userssql);
		$returnresult['rm_application_users'] = $rmapplication_usersres; 


		$offer_detailssql = "SELECT  id, parent_id, category_id, header, sub_header, content, image, url, is_active, current_status FROM offer_details WHERE company_id = '".$get['company_id']."' AND is_active =0";
		$offer_detailsres=$callConfig->getAllRows($offer_detailssql);
		$returnresult['offer_details'] = $offer_detailsres; 

		$offer_detailssql = "SELECT  id, job_category_id, eeo_category_id, job_title, job_description, list_check, note, notice_period FROM job_titles WHERE company_id = '".$get['company_id']."' AND id = '".$candidatesres->job_tittle_id."' AND is_active =0";
		$offer_detailsres=$callConfig->getRow($offer_detailssql);
		$returnresult['job_titles'] = $offer_detailsres; 


		$job_grade_templatessql = "SELECT  id,  `name`, `grade_id`, `salary_template_id`, `letter_template_id` FROM job_grade_templates WHERE company_id = '".$get['company_id']."' AND id = '".$candidatesres->pay_template."' AND is_active =0";
		$job_grade_templatesres=$callConfig->getRow($job_grade_templatessql);
		$returnresult['job_grade_templates'] = $job_grade_templatesres; 

		$letterssql = "SELECT  id,  `transaction_number`, `transaction_date`, `version_number`, `originator`, `customer_id`, `ref_id`, `ref_no`, `ref_date`, `subject`, `address_to`, `content` FROM letters WHERE company_id = '".$get['company_id']."' AND id = '".$job_grade_templatesres->letter_template_id."' AND is_active =0";
		$lettersres=$callConfig->getRow($letterssql);
		$returnresult['letters'] = $lettersres; 

		$employeessql = "SELECT image FROM employees WHERE company_id = '".$get['company_id']."' AND id = '".$job_grade_templatesres->hr_connect."' AND is_active =0";
		$lettersres=$callConfig->getRow($employeessql);
		$returnresult['hrempdata'] = $lettersres;

		$salary_templatesql = "SELECT * FROM salary_template WHERE company_id = '".$get['company_id']."' AND candidate_id =  '".$get['rid']."' AND is_active =0";
		$salary_templateres=$callConfig->getRow($salary_templatesql);
		$returnresult['salary_template'] = $salary_templateres;
		$salary_template_componentssql = "SELECT stc.id, stc.salary_template_id, stc.component_id, stc.component_cat_id, stc.component_name, stc.component_category, stc.component_type, stc.monthly_amount, stc.yearly_amount, ear.name, ear.display_in_salary_structure FROM salary_template_components stc, earnings ear WHERE stc.company_id = '".$get['company_id']."' AND stc.company_id = ear.company_id AND stc.salary_template_id =  '".$salary_templateres->id."' AND stc.component_id = ear.id AND stc.is_active = ear.is_active AND stc.is_active = 0 ORDER BY ear.display_seq ASC";
		//$salary_template_componentssql = "SELECT * FROM salary_template_components WHERE company_id = '".$get['company_id']."' AND salary_template_id =  '".$salary_templateres->id."' AND is_active =0";
		$salary_template_componentsres=$callConfig->getAllRows($salary_template_componentssql);
		$returnresult['salary_template_components'] = $salary_template_componentsres;

		$salary_categoriessql = "SELECT * FROM salary_categories WHERE company_id = '".$get['company_id']."' AND is_active =0";
		$salary_categoriesres=$callConfig->getAllRows($salary_categoriessql);
		$returnresult['salary_categories'] = $salary_categoriesres;

		$candidate_offer_historiessql = "SELECT id,current_status,status_date FROM candidate_offer_histories WHERE company_id = '".$get['company_id']."' AND candidate_id = '".$get['rid']."' AND is_active =0 order by id DESC";
		$candidate_offer_historiesres=$callConfig->getRow($candidate_offer_historiessql);
		$returnresult['candidate_offer_histories'] = $candidate_offer_historiesres;


		$email_contentsql = "SELECT id,email_content_to FROM email_content WHERE company_id = '".$get['company_id']."' AND template_reference_id = 58 AND is_active =0 order by id DESC";
		$email_contentres=$callConfig->getRow($email_contentsql);
		$returnresult['email_content'] = $email_contentres;

		$configsql = "SELECT * FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
		$configres=$callConfig->getRow($configsql);
		$returnresult['config'] = $configres;
		return $returnresult;
	} 
	function getCandidateInterviewPopUp($get){
		global $callConfig;
		$job_post_can_id =  $get['job_post_can_id'];
		$job_posting_candidatessql = "SELECT * FROM job_posting_candidates WHERE company_id = '".$get['company_id']."' AND id = '".$job_post_can_id."' AND is_active =0";
		$job_posting_candidatesres=$callConfig->getRow($job_posting_candidatessql);
		$returnresult['job_posting_candidates'] = $job_posting_candidatesres; 

		$locationssql = "SELECT location_name FROM locations WHERE company_id = '".$get['company_id']."' AND id = '".$job_posting_candidatesres->preferred_location."' AND is_active =0";
		$locationsres=$callConfig->getRow($locationssql);
		$returnresult['locations'] = $locationsres; 
		

		$job_posting_candidatessql = "SELECT * FROM candidates WHERE company_id = '".$get['company_id']."' AND id = '".$job_posting_candidatesres->candidate_id."' AND is_active =0";
		$returnresult['candidate_details'] =$callConfig->getRow($job_posting_candidatessql);
		
		$application_sharesql = "SELECT * FROM application_share WHERE company_id = '".$get['company_id']."' AND application_table = 'job_posting_candidates' AND transaction_id = '".$job_post_can_id."' AND is_active =0";
		$application_shareres=$callConfig->getAllRows($application_sharesql);
		$returnresult['application_share'] = $application_shareres; 

		$job_postingssql = "SELECT * FROM job_postings WHERE company_id = '".$get['company_id']."' AND id = '".$job_posting_candidatesres->job_posting_id."' AND is_active =0";
		$job_postingsres=$callConfig->getRow($job_postingssql);
		$returnresult['job_postings'] = $job_postingsres;

		$job_selection_process_stepssql = "SELECT * FROM job_selection_process_steps WHERE company_id = '".$get['company_id']."' AND process_id = '".$job_postingsres->job_selection_process."' AND is_active =0";
		$job_selection_process_stepsres=$callConfig->getAllRows($job_selection_process_stepssql);
		$returnresult['job_selection_process_steps'] = $job_selection_process_stepsres;
		
		$application_userssql = "SELECT id,display_name,user_photo FROM application_users WHERE company_id = '".$get['company_id']."'  AND JSON_SEARCH(role_id, 'one', '".RECRUITINGMANAGER_ROLE."') IS NOT NULL AND is_active =0";
		$application_usersres=$callConfig->getAllRows($application_userssql);
		$returnresult['application_users'] = $application_usersres;

		$job_candidate_interview_schedulessql = "SELECT id,job_posing_candidate_id,job_selection_step_id,assign_id,inerview_date_time,remarks FROM job_candidate_interview_schedules WHERE company_id = '".$get['company_id']."' AND job_posing_candidate_id = '".$job_post_can_id."' AND is_active =0";
		$job_candidate_interview_schedulessres=$callConfig->getAllRows($job_candidate_interview_schedulessql);
		$returnresult['job_candidate_interview_schedules'] = $job_candidate_interview_schedulessres;
		return $returnresult;
	}
	function getCandidateMaster($get){
		global $callConfig;
		$application_querysql = "SELECT id, field_group FROM application_query WHERE company_id = '".$get['company_id']."' AND is_active =0";
		$application_queryres=$callConfig->getAllRows($application_querysql);
		$return['application_query'] = $application_queryres; 

		$candidatessql = "SELECT * FROM candidates WHERE company_id = '".$get['company_id']."' AND id ='".$get['candidate_id']."' AND is_active =0";
		$candidatesres=$callConfig->getRow($candidatessql);
		$return['candidates'] = $candidatesres;

		$job_posting_candidatessql = "SELECT id FROM job_posting_candidates WHERE company_id = '".$get['company_id']."' AND candidate_id ='".$get['candidate_id']."' AND is_active =0";
		$job_posting_candidatesres=$callConfig->getRow($job_posting_candidatessql);
		$return['job_posting_candidates'] = $job_posting_candidatesres;

		$comdition = "";
		if($get['admin_type']!='1'){
			$comdition = " AND (tas.assigned_to = '".$get['user_id']."' OR tas.approval_user_id = '".$get['user_id']."')";
		}

		$tasks_cntssql = "SELECT count(*) as total_task_cnt, SUM(if(tas.current_status=".ST_APPROVED.",1,0)) as approve_cnt, SUM(if(tas.current_status=".ST_COMPLETE.",1,0)) as review_cnt, SUM(if(tas.current_status=".ST_INPROGRESS.",1,0)) as inprogress_cnt, SUM(if(tas.current_status=".ST_ACTIVE.",1,0)) as yet_to_start_cnt, SUM(if(tas.assigned_to = '".$get['user_id']."',1,0)) as self_task_cnt, SUM(if(tas.approval_user_id = '".$get['user_id']."' AND tas.assigned_to != '".$get['user_id']."',1,0)) as riview_cnt FROM transaction_activity_schedule tas WHERE  tas.company_id = '".$get['company_id']."' AND tas.transaction_table = 'candidates'  AND transaction_id= '".$candidatesres->id."'  ".$comdition." AND tas.is_active = 0";
		$tasks_cntsres = $callConfig->getRow($tasks_cntssql);	
		$return['task_cnt'] = $tasks_cntsres;
		$return['task_cnt_sql'] = $tasks_cntssql;

		$whrcat ="company_id = '".$get['company_id']."' AND is_active=0";
    	$query=$callConfig->selectQuery('countries','id,country_name',$whrcat,'','','');
    	$components6= (array)$callConfig->getAllRows($query);
    	$return['countries'] = (array)$components6;

    	$statessql = "SELECT id, state_name FROM `states` WHERE company_id = '".$get['company_id']."' AND country_id = '".$candidatesres->country_id."' AND is_active=0";
    	$statesres = $callConfig->getAllRows($statessql);
    	$return['states'] = $statesres;

    	$citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '".$get['company_id']."' AND state_id = '".$candidatesres->state_id."' AND is_active=0";
    	$citiesres = $callConfig->getAllRows($citiessql);
    	$return['cities'] = $citiesres;

		$spihitsql = "SELECT id, api_master_id, api_request_parameters, api_response_details, response_date_time FROM api_hits WHERE company_id = '".$get['company_id']."' AND application_table = '".$get['transaction_table']."' AND transaction_id = '".$vendorres->id."'  AND is_active = 0 ORDER BY response_date_time DESC";
		$spihitres=$callConfig->getAllRows($spihitsql);
		$return['api_hits'] = $spihitres;

		return $return;
	}
	function getJobPostingList($get){
		global $callConfig;
		$roleshortcodes = json_decode($get['roleshortcode'],1);
		$whr = ""; $whrjp="";
		if(in_array(HIRINGMANAGER_ROLE, (array)$roleshortcodes) || $get['user_type']==4){
			$whr = " AND requested_by = ".$get['user_id'];
		}
		if(in_array(RECRUITINGMANAGER_ROLE, (array)$roleshortcodes)){
			$whr = " AND (recruitment_manager = '".$get['user_id']."' AND current_status > 0)";
		}
		$job_postingssql = "SELECT * FROM `job_postings` WHERE company_id = '".$get['company_id']."' AND is_active = 0 ".$whr;
		$job_postingsres=$callConfig->getAllRows($job_postingssql);
		$return['job_postings'] = $job_postingsres;

		$job_titlessql = "SELECT id,job_title FROM `job_titles` WHERE company_id = '".$get['company_id']."' AND is_active = 0 ";
		$job_titlesres=$callConfig->getAllRows($job_titlessql);
		$return['job_titles'] = $job_titlesres;


		$job_posting_distributesql = "SELECT id,job_posting_id FROM `job_posting_distribute` WHERE company_id = '".$get['company_id']."' AND vendor_id = '".$get['vendor_id']."' AND is_active = 0 ";
		$job_posting_distributeres=$callConfig->getAllRows($job_posting_distributesql);
		$return['job_posting_distribute'] = $job_posting_distributeres;
		
		return $return;
	}
	function getrecruitmentintegdashboard($get){
		global $callConfig; 
		//echo "<pre>";
		//print_r($get); exit;
		if($get['apitype'] == "candidatelistpopup"){
			$candidatessql = "SELECT id, candidate_code, display_name, email_id, phone, skills FROM candidates WHERE company_id = '".$get['company_id']."' AND id IN(".$get['candidate_ids'].") AND is_active =0";
			$candidatesres=$callConfig->getAllRows($candidatessql);
			$return['candidates'] = $candidatesres;
			return $return;
		}	


		$roleshortcodes = json_decode($get['roleshortcode'],1);
		$reportstartdate = $get['reportstartdate'];
		$reportenddate = $get['reportenddate'];
		$whr = ""; $whrjp="";
		//if($get['roleshortcode']==HIRINGMANAGER_ROLE){
		if(in_array(HIRINGMANAGER_ROLE, (array)$roleshortcodes)){
			$whr = " AND requested_by = ".$get['user_id'];
			$whrjp = " AND jp.requested_by = ".$get['user_id'];
		}
		//if($get['roleshortcode']==RECRUITINGMANAGER_ROLE){
		if(in_array(RECRUITINGMANAGER_ROLE, (array)$roleshortcodes)){
			//$whr = " AND recruitment_manager = ".$get['user_id'];
			$whr = " AND (recruitment_manager = '".$get['user_id']."' AND current_status > 0)";
			$whrjp = " AND (jp.recruitment_manager = '".$get['user_id']."' AND jp.current_status > 0)";
			//$whrjp = " AND jp.recruitment_manager = ".$get['user_id'];
		}
		/*--------- Job Details ---------------- */		
		$query1 = "SELECT count(id) total_jobs , sum(if(job_status=0,1,0)) open_jobs, sum(if(job_status=1,1,0)) close_jobs, sum(if(job_status=2,1,0)) hold_jobs FROM `job_postings` where company_id = '".$get['company_id']."' AND is_active = 0 ".$whr;
		//$query1 = "SELECT SUM(IF(recruitment_manager = '".$get['user_id']."',1,0))rm_total_jobs, SUM(if(recruitment_manager = '".$get['user_id']."' AND job_status=0,1,0)) rm_open_jobs,  SUM(if(recruitment_manager = '".$get['user_id']."' AND job_status=1,1,0)) rm_close_jobs, SUM(if(recruitment_manager = '".$get['user_id']."' AND job_status=2,1,0)) rm_hold_jobs, SUM(IF(requested_by = '".$get['user_id']."',1,0)) hm_total_jobs, SUM(if(requested_by = '".$get['user_id']."' AND job_status=0,1,0)) hm_open_jobs, SUM(if(requested_by = '".$get['user_id']."' AND job_status=1,1,0)) hm_close_jobs, SUM(if(requested_by = '".$get['user_id']."' AND job_status=2,1,0)) hm_hold_jobs FROM `job_postings` WHERE company_id = '".$get['company_id']."' AND is_active = 0 ".$whr;
		$roleres1 = $callConfig->getRow($query1);	
		$return = (array)$roleres1;
		$return['cnt_sql'] = $query1;
		$return['get'] = $get;
		/*--------- Holidays  Details ---------------- */	
		$location_id = $_GET['location_id']!="" ? $_GET['location_id'] : 1;
		$holidayssql = "SELECT id,holiday_date,holiday_name FROM holidays where company_id = '".$get['company_id']."' AND holiday_year ='".date('Y')."' AND location_id = ".$location_id." AND is_active = 0 ORDER BY `holiday_date` ASC";
		$holidaysres = $callConfig->getAllRows($holidayssql);	
		$return['holidays'] = $holidaysres;					
		/*--------- All Tab Wise Job Details ---------------- */
		$loquery = "SELECT jp.job_name,ca.display_name candidate_name,ca.email_id, ca.phone, ca.created_date, jp.requested_by,ca.current_status, ca.id candidate_id, jp.id job_post_id, jpc.id job_post_canidate_id  FROM job_posting_candidates jpc, job_postings jp, candidates ca WHERE jpc.company_id = jp.company_id AND jpc.company_id = ca.company_id AND jpc.company_id =".$get['company_id']." AND jpc.job_posting_id = jp.id AND jpc.candidate_id = ca.id ".$whrjp." AND jpc.is_active = 0";
		$loqres = $callConfig->getAllRows($loquery);	
		$return['tabsdata_sql'] = $loquery;
		$return['tabsdata'] = $loqres;

		/*--------- All side Tab Job Details ---------------- */

		/*--------- All Active 3 Job Details ---------------- */
		$job_postingssql = "SELECT id, requested_date, job_number, job_name,current_status, current_status FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND requested_date <= '".date('Y-m-d')."' AND current_status=0 AND  is_active = 0  ORDER BY `requested_date` DESC LIMIT 0,3";
		$job_postingsres = $callConfig->getAllRows($job_postingssql);	
		$return['job_postings'] = $job_postingsres;
		$countsql = "SELECT count(id) cc FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND requested_date <= '".date('Y-m-d')."' AND current_status=0 AND  is_active = 0";
		$allcountdata = $callConfig->getRow($countsql);
		$return['job_postings_count']= $allcountdata->cc;	
		/*--------- All Yet to submit  3 Job Details ---------------- */
		//if($get['roleshortcode']==HIRINGMANAGER_ROLE){
		if(in_array(HIRINGMANAGER_ROLE, (array)$roleshortcodes)){
			$job_postingssql = "SELECT id, requested_date, job_number, job_name, current_status, updated_date FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND is_active = 0  ORDER BY `updated_date` DESC LIMIT 0,3";
			$return['job_post_status']['name'] = "Jobs Yet to Publish";
			$countsql = "SELECT count(id) cc FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND is_active = 0 ";

		}
		if(in_array(RECRUITINGMANAGER_ROLE, (array)$roleshortcodes)){
		//if($get['roleshortcode']==RECRUITINGMANAGER_ROLE){
			$job_postingssql = "SELECT id, requested_date, job_number, job_name, current_status FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND is_active = 0  ORDER BY `updated_date` DESC LIMIT 0,3";
			$return['job_post_status']['name'] = "Jobs Yet to Publish";	
			$countsql = "SELECT count(id) cc FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status=51 AND is_active = 0";

		}
		$allcountdata = $callConfig->getRow($countsql);
		$return['job_post_status']['count']= $allcountdata->cc;
		$return['job_post_status']['data'] =  $callConfig->getAllRows($job_postingssql);	

		/*--------- All Recently Published 3 Job Details ---------------- */
		$recent_publishsql = "SELECT id, requested_date, job_number, job_name,current_status, updated_date FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_PUBLISH." AND is_active = 0  ORDER BY updated_date DESC LIMIT 0,3";
		$return['recent_publish']['name'] = "Recently Published";
		$recent_publish = $callConfig->getAllRows($recent_publishsql);	
		$return['recent_publish']['data'] = $recent_publish;
		$countsql = "SELECT count(id) cc FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_PUBLISH." AND is_active = 0 ";
		$allcountdata = $callConfig->getRow($countsql);
		$return['recent_publish']['count']= $allcountdata->cc;
		/*--------- All Recently Un-Published 3 Job Details ---------------- */
		$recent_unpublishsql = "SELECT id, requested_date, job_number, job_name,current_status, updated_date FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_UNPUBLISH." AND is_active = 0  ORDER BY updated_date DESC LIMIT 0,3";
		$return['recent_unpublish']['name'] = "Recently Un-Published";
		$recent_publish = $callConfig->getAllRows($recent_unpublishsql);	
		$return['recent_unpublish']['data'] = $recent_unpublish;
		$countsql = "SELECT count(id) cc FROM `job_postings` WHERE company_id = '".$get['company_id']."' ".$whr." AND current_status = ".ST_UNPUBLISH." AND is_active = 0";
		$return['recent_unpublish']['name'] = "Recently Un-Published ";
		$allcountdata = $callConfig->getRow($countsql);
		$return['recent_unpublish']['count']= $allcountdata->cc;
		/*--------- Month Wise Job Details ---------------- */
		/*$candidatessql = "SELECT id,display_name,candidate_code,reporting_date, image FROM `candidates` WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY `candidates`.`id` DESC limit 0,3";*/
		$candidatessql = "SELECT jpc.id,jpc.candidate_id ,jpc.candidate_name display_name,jpc.candidate_email, jpc.created_date,jpc.image FROM job_posting_candidates jpc,job_postings jp WHERE jpc.company_id = jp.company_id AND jpc.company_id ='".$get['company_id']."' AND jpc.job_posting_id = jp.id ".$whrjp." AND jpc.is_active = 0 ORDER BY jpc.id DESC limit 0,3";
		$candidatesres = $callConfig->getAllRows($candidatessql);	
		$return['candidates'] = $candidatesres;

		//$condidate_postingssql = "SELECT jpc.job_posting_id,COUNT(ca.id) as apply_total, SUM(IF(ca.current_status >'30', 1,0)) AS filter_cnt, SUM(IF(ca.current_status ='30', 1,0)) AS reject_cnt FROM job_posting_candidates jpc, candidates ca where jpc.company_id = ca.company_id AND ca.company_id = ".$get['company_id']." AND jpc.candidate_id = ca.id AND ca.is_active = 0 GROUP BY job_posting_id";
		$condidate_postingssql = "SELECT jpc.job_posting_id,COUNT(jpc.id) as apply_total,SUM(IF(ca.current_status =".ST_RMSHORTLISTED.", 1,0)) AS rm_shortlisted,SUM(IF(ca.current_status =".ST_HMSHORTLISTED.", 1,0)) AS hm_shortlisted, SUM(IF(ca.current_status =".ST_LOIREQUESTED.", 1,0)) AS loi_requested, SUM(IF(ca.current_status =".ST_LOIGENERATED.", 1,0)) AS loi_generated, SUM(IF(ca.current_status =".ST_LOIACCEPTED.", 1,0)) AS loi_accepted,SUM(IF(ca.current_status =".ST_REJECTED.", 1,0)) AS loi_rejected,  SUM(IF(ca.current_status >'30', 1,0)) AS filter_cnt FROM job_posting_candidates jpc, candidates ca where jpc.company_id = ca.company_id AND ca.company_id = ".$get['company_id']." AND jpc.candidate_id = ca.id AND jpc.is_active = 0 GROUP BY job_posting_id";
		$condidate_postingsres = $callConfig->getAllRows($condidate_postingssql);	
		$job_posting_candidates = array();
		foreach($condidate_postingsres as $condidate_postres){
			$job_posting_candidates[$condidate_postres->job_posting_id]  = $condidate_postres;
		}
		$sqlquery ="SELECT jp.id,MONTH(jp.requested_date) month, YEAR(jp.requested_date) year, count(id) cnt, jp.job_name, jp.current_status,(SELECT jppd.publish_date FROM job_posting_publish_details jppd WHERE jppd.company_id = jp.company_id AND jppd.job_post_id = jp.id ORDER BY jppd.publish_date ASC LIMIT 1) as job_publish_date,  jp.no_of_resources,jp.requested_by FROM job_postings jp WHERE company_id = '".$get['company_id']."' ".$whrjp." AND jp.requested_date BETWEEN '".$reportstartdate."' AND DATE_ADD('".$reportenddate."', INTERVAL 9 MONTH) - INTERVAL 1 DAY GROUP BY MONTH(jp.requested_date),jp.id";
		$sqlres = $callConfig->getAllRows($sqlquery);
		$return['job_posting_candidates_sql'] = $condidate_postingssql;
		$candidatesarray = array();
		$countcandidatenmonth = array();
		$donutchart = array();
		foreach($sqlres as $sqlres1){
			$apply_total = $filter_cnt = $reject_cnt = $rm_shortlisted = $hm_shortlisted =  $loi_requested = $loi_generated =$loi_accepted  = $loi_rejected=  0;
			$jobpostingcandidates = $job_posting_candidates[$sqlres1->id];
			$apply_total = $jobpostingcandidates->apply_total;
			$rm_shortlisted = $jobpostingcandidates->rm_shortlisted;
			$hm_shortlisted = $jobpostingcandidates->hm_shortlisted;
			$loi_requested = $jobpostingcandidates->loi_requested;
			$loi_generated = $jobpostingcandidates->loi_generated;
			$loi_accepted = $jobpostingcandidates->loi_accepted;
			$loi_rejected = $jobpostingcandidates->loi_rejected;
			$monthyearkey = date("Ym",strtotime($sqlres1->year."-".$sqlres1->month."-01"));
			$candidatesarray[$monthyearkey][$sqlres1->id]['id'] =$sqlres1->id;
			$candidatesarray[$monthyearkey][$sqlres1->id]['apply_total'] =$apply_total !=""?$apply_total:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['filter_cnt'] =$filter_cnt;
			$candidatesarray[$monthyearkey][$sqlres1->id]['reject_cnt'] =$reject_cnt;
			$candidatesarray[$monthyearkey][$sqlres1->id]['job_name'] =$sqlres1->job_name;
			$candidatesarray[$monthyearkey][$sqlres1->id]['requested_by'] =$sqlres1->requested_by;
			$candidatesarray[$monthyearkey][$sqlres1->id]['current_status'] =$sqlres1->current_status;
			$candidatesarray[$monthyearkey][$sqlres1->id]['job_publish_date'] =$sqlres1->job_publish_date;
			$candidatesarray[$monthyearkey][$sqlres1->id]['no_of_resources'] =$sqlres1->no_of_resources!=""?$sqlres1->no_of_resources:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['rm_shortlisted'] =$rm_shortlisted !=""?$rm_shortlisted:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['hm_shortlisted'] =$hm_shortlisted !=""?$hm_shortlisted:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['loi_requested'] =$loi_requested !=""?$loi_requested:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['loi_generated'] =$loi_generated !=""?$loi_generated:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['loi_accepted'] =$loi_accepted !=""?$loi_accepted:"0";
			$candidatesarray[$monthyearkey][$sqlres1->id]['loi_rejected'] =$loi_rejected !=""?$loi_rejected:"0";

			$countcandidatenmonth[$monthyearkey] +=$apply_total;

			if($sqlres1->current_status ==ST_ACTIVE){
				$donutchart[$monthyearkey][ST_ACTIVE] += 1;	
			}elseif($sqlres1->current_status ==ST_INPROGRESS){
				$donutchart[$monthyearkey][ST_INPROGRESS] += 1;
			}elseif($sqlres1->current_status ==ST_HOLD){
				$donutchart[$monthyearkey][ST_HOLD] += 1;
			}elseif($sqlres1->current_status==ST_COMPLETE){
				$donutchart[$monthyearkey][ST_COMPLETE] += 1;
			}
		}
		$return['countcandidatenmonth'] = $countcandidatenmonth;
		$return['job_posting_candidates'] = $candidatesarray;
		$return['donutchart'] = $donutchart;
		$return['cand_sql'] = $condidate_postingssql;
		

		$job_candidate_interview_schedulessql = "SELECT jcis.id, jcis.inerview_date_time, jpc.candidate_name,jpc.candidate_email, jcis.job_posing_candidate_id FROM job_candidate_interview_schedules jcis, job_posting_candidates jpc WHERE jcis.company_id = '".$get['company_id']."' AND jcis.company_id = jpc.company_id AND jcis.job_posing_candidate_id = jpc.id AND jcis.assign_id = '".$get['user_id']."' AND jcis.is_active = 0 AND jcis.is_active = jpc.is_active AND jcis.current_status = ".ST_ACTIVE." GROUP BY jpc.job_posting_id";
		$job_candidate_interview_schedules = $callConfig->getAllRows($job_candidate_interview_schedulessql);	
		$return['job_candidate_interview_schedules'] = $job_candidate_interview_schedules;

		$job_candidate_interview_schedulessql = "SELECT jcis.id, jcis.inerview_date_time, jpc.candidate_name,jpc.candidate_email, jcis.job_posing_candidate_id FROM job_candidate_interview_schedules jcis, job_posting_candidates jpc, job_postings jp WHERE jcis.company_id = '".$get['company_id']."' AND jcis.company_id = jpc.company_id AND jcis.company_id = jp.company_id AND jcis.job_posing_candidate_id = jpc.id AND jpc.job_posting_id = jp.id AND jcis.is_active = 0 AND jcis.is_active = jpc.is_active AND jcis.current_status = ".ST_APPROVED." AND jp.recruitment_manager = '".$get['user_id']."' GROUP BY jpc.job_posting_id";
		$job_candidate_interview_schedules = $callConfig->getAllRows($job_candidate_interview_schedulessql);	
		$return['approved_job_candidate_interview_schedules'] = $job_candidate_interview_schedules;


		//$candidatesql = "SELECT COUNT(can.id) AS total_candidates, COUNT(DISTINCT jpc.candidate_id) AS linked_candidates, COUNT(can.id) - COUNT(DISTINCT jpc.candidate_id) AS unlinked_candidates, COUNT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'HOLD' THEN jpc.candidate_id END) AS hold_count, COUNT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'PROCEED' THEN jpc.candidate_id END) AS proceed_count, COUNT(DISTINCT CASE  WHEN jpc.ai_shortlisted = 'REJECT' THEN jpc.candidate_id END) AS reject_count, COUNT(DISTINCT CASE WHEN jpc.screening_status = 'Y' THEN jpc.candidate_id END) AS screening_started, COUNT(DISTINCT CASE WHEN jpc.screening_status = 'N' THEN jpc.candidate_id END) AS screening_not_started FROM candidates can LEFT JOIN job_posting_candidates jpc ON jpc.candidate_id = can.id AND jpc.company_id = can.company_id AND jpc.is_active = can.is_active WHERE can.company_id = '".$get['company_id']."' AND can.is_active = 0"; 
		$candidatesql = "SELECT COUNT(can.id) AS total_candidates, GROUP_CONCAT(DISTINCT can.id) AS total_candidate_ids, COUNT(DISTINCT jpc.candidate_id) AS linked_candidates, GROUP_CONCAT(DISTINCT CASE WHEN jpc.candidate_id IS NOT NULL THEN can.id END) AS linked_candidate_ids, COUNT(can.id) - COUNT(DISTINCT jpc.candidate_id) AS unlinked_candidates,GROUP_CONCAT(DISTINCT CASE WHEN jpc.candidate_id IS NULL THEN can.id END) AS unlinked_candidate_ids, COUNT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'HOLD' THEN jpc.candidate_id END) AS hold_count,    GROUP_CONCAT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'HOLD' THEN can.id END) AS hold_candidate_ids, COUNT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'PROCEED' THEN jpc.candidate_id END) AS proceed_count, GROUP_CONCAT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'PROCEED' THEN can.id END) AS proceed_candidate_ids,COUNT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'REJECT' THEN jpc.candidate_id END) AS reject_count,  GROUP_CONCAT(DISTINCT CASE WHEN jpc.ai_shortlisted = 'REJECT' THEN can.id END) AS reject_candidate_ids, COUNT(DISTINCT CASE WHEN jpc.screening_status = 'Y' THEN jpc.candidate_id END) AS screening_started, GROUP_CONCAT(DISTINCT CASE WHEN jpc.screening_status = 'Y' THEN can.id END) AS screening_started_ids, COUNT(DISTINCT CASE WHEN jpc.screening_status = 'N' THEN jpc.candidate_id END) AS screening_not_started, GROUP_CONCAT(DISTINCT CASE WHEN jpc.screening_status = 'N' THEN can.id END) AS screening_not_started_ids FROM candidates can LEFT JOIN job_posting_candidates jpc ON jpc.candidate_id = can.id AND jpc.company_id = can.company_id AND jpc.is_active = can.is_active WHERE can.company_id = '1' AND can.is_active = 0";
		$candidateres = $callConfig->getRow($candidatesql);	
		$return['candidate_dashboard'] = $candidateres;


		$todaycandidatessql = "SELECT can.id, can.candidate_code, can.email_id, can.display_name, can.created_date, jpc.screening_status, jpc.screening_start_datetime , jpc.screening_end_datetime , jpc.ai_shortlisted, jpc.ai_summary FROM candidates can LEFT JOIN job_posting_candidates jpc ON jpc.candidate_id = can.id AND jpc.company_id = can.company_id AND jpc.is_active = can.is_active WHERE can.company_id = '1' AND can.is_active = 0";
		$todaycandidatesres = $callConfig->getAllRows($todaycandidatessql);	
		$candidate_dashboard_today = array();
		$today = date('Y-m-d');
		foreach($todaycandidatesres as $valred){
			if(!empty($valred->created_date) && date('Y-m-d', strtotime($valred->created_date)) == $today) {
		        $candidate_dashboard_today['created_today'][] = $valred;
		    }
		    if(!empty($valred->screening_start_datetime) && date('Y-m-d', strtotime($valred->screening_start_datetime)) == $today) {
		        $candidate_dashboard_today['screening_start_today'][] = $valred;
		    }
		    if(!empty($valred->screening_end_datetime) && date('Y-m-d', strtotime($valred->screening_end_datetime)) == $today) {
		        $candidate_dashboard_today['screening_end_today'][] = $valred;
		    }
		    if(!empty($valred->screening_end_datetime) && date('Y-m-d', strtotime($valred->screening_end_datetime)) == $today && $valred->ai_shortlisted == 'PROCEED'){
		        $candidate_dashboard_today['ai_shortlisted_today'][] = $valred;
		    }
		}
		$return['candidate_dashboard_today'] = $candidate_dashboard_today;

		return $return; 
	}
	function getCandidateScreeningQuestions($get){
		global $callConfig; 
		if($get['gettype'] =="QuestionsAnswers"){

			$job_posting_candidate_id = $get['job_posting_candidate_id'];
			$candidatessql = "SELECT jpc.id, jpc.job_posting_id, jpc.candidate_id FROM job_posting_candidates jpc WHERE jpc.company_id = '".$get['company_id']."' AND jpc.id = '".$job_posting_candidate_id."'";
	        $candidatesres=$callConfig->getRow($candidatessql);
	        $return['job_posting_candidates'] = $candidatesres;
	        if($candidatesres->job_posting_id !="" AND $candidatesres->candidate_id !=""){
				$sql = "SELECT id, questions, answers FROM candidate_screening_questions_and_answers WHERE company_id = '".$get['company_id']."' AND candidate_id = '".$candidatesres->candidate_id."' AND job_posting_id = '".$candidatesres->job_posting_id."' AND is_active = 0";
				$candidate_screening_questions_and_answersres=$callConfig->getAllRows($sql);
			}
			$return['candidate_screening_questions_and_answers'] = $candidate_screening_questions_and_answersres;

		}elseif($get['gettype'] =="ManualQuestions"){
			$candidate_id = $get['candidate_id'];
			$job_posting_id = $get['job_posting_id'];
			$job_postingssql = "SELECT * FROM job_postings jp WHERE jp.company_id = '".$get['company_id']."' AND jp.id = '".$job_posting_id."'  AND jp.is_active =0";
	        $job_postingsres=$callConfig->getRow($job_postingssql);
	        $return['job_postings'] = $job_postingsres;

		}else{
			$candidate_id = $get['candidate_id'];
			$candidatessql = "SELECT * FROM candidates can WHERE can.company_id = '".$get['company_id']."' AND can.id = '".$candidate_id."'  AND can.is_active =0";
	        $candidatesres=$callConfig->getRow($candidatessql);
	        $return['candidates'] = $candidatesres;

	        $candidatessql = "SELECT jpc.id, jpc.candidate_id, jpc.job_posting_id, jpc.is_active, jpc.ai_shortlisted, jpc.screening_status, jpc.screening_start_datetime, jpc.screening_end_datetime, jp.job_name, jp.job_number, jp.job_code, jpc.ai_summary FROM job_posting_candidates jpc, job_postings jp WHERE jpc.company_id = '".$get['company_id']."' AND jpc.job_posting_id = jp.id AND jpc.candidate_id = '".$candidate_id."'";
	        $candidatesres=$callConfig->getAllRows($candidatessql);
	        $return['job_posting_candidates'] = $candidatesres;
	    }
	    return $return; 
	}
} 